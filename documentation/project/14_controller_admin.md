# 14. Admin Controller (`AdminController.php`)

## File Location & Metadata
- **File Path**: [`app/Controllers/AdminController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/AdminController.php)
- **Primary Role**: Enterprise Administration Panel Controller handling system metrics, editorial reviews, user management, 3-strike disciplinary actions, double-entry financial ledgering, payout processing, firewall management, and database backup operations.
- **Dependencies**: [`app/Core/Auth.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Auth.php), [`app/Models/User.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/User.php), [`app/Models/Content.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/Content.php), [`app/Models/FinancialLedger.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/FinancialLedger.php), [`app/Models/DatabaseBackupManager.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/DatabaseBackupManager.php), [`app/Models/AuditLog.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/AuditLog.php).

---

## Action Method Catalog & Implementation Specs

### 1. `dashboard(Request $request, Response $response)`
- **HTTP Method**: `GET`
- **Route**: `/admin`
- **Access Level**: `super_admin`, `admin`, `editor`
- **Description**: Renders administrative dashboard overview cards, user sign-up trends, platform revenue summaries, pending editorial review counts, active firewall threat counts, and system status indicators.

### 2. `articles(Request $request, Response $response)`
- **HTTP Method**: `GET`
- **Route**: `/admin/content-queue`
- **Access Level**: `super_admin`, `admin`, `editor`
- **Description**: Fetches pending articles from `contents` where `status = 'pending'`, rendering the editorial review interface.

### 3. `reviewArticle(Request $request, Response $response)`
- **HTTP Method**: `POST`
- **Route**: `/admin/content-queue/review`
- **Access Level**: `super_admin`, `admin`, `editor`
- **Form Inputs**: `content_id` (int), `action` (string: `'approve'` | `'reject'` | `'request_changes'`), `editorial_notes` (text).
- **Business Logic**:
  - If `approve`: Updates `status = 'published'`, `published_at = NOW()`. Increments author reputation score by +50. Invokes `AuthorTierEngine::evaluateAuthorTier($authorId)`. Dispatches notification to author.
  - If `reject`: Updates `status = 'rejected'`, records editorial feedback notes.

### 4. `users(Request $request, Response $response)`
- **HTTP Method**: `GET`, `POST`
- **Route**: `/admin/users`
- **Access Level**: `super_admin`, `admin`
- **Description**: User management table supporting instant search filtering, role assignment (`super_admin`, `admin`, `editor`, `author`, `member`), account status updates (`active`, `suspended`, `banned`), strike issuing, user account merging, and super-admin user impersonation.

### 5. `issueStrike(Request $request, Response $response)`
- **HTTP Method**: `POST`
- **Route**: `/admin/users/strike`
- **Access Level**: `super_admin`, `admin`
- **Form Inputs**: `user_id` (int), `reason` (text).
- **Business Logic**: Invokes `User::issueStrike()`, recording strike entry in `author_strikes` table and escalating user penalties (Strike 1: Warning email, Strike 2: 14-day suspension + session revocation, Strike 3: Permanent ban).

### 6. `financialLedger(Request $request, Response $response)`
- **HTTP Method**: `GET`
- **Route**: `/admin/revenue`
- **Access Level**: `super_admin`
- **Description**: Displays global double-entry financial ledger transaction history (`earnings`, `tips`, `payouts`, `platform_fees`), author balances, and pending withdrawal claims.

### 7. `processPayout(Request $request, Response $response)`
- **HTTP Method**: `POST`
- **Route**: `/admin/revenue/payout/process`
- **Access Level**: `super_admin`
- **Form Inputs**: `payout_id` (int), `action` (string: `'approve'` | `'reject'`), `transaction_reference` (string).
- **Business Logic**: Executes double-entry ledger transaction in `FinancialLedger`, reducing author withdrawable credit balance and sending payout confirmation email.

### 8. `firewall(Request $request, Response $response)`
- **HTTP Method**: `GET`, `POST`
- **Route**: `/admin/firewall`
- **Access Level**: `super_admin`
- **Description**: Configures IP rate-limiting decay windows, reviews threat detection logs, and manages blacklisted IP addresses in `banned_ips`.

### 9. `databaseBackup(Request $request, Response $response)`
- **HTTP Method**: `GET`, `POST`
- **Route**: `/admin/db-manager`
- **Access Level**: `super_admin`
- **Description**: Triggers on-demand MySQL database exports via `DatabaseBackupManager::generateBackup()`, lists backup archives, and handles database restoration commands.

---

## Complete Method Parameter & Return Signature Table

| Method Name | Parameters | Return Type | Description |
| :--- | :--- | :--- | :--- |
| `dashboard()` | `Request $req, Response $res` | `string` (HTML) | Renders admin dashboard homepage. |
| `articles()` | `Request $req, Response $res` | `string` (HTML) | Editorial review queue view. |
| `reviewArticle()` | `Request $req, Response $res` | `void` (Redirect) | Approves or rejects submitted article. |
| `users()` | `Request $req, Response $res` | `string` (HTML) | User account management view. |
| `issueStrike()` | `Request $req, Response $res` | `void` (Redirect) | Issues disciplinary strike to user. |
| `pardonStrike()` | `Request $req, Response $res` | `void` (Redirect) | Pardons an existing user strike. |
| `mergeUsers()` | `Request $req, Response $res` | `void` (Redirect) | Merges secondary user account into primary. |
| `financialLedger()`| `Request $req, Response $res` | `string` (HTML) | Financial ledger audit view. |
| `processPayout()` | `Request $req, Response $res` | `void` (Redirect) | Processes creator payout claim. |
| `firewall()` | `Request $req, Response $res` | `string` (HTML) | Security firewall configuration view. |
| `databaseBackup()` | `Request $req, Response $res` | `string` (HTML) | Database backup management view. |

---

## Detailed QA Test Suite for Admin Controller

### Test Case 1: Editorial Article Approval Workflow
- **QA Test ID**: `TC-ADM-001`
- **Target Function**: `AdminController::reviewArticle()`
- **Preconditions**: Logged in as `admin@techscout.com`. A pending article exists in `contents`.
- **Execution Steps**:
  1. Submit POST request to `/admin/content-queue/review`:
     ```bash
     curl -X POST http://localhost/techscout.n/admin/content-queue/review \
       -b "TECHSCOUT_SESSION=admin_session" \
       -d "csrf_token=VALID_TOKEN&content_id=12&action=approve" \
       -i
     ```
  2. Verify database record: `SELECT status FROM contents WHERE id = 12;`
- **Expected Result**: Article status updates from `'pending'` to `'published'`. Author reputation score increments by +50 points.
- **Assertion**: `status === 'published'`.

### Test Case 2: Disciplinary Strike Issuance & Penalty Escalation
- **QA Test ID**: `TC-ADM-002`
- **Target Function**: `AdminController::issueStrike()`
- **Execution Steps**:
  1. Issue Strike 2 to target user ID 8 via `/admin/users/strike`.
  2. Query `author_strikes` table for user ID 8.
  3. Attempt login as user ID 8.
- **Expected Result**: User status becomes `'suspended'`, active sessions revoked, and login blocked with 14-day suspension notice.
