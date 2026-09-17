-- Migration: 2026_09_17_000001_categories_and_subcategories_upgrade
-- Purpose: Optimize categories & subcategories indexing, remove topic coupling, ensure hierarchy performance

-- 1. Ensure indexes exist for fast category & subcategory retrieval
SET @exist_idx_parent := (SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'categories' AND index_name = 'idx_categories_parent_id');
SET @sql_idx_parent := IF(@exist_idx_parent = 0, 'ALTER TABLE `categories` ADD INDEX `idx_categories_parent_id` (`parent_id`)', 'SELECT 1');
PREPARE stmt_idx_parent FROM @sql_idx_parent;
EXECUTE stmt_idx_parent;
DEALLOCATE PREPARE stmt_idx_parent;

SET @exist_idx_scope := (SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'categories' AND index_name = 'idx_categories_applicable_to');
SET @sql_idx_scope := IF(@exist_idx_scope = 0, 'ALTER TABLE `categories` ADD INDEX `idx_categories_applicable_to` (`applicable_to`)', 'SELECT 1');
PREPARE stmt_idx_scope FROM @sql_idx_scope;
EXECUTE stmt_idx_scope;
DEALLOCATE PREPARE stmt_idx_scope;

SET @exist_idx_sort := (SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'categories' AND index_name = 'idx_categories_sort_order');
SET @sql_idx_sort := IF(@exist_idx_sort = 0, 'ALTER TABLE `categories` ADD INDEX `idx_categories_sort_order` (`sort_order`)', 'SELECT 1');
PREPARE stmt_idx_sort FROM @sql_idx_sort;
EXECUTE stmt_idx_sort;
DEALLOCATE PREPARE stmt_idx_sort;

-- 2. Clean up any invalid circular parenting
UPDATE `categories` SET `parent_id` = NULL WHERE `parent_id` = `id`;
