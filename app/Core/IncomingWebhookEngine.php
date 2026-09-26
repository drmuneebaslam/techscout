<?php
declare(strict_types=1);

namespace App\Core;

class IncomingWebhookEngine {

    /**
     * Dynamically retrieve definitions, columns, and primary keys for all database tables
     */
    public static function getTableDefinitions(): array {
        $tables = Database::getAllTablesWithMetadata();
        $definitions = [];

        foreach ($tables as $t) {
            $tableName = $t['table_name'];
            $cols = Database::getTableColumnsDetailed($tableName);
            $colNames = array_column($cols, 'Field');
            $uniqueKey = null;
            $hasContentId = in_array('content_id', $colNames, true);

            foreach ($cols as $c) {
                if (($c['Key'] ?? '') === 'PRI') {
                    $uniqueKey = $c['Field'];
                    break;
                }
            }
            if (!$uniqueKey && in_array('slug', $colNames, true)) {
                $uniqueKey = 'slug';
            } elseif (!$uniqueKey && in_array('email', $colNames, true)) {
                $uniqueKey = 'email';
            }

            $parentContentType = null;
            if ($hasContentId) {
                if ($tableName === 'prompts') $parentContentType = 'prompt';
                elseif ($tableName === 'ai_tools') $parentContentType = 'ai_tool';
                elseif ($tableName === 'articles') $parentContentType = 'article';
                else $parentContentType = $tableName;
                $colNames = array_values(array_unique(array_merge(['title', 'slug', 'summary', 'featured_image_url', 'status', 'category_id'], $colNames)));
            }

            $definitions[$tableName] = [
                'columns' => $colNames,
                'unique_key' => $uniqueKey,
                'is_extension' => $hasContentId,
                'parent_content_type' => $parentContentType,
                'description' => $t['table_comment'] ?: "Database table {$tableName}",
                'total_rows' => (int)($t['table_rows'] ?? 0)
            ];
        }

        return $definitions;
    }

    /**
     * Process incoming webhook request dynamically without static table whitelisting.
     * Supports single payloads or batch record arrays targeting ANY database table.
     */
    public static function processPayload(string $slug, array $headers, string $rawBody, string $ip): array {
        $slug = trim($slug);
        if (empty($slug)) {
            return ['success' => false, 'code' => 400, 'error' => 'Endpoint slug is required'];
        }

        $isOmni = in_array(strtolower($slug), ['omni', 'universal', 'import', 'all'], true);

        // 1. Fetch active webhook configuration
        $webhook = null;
        try {
            $webhook = Database::fetch("SELECT * FROM incoming_webhooks WHERE slug = ? AND is_active = 1", [$slug]);
        } catch (\Throwable $e) {}

        if (!$webhook && !$isOmni) {
            self::logExecution(null, $slug, $headers, $rawBody, 'failed', 404, 0, 'Webhook endpoint not found or inactive', $ip);
            return ['success' => false, 'code' => 404, 'error' => "Incoming webhook endpoint '{$slug}' not found or currently inactive"];
        }

        $webhookId = $webhook ? (int)$webhook['id'] : null;

        // 2. Authentication check
        $authType = $webhook['auth_type'] ?? ($isOmni ? 'bearer' : 'none');
        $secretKey = $webhook['secret_key'] ?? Config::get('deploy_webhook_secret', 'ts_deploy_sec_99182374619a8b');

        $authHeader = $headers['authorization'] ?? ($headers['Authorization'] ?? ($_SERVER['HTTP_AUTHORIZATION'] ?? ''));
        $token = '';
        if (preg_match('/Bearer\s+(\S+)/i', $authHeader, $matches)) {
            $token = trim($matches[1]);
        }
        if (empty($token) && !empty($_GET['token'])) {
            $token = trim((string)$_GET['token']);
        }
        if (empty($token) && !empty($_POST['token'])) {
            $token = trim((string)$_POST['token']);
        }

        if ($authType === 'bearer') {
            $adminSecret = Config::get('deploy_webhook_secret', 'ts_deploy_sec_99182374619a8b');
            $isValid = (!empty($token) && (hash_equals((string)$secretKey, $token) || hash_equals((string)$adminSecret, $token)));
            // Also allow admin session
            if (!$isValid && Auth::isAdmin()) {
                $isValid = true;
            }
            if (!$isValid) {
                self::logExecution($webhookId, $slug, $headers, $rawBody, 'failed', 401, 0, 'Bearer token authentication failed', $ip);
                return ['success' => false, 'code' => 401, 'error' => 'Unauthorized: Invalid or missing Bearer token'];
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

        // 4. Discover all database tables dynamically (no static whitelisting)
        $tableDefinitions = self::getTableDefinitions();
        $allTableNames = array_keys($tableDefinitions);

        // Determine records array: Support single item or batch array
        $itemsToProcess = [];
        if (isset($payload[0]) && is_array($payload[0])) {
            // Root JSON array [ {...}, {...} ]
            $itemsToProcess = $payload;
        } elseif (isset($payload['records']) && is_array($payload['records'])) {
            // Batch wrapper: { "table": "...", "records": [ {...} ] }
            $defaultTable = $payload['table'] ?? ($payload['target_table'] ?? null);
            foreach ($payload['records'] as $rec) {
                if (is_array($rec)) {
                    if ($defaultTable && !isset($rec['table']) && !isset($rec['target_table'])) {
                        $rec['table'] = $defaultTable;
                    }
                    $itemsToProcess[] = $rec;
                }
            }
        } elseif (isset($payload['items']) && is_array($payload['items'])) {
            $defaultTable = $payload['table'] ?? ($payload['target_table'] ?? null);
            foreach ($payload['items'] as $rec) {
                if (is_array($rec)) {
                    if ($defaultTable && !isset($rec['table']) && !isset($rec['target_table'])) {
                        $rec['table'] = $defaultTable;
                    }
                    $itemsToProcess[] = $rec;
                }
            }
        } else {
            // Single object payload
            $itemsToProcess[] = $payload;
        }

        $rules = json_decode($webhook['mapping_rules_json'] ?? '[]', true) ?: [];
        $defaultConfiguredTable = $webhook['target_table'] ?? 'contents';
        $headerTable = $headers['x-target-table'] ?? ($headers['X-Target-Table'] ?? ($_GET['table'] ?? null));

        $totalAffected = 0;
        $processedResults = [];
        $tablesModified = [];

        foreach ($itemsToProcess as $index => $itemData) {
            // Dynamic table resolution: item table > header/GET table > webhook default
            $targetTable = $itemData['table'] ?? ($itemData['target_table'] ?? ($itemData['_table'] ?? ($headerTable ?: $defaultConfiguredTable)));
            $targetTable = strtolower(trim((string)$targetTable));

            if (!in_array($targetTable, $allTableNames, true)) {
                $targetTable = in_array('contents', $allTableNames, true) ? 'contents' : ($allTableNames[0] ?? 'contents');
            }

            $tableDef = $tableDefinitions[$targetTable] ?? null;
            if (!$tableDef) {
                continue;
            }

            $allowedColumns = $tableDef['columns'];
            $uniqueKey = $tableDef['unique_key'];
            $isExtension = $tableDef['is_extension'] ?? false;

            $extractedData = [];

            // A. Apply explicit webhook mapping rules if configured
            foreach ($rules as $rule) {
                $jsonKey = trim((string)($rule['json_key'] ?? ''));
                $targetCol = trim((string)($rule['target_column'] ?? ''));

                if ($jsonKey !== '' && in_array($targetCol, $allowedColumns, true)) {
                    $val = self::extractNestedKey($itemData, $jsonKey);
                    if ($val !== null) {
                        $extractedData[$targetCol] = is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : (string)$val;
                    }
                }
            }

            // B. Automatic smart column mapping for any matching keys in itemData
            foreach ($itemData as $k => $v) {
                if (in_array($k, ['table', 'target_table', '_table', 'records', 'items'], true)) {
                    continue;
                }
                $cleanKey = strtolower(trim((string)$k));
                if (in_array($cleanKey, $allowedColumns, true) && !isset($extractedData[$cleanKey])) {
                    $extractedData[$cleanKey] = is_array($v) ? json_encode($v, JSON_UNESCAPED_UNICODE) : (string)$v;
                }
            }

            // C. Common alias mappings if target column still empty
            if (empty($extractedData['title']) && !empty($itemData['name']) && in_array('title', $allowedColumns, true)) {
                $extractedData['title'] = (string)$itemData['name'];
            }
            if (empty($extractedData['summary']) && !empty($itemData['description']) && in_array('summary', $allowedColumns, true)) {
                $extractedData['summary'] = (string)$itemData['description'];
            }
            if (empty($extractedData['featured_image_url']) && !empty($itemData['image']) && in_array('featured_image_url', $allowedColumns, true)) {
                $extractedData['featured_image_url'] = (string)$itemData['image'];
            }
            if (empty($extractedData['featured_image_url']) && !empty($itemData['image_url']) && in_array('featured_image_url', $allowedColumns, true)) {
                $extractedData['featured_image_url'] = (string)$itemData['image_url'];
            }

            // Auto-generate slug if title exists but slug not provided
            if (isset($extractedData['title']) && !isset($extractedData['slug']) && in_array('slug', $allowedColumns, true)) {
                $extractedData['slug'] = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $extractedData['title']), '-'));
            }

            if (empty($extractedData)) {
                continue;
            }

            try {
                $pdo = Database::getConnection();

                if ($isExtension) {
                    // Extension table (prompts, ai_tools, articles, etc.) linked to parent contents
                    $slugVal = $extractedData['slug'] ?? ('item-' . time() . '-' . mt_rand(100, 999));
                    $titleVal = $extractedData['title'] ?? 'Untitled Item';
                    $summaryVal = $extractedData['summary'] ?? (isset($extractedData['prompt_template']) ? substr($extractedData['prompt_template'], 0, 160) : '');
                    $parentType = $tableDef['parent_content_type'] ?? 'prompt';
                    $imgUrl = $extractedData['featured_image_url'] ?? null;
                    $catId = isset($extractedData['category_id']) ? (int)$extractedData['category_id'] : null;

                    // 1. Check or create parent contents row
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

                    // Auto-fill prompt_template if target is prompts table and not explicitly mapped
                    if ($targetTable === 'prompts' && in_array('prompt_template', $childCols, true) && empty($childData['prompt_template'])) {
                        $childData['prompt_template'] = !empty($summaryVal) ? $summaryVal : ($titleVal ?: 'Prompt Blueprint');
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
                        $rowAff = $stmt->rowCount() ?: 1;
                        $totalAffected += $rowAff;
                        $tablesModified[$targetTable] = ($tablesModified[$targetTable] ?? 0) + 1;
                        $processedResults[] = [
                            'index' => $index,
                            'table' => $targetTable,
                            'status' => 'upserted',
                            'record_id' => $contentId,
                            'slug' => $slugVal
                        ];
                    }
                } else {
                    // Standard direct single-table insertion or upsert
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
                    $rowAff = $stmt->rowCount() ?: 1;
                    $totalAffected += $rowAff;
                    $insId = $pdo->lastInsertId() ?: ($extractedData[$uniqueKey] ?? null);
                    $tablesModified[$targetTable] = ($tablesModified[$targetTable] ?? 0) + 1;
                    $processedResults[] = [
                        'index' => $index,
                        'table' => $targetTable,
                        'status' => 'upserted',
                        'record_id' => $insId
                    ];
                }
            } catch (\Throwable $e) {
                $processedResults[] = [
                    'index' => $index,
                    'table' => $targetTable,
                    'status' => 'error',
                    'error' => $e->getMessage()
                ];
            }
        }

        // 6. Update Webhook Stats
        if ($webhookId) {
            try {
                Database::execute("UPDATE incoming_webhooks SET received_count = received_count + 1, last_received_at = NOW() WHERE id = ?", [$webhookId]);
            } catch (\Throwable $e) {}
        }

        // 7. Record Execution Log
        self::logExecution($webhookId, $slug, $headers, $rawBody, ($totalAffected > 0 ? 'success' : 'processed'), 200, $totalAffected, null, $ip);

        return [
            'success' => true,
            'code' => 200,
            'message' => 'Payload successfully processed across ' . count($tablesModified) . ' table(s)',
            'webhook_id' => $webhookId,
            'tables_affected' => array_keys($tablesModified),
            'total_items_received' => count($itemsToProcess),
            'records_affected' => $totalAffected,
            'results' => $processedResults
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
