<?php
namespace App\Core;

use PDO;
use Throwable;

class ErrorLogger {
    private static bool $isRegistered = false;
    private static bool $schemaChecked = false;

    public static function register(): void {
        if (self::$isRegistered) return;
        self::$isRegistered = true;

        // Custom Error Handler
        set_error_handler([self::class, 'handleError']);

        // Custom Exception Handler
        set_exception_handler([self::class, 'handleException']);

        // Shutdown Handler for Fatal Errors
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    /**
     * Comprehensive Exception and Error Telemetry Logger
     */
    public static function log(string $type, string $message, ?string $file = null, ?int $line = null, ?string $trace = null, ?array $extra = null): void {
        try {
            $pdo = Database::getConnection();
            if (!$pdo) return;

            $normalizedType = trim($type);
            if (empty($normalizedType)) {
                $normalizedType = 'SYSTEM_ERROR';
            }

            self::ensureTableSchema($pdo);

            $uri = $_SERVER['REQUEST_URI'] ?? (PHP_SAPI === 'cli' ? 'CLI Command' : '/');
            $method = $_SERVER['REQUEST_METHOD'] ?? (PHP_SAPI === 'cli' ? 'CLI' : 'GET');
            $ip = self::getClientIp();

            $userId = null;
            if (class_exists('\\App\\Core\\Session')) {
                $userId = Session::get('user_id');
            }
            if (!$userId && !empty($_SESSION['user_id'])) {
                $userId = (int)$_SESSION['user_id'];
            }

            $requestDataJson = self::captureRequestData($extra);
            $requestHeadersJson = self::captureRequestHeaders();
            $deviceDetailsJson = self::captureDeviceDetails();
            $locationDetailsJson = self::captureLocationDetails($ip);
            $userDetailsJson = self::captureUserDetails($userId);

            try {
                $stmt = $pdo->prepare("
                    INSERT INTO `system_error_logs` (
                        `error_type`, `message`, `file`, `line`, `stack_trace`, 
                        `request_uri`, `request_method`, `request_data`, `request_headers`, 
                        `user_id`, `user_details`, `ip_address`, `location_details`, 
                        `device_details`, `created_at`
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $normalizedType,
                    $message,
                    $file,
                    $line,
                    $trace,
                    $uri,
                    $method,
                    $requestDataJson,
                    $requestHeadersJson,
                    $userId,
                    $userDetailsJson,
                    $ip,
                    $locationDetailsJson,
                    $deviceDetailsJson
                ]);
            } catch (Throwable $dbEx) {
                // If table doesn't have new columns yet, execute emergency alter and retry
                if (str_contains($dbEx->getMessage(), '1054') || str_contains($dbEx->getMessage(), 'Unknown column')) {
                    self::runEmergencyMigration($pdo);
                    try {
                        $stmt = $pdo->prepare("
                            INSERT INTO `system_error_logs` (
                                `error_type`, `message`, `file`, `line`, `stack_trace`, 
                                `request_uri`, `request_method`, `request_data`, `request_headers`, 
                                `user_id`, `user_details`, `ip_address`, `location_details`, 
                                `device_details`, `created_at`
                            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                        ");
                        $stmt->execute([
                            $normalizedType,
                            $message,
                            $file,
                            $line,
                            $trace,
                            $uri,
                            $method,
                            $requestDataJson,
                            $requestHeadersJson,
                            $userId,
                            $userDetailsJson,
                            $ip,
                            $locationDetailsJson,
                            $deviceDetailsJson
                        ]);
                        return;
                    } catch (Throwable $dbEx2) {}
                }

                // Fallback to minimal insert
                try {
                    $stmtLegacy = $pdo->prepare("
                        INSERT INTO `system_error_logs` (
                            `error_type`, `message`, `file`, `line`, `stack_trace`, 
                            `request_uri`, `request_method`, `user_id`, `ip_address`, `created_at`
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                    ");
                    $stmtLegacy->execute([$normalizedType, $message, $file, $line, $trace, $uri, $method, $userId, $ip]);
                } catch (Throwable $innerEx) {
                    @error_log("[SystemErrorLogger Fallback] " . $normalizedType . ": " . $message);
                }
            }
        } catch (Throwable $e) {
            @error_log("[SystemErrorLogger] " . $type . ": " . $message . " | Error: " . $e->getMessage());
        }
    }

    /**
     * Ensures table and required forensic telemetry columns exist
     */
    private static function ensureTableSchema(PDO $pdo): void {
        if (self::$schemaChecked) return;
        self::$schemaChecked = true;

        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS `system_error_logs` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `error_type` VARCHAR(100) NOT NULL DEFAULT 'ERROR',
                `message` TEXT NOT NULL,
                `file` VARCHAR(255) NULL,
                `line` INT UNSIGNED NULL,
                `stack_trace` LONGTEXT NULL,
                `request_uri` VARCHAR(255) NULL,
                `request_method` VARCHAR(10) NULL,
                `request_data` LONGTEXT NULL,
                `request_headers` LONGTEXT NULL,
                `user_id` BIGINT UNSIGNED NULL,
                `user_details` LONGTEXT NULL,
                `ip_address` VARCHAR(45) NULL,
                `location_details` LONGTEXT NULL,
                `device_details` LONGTEXT NULL,
                `resolved` TINYINT(1) NOT NULL DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_err_type` (`error_type`),
                INDEX `idx_err_resolved` (`resolved`),
                INDEX `idx_err_created` (`created_at` DESC)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        } catch (Throwable $e) {}
    }

    /**
     * Emergency column migration if table was created with older schema
     */
    public static function runEmergencyMigration(PDO $pdo): void {
        $cols = [
            'request_data' => 'LONGTEXT NULL AFTER `request_method`',
            'request_headers' => 'LONGTEXT NULL AFTER `request_data`',
            'user_details' => 'LONGTEXT NULL AFTER `user_id`',
            'location_details' => 'LONGTEXT NULL AFTER `ip_address`',
            'device_details' => 'LONGTEXT NULL AFTER `location_details`',
        ];

        foreach ($cols as $colName => $definition) {
            try {
                $pdo->exec("ALTER TABLE `system_error_logs` ADD COLUMN `{$colName}` {$definition}");
            } catch (Throwable $e) {}
        }
    }

    /**
     * Captures and sanitizes request parameters, query string, and payload
     */
    public static function captureRequestData(?array $extra = null): string {
        $data = [
            'query_params' => !empty($_GET) ? self::maskSensitiveData($_GET) : [],
            'body_params' => !empty($_POST) ? self::maskSensitiveData($_POST) : [],
            'content_type' => $_SERVER['CONTENT_TYPE'] ?? ($_SERVER['HTTP_CONTENT_TYPE'] ?? null),
            'content_length' => $_SERVER['CONTENT_LENGTH'] ?? ($_SERVER['HTTP_CONTENT_LENGTH'] ?? null),
        ];

        // Capture raw php://input for API JSON requests or PUT/PATCH
        if (empty($data['body_params']) && !empty($_SERVER['REQUEST_METHOD']) && in_array(strtoupper($_SERVER['REQUEST_METHOD']), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            $rawInput = @file_get_contents('php://input');
            if (!empty($rawInput) && strlen($rawInput) < 65536) {
                $decoded = @json_decode($rawInput, true);
                if (is_array($decoded)) {
                    $data['json_payload'] = self::maskSensitiveData($decoded);
                } else {
                    $data['raw_payload'] = substr($rawInput, 0, 2000);
                }
            }
        }

        if (!empty($extra)) {
            $data['context_extra'] = self::maskSensitiveData($extra);
        }

        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?: '{}';
    }

    /**
     * Captures and masks HTTP Request Headers
     */
    public static function captureRequestHeaders(): string {
        $headers = [];
        if (function_exists('getallheaders')) {
            $rawHeaders = getallheaders();
            if (is_array($rawHeaders)) {
                $headers = $rawHeaders;
            }
        }

        if (empty($headers)) {
            foreach ($_SERVER as $key => $val) {
                if (str_starts_with($key, 'HTTP_')) {
                    $headerName = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                    $headers[$headerName] = $val;
                } elseif (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH', 'CONTENT_MD5'], true)) {
                    $headerName = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
                    $headers[$headerName] = $val;
                }
            }
        }

        // Mask sensitive tokens in headers
        $maskedHeaders = [];
        foreach ($headers as $k => $v) {
            $lowerKey = strtolower((string)$k);
            if (str_contains($lowerKey, 'authorization') || str_contains($lowerKey, 'token') || str_contains($lowerKey, 'secret') || str_contains($lowerKey, 'api-key') || str_contains($lowerKey, 'apikey')) {
                if (is_string($v) && strlen($v) > 12) {
                    $prefix = substr($v, 0, 7);
                    $maskedHeaders[$k] = $prefix . '...[REDACTED_FOR_SECURITY]';
                } else {
                    $maskedHeaders[$k] = '********';
                }
            } elseif (str_contains($lowerKey, 'cookie')) {
                // Keep cookie keys, mask values
                $maskedCookies = [];
                $pairs = explode(';', (string)$v);
                foreach ($pairs as $pair) {
                    $parts = explode('=', trim($pair), 2);
                    if (count($parts) === 2) {
                        $cKey = trim($parts[0]);
                        $cVal = trim($parts[1]);
                        if (strlen($cVal) > 6) {
                            $maskedCookies[] = $cKey . '=' . substr($cVal, 0, 3) . '***' . substr($cVal, -3);
                        } else {
                            $maskedCookies[] = $cKey . '=***';
                        }
                    } else {
                        $maskedCookies[] = $pair;
                    }
                }
                $maskedHeaders[$k] = implode('; ', $maskedCookies);
            } else {
                $maskedHeaders[$k] = $v;
            }
        }

        return json_encode($maskedHeaders, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?: '{}';
    }

    /**
     * Parses client User-Agent into detailed device, OS, and browser breakdown
     */
    public static function captureDeviceDetails(): string {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown / CLI Environment';

        $os = 'Unknown OS';
        if (stripos($ua, 'Windows NT 10.0') !== false || stripos($ua, 'Windows 11') !== false) {
            $os = 'Windows 11 / 10 🪟';
        } elseif (stripos($ua, 'Windows NT 6.3') !== false) {
            $os = 'Windows 8.1 🪟';
        } elseif (stripos($ua, 'Windows NT 6.1') !== false) {
            $os = 'Windows 7 🪟';
        } elseif (stripos($ua, 'Windows NT') !== false) {
            $os = 'Windows NT 🪟';
        } elseif (stripos($ua, 'iPhone') !== false) {
            $os = 'iOS (iPhone) 📱';
        } elseif (stripos($ua, 'iPad') !== false) {
            $os = 'iPadOS 📟';
        } elseif (stripos($ua, 'Macintosh') !== false || stripos($ua, 'Mac OS X') !== false) {
            $os = 'macOS 🍏';
        } elseif (stripos($ua, 'Android') !== false) {
            $os = 'Android 🤖';
        } elseif (stripos($ua, 'Linux') !== false) {
            $os = 'Linux 🐧';
        } elseif (stripos($ua, 'CrOS') !== false) {
            $os = 'ChromeOS 💻';
        }

        $browser = 'Unknown Browser';
        $browserVersion = '';
        if (preg_match('/Edg(?:e)?\/([0-9\.]+)/i', $ua, $m)) {
            $browser = 'Microsoft Edge';
            $browserVersion = $m[1] ?? '';
        } elseif (preg_match('/OPR\/([0-9\.]+)/i', $ua, $m) || stripos($ua, 'Opera') !== false) {
            $browser = 'Opera';
            $browserVersion = $m[1] ?? '';
        } elseif (preg_match('/Chrome\/([0-9\.]+)/i', $ua, $m) && stripos($ua, 'Chromium') === false) {
            $browser = 'Google Chrome';
            $browserVersion = $m[1] ?? '';
        } elseif (preg_match('/Firefox\/([0-9\.]+)/i', $ua, $m)) {
            $browser = 'Mozilla Firefox';
            $browserVersion = $m[1] ?? '';
        } elseif (preg_match('/Version\/([0-9\.]+).*Safari/i', $ua, $m)) {
            $browser = 'Apple Safari';
            $browserVersion = $m[1] ?? '';
        } elseif (stripos($ua, 'PostmanRuntime') !== false) {
            $browser = 'Postman API Client';
        } elseif (stripos($ua, 'curl') !== false) {
            $browser = 'cURL CLI';
        } elseif (stripos($ua, 'bot') !== false || stripos($ua, 'crawler') !== false || stripos($ua, 'spider') !== false) {
            $browser = 'Automated Bot / Web Crawler 🤖';
        }

        $deviceType = 'Desktop 🖥️';
        if (stripos($ua, 'Mobile') !== false || stripos($ua, 'iPhone') !== false || (stripos($ua, 'Android') !== false && stripos($ua, 'Tablet') === false)) {
            $deviceType = 'Mobile Device 📱';
        } elseif (stripos($ua, 'iPad') !== false || stripos($ua, 'Tablet') !== false) {
            $deviceType = 'Tablet Device 📟';
        } elseif (stripos($ua, 'bot') !== false || stripos($ua, 'crawler') !== false) {
            $deviceType = 'Search Engine Bot 🤖';
        }

        $info = [
            'os' => $os,
            'browser' => $browser,
            'browser_version' => $browserVersion,
            'device_type' => $deviceType,
            'raw_user_agent' => $ua
        ];

        return json_encode($info, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '{}';
    }

    /**
     * Determines client IP and Geographic Location
     */
    public static function captureLocationDetails(string $ip): string {
        $country = 'Pakistan 🇵🇰';
        $countryCode = 'PK';
        $city = 'Unknown City';
        $region = 'Unknown Region';
        $isLocal = false;

        if (!empty($_SERVER['HTTP_CF_IPCOUNTRY'])) {
            $countryCode = strtoupper(trim($_SERVER['HTTP_CF_IPCOUNTRY']));
            $country = self::countryCodeToName($countryCode);
        }

        if (!empty($_SERVER['HTTP_CF_IPCITY'])) {
            $city = trim($_SERVER['HTTP_CF_IPCITY']);
        }

        if (!empty($_SERVER['HTTP_CF_REGION'])) {
            $region = trim($_SERVER['HTTP_CF_REGION']);
        }

        if ($ip === '127.0.0.1' || $ip === '::1' || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.') || str_starts_with($ip, '172.16.')) {
            $isLocal = true;
            $country = 'Localhost / Intranet 🏠';
            $city = 'Local Development Node';
            $region = 'Localhost (XAMPP / Dev)';
        }

        $details = [
            'ip_address' => $ip,
            'is_local' => $isLocal,
            'country_code' => $countryCode,
            'country_name' => $country,
            'city' => $city,
            'region' => $region,
            'timezone' => $_SERVER['HTTP_CF_TIMEZONE'] ?? 'Asia/Karachi'
        ];

        return json_encode($details, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '{}';
    }

    /**
     * Captures identity details of user triggering the error
     */
    public static function captureUserDetails(?int $userId): string {
        if (!$userId || $userId <= 0) {
            return json_encode([
                'authenticated' => false,
                'user_type' => 'Guest Visitor / Anonymous Session',
                'session_id' => session_id() ?: null
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '{}';
        }

        try {
            $pdo = Database::getConnection();
            if ($pdo) {
                $stmt = $pdo->prepare("SELECT id, username, email, role, status, is_flagged_suspicious, strike_count, created_at FROM users WHERE id = ? LIMIT 1");
                $stmt->execute([$userId]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user) {
                    return json_encode([
                        'authenticated' => true,
                        'id' => (int)$user['id'],
                        'username' => $user['username'],
                        'email' => $user['email'],
                        'role' => $user['role'],
                        'status' => $user['status'],
                        'strike_count' => (int)$user['strike_count'],
                        'is_flagged_suspicious' => (int)$user['is_flagged_suspicious'],
                        'registered_at' => $user['created_at']
                    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '{}';
                }
            }
        } catch (Throwable $e) {}

        // Fallback to session data
        return json_encode([
            'authenticated' => true,
            'id' => (int)$userId,
            'role' => Session::get('user_role') ?? 'member',
            'note' => 'Retrieved from active session state'
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '{}';
    }

    /**
     * Safely redacts sensitive passwords, tokens, API keys and secrets
     */
    private static function maskSensitiveData(mixed $data): mixed {
        if (!is_array($data)) return $data;

        $sanitized = [];
        foreach ($data as $key => $val) {
            $lowerKey = strtolower((string)$key);
            if (preg_match('/(password|pwd|pass|secret|token|auth|key|credit|card|cvv|pin|ssn)/i', $lowerKey)) {
                if (is_string($val) && strlen($val) > 10) {
                    $sanitized[$key] = substr($val, 0, 3) . '********' . substr($val, -2) . ' [MASKED]';
                } else {
                    $sanitized[$key] = '******** [MASKED]';
                }
            } elseif (is_array($val)) {
                $sanitized[$key] = self::maskSensitiveData($val);
            } else {
                $sanitized[$key] = $val;
            }
        }
        return $sanitized;
    }

    /**
     * Resolves accurate client IP with Cloudflare & proxy header inspection
     */
    public static function getClientIp(): string {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return trim($_SERVER['HTTP_CF_CONNECTING_IP']);
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return trim($_SERVER['HTTP_CLIENT_IP']);
        }
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    /**
     * Converts ISO 2-Letter Country Code to full country name with flag
     */
    private static function countryCodeToName(string $code): string {
        $map = [
            'PK' => 'Pakistan 🇵🇰',
            'US' => 'United States 🇺🇸',
            'GB' => 'United Kingdom 🇬🇧',
            'CA' => 'Canada 🇨🇦',
            'AU' => 'Australia 🇦🇺',
            'DE' => 'Germany 🇩🇪',
            'FR' => 'France 🇫🇷',
            'IN' => 'India 🇮🇳',
            'AE' => 'United Arab Emirates 🇦🇪',
            'SA' => 'Saudi Arabia 🇸🇦',
            'TR' => 'Turkey 🇹🇷',
            'CN' => 'China 🇨🇳',
            'JP' => 'Japan 🇯🇵',
            'BR' => 'Brazil 🇧🇷',
            'NL' => 'Netherlands 🇳🇱',
            'SG' => 'Singapore 🇸🇬',
            'ID' => 'Indonesia 🇮🇩',
            'MY' => 'Malaysia 🇲🇾',
            'BD' => 'Bangladesh 🇧🇩',
            'EG' => 'Egypt 🇪🇬',
            'RU' => 'Russia 🇷🇺',
            'IT' => 'Italy 🇮🇹',
            'ES' => 'Spain 🇪🇸',
            'KR' => 'South Korea 🇰🇷',
            'ZA' => 'South Africa 🇿🇦',
            'NG' => 'Nigeria 🇳🇬',
            'SE' => 'Sweden 🇸🇪',
            'CH' => 'Switzerland 🇨🇭',
        ];

        return $map[$code] ?? "{$code} 🌐";
    }

    public static function handleError(int $severity, string $message, string $file, int $line): bool {
        if (!(error_reporting() & $severity)) {
            return false;
        }

        $typeMap = [
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_STRICT => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED',
        ];

        $type = $typeMap[$severity] ?? 'E_UNKNOWN';
        self::log($type, $message, $file, $line, (new \Exception())->getTraceAsString());
        return false;
    }

    public static function handleException(Throwable $e): void {
        $type = get_class($e);
        if (str_contains($type, '\\')) {
            $parts = explode('\\', $type);
            $type = end($parts);
        }
        self::log($type, $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());

        $isJson = isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'json')
            || str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api')
            || str_contains($_SERVER['REQUEST_URI'] ?? '', '/detail/')
            || !empty($_SERVER['HTTP_X_REQUESTED_WITH']);

        if ($isJson) {
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
                'error_type' => $type,
                'file' => basename($e->getFile()),
                'line' => $e->getLine()
            ]);
            exit;
        }
    }

    public static function handleShutdown(): void {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            self::log('FATAL_ERROR', $error['message'], $error['file'], $error['line'], '');
        }
    }
}
