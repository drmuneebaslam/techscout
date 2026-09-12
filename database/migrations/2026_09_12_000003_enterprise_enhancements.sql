-- Migration: 2026_09_12_000003_enterprise_enhancements.sql
-- Enterprise Architecture & Enhancements Upgrade

-- 1. Add TOTP Two-Factor Authentication fields to users if missing
ALTER TABLE users ADD COLUMN IF NOT EXISTS totp_secret VARCHAR(64) NULL AFTER password_hash;
ALTER TABLE users ADD COLUMN IF NOT EXISTS totp_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER totp_secret;
ALTER TABLE users ADD COLUMN IF NOT EXISTS totp_recovery_codes TEXT NULL AFTER totp_enabled;

-- 2. Cron Execution Logs table [TS-INF-10]
CREATE TABLE IF NOT EXISTS cron_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    job_name VARCHAR(100) NOT NULL,
    status ENUM('success', 'failed', 'running') NOT NULL DEFAULT 'success',
    output LONGTEXT NULL,
    duration_ms INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_job_created (job_name, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Insert or update default sponsorship and ad settings
INSERT INTO site_settings (key_name, key_value) VALUES
('ad_tier_7_price', '49'),
('ad_tier_30_price', '149'),
('ad_tier_90_price', '299'),
('ad_direct_link_url', 'https://omg10.com/4/11737440'),
('ad_popunder_url', 'https://omg10.com/4/11737440'),
('site_maintenance_allowed_ips', '127.0.0.1, ::1'),
('site_maintenance_countdown_minutes', '45'),
('site_maintenance_message', 'TechScout core engineering systems are currently executing scheduled platform upgrades.')
ON DUPLICATE KEY UPDATE key_value = VALUES(key_value);
