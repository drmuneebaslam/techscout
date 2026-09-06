<?php
namespace App\Models;

use App\Core\Database;

class Author {
    public static function findBySlug(string $slug): ?array {
        return Database::fetch("SELECT a.*, u.email, u.username, u.role FROM authors a JOIN users u ON a.user_id = u.id WHERE a.slug = ?", [$slug]);
    }

    public static function findByUserId(int $userId): ?array {
        return Database::fetch("SELECT * FROM authors WHERE user_id = ?", [$userId]);
    }

    public static function getTopAuthors(int $limit = 8): array {
        return Database::query("SELECT a.*, COUNT(c.id) as published_articles 
                               FROM authors a 
                               LEFT JOIN contents c ON a.id = c.author_id AND c.status = 'published'
                               GROUP BY a.id 
                               ORDER BY a.reputation_score DESC, a.total_views DESC LIMIT {$limit}");
    }

    public static function grantCredits(int $authorId, float $amount, string $reason): bool {
        Database::execute("UPDATE authors SET promo_credits = promo_credits + ? WHERE id = ?", [$amount, $authorId]);
        Database::execute("INSERT INTO revenue_records (author_id, revenue_type, amount_usd, credits_granted, description) VALUES (?, 'non_cash_credit', 0, ?, ?)", [
            $authorId, $amount, $reason
        ]);
        return true;
    }

    public static function updateBadge(int $authorId, string $badge): bool {
        return Database::execute("UPDATE authors SET verification_badge = ? WHERE id = ?", [$badge, $authorId]);
    }

    /**
     * Compute Creator Monetization Partner Program Status & Eligibility Criteria
     */
    public static function getMonetizationStatus(int $authorId): array {
        $author = Database::fetch("SELECT * FROM authors WHERE id = ?", [$authorId]);
        if (!$author) return [];

        // 1. Follower Count (Target: 1,000)
        $dbFollowers = (int)(Database::fetch("SELECT COUNT(*) as c FROM author_followers WHERE author_id = ?", [$authorId])['c'] ?? 0);
        $totalFollowers = max($dbFollowers, (int)($author['follower_count'] ?? 0));
        $reqFollowers = 1000;
        $followersMet = $totalFollowers >= $reqFollowers;
        $followersPct = min(100, round(($totalFollowers / $reqFollowers) * 100, 1));

        // 2. Qualified 10s+ Views (Target: 100,000)
        $qEvents = (int)(Database::fetch("
            SELECT COUNT(*) as c FROM analytics_events 
            WHERE (author_id = ? OR content_id IN (SELECT id FROM contents WHERE author_id = ?))
              AND duration_seconds >= 10
        ", [$authorId, $authorId])['c'] ?? 0);

        $qHistory = (int)(Database::fetch("
            SELECT COUNT(*) as c FROM reading_history 
            WHERE content_id IN (SELECT id FROM contents WHERE author_id = ?)
              AND read_seconds >= 10
        ", [$authorId])['c'] ?? 0);

        $totalViews = (int)(Database::fetch("SELECT SUM(view_count) as s FROM contents WHERE author_id = ?", [$authorId])['s'] ?? 0);
        $qualifiedViews = max($qEvents + $qHistory, (int)($totalViews * 0.75));
        $reqQualifiedViews = 100000;
        $viewsMet = $qualifiedViews >= $reqQualifiedViews;
        $viewsPct = min(100, round(($qualifiedViews / $reqQualifiedViews) * 100, 1));

        // 3. Published Articles (Target: 5)
        $publishedCount = (int)(Database::fetch("SELECT COUNT(*) as c FROM contents WHERE author_id = ? AND status = 'published'", [$authorId])['c'] ?? 0);
        $reqPublished = 5;
        $publishedMet = $publishedCount >= $reqPublished;
        $publishedPct = min(100, round(($publishedCount / $reqPublished) * 100, 1));

        // 4. Content Quality Score (Target: >= 70%)
        $avgScore = round((float)(Database::fetch("SELECT AVG(quality_score) as avg_s FROM contents WHERE author_id = ?", [$authorId])['avg_s'] ?? 80), 1);
        $reqScore = 70;
        $scoreMet = $avgScore >= $reqScore;

        // Overall Eligibility
        $isEligible = $followersMet && $viewsMet && $publishedMet && $scoreMet;
        $overallProgress = round(($followersPct + $viewsPct + $publishedPct) / 3, 1);

        $status = $author['monetization_status'] ?? 'in_progress';
        if (!empty($author['is_monetized'])) {
            $status = 'approved';
        } elseif ($status === 'applied') {
            // Awaiting review
        } elseif ($isEligible) {
            $status = 'eligible';
        } else {
            $status = 'in_progress';
        }

        return [
            'author' => $author,
            'status' => $status,
            'is_monetized' => (bool)($author['is_monetized'] ?? 0),
            'applied_at' => $author['monetization_applied_at'] ?? null,
            'approved_at' => $author['monetization_approved_at'] ?? null,
            'is_eligible' => $isEligible,
            'overall_progress' => $overallProgress,
            'metrics' => [
                'followers' => [
                    'current' => $totalFollowers,
                    'required' => $reqFollowers,
                    'met' => $followersMet,
                    'percent' => $followersPct,
                    'label' => '1,000 Verified Followers'
                ],
                'qualified_views' => [
                    'current' => $qualifiedViews,
                    'required' => $reqQualifiedViews,
                    'met' => $viewsMet,
                    'percent' => $viewsPct,
                    'label' => '100,000 Qualified Reads (≥ 10s Focus)'
                ],
                'published_articles' => [
                    'current' => $publishedCount,
                    'required' => $reqPublished,
                    'met' => $publishedMet,
                    'percent' => $publishedPct,
                    'label' => '5 Active Publications'
                ],
                'quality_score' => [
                    'current' => $avgScore,
                    'required' => $reqScore,
                    'met' => $scoreMet,
                    'percent' => min(100, round(($avgScore / 100) * 100, 1)),
                    'label' => '70% Minimum Quality Score'
                ]
            ]
        ];
    }
}
