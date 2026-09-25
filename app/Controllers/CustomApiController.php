<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;
use App\Core\Config;

class CustomApiController {

    /**
     * Whitelist of permitted database tables for schema introspection and data querying
     */
    public static array $allowedTables = [
        'prompts' => 'AI prompt blueprints, generation parameters, models, and variables',
        'contents' => 'Unified platform entities (articles, tools, prompts, gpts, news)',
        'ai_tools' => 'Catalog of AI tools, platforms, pricing tiers, and rating scores',
        'articles' => 'Editorial articles, deep dives, tutorials, and benchmark studies',
        'categories' => 'Topic and entity classification taxonomy',
        'custom_popups' => 'Active promotional banners, modal announcements, and CTAs',
        'advertisements' => 'Display advertising inventory, slots, and sponsored partner links',
        'custom_api_endpoints' => 'Dynamic custom REST API endpoints and data views',
        'incoming_webhooks' => 'Configured incoming webhook ingestion pipelines',
        'incoming_webhook_logs' => 'Audit history of incoming webhook payload executions',
        'newsletter_subscribers' => 'Newsletter audience and subscription preferences'
    ];

    /**
     * Public Dispatcher: GET /api/v1/custom/{endpoint_slug}
     */
    public function handle(Request $request, array $params): void {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }

        $slug = trim((string)($params['endpoint_slug'] ?? ''));
        if (empty($slug)) {
            Response::json(['success' => false, 'error' => 'Endpoint slug is required'], 400);
            return;
        }

        // Fetch endpoint definition
        $endpoint = null;
        try {
            $endpoint = Database::fetch("SELECT * FROM custom_api_endpoints WHERE slug = ? AND is_active = 1", [$slug]);
        } catch (\Throwable $e) {}

        if (!$endpoint) {
            Response::json(['success' => false, 'error' => "Custom API endpoint '{$slug}' not found or inactive"], 404);
            return;
        }

        // Authentication check if enabled
        if (!empty($endpoint['auth_required'])) {
            $authHeader = $request->header('Authorization') ?? ($request->header('authorization') ?? '');
            $providedToken = '';
            if (preg_match('/Bearer\s+(\S+)/i', $authHeader, $matches)) {
                $providedToken = $matches[1];
            } else {
                $providedToken = (string)$request->query('token', '');
            }

            $secretToken = $endpoint['secret_token'] ?? '';
            $isAuthed = false;
            if (!empty($secretToken) && hash_equals($secretToken, $providedToken)) {
                $isAuthed = true;
            } elseif (!empty($providedToken)) {
                // Also check api_keys table
                $hash = hash('sha256', $providedToken);
                $keyRow = Database::fetch("SELECT id FROM api_keys WHERE api_key_hash = ? AND is_active = 1", [$hash]);
                if ($keyRow) {
                    $isAuthed = true;
                }
            }

            if (!$isAuthed) {
                Response::json(['success' => false, 'error' => 'Unauthorized: Invalid or missing API authentication token'], 401);
                return;
            }
        }

        // Increment stats
        try {
            Database::execute("UPDATE custom_api_endpoints SET request_count = request_count + 1, last_accessed_at = NOW() WHERE id = ?", [(int)$endpoint['id']]);
        } catch (\Throwable $e) {}

        $type = $endpoint['endpoint_type'] ?? 'table_data';

        switch ($type) {
            case 'table_list':
                $this->serveTableList($endpoint);
                break;

            case 'table_schema':
                $this->serveTableSchema($request, $endpoint);
                break;

            case 'custom_json':
                $this->serveCustomJson($endpoint);
                break;

            case 'table_data':
            default:
                $this->serveTableData($request, $endpoint);
                break;
        }
    }

    /**
     * Outputs catalog list of permitted database tables
     */
    private function serveTableList(array $endpoint): void {
        try {
            $pdo = Database::getConnection();
            $tablesData = [];

            foreach (self::$allowedTables as $tableName => $desc) {
                $count = 0;
                try {
                    $count = (int)$pdo->query("SELECT COUNT(*) FROM `{$tableName}`")->fetchColumn();
                } catch (\Throwable $e) {}

                $tablesData[] = [
                    'table_name' => $tableName,
                    'description' => $desc,
                    'total_records' => $count,
                    'schema_endpoint' => Config::url('/api/v1/custom/prompts-schema?table=' . $tableName),
                    'inspect_url' => Config::url('/api/v1/custom/' . $endpoint['slug'] . '?table=' . $tableName)
                ];
            }

            Response::json([
                'success' => true,
                'endpoint' => $endpoint['name'],
                'endpoint_slug' => $endpoint['slug'],
                'type' => 'table_list',
                'timestamp_gmt' => gmdate('Y-m-d\TH:i:s\Z'),
                'total_tables' => count($tablesData),
                'tables' => $tablesData
            ]);
        } catch (\Throwable $e) {
            Response::json(['success' => false, 'error' => 'Failed to retrieve tables: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Outputs structure / schema columns for a given table
     */
    private function serveTableSchema(Request $request, array $endpoint): void {
        $table = trim((string)$request->query('table', ''));
        if (empty($table)) {
            $table = $endpoint['target_table'] ?? 'prompts';
        }

        if (!array_key_exists($table, self::$allowedTables)) {
            Response::json([
                'success' => false, 
                'error' => "Table '{$table}' is not permitted for schema inspection. Allowed tables: " . implode(', ', array_keys(self::$allowedTables))
            ], 403);
            return;
        }

        try {
            $pdo = Database::getConnection();
            $columnsStmt = $pdo->query("SHOW FULL COLUMNS FROM `{$table}`");
            $rawCols = $columnsStmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

            $schema = [];
            foreach ($rawCols as $c) {
                $schema[] = [
                    'column_name' => $c['Field'],
                    'data_type' => $c['Type'],
                    'nullable' => $c['Null'] === 'YES',
                    'key_type' => $c['Key'],
                    'default_value' => $c['Default'],
                    'comment' => $c['Comment'] ?? ''
                ];
            }

            $rowCount = (int)$pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();

            Response::json([
                'success' => true,
                'endpoint' => $endpoint['name'],
                'endpoint_slug' => $endpoint['slug'],
                'type' => 'table_schema',
                'table_name' => $table,
                'table_description' => self::$allowedTables[$table] ?? '',
                'total_records' => $rowCount,
                'total_columns' => count($schema),
                'timestamp_gmt' => gmdate('Y-m-d\TH:i:s\Z'),
                'columns' => $schema
            ]);
        } catch (\Throwable $e) {
            Response::json(['success' => false, 'error' => 'Schema inspection error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Outputs complete or filtered data from a single database table
     */
    private function serveTableData(Request $request, array $endpoint): void {
        $table = $endpoint['target_table'] ?? 'prompts';
        if (!array_key_exists($table, self::$allowedTables)) {
            Response::json(['success' => false, 'error' => "Table '{$table}' is not accessible via custom API"], 403);
            return;
        }

        try {
            $pdo = Database::getConnection();

            // 1. Determine columns to select
            $validCols = $pdo->query("SHOW COLUMNS FROM `{$table}`")->fetchAll(\PDO::FETCH_COLUMN) ?: [];
            
            $requestedFields = trim((string)$request->query('fields', ''));
            $selectCols = [];

            if (!empty($requestedFields)) {
                $reqArr = array_map('trim', explode(',', $requestedFields));
                foreach ($reqArr as $f) {
                    if (in_array($f, $validCols, true)) {
                        $selectCols[] = "`{$f}`";
                    }
                }
            } elseif (!empty($endpoint['selected_columns']) && $endpoint['selected_columns'] !== '*') {
                $selArr = array_map('trim', explode(',', $endpoint['selected_columns']));
                foreach ($selArr as $f) {
                    if (in_array($f, $validCols, true)) {
                        $selectCols[] = "`{$f}`";
                    }
                }
            }

            if (empty($selectCols)) {
                $selectCols = ['*'];
            }
            $selectClause = implode(', ', $selectCols);

            // 2. WHERE Conditions
            $whereParts = [];
            $bindings = [];

            // Static rules configured on endpoint
            $staticRules = json_decode($endpoint['where_conditions_json'] ?? '[]', true) ?: [];
            foreach ($staticRules as $rule) {
                $col = $rule['column'] ?? '';
                $val = $rule['value'] ?? '';
                $op = $rule['operator'] ?? '=';
                if (in_array($col, $validCols, true) && in_array($op, ['=', '!=', '>', '<', 'LIKE'], true)) {
                    $whereParts[] = "`{$col}` {$op} ?";
                    $bindings[] = $val;
                }
            }

            // Dynamic Search query (?q=...)
            $searchQuery = trim((string)$request->query('q', ''));
            if (!empty($searchQuery)) {
                $searchable = ['title', 'slug', 'name', 'summary', 'prompt_template', 'email'];
                $sParts = [];
                foreach ($searchable as $sc) {
                    if (in_array($sc, $validCols, true)) {
                        $sParts[] = "`{$sc}` LIKE ?";
                        $bindings[] = '%' . $searchQuery . '%';
                    }
                }
                if (!empty($sParts)) {
                    $whereParts[] = '(' . implode(' OR ', $sParts) . ')';
                }
            }

            // Filter by single exact match parameters (e.g. ?prompt_type=image)
            foreach ($_GET as $paramKey => $paramVal) {
                if (in_array($paramKey, ['fields', 'q', 'sort', 'dir', 'page', 'limit', 'token'], true)) {
                    continue;
                }
                if (in_array($paramKey, $validCols, true)) {
                    $whereParts[] = "`{$paramKey}` = ?";
                    $bindings[] = trim((string)$paramVal);
                }
            }

            $whereSql = !empty($whereParts) ? ' WHERE ' . implode(' AND ', $whereParts) : '';

            // 3. Count Total Matching Records
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM `{$table}` {$whereSql}");
            $countStmt->execute($bindings);
            $totalCount = (int)$countStmt->fetchColumn();

            // 4. Ordering & Pagination
            $sortCol = trim((string)$request->query('sort', $endpoint['order_by_column'] ?? 'id'));
            if (!in_array($sortCol, $validCols, true)) {
                $sortCol = in_array('id', $validCols, true) ? 'id' : ($validCols[0] ?? 'id');
            }

            $sortDir = strtoupper(trim((string)$request->query('dir', $endpoint['order_direction'] ?? 'DESC')));
            if ($sortDir !== 'ASC' && $sortDir !== 'DESC') {
                $sortDir = 'DESC';
            }

            $maxLimit = max(1, min(100, (int)($endpoint['result_limit'] ?? 50)));
            $limit = max(1, min($maxLimit, (int)$request->query('limit', 25)));
            $page = max(1, (int)$request->query('page', 1));
            $offset = ($page - 1) * $limit;

            $sql = "SELECT {$selectClause} FROM `{$table}` {$whereSql} ORDER BY `{$sortCol}` {$sortDir} LIMIT {$limit} OFFSET {$offset}";
            $dataStmt = $pdo->prepare($sql);
            $dataStmt->execute($bindings);
            $rows = $dataStmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

            // JSON parse nested strings if any
            foreach ($rows as &$r) {
                foreach ($r as $k => $v) {
                    if (is_string($v) && (str_starts_with($v, '{') || str_starts_with($v, '['))) {
                        $decoded = json_decode($v, true);
                        if ($decoded !== null) {
                            $r[$k] = $decoded;
                        }
                    }
                }
            }
            unset($r);

            Response::json([
                'success' => true,
                'endpoint' => $endpoint['name'],
                'endpoint_slug' => $endpoint['slug'],
                'type' => 'table_data',
                'target_table' => $table,
                'timestamp_gmt' => gmdate('Y-m-d\TH:i:s\Z'),
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total_records' => $totalCount,
                    'total_pages' => (int)ceil($totalCount / $limit)
                ],
                'data' => $rows
            ]);
        } catch (\Throwable $e) {
            Response::json(['success' => false, 'error' => 'Query execution error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Outputs custom formatted JSON response template
     */
    private function serveCustomJson(array $endpoint): void {
        $rawTemplate = $endpoint['custom_response_template'] ?? '{}';
        $cleanJson = str_replace(
            ['{{timestamp}}', '{{timestamp_gmt}}', '{{app_url}}'],
            [time(), gmdate('Y-m-d\TH:i:s\Z'), Config::url('/')],
            $rawTemplate
        );

        $parsed = json_decode($cleanJson, true);
        if ($parsed === null) {
            $parsed = ['raw_output' => $cleanJson];
        }

        Response::json([
            'success' => true,
            'endpoint' => $endpoint['name'],
            'endpoint_slug' => $endpoint['slug'],
            'type' => 'custom_json',
            'timestamp_gmt' => gmdate('Y-m-d\TH:i:s\Z'),
            'payload' => $parsed
        ]);
    }
}
