<?php
namespace App\Core;

class NotificationHelper {
    /**
     * Dispatch an in-app notification to a user.
     */
    public static function send(int $userId, string $type, string $title, string $message, ?string $actionUrl = null, array $metadata = []): bool {
        if ($userId <= 0) return false;
        try {
            if ($actionUrl) {
                // Strip stale /tech/ subfolder
                $actionUrl = preg_replace('#^https?://[^/]+/tech(/|$)#i', '/', $actionUrl);
                if (str_starts_with($actionUrl, '/tech/')) {
                    $actionUrl = substr($actionUrl, 5);
                } elseif ($actionUrl === '/tech') {
                    $actionUrl = '/';
                }
            }
            $metaJson = !empty($metadata) ? json_encode($metadata) : null;
            $eventType = !empty($metadata['event_type']) ? (string)$metadata['event_type'] : ($type ?: 'system');
            Database::execute("INSERT INTO notifications (user_id, type, event_type, title, message, action_url, is_read, created_at) VALUES (?, ?, ?, ?, ?, ?, 0, NOW())", [
                $userId, $type, $eventType, $title, $message, $actionUrl
            ]);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Alias for send() with support for flexible arguments.
     */
    public static function dispatch(int $userId, string $type, string $title, string $message, ?string $actionUrl = null, array $metadata = []): bool {
        return self::send($userId, $type, $title, $message, $actionUrl, $metadata);
    }

    public static function getUnreadCount(int $userId): int {
        if ($userId <= 0) return 0;
        try {
            $row = Database::fetch("SELECT COUNT(*) as c FROM notifications WHERE user_id = ? AND is_read = 0", [$userId]);
            return (int)($row['c'] ?? 0);
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public static function getCountsByFilter(int $userId): array {
        if ($userId <= 0) {
            return ['all' => 0, 'unread' => 0, 'replies' => 0, 'system' => 0, 'rewards' => 0, 'tickets' => 0, 'editorial' => 0];
        }
        try {
            $all = (int)(Database::fetch("SELECT COUNT(*) as c FROM notifications WHERE user_id = ?", [$userId])['c'] ?? 0);
            $unread = (int)(Database::fetch("SELECT COUNT(*) as c FROM notifications WHERE user_id = ? AND is_read = 0", [$userId])['c'] ?? 0);
            $replies = (int)(Database::fetch("SELECT COUNT(*) as c FROM notifications WHERE user_id = ? AND type IN ('reply', 'comment', 'mention')", [$userId])['c'] ?? 0);
            $system = (int)(Database::fetch("SELECT COUNT(*) as c FROM notifications WHERE user_id = ? AND type IN ('system', 'approval', 'moderation', 'security', 'login_alert')", [$userId])['c'] ?? 0);
            $rewards = (int)(Database::fetch("SELECT COUNT(*) as c FROM notifications WHERE user_id = ? AND type IN ('reward', 'follow', 'milestone', 'payout', 'monetization')", [$userId])['c'] ?? 0);
            $tickets = (int)(Database::fetch("SELECT COUNT(*) as c FROM notifications WHERE user_id = ? AND type IN ('ticket', 'ticket_reply', 'support')", [$userId])['c'] ?? 0);
            $editorial = (int)(Database::fetch("SELECT COUNT(*) as c FROM notifications WHERE user_id = ? AND type IN ('editorial_feedback', 'article_approved', 'article_rejected', 'changes_required')", [$userId])['c'] ?? 0);

            return [
                'all' => $all,
                'unread' => $unread,
                'replies' => $replies,
                'system' => $system,
                'rewards' => $rewards,
                'tickets' => $tickets,
                'editorial' => $editorial
            ];
        } catch (\Throwable $e) {
            return ['all' => 0, 'unread' => 0, 'replies' => 0, 'system' => 0, 'rewards' => 0, 'tickets' => 0, 'editorial' => 0];
        }
    }

    public static function getPaginated(int $userId, int $page = 1, int $perPage = 25, ?string $filter = null, ?string $search = null): array {
        if ($userId <= 0) {
            return ['items' => [], 'total' => 0, 'page' => 1, 'perPage' => $perPage, 'totalPages' => 1, 'unreadCount' => 0];
        }

        $page = max(1, $page);
        $perPage = max(1, min(500, $perPage));
        $offset = ($page - 1) * $perPage;

        $whereClause = "WHERE user_id = ?";
        $params = [$userId];

        if ($filter === 'unread') {
            $whereClause .= " AND is_read = 0";
        } elseif ($filter === 'replies') {
            $whereClause .= " AND type IN ('reply', 'comment', 'mention')";
        } elseif ($filter === 'system') {
            $whereClause .= " AND type IN ('system', 'approval', 'moderation', 'security', 'login_alert')";
        } elseif ($filter === 'rewards') {
            $whereClause .= " AND type IN ('reward', 'follow', 'milestone', 'payout', 'monetization')";
        } elseif ($filter === 'tickets') {
            $whereClause .= " AND type IN ('ticket', 'ticket_reply', 'support')";
        } elseif ($filter === 'editorial') {
            $whereClause .= " AND type IN ('editorial_feedback', 'article_approved', 'article_rejected', 'changes_required')";
        }

        if (!empty($search)) {
            $whereClause .= " AND (title LIKE ? OR message LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        try {
            $totalRow = Database::fetch("SELECT COUNT(*) as c FROM notifications {$whereClause}", $params);
            $total = (int)($totalRow['c'] ?? 0);
            $totalPages = max(1, (int)ceil($total / $perPage));

            $items = Database::query("SELECT * FROM notifications {$whereClause} ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}", $params) ?: [];
            
            // Enrich with UI metadata
            foreach ($items as &$item) {
                $meta = self::getTypeMeta($item['type'] ?? 'system');
                $item['icon'] = $meta['icon'];
                $item['badge_class'] = $meta['badge_class'];
                $item['type_label'] = $meta['type_label'];
                $item['action_label'] = $meta['action_label'];
                $item['color'] = $meta['color'];
                $item['relative_time'] = self::formatRelativeTime($item['created_at'] ?? '');
            }

            $unreadCount = self::getUnreadCount($userId);

            return [
                'items' => $items,
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
                'totalPages' => $totalPages,
                'unreadCount' => $unreadCount
            ];
        } catch (\Throwable $e) {
            return ['items' => [], 'total' => 0, 'page' => 1, 'perPage' => $perPage, 'totalPages' => 1, 'unreadCount' => 0];
        }
    }

    public static function getTypeMeta(string $type): array {
        switch ($type) {
            case 'ticket_reply':
            case 'ticket':
            case 'support':
                return [
                    'icon' => 'life-buoy',
                    'badge_class' => 'ts-badge-amber',
                    'type_label' => 'SUPPORT TICKET',
                    'action_label' => 'View Ticket 🎫',
                    'color' => '#f59e0b',
                    'bg_glow' => 'rgba(245, 158, 11, 0.15)'
                ];
            case 'moderation':
                return [
                    'icon' => 'shield-alert',
                    'badge_class' => 'ts-badge-purple',
                    'type_label' => 'CONTENT REPORT',
                    'action_label' => 'View Report 🛡️',
                    'color' => '#a855f7',
                    'bg_glow' => 'rgba(168, 85, 247, 0.15)'
                ];
            case 'editorial_feedback':
            case 'article_rejected':
            case 'changes_required':
                return [
                    'icon' => 'file-edit',
                    'badge_class' => 'ts-badge-pink',
                    'type_label' => 'EDITORIAL REVIEW',
                    'action_label' => 'Open in Studio ✍️',
                    'color' => '#ec4899',
                    'bg_glow' => 'rgba(236, 72, 153, 0.15)'
                ];
            case 'article_approved':
            case 'approval':
                return [
                    'icon' => 'check-circle',
                    'badge_class' => 'ts-badge-green',
                    'type_label' => 'PUBLISHED',
                    'action_label' => 'View Live Post 🚀',
                    'color' => '#10b981',
                    'bg_glow' => 'rgba(16, 185, 129, 0.15)'
                ];
            case 'reply':
                return [
                    'icon' => 'message-square',
                    'badge_class' => 'ts-badge-cyan',
                    'type_label' => 'REPLY',
                    'action_label' => 'View Reply 💬',
                    'color' => '#06b6d4',
                    'bg_glow' => 'rgba(6, 182, 212, 0.15)'
                ];
            case 'comment':
            case 'mention':
                return [
                    'icon' => 'message-circle',
                    'badge_class' => 'ts-badge-blue',
                    'type_label' => 'COMMENT',
                    'action_label' => 'View Discussion 💬',
                    'color' => '#38bdf8',
                    'bg_glow' => 'rgba(56, 189, 248, 0.15)'
                ];
            case 'reward':
            case 'milestone':
            case 'payout':
            case 'monetization':
                return [
                    'icon' => 'award',
                    'badge_class' => 'ts-badge-gold',
                    'type_label' => 'REWARD & REVENUE',
                    'action_label' => 'View Rewards 🏆',
                    'color' => '#eab308',
                    'bg_glow' => 'rgba(234, 179, 8, 0.15)'
                ];
            case 'follow':
                return [
                    'icon' => 'user-plus',
                    'badge_class' => 'ts-badge-emerald',
                    'type_label' => 'NEW FOLLOWER',
                    'action_label' => 'View Creator 👤',
                    'color' => '#10b981',
                    'bg_glow' => 'rgba(16, 185, 129, 0.15)'
                ];
            case 'security':
            case 'login_alert':
                return [
                    'icon' => 'shield-check',
                    'badge_class' => 'ts-badge-pink',
                    'type_label' => 'SECURITY ALERT',
                    'action_label' => 'Review Security 🔒',
                    'color' => '#f43f5e',
                    'bg_glow' => 'rgba(244, 63, 94, 0.15)'
                ];
            default:
                return [
                    'icon' => 'bell',
                    'badge_class' => 'ts-badge-blue',
                    'type_label' => 'SYSTEM ALERT',
                    'action_label' => 'View Details ↗',
                    'color' => '#818cf8',
                    'bg_glow' => 'rgba(129, 140, 248, 0.15)'
                ];
        }
    }

    public static function formatRelativeTime(?string $datetime): string {
        if (empty($datetime)) return '';
        $time = strtotime($datetime);
        if (!$time) return $datetime;

        $diff = time() - $time;
        if ($diff < 45) {
            return 'Just now';
        } elseif ($diff < 3600) {
            $mins = max(1, (int)round($diff / 60));
            return "{$mins}m ago";
        } elseif ($diff < 86400) {
            $hours = (int)round($diff / 3600);
            return "{$hours}h ago";
        } elseif ($diff < 172800) {
            return 'Yesterday at ' . DateHelper::formatTime($datetime);
        } elseif ($diff < 604800) {
            $days = (int)round($diff / 86400);
            return "{$days}d ago";
        } else {
            return DateHelper::formatDateTime($datetime);
        }
    }

    public static function getRecent(int $userId, int $limit = 20, ?string $filter = null): array {
        $res = self::getPaginated($userId, 1, $limit, $filter);
        return $res['items'];
    }

    public static function markAsRead(int $id, int $userId): bool {
        try {
            Database::execute("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?", [$id, $userId]);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function toggleRead(int $id, int $userId): bool {
        try {
            $item = Database::fetch("SELECT is_read FROM notifications WHERE id = ? AND user_id = ?", [$id, $userId]);
            if (!$item) return false;
            $newVal = (int)$item['is_read'] === 1 ? 0 : 1;
            Database::execute("UPDATE notifications SET is_read = ? WHERE id = ? AND user_id = ?", [$newVal, $id, $userId]);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function markAllAsRead(int $userId, ?string $filter = null): bool {
        try {
            $whereClause = "WHERE user_id = ?";
            $params = [$userId];

            if ($filter === 'replies') {
                $whereClause .= " AND type IN ('reply', 'comment', 'mention')";
            } elseif ($filter === 'system') {
                $whereClause .= " AND type IN ('system', 'approval', 'moderation', 'security', 'login_alert')";
            } elseif ($filter === 'rewards') {
                $whereClause .= " AND type IN ('reward', 'follow', 'milestone', 'payout', 'monetization')";
            } elseif ($filter === 'tickets') {
                $whereClause .= " AND type IN ('ticket', 'ticket_reply', 'support')";
            } elseif ($filter === 'editorial') {
                $whereClause .= " AND type IN ('editorial_feedback', 'article_approved', 'article_rejected', 'changes_required')";
            }

            Database::execute("UPDATE notifications SET is_read = 1 {$whereClause}", $params);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function delete(int $id, int $userId): bool {
        try {
            Database::execute("DELETE FROM notifications WHERE id = ? AND user_id = ?", [$id, $userId]);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function deleteBatch(array $ids, int $userId): bool {
        if (empty($ids) || $userId <= 0) return false;
        try {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $params = array_merge([(int)$userId], array_map('intval', $ids));
            Database::execute("DELETE FROM notifications WHERE user_id = ? AND id IN ({$placeholders})", $params);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function markBatchRead(array $ids, int $userId): bool {
        if (empty($ids) || $userId <= 0) return false;
        try {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $params = array_merge([(int)$userId], array_map('intval', $ids));
            Database::execute("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND id IN ({$placeholders})", $params);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function clearAll(int $userId, ?string $filter = null): bool {
        try {
            $whereClause = "WHERE user_id = ?";
            $params = [$userId];

            if ($filter === 'unread') {
                $whereClause .= " AND is_read = 0";
            } elseif ($filter === 'replies') {
                $whereClause .= " AND type IN ('reply', 'comment', 'mention')";
            } elseif ($filter === 'system') {
                $whereClause .= " AND type IN ('system', 'approval', 'moderation', 'security', 'login_alert')";
            } elseif ($filter === 'rewards') {
                $whereClause .= " AND type IN ('reward', 'follow', 'milestone', 'payout', 'monetization')";
            } elseif ($filter === 'tickets') {
                $whereClause .= " AND type IN ('ticket', 'ticket_reply', 'support')";
            } elseif ($filter === 'editorial') {
                $whereClause .= " AND type IN ('editorial_feedback', 'article_approved', 'article_rejected', 'changes_required')";
            }

            Database::execute("DELETE FROM notifications {$whereClause}", $params);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
