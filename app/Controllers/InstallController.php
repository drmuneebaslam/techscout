<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;
use App\Core\Config;
use App\Core\Auth;
use PDO;
use Throwable;

class InstallController {

    private static function getLockFilePath(): string {
        return dirname(__DIR__, 2) . '/storage/installed.lock';
    }

    public static function isInstalled(): bool {
        $lockFile = self::getLockFilePath();
        if (!file_exists($lockFile)) {
            return false;
        }
        if (!Database::hasConnection()) {
            return false;
        }
        try {
            $pdo = Database::getConnection();
            $check = $pdo->query("SHOW TABLES LIKE 'contents'")->fetchAll();
            return !empty($check);
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * Web Installer Wizard Page
     */
    public function index(Request $request): void {
        $isInstalled = self::isInstalled();
        $isAuthorized = !empty($_SESSION['reinstall_authorized']);
        $dbConnected = Database::hasConnection();
        $isRecovery = !$dbConnected || !empty($_GET['recovery']);

        // If platform is already installed, database is reachable, and re-install is not authorized
        if ($isInstalled && !$isAuthorized && $dbConnected && !$isRecovery) {
            Response::view('install/locked', [
                'title' => 'Platform Already Installed & Protected | TechScout',
                'lockData' => self::getLockData(),
                'dbConnected' => true
            ]);
            return;
        }

        // Requirements Check
        $requirements = self::checkRequirements();
        $allPassed = !in_array(false, array_column($requirements, 'passed'), true);

        // Current Config defaults (Smart Local Detection for downloaded backups)
        $isLocal = Config::isLocalEnvironment();
        $rawDbHost = Config::$settings['db_host'] ?? '127.0.0.1';
        $isRemoteHost = str_contains($rawDbHost, 'infinityfree.com') 
            || str_contains($rawDbHost, 'epizy.com') 
            || str_contains($rawDbHost, 'byethost');

        $currentConfig = [
            'db_host' => ($isLocal && $isRemoteHost) ? '127.0.0.1' : Config::get('db_host', '127.0.0.1'),
            'db_port' => Config::get('db_port', '3306'),
            'db_name' => ($isLocal && $isRemoteHost) ? 'techscout_db' : Config::get('db_name', 'techscout_db'),
            'db_user' => ($isLocal && $isRemoteHost) ? 'root' : Config::get('db_user', 'root'),
            'db_pass' => ($isLocal && $isRemoteHost) ? '' : Config::get('db_pass', ''),
            'app_url' => Config::get('app_url', Config::detectBaseUrl()),
        ];

        // Fetch existing white-label branding if available in site_settings
        $existingBranding = [
            'site_name' => Config::get('app_name', 'TechScout'),
            'site_tagline' => Config::get('app_tagline', 'Enterprise AI Directory & Engineering Ecosystem'),
            'primary_color' => Config::get('primary_color', '#38bdf8'),
            'support_email' => Config::get('support_email', 'support@techscout.ai'),
            'site_copyright' => Config::get('site_copyright', '© ' . date('Y') . ' TechScout Inc. All rights reserved.'),
            'default_theme' => Config::get('default_theme', 'dark')
        ];

        if ($dbConnected) {
            try {
                $settingsRows = Database::query("SELECT key_name, key_value FROM site_settings WHERE key_name IN ('site_name', 'site_tagline', 'primary_color', 'support_email', 'site_copyright', 'default_theme')");
                foreach ($settingsRows as $sr) {
                    if (!empty($sr['key_value'])) {
                        $existingBranding[$sr['key_name']] = $sr['key_value'];
                    }
                }
            } catch (Throwable $e) {}
        }

        Response::view('install/index', [
            'title' => 'Comprehensive Web Setup Wizard & White-Label Installer',
            'requirements' => $requirements,
            'allPassed' => $allPassed,
            'currentConfig' => $currentConfig,
            'branding' => $existingBranding,
            'isReinstall' => $isInstalled && $isAuthorized,
            'isRecovery' => $isRecovery,
            'dbConnected' => $dbConnected
        ]);
    }

    /**
     * AJAX Database Connection Verification
     */
    public function testDb(Request $request): void {
        $host = trim((string)$request->post('db_host', '127.0.0.1'));
        $port = trim((string)$request->post('db_port', '3306'));
        $dbname = trim((string)$request->post('db_name', 'techscout_db'));
        $user = trim((string)$request->post('db_user', 'root'));
        $pass = (string)$request->post('db_pass', '');

        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 3
            ]);

            $version = $pdo->query("SELECT VERSION() as v")->fetchColumn() ?: 'Unknown';
            Response::json([
                'success' => true,
                'message' => "Successfully connected to MySQL server! Engine version: {$version}"
            ]);
        } catch (Throwable $e) {
            // Try connecting to MySQL server directly if DB doesn't exist
            try {
                $dsnServer = "mysql:host={$host};port={$port};charset=utf8mb4";
                $pdoServer = new PDO($dsnServer, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT => 3
                ]);
                Response::json([
                    'success' => true,
                    'message' => "Connected to MySQL server! Database `{$dbname}` will be automatically created upon setup."
                ]);
            } catch (Throwable $srvEx) {
                Response::json([
                    'success' => false,
                    'message' => "Connection failed: " . $e->getMessage()
                ], 400);
            }
        }
    }

    /**
     * Submits and executes the complete installation
     */
    public function process(Request $request): void {
        $isInstalled = self::isInstalled();
        $isAuthorized = !empty($_SESSION['reinstall_authorized']) || !empty($request->post('force_reinstall'));

        if ($isInstalled && !$isAuthorized) {
            Response::json(['success' => false, 'message' => 'Setup wizard is locked. Authorization required.'], 403);
            return;
        }

        // 1. Database Credentials
        $host = trim((string)$request->post('db_host', '127.0.0.1'));
        $port = trim((string)$request->post('db_port', '3306'));
        $dbname = trim((string)$request->post('db_name', 'techscout_db'));
        $user = trim((string)$request->post('db_user', 'root'));
        $pass = (string)$request->post('db_pass', '');

        // 2. White-Label Branding
        $siteName = trim((string)$request->post('site_name', 'TechScout'));
        $siteTagline = trim((string)$request->post('site_tagline', 'Enterprise AI Directory & Engineering Ecosystem'));
        $primaryColor = trim((string)$request->post('primary_color', '#38bdf8'));
        $supportEmail = trim((string)$request->post('support_email', 'support@techscout.ai'));
        $siteCopyright = trim((string)$request->post('site_copyright', '© ' . date('Y') . ' ' . $siteName . '. All rights reserved.'));
        $defaultTheme = trim((string)$request->post('default_theme', 'dark'));

        // 3. SuperAdmin Account
        $adminUsername = trim((string)$request->post('admin_username', 'admin'));
        $adminEmail = trim((string)$request->post('admin_email', 'admin@techscout.ai'));
        $adminPassword = (string)$request->post('admin_password', '');

        if (empty($adminUsername) || empty($adminEmail)) {
            Response::json(['success' => false, 'message' => 'Please provide an administrator username and email address.'], 400);
            return;
        }

        if (empty($adminPassword) && !$isInstalled) {
            Response::json(['success' => false, 'message' => 'Please specify a secure administrator password.'], 400);
            return;
        }

        try {
            // Guaranteed Database Auto-Creation: Always attempt server-level CREATE DATABASE IF NOT EXISTS first
            try {
                $dsnServer = "mysql:host={$host};port={$port};charset=utf8mb4";
                $pdoServer = new PDO($dsnServer, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT => 3
                ]);
                $pdoServer->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            } catch (Throwable $srvEx) {}

            // Connect to target database
            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 3
            ]);

            // Update configuration in runtime memory
            Config::set('db_host', $host);
            Config::set('db_port', $port);
            Config::set('db_name', $dbname);
            Config::set('db_user', $user);
            Config::set('db_pass', $pass);
            Config::set('app_name', $siteName);
            Config::set('app_tagline', $siteTagline);
            Config::set('primary_color', $primaryColor);
            Config::set('support_email', $supportEmail);
            Config::set('site_copyright', $siteCopyright);
            Config::set('default_theme', $defaultTheme);

            // Persist updated credentials into Config.php file
            $isLocal = Config::isLocalEnvironment();
            $configUpdates = [
                'app_name' => $siteName,
                'app_tagline' => $siteTagline,
                'primary_color' => $primaryColor,
                'support_email' => $supportEmail,
                'site_copyright' => $siteCopyright,
                'default_theme' => in_array($defaultTheme, ['dark', 'light'], true) ? $defaultTheme : 'dark',
            ];
            if ($isLocal) {
                $configUpdates['local_db_host'] = $host;
                $configUpdates['local_db_port'] = $port;
                $configUpdates['local_db_name'] = $dbname;
                $configUpdates['local_db_user'] = $user;
                $configUpdates['local_db_pass'] = $pass;
                $configUpdates['local_app_url'] = Config::detectBaseUrl();
            } else {
                $configUpdates['db_host'] = $host;
                $configUpdates['db_port'] = $port;
                $configUpdates['db_name'] = $dbname;
                $configUpdates['db_user'] = $user;
                $configUpdates['db_pass'] = $pass;
                $configUpdates['app_url'] = Config::detectBaseUrl();
            }
            Config::saveConfig($configUpdates);

            // Schema & Initial Data Import (Custom SQL dump if uploaded, or automated schema & seeds)
            $customSqlFile = $_FILES['custom_sql_file'] ?? null;
            $hasCustomSql = !empty($customSqlFile['tmp_name']) && is_uploaded_file($customSqlFile['tmp_name']);

            if ($hasCustomSql) {
                $sqlContent = file_get_contents($customSqlFile['tmp_name']);
                self::executeSqlDump($pdo, $sqlContent);
            } else {
                // Execute automated schema & seed installation
                Database::ensureSchemaAndSeeds();
            }

            // Save White-Label site settings
            $brandingSettings = [
                'site_name' => $siteName,
                'site_tagline' => $siteTagline,
                'primary_color' => $primaryColor,
                'support_email' => $supportEmail,
                'site_copyright' => $siteCopyright,
                'default_theme' => in_array($defaultTheme, ['dark', 'light'], true) ? $defaultTheme : 'dark',
                'platform_white_labeled' => '1',
                'installed_at' => date('Y-m-d H:i:s')
            ];

            foreach ($brandingSettings as $k => $v) {
                Database::execute(
                    "INSERT INTO site_settings (key_name, key_value, updated_at) VALUES (?, ?, NOW()) 
                     ON DUPLICATE KEY UPDATE key_value = VALUES(key_value), updated_at = NOW()",
                    [$k, $v]
                );
            }

            // Create or update Super Administrator
            if (!empty($adminPassword)) {
                $hashedPass = password_hash($adminPassword, PASSWORD_BCRYPT);
                $existingUser = Database::fetch("SELECT id FROM users WHERE email = ? OR username = ?", [$adminEmail, $adminUsername]);

                if ($existingUser) {
                    Database::execute(
                        "UPDATE users SET username = ?, email = ?, password = ?, role = 'super_admin', status = 'active', email_verified_at = NOW(), updated_at = NOW() WHERE id = ?",
                        [$adminUsername, $adminEmail, $hashedPass, $existingUser['id']]
                    );
                } else {
                    Database::execute(
                        "INSERT INTO users (username, full_name, email, password, role, status, email_verified_at, created_at, updated_at) 
                         VALUES (?, ?, ?, ?, 'super_admin', 'active', NOW(), NOW(), NOW())",
                        [$adminUsername, $siteName . ' Administrator', $adminEmail, $hashedPass]
                    );
                }
            }

            // Write Lock File
            $lockData = [
                'platform' => $siteName,
                'version' => '2.5.0',
                'installed_at' => date('Y-m-d H:i:s'),
                'installed_by' => $adminEmail,
                'environment' => in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true) ? 'development' : 'production',
                'white_label_enabled' => true
            ];
            file_put_contents(self::getLockFilePath(), json_encode($lockData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            // Clear temporary reinstallation auth flag
            unset($_SESSION['reinstall_authorized']);

            Response::json([
                'success' => true,
                'message' => "Installation completed successfully! You can now log into your administrator control panel.",
                'redirect' => '/auth/login'
            ]);
        } catch (Throwable $e) {
            Response::json([
                'success' => false,
                'message' => "Installation error: " . $e->getMessage()
            ], 500);
        }
    }

    private static function checkRequirements(): array {
        $rootDir = dirname(__DIR__, 2);
        return [
            [
                'name' => 'PHP Version >= 8.1',
                'current' => PHP_VERSION,
                'passed' => version_compare(PHP_VERSION, '8.1.0', '>='),
                'required' => '>= 8.1.0'
            ],
            [
                'name' => 'PDO MySQL Extension',
                'current' => extension_loaded('pdo_mysql') ? 'Enabled' : 'Disabled',
                'passed' => extension_loaded('pdo_mysql'),
                'required' => 'Required'
            ],
            [
                'name' => 'OpenSSL Extension',
                'current' => extension_loaded('openssl') ? 'Enabled' : 'Disabled',
                'passed' => extension_loaded('openssl'),
                'required' => 'Required'
            ],
            [
                'name' => 'cURL Extension',
                'current' => extension_loaded('curl') ? 'Enabled' : 'Disabled',
                'passed' => extension_loaded('curl'),
                'required' => 'Required'
            ],
            [
                'name' => 'GD Image Library',
                'current' => extension_loaded('gd') ? 'Enabled' : 'Disabled',
                'passed' => extension_loaded('gd'),
                'required' => 'Required'
            ],
            [
                'name' => 'Multibyte String (mbstring)',
                'current' => extension_loaded('mbstring') ? 'Enabled' : 'Disabled',
                'passed' => extension_loaded('mbstring'),
                'required' => 'Required'
            ],
            [
                'name' => 'ZipArchive Extension',
                'current' => class_exists('ZipArchive') ? 'Enabled' : 'Disabled',
                'passed' => class_exists('ZipArchive'),
                'required' => 'Required'
            ],
            [
                'name' => 'Storage Directory Writable (`storage/`)',
                'current' => is_writable($rootDir . '/storage') ? 'Writable' : 'Read-only',
                'passed' => is_writable($rootDir . '/storage'),
                'required' => 'Writable'
            ]
        ];
    }

    private static function getLockData(): array {
        $path = self::getLockFilePath();
        if (file_exists($path)) {
            $data = json_decode((string)file_get_contents($path), true);
            if (is_array($data)) return $data;
        }
        return [
            'platform' => 'TechScout Enterprise AI',
            'installed_at' => date('Y-m-d H:i:s'),
            'version' => '2.5.0'
        ];
    }

    /**
     * Executes uploaded custom SQL dump safely with foreign key handling
     */
    public static function executeSqlDump(PDO $pdo, string $sql): void {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        try {
            $pdo->exec($sql);
        } catch (Throwable $e) {
            // Split into individual queries if batch execution encounters multi-statement limits
            $lines = explode("\n", $sql);
            $query = '';
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if ($trimmed === '' || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '/*') || str_starts_with($trimmed, '#')) {
                    continue;
                }
                $query .= $line . "\n";
                if (str_ends_with($trimmed, ';')) {
                    try {
                        $pdo->exec($query);
                    } catch (Throwable $stmtEx) {}
                    $query = '';
                }
            }
            if (trim($query) !== '') {
                try {
                    $pdo->exec($query);
                } catch (Throwable $finalEx) {}
            }
        }
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
