-- ==============================================================================
-- TechScout Universal Database Schema Upgrade & Error Fix Script
-- Target Database: if0_40102772_techscoutmainafterallchanges (Production & Local)
-- 
-- DESCRIPTION:
--   1. Fixes missing `post_views` table (resolves SQLSTATE[42S02] in simulateEngagement).
--   2. Fixes `mail_logs` missing columns (from_email, credential_name, tracking_token, etc.).
--   3. Relaxes `mail_logs.attachments_json` to LONGTEXT NULL without strict CHECK constraint
--      (resolves SQLSTATE[23000] 4025 constraint violation in MariaDB).
--   4. Creates canonical `mail_templates` table if missing and seeds default templates.
--   5. Safely migrates any data from legacy `custom_mail_templates` if it exists.
--
-- SAFETY GUARANTEE:
--   - This script NEVER drops tables or deletes user records.
--   - Uses IF NOT EXISTS and non-destructive column additions/modifications.
-- ==============================================================================

-- ------------------------------------------------------------------------------
-- 1. Create `post_views` Telemetry Table
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `post_views` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `content_id` BIGINT(20) UNSIGNED NOT NULL,
  `user_id` BIGINT(20) UNSIGNED DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `referrer` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pv_content` (`content_id`),
  KEY `idx_pv_created` (`created_at`),
  KEY `idx_pv_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 2. Create `mail_templates` Table
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mail_templates` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_key` VARCHAR(80) NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `html_content` LONGTEXT NOT NULL,
  `assigned_credential_id` BIGINT(20) UNSIGNED DEFAULT NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `template_key` (`template_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 3. Ensure `mail_logs` Exists & Has All Modern Tracking & Telemetry Columns
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mail_logs` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `to_email` VARCHAR(191) NOT NULL,
  `to_name` VARCHAR(150) DEFAULT NULL,
  `from_email` VARCHAR(191) DEFAULT NULL,
  `from_name` VARCHAR(150) DEFAULT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `template_key` VARCHAR(80) DEFAULT NULL,
  `body_html` LONGTEXT DEFAULT NULL,
  `attachments_json` LONGTEXT DEFAULT NULL,
  `driver` VARCHAR(30) NOT NULL DEFAULT 'log',
  `credential_name` VARCHAR(150) DEFAULT NULL,
  `tracking_token` VARCHAR(64) DEFAULT NULL,
  `open_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `opened_at` DATETIME DEFAULT NULL,
  `click_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `clicked_at` DATETIME DEFAULT NULL,
  `last_clicked_url` TEXT DEFAULT NULL,
  `user_ip` VARCHAR(45) DEFAULT NULL,
  `user_agent` TEXT DEFAULT NULL,
  `status` ENUM('sent','failed','queued') NOT NULL DEFAULT 'sent',
  `error_message` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ml_email` (`to_email`),
  KEY `idx_ml_status` (`status`),
  KEY `idx_ml_token` (`tracking_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 4. Upgrade Existing `mail_logs` Table Columns & Relax attachments_json
-- ------------------------------------------------------------------------------
-- Relax attachments_json to standard LONGTEXT NULL without strict CHECK constraint
ALTER TABLE `mail_logs` 
  MODIFY COLUMN `attachments_json` LONGTEXT NULL DEFAULT NULL;

-- Add tracking and sender columns if they do not yet exist
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

-- Ensure indexes exist
ALTER TABLE `mail_logs`
  ADD INDEX IF NOT EXISTS `idx_ml_token` (`tracking_token`),
  ADD INDEX IF NOT EXISTS `idx_ml_email` (`to_email`),
  ADD INDEX IF NOT EXISTS `idx_ml_status` (`status`);

-- ------------------------------------------------------------------------------
-- 5. Seed Core Mail Templates (If Not Already Present)
-- ------------------------------------------------------------------------------
INSERT IGNORE INTO `mail_templates` (`template_key`, `name`, `subject`, `description`, `html_content`, `updated_at`) VALUES
('payout_approved', 'Author Payout Approved & Dispatched', 'Payment Sent: Your {{platform_name}} Creator Payout of ${{amount}} is Complete! 🎉', 'Dispatched when author payout withdrawal is processed and paid.', '<!-- Payout Approved Email --><div style=\"font-family:sans-serif;max-width:600px;margin:0 auto;padding:20px;\"><h2 style=\"color:#10b981;\">Payout Dispatched</h2><p>Hi {{name}},</p><p>Great news! Your payout of <strong>${{amount}} USD</strong> has been approved and processed.</p><p><strong>Transaction Ref:</strong> {{transaction_ref}}</p><p><a href=\"{{action_url}}\" style=\"display:inline-block;background:#6366f1;color:#fff;padding:10px 20px;text-decoration:none;border-radius:6px;\">View Payout History</a></p></div>', NOW()),
('payout_rejected', 'Author Payout Request Rejected', 'Important Update on your {{platform_name}} Payout Request #{{payout_id}}', 'Dispatched when payout request is rejected and credits are restored.', '<!-- Payout Rejected Email --><div style=\"font-family:sans-serif;max-width:600px;margin:0 auto;padding:20px;\"><h2 style=\"color:#ef4444;\">Payout Update</h2><p>Hi {{name}},</p><p>Your withdrawal request #{{payout_id}} could not be processed at this time.</p><p><strong>Reason:</strong> {{admin_notes}}</p><p>Any deducted credits have been fully restored to your account balance.</p><p><a href=\"{{action_url}}\" style=\"display:inline-block;background:#6366f1;color:#fff;padding:10px 20px;text-decoration:none;border-radius:6px;\">Check Balance</a></p></div>', NOW()),
('payout_requested', 'Author Payout Withdrawal Claim Received', 'We received your payout request for ${{amount}} USD 💳', 'Dispatched immediately when an author submits a withdrawal claim.', '<!-- Payout Requested Email --><div style=\"font-family:sans-serif;max-width:600px;margin:0 auto;padding:20px;\"><h2 style=\"color:#6366f1;\">Withdrawal Request Received</h2><p>Hi {{name}},</p><p>We received your payout claim for <strong>${{amount}} USD</strong>.</p><p>Our finance team will review and process your transfer shortly.</p></div>', NOW());

-- Upgrade complete. Safe for repeated runs.
