#!/usr/bin/env bash
# ==============================================================================
# TechScout Production Server (AWS EC2) Deployment & Migration Sync
# Pulls latest code from GitHub origin/main, runs database migrations,
# clears OPcache/storage cache, and reloads PHP-FPM.
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

# 3. Clear storage/cache directory (invalidate all app caches)
echo "[$(date -u +"%Y-%m-%dT%H:%M:%SZ")] Clearing storage/cache..."
if [ -d "$APP_DIR/storage/cache" ]; then
    find "$APP_DIR/storage/cache" -type f \( -name "*.php" -o -name "*.cache" -o -name "*.json" \) -delete 2>/dev/null || true
    echo "  storage/cache cleared."
fi

# 4. Clear PHP OPcache via CLI (writes a temp script, executes it, then removes it)
echo "[$(date -u +"%Y-%m-%dT%H:%M:%SZ")] Resetting PHP OPcache..."
OPCACHE_SCRIPT="$APP_DIR/storage/opcache_reset_$$.php"
printf '<?php if(function_exists("opcache_reset")){opcache_reset();echo "OPcache reset OK\n";}else{echo "OPcache not available\n";}' > "$OPCACHE_SCRIPT"
php "$OPCACHE_SCRIPT" 2>/dev/null || true
rm -f "$OPCACHE_SCRIPT"

# 5. Ensure permissions for web server
echo "[$(date -u +"%Y-%m-%dT%H:%M:%SZ")] Setting permissions for web server..."
if id "www-data" &>/dev/null; then
    chown -R www-data:www-data "$APP_DIR" 2>/dev/null || true
fi
chmod -R 755 "$APP_DIR" 2>/dev/null || true
chmod -R 775 "$APP_DIR/storage" 2>/dev/null || true
[ -d "$APP_DIR/public/uploads" ] && chmod -R 775 "$APP_DIR/public/uploads" 2>/dev/null || true

# 6. Reload PHP-FPM (this also flushes the web-process OPcache)
systemctl daemon-reload 2>/dev/null || true
if systemctl is-active --quiet php8.3-fpm 2>/dev/null; then
    echo "[$(date -u +"%Y-%m-%dT%H:%M:%SZ")] Reloading php8.3-fpm..."
    systemctl reload php8.3-fpm || true
elif systemctl is-active --quiet php8.2-fpm 2>/dev/null; then
    echo "[$(date -u +"%Y-%m-%dT%H:%M:%SZ")] Reloading php8.2-fpm..."
    systemctl reload php8.2-fpm || true
fi

# 7. Reload Apache for good measure
if systemctl is-active --quiet apache2 2>/dev/null; then
    echo "[$(date -u +"%Y-%m-%dT%H:%M:%SZ")] Reloading apache2..."
    systemctl reload apache2 || true
fi

echo "================================================================="
echo "[$(date -u +"%Y-%m-%dT%H:%M:%SZ")] EC2 Deployment & Migration Complete!"
echo "================================================================="
