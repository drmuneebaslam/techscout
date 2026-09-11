# TechScout AI Engineering: Mistakes Learned & Prevention Rules

This document serves as the permanent registry of architectural and implementation mistakes identified across production releases, their root causes, and mandatory prevention rules. Every response and code modification must be audited against these rules prior to delivery.

---

## 1. Registry of Mistakes & Architectural Rules

### 🔴 MISTAKE-01: Runtime DDL in Application Request Lifecycle
* **Root Cause:** Executing DDL (`ALTER TABLE`, `CREATE TABLE`) dynamically inside web request handlers (controllers, mail dispatchers, telemetry loops) or during `Database::runIncrementalMigrations`.
* **Consequences:** 
  - Causes `SQLSTATE[42S21]: 1060 Duplicate column name` when columns already exist or during race conditions.
  - Causes table metadata locking and slows down user requests.
  - Free hosting or restricted database users (e.g. InfinityFree) often deny or throttle DDL during runtime.
  - An exception inside an incremental migration bails out the entire chain, leaving subsequent tables (e.g. `post_views`, `mail_templates`) uncreated.
* **Mandatory Rule:**
  - **NEVER** execute runtime `ALTER TABLE` or `CREATE TABLE` inside application controllers, service classes, or mail delivery functions.
  - When database changes or new tables/columns are needed, provide a dedicated, non-destructive upgrade SQL script (using `IF NOT EXISTS`, safe column additions, preserving all existing user records) for execution in phpMyAdmin / MySQL CLI.

---

### 🔴 MISTAKE-02: Silent Database Error Logging & Root-Cause Elimination
* **Root Cause:** Queries in `Database::executeSilent()` or fallback paths failed due to unmigrated columns, non-canonical table names, or duplicate index definitions, while either completely hiding genuine database corruptions or polluting error tables with benign warnings.
* **Consequences:**
  - Masking errors completely leaves developers unaware when queries are broken.
  - Logging non-fatal, expected idempotent operations (such as duplicate indexes) creates noise.
* **Mandatory Rule:**
  - **Eliminate Root Causes First:** Ensure schema, column names, and queries are verified so that silent errors do not occur in normal application flow.
  - **Selective Telemetry Logging:** `Database::executeSilent()` should log genuine, unexpected query failures to `system_error_logs` with contextual SQL and error details, but benign idempotent DDL warnings (e.g., duplicate index/column) should be filtered so production logs only contain actionable defects.

---

### 🔴 MISTAKE-03: Unsanitized JSON Serialization & Strict Check Constraints
* **Root Cause:** In MariaDB, `JSON` is an alias for `LONGTEXT ... CHECK (json_valid(col))`. If `json_encode()` encountered non-UTF8 strings or returned `false`, PDO bound `false` as `""` (empty string). MariaDB evaluates `json_valid('') = 0`, failing constraint 4025: `CONSTRAINT mail_logs.attachments_json failed`.
* **Consequences:**
  - Email logging failed whenever attachments or telemetry were dispatched, triggering silent error loops.
* **Mandatory Rule:**
  - In PHP: Always ensure UTF-8 encoding before `json_encode()`. If `json_encode()` fails or yields empty, default to `'[]'`. Never pass `false` or `""` into a JSON column.
  - In SQL: Modify `attachments_json` to `LONGTEXT NULL` and avoid strict check constraints that can reject edge-case payloads.

---

### 🔴 MISTAKE-04: Missing Tables in Production Without Upgrade SQL
* **Root Cause:** New telemetry features (like `post_views` synthetic view logging in `AdminController::simulateEngagement`) were added to code while the production database did not yet have the table.
* **Consequences:**
  - Threw fatal `SQLSTATE[42S02]: Base table or view not found: 1146 Table 'post_views' doesn't exist`.
* **Mandatory Rule:**
  - Application code querying auxiliary/telemetry tables must handle table absence gracefully (e.g. check table existence or safe try/catch without throwing 500 errors).
  - All new tables must be included in `database/schema.sql` and shipped in a standalone non-destructive upgrade script.

---

### 🔴 MISTAKE-05: Querying Obsolete or Divergent Table Names
* **Root Cause:** A previous revision referenced `custom_mail_templates` in `AdminController::mailTemplateEditor` when the official table in the system is `mail_templates`.
* **Consequences:**
  - Produced `SQLSTATE[42S02]: Base table not found: 1146 Table 'custom_mail_templates' doesn't exist`.
* **Mandatory Rule:**
  - Always cross-reference table and column names with canonical schema definitions (`database/schema.sql`).
  - Standardize on single canonical table names across all controllers, views, and models.

---

### 🔴 MISTAKE-06: Unclosed JavaScript Blocks Breaking View Execution Scope
* **Root Cause:** Inline `<script>` tags in PHP view templates containing unclosed `try...catch` blocks or orphaned brackets (e.g., `views/admin/mail.php` line 2806 and `views/layouts/admin_footer.php` line 448).
* **Consequences:**
  - Causes browser parsing failures: `Uncaught SyntaxError: Unexpected end of input` or `Unexpected token ')'`.
  - Halts script execution before global window methods are exported (`window.switchMailTab`, `window.loadTemplateToEditor`), leading to cascading HTML inline handler failures (`ReferenceError: switchMailTab is not defined`).
* **Mandatory Rule:**
  - All JavaScript written inside PHP view files must be syntax-checked with Node.js parser (`node -c`) before staging.
  - Global functions called by inline HTML attributes (`onclick`, `onchange`) must be explicitly declared and protected against parsing halts.

---

### 🔴 MISTAKE-07: Unchecked Reaction Counters Lacking Idempotent Toggle / Undo
* **Root Cause:** Comment upvote/helpful actions simply ran `UPDATE comments SET reaction_count = reaction_count + 1` without recording individual user or guest IP associations.
* **Consequences:**
  - A single user or spammer clicking the helpful button multiple times caused the counter to increase infinitely.
  - Users had no ability to undo or revoke an accidental reaction.
* **Mandatory Rule:**
  - All reaction and helpful interactions must maintain a relational record (e.g., `comment_reactions` with `comment_id`, `user_id`, and `ip_address`).
  - The API endpoint must act as an idempotent toggle: if a record exists for the user/IP, remove it and decrement the counter (`GREATEST(0, count - 1)`); if not, insert it and increment the counter.

### 🔴 MISTAKE-08: Hardcoded Static Error Pages Omitting Core Branding & Dynamic PHP
* **Root Cause:** Creating standalone `.html` files for error pages without bootstrapping the application or including `views/layouts/header.php` and `footer.php`.
* **Consequences:**
  - Users visiting error pages (403, 404, 405, 429, 500) saw disconnected, isolated screens lacking platform navigation, active session context, search, and corporate branding.
  - Adding Apache `ErrorDocument` directives into `.htaccess` conflicts with hosting control panels (e.g. InfinityFree / cPanel) where custom error pages are configured directly.
* **Mandatory Rule:**
  - Custom error pages must be executable PHP files that load application bootstrap and embed the official `header.php` and `footer.php` templates.
  - Do not force Apache `ErrorDocument` directives inside `.htaccess` when the hosting environment requires manual control panel mapping.

---

## 2. Response Audit Protocol
At the conclusion of any code generation, the agent must cross-check every modified file against:
1. [ ] **No Runtime DDL**: Are there any `ALTER TABLE` or `CREATE TABLE` calls in application request paths?
2. [ ] **No Unchecked Silent Errors**: Are database operations verified so silent errors do not occur, while keeping meaningful error logging intact?
3. [ ] **Safe JSON Handling**: Are all JSON column values guaranteed to be valid JSON strings (defaulting to `'[]'` or valid JSON, never `false` or empty string)?
4. [ ] **Graceful Telemetry Operations**: Are auxiliary telemetry queries (like `post_views`) guarded so they never crash requests?
5. [ ] **Non-Destructive Upgrade SQL Provided**: Has an upgrade SQL script been provided for any database schema changes that preserves all existing data?
6. [ ] **Syntax-Verified Scripts**: Are all JavaScript view scripts and inline blocks verified with a JS parser to prevent `SyntaxError` and `ReferenceError`?
7. [ ] **Idempotent Reaction Toggling**: Do user reaction/helpful counters maintain state via user/IP tracking with toggle (add/undo) functionality?
8. [ ] **Branded Dynamic Error Pages**: Are error pages written as dynamic PHP scripts incorporating official header/footer layouts?

