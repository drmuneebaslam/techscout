<?php
namespace App\Core;

class Session {
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            if (!headers_sent()) {
                session_set_cookie_params([
                    'lifetime' => Config::get('session_lifetime', 86400 * 7),
                    'path' => '/',
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);
            }
            @session_start();
        }
    }

    public static function set(string $key, $value): void {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, $value = null) {
        self::start();
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
            return null;
        }
        $val = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $val;
    }

    public static function getFlash(string $key, $default = null) {
        return self::flash($key) ?? $default;
    }

    public static function hasFlash(string $key): bool {
        self::start();
        return isset($_SESSION['_flash'][$key]);
    }

    public static function csrfToken(): string {
        self::start();
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }

    public static function verifyCsrf(?string $token): bool {
        self::start();
        $stored = $_SESSION['_csrf_token'] ?? '';
        return $stored && $token && hash_equals($stored, $token);
    }
}
