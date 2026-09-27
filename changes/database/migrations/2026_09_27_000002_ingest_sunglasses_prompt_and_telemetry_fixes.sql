-- TechScout Ingestion: B&W Cinematic Reflective Sunglasses AI Prompt & Schema Alignment
-- Migration: 2026_09_27_000002_ingest_sunglasses_prompt_and_telemetry_fixes.sql

-- 1. Ensure columns exist on categories, author_tips, ai_tools, gpt_listings, prompts
ALTER TABLE `categories` ADD COLUMN IF NOT EXISTS `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP() AFTER `created_at`;
ALTER TABLE `author_tips` ADD COLUMN IF NOT EXISTS `supporter_user_id` BIGINT(20) UNSIGNED NULL AFTER `tipper_user_id`;
ALTER TABLE `ai_tools` ADD COLUMN IF NOT EXISTS `slug` VARCHAR(100) NULL AFTER `content_id`;
ALTER TABLE `gpt_listings` ADD COLUMN IF NOT EXISTS `slug` VARCHAR(100) NULL AFTER `content_id`;
ALTER TABLE `prompts` ADD COLUMN IF NOT EXISTS `slug` VARCHAR(100) NULL AFTER `content_id`;

UPDATE `author_tips` SET `supporter_user_id` = `tipper_user_id` WHERE `supporter_user_id` IS NULL AND `tipper_user_id` IS NOT NULL;
UPDATE `ai_tools` t JOIN `contents` c ON t.content_id = c.id SET t.slug = c.slug WHERE t.slug IS NULL;
UPDATE `gpt_listings` g JOIN `contents` c ON g.content_id = c.id SET g.slug = c.slug WHERE g.slug IS NULL;
UPDATE `prompts` p JOIN `contents` c ON p.content_id = c.id SET p.slug = c.slug WHERE p.slug IS NULL;

-- 2. Category Ingestion
INSERT IGNORE INTO `categories` (`name`, `slug`, `applicable_to`, `description`, `created_at`) 
VALUES ('Black & White', 'black-and-white', 'prompt', 'Black & White fine-art and high-contrast photography prompts.', NOW());

SET @cat_id = COALESCE((SELECT `id` FROM `categories` WHERE `slug` = 'black-and-white' LIMIT 1), 1);
SET @author_id = COALESCE((SELECT `id` FROM `users` WHERE `role` = 'admin' OR `is_admin` = 1 LIMIT 1), 1);

-- 3. Content Entity Ingestion
INSERT INTO `contents` (`uuid`, `content_type`, `author_id`, `category_id`, `title`, `slug`, `summary`, `featured_image_url`, `status`, `visibility`, `is_featured`, `is_trending`, `published_at`, `created_at`, `updated_at`)
VALUES (
  UUID(), 
  'prompt', 
  @author_id, 
  @cat_id, 
  'B&W Cinematic Reflective Sunglasses AI Prompt', 
  'bw-cinematic-reflective-sunglasses-ai-prompt', 
  'Copy this AI prompt to generate dramatic black-and-white cinematic portraits featuring high-contrast lighting and round sunglasses reflecting a city skyline.', 
  'https://img.promptplum.com/cdn-cgi/image/q=78,scq=50,width=1400,height=1400,fit=contain,f=auto/promptImages/736df081-6641-45fd-9fd6-9651fe7e9cb1.webp', 
  'published', 
  'public', 
  1, 
  1, 
  NOW(), 
  NOW(), 
  NOW()
)
ON DUPLICATE KEY UPDATE 
  `title` = VALUES(`title`),
  `summary` = VALUES(`summary`),
  `featured_image_url` = VALUES(`featured_image_url`),
  `status` = 'published',
  `visibility` = 'public',
  `updated_at` = NOW();

SET @prompt_content_id = COALESCE((SELECT `id` FROM `contents` WHERE `slug` = 'bw-cinematic-reflective-sunglasses-ai-prompt' LIMIT 1), 1);

-- 4. Prompts Extension Ingestion
INSERT INTO `prompts` (`content_id`, `slug`, `prompt_type`, `prompt_template`, `target_models`, `aspect_ratio`, `parameters_json`)
VALUES (
  @prompt_content_id,
  'bw-cinematic-reflective-sunglasses-ai-prompt',
  'image',
  'Create a dramatic, high-end fashion editorial black-and-white medium close-up portrait of the provided face image, with sharp facial features, wearing round reflective sunglasses. He gazes upward confidently into a dark void. The lighting is high-contrast and chiaroscuro, highlighting the contours of his face and his black t-shirt. The sunglasses reflect a tall city skyline. The mood is mysterious and cinematic, with a minimalist black background. 8k, 1:1 aspect-ratio.',
  '["Gemini", "Gemini Watermark Remover"]',
  '1:1',
  '{"models": ["Gemini", "Gemini Watermark Remover"], "aspect_ratio": "1:1", "difficulty": "Production", "show_parameters": true, "show_models": true, "show_aspect_ratio": true}'
)
ON DUPLICATE KEY UPDATE 
  `prompt_template` = VALUES(`prompt_template`),
  `target_models` = VALUES(`target_models`),
  `aspect_ratio` = VALUES(`aspect_ratio`),
  `parameters_json` = VALUES(`parameters_json`);

-- 5. Prompt Image Ingestion
INSERT INTO `prompt_images` (`prompt_id`, `image_url`, `caption`, `sort_order`, `created_at`)
SELECT @prompt_content_id, 'https://img.promptplum.com/cdn-cgi/image/q=78,scq=50,width=1400,height=1400,fit=contain,f=auto/promptImages/736df081-6641-45fd-9fd6-9651fe7e9cb1.webp', 'High-contrast B&W portrait with skyline reflections in round sunglasses', 0, NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM `prompt_images` WHERE `prompt_id` = @prompt_content_id AND `image_url` = 'https://img.promptplum.com/cdn-cgi/image/q=78,scq=50,width=1400,height=1400,fit=contain,f=auto/promptImages/736df081-6641-45fd-9fd6-9651fe7e9cb1.webp'
);

-- 6. Content Categories Link
INSERT IGNORE INTO `content_categories` (`content_id`, `category_id`, `is_primary`) 
VALUES (@prompt_content_id, @cat_id, 1);
