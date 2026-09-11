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

### 🔴 MISTAKE-02: Logging `DATABASE_SILENT_ERROR` into Database Error Table
* **Root Cause:** `Database::executeSilent()` was catching query failures and calling `\App\Core\ErrorLogger::log('DATABASE_SILENT_ERROR', ...)`.
* **Consequences:**
  - Non-critical telemetry queries (or fallback queries) flooded the database `system_error_logs` table with "DATABASE_SILENT_ERROR" entries.
  - Turned non-critical, expected fallback operations into alarming red badges in the Admin System Diagnostics UI.
* **Mandatory Rule:**
  - `Database::executeSilent()` must be truly silent. It must **NEVER** log to the user-facing `system_error_logs` database table.
  - If low-level tracing is needed, write to standard PHP `error_log()`, never creating database error records for silent operations.

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

## 2. Response Audit Protocol
At the conclusion of any code generation, the agent must cross-check every modified file against:
1. [ ] **No Runtime DDL**: Are there any `ALTER TABLE` or `CREATE TABLE` calls in application request paths?
2. [ ] **No Silent Error Generation**: Does `executeSilent` or any helper generate database error log records?
3. [ ] **Safe JSON Handling**: Are all JSON column values guaranteed to be valid JSON strings (defaulting to `'[]'` or valid JSON, never `false` or empty string)?
4. [ ] **Graceful Telemetry Operations**: Are auxiliary telemetry queries (like `post_views`) guarded so they never crash requests?
5. [ ] **Non-Destructive Upgrade SQL Provided**: Has an upgrade SQL script been provided for any database schema changes that preserves all existing data?
