-- Migration 2026_09_12_000001_enterprise_features_upgrade
-- Multi-image prompt gallery, Author crypto tips, Polls, and Migration tracking

CREATE TABLE IF NOT EXISTS `prompt_images` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `prompt_id` INT NOT NULL,
  `image_url` VARCHAR(255) NOT NULL,
  `caption` VARCHAR(255) NULL,
  `sort_order` INT DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_prompt_id` (`prompt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE IF NOT EXISTS `article_polls` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `content_id` INT NOT NULL,
  `question` VARCHAR(255) NOT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_content_poll` (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `article_poll_options` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `poll_id` INT NOT NULL,
  `option_text` VARCHAR(255) NOT NULL,
  `votes_count` INT DEFAULT 0,
  `sort_order` INT DEFAULT 0,
  INDEX `idx_poll_id` (`poll_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `article_poll_votes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `poll_id` INT NOT NULL,
  `option_id` INT NOT NULL,
  `user_id` INT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_vote` (`poll_id`, `ip_address`),
  INDEX `idx_poll_user` (`poll_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
