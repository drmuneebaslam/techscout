-- ============================================================================
-- TechScout Universal Database Schema
-- Updated: 2026-09-05 06:23:24
-- Total Tables: 76
-- Supports Complete Architecture, Telemetry, Payout Taxes & Creator Governance
-- Compatible with MySQL 5.7+, MySQL 8.0+, MariaDB 10.3+
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `techscout_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `techscout_db`;

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
SET NAMES utf8mb4;

DROP TABLE IF EXISTS `admin_role_permissions`;
CREATE TABLE `admin_role_permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `permission_key` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_role_perm` (`role_id`,`permission_key`),
  KEY `idx_arp_role` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `admin_roles`;
CREATE TABLE `admin_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_key` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_key` (`role_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `advertisements`;
CREATE TABLE `advertisements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `advertiser_user_id` bigint(20) unsigned NOT NULL,
  `slot_location` enum('header_banner','article_inline','sidebar_sticky','category_featured','newsletter_top') NOT NULL,
  `title` varchar(150) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `target_url` varchar(255) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `impression_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `click_count` int(10) unsigned NOT NULL DEFAULT 0,
  `cost_usd` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','active','paused','expired') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_ads_user` (`advertiser_user_id`),
  CONSTRAINT `fk_ads_user` FOREIGN KEY (`advertiser_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `ai_tools`;
CREATE TABLE `ai_tools` (
  `content_id` bigint(20) unsigned NOT NULL,
  `website_url` varchar(255) NOT NULL,
  `docs_url` varchar(255) DEFAULT NULL,
  `discord_url` varchar(255) DEFAULT NULL,
  `twitter_handle` varchar(100) DEFAULT NULL,
  `pricing_model` enum('free','freemium','paid','subscription','free_trial','open_source','contact_pricing') NOT NULL DEFAULT 'freemium',
  `secondary_categories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`secondary_categories`)),
  `use_cases` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`use_cases`)),
  `free_trial_available` tinyint(1) NOT NULL DEFAULT 1,
  `free_trial_days` int(11) DEFAULT 14,
  `pricing_starting_at` varchar(50) DEFAULT NULL,
  `pricing_billing_cycle` varchar(50) DEFAULT 'monthly',
  `discount_code` varchar(100) DEFAULT NULL,
  `discount_deal` varchar(255) DEFAULT NULL,
  `features_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features_list`)),
  `platforms_supported` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`platforms_supported`)),
  `api_available` tinyint(1) NOT NULL DEFAULT 0,
  `open_source_repo` varchar(255) DEFAULT NULL,
  `license_type` varchar(100) DEFAULT 'Proprietary',
  `alternatives_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`alternatives_list`)),
  `comparison_highlights` text DEFAULT NULL,
  `editorial_review` text DEFAULT NULL,
  `pros_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`pros_list`)),
  `cons_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`cons_list`)),
  `verified_by_techscout` tinyint(1) NOT NULL DEFAULT 0,
  `rating_avg` decimal(3,2) NOT NULL DEFAULT 0.00,
  `rating_count` int(10) unsigned NOT NULL DEFAULT 0,
  `rating_ease_of_use` decimal(3,2) NOT NULL DEFAULT 4.80,
  `rating_value_for_money` decimal(3,2) NOT NULL DEFAULT 4.70,
  `rating_features` decimal(3,2) NOT NULL DEFAULT 4.90,
  `rating_performance` decimal(3,2) NOT NULL DEFAULT 4.85,
  `rating_support` decimal(3,2) NOT NULL DEFAULT 4.60,
  `screenshots_gallery` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`screenshots_gallery`)),
  `affiliate_url` varchar(255) DEFAULT NULL,
  `custom_affiliate_slug` varchar(100) DEFAULT NULL,
  `affiliate_commission_rate` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`content_id`),
  CONSTRAINT `fk_ai_tools_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `analytics_events`;
CREATE TABLE `analytics_events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_time` datetime NOT NULL DEFAULT current_timestamp(),
  `event_type` varchar(50) NOT NULL,
  `content_id` bigint(20) unsigned DEFAULT NULL,
  `author_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `referrer_domain` varchar(150) DEFAULT NULL,
  `device_type` enum('desktop','mobile','tablet','bot') NOT NULL DEFAULT 'desktop',
  `duration_seconds` int(10) unsigned DEFAULT 0,
  `metadata_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata_json`)),
  PRIMARY KEY (`id`),
  KEY `idx_analytics_type_time` (`event_type`,`event_time`),
  KEY `idx_analytics_lookup` (`event_time`,`event_type`,`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `api_keys`;
CREATE TABLE `api_keys` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `key_name` varchar(100) NOT NULL,
  `api_key_prefix` varchar(16) NOT NULL,
  `api_key_hash` varchar(128) NOT NULL,
  `scopes_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`scopes_json`)),
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

DROP TABLE IF EXISTS `article_co_authors`;
CREATE TABLE `article_co_authors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int(10) unsigned NOT NULL,
  `author_id` int(10) unsigned NOT NULL,
  `role` enum('lead_author','co_author','technical_reviewer') NOT NULL DEFAULT 'co_author',
  `revenue_share_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `invitation_status` enum('pending','accepted','declined') NOT NULL DEFAULT 'accepted',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_article_author` (`article_id`,`author_id`),
  KEY `idx_aca_article` (`article_id`),
  KEY `idx_aca_author` (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `articles`;
CREATE TABLE `articles` (
  `content_id` bigint(20) unsigned NOT NULL,
  `reading_time_mins` smallint(5) unsigned NOT NULL DEFAULT 5,
  `difficulty_level` enum('beginner','intermediate','advanced','expert') NOT NULL DEFAULT 'intermediate',
  `table_of_contents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`table_of_contents`)),
  `co_authors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`co_authors`)),
  `editor_attribution` varchar(100) DEFAULT NULL,
  `citation_sources` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`citation_sources`)),
  `code_snippets_count` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`content_id`),
  CONSTRAINT `fk_articles_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `actor_role` varchar(50) DEFAULT NULL,
  `action` varchar(80) NOT NULL,
  `action_domain` varchar(50) DEFAULT 'general',
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `country` varchar(100) DEFAULT 'Pakistan',
  `device_info` varchar(255) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `before_state` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`before_state`)),
  `after_state` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`after_state`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_audit_created` (`created_at`),
  KEY `idx_audit_filter` (`created_at`,`action`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `author_applications`;
CREATE TABLE `author_applications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `headline` varchar(150) NOT NULL,
  `bio` text NOT NULL,
  `portfolio_url` varchar(255) DEFAULT NULL,
  `github_handle` varchar(100) DEFAULT NULL,
  `twitter_handle` varchar(100) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `specializations` varchar(255) DEFAULT NULL,
  `topic_interests` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`topic_interests`)),
  `sample_pitch` text NOT NULL,
  `sample_file_url` varchar(255) DEFAULT NULL,
  `requested_prompts` tinyint(1) NOT NULL DEFAULT 0,
  `requested_gpts` tinyint(1) NOT NULL DEFAULT 0,
  `requested_tools` tinyint(1) NOT NULL DEFAULT 0,
  `requested_credits` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_feedback` text DEFAULT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_author_apps_status` (`status`),
  KEY `idx_author_apps_user` (`user_id`),
  CONSTRAINT `fk_author_apps_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `author_followers`;
CREATE TABLE `author_followers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_author_user` (`author_id`,`user_id`),
  KEY `idx_author` (`author_id`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `author_payout_methods`;
CREATE TABLE `author_payout_methods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` bigint(20) unsigned NOT NULL,
  `method_type` enum('bank_wire','iban_sepa','paypal','stripe_connect','crypto_usdc') NOT NULL,
  `account_holder_name` varchar(150) NOT NULL,
  `bank_name` varchar(150) DEFAULT NULL,
  `account_number_or_iban` varchar(100) DEFAULT NULL,
  `routing_or_swift` varchar(50) DEFAULT NULL,
  `account_email` varchar(191) DEFAULT NULL,
  `crypto_wallet_address` varchar(150) DEFAULT NULL,
  `crypto_network` varchar(50) DEFAULT 'ERC-20 (Ethereum)',
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('verified','pending_verification','active','disabled') NOT NULL DEFAULT 'verified',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_apm_author` (`author_id`),
  KEY `idx_apm_default` (`author_id`,`is_default`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `author_payouts`;
CREATE TABLE `author_payouts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ledger_reference_no` varchar(64) DEFAULT NULL,
  `author_id` bigint(20) unsigned NOT NULL,
  `payout_method_id` int(10) unsigned DEFAULT NULL,
  `amount_usd` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax_deducted` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bank_tax_deducted` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fee_deducted` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `credits_deducted` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payout_method` varchar(50) DEFAULT 'paypal',
  `payout_address` varchar(255) NOT NULL,
  `transaction_reference` varchar(150) DEFAULT NULL,
  `receipt_url` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','paid','rejected') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `processed_by` bigint(20) unsigned DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_payouts_author_status` (`author_id`,`status`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `author_strikes`;
CREATE TABLE `author_strikes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `strike_level` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `penalty_type` enum('warning','suspension','ban') NOT NULL DEFAULT 'warning',
  `reason` varchar(500) NOT NULL,
  `notes` text DEFAULT NULL,
  `issued_by_user_id` bigint(20) unsigned DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_strikes` (`user_id`,`is_active`),
  KEY `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `author_tier_history`;
CREATE TABLE `author_tier_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(10) unsigned NOT NULL,
  `tier_code` varchar(50) NOT NULL,
  `tier_level` int(11) NOT NULL DEFAULT 1,
  `tier_title` varchar(100) NOT NULL,
  `cpm_multiplier` decimal(4,2) NOT NULL DEFAULT 1.00,
  `unlocked_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_th_author` (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `author_tips`;
CREATE TABLE `author_tips` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(64) NOT NULL,
  `author_id` int(10) unsigned NOT NULL,
  `article_id` int(10) unsigned DEFAULT NULL,
  `tipper_user_id` bigint(20) unsigned DEFAULT NULL,
  `tipper_name` varchar(100) NOT NULL DEFAULT 'Generous Supporter',
  `tipper_email` varchar(150) DEFAULT NULL,
  `amount_usd` decimal(10,2) NOT NULL,
  `platform_fee_usd` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_amount_usd` decimal(10,2) NOT NULL,
  `message` text DEFAULT NULL,
  `is_anonymous` tinyint(1) NOT NULL DEFAULT 0,
  `payment_method` varchar(50) NOT NULL DEFAULT 'card_stripe',
  `status` enum('completed','pending','refunded') NOT NULL DEFAULT 'completed',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference_no` (`reference_no`),
  KEY `idx_tip_author` (`author_id`),
  KEY `idx_tip_article` (`article_id`),
  KEY `idx_tip_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `authors`;
CREATE TABLE `authors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `slug` varchar(80) NOT NULL,
  `headline` varchar(180) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `cover_image_url` varchar(255) DEFAULT NULL,
  `expertise_tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`expertise_tags`)),
  `social_links` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`social_links`)),
  `verification_badge` enum('none','verified_builder','expert','staff','top_creator') NOT NULL DEFAULT 'none',
  `custom_badge_label` varchar(100) DEFAULT NULL,
  `custom_badge_color` varchar(30) DEFAULT NULL,
  `reputation_score` int(10) unsigned NOT NULL DEFAULT 0,
  `reputation_level` varchar(40) NOT NULL DEFAULT 'Scout Initiate',
  `tier_override` varchar(50) DEFAULT NULL,
  `tier_title` varchar(100) DEFAULT NULL,
  `current_tier_code` varchar(50) NOT NULL DEFAULT 'bronze',
  `tier_sublevel` int(11) NOT NULL DEFAULT 1,
  `tier_multiplier` decimal(4,2) NOT NULL DEFAULT 1.00,
  `referral_code` varchar(50) DEFAULT NULL,
  `promo_credits` decimal(10,2) NOT NULL DEFAULT 0.00,
  `can_publish_prompts` tinyint(1) NOT NULL DEFAULT 0,
  `can_publish_gpts` tinyint(1) NOT NULL DEFAULT 0,
  `can_publish_tools` tinyint(1) NOT NULL DEFAULT 0,
  `can_use_templates` tinyint(1) NOT NULL DEFAULT 0,
  `monetization_status` varchar(50) NOT NULL DEFAULT 'ineligible',
  `follower_count` int(10) unsigned NOT NULL DEFAULT 0,
  `article_count` int(10) unsigned NOT NULL DEFAULT 0,
  `total_views` bigint(20) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_monetized` tinyint(1) DEFAULT 0,
  `monetization_applied_at` datetime DEFAULT NULL,
  `monetization_approved_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_authors_reputation` (`reputation_score`),
  CONSTRAINT `fk_authors_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `bookmarks`;
CREATE TABLE `bookmarks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `content_id` bigint(20) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_content_bookmark` (`user_id`,`content_id`),
  KEY `fk_bookmarks_content` (`content_id`),
  CONSTRAINT `fk_bookmarks_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_bookmarks_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `campaigns`;
CREATE TABLE `campaigns` (
  `content_id` bigint(20) unsigned NOT NULL,
  `campaign_type` enum('launch','hackathon','contest','sponsorship','referral') NOT NULL DEFAULT 'hackathon',
  `banner_image_url` varchar(255) DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `prize_pool` varchar(100) DEFAULT NULL,
  `rules_markdown` text DEFAULT NULL,
  `submission_fields` text DEFAULT NULL,
  `enable_referral_system` tinyint(1) DEFAULT 1,
  `enable_public_voting` tinyint(1) DEFAULT 1,
  `external_partner_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`content_id`),
  CONSTRAINT `fk_campaigns_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `topic_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon_name` varchar(50) DEFAULT 'folder',
  `color_hex` varchar(10) DEFAULT '#6366f1',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `content_count` int(10) unsigned NOT NULL DEFAULT 0,
  `applicable_to` varchar(50) NOT NULL DEFAULT 'all',
  `is_featured_home` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_categories_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `challenge_submissions`;
CREATE TABLE `challenge_submissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `project_title` varchar(255) NOT NULL,
  `project_slug` varchar(255) DEFAULT NULL,
  `summary` text NOT NULL,
  `detailed_description` longtext DEFAULT NULL,
  `tech_stack` varchar(255) DEFAULT NULL,
  `github_repo_url` varchar(255) DEFAULT NULL,
  `live_demo_url` varchar(255) DEFAULT NULL,
  `video_demo_url` varchar(255) DEFAULT NULL,
  `linked_article_id` bigint(20) unsigned DEFAULT NULL,
  `referral_code` varchar(64) DEFAULT NULL,
  `referral_clicks` int(10) unsigned DEFAULT 0,
  `referral_signups` int(10) unsigned DEFAULT 0,
  `page_views` int(10) unsigned DEFAULT 0,
  `votes_count` int(10) unsigned DEFAULT 0,
  `status` enum('draft','submitted','under_review','shortlisted','winner','runner_up','rejected') DEFAULT 'submitted',
  `score` decimal(5,2) DEFAULT NULL,
  `judge_feedback` text DEFAULT NULL,
  `prize_awarded` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_campaign_id` (`campaign_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_referral_code` (`referral_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `challenge_votes`;
CREATE TABLE `challenge_votes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `submission_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user_sub` (`submission_id`,`user_id`),
  KEY `idx_submission_id` (`submission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `comment_text` text NOT NULL,
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('visible','flagged','hidden','deleted') NOT NULL DEFAULT 'visible',
  `reaction_count` int(10) unsigned NOT NULL DEFAULT 0,
  `reply_count` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_comments_content` (`content_id`),
  KEY `fk_comments_user` (`user_id`),
  KEY `idx_comments_content_time` (`content_id`,`created_at`),
  CONSTRAINT `fk_comments_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(191) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read','replied') NOT NULL DEFAULT 'unread',
  `reply_text` text DEFAULT NULL,
  `replied_at` datetime DEFAULT NULL,
  `replied_by` int(10) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_contact_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `content_filters`;
CREATE TABLE `content_filters` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pattern` varchar(191) NOT NULL,
  `match_type` enum('exact','contains','regex') NOT NULL DEFAULT 'contains',
  `replacement` varchar(50) NOT NULL DEFAULT '***',
  `action` enum('replace','flag_review','auto_quarantine') NOT NULL DEFAULT 'replace',
  `severity` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `content_revisions`;
CREATE TABLE `content_revisions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` bigint(20) unsigned NOT NULL,
  `editor_id` bigint(20) unsigned NOT NULL,
  `version_number` int(10) unsigned NOT NULL DEFAULT 1,
  `title` varchar(255) NOT NULL,
  `summary` varchar(500) NOT NULL,
  `body_html` longtext DEFAULT NULL,
  `change_summary` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_revisions_content` (`content_id`),
  CONSTRAINT `fk_revisions_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `content_tags`;
CREATE TABLE `content_tags` (
  `content_id` bigint(20) unsigned NOT NULL,
  `tag_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`content_id`,`tag_id`),
  KEY `fk_ct_tag` (`tag_id`),
  CONSTRAINT `fk_ct_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ct_tag` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `contents`;
CREATE TABLE `contents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `content_type` enum('article','ai_tool','gpt_listing','prompt','software','resource','collection','campaign') NOT NULL,
  `author_id` bigint(20) unsigned NOT NULL,
  `category_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `summary` varchar(500) NOT NULL,
  `body_html` longtext DEFAULT NULL,
  `featured_image_url` varchar(255) DEFAULT NULL,
  `status` enum('draft','in_review','scheduled','published','rejected','archived') NOT NULL DEFAULT 'draft',
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0,
  `custom_badge` varchar(50) DEFAULT NULL,
  `badge_color` varchar(20) DEFAULT NULL,
  `badge_bg` varchar(40) DEFAULT NULL,
  `visibility` enum('public','members_only','unlisted','private') NOT NULL DEFAULT 'public',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_trending` tinyint(1) NOT NULL DEFAULT 0,
  `is_sponsored` tinyint(1) NOT NULL DEFAULT 0,
  `sponsored_by` varchar(100) DEFAULT NULL,
  `quality_score` decimal(4,2) NOT NULL DEFAULT 0.00,
  `editorial_notes` text DEFAULT NULL,
  `internal_notes` longtext DEFAULT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `seo_title` varchar(191) DEFAULT NULL,
  `seo_description` varchar(300) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL,
  `view_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `unique_view_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `reaction_count` int(10) unsigned NOT NULL DEFAULT 0,
  `comment_count` int(10) unsigned NOT NULL DEFAULT 0,
  `bookmark_count` int(10) unsigned NOT NULL DEFAULT 0,
  `share_count` int(10) unsigned NOT NULL DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `scheduled_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `creator_referrals`;
CREATE TABLE `creator_referrals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `referrer_author_id` int(10) unsigned NOT NULL,
  `referred_user_id` bigint(20) unsigned NOT NULL,
  `referred_author_id` int(10) unsigned DEFAULT NULL,
  `referral_code` varchar(50) NOT NULL,
  `commission_rate` decimal(5,2) NOT NULL DEFAULT 5.00,
  `expires_at` datetime NOT NULL,
  `total_commission_earned` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_cr_referrer` (`referrer_author_id`),
  KEY `idx_cr_referred` (`referred_author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `database_backups`;
CREATE TABLE `database_backups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `filepath` varchar(500) NOT NULL,
  `file_size_bytes` bigint(20) unsigned NOT NULL,
  `table_count` int(10) unsigned NOT NULL,
  `total_rows` bigint(20) unsigned DEFAULT 0,
  `compression` enum('sql','gzip') DEFAULT 'sql',
  `checksum` varchar(64) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `editorial_badges`;
CREATE TABLE `editorial_badges` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `color` varchar(20) NOT NULL DEFAULT '#38bdf8',
  `bg_color` varchar(40) NOT NULL DEFAULT 'rgba(56,189,248,0.15)',
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `entity_reports`;
CREATE TABLE `entity_reports` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reporter_user_id` bigint(20) unsigned DEFAULT NULL,
  `entity_type` enum('article','ai_tool','gpt_listing','prompt','job','comment','campaign','author') NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `entity_title` varchar(255) DEFAULT NULL,
  `entity_url` varchar(255) DEFAULT NULL,
  `reason` enum('spam','broken_or_misleading','plagiarism_copyright','inappropriate_harmful','malicious_prompt_injection','other') NOT NULL,
  `details` text DEFAULT NULL,
  `status` enum('pending','reviewed','actioned','dismissed') NOT NULL DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `action_taken` varchar(100) DEFAULT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_er_status` (`status`),
  KEY `idx_er_entity` (`entity_type`,`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `failed_login_attempts`;
CREATE TABLE `failed_login_attempts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `identifier` varchar(191) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `attempted_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ip_attempt` (`ip_address`,`attempted_at`),
  KEY `idx_identifier` (`identifier`,`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `feature_flags`;
CREATE TABLE `feature_flags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `flag_key` varchar(64) NOT NULL,
  `name` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'general',
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `flag_key` (`flag_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `financial_ledger`;
CREATE TABLE `financial_ledger` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(64) NOT NULL,
  `author_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `entry_type` enum('credit','debit') NOT NULL,
  `category` varchar(64) NOT NULL,
  `amount_usd` decimal(12,2) NOT NULL,
  `credits` decimal(12,2) NOT NULL,
  `balance_before` decimal(12,2) NOT NULL DEFAULT 0.00,
  `balance_after` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payout_method_id` int(10) unsigned DEFAULT NULL,
  `payout_method_details` varchar(255) DEFAULT NULL,
  `status` enum('completed','pending','processing','reversed','failed') NOT NULL DEFAULT 'completed',
  `performed_by_user_id` bigint(20) unsigned DEFAULT NULL,
  `performed_by_role` varchar(50) NOT NULL DEFAULT 'system',
  `reason` text NOT NULL,
  `internal_notes` text DEFAULT NULL,
  `metadata_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata_json`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference_no` (`reference_no`),
  KEY `idx_fl_author` (`author_id`),
  KEY `idx_fl_type` (`entry_type`),
  KEY `idx_fl_category` (`category`),
  KEY `idx_fl_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `firewall_logs`;
CREATE TABLE `firewall_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `path` varchar(255) NOT NULL,
  `threat_type` varchar(80) NOT NULL,
  `action_taken` varchar(40) NOT NULL DEFAULT 'blocked',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_fl_ip` (`ip_address`),
  KEY `idx_fl_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `firewall_rules`;
CREATE TABLE `firewall_rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rule_type` enum('ip','cidr','user_agent','country') NOT NULL DEFAULT 'ip',
  `target_value` varchar(191) NOT NULL,
  `action` enum('block','allow','challenge') NOT NULL DEFAULT 'block',
  `reason` varchar(255) DEFAULT NULL,
  `hits_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_fw_target` (`target_value`),
  KEY `idx_fw_type` (`rule_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `follows`;
CREATE TABLE `follows` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `follower_user_id` bigint(20) unsigned NOT NULL,
  `following_author_id` bigint(20) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_follower_following` (`follower_user_id`,`following_author_id`),
  KEY `fk_follows_author` (`following_author_id`),
  CONSTRAINT `fk_follows_author` FOREIGN KEY (`following_author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_follows_follower` FOREIGN KEY (`follower_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `gpt_listings`;
CREATE TABLE `gpt_listings` (
  `content_id` bigint(20) unsigned NOT NULL,
  `openai_url` varchar(255) NOT NULL,
  `model_base` varchar(50) NOT NULL DEFAULT 'GPT-4o',
  `prompt_starters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`prompt_starters`)),
  `creator_handle` varchar(100) DEFAULT NULL,
  `use_case` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`content_id`),
  CONSTRAINT `fk_gpts_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `homepage_sections`;
CREATE TABLE `homepage_sections` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `section_key` varchar(60) NOT NULL,
  `title` varchar(150) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `item_limit` int(11) NOT NULL DEFAULT 6,
  `config_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`config_json`)),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `section_key` (`section_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `ip_lockouts`;
CREATE TABLE `ip_lockouts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `failed_count` int(10) unsigned DEFAULT 1,
  `locked_until` datetime NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip_address` (`ip_address`),
  KEY `idx_locked_until` (`locked_until`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `job_applications`;
CREATE TABLE `job_applications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `job_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `applicant_name` varchar(150) NOT NULL,
  `applicant_email` varchar(191) NOT NULL,
  `portfolio_url` varchar(255) DEFAULT NULL,
  `resume_text_or_url` text NOT NULL,
  `cover_letter` text DEFAULT NULL,
  `status` enum('pending','reviewed','interviewing','accepted','rejected') NOT NULL DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_job_app_job` (`job_id`),
  KEY `idx_job_app_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `company_name` varchar(150) NOT NULL,
  `company_logo_url` varchar(255) DEFAULT NULL,
  `location` varchar(100) NOT NULL DEFAULT 'Remote',
  `job_type` enum('Full-time','Contract','Remote','Part-time') NOT NULL DEFAULT 'Remote',
  `salary_range` varchar(100) DEFAULT NULL,
  `apply_url` varchar(255) NOT NULL,
  `description_html` longtext NOT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_jobs_active` (`is_active`),
  KEY `idx_jobs_featured` (`is_featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `mail_credentials`;
CREATE TABLE `mail_credentials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `provider` enum('smtp','resend','sendgrid','ses','mailgun','pythonanywhere_api') NOT NULL DEFAULT 'smtp',
  `from_name` varchar(150) NOT NULL,
  `from_email` varchar(191) NOT NULL,
  `host` varchar(191) DEFAULT NULL,
  `port` int(11) DEFAULT 587,
  `encryption` enum('tls','ssl','none') DEFAULT 'tls',
  `username` varchar(191) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `api_key` varchar(255) DEFAULT NULL,
  `api_endpoint` varchar(255) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `status` enum('active','inactive','testing') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `mail_logs`;
CREATE TABLE `mail_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `to_email` varchar(191) NOT NULL,
  `to_name` varchar(150) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `template_key` varchar(80) DEFAULT NULL,
  `body_html` longtext DEFAULT NULL,
  `attachments_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments_json`)),
  `driver` varchar(30) NOT NULL DEFAULT 'log',
  `status` enum('sent','failed','queued') NOT NULL DEFAULT 'sent',
  `error_message` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ml_email` (`to_email`),
  KEY `idx_ml_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `mail_templates`;
CREATE TABLE `mail_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `template_key` varchar(80) NOT NULL,
  `name` varchar(150) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `html_content` longtext NOT NULL,
  `assigned_credential_id` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `template_key` (`template_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `media_files`;
CREATE TABLE `media_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT 1,
  `filename` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` enum('image','video','audio','document') NOT NULL DEFAULT 'image',
  `file_size` bigint(20) unsigned NOT NULL DEFAULT 0,
  `mime_type` varchar(100) NOT NULL DEFAULT 'image/jpeg',
  `folder` varchar(100) NOT NULL DEFAULT 'general',
  `alt_text` varchar(255) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `copyright_info` varchar(255) DEFAULT 'TechScout Editorial / Unsplash License',
  `attribution` varchar(255) DEFAULT NULL,
  `metadata_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata_json`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_media_type` (`file_type`),
  KEY `idx_media_folder` (`folder`),
  KEY `idx_media_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `navigation_menus`;
CREATE TABLE `navigation_menus` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `location` enum('main','secondary','footer','mobile','dropdown','mega_menu','external') NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `label` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `target` enum('_self','_blank') DEFAULT '_self',
  `icon_name` varchar(50) DEFAULT '',
  `badge_text` varchar(50) DEFAULT '',
  `badge_color` varchar(50) DEFAULT '',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `mega_menu_data` longtext DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `newsletter_issues`;
CREATE TABLE `newsletter_issues` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `issue_number` int(10) unsigned DEFAULT 1,
  `subject_line` varchar(255) NOT NULL,
  `preview_text` varchar(255) DEFAULT '',
  `template_type` varchar(100) DEFAULT 'weekly_digest',
  `body_html` longtext DEFAULT NULL,
  `status` enum('draft','scheduled','sent','archived') DEFAULT 'draft',
  `scheduled_at` datetime DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL,
  `total_recipients` int(10) unsigned DEFAULT 0,
  `open_count` int(10) unsigned DEFAULT 0,
  `click_count` int(10) unsigned DEFAULT 0,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `newsletter_subscriptions`;
CREATE TABLE `newsletter_subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(191) NOT NULL,
  `status` enum('pending','subscribed','unsubscribed') NOT NULL DEFAULT 'subscribed',
  `subscriber_type` enum('all','weekly_digest','ai_tools_only','developer_only') NOT NULL DEFAULT 'all',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'system',
  `event_type` varchar(60) NOT NULL,
  `title` varchar(150) NOT NULL,
  `message` varchar(300) NOT NULL,
  `action_url` varchar(255) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_notif_user` (`user_id`),
  CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `plagiarism_scans`;
CREATE TABLE `plagiarism_scans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` bigint(20) unsigned NOT NULL,
  `originality_score` decimal(5,2) NOT NULL DEFAULT 100.00,
  `duplicate_ratio` decimal(5,2) NOT NULL DEFAULT 0.00,
  `verdict` enum('clean','acceptable','suspicious','flagged_plagiarism') DEFAULT 'clean',
  `matched_content_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`matched_content_ids`)),
  `overlapping_shingles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`overlapping_shingles`)),
  `total_shingles` int(10) unsigned DEFAULT 0,
  `matching_shingles` int(10) unsigned DEFAULT 0,
  `scanned_by` bigint(20) unsigned DEFAULT NULL,
  `scanned_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_content_id` (`content_id`),
  KEY `idx_verdict` (`verdict`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `prompts`;
CREATE TABLE `prompts` (
  `content_id` bigint(20) unsigned NOT NULL,
  `prompt_template` longtext NOT NULL,
  `template_variables` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`template_variables`)),
  `target_models` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`target_models`)),
  `copy_count` int(10) unsigned NOT NULL DEFAULT 0,
  `sample_output` text DEFAULT NULL,
  PRIMARY KEY (`content_id`),
  CONSTRAINT `fk_prompts_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `reactions`;
CREATE TABLE `reactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `content_id` bigint(20) unsigned NOT NULL,
  `reaction_type` enum('like','clap','rocket','insightful','mind_blown','bookmark') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_content_reaction` (`user_id`,`content_id`,`reaction_type`),
  KEY `fk_reactions_content` (`content_id`),
  CONSTRAINT `fk_reactions_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reactions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `reading_history`;
CREATE TABLE `reading_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `content_id` bigint(20) unsigned NOT NULL,
  `session_id` varchar(64) DEFAULT NULL,
  `session_number` int(11) NOT NULL DEFAULT 1,
  `started_at` datetime NOT NULL DEFAULT current_timestamp(),
  `progress_percent` int(11) NOT NULL DEFAULT 0,
  `read_seconds` int(11) NOT NULL DEFAULT 0,
  `last_read_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `completed_at` datetime DEFAULT NULL,
  `status` enum('reading','completed','re_reading') NOT NULL DEFAULT 'reading',
  `referrer_type` varchar(32) NOT NULL DEFAULT 'internal',
  `referrer_source` varchar(255) DEFAULT '/directory',
  PRIMARY KEY (`id`),
  KEY `idx_rh_user` (`user_id`),
  KEY `idx_rh_last_read` (`last_read_at`),
  KEY `idx_user_content` (`user_id`,`content_id`),
  KEY `idx_reading_history_lookup` (`content_id`,`last_read_at`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `redirects`;
CREATE TABLE `redirects` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `source_path` varchar(255) NOT NULL,
  `target_path` varchar(255) NOT NULL,
  `status_code` smallint(5) unsigned NOT NULL DEFAULT 301,
  `hit_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `source_path` (`source_path`),
  KEY `idx_redirect_source` (`source_path`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `referral_commissions`;
CREATE TABLE `referral_commissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `referral_id` bigint(20) unsigned NOT NULL,
  `referrer_author_id` int(10) unsigned NOT NULL,
  `ledger_reference_no` varchar(64) NOT NULL,
  `amount_usd` decimal(10,2) NOT NULL,
  `source_event` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_rc_referral` (`referral_id`),
  KEY `idx_rc_referrer` (`referrer_author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `reports`;
CREATE TABLE `reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reporter_user_id` bigint(20) unsigned DEFAULT NULL,
  `content_id` bigint(20) unsigned DEFAULT NULL,
  `comment_id` bigint(20) unsigned DEFAULT NULL,
  `reason` enum('spam','plagiarism','misinformation','abusive','broken_link','copyright','other') NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','investigating','resolved','dismissed') NOT NULL DEFAULT 'pending',
  `resolved_by_user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_reports_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `revenue_records`;
CREATE TABLE `revenue_records` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` bigint(20) unsigned NOT NULL,
  `description` varchar(255) NOT NULL,
  `credits_granted` decimal(10,2) NOT NULL DEFAULT 0.00,
  `amount_usd` decimal(10,2) NOT NULL DEFAULT 0.00,
  `transaction_type` enum('credit_reward','view_milestone','upvote_bonus','editorial_grant','ad_redemption') NOT NULL DEFAULT 'credit_reward',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_rev_author` (`author_id`),
  CONSTRAINT `fk_rev_author` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `security_anomalies`;
CREATE TABLE `security_anomalies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `event_type` enum('brute_force_lockout','new_ip_login','new_device_login','rapid_requests','sensitive_endpoint_breach','session_hijack_suspected') NOT NULL,
  `severity` enum('low','medium','high','critical') DEFAULT 'medium',
  `description` varchar(500) NOT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `is_resolved` tinyint(1) DEFAULT 0,
  `resolved_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ip_created` (`ip_address`,`created_at`),
  KEY `idx_user_resolved` (`user_id`,`is_resolved`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `seo_metadata`;
CREATE TABLE `seo_metadata` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `path_pattern` varchar(191) NOT NULL,
  `meta_title` varchar(200) NOT NULL,
  `meta_description` text NOT NULL,
  `og_image_url` varchar(255) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL,
  `robots_directive` varchar(60) NOT NULL DEFAULT 'index, follow',
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `path_pattern` (`path_pattern`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `site_announcements`;
CREATE TABLE `site_announcements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `ribbon_type` enum('info','announcement','warning','promo','maintenance') NOT NULL DEFAULT 'announcement',
  `banner_color` varchar(20) NOT NULL DEFAULT '#6366f1',
  `text_color` varchar(20) NOT NULL DEFAULT '#ffffff',
  `action_label` varchar(60) DEFAULT NULL,
  `action_url` varchar(255) DEFAULT NULL,
  `target_audience` enum('all','guests','members','authors') NOT NULL DEFAULT 'all',
  `is_dismissible` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `site_settings`;
CREATE TABLE `site_settings` (
  `key_name` varchar(100) NOT NULL,
  `key_value` text DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`key_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `support_tickets`;
CREATE TABLE `support_tickets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_number` varchar(30) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'general',
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `sla_due_at` datetime DEFAULT NULL,
  `is_escalated` tinyint(1) DEFAULT 0,
  `escalated_at` datetime DEFAULT NULL,
  `first_response_at` datetime DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `status` enum('open','in_progress','waiting_user','resolved','closed') NOT NULL DEFAULT 'open',
  `assigned_to` bigint(20) unsigned DEFAULT NULL,
  `last_reply_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `ticket_number` (`ticket_number`),
  KEY `idx_st_user` (`user_id`),
  KEY `idx_st_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `system_error_logs`;
CREATE TABLE `system_error_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `error_type` varchar(100) NOT NULL DEFAULT 'ERROR',
  `message` text NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `line` int(10) unsigned DEFAULT NULL,
  `stack_trace` longtext DEFAULT NULL,
  `request_uri` varchar(255) DEFAULT NULL,
  `request_method` varchar(10) DEFAULT NULL,
  `request_data` longtext DEFAULT NULL,
  `request_headers` longtext DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_details` longtext DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `location_details` longtext DEFAULT NULL,
  `device_details` longtext DEFAULT NULL,
  `resolved` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_err_type` (`error_type`),
  KEY `idx_err_resolved` (`resolved`),
  KEY `idx_err_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings` (
  `key_name` varchar(100) NOT NULL,
  `value_content` longtext NOT NULL,
  `setting_group` varchar(50) NOT NULL DEFAULT 'general',
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`key_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `slug` varchar(60) NOT NULL,
  `applicable_to` varchar(50) NOT NULL DEFAULT 'all',
  `usage_count` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_tags_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `ticket_canned_macros`;
CREATE TABLE `ticket_canned_macros` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(50) NOT NULL DEFAULT 'General',
  `title` varchar(150) NOT NULL,
  `shortcut_code` varchar(50) NOT NULL,
  `body_content` text NOT NULL,
  `usage_count` int(10) unsigned DEFAULT 0,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `shortcut_code` (`shortcut_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `ticket_internal_notes`;
CREATE TABLE `ticket_internal_notes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint(20) unsigned NOT NULL,
  `staff_user_id` bigint(20) unsigned NOT NULL,
  `note_content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ticket` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `ticket_messages`;
CREATE TABLE `ticket_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(10) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `is_staff` tinyint(1) NOT NULL DEFAULT 0,
  `is_internal_note` tinyint(1) NOT NULL DEFAULT 0,
  `message` text NOT NULL,
  `attachment_url` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_tm_ticket` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `topics`;
CREATE TABLE `topics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon_name` varchar(50) DEFAULT 'layers',
  `color_hex` varchar(10) DEFAULT '#6366f1',
  `banner_image_url` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `follower_count` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_topics_slug` (`slug`),
  KEY `idx_topics_featured` (`is_featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `user_sessions`;
CREATE TABLE `user_sessions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `session_token_hash` varchar(64) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `device_type` enum('desktop','mobile','tablet','bot','unknown') DEFAULT 'desktop',
  `browser_name` varchar(50) DEFAULT 'Unknown Browser',
  `os_name` varchar(50) DEFAULT 'Unknown OS',
  `location_hint` varchar(100) DEFAULT 'Local Network',
  `is_current` tinyint(1) DEFAULT 0,
  `last_active_at` datetime DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_token_hash` (`session_token_hash`),
  KEY `idx_user_active` (`user_id`,`last_active_at`),
  KEY `idx_token_hash` (`session_token_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `user_subscriptions`;
CREATE TABLE `user_subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `subscribable_type` enum('topic','category','author') NOT NULL,
  `subscribable_id` bigint(20) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_subscription` (`user_id`,`subscribable_type`,`subscribable_id`),
  KEY `idx_user_subs_type_id` (`subscribable_type`,`subscribable_id`),
  CONSTRAINT `fk_user_subs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `username` varchar(60) NOT NULL,
  `role` enum('guest','member','author','editor','moderator','advertiser','admin','super_admin') NOT NULL DEFAULT 'member',
  `admin_role_id` int(10) unsigned DEFAULT NULL,
  `status` enum('pending_verification','unverified','active','suspended','banned','deleted') NOT NULL DEFAULT 'pending_verification',
  `suspended_until` datetime DEFAULT NULL,
  `verification_code` varchar(20) DEFAULT NULL,
  `email_verified_at` datetime DEFAULT NULL,
  `email_verify_token` varchar(100) DEFAULT NULL,
  `password_reset_token` varchar(100) DEFAULT NULL,
  `password_reset_expires_at` datetime DEFAULT NULL,
  `two_factor_secret` varchar(255) DEFAULT NULL,
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `strike_count` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `is_flagged_suspicious` tinyint(1) NOT NULL DEFAULT 0,
  `suspicious_reason` varchar(255) DEFAULT NULL,
  `flagged_at` datetime DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`),
  KEY `idx_users_role_status` (`role`,`status`),
  KEY `idx_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `webhook_deliveries`;
CREATE TABLE `webhook_deliveries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `webhook_id` int(10) unsigned NOT NULL,
  `event` varchar(60) NOT NULL,
  `payload_json` longtext NOT NULL,
  `response_code` int(11) DEFAULT NULL,
  `response_body` text DEFAULT NULL,
  `status` enum('success','failed') NOT NULL DEFAULT 'success',
  `duration_ms` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_wh_id` (`webhook_id`),
  KEY `idx_wh_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `webhooks`;
CREATE TABLE `webhooks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `target_url` varchar(255) NOT NULL,
  `secret_key` varchar(64) NOT NULL,
  `events_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`events_json`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET SQL_MODE=@OLD_SQL_MODE;
