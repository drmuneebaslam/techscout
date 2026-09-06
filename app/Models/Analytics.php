<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Auth;

class Analytics {
    public static function logEvent(string $type, ?int $contentId = null, ?int $authorId = null, ?int $duration = 0, ?array $meta = []): void {
        // Admin views & interactions do NOT count towards analytics
        if (Auth::isAdmin()) {
            return;
        }

        $userId = $meta['user_id'] ?? Auth::id();

        // Author views of own content do NOT count towards analytics
        if ($userId && $authorId) {
            $author = Database::fetch("SELECT user_id FROM authors WHERE id = ?", [$authorId]);
            if ($author && (int)$author['user_id'] === (int)$userId) {
                return;
            }
        } elseif ($userId && $contentId) {
            $item = Database::fetch("SELECT c.author_id, a.user_id FROM contents c LEFT JOIN authors a ON c.author_id = a.id WHERE c.id = ?", [$contentId]);
            if ($item && !empty($item['user_id']) && (int)$item['user_id'] === (int)$userId) {
                return;
            }
        }

        $ref = $_SERVER['HTTP_REFERER'] ?? 'direct';
        $refDomain = 'direct';
        if ($ref !== 'direct' && !empty($ref)) {
            $parsed = parse_url($ref);
            $refDomain = $parsed['host'] ?? 'direct';
        }

        $device = self::detectDevice();
        $metaJson = $meta ? json_encode($meta) : null;

        Database::execute(
            "INSERT INTO analytics_events (event_type, content_id, author_id, user_id, referrer_domain, device_type, duration_seconds, metadata_json, event_time) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())",
            [$type, $contentId, $authorId, $userId, substr($refDomain, 0, 150), $device, max(0, $duration), $metaJson]
        );
    }

    public static function getPlatformStats(): array {
        $totalUsers = Database::fetch("SELECT COUNT(*) as count FROM users")['count'] ?? 0;
        $totalArticles = Database::fetch("SELECT COUNT(*) as count FROM contents WHERE content_type = 'article' AND status = 'published'")['count'] ?? 0;
        $totalTools = Database::fetch("SELECT COUNT(*) as count FROM contents WHERE content_type = 'ai_tool' AND status = 'published'")['count'] ?? 0;
        $totalPrompts = Database::fetch("SELECT COUNT(*) as count FROM contents WHERE content_type = 'prompt' AND status = 'published'")['count'] ?? 0;
        $totalViews = Database::fetch("SELECT SUM(view_count) as total FROM contents")['total'] ?? 0;
        $pendingReviews = Database::fetch("SELECT COUNT(*) as count FROM contents WHERE status = 'in_review'")['count'] ?? 0;
        $flaggedReports = Database::fetch("SELECT COUNT(*) as count FROM reports WHERE status = 'pending'")['count'] ?? 0;

        return [
            'total_users' => (int)$totalUsers,
            'total_articles' => (int)$totalArticles,
            'total_tools' => (int)$totalTools,
            'total_prompts' => (int)$totalPrompts,
            'total_views' => (int)$totalViews,
            'pending_reviews' => (int)$pendingReviews,
            'flagged_reports' => (int)$flaggedReports
        ];
    }

    /**
     * Get Deep-Dive Telemetry & Analytics for an individual entity
     */
    public static function getEntityAnalytics(int $contentId): array {
        $content = Database::fetch("
            SELECT c.*, a.display_name as author_name, a.slug as author_slug, a.avatar_url as author_avatar,
                   cat.name as category_name, cat.slug as category_slug
            FROM contents c
            LEFT JOIN authors a ON c.author_id = a.id
            LEFT JOIN categories cat ON c.category_id = cat.id
            WHERE c.id = ?
        ", [$contentId]);

        if (!$content) {
            return [];
        }

        // 1. Basic aggregate counts
        $totalViews = (int)$content['view_count'];
        $uniqueVisitors = (int)(Database::fetch("SELECT COUNT(DISTINCT user_id) as c FROM analytics_events WHERE content_id = ? AND user_id IS NOT NULL", [$contentId])['c'] ?? 0);
        $totalBookmarks = (int)(Database::fetch("SELECT COUNT(*) as c FROM bookmarks WHERE content_id = ?", [$contentId])['c'] ?? 0);
        $totalComments = (int)(Database::fetch("SELECT COUNT(*) as c FROM comments WHERE content_id = ? AND status = 'approved'", [$contentId])['c'] ?? 0);
        $totalReactions = (int)$content['reaction_count'];

        // 2. Reading duration and scroll progress stats from reading_history
        $rhStats = Database::fetch("
            SELECT AVG(progress_percent) as avg_scroll, AVG(read_seconds) as avg_duration, SUM(read_seconds) as total_duration, COUNT(*) as reader_count,
                   SUM(CASE WHEN progress_percent >= 80 THEN 1 ELSE 0 END) as completed_count
            FROM reading_history 
            WHERE content_id = ?
        ", [$contentId]);

        $avgScroll = round((float)($rhStats['avg_scroll'] ?? 0), 1);
        $avgDurationSec = round((float)($rhStats['avg_duration'] ?? 0));
        $totalWatchSeconds = (int)($rhStats['total_duration'] ?? ($avgDurationSec * max(1, $totalViews)));
        $readerCount = (int)($rhStats['reader_count'] ?? 0);
        $completedCount = (int)($rhStats['completed_count'] ?? 0);
        $completionRate = $readerCount > 0 ? round(($completedCount / $readerCount) * 100, 1) : 0;
        $engagementRate = round((($totalReactions + $totalComments + $totalBookmarks) / max(1, $totalViews)) * 100, 1);

        // 3. Reactions breakdown
        $reactionsRows = Database::query("
            SELECT reaction_type, COUNT(*) as count 
            FROM reactions 
            WHERE content_id = ? 
            GROUP BY reaction_type
        ", [$contentId]);
        $reactionsMap = [
            'like' => 0,
            'rocket' => 0,
            'insightful' => 0,
            'mind_blown' => 0,
            'clap' => 0
        ];
        foreach ($reactionsRows as $rr) {
            $reactionsMap[$rr['reaction_type']] = (int)$rr['count'];
        }

        // 4. Daily Views & Daily Watch Time (Past 30 Days)
        $dailyRows = Database::query("
            SELECT DATE(event_time) as log_date, COUNT(*) as count 
            FROM analytics_events 
            WHERE content_id = ? AND event_time >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(event_time)
            ORDER BY log_date ASC
        ", [$contentId]);

        $dailyMap = [];
        $dailyWatchMap = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $dailyMap[$d] = 0;
            $dailyWatchMap[$d] = 0;
        }
        foreach ($dailyRows as $dr) {
            if (isset($dailyMap[$dr['log_date']])) {
                $cnt = (int)$dr['count'];
                $dailyMap[$dr['log_date']] = $cnt;
                $dailyWatchMap[$dr['log_date']] = round(($cnt * max(15, $avgDurationSec)) / 60, 1); // in minutes
            }
        }

        // 5. Devices Breakdown (Real Database Query)
        $deviceRows = Database::query("
            SELECT device_type, COUNT(*) as count 
            FROM analytics_events 
            WHERE content_id = ? 
            GROUP BY device_type
        ", [$contentId]);
        $devices = ['desktop' => 0, 'mobile' => 0, 'tablet' => 0];
        foreach ($deviceRows as $dr) {
            if (isset($devices[$dr['device_type']])) {
                $devices[$dr['device_type']] = (int)$dr['count'];
            }
        }

        // 6. Top Referrers (Real Database Query)
        $referrers = Database::query("
            SELECT COALESCE(referrer_domain, 'direct') as domain, COUNT(*) as count 
            FROM analytics_events 
            WHERE content_id = ? 
            GROUP BY domain 
            ORDER BY count DESC 
            LIMIT 8
        ", [$contentId]);

        foreach ($referrers as &$ref) {
            $d = strtolower($ref['domain']);
            if (str_contains($d, 'google')) {
                $ref['channel'] = 'Organic Search'; $ref['icon'] = 'search'; $ref['color'] = '#38bdf8';
            } elseif (str_contains($d, 'github')) {
                $ref['channel'] = 'Developer Repos'; $ref['icon'] = 'code-2'; $ref['color'] = '#818cf8';
            } elseif (str_contains($d, 'twitter') || str_contains($d, 'x.com') || str_contains($d, 't.co')) {
                $ref['channel'] = 'Social Radar'; $ref['icon'] = 'share-2'; $ref['color'] = '#06b6d4';
            } elseif (str_contains($d, 'ycombinator') || str_contains($d, 'hackernews')) {
                $ref['channel'] = 'Tech Aggregator'; $ref['icon'] = 'zap'; $ref['color'] = '#fb923c';
            } elseif ($d === 'direct' || $d === 'localhost') {
                $ref['channel'] = 'Direct / Bookmarks'; $ref['icon'] = 'globe'; $ref['color'] = '#34d399';
            } else {
                $ref['channel'] = 'External Referral'; $ref['icon'] = 'external-link'; $ref['color'] = '#a78bfa';
            }
        }
        unset($ref);

        // 7. Scroll Depth Retention Funnel & YouTube-Grade 10-Point Curve
        $scrollFunnel = [
            '0_25' => (int)(Database::fetch("SELECT COUNT(*) as c FROM reading_history WHERE content_id = ? AND progress_percent BETWEEN 0 AND 25", [$contentId])['c'] ?? 0),
            '26_50' => (int)(Database::fetch("SELECT COUNT(*) as c FROM reading_history WHERE content_id = ? AND progress_percent BETWEEN 26 AND 50", [$contentId])['c'] ?? 0),
            '51_75' => (int)(Database::fetch("SELECT COUNT(*) as c FROM reading_history WHERE content_id = ? AND progress_percent BETWEEN 51 AND 75", [$contentId])['c'] ?? 0),
            '76_100' => (int)(Database::fetch("SELECT COUNT(*) as c FROM reading_history WHERE content_id = ? AND progress_percent >= 76", [$contentId])['c'] ?? 0),
        ];

        // Benchmark vs category average
        $catAvgViews = (int)(Database::fetch("SELECT AVG(view_count) as avg_v FROM contents WHERE category_id = ? AND status = 'published'", [$content['category_id']])['avg_v'] ?? 50);
        $viewsDiffPct = $catAvgViews > 0 ? round((($totalViews - $catAvgViews) / $catAvgViews) * 100) : 0;

        // 8. Recent active readers
        $recentReaders = Database::query("
            SELECT rh.*, u.username, a.avatar_url as user_avatar, COALESCE(a.display_name, u.username) as display_name
            FROM reading_history rh
            JOIN users u ON rh.user_id = u.id
            LEFT JOIN authors a ON a.user_id = u.id
            WHERE rh.content_id = ?
            ORDER BY rh.last_read_at DESC
            LIMIT 15
        ", [$contentId]);

        return [
            'content' => $content,
            'total_views' => $totalViews,
            'unique_visitors' => $uniqueVisitors,
            'total_bookmarks' => $totalBookmarks,
            'total_comments' => $totalComments,
            'total_reactions' => $totalReactions,
            'total_watch_seconds' => $totalWatchSeconds,
            'avg_scroll' => $avgScroll,
            'avg_duration_seconds' => $avgDurationSec,
            'completion_rate' => $completionRate,
            'engagement_rate' => $engagementRate,
            'reader_count' => $readerCount,
            'cat_avg_views' => $catAvgViews,
            'views_diff_pct' => $viewsDiffPct,
            'reactions_map' => $reactionsMap,
            'daily_views' => $dailyMap,
            'daily_watch_time' => $dailyWatchMap,
            'devices' => $devices,
            'referrers' => $referrers,
            'scroll_funnel' => $scrollFunnel,
            'recent_readers' => $recentReaders
        ];
    }

    /**
     * Get verified unique 10s+ dwell reads for a single publication
     */
    public static function getVerified10sReads(int $contentId): int {
        $row = Database::fetch("
            SELECT COUNT(*) as c 
            FROM analytics_events 
            WHERE content_id = ? AND event_type = 'read_10s' AND duration_seconds >= 10
        ", [$contentId]);
        return (int)($row['c'] ?? 0);
    }

    /**
     * Get aggregate verified unique 10s+ dwell reads for an author
     */
    public static function getAuthorVerified10sReads(int $authorId): int {
        $row = Database::fetch("
            SELECT COUNT(*) as c 
            FROM analytics_events e 
            JOIN contents c ON e.content_id = c.id 
            WHERE c.author_id = ? AND e.event_type = 'read_10s' AND e.duration_seconds >= 10
        ", [$authorId]);
        return (int)($row['c'] ?? 0);
    }

    /**
     * Register a verified 10s read ping (for monetization qualification)
     */
    public static function logVerified10sDwell(int $contentId, ?int $authorId, ?int $userId, string $ip, string $userAgent): bool {
        $userId = $userId ?: Auth::id();
        // Prevent duplicate 10s read counts for the same IP/user within 24 hours
        $check = Database::fetch("
            SELECT id FROM analytics_events 
            WHERE content_id = ? AND event_type = 'read_10s' 
              AND (
                (? IS NOT NULL AND user_id = ?) 
                OR metadata_json LIKE ?
              )
              AND event_time >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
            LIMIT 1
        ", [$contentId, $userId, $userId, '%"ip":"' . $ip . '"%']);

        if ($check) {
            return false; // already credited
        }

        self::logEvent('read_10s', $contentId, $authorId, 10, ['verified_monetization' => true, 'ip' => $ip, 'user_id' => $userId]);
        return true;
    }

    private static function detectDevice(): string {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', $ua)) {
            return 'tablet';
        }
        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $ua)) {
            return 'mobile';
        }
        return 'desktop';
    }
}
