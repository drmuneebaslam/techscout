# 45. Core User Account Model (`User.php`)

## File Location & Metadata
- **File Path**: [`app/Models/User.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/User.php)
- **Primary Role**: Encapsulates user account entity operations, password hashing, authentication credentials validation, account status state changes, and the 3-Strike Disciplinary System.
- **Dependencies**: [`app/Core/Database.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Database.php), [`app/Models/AuditLog.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/AuditLog.php), [`app/Models/SessionManager.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/SessionManager.php), [`app/Core/Mailer.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Mailer.php).

---

## 3-Strike Disciplinary System Architecture

TechScout enforces a 3-Strike Disciplinary System to moderate platform policy violations:

```
3-Strike Escalation Flow
┌─────────────────────────────────────────────────────────┐
│        Admin Issues Strike (Reason & Reference)         │
└───────────────────────────┬─────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────┐
│    Insert Strike Record in `author_strikes` Table       │
└───────────────────────────┬─────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────┐
│      Count Active Strikes for User (SELECT COUNT)       │
└───────────────────────────┬─────────────────────────────┘
                            │
         ┌──────────────────┼──────────────────┐
         │                  │                  │
         ▼                  ▼                  ▼
  Active Strikes = 1  Active Strikes = 2  Active Strikes = 3
 ┌────────────────┐  ┌────────────────┐  ┌────────────────┐
 │ Strike 1:      │  │ Strike 2:      │  │ Strike 3:      │
 │ Official       │  │ 14-Day Account │  │ Permanent Ban  │
 │ Warning Email  │  │ Suspension &   │  │ & Immediate    │
 │ Dispatched     │  │ Session Revoke │  │ Session Revoke │
 └────────────────┘  └────────────────┘  └────────────────┘
```

### Escalation Thresholds & Penalties
1. **Strike 1 (Official Warning)**:
   - User account status remains `'active'`.
   - Automated Warning Email dispatched via `Mailer::send()`.
   - Strike record stored in `author_strikes`.
2. **Strike 2 (14-Day Account Suspension)**:
   - User account status updated to `'suspended'`.
   - `suspended_until` timestamp set to `NOW() + INTERVAL 14 DAY`.
   - All active user sessions forcibly revoked across all devices via `SessionManager::revokeAllOtherSessions()`.
   - Suspension notice email sent.
3. **Strike 3 (Permanent Ban)**:
   - User account status updated to `'banned'`.
   - All active sessions destroyed immediately.
   - User email and IP flagged in security firewall logs.

---

## Code Implementation & Method Reference

```php
namespace App\Models;

use App\Core\Database;
use App\Core\Mailer;
use Exception;

class User {

    public static function findById(int $id): ?array {
        $db = Database::getInstance();
        $user = $db->fetch("SELECT * FROM users WHERE id = ?", [$id]);
        return $user ?: null;
    }

    public static function findByEmail(string $email): ?array {
        $db = Database::getInstance();
        $user = $db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
        return $user ?: null;
    }

    public static function create(array $data): int {
        $db = Database::getInstance();
        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        $verificationToken = bin2hex(random_bytes(16));

        $db->execute(
            "INSERT INTO users (name, email, password_hash, role, status, email_verified, verification_token, created_at) 
             VALUES (?, ?, ?, ?, 'active', 0, ?, NOW())",
            [$data['name'], $data['email'], $passwordHash, $data['role'] ?? 'member', $verificationToken]
        );

        $userId = (int) $db->lastInsertId();
        AuditLog::log("user.created", "user", $userId, ["email" => $data['email']]);

        return $userId;
    }

    public static function issueStrike(int $userId, string $reason, int $adminId): array {
        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            // Record strike in database
            $db->execute(
                "INSERT INTO author_strikes (user_id, admin_id, reason, created_at) VALUES (?, ?, ?, NOW())",
                [$userId, $adminId, $reason]
            );

            // Count total active strikes
            $strikeCount = (int) $db->fetch(
                "SELECT COUNT(*) as cnt FROM author_strikes WHERE user_id = ? AND status = 'active'",
                [$userId]
            )['cnt'];

            $penalty = 'warning';

            if ($strikeCount === 1) {
                // Strike 1: Warning
                $penalty = 'warning';
                Mailer::send($userId, 'strike_warning', ['reason' => $reason]);

            } elseif ($strikeCount === 2) {
                // Strike 2: 14-Day Suspension
                $penalty = 'suspension';
                $db->execute(
                    "UPDATE users SET status = 'suspended', suspended_until = DATE_ADD(NOW(), INTERVAL 14 DAY) WHERE id = ?",
                    [$userId]
                );
                // Revoke all active sessions
                SessionManager::revokeAllSessions($userId);
                Mailer::send($userId, 'strike_suspension', ['reason' => $reason, 'duration' => '14 Days']);

            } else {
                // Strike 3: Permanent Ban
                $penalty = 'ban';
                $db->execute("UPDATE users SET status = 'banned' WHERE id = ?", [$userId]);
                SessionManager::revokeAllSessions($userId);
                Mailer::send($userId, 'strike_ban', ['reason' => $reason]);
            }

            $db->commit();
            AuditLog::log("user.strike_issued", "user", $userId, ["strike_count" => $strikeCount, "penalty" => $penalty, "reason" => $reason]);

            return ['strike_count' => $strikeCount, 'penalty' => $penalty];

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function pardonStrike(int $strikeId, int $adminId): bool {
        $db = Database::getInstance();
        $strike = $db->fetch("SELECT user_id FROM author_strikes WHERE id = ?", [$strikeId]);
        if (!$strike) return false;

        $db->execute("UPDATE author_strikes SET status = 'pardoned' WHERE id = ?", [$strikeId]);
        AuditLog::log("user.strike_pardoned", "user", $strike['user_id'], ["strike_id" => $strikeId]);
        return true;
    }
}
```

---

## Complete Method Parameter & Return Signature Table

| Method Signature | Return Type | Purpose & Action |
| :--- | :--- | :--- |
| `findById(int $id)` | `?array` | Resolves user entity by ID. |
| `findByEmail(string $email)` | `?array` | Resolves user entity by email. |
| `create(array $data)` | `int` | Creates user account with BCRYPT password hash. |
| `issueStrike(int $userId, string $reason, int $adminId)` | `array` | Issues strike & escalates 3-strike penalty. |
| `pardonStrike(int $strikeId, int $adminId)` | `bool` | Pardons active strike record. |
| `verifyPassword(string $password, string $hash)` | `bool` | Verifies password against BCRYPT hash. |
| `updateProfile(int $userId, array $data)` | `bool` | Updates profile name, bio, and avatar. |

---

## Detailed QA Test Suite for User Account Model

### Test Case 1: 3-Strike Penalty Escalation Execution
- **QA Test ID**: `TC-USER-001`
- **Target Function**: `User::issueStrike()`
- **Preconditions**: Test user ID 10 is active with 0 strikes.
- **Execution Steps**:
  1. Call `User::issueStrike(10, 'First violation', 1)`. Assert `strike_count === 1`, `penalty === 'warning'`. User status stays `'active'`.
  2. Call `User::issueStrike(10, 'Second violation', 1)`. Assert `strike_count === 2`, `penalty === 'suspension'`. User status updates to `'suspended'`.
  3. Call `User::issueStrike(10, 'Third violation', 1)`. Assert `strike_count === 3`, `penalty === 'ban'`. User status updates to `'banned'`.
- **Expected Result**: User status correctly transitions from `active` -> `suspended` -> `banned`, destroying active sessions at Strike 2 and Strike 3.
