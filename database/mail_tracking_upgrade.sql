-- ==============================================================================
-- TechScout Mail Tracking & Schema Upgrade Script
-- Target Database: if0_40102772_techscoutmainafterallchanges
-- Execution: Run in phpMyAdmin or MySQL CLI on production server
-- ==============================================================================

-- 1. Ensure columns exist on mail_logs
ALTER TABLE `mail_logs` 
  ADD COLUMN IF NOT EXISTS `from_email` VARCHAR(191) NULL AFTER `to_name`,
  ADD COLUMN IF NOT EXISTS `from_name` VARCHAR(150) NULL AFTER `from_email`,
  ADD COLUMN IF NOT EXISTS `credential_name` VARCHAR(150) NULL AFTER `driver`,
  ADD COLUMN IF NOT EXISTS `tracking_token` VARCHAR(64) NULL AFTER `credential_name`,
  ADD COLUMN IF NOT EXISTS `open_count` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `tracking_token`,
  ADD COLUMN IF NOT EXISTS `opened_at` DATETIME NULL AFTER `open_count`,
  ADD COLUMN IF NOT EXISTS `click_count` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `opened_at`,
  ADD COLUMN IF NOT EXISTS `clicked_at` DATETIME NULL AFTER `click_count`,
  ADD COLUMN IF NOT EXISTS `last_clicked_url` TEXT NULL AFTER `clicked_at`,
  ADD COLUMN IF NOT EXISTS `user_ip` VARCHAR(45) NULL AFTER `last_clicked_url`,
  ADD COLUMN IF NOT EXISTS `user_agent` TEXT NULL AFTER `user_ip`;

-- 2. Add indexing for high-speed tracking resolution
ALTER TABLE `mail_logs`
  ADD INDEX IF NOT EXISTS `idx_ml_token` (`tracking_token`),
  ADD INDEX IF NOT EXISTS `idx_ml_email` (`to_email`),
  ADD INDEX IF NOT EXISTS `idx_ml_status` (`status`);

-- 3. Auto-heal notification stale URLs (remove stale /tech/ or /techscout.n/ paths)
UPDATE `notifications` 
  SET `action_url` = REPLACE(`action_url`, '/tech/', '/') 
  WHERE `action_url` LIKE '%/tech/%';

UPDATE `notifications` 
  SET `action_url` = REPLACE(`action_url`, 'funwithcoding.free.nf/tech', 'funwithcoding.free.nf') 
  WHERE `action_url` LIKE '%funwithcoding.free.nf/tech%';

UPDATE `notifications` 
  SET `action_url` = REPLACE(`action_url`, '/techscout.n/', '/') 
  WHERE `action_url` LIKE '%/techscout.n/%';

UPDATE `notifications` 
  SET `action_url` = REPLACE(`action_url`, 'http://localhost/techscout.n', 'https://funwithcoding.free.nf') 
  WHERE `action_url` LIKE '%http://localhost/techscout.n%';

-- Upgrade complete.
