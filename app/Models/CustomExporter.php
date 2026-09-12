<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Config;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use FilesystemIterator;

class CustomExporter {

    /**
     * Scans root workspace directories and returns their size, file count, and descriptions.
     */
    public static function getDirectoryTree(): array {
        $rootDir = dirname(__DIR__, 2);
        $candidates = [
            'app' => ['name' => 'Application Core (app/)', 'desc' => 'Controllers, Models, Core classes & business logic', 'recommended' => true],
            'views' => ['name' => 'UI Templates (views/)', 'desc' => 'Public, Admin, Author layouts & Blade/PHP views', 'recommended' => true],
            'assets' => ['name' => 'Static Assets (assets/)', 'desc' => 'CSS stylesheets, JS scripts, icons, brand artwork', 'recommended' => true],
            'database' => ['name' => 'Database Files (database/)', 'desc' => 'Schema blueprints, seed SQL, and historical upgrade scripts', 'recommended' => true],
            'public' => ['name' => 'Public Webroot (public/)', 'desc' => 'Entry points, media uploads & static files', 'recommended' => true],
            'docs' => ['name' => 'Documentation (docs/)', 'desc' => 'Architectural specs, API docs, and mistakes learned registry', 'recommended' => false],
            'config' => ['name' => 'Custom Configs (config/)', 'desc' => 'Custom environment overrides and templates', 'recommended' => false],
            'storage' => ['name' => 'Storage & Locks (storage/)', 'desc' => 'System lockfiles, caches, and runtime metadata', 'recommended' => false],
        ];

        $results = [];
        foreach ($candidates as $dirName => $meta) {
            $path = $rootDir . '/' . $dirName;
            if (is_dir($path)) {
                $stats = self::calculateFolderStats($path);
                $results[$dirName] = array_merge($meta, [
                    'path' => $dirName,
                    'exists' => true,
                    'file_count' => $stats['files'],
                    'size_bytes' => $stats['size'],
                    'size_formatted' => self::formatBytes($stats['size'])
                ]);
            }
        }

        // Detect Root Core Files
        $rootFiles = ['index.php', '.htaccess', 'favicon.ico', 'robots.txt', 'composer.json'];
        $foundRootFiles = [];
        $rootTotalSize = 0;
        foreach ($rootFiles as $rf) {
            $rfPath = $rootDir . '/' . $rf;
            if (file_exists($rfPath)) {
                $sz = filesize($rfPath);
                $rootTotalSize += $sz;
                $foundRootFiles[] = [
                    'name' => $rf,
                    'size' => $sz,
                    'size_formatted' => self::formatBytes($sz)
                ];
            }
        }

        return [
            'directories' => $results,
            'root_files' => $foundRootFiles,
            'root_files_total_size' => self::formatBytes($rootTotalSize)
        ];
    }

    /**
     * Introspects MySQL database tables, schemas, and row metrics.
     */
    public static function getTablesMetadata(): array {
        $dbName = Config::get('db_name', 'techscout_db');
        $rawTables = Database::query(
            "SELECT TABLE_NAME, TABLE_ROWS, DATA_LENGTH, INDEX_LENGTH, (DATA_LENGTH + INDEX_LENGTH) as TOTAL_SIZE, ENGINE, TABLE_COLLATION 
             FROM information_schema.TABLES 
             WHERE TABLE_SCHEMA = ? 
             ORDER BY TABLE_NAME ASC",
            [$dbName]
        );

        $tables = [];
        foreach ($rawTables as $t) {
            $name = $t['TABLE_NAME'];
            // Get actual accurate live row count
            try {
                $actualCount = (int)Database::fetch("SELECT COUNT(*) as c FROM `{$name}`")['c'];
            } catch (\Throwable $e) {
                $actualCount = (int)($t['TABLE_ROWS'] ?? 0);
            }

            // Get columns
            $cols = [];
            try {
                $rawCols = Database::query("SHOW COLUMNS FROM `{$name}`");
                foreach ($rawCols as $rc) {
                    $cols[] = [
                        'name' => $rc['Field'],
                        'type' => $rc['Type'],
                        'is_pk' => ($rc['Key'] === 'PRI')
                    ];
                }
            } catch (\Throwable $e) {}

            $tables[$name] = [
                'name' => $name,
                'rows' => $actualCount,
                'size_formatted' => self::formatBytes((int)$t['TOTAL_SIZE']),
                'engine' => $t['ENGINE'] ?? 'InnoDB',
                'columns' => $cols,
                'has_data' => ($actualCount > 0)
            ];
        }

        return $tables;
    }

    /**
     * Fetches paginated sample rows for interactive seed selection.
     */
    public static function getTableRowsForSelection(string $tableName, int $limit = 50, int $offset = 0, string $search = ''): array {
        $tableName = preg_replace('/[^a-zA-Z0-9_]/', '', $tableName);
        if (empty($tableName)) return ['total' => 0, 'rows' => [], 'pk' => 'id'];

        // Find primary key
        $cols = Database::query("SHOW COLUMNS FROM `{$tableName}`");
        $pk = 'id';
        $previewCols = [];
        foreach ($cols as $c) {
            if ($c['Key'] === 'PRI') {
                $pk = $c['Field'];
            }
            if (in_array(strtolower($c['Field']), ['title', 'name', 'username', 'email', 'slug', 'key_name', 'status', 'created_at'], true)) {
                $previewCols[] = $c['Field'];
            }
        }
        if (empty($previewCols)) {
            $previewCols = array_slice(array_column($cols, 'Field'), 0, 4);
        }

        $whereClause = "";
        $params = [];
        if (!empty($search)) {
            $likes = [];
            foreach ($previewCols as $pc) {
                $likes[] = "`{$pc}` LIKE ?";
                $params[] = '%' . $search . '%';
            }
            if (!empty($likes)) {
                $whereClause = " WHERE " . implode(' OR ', $likes);
            }
        }

        $total = (int)Database::fetch("SELECT COUNT(*) as c FROM `{$tableName}` {$whereClause}", $params)['c'];
        
        $sql = "SELECT * FROM `{$tableName}` {$whereClause} ORDER BY `{$pk}` ASC LIMIT {$limit} OFFSET {$offset}";
        $rows = Database::query($sql, $params);

        return [
            'table' => $tableName,
            'pk' => $pk,
            'preview_columns' => $previewCols,
            'total' => $total,
            'rows' => $rows
        ];
    }

    /**
     * Builds the custom export ZIP containing selected folders + bespoke schema & granular seed SQL.
     */
    public static function generateCustomZip(
        array $selectedFolders,
        bool $includeRootFiles,
        array $selectedTablesConfig,
        ?int $userId = null
    ): string {
        $rootDir = dirname(__DIR__, 2);
        $tempDir = $rootDir . '/assets/backups/custom_exports';
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }

        $zipFilename = 'techscout_custom_export_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.zip';
        $zipPath = $tempDir . '/' . $zipFilename;

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Failed to initialize ZipArchive at {$zipPath}");
        }

        // 1. Pack Selected Folders
        $allowedFolders = ['app', 'views', 'assets', 'database', 'public', 'docs', 'config', 'storage'];
        $packedFolders = [];
        $totalFilesPacked = 0;

        foreach ($selectedFolders as $folder) {
            $cleanFolder = basename(trim((string)$folder));
            if (!in_array($cleanFolder, $allowedFolders, true)) continue;

            $srcPath = $rootDir . '/' . $cleanFolder;
            if (is_dir($srcPath)) {
                $packedFolders[] = $cleanFolder;
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($srcPath, FilesystemIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::SELF_FIRST
                );

                foreach ($files as $file) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($rootDir) + 1);
                    $relativePath = str_replace('\\', '/', $relativePath);

                    // Skip vendor, node_modules, and nested backup dumps inside zip
                    if (str_contains($relativePath, 'node_modules') || 
                        str_contains($relativePath, 'assets/backups') ||
                        str_contains($relativePath, '.git')) {
                        continue;
                    }

                    if ($file->isDir()) {
                        $zip->addEmptyDir($relativePath);
                    } elseif ($file->isFile()) {
                        $zip->addFile($filePath, $relativePath);
                        $totalFilesPacked++;
                    }
                }
            }
        }

        // 2. Pack Root Files
        if ($includeRootFiles) {
            $rootFiles = ['index.php', '.htaccess', 'favicon.ico', 'robots.txt', 'composer.json'];
            foreach ($rootFiles as $rf) {
                $rfPath = $rootDir . '/' . $rf;
                if (file_exists($rfPath)) {
                    $zip->addFile($rfPath, $rf);
                    $totalFilesPacked++;
                }
            }
        }

        // 3. Generate Custom SQL Schema & Granular Seeds
        $sqlContent = self::buildCustomSqlDump($selectedTablesConfig);
        $zip->addFromString('database/custom_schema_and_seeds.sql', $sqlContent);

        // 4. Generate Export Manifest
        $manifest = [
            'platform' => 'TechScout Enterprise AI Hub',
            'exported_at' => date('Y-m-d H:i:s'),
            'exported_by_user_id' => $userId,
            'directories_included' => $packedFolders,
            'root_files_included' => $includeRootFiles,
            'total_files_packed' => $totalFilesPacked,
            'tables_custom_export_count' => count($selectedTablesConfig),
            'manifest_version' => '2.0.0'
        ];
        $zip->addFromString('EXPORT_MANIFEST.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $zip->close();
        return $zipPath;
    }

    /**
     * Constructs SQL containing live CREATE TABLE schemas and specifically chosen seed records.
     */
    public static function buildCustomSqlDump(array $tablesConfig): string {
        $pdo = Database::getConnection();
        $dbName = Config::get('db_name', 'techscout_db');

        $out = "-- ========================================================\n";
        $out .= "-- TechScout Customized Schema & Granular Seed Package\n";
        $out .= "-- Generated: " . date('Y-m-d H:i:s') . " (Pakistani Standard Time)\n";
        $out .= "-- Source Database: {$dbName}\n";
        $out .= "-- ========================================================\n\n";
        $out .= "SET FOREIGN_KEY_CHECKS = 0;\n";
        $out .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
        $out .= "SET NAMES utf8mb4;\n\n";

        foreach ($tablesConfig as $tableName => $cfg) {
            $tableName = preg_replace('/[^a-zA-Z0-9_]/', '', $tableName);
            if (empty($tableName)) continue;

            $includeSchema = !empty($cfg['include_schema']);
            $seedMode = $cfg['seed_mode'] ?? 'none'; // 'all', 'selected', 'none'
            $selectedIds = (array)($cfg['selected_ids'] ?? []);

            // 1. Schema Generation
            if ($includeSchema) {
                $createRow = Database::fetch("SHOW CREATE TABLE `{$tableName}`");
                $createSql = $createRow['Create Table'] ?? null;
                if ($createSql) {
                    $out .= "--\n-- Table structure for: `{$tableName}`\n--\n";
                    $out .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                    $out .= $createSql . ";\n\n";
                }
            }

            // 2. Seeds Generation
            if ($seedMode === 'all') {
                $rows = Database::query("SELECT * FROM `{$tableName}`");
                $out .= self::formatInsertRows($tableName, $rows, $pdo);
            } elseif ($seedMode === 'selected' && !empty($selectedIds)) {
                // Find primary key
                $pkRow = Database::fetch("SHOW KEYS FROM `{$tableName}` WHERE Key_name = 'PRIMARY'");
                $pk = $pkRow['Column_name'] ?? 'id';

                $placeholders = implode(',', array_fill(0, count($selectedIds), '?'));
                $rows = Database::query("SELECT * FROM `{$tableName}` WHERE `{$pk}` IN ({$placeholders})", $selectedIds);
                $out .= self::formatInsertRows($tableName, $rows, $pdo);
            }
        }

        $out .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        return $out;
    }

    private static function formatInsertRows(string $tableName, array $rows, \PDO $pdo): string {
        $count = count($rows);
        if ($count === 0) return "";

        $out = "--\n-- Granular Seed Data for table `{$tableName}` ({$count} records)\n--\n";
        $columns = array_keys($rows[0]);
        $colList = '`' . implode('`, `', $columns) . '`';

        $chunks = array_chunk($rows, 40);
        foreach ($chunks as $chunk) {
            $out .= "INSERT INTO `{$tableName}` ({$colList}) VALUES \n";
            $valLines = [];
            foreach ($chunk as $r) {
                $vals = [];
                foreach ($r as $v) {
                    if ($v === null) {
                        $vals[] = 'NULL';
                    } elseif (is_numeric($v)) {
                        $vals[] = $v;
                    } else {
                        $vals[] = $pdo->quote((string)$v);
                    }
                }
                $valLines[] = "(" . implode(', ', $vals) . ")";
            }
            $out .= implode(",\n", $valLines) . ";\n";
        }
        $out .= "\n";
        return $out;
    }

    private static function calculateFolderStats(string $dir): array {
        $totalSize = 0;
        $fileCount = 0;
        try {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
            );
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $totalSize += $file->getSize();
                    $fileCount++;
                }
            }
        } catch (\Throwable $e) {}
        return ['size' => $totalSize, 'files' => $fileCount];
    }

    private static function formatBytes(int $bytes): string {
        if ($bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes, 1024));
        return round($bytes / pow(1024, $i), 2) . ' ' . ($units[$i] ?? 'MB');
    }
}
