-- Migration: 2026_09_27_000005_fix_sunglasses_prompt_and_secondary_categories.sql
-- Purpose: Ingest and link primary + secondary categories for AI Prompts (applicable_to = 'prompt')
-- Populate prompts & prompt_images data robustly for bw-cinematic-reflective-sunglasses-ai-prompt

-- 1. Ensure all prompt categories exist with applicable_to = 'prompt'
INSERT INTO `categories` (`name`, `slug`, `applicable_to`, `icon_name`, `color_hex`, `sort_order`, `is_featured_home`) VALUES
('Black & White', 'black-and-white', 'prompt', 'camera', '#64748b', 1, 1),
('Cinematic Portrait', 'cinematic-portrait', 'prompt', 'film', '#6366f1', 2, 0),
('Fashion & Editorial', 'fashion-and-editorial', 'prompt', 'sparkles', '#ec4899', 3, 0),
('Men', 'men', 'prompt', 'user', '#3b82f6', 4, 0),
('Moody', 'moody', 'prompt', 'moon', '#8b5cf6', 5, 0),
('Portraits', 'portraits', 'prompt', 'smile', '#06b6d4', 6, 0),
('Urban', 'urban', 'prompt', 'building', '#10b981', 7, 0),
('Dark Aesthetic', 'dark-aesthetic', 'prompt', 'eye-off', '#475569', 8, 0),
('Gemini', 'gemini', 'prompt', 'cpu', '#f59e0b', 9, 0),
('Studio Portrait', 'studio-portrait', 'prompt', 'aperture', '#14b8a6', 10, 0)
ON DUPLICATE KEY UPDATE `applicable_to` = 'prompt';

-- 2. Link Primary Category to contents table
UPDATE `contents` c
JOIN `categories` cat ON cat.slug = 'black-and-white'
SET c.`category_id` = cat.id
WHERE c.`slug` = 'bw-cinematic-reflective-sunglasses-ai-prompt';

-- 3. Link Content Categories (Primary + Secondary)
DELETE cc FROM `content_categories` cc
JOIN `contents` c ON cc.content_id = c.id
WHERE c.`slug` = 'bw-cinematic-reflective-sunglasses-ai-prompt';

-- Insert Primary Category (is_primary = 1)
INSERT INTO `content_categories` (`content_id`, `category_id`, `is_primary`)
SELECT c.id, cat.id, 1
FROM `contents` c
JOIN `categories` cat ON cat.slug = 'black-and-white'
WHERE c.`slug` = 'bw-cinematic-reflective-sunglasses-ai-prompt';

-- Insert Secondary Categories (is_primary = 0)
INSERT INTO `content_categories` (`content_id`, `category_id`, `is_primary`)
SELECT c.id, cat.id, 0
FROM `contents` c
JOIN `categories` cat ON cat.slug IN (
  'cinematic-portrait',
  'fashion-and-editorial',
  'men',
  'moody',
  'portraits',
  'urban',
  'dark-aesthetic',
  'gemini',
  'studio-portrait'
)
WHERE c.`slug` = 'bw-cinematic-reflective-sunglasses-ai-prompt';

-- 4. Ensure prompts schema columns exist
ALTER TABLE `prompts` ADD COLUMN IF NOT EXISTS `aspect_ratio` VARCHAR(20) DEFAULT '16:9';
ALTER TABLE `prompts` ADD COLUMN IF NOT EXISTS `negative_prompt` TEXT NULL;
ALTER TABLE `prompts` ADD COLUMN IF NOT EXISTS `model_version` VARCHAR(100) NULL;
ALTER TABLE `prompts` ADD COLUMN IF NOT EXISTS `difficulty_level` VARCHAR(50) NOT NULL DEFAULT 'beginner';
ALTER TABLE `prompts` ADD COLUMN IF NOT EXISTS `parameters_json` LONGTEXT NULL;

-- 5. Robust Insert/Update for prompts child table
INSERT INTO `prompts` (
  `content_id`,
  `prompt_type`,
  `prompt_template`,
  `target_models`,
  `copy_count`
)
SELECT 
  c.id,
  'image',
  'Create a dramatic, high-end fashion editorial black-and-white medium close-up portrait of the provided face image, with sharp facial features, wearing round reflective sunglasses. He gazes upward confidently into a dark void. The lighting is high-contrast and chiaroscuro, highlighting the contours of his face and his black t-shirt. The sunglasses reflect a tall city skyline. The mood is mysterious and cinematic, with a minimalist black background. 8k, 1:1 aspect-ratio.',
  '[\"Gemini\", \"Gemini Watermark Remover\"]',
  0
FROM `contents` c
WHERE c.`slug` = 'bw-cinematic-reflective-sunglasses-ai-prompt'
ON DUPLICATE KEY UPDATE
  `prompt_type` = VALUES(`prompt_type`),
  `prompt_template` = VALUES(`prompt_template`),
  `target_models` = VALUES(`target_models`);

UPDATE `prompts` p
JOIN `contents` c ON p.content_id = c.id
SET 
  p.aspect_ratio = '1:1',
  p.parameters_json = '{\"models\":[\"Gemini\",\"Gemini Watermark Remover\"],\"aspect_ratio\":\"1:1\",\"difficulty\":\"Production\",\"show_parameters\":true,\"show_models\":true,\"show_aspect_ratio\":true}'
WHERE c.`slug` = 'bw-cinematic-reflective-sunglasses-ai-prompt';

-- 6. Robust Insert into prompt_images
DELETE pi FROM `prompt_images` pi
JOIN `contents` c ON pi.prompt_id = c.id
WHERE c.`slug` = 'bw-cinematic-reflective-sunglasses-ai-prompt';

INSERT INTO `prompt_images` (`prompt_id`, `image_url`, `caption`, `sort_order`)
SELECT 
  c.id,
  'https://img.promptplum.com/cdn-cgi/image/q=78,scq=50,width=1400,height=1400,fit=contain,f=auto/promptImages/736df081-6641-45fd-9fd6-9651fe7e9cb1.webp',
  'High-contrast B&W portrait with skyline reflections in round sunglasses',
  0
FROM `contents` c
WHERE c.`slug` = 'bw-cinematic-reflective-sunglasses-ai-prompt';
