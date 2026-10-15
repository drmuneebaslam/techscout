-- Migration: 2026_10_15_000001_create_content_secondary_categories_and_visitor_logs.sql
-- Purpose: 
-- 1. Create content_secondary_categories table to eliminate SQLSTATE[42S02]
-- 2. Modify newsletter_subscriptions.subscriber_type from ENUM to VARCHAR(64) to eliminate SQLSTATE[01000] warning 1265
-- 3. Create visitor_logs table for comprehensive visitor tracking intelligence
-- 4. Ensure post_views and newsletter_delivery_logs exist
-- 5. Seed initial maintenance timezone settings

-- 1. Secondary Categories Table
CREATE TABLE IF NOT EXISTS `content_secondary_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `content_id` bigint unsigned NOT NULL,
  `category_id` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `content_category` (`content_id`,`category_id`),
  KEY `idx_csc_category` (`category_id`),
  KEY `idx_csc_content` (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Newsletter Subscriptions Column Expansion
ALTER TABLE `newsletter_subscriptions` MODIFY COLUMN `subscriber_type` VARCHAR(64) NOT NULL DEFAULT 'all';

-- 3. Auxiliary tables if missing
CREATE TABLE IF NOT EXISTS `post_views` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `content_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `referrer` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pv_content` (`content_id`),
  KEY `idx_pv_created` (`created_at`),
  KEY `idx_pv_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `newsletter_delivery_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` bigint unsigned NOT NULL,
  `subscriber_id` bigint unsigned NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('delivered','bounced','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'delivered',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `sent_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ndl_issue` (`issue_id`),
  KEY `idx_ndl_subscriber` (`subscriber_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Comprehensive Visitor Intelligence Logs Table
CREATE TABLE IF NOT EXISTS `visitor_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `visitor_hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `username` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_method` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'GET',
  `request_uri` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `route_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `http_referer` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referrer_domain` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser_version` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `os` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_type` enum('desktop','mobile','tablet','bot') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'desktop',
  `is_bot` tinyint(1) NOT NULL DEFAULT '0',
  `bot_name` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `http_status` smallint NOT NULL DEFAULT '200',
  `response_time_ms` decimal(8,2) DEFAULT '0.00',
  `client_language` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_timezone` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_vl_created` (`created_at`),
  KEY `idx_vl_ip` (`ip_address`),
  KEY `idx_vl_hash` (`visitor_hash`),
  KEY `idx_vl_user` (`user_id`),
  KEY `idx_vl_bot` (`is_bot`),
  KEY `idx_vl_device` (`device_type`),
  KEY `idx_vl_uri` (`request_uri`(191)),
  KEY `idx_vl_country` (`country_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Seed Maintenance UTC Settings if missing
INSERT INTO `site_settings` (`key_name`, `key_value`, `updated_at`)
VALUES 
  ('site_maintenance_until_utc', '', NOW()),
  ('site_maintenance_countdown_minutes', '45', NOW())
ON DUPLICATE KEY UPDATE `key_name` = `key_name`;
