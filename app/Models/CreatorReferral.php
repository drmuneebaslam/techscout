<?php
namespace App\Models;

use App\Core\Database;
use App\Core\NotificationHelper;

class CreatorReferral {
    /**
     * Get or Generate Author Vanity Referral Code
     */
    public static function getOrCreateReferralCode(int $authorId): string {
        $author = Database::fetch("SELECT id, slug, referral_code FROM authors WHERE id = ?", [$authorId]);
        if (!$author) return '';

        if (!empty($author['referral_code'])) {
            return $author['referral_code'];
        }

        $code = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $author['slug'] ?: 'author' . $authorId));
        if (strlen($code) < 3) $code .= mt_rand(100, 999);

        // Ensure unique
        $exists = Database::fetch("SELECT id FROM authors WHERE referral_code = ? AND id != ?", [$code, $authorId]);
        if ($exists) {
            $code .= mt_rand(10, 99);
        }

        Database::execute("UPDATE authors SET referral_code = ? WHERE id = ?", [$code, $authorId]);
        return $code;
    }

    /**
     * Track a user signup through an ambassador link
     */
    public static function trackReferralSignup(int $referredUserId, string $referralCode): bool {
        $referrer = Database::fetch("SELECT id, user_id FROM authors WHERE referral_code = ?", [$referralCode]);
        if (!$referrer || (int)$referrer['user_id'] === $referredUserId) return false;

        $referrerAuthorId = (int)$referrer['id'];

        // Check if already referred
        $existing = Database::fetch("SELECT id FROM creator_referrals WHERE referred_user_id = ?", [$referredUserId]);
        if ($existing) return false;

        // 6 months expiration window
        $expiresAt = date('Y-m-d H:i:s', strtotime('+6 months'));

        Database::execute(
            "INSERT INTO creator_referrals (referrer_author_id, referred_user_id, referral_code, commission_rate, expires_at, created_at)
             VALUES (?, ?, ?, 5.00, ?, NOW())",
            [$referrerAuthorId, $referredUserId, $referralCode, $expiresAt]
        );

        NotificationHelper::send(
            (int)$referrer['user_id'],
            'system',
            "🤝 New Creator Referral Linked!",
            "A new member joined TechScout using your ambassador invite link! You will earn a 5% bonus commission on their readership royalties for the next 6 months.",
            "/author/dashboard?tab=ambassador"
        );

        return true;
    }

    /**
     * Link author profile when a referred user becomes an approved creator
     */
    public static function linkReferredAuthor(int $userId, int $authorId): void {
        Database::execute("UPDATE creator_referrals SET referred_author_id = ? WHERE referred_user_id = ?", [$authorId, $userId]);
    }

    /**
     * Process 5% Ambassador Commission on Referred Author Royalties
     */
    public static function processReferralRoyaltyCommission(int $earningAuthorId, float $royaltyAmount, string $sourceDescription): ?array {
        if ($royaltyAmount <= 0) return null;

        // Check if author was referred and referral is still within 6-month window
        $ref = Database::fetch(
            "SELECT cr.*, a.user_id as referrer_user_id, a.display_name as referrer_name
             FROM creator_referrals cr
             JOIN authors a ON cr.referrer_author_id = a.id
             WHERE cr.referred_author_id = ? AND cr.expires_at > NOW()",
            [$earningAuthorId]
        );

        if (!$ref) return null;

        $commissionRate = (float)($ref['commission_rate'] ?? 5.00) / 100.0;
        $commissionAmount = round($royaltyAmount * $commissionRate, 2);

        if ($commissionAmount < 0.01) return null;

        $earner = Database::fetch("SELECT display_name FROM authors WHERE id = ?", [$earningAuthorId]);
        $earnerName = $earner['display_name'] ?? 'Referred Author';

        $desc = "Ambassador Referral Royalty Commission (5%) from {$earnerName} for: {$sourceDescription}";

        $ledgerRes = FinancialLedger::recordEntry(
            (int)$ref['referrer_author_id'],
            'credit',
            'referral_commission',
            $commissionAmount,
            $desc,
            null,
            'system',
            [
                'referral_id' => $ref['id'],
                'referred_author_id' => $earningAuthorId,
                'source_royalty' => $royaltyAmount
            ]
        );

        // Update total commission earned
        Database::execute(
            "UPDATE creator_referrals SET total_commission_earned = total_commission_earned + ? WHERE id = ?",
            [$commissionAmount, $ref['id']]
        );

        // Log commission
        Database::execute(
            "INSERT INTO referral_commissions (referral_id, referrer_author_id, ledger_reference_no, amount_usd, source_event, created_at)
             VALUES (?, ?, ?, ?, ?, NOW())",
            [$ref['id'], $ref['referrer_author_id'], $ledgerRes['reference_no'], $commissionAmount, $sourceDescription]
        );

        return [
            'referral_id' => $ref['id'],
            'amount_usd' => $commissionAmount,
            'commission_amount' => $commissionAmount,
            'ledger_reference_no' => $ledgerRes['reference_no']
        ];
    }

    /**
     * Get Ambassador Dashboard Statistics for an Author
     */
    public static function getAmbassadorStats(int $authorId): array {
        $referrals = Database::query(
            "SELECT cr.*, u.username as referred_username, u.email as referred_email, a.display_name as author_name, a.avatar_url as author_avatar, a.total_views, a.promo_credits
             FROM creator_referrals cr
             JOIN users u ON cr.referred_user_id = u.id
             LEFT JOIN authors a ON cr.referred_author_id = a.id
             WHERE cr.referrer_author_id = ?
             ORDER BY cr.created_at DESC",
            [$authorId]
        );

        $totalEarned = Database::fetch(
            "SELECT COALESCE(SUM(amount_usd), 0) as total FROM referral_commissions WHERE referrer_author_id = ?",
            [$authorId]
        );

        $recentCommissions = Database::query(
            "SELECT rc.*, cr.referred_author_id, a.display_name as author_name
             FROM referral_commissions rc
             JOIN creator_referrals cr ON rc.referral_id = cr.id
             LEFT JOIN authors a ON cr.referred_author_id = a.id
             WHERE rc.referrer_author_id = ?
             ORDER BY rc.created_at DESC
             LIMIT 10",
            [$authorId]
        );

        return [
            'referral_code' => self::getOrCreateReferralCode($authorId),
            'total_referrals' => count($referrals),
            'active_authors' => count(array_filter($referrals, fn($r) => !empty($r['referred_author_id']))),
            'total_earned_usd' => (float)($totalEarned['total'] ?? 0),
            'referrals_list' => $referrals,
            'commissions_list' => $recentCommissions
        ];
    }
}
