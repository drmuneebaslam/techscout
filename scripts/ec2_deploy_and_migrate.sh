#!/usr/bin/env bash
# ==============================================================================
# TechScout Production Server (AWS EC2) Deployment & Migration Sync
# Pulls latest code from GitHub origin/main and executes pending database migrations
#
# Usage:
#   bash /var/www/techscout/scripts/ec2_deploy_and_migrate.sh
# ==============================================================================

set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/techscout}"
if [ ! -d "$APP_DIR" ]; then
    APP_DIR="$(pwd)"
fi

echo "================================================================="
echo "[$(date -u +"%Y-%m-%dT%H:%M:%SZ")] TechScout EC2 Production Sync"
echo "Target directory: $APP_DIR"
echo "================================================================="

cd "$APP_DIR"

# 1. Fetch & Merge from origin/main
echo "[$(date -u +"%Y-%m-%dT%H:%M:%SZ")] Pulling latest commits from GitHub origin main..."
git fetch origin main
git merge origin/main --ff-only || git pull origin main

# 2. Run Database Migrations via PHP CLI
echo "[$(date -u +"%Y-%m-%dT%H:%M:%SZ")] Executing pending database migrations..."
php -r '
require_once "app/Core/Config.php";
require_once "app/Core/Database.php";

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
                    if (!str_contains($errMsg, "already exists") && !str_contains($errMsg, "Duplicate")) {
                        echo "ERROR: " . $errMsg . "\n";
                        exit(1);
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
'

# 3. Ensure permissions for web server (www-data / nginx / apache)
echo "[$(date -u +"%Y-%m-%dT%H:%M:%SZ")] Setting permissions for web server..."
if id "www-data" &>/dev/null; then
    chown -R www-data:www-data "$APP_DIR/storage" "$APP_DIR/public/uploads" 2>/dev/null || true
fi
chmod -R 775 "$APP_DIR/storage" 2>/dev/null || true

# 4. Optional: Reload PHP-FPM if active
if systemctl is-active --quiet php8.2-fpm 2>/dev/null; then
    echo "[$(date -u +"%Y-%m-%dT%H:%M:%SZ")] Reloading php8.2-fpm..."
    systemctl reload php8.2-fpm || true
elif systemctl is-active --quiet php8.3-fpm 2>/dev/null; then
    echo "[$(date -u +"%Y-%m-%dT%H:%M:%SZ")] Reloading php8.3-fpm..."
    systemctl reload php8.3-fpm || true
fi

echo "================================================================="
echo "[$(date -u +"%Y-%m-%dT%H:%M:%SZ")] EC2 Deployment & Migration Complete!"
echo "================================================================="
