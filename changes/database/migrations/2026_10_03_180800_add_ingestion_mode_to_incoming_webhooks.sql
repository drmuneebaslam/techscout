ALTER TABLE `incoming_webhooks` ADD COLUMN `ingestion_mode` VARCHAR(50) NOT NULL DEFAULT 'live_ingest';
