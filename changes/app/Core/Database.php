<?php
namespace App\Core;

use PDO;
use PDOException;
use Throwable;

class Database {
    private static ?PDO $instance = null;
    private static bool $initialized = false;
    public static bool $connectionFailed = false;
    public static ?string $connectionError = null;

    /**
     * Non-blocking check to verify if database is currently connectable.
     */
    public static function hasConnection(): bool {
        if (self::$instance !== null) {
            return true;
        }
        if (self::$connectionFailed) {
            return false;
        }

        $host = Config::get('db_host', '127.0.0.1');
        $port = Config::get('db_port', '3306');
        $dbname = Config::get('db_name', 'techscout_db');
        $user = Config::get('db_user', 'root');
        $pass = Config::get('db_pass', '');

        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 2
            ]);
            self::$instance = $pdo;
            self::$connectionFailed = false;
            self::$connectionError = null;
            return true;
        } catch (Throwable $e) {
            // Attempt server-level connect if DB needs creation
            try {
                $dsnServer = "mysql:host={$host};port={$port};charset=utf8mb4";
                $pdoServer = new PDO($dsnServer, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT => 2
                ]);
                $pdoServer->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT => 2
                ]);
                self::$connectionFailed = false;
                self::$connectionError = null;
                return true;
            } catch (Throwable $srvEx) {
                self::$connectionFailed = true;
                self::$connectionError = $e->getMessage();
                return false;
            }
        }
    }

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $host = Config::get('db_host', '127.0.0.1');
            $port = Config::get('db_port', '3306');
            $dbname = Config::get('db_name', 'techscout_db');
            $user = Config::get('db_user', 'root');
            $pass = Config::get('db_pass', '');

            // Handle manual repair trigger if passed
            if (isset($_GET['db_action']) && $_GET['db_action'] === 'repair') {
                self::forceResetAndSeed($host, $port, $dbname, $user, $pass);
            }

            try {
                // First connect to target DB directly with short 2s timeout
                $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
                try {
                    self::$instance = new PDO($dsn, $user, $pass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::ATTR_TIMEOUT => 2
                    ]);
                } catch (Throwable $dbEx) {
                    // If target DB doesn't exist and on localhost/server with root privileges, attempt CREATE DATABASE
                    $dsnServer = "mysql:host={$host};port={$port};charset=utf8mb4";
                    $pdoServer = new PDO($dsnServer, $user, $pass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_TIMEOUT => 2
                    ]);
                    $pdoServer->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

                    self::$instance = new PDO($dsn, $user, $pass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::ATTR_TIMEOUT => 2
                    ]);
                }

                // Force session timezone to Pakistan Standard Time (UTC+5) so MySQL NOW() and CURRENT_TIMESTAMP match server/app time
                try {
                    self::$instance->exec("SET time_zone = '+05:00'");
                } catch (Throwable $tzEx) {}

                // Auto-initialize schema & seed data if tables are missing
                if (!self::$initialized) {
                    self::ensureSchemaAndSeeds();
                    self::$initialized = true;
                }
            } catch (Throwable $e) {
                self::$connectionFailed = true;
                self::$connectionError = $e->getMessage();

                $uri = $_SERVER['REQUEST_URI'] ?? '';
                if (str_contains($uri, '/install')) {
                    throw $e;
                }
                self::renderDetailedError($e, $host, $port, $dbname, $user);
            }
        }
        return self::$instance;
    }

    private static function forceResetAndSeed(string $host, string $port, string $dbname, string $user, string $pass): void {
        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            foreach ($tables as $t) {
                $pdo->exec("DROP TABLE IF EXISTS `{$t}`");
            }

            $schemaFile = __DIR__ . '/../../database/schema.sql';
            $seedsFile = __DIR__ . '/../../database/seeds.sql';

            if (file_exists($schemaFile)) {
                $pdo->exec(file_get_contents($schemaFile));
            }
            if (file_exists($seedsFile)) {
                $pdo->exec(file_get_contents($seedsFile));
            }
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

            header("Location: " . Config::url('/'));
            exit;
        } catch (Throwable $e) {
            self::renderDetailedError($e, $host, $port, $dbname, $user);
        }
    }

    public static function ensureSchemaAndSeeds(): void {
        $pdo = self::$instance;
        $check = $pdo->query("SHOW TABLES LIKE 'contents'")->fetchAll();
        if (empty($check)) {
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
            $schemaFile = __DIR__ . '/../../database/schema.sql';
            $seedsFile = __DIR__ . '/../../database/seeds.sql';

            if (file_exists($schemaFile)) {
                $schemaSql = file_get_contents($schemaFile);
                $pdo->exec($schemaSql);
            }
            if (file_exists($seedsFile)) {
                $seedsSql = file_get_contents($seedsFile);
                $pdo->exec($seedsSql);
            }
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        }
    }


    public static function query(string $sql, array $params = []): array {
        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Throwable $e) {
            self::renderDetailedError($e, Config::get('db_host'), Config::get('db_port'), Config::get('db_name'), Config::get('db_user'), $sql, $params);
            return [];
        }
    }

    public static function queryOne(string $sql, array $params = []): ?array {
        return self::fetchSilent($sql, $params);
    }

    public static function queryScalar(string $sql, array $params = []): mixed {
        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            $val = $stmt->fetchColumn();
            return ($val !== false) ? $val : null;
        } catch (Throwable $e) {
            return null;
        }
    }

    public static function querySilent(string $sql, array $params = []): array {
        try {
            $conn = self::getConnection();
            if (!$conn) return [];
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }

    public static function fetch(string $sql, array $params = []): ?array {
        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            $res = $stmt->fetch();
            return $res ? $res : null;
        } catch (Throwable $e) {
            self::renderDetailedError($e, Config::get('db_host'), Config::get('db_port'), Config::get('db_name'), Config::get('db_user'), $sql, $params);
            return null;
        }
    }

    public static function fetchSilent(string $sql, array $params = []): ?array {
        try {
            $conn = self::getConnection();
            if (!$conn) return null;
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $res = $stmt->fetch();
            return $res ? $res : null;
        } catch (Throwable $e) {
            return null;
        }
    }

    public static function execute(string $sql, array $params = []): bool {
        try {
            $stmt = self::getConnection()->prepare($sql);
            return $stmt->execute($params);
        } catch (Throwable $e) {
            self::renderDetailedError($e, Config::get('db_host'), Config::get('db_port'), Config::get('db_name'), Config::get('db_user'), $sql, $params);
            return false;
        }
    }

    /**
     * Executes SQL statement silently without triggering renderDetailedError or halting script on failure.
     * Ideal for telemetry, logging, and non-critical writes.
     */
    public static function executeSilent(string $sql, array $params = []): bool {
        try {
            $conn = self::getConnection();
            if (!$conn) return false;
            $stmt = $conn->prepare($sql);
            return $stmt->execute($params);
        } catch (Throwable $e) {
            $msg = $e->getMessage();
            // Suppress benign duplicate column/index notifications
            if (str_contains($msg, '1060') || str_contains($msg, '42S21') || str_contains($msg, '1061') || str_contains($msg, 'Duplicate column')) {
                return false;
            }
            if (class_exists('\\App\\Core\\ErrorLogger')) {
                \App\Core\ErrorLogger::log('DATABASE_SILENT_ERROR', $msg . ($sql ? " | SQL: " . substr($sql, 0, 200) : ""));
            }
            error_log("Database::executeSilent: " . $msg);
            return false;
        }
    }

    public static function lastInsertId(): string {
        return self::getConnection()->lastInsertId();
    }

    private static function renderDetailedError(Throwable $e, string $host, string $port, string $dbname, string $user, ?string $sql = null, ?array $params = null): void {
        http_response_code(500);
        $message = $e->getMessage();
        $code = $e->getCode();
        $trace = $e->getTraceAsString();
        $file = $e->getFile();
        $line = $e->getLine();

        $trace = $e->getTraceAsString();
        $incidentId = strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 8));

        // Always log complete forensics to secure backend error logs
        if (class_exists('\\App\\Core\\ErrorLogger')) {
            \App\Core\ErrorLogger::log('DATABASE_ERROR', "[{$incidentId}] {$message}" . ($sql ? " | SQL: " . substr($sql, 0, 300) : ""), $file, $line, $trace);
        } else {
            error_log("[{$incidentId}] Database Error [{$code}]: {$message} in {$file}:{$line} | SQL: {$sql}");
        }

        // Check if JSON response is expected
        $isJson = (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'json'))
            || str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api')
            || str_contains($_SERVER['REQUEST_URI'] ?? '', '/detail/')
            || str_contains($_SERVER['REQUEST_URI'] ?? '', '/json')
            || !empty($_SERVER['HTTP_X_REQUESTED_WITH']);

        if ($isJson) {
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'error' => 'An unexpected database error occurred. Administration has been notified and is working to resolve the issue.',
                'incident_id' => $incidentId
            ]);
            exit;
        }

        http_response_code(500);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>Service Notice | TechScout</title>
          <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
          <style>
            :root {
              --bg: #070b14;
              --card: #0d1424;
              --border: rgba(99, 102, 241, 0.2);
              --text: #f8fafc;
              --muted: #94a3b8;
              --accent: #6366f1;
              --accent-cyan: #38bdf8;
            }
            [data-theme="light"], .theme-light {
              --bg: #f8fafc;
              --card: #ffffff;
              --border: #e2e8f0;
              --text: #0f172a;
              --muted: #64748b;
              --accent: #4f46e5;
              --accent-cyan: #0284c7;
            }
            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
            body {
              background: var(--bg);
              color: var(--muted);
              font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
              display: flex;
              align-items: center;
              justify-content: center;
              min-height: 100vh;
              padding: 1.5rem;
              line-height: 1.6;
            }
            .error-card {
              max-width: 580px;
              width: 100%;
              background: var(--card);
              border: 1px solid var(--border);
              border-radius: 20px;
              padding: 2.5rem;
              box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
              text-align: center;
              position: relative;
              overflow: hidden;
            }
            .error-card::before {
              content: '';
              position: absolute;
              top: 0;
              left: 0;
              right: 0;
              height: 4px;
              background: linear-gradient(90deg, var(--accent), var(--accent-cyan));
            }
            .icon-badge {
              width: 64px;
              height: 64px;
              border-radius: 16px;
              background: rgba(99, 102, 241, 0.12);
              color: var(--accent-cyan);
              display: inline-flex;
              align-items: center;
              justify-content: center;
              margin-bottom: 1.5rem;
              border: 1px solid rgba(99, 102, 241, 0.25);
            }
            .icon-badge svg {
              width: 32px;
              height: 32px;
            }
            h1 {
              font-family: 'Outfit', sans-serif;
              font-size: 1.65rem;
              color: var(--text);
              font-weight: 800;
              margin-bottom: 0.75rem;
            }
            p {
              font-size: 0.95rem;
              color: var(--muted);
              margin-bottom: 1.5rem;
            }
            .incident-box {
              background: rgba(99, 102, 241, 0.06);
              border: 1px solid var(--border);
              border-radius: 10px;
              padding: 0.85rem 1rem;
              margin-bottom: 2rem;
              font-size: 0.82rem;
              color: var(--muted);
              display: flex;
              align-items: center;
              justify-content: space-between;
              flex-wrap: wrap;
              gap: 0.5rem;
            }
            .incident-code {
              font-family: 'JetBrains Mono', monospace;
              font-weight: 700;
              color: var(--accent-cyan);
            }
            .action-buttons {
              display: flex;
              gap: 0.75rem;
              justify-content: center;
              flex-wrap: wrap;
            }
            .btn {
              display: inline-flex;
              align-items: center;
              gap: 0.5rem;
              padding: 0.7rem 1.4rem;
              font-size: 0.9rem;
              font-weight: 700;
              border-radius: 10px;
              text-decoration: none;
              cursor: pointer;
              transition: all 0.2s ease;
              border: none;
            }
            .btn-primary {
              background: linear-gradient(135deg, #6366f1, #38bdf8);
              color: #ffffff;
              box-shadow: 0 4px 15px rgba(99, 102, 241, 0.35);
            }
            .btn-primary:hover {
              transform: translateY(-2px);
              box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5);
            }
            .btn-outline {
              background: transparent;
              border: 1px solid var(--border);
              color: var(--text);
            }
            .btn-outline:hover {
              background: rgba(99, 102, 241, 0.1);
              border-color: var(--accent);
            }
          </style>
        </head>
        <body>
          <div class="error-card">
            <div class="icon-badge">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
              </svg>
            </div>
            <h1>An Unexpected Error Occurred</h1>
            <p>
              We encountered a temporary database or system issue. Our administration team has been automatically notified and is actively working to resolve it.
            </p>
            <div class="incident-box">
              <span>Incident Reference</span>
              <span class="incident-code"><?= htmlspecialchars($incidentId) ?></span>
            </div>
            <div class="action-buttons">
              <button onclick="location.reload()" class="btn btn-primary">
                🔄 Refresh Page
              </button>
              <a href="<?= Config::url('/') ?>" class="btn btn-outline">
                🏠 Return to Homepage
              </a>
              <a href="<?= Config::url('/admin') ?>" class="btn btn-outline">
                ⚡ Admin Hub
              </a>
            </div>
          </div>
        </body>
        </html>
        <?php
        exit;
    }

    /**
     * Dynamically retrieve all tables with schema comments, row counts, and storage metrics
     */
    public static function getAllTablesWithMetadata(): array {
        try {
            $pdo = self::getConnection();
            $sql = "SELECT TABLE_NAME as table_name, TABLE_COMMENT as table_comment, 
                           COALESCE(TABLE_ROWS, 0) as table_rows, 
                           COALESCE(DATA_LENGTH + INDEX_LENGTH, 0) as total_size_bytes, 
                           ENGINE as engine, CREATE_TIME as create_time, UPDATE_TIME as update_time 
                    FROM information_schema.TABLES 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    ORDER BY TABLE_NAME ASC";
            $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($rows)) {
                return $rows;
            }
        } catch (Throwable $e) {}

        // Fallback using SHOW TABLE STATUS
        try {
            $pdo = self::getConnection();
            $statusRows = $pdo->query("SHOW TABLE STATUS")->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $result = [];
            foreach ($statusRows as $sr) {
                $name = $sr['Name'] ?? ($sr['TABLE_NAME'] ?? '');
                if (empty($name)) continue;
                $result[] = [
                    'table_name' => $name,
                    'table_comment' => $sr['Comment'] ?? '',
                    'table_rows' => (int)($sr['Rows'] ?? 0),
                    'total_size_bytes' => (int)($sr['Data_length'] ?? 0) + (int)($sr['Index_length'] ?? 0),
                    'engine' => $sr['Engine'] ?? 'InnoDB',
                    'create_time' => $sr['Create_time'] ?? null,
                    'update_time' => $sr['Update_time'] ?? null
                ];
            }
            if (!empty($result)) {
                return $result;
            }
        } catch (Throwable $e) {}

        // Final fallback: SHOW TABLES
        try {
            $pdo = self::getConnection();
            $tableNames = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN) ?: [];
            $result = [];
            foreach ($tableNames as $t) {
                $result[] = [
                    'table_name' => $t,
                    'table_comment' => "Database table {$t}",
                    'table_rows' => 0,
                    'total_size_bytes' => 0,
                    'engine' => 'InnoDB',
                    'create_time' => null,
                    'update_time' => null
                ];
            }
            return $result;
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Retrieve column metadata for any specific database table
     */
    public static function getTableColumnsDetailed(string $tableName): array {
        $cleanTable = preg_replace('/[^a-zA-Z0-9_]/', '', $tableName);
        if (empty($cleanTable)) {
            return [];
        }
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->query("SHOW FULL COLUMNS FROM `{$cleanTable}`");
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Provide comprehensive description of the entire database schema
     */
    public static function describeDatabase(): array {
        $pdo = self::getConnection();
        $dbName = (string)$pdo->query("SELECT DATABASE()")->fetchColumn();
        $version = (string)$pdo->query("SELECT VERSION()")->fetchColumn();
        $tables = self::getAllTablesWithMetadata();

        $totalRows = 0;
        $totalBytes = 0;
        $detailedTables = [];

        foreach ($tables as $t) {
            $name = $t['table_name'];
            $totalRows += (int)($t['table_rows'] ?? 0);
            $totalBytes += (int)($t['total_size_bytes'] ?? 0);
            $cols = self::getTableColumnsDetailed($name);
            $pk = [];
            $columnsSummary = [];
            foreach ($cols as $col) {
                if (($col['Key'] ?? '') === 'PRI') {
                    $pk[] = $col['Field'];
                }
                $columnsSummary[] = [
                    'name' => $col['Field'],
                    'type' => $col['Type'],
                    'nullable' => ($col['Null'] ?? '') === 'YES',
                    'default' => $col['Default'],
                    'key' => $col['Key'] ?? '',
                    'extra' => $col['Extra'] ?? '',
                    'comment' => $col['Comment'] ?? ''
                ];
            }

            $detailedTables[] = [
                'table_name' => $name,
                'comment' => $t['table_comment'] ?? '',
                'engine' => $t['engine'] ?? 'InnoDB',
                'rows' => (int)($t['table_rows'] ?? 0),
                'size_bytes' => (int)($t['total_size_bytes'] ?? 0),
                'primary_keys' => $pk,
                'columns_count' => count($cols),
                'columns' => $columnsSummary
            ];
        }

        return [
            'database_name' => $dbName,
            'server_version' => $version,
            'timestamp_gmt' => gmdate('Y-m-d\TH:i:s\Z'),
            'total_tables' => count($tables),
            'total_estimated_rows' => $totalRows,
            'total_size_bytes' => $totalBytes,
            'total_size_mb' => round($totalBytes / (1024 * 1024), 2),
            'tables' => $detailedTables
        ];
    }

    /**
     * Provide comprehensive description of a single table, its columns, indexes, and sample rows
     */
    public static function describeTable(string $tableName): array {
        $cleanTable = preg_replace('/[^a-zA-Z0-9_]/', '', $tableName);
        if (empty($cleanTable)) {
            return ['error' => 'Invalid table name'];
        }
        $pdo = self::getConnection();
        $cols = self::getTableColumnsDetailed($cleanTable);
        if (empty($cols)) {
            return ['error' => "Table '{$cleanTable}' does not exist or has no columns"];
        }

        $indexes = [];
        try {
            $idxStmt = $pdo->query("SHOW INDEX FROM `{$cleanTable}`");
            $indexes = $idxStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {}

        $rowCount = 0;
        try {
            $rowCount = (int)$pdo->query("SELECT COUNT(*) FROM `{$cleanTable}`")->fetchColumn();
        } catch (Throwable $e) {}

        $sampleRows = [];
        try {
            $sampleRows = $pdo->query("SELECT * FROM `{$cleanTable}` ORDER BY 1 DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {}

        return [
            'table_name' => $cleanTable,
            'timestamp_gmt' => gmdate('Y-m-d\TH:i:s\Z'),
            'total_records' => $rowCount,
            'total_columns' => count($cols),
            'columns' => array_map(function($c) {
                return [
                    'name' => $c['Field'],
                    'type' => $c['Type'],
                    'collation' => $c['Collation'] ?? null,
                    'nullable' => ($c['Null'] ?? '') === 'YES',
                    'key' => $c['Key'] ?? '',
                    'default' => $c['Default'],
                    'extra' => $c['Extra'] ?? '',
                    'comment' => $c['Comment'] ?? ''
                ];
            }, $cols),
            'indexes' => array_map(function($idx) {
                return [
                    'key_name' => $idx['Key_name'] ?? '',
                    'column_name' => $idx['Column_name'] ?? '',
                    'non_unique' => (int)($idx['Non_unique'] ?? 0),
                    'index_type' => $idx['Index_type'] ?? ''
                ];
            }, $indexes),
            'sample_data' => $sampleRows
        ];
    }
}

