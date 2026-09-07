# 37. Database Backup & Disaster Recovery (`DatabaseBackupManager.php`)

## File Location
[`app/Models/DatabaseBackupManager.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/DatabaseBackupManager.php)

## Key Functions
- `generateBackup()`: Exports full MySQL database schema and table rows into `.sql` dump archives.
- `listBackups()`: Returns available backup file archives.
- `restoreBackup($filename)`: Restores database state from export archive.
