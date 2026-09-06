# TechScout Platform Changelog

All notable changes and architectural updates to the platform are documented in this file.

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
