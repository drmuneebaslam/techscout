# TechScout Master QA Testing & Quality Assurance Suite

This master document provides a fully detailed, step-by-step Quality Assurance (QA) testing protocol for every single function, module, HTTP route, database schema, security mechanism, and workflow in TechScout.

---

## Table of Contents
1. [QA Test Execution Environment](#1-qa-test-execution-environment)
2. [Core Framework & Infrastructure Test Suite](#2-core-framework--infrastructure-test-suite)
3. [Authentication, Security & Disciplinary Test Suite](#3-authentication-security--disciplinary-test-suite)
4. [Administrative & Editorial Moderation Test Suite](#4-administrative--editorial-moderation-test-suite)
5. [Author Creator Studio & Tier Engine Test Suite](#5-author-creator-studio--tier-engine-test-suite)
6. [Double-Entry Financial Ledger & Payout Test Suite](#6-double-entry-financial-ledger--payout-test-suite)
7. [AI Directories & Public Portal Test Suite](#7-ai-directories--public-portal-test-suite)
8. [Automated cURL API Verification Commands](#8-automated-curl-api-verification-commands)

---

## 1. QA Test Execution Environment

### Test Credentials Registry
| User Role | Test Email | Password | Access Capabilities |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `admin@techscout.com` | `admin123` | Full access to all admin CRUD, firewall, mailer, backups, payouts |
| **Staff Editor** | `editor@techscout.com` | `editor123` | Editorial submission queue review, content approval |
| **Verified Author** | `author@techscout.com` | `author123` | Creator studio, WYSIWYG editor, earnings, co-authorship |
| **Regular Member** | `member@techscout.com` | `member123` | Directory browsing, bookmarking, upvoting, comments, tipping |

### QA Auth Helper Override
To bypass login screens during visual regression testing on XAMPP:
`http://localhost/techscout.n/index.php?qa_auth=admin`

---

## 2. Core Framework & Infrastructure Test Suite

### 2.1 Front Controller (`index.php`) & Dynamic Base URL Auto-Healing
- **QA Test Case ID**: `TC-CORE-001`
- **Objective**: Verify that `index.php` correctly auto-detects host environment and heals `site_settings.domain_app_url`.
- **Preconditions**: MySQL database active on XAMPP.
- **Test Steps**:
  1. Access `http://localhost/techscout.n/index.php` in a browser.
  2. Inspect database table `site_settings` where `setting_key = 'domain_app_url'`.
- **Expected Result**: `setting_value` equals `http://localhost/techscout.n`.
- **Failure Analysis**: If base URL fails to resolve, check `Request::getSchemeAndHttpHost()`.

### 2.2 PSR-4 Autoloader (`app/autoload.php`)
- **QA Test Case ID**: `TC-CORE-002`
- **Objective**: Ensure namespace `App\` classes load seamlessly without missing file exceptions.
- **Test Steps**:
  1. Instantiate any core class via CLI: `php -r "require 'app/autoload.php'; var_dump(new App\Core\Sanitizer());"`
- **Expected Result**: Returns object instance without fatal error.

### 2.3 PDO Singleton Database Connection & Auto-Healing (`Database.php`)
- **QA Test Case ID**: `TC-CORE-003`
- **Objective**: Verify PDO auto-creates missing database tables and executes `schema.sql` and `seeds.sql`.
- **Test Steps**:
  1. Drop table `topics` in phpMyAdmin.
  2. Refresh homepage `http://localhost/techscout.n/`.
- **Expected Result**: `Database.php` auto-detects missing table, runs schema migration, and recreates `topics` table.

---

## 3. Authentication, Security & Disciplinary Test Suite

### 3.1 CSRF Token Defense (`Session.php`)
- **QA Test Case ID**: `TC-SEC-001`
- **Objective**: Verify POST submissions without valid CSRF tokens are rejected with HTTP 419.
- **Test Command**:
  ```bash
  curl -X POST http://localhost/techscout.n/login \
    -d "email=test@example.com&password=wrong" \
    -i
  ```
- **Expected Result**: Response header returns HTTP 419 CSRF Token Mismatch.

### 3.2 IP Rate Limiting & Firewall (`Firewall.php`)
- **QA Test Case ID**: `TC-SEC-002`
- **Objective**: Verify IP rate limiter blocks excessive rapid requests.
- **Test Steps**: Execute 60 rapid GET requests within 60 seconds to `/login`.
- **Expected Result**: HTTP 429 Rate Limit Exceeded page rendered.

### 3.3 3-Strike Disciplinary Escalation Engine (`User::issueStrike()`)
- **QA Test Case ID**: `TC-SEC-003`
- **Objective**: Test warning -> 14-day suspension -> permanent ban escalation.
- **Test Steps**:
  1. Log in as `admin@techscout.com`.
  2. Issue Strike 1 to target test user. Confirm warning email received.
  3. Issue Strike 2 to target test user. Attempt login as target user.
  4. Issue Strike 3 to target test user. Confirm permanent ban status.
- **Expected Result**: Strike 2 blocks login with 14-day suspension message. Strike 3 sets `status = 'banned'`.

---

## 4. Administrative & Editorial Moderation Test Suite

### 4.1 Editorial Article Queue Approval (`AdminController::reviewArticle()`)
- **QA Test Case ID**: `TC-ADM-001`
- **Objective**: Verify submitted articles transition from `pending` to `published`.
- **Test Steps**:
  1. Log in as author and submit draft article.
  2. Log in as admin, navigate to `/admin/content-queue`.
  3. Click "Approve Article".
- **Expected Result**: Article status becomes `published`, public URL works, and author reputation points increment by +50.

### 4.2 Super-Admin User Account Impersonation (`Auth::impersonate()`)
- **QA Test Case ID**: `TC-ADM-002`
- **Objective**: Test super-admin user impersonation and return-to-admin functionality.
- **Test Steps**:
  1. Log in as `admin@techscout.com`.
  2. Navigate to `/admin/users` and click "Impersonate" on user ID 5.
  3. Verify top notification bar shows impersonation warning.
  4. Click "Stop Impersonating".
- **Expected Result**: User context switches to target user, then reverts back to admin without losing admin session.

---

## 5. Author Creator Studio & Tier Engine Test Suite

### 5.1 Word `.docx` Document Parsing (`DocxParser.php`)
- **QA Test Case ID**: `TC-AUTH-001`
- **Objective**: Verify Microsoft Word `.docx` upload converts to rich HTML content in WYSIWYG editor.
- **Test Steps**:
  1. Open Creator Studio `/author/studio/editor`.
  2. Upload sample `.docx` file containing headings, bold text, and bullet lists.
- **Expected Result**: Editor populates formatted HTML matching Word document structure.

### 5.2 20-Sublevel Author Tier Rank Progression (`AuthorTierEngine.php`)
- **QA Test Case ID**: `TC-AUTH-002`
- **Objective**: Verify author tier sublevel and CPM multiplier recalculates automatically.
- **Test Steps**:
  1. Increment author reputation score from 0 to 500 points.
  2. Execute `AuthorTierEngine::evaluateAuthorTier($authorId)`.
- **Expected Result**: Author moves from Bronze I (1.00x CPM) to Silver I (1.20x CPM).

---

## 6. Double-Entry Financial Ledger & Payout Test Suite

### 6.1 Patronage Tipping Engine (`CreatorTipping.php`)
- **QA Test Case ID**: `TC-FIN-001`
- **Objective**: Verify tipping an author credits author balance and records double-entry ledger rows.
- **Test Steps**:
  1. Log in as member and click "Tip Creator ($5.00)" on an article.
  2. Check `financial_ledger` database table.
- **Expected Result**: Transaction entry `type = 'tip_received'`, `amount = 5.00`, author withdrawable balance increases by $5.00.

### 6.2 Creator Payout Withdrawal Claim (`FinancialLedger::requestPayout()`)
- **QA Test Case ID**: `TC-FIN-002`
- **Objective**: Test creator payout claim submission and administrative processing.
- **Test Steps**:
  1. Author submits payout claim for $50.00 in `/author/rewards`.
  2. Admin approves claim in `/admin/revenue`.
- **Expected Result**: Claim status transitions to `paid`, double-entry debit entry posted to ledger.

---

## 7. AI Directories & Public Portal Test Suite

### 7.1 `/go/{slug}` Affiliate Click Tracking (`DirectoryController::affiliateRedirect()`)
- **QA Test Case ID**: `TC-DIR-001`
- **Objective**: Verify clicking outbound AI Tool links records affiliate click analytics and redirects to destination URL.
- **Test Steps**:
  1. Click "Visit Website" button on AI tool page (`/go/chatgpt`).
  2. Inspect database table `affiliate_clicks`.
- **Expected Result**: Click event recorded with referrer and IP, user redirected to external target URL.

---

## 8. Automated cURL API Verification Commands

### Test 1: API Content Endpoint
```bash
curl -s "http://localhost/techscout.n/api/v1/content?type=tool&limit=5" | jq .
```

### Test 2: Live Search Autocomplete
```bash
curl -s "http://localhost/techscout.n/api/v1/search?q=gpt" | jq .
```

### Test 3: Unread Notification Count
```bash
curl -s "http://localhost/techscout.n/api/v1/notifications" -b "TECHSCOUT_SESSION=qa_session" | jq .
```
