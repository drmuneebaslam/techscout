<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/Config.php';
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

try {
    $pdo = Database::getConnection();

    // 1. Ensure migrations tracking table exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS `migrations` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `migration_name` varchar(255) NOT NULL,
      `batch` int(11) NOT NULL DEFAULT 1,
      `applied_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `idx_migration_name` (`migration_name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $applied = $pdo->query("SELECT migration_name FROM migrations")->fetchAll(PDO::FETCH_COLUMN) ?: [];
    $migrationFiles = glob(__DIR__ . '/../database/migrations/*.sql') ?: [];
    sort($migrationFiles);

    $maxBatch = (int)($pdo->query("SELECT MAX(batch) FROM migrations")->fetchColumn() ?: 0);
    $newBatch = $maxBatch + 1;
    $count = 0;

    foreach ($migrationFiles as $mf) {
        $baseName = basename($mf, '.sql');
        if (!in_array($baseName, $applied, true)) {
            echo "Applying migration: {$baseName} ... ";
            $sql = file_get_contents($mf);
            if ($sql === false || trim($sql) === '') {
                echo "SKIPPED (empty)\n";
                continue;
            }

            // Split into individual executable statements
            // Handle DELIMITER blocks or strip them
            $statements = [];
            if (strpos($sql, 'DELIMITER') !== false) {
                // If contains procedure with delimiter
                // Execute using raw mysql connection or clean statements
                $cleanSql = preg_replace('/DELIMITER\s+\$\$/i', '', $sql);
                $cleanSql = preg_replace('/DELIMITER\s+;/i', '', $cleanSql);
                $parts = explode('$$', $cleanSql);
                foreach ($parts as $p) {
                    $subParts = explode(';', $p);
                    foreach ($subParts as $sp) {
                        $sp = trim($sp);
                        if (!empty($sp)) {
                            $statements[] = $sp;
                        }
                    }
                }
            } else {
                $rawParts = explode(';', $sql);
                foreach ($rawParts as $p) {
                    $p = trim($p);
                    if (!empty($p)) {
                        $statements[] = $p;
                    }
                }
            }

            $success = true;
            foreach ($statements as $stmt) {
                if (empty($stmt)) continue;
                try {
                    $pdo->exec($stmt);
                } catch (\Throwable $e) {
                    // Ignore benign errors (e.g. duplicate column if rerun)
                    $msg = $e->getMessage();
                    if (str_contains($msg, 'Duplicate column') || str_contains($msg, 'already exists') || str_contains($msg, 'Duplicate key')) {
                        continue;
                    }
                    echo "WARNING: " . $msg . "\n";
                }
            }

            $ins = $pdo->prepare("INSERT INTO migrations (migration_name, batch) VALUES (?, ?)");
            $ins->execute([$baseName, $newBatch]);
            echo "DONE\n";
            $count++;
        }
    }

    echo "Migrations check complete. Applied: {$count}\n";
} catch (\Throwable $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
