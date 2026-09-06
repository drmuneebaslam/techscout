<?php
namespace App\Models;

use App\Core\Database;
use App\Core\NotificationHelper;

class AuthorTierEngine {
    /**
     * Complete 20-Sublevel Tier Hierarchy Matrix
     */
    public static function getTierHierarchy(): array {
        return [
            // 1. Bronze League
            'bronze_1' => ['league' => 'Bronze', 'sublevel' => 1, 'level' => 1,  'name' => 'Bronze I (Initiate)',     'views_req' => 0,       'articles_req' => 0,  'rep_req' => 0,    'multiplier' => 1.00, 'color' => '#d97706', 'icon' => 'award'],
            'bronze_2' => ['league' => 'Bronze', 'sublevel' => 2, 'level' => 2,  'name' => 'Bronze II (Contributor)',  'views_req' => 500,     'articles_req' => 1,  'rep_req' => 25,   'multiplier' => 1.03, 'color' => '#d97706', 'icon' => 'award'],
            'bronze_3' => ['league' => 'Bronze', 'sublevel' => 3, 'level' => 3,  'name' => 'Bronze III (Practitioner)','views_req' => 1500,    'articles_req' => 2,  'rep_req' => 50,   'multiplier' => 1.06, 'color' => '#d97706', 'icon' => 'award'],
            'bronze_4' => ['league' => 'Bronze', 'sublevel' => 4, 'level' => 4,  'name' => 'Bronze IV (Rising Scout)', 'views_req' => 3000,    'articles_req' => 3,  'rep_req' => 100,  'multiplier' => 1.10, 'color' => '#d97706', 'icon' => 'award'],

            // 2. Silver League
            'silver_1' => ['league' => 'Silver', 'sublevel' => 1, 'level' => 5,  'name' => 'Silver I (Specialist)',    'views_req' => 6000,    'articles_req' => 5,  'rep_req' => 200,  'multiplier' => 1.15, 'color' => '#94a3b8', 'icon' => 'shield'],
            'silver_2' => ['league' => 'Silver', 'sublevel' => 2, 'level' => 6,  'name' => 'Silver II (Senior Scribe)', 'views_req' => 10000,  'articles_req' => 7,  'rep_req' => 350,  'multiplier' => 1.18, 'color' => '#94a3b8', 'icon' => 'shield'],
            'silver_3' => ['league' => 'Silver', 'sublevel' => 3, 'level' => 7,  'name' => 'Silver III (Technologist)','views_req' => 15000,   'articles_req' => 10, 'rep_req' => 500,  'multiplier' => 1.22, 'color' => '#94a3b8', 'icon' => 'shield'],
            'silver_4' => ['league' => 'Silver', 'sublevel' => 4, 'level' => 8,  'name' => 'Silver IV (Deep Diver)',   'views_req' => 22000,   'articles_req' => 12, 'rep_req' => 750,  'multiplier' => 1.28, 'color' => '#94a3b8', 'icon' => 'shield'],

            // 3. Gold League
            'gold_1'   => ['league' => 'Gold',   'sublevel' => 1, 'level' => 9,  'name' => 'Gold I (Architect)',       'views_req' => 35000,   'articles_req' => 15, 'rep_req' => 1000, 'multiplier' => 1.35, 'color' => '#f59e0b', 'icon' => 'zap'],
            'gold_2'   => ['league' => 'Gold',   'sublevel' => 2, 'level' => 10, 'name' => 'Gold II (Lead Author)',    'views_req' => 50000,   'articles_req' => 18, 'rep_req' => 1500, 'multiplier' => 1.40, 'color' => '#f59e0b', 'icon' => 'zap'],
            'gold_3'   => ['league' => 'Gold',   'sublevel' => 3, 'level' => 11, 'name' => 'Gold III (Master Builder)','views_req' => 75000,  'articles_req' => 22, 'rep_req' => 2000, 'multiplier' => 1.45, 'color' => '#f59e0b', 'icon' => 'zap'],
            'gold_4'   => ['league' => 'Gold',   'sublevel' => 4, 'level' => 12, 'name' => 'Gold IV (Distinguished)',  'views_req' => 100000, 'articles_req' => 25, 'rep_req' => 2800, 'multiplier' => 1.52, 'color' => '#f59e0b', 'icon' => 'zap'],

            // 4. Diamond League
            'diamond_1'=> ['league' => 'Diamond','sublevel' => 1, 'level' => 13, 'name' => 'Diamond I (Fellow)',        'views_req' => 150000, 'articles_req' => 30, 'rep_req' => 3800, 'multiplier' => 1.60, 'color' => '#38bdf8', 'icon' => 'gem'],
            'diamond_2'=> ['league' => 'Diamond','sublevel' => 2, 'level' => 14, 'name' => 'Diamond II (Principal)',   'views_req' => 200000, 'articles_req' => 35, 'rep_req' => 5000, 'multiplier' => 1.68, 'color' => '#38bdf8', 'icon' => 'gem'],
            'diamond_3'=> ['league' => 'Diamond','sublevel' => 3, 'level' => 15, 'name' => 'Diamond III (Luminary)',   'views_req' => 280000, 'articles_req' => 40, 'rep_req' => 6500, 'multiplier' => 1.78, 'color' => '#38bdf8', 'icon' => 'gem'],
            'diamond_4'=> ['league' => 'Diamond','sublevel' => 4, 'level' => 16, 'name' => 'Diamond IV (Grandmaster)', 'views_req' => 380000, 'articles_req' => 45, 'rep_req' => 8500, 'multiplier' => 1.90, 'color' => '#38bdf8', 'icon' => 'gem'],

            // 5. Partner Elite League
            'elite_1'  => ['league' => 'Elite',  'sublevel' => 1, 'level' => 17, 'name' => 'Partner Elite I (Legend)', 'views_req' => 500000, 'articles_req' => 50, 'rep_req' => 11000,'multiplier' => 2.00, 'color' => '#a855f7', 'icon' => 'crown'],
            'elite_2'  => ['league' => 'Elite',  'sublevel' => 2, 'level' => 18, 'name' => 'Partner Elite II (Icon)',  'views_req' => 700000, 'articles_req' => 60, 'rep_req' => 15000,'multiplier' => 2.15, 'color' => '#a855f7', 'icon' => 'crown'],
            'elite_3'  => ['league' => 'Elite',  'sublevel' => 3, 'level' => 19, 'name' => 'Partner Elite III (Titan)', 'views_req' => 1000000,'articles_req' => 75, 'rep_req' => 20000,'multiplier' => 2.30, 'color' => '#a855f7', 'icon' => 'crown'],
            'elite_4'  => ['league' => 'Elite',  'sublevel' => 4, 'level' => 20, 'name' => 'Partner Elite IV (Apex)',  'views_req' => 1500000,'articles_req' => 100,'rep_req' => 30000,'multiplier' => 2.50, 'color' => '#a855f7', 'icon' => 'crown'],
        ];
    }

    public static function getTierProgression(): array {
        return self::getTierHierarchy();
    }

    /**
     * Compute and evaluate author's current ranking tier
     */
    public static function evaluateAuthorTier(int $authorId): array {
        $author = Database::fetch("SELECT * FROM authors WHERE id = ?", [$authorId]);
        if (!$author) return [];

        $views = (int)($author['total_views'] ?? 0);
        $articles = (int)($author['article_count'] ?? 0);
        $rep = (int)($author['reputation_score'] ?? 0);

        $hierarchy = self::getTierHierarchy();
        $bestKey = 'bronze_1';

        foreach ($hierarchy as $key => $t) {
            if ($views >= $t['views_req'] && $articles >= $t['articles_req'] && $rep >= $t['rep_req']) {
                $bestKey = $key;
            }
        }

        $curTier = $hierarchy[$bestKey];

        // Find next tier if available
        $allKeys = array_keys($hierarchy);
        $curIdx = array_search($bestKey, $allKeys, true);
        $nextTier = isset($allKeys[$curIdx + 1]) ? $hierarchy[$allKeys[$curIdx + 1]] : null;

        // Check if tier changed
        $oldTierCode = $author['current_tier_code'] ?? 'bronze_1';
        if ($oldTierCode !== $bestKey) {
            Database::execute(
                "UPDATE authors SET current_tier_code = ?, tier_sublevel = ?, tier_multiplier = ? WHERE id = ?",
                [$bestKey, $curTier['sublevel'], $curTier['multiplier'], $authorId]
            );

            Database::execute(
                "INSERT INTO author_tier_history (author_id, tier_code, tier_level, tier_title, cpm_multiplier, unlocked_at)
                 VALUES (?, ?, ?, ?, ?, NOW())",
                [$authorId, $bestKey, $curTier['level'], $curTier['name'], $curTier['multiplier']]
            );

            NotificationHelper::send(
                (int)$author['user_id'],
                'reward',
                "🏆 Tier Rank Up: You Reached {$curTier['name']}!",
                "Congratulations! Your readership has elevated your creator status to {$curTier['name']}.",
                "/author/dashboard"
            );
        }

        return [
            'tier_key' => $bestKey,
            'current' => $curTier,
            'next' => $nextTier,
            'progress' => self::calculateProgress($views, $articles, $rep, $curTier, $nextTier)
        ];
    }

    private static function calculateProgress(int $views, int $articles, int $rep, array $cur, ?array $next): int {
        if (!$next) return 100;
        $viewsRatio = ($next['views_req'] > $cur['views_req']) ? min(1, ($views - $cur['views_req']) / ($next['views_req'] - $cur['views_req'])) : 1;
        $artRatio = ($next['articles_req'] > $cur['articles_req']) ? min(1, ($articles - $cur['articles_req']) / ($next['articles_req'] - $cur['articles_req'])) : 1;
        $repRatio = ($next['rep_req'] > $cur['rep_req']) ? min(1, ($rep - $cur['rep_req']) / ($next['rep_req'] - $cur['rep_req'])) : 1;

        return (int)round((($viewsRatio + $artRatio + $repRatio) / 3) * 100);
    }

    /**
     * Render an Adaptive, High-Contrast Dual-Theme Badge HTML
     * Works with crisp contrast on BOTH Light Mode and Dark Mode!
     */
    public static function renderAdaptiveBadge(string $tierKey, string $customClass = ''): string {
        $hierarchy = self::getTierHierarchy();
        $t = $hierarchy[$tierKey] ?? $hierarchy['bronze_1'];

        $league = strtolower($t['league']);
        $roman = match($t['sublevel']) {
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', default => 'I'
        };

        // Theme palette definitions with high-contrast light mode text & crisp borders
        $styles = match($league) {
            'bronze' => [
                'icon' => '🥉',
                'bg_dark' => 'rgba(217, 119, 6, 0.15)',
                'border_dark' => 'rgba(217, 119, 6, 0.45)',
                'text_dark' => '#fbbf24',
                'bg_light' => '#fef3c7',
                'border_light' => '#d97706',
                'text_light' => '#92400e'
            ],
            'silver' => [
                'icon' => '🥈',
                'bg_dark' => 'rgba(148, 163, 184, 0.15)',
                'border_dark' => 'rgba(148, 163, 184, 0.45)',
                'text_dark' => '#f1f5f9',
                'bg_light' => '#f1f5f9',
                'border_light' => '#64748b',
                'text_light' => '#1e293b'
            ],
            'gold' => [
                'icon' => '🥇',
                'bg_dark' => 'rgba(245, 158, 11, 0.18)',
                'border_dark' => 'rgba(245, 158, 11, 0.55)',
                'text_dark' => '#fde047',
                'bg_light' => '#fef9c3',
                'border_light' => '#ca8a04',
                'text_light' => '#854d0e'
            ],
            'diamond' => [
                'icon' => '💎',
                'bg_dark' => 'rgba(56, 189, 248, 0.18)',
                'border_dark' => 'rgba(56, 189, 248, 0.55)',
                'text_dark' => '#38bdf8',
                'bg_light' => '#e0f2fe',
                'border_light' => '#0284c7',
                'text_light' => '#0369a1'
            ],
            'elite' => [
                'icon' => '👑',
                'bg_dark' => 'rgba(168, 85, 247, 0.22)',
                'border_dark' => 'rgba(168, 85, 247, 0.65)',
                'text_dark' => '#c084fc',
                'bg_light' => '#f3e8ff',
                'border_light' => '#9333ea',
                'text_light' => '#6b21a8'
            ],
            default => [
                'icon' => '🥉',
                'bg_dark' => 'rgba(217, 119, 6, 0.15)',
                'border_dark' => 'rgba(217, 119, 6, 0.45)',
                'text_dark' => '#fbbf24',
                'bg_light' => '#fef3c7',
                'border_light' => '#d97706',
                'text_light' => '#92400e'
            ]
        };

        $badgeId = 'ts_badge_' . uniqid();

        $html = '<span id="' . $badgeId . '" class="ts-author-tier-badge ' . htmlspecialchars($customClass) . '" title="' . htmlspecialchars($t['name']) . '" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.2rem 0.55rem; border-radius: 999px; font-size: 0.72rem; font-weight: 800; letter-spacing: 0.03em; text-transform: uppercase; font-family: var(--ts-font-sans, sans-serif); line-height: 1; vertical-align: middle; transition: all 0.2s ease; border-width: 1.5px; border-style: solid;">';
        $html .= '<span style="font-size: 0.85rem; line-height: 1;">' . $styles['icon'] . '</span> ';
        $html .= '<span>' . htmlspecialchars($t['league']) . ' ' . $roman . '</span>';
        $html .= '</span>';

        $html .= '<style>
        #' . $badgeId . ' {
          background: ' . $styles['bg_dark'] . ';
          border-color: ' . $styles['border_dark'] . ';
          color: ' . $styles['text_dark'] . ';
          box-shadow: 0 0 10px ' . $styles['bg_dark'] . ';
        }
        [data-theme="light"] #' . $badgeId . ', body.light-mode #' . $badgeId . ' {
          background: ' . $styles['bg_light'] . ' !important;
          border-color: ' . $styles['border_light'] . ' !important;
          color: ' . $styles['text_light'] . ' !important;
          box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
        }
        </style>';

        return $html;
    }
}
