-- ============================================================================
-- TechScout Migration: Entity Categories Junction & Category Subscriptions
-- Migration: 2026_09_17_000002_entity_categories_and_subscriptions
-- Safe, idempotent script for Many-to-Many Entity Taxonomy & Subscriptions
-- ============================================================================

-- 1. Ensure content_categories many-to-many junction table exists
CREATE TABLE IF NOT EXISTS `content_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` bigint(20) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_content_category` (`content_id`, `category_id`),
  KEY `idx_cc_category` (`category_id`),
  KEY `idx_cc_content` (`content_id`),
  KEY `idx_cc_primary` (`is_primary`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Backfill existing primary categories from contents table into content_categories
INSERT IGNORE INTO `content_categories` (`content_id`, `category_id`, `is_primary`, `created_at`)
SELECT `id`, `category_id`, 1, NOW()
FROM `contents`
WHERE `category_id` IS NOT NULL AND `category_id` > 0;

-- 3. Ensure user_subscriptions supports category subscriptions
CREATE TABLE IF NOT EXISTS `user_subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `subscribable_type` varchar(50) NOT NULL DEFAULT 'category',
  `subscribable_id` bigint(20) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_sub` (`user_id`, `subscribable_type`, `subscribable_id`),
  KEY `idx_sub_lookup` (`subscribable_type`, `subscribable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Ensure categories table column applicable_to supports all entity types including image_prompt
ALTER TABLE `categories` MODIFY COLUMN `applicable_to` VARCHAR(50) NOT NULL DEFAULT 'all';

-- 5. Safe indexing on categories
ALTER TABLE `categories` ADD INDEX IF NOT EXISTS `idx_cat_parent_scope` (`parent_id`, `applicable_to`);
ALTER TABLE `categories` ADD INDEX IF NOT EXISTS `idx_cat_scope_featured` (`applicable_to`, `is_featured_home`);

-- 6. Permanently purge legacy Topics from navigation menus
DELETE FROM `navigation_menus` WHERE `label` = 'Topics' OR `url` LIKE '%/topics%';

