<?php
require_once "app/Core/Config.php";
require_once "app/Core/Database.php";

try {
    $pdo = \App\Core\Database::getConnection();

    // Ensure migrations tracking table exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS `migrations` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `migration_name` varchar(255) NOT NULL,
      `batch` int(11) NOT NULL DEFAULT 1,
      `applied_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `idx_migration_name` (`migration_name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $applied = $pdo->query("SELECT migration_name FROM migrations")->fetchAll(PDO::FETCH_COLUMN) ?: [];
    $migrationFiles = glob(__DIR__ . "/database/migrations/*.sql") ?: [];
    sort($migrationFiles);

    $maxBatch = (int)($pdo->query("SELECT MAX(batch) FROM migrations")->fetchColumn() ?: 0);
    $newBatch = $maxBatch + 1;
    $executed = 0;
    
    echo "<pre>Starting Migrations...\n";

    foreach ($migrationFiles as $mf) {
        $baseName = basename($mf, ".sql");
        if (!in_array($baseName, $applied, true)) {
            echo "Applying migration: $baseName ... ";
            $sql = file_get_contents($mf);
            $stmts = array_filter(array_map("trim", explode(";", $sql)));
            foreach ($stmts as $s) {
                $sClean = trim(preg_replace("/^\s*--[^\n]*/m", "", $s));
                if (!empty($sClean)) {
                    try {
                        $pdo->exec($sClean);
                    } catch (\Throwable $e) {
                        $errMsg = $e->getMessage();
                        if (!str_contains($errMsg, "already exists") && !str_contains($errMsg, "Duplicate column")) {
                            echo "[WARNING: " . $errMsg . "] ";
                        }
                    }
                }
            }
            $stmt = $pdo->prepare("INSERT INTO migrations (migration_name, batch, applied_at) VALUES (?, ?, NOW())");
            $stmt->execute([$baseName, $newBatch]);
            echo "OK\n";
            $executed++;
        }
    }

    if ($executed === 0) {
        echo "All migrations are already up to date (0 pending).\n";
    } else {
        echo "Batch #$newBatch successfully applied $executed migration(s).\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
echo "</pre>";
