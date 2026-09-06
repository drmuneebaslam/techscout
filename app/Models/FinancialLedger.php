<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Auth;
use App\Core\NotificationHelper;

class FinancialLedger {

    /**
     * Record a Double-Entry Ledger Transaction & Synchronize Author Balance
     */
    public static function recordEntry(
        int $authorId,
        string $entryType, // 'credit' or 'debit'
        string $category,   // 'manual_adjustment_credit', 'manual_adjustment_debit', 'clawback', 'payout_withdrawal', 'editorial_grant', 'refund_rejected_payout', 'ad_revenue_share', 'tip_received'
        float $amount,
        string $reason,
        ?int $performedBy = null,
        string $role = 'admin',
        array $extra = []
    ): array {
        $amount = abs($amount);
        if ($amount <= 0) {
            return ['success' => false, 'error' => 'Transaction amount must be greater than zero.'];
        }

        $author = Database::fetch("SELECT a.*, u.id as user_id, u.email, u.username FROM authors a JOIN users u ON a.user_id = u.id WHERE a.id = ?", [$authorId]);
        if (!$author) {
            return ['success' => false, 'error' => 'Author record not found.'];
        }

        $balanceBefore = (float)($author['promo_credits'] ?? 0.0);
        
        if ($entryType === 'debit') {
            $allowNegative = $extra['allow_overdraft'] ?? false;
            if ($balanceBefore < $amount && !$allowNegative) {
                return [
                    'success' => false, 
                    'error' => "Cannot deduct \${$amount} USD. Creator only has \${$balanceBefore} USD available."
                ];
            }
            $balanceAfter = max(0.0, $balanceBefore - $amount);
            if ($allowNegative) {
                $balanceAfter = $balanceBefore - $amount;
            }
        } else {
            $balanceAfter = $balanceBefore + $amount;
        }

        $refNo = 'TXN-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        $payoutMethodId = !empty($extra['payout_method_id']) ? (int)$extra['payout_method_id'] : null;
        $payoutMethodDetails = $extra['payout_method_details'] ?? null;
        $status = $extra['status'] ?? 'completed';
        $internalNotes = $extra['internal_notes'] ?? null;
        $metadataJson = !empty($extra['metadata']) ? json_encode($extra['metadata'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null;

        // 1. Update Author Balance
        Database::execute("UPDATE authors SET promo_credits = ?, updated_at = NOW() WHERE id = ?", [$balanceAfter, $authorId]);

        // 2. Insert into financial_ledger
        Database::execute("INSERT INTO financial_ledger (
            reference_no, author_id, user_id, entry_type, category, amount_usd, credits,
            balance_before, balance_after, payout_method_id, payout_method_details, status,
            performed_by_user_id, performed_by_role, reason, internal_notes, metadata_json, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())", [
            $refNo, $authorId, (int)$author['user_id'], $entryType, $category, $amount, $amount,
            $balanceBefore, $balanceAfter, $payoutMethodId, $payoutMethodDetails, $status,
            $performedBy ?: (Auth::id() ?: 1), $role, $reason, $internalNotes, $metadataJson
        ]);
        $ledgerId = (int)Database::lastInsertId();

        // 3. Mirror into revenue_records for compatibility
        $signedAmount = ($entryType === 'debit') ? -$amount : $amount;
        Database::execute("INSERT INTO revenue_records (author_id, description, credits_granted, amount_usd, transaction_type, created_at) VALUES (?, ?, ?, ?, ?, NOW())", [
            $authorId, $reason, $signedAmount, $signedAmount, $category
        ]);

        // 4. Audit Log
        AuditLog::record("ledger.{$entryType}", 'financial_ledger', $ledgerId, [
            'reference_no' => $refNo,
            'author_id' => $authorId,
            'amount_usd' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'reason' => $reason
        ]);

        // 5. Send In-App Notification to Author
        $icon = ($entryType === 'credit') ? '💰' : '📉';
        $title = ($entryType === 'credit') ? "{$icon} Wallet Credited: +\${$amount} USD" : "{$icon} Balance Adjustment: -\${$amount} USD";
        $notice = ($entryType === 'credit') 
            ? "Your creator balance has been credited with \${$amount} USD. New Balance: \${$balanceAfter} USD. Reason: {$reason}"
            : "An adjustment of -\${$amount} USD was recorded on your account. New Balance: \${$balanceAfter} USD. Reason: {$reason}";

        NotificationHelper::send((int)$author['user_id'], 'reward', $title, $notice, '/author/dashboard');

        return [
            'success' => true,
            'reference_no' => $refNo,
            'ledger_id' => $ledgerId,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'amount' => $amount,
            'entry_type' => $entryType
        ];
    }

    /**
     * Fetch author transaction ledger history
     */
    public static function getAuthorLedger(int $authorId, int $limit = 50, int $offset = 0): array {
        return Database::query("SELECT fl.*, pm.method_type, pm.bank_name, pm.account_holder_name
                               FROM financial_ledger fl
                               LEFT JOIN author_payout_methods pm ON fl.payout_method_id = pm.id
                               WHERE fl.author_id = ?
                               ORDER BY fl.created_at DESC, fl.id DESC
                               LIMIT {$limit} OFFSET {$offset}", [$authorId]);
    }

    /**
     * Fetch global platform ledger for Admin Hub
     */
    public static function getPlatformLedger(?string $entryType = null, ?string $category = null, ?int $authorId = null, int $limit = 100): array {
        $sql = "SELECT fl.*, a.display_name as author_name, a.slug as author_slug, u.email as user_email,
                       pm.method_type, pm.bank_name,
                       admin_u.username as performed_by_username
                FROM financial_ledger fl
                JOIN authors a ON fl.author_id = a.id
                JOIN users u ON a.user_id = u.id
                LEFT JOIN author_payout_methods pm ON fl.payout_method_id = pm.id
                LEFT JOIN users admin_u ON fl.performed_by_user_id = admin_u.id
                WHERE 1=1";
        $params = [];

        if ($entryType && $entryType !== 'all') {
            $sql .= " AND fl.entry_type = ?";
            $params[] = $entryType;
        }
        if ($category && $category !== 'all') {
            $sql .= " AND fl.category = ?";
            $params[] = $category;
        }
        if ($authorId && $authorId > 0) {
            $sql .= " AND fl.author_id = ?";
            $params[] = $authorId;
        }

        $sql .= " ORDER BY fl.created_at DESC, fl.id DESC LIMIT {$limit}";
        return Database::query($sql, $params);
    }

    /**
     * Get all linked payout methods for an author
     */
    public static function getPayoutMethods(int $authorId): array {
        return Database::query("SELECT * FROM author_payout_methods WHERE author_id = ? AND status != 'disabled' ORDER BY is_default DESC, created_at DESC", [$authorId]);
    }

    /**
     * Get default verified payout method
     */
    public static function getDefaultPayoutMethod(int $authorId): ?array {
        $method = Database::fetch("SELECT * FROM author_payout_methods WHERE author_id = ? AND is_default = 1 AND status != 'disabled' LIMIT 1", [$authorId]);
        if (!$method) {
            $method = Database::fetch("SELECT * FROM author_payout_methods WHERE author_id = ? AND status != 'disabled' ORDER BY id ASC LIMIT 1", [$authorId]);
        }
        return $method ?: null;
    }

    /**
     * Add or update a linked payout destination
     */
    public static function savePayoutMethod(int $authorId, array $data): int {
        $type = $data['method_type'] ?? 'bank_wire';
        $holder = trim((string)($data['account_holder_name'] ?? ''));
        $bank = trim((string)($data['bank_name'] ?? ''));
        $accountNum = trim((string)($data['account_number_or_iban'] ?? ''));
        $swift = trim((string)($data['routing_or_swift'] ?? ''));
        $email = trim((string)($data['account_email'] ?? ''));
        $wallet = trim((string)($data['crypto_wallet_address'] ?? ''));
        $net = trim((string)($data['crypto_network'] ?? 'ERC-20 (Ethereum)'));
        $isDefault = !empty($data['is_default']) ? 1 : 0;

        // If setting as default, clear other defaults
        if ($isDefault) {
            Database::execute("UPDATE author_payout_methods SET is_default = 0 WHERE author_id = ?", [$authorId]);
        } else {
            // Check if this is the first method, if so make it default
            $existing = Database::fetch("SELECT COUNT(*) as c FROM author_payout_methods WHERE author_id = ? AND status != 'disabled'", [$authorId]);
            if (($existing['c'] ?? 0) === 0) {
                $isDefault = 1;
            }
        }

        Database::execute("INSERT INTO author_payout_methods (
            author_id, method_type, account_holder_name, bank_name, account_number_or_iban,
            routing_or_swift, account_email, crypto_wallet_address, crypto_network, is_default, status, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'verified', NOW())", [
            $authorId, $type, $holder, $bank, $accountNum, $swift, $email, $wallet, $net, $isDefault
        ]);

        $newId = (int)Database::lastInsertId();

        AuditLog::record('author.payout_method_linked', 'author_payout_methods', $newId, [
            'author_id' => $authorId,
            'method_type' => $type
        ]);

        return $newId;
    }

    /**
     * Delete / Disable a linked payout method
     */
    public static function deletePayoutMethod(int $authorId, int $methodId): bool {
        $method = Database::fetch("SELECT * FROM author_payout_methods WHERE id = ? AND author_id = ?", [$methodId, $authorId]);
        if (!$method) return false;

        Database::execute("UPDATE author_payout_methods SET status = 'disabled', is_default = 0 WHERE id = ?", [$methodId]);
        
        // If it was default, set another active method as default
        if (!empty($method['is_default'])) {
            $next = Database::fetch("SELECT id FROM author_payout_methods WHERE author_id = ? AND status != 'disabled' ORDER BY id ASC LIMIT 1", [$authorId]);
            if ($next) {
                Database::execute("UPDATE author_payout_methods SET is_default = 1 WHERE id = ?", [$next['id']]);
            }
        }

        AuditLog::record('author.payout_method_removed', 'author_payout_methods', $methodId, ['author_id' => $authorId]);
        return true;
    }

    /**
     * Get platform & bank fee settings
     */
    public static function getPayoutFeeSettings(): array {
        $platformFee = Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'payout_platform_fee_percent'");
        $bankFee = Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'payout_bank_fee_percent'");
        $wireSurcharge = Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'payout_bank_wire_surcharge'");

        return [
            'platform_fee_percent' => $platformFee !== false && is_numeric($platformFee['key_value'] ?? null) ? (float)$platformFee['key_value'] : 5.0,
            'bank_fee_percent' => $bankFee !== false && is_numeric($bankFee['key_value'] ?? null) ? (float)$bankFee['key_value'] : 2.5,
            'bank_wire_surcharge' => $wireSurcharge !== false && is_numeric($wireSurcharge['key_value'] ?? null) ? (float)$wireSurcharge['key_value'] : 5.0
        ];
    }

    /**
     * Save platform & bank fee settings
     */
    public static function savePayoutFeeSettings(float $platformFee, float $bankFee, float $bankSurcharge): bool {
        Database::execute(
            "INSERT INTO site_settings (key_name, key_value, updated_at) VALUES ('payout_platform_fee_percent', ?, NOW()) 
             ON DUPLICATE KEY UPDATE key_value = VALUES(key_value), updated_at = NOW()",
            [(string)$platformFee]
        );
        Database::execute(
            "INSERT INTO site_settings (key_name, key_value, updated_at) VALUES ('payout_bank_fee_percent', ?, NOW()) 
             ON DUPLICATE KEY UPDATE key_value = VALUES(key_value), updated_at = NOW()",
            [(string)$bankFee]
        );
        Database::execute(
            "INSERT INTO site_settings (key_name, key_value, updated_at) VALUES ('payout_bank_wire_surcharge', ?, NOW()) 
             ON DUPLICATE KEY UPDATE key_value = VALUES(key_value), updated_at = NOW()",
            [(string)$bankSurcharge]
        );
        return true;
    }

    /**
     * Get allowed payout methods (standard + admin custom methods)
     */
    public static function getAllowedPayoutMethods(): array {
        $methods = [];
        $row = Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'payout_allowed_methods'");
        if (!empty($row['key_value'])) {
            $decoded = json_decode($row['key_value'], true);
            if (is_array($decoded) && !empty($decoded)) {
                $methods = $decoded;
            }
        }

        if (empty($methods)) {
            // Standard default methods
            $methods = [
                [
                    'key' => 'paypal',
                    'name' => 'PayPal (Instant Electronic Transfer)',
                    'description' => 'Fast digital disbursement directly to your PayPal registered email address',
                    'icon' => '🅿️',
                    'enabled' => true,
                    'is_custom' => false,
                    'custom' => false,
                    'fee_policy' => 'standard',
                    'fee_type' => 'platform_only'
                ],
                [
                    'key' => 'bank_wire',
                    'name' => 'Direct Bank Wire (ACH / SWIFT / IBAN)',
                    'description' => 'Direct wire transfer to your local or international bank checking / savings account',
                    'icon' => '🏦',
                    'enabled' => true,
                    'is_custom' => false,
                    'custom' => false,
                    'fee_policy' => 'bank',
                    'fee_type' => 'platform_and_bank'
                ],
                [
                    'key' => 'crypto_usdc',
                    'name' => 'USDC / USDT Stablecoin Crypto Transfer',
                    'description' => 'On-chain payout directly to your crypto wallet address (ERC-20, Solana, Polygon, Arbitrum)',
                    'icon' => '🪙',
                    'enabled' => true,
                    'is_custom' => false,
                    'custom' => false,
                    'fee_policy' => 'standard',
                    'fee_type' => 'platform_only'
                ],
                [
                    'key' => 'stripe',
                    'name' => 'Stripe Express Direct',
                    'description' => 'Direct automated payout to your verified debit card or bank account via Stripe Connect',
                    'icon' => '💳',
                    'enabled' => false,
                    'is_custom' => false,
                    'custom' => false,
                    'fee_policy' => 'standard',
                    'fee_type' => 'platform_only'
                ],
                [
                    'key' => 'wise',
                    'name' => 'Wise (formerly TransferWise)',
                    'description' => 'Low-fee cross-border multi-currency bank disbursement directly via Wise',
                    'icon' => '🌐',
                    'enabled' => false,
                    'is_custom' => false,
                    'custom' => false,
                    'fee_policy' => 'bank',
                    'fee_type' => 'platform_and_bank'
                ]
            ];
        }

        // Normalize every method record
        foreach ($methods as &$m) {
            $isCust = !empty($m['is_custom']) || !empty($m['custom']);
            $m['is_custom'] = $isCust;
            $m['custom'] = $isCust;
            if (empty($m['fee_policy'])) {
                $m['fee_policy'] = ($m['fee_type'] ?? '') === 'platform_and_bank' ? 'bank' : 'standard';
            }
            if (empty($m['fee_type'])) {
                $m['fee_type'] = $m['fee_policy'] === 'bank' ? 'platform_and_bank' : 'platform_only';
            }
        }
        unset($m);

        return $methods;
    }

    /**
     * Save allowed payout methods
     */
    public static function saveAllowedPayoutMethods(array $methods): bool {
        return Database::execute(
            "INSERT INTO site_settings (key_name, key_value, updated_at) VALUES ('payout_allowed_methods', ?, NOW()) 
             ON DUPLICATE KEY UPDATE key_value = VALUES(key_value), updated_at = NOW()",
            [json_encode(array_values($methods), JSON_UNESCAPED_UNICODE)]
        );
    }
}
