-- ============================================================================
-- TechScout Database Upgrade: Prompt Library Segregation & Pricing Settings
-- Non-destructive upgrade script
-- ============================================================================

-- 1. Safely add prompt_type column to prompts table if it doesn't already exist
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'prompts' 
  AND COLUMN_NAME = 'prompt_type';

SET @ddl = IF(@col_exists = 0, 
    'ALTER TABLE `prompts` ADD COLUMN `prompt_type` ENUM(\'text\', \'image\') NOT NULL DEFAULT \'text\' AFTER `content_id`', 
    'SELECT "prompt_type already exists"');
PREPARE stmt FROM @ddl;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2. Identify and classify AI image generation prompts based on categories and target models
UPDATE `prompts` p
JOIN `contents` c ON p.content_id = c.id
LEFT JOIN `categories` cat ON c.category_id = cat.id
SET p.prompt_type = 'image'
WHERE cat.slug IN ('ai-image-prompts', 'midjourney', 'dall-e', 'flux', 'stable-diffusion', 'photorealism', '3d-renders', 'anime', 'architecture', 'landscapes', 'fantasy')
   OR p.target_models LIKE '%Midjourney%'
   OR p.target_models LIKE '%DALL-E%'
   OR p.target_models LIKE '%Flux%'
   OR p.target_models LIKE '%Stable Diffusion%'
   OR p.target_models LIKE '%Leonardo%'
   OR p.target_models LIKE '%Ideogram%';

-- 3. Strict Image Isolation: Text prompts will have NO image attached. Only image prompts may have images attached.
UPDATE `contents` c
JOIN `prompts` p ON c.id = p.content_id
SET c.featured_image_url = NULL
WHERE p.prompt_type = 'text';

-- 4. Initial Site Settings for Live Pricing APIs & Exchange Rates
INSERT INTO `site_settings` (`key_name`, `key_value`, `updated_at`)
VALUES 
  ('pricing_api_provider', 'coingecko', NOW()),
  ('pricing_api_key', '', NOW()),
  ('pricing_cache_ttl', '120', NOW()),
  ('pricing_rates_cache', '{"BTC":{"price":64250,"change_24h":0},"ETH":{"price":3450,"change_24h":0},"SOL":{"price":145,"change_24h":0},"USDT":{"price":1,"change_24h":0},"updated_at":"2026-09-13 11:00:00"}', NOW()),
  ('pricing_fallback_btc', '64250.00', NOW()),
  ('pricing_fallback_eth', '3450.00', NOW()),
  ('pricing_fallback_sol', '145.00', NOW()),
  ('pricing_fallback_usdt', '1.00', NOW())
ON DUPLICATE KEY UPDATE 
  `key_value` = VALUES(`key_value`),
  `updated_at` = NOW();
