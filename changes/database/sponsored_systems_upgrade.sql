-- ============================================================================
-- TechScout Upgrade: Sponsored Tools & Editorial Posts Submission System
-- Idempotent Non-Destructive Table Migration
-- ============================================================================

CREATE TABLE IF NOT EXISTS `sponsored_submissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('tool','article','banner') NOT NULL DEFAULT 'tool',
  `title` varchar(255) NOT NULL,
  `target_url` varchar(500) DEFAULT NULL,
  `category_id` int(10) unsigned DEFAULT NULL,
  `author_name` varchar(150) NOT NULL,
  `author_email` varchar(150) NOT NULL,
  `summary` text DEFAULT NULL,
  `body_content` longtext DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `pricing_amount_usd` decimal(10,2) NOT NULL DEFAULT 0.00,
  `crypto_currency` varchar(20) NOT NULL DEFAULT 'USDT',
  `crypto_txid` varchar(255) DEFAULT NULL,
  `crypto_wallet_sent` varchar(255) DEFAULT NULL,
  `status` enum('pending_review','approved','rejected') NOT NULL DEFAULT 'pending_review',
  `admin_notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_spons_status` (`status`),
  KEY `idx_spons_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
