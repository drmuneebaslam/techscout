# TechScout — Production Database Schema & SQL DDL

> **Tasks Covered**: 610 through 640 (Phase 23 — Database Architecture)  
> **Target Database Engine**: MySQL 8.0+ / MariaDB 10.6+ (InnoDB Engine, `utf8mb4_unicode_ci` Collation)  
> **Key Design Feature**: Unified Polymorphic Content Engine with specialized sub-entity extensions, strict referential integrity, and composite index optimization.

---

## 1. Complete Database DDL SQL Script

```sql
-- ============================================================================
-- TECHSCOUT PRODUCTION DATABASE SCHEMA
-- Compatible with MySQL 8.0+ / MariaDB 10.6+
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;
DROP DATABASE IF EXISTS `techscout_db`;
CREATE DATABASE `techscout_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `techscout_db`;
SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------------------------
-- 1. USERS & AUTHENTICATION (Task 610)
-- ----------------------------------------------------------------------------
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NOT NULL UNIQUE,
  `email` VARCHAR(191) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `username` VARCHAR(60) NOT NULL UNIQUE,
  `role` ENUM('guest', 'member', 'author', 'editor', 'moderator', 'advertiser', 'admin', 'super_admin') NOT NULL DEFAULT 'member',
  `status` ENUM('unverified', 'active', 'suspended', 'banned', 'deleted') NOT NULL DEFAULT 'unverified',
  `email_verified_at` DATETIME NULL,
  `email_verify_token` VARCHAR(100) NULL,
  `password_reset_token` VARCHAR(100) NULL,
  `password_reset_expires_at` DATETIME NULL,
  `two_factor_secret` VARCHAR(255) NULL,
  `two_factor_enabled` TINYINT(1) NOT NULL DEFAULT 0,
  `last_login_at` DATETIME NULL,
  `last_login_ip` VARCHAR(45) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_users_role_status` (`role`, `status`),
  INDEX `idx_users_email` (`email`)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- 2. AUTHORS & CREATOR PROFILES (Tasks 611 & 612)
-- ----------------------------------------------------------------------------
CREATE TABLE `authors` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL UNIQUE,
  `display_name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(80) NOT NULL UNIQUE,
  `headline` VARCHAR(180) NULL,
  `bio` TEXT NULL,
  `avatar_url` VARCHAR(255) NULL,
  `cover_image_url` VARCHAR(255) NULL,
  `expertise_tags` JSON NULL,
  `social_links` JSON NULL, -- {twitter, github, linkedin, website}
  `verification_badge` ENUM('none', 'verified_builder', 'expert', 'staff', 'top_creator') NOT NULL DEFAULT 'none',
  `reputation_score` INT UNSIGNED NOT NULL DEFAULT 0,
  `reputation_level` VARCHAR(40) NOT NULL DEFAULT 'Scout Initiate',
  `promo_credits` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `monetization_status` ENUM('ineligible', 'eligible', 'active', 'suspended') NOT NULL DEFAULT 'ineligible',
  `payout_method` VARCHAR(50) NULL,
  `payout_account` VARCHAR(191) NULL,
  `follower_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `article_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `total_views` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_authors_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  INDEX `idx_authors_reputation` (`reputation_score` DESC),
  INDEX `idx_authors_verification` (`verification_badge`)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- 3. TAXONOMY: CATEGORIES & TAGS (Tasks 615 & 616)
-- ----------------------------------------------------------------------------
CREATE TABLE `categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` INT UNSIGNED NULL,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT NULL,
  `icon_name` VARCHAR(50) NULL DEFAULT 'folder',
  `color_hex` VARCHAR(10) NULL DEFAULT '#6366f1',
  `sort_order` INT NOT NULL DEFAULT 0,
  `content_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_categories_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  INDEX `idx_categories_slug` (`slug`),
  INDEX `idx_categories_sort` (`sort_order`)
) ENGINE=InnoDB;

CREATE TABLE `tags` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(60) NOT NULL UNIQUE,
  `slug` VARCHAR(60) NOT NULL UNIQUE,
  `usage_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_tags_usage` (`usage_count` DESC)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- 4. UNIFIED CONTENT ENGINE (Tasks 613, 618, 619, 620, 621, 622, 629)
-- ----------------------------------------------------------------------------
CREATE TABLE `contents` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NOT NULL UNIQUE,
  `content_type` ENUM('article', 'ai_tool', 'gpt_listing', 'prompt', 'software', 'resource', 'collection', 'campaign') NOT NULL,
  `author_id` BIGINT UNSIGNED NOT NULL,
  `category_id` INT UNSIGNED NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(200) NOT NULL UNIQUE,
  `summary` VARCHAR(500) NOT NULL,
  `body_html` LONGTEXT NULL,
  `featured_image_url` VARCHAR(255) NULL,
  `status` ENUM('draft', 'in_review', 'scheduled', 'published', 'rejected', 'archived') NOT NULL DEFAULT 'draft',
  `visibility` ENUM('public', 'members_only', 'unlisted', 'private') NOT NULL DEFAULT 'public',
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_trending` TINYINT(1) NOT NULL DEFAULT 0,
  `is_sponsored` TINYINT(1) NOT NULL DEFAULT 0,
  `sponsored_by` VARCHAR(100) NULL,
  `quality_score` DECIMAL(4,2) NOT NULL DEFAULT 0.00,
  `seo_title` VARCHAR(191) NULL,
  `seo_description` VARCHAR(300) NULL,
  `canonical_url` VARCHAR(255) NULL,
  `view_count` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `unique_view_count` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `reaction_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `comment_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `bookmark_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `share_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `published_at` DATETIME NULL,
  `scheduled_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_contents_author` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_contents_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  INDEX `idx_contents_type_status_published` (`content_type`, `status`, `published_at` DESC),
  INDEX `idx_contents_featured_trending` (`is_featured`, `is_trending`),
  FULLTEXT KEY `ft_contents_search` (`title`, `summary`, `body_html`)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- 5. POLYMORPHIC CONTENT EXTENSIONS
-- ----------------------------------------------------------------------------
-- Articles Extension (Task 613)
CREATE TABLE `articles` (
  `content_id` BIGINT UNSIGNED NOT NULL PRIMARY KEY,
  `reading_time_mins` SMALLINT UNSIGNED NOT NULL DEFAULT 5,
  `difficulty_level` ENUM('beginner', 'intermediate', 'advanced', 'expert') NOT NULL DEFAULT 'intermediate',
  `table_of_contents` JSON NULL,
  `co_authors` JSON NULL,
  `editor_attribution` VARCHAR(100) NULL,
  `citation_sources` JSON NULL,
  `code_snippets_count` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  CONSTRAINT `fk_articles_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- AI Tools & Software Directory Extension (Tasks 618 & 621)
CREATE TABLE `ai_tools` (
  `content_id` BIGINT UNSIGNED NOT NULL PRIMARY KEY,
  `website_url` VARCHAR(255) NOT NULL,
  `pricing_model` ENUM('free', 'freemium', 'paid', 'subscription', 'open_source') NOT NULL DEFAULT 'freemium',
  `free_trial_available` TINYINT(1) NOT NULL DEFAULT 1,
  `pricing_starting_at` VARCHAR(50) NULL,
  `api_available` TINYINT(1) NOT NULL DEFAULT 0,
  `open_source_repo` VARCHAR(255) NULL,
  `verified_by_techscout` TINYINT(1) NOT NULL DEFAULT 0,
  `rating_avg` DECIMAL(3,2) NOT NULL DEFAULT 0.00,
  `rating_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `affiliate_url` VARCHAR(255) NULL,
  CONSTRAINT `fk_ai_tools_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- GPT Listings Extension (Task 619)
CREATE TABLE `gpt_listings` (
  `content_id` BIGINT UNSIGNED NOT NULL PRIMARY KEY,
  `openai_url` VARCHAR(255) NOT NULL,
  `model_base` VARCHAR(50) NOT NULL DEFAULT 'GPT-4o',
  `prompt_starters` JSON NULL,
  `creator_handle` VARCHAR(100) NULL,
  `use_case` VARCHAR(120) NULL,
  CONSTRAINT `fk_gpts_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Prompts Extension (Task 620)
CREATE TABLE `prompts` (
  `content_id` BIGINT UNSIGNED NOT NULL PRIMARY KEY,
  `prompt_template` LONGTEXT NOT NULL,
  `template_variables` JSON NULL, -- e.g. [{"name": "topic", "type": "text"}]
  `target_models` JSON NULL,     -- e.g. ["ChatGPT", "Claude", "Gemini"]
  `copy_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `sample_output` TEXT NULL,
  CONSTRAINT `fk_prompts_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Campaigns Extension (Task 629)
CREATE TABLE `campaigns` (
  `content_id` BIGINT UNSIGNED NOT NULL PRIMARY KEY,
  `campaign_type` ENUM('launch', 'hackathon', 'contest', 'sponsorship', 'referral') NOT NULL,
  `banner_image_url` VARCHAR(255) NULL,
  `start_date` DATETIME NOT NULL,
  `end_date` DATETIME NOT NULL,
  `prize_pool` VARCHAR(100) NULL,
  `rules_markdown` TEXT NULL,
  `external_partner_url` VARCHAR(255) NULL,
  CONSTRAINT `fk_campaigns_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- 6. CONTENT TAG RELATIONSHIPS & REVISIONS (Tasks 614 & 617)
-- ----------------------------------------------------------------------------
CREATE TABLE `content_tags` (
  `content_id` BIGINT UNSIGNED NOT NULL,
  `tag_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`content_id`, `tag_id`),
  CONSTRAINT `fk_ct_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ct_tag` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `content_revisions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `content_id` BIGINT UNSIGNED NOT NULL,
  `editor_id` BIGINT UNSIGNED NOT NULL,
  `version_number` INT UNSIGNED NOT NULL DEFAULT 1,
  `title` VARCHAR(255) NOT NULL,
  `summary` VARCHAR(500) NOT NULL,
  `body_html` LONGTEXT NULL,
  `change_summary` VARCHAR(255) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_revisions_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_revisions_editor` FOREIGN KEY (`editor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  INDEX `idx_revisions_content_version` (`content_id`, `version_number` DESC)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- 7. COMMENTS, REPLIES & MODERATION (Tasks 623 & 624)
-- ----------------------------------------------------------------------------
CREATE TABLE `comments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `content_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `parent_id` BIGINT UNSIGNED NULL,
  `comment_text` TEXT NOT NULL,
  `is_pinned` TINYINT(1) NOT NULL DEFAULT 0,
  `status` ENUM('visible', 'flagged', 'hidden', 'deleted') NOT NULL DEFAULT 'visible',
  `reaction_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `reply_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_comments_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_comments_parent` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  INDEX `idx_comments_content_parent` (`content_id`, `parent_id`, `created_at` ASC)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- 8. REACTIONS, BOOKMARKS & SOCIAL GRAPH (Tasks 625, 626, 627)
-- ----------------------------------------------------------------------------
CREATE TABLE `reactions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `content_id` BIGINT UNSIGNED NOT NULL,
  `reaction_type` ENUM('like', 'clap', 'rocket', 'insightful', 'mind_blown', 'bookmark') NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_content_reaction` (`user_id`, `content_id`, `reaction_type`),
  CONSTRAINT `fk_reactions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reactions_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE,
  INDEX `idx_reactions_content_type` (`content_id`, `reaction_type`)
) ENGINE=InnoDB;

CREATE TABLE `bookmarks` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `content_id` BIGINT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_content_bookmark` (`user_id`, `content_id`),
  CONSTRAINT `fk_bookmarks_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_bookmarks_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `follows` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `follower_user_id` BIGINT UNSIGNED NOT NULL,
  `following_author_id` BIGINT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_follower_following` (`follower_user_id`, `following_author_id`),
  CONSTRAINT `fk_follows_follower` FOREIGN KEY (`follower_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_follows_author` FOREIGN KEY (`following_author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- 9. NOTIFICATIONS & EMAIL SUBSCRIPTIONS (Tasks 628, 639, 640)
-- ----------------------------------------------------------------------------
CREATE TABLE `notifications` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `event_type` VARCHAR(60) NOT NULL, -- 'comment_reply', 'new_follower', 'article_approved', 'milestone_reached'
  `title` VARCHAR(150) NOT NULL,
  `message` VARCHAR(300) NOT NULL,
  `link_url` VARCHAR(255) NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  INDEX `idx_notif_user_read` (`user_id`, `is_read`, `created_at` DESC)
) ENGINE=InnoDB;

CREATE TABLE `newsletter_subscriptions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(191) NOT NULL UNIQUE,
  `status` ENUM('pending', 'subscribed', 'unsubscribed') NOT NULL DEFAULT 'subscribed',
  `subscriber_type` ENUM('all', 'weekly_digest', 'ai_tools_only', 'developer_only') NOT NULL DEFAULT 'all',
  `confirmation_token` VARCHAR(100) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_news_email` (`email`)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- 10. ADVERTISEMENTS, PAYMENTS & MONETIZATION (Tasks 630, 631, 632, 633)
-- ----------------------------------------------------------------------------
CREATE TABLE `advertisements` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `advertiser_user_id` BIGINT UNSIGNED NOT NULL,
  `slot_location` ENUM('header_banner', 'article_inline', 'sidebar_sticky', 'category_featured', 'newsletter_top') NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `image_url` VARCHAR(255) NOT NULL,
  `target_url` VARCHAR(255) NOT NULL,
  `start_date` DATETIME NOT NULL,
  `end_date` DATETIME NOT NULL,
  `impression_count` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `click_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `cost_usd` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `status` ENUM('pending', 'active', 'paused', 'expired') NOT NULL DEFAULT 'pending',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_ads_user` FOREIGN KEY (`advertiser_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  INDEX `idx_ads_slot_status` (`slot_location`, `status`, `start_date`, `end_date`)
) ENGINE=InnoDB;

CREATE TABLE `revenue_records` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `author_id` BIGINT UNSIGNED NOT NULL,
  `content_id` BIGINT UNSIGNED NULL,
  `revenue_type` ENUM('ad_share', 'affiliate', 'sponsorship_pool', 'referral_credit', 'non_cash_credit') NOT NULL,
  `amount_usd` DECIMAL(10,4) NOT NULL DEFAULT 0.0000,
  `credits_granted` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `description` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_rev_author` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rev_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE SET NULL,
  INDEX `idx_rev_author_date` (`author_id`, `created_at` DESC)
) ENGINE=InnoDB;

CREATE TABLE `payouts` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `author_id` BIGINT UNSIGNED NOT NULL,
  `amount_usd` DECIMAL(10,2) NOT NULL,
  `payout_method` VARCHAR(50) NOT NULL,
  `status` ENUM('requested', 'approved', 'processed', 'rejected') NOT NULL DEFAULT 'requested',
  `processed_at` DATETIME NULL,
  `transaction_ref` VARCHAR(100) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_payouts_author` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE,
  INDEX `idx_payouts_status` (`status`)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- 11. ANALYTICS, AUDIT LOGS & REPORTS (Tasks 634, 635, 636, 637, 638)
-- ----------------------------------------------------------------------------
CREATE TABLE `analytics_events` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `event_type` VARCHAR(50) NOT NULL, -- 'pageview', 'read_complete', 'scroll_depth', 'copy_prompt', 'click_affiliate'
  `content_id` BIGINT UNSIGNED NULL,
  `author_id` BIGINT UNSIGNED NULL,
  `user_id` BIGINT UNSIGNED NULL,
  `ip_hash` CHAR(64) NULL,
  `referrer_domain` VARCHAR(150) NULL,
  `device_type` ENUM('desktop', 'mobile', 'tablet', 'bot') NOT NULL DEFAULT 'desktop',
  `country_code` CHAR(2) NULL,
  `duration_seconds` INT UNSIGNED NULL DEFAULT 0,
  `metadata_json` JSON NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_events_content_time` (`content_id`, `event_time`),
  INDEX `idx_events_author_time` (`author_id`, `event_time`),
  INDEX `idx_events_type` (`event_type`, `event_time`)
) ENGINE=InnoDB;

CREATE TABLE `audit_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NULL,
  `action` VARCHAR(80) NOT NULL, -- 'user.login', 'article.publish', 'user.ban', 'config.change'
  `entity_type` VARCHAR(50) NULL,
  `entity_id` BIGINT UNSIGNED NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `user_agent` VARCHAR(255) NULL,
  `details` JSON NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_audit_user_action` (`user_id`, `action`),
  INDEX `idx_audit_created` (`created_at` DESC)
) ENGINE=InnoDB;

CREATE TABLE `reports` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `reporter_user_id` BIGINT UNSIGNED NULL,
  `content_id` BIGINT UNSIGNED NULL,
  `comment_id` BIGINT UNSIGNED NULL,
  `reason` ENUM('spam', 'plagiarism', 'misinformation', 'abusive', 'broken_link', 'copyright', 'other') NOT NULL,
  `notes` TEXT NULL,
  `status` ENUM('pending', 'investigating', 'resolved', 'dismissed') NOT NULL DEFAULT 'pending',
  `resolved_by_user_id` BIGINT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_reports_status` (`status`)
) ENGINE=InnoDB;
```
