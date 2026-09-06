<?php
namespace App\Models;

use App\Core\Database;
use App\Models\AuditLog;

class SessionManager {

    /**
     * Parse User Agent into readable Device, OS & Browser
     */
    public static function parseUserAgent(?string $ua): array {
        $ua = $ua ?: ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown Client');

        // OS
        $os = 'Unknown OS';
        if (stripos($ua, 'Windows NT 10.0') !== false) $os = 'Windows 11/10';
        elseif (stripos($ua, 'Windows NT') !== false) $os = 'Windows';
        elseif (stripos($ua, 'Macintosh') !== false || stripos($ua, 'Mac OS X') !== false) $os = 'macOS';
        elseif (stripos($ua, 'iPhone') !== false) $os = 'iOS (iPhone)';
        elseif (stripos($ua, 'iPad') !== false) $os = 'iOS (iPad)';
        elseif (stripos($ua, 'Android') !== false) $os = 'Android';
        elseif (stripos($ua, 'Linux') !== false) $os = 'Linux';

        // Browser
        $browser = 'Unknown Browser';
        if (stripos($ua, 'Edg/') !== false) $browser = 'Microsoft Edge';
        elseif (stripos($ua, 'Chrome/') !== false) $browser = 'Google Chrome';
        elseif (stripos($ua, 'Firefox/') !== false) $browser = 'Mozilla Firefox';
        elseif (stripos($ua, 'Safari/') !== false) $browser = 'Apple Safari';
        elseif (stripos($ua, 'Opera') !== false || stripos($ua, 'OPR/') !== false) $browser = 'Opera';

        // Device Type
        $deviceType = 'desktop';
        if (stripos($ua, 'Mobile') !== false || stripos($ua, 'iPhone') !== false || stripos($ua, 'Android') !== false) {
            $deviceType = 'mobile';
        } elseif (stripos($ua, 'iPad') !== false || stripos($ua, 'Tablet') !== false) {
            $deviceType = 'tablet';
        }

        return [
            'os' => $os,
            'browser' => $browser,
            'device_type' => $deviceType,
            'user_agent' => $ua
        ];
    }

    /**
     * Deduplicate redundant sessions for the same physical device/browser
     */
    public static function cleanupDuplicates(?int $userId = null): void {
        try {
            $userClause = $userId ? "AND s1.user_id = " . (int)$userId : "";
            Database::execute("
                DELETE s1 FROM user_sessions s1
                INNER JOIN user_sessions s2 
                ON s1.user_id = s2.user_id 
                AND s1.ip_address = s2.ip_address 
                AND s1.browser_name = s2.browser_name 
                AND s1.os_name = s2.os_name
                AND s1.id < s2.id
                WHERE 1=1 {$userClause}
            ");
        } catch (\Throwable $e) {}
    }

    /**
     * Register or update an active user session deduplicated per client device
     */
    public static function registerSession(int $userId, string $sessionToken, string $ip, ?string $userAgent = null): string {
        $tokenHash = hash('sha256', $sessionToken);
        $uaInfo = self::parseUserAgent($userAgent);

        $locHint = ($ip === '127.0.0.1' || $ip === '::1') ? 'Local Development Server' : 'Secure Public Network';

        // Check if an existing active device session exists for this user + IP + browser + OS
        $existing = Database::fetch(
            "SELECT id FROM user_sessions 
             WHERE user_id = ? AND ip_address = ? AND browser_name = ? AND os_name = ? 
             ORDER BY last_active_at DESC LIMIT 1",
            [$userId, $ip, $uaInfo['browser'], $uaInfo['os']]
        );

        if ($existing) {
            // Update the existing device record with current token hash and timestamp (NO DUPLICATE DEVICE ROW)
            Database::execute(
                "UPDATE user_sessions 
                 SET session_token_hash = ?, user_agent = ?, device_type = ?, location_hint = ?, is_current = 1, last_active_at = NOW() 
                 WHERE id = ?",
                [$tokenHash, $uaInfo['user_agent'], $uaInfo['device_type'], $locHint, $existing['id']]
            );
        } else {
            // New distinct device: insert single clean entry
            Database::execute(
                "INSERT INTO user_sessions (user_id, session_token_hash, ip_address, user_agent, device_type, browser_name, os_name, location_hint, is_current, last_active_at, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())",
                [
                    $userId,
                    $tokenHash,
                    $ip,
                    $uaInfo['user_agent'],
                    $uaInfo['device_type'],
                    $uaInfo['browser'],
                    $uaInfo['os'],
                    $locHint
                ]
            );
        }

        // Prune any legacy duplicates in the background
        self::cleanupDuplicates($userId);

        return $tokenHash;
    }

    /**
     * Get all active sessions for a user
     */
    public static function getUserSessions(int $userId, ?string $currentSessionToken = null): array {
        self::cleanupDuplicates($userId);

        $currentHash = $currentSessionToken ? hash('sha256', $currentSessionToken) : null;
        $sessions = Database::query(
            "SELECT * FROM user_sessions WHERE user_id = ? ORDER BY last_active_at DESC",
            [$userId]
        );

        foreach ($sessions as &$s) {
            $s['is_current_client'] = ($currentHash && $s['session_token_hash'] === $currentHash);
            $s['icon'] = match($s['device_type']) {
                'mobile' => 'smartphone',
                'tablet' => 'tablet',
                default => 'monitor'
            };
        }

        return $sessions;
    }

    /**
     * Revoke a single remote session
     */
    public static function revokeSession(int $userId, int $sessionId): bool {
        $session = Database::fetch("SELECT * FROM user_sessions WHERE id = ? AND user_id = ?", [$sessionId, $userId]);
        if ($session) {
            Database::execute("DELETE FROM user_sessions WHERE id = ?", [$sessionId]);
            AuditLog::record('session.remote_revoked', 'user_sessions', $sessionId, [
                'user_id' => $userId,
                'ip' => $session['ip_address'],
                'browser' => $session['browser_name']
            ]);
            return true;
        }
        return false;
    }

    /**
     * Terminate all other sessions for a user except the current one
     */
    public static function terminateOtherSessions(int $userId, ?string $currentSessionToken = null): int {
        $currentHash = $currentSessionToken ? hash('sha256', $currentSessionToken) : null;
        
        if ($currentHash) {
            $countRow = Database::fetch("SELECT COUNT(*) as c FROM user_sessions WHERE user_id = ? AND session_token_hash != ?", [$userId, $currentHash]);
            $revokedCount = (int)($countRow['c'] ?? 0);
            Database::execute("DELETE FROM user_sessions WHERE user_id = ? AND session_token_hash != ?", [$userId, $currentHash]);
        } else {
            $countRow = Database::fetch("SELECT COUNT(*) as c FROM user_sessions WHERE user_id = ?", [$userId]);
            $revokedCount = (int)($countRow['c'] ?? 0);
            Database::execute("DELETE FROM user_sessions WHERE user_id = ?", [$userId]);
        }

        AuditLog::record('session.all_remote_terminated', 'users', $userId, [
            'user_id' => $userId,
            'revoked_count' => $revokedCount
        ]);

        return $revokedCount;
    }
}
