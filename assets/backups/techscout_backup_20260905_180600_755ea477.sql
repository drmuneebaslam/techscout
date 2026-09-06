-- ========================================================
-- TechScout Enterprise Disaster Recovery Snapshot
-- Generated: 2026-09-05 18:06:00
-- Database: techscout_db
-- ========================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET NAMES utf8mb4;

--
-- Structure for table `admin_role_permissions`
--
DROP TABLE IF EXISTS `admin_role_permissions`;
CREATE TABLE `admin_role_permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `permission_key` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_role_perm` (`role_id`,`permission_key`),
  KEY `idx_arp_role` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_role_permissions` (53 rows)
--
INSERT INTO `admin_role_permissions` (`id`, `role_id`, `permission_key`) VALUES 
(7, 1, 'articles.create'),
(10, 1, 'articles.delete'),
(8, 1, 'articles.edit'),
(11, 1, 'articles.feature'),
(9, 1, 'articles.publish'),
(6, 1, 'articles.view'),
(14, 1, 'authors.monetization'),
(15, 1, 'authors.suspend'),
(13, 1, 'authors.verify'),
(12, 1, 'authors.view'),
(24, 1, 'cms.branding'),
(23, 1, 'cms.navigation'),
(22, 1, 'cms.settings'),
(16, 1, 'community.comments'),
(18, 1, 'community.moderation'),
(17, 1, 'community.reports'),
(4, 1, 'revenue.adjust_balance'),
(3, 1, 'revenue.ledger'),
(2, 1, 'revenue.payouts'),
(5, 1, 'revenue.settings'),
(1, 1, 'revenue.view'),
(21, 1, 'support.escalate'),
(20, 1, 'support.reply'),
(19, 1, 'support.tickets'),
(27, 1, 'system.backups'),
(28, 1, 'system.flags'),
(26, 1, 'system.logs'),
(25, 1, 'system.rbac'),
(32, 2, 'revenue.adjust_balance'),
(31, 2, 'revenue.ledger'),
(30, 2, 'revenue.payouts'),
(33, 2, 'revenue.settings'),
(29, 2, 'revenue.view'),
(34, 2, 'system.logs'),
(36, 3, 'articles.create'),
(37, 3, 'articles.edit'),
(39, 3, 'articles.feature'),
(38, 3, 'articles.publish'),
(35, 3, 'articles.view'),
(42, 3, 'authors.monetization'),
(41, 3, 'authors.verify'),
(40, 3, 'authors.view'),
(43, 3, 'cms.navigation'),
(48, 4, 'authors.suspend'),
(47, 4, 'authors.view'),
(44, 4, 'community.comments'),
(46, 4, 'community.moderation'),
(45, 4, 'community.reports'),
(49, 4, 'system.logs'),
(53, 5, 'community.reports');
INSERT INTO `admin_role_permissions` (`id`, `role_id`, `permission_key`) VALUES 
(52, 5, 'support.escalate'),
(51, 5, 'support.reply'),
(50, 5, 'support.tickets');

--
-- Structure for table `admin_roles`
--
DROP TABLE IF EXISTS `admin_roles`;
CREATE TABLE `admin_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_key` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_key` (`role_key`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_roles` (5 rows)
--
INSERT INTO `admin_roles` (`id`, `role_key`, `name`, `description`, `is_system`, `created_at`) VALUES 
(1, 'superadmin', 'Superadministrator', 'Unrestricted administrative access to all system subsystems.', 1, '2026-09-04 21:17:02'),
(2, 'financial_auditor', 'Financial Auditor', 'Full visibility into revenues, double-entry ledger, payouts, and tipping streams without content edit rights.', 1, '2026-09-04 21:17:02'),
(3, 'managing_editor', 'Lead Managing Editor', 'Publishing, article curation, author onboarding, and category taxonomy governance.', 1, '2026-09-04 21:17:02'),
(4, 'trust_safety', 'Trust & Safety Moderator', 'Community flag resolution, comment moderation, plagiarism investigations, and user strikes.', 1, '2026-09-04 21:17:02'),
(5, 'support_specialist', 'Support Specialist', 'Support ticket management, user assistance, and knowledge base curation.', 1, '2026-09-04 21:17:02');

--
-- Structure for table `advertisements`
--
DROP TABLE IF EXISTS `advertisements`;
CREATE TABLE `advertisements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `advertiser_user_id` bigint(20) unsigned NOT NULL,
  `slot_location` enum('header_banner','article_inline','sidebar_sticky','category_featured','newsletter_top') NOT NULL,
  `title` varchar(150) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `target_url` varchar(255) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `impression_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `click_count` int(10) unsigned NOT NULL DEFAULT 0,
  `cost_usd` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','active','paused','expired') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_ads_user` (`advertiser_user_id`),
  CONSTRAINT `fk_ads_user` FOREIGN KEY (`advertiser_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `advertisements` (4 rows)
--
INSERT INTO `advertisements` (`id`, `advertiser_user_id`, `slot_location`, `title`, `image_url`, `target_url`, `start_date`, `end_date`, `impression_count`, `click_count`, `cost_usd`, `status`, `created_at`) VALUES 
(1, 1, 'header_banner', 'VectorScale DB: Sub-millisecond Hybrid Embeddings Cloud', '/assets/images/ads/vectorscale_banner.webp', 'https://vectorscale.io?ref=techscout', '2026-08-06 06:22:55', '2026-11-04 06:22:55', 142850, 3850, 1250.00, 'active', '2026-08-06 06:22:55'),
(2, 1, 'article_inline', 'NeonAI Copilot: Local Privacy-First Code Assistant', '/assets/images/ads/neonai_inline.webp', 'https://neonai.dev?utm_source=techscout', '2026-08-06 06:22:55', '2026-11-04 06:22:55', 98400, 2410, 850.00, 'active', '2026-08-06 06:22:55'),
(3, 1, 'sidebar_sticky', 'Quantum Prompt Studio: Collaborative Prompt Engineering Suite', '/assets/images/ads/quantum_sidebar.webp', 'https://quantumprompt.cloud?ref=techscout_sponsor', '2026-08-06 06:22:55', '2026-11-04 06:22:55', 210600, 5120, 1800.00, 'active', '2026-08-06 06:22:55'),
(4, 1, 'category_featured', 'CloudMatrix Observability: P99 Latency Tracing for LLMs', '/assets/images/ads/cloudmatrix_card.webp', 'https://cloudmatrix.tech?sponsor=techscout', '2026-08-06 06:22:55', '2026-11-04 06:22:55', 64300, 1420, 620.00, 'active', '2026-08-06 06:22:55');

--
-- Structure for table `ai_tools`
--
DROP TABLE IF EXISTS `ai_tools`;
CREATE TABLE `ai_tools` (
  `content_id` bigint(20) unsigned NOT NULL,
  `website_url` varchar(255) NOT NULL,
  `docs_url` varchar(255) DEFAULT NULL,
  `discord_url` varchar(255) DEFAULT NULL,
  `twitter_handle` varchar(100) DEFAULT NULL,
  `pricing_model` enum('free','freemium','paid','subscription','free_trial','open_source','contact_pricing') NOT NULL DEFAULT 'freemium',
  `secondary_categories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`secondary_categories`)),
  `use_cases` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`use_cases`)),
  `free_trial_available` tinyint(1) NOT NULL DEFAULT 1,
  `free_trial_days` int(11) DEFAULT 14,
  `pricing_starting_at` varchar(50) DEFAULT NULL,
  `pricing_billing_cycle` varchar(50) DEFAULT 'monthly',
  `discount_code` varchar(100) DEFAULT NULL,
  `discount_deal` varchar(255) DEFAULT NULL,
  `features_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features_list`)),
  `platforms_supported` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`platforms_supported`)),
  `api_available` tinyint(1) NOT NULL DEFAULT 0,
  `open_source_repo` varchar(255) DEFAULT NULL,
  `license_type` varchar(100) DEFAULT 'Proprietary',
  `alternatives_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`alternatives_list`)),
  `comparison_highlights` text DEFAULT NULL,
  `editorial_review` text DEFAULT NULL,
  `pros_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`pros_list`)),
  `cons_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`cons_list`)),
  `verified_by_techscout` tinyint(1) NOT NULL DEFAULT 0,
  `rating_avg` decimal(3,2) NOT NULL DEFAULT 0.00,
  `rating_count` int(10) unsigned NOT NULL DEFAULT 0,
  `rating_ease_of_use` decimal(3,2) NOT NULL DEFAULT 4.80,
  `rating_value_for_money` decimal(3,2) NOT NULL DEFAULT 4.70,
  `rating_features` decimal(3,2) NOT NULL DEFAULT 4.90,
  `rating_performance` decimal(3,2) NOT NULL DEFAULT 4.85,
  `rating_support` decimal(3,2) NOT NULL DEFAULT 4.60,
  `screenshots_gallery` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`screenshots_gallery`)),
  `affiliate_url` varchar(255) DEFAULT NULL,
  `custom_affiliate_slug` varchar(100) DEFAULT NULL,
  `affiliate_commission_rate` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`content_id`),
  CONSTRAINT `fk_ai_tools_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ai_tools` (11 rows)
--
INSERT INTO `ai_tools` (`content_id`, `website_url`, `docs_url`, `discord_url`, `twitter_handle`, `pricing_model`, `secondary_categories`, `use_cases`, `free_trial_available`, `free_trial_days`, `pricing_starting_at`, `pricing_billing_cycle`, `discount_code`, `discount_deal`, `features_list`, `platforms_supported`, `api_available`, `open_source_repo`, `license_type`, `alternatives_list`, `comparison_highlights`, `editorial_review`, `pros_list`, `cons_list`, `verified_by_techscout`, `rating_avg`, `rating_count`, `rating_ease_of_use`, `rating_value_for_money`, `rating_features`, `rating_performance`, `rating_support`, `screenshots_gallery`, `affiliate_url`, `custom_affiliate_slug`, `affiliate_commission_rate`) VALUES 
(8, 'https://cursor.com', NULL, NULL, NULL, 'freemium', NULL, NULL, 1, 14, '$20/month', 'monthly', NULL, NULL, NULL, NULL, 1, NULL, 'Proprietary', NULL, NULL, NULL, NULL, NULL, 1, 4.90, 1240, 4.80, 4.70, 4.90, 4.85, 4.60, NULL, 'https://cursor.com?ref=techscout', NULL, NULL),
(9, 'https://v0.dev', NULL, NULL, NULL, 'freemium', NULL, NULL, 1, 14, '$20/month', 'monthly', NULL, NULL, NULL, NULL, 1, NULL, 'Proprietary', NULL, NULL, NULL, NULL, NULL, 1, 4.80, 890, 4.80, 4.70, 4.90, 4.85, 4.60, NULL, 'https://v0.dev?ref=techscout', NULL, NULL),
(10, 'https://bolt.new', NULL, NULL, NULL, 'freemium', NULL, NULL, 1, 14, '$20/month', 'monthly', NULL, NULL, NULL, NULL, 1, NULL, 'Proprietary', NULL, NULL, NULL, NULL, NULL, 1, 4.85, 960, 4.80, 4.70, 4.90, 4.85, 4.60, NULL, 'https://bolt.new?ref=techscout', NULL, NULL),
(11, 'https://perplexity.ai', NULL, NULL, NULL, 'freemium', NULL, NULL, 1, 14, '$20/month', 'monthly', NULL, NULL, NULL, NULL, 1, NULL, 'Proprietary', NULL, NULL, NULL, NULL, NULL, 1, 4.92, 2150, 4.80, 4.70, 4.90, 4.85, 4.60, NULL, 'https://perplexity.ai?ref=techscout', NULL, NULL),
(12, 'https://claude.ai', NULL, NULL, NULL, 'freemium', NULL, NULL, 1, 14, '$20/month', 'monthly', NULL, NULL, NULL, NULL, 1, NULL, 'Proprietary', NULL, NULL, NULL, NULL, NULL, 1, 4.95, 3400, 4.80, 4.70, 4.90, 4.85, 4.60, NULL, 'https://claude.ai?ref=techscout', NULL, NULL),
(13, 'https://supermaven.com', NULL, NULL, NULL, 'freemium', NULL, NULL, 1, 14, '$10/month', 'monthly', NULL, NULL, NULL, NULL, 1, NULL, 'Proprietary', NULL, NULL, NULL, NULL, NULL, 1, 4.78, 640, 4.80, 4.70, 4.90, 4.85, 4.60, NULL, 'https://supermaven.com?ref=techscout', NULL, NULL),
(14, 'https://cognition.ai', NULL, NULL, NULL, 'paid', NULL, NULL, 0, 14, '$500/month', 'monthly', NULL, NULL, NULL, NULL, 1, NULL, 'Proprietary', NULL, NULL, NULL, NULL, NULL, 1, 4.70, 420, 4.80, 4.70, 4.90, 4.85, 4.60, NULL, 'https://cognition.ai?ref=techscout', NULL, NULL),
(15, 'https://lovable.dev', NULL, NULL, NULL, 'freemium', NULL, NULL, 1, 14, '$20/month', 'monthly', NULL, NULL, NULL, NULL, 1, NULL, 'Proprietary', NULL, NULL, NULL, NULL, NULL, 1, 4.82, 780, 4.80, 4.70, 4.90, 4.85, 4.60, NULL, 'https://lovable.dev?ref=techscout', NULL, NULL),
(39, 'https://cursor.com', 'https://docs.cursor.com', 'https://discord.gg/cursor', '@cursor_ai', 'freemium', '[]', '[\"Code Generation\",\"Full-Stack Refactoring\",\"Terminal Automation\",\"Bug Fixing\"]', 1, 14, '$20', 'monthly', 'TECHSCOUT20', '20% OFF First 3 Months', '[\"Multi-file repository vector indexing\",\"Terminal command execution with auto-repair\",\"Claude 3.5 Sonnet & GPT-4o native support\",\"Strict Privacy Mode with zero data retention\"]', '[\"macOS Desktop\",\"Windows Desktop\",\"Linux Desktop\",\"VS Code Extension\"]', 1, 'https://github.com/getcursor/cursor', 'Proprietary Core with Open Source Extensions', '[\"GitHub Copilot\",\"Windsurf\",\"Cline\",\"Void\",\"Continue.dev\"]', 'Unlike inline-only completion tools, Cursor executes multi-file architectural changes directly from chat prompts with instant diff inspection.', 'TechScout Editorial Gold Award: The most impactful development tool of 2026. Vector indexing and Sonnet 3.5 integration reduce boilerplate by 60%.', '[\"Instant repository-wide context retrieval\",\"Unbeatable multi-file code diff engine\",\"Generous monthly fast-request quota\"]', '[\"Requires dedicated IDE download\",\"High memory usage on massive 50k+ file monorepos\"]', 1, 4.95, 0, 4.90, 4.80, 4.95, 4.90, 4.70, '[\"https:\\/\\/images.unsplash.com\\/photo-1618005182384-a83a8bd57fbe\",\"https:\\/\\/images.unsplash.com\\/photo-1526374965328-7f61d4dc18c5\"]', 'https://partner.cursor.com/?via=techscout', 'cursor-ai-pro', '25% Lifetime Recurring'),
(41, 'https://cursor.com', 'https://docs.cursor.com', 'https://discord.gg/cursor', '@cursor_ai', 'freemium', '[]', '[\"Code Generation\",\"Full-Stack Refactoring\",\"Terminal Automation\",\"Bug Fixing\"]', 1, 14, '$20', 'monthly', 'TECHSCOUT20', '20% OFF First 3 Months', '[\"Multi-file repository vector indexing\",\"Terminal command execution with auto-repair\",\"Claude 3.5 Sonnet & GPT-4o native support\",\"Strict Privacy Mode with zero data retention\"]', '[\"macOS Desktop\",\"Windows Desktop\",\"Linux Desktop\",\"VS Code Extension\"]', 1, 'https://github.com/getcursor/cursor', 'Proprietary Core with Open Source Extensions', '[\"GitHub Copilot\",\"Windsurf\",\"Cline\",\"Void\",\"Continue.dev\"]', 'Unlike inline-only completion tools, Cursor executes multi-file architectural changes directly from chat prompts with instant diff inspection.', 'TechScout Editorial Gold Award: The most impactful development tool of 2026. Vector indexing and Sonnet 3.5 integration reduce boilerplate by 60%.', '[\"Instant repository-wide context retrieval\",\"Unbeatable multi-file code diff engine\",\"Generous monthly fast-request quota\"]', '[\"Requires dedicated IDE download\",\"High memory usage on massive 50k+ file monorepos\"]', 1, 4.95, 0, 4.90, 4.80, 4.95, 4.90, 4.70, '[\"https:\\/\\/images.unsplash.com\\/photo-1618005182384-a83a8bd57fbe\",\"https:\\/\\/images.unsplash.com\\/photo-1526374965328-7f61d4dc18c5\"]', 'https://partner.cursor.com/?via=techscout', 'cursor-ai-pro', '25% Lifetime Recurring'),
(42, 'https://cursor.com', 'https://docs.cursor.com', 'https://discord.gg/cursor', '@cursor_ai', 'freemium', '[]', '[\"Code Generation\",\"Full-Stack Refactoring\",\"Terminal Automation\",\"Bug Fixing\"]', 1, 14, '$20', 'monthly', 'TECHSCOUT20', '20% OFF First 3 Months', '[\"Multi-file repository vector indexing\",\"Terminal command execution with auto-repair\",\"Claude 3.5 Sonnet & GPT-4o native support\",\"Strict Privacy Mode with zero data retention\"]', '[\"macOS Desktop\",\"Windows Desktop\",\"Linux Desktop\",\"VS Code Extension\"]', 1, 'https://github.com/getcursor/cursor', 'Proprietary Core with Open Source Extensions', '[\"GitHub Copilot\",\"Windsurf\",\"Cline\",\"Void\",\"Continue.dev\"]', 'Unlike inline-only completion tools, Cursor executes multi-file architectural changes directly from chat prompts with instant diff inspection.', 'TechScout Editorial Gold Award: The most impactful development tool of 2026. Vector indexing and Sonnet 3.5 integration reduce boilerplate by 60%.', '[\"Instant repository-wide context retrieval\",\"Unbeatable multi-file code diff engine\",\"Generous monthly fast-request quota\"]', '[\"Requires dedicated IDE download\",\"High memory usage on massive 50k+ file monorepos\"]', 1, 4.95, 0, 4.90, 4.80, 4.95, 4.90, 4.70, '[\"https:\\/\\/images.unsplash.com\\/photo-1618005182384-a83a8bd57fbe\",\"https:\\/\\/images.unsplash.com\\/photo-1526374965328-7f61d4dc18c5\"]', 'https://partner.cursor.com/?via=techscout', 'cursor-ai-pro', '25% Lifetime Recurring');

--
-- Structure for table `analytics_events`
--
DROP TABLE IF EXISTS `analytics_events`;
CREATE TABLE `analytics_events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_time` datetime NOT NULL DEFAULT current_timestamp(),
  `event_type` varchar(50) NOT NULL,
  `content_id` bigint(20) unsigned DEFAULT NULL,
  `author_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `referrer_domain` varchar(150) DEFAULT NULL,
  `device_type` enum('desktop','mobile','tablet','bot') NOT NULL DEFAULT 'desktop',
  `duration_seconds` int(10) unsigned DEFAULT 0,
  `metadata_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata_json`)),
  PRIMARY KEY (`id`),
  KEY `idx_analytics_type_time` (`event_type`,`event_time`),
  KEY `idx_analytics_lookup` (`event_time`,`event_type`,`content_id`)
) ENGINE=InnoDB AUTO_INCREMENT=986 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `analytics_events` (985 rows)
--
INSERT INTO `analytics_events` (`id`, `event_time`, `event_type`, `content_id`, `author_id`, `user_id`, `referrer_domain`, `device_type`, `duration_seconds`, `metadata_json`) VALUES 
(1, '2026-09-04 21:57:56', 'view', 8, 1, NULL, 'direct', 'desktop', 0, NULL),
(2, '2026-09-04 21:57:56', 'view', 23, 2, NULL, 'direct', 'desktop', 0, NULL),
(3, '2026-09-04 21:57:57', 'view', 16, 2, NULL, 'direct', 'desktop', 0, NULL),
(4, '2026-09-04 21:57:57', 'view', 30, 1, NULL, 'direct', 'desktop', 0, NULL),
(5, '2026-09-04 22:02:07', 'view', 8, 1, NULL, 'direct', 'desktop', 0, NULL),
(6, '2026-09-04 22:02:08', 'view', 23, 2, NULL, 'direct', 'desktop', 0, NULL),
(7, '2026-09-04 22:02:08', 'view', 16, 2, NULL, 'direct', 'desktop', 0, NULL),
(8, '2026-09-04 22:02:09', 'view', 30, 1, NULL, 'direct', 'desktop', 0, NULL),
(9, '2026-09-04 22:02:14', 'view', 8, 1, NULL, 'direct', 'desktop', 0, NULL),
(10, '2026-09-04 22:02:15', 'view', 23, 2, NULL, 'direct', 'desktop', 0, NULL),
(11, '2026-09-04 22:02:15', 'view', 16, 2, NULL, 'direct', 'desktop', 0, NULL),
(12, '2026-09-04 22:02:15', 'view', 30, 1, NULL, 'direct', 'desktop', 0, NULL),
(13, '2026-09-04 22:33:58', 'view', 42, 1, 11, 'localhost', 'desktop', 0, NULL),
(14, '2026-09-04 22:54:37', 'view', 31, 1, 11, 'localhost', 'desktop', 0, NULL),
(15, '2026-09-03 00:12:31', 'read_10s', 2, 3, NULL, 'google.com', 'desktop', 43, NULL),
(16, '2026-08-14 17:12:31', 'read_10s', 2, 3, NULL, 'google.com', 'mobile', 130, NULL),
(17, '2026-08-22 14:12:31', 'read_10s', 2, 3, NULL, 'x.com', 'mobile', 23, NULL),
(18, '2026-08-15 06:12:31', 'read_10s', 2, 3, NULL, 'github.com', 'desktop', 111, NULL),
(19, '2026-08-26 21:12:31', 'read_10s', 2, 3, NULL, 'google.com', 'mobile', 121, NULL),
(20, '2026-08-31 13:12:31', 'read_10s', 2, 3, NULL, 'github.com', 'desktop', 164, NULL),
(21, '2026-08-26 21:12:31', 'read_10s', 2, 3, NULL, 'x.com', 'desktop', 166, NULL),
(22, '2026-08-22 14:12:31', 'read_10s', 2, 3, NULL, 'news.ycombinator.com', 'tablet', 152, NULL),
(23, '2026-08-23 19:12:31', 'read_10s', 2, 3, NULL, 'google.com', 'desktop', 178, NULL),
(24, '2026-08-17 13:12:31', 'view', 2, 3, NULL, 'x.com', 'mobile', 9, NULL),
(25, '2026-08-09 00:12:31', 'read_10s', 2, 3, NULL, 'google.com', 'mobile', 79, NULL),
(26, '2026-08-18 23:12:31', 'read_10s', 2, 3, NULL, 'x.com', 'desktop', 13, NULL),
(27, '2026-08-13 03:12:31', 'read_10s', 2, 3, NULL, 'news.ycombinator.com', 'desktop', 89, NULL),
(28, '2026-08-21 15:12:31', 'read_10s', 2, 3, NULL, 'github.com', 'mobile', 45, NULL),
(29, '2026-09-01 23:12:31', 'read_10s', 2, 3, NULL, 'x.com', 'desktop', 81, NULL),
(30, '2026-08-20 07:12:31', 'read_10s', 2, 3, NULL, 'news.ycombinator.com', 'mobile', 45, NULL),
(31, '2026-08-20 14:12:31', 'read_10s', 2, 3, NULL, 'google.com', 'desktop', 108, NULL),
(32, '2026-08-15 17:12:31', 'read_10s', 2, 3, NULL, 'direct', 'tablet', 148, NULL),
(33, '2026-08-14 10:12:31', 'read_10s', 2, 3, NULL, 'direct', 'desktop', 26, NULL),
(34, '2026-08-23 10:12:31', 'read_10s', 2, 3, NULL, 'github.com', 'desktop', 74, NULL),
(35, '2026-08-12 20:12:31', 'read_10s', 2, 3, NULL, 'github.com', 'desktop', 44, NULL),
(36, '2026-08-19 06:12:31', 'read_10s', 2, 3, NULL, 'x.com', 'desktop', 18, NULL),
(37, '2026-08-12 15:12:31', 'read_10s', 2, 3, NULL, 'google.com', 'desktop', 86, NULL),
(38, '2026-08-13 18:12:31', 'read_10s', 2, 3, NULL, 'github.com', 'desktop', 63, NULL),
(39, '2026-08-27 20:12:31', 'read_10s', 2, 3, NULL, 'news.ycombinator.com', 'desktop', 79, NULL),
(40, '2026-08-17 06:12:31', 'read_10s', 3, 4, NULL, 'google.com', 'mobile', 36, NULL),
(41, '2026-08-14 00:12:31', 'read_10s', 3, 4, NULL, 'x.com', 'mobile', 156, NULL),
(42, '2026-08-18 22:12:31', 'read_10s', 3, 4, NULL, 'github.com', 'desktop', 101, NULL),
(43, '2026-08-18 22:12:31', 'read_10s', 3, 4, NULL, 'x.com', 'desktop', 82, NULL),
(44, '2026-08-31 04:12:31', 'read_10s', 3, 4, NULL, 'direct', 'tablet', 145, NULL),
(45, '2026-08-19 09:12:31', 'read_10s', 3, 4, NULL, 'x.com', 'desktop', 170, NULL),
(46, '2026-08-12 17:12:31', 'read_10s', 3, 4, NULL, 'x.com', 'desktop', 178, NULL),
(47, '2026-08-16 12:12:31', 'read_10s', 3, 4, NULL, 'direct', 'mobile', 61, NULL),
(48, '2026-08-20 06:12:31', 'read_10s', 3, 4, NULL, 'google.com', 'desktop', 43, NULL),
(49, '2026-08-27 23:12:31', 'read_10s', 3, 4, NULL, 'direct', 'mobile', 98, NULL),
(50, '2026-08-25 03:12:31', 'read_10s', 3, 4, NULL, 'direct', 'desktop', 48, NULL);
INSERT INTO `analytics_events` (`id`, `event_time`, `event_type`, `content_id`, `author_id`, `user_id`, `referrer_domain`, `device_type`, `duration_seconds`, `metadata_json`) VALUES 
(51, '2026-08-22 23:12:31', 'read_10s', 3, 4, NULL, 'github.com', 'desktop', 149, NULL),
(52, '2026-08-11 03:12:31', 'read_10s', 3, 4, NULL, 'x.com', 'desktop', 59, NULL),
(53, '2026-08-12 18:12:31', 'read_10s', 3, 4, NULL, 'google.com', 'mobile', 65, NULL),
(54, '2026-08-21 23:12:31', 'read_10s', 3, 4, NULL, 'x.com', 'tablet', 155, NULL),
(55, '2026-09-02 09:12:31', 'read_10s', 3, 4, NULL, 'direct', 'tablet', 148, NULL),
(56, '2026-08-22 11:12:31', 'read_10s', 3, 4, NULL, 'direct', 'desktop', 45, NULL),
(57, '2026-08-19 06:12:31', 'read_10s', 3, 4, NULL, 'news.ycombinator.com', 'desktop', 104, NULL),
(58, '2026-08-27 18:12:31', 'read_10s', 3, 4, NULL, 'direct', 'mobile', 50, NULL),
(59, '2026-08-23 23:12:31', 'read_10s', 3, 4, NULL, 'google.com', 'mobile', 63, NULL),
(60, '2026-09-02 06:12:31', 'read_10s', 3, 4, NULL, 'google.com', 'tablet', 155, NULL),
(61, '2026-08-30 11:12:31', 'read_10s', 3, 4, NULL, 'google.com', 'tablet', 76, NULL),
(62, '2026-09-04 20:12:31', 'read_10s', 3, 4, NULL, 'x.com', 'desktop', 98, NULL),
(63, '2026-08-18 05:12:31', 'read_10s', 3, 4, NULL, 'x.com', 'tablet', 49, NULL),
(64, '2026-08-07 19:12:31', 'read_10s', 3, 4, NULL, 'github.com', 'tablet', 92, NULL),
(65, '2026-08-23 04:12:31', 'read_10s', 4, 6, NULL, 'x.com', 'mobile', 32, NULL),
(66, '2026-08-16 03:12:31', 'read_10s', 4, 6, NULL, 'x.com', 'mobile', 20, NULL),
(67, '2026-09-02 10:12:31', 'read_10s', 4, 6, NULL, 'direct', 'desktop', 83, NULL),
(68, '2026-08-21 06:12:31', 'read_10s', 4, 6, NULL, 'x.com', 'tablet', 167, NULL),
(69, '2026-08-28 23:12:31', 'read_10s', 4, 6, NULL, 'github.com', 'mobile', 32, NULL),
(70, '2026-08-27 14:12:31', 'read_10s', 4, 6, NULL, 'google.com', 'desktop', 155, NULL),
(71, '2026-09-03 06:12:31', 'read_10s', 4, 6, NULL, 'direct', 'desktop', 125, NULL),
(72, '2026-08-16 19:12:31', 'read_10s', 4, 6, NULL, 'direct', 'desktop', 136, NULL),
(73, '2026-08-27 16:12:31', 'read_10s', 4, 6, NULL, 'google.com', 'tablet', 132, NULL),
(74, '2026-08-28 16:12:31', 'read_10s', 4, 6, NULL, 'x.com', 'desktop', 118, NULL),
(75, '2026-08-27 09:12:31', 'read_10s', 4, 6, NULL, 'google.com', 'desktop', 10, NULL),
(76, '2026-08-09 01:12:31', 'read_10s', 4, 6, NULL, 'google.com', 'mobile', 118, NULL),
(77, '2026-08-15 15:12:31', 'read_10s', 4, 6, NULL, 'direct', 'desktop', 111, NULL),
(78, '2026-08-09 20:12:31', 'read_10s', 4, 6, NULL, 'direct', 'tablet', 63, NULL),
(79, '2026-08-08 22:12:31', 'read_10s', 4, 6, NULL, 'direct', 'tablet', 15, NULL),
(80, '2026-08-18 18:12:31', 'read_10s', 4, 6, NULL, 'direct', 'mobile', 172, NULL),
(81, '2026-08-20 12:12:31', 'read_10s', 4, 6, NULL, 'google.com', 'desktop', 166, NULL),
(82, '2026-08-20 18:12:31', 'read_10s', 4, 6, NULL, 'github.com', 'mobile', 106, NULL),
(83, '2026-08-29 23:12:31', 'read_10s', 4, 6, NULL, 'google.com', 'mobile', 82, NULL),
(84, '2026-08-10 18:12:31', 'read_10s', 4, 6, NULL, 'github.com', 'desktop', 140, NULL),
(85, '2026-08-11 01:12:31', 'read_10s', 4, 6, NULL, 'direct', 'desktop', 106, NULL),
(86, '2026-08-28 17:12:31', 'read_10s', 4, 6, NULL, 'google.com', 'mobile', 63, NULL),
(87, '2026-08-23 20:12:31', 'read_10s', 4, 6, NULL, 'google.com', 'mobile', 91, NULL),
(88, '2026-09-02 10:12:31', 'read_10s', 4, 6, NULL, 'google.com', 'mobile', 146, NULL),
(89, '2026-08-14 01:12:31', 'read_10s', 4, 6, NULL, 'github.com', 'mobile', 170, NULL),
(90, '2026-08-21 13:12:31', 'read_10s', 5, 5, NULL, 'github.com', 'desktop', 180, NULL),
(91, '2026-08-24 10:12:31', 'read_10s', 5, 5, NULL, 'x.com', 'tablet', 27, NULL),
(92, '2026-09-04 19:12:31', 'read_10s', 5, 5, NULL, 'direct', 'mobile', 147, NULL),
(93, '2026-08-08 22:12:31', 'read_10s', 5, 5, NULL, 'direct', 'desktop', 109, NULL),
(94, '2026-08-22 11:12:31', 'read_10s', 5, 5, NULL, 'x.com', 'desktop', 77, NULL),
(95, '2026-08-25 09:12:31', 'read_10s', 5, 5, NULL, 'news.ycombinator.com', 'mobile', 28, NULL),
(96, '2026-08-31 16:12:31', 'read_10s', 5, 5, NULL, 'direct', 'desktop', 23, NULL),
(97, '2026-08-16 19:12:31', 'read_10s', 5, 5, NULL, 'google.com', 'tablet', 95, NULL),
(98, '2026-08-20 02:12:31', 'read_10s', 5, 5, NULL, 'direct', 'mobile', 98, NULL),
(99, '2026-08-17 19:12:31', 'read_10s', 5, 5, NULL, 'google.com', 'mobile', 145, NULL),
(100, '2026-08-30 18:12:31', 'read_10s', 5, 5, NULL, 'direct', 'mobile', 103, NULL);
INSERT INTO `analytics_events` (`id`, `event_time`, `event_type`, `content_id`, `author_id`, `user_id`, `referrer_domain`, `device_type`, `duration_seconds`, `metadata_json`) VALUES 
(101, '2026-08-12 03:12:31', 'read_10s', 5, 5, NULL, 'google.com', 'desktop', 149, NULL),
(102, '2026-08-20 13:12:31', 'read_10s', 5, 5, NULL, 'x.com', 'desktop', 11, NULL),
(103, '2026-08-17 16:12:31', 'read_10s', 5, 5, NULL, 'google.com', 'mobile', 96, NULL),
(104, '2026-08-15 00:12:31', 'read_10s', 5, 5, NULL, 'direct', 'desktop', 98, NULL),
(105, '2026-08-24 10:12:31', 'read_10s', 5, 5, NULL, 'news.ycombinator.com', 'desktop', 177, NULL),
(106, '2026-08-17 11:12:31', 'read_10s', 5, 5, NULL, 'x.com', 'desktop', 71, NULL),
(107, '2026-09-03 19:12:31', 'read_10s', 5, 5, NULL, 'google.com', 'desktop', 151, NULL),
(108, '2026-08-18 11:12:31', 'read_10s', 5, 5, NULL, 'google.com', 'desktop', 50, NULL),
(109, '2026-08-29 18:12:31', 'read_10s', 5, 5, NULL, 'direct', 'tablet', 98, NULL),
(110, '2026-08-11 00:12:31', 'read_10s', 5, 5, NULL, 'direct', 'desktop', 53, NULL),
(111, '2026-08-27 23:12:31', 'read_10s', 5, 5, NULL, 'google.com', 'desktop', 121, NULL),
(112, '2026-09-04 05:12:31', 'read_10s', 5, 5, NULL, 'google.com', 'desktop', 22, NULL),
(113, '2026-08-09 00:12:31', 'read_10s', 5, 5, NULL, 'github.com', 'desktop', 161, NULL),
(114, '2026-09-04 02:12:31', 'read_10s', 5, 5, NULL, 'google.com', 'tablet', 47, NULL),
(115, '2026-08-08 12:12:31', 'read_10s', 6, 7, NULL, 'google.com', 'desktop', 122, NULL),
(116, '2026-08-21 18:12:31', 'read_10s', 6, 7, NULL, 'direct', 'desktop', 130, NULL),
(117, '2026-08-24 03:12:31', 'read_10s', 6, 7, NULL, 'x.com', 'tablet', 61, NULL),
(118, '2026-09-02 07:12:31', 'read_10s', 6, 7, NULL, 'x.com', 'desktop', 95, NULL),
(119, '2026-08-13 08:12:31', 'read_10s', 6, 7, NULL, 'direct', 'desktop', 62, NULL),
(120, '2026-08-16 02:12:31', 'read_10s', 6, 7, NULL, 'x.com', 'desktop', 21, NULL),
(121, '2026-09-01 20:12:31', 'read_10s', 6, 7, NULL, 'direct', 'desktop', 164, NULL),
(122, '2026-08-18 21:12:31', 'read_10s', 6, 7, NULL, 'google.com', 'desktop', 51, NULL),
(123, '2026-08-19 20:12:31', 'read_10s', 6, 7, NULL, 'google.com', 'desktop', 145, NULL),
(124, '2026-08-14 04:12:31', 'read_10s', 6, 7, NULL, 'direct', 'mobile', 56, NULL),
(125, '2026-08-30 22:12:31', 'read_10s', 6, 7, NULL, 'google.com', 'tablet', 102, NULL),
(126, '2026-08-28 13:12:31', 'read_10s', 6, 7, NULL, 'x.com', 'mobile', 86, NULL),
(127, '2026-08-18 19:12:31', 'read_10s', 6, 7, NULL, 'github.com', 'desktop', 73, NULL),
(128, '2026-08-17 17:12:31', 'view', 6, 7, NULL, 'google.com', 'desktop', 9, NULL),
(129, '2026-08-10 15:12:31', 'read_10s', 6, 7, NULL, 'github.com', 'desktop', 51, NULL),
(130, '2026-08-27 01:12:31', 'view', 6, 7, NULL, 'google.com', 'desktop', 9, NULL),
(131, '2026-08-19 20:12:31', 'read_10s', 6, 7, NULL, 'x.com', 'desktop', 124, NULL),
(132, '2026-08-23 07:12:31', 'read_10s', 6, 7, NULL, 'github.com', 'mobile', 129, NULL),
(133, '2026-08-25 08:12:31', 'read_10s', 6, 7, NULL, 'x.com', 'mobile', 21, NULL),
(134, '2026-09-02 16:12:31', 'read_10s', 6, 7, NULL, 'x.com', 'mobile', 67, NULL),
(135, '2026-08-29 00:12:31', 'read_10s', 6, 7, NULL, 'google.com', 'desktop', 87, NULL),
(136, '2026-08-18 19:12:31', 'read_10s', 6, 7, NULL, 'direct', 'desktop', 58, NULL),
(137, '2026-08-25 04:12:31', 'read_10s', 6, 7, NULL, 'google.com', 'desktop', 159, NULL),
(138, '2026-08-26 03:12:31', 'read_10s', 6, 7, NULL, 'github.com', 'desktop', 73, NULL),
(139, '2026-08-31 17:12:31', 'read_10s', 6, 7, NULL, 'x.com', 'desktop', 145, NULL),
(140, '2026-09-03 04:12:31', 'read_10s', 7, 5, NULL, 'github.com', 'desktop', 129, NULL),
(141, '2026-08-13 20:12:31', 'read_10s', 7, 5, NULL, 'direct', 'tablet', 145, NULL),
(142, '2026-08-16 21:12:31', 'read_10s', 7, 5, NULL, 'google.com', 'desktop', 37, NULL),
(143, '2026-08-24 10:12:31', 'read_10s', 7, 5, NULL, 'google.com', 'tablet', 95, NULL),
(144, '2026-08-18 05:12:31', 'read_10s', 7, 5, NULL, 'github.com', 'tablet', 132, NULL),
(145, '2026-08-16 00:12:31', 'read_10s', 7, 5, NULL, 'google.com', 'tablet', 26, NULL),
(146, '2026-08-25 01:12:31', 'read_10s', 7, 5, NULL, 'github.com', 'desktop', 59, NULL),
(147, '2026-08-29 09:12:31', 'read_10s', 7, 5, NULL, 'google.com', 'mobile', 63, NULL),
(148, '2026-08-12 23:12:31', 'read_10s', 7, 5, NULL, 'google.com', 'desktop', 117, NULL),
(149, '2026-08-08 02:12:31', 'read_10s', 7, 5, NULL, 'x.com', 'desktop', 76, NULL),
(150, '2026-08-20 12:12:31', 'read_10s', 7, 5, NULL, 'github.com', 'desktop', 150, NULL);
INSERT INTO `analytics_events` (`id`, `event_time`, `event_type`, `content_id`, `author_id`, `user_id`, `referrer_domain`, `device_type`, `duration_seconds`, `metadata_json`) VALUES 
(151, '2026-08-28 06:12:31', 'read_10s', 7, 5, NULL, 'x.com', 'desktop', 74, NULL),
(152, '2026-08-28 08:12:31', 'read_10s', 7, 5, NULL, 'direct', 'mobile', 57, NULL),
(153, '2026-09-02 04:12:31', 'read_10s', 7, 5, NULL, 'x.com', 'desktop', 159, NULL),
(154, '2026-08-30 10:12:31', 'read_10s', 7, 5, NULL, 'github.com', 'tablet', 178, NULL),
(155, '2026-08-12 10:12:31', 'read_10s', 7, 5, NULL, 'x.com', 'desktop', 167, NULL),
(156, '2026-08-28 10:12:31', 'read_10s', 7, 5, NULL, 'google.com', 'desktop', 103, NULL),
(157, '2026-08-17 10:12:31', 'read_10s', 7, 5, NULL, 'google.com', 'desktop', 32, NULL),
(158, '2026-08-30 22:12:31', 'read_10s', 7, 5, NULL, 'x.com', 'desktop', 68, NULL),
(159, '2026-08-24 21:12:31', 'read_10s', 7, 5, NULL, 'google.com', 'desktop', 52, NULL),
(160, '2026-08-24 09:12:31', 'read_10s', 7, 5, NULL, 'github.com', 'desktop', 12, NULL),
(161, '2026-08-14 22:12:31', 'read_10s', 7, 5, NULL, 'x.com', 'mobile', 165, NULL),
(162, '2026-08-14 23:12:31', 'read_10s', 7, 5, NULL, 'github.com', 'desktop', 179, NULL),
(163, '2026-08-24 17:12:31', 'read_10s', 7, 5, NULL, 'x.com', 'desktop', 108, NULL),
(164, '2026-09-02 07:12:31', 'read_10s', 7, 5, NULL, 'news.ycombinator.com', 'tablet', 106, NULL),
(165, '2026-09-03 16:12:31', 'read_10s', 8, 1, NULL, 'google.com', 'mobile', 112, NULL),
(166, '2026-08-16 14:12:31', 'read_10s', 8, 1, NULL, 'github.com', 'desktop', 64, NULL),
(167, '2026-08-12 14:12:31', 'read_10s', 8, 1, NULL, 'google.com', 'mobile', 179, NULL),
(168, '2026-08-14 12:12:31', 'read_10s', 8, 1, NULL, 'google.com', 'tablet', 99, NULL),
(169, '2026-08-23 19:12:31', 'read_10s', 8, 1, NULL, 'google.com', 'mobile', 37, NULL),
(170, '2026-08-13 02:12:31', 'read_10s', 8, 1, NULL, 'github.com', 'desktop', 161, NULL),
(171, '2026-08-14 18:12:31', 'read_10s', 8, 1, NULL, 'google.com', 'desktop', 58, NULL),
(172, '2026-08-18 09:12:31', 'read_10s', 8, 1, NULL, 'x.com', 'mobile', 10, NULL),
(173, '2026-08-16 21:12:31', 'read_10s', 8, 1, NULL, 'news.ycombinator.com', 'desktop', 179, NULL),
(174, '2026-08-30 10:12:31', 'read_10s', 8, 1, NULL, 'github.com', 'desktop', 81, NULL),
(175, '2026-08-23 01:12:31', 'read_10s', 8, 1, NULL, 'news.ycombinator.com', 'mobile', 180, NULL),
(176, '2026-08-08 21:12:31', 'read_10s', 8, 1, NULL, 'google.com', 'desktop', 106, NULL),
(177, '2026-09-04 17:12:31', 'read_10s', 8, 1, NULL, 'github.com', 'desktop', 149, NULL),
(178, '2026-09-02 19:12:31', 'read_10s', 8, 1, NULL, 'x.com', 'desktop', 155, NULL),
(179, '2026-08-31 17:12:31', 'read_10s', 8, 1, NULL, 'github.com', 'tablet', 15, NULL),
(180, '2026-09-02 03:12:31', 'read_10s', 8, 1, NULL, 'direct', 'desktop', 95, NULL),
(181, '2026-08-19 04:12:31', 'read_10s', 8, 1, NULL, 'direct', 'tablet', 47, NULL),
(182, '2026-08-20 09:12:31', 'read_10s', 8, 1, NULL, 'google.com', 'desktop', 168, NULL),
(183, '2026-08-08 00:12:31', 'read_10s', 8, 1, NULL, 'direct', 'desktop', 70, NULL),
(184, '2026-08-16 12:12:31', 'read_10s', 8, 1, NULL, 'google.com', 'mobile', 159, NULL),
(185, '2026-09-04 19:12:31', 'read_10s', 8, 1, NULL, 'github.com', 'desktop', 61, NULL),
(186, '2026-08-14 21:12:31', 'read_10s', 8, 1, NULL, 'google.com', 'mobile', 22, NULL),
(187, '2026-08-26 13:12:31', 'read_10s', 8, 1, NULL, 'news.ycombinator.com', 'tablet', 139, NULL),
(188, '2026-08-31 10:12:31', 'read_10s', 8, 1, NULL, 'news.ycombinator.com', 'desktop', 119, NULL),
(189, '2026-09-02 14:12:31', 'read_10s', 8, 1, NULL, 'x.com', 'desktop', 163, NULL),
(190, '2026-08-18 02:12:31', 'read_10s', 9, 1, NULL, 'github.com', 'desktop', 58, NULL),
(191, '2026-08-15 20:12:31', 'read_10s', 9, 1, NULL, 'github.com', 'tablet', 164, NULL),
(192, '2026-08-12 13:12:31', 'read_10s', 9, 1, NULL, 'news.ycombinator.com', 'desktop', 84, NULL),
(193, '2026-08-19 04:12:31', 'read_10s', 9, 1, NULL, 'google.com', 'mobile', 132, NULL),
(194, '2026-08-26 20:12:31', 'read_10s', 9, 1, NULL, 'google.com', 'mobile', 26, NULL),
(195, '2026-08-09 19:12:31', 'read_10s', 9, 1, NULL, 'x.com', 'mobile', 145, NULL),
(196, '2026-09-04 15:12:31', 'read_10s', 9, 1, NULL, 'x.com', 'desktop', 41, NULL),
(197, '2026-08-19 16:12:31', 'read_10s', 9, 1, NULL, 'google.com', 'desktop', 17, NULL),
(198, '2026-08-24 10:12:31', 'read_10s', 9, 1, NULL, 'direct', 'desktop', 139, NULL),
(199, '2026-08-18 16:12:31', 'read_10s', 9, 1, NULL, 'direct', 'desktop', 22, NULL),
(200, '2026-09-02 01:12:31', 'read_10s', 9, 1, NULL, 'google.com', 'desktop', 43, NULL);
INSERT INTO `analytics_events` (`id`, `event_time`, `event_type`, `content_id`, `author_id`, `user_id`, `referrer_domain`, `device_type`, `duration_seconds`, `metadata_json`) VALUES 
(201, '2026-08-24 12:12:31', 'read_10s', 9, 1, NULL, 'x.com', 'desktop', 179, NULL),
(202, '2026-08-11 11:12:31', 'read_10s', 9, 1, NULL, 'google.com', 'desktop', 13, NULL),
(203, '2026-09-01 13:12:31', 'read_10s', 9, 1, NULL, 'google.com', 'mobile', 58, NULL),
(204, '2026-08-14 20:12:31', 'read_10s', 9, 1, NULL, 'google.com', 'desktop', 108, NULL),
(205, '2026-08-30 23:12:31', 'read_10s', 9, 1, NULL, 'github.com', 'mobile', 166, NULL),
(206, '2026-08-27 16:12:31', 'read_10s', 9, 1, NULL, 'github.com', 'tablet', 110, NULL),
(207, '2026-08-26 17:12:31', 'read_10s', 9, 1, NULL, 'direct', 'desktop', 93, NULL),
(208, '2026-09-01 07:12:31', 'read_10s', 9, 1, NULL, 'github.com', 'desktop', 97, NULL),
(209, '2026-08-07 16:12:31', 'read_10s', 9, 1, NULL, 'x.com', 'desktop', 177, NULL),
(210, '2026-08-24 07:12:31', 'read_10s', 9, 1, NULL, 'github.com', 'desktop', 133, NULL),
(211, '2026-08-22 14:12:31', 'read_10s', 9, 1, NULL, 'direct', 'mobile', 48, NULL),
(212, '2026-08-12 06:12:31', 'read_10s', 9, 1, NULL, 'google.com', 'desktop', 162, NULL),
(213, '2026-08-22 08:12:31', 'read_10s', 9, 1, NULL, 'google.com', 'mobile', 98, NULL),
(214, '2026-08-29 13:12:31', 'read_10s', 9, 1, NULL, 'google.com', 'desktop', 160, NULL),
(215, '2026-08-24 10:12:31', 'read_10s', 10, 1, NULL, 'google.com', 'desktop', 115, NULL),
(216, '2026-08-29 14:12:31', 'read_10s', 10, 1, NULL, 'github.com', 'desktop', 77, NULL),
(217, '2026-08-30 17:12:31', 'read_10s', 10, 1, NULL, 'github.com', 'desktop', 45, NULL),
(218, '2026-08-13 19:12:31', 'read_10s', 10, 1, NULL, 'x.com', 'tablet', 92, NULL),
(219, '2026-08-29 09:12:31', 'read_10s', 10, 1, NULL, 'github.com', 'mobile', 56, NULL),
(220, '2026-08-25 08:12:31', 'read_10s', 10, 1, NULL, 'news.ycombinator.com', 'desktop', 35, NULL),
(221, '2026-08-07 05:12:31', 'read_10s', 10, 1, NULL, 'google.com', 'desktop', 68, NULL),
(222, '2026-08-29 09:12:31', 'read_10s', 10, 1, NULL, 'x.com', 'desktop', 179, NULL),
(223, '2026-08-13 03:12:31', 'read_10s', 10, 1, NULL, 'direct', 'desktop', 82, NULL),
(224, '2026-08-27 08:12:31', 'read_10s', 10, 1, NULL, 'x.com', 'desktop', 132, NULL),
(225, '2026-08-26 04:12:31', 'read_10s', 10, 1, NULL, 'news.ycombinator.com', 'desktop', 20, NULL),
(226, '2026-08-21 14:12:31', 'read_10s', 10, 1, NULL, 'github.com', 'mobile', 159, NULL),
(227, '2026-08-12 12:12:31', 'read_10s', 10, 1, NULL, 'github.com', 'desktop', 119, NULL),
(228, '2026-08-07 16:12:31', 'read_10s', 10, 1, NULL, 'x.com', 'desktop', 12, NULL),
(229, '2026-09-01 14:12:31', 'read_10s', 10, 1, NULL, 'direct', 'mobile', 97, NULL),
(230, '2026-08-31 09:12:31', 'read_10s', 10, 1, NULL, 'github.com', 'desktop', 131, NULL),
(231, '2026-08-16 22:12:31', 'read_10s', 10, 1, NULL, 'github.com', 'tablet', 113, NULL),
(232, '2026-08-19 21:12:31', 'read_10s', 10, 1, NULL, 'google.com', 'desktop', 19, NULL),
(233, '2026-09-03 08:12:31', 'read_10s', 10, 1, NULL, 'google.com', 'desktop', 168, NULL),
(234, '2026-08-24 03:12:31', 'read_10s', 10, 1, NULL, 'github.com', 'desktop', 112, NULL),
(235, '2026-08-21 02:12:31', 'read_10s', 10, 1, NULL, 'x.com', 'desktop', 171, NULL),
(236, '2026-08-17 04:12:31', 'read_10s', 10, 1, NULL, 'direct', 'desktop', 163, NULL),
(237, '2026-08-30 06:12:31', 'read_10s', 10, 1, NULL, 'direct', 'desktop', 167, NULL),
(238, '2026-08-20 08:12:31', 'read_10s', 10, 1, NULL, 'google.com', 'desktop', 48, NULL),
(239, '2026-08-09 08:12:31', 'read_10s', 10, 1, NULL, 'direct', 'desktop', 87, NULL),
(240, '2026-08-09 03:12:31', 'read_10s', 11, 1, NULL, 'x.com', 'tablet', 118, NULL),
(241, '2026-08-24 10:12:31', 'read_10s', 11, 1, NULL, 'google.com', 'mobile', 153, NULL),
(242, '2026-08-09 00:12:31', 'read_10s', 11, 1, NULL, 'x.com', 'desktop', 86, NULL),
(243, '2026-08-07 04:12:31', 'read_10s', 11, 1, NULL, 'github.com', 'mobile', 115, NULL),
(244, '2026-08-15 06:12:31', 'read_10s', 11, 1, NULL, 'google.com', 'mobile', 86, NULL),
(245, '2026-08-31 09:12:31', 'read_10s', 11, 1, NULL, 'news.ycombinator.com', 'desktop', 111, NULL),
(246, '2026-08-14 19:12:31', 'read_10s', 11, 1, NULL, 'google.com', 'desktop', 63, NULL),
(247, '2026-08-07 21:12:31', 'read_10s', 11, 1, NULL, 'x.com', 'desktop', 38, NULL),
(248, '2026-09-01 16:12:31', 'read_10s', 11, 1, NULL, 'direct', 'desktop', 158, NULL),
(249, '2026-08-16 15:12:31', 'read_10s', 11, 1, NULL, 'direct', 'desktop', 146, NULL),
(250, '2026-08-14 09:12:31', 'read_10s', 11, 1, NULL, 'github.com', 'desktop', 114, NULL);
INSERT INTO `analytics_events` (`id`, `event_time`, `event_type`, `content_id`, `author_id`, `user_id`, `referrer_domain`, `device_type`, `duration_seconds`, `metadata_json`) VALUES 
(251, '2026-08-13 19:12:31', 'read_10s', 11, 1, NULL, 'x.com', 'desktop', 105, NULL),
(252, '2026-08-08 20:12:31', 'read_10s', 11, 1, NULL, 'news.ycombinator.com', 'desktop', 101, NULL),
(253, '2026-08-20 11:12:31', 'read_10s', 11, 1, NULL, 'google.com', 'desktop', 76, NULL),
(254, '2026-08-12 20:12:31', 'read_10s', 11, 1, NULL, 'x.com', 'desktop', 147, NULL),
(255, '2026-08-22 02:12:31', 'read_10s', 11, 1, NULL, 'x.com', 'tablet', 25, NULL),
(256, '2026-08-08 00:12:31', 'read_10s', 11, 1, NULL, 'google.com', 'mobile', 115, NULL),
(257, '2026-08-10 14:12:31', 'read_10s', 11, 1, NULL, 'direct', 'desktop', 178, NULL),
(258, '2026-08-07 05:12:31', 'read_10s', 11, 1, NULL, 'google.com', 'mobile', 29, NULL),
(259, '2026-09-01 02:12:31', 'read_10s', 11, 1, NULL, 'github.com', 'desktop', 53, NULL),
(260, '2026-09-01 09:12:31', 'read_10s', 11, 1, NULL, 'github.com', 'tablet', 177, NULL),
(261, '2026-08-16 19:12:31', 'read_10s', 11, 1, NULL, 'github.com', 'desktop', 79, NULL),
(262, '2026-08-07 07:12:31', 'read_10s', 11, 1, NULL, 'direct', 'desktop', 144, NULL),
(263, '2026-08-29 02:12:31', 'read_10s', 11, 1, NULL, 'google.com', 'mobile', 111, NULL),
(264, '2026-08-08 17:12:31', 'read_10s', 11, 1, NULL, 'github.com', 'desktop', 58, NULL),
(265, '2026-08-08 10:12:31', 'read_10s', 12, 1, NULL, 'news.ycombinator.com', 'desktop', 115, NULL),
(266, '2026-09-01 16:12:31', 'read_10s', 12, 1, NULL, 'github.com', 'mobile', 34, NULL),
(267, '2026-08-30 06:12:31', 'read_10s', 12, 1, NULL, 'x.com', 'tablet', 165, NULL),
(268, '2026-08-27 05:12:31', 'read_10s', 12, 1, NULL, 'direct', 'mobile', 30, NULL),
(269, '2026-08-12 06:12:31', 'read_10s', 12, 1, NULL, 'github.com', 'desktop', 149, NULL),
(270, '2026-08-24 17:12:31', 'read_10s', 12, 1, NULL, 'direct', 'mobile', 73, NULL),
(271, '2026-08-24 17:12:31', 'read_10s', 12, 1, NULL, 'github.com', 'mobile', 92, NULL),
(272, '2026-08-10 16:12:31', 'read_10s', 12, 1, NULL, 'direct', 'mobile', 66, NULL),
(273, '2026-08-16 10:12:31', 'read_10s', 12, 1, NULL, 'google.com', 'mobile', 35, NULL),
(274, '2026-08-17 07:12:31', 'read_10s', 12, 1, NULL, 'github.com', 'desktop', 163, NULL),
(275, '2026-08-12 04:12:31', 'read_10s', 12, 1, NULL, 'x.com', 'mobile', 65, NULL),
(276, '2026-09-03 14:12:31', 'read_10s', 12, 1, NULL, 'x.com', 'desktop', 160, NULL),
(277, '2026-08-20 03:12:31', 'read_10s', 12, 1, NULL, 'x.com', 'desktop', 141, NULL),
(278, '2026-08-26 07:12:31', 'read_10s', 12, 1, NULL, 'google.com', 'mobile', 138, NULL),
(279, '2026-08-14 15:12:31', 'read_10s', 12, 1, NULL, 'direct', 'desktop', 15, NULL),
(280, '2026-08-12 06:12:31', 'read_10s', 12, 1, NULL, 'news.ycombinator.com', 'desktop', 62, NULL),
(281, '2026-08-31 22:12:31', 'read_10s', 12, 1, NULL, 'direct', 'tablet', 90, NULL),
(282, '2026-08-29 01:12:31', 'read_10s', 12, 1, NULL, 'direct', 'desktop', 72, NULL),
(283, '2026-08-07 07:12:31', 'read_10s', 12, 1, NULL, 'github.com', 'desktop', 103, NULL),
(284, '2026-08-10 03:12:31', 'read_10s', 12, 1, NULL, 'google.com', 'mobile', 19, NULL),
(285, '2026-08-21 20:12:31', 'read_10s', 12, 1, NULL, 'x.com', 'desktop', 172, NULL),
(286, '2026-08-27 13:12:31', 'read_10s', 12, 1, NULL, 'direct', 'tablet', 68, NULL),
(287, '2026-08-27 15:12:31', 'read_10s', 12, 1, NULL, 'news.ycombinator.com', 'desktop', 114, NULL),
(288, '2026-08-10 10:12:31', 'read_10s', 12, 1, NULL, 'x.com', 'tablet', 170, NULL),
(289, '2026-08-24 09:12:31', 'read_10s', 12, 1, NULL, 'direct', 'desktop', 120, NULL),
(290, '2026-08-29 09:12:31', 'read_10s', 13, 1, NULL, 'github.com', 'mobile', 133, NULL),
(291, '2026-08-22 12:12:31', 'read_10s', 13, 1, NULL, 'direct', 'desktop', 143, NULL),
(292, '2026-08-31 18:12:31', 'read_10s', 13, 1, NULL, 'direct', 'desktop', 135, NULL),
(293, '2026-08-14 17:12:31', 'read_10s', 13, 1, NULL, 'github.com', 'desktop', 67, NULL),
(294, '2026-09-03 20:12:31', 'read_10s', 13, 1, NULL, 'google.com', 'desktop', 123, NULL),
(295, '2026-08-07 14:12:31', 'read_10s', 13, 1, NULL, 'x.com', 'mobile', 123, NULL),
(296, '2026-08-11 13:12:31', 'read_10s', 13, 1, NULL, 'google.com', 'tablet', 40, NULL),
(297, '2026-08-31 04:12:31', 'read_10s', 13, 1, NULL, 'x.com', 'mobile', 36, NULL),
(298, '2026-08-27 20:12:31', 'read_10s', 13, 1, NULL, 'direct', 'desktop', 66, NULL),
(299, '2026-08-21 20:12:31', 'read_10s', 13, 1, NULL, 'google.com', 'mobile', 112, NULL),
(300, '2026-08-12 17:12:31', 'read_10s', 13, 1, NULL, 'direct', 'desktop', 154, NULL);
INSERT INTO `analytics_events` (`id`, `event_time`, `event_type`, `content_id`, `author_id`, `user_id`, `referrer_domain`, `device_type`, `duration_seconds`, `metadata_json`) VALUES 
(301, '2026-08-10 00:12:31', 'read_10s', 13, 1, NULL, 'direct', 'desktop', 44, NULL),
(302, '2026-09-04 02:12:31', 'read_10s', 13, 1, NULL, 'direct', 'desktop', 127, NULL),
(303, '2026-08-21 03:12:31', 'read_10s', 13, 1, NULL, 'x.com', 'desktop', 16, NULL),
(304, '2026-08-13 16:12:31', 'read_10s', 13, 1, NULL, 'direct', 'desktop', 75, NULL),
(305, '2026-08-27 10:12:31', 'read_10s', 13, 1, NULL, 'google.com', 'desktop', 62, NULL),
(306, '2026-08-19 15:12:31', 'read_10s', 13, 1, NULL, 'google.com', 'mobile', 94, NULL),
(307, '2026-08-20 13:12:31', 'read_10s', 13, 1, NULL, 'x.com', 'mobile', 30, NULL),
(308, '2026-08-07 16:12:31', 'read_10s', 13, 1, NULL, 'x.com', 'desktop', 34, NULL),
(309, '2026-08-13 06:12:31', 'read_10s', 13, 1, NULL, 'github.com', 'desktop', 99, NULL),
(310, '2026-08-09 07:12:31', 'read_10s', 13, 1, NULL, 'news.ycombinator.com', 'desktop', 100, NULL),
(311, '2026-09-03 20:12:31', 'read_10s', 13, 1, NULL, 'google.com', 'desktop', 87, NULL),
(312, '2026-08-08 13:12:31', 'read_10s', 13, 1, NULL, 'github.com', 'desktop', 30, NULL),
(313, '2026-08-11 16:12:31', 'read_10s', 13, 1, NULL, 'direct', 'desktop', 32, NULL),
(314, '2026-08-16 18:12:31', 'read_10s', 13, 1, NULL, 'github.com', 'mobile', 81, NULL),
(315, '2026-09-01 08:12:31', 'read_10s', 14, 1, NULL, 'google.com', 'desktop', 158, NULL),
(316, '2026-08-31 06:12:31', 'read_10s', 14, 1, NULL, 'x.com', 'mobile', 71, NULL),
(317, '2026-08-13 13:12:31', 'read_10s', 14, 1, NULL, 'x.com', 'desktop', 63, NULL),
(318, '2026-08-26 03:12:31', 'read_10s', 14, 1, NULL, 'github.com', 'mobile', 132, NULL),
(319, '2026-08-24 01:12:31', 'read_10s', 14, 1, NULL, 'github.com', 'mobile', 151, NULL),
(320, '2026-08-22 19:12:31', 'read_10s', 14, 1, NULL, 'news.ycombinator.com', 'desktop', 79, NULL),
(321, '2026-08-23 19:12:31', 'read_10s', 14, 1, NULL, 'direct', 'tablet', 98, NULL),
(322, '2026-08-27 23:12:31', 'read_10s', 14, 1, NULL, 'google.com', 'desktop', 96, NULL),
(323, '2026-08-22 19:12:31', 'read_10s', 14, 1, NULL, 'google.com', 'desktop', 158, NULL),
(324, '2026-08-11 02:12:31', 'read_10s', 14, 1, NULL, 'google.com', 'desktop', 78, NULL),
(325, '2026-08-15 12:12:31', 'read_10s', 14, 1, NULL, 'direct', 'desktop', 30, NULL),
(326, '2026-08-12 17:12:31', 'read_10s', 14, 1, NULL, 'news.ycombinator.com', 'mobile', 126, NULL),
(327, '2026-08-24 11:12:31', 'read_10s', 14, 1, NULL, 'google.com', 'desktop', 180, NULL),
(328, '2026-08-20 15:12:31', 'read_10s', 14, 1, NULL, 'github.com', 'mobile', 102, NULL),
(329, '2026-08-28 06:12:31', 'read_10s', 14, 1, NULL, 'google.com', 'desktop', 60, NULL),
(330, '2026-08-29 10:12:31', 'read_10s', 14, 1, NULL, 'github.com', 'tablet', 101, NULL),
(331, '2026-08-17 09:12:31', 'read_10s', 14, 1, NULL, 'direct', 'mobile', 162, NULL),
(332, '2026-08-10 11:12:31', 'read_10s', 14, 1, NULL, 'direct', 'mobile', 25, NULL),
(333, '2026-08-15 00:12:31', 'read_10s', 14, 1, NULL, 'news.ycombinator.com', 'desktop', 77, NULL),
(334, '2026-08-24 12:12:31', 'read_10s', 14, 1, NULL, 'x.com', 'desktop', 89, NULL),
(335, '2026-08-10 23:12:31', 'read_10s', 14, 1, NULL, 'github.com', 'desktop', 106, NULL),
(336, '2026-09-02 10:12:31', 'read_10s', 14, 1, NULL, 'x.com', 'tablet', 30, NULL),
(337, '2026-08-07 07:12:31', 'read_10s', 14, 1, NULL, 'direct', 'mobile', 128, NULL),
(338, '2026-08-21 03:12:31', 'read_10s', 14, 1, NULL, 'google.com', 'tablet', 173, NULL),
(339, '2026-08-25 14:12:31', 'read_10s', 14, 1, NULL, 'x.com', 'desktop', 127, NULL),
(340, '2026-08-22 00:12:31', 'read_10s', 15, 1, NULL, 'x.com', 'desktop', 87, NULL),
(341, '2026-08-26 10:12:31', 'read_10s', 15, 1, NULL, 'x.com', 'desktop', 74, NULL),
(342, '2026-09-02 11:12:31', 'read_10s', 15, 1, NULL, 'google.com', 'mobile', 157, NULL),
(343, '2026-08-18 16:12:31', 'read_10s', 15, 1, NULL, 'x.com', 'desktop', 34, NULL),
(344, '2026-08-08 10:12:31', 'read_10s', 15, 1, NULL, 'x.com', 'mobile', 45, NULL),
(345, '2026-08-09 02:12:31', 'read_10s', 15, 1, NULL, 'direct', 'desktop', 111, NULL),
(346, '2026-08-25 02:12:31', 'read_10s', 15, 1, NULL, 'direct', 'mobile', 92, NULL),
(347, '2026-08-26 16:12:31', 'read_10s', 15, 1, NULL, 'direct', 'desktop', 122, NULL),
(348, '2026-08-30 20:12:31', 'read_10s', 15, 1, NULL, 'x.com', 'mobile', 163, NULL),
(349, '2026-08-31 13:12:31', 'read_10s', 15, 1, NULL, 'google.com', 'desktop', 137, NULL),
(350, '2026-08-28 15:12:31', 'read_10s', 15, 1, NULL, 'github.com', 'desktop', 14, NULL);
INSERT INTO `analytics_events` (`id`, `event_time`, `event_type`, `content_id`, `author_id`, `user_id`, `referrer_domain`, `device_type`, `duration_seconds`, `metadata_json`) VALUES 
(351, '2026-08-19 04:12:31', 'read_10s', 15, 1, NULL, 'news.ycombinator.com', 'desktop', 53, NULL),
(352, '2026-08-15 03:12:31', 'read_10s', 15, 1, NULL, 'github.com', 'desktop', 18, NULL),
(353, '2026-08-19 23:12:31', 'read_10s', 15, 1, NULL, 'github.com', 'desktop', 147, NULL),
(354, '2026-08-16 23:12:31', 'read_10s', 15, 1, NULL, 'github.com', 'desktop', 97, NULL),
(355, '2026-08-17 22:12:31', 'view', 15, 1, NULL, 'direct', 'desktop', 8, NULL),
(356, '2026-08-15 03:12:31', 'read_10s', 15, 1, NULL, 'github.com', 'tablet', 175, NULL),
(357, '2026-08-13 07:12:31', 'read_10s', 15, 1, NULL, 'x.com', 'tablet', 53, NULL),
(358, '2026-08-22 02:12:31', 'read_10s', 15, 1, NULL, 'google.com', 'desktop', 156, NULL),
(359, '2026-09-02 10:12:31', 'read_10s', 15, 1, NULL, 'news.ycombinator.com', 'mobile', 161, NULL),
(360, '2026-08-31 07:12:31', 'read_10s', 15, 1, NULL, 'github.com', 'mobile', 103, NULL),
(361, '2026-08-07 19:12:31', 'read_10s', 15, 1, NULL, 'x.com', 'desktop', 26, NULL),
(362, '2026-09-03 22:12:31', 'read_10s', 15, 1, NULL, 'google.com', 'desktop', 156, NULL),
(363, '2026-08-09 21:12:31', 'read_10s', 15, 1, NULL, 'x.com', 'desktop', 63, NULL),
(364, '2026-08-30 02:12:31', 'read_10s', 15, 1, NULL, 'direct', 'desktop', 161, NULL),
(365, '2026-08-20 10:12:31', 'read_10s', 16, 2, NULL, 'github.com', 'tablet', 139, NULL),
(366, '2026-08-07 15:12:31', 'read_10s', 16, 2, NULL, 'google.com', 'mobile', 18, NULL),
(367, '2026-08-17 04:12:31', 'read_10s', 16, 2, NULL, 'github.com', 'mobile', 105, NULL),
(368, '2026-08-14 20:12:31', 'read_10s', 16, 2, NULL, 'x.com', 'mobile', 61, NULL),
(369, '2026-08-28 10:12:31', 'read_10s', 16, 2, NULL, 'direct', 'desktop', 53, NULL),
(370, '2026-08-28 00:12:31', 'read_10s', 16, 2, NULL, 'google.com', 'desktop', 86, NULL),
(371, '2026-08-09 17:12:31', 'read_10s', 16, 2, NULL, 'google.com', 'mobile', 146, NULL),
(372, '2026-09-02 19:12:31', 'read_10s', 16, 2, NULL, 'news.ycombinator.com', 'desktop', 47, NULL),
(373, '2026-08-25 11:12:31', 'read_10s', 16, 2, NULL, 'direct', 'desktop', 45, NULL),
(374, '2026-08-31 06:12:31', 'read_10s', 16, 2, NULL, 'github.com', 'desktop', 109, NULL),
(375, '2026-08-24 05:12:31', 'read_10s', 16, 2, NULL, 'google.com', 'mobile', 40, NULL),
(376, '2026-08-19 04:12:31', 'read_10s', 16, 2, NULL, 'github.com', 'mobile', 160, NULL),
(377, '2026-08-07 12:12:31', 'read_10s', 16, 2, NULL, 'x.com', 'tablet', 49, NULL),
(378, '2026-08-30 11:12:31', 'read_10s', 16, 2, NULL, 'github.com', 'tablet', 105, NULL),
(379, '2026-08-29 07:12:31', 'read_10s', 16, 2, NULL, 'news.ycombinator.com', 'mobile', 55, NULL),
(380, '2026-08-28 12:12:31', 'read_10s', 16, 2, NULL, 'x.com', 'desktop', 39, NULL),
(381, '2026-08-30 22:12:31', 'read_10s', 16, 2, NULL, 'github.com', 'desktop', 30, NULL),
(382, '2026-08-25 14:12:31', 'read_10s', 16, 2, NULL, 'x.com', 'desktop', 108, NULL),
(383, '2026-08-23 18:12:32', 'read_10s', 16, 2, NULL, 'news.ycombinator.com', 'desktop', 86, NULL),
(384, '2026-08-16 11:12:32', 'read_10s', 16, 2, NULL, 'github.com', 'tablet', 124, NULL),
(385, '2026-08-09 04:12:32', 'read_10s', 16, 2, NULL, 'x.com', 'desktop', 86, NULL),
(386, '2026-08-11 21:12:32', 'read_10s', 16, 2, NULL, 'direct', 'desktop', 52, NULL),
(387, '2026-08-13 12:12:32', 'read_10s', 16, 2, NULL, 'x.com', 'desktop', 90, NULL),
(388, '2026-08-25 05:12:32', 'read_10s', 16, 2, NULL, 'direct', 'mobile', 19, NULL),
(389, '2026-08-29 21:12:32', 'read_10s', 16, 2, NULL, 'google.com', 'desktop', 172, NULL),
(390, '2026-08-23 18:12:32', 'read_10s', 17, 6, NULL, 'google.com', 'tablet', 162, NULL),
(391, '2026-08-25 06:12:32', 'read_10s', 17, 6, NULL, 'news.ycombinator.com', 'desktop', 77, NULL),
(392, '2026-09-01 10:12:32', 'read_10s', 17, 6, NULL, 'github.com', 'desktop', 178, NULL),
(393, '2026-08-15 00:12:32', 'read_10s', 17, 6, NULL, 'google.com', 'mobile', 24, NULL),
(394, '2026-08-25 21:12:32', 'read_10s', 17, 6, NULL, 'google.com', 'mobile', 99, NULL),
(395, '2026-08-28 12:12:32', 'read_10s', 17, 6, NULL, 'news.ycombinator.com', 'desktop', 50, NULL),
(396, '2026-08-24 20:12:32', 'view', 17, 6, NULL, 'google.com', 'desktop', 8, NULL),
(397, '2026-08-25 00:12:32', 'read_10s', 17, 6, NULL, 'direct', 'mobile', 92, NULL),
(398, '2026-08-19 18:12:32', 'read_10s', 17, 6, NULL, 'direct', 'desktop', 100, NULL),
(399, '2026-08-27 19:12:32', 'read_10s', 17, 6, NULL, 'github.com', 'desktop', 33, NULL),
(400, '2026-08-15 22:12:32', 'read_10s', 17, 6, NULL, 'google.com', 'desktop', 156, NULL);
INSERT INTO `analytics_events` (`id`, `event_time`, `event_type`, `content_id`, `author_id`, `user_id`, `referrer_domain`, `device_type`, `duration_seconds`, `metadata_json`) VALUES 
(401, '2026-08-24 07:12:32', 'read_10s', 17, 6, NULL, 'github.com', 'desktop', 124, NULL),
(402, '2026-08-17 12:12:32', 'read_10s', 17, 6, NULL, 'google.com', 'desktop', 96, NULL),
(403, '2026-08-13 13:12:32', 'read_10s', 17, 6, NULL, 'github.com', 'desktop', 107, NULL),
(404, '2026-08-31 10:12:32', 'read_10s', 17, 6, NULL, 'direct', 'desktop', 111, NULL),
(405, '2026-08-08 11:12:32', 'read_10s', 17, 6, NULL, 'github.com', 'desktop', 138, NULL),
(406, '2026-08-23 08:12:32', 'read_10s', 17, 6, NULL, 'google.com', 'desktop', 34, NULL),
(407, '2026-08-30 00:12:32', 'read_10s', 17, 6, NULL, 'google.com', 'desktop', 134, NULL),
(408, '2026-08-07 04:12:32', 'read_10s', 17, 6, NULL, 'google.com', 'mobile', 31, NULL),
(409, '2026-08-15 00:12:32', 'read_10s', 17, 6, NULL, 'direct', 'desktop', 29, NULL),
(410, '2026-08-16 01:12:32', 'read_10s', 17, 6, NULL, 'news.ycombinator.com', 'desktop', 63, NULL),
(411, '2026-08-31 06:12:32', 'read_10s', 17, 6, NULL, 'google.com', 'mobile', 31, NULL),
(412, '2026-08-27 04:12:32', 'read_10s', 17, 6, NULL, 'x.com', 'mobile', 31, NULL),
(413, '2026-08-08 01:12:32', 'read_10s', 17, 6, NULL, 'google.com', 'tablet', 24, NULL),
(414, '2026-08-17 02:12:32', 'view', 17, 6, NULL, 'direct', 'mobile', 8, NULL),
(415, '2026-09-02 01:12:32', 'read_10s', 18, 3, NULL, 'google.com', 'tablet', 110, NULL),
(416, '2026-09-02 16:12:32', 'read_10s', 18, 3, NULL, 'direct', 'desktop', 48, NULL),
(417, '2026-08-29 19:12:32', 'read_10s', 18, 3, NULL, 'direct', 'tablet', 150, NULL),
(418, '2026-08-18 00:12:32', 'read_10s', 18, 3, NULL, 'x.com', 'desktop', 100, NULL),
(419, '2026-08-16 05:12:32', 'read_10s', 18, 3, NULL, 'x.com', 'tablet', 25, NULL),
(420, '2026-08-21 17:12:32', 'read_10s', 18, 3, NULL, 'google.com', 'desktop', 41, NULL),
(421, '2026-08-19 22:12:32', 'read_10s', 18, 3, NULL, 'google.com', 'tablet', 61, NULL),
(422, '2026-08-21 15:12:32', 'read_10s', 18, 3, NULL, 'github.com', 'desktop', 161, NULL),
(423, '2026-08-14 03:12:32', 'read_10s', 18, 3, NULL, 'github.com', 'mobile', 131, NULL),
(424, '2026-08-20 15:12:32', 'read_10s', 18, 3, NULL, 'google.com', 'mobile', 107, NULL),
(425, '2026-08-31 13:12:32', 'read_10s', 18, 3, NULL, 'github.com', 'desktop', 50, NULL),
(426, '2026-08-19 15:12:32', 'read_10s', 18, 3, NULL, 'google.com', 'desktop', 83, NULL),
(427, '2026-08-24 15:12:32', 'read_10s', 18, 3, NULL, 'x.com', 'mobile', 82, NULL),
(428, '2026-08-25 07:12:32', 'read_10s', 18, 3, NULL, 'x.com', 'mobile', 78, NULL),
(429, '2026-08-20 06:12:32', 'read_10s', 18, 3, NULL, 'x.com', 'desktop', 64, NULL),
(430, '2026-08-18 12:12:32', 'read_10s', 18, 3, NULL, 'x.com', 'mobile', 59, NULL),
(431, '2026-08-29 12:12:32', 'read_10s', 18, 3, NULL, 'news.ycombinator.com', 'desktop', 122, NULL),
(432, '2026-08-15 01:12:32', 'read_10s', 18, 3, NULL, 'google.com', 'desktop', 99, NULL),
(433, '2026-08-10 00:12:32', 'read_10s', 18, 3, NULL, 'github.com', 'desktop', 122, NULL),
(434, '2026-08-11 09:12:32', 'read_10s', 18, 3, NULL, 'news.ycombinator.com', 'mobile', 58, NULL),
(435, '2026-08-28 03:12:32', 'read_10s', 18, 3, NULL, 'direct', 'desktop', 156, NULL),
(436, '2026-08-30 16:12:32', 'read_10s', 18, 3, NULL, 'github.com', 'desktop', 108, NULL),
(437, '2026-09-01 05:12:32', 'read_10s', 18, 3, NULL, 'direct', 'desktop', 113, NULL),
(438, '2026-08-26 03:12:32', 'read_10s', 18, 3, NULL, 'google.com', 'desktop', 128, NULL),
(439, '2026-08-07 11:12:32', 'read_10s', 18, 3, NULL, 'github.com', 'desktop', 105, NULL),
(440, '2026-08-23 02:12:32', 'read_10s', 19, 4, NULL, 'google.com', 'desktop', 36, NULL),
(441, '2026-08-23 00:12:32', 'read_10s', 19, 4, NULL, 'x.com', 'mobile', 77, NULL),
(442, '2026-08-22 21:12:32', 'read_10s', 19, 4, NULL, 'news.ycombinator.com', 'desktop', 11, NULL),
(443, '2026-08-15 12:12:32', 'read_10s', 19, 4, NULL, 'github.com', 'mobile', 48, NULL),
(444, '2026-08-30 11:12:32', 'read_10s', 19, 4, NULL, 'x.com', 'mobile', 157, NULL),
(445, '2026-08-10 23:12:32', 'read_10s', 19, 4, NULL, 'github.com', 'desktop', 165, NULL),
(446, '2026-08-21 16:12:32', 'read_10s', 19, 4, NULL, 'direct', 'tablet', 49, NULL),
(447, '2026-08-15 23:12:32', 'read_10s', 19, 4, NULL, 'x.com', 'desktop', 83, NULL),
(448, '2026-08-09 17:12:32', 'read_10s', 19, 4, NULL, 'x.com', 'mobile', 162, NULL),
(449, '2026-08-29 15:12:32', 'read_10s', 19, 4, NULL, 'x.com', 'mobile', 77, NULL),
(450, '2026-09-01 07:12:32', 'read_10s', 19, 4, NULL, 'google.com', 'mobile', 76, NULL);
INSERT INTO `analytics_events` (`id`, `event_time`, `event_type`, `content_id`, `author_id`, `user_id`, `referrer_domain`, `device_type`, `duration_seconds`, `metadata_json`) VALUES 
(451, '2026-08-23 17:12:32', 'read_10s', 19, 4, NULL, 'google.com', 'tablet', 180, NULL),
(452, '2026-09-01 08:12:32', 'read_10s', 19, 4, NULL, 'news.ycombinator.com', 'desktop', 119, NULL),
(453, '2026-09-02 11:12:32', 'read_10s', 19, 4, NULL, 'google.com', 'desktop', 159, NULL),
(454, '2026-08-14 12:12:32', 'read_10s', 19, 4, NULL, 'direct', 'mobile', 83, NULL),
(455, '2026-09-04 01:12:32', 'read_10s', 19, 4, NULL, 'direct', 'desktop', 29, NULL),
(456, '2026-08-08 18:12:32', 'read_10s', 19, 4, NULL, 'news.ycombinator.com', 'desktop', 12, NULL),
(457, '2026-08-31 22:12:32', 'read_10s', 19, 4, NULL, 'google.com', 'tablet', 56, NULL),
(458, '2026-08-27 01:12:32', 'read_10s', 19, 4, NULL, 'google.com', 'tablet', 66, NULL),
(459, '2026-08-11 20:12:32', 'read_10s', 19, 4, NULL, 'google.com', 'desktop', 137, NULL),
(460, '2026-08-11 10:12:32', 'read_10s', 19, 4, NULL, 'news.ycombinator.com', 'desktop', 118, NULL),
(461, '2026-08-09 14:12:32', 'read_10s', 19, 4, NULL, 'google.com', 'desktop', 170, NULL),
(462, '2026-08-12 17:12:32', 'read_10s', 19, 4, NULL, 'github.com', 'desktop', 138, NULL),
(463, '2026-09-03 10:12:32', 'read_10s', 19, 4, NULL, 'github.com', 'desktop', 81, NULL),
(464, '2026-08-31 14:12:32', 'read_10s', 19, 4, NULL, 'x.com', 'desktop', 159, NULL),
(465, '2026-08-21 17:12:32', 'read_10s', 20, 7, NULL, 'google.com', 'desktop', 33, NULL),
(466, '2026-08-23 22:12:32', 'read_10s', 20, 7, NULL, 'google.com', 'desktop', 156, NULL),
(467, '2026-08-17 15:12:32', 'read_10s', 20, 7, NULL, 'google.com', 'mobile', 146, NULL),
(468, '2026-08-08 05:12:32', 'read_10s', 20, 7, NULL, 'direct', 'desktop', 139, NULL),
(469, '2026-09-03 01:12:32', 'read_10s', 20, 7, NULL, 'github.com', 'desktop', 40, NULL),
(470, '2026-08-14 08:12:32', 'read_10s', 20, 7, NULL, 'github.com', 'tablet', 113, NULL),
(471, '2026-08-23 07:12:32', 'read_10s', 20, 7, NULL, 'x.com', 'desktop', 128, NULL),
(472, '2026-08-28 18:12:32', 'read_10s', 20, 7, NULL, 'news.ycombinator.com', 'tablet', 140, NULL),
(473, '2026-08-07 23:12:32', 'read_10s', 20, 7, NULL, 'direct', 'mobile', 149, NULL),
(474, '2026-09-01 05:12:32', 'read_10s', 20, 7, NULL, 'direct', 'desktop', 93, NULL),
(475, '2026-08-30 12:12:32', 'read_10s', 20, 7, NULL, 'google.com', 'tablet', 123, NULL),
(476, '2026-08-07 08:12:32', 'read_10s', 20, 7, NULL, 'x.com', 'desktop', 138, NULL),
(477, '2026-09-02 21:12:32', 'read_10s', 20, 7, NULL, 'news.ycombinator.com', 'desktop', 136, NULL),
(478, '2026-08-08 15:12:32', 'view', 20, 7, NULL, 'google.com', 'desktop', 9, NULL),
(479, '2026-08-20 11:12:32', 'read_10s', 20, 7, NULL, 'google.com', 'desktop', 47, NULL),
(480, '2026-08-30 08:12:32', 'read_10s', 20, 7, NULL, 'direct', 'mobile', 71, NULL),
(481, '2026-08-27 23:12:32', 'read_10s', 20, 7, NULL, 'direct', 'mobile', 31, NULL),
(482, '2026-08-21 02:12:32', 'read_10s', 20, 7, NULL, 'direct', 'desktop', 11, NULL),
(483, '2026-08-22 17:12:32', 'read_10s', 20, 7, NULL, 'google.com', 'desktop', 160, NULL),
(484, '2026-08-07 20:12:32', 'read_10s', 20, 7, NULL, 'google.com', 'mobile', 82, NULL),
(485, '2026-08-26 10:12:32', 'read_10s', 20, 7, NULL, 'google.com', 'mobile', 45, NULL),
(486, '2026-08-15 16:12:32', 'read_10s', 20, 7, NULL, 'google.com', 'desktop', 116, NULL),
(487, '2026-08-13 09:12:32', 'read_10s', 20, 7, NULL, 'news.ycombinator.com', 'desktop', 69, NULL),
(488, '2026-08-10 22:12:32', 'read_10s', 20, 7, NULL, 'direct', 'desktop', 43, NULL),
(489, '2026-08-30 22:12:32', 'read_10s', 20, 7, NULL, 'google.com', 'desktop', 65, NULL),
(490, '2026-08-24 13:12:32', 'read_10s', 21, 4, NULL, 'x.com', 'desktop', 107, NULL),
(491, '2026-08-10 21:12:32', 'read_10s', 21, 4, NULL, 'x.com', 'desktop', 166, NULL),
(492, '2026-08-19 07:12:32', 'read_10s', 21, 4, NULL, 'google.com', 'desktop', 52, NULL),
(493, '2026-09-02 22:12:32', 'read_10s', 21, 4, NULL, 'direct', 'tablet', 104, NULL),
(494, '2026-08-17 15:12:32', 'read_10s', 21, 4, NULL, 'x.com', 'desktop', 101, NULL),
(495, '2026-09-02 21:12:32', 'read_10s', 21, 4, NULL, 'google.com', 'desktop', 53, NULL),
(496, '2026-08-16 22:12:32', 'read_10s', 21, 4, NULL, 'direct', 'mobile', 100, NULL),
(497, '2026-08-19 00:12:32', 'read_10s', 21, 4, NULL, 'github.com', 'tablet', 167, NULL),
(498, '2026-08-16 23:12:32', 'read_10s', 21, 4, NULL, 'github.com', 'mobile', 100, NULL),
(499, '2026-08-11 18:12:32', 'read_10s', 21, 4, NULL, 'x.com', 'desktop', 156, NULL),
(500, '2026-08-15 00:12:32', 'read_10s', 21, 4, NULL, 'direct', 'mobile', 58, NULL);
INSERT INTO `analytics_events` (`id`, `event_time`, `event_type`, `content_id`, `author_id`, `user_id`, `referrer_domain`, `device_type`, `duration_seconds`, `metadata_json`) VALUES 
(501, '2026-09-04 10:12:32', 'read_10s', 21, 4, NULL, 'news.ycombinator.com', 'desktop', 141, NULL),
(502, '2026-08-23 08:12:32', 'read_10s', 21, 4, NULL, 'news.ycombinator.com', 'desktop', 95, NULL),
(503, '2026-08-29 14:12:32', 'read_10s', 21, 4, NULL, 'news.ycombinator.com', 'desktop', 93, NULL),
(504, '2026-08-17 05:12:32', 'read_10s', 21, 4, NULL, 'x.com', 'desktop', 87, NULL),
(505, '2026-08-09 16:12:32', 'read_10s', 21, 4, NULL, 'x.com', 'desktop', 44, NULL),
(506, '2026-08-26 22:12:32', 'read_10s', 21, 4, NULL, 'github.com', 'mobile', 157, NULL),
(507, '2026-08-07 14:12:32', 'read_10s', 21, 4, NULL, 'github.com', 'mobile', 140, NULL),
(508, '2026-08-19 07:12:32', 'read_10s', 21, 4, NULL, 'x.com', 'desktop', 50, NULL),
(509, '2026-08-19 18:12:32', 'read_10s', 21, 4, NULL, 'google.com', 'mobile', 152, NULL),
(510, '2026-08-08 11:12:32', 'read_10s', 21, 4, NULL, 'github.com', 'mobile', 39, NULL),
(511, '2026-08-17 16:12:32', 'read_10s', 21, 4, NULL, 'x.com', 'desktop', 125, NULL),
(512, '2026-08-13 18:12:32', 'read_10s', 21, 4, NULL, 'x.com', 'mobile', 164, NULL),
(513, '2026-08-10 09:12:32', 'read_10s', 21, 4, NULL, 'x.com', 'desktop', 177, NULL),
(514, '2026-08-11 22:12:32', 'read_10s', 21, 4, NULL, 'google.com', 'desktop', 165, NULL),
(515, '2026-08-15 22:12:32', 'read_10s', 22, 5, NULL, 'github.com', 'desktop', 90, NULL),
(516, '2026-08-07 01:12:32', 'view', 22, 5, NULL, 'google.com', 'mobile', 9, NULL),
(517, '2026-08-15 23:12:32', 'read_10s', 22, 5, NULL, 'direct', 'mobile', 154, NULL),
(518, '2026-08-19 21:12:32', 'read_10s', 22, 5, NULL, 'github.com', 'tablet', 79, NULL),
(519, '2026-08-24 16:12:32', 'read_10s', 22, 5, NULL, 'news.ycombinator.com', 'mobile', 48, NULL),
(520, '2026-08-18 16:12:32', 'read_10s', 22, 5, NULL, 'x.com', 'desktop', 13, NULL),
(521, '2026-08-26 13:12:32', 'read_10s', 22, 5, NULL, 'google.com', 'mobile', 172, NULL),
(522, '2026-09-02 16:12:32', 'read_10s', 22, 5, NULL, 'google.com', 'desktop', 102, NULL),
(523, '2026-08-13 14:12:32', 'read_10s', 22, 5, NULL, 'news.ycombinator.com', 'desktop', 95, NULL),
(524, '2026-08-07 16:12:32', 'read_10s', 22, 5, NULL, 'google.com', 'desktop', 128, NULL),
(525, '2026-08-25 03:12:32', 'read_10s', 22, 5, NULL, 'x.com', 'desktop', 78, NULL),
(526, '2026-08-11 20:12:32', 'read_10s', 22, 5, NULL, 'x.com', 'desktop', 107, NULL),
(527, '2026-08-22 04:12:32', 'read_10s', 22, 5, NULL, 'github.com', 'desktop', 177, NULL),
(528, '2026-09-03 07:12:32', 'read_10s', 22, 5, NULL, 'google.com', 'desktop', 127, NULL),
(529, '2026-08-08 07:12:32', 'read_10s', 22, 5, NULL, 'github.com', 'desktop', 138, NULL),
(530, '2026-08-11 01:12:32', 'read_10s', 22, 5, NULL, 'google.com', 'desktop', 24, NULL),
(531, '2026-08-25 14:12:32', 'read_10s', 22, 5, NULL, 'x.com', 'mobile', 74, NULL),
(532, '2026-09-03 09:12:32', 'read_10s', 22, 5, NULL, 'google.com', 'desktop', 40, NULL),
(533, '2026-08-08 09:12:32', 'read_10s', 22, 5, NULL, 'google.com', 'desktop', 46, NULL),
(534, '2026-08-22 04:12:32', 'view', 22, 5, NULL, 'google.com', 'mobile', 8, NULL),
(535, '2026-08-08 12:12:32', 'read_10s', 22, 5, NULL, 'news.ycombinator.com', 'desktop', 170, NULL),
(536, '2026-09-01 15:12:32', 'read_10s', 22, 5, NULL, 'google.com', 'mobile', 52, NULL),
(537, '2026-08-30 14:12:32', 'read_10s', 22, 5, NULL, 'x.com', 'desktop', 114, NULL),
(538, '2026-09-01 21:12:32', 'read_10s', 22, 5, NULL, 'direct', 'desktop', 173, NULL),
(539, '2026-08-25 09:12:32', 'read_10s', 22, 5, NULL, 'github.com', 'desktop', 174, NULL),
(540, '2026-08-24 17:12:32', 'read_10s', 23, 2, NULL, 'news.ycombinator.com', 'mobile', 97, NULL),
(541, '2026-08-13 01:12:32', 'read_10s', 23, 2, NULL, 'x.com', 'desktop', 60, NULL),
(542, '2026-08-30 13:12:32', 'read_10s', 23, 2, NULL, 'google.com', 'mobile', 34, NULL),
(543, '2026-08-12 12:12:32', 'read_10s', 23, 2, NULL, 'google.com', 'desktop', 160, NULL),
(544, '2026-08-25 14:12:32', 'read_10s', 23, 2, NULL, 'github.com', 'desktop', 123, NULL),
(545, '2026-08-08 02:12:32', 'read_10s', 23, 2, NULL, 'direct', 'desktop', 100, NULL),
(546, '2026-08-20 15:12:32', 'read_10s', 23, 2, NULL, 'direct', 'desktop', 138, NULL),
(547, '2026-08-23 00:12:32', 'read_10s', 23, 2, NULL, 'github.com', 'mobile', 53, NULL),
(548, '2026-08-24 19:12:32', 'read_10s', 23, 2, NULL, 'x.com', 'desktop', 162, NULL),
(549, '2026-08-28 13:12:32', 'read_10s', 23, 2, NULL, 'x.com', 'tablet', 22, NULL),
(550, '2026-08-28 17:12:32', 'read_10s', 23, 2, NULL, 'google.com', 'desktop', 96, NULL);
INSERT INTO `analytics_events` (`id`, `event_time`, `event_type`, `content_id`, `author_id`, `user_id`, `referrer_domain`, `device_type`, `duration_seconds`, `metadata_json`) VALUES 
(551, '2026-09-01 19:12:32', 'read_10s', 23, 2, NULL, 'github.com', 'desktop', 92, NULL),
(552, '2026-09-01 05:12:32', 'read_10s', 23, 2, NULL, 'google.com', 'desktop', 91, NULL),
(553, '2026-08-27 05:12:32', 'read_10s', 23, 2, NULL, 'x.com', 'desktop', 16, NULL),
(554, '2026-08-31 20:12:32', 'read_10s', 23, 2, NULL, 'news.ycombinator.com', 'mobile', 83, NULL),
(555, '2026-08-17 10:12:32', 'read_10s', 23, 2, NULL, 'github.com', 'mobile', 119, NULL),
(556, '2026-08-08 06:12:32', 'read_10s', 23, 2, NULL, 'direct', 'desktop', 103, NULL),
(557, '2026-08-11 13:12:32', 'read_10s', 23, 2, NULL, 'google.com', 'mobile', 92, NULL),
(558, '2026-08-15 18:12:32', 'read_10s', 23, 2, NULL, 'news.ycombinator.com', 'desktop', 177, NULL),
(559, '2026-08-31 11:12:32', 'read_10s', 23, 2, NULL, 'x.com', 'tablet', 39, NULL),
(560, '2026-08-08 09:12:32', 'read_10s', 23, 2, NULL, 'github.com', 'desktop', 81, NULL),
(561, '2026-08-09 18:12:32', 'read_10s', 23, 2, NULL, 'github.com', 'desktop', 110, NULL),
(562, '2026-08-15 00:12:32', 'read_10s', 23, 2, NULL, 'news.ycombinator.com', 'tablet', 32, NULL),
(563, '2026-08-29 13:12:32', 'read_10s', 23, 2, NULL, 'news.ycombinator.com', 'desktop', 136, NULL),
(564, '2026-08-18 21:12:32', 'read_10s', 23, 2, NULL, 'google.com', 'mobile', 132, NULL),
(565, '2026-08-13 20:12:32', 'read_10s', 24, 3, NULL, 'x.com', 'desktop', 111, NULL),
(566, '2026-09-04 14:12:32', 'read_10s', 24, 3, NULL, 'google.com', 'mobile', 124, NULL),
(567, '2026-08-22 13:12:32', 'read_10s', 24, 3, NULL, 'github.com', 'desktop', 169, NULL),
(568, '2026-09-01 19:12:32', 'read_10s', 24, 3, NULL, 'news.ycombinator.com', 'tablet', 17, NULL),
(569, '2026-08-12 02:12:32', 'read_10s', 24, 3, NULL, 'github.com', 'desktop', 169, NULL),
(570, '2026-08-08 12:12:32', 'read_10s', 24, 3, NULL, 'x.com', 'desktop', 20, NULL),
(571, '2026-08-22 15:12:32', 'read_10s', 24, 3, NULL, 'direct', 'desktop', 165, NULL),
(572, '2026-08-22 10:12:32', 'read_10s', 24, 3, NULL, 'x.com', 'desktop', 118, NULL),
(573, '2026-08-15 01:12:32', 'read_10s', 24, 3, NULL, 'x.com', 'desktop', 87, NULL),
(574, '2026-08-14 21:12:32', 'read_10s', 24, 3, NULL, 'direct', 'desktop', 81, NULL),
(575, '2026-08-17 12:12:32', 'read_10s', 24, 3, NULL, 'google.com', 'mobile', 23, NULL),
(576, '2026-08-19 10:12:32', 'read_10s', 24, 3, NULL, 'github.com', 'mobile', 29, NULL),
(577, '2026-08-21 13:12:32', 'read_10s', 24, 3, NULL, 'google.com', 'mobile', 40, NULL),
(578, '2026-09-01 02:12:32', 'read_10s', 24, 3, NULL, 'x.com', 'desktop', 61, NULL),
(579, '2026-08-18 18:12:32', 'read_10s', 24, 3, NULL, 'google.com', 'desktop', 122, NULL),
(580, '2026-08-31 17:12:32', 'read_10s', 24, 3, NULL, 'github.com', 'desktop', 122, NULL),
(581, '2026-08-07 20:12:32', 'read_10s', 24, 3, NULL, 'google.com', 'desktop', 66, NULL),
(582, '2026-08-24 04:12:32', 'read_10s', 24, 3, NULL, 'x.com', 'desktop', 50, NULL),
(583, '2026-08-07 05:12:32', 'read_10s', 24, 3, NULL, 'news.ycombinator.com', 'desktop', 37, NULL),
(584, '2026-08-11 17:12:32', 'read_10s', 24, 3, NULL, 'github.com', 'tablet', 159, NULL),
(585, '2026-08-17 19:12:32', 'read_10s', 24, 3, NULL, 'news.ycombinator.com', 'desktop', 33, NULL),
(586, '2026-08-23 12:12:32', 'read_10s', 24, 3, NULL, 'google.com', 'mobile', 175, NULL),
(587, '2026-08-22 03:12:32', 'read_10s', 24, 3, NULL, 'direct', 'desktop', 29, NULL),
(588, '2026-08-20 21:12:32', 'read_10s', 24, 3, NULL, 'news.ycombinator.com', 'desktop', 118, NULL),
(589, '2026-09-02 02:12:32', 'read_10s', 24, 3, NULL, 'direct', 'desktop', 133, NULL),
(590, '2026-08-24 01:12:32', 'read_10s', 25, 4, NULL, 'github.com', 'mobile', 96, NULL),
(591, '2026-08-12 22:12:32', 'read_10s', 25, 4, NULL, 'direct', 'desktop', 164, NULL),
(592, '2026-08-19 05:12:32', 'read_10s', 25, 4, NULL, 'direct', 'mobile', 99, NULL),
(593, '2026-08-19 22:12:32', 'read_10s', 25, 4, NULL, 'direct', 'mobile', 145, NULL),
(594, '2026-09-03 23:12:32', 'read_10s', 25, 4, NULL, 'news.ycombinator.com', 'mobile', 14, NULL),
(595, '2026-08-25 10:12:32', 'read_10s', 25, 4, NULL, 'github.com', 'desktop', 64, NULL),
(596, '2026-09-01 18:12:32', 'read_10s', 25, 4, NULL, 'x.com', 'desktop', 24, NULL),
(597, '2026-08-24 11:12:32', 'read_10s', 25, 4, NULL, 'github.com', 'desktop', 78, NULL),
(598, '2026-08-09 04:12:32', 'read_10s', 25, 4, NULL, 'github.com', 'tablet', 50, NULL),
(599, '2026-09-04 01:12:32', 'read_10s', 25, 4, NULL, 'direct', 'desktop', 80, NULL),
(600, '2026-08-26 19:12:32', 'read_10s', 25, 4, NULL, 'x.com', 'desktop', 170, NULL);
INSERT INTO `analytics_events` (`id`, `event_time`, `event_type`, `content_id`, `author_id`, `user_id`, `referrer_domain`, `device_type`, `duration_seconds`, `metadata_json`) VALUES 
(601, '2026-08-08 10:12:32', 'read_10s', 25, 4, NULL, 'google.com', 'desktop', 73, NULL),
(602, '2026-08-13 09:12:32', 'read_10s', 25, 4, NULL, 'news.ycombinator.com', 'desktop', 70, NULL),
(603, '2026-08-28 13:12:32', 'read_10s', 25, 4, NULL, 'x.com', 'desktop', 38, NULL),
(604, '2026-08-22 12:12:32', 'read_10s', 25, 4, NULL, 'news.ycombinator.com', 'mobile', 11, NULL),
(605, '2026-08-27 12:12:32', 'read_10s', 25, 4, NULL, 'google.com', 'mobile', 164, NULL),
(606, '2026-08-18 15:12:32', 'read_10s', 25, 4, NULL, 'direct', 'mobile', 68, NULL),
(607, '2026-09-03 06:12:32', 'read_10s', 25, 4, NULL, 'news.ycombinator.com', 'mobile', 27, NULL),
(608, '2026-08-13 10:12:32', 'read_10s', 25, 4, NULL, 'x.com', 'desktop', 31, NULL),
(609, '2026-08-17 02:12:32', 'read_10s', 25, 4, NULL, 'x.com', 'desktop', 137, NULL),
(610, '2026-08-18 13:12:32', 'read_10s', 25, 4, NULL, 'direct', 'mobile', 111, NULL),
(611, '2026-09-02 01:12:32', 'read_10s', 25, 4, NULL, 'x.com', 'desktop', 116, NULL),
(612, '2026-08-18 01:12:32', 'read_10s', 25, 4, NULL, 'github.com', 'mobile', 11, NULL),
(613, '2026-08-15 20:12:32', 'read_10s', 25, 4, NULL, 'github.com', 'tablet', 28, NULL),
(614, '2026-08-23 23:12:32', 'read_10s', 25, 4, NULL, 'github.com', 'desktop', 100, NULL),
(615, '2026-08-18 12:12:32', 'read_10s', 26, 2, NULL, 'github.com', 'desktop', 61, NULL),
(616, '2026-08-16 05:12:32', 'read_10s', 26, 2, NULL, 'google.com', 'desktop', 38, NULL),
(617, '2026-08-07 18:12:32', 'read_10s', 26, 2, NULL, 'github.com', 'mobile', 72, NULL),
(618, '2026-08-23 02:12:32', 'read_10s', 26, 2, NULL, 'github.com', 'desktop', 99, NULL),
(619, '2026-09-04 13:12:32', 'read_10s', 26, 2, NULL, 'google.com', 'mobile', 109, NULL),
(620, '2026-08-22 01:12:32', 'read_10s', 26, 2, NULL, 'direct', 'tablet', 154, NULL),
(621, '2026-08-10 03:12:32', 'read_10s', 26, 2, NULL, 'x.com', 'desktop', 100, NULL),
(622, '2026-08-13 10:12:32', 'read_10s', 26, 2, NULL, 'x.com', 'desktop', 108, NULL),
(623, '2026-08-09 01:12:32', 'view', 26, 2, NULL, 'github.com', 'desktop', 8, NULL),
(624, '2026-08-27 18:12:32', 'read_10s', 26, 2, NULL, 'news.ycombinator.com', 'desktop', 119, NULL),
(625, '2026-08-16 15:12:32', 'read_10s', 26, 2, NULL, 'google.com', 'mobile', 57, NULL),
(626, '2026-09-02 03:12:32', 'read_10s', 26, 2, NULL, 'github.com', 'desktop', 178, NULL),
(627, '2026-08-08 19:12:32', 'read_10s', 26, 2, NULL, 'direct', 'desktop', 65, NULL),
(628, '2026-08-17 05:12:32', 'read_10s', 26, 2, NULL, 'google.com', 'desktop', 83, NULL),
(629, '2026-09-03 19:12:32', 'read_10s', 26, 2, NULL, 'google.com', 'mobile', 123, NULL),
(630, '2026-08-22 01:12:32', 'read_10s', 26, 2, NULL, 'google.com', 'mobile', 87, NULL),
(631, '2026-08-09 19:12:32', 'read_10s', 26, 2, NULL, 'github.com', 'desktop', 75, NULL),
(632, '2026-08-10 16:12:32', 'read_10s', 26, 2, NULL, 'github.com', 'mobile', 113, NULL),
(633, '2026-08-16 20:12:32', 'read_10s', 26, 2, NULL, 'google.com', 'tablet', 152, NULL),
(634, '2026-08-10 11:12:32', 'read_10s', 26, 2, NULL, 'google.com', 'mobile', 71, NULL),
(635, '2026-08-16 22:12:32', 'read_10s', 26, 2, NULL, 'x.com', 'desktop', 108, NULL),
(636, '2026-08-14 18:12:32', 'read_10s', 26, 2, NULL, 'google.com', 'mobile', 44, NULL),
(637, '2026-08-08 05:12:32', 'read_10s', 26, 2, NULL, 'google.com', 'desktop', 47, NULL),
(638, '2026-08-28 16:12:32', 'read_10s', 26, 2, NULL, 'google.com', 'desktop', 33, NULL),
(639, '2026-09-03 03:12:32', 'read_10s', 26, 2, NULL, 'github.com', 'mobile', 73, NULL),
(640, '2026-08-31 18:12:32', 'read_10s', 27, 5, NULL, 'google.com', 'desktop', 75, NULL),
(641, '2026-08-23 06:12:32', 'read_10s', 27, 5, NULL, 'google.com', 'desktop', 60, NULL),
(642, '2026-08-24 05:12:32', 'read_10s', 27, 5, NULL, 'direct', 'desktop', 71, NULL),
(643, '2026-08-13 12:12:32', 'read_10s', 27, 5, NULL, 'x.com', 'desktop', 131, NULL),
(644, '2026-08-25 05:12:32', 'read_10s', 27, 5, NULL, 'x.com', 'mobile', 145, NULL),
(645, '2026-08-30 03:12:32', 'read_10s', 27, 5, NULL, 'direct', 'desktop', 78, NULL),
(646, '2026-08-14 13:12:32', 'read_10s', 27, 5, NULL, 'github.com', 'desktop', 77, NULL),
(647, '2026-08-30 08:12:32', 'read_10s', 27, 5, NULL, 'news.ycombinator.com', 'desktop', 31, NULL),
(648, '2026-08-23 09:12:32', 'read_10s', 27, 5, NULL, 'github.com', 'desktop', 176, NULL),
(649, '2026-08-11 13:12:32', 'read_10s', 27, 5, NULL, 'direct', 'tablet', 157, NULL),
(650, '2026-08-18 02:12:32', 'read_10s', 27, 5, NULL, 'direct', 'mobile', 97, NULL);
INSERT INTO `analytics_events` (`id`, `event_time`, `event_type`, `content_id`, `author_id`, `user_id`, `referrer_domain`, `device_type`, `duration_seconds`, `metadata_json`) VALUES 
(651, '2026-08-22 06:12:32', 'read_10s', 27, 5, NULL, 'news.ycombinator.com', 'desktop', 36, NULL),
(652, '2026-08-09 03:12:32', 'read_10s', 27, 5, NULL, 'github.com', 'mobile', 115, NULL),
(653, '2026-08-08 11:12:32', 'read_10s', 27, 5, NULL, 'github.com', 'desktop', 164, NULL),
(654, '2026-08-11 17:12:32', 'read_10s', 27, 5, NULL, 'direct', 'desktop', 104, NULL),
(655, '2026-08-28 11:12:32', 'read_10s', 27, 5, NULL, 'news.ycombinator.com', 'tablet', 42, NULL),
(656, '2026-08-16 15:12:32', 'read_10s', 27, 5, NULL, 'x.com', 'desktop', 136, NULL),
(657, '2026-08-13 10:12:32', 'read_10s', 27, 5, NULL, 'google.com', 'desktop', 92, NULL),
(658, '2026-08-25 09:12:32', 'read_10s', 27, 5, NULL, 'google.com', 'desktop', 91, NULL),
(659, '2026-08-31 07:12:32', 'read_10s', 27, 5, NULL, 'x.com', 'desktop', 146, NULL),
(660, '2026-08-13 23:12:32', 'read_10s', 27, 5, NULL, 'github.com', 'desktop', 176, NULL),
(661, '2026-08-24 14:12:32', 'read_10s', 27, 5, NULL, 'google.com', 'mobile', 138, NULL),
(662, '2026-08-26 08:12:32', 'read_10s', 27, 5, NULL, 'news.ycombinator.com', 'tablet', 101, NULL),
(663, '2026-09-02 09:12:32', 'read_10s', 27, 5, NULL, 'news.ycombinator.com', 'desktop', 149, NULL),
(664, '2026-08-17 01:12:32', 'read_10s', 27, 5, NULL, 'x.com', 'mobile', 100, NULL),
(665, '2026-08-21 02:12:32', 'read_10s', 28, 7, NULL, 'x.com', 'desktop', 134, NULL),
(666, '2026-09-01 17:12:32', 'read_10s', 28, 7, NULL, 'news.ycombinator.com', 'mobile', 141, NULL),
(667, '2026-08-27 20:12:32', 'read_10s', 28, 7, NULL, 'direct', 'desktop', 156, NULL),
(668, '2026-08-31 11:12:32', 'read_10s', 28, 7, NULL, 'google.com', 'desktop', 53, NULL),
(669, '2026-08-09 03:12:32', 'read_10s', 28, 7, NULL, 'x.com', 'desktop', 114, NULL),
(670, '2026-08-10 11:12:32', 'read_10s', 28, 7, NULL, 'direct', 'mobile', 110, NULL),
(671, '2026-08-23 06:12:32', 'read_10s', 28, 7, NULL, 'x.com', 'desktop', 23, NULL),
(672, '2026-08-20 05:12:32', 'read_10s', 28, 7, NULL, 'google.com', 'mobile', 157, NULL),
(673, '2026-08-26 11:12:32', 'read_10s', 28, 7, NULL, 'x.com', 'mobile', 13, NULL),
(674, '2026-08-15 21:12:32', 'read_10s', 28, 7, NULL, 'x.com', 'desktop', 133, NULL),
(675, '2026-08-28 10:12:32', 'read_10s', 28, 7, NULL, 'github.com', 'tablet', 177, NULL),
(676, '2026-08-07 07:12:32', 'read_10s', 28, 7, NULL, 'google.com', 'desktop', 39, NULL),
(677, '2026-08-12 23:12:32', 'read_10s', 28, 7, NULL, 'direct', 'mobile', 11, NULL),
(678, '2026-09-04 16:12:32', 'read_10s', 28, 7, NULL, 'direct', 'desktop', 167, NULL),
(679, '2026-08-09 01:12:32', 'read_10s', 28, 7, NULL, 'github.com', 'mobile', 103, NULL),
(680, '2026-08-13 23:12:32', 'read_10s', 28, 7, NULL, 'github.com', 'mobile', 131, NULL),
(681, '2026-08-22 00:12:32', 'read_10s', 28, 7, NULL, 'direct', 'tablet', 64, NULL),
(682, '2026-08-17 18:12:32', 'read_10s', 28, 7, NULL, 'news.ycombinator.com', 'mobile', 31, NULL),
(683, '2026-09-01 07:12:32', 'read_10s', 28, 7, NULL, 'news.ycombinator.com', 'mobile', 45, NULL),
(684, '2026-08-14 00:12:32', 'read_10s', 28, 7, NULL, 'google.com', 'desktop', 55, NULL),
(685, '2026-08-27 23:12:32', 'read_10s', 28, 7, NULL, 'github.com', 'tablet', 147, NULL),
(686, '2026-08-12 07:12:32', 'read_10s', 28, 7, NULL, 'x.com', 'desktop', 111, NULL),
(687, '2026-08-31 12:12:32', 'read_10s', 28, 7, NULL, 'google.com', 'mobile', 119, NULL),
(688, '2026-08-08 20:12:32', 'read_10s', 28, 7, NULL, 'github.com', 'mobile', 43, NULL),
(689, '2026-08-08 07:12:32', 'read_10s', 28, 7, NULL, 'github.com', 'desktop', 15, NULL),
(690, '2026-08-17 18:12:32', 'read_10s', 29, 6, NULL, 'x.com', 'desktop', 67, NULL),
(691, '2026-08-25 06:12:32', 'read_10s', 29, 6, NULL, 'x.com', 'desktop', 44, NULL),
(692, '2026-08-12 23:12:32', 'read_10s', 29, 6, NULL, 'google.com', 'desktop', 92, NULL),
(693, '2026-08-09 08:12:32', 'read_10s', 29, 6, NULL, 'github.com', 'tablet', 13, NULL),
(694, '2026-08-10 15:12:32', 'read_10s', 29, 6, NULL, 'google.com', 'desktop', 111, NULL),
(695, '2026-08-19 19:12:32', 'read_10s', 29, 6, NULL, 'google.com', 'desktop', 177, NULL),
(696, '2026-08-18 11:12:32', 'read_10s', 29, 6, NULL, 'direct', 'mobile', 104, NULL),
(697, '2026-08-24 19:12:32', 'read_10s', 29, 6, NULL, 'github.com', 'mobile', 147, NULL),
(698, '2026-08-14 16:12:32', 'read_10s', 29, 6, NULL, 'google.com', 'mobile', 177, NULL),
(699, '2026-08-27 03:12:32', 'read_10s', 29, 6, NULL, 'github.com', 'desktop', 142, NULL),
(700, '2026-08-15 13:12:32', 'read_10s', 29, 6, NULL, 'google.com', 'desktop', 25, NULL);
INSERT INTO `analytics_events` (`id`, `event_time`, `event_type`, `content_id`, `author_id`, `user_id`, `referrer_domain`, `device_type`, `duration_seconds`, `metadata_json`) VALUES 
(701, '2026-08-12 10:12:32', 'read_10s', 29, 6, NULL, 'news.ycombinator.com', 'desktop', 16, NULL),
(702, '2026-08-12 15:12:32', 'read_10s', 29, 6, NULL, 'direct', 'desktop', 82, NULL),
(703, '2026-08-09 10:12:32', 'read_10s', 29, 6, NULL, 'direct', 'desktop', 138, NULL),
(704, '2026-09-02 03:12:32', 'read_10s', 29, 6, NULL, 'news.ycombinator.com', 'desktop', 80, NULL),
(705, '2026-08-07 10:12:32', 'read_10s', 29, 6, NULL, 'direct', 'tablet', 120, NULL),
(706, '2026-08-08 00:12:32', 'read_10s', 29, 6, NULL, 'google.com', 'mobile', 155, NULL),
(707, '2026-08-20 03:12:32', 'read_10s', 29, 6, NULL, 'github.com', 'tablet', 19, NULL),
(708, '2026-08-29 01:12:32', 'read_10s', 29, 6, NULL, 'github.com', 'desktop', 142, NULL),
(709, '2026-09-01 08:12:32', 'read_10s', 29, 6, NULL, 'github.com', 'desktop', 162, NULL),
(710, '2026-08-11 17:12:32', 'read_10s', 29, 6, NULL, 'google.com', 'mobile', 66, NULL),
(711, '2026-08-07 01:12:32', 'read_10s', 29, 6, NULL, 'direct', 'mobile', 58, NULL),
(712, '2026-08-10 10:12:32', 'read_10s', 29, 6, NULL, 'x.com', 'desktop', 32, NULL),
(713, '2026-08-14 00:12:32', 'read_10s', 29, 6, NULL, 'x.com', 'mobile', 165, NULL),
(714, '2026-08-30 20:12:32', 'read_10s', 29, 6, NULL, 'google.com', 'mobile', 96, NULL),
(715, '2026-08-17 16:12:32', 'read_10s', 30, 1, NULL, 'google.com', 'mobile', 30, NULL),
(716, '2026-08-21 18:12:32', 'read_10s', 30, 1, NULL, 'news.ycombinator.com', 'mobile', 47, NULL),
(717, '2026-08-25 00:12:32', 'read_10s', 30, 1, NULL, 'x.com', 'desktop', 110, NULL),
(718, '2026-08-16 18:12:32', 'read_10s', 30, 1, NULL, 'x.com', 'desktop', 114, NULL),
(719, '2026-08-31 08:12:32', 'read_10s', 30, 1, NULL, 'direct', 'tablet', 56, NULL),
(720, '2026-08-26 15:12:32', 'read_10s', 30, 1, NULL, 'github.com', 'desktop', 53, NULL),
(721, '2026-08-10 17:12:32', 'read_10s', 30, 1, NULL, 'github.com', 'desktop', 114, NULL),
(722, '2026-08-29 03:12:32', 'read_10s', 30, 1, NULL, 'x.com', 'desktop', 105, NULL),
(723, '2026-09-02 02:12:32', 'read_10s', 30, 1, NULL, 'x.com', 'desktop', 107, NULL),
(724, '2026-08-19 04:12:32', 'read_10s', 30, 1, NULL, 'github.com', 'tablet', 94, NULL),
(725, '2026-08-10 14:12:32', 'read_10s', 30, 1, NULL, 'news.ycombinator.com', 'tablet', 152, NULL),
(726, '2026-08-22 18:12:32', 'read_10s', 30, 1, NULL, 'github.com', 'desktop', 22, NULL),
(727, '2026-08-06 21:12:32', 'read_10s', 30, 1, NULL, 'github.com', 'mobile', 82, NULL),
(728, '2026-09-02 18:12:32', 'read_10s', 30, 1, NULL, 'github.com', 'tablet', 75, NULL),
(729, '2026-09-02 19:12:32', 'view', 30, 1, NULL, 'google.com', 'mobile', 9, NULL),
(730, '2026-08-24 19:12:32', 'read_10s', 30, 1, NULL, 'x.com', 'desktop', 112, NULL),
(731, '2026-08-26 08:12:32', 'read_10s', 30, 1, NULL, 'github.com', 'desktop', 39, NULL),
(732, '2026-08-16 23:12:32', 'read_10s', 30, 1, NULL, 'github.com', 'desktop', 50, NULL),
(733, '2026-08-20 15:12:32', 'read_10s', 30, 1, NULL, 'direct', 'desktop', 52, NULL),
(734, '2026-08-28 04:12:32', 'read_10s', 30, 1, NULL, 'direct', 'desktop', 60, NULL),
(735, '2026-09-04 17:12:32', 'read_10s', 30, 1, NULL, 'x.com', 'desktop', 54, NULL),
(736, '2026-08-20 09:12:32', 'read_10s', 30, 1, NULL, 'x.com', 'tablet', 53, NULL),
(737, '2026-08-12 16:12:32', 'read_10s', 30, 1, NULL, 'google.com', 'mobile', 86, NULL),
(738, '2026-08-23 01:12:32', 'read_10s', 30, 1, NULL, 'x.com', 'desktop', 156, NULL),
(739, '2026-08-22 16:12:32', 'read_10s', 30, 1, NULL, 'direct', 'mobile', 136, NULL),
(740, '2026-08-07 17:12:32', 'read_10s', 31, 1, NULL, 'x.com', 'mobile', 81, NULL),
(741, '2026-08-13 00:12:32', 'read_10s', 31, 1, NULL, 'github.com', 'mobile', 118, NULL),
(742, '2026-08-12 00:12:32', 'read_10s', 31, 1, NULL, 'google.com', 'desktop', 116, NULL),
(743, '2026-09-01 16:12:32', 'read_10s', 31, 1, NULL, 'google.com', 'desktop', 75, NULL),
(744, '2026-08-27 07:12:32', 'read_10s', 31, 1, NULL, 'google.com', 'desktop', 30, NULL),
(745, '2026-09-02 09:12:32', 'read_10s', 31, 1, NULL, 'google.com', 'desktop', 46, NULL),
(746, '2026-08-22 07:12:32', 'read_10s', 31, 1, NULL, 'news.ycombinator.com', 'desktop', 71, NULL),
(747, '2026-08-27 09:12:32', 'read_10s', 31, 1, NULL, 'direct', 'desktop', 174, NULL),
(748, '2026-08-11 06:12:32', 'read_10s', 31, 1, NULL, 'google.com', 'desktop', 123, NULL),
(749, '2026-08-30 00:12:32', 'read_10s', 31, 1, NULL, 'direct', 'mobile', 33, NULL),
(750, '2026-08-09 16:12:32', 'read_10s', 31, 1, NULL, 'news.ycombinator.com', 'tablet', 165, NULL);
INSERT INTO `analytics_events` (`id`, `event_time`, `event_type`, `content_id`, `author_id`, `user_id`, `referrer_domain`, `device_type`, `duration_seconds`, `metadata_json`) VALUES 
(751, '2026-08-20 20:12:32', 'read_10s', 31, 1, NULL, 'x.com', 'desktop', 18, NULL),
(752, '2026-09-03 04:12:32', 'read_10s', 31, 1, NULL, 'google.com', 'tablet', 107, NULL),
(753, '2026-08-21 05:12:32', 'read_10s', 31, 1, NULL, 'google.com', 'mobile', 119, NULL),
(754, '2026-08-15 22:12:32', 'read_10s', 31, 1, NULL, 'github.com', 'desktop', 88, NULL),
(755, '2026-08-19 02:12:32', 'read_10s', 31, 1, NULL, 'google.com', 'desktop', 13, NULL),
(756, '2026-08-20 07:12:32', 'read_10s', 31, 1, NULL, 'direct', 'mobile', 61, NULL),
(757, '2026-09-04 12:12:32', 'read_10s', 31, 1, NULL, 'x.com', 'desktop', 44, NULL),
(758, '2026-08-19 19:12:32', 'read_10s', 31, 1, NULL, 'x.com', 'desktop', 62, NULL),
(759, '2026-08-11 21:12:32', 'read_10s', 31, 1, NULL, 'github.com', 'tablet', 116, NULL),
(760, '2026-08-17 09:12:32', 'read_10s', 31, 1, NULL, 'direct', 'mobile', 143, NULL),
(761, '2026-08-31 20:12:32', 'read_10s', 31, 1, NULL, 'github.com', 'mobile', 152, NULL),
(762, '2026-09-02 06:12:32', 'read_10s', 31, 1, NULL, 'google.com', 'mobile', 175, NULL),
(763, '2026-08-12 03:12:32', 'read_10s', 31, 1, NULL, 'x.com', 'mobile', 61, NULL),
(764, '2026-08-21 23:12:32', 'read_10s', 31, 1, NULL, 'github.com', 'desktop', 158, NULL),
(765, '2026-08-12 03:12:32', 'read_10s', 32, 1, NULL, 'direct', 'mobile', 52, NULL),
(766, '2026-08-25 18:12:32', 'read_10s', 32, 1, NULL, 'news.ycombinator.com', 'desktop', 97, NULL),
(767, '2026-08-31 11:12:32', 'read_10s', 32, 1, NULL, 'google.com', 'desktop', 16, NULL),
(768, '2026-08-09 05:12:32', 'read_10s', 32, 1, NULL, 'direct', 'desktop', 115, NULL),
(769, '2026-08-17 15:12:32', 'read_10s', 32, 1, NULL, 'direct', 'desktop', 140, NULL),
(770, '2026-08-20 09:12:32', 'read_10s', 32, 1, NULL, 'github.com', 'desktop', 161, NULL),
(771, '2026-08-10 18:12:32', 'read_10s', 32, 1, NULL, 'direct', 'tablet', 58, NULL),
(772, '2026-08-24 05:12:32', 'read_10s', 32, 1, NULL, 'news.ycombinator.com', 'desktop', 145, NULL),
(773, '2026-08-10 06:12:32', 'read_10s', 32, 1, NULL, 'x.com', 'mobile', 132, NULL),
(774, '2026-08-23 05:12:32', 'read_10s', 32, 1, NULL, 'x.com', 'desktop', 108, NULL),
(775, '2026-08-15 00:12:32', 'read_10s', 32, 1, NULL, 'google.com', 'mobile', 94, NULL),
(776, '2026-08-27 08:12:32', 'read_10s', 32, 1, NULL, 'google.com', 'desktop', 134, NULL),
(777, '2026-08-08 20:12:32', 'read_10s', 32, 1, NULL, 'x.com', 'mobile', 21, NULL),
(778, '2026-08-23 08:12:32', 'read_10s', 32, 1, NULL, 'google.com', 'desktop', 146, NULL),
(779, '2026-08-08 21:12:32', 'read_10s', 32, 1, NULL, 'news.ycombinator.com', 'desktop', 32, NULL),
(780, '2026-08-09 02:12:32', 'read_10s', 32, 1, NULL, 'x.com', 'mobile', 166, NULL),
(781, '2026-09-04 05:12:32', 'read_10s', 32, 1, NULL, 'google.com', 'mobile', 174, NULL),
(782, '2026-08-13 14:12:32', 'read_10s', 32, 1, NULL, 'x.com', 'mobile', 19, NULL),
(783, '2026-08-13 16:12:32', 'read_10s', 32, 1, NULL, 'x.com', 'desktop', 80, NULL),
(784, '2026-08-13 20:12:32', 'read_10s', 32, 1, NULL, 'github.com', 'tablet', 97, NULL),
(785, '2026-08-07 15:12:32', 'read_10s', 32, 1, NULL, 'direct', 'desktop', 164, NULL),
(786, '2026-09-03 14:12:32', 'read_10s', 32, 1, NULL, 'direct', 'desktop', 134, NULL),
(787, '2026-08-19 04:12:32', 'read_10s', 32, 1, NULL, 'direct', 'desktop', 81, NULL),
(788, '2026-08-20 13:12:32', 'read_10s', 32, 1, NULL, 'x.com', 'tablet', 107, NULL),
(789, '2026-08-11 02:12:32', 'read_10s', 32, 1, NULL, 'x.com', 'desktop', 13, NULL),
(790, '2026-08-21 11:12:32', 'read_10s', 33, 1, NULL, 'github.com', 'mobile', 173, NULL),
(791, '2026-08-22 05:12:32', 'read_10s', 33, 1, NULL, 'x.com', 'desktop', 90, NULL),
(792, '2026-08-29 10:12:32', 'read_10s', 33, 1, NULL, 'x.com', 'desktop', 10, NULL),
(793, '2026-09-02 21:12:32', 'read_10s', 33, 1, NULL, 'x.com', 'desktop', 133, NULL),
(794, '2026-08-24 16:12:32', 'read_10s', 33, 1, NULL, 'google.com', 'desktop', 76, NULL),
(795, '2026-08-10 01:12:32', 'read_10s', 33, 1, NULL, 'github.com', 'mobile', 44, NULL),
(796, '2026-08-19 09:12:32', 'read_10s', 33, 1, NULL, 'news.ycombinator.com', 'desktop', 11, NULL),
(797, '2026-08-31 19:12:32', 'read_10s', 33, 1, NULL, 'news.ycombinator.com', 'mobile', 84, NULL),
(798, '2026-08-10 20:12:32', 'read_10s', 33, 1, NULL, 'google.com', 'tablet', 137, NULL),
(799, '2026-09-01 22:12:32', 'read_10s', 33, 1, NULL, 'github.com', 'desktop', 22, NULL),
(800, '2026-08-18 12:12:32', 'read_10s', 33, 1, NULL, 'x.com', 'desktop', 142, NULL);
INSERT INTO `analytics_events` (`id`, `event_time`, `event_type`, `content_id`, `author_id`, `user_id`, `referrer_domain`, `device_type`, `duration_seconds`, `metadata_json`) VALUES 
(801, '2026-08-16 05:12:32', 'read_10s', 33, 1, NULL, 'news.ycombinator.com', 'desktop', 161, NULL),
(802, '2026-08-19 01:12:32', 'view', 33, 1, NULL, 'google.com', 'desktop', 9, NULL),
(803, '2026-08-12 14:12:32', 'read_10s', 33, 1, NULL, 'x.com', 'mobile', 23, NULL),
(804, '2026-08-08 00:12:32', 'read_10s', 33, 1, NULL, 'direct', 'desktop', 88, NULL),
(805, '2026-08-24 09:12:32', 'read_10s', 33, 1, NULL, 'x.com', 'tablet', 81, NULL),
(806, '2026-08-27 11:12:32', 'read_10s', 33, 1, NULL, 'direct', 'mobile', 162, NULL),
(807, '2026-08-10 09:12:32', 'read_10s', 33, 1, NULL, 'google.com', 'mobile', 13, NULL),
(808, '2026-08-08 02:12:32', 'read_10s', 33, 1, NULL, 'direct', 'mobile', 30, NULL),
(809, '2026-08-20 07:12:32', 'read_10s', 33, 1, NULL, 'google.com', 'mobile', 93, NULL),
(810, '2026-08-09 10:12:32', 'read_10s', 33, 1, NULL, 'direct', 'mobile', 52, NULL),
(811, '2026-09-03 16:12:32', 'view', 33, 1, NULL, 'x.com', 'desktop', 8, NULL),
(812, '2026-08-24 08:12:32', 'read_10s', 33, 1, NULL, 'google.com', 'desktop', 67, NULL),
(813, '2026-08-29 00:12:32', 'read_10s', 33, 1, NULL, 'google.com', 'mobile', 85, NULL),
(814, '2026-08-29 03:12:32', 'read_10s', 33, 1, NULL, 'direct', 'desktop', 173, NULL),
(815, '2026-08-09 15:12:32', 'read_10s', 34, 1, NULL, 'github.com', 'tablet', 61, NULL),
(816, '2026-08-24 14:12:32', 'read_10s', 34, 1, NULL, 'direct', 'desktop', 69, NULL),
(817, '2026-08-22 00:12:32', 'read_10s', 34, 1, NULL, 'google.com', 'mobile', 157, NULL),
(818, '2026-08-06 21:12:32', 'read_10s', 34, 1, NULL, 'x.com', 'desktop', 48, NULL),
(819, '2026-08-18 17:12:32', 'read_10s', 34, 1, NULL, 'direct', 'desktop', 22, NULL),
(820, '2026-08-31 09:12:32', 'read_10s', 34, 1, NULL, 'x.com', 'tablet', 16, NULL),
(821, '2026-09-01 22:12:32', 'read_10s', 34, 1, NULL, 'x.com', 'desktop', 42, NULL),
(822, '2026-08-25 04:12:32', 'read_10s', 34, 1, NULL, 'x.com', 'mobile', 18, NULL),
(823, '2026-08-17 00:12:32', 'read_10s', 34, 1, NULL, 'github.com', 'mobile', 140, NULL),
(824, '2026-08-21 07:12:32', 'read_10s', 34, 1, NULL, 'github.com', 'desktop', 106, NULL),
(825, '2026-08-11 06:12:32', 'read_10s', 34, 1, NULL, 'google.com', 'desktop', 66, NULL),
(826, '2026-08-15 01:12:32', 'read_10s', 34, 1, NULL, 'x.com', 'desktop', 167, NULL),
(827, '2026-08-11 13:12:32', 'read_10s', 34, 1, NULL, 'google.com', 'desktop', 19, NULL),
(828, '2026-08-19 06:12:32', 'read_10s', 34, 1, NULL, 'x.com', 'desktop', 129, NULL),
(829, '2026-08-24 19:12:32', 'read_10s', 34, 1, NULL, 'github.com', 'desktop', 63, NULL),
(830, '2026-08-29 01:12:32', 'read_10s', 34, 1, NULL, 'google.com', 'desktop', 136, NULL),
(831, '2026-08-14 12:12:32', 'read_10s', 34, 1, NULL, 'direct', 'mobile', 82, NULL),
(832, '2026-08-23 06:12:32', 'read_10s', 34, 1, NULL, 'direct', 'tablet', 179, NULL),
(833, '2026-08-30 13:12:32', 'read_10s', 34, 1, NULL, 'direct', 'desktop', 17, NULL),
(834, '2026-08-17 17:12:32', 'read_10s', 34, 1, NULL, 'github.com', 'desktop', 129, NULL),
(835, '2026-08-19 17:12:32', 'read_10s', 34, 1, NULL, 'google.com', 'mobile', 13, NULL),
(836, '2026-08-21 02:12:32', 'read_10s', 34, 1, NULL, 'google.com', 'desktop', 27, NULL),
(837, '2026-08-25 14:12:32', 'read_10s', 34, 1, NULL, 'news.ycombinator.com', 'desktop', 98, NULL),
(838, '2026-08-28 18:12:32', 'read_10s', 34, 1, NULL, 'x.com', 'desktop', 72, NULL),
(839, '2026-08-29 15:12:32', 'read_10s', 34, 1, NULL, 'google.com', 'desktop', 49, NULL),
(840, '2026-08-29 05:12:32', 'read_10s', 35, 1, NULL, 'x.com', 'desktop', 104, NULL),
(841, '2026-08-16 12:12:32', 'read_10s', 35, 1, NULL, 'github.com', 'mobile', 132, NULL),
(842, '2026-08-24 23:12:32', 'read_10s', 35, 1, NULL, 'github.com', 'mobile', 102, NULL),
(843, '2026-08-27 22:12:32', 'read_10s', 35, 1, NULL, 'google.com', 'desktop', 157, NULL),
(844, '2026-08-20 16:12:32', 'read_10s', 35, 1, NULL, 'news.ycombinator.com', 'desktop', 76, NULL),
(845, '2026-08-25 11:12:32', 'read_10s', 35, 1, NULL, 'direct', 'tablet', 162, NULL),
(846, '2026-08-07 00:12:32', 'read_10s', 35, 1, NULL, 'x.com', 'desktop', 42, NULL),
(847, '2026-08-26 07:12:32', 'read_10s', 35, 1, NULL, 'x.com', 'desktop', 99, NULL),
(848, '2026-08-20 14:12:32', 'read_10s', 35, 1, NULL, 'x.com', 'tablet', 55, NULL),
(849, '2026-09-04 14:12:32', 'read_10s', 35, 1, NULL, 'github.com', 'desktop', 112, NULL),
(850, '2026-08-15 18:12:32', 'read_10s', 35, 1, NULL, 'github.com', 'desktop', 49, NULL);
INSERT INTO `analytics_events` (`id`, `event_time`, `event_type`, `content_id`, `author_id`, `user_id`, `referrer_domain`, `device_type`, `duration_seconds`, `metadata_json`) VALUES 
(851, '2026-08-23 05:12:32', 'read_10s', 35, 1, NULL, 'direct', 'desktop', 18, NULL),
(852, '2026-08-28 03:12:32', 'read_10s', 35, 1, NULL, 'news.ycombinator.com', 'tablet', 41, NULL),
(853, '2026-08-07 07:12:32', 'read_10s', 35, 1, NULL, 'direct', 'desktop', 131, NULL),
(854, '2026-08-15 16:12:32', 'read_10s', 35, 1, NULL, 'google.com', 'mobile', 128, NULL),
(855, '2026-09-04 20:12:32', 'read_10s', 35, 1, NULL, 'direct', 'tablet', 39, NULL),
(856, '2026-08-12 04:12:32', 'read_10s', 35, 1, NULL, 'x.com', 'desktop', 85, NULL),
(857, '2026-08-22 20:12:32', 'read_10s', 35, 1, NULL, 'github.com', 'desktop', 51, NULL),
(858, '2026-08-27 21:12:32', 'read_10s', 35, 1, NULL, 'google.com', 'mobile', 77, NULL),
(859, '2026-08-20 03:12:32', 'read_10s', 35, 1, NULL, 'github.com', 'mobile', 28, NULL),
(860, '2026-08-08 13:12:32', 'read_10s', 35, 1, NULL, 'x.com', 'mobile', 24, NULL),
(861, '2026-08-19 01:12:32', 'read_10s', 35, 1, NULL, 'google.com', 'mobile', 161, NULL),
(862, '2026-08-28 12:12:32', 'read_10s', 35, 1, NULL, 'google.com', 'desktop', 80, NULL),
(863, '2026-08-31 02:12:32', 'read_10s', 35, 1, NULL, 'direct', 'tablet', 43, NULL),
(864, '2026-09-01 01:12:32', 'read_10s', 35, 1, NULL, 'google.com', 'desktop', 44, NULL),
(865, '2026-08-10 09:12:32', 'read_10s', 38, 1, NULL, 'google.com', 'desktop', 163, NULL),
(866, '2026-08-26 04:12:32', 'read_10s', 38, 1, NULL, 'x.com', 'mobile', 18, NULL),
(867, '2026-08-22 04:12:32', 'read_10s', 38, 1, NULL, 'github.com', 'mobile', 22, NULL),
(868, '2026-08-09 01:12:32', 'read_10s', 38, 1, NULL, 'x.com', 'desktop', 75, NULL),
(869, '2026-08-08 23:12:32', 'read_10s', 38, 1, NULL, 'x.com', 'tablet', 170, NULL),
(870, '2026-08-16 15:12:32', 'read_10s', 38, 1, NULL, 'google.com', 'desktop', 40, NULL),
(871, '2026-08-20 00:12:32', 'read_10s', 38, 1, NULL, 'github.com', 'desktop', 72, NULL),
(872, '2026-08-21 20:12:32', 'read_10s', 38, 1, NULL, 'direct', 'desktop', 83, NULL),
(873, '2026-08-31 23:12:32', 'read_10s', 38, 1, NULL, 'news.ycombinator.com', 'mobile', 94, NULL),
(874, '2026-08-24 04:12:32', 'read_10s', 38, 1, NULL, 'google.com', 'mobile', 140, NULL),
(875, '2026-08-15 22:12:32', 'read_10s', 38, 1, NULL, 'google.com', 'desktop', 106, NULL),
(876, '2026-08-30 03:12:32', 'read_10s', 38, 1, NULL, 'x.com', 'mobile', 167, NULL),
(877, '2026-08-09 22:12:32', 'read_10s', 38, 1, NULL, 'google.com', 'desktop', 163, NULL),
(878, '2026-08-22 15:12:32', 'read_10s', 38, 1, NULL, 'google.com', 'mobile', 92, NULL),
(879, '2026-08-12 23:12:32', 'read_10s', 38, 1, NULL, 'direct', 'desktop', 63, NULL),
(880, '2026-08-27 05:12:32', 'read_10s', 38, 1, NULL, 'direct', 'mobile', 76, NULL),
(881, '2026-08-10 07:12:32', 'read_10s', 38, 1, NULL, 'direct', 'desktop', 40, NULL),
(882, '2026-08-28 11:12:32', 'read_10s', 38, 1, NULL, 'news.ycombinator.com', 'mobile', 96, NULL),
(883, '2026-08-23 05:12:32', 'read_10s', 38, 1, NULL, 'direct', 'desktop', 178, NULL),
(884, '2026-08-18 21:12:32', 'read_10s', 38, 1, NULL, 'direct', 'desktop', 176, NULL),
(885, '2026-08-18 11:12:32', 'read_10s', 38, 1, NULL, 'github.com', 'desktop', 170, NULL),
(886, '2026-09-01 12:12:32', 'read_10s', 38, 1, NULL, 'google.com', 'mobile', 51, NULL),
(887, '2026-08-13 08:12:32', 'read_10s', 38, 1, NULL, 'github.com', 'desktop', 95, NULL),
(888, '2026-09-03 00:12:32', 'read_10s', 38, 1, NULL, 'google.com', 'mobile', 20, NULL),
(889, '2026-08-23 08:12:32', 'read_10s', 38, 1, NULL, 'google.com', 'mobile', 90, NULL),
(890, '2026-08-18 10:12:32', 'read_10s', 39, 1, NULL, 'direct', 'desktop', 154, NULL),
(891, '2026-08-17 01:12:32', 'read_10s', 39, 1, NULL, 'direct', 'desktop', 118, NULL),
(892, '2026-08-19 17:12:32', 'read_10s', 39, 1, NULL, 'google.com', 'desktop', 131, NULL),
(893, '2026-08-18 13:12:32', 'read_10s', 39, 1, NULL, 'direct', 'mobile', 122, NULL),
(894, '2026-09-02 19:12:32', 'read_10s', 39, 1, NULL, 'github.com', 'mobile', 78, NULL),
(895, '2026-08-28 07:12:32', 'read_10s', 39, 1, NULL, 'google.com', 'mobile', 64, NULL),
(896, '2026-08-20 09:12:32', 'read_10s', 39, 1, NULL, 'direct', 'desktop', 98, NULL),
(897, '2026-08-16 01:12:32', 'read_10s', 39, 1, NULL, 'direct', 'desktop', 62, NULL),
(898, '2026-09-03 23:12:32', 'read_10s', 39, 1, NULL, 'direct', 'desktop', 87, NULL),
(899, '2026-08-09 10:12:32', 'read_10s', 39, 1, NULL, 'github.com', 'desktop', 30, NULL),
(900, '2026-09-02 21:12:32', 'read_10s', 39, 1, NULL, 'direct', 'desktop', 116, NULL);
INSERT INTO `analytics_events` (`id`, `event_time`, `event_type`, `content_id`, `author_id`, `user_id`, `referrer_domain`, `device_type`, `duration_seconds`, `metadata_json`) VALUES 
(901, '2026-08-11 08:12:32', 'read_10s', 39, 1, NULL, 'x.com', 'desktop', 92, NULL),
(902, '2026-08-24 02:12:32', 'read_10s', 39, 1, NULL, 'github.com', 'desktop', 30, NULL),
(903, '2026-08-17 16:12:32', 'read_10s', 39, 1, NULL, 'github.com', 'mobile', 37, NULL),
(904, '2026-09-02 18:12:32', 'read_10s', 39, 1, NULL, 'github.com', 'desktop', 162, NULL),
(905, '2026-08-13 12:12:32', 'read_10s', 39, 1, NULL, 'google.com', 'mobile', 94, NULL),
(906, '2026-08-23 09:12:32', 'read_10s', 39, 1, NULL, 'x.com', 'desktop', 161, NULL),
(907, '2026-08-23 06:12:32', 'read_10s', 39, 1, NULL, 'direct', 'desktop', 93, NULL),
(908, '2026-08-14 00:12:32', 'read_10s', 39, 1, NULL, 'github.com', 'desktop', 101, NULL),
(909, '2026-08-11 09:12:32', 'read_10s', 39, 1, NULL, 'direct', 'desktop', 142, NULL),
(910, '2026-09-02 00:12:32', 'read_10s', 39, 1, NULL, 'github.com', 'desktop', 122, NULL),
(911, '2026-08-31 00:12:32', 'read_10s', 39, 1, NULL, 'news.ycombinator.com', 'desktop', 11, NULL),
(912, '2026-09-01 19:12:32', 'read_10s', 39, 1, NULL, 'google.com', 'desktop', 47, NULL),
(913, '2026-08-19 17:12:32', 'read_10s', 39, 1, NULL, 'direct', 'mobile', 126, NULL),
(914, '2026-09-01 13:12:32', 'read_10s', 39, 1, NULL, 'x.com', 'desktop', 177, NULL),
(915, '2026-08-29 16:12:32', 'read_10s', 41, 1, NULL, 'news.ycombinator.com', 'desktop', 20, NULL),
(916, '2026-08-10 21:12:32', 'read_10s', 41, 1, NULL, 'direct', 'desktop', 16, NULL),
(917, '2026-09-03 09:12:32', 'read_10s', 41, 1, NULL, 'google.com', 'desktop', 156, NULL),
(918, '2026-09-02 21:12:32', 'read_10s', 41, 1, NULL, 'news.ycombinator.com', 'desktop', 83, NULL),
(919, '2026-08-29 08:12:32', 'read_10s', 41, 1, NULL, 'news.ycombinator.com', 'mobile', 27, NULL),
(920, '2026-08-28 01:12:32', 'read_10s', 41, 1, NULL, 'github.com', 'desktop', 118, NULL),
(921, '2026-08-27 23:12:32', 'read_10s', 41, 1, NULL, 'github.com', 'desktop', 108, NULL),
(922, '2026-08-18 21:12:32', 'read_10s', 41, 1, NULL, 'google.com', 'desktop', 45, NULL),
(923, '2026-09-02 21:12:32', 'read_10s', 41, 1, NULL, 'google.com', 'mobile', 95, NULL),
(924, '2026-08-06 22:12:32', 'read_10s', 41, 1, NULL, 'google.com', 'tablet', 19, NULL),
(925, '2026-08-30 11:12:32', 'read_10s', 41, 1, NULL, 'x.com', 'desktop', 68, NULL),
(926, '2026-08-18 11:12:32', 'view', 41, 1, NULL, 'google.com', 'desktop', 8, NULL),
(927, '2026-08-10 22:12:32', 'read_10s', 41, 1, NULL, 'github.com', 'desktop', 157, NULL),
(928, '2026-08-21 00:12:32', 'read_10s', 41, 1, NULL, 'x.com', 'mobile', 178, NULL),
(929, '2026-09-01 21:12:32', 'read_10s', 41, 1, NULL, 'direct', 'desktop', 12, NULL),
(930, '2026-08-21 14:12:32', 'read_10s', 41, 1, NULL, 'x.com', 'desktop', 39, NULL),
(931, '2026-08-12 10:12:32', 'read_10s', 41, 1, NULL, 'github.com', 'desktop', 86, NULL),
(932, '2026-08-13 10:12:32', 'read_10s', 41, 1, NULL, 'google.com', 'desktop', 142, NULL),
(933, '2026-08-27 09:12:32', 'read_10s', 41, 1, NULL, 'news.ycombinator.com', 'desktop', 55, NULL),
(934, '2026-08-11 02:12:32', 'read_10s', 41, 1, NULL, 'direct', 'desktop', 104, NULL),
(935, '2026-08-24 21:12:32', 'read_10s', 41, 1, NULL, 'github.com', 'mobile', 164, NULL),
(936, '2026-09-02 07:12:32', 'read_10s', 41, 1, NULL, 'github.com', 'tablet', 66, NULL),
(937, '2026-08-30 15:12:32', 'read_10s', 41, 1, NULL, 'news.ycombinator.com', 'desktop', 81, NULL),
(938, '2026-08-24 05:12:32', 'read_10s', 41, 1, NULL, 'google.com', 'desktop', 18, NULL),
(939, '2026-08-08 11:12:32', 'read_10s', 41, 1, NULL, 'github.com', 'mobile', 135, NULL),
(940, '2026-08-21 15:12:32', 'read_10s', 42, 1, NULL, 'github.com', 'desktop', 81, NULL),
(941, '2026-08-21 14:12:32', 'read_10s', 42, 1, NULL, 'google.com', 'desktop', 163, NULL),
(942, '2026-09-04 01:12:32', 'read_10s', 42, 1, NULL, 'direct', 'desktop', 128, NULL),
(943, '2026-08-31 06:12:32', 'read_10s', 42, 1, NULL, 'x.com', 'desktop', 76, NULL),
(944, '2026-08-08 05:12:32', 'read_10s', 42, 1, NULL, 'news.ycombinator.com', 'desktop', 139, NULL),
(945, '2026-08-29 02:12:32', 'read_10s', 42, 1, NULL, 'x.com', 'desktop', 147, NULL),
(946, '2026-08-19 18:12:32', 'read_10s', 42, 1, NULL, 'google.com', 'desktop', 173, NULL),
(947, '2026-08-11 14:12:32', 'read_10s', 42, 1, NULL, 'google.com', 'desktop', 36, NULL),
(948, '2026-08-26 12:12:32', 'read_10s', 42, 1, NULL, 'google.com', 'desktop', 115, NULL),
(949, '2026-08-21 18:12:32', 'read_10s', 42, 1, NULL, 'direct', 'mobile', 146, NULL),
(950, '2026-09-01 13:12:32', 'read_10s', 42, 1, NULL, 'direct', 'mobile', 121, NULL);
INSERT INTO `analytics_events` (`id`, `event_time`, `event_type`, `content_id`, `author_id`, `user_id`, `referrer_domain`, `device_type`, `duration_seconds`, `metadata_json`) VALUES 
(951, '2026-09-03 12:12:32', 'read_10s', 42, 1, NULL, 'direct', 'tablet', 15, NULL),
(952, '2026-09-02 23:12:32', 'read_10s', 42, 1, NULL, 'github.com', 'desktop', 48, NULL),
(953, '2026-08-29 16:12:32', 'read_10s', 42, 1, NULL, 'direct', 'desktop', 33, NULL),
(954, '2026-08-19 16:12:32', 'read_10s', 42, 1, NULL, 'news.ycombinator.com', 'mobile', 172, NULL),
(955, '2026-08-12 00:12:32', 'read_10s', 42, 1, NULL, 'github.com', 'desktop', 175, NULL),
(956, '2026-09-04 18:12:32', 'read_10s', 42, 1, NULL, 'direct', 'mobile', 57, NULL),
(957, '2026-08-27 20:12:32', 'read_10s', 42, 1, NULL, 'google.com', 'desktop', 53, NULL),
(958, '2026-08-12 23:12:32', 'read_10s', 42, 1, NULL, 'direct', 'mobile', 117, NULL),
(959, '2026-09-01 17:12:32', 'read_10s', 42, 1, NULL, 'x.com', 'mobile', 156, NULL),
(960, '2026-08-13 09:12:32', 'read_10s', 42, 1, NULL, 'x.com', 'mobile', 56, NULL),
(961, '2026-08-09 05:12:32', 'read_10s', 42, 1, NULL, 'x.com', 'mobile', 52, NULL),
(962, '2026-08-14 12:12:32', 'read_10s', 42, 1, NULL, 'direct', 'desktop', 96, NULL),
(963, '2026-08-15 23:12:32', 'read_10s', 42, 1, NULL, 'x.com', 'tablet', 128, NULL),
(964, '2026-09-02 23:12:32', 'read_10s', 42, 1, NULL, 'x.com', 'desktop', 139, NULL),
(965, '2026-09-04 23:21:14', 'read_10s', 2, 3, NULL, 'direct', 'desktop', 10, '{\"verified_monetization\":true,\"ip\":\"10.0.0.240\"}'),
(966, '2026-09-04 23:21:14', 'read_10s', 2, 3, NULL, 'direct', 'desktop', 10, '{\"verified_monetization\":true,\"ip\":\"10.0.0.240\"}'),
(967, '2026-09-04 23:22:03', 'read_10s', 2, 3, 9999, 'direct', 'desktop', 10, '{\"verified_monetization\":true,\"ip\":\"10.0.0.11\",\"user_id\":9999}'),
(968, '2026-09-04 23:26:07', 'read_10s', 2, 3, 95545, 'direct', 'desktop', 10, '{\"verified_monetization\":true,\"ip\":\"10.0.0.236\",\"user_id\":95545}'),
(969, '2026-09-04 23:37:45', 'read_10s', 38, 1, 12, 'localhost', 'desktop', 10, '{\"verified_monetization\":true,\"ip\":\"::1\",\"user_id\":12}'),
(970, '2026-08-31 09:41:17', 'read_10s', 46, NULL, NULL, '/search', 'mobile', 135, NULL),
(971, '2026-08-31 09:41:17', 'read_10s', 46, NULL, NULL, '/home', 'mobile', 144, NULL),
(972, '2026-08-31 09:41:17', 'read_10s', 46, NULL, NULL, '/home', 'desktop', 288, NULL),
(973, '2026-09-04 09:41:17', 'read_10s', 46, NULL, NULL, '/directory/articles', 'desktop', 86, NULL),
(974, '2026-09-05 09:41:17', 'read_10s', 46, NULL, NULL, '/topic/llm-engineering', 'desktop', 243, NULL),
(975, '2026-09-01 09:41:17', 'read_10s', 46, NULL, NULL, '/directory/gpts', 'desktop', 331, NULL),
(976, '2026-09-01 09:41:17', 'read_10s', 46, NULL, NULL, '/directory/prompts', 'desktop', 278, NULL),
(977, '2026-09-02 09:41:17', 'read_10s', 46, NULL, NULL, '/directory/gpts', 'desktop', 285, NULL),
(978, '2026-09-04 09:41:17', 'read_10s', 46, NULL, NULL, '/topic/llm-engineering', 'mobile', 172, NULL),
(979, '2026-09-03 09:41:17', 'read_10s', 46, NULL, NULL, '/directory/articles', 'desktop', 279, NULL),
(980, '2026-09-03 09:41:17', 'read_10s', 46, NULL, NULL, '/directory/gpts', 'desktop', 272, NULL),
(981, '2026-09-05 09:41:17', 'read_10s', 46, NULL, NULL, '/campaigns', 'mobile', 355, NULL),
(982, '2026-09-05 09:41:17', 'read_10s', 46, NULL, NULL, '/directory/articles', 'mobile', 81, NULL),
(983, '2026-09-03 09:41:17', 'read_10s', 46, NULL, NULL, '/campaigns', 'desktop', 243, NULL),
(984, '2026-09-01 09:41:17', 'read_10s', 46, NULL, NULL, '/home', 'desktop', 239, NULL),
(985, '2026-09-05 15:05:33', 'read_10s', 2, 3, 12, 'localhost', 'mobile', 10, '{\"verified_monetization\":true,\"ip\":\"::1\",\"user_id\":12}');

--
-- Structure for table `api_keys`
--
DROP TABLE IF EXISTS `api_keys`;
CREATE TABLE `api_keys` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `key_name` varchar(100) NOT NULL,
  `api_key_prefix` varchar(16) NOT NULL,
  `api_key_hash` varchar(128) NOT NULL,
  `scopes_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`scopes_json`)),
  `rate_limit_per_min` int(11) NOT NULL DEFAULT 60,
  `requests_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `last_used_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ak_user` (`user_id`),
  KEY `idx_ak_prefix` (`api_key_prefix`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure for table `article_co_authors`
--
DROP TABLE IF EXISTS `article_co_authors`;
CREATE TABLE `article_co_authors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int(10) unsigned NOT NULL,
  `author_id` int(10) unsigned NOT NULL,
  `role` enum('lead_author','co_author','technical_reviewer') NOT NULL DEFAULT 'co_author',
  `revenue_share_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `invitation_status` enum('pending','accepted','declined') NOT NULL DEFAULT 'accepted',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_article_author` (`article_id`,`author_id`),
  KEY `idx_aca_article` (`article_id`),
  KEY `idx_aca_author` (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure for table `articles`
--
DROP TABLE IF EXISTS `articles`;
CREATE TABLE `articles` (
  `content_id` bigint(20) unsigned NOT NULL,
  `reading_time_mins` smallint(5) unsigned NOT NULL DEFAULT 5,
  `difficulty_level` enum('beginner','intermediate','advanced','expert') NOT NULL DEFAULT 'intermediate',
  `table_of_contents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`table_of_contents`)),
  `co_authors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`co_authors`)),
  `editor_attribution` varchar(100) DEFAULT NULL,
  `citation_sources` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`citation_sources`)),
  `code_snippets_count` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`content_id`),
  CONSTRAINT `fk_articles_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `articles` (11 rows)
--
INSERT INTO `articles` (`content_id`, `reading_time_mins`, `difficulty_level`, `table_of_contents`, `co_authors`, `editor_attribution`, `citation_sources`, `code_snippets_count`) VALUES 
(2, 6, 'intermediate', NULL, NULL, NULL, NULL, 1),
(3, 9, 'advanced', NULL, NULL, NULL, NULL, 3),
(4, 11, 'expert', NULL, NULL, NULL, NULL, 2),
(5, 7, 'intermediate', NULL, NULL, NULL, NULL, 2),
(6, 6, 'intermediate', NULL, NULL, NULL, NULL, 2),
(7, 10, 'advanced', NULL, NULL, NULL, NULL, 3),
(36, 1, 'intermediate', NULL, NULL, NULL, NULL, 0),
(37, 1, 'intermediate', NULL, NULL, NULL, NULL, 0),
(38, 17, 'intermediate', '[{\"level\":2,\"title\":\"AURA \\u2014 Professional Product &amp; Development Specification\",\"slug\":\"aura-professional-product-amp-development-specification\"},{\"level\":2,\"title\":\"1. Executive Vision\",\"slug\":\"1-executive-vision\"},{\"level\":2,\"title\":\"2. Product Principles\",\"slug\":\"2-product-principles\"},{\"level\":2,\"title\":\"3. Recommended Technology Strategy\",\"slug\":\"3-recommended-technology-strategy\"},{\"level\":2,\"title\":\"4. High-Level Architecture\",\"slug\":\"4-high-level-architecture\"},{\"level\":2,\"title\":\"4.1 Core Services\",\"slug\":\"4-1-core-services\"},{\"level\":2,\"title\":\"5. Professional Project Structure\",\"slug\":\"5-professional-project-structure\"},{\"level\":2,\"title\":\"6. Voice System\",\"slug\":\"6-voice-system\"},{\"level\":2,\"title\":\"6.1 Wake Word\",\"slug\":\"6-1-wake-word\"},{\"level\":2,\"title\":\"6.2 Speech-to-Text\",\"slug\":\"6-2-speech-to-text\"},{\"level\":2,\"title\":\"6.3 Voice Activity Detection\",\"slug\":\"6-3-voice-activity-detection\"},{\"level\":2,\"title\":\"6.4 Text-to-Speech\",\"slug\":\"6-4-text-to-speech\"},{\"level\":2,\"title\":\"7. AI Brain\",\"slug\":\"7-ai-brain\"},{\"level\":2,\"title\":\"7.1 AI Routing\",\"slug\":\"7-1-ai-routing\"},{\"level\":2,\"title\":\"7.2 Local Model Strategy\",\"slug\":\"7-2-local-model-strategy\"},{\"level\":2,\"title\":\"7.3 Context Engine\",\"slug\":\"7-3-context-engine\"},{\"level\":2,\"title\":\"8. Memory Architecture\",\"slug\":\"8-memory-architecture\"},{\"level\":2,\"title\":\"9. Skill System\",\"slug\":\"9-skill-system\"},{\"level\":2,\"title\":\"10. Desktop Control\",\"slug\":\"10-desktop-control\"},{\"level\":2,\"title\":\"11. Browser Assistant\",\"slug\":\"11-browser-assistant\"},{\"level\":2,\"title\":\"12. Computer Vision\",\"slug\":\"12-computer-vision\"},{\"level\":2,\"title\":\"13. Files &amp; Document Intelligence\",\"slug\":\"13-files-amp-document-intelligence\"},{\"level\":2,\"title\":\"14. Terminal &amp; Coding Assistant\",\"slug\":\"14-terminal-amp-coding-assistant\"},{\"level\":2,\"title\":\"15. Productivity Suite\",\"slug\":\"15-productivity-suite\"},{\"level\":2,\"title\":\"16. Automation Engine\",\"slug\":\"16-automation-engine\"},{\"level\":2,\"title\":\"16.1 Triggers\",\"slug\":\"16-1-triggers\"},{\"level\":2,\"title\":\"16.2 Conditions\",\"slug\":\"16-2-conditions\"},{\"level\":2,\"title\":\"16.3 Actions\",\"slug\":\"16-3-actions\"},{\"level\":2,\"title\":\"16.4 Natural-Language Automation\",\"slug\":\"16-4-natural-language-automation\"},{\"level\":2,\"title\":\"17. Agent\\/Mission System\",\"slug\":\"17-agent-mission-system\"},{\"level\":2,\"title\":\"18. Security &amp; Permission Model\",\"slug\":\"18-security-amp-permission-model\"},{\"level\":2,\"title\":\"19. Privacy\",\"slug\":\"19-privacy\"},{\"level\":2,\"title\":\"20. SaaS-Level UI\",\"slug\":\"20-saas-level-ui\"},{\"level\":2,\"title\":\"20.1 Main Pages\",\"slug\":\"20-1-main-pages\"},{\"level\":2,\"title\":\"20.2 Dashboard\",\"slug\":\"20-2-dashboard\"},{\"level\":2,\"title\":\"20.3 Floating Orb\",\"slug\":\"20-3-floating-orb\"},{\"level\":2,\"title\":\"20.4 Command Center\",\"slug\":\"20-4-command-center\"},{\"level\":2,\"title\":\"20.5 Design System\",\"slug\":\"20-5-design-system\"},{\"level\":2,\"title\":\"21. UI State Model\",\"slug\":\"21-ui-state-model\"},{\"level\":2,\"title\":\"22. Database Design\",\"slug\":\"22-database-design\"},{\"level\":2,\"title\":\"23. Event-Driven Design\",\"slug\":\"23-event-driven-design\"},{\"level\":2,\"title\":\"24. Error Handling\",\"slug\":\"24-error-handling\"},{\"level\":2,\"title\":\"25. Logging &amp; Observability\",\"slug\":\"25-logging-amp-observability\"},{\"level\":2,\"title\":\"26. Testing Strategy\",\"slug\":\"26-testing-strategy\"},{\"level\":2,\"title\":\"26.1 Unit Tests\",\"slug\":\"26-1-unit-tests\"},{\"level\":2,\"title\":\"26.2 Integration Tests\",\"slug\":\"26-2-integration-tests\"},{\"level\":2,\"title\":\"26.3 End-to-End Tests\",\"slug\":\"26-3-end-to-end-tests\"},{\"level\":2,\"title\":\"26.4 Safety Tests\",\"slug\":\"26-4-safety-tests\"},{\"level\":2,\"title\":\"27. Performance Engineering\",\"slug\":\"27-performance-engineering\"},{\"level\":2,\"title\":\"28. Offline\\/Online Architecture\",\"slug\":\"28-offline-online-architecture\"},{\"level\":2,\"title\":\"29. Model Manager\",\"slug\":\"29-model-manager\"},{\"level\":2,\"title\":\"30. Plugin Architecture\",\"slug\":\"30-plugin-architecture\"},{\"level\":2,\"title\":\"31. Developer SDK\",\"slug\":\"31-developer-sdk\"},{\"level\":2,\"title\":\"32. Feature Catalog \\u2014 220 Capabilities\",\"slug\":\"32-feature-catalog-220-capabilities\"},{\"level\":2,\"title\":\"Core Voice\",\"slug\":\"core-voice\"},{\"level\":2,\"title\":\"AI\",\"slug\":\"ai\"},{\"level\":2,\"title\":\"Desktop\",\"slug\":\"desktop\"},{\"level\":2,\"title\":\"Browser\",\"slug\":\"browser\"},{\"level\":2,\"title\":\"Vision\\/OCR\",\"slug\":\"vision-ocr\"},{\"level\":2,\"title\":\"Clipboard\",\"slug\":\"clipboard\"},{\"level\":2,\"title\":\"Productivity\",\"slug\":\"productivity\"},{\"level\":2,\"title\":\"Documents\",\"slug\":\"documents\"},{\"level\":2,\"title\":\"Coding\",\"slug\":\"coding\"},{\"level\":2,\"title\":\"System\",\"slug\":\"system\"},{\"level\":2,\"title\":\"Automation\",\"slug\":\"automation\"},{\"level\":2,\"title\":\"Memory\",\"slug\":\"memory\"},{\"level\":2,\"title\":\"Security\",\"slug\":\"security\"},{\"level\":2,\"title\":\"UI\",\"slug\":\"ui\"},{\"level\":2,\"title\":\"Advanced Agents\",\"slug\":\"advanced-agents\"},{\"level\":2,\"title\":\"Extensibility\",\"slug\":\"extensibility\"},{\"level\":2,\"title\":\"33. Development Roadmap\",\"slug\":\"33-development-roadmap\"},{\"level\":2,\"title\":\"Phase 0 \\u2014 Product Foundation\",\"slug\":\"phase-0-product-foundation\"},{\"level\":2,\"title\":\"Phase 1 \\u2014 MVP Voice Assistant\",\"slug\":\"phase-1-mvp-voice-assistant\"},{\"level\":2,\"title\":\"Phase 2 \\u2014 Computer Control\",\"slug\":\"phase-2-computer-control\"},{\"level\":2,\"title\":\"Phase 3 \\u2014 Productivity\",\"slug\":\"phase-3-productivity\"},{\"level\":2,\"title\":\"Phase 4 \\u2014 AI Knowledge\",\"slug\":\"phase-4-ai-knowledge\"},{\"level\":2,\"title\":\"Phase 5 \\u2014 Automation\",\"slug\":\"phase-5-automation\"},{\"level\":2,\"title\":\"Phase 6 \\u2014 Agents\",\"slug\":\"phase-6-agents\"},{\"level\":2,\"title\":\"Phase 7 \\u2014 Browser &amp; Advanced Integrations\",\"slug\":\"phase-7-browser-amp-advanced-integrations\"},{\"level\":2,\"title\":\"Phase 8 \\u2014 Professionalization\",\"slug\":\"phase-8-professionalization\"},{\"level\":2,\"title\":\"34. Professional Engineering Workflow\",\"slug\":\"34-professional-engineering-workflow\"},{\"level\":2,\"title\":\"35. Acceptance Criteria for a Professional Release\",\"slug\":\"35-acceptance-criteria-for-a-professional-release\"},{\"level\":2,\"title\":\"36. Important Scope Decision\",\"slug\":\"36-important-scope-decision\"},{\"level\":2,\"title\":\"37. Suggested MVP\",\"slug\":\"37-suggested-mvp\"},{\"level\":2,\"title\":\"38. Long-Term AURA Vision\",\"slug\":\"38-long-term-aura-vision\"},{\"level\":2,\"title\":\"39. Final Build Philosophy\",\"slug\":\"39-final-build-philosophy\"}]', NULL, NULL, NULL, 0),
(44, 2, 'intermediate', NULL, NULL, NULL, NULL, 0),
(45, 1, 'intermediate', '[{\"level\":2,\"title\":\"1. Architecture Overview &amp; Problem Statement\",\"slug\":\"1-architecture-overview-amp-problem-statement\"},{\"level\":2,\"title\":\"2. Implementation Blueprint\",\"slug\":\"2-implementation-blueprint\"},{\"level\":2,\"title\":\"3. Benchmarks &amp; Key Takeaways\",\"slug\":\"3-benchmarks-amp-key-takeaways\"}]', NULL, NULL, NULL, 0);

--
-- Structure for table `audit_logs`
--
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `actor_role` varchar(50) DEFAULT NULL,
  `action` varchar(80) NOT NULL,
  `action_domain` varchar(50) DEFAULT 'general',
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `country` varchar(100) DEFAULT 'Pakistan',
  `device_info` varchar(255) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `before_state` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`before_state`)),
  `after_state` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`after_state`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_audit_created` (`created_at`),
  KEY `idx_audit_filter` (`created_at`,`action`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs` (132 rows)
--
INSERT INTO `audit_logs` (`id`, `user_id`, `actor_role`, `action`, `action_domain`, `entity_type`, `entity_id`, `ip_address`, `country`, `device_info`, `user_agent`, `details`, `before_state`, `after_state`, `created_at`) VALUES 
(1, 2, 'author', 'admin.impersonate_start', 'general', 'users', 2, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"admin_id\":1,\"target_user_id\":2,\"target_username\":\"alexvance\",\"target_role\":\"author\"}', NULL, NULL, '2026-09-04 21:17:45'),
(2, 1, 'super_admin', 'admin.impersonate_end', 'general', 'users', 2, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"admin_id\":1,\"reverted_target_user_id\":2}', NULL, NULL, '2026-09-04 21:17:46'),
(3, 1, 'super_admin', 'user.strike_issued', 'general', 'author_strikes', 1, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_level\":1,\"penalty_type\":\"Minor guideline notice\",\"reason\":\"warning\",\"expires_at\":\"Test notes 1\",\"issued_by\":1}', NULL, NULL, '2026-09-04 21:17:46'),
(4, 1, 'super_admin', 'session.all_remote_terminated', 'general', 'users', 2, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"revoked_count\":1}', NULL, NULL, '2026-09-04 21:17:46'),
(5, 1, 'super_admin', 'user.strike_issued', 'general', 'author_strikes', 2, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_level\":2,\"penalty_type\":\"Repeated policy violation\",\"reason\":\"suspension\",\"expires_at\":\"Test notes 2\",\"issued_by\":1}', NULL, NULL, '2026-09-04 21:17:46'),
(6, 1, 'super_admin', 'session.all_remote_terminated', 'general', 'users', 2, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"revoked_count\":0}', NULL, NULL, '2026-09-04 21:17:46'),
(7, 1, 'super_admin', 'user.strike_issued', 'general', 'author_strikes', 3, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_level\":3,\"penalty_type\":\"Severe plagiarism and harassment\",\"reason\":\"ban\",\"expires_at\":\"Permanent ban issued\",\"issued_by\":1}', NULL, NULL, '2026-09-04 21:17:46'),
(8, 1, 'super_admin', 'user.strike_pardoned', 'general', 'author_strikes', 1, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_id\":1,\"pardoned_by\":1,\"remaining_active_strikes\":2}', NULL, NULL, '2026-09-04 21:17:46'),
(9, 1, 'super_admin', 'user.strike_pardoned', 'general', 'author_strikes', 2, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_id\":2,\"pardoned_by\":1,\"remaining_active_strikes\":1}', NULL, NULL, '2026-09-04 21:17:46'),
(10, 1, 'super_admin', 'user.strike_pardoned', 'general', 'author_strikes', 3, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_id\":3,\"pardoned_by\":1,\"remaining_active_strikes\":0}', NULL, NULL, '2026-09-04 21:17:46'),
(11, 2, 'author', 'admin.impersonate_start', 'general', 'users', 2, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"admin_id\":1,\"target_user_id\":2,\"target_username\":\"alexvance\",\"target_role\":\"author\"}', NULL, NULL, '2026-09-04 21:19:15'),
(12, 1, 'super_admin', 'admin.impersonate_end', 'general', 'users', 2, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"admin_id\":1,\"reverted_target_user_id\":2}', NULL, NULL, '2026-09-04 21:19:15'),
(13, 1, 'super_admin', 'user.strike_issued', 'general', 'author_strikes', 4, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_level\":1,\"penalty_type\":\"warning\",\"reason\":\"Minor guideline notice\",\"expires_at\":\"2026-10-04 18:19:15\",\"issued_by\":1}', NULL, NULL, '2026-09-04 21:19:15'),
(14, 1, 'super_admin', 'session.all_remote_terminated', 'general', 'users', 2, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"revoked_count\":1}', NULL, NULL, '2026-09-04 21:19:15'),
(15, 1, 'super_admin', 'user.strike_issued', 'general', 'author_strikes', 5, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_level\":2,\"penalty_type\":\"suspension\",\"reason\":\"Repeated policy violation\",\"expires_at\":\"2026-09-18 18:19:15\",\"issued_by\":1}', NULL, NULL, '2026-09-04 21:19:15'),
(16, 1, 'super_admin', 'session.all_remote_terminated', 'general', 'users', 2, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"revoked_count\":0}', NULL, NULL, '2026-09-04 21:19:15'),
(17, 1, 'super_admin', 'user.strike_issued', 'general', 'author_strikes', 6, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_level\":3,\"penalty_type\":\"ban\",\"reason\":\"Severe plagiarism and harassment\",\"expires_at\":null,\"issued_by\":1}', NULL, NULL, '2026-09-04 21:19:15'),
(18, 1, 'super_admin', 'user.strike_pardoned', 'general', 'author_strikes', 4, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_id\":4,\"pardoned_by\":1,\"remaining_active_strikes\":2}', NULL, NULL, '2026-09-04 21:19:15'),
(19, 1, 'super_admin', 'user.strike_pardoned', 'general', 'author_strikes', 5, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_id\":5,\"pardoned_by\":1,\"remaining_active_strikes\":1}', NULL, NULL, '2026-09-04 21:19:15'),
(20, 1, 'super_admin', 'user.strike_pardoned', 'general', 'author_strikes', 6, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_id\":6,\"pardoned_by\":1,\"remaining_active_strikes\":0}', NULL, NULL, '2026-09-04 21:19:15'),
(21, 1, 'super_admin', 'session.all_remote_terminated', 'general', 'users', 14, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":14,\"revoked_count\":0}', NULL, NULL, '2026-09-04 21:19:15'),
(22, 1, 'super_admin', 'admin.merge_users', 'general', 'users', 15, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"source_user_id\":14,\"source_username\":\"merge_src_1788538755_797\",\"target_user_id\":15,\"target_username\":\"merge_tgt_1788538755_797\",\"reassigned_summary\":{\"contents\":0,\"comments\":1,\"bookmarks\":1,\"reading_history\":0,\"reactions\":0,\"notifications\":0,\"support_tickets\":0,\"user_subscriptions\":0},\"merged_by\":1}', NULL, NULL, '2026-09-04 21:19:15'),
(23, 1, 'super_admin', 'admin.generate_reset_link', 'general', 'users', 1, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":1,\"expires_at\":\"2026-09-05 18:19:15\"}', NULL, NULL, '2026-09-04 21:19:15'),
(24, 2, 'author', 'admin.impersonate_start', 'general', 'users', 2, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"admin_id\":1,\"target_user_id\":2,\"target_username\":\"alexvance\",\"target_role\":\"author\"}', NULL, NULL, '2026-09-04 21:19:50'),
(25, 1, 'super_admin', 'admin.impersonate_end', 'general', 'users', 2, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"admin_id\":1,\"reverted_target_user_id\":2}', NULL, NULL, '2026-09-04 21:19:50'),
(26, 1, 'super_admin', 'user.strike_issued', 'general', 'author_strikes', 7, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_level\":1,\"penalty_type\":\"warning\",\"reason\":\"Minor guideline notice\",\"expires_at\":\"2026-10-04 18:19:50\",\"issued_by\":1}', NULL, NULL, '2026-09-04 21:19:50'),
(27, 1, 'super_admin', 'session.all_remote_terminated', 'general', 'users', 2, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"revoked_count\":1}', NULL, NULL, '2026-09-04 21:19:50'),
(28, 1, 'super_admin', 'user.strike_issued', 'general', 'author_strikes', 8, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_level\":2,\"penalty_type\":\"suspension\",\"reason\":\"Repeated policy violation\",\"expires_at\":\"2026-09-18 18:19:50\",\"issued_by\":1}', NULL, NULL, '2026-09-04 21:19:50'),
(29, 1, 'super_admin', 'session.all_remote_terminated', 'general', 'users', 2, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"revoked_count\":0}', NULL, NULL, '2026-09-04 21:19:50'),
(30, 1, 'super_admin', 'user.strike_issued', 'general', 'author_strikes', 9, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_level\":3,\"penalty_type\":\"ban\",\"reason\":\"Severe plagiarism and harassment\",\"expires_at\":null,\"issued_by\":1}', NULL, NULL, '2026-09-04 21:19:50'),
(31, 1, 'super_admin', 'user.strike_pardoned', 'general', 'author_strikes', 7, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_id\":7,\"pardoned_by\":1,\"remaining_active_strikes\":2}', NULL, NULL, '2026-09-04 21:19:50'),
(32, 1, 'super_admin', 'user.strike_pardoned', 'general', 'author_strikes', 8, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_id\":8,\"pardoned_by\":1,\"remaining_active_strikes\":1}', NULL, NULL, '2026-09-04 21:19:50'),
(33, 1, 'super_admin', 'user.strike_pardoned', 'general', 'author_strikes', 9, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_id\":9,\"pardoned_by\":1,\"remaining_active_strikes\":0}', NULL, NULL, '2026-09-04 21:19:50'),
(34, 1, 'super_admin', 'session.all_remote_terminated', 'general', 'users', 16, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":16,\"revoked_count\":0}', NULL, NULL, '2026-09-04 21:19:50'),
(35, 1, 'super_admin', 'admin.merge_users', 'general', 'users', 17, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"source_user_id\":16,\"source_username\":\"merge_src_1788538790_789\",\"target_user_id\":17,\"target_username\":\"merge_tgt_1788538790_789\",\"reassigned_summary\":{\"contents\":0,\"comments\":1,\"bookmarks\":1,\"reading_history\":0,\"reactions\":0,\"notifications\":0,\"support_tickets\":0,\"user_subscriptions\":0},\"merged_by\":1}', NULL, NULL, '2026-09-04 21:19:50'),
(36, 1, 'super_admin', 'admin.generate_reset_link', 'general', 'users', 1, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":1,\"expires_at\":\"2026-09-05 18:19:50\"}', NULL, NULL, '2026-09-04 21:19:50'),
(37, 2, 'author', 'admin.impersonate_start', 'general', 'users', 2, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"admin_id\":1,\"target_user_id\":2,\"target_username\":\"alexvance\",\"target_role\":\"author\"}', NULL, NULL, '2026-09-04 21:20:26'),
(38, 1, 'super_admin', 'admin.impersonate_end', 'general', 'users', 2, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"admin_id\":1,\"reverted_target_user_id\":2}', NULL, NULL, '2026-09-04 21:20:26'),
(39, 1, 'super_admin', 'user.strike_issued', 'general', 'author_strikes', 10, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_level\":1,\"penalty_type\":\"warning\",\"reason\":\"Minor guideline notice\",\"expires_at\":\"2026-10-04 18:20:26\",\"issued_by\":1}', NULL, NULL, '2026-09-04 21:20:26'),
(40, 1, 'super_admin', 'session.all_remote_terminated', 'general', 'users', 2, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"revoked_count\":1}', NULL, NULL, '2026-09-04 21:20:26'),
(41, 1, 'super_admin', 'user.strike_issued', 'general', 'author_strikes', 11, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_level\":2,\"penalty_type\":\"suspension\",\"reason\":\"Repeated policy violation\",\"expires_at\":\"2026-09-18 18:20:26\",\"issued_by\":1}', NULL, NULL, '2026-09-04 21:20:26'),
(42, 1, 'super_admin', 'session.all_remote_terminated', 'general', 'users', 2, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"revoked_count\":0}', NULL, NULL, '2026-09-04 21:20:26'),
(43, 1, 'super_admin', 'user.strike_issued', 'general', 'author_strikes', 12, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_level\":3,\"penalty_type\":\"ban\",\"reason\":\"Severe plagiarism and harassment\",\"expires_at\":null,\"issued_by\":1}', NULL, NULL, '2026-09-04 21:20:26'),
(44, 1, 'super_admin', 'user.strike_pardoned', 'general', 'author_strikes', 10, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_id\":10,\"pardoned_by\":1,\"remaining_active_strikes\":2}', NULL, NULL, '2026-09-04 21:20:26'),
(45, 1, 'super_admin', 'user.strike_pardoned', 'general', 'author_strikes', 11, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_id\":11,\"pardoned_by\":1,\"remaining_active_strikes\":1}', NULL, NULL, '2026-09-04 21:20:26'),
(46, 1, 'super_admin', 'user.strike_pardoned', 'general', 'author_strikes', 12, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_id\":12,\"pardoned_by\":1,\"remaining_active_strikes\":0}', NULL, NULL, '2026-09-04 21:20:26'),
(47, 1, 'super_admin', 'session.all_remote_terminated', 'general', 'users', 18, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":18,\"revoked_count\":0}', NULL, NULL, '2026-09-04 21:20:27'),
(48, 1, 'super_admin', 'admin.merge_users', 'general', 'users', 19, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"source_user_id\":18,\"source_username\":\"merge_src_1788538826_156\",\"target_user_id\":19,\"target_username\":\"merge_tgt_1788538826_156\",\"reassigned_summary\":{\"contents\":0,\"comments\":1,\"bookmarks\":1,\"reading_history\":0,\"reactions\":0,\"notifications\":0,\"support_tickets\":0,\"user_subscriptions\":0},\"merged_by\":1}', NULL, NULL, '2026-09-04 21:20:27'),
(49, 1, 'super_admin', 'admin.generate_reset_link', 'general', 'users', 1, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":1,\"expires_at\":\"2026-09-05 18:20:27\"}', NULL, NULL, '2026-09-04 21:20:27'),
(50, 1, 'super_admin', 'user.suspicious_cleared', 'general', 'users', 1, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":1}', NULL, NULL, '2026-09-04 21:20:27');
INSERT INTO `audit_logs` (`id`, `user_id`, `actor_role`, `action`, `action_domain`, `entity_type`, `entity_id`, `ip_address`, `country`, `device_info`, `user_agent`, `details`, `before_state`, `after_state`, `created_at`) VALUES 
(51, 1, 'super_admin', 'security_test_event', 'general', 'test_entity', 99, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":1,\"browser\":\"Chrome QA\"}', NULL, NULL, '2026-09-04 21:20:27'),
(52, 2, 'author', 'admin.impersonate_start', 'general', 'users', 2, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"admin_id\":1,\"target_user_id\":2,\"target_username\":\"alexvance\",\"target_role\":\"author\"}', NULL, NULL, '2026-09-04 21:20:45'),
(53, 1, 'super_admin', 'admin.impersonate_end', 'general', 'users', 2, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"admin_id\":1,\"reverted_target_user_id\":2}', NULL, NULL, '2026-09-04 21:20:45'),
(54, 1, 'super_admin', 'user.strike_issued', 'general', 'author_strikes', 13, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_level\":1,\"penalty_type\":\"warning\",\"reason\":\"Minor guideline notice\",\"expires_at\":\"2026-10-04 18:20:45\",\"issued_by\":1}', NULL, NULL, '2026-09-04 21:20:45'),
(55, 1, 'super_admin', 'session.all_remote_terminated', 'general', 'users', 2, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"revoked_count\":1}', NULL, NULL, '2026-09-04 21:20:45'),
(56, 1, 'super_admin', 'user.strike_issued', 'general', 'author_strikes', 14, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_level\":2,\"penalty_type\":\"suspension\",\"reason\":\"Repeated policy violation\",\"expires_at\":\"2026-09-18 18:20:45\",\"issued_by\":1}', NULL, NULL, '2026-09-04 21:20:45'),
(57, 1, 'super_admin', 'session.all_remote_terminated', 'general', 'users', 2, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"revoked_count\":0}', NULL, NULL, '2026-09-04 21:20:45'),
(58, 1, 'super_admin', 'user.strike_issued', 'general', 'author_strikes', 15, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_level\":3,\"penalty_type\":\"ban\",\"reason\":\"Severe plagiarism and harassment\",\"expires_at\":null,\"issued_by\":1}', NULL, NULL, '2026-09-04 21:20:45'),
(59, 1, 'super_admin', 'user.strike_pardoned', 'general', 'author_strikes', 13, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_id\":13,\"pardoned_by\":1,\"remaining_active_strikes\":2}', NULL, NULL, '2026-09-04 21:20:45'),
(60, 1, 'super_admin', 'user.strike_pardoned', 'general', 'author_strikes', 14, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_id\":14,\"pardoned_by\":1,\"remaining_active_strikes\":1}', NULL, NULL, '2026-09-04 21:20:45'),
(61, 1, 'super_admin', 'user.strike_pardoned', 'general', 'author_strikes', 15, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_id\":15,\"pardoned_by\":1,\"remaining_active_strikes\":0}', NULL, NULL, '2026-09-04 21:20:45'),
(62, 1, 'super_admin', 'session.all_remote_terminated', 'general', 'users', 20, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":20,\"revoked_count\":0}', NULL, NULL, '2026-09-04 21:20:45'),
(63, 1, 'super_admin', 'admin.merge_users', 'general', 'users', 21, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"source_user_id\":20,\"source_username\":\"merge_src_1788538845_416\",\"target_user_id\":21,\"target_username\":\"merge_tgt_1788538845_416\",\"reassigned_summary\":{\"contents\":0,\"comments\":1,\"bookmarks\":1,\"reading_history\":0,\"reactions\":0,\"notifications\":0,\"support_tickets\":0,\"user_subscriptions\":0},\"merged_by\":1}', NULL, NULL, '2026-09-04 21:20:45'),
(64, 1, 'super_admin', 'admin.generate_reset_link', 'general', 'users', 1, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":1,\"expires_at\":\"2026-09-05 18:20:45\"}', NULL, NULL, '2026-09-04 21:20:45'),
(65, 1, 'super_admin', 'user.suspicious_cleared', 'general', 'users', 1, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":1}', NULL, NULL, '2026-09-04 21:20:45'),
(66, 1, 'super_admin', 'security_test_event', 'general', 'test_entity', 99, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":1,\"browser\":\"Chrome QA\"}', NULL, NULL, '2026-09-04 21:20:45'),
(67, 1, 'super_admin', 'admin.generate_reset_link', 'general', 'users', 2, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Edge on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '{\"user_id\":2,\"expires_at\":\"2026-09-05 21:23:02\"}', NULL, NULL, '2026-09-04 21:23:02'),
(68, 2, 'author', 'admin.impersonate_start', 'general', 'users', 2, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Edge on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '{\"admin_id\":1,\"target_user_id\":2,\"target_username\":\"alexvance\",\"target_role\":\"author\"}', NULL, NULL, '2026-09-04 21:24:14'),
(69, 2, 'author', 'admin.impersonate_start', 'general', 'users', 2, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"admin_id\":1,\"target_user_id\":2,\"target_username\":\"alexvance\",\"target_role\":\"author\"}', NULL, NULL, '2026-09-04 21:25:11'),
(70, 1, 'super_admin', 'admin.impersonate_end', 'general', 'users', 2, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"admin_id\":1,\"reverted_target_user_id\":2}', NULL, NULL, '2026-09-04 21:25:11'),
(71, 1, 'super_admin', 'user.strike_issued', 'general', 'author_strikes', 16, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_level\":1,\"penalty_type\":\"warning\",\"reason\":\"Minor guideline notice\",\"expires_at\":\"2026-10-04 18:25:11\",\"issued_by\":1}', NULL, NULL, '2026-09-04 21:25:11'),
(72, 1, 'super_admin', 'session.all_remote_terminated', 'general', 'users', 2, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"revoked_count\":2}', NULL, NULL, '2026-09-04 21:25:11'),
(73, 1, 'super_admin', 'user.strike_issued', 'general', 'author_strikes', 17, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_level\":2,\"penalty_type\":\"suspension\",\"reason\":\"Repeated policy violation\",\"expires_at\":\"2026-09-18 18:25:11\",\"issued_by\":1}', NULL, NULL, '2026-09-04 21:25:11'),
(74, 1, 'super_admin', 'session.all_remote_terminated', 'general', 'users', 2, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"revoked_count\":0}', NULL, NULL, '2026-09-04 21:25:11'),
(75, 1, 'super_admin', 'user.strike_issued', 'general', 'author_strikes', 18, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_level\":3,\"penalty_type\":\"ban\",\"reason\":\"Severe plagiarism and harassment\",\"expires_at\":null,\"issued_by\":1}', NULL, NULL, '2026-09-04 21:25:11'),
(76, 1, 'super_admin', 'user.strike_pardoned', 'general', 'author_strikes', 16, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_id\":16,\"pardoned_by\":1,\"remaining_active_strikes\":2}', NULL, NULL, '2026-09-04 21:25:11'),
(77, 1, 'super_admin', 'user.strike_pardoned', 'general', 'author_strikes', 17, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_id\":17,\"pardoned_by\":1,\"remaining_active_strikes\":1}', NULL, NULL, '2026-09-04 21:25:11'),
(78, 1, 'super_admin', 'user.strike_pardoned', 'general', 'author_strikes', 18, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_id\":18,\"pardoned_by\":1,\"remaining_active_strikes\":0}', NULL, NULL, '2026-09-04 21:25:11'),
(79, 1, 'super_admin', 'session.all_remote_terminated', 'general', 'users', 22, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":22,\"revoked_count\":0}', NULL, NULL, '2026-09-04 21:25:11'),
(80, 1, 'super_admin', 'admin.merge_users', 'general', 'users', 23, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"source_user_id\":22,\"source_username\":\"merge_src_1788539111_998\",\"target_user_id\":23,\"target_username\":\"merge_tgt_1788539111_998\",\"reassigned_summary\":{\"contents\":0,\"comments\":1,\"bookmarks\":1,\"reading_history\":0,\"reactions\":0,\"notifications\":0,\"support_tickets\":0,\"user_subscriptions\":0},\"merged_by\":1}', NULL, NULL, '2026-09-04 21:25:11'),
(81, 1, 'super_admin', 'admin.generate_reset_link', 'general', 'users', 1, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":1,\"expires_at\":\"2026-09-05 18:25:11\"}', NULL, NULL, '2026-09-04 21:25:11'),
(82, 1, 'super_admin', 'user.suspicious_cleared', 'general', 'users', 1, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":1}', NULL, NULL, '2026-09-04 21:25:11'),
(83, 1, 'super_admin', 'security_test_event', 'general', 'test_entity', 99, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":1,\"browser\":\"Chrome QA\"}', NULL, NULL, '2026-09-04 21:25:11'),
(84, NULL, 'system', 'user.strike_issued', 'general', 'author_strikes', 19, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":2,\"strike_level\":1,\"penalty_type\":\"warning\",\"reason\":\"Test editorial strike for QA verification\",\"expires_at\":\"2026-10-04 18:46:18\",\"issued_by\":1}', NULL, NULL, '2026-09-04 21:46:18'),
(85, 2, 'author', 'admin.impersonate_start', 'general', 'users', 2, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Edge on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '{\"admin_id\":1,\"target_user_id\":2,\"target_username\":\"alexvance\",\"target_role\":\"author\"}', NULL, NULL, '2026-09-04 21:52:54'),
(86, 2, 'author', 'admin.impersonate_start', 'general', 'users', 2, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Edge on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '{\"admin_id\":1,\"target_user_id\":2,\"target_username\":\"alexvance\",\"target_role\":\"author\"}', NULL, NULL, '2026-09-04 21:53:00'),
(87, 1, 'super_admin', 'admin.generate_reset_link', 'general', 'users', 12, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"user_id\":12,\"expires_at\":\"2026-09-05 21:57:24\"}', NULL, NULL, '2026-09-04 21:57:24'),
(88, 12, 'member', 'admin.impersonate_start', 'general', 'users', 12, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"admin_id\":1,\"target_user_id\":12,\"target_username\":\"merge_src_1788538666_252\",\"target_role\":\"member\"}', NULL, NULL, '2026-09-04 21:57:29'),
(89, 1, 'super_admin', 'admin.impersonate_end', 'general', 'users', 12, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"admin_id\":1,\"reverted_target_user_id\":12}', NULL, NULL, '2026-09-04 21:57:39'),
(90, 12, 'member', 'admin.impersonate_start', 'general', 'users', 12, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"admin_id\":1,\"target_user_id\":12,\"target_username\":\"merge_src_1788538666_252\",\"target_role\":\"member\"}', NULL, NULL, '2026-09-04 21:58:34'),
(91, 2, 'author', 'admin.impersonate_start', 'general', 'users', 2, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Edge on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '{\"admin_id\":1,\"target_user_id\":2,\"target_username\":\"alexvance\",\"target_role\":\"author\"}', NULL, NULL, '2026-09-04 22:06:37'),
(92, 2, 'author', 'admin.impersonate_start', 'general', 'users', 2, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Edge on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '{\"admin_id\":1,\"target_user_id\":2,\"target_username\":\"alexvance\",\"target_role\":\"author\"}', NULL, NULL, '2026-09-04 22:06:41'),
(93, 1, 'super_admin', 'admin.impersonate_end', 'general', 'users', 12, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"admin_id\":1,\"reverted_target_user_id\":12}', NULL, NULL, '2026-09-04 22:07:22'),
(94, 1, 'super_admin', 'admin.generate_reset_link', 'general', 'users', 12, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"user_id\":12,\"expires_at\":\"2026-09-05 22:07:32\"}', NULL, NULL, '2026-09-04 22:07:32'),
(95, 2, 'author', 'admin.impersonate_start', 'general', 'users', 2, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Edge on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '{\"admin_id\":1,\"target_user_id\":2,\"target_username\":\"alexvance\",\"target_role\":\"author\"}', NULL, NULL, '2026-09-04 22:14:44'),
(96, 2, 'author', 'admin.impersonate_start', 'general', 'users', 2, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Edge on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '{\"admin_id\":1,\"target_user_id\":2,\"target_username\":\"alexvance\",\"target_role\":\"author\"}', NULL, NULL, '2026-09-04 22:14:50'),
(97, 1, 'super_admin', 'admin.user_password_change', 'general', 'user', 11, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"changed_by\":1}', NULL, NULL, '2026-09-04 22:19:10'),
(98, NULL, 'system', 'admin.generate_reset_link', 'general', 'users', 12, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"user_id\":12,\"expires_at\":\"2026-09-05 19:27:03\"}', NULL, NULL, '2026-09-04 22:27:03'),
(99, 1, 'super_admin', 'admin.generate_reset_link', 'general', 'users', 12, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Unknown Browser on Unknown OS (Desktop)', 'curl/8.21.0', '{\"user_id\":12,\"expires_at\":\"2026-09-05 22:30:31\"}', NULL, NULL, '2026-09-04 22:30:31'),
(100, 1, 'super_admin', 'admin.generate_reset_link', 'general', 'users', 12, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Unknown Browser on Unknown OS (Desktop)', 'curl/8.21.0', '{\"user_id\":12,\"expires_at\":\"2026-09-05 22:37:51\"}', NULL, NULL, '2026-09-04 22:37:51');
INSERT INTO `audit_logs` (`id`, `user_id`, `actor_role`, `action`, `action_domain`, `entity_type`, `entity_id`, `ip_address`, `country`, `device_info`, `user_agent`, `details`, `before_state`, `after_state`, `created_at`) VALUES 
(101, 1, 'super_admin', 'admin.media_uploaded', 'general', 'media_files', 6, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"filename\":\"1788543705_2ef81502.jpg\",\"folder\":\"articles\"}', NULL, NULL, '2026-09-04 22:41:45'),
(102, 1, 'super_admin', 'admin.media_deleted', 'general', 'media_files', 6, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"filename\":\"1788543705_2ef81502.jpg\"}', NULL, NULL, '2026-09-04 22:42:12'),
(103, 1, 'super_admin', 'ledger.credit', 'general', 'financial_ledger', 1, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"reference_no\":\"TXN-2026-639ADC1C\",\"author_id\":7,\"amount_usd\":23,\"balance_before\":280,\"balance_after\":303,\"reason\":\"nothing\"}', NULL, NULL, '2026-09-04 22:43:44'),
(104, NULL, 'system', 'ledger.credit', 'general', 'financial_ledger', 2, '127.0.0.1', 'Pakistan 🇵🇰 (Local Host)', 'Server CLI Environment 🖥️', 'CLI / Background Runner', '{\"reference_no\":\"TXN-2026-DD4C34CE\",\"author_id\":1,\"amount_usd\":3.5,\"balance_before\":1026.25,\"balance_after\":1029.75,\"reason\":\"Direct QA Pool Verification Test\"}', NULL, NULL, '2026-09-04 23:08:52'),
(105, 1, 'super_admin', 'admin.generate_reset_link', 'general', 'users', 12, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"user_id\":12,\"expires_at\":\"2026-09-05 23:10:46\"}', NULL, NULL, '2026-09-04 23:10:46'),
(106, 12, 'member', 'user.quick_login_used', 'general', 'users', 12, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"ip\":\"::1\"}', NULL, NULL, '2026-09-04 23:10:54'),
(107, 1, 'super_admin', 'author.payout_method_linked', 'general', 'author_payout_methods', 1, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"author_id\":1,\"method_type\":\"paypal\"}', NULL, NULL, '2026-09-04 23:27:38'),
(108, 1, 'super_admin', 'ledger.debit', 'general', 'financial_ledger', 3, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"reference_no\":\"TXN-2026-29752853\",\"author_id\":1,\"amount_usd\":40,\"balance_before\":1029.75,\"balance_after\":989.75,\"reason\":\"Withdrawal request via PAYPAL (PayPal: admin@techscout.online) [Tax: $2, Bank Tax/Fee: $0]\"}', NULL, NULL, '2026-09-05 09:19:17'),
(109, 2, 'author', 'admin.impersonate_start', 'general', 'users', 2, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"admin_id\":1,\"target_user_id\":2,\"target_username\":\"alexvance\",\"target_role\":\"author\"}', NULL, NULL, '2026-09-05 09:30:34'),
(110, 1, 'super_admin', 'admin.impersonate_end', 'general', 'users', 2, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"admin_id\":1,\"reverted_target_user_id\":2}', NULL, NULL, '2026-09-05 09:34:06'),
(111, 1, 'super_admin', 'user.strike_pardoned', 'general', 'author_strikes', 19, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"user_id\":2,\"strike_id\":19,\"pardoned_by\":1,\"remaining_active_strikes\":0}', NULL, NULL, '2026-09-05 09:34:40'),
(112, 2, 'author', 'admin.impersonate_start', 'general', 'users', 2, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"admin_id\":1,\"target_user_id\":2,\"target_username\":\"alexvance\",\"target_role\":\"author\"}', NULL, NULL, '2026-09-05 09:34:48'),
(113, 1, 'super_admin', 'admin.impersonate_end', 'general', 'users', 2, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"admin_id\":1,\"reverted_target_user_id\":2}', NULL, NULL, '2026-09-05 09:39:46'),
(114, 1, 'super_admin', 'admin.generate_reset_link', 'general', 'users', 12, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"user_id\":12,\"expires_at\":\"2026-09-06 11:19:56\"}', NULL, NULL, '2026-09-05 11:19:56'),
(115, 12, 'member', 'admin.impersonate_start', 'general', 'users', 12, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"admin_id\":1,\"target_user_id\":12,\"target_username\":\"merge_src_1788538666_252\",\"target_role\":\"member\"}', NULL, NULL, '2026-09-05 11:20:14'),
(116, 1, 'super_admin', 'admin.impersonate_end', 'general', 'users', 12, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"admin_id\":1,\"reverted_target_user_id\":12}', NULL, NULL, '2026-09-05 11:20:34'),
(117, 2, 'author', 'admin.impersonate_start', 'general', 'users', 2, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Edge on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '{\"admin_id\":1,\"target_user_id\":2,\"target_username\":\"alexvance\",\"target_role\":\"author\"}', NULL, NULL, '2026-09-05 11:34:21'),
(118, 2, 'author', 'admin.impersonate_start', 'general', 'users', 2, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Edge on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '{\"admin_id\":1,\"target_user_id\":2,\"target_username\":\"alexvance\",\"target_role\":\"author\"}', NULL, NULL, '2026-09-05 11:34:22'),
(119, 1, 'super_admin', 'session.all_remote_terminated', 'general', 'users', 1, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"user_id\":1,\"revoked_count\":314}', NULL, NULL, '2026-09-05 12:03:16'),
(120, 1, 'super_admin', 'admin.generate_reset_link', 'general', 'users', 12, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"user_id\":12,\"expires_at\":\"2026-09-06 12:13:04\"}', NULL, NULL, '2026-09-05 12:13:04'),
(121, 12, 'member', 'user.quick_login_used', 'general', 'users', 12, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"ip\":\"::1\"}', NULL, NULL, '2026-09-05 12:13:21'),
(122, 1, 'super_admin', 'admin.save_settings', 'general', 'system', 0, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"tab\":\"domain\",\"updated_by\":1}', NULL, NULL, '2026-09-05 12:42:33'),
(123, 1, 'super_admin', 'admin.campaign_create', 'general', 'campaign', 47, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"title\":\"refferal\"}', NULL, NULL, '2026-09-05 13:26:36'),
(124, 1, 'super_admin', 'admin.broadcast_notification', 'general', 'system', 0, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"audience\":\"single\",\"single_user_id\":12,\"title\":\"Notice for @merge_src_1788538666_252\",\"recipients_count\":1}', NULL, NULL, '2026-09-05 15:35:16'),
(125, 1, 'super_admin', 'admin.save_settings', 'general', 'system', 0, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"tab\":\"general\",\"updated_by\":1}', NULL, NULL, '2026-09-05 15:44:13'),
(126, 1, 'super_admin', 'admin.create_mail_credential', 'general', 'mail_credentials', 5, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"name\":\"Xpert Gmail\",\"provider\":\"smtp\"}', NULL, NULL, '2026-09-05 17:48:42'),
(127, 1, 'super_admin', 'admin.send_mail_broadcast', 'general', 'mail', 0, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"template\":\"author_milestone_badge\",\"count\":0,\"credential_id\":5,\"has_attachments\":false}', NULL, NULL, '2026-09-05 17:49:58'),
(128, 1, 'super_admin', 'admin.assign_template_credential', 'general', 'mail_template', 0, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"template_key\":\"account_activated\",\"assigned_credential_id\":5}', NULL, NULL, '2026-09-05 17:57:36'),
(129, 1, 'super_admin', 'admin.update_mail_settings', 'general', 'system', 0, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"driver\":\"log\"}', NULL, NULL, '2026-09-05 17:58:35'),
(130, 1, 'super_admin', 'admin.update_mail_settings', 'general', 'system', 0, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"driver\":\"smtp\"}', NULL, NULL, '2026-09-05 17:59:02'),
(131, 1, 'super_admin', 'admin.assign_template_credential', 'general', 'mail_template', 0, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"template_key\":\"welcome\",\"assigned_credential_id\":5}', NULL, NULL, '2026-09-05 18:00:33'),
(132, 1, 'super_admin', 'database.backup_created', 'general', 'database_backups', 1, '::1', 'Pakistan 🇵🇰 (Local Host)', 'Chrome on Windows 11 / 10 🪟 (Desktop)', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '{\"filename\":\"techscout_backup_20260905_180549_4e1b75be.sql\",\"size_bytes\":547670,\"table_count\":77,\"total_rows\":2409}', NULL, NULL, '2026-09-05 18:05:49');

--
-- Structure for table `author_applications`
--
DROP TABLE IF EXISTS `author_applications`;
CREATE TABLE `author_applications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `headline` varchar(150) NOT NULL,
  `bio` text NOT NULL,
  `portfolio_url` varchar(255) DEFAULT NULL,
  `github_handle` varchar(100) DEFAULT NULL,
  `twitter_handle` varchar(100) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `specializations` varchar(255) DEFAULT NULL,
  `topic_interests` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`topic_interests`)),
  `sample_pitch` text NOT NULL,
  `sample_file_url` varchar(255) DEFAULT NULL,
  `requested_prompts` tinyint(1) NOT NULL DEFAULT 0,
  `requested_gpts` tinyint(1) NOT NULL DEFAULT 0,
  `requested_tools` tinyint(1) NOT NULL DEFAULT 0,
  `requested_credits` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_feedback` text DEFAULT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_author_apps_status` (`status`),
  KEY `idx_author_apps_user` (`user_id`),
  CONSTRAINT `fk_author_apps_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `author_applications` (3 rows)
--
INSERT INTO `author_applications` (`id`, `user_id`, `display_name`, `headline`, `bio`, `portfolio_url`, `github_handle`, `twitter_handle`, `linkedin_url`, `specializations`, `topic_interests`, `sample_pitch`, `sample_file_url`, `requested_prompts`, `requested_gpts`, `requested_tools`, `requested_credits`, `status`, `admin_feedback`, `reviewed_by`, `reviewed_at`, `created_at`, `updated_at`) VALUES 
(1, 2, 'Elena Rostova', 'Staff Machine Learning Infrastructure Architect', '10+ years scaling distributed model training clusters and low-latency inference pipelines with vLLM and TensorRT.', 'https://erostova.tech', 'erostova-ml', 'erostova_ai', 'https://linkedin.com/in/erostova', 'Distributed Training, vLLM, CUDA Optimization', '[\"LLMs\",\"DevOps\",\"Cloud Architecture\"]', 'Drafting a deep dive on optimizing KV-cache paging in Multi-Node vLLM setups.', NULL, 1, 1, 1, 100.00, 'approved', NULL, NULL, NULL, '2026-08-27 06:22:55', '2026-08-27 06:22:55'),
(2, 3, 'Marcus Vance', 'Principal Security Researcher & Red Teamer', 'Specializing in prompt injection fuzzing, model jailbreaks, and zero-trust LLM firewall architectures.', 'https://vance-sec.org', 'vance-redteam', 'vancesec', 'https://linkedin.com/in/marcusvance', 'Cybersecurity, AI Safety, Prompt Hardening', '[\"Security\",\"Prompt Engineering\"]', 'Case study: Mitigating indirect prompt injection in autonomous web agent browser pipelines.', NULL, 1, 0, 1, 50.00, 'pending', NULL, NULL, NULL, '2026-08-26 06:22:55', '2026-08-26 06:22:55'),
(3, 4, 'Devon Chen', 'Full-Stack Agentic AI Developer', 'Building autonomous coding assistants and multi-agent coordination frameworks.', 'https://devonchen.io', 'dchen-agents', 'dchen_dev', 'https://linkedin.com/in/devonchen', 'Multi-Agent Frameworks, LangGraph, Python/PHP', '[\"Agents\",\"Autonomous Systems\"]', 'Benchmark of hierarchical agent supervisory models vs peer-to-peer agent voting.', NULL, 1, 1, 1, 75.00, 'approved', NULL, NULL, NULL, '2026-08-25 06:22:55', '2026-08-25 06:22:55');

--
-- Structure for table `author_followers`
--
DROP TABLE IF EXISTS `author_followers`;
CREATE TABLE `author_followers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_author_user` (`author_id`,`user_id`),
  KEY `idx_author` (`author_id`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `author_followers` (1 rows)
--
INSERT INTO `author_followers` (`id`, `author_id`, `user_id`, `created_at`) VALUES 
(1, 1, 12, '2026-09-05 11:20:30');

--
-- Structure for table `author_payout_methods`
--
DROP TABLE IF EXISTS `author_payout_methods`;
CREATE TABLE `author_payout_methods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` bigint(20) unsigned NOT NULL,
  `method_type` enum('bank_wire','iban_sepa','paypal','stripe_connect','crypto_usdc') NOT NULL,
  `account_holder_name` varchar(150) NOT NULL,
  `bank_name` varchar(150) DEFAULT NULL,
  `account_number_or_iban` varchar(100) DEFAULT NULL,
  `routing_or_swift` varchar(50) DEFAULT NULL,
  `account_email` varchar(191) DEFAULT NULL,
  `crypto_wallet_address` varchar(150) DEFAULT NULL,
  `crypto_network` varchar(50) DEFAULT 'ERC-20 (Ethereum)',
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('verified','pending_verification','active','disabled') NOT NULL DEFAULT 'verified',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_apm_author` (`author_id`),
  KEY `idx_apm_default` (`author_id`,`is_default`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `author_payout_methods` (1 rows)
--
INSERT INTO `author_payout_methods` (`id`, `author_id`, `method_type`, `account_holder_name`, `bank_name`, `account_number_or_iban`, `routing_or_swift`, `account_email`, `crypto_wallet_address`, `crypto_network`, `is_default`, `status`, `created_at`, `updated_at`) VALUES 
(1, 1, 'paypal', 'TechScout Editorial Staff', '', '', '', 'admin@techscout.online', '', 'ERC-20 (Ethereum)', 1, 'verified', '2026-09-04 23:27:38', '2026-09-04 23:27:38');

--
-- Structure for table `author_payouts`
--
DROP TABLE IF EXISTS `author_payouts`;
CREATE TABLE `author_payouts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ledger_reference_no` varchar(64) DEFAULT NULL,
  `author_id` bigint(20) unsigned NOT NULL,
  `payout_method_id` int(10) unsigned DEFAULT NULL,
  `amount_usd` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax_deducted` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bank_tax_deducted` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fee_deducted` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `credits_deducted` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payout_method` varchar(50) DEFAULT 'paypal',
  `payout_address` varchar(255) NOT NULL,
  `transaction_reference` varchar(150) DEFAULT NULL,
  `receipt_url` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','paid','rejected') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `processed_by` bigint(20) unsigned DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_payouts_author_status` (`author_id`,`status`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `author_payouts` (1 rows)
--
INSERT INTO `author_payouts` (`id`, `ledger_reference_no`, `author_id`, `payout_method_id`, `amount_usd`, `tax_deducted`, `bank_tax_deducted`, `fee_deducted`, `net_amount`, `credits_deducted`, `payout_method`, `payout_address`, `transaction_reference`, `receipt_url`, `status`, `admin_notes`, `processed_by`, `processed_at`, `created_at`) VALUES 
(1, 'TXN-2026-29752853', 1, 1, 40.00, 2.00, 0.00, 2.00, 38.00, 40.00, 'paypal', 'PayPal: admin@techscout.online', NULL, NULL, 'pending', '', NULL, NULL, '2026-09-05 09:19:17');

--
-- Structure for table `author_strikes`
--
DROP TABLE IF EXISTS `author_strikes`;
CREATE TABLE `author_strikes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `strike_level` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `penalty_type` enum('warning','suspension','ban') NOT NULL DEFAULT 'warning',
  `reason` varchar(500) NOT NULL,
  `notes` text DEFAULT NULL,
  `issued_by_user_id` bigint(20) unsigned DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_strikes` (`user_id`,`is_active`),
  KEY `idx_expires` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `author_strikes` (4 rows)
--
INSERT INTO `author_strikes` (`id`, `user_id`, `strike_level`, `penalty_type`, `reason`, `notes`, `issued_by_user_id`, `expires_at`, `is_active`, `created_at`) VALUES 
(16, 2, 1, 'warning', 'Minor guideline notice', 'Test notes 1', 1, '2026-10-04 18:25:11', 0, '2026-09-04 21:25:11'),
(17, 2, 2, 'suspension', 'Repeated policy violation', 'Test notes 2', 1, '2026-09-18 18:25:11', 0, '2026-09-04 21:25:11'),
(18, 2, 3, 'ban', 'Severe plagiarism and harassment', 'Permanent ban issued', 1, NULL, 0, '2026-09-04 21:25:11'),
(19, 2, 1, 'warning', 'Test editorial strike for QA verification', 'Prime QA state', 1, '2026-10-04 18:46:18', 0, '2026-09-04 21:46:18');

--
-- Structure for table `author_tier_history`
--
DROP TABLE IF EXISTS `author_tier_history`;
CREATE TABLE `author_tier_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(10) unsigned NOT NULL,
  `tier_code` varchar(50) NOT NULL,
  `tier_level` int(11) NOT NULL DEFAULT 1,
  `tier_title` varchar(100) NOT NULL,
  `cpm_multiplier` decimal(4,2) NOT NULL DEFAULT 1.00,
  `unlocked_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_th_author` (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure for table `author_tips`
--
DROP TABLE IF EXISTS `author_tips`;
CREATE TABLE `author_tips` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(64) NOT NULL,
  `author_id` int(10) unsigned NOT NULL,
  `article_id` int(10) unsigned DEFAULT NULL,
  `tipper_user_id` bigint(20) unsigned DEFAULT NULL,
  `tipper_name` varchar(100) NOT NULL DEFAULT 'Generous Supporter',
  `tipper_email` varchar(150) DEFAULT NULL,
  `amount_usd` decimal(10,2) NOT NULL,
  `platform_fee_usd` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_amount_usd` decimal(10,2) NOT NULL,
  `message` text DEFAULT NULL,
  `is_anonymous` tinyint(1) NOT NULL DEFAULT 0,
  `payment_method` varchar(50) NOT NULL DEFAULT 'card_stripe',
  `status` enum('completed','pending','refunded') NOT NULL DEFAULT 'completed',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference_no` (`reference_no`),
  KEY `idx_tip_author` (`author_id`),
  KEY `idx_tip_article` (`article_id`),
  KEY `idx_tip_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure for table `authors`
--
DROP TABLE IF EXISTS `authors`;
CREATE TABLE `authors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `slug` varchar(80) NOT NULL,
  `headline` varchar(180) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `cover_image_url` varchar(255) DEFAULT NULL,
  `expertise_tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`expertise_tags`)),
  `social_links` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`social_links`)),
  `verification_badge` enum('none','verified_builder','expert','staff','top_creator') NOT NULL DEFAULT 'none',
  `custom_badge_label` varchar(100) DEFAULT NULL,
  `custom_badge_color` varchar(30) DEFAULT NULL,
  `reputation_score` int(10) unsigned NOT NULL DEFAULT 0,
  `reputation_level` varchar(40) NOT NULL DEFAULT 'Scout Initiate',
  `tier_override` varchar(50) DEFAULT NULL,
  `tier_title` varchar(100) DEFAULT NULL,
  `current_tier_code` varchar(50) NOT NULL DEFAULT 'bronze',
  `tier_sublevel` int(11) NOT NULL DEFAULT 1,
  `tier_multiplier` decimal(4,2) NOT NULL DEFAULT 1.00,
  `referral_code` varchar(50) DEFAULT NULL,
  `promo_credits` decimal(10,2) NOT NULL DEFAULT 0.00,
  `can_publish_prompts` tinyint(1) NOT NULL DEFAULT 0,
  `can_publish_gpts` tinyint(1) NOT NULL DEFAULT 0,
  `can_publish_tools` tinyint(1) NOT NULL DEFAULT 0,
  `can_use_templates` tinyint(1) NOT NULL DEFAULT 0,
  `monetization_status` varchar(50) NOT NULL DEFAULT 'ineligible',
  `follower_count` int(10) unsigned NOT NULL DEFAULT 0,
  `article_count` int(10) unsigned NOT NULL DEFAULT 0,
  `total_views` bigint(20) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_monetized` tinyint(1) DEFAULT 0,
  `monetization_applied_at` datetime DEFAULT NULL,
  `monetization_approved_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_authors_reputation` (`reputation_score`),
  CONSTRAINT `fk_authors_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `authors` (12 rows)
--
INSERT INTO `authors` (`id`, `user_id`, `display_name`, `slug`, `headline`, `bio`, `avatar_url`, `cover_image_url`, `expertise_tags`, `social_links`, `verification_badge`, `custom_badge_label`, `custom_badge_color`, `reputation_score`, `reputation_level`, `tier_override`, `tier_title`, `current_tier_code`, `tier_sublevel`, `tier_multiplier`, `referral_code`, `promo_credits`, `can_publish_prompts`, `can_publish_gpts`, `can_publish_tools`, `can_use_templates`, `monetization_status`, `follower_count`, `article_count`, `total_views`, `created_at`, `updated_at`, `is_monetized`, `monetization_applied_at`, `monetization_approved_at`) VALUES 
(1, 1, 'TechScout Editorial Staff', 'techscout-editorial', 'Chief Distributed Systems Engineer & Core Contributor', 'Focusing on low-latency microservices, fault-tolerant consensus, and distributed databases.', 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=150&auto=format&fit=crop&q=80', NULL, NULL, '{\"website\":\"https://alexvance.systems\",\"github\":\"https://github.com/alexvance\",\"twitter\":\"https://x.com/alexvance\",\"linkedin\":\"https://linkedin.com/in/alexvance\",\"discord\":\"alexvance#1337\",\"tax_id_or_ssn\":\"XX-XXXX1234\",\"tax_country\":\"US\"}', 'expert', 'Tech Scout Architect', '#38bdf8', 10149, 'Master Scout', 1, 'Distinguished Fellow', 'fellow', 4, 2.00, 'techscouteditorial', 989.75, 1, 1, 1, 1, 'approved', 1438, 48, 860015, '2026-08-27 20:39:38', '2026-09-05 11:20:30', 1, '2026-08-30 13:33:54', '2026-08-30 13:38:09'),
(2, 2, 'Alex Vance', 'alexvance', 'Principal AI & Systems Architect', 'Building high-throughput LLM pipelines, autonomous agents, and scalable distributed architectures.', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80', NULL, NULL, NULL, 'expert', 'Tech Scout Architect', '#38bdf8', 2850, 'Tech Lead Contributor', 1, 'Distinguished Fellow', 'fellow', 1, 2.00, 'alexvance', 570.00, 0, 0, 0, 0, 'ineligible', 380, 14, 148200, '2026-08-27 20:39:38', '2026-09-05 09:35:27', 0, NULL, NULL),
(3, 3, 'Sarah Lin', 'sarah_dev', 'DevOps Lead & Cloud Native Specialist', 'Kubernetes practitioner, cloud infrastructure optimizer, and Go/Rust engineer.', 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=150&auto=format&fit=crop&q=80', NULL, NULL, NULL, 'expert', NULL, NULL, 1920, 'Verified Builder', NULL, NULL, 'bronze', 1, 1.00, NULL, 257.00, 0, 0, 0, 0, 'ineligible', 460, 9, 92400, '2026-08-27 20:39:38', '2026-09-05 09:35:27', 0, NULL, NULL),
(4, 4, 'David Chen', 'david_chen', 'Senior Full-Stack & UI Architect', 'React 19, Next.js, WebAssembly and modern design systems performance engineer.', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80', NULL, NULL, NULL, 'verified_builder', NULL, NULL, 1650, 'Senior Contributor', NULL, NULL, 'bronze', 1, 1.00, NULL, 150.00, 1, 0, 1, 0, 'ineligible', 178, 7, 78100, '2026-08-27 20:39:38', '2026-09-05 09:35:27', 0, NULL, NULL),
(5, 5, 'Dr. Elena Rostova', 'elena_ai', 'ML Scientist & Prompt Architect', 'Researching token-efficient RAG indexing, reasoning models, and autonomous tool calling.', 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=150&auto=format&fit=crop&q=80', NULL, NULL, NULL, 'top_creator', NULL, NULL, 3100, 'Chief Research Fellow', NULL, NULL, 'bronze', 1, 1.00, NULL, 642.75, 1, 1, 0, 0, 'ineligible', 195, 16, 210500, '2026-08-27 20:39:38', '2026-09-05 09:35:27', 0, NULL, NULL),
(6, 6, 'Marcus Thorne', 'marcus_db', 'Database Reliability Engineer', 'PostgreSQL, MySQL 8, Redis Streams and distributed cache architect.', 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&auto=format&fit=crop&q=80', NULL, NULL, NULL, 'expert', NULL, NULL, 2200, 'Staff Engineer', NULL, NULL, 'bronze', 1, 1.00, NULL, 320.00, 1, 0, 0, 0, 'ineligible', 210, 11, 115000, '2026-08-27 20:39:38', '2026-09-05 09:35:27', 0, NULL, NULL),
(7, 7, 'Maya Patel', 'maya_sec', 'AppSec & Zero-Trust Specialist', 'Security researcher, API penetration tester, and cryptography enthusiast.', 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=150&auto=format&fit=crop&q=80', NULL, NULL, NULL, 'verified_builder', NULL, NULL, 1880, 'Security Scout', NULL, NULL, 'bronze', 1, 1.00, NULL, 303.00, 0, 1, 0, 0, 'ineligible', 320, 8, 86300, '2026-08-27 20:39:38', '2026-09-05 09:35:27', 0, NULL, NULL),
(8, 9, 'Rachel Wei', 'rachel-wei', 'Kubernetes Security & Cloud Native Architect', 'AWS certified solutions architect and security researcher focused on zero-trust service mesh.', NULL, NULL, NULL, NULL, 'verified_builder', NULL, NULL, 100, 'Scout Author', NULL, NULL, 'bronze', 1, 1.00, 'rachelwei', 200.00, 0, 0, 0, 0, 'ineligible', 94, 0, 0, '2026-08-29 16:41:03', '2026-09-05 09:35:27', 0, NULL, NULL),
(9, 10, 'Samir Patel', 'samir-patel', 'Full-Stack Agentic AI Engineer', 'Building production AI copilots with Next.js 15, FastAPI, and LangGraph multi-agent systems.', NULL, NULL, NULL, NULL, 'verified_builder', NULL, NULL, 100, 'Scout Author', NULL, NULL, 'bronze', 1, 1.00, 'samirpatel', 100.00, 0, 0, 0, 0, 'ineligible', 512, 0, 0, '2026-08-29 22:14:19', '2026-09-05 09:35:27', 0, NULL, NULL),
(10, 8, 'kai_systems', 'author-8', '', '', NULL, NULL, NULL, NULL, 'none', NULL, NULL, 0, 'Scout Initiate', NULL, NULL, 'bronze', 1, 1.00, NULL, 0.00, 0, 0, 0, 0, 'ineligible', 142, 0, 0, '2026-09-02 19:34:11', '2026-09-05 09:35:27', 0, NULL, NULL),
(11, 11, 'asdf', 'asdf', 'Full-Stack Developer', NULL, NULL, NULL, NULL, NULL, 'none', NULL, NULL, 0, 'Scout Initiate', NULL, NULL, 'bronze', 1, 1.00, NULL, 0.00, 0, 0, 0, 0, 'ineligible', 285, 0, 0, '2026-09-02 23:27:25', '2026-09-05 09:35:27', 0, NULL, NULL),
(12, 12, 'merge_src_1788538666_252', 'merge-src-1788538666-252', '', '', '/assets/uploads/avatars/avatar_12_c17fe459.png', NULL, NULL, NULL, 'none', NULL, NULL, 0, 'Scout Initiate', NULL, NULL, 'bronze', 1, 1.00, NULL, 0.00, 0, 0, 0, 0, 'ineligible', 0, 0, 0, '2026-09-05 12:40:18', '2026-09-05 12:40:18', 0, NULL, NULL);

--
-- Structure for table `bookmarks`
--
DROP TABLE IF EXISTS `bookmarks`;
CREATE TABLE `bookmarks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `content_id` bigint(20) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_content_bookmark` (`user_id`,`content_id`),
  KEY `fk_bookmarks_content` (`content_id`),
  CONSTRAINT `fk_bookmarks_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_bookmarks_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookmarks` (160 rows)
--
INSERT INTO `bookmarks` (`id`, `user_id`, `content_id`, `created_at`) VALUES 
(1, 1, 2, '2026-08-24 06:22:54'),
(2, 2, 2, '2026-09-03 06:22:54'),
(3, 10, 2, '2026-08-15 06:22:54'),
(4, 12, 2, '2026-08-29 06:22:54'),
(5, 2, 3, '2026-09-02 06:22:54'),
(6, 5, 3, '2026-08-17 06:22:54'),
(7, 7, 3, '2026-09-01 06:22:54'),
(8, 8, 3, '2026-08-12 06:22:54'),
(9, 9, 3, '2026-08-26 06:22:54'),
(10, 10, 3, '2026-08-23 06:22:54'),
(11, 11, 3, '2026-08-19 06:22:54'),
(12, 1, 4, '2026-08-23 06:22:54'),
(13, 4, 4, '2026-08-25 06:22:54'),
(14, 9, 4, '2026-08-22 06:22:54'),
(15, 10, 4, '2026-08-12 06:22:54'),
(16, 12, 4, '2026-08-11 06:22:54'),
(17, 2, 5, '2026-08-26 06:22:54'),
(18, 4, 5, '2026-08-12 06:22:54'),
(19, 10, 5, '2026-09-01 06:22:54'),
(20, 7, 6, '2026-09-01 06:22:54'),
(21, 9, 6, '2026-08-22 06:22:54'),
(22, 12, 6, '2026-08-11 06:22:54'),
(23, 3, 7, '2026-08-17 06:22:54'),
(24, 6, 7, '2026-08-13 06:22:54'),
(25, 10, 7, '2026-08-18 06:22:54'),
(26, 11, 7, '2026-08-21 06:22:54'),
(27, 4, 8, '2026-08-23 06:22:54'),
(28, 7, 8, '2026-08-13 06:22:54'),
(29, 8, 8, '2026-08-29 06:22:54'),
(30, 11, 8, '2026-08-11 06:22:54'),
(31, 6, 9, '2026-08-24 06:22:54'),
(32, 7, 9, '2026-08-14 06:22:54'),
(33, 3, 10, '2026-08-18 06:22:54'),
(34, 12, 10, '2026-08-19 06:22:54'),
(35, 1, 11, '2026-08-16 06:22:54'),
(36, 9, 11, '2026-08-29 06:22:54'),
(37, 2, 12, '2026-09-02 06:22:54'),
(38, 7, 12, '2026-08-14 06:22:54'),
(39, 12, 12, '2026-09-03 06:22:54'),
(40, 2, 13, '2026-08-20 06:22:54'),
(41, 6, 13, '2026-08-19 06:22:54'),
(42, 10, 13, '2026-08-16 06:22:54'),
(43, 2, 14, '2026-08-11 06:22:54'),
(44, 4, 14, '2026-08-13 06:22:54'),
(45, 6, 14, '2026-08-11 06:22:54'),
(46, 8, 14, '2026-09-03 06:22:54'),
(47, 10, 14, '2026-09-04 06:22:54'),
(48, 11, 14, '2026-09-03 06:22:54'),
(49, 12, 14, '2026-08-12 06:22:54'),
(50, 7, 15, '2026-09-01 06:22:54');
INSERT INTO `bookmarks` (`id`, `user_id`, `content_id`, `created_at`) VALUES 
(51, 1, 17, '2026-08-18 06:22:54'),
(52, 4, 17, '2026-08-28 06:22:54'),
(53, 7, 17, '2026-08-24 06:22:54'),
(54, 12, 17, '2026-08-16 06:22:54'),
(55, 1, 18, '2026-08-21 06:22:54'),
(56, 6, 18, '2026-08-18 06:22:54'),
(57, 1, 19, '2026-08-29 06:22:54'),
(58, 2, 19, '2026-08-20 06:22:54'),
(59, 3, 19, '2026-08-17 06:22:54'),
(60, 4, 19, '2026-09-03 06:22:55'),
(61, 5, 19, '2026-08-23 06:22:55'),
(62, 6, 19, '2026-08-14 06:22:55'),
(63, 11, 19, '2026-08-28 06:22:55'),
(64, 12, 19, '2026-08-21 06:22:55'),
(65, 6, 20, '2026-08-16 06:22:55'),
(66, 9, 20, '2026-08-11 06:22:55'),
(67, 10, 20, '2026-08-19 06:22:55'),
(68, 1, 21, '2026-08-20 06:22:55'),
(69, 4, 21, '2026-08-19 06:22:55'),
(70, 9, 21, '2026-08-28 06:22:55'),
(71, 2, 22, '2026-08-21 06:22:55'),
(72, 5, 22, '2026-08-30 06:22:55'),
(73, 6, 22, '2026-08-24 06:22:55'),
(74, 7, 22, '2026-08-17 06:22:55'),
(75, 8, 22, '2026-08-21 06:22:55'),
(76, 1, 23, '2026-08-17 06:22:55'),
(77, 6, 23, '2026-08-20 06:22:55'),
(78, 9, 23, '2026-08-14 06:22:55'),
(79, 5, 24, '2026-08-23 06:22:55'),
(80, 6, 24, '2026-08-17 06:22:55'),
(81, 12, 24, '2026-08-15 06:22:55'),
(82, 1, 25, '2026-08-26 06:22:55'),
(83, 3, 25, '2026-08-15 06:22:55'),
(84, 6, 25, '2026-09-02 06:22:55'),
(85, 8, 25, '2026-08-13 06:22:55'),
(86, 9, 25, '2026-08-22 06:22:55'),
(87, 12, 25, '2026-08-28 06:22:55'),
(88, 3, 26, '2026-08-24 06:22:55'),
(89, 7, 26, '2026-09-03 06:22:55'),
(90, 9, 26, '2026-09-02 06:22:55'),
(91, 12, 26, '2026-08-16 06:22:55'),
(92, 1, 27, '2026-08-26 06:22:55'),
(93, 6, 27, '2026-08-29 06:22:55'),
(94, 12, 27, '2026-09-04 06:22:55'),
(95, 3, 28, '2026-08-14 06:22:55'),
(96, 4, 28, '2026-08-28 06:22:55'),
(97, 5, 28, '2026-08-11 06:22:55'),
(98, 7, 28, '2026-09-03 06:22:55'),
(99, 11, 28, '2026-08-28 06:22:55'),
(100, 3, 29, '2026-08-13 06:22:55');
INSERT INTO `bookmarks` (`id`, `user_id`, `content_id`, `created_at`) VALUES 
(101, 7, 29, '2026-08-20 06:22:55'),
(102, 9, 29, '2026-08-25 06:22:55'),
(103, 10, 29, '2026-08-31 06:22:55'),
(104, 1, 30, '2026-08-27 06:22:55'),
(105, 2, 30, '2026-08-31 06:22:55'),
(106, 3, 30, '2026-08-15 06:22:55'),
(107, 7, 30, '2026-08-28 06:22:55'),
(108, 9, 30, '2026-08-21 06:22:55'),
(109, 10, 30, '2026-08-28 06:22:55'),
(110, 9, 31, '2026-08-18 06:22:55'),
(111, 10, 31, '2026-08-14 06:22:55'),
(112, 11, 31, '2026-08-13 06:22:55'),
(113, 1, 32, '2026-08-22 06:22:55'),
(114, 4, 32, '2026-08-22 06:22:55'),
(115, 7, 32, '2026-08-24 06:22:55'),
(116, 8, 32, '2026-08-23 06:22:55'),
(117, 7, 34, '2026-08-30 06:22:55'),
(118, 8, 34, '2026-09-01 06:22:55'),
(119, 12, 34, '2026-08-24 06:22:55'),
(120, 4, 35, '2026-08-19 06:22:55'),
(121, 5, 35, '2026-08-31 06:22:55'),
(122, 8, 35, '2026-08-17 06:22:55'),
(123, 3, 36, '2026-08-21 06:22:55'),
(124, 8, 36, '2026-09-01 06:22:55'),
(125, 1, 37, '2026-08-26 06:22:55'),
(126, 4, 37, '2026-08-27 06:22:55'),
(127, 6, 37, '2026-08-16 06:22:55'),
(128, 8, 37, '2026-08-19 06:22:55'),
(129, 12, 37, '2026-08-21 06:22:55'),
(130, 1, 38, '2026-08-29 06:22:55'),
(131, 6, 38, '2026-08-28 06:22:55'),
(132, 7, 38, '2026-08-21 06:22:55'),
(133, 10, 38, '2026-08-18 06:22:55'),
(134, 1, 39, '2026-08-16 06:22:55'),
(135, 2, 39, '2026-08-21 06:22:55'),
(136, 7, 39, '2026-08-22 06:22:55'),
(137, 8, 39, '2026-08-27 06:22:55'),
(138, 10, 39, '2026-08-17 06:22:55'),
(139, 12, 39, '2026-08-25 06:22:55'),
(140, 2, 41, '2026-08-27 06:22:55'),
(141, 4, 41, '2026-08-16 06:22:55'),
(142, 5, 41, '2026-08-22 06:22:55'),
(143, 6, 41, '2026-08-13 06:22:55'),
(144, 7, 41, '2026-08-13 06:22:55'),
(145, 8, 41, '2026-08-25 06:22:55'),
(146, 9, 41, '2026-08-24 06:22:55'),
(147, 3, 42, '2026-08-22 06:22:55'),
(148, 4, 42, '2026-08-27 06:22:55'),
(149, 10, 42, '2026-08-21 06:22:55'),
(150, 3, 44, '2026-08-30 06:22:55');
INSERT INTO `bookmarks` (`id`, `user_id`, `content_id`, `created_at`) VALUES 
(151, 4, 44, '2026-08-15 06:22:55'),
(152, 5, 44, '2026-08-25 06:22:55'),
(153, 7, 44, '2026-09-03 06:22:55'),
(154, 11, 44, '2026-08-16 06:22:55'),
(155, 10, 45, '2026-08-22 06:22:55'),
(156, 11, 45, '2026-08-22 06:22:55'),
(157, 12, 45, '2026-08-24 06:22:55'),
(158, 6, 46, '2026-08-17 06:22:55'),
(159, 7, 46, '2026-09-03 06:22:55'),
(160, 8, 46, '2026-08-22 06:22:55');

--
-- Structure for table `campaigns`
--
DROP TABLE IF EXISTS `campaigns`;
CREATE TABLE `campaigns` (
  `content_id` bigint(20) unsigned NOT NULL,
  `campaign_type` enum('launch','hackathon','contest','sponsorship','referral') NOT NULL DEFAULT 'hackathon',
  `banner_image_url` varchar(255) DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `prize_pool` varchar(100) DEFAULT NULL,
  `rules_markdown` text DEFAULT NULL,
  `submission_fields` text DEFAULT NULL,
  `enable_referral_system` tinyint(1) DEFAULT 1,
  `enable_public_voting` tinyint(1) DEFAULT 1,
  `external_partner_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`content_id`),
  CONSTRAINT `fk_campaigns_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `campaigns` (7 rows)
--
INSERT INTO `campaigns` (`content_id`, `campaign_type`, `banner_image_url`, `start_date`, `end_date`, `prize_pool`, `rules_markdown`, `submission_fields`, `enable_referral_system`, `enable_public_voting`, `external_partner_url`) VALUES 
(30, 'hackathon', 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=1200&auto=format&fit=crop&q=80', '2026-10-01 00:00:00', '2026-11-15 23:59:59', '$15,000 & 500k API Credits', NULL, NULL, 1, 1, NULL),
(31, 'hackathon', 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=1200&auto=format&fit=crop&q=80', '2026-09-15 00:00:00', '2026-10-31 23:59:59', '$10,000 Cloud Grants', NULL, NULL, 1, 1, NULL),
(32, 'contest', 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=1200&auto=format&fit=crop&q=80', '2026-09-01 00:00:00', '2026-10-15 23:59:59', '$8,000 & Pro Subscriptions', NULL, NULL, 1, 1, NULL),
(33, 'hackathon', 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=1200&auto=format&fit=crop&q=80', '2026-11-01 00:00:00', '2026-12-15 23:59:59', '$12,000 Bare Metal Hosting', NULL, NULL, 1, 1, NULL),
(34, 'contest', 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=1200&auto=format&fit=crop&q=80', '2026-10-10 00:00:00', '2026-11-10 23:59:59', '$10,000 Bug Bounty Pool', NULL, NULL, 1, 1, NULL),
(35, 'sponsorship', 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=1200&auto=format&fit=crop&q=80', '2026-08-01 00:00:00', '2026-12-31 23:59:59', '$5,000 Micro-Grants per project', NULL, NULL, 1, 1, NULL),
(47, 'referral', NULL, '2026-01-01 13:25:00', '2026-09-30 13:25:00', '$15,000', NULL, '[]', 1, 1, NULL);

--
-- Structure for table `categories`
--
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `topic_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon_name` varchar(50) DEFAULT 'folder',
  `color_hex` varchar(10) DEFAULT '#6366f1',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `content_count` int(10) unsigned NOT NULL DEFAULT 0,
  `applicable_to` varchar(50) NOT NULL DEFAULT 'all',
  `is_featured_home` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_categories_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories` (19 rows)
--
INSERT INTO `categories` (`id`, `parent_id`, `topic_id`, `name`, `slug`, `description`, `icon_name`, `color_hex`, `sort_order`, `content_count`, `applicable_to`, `is_featured_home`, `created_at`) VALUES 
(1, NULL, 1, 'Artificial Intelligence & ML', 'ai-machine-learning', 'LLMs, Neural Networks, Computer Vision, RAG & Autonomous Agents.', 'cpu', '#6366f1', 1, 28, 'all', 1, '2026-08-27 20:56:57'),
(2, NULL, 2, 'Web Engineering & Full-Stack', 'web-engineering', 'Modern PHP 8, Native JS, Architecture, APIs, CSS Layouts.', 'code-2', '#06b6d4', 2, 22, 'all', 1, '2026-08-27 20:56:57'),
(3, NULL, 3, 'Cloud, Kubernetes & DevOps', 'cloud-devops', 'Docker, Kubernetes, CI/CD, Serverless, Observability & Cloud Security.', 'cloud', '#3b82f6', 3, 16, 'all', 1, '2026-08-27 20:56:57'),
(4, NULL, 4, 'Software Architecture & Systems', 'software-architecture', 'System Design, Microservices, Database Scaling, High-Load Patterns.', 'layers', '#8b5cf6', 4, 19, 'all', 1, '2026-08-27 20:56:57'),
(5, NULL, 5, 'AppSec & Zero-Trust Security', 'security', 'AppSec, Penetration Testing, Cryptography, Zero-Trust Architecture.', 'shield-check', '#10b981', 5, 14, 'all', 1, '2026-08-27 20:56:57'),
(6, NULL, 8, 'Indie Hacking & SaaS Growth', 'indie-hacking', 'Bootstrapping SaaS, Product Discovery, Growth Case Studies.', 'rocket', '#ec4899', 6, 12, 'all', 0, '2026-08-27 20:56:57'),
(7, 1, 1, 'Autonomous Coding Agents', 'autonomous-coding-agents', 'Specialized GPTs and agents that write, refactor, and review code.', 'bot', '#ec4899', 7, 8, 'gpt_listing', 1, '2026-08-27 20:56:57'),
(8, 4, 4, 'System Design & DBA GPTs', 'system-design-gpts', 'Agent personas engineered for MySQL tuning, cloud topology, and SRE.', 'database', '#8b5cf6', 8, 6, 'gpt_listing', 0, '2026-08-27 20:56:57'),
(9, 5, 5, 'AppSec Sentinel Agents', 'appsec-sentinel-agents', 'Security audit bots, vulnerability scanners, and threat modelers.', 'shield-alert', '#10b981', 9, 5, 'gpt_listing', 0, '2026-08-27 20:56:57'),
(10, 1, 1, 'Data Science & Research GPTs', 'data-research-gpts', 'Agents specialized in semantic clustering, mathematical reasoning, and data analysis.', 'sparkles', '#3b82f6', 10, 5, 'gpt_listing', 0, '2026-08-27 20:56:57'),
(11, 2, 2, 'Framework Specialists', 'framework-specialists', 'Dedicated expert assistants for Next.js 15, React 19, Laravel, and Swoole.', 'code', '#06b6d4', 11, 6, 'gpt_listing', 0, '2026-08-27 20:56:57'),
(12, 1, 1, 'System Prompt Architecture', 'system-prompts', 'Tested prompt blueprints with XML tag boundaries and chain-of-thought.', 'terminal', '#06b6d4', 12, 9, 'prompt', 1, '2026-08-27 20:56:57'),
(13, 4, 4, 'Database & Query Optimizers', 'database-prompts', 'Prompts that analyze EXPLAIN plans and design composite indexes.', 'database', '#8b5cf6', 13, 7, 'prompt', 0, '2026-08-27 20:56:57'),
(14, 2, 2, 'Component Refactorers', 'code-refactor-prompts', 'Prompts designed to decompose complex components into clean patterns.', 'repeat', '#ec4899', 14, 6, 'prompt', 0, '2026-08-27 20:56:57'),
(15, 5, 5, 'Security & Pen-Testing Prompts', 'security-prompts', 'Fuzzing blueprints, JWT validation checkers, and auth auditing prompts.', 'lock', '#10b981', 15, 5, 'prompt', 0, '2026-08-27 20:56:57'),
(16, 1, 1, 'AI Code Editors & IDEs', 'ai-code-editors', 'Next-generation editors and autocomplete plugins benchmarked for speed.', 'laptop', '#6366f1', 16, 8, 'ai_tool', 1, '2026-08-27 20:56:57'),
(17, 2, 2, 'Full-Stack UI Generators', 'full-stack-generators', 'Prompt-to-app builders generating full frontend and backend codebases.', 'layout', '#ec4899', 17, 6, 'ai_tool', 0, '2026-08-27 20:56:57'),
(18, 1, 1, 'Autonomous Software Engineers', 'autonomous-engineers', 'End-to-end agents capable of resolving GitHub issues and building features.', 'cpu', '#10b981', 18, 5, 'ai_tool', 0, '2026-08-27 20:56:57'),
(19, 1, 1, 'AI Research & Search Engines', 'ai-search-engines', 'Real-time citation engines and web search intelligence assistants.', 'search', '#3b82f6', 19, 5, 'ai_tool', 0, '2026-08-27 20:56:57');

--
-- Structure for table `challenge_submissions`
--
DROP TABLE IF EXISTS `challenge_submissions`;
CREATE TABLE `challenge_submissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `project_title` varchar(255) NOT NULL,
  `project_slug` varchar(255) DEFAULT NULL,
  `summary` text NOT NULL,
  `detailed_description` longtext DEFAULT NULL,
  `tech_stack` varchar(255) DEFAULT NULL,
  `github_repo_url` varchar(255) DEFAULT NULL,
  `live_demo_url` varchar(255) DEFAULT NULL,
  `video_demo_url` varchar(255) DEFAULT NULL,
  `linked_article_id` bigint(20) unsigned DEFAULT NULL,
  `referral_code` varchar(64) DEFAULT NULL,
  `referral_clicks` int(10) unsigned DEFAULT 0,
  `referral_signups` int(10) unsigned DEFAULT 0,
  `page_views` int(10) unsigned DEFAULT 0,
  `votes_count` int(10) unsigned DEFAULT 0,
  `status` enum('draft','submitted','under_review','shortlisted','winner','runner_up','rejected') DEFAULT 'submitted',
  `score` decimal(5,2) DEFAULT NULL,
  `judge_feedback` text DEFAULT NULL,
  `prize_awarded` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_campaign_id` (`campaign_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_referral_code` (`referral_code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `challenge_submissions` (1 rows)
--
INSERT INTO `challenge_submissions` (`id`, `campaign_id`, `user_id`, `project_title`, `project_slug`, `summary`, `detailed_description`, `tech_stack`, `github_repo_url`, `live_demo_url`, `video_demo_url`, `linked_article_id`, `referral_code`, `referral_clicks`, `referral_signups`, `page_views`, `votes_count`, `status`, `score`, `judge_feedback`, `prize_awarded`, `created_at`, `updated_at`) VALUES 
(4, 30, 8, 'AutoAgent Cloud Orchestrator', NULL, 'Enterprise multi-agent autonomous system for cloud cost optimization.', 'Full system architecture with LangChain and Redis.', 'Next.js, Python, LangChain, PostgreSQL', 'https://github.com/techscout/autoagent', 'https://autoagent.demo.app', 'https://loom.com/share/demo123', NULL, 'c_c482ef51', 1, 0, 0, 1, 'shortlisted', 96.50, 'Exceptional architecture design and clean modular code.', 'Finalist: $2,500 AWS Cloud Credits', '2026-09-04 23:26:07', '2026-09-04 23:26:07');

--
-- Structure for table `challenge_votes`
--
DROP TABLE IF EXISTS `challenge_votes`;
CREATE TABLE `challenge_votes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `submission_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user_sub` (`submission_id`,`user_id`),
  KEY `idx_submission_id` (`submission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `challenge_votes` (4 rows)
--
INSERT INTO `challenge_votes` (`id`, `submission_id`, `user_id`, `ip_address`, `created_at`) VALUES 
(1, 1, 8, '127.0.0.1', '2026-09-04 23:21:14'),
(2, 2, 8, '127.0.0.1', '2026-09-04 23:22:03'),
(3, 3, 8, '127.0.0.1', '2026-09-04 23:25:44'),
(4, 4, 8, '127.0.0.1', '2026-09-04 23:26:07');

--
-- Structure for table `comments`
--
DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `comment_text` text NOT NULL,
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('visible','flagged','hidden','deleted') NOT NULL DEFAULT 'visible',
  `reaction_count` int(10) unsigned NOT NULL DEFAULT 0,
  `reply_count` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_comments_content` (`content_id`),
  KEY `fk_comments_user` (`user_id`),
  KEY `idx_comments_content_time` (`content_id`,`created_at`),
  CONSTRAINT `fk_comments_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comments` (94 rows)
--
INSERT INTO `comments` (`id`, `content_id`, `user_id`, `parent_id`, `comment_text`, `is_pinned`, `status`, `reaction_count`, `reply_count`, `created_at`, `updated_at`) VALUES 
(1, 2, 10, NULL, 'Could you elaborate more on how the tokenizer handles multilingual prompt fallbacks? We hit edge cases with Arabic and Japanese.', 0, 'visible', 18, 0, '2026-08-24 05:35:54', '2026-08-24 05:35:54'),
(2, 2, 2, NULL, 'Phenomenal breakdown of the memory overhead in high-throughput vector search. The benchmark table matches what we saw in production.', 0, 'visible', 17, 0, '2026-08-22 03:05:54', '2026-08-22 03:05:54'),
(3, 2, 5, NULL, 'Very comprehensive guide. One minor note: for PHP 8.3+, you can also use typed class constants for the enum mappings.', 0, 'visible', 11, 0, '2026-08-20 03:42:54', '2026-08-20 03:42:54'),
(4, 3, 12, NULL, 'The latency comparison between QLoRA and full fine-tuning in section 3 is super eye-opening. Saving this for our sprint review.', 1, 'visible', 11, 0, '2026-08-24 03:01:54', '2026-08-24 03:01:54'),
(5, 3, 11, NULL, 'Very comprehensive guide. One minor note: for PHP 8.3+, you can also use typed class constants for the enum mappings.', 0, 'visible', 11, 0, '2026-08-25 23:11:54', '2026-08-25 23:11:54'),
(6, 3, 4, NULL, 'Could you elaborate more on how the tokenizer handles multilingual prompt fallbacks? We hit edge cases with Arabic and Japanese.', 0, 'visible', 15, 0, '2026-08-20 06:09:54', '2026-08-20 06:09:54'),
(7, 4, 8, NULL, 'Bookmarked! This is by far the cleanest implementation guide for local Ollama orchestration with custom tooling.', 1, 'visible', 10, 0, '2026-08-26 01:41:54', '2026-08-26 01:41:54'),
(8, 4, 4, NULL, 'The latency comparison between QLoRA and full fine-tuning in section 3 is super eye-opening. Saving this for our sprint review.', 0, 'visible', 18, 0, '2026-09-03 00:30:54', '2026-09-03 00:30:54'),
(9, 5, 6, NULL, 'Tried this in our staging cluster today. Dropped P99 inference latency from 420ms to 85ms after enabling the KV-cache quantization.', 0, 'visible', 8, 0, '2026-08-27 01:07:54', '2026-08-27 01:07:54'),
(10, 5, 6, NULL, 'Phenomenal breakdown of the memory overhead in high-throughput vector search. The benchmark table matches what we saw in production.', 1, 'visible', 11, 0, '2026-08-18 23:04:54', '2026-08-18 23:04:54'),
(11, 5, 7, NULL, 'Could you elaborate more on how the tokenizer handles multilingual prompt fallbacks? We hit edge cases with Arabic and Japanese.', 0, 'visible', 14, 0, '2026-09-02 00:21:54', '2026-09-02 00:21:54'),
(12, 6, 2, NULL, 'The telemetry and feedback loop mechanism is pure gold. Deploying a similar agentic loop this week.', 1, 'visible', 11, 0, '2026-08-24 03:11:54', '2026-08-24 03:11:54'),
(13, 7, 1, NULL, 'Bookmarked! This is by far the cleanest implementation guide for local Ollama orchestration with custom tooling.', 0, 'visible', 11, 0, '2026-08-24 23:36:54', '2026-08-24 23:36:54'),
(14, 7, 6, NULL, 'Great architectural insight. How does this setup behave under heavy concurrent SSE connections?', 0, 'visible', 16, 0, '2026-08-24 23:33:54', '2026-08-24 23:33:54'),
(15, 8, 2, NULL, 'Tried this in our staging cluster today. Dropped P99 inference latency from 420ms to 85ms after enabling the KV-cache quantization.', 0, 'visible', 17, 0, '2026-08-24 22:44:54', '2026-08-24 22:44:54'),
(16, 9, 1, NULL, 'Phenomenal breakdown of the memory overhead in high-throughput vector search. The benchmark table matches what we saw in production.', 0, 'visible', 4, 0, '2026-08-19 03:46:54', '2026-08-19 03:46:54'),
(17, 10, 10, NULL, 'Could you elaborate more on how the tokenizer handles multilingual prompt fallbacks? We hit edge cases with Arabic and Japanese.', 0, 'visible', 13, 0, '2026-08-29 00:48:54', '2026-08-29 00:48:54'),
(18, 10, 7, NULL, 'Bookmarked! This is by far the cleanest implementation guide for local Ollama orchestration with custom tooling.', 0, 'visible', 3, 0, '2026-08-27 01:23:54', '2026-08-27 01:23:54'),
(19, 11, 10, NULL, 'Bookmarked! This is by far the cleanest implementation guide for local Ollama orchestration with custom tooling.', 0, 'visible', 17, 0, '2026-08-25 05:35:54', '2026-08-25 05:35:54'),
(20, 11, 12, NULL, 'The latency comparison between QLoRA and full fine-tuning in section 3 is super eye-opening. Saving this for our sprint review.', 0, 'visible', 18, 0, '2026-08-28 02:26:54', '2026-08-28 02:26:54'),
(21, 11, 11, NULL, 'Tried this in our staging cluster today. Dropped P99 inference latency from 420ms to 85ms after enabling the KV-cache quantization.', 0, 'visible', 14, 0, '2026-08-26 00:34:54', '2026-08-26 00:34:54'),
(22, 12, 12, NULL, 'The latency comparison between QLoRA and full fine-tuning in section 3 is super eye-opening. Saving this for our sprint review.', 0, 'visible', 7, 0, '2026-08-17 22:36:54', '2026-08-17 22:36:54'),
(23, 12, 7, NULL, 'Great architectural insight. How does this setup behave under heavy concurrent SSE connections?', 0, 'visible', 2, 0, '2026-09-04 06:03:54', '2026-09-04 06:03:54'),
(24, 13, 9, NULL, 'Could you elaborate more on how the tokenizer handles multilingual prompt fallbacks? We hit edge cases with Arabic and Japanese.', 0, 'visible', 17, 0, '2026-08-31 22:30:54', '2026-08-31 22:30:54'),
(25, 14, 5, NULL, 'Bookmarked! This is by far the cleanest implementation guide for local Ollama orchestration with custom tooling.', 0, 'visible', 17, 0, '2026-08-28 04:28:54', '2026-08-28 04:28:54'),
(26, 15, 3, NULL, 'Bookmarked! This is by far the cleanest implementation guide for local Ollama orchestration with custom tooling.', 1, 'visible', 15, 0, '2026-08-21 01:13:54', '2026-08-21 01:13:54'),
(27, 16, 3, NULL, 'Bookmarked! This is by far the cleanest implementation guide for local Ollama orchestration with custom tooling.', 0, 'visible', 8, 0, '2026-08-31 04:43:54', '2026-08-31 04:43:54'),
(28, 16, 1, NULL, 'Phenomenal breakdown of the memory overhead in high-throughput vector search. The benchmark table matches what we saw in production.', 0, 'visible', 10, 0, '2026-08-26 02:58:54', '2026-08-26 02:58:54'),
(29, 17, 9, NULL, 'The latency comparison between QLoRA and full fine-tuning in section 3 is super eye-opening. Saving this for our sprint review.', 0, 'visible', 13, 0, '2026-08-28 05:52:54', '2026-08-28 05:52:54'),
(30, 18, 1, NULL, 'Could you elaborate more on how the tokenizer handles multilingual prompt fallbacks? We hit edge cases with Arabic and Japanese.', 1, 'visible', 7, 0, '2026-08-17 23:52:54', '2026-08-17 23:52:54'),
(31, 18, 1, NULL, 'Very comprehensive guide. One minor note: for PHP 8.3+, you can also use typed class constants for the enum mappings.', 0, 'visible', 5, 0, '2026-08-21 04:28:54', '2026-08-21 04:28:54'),
(32, 19, 9, NULL, 'Phenomenal breakdown of the memory overhead in high-throughput vector search. The benchmark table matches what we saw in production.', 0, 'visible', 5, 0, '2026-08-24 00:46:54', '2026-08-24 00:46:54'),
(33, 19, 4, NULL, 'Very comprehensive guide. One minor note: for PHP 8.3+, you can also use typed class constants for the enum mappings.', 0, 'visible', 14, 0, '2026-09-02 04:28:54', '2026-09-02 04:28:54'),
(34, 19, 6, NULL, 'Great architectural insight. How does this setup behave under heavy concurrent SSE connections?', 0, 'visible', 4, 0, '2026-08-31 01:38:54', '2026-08-31 01:38:54'),
(35, 20, 2, NULL, 'The telemetry and feedback loop mechanism is pure gold. Deploying a similar agentic loop this week.', 1, 'visible', 9, 0, '2026-08-22 02:46:54', '2026-08-22 02:46:54'),
(36, 20, 6, NULL, 'The telemetry and feedback loop mechanism is pure gold. Deploying a similar agentic loop this week.', 1, 'visible', 16, 0, '2026-08-24 03:26:54', '2026-08-24 03:26:54'),
(37, 20, 10, NULL, 'Tried this in our staging cluster today. Dropped P99 inference latency from 420ms to 85ms after enabling the KV-cache quantization.', 0, 'visible', 7, 0, '2026-08-27 00:33:54', '2026-08-27 00:33:54'),
(38, 21, 12, NULL, 'Bookmarked! This is by far the cleanest implementation guide for local Ollama orchestration with custom tooling.', 0, 'visible', 5, 0, '2026-08-18 01:06:54', '2026-08-18 01:06:54'),
(39, 22, 11, NULL, 'The telemetry and feedback loop mechanism is pure gold. Deploying a similar agentic loop this week.', 0, 'visible', 2, 0, '2026-08-21 00:01:54', '2026-08-21 00:01:54'),
(40, 22, 7, NULL, 'Bookmarked! This is by far the cleanest implementation guide for local Ollama orchestration with custom tooling.', 0, 'visible', 13, 0, '2026-08-25 04:40:54', '2026-08-25 04:40:54'),
(41, 23, 12, NULL, 'Phenomenal breakdown of the memory overhead in high-throughput vector search. The benchmark table matches what we saw in production.', 0, 'visible', 3, 0, '2026-08-22 05:05:54', '2026-08-22 05:05:54'),
(42, 23, 2, NULL, 'Very comprehensive guide. One minor note: for PHP 8.3+, you can also use typed class constants for the enum mappings.', 0, 'visible', 13, 0, '2026-08-16 03:17:54', '2026-08-16 03:17:54'),
(43, 24, 3, NULL, 'Bookmarked! This is by far the cleanest implementation guide for local Ollama orchestration with custom tooling.', 0, 'visible', 10, 0, '2026-09-01 23:17:54', '2026-09-01 23:17:54'),
(44, 24, 8, NULL, 'The latency comparison between QLoRA and full fine-tuning in section 3 is super eye-opening. Saving this for our sprint review.', 0, 'visible', 18, 0, '2026-08-29 23:11:54', '2026-08-29 23:11:54'),
(45, 24, 9, NULL, 'Tried this in our staging cluster today. Dropped P99 inference latency from 420ms to 85ms after enabling the KV-cache quantization.', 0, 'visible', 4, 0, '2026-08-30 23:38:54', '2026-08-30 23:38:54'),
(46, 25, 4, NULL, 'Bookmarked! This is by far the cleanest implementation guide for local Ollama orchestration with custom tooling.', 0, 'visible', 12, 0, '2026-08-26 03:57:54', '2026-08-26 03:57:54'),
(47, 25, 8, NULL, 'Bookmarked! This is by far the cleanest implementation guide for local Ollama orchestration with custom tooling.', 0, 'visible', 12, 0, '2026-08-20 02:46:54', '2026-08-20 02:46:54'),
(48, 25, 7, NULL, 'Great architectural insight. How does this setup behave under heavy concurrent SSE connections?', 0, 'visible', 3, 0, '2026-08-19 01:11:54', '2026-08-19 01:11:54'),
(49, 26, 3, NULL, 'The latency comparison between QLoRA and full fine-tuning in section 3 is super eye-opening. Saving this for our sprint review.', 0, 'visible', 16, 0, '2026-08-22 02:53:54', '2026-08-22 02:53:54'),
(50, 26, 8, NULL, 'Bookmarked! This is by far the cleanest implementation guide for local Ollama orchestration with custom tooling.', 0, 'visible', 4, 0, '2026-08-25 02:11:54', '2026-08-25 02:11:54');
INSERT INTO `comments` (`id`, `content_id`, `user_id`, `parent_id`, `comment_text`, `is_pinned`, `status`, `reaction_count`, `reply_count`, `created_at`, `updated_at`) VALUES 
(51, 26, 9, NULL, 'Very comprehensive guide. One minor note: for PHP 8.3+, you can also use typed class constants for the enum mappings.', 1, 'visible', 4, 0, '2026-08-25 01:38:54', '2026-08-25 01:38:54'),
(52, 27, 9, NULL, 'Tried this in our staging cluster today. Dropped P99 inference latency from 420ms to 85ms after enabling the KV-cache quantization.', 0, 'visible', 12, 0, '2026-09-04 02:48:54', '2026-09-04 02:48:54'),
(53, 27, 9, NULL, 'Bookmarked! This is by far the cleanest implementation guide for local Ollama orchestration with custom tooling.', 0, 'visible', 8, 0, '2026-08-23 01:34:54', '2026-08-23 01:34:54'),
(54, 27, 3, NULL, 'The telemetry and feedback loop mechanism is pure gold. Deploying a similar agentic loop this week.', 0, 'visible', 7, 0, '2026-09-02 01:43:54', '2026-09-02 01:43:54'),
(55, 28, 8, NULL, 'The telemetry and feedback loop mechanism is pure gold. Deploying a similar agentic loop this week.', 0, 'visible', 6, 0, '2026-08-25 05:12:54', '2026-08-25 05:12:54'),
(56, 28, 8, NULL, 'Great architectural insight. How does this setup behave under heavy concurrent SSE connections?', 0, 'visible', 15, 0, '2026-09-01 22:03:54', '2026-09-01 22:03:54'),
(57, 28, 9, NULL, 'The latency comparison between QLoRA and full fine-tuning in section 3 is super eye-opening. Saving this for our sprint review.', 0, 'visible', 13, 0, '2026-08-24 02:44:54', '2026-08-24 02:44:54'),
(58, 29, 3, NULL, 'The telemetry and feedback loop mechanism is pure gold. Deploying a similar agentic loop this week.', 0, 'visible', 10, 0, '2026-09-02 05:05:54', '2026-09-02 05:05:54'),
(59, 30, 4, NULL, 'Bookmarked! This is by far the cleanest implementation guide for local Ollama orchestration with custom tooling.', 1, 'visible', 3, 0, '2026-08-25 00:18:54', '2026-08-25 00:18:54'),
(60, 30, 5, NULL, 'Phenomenal breakdown of the memory overhead in high-throughput vector search. The benchmark table matches what we saw in production.', 1, 'visible', 12, 0, '2026-08-28 03:11:54', '2026-08-28 03:11:54'),
(61, 30, 10, NULL, 'Bookmarked! This is by far the cleanest implementation guide for local Ollama orchestration with custom tooling.', 1, 'visible', 4, 0, '2026-08-22 00:10:54', '2026-08-22 00:10:54'),
(62, 31, 5, NULL, 'Bookmarked! This is by far the cleanest implementation guide for local Ollama orchestration with custom tooling.', 0, 'visible', 10, 0, '2026-08-18 02:53:54', '2026-08-18 02:53:54'),
(63, 31, 12, NULL, 'Tried this in our staging cluster today. Dropped P99 inference latency from 420ms to 85ms after enabling the KV-cache quantization.', 0, 'visible', 7, 0, '2026-08-18 00:13:54', '2026-08-18 00:13:54'),
(64, 32, 2, NULL, 'Phenomenal breakdown of the memory overhead in high-throughput vector search. The benchmark table matches what we saw in production.', 0, 'visible', 13, 0, '2026-09-01 22:50:54', '2026-09-01 22:50:54'),
(65, 32, 3, NULL, 'The latency comparison between QLoRA and full fine-tuning in section 3 is super eye-opening. Saving this for our sprint review.', 0, 'visible', 11, 0, '2026-08-20 22:14:54', '2026-08-20 22:14:54'),
(66, 33, 7, NULL, 'Very comprehensive guide. One minor note: for PHP 8.3+, you can also use typed class constants for the enum mappings.', 0, 'visible', 13, 0, '2026-08-20 05:11:54', '2026-08-20 05:11:54'),
(67, 34, 1, NULL, 'Phenomenal breakdown of the memory overhead in high-throughput vector search. The benchmark table matches what we saw in production.', 0, 'visible', 17, 0, '2026-08-20 04:05:54', '2026-08-20 04:05:54'),
(68, 34, 8, NULL, 'Could you elaborate more on how the tokenizer handles multilingual prompt fallbacks? We hit edge cases with Arabic and Japanese.', 0, 'visible', 13, 0, '2026-08-17 02:43:54', '2026-08-17 02:43:54'),
(69, 34, 12, NULL, 'The latency comparison between QLoRA and full fine-tuning in section 3 is super eye-opening. Saving this for our sprint review.', 0, 'visible', 18, 0, '2026-08-20 05:01:54', '2026-08-20 05:01:54'),
(70, 35, 12, NULL, 'Great architectural insight. How does this setup behave under heavy concurrent SSE connections?', 0, 'visible', 2, 0, '2026-08-27 01:45:54', '2026-08-27 01:45:54'),
(71, 35, 2, NULL, 'Phenomenal breakdown of the memory overhead in high-throughput vector search. The benchmark table matches what we saw in production.', 0, 'visible', 2, 0, '2026-08-16 22:05:54', '2026-08-16 22:05:54'),
(72, 35, 3, NULL, 'Bookmarked! This is by far the cleanest implementation guide for local Ollama orchestration with custom tooling.', 0, 'visible', 4, 0, '2026-08-17 03:01:54', '2026-08-17 03:01:54'),
(73, 36, 4, NULL, 'The telemetry and feedback loop mechanism is pure gold. Deploying a similar agentic loop this week.', 0, 'visible', 16, 0, '2026-08-27 05:58:54', '2026-08-27 05:58:54'),
(74, 37, 6, NULL, 'Could you elaborate more on how the tokenizer handles multilingual prompt fallbacks? We hit edge cases with Arabic and Japanese.', 1, 'visible', 14, 0, '2026-08-31 22:06:54', '2026-08-31 22:06:54'),
(75, 38, 2, NULL, 'Tried this in our staging cluster today. Dropped P99 inference latency from 420ms to 85ms after enabling the KV-cache quantization.', 0, 'visible', 3, 0, '2026-08-26 22:49:54', '2026-08-26 22:49:54'),
(76, 38, 3, NULL, 'The telemetry and feedback loop mechanism is pure gold. Deploying a similar agentic loop this week.', 0, 'visible', 5, 0, '2026-08-31 22:26:54', '2026-08-31 22:26:54'),
(77, 38, 10, NULL, 'Bookmarked! This is by far the cleanest implementation guide for local Ollama orchestration with custom tooling.', 0, 'visible', 12, 0, '2026-08-31 22:20:54', '2026-08-31 22:20:54'),
(78, 39, 12, NULL, 'Could you elaborate more on how the tokenizer handles multilingual prompt fallbacks? We hit edge cases with Arabic and Japanese.', 0, 'visible', 4, 0, '2026-09-03 23:07:54', '2026-09-03 23:07:54'),
(79, 39, 4, NULL, 'Phenomenal breakdown of the memory overhead in high-throughput vector search. The benchmark table matches what we saw in production.', 1, 'visible', 12, 0, '2026-08-26 22:33:54', '2026-08-26 22:33:54'),
(80, 39, 10, NULL, 'Bookmarked! This is by far the cleanest implementation guide for local Ollama orchestration with custom tooling.', 0, 'visible', 2, 0, '2026-08-22 02:51:54', '2026-08-22 02:51:54'),
(81, 41, 2, NULL, 'Great architectural insight. How does this setup behave under heavy concurrent SSE connections?', 0, 'visible', 18, 0, '2026-08-22 02:28:54', '2026-08-22 02:28:54'),
(82, 41, 5, NULL, 'Great architectural insight. How does this setup behave under heavy concurrent SSE connections?', 0, 'visible', 13, 0, '2026-08-24 00:16:54', '2026-08-24 00:16:54'),
(83, 41, 9, NULL, 'Very comprehensive guide. One minor note: for PHP 8.3+, you can also use typed class constants for the enum mappings.', 0, 'visible', 15, 0, '2026-08-16 00:04:54', '2026-08-16 00:04:54'),
(84, 42, 12, NULL, 'Could you elaborate more on how the tokenizer handles multilingual prompt fallbacks? We hit edge cases with Arabic and Japanese.', 0, 'visible', 13, 0, '2026-08-24 03:38:54', '2026-08-24 03:38:54'),
(85, 42, 11, NULL, 'Very comprehensive guide. One minor note: for PHP 8.3+, you can also use typed class constants for the enum mappings.', 0, 'visible', 17, 0, '2026-09-03 02:58:54', '2026-09-03 02:58:54'),
(86, 42, 10, NULL, 'Tried this in our staging cluster today. Dropped P99 inference latency from 420ms to 85ms after enabling the KV-cache quantization.', 0, 'visible', 16, 0, '2026-08-22 23:07:54', '2026-08-22 23:07:54'),
(87, 44, 9, NULL, 'Could you elaborate more on how the tokenizer handles multilingual prompt fallbacks? We hit edge cases with Arabic and Japanese.', 0, 'visible', 17, 0, '2026-08-17 00:11:54', '2026-08-17 00:11:54'),
(88, 44, 12, NULL, 'Very comprehensive guide. One minor note: for PHP 8.3+, you can also use typed class constants for the enum mappings.', 0, 'visible', 17, 0, '2026-08-17 05:07:54', '2026-08-17 05:07:54'),
(89, 44, 3, NULL, 'Bookmarked! This is by far the cleanest implementation guide for local Ollama orchestration with custom tooling.', 0, 'visible', 8, 0, '2026-09-04 03:42:54', '2026-09-04 03:42:54'),
(90, 45, 4, NULL, 'Bookmarked! This is by far the cleanest implementation guide for local Ollama orchestration with custom tooling.', 0, 'visible', 7, 0, '2026-08-30 23:51:54', '2026-08-30 23:51:54'),
(91, 45, 11, NULL, 'Great architectural insight. How does this setup behave under heavy concurrent SSE connections?', 0, 'visible', 13, 0, '2026-08-20 00:46:54', '2026-08-20 00:46:54'),
(92, 46, 4, NULL, 'Tried this in our staging cluster today. Dropped P99 inference latency from 420ms to 85ms after enabling the KV-cache quantization.', 0, 'visible', 3, 0, '2026-08-18 05:50:54', '2026-08-18 05:50:54'),
(93, 46, 7, NULL, 'The latency comparison between QLoRA and full fine-tuning in section 3 is super eye-opening. Saving this for our sprint review.', 0, 'visible', 5, 0, '2026-08-27 01:28:54', '2026-08-27 01:28:54'),
(94, 46, 9, NULL, 'Bookmarked! This is by far the cleanest implementation guide for local Ollama orchestration with custom tooling.', 0, 'visible', 11, 0, '2026-08-18 23:50:54', '2026-08-18 23:50:54');

--
-- Structure for table `contact_messages`
--
DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(191) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read','replied') NOT NULL DEFAULT 'unread',
  `reply_text` text DEFAULT NULL,
  `replied_at` datetime DEFAULT NULL,
  `replied_by` int(10) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_contact_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_messages` (3 rows)
--
INSERT INTO `contact_messages` (`id`, `user_id`, `name`, `email`, `subject`, `message`, `status`, `reply_text`, `replied_at`, `replied_by`, `ip_address`, `created_at`) VALUES 
(1, 2, 'Sarah Connor', 'sarah@cyberdyne.ai', 'Enterprise API & Sponsored Challenge Integration', 'Hello TechScout team, we would love to sponsor the next Autonomous Agent Hackathon Challenge with a $10,000 prize pool.', 'unread', NULL, NULL, NULL, '127.0.0.1', '2026-08-30 06:22:55'),
(2, 3, 'Hassan Tariq', 'hassan.tariq@neuralflow.pk', 'AI Tool Verification Inquiry', 'We submitted NeuralFlow to the AI tools directory 2 days ago. Looking forward to editorial verification.', 'read', NULL, NULL, NULL, '127.0.0.1', '2026-08-28 06:22:55'),
(3, NULL, 'Dr. James Thorne', 'thorne@ai-research-institute.org', 'Research Citation & Data Export Collaboration', 'Our university lab is citing TechScouts prompt engineering benchmarks in our upcoming ICLR paper.', 'replied', NULL, NULL, NULL, '127.0.0.1', '2026-08-27 06:22:55');

--
-- Structure for table `content_filters`
--
DROP TABLE IF EXISTS `content_filters`;
CREATE TABLE `content_filters` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pattern` varchar(191) NOT NULL,
  `match_type` enum('exact','contains','regex') NOT NULL DEFAULT 'contains',
  `replacement` varchar(50) NOT NULL DEFAULT '***',
  `action` enum('replace','flag_review','auto_quarantine') NOT NULL DEFAULT 'replace',
  `severity` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure for table `content_revisions`
--
DROP TABLE IF EXISTS `content_revisions`;
CREATE TABLE `content_revisions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` bigint(20) unsigned NOT NULL,
  `editor_id` bigint(20) unsigned NOT NULL,
  `version_number` int(10) unsigned NOT NULL DEFAULT 1,
  `title` varchar(255) NOT NULL,
  `summary` varchar(500) NOT NULL,
  `body_html` longtext DEFAULT NULL,
  `change_summary` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_revisions_content` (`content_id`),
  CONSTRAINT `fk_revisions_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure for table `content_tags`
--
DROP TABLE IF EXISTS `content_tags`;
CREATE TABLE `content_tags` (
  `content_id` bigint(20) unsigned NOT NULL,
  `tag_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`content_id`,`tag_id`),
  KEY `fk_ct_tag` (`tag_id`),
  CONSTRAINT `fk_ct_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ct_tag` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure for table `contents`
--
DROP TABLE IF EXISTS `contents`;
CREATE TABLE `contents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `content_type` enum('article','ai_tool','gpt_listing','prompt','software','resource','collection','campaign') NOT NULL,
  `author_id` bigint(20) unsigned NOT NULL,
  `category_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `summary` varchar(500) NOT NULL,
  `body_html` longtext DEFAULT NULL,
  `featured_image_url` varchar(255) DEFAULT NULL,
  `status` enum('draft','in_review','scheduled','published','rejected','archived') NOT NULL DEFAULT 'draft',
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0,
  `custom_badge` varchar(50) DEFAULT NULL,
  `badge_color` varchar(20) DEFAULT NULL,
  `badge_bg` varchar(40) DEFAULT NULL,
  `visibility` enum('public','members_only','unlisted','private') NOT NULL DEFAULT 'public',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_trending` tinyint(1) NOT NULL DEFAULT 0,
  `is_sponsored` tinyint(1) NOT NULL DEFAULT 0,
  `sponsored_by` varchar(100) DEFAULT NULL,
  `quality_score` decimal(4,2) NOT NULL DEFAULT 0.00,
  `editorial_notes` text DEFAULT NULL,
  `internal_notes` longtext DEFAULT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `seo_title` varchar(191) DEFAULT NULL,
  `seo_description` varchar(300) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL,
  `view_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `unique_view_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `reaction_count` int(10) unsigned NOT NULL DEFAULT 0,
  `comment_count` int(10) unsigned NOT NULL DEFAULT 0,
  `bookmark_count` int(10) unsigned NOT NULL DEFAULT 0,
  `share_count` int(10) unsigned NOT NULL DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `scheduled_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `slug` (`slug`),
  KEY `fk_contents_author` (`author_id`),
  KEY `fk_contents_category` (`category_id`),
  KEY `idx_contents_type_status_pub` (`content_type`,`status`,`published_at`),
  KEY `idx_contents_slug` (`slug`),
  KEY `idx_contents_pinned` (`is_pinned`),
  KEY `idx_contents_composite` (`status`,`content_type`,`is_pinned`,`created_at`),
  FULLTEXT KEY `ft_contents_search` (`title`,`summary`,`body_html`),
  CONSTRAINT `fk_contents_author` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_contents_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contents` (44 rows)
--
INSERT INTO `contents` (`id`, `uuid`, `content_type`, `author_id`, `category_id`, `title`, `slug`, `summary`, `body_html`, `featured_image_url`, `status`, `is_pinned`, `custom_badge`, `badge_color`, `badge_bg`, `visibility`, `is_featured`, `is_trending`, `is_sponsored`, `sponsored_by`, `quality_score`, `editorial_notes`, `internal_notes`, `reviewed_by`, `seo_title`, `seo_description`, `canonical_url`, `view_count`, `unique_view_count`, `reaction_count`, `comment_count`, `bookmark_count`, `share_count`, `published_at`, `scheduled_at`, `created_at`, `updated_at`) VALUES 
(2, 'f2a3b4c5-d6e7-8f9a-0b1c-2d3e4f5a6b7c', 'article', 3, 3, 'Zero-Downtime Kubernetes 1.31 Rolling Upgrades on Bare Metal', 'zero-downtime-kubernetes-upgrades-bare-metal', 'Practical guide to automating etcd snapshots, node cordoning, and kubelet rolling updates without dropping a single packet.', '<h2>1. Upgrade Prerequisites</h2><p>Ensure your control plane has a quorum across all 3 master nodes before starting the kubeadm rolling update procedure.</p><h2>2. Cordon and Drain Nodes</h2><pre><code class=\"language-bash\">kubectl drain node-01 --ignore-daemonsets --delete-emptydir-data\napt-mark unhold kubeadm &amp;&amp; apt-get update &amp;&amp; apt-get install -y kubeadm=1.31.0-00</code></pre><h2>3. Validating Pod Disruption Budgets</h2><p>Always verify PDBs are respected across critical stateful sets to avoid cascade drops.</p>', 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=1200&auto=format&fit=crop&q=80', 'published', 1, 'Editor\'s Choice', '#f59e0b', 'rgba(245,158,11,0.15)', 'public', 0, 1, 0, NULL, 92.00, NULL, 'Editorial Review Note 2026-09-02 19:06:06: Verified benchmarks and system code snippets.', NULL, 'Zero-Downtime Kubernetes 1.31 Rolling Upgrades | TechScout', 'Mastering bare-metal Kubernetes upgrades with zero downtime and automated rollbacks.', NULL, 9037, 6120, 48, 10, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-09-05 15:05:35'),
(3, 'a1111111-1111-1111-1111-111111111111', 'article', 4, 2, 'High-Throughput Realtime WebSockets with PHP Swoole and Redis Streams', 'high-throughput-websockets-php-swoole-redis', 'Architecting a real-time event streaming cluster supporting 100,000 concurrent WebSocket connections using Swoole coroutines.', '<h2>1. Concurrency Bottlenecks in Traditional PHP-FPM</h2><p>Traditional PHP processes terminate on each HTTP lifecycle. Swoole keeps the application memory resident in event-driven workers.</p><h2>2. Implementing Swoole WebSocket Server</h2><pre><code class=\"language-php\">$server = new Swoole\\WebSocket\\Server(\"0.0.0.0\", 9501);\n$server->on(\"message\", function ($server, $frame) {\n    $server->push($frame->fd, \"ack: \" . $frame->data);\n});\n$server->start();</code></pre><h2>3. Redis Stream Fanout</h2><p>Use consumer groups to scale broadcast events horizontally across all socket nodes.</p>', 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=1200&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 1, 1, 0, NULL, 94.00, NULL, NULL, NULL, 'High-Throughput WebSockets with PHP Swoole | TechScout', 'Scale PHP WebSockets to 100k concurrent connections with Swoole and Redis.', NULL, 200000, 7890, 68, 14, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-09-05 10:01:22'),
(4, 'a2222222-2222-2222-2222-222222222222', 'article', 6, 4, 'Designing Resilient Microservices with Distributed Sagas and Outbox Patterns', 'resilient-microservices-distributed-sagas-outbox', 'Solving distributed transaction consistency across microservices without 2-phase locking overhead.', '<h2>1. Dual-Write Problem</h2><p>Writing to a relational database and publishing to message brokers simultaneously leads to silent state discrepancies when network partitions occur.</p><h2>2. Transactional Outbox Pattern</h2><p>Write events to an outbox table within the local DB transaction, then asynchronously stream changes to Kafka with Debezium.</p>', 'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?w=1200&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 1, 0, 0, NULL, 95.00, NULL, NULL, NULL, 'Distributed Sagas & Outbox Pattern in Microservices | TechScout', 'Production architectural guide for distributed transactions and eventual consistency.', NULL, 13801, 9400, 92, 21, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-08-30 11:18:26'),
(5, 'a3333333-3333-3333-3333-333333333333', 'article', 5, 1, 'Deep Dive: Benchmarking Vector Search in PostgreSQL pgvector vs MySQL 8.0', 'benchmarking-vector-search-pgvector-mysql', 'Extensive benchmarks across 1M 1536-dimensional embeddings measuring recall, QPS, and memory footprint.', '<h2>1. Benchmark Environment</h2><p>Evaluated on AMD EPYC 64-core with 128GB RAM running HNSW indexes on 1M embedding chunks.</p><h2>2. Results Summary</h2><p>HNSW indexes provide 98.4% recall at 1,450 queries per second with sub-12ms p99 latency.</p>', 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=1200&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 0, 1, 0, NULL, 97.00, NULL, NULL, NULL, 'Vector Search Benchmarks: pgvector vs MySQL 8 | TechScout', 'Comprehensive vector retrieval performance benchmarks for modern AI pipelines.', NULL, 18400, 13100, 134, 29, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-08-27 20:39:38'),
(6, 'a4444444-4444-4444-4444-444444444444', 'article', 7, 6, 'Hardening Modern REST APIs: JWT Rotation, Rate Limiting, and CSP 3.0', 'hardening-rest-apis-jwt-rate-limiting-csp', 'A complete cybersecurity checklist for production-facing APIs against token hijacking, SSRF, and injection.', '<h2>1. Cryptographic Token Rotation</h2><p>Pair short-lived access tokens (15 mins) with rotating refresh tokens stored in HTTP-only SameSite=Strict cookies.</p><h2>2. Token Bucket Rate Limiting</h2><p>Deploy Redis-backed sliding window rate limiters per IP and API token to prevent denial of service.</p>', 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=1200&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 0, 0, 0, NULL, 91.50, NULL, NULL, NULL, 'Hardening REST APIs & Token Security | TechScout', 'Production security engineering guide for API protection and token management.', NULL, 7600, 5200, 41, 8, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-08-27 20:39:38'),
(7, 'a5555555-5555-5555-5555-555555555555', 'article', 5, 1, 'Building Autonomous Agent Loops in TypeScript with Tool Calling and Reflection', 'building-autonomous-agent-loops-typescript', 'How to implement multi-step autonomous LLM workflows with state validation, rollbacks, and reflection.', '<h2>1. Agent Architecture</h2><p>Autonomous agents require three primitives: observation, reflection, and deterministic tool execution.</p><h2>2. Tool Calling Execution Loop</h2><p>Implement recursive tool dispatch with maximum iteration boundaries and automatic schema validation.</p>', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=1200&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 1, 1, 0, NULL, 98.00, NULL, NULL, NULL, 'Autonomous Agent Loops in TypeScript | TechScout', 'Build multi-step agentic systems with TypeScript and tool calling.', NULL, 22125, 16400, 189, 40, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-08-30 15:11:10'),
(8, 'b1111111-1111-1111-1111-111111111111', 'ai_tool', 1, 1, 'Cursor AI — The AI-First Code Editor', 'cursor-ai-code-editor', 'An intelligent code editor built on VS Code with full codebase indexing, semantic code edits, and inline chat.', '<p>Cursor is an AI-first IDE that indexes your entire repository to provide context-aware autocompletions and automated multi-file refactoring.</p><p>Key capabilities include Composer multi-file agent, instant codebase semantic search, and seamless terminal integration.</p>', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=600&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 1, 1, 0, NULL, 95.00, NULL, NULL, NULL, 'Cursor AI Code Editor Review & Features | TechScout', 'Comprehensive review of Cursor AI code editor, benchmarks, pricing, and capabilities.', NULL, 250003, 18900, 156, 32, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-09-04 22:02:14'),
(9, 'b2222222-2222-2222-2222-222222222222', 'ai_tool', 1, 2, 'v0 by Vercel — Generative UI Design Engine', 'v0-by-vercel', 'Generate responsive UI components and full application mockups using React, Tailwind CSS, and Lucide icons.', '<p>v0 transforms natural language prompts into clean, copy-pasteable frontend components with live sandbox preview.</p><p>Exports to Next.js, Figma, and React native seamlessly.</p>', 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=600&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 1, 0, 0, NULL, 95.00, NULL, NULL, NULL, 'v0 by Vercel Review & Benchmarks | TechScout', 'Generate React and Tailwind UI components with AI prompts in v0 by Vercel.', NULL, 250000, 12400, 92, 14, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-08-30 13:32:14'),
(10, 'b3333333-3333-3333-3333-333333333333', 'ai_tool', 1, 2, 'Bolt.new — In-Browser Full-Stack Development Sandbox', 'bolt-new-ai-web-editor', 'Run Node.js containers, Vite builds, and database instances directly in the browser via WebContainers.', '<p>Bolt.new enables complete end-to-end web application development inside the browser with zero local environment setup.</p>', 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=600&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 1, 1, 0, NULL, 95.00, NULL, NULL, NULL, 'Bolt.new Full-Stack Web Development Sandbox | TechScout', 'Build and deploy full-stack apps in the browser with Bolt.new WebContainers.', NULL, 250000, 14200, 118, 23, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-08-30 13:32:14'),
(11, 'b4444444-4444-4444-4444-444444444444', 'ai_tool', 1, 1, 'Perplexity Pro — Enterprise AI Search & Deep Research', 'perplexity-pro-ai-search', 'Conversational search engine with real-time web grounding, academic source citation, and multi-model switching.', '<p>Perplexity Pro provides verified research with citations across Claude 3.5 Sonnet, GPT-4o, and DeepSeek R1.</p>', 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=600&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 0, 1, 0, NULL, 95.00, NULL, NULL, NULL, 'Perplexity Pro AI Search Review | TechScout', 'In-depth review of Perplexity Pro search, research modes, and API capabilities.', NULL, 250000, 21300, 175, 41, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-08-30 13:32:14'),
(12, 'b5555555-5555-5555-5555-555555555555', 'ai_tool', 1, 1, 'Claude 3.5 Sonnet Artifacts — Interactive Code & Canvas', 'claude-35-sonnet-artifacts', 'Anthropic state-of-the-art reasoning model with interactive standalone code artifacts and SVG visualizers.', '<p>Claude Artifacts display code snippets, UI mockups, and documents side-by-side in real-time execution sandboxes.</p>', 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=600&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 1, 1, 0, NULL, 95.00, NULL, NULL, NULL, 'Claude 3.5 Sonnet Artifacts Review | TechScout', 'Mastering Anthropic Claude Artifacts for rapid frontend and backend prototyping.', NULL, 250003, 26500, 290, 58, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-08-30 15:49:47'),
(13, 'b6666666-6666-6666-6666-666666666666', 'ai_tool', 1, 2, 'Supermaven — Ultra-Fast 300k Context Code Completion', 'supermaven-ai-code-copilot', 'Sub-50 millisecond code autocompletion engine with an expansive 300,000 token context window.', '<p>Supermaven indexes full repository codebases locally to offer near-zero latency completions across 50+ languages.</p>', 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=600&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 0, 0, 0, NULL, 95.00, NULL, NULL, NULL, 'Supermaven AI Code Completion Review | TechScout', 'Fastest code autocompletion tool with 300k token context window.', NULL, 250000, 8900, 76, 11, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-08-30 13:32:14'),
(14, 'b7777777-7777-7777-7777-777777777777', 'ai_tool', 1, 1, 'Devin AI — Autonomous Software Engineering Agent', 'devin-ai-software-engineer', 'Autonomous AI software engineer by Cognition that builds features, debugs repositories, and submits pull requests.', '<p>Devin runs terminal commands, writes tests, resolves merge conflicts, and deploys applications independently.</p>', 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=600&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 1, 1, 0, NULL, 95.00, NULL, NULL, NULL, 'Devin AI Autonomous Software Engineer | TechScout', 'Full analysis of Cognition Devin autonomous coding capabilities and benchmarks.', NULL, 250001, 23800, 210, 48, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-09-02 23:14:39'),
(15, 'b8888888-8888-8888-8888-888888888888', 'ai_tool', 1, 2, 'Lovable.dev — Natural Language Full-Stack Builder', 'lovable-dev-app-builder', 'Build, design, and deploy full-stack web applications directly from conversational chat with Supabase integration.', '<p>Lovable creates production-ready React applications with authentic authentication and database backends in seconds.</p>', 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=600&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 0, 1, 0, NULL, 95.00, NULL, NULL, NULL, 'Lovable.dev Full-Stack Builder Review | TechScout', 'Create and deploy full-stack SaaS applications from prompts with Lovable.dev.', NULL, 250000, 11600, 95, 19, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-08-30 13:32:14'),
(16, 'c1111111-1111-1111-1111-111111111111', 'gpt_listing', 2, 4, 'Full-Stack System Architect GPT', 'full-stack-architect-gpt', 'Specialized AI architect for high-concurrency microservices, DB partitioning, and clean cloud infrastructure.', '<p>This custom GPT acts as a Principal Software Architect. It audits high-level system diagrams, suggests caching strategies, evaluates trade-offs between SQL vs NoSQL, and outputs clean DDD folder structures.</p>', 'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?w=600&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 1, 1, 0, NULL, 98.00, NULL, NULL, NULL, 'Full-Stack System Architect GPT | TechScout', 'Custom GPT agent specialized in system design, distributed scaling, and clean architecture.', NULL, 21510, 16200, 198, 42, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-09-04 22:02:15'),
(17, 'c2222222-2222-2222-2222-222222222222', 'gpt_listing', 6, 4, 'MySQL & PostgreSQL DBA Copilot', 'mysql-dba-performance-copilot', 'Expert Database Administrator that analyzes EXPLAIN execution plans, normalizes schemas, and tunes composite indexes.', '<p>Paste your slow queries and EXPLAIN ANALYZE outputs to get deterministic indexing recommendations and query refactorings.</p>', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=600&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 1, 1, 0, NULL, 96.00, NULL, NULL, NULL, 'MySQL & PostgreSQL DBA Copilot GPT | TechScout', 'Database performance optimization and index tuning custom GPT agent.', NULL, 17900, 13400, 142, 28, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-08-27 20:39:38'),
(18, 'c3333333-3333-3333-3333-333333333333', 'gpt_listing', 3, 3, 'Kubernetes & Cloud Native SRE Bot', 'kubernetes-cloud-sre-bot', 'Diagnose pod crashes, write hardened Helm charts, configure Ingress controllers, and optimize node compute allocation.', '<p>Built for Site Reliability Engineers managing production Kubernetes clusters on AWS, GCP, and bare metal nodes.</p>', 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=600&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 0, 1, 0, NULL, 95.00, NULL, NULL, NULL, 'Kubernetes & Cloud Native SRE Bot | TechScout', 'Cloud-native infrastructure and Kubernetes cluster management custom GPT agent.', NULL, 14300, 10700, 115, 19, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-08-27 20:39:38'),
(19, 'c4444444-4444-4444-4444-444444444444', 'gpt_listing', 4, 2, 'TypeScript & React Clean Code Master', 'typescript-react-clean-code-master', 'Refactors messy React code into atomic components, strict TypeScript generics, and optimized custom hooks.', '<p>Enforces strict typing, eliminates any types, optimizes re-renders with useMemo/useCallback, and generates comprehensive Vitest unit tests.</p>', 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=600&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 1, 0, 0, NULL, 94.00, NULL, NULL, NULL, 'TypeScript & React Clean Code Master GPT | TechScout', 'Strict TypeScript and modern React component refactoring agent.', NULL, 18604, 13900, 150, 31, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-08-29 17:55:17'),
(20, 'c5555555-5555-5555-5555-555555555555', 'gpt_listing', 7, 6, 'AppSec & Penetration Testing Sentinel', 'appsec-pen-testing-sentinel', 'Identifies OWASP Top 10 vulnerabilities, validates input sanitization, audits JWT auth, and hardens HTTP headers.', '<p>Security-first AI agent designed for white-hat vulnerability assessments, SAST code audits, and secure coding verification.</p>', 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=600&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 0, 1, 0, NULL, 97.50, NULL, NULL, NULL, 'AppSec & Penetration Testing Sentinel GPT | TechScout', 'Cybersecurity audit and application security review custom GPT agent.', NULL, 16206, 12100, 137, 24, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-08-30 11:13:43'),
(21, 'c6666666-6666-6666-6666-666666666666', 'gpt_listing', 4, 2, 'Next.js 15 & Server Components Specialist', 'nextjs-15-server-components-specialist', 'Deep expertise in Next.js App Router, React Server Components (RSC), Server Actions, and Turbopack optimization.', '<p>Optimizes data fetching waterfalls, manages cache revalidation tags, and implements zero-bundle-size server components.</p>', 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=600&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 1, 1, 0, NULL, 96.50, NULL, NULL, NULL, 'Next.js 15 & Server Actions Specialist GPT | TechScout', 'Mastering Next.js 15 App Router and React Server Components with custom GPT.', NULL, 22903, 17100, 184, 39, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-08-30 14:54:14'),
(22, 'c7777777-7777-7777-7777-777777777777', 'gpt_listing', 5, 1, 'Autonomous Agent Blueprint Designer', 'autonomous-agent-blueprint-designer', 'Generates prompt chains, state graphs (LangGraph), memory stores, and JSON tool definitions for autonomous agents.', '<p>Architect multi-agent collaboration systems with hierarchical supervisors and deterministic validation gates.</p>', 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=600&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 0, 0, 0, NULL, 95.00, NULL, NULL, NULL, 'Autonomous Agent Blueprint Designer | TechScout', 'Design multi-agent collaboration loops and tool schemas with AI.', NULL, 13500, 9800, 98, 16, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-08-27 20:39:38'),
(23, 'd1111111-1111-1111-1111-111111111111', 'prompt', 2, 4, 'Production MySQL Schema & Index Optimizer', 'mysql-schema-index-optimizer', 'Expert Principal Database Engineer prompt that reviews SQL schemas and outputs exact composite indexes.', '<p>Use this prompt to optimize heavy queries and generate composite index blueprints tailored to your database volume.</p>', 'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?w=600&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 1, 1, 0, NULL, 97.00, NULL, NULL, NULL, 'MySQL Schema & Index Optimizer Prompt | TechScout', 'Production database indexing prompt for high-throughput MySQL and PostgreSQL workloads.', NULL, 19808, 14500, 210, 27, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-09-04 22:02:14'),
(24, 'd2222222-2222-2222-2222-222222222222', 'prompt', 3, 3, 'Multi-Stage Dockerfile Hardener & Size Reducer', 'dockerfile-hardener-size-reducer', 'Transforms bloated Docker containers into non-root, alpine/distroless multi-stage hardened production images.', '<p>Shrink container image footprint by up to 85% while eliminating CVE vulnerabilities and setting up proper health checks.</p>', 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=600&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 1, 0, 0, NULL, 95.00, NULL, NULL, NULL, 'Dockerfile Hardener & Size Reducer Prompt | TechScout', 'Container optimization prompt for minimal attack surface and fast CI/CD builds.', NULL, 15401, 11200, 168, 22, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-08-29 20:33:03'),
(25, 'd3333333-3333-3333-3333-333333333333', 'prompt', 4, 2, 'React Clean Architecture & Custom Hook Refactorer', 'react-clean-architecture-hook-refactorer', 'Decomposes mega-components into pure presentation components, business logic hooks, and memoized selectors.', '<p>Eliminates prop drilling and redundant re-renders by enforcing clean separation of concerns in modern React.</p>', 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=600&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 0, 1, 0, NULL, 96.00, NULL, NULL, NULL, 'React Clean Architecture Refactorer Prompt | TechScout', 'Refactor complex React components into atomic hooks and presentation layers.', NULL, 18200, 13600, 194, 29, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-08-27 20:39:38'),
(26, 'd4444444-4444-4444-4444-444444444444', 'prompt', 2, 2, 'OpenAPI 3.1 Specification & JSON Schema Generator', 'openapi-31-spec-generator', 'Generates complete OpenAPI 3.1 YAML specifications with request body schemas, response codes, and security schemes.', '<p>Feed code controllers or endpoint documentation to automatically output production-ready Swagger/OpenAPI documentation.</p>', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=600&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 1, 1, 0, NULL, 94.50, NULL, NULL, NULL, 'OpenAPI 3.1 Spec Generator Prompt | TechScout', 'Automatic Swagger and OpenAPI specification generator prompt blueprint.', NULL, 14100, 10300, 135, 18, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-08-27 20:39:38'),
(27, 'd5555555-5555-5555-5555-555555555555', 'prompt', 5, 1, 'Senior AI Engineer System Prompt Builder', 'senior-ai-engineer-system-prompt-builder', 'Creates robust system prompts with XML tag boundaries, few-shot reasoning exemplars, and anti-jailbreak guardrails.', '<p>Craft high-reliability prompts for LLM production applications that strictly adhere to structured JSON output.</p>', 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=600&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 1, 1, 0, NULL, 98.50, NULL, NULL, NULL, 'Senior AI System Prompt Builder | TechScout', 'Production system prompt engineering blueprint with defensive guardrails.', NULL, 24805, 18700, 280, 45, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-08-30 14:54:30'),
(28, 'd6666666-6666-6666-6666-666666666666', 'prompt', 7, 6, 'API Penetration Test & Security Audit Checklist', 'api-penetration-test-audit-checklist', 'Simulates white-hat penetration tester checking for BOLA, SSRF, mass assignment, and CORS misconfigurations.', '<p>Audits API routes and endpoint controllers against real-world attack vectors and returns remediation code snippets.</p>', 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=600&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 0, 0, 0, NULL, 93.50, NULL, NULL, NULL, 'API Penetration Test Audit Prompt | TechScout', 'Comprehensive API vulnerability assessment and security hardening prompt.', NULL, 11900, 8700, 104, 15, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-08-27 20:39:38'),
(29, 'd7777777-7777-7777-7777-777777777777', 'prompt', 6, 4, 'Distributed Microservices Resiliency Pattern Blueprint', 'microservices-resiliency-pattern-blueprint', 'Generates circuit breakers, retry policies with exponential backoff and jitter, and fallback handler patterns.', '<p>Prevents cascading failures across distributed services by embedding resilience patterns directly into network clients.</p>', 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=600&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 1, 0, 0, NULL, 96.00, NULL, NULL, NULL, 'Microservices Resiliency Pattern Blueprint | TechScout', 'Circuit breaker and retry policy code generator for distributed systems.', NULL, 16701, 12200, 158, 23, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-08-27 21:02:33'),
(30, 'e1111111-1111-1111-1111-111111111111', 'campaign', 1, 1, 'The Autonomous Agent Challenge 2026', 'autonomous-agent-challenge-2026', 'Build and document a production multi-agent system using TechScout prompt models. $15,000 prize pool!', '<p>Join the 2026 Autonomous Agent Challenge. Submit your open-source agent or architecture breakdown to win cash rewards and global developer exposure.</p>', 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=1200&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 1, 1, 0, NULL, 95.00, NULL, NULL, NULL, 'Autonomous Agent Challenge 2026 | TechScout', 'Join the global multi-agent hackathon challenge with $15,000 in prizes.', NULL, 250003, 22100, 340, 54, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-09-04 22:02:15'),
(31, 'e2222222-2222-2222-2222-222222222222', 'campaign', 1, 2, 'Global Web Performance & Speed Cup 2026', 'global-web-performance-speed-cup-2026', 'Benchmark and optimize web apps for sub-100ms Core Web Vitals. $10,000 in cloud hosting & prizes.', '<p>Compete against frontend and full-stack builders worldwide to achieve the lowest First Contentful Paint and interaction latency.</p>', 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=1200&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 1, 1, 0, NULL, 95.00, NULL, NULL, NULL, 'Global Web Performance Cup 2026 | TechScout', 'Win cloud credits and prizes by optimizing web performance.', NULL, 250001, 13200, 195, 31, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-09-04 22:54:37'),
(32, 'e3333333-3333-3333-3333-333333333333', 'campaign', 1, 1, 'LLM Prompt Engineering Grand Prix', 'llm-prompt-engineering-grand-prix', 'Design token-efficient prompts with 99%+ deterministic accuracy across coding benchmarks. $8,000 prize.', '<p>Submit your prompt blueprints to be evaluated by our automated benchmarking test suites across OpenAI and Claude models.</p>', 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=1200&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 0, 1, 0, NULL, 95.00, NULL, NULL, NULL, 'LLM Prompt Engineering Grand Prix | TechScout', 'Global prompt engineering contest with cash rewards and editorial features.', NULL, 250000, 16500, 240, 38, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-08-30 13:32:14'),
(33, 'e4444444-4444-4444-4444-444444444444', 'campaign', 1, 3, 'Cloud Native & Kubernetes Hackathon', 'cloud-native-kubernetes-hackathon', 'Deploy and automate resilient microservices architectures using Docker, K8s, and GitOps workflows. $12,000 pool.', '<p>Build cloud-native infrastructure solutions with automated zero-downtime deployments and observability dashboards.</p>', 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=1200&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 1, 0, 0, NULL, 95.00, NULL, NULL, NULL, 'Cloud Native Kubernetes Hackathon | TechScout', 'Compete in bare-metal and cloud infrastructure deployment challenges.', NULL, 250000, 14700, 180, 27, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-08-30 13:32:14'),
(34, 'e5555555-5555-5555-5555-555555555555', 'campaign', 1, 6, 'AppSec & Zero-Trust Capture the Flag (CTF)', 'appsec-zero-trust-ctf', 'Identify security flaws in deliberately vulnerable API endpoints and claim bounty flags. $10,000 in rewards.', '<p>Sharpen your white-hat application security skills in our simulated enterprise microservice sandbox environment.</p>', 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=1200&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 0, 1, 0, NULL, 95.00, NULL, NULL, NULL, 'AppSec Capture the Flag CTF 2026 | TechScout', 'Compete in API security challenges and win bug bounty rewards.', NULL, 250000, 12900, 160, 24, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-08-30 13:32:14'),
(35, 'e6666666-6666-6666-6666-666666666666', 'campaign', 1, 5, 'Open Source AI Builder Grants 2026', 'open-source-ai-builder-grants-2026', 'Non-dilutive micro-grants of $5,000 each for independent developers shipping open-source developer tooling.', '<p>Apply for direct funding and promotional distribution across TechScout network for your open-source projects.</p>', 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=1200&auto=format&fit=crop&q=80', 'published', 0, NULL, NULL, NULL, 'public', 1, 1, 0, NULL, 95.00, NULL, NULL, NULL, 'Open Source AI Builder Grants | TechScout', 'Get funded to build open source AI software and tools.', NULL, 250001, 21800, 310, 49, 0, 0, '2026-08-27 20:39:38', NULL, '2026-08-27 20:39:38', '2026-08-30 14:55:15'),
(36, 'fd086b9f04c3e3fbc511bffc39f683df', 'article', 1, 1, 'hellow', 'hellow', 'hellow', '\r\n                      <h1>1. Architecture Overview &amp; Prerequisites</h1>\r\n            <p>Write an introductory overview explaining the problem statement, system design constraints, and technological stack decisions.</p>\r\n            \r\n            <div class=\"word-callout word-callout-tip\">\r\n              <strong>💡 Pro Tip:</strong> You can select any text and apply formatting ribbons above, insert syntax-highlighted code blocks, add tables, or drop diagrams directly into this canvas.\r\n            </div>\r\n\r\n            <h2>2. Implementation Blueprint</h2>\r\n            <p>Detail your step-by-step code implementation and architectural pipeline.</p>\r\n            \r\n            <pre><code>// Example implementation snippet\r\nfunction executePipeline(Context $ctx): Result {\r\n    return $ctx-&gt;benchmark();\r\n}</code></pre>\r\n\r\n            <h2>3. Benchmarks &amp; Key Findings</h2>\r\n            <p>Summarize latency benchmarks, throughput metrics, and takeaways for fellow engineers.</p>\r\n                  ', '', 'in_review', 1, 'Editor\'s Choice', '#f59e0b', 'rgba(245,158,11,0.15)', 'public', 0, 0, 0, NULL, 95.00, 'hhhhh', 'Editorial Review Note 2026-09-02 19:05:36: Verified benchmarks and system code snippets.', 1, NULL, NULL, NULL, 250000, 0, 6, 2, 0, 0, NULL, NULL, '2026-08-27 20:56:59', '2026-09-02 22:05:36'),
(37, 'test-draft-uuid', 'article', 1, 1, 'Unapproved Draft Article Test', 'unapproved-draft-article-test', 'Draft summary', '\r\n                      <p>Draft body</p>                  ', '', 'in_review', 0, NULL, NULL, NULL, 'public', 0, 0, 0, NULL, 95.00, 'Editorial Note: there is a lot of plagiarism there you have to improve the article and again submit', NULL, 1, NULL, NULL, NULL, 250000, 0, 0, 0, 0, 0, NULL, NULL, '2026-08-27 21:10:00', '2026-09-02 21:37:43'),
(38, '83ce2a3c2d9490f45693f46a0a5e593d', 'article', 1, 1, 'AURA — Professional Product & Development Specification', 'aura-professional-product-development-specification', 'Version 1.0 • Windows-first • Python • Local-first AI desktop voice assistant', '<h2>AURA — Professional Product &amp; Development Specification</h2>\r\n\r\n<p>Version 1.0 • Windows-first • Python • Local-first AI desktop voice assistant</p>\r\n\r\n<p>A comprehensive product, architecture, feature, UI, security, automation, AI, and engineering blueprint for building AURA as a professional JARVIS-inspired desktop assistant.</p>\r\n\r\n<h2>1. Executive Vision</h2>\r\n\r\n<p>AURA is a local-first desktop AI assistant designed to combine voice interaction, natural-language understanding, local AI, computer control, productivity, automation, document intelligence, system monitoring, memory, and an extensible skill/plugin architecture. The product should feel like a polished SaaS application while remaining fundamentally a native desktop application.</p>\r\n\r\n<p>The central product idea is: Voice + AI + Computer Control + Automation + Local Intelligence + Personal Productivity + Extensible Skills.</p>\r\n\r\n<p>AURA should not be implemented as a simple pipeline of speech-to-text → chatbot → text-to-speech. It should be implemented as an orchestration platform in which an intent router, context engine, planner, permission layer, skill system, execution engine, memory system, and UI cooperate.</p>\r\n\r\n<h2>2. Product Principles</h2>\r\n\r\n<p>Local-first: core functionality should work without internet whenever technically possible.</p>\r\n\r\n<p>Free-first: prefer open-source/local components and avoid mandatory paid APIs.</p>\r\n\r\n<p>Privacy by design: local data stays local unless the user explicitly enables an external provider.</p>\r\n\r\n<p>Permission by action: AI never receives unrestricted computer control by default.</p>\r\n\r\n<p>Human approval for risky operations: deletion, arbitrary shell execution, credential access, and other sensitive actions require explicit approval.</p>\r\n\r\n<p>Modular architecture: every major capability should be a skill or service rather than hard-coded into the assistant core.</p>\r\n\r\n<p>Observable execution: users should be able to see what AURA did, why it did it, and whether an action succeeded.</p>\r\n\r\n<p>Recoverability: actions should be cancellable and reversible where possible.</p>\r\n\r\n<p>Professional UX: the application should have clear states, loading behavior, errors, accessibility, keyboard navigation, and consistent visual language.</p>\r\n\r\n<p>Extensibility: plugins, skills, automation workflows, model providers, and UI modules should be replaceable without rewriting the core.</p>\r\n\r\n<h2>3. Recommended Technology Strategy</h2>\r\n\r\n<table class=\"word-table\" style=\"width: 100%; border-collapse: collapse; margin: 1.5rem 0;\">\r\n  <tbody><tr>\r\n    <th style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Layer</th>\r\n    <th style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Recommended approach</th>\r\n    <th style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Purpose</th>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Language</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Python 3.12+ (pin supported version)</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Core application, orchestration, AI integration, automation</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Desktop UI</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">PySide6 + Qt/QML</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Premium native desktop interface</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Database</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">SQLite + SQLAlchemy/SQLModel</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Settings, tasks, conversations, logs, metadata</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Vector memory</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Local vector store such as FAISS/Chroma/LanceDB</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Semantic retrieval</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">STT</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Local Whisper-family implementation</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Offline speech recognition</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">TTS</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Local open-source TTS engine</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Offline speech synthesis</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">LLM</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Local model runtime such as llama.cpp/Ollama-compatible architecture</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Reasoning and natural-language understanding</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Computer automation</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">PyAutoGUI, Windows APIs/UI Automation, subprocess, psutil</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Desktop interaction</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Browser</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Playwright</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Structured browser automation</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">OCR</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Tesseract/EasyOCR or equivalent local OCR</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Text extraction</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Scheduling</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">APScheduler or a dedicated scheduler service</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Timers and routines</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Packaging</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">PyInstaller/Nuitka evaluation</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Distributable Windows application</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Testing</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">pytest + pytest-qt + integration tests</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Quality assurance</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Logging</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Python logging/structlog-style structured logs</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Diagnostics and observability</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Configuration</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Typed settings + local config files</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Reliable configuration management</td>\r\n  </tr>\r\n</tbody></table>\r\n\r\n<h2>4. High-Level Architecture</h2>\r\n\r\n<p><code>USER\r\n  ↓\r\nVoice / Text / Global Hotkey\r\n  ↓\r\nInput &amp; Conversation Layer\r\n  ↓\r\nIntent + Context Router\r\n  ↓\r\nPlanner / Agent\r\n  ↓\r\nPermission &amp; Safety Gate\r\n  ↓\r\nSkill / Tool Registry\r\n  ↓\r\nExecution Engine\r\n  ↓\r\nResult Validation\r\n  ↓\r\nMemory + Audit Log\r\n  ↓\r\nVoice + UI Response</code></p>\r\n\r\n<p>The architecture should be event-driven internally. Commands, application changes, timers, files, notifications, and system state changes should produce typed events. Skills subscribe to events instead of directly coupling themselves to every other subsystem.</p>\r\n\r\n<h2>4.1 Core Services</h2>\r\n\r\n<p>AssistantCore: owns the high-level interaction lifecycle.</p>\r\n\r\n<p>IntentRouter: classifies a request into conversation, tool execution, search, automation, or agent task.</p>\r\n\r\n<p>ContextEngine: gathers only the context relevant to the current request.</p>\r\n\r\n<p>Planner: converts complex goals into explicit steps.</p>\r\n\r\n<p>Executor: executes approved tool calls.</p>\r\n\r\n<p>PermissionManager: evaluates action risk and required approval.</p>\r\n\r\n<p>SkillRegistry: discovers and manages skills.</p>\r\n\r\n<p>MemoryManager: handles short-term, long-term, semantic, and task memory.</p>\r\n\r\n<p>EventBus: publishes and subscribes to application events.</p>\r\n\r\n<p>ModelManager: manages local AI models and runtime selection.</p>\r\n\r\n<p>Scheduler: handles reminders, timers, routines, and recurring jobs.</p>\r\n\r\n<p>AuditLogger: records significant decisions and actions.</p>\r\n\r\n<p>HealthMonitor: checks services and dependencies.</p>\r\n\r\n<p>UIBridge: provides asynchronous state updates to the desktop UI.</p>\r\n\r\n<h2>5. Professional Project Structure</h2>\r\n\r\n<p><code>AURA/\r\n├── app/\r\n│   ├── main.py\r\n│   ├── bootstrap.py\r\n│   └── lifecycle.py\r\n├── core/\r\n│   ├── assistant.py\r\n│   ├── router.py\r\n│   ├── planner.py\r\n│   ├── executor.py\r\n│   ├── context.py\r\n│   ├── events.py\r\n│   ├── permissions.py\r\n│   ├── errors.py\r\n│   └── contracts.py\r\n├── ai/\r\n│   ├── llm/\r\n│   ├── stt/\r\n│   ├── tts/\r\n│   ├── vision/\r\n│   ├── embeddings/\r\n│   └── model_manager/\r\n├── voice/\r\n│   ├── wake_word/\r\n│   ├── vad/\r\n│   ├── audio_input/\r\n│   └── audio_output/\r\n├── skills/\r\n│   ├── filesystem/\r\n│   ├── applications/\r\n│   ├── browser/\r\n│   ├── terminal/\r\n│   ├── system/\r\n│   ├── productivity/\r\n│   ├── documents/\r\n│   ├── coding/\r\n│   ├── media/\r\n│   └── communication/\r\n├── automation/\r\n│   ├── triggers/\r\n│   ├── conditions/\r\n│   ├── actions/\r\n│   ├── workflows/\r\n│   └── scheduler/\r\n├── memory/\r\n│   ├── conversation/\r\n│   ├── semantic/\r\n│   ├── personal/\r\n│   └── knowledge/\r\n├── security/\r\n│   ├── permissions/\r\n│   ├── sandbox/\r\n│   └── audit/\r\n├── data/\r\n├── plugins/\r\n├── ui/\r\n│   ├── qml/\r\n│   ├── viewmodels/\r\n│   └── resources/\r\n├── tests/\r\n│   ├── unit/\r\n│   ├── integration/\r\n│   ├── ui/\r\n│   └── fixtures/\r\n├── scripts/\r\n├── docs/\r\n├── config/\r\n├── pyproject.toml\r\n└── README.md</code></p>\r\n\r\n<h2>6. Voice System</h2>\r\n\r\n<h2>6.1 Wake Word</h2>\r\n\r\n<p>Custom wake word</p>\r\n\r\n<p>Sensitivity control</p>\r\n\r\n<p>False-trigger reduction</p>\r\n\r\n<p>Push-to-talk fallback</p>\r\n\r\n<p>Global hotkey activation</p>\r\n\r\n<p>Listening indicator</p>\r\n\r\n<p>Microphone mute state</p>\r\n\r\n<h2>6.2 Speech-to-Text</h2>\r\n\r\n<p>Offline transcription</p>\r\n\r\n<p>Streaming/partial transcription</p>\r\n\r\n<p>Noise handling</p>\r\n\r\n<p>Language detection</p>\r\n\r\n<p>Multiple microphones</p>\r\n\r\n<p>Confidence scoring</p>\r\n\r\n<p>Timeouts</p>\r\n\r\n<p>Recovery when microphone fails</p>\r\n\r\n<h2>6.3 Voice Activity Detection</h2>\r\n\r\n<p>Speech start detection</p>\r\n\r\n<p>Speech stop detection</p>\r\n\r\n<p>Silence timeout</p>\r\n\r\n<p>Background-noise filtering</p>\r\n\r\n<p>Configurable interruption threshold</p>\r\n\r\n<h2>6.4 Text-to-Speech</h2>\r\n\r\n<p>Local voices</p>\r\n\r\n<p>Voice profiles</p>\r\n\r\n<p>Speed</p>\r\n\r\n<p>Pitch</p>\r\n\r\n<p>Volume</p>\r\n\r\n<p>Streaming synthesis</p>\r\n\r\n<p>Interruptible output</p>\r\n\r\n<p>Queue management</p>\r\n\r\n<p>All audio components should run asynchronously so the UI remains responsive. Audio capture, transcription, AI inference, and TTS must never block the main UI thread.</p>\r\n\r\n<h2>7. AI Brain</h2>\r\n\r\n<h2>7.1 AI Routing</h2>\r\n\r\n<p>AURA should first classify requests before invoking a model or tool. Examples include: conversational answer, calculator, filesystem search, application launch, browser task, automation, document query, coding task, or multi-step agent mission.</p>\r\n\r\n<h2>7.2 Local Model Strategy</h2>\r\n\r\n<p>Support multiple local model sizes.</p>\r\n\r\n<p>Allow fast/balanced/smart profiles.</p>\r\n\r\n<p>Unload inactive models to reduce RAM/VRAM pressure.</p>\r\n\r\n<p>Benchmark models on the user\'s hardware.</p>\r\n\r\n<p>Expose model requirements in the UI.</p>\r\n\r\n<p>Keep the model-provider interface abstract so runtimes can be replaced.</p>\r\n\r\n<h2>7.3 Context Engine</h2>\r\n\r\n<p>Active application</p>\r\n\r\n<p>Active window</p>\r\n\r\n<p>Current project</p>\r\n\r\n<p>Current file</p>\r\n\r\n<p>Clipboard</p>\r\n\r\n<p>Recent conversation</p>\r\n\r\n<p>Relevant memories</p>\r\n\r\n<p>Current task</p>\r\n\r\n<p>System state</p>\r\n\r\n<p>Time/date</p>\r\n\r\n<p>Context should be assembled selectively. Never dump the entire filesystem, clipboard history, conversation history, or private database into every AI prompt.</p>\r\n\r\n<h2>8. Memory Architecture</h2>\r\n\r\n<table class=\"word-table\" style=\"width: 100%; border-collapse: collapse; margin: 1.5rem 0;\">\r\n  <tbody><tr>\r\n    <th style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Memory</th>\r\n    <th style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Example</th>\r\n    <th style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Storage</th>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Working memory</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Current conversation/task</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">In-memory + conversation DB</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">User preference</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Preferred response style</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">SQLite</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Task memory</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Current project objective</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">SQLite</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Knowledge memory</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Imported PDFs/notes</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Document DB + vector store</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Semantic memory</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Meaning-based retrieval</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Embeddings + vector store</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Audit history</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">What AURA executed</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">SQLite/log files</td>\r\n  </tr>\r\n</tbody></table>\r\n\r\n<p>Memory must have explicit user controls: view, search, edit, delete, export, and clear. Sensitive data should not be silently promoted into long-term memory.</p>\r\n\r\n<h2>9. Skill System</h2>\r\n\r\n<p>Every operational capability should be represented as a skill exposing typed tools. A skill should declare its name, description, tools, permissions, configuration schema, and event subscriptions.</p>\r\n\r\n<p><code>class Skill:\r\n    name: str\r\n    version: str\r\n    description: str\r\n    permissions: list\r\n    tools: list\r\n    events: list</code></p>\r\n\r\n<p>Tool calls should use structured arguments validated before execution. The AI should never construct raw Python code as the default mechanism for normal actions.</p>\r\n\r\n<h2>10. Desktop Control</h2>\r\n\r\n<p>Open/close/restart applications.</p>\r\n\r\n<p>Focus, minimize, maximize, move, resize, and snap windows.</p>\r\n\r\n<p>Inspect running processes.</p>\r\n\r\n<p>Terminate processes only through permission-controlled actions.</p>\r\n\r\n<p>Create, rename, copy, move, delete, compress, and extract files.</p>\r\n\r\n<p>Lock, sleep, restart, shut down, and log out through high-risk commands.</p>\r\n\r\n<p>Read system state using psutil and platform-specific Windows APIs.</p>\r\n\r\n<p>Use stable OS APIs or UI Automation where possible. Coordinate-based clicking should be a fallback, not the primary automation mechanism.</p>\r\n\r\n<h2>11. Browser Assistant</h2>\r\n\r\n<p>Use Playwright for browser automation where supported.</p>\r\n\r\n<p>Open and close tabs.</p>\r\n\r\n<p>Navigate, search, scroll, click, type, and extract structured text.</p>\r\n\r\n<p>Summarize pages locally.</p>\r\n\r\n<p>Search and group tabs.</p>\r\n\r\n<p>Save and restore sessions.</p>\r\n\r\n<p>Detect duplicate tabs.</p>\r\n\r\n<p>Download files with explicit user control.</p>\r\n\r\n<p>Browser automation should have domain/session boundaries and should not automatically submit sensitive forms or expose credentials.</p>\r\n\r\n<h2>12. Computer Vision</h2>\r\n\r\n<p>Screenshot capture</p>\r\n\r\n<p>OCR</p>\r\n\r\n<p>UI element recognition</p>\r\n\r\n<p>Error/dialog detection</p>\r\n\r\n<p>Visual explanation</p>\r\n\r\n<p>Region analysis</p>\r\n\r\n<p>Screen-state comparison</p>\r\n\r\n<p>For robust automation, prefer accessibility/UI Automation trees and DOM selectors over vision coordinates. Vision should complement deterministic interfaces.</p>\r\n\r\n<h2>13. Files &amp; Document Intelligence</h2>\r\n\r\n<p>Filename and content search.</p>\r\n\r\n<p>Semantic search across documents.</p>\r\n\r\n<p>PDF/Word/spreadsheet/presentation ingestion.</p>\r\n\r\n<p>OCR for scanned documents.</p>\r\n\r\n<p>Summaries and question answering.</p>\r\n\r\n<p>Table extraction.</p>\r\n\r\n<p>Duplicate detection.</p>\r\n\r\n<p>Smart Downloads organization.</p>\r\n\r\n<p>Batch rename and categorization.</p>\r\n\r\n<p>Document ingestion should be a pipeline: detect type → extract text → normalize → chunk → embed → index → record metadata. Store source paths and document identifiers so answers can cite the local source.</p>\r\n\r\n<h2>14. Terminal &amp; Coding Assistant</h2>\r\n\r\n<p>Open terminal.</p>\r\n\r\n<p>Execute approved commands.</p>\r\n\r\n<p>Read and explain output.</p>\r\n\r\n<p>Detect errors.</p>\r\n\r\n<p>Suggest fixes.</p>\r\n\r\n<p>Run tests/builds.</p>\r\n\r\n<p>Inspect Git status/diff.</p>\r\n\r\n<p>Generate commit messages.</p>\r\n\r\n<p>Explain and refactor code.</p>\r\n\r\n<p>Generate tests and documentation.</p>\r\n\r\n<p>Arbitrary shell commands are a high-risk capability. Implement command previews, allowlists, explicit confirmation, working-directory restrictions, timeouts, output limits, and an emergency stop.</p>\r\n\r\n<h2>15. Productivity Suite</h2>\r\n\r\n<p>Tasks: create, edit, complete, prioritize, categorize, schedule, recur.</p>\r\n\r\n<p>Projects: milestones, progress, notes, deadlines.</p>\r\n\r\n<p>Notes: voice notes, tags, search, pinning.</p>\r\n\r\n<p>Reminders: one-time and recurring.</p>\r\n\r\n<p>Calendar: events, conflicts, schedule views.</p>\r\n\r\n<p>Pomodoro: focus/break cycles and statistics.</p>\r\n\r\n<p>Daily briefing and end-of-day summaries.</p>\r\n\r\n<p>Focus, study, work, presentation, gaming, and sleep modes.</p>\r\n\r\n<h2>16. Automation Engine</h2>\r\n\r\n<h2>16.1 Triggers</h2>\r\n\r\n<p>Voice</p>\r\n\r\n<p>Time</p>\r\n\r\n<p>Application opened/closed</p>\r\n\r\n<p>File created/changed</p>\r\n\r\n<p>Startup</p>\r\n\r\n<p>Battery state</p>\r\n\r\n<p>Network state</p>\r\n\r\n<p>Notification/event</p>\r\n\r\n<h2>16.2 Conditions</h2>\r\n\r\n<p>Application exists</p>\r\n\r\n<p>Battery threshold</p>\r\n\r\n<p>File exists</p>\r\n\r\n<p>Network available</p>\r\n\r\n<p>Time/date condition</p>\r\n\r\n<p>CPU/resource threshold</p>\r\n\r\n<p>User profile</p>\r\n\r\n<h2>16.3 Actions</h2>\r\n\r\n<p>Open application</p>\r\n\r\n<p>Close application</p>\r\n\r\n<p>Run approved script</p>\r\n\r\n<p>Move file</p>\r\n\r\n<p>Create note</p>\r\n\r\n<p>Speak response</p>\r\n\r\n<p>Show notification</p>\r\n\r\n<p>Start timer</p>\r\n\r\n<p>Invoke another skill</p>\r\n\r\n<h2>16.4 Natural-Language Automation</h2>\r\n\r\n<p>AURA should turn a request such as \'Every weekday at 9 AM open my work apps and start a 50-minute timer\' into a structured workflow. Show the generated trigger, conditions, and actions before activation.</p>\r\n\r\n<p><code>Workflow\r\n  Trigger: weekdays 09:00\r\n  Actions:\r\n    1. Open VS Code\r\n    2. Open browser\r\n    3. Open project\r\n    4. Start focus timer\r\n  Approval: required before first activation</code></p>\r\n\r\n<h2>17. Agent/Mission System</h2>\r\n\r\n<p>An agent should operate on explicit goals, not vague unrestricted autonomy.</p>\r\n\r\n<p><code>Mission\r\nGoal\r\n↓\r\nPlan\r\n↓\r\nRisk analysis\r\n↓\r\nApproval\r\n↓\r\nExecute step\r\n↓\r\nValidate result\r\n↓\r\nNext step / recover\r\n↓\r\nComplete + report</code></p>\r\n\r\n<p>Mission creation.</p>\r\n\r\n<p>Step planning.</p>\r\n\r\n<p>Tool selection.</p>\r\n\r\n<p>Progress reporting.</p>\r\n\r\n<p>Failure recovery.</p>\r\n\r\n<p>Pause/resume.</p>\r\n\r\n<p>Cancellation.</p>\r\n\r\n<p>Undo where possible.</p>\r\n\r\n<p>Action timeline.</p>\r\n\r\n<p>Mission templates.</p>\r\n\r\n<h2>18. Security &amp; Permission Model</h2>\r\n\r\n<table class=\"word-table\" style=\"width: 100%; border-collapse: collapse; margin: 1.5rem 0;\">\r\n  <tbody><tr>\r\n    <th style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Risk</th>\r\n    <th style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Examples</th>\r\n    <th style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Default</th>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Low</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Open application, read non-sensitive system state</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Allow</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Medium</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Move files, browser actions, create automations</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Confirm when needed</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">High</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Delete files, execute arbitrary shell commands</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Explicit confirmation</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Critical</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Credential access, destructive system actions</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Explicit confirmation + restricted tool</td>\r\n  </tr>\r\n</tbody></table>\r\n\r\n<p>Per-skill permissions.</p>\r\n\r\n<p>Per-tool permissions.</p>\r\n\r\n<p>Temporary permissions.</p>\r\n\r\n<p>Command allowlists.</p>\r\n\r\n<p>Sensitive directory restrictions.</p>\r\n\r\n<p>Audit logs.</p>\r\n\r\n<p>Emergency stop.</p>\r\n\r\n<p>Dry-run mode.</p>\r\n\r\n<p>Action previews.</p>\r\n\r\n<p>User-facing explanation of why permission is required.</p>\r\n\r\n<h2>19. Privacy</h2>\r\n\r\n<p>Offline mode.</p>\r\n\r\n<p>Online/offline indicator.</p>\r\n\r\n<p>Internet kill switch.</p>\r\n\r\n<p>Local database.</p>\r\n\r\n<p>Local AI processing.</p>\r\n\r\n<p>Memory viewer.</p>\r\n\r\n<p>Memory deletion.</p>\r\n\r\n<p>Conversation deletion.</p>\r\n\r\n<p>Telemetry disabled by default if possible.</p>\r\n\r\n<p>Clear disclosure whenever data leaves the machine.</p>\r\n\r\n<h2>20. SaaS-Level UI</h2>\r\n\r\n<h2>20.1 Main Pages</h2>\r\n\r\n<p>Dashboard</p>\r\n\r\n<p>Assistant</p>\r\n\r\n<p>Automations</p>\r\n\r\n<p>Skills</p>\r\n\r\n<p>Memory</p>\r\n\r\n<p>Activity</p>\r\n\r\n<p>System</p>\r\n\r\n<p>Settings</p>\r\n\r\n<h2>20.2 Dashboard</h2>\r\n\r\n<p>The dashboard should show greeting, assistant state, quick actions, system health, recent activity, active tasks, timers, and automation status.</p>\r\n\r\n<h2>20.3 Floating Orb</h2>\r\n\r\n<p>The orb should communicate state: idle, listening, thinking, speaking, executing, and error. The animation must remain subtle and performance-friendly.</p>\r\n\r\n<h2>20.4 Command Center</h2>\r\n\r\n<p>A global command palette should be accessible through a configurable shortcut such as Ctrl+Space. It should accept text or voice and surface actions, skills, files, automations, and recent commands.</p>\r\n\r\n<h2>20.5 Design System</h2>\r\n\r\n<p>Consistent typography</p>\r\n\r\n<p>Spacing scale</p>\r\n\r\n<p>Reusable buttons/cards/dialogs</p>\r\n\r\n<p>Dark/light themes</p>\r\n\r\n<p>Keyboard focus states</p>\r\n\r\n<p>Accessible contrast</p>\r\n\r\n<p>Reduced-motion mode</p>\r\n\r\n<p>Responsive layouts</p>\r\n\r\n<p>Toast notifications</p>\r\n\r\n<p>Loading/skeleton states</p>\r\n\r\n<p>Empty states</p>\r\n\r\n<p>Error states</p>\r\n\r\n<h2>21. UI State Model</h2>\r\n\r\n<p><code>IDLE\r\nLISTENING\r\nTRANSCRIBING\r\nUNDERSTANDING\r\nPLANNING\r\nWAITING_FOR_APPROVAL\r\nEXECUTING\r\nVALIDATING\r\nRESPONDING\r\nERROR\r\nCANCELLED\r\nOFFLINE\r\n</code></p>\r\n\r\n<p>Every state should have a deterministic UI representation and be exposed through a state store/view-model rather than inferred from visual animations.</p>\r\n\r\n<h2>22. Database Design</h2>\r\n\r\n<table class=\"word-table\" style=\"width: 100%; border-collapse: collapse; margin: 1.5rem 0;\">\r\n  <tbody><tr>\r\n    <th style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Entity</th>\r\n    <th style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Important fields</th>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">users</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">id, profile, created_at</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">settings</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">key, value, scope</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">conversations</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">id, title, created_at, updated_at</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">messages</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">id, conversation_id, role, content, timestamp</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">memories</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">id, type, content, importance, created_at</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">documents</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">id, path, hash, metadata, indexed_at</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">tasks</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">id, title, status, priority, due_at</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">automations</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">id, name, definition, enabled</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">runs</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">id, automation_id, status, started_at, finished_at</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">audit_events</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">id, event_type, tool, result, timestamp</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">skills</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">id, name, version, enabled</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">permissions</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">id, subject, action, policy</td>\r\n  </tr>\r\n</tbody></table>\r\n\r\n<h2>23. Event-Driven Design</h2>\r\n\r\n<p><code>VOICE_COMMAND_RECEIVED\r\nTRANSCRIPTION_COMPLETED\r\nINTENT_DETECTED\r\nPLAN_CREATED\r\nAPPROVAL_REQUESTED\r\nACTION_STARTED\r\nACTION_COMPLETED\r\nACTION_FAILED\r\nTASK_CREATED\r\nFILE_CHANGED\r\nAPP_OPENED\r\nTIMER_FINISHED\r\nBATTERY_LOW\r\nNETWORK_CHANGED\r\nMEMORY_UPDATED\r\nMISSION_COMPLETED</code></p>\r\n\r\n<p>Use typed event payloads and avoid circular dependencies. Event handlers should be idempotent when possible.</p>\r\n\r\n<h2>24. Error Handling</h2>\r\n\r\n<p>Typed exceptions per subsystem.</p>\r\n\r\n<p>Human-readable error messages.</p>\r\n\r\n<p>Technical details available in diagnostics.</p>\r\n\r\n<p>Retry only when safe.</p>\r\n\r\n<p>Exponential backoff for transient failures.</p>\r\n\r\n<p>Timeout every external/process operation.</p>\r\n\r\n<p>Cancel long-running tasks.</p>\r\n\r\n<p>Never silently ignore failed actions.</p>\r\n\r\n<p>Return partial-success reports for multi-step tasks.</p>\r\n\r\n<h2>25. Logging &amp; Observability</h2>\r\n\r\n<p>Use structured logs with timestamp, subsystem, operation, correlation ID, severity, duration, and result. Never write passwords, tokens, private keys, or raw sensitive content into ordinary logs.</p>\r\n\r\n<p>Debug mode</p>\r\n\r\n<p>Activity timeline</p>\r\n\r\n<p>Error logs</p>\r\n\r\n<p>Performance metrics</p>\r\n\r\n<p>Tool execution history</p>\r\n\r\n<p>Health checks</p>\r\n\r\n<p>Diagnostic export</p>\r\n\r\n<h2>26. Testing Strategy</h2>\r\n\r\n<h2>26.1 Unit Tests</h2>\r\n\r\n<p>Intent classification</p>\r\n\r\n<p>Permission evaluation</p>\r\n\r\n<p>Workflow parsing</p>\r\n\r\n<p>Memory retrieval</p>\r\n\r\n<p>Database repositories</p>\r\n\r\n<p>Model routing</p>\r\n\r\n<p>File operation safety</p>\r\n\r\n<h2>26.2 Integration Tests</h2>\r\n\r\n<p>STT → router</p>\r\n\r\n<p>Router → skill</p>\r\n\r\n<p>Skill → executor</p>\r\n\r\n<p>Automation → action</p>\r\n\r\n<p>Document ingestion → retrieval</p>\r\n\r\n<p>UI → service layer</p>\r\n\r\n<h2>26.3 End-to-End Tests</h2>\r\n\r\n<p>Voice command to app launch</p>\r\n\r\n<p>Natural-language automation creation</p>\r\n\r\n<p>File organization with approval</p>\r\n\r\n<p>Mission cancellation</p>\r\n\r\n<p>Offline operation</p>\r\n\r\n<h2>26.4 Safety Tests</h2>\r\n\r\n<p>Delete confirmation cannot be bypassed</p>\r\n\r\n<p>Blocked command cannot execute</p>\r\n\r\n<p>Revoked permission is respected</p>\r\n\r\n<p>Emergency stop interrupts active execution</p>\r\n\r\n<h2>27. Performance Engineering</h2>\r\n\r\n<p>Keep UI thread free of blocking work.</p>\r\n\r\n<p>Use asyncio/thread/process workers appropriately.</p>\r\n\r\n<p>Lazy-load heavy AI models.</p>\r\n\r\n<p>Cache embeddings and file metadata.</p>\r\n\r\n<p>Stream STT/TTS/LLM results where possible.</p>\r\n\r\n<p>Throttle system monitoring.</p>\r\n\r\n<p>Use bounded queues for audio and events.</p>\r\n\r\n<p>Limit context size before LLM calls.</p>\r\n\r\n<p>Measure cold-start and warm-start latency.</p>\r\n\r\n<p>Provide low-resource mode.</p>\r\n\r\n<h2>28. Offline/Online Architecture</h2>\r\n\r\n<p><code>Request\r\n  ↓\r\nCan local tools answer?\r\n  ├─ Yes → execute locally\r\n  └─ No\r\n      ↓\r\nCan local LLM answer?\r\n  ├─ Yes → local inference\r\n  └─ No\r\n      ↓\r\nInternet enabled?\r\n  ├─ No → explain limitation\r\n  └─ Yes → optional external provider</code></p>\r\n\r\n<p>The external provider interface should be an adapter. No business logic should depend directly on a particular cloud provider.</p>\r\n\r\n<h2>29. Model Manager</h2>\r\n\r\n<p>Install/remove models</p>\r\n\r\n<p>Select default model</p>\r\n\r\n<p>Model profiles</p>\r\n\r\n<p>Hardware compatibility check</p>\r\n\r\n<p>RAM/VRAM estimates</p>\r\n\r\n<p>Benchmarking</p>\r\n\r\n<p>Model loading status</p>\r\n\r\n<p>Automatic unloading</p>\r\n\r\n<p>Provider abstraction</p>\r\n\r\n<h2>30. Plugin Architecture</h2>\r\n\r\n<p>Plugins should be isolated from the core. Define a manifest containing plugin ID, name, version, compatibility, permissions, tools, and configuration schema. Plugins should be disabled by default until installed/approved.</p>\r\n\r\n<p><code>plugin/\r\n├── manifest.json\r\n├── plugin.py\r\n├── tools/\r\n├── ui/\r\n├── resources/\r\n└── tests/</code></p>\r\n\r\n<h2>31. Developer SDK</h2>\r\n\r\n<p>Skill base class.</p>\r\n\r\n<p>Tool decorator/registration.</p>\r\n\r\n<p>Permission declaration.</p>\r\n\r\n<p>Typed input/output schemas.</p>\r\n\r\n<p>Event subscription API.</p>\r\n\r\n<p>Memory API.</p>\r\n\r\n<p>Notification API.</p>\r\n\r\n<p>UI extension API.</p>\r\n\r\n<p>Automation API.</p>\r\n\r\n<p>Testing utilities.</p>\r\n\r\n<h2>32. Feature Catalog — 220 Capabilities</h2>\r\n\r\n<p>The following is the complete feature inventory established for the AURA concept. Each feature should become a backlog item with acceptance criteria rather than being implemented simultaneously.</p>\r\n\r\n<h2>Core Voice</h2>\r\n\r\n<p>1. Wake word</p>\r\n\r\n<p>2. Custom wake word</p>\r\n\r\n<p>3. Sensitivity</p>\r\n\r\n<p>4. Push-to-talk</p>\r\n\r\n<p>5. Global voice hotkey</p>\r\n\r\n<p>6. Offline STT</p>\r\n\r\n<p>7. Streaming STT</p>\r\n\r\n<p>8. Language detection</p>\r\n\r\n<p>9. Noise filtering</p>\r\n\r\n<p>10. VAD</p>\r\n\r\n<p>11. Silence timeout</p>\r\n\r\n<p>12. Multiple microphones</p>\r\n\r\n<p>13. TTS</p>\r\n\r\n<p>14. Voice profiles</p>\r\n\r\n<p>15. Voice speed</p>\r\n\r\n<p>16. Pitch</p>\r\n\r\n<p>17. Volume</p>\r\n\r\n<p>18. Interruptible speech</p>\r\n\r\n<h2>AI</h2>\r\n\r\n<p>19. Local LLM</p>\r\n\r\n<p>20. Model switching</p>\r\n\r\n<p>21. Model profiles</p>\r\n\r\n<p>22. AI routing</p>\r\n\r\n<p>23. Intent detection</p>\r\n\r\n<p>24. Context awareness</p>\r\n\r\n<p>25. Task decomposition</p>\r\n\r\n<p>26. Planning</p>\r\n\r\n<p>27. Goal tracking</p>\r\n\r\n<p>28. Error recovery</p>\r\n\r\n<p>29. Model benchmarking</p>\r\n\r\n<p>30. Vision model</p>\r\n\r\n<p>31. Embeddings</p>\r\n\r\n<p>32. RAG</p>\r\n\r\n<p>33. Semantic retrieval</p>\r\n\r\n<h2>Desktop</h2>\r\n\r\n<p>34. Open app</p>\r\n\r\n<p>35. Close app</p>\r\n\r\n<p>36. Restart app</p>\r\n\r\n<p>37. Window focus</p>\r\n\r\n<p>38. Minimize/maximize</p>\r\n\r\n<p>39. Move/resize windows</p>\r\n\r\n<p>40. Process list</p>\r\n\r\n<p>41. Process control</p>\r\n\r\n<p>42. File creation</p>\r\n\r\n<p>43. File rename</p>\r\n\r\n<p>44. Copy/move</p>\r\n\r\n<p>45. Delete/restore</p>\r\n\r\n<p>46. Archive/extract</p>\r\n\r\n<p>47. System lock</p>\r\n\r\n<p>48. Sleep/restart/shutdown</p>\r\n\r\n<h2>Browser</h2>\r\n\r\n<p>49. Open browser</p>\r\n\r\n<p>50. Tabs</p>\r\n\r\n<p>51. Navigation</p>\r\n\r\n<p>52. Search</p>\r\n\r\n<p>53. Click/type/scroll</p>\r\n\r\n<p>54. Page reading</p>\r\n\r\n<p>55. Extraction</p>\r\n\r\n<p>56. Downloads</p>\r\n\r\n<p>57. Tab search</p>\r\n\r\n<p>58. Tab groups</p>\r\n\r\n<p>59. Session save/restore</p>\r\n\r\n<p>60. Page summaries</p>\r\n\r\n<p>61. Duplicate tab detection</p>\r\n\r\n<h2>Vision/OCR</h2>\r\n\r\n<p>62. Screenshot capture</p>\r\n\r\n<p>63. Screen analysis</p>\r\n\r\n<p>64. UI detection</p>\r\n\r\n<p>65. OCR</p>\r\n\r\n<p>66. Dialog detection</p>\r\n\r\n<p>67. Error detection</p>\r\n\r\n<p>68. Region analysis</p>\r\n\r\n<p>69. Visual comparison</p>\r\n\r\n<h2>Clipboard</h2>\r\n\r\n<p>70. Clipboard history</p>\r\n\r\n<p>71. Text/image/file history</p>\r\n\r\n<p>72. Search</p>\r\n\r\n<p>73. Pin</p>\r\n\r\n<p>74. AI summarize</p>\r\n\r\n<p>75. Rewrite</p>\r\n\r\n<p>76. Translate</p>\r\n\r\n<p>77. Format</p>\r\n\r\n<p>78. Extract</p>\r\n\r\n<h2>Productivity</h2>\r\n\r\n<p>79. Tasks</p>\r\n\r\n<p>80. Projects</p>\r\n\r\n<p>81. Notes</p>\r\n\r\n<p>82. Voice notes</p>\r\n\r\n<p>83. Reminders</p>\r\n\r\n<p>84. Calendar</p>\r\n\r\n<p>85. Conflict detection</p>\r\n\r\n<p>86. Pomodoro</p>\r\n\r\n<p>87. Daily briefing</p>\r\n\r\n<p>88. End-of-day summary</p>\r\n\r\n<p>89. Focus mode</p>\r\n\r\n<p>90. Study mode</p>\r\n\r\n<p>91. Work mode</p>\r\n\r\n<p>92. Presentation mode</p>\r\n\r\n<p>93. Gaming mode</p>\r\n\r\n<p>94. Sleep mode</p>\r\n\r\n<h2>Documents</h2>\r\n\r\n<p>95. PDF reading</p>\r\n\r\n<p>96. PDF search</p>\r\n\r\n<p>97. PDF summary</p>\r\n\r\n<p>98. PDF Q&amp;A</p>\r\n\r\n<p>99. OCR PDFs</p>\r\n\r\n<p>100. Word processing</p>\r\n\r\n<p>101. Spreadsheet analysis</p>\r\n\r\n<p>102. Formula assistance</p>\r\n\r\n<p>103. Chart generation</p>\r\n\r\n<p>104. PowerPoint analysis</p>\r\n\r\n<p>105. Document generation</p>\r\n\r\n<h2>Coding</h2>\r\n\r\n<p>106. Code explanation</p>\r\n\r\n<p>107. Bug finding</p>\r\n\r\n<p>108. Refactoring</p>\r\n\r\n<p>109. Optimization</p>\r\n\r\n<p>110. Tests</p>\r\n\r\n<p>111. Documentation</p>\r\n\r\n<p>112. Git status</p>\r\n\r\n<p>113. Git diff</p>\r\n\r\n<p>114. Commit assistance</p>\r\n\r\n<p>115. Build/test execution</p>\r\n\r\n<p>116. Terminal explanations</p>\r\n\r\n<h2>System</h2>\r\n\r\n<p>117. CPU monitoring</p>\r\n\r\n<p>118. GPU monitoring</p>\r\n\r\n<p>119. RAM</p>\r\n\r\n<p>120. Storage</p>\r\n\r\n<p>121. Temperature</p>\r\n\r\n<p>122. Fan status</p>\r\n\r\n<p>123. Battery</p>\r\n\r\n<p>124. Network status</p>\r\n\r\n<p>125. Ping</p>\r\n\r\n<p>126. Disk cleanup suggestions</p>\r\n\r\n<p>127. Crash detection</p>\r\n\r\n<p>128. Service health</p>\r\n\r\n<p>129. Diagnostics</p>\r\n\r\n<h2>Automation</h2>\r\n\r\n<p>130. Time triggers</p>\r\n\r\n<p>131. Voice triggers</p>\r\n\r\n<p>132. App triggers</p>\r\n\r\n<p>133. File triggers</p>\r\n\r\n<p>134. Startup triggers</p>\r\n\r\n<p>135. Battery triggers</p>\r\n\r\n<p>136. Network triggers</p>\r\n\r\n<p>137. Conditions</p>\r\n\r\n<p>138. Actions</p>\r\n\r\n<p>139. Workflow builder</p>\r\n\r\n<p>140. Natural-language workflow generation</p>\r\n\r\n<p>141. Schedules</p>\r\n\r\n<p>142. Routines</p>\r\n\r\n<p>143. Approval flow</p>\r\n\r\n<p>144. Undo</p>\r\n\r\n<h2>Memory</h2>\r\n\r\n<p>145. Conversation memory</p>\r\n\r\n<p>146. User memory</p>\r\n\r\n<p>147. Task memory</p>\r\n\r\n<p>148. Knowledge memory</p>\r\n\r\n<p>149. Semantic search</p>\r\n\r\n<p>150. Memory viewer</p>\r\n\r\n<p>151. Memory edit</p>\r\n\r\n<p>152. Memory delete</p>\r\n\r\n<p>153. Memory export</p>\r\n\r\n<p>154. Memory expiration</p>\r\n\r\n<h2>Security</h2>\r\n\r\n<p>155. PIN/password</p>\r\n\r\n<p>156. OS authentication</p>\r\n\r\n<p>157. Permission levels</p>\r\n\r\n<p>158. Tool permissions</p>\r\n\r\n<p>159. Temporary permissions</p>\r\n\r\n<p>160. Command allowlist</p>\r\n\r\n<p>161. Sandboxing</p>\r\n\r\n<p>162. Audit logs</p>\r\n\r\n<p>163. Dry-run</p>\r\n\r\n<p>164. Emergency stop</p>\r\n\r\n<p>165. Privacy dashboard</p>\r\n\r\n<p>166. Offline-only mode</p>\r\n\r\n<h2>UI</h2>\r\n\r\n<p>167. Dashboard</p>\r\n\r\n<p>168. Assistant page</p>\r\n\r\n<p>169. Automation page</p>\r\n\r\n<p>170. Skills page</p>\r\n\r\n<p>171. Memory page</p>\r\n\r\n<p>172. Activity page</p>\r\n\r\n<p>173. System page</p>\r\n\r\n<p>174. Settings</p>\r\n\r\n<p>175. Command palette</p>\r\n\r\n<p>176. Floating orb</p>\r\n\r\n<p>177. System tray</p>\r\n\r\n<p>178. Global hotkeys</p>\r\n\r\n<p>179. Themes</p>\r\n\r\n<p>180. Animations</p>\r\n\r\n<p>181. Multi-monitor</p>\r\n\r\n<p>182. Accessibility</p>\r\n\r\n<h2>Advanced Agents</h2>\r\n\r\n<p>183. Agent mode</p>\r\n\r\n<p>184. Mission system</p>\r\n\r\n<p>185. Multi-step execution</p>\r\n\r\n<p>186. Progress tracking</p>\r\n\r\n<p>187. Pause/resume</p>\r\n\r\n<p>188. Cancellation</p>\r\n\r\n<p>189. Self-correction</p>\r\n\r\n<p>190. Long-running tasks</p>\r\n\r\n<p>191. Mission templates</p>\r\n\r\n<p>192. Mission history</p>\r\n\r\n<p>193. Agent collaboration</p>\r\n\r\n<p>194. Proactive suggestions</p>\r\n\r\n<p>195. Goal-based interaction</p>\r\n\r\n<h2>Extensibility</h2>\r\n\r\n<p>196. Skill registry</p>\r\n\r\n<p>197. Plugin manager</p>\r\n\r\n<p>198. Plugin permissions</p>\r\n\r\n<p>199. Plugin updates</p>\r\n\r\n<p>200. Skill builder</p>\r\n\r\n<p>201. Developer API</p>\r\n\r\n<p>202. SDK</p>\r\n\r\n<p>203. Event API</p>\r\n\r\n<p>204. Memory API</p>\r\n\r\n<p>205. Automation API</p>\r\n\r\n<p>206. UI API</p>\r\n\r\n<h2>33. Development Roadmap</h2>\r\n\r\n<h2>Phase 0 — Product Foundation</h2>\r\n\r\n<p>Create repository and branching strategy.</p>\r\n\r\n<p>Set up pyproject.toml, linting, formatting, type checking, tests, and CI.</p>\r\n\r\n<p>Create application lifecycle and configuration system.</p>\r\n\r\n<p>Create logging and error framework.</p>\r\n\r\n<p>Create SQLite schema and migrations.</p>\r\n\r\n<p>Create event bus and service container.</p>\r\n\r\n<h2>Phase 1 — MVP Voice Assistant</h2>\r\n\r\n<p>Build desktop shell.</p>\r\n\r\n<p>Implement microphone input.</p>\r\n\r\n<p>Implement local STT.</p>\r\n\r\n<p>Implement local TTS.</p>\r\n\r\n<p>Implement text chat.</p>\r\n\r\n<p>Implement local LLM adapter.</p>\r\n\r\n<p>Implement basic intent router.</p>\r\n\r\n<p>Implement open-application skill.</p>\r\n\r\n<p>Implement system information skill.</p>\r\n\r\n<p>Implement global hotkey and tray.</p>\r\n\r\n<h2>Phase 2 — Computer Control</h2>\r\n\r\n<p>Filesystem skill.</p>\r\n\r\n<p>Application/window skill.</p>\r\n\r\n<p>Terminal skill with permission gate.</p>\r\n\r\n<p>Clipboard manager.</p>\r\n\r\n<p>Screenshot/OCR.</p>\r\n\r\n<p>Activity timeline.</p>\r\n\r\n<p>Undo framework.</p>\r\n\r\n<h2>Phase 3 — Productivity</h2>\r\n\r\n<p>Tasks.</p>\r\n\r\n<p>Notes.</p>\r\n\r\n<p>Reminders.</p>\r\n\r\n<p>Timers.</p>\r\n\r\n<p>Pomodoro.</p>\r\n\r\n<p>Calendar abstraction.</p>\r\n\r\n<p>Work/study modes.</p>\r\n\r\n<p>Daily briefing.</p>\r\n\r\n<h2>Phase 4 — AI Knowledge</h2>\r\n\r\n<p>Document ingestion.</p>\r\n\r\n<p>Embeddings.</p>\r\n\r\n<p>Vector search.</p>\r\n\r\n<p>Local RAG.</p>\r\n\r\n<p>Memory controls.</p>\r\n\r\n<p>Conversation search.</p>\r\n\r\n<p>Semantic file search.</p>\r\n\r\n<h2>Phase 5 — Automation</h2>\r\n\r\n<p>Trigger engine.</p>\r\n\r\n<p>Condition engine.</p>\r\n\r\n<p>Action engine.</p>\r\n\r\n<p>Scheduler.</p>\r\n\r\n<p>Visual workflow builder.</p>\r\n\r\n<p>Natural-language workflow generation.</p>\r\n\r\n<p>Approval and audit system.</p>\r\n\r\n<h2>Phase 6 — Agents</h2>\r\n\r\n<p>Planner.</p>\r\n\r\n<p>Mission object.</p>\r\n\r\n<p>Step executor.</p>\r\n\r\n<p>Validation.</p>\r\n\r\n<p>Recovery.</p>\r\n\r\n<p>Pause/resume/cancel.</p>\r\n\r\n<p>Progress UI.</p>\r\n\r\n<p>Mission history.</p>\r\n\r\n<h2>Phase 7 — Browser &amp; Advanced Integrations</h2>\r\n\r\n<p>Playwright integration.</p>\r\n\r\n<p>Browser skill.</p>\r\n\r\n<p>Tab/session intelligence.</p>\r\n\r\n<p>Optional communication integrations.</p>\r\n\r\n<p>Developer integrations.</p>\r\n\r\n<p>Plugin SDK.</p>\r\n\r\n<h2>Phase 8 — Professionalization</h2>\r\n\r\n<p>Performance profiling.</p>\r\n\r\n<p>Crash recovery.</p>\r\n\r\n<p>Installer.</p>\r\n\r\n<p>Auto-update architecture.</p>\r\n\r\n<p>Accessibility audit.</p>\r\n\r\n<p>Security review.</p>\r\n\r\n<p>End-to-end testing.</p>\r\n\r\n<p>Documentation.</p>\r\n\r\n<p>Release channels.</p>\r\n\r\n<h2>34. Professional Engineering Workflow</h2>\r\n\r\n<p>Create a product requirements document and freeze the MVP scope.</p>\r\n\r\n<p>Create architecture decision records for major technology choices.</p>\r\n\r\n<p>Define interfaces before implementations.</p>\r\n\r\n<p>Implement one vertical slice end-to-end before adding dozens of features.</p>\r\n\r\n<p>Write unit tests alongside core services.</p>\r\n\r\n<p>Use feature branches and pull requests even if working alone.</p>\r\n\r\n<p>Run formatting, linting, type checking, unit tests, and build checks before release.</p>\r\n\r\n<p>Use semantic versioning and migration scripts.</p>\r\n\r\n<p>Keep secrets outside source control.</p>\r\n\r\n<p>Maintain a changelog and technical documentation.</p>\r\n\r\n<h2>35. Acceptance Criteria for a Professional Release</h2>\r\n\r\n<p>Application starts reliably on supported Windows versions.</p>\r\n\r\n<p>Voice interaction does not freeze the UI.</p>\r\n\r\n<p>Every high-risk action has an enforceable permission check.</p>\r\n\r\n<p>Offline mode works for the declared offline feature set.</p>\r\n\r\n<p>No sensitive information is written to ordinary logs.</p>\r\n\r\n<p>Failed actions are reported honestly.</p>\r\n\r\n<p>Long-running tasks can be cancelled.</p>\r\n\r\n<p>Settings persist correctly.</p>\r\n\r\n<p>Database migrations are tested.</p>\r\n\r\n<p>Application can recover from common service failures.</p>\r\n\r\n<p>Installer creates a clean user installation.</p>\r\n\r\n<p>Uninstall removes or preserves user data according to an explicit choice.</p>\r\n\r\n<p>Documentation explains permissions, privacy, supported models, and limitations.</p>\r\n\r\n<h2>36. Important Scope Decision</h2>\r\n\r\n<p>Do not attempt to implement all 200+ features in the first release. The professional approach is to build the platform underneath the features first. The first usable release should prove six things: reliable voice input, local AI reasoning, safe computer control, persistent memory, polished UI, and a skill/automation architecture. Once those foundations are stable, additional features become modules rather than rewrites.</p>\r\n\r\n<h2>37. Suggested MVP</h2>\r\n\r\n<table class=\"word-table\" style=\"width: 100%; border-collapse: collapse; margin: 1.5rem 0;\">\r\n  <tbody><tr>\r\n    <th style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Area</th>\r\n    <th style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">MVP scope</th>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Voice</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Wake/hotkey, local STT, local TTS</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">AI</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">One local LLM + router + context</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Computer</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Open apps, filesystem, system info</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">UI</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Dashboard, assistant, floating orb, tray</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Memory</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Conversation history + simple user memory</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Automation</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Timers + basic routines</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Security</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Permissions + confirmations + emergency stop</td>\r\n  </tr>\r\n  <tr>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Observability</td>\r\n    <td style=\"border: 1px solid rgba(148,163,184,0.2); padding: 0.6rem 0.85rem;\">Activity timeline + structured logs</td>\r\n  </tr>\r\n</tbody></table>\r\n\r\n<h2>38. Long-Term AURA Vision</h2>\r\n\r\n<p>The final product should feel less like a chatbot and more like an intelligent control plane for the user\'s computer. AURA should be able to understand natural language, inspect the relevant context, choose the correct skill, plan a task, request permission when needed, execute actions, verify results, recover from failures, remember useful information, and explain what it did.</p>\r\n\r\n<p>The strongest differentiator is not simply having a voice. It is the combination of local intelligence, safe computer control, transparent automation, memory, and a modular architecture that allows AURA to continuously gain capabilities without becoming a monolithic application.</p>\r\n\r\n<h2>39. Final Build Philosophy</h2>\r\n\r\n<p>Build the core once and add capabilities as skills. Keep AI separate from execution. Keep permissions separate from AI. Keep UI separate from business logic. Keep local models behind adapters. Keep every action observable. Treat safety, cancellation, privacy, and error recovery as first-class engineering requirements rather than features added at the end.</p>', '/assets/uploads/images/img_20260830_130438_57e28d6c.jpg', 'published', 0, NULL, NULL, NULL, 'public', 0, 0, 0, NULL, 95.00, NULL, NULL, 1, NULL, NULL, NULL, 250006, 0, 1, 0, 0, 0, '2026-08-30 13:08:32', NULL, '2026-08-30 13:04:41', '2026-09-04 23:37:55'),
(39, '19c77dd8c4f27c57b6719f4c8e825789', 'ai_tool', 1, 1, 'Cursor AI Code Editor Pro', 'cursor-ai-code-editor-pro', 'Next-generation AI-first code editor designed for ultra-productive pair programming with Claude 3.5 Sonnet and GPT-4o.', '<h3>Architectural Deep-Dive</h3><p>Cursor indexes entire repositories using local and remote vector embeddings, enabling multi-file refactors with single prompts.</p>', 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=400', 'published', 0, 'Staff Pick', '#ec4899', 'rgba(236,72,153,0.15)', 'public', 0, 0, 0, NULL, 95.00, NULL, NULL, NULL, NULL, NULL, NULL, 250000, 0, 0, 0, 0, 0, '2026-08-30 13:22:59', NULL, '2026-08-30 13:22:59', '2026-09-02 22:07:11'),
(41, '8341650ed709f7349cac1b6c4714cceb', 'ai_tool', 1, 1, 'Cursor AI Code Editor Pro', 'cursor-ai-code-editor-pro-2', 'Next-generation AI-first code editor designed for ultra-productive pair programming with Claude 3.5 Sonnet and GPT-4o.', '<h3>Architectural Deep-Dive</h3><p>Cursor indexes entire repositories using local and remote vector embeddings, enabling multi-file refactors with single prompts.</p>', 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=400', 'published', 0, NULL, NULL, NULL, 'public', 0, 0, 0, NULL, 95.00, NULL, NULL, NULL, NULL, NULL, NULL, 250000, 0, 0, 0, 0, 0, '2026-08-30 13:23:37', NULL, '2026-08-30 13:23:37', '2026-08-30 13:32:14'),
(42, 'c9511921ffd5801f9192ed17d59d3425', 'ai_tool', 1, 1, 'Cursor AI Code Editor Pro', 'cursor-ai-code-editor-pro-3', 'Next-generation AI-first code editor designed for ultra-productive pair programming with Claude 3.5 Sonnet and GPT-4o.', '<h3>Architectural Deep-Dive</h3><p>Cursor indexes entire repositories using local and remote vector embeddings, enabling multi-file refactors with single prompts.</p>', 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=400', 'published', 0, NULL, NULL, NULL, 'public', 0, 0, 0, NULL, 95.00, NULL, NULL, NULL, NULL, NULL, NULL, 250002, 0, 0, 0, 0, 0, '2026-08-30 13:23:57', NULL, '2026-08-30 13:23:57', '2026-09-04 22:33:58'),
(44, '43afd1ecb32ddb3193c18e348945adab', 'article', 2, 16, 'asdfghjk', 'asdfghjk', 'asdfghj', 'AURA — Professional Product & Development Specification\r\nVersion 1.0 • Windows-first • Python • Local-first AI desktop voice assistant\r\n\r\nA comprehensive product, architecture, feature, UI, security, automation, AI, and engineering blueprint for building AURA as a professional JARVIS-inspired desktop assistant.\r\n\r\n1. Executive Vision\r\nAURA is a local-first desktop AI assistant designed to combine voice interaction, natural-language understanding, local AI, computer control, productivity, automation, document intelligence, system monitoring, memory, and an extensible skill/plugin architecture. The product should feel like a polished SaaS application while remaining fundamentally a native desktop application.\r\n\r\nThe central product idea is: Voice + AI + Computer Control + Automation + Local Intelligence + Personal Productivity + Extensible Skills.\r\n\r\nAURA should not be implemented as a simple pipeline of speech-to-text → chatbot → text-to-speech. It should be implemented as an orchestration platform in which an intent router, context engine, planner, permission layer, skill system, execution engine, memory system, and UI cooperate.\r\n\r\n2. Product Principles\r\nLocal-first: core functionality should work without internet whenever technically possible.\r\n\r\nFree-first: prefer open-source/local components and avoid mandatory paid APIs.\r\n\r\nPrivacy by design: local data stays local unless the user explicitly enables an external provider.\r\n\r\nPermission by action: AI never receives unrestricted computer control by default.\r\n\r\nHuman approval for risky operations: deletion, arbitrary shell execution, credential access, and other sensitive actions require explicit approval.\r\n\r\nModular architecture: every major capability should be a skill or service rather than hard-coded into the assistant core.\r\n\r\nObservable execution: users should be able to see what AURA did, why it did it, and whether an action succeeded.\r\n\r\nRecoverability: actions should be cancellable and reversible where possible.\r\n\r\nProfessional UX: the application should have clear states, loading behavior, errors, accessibility, keyboard navigation, and consistent visual language.\r\n\r\nExtensibility: plugins, skills, automation workflows, model providers, and UI modules should be replaceable without rewriting the core.\r\n\r\n3. Recommended Technology Strategy\r\n', '', 'in_review', 0, NULL, NULL, NULL, 'public', 0, 0, 0, NULL, 70.00, 'there is a lot of plagiarism there you have to improve the article and again submit', NULL, 1, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, '2026-08-30 14:48:21', NULL, '2026-08-30 14:48:21', '2026-09-02 21:37:24'),
(45, '3c6c40ab60c0fe83106658a0be01de6b', 'article', 1, 1, 'asdfghj', 'asdfghj', 'asdfghjk', '\r\n                      <h2>1. Architecture Overview &amp; Problem Statement</h2>\r\n            <p>Write an introductory overview explaining the problem statement, system design constraints, and technological&nbsp;<a href=\"http://localhost/techscout.n/tools/cursor-ai-code-editor-pro-3\" class=\"ts-internal-link\" data-internal=\"true\" title=\"Cursor AI Code Editor Pro\" style=\"font-size: 1.05rem;\">stack decisions.</a></p>\r\n            \r\n                          <div class=\"word-callout word-callout-tip\">\r\n                <strong>💡 Pro Tip:</strong> Click the pink <strong>Templates</strong> button above to load complete production blueprints for Articles, Prompts, Custom GPTs, or AI Tools!\r\n              </div>\r\n            \r\n            <h2>2. Implementation Blueprint</h2>\r\n            <p>Detail your step-by-step code implementation and architectural pipeline.</p>\r\n            \r\n            <pre><code>// Example implementation snippet\r\nfunction executePipeline(Context $ctx): Result {\r\n    return $ctx-&gt;benchmark();\r\n}</code></pre>\r\n\r\n            <h2>3. Benchmarks &amp; Key Takeaways</h2>\r\n            <p>Summarize latency benchmarks, throughput metrics, and conclusions for fellow builders.</p>\r\n                  ', '', 'in_review', 0, NULL, NULL, NULL, 'public', 0, 0, 0, NULL, 70.00, 'more detailed', NULL, 1, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, NULL, NULL, '2026-09-02 23:21:30', '2026-09-02 23:24:37'),
(46, '34c48d47-a880-11f1-9f82-70b5e8b3215c', 'article', 1, NULL, 'Autonomous LLM Agent Architectures in 2026', 'autonomous-llm-agent-architectures-2026', 'A deep architectural dive into agentic coding workflows.', '<p>Autonomous agents represent the next tier of computing...</p>', NULL, 'draft', 0, NULL, NULL, NULL, 'public', 0, 0, 0, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 10015, 0, 25, 0, 0, 0, NULL, NULL, '2026-09-04 21:46:39', '2026-09-05 11:17:35'),
(47, 'dc641d6a2e4c63a00d28cfe92d9b7436', 'campaign', 1, NULL, 'refferal', 'refferal', 'asdfghjkl', 'asdfghjk', '/assets/uploads/general/general_1788596724_caf4111137b1.png', 'published', 0, NULL, NULL, NULL, 'public', 0, 0, 0, NULL, 98.00, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, '2026-09-05 13:26:36', NULL, '2026-09-05 13:26:36', '2026-09-05 14:39:54');

--
-- Structure for table `creator_referrals`
--
DROP TABLE IF EXISTS `creator_referrals`;
CREATE TABLE `creator_referrals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `referrer_author_id` int(10) unsigned NOT NULL,
  `referred_user_id` bigint(20) unsigned NOT NULL,
  `referred_author_id` int(10) unsigned DEFAULT NULL,
  `referral_code` varchar(50) NOT NULL,
  `commission_rate` decimal(5,2) NOT NULL DEFAULT 5.00,
  `expires_at` datetime NOT NULL,
  `total_commission_earned` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_cr_referrer` (`referrer_author_id`),
  KEY `idx_cr_referred` (`referred_author_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `creator_referrals` (2 rows)
--
INSERT INTO `creator_referrals` (`id`, `referrer_author_id`, `referred_user_id`, `referred_author_id`, `referral_code`, `commission_rate`, `expires_at`, `total_commission_earned`, `created_at`) VALUES 
(1, 1, 3, 2, 'ref_techscouteditorialstaff_spring', 10.00, '2027-03-04 06:22:56', 45.00, '2026-08-21 06:22:56'),
(2, 2, 4, NULL, 'ref_alexvance_community', 10.00, '2027-03-04 06:22:56', 15.00, '2026-08-21 06:22:56');

--
-- Structure for table `database_backups`
--
DROP TABLE IF EXISTS `database_backups`;
CREATE TABLE `database_backups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `filepath` varchar(500) NOT NULL,
  `file_size_bytes` bigint(20) unsigned NOT NULL,
  `table_count` int(10) unsigned NOT NULL,
  `total_rows` bigint(20) unsigned DEFAULT 0,
  `compression` enum('sql','gzip') DEFAULT 'sql',
  `checksum` varchar(64) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `database_backups` (1 rows)
--
INSERT INTO `database_backups` (`id`, `filename`, `filepath`, `file_size_bytes`, `table_count`, `total_rows`, `compression`, `checksum`, `created_by`, `created_at`) VALUES 
(1, 'techscout_backup_20260905_180549_4e1b75be.sql', 'C:\\xampp\\htdocs\\techscout.n/assets/backups/techscout_backup_20260905_180549_4e1b75be.sql', 547670, 77, 2409, 'sql', 'b7af01472ebc9921ba40920c8f73f434c68dbbce3807b34762b6fe48096274dc', 1, '2026-09-05 18:05:49');

--
-- Structure for table `editorial_badges`
--
DROP TABLE IF EXISTS `editorial_badges`;
CREATE TABLE `editorial_badges` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `color` varchar(20) NOT NULL DEFAULT '#38bdf8',
  `bg_color` varchar(40) NOT NULL DEFAULT 'rgba(56,189,248,0.15)',
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `editorial_badges` (6 rows)
--
INSERT INTO `editorial_badges` (`id`, `name`, `slug`, `color`, `bg_color`, `description`, `created_at`) VALUES 
(1, 'Editor\'s Choice', 'editors-choice', '#f59e0b', 'rgba(245,158,11,0.15)', 'Handpicked by the TechScout editorial board', '2026-09-02 21:55:08'),
(2, 'Deep Dive', 'deep-dive', '#38bdf8', 'rgba(56,189,248,0.15)', 'In-depth, exhaustive technical breakdown', '2026-09-02 21:55:08'),
(3, 'Breaking', 'breaking', '#ef4444', 'rgba(239,68,68,0.15)', 'Urgent, high-impact industry or security alert', '2026-09-02 21:55:08'),
(4, 'Staff Pick', 'staff-pick', '#ec4899', 'rgba(236,72,153,0.15)', 'Recommended reading from our core contributors', '2026-09-02 21:55:08'),
(5, 'Architecture Blueprint', 'architecture-blueprint', '#10b981', 'rgba(16,185,129,0.15)', 'Full-stack production reference design', '2026-09-02 21:55:08'),
(6, 'Security Audit', 'security-audit', '#a855f7', 'rgba(168,85,247,0.15)', 'Vulnerability analysis and mitigation guide', '2026-09-02 21:55:08');

--
-- Structure for table `entity_reports`
--
DROP TABLE IF EXISTS `entity_reports`;
CREATE TABLE `entity_reports` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reporter_user_id` bigint(20) unsigned DEFAULT NULL,
  `entity_type` enum('article','ai_tool','gpt_listing','prompt','job','comment','campaign','author') NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `entity_title` varchar(255) DEFAULT NULL,
  `entity_url` varchar(255) DEFAULT NULL,
  `reason` enum('spam','broken_or_misleading','plagiarism_copyright','inappropriate_harmful','malicious_prompt_injection','other') NOT NULL,
  `details` text DEFAULT NULL,
  `status` enum('pending','reviewed','actioned','dismissed') NOT NULL DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `action_taken` varchar(100) DEFAULT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_er_status` (`status`),
  KEY `idx_er_entity` (`entity_type`,`entity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `entity_reports` (2 rows)
--
INSERT INTO `entity_reports` (`id`, `reporter_user_id`, `entity_type`, `entity_id`, `entity_title`, `entity_url`, `reason`, `details`, `status`, `admin_notes`, `action_taken`, `reviewed_by`, `ip_address`, `created_at`, `updated_at`) VALUES 
(1, 2, 'article', 2, 'Zero-Downtime Kubernetes 1.31 Rolling Upgrades on Bare Metal', '/article/zero-downtime-kubernetes-upgrades-bare-metal', 'spam', 'Suspicious outbound affiliate link in footer section of article.', 'pending', NULL, NULL, NULL, '127.0.0.1', '2026-09-01 06:22:56', '2026-09-05 12:42:54'),
(2, 3, 'ai_tool', 8, 'Cursor AI — The AI-First Code Editor', '/tools/cursor-ai-code-editor', 'broken_or_misleading', 'The live demo endpoint URL returns 404 on their hosting provider.', 'pending', NULL, NULL, NULL, '127.0.0.1', '2026-09-01 06:22:56', '2026-09-01 06:22:56');

--
-- Structure for table `failed_login_attempts`
--
DROP TABLE IF EXISTS `failed_login_attempts`;
CREATE TABLE `failed_login_attempts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `identifier` varchar(191) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `attempted_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ip_attempt` (`ip_address`,`attempted_at`),
  KEY `idx_identifier` (`identifier`,`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure for table `feature_flags`
--
DROP TABLE IF EXISTS `feature_flags`;
CREATE TABLE `feature_flags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `flag_key` varchar(64) NOT NULL,
  `name` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'general',
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `flag_key` (`flag_key`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `feature_flags` (15 rows)
--
INSERT INTO `feature_flags` (`id`, `flag_key`, `name`, `description`, `category`, `is_enabled`, `updated_at`) VALUES 
(1, 'enable_ai_tools', 'AI Tools Directory', 'Public access to AI tools discovery and benchmarking hub', 'modules', 1, '2026-09-02 21:19:45'),
(2, 'enable_gpts', 'Custom GPTs Directory', 'Access to custom GPT directory & prompt listings', 'modules', 1, '2026-09-05 15:44:13'),
(3, 'enable_prompts', 'Engineering Prompt Library', 'Curated prompts for frontier LLMs and developer pipelines', 'modules', 1, '2026-09-05 15:44:13'),
(4, 'enable_jobs', 'Tech Jobs & Bounties Board', 'Developer hiring marketplace and engineering job postings', 'modules', 1, '2026-09-05 15:44:13'),
(5, 'enable_challenges', 'Creator Challenges & Hackathons', 'Developer bounties, hackathons and prize challenges', 'modules', 1, '2026-09-02 21:19:45'),
(6, 'enable_author_registration', 'Creator Applications', 'Allow members to submit creator / author applications', 'community', 1, '2026-09-05 15:44:13'),
(7, 'enable_comments', 'Interactive Discussions', 'Allow logged-in users to post comments on publications', 'community', 1, '2026-09-05 15:44:13'),
(8, 'enable_instant_search', 'Command+K Instant Search', 'Global fast search index overlay across all entities', 'ux', 1, '2026-09-02 21:19:45'),
(9, 'enable_social_share', 'Social Share Widget', 'Floating and inline social sharing buttons on articles', 'ux', 1, '2026-09-02 21:19:45'),
(10, 'enable_rss_feeds', 'RSS / XML Content Syndication', 'Syndication feeds at /feed.xml and category RSS', 'seo', 1, '2026-09-02 21:19:45'),
(11, 'enable_dark_mode_toggle', 'Light/Dark Theme Switcher', 'Allow readers to toggle between Light and Cyber Dark mode', 'ux', 1, '2026-09-02 21:19:45'),
(12, 'enable_live_notifications', 'In-App Notification Center', 'Real-time notification bell dropdown in the navigation bar', 'community', 1, '2026-09-02 21:19:45'),
(13, 'enable_webhooks', 'Outbound Webhook Dispatcher', 'Deliver HTTP POST event payloads to external URLs', 'integrations', 1, '2026-09-02 21:19:45'),
(14, 'enable_api_keys', 'Developer REST API Gateway', 'Allow API token generation for programmatic access', 'integrations', 1, '2026-09-02 21:19:45'),
(15, 'enable_security_firewall', 'Cyber Security WAF Shield', 'Active IP blocking, honeypots, and threat mitigation', 'security', 1, '2026-09-02 21:19:45');

--
-- Structure for table `financial_ledger`
--
DROP TABLE IF EXISTS `financial_ledger`;
CREATE TABLE `financial_ledger` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(64) NOT NULL,
  `author_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `entry_type` enum('credit','debit') NOT NULL,
  `category` varchar(64) NOT NULL,
  `amount_usd` decimal(12,2) NOT NULL,
  `credits` decimal(12,2) NOT NULL,
  `balance_before` decimal(12,2) NOT NULL DEFAULT 0.00,
  `balance_after` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payout_method_id` int(10) unsigned DEFAULT NULL,
  `payout_method_details` varchar(255) DEFAULT NULL,
  `status` enum('completed','pending','processing','reversed','failed') NOT NULL DEFAULT 'completed',
  `performed_by_user_id` bigint(20) unsigned DEFAULT NULL,
  `performed_by_role` varchar(50) NOT NULL DEFAULT 'system',
  `reason` text NOT NULL,
  `internal_notes` text DEFAULT NULL,
  `metadata_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata_json`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference_no` (`reference_no`),
  KEY `idx_fl_author` (`author_id`),
  KEY `idx_fl_type` (`entry_type`),
  KEY `idx_fl_category` (`category`),
  KEY `idx_fl_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `financial_ledger` (3 rows)
--
INSERT INTO `financial_ledger` (`id`, `reference_no`, `author_id`, `user_id`, `entry_type`, `category`, `amount_usd`, `credits`, `balance_before`, `balance_after`, `payout_method_id`, `payout_method_details`, `status`, `performed_by_user_id`, `performed_by_role`, `reason`, `internal_notes`, `metadata_json`, `created_at`) VALUES 
(1, 'TXN-2026-639ADC1C', 7, 7, 'credit', 'editorial_grant', 23.00, 23.00, 280.00, 303.00, NULL, NULL, 'completed', 1, 'admin', 'nothing', 'nothing', NULL, '2026-09-04 22:43:44'),
(2, 'TXN-2026-DD4C34CE', 1, 1, 'credit', 'pool_distribution', 3.50, 3.50, 1026.25, 1029.75, NULL, NULL, 'completed', 1, 'admin', 'Direct QA Pool Verification Test', NULL, NULL, '2026-09-04 23:08:52'),
(3, 'TXN-2026-29752853', 1, 1, 'debit', 'payout_withdrawal', 40.00, 40.00, 1029.75, 989.75, 1, 'PayPal: admin@techscout.online', 'pending', 1, 'author', 'Withdrawal request via PAYPAL (PayPal: admin@techscout.online) [Tax: $2, Bank Tax/Fee: $0]', '', '{\"platform_tax\":2,\"bank_tax\":0,\"bank_fee\":0,\"fee_deducted\":2,\"net_amount\":38,\"method_type\":\"paypal\"}', '2026-09-05 09:19:17');

--
-- Structure for table `firewall_logs`
--
DROP TABLE IF EXISTS `firewall_logs`;
CREATE TABLE `firewall_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `path` varchar(255) NOT NULL,
  `threat_type` varchar(80) NOT NULL,
  `action_taken` varchar(40) NOT NULL DEFAULT 'blocked',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_fl_ip` (`ip_address`),
  KEY `idx_fl_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure for table `firewall_rules`
--
DROP TABLE IF EXISTS `firewall_rules`;
CREATE TABLE `firewall_rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rule_type` enum('ip','cidr','user_agent','country') NOT NULL DEFAULT 'ip',
  `target_value` varchar(191) NOT NULL,
  `action` enum('block','allow','challenge') NOT NULL DEFAULT 'block',
  `reason` varchar(255) DEFAULT NULL,
  `hits_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_fw_target` (`target_value`),
  KEY `idx_fw_type` (`rule_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure for table `follows`
--
DROP TABLE IF EXISTS `follows`;
CREATE TABLE `follows` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `follower_user_id` bigint(20) unsigned NOT NULL,
  `following_author_id` bigint(20) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_follower_following` (`follower_user_id`,`following_author_id`),
  KEY `fk_follows_author` (`following_author_id`),
  CONSTRAINT `fk_follows_author` FOREIGN KEY (`following_author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_follows_follower` FOREIGN KEY (`follower_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure for table `gpt_listings`
--
DROP TABLE IF EXISTS `gpt_listings`;
CREATE TABLE `gpt_listings` (
  `content_id` bigint(20) unsigned NOT NULL,
  `openai_url` varchar(255) NOT NULL,
  `model_base` varchar(50) NOT NULL DEFAULT 'GPT-4o',
  `prompt_starters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`prompt_starters`)),
  `creator_handle` varchar(100) DEFAULT NULL,
  `use_case` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`content_id`),
  CONSTRAINT `fk_gpts_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gpt_listings` (7 rows)
--
INSERT INTO `gpt_listings` (`content_id`, `openai_url`, `model_base`, `prompt_starters`, `creator_handle`, `use_case`) VALUES 
(16, 'https://chatgpt.com/g/g-system-architect', 'GPT-4o', '[\"Review my microservice architecture diagram for 500k DAU.\", \"How should I partition my MySQL multi-tenant database?\", \"Design an asynchronous outbox queue with Redis and Kafka.\", \"Evaluate trade-offs between monolithic vs modular repo.\"]', '@alexvance', 'Software Architecture'),
(17, 'https://chatgpt.com/g/g-mysql-dba-copilot', 'GPT-4o', '[\"Analyze this slow query EXPLAIN ANALYZE output.\", \"What composite index should I create for status + published_at?\", \"How to optimize buffer pool size for a 64GB RAM MySQL node.\", \"Write an idempotency check for bulk inserts.\"]', '@marcus_db', 'Database Optimization'),
(18, 'https://chatgpt.com/g/g-k8s-sre-bot', 'GPT-4o', '[\"Diagnose CrashLoopBackOff on this deployment YAML.\", \"Generate an Ingress controller config with TLS termination.\", \"Configure PodDisruptionBudget for zero-downtime node drain.\", \"Optimize container CPU/Memory resource limits.\"]', '@sarah_dev', 'Cloud & DevOps'),
(19, 'https://chatgpt.com/g/g-typescript-master', 'Claude 3.5 Sonnet', '[\"Refactor this 300-line React component into clean hooks.\", \"Create a type-safe generic wrapper for fetch API.\", \"Eliminate all any types from this legacy TypeScript module.\", \"Write Vitest unit test cases for this state reducer.\"]', '@david_chen', 'Frontend Engineering'),
(20, 'https://chatgpt.com/g/g-appsec-sentinel', 'GPT-4o', '[\"Audit this PHP authentication endpoint for JWT flaws.\", \"Generate a strict Content Security Policy (CSP 3.0) header.\", \"Check this SQL query builder for potential injection bugs.\", \"Provide a remediation blueprint for Broken Object Level Auth.\"]', '@maya_sec', 'Cybersecurity'),
(21, 'https://chatgpt.com/g/g-nextjs-specialist', 'GPT-4o', '[\"How to eliminate waterfalls in Next.js 15 Server Components?\", \"Implement Server Actions with Zod schema validation.\", \"Configure On-Demand ISR revalidation tags in App Router.\", \"Optimize bundle size with dynamic imports and Turbopack.\"]', '@david_chen', 'Next.js & React'),
(22, 'https://chatgpt.com/g/g-autonomous-designer', 'Claude 3.5 Sonnet', '[\"Design a state machine graph for a coding agent.\", \"Write JSON schema tool definitions for a bash execution tool.\", \"How to implement reflection and self-correction loops?\", \"Configure vector memory retrieval for agent dialogue.\"]', '@elena_ai', 'Autonomous Agents');

--
-- Structure for table `homepage_sections`
--
DROP TABLE IF EXISTS `homepage_sections`;
CREATE TABLE `homepage_sections` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `section_key` varchar(60) NOT NULL,
  `title` varchar(150) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `item_limit` int(11) NOT NULL DEFAULT 6,
  `config_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`config_json`)),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `section_key` (`section_key`)
) ENGINE=InnoDB AUTO_INCREMENT=23911 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `homepage_sections` (15 rows)
--
INSERT INTO `homepage_sections` (`id`, `section_key`, `title`, `subtitle`, `is_enabled`, `sort_order`, `item_limit`, `config_json`, `updated_at`) VALUES 
(1, 'hero', 'Hero Frontier HUD', 'Primary above-the-fold headline, dynamic search, live stats counter & quick links', 1, 1, 1, NULL, '2026-09-04 21:17:02'),
(2, 'featured', 'Featured Spotlight Content', 'Handpicked editorial deep dives & architectural showcases', 1, 2, 4, NULL, '2026-09-04 21:17:02'),
(3, 'trending', 'Trending Radar', 'Real-time most viral articles based on telemetry, upvotes and reads', 1, 3, 6, NULL, '2026-09-04 21:17:02'),
(4, 'latest', 'Latest Scouted Publications', 'Reverse-chronological editorial stream with category filter badges', 1, 4, 8, NULL, '2026-09-04 21:17:02'),
(5, 'recommended', 'Recommended For You', 'Personalized content recommendations tailored to member topic subscriptions', 1, 5, 4, NULL, '2026-09-04 21:17:02'),
(6, 'authors', 'Top Curators & Staff Authors', 'Featured engineering creators, builders, and technical researchers', 1, 6, 6, NULL, '2026-09-04 21:17:02'),
(7, 'topics', 'Taxonomy & Knowledge Hubs', '100+ categories and curated domain tracks', 1, 7, 8, NULL, '2026-09-04 21:17:02'),
(8, 'tools', 'AI Tools & Software Radar', 'Curated developer tools, agent frameworks, and LLM APIs', 1, 8, 6, NULL, '2026-09-04 21:17:02'),
(9, 'ai_tools', 'Frontier AI Directory', 'Deep-dive machine learning model directory and benchmarks', 1, 9, 6, NULL, '2026-09-04 21:17:02'),
(10, 'prompts', 'Engineering Prompt Blueprints', 'Production-ready system prompts and variable injector templates', 1, 10, 6, NULL, '2026-09-04 21:17:02'),
(11, 'news', 'Breaking Intelligence & Scoops', 'Real-time updates, releases, and tech industry news wire', 1, 11, 5, NULL, '2026-09-04 21:17:02'),
(12, 'collections', 'Curated Resource Collections', 'Starter kits, architecture stacks, and reading tracks', 1, 12, 4, NULL, '2026-09-04 21:17:02'),
(13, 'campaigns', 'Developer Bounties & Hackathons', 'Active prize pools, engineering challenges, and competitions', 1, 13, 3, NULL, '2026-09-04 21:17:02'),
(14, 'newsletter', 'TechScout Dispatch Newsletter', 'Weekly curated dispatch delivered to 48,000+ developers', 1, 14, 1, NULL, '2026-09-04 21:17:02'),
(15, 'cta', 'Creator Studio Onboarding CTA', 'Call-to-action banner inviting builders to apply for creator verification', 1, 15, 1, NULL, '2026-09-04 21:17:02');

--
-- Structure for table `ip_lockouts`
--
DROP TABLE IF EXISTS `ip_lockouts`;
CREATE TABLE `ip_lockouts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `failed_count` int(10) unsigned DEFAULT 1,
  `locked_until` datetime NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip_address` (`ip_address`),
  KEY `idx_locked_until` (`locked_until`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure for table `job_applications`
--
DROP TABLE IF EXISTS `job_applications`;
CREATE TABLE `job_applications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `job_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `applicant_name` varchar(150) NOT NULL,
  `applicant_email` varchar(191) NOT NULL,
  `portfolio_url` varchar(255) DEFAULT NULL,
  `resume_text_or_url` text NOT NULL,
  `cover_letter` text DEFAULT NULL,
  `status` enum('pending','reviewed','interviewing','accepted','rejected') NOT NULL DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_job_app_job` (`job_id`),
  KEY `idx_job_app_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure for table `jobs`
--
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `company_name` varchar(150) NOT NULL,
  `company_logo_url` varchar(255) DEFAULT NULL,
  `location` varchar(100) NOT NULL DEFAULT 'Remote',
  `job_type` enum('Full-time','Contract','Remote','Part-time') NOT NULL DEFAULT 'Remote',
  `salary_range` varchar(100) DEFAULT NULL,
  `apply_url` varchar(255) NOT NULL,
  `description_html` longtext NOT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_jobs_active` (`is_active`),
  KEY `idx_jobs_featured` (`is_featured`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs` (3 rows)
--
INSERT INTO `jobs` (`id`, `title`, `slug`, `company_name`, `company_logo_url`, `location`, `job_type`, `salary_range`, `apply_url`, `description_html`, `is_featured`, `is_active`, `created_at`) VALUES 
(1, 'Senior AI Systems Engineer (Inference)', 'senior-ai-systems-engineer-inference', 'ScaleVector Labs', '/assets/images/logos/scalevector.png', 'Remote (Global)', 'Full-time', '$160,000 - $210,000 USD', 'https://careers.scalevector.io/apply/inf-eng', '<p>ScaleVector is looking for a Senior AI Systems Engineer to optimize distributed LLM inference clusters.</p>', 1, 1, '2026-09-02 06:22:55'),
(2, 'Prompt Operations & Red Teaming Lead', 'prompt-operations-red-teaming-lead', 'GuardPrompt AI', '/assets/images/logos/guardprompt.png', 'San Francisco, CA / Hybrid', 'Full-time', '$140,000 - $185,000 USD', 'https://guardprompt.ai/jobs/redteam-lead', '<p>Lead our automated jailbreak prevention and safety evaluation benchmark pipelines.</p>', 1, 1, '2026-09-03 06:22:55'),
(3, 'Principal Full-Stack Architect (PHP 8.3 & React)', 'principal-fullstack-architect-php-react', 'TechScout Guild', '/assets/images/logo.png', 'Remote', 'Full-time', '$150,000 - $190,000 USD', 'https://techscout.n/careers/architect', '<p>Help build the future of high-speed developer telemetry, creator monetization, and AI tool benchmarking.</p>', 1, 1, '2026-08-18 06:22:55');

--
-- Structure for table `mail_credentials`
--
DROP TABLE IF EXISTS `mail_credentials`;
CREATE TABLE `mail_credentials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `provider` enum('smtp','resend','sendgrid','ses','mailgun','pythonanywhere_api') NOT NULL DEFAULT 'smtp',
  `from_name` varchar(150) NOT NULL,
  `from_email` varchar(191) NOT NULL,
  `reply_to_email` varchar(191) DEFAULT NULL,
  `host` varchar(191) DEFAULT NULL,
  `port` int(11) DEFAULT 587,
  `encryption` enum('tls','ssl','none') DEFAULT 'tls',
  `username` varchar(191) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `api_key` varchar(255) DEFAULT NULL,
  `api_endpoint` varchar(255) DEFAULT NULL,
  `custom_headers_json` text DEFAULT NULL,
  `request_timeout` int(11) NOT NULL DEFAULT 15,
  `payload_format` varchar(50) NOT NULL DEFAULT 'standard_json',
  `webhook_url` varchar(255) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `status` enum('active','inactive','testing') DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mail_credentials` (3 rows)
--
INSERT INTO `mail_credentials` (`id`, `name`, `provider`, `from_name`, `from_email`, `reply_to_email`, `host`, `port`, `encryption`, `username`, `password`, `api_key`, `api_endpoint`, `custom_headers_json`, `request_timeout`, `payload_format`, `webhook_url`, `is_default`, `status`, `notes`, `created_at`, `updated_at`) VALUES 
(4, 'Gmail SMTP - Treasury Support', 'smtp', 'TechScout Treasury', 'treasury@techscout.online', 'treasury@techscout.online', 'smtp.gmail.com', 587, 'tls', 'techscout.treasury@gmail.com', NULL, NULL, NULL, NULL, 15, 'standard_json', NULL, 1, 'active', NULL, '2026-09-05 17:39:43', '2026-09-05 17:39:43'),
(5, 'Xpert Gmail', 'smtp', 'Xpert from TechScout', 'xpertprogrammerofficial@gmail.com', 'xpertprogrammerofficial@gmail.com', 'smtp.gmail.com', 587, 'tls', 'xpertprogrammerofficial@gmail.com', 'mhif hhtr bdef esku', NULL, NULL, '{\r\n  \"X-API-Key\": \"YOUR_SECRET_TOKEN\",\r\n  \"X-Client-ID\": \"techscout\"\r\n}', 15, 'standard_json', NULL, 0, 'active', 'gmail', '2026-09-05 17:48:42', '2026-09-05 17:48:42'),
(6, 'Gmail SMTP - Treasury Support', 'smtp', 'TechScout Treasury', 'treasury@techscout.online', 'treasury@techscout.online', 'smtp.gmail.com', 587, 'tls', 'techscout.treasury@gmail.com', NULL, NULL, NULL, NULL, 15, 'standard_json', NULL, 1, 'active', NULL, '2026-09-05 17:57:51', '2026-09-05 17:57:51');

--
-- Structure for table `mail_logs`
--
DROP TABLE IF EXISTS `mail_logs`;
CREATE TABLE `mail_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `to_email` varchar(191) NOT NULL,
  `to_name` varchar(150) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `template_key` varchar(80) DEFAULT NULL,
  `body_html` longtext DEFAULT NULL,
  `attachments_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments_json`)),
  `driver` varchar(30) NOT NULL DEFAULT 'log',
  `status` enum('sent','failed','queued') NOT NULL DEFAULT 'sent',
  `error_message` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ml_email` (`to_email`),
  KEY `idx_ml_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mail_logs` (8 rows)
--
INSERT INTO `mail_logs` (`id`, `to_email`, `to_name`, `subject`, `template_key`, `body_html`, `attachments_json`, `driver`, `status`, `error_message`, `created_at`) VALUES 
(1, 'author@example.com', 'Alex Author', 'Payment Sent! $750.00 USD has been transferred to your account 🎉', 'payout_approved', '<!DOCTYPE html>\n<html>\n<head>\n  <meta charset=\'utf-8\'>\n  <meta name=\'viewport\' content=\'width=device-width, initial-scale=1.0\'>\n  <title>Payment Sent! $750.00 USD has been transferred to your account 🎉</title>\n</head>\n<body style=\'margin:0;padding:0;background-color:#05070b;font-family:-apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,Helvetica,Arial,sans-serif;color:#f8fafc;\'>\n  <table width=\'100%\' border=\'0\' cellspacing=\'0\' cellpadding=\'0\' style=\'background-color:#05070b;padding:30px 10px;\'>\n    <tr>\n      <td align=\'center\'>\n        <table width=\'100%\' border=\'0\' cellspacing=\'0\' cellpadding=\'0\' style=\'max-width:600px;background-color:#0b0f17;border:1px solid #1e293b;border-radius:12px;overflow:hidden;\'>\n          <!-- Header Branding -->\n          <tr>\n            <td style=\'background:linear-gradient(135deg,rgba(99,102,241,0.15),rgba(56,189,248,0.15));padding:24px 30px;border-bottom:1px solid rgba(148,163,184,0.1);text-align:center;\'>\n              <a href=\'http://localhost/techscout.n\' style=\'text-decoration:none;\'>\n                <span style=\'font-size:22px;font-weight:900;letter-spacing:-0.5px;color:#f8fafc;\'>Tech<span style=\'color:#38bdf8;\'>Scout</span></span>\n              </a>\n              <div style=\'color:#94a3b8;font-size:12px;margin-top:4px;letter-spacing:1px;text-transform:uppercase;\'>Developer Intelligence Network</div>\n            </td>\n          </tr>\n          <!-- Body Content -->\n          <tr>\n            <td style=\'padding:32px 30px;color:#cbd5e1;font-size:15px;line-height:1.6;\'>\n              \n                <h2 style=\'color:#34d399;font-size:22px;margin:0 0 16px 0;\'>Creator Payout Transferred &amp; Disbursed! 💰</h2>\n                <p style=\'color:#cbd5e1;font-size:15px;line-height:1.6;margin:0 0 20px 0;\'>Hello <strong>Engineer</strong>, your withdrawal claim <strong>#1042</strong> has been approved by the treasury desk and funds have been dispatched!</p>\n                <div style=\'background:#0f172a;border:1px solid rgba(16,185,129,0.3);border-radius:8px;padding:18px;margin-bottom:24px;\'>\n                  <div style=\'display:grid;gap:8px;font-size:14px;color:#cbd5e1;\'>\n                    <div><strong style=\'color:#94a3b8;\'>Disbursed Amount:</strong> <strong style=\'font-size:18px;color:#34d399;\'>$750.00 USD</strong></div>\n                    <div><strong style=\'color:#94a3b8;\'>Transaction Reference:</strong> <code>TXN-ABC-999</code></div>\n                    <div><strong style=\'color:#94a3b8;\'>Destination:</strong> Verified Account</div>\n                    <div><strong style=\'color:#94a3b8;\'>Status:</strong> <span style=\'background:rgba(16,185,129,0.2);color:#34d399;padding:2px 8px;border-radius:4px;font-weight:700;\'>✅ COMPLETED &amp; DISBURSED</span></div>\n                  </div>\n                </div>\n                <div style=\'text-align:center;margin:28px 0;\'>\n                  <a href=\'http://localhost/techscout.n\' style=\'display:inline-block;background:#10b981;color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:6px;font-size:15px;\'>View Royalty Balance Ledger &rarr;</a>\n                </div>\n              \n            </td>\n          </tr>\n          <!-- Footer -->\n          <tr>\n            <td style=\'background-color:#070a0f;padding:24px 30px;border-top:1px solid rgba(148,163,184,0.1);text-align:center;\'>\n              <p style=\'color:#64748b;font-size:12px;margin:0 0 8px 0;\'>\n                &copy; 2026 TechScout Platform &bull; Built for builders, engineers, and creators.\n              </p>\n              <p style=\'color:#475569;font-size:11px;margin:0;\'>\n                You received this email because you are a registered member or subscriber of TechScout.\n              </p>\n            </td>\n          </tr>\n        </table>\n      </td>\n    </tr>\n  </table>\n</body>\n</html>', NULL, 'smtp', 'failed', 'MAIL FROM rejected: 530-5.7.0 Authentication Required. For more information, go to\r\n530 5.7.0  https://support.google.com/accounts/troubleshooter/2402620. 5b1f17b1804b1-49d00a5b19dsm65521815e9.2 - gsmtp', '2026-09-05 17:37:46'),
(2, 'author@example.com', 'Alex Author', 'Payment Sent! $750.00 USD has been transferred to your account 🎉', 'payout_approved', '<!DOCTYPE html>\n<html>\n<head>\n  <meta charset=\'utf-8\'>\n  <meta name=\'viewport\' content=\'width=device-width, initial-scale=1.0\'>\n  <title>Payment Sent! $750.00 USD has been transferred to your account 🎉</title>\n</head>\n<body style=\'margin:0;padding:0;background-color:#05070b;font-family:-apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,Helvetica,Arial,sans-serif;color:#f8fafc;\'>\n  <table width=\'100%\' border=\'0\' cellspacing=\'0\' cellpadding=\'0\' style=\'background-color:#05070b;padding:30px 10px;\'>\n    <tr>\n      <td align=\'center\'>\n        <table width=\'100%\' border=\'0\' cellspacing=\'0\' cellpadding=\'0\' style=\'max-width:600px;background-color:#0b0f17;border:1px solid #1e293b;border-radius:12px;overflow:hidden;\'>\n          <!-- Header Branding -->\n          <tr>\n            <td style=\'background:linear-gradient(135deg,rgba(99,102,241,0.15),rgba(56,189,248,0.15));padding:24px 30px;border-bottom:1px solid rgba(148,163,184,0.1);text-align:center;\'>\n              <a href=\'http://localhost/techscout.n\' style=\'text-decoration:none;\'>\n                <span style=\'font-size:22px;font-weight:900;letter-spacing:-0.5px;color:#f8fafc;\'>Tech<span style=\'color:#38bdf8;\'>Scout</span></span>\n              </a>\n              <div style=\'color:#94a3b8;font-size:12px;margin-top:4px;letter-spacing:1px;text-transform:uppercase;\'>Developer Intelligence Network</div>\n            </td>\n          </tr>\n          <!-- Body Content -->\n          <tr>\n            <td style=\'padding:32px 30px;color:#cbd5e1;font-size:15px;line-height:1.6;\'>\n              \n                <h2 style=\'color:#34d399;font-size:22px;margin:0 0 16px 0;\'>Creator Payout Transferred &amp; Disbursed! 💰</h2>\n                <p style=\'color:#cbd5e1;font-size:15px;line-height:1.6;margin:0 0 20px 0;\'>Hello <strong>Engineer</strong>, your withdrawal claim <strong>#1042</strong> has been approved by the treasury desk and funds have been dispatched!</p>\n                <div style=\'background:#0f172a;border:1px solid rgba(16,185,129,0.3);border-radius:8px;padding:18px;margin-bottom:24px;\'>\n                  <div style=\'display:grid;gap:8px;font-size:14px;color:#cbd5e1;\'>\n                    <div><strong style=\'color:#94a3b8;\'>Disbursed Amount:</strong> <strong style=\'font-size:18px;color:#34d399;\'>$750.00 USD</strong></div>\n                    <div><strong style=\'color:#94a3b8;\'>Transaction Reference:</strong> <code>TXN-ABC-999</code></div>\n                    <div><strong style=\'color:#94a3b8;\'>Destination:</strong> Verified Account</div>\n                    <div><strong style=\'color:#94a3b8;\'>Status:</strong> <span style=\'background:rgba(16,185,129,0.2);color:#34d399;padding:2px 8px;border-radius:4px;font-weight:700;\'>✅ COMPLETED &amp; DISBURSED</span></div>\n                  </div>\n                </div>\n                <div style=\'text-align:center;margin:28px 0;\'>\n                  <a href=\'http://localhost/techscout.n\' style=\'display:inline-block;background:#10b981;color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:6px;font-size:15px;\'>View Royalty Balance Ledger &rarr;</a>\n                </div>\n              \n            </td>\n          </tr>\n          <!-- Footer -->\n          <tr>\n            <td style=\'background-color:#070a0f;padding:24px 30px;border-top:1px solid rgba(148,163,184,0.1);text-align:center;\'>\n              <p style=\'color:#64748b;font-size:12px;margin:0 0 8px 0;\'>\n                &copy; 2026 TechScout Platform &bull; Built for builders, engineers, and creators.\n              </p>\n              <p style=\'color:#475569;font-size:11px;margin:0;\'>\n                You received this email because you are a registered member or subscriber of TechScout.\n              </p>\n            </td>\n          </tr>\n        </table>\n      </td>\n    </tr>\n  </table>\n</body>\n</html>', NULL, 'smtp', 'failed', 'MAIL FROM rejected: 530-5.7.0 Authentication Required. For more information, go to\r\n530 5.7.0  https://support.google.com/accounts/troubleshooter/2402620. 5b1f17b1804b1-49cf5135353sm145210775e9.2 - gsmtp', '2026-09-05 17:38:11'),
(3, 'author@example.com', 'Alex Author', 'Payment Sent! $750.00 USD has been transferred to your account 🎉', 'payout_approved', '<!DOCTYPE html>\n<html>\n<head>\n  <meta charset=\'utf-8\'>\n  <meta name=\'viewport\' content=\'width=device-width, initial-scale=1.0\'>\n  <title>Payment Sent! $750.00 USD has been transferred to your account 🎉</title>\n</head>\n<body style=\'margin:0;padding:0;background-color:#05070b;font-family:-apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,Helvetica,Arial,sans-serif;color:#f8fafc;\'>\n  <table width=\'100%\' border=\'0\' cellspacing=\'0\' cellpadding=\'0\' style=\'background-color:#05070b;padding:30px 10px;\'>\n    <tr>\n      <td align=\'center\'>\n        <table width=\'100%\' border=\'0\' cellspacing=\'0\' cellpadding=\'0\' style=\'max-width:600px;background-color:#0b0f17;border:1px solid #1e293b;border-radius:12px;overflow:hidden;\'>\n          <!-- Header Branding -->\n          <tr>\n            <td style=\'background:linear-gradient(135deg,rgba(99,102,241,0.15),rgba(56,189,248,0.15));padding:24px 30px;border-bottom:1px solid rgba(148,163,184,0.1);text-align:center;\'>\n              <a href=\'http://localhost/techscout.n\' style=\'text-decoration:none;\'>\n                <span style=\'font-size:22px;font-weight:900;letter-spacing:-0.5px;color:#f8fafc;\'>Tech<span style=\'color:#38bdf8;\'>Scout</span></span>\n              </a>\n              <div style=\'color:#94a3b8;font-size:12px;margin-top:4px;letter-spacing:1px;text-transform:uppercase;\'>Developer Intelligence Network</div>\n            </td>\n          </tr>\n          <!-- Body Content -->\n          <tr>\n            <td style=\'padding:32px 30px;color:#cbd5e1;font-size:15px;line-height:1.6;\'>\n              \n                <h2 style=\'color:#34d399;font-size:22px;margin:0 0 16px 0;\'>Creator Payout Transferred &amp; Disbursed! 💰</h2>\n                <p style=\'color:#cbd5e1;font-size:15px;line-height:1.6;margin:0 0 20px 0;\'>Hello <strong>Engineer</strong>, your withdrawal claim <strong>#1042</strong> has been approved by the treasury desk and funds have been dispatched!</p>\n                <div style=\'background:#0f172a;border:1px solid rgba(16,185,129,0.3);border-radius:8px;padding:18px;margin-bottom:24px;\'>\n                  <div style=\'display:grid;gap:8px;font-size:14px;color:#cbd5e1;\'>\n                    <div><strong style=\'color:#94a3b8;\'>Disbursed Amount:</strong> <strong style=\'font-size:18px;color:#34d399;\'>$750.00 USD</strong></div>\n                    <div><strong style=\'color:#94a3b8;\'>Transaction Reference:</strong> <code>TXN-ABC-999</code></div>\n                    <div><strong style=\'color:#94a3b8;\'>Destination:</strong> Verified Account</div>\n                    <div><strong style=\'color:#94a3b8;\'>Status:</strong> <span style=\'background:rgba(16,185,129,0.2);color:#34d399;padding:2px 8px;border-radius:4px;font-weight:700;\'>✅ COMPLETED &amp; DISBURSED</span></div>\n                  </div>\n                </div>\n                <div style=\'text-align:center;margin:28px 0;\'>\n                  <a href=\'http://localhost/techscout.n\' style=\'display:inline-block;background:#10b981;color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:6px;font-size:15px;\'>View Royalty Balance Ledger &rarr;</a>\n                </div>\n              \n            </td>\n          </tr>\n          <!-- Footer -->\n          <tr>\n            <td style=\'background-color:#070a0f;padding:24px 30px;border-top:1px solid rgba(148,163,184,0.1);text-align:center;\'>\n              <p style=\'color:#64748b;font-size:12px;margin:0 0 8px 0;\'>\n                &copy; 2026 TechScout Platform &bull; Built for builders, engineers, and creators.\n              </p>\n              <p style=\'color:#475569;font-size:11px;margin:0;\'>\n                You received this email because you are a registered member or subscriber of TechScout.\n              </p>\n            </td>\n          </tr>\n        </table>\n      </td>\n    </tr>\n  </table>\n</body>\n</html>', NULL, 'smtp', 'failed', 'MAIL FROM rejected: 530-5.7.0 Authentication Required. For more information, go to\r\n530 5.7.0  https://support.google.com/accounts/troubleshooter/2402620. 5b1f17b1804b1-49cfd3f8192sm107784665e9.3 - gsmtp', '2026-09-05 17:39:44'),
(4, 'admin@techscout.online', 'superadmin', 'Congratulations! You unlocked the {{milestone_title}} Milestone 🌟', 'author_milestone_badge', '<!DOCTYPE html>\n<html>\n<head>\n  <meta charset=\'utf-8\'>\n  <meta name=\'viewport\' content=\'width=device-width, initial-scale=1.0\'>\n  <title>Congratulations! You unlocked the 10,000 Verified Readers Milestone 🌟</title>\n</head>\n<body style=\'margin:0;padding:0;background-color:#05070b;font-family:-apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,Helvetica,Arial,sans-serif;color:#f8fafc;\'>\n  <table width=\'100%\' border=\'0\' cellspacing=\'0\' cellpadding=\'0\' style=\'background-color:#05070b;padding:30px 10px;\'>\n    <tr>\n      <td align=\'center\'>\n        <table width=\'100%\' border=\'0\' cellspacing=\'0\' cellpadding=\'0\' style=\'max-width:600px;background-color:#0b0f17;border:1px solid #1e293b;border-radius:12px;overflow:hidden;\'>\n          <!-- Header Branding -->\n          <tr>\n            <td style=\'background:linear-gradient(135deg,rgba(99,102,241,0.15),rgba(56,189,248,0.15));padding:24px 30px;border-bottom:1px solid rgba(148,163,184,0.1);text-align:center;\'>\n              <a href=\'http://localhost/techscout.n\' style=\'text-decoration:none;\'>\n                <span style=\'font-size:22px;font-weight:900;letter-spacing:-0.5px;color:#f8fafc;\'>Tech<span style=\'color:#38bdf8;\'>Scout</span></span>\n              </a>\n              <div style=\'color:#94a3b8;font-size:12px;margin-top:4px;letter-spacing:1px;text-transform:uppercase;\'>Developer Intelligence Network</div>\n            </td>\n          </tr>\n          <!-- Body Content -->\n          <tr>\n            <td style=\'padding:32px 30px;color:#cbd5e1;font-size:15px;line-height:1.6;\'>\n              \n                <h2 style=\'color:#f59e0b;font-size:22px;margin:0 0 16px 0;\'>Creator Milestone Unlocked! 🎖️</h2>\n                <p style=\'color:#cbd5e1;font-size:15px;line-height:1.6;margin:0 0 20px 0;\'>Hello <strong>superadmin</strong>,</p>\n                <p style=\'color:#cbd5e1;font-size:15px;line-height:1.6;margin:0 0 20px 0;\'>Congratulations! Your contributions have achieved the <strong>&ldquo;10,000 Verified Readers&rdquo;</strong> honor on TechScout.</p>\n                <div style=\'text-align:center;background:#0f172a;border:1px solid rgba(245,158,11,0.3);border-radius:8px;padding:20px;margin:20px 0;\'>\n                  <div style=\'font-size:36px;margin-bottom:6px;\'>🌟</div>\n                  <div style=\'font-size:18px;font-weight:800;color:#fcd34d;\'>10,000 Verified Readers</div>\n                  <div style=\'font-size:13px;color:#94a3b8;margin-top:6px;\'>Your author profile badge has been updated to reflect this milestone.</div>\n                </div>\n                <div style=\'text-align:center;margin:28px 0;\'>\n                  <a href=\'http://localhost/techscout.n/blog\' style=\'display:inline-block;background:linear-gradient(135deg,#f59e0b,#38bdf8);color:#020617;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:6px;font-size:15px;\'>View Profile &amp; Badges &rarr;</a>\n                </div>\n              \n            </td>\n          </tr>\n          <!-- Footer -->\n          <tr>\n            <td style=\'background-color:#070a0f;padding:24px 30px;border-top:1px solid rgba(148,163,184,0.1);text-align:center;\'>\n              <p style=\'color:#64748b;font-size:12px;margin:0 0 8px 0;\'>\n                &copy; 2026 TechScout Platform &bull; Built for builders, engineers, and creators.\n              </p>\n              <p style=\'color:#475569;font-size:11px;margin:0;\'>\n                You received this email because you are a registered member or subscriber of TechScout.\n              </p>\n            </td>\n          </tr>\n        </table>\n      </td>\n    </tr>\n  </table>\n</body>\n</html>', '{\"links\":[],\"files\":[]}', 'smtp', 'failed', 'MAIL FROM rejected: 530-5.7.0 Authentication Required. For more information, go to\r\n530 5.7.0  https://support.google.com/accounts/troubleshooter/2402620. ffacd0b85a97d-48588394fa1sm13476937f8f.8 - gsmtp', '2026-09-05 17:49:58'),
(5, 'author@example.com', 'Alex Author', 'Payment Sent! $750.00 USD has been transferred to your account 🎉', 'payout_approved', '<!DOCTYPE html>\n<html>\n<head>\n  <meta charset=\'utf-8\'>\n  <meta name=\'viewport\' content=\'width=device-width, initial-scale=1.0\'>\n  <title>Payment Sent! $750.00 USD has been transferred to your account 🎉</title>\n</head>\n<body style=\'margin:0;padding:0;background-color:#05070b;font-family:-apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,Helvetica,Arial,sans-serif;color:#f8fafc;\'>\n  <table width=\'100%\' border=\'0\' cellspacing=\'0\' cellpadding=\'0\' style=\'background-color:#05070b;padding:30px 10px;\'>\n    <tr>\n      <td align=\'center\'>\n        <table width=\'100%\' border=\'0\' cellspacing=\'0\' cellpadding=\'0\' style=\'max-width:600px;background-color:#0b0f17;border:1px solid #1e293b;border-radius:12px;overflow:hidden;\'>\n          <!-- Header Branding -->\n          <tr>\n            <td style=\'background:linear-gradient(135deg,rgba(99,102,241,0.15),rgba(56,189,248,0.15));padding:24px 30px;border-bottom:1px solid rgba(148,163,184,0.1);text-align:center;\'>\n              <a href=\'http://localhost/techscout.n\' style=\'text-decoration:none;\'>\n                <span style=\'font-size:22px;font-weight:900;letter-spacing:-0.5px;color:#f8fafc;\'>Tech<span style=\'color:#38bdf8;\'>Scout</span></span>\n              </a>\n              <div style=\'color:#94a3b8;font-size:12px;margin-top:4px;letter-spacing:1px;text-transform:uppercase;\'>Developer Intelligence Network</div>\n            </td>\n          </tr>\n          <!-- Body Content -->\n          <tr>\n            <td style=\'padding:32px 30px;color:#cbd5e1;font-size:15px;line-height:1.6;\'>\n              \n                <h2 style=\'color:#34d399;font-size:22px;margin:0 0 16px 0;\'>Creator Payout Transferred &amp; Disbursed! 💰</h2>\n                <p style=\'color:#cbd5e1;font-size:15px;line-height:1.6;margin:0 0 20px 0;\'>Hello <strong>Engineer</strong>, your withdrawal claim <strong>#1042</strong> has been approved by the treasury desk and funds have been dispatched!</p>\n                <div style=\'background:#0f172a;border:1px solid rgba(16,185,129,0.3);border-radius:8px;padding:18px;margin-bottom:24px;\'>\n                  <div style=\'display:grid;gap:8px;font-size:14px;color:#cbd5e1;\'>\n                    <div><strong style=\'color:#94a3b8;\'>Disbursed Amount:</strong> <strong style=\'font-size:18px;color:#34d399;\'>$750.00 USD</strong></div>\n                    <div><strong style=\'color:#94a3b8;\'>Transaction Reference:</strong> <code>TXN-ABC-999</code></div>\n                    <div><strong style=\'color:#94a3b8;\'>Destination:</strong> Verified Account</div>\n                    <div><strong style=\'color:#94a3b8;\'>Status:</strong> <span style=\'background:rgba(16,185,129,0.2);color:#34d399;padding:2px 8px;border-radius:4px;font-weight:700;\'>✅ COMPLETED &amp; DISBURSED</span></div>\n                  </div>\n                </div>\n                <div style=\'text-align:center;margin:28px 0;\'>\n                  <a href=\'http://localhost/techscout.n\' style=\'display:inline-block;background:#10b981;color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:6px;font-size:15px;\'>View Royalty Balance Ledger &rarr;</a>\n                </div>\n              \n            </td>\n          </tr>\n          <!-- Footer -->\n          <tr>\n            <td style=\'background-color:#070a0f;padding:24px 30px;border-top:1px solid rgba(148,163,184,0.1);text-align:center;\'>\n              <p style=\'color:#64748b;font-size:12px;margin:0 0 8px 0;\'>\n                &copy; 2026 TechScout Platform &bull; Built for builders, engineers, and creators.\n              </p>\n              <p style=\'color:#475569;font-size:11px;margin:0;\'>\n                You received this email because you are a registered member or subscriber of TechScout.\n              </p>\n            </td>\n          </tr>\n        </table>\n      </td>\n    </tr>\n  </table>\n</body>\n</html>', NULL, 'smtp', 'failed', 'MAIL FROM rejected: 530-5.7.0 Authentication Required. For more information, go to\r\n530 5.7.0  https://support.google.com/accounts/troubleshooter/2402620. ffacd0b85a97d-4858ab73c2bsm12107903f8f.22 - gsmtp', '2026-09-05 17:57:52'),
(6, 'ayesha.aslam.pharmacist@gmail.com', 'Admin Tester', '⚡ TechScout Mail Engine Delivery Verification', 'test_verification', '<!DOCTYPE html>\n<html>\n<head>\n  <meta charset=\'utf-8\'>\n  <meta name=\'viewport\' content=\'width=device-width, initial-scale=1.0\'>\n  <title>Welcome to TechScout - AI &amp; Software Engineering Intelligence</title>\n</head>\n<body style=\'margin:0;padding:0;background-color:#05070b;font-family:-apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,Helvetica,Arial,sans-serif;color:#f8fafc;\'>\n  <table width=\'100%\' border=\'0\' cellspacing=\'0\' cellpadding=\'0\' style=\'background-color:#05070b;padding:30px 10px;\'>\n    <tr>\n      <td align=\'center\'>\n        <table width=\'100%\' border=\'0\' cellspacing=\'0\' cellpadding=\'0\' style=\'max-width:600px;background-color:#0b0f17;border:1px solid #1e293b;border-radius:12px;overflow:hidden;\'>\n          <!-- Header Branding -->\n          <tr>\n            <td style=\'background:linear-gradient(135deg,rgba(99,102,241,0.15),rgba(56,189,248,0.15));padding:24px 30px;border-bottom:1px solid rgba(148,163,184,0.1);text-align:center;\'>\n              <a href=\'http://localhost/techscout.n\' style=\'text-decoration:none;\'>\n                <span style=\'font-size:22px;font-weight:900;letter-spacing:-0.5px;color:#f8fafc;\'>Tech<span style=\'color:#38bdf8;\'>Scout</span></span>\n              </a>\n              <div style=\'color:#94a3b8;font-size:12px;margin-top:4px;letter-spacing:1px;text-transform:uppercase;\'>Developer Intelligence Network</div>\n            </td>\n          </tr>\n          <!-- Body Content -->\n          <tr>\n            <td style=\'padding:32px 30px;color:#cbd5e1;font-size:15px;line-height:1.6;\'>\n              \n                <h2 style=\'color:#f8fafc;font-size:22px;margin:0 0 16px 0;\'>Welcome aboard TechScout, TechScout Admin Test! 🚀</h2>\n                <p style=\'color:#cbd5e1;font-size:15px;line-height:1.6;margin:0 0 20px 0;\'>You have joined the frontier community for technical journalism, AI tool indexing, custom GPT exploration, and creator monetization.</p>\n                <div style=\'background:#0f172a;border:1px solid rgba(99,102,241,0.3);border-radius:8px;padding:16px;margin-bottom:24px;\'>\n                  <div style=\'color:#38bdf8;font-weight:700;font-size:14px;margin-bottom:6px;\'>⚡ Quickstart actions:</div>\n                  <ul style=\'color:#94a3b8;font-size:14px;line-height:1.6;margin:0;padding-left:20px;\'>\n                    <li>Explore curated technical breakdowns &amp; blueprints</li>\n                    <li>Subscribe to your favorite technology topics</li>\n                    <li>Publish technical guides and earn creator reputation</li>\n                  </ul>\n                </div>\n                <div style=\'text-align:center;margin:28px 0;\'>\n                  <a href=\'http://localhost/techscout.n/\' style=\'display:inline-block;background:linear-gradient(135deg,#6366f1,#38bdf8);color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:6px;font-size:15px;\'>Explore TechScout Intelligence &rarr;</a>\n                </div>\n              \n            </td>\n          </tr>\n          <!-- Footer -->\n          <tr>\n            <td style=\'background-color:#070a0f;padding:24px 30px;border-top:1px solid rgba(148,163,184,0.1);text-align:center;\'>\n              <p style=\'color:#64748b;font-size:12px;margin:0 0 8px 0;\'>\n                &copy; 2026 TechScout Platform &bull; Built for builders, engineers, and creators.\n              </p>\n              <p style=\'color:#475569;font-size:11px;margin:0;\'>\n                You received this email because you are a registered member or subscriber of TechScout.\n              </p>\n            </td>\n          </tr>\n        </table>\n      </td>\n    </tr>\n  </table>\n</body>\n</html>', NULL, 'smtp', 'sent', NULL, '2026-09-05 17:59:36'),
(7, 'ayesha.aslam.pharmacist@gmail.com', 'Admin Tester', '⚡ TechScout Mail Engine Delivery Verification', 'test_verification', '<!DOCTYPE html>\n<html>\n<head>\n  <meta charset=\'utf-8\'>\n  <meta name=\'viewport\' content=\'width=device-width, initial-scale=1.0\'>\n  <title>Welcome to TechScout - AI &amp; Software Engineering Intelligence</title>\n</head>\n<body style=\'margin:0;padding:0;background-color:#05070b;font-family:-apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,Helvetica,Arial,sans-serif;color:#f8fafc;\'>\n  <table width=\'100%\' border=\'0\' cellspacing=\'0\' cellpadding=\'0\' style=\'background-color:#05070b;padding:30px 10px;\'>\n    <tr>\n      <td align=\'center\'>\n        <table width=\'100%\' border=\'0\' cellspacing=\'0\' cellpadding=\'0\' style=\'max-width:600px;background-color:#0b0f17;border:1px solid #1e293b;border-radius:12px;overflow:hidden;\'>\n          <!-- Header Branding -->\n          <tr>\n            <td style=\'background:linear-gradient(135deg,rgba(99,102,241,0.15),rgba(56,189,248,0.15));padding:24px 30px;border-bottom:1px solid rgba(148,163,184,0.1);text-align:center;\'>\n              <a href=\'http://localhost/techscout.n\' style=\'text-decoration:none;\'>\n                <span style=\'font-size:22px;font-weight:900;letter-spacing:-0.5px;color:#f8fafc;\'>Tech<span style=\'color:#38bdf8;\'>Scout</span></span>\n              </a>\n              <div style=\'color:#94a3b8;font-size:12px;margin-top:4px;letter-spacing:1px;text-transform:uppercase;\'>Developer Intelligence Network</div>\n            </td>\n          </tr>\n          <!-- Body Content -->\n          <tr>\n            <td style=\'padding:32px 30px;color:#cbd5e1;font-size:15px;line-height:1.6;\'>\n              \n                <h2 style=\'color:#f8fafc;font-size:22px;margin:0 0 16px 0;\'>Welcome aboard TechScout, TechScout Admin Test! 🚀</h2>\n                <p style=\'color:#cbd5e1;font-size:15px;line-height:1.6;margin:0 0 20px 0;\'>You have joined the frontier community for technical journalism, AI tool indexing, custom GPT exploration, and creator monetization.</p>\n                <div style=\'background:#0f172a;border:1px solid rgba(99,102,241,0.3);border-radius:8px;padding:16px;margin-bottom:24px;\'>\n                  <div style=\'color:#38bdf8;font-weight:700;font-size:14px;margin-bottom:6px;\'>⚡ Quickstart actions:</div>\n                  <ul style=\'color:#94a3b8;font-size:14px;line-height:1.6;margin:0;padding-left:20px;\'>\n                    <li>Explore curated technical breakdowns &amp; blueprints</li>\n                    <li>Subscribe to your favorite technology topics</li>\n                    <li>Publish technical guides and earn creator reputation</li>\n                  </ul>\n                </div>\n                <div style=\'text-align:center;margin:28px 0;\'>\n                  <a href=\'http://localhost/techscout.n/\' style=\'display:inline-block;background:linear-gradient(135deg,#6366f1,#38bdf8);color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:6px;font-size:15px;\'>Explore TechScout Intelligence &rarr;</a>\n                </div>\n              \n            </td>\n          </tr>\n          <!-- Footer -->\n          <tr>\n            <td style=\'background-color:#070a0f;padding:24px 30px;border-top:1px solid rgba(148,163,184,0.1);text-align:center;\'>\n              <p style=\'color:#64748b;font-size:12px;margin:0 0 8px 0;\'>\n                &copy; 2026 TechScout Platform &bull; Built for builders, engineers, and creators.\n              </p>\n              <p style=\'color:#475569;font-size:11px;margin:0;\'>\n                You received this email because you are a registered member or subscriber of TechScout.\n              </p>\n            </td>\n          </tr>\n        </table>\n      </td>\n    </tr>\n  </table>\n</body>\n</html>', NULL, 'smtp', 'sent', NULL, '2026-09-05 18:00:46'),
(8, 'ayesha.aslam.pharmacist@gmail.com', 'Admin Tester', '⚡ TechScout Mail Engine Delivery Verification', 'test_verification', '<!DOCTYPE html>\n<html>\n<head>\n  <meta charset=\'utf-8\'>\n  <meta name=\'viewport\' content=\'width=device-width, initial-scale=1.0\'>\n  <title>Welcome to TechScout - AI &amp; Software Engineering Intelligence</title>\n</head>\n<body style=\'margin:0;padding:0;background-color:#05070b;font-family:-apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,Helvetica,Arial,sans-serif;color:#f8fafc;\'>\n  <table width=\'100%\' border=\'0\' cellspacing=\'0\' cellpadding=\'0\' style=\'background-color:#05070b;padding:30px 10px;\'>\n    <tr>\n      <td align=\'center\'>\n        <table width=\'100%\' border=\'0\' cellspacing=\'0\' cellpadding=\'0\' style=\'max-width:600px;background-color:#0b0f17;border:1px solid #1e293b;border-radius:12px;overflow:hidden;\'>\n          <!-- Header Branding -->\n          <tr>\n            <td style=\'background:linear-gradient(135deg,rgba(99,102,241,0.15),rgba(56,189,248,0.15));padding:24px 30px;border-bottom:1px solid rgba(148,163,184,0.1);text-align:center;\'>\n              <a href=\'http://localhost/techscout.n\' style=\'text-decoration:none;\'>\n                <span style=\'font-size:22px;font-weight:900;letter-spacing:-0.5px;color:#f8fafc;\'>Tech<span style=\'color:#38bdf8;\'>Scout</span></span>\n              </a>\n              <div style=\'color:#94a3b8;font-size:12px;margin-top:4px;letter-spacing:1px;text-transform:uppercase;\'>Developer Intelligence Network</div>\n            </td>\n          </tr>\n          <!-- Body Content -->\n          <tr>\n            <td style=\'padding:32px 30px;color:#cbd5e1;font-size:15px;line-height:1.6;\'>\n              \n                <h2 style=\'color:#f8fafc;font-size:22px;margin:0 0 16px 0;\'>Welcome aboard TechScout, TechScout Admin Test! 🚀</h2>\n                <p style=\'color:#cbd5e1;font-size:15px;line-height:1.6;margin:0 0 20px 0;\'>You have joined the frontier community for technical journalism, AI tool indexing, custom GPT exploration, and creator monetization.</p>\n                <div style=\'background:#0f172a;border:1px solid rgba(99,102,241,0.3);border-radius:8px;padding:16px;margin-bottom:24px;\'>\n                  <div style=\'color:#38bdf8;font-weight:700;font-size:14px;margin-bottom:6px;\'>⚡ Quickstart actions:</div>\n                  <ul style=\'color:#94a3b8;font-size:14px;line-height:1.6;margin:0;padding-left:20px;\'>\n                    <li>Explore curated technical breakdowns &amp; blueprints</li>\n                    <li>Subscribe to your favorite technology topics</li>\n                    <li>Publish technical guides and earn creator reputation</li>\n                  </ul>\n                </div>\n                <div style=\'text-align:center;margin:28px 0;\'>\n                  <a href=\'http://localhost/techscout.n/\' style=\'display:inline-block;background:linear-gradient(135deg,#6366f1,#38bdf8);color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:6px;font-size:15px;\'>Explore TechScout Intelligence &rarr;</a>\n                </div>\n              \n            </td>\n          </tr>\n          <!-- Footer -->\n          <tr>\n            <td style=\'background-color:#070a0f;padding:24px 30px;border-top:1px solid rgba(148,163,184,0.1);text-align:center;\'>\n              <p style=\'color:#64748b;font-size:12px;margin:0 0 8px 0;\'>\n                &copy; 2026 TechScout Platform &bull; Built for builders, engineers, and creators.\n              </p>\n              <p style=\'color:#475569;font-size:11px;margin:0;\'>\n                You received this email because you are a registered member or subscriber of TechScout.\n              </p>\n            </td>\n          </tr>\n        </table>\n      </td>\n    </tr>\n  </table>\n</body>\n</html>', NULL, 'smtp', 'sent', NULL, '2026-09-05 18:01:06');

--
-- Structure for table `mail_templates`
--
DROP TABLE IF EXISTS `mail_templates`;
CREATE TABLE `mail_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `template_key` varchar(80) NOT NULL,
  `name` varchar(150) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `html_content` longtext NOT NULL,
  `assigned_credential_id` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `template_key` (`template_key`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mail_templates` (3 rows)
--
INSERT INTO `mail_templates` (`id`, `template_key`, `name`, `subject`, `description`, `html_content`, `assigned_credential_id`, `updated_at`) VALUES 
(1, 'payout_approved', '💰 Creator Payout Processed & Transferred', 'Payment Sent! ${{amount}} USD has been transferred to your account 🎉', 'Sent to creators when their payout request is approved and funds are disbursed.', '\n                <h2 style=\'color:#34d399;font-size:22px;margin:0 0 16px 0;\'>Creator Payout Transferred &amp; Disbursed! 💰</h2>\n                <p style=\'color:#cbd5e1;font-size:15px;line-height:1.6;margin:0 0 20px 0;\'>Hello <strong>{{name}}</strong>, your withdrawal claim <strong>#{{payout_id}}</strong> has been approved by the treasury desk and funds have been dispatched!</p>\n                <div style=\'background:#0f172a;border:1px solid rgba(16,185,129,0.3);border-radius:8px;padding:18px;margin-bottom:24px;\'>\n                  <div style=\'display:grid;gap:8px;font-size:14px;color:#cbd5e1;\'>\n                    <div><strong style=\'color:#94a3b8;\'>Disbursed Amount:</strong> <strong style=\'font-size:18px;color:#34d399;\'>${{net_amount}} USD</strong></div>\n                    <div><strong style=\'color:#94a3b8;\'>Transaction Reference:</strong> <code>{{transaction_ref}}</code></div>\n                    <div><strong style=\'color:#94a3b8;\'>Destination:</strong> {{destination}}</div>\n                    <div><strong style=\'color:#94a3b8;\'>Status:</strong> <span style=\'background:rgba(16,185,129,0.2);color:#34d399;padding:2px 8px;border-radius:4px;font-weight:700;\'>✅ COMPLETED &amp; DISBURSED</span></div>\n                  </div>\n                </div>\n                <div style=\'text-align:center;margin:28px 0;\'>\n                  <a href=\'{{action_url}}\' style=\'display:inline-block;background:#10b981;color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:6px;font-size:15px;\'>View Royalty Balance Ledger &rarr;</a>\n                </div>', 4, '2026-09-05 17:57:51'),
(2, 'welcome', '🚀 Welcome to TechScout Network', 'Welcome to TechScout - AI & Software Engineering Intelligence', 'Sent to new users upon account registration and onboarding.', '\n                <h2 style=\'color:#f8fafc;font-size:22px;margin:0 0 16px 0;\'>Welcome aboard TechScout, {{name}}! 🚀</h2>\n                <p style=\'color:#cbd5e1;font-size:15px;line-height:1.6;margin:0 0 20px 0;\'>You have joined the frontier community for technical journalism, AI tool indexing, custom GPT exploration, and creator monetization.</p>\n                <div style=\'background:#0f172a;border:1px solid rgba(99,102,241,0.3);border-radius:8px;padding:16px;margin-bottom:24px;\'>\n                  <div style=\'color:#38bdf8;font-weight:700;font-size:14px;margin-bottom:6px;\'>⚡ Quickstart actions:</div>\n                  <ul style=\'color:#94a3b8;font-size:14px;line-height:1.6;margin:0;padding-left:20px;\'>\n                    <li>Explore curated technical breakdowns &amp; blueprints</li>\n                    <li>Subscribe to your favorite technology topics</li>\n                    <li>Publish technical guides and earn creator reputation</li>\n                  </ul>\n                </div>\n                <div style=\'text-align:center;margin:28px 0;\'>\n                  <a href=\'{{action_url}}\' style=\'display:inline-block;background:linear-gradient(135deg,#6366f1,#38bdf8);color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:6px;font-size:15px;\'>Explore TechScout Intelligence &rarr;</a>\n                </div>', 5, '2026-09-05 18:00:33'),
(9, 'account_activated', '✅ Account Verified & Activated', 'Your TechScout Account is Ready & Verified', 'Sent upon successful email confirmation and account activation.', '\n                <h2 style=\'color:#34d399;font-size:22px;margin:0 0 16px 0;\'>Account Successfully Verified! ✅</h2>\n                <p style=\'color:#cbd5e1;font-size:15px;line-height:1.6;margin:0 0 20px 0;\'>Hello <strong>{{name}}</strong>, your email address has been verified and your TechScout account is fully active.</p>\n                <div style=\'background:#0f172a;border:1px solid rgba(16,185,129,0.3);border-radius:8px;padding:16px;margin-bottom:24px;\'>\n                  <div style=\'color:#34d399;font-weight:700;font-size:14px;margin-bottom:4px;\'>✓ Member Permissions Unlocked</div>\n                  <div style=\'color:#94a3b8;font-size:13px;\'>You can now join architectural discussions, save custom prompt blueprints, and explore the AI tool radar.</div>\n                </div>\n                <div style=\'text-align:center;margin:28px 0;\'>\n                  <a href=\'{{action_url}}\' style=\'display:inline-block;background:linear-gradient(135deg,#10b981,#38bdf8);color:#ffffff;font-weight:700;text-decoration:none;padding:12px 28px;border-radius:6px;font-size:15px;\'>Go to Your Dashboard &rarr;</a>\n                </div>', 5, '2026-09-05 17:57:36');

--
-- Structure for table `media_files`
--
DROP TABLE IF EXISTS `media_files`;
CREATE TABLE `media_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT 1,
  `filename` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` enum('image','video','audio','document') NOT NULL DEFAULT 'image',
  `file_size` bigint(20) unsigned NOT NULL DEFAULT 0,
  `mime_type` varchar(100) NOT NULL DEFAULT 'image/jpeg',
  `folder` varchar(100) NOT NULL DEFAULT 'general',
  `alt_text` varchar(255) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `copyright_info` varchar(255) DEFAULT 'TechScout Editorial / Unsplash License',
  `attribution` varchar(255) DEFAULT NULL,
  `metadata_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata_json`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_media_type` (`file_type`),
  KEY `idx_media_folder` (`folder`),
  KEY `idx_media_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `media_files` (5 rows)
--
INSERT INTO `media_files` (`id`, `uuid`, `user_id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `mime_type`, `folder`, `alt_text`, `caption`, `copyright_info`, `attribution`, `metadata_json`, `created_at`) VALUES 
(1, 'med-uuid-01', 1, 'agentic-ai-flow.png', 'agentic-ai-flow.png', 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=1200&auto=format&fit=crop&q=80', 'image', 245120, 'image/png', 'articles', 'Agentic AI Autonomous Workflow Diagram', 'Multi-Agent LLM Orchestration Architecture', 'CC-BY-SA 4.0', 'TechScout Research Lab', NULL, '2026-09-04 21:17:02'),
(2, 'med-uuid-02', 1, 'deepseek-r1-benchmark.png', 'deepseek-r1-benchmark.png', 'https://images.unsplash.com/photo-1620712943543-bcc4688e7485?w=1200&auto=format&fit=crop&q=80', 'image', 382040, 'image/png', 'articles', 'DeepSeek R1 Benchmark Analysis', 'Reasoning Model Benchmark Performance Comparison', 'Editorial Use Only', 'DeepSeek AI & TechScout', NULL, '2026-09-04 21:17:02'),
(3, 'med-uuid-03', 1, 'cloud-kubernetes-mesh.png', 'cloud-kubernetes-mesh.png', 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=1200&auto=format&fit=crop&q=80', 'image', 512000, 'image/png', 'infrastructure', 'High-Availability Service Mesh Topology', 'Zero-Trust Istio Service Mesh on Kubernetes', 'Proprietary', 'DevOps Engineering Team', NULL, '2026-09-04 21:17:02'),
(4, 'med-uuid-04', 1, 'techscout-podcast-ep1.mp3', 'techscout-podcast-ep1.mp3', 'https://actions.google.com/sounds/v1/science/electronic_synth_chords.ogg', 'audio', 1845000, 'audio/mpeg', 'audio', 'TechScout Engineering Podcast Episode 1', 'Frontier Reasoning Models & Autonomous Code Synthesis', 'Copyright 2026 TechScout Media', 'TechScout Audio Productions', NULL, '2026-09-04 21:17:02'),
(5, 'med-uuid-05', 1, 'techscout-architecture-whitepaper.pdf', 'techscout-architecture-whitepaper.pdf', '/docs/techscout-architecture.pdf', 'document', 4194304, 'application/pdf', 'documents', 'TechScout System Architecture Whitepaper v1.0', 'Comprehensive 960-Task System Architecture Specification', 'All Rights Reserved', 'Antigravity Engineering Team', NULL, '2026-09-04 21:17:02');

--
-- Structure for table `navigation_menus`
--
DROP TABLE IF EXISTS `navigation_menus`;
CREATE TABLE `navigation_menus` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `location` enum('main','secondary','footer','mobile','dropdown','mega_menu','external') NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `label` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `target` enum('_self','_blank') DEFAULT '_self',
  `icon_name` varchar(50) DEFAULT '',
  `badge_text` varchar(50) DEFAULT '',
  `badge_color` varchar(50) DEFAULT '',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `mega_menu_data` longtext DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `navigation_menus` (21 rows)
--
INSERT INTO `navigation_menus` (`id`, `location`, `parent_id`, `label`, `url`, `target`, `icon_name`, `badge_text`, `badge_color`, `sort_order`, `is_active`, `mega_menu_data`, `created_at`, `updated_at`) VALUES 
(1, 'main', NULL, 'Home', '/', '_self', 'home', '', 'cyan', 1, 1, '', '2026-08-29 22:30:52', '2026-08-30 07:46:37'),
(2, 'main', NULL, 'Articles', '/blog', '_self', 'file-text', '', 'cyan', 2, 1, '', '2026-08-29 22:30:52', '2026-08-30 08:18:19'),
(3, 'main', NULL, 'Topics', '/topics', '_self', 'layers', '', 'pink', 3, 1, '', '2026-08-29 22:30:52', '2026-08-30 08:18:46'),
(4, 'main', NULL, 'AI Tools', '/tools', '_self', 'cpu', '', '', 4, 1, NULL, '2026-08-29 22:30:52', '2026-08-29 22:30:52'),
(5, 'main', NULL, 'Prompts', '/prompts', '_self', 'terminal', '', '', 5, 1, NULL, '2026-08-29 22:30:52', '2026-08-29 22:30:52'),
(6, 'main', NULL, 'GPTs', '/gpts', '_self', 'bot', '', 'green', 6, 1, '', '2026-08-29 22:30:52', '2026-08-30 08:18:56'),
(7, 'main', NULL, 'Challenges', '/campaigns', '_self', 'flag', '', 'amber', 7, 1, '', '2026-08-29 22:30:52', '2026-08-30 08:19:03'),
(8, 'main', NULL, 'Tech Jobs', '/jobs', '_self', 'briefcase', '', 'cyan', 8, 0, '', '2026-08-29 22:30:52', '2026-08-30 08:18:37'),
(9, 'secondary', NULL, 'Frontier AI Radar', '/tools', '_self', 'radar', '', '', 1, 1, NULL, '2026-08-29 22:30:52', '2026-08-29 22:30:52'),
(10, 'secondary', NULL, 'System Architecture', '/topic/architecture', '_self', 'server', '', '', 2, 1, NULL, '2026-08-29 22:30:52', '2026-08-29 22:30:52'),
(11, 'secondary', NULL, 'OpenAI Blueprints', '/prompts', '_self', 'sparkles', '', '', 3, 1, NULL, '2026-08-29 22:30:52', '2026-08-29 22:30:52'),
(12, 'secondary', NULL, 'Engineering Salaries', '/jobs', '_self', 'dollar-sign', '', '', 4, 1, NULL, '2026-08-29 22:30:52', '2026-08-29 22:30:52'),
(13, 'footer', NULL, 'AI Tools Directory', '/tools', '_self', '', '', '', 1, 1, NULL, '2026-08-29 22:30:52', '2026-08-29 22:30:52'),
(14, 'footer', NULL, 'Autonomous GPT Directory', '/gpts', '_self', '', '', '', 2, 1, NULL, '2026-08-29 22:30:52', '2026-08-29 22:30:52'),
(15, 'footer', NULL, 'Engineering Prompts', '/prompts', '_self', '', '', '', 3, 1, NULL, '2026-08-29 22:30:52', '2026-08-29 22:30:52'),
(16, 'footer', NULL, 'Hackathons & Bounties', '/campaigns', '_self', '', '', '', 4, 1, NULL, '2026-08-29 22:30:52', '2026-08-29 22:30:52'),
(17, 'footer', NULL, 'Engineering Jobs Board', '/jobs', '_self', '', '', '', 5, 1, NULL, '2026-08-29 22:30:52', '2026-08-29 22:30:52'),
(18, 'external', NULL, 'Twitter / X', 'https://twitter.com/techscout', '_blank', 'twitter', '', '', 1, 1, NULL, '2026-08-29 22:30:52', '2026-08-29 22:30:52'),
(19, 'external', NULL, 'GitHub', 'https://github.com/techscout', '_blank', 'github', '', '', 2, 1, NULL, '2026-08-29 22:30:52', '2026-08-29 22:30:52'),
(20, 'external', NULL, 'Discord Community', 'https://discord.gg/techscout', '_blank', 'message-circle', '', '', 3, 1, NULL, '2026-08-29 22:30:52', '2026-08-29 22:30:52'),
(21, 'external', NULL, 'RSS Feed', '/rss', '_blank', 'rss', '', '', 4, 1, NULL, '2026-08-29 22:30:52', '2026-08-29 22:30:52');

--
-- Structure for table `newsletter_issues`
--
DROP TABLE IF EXISTS `newsletter_issues`;
CREATE TABLE `newsletter_issues` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `issue_number` int(10) unsigned DEFAULT 1,
  `subject_line` varchar(255) NOT NULL,
  `preview_text` varchar(255) DEFAULT '',
  `template_type` varchar(100) DEFAULT 'weekly_digest',
  `body_html` longtext DEFAULT NULL,
  `status` enum('draft','scheduled','sent','archived') DEFAULT 'draft',
  `scheduled_at` datetime DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL,
  `total_recipients` int(10) unsigned DEFAULT 0,
  `open_count` int(10) unsigned DEFAULT 0,
  `click_count` int(10) unsigned DEFAULT 0,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Structure for table `newsletter_subscriptions`
--
DROP TABLE IF EXISTS `newsletter_subscriptions`;
CREATE TABLE `newsletter_subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(191) NOT NULL,
  `status` enum('pending','subscribed','unsubscribed') NOT NULL DEFAULT 'subscribed',
  `subscriber_type` enum('all','weekly_digest','ai_tools_only','developer_only') NOT NULL DEFAULT 'all',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `newsletter_subscriptions` (22 rows)
--
INSERT INTO `newsletter_subscriptions` (`id`, `email`, `status`, `subscriber_type`, `created_at`) VALUES 
(1, 'admin@techscout.online', 'subscribed', 'weekly_digest', '2026-07-07 06:22:31'),
(2, 'alex@techscout.online', 'subscribed', 'developer_only', '2026-07-22 06:22:32'),
(3, 'sarah@techscout.online', 'subscribed', 'developer_only', '2026-08-23 06:22:32'),
(4, 'david@techscout.online', 'subscribed', 'developer_only', '2026-08-01 06:22:32'),
(5, 'elena@techscout.online', 'subscribed', 'weekly_digest', '2026-08-20 06:22:32'),
(6, 'marcus@techscout.online', 'subscribed', 'all', '2026-07-14 06:22:32'),
(7, 'maya@techscout.online', 'subscribed', 'ai_tools_only', '2026-07-18 06:22:32'),
(8, 'kai@techscout.online', 'subscribed', 'all', '2026-07-27 06:22:32'),
(9, 'rachel@techscout.online', 'subscribed', 'ai_tools_only', '2026-07-12 06:22:32'),
(10, 'sam@techscout.online', 'subscribed', 'ai_tools_only', '2026-07-31 06:22:32'),
(11, 'asdf@asdf.asdf', 'subscribed', 'developer_only', '2026-08-14 06:22:32'),
(12, 'src_1788538666_252@example.com', 'subscribed', 'ai_tools_only', '2026-07-09 06:22:32'),
(13, 'sarah.connor@cyberdyne-ai.io', 'subscribed', 'weekly_digest', '2026-08-04 06:22:32'),
(14, 'elena.rostova@techscale.org', 'subscribed', 'all', '2026-07-26 06:22:32'),
(15, 'marcus.vance@vectorcloud.net', 'subscribed', 'ai_tools_only', '2026-08-13 06:22:32'),
(16, 'dev.lead@promptops.ai', 'subscribed', 'developer_only', '2026-08-21 06:22:32'),
(17, 'hassan.tariq@neuralflow.pk', 'subscribed', 'weekly_digest', '2026-07-30 06:22:32'),
(18, 'linus.torvalds@kernel-edge.com', 'subscribed', 'all', '2026-08-27 06:22:32'),
(19, 'alyssa.p@cloudmatrix.dev', 'subscribed', 'all', '2026-08-14 06:22:32'),
(20, 'chen.wei@deepinference.cn', 'subscribed', 'all', '2026-08-16 06:22:32'),
(21, 'james.wilson@quantumaudit.co.uk', 'subscribed', 'weekly_digest', '2026-08-10 06:22:32'),
(22, 'priya.sharma@mlstack.in', 'subscribed', 'weekly_digest', '2026-08-18 06:22:32');

--
-- Structure for table `notifications`
--
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'system',
  `event_type` varchar(60) NOT NULL,
  `title` varchar(150) NOT NULL,
  `message` varchar(300) NOT NULL,
  `action_url` varchar(255) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_notif_user` (`user_id`),
  CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications` (25 rows)
--
INSERT INTO `notifications` (`id`, `user_id`, `type`, `event_type`, `title`, `message`, `action_url`, `link_url`, `image_url`, `is_read`, `created_at`) VALUES 
(1, 2, 'system', '', 'Official Editorial Warning (1st Strike)', 'An editorial strike was issued against your account: warning. Further violations will result in account suspension.', '/support', NULL, NULL, 0, '2026-09-04 21:17:46'),
(2, 2, 'security', '', 'Account Suspended until Jan 1, 1970 (2nd Strike)', 'Your account has been suspended until Jan 1, 1970 for: suspension', '/support', NULL, NULL, 0, '2026-09-04 21:17:46'),
(3, 2, 'security', '', 'Account Permanently Banned (3rd Strike)', 'Your account has been banned due to repeated guideline violations: ban', '/support', NULL, NULL, 1, '2026-09-04 21:17:46'),
(4, 2, 'system', '', 'Official Editorial Warning (1st Strike)', 'An editorial strike was issued against your account: Minor guideline notice. Further violations will result in account suspension.', '/support', NULL, NULL, 0, '2026-09-04 21:19:15'),
(5, 2, 'security', '', 'Account Suspended until Sep 18, 2026 (2nd Strike)', 'Your account has been suspended until Sep 18, 2026 for: Repeated policy violation', '/support', NULL, NULL, 0, '2026-09-04 21:19:15'),
(6, 2, 'security', '', 'Account Permanently Banned (3rd Strike)', 'Your account has been banned due to repeated guideline violations: Severe plagiarism and harassment', '/support', NULL, NULL, 0, '2026-09-04 21:19:15'),
(7, 2, 'system', '', 'Official Editorial Warning (1st Strike)', 'An editorial strike was issued against your account: Minor guideline notice. Further violations will result in account suspension.', '/support', NULL, NULL, 0, '2026-09-04 21:19:50'),
(8, 2, 'security', '', 'Account Suspended until Sep 18, 2026 (2nd Strike)', 'Your account has been suspended until Sep 18, 2026 for: Repeated policy violation', '/support', NULL, NULL, 0, '2026-09-04 21:19:50'),
(9, 2, 'security', '', 'Account Permanently Banned (3rd Strike)', 'Your account has been banned due to repeated guideline violations: Severe plagiarism and harassment', '/support', NULL, NULL, 0, '2026-09-04 21:19:50'),
(10, 2, 'system', '', 'Official Editorial Warning (1st Strike)', 'An editorial strike was issued against your account: Minor guideline notice. Further violations will result in account suspension.', '/support', NULL, NULL, 0, '2026-09-04 21:20:26'),
(11, 2, 'security', '', 'Account Suspended until Sep 18, 2026 (2nd Strike)', 'Your account has been suspended until Sep 18, 2026 for: Repeated policy violation', '/support', NULL, NULL, 0, '2026-09-04 21:20:26'),
(12, 2, 'security', '', 'Account Permanently Banned (3rd Strike)', 'Your account has been banned due to repeated guideline violations: Severe plagiarism and harassment', '/support', NULL, NULL, 0, '2026-09-04 21:20:26'),
(13, 2, 'system', '', 'Official Editorial Warning (1st Strike)', 'An editorial strike was issued against your account: Minor guideline notice. Further violations will result in account suspension.', '/support', NULL, NULL, 0, '2026-09-04 21:20:45'),
(14, 2, 'security', '', 'Account Suspended until Sep 18, 2026 (2nd Strike)', 'Your account has been suspended until Sep 18, 2026 for: Repeated policy violation', '/support', NULL, NULL, 0, '2026-09-04 21:20:45'),
(15, 2, 'security', '', 'Account Permanently Banned (3rd Strike)', 'Your account has been banned due to repeated guideline violations: Severe plagiarism and harassment', '/support', NULL, NULL, 0, '2026-09-04 21:20:45'),
(16, 2, 'system', '', 'Official Editorial Warning (1st Strike)', 'An editorial strike was issued against your account: Minor guideline notice. Further violations will result in account suspension.', '/support', NULL, NULL, 0, '2026-09-04 21:25:11'),
(17, 2, 'security', '', 'Account Suspended until Sep 18, 2026 (2nd Strike)', 'Your account has been suspended until Sep 18, 2026 for: Repeated policy violation', '/support', NULL, NULL, 0, '2026-09-04 21:25:11'),
(18, 2, 'security', '', 'Account Permanently Banned (3rd Strike)', 'Your account has been banned due to repeated guideline violations: Severe plagiarism and harassment', '/support', NULL, NULL, 0, '2026-09-04 21:25:11'),
(19, 2, 'system', '', 'Official Editorial Warning (1st Strike)', 'An editorial strike was issued against your account: Test editorial strike for QA verification. Further violations will result in account suspension.', '/support', NULL, NULL, 0, '2026-09-04 21:46:18'),
(20, 11, 'system', '', 'Security Notice: Password Updated 🔒', 'Your account password was updated by a platform administrator. Please use your new password for your next login.', '/notifications', NULL, NULL, 1, '2026-09-04 22:19:10'),
(21, 7, 'reward', '', '💰 Wallet Credited: +$23 USD', 'Your creator balance has been credited with $23 USD. New Balance: $303 USD. Reason: nothing', '/author/dashboard', NULL, NULL, 0, '2026-09-04 22:43:44'),
(22, 1, 'reward', '', '💰 Wallet Credited: +$3.5 USD', 'Your creator balance has been credited with $3.5 USD. New Balance: $1029.75 USD. Reason: Direct QA Pool Verification Test', '/author/dashboard', NULL, NULL, 1, '2026-09-04 23:08:52'),
(23, 1, 'reward', '', '📉 Balance Adjustment: -$40 USD', 'An adjustment of -$40 USD was recorded on your account. New Balance: $989.75 USD. Reason: Withdrawal request via PAYPAL (PayPal: admin@techscout.online) [Tax: $2, Bank Tax/Fee: $0]', '/author/dashboard', NULL, NULL, 1, '2026-09-05 09:19:17'),
(24, 1, 'direct', '', 'Test In-App Direct Notification ⚡', 'This is a verified test dispatch for single-user notification targeting.', '/account/settings', NULL, NULL, 0, '2026-09-05 15:19:30'),
(25, 1, 'direct', '', 'Test In-App Direct Notification ⚡', 'This is a verified test dispatch for single-user notification targeting.', '/account/settings', NULL, NULL, 0, '2026-09-05 15:20:38');

--
-- Structure for table `plagiarism_scans`
--
DROP TABLE IF EXISTS `plagiarism_scans`;
CREATE TABLE `plagiarism_scans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` bigint(20) unsigned NOT NULL,
  `originality_score` decimal(5,2) NOT NULL DEFAULT 100.00,
  `duplicate_ratio` decimal(5,2) NOT NULL DEFAULT 0.00,
  `verdict` enum('clean','acceptable','suspicious','flagged_plagiarism') DEFAULT 'clean',
  `matched_content_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`matched_content_ids`)),
  `overlapping_shingles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`overlapping_shingles`)),
  `total_shingles` int(10) unsigned DEFAULT 0,
  `matching_shingles` int(10) unsigned DEFAULT 0,
  `scanned_by` bigint(20) unsigned DEFAULT NULL,
  `scanned_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_content_id` (`content_id`),
  KEY `idx_verdict` (`verdict`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure for table `prompts`
--
DROP TABLE IF EXISTS `prompts`;
CREATE TABLE `prompts` (
  `content_id` bigint(20) unsigned NOT NULL,
  `prompt_template` longtext NOT NULL,
  `template_variables` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`template_variables`)),
  `target_models` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`target_models`)),
  `copy_count` int(10) unsigned NOT NULL DEFAULT 0,
  `sample_output` text DEFAULT NULL,
  PRIMARY KEY (`content_id`),
  CONSTRAINT `fk_prompts_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `prompts` (7 rows)
--
INSERT INTO `prompts` (`content_id`, `prompt_template`, `template_variables`, `target_models`, `copy_count`, `sample_output`) VALUES 
(23, 'You are a Principal Database Reliability Engineer. Analyze the following schema and queries for table: {{table_name}}.\nDaily Query Volume: {{daily_queries}}.\nTarget Response Latency: {{target_latency}}.\n\nOutput:\n1. Execution plan bottlenecks.\n2. Exact CREATE INDEX statements with composite column order rationale.\n3. Query rewrite recommendations.', '[{\"name\": \"table_name\", \"label\": \"Table Name(s)\", \"default\": \"users, contents, comments\"}, {\"name\": \"daily_queries\", \"label\": \"Daily Query Load\", \"default\": \"5,000,000 queries/day\"}, {\"name\": \"target_latency\", \"label\": \"Target Query Latency\", \"default\": \"< 15ms\"}]', '[\"ChatGPT 4o\", \"Claude 3.5 Sonnet\", \"DeepSeek V3\"]', 4120, 'CREATE INDEX idx_contents_composite ON contents (content_type, status, published_at DESC);'),
(24, 'You are a Senior Platform & Cloud Architect. Hardened the following Dockerfile:\nBase Image: {{base_image}}\nLanguage Runtime: {{runtime_version}}\nTarget Size: {{target_size}}\n\nEnforce non-root user, multi-stage build, minimal package installations, and HEALTHCHECK directives.', '[{\"name\": \"base_image\", \"label\": \"Base Image\", \"default\": \"node:20-alpine\"}, {\"name\": \"runtime_version\", \"label\": \"Runtime Version\", \"default\": \"Node.js 20.18 LTS\"}, {\"name\": \"target_size\", \"label\": \"Target Image Size\", \"default\": \"< 80MB\"}]', '[\"Claude 3.5 Sonnet\", \"GPT-4o\"]', 2890, 'FROM node:20-alpine AS builder\nWORKDIR /app\nCOPY package*.json ./\nRUN npm ci\nCOPY . .\nRUN npm run build\n\nFROM node:20-alpine AS runner\nUSER node\nWORKDIR /app\nCOPY --from=builder /app/dist ./dist\nEXPOSE 3000\nCMD [\"node\", \"dist/index.js\"]'),
(25, 'You are a Staff Frontend Engineer. Refactor this React component:\nComponent Name: {{component_name}}\nState Management: {{state_type}}\nFramework: {{framework}}\n\nDecompose into atomic presentation components, extracted custom hooks with TypeScript generics, and zero redundant re-renders.', '[{\"name\": \"component_name\", \"label\": \"Component Name\", \"default\": \"UserProfileEditor\"}, {\"name\": \"state_type\", \"label\": \"State Type\", \"default\": \"Zustand / Local State\"}, {\"name\": \"framework\", \"label\": \"Framework\", \"default\": \"React 19 / Next.js\"}]', '[\"Claude 3.5 Sonnet\", \"GPT-4o\"]', 3450, 'export function useUserProfile(userId: string) { ... }'),
(26, 'You are a Lead API Architect. Generate a complete OpenAPI 3.1 YAML specification for module: {{module_name}}.\nBase URL: {{base_url}}\nAuthentication Method: {{auth_method}}.\n\nInclude JSON request schemas, 200/400/401/422 response examples, and rate limit header definitions.', '[{\"name\": \"module_name\", \"label\": \"Module Name\", \"default\": \"AI Tools Directory API\"}, {\"name\": \"base_url\", \"label\": \"Base URL\", \"default\": \"https://api.techscout.online/v1\"}, {\"name\": \"auth_method\", \"label\": \"Auth Method\", \"default\": \"Bearer JWT\"}]', '[\"GPT-4o\", \"Claude 3.5 Sonnet\"]', 2110, 'openapi: 3.1.0\ninfo:\n  title: TechScout Directory API\n  version: 1.0.0'),
(27, 'You are a Principal AI Prompt Engineer. Create a production-grade system prompt for: {{agent_role}}.\nTarget Domain: {{domain}}\nOutput Format: {{output_format}}.\n\nInclude explicit XML tag delimiters (<instructions>, <context>, <rules>, <output_format>), few-shot input/output examples, and strict refusal policies for out-of-domain requests.', '[{\"name\": \"agent_role\", \"label\": \"Agent Role\", \"default\": \"Senior SQL Optimization Agent\"}, {\"name\": \"domain\", \"label\": \"Domain Focus\", \"default\": \"Relational Database Architecture\"}, {\"name\": \"output_format\", \"label\": \"Output Format\", \"default\": \"JSON Schema + SQL Snippet\"}]', '[\"Claude 3.5 Sonnet\", \"GPT-4o\", \"DeepSeek R1\"]', 5622, '<system_prompt>\n<role>Senior SQL Optimization Agent</role>\n<rules>1. Always explain execution cost. 2. Output valid SQL.</rules>\n</system_prompt>'),
(28, 'You are an Application Security Auditor. Perform a penetration test review on the following controller:\nEndpoint: {{endpoint_path}}\nHTTP Method: {{http_method}}\nData Format: {{payload_type}}.\n\nCheck for OWASP Top 10 vulnerabilities, BOLA/BFLA, SQL Injection, SSRF, and authorization bypasses. Output vulnerability level and exact remediation code.', '[{\"name\": \"endpoint_path\", \"label\": \"Endpoint Path\", \"default\": \"/api/v1/users/{id}/settings\"}, {\"name\": \"http_method\", \"label\": \"HTTP Method\", \"default\": \"PUT / PATCH\"}, {\"name\": \"payload_type\", \"label\": \"Payload Type\", \"default\": \"JSON Object\"}]', '[\"GPT-4o\", \"Claude 3.5 Sonnet\"]', 1890, '### Vulnerability Assessment: BOLA Detected (High Severity)\nRemediation: Verify session user_id matches target entity before mutation.'),
(29, 'You are a Distributed Systems Architect. Design a resiliency blueprint for remote service communication:\nService Name: {{service_name}}\nFailure Mode: {{failure_mode}}\nTimeout Budget: {{timeout_ms}}.\n\nProvide Circuit Breaker configuration, Exponential Backoff retry policy with jitter, and Fallback response implementation.', '[{\"name\": \"service_name\", \"label\": \"Service Name\", \"default\": \"OpenAI Embeddings API Client\"}, {\"name\": \"failure_mode\", \"label\": \"Failure Mode\", \"default\": \"HTTP 429 Rate Limit & 503 Outage\"}, {\"name\": \"timeout_ms\", \"label\": \"Timeout Budget\", \"default\": \"2500ms\"}]', '[\"Claude 3.5 Sonnet\", \"GPT-4o\"]', 2410, 'class ResilientApiClient {\n  private circuitBreaker: CircuitBreaker;\n  // Exponential backoff with full jitter\n}');

--
-- Structure for table `reactions`
--
DROP TABLE IF EXISTS `reactions`;
CREATE TABLE `reactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `content_id` bigint(20) unsigned NOT NULL,
  `reaction_type` enum('like','clap','rocket','insightful','mind_blown','bookmark') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_content_reaction` (`user_id`,`content_id`,`reaction_type`),
  KEY `fk_reactions_content` (`content_id`),
  CONSTRAINT `fk_reactions_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reactions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=239 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reactions` (238 rows)
--
INSERT INTO `reactions` (`id`, `user_id`, `content_id`, `reaction_type`, `created_at`) VALUES 
(1, 1, 2, 'insightful', '2026-08-18 06:22:54'),
(2, 2, 2, 'like', '2026-08-11 06:22:54'),
(3, 4, 2, 'insightful', '2026-08-14 06:22:54'),
(4, 5, 2, 'clap', '2026-08-24 06:22:54'),
(5, 6, 2, 'like', '2026-08-30 06:22:54'),
(6, 9, 2, 'insightful', '2026-08-19 06:22:54'),
(7, 1, 3, 'clap', '2026-09-01 06:22:54'),
(8, 5, 3, 'rocket', '2026-08-29 06:22:54'),
(9, 6, 3, 'clap', '2026-08-29 06:22:54'),
(10, 8, 3, 'mind_blown', '2026-08-29 06:22:54'),
(11, 11, 3, 'rocket', '2026-08-16 06:22:54'),
(12, 1, 4, 'mind_blown', '2026-08-24 06:22:54'),
(13, 2, 4, 'rocket', '2026-08-22 06:22:54'),
(14, 3, 4, 'clap', '2026-08-14 06:22:54'),
(15, 4, 4, 'insightful', '2026-08-31 06:22:54'),
(16, 9, 4, 'clap', '2026-08-27 06:22:54'),
(17, 12, 4, 'like', '2026-09-02 06:22:54'),
(18, 1, 5, 'mind_blown', '2026-08-27 06:22:54'),
(19, 3, 5, 'insightful', '2026-08-27 06:22:54'),
(20, 4, 5, 'rocket', '2026-09-01 06:22:54'),
(21, 6, 5, 'rocket', '2026-08-28 06:22:54'),
(22, 7, 5, 'mind_blown', '2026-09-02 06:22:54'),
(23, 10, 5, 'mind_blown', '2026-08-11 06:22:54'),
(24, 11, 5, 'clap', '2026-08-14 06:22:54'),
(25, 12, 5, 'insightful', '2026-08-30 06:22:54'),
(26, 3, 6, 'mind_blown', '2026-08-20 06:22:54'),
(27, 5, 6, 'insightful', '2026-08-16 06:22:54'),
(28, 6, 6, 'rocket', '2026-08-21 06:22:54'),
(29, 9, 6, 'clap', '2026-08-28 06:22:54'),
(30, 6, 7, 'mind_blown', '2026-08-19 06:22:54'),
(31, 8, 7, 'rocket', '2026-08-25 06:22:54'),
(32, 10, 7, 'clap', '2026-08-28 06:22:54'),
(33, 11, 7, 'like', '2026-08-13 06:22:54'),
(34, 1, 8, 'insightful', '2026-08-29 06:22:54'),
(35, 2, 8, 'clap', '2026-08-20 06:22:54'),
(36, 4, 8, 'clap', '2026-09-04 06:22:54'),
(37, 5, 8, 'rocket', '2026-08-15 06:22:54'),
(38, 8, 8, 'clap', '2026-08-28 06:22:54'),
(39, 9, 8, 'insightful', '2026-08-15 06:22:54'),
(40, 11, 8, 'rocket', '2026-09-04 06:22:54'),
(41, 3, 9, 'mind_blown', '2026-08-13 06:22:54'),
(42, 4, 9, 'mind_blown', '2026-08-23 06:22:54'),
(43, 8, 9, 'mind_blown', '2026-08-28 06:22:54'),
(44, 9, 9, 'mind_blown', '2026-08-29 06:22:54'),
(45, 11, 9, 'rocket', '2026-08-14 06:22:54'),
(46, 12, 9, 'rocket', '2026-09-03 06:22:54'),
(47, 1, 10, 'rocket', '2026-08-28 06:22:54'),
(48, 2, 10, 'insightful', '2026-09-04 06:22:54'),
(49, 3, 10, 'mind_blown', '2026-08-25 06:22:54'),
(50, 5, 10, 'insightful', '2026-08-11 06:22:54');
INSERT INTO `reactions` (`id`, `user_id`, `content_id`, `reaction_type`, `created_at`) VALUES 
(51, 6, 10, 'insightful', '2026-08-18 06:22:54'),
(52, 10, 10, 'rocket', '2026-08-14 06:22:54'),
(53, 6, 11, 'like', '2026-08-24 06:22:54'),
(54, 8, 11, 'insightful', '2026-08-28 06:22:54'),
(55, 1, 12, 'clap', '2026-08-24 06:22:54'),
(56, 2, 12, 'mind_blown', '2026-09-02 06:22:54'),
(57, 6, 12, 'rocket', '2026-08-14 06:22:54'),
(58, 10, 12, 'clap', '2026-08-15 06:22:54'),
(59, 11, 12, 'clap', '2026-08-27 06:22:54'),
(60, 12, 12, 'like', '2026-08-11 06:22:54'),
(61, 3, 13, 'like', '2026-08-23 06:22:54'),
(62, 6, 13, 'like', '2026-08-29 06:22:54'),
(63, 12, 13, 'rocket', '2026-08-11 06:22:54'),
(64, 3, 14, 'mind_blown', '2026-08-12 06:22:54'),
(65, 4, 14, 'rocket', '2026-08-14 06:22:54'),
(66, 5, 14, 'insightful', '2026-08-16 06:22:54'),
(67, 7, 14, 'mind_blown', '2026-08-21 06:22:54'),
(68, 8, 14, 'like', '2026-08-17 06:22:54'),
(69, 9, 14, 'mind_blown', '2026-09-02 06:22:54'),
(70, 11, 14, 'like', '2026-08-22 06:22:54'),
(71, 1, 15, 'insightful', '2026-08-16 06:22:54'),
(72, 3, 15, 'insightful', '2026-08-18 06:22:54'),
(73, 4, 15, 'clap', '2026-09-01 06:22:54'),
(74, 6, 15, 'insightful', '2026-08-26 06:22:54'),
(75, 8, 15, 'rocket', '2026-08-11 06:22:54'),
(76, 9, 15, 'rocket', '2026-09-01 06:22:54'),
(77, 11, 15, 'mind_blown', '2026-08-18 06:22:54'),
(78, 1, 16, 'rocket', '2026-08-31 06:22:54'),
(79, 2, 16, 'like', '2026-08-15 06:22:54'),
(80, 3, 16, 'rocket', '2026-08-12 06:22:54'),
(81, 4, 16, 'like', '2026-08-13 06:22:54'),
(82, 6, 16, 'like', '2026-08-16 06:22:54'),
(83, 7, 16, 'mind_blown', '2026-09-02 06:22:54'),
(84, 11, 16, 'like', '2026-08-22 06:22:54'),
(85, 12, 16, 'mind_blown', '2026-08-15 06:22:54'),
(86, 2, 17, 'insightful', '2026-08-17 06:22:54'),
(87, 4, 17, 'like', '2026-08-28 06:22:54'),
(88, 8, 17, 'like', '2026-08-23 06:22:54'),
(89, 9, 17, 'clap', '2026-09-03 06:22:54'),
(90, 10, 17, 'mind_blown', '2026-08-19 06:22:54'),
(91, 12, 17, 'mind_blown', '2026-08-19 06:22:54'),
(92, 4, 18, 'insightful', '2026-08-28 06:22:54'),
(93, 5, 18, 'rocket', '2026-08-20 06:22:54'),
(94, 7, 18, 'clap', '2026-08-31 06:22:54'),
(95, 8, 18, 'mind_blown', '2026-08-26 06:22:54'),
(96, 9, 18, 'clap', '2026-08-29 06:22:54'),
(97, 11, 18, 'rocket', '2026-08-18 06:22:54'),
(98, 12, 18, 'insightful', '2026-08-13 06:22:54'),
(99, 3, 19, 'mind_blown', '2026-08-24 06:22:54'),
(100, 4, 19, 'mind_blown', '2026-08-11 06:22:54');
INSERT INTO `reactions` (`id`, `user_id`, `content_id`, `reaction_type`, `created_at`) VALUES 
(101, 7, 19, 'insightful', '2026-08-21 06:22:55'),
(102, 8, 19, 'rocket', '2026-08-30 06:22:55'),
(103, 9, 19, 'clap', '2026-08-17 06:22:55'),
(104, 11, 19, 'insightful', '2026-08-20 06:22:55'),
(105, 1, 20, 'like', '2026-08-30 06:22:55'),
(106, 3, 20, 'insightful', '2026-08-15 06:22:55'),
(107, 4, 20, 'like', '2026-08-31 06:22:55'),
(108, 6, 20, 'mind_blown', '2026-08-16 06:22:55'),
(109, 7, 20, 'rocket', '2026-08-31 06:22:55'),
(110, 8, 20, 'mind_blown', '2026-08-28 06:22:55'),
(111, 9, 20, 'mind_blown', '2026-09-04 06:22:55'),
(112, 10, 20, 'clap', '2026-08-28 06:22:55'),
(113, 11, 20, 'insightful', '2026-08-13 06:22:55'),
(114, 12, 20, 'insightful', '2026-08-14 06:22:55'),
(115, 3, 21, 'rocket', '2026-08-21 06:22:55'),
(116, 4, 21, 'like', '2026-09-03 06:22:55'),
(117, 6, 21, 'rocket', '2026-08-27 06:22:55'),
(118, 8, 21, 'clap', '2026-08-13 06:22:55'),
(119, 10, 21, 'mind_blown', '2026-08-16 06:22:55'),
(120, 3, 22, 'rocket', '2026-08-30 06:22:55'),
(121, 6, 22, 'mind_blown', '2026-08-31 06:22:55'),
(122, 7, 22, 'rocket', '2026-08-27 06:22:55'),
(123, 1, 23, 'like', '2026-08-28 06:22:55'),
(124, 3, 23, 'mind_blown', '2026-08-12 06:22:55'),
(125, 5, 23, 'rocket', '2026-08-26 06:22:55'),
(126, 9, 23, 'like', '2026-08-22 06:22:55'),
(127, 10, 23, 'clap', '2026-09-03 06:22:55'),
(128, 1, 24, 'mind_blown', '2026-08-26 06:22:55'),
(129, 2, 24, 'insightful', '2026-08-30 06:22:55'),
(130, 3, 24, 'mind_blown', '2026-08-24 06:22:55'),
(131, 4, 24, 'mind_blown', '2026-08-23 06:22:55'),
(132, 8, 24, 'clap', '2026-08-22 06:22:55'),
(133, 12, 24, 'insightful', '2026-08-16 06:22:55'),
(134, 1, 25, 'rocket', '2026-09-01 06:22:55'),
(135, 6, 25, 'rocket', '2026-08-23 06:22:55'),
(136, 7, 25, 'insightful', '2026-08-31 06:22:55'),
(137, 8, 25, 'clap', '2026-08-30 06:22:55'),
(138, 9, 25, 'like', '2026-08-12 06:22:55'),
(139, 11, 25, 'mind_blown', '2026-08-15 06:22:55'),
(140, 1, 26, 'clap', '2026-08-12 06:22:55'),
(141, 2, 26, 'insightful', '2026-09-02 06:22:55'),
(142, 5, 26, 'clap', '2026-08-30 06:22:55'),
(143, 8, 26, 'clap', '2026-08-31 06:22:55'),
(144, 10, 26, 'like', '2026-08-21 06:22:55'),
(145, 1, 27, 'mind_blown', '2026-08-23 06:22:55'),
(146, 4, 27, 'rocket', '2026-08-16 06:22:55'),
(147, 9, 27, 'clap', '2026-08-30 06:22:55'),
(148, 10, 27, 'rocket', '2026-08-28 06:22:55'),
(149, 1, 28, 'clap', '2026-08-25 06:22:55'),
(150, 6, 28, 'clap', '2026-08-12 06:22:55');
INSERT INTO `reactions` (`id`, `user_id`, `content_id`, `reaction_type`, `created_at`) VALUES 
(151, 7, 28, 'clap', '2026-08-22 06:22:55'),
(152, 8, 28, 'rocket', '2026-08-23 06:22:55'),
(153, 9, 28, 'mind_blown', '2026-08-26 06:22:55'),
(154, 10, 28, 'insightful', '2026-08-30 06:22:55'),
(155, 2, 29, 'clap', '2026-08-31 06:22:55'),
(156, 7, 29, 'rocket', '2026-09-04 06:22:55'),
(157, 10, 29, 'insightful', '2026-08-28 06:22:55'),
(158, 12, 29, 'rocket', '2026-08-14 06:22:55'),
(159, 1, 30, 'rocket', '2026-08-27 06:22:55'),
(160, 3, 30, 'insightful', '2026-08-18 06:22:55'),
(161, 5, 30, 'clap', '2026-08-13 06:22:55'),
(162, 6, 30, 'clap', '2026-09-02 06:22:55'),
(163, 7, 30, 'like', '2026-08-19 06:22:55'),
(164, 8, 30, 'clap', '2026-08-13 06:22:55'),
(165, 9, 30, 'insightful', '2026-08-19 06:22:55'),
(166, 11, 30, 'rocket', '2026-08-20 06:22:55'),
(167, 12, 30, 'insightful', '2026-08-23 06:22:55'),
(168, 5, 31, 'mind_blown', '2026-08-27 06:22:55'),
(169, 7, 31, 'rocket', '2026-08-13 06:22:55'),
(170, 8, 31, 'insightful', '2026-09-01 06:22:55'),
(171, 2, 32, 'insightful', '2026-08-26 06:22:55'),
(172, 3, 32, 'mind_blown', '2026-08-11 06:22:55'),
(173, 4, 32, 'like', '2026-08-27 06:22:55'),
(174, 8, 32, 'mind_blown', '2026-08-22 06:22:55'),
(175, 10, 32, 'rocket', '2026-08-12 06:22:55'),
(176, 11, 32, 'clap', '2026-08-26 06:22:55'),
(177, 1, 33, 'like', '2026-08-21 06:22:55'),
(178, 4, 33, 'like', '2026-08-18 06:22:55'),
(179, 5, 33, 'rocket', '2026-08-25 06:22:55'),
(180, 6, 33, 'like', '2026-08-16 06:22:55'),
(181, 8, 33, 'rocket', '2026-08-29 06:22:55'),
(182, 10, 33, 'insightful', '2026-08-31 06:22:55'),
(183, 10, 34, 'like', '2026-08-12 06:22:55'),
(184, 11, 34, 'like', '2026-08-30 06:22:55'),
(185, 12, 34, 'insightful', '2026-08-17 06:22:55'),
(186, 3, 35, 'rocket', '2026-08-15 06:22:55'),
(187, 4, 35, 'like', '2026-09-02 06:22:55'),
(188, 5, 35, 'insightful', '2026-08-14 06:22:55'),
(189, 8, 35, 'mind_blown', '2026-08-16 06:22:55'),
(190, 11, 35, 'clap', '2026-08-12 06:22:55'),
(191, 12, 35, 'like', '2026-08-21 06:22:55'),
(192, 1, 36, 'rocket', '2026-08-13 06:22:55'),
(193, 3, 36, 'mind_blown', '2026-08-13 06:22:55'),
(194, 5, 36, 'insightful', '2026-09-02 06:22:55'),
(195, 6, 36, 'insightful', '2026-08-24 06:22:55'),
(196, 7, 36, 'like', '2026-08-15 06:22:55'),
(197, 9, 36, 'insightful', '2026-08-29 06:22:55'),
(198, 12, 36, 'mind_blown', '2026-09-03 06:22:55'),
(199, 2, 37, 'rocket', '2026-08-28 06:22:55'),
(200, 4, 37, 'mind_blown', '2026-08-26 06:22:55');
INSERT INTO `reactions` (`id`, `user_id`, `content_id`, `reaction_type`, `created_at`) VALUES 
(201, 5, 37, 'clap', '2026-08-24 06:22:55'),
(202, 7, 37, 'insightful', '2026-08-21 06:22:55'),
(203, 3, 38, 'like', '2026-08-20 06:22:55'),
(204, 5, 38, 'clap', '2026-08-20 06:22:55'),
(205, 7, 38, 'rocket', '2026-09-03 06:22:55'),
(206, 10, 38, 'mind_blown', '2026-08-22 06:22:55'),
(207, 12, 38, 'like', '2026-08-21 06:22:55'),
(208, 1, 39, 'mind_blown', '2026-08-31 06:22:55'),
(209, 5, 39, 'mind_blown', '2026-08-19 06:22:55'),
(210, 7, 39, 'rocket', '2026-08-16 06:22:55'),
(211, 8, 39, 'like', '2026-09-03 06:22:55'),
(212, 1, 41, 'mind_blown', '2026-08-13 06:22:55'),
(213, 2, 41, 'clap', '2026-08-31 06:22:55'),
(214, 4, 41, 'clap', '2026-08-31 06:22:55'),
(215, 9, 41, 'like', '2026-08-11 06:22:55'),
(216, 12, 41, 'insightful', '2026-09-04 06:22:55'),
(217, 4, 42, 'rocket', '2026-08-25 06:22:55'),
(218, 9, 42, 'like', '2026-08-17 06:22:55'),
(219, 10, 42, 'rocket', '2026-08-18 06:22:55'),
(220, 11, 42, 'like', '2026-08-29 06:22:55'),
(221, 12, 42, 'rocket', '2026-09-02 06:22:55'),
(222, 2, 44, 'like', '2026-08-31 06:22:55'),
(223, 3, 44, 'mind_blown', '2026-08-15 06:22:55'),
(224, 4, 44, 'mind_blown', '2026-08-14 06:22:55'),
(225, 5, 44, 'mind_blown', '2026-08-15 06:22:55'),
(226, 11, 44, 'clap', '2026-08-11 06:22:55'),
(227, 1, 45, 'clap', '2026-08-16 06:22:55'),
(228, 2, 45, 'rocket', '2026-08-21 06:22:55'),
(229, 3, 45, 'mind_blown', '2026-08-18 06:22:55'),
(230, 4, 45, 'clap', '2026-08-15 06:22:55'),
(231, 6, 45, 'like', '2026-08-19 06:22:55'),
(232, 9, 45, 'insightful', '2026-08-29 06:22:55'),
(233, 11, 45, 'rocket', '2026-09-04 06:22:55'),
(234, 5, 46, 'mind_blown', '2026-08-23 06:22:55'),
(235, 6, 46, 'clap', '2026-08-13 06:22:55'),
(236, 8, 46, 'rocket', '2026-08-27 06:22:55'),
(237, 11, 46, 'like', '2026-08-28 06:22:55'),
(238, 12, 2, 'like', '2026-09-05 15:05:35');

--
-- Structure for table `reading_history`
--
DROP TABLE IF EXISTS `reading_history`;
CREATE TABLE `reading_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `content_id` bigint(20) unsigned NOT NULL,
  `session_id` varchar(64) DEFAULT NULL,
  `session_number` int(11) NOT NULL DEFAULT 1,
  `started_at` datetime NOT NULL DEFAULT current_timestamp(),
  `progress_percent` int(11) NOT NULL DEFAULT 0,
  `read_seconds` int(11) NOT NULL DEFAULT 0,
  `last_read_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `completed_at` datetime DEFAULT NULL,
  `status` enum('reading','completed','re_reading') NOT NULL DEFAULT 'reading',
  `referrer_type` varchar(32) NOT NULL DEFAULT 'internal',
  `referrer_source` varchar(255) DEFAULT '/directory',
  PRIMARY KEY (`id`),
  KEY `idx_rh_user` (`user_id`),
  KEY `idx_rh_last_read` (`last_read_at`),
  KEY `idx_user_content` (`user_id`,`content_id`),
  KEY `idx_reading_history_lookup` (`content_id`,`last_read_at`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reading_history` (2 rows)
--
INSERT INTO `reading_history` (`id`, `user_id`, `content_id`, `session_id`, `session_number`, `started_at`, `progress_percent`, `read_seconds`, `last_read_at`, `completed_at`, `status`, `referrer_type`, `referrer_source`) VALUES 
(1, 1, 38, 'sess_1788601921448_7ebx0ra', 1, '2026-09-05 14:52:01', 12, 23, '2026-09-05 14:52:24', NULL, 'reading', 'internal', '/directory'),
(2, 12, 2, 'sess_1788602723158_9rp3zyj', 1, '2026-09-05 15:05:23', 100, 40, '2026-09-05 15:06:24', '2026-09-05 15:05:35', 'completed', 'internal', '/directory');

--
-- Structure for table `redirects`
--
DROP TABLE IF EXISTS `redirects`;
CREATE TABLE `redirects` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `source_path` varchar(255) NOT NULL,
  `target_path` varchar(255) NOT NULL,
  `status_code` smallint(5) unsigned NOT NULL DEFAULT 301,
  `hit_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `source_path` (`source_path`),
  KEY `idx_redirect_source` (`source_path`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure for table `referral_commissions`
--
DROP TABLE IF EXISTS `referral_commissions`;
CREATE TABLE `referral_commissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `referral_id` bigint(20) unsigned NOT NULL,
  `referrer_author_id` int(10) unsigned NOT NULL,
  `ledger_reference_no` varchar(64) NOT NULL,
  `amount_usd` decimal(10,2) NOT NULL,
  `source_event` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_rc_referral` (`referral_id`),
  KEY `idx_rc_referrer` (`referrer_author_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `referral_commissions` (2 rows)
--
INSERT INTO `referral_commissions` (`id`, `referral_id`, `referrer_author_id`, `ledger_reference_no`, `amount_usd`, `source_event`, `created_at`) VALUES 
(1, 1, 1, 'LEDGER-REF-2AF524C497', 45.00, 'challenge_bounty_split', '2026-08-21 06:22:56'),
(2, 2, 2, 'LEDGER-REF-28A952DAC7', 15.00, 'challenge_bounty_split', '2026-08-21 06:22:56');

--
-- Structure for table `reports`
--
DROP TABLE IF EXISTS `reports`;
CREATE TABLE `reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reporter_user_id` bigint(20) unsigned DEFAULT NULL,
  `content_id` bigint(20) unsigned DEFAULT NULL,
  `comment_id` bigint(20) unsigned DEFAULT NULL,
  `reason` enum('spam','plagiarism','misinformation','abusive','broken_link','copyright','other') NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','investigating','resolved','dismissed') NOT NULL DEFAULT 'pending',
  `resolved_by_user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_reports_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure for table `revenue_records`
--
DROP TABLE IF EXISTS `revenue_records`;
CREATE TABLE `revenue_records` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` bigint(20) unsigned NOT NULL,
  `description` varchar(255) NOT NULL,
  `credits_granted` decimal(10,2) NOT NULL DEFAULT 0.00,
  `amount_usd` decimal(10,2) NOT NULL DEFAULT 0.00,
  `transaction_type` enum('credit_reward','view_milestone','upvote_bonus','editorial_grant','ad_redemption') NOT NULL DEFAULT 'credit_reward',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_rev_author` (`author_id`),
  CONSTRAINT `fk_rev_author` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `revenue_records` (3 rows)
--
INSERT INTO `revenue_records` (`id`, `author_id`, `description`, `credits_granted`, `amount_usd`, `transaction_type`, `created_at`) VALUES 
(1, 7, 'nothing', 23.00, 23.00, 'editorial_grant', '2026-09-04 22:43:44'),
(2, 1, 'Direct QA Pool Verification Test', 3.50, 3.50, '', '2026-09-04 23:08:52'),
(3, 1, 'Withdrawal request via PAYPAL (PayPal: admin@techscout.online) [Tax: $2, Bank Tax/Fee: $0]', -40.00, -40.00, '', '2026-09-05 09:19:17');

--
-- Structure for table `security_anomalies`
--
DROP TABLE IF EXISTS `security_anomalies`;
CREATE TABLE `security_anomalies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `event_type` enum('brute_force_lockout','new_ip_login','new_device_login','rapid_requests','sensitive_endpoint_breach','session_hijack_suspected') NOT NULL,
  `severity` enum('low','medium','high','critical') DEFAULT 'medium',
  `description` varchar(500) NOT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `is_resolved` tinyint(1) DEFAULT 0,
  `resolved_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ip_created` (`ip_address`,`created_at`),
  KEY `idx_user_resolved` (`user_id`,`is_resolved`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `security_anomalies` (4 rows)
--
INSERT INTO `security_anomalies` (`id`, `user_id`, `ip_address`, `user_agent`, `event_type`, `severity`, `description`, `metadata`, `is_resolved`, `resolved_at`, `created_at`) VALUES 
(1, 1, '127.0.0.1', 'Unknown', 'rapid_requests', 'high', 'Suspicious activity auto-flag triggered: Automated comment spam detected (14 comments/min)', '{\"reason\":\"Automated comment spam detected (14 comments\\/min)\"}', 1, '2026-09-04 22:51:35', '2026-09-04 21:19:50'),
(2, 1, '127.0.0.1', 'Unknown', 'rapid_requests', 'high', 'Suspicious activity auto-flag triggered: Automated comment spam detected (14 comments/min)', '{\"reason\":\"Automated comment spam detected (14 comments\\/min)\"}', 1, '2026-09-04 22:51:37', '2026-09-04 21:20:27'),
(3, 1, '127.0.0.1', 'Unknown', 'rapid_requests', 'high', 'Suspicious activity auto-flag triggered: Automated comment spam detected (14 comments/min)', '{\"reason\":\"Automated comment spam detected (14 comments\\/min)\"}', 1, '2026-09-04 22:51:40', '2026-09-04 21:20:45'),
(4, 1, '127.0.0.1', 'Unknown', 'rapid_requests', 'high', 'Suspicious activity auto-flag triggered: Automated comment spam detected (14 comments/min)', '{\"reason\":\"Automated comment spam detected (14 comments\\/min)\"}', 1, '2026-09-04 22:51:28', '2026-09-04 21:25:11');

--
-- Structure for table `seo_metadata`
--
DROP TABLE IF EXISTS `seo_metadata`;
CREATE TABLE `seo_metadata` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `path_pattern` varchar(191) NOT NULL,
  `meta_title` varchar(200) NOT NULL,
  `meta_description` text NOT NULL,
  `og_image_url` varchar(255) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL,
  `robots_directive` varchar(60) NOT NULL DEFAULT 'index, follow',
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `path_pattern` (`path_pattern`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure for table `site_announcements`
--
DROP TABLE IF EXISTS `site_announcements`;
CREATE TABLE `site_announcements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `ribbon_type` enum('info','announcement','warning','promo','maintenance') NOT NULL DEFAULT 'announcement',
  `banner_color` varchar(20) NOT NULL DEFAULT '#6366f1',
  `text_color` varchar(20) NOT NULL DEFAULT '#ffffff',
  `action_label` varchar(60) DEFAULT NULL,
  `action_url` varchar(255) DEFAULT NULL,
  `target_audience` enum('all','guests','members','authors') NOT NULL DEFAULT 'all',
  `is_dismissible` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure for table `site_settings`
--
DROP TABLE IF EXISTS `site_settings`;
CREATE TABLE `site_settings` (
  `key_name` varchar(100) NOT NULL,
  `key_value` text DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`key_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings` (161 rows)
--
INSERT INTO `site_settings` (`key_name`, `key_value`, `updated_at`) VALUES 
('ad_header_code', '', '2026-08-29 16:36:37'),
('ad_slot_footer', '', '2026-08-29 16:36:37'),
('ad_slot_in_article', '', '2026-08-29 16:36:37'),
('ad_slot_sidebar', '', '2026-08-29 16:36:37'),
('ad_slot_top', '', '2026-08-29 16:36:37'),
('allow_all_authors_templates', 0, '2026-08-29 16:14:02'),
('analytics_anonymize_ips', 1, '2026-08-29 22:55:30'),
('analytics_ga4_id', '', '2026-08-29 22:55:30'),
('analytics_plausible_domain', '', '2026-08-29 22:55:30'),
('analytics_track_internal_telemetry', 0, '2026-08-29 22:55:30'),
('api_default_key_rpm', 60, '2026-08-29 22:55:30'),
('api_default_token_expiry_days', 365, '2026-08-29 22:55:30'),
('api_expose_swagger', 0, '2026-08-29 22:55:31'),
('api_public_enabled', 0, '2026-08-29 22:55:30'),
('api_require_auth_public', 0, '2026-08-29 22:55:30'),
('auth_max_login_retries', 5, '2026-08-29 22:55:30'),
('auth_min_password_len', 6, '2026-08-29 22:55:30'),
('auth_session_lifetime_min', 1440, '2026-08-29 22:55:30'),
('auto_compress_images', 0, '2026-08-29 22:55:30'),
('brand_custom_css', '', '2026-08-29 22:55:30'),
('brand_default_theme', 'dark', '2026-08-29 22:55:30'),
('brand_favicon_url', '', '2026-08-29 22:55:30'),
('brand_logo_url', '', '2026-08-29 22:55:30'),
('brand_primary_color', '#6366f1', '2026-08-29 22:55:30'),
('brand_secondary_color', '#00f0ff', '2026-08-29 22:55:30'),
('cdn_base_url', '', '2026-08-29 22:55:30'),
('cdn_cache_ttl', 86400, '2026-08-29 22:55:30'),
('cdn_enabled', 0, '2026-08-29 22:55:30'),
('cdn_minify_assets', 1, '2026-08-29 22:55:30'),
('custom_robots_txt', 'User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /auth/\nSitemap: https://techscout.online/sitemap.xml', '2026-08-29 19:21:45'),
('default_currency', 'USD', '2026-08-29 22:55:30'),
('default_timezone', 'Asia/Karachi', '2026-08-29 16:36:37'),
('domain_app_url', 'http://localhost/techscout.n', '2026-08-29 22:55:30'),
('domain_canonical', 'http://localhost/techscout.n', '2026-09-05 12:42:33'),
('domain_force_https', 1, '2026-08-29 22:55:30'),
('domain_trailing_slash', 'strip', '2026-08-29 22:55:30'),
('enable_bg_grid_lines', 1, '2026-09-02 21:03:41'),
('enable_crypto_payments', 0, '2026-08-29 22:55:30'),
('enable_jobs_section', 0, '2026-09-02 21:04:28'),
('enable_stripe_payments', 0, '2026-08-29 22:55:30'),
('enable_user_registration', 1, '2026-08-29 22:55:30'),
('flag_author_studio', 1, '2026-08-29 22:55:30'),
('flag_community_comments', 1, '2026-08-29 22:55:30'),
('flag_custom_gpts', 1, '2026-08-29 22:55:30'),
('flag_emoji_reactions', 1, '2026-08-29 22:55:30'),
('flag_jobs_board', 1, '2026-08-29 22:55:30'),
('flag_markdown_exporter', 1, '2026-08-29 22:55:30'),
('flag_prompt_blueprints', 1, '2026-08-29 22:55:30'),
('flag_rewards_shop', 1, '2026-08-29 22:55:30'),
('force_staff_2fa', 0, '2026-08-29 22:55:30');
INSERT INTO `site_settings` (`key_name`, `key_value`, `updated_at`) VALUES 
('integration_discord_enabled', 0, '2026-08-29 22:55:31'),
('integration_discord_webhook_url', '', '2026-08-29 22:55:30'),
('integration_slack_enabled', 0, '2026-08-29 22:55:31'),
('integration_telegram_bot_token', '', '2026-08-29 22:55:30'),
('integration_telegram_chat_id', '', '2026-08-29 22:55:30'),
('integration_telegram_enabled', 0, '2026-08-29 22:55:31'),
('items_per_page', 32, '2026-08-30 13:29:07'),
('loc_date_format', 'M j, Y', '2026-08-29 22:55:30'),
('loc_default_lang', 'en', '2026-08-29 22:55:30'),
('loc_direction', 'ltr', '2026-08-29 22:55:30'),
('loc_multilang_enabled', 0, '2026-08-29 22:55:31'),
('mail_api_endpoint', 'https://api.resend.com/emails', '2026-08-29 17:35:52'),
('mail_api_key', '', '2026-08-29 17:35:52'),
('mail_api_provider', 'resend', '2026-08-29 17:35:52'),
('mail_driver', 'smtp', '2026-09-05 17:59:02'),
('mail_enabled', 1, '2026-09-05 17:59:02'),
('mail_from_address', 'notifications@techscout.online', '2026-08-29 17:35:52'),
('mail_from_name', 'TechScout Intelligence', '2026-08-29 17:35:52'),
('mail_smtp_encryption', 'tls', '2026-08-29 17:35:52'),
('mail_smtp_host', 'smtp.gmail.com', '2026-08-29 17:35:52'),
('mail_smtp_password', 'mhif hhtr bdef esku', '2026-09-05 17:58:35'),
('mail_smtp_port', 587, '2026-08-29 17:35:52'),
('mail_smtp_username', 'xpertprogrammerofficial@gmail.com', '2026-09-05 17:58:35'),
('moderation_auto_engine_enabled', 0, '2026-08-29 22:55:30'),
('moderation_blacklist_action', 'mask', '2026-08-29 22:55:30'),
('moderation_max_strikes', 3, '2026-08-29 22:55:30'),
('notifications_inapp_enabled', 1, '2026-08-29 22:55:30'),
('notifications_sound_enabled', 1, '2026-08-29 22:55:30'),
('payment_creator_rev_share', 70, '2026-08-29 22:55:30'),
('payment_credits_per_xp', 10, '2026-08-29 22:55:30'),
('payment_min_payout_usd', 50, '2026-08-29 22:55:30'),
('payout_allowed_methods', '[{\"key\":\"paypal\",\"name\":\"PayPal (Instant Electronic Transfer)\",\"description\":\"Fast digital disbursement directly to your PayPal registered email address\",\"icon\":\"🅿️\",\"enabled\":true,\"is_custom\":false,\"fee_type\":\"platform_only\",\"custom\":false,\"fee_policy\":\"standard\"},{\"key\":\"bank_wire\",\"name\":\"Direct Bank Wire (ACH \\/ SWIFT \\/ IBAN)\",\"description\":\"Direct wire transfer to your local or international bank checking \\/ savings account\",\"icon\":\"🏦\",\"enabled\":true,\"is_custom\":false,\"fee_type\":\"platform_and_bank\",\"custom\":false,\"fee_policy\":\"bank\"},{\"key\":\"crypto_usdc\",\"name\":\"USDC \\/ USDT Stablecoin Crypto Transfer\",\"description\":\"On-chain payout directly to your crypto wallet address (ERC-20, Solana, Polygon, Arbitrum)\",\"icon\":\"🪙\",\"enabled\":true,\"is_custom\":false,\"fee_type\":\"platform_only\",\"custom\":false,\"fee_policy\":\"standard\"},{\"key\":\"stripe\",\"name\":\"Stripe Express Direct\",\"description\":\"Direct automated payout to your verified debit card or bank account via Stripe Connect\",\"icon\":\"💳\",\"enabled\":false,\"is_custom\":false,\"fee_type\":\"platform_only\",\"custom\":false,\"fee_policy\":\"standard\"},{\"key\":\"wise\",\"name\":\"Wise (formerly TransferWise)\",\"description\":\"Low-fee cross-border multi-currency bank disbursement directly via Wise\",\"icon\":\"🌐\",\"enabled\":false,\"is_custom\":false,\"fee_type\":\"platform_and_bank\",\"custom\":false,\"fee_policy\":\"bank\"}]', '2026-09-05 10:01:46'),
('payout_bank_fee_percent', 2.5, '2026-09-05 10:01:46'),
('payout_bank_wire_surcharge', 5, '2026-09-05 10:01:46'),
('payout_platform_fee_percent', 5, '2026-09-05 10:01:46'),
('require_email_verification', 1, '2026-08-29 22:55:30'),
('search_driver', 'mysql', '2026-08-29 22:55:30'),
('search_highlight_matches', 0, '2026-08-29 22:55:30'),
('search_min_query_len', 2, '2026-08-29 22:55:30'),
('security_auto_ban_threshold', 15, '2026-08-29 22:55:30'),
('security_csp_strict', 0, '2026-08-29 22:55:30'),
('security_csrf_strict', 0, '2026-08-29 22:55:30'),
('security_honeypot_enabled', 0, '2026-08-29 22:55:30'),
('security_rate_limit_per_min', 120, '2026-08-29 22:55:30'),
('seo_auto_generate_sitemap', 0, '2026-08-29 22:55:30'),
('seo_bing_verification', '', '2026-08-29 22:55:30'),
('seo_google_verification', '', '2026-08-29 22:55:30'),
('seo_title_suffix', ' | TechScout Platform', '2026-08-29 22:55:30'),
('seo_twitter_handle', '@techscout', '2026-08-29 22:55:30'),
('site_copyright', '© 2026 TechScout Platform • Built for builders, engineers, and creators.', '2026-08-29 22:55:30');
INSERT INTO `site_settings` (`key_name`, `key_value`, `updated_at`) VALUES 
('site_description', 'TechScout is a high-performance engineering publication and verified directory of AI tools, custom GPTs, and prompt blueprints.', '2026-08-29 22:55:30'),
('site_maintenance_mode', 1, '2026-09-05 15:44:13'),
('site_name', 'TechScout Innovation Hub', '2026-08-30 13:29:07'),
('site_tagline', 'Enterprise AI & Tech Knowledge Network', '2026-08-30 13:29:07'),
('social_color_discord', '#5865f2', '2026-08-29 20:13:29'),
('social_color_facebook', '#1877f2', '2026-08-29 20:13:29'),
('social_color_github', '#f8fafc', '2026-08-29 20:13:29'),
('social_color_instagram', '#e1306c', '2026-08-29 20:13:29'),
('social_color_linkedin', '#0a66c2', '2026-08-29 20:13:29'),
('social_color_reddit', '#ff4500', '2026-08-29 20:13:29'),
('social_color_rss', '#f59e0b', '2026-08-29 20:13:29'),
('social_color_telegram', '#229ed9', '2026-08-29 20:13:29'),
('social_color_tiktok', '#ff0050', '2026-08-29 20:13:29'),
('social_color_twitch', '#9146ff', '2026-08-29 20:13:29'),
('social_color_twitter', '#38bdf8', '2026-08-29 20:13:29'),
('social_color_youtube', '#ef4444', '2026-08-29 20:13:29'),
('social_discord', 'https://discord.gg/techscout', '2026-08-29 16:14:02'),
('social_enabled_discord', 1, '2026-08-29 20:13:29'),
('social_enabled_facebook', 1, '2026-08-29 20:13:29'),
('social_enabled_github', 0, '2026-08-29 20:13:29'),
('social_enabled_instagram', 1, '2026-08-29 20:13:29'),
('social_enabled_linkedin', 0, '2026-08-29 20:14:05'),
('social_enabled_reddit', 0, '2026-08-29 20:13:29'),
('social_enabled_rss', 0, '2026-09-02 21:04:11'),
('social_enabled_telegram', 1, '2026-09-02 21:04:11'),
('social_enabled_tiktok', 1, '2026-08-29 20:13:29'),
('social_enabled_twitch', 0, '2026-08-29 20:13:29'),
('social_enabled_twitter', 0, '2026-08-29 20:14:05'),
('social_enabled_youtube', 1, '2026-08-29 20:13:29'),
('social_facebook', 'https://facebook.com/techscout', '2026-08-29 16:14:02'),
('social_github', 'https://github.com/techscout', '2026-08-29 16:14:02'),
('social_icon_discord', 'message-square', '2026-08-29 20:13:29'),
('social_icon_facebook', 'facebook', '2026-08-29 20:13:29'),
('social_icon_github', 'github', '2026-08-29 20:13:29'),
('social_icon_instagram', 'instagram', '2026-08-29 20:13:29'),
('social_icon_linkedin', 'linkedin', '2026-08-29 20:13:29'),
('social_icon_reddit', 'message-circle', '2026-08-29 20:13:29'),
('social_icon_rss', 'rss', '2026-08-29 20:13:29'),
('social_icon_telegram', 'send', '2026-08-29 20:13:29'),
('social_icon_tiktok', 'video', '2026-08-29 20:13:29'),
('social_icon_twitch', 'tv', '2026-08-29 20:13:29'),
('social_icon_twitter', 'twitter', '2026-08-29 20:13:29'),
('social_icon_youtube', 'youtube', '2026-08-29 20:13:29'),
('social_instagram', 'https://instagram.com/techscout', '2026-08-29 20:13:29'),
('social_linkedin', 'https://linkedin.com/company/techscout', '2026-08-29 16:14:02'),
('social_login_github', 0, '2026-08-29 22:55:30'),
('social_login_google', 0, '2026-08-29 22:55:30'),
('social_reddit', 'https://reddit.com/r/techscout', '2026-08-29 16:36:37'),
('social_rss', 'https://techscout.n/feed.xml', '2026-08-29 16:14:02'),
('social_telegram', 'https://t.me/techscout', '2026-08-29 16:36:37');
INSERT INTO `site_settings` (`key_name`, `key_value`, `updated_at`) VALUES 
('social_tiktok', 'https://tiktok.com/@techscout', '2026-08-29 20:13:29'),
('social_twitch', 'https://twitch.tv/techscout', '2026-08-29 20:13:29'),
('social_twitter', 'https://twitter.com/techscout', '2026-08-29 16:14:02'),
('social_youtube', 'https://youtube.com/@techscout', '2026-08-29 16:14:02'),
('storage_driver', 'local', '2026-08-29 22:55:30'),
('storage_image_quality', 85, '2026-08-29 22:55:30'),
('storage_max_upload_mb', 10, '2026-08-29 22:55:30'),
('storage_s3_bucket', '', '2026-08-29 22:55:30'),
('storage_s3_region', 'us-east-1', '2026-08-29 22:55:30'),
('stripe_publishable_key', '', '2026-08-29 22:55:30'),
('stripe_secret_key', '', '2026-08-29 22:55:30');

--
-- Structure for table `support_categories`
--
DROP TABLE IF EXISTS `support_categories`;
CREATE TABLE `support_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `department` varchar(100) NOT NULL DEFAULT 'Engineering',
  `name` varchar(150) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT '',
  `subcategories_json` text DEFAULT NULL,
  `default_priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `sla_hours` int(10) unsigned NOT NULL DEFAULT 24,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `support_categories` (6 rows)
--
INSERT INTO `support_categories` (`id`, `department`, `name`, `slug`, `description`, `subcategories_json`, `default_priority`, `sla_hours`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES 
(1, 'Engineering & Systems', 'Technical Support & Platform Infrastructure', 'technical', 'API errors, server 500s, webhook delivery, CLI sync issues', '[{\"name\":\"Cloud Infrastructure & Server Error\",\"slug\":\"cloud_infra\",\"priority\":\"high\",\"sla_hours\":6},{\"name\":\"REST \\/ Webhook API Ingestion\",\"slug\":\"api_webhooks\",\"priority\":\"high\",\"sla_hours\":6},{\"name\":\"Database & Query Diagnostic\",\"slug\":\"db_error\",\"priority\":\"urgent\",\"sla_hours\":2},{\"name\":\"Frontend UI \\/ Rendering Bug\",\"slug\":\"frontend_bug\",\"priority\":\"medium\",\"sla_hours\":24},{\"name\":\"Latency & Connection Timeout\",\"slug\":\"latency\",\"priority\":\"medium\",\"sla_hours\":24}]', 'high', 6, 1, 1, '2026-09-05 12:36:56', '2026-09-05 12:36:56'),
(2, 'Creator & Editorial', 'Author Studio & Creator Royalties', 'creator', 'Author verification, CPM calculations, pool distributions & drafts', '[{\"name\":\"Revenue Pool & CPM Inquiries\",\"slug\":\"revenue_pool\",\"priority\":\"medium\",\"sla_hours\":24},{\"name\":\"Author Application Status\",\"slug\":\"author_application\",\"priority\":\"low\",\"sla_hours\":48},{\"name\":\"Editor Studio Draft Autosave Issue\",\"slug\":\"draft_autosave\",\"priority\":\"high\",\"sla_hours\":6},{\"name\":\"Monetization Checklist Verification\",\"slug\":\"monetization_check\",\"priority\":\"medium\",\"sla_hours\":24},{\"name\":\"Custom Tip Jar & Payout Destination\",\"slug\":\"tip_jar\",\"priority\":\"medium\",\"sla_hours\":24}]', 'medium', 24, 2, 1, '2026-09-05 12:36:56', '2026-09-05 12:36:56'),
(3, 'Finance & Operations', 'Billing, Credits & Withdrawals', 'billing', 'Payout requests, withdrawal disputes, subscription billing, invoice requests', '[{\"name\":\"Pending Withdrawal Claim Dispute\",\"slug\":\"withdrawal_claim\",\"priority\":\"high\",\"sla_hours\":6},{\"name\":\"Bank \\/ PayPal \\/ Crypto Payout Delay\",\"slug\":\"payout_delay\",\"priority\":\"high\",\"sla_hours\":6},{\"name\":\"Invoice & VAT Receipt Request\",\"slug\":\"invoice_request\",\"priority\":\"low\",\"sla_hours\":48},{\"name\":\"Chargeback & Payment Reversal\",\"slug\":\"chargeback\",\"priority\":\"urgent\",\"sla_hours\":2}]', 'high', 6, 3, 1, '2026-09-05 12:36:56', '2026-09-05 12:36:56'),
(4, 'Cybersecurity & Trust', 'Account Security & Access Perimeter', 'security', '2FA lockout, unauthorized device sign-in, security vulnerability reports', '[{\"name\":\"2FA TOTP Lost Authenticator Reset\",\"slug\":\"2fa_reset\",\"priority\":\"urgent\",\"sla_hours\":2},{\"name\":\"Suspicious Session \\/ Device Compromise\",\"slug\":\"device_compromise\",\"priority\":\"urgent\",\"sla_hours\":2},{\"name\":\"Responsible Vulnerability Disclosure (Bounty)\",\"slug\":\"vuln_bounty\",\"priority\":\"urgent\",\"sla_hours\":2},{\"name\":\"Password Reset Delivery Failure\",\"slug\":\"reset_delivery\",\"priority\":\"high\",\"sla_hours\":6}]', 'urgent', 2, 4, 1, '2026-09-05 12:36:56', '2026-09-05 12:36:56'),
(5, 'Content Governance', 'Content Moderation & Strike Appeals', 'appeal', 'Appeals for unlisted articles, strike removals, plagiarism defense', '[{\"name\":\"Article Quarantine Appeal\",\"slug\":\"quarantine_appeal\",\"priority\":\"medium\",\"sla_hours\":24},{\"name\":\"Author Strike Removal Petition\",\"slug\":\"strike_appeal\",\"priority\":\"medium\",\"sla_hours\":24},{\"name\":\"False Plagiarism Flag Refutation\",\"slug\":\"plagiarism_defense\",\"priority\":\"medium\",\"sla_hours\":24},{\"name\":\"DMCA Counter-Notification Notice\",\"slug\":\"dmca_counter\",\"priority\":\"high\",\"sla_hours\":6}]', 'medium', 24, 5, 1, '2026-09-05 12:36:56', '2026-09-05 12:36:56'),
(6, 'Product Innovation', 'Feature Suggestions & Architecture Requests', 'feature', 'Recommendations for new developer challenges, AI integrations, or CLI features', '[{\"name\":\"New AI Tool \\/ Model Directory Request\",\"slug\":\"tool_request\",\"priority\":\"low\",\"sla_hours\":48},{\"name\":\"Developer Challenge \\/ Hackathon Idea\",\"slug\":\"hackathon_idea\",\"priority\":\"low\",\"sla_hours\":48},{\"name\":\"Editor Studio UI & Markdown Enhancements\",\"slug\":\"editor_feedback\",\"priority\":\"low\",\"sla_hours\":48},{\"name\":\"Public REST API Feature Proposal\",\"slug\":\"api_proposal\",\"priority\":\"low\",\"sla_hours\":48}]', 'low', 48, 6, 1, '2026-09-05 12:36:56', '2026-09-05 12:36:56');

--
-- Structure for table `support_tickets`
--
DROP TABLE IF EXISTS `support_tickets`;
CREATE TABLE `support_tickets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_number` varchar(30) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `department` varchar(100) DEFAULT 'General Engineering',
  `category` varchar(50) NOT NULL DEFAULT 'general',
  `subcategory` varchar(100) DEFAULT 'General',
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `sla_due_at` datetime DEFAULT NULL,
  `is_escalated` tinyint(1) DEFAULT 0,
  `escalated_at` datetime DEFAULT NULL,
  `first_response_at` datetime DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `status` enum('open','in_progress','waiting_user','resolved','closed') NOT NULL DEFAULT 'open',
  `assigned_to` bigint(20) unsigned DEFAULT NULL,
  `last_reply_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `ticket_number` (`ticket_number`),
  KEY `idx_st_user` (`user_id`),
  KEY `idx_st_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `support_tickets` (1 rows)
--
INSERT INTO `support_tickets` (`id`, `ticket_number`, `user_id`, `department`, `category`, `subcategory`, `priority`, `sla_due_at`, `is_escalated`, `escalated_at`, `first_response_at`, `subject`, `status`, `assigned_to`, `last_reply_at`, `created_at`, `updated_at`) VALUES 
(1, 'TS-E78384B4', 8, 'General Engineering', 'technical', 'General', 'high', NULL, 0, NULL, NULL, 'API Integration Webhook Latency Question', 'open', NULL, '2026-09-04 21:46:18', '2026-09-04 21:46:18', '2026-09-04 21:46:18');

--
-- Structure for table `system_error_logs`
--
DROP TABLE IF EXISTS `system_error_logs`;
CREATE TABLE `system_error_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `error_type` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `line` int(10) unsigned DEFAULT NULL,
  `stack_trace` longtext DEFAULT NULL,
  `request_uri` varchar(255) DEFAULT NULL,
  `request_method` varchar(10) DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `resolved` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_err_type` (`error_type`),
  KEY `idx_err_resolved` (`resolved`),
  KEY `idx_err_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_error_logs` (72 rows)
--
INSERT INTO `system_error_logs` (`id`, `error_type`, `message`, `file`, `line`, `stack_trace`, `request_uri`, `request_method`, `user_id`, `ip_address`, `resolved`, `created_at`) VALUES 
(1, 'Error', 'Call to undefined method App\\Core\\Request::isAjax()', 'C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\AdminController.php', 1452, '#0 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(67): App\\Controllers\\AdminController->quickResetLink(Object(App\\Core\\Request), Array)\n#1 C:\\xampp\\htdocs\\techscout.n\\index.php(586): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#2 {main}', '/techscout.n/admin/users/quick-reset-link/2', 'POST', 1, '::1', 1, '2026-09-04 21:23:02'),
(2, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 28, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(28): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 28)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(67): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(589): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?qa_auth=member&theme=dark', 'GET', 8, '::1', 0, '2026-09-04 21:50:15'),
(3, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 31, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(31): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 31)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(67): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(589): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?qa_auth=member&theme=dark', 'GET', 8, '::1', 0, '2026-09-04 21:50:15'),
(4, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 34, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(34): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 34)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(67): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(589): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?qa_auth=member&theme=dark', 'GET', 8, '::1', 0, '2026-09-04 21:50:15'),
(5, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 37, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(37): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 37)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(67): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(589): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?qa_auth=member&theme=dark', 'GET', 8, '::1', 0, '2026-09-04 21:50:15'),
(6, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 28, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(28): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 28)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(67): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(589): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?qa_auth=member&theme=light', 'GET', 8, '::1', 0, '2026-09-04 21:50:19'),
(7, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 31, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(31): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 31)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(67): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(589): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?qa_auth=member&theme=light', 'GET', 8, '::1', 0, '2026-09-04 21:50:19'),
(8, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 34, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(34): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 34)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(67): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(589): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?qa_auth=member&theme=light', 'GET', 8, '::1', 0, '2026-09-04 21:50:19'),
(9, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 37, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(37): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 37)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(67): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(589): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?qa_auth=member&theme=light', 'GET', 8, '::1', 0, '2026-09-04 21:50:19'),
(10, 'DATABASE_ERROR', 'SQLSTATE[42S22]: Column not found: 1054 Unknown column \'u.display_name\' in \'field list\' | SQL: \n            SELECT u.id, u.username, u.display_name, u.avatar_url, u.role\n            FROM users u\n            ORDER BY u.id ASC\n            LIMIT 50\n        ', 'C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Database.php', 1368, '#0 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Database.php(1368): PDO->prepare(\'\\n            SE...\')\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\AdminController.php(4917): App\\Core\\Database::query(\'\\n            SE...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(67): App\\Controllers\\AdminController->engagementStudio(Object(App\\Core\\Request), Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\index.php(589): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#4 {main}', '/techscout.n/admin/engagement-studio', 'GET', 1, '::1', 0, '2026-09-04 21:55:32'),
(11, 'Error', 'Call to undefined method App\\Core\\Request::isAjax()', 'C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\AdminController.php', 1452, '#0 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(67): App\\Controllers\\AdminController->quickResetLink(Object(App\\Core\\Request), Array)\n#1 C:\\xampp\\htdocs\\techscout.n\\index.php(589): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#2 {main}', '/techscout.n/admin/users/quick-reset-link/12', 'POST', 1, '::1', 0, '2026-09-04 21:57:24'),
(12, 'Error', 'Call to undefined method App\\Core\\Request::isAjax()', 'C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\AdminController.php', 1452, '#0 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\AdminController->quickResetLink(Object(App\\Core\\Request), Array)\n#1 C:\\xampp\\htdocs\\techscout.n\\index.php(593): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#2 {main}', '/techscout.n/admin/users/quick-reset-link/12', 'POST', 1, '::1', 0, '2026-09-04 22:07:32'),
(13, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 28, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(28): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 28)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(593): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?qa_auth=member&theme=dark', 'GET', 8, '::1', 0, '2026-09-04 22:11:43'),
(14, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 31, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(31): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 31)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(593): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?qa_auth=member&theme=dark', 'GET', 8, '::1', 0, '2026-09-04 22:11:43'),
(15, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 34, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(34): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 34)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(593): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?qa_auth=member&theme=dark', 'GET', 8, '::1', 0, '2026-09-04 22:11:43'),
(16, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 37, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(37): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 37)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(593): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?qa_auth=member&theme=dark', 'GET', 8, '::1', 0, '2026-09-04 22:11:43'),
(17, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 28, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(28): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 28)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(593): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?qa_auth=member&theme=light', 'GET', 8, '::1', 0, '2026-09-04 22:11:48'),
(18, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 31, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(31): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 31)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(593): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?qa_auth=member&theme=light', 'GET', 8, '::1', 0, '2026-09-04 22:11:48'),
(19, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 34, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(34): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 34)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(593): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?qa_auth=member&theme=light', 'GET', 8, '::1', 0, '2026-09-04 22:11:48'),
(20, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 37, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(37): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 37)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(593): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?qa_auth=member&theme=light', 'GET', 8, '::1', 0, '2026-09-04 22:11:48'),
(21, 'DATABASE_ERROR', 'SQLSTATE[42S22]: Column not found: 1054 Unknown column \'u.display_name\' in \'field list\' | SQL: \n            SELECT u.id, u.username, u.display_name, u.avatar_url, u.role\n            FROM users u\n            ORDER BY u.id ASC\n            LIMIT 50\n        ', 'C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Database.php', 1368, '#0 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Database.php(1368): PDO->prepare(\'\\n            SE...\')\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\AdminController.php(4917): App\\Core\\Database::query(\'\\n            SE...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\AdminController->engagementStudio(Object(App\\Core\\Request), Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\index.php(593): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#4 {main}', '/techscout.n/admin/engagement-studio', 'GET', 1, '::1', 0, '2026-09-04 22:25:18'),
(22, 'DATABASE_ERROR', 'SQLSTATE[42S02]: Base table or view not found: 1146 Table \'techscout_db.user_profiles\' doesn\'t exist | SQL: DESCRIBE user_profiles', 'C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Database.php', 1369, '#0 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Database.php(1369): PDOStatement->execute(Array)\n#1 Command line code(1): App\\Core\\Database::query(\'DESCRIBE user_p...\')\n#2 {main}', NULL, NULL, NULL, '127.0.0.1', 0, '2026-09-04 22:27:36'),
(23, 'Error', 'Class \"App\\Controllers\\View\" not found', 'C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\AdminController.php', 4955, '#0 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\AdminController->engagementStudio(Object(App\\Core\\Request), Array)\n#1 C:\\xampp\\htdocs\\techscout.n\\index.php(595): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#2 {main}', '/techscout.n/admin/engagement-studio?qa_auth=admin', 'HEAD', 1, '::1', 0, '2026-09-04 22:30:27'),
(24, 'E_WARNING', 'Undefined array key \"avatar_url\"', 'C:\\xampp\\htdocs\\techscout.n\\views\\account\\settings.php', 9, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\account\\settings.php(9): App\\Core\\ErrorLogger::handleError(2, \'Undefined array...\', \'C:\\\\xampp\\\\htdocs...\', 9)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\AuthController.php(507): App\\Core\\Response::view(\'account/setting...\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\AuthController->accountSettings(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(595): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/account/settings', 'GET', 11, '::1', 0, '2026-09-04 22:33:00'),
(25, 'E_WARNING', 'Undefined array key \"avatar_url\"', 'C:\\xampp\\htdocs\\techscout.n\\views\\account\\settings.php', 107, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\account\\settings.php(107): App\\Core\\ErrorLogger::handleError(2, \'Undefined array...\', \'C:\\\\xampp\\\\htdocs...\', 107)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\AuthController.php(507): App\\Core\\Response::view(\'account/setting...\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\AuthController->accountSettings(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(595): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/account/settings', 'GET', 11, '::1', 0, '2026-09-04 22:33:00'),
(26, 'E_WARNING', 'Undefined array key \"avatar_url\"', 'C:\\xampp\\htdocs\\techscout.n\\views\\account\\settings.php', 9, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\account\\settings.php(9): App\\Core\\ErrorLogger::handleError(2, \'Undefined array...\', \'C:\\\\xampp\\\\htdocs...\', 9)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\AuthController.php(507): App\\Core\\Response::view(\'account/setting...\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\AuthController->accountSettings(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(595): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/account/settings', 'GET', 11, '::1', 0, '2026-09-04 22:34:50'),
(27, 'E_WARNING', 'Undefined array key \"avatar_url\"', 'C:\\xampp\\htdocs\\techscout.n\\views\\account\\settings.php', 107, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\account\\settings.php(107): App\\Core\\ErrorLogger::handleError(2, \'Undefined array...\', \'C:\\\\xampp\\\\htdocs...\', 107)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\AuthController.php(507): App\\Core\\Response::view(\'account/setting...\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\AuthController->accountSettings(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(595): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/account/settings', 'GET', 11, '::1', 0, '2026-09-04 22:34:50'),
(28, 'E_WARNING', 'Undefined array key \"avatar_url\"', 'C:\\xampp\\htdocs\\techscout.n\\views\\account\\settings.php', 9, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\account\\settings.php(9): App\\Core\\ErrorLogger::handleError(2, \'Undefined array...\', \'C:\\\\xampp\\\\htdocs...\', 9)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\AuthController.php(507): App\\Core\\Response::view(\'account/setting...\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\AuthController->accountSettings(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(595): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/account/settings', 'GET', 11, '::1', 0, '2026-09-04 22:36:45'),
(29, 'E_WARNING', 'Undefined array key \"avatar_url\"', 'C:\\xampp\\htdocs\\techscout.n\\views\\account\\settings.php', 107, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\account\\settings.php(107): App\\Core\\ErrorLogger::handleError(2, \'Undefined array...\', \'C:\\\\xampp\\\\htdocs...\', 107)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\AuthController.php(507): App\\Core\\Response::view(\'account/setting...\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\AuthController->accountSettings(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(595): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/account/settings', 'GET', 11, '::1', 0, '2026-09-04 22:36:45'),
(30, 'Error', 'Class \"App\\Controllers\\View\" not found', 'C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\AdminController.php', 4947, '#0 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\AdminController->engagementStudio(Object(App\\Core\\Request), Array)\n#1 C:\\xampp\\htdocs\\techscout.n\\index.php(595): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#2 {main}', '/techscout.n/admin/engagement-studio?qa_auth=admin', 'HEAD', 1, '::1', 0, '2026-09-04 22:37:29'),
(31, 'E_WARNING', 'Undefined array key \"avatar_url\"', 'C:\\xampp\\htdocs\\techscout.n\\views\\account\\settings.php', 9, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\account\\settings.php(9): App\\Core\\ErrorLogger::handleError(2, \'Undefined array...\', \'C:\\\\xampp\\\\htdocs...\', 9)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\AuthController.php(507): App\\Core\\Response::view(\'account/setting...\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\AuthController->accountSettings(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(595): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/account/settings?qa_auth=member&theme=light', 'HEAD', 8, '::1', 0, '2026-09-04 22:37:34'),
(32, 'E_WARNING', 'Undefined array key \"avatar_url\"', 'C:\\xampp\\htdocs\\techscout.n\\views\\account\\settings.php', 107, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\account\\settings.php(107): App\\Core\\ErrorLogger::handleError(2, \'Undefined array...\', \'C:\\\\xampp\\\\htdocs...\', 107)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\AuthController.php(507): App\\Core\\Response::view(\'account/setting...\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\AuthController->accountSettings(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(595): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/account/settings?qa_auth=member&theme=light', 'HEAD', 8, '::1', 0, '2026-09-04 22:37:34'),
(33, 'Error', 'Call to undefined method App\\Core\\Auth::requireRole()', 'C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\AdminController.php', 4279, '#0 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\AdminController->distributeRevenuePool(Object(App\\Core\\Request), Array)\n#1 C:\\xampp\\htdocs\\techscout.n\\index.php(596): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#2 {main}', '/techscout.n/admin/revenue/distribute-pool?qa_auth=admin', 'POST', 1, '::1', 0, '2026-09-04 23:05:36'),
(34, 'Error', 'Call to undefined method App\\Core\\Auth::requireRole()', 'C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\AdminController.php', 4279, '#0 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\AdminController->distributeRevenuePool(Object(App\\Core\\Request), Array)\n#1 C:\\xampp\\htdocs\\techscout.n\\index.php(596): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#2 {main}', '/techscout.n/admin/revenue/distribute-pool?qa_auth=admin', 'POST', 1, '::1', 0, '2026-09-04 23:06:24'),
(35, 'Error', 'Class \"App\\Controllers\\View\" not found', 'C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\AdminController.php', 5044, '#0 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\AdminController->engagementStudio(Object(App\\Core\\Request), Array)\n#1 C:\\xampp\\htdocs\\techscout.n\\index.php(596): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#2 {main}', '/techscout.n/admin/engagement-studio', 'GET', 1, '::1', 0, '2026-09-04 23:09:26'),
(36, 'Error', 'Class \"App\\Controllers\\View\" not found', 'C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\AdminController.php', 5044, '#0 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\AdminController->engagementStudio(Object(App\\Core\\Request), Array)\n#1 C:\\xampp\\htdocs\\techscout.n\\index.php(596): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#2 {main}', '/techscout.n/admin/engagement-studio', 'GET', 1, '::1', 0, '2026-09-04 23:09:37'),
(37, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 28, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(28): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 28)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support', 'GET', 2, '::1', 0, '2026-09-05 09:33:48'),
(38, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 31, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(31): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 31)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support', 'GET', 2, '::1', 0, '2026-09-05 09:33:48'),
(39, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 34, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(34): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 34)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support', 'GET', 2, '::1', 0, '2026-09-05 09:33:48'),
(40, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 37, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(37): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 37)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support', 'GET', 2, '::1', 0, '2026-09-05 09:33:48'),
(41, 'DATABASE_ERROR', 'SQLSTATE[42S02]: Base table or view not found: 1146 Table \'techscout_db.post_views\' doesn\'t exist | SQL: \n                        INSERT INTO post_views (content_id, ip_address, created_at)\n                        VALUES (?, ?, DATE_SUB(NOW(), INTERVAL ? DAY))\n                    ', 'C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Database.php', 1406, '#0 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Database.php(1406): PDO->prepare(\'\\n              ...\')\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\AdminController.php(5267): App\\Core\\Database::execute(\'\\n              ...\', Array)\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\AdminController->simulateEngagement(Object(App\\Core\\Request), Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#4 {main}', '/techscout.n/admin/engagement-studio/simulate', 'POST', 1, '::1', 0, '2026-09-05 09:41:10'),
(42, 'DATABASE_ERROR', 'SQLSTATE[42S02]: Base table or view not found: 1146 Table \'techscout_db.post_views\' doesn\'t exist | SQL: \n                        INSERT INTO post_views (content_id, ip_address, created_at)\n                        VALUES (?, ?, DATE_SUB(NOW(), INTERVAL ? DAY))\n                    ', 'C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Database.php', 1406, '#0 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Database.php(1406): PDO->prepare(\'\\n              ...\')\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\AdminController.php(5351): App\\Core\\Database::execute(\'\\n              ...\', Array)\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\AdminController->simulateEngagement(Object(App\\Core\\Request), Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#4 {main}', '/techscout.n/admin/engagement-studio/simulate', 'POST', 1, '::1', 0, '2026-09-05 11:17:35'),
(43, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 28, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(28): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 28)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?qa_auth=member&theme=dark', 'GET', 8, '::1', 0, '2026-09-05 11:33:09'),
(44, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 31, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(31): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 31)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?qa_auth=member&theme=dark', 'GET', 8, '::1', 0, '2026-09-05 11:33:09'),
(45, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 34, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(34): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 34)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?qa_auth=member&theme=dark', 'GET', 8, '::1', 0, '2026-09-05 11:33:09'),
(46, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 37, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(37): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 37)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?qa_auth=member&theme=dark', 'GET', 8, '::1', 0, '2026-09-05 11:33:09'),
(47, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 28, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(28): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 28)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?qa_auth=member&theme=light', 'GET', 8, '::1', 0, '2026-09-05 11:33:10'),
(48, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 31, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(31): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 31)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?qa_auth=member&theme=light', 'GET', 8, '::1', 0, '2026-09-05 11:33:10'),
(49, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 34, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(34): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 34)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?qa_auth=member&theme=light', 'GET', 8, '::1', 0, '2026-09-05 11:33:10'),
(50, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 37, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(37): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 37)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?qa_auth=member&theme=light', 'GET', 8, '::1', 0, '2026-09-05 11:33:10');
INSERT INTO `system_error_logs` (`id`, `error_type`, `message`, `file`, `line`, `stack_trace`, `request_uri`, `request_method`, `user_id`, `ip_address`, `resolved`, `created_at`) VALUES 
(51, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 28, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(28): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 28)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?ticket=1&qa_auth=member&theme=dark', 'GET', 8, '::1', 0, '2026-09-05 11:33:20'),
(52, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 31, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(31): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 31)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?ticket=1&qa_auth=member&theme=dark', 'GET', 8, '::1', 0, '2026-09-05 11:33:20'),
(53, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 34, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(34): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 34)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?ticket=1&qa_auth=member&theme=dark', 'GET', 8, '::1', 0, '2026-09-05 11:33:20'),
(54, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 37, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(37): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 37)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?ticket=1&qa_auth=member&theme=dark', 'GET', 8, '::1', 0, '2026-09-05 11:33:20'),
(55, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 28, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(28): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 28)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?ticket=1&qa_auth=member&theme=light', 'GET', 8, '::1', 0, '2026-09-05 11:33:21'),
(56, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 31, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(31): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 31)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?ticket=1&qa_auth=member&theme=light', 'GET', 8, '::1', 0, '2026-09-05 11:33:21'),
(57, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 34, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(34): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 34)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?ticket=1&qa_auth=member&theme=light', 'GET', 8, '::1', 0, '2026-09-05 11:33:21'),
(58, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 37, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(37): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 37)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support?ticket=1&qa_auth=member&theme=light', 'GET', 8, '::1', 0, '2026-09-05 11:33:21'),
(59, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 28, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(28): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 28)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support', 'GET', 12, '::1', 0, '2026-09-05 12:18:04'),
(60, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 31, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(31): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 31)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support', 'GET', 12, '::1', 0, '2026-09-05 12:18:04'),
(61, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 34, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(34): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 34)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support', 'GET', 12, '::1', 0, '2026-09-05 12:18:04'),
(62, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 37, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(37): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 37)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support', 'GET', 12, '::1', 0, '2026-09-05 12:18:04'),
(63, 'DATABASE_ERROR', 'SQLSTATE[HY093]: Invalid parameter number | SQL: INSERT INTO support_tickets (ticket_number, user_id, category, priority, sla_due_at, status, last_reply_at, created_at) VALUES (?, ?, ?, ?, ?, \'open\', NOW(), NOW())', 'C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Database.php', 1407, '#0 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Database.php(1407): PDOStatement->execute(Array)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(93): App\\Core\\Database::execute(\'INSERT INTO sup...\', Array)\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->create(Object(App\\Core\\Request), Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#4 {main}', '/techscout.n/support/ticket/create', 'POST', 12, '::1', 0, '2026-09-05 12:18:23'),
(64, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 28, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(28): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 28)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support', 'GET', 12, '::1', 0, '2026-09-05 12:36:03'),
(65, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 31, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(31): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 31)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support', 'GET', 12, '::1', 0, '2026-09-05 12:36:03'),
(66, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 34, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(34): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 34)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support', 'GET', 12, '::1', 0, '2026-09-05 12:36:03'),
(67, 'E_WARNING', 'Undefined variable $currentStatus', 'C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php', 37, '#0 C:\\xampp\\htdocs\\techscout.n\\views\\support\\index.php(37): App\\Core\\ErrorLogger::handleError(2, \'Undefined varia...\', \'C:\\\\xampp\\\\htdocs...\', 37)\n#1 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Response.php(16): require(\'C:\\\\xampp\\\\htdocs...\')\n#2 C:\\xampp\\htdocs\\techscout.n\\app\\Controllers\\SupportController.php(60): App\\Core\\Response::view(\'support/index\', Array)\n#3 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Router.php(73): App\\Controllers\\SupportController->index(Object(App\\Core\\Request), Array)\n#4 C:\\xampp\\htdocs\\techscout.n\\index.php(601): App\\Core\\Router->dispatch(Object(App\\Core\\Request))\n#5 {main}', '/techscout.n/support', 'GET', 12, '::1', 0, '2026-09-05 12:36:03'),
(68, 'DATABASE_ERROR', 'SQLSTATE[42S22]: Column not found: 1054 Unknown column \'avatar_url\' in \'field list\' | SQL: SELECT id, username, avatar_url FROM users WHERE avatar_url IS NOT NULL AND avatar_url != \'\'', 'C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Database.php', 1391, '#0 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Database.php(1391): PDO->prepare(\'SELECT id, user...\')\n#1 C:\\xampp\\htdocs\\techscout.n\\scratch\\check_image_urls.php(5): App\\Core\\Database::query(\'SELECT id, user...\')\n#2 {main}', NULL, NULL, NULL, '127.0.0.1', 0, '2026-09-05 12:50:48'),
(69, 'DATABASE_ERROR', 'SQLSTATE[42S22]: Column not found: 1054 Unknown column \'name\' in \'field list\' | SQL: SELECT id, name, avatar_url FROM users WHERE avatar_url LIKE \'%avatar%\'', 'C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Database.php', 1391, '#0 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Database.php(1391): PDO->prepare(\'SELECT id, name...\')\n#1 C:\\xampp\\htdocs\\techscout.n\\scratch\\find_avatar.php(7): App\\Core\\Database::query(\'SELECT id, name...\')\n#2 {main}', NULL, NULL, NULL, '127.0.0.1', 0, '2026-09-05 13:09:31'),
(70, 'DATABASE_ERROR', 'SQLSTATE[42S22]: Column not found: 1054 Unknown column \'verification_badge\' in \'where clause\' | SQL: SELECT id FROM users WHERE role = \'author\' AND status = \'active\' AND (verification_badge IS NOT NULL AND verification_badge != \'\' AND verification_badge != \'none\')', 'C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Database.php', 1391, '#0 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Database.php(1391): PDO->prepare(\'SELECT id FROM ...\')\n#1 C:\\Users\\Dell i5\\.gemini\\antigravity-ide\\brain\\733a500e-35c8-444e-8e8f-ecbfda2bec84\\scratch\\test_single_notif_and_groups.php(39): App\\Core\\Database::query(\'SELECT id FROM ...\')\n#2 {main}', NULL, NULL, NULL, '127.0.0.1', 0, '2026-09-05 15:19:30'),
(71, 'DATABASE_ERROR', 'SQLSTATE[42S02]: Base table or view not found: 1146 Table \'techscout_db.user_strikes\' doesn\'t exist | SQL: DESCRIBE user_strikes', 'C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Database.php', 1392, '#0 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Database.php(1392): PDOStatement->execute(Array)\n#1 Command line code(1): App\\Core\\Database::query(\'DESCRIBE user_s...\')\n#2 {main}', NULL, NULL, NULL, '127.0.0.1', 0, '2026-09-05 15:19:39'),
(72, 'DATABASE_ERROR', 'SQLSTATE[42S22]: Column not found: 1054 Unknown column \'reply_to_email\' in \'field list\' | SQL: INSERT INTO mail_credentials (name, provider, from_name, from_email, reply_to_email, host, port, encryption, username, is_default, status, created_at, updated_at) \n                   VALUES (\'Gmail SM', 'C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Database.php', 1414, '#0 C:\\xampp\\htdocs\\techscout.n\\app\\Core\\Database.php(1414): PDO->prepare(\'INSERT INTO mai...\')\n#1 C:\\xampp\\htdocs\\techscout.n\\scratch\\test_templates_and_manual_assignment.php(41): App\\Core\\Database::execute(\'INSERT INTO mai...\')\n#2 {main}', NULL, NULL, NULL, '127.0.0.1', 0, '2026-09-05 17:36:16');

--
-- Structure for table `system_settings`
--
DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings` (
  `key_name` varchar(100) NOT NULL,
  `value_content` longtext NOT NULL,
  `setting_group` varchar(50) NOT NULL DEFAULT 'general',
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`key_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure for table `tags`
--
DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `slug` varchar(60) NOT NULL,
  `applicable_to` varchar(50) NOT NULL DEFAULT 'all',
  `usage_count` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_tags_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure for table `ticket_canned_macros`
--
DROP TABLE IF EXISTS `ticket_canned_macros`;
CREATE TABLE `ticket_canned_macros` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(50) NOT NULL DEFAULT 'General',
  `title` varchar(150) NOT NULL,
  `shortcut_code` varchar(50) NOT NULL,
  `body_content` text NOT NULL,
  `usage_count` int(10) unsigned DEFAULT 0,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `shortcut_code` (`shortcut_code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ticket_canned_macros` (4 rows)
--
INSERT INTO `ticket_canned_macros` (`id`, `category`, `title`, `shortcut_code`, `body_content`, `usage_count`, `created_by`, `created_at`) VALUES 
(1, 'General', 'Standard Ticket Resolution', '#resolve', 'Hello,\n\nWe have reviewed your request and resolved the issue. If you need any further assistance, feel free to reopen this ticket.\n\nBest regards,\nTechScout Engineering & Support Team', 0, NULL, '2026-08-30 14:28:34'),
(2, 'Monetization', 'Partner Payout Schedule Notice', '#payout_schedule', 'Hello Creator,\n\nAll verified Partner Monetization royalty payouts are processed every Friday via your default withdrawal method. Please ensure your banking/crypto details are up to date in your Creator Studio.\n\nBest regards,\nTechScout Finance Desk', 0, NULL, '2026-08-30 14:28:34'),
(3, 'Editorial', 'Originality & Plagiarism Notice', '#plagiarism_notice', 'Hello,\n\nDuring editorial review, automated content fingerprinting flagged significant overlapping code/text blocks with existing resources. Please revise your publication with proper attributions and unique analysis before resubmission.\n\nBest regards,\nTechScout Editorial Board', 0, NULL, '2026-08-30 14:28:34'),
(4, 'Security', 'Account Security Recommendations', '#security_steps', 'Hello,\n\nWe recommend enabling Two-Factor Authentication (2FA) and reviewing your Active Sessions under Creator Studio > Security Settings to secure your account.\n\nBest regards,\nTechScout Security Team', 0, NULL, '2026-08-30 14:28:34');

--
-- Structure for table `ticket_internal_notes`
--
DROP TABLE IF EXISTS `ticket_internal_notes`;
CREATE TABLE `ticket_internal_notes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint(20) unsigned NOT NULL,
  `staff_user_id` bigint(20) unsigned NOT NULL,
  `note_content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ticket` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure for table `ticket_messages`
--
DROP TABLE IF EXISTS `ticket_messages`;
CREATE TABLE `ticket_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(10) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `is_staff` tinyint(1) NOT NULL DEFAULT 0,
  `is_internal_note` tinyint(1) NOT NULL DEFAULT 0,
  `message` text NOT NULL,
  `attachment_url` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_tm_ticket` (`ticket_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ticket_messages` (1 rows)
--
INSERT INTO `ticket_messages` (`id`, `ticket_id`, `user_id`, `is_staff`, `is_internal_note`, `message`, `attachment_url`, `created_at`) VALUES 
(1, 1, 8, 0, 0, 'Hello TechScout Support,\n\nWe are experiencing occasional latency spikes when dispatching automated webhooks. Could you verify our rate limits?', NULL, '2026-09-04 21:46:18');

--
-- Structure for table `topics`
--
DROP TABLE IF EXISTS `topics`;
CREATE TABLE `topics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon_name` varchar(50) DEFAULT 'layers',
  `color_hex` varchar(10) DEFAULT '#6366f1',
  `banner_image_url` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `follower_count` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_topics_slug` (`slug`),
  KEY `idx_topics_featured` (`is_featured`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `topics` (8 rows)
--
INSERT INTO `topics` (`id`, `name`, `slug`, `description`, `icon_name`, `color_hex`, `banner_image_url`, `sort_order`, `is_featured`, `follower_count`, `created_at`) VALUES 
(1, 'Artificial Intelligence & ML', 'ai-machine-learning', 'LLMs, Neural Networks, Computer Vision, RAG & Autonomous Tool Calling.', 'cpu', '#6366f1', NULL, 1, 1, 3841, '2026-08-29 15:42:28'),
(2, 'Web Engineering & Frontend', 'web-engineering', 'Modern PHP 8, React 19, TypeScript, WebAssembly, Performance & APIs.', 'code-2', '#06b6d4', NULL, 2, 1, 2922, '2026-08-29 15:42:28'),
(3, 'Cloud, Kubernetes & DevOps', 'cloud-devops', 'Docker, Kubernetes, CI/CD, Terraform, Serverless & Observability.', 'cloud', '#3b82f6', NULL, 3, 1, 2410, '2026-08-29 15:42:28'),
(4, 'Software Architecture & Systems', 'software-architecture', 'System Design, Microservices, High-Throughput Relational & Vector DBs.', 'layers', '#8b5cf6', NULL, 4, 1, 2750, '2026-08-29 15:42:28'),
(5, 'AppSec & Zero-Trust Security', 'security', 'Pen-Testing, OWASP Top 10, Auth Protocols, Cryptography & Fuzzing.', 'shield-check', '#10b981', NULL, 5, 1, 1980, '2026-08-29 15:42:28'),
(6, 'Databases & Data Engineering', 'databases-data', 'MySQL 8, PostgreSQL Vector, Redis Streams, ETL & Real-Time Analytics.', 'database', '#ec4899', NULL, 6, 1, 2150, '2026-08-29 15:42:28'),
(7, 'Developer Tooling & Automation', 'developer-tools', 'CLI tooling, IDE plugins, Prompt engineering blueprints & custom bots.', 'terminal', '#f59e0b', NULL, 7, 1, 1890, '2026-08-29 15:42:28'),
(8, 'Indie Hacking & SaaS Growth', 'indie-hacking', 'Bootstrapping SaaS, Product Discovery, Growth Architecture & Case Studies.', 'rocket', '#f43f5e', NULL, 8, 1, 1640, '2026-08-29 15:42:28');

--
-- Structure for table `user_sessions`
--
DROP TABLE IF EXISTS `user_sessions`;
CREATE TABLE `user_sessions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `session_token_hash` varchar(64) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `device_type` enum('desktop','mobile','tablet','bot','unknown') DEFAULT 'desktop',
  `browser_name` varchar(50) DEFAULT 'Unknown Browser',
  `os_name` varchar(50) DEFAULT 'Unknown OS',
  `location_hint` varchar(100) DEFAULT 'Local Network',
  `is_current` tinyint(1) DEFAULT 0,
  `last_active_at` datetime DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_token_hash` (`session_token_hash`),
  KEY `idx_user_active` (`user_id`,`last_active_at`),
  KEY `idx_token_hash` (`session_token_hash`)
) ENGINE=InnoDB AUTO_INCREMENT=557 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_sessions` (14 rows)
--
INSERT INTO `user_sessions` (`id`, `user_id`, `session_token_hash`, `ip_address`, `user_agent`, `device_type`, `browser_name`, `os_name`, `location_hint`, `is_current`, `last_active_at`, `created_at`) VALUES 
(30, 10, '07d8a04f39f726198730804593bd615ba064540bdbb12d52970666bc3ea3b72d', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'desktop', 'Google Chrome', 'Windows 11/10', 'Local Development Server', 1, '2026-09-04 22:19:23', '2026-09-02 21:57:19'),
(35, 8, '82ccc5318de46b41fff2a8667fd847c09dc0dbabb0ec486bbc09b4d2db2765dd', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'desktop', 'Google Chrome', 'Windows 11/10', 'Local Development Server', 1, '2026-09-02 22:13:57', '2026-09-02 22:13:57'),
(38, 9, '8a821ae660cfacc9434604db4e9c798c0b0d7a893f1a527d49a2ff5c2e8982ac', '127.0.0.1', 'Unknown Client', 'desktop', 'Unknown Browser', 'Unknown OS', 'Local Development Server', 1, '2026-09-02 22:23:29', '2026-09-02 22:23:29'),
(80, 8, '51adebd4c9ccd7f0f4aafbfd22d3818c014ef3c1242e206901bd291c2db295d9', '192.168.1.45', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/122.0.0.0', 'desktop', 'Chrome 122', 'macOS Sonoma', 'San Francisco, US', 1, '2026-09-04 21:46:39', '2026-09-04 21:46:39'),
(240, 11, '067d2b4ad5fff3c534c34a1e648926bd82b21e0f21de4e5a760ca40633948260', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'desktop', 'Google Chrome', 'Windows 11/10', 'Local Development Server', 1, '2026-09-04 22:19:23', '2026-09-04 22:19:23'),
(251, 2, '795301b23a105d4ef34c4dc1eca7a841dccbd3836c61aafde374ccfe0017a5c2', '::1', 'Unknown Client', 'desktop', 'Unknown Browser', 'Unknown OS', 'Local Development Server', 1, '2026-09-04 23:04:30', '2026-09-04 23:04:30'),
(280, 8, '591d825fe92dc91b4b026ee3a53926cabf6d42d6760dabd94771c4f04c98ca1a', '::1', 'Unknown Client', 'desktop', 'Unknown Browser', 'Unknown OS', 'Local Development Server', 1, '2026-09-05 15:40:19', '2026-09-04 23:08:51'),
(298, 2, '9659bd8ee44c8a13f97d3d4908c0514672bc20e89b991fcbb850362a198fb62e', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 [Admin Impersonation]', 'desktop', 'Google Chrome', 'Windows 11/10', 'Local Development Server', 1, '2026-09-05 09:34:48', '2026-09-05 09:34:48'),
(301, 1, '252fcc1057059e269b11644fd4b8192159326e361b5efb0a61ca2449d235f2d9', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'desktop', 'Google Chrome', 'Windows 11/10', 'Local Development Server', 1, '2026-09-05 11:20:34', '2026-09-05 11:20:34'),
(339, 8, '3207967567c92e3b4564df7def1c5fce68f9c5a58f888853ec2fbdc220032e24', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', 'desktop', 'Microsoft Edge', 'Windows 11/10', 'Local Development Server', 1, '2026-09-05 11:33:21', '2026-09-05 11:33:21'),
(406, 2, 'dc84ca855251878d18e3cacafc43a53e83eb1429ea91e0af74baf1267ab2d99b', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0 [Admin Impersonation]', 'desktop', 'Microsoft Edge', 'Windows 11/10', 'Local Development Server', 1, '2026-09-05 11:34:22', '2026-09-05 11:34:22'),
(554, 12, 'c39e1614e6032f7e0f45a781dd7538c84e4c53171776ea79af43640dcde3a1dd', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'desktop', 'Google Chrome', 'Windows 11/10', 'Local Development Server', 1, '2026-09-05 12:13:21', '2026-09-05 12:13:21'),
(555, 1, '1742b460447a780fc4d6b674478f7884157a7534029bf1ff068afa4dcfb1b3b7', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36', 'desktop', 'Google Chrome', 'Windows 11/10', 'Local Development Server', 1, '2026-09-05 12:41:32', '2026-09-05 12:41:32'),
(556, 1, '9e30934ddc8e0f4d9d0fbb7415d1e7a7bab514a77adb2be60fcc2096c0863695', '127.0.0.1', 'Unknown Client', 'desktop', 'Unknown Browser', 'Unknown OS', 'Local Development Server', 1, '2026-09-05 17:57:46', '2026-09-05 17:54:48');

--
-- Structure for table `user_subscriptions`
--
DROP TABLE IF EXISTS `user_subscriptions`;
CREATE TABLE `user_subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `subscribable_type` enum('topic','category','author') NOT NULL,
  `subscribable_id` bigint(20) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_subscription` (`user_id`,`subscribable_type`,`subscribable_id`),
  KEY `idx_user_subs_type_id` (`subscribable_type`,`subscribable_id`),
  CONSTRAINT `fk_user_subs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=212 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_subscriptions` (180 rows)
--
INSERT INTO `user_subscriptions` (`id`, `user_id`, `subscribable_type`, `subscribable_id`, `created_at`) VALUES 
(1, 1, 'topic', 1, '2026-08-17 06:22:31'),
(2, 1, 'topic', 3, '2026-08-15 06:22:31'),
(3, 1, 'topic', 5, '2026-08-24 06:22:31'),
(4, 1, 'topic', 7, '2026-08-18 06:22:31'),
(5, 1, 'topic', 8, '2026-08-20 06:22:31'),
(6, 1, 'category', 9, '2026-08-27 06:22:31'),
(7, 1, 'category', 11, '2026-08-06 06:22:31'),
(8, 1, 'category', 17, '2026-08-22 06:22:31'),
(9, 1, 'author', 2, '2026-08-31 06:22:31'),
(10, 1, 'author', 7, '2026-09-04 06:22:31'),
(11, 1, 'author', 10, '2026-08-23 06:22:31'),
(12, 2, 'topic', 1, '2026-08-14 06:22:31'),
(13, 2, 'topic', 2, '2026-08-30 06:22:31'),
(14, 2, 'category', 1, '2026-08-12 06:22:31'),
(15, 2, 'category', 4, '2026-08-28 06:22:31'),
(16, 2, 'category', 10, '2026-08-21 06:22:31'),
(17, 2, 'category', 16, '2026-08-16 06:22:31'),
(18, 2, 'author', 8, '2026-08-16 06:22:31'),
(19, 2, 'author', 9, '2026-08-27 06:22:31'),
(20, 2, 'author', 11, '2026-08-23 06:22:31'),
(21, 3, 'topic', 2, '2026-08-07 06:22:31'),
(22, 3, 'topic', 3, '2026-08-12 06:22:31'),
(23, 3, 'topic', 5, '2026-09-02 06:22:31'),
(24, 3, 'topic', 7, '2026-08-12 06:22:31'),
(25, 3, 'topic', 8, '2026-09-01 06:22:31'),
(26, 3, 'category', 10, '2026-07-30 06:22:31'),
(27, 3, 'category', 12, '2026-08-04 06:22:31'),
(28, 3, 'author', 3, '2026-09-01 06:22:31'),
(29, 4, 'topic', 1, '2026-08-30 06:22:31'),
(30, 4, 'topic', 4, '2026-08-14 06:22:31'),
(31, 4, 'topic', 5, '2026-08-30 06:22:31'),
(32, 4, 'topic', 7, '2026-08-06 06:22:31'),
(33, 4, 'topic', 8, '2026-08-12 06:22:31'),
(34, 4, 'category', 1, '2026-07-31 06:22:31'),
(35, 4, 'category', 2, '2026-08-25 06:22:31'),
(36, 4, 'author', 2, '2026-09-04 06:22:31'),
(37, 4, 'author', 9, '2026-08-20 06:22:31'),
(38, 5, 'topic', 1, '2026-08-29 06:22:31'),
(39, 5, 'topic', 3, '2026-08-08 06:22:31'),
(40, 5, 'topic', 4, '2026-08-24 06:22:31'),
(41, 5, 'topic', 5, '2026-08-22 06:22:31'),
(42, 5, 'topic', 6, '2026-08-06 06:22:31'),
(43, 5, 'category', 2, '2026-07-29 06:22:31'),
(44, 5, 'category', 4, '2026-08-30 06:22:31'),
(45, 5, 'category', 16, '2026-08-24 06:22:31'),
(46, 5, 'author', 3, '2026-08-30 06:22:31'),
(47, 5, 'author', 10, '2026-08-29 06:22:31'),
(48, 6, 'topic', 1, '2026-08-23 06:22:31'),
(49, 6, 'topic', 3, '2026-08-23 06:22:31'),
(50, 6, 'topic', 5, '2026-09-02 06:22:31');
INSERT INTO `user_subscriptions` (`id`, `user_id`, `subscribable_type`, `subscribable_id`, `created_at`) VALUES 
(51, 6, 'topic', 6, '2026-08-06 06:22:31'),
(52, 6, 'topic', 8, '2026-08-23 06:22:31'),
(53, 6, 'category', 1, '2026-07-29 06:22:31'),
(54, 6, 'category', 4, '2026-09-03 06:22:31'),
(55, 6, 'category', 18, '2026-08-14 06:22:31'),
(56, 6, 'author', 1, '2026-08-27 06:22:31'),
(57, 6, 'author', 7, '2026-09-04 06:22:31'),
(58, 7, 'topic', 2, '2026-08-10 06:22:31'),
(59, 7, 'topic', 5, '2026-08-17 06:22:31'),
(60, 7, 'category', 1, '2026-08-15 06:22:31'),
(61, 7, 'category', 11, '2026-08-29 06:22:31'),
(62, 7, 'category', 14, '2026-08-19 06:22:31'),
(63, 7, 'category', 16, '2026-07-30 06:22:31'),
(64, 7, 'author', 1, '2026-08-31 06:22:31'),
(65, 7, 'author', 6, '2026-09-04 06:22:31'),
(66, 8, 'topic', 2, '2026-08-19 06:22:31'),
(67, 8, 'topic', 3, '2026-08-27 06:22:31'),
(68, 8, 'topic', 6, '2026-09-01 06:22:31'),
(69, 8, 'category', 15, '2026-08-02 06:22:31'),
(70, 8, 'category', 18, '2026-08-03 06:22:31'),
(71, 8, 'author', 5, '2026-08-25 06:22:31'),
(72, 8, 'author', 10, '2026-09-01 06:22:31'),
(73, 9, 'topic', 4, '2026-08-20 06:22:31'),
(74, 9, 'topic', 7, '2026-08-29 06:22:31'),
(75, 9, 'category', 1, '2026-08-17 06:22:31'),
(76, 9, 'category', 2, '2026-08-31 06:22:31'),
(77, 9, 'category', 12, '2026-08-28 06:22:31'),
(78, 9, 'category', 13, '2026-08-03 06:22:31'),
(79, 9, 'author', 10, '2026-08-22 06:22:31'),
(80, 10, 'topic', 2, '2026-08-28 06:22:31'),
(81, 10, 'topic', 4, '2026-08-27 06:22:31'),
(82, 10, 'topic', 5, '2026-08-23 06:22:31'),
(83, 10, 'topic', 6, '2026-08-27 06:22:31'),
(84, 10, 'topic', 7, '2026-08-24 06:22:31'),
(85, 10, 'category', 2, '2026-08-11 06:22:31'),
(86, 10, 'category', 7, '2026-08-02 06:22:31'),
(87, 10, 'category', 19, '2026-09-01 06:22:31'),
(88, 10, 'author', 5, '2026-08-24 06:22:31'),
(89, 10, 'author', 10, '2026-08-29 06:22:31'),
(90, 11, 'topic', 4, '2026-08-09 06:22:31'),
(91, 11, 'topic', 7, '2026-08-09 06:22:31'),
(92, 11, 'topic', 8, '2026-08-14 06:22:31'),
(93, 11, 'category', 3, '2026-08-14 06:22:31'),
(94, 11, 'category', 15, '2026-09-04 06:22:31'),
(95, 11, 'category', 16, '2026-08-26 06:22:31'),
(96, 11, 'category', 18, '2026-09-03 06:22:31'),
(97, 11, 'author', 3, '2026-08-31 06:22:31'),
(98, 11, 'author', 6, '2026-08-31 06:22:31'),
(99, 11, 'author', 7, '2026-08-19 06:22:31'),
(100, 12, 'topic', 4, '2026-08-23 06:22:31');
INSERT INTO `user_subscriptions` (`id`, `user_id`, `subscribable_type`, `subscribable_id`, `created_at`) VALUES 
(101, 12, 'topic', 5, '2026-09-03 06:22:31'),
(102, 12, 'topic', 7, '2026-08-06 06:22:31'),
(103, 12, 'topic', 8, '2026-08-14 06:22:31'),
(104, 12, 'category', 3, '2026-08-08 06:22:31'),
(105, 12, 'category', 5, '2026-08-09 06:22:31'),
(106, 12, 'author', 3, '2026-08-17 06:22:31'),
(107, 12, 'author', 5, '2026-08-23 06:22:31'),
(108, 1, 'topic', 2, '2026-08-21 06:22:53'),
(110, 1, 'topic', 4, '2026-09-02 06:22:53'),
(113, 1, 'category', 18, '2026-08-16 06:22:53'),
(115, 1, 'author', 9, '2026-08-20 06:22:53'),
(116, 1, 'author', 11, '2026-08-26 06:22:53'),
(119, 2, 'topic', 3, '2026-08-28 06:22:53'),
(121, 2, 'category', 5, '2026-08-14 06:22:53'),
(122, 2, 'category', 7, '2026-08-03 06:22:53'),
(124, 2, 'author', 4, '2026-08-29 06:22:53'),
(128, 3, 'topic', 6, '2026-08-24 06:22:53'),
(131, 3, 'category', 9, '2026-08-14 06:22:53'),
(132, 3, 'category', 15, '2026-08-05 06:22:53'),
(133, 3, 'author', 11, '2026-08-16 06:22:53'),
(135, 4, 'topic', 6, '2026-08-10 06:22:53'),
(137, 4, 'category', 17, '2026-08-29 06:22:53'),
(138, 4, 'category', 18, '2026-08-21 06:22:53'),
(139, 4, 'author', 1, '2026-08-26 06:22:53'),
(140, 4, 'author', 6, '2026-08-19 06:22:53'),
(141, 5, 'topic', 2, '2026-08-06 06:22:53'),
(142, 5, 'topic', 7, '2026-08-17 06:22:53'),
(143, 5, 'topic', 8, '2026-08-09 06:22:53'),
(144, 5, 'category', 1, '2026-08-29 06:22:53'),
(145, 5, 'category', 8, '2026-08-27 06:22:53'),
(146, 5, 'author', 8, '2026-08-28 06:22:53'),
(147, 6, 'topic', 2, '2026-08-27 06:22:53'),
(148, 6, 'topic', 4, '2026-08-14 06:22:53'),
(151, 6, 'category', 7, '2026-07-30 06:22:53'),
(152, 6, 'category', 10, '2026-08-03 06:22:53'),
(153, 6, 'category', 17, '2026-08-11 06:22:53'),
(154, 6, 'author', 2, '2026-09-03 06:22:53'),
(155, 6, 'author', 4, '2026-08-23 06:22:53'),
(156, 6, 'author', 5, '2026-08-19 06:22:53'),
(157, 7, 'topic', 1, '2026-08-23 06:22:53'),
(158, 7, 'topic', 4, '2026-09-03 06:22:53'),
(159, 7, 'topic', 6, '2026-08-28 06:22:53'),
(160, 7, 'topic', 7, '2026-08-23 06:22:53'),
(161, 7, 'category', 3, '2026-08-27 06:22:53'),
(162, 7, 'category', 6, '2026-08-21 06:22:53'),
(163, 7, 'category', 8, '2026-08-24 06:22:53'),
(164, 7, 'author', 2, '2026-08-28 06:22:53'),
(165, 7, 'author', 4, '2026-08-21 06:22:53'),
(167, 8, 'topic', 1, '2026-08-06 06:22:53'),
(171, 8, 'topic', 7, '2026-08-06 06:22:53');
INSERT INTO `user_subscriptions` (`id`, `user_id`, `subscribable_type`, `subscribable_id`, `created_at`) VALUES 
(173, 8, 'category', 19, '2026-08-14 06:22:53'),
(174, 8, 'author', 2, '2026-08-26 06:22:53'),
(175, 8, 'author', 7, '2026-08-22 06:22:53'),
(176, 9, 'topic', 1, '2026-08-19 06:22:53'),
(177, 9, 'topic', 3, '2026-09-02 06:22:53'),
(182, 9, 'category', 15, '2026-08-16 06:22:53'),
(183, 9, 'category', 17, '2026-08-10 06:22:53'),
(184, 9, 'author', 1, '2026-08-22 06:22:53'),
(185, 9, 'author', 3, '2026-08-26 06:22:53'),
(186, 9, 'author', 9, '2026-08-28 06:22:53'),
(187, 10, 'topic', 3, '2026-08-18 06:22:53'),
(189, 10, 'category', 1, '2026-07-28 06:22:53'),
(190, 10, 'category', 9, '2026-08-16 06:22:53'),
(191, 10, 'category', 10, '2026-09-04 06:22:53'),
(192, 10, 'category', 13, '2026-08-13 06:22:53'),
(193, 10, 'author', 1, '2026-09-01 06:22:53'),
(194, 10, 'author', 6, '2026-08-16 06:22:53'),
(195, 11, 'topic', 1, '2026-08-09 06:22:53'),
(196, 11, 'topic', 6, '2026-08-09 06:22:53'),
(198, 11, 'category', 2, '2026-08-04 06:22:53'),
(199, 11, 'category', 8, '2026-08-05 06:22:53'),
(200, 11, 'category', 9, '2026-08-25 06:22:53'),
(201, 11, 'category', 19, '2026-08-20 06:22:53'),
(202, 11, 'author', 5, '2026-08-17 06:22:53'),
(205, 12, 'topic', 6, '2026-08-22 06:22:53'),
(207, 12, 'category', 4, '2026-08-01 06:22:53'),
(208, 12, 'category', 7, '2026-08-09 06:22:53'),
(209, 12, 'category', 15, '2026-08-07 06:22:53'),
(210, 12, 'category', 16, '2026-08-14 06:22:53'),
(211, 12, 'author', 1, '2026-08-20 06:22:53');

--
-- Structure for table `users`
--
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `username` varchar(60) NOT NULL,
  `role` enum('guest','member','author','editor','moderator','advertiser','admin','super_admin') NOT NULL DEFAULT 'member',
  `admin_role_id` int(10) unsigned DEFAULT NULL,
  `status` enum('unverified','active','suspended','banned','deleted') NOT NULL DEFAULT 'active',
  `suspended_until` datetime DEFAULT NULL,
  `email_verified_at` datetime DEFAULT NULL,
  `email_verify_token` varchar(100) DEFAULT NULL,
  `password_reset_token` varchar(100) DEFAULT NULL,
  `password_reset_expires_at` datetime DEFAULT NULL,
  `two_factor_secret` varchar(255) DEFAULT NULL,
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `strike_count` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `is_flagged_suspicious` tinyint(1) NOT NULL DEFAULT 0,
  `suspicious_reason` varchar(255) DEFAULT NULL,
  `flagged_at` datetime DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`),
  KEY `idx_users_role_status` (`role`,`status`),
  KEY `idx_users_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users` (12 rows)
--
INSERT INTO `users` (`id`, `uuid`, `email`, `password_hash`, `username`, `role`, `admin_role_id`, `status`, `suspended_until`, `email_verified_at`, `email_verify_token`, `password_reset_token`, `password_reset_expires_at`, `two_factor_secret`, `two_factor_enabled`, `strike_count`, `is_flagged_suspicious`, `suspicious_reason`, `flagged_at`, `last_login_at`, `last_login_ip`, `created_at`, `updated_at`) VALUES 
(1, 'a1b2c3d4-e5f6-7a8b-9c0d-1e2f3a4b5c6d', 'admin@techscout.online', '$2y$10$wEeVq045eW9hU71WbK1Y9OHJ5z6nU2C/tZqIuL.VqS5l7eQ5T4.kO', 'superadmin', 'super_admin', NULL, 'active', NULL, '2026-08-27 20:39:38', NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, '2026-09-04 21:55:22', '::1', '2026-08-27 20:39:38', '2026-09-04 21:55:22'),
(2, 'b2c3d4e5-f6a7-8b9c-0d1e-2f3a4b5c6d7e', 'alex@techscout.online', '$2y$10$wEeVq045eW9hU71WbK1Y9OHJ5z6nU2C/tZqIuL.VqS5l7eQ5T4.kO', 'alexvance', 'author', NULL, 'active', NULL, '2026-08-27 20:39:38', NULL, '051883ff9fe5cefcefd083bd1a99f4947c76a78f42c5256a5d28ae8f53fb3497', '2026-09-05 21:23:02', NULL, 0, 0, 0, NULL, NULL, '2026-08-29 20:08:12', '::1', '2026-08-27 20:39:38', '2026-09-05 09:34:40'),
(3, 'c3d4e5f6-a7b8-9c0d-1e2f-3a4b5c6d7e8f', 'sarah@techscout.online', '$2y$10$wEeVq045eW9hU71WbK1Y9OHJ5z6nU2C/tZqIuL.VqS5l7eQ5T4.kO', 'sarah_dev', 'author', NULL, 'active', NULL, '2026-08-27 20:39:38', NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, '2026-08-27 20:39:38', '2026-08-27 20:39:38'),
(4, 'd4e5f6a7-b8c9-0d1e-2f3a-4b5c6d7e8f9a', 'david@techscout.online', '$2y$10$wEeVq045eW9hU71WbK1Y9OHJ5z6nU2C/tZqIuL.VqS5l7eQ5T4.kO', 'david_chen', 'author', NULL, 'active', NULL, '2026-08-27 20:39:38', NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, '2026-08-27 20:39:38', '2026-08-27 20:39:38'),
(5, 'e5f6a7b8-c9d0-1e2f-3a4b-5c6d7e8f9a0b', 'elena@techscout.online', '$2y$10$wEeVq045eW9hU71WbK1Y9OHJ5z6nU2C/tZqIuL.VqS5l7eQ5T4.kO', 'elena_ai', 'author', NULL, 'active', NULL, '2026-08-27 20:39:38', NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, '2026-08-27 20:39:38', '2026-08-27 20:39:38'),
(6, 'f6a7b8c9-d0e1-2f3a-4b5c-6d7e8f9a0b1c', 'marcus@techscout.online', '$2y$10$wEeVq045eW9hU71WbK1Y9OHJ5z6nU2C/tZqIuL.VqS5l7eQ5T4.kO', 'marcus_db', 'author', NULL, 'active', NULL, '2026-08-27 20:39:38', NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, '2026-08-27 20:39:38', '2026-08-27 20:39:38'),
(7, 'a7b8c9d0-e1f2-3a4b-5c6d-7e8f9a0b1c2d', 'maya@techscout.online', '$2y$10$wEeVq045eW9hU71WbK1Y9OHJ5z6nU2C/tZqIuL.VqS5l7eQ5T4.kO', 'maya_sec', 'author', NULL, 'active', NULL, '2026-08-27 20:39:38', NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, '2026-08-27 20:39:38', '2026-08-27 20:39:38'),
(8, 'b8c9d0e1-f2a3-4b5c-6d7e-8f9a0b1c2d3e', 'kai@techscout.online', '$2y$10$g1bFd6dGL/5ARdkAiLqbVOtQTGVp.zv4bFnrInZP0o4K5tYZX6miq', 'kai_systems', 'member', NULL, 'active', NULL, '2026-08-27 21:16:39', NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, '2026-09-02 22:13:57', '::1', '2026-08-27 21:16:39', '2026-09-02 22:13:57'),
(9, 'c9d0e1f2-a3b4-5c6d-7e8f-9a0b1c2d3e4f', 'rachel@techscout.online', '$2y$10$wEeVq045eW9hU71WbK1Y9OHJ5z6nU2C/tZqIuL.VqS5l7eQ5T4.kO', 'rachel_k8s', 'member', NULL, 'active', NULL, '2026-08-27 21:16:39', NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, '2026-09-02 22:10:15', '::1', '2026-08-27 21:16:39', '2026-09-02 22:18:44'),
(10, 'd0e1f2a3-b4c5-6d7e-8f9a-0b1c2d3e4f5a', 'sam@techscout.online', '$2y$10$wEeVq045eW9hU71WbK1Y9OHJ5z6nU2C/tZqIuL.VqS5l7eQ5T4.kO', 'sam_llm', 'author', NULL, 'active', NULL, '2026-08-27 21:16:39', NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, '2026-09-02 21:57:19', '::1', '2026-08-27 21:16:39', '2026-09-02 21:57:19'),
(11, '8f9954d347d97a59e08feacea3e2c453', 'asdf@asdf.asdf', '$2y$10$H0P2DA0sb0DIQaeD3IpN5OhPMTL6T6HquX5jcw/W1qxcmrMVfCnPK', 'asdf', 'member', NULL, 'active', NULL, '2026-09-02 23:27:25', NULL, 'bf6a3e16ccd70fc57d565edd392d596cf81de9218965f07c', '2026-09-05 14:06:17', NULL, 0, 0, 0, NULL, NULL, '2026-09-04 22:19:23', '::1', '2026-09-02 23:27:25', '2026-09-05 12:06:17'),
(12, '', 'src_1788538666_252@example.com', 'test', 'merge_src_1788538666_252', 'member', NULL, 'active', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, '2026-09-04 21:17:46', '2026-09-05 12:13:21');

--
-- Structure for table `webhook_deliveries`
--
DROP TABLE IF EXISTS `webhook_deliveries`;
CREATE TABLE `webhook_deliveries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `webhook_id` int(10) unsigned NOT NULL,
  `event` varchar(60) NOT NULL,
  `payload_json` longtext NOT NULL,
  `response_code` int(11) DEFAULT NULL,
  `response_body` text DEFAULT NULL,
  `status` enum('success','failed') NOT NULL DEFAULT 'success',
  `duration_ms` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_wh_id` (`webhook_id`),
  KEY `idx_wh_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure for table `webhooks`
--
DROP TABLE IF EXISTS `webhooks`;
CREATE TABLE `webhooks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `target_url` varchar(255) NOT NULL,
  `secret_key` varchar(64) NOT NULL,
  `events_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`events_json`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


SET FOREIGN_KEY_CHECKS = 1;
-- Snapshot completed successfully.
