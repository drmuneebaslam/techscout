-- Ingest tags for B&W Cinematic Reflective Sunglasses AI Prompt
-- Migration: 2026_09_27_000004_sunglasses_tags.sql

SET @pid = COALESCE((SELECT `id` FROM `contents` WHERE `slug` = 'bw-cinematic-reflective-sunglasses-ai-prompt' LIMIT 1), 59);

-- Insert tags if not exist
INSERT IGNORE INTO `tags` (`name`, `slug`, `created_at`) VALUES
('Black & White', 'black-and-white', NOW()),
('Cinematic Portrait', 'cinematic-portrait', NOW()),
('Fashion & Editorial', 'fashion-and-editorial', NOW()),
('Men', 'men', NOW()),
('Moody', 'moody', NOW()),
('Portraits', 'portraits', NOW()),
('Urban', 'urban', NOW()),
('Dark Aesthetic', 'dark-aesthetic', NOW()),
('Gemini', 'gemini', NOW()),
('Studio Portrait', 'studio-portrait', NOW());

-- Link tags to content
INSERT IGNORE INTO `content_tags` (`content_id`, `tag_id`)
SELECT @pid, `id` FROM `tags` WHERE `slug` IN (
  'black-and-white',
  'cinematic-portrait',
  'fashion-and-editorial',
  'men',
  'moody',
  'portraits',
  'urban',
  'dark-aesthetic',
  'gemini',
  'studio-portrait'
);
