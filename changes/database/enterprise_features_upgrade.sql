-- =======================================================================
-- TechScout Enterprise Architecture Upgrade: Non-Destructive Schema Expansion
-- =======================================================================

-- 1. Multi-Image Prompt Galleries
CREATE TABLE IF NOT EXISTS `prompt_images` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `prompt_id` INT NOT NULL,
  `image_url` VARCHAR(255) NOT NULL,
  `caption` VARCHAR(255) NULL,
  `sort_order` INT DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_prompt_id` (`prompt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Platform Crypto Tip Jar & Author Ledger
CREATE TABLE IF NOT EXISTS `author_tips` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `author_id` INT NOT NULL,
  `sender_name` VARCHAR(100) DEFAULT 'Anonymous Supporter',
  `sender_wallet` VARCHAR(120) NULL,
  `article_url` VARCHAR(255) NULL,
  `gross_amount` DECIMAL(10,2) NOT NULL,
  `platform_fee` DECIMAL(10,2) NOT NULL,
  `net_author_amount` DECIMAL(10,2) NOT NULL,
  `currency` VARCHAR(10) DEFAULT 'USDT',
  `txid` VARCHAR(120) NULL,
  `status` ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
  `admin_notes` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_author_status` (`author_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `author_tip_balances` (
  `user_id` INT PRIMARY KEY,
  `total_earned` DECIMAL(10,2) DEFAULT 0.00,
  `current_balance` DECIMAL(10,2) DEFAULT 0.00,
  `total_withdrawn` DECIMAL(10,2) DEFAULT 0.00,
  `preferred_payout_wallet` VARCHAR(120) NULL,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `author_payout_requests` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `payout_currency` VARCHAR(10) DEFAULT 'USDT',
  `payout_wallet` VARCHAR(120) NOT NULL,
  `status` ENUM('requested', 'processing', 'completed', 'rejected') DEFAULT 'requested',
  `txid` VARCHAR(120) NULL,
  `admin_notes` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `processed_at` DATETIME NULL,
  INDEX `idx_user_status` (`user_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Community Polls
CREATE TABLE IF NOT EXISTS `article_polls` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `content_id` INT NOT NULL,
  `question` VARCHAR(255) NOT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_content_id` (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `article_poll_options` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `poll_id` INT NOT NULL,
  `option_text` VARCHAR(255) NOT NULL,
  `votes_count` INT DEFAULT 0,
  INDEX `idx_poll_id` (`poll_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `article_poll_votes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `poll_id` INT NOT NULL,
  `option_id` INT NOT NULL,
  `user_id` INT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `voted_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_poll_ip` (`poll_id`, `ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Schema Migrations Table
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `migration_name` VARCHAR(255) NOT NULL UNIQUE,
  `batch` INT NOT NULL,
  `applied_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Safe Column Enhancements for Comments
SET @dbname = DATABASE();
SET @tablename = "comments";
SET @columnname = "is_pinned";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " TINYINT(1) DEFAULT 0 AFTER `status`")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @columnname = "reactions_json";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " TEXT NULL AFTER `is_pinned`")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 6. Insert Default Platform Ad & Wallet Settings if not present
INSERT IGNORE INTO `system_settings` (`key_name`, `value_content`, `setting_group`) VALUES
('ad_direct_link_url', 'https://omg10.com/4/11737440', 'advertising'),
('ad_popunder_url', 'https://omg10.com/4/11737440', 'advertising'),
('ad_tier_7_price', '29.00', 'advertising'),
('ad_tier_30_price', '89.00', 'advertising'),
('ad_tier_90_price', '199.00', 'advertising'),
('platform_wallet_btc', 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh', 'crypto'),
('platform_wallet_eth', '0x71C83709b1fbf4A8b7fa3b3E2F46E8295b92FEA1', 'crypto'),
('platform_wallet_usdt', '0x71C83709b1fbf4A8b7fa3b3E2F46E8295b92FEA1', 'crypto'),
('platform_wallet_sol', '7xKXtg2CW87d97TXJSDpbD5jBkheTqA83TZRuJosgAsU', 'crypto'),
('author_tip_platform_fee_pct', '5', 'crypto'),
('author_min_payout_threshold', '50.00', 'crypto');

INSERT IGNORE INTO `site_settings` (`key_name`, `key_value`) VALUES
('ad_direct_link_url', 'https://omg10.com/4/11737440'),
('ad_popunder_url', 'https://omg10.com/4/11737440'),
('ad_tier_7_price', '29.00'),
('ad_tier_30_price', '89.00'),
('ad_tier_90_price', '199.00'),
('platform_wallet_btc', 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh'),
('platform_wallet_eth', '0x71C83709b1fbf4A8b7fa3b3E2F46E8295b92FEA1'),
('platform_wallet_usdt', '0x71C83709b1fbf4A8b7fa3b3E2F46E8295b92FEA1'),
('platform_wallet_sol', '7xKXtg2CW87d97TXJSDpbD5jBkheTqA83TZRuJosgAsU'),
('author_tip_platform_fee_pct', '5'),
('author_min_payout_threshold', '50.00');

-- Mark Migration
INSERT IGNORE INTO `migrations` (`migration_name`, `batch`) VALUES
('2026_09_12_enterprise_features_upgrade', 1);
