<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Auth;

class AuditLog {
    public static function record(string $action, ?string $entityType = null, ?int $entityId = null, ?array $details = null, ?array $beforeState = null, ?array $afterState = null, string $actionDomain = 'general'): void {
        self::log($action, $entityType, $entityId, $details, $beforeState, $afterState, $actionDomain);
    }

    public static function log(string $action, ?string $entityType = null, ?int $entityId = null, ?array $details = null, ?array $beforeState = null, ?array $afterState = null, string $actionDomain = 'general'): void {
        $user = Auth::user();
        $userId = $user ? (int)$user['id'] : null;
        $actorRole = $user['role'] ?? ($userId ? 'member' : 'system');
        $ip = self::getClientIp();
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'CLI / Background Runner';
        $country = self::detectCountry($ip);
        $deviceInfo = self::parseDevice($ua);
        $detailsJson = $details ? json_encode($details, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null;
        $beforeJson = $beforeState ? json_encode($beforeState, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null;
        $afterJson = $afterState ? json_encode($afterState, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null;

        try {
            Database::execute("INSERT INTO audit_logs (user_id, actor_role, action, action_domain, entity_type, entity_id, ip_address, country, device_info, user_agent, details, before_state, after_state, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())", [
                $userId, $actorRole, $action, $actionDomain, $entityType, $entityId, $ip, $country, $deviceInfo, $ua, $detailsJson, $beforeJson, $afterJson
            ]);
        } catch (\Throwable $e) {
            try {
                Database::execute("INSERT INTO audit_logs (user_id, action, entity_type, entity_id, ip_address, user_agent, details, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())", [
                    $userId, $action, $entityType, $entityId, $ip, $ua, $detailsJson
                ]);
            } catch (\Throwable $ex) {}
        }
    }

    public static function getClientIp(): string {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public static function detectCountry(string $ip): string {
        if (!empty($_SERVER['HTTP_CF_IPCOUNTRY'])) {
            return strtoupper($_SERVER['HTTP_CF_IPCOUNTRY']);
        }
        if ($ip === '127.0.0.1' || $ip === '::1' || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.')) {
            return 'Pakistan 🇵🇰 (Local Host)';
        }
        return 'Global / PK 🇵🇰';
    }

    public static function parseDevice(string $ua): string {
        if (empty($ua) || $ua === 'CLI' || $ua === 'CLI / Background Runner') {
            return 'Server CLI Environment 🖥️';
        }

        // Detect OS
        $os = 'Unknown OS';
        if (stripos($ua, 'Windows NT 10.0') !== false || stripos($ua, 'Windows 11') !== false) {
            $os = 'Windows 11 / 10 🪟';
        } elseif (stripos($ua, 'Macintosh') !== false || stripos($ua, 'Mac OS X') !== false) {
            $os = 'macOS 🍏';
        } elseif (stripos($ua, 'iPhone') !== false) {
            $os = 'iPhone iOS 📱';
        } elseif (stripos($ua, 'iPad') !== false) {
            $os = 'iPadOS 📟';
        } elseif (stripos($ua, 'Android') !== false) {
            $os = 'Android 🤖';
        } elseif (stripos($ua, 'Linux') !== false) {
            $os = 'Linux 🐧';
        }

        // Detect Browser
        $browser = 'Unknown Browser';
        if (stripos($ua, 'Edg/') !== false) {
            $browser = 'Edge';
        } elseif (stripos($ua, 'Chrome/') !== false) {
            $browser = 'Chrome';
        } elseif (stripos($ua, 'Firefox/') !== false) {
            $browser = 'Firefox';
        } elseif (stripos($ua, 'Safari/') !== false && stripos($ua, 'Chrome') === false) {
            $browser = 'Safari';
        } elseif (stripos($ua, 'Opera') !== false || stripos($ua, 'OPR/') !== false) {
            $browser = 'Opera';
        }

        // Detect Device Form
        $deviceType = (stripos($ua, 'Mobile') !== false || stripos($ua, 'Android') !== false || stripos($ua, 'iPhone') !== false) ? 'Mobile' : 'Desktop';

        return "{$browser} on {$os} ({$deviceType})";
    }

    public static function recent(int $limit = 50): array {
        return self::getFiltered(null, null, null, $limit);
    }

    public static function getFiltered(?int $userId = null, ?string $action = null, ?string $entityType = null, int $limit = 100): array {
        $sql = "SELECT al.*, u.username, u.email, u.role as user_role, COALESCE(a.display_name, u.username) as display_name, a.avatar_url 
                FROM audit_logs al 
                LEFT JOIN users u ON al.user_id = u.id 
                LEFT JOIN authors a ON u.id = a.user_id 
                WHERE 1=1";
        $params = [];

        if ($userId !== null && $userId > 0) {
            $sql .= " AND al.user_id = ?";
            $params[] = $userId;
        }

        if (!empty($action) && $action !== 'all') {
            $sql .= " AND al.action = ?";
            $params[] = $action;
        }

        if (!empty($entityType) && $entityType !== 'all') {
            $sql .= " AND al.entity_type = ?";
            $params[] = $entityType;
        }

        $sql .= " ORDER BY al.created_at DESC LIMIT {$limit}";
        try {
            return Database::query($sql, $params);
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function getDistinctActions(): array {
        try {
            $rows = Database::query("SELECT DISTINCT action FROM audit_logs ORDER BY action ASC");
            return array_column($rows, 'action');
        } catch (\Throwable $e) {
            return [];
        }
    }
}
