-- ============================================================================
-- TechScout Enterprise Features & Infrastructure Upgrade V2
-- Standalone Non-Destructive DDL Script
-- Tables: incoming_webhooks, incoming_webhook_logs, custom_popups
-- Safe for execution on InfinityFree / MariaDB / MySQL 5.7+ / MySQL 8.0+
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. INCOMING WEBHOOKS TABLE
CREATE TABLE IF NOT EXISTS `incoming_webhooks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `secret_key` varchar(128) NOT NULL,
  `target_table` varchar(100) NOT NULL DEFAULT 'contents',
  `mapping_rules_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `auth_type` enum('none','bearer','signature') NOT NULL DEFAULT 'none',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `received_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `last_received_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_inwh_slug` (`slug`),
  KEY `idx_inwh_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. INCOMING WEBHOOK LOGS TABLE
CREATE TABLE IF NOT EXISTS `incoming_webhook_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `webhook_id` int(10) unsigned DEFAULT NULL,
  `endpoint_slug` varchar(100) NOT NULL,
  `headers_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `status` enum('success','failed','ignored') NOT NULL DEFAULT 'success',
  `response_code` int(11) NOT NULL DEFAULT 200,
  `records_affected` int(11) NOT NULL DEFAULT 0,
  `error_message` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_inwh_log_wh` (`webhook_id`),
  KEY `idx_inwh_log_slug` (`endpoint_slug`),
  KEY `idx_inwh_log_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. CUSTOM DYNAMIC POPUPS TABLE
CREATE TABLE IF NOT EXISTS `custom_popups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `badge_text` varchar(100) NOT NULL DEFAULT 'FEATURED ANNOUNCEMENT',
  `headline` varchar(255) NOT NULL,
  `content_html` mediumtext NOT NULL,
  `cta_button_text` varchar(100) NOT NULL DEFAULT 'Explore Now ↗',
  `cta_button_url` varchar(500) NOT NULL,
  `secondary_button_text` varchar(100) DEFAULT NULL,
  `secondary_button_url` varchar(500) DEFAULT NULL,
  `target_audience` enum('all','guests_only','logged_in_only','donors_only') NOT NULL DEFAULT 'all',
  `display_trigger` enum('immediate','delay','scroll_depth','exit_intent') NOT NULL DEFAULT 'delay',
  `trigger_delay_seconds` int(11) NOT NULL DEFAULT 3,
  `trigger_scroll_percentage` int(11) NOT NULL DEFAULT 50,
  `frequency` enum('always','once_per_session','once_ever') NOT NULL DEFAULT 'once_per_session',
  `popup_theme` enum('modern_gradient','minimal_card','dark_glass','cyber_amber') NOT NULL DEFAULT 'modern_gradient',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `views_count` int(10) unsigned NOT NULL DEFAULT 0,
  `clicks_count` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_popups_active` (`is_active`),
  KEY `idx_popups_audience` (`target_audience`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. API KEYS TABLE (REST API GATEWAY)
CREATE TABLE IF NOT EXISTS `api_keys` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `key_name` varchar(100) NOT NULL,
  `api_key_prefix` varchar(16) NOT NULL,
  `api_key_hash` varchar(128) NOT NULL,
  `scopes_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `rate_limit_per_min` int(11) NOT NULL DEFAULT 60,
  `requests_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `last_used_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ak_user` (`user_id`),
  KEY `idx_ak_prefix` (`api_key_prefix`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. SEED SAMPLE INCOMING WEBHOOKS, POPUP & REST API KEY (IF NONE EXIST)
INSERT IGNORE INTO `incoming_webhooks` (`id`, `name`, `slug`, `secret_key`, `target_table`, `mapping_rules_json`, `auth_type`, `is_active`) VALUES
(1, 'General Content Ingestion Webhook', 'general-ingest', 'whsec_ts_live_9a8b7c6d5e4f3a2b1c0d9e8f7a6b5c4d', 'contents', '[{"json_key":"title","target_column":"title"},{"json_key":"summary","target_column":"summary"},{"json_key":"slug","target_column":"slug"},{"json_key":"content_type","target_column":"content_type"}]', 'none', 1),
(2, 'AI Tool Sync Pipeline', 'tool-sync', 'whsec_ts_live_f1e2d3c4b5a69788796a5b4c3d2e1f0a', 'ai_tools', '[{"json_key":"title","target_column":"title"},{"json_key":"website_url","target_column":"website_url"},{"json_key":"pricing_type","target_column":"pricing_type"}]', 'bearer', 1);

INSERT IGNORE INTO `custom_popups` (`id`, `title`, `badge_text`, `headline`, `content_html`, `cta_button_text`, `cta_button_url`, `target_audience`, `display_trigger`, `trigger_delay_seconds`, `frequency`, `popup_theme`, `is_active`) VALUES
(1, 'Welcome to TechScout 2.0', '⚡ NEW DISCOVERY RADAR', 'Explore 1,200+ Verified AI Tools & Prompt Blueprints', 'Welcome to the updated TechScout platform. Discover real-time benchmark ratings, open-source model checkpoints, and verified creator prompts.', 'Browse AI Directory ↗', '/tools', 'all', 'delay', 4, 'once_per_session', 'modern_gradient', 1);

INSERT IGNORE INTO `api_keys` (`id`, `user_id`, `key_name`, `api_key_prefix`, `api_key_hash`, `scopes_json`, `rate_limit_per_min`, `is_active`) VALUES
(1, 1, 'Master Developer Live Key', 'ts_live_admin', '484ae74d21495767eed8d94e57d646aca08d822d02f6a8c6661e34abb89ee6f9', '[\"tools:read\",\"prompts:read\",\"gpts:read\",\"articles:read\",\"admin:telemetry\"]', 120, 1);

SET FOREIGN_KEY_CHECKS = 1;

