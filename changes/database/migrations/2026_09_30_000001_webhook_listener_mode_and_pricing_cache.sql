-- TechScout Upgrade: Ingestion Webhook Listener Mode & Live Pricing Cache
-- Migration: 2026_09_30_000001_webhook_listener_mode_and_pricing_cache.sql

-- 1. Ensure ingestion_mode column exists on incoming_webhooks
ALTER TABLE `incoming_webhooks` 
  ADD COLUMN IF NOT EXISTS `ingestion_mode` VARCHAR(32) NOT NULL DEFAULT 'live_ingest' AFTER `is_active`;

-- 2. Seed / Update fallback pricing rates in site_settings
INSERT INTO `site_settings` (`key_name`, `key_value`, `created_at`, `updated_at`) 
VALUES 
  ('pricing_fallback_btc', '84292.39', NOW(), NOW()),
  ('pricing_fallback_eth', '2687.66', NOW(), NOW()),
  ('pricing_fallback_sol', '119.92', NOW(), NOW()),
  ('pricing_fallback_usdt', '1.00', NOW(), NOW())
ON DUPLICATE KEY UPDATE `key_value` = VALUES(`key_value`), `updated_at` = NOW();
