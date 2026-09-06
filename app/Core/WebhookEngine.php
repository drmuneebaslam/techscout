<?php
namespace App\Core;

use Throwable;

class WebhookEngine {
    /**
     * Dispatches an outbound webhook event with comprehensive A-to-Z telemetry.
     */
    public static function dispatch(string $event, array $payload = []): void {
        if (!FeatureFlag::isEnabled('enable_webhooks', true)) return;

        try {
            $webhooks = Database::query("SELECT * FROM webhooks WHERE is_active = 1");
            if (empty($webhooks)) return;

            $timestampEpoch = time();
            $timestampIso = gmdate('c');
            $traceId = 'trace_' . bin2hex(random_bytes(8));

            // Comprehensive A to Z Metadata Structure
            $fullPayload = [
                'event_id' => 'evt_' . bin2hex(random_bytes(8)),
                'event' => $event,
                'trace_id' => $traceId,
                'timestamp' => $timestampEpoch,
                'timestamp_iso' => $timestampIso,
                'platform' => [
                    'name' => 'TechScout Intelligence Platform',
                    'version' => '2.5.0',
                    'environment' => defined('APP_ENV') ? APP_ENV : 'production',
                    'base_url' => rtrim(Config::get('app_url'), '/')
                ],
                'client_context' => [
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                    'forwarded_ip' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null,
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'TechScout-Core',
                    'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'INTERNAL',
                    'request_uri' => $_SERVER['REQUEST_URI'] ?? '/'
                ],
                'triggered_by' => [
                    'user_id' => Auth::id() ?? null,
                    'username' => Auth::user()['username'] ?? null,
                    'role' => Auth::user()['role'] ?? 'guest'
                ],
                'data' => $payload
            ];

            $jsonPayload = json_encode($fullPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            foreach ($webhooks as $wh) {
                $subscribedEvents = json_decode($wh['events_json'] ?? '[]', true) ?: [];
                if (!in_array('*', $subscribedEvents, true) && !in_array($event, $subscribedEvents, true)) {
                    continue;
                }

                self::sendWebhook((int)$wh['id'], $wh['target_url'], $wh['secret_key'], $event, $jsonPayload, $traceId);
            }
        } catch (Throwable $e) {
            // Non-blocking webhook dispatch
        }
    }

    /**
     * Executes HTTP POST webhook delivery with HMAC-SHA256 signature and detailed logging.
     */
    public static function sendWebhook(int $webhookId, string $url, string $secret, string $event, string $jsonPayload, ?string $traceId = null): array {
        $startTime = microtime(true);
        $signature = hash_hmac('sha256', $jsonPayload, $secret);
        $deliveryId = 'del_' . bin2hex(random_bytes(8));
        $traceId = $traceId ?: ('trace_' . bin2hex(random_bytes(8)));

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: TechScout-Webhook-Engine/2.5',
            'X-TechScout-Event: ' . $event,
            'X-TechScout-Signature: ' . $signature,
            'X-TechScout-Delivery: ' . $deliveryId,
            'X-TechScout-Trace: ' . $traceId,
            'X-TechScout-Timestamp: ' . time()
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $responseBody = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        $durationMs = (int)((microtime(true) - $startTime) * 1000);
        $status = ($httpCode >= 200 && $httpCode < 300) ? 'success' : 'failed';
        $finalBody = $curlError ? "CURL Error: {$curlError}" : ($responseBody ?: 'No Response Body');

        try {
            Database::execute("INSERT INTO webhook_deliveries (webhook_id, event, payload_json, response_code, response_body, status, duration_ms, created_at)
                               VALUES (?, ?, ?, ?, ?, ?, ?, NOW())", [
                $webhookId,
                $event,
                $jsonPayload,
                $httpCode,
                mb_strimwidth($finalBody, 0, 3000, '...'),
                $status,
                $durationMs
            ]);
        } catch (Throwable $e) {}

        return [
            'status' => $status,
            'code' => $httpCode,
            'duration_ms' => $durationMs,
            'response' => $finalBody,
            'delivery_id' => $deliveryId,
            'trace_id' => $traceId
        ];
    }

    /**
     * Retries a previously recorded webhook delivery by its delivery ID.
     */
    public static function retryDelivery(int $deliveryId): array {
        try {
            $del = Database::fetch("SELECT d.*, w.target_url, w.secret_key FROM webhook_deliveries d JOIN webhooks w ON d.webhook_id = w.id WHERE d.id = ?", [$deliveryId]);
            if (!$del) {
                return ['success' => false, 'message' => 'Delivery log not found'];
            }

            $res = self::sendWebhook(
                (int)$del['webhook_id'],
                $del['target_url'],
                $del['secret_key'],
                $del['event'],
                $del['payload_json']
            );

            return ['success' => true, 'result' => $res];
        } catch (Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}

