ALTER TABLE `users` ADD COLUMN `disable_ads` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_flagged_suspicious`;
