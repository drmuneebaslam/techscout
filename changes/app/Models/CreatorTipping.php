<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Auth;
use App\Core\NotificationHelper;

class CreatorTipping {
    /**
     * Send a Tip / Micro-Sponsorship to an Author
     */
    public static function sendTip(int $authorId, ?int $articleId, ?int $tipperUserId, float $amount, array $meta = []): array {
        if ($amount < 1.00) {
            throw new \InvalidArgumentException("Minimum tip amount is $1.00 USD.");
        }

        $author = Database::fetch("SELECT a.*, u.email as author_email FROM authors a JOIN users u ON a.user_id = u.id WHERE a.id = ?", [$authorId]);
        if (!$author) {
            throw new \RuntimeException("Target author not found.");
        }

        $article = $articleId ? Database::fetch("SELECT id, title, slug FROM contents WHERE id = ?", [$articleId]) : null;

        // Platform fee: 5% (min $0.15)
        $feeRate = 0.05;
        $platformFee = round($amount * $feeRate, 2);
        $netAmount = $amount - $platformFee;

        $referenceNo = 'TIP-' . date('Y') . '-' . strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 8));

        $tipperName = !empty($meta['tipper_name']) ? trim($meta['tipper_name']) : 'Generous Supporter';
        $tipperEmail = !empty($meta['tipper_email']) ? trim($meta['tipper_email']) : null;
        $message = !empty($meta['message']) ? trim($meta['message']) : null;
        $isAnonymous = !empty($meta['is_anonymous']) ? 1 : 0;
        $paymentMethod = !empty($meta['payment_method']) ? trim($meta['payment_method']) : 'card_stripe';

        if ($tipperUserId && $isAnonymous) {
            $tipperName = 'Anonymous Supporter';
        }

        // 1. Insert Tip Record
        Database::execute(
            "INSERT INTO author_tips (reference_no, author_id, article_id, tipper_user_id, tipper_name, tipper_email, amount_usd, platform_fee_usd, net_amount_usd, message, is_anonymous, payment_method, status, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'completed', NOW())",
            [$referenceNo, $authorId, $articleId, $tipperUserId, $tipperName, $tipperEmail, $amount, $platformFee, $netAmount, $message, $isAnonymous, $paymentMethod]
        );
        $tipId = (int)Database::lastInsertId();

        // 2. Double-Entry Credit in Financial Ledger
        $articleTitle = $article ? " for article \"{$article['title']}\"" : "";
        $ledgerDesc = "Reader Tip / Patronage from {$tipperName} (\${$amount} USD gross, \${$platformFee} fee){$articleTitle}";

        $ledgerRes = FinancialLedger::recordEntry(
            $authorId,
            'credit',
            'reader_tip',
            $netAmount,
            $ledgerDesc,
            $tipperUserId,
            'reader',
            [
                'tip_id' => $tipId,
                'reference_no' => $referenceNo,
                'article_id' => $articleId,
                'gross_amount' => $amount,
                'platform_fee' => $platformFee
            ]
        );

        // 3. Notify Author
        $articleMsg = $article ? " on article \"{$article['title']}\"" : "";
        $noteMsg = $message ? " Note: \"{$message}\"" : "";
        NotificationHelper::send(
            (int)$author['user_id'],
            'reward',
            "☕ New Reader Tip Received: +\${$netAmount} USD",
            "{$tipperName} sent you a \${$amount} USD tip{$articleMsg}! Net +\${$netAmount} USD has been credited to your withdrawable balance.{$noteMsg}",
            $article ? "/articles/{$article['slug']}" : "/author/dashboard"
        );

        // Send Email to Author
        try {
            $authorUser = Database::fetch("SELECT u.username, u.email, a.display_name FROM users u LEFT JOIN authors a ON u.id = a.user_id WHERE u.id = ?", [(int)$author['user_id']]);
            if ($authorUser && !empty($authorUser['email'])) {
                $recipientName = !empty($authorUser['display_name']) ? $authorUser['display_name'] : ($authorUser['username'] ?? ($author['display_name'] ?? 'Creator'));
                \App\Core\Mailer::sendTemplate('tip_received', $authorUser['email'], $recipientName, [
                    'name' => $recipientName,
                    'tipper_name' => $tipperName,
                    'amount' => number_format($amount, 2),
                    'net_amount' => number_format($netAmount, 2),
                    'article_title' => $article ? $article['title'] : 'Platform Creator Profile',
                    'message' => $message ?: 'Thank you for your high-quality engineering contributions on TechScout!',
                    'action_url' => url('/author/rewards')
                ]);
            }
        } catch (\Throwable $e) {}

        // 4. Audit Log
        AuditLog::record('reader.tip_sent', 'author_tips', $tipId, [
            'author_id' => $authorId,
            'amount_usd' => $amount,
            'net_amount_usd' => $netAmount,
            'reference_no' => $referenceNo
        ]);

        return [
            'id' => $tipId,
            'tip_id' => $tipId,
            'reference_no' => $referenceNo,
            'amount_usd' => $amount,
            'gross_amount' => $amount,
            'net_amount_usd' => $netAmount,
            'net_amount' => $netAmount,
            'platform_fee_usd' => $platformFee,
            'platform_fee' => $platformFee,
            'ledger_entry' => $ledgerRes
        ];
    }

    /**
     * Get Top Supporters / Patrons for an Author
     */
    public static function getAuthorSupporters(int $authorId, int $limit = 10): array {
        $limitInt = max(1, (int)$limit);
        return Database::query(
            "SELECT COALESCE(tipper_name, 'Generous Patron') as tipper_name, COALESCE(tipper_name, 'Generous Patron') as supporter_name, is_anonymous, SUM(amount_usd) as total_tipped, COUNT(*) as tip_count, MAX(created_at) as last_tipped_at
             FROM author_tips
             WHERE author_id = ? AND status = 'completed'
             GROUP BY IF(is_anonymous = 1, CONCAT('anon_', id), COALESCE(tipper_user_id, tipper_name))
             ORDER BY total_tipped DESC, last_tipped_at DESC
             LIMIT {$limitInt}",
            [$authorId]
        );
    }

    /**
     * Get Recent Tips for an Article
     */
    public static function getArticleTips(int $articleId, int $limit = 8): array {
        $limitInt = max(1, (int)$limit);
        return Database::query(
            "SELECT t.*, a_tip.avatar_url as tipper_avatar
             FROM author_tips t
             LEFT JOIN authors a_tip ON t.tipper_user_id = a_tip.user_id
             WHERE t.article_id = ? AND t.status = 'completed'
             ORDER BY t.created_at DESC
             LIMIT {$limitInt}",
            [$articleId]
        );
    }

    /**
     * Get All Tips Stream for Admin Revenue
     */
    public static function getAllTips(int $limit = 50): array {
        $limitInt = max(1, (int)$limit);
        return Database::query(
            "SELECT t.*, a.display_name as author_name, a.slug as author_slug, art.title as article_title, art.slug as article_slug
             FROM author_tips t
             JOIN authors a ON t.author_id = a.id
             LEFT JOIN contents art ON t.article_id = art.id
             ORDER BY t.created_at DESC
             LIMIT {$limitInt}"
        );
    }
}
