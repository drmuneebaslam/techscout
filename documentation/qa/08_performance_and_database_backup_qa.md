# QA Test Guide 08: Database Backup, Performance & Maintenance QA

This document specifies the step-by-step Enterprise QA testing suite for Database Backup Manager (`app/Models/DatabaseBackupManager.php`), Schema Migration Tools, Error Logger (`app/Core/ErrorLogger.php`), Cache Management, and System Optimization (`views/admin/db_manager.php`).

---

## Pre-Test Setup & Requirements

| Requirement | Value / Condition |
| :--- | :--- |
| **Base URL** | `http://localhost/techscout.n/` |
| **Database Credentials** | MySQL root/user privileges with `mysqldump` and `mysql` CLI available. |
| **Storage Paths** | `/storage/backups/`, `/storage/logs/error.log`. |

---

## Test Suite 1.0: Database Backup & Restoration Manager

### Test Case 1.1: Automated Database Backup Generation (`app/Models/DatabaseBackupManager.php`)
- **Objective**: Verify creation of compressed SQL dump files with checksum validation.

#### Test Execution Steps & Sub-Steps:
1. **Step 1: Admin Trigger Manual Backup**
   - 1.1: Log in as Admin -> Open `/admin/db-manager`.
   - 1.2: Click "Create Full Database Backup" button (`#btn_create_backup`).
   - 1.3: Verify progress modal displays step progress (Dumping Schema -> Dumping Data -> Gzipping Dump).

2. **Step 2: File System & Database Verification**
   - 2.1: Verify `.sql` or `.sql.gz` file created in `/storage/backups/backup_techscout_YYYY-MM-DD_HHMMSS.sql.gz`.
   - 2.2: Confirm backup file size is non-zero (> 2MB for full seed DB).
   - 2.3: Verify record logged in `database_backups` table (`filename`, `filesize`, `created_by = <admin_id>`).

3. **Step 3: Backup Restoration Test**
   - 3.1: On test database instance, select backup file and click "Restore Database".
   - 3.2: Confirm system completes import and all tables/indexes are intact.

---

## Test Suite 2.0: Error Logging & System Diagnostics (`app/Core/ErrorLogger.php`)

### Test Case 2.1: Exception Capture & Log Rotation
- **Objective**: Verify that uncaught PHP exceptions are captured into `/storage/logs/error.log` without exposing sensitive credentials.

#### Test Execution Steps & Sub-Steps:
1. **Step 1: Trigger Controlled Test Exception**
   - 1.1: Request route `/debug/test-error` (or trigger forced DB connection failure).
   - 1.2: Verify user receives clean 500 error page (`views/errors/500.php`) without raw database passwords exposed.

2. **Step 2: Log File Content Inspection**
   - 2.1: Open `/storage/logs/error.log`.
   - 2.2: Confirm log entry contains timestamp, stack trace, class name, and sanitized request parameters.

---

## Pass / Fail Criteria Checklist

- [ ] Database backups generate valid gzip archives without syntax errors.
- [ ] Database restoration restores schema and data perfectly.
- [ ] Raw database credentials are never written to error logs.
- [ ] QA Inspector: TechScout Lead Database Administrator.
