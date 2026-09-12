<?php
namespace App\Core;

class Config {
    public static array $settings = [
        'app_name' => 'TechScout',
        'app_tagline' => 'The Frontier of Tech, Scouted for Builders.',
        'app_url' => 'https://funwithcoding.free.nf',
        'db_host' => 'sql306.infinityfree.com',
        'db_port' => '3306',
        'db_name' => 'if0_40102772_techscoutmainafterallchanges',
        'db_user' => 'if0_40102772',
        'db_pass' => 'Fp9QFWfj5fm',
        'session_lifetime' => 86400 * 7,
        'environment' => 'production' // 'development' or 'production'
    ];

    public static function get(string $key, $default = null) {
        return self::$settings[$key] ?? $default;
    }

    public static function set(string $key, $value): void {
        self::$settings[$key] = $value;
    }

    public static function url(string $path = ''): string {
        $path = trim($path);
        if ($path === '' || $path === '#') {
            return $path;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'data:') || str_starts_with($path, 'mailto:') || str_starts_with($path, 'javascript:') || str_starts_with($path, 'tel:')) {
            return $path;
        }

        $base = rtrim(self::get('app_url', 'https://funwithcoding.free.nf'), '/');
        
        // Strip duplicate subfolder prefix if already present in path
        $parsed = parse_url($base);
        $basePath = trim($parsed['path'] ?? '', '/');
        if ($basePath !== '') {
            $prefix = '/' . $basePath;
            if (str_starts_with($path, $prefix . '/')) {
                $path = substr($path, strlen($prefix));
            } elseif ($path === $prefix) {
                $path = '';
            }
        }

        $cleanPath = ltrim($path, '/');
        return $cleanPath ? "{$base}/{$cleanPath}" : $base . '/';
    }
}
