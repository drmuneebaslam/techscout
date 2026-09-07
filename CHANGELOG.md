# TechScout Platform Changelog

All notable changes and architectural updates to the platform are documented in this file.

---

## [2026-09-08] - Enterprise Documentation Portal, Host Diagnostic Isolation & Comprehensive Log Audit

### 1. Enterprise Web Documentation Reader & QA Portal
- **`documentation/index.php`**:
  - Integrated server-side PHP Markdown rendering engine supporting GitHub alert callouts (`> [!NOTE]`, `[!TIP]`, `[!IMPORTANT]`, `[!WARNING]`, `[!CAUTION]`), task checklists, scroll-spy TOC, and syntax-highlighted code blocks.
  - Added reading progress indicator, dark/light mode toggle with theme sync, font scaling (`sm`/`md`/`lg`), distraction-free focus mode, and interactive command palette search (`Ctrl+K`).
- **Documentation Reference Suite**:
  - Added full reference suites in `documentation/`, `documentation/project/`, and `documentation/qa/` covering core architecture, controller mappings, database schemas, symbol catalogs, and master QA testing specifications.

### 2. Host Diagnostics & Fail-Safe Database Recovery Engine
- **`app/Core/Database.php`**:
  - Enhanced connection diagnostic exception handler (`renderDetailedError`) with detailed SQL state diagnostics, host telemetry, source location tracking, and auto-repair hooks.
  - Prevents continuous reconnect loops and memory exhaustion when remote host DNS resolution fails.

---

## [2026-09-07] - Favicon 404 Resolution, Global Script Scope Bindings & Navigation Enhancements

### 1. Favicon 404 Elimination & Binary Favicon Generation
- **`favicon.ico` & `assets/favicon.svg`**:
  - Generated high-resolution binary `favicon.ico` and SVG favicon assets.
- **`index.php`**:
  - Added direct 200 stream binary header response for `/favicon.ico` requests, preventing 404 favicon logs across admin and client portals.
- **`views/layouts/admin_header.php` & `views/layouts/header.php`**:
  - Added explicit `<link rel="icon">` and `<link rel="shortcut icon">` tags referencing SVG and ICO assets.

### 2. Admin Sidebar & Navigation Optimization
- **`views/layouts/admin_header.php`**:
  - Streamlined sidebar hierarchy by eliminating duplicate *Author Applications* link from *Command & Intelligence*, confining it strictly to *Community & Support*.
  - Restored uniform navigation visibility and permission access for all admin roles (`admin`, `super_admin`, `superadmin`).

### 3. PJAX Script Evaluator & Scope Isolation Fixes
- **`views/layouts/admin_footer.php`**:
  - Upgraded PJAX script evaluator to inject scripts via `document.head` and replace top-level `const`/`let` with `var`, avoiding duplicate identifier syntax errors on repeated PJAX page switches.
- **`views/admin/mail.php`**:
  - Bound `MAIL_TEMPLATES`, `switchMailTab`, and modal handlers directly to `window` scope.
- **`views/admin/users.php`**:
  - Explicitly attached `openUserDetailModal`, `openAuditTrailDrawer`, and all modal/drawer handlers to `window` scope.

---

## [2026-09-06] - Admin Single-Record Table Deletion, Post-Signup OTP Email Verification, Click-to-Reset Password Link & Responsive Notification Center

### 1. Granular Admin Single-Record Deletion Across All Tables
- **`AdminController::deleteMailLog` & `views/admin/mail.php`**:
  - Added dedicated per-row delete action (`POST /admin/mail/log/delete/{id}`) allowing administrators to delete single email delivery records with confirmation.
- **`AdminController::deleteSystemLog` & `views/admin/system_logs.php`**:
  - Added per-row delete action (`POST /admin/system-logs/delete/{id}`) for individual runtime exception telemetry records.
- **`AdminController::deleteAuditLog` & `views/admin/audit_logs.php`**:
  - Added individual deletion button (`POST /admin/audit-logs/delete/{id}`) for specific forensic audit trail entries.
- **`AdminController::deleteUserSubscription` & `views/admin/subscribers.php`**:
  - Added single-record deletion (`POST /admin/subscribers/record/delete/{id}`) for topic/author/category subscriptions in the live follow stream.
- **`AdminController::deleteJobApplication` & `views/admin/job_applications.php`**:
  - Added delete application action (`POST /admin/jobs/applications/delete/{id}`) for individual candidate submissions.

### 2. Post-Signup 6-Digit OTP Email Verification Screen
- **`AuthController::register`**:
  - Users are now registered with `status = 'pending_verification'` and a cryptographically generated 6-digit `verification_code`.
  - Sends the `email_verification` template and redirects the new user immediately to `/verify-email`.
- **`AuthController::verifyEmail`**:
  - Validates user-submitted OTP against the stored database `verification_code`.
  - Upon valid matching, sets `email_verified_at = NOW()`, updates `status = 'active'`, clears code, dispatches `account_activated` confirmation email, refreshes session, and redirects to home.
- **`views/auth/verify_email.php`**:
  - Rebuilt with a high-contrast, responsive card featuring 6 individual auto-advancing digit inputs, paste support (splitting 6 digits across inputs), 60-second cooldown timer on resend, and spam folder tips.

### 3. Forgot Password Email Updated to Direct Click-to-Reset Link
- **`Mailer.php` (`password_reset` template)**:
  - Replaced prominent raw hex PIN code with a high-visibility, primary "Click Here to Reset Password &rarr;" call-to-action button.
  - Added fallback clickable plaintext link in a clean code box for seamless copy-pasting, complete with 2-hour expiration notice and security advisory.

### 4. Notification Screen Full Mobile Responsiveness Overhaul
- **`views/notifications/index.php`**:
  - Added comprehensive `@media (max-width: 768px)` and `@media (max-width: 480px)` responsive architecture:
    - **Header & Hero**: Collapsible padding, fluid title typography, stacked full-width touch-friendly actions.
    - **Telemetry KPI Grid**: Adapts cleanly from multi-column desktop grid to 2-column mobile layout.
    - **Filter Ribbon**: Smooth horizontal swipe with hidden scrollbars for one-touch category toggling on phones.
    - **Search Toolbar**: Full-width input and responsive pagination controls.
    - **Notification Row Items (`.notif-item-row`)**: Stacks icon, title, message body, and action buttons vertically on mobile devices, ensuring full readability without truncation or horizontal overflow.
    - **Detail Reader Modal (`#notifDetailModal`)**: Responsive width up to 95vw with scrollable modal body and accessible buttons.

---

## [2026-09-06] - Universal Mail Envelope System, Block-Based Component Builder & Payout File Attachments

### 1. Universal Envelope System & Auto Light Mode Sanitization
- **`Mailer::wrapUniversalEnvelope`**:
  - Implemented an automatic universal background & shell system so new templates can be written as simple inner HTML blocks without boilerplate `<html>` or wrapper duplication.
  - Automatically wraps inner content in a crisp white card (`#ffffff`), subtle grid lines (`rgba(99,102,241,0.08)` 32px grid), TechScout brand header, and official platform footer.
  - Fully backward compatible with legacy full-document templates.
- **`Mailer::sanitizeLightMode`**:
  - Automatically sanitizes dark backgrounds (`#0f172a`, `#05070b`, `#1e293b`) and dim text (`#f8fafc`, `#cbd5e1`, `#94a3b8`) across all email templates and legacy database template rows into vibrant, high-contrast light styling (`#ffffff`, `#f8fafc`, `#0f172a`, `#334155`).

### 2. Modern Block-Based Component Builder (Template Studio)
- Added an interactive **Universal Block Component Library** in `views/admin/mail.php` (`#templateEditorModal`):
  - **Hero Header**: High-contrast title and greeting block.
  - **Info Callout**: Crisp blue alert box with left accent border.
  - **Success Box**: Green confirmation block with checkmark.
  - **Warning Box**: Amber compliance attention notice.
  - **Primary CTA Button**: Modern indigo gradient action button.
  - **Disbursal / Action Button**: Green payout receipt ledger action button.
  - **Key-Value Data Grid**: Light slate data card with borders and status pill.
  - **OTP PIN Badge**: Mono-spaced 6-digit verification box with expiration badge.
- Live template preview iframe upgraded to automatically sanitize dark styles and render within the universal light envelope.

### 3. Physical Receipt & Document Attachments for Creator Payouts
- **`AdminController::processAuthorPayout`**:
  - Uploaded receipt proof (`receipt_file` via `multipart/form-data`) is physically attached to the `payout_approved` email dispatch (`Mailer::sendTemplate`).
  - Automatically handles both newly uploaded receipts and existing stored receipts from `/assets/uploads/receipts/`.
  - Attaches actual binary receipt files (PDF, PNG, JPG, WEBP) directly into the outgoing email so creators receive their payment confirmation slip in Gmail/client inbox.

### 4. Database Light Mode Migration Script
- Updated `database/mail_tracking_upgrade.sql` with queries to clean and convert any older custom rows in `mail_templates` table to light mode automatically upon running the upgrade.

---

## [2026-09-06] - Extended Mail System, Pure Light Mode Templates, Telemetry HUD & Timezone Engine

### 1. Pure Light Mode Mail Templates & Grid Lines (Zero Dark Boxes)
- Converted all built-in email templates in [`app/Core/Mailer.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Mailer.php) (`welcome`, `email_verification`, `account_activated`, `login_alert`, `password_reset`, `security_alert`, `payout_requested`, `payout_approved`, `payout_rejected`, `tip_received`, `author_application_received`, `article_submitted`, `article_rejected`, `comment_reply_alert`, etc.) to **100% pure light mode**:
  - Outer background: `#ffffff` with subtle indigo grid lines (`rgba(99,102,241,0.08)` 32px grid).
  - Main container: pure white card (`#ffffff`), subtle 1px border (`#e2e8f0`), soft shadow, zero dark boxes (`#0f172a`, `#05070b`).
  - Typography: crisp dark slate headings (`#0f172a`), neutral slate body text (`#334155`), and subtle muted captions (`#64748b`).
  - Resource boxes & code blocks: light slate background (`#f8fafc`) with subtle grid accents and crisp borders.
  - Live simulation frame in [`views/admin/mail.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/mail.php): upgraded to pure white canvas with light header and grid lines.

### 2. Comprehensive Open & Click Tracking for General Platform Mail
- Extended tracking engine in [`app/Core/Mailer.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Mailer.php):
  - In `Mailer::injectTracking`: now seamlessly supports both newsletter broadcasts (`/newsletter/track/...`) and general platform mail (`/mail/track/...`).
  - Generates a unique 32-character hexadecimal tracking token (`bin2hex(random_bytes(16))`) for every single outgoing email.
  - Injects transparent 1x1 tracking pixel and rewrites clickable links for click tracking.
- Added unauthenticated public tracking routes in [`index.php`](file:///c:/xampp/htdocs/techscout.n/index.php):
  - `GET /mail/track/open/{token}` -> `AdminController::trackMailOpen`: increments `open_count`, sets `opened_at`, captures `user_ip` & `user_agent`, and outputs a zero-cache 1x1 transparent GIF.
  - `GET /mail/track/click/{token}` -> `AdminController::trackMailClick`: increments `click_count`, sets `clicked_at`, logs `last_clicked_url`, `user_ip`, `user_agent`, and redirects via 302 to destination URL.

### 3. Minimized Delivery Logs Table & 10x Detailed Executive Telemetry HUD
- **Delivery Logs Table**: Minimized and compact layout with micro engagement badges:
  - Status column shows `SENT ✓` or `FAILED ❌` with micro badges: `👁️ 2x` (opens) and `🔗 1x` (clicks).
  - Shows recipient email & name, subject with template key tag, sender credential channel badge, and localized dispatch time.
- **10x Executive Telemetry Inspector Modal (`sentMailModal`)**:
  - **Tracking Token Bar**: Displays unique token with one-click copy and live open/click summary counter.
  - **6 Rich Metadata Cards**: Sent By (Sender & Profile), Delivered To (Recipient), Open Engagement Telemetry (First opened time, total opens, reader IP, user-agent), Click Engagement Telemetry (First clicked time, total clicks, last clicked URL), Coordinates (Log ID, MsgID, Transport), and Timing.
  - **Subtabs**:
    - `[👁️ Email Sandbox]`: Sandboxed pure light-mode preview rendered in an iframe with fullscreen & copy HTML tools.
    - `[💻 Technical Payload (JSON)]`: Complete formatted JSON database record with 1-click copy.

### 4. Elimination of Ghost "2 attachments • Scanned by Gmail"
- Root Cause: Passing associative arrays (`['links' => [], 'files' => []]`) or link dictionaries to mail dispatchers caused parsers to treat associative keys as separate attachments; furthermore, pushing both `text/plain` and `text/html` without explicit alternative boundaries in edge relays caused Gmail to expose the plain text as an attachment.
- Solution:
  - Added strict attachment sanitization in `Mailer::send`: filters out empty arrays, links, and non-file dictionaries, strictly verifying non-empty binary content or local file paths before dispatching.
  - Updated `waste/cloudflare_worker/worker.js` to only push HTML (or plain text fallback) rather than duplicate parts.

### 5. Automatic Client-Side Timezone Detection & Dynamic Localization
- Added automatic browser timezone detection script in [`views/layouts/admin_header.php`](file:///c:/xampp/htdocs/techscout.n/views/layouts/admin_header.php) and [`views/layouts/header.php`](file:///c:/xampp/htdocs/techscout.n/views/layouts/header.php):
  - Reads `Intl.DateTimeFormat().resolvedOptions().timeZone` and writes `user_timezone` cookie.
  - [`app/Core/DateHelper.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/DateHelper.php) checks `$_COOKIE['user_timezone']` to dynamically render timestamps in the exact local time for any user worldwide without a 12-hour offset.

### 6. Notification Left-Click 404 Resolution
- Root Cause: Hardcoded `RewriteBase /techscout.n/` in `.htaccess` caused Apache on root domains (e.g. `funwithcoding.free.nf`) to prepend `/techscout.n/` to the rewrite target, causing 404 errors.
- Solution:
  - Removed hardcoded `RewriteBase` from [`.htaccess`](file:///c:/xampp/htdocs/techscout.n/.htaccess).
  - Added URL normalization in [`views/layouts/header.php`](file:///c:/xampp/htdocs/techscout.n/views/layouts/header.php) and [`views/notifications/index.php`](file:///c:/xampp/htdocs/techscout.n/views/notifications/index.php) to safely strip stale `/tech/` or `/techscout.n/` prefixes.
  - Added automated database auto-healing queries in [`app/Core/Database.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Database.php) and [`database/mail_tracking_upgrade.sql`](file:///c:/xampp/htdocs/techscout.n/database/mail_tracking_upgrade.sql).

### 7. Mail Profile Duplication & Standalone SQL File
- Added `duplicateMailCredential` endpoint (`POST /admin/mail/credentials/duplicate/{id}`) in [`app/Controllers/AdminController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/AdminController.php) and [`index.php`](file:///c:/xampp/htdocs/techscout.n/index.php).
- Delivered production database script [`database/mail_tracking_upgrade.sql`](file:///c:/xampp/htdocs/techscout.n/database/mail_tracking_upgrade.sql) for running standalone migrations.

---

## [2026-09-06] - Fix: Mail Logs Schema Auto-Healing & Fail-Safe Newsletter Dispatch

### Problem Addressed
- An uncaught SQL error (`SQLSTATE[42S22]: Column not found: 1054 Unknown column 'from_email' in 'INSERT INTO'`) was triggered when sending newsletters or platform broadcasts via `/admin/newsletters/send-message`.
- On remote/existing MySQL database instances (e.g. InfinityFree hosting), the `mail_logs` table had not yet been migrated to include `from_email`, `from_name`, and `credential_name`.
- Because `Database::execute()` intercepts PDO exceptions and immediately invokes `renderDetailedError()`, logging failures halted script execution and rendered the database diagnostic HUD instead of allowing the newsletter to complete.

### Root Cause
1. `app/Core/Database.php` schema verification block #17 for `mail_logs` was missing column definitions and `ALTER TABLE` checks for `from_email`, `from_name`, and `credential_name`.
2. `app/Core/Mailer.php` was using `Database::execute()` without silent execution fallback, preventing graceful degradation when database schema differences occurred.

### Solutions Implemented
1. **Silent Execution Engine (`Database::executeSilent` & `Database::fetchSilent`)**:
   - Added `Database::executeSilent(string $sql, array $params = []): bool` to [`app/Core/Database.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Database.php).
   - Silently catches exceptions, logs them to `ErrorLogger` if available, and returns `false` without terminating the request or rendering the error HUD.
2. **Automated Schema Auto-Migration for `mail_logs`**:
   - Updated `mail_logs` table creation in [`app/Core/Database.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Database.php) to include:
     - `from_email VARCHAR(191) NULL AFTER to_name`
     - `from_name VARCHAR(150) NULL AFTER from_email`
     - `credential_name VARCHAR(150) NULL AFTER driver`
   - Added `ALTER TABLE` checks in the migration sequence to automatically add these columns to any existing `mail_logs` table if they are missing.
3. **Multi-Tiered Fail-Safe Mail & Newsletter Logging**:
   - Updated [`app/Core/Mailer.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Mailer.php) in `send()` and `sendBatch()`:
     - **Tier 1**: Attempts to insert with modern schema (`from_email`, `from_name`, `credential_name`) using `executeSilent`.
     - **Tier 2 (Auto-Heal)**: If the insert fails, immediately executes `ALTER TABLE mail_logs ADD COLUMN ...` statements silently to heal the database schema.
     - **Tier 3 (Legacy Fallback)**: If columns cannot be added (e.g., restricted hosting permissions), safely falls back to inserting into legacy columns without `from_*` / `credential_name`.
     - **Result**: Newsletter sending and mail dispatch will **never** crash or be interrupted due to logging table schema differences.

### Modified Files for this Request
- [`changes/app/Core/Database.php`](file:///c:/xampp/htdocs/techscout.n/changes/app/Core/Database.php) & [`app/Core/Database.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Database.php)
- [`changes/app/Core/Mailer.php`](file:///c:/xampp/htdocs/techscout.n/changes/app/Core/Mailer.php) & [`app/Core/Mailer.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Mailer.php)

### Previous Session Changes Synchronized to Real Paths
All changes from prior sessions were verified, reconstructed from IDE session logs, and confirmed identical in real project files:
1. `views/admin/newsletters.php`: Reconstructed complete 93,690-byte newsletter engine with broadcast telemetry, live dispatch, and campaign logs.
2. `app/Models/CreatorTipping.php`: User join query fix for author `display_name` and `username`.
3. `app/Controllers/AuthController.php`: Auto-dispatch for welcome onboarding and password alert notifications.
4. `views/admin/mail.php`: Upgraded executive inspect modal, minimized delivery logs table, and single batch save for template credentials.
5. `app/Controllers/AdminController.php`: Batch save credentials endpoint and clear logs handlers.
6. `index.php`: Updated router mappings.

