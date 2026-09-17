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

        // Interpret common MySQL errors with diagnostic tips
        $diagnostic = "An unexpected database exception occurred.";
        if (str_contains($message, '1005') || str_contains($message, 'errno: 150') || str_contains($message, 'Foreign key')) {
            $diagnostic = "<strong>Foreign Key Constraint Mismatch (errno 150):</strong> Existing tables in <code>{$dbname}</code> contain legacy column types (e.g. INT vs BIGINT UNSIGNED). Use the Auto-Repair button below to rebuild the normalized schema.";
        } elseif (str_contains($message, 'Connection refused') || str_contains($message, '2002')) {
            $diagnostic = "<strong>MySQL Server Offline:</strong> Unable to connect to MySQL on <code>{$host}:{$port}</code>. Please make sure MySQL is running in your XAMPP Control Panel.";
        } elseif (str_contains($message, 'Access denied') || str_contains($message, '1045')) {
            $diagnostic = "<strong>Authentication Failure:</strong> The credentials for user <code>{$user}</code> were rejected. Check your <code>app/Core/Config.php</code> credentials.";
        } elseif (str_contains($message, 'Table') && str_contains($message, "doesn't exist")) {
            $diagnostic = "<strong>Missing Table:</strong> A required database table is missing from <code>{$dbname}</code>. Click Auto-Repair to initialize the schema.";
        }

        // Auto-log to ErrorLogger
        if (class_exists('\\App\\Core\\ErrorLogger')) {
            \App\Core\ErrorLogger::log('DATABASE_ERROR', $message . ($sql ? " | SQL: " . substr($sql, 0, 200) : ""), $file, $line, $trace);
        }

        // Check if JSON response is expected
        $isJson = (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'json'))
            || str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api')
            || str_contains($_SERVER['REQUEST_URI'] ?? '', '/detail/')
            || str_contains($_SERVER['REQUEST_URI'] ?? '', '/json')
            || !empty($_SERVER['HTTP_X_REQUESTED_WITH']);

        if ($isJson) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'error' => $message,
                'diagnostic' => strip_tags($diagnostic),
                'sql' => $sql
            ]);
            exit;
        }

        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>Database Diagnostic & Error HUD | TechScout</title>
          <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=JetBrains+Mono:wght@400;600&family=Outfit:wght@700;800&display=swap" rel="stylesheet">
          <style>
            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
            body { background: #05070b; color: #94a3b8; font-family: 'Inter', sans-serif; padding: 2.5rem 1rem; line-height: 1.6; }
            .diag-container { max-width: 960px; margin: 0 auto; background: #090d16; border: 1px solid rgba(239, 68, 68, 0.4); border-radius: 16px; padding: 2.5rem; box-shadow: 0 10px 40px rgba(0,0,0,0.8), 0 0 30px rgba(239,68,68,0.2); }
            .diag-header { display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(148,163,184,0.12); padding-bottom: 1.5rem; margin-bottom: 2rem; }
            .diag-title { font-family: 'Outfit', sans-serif; font-size: 1.75rem; color: #f8fafc; font-weight: 800; }
            .diag-badge { display: inline-block; padding: 0.35rem 0.8rem; background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.4); border-radius: 999px; font-weight: 700; font-size: 0.8rem; }
            .alert-box { background: rgba(239,68,68,0.08); border-left: 4px solid #ef4444; padding: 1.25rem; border-radius: 0 8px 8px 0; margin-bottom: 2rem; color: #fecaca; font-size: 0.95rem; }
            .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
            .info-card { background: #0d131f; border: 1px solid rgba(148,163,184,0.08); border-radius: 8px; padding: 1rem; }
            .info-label { font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 600; }
            .info-val { font-size: 1rem; font-weight: 700; color: #f8fafc; margin-top: 0.2rem; font-family: 'JetBrains Mono', monospace; }
            .code-block { background: #05070b; border: 1px solid rgba(148,163,184,0.1); border-radius: 8px; padding: 1.25rem; font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; color: #e2e8f0; overflow-x: auto; margin-bottom: 2rem; white-space: pre-wrap; word-break: break-all; }
            .btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem 1.5rem; font-size: 0.95rem; font-weight: 700; border-radius: 8px; text-decoration: none; cursor: pointer; border: none; transition: all 0.2s ease; }
            .btn-primary { background: linear-gradient(135deg, #10b981, #059669); color: #ffffff; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4); }
            .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(16, 185, 129, 0.6); }
            .btn-outline { background: transparent; border: 1px solid rgba(148,163,184,0.2); color: #f8fafc; }
          </style>
        </head>
        <body>
          <div class="diag-container">
            <div class="diag-header">
              <div>
                <div class="diag-title">Database Exception Encountered</div>
                <div style="font-size: 0.85rem; color: #64748b; margin-top: 0.25rem;">TechScout Database Diagnostics & Recovery Engine</div>
              </div>
              <span class="diag-badge">SQLSTATE <?= htmlspecialchars((string)$code) ?></span>
            </div>

            <div class="alert-box">
              <div style="font-weight: 700; font-size: 1.05rem; margin-bottom: 0.4rem; color: #ffffff;">Diagnostic Insight:</div>
              <?= $diagnostic ?>
            </div>

            <div class="info-grid">
              <div class="info-card">
                <div class="info-label">Database Host & Port</div>
                <div class="info-val"><?= htmlspecialchars("{$host}:{$port}") ?></div>
              </div>
              <div class="info-card">
                <div class="info-label">Target Database</div>
                <div class="info-val"><?= htmlspecialchars($dbname) ?></div>
              </div>
              <div class="info-card">
                <div class="info-label">Connected User</div>
                <div class="info-val"><?= htmlspecialchars($user) ?></div>
              </div>
              <div class="info-card">
                <div class="info-label">Source Location</div>
                <div class="info-val" style="font-size: 0.85rem;"><?= basename($file) ?>:<?= $line ?></div>
              </div>
            </div>

            <?php if ($sql): ?>
              <div style="font-weight: 600; color: #f8fafc; margin-bottom: 0.5rem; font-size: 0.9rem;">Executed SQL Statement:</div>
              <div class="code-block" style="color: #38bdf8;"><?= htmlspecialchars($sql) ?></div>
            <?php endif; ?>

            <div style="font-weight: 600; color: #f8fafc; margin-bottom: 0.5rem; font-size: 0.9rem;">Raw Database Error Message:</div>
            <div class="code-block" style="color: #f87171;"><?= htmlspecialchars($message) ?></div>

            <div style="font-weight: 600; color: #f8fafc; margin-bottom: 0.5rem; font-size: 0.9rem;">Execution Stack Trace:</div>
            <div class="code-block" style="max-height: 220px; font-size: 0.75rem; color: #94a3b8;"><?= htmlspecialchars($trace) ?></div>

            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1.5rem; border-top: 1px solid rgba(148,163,184,0.12); flex-wrap: wrap; gap: 1rem;">
              <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <a href="<?= Config::url('/install?recovery=1') ?>" class="btn btn-primary" style="background: linear-gradient(135deg, #0284c7, #0369a1); box-shadow: 0 4px 15px rgba(2, 132, 199, 0.4);">
                  🛠️ Configure Database / Run Setup Wizard
                </a>
                <a href="?db_action=repair" class="btn btn-primary">
                  ⚡ Auto-Repair & Reinitialize Schema
                </a>
              </div>
              <button onclick="location.reload()" class="btn btn-outline">
                🔄 Retry Query
              </button>
            </div>
          </div>
        </body>
        </html>
        <?php
        exit;
    }
}
