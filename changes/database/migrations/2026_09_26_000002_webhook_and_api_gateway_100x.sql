-- TechScout Enterprise 100x Upgrade: Webhooks, Multi-Table Ingestion, Notifications & Telemetry
-- Migration: 2026_09_26_000002_webhook_and_api_gateway_100x.sql

-- 1. Eliminate Data Truncation Error 1265 on incoming_webhook_logs.status
ALTER TABLE `incoming_webhook_logs` 
  MODIFY COLUMN `status` VARCHAR(32) NOT NULL DEFAULT 'success';

-- 2. Prevent Error 1364: Field 'event_type' doesn't have a default value in notifications
ALTER TABLE `notifications` 
  MODIFY COLUMN `event_type` VARCHAR(60) NOT NULL DEFAULT 'system';

-- 3. Ensure multi-table support flag on incoming_webhooks
-- Add supports_multi_table column if it does not already exist
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'incoming_webhooks' 
  AND COLUMN_NAME = 'supports_multi_table';

SET @sql_multi = IF(@col_exists = 0, 
  'ALTER TABLE `incoming_webhooks` ADD COLUMN `supports_multi_table` TINYINT(1) NOT NULL DEFAULT 1 AFTER `target_table`', 
  'SELECT "Column supports_multi_table already exists"'
);
PREPARE stmt_multi FROM @sql_multi;
EXECUTE stmt_multi;
DEALLOCATE PREPARE stmt_multi;

-- 4. Ensure updated_at exists on categories
SET @col_cat_exists = 0;
SELECT COUNT(*) INTO @col_cat_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'categories' 
  AND COLUMN_NAME = 'updated_at';

SET @sql_cat = IF(@col_cat_exists = 0, 
  'ALTER TABLE `categories` ADD COLUMN `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP() AFTER `created_at`', 
  'SELECT "Column categories.updated_at already exists"'
);
PREPARE stmt_cat FROM @sql_cat;
EXECUTE stmt_cat;
DEALLOCATE PREPARE stmt_cat;

-- 5. Auto-mark historical resolved errors in system_error_logs
UPDATE `system_error_logs` 
SET `resolved` = 1 
WHERE `resolved` = 0 
  AND (`message` LIKE '%supporter_user_id%' 
   OR `message` LIKE '%Config%not found%' 
   OR `message` LIKE '%legacyTopic%' 
   OR `message` LIKE '%CategoryController::subscribe%' 
   OR `message` LIKE '%Unknown column \'slug\' in \'where clause\'%ai_tools%');
