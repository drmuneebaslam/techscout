#!/usr/bin/env bash
# ==============================================================================
# TechScout Production Database Daily Automated Backup
# 7-Day Rolling Retention for AWS EC2
#
# Crontab Entry (runs daily at 02:00 AM UTC):
# 0 2 * * * /bin/bash /var/www/techscout/scripts/ec2_db_backup.sh >> /var/log/techscout_backup.log 2>&1
# ==============================================================================

set -euo pipefail

BACKUP_DIR="/var/backups/techscout_db"
DATE_TAG=$(date +"%Y%m%d_%H%M%S")
DB_NAME="${DB_NAME:-techscout_db}"
DB_USER="${DB_USER:-root}"
DB_PASS="${DB_PASS:-}"
RETENTION_DAYS=7

mkdir -p "$BACKUP_DIR"

TARGET_FILE="$BACKUP_DIR/techscout_db_${DATE_TAG}.sql.gz"
LATEST_LINK="$BACKUP_DIR/techscout_db_daily.sql.gz"

echo "[$(date -u +"%Y-%m-%dT%H:%M:%SZ")] Starting MySQL export for database: $DB_NAME..."

if [ -n "$DB_PASS" ]; then
    mysqldump -u"$DB_USER" -p"$DB_PASS" --single-transaction --quick --routines --triggers "$DB_NAME" | gzip -9 > "$TARGET_FILE"
else
    mysqldump -u"$DB_USER" --single-transaction --quick --routines --triggers "$DB_NAME" | gzip -9 > "$TARGET_FILE"
fi

# Update symlink to latest backup
ln -sf "$TARGET_FILE" "$LATEST_LINK"

BACKUP_SIZE=$(ls -lh "$TARGET_FILE" | awk '{print $5}')
echo "[$(date -u +"%Y-%m-%dT%H:%M:%SZ")] Backup completed: $TARGET_FILE ($BACKUP_SIZE)"

# Purge backups older than 7 days
echo "[$(date -u +"%Y-%m-%dT%H:%M:%SZ")] Purging backups older than $RETENTION_DAYS days..."
find "$BACKUP_DIR" -type f -name "techscout_db_*.sql.gz" -mtime +"$RETENTION_DAYS" -exec rm -f {} +

echo "[$(date -u +"%Y-%m-%dT%H:%M:%SZ")] Database backup lifecycle completed successfully."
