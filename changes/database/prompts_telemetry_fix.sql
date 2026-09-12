-- =========================================================================
-- TechScout Telemetry & Prompts Table Upgrade Script
-- Fixes: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'created_at' in 'WHERE'
-- Safe for execution in phpMyAdmin / MySQL CLI / MariaDB
-- =========================================================================

-- Step 1: Add created_at and updated_at to prompts table if missing
-- (Uses MariaDB 10.2+ / MySQL 8.0+ IF NOT EXISTS syntax for idempotency)
ALTER TABLE `prompts` 
  ADD COLUMN IF NOT EXISTS `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  ADD COLUMN IF NOT EXISTS `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp();

-- Step 2: Backfill created_at from parent contents table for existing prompt records
UPDATE `prompts` p
INNER JOIN `contents` c ON p.content_id = c.id
SET p.created_at = c.created_at
WHERE p.created_at IS NULL OR p.created_at = '0000-00-00 00:00:00';

-- Step 3: Add index on created_at for fast time-range analytics filtering
CREATE INDEX IF NOT EXISTS `idx_prompts_created_at` ON `prompts` (`created_at`);
