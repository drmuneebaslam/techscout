<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Config;
use App\Models\AuditLog;

class DatabaseBackupManager {

    /**
     * Get backup directory path
     */
    public static function getBackupDir(): string {
        $dir = dirname(__DIR__, 2) . '/assets/backups';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        return $dir;
    }

    /**
     * Generate a full database SQL backup snapshot
     */
    public static function createBackup(?int $createdByUserId = null): array {
        $pdo = Database::getConnection();
        $tables = Database::query("SHOW TABLES");
        $dbName = Config::get('db_name', 'techscout_db');
        
        $backupDir = self::getBackupDir();
        $filename = 'techscout_backup_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.sql';
        $filepath = $backupDir . '/' . $filename;

        $handle = fopen($filepath, 'w');
        if (!$handle) {
            throw new \RuntimeException("Unable to open backup file for writing.");
        }

        // Header
        fwrite($handle, "-- ========================================================\n");
        fwrite($handle, "-- TechScout Enterprise Disaster Recovery Snapshot\n");
        fwrite($handle, "-- Generated: " . date('Y-m-d H:i:s') . "\n");
        fwrite($handle, "-- Database: {$dbName}\n");
        fwrite($handle, "-- ========================================================\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS = 0;\n");
        fwrite($handle, "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n");
        fwrite($handle, "SET NAMES utf8mb4;\n\n");

        $totalRows = 0;
        $tableCount = 0;

        foreach ($tables as $tableRow) {
            $tableName = reset($tableRow);
            if (!$tableName) continue;

            $tableCount++;

            // 1. Table Schema
            $createTable = Database::fetch("SHOW CREATE TABLE `{$tableName}`");
            $createSql = $createTable['Create Table'] ?? null;
            if ($createSql) {
                fwrite($handle, "--\n-- Structure for table `{$tableName}`\n--\n");
                fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");
                fwrite($handle, $createSql . ";\n\n");
            }

            // 2. Table Data
            $rows = Database::query("SELECT * FROM `{$tableName}`");
            $rowCount = count($rows);
            $totalRows += $rowCount;

            if ($rowCount > 0) {
                fwrite($handle, "--\n-- Dumping data for table `{$tableName}` ({$rowCount} rows)\n--\n");
                $columns = array_keys($rows[0]);
                $colList = '`' . implode('`, `', $columns) . '`';

                // Batch insert chunks of 50 rows
                $chunks = array_chunk($rows, 50);
                foreach ($chunks as $chunk) {
                    $insertSql = "INSERT INTO `{$tableName}` ({$colList}) VALUES \n";
                    $valueLines = [];

                    foreach ($chunk as $row) {
                        $values = [];
                        foreach ($row as $val) {
                            if ($val === null) {
                                $values[] = 'NULL';
                            } elseif (is_numeric($val)) {
                                $values[] = $val;
                            } else {
                                $values[] = $pdo->quote((string)$val);
                            }
                        }
                        $valueLines[] = "(" . implode(', ', $values) . ")";
                    }

                    $insertSql .= implode(",\n", $valueLines) . ";\n";
                    fwrite($handle, $insertSql);
                }
                fwrite($handle, "\n");
            }
        }

        fwrite($handle, "\nSET FOREIGN_KEY_CHECKS = 1;\n");
        fwrite($handle, "-- Snapshot completed successfully.\n");
        fclose($handle);

        $fileSizeBytes = filesize($filepath);
        $checksum = hash_file('sha256', $filepath);

        // Record in database
        Database::execute(
            "INSERT INTO database_backups (filename, filepath, file_size_bytes, table_count, total_rows, compression, checksum, created_by, created_at)
             VALUES (?, ?, ?, ?, ?, 'sql', ?, ?, NOW())",
            [$filename, $filepath, $fileSizeBytes, $tableCount, $totalRows, $checksum, $createdByUserId]
        );

        $backupId = (int)Database::lastInsertId();

        AuditLog::record('database.backup_created', 'database_backups', $backupId, [
            'filename' => $filename,
            'size_bytes' => $fileSizeBytes,
            'table_count' => $tableCount,
            'total_rows' => $totalRows
        ]);

        return [
            'id' => $backupId,
            'filename' => $filename,
            'filepath' => $filepath,
            'file_size_bytes' => $fileSizeBytes,
            'file_size_formatted' => self::formatBytes($fileSizeBytes),
            'table_count' => $tableCount,
            'total_rows' => $totalRows,
            'checksum' => $checksum
        ];
    }

    /**
     * Restore a backup snapshot
     */
    public static function restoreBackup(int $backupId): bool {
        $backup = Database::fetch("SELECT * FROM database_backups WHERE id = ?", [$backupId]);
        if (!$backup || !file_exists($backup['filepath'])) {
            throw new \RuntimeException("Backup file not found on disk.");
        }

        $sql = file_get_contents($backup['filepath']);
        if (empty($sql)) {
            throw new \RuntimeException("Backup file is empty.");
        }

        $pdo = Database::getConnection();
        $pdo->exec($sql);

        AuditLog::record('database.backup_restored', 'database_backups', $backupId, [
            'filename' => $backup['filename']
        ]);

        return true;
    }

    /**
     * Get all backups inventory
     */
    public static function getBackups(int $limit = 50): array {
        $limit = max(1, (int)$limit);
        $rows = Database::query("SELECT b.*, u.username as creator_name FROM database_backups b LEFT JOIN users u ON b.created_by = u.id ORDER BY b.created_at DESC LIMIT {$limit}");
        foreach ($rows as &$r) {
            $r['file_size_formatted'] = self::formatBytes((int)$r['file_size_bytes']);
            $r['file_exists'] = file_exists($r['filepath']);
        }
        return $rows;
    }

    /**
     * Delete a backup snapshot
     */
    public static function deleteBackup(int $backupId): bool {
        $backup = Database::fetch("SELECT * FROM database_backups WHERE id = ?", [$backupId]);
        if ($backup) {
            if (file_exists($backup['filepath'])) {
                @unlink($backup['filepath']);
            }
            Database::execute("DELETE FROM database_backups WHERE id = ?", [$backupId]);
            AuditLog::record('database.backup_deleted', 'database_backups', $backupId, ['filename' => $backup['filename']]);
            return true;
        }
        return false;
    }

    /**
     * Helper to format bytes
     */
    public static function formatBytes(int $bytes): string {
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
