<?php
namespace App\Core;

use PDO;
use PDOException;
use Throwable;

class Database {
    private static ?PDO $instance = null;
    private static bool $initialized = false;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $host = Config::get('db_host', '127.0.0.1');
            $port = Config::get('db_port', '3306');
            $dbname = Config::get('db_name', 'techscout_db');
            $user = Config::get('db_user', 'root');
            $pass = Config::get('db_pass', '');

            // Handle manual repair trigger if passed
            if (isset($_GET['db_action']) && $_GET['db_action'] === 'repair') {
                self::forceResetAndSeed($host, $port, $dbname, $user, $pass);
            }

            try {
                // First connect to target DB directly
                $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
                try {
                    self::$instance = new PDO($dsn, $user, $pass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]);
                } catch (Throwable $dbEx) {
                    // If target DB doesn't exist and on localhost/server with root privileges, attempt CREATE DATABASE
                    $dsnServer = "mysql:host={$host};port={$port};charset=utf8mb4";
                    $pdoServer = new PDO($dsnServer, $user, $pass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]);
                    $pdoServer->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

                    self::$instance = new PDO($dsn, $user, $pass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]);
                }

                // Force session timezone to Pakistan Standard Time (UTC+5) so MySQL NOW() and CURRENT_TIMESTAMP match server/app time
                try {
                    self::$instance->exec("SET time_zone = '+05:00'");
                } catch (Throwable $tzEx) {}

                // Auto-initialize schema & seed data if tables are missing
                if (!self::$initialized) {
                    self::ensureSchemaAndSeeds();
                    self::$initialized = true;
                }
            } catch (Throwable $e) {
                self::renderDetailedError($e, $host, $port, $dbname, $user);
            }
        }
        return self::$instance;
    }

    private static function forceResetAndSeed(string $host, string $port, string $dbname, string $user, string $pass): void {
        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            foreach ($tables as $t) {
                $pdo->exec("DROP TABLE IF EXISTS `{$t}`");
            }

            $schemaFile = __DIR__ . '/../../database/schema.sql';
            $seedsFile = __DIR__ . '/../../database/seeds.sql';

            if (file_exists($schemaFile)) {
                $pdo->exec(file_get_contents($schemaFile));
            }
            if (file_exists($seedsFile)) {
                $pdo->exec(file_get_contents($seedsFile));
            }
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

            header("Location: " . Config::url('/'));
            exit;
        } catch (Throwable $e) {
            self::renderDetailedError($e, $host, $port, $dbname, $user);
        }
    }

    private static function ensureSchemaAndSeeds(): void {
        $pdo = self::$instance;
        $check = $pdo->query("SHOW TABLES LIKE 'contents'")->fetchAll();
        if (empty($check)) {
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
            $schemaFile = __DIR__ . '/../../database/schema.sql';
            $seedsFile = __DIR__ . '/../../database/seeds.sql';

            if (file_exists($schemaFile)) {
                $schemaSql = file_get_contents($schemaFile);
                $pdo->exec($schemaSql);
            }
            if (file_exists($seedsFile)) {
                $seedsSql = file_get_contents($seedsFile);
                $pdo->exec($seedsSql);
            }
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        } else {
            // Live incremental migrations
            self::runIncrementalMigrations($pdo);
        }
    }

    private static function runIncrementalMigrations(PDO $pdo): void {
        try {
            // 1. Check topics table
            $hasTopics = $pdo->query("SHOW TABLES LIKE 'topics'")->fetchAll();
            if (empty($hasTopics)) {
                $pdo->exec("CREATE TABLE IF NOT EXISTS `topics` (
                  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                  `name` VARCHAR(100) NOT NULL,
                  `slug` VARCHAR(100) NOT NULL UNIQUE,
                  `description` TEXT NULL,
                  `icon_name` VARCHAR(50) NULL DEFAULT 'layers',
                  `color_hex` VARCHAR(10) NULL DEFAULT '#6366f1',
                  `banner_image_url` VARCHAR(255) NULL,
                  `sort_order` INT NOT NULL DEFAULT 0,
                  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
                  `follower_count` INT UNSIGNED NOT NULL DEFAULT 0,
                  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  INDEX `idx_topics_slug` (`slug`),
                  INDEX `idx_topics_featured` (`is_featured`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // Seed default topics
                $pdo->exec("INSERT IGNORE INTO `topics` (`id`, `name`, `slug`, `description`, `icon_name`, `color_hex`, `sort_order`, `is_featured`, `follower_count`) VALUES
                (1, 'Artificial Intelligence & ML', 'ai-machine-learning', 'LLMs, Neural Networks, Computer Vision, RAG & Autonomous Tool Calling.', 'cpu', '#6366f1', 1, 1, 3840),
                (2, 'Web Engineering & Frontend', 'web-engineering', 'Modern PHP 8, React 19, TypeScript, WebAssembly, Performance & APIs.', 'code-2', '#06b6d4', 2, 1, 2920),
                (3, 'Cloud, Kubernetes & DevOps', 'cloud-devops', 'Docker, Kubernetes, CI/CD, Terraform, Serverless & Observability.', 'cloud', '#3b82f6', 3, 1, 2410),
                (4, 'Software Architecture & Systems', 'software-architecture', 'System Design, Microservices, High-Throughput Relational & Vector DBs.', 'layers', '#8b5cf6', 4, 1, 2750),
                (5, 'AppSec & Zero-Trust Security', 'security', 'Pen-Testing, OWASP Top 10, Auth Protocols, Cryptography & Fuzzing.', 'shield-check', '#10b981', 5, 1, 1980),
                (6, 'Databases & Data Engineering', 'databases-data', 'MySQL 8, PostgreSQL Vector, Redis Streams, ETL & Real-Time Analytics.', 'database', '#ec4899', 6, 1, 2150),
                (7, 'Developer Tooling & Automation', 'developer-tools', 'CLI tooling, IDE plugins, Prompt engineering blueprints & custom bots.', 'terminal', '#f59e0b', 7, 1, 1890),
                (8, 'Indie Hacking & SaaS Growth', 'indie-hacking', 'Bootstrapping SaaS, Product Discovery, Growth Architecture & Case Studies.', 'rocket', '#f43f5e', 8, 1, 1640)");
            }

            // 2. Check topic_id column in categories
            $colCheck = $pdo->query("SHOW COLUMNS FROM `categories` LIKE 'topic_id'")->fetchAll();
            if (empty($colCheck)) {
                $pdo->exec("ALTER TABLE `categories` ADD COLUMN `topic_id` INT UNSIGNED NULL AFTER `parent_id`");
                $pdo->exec("UPDATE `categories` SET `topic_id` = 1 WHERE id IN (1, 7, 10, 12, 16, 18, 19)");
                $pdo->exec("UPDATE `categories` SET `topic_id` = 2 WHERE id IN (2, 11, 14, 17)");
                $pdo->exec("UPDATE `categories` SET `topic_id` = 3 WHERE id IN (3)");
                $pdo->exec("UPDATE `categories` SET `topic_id` = 4 WHERE id IN (4, 8, 13)");
                $pdo->exec("UPDATE `categories` SET `topic_id` = 5 WHERE id IN (5, 9, 15)");
                $pdo->exec("UPDATE `categories` SET `topic_id` = 8 WHERE id IN (6)");
            }

            // 3. Check user_subscriptions table
            $hasSubs = $pdo->query("SHOW TABLES LIKE 'user_subscriptions'")->fetchAll();
            if (empty($hasSubs)) {
                $pdo->exec("CREATE TABLE IF NOT EXISTS `user_subscriptions` (
                  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                  `user_id` BIGINT UNSIGNED NOT NULL,
                  `subscribable_type` ENUM('topic', 'category', 'author') NOT NULL,
                  `subscribable_id` BIGINT UNSIGNED NOT NULL,
                  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `uk_user_subscription` (`user_id`, `subscribable_type`, `subscribable_id`),
                  CONSTRAINT `fk_user_subs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
                  INDEX `idx_user_subs_type_id` (`subscribable_type`, `subscribable_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            }

            // 4. Check author_applications extra columns
            $appCols = $pdo->query("SHOW COLUMNS FROM `author_applications` LIKE 'sample_file_url'")->fetchAll();
            if (empty($appCols)) {
                $pdo->exec("ALTER TABLE `author_applications` ADD COLUMN `topic_interests` JSON NULL AFTER `specializations`, ADD COLUMN `sample_file_url` VARCHAR(255) NULL AFTER `sample_pitch`, ADD COLUMN `requested_credits` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `requested_tools`");
            }

            // 5. Check author template permissions
            $authCols = $pdo->query("SHOW COLUMNS FROM `authors` LIKE 'can_use_templates'")->fetchAll();
            if (empty($authCols)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `can_use_templates` TINYINT(1) NOT NULL DEFAULT 0 AFTER `can_publish_tools`");
            }

            // 6. Check site_settings table
            $hasSettings = $pdo->query("SHOW TABLES LIKE 'site_settings'")->fetchAll();
            if (empty($hasSettings)) {
                $pdo->exec("CREATE TABLE IF NOT EXISTS `site_settings` (
                  `key_name` VARCHAR(100) NOT NULL PRIMARY KEY,
                  `key_value` TEXT NULL,
                  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // Seed defaults
                $pdo->exec("INSERT IGNORE INTO `site_settings` (`key_name`, `key_value`) VALUES
                ('social_twitter', 'https://twitter.com/techscout'),
                ('social_github', 'https://github.com/techscout'),
                ('social_linkedin', 'https://linkedin.com/company/techscout'),
                ('social_youtube', 'https://youtube.com/@techscout'),
                ('social_facebook', 'https://facebook.com/techscout'),
                ('social_discord', 'https://discord.gg/techscout'),
                ('social_rss', 'https://techscout.n/feed.xml'),
                ('enable_jobs_section', '0'),
                ('allow_all_authors_templates', '0')");
            }

            // 7. Check jobs table
            $hasJobs = $pdo->query("SHOW TABLES LIKE 'jobs'")->fetchAll();
            if (empty($hasJobs)) {
                $pdo->exec("CREATE TABLE IF NOT EXISTS `jobs` (
                  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                  `title` VARCHAR(200) NOT NULL,
                  `slug` VARCHAR(200) NOT NULL UNIQUE,
                  `company_name` VARCHAR(150) NOT NULL,
                  `company_logo_url` VARCHAR(255) NULL,
                  `location` VARCHAR(100) NOT NULL DEFAULT 'Remote',
                  `job_type` ENUM('Full-time', 'Contract', 'Remote', 'Part-time') NOT NULL DEFAULT 'Remote',
                  `salary_range` VARCHAR(100) NULL,
                  `apply_url` VARCHAR(255) NULL,
                  `description_html` LONGTEXT NOT NULL,
                  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
                  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  INDEX `idx_jobs_active` (`is_active`),
                  INDEX `idx_jobs_featured` (`is_featured`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // Seed initial jobs for TechScout organization
                $pdo->exec("INSERT IGNORE INTO `jobs` (`id`, `title`, `slug`, `company_name`, `location`, `job_type`, `salary_range`, `description_html`, `is_featured`, `is_active`) VALUES
                (1, 'Lead AI Architecture Scout', 'lead-ai-architecture-scout', 'TechScout Organization', 'Islamabad / Remote (Worldwide)', 'Full-time', '$140,000 - $185,000 + Equity', '<p>We are seeking a Lead AI Architecture Scout to evaluate frontier LLM papers, compile benchmarks, and build interactive tooling.</p>', 1, 1),
                (2, 'Senior High-Throughput Systems Engineer', 'senior-systems-engineer', 'TechScout Organization', 'Remote (PKT / UTC+5 friendly)', 'Full-time', '$120,000 - $160,000', '<p>Scale our distributed MySQL read replicas, low-latency API caching layer, and background indexing queue.</p>', 1, 1)");
            }

            // 8. Check job_applications table (On-site application submission)
            $hasJobApps = $pdo->query("SHOW TABLES LIKE 'job_applications'")->fetchAll();
            if (empty($hasJobApps)) {
                $pdo->exec("CREATE TABLE IF NOT EXISTS `job_applications` (
                  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                  `job_id` INT UNSIGNED NOT NULL,
                  `user_id` INT UNSIGNED NULL,
                  `applicant_name` VARCHAR(150) NOT NULL,
                  `applicant_email` VARCHAR(191) NOT NULL,
                  `portfolio_url` VARCHAR(255) NULL,
                  `resume_text_or_url` TEXT NOT NULL,
                  `cover_letter` TEXT NULL,
                  `status` ENUM('pending', 'reviewed', 'interviewing', 'accepted', 'rejected') NOT NULL DEFAULT 'pending',
                  `admin_notes` TEXT NULL,
                  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  INDEX `idx_job_app_job` (`job_id`),
                  INDEX `idx_job_app_status` (`status`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            }

            // 9. Check contact_messages table (Admin replyable)
            $hasContact = $pdo->query("SHOW TABLES LIKE 'contact_messages'")->fetchAll();
            if (empty($hasContact)) {
                $pdo->exec("CREATE TABLE IF NOT EXISTS `contact_messages` (
                  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                  `name` VARCHAR(150) NOT NULL,
                  `email` VARCHAR(191) NOT NULL,
                  `subject` VARCHAR(255) NOT NULL,
                  `message` TEXT NOT NULL,
                  `status` ENUM('unread', 'read', 'replied') NOT NULL DEFAULT 'unread',
                  `reply_text` TEXT NULL,
                  `replied_at` DATETIME NULL,
                  `replied_by` INT UNSIGNED NULL,
                  `ip_address` VARCHAR(45) NULL,
                  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  INDEX `idx_contact_status` (`status`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            }

            // 10. Check tags and content_tags tables
            $hasTags = $pdo->query("SHOW TABLES LIKE 'tags'")->fetchAll();
            if (empty($hasTags)) {
                $pdo->exec("CREATE TABLE IF NOT EXISTS `tags` (
                  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                  `name` VARCHAR(100) NOT NULL UNIQUE,
                  `slug` VARCHAR(120) NOT NULL UNIQUE,
                  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  INDEX `idx_tag_slug` (`slug`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                $pdo->exec("CREATE TABLE IF NOT EXISTS `content_tags` (
                  `content_id` INT UNSIGNED NOT NULL,
                  `tag_id` INT UNSIGNED NOT NULL,
                  PRIMARY KEY (`content_id`, `tag_id`),
                  INDEX `idx_ct_tag` (`tag_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // Seed some foundational developer tags
                $pdo->exec("INSERT IGNORE INTO `tags` (`name`, `slug`) VALUES
                ('Distributed Systems', 'distributed-systems'),
                ('AI Agents', 'ai-agents'),
                ('LLM Architecture', 'llm-architecture'),
                ('Database Optimization', 'database-optimization'),
                ('Cloud Infrastructure', 'cloud-infrastructure'),
                ('Python', 'python'),
                ('TypeScript', 'typescript'),
                ('Performance', 'performance'),
                ('Low Latency', 'low-latency'),
                ('Production Engineering', 'production-engineering')");
            }

            // 11. Check notifications table
            $hasNotifs = $pdo->query("SHOW TABLES LIKE 'notifications'")->fetchAll();
            if (empty($hasNotifs)) {
                $pdo->exec("CREATE TABLE IF NOT EXISTS `notifications` (
                  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                  `user_id` BIGINT UNSIGNED NOT NULL,
                  `type` VARCHAR(50) NOT NULL DEFAULT 'system',
                  `title` VARCHAR(255) NOT NULL,
                  `message` TEXT NOT NULL,
                  `action_url` VARCHAR(255) NULL,
                  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
                  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  INDEX `idx_notif_user` (`user_id`, `is_read`, `created_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            } else {
                // Ensure columns exist on pre-existing notifications table
                $notifCols = array_column($pdo->query("SHOW COLUMNS FROM `notifications`")->fetchAll(PDO::FETCH_ASSOC), 'Field');
                if (!in_array('type', $notifCols)) {
                    $pdo->exec("ALTER TABLE `notifications` ADD COLUMN `type` VARCHAR(50) NOT NULL DEFAULT 'system' AFTER `user_id`");
                }
                if (!in_array('title', $notifCols)) {
                    $pdo->exec("ALTER TABLE `notifications` ADD COLUMN `title` VARCHAR(255) NOT NULL AFTER `type`");
                }
                if (!in_array('message', $notifCols)) {
                    $pdo->exec("ALTER TABLE `notifications` ADD COLUMN `message` TEXT NOT NULL AFTER `title`");
                }
                if (!in_array('action_url', $notifCols)) {
                    $pdo->exec("ALTER TABLE `notifications` ADD COLUMN `action_url` VARCHAR(255) NULL AFTER `message`");
                }
                if (!in_array('is_read', $notifCols)) {
                    $pdo->exec("ALTER TABLE `notifications` ADD COLUMN `is_read` TINYINT(1) NOT NULL DEFAULT 0 AFTER `action_url`");
                }
            }

            // 12. Check support_tickets and ticket_messages tables
            $hasTickets = $pdo->query("SHOW TABLES LIKE 'support_tickets'")->fetchAll();
            if (empty($hasTickets)) {
                $pdo->exec("CREATE TABLE IF NOT EXISTS `support_tickets` (
                  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                  `ticket_number` VARCHAR(30) NOT NULL UNIQUE,
                  `user_id` BIGINT UNSIGNED NOT NULL,
                  `category` VARCHAR(50) NOT NULL DEFAULT 'general',
                  `priority` ENUM('low', 'medium', 'high', 'urgent') NOT NULL DEFAULT 'medium',
                  `subject` VARCHAR(255) NOT NULL,
                  `status` ENUM('open', 'in_progress', 'waiting_user', 'resolved', 'closed') NOT NULL DEFAULT 'open',
                  `assigned_to` BIGINT UNSIGNED NULL,
                  `last_reply_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  INDEX `idx_st_user` (`user_id`),
                  INDEX `idx_st_status` (`status`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                $pdo->exec("CREATE TABLE IF NOT EXISTS `ticket_messages` (
                  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                  `ticket_id` INT UNSIGNED NOT NULL,
                  `user_id` BIGINT UNSIGNED NOT NULL,
                  `is_staff` TINYINT(1) NOT NULL DEFAULT 0,
                  `is_internal_note` TINYINT(1) NOT NULL DEFAULT 0,
                  `message` TEXT NOT NULL,
                  `attachment_url` VARCHAR(255) NULL,
                  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  INDEX `idx_tm_ticket` (`ticket_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            }

            // 13. Check entity_reports table (Unified Reporting for all entities)
            $hasReports = $pdo->query("SHOW TABLES LIKE 'entity_reports'")->fetchAll();
            if (empty($hasReports)) {
                $pdo->exec("CREATE TABLE IF NOT EXISTS `entity_reports` (
                  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                  `reporter_user_id` BIGINT UNSIGNED NULL,
                  `entity_type` ENUM('article', 'ai_tool', 'gpt_listing', 'prompt', 'job', 'comment', 'campaign', 'author') NOT NULL,
                  `entity_id` INT UNSIGNED NOT NULL,
                  `entity_title` VARCHAR(255) NULL,
                  `entity_url` VARCHAR(255) NULL,
                  `reason` ENUM('spam', 'broken_or_misleading', 'plagiarism_copyright', 'inappropriate_harmful', 'malicious_prompt_injection', 'other') NOT NULL,
                  `details` TEXT NULL,
                  `status` ENUM('pending', 'reviewed', 'actioned', 'dismissed') NOT NULL DEFAULT 'pending',
                  `admin_notes` TEXT NULL,
                  `action_taken` VARCHAR(100) NULL,
                  `reviewed_by` BIGINT UNSIGNED NULL,
                  `ip_address` VARCHAR(45) NULL,
                  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  INDEX `idx_er_status` (`status`),
                  INDEX `idx_er_entity` (`entity_type`, `entity_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            } else {
                $erCols = array_column($pdo->query("SHOW COLUMNS FROM `entity_reports`")->fetchAll(PDO::FETCH_ASSOC), 'Field');
                if (!in_array('created_at', $erCols)) {
                    $pdo->exec("ALTER TABLE `entity_reports` ADD COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP");
                }
                if (!in_array('updated_at', $erCols)) {
                    $pdo->exec("ALTER TABLE `entity_reports` ADD COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
                }
            }

            // 14. Check audit_logs table & columns
            $hasAudit = $pdo->query("SHOW TABLES LIKE 'audit_logs'")->fetchAll();
            if (empty($hasAudit)) {
                $pdo->exec("CREATE TABLE IF NOT EXISTS `audit_logs` (
                  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                  `user_id` BIGINT UNSIGNED NULL,
                  `action` VARCHAR(80) NOT NULL,
                  `entity_type` VARCHAR(50) NULL,
                  `entity_id` BIGINT UNSIGNED NULL,
                  `ip_address` VARCHAR(45) NOT NULL,
                  `country` VARCHAR(100) NULL DEFAULT 'Pakistan',
                  `device_info` VARCHAR(255) NULL,
                  `user_agent` TEXT NULL,
                  `details` JSON NULL,
                  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  INDEX `idx_audit_user` (`user_id`),
                  INDEX `idx_audit_action` (`action`),
                  INDEX `idx_audit_created` (`created_at` DESC)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            } else {
                $auditCols = array_column($pdo->query("SHOW COLUMNS FROM `audit_logs`")->fetchAll(PDO::FETCH_ASSOC), 'Field');
                if (!in_array('country', $auditCols)) {
                    $pdo->exec("ALTER TABLE `audit_logs` ADD COLUMN `country` VARCHAR(100) NULL DEFAULT 'Pakistan' AFTER `ip_address`");
                }
                if (!in_array('device_info', $auditCols)) {
                    $pdo->exec("ALTER TABLE `audit_logs` ADD COLUMN `device_info` VARCHAR(255) NULL AFTER `country`");
                }
            }

            // 15. Check contents editorial_notes and reviewed_by columns
            $contentCols = array_column($pdo->query("SHOW COLUMNS FROM `contents`")->fetchAll(PDO::FETCH_ASSOC), 'Field');
            if (!in_array('editorial_notes', $contentCols)) {
                $pdo->exec("ALTER TABLE `contents` ADD COLUMN `editorial_notes` TEXT NULL AFTER `quality_score`");
            }
            if (!in_array('reviewed_by', $contentCols)) {
                $pdo->exec("ALTER TABLE `contents` ADD COLUMN `reviewed_by` BIGINT UNSIGNED NULL AFTER `editorial_notes`");
            }

            // 16. Check contact_messages user_id column
            $contactCols = array_column($pdo->query("SHOW COLUMNS FROM `contact_messages`")->fetchAll(PDO::FETCH_ASSOC), 'Field');
            if (!in_array('user_id', $contactCols)) {
                $pdo->exec("ALTER TABLE `contact_messages` ADD COLUMN `user_id` BIGINT UNSIGNED NULL AFTER `id`");
            }

            // 16b. Check ai_tools extended metadata columns
            $aiToolCols = array_column($pdo->query("SHOW COLUMNS FROM `ai_tools`")->fetchAll(PDO::FETCH_ASSOC), 'Field');
            $aiToolAdds = [
                'secondary_categories' => "JSON NULL AFTER `pricing_model`",
                'use_cases' => "JSON NULL AFTER `secondary_categories`",
                'pricing_billing_cycle' => "VARCHAR(50) NULL DEFAULT 'monthly' AFTER `pricing_starting_at`",
                'free_trial_days' => "INT NULL DEFAULT 14 AFTER `free_trial_available`",
                'discount_code' => "VARCHAR(100) NULL AFTER `pricing_billing_cycle`",
                'discount_deal' => "VARCHAR(255) NULL AFTER `discount_code`",
                'features_list' => "JSON NULL AFTER `discount_deal`",
                'platforms_supported' => "JSON NULL AFTER `features_list`",
                'license_type' => "VARCHAR(100) NULL DEFAULT 'Proprietary' AFTER `open_source_repo`",
                'alternatives_list' => "JSON NULL AFTER `license_type`",
                'comparison_highlights' => "TEXT NULL AFTER `alternatives_list`",
                'editorial_review' => "TEXT NULL AFTER `comparison_highlights`",
                'pros_list' => "JSON NULL AFTER `editorial_review`",
                'cons_list' => "JSON NULL AFTER `pros_list`",
                'rating_ease_of_use' => "DECIMAL(3,2) NOT NULL DEFAULT 4.80 AFTER `rating_count`",
                'rating_value_for_money' => "DECIMAL(3,2) NOT NULL DEFAULT 4.70 AFTER `rating_ease_of_use`",
                'rating_features' => "DECIMAL(3,2) NOT NULL DEFAULT 4.90 AFTER `rating_value_for_money`",
                'rating_performance' => "DECIMAL(3,2) NOT NULL DEFAULT 4.85 AFTER `rating_features`",
                'rating_support' => "DECIMAL(3,2) NOT NULL DEFAULT 4.60 AFTER `rating_performance`",
                'screenshots_gallery' => "JSON NULL AFTER `rating_support`",
                'docs_url' => "VARCHAR(255) NULL AFTER `website_url`",
                'discord_url' => "VARCHAR(255) NULL AFTER `docs_url`",
                'twitter_handle' => "VARCHAR(100) NULL AFTER `discord_url`",
                'custom_affiliate_slug' => "VARCHAR(100) NULL AFTER `affiliate_url`",
                'affiliate_commission_rate' => "VARCHAR(50) NULL AFTER `custom_affiliate_slug`"
            ];
            foreach ($aiToolAdds as $col => $def) {
                if (!in_array($col, $aiToolCols, true)) {
                    $pdo->exec("ALTER TABLE `ai_tools` ADD COLUMN `{$col}` {$def}");
                }
            }

            // 16c. Ensure authors monetization_status supports all workflow states
            $authorMonCol = $pdo->query("SHOW COLUMNS FROM `authors` LIKE 'monetization_status'")->fetch(PDO::FETCH_ASSOC);
            if ($authorMonCol && str_contains(strtolower($authorMonCol['Type'] ?? ''), 'enum')) {
                $pdo->exec("ALTER TABLE `authors` MODIFY COLUMN `monetization_status` VARCHAR(50) NOT NULL DEFAULT 'ineligible'");
            }

            // 17. Check mail_logs table & columns
            $hasMailLogs = $pdo->query("SHOW TABLES LIKE 'mail_logs'")->fetchAll();
            if (empty($hasMailLogs)) {
                $pdo->exec("CREATE TABLE IF NOT EXISTS `mail_logs` (
                  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                  `to_email` VARCHAR(191) NOT NULL,
                  `to_name` VARCHAR(150) NULL,
                  `from_email` VARCHAR(191) NULL,
                  `from_name` VARCHAR(150) NULL,
                  `subject` VARCHAR(255) NOT NULL,
                  `template_key` VARCHAR(80) NULL,
                  `body_html` LONGTEXT NULL,
                  `attachments_json` JSON NULL,
                  `driver` VARCHAR(30) NOT NULL DEFAULT 'log',
                  `credential_name` VARCHAR(150) NULL,
                  `tracking_token` VARCHAR(64) NULL,
                  `open_count` INT UNSIGNED NOT NULL DEFAULT 0,
                  `opened_at` DATETIME NULL,
                  `click_count` INT UNSIGNED NOT NULL DEFAULT 0,
                  `clicked_at` DATETIME NULL,
                  `last_clicked_url` TEXT NULL,
                  `user_ip` VARCHAR(45) NULL,
                  `user_agent` TEXT NULL,
                  `status` ENUM('sent', 'failed', 'queued') NOT NULL DEFAULT 'sent',
                  `error_message` TEXT NULL,
                  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  INDEX `idx_ml_email` (`to_email`),
                  INDEX `idx_ml_status` (`status`),
                  INDEX `idx_ml_token` (`tracking_token`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            } else {
                $mailCols = array_column($pdo->query("SHOW COLUMNS FROM `mail_logs`")->fetchAll(PDO::FETCH_ASSOC), 'Field');
                if (!in_array('body_html', $mailCols)) {
                    $pdo->exec("ALTER TABLE `mail_logs` ADD COLUMN `body_html` LONGTEXT NULL AFTER `template_key`");
                }
                if (!in_array('attachments_json', $mailCols)) {
                    $pdo->exec("ALTER TABLE `mail_logs` ADD COLUMN `attachments_json` JSON NULL AFTER `body_html`");
                }
                if (!in_array('from_email', $mailCols)) {
                    $pdo->exec("ALTER TABLE `mail_logs` ADD COLUMN `from_email` VARCHAR(191) NULL AFTER `to_name`");
                }
                if (!in_array('from_name', $mailCols)) {
                    $pdo->exec("ALTER TABLE `mail_logs` ADD COLUMN `from_name` VARCHAR(150) NULL AFTER `from_email`");
                }
                if (!in_array('credential_name', $mailCols)) {
                    $pdo->exec("ALTER TABLE `mail_logs` ADD COLUMN `credential_name` VARCHAR(150) NULL AFTER `driver`");
                }
                if (!in_array('tracking_token', $mailCols)) {
                    $pdo->exec("ALTER TABLE `mail_logs` ADD COLUMN `tracking_token` VARCHAR(64) NULL AFTER `credential_name`, ADD INDEX `idx_ml_token` (`tracking_token`)");
                }
                if (!in_array('open_count', $mailCols)) {
                    $pdo->exec("ALTER TABLE `mail_logs` ADD COLUMN `open_count` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `tracking_token`");
                }
                if (!in_array('opened_at', $mailCols)) {
                    $pdo->exec("ALTER TABLE `mail_logs` ADD COLUMN `opened_at` DATETIME NULL AFTER `open_count`");
                }
                if (!in_array('click_count', $mailCols)) {
                    $pdo->exec("ALTER TABLE `mail_logs` ADD COLUMN `click_count` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `opened_at`");
                }
                if (!in_array('clicked_at', $mailCols)) {
                    $pdo->exec("ALTER TABLE `mail_logs` ADD COLUMN `clicked_at` DATETIME NULL AFTER `click_count`");
                }
                if (!in_array('last_clicked_url', $mailCols)) {
                    $pdo->exec("ALTER TABLE `mail_logs` ADD COLUMN `last_clicked_url` TEXT NULL AFTER `clicked_at`");
                }
                if (!in_array('user_ip', $mailCols)) {
                    $pdo->exec("ALTER TABLE `mail_logs` ADD COLUMN `user_ip` VARCHAR(45) NULL AFTER `last_clicked_url`");
                }
                if (!in_array('user_agent', $mailCols)) {
                    $pdo->exec("ALTER TABLE `mail_logs` ADD COLUMN `user_agent` TEXT NULL AFTER `user_ip`");
                }
            }

            // 18. Check custom editable mail_templates table
            $hasTemplates = $pdo->query("SHOW TABLES LIKE 'mail_templates'")->fetchAll();
            if (empty($hasTemplates)) {
                $pdo->exec("CREATE TABLE IF NOT EXISTS `mail_templates` (
                  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                  `template_key` VARCHAR(80) NOT NULL UNIQUE,
                  `name` VARCHAR(150) NOT NULL,
                  `subject` VARCHAR(255) NOT NULL,
                  `description` TEXT NULL,
                  `html_content` LONGTEXT NOT NULL,
                  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            }

            // 19. Feature Flags Table
            $pdo->exec("CREATE TABLE IF NOT EXISTS `feature_flags` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `flag_key` VARCHAR(64) NOT NULL UNIQUE,
              `name` VARCHAR(120) NOT NULL,
              `description` TEXT NULL,
              `category` VARCHAR(50) NOT NULL DEFAULT 'general',
              `is_enabled` TINYINT(1) NOT NULL DEFAULT 1,
              `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // 19b. Author Payout Methods & Universal Financial Ledger
            $pdo->exec("CREATE TABLE IF NOT EXISTS `author_payout_methods` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `author_id` BIGINT UNSIGNED NOT NULL,
              `method_type` ENUM('bank_wire', 'iban_sepa', 'paypal', 'stripe_connect', 'crypto_usdc') NOT NULL,
              `account_holder_name` VARCHAR(150) NOT NULL,
              `bank_name` VARCHAR(150) NULL,
              `account_number_or_iban` VARCHAR(100) NULL,
              `routing_or_swift` VARCHAR(50) NULL,
              `account_email` VARCHAR(191) NULL,
              `crypto_wallet_address` VARCHAR(150) NULL,
              `crypto_network` VARCHAR(50) NULL DEFAULT 'ERC-20 (Ethereum)',
              `is_default` TINYINT(1) NOT NULL DEFAULT 0,
              `status` ENUM('verified', 'pending_verification', 'active', 'disabled') NOT NULL DEFAULT 'verified',
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              INDEX `idx_apm_author` (`author_id`),
              INDEX `idx_apm_default` (`author_id`, `is_default`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            $pdo->exec("CREATE TABLE IF NOT EXISTS `financial_ledger` (
              `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `reference_no` VARCHAR(64) NOT NULL UNIQUE,
              `author_id` BIGINT UNSIGNED NOT NULL,
              `user_id` BIGINT UNSIGNED NULL,
              `entry_type` ENUM('credit', 'debit') NOT NULL,
              `category` VARCHAR(64) NOT NULL,
              `amount_usd` DECIMAL(12,2) NOT NULL,
              `credits` DECIMAL(12,2) NOT NULL,
              `balance_before` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
              `balance_after` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
              `payout_method_id` INT UNSIGNED NULL,
              `payout_method_details` VARCHAR(255) NULL,
              `status` ENUM('completed', 'pending', 'processing', 'reversed', 'failed') NOT NULL DEFAULT 'completed',
              `performed_by_user_id` BIGINT UNSIGNED NULL,
              `performed_by_role` VARCHAR(50) NOT NULL DEFAULT 'system',
              `reason` TEXT NOT NULL,
              `internal_notes` TEXT NULL,
              `metadata_json` JSON NULL,
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              INDEX `idx_fl_author` (`author_id`),
              INDEX `idx_fl_type` (`entry_type`),
              INDEX `idx_fl_category` (`category`),
              INDEX `idx_fl_created` (`created_at` DESC)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // 19c. Check author_payouts columns
            $payoutCols = array_column($pdo->query("SHOW COLUMNS FROM `author_payouts`")->fetchAll(PDO::FETCH_ASSOC), 'Field');
            if (!in_array('tax_deducted', $payoutCols, true)) {
                $pdo->exec("ALTER TABLE `author_payouts` ADD COLUMN `tax_deducted` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `amount_usd`");
            }
            if (!in_array('bank_tax_deducted', $payoutCols, true)) {
                $pdo->exec("ALTER TABLE `author_payouts` ADD COLUMN `bank_tax_deducted` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `tax_deducted`");
            }
            if (!in_array('fee_deducted', $payoutCols, true)) {
                $pdo->exec("ALTER TABLE `author_payouts` ADD COLUMN `fee_deducted` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `bank_tax_deducted`");
            }
            if (!in_array('net_amount', $payoutCols, true)) {
                $pdo->exec("ALTER TABLE `author_payouts` ADD COLUMN `net_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `fee_deducted`");
            }
            if (!in_array('receipt_url', $payoutCols, true)) {
                $pdo->exec("ALTER TABLE `author_payouts` ADD COLUMN `receipt_url` VARCHAR(255) NULL AFTER `transaction_reference`");
            }
            if (!in_array('payout_method_id', $payoutCols, true)) {
                $pdo->exec("ALTER TABLE `author_payouts` ADD COLUMN `payout_method_id` INT UNSIGNED NULL AFTER `author_id`");
            }
            if (!in_array('ledger_reference_no', $payoutCols, true)) {
                $pdo->exec("ALTER TABLE `author_payouts` ADD COLUMN `ledger_reference_no` VARCHAR(64) NULL AFTER `id`");
            }

            // Check reading_history columns
            $rhCols = array_column($pdo->query("SHOW COLUMNS FROM `reading_history`")->fetchAll(PDO::FETCH_ASSOC), 'Field');
            if (!in_array('referrer_type', $rhCols, true)) {
                $pdo->exec("ALTER TABLE `reading_history` ADD COLUMN `referrer_type` VARCHAR(32) NOT NULL DEFAULT 'internal' AFTER `status`");
            }
            if (!in_array('referrer_source', $rhCols, true)) {
                $pdo->exec("ALTER TABLE `reading_history` ADD COLUMN `referrer_source` VARCHAR(255) NULL DEFAULT '/directory' AFTER `referrer_type`");
            }

            // 20. Creator Tipping & Article Sponsorship Table
            $pdo->exec("CREATE TABLE IF NOT EXISTS `author_tips` (
              `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `reference_no` VARCHAR(64) NOT NULL UNIQUE,
              `author_id` INT UNSIGNED NOT NULL,
              `article_id` INT UNSIGNED NULL,
              `tipper_user_id` BIGINT UNSIGNED NULL,
              `tipper_name` VARCHAR(100) NOT NULL DEFAULT 'Generous Supporter',
              `tipper_email` VARCHAR(150) NULL,
              `amount_usd` DECIMAL(10,2) NOT NULL,
              `platform_fee_usd` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
              `net_amount_usd` DECIMAL(10,2) NOT NULL,
              `message` TEXT NULL,
              `is_anonymous` TINYINT(1) NOT NULL DEFAULT 0,
              `payment_method` VARCHAR(50) NOT NULL DEFAULT 'card_stripe',
              `status` ENUM('completed', 'pending', 'refunded') NOT NULL DEFAULT 'completed',
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              INDEX `idx_tip_author` (`author_id`),
              INDEX `idx_tip_article` (`article_id`),
              INDEX `idx_tip_created` (`created_at` DESC)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // 21. Dynamic Author Tier Progression History
            $pdo->exec("CREATE TABLE IF NOT EXISTS `author_tier_history` (
              `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `author_id` INT UNSIGNED NOT NULL,
              `tier_code` VARCHAR(50) NOT NULL,
              `tier_level` INT NOT NULL DEFAULT 1,
              `tier_title` VARCHAR(100) NOT NULL,
              `cpm_multiplier` DECIMAL(4,2) NOT NULL DEFAULT 1.00,
              `unlocked_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              INDEX `idx_th_author` (`author_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // 22. Interactive Co-Authoring & Royalty Splitting
            $pdo->exec("CREATE TABLE IF NOT EXISTS `article_co_authors` (
              `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `article_id` INT UNSIGNED NOT NULL,
              `author_id` INT UNSIGNED NOT NULL,
              `role` ENUM('lead_author', 'co_author', 'technical_reviewer') NOT NULL DEFAULT 'co_author',
              `revenue_share_percentage` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
              `invitation_status` ENUM('pending', 'accepted', 'declined') NOT NULL DEFAULT 'accepted',
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              UNIQUE KEY `uk_article_author` (`article_id`, `author_id`),
              INDEX `idx_aca_article` (`article_id`),
              INDEX `idx_aca_author` (`author_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // 23. Granular RBAC Roles & Capabilities
            $pdo->exec("CREATE TABLE IF NOT EXISTS `admin_roles` (
              `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `role_key` VARCHAR(50) NOT NULL UNIQUE,
              `name` VARCHAR(100) NOT NULL,
              `description` TEXT NULL,
              `is_system` TINYINT(1) NOT NULL DEFAULT 0,
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            $pdo->exec("CREATE TABLE IF NOT EXISTS `admin_role_permissions` (
              `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `role_id` INT UNSIGNED NOT NULL,
              `permission_key` VARCHAR(100) NOT NULL,
              UNIQUE KEY `uk_role_perm` (`role_id`, `permission_key`),
              INDEX `idx_arp_role` (`role_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // 24. Creator Referral & Ambassador Program
            $pdo->exec("CREATE TABLE IF NOT EXISTS `creator_referrals` (
              `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `referrer_author_id` INT UNSIGNED NOT NULL,
              `referred_user_id` BIGINT UNSIGNED NOT NULL,
              `referred_author_id` INT UNSIGNED NULL,
              `referral_code` VARCHAR(50) NOT NULL,
              `commission_rate` DECIMAL(5,2) NOT NULL DEFAULT 5.00,
              `expires_at` DATETIME NOT NULL,
              `total_commission_earned` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              INDEX `idx_cr_referrer` (`referrer_author_id`),
              INDEX `idx_cr_referred` (`referred_author_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            $pdo->exec("CREATE TABLE IF NOT EXISTS `referral_commissions` (
              `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `referral_id` BIGINT UNSIGNED NOT NULL,
              `referrer_author_id` INT UNSIGNED NOT NULL,
              `ledger_reference_no` VARCHAR(64) NOT NULL,
              `amount_usd` DECIMAL(10,2) NOT NULL,
              `source_event` VARCHAR(100) NOT NULL,
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              INDEX `idx_rc_referral` (`referral_id`),
              INDEX `idx_rc_referrer` (`referrer_author_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // Check author tier & referral columns
            $authorCols = array_column($pdo->query("SHOW COLUMNS FROM `authors`")->fetchAll(PDO::FETCH_ASSOC), 'Field');
            if (!in_array('current_tier_code', $authorCols, true)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `current_tier_code` VARCHAR(50) NOT NULL DEFAULT 'bronze' AFTER `reputation_level`");
            }
            if (!in_array('tier_sublevel', $authorCols, true)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `tier_sublevel` INT NOT NULL DEFAULT 1 AFTER `current_tier_code`");
            }
            if (!in_array('tier_multiplier', $authorCols, true)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `tier_multiplier` DECIMAL(4,2) NOT NULL DEFAULT 1.00 AFTER `tier_sublevel`");
            }
            if (!in_array('referral_code', $authorCols, true)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `referral_code` VARCHAR(50) NULL AFTER `tier_multiplier`");
            }

            // Check users admin_role_id column
            $userCols = array_column($pdo->query("SHOW COLUMNS FROM `users`")->fetchAll(PDO::FETCH_ASSOC), 'Field');
            if (!in_array('admin_role_id', $userCols, true)) {
                $pdo->exec("ALTER TABLE `users` ADD COLUMN `admin_role_id` INT UNSIGNED NULL AFTER `role`");
            }

            // Seed default admin roles if empty
            $roleCount = (int)($pdo->query("SELECT COUNT(*) as c FROM `admin_roles`")->fetch(PDO::FETCH_ASSOC)['c'] ?? 0);
            if ($roleCount === 0) {
                $pdo->exec("INSERT INTO `admin_roles` (`id`, `role_key`, `name`, `description`, `is_system`) VALUES
                (1, 'superadmin', 'Superadministrator', 'Unrestricted administrative access to all system subsystems.', 1),
                (2, 'financial_auditor', 'Financial Auditor', 'Full visibility into revenues, double-entry ledger, payouts, and tipping streams without content edit rights.', 1),
                (3, 'managing_editor', 'Lead Managing Editor', 'Publishing, article curation, author onboarding, and category taxonomy governance.', 1),
                (4, 'trust_safety', 'Trust & Safety Moderator', 'Community flag resolution, comment moderation, plagiarism investigations, and user strikes.', 1),
                (5, 'support_specialist', 'Support Specialist', 'Support ticket management, user assistance, and knowledge base curation.', 1)");

                // Seed superadmin all permissions
                $allPerms = [
                    'revenue.view', 'revenue.payouts', 'revenue.ledger', 'revenue.adjust_balance', 'revenue.settings',
                    'articles.view', 'articles.create', 'articles.edit', 'articles.publish', 'articles.delete', 'articles.feature',
                    'authors.view', 'authors.verify', 'authors.monetization', 'authors.suspend',
                    'community.comments', 'community.reports', 'community.moderation',
                    'support.tickets', 'support.reply', 'support.escalate',
                    'cms.settings', 'cms.navigation', 'cms.branding',
                    'system.rbac', 'system.logs', 'system.backups', 'system.flags'
                ];
                foreach ($allPerms as $p) {
                    $pdo->exec("INSERT INTO `admin_role_permissions` (`role_id`, `permission_key`) VALUES (1, '{$p}')");
                }
                // Financial auditor perms
                foreach (['revenue.view', 'revenue.payouts', 'revenue.ledger', 'revenue.adjust_balance', 'revenue.settings', 'system.logs'] as $p) {
                    $pdo->exec("INSERT INTO `admin_role_permissions` (`role_id`, `permission_key`) VALUES (2, '{$p}')");
                }
                // Managing editor perms
                foreach (['articles.view', 'articles.create', 'articles.edit', 'articles.publish', 'articles.feature', 'authors.view', 'authors.verify', 'authors.monetization', 'cms.navigation'] as $p) {
                    $pdo->exec("INSERT INTO `admin_role_permissions` (`role_id`, `permission_key`) VALUES (3, '{$p}')");
                }
                // Trust & safety perms
                foreach (['community.comments', 'community.reports', 'community.moderation', 'authors.view', 'authors.suspend', 'system.logs'] as $p) {
                    $pdo->exec("INSERT INTO `admin_role_permissions` (`role_id`, `permission_key`) VALUES (4, '{$p}')");
                }
                // Support specialist perms
                foreach (['support.tickets', 'support.reply', 'support.escalate', 'community.reports'] as $p) {
                    $pdo->exec("INSERT INTO `admin_role_permissions` (`role_id`, `permission_key`) VALUES (5, '{$p}')");
                }
            }

            // Seed default feature flags if empty
            $ffCount = (int)($pdo->query("SELECT COUNT(*) as c FROM `feature_flags`")->fetch(PDO::FETCH_ASSOC)['c'] ?? 0);
            if ($ffCount === 0) {
                $pdo->exec("INSERT INTO `feature_flags` (`flag_key`, `name`, `description`, `category`, `is_enabled`) VALUES
                ('enable_ai_tools', 'AI Tools Directory', 'Public access to AI tools discovery and benchmarking hub', 'modules', 1),
                ('enable_gpts', 'Custom GPTs Directory', 'Access to custom GPT directory & prompt listings', 'modules', 1),
                ('enable_prompts', 'Engineering Prompt Library', 'Curated prompts for frontier LLMs and developer pipelines', 'modules', 1),
                ('enable_jobs', 'Tech Jobs & Bounties Board', 'Developer hiring marketplace and engineering job postings', 'modules', 1),
                ('enable_challenges', 'Creator Challenges & Hackathons', 'Developer bounties, hackathons and prize challenges', 'modules', 1),
                ('enable_author_registration', 'Creator Applications', 'Allow members to submit creator / author applications', 'community', 1),
                ('enable_comments', 'Interactive Discussions', 'Allow logged-in users to post comments on publications', 'community', 1),
                ('enable_instant_search', 'Command+K Instant Search', 'Global fast search index overlay across all entities', 'ux', 1),
                ('enable_social_share', 'Social Share Widget', 'Floating and inline social sharing buttons on articles', 'ux', 1),
                ('enable_rss_feeds', 'RSS / XML Content Syndication', 'Syndication feeds at /feed.xml and category RSS', 'seo', 1),
                ('enable_dark_mode_toggle', 'Light/Dark Theme Switcher', 'Allow readers to toggle between Light and Cyber Dark mode', 'ux', 1),
                ('enable_live_notifications', 'In-App Notification Center', 'Real-time notification bell dropdown in the navigation bar', 'community', 1),
                ('enable_webhooks', 'Outbound Webhook Dispatcher', 'Deliver HTTP POST event payloads to external URLs', 'integrations', 1),
                ('enable_api_keys', 'Developer REST API Gateway', 'Allow API token generation for programmatic access', 'integrations', 1),
                ('enable_security_firewall', 'Cyber Security WAF Shield', 'Active IP blocking, honeypots, and threat mitigation', 'security', 1)");
            }

            // 20. Outbound Webhooks & Delivery Logs
            $pdo->exec("CREATE TABLE IF NOT EXISTS `webhooks` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `name` VARCHAR(120) NOT NULL,
              `target_url` VARCHAR(255) NOT NULL,
              `secret_key` VARCHAR(64) NOT NULL,
              `events_json` JSON NOT NULL,
              `is_active` TINYINT(1) NOT NULL DEFAULT 1,
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            $pdo->exec("CREATE TABLE IF NOT EXISTS `webhook_deliveries` (
              `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `webhook_id` INT UNSIGNED NOT NULL,
              `event` VARCHAR(60) NOT NULL,
              `payload_json` LONGTEXT NOT NULL,
              `response_code` INT NULL,
              `response_body` TEXT NULL,
              `status` ENUM('success', 'failed') NOT NULL DEFAULT 'success',
              `duration_ms` INT NOT NULL DEFAULT 0,
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              INDEX `idx_wh_id` (`webhook_id`),
              INDEX `idx_wh_created` (`created_at` DESC)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // 21. API Keys & Developer Access
            $pdo->exec("CREATE TABLE IF NOT EXISTS `api_keys` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `user_id` BIGINT UNSIGNED NOT NULL,
              `key_name` VARCHAR(100) NOT NULL,
              `api_key_prefix` VARCHAR(16) NOT NULL,
              `api_key_hash` VARCHAR(128) NOT NULL,
              `scopes_json` JSON NOT NULL,
              `rate_limit_per_min` INT NOT NULL DEFAULT 60,
              `requests_count` BIGINT UNSIGNED NOT NULL DEFAULT 0,
              `last_used_at` DATETIME NULL,
              `expires_at` DATETIME NULL,
              `is_active` TINYINT(1) NOT NULL DEFAULT 1,
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              INDEX `idx_ak_user` (`user_id`),
              INDEX `idx_ak_prefix` (`api_key_prefix`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // 22. Security Firewall Rules & Telemetry Logs
            $pdo->exec("CREATE TABLE IF NOT EXISTS `firewall_rules` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `rule_type` ENUM('ip', 'cidr', 'user_agent', 'country') NOT NULL DEFAULT 'ip',
              `target_value` VARCHAR(191) NOT NULL,
              `action` ENUM('block', 'allow', 'challenge') NOT NULL DEFAULT 'block',
              `reason` VARCHAR(255) NULL,
              `hits_count` BIGINT UNSIGNED NOT NULL DEFAULT 0,
              `is_active` TINYINT(1) NOT NULL DEFAULT 1,
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              INDEX `idx_fw_target` (`target_value`),
              INDEX `idx_fw_type` (`rule_type`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            $pdo->exec("CREATE TABLE IF NOT EXISTS `firewall_logs` (
              `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `ip_address` VARCHAR(45) NOT NULL,
              `user_agent` VARCHAR(255) NULL,
              `path` VARCHAR(255) NOT NULL,
              `threat_type` VARCHAR(80) NOT NULL,
              `action_taken` VARCHAR(40) NOT NULL DEFAULT 'blocked',
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              INDEX `idx_fl_ip` (`ip_address`),
              INDEX `idx_fl_created` (`created_at` DESC)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // 23. Global Announcement Ribbons & Alerts
            $pdo->exec("CREATE TABLE IF NOT EXISTS `site_announcements` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `title` VARCHAR(150) NOT NULL,
              `message` TEXT NOT NULL,
              `ribbon_type` ENUM('info', 'announcement', 'warning', 'promo', 'maintenance') NOT NULL DEFAULT 'announcement',
              `banner_color` VARCHAR(20) NOT NULL DEFAULT '#6366f1',
              `text_color` VARCHAR(20) NOT NULL DEFAULT '#ffffff',
              `action_label` VARCHAR(60) NULL,
              `action_url` VARCHAR(255) NULL,
              `target_audience` ENUM('all', 'guests', 'members', 'authors') NOT NULL DEFAULT 'all',
              `is_dismissible` TINYINT(1) NOT NULL DEFAULT 1,
              `is_active` TINYINT(1) NOT NULL DEFAULT 1,
              `start_at` DATETIME NULL,
              `end_at` DATETIME NULL,
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // 24. Keyword Blacklist & Content Filter
            $pdo->exec("CREATE TABLE IF NOT EXISTS `content_filters` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `pattern` VARCHAR(191) NOT NULL,
              `match_type` ENUM('exact', 'contains', 'regex') NOT NULL DEFAULT 'contains',
              `replacement` VARCHAR(50) NOT NULL DEFAULT '***',
              `action` ENUM('replace', 'flag_review', 'auto_quarantine') NOT NULL DEFAULT 'replace',
              `severity` ENUM('low', 'medium', 'high', 'critical') NOT NULL DEFAULT 'medium',
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // 25. SEO Overrides & Robots Settings
            $pdo->exec("CREATE TABLE IF NOT EXISTS `seo_metadata` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `path_pattern` VARCHAR(191) NOT NULL UNIQUE,
              `meta_title` VARCHAR(200) NOT NULL,
              `meta_description` TEXT NOT NULL,
              `og_image_url` VARCHAR(255) NULL,
              `canonical_url` VARCHAR(255) NULL,
              `robots_directive` VARCHAR(60) NOT NULL DEFAULT 'index, follow',
              `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // 11. Seed extended site settings (Ad slots, timezone, extended social, mail)
            $pdo->exec("INSERT IGNORE INTO `site_settings` (`key_name`, `key_value`) VALUES
            ('default_timezone', 'Asia/Karachi'),
            ('social_telegram', 'https://t.me/techscout'),
            ('social_reddit', 'https://reddit.com/r/techscout'),
            ('ad_header_code', ''),
            ('ad_slot_top', ''),
            ('ad_slot_sidebar', ''),
            ('ad_slot_in_article', ''),
            ('ad_slot_footer', ''),
            ('mail_enabled', '0'),
            ('mail_driver', 'log'),
            ('mail_from_address', 'notifications@techscout.online'),
            ('mail_from_name', 'TechScout Intelligence'),
            ('mail_api_provider', 'resend'),
            ('mail_api_key', ''),
            ('mail_api_endpoint', 'https://api.resend.com/emails'),
            ('mail_smtp_host', 'smtp.gmail.com'),
            ('mail_smtp_port', '587'),
            ('mail_smtp_encryption', 'tls'),
            ('mail_smtp_username', ''),
            ('mail_smtp_password', ''),
            ('custom_robots_txt', 'User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /auth/\nSitemap: https://techscout.online/sitemap.xml')");

            // 12. CMS Homepage Sections Configuration
            $pdo->exec("CREATE TABLE IF NOT EXISTS `homepage_sections` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `section_key` VARCHAR(60) NOT NULL UNIQUE,
              `title` VARCHAR(150) NOT NULL,
              `subtitle` VARCHAR(255) NULL,
              `is_enabled` TINYINT(1) NOT NULL DEFAULT 1,
              `sort_order` INT NOT NULL DEFAULT 0,
              `item_limit` INT NOT NULL DEFAULT 6,
              `config_json` JSON NULL,
              `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // Seed default 15 homepage sections
            $homepageSections = [
                ['hero', 'Hero Frontier HUD', 'Primary above-the-fold headline, dynamic search, live stats counter & quick links', 1, 1, 1],
                ['featured', 'Featured Spotlight Content', 'Handpicked editorial deep dives & architectural showcases', 1, 2, 4],
                ['trending', 'Trending Radar', 'Real-time most viral articles based on telemetry, upvotes and reads', 1, 3, 6],
                ['latest', 'Latest Scouted Publications', 'Reverse-chronological editorial stream with category filter badges', 1, 4, 8],
                ['recommended', 'Recommended For You', 'Personalized content recommendations tailored to member topic subscriptions', 1, 5, 4],
                ['authors', 'Top Curators & Staff Authors', 'Featured engineering creators, builders, and technical researchers', 1, 6, 6],
                ['topics', 'Taxonomy & Knowledge Hubs', '100+ categories and curated domain tracks', 1, 7, 8],
                ['tools', 'AI Tools & Software Radar', 'Curated developer tools, agent frameworks, and LLM APIs', 1, 8, 6],
                ['ai_tools', 'Frontier AI Directory', 'Deep-dive machine learning model directory and benchmarks', 1, 9, 6],
                ['prompts', 'Engineering Prompt Blueprints', 'Production-ready system prompts and variable injector templates', 1, 10, 6],
                ['news', 'Breaking Intelligence & Scoops', 'Real-time updates, releases, and tech industry news wire', 1, 11, 5],
                ['collections', 'Curated Resource Collections', 'Starter kits, architecture stacks, and reading tracks', 1, 12, 4],
                ['campaigns', 'Developer Bounties & Hackathons', 'Active prize pools, engineering challenges, and competitions', 1, 13, 3],
                ['newsletter', 'TechScout Dispatch Newsletter', 'Weekly curated dispatch delivered to 48,000+ developers', 1, 14, 1],
                ['cta', 'Creator Studio Onboarding CTA', 'Call-to-action banner inviting builders to apply for creator verification', 1, 15, 1],
            ];
            foreach ($homepageSections as $hs) {
                $pdo->exec("INSERT IGNORE INTO `homepage_sections` (`section_key`, `title`, `subtitle`, `is_enabled`, `sort_order`, `item_limit`) VALUES ('{$hs[0]}', " . $pdo->quote($hs[1]) . ", " . $pdo->quote($hs[2]) . ", {$hs[3]}, {$hs[4]}, {$hs[5]})");
            }

            // 13. CMS Media Library & Asset Registry
            $pdo->exec("CREATE TABLE IF NOT EXISTS `media_files` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `uuid` CHAR(36) NOT NULL,
              `user_id` BIGINT UNSIGNED NOT NULL DEFAULT 1,
              `filename` VARCHAR(255) NOT NULL,
              `original_name` VARCHAR(255) NOT NULL,
              `file_path` VARCHAR(255) NOT NULL,
              `file_type` ENUM('image', 'video', 'audio', 'document') NOT NULL DEFAULT 'image',
              `file_size` BIGINT UNSIGNED NOT NULL DEFAULT 0,
              `mime_type` VARCHAR(100) NOT NULL DEFAULT 'image/jpeg',
              `folder` VARCHAR(100) NOT NULL DEFAULT 'general',
              `alt_text` VARCHAR(255) NULL,
              `caption` VARCHAR(255) NULL,
              `copyright_info` VARCHAR(255) NULL DEFAULT 'TechScout Editorial / Unsplash License',
              `attribution` VARCHAR(255) NULL,
              `metadata_json` JSON NULL,
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              INDEX `idx_media_type` (`file_type`),
              INDEX `idx_media_folder` (`folder`),
              INDEX `idx_media_user` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // Seed initial sample media items if empty
            $hasMedia = $pdo->query("SELECT COUNT(*) as c FROM `media_files`")->fetch(PDO::FETCH_ASSOC)['c'] ?? 0;
            if ($hasMedia == 0) {
                $pdo->exec("INSERT INTO `media_files` (`uuid`, `user_id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `mime_type`, `folder`, `alt_text`, `caption`, `copyright_info`, `attribution`) VALUES
                ('med-uuid-01', 1, 'agentic-ai-flow.png', 'agentic-ai-flow.png', 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=1200&auto=format&fit=crop&q=80', 'image', 245120, 'image/png', 'articles', 'Agentic AI Autonomous Workflow Diagram', 'Multi-Agent LLM Orchestration Architecture', 'CC-BY-SA 4.0', 'TechScout Research Lab'),
                ('med-uuid-02', 1, 'deepseek-r1-benchmark.png', 'deepseek-r1-benchmark.png', 'https://images.unsplash.com/photo-1620712943543-bcc4688e7485?w=1200&auto=format&fit=crop&q=80', 'image', 382040, 'image/png', 'articles', 'DeepSeek R1 Benchmark Analysis', 'Reasoning Model Benchmark Performance Comparison', 'Editorial Use Only', 'DeepSeek AI & TechScout'),
                ('med-uuid-03', 1, 'cloud-kubernetes-mesh.png', 'cloud-kubernetes-mesh.png', 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=1200&auto=format&fit=crop&q=80', 'image', 512000, 'image/png', 'infrastructure', 'High-Availability Service Mesh Topology', 'Zero-Trust Istio Service Mesh on Kubernetes', 'Proprietary', 'DevOps Engineering Team'),
                ('med-uuid-04', 1, 'techscout-podcast-ep1.mp3', 'techscout-podcast-ep1.mp3', 'https://actions.google.com/sounds/v1/science/electronic_synth_chords.ogg', 'audio', 1845000, 'audio/mpeg', 'audio', 'TechScout Engineering Podcast Episode 1', 'Frontier Reasoning Models & Autonomous Code Synthesis', 'Copyright 2026 TechScout Media', 'TechScout Audio Productions'),
                ('med-uuid-05', 1, 'techscout-architecture-whitepaper.pdf', 'techscout-architecture-whitepaper.pdf', '/docs/techscout-architecture.pdf', 'document', 4194304, 'application/pdf', 'documents', 'TechScout System Architecture Whitepaper v1.0', 'Comprehensive 960-Task System Architecture Specification', 'All Rights Reserved', 'Antigravity Engineering Team')");
            }

            // 14. CMS SEO Metadata & Page Schema
            $pdo->exec("CREATE TABLE IF NOT EXISTS `seo_metadata` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `page_key` VARCHAR(100) NOT NULL UNIQUE,
              `title` VARCHAR(200) NOT NULL,
              `meta_description` TEXT NULL,
              `meta_keywords` VARCHAR(255) NULL,
              `canonical_url` VARCHAR(255) NULL,
              `og_image_url` VARCHAR(255) NULL,
              `structured_data` LONGTEXT NULL,
              `robots_rule` VARCHAR(50) NOT NULL DEFAULT 'index, follow',
              `sitemap_priority` VARCHAR(10) NOT NULL DEFAULT '0.8',
              `sitemap_frequency` VARCHAR(20) NOT NULL DEFAULT 'weekly',
              `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // Seed foundational SEO pages
            $seoPages = [
                ['global', 'TechScout — The Frontier of Tech, Scouted for Builders', 'Discover frontier AI tools, production prompt blueprints, custom GPTs, and high-performance system architecture guides.', 'AI tools, software engineering, prompts, LLMs, tech news, architecture', 'https://techscout.online', 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=1200', '1.0', 'daily'],
                ['home', 'TechScout — Autonomous Tech Intelligence & Builder Hub', 'TechScout is the definitive technical discovery hub for software architects, AI engineers, and modern product builders.', 'developer hub, ai radar, system design, technical articles', 'https://techscout.online/', '', '1.0', 'daily'],
                ['blog', 'Technical Articles & Deep Dives | TechScout', 'Production engineering articles, low-latency architectures, LLM reasoning evaluations, and developer tutorials.', 'tech blog, programming tutorials, backend architecture, AI engineering', 'https://techscout.online/blog', '', '0.9', 'daily'],
                ['tools', 'AI Tools Radar & Developer Directory | TechScout', 'Curated, benchmarked AI tools, autonomous coding assistants, synthetic voice APIs, and model frameworks.', 'AI tools, developer tools, coding AI, vector databases', 'https://techscout.online/tools', '', '0.9', 'daily'],
                ['prompts', 'Engineering Prompt Blueprints | TechScout', 'Tested prompt templates with variable injection schemas for Claude 3.7, GPT-4o, DeepSeek, and Gemini 2.0.', 'prompt engineering, system prompts, AI templates', 'https://techscout.online/prompts', '', '0.8', 'weekly'],
                ['gpts', 'Custom GPTs & Autonomous Agent Directory | TechScout', 'Curated list of verified OpenAI GPTs and autonomous assistant agents with deep link integration.', 'custom gpts, chatgpt agents, ai assistants', 'https://techscout.online/gpts', '', '0.8', 'weekly'],
                ['campaigns', 'Developer Challenges, Bounties & Hackathons | TechScout', 'Compete in developer hackathons, solve engineering bounties, and win cash prizes and promo credits.', 'developer hackathons, tech bounties, coding contests', 'https://techscout.online/campaigns', '', '0.8', 'weekly'],
                ['topics', 'Explore 100+ Topics & Engineering Taxonomies | TechScout', 'Browse specialized technology taxonomies from AppSec to High-Throughput Databases and WebAssembly.', 'tech categories, topics taxonomy, software disciplines', 'https://techscout.online/topics', '', '0.8', 'weekly'],
            ];
            foreach ($seoPages as $sp) {
                $pdo->exec("INSERT IGNORE INTO `seo_metadata` (`page_key`, `title`, `meta_description`, `meta_keywords`, `canonical_url`, `og_image_url`, `sitemap_priority`, `sitemap_frequency`) VALUES ('{$sp[0]}', " . $pdo->quote($sp[1]) . ", " . $pdo->quote($sp[2]) . ", " . $pdo->quote($sp[3]) . ", " . $pdo->quote($sp[4]) . ", " . $pdo->quote($sp[5]) . ", '{$sp[6]}', '{$sp[7]}')");
            }

            // 15. CMS Navigation Menus Studio
            $pdo->exec("CREATE TABLE IF NOT EXISTS `navigation_menus` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `location` ENUM('main', 'secondary', 'footer', 'mobile', 'dropdown', 'mega_menu', 'external') NOT NULL DEFAULT 'main',
              `parent_id` INT UNSIGNED NULL,
              `label` VARCHAR(100) NOT NULL,
              `url` VARCHAR(255) NOT NULL,
              `target` ENUM('_self', '_blank') NOT NULL DEFAULT '_self',
              `icon_name` VARCHAR(50) NULL,
              `badge_text` VARCHAR(30) NULL,
              `badge_color` VARCHAR(20) NOT NULL DEFAULT 'cyan',
              `sort_order` INT NOT NULL DEFAULT 0,
              `is_active` TINYINT(1) NOT NULL DEFAULT 1,
              `mega_menu_data` TEXT NULL,
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              INDEX `idx_nav_loc_order` (`location`, `sort_order`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // Seed default navigation items if empty
            $hasNav = $pdo->query("SELECT COUNT(*) as c FROM `navigation_menus`")->fetch(PDO::FETCH_ASSOC)['c'] ?? 0;
            if ($hasNav == 0) {
                $navDefaults = [
                    ['main', 'Home', '/', 1],
                    ['main', 'Articles', '/blog', 2],
                    ['main', 'Topics', '/topics', 3],
                    ['main', 'AI Tools', '/tools', 4],
                    ['main', 'Prompts', '/prompts', 5],
                    ['main', 'GPTs', '/gpts', 6],
                    ['main', 'Challenges', '/campaigns', 7],
                    ['footer', 'Latest Articles', '/blog', 1],
                    ['footer', 'AI Tools Radar', '/tools', 2],
                    ['footer', 'Prompt Blueprints', '/prompts', 3],
                    ['footer', 'About TechScout', '/about', 4],
                    ['footer', 'Support & Helpdesk', '/support', 5],
                    ['footer', 'Privacy Policy', '/privacy', 6],
                    ['footer', 'Terms of Service', '/terms', 7],
                    ['footer', 'Cookies Policy', '/cookies', 8],
                    ['footer', 'Community Guidelines', '/community-guidelines', 9],
                ];
                foreach ($navDefaults as $nd) {
                    $pdo->exec("INSERT INTO `navigation_menus` (`location`, `label`, `url`, `sort_order`, `is_active`) VALUES ('{$nd[0]}', " . $pdo->quote($nd[1]) . ", '{$nd[2]}', {$nd[3]}, 1)");
                }
            }

            // 16. Ensure users columns for onboarding and 2FA
            $userCols = array_column($pdo->query("SHOW COLUMNS FROM `users`")->fetchAll(PDO::FETCH_ASSOC), 'Field');
            if (!in_array('onboarding_completed', $userCols)) {
                $pdo->exec("ALTER TABLE `users` ADD COLUMN `onboarding_completed` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`");
            }
            if (!in_array('user_interests', $userCols)) {
                $pdo->exec("ALTER TABLE `users` ADD COLUMN `user_interests` JSON NULL AFTER `onboarding_completed`");
            }

            // 17. Mail Credentials & Multi-Provider Microservice Architecture
            $pdo->exec("CREATE TABLE IF NOT EXISTS `mail_credentials` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `name` VARCHAR(150) NOT NULL,
              `provider` VARCHAR(50) NOT NULL DEFAULT 'smtp',
              `from_name` VARCHAR(150) NOT NULL,
              `from_email` VARCHAR(191) NOT NULL,
              `reply_to_email` VARCHAR(191) NULL,
              `host` VARCHAR(191) NULL,
              `port` INT NULL DEFAULT 587,
              `encryption` VARCHAR(20) NULL DEFAULT 'tls',
              `username` VARCHAR(191) NULL,
              `password` VARCHAR(255) NULL,
              `api_key` TEXT NULL,
              `api_endpoint` VARCHAR(255) NULL,
              `custom_headers_json` TEXT NULL,
              `request_timeout` INT NOT NULL DEFAULT 15,
              `payload_format` VARCHAR(50) NOT NULL DEFAULT 'standard_json',
              `webhook_url` VARCHAR(255) NULL,
              `is_default` TINYINT(1) NOT NULL DEFAULT 0,
              `status` ENUM('active', 'inactive', 'testing') NOT NULL DEFAULT 'active',
              `notes` TEXT NULL,
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              INDEX `idx_mc_provider` (`provider`),
              INDEX `idx_mc_default` (`is_default`),
              INDEX `idx_mc_status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            $mcCols = array_column($pdo->query("SHOW COLUMNS FROM `mail_credentials`")->fetchAll(PDO::FETCH_ASSOC), 'Field');
            if (!in_array('custom_headers_json', $mcCols)) {
                $pdo->exec("ALTER TABLE `mail_credentials` ADD COLUMN `custom_headers_json` TEXT NULL AFTER `api_endpoint`");
            }
            if (!in_array('request_timeout', $mcCols)) {
                $pdo->exec("ALTER TABLE `mail_credentials` ADD COLUMN `request_timeout` INT NOT NULL DEFAULT 15 AFTER `custom_headers_json`");
            }
            if (!in_array('payload_format', $mcCols)) {
                $pdo->exec("ALTER TABLE `mail_credentials` ADD COLUMN `payload_format` VARCHAR(50) NOT NULL DEFAULT 'standard_json' AFTER `request_timeout`");
            }
            if (!in_array('reply_to_email', $mcCols)) {
                $pdo->exec("ALTER TABLE `mail_credentials` ADD COLUMN `reply_to_email` VARCHAR(191) NULL AFTER `from_email`");
            }
            if (!in_array('webhook_url', $mcCols)) {
                $pdo->exec("ALTER TABLE `mail_credentials` ADD COLUMN `webhook_url` VARCHAR(255) NULL AFTER `payload_format`");
            }
            if (!in_array('notes', $mcCols)) {
                $pdo->exec("ALTER TABLE `mail_credentials` ADD COLUMN `notes` TEXT NULL AFTER `status`");
            }

            // 18. Bookmarks & Private User Library
            $pdo->exec("CREATE TABLE IF NOT EXISTS `bookmarks` (
              `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `user_id` BIGINT UNSIGNED NOT NULL,
              `content_id` BIGINT UNSIGNED NOT NULL,
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              UNIQUE KEY `uk_user_content_bookmark` (`user_id`, `content_id`),
              INDEX `idx_bm_user` (`user_id`),
              INDEX `idx_bm_content` (`content_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // 19. User Reading History & Scroll Progress Tracking
            $pdo->exec("CREATE TABLE IF NOT EXISTS `reading_history` (
              `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `user_id` BIGINT UNSIGNED NOT NULL,
              `content_id` BIGINT UNSIGNED NOT NULL,
              `progress_percent` INT NOT NULL DEFAULT 0,
              `read_seconds` INT NOT NULL DEFAULT 0,
              `last_read_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              UNIQUE KEY `uk_user_content_history` (`user_id`, `content_id`),
              INDEX `idx_rh_user` (`user_id`),
              INDEX `idx_rh_last_read` (`last_read_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // 19b. Analytics Events & Telemetry Collection
            $pdo->exec("CREATE TABLE IF NOT EXISTS `analytics_events` (
              `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `event_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `event_type` VARCHAR(50) NOT NULL,
              `content_id` BIGINT UNSIGNED NULL,
              `author_id` BIGINT UNSIGNED NULL,
              `user_id` BIGINT UNSIGNED NULL,
              `referrer_domain` VARCHAR(150) NULL,
              `device_type` ENUM('desktop', 'mobile', 'tablet', 'bot') NOT NULL DEFAULT 'desktop',
              `duration_seconds` INT UNSIGNED NULL DEFAULT 0,
              `metadata_json` JSON NULL,
              INDEX `idx_analytics_type_time` (`event_type`, `event_time`),
              INDEX `idx_analytics_content` (`content_id`),
              INDEX `idx_analytics_author` (`author_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // 20. Revenue Records & Creator Payout Ledger
            $pdo->exec("CREATE TABLE IF NOT EXISTS `revenue_records` (
              `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `author_id` BIGINT UNSIGNED NOT NULL,
              `amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
              `fee_deducted` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
              `net_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
              `status` ENUM('pending', 'processing', 'paid', 'rejected') NOT NULL DEFAULT 'pending',
              `payout_method` VARCHAR(50) NOT NULL DEFAULT 'paypal',
              `payout_address` VARCHAR(255) NULL,
              `transaction_id` VARCHAR(150) NULL,
              `admin_notes` TEXT NULL,
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `processed_at` DATETIME NULL,
              INDEX `idx_rev_author` (`author_id`),
              INDEX `idx_rev_status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // 21. Check & Alter authors banking, tax, and social columns
            $authCols = array_column($pdo->query("SHOW COLUMNS FROM `authors`")->fetchAll(PDO::FETCH_ASSOC), 'Field');
            if (!in_array('payout_method', $authCols)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `payout_method` VARCHAR(50) NOT NULL DEFAULT 'paypal' AFTER `promo_credits`");
            }
            if (!in_array('paypal_email', $authCols)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `paypal_email` VARCHAR(191) NULL AFTER `payout_method`");
            }
            if (!in_array('bank_name', $authCols)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `bank_name` VARCHAR(150) NULL AFTER `paypal_email`");
            }
            if (!in_array('bank_account_name', $authCols)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `bank_account_name` VARCHAR(150) NULL AFTER `bank_name`");
            }
            if (!in_array('bank_account_number', $authCols)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `bank_account_number` VARCHAR(100) NULL AFTER `bank_account_name`");
            }
            if (!in_array('bank_routing_or_swift', $authCols)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `bank_routing_or_swift` VARCHAR(100) NULL AFTER `bank_account_number`");
            }
            if (!in_array('bank_country', $authCols)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `bank_country` VARCHAR(100) NULL AFTER `bank_routing_or_swift`");
            }
            if (!in_array('crypto_wallet_address', $authCols)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `crypto_wallet_address` VARCHAR(255) NULL AFTER `bank_country`");
            }
            if (!in_array('crypto_network', $authCols)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `crypto_network` VARCHAR(50) NOT NULL DEFAULT 'ERC-20' AFTER `crypto_wallet_address`");
            }
            if (!in_array('tax_id_or_ssn', $authCols)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `tax_id_or_ssn` VARCHAR(100) NULL AFTER `crypto_network`");
            }
            if (!in_array('tax_country', $authCols)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `tax_country` VARCHAR(100) NULL AFTER `tax_id_or_ssn`");
            }
            if (!in_array('website_url', $authCols)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `website_url` VARCHAR(255) NULL AFTER `cover_image_url`");
            }
            if (!in_array('github_url', $authCols)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `github_url` VARCHAR(255) NULL AFTER `website_url`");
            }
            if (!in_array('twitter_url', $authCols)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `twitter_url` VARCHAR(255) NULL AFTER `github_url`");
            }
            if (!in_array('linkedin_url', $authCols)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `linkedin_url` VARCHAR(255) NULL AFTER `twitter_url`");
            }
            if (!in_array('discord_handle', $authCols)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `discord_handle` VARCHAR(100) NULL AFTER `linkedin_url`");
            }
            if (!in_array('can_publish_prompts', $authCols)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `can_publish_prompts` TINYINT(1) NOT NULL DEFAULT 1");
            }
            if (!in_array('can_publish_gpts', $authCols)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `can_publish_gpts` TINYINT(1) NOT NULL DEFAULT 1");
            }
            if (!in_array('can_publish_tools', $authCols)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `can_publish_tools` TINYINT(1) NOT NULL DEFAULT 1");
            }
            if (!in_array('can_use_templates', $authCols)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `can_use_templates` TINYINT(1) NOT NULL DEFAULT 1");
            }
            if (!in_array('is_monetized', $authCols)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `is_monetized` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_featured`");
            }
            if (!in_array('monetization_status', $authCols)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `monetization_status` VARCHAR(50) NOT NULL DEFAULT 'in_progress' AFTER `is_monetized`");
            }
            if (!in_array('monetization_applied_at', $authCols)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `monetization_applied_at` DATETIME NULL AFTER `monetization_status`");
            }
            if (!in_array('monetization_approved_at', $authCols)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `monetization_approved_at` DATETIME NULL AFTER `monetization_applied_at`");
            }
            if (!in_array('follower_count', $authCols)) {
                $pdo->exec("ALTER TABLE `authors` ADD COLUMN `follower_count` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `monetization_approved_at`");
            }

            // 21b. Author Followers Table Engine
            $pdo->exec("CREATE TABLE IF NOT EXISTS `author_followers` (
              `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `author_id` BIGINT UNSIGNED NOT NULL,
              `user_id` BIGINT UNSIGNED NOT NULL,
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              UNIQUE KEY `uk_author_user_follower` (`author_id`, `user_id`),
              INDEX `idx_af_author` (`author_id`),
              INDEX `idx_af_user` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // 22. Check & Alter author_payouts table
            $payCols = array_column($pdo->query("SHOW COLUMNS FROM `author_payouts`")->fetchAll(PDO::FETCH_ASSOC), 'Field');
            if (!in_array('fee_deducted', $payCols)) {
                $pdo->exec("ALTER TABLE `author_payouts` ADD COLUMN `fee_deducted` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `amount_usd`");
            }
            if (!in_array('net_amount', $payCols)) {
                $pdo->exec("ALTER TABLE `author_payouts` ADD COLUMN `net_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `fee_deducted`");
            }
            if (!in_array('receipt_url', $payCols)) {
                $pdo->exec("ALTER TABLE `author_payouts` ADD COLUMN `receipt_url` VARCHAR(255) NULL AFTER `transaction_reference`");
            }

            // 23. Newsletter Issues & Subscriptions Table Engine
            $pdo->exec("CREATE TABLE IF NOT EXISTS `newsletter_issues` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(255) NOT NULL,
                `issue_number` INT UNSIGNED DEFAULT 1,
                `subject_line` VARCHAR(255) NOT NULL,
                `preview_text` VARCHAR(255) DEFAULT '',
                `template_type` VARCHAR(100) DEFAULT 'weekly_digest',
                `body_html` LONGTEXT,
                `status` ENUM('draft', 'scheduled', 'sending', 'sent', 'archived') DEFAULT 'draft',
                `scheduled_at` DATETIME NULL,
                `sent_at` DATETIME NULL,
                `total_recipients` INT UNSIGNED DEFAULT 0,
                `sent_count` INT UNSIGNED DEFAULT 0,
                `failed_count` INT UNSIGNED DEFAULT 0,
                `open_count` INT UNSIGNED DEFAULT 0,
                `click_count` INT UNSIGNED DEFAULT 0,
                `created_by` BIGINT UNSIGNED NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $pdo->exec("CREATE TABLE IF NOT EXISTS `newsletter_subscriptions` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `email` VARCHAR(255) NOT NULL UNIQUE,
                `subscriber_type` VARCHAR(100) DEFAULT 'general',
                `status` ENUM('subscribed', 'unsubscribed', 'pending') DEFAULT 'subscribed',
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $newsCols = array_column($pdo->query("SHOW COLUMNS FROM `newsletter_issues`")->fetchAll(PDO::FETCH_ASSOC), 'Field');
            if (!in_array('sent_count', $newsCols)) {
                $pdo->exec("ALTER TABLE `newsletter_issues` ADD COLUMN `sent_count` INT UNSIGNED DEFAULT 0 AFTER `total_recipients`");
            }
            if (!in_array('failed_count', $newsCols)) {
                $pdo->exec("ALTER TABLE `newsletter_issues` ADD COLUMN `failed_count` INT UNSIGNED DEFAULT 0 AFTER `sent_count`");
            }

            // 24. Content editorial additions: internal_notes, is_pinned, custom_badge
            $contentCols = array_column($pdo->query("SHOW COLUMNS FROM `contents`")->fetchAll(PDO::FETCH_ASSOC), 'Field');
            if (!in_array('internal_notes', $contentCols)) {
                $pdo->exec("ALTER TABLE `contents` ADD COLUMN `internal_notes` LONGTEXT NULL AFTER `editorial_notes`");
            }
            if (!in_array('is_pinned', $contentCols)) {
                $pdo->exec("ALTER TABLE `contents` ADD COLUMN `is_pinned` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`");
                $pdo->exec("ALTER TABLE `contents` ADD INDEX `idx_contents_pinned` (`is_pinned`)");
            }
            if (!in_array('custom_badge', $contentCols)) {
                $pdo->exec("ALTER TABLE `contents` ADD COLUMN `custom_badge` VARCHAR(50) NULL AFTER `is_pinned`");
                $pdo->exec("ALTER TABLE `contents` ADD COLUMN `badge_color` VARCHAR(20) NULL AFTER `custom_badge`");
                $pdo->exec("ALTER TABLE `contents` ADD COLUMN `badge_bg` VARCHAR(40) NULL AFTER `badge_color`");
            }

            // 25. Custom Editorial Badges Engine
            $pdo->exec("CREATE TABLE IF NOT EXISTS `editorial_badges` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(50) NOT NULL UNIQUE,
                `slug` VARCHAR(50) NOT NULL UNIQUE,
                `color` VARCHAR(20) NOT NULL DEFAULT '#38bdf8',
                `bg_color` VARCHAR(40) NOT NULL DEFAULT 'rgba(56,189,248,0.15)',
                `description` VARCHAR(255) NULL,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            // 26. Reading History Multi-Session Re-read Support
            $rhCols = array_column($pdo->query("SHOW COLUMNS FROM `reading_history`")->fetchAll(PDO::FETCH_ASSOC), 'Field');
            if (!in_array('session_id', $rhCols)) {
                $pdo->exec("ALTER TABLE `reading_history` ADD COLUMN `session_id` VARCHAR(64) NULL AFTER `content_id`");
            }
            if (!in_array('session_number', $rhCols)) {
                $pdo->exec("ALTER TABLE `reading_history` ADD COLUMN `session_number` INT NOT NULL DEFAULT 1 AFTER `session_id`");
            }
            if (!in_array('started_at', $rhCols)) {
                $pdo->exec("ALTER TABLE `reading_history` ADD COLUMN `started_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `session_number`");
            }
            if (!in_array('completed_at', $rhCols)) {
                $pdo->exec("ALTER TABLE `reading_history` ADD COLUMN `completed_at` DATETIME NULL AFTER `last_read_at`");
            }
            if (!in_array('status', $rhCols)) {
                $pdo->exec("ALTER TABLE `reading_history` ADD COLUMN `status` ENUM('reading', 'completed', 're_reading') NOT NULL DEFAULT 'reading' AFTER `completed_at`");
            }

            // Ensure maintenance mode default settings exist
            $pdo->exec("INSERT IGNORE INTO `site_settings` (`key_name`, `key_value`) VALUES 
                ('site_maintenance_mode', '0'),
                ('site_maintenance_message', 'TechScout core systems are currently executing scheduled platform upgrades. We will be back online shortly.')");

            // 27. Ensure mail_logs table has sender, credential, and engagement tracking columns
            try {
                $mlCheck = $pdo->query("SHOW TABLES LIKE 'mail_logs'")->fetchAll();
                if (!empty($mlCheck)) {
                    $mlCols = array_column($pdo->query("SHOW COLUMNS FROM `mail_logs`")->fetchAll(PDO::FETCH_ASSOC), 'Field');
                    if (!in_array('from_email', $mlCols)) {
                        $pdo->exec("ALTER TABLE `mail_logs` ADD COLUMN `from_email` VARCHAR(255) NULL AFTER `to_name`");
                    }
                    if (!in_array('from_name', $mlCols)) {
                        $pdo->exec("ALTER TABLE `mail_logs` ADD COLUMN `from_name` VARCHAR(255) NULL AFTER `from_email`");
                    }
                    if (!in_array('credential_name', $mlCols)) {
                        $pdo->exec("ALTER TABLE `mail_logs` ADD COLUMN `credential_name` VARCHAR(255) NULL AFTER `driver`");
                    }
                    if (!in_array('tracking_token', $mlCols)) {
                        $pdo->exec("ALTER TABLE `mail_logs` ADD COLUMN `tracking_token` VARCHAR(64) NULL AFTER `credential_name`, ADD INDEX `idx_ml_token` (`tracking_token`)");
                    }
                    if (!in_array('open_count', $mlCols)) {
                        $pdo->exec("ALTER TABLE `mail_logs` ADD COLUMN `open_count` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `tracking_token`");
                    }
                    if (!in_array('opened_at', $mlCols)) {
                        $pdo->exec("ALTER TABLE `mail_logs` ADD COLUMN `opened_at` DATETIME NULL AFTER `open_count`");
                    }
                    if (!in_array('click_count', $mlCols)) {
                        $pdo->exec("ALTER TABLE `mail_logs` ADD COLUMN `click_count` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `opened_at`");
                    }
                    if (!in_array('clicked_at', $mlCols)) {
                        $pdo->exec("ALTER TABLE `mail_logs` ADD COLUMN `clicked_at` DATETIME NULL AFTER `click_count`");
                    }
                    if (!in_array('last_clicked_url', $mlCols)) {
                        $pdo->exec("ALTER TABLE `mail_logs` ADD COLUMN `last_clicked_url` TEXT NULL AFTER `clicked_at`");
                    }
                    if (!in_array('user_ip', $mlCols)) {
                        $pdo->exec("ALTER TABLE `mail_logs` ADD COLUMN `user_ip` VARCHAR(45) NULL AFTER `last_clicked_url`");
                    }
                    if (!in_array('user_agent', $mlCols)) {
                        $pdo->exec("ALTER TABLE `mail_logs` ADD COLUMN `user_agent` TEXT NULL AFTER `user_ip`");
                    }
                }
            } catch (Throwable $e) {}

            // 28. Auto-heal 12-hour offset for historical data stored when MySQL was running on US Pacific time (-12h from Pakistan)
            try {
                $tzCheck = $pdo->query("SELECT key_value FROM `site_settings` WHERE `key_name` = 'tz_offset_healed_v1' LIMIT 1")->fetch();
                if (empty($tzCheck)) {
                    // One-time repair: add 12 hours to existing records in notifications, mail_logs, and newsletter_delivery_logs
                    $pdo->exec("UPDATE `notifications` SET `created_at` = DATE_ADD(`created_at`, INTERVAL 12 HOUR) WHERE `created_at` < NOW()");
                    $pdo->exec("UPDATE `mail_logs` SET `created_at` = DATE_ADD(`created_at`, INTERVAL 12 HOUR) WHERE `created_at` < NOW()");
                    try {
                        $pdo->exec("UPDATE `newsletter_delivery_logs` SET `dispatched_at` = DATE_ADD(`dispatched_at`, INTERVAL 12 HOUR) WHERE `dispatched_at` < NOW()");
                    } catch (Throwable $e2) {}
                    $pdo->exec("INSERT INTO `site_settings` (`key_name`, `key_value`) VALUES ('tz_offset_healed_v1', '1') ON DUPLICATE KEY UPDATE `key_value` = '1'");
                }
            } catch (Throwable $e) {}

            // 29. Auto-heal notification action_url to remove stale /tech/ or /techscout.n/ subfolder path
            try {
                $pdo->exec("UPDATE `notifications` SET `action_url` = REPLACE(`action_url`, '/tech/', '/') WHERE `action_url` LIKE '%/tech/%'");
                $pdo->exec("UPDATE `notifications` SET `action_url` = REPLACE(`action_url`, 'funwithcoding.free.nf/tech', 'funwithcoding.free.nf') WHERE `action_url` LIKE '%funwithcoding.free.nf/tech%'");
                $pdo->exec("UPDATE `notifications` SET `action_url` = REPLACE(`action_url`, '/techscout.n/', '/') WHERE `action_url` LIKE '%/techscout.n/%'");
                $pdo->exec("UPDATE `notifications` SET `action_url` = REPLACE(`action_url`, 'http://localhost/techscout.n', 'https://funwithcoding.free.nf') WHERE `action_url` LIKE '%http://localhost/techscout.n%'");
            } catch (Throwable $e) {}
        } catch (Throwable $e) {
            // Ignore minor migration conflicts
        }
    }

    public static function query(string $sql, array $params = []): array {
        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Throwable $e) {
            self::renderDetailedError($e, Config::get('db_host'), Config::get('db_port'), Config::get('db_name'), Config::get('db_user'), $sql, $params);
            return [];
        }
    }

    public static function fetch(string $sql, array $params = []): ?array {
        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            $res = $stmt->fetch();
            return $res ? $res : null;
        } catch (Throwable $e) {
            self::renderDetailedError($e, Config::get('db_host'), Config::get('db_port'), Config::get('db_name'), Config::get('db_user'), $sql, $params);
            return null;
        }
    }

    public static function execute(string $sql, array $params = []): bool {
        try {
            $stmt = self::getConnection()->prepare($sql);
            return $stmt->execute($params);
        } catch (Throwable $e) {
            self::renderDetailedError($e, Config::get('db_host'), Config::get('db_port'), Config::get('db_name'), Config::get('db_user'), $sql, $params);
            return false;
        }
    }

    /**
     * Executes SQL statement silently without triggering renderDetailedError or halting script on failure.
     * Ideal for telemetry, logging, and non-critical writes.
     */
    public static function executeSilent(string $sql, array $params = []): bool {
        try {
            $conn = self::getConnection();
            if (!$conn) return false;
            $stmt = $conn->prepare($sql);
            return $stmt->execute($params);
        } catch (Throwable $e) {
            if (class_exists('\\App\\Core\\ErrorLogger')) {
                \App\Core\ErrorLogger::log('DATABASE_SILENT_ERROR', $e->getMessage() . ($sql ? " | SQL: " . substr($sql, 0, 200) : ""));
            }
            return false;
        }
    }

    /**
     * Fetches single row silently without triggering renderDetailedError on failure.
     */
    public static function fetchSilent(string $sql, array $params = []): ?array {
        try {
            $conn = self::getConnection();
            if (!$conn) return null;
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $res = $stmt->fetch();
            return $res ? $res : null;
        } catch (Throwable $e) {
            return null;
        }
    }

    public static function lastInsertId(): string {
        return self::getConnection()->lastInsertId();
    }

    private static function renderDetailedError(Throwable $e, string $host, string $port, string $dbname, string $user, ?string $sql = null, ?array $params = null): void {
        http_response_code(500);
        $message = $e->getMessage();
        $code = $e->getCode();
        $trace = $e->getTraceAsString();
        $file = $e->getFile();
        $line = $e->getLine();

        // Interpret common MySQL errors with diagnostic tips
        $diagnostic = "An unexpected database exception occurred.";
        if (str_contains($message, '1005') || str_contains($message, 'errno: 150') || str_contains($message, 'Foreign key')) {
            $diagnostic = "<strong>Foreign Key Constraint Mismatch (errno 150):</strong> Existing tables in <code>{$dbname}</code> contain legacy column types (e.g. INT vs BIGINT UNSIGNED). Use the Auto-Repair button below to rebuild the normalized schema.";
        } elseif (str_contains($message, 'Connection refused') || str_contains($message, '2002')) {
            $diagnostic = "<strong>MySQL Server Offline:</strong> Unable to connect to MySQL on <code>{$host}:{$port}</code>. Please make sure MySQL is running in your XAMPP Control Panel.";
        } elseif (str_contains($message, 'Access denied') || str_contains($message, '1045')) {
            $diagnostic = "<strong>Authentication Failure:</strong> The credentials for user <code>{$user}</code> were rejected. Check your <code>app/Core/Config.php</code> credentials.";
        } elseif (str_contains($message, 'Table') && str_contains($message, "doesn't exist")) {
            $diagnostic = "<strong>Missing Table:</strong> A required database table is missing from <code>{$dbname}</code>. Click Auto-Repair to initialize the schema.";
        }

        // Auto-log to ErrorLogger
        if (class_exists('\\App\\Core\\ErrorLogger')) {
            \App\Core\ErrorLogger::log('DATABASE_ERROR', $message . ($sql ? " | SQL: " . substr($sql, 0, 200) : ""), $file, $line, $trace);
        }

        // Check if JSON response is expected
        $isJson = (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'json'))
            || str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api')
            || str_contains($_SERVER['REQUEST_URI'] ?? '', '/detail/')
            || str_contains($_SERVER['REQUEST_URI'] ?? '', '/json')
            || !empty($_SERVER['HTTP_X_REQUESTED_WITH']);

        if ($isJson) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'error' => $message,
                'diagnostic' => strip_tags($diagnostic),
                'sql' => $sql
            ]);
            exit;
        }

        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>Database Diagnostic & Error HUD | TechScout</title>
          <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=JetBrains+Mono:wght@400;600&family=Outfit:wght@700;800&display=swap" rel="stylesheet">
          <style>
            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
            body { background: #05070b; color: #94a3b8; font-family: 'Inter', sans-serif; padding: 2.5rem 1rem; line-height: 1.6; }
            .diag-container { max-width: 960px; margin: 0 auto; background: #090d16; border: 1px solid rgba(239, 68, 68, 0.4); border-radius: 16px; padding: 2.5rem; box-shadow: 0 10px 40px rgba(0,0,0,0.8), 0 0 30px rgba(239,68,68,0.2); }
            .diag-header { display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(148,163,184,0.12); padding-bottom: 1.5rem; margin-bottom: 2rem; }
            .diag-title { font-family: 'Outfit', sans-serif; font-size: 1.75rem; color: #f8fafc; font-weight: 800; }
            .diag-badge { display: inline-block; padding: 0.35rem 0.8rem; background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.4); border-radius: 999px; font-weight: 700; font-size: 0.8rem; }
            .alert-box { background: rgba(239,68,68,0.08); border-left: 4px solid #ef4444; padding: 1.25rem; border-radius: 0 8px 8px 0; margin-bottom: 2rem; color: #fecaca; font-size: 0.95rem; }
            .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
            .info-card { background: #0d131f; border: 1px solid rgba(148,163,184,0.08); border-radius: 8px; padding: 1rem; }
            .info-label { font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 600; }
            .info-val { font-size: 1rem; font-weight: 700; color: #f8fafc; margin-top: 0.2rem; font-family: 'JetBrains Mono', monospace; }
            .code-block { background: #05070b; border: 1px solid rgba(148,163,184,0.1); border-radius: 8px; padding: 1.25rem; font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; color: #e2e8f0; overflow-x: auto; margin-bottom: 2rem; white-space: pre-wrap; word-break: break-all; }
            .btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem 1.5rem; font-size: 0.95rem; font-weight: 700; border-radius: 8px; text-decoration: none; cursor: pointer; border: none; transition: all 0.2s ease; }
            .btn-primary { background: linear-gradient(135deg, #10b981, #059669); color: #ffffff; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4); }
            .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(16, 185, 129, 0.6); }
            .btn-outline { background: transparent; border: 1px solid rgba(148,163,184,0.2); color: #f8fafc; }
          </style>
        </head>
        <body>
          <div class="diag-container">
            <div class="diag-header">
              <div>
                <div class="diag-title">Database Exception Encountered</div>
                <div style="font-size: 0.85rem; color: #64748b; margin-top: 0.25rem;">TechScout Database Diagnostics & Recovery Engine</div>
              </div>
              <span class="diag-badge">SQLSTATE <?= htmlspecialchars((string)$code) ?></span>
            </div>

            <div class="alert-box">
              <div style="font-weight: 700; font-size: 1.05rem; margin-bottom: 0.4rem; color: #ffffff;">Diagnostic Insight:</div>
              <?= $diagnostic ?>
            </div>

            <div class="info-grid">
              <div class="info-card">
                <div class="info-label">Database Host & Port</div>
                <div class="info-val"><?= htmlspecialchars("{$host}:{$port}") ?></div>
              </div>
              <div class="info-card">
                <div class="info-label">Target Database</div>
                <div class="info-val"><?= htmlspecialchars($dbname) ?></div>
              </div>
              <div class="info-card">
                <div class="info-label">Connected User</div>
                <div class="info-val"><?= htmlspecialchars($user) ?></div>
              </div>
              <div class="info-card">
                <div class="info-label">Source Location</div>
                <div class="info-val" style="font-size: 0.85rem;"><?= basename($file) ?>:<?= $line ?></div>
              </div>
            </div>

            <?php if ($sql): ?>
              <div style="font-weight: 600; color: #f8fafc; margin-bottom: 0.5rem; font-size: 0.9rem;">Executed SQL Statement:</div>
              <div class="code-block" style="color: #38bdf8;"><?= htmlspecialchars($sql) ?></div>
            <?php endif; ?>

            <div style="font-weight: 600; color: #f8fafc; margin-bottom: 0.5rem; font-size: 0.9rem;">Raw Database Error Message:</div>
            <div class="code-block" style="color: #f87171;"><?= htmlspecialchars($message) ?></div>

            <div style="font-weight: 600; color: #f8fafc; margin-bottom: 0.5rem; font-size: 0.9rem;">Execution Stack Trace:</div>
            <div class="code-block" style="max-height: 220px; font-size: 0.75rem; color: #94a3b8;"><?= htmlspecialchars($trace) ?></div>

            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1.5rem; border-top: 1px solid rgba(148,163,184,0.12); flex-wrap: wrap; gap: 1rem;">
              <a href="?db_action=repair" class="btn btn-primary">
                ⚡ Auto-Repair & Reinitialize Schema (Fixes Foreign Keys)
              </a>
              <button onclick="location.reload()" class="btn btn-outline">
                🔄 Retry Query
              </button>
            </div>
          </div>
        </body>
        </html>
        <?php
        exit;
    }
}
