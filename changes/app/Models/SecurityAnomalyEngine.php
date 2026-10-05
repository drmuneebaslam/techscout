<?php
namespace App\Models;

use App\Core\Database;
use App\Core\NotificationHelper;

class SecurityAnomalyEngine {
    const MAX_FAILED_ATTEMPTS = 5;
    const ATTEMPT_WINDOW_MINUTES = 10;
    const LOCKOUT_DURATION_MINUTES = 15;

    /**
     * Check if an IP address is currently locked out
     */
    public static function isIpLockedOut(string $ip): array {
        $lock = Database::fetch(
            "SELECT * FROM ip_lockouts WHERE ip_address = ? AND is_active = 1",
            [$ip]
        );

        if ($lock && strtotime($lock['locked_until']) > time()) {
            $secondsRemaining = max(0, strtotime($lock['locked_until']) - time());
            return [
                'locked' => true,
                'reason' => $lock['reason'],
                'seconds_remaining' => $secondsRemaining,
                'minutes_remaining' => ceil($secondsRemaining / 60),
                'locked_until' => $lock['locked_until']
            ];
        }

        return ['locked' => false, 'seconds_remaining' => 0];
    }

    /**
     * Record a failed login / auth / 2FA attempt and trigger automatic lockout if threshold reached
     */
    public static function recordFailedAttempt(string $identifier, string $ip, ?string $userAgent = null): array {
        Database::execute(
            "INSERT INTO failed_login_attempts (identifier, ip_address, user_agent, attempted_at) VALUES (?, ?, ?, NOW())",
            [$identifier, $ip, $userAgent ?: ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown')]
        );

        // Count recent failed attempts from this IP within the sliding window
        $recent = Database::fetch(
            "SELECT COUNT(*) as cnt FROM failed_login_attempts 
             WHERE ip_address = ? AND attempted_at >= DATE_SUB(NOW(), INTERVAL ? MINUTE)",
            [$ip, self::ATTEMPT_WINDOW_MINUTES]
        );

        $failCount = (int)($recent['cnt'] ?? 0);
        $maxRetries = (int)\App\Core\Config::get('auth_max_login_retries', self::MAX_FAILED_ATTEMPTS);

        if ($failCount >= $maxRetries) {
            $lockedUntil = date('Y-m-d H:i:s', strtotime('+' . self::LOCKOUT_DURATION_MINUTES . ' minutes'));
            $reason = "Triggered automatic 15-minute brute-force lockout ({$failCount} consecutive failed attempts)";

            Database::execute(
                "INSERT INTO ip_lockouts (ip_address, reason, failed_count, locked_until, is_active, created_at)
                 VALUES (?, ?, ?, ?, 1, NOW())
                 ON DUPLICATE KEY UPDATE reason = VALUES(reason), failed_count = VALUES(failed_count), locked_until = VALUES(locked_until), is_active = 1",
                [$ip, $reason, $failCount, $lockedUntil]
            );

            // Log security anomaly
            self::logAnomaly(
                null,
                $ip,
                'brute_force_lockout',
                'high',
                "IP {$ip} automatically locked out for 15 minutes after {$failCount} failed login attempts.",
                ['identifier' => $identifier, 'failed_count' => $failCount, 'locked_until' => $lockedUntil],
                $userAgent
            );

            return [
                'locked' => true,
                'failures' => $failCount,
                'locked_until' => $lockedUntil,
                'minutes_remaining' => self::LOCKOUT_DURATION_MINUTES
            ];
        }

        return [
            'locked' => false,
            'failures' => $failCount,
            'attempts_left' => max(0, $maxRetries - $failCount)
        ];
    }

    /**
     * Clear failed attempts upon successful authentication
     */
    public static function clearFailedAttempts(string $identifier, string $ip): void {
        Database::execute("DELETE FROM failed_login_attempts WHERE ip_address = ? OR identifier = ?", [$ip, $identifier]);
    }

    /**
     * Inspect login context for new IP or device anomalies
     */
    public static function checkLoginAnomaly(int $userId, string $ip, string $userAgent): void {
        $user = Database::fetch("SELECT * FROM users WHERE id = ?", [$userId]);
        if (!$user) return;

        // Check previous known logins
        $prevLogin = Database::fetch(
            "SELECT id FROM user_sessions WHERE user_id = ? AND ip_address = ? LIMIT 1",
            [$userId, $ip]
        );

        if (!$prevLogin && !empty($user['last_login_at'])) {
            // Unrecognized IP login
            self::logAnomaly(
                $userId,
                $ip,
                'new_ip_login',
                'medium',
                "User {$user['username']} logged in from a new IP address: {$ip}",
                ['previous_ip' => $user['last_login_ip'] ?? 'none'],
                $userAgent
            );

            // Dispatch notification to user
            NotificationHelper::send(
                $userId,
                'security',
                "🔒 New Login Detected from IP {$ip}",
                "Your TechScout account was just accessed from a new IP address ({$ip}). If this wasn't you, review your Active Sessions immediately.",
                "/author/dashboard?tab=security"
            );
        }
    }

    /**
     * Log a Security Anomaly event
     */
    public static function logAnomaly(?int $userId, string $ip, string $eventType, string $severity, string $description, array $metadata = [], ?string $userAgent = null): int {
        Database::execute(
            "INSERT INTO security_anomalies (user_id, ip_address, user_agent, event_type, severity, description, metadata, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW())",
            [
                $userId,
                $ip,
                $userAgent ?: ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'),
                $eventType,
                $severity,
                $description,
                json_encode($metadata)
            ]
        );

        return (int)Database::lastInsertId();
    }

    /**
     * Get Recent Security Anomalies for Admin Firewall HUD
     */
    public static function getRecentAnomalies(int $limit = 50): array {
        $limit = max(1, (int)$limit);
        return Database::query(
            "SELECT sa.*, u.username, u.email 
             FROM security_anomalies sa
             LEFT JOIN users u ON sa.user_id = u.id
             ORDER BY sa.created_at DESC
             LIMIT {$limit}"
        );
    }

    /**
     * Unlock an IP Address manually
     */
    public static function unlockIp(string $ip): bool {
        Database::execute("UPDATE ip_lockouts SET is_active = 0 WHERE ip_address = ?", [$ip]);
        Database::execute("DELETE FROM failed_login_attempts WHERE ip_address = ?", [$ip]);
        return true;
    }

    /**
     * Mark anomaly resolved
     */
    public static function resolveAnomaly(int $id): bool {
        return Database::execute("UPDATE security_anomalies SET is_resolved = 1, resolved_at = NOW() WHERE id = ?", [$id]);
    }
}
