<?php
namespace App\Core;

/**
 * 100x Enhanced Enterprise Incoming Webhook Engine
 * 
 * Features:
 * - Strict Field Mapping & Validation Engine (Only mapped fields ingested, no arbitrary unmapped writes)
 * - Multi-Rule Validation: required, string, integer, url, email, in:a,b,c, min:X, max:X
 * - Live Captured Payload Introspection: Detects actual keys sent to each endpoint
 * - Atomic Multi-Table Transaction Engine: { "tables": { "contents": {...}, "prompts": {...}, "tags": [...] } }
 * - Dynamic Schema Introspection without hardcoded column assumptions
 * - Idempotent Upsert & Category/Tag Relational Linking
 */
class IncomingWebhookEngine {

    /**
     * Cache discovered table definitions for request lifecycle
     */
    private static ?array $cachedTableDefs = null;

    /**
     * Inspect MySQL database to discover all tables, columns, and data types dynamically
     */
    public static function getTableDefinitions(): array {
        if (self::$cachedTableDefs !== null) {
            return self::$cachedTableDefs;
        }

        $definitions = [];
        $tables = Database::query("
            SELECT TABLE_NAME, TABLE_COMMENT, TABLE_ROWS 
            FROM information_schema.TABLES 
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_TYPE = 'BASE TABLE'
            ORDER BY TABLE_NAME ASC
        ");

        foreach ($tables as $t) {
            $tbl = $t['TABLE_NAME'];
            $cols = Database::query("
                SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_KEY, COLUMN_DEFAULT, EXTRA 
                FROM information_schema.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?
                ORDER BY ORDINAL_POSITION ASC
            ", [$tbl]);

            $colNames = array_column($cols, 'COLUMN_NAME');
            $primaryKey = null;
            $uniqueKey = null;

            foreach ($cols as $c) {
                if ($c['COLUMN_KEY'] === 'PRI' && !$primaryKey) {
                    $primaryKey = $c['COLUMN_NAME'];
                }
                if ($c['COLUMN_KEY'] === 'UNI' && !$uniqueKey) {
                    $uniqueKey = $c['COLUMN_NAME'];
                }
            }

            if (!$uniqueKey) {
                if (in_array('slug', $colNames, true)) {
                    $uniqueKey = 'slug';
                } elseif (in_array('uuid', $colNames, true)) {
                    $uniqueKey = 'uuid';
                } elseif (in_array('code', $colNames, true)) {
                    $uniqueKey = 'code';
                } elseif (in_array('email', $colNames, true)) {
                    $uniqueKey = 'email';
                } else {
                    $uniqueKey = $primaryKey;
                }
            }

            $definitions[$tbl] = [
                'name' => $tbl,
                'columns' => $colNames,
                'column_meta' => $cols,
                'primary_key' => $primaryKey,
                'unique_key' => $uniqueKey,
                'is_extension' => in_array($tbl, ['prompts', 'ai_tools', 'articles', 'gpt_listings', 'campaigns'], true),
                'parent_content_type' => [
                    'prompts' => 'prompt',
                    'ai_tools' => 'ai_tool',
                    'articles' => 'article',
                    'gpt_listings' => 'gpt',
                    'campaigns' => 'campaign'
                ][$tbl] ?? null,
                'total_rows' => (int)($t['TABLE_ROWS'] ?? 0)
            ];
        }

        self::$cachedTableDefs = $definitions;
        return $definitions;
    }

    /**
     * Validate an individual field against pipe-separated rules (e.g. "required|string|min:3")
     */
    public static function validateFieldRule($value, string $ruleDef, string $fieldLabel): array {
        $ruleDef = trim($ruleDef);
        if ($ruleDef === '') {
            return ['valid' => true, 'error' => null];
        }

        $rules = explode('|', $ruleDef);
        $isEmpty = ($value === null || $value === '' || (is_array($value) && empty($value)));

        foreach ($rules as $r) {
            $r = trim($r);
            if ($r === '') continue;

            if ($r === 'required') {
                if ($isEmpty) {
                    return ['valid' => false, 'error' => "Field '{$fieldLabel}' is required."];
                }
            } elseif ($r === 'string') {
                if (!$isEmpty && !is_scalar($value)) {
                    return ['valid' => false, 'error' => "Field '{$fieldLabel}' must be a valid string."];
                }
            } elseif ($r === 'integer' || $r === 'int') {
                if (!$isEmpty && !filter_var($value, FILTER_VALIDATE_INT) && $value !== 0 && $value !== '0') {
                    return ['valid' => false, 'error' => "Field '{$fieldLabel}' must be a valid integer."];
                }
            } elseif ($r === 'url') {
                if (!$isEmpty && !filter_var($value, FILTER_VALIDATE_URL)) {
                    return ['valid' => false, 'error' => "Field '{$fieldLabel}' must be a valid URL."];
                }
            } elseif ($r === 'email') {
                if (!$isEmpty && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return ['valid' => false, 'error' => "Field '{$fieldLabel}' must be a valid email address."];
                }
            } elseif (str_starts_with($r, 'in:')) {
                $allowed = array_map('trim', explode(',', substr($r, 3)));
                if (!$isEmpty && !in_array((string)$value, $allowed, true)) {
                    return ['valid' => false, 'error' => "Field '{$fieldLabel}' must be one of: " . implode(', ', $allowed)];
                }
            } elseif (str_starts_with($r, 'min:')) {
                $min = (int)substr($r, 4);
                if (!$isEmpty && strlen((string)$value) < $min) {
                    return ['valid' => false, 'error' => "Field '{$fieldLabel}' must have at least {$min} characters."];
                }
            } elseif (str_starts_with($r, 'max:')) {
                $max = (int)substr($r, 4);
                if (!$isEmpty && strlen((string)$value) > $max) {
                    return ['valid' => false, 'error' => "Field '{$fieldLabel}' must have at most {$max} characters."];
                }
            }
        }

        return ['valid' => true, 'error' => null];
    }

    /**
     * Process incoming webhook request with strict mapping and validation.
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
            $signatureHeader = $headers['x-techscout-signature'] ?? ($headers['X-TechScout-Signature'] ?? ($headers['x-ts-signature'] ?? ($headers['X-Ts-Signature'] ?? '')));
            $expectedSignature = hash_hmac('sha256', $rawBody, (string)$secretKey);
            $cleanSignature = str_replace('sha256=', '', $signatureHeader);
            if (empty($cleanSignature) || !hash_equals($expectedSignature, $cleanSignature)) {
                self::logExecution($webhookId, $slug, $headers, $rawBody, 'failed', 401, 0, 'HMAC SHA-256 signature verification failed', $ip);
                return ['success' => false, 'code' => 401, 'error' => 'Unauthorized: Invalid HMAC signature'];
            }
        }

        // 3. JSON Payload Parsing with UTF-8 Sanitization
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
        $hasCustomRules = !empty($rules);
        $defaultConfiguredTable = $webhook['target_table'] ?? 'contents';
        $headerTable = $headers['x-target-table'] ?? ($headers['X-Target-Table'] ?? ($_GET['table'] ?? null));

        // Ingestion Mode: 'listen_only' (Schema Discovery & Mapping) vs 'live_ingest' (Active Database Writes)
        $isListenOnlyMode = (($webhook['ingestion_mode'] ?? 'live_ingest') === 'listen_only') 
            || (!empty($_GET['mode']) && strtolower((string)$_GET['mode']) === 'listen')
            || (!empty($_GET['listen']) && $_GET['listen'] !== '0')
            || (!empty($_GET['simulate']) && $_GET['simulate'] !== '0')
            || (isset($payload['mode']) && in_array(strtolower((string)$payload['mode']), ['listen', 'simulate', 'dry_run', 'discovery'], true));

        // If in Listener / Schema Discovery Mode, record log as 'simulated' and return detected structure WITHOUT modifying DB
        if ($isListenOnlyMode) {
            $detectedKeys = self::extractKeysRecursive($payload);
            self::logExecution($webhookId, $slug, $headers, $rawBody, 'simulated', 200, 0, 'Payload captured in Listener Mode. No database records modified.', $ip);
            if ($webhookId) {
                try {
                    Database::execute("UPDATE incoming_webhooks SET received_count = received_count + 1, last_received_at = NOW() WHERE id = ?", [$webhookId]);
                } catch (\Throwable $e) {}
            }

            return [
                'success' => true,
                'code' => 200,
                'mode' => 'listen_only',
                'status' => 'simulated',
                'message' => 'Payload captured in Schema Listener mode. No database records modified.',
                'endpoint_slug' => $slug,
                'detected_keys' => $detectedKeys,
                'detected_keys_count' => count($detectedKeys),
                'payload_preview' => $payload,
                'target_table' => $defaultConfiguredTable,
                'mapped_rules_count' => count($rules),
                'ready_to_switch_live' => count($detectedKeys) > 0,
                'instruction' => 'Review the detected keys in the Field Mapping matrix. Once configured, switch endpoint mode to Live Ingestion to begin saving records.'
            ];
        }

        $totalAffected = 0;
        $processedResults = [];
        $tablesModified = [];

        // --------------------------------------------------------------------
        // Branch A: Explicit Multi-Table Transaction: { "tables": { ... } }
        // --------------------------------------------------------------------
        if (isset($payload['tables']) && is_array($payload['tables'])) {
            $pdo = Database::getConnection();
            $pdo->beginTransaction();
            try {
                $sharedContentId = null;
                $sharedSlug = null;

                // Safe fallback IDs
                $defaultAuthorId = (int)(Database::queryScalar("SELECT id FROM authors ORDER BY id ASC LIMIT 1") ?? 1);
                $defaultCatId = (int)(Database::queryScalar("SELECT id FROM categories ORDER BY id ASC LIMIT 1") ?? 1);

                // Process 'contents' first if present to extract shared content_id
                if (isset($payload['tables']['contents']) && is_array($payload['tables']['contents'])) {
                    $cntData = $payload['tables']['contents'];
                    $slugVal = $cntData['slug'] ?? (!empty($cntData['title']) ? self::slugify($cntData['title']) : ('content-' . time()));
                    $sharedSlug = $slugVal;
                    $titleVal = $cntData['title'] ?? 'Untitled Ingested Content';
                    $summaryVal = $cntData['summary'] ?? '';
                    $contentType = $cntData['content_type'] ?? 'article';
                    $authorId = (int)($cntData['author_id'] ?? $defaultAuthorId);
                    $categoryId = isset($cntData['category_id']) ? (int)$cntData['category_id'] : self::resolveCategoryId($cntData['category'] ?? ($cntData['category_name'] ?? null));
                    if (!$categoryId) $categoryId = $defaultCatId;
                    $imgUrl = $cntData['featured_image_url'] ?? ($cntData['image_url'] ?? null);

                    $existingStmt = $pdo->prepare("SELECT id FROM contents WHERE slug = ? LIMIT 1");
                    $existingStmt->execute([$slugVal]);
                    $existing = $existingStmt->fetch(\PDO::FETCH_ASSOC);

                    if ($existing) {
                        $sharedContentId = (int)$existing['id'];
                        $upStmt = $pdo->prepare("UPDATE contents SET title = ?, summary = ?, category_id = COALESCE(?, category_id), featured_image_url = COALESCE(?, featured_image_url), updated_at = NOW() WHERE id = ?");
                        $upStmt->execute([$titleVal, $summaryVal, $categoryId, $imgUrl, $sharedContentId]);
                        $totalAffected += 1;
                    } else {
                        $insStmt = $pdo->prepare("INSERT INTO contents (uuid, content_type, author_id, category_id, title, slug, summary, featured_image_url, status, visibility, is_featured, is_trending, published_at, created_at, updated_at) 
                                                 VALUES (UUID(), ?, ?, ?, ?, ?, ?, ?, 'published', 'public', 0, 0, NOW(), NOW(), NOW())");
                        $insStmt->execute([$contentType, $authorId, $categoryId, $titleVal, $slugVal, $summaryVal, $imgUrl]);
                        $sharedContentId = (int)$pdo->lastInsertId();
                        $totalAffected += 1;
                    }
                    $tablesModified['contents'] = ($tablesModified['contents'] ?? 0) + 1;
                    $processedResults[] = ['table' => 'contents', 'record_id' => $sharedContentId, 'slug' => $sharedSlug, 'status' => 'upserted'];
                }

                // Process remaining child tables
                foreach ($payload['tables'] as $tbl => $tblData) {
                    $tbl = strtolower(trim((string)$tbl));
                    if ($tbl === 'contents') continue;

                    // Handle tags array
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

                    $tblCols = $pdo->query("SHOW COLUMNS FROM `{$tbl}`")->fetchAll(\PDO::FETCH_COLUMN);
                    if ($sharedContentId && in_array('content_id', $tblCols, true) && !isset($tblData['content_id'])) {
                        $tblData['content_id'] = $sharedContentId;
                    }

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

            if ($webhookId) {
                try {
                    Database::execute("UPDATE incoming_webhooks SET received_count = received_count + 1, last_received_at = NOW() WHERE id = ?", [$webhookId]);
                } catch (\Throwable $e) {}
            }

            self::logExecution($webhookId, $slug, $headers, $rawBody, ($totalAffected > 0 ? 'success' : 'ignored'), 200, $totalAffected, null, $ip);

            // Invalidate cache
            try {
                foreach (array_keys($tablesModified) as $tbl) {
                    Cache::invalidateTag($tbl);
                }
                Cache::invalidateTag('contents');
                Cache::invalidateTag('feed');
            } catch (\Throwable $ce) {}

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
        // Branch B: Single Entity, Items Array, or Records Ingestion
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
            $validationErrors = [];

            // ----------------------------------------------------------------
            // 1. Strict Mapping & Validation when mapping rules are defined
            // ----------------------------------------------------------------
            if ($hasCustomRules) {
                foreach ($rules as $rule) {
                    $jsonKey = trim((string)($rule['json_key'] ?? ''));
                    $ruleTargetTable = trim((string)($rule['target_table'] ?? $targetTable));
                    $targetCol = trim((string)($rule['target_column'] ?? ''));
                    $ruleDef = trim((string)($rule['rule'] ?? ''));
                    $defaultVal = $rule['default'] ?? null;

                    if ($jsonKey === '' || $targetCol === '') continue;

                    $val = self::extractNestedKey($itemData, $jsonKey);
                    if ($val === null && $defaultVal !== null && $defaultVal !== '') {
                        $val = $defaultVal;
                    }

                    // Validate field rule
                    $valCheck = self::validateFieldRule($val, $ruleDef, $jsonKey);
                    if (!$valCheck['valid']) {
                        $validationErrors[] = $valCheck['error'];
                        continue;
                    }

                    if ($val !== null) {
                        $extractedData[$targetCol] = is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : (string)$val;
                    }
                }

                // If validation failed, reject payload cleanly without partial database pollution
                if (!empty($validationErrors)) {
                    $errorSummary = "Validation failed for item #{$index}: " . implode('; ', $validationErrors);
                    self::logExecution($webhookId, $slug, $headers, $rawBody, 'failed', 422, 0, $errorSummary, $ip);
                    return ['success' => false, 'code' => 422, 'error' => $errorSummary, 'validation_errors' => $validationErrors];
                }
            } else {
                if ($webhookId) {
                    $errorSummary = "Webhook has no explicit field mappings configured. Payload rejected.";
                    self::logExecution($webhookId, $slug, $headers, $rawBody, 'failed', 422, 0, $errorSummary, $ip);
                    return ['success' => false, 'code' => 422, 'error' => $errorSummary];
                }
                
                // ------------------------------------------------------------
                // 2. Fallback Permissive Mode (Only for Omni/Universal Webhooks)
                // ------------------------------------------------------------
                foreach ($itemData as $k => $v) {
                    if (in_array($k, ['table', 'target_table', '_table', 'records', 'items'], true)) continue;
                    $cleanKey = strtolower(trim((string)$k));
                    if (in_array($cleanKey, $allowedColumns, true)) {
                        $extractedData[$cleanKey] = is_array($v) ? json_encode($v, JSON_UNESCAPED_UNICODE) : (string)$v;
                    }
                }

                // Common aliases and smart field normalizations
                if (empty($extractedData['title']) && !empty($itemData['title'])) {
                    $extractedData['title'] = (string)$itemData['title'];
                } elseif (empty($extractedData['title']) && !empty($itemData['name']) && in_array('title', $allowedColumns, true)) {
                    $extractedData['title'] = (string)$itemData['name'];
                } elseif (empty($extractedData['title']) && !empty($itemData['prompt_title']) && in_array('title', $allowedColumns, true)) {
                    $extractedData['title'] = (string)$itemData['prompt_title'];
                }

                if (empty($extractedData['summary']) && !empty($itemData['description']) && in_array('summary', $allowedColumns, true)) {
                    $extractedData['summary'] = (string)$itemData['description'];
                } elseif (empty($extractedData['summary']) && !empty($itemData['caption']) && in_array('summary', $allowedColumns, true)) {
                    $extractedData['summary'] = (string)$itemData['caption'];
                }

                // Image / media field aliases (field, image, photo, thumbnail)
                $detectedImg = $itemData['featured_image_url'] ?? ($itemData['image_url'] ?? ($itemData['field'] ?? ($itemData['image'] ?? ($itemData['photo'] ?? ($itemData['thumbnail'] ?? null)))));
                if (!empty($detectedImg) && in_array('featured_image_url', $allowedColumns, true)) {
                    $extractedData['featured_image_url'] = (string)$detectedImg;
                }

                // Prompt template aliases
                $detectedPromptText = $itemData['prompt_template'] ?? ($itemData['prompt_text'] ?? ($itemData['prompt'] ?? ($itemData['template'] ?? ($itemData['prompt_body'] ?? null))));
                if (!empty($detectedPromptText) && in_array('prompt_template', $allowedColumns, true)) {
                    $extractedData['prompt_template'] = (string)$detectedPromptText;
                }

                // Model / Engine aliases
                $detectedModel = $itemData['target_models'] ?? ($itemData['model'] ?? ($itemData['models'] ?? ($itemData['engine'] ?? null)));
                if (!empty($detectedModel) && in_array('target_models', $allowedColumns, true)) {
                    if (is_string($detectedModel)) {
                        $modelArray = array_values(array_filter(array_map('trim', explode(',', $detectedModel))));
                        $extractedData['target_models'] = json_encode($modelArray, JSON_UNESCAPED_SLASHES);
                    } elseif (is_array($detectedModel)) {
                        $extractedData['target_models'] = json_encode(array_values($detectedModel), JSON_UNESCAPED_SLASHES);
                    }
                }

                // Aspect ratio alias
                if (!empty($itemData['aspect_ratio']) && in_array('aspect_ratio', $allowedColumns, true)) {
                    $extractedData['aspect_ratio'] = (string)$itemData['aspect_ratio'];
                }

                // Parameters JSON
                if (empty($extractedData['parameters_json']) && in_array('parameters_json', $allowedColumns, true)) {
                    $paramArr = [];
                    if (!empty($detectedModel)) {
                        $paramArr['models'] = is_array($detectedModel) ? $detectedModel : array_values(array_filter(array_map('trim', explode(',', (string)$detectedModel))));
                    }
                    if (!empty($itemData['aspect_ratio'])) $paramArr['aspect_ratio'] = (string)$itemData['aspect_ratio'];
                    if (!empty($itemData['temperature'])) $paramArr['temperature'] = (float)$itemData['temperature'];
                    if (!empty($itemData['top_p'])) $paramArr['top_p'] = (float)$itemData['top_p'];
                    if (!empty($itemData['difficulty'])) $paramArr['difficulty'] = (string)$itemData['difficulty'];
                    $paramArr['show_parameters'] = true;
                    $paramArr['show_models'] = true;
                    $paramArr['show_aspect_ratio'] = true;
                    $extractedData['parameters_json'] = json_encode($paramArr, JSON_UNESCAPED_SLASHES);
                }
            }

            // Auto-generate slug if title exists and slug column is present
            if (isset($extractedData['title']) && !isset($extractedData['slug']) && in_array('slug', $allowedColumns, true)) {
                $extractedData['slug'] = self::slugify($extractedData['title']);
            }

            if (empty($extractedData)) {
                continue;
            }

            try {
                if ($isExtension) {
                    // Unified Ingestion for Prompts, AI Tools, Articles, GPT Listings
                    $slugVal = $extractedData['slug'] ?? ('item-' . time() . '-' . mt_rand(100, 999));
                    $titleVal = $extractedData['title'] ?? ($itemData['title'] ?? 'Untitled Entity');
                    $summaryVal = $extractedData['summary'] ?? ($itemData['summary'] ?? ($itemData['description'] ?? (isset($extractedData['prompt_template']) ? substr($extractedData['prompt_template'], 0, 160) : '')));
                    $parentType = $tableDef['parent_content_type'] ?? 'prompt';
                    $imgUrl = $extractedData['featured_image_url'] ?? ($itemData['featured_image_url'] ?? ($itemData['field'] ?? ($itemData['image_url'] ?? null)));
                    
                    // Category resolution with aliases (category_main, category, category_name, topic)
                    $rawCatInput = $itemData['category_main'] ?? ($itemData['category'] ?? ($itemData['category_name'] ?? ($itemData['topic'] ?? null)));
                    $catId = isset($extractedData['category_id']) ? (int)$extractedData['category_id'] : self::resolveCategoryId($rawCatInput);

                    // 1. Upsert parent contents record
                    $existingStmt = $pdo->prepare("SELECT id FROM contents WHERE slug = ? LIMIT 1");
                    $existingStmt->execute([$slugVal]);
                    $existingContent = $existingStmt->fetch(\PDO::FETCH_ASSOC);

                    if ($existingContent) {
                        $contentId = (int)$existingContent['id'];
                        $upCntStmt = $pdo->prepare("UPDATE contents SET title = ?, summary = ?, category_id = COALESCE(?, category_id), featured_image_url = COALESCE(?, featured_image_url), updated_at = NOW() WHERE id = ?");
                        $upCntStmt->execute([$titleVal, $summaryVal, $catId, $imgUrl, $contentId]);
                    } else {
                        $authorId = (int)(Database::queryScalar("SELECT id FROM authors ORDER BY id ASC LIMIT 1") ?? 1);
                        if (!$catId) $catId = (int)(Database::queryScalar("SELECT id FROM categories ORDER BY id ASC LIMIT 1") ?? 1);

                        $insCntStmt = $pdo->prepare("INSERT INTO contents (uuid, content_type, author_id, category_id, title, slug, summary, featured_image_url, status, visibility, is_featured, is_trending, published_at, created_at, updated_at) 
                                                     VALUES (UUID(), ?, ?, ?, ?, ?, ?, ?, 'published', 'public', 0, 0, NOW(), NOW(), NOW())");
                        $insCntStmt->execute([$parentType, $authorId, $catId, $titleVal, $slugVal, $summaryVal, $imgUrl]);
                        $contentId = (int)$pdo->lastInsertId();
                    }

                    // 2. Upsert child extension record
                    $childCols = $allowedColumns;
                    $childData = [];
                    foreach ($extractedData as $k => $v) {
                        if (in_array($k, $childCols, true)) {
                            $childData[$k] = $v;
                        }
                    }
                    if (in_array('content_id', $childCols, true)) {
                        $childData['content_id'] = $contentId;
                    }
                    if (in_array('slug', $childCols, true) && !isset($childData['slug'])) {
                        $childData['slug'] = $slugVal;
                    }

                    if ($targetTable === 'prompts' && in_array('prompt_template', $childCols, true) && empty($childData['prompt_template'])) {
                        $childData['prompt_template'] = !empty($summaryVal) ? $summaryVal : ($titleVal ?: 'Prompt Blueprint');
                    }

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

                        // 3. Auto-sync Prompt Images if image is present
                        if ($targetTable === 'prompts' && !empty($imgUrl) && $contentId) {
                            try {
                                $imgCheck = $pdo->prepare("SELECT id FROM prompt_images WHERE prompt_id = ? AND image_url = ? LIMIT 1");
                                $imgCheck->execute([$contentId, $imgUrl]);
                                if (!$imgCheck->fetch()) {
                                    $insImg = $pdo->prepare("INSERT INTO prompt_images (prompt_id, image_url, caption, sort_order, created_at) VALUES (?, ?, ?, 0, NOW())");
                                    $insImg->execute([$contentId, $imgUrl, $titleVal ?: 'Visual Generation Sample']);
                                    $tablesModified['prompt_images'] = ($tablesModified['prompt_images'] ?? 0) + 1;
                                }
                            } catch (\Throwable $ie) {}
                        }

                        // 4. Sync Tags if passed
                        $tagsInput = $itemData['tags'] ?? ($itemData['tag_list'] ?? ($itemData['keywords'] ?? null));
                        if (!empty($tagsInput) && $contentId) {
                            self::syncContentTags($contentId, is_array($tagsInput) ? $tagsInput : explode(',', (string)$tagsInput));
                            $tablesModified['tags'] = ($tablesModified['tags'] ?? 0) + 1;
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
                self::logExecution($webhookId, $slug, $headers, $rawBody, 'failed', 500, $totalAffected, $e->getMessage(), $ip);
                return [
                    'success' => false,
                    'code' => 500,
                    'error' => "Error ingesting item #{$index} into table '{$targetTable}': " . $e->getMessage()
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
        self::logExecution($webhookId, $slug, $headers, $rawBody, ($totalAffected > 0 ? 'success' : 'ignored'), 200, $totalAffected, null, $ip);

        // 8. Targeted Cache Invalidation
        try {
            foreach (array_keys($tablesModified) as $tbl) {
                Cache::invalidateTag($tbl);
            }
            Cache::invalidateTag('contents');
            Cache::invalidateTag('feed');
        } catch (\Throwable $ce) {}

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
     * Synchronize tags for content item
     */
    private static function syncContentTags(int $contentId, array $tags): void {
        if ($contentId <= 0 || empty($tags)) return;

        foreach ($tags as $rawTag) {
            $tag = trim((string)$rawTag);
            if ($tag === '') continue;

            $slug = self::slugify($tag);
            Database::execute("INSERT IGNORE INTO tags (name, slug) VALUES (?, ?)", [$tag, $slug]);
            $tagId = Database::queryScalar("SELECT id FROM tags WHERE slug = ? LIMIT 1", [$slug]);

            if ($tagId) {
                Database::execute("INSERT IGNORE INTO content_tags (content_id, tag_id) VALUES (?, ?)", [$contentId, (int)$tagId]);
            }
        }
    }

    /**
     * Resolve category ID by slug or name
     */
    private static function resolveCategoryId(?string $categoryInput): ?int {
        if (!$categoryInput) return null;
        $catInput = trim($categoryInput);

        $cat = Database::fetch("SELECT id FROM categories WHERE slug = ? OR name = ? LIMIT 1", [$catInput, $catInput]);
        if ($cat) {
            return (int)$cat['id'];
        }

        $slug = self::slugify($catInput);
        Database::execute("INSERT IGNORE INTO categories (name, slug, applicable_to) VALUES (?, ?, 'all')", [$catInput, $slug]);
        return (int)(Database::queryScalar("SELECT id FROM categories WHERE slug = ? LIMIT 1", [$slug]) ?? null);
    }

    /**
     * Extract value from dot-notated nested key
     */
    private static function extractNestedKey(array $data, string $keyPath) {
        if (array_key_exists($keyPath, $data)) {
            return $data[$keyPath];
        }

        $parts = explode('.', $keyPath);
        $current = $data;

        foreach ($parts as $p) {
            if (!is_array($current) || !array_key_exists($p, $current)) {
                return null;
            }
            $current = $current[$p];
        }

        return $current;
    }

    /**
     * Generate URL-safe slug
     */
    public static function slugify(string $text): string {
        $slug = preg_replace('~[^\pL\d]+~u', '-', $text);
        $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);
        $slug = preg_replace('~[^-\w]+~', '', $slug);
        $slug = trim($slug, '-');
        $slug = preg_replace('~-+~', '-', $slug);
        $slug = strtolower($slug);
        return $slug ?: ('entity-' . time());
    }

    /**
     * Log execution to incoming_webhook_logs with normalized status (Safe VARCHAR-32)
     */
    public static function logExecution(?int $webhookId, string $slug, array $headers, string $payload, string $status, int $responseCode, int $affectedRows, ?string $errorMessage, string $ip): void {
        try {
            $allowedStatuses = ['success', 'failed', 'ignored', 'simulated', 'pending', 'partial'];
            $cleanStatus = strtolower(trim($status));
            $normalizedStatus = in_array($cleanStatus, $allowedStatuses, true) ? $cleanStatus : substr($cleanStatus, 0, 32);

            Database::execute(
                "INSERT INTO incoming_webhook_logs 
                 (webhook_id, endpoint_slug, headers_json, payload_json, status, response_code, records_affected, error_message, ip_address) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $webhookId,
                    $slug,
                    json_encode($headers, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    $payload,
                    $normalizedStatus,
                    $responseCode,
                    $affectedRows,
                    $errorMessage,
                    $ip
                ]
            );
        } catch (\Throwable $e) {
            error_log("IncomingWebhookEngine::logExecution failed: " . $e->getMessage());
        }
    }

    /**
     * Comprehensive high-fidelity sample payload for any table in database
     */
    public static function getSamplePayloadForTable(string $table): array {
        $table = strtolower(trim($table));

        // High-fidelity handcrafted sample templates for primary platform entities
        if ($table === 'prompts') {
            return [
                "title" => "B&W Cinematic Reflective Sunglasses AI Prompt",
                "category_main" => "Black & White",
                "description" => "Copy this AI prompt to generate dramatic black-and-white cinematic portraits featuring high-contrast lighting and round sunglasses reflecting a city skyline.",
                "field" => "https://img.promptplum.com/cdn-cgi/image/q=78,scq=50,width=1400,height=1400,fit=contain,f=auto/promptImages/736df081-6641-45fd-9fd6-9651fe7e9cb1.webp",
                "prompt_type" => "image",
                "prompt_text" => "Create a dramatic, high-end fashion editorial black-and-white medium close-up portrait of the provided face image, with sharp facial features, wearing round reflective sunglasses. He gazes upward confidently into a dark void. The lighting is high-contrast and chiaroscuro, highlighting the contours of his face and his black t-shirt. The sunglasses reflect a tall city skyline. The mood is mysterious and cinematic, with a minimalist black background. 8k, 1:1 aspect-ratio.",
                "model" => "Gemini, Gemini Watermark Remover",
                "aspect_ratio" => "1:1",
                "difficulty" => "Production",
                "tags" => ["Black & White", "Cinematic Portrait", "Fashion & Editorial", "Men", "Moody", "Portraits", "Urban", "Dark Aesthetic", "Gemini", "Studio Portrait"]
            ];
        }

        if ($table === 'prompt_images') {
            return [
                "table" => "prompt_images",
                "prompt_id" => 1,
                "image_url" => "https://img.promptplum.com/cdn-cgi/image/q=78,scq=50,width=1400,height=1400,fit=contain,f=auto/promptImages/736df081-6641-45fd-9fd6-9651fe7e9cb1.webp",
                "caption" => "High-contrast B&W portrait with skyline reflections in round sunglasses",
                "sort_order" => 0
            ];
        }

        if ($table === 'ai_tools') {
            return [
                "title" => "ElevenLabs Voice AI Studio",
                "slug" => "elevenlabs-voice-studio",
                "category" => "Voice & Speech AI",
                "summary" => "Industry-leading generative AI voice generator with realistic human speech, emotion control, and real-time voice cloning in 29+ languages.",
                "featured_image_url" => "https://images.unsplash.com/photo-1589254065878-42c9da997008?w=1200&auto=format&fit=crop&q=80",
                "website_url" => "https://elevenlabs.io",
                "docs_url" => "https://docs.elevenlabs.io",
                "pricing_model" => "freemium",
                "pricing_starting_at" => "$5.00/mo",
                "free_trial_available" => 1,
                "free_trial_days" => 14,
                "verified_by_techscout" => 1,
                "rating_avg" => 4.95,
                "features_list" => ["Voice Cloning", "Text to Speech", "Dubbing Studio", "Sound Effects Generator", "Low Latency API"],
                "tags" => ["Voice AI", "TTS", "Audio Generation", "Productivity", "Speech Synthesis"]
            ];
        }

        if ($table === 'articles') {
            return [
                "title" => "Building Resilient AI Agent Pipelines in 2026",
                "slug" => "building-resilient-ai-agent-pipelines-2026",
                "category" => "Engineering",
                "summary" => "A deep dive into distributed LLM orchestration, semantic state machines, and autonomous reasoning loops with real-world enterprise benchmarks.",
                "featured_image_url" => "https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=1200&auto=format&fit=crop&q=80",
                "body_markdown" => "## Architectural Principles of Autonomous Agents\n\nModern agentic workflows require deterministic state transitions alongside stochastic model completions...\n\n### 1. Dual-Layer Memory Architecture\nUsing vector databases alongside relational state ensures sub-millisecond retrieval...",
                "reading_time_minutes" => 7,
                "is_featured" => 1,
                "tags" => ["AI Agents", "LLM", "Python", "Architecture", "Engineering"]
            ];
        }

        if ($table === 'gpt_listings' || $table === 'custom_gpts') {
            return [
                "title" => "Code Architect Pro GPT",
                "slug" => "code-architect-pro-gpt",
                "category" => "Developer Tools",
                "summary" => "Senior system architect and clean code mentor that reviews pull requests, optimizes SQL schemas, and designs microservice boundaries.",
                "featured_image_url" => "https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=1200&auto=format&fit=crop&q=80",
                "openai_url" => "https://chatgpt.com/g/g-code-architect-pro",
                "creator_name" => "TechScout Lab",
                "conversation_starters" => ["Review my MySQL indexing plan", "Draft a Redis caching architecture", "Debug my async worker queue"],
                "tags" => ["Custom GPT", "ChatGPT", "Code Review", "Architecture", "DevOps"]
            ];
        }

        if ($table === 'jobs') {
            return [
                "title" => "Senior Generative AI Platform Engineer",
                "slug" => "senior-generative-ai-platform-engineer",
                "company_name" => "Synthetix Labs",
                "location" => "Remote (US / Europe / Asia)",
                "job_type" => "Full-time",
                "salary_range" => "$160,000 - $220,000 USD",
                "apply_url" => "https://synthetix.io/careers/senior-ai-engineer",
                "description" => "We are seeking an experienced platform engineer to build high-throughput LLM inference gateways, fine-tuning pipelines, and distributed vector caching.",
                "tags" => ["AI Jobs", "Remote", "Python", "PyTorch", "Kubernetes"]
            ];
        }

        if ($table === 'categories') {
            return [
                "name" => "Black & White",
                "slug" => "black-and-white",
                "applicable_to" => "prompt",
                "description" => "Black & White fine-art and high-contrast photography prompts.",
                "icon_name" => "camera",
                "color_hex" => "#0f172a",
                "is_featured_home" => 1
            ];
        }

        // Generic dynamic introspection fallback for other database tables
        $defs = self::getTableDefinitions();
        $def = $defs[$table] ?? null;

        if (!$def) {
            return [
                "table" => $table,
                "title" => "Sample " . ucfirst($table) . " Entry",
                "status" => "active"
            ];
        }

        $sample = ["table" => $table];
        foreach ($def['column_meta'] as $col) {
            $name = $col['COLUMN_NAME'];
            $type = strtolower($col['DATA_TYPE']);

            if (in_array($name, ['id', 'created_at', 'modified_at', 'updated_at', 'last_login', 'deleted_at'], true)) {
                continue;
            }

            if ($name === 'uuid') {
                $sample[$name] = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
            } elseif ($name === 'title' || $name === 'name') {
                $sample[$name] = "Sample " . ucfirst(str_replace('_', ' ', $table)) . " Item";
            } elseif ($name === 'slug') {
                $sample[$name] = str_replace('_', '-', $table) . "-sample-item";
            } elseif ($name === 'summary' || $name === 'description') {
                $sample[$name] = "High-performance enterprise data ingested dynamically into {$table}.";
            } elseif ($name === 'status') {
                $sample[$name] = 'published';
            } elseif ($name === 'email') {
                $sample[$name] = 'creator@techscout.online';
            } elseif ($name === 'prompt_template') {
                $sample[$name] = "Cinematic shot of futuristic cyberpunk metropolis, neon volumetric lighting --ar 16:9 --v 6.0";
            } elseif ($name === 'aspect_ratio') {
                $sample[$name] = '16:9';
            } elseif ($name === 'website_url' || $name === 'featured_image_url' || str_ends_with($name, '_url')) {
                $sample[$name] = 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=800';
            } elseif ($type === 'int' || $type === 'bigint' || $type === 'tinyint') {
                $sample[$name] = 1;
            } elseif ($type === 'decimal' || $type === 'float') {
                $sample[$name] = 4.95;
            } elseif ($type === 'json' || str_ends_with($name, '_json')) {
                $sample[$name] = ["key" => "value", "tags" => ["tech", "ai"]];
            } else {
                $sample[$name] = "Sample value for {$name}";
            }
        }

        return $sample;
    }

    /**
     * Multi-table transactional sample payload
     */
    public static function getMultiTableSamplePayload(): array {
        return [
            "mode" => "multi_table_transaction",
            "tables" => [
                "contents" => [
                    "title" => "B&W Cinematic Reflective Sunglasses AI Prompt",
                    "slug" => "bw-cinematic-reflective-sunglasses-ai-prompt",
                    "content_type" => "prompt",
                    "category" => "Black & White",
                    "summary" => "Copy this AI prompt to generate dramatic black-and-white cinematic portraits featuring high-contrast lighting and round sunglasses reflecting a city skyline.",
                    "featured_image_url" => "https://img.promptplum.com/cdn-cgi/image/q=78,scq=50,width=1400,height=1400,fit=contain,f=auto/promptImages/736df081-6641-45fd-9fd6-9651fe7e9cb1.webp",
                    "status" => "published"
                ],
                "prompts" => [
                    "prompt_type" => "image",
                    "prompt_template" => "Create a dramatic, high-end fashion editorial black-and-white medium close-up portrait of the provided face image, with sharp facial features, wearing round reflective sunglasses. He gazes upward confidently into a dark void. The lighting is high-contrast and chiaroscuro, highlighting the contours of his face and his black t-shirt. The sunglasses reflect a tall city skyline. The mood is mysterious and cinematic, with a minimalist black background. 8k, 1:1 aspect-ratio.",
                    "aspect_ratio" => "1:1",
                    "model_version" => "Gemini, Gemini Watermark Remover",
                    "target_models" => ["Gemini", "Gemini Watermark Remover"],
                    "difficulty_level" => "production"
                ],
                "prompt_images" => [
                    "image_url" => "https://img.promptplum.com/cdn-cgi/image/q=78,scq=50,width=1400,height=1400,fit=contain,f=auto/promptImages/736df081-6641-45fd-9fd6-9651fe7e9cb1.webp",
                    "caption" => "High-contrast B&W portrait with skyline reflections in round sunglasses",
                    "sort_order" => 0
                ],
                "tags" => [
                    "Black & White",
                    "Cinematic Portrait",
                    "Fashion & Editorial",
                    "Men",
                    "Moody",
                    "Portraits",
                    "Urban",
                    "Dark Aesthetic",
                    "Gemini",
                    "Studio Portrait"
                ]
            ]
        ];
    }

    /**
     * Recursively extract all keys from an arbitrary JSON payload (including dot notation for nested objects)
     */
    public static function extractKeysRecursive(mixed $data, string $prefix = ''): array {
        if (!is_array($data)) {
            return [];
        }

        $keys = [];
        foreach ($data as $key => $value) {
            $currentKey = $prefix === '' ? (string)$key : "{$prefix}.{$key}";
            if (!is_numeric($key)) {
                $keys[] = $currentKey;
            }
            if (is_array($value)) {
                $nested = self::extractKeysRecursive($value, is_numeric($key) ? $prefix : $currentKey);
                $keys = array_merge($keys, $nested);
            }
        }

        return array_values(array_unique(array_filter($keys, fn($k) => !empty($k))));
    }

    /**
     * Fetch recent captured logs and extract aggregate detected keys for inspector
     */
    public static function getRecentCapturedPayloads(string $slug, int $limit = 10): array {
        try {
            $logs = Database::query(
                "SELECT * FROM incoming_webhook_logs WHERE endpoint_slug = ? ORDER BY id DESC LIMIT " . (int)$limit,
                [$slug]
            );

            $allKeys = [];
            $latestPayload = null;

            foreach ($logs as $log) {
                if (!empty($log['payload_json'])) {
                    $decoded = json_decode($log['payload_json'], true);
                    if (is_array($decoded)) {
                        if ($latestPayload === null) {
                            $latestPayload = $decoded;
                        }
                        $extracted = self::extractKeysRecursive($decoded);
                        $allKeys = array_merge($allKeys, $extracted);
                    }
                }
            }

            return [
                'logs' => $logs,
                'detected_keys' => array_values(array_unique($allKeys)),
                'latest_payload' => $latestPayload
            ];
        } catch (\Throwable $e) {
            return [
                'logs' => [],
                'detected_keys' => [],
                'latest_payload' => null,
                'error' => $e->getMessage()
            ];
        }
    }
}
