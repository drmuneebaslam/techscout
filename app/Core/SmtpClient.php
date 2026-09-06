<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Enterprise Native SMTP Socket Client for TechScout.
 * Fully supports Gmail SMTP (smtp.gmail.com:587 / 465) with Google App Passwords,
 * ImprovMX custom domain aliases, and standard SMTP relays.
 */
class SmtpClient {
    private string $host;
    private int $port;
    private string $encryption;
    private ?string $username;
    private ?string $password;
    private int $timeout;

    public function __construct(
        string $host = 'smtp.gmail.com',
        int $port = 587,
        string $encryption = 'tls',
        ?string $username = null,
        ?string $password = null,
        int $timeout = 6
    ) {
        $this->host = trim($host);
        $this->port = $port;
        $this->encryption = strtolower(trim($encryption));
        $this->username = $username ? trim($username) : null;
        // Remove spaces from Google 16-character app passwords if pasted with spaces e.g. "abcd efgh ijkl mnop"
        $this->password = $password ? str_replace(' ', '', trim($password)) : null;
        $this->timeout = $timeout;
    }

    /**
     * Dispatches an email via SMTP socket connection.
     * 
     * @return array ['success' => bool, 'message_id' => ?string, 'latency_ms' => int, 'error' => ?string]
     */
    public function send(
        string $toEmail,
        ?string $toName,
        string $fromEmail,
        string $fromName,
        string $subject,
        string $htmlBody,
        ?string $replyTo = null,
        array $attachments = []
    ): array {
        $startTime = microtime(true);
        $socket = null;

        try {
            $hostPrefix = ($this->encryption === 'ssl') ? 'ssl://' : '';
            $connectHost = $hostPrefix . $this->host;

            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);

            $socket = @stream_socket_client(
                "{$connectHost}:{$this->port}",
                $errNo,
                $errStr,
                $this->timeout,
                STREAM_CLIENT_CONNECT,
                $context
            );

            if (!$socket) {
                throw new \Exception("Could not connect to SMTP host {$this->host}:{$this->port} - Error: {$errStr} ({$errNo}). Note: Outbound SMTP ports 25, 465, and 587 are blocked on InfinityFree free hosting. Use an HTTP/REST API provider like Brevo (api.brevo.com) or Resend (api.resend.com) which works over standard HTTPS port 443.");
            }

            stream_set_timeout($socket, $this->timeout);

            // Read greeting (220)
            $response = $this->readResponse($socket);
            if (!str_starts_with($response, '220')) {
                throw new \Exception("Invalid greeting response: {$response}");
            }

            // Send EHLO
            $localHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $this->sendCommand($socket, "EHLO {$localHost}");
            $response = $this->readResponse($socket);

            // STARTTLS for port 587 or TLS encryption
            if ($this->encryption === 'tls' && $this->port !== 465) {
                $this->sendCommand($socket, "STARTTLS");
                $response = $this->readResponse($socket);
                if (!str_starts_with($response, '220')) {
                    throw new \Exception("STARTTLS failed: {$response}");
                }

                $cryptoMethod = STREAM_CRYPTO_METHOD_TLS_CLIENT;
                if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) {
                    $cryptoMethod |= STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
                }
                if (defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT')) {
                    $cryptoMethod |= STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT;
                }

                if (!@stream_socket_enable_crypto($socket, true, $cryptoMethod)) {
                    throw new \Exception("TLS handshake negotiation failed with {$this->host}");
                }

                // Re-send EHLO after TLS handshake
                $this->sendCommand($socket, "EHLO {$localHost}");
                $response = $this->readResponse($socket);
            }

            // Authenticate if credentials supplied (AUTH LOGIN for Gmail)
            if ($this->username && $this->password) {
                $this->sendCommand($socket, "AUTH LOGIN");
                $response = $this->readResponse($socket);
                if (!str_starts_with($response, '334')) {
                    throw new \Exception("AUTH LOGIN rejected: {$response}");
                }

                $this->sendCommand($socket, base64_encode($this->username));
                $response = $this->readResponse($socket);
                if (!str_starts_with($response, '334')) {
                    throw new \Exception("Username rejected by SMTP: {$response}");
                }

                $this->sendCommand($socket, base64_encode($this->password));
                $response = $this->readResponse($socket);
                if (!str_starts_with($response, '235')) {
                    throw new \Exception("SMTP Authentication failed for {$this->username}. For Gmail, ensure 2-Step Verification is ON and you are using a 16-character Google App Password (not your primary password). Response: {$response}");
                }
            }

            // Envelope From (Use SMTP username if sending through Gmail to prevent sender mismatch)
            $envelopeFrom = $this->username ?: $fromEmail;
            $this->sendCommand($socket, "MAIL FROM:<{$envelopeFrom}>");
            $response = $this->readResponse($socket);
            if (!str_starts_with($response, '250')) {
                throw new \Exception("MAIL FROM rejected: {$response}");
            }

            // Envelope To
            $this->sendCommand($socket, "RCPT TO:<{$toEmail}>");
            $response = $this->readResponse($socket);
            if (!str_starts_with($response, '250') && !str_starts_with($response, '251')) {
                throw new \Exception("RCPT TO rejected for {$toEmail}: {$response}");
            }

            // Send DATA
            $this->sendCommand($socket, "DATA");
            $response = $this->readResponse($socket);
            if (!str_starts_with($response, '354')) {
                throw new \Exception("DATA command rejected: {$response}");
            }

            // Construct Full MIME Message
            $messageId = '<' . bin2hex(random_bytes(16)) . '@' . parse_url(Config::get('app_url', 'http://techscout.online'), PHP_URL_HOST) . '>';
            $rawMessage = $this->buildMimeMessage(
                $toEmail,
                $toName,
                $fromEmail,
                $fromName,
                $subject,
                $htmlBody,
                $replyTo,
                $messageId,
                $attachments
            );

            // Stream Message + <CRLF>.<CRLF>
            fputs($socket, $rawMessage . "\r\n.\r\n");
            $response = $this->readResponse($socket);
            if (!str_starts_with($response, '250')) {
                throw new \Exception("Message delivery rejected: {$response}");
            }

            // Send QUIT
            $this->sendCommand($socket, "QUIT");
            fclose($socket);

            $latencyMs = (int)round((microtime(true) - $startTime) * 1000);
            return [
                'success' => true,
                'message_id' => $messageId,
                'latency_ms' => $latencyMs,
                'error' => null
            ];

        } catch (\Throwable $e) {
            if ($socket && is_resource($socket)) {
                @fclose($socket);
            }
            $latencyMs = (int)round((microtime(true) - $startTime) * 1000);
            return [
                'success' => false,
                'message_id' => null,
                'latency_ms' => $latencyMs,
                'error' => $e->getMessage()
            ];
        }
    }

    private function sendCommand($socket, string $command): void {
        fputs($socket, $command . "\r\n");
    }

    private function readResponse($socket): string {
        $data = '';
        while ($str = fgets($socket, 515)) {
            $data .= $str;
            if (substr($str, 3, 1) === ' ') {
                break;
            }
        }
        return trim($data);
    }

    private function buildMimeMessage(
        string $toEmail,
        ?string $toName,
        string $fromEmail,
        string $fromName,
        string $subject,
        string $htmlBody,
        ?string $replyTo,
        string $messageId,
        array $attachments = []
    ): string {
        $boundary = '=_ts_bound_' . bin2hex(random_bytes(8));
        $toHeader = $toName ? "=?UTF-8?B?" . base64_encode($toName) . "?= <{$toEmail}>" : "<{$toEmail}>";
        $fromHeader = "=?UTF-8?B?" . base64_encode($fromName) . "?= <{$fromEmail}>";
        $subjectHeader = "=?UTF-8?B?" . base64_encode($subject) . "?=";
        $dateHeader = date('r');

        $headers = [];
        $headers[] = "Date: {$dateHeader}";
        $headers[] = "To: {$toHeader}";
        $headers[] = "From: {$fromHeader}";
        if ($replyTo) {
            $headers[] = "Reply-To: <{$replyTo}>";
        }
        $headers[] = "Subject: {$subjectHeader}";
        $headers[] = "Message-ID: {$messageId}";
        $headers[] = "X-Mailer: TechScout Enterprise Mailer/2.5.0";
        $headers[] = "MIME-Version: 1.0";

        // Filter out non-file attachments, empty arrays, and dummy empty attachments
        $validAttachments = [];
        if (!empty($attachments) && is_array($attachments)) {
            $candidateFiles = isset($attachments['files']) ? $attachments['files'] : $attachments;
            if (is_array($candidateFiles)) {
                foreach ($candidateFiles as $k => $att) {
                    if ($k === 'links' || $k === 'files' || !is_array($att)) continue;
                    // Must have valid content or a readable local file path
                    if (!empty($att['content']) || !empty($att['path'])) {
                        $content = !empty($att['content']) ? $att['content'] : ((!empty($att['path']) && file_exists($att['path'])) ? file_get_contents($att['path']) : '');
                        if (!empty($content)) {
                            $validAttachments[] = [
                                'name' => $att['name'] ?? 'attachment_' . (is_numeric($k) ? ($k + 1) : $k),
                                'content' => $content,
                                'type' => $att['type'] ?? 'application/octet-stream'
                            ];
                        }
                    }
                }
            }
        }

        if (empty($validAttachments)) {
            $headers[] = "Content-Type: text/html; charset=UTF-8";
            $headers[] = "Content-Transfer-Encoding: base64";
            return implode("\r\n", $headers) . "\r\n\r\n" . chunk_split(base64_encode($htmlBody));
        }

        // Multipart message with real attachments only
        $headers[] = "Content-Type: multipart/mixed; boundary=\"{$boundary}\"";
        $out = implode("\r\n", $headers) . "\r\n\r\n";
        $out .= "--{$boundary}\r\n";
        $out .= "Content-Type: text/html; charset=UTF-8\r\n";
        $out .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $out .= chunk_split(base64_encode($htmlBody)) . "\r\n";

        foreach ($validAttachments as $att) {
            $name = $att['name'];
            $content = $att['content'];
            $type = $att['type'];

            $out .= "--{$boundary}\r\n";
            $out .= "Content-Type: {$type}; name=\"{$name}\"\r\n";
            $out .= "Content-Disposition: attachment; filename=\"{$name}\"\r\n";
            $out .= "Content-Transfer-Encoding: base64\r\n\r\n";
            $out .= chunk_split(base64_encode($content)) . "\r\n";
        }

        $out .= "--{$boundary}--";
        return $out;
    }

    public static function sendMail(
        string $host,
        int $port,
        ?string $username,
        ?string $password,
        string $encryption,
        string $fromEmail,
        string $fromName,
        string $toEmail,
        ?string $toName,
        string $subject,
        string $htmlContent,
        ?string $replyTo = null,
        array $attachments = []
    ): array {
        $client = new self($host, $port, $encryption, $username, $password);
        return $client->send($toEmail, $toName, $fromEmail, $fromName, $subject, $htmlContent, $replyTo, $attachments);
    }
}
