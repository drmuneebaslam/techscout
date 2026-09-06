<?php
namespace App\Core;

use PDO;
use Throwable;

class ErrorLogger {
    private static bool $isRegistered = false;

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

    public static function log(string $type, string $message, ?string $file = null, ?int $line = null, ?string $trace = null): void {
        try {
            $pdo = Database::getConnection();
            $pdo->exec("CREATE TABLE IF NOT EXISTS `system_error_logs` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `error_type` VARCHAR(100) NOT NULL,
                `message` TEXT NOT NULL,
                `file` VARCHAR(255) NULL,
                `line` INT UNSIGNED NULL,
                `stack_trace` LONGTEXT NULL,
                `request_uri` VARCHAR(255) NULL,
                `request_method` VARCHAR(10) NULL,
                `user_id` BIGINT UNSIGNED NULL,
                `ip_address` VARCHAR(45) NULL,
                `resolved` TINYINT(1) NOT NULL DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_err_type` (`error_type`),
                INDEX `idx_err_resolved` (`resolved`),
                INDEX `idx_err_created` (`created_at` DESC)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            $uri = $_SERVER['REQUEST_URI'] ?? null;
            $method = $_SERVER['REQUEST_METHOD'] ?? null;
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $userId = Session::get('user_id');

            $stmt = $pdo->prepare("INSERT INTO `system_error_logs` (`error_type`, `message`, `file`, `line`, `stack_trace`, `request_uri`, `request_method`, `user_id`, `ip_address`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$type, $message, $file, $line, $trace, $uri, $method, $userId, $ip]);
        } catch (Throwable $e) {
            @error_log("[SystemErrorLogger] " . $type . ": " . $message);
        }
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
        self::log(get_class($e), $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());

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
