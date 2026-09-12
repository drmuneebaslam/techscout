-- Migration 2026_09_12_000002_add_social_and_crypto_indices
-- High-performance indices for fast comment streaming, tip ledger queries, and prompt gallery loading

ALTER TABLE `comments` ADD INDEX IF NOT EXISTS `idx_comments_content_pinned` (`content_id`, `is_pinned`);
ALTER TABLE `comments` ADD INDEX IF NOT EXISTS `idx_comments_created` (`created_at`);
ALTER TABLE `author_tips` ADD INDEX IF NOT EXISTS `idx_author_tips_status_date` (`status`, `created_at`);
ALTER TABLE `prompt_images` ADD INDEX IF NOT EXISTS `idx_prompt_sort` (`prompt_id`, `sort_order`);
ALTER TABLE `article_polls` ADD INDEX IF NOT EXISTS `idx_polls_content_active` (`content_id`, `is_active`);
