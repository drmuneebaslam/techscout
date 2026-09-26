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
                elseif ($tableName === 'gpt_listings') $parentContentType = 'gpt_listing';
                else $parentContentType = $tableName;
                $colNames = array_values(array_unique(array_merge(['title', 'slug', 'summary', 'featured_image_url', 'status', 'category_id', 'tags'], $colNames)));
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
     * Supports:
     * 1. Multi-table payloads: { "tables": { "contents": {...}, "prompts": {...}, "tags": [...] } }
     * 2. Relational Entity Upserts: Auto-syncs parent contents + child extension table + tags + categories.
     * 3. Batch arrays: [ {...}, {...} ] or { "records": [ ... ] }
     * 4. Single-record direct payloads for ANY table in the database.
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

        // 4. Discover all database tables dynamically
        $tableDefinitions = self::getTableDefinitions();
        $allTableNames = array_keys($tableDefinitions);
        $rules = json_decode($webhook['mapping_rules_json'] ?? '[]', true) ?: [];
        $defaultConfiguredTable = $webhook['target_table'] ?? 'contents';
        $headerTable = $headers['x-target-table'] ?? ($headers['X-Target-Table'] ?? ($_GET['table'] ?? null));

        $totalAffected = 0;
        $processedResults = [];
        $tablesModified = [];

        // --------------------------------------------------------------------
        // Branch A: Explicit Multi-Table Structure: { "tables": { "contents": {...}, "prompts": {...}, "tags": [...] } }
        // --------------------------------------------------------------------
        if (isset($payload['tables']) && is_array($payload['tables'])) {
            $pdo = Database::getConnection();
            $pdo->beginTransaction();
            try {
                $sharedContentId = null;
                $sharedSlug = null;

                // Process 'contents' first if present to extract shared content_id
                if (isset($payload['tables']['contents']) && is_array($payload['tables']['contents'])) {
                    $cntData = $payload['tables']['contents'];
                    $slugVal = $cntData['slug'] ?? (!empty($cntData['title']) ? self::slugify($cntData['title']) : ('content-' . time()));
                    $sharedSlug = $slugVal;
                    $titleVal = $cntData['title'] ?? 'Untitled Ingested Content';
                    $summaryVal = $cntData['summary'] ?? '';
                    $contentType = $cntData['content_type'] ?? 'article';
                    $authorId = (int)($cntData['author_id'] ?? 1);
                    $categoryId = isset($cntData['category_id']) ? (int)$cntData['category_id'] : self::resolveCategoryId($cntData['category'] ?? ($cntData['category_name'] ?? null));
                    $imgUrl = $cntData['featured_image_url'] ?? ($cntData['image_url'] ?? null);

                    $existing = Database::fetch("SELECT id FROM contents WHERE slug = ? LIMIT 1", [$slugVal]);
                    if ($existing) {
                        $sharedContentId = (int)$existing['id'];
                        Database::execute(
                            "UPDATE contents SET title = ?, summary = ?, category_id = COALESCE(?, category_id), featured_image_url = COALESCE(?, featured_image_url), updated_at = NOW() WHERE id = ?",
                            [$titleVal, $summaryVal, $categoryId, $imgUrl, $sharedContentId]
                        );
                        $totalAffected += 1;
                    } else {
                        Database::execute(
                            "INSERT INTO contents (uuid, content_type, author_id, category_id, title, slug, summary, featured_image_url, status, visibility, is_featured, is_trending, published_at, created_at, updated_at) 
                             VALUES (UUID(), ?, ?, ?, ?, ?, ?, ?, 'published', 'public', 0, 0, NOW(), NOW(), NOW())",
                            [$contentType, $authorId, $categoryId, $titleVal, $slugVal, $summaryVal, $imgUrl]
                        );
                        $sharedContentId = (int)$pdo->lastInsertId();
                        $totalAffected += 1;
                    }
                    $tablesModified['contents'] = ($tablesModified['contents'] ?? 0) + 1;
                    $processedResults[] = ['table' => 'contents', 'record_id' => $sharedContentId, 'slug' => $sharedSlug, 'status' => 'upserted'];
                }

                // Process remaining tables
                foreach ($payload['tables'] as $tbl => $tblData) {
                    $tbl = strtolower(trim((string)$tbl));
                    if ($tbl === 'contents') continue; // already handled above

                    // Handle tags array: e.g. "tags": ["midjourney", "ai", "architecture"]
                    if ($tbl === 'tags' || $tbl === 'content_tags') {
                        if ($sharedContentId && is_array($tblData)) {
                            self::syncContentTags($sharedContentId, $tblData);
                            $tablesModified['content_tags'] = ($tablesModified['content_tags'] ?? 0) + count($tblData);
                            $processedResults[] = ['table' => 'content_tags', 'tags_synced' => count($tblData), 'status' => 'synced'];
                        }
                        continue;
                    }

                    if (!in_array($tbl, $allTableNames, true) || !is_array($tblData)) {
                        continue;
                    }

                    // Auto-link content_id if this child table expects it
                    $tblCols = $pdo->query("SHOW COLUMNS FROM `{$tbl}`")->fetchAll(\PDO::FETCH_COLUMN);
                    if ($sharedContentId && in_array('content_id', $tblCols, true) && !isset($tblData['content_id'])) {
                        $tblData['content_id'] = $sharedContentId;
                    }

                    // Perform safe upsert
                    $upsertRes = self::upsertTableRow($pdo, $tbl, $tblData, $tableDefinitions[$tbl]['unique_key'] ?? null);
                    if ($upsertRes['success']) {
                        $totalAffected += $upsertRes['affected'];
                        $tablesModified[$tbl] = ($tablesModified[$tbl] ?? 0) + 1;
                        $processedResults[] = ['table' => $tbl, 'record_id' => $upsertRes['record_id'], 'status' => 'upserted'];
                    }
                }

                $pdo->commit();
            } catch (\Throwable $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                self::logExecution($webhookId, $slug, $headers, $rawBody, 'failed', 500, 0, $e->getMessage(), $ip);
                return ['success' => false, 'code' => 500, 'error' => 'Multi-table transaction failed: ' . $e->getMessage()];
            }

            // Record execution stats
            if ($webhookId) {
                try {
                    Database::execute("UPDATE incoming_webhooks SET received_count = received_count + 1, last_received_at = NOW() WHERE id = ?", [$webhookId]);
                } catch (\Throwable $e) {}
            }

            self::logExecution($webhookId, $slug, $headers, $rawBody, ($totalAffected > 0 ? 'success' : 'ignored'), 200, $totalAffected, null, $ip);

            return [
                'success' => true,
                'code' => 200,
                'mode' => 'multi_table_transaction',
                'message' => 'Multi-table payload processed successfully across ' . count($tablesModified) . ' table(s)',
                'webhook_id' => $webhookId,
                'tables_affected' => array_keys($tablesModified),
                'records_affected' => $totalAffected,
                'results' => $processedResults
            ];
        }

        // --------------------------------------------------------------------
        // Branch B: Array of items, records wrapper, or single entity payload
        // --------------------------------------------------------------------
        $itemsToProcess = [];
        if (isset($payload[0]) && is_array($payload[0])) {
            $itemsToProcess = $payload;
        } elseif (isset($payload['records']) && is_array($payload['records'])) {
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
            $itemsToProcess[] = $payload;
        }

        $pdo = Database::getConnection();

        foreach ($itemsToProcess as $index => $itemData) {
            $targetTable = $itemData['table'] ?? ($itemData['target_table'] ?? ($itemData['_table'] ?? ($headerTable ?: $defaultConfiguredTable)));
            $targetTable = strtolower(trim((string)$targetTable));

            if (!in_array($targetTable, $allTableNames, true)) {
                $targetTable = in_array('contents', $allTableNames, true) ? 'contents' : ($allTableNames[0] ?? 'contents');
            }

            $tableDef = $tableDefinitions[$targetTable] ?? null;
            if (!$tableDef) continue;

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

            // B. Automatic smart column mapping for matching keys
            foreach ($itemData as $k => $v) {
                if (in_array($k, ['table', 'target_table', '_table', 'records', 'items'], true)) continue;
                $cleanKey = strtolower(trim((string)$k));
                if (in_array($cleanKey, $allowedColumns, true) && !isset($extractedData[$cleanKey])) {
                    $extractedData[$cleanKey] = is_array($v) ? json_encode($v, JSON_UNESCAPED_UNICODE) : (string)$v;
                }
            }

            // C. Common alias mappings
            if (empty($extractedData['title']) && !empty($itemData['name']) && in_array('title', $allowedColumns, true)) {
                $extractedData['title'] = (string)$itemData['name'];
            }
            if (empty($extractedData['summary']) && !empty($itemData['description']) && in_array('summary', $allowedColumns, true)) {
                $extractedData['summary'] = (string)$itemData['description'];
            }
            if (empty($extractedData['featured_image_url']) && !empty($itemData['image_url']) && in_array('featured_image_url', $allowedColumns, true)) {
                $extractedData['featured_image_url'] = (string)$itemData['image_url'];
            }

            // Auto-generate slug
            if (isset($extractedData['title']) && !isset($extractedData['slug']) && in_array('slug', $allowedColumns, true)) {
                $extractedData['slug'] = self::slugify($extractedData['title']);
            }

            if (empty($extractedData)) continue;

            try {
                if ($isExtension) {
                    // Unified Ingestion for Prompts, AI Tools, Articles, GPT Listings
                    $slugVal = $extractedData['slug'] ?? ('item-' . time() . '-' . mt_rand(100, 999));
                    $titleVal = $extractedData['title'] ?? 'Untitled Ingested Entity';
                    $summaryVal = $extractedData['summary'] ?? (isset($extractedData['prompt_template']) ? substr($extractedData['prompt_template'], 0, 160) : '');
                    $parentType = $tableDef['parent_content_type'] ?? 'prompt';
                    $imgUrl = $extractedData['featured_image_url'] ?? null;
                    $catId = isset($extractedData['category_id']) ? (int)$extractedData['category_id'] : self::resolveCategoryId($itemData['category'] ?? ($itemData['category_name'] ?? null));

                    // 1. Upsert parent contents record
                    $existingContent = Database::fetch("SELECT id FROM contents WHERE slug = ? LIMIT 1", [$slugVal]);
                    $contentId = null;

                    if ($existingContent) {
                        $contentId = (int)$existingContent['id'];
                        Database::execute(
                            "UPDATE contents SET title = ?, summary = ?, category_id = COALESCE(?, category_id), featured_image_url = COALESCE(?, featured_image_url), updated_at = NOW() WHERE id = ?",
                            [$titleVal, $summaryVal, $catId, $imgUrl, $contentId]
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
                    if (in_array('content_id', $childCols, true)) {
                        $childData['content_id'] = $contentId;
                    }

                    // Auto-fill prompt_template if target is prompts
                    if ($targetTable === 'prompts' && in_array('prompt_template', $childCols, true) && empty($childData['prompt_template'])) {
                        $childData['prompt_template'] = !empty($summaryVal) ? $summaryVal : ($titleVal ?: 'Prompt Blueprint');
                    }

                    // Sync child record
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
                        $tablesModified['contents'] = ($tablesModified['contents'] ?? 0) + 1;

                        // 3. Sync Tags if passed
                        $tagsInput = $itemData['tags'] ?? ($itemData['tag_list'] ?? null);
                        if (!empty($tagsInput) && $contentId) {
                            self::syncContentTags($contentId, is_array($tagsInput) ? $tagsInput : explode(',', (string)$tagsInput));
                        }

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
                    $upsertRes = self::upsertTableRow($pdo, $targetTable, $extractedData, $uniqueKey);
                    if ($upsertRes['success']) {
                        $totalAffected += $upsertRes['affected'];
                        $tablesModified[$targetTable] = ($tablesModified[$targetTable] ?? 0) + 1;
                        $processedResults[] = [
                            'index' => $index,
                            'table' => $targetTable,
                            'status' => 'upserted',
                            'record_id' => $upsertRes['record_id']
                        ];
                    }
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

        // 7. Record Execution Log (Always valid ENUM: 'success', 'failed', 'ignored')
        self::logExecution($webhookId, $slug, $headers, $rawBody, ($totalAffected > 0 ? 'success' : 'ignored'), 200, $totalAffected, null, $ip);

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
     * Helper to upsert a row into any database table
     */
    private static function upsertTableRow(\PDO $pdo, string $table, array $data, ?string $uniqueKey = null): array {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $values = array_values($data);

        if ($uniqueKey && isset($data[$uniqueKey])) {
            $updates = [];
            foreach ($columns as $c) {
                if ($c !== $uniqueKey) {
                    $updates[] = "`{$c}` = VALUES(`{$c}`)";
                }
            }
            $updateSql = !empty($updates) ? " ON DUPLICATE KEY UPDATE " . implode(', ', $updates) : "";
            $sql = "INSERT INTO `{$table}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $placeholders) . ")" . $updateSql;
        } else {
            $sql = "INSERT INTO `{$table}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $placeholders) . ")";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        $rowAff = $stmt->rowCount() ?: 1;
        $insId = $pdo->lastInsertId() ?: ($data[$uniqueKey] ?? null);

        return [
            'success' => true,
            'affected' => $rowAff,
            'record_id' => $insId
        ];
    }

    /**
     * Sync tags for a content item
     */
    public static function syncContentTags(int $contentId, array $tags): void {
        if ($contentId <= 0 || empty($tags)) return;
        try {
            foreach ($tags as $tagName) {
                $tagName = trim((string)$tagName);
                if (empty($tagName)) continue;
                $slug = self::slugify($tagName);

                // Upsert tag
                Database::execute(
                    "INSERT INTO tags (name, slug, created_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)",
                    [$tagName, $slug]
                );
                $tagId = (int)Database::lastInsertId();
                if ($tagId <= 0) {
                    $tRow = Database::fetch("SELECT id FROM tags WHERE slug = ? LIMIT 1", [$slug]);
                    $tagId = $tRow ? (int)$tRow['id'] : 0;
                }

                if ($tagId > 0) {
                    Database::execute(
                        "INSERT IGNORE INTO content_tags (content_id, tag_id) VALUES (?, ?)",
                        [$contentId, $tagId]
                    );
                }
            }
        } catch (\Throwable $e) {}
    }

    /**
     * Resolve category ID by name or slug
     */
    public static function resolveCategoryId(?string $catNameOrSlug): int {
        if (empty($catNameOrSlug)) return 1;
        $term = trim($catNameOrSlug);
        try {
            $row = Database::fetch("SELECT id FROM categories WHERE slug = ? OR name = ? LIMIT 1", [self::slugify($term), $term]);
            if ($row) return (int)$row['id'];
        } catch (\Throwable $e) {}
        return 1;
    }

    /**
     * Generate URL-friendly slug
     */
    public static function slugify(string $text): string {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text), '-'));
        return $slug ?: 'item-' . time();
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
     * Returns rich, production-ready sample payloads for ANY database table
     */
    public static function getSamplePayloadForTable(string $table): array {
        $table = strtolower(trim($table));

        switch ($table) {
            case 'prompts':
                return [
                    'table' => 'prompts',
                    'title' => 'Hyper-Realistic Brutalist Villa in Nordic Mist',
                    'slug' => 'brutalist-villa-nordic-mist',
                    'summary' => 'Architectural cinematic generation prompt for Midjourney v6 and DALL-E 3.',
                    'content_type' => 'prompt',
                    'prompt_type' => 'image',
                    'prompt_template' => '/imagine prompt: An ultra-photorealistic brutalist glass and concrete cantilevered villa overlooking a fjord, shrouded in morning atmospheric mist, soft diffused cinematic lighting, 8k resolution, Hasselblad X2D 100C --ar 16:9 --v 6.0 --style raw',
                    'negative_prompt' => 'blurry, oversaturated, deformed geometry, low quality, artifacts',
                    'model_compatibility' => 'Midjourney v6, DALL-E 3, Stable Diffusion XL',
                    'aspect_ratio' => '16:9',
                    'category' => 'Generative Art',
                    'status' => 'published',
                    'featured_image_url' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80',
                    'tags' => ['midjourney', 'architecture', 'photorealism', 'prompt-engineering']
                ];

            case 'ai_tools':
                return [
                    'table' => 'ai_tools',
                    'title' => 'QuantumFlow Neural Engine',
                    'slug' => 'quantumflow-neural-engine',
                    'summary' => 'High-throughput transformer inference engine with dynamic FP8 quantization and sub-millisecond latency.',
                    'content_type' => 'ai_tool',
                    'website_url' => 'https://github.com/techscout/quantumflow',
                    'pricing_type' => 'freemium',
                    'starting_price' => '$29/mo',
                    'is_verified' => 1,
                    'is_editor_pick' => 1,
                    'category' => 'Developer Tools',
                    'status' => 'published',
                    'featured_image_url' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=1200&q=80',
                    'tags' => ['inference', 'transformers', 'open-source', 'gpu-acceleration']
                ];

            case 'articles':
                return [
                    'table' => 'articles',
                    'title' => 'Architecting Autonomous Multi-Agent Swarms with Google Antigravity',
                    'slug' => 'architecting-autonomous-multi-agent-swarms',
                    'summary' => 'Deep dive into decentralized agent orchestration, reactive DAG pipelines, and memory clustering.',
                    'content_type' => 'article',
                    'body_html' => '<p>Autonomous multi-agent orchestration represents a pivotal shift from sequential prompt chaining toward dynamic, event-driven cognitive networks...</p><h2>Decentralized Consensus</h2><p>By leveraging lightweight message busses and memory checkpoints...</p>',
                    'reading_time_minutes' => 6,
                    'category' => 'AI Engineering',
                    'status' => 'published',
                    'featured_image_url' => 'https://images.unsplash.com/photo-1620712943543-bcc4688e7485?auto=format&fit=crop&w=1200&q=80',
                    'tags' => ['multi-agent', 'antigravity', 'distributed-systems', 'llm-architecture']
                ];

            case 'gpt_listings':
                return [
                    'table' => 'gpt_listings',
                    'title' => 'Full-Stack Code Architect GPT',
                    'slug' => 'fullstack-code-architect-gpt',
                    'summary' => 'Specialized assistant for refactoring legacy codebases into modern clean architecture with high test coverage.',
                    'content_type' => 'gpt_listing',
                    'openai_url' => 'https://chatgpt.com/g/g-sample-architect',
                    'capabilities_json' => ['Code Interpreter', 'Web Browsing', 'DALL-E 3'],
                    'starter_prompts_json' => ['Audit this SQL schema for N+1 query bottlenecks', 'Convert this monolithic endpoint to clean hexagonal architecture'],
                    'category' => 'Coding & Development',
                    'status' => 'published',
                    'tags' => ['chatgpt', 'gpts', 'code-review', 'refactoring']
                ];

            case 'jobs':
                return [
                    'table' => 'jobs',
                    'title' => 'Senior Machine Learning Systems Engineer',
                    'slug' => 'sr-ml-systems-engineer-' . time(),
                    'company_name' => 'Nexus Cognitive Labs',
                    'location' => 'San Francisco, CA (Remote)',
                    'location_type' => 'remote',
                    'salary_range' => '$190,000 - $240,000',
                    'apply_url' => 'https://nexus.example.com/careers/ml-systems',
                    'is_active' => 1,
                    'description' => 'We are seeking a senior systems engineer to scale our distributed inference clusters and Triton server pipelines.'
                ];

            case 'categories':
                return [
                    'table' => 'categories',
                    'name' => 'Autonomous Robotics',
                    'slug' => 'autonomous-robotics-' . time(),
                    'description' => 'Edge AI models, ROS2 frameworks, and computer vision systems for autonomous hardware.',
                    'icon_name' => 'bot',
                    'color_hex' => '#06b6d4',
                    'applicable_to' => 'all'
                ];

            case 'announcements':
                return [
                    'table' => 'announcements',
                    'title' => 'TechScout 2.5 Released',
                    'content' => 'Explore the all-new 100x Custom API Gateway and Live Ingestion Webhooks.',
                    'action_text' => 'Explore Features',
                    'action_url' => '/tools',
                    'badge_text' => 'NEW',
                    'type' => 'info',
                    'is_active' => 1
                ];

            default:
                // Dynamic schema inspection fallback
                try {
                    $cols = Database::getTableColumnsDetailed($table);
                    $sample = ['table' => $table];
                    foreach ($cols as $c) {
                        $col = $c['Field'];
                        $type = strtolower((string)$c['Type']);
                        if (in_array($col, ['id', 'created_at', 'updated_at'], true)) continue;

                        if (str_contains($type, 'int') || str_contains($type, 'decimal') || str_contains($type, 'float')) {
                            $sample[$col] = 1;
                        } elseif (str_contains($type, 'json') || str_contains($type, 'longtext')) {
                            $sample[$col] = ['sample_key' => 'sample_value'];
                        } elseif (str_contains($type, 'date')) {
                            $sample[$col] = date('Y-m-d H:i:s');
                        } else {
                            $sample[$col] = "Sample {$col}";
                        }
                    }
                    return $sample;
                } catch (\Throwable $e) {
                    return [
                        'table' => $table,
                        'name' => 'Sample Record',
                        'status' => 'active'
                    ];
                }
        }
    }

    /**
     * Returns rich Multi-Table combined sample demonstrating parent-child transactional ingestion
     */
    public static function getMultiTableSamplePayload(): array {
        return [
            'tables' => [
                'contents' => [
                    'title' => 'Cinematic Cyberpunk Urban Exploration Blueprint',
                    'slug' => 'cinematic-cyberpunk-urban-exploration',
                    'summary' => 'Atmospheric neon visual generation template for game developers and 3D artists.',
                    'content_type' => 'prompt',
                    'status' => 'published',
                    'featured_image_url' => 'https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=1200&q=80',
                    'category' => 'Generative Art'
                ],
                'prompts' => [
                    'prompt_type' => 'image',
                    'prompt_template' => '/imagine prompt: A lone cybernetic scout navigating rain-slicked neo-Tokyo alleyways illuminated by holographic advertisements, volumetric fog, octane render, 8k --ar 21:9 --style raw',
                    'negative_prompt' => 'flat lighting, low poly, noisy, oversaturated',
                    'model_compatibility' => 'Midjourney v6, Flux.1, SDXL',
                    'aspect_ratio' => '21:9'
                ],
                'tags' => ['cyberpunk', 'midjourney', 'octane-render', 'concept-art']
            ]
        ];
    }

    /**
     * Safely logs webhook execution to incoming_webhook_logs.
     * Guarantees status fits database schema without truncation (MISTAKE-01, MISTAKE-03).
     */
    private static function logExecution(?int $webhookId, string $slug, array $headers, string $body, string $status, int $code, int $affected, ?string $error, string $ip): void {
        try {
            // Strict normalization of status to prevent enum truncation
            $cleanStatus = in_array(strtolower($status), ['success', 'failed', 'ignored'], true) ? strtolower($status) : ($affected > 0 ? 'success' : 'ignored');

            $headersJson = json_encode($headers, JSON_UNESCAPED_SLASHES) ?: '{}';
            $payloadJson = $body ?: '{}';
            if (strlen($payloadJson) > 65535) {
                $payloadJson = substr($payloadJson, 0, 65530) . '...';
            }

            Database::execute(
                "INSERT INTO incoming_webhook_logs (webhook_id, endpoint_slug, headers_json, payload_json, status, response_code, records_affected, error_message, ip_address) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$webhookId, $slug, $headersJson, $payloadJson, $cleanStatus, $code, $affected, $error, $ip]
            );
        } catch (\Throwable $e) {}
    }
}
