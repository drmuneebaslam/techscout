-- ============================================================================
-- TechScout Universal Database Upgrade Script (v2.0 Telemetry & Auth Fix)
-- Description: 
--   1. Adds `verification_code` and updates `status` ENUM on `users` table
--      to resolve SQLSTATE[42S22] column not found during registration.
--   2. Extends `system_error_logs` with comprehensive request, device, headers,
--      user context, and location telemetry columns.
-- Compatible with: MySQL 5.7+, MySQL 8.0+, MariaDB 10.3+
-- ============================================================================

-- Step 1: Upgrade Users Table for Email Verification OTP PIN
ALTER TABLE `users` 
  MODIFY COLUMN `status` ENUM('pending_verification','unverified','active','suspended','banned','deleted') NOT NULL DEFAULT 'pending_verification',
  ADD COLUMN IF NOT EXISTS `verification_code` VARCHAR(20) DEFAULT NULL AFTER `suspended_until`;

-- (For MySQL versions prior to 8.0.19 where 'IF NOT EXISTS' in ADD COLUMN is not supported, run this if the above throws syntax error):
-- ALTER TABLE `users` MODIFY COLUMN `status` ENUM('pending_verification','unverified','active','suspended','banned','deleted') NOT NULL DEFAULT 'pending_verification';
-- ALTER TABLE `users` ADD COLUMN `verification_code` VARCHAR(20) DEFAULT NULL AFTER `suspended_until`;

-- Step 2: Upgrade system_error_logs for Comprehensive Forensics & Telemetry
ALTER TABLE `system_error_logs`
  MODIFY COLUMN `error_type` VARCHAR(100) NOT NULL DEFAULT 'ERROR',
  ADD COLUMN IF NOT EXISTS `request_data` LONGTEXT NULL AFTER `request_method`,
  ADD COLUMN IF NOT EXISTS `request_headers` LONGTEXT NULL AFTER `request_data`,
  ADD COLUMN IF NOT EXISTS `user_details` LONGTEXT NULL AFTER `user_id`,
  ADD COLUMN IF NOT EXISTS `location_details` LONGTEXT NULL AFTER `ip_address`,
  ADD COLUMN IF NOT EXISTS `device_details` LONGTEXT NULL AFTER `location_details`;

-- (Fallback for MySQL 5.7 / MariaDB without IF NOT EXISTS):
-- ALTER TABLE `system_error_logs` ADD COLUMN `request_data` LONGTEXT NULL AFTER `request_method`;
-- ALTER TABLE `system_error_logs` ADD COLUMN `request_headers` LONGTEXT NULL AFTER `request_data`;
-- ALTER TABLE `system_error_logs` ADD COLUMN `user_details` LONGTEXT NULL AFTER `user_id`;
-- ALTER TABLE `system_error_logs` ADD COLUMN `location_details` LONGTEXT NULL AFTER `ip_address`;
-- ALTER TABLE `system_error_logs` ADD COLUMN `device_details` LONGTEXT NULL AFTER `location_details`;

-- ============================================================================
-- Verification query:
-- DESCRIBE `users`;
-- DESCRIBE `system_error_logs`;
-- ============================================================================
