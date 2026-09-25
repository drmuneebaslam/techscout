<?php
declare(strict_types=1);

namespace App\Core;

class IncomingWebhookEngine {

    /**
     * Whitelist of permitted target database tables for incoming webhook mapping
     */
    public static array $permittedTables = [
        'prompts' => [
            'columns' => [
                'title', 'slug', 'summary', 'prompt_template', 'prompt_type', 'aspect_ratio', 
                'negative_prompt', 'target_models', 'model_version', 'cfg_scale', 'steps', 
                'seed', 'sampler', 'style_preset', 'difficulty_level', 'system_prompt', 
                'parameters_json', 'how_to_use', 'sample_output', 'template_variables', 
                'featured_image_url', 'category_id'
            ],
            'unique_key' => 'slug',
            'is_extension' => true,
            'parent_content_type' => 'prompt'
        ],
        'contents' => [
            'columns' => ['title', 'slug', 'summary', 'body_html', 'content_type', 'status', 'meta_description', 'canonical_url', 'featured_image_url'],
            'unique_key' => 'slug',
            'is_extension' => false
        ],
        'ai_tools' => [
            'columns' => ['title', 'slug', 'summary', 'body_html', 'website_url', 'docs_url', 'pricing_type', 'starting_price', 'rating_score', 'verified', 'featured_image_url'],
            'unique_key' => 'slug',
            'is_extension' => true,
            'parent_content_type' => 'ai_tool'
        ],
        'articles' => [
            'columns' => ['title', 'slug', 'summary', 'body_html', 'read_time_minutes', 'primary_category', 'featured_image_url'],
            'unique_key' => 'slug',
            'is_extension' => true,
            'parent_content_type' => 'article'
        ],
        'newsletter_subscribers' => [
            'columns' => ['email', 'frequency', 'status', 'source_page'],
            'unique_key' => 'email',
            'is_extension' => false
        ],
        'custom_popups' => [
            'columns' => ['title', 'badge_text', 'headline', 'content_html', 'cta_button_text', 'cta_button_url', 'secondary_button_text', 'secondary_button_url', 'target_audience', 'popup_theme'],
            'unique_key' => 'title',
            'is_extension' => false
        ],
        'custom_api_endpoints' => [
            'columns' => ['name', 'slug', 'description', 'endpoint_type', 'target_table', 'selected_columns', 'where_conditions_json', 'order_by_column', 'order_direction', 'result_limit', 'custom_response_template', 'auth_required', 'secret_token', 'is_active'],
            'unique_key' => 'slug',
            'is_extension' => false
        ],
        'feedback_submissions' => [
            'columns' => ['feedback_type', 'message', 'contact_email', 'page_url', 'status'],
            'unique_key' => null,
            'is_extension' => false
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
        $isExtension = $tableDef['is_extension'] ?? false;

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
                $pdo = Database::getConnection();

                // If table is an extension table (prompts, ai_tools, articles)
                if ($isExtension) {
                    $slugVal = $extractedData['slug'] ?? ('item-' . time() . '-' . mt_rand(100, 999));
                    $titleVal = $extractedData['title'] ?? 'Untitled Item';
                    $summaryVal = $extractedData['summary'] ?? (isset($extractedData['prompt_template']) ? substr($extractedData['prompt_template'], 0, 160) : '');
                    $parentType = $tableDef['parent_content_type'] ?? 'prompt';
                    $imgUrl = $extractedData['featured_image_url'] ?? null;
                    $catId = isset($extractedData['category_id']) ? (int)$extractedData['category_id'] : null;

                    // 1. Check or create parent `contents` row
                    $existingContent = Database::fetch("SELECT id FROM contents WHERE slug = ? LIMIT 1", [$slugVal]);
                    $contentId = null;

                    if ($existingContent) {
                        $contentId = (int)$existingContent['id'];
                        Database::execute(
                            "UPDATE contents SET title = ?, summary = ?, featured_image_url = COALESCE(?, featured_image_url), updated_at = NOW() WHERE id = ?",
                            [$titleVal, $summaryVal, $imgUrl, $contentId]
                        );
                    } else {
                        Database::execute(
                            "INSERT INTO contents (uuid, content_type, author_id, category_id, title, slug, summary, featured_image_url, status, visibility, is_featured, is_trending, published_at, created_at, updated_at) 
                             VALUES (UUID(), ?, 1, ?, ?, ?, ?, ?, 'published', 'public', 0, 0, NOW(), NOW(), NOW())",
                            [$parentType, $catId, $titleVal, $slugVal, $summaryVal, $imgUrl]
                        );
                        $contentId = (int)$pdo->lastInsertId();
                    }

                    // 2. Prepare extension table columns
                    $childCols = $pdo->query("SHOW COLUMNS FROM `{$targetTable}`")->fetchAll(\PDO::FETCH_COLUMN);
                    $childData = [];

                    foreach ($extractedData as $k => $v) {
                        if (in_array($k, $childCols, true)) {
                            $childData[$k] = $v;
                        }
                    }

                    // Ensure foreign key
                    if (in_array('content_id', $childCols, true)) {
                        $childData['content_id'] = $contentId;
                    }

                    // Insert or update child record
                    if (!empty($childData)) {
                        $cCols = array_keys($childData);
                        $cPlaceholders = array_fill(0, count($cCols), '?');
                        $cUpdates = [];
                        foreach ($cCols as $colName) {
                            if ($colName !== 'content_id') {
                                $cUpdates[] = "`{$colName}` = VALUES(`{$colName}`)";
                            }
                        }
                        $updateClause = !empty($cUpdates) ? " ON DUPLICATE KEY UPDATE " . implode(', ', $cUpdates) : "";
                        $sql = "INSERT INTO `{$targetTable}` (`" . implode('`, `', $cCols) . "`) VALUES (" . implode(', ', $cPlaceholders) . ")" . $updateClause;
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(array_values($childData));
                        $recordsAffected = $stmt->rowCount() ?: 1;
                        $insertedId = $contentId;
                    }
                } else {
                    // Standard direct single-table insertion
                    $columns = array_keys($extractedData);
                    $placeholders = array_fill(0, count($columns), '?');
                    $values = array_values($extractedData);

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

                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($values);
                    $recordsAffected = $stmt->rowCount();
                    $insertedId = $pdo->lastInsertId();
                }
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
