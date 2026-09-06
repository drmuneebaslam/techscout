<?php
namespace App\Models;

use App\Core\Database;
use App\Models\AuditLog;
use App\Core\NotificationHelper;
use App\Core\Config;

class User {
    public static function all(int $limit = 50, int $offset = 0): array {
        return Database::query("SELECT u.*, a.id as author_id, a.display_name, a.reputation_score, a.promo_credits, a.verification_badge, a.can_publish_prompts, a.can_publish_gpts, a.can_publish_tools, a.can_use_templates 
                               FROM users u 
                               LEFT JOIN authors a ON u.id = a.user_id 
                               ORDER BY u.created_at DESC LIMIT {$limit} OFFSET {$offset}");
    }

    public static function find(int $id): ?array {
        return Database::fetch("SELECT u.*, a.id as author_id, a.display_name, a.slug as author_slug, a.headline, a.bio, a.avatar_url, a.reputation_score, a.promo_credits, a.verification_badge, a.custom_badge_label, a.custom_badge_color, a.tier_override, a.tier_title, a.current_tier_code, a.tier_multiplier, u.strike_count as author_strikes 
                               FROM users u 
                               LEFT JOIN authors a ON u.id = a.user_id 
                               WHERE u.id = ?", [$id]);
    }

    public static function updateRole(int $id, string $role): bool {
        return Database::execute("UPDATE users SET role = ? WHERE id = ?", [$role, $id]);
    }

    public static function updateStatus(int $id, string $status, ?string $suspendedUntil = null): bool {
        return Database::execute("UPDATE users SET status = ?, suspended_until = ? WHERE id = ?", [$status, $suspendedUntil, $id]);
    }

    /**
     * Issue Structured Strike (1st Warning, 2nd Suspension, 3rd Ban)
     */
    public static function issueStrike(int $userId, string $reason, ?string $penaltyType = null, ?string $expiresAt = null, ?int $issuedBy = null, ?string $notes = null): array {
        // Count existing active strikes
        $activeCount = (int)(Database::fetch("SELECT COUNT(*) as c FROM author_strikes WHERE user_id = ? AND is_active = 1", [$userId])['c'] ?? 0);
        $strikeLevel = min(3, $activeCount + 1);

        // Auto-determine penalty type if not specified
        if (empty($penaltyType)) {
            $penaltyType = match($strikeLevel) {
                1 => 'warning',
                2 => 'suspension',
                3 => 'ban',
                default => 'warning'
            };
        }

        // Auto-determine expiration if not specified
        if (empty($expiresAt)) {
            if ($penaltyType === 'warning') {
                $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));
            } elseif ($penaltyType === 'suspension') {
                $expiresAt = date('Y-m-d H:i:s', strtotime('+14 days'));
            } else {
                $expiresAt = null; // Permanent ban has no expiration
            }
        }

        Database::execute(
            "INSERT INTO author_strikes (user_id, strike_level, penalty_type, reason, notes, issued_by_user_id, expires_at, is_active, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW())",
            [$userId, $strikeLevel, $penaltyType, $reason, $notes, $issuedBy, $expiresAt]
        );
        $strikeId = (int)Database::lastInsertId();

        // Update strike_count on users table
        $newActiveCount = $activeCount + 1;
        Database::execute("UPDATE users SET strike_count = ? WHERE id = ?", [$newActiveCount, $userId]);

        // Trigger escalation actions
        $user = Database::fetch("SELECT * FROM users WHERE id = ?", [$userId]);
        if ($penaltyType === 'ban' || $strikeLevel >= 3) {
            Database::execute("UPDATE users SET status = 'banned', suspended_until = NULL WHERE id = ?", [$userId]);
            // Terminate all sessions
            \App\Models\SessionManager::terminateOtherSessions($userId, null);
            NotificationHelper::send($userId, 'security', 'Account Permanently Banned (3rd Strike)', "Your account has been banned due to repeated guideline violations: {$reason}", '/support');
        } elseif ($penaltyType === 'suspension' || $strikeLevel === 2) {
            Database::execute("UPDATE users SET status = 'suspended', suspended_until = ? WHERE id = ?", [$expiresAt, $userId]);
            // Terminate active sessions
            \App\Models\SessionManager::terminateOtherSessions($userId, null);
            $expDateStr = $expiresAt ? date('M j, Y', strtotime($expiresAt)) : '14 days';
            NotificationHelper::send($userId, 'security', "Account Suspended until {$expDateStr} (2nd Strike)", "Your account has been suspended until {$expDateStr} for: {$reason}", '/support');
        } else {
            // 1st warning
            NotificationHelper::send($userId, 'system', 'Official Editorial Warning (1st Strike)', "An editorial strike was issued against your account: {$reason}. Further violations will result in account suspension.", '/support');
        }

        AuditLog::log('user.strike_issued', 'author_strikes', $strikeId, [
            'user_id' => $userId,
            'strike_level' => $strikeLevel,
            'penalty_type' => $penaltyType,
            'reason' => $reason,
            'expires_at' => $expiresAt,
            'issued_by' => $issuedBy
        ]);

        return [
            'success' => true,
            'strike_id' => $strikeId,
            'strike_level' => $strikeLevel,
            'penalty_type' => $penaltyType,
            'expires_at' => $expiresAt,
            'active_count' => $newActiveCount
        ];
    }

    /**
     * Pardon / Revoke an active strike
     */
    public static function pardonStrike(int $strikeId, ?int $pardonedBy = null): bool {
        $strike = Database::fetch("SELECT * FROM author_strikes WHERE id = ?", [$strikeId]);
        if (!$strike) return false;

        Database::execute("UPDATE author_strikes SET is_active = 0 WHERE id = ?", [$strikeId]);

        $userId = (int)$strike['user_id'];
        // Recount active strikes
        $remainingCount = (int)(Database::fetch("SELECT COUNT(*) as c FROM author_strikes WHERE user_id = ? AND is_active = 1", [$userId])['c'] ?? 0);
        Database::execute("UPDATE users SET strike_count = ? WHERE id = ?", [$remainingCount, $userId]);

        // Check if user should be restored from suspension/ban
        if ($remainingCount < 2) {
            $currUser = Database::fetch("SELECT status FROM users WHERE id = ?", [$userId]);
            if ($currUser && in_array($currUser['status'], ['suspended', 'banned'], true)) {
                Database::execute("UPDATE users SET status = 'active', suspended_until = NULL WHERE id = ?", [$userId]);
            }
        }

        AuditLog::log('user.strike_pardoned', 'author_strikes', $strikeId, [
            'user_id' => $userId,
            'strike_id' => $strikeId,
            'pardoned_by' => $pardonedBy,
            'remaining_active_strikes' => $remainingCount
        ]);

        return true;
    }

    /**
     * Get active and past strikes for user
     */
    public static function getStrikes(int $userId): array {
        return Database::query(
            "SELECT s.*, u.username as issued_by_username 
             FROM author_strikes s 
             LEFT JOIN users u ON s.issued_by_user_id = u.id 
             WHERE s.user_id = ? 
             ORDER BY s.created_at DESC",
            [$userId]
        );
    }

    /**
     * Consolidate two duplicate accounts into target ID
     */
    public static function mergeAccounts(int $sourceUserId, int $targetUserId, ?int $adminId = null): array {
        if ($sourceUserId <= 0 || $targetUserId <= 0 || $sourceUserId === $targetUserId) {
            throw new \InvalidArgumentException("Invalid source or target user ID for merging.");
        }

        $sourceUser = Database::fetch("SELECT * FROM users WHERE id = ?", [$sourceUserId]);
        $targetUser = Database::fetch("SELECT * FROM users WHERE id = ?", [$targetUserId]);
        if (!$sourceUser || !$targetUser) {
            throw new \RuntimeException("Both source and target user accounts must exist.");
        }

        $sourceAuthor = Database::fetch("SELECT * FROM authors WHERE user_id = ?", [$sourceUserId]);
        $targetAuthor = Database::fetch("SELECT * FROM authors WHERE user_id = ?", [$targetUserId]);

        $reassigned = [
            'contents' => 0,
            'comments' => 0,
            'bookmarks' => 0,
            'reading_history' => 0,
            'reactions' => 0,
            'notifications' => 0,
            'support_tickets' => 0,
            'user_subscriptions' => 0
        ];

        // 1. Reassign Contents (if both have author profiles, point contents to target author)
        if ($sourceAuthor && $targetAuthor) {
            $countRow = Database::fetch("SELECT COUNT(*) as c FROM contents WHERE author_id = ?", [$sourceAuthor['id']]);
            $reassigned['contents'] = (int)($countRow['c'] ?? 0);
            Database::execute("UPDATE contents SET author_id = ? WHERE author_id = ?", [$targetAuthor['id'], $sourceAuthor['id']]);
            
            // Reassign tools, gpts, prompts
            Database::execute("UPDATE ai_tools SET author_id = ? WHERE author_id = ?", [$targetAuthor['id'], $sourceAuthor['id']]);
            Database::execute("UPDATE gpt_listings SET author_id = ? WHERE author_id = ?", [$targetAuthor['id'], $sourceAuthor['id']]);
            Database::execute("UPDATE prompts SET author_id = ? WHERE author_id = ?", [$targetAuthor['id'], $sourceAuthor['id']]);
            Database::execute("UPDATE campaigns SET author_id = ? WHERE author_id = ?", [$targetAuthor['id'], $sourceAuthor['id']]);

            // Consolidate author stats
            $mergedViews = (int)$sourceAuthor['total_views'] + (int)$targetAuthor['total_views'];
            $mergedArticles = (int)$sourceAuthor['article_count'] + (int)$targetAuthor['article_count'];
            $mergedRep = (int)$sourceAuthor['reputation_score'] + (int)$targetAuthor['reputation_score'];
            $mergedCredits = (float)$sourceAuthor['promo_credits'] + (float)$targetAuthor['promo_credits'];
            Database::execute("UPDATE authors SET total_views = ?, article_count = ?, reputation_score = ?, promo_credits = ? WHERE id = ?", [
                $mergedViews, $mergedArticles, $mergedRep, $mergedCredits, $targetAuthor['id']
            ]);
        }

        // 2. Reassign Comments
        $commCount = (int)(Database::fetch("SELECT COUNT(*) as c FROM comments WHERE user_id = ?", [$sourceUserId])['c'] ?? 0);
        $reassigned['comments'] = $commCount;
        Database::execute("UPDATE comments SET user_id = ? WHERE user_id = ?", [$targetUserId, $sourceUserId]);

        // 3. Reassign Bookmarks (avoid duplicates)
        try {
            $sourceBookmarks = Database::query("SELECT content_id FROM bookmarks WHERE user_id = ?", [$sourceUserId]);
            foreach ($sourceBookmarks as $bm) {
                $exists = Database::fetch("SELECT id FROM bookmarks WHERE user_id = ? AND content_id = ?", [$targetUserId, $bm['content_id']]);
                if (!$exists) {
                    Database::execute("UPDATE bookmarks SET user_id = ? WHERE user_id = ? AND content_id = ?", [$targetUserId, $sourceUserId, $bm['content_id']]);
                    $reassigned['bookmarks']++;
                } else {
                    Database::execute("DELETE FROM bookmarks WHERE user_id = ? AND content_id = ?", [$sourceUserId, $bm['content_id']]);
                }
            }
        } catch (\Throwable $e) {}

        // 4. Reassign Reading History
        try {
            $sourceHist = Database::query("SELECT content_id FROM reading_history WHERE user_id = ?", [$sourceUserId]);
            foreach ($sourceHist as $rh) {
                $exists = Database::fetch("SELECT id FROM reading_history WHERE user_id = ? AND content_id = ?", [$targetUserId, $rh['content_id']]);
                if (!$exists) {
                    Database::execute("UPDATE reading_history SET user_id = ? WHERE user_id = ? AND content_id = ?", [$targetUserId, $sourceUserId, $rh['content_id']]);
                    $reassigned['reading_history']++;
                } else {
                    Database::execute("DELETE FROM reading_history WHERE user_id = ? AND content_id = ?", [$sourceUserId, $rh['content_id']]);
                }
            }
        } catch (\Throwable $e) {}

        // 5. Reassign Reactions
        try {
            $sourceReacts = Database::query("SELECT content_id, reaction_type FROM reactions WHERE user_id = ?", [$sourceUserId]);
            foreach ($sourceReacts as $sr) {
                $exists = Database::fetch("SELECT id FROM reactions WHERE user_id = ? AND content_id = ? AND reaction_type = ?", [$targetUserId, $sr['content_id'], $sr['reaction_type']]);
                if (!$exists) {
                    Database::execute("UPDATE reactions SET user_id = ? WHERE user_id = ? AND content_id = ?", [$targetUserId, $sourceUserId, $sr['content_id']]);
                    $reassigned['reactions']++;
                } else {
                    Database::execute("DELETE FROM reactions WHERE user_id = ? AND content_id = ?", [$sourceUserId, $sr['content_id']]);
                }
            }
        } catch (\Throwable $e) {}

        // 6. Reassign Support Tickets
        $ticketCount = (int)(Database::fetch("SELECT COUNT(*) as c FROM support_tickets WHERE user_id = ?", [$sourceUserId])['c'] ?? 0);
        $reassigned['support_tickets'] = $ticketCount;
        Database::execute("UPDATE support_tickets SET user_id = ? WHERE user_id = ?", [$targetUserId, $sourceUserId]);
        Database::execute("UPDATE ticket_messages SET user_id = ? WHERE user_id = ?", [$targetUserId, $sourceUserId]);

        // 7. Reassign Notifications
        $notifCount = (int)(Database::fetch("SELECT COUNT(*) as c FROM notifications WHERE user_id = ?", [$sourceUserId])['c'] ?? 0);
        $reassigned['notifications'] = $notifCount;
        Database::execute("UPDATE notifications SET user_id = ? WHERE user_id = ?", [$targetUserId, $sourceUserId]);

        // 8. Terminate source sessions and deactivate source account
        \App\Models\SessionManager::terminateOtherSessions($sourceUserId, null);
        $stamp = time();
        $archivedEmail = $sourceUser['email'] . ".merged_{$targetUserId}_{$stamp}";
        $archivedUsername = substr($sourceUser['username'], 0, 40) . "_m{$stamp}";
        Database::execute("UPDATE users SET status = 'deleted', email = ?, username = ? WHERE id = ?", [
            $archivedEmail, $archivedUsername, $sourceUserId
        ]);

        if ($sourceAuthor) {
            Database::execute("DELETE FROM authors WHERE id = ?", [$sourceAuthor['id']]);
        }

        AuditLog::log('admin.merge_users', 'users', $targetUserId, [
            'source_user_id' => $sourceUserId,
            'source_username' => $sourceUser['username'],
            'target_user_id' => $targetUserId,
            'target_username' => $targetUser['username'],
            'reassigned_summary' => $reassigned,
            'merged_by' => $adminId
        ]);

        return [
            'success' => true,
            'source_user' => $sourceUser,
            'target_user' => $targetUser,
            'reassigned' => $reassigned
        ];
    }

    /**
     * Generate 24-hour one-time password reset and quick login token
     */
    public static function generateQuickResetLink(int $userId): array {
        $user = Database::fetch("SELECT * FROM users WHERE id = ?", [$userId]);
        if (!$user) {
            throw new \RuntimeException("User not found.");
        }

        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        Database::execute(
            "UPDATE users SET password_reset_token = ?, password_reset_expires_at = ? WHERE id = ?",
            [$token, $expiresAt, $userId]
        );

        $resetUrl = Config::url('/reset-password?token=' . urlencode($token));
        $quickLoginUrl = Config::url('/auth/quick-login?token=' . urlencode($token));

        AuditLog::log('admin.generate_reset_link', 'users', $userId, [
            'user_id' => $userId,
            'expires_at' => $expiresAt
        ]);

        return [
            'success' => true,
            'token' => $token,
            'expires_at' => $expiresAt,
            'reset_url' => $resetUrl,
            'quick_login_url' => $quickLoginUrl,
            'username' => $user['username'],
            'email' => $user['email']
        ];
    }

    /**
     * Find user by valid unexpired reset token
     */
    public static function findValidResetToken(string $token): ?array {
        return Database::fetch(
            "SELECT * FROM users WHERE password_reset_token = ? AND password_reset_expires_at > NOW()",
            [$token]
        ) ?: null;
    }

    /**
     * Clear password reset token
     */
    public static function clearResetToken(int $userId): void {
        Database::execute(
            "UPDATE users SET password_reset_token = NULL, password_reset_expires_at = NULL WHERE id = ?",
            [$userId]
        );
    }

    /**
     * Flag user account for suspicious activity
     */
    public static function flagSuspicious(int $userId, string $reason): bool {
        Database::execute(
            "UPDATE users SET is_flagged_suspicious = 1, suspicious_reason = ?, flagged_at = NOW() WHERE id = ?",
            [$reason, $userId]
        );
        \App\Models\SecurityAnomalyEngine::logAnomaly(
            $userId,
            $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'rapid_requests',
            'high',
            "Suspicious activity auto-flag triggered: {$reason}",
            ['reason' => $reason]
        );
        return true;
    }

    /**
     * Clear suspicious flag
     */
    public static function clearSuspiciousFlag(int $userId): bool {
        Database::execute(
            "UPDATE users SET is_flagged_suspicious = 0, suspicious_reason = NULL, flagged_at = NULL WHERE id = ?",
            [$userId]
        );
        AuditLog::log('user.suspicious_cleared', 'users', $userId, ['user_id' => $userId]);
        return true;
    }

    /**
     * Compatibility alias for existing codebase
     */
    public static function addStrike(int $id, string $reason = ''): bool {
        self::issueStrike($id, $reason);
        return true;
    }

    /**
     * Item 16: Retrieve security anomalies for user
     */
    public static function getSecurityAnomalies(int $userId, int $limit = 20): array {
        return Database::query(
            "SELECT * FROM security_anomalies WHERE user_id = ? ORDER BY created_at DESC LIMIT " . (int)$limit,
            [$userId]
        );
    }

    /**
     * Item 16: Retrieve last logins, IP changes, user agents
     */
    public static function getRecentLogins(int $userId, int $limit = 20): array {
        return Database::query(
            "SELECT * FROM user_sessions WHERE user_id = ? ORDER BY last_active_at DESC LIMIT " . (int)$limit,
            [$userId]
        );
    }

    /**
     * Item 16: Security audit timeline
     */
    public static function getSecurityAuditTimeline(int $userId, int $limit = 20): array {
        return Database::query(
            "SELECT id, action, entity_type, entity_id, ip_address, user_agent, details, created_at 
             FROM audit_logs 
             WHERE (user_id = ?) OR (entity_type = 'user' AND entity_id = ?) OR (entity_type = 'users' AND entity_id = ?)
             ORDER BY created_at DESC LIMIT " . (int)$limit,
            [$userId, $userId, $userId]
        );
    }

    /**
     * Log security audit event for user
     */
    public static function logSecurityAudit(int $userId, string $action, string $entityType, int $entityId, array $details = []): void {
        AuditLog::log($action, $entityType, $entityId, array_merge(['user_id' => $userId], $details));
    }
}
