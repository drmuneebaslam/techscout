-- ============================================================================
-- Migration: 2026_09_26_000001_prompts_optimization_and_custom_api_gateway
-- Safe, idempotent migration for MySQL 5.7+ / MariaDB / MySQL 8.0+
-- Adds rich prompt engineering & image generation columns to `prompts`
-- Creates `custom_api_endpoints` for Dynamic API Gateway & Schema Introspector
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Ensure `custom_api_endpoints` table exists
CREATE TABLE IF NOT EXISTS `custom_api_endpoints` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `endpoint_type` enum('table_schema','table_list','table_data','custom_json') NOT NULL DEFAULT 'table_data',
  `target_table` varchar(100) DEFAULT NULL,
  `selected_columns` text DEFAULT NULL,
  `where_conditions_json` text DEFAULT NULL,
  `order_by_column` varchar(100) DEFAULT 'id',
  `order_direction` enum('ASC','DESC') NOT NULL DEFAULT 'DESC',
  `result_limit` int(11) NOT NULL DEFAULT 50,
  `custom_response_template` longtext DEFAULT NULL,
  `auth_required` tinyint(1) NOT NULL DEFAULT 0,
  `secret_token` varchar(128) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `request_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `last_accessed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_cae_slug` (`slug`),
  KEY `idx_cae_active` (`is_active`),
  KEY `idx_cae_type` (`endpoint_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Safely add rich prompt generation columns to `prompts` table
ALTER TABLE `prompts` ADD COLUMN IF NOT EXISTS `prompt_type` VARCHAR(50) NOT NULL DEFAULT 'text';
ALTER TABLE `prompts` ADD COLUMN IF NOT EXISTS `aspect_ratio` VARCHAR(20) DEFAULT '16:9';
ALTER TABLE `prompts` ADD COLUMN IF NOT EXISTS `negative_prompt` TEXT NULL;
ALTER TABLE `prompts` ADD COLUMN IF NOT EXISTS `model_version` VARCHAR(100) NULL;
ALTER TABLE `prompts` ADD COLUMN IF NOT EXISTS `cfg_scale` DECIMAL(5,2) NULL DEFAULT 7.00;
ALTER TABLE `prompts` ADD COLUMN IF NOT EXISTS `steps` INT NULL DEFAULT 30;
ALTER TABLE `prompts` ADD COLUMN IF NOT EXISTS `seed` BIGINT NULL DEFAULT NULL;
ALTER TABLE `prompts` ADD COLUMN IF NOT EXISTS `sampler` VARCHAR(50) NULL DEFAULT 'Euler a';
ALTER TABLE `prompts` ADD COLUMN IF NOT EXISTS `style_preset` VARCHAR(100) NULL DEFAULT 'Photorealistic';
ALTER TABLE `prompts` ADD COLUMN IF NOT EXISTS `difficulty_level` VARCHAR(50) NOT NULL DEFAULT 'beginner';
ALTER TABLE `prompts` ADD COLUMN IF NOT EXISTS `system_prompt` TEXT NULL;
ALTER TABLE `prompts` ADD COLUMN IF NOT EXISTS `parameters_json` LONGTEXT NULL;
ALTER TABLE `prompts` ADD COLUMN IF NOT EXISTS `how_to_use` TEXT NULL;

-- 3. Seed Starter Custom API Endpoints if not present
INSERT IGNORE INTO `custom_api_endpoints` 
  (`name`, `slug`, `description`, `endpoint_type`, `target_table`, `selected_columns`, `order_by_column`, `order_direction`, `result_limit`, `auth_required`, `is_active`)
VALUES 
  ('Database Tables Catalog', 'database-tables', 'Returns list of accessible tables with structural metadata for microservices and data pipelines', 'table_list', NULL, NULL, 'id', 'DESC', 50, 0, 1),
  ('Prompts Schema Inspector', 'prompts-schema', 'Returns the full column structure and types of the prompts database table', 'table_schema', 'prompts', NULL, 'id', 'DESC', 50, 0, 1),
  ('Public Prompts Feed API', 'prompts-feed', 'Exposes published prompt blueprints with template parameters, engine, and aspect ratio', 'table_data', 'prompts', 'content_id,prompt_type,aspect_ratio,negative_prompt,model_version,cfg_scale,steps,style_preset,difficulty_level,copy_count', 'copy_count', 'DESC', 25, 0, 1),
  ('AI Tools Catalog API', 'tools-feed', 'Exposes verified AI directory tools with pricing, ratings, and URLs', 'table_data', 'ai_tools', 'id,content_id,pricing_type,starting_price,rating_score,verified_badge', 'id', 'DESC', 25, 0, 1);

SET FOREIGN_KEY_CHECKS = 1;
