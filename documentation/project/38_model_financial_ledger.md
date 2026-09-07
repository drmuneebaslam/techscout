# 38. Double-Entry Financial Ledger Engine (`FinancialLedger.php`)

## File Location & Metadata
- **File Path**: [`app/Models/FinancialLedger.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/FinancialLedger.php)
- **Primary Role**: Manages double-entry financial accounting for author CPM view earnings, member patronage tips, referral bonuses, promotional credit redemptions, and payout withdrawal claims.
- **Dependencies**: [`app/Core/Database.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Database.php), [`app/Models/AuditLog.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/AuditLog.php).

---

## Accounting Architecture & Transaction Types

FinancialLedger enforces immutable double-entry credit ledgering. Transactions cannot be deleted or mutated; correcting adjustments require balancing credit or debit entries.

```
Ledger Accounting Flow
┌─────────────────────────────────────────────────────────┐
│                 Transaction Initiated                   │
│  (CPM View Earn, Tip Received, Payout Withdrawal Claim) │
└───────────────────────────┬─────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────┐
│              Database Transaction Begins                │
│                 $db->beginTransaction()                 │
└───────────────────────────┬─────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────┐
│        Insert Immutable Row in `financial_ledger`       │
│  - entry_type (credit | debit)                          │
│  - transaction_category (earnings | tip | payout)       │
│  - gross_amount, platform_fee, net_amount               │
│  - running_balance_after                                │
└───────────────────────────┬─────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────┐
│            Update Author Withdrawable Balance           │
│        UPDATE authors SET withdrawable_balance = ...    │
└───────────────────────────┬─────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────┐
│                 $db->commit() & Audit Log               │
└─────────────────────────────────────────────────────────┘
```

### Supported Transaction Categories
- **`earnings`**: Article CPM pageview earnings credited to author.
- **`tip_received`**: Direct member patronage tipping transferred to author.
- **`referral_reward`**: Bonus credit rewarded for creator referrals.
- **`payout_claim`**: Creator withdrawal request debited from balance.
- **`payout_refund`**: Rejected payout claim refunded back to author balance.
- **`platform_fee`**: Platform fee deducted on patronage transactions.

---

## Detailed Code Implementation

```php
namespace App\Models;

use App\Core\Database;
use Exception;

class FinancialLedger {

    public static function recordEntry(
        int $authorId, 
        string $entryType, 
        string $category, 
        float $grossAmount, 
        float $platformFee = 0.0, 
        ?string $description = null, 
        ?int $referenceId = null
    ): int {
        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            // Lock author row for update
            $author = $db->fetch("SELECT withdrawable_balance FROM authors WHERE id = ? FOR UPDATE", [$authorId]);
            if (!$author) {
                throw new Exception("Author not found.");
            }

            $currentBalance = (float) $author['withdrawable_balance'];
            $netAmount = $grossAmount - $platformFee;

            if ($entryType === 'credit') {
                $newBalance = $currentBalance + $netAmount;
            } elseif ($entryType === 'debit') {
                if ($currentBalance < $netAmount) {
                    throw new Exception("Insufficient credit balance.");
                }
                $newBalance = $currentBalance - $netAmount;
            } else {
                throw new Exception("Invalid entry type.");
            }

            // Insert Ledger Entry
            $db->execute(
                "INSERT INTO financial_ledger 
                 (author_id, entry_type, category, gross_amount, platform_fee, net_amount, running_balance_after, description, reference_id, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())",
                [$authorId, $entryType, $category, $grossAmount, $platformFee, $netAmount, $newBalance, $description, $referenceId]
            );

            $ledgerId = (int) $db->lastInsertId();

            // Update Author Balance
            $db->execute("UPDATE authors SET withdrawable_balance = ? WHERE id = ?", [$newBalance, $authorId]);

            $db->commit();
            AuditLog::log("ledger.entry_recorded", "author", $authorId, ["ledger_id" => $ledgerId, "category" => $category, "amount" => $netAmount]);

            return $ledgerId;

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function getAuthorBalance(int $authorId): float {
        $db = Database::getInstance();
        $row = $db->fetch("SELECT withdrawable_balance FROM authors WHERE id = ?", [$authorId]);
        return $row ? (float) $row['withdrawable_balance'] : 0.0;
    }

    public static function requestPayout(int $authorId, float $amount, string $payoutMethod, array $details): int {
        $db = Database::getInstance();
        if ($amount < 10.00) {
            throw new Exception("Minimum payout threshold is $10.00.");
        }

        $balance = self::getAuthorBalance($authorId);
        if ($balance < $amount) {
            throw new Exception("Insufficient withdrawable balance.");
        }

        // Record debit transaction in ledger
        $ledgerId = self::recordEntry($authorId, 'debit', 'payout_claim', $amount, 0.0, "Payout Claim Request via {$payoutMethod}");

        // Insert pending payout claim
        $db->execute(
            "INSERT INTO payout_claims (author_id, ledger_id, amount, payout_method, account_details_json, status, created_at) 
             VALUES (?, ?, ?, ?, ?, 'pending', NOW())",
            [$authorId, $ledgerId, $amount, $payoutMethod, json_encode($details)]
        );

        return (int) $db->lastInsertId();
    }
}
```

---

## Detailed QA Test Suite for Financial Ledger

### Test Case 1: Member Tip Double-Entry Transaction
- **QA Test ID**: `TC-FIN-001`
- **Target Function**: `FinancialLedger::recordEntry()`
- **Execution Steps**:
  1. Record $20.00 credit tip for author ID 2:
     ```php
     FinancialLedger::recordEntry(2, 'credit', 'tip_received', 20.00, 1.00, 'Tip from Reader');
     ```
  2. Query `financial_ledger` and `authors` balance.
- **Expected Result**: Net amount $19.00 credited ($20.00 gross - $1.00 fee). Running balance and `authors.withdrawable_balance` increase by $19.00.
- **Assertion**: `withdrawable_balance` updated atomically.

### Test Case 2: Insufficient Balance Payout Prevention
- **QA Test ID**: `TC-FIN-002`
- **Target Function**: `FinancialLedger::requestPayout()`
- **Execution Steps**:
  1. Set author ID 4 balance to $5.00.
  2. Attempt payout claim for $50.00:
     ```php
     FinancialLedger::requestPayout(4, 50.00, 'paypal', ['email' => 'test@paypal.com']);
     ```
- **Expected Result**: Throws Exception "Insufficient withdrawable balance." Transaction rolled back without balance modification.
