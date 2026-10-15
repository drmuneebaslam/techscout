-- ========================================================
-- TechScout Customized Schema & Granular Seed Package
-- Generated: 2026-10-05 11:25:03 (Pakistani Standard Time)
-- Source Database: techscout_db
-- ========================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET NAMES utf8mb4;

--
-- Table structure for: `admin_role_permissions`
--
DROP TABLE IF EXISTS `admin_role_permissions`;
CREATE TABLE `admin_role_permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int unsigned NOT NULL,
  `permission_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_role_perm` (`role_id`,`permission_key`),
  KEY `idx_arp_role` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `admin_roles`
--
DROP TABLE IF EXISTS `admin_roles`;
CREATE TABLE `admin_roles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `role_key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_key` (`role_key`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `advertisements`
--
DROP TABLE IF EXISTS `advertisements`;
CREATE TABLE `advertisements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `advertiser_user_id` bigint unsigned NOT NULL,
  `slot_location` enum('header_banner','article_inline','sidebar_sticky','category_featured','newsletter_top') COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `impression_count` bigint unsigned NOT NULL DEFAULT '0',
  `click_count` int unsigned NOT NULL DEFAULT '0',
  `cost_usd` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('pending','active','paused','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_ads_user` (`advertiser_user_id`),
  CONSTRAINT `fk_ads_user` FOREIGN KEY (`advertiser_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `ai_tools`
--
DROP TABLE IF EXISTS `ai_tools`;
CREATE TABLE `ai_tools` (
  `content_id` bigint unsigned NOT NULL,
  `website_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `docs_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discord_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_handle` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pricing_model` enum('free','freemium','paid','subscription','free_trial','open_source','contact_pricing') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'freemium',
  `secondary_categories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `use_cases` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `free_trial_available` tinyint(1) NOT NULL DEFAULT '1',
  `free_trial_days` int DEFAULT '14',
  `pricing_starting_at` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pricing_billing_cycle` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'monthly',
  `discount_code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_deal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `features_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `platforms_supported` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `api_available` tinyint(1) NOT NULL DEFAULT '0',
  `open_source_repo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Proprietary',
  `alternatives_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `comparison_highlights` text COLLATE utf8mb4_unicode_ci,
  `editorial_review` text COLLATE utf8mb4_unicode_ci,
  `pros_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `cons_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `verified_by_techscout` tinyint(1) NOT NULL DEFAULT '0',
  `rating_avg` decimal(3,2) NOT NULL DEFAULT '0.00',
  `rating_count` int unsigned NOT NULL DEFAULT '0',
  `rating_ease_of_use` decimal(3,2) NOT NULL DEFAULT '4.80',
  `rating_value_for_money` decimal(3,2) NOT NULL DEFAULT '4.70',
  `rating_features` decimal(3,2) NOT NULL DEFAULT '4.90',
  `rating_performance` decimal(3,2) NOT NULL DEFAULT '4.85',
  `rating_support` decimal(3,2) NOT NULL DEFAULT '4.60',
  `screenshots_gallery` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `affiliate_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_affiliate_slug` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `affiliate_commission_rate` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`content_id`),
  CONSTRAINT `fk_ai_tools_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ai_tools_chk_1` CHECK (json_valid(`secondary_categories`)),
  CONSTRAINT `ai_tools_chk_2` CHECK (json_valid(`use_cases`)),
  CONSTRAINT `ai_tools_chk_3` CHECK (json_valid(`features_list`)),
  CONSTRAINT `ai_tools_chk_4` CHECK (json_valid(`platforms_supported`)),
  CONSTRAINT `ai_tools_chk_5` CHECK (json_valid(`alternatives_list`)),
  CONSTRAINT `ai_tools_chk_6` CHECK (json_valid(`pros_list`)),
  CONSTRAINT `ai_tools_chk_7` CHECK (json_valid(`cons_list`)),
  CONSTRAINT `ai_tools_chk_8` CHECK (json_valid(`screenshots_gallery`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `analytics_events`
--
DROP TABLE IF EXISTS `analytics_events`;
CREATE TABLE `analytics_events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `event_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_id` bigint unsigned DEFAULT NULL,
  `author_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `referrer_domain` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_type` enum('desktop','mobile','tablet','bot') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'desktop',
  `duration_seconds` int unsigned DEFAULT '0',
  `metadata_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  PRIMARY KEY (`id`),
  KEY `idx_analytics_type_time` (`event_type`,`event_time`),
  KEY `idx_analytics_lookup` (`event_time`,`event_type`,`content_id`),
  CONSTRAINT `analytics_events_chk_1` CHECK (json_valid(`metadata_json`))
) ENGINE=InnoDB AUTO_INCREMENT=1284 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `api_keys`
--
DROP TABLE IF EXISTS `api_keys`;
CREATE TABLE `api_keys` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `key_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_key_prefix` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_key_hash` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scopes_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `rate_limit_per_min` int NOT NULL DEFAULT '60',
  `requests_count` bigint unsigned NOT NULL DEFAULT '0',
  `last_used_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ak_user` (`user_id`),
  KEY `idx_ak_prefix` (`api_key_prefix`),
  CONSTRAINT `api_keys_chk_1` CHECK (json_valid(`scopes_json`))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `article_co_authors`
--
DROP TABLE IF EXISTS `article_co_authors`;
CREATE TABLE `article_co_authors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int unsigned NOT NULL,
  `author_id` int unsigned NOT NULL,
  `role` enum('lead_author','co_author','technical_reviewer') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'co_author',
  `revenue_share_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `invitation_status` enum('pending','accepted','declined') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'accepted',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_article_author` (`article_id`,`author_id`),
  KEY `idx_aca_article` (`article_id`),
  KEY `idx_aca_author` (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `article_poll_options`
--
DROP TABLE IF EXISTS `article_poll_options`;
CREATE TABLE `article_poll_options` (
  `id` int NOT NULL AUTO_INCREMENT,
  `poll_id` int NOT NULL,
  `option_text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `votes_count` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_poll_id` (`poll_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `article_poll_votes`
--
DROP TABLE IF EXISTS `article_poll_votes`;
CREATE TABLE `article_poll_votes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `poll_id` int NOT NULL,
  `option_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `voted_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_poll_ip` (`poll_id`,`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `article_polls`
--
DROP TABLE IF EXISTS `article_polls`;
CREATE TABLE `article_polls` (
  `id` int NOT NULL AUTO_INCREMENT,
  `content_id` int NOT NULL,
  `question` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_content_id` (`content_id`),
  KEY `idx_polls_content_active` (`content_id`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `articles`
--
DROP TABLE IF EXISTS `articles`;
CREATE TABLE `articles` (
  `content_id` bigint unsigned NOT NULL,
  `reading_time_mins` smallint unsigned NOT NULL DEFAULT '5',
  `difficulty_level` enum('beginner','intermediate','advanced','expert') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'intermediate',
  `table_of_contents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `co_authors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `editor_attribution` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `citation_sources` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `code_snippets_count` smallint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`content_id`),
  CONSTRAINT `fk_articles_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `articles_chk_1` CHECK (json_valid(`table_of_contents`)),
  CONSTRAINT `articles_chk_2` CHECK (json_valid(`co_authors`)),
  CONSTRAINT `articles_chk_3` CHECK (json_valid(`citation_sources`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `audit_logs`
--
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `actor_role` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_domain` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'general',
  `entity_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Pakistan',
  `device_info` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `before_state` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `after_state` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_audit_created` (`created_at`),
  KEY `idx_audit_filter` (`created_at`,`action`,`user_id`),
  CONSTRAINT `audit_logs_chk_1` CHECK (json_valid(`details`)),
  CONSTRAINT `audit_logs_chk_2` CHECK (json_valid(`before_state`)),
  CONSTRAINT `audit_logs_chk_3` CHECK (json_valid(`after_state`))
) ENGINE=InnoDB AUTO_INCREMENT=287 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `author_applications`
--
DROP TABLE IF EXISTS `author_applications`;
CREATE TABLE `author_applications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `display_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `headline` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `portfolio_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `github_handle` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_handle` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `specializations` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `topic_interests` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `sample_pitch` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sample_file_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requested_prompts` tinyint(1) NOT NULL DEFAULT '0',
  `requested_gpts` tinyint(1) NOT NULL DEFAULT '0',
  `requested_tools` tinyint(1) NOT NULL DEFAULT '0',
  `requested_credits` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `admin_feedback` text COLLATE utf8mb4_unicode_ci,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_author_apps_status` (`status`),
  KEY `idx_author_apps_user` (`user_id`),
  CONSTRAINT `fk_author_apps_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `author_applications_chk_1` CHECK (json_valid(`topic_interests`))
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `author_feedbacks`
--
DROP TABLE IF EXISTS `author_feedbacks`;
CREATE TABLE `author_feedbacks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `author_id` bigint unsigned NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','reviewed','resolved') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `author_id` (`author_id`),
  CONSTRAINT `fk_author_feedbacks_user` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `author_followers`
--
DROP TABLE IF EXISTS `author_followers`;
CREATE TABLE `author_followers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `author_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_author_user` (`author_id`,`user_id`),
  KEY `idx_author` (`author_id`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for: `author_payout_methods`
--
DROP TABLE IF EXISTS `author_payout_methods`;
CREATE TABLE `author_payout_methods` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `author_id` bigint unsigned NOT NULL,
  `method_type` enum('bank_wire','iban_sepa','paypal','stripe_connect','crypto_usdc') COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_holder_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_number_or_iban` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `routing_or_swift` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `crypto_wallet_address` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `crypto_network` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'ERC-20 (Ethereum)',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('verified','pending_verification','active','disabled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'verified',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_apm_author` (`author_id`),
  KEY `idx_apm_default` (`author_id`,`is_default`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `author_payout_requests`
--
DROP TABLE IF EXISTS `author_payout_requests`;
CREATE TABLE `author_payout_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payout_currency` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT 'USDT',
  `payout_wallet` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('requested','processing','completed','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'requested',
  `txid` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `processed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_status` (`user_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `author_payouts`
--
DROP TABLE IF EXISTS `author_payouts`;
CREATE TABLE `author_payouts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ledger_reference_no` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `author_id` bigint unsigned NOT NULL,
  `payout_method_id` int unsigned DEFAULT NULL,
  `amount_usd` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_deducted` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bank_tax_deducted` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fee_deducted` decimal(10,2) NOT NULL DEFAULT '0.00',
  `net_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `credits_deducted` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payout_method` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'paypal',
  `payout_address` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `transaction_reference` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `receipt_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('pending','approved','paid','rejected') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `admin_notes` text COLLATE utf8mb4_general_ci,
  `processed_by` bigint unsigned DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_payouts_author_status` (`author_id`,`status`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for: `author_strikes`
--
DROP TABLE IF EXISTS `author_strikes`;
CREATE TABLE `author_strikes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `strike_level` tinyint unsigned NOT NULL DEFAULT '1',
  `penalty_type` enum('warning','suspension','ban') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'warning',
  `reason` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `issued_by_user_id` bigint unsigned DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_strikes` (`user_id`,`is_active`),
  KEY `idx_expires` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `author_tier_history`
--
DROP TABLE IF EXISTS `author_tier_history`;
CREATE TABLE `author_tier_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int unsigned NOT NULL,
  `tier_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tier_level` int NOT NULL DEFAULT '1',
  `tier_title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cpm_multiplier` decimal(4,2) NOT NULL DEFAULT '1.00',
  `unlocked_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_th_author` (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `author_tip_balances`
--
DROP TABLE IF EXISTS `author_tip_balances`;
CREATE TABLE `author_tip_balances` (
  `user_id` int NOT NULL,
  `total_earned` decimal(10,2) DEFAULT '0.00',
  `current_balance` decimal(10,2) DEFAULT '0.00',
  `total_withdrawn` decimal(10,2) DEFAULT '0.00',
  `preferred_payout_wallet` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `author_tips`
--
DROP TABLE IF EXISTS `author_tips`;
CREATE TABLE `author_tips` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_id` int unsigned NOT NULL,
  `article_id` int unsigned DEFAULT NULL,
  `tipper_user_id` bigint unsigned DEFAULT NULL,
  `tipper_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Generous Supporter',
  `tipper_email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount_usd` decimal(10,2) NOT NULL,
  `platform_fee_usd` decimal(10,2) NOT NULL DEFAULT '0.00',
  `net_amount_usd` decimal(10,2) NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `is_anonymous` tinyint(1) NOT NULL DEFAULT '0',
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'card_stripe',
  `status` enum('completed','pending','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference_no` (`reference_no`),
  KEY `idx_tip_author` (`author_id`),
  KEY `idx_tip_article` (`article_id`),
  KEY `idx_tip_created` (`created_at`),
  KEY `idx_author_tips_status_date` (`status`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `authors`
--
DROP TABLE IF EXISTS `authors`;
CREATE TABLE `authors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `display_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `headline` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `avatar_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expertise_tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `social_links` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `verification_badge` enum('none','verified_builder','expert','staff','top_creator') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `custom_badge_label` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_badge_color` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reputation_score` int unsigned NOT NULL DEFAULT '0',
  `reputation_level` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Scout Initiate',
  `tier_override` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tier_title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_tier_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bronze',
  `tier_sublevel` int NOT NULL DEFAULT '1',
  `tier_multiplier` decimal(4,2) NOT NULL DEFAULT '1.00',
  `referral_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `promo_credits` decimal(10,2) NOT NULL DEFAULT '0.00',
  `can_publish_prompts` tinyint(1) NOT NULL DEFAULT '0',
  `can_publish_gpts` tinyint(1) NOT NULL DEFAULT '0',
  `can_publish_tools` tinyint(1) NOT NULL DEFAULT '0',
  `can_use_templates` tinyint(1) NOT NULL DEFAULT '0',
  `monetization_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ineligible',
  `follower_count` int unsigned NOT NULL DEFAULT '0',
  `article_count` int unsigned NOT NULL DEFAULT '0',
  `total_views` bigint unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_monetized` tinyint(1) DEFAULT '0',
  `monetization_applied_at` datetime DEFAULT NULL,
  `monetization_approved_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_authors_reputation` (`reputation_score`),
  CONSTRAINT `fk_authors_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `authors_chk_1` CHECK (json_valid(`expertise_tags`)),
  CONSTRAINT `authors_chk_2` CHECK (json_valid(`social_links`))
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `bookmarks`
--
DROP TABLE IF EXISTS `bookmarks`;
CREATE TABLE `bookmarks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `content_id` bigint unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_content_bookmark` (`user_id`,`content_id`),
  KEY `fk_bookmarks_content` (`content_id`),
  CONSTRAINT `fk_bookmarks_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_bookmarks_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `campaigns`
--
DROP TABLE IF EXISTS `campaigns`;
CREATE TABLE `campaigns` (
  `content_id` bigint unsigned NOT NULL,
  `campaign_type` enum('launch','hackathon','contest','sponsorship','referral') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hackathon',
  `banner_image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `prize_pool` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rules_markdown` text COLLATE utf8mb4_unicode_ci,
  `submission_fields` text COLLATE utf8mb4_unicode_ci,
  `enable_referral_system` tinyint(1) DEFAULT '1',
  `enable_public_voting` tinyint(1) DEFAULT '1',
  `external_partner_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`content_id`),
  CONSTRAINT `fk_campaigns_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `categories`
--
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int unsigned DEFAULT NULL,
  `topic_id` int unsigned DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'folder',
  `color_hex` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '#6366f1',
  `sort_order` int NOT NULL DEFAULT '0',
  `content_count` int unsigned NOT NULL DEFAULT '0',
  `applicable_to` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `is_featured_home` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_categories_slug` (`slug`),
  KEY `idx_categories_parent_id` (`parent_id`),
  KEY `idx_categories_applicable_to` (`applicable_to`),
  KEY `idx_categories_sort_order` (`sort_order`),
  KEY `idx_cat_parent_scope` (`parent_id`,`applicable_to`),
  KEY `idx_cat_scope_featured` (`applicable_to`,`is_featured_home`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `challenge_submissions`
--
DROP TABLE IF EXISTS `challenge_submissions`;
CREATE TABLE `challenge_submissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `project_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `detailed_description` longtext COLLATE utf8mb4_unicode_ci,
  `tech_stack` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `github_repo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `live_demo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_demo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linked_article_id` bigint unsigned DEFAULT NULL,
  `referral_code` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referral_clicks` int unsigned DEFAULT '0',
  `referral_signups` int unsigned DEFAULT '0',
  `page_views` int unsigned DEFAULT '0',
  `votes_count` int unsigned DEFAULT '0',
  `status` enum('draft','submitted','under_review','shortlisted','winner','runner_up','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'submitted',
  `score` decimal(5,2) DEFAULT NULL,
  `judge_feedback` text COLLATE utf8mb4_unicode_ci,
  `prize_awarded` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_campaign_id` (`campaign_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_referral_code` (`referral_code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `challenge_votes`
--
DROP TABLE IF EXISTS `challenge_votes`;
CREATE TABLE `challenge_votes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `submission_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user_sub` (`submission_id`,`user_id`),
  KEY `idx_submission_id` (`submission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `comment_reactions`
--
DROP TABLE IF EXISTS `comment_reactions`;
CREATE TABLE `comment_reactions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` int unsigned NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reaction_type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'helpful',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cr_comment_user` (`comment_id`,`user_id`),
  KEY `idx_cr_comment_ip` (`comment_id`,`ip_address`),
  KEY `idx_cr_reaction` (`reaction_type`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `comments`
--
DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `content_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `comment_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `reactions_json` text COLLATE utf8mb4_unicode_ci,
  `status` enum('visible','flagged','hidden','deleted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'visible',
  `reaction_count` int unsigned NOT NULL DEFAULT '0',
  `reply_count` int unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_comments_content` (`content_id`),
  KEY `fk_comments_user` (`user_id`),
  KEY `idx_comments_content_time` (`content_id`,`created_at`),
  KEY `idx_comments_content_pinned` (`content_id`,`is_pinned`),
  KEY `idx_comments_created` (`created_at`),
  CONSTRAINT `fk_comments_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `contact_messages`
--
DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('unread','read','replied') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unread',
  `reply_text` text COLLATE utf8mb4_unicode_ci,
  `replied_at` datetime DEFAULT NULL,
  `replied_by` int unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_contact_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `content_categories`
--
DROP TABLE IF EXISTS `content_categories`;
CREATE TABLE `content_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `content_id` bigint unsigned NOT NULL,
  `category_id` int unsigned NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_content_category` (`content_id`,`category_id`),
  KEY `idx_cc_category` (`category_id`),
  KEY `idx_cc_content` (`content_id`),
  KEY `idx_cc_primary` (`is_primary`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `content_secondary_categories`
--
DROP TABLE IF EXISTS `content_secondary_categories`;
CREATE TABLE `content_secondary_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `content_id` bigint unsigned NOT NULL,
  `category_id` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `content_category` (`content_id`,`category_id`),
  KEY `idx_csc_category` (`category_id`),
  KEY `idx_csc_content` (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `content_filters`
--
DROP TABLE IF EXISTS `content_filters`;
CREATE TABLE `content_filters` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `pattern` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `match_type` enum('exact','contains','regex') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'contains',
  `replacement` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '***',
  `action` enum('replace','flag_review','auto_quarantine') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'replace',
  `severity` enum('low','medium','high','critical') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `content_revisions`
--
DROP TABLE IF EXISTS `content_revisions`;
CREATE TABLE `content_revisions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `content_id` bigint unsigned NOT NULL,
  `editor_id` bigint unsigned NOT NULL,
  `version_number` int unsigned NOT NULL DEFAULT '1',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body_html` longtext COLLATE utf8mb4_unicode_ci,
  `change_summary` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_revisions_content` (`content_id`),
  CONSTRAINT `fk_revisions_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `contents`
--
DROP TABLE IF EXISTS `contents`;
CREATE TABLE `contents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_type` enum('article','ai_tool','gpt_listing','prompt','software','resource','collection','campaign') COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_id` bigint unsigned NOT NULL,
  `category_id` int unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body_html` longtext COLLATE utf8mb4_unicode_ci,
  `featured_image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','in_review','scheduled','published','rejected','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `custom_badge` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `badge_color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `badge_bg` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visibility` enum('public','members_only','unlisted','private') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_trending` tinyint(1) NOT NULL DEFAULT '0',
  `is_sponsored` tinyint(1) NOT NULL DEFAULT '0',
  `sponsored_by` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quality_score` decimal(4,2) NOT NULL DEFAULT '0.00',
  `editorial_notes` text COLLATE utf8mb4_unicode_ci,
  `internal_notes` longtext COLLATE utf8mb4_unicode_ci,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `seo_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `canonical_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `view_count` bigint unsigned NOT NULL DEFAULT '0',
  `unique_view_count` bigint unsigned NOT NULL DEFAULT '0',
  `reaction_count` int unsigned NOT NULL DEFAULT '0',
  `comment_count` int unsigned NOT NULL DEFAULT '0',
  `bookmark_count` int unsigned NOT NULL DEFAULT '0',
  `share_count` int unsigned NOT NULL DEFAULT '0',
  `published_at` datetime DEFAULT NULL,
  `scheduled_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `slug` (`slug`),
  KEY `fk_contents_author` (`author_id`),
  KEY `fk_contents_category` (`category_id`),
  KEY `idx_contents_type_status_pub` (`content_type`,`status`,`published_at`),
  KEY `idx_contents_slug` (`slug`),
  KEY `idx_contents_pinned` (`is_pinned`),
  KEY `idx_contents_composite` (`status`,`content_type`,`is_pinned`,`created_at`),
  FULLTEXT KEY `ft_contents_search` (`title`,`summary`,`body_html`),
  CONSTRAINT `fk_contents_author` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_contents_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `creator_referrals`
--
DROP TABLE IF EXISTS `creator_referrals`;
CREATE TABLE `creator_referrals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `referrer_author_id` int unsigned NOT NULL,
  `referred_user_id` bigint unsigned NOT NULL,
  `referred_author_id` int unsigned DEFAULT NULL,
  `referral_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commission_rate` decimal(5,2) NOT NULL DEFAULT '5.00',
  `expires_at` datetime NOT NULL,
  `total_commission_earned` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cr_referrer` (`referrer_author_id`),
  KEY `idx_cr_referred` (`referred_author_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `cron_logs`
--
DROP TABLE IF EXISTS `cron_logs`;
CREATE TABLE `cron_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `job_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('success','failed','running') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'success',
  `output` longtext COLLATE utf8mb4_unicode_ci,
  `duration_ms` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_job_created` (`job_name`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `custom_api_endpoints`
--
DROP TABLE IF EXISTS `custom_api_endpoints`;
CREATE TABLE `custom_api_endpoints` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endpoint_type` enum('table_schema','table_list','table_data','custom_json') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'table_data',
  `target_table` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `selected_columns` text COLLATE utf8mb4_unicode_ci,
  `where_conditions_json` text COLLATE utf8mb4_unicode_ci,
  `order_by_column` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'id',
  `order_direction` enum('ASC','DESC') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DESC',
  `result_limit` int NOT NULL DEFAULT '50',
  `custom_response_template` longtext COLLATE utf8mb4_unicode_ci,
  `auth_required` tinyint(1) NOT NULL DEFAULT '0',
  `secret_token` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `request_count` bigint unsigned NOT NULL DEFAULT '0',
  `last_accessed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_cae_slug` (`slug`),
  KEY `idx_cae_active` (`is_active`),
  KEY `idx_cae_type` (`endpoint_type`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `custom_popups`
--
DROP TABLE IF EXISTS `custom_popups`;
CREATE TABLE `custom_popups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `badge_text` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'FEATURED ANNOUNCEMENT',
  `headline` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_html` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `cta_button_text` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Explore Now ↗',
  `cta_button_url` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secondary_button_text` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondary_button_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_audience` enum('all','guests_only','logged_in_only','donors_only') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `display_trigger` enum('immediate','delay','scroll_depth','exit_intent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'delay',
  `trigger_delay_seconds` int NOT NULL DEFAULT '3',
  `trigger_scroll_percentage` int NOT NULL DEFAULT '50',
  `frequency` enum('always','once_per_session','once_ever') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'once_per_session',
  `popup_theme` enum('modern_gradient','minimal_card','dark_glass','cyber_amber') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'modern_gradient',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `views_count` int unsigned NOT NULL DEFAULT '0',
  `clicks_count` int unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_popups_active` (`is_active`),
  KEY `idx_popups_audience` (`target_audience`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `database_backups`
--
DROP TABLE IF EXISTS `database_backups`;
CREATE TABLE `database_backups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filepath` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size_bytes` bigint unsigned NOT NULL,
  `table_count` int unsigned NOT NULL,
  `total_rows` bigint unsigned DEFAULT '0',
  `compression` enum('sql','gzip') COLLATE utf8mb4_unicode_ci DEFAULT 'sql',
  `checksum` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `editorial_badges`
--
DROP TABLE IF EXISTS `editorial_badges`;
CREATE TABLE `editorial_badges` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#38bdf8',
  `bg_color` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'rgba(56,189,248,0.15)',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `entity_reports`
--
DROP TABLE IF EXISTS `entity_reports`;
CREATE TABLE `entity_reports` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `reporter_user_id` bigint unsigned DEFAULT NULL,
  `entity_type` enum('article','ai_tool','gpt_listing','prompt','job','comment','campaign','author') COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` int unsigned NOT NULL,
  `entity_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason` enum('spam','broken_or_misleading','plagiarism_copyright','inappropriate_harmful','malicious_prompt_injection','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','reviewed','actioned','dismissed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `action_taken` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_er_status` (`status`),
  KEY `idx_er_entity` (`entity_type`,`entity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `failed_login_attempts`
--
DROP TABLE IF EXISTS `failed_login_attempts`;
CREATE TABLE `failed_login_attempts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `identifier` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attempted_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ip_attempt` (`ip_address`,`attempted_at`),
  KEY `idx_identifier` (`identifier`,`attempted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `feature_flags`
--
DROP TABLE IF EXISTS `feature_flags`;
CREATE TABLE `feature_flags` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `flag_key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `is_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `flag_key` (`flag_key`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `financial_ledger`
--
DROP TABLE IF EXISTS `financial_ledger`;
CREATE TABLE `financial_ledger` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `entry_type` enum('credit','debit') COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount_usd` decimal(12,2) NOT NULL,
  `credits` decimal(12,2) NOT NULL,
  `balance_before` decimal(12,2) NOT NULL DEFAULT '0.00',
  `balance_after` decimal(12,2) NOT NULL DEFAULT '0.00',
  `payout_method_id` int unsigned DEFAULT NULL,
  `payout_method_details` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('completed','pending','processing','reversed','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `performed_by_user_id` bigint unsigned DEFAULT NULL,
  `performed_by_role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'system',
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `internal_notes` text COLLATE utf8mb4_unicode_ci,
  `metadata_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference_no` (`reference_no`),
  KEY `idx_fl_author` (`author_id`),
  KEY `idx_fl_type` (`entry_type`),
  KEY `idx_fl_category` (`category`),
  KEY `idx_fl_created` (`created_at`),
  CONSTRAINT `financial_ledger_chk_1` CHECK (json_valid(`metadata_json`))
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `firewall_logs`
--
DROP TABLE IF EXISTS `firewall_logs`;
CREATE TABLE `firewall_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `threat_type` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_taken` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'blocked',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_fl_ip` (`ip_address`),
  KEY `idx_fl_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=37093 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `firewall_rules`
--
DROP TABLE IF EXISTS `firewall_rules`;
CREATE TABLE `firewall_rules` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `rule_type` enum('ip','cidr','user_agent','country') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ip',
  `target_value` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` enum('block','allow','challenge') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'block',
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hits_count` bigint unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_fw_target` (`target_value`),
  KEY `idx_fw_type` (`rule_type`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `follows`
--
DROP TABLE IF EXISTS `follows`;
CREATE TABLE `follows` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `follower_user_id` bigint unsigned NOT NULL,
  `following_author_id` bigint unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_follower_following` (`follower_user_id`,`following_author_id`),
  KEY `fk_follows_author` (`following_author_id`),
  CONSTRAINT `fk_follows_author` FOREIGN KEY (`following_author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_follows_follower` FOREIGN KEY (`follower_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `gpt_listings`
--
DROP TABLE IF EXISTS `gpt_listings`;
CREATE TABLE `gpt_listings` (
  `content_id` bigint unsigned NOT NULL,
  `openai_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_base` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'GPT-4o',
  `prompt_starters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `creator_handle` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `use_case` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`content_id`),
  CONSTRAINT `fk_gpts_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gpt_listings_chk_1` CHECK (json_valid(`prompt_starters`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `help_articles`
--
DROP TABLE IF EXISTS `help_articles`;
CREATE TABLE `help_articles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_slug` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'getting_started',
  `category_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Getting Started & Account',
  `category_icon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sparkles',
  `category_color` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#6366f1',
  `category_desc` text COLLATE utf8mb4_unicode_ci,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `badge` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'GUIDE',
  `summary` text COLLATE utf8mb4_unicode_ci,
  `key_takeaway` text COLLATE utf8mb4_unicode_ci,
  `content_html` longtext COLLATE utf8mb4_unicode_ci,
  `steps_json` longtext COLLATE utf8mb4_unicode_ci,
  `faqs_json` longtext COLLATE utf8mb4_unicode_ci,
  `read_time` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '3 min read',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_published` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `views_count` int unsigned NOT NULL DEFAULT '0',
  `helpful_count` int unsigned NOT NULL DEFAULT '0',
  `unhelpful_count` int unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_help_slug` (`slug`),
  KEY `idx_help_cat` (`category_slug`),
  KEY `idx_help_featured` (`is_featured`),
  KEY `idx_help_published` (`is_published`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `homepage_sections`
--
DROP TABLE IF EXISTS `homepage_sections`;
CREATE TABLE `homepage_sections` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `section_key` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `item_limit` int NOT NULL DEFAULT '6',
  `config_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `section_key` (`section_key`),
  CONSTRAINT `homepage_sections_chk_1` CHECK (json_valid(`config_json`))
) ENGINE=InnoDB AUTO_INCREMENT=67816 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `incoming_webhook_logs`
--
DROP TABLE IF EXISTS `incoming_webhook_logs`;
CREATE TABLE `incoming_webhook_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `webhook_id` int unsigned DEFAULT NULL,
  `endpoint_slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'success',
  `response_code` int NOT NULL DEFAULT '200',
  `records_affected` int NOT NULL DEFAULT '0',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_inwh_log_wh` (`webhook_id`),
  KEY `idx_inwh_log_slug` (`endpoint_slug`),
  KEY `idx_inwh_log_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `incoming_webhooks`
--
DROP TABLE IF EXISTS `incoming_webhooks`;
CREATE TABLE `incoming_webhooks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret_key` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_table` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'contents',
  `supports_multi_table` tinyint(1) NOT NULL DEFAULT '1',
  `mapping_rules_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `auth_type` enum('none','bearer','signature') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `received_count` bigint unsigned NOT NULL DEFAULT '0',
  `last_received_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ingestion_mode` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'live_ingest',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_inwh_slug` (`slug`),
  KEY `idx_inwh_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `ip_lockouts`
--
DROP TABLE IF EXISTS `ip_lockouts`;
CREATE TABLE `ip_lockouts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_count` int unsigned DEFAULT '1',
  `locked_until` datetime NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip_address` (`ip_address`),
  KEY `idx_locked_until` (`locked_until`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `job_applications`
--
DROP TABLE IF EXISTS `job_applications`;
CREATE TABLE `job_applications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `job_id` int unsigned NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `applicant_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `applicant_email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `portfolio_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resume_text_or_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `cover_letter` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','reviewed','interviewing','accepted','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_job_app_job` (`job_id`),
  KEY `idx_job_app_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `jobs`
--
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_logo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Remote',
  `job_type` enum('Full-time','Contract','Remote','Part-time') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Remote',
  `salary_range` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apply_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_html` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_jobs_active` (`is_active`),
  KEY `idx_jobs_featured` (`is_featured`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `legal_pages`
--
DROP TABLE IF EXISTS `legal_pages`;
CREATE TABLE `legal_pages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtitle` text COLLATE utf8mb4_unicode_ci,
  `badge` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT 'LEGAL & COMPLIANCE',
  `badge_color` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT 'indigo',
  `version` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1.0',
  `effective_date` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'September 2026',
  `content_html` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `is_published` tinyint(1) NOT NULL DEFAULT '1',
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_legal_slug` (`slug`),
  KEY `idx_legal_published` (`is_published`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `mail_credentials`
--
DROP TABLE IF EXISTS `mail_credentials`;
CREATE TABLE `mail_credentials` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `provider` enum('smtp','resend','sendgrid','ses','mailgun','pythonanywhere_api') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'smtp',
  `from_name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `from_email` varchar(191) COLLATE utf8mb4_general_ci NOT NULL,
  `reply_to_email` varchar(191) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `host` varchar(191) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `port` int DEFAULT '587',
  `encryption` enum('tls','ssl','none') COLLATE utf8mb4_general_ci DEFAULT 'tls',
  `username` varchar(191) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `api_key` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `api_endpoint` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `custom_headers_json` text COLLATE utf8mb4_general_ci,
  `request_timeout` int NOT NULL DEFAULT '15',
  `payload_format` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'standard_json',
  `webhook_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT '0',
  `status` enum('active','inactive','testing') COLLATE utf8mb4_general_ci DEFAULT 'active',
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for: `mail_logs`
--
DROP TABLE IF EXISTS `mail_logs`;
CREATE TABLE `mail_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `to_email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `to_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_key` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body_html` longtext COLLATE utf8mb4_unicode_ci,
  `attachments_json` longtext COLLATE utf8mb4_unicode_ci,
  `driver` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'log',
  `credential_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tracking_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `open_count` int unsigned NOT NULL DEFAULT '0',
  `opened_at` datetime DEFAULT NULL,
  `click_count` int unsigned NOT NULL DEFAULT '0',
  `clicked_at` datetime DEFAULT NULL,
  `last_clicked_url` text COLLATE utf8mb4_unicode_ci,
  `user_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `status` enum('sent','failed','queued') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sent',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ml_email` (`to_email`),
  KEY `idx_ml_status` (`status`),
  KEY `idx_ml_token` (`tracking_token`)
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `mail_templates`
--
DROP TABLE IF EXISTS `mail_templates`;
CREATE TABLE `mail_templates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `template_key` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `html_content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `assigned_credential_id` bigint unsigned DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `template_key` (`template_key`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `media_files`
--
DROP TABLE IF EXISTS `media_files`;
CREATE TABLE `media_files` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL DEFAULT '1',
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` enum('image','video','audio','document') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'image',
  `file_size` bigint unsigned NOT NULL DEFAULT '0',
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'image/jpeg',
  `folder` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caption` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `copyright_info` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'TechScout Editorial / Unsplash License',
  `attribution` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_media_type` (`file_type`),
  KEY `idx_media_folder` (`folder`),
  KEY `idx_media_user` (`user_id`),
  CONSTRAINT `media_files_chk_1` CHECK (json_valid(`metadata_json`))
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `migrations`
--
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `migration_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  `applied_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `migration_name` (`migration_name`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `navigation_menus`
--
DROP TABLE IF EXISTS `navigation_menus`;
CREATE TABLE `navigation_menus` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `location` enum('main','secondary','footer','mobile','dropdown','mega_menu','external') COLLATE utf8mb4_general_ci NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `label` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `target` enum('_self','_blank') COLLATE utf8mb4_general_ci DEFAULT '_self',
  `icon_name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT '',
  `badge_text` varchar(50) COLLATE utf8mb4_general_ci DEFAULT '',
  `badge_color` varchar(50) COLLATE utf8mb4_general_ci DEFAULT '',
  `sort_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `mega_menu_data` longtext COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for: `newsletter_delivery_logs`
--
DROP TABLE IF EXISTS `newsletter_delivery_logs`;
CREATE TABLE `newsletter_delivery_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` bigint unsigned DEFAULT NULL,
  `campaign_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Direct Dispatch',
  `subscriber_id` bigint unsigned DEFAULT NULL,
  `to_email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `to_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tracking_token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('delivered','failed','opened','clicked') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'delivered',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `open_count` int unsigned NOT NULL DEFAULT '0',
  `opened_at` datetime DEFAULT NULL,
  `click_count` int unsigned NOT NULL DEFAULT '0',
  `clicked_at` datetime DEFAULT NULL,
  `last_clicked_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dispatched_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_token` (`tracking_token`),
  KEY `idx_email` (`to_email`),
  KEY `idx_issue` (`issue_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `newsletter_issues`
--
DROP TABLE IF EXISTS `newsletter_issues`;
CREATE TABLE `newsletter_issues` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `issue_number` int unsigned DEFAULT '1',
  `subject_line` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `preview_text` varchar(255) COLLATE utf8mb4_general_ci DEFAULT '',
  `template_type` varchar(100) COLLATE utf8mb4_general_ci DEFAULT 'weekly_digest',
  `body_html` longtext COLLATE utf8mb4_general_ci,
  `status` enum('draft','scheduled','sent','archived') COLLATE utf8mb4_general_ci DEFAULT 'draft',
  `scheduled_at` datetime DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL,
  `total_recipients` int unsigned DEFAULT '0',
  `open_count` int unsigned DEFAULT '0',
  `click_count` int unsigned DEFAULT '0',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for: `newsletter_subscriptions`
--
DROP TABLE IF EXISTS `newsletter_subscriptions`;
CREATE TABLE `newsletter_subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','subscribed','unsubscribed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'subscribed',
  `subscriber_type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `notifications`
--
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'system',
  `event_type` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'system',
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_notif_user` (`user_id`),
  CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `plagiarism_scans`
--
DROP TABLE IF EXISTS `plagiarism_scans`;
CREATE TABLE `plagiarism_scans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `content_id` bigint unsigned NOT NULL,
  `originality_score` decimal(5,2) NOT NULL DEFAULT '100.00',
  `duplicate_ratio` decimal(5,2) NOT NULL DEFAULT '0.00',
  `verdict` enum('clean','acceptable','suspicious','flagged_plagiarism') COLLATE utf8mb4_unicode_ci DEFAULT 'clean',
  `matched_content_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `overlapping_shingles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `total_shingles` int unsigned DEFAULT '0',
  `matching_shingles` int unsigned DEFAULT '0',
  `scanned_by` bigint unsigned DEFAULT NULL,
  `scanned_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_content_id` (`content_id`),
  KEY `idx_verdict` (`verdict`),
  CONSTRAINT `plagiarism_scans_chk_1` CHECK (json_valid(`matched_content_ids`)),
  CONSTRAINT `plagiarism_scans_chk_2` CHECK (json_valid(`overlapping_shingles`))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `post_views`
--
DROP TABLE IF EXISTS `post_views`;
CREATE TABLE `post_views` (
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
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for: `prompt_images`
--
DROP TABLE IF EXISTS `prompt_images`;
CREATE TABLE `prompt_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `prompt_id` int NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_prompt_id` (`prompt_id`),
  KEY `idx_prompt_sort` (`prompt_id`,`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `prompts`
--
DROP TABLE IF EXISTS `prompts`;
CREATE TABLE `prompts` (
  `content_id` bigint unsigned NOT NULL,
  `prompt_template` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_variables` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `target_models` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `copy_count` int unsigned NOT NULL DEFAULT '0',
  `sample_output` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `prompt_type` enum('text','image') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  PRIMARY KEY (`content_id`),
  KEY `idx_prompts_created_at` (`created_at`),
  CONSTRAINT `fk_prompts_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `prompts_chk_1` CHECK (json_valid(`template_variables`)),
  CONSTRAINT `prompts_chk_2` CHECK (json_valid(`target_models`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `reactions`
--
DROP TABLE IF EXISTS `reactions`;
CREATE TABLE `reactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `content_id` bigint unsigned NOT NULL,
  `reaction_type` enum('like','clap','rocket','insightful','mind_blown','bookmark') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_content_reaction` (`user_id`,`content_id`,`reaction_type`),
  KEY `fk_reactions_content` (`content_id`),
  CONSTRAINT `fk_reactions_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reactions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=240 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `reading_history`
--
DROP TABLE IF EXISTS `reading_history`;
CREATE TABLE `reading_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `content_id` bigint unsigned NOT NULL,
  `session_id` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `session_number` int NOT NULL DEFAULT '1',
  `started_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `progress_percent` int NOT NULL DEFAULT '0',
  `read_seconds` int NOT NULL DEFAULT '0',
  `last_read_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `completed_at` datetime DEFAULT NULL,
  `status` enum('reading','completed','re_reading') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'reading',
  `referrer_type` varchar(32) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'internal',
  `referrer_source` varchar(255) COLLATE utf8mb4_general_ci DEFAULT '/directory',
  PRIMARY KEY (`id`),
  KEY `idx_rh_user` (`user_id`),
  KEY `idx_rh_last_read` (`last_read_at`),
  KEY `idx_user_content` (`user_id`,`content_id`),
  KEY `idx_reading_history_lookup` (`content_id`,`last_read_at`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for: `redirects`
--
DROP TABLE IF EXISTS `redirects`;
CREATE TABLE `redirects` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `source_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_code` smallint unsigned NOT NULL DEFAULT '301',
  `hit_count` bigint unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `source_path` (`source_path`),
  KEY `idx_redirect_source` (`source_path`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `referral_commissions`
--
DROP TABLE IF EXISTS `referral_commissions`;
CREATE TABLE `referral_commissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `referral_id` bigint unsigned NOT NULL,
  `referrer_author_id` int unsigned NOT NULL,
  `ledger_reference_no` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount_usd` decimal(10,2) NOT NULL,
  `source_event` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_rc_referral` (`referral_id`),
  KEY `idx_rc_referrer` (`referrer_author_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `reports`
--
DROP TABLE IF EXISTS `reports`;
CREATE TABLE `reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reporter_user_id` bigint unsigned DEFAULT NULL,
  `content_id` bigint unsigned DEFAULT NULL,
  `comment_id` bigint unsigned DEFAULT NULL,
  `reason` enum('spam','plagiarism','misinformation','abusive','broken_link','copyright','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','investigating','resolved','dismissed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `resolved_by_user_id` bigint unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_reports_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `revenue_records`
--
DROP TABLE IF EXISTS `revenue_records`;
CREATE TABLE `revenue_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `author_id` bigint unsigned NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `credits_granted` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amount_usd` decimal(10,2) NOT NULL DEFAULT '0.00',
  `transaction_type` enum('credit_reward','view_milestone','upvote_bonus','editorial_grant','ad_redemption') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'credit_reward',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_rev_author` (`author_id`),
  CONSTRAINT `fk_rev_author` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `security_anomalies`
--
DROP TABLE IF EXISTS `security_anomalies`;
CREATE TABLE `security_anomalies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_type` enum('brute_force_lockout','new_ip_login','new_device_login','rapid_requests','sensitive_endpoint_breach','session_hijack_suspected') COLLATE utf8mb4_unicode_ci NOT NULL,
  `severity` enum('low','medium','high','critical') COLLATE utf8mb4_unicode_ci DEFAULT 'medium',
  `description` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `is_resolved` tinyint(1) DEFAULT '0',
  `resolved_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ip_created` (`ip_address`,`created_at`),
  KEY `idx_user_resolved` (`user_id`,`is_resolved`),
  CONSTRAINT `security_anomalies_chk_1` CHECK (json_valid(`metadata`))
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `seo_metadata`
--
DROP TABLE IF EXISTS `seo_metadata`;
CREATE TABLE `seo_metadata` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `path_pattern` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `og_image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `canonical_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `robots_directive` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'index, follow',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `path_pattern` (`path_pattern`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `site_announcements`
--
DROP TABLE IF EXISTS `site_announcements`;
CREATE TABLE `site_announcements` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ribbon_type` enum('info','announcement','warning','promo','maintenance') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'announcement',
  `banner_color` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#6366f1',
  `text_color` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ffffff',
  `action_label` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_audience` enum('all','guests','members','authors') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `is_dismissible` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `site_settings`
--
DROP TABLE IF EXISTS `site_settings`;
CREATE TABLE `site_settings` (
  `key_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key_value` text COLLATE utf8mb4_unicode_ci,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`key_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `sponsored_submissions`
--
DROP TABLE IF EXISTS `sponsored_submissions`;
CREATE TABLE `sponsored_submissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('tool','article','banner') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tool',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` int unsigned DEFAULT NULL,
  `author_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `body_content` longtext COLLATE utf8mb4_unicode_ci,
  `image_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pricing_amount_usd` decimal(10,2) NOT NULL DEFAULT '0.00',
  `crypto_currency` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USDT',
  `crypto_txid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `crypto_wallet_sent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending_review','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending_review',
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_spons_status` (`status`),
  KEY `idx_spons_type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `support_categories`
--
DROP TABLE IF EXISTS `support_categories`;
CREATE TABLE `support_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `department` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Engineering',
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `subcategories_json` text COLLATE utf8mb4_unicode_ci,
  `default_priority` enum('low','medium','high','urgent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `sla_hours` int unsigned NOT NULL DEFAULT '24',
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `support_tickets`
--
DROP TABLE IF EXISTS `support_tickets`;
CREATE TABLE `support_tickets` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ticket_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `department` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'General Engineering',
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `subcategory` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'General',
  `priority` enum('low','medium','high','urgent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `sla_due_at` datetime DEFAULT NULL,
  `is_escalated` tinyint(1) DEFAULT '0',
  `escalated_at` datetime DEFAULT NULL,
  `first_response_at` datetime DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('open','in_progress','waiting_user','resolved','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `assigned_to` bigint unsigned DEFAULT NULL,
  `last_reply_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ticket_number` (`ticket_number`),
  KEY `idx_st_user` (`user_id`),
  KEY `idx_st_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `system_error_logs`
--
DROP TABLE IF EXISTS `system_error_logs`;
CREATE TABLE `system_error_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `error_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ERROR',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `line` int unsigned DEFAULT NULL,
  `stack_trace` longtext COLLATE utf8mb4_unicode_ci,
  `request_uri` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_method` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_data` longtext COLLATE utf8mb4_unicode_ci,
  `request_headers` longtext COLLATE utf8mb4_unicode_ci,
  `user_id` bigint unsigned DEFAULT NULL,
  `user_details` longtext COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_details` longtext COLLATE utf8mb4_unicode_ci,
  `device_details` longtext COLLATE utf8mb4_unicode_ci,
  `resolved` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_err_type` (`error_type`),
  KEY `idx_err_resolved` (`resolved`),
  KEY `idx_err_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `system_settings`
--
DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings` (
  `key_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value_content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_group` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`key_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `ticket_canned_macros`
--
DROP TABLE IF EXISTS `ticket_canned_macros`;
CREATE TABLE `ticket_canned_macros` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'General',
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shortcut_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body_content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `usage_count` int unsigned DEFAULT '0',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shortcut_code` (`shortcut_code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `ticket_internal_notes`
--
DROP TABLE IF EXISTS `ticket_internal_notes`;
CREATE TABLE `ticket_internal_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint unsigned NOT NULL,
  `staff_user_id` bigint unsigned NOT NULL,
  `note_content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ticket` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `ticket_messages`
--
DROP TABLE IF EXISTS `ticket_messages`;
CREATE TABLE `ticket_messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `is_staff` tinyint(1) NOT NULL DEFAULT '0',
  `is_internal_note` tinyint(1) NOT NULL DEFAULT '0',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachment_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tm_ticket` (`ticket_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `topics`
--
DROP TABLE IF EXISTS `topics`;
CREATE TABLE `topics` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'layers',
  `color_hex` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '#6366f1',
  `banner_image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `follower_count` int unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_topics_slug` (`slug`),
  KEY `idx_topics_featured` (`is_featured`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `user_sessions`
--
DROP TABLE IF EXISTS `user_sessions`;
CREATE TABLE `user_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `session_token_hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_type` enum('desktop','mobile','tablet','bot','unknown') COLLATE utf8mb4_unicode_ci DEFAULT 'desktop',
  `browser_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Unknown Browser',
  `os_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Unknown OS',
  `location_hint` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Local Network',
  `is_current` tinyint(1) DEFAULT '0',
  `last_active_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_token_hash` (`session_token_hash`),
  KEY `idx_user_active` (`user_id`,`last_active_at`),
  KEY `idx_token_hash` (`session_token_hash`)
) ENGINE=InnoDB AUTO_INCREMENT=572 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `user_subscriptions`
--
DROP TABLE IF EXISTS `user_subscriptions`;
CREATE TABLE `user_subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `subscribable_type` enum('topic','category','author') COLLATE utf8mb4_unicode_ci NOT NULL,
  `subscribable_id` bigint unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_subscription` (`user_id`,`subscribable_type`,`subscribable_id`),
  KEY `idx_user_subs_type_id` (`subscribable_type`,`subscribable_id`),
  CONSTRAINT `fk_user_subs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=212 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `users`
--
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `totp_secret` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `totp_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `totp_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `username` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('guest','member','author','editor','moderator','advertiser','admin','super_admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'member',
  `admin_role_id` int unsigned DEFAULT NULL,
  `status` enum('pending_verification','unverified','active','suspended','banned','deleted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending_verification',
  `suspended_until` datetime DEFAULT NULL,
  `verification_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` datetime DEFAULT NULL,
  `email_verify_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_reset_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_reset_expires_at` datetime DEFAULT NULL,
  `two_factor_secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `strike_count` tinyint unsigned NOT NULL DEFAULT '0',
  `is_flagged_suspicious` tinyint(1) NOT NULL DEFAULT '0',
  `disable_ads` tinyint(1) NOT NULL DEFAULT '0',
  `suspicious_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `flagged_at` datetime DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `last_login_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`),
  KEY `idx_users_role_status` (`role`,`status`),
  KEY `idx_users_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `webhook_deliveries`
--
DROP TABLE IF EXISTS `webhook_deliveries`;
CREATE TABLE `webhook_deliveries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `webhook_id` int unsigned NOT NULL,
  `event` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload_json` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `response_code` int DEFAULT NULL,
  `response_body` text COLLATE utf8mb4_unicode_ci,
  `status` enum('success','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'success',
  `duration_ms` int NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_wh_id` (`webhook_id`),
  KEY `idx_wh_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `webhooks`
--
DROP TABLE IF EXISTS `webhooks`;
CREATE TABLE `webhooks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret_key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `events_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `webhooks_chk_1` CHECK (json_valid(`events_json`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for: `visitor_logs`
--
DROP TABLE IF EXISTS `visitor_logs`;
CREATE TABLE `visitor_logs` (
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

SET FOREIGN_KEY_CHECKS = 1;
