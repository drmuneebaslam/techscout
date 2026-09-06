<?php
namespace App\Models;

use App\Core\Database;
use App\Core\NotificationHelper;

class CoAuthorEngine {
    /**
     * Get all active co-authors for an article
     */
    public static function getArticleCoAuthors(int $articleId): array {
        return Database::query(
            "SELECT aca.*, a.display_name, a.slug, a.avatar_url, a.headline, u.email as author_email
             FROM article_co_authors aca
             JOIN authors a ON aca.author_id = a.id
             JOIN users u ON a.user_id = u.id
             WHERE aca.article_id = ?
             ORDER BY aca.role = 'lead_author' DESC, aca.revenue_share_percentage DESC",
            [$articleId]
        );
    }

    /**
     * Set / Update Article Co-Authors and Revenue Split
     */
    public static function setArticleCoAuthors(int $articleId, int $leadAuthorId, array $coAuthorRows): bool {
        $article = Database::fetch("SELECT * FROM contents WHERE id = ?", [$articleId]);
        if (!$article) {
            throw new \RuntimeException("Article not found.");
        }

        // Validate total split percentage
        $totalSplit = 0.0;
        foreach ($coAuthorRows as $row) {
            $totalSplit += (float)($row['revenue_share_percentage'] ?? 0);
        }

        if ($totalSplit > 100.01) {
            throw new \InvalidArgumentException("Total revenue split sum cannot exceed 100%. Current sum: {$totalSplit}%");
        }

        // Keep lead author row guaranteed
        $hasLead = false;
        foreach ($coAuthorRows as $row) {
            if ((int)$row['author_id'] === $leadAuthorId && ($row['role'] ?? '') === 'lead_author') {
                $hasLead = true;
                break;
            }
        }

        if (!$hasLead) {
            $leadShare = max(0, 100.0 - $totalSplit);
            array_unshift($coAuthorRows, [
                'author_id' => $leadAuthorId,
                'role' => 'lead_author',
                'revenue_share_percentage' => $leadShare,
                'invitation_status' => 'accepted'
            ]);
        }

        // Remove old rows not in new list
        $newAuthorIds = array_map(fn($r) => (int)$r['author_id'], $coAuthorRows);
        Database::execute(
            "DELETE FROM article_co_authors WHERE article_id = ? AND author_id NOT IN (" . implode(',', array_fill(0, count($newAuthorIds), '?')) . ")",
            array_merge([$articleId], $newAuthorIds)
        );

        // Upsert co-author rows
        foreach ($coAuthorRows as $r) {
            $authId = (int)$r['author_id'];
            $role = in_array($r['role'] ?? '', ['lead_author', 'co_author', 'technical_reviewer']) ? $r['role'] : 'co_author';
            $pct = (float)($r['revenue_share_percentage'] ?? 0);
            $status = ($authId === $leadAuthorId) ? 'accepted' : ($r['invitation_status'] ?? 'accepted');

            Database::execute(
                "INSERT INTO article_co_authors (article_id, author_id, role, revenue_share_percentage, invitation_status, created_at)
                 VALUES (?, ?, ?, ?, ?, NOW())
                 ON DUPLICATE KEY UPDATE role = VALUES(role), revenue_share_percentage = VALUES(revenue_share_percentage), invitation_status = VALUES(invitation_status)",
                [$articleId, $authId, $role, $pct, $status]
            );

            // Notify co-author if new invite
            if ($authId !== $leadAuthorId && $status === 'pending') {
                $coAuth = Database::fetch("SELECT user_id FROM authors WHERE id = ?", [$authId]);
                if ($coAuth) {
                    NotificationHelper::send(
                        (int)$coAuth['user_id'],
                        'system',
                        "👥 Co-Authoring Invitation: \"{$article['title']}\"",
                        "You have been invited as a {$role} with a {$pct}% revenue royalty share on \"{$article['title']}\".",
                        "/author/dashboard?tab=collaborations"
                    );
                }
            }
        }

        return true;
    }

    /**
     * Disburse Split Royalties Across All Co-Authors on the Double-Entry Ledger
     */
    public static function disburseSplitRoyalties(int $articleId, float $totalRoyalty, string $category, string $description, ?int $originUserId = null): array {
        if ($totalRoyalty <= 0) return [];

        $article = Database::fetch("SELECT id, title, slug, author_id FROM contents WHERE id = ?", [$articleId]);
        if (!$article) return [];

        $coAuthors = self::getArticleCoAuthors($articleId);
        if (empty($coAuthors)) {
            // Default single author
            $coAuthors = [[
                'author_id' => $article['author_id'],
                'role' => 'lead_author',
                'revenue_share_percentage' => 100.0,
                'invitation_status' => 'accepted'
            ]];
        }

        $disbursements = [];

        foreach ($coAuthors as $ca) {
            $sharePct = (float)$ca['revenue_share_percentage'];
            if ($sharePct <= 0 || ($ca['invitation_status'] ?? 'accepted') !== 'accepted') continue;

            $authorAmount = round(($totalRoyalty * ($sharePct / 100.0)), 2);
            if ($authorAmount <= 0) continue;

            $splitDesc = "{$description} (Split: {$sharePct}% for article \"{$article['title']}\")";

            $ledgerRes = FinancialLedger::recordEntry(
                (int)$ca['author_id'],
                'credit',
                $category,
                $authorAmount,
                $splitDesc,
                $originUserId,
                'system',
                [
                    'article_id' => $articleId,
                    'role' => $ca['role'],
                    'share_percentage' => $sharePct,
                    'total_gross_royalty' => $totalRoyalty
                ]
            );

            $disbursements[] = [
                'author_id' => $ca['author_id'],
                'role' => $ca['role'],
                'share_percentage' => $sharePct,
                'amount_usd' => $authorAmount,
                'ledger_reference_no' => $ledgerRes['reference_no']
            ];
        }

        return $disbursements;
    }
}
