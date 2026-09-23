<?php
declare(strict_types=1);

namespace App\Core;

class IncomingWebhookEngine {

    /**
     * Whitelist of permitted target database tables for incoming webhook mapping
     */
    public static array $permittedTables = [
        'contents' => [
            'columns' => ['title', 'slug', 'summary', 'body_html', 'content_type', 'status', 'meta_description', 'canonical_url'],
            'unique_key' => 'slug'
        ],
        'ai_tools' => [
            'columns' => ['title', 'slug', 'website_url', 'docs_url', 'pricing_type', 'starting_price', 'rating_score', 'verified'],
            'unique_key' => 'slug'
        ],
        'prompts' => [
            'columns' => ['title', 'slug', 'prompt_template', 'prompt_type', 'target_models', 'variables_json'],
            'unique_key' => 'slug'
        ],
        'articles' => [
            'columns' => ['title', 'slug', 'summary', 'body_html', 'read_time_minutes', 'primary_category'],
            'unique_key' => 'slug'
        ],
        'newsletter_subscribers' => [
            'columns' => ['email', 'frequency', 'status', 'source_page'],
            'unique_key' => 'email'
        ],
        'custom_popups' => [
            'columns' => ['title', 'headline', 'content_html', 'cta_button_text', 'cta_button_url', 'badge_text'],
            'unique_key' => 'title'
        ],
        'feedback_submissions' => [
            'columns' => ['feedback_type', 'message', 'contact_email', 'page_url', 'status'],
            'unique_key' => null
        ]
    ];

    /**
     * Process incoming webhook request
     */
    public static function processPayload(string $slug, array $headers, string $rawBody, string $ip): array {
        $slug = trim($slug);
        if (empty($slug)) {
            return ['success' => false, 'code' => 400, 'error' => 'Endpoint slug is required'];
        }

        // 1. Fetch active webhook configuration
        $webhook = null;
        try {
            $webhook = Database::fetch("SELECT * FROM incoming_webhooks WHERE slug = ? AND is_active = 1", [$slug]);
        } catch (\Throwable $e) {}

        if (!$webhook) {
            self::logExecution(null, $slug, $headers, $rawBody, 'failed', 404, 0, 'Webhook endpoint not found or inactive', $ip);
            return ['success' => false, 'code' => 404, 'error' => "Incoming webhook endpoint '{$slug}' not found or currently inactive"];
        }

        $webhookId = (int)$webhook['id'];

        // 2. Authentication check
        $authType = $webhook['auth_type'] ?? 'none';
        $secretKey = $webhook['secret_key'] ?? '';

        if ($authType === 'bearer') {
            $authHeader = $headers['authorization'] ?? ($headers['Authorization'] ?? ($_SERVER['HTTP_AUTHORIZATION'] ?? ''));
            $token = '';
            if (preg_match('/Bearer\s+(\S+)/i', $authHeader, $matches)) {
                $token = $matches[1];
            }
            if (empty($token) || !hash_equals($secretKey, $token)) {
                self::logExecution($webhookId, $slug, $headers, $rawBody, 'failed', 401, 0, 'Bearer token authentication failed', $ip);
                return ['success' => false, 'code' => 401, 'error' => 'Unauthorized: Invalid Bearer token'];
            }
        } elseif ($authType === 'signature') {
            $signatureHeader = $headers['x-ts-signature'] ?? ($headers['X-Ts-Signature'] ?? ($headers['x-hub-signature-256'] ?? ''));
            $expectedSignature = hash_hmac('sha256', $rawBody, $secretKey);
            $cleanSignature = str_replace('sha256=', '', $signatureHeader);
            if (empty($cleanSignature) || !hash_equals($expectedSignature, $cleanSignature)) {
                self::logExecution($webhookId, $slug, $headers, $rawBody, 'failed', 401, 0, 'HMAC SHA-256 signature verification failed', $ip);
                return ['success' => false, 'code' => 401, 'error' => 'Unauthorized: Invalid HMAC signature'];
            }
        }

        // 3. JSON Payload Parsing with UTF-8 Sanitization (MISTAKE-03)
        $cleanBody = mb_convert_encoding($rawBody, 'UTF-8', 'UTF-8');
        $payload = json_decode($cleanBody, true);
        if ($payload === null && !empty($rawBody)) {
            self::logExecution($webhookId, $slug, $headers, $rawBody, 'failed', 400, 0, 'Invalid JSON payload structure', $ip);
            return ['success' => false, 'code' => 400, 'error' => 'Invalid JSON body: ' . json_last_error_msg()];
        }
        if (!is_array($payload)) {
            $payload = [];
        }

        // 4. Evaluate Dynamic Field Mappings
        $rules = json_decode($webhook['mapping_rules_json'] ?? '[]', true) ?: [];
        $targetTable = $webhook['target_table'] ?? 'contents';

        if (!isset(self::$permittedTables[$targetTable])) {
            $targetTable = 'contents';
        }

        $tableDef = self::$permittedTables[$targetTable];
        $allowedColumns = $tableDef['columns'];
        $uniqueKey = $tableDef['unique_key'];

        $extractedData = [];
        foreach ($rules as $rule) {
            $jsonKey = trim((string)($rule['json_key'] ?? ''));
            $targetCol = trim((string)($rule['target_column'] ?? ''));

            if ($jsonKey !== '' && in_array($targetCol, $allowedColumns, true)) {
                $val = self::extractNestedKey($payload, $jsonKey);
                if ($val !== null) {
                    $extractedData[$targetCol] = is_array($val) ? json_encode($val) : (string)$val;
                }
            }
        }

        // Auto-generate slug if title exists but slug not provided
        if (isset($extractedData['title']) && !isset($extractedData['slug']) && in_array('slug', $allowedColumns, true)) {
            $extractedData['slug'] = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $extractedData['title']), '-'));
        }

        $recordsAffected = 0;
        $insertedId = null;

        if (!empty($extractedData)) {
            try {
                $columns = array_keys($extractedData);
                $placeholders = array_fill(0, count($columns), '?');
                $values = array_values($extractedData);

                // If table has a unique key and unique value is provided, do INSERT ... ON DUPLICATE KEY UPDATE
                if ($uniqueKey && isset($extractedData[$uniqueKey])) {
                    $updates = [];
                    foreach ($columns as $c) {
                        if ($c !== $uniqueKey) {
                            $updates[] = "`{$c}` = VALUES(`{$c}`)";
                        }
                    }
                    $updateSql = !empty($updates) ? " ON DUPLICATE KEY UPDATE " . implode(', ', $updates) : "";
                    $sql = "INSERT INTO `{$targetTable}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $placeholders) . ")" . $updateSql;
                } else {
                    $sql = "INSERT INTO `{$targetTable}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $placeholders) . ")";
                }

                $stmt = Database::getConnection()->prepare($sql);
                $stmt->execute($values);
                $recordsAffected = $stmt->rowCount();
                $insertedId = Database::getConnection()->lastInsertId();
            } catch (\Throwable $e) {
                self::logExecution($webhookId, $slug, $headers, $rawBody, 'failed', 500, 0, 'Database insertion error: ' . $e->getMessage(), $ip);
                return ['success' => false, 'code' => 500, 'error' => 'Database mapping failed: ' . $e->getMessage()];
            }
        }

        // 5. Update Webhook Stats
        try {
            Database::execute("UPDATE incoming_webhooks SET received_count = received_count + 1, last_received_at = NOW() WHERE id = ?", [$webhookId]);
        } catch (\Throwable $e) {}

        // 6. Record Execution Log
        self::logExecution($webhookId, $slug, $headers, $rawBody, 'success', 200, $recordsAffected, null, $ip);

        return [
            'success' => true,
            'code' => 200,
            'message' => 'Payload successfully processed and mapped',
            'webhook_id' => $webhookId,
            'target_table' => $targetTable,
            'mapped_fields_count' => count($extractedData),
            'records_affected' => $recordsAffected,
            'record_id' => $insertedId ?: null
        ];
    }

    /**
     * Extracts values from dotted JSON paths e.g. "item.pricing.usd"
     */
    public static function extractNestedKey(array $data, string $keyPath): mixed {
        $keys = explode('.', $keyPath);
        $current = $data;
        foreach ($keys as $k) {
            if (is_array($current) && array_key_exists($k, $current)) {
                $current = $current[$k];
            } else {
                return null;
            }
        }
        return $current;
    }

    /**
     * Flattens a JSON structure into key-value pairs for interactive tree rendering
     */
    public static function flattenPayload(array $data, string $prefix = ''): array {
        $result = [];
        foreach ($data as $key => $value) {
            $newKey = $prefix === '' ? (string)$key : $prefix . '.' . $key;
            if (is_array($value)) {
                $result = array_merge($result, self::flattenPayload($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }
        return $result;
    }

    /**
     * Safely logs webhook execution to incoming_webhook_logs
     */
    private static function logExecution(?int $webhookId, string $slug, array $headers, string $body, string $status, int $code, int $affected, ?string $error, string $ip): void {
        try {
            $headersJson = json_encode($headers, JSON_UNESCAPED_SLASHES) ?: '{}';
            $payloadJson = $body ?: '{}';
            if (strlen($payloadJson) > 65535) {
                $payloadJson = substr($payloadJson, 0, 65530) . '...';
            }

            Database::execute(
                "INSERT INTO incoming_webhook_logs (webhook_id, endpoint_slug, headers_json, payload_json, status, response_code, records_affected, error_message, ip_address) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$webhookId, $slug, $headersJson, $payloadJson, $status, $code, $affected, $error, $ip]
            );
        } catch (\Throwable $e) {}
    }
}
