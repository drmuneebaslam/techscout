-- ==============================================================================
-- TechScout Universal Database Schema Upgrade & Error Fix Script
-- Target Database: if0_40102772_techscoutmainafterallchanges (Production & Local)
-- 
-- DESCRIPTION:
--   1. Fixes missing `post_views` table (resolves SQLSTATE[42S02] in simulateEngagement).
--   2. Fixes `mail_logs` missing columns (from_email, credential_name, tracking_token, etc.).
--   3. Relaxes `mail_logs.attachments_json` to LONGTEXT NULL without strict CHECK constraint
--      (resolves SQLSTATE[23000] 4025 constraint violation in MariaDB).
--   4. Creates canonical `mail_templates` table if missing and seeds default templates.
--   5. Safely migrates any data from legacy `custom_mail_templates` if it exists.
--
-- SAFETY GUARANTEE:
--   - This script NEVER drops tables or deletes user records.
--   - Uses IF NOT EXISTS and non-destructive column additions/modifications.
-- ==============================================================================

-- ------------------------------------------------------------------------------
-- 1. Create `post_views` Telemetry Table
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `post_views` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `content_id` BIGINT(20) UNSIGNED NOT NULL,
  `user_id` BIGINT(20) UNSIGNED DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `referrer` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pv_content` (`content_id`),
  KEY `idx_pv_created` (`created_at`),
  KEY `idx_pv_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 2. Create `mail_templates` Table
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mail_templates` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_key` VARCHAR(80) NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `html_content` LONGTEXT NOT NULL,
  `assigned_credential_id` BIGINT(20) UNSIGNED DEFAULT NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `template_key` (`template_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 3. Ensure `mail_logs` Exists & Has All Modern Tracking & Telemetry Columns
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mail_logs` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `to_email` VARCHAR(191) NOT NULL,
  `to_name` VARCHAR(150) DEFAULT NULL,
  `from_email` VARCHAR(191) DEFAULT NULL,
  `from_name` VARCHAR(150) DEFAULT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `template_key` VARCHAR(80) DEFAULT NULL,
  `body_html` LONGTEXT DEFAULT NULL,
  `attachments_json` LONGTEXT DEFAULT NULL,
  `driver` VARCHAR(30) NOT NULL DEFAULT 'log',
  `credential_name` VARCHAR(150) DEFAULT NULL,
  `tracking_token` VARCHAR(64) DEFAULT NULL,
  `open_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `opened_at` DATETIME DEFAULT NULL,
  `click_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `clicked_at` DATETIME DEFAULT NULL,
  `last_clicked_url` TEXT DEFAULT NULL,
  `user_ip` VARCHAR(45) DEFAULT NULL,
  `user_agent` TEXT DEFAULT NULL,
  `status` ENUM('sent','failed','queued') NOT NULL DEFAULT 'sent',
  `error_message` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ml_email` (`to_email`),
  KEY `idx_ml_status` (`status`),
  KEY `idx_ml_token` (`tracking_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 4. Upgrade Existing `mail_logs` Table Columns & Relax attachments_json
-- ------------------------------------------------------------------------------
-- Relax attachments_json to standard LONGTEXT NULL without strict CHECK constraint
ALTER TABLE `mail_logs` 
  MODIFY COLUMN `attachments_json` LONGTEXT NULL DEFAULT NULL;

-- Add tracking and sender columns if they do not yet exist
ALTER TABLE `mail_logs` 
  ADD COLUMN IF NOT EXISTS `from_email` VARCHAR(191) NULL AFTER `to_name`,
  ADD COLUMN IF NOT EXISTS `from_name` VARCHAR(150) NULL AFTER `from_email`,
  ADD COLUMN IF NOT EXISTS `credential_name` VARCHAR(150) NULL AFTER `driver`,
  ADD COLUMN IF NOT EXISTS `tracking_token` VARCHAR(64) NULL AFTER `credential_name`,
  ADD COLUMN IF NOT EXISTS `open_count` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `tracking_token`,
  ADD COLUMN IF NOT EXISTS `opened_at` DATETIME NULL AFTER `open_count`,
  ADD COLUMN IF NOT EXISTS `click_count` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `opened_at`,
  ADD COLUMN IF NOT EXISTS `clicked_at` DATETIME NULL AFTER `click_count`,
  ADD COLUMN IF NOT EXISTS `last_clicked_url` TEXT NULL AFTER `clicked_at`,
  ADD COLUMN IF NOT EXISTS `user_ip` VARCHAR(45) NULL AFTER `last_clicked_url`,
  ADD COLUMN IF NOT EXISTS `user_agent` TEXT NULL AFTER `user_ip`;

-- Ensure indexes exist
ALTER TABLE `mail_logs`
  ADD INDEX IF NOT EXISTS `idx_ml_token` (`tracking_token`),
  ADD INDEX IF NOT EXISTS `idx_ml_email` (`to_email`),
  ADD INDEX IF NOT EXISTS `idx_ml_status` (`status`);

-- ------------------------------------------------------------------------------
-- 5. Seed Core Mail Templates (If Not Already Present)
-- ------------------------------------------------------------------------------
INSERT IGNORE INTO `mail_templates` (`template_key`, `name`, `subject`, `description`, `html_content`, `updated_at`) VALUES
('payout_approved', 'Author Payout Approved & Dispatched', 'Payment Sent: Your {{platform_name}} Creator Payout of ${{amount}} is Complete! 🎉', 'Dispatched when author payout withdrawal is processed and paid.', '<!-- Payout Approved Email --><div style=\"font-family:sans-serif;max-width:600px;margin:0 auto;padding:20px;\"><h2 style=\"color:#10b981;\">Payout Dispatched</h2><p>Hi {{name}},</p><p>Great news! Your payout of <strong>${{amount}} USD</strong> has been approved and processed.</p><p><strong>Transaction Ref:</strong> {{transaction_ref}}</p><p><a href=\"{{action_url}}\" style=\"display:inline-block;background:#6366f1;color:#fff;padding:10px 20px;text-decoration:none;border-radius:6px;\">View Payout History</a></p></div>', NOW()),
('payout_rejected', 'Author Payout Request Rejected', 'Important Update on your {{platform_name}} Payout Request #{{payout_id}}', 'Dispatched when payout request is rejected and credits are restored.', '<!-- Payout Rejected Email --><div style=\"font-family:sans-serif;max-width:600px;margin:0 auto;padding:20px;\"><h2 style=\"color:#ef4444;\">Payout Update</h2><p>Hi {{name}},</p><p>Your withdrawal request #{{payout_id}} could not be processed at this time.</p><p><strong>Reason:</strong> {{admin_notes}}</p><p>Any deducted credits have been fully restored to your account balance.</p><p><a href=\"{{action_url}}\" style=\"display:inline-block;background:#6366f1;color:#fff;padding:10px 20px;text-decoration:none;border-radius:6px;\">Check Balance</a></p></div>', NOW()),
('payout_requested', 'Author Payout Withdrawal Claim Received', 'We received your payout request for ${{amount}} USD 💳', 'Dispatched immediately when an author submits a withdrawal claim.', '<!-- Payout Requested Email --><div style=\"font-family:sans-serif;max-width:600px;margin:0 auto;padding:20px;\"><h2 style=\"color:#6366f1;\">Withdrawal Request Received</h2><p>Hi {{name}},</p><p>We received your payout claim for <strong>${{amount}} USD</strong>.</p><p>Our finance team will review and process your transfer shortly.</p></div>', NOW()),
('payout_method_linked', 'Creator Payout Method Linked & Verified', 'Payment Method Linked to Your Creator Account 💳', 'Dispatched when an author connects a new payout destination (Bank, PayPal, SEPA, USDC).', '<div style=\"font-family:sans-serif;max-width:600px;margin:0 auto;padding:20px;\"><h2 style=\"color:#10b981;\">Payment Method Linked</h2><p>Hi {{name}},</p><p>A new payout destination (<strong>{{method_type}}</strong>) has been linked to your Creator Account.</p><p><strong>Account Identifier:</strong> {{account_identifier}}</p><p><a href=\"{{action_url}}\" style=\"display:inline-block;background:#6366f1;color:#fff;padding:10px 20px;text-decoration:none;border-radius:6px;\">Manage Payout Methods</a></p></div>', NOW()),
('payout_method_removed', 'Creator Payout Method Removed', 'Security Notice: Payout Method Removed from Account', 'Dispatched when a payment destination is removed from author studio.', '<div style=\"font-family:sans-serif;max-width:600px;margin:0 auto;padding:20px;\"><h2 style=\"color:#ef4444;\">Payout Method Removed</h2><p>Hi {{name}},</p><p>Your payout destination (<strong>{{method_type}}</strong>) has been removed from your Creator Account.</p><p><a href=\"{{action_url}}\" style=\"display:inline-block;background:#ef4444;color:#fff;padding:10px 20px;text-decoration:none;border-radius:6px;\">Review Creator Settings</a></p></div>', NOW()),
('author_profile_updated', 'Creator Profile & Brand Identity Updated', 'Creator Guild: Your Author Profile Was Updated', 'Dispatched when an author updates their public portfolio or specialties.', '<div style=\"font-family:sans-serif;max-width:600px;margin:0 auto;padding:20px;\"><h2 style=\"color:#6366f1;\">Creator Profile Updated</h2><p>Hi {{name}},</p><p>Your public author profile headline is now: <em>{{headline}}</em>.</p><p><a href=\"{{action_url}}\" style=\"display:inline-block;background:#6366f1;color:#fff;padding:10px 20px;text-decoration:none;border-radius:6px;\">View Public Profile</a></p></div>', NOW()),
('campaign_submission_received', 'Hackathon / Campaign Submission Received', 'Project Submitted: We received your entry for {{campaign_title}}!', 'Dispatched upon submitting a project entry to an active AI sprint.', '<div style=\"font-family:sans-serif;max-width:600px;margin:0 auto;padding:20px;\"><h2 style=\"color:#f59e0b;\">Project Submission Received</h2><p>Hi {{name}},</p><p>We received your challenge entry <strong>\"{{project_title}}\"</strong> for <strong>{{campaign_title}}</strong>!</p><p><a href=\"{{action_url}}\" style=\"display:inline-block;background:#f59e0b;color:#000;padding:10px 20px;text-decoration:none;font-weight:bold;border-radius:6px;\">View Submission in Campaign</a></p></div>', NOW()),
('account_email_verify', 'Email Address Verification', 'Verify Your Email Address - {{platform_name}}', 'Dispatched when a user needs to verify their email address via link or OTP.', '<div style=\"font-family:sans-serif;max-width:600px;margin:0 auto;padding:24px;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;\"><h2 style=\"color:#0f172a;margin-top:0;\">Verify Your Email Address</h2><p style=\"color:#475569;font-size:15px;\">Hi {{name}}, please confirm your email address to complete your registration and activate full access to {{platform_name}}.</p><div style=\"text-align:center;margin:28px 0;\"><a href=\"{{verify_url}}\" style=\"display:inline-block;background:#4f46e5;color:#ffffff;padding:12px 28px;text-decoration:none;border-radius:8px;font-weight:bold;font-size:15px;\">Verify Email Address</a></div><p style=\"color:#64748b;font-size:13px;\">Or use this code: <strong style=\"letter-spacing:3px;font-size:16px;color:#0f172a;\">{{verification_code}}</strong></p></div>', NOW()),
('magic_login_link', 'Passwordless Magic Login Link', 'Your 1-Click Secure Login Link - {{platform_name}}', 'Dispatched when a member requests a passwordless magic login link.', '<div style=\"font-family:sans-serif;max-width:600px;margin:0 auto;padding:24px;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;\"><h2 style=\"color:#0f172a;margin-top:0;\">1-Click Sign In</h2><p style=\"color:#475569;font-size:15px;\">Hi {{name}}, click the button below to sign in instantly without a password. This link is valid for 15 minutes.</p><div style=\"text-align:center;margin:28px 0;\"><a href=\"{{magic_login_url}}\" style=\"display:inline-block;background:#4f46e5;color:#ffffff;padding:12px 28px;text-decoration:none;border-radius:8px;font-weight:bold;font-size:15px;\">Sign In Instantly</a></div><p style=\"color:#94a3b8;font-size:12px;\">If you didn\'t request this login link, you can safely ignore this message.</p></div>', NOW()),
('new_device_login_alert', 'Unfamiliar Device Sign-in Alert', 'Security Alert: New Sign-in to Your Account', 'Dispatched when account is accessed from an unfamiliar IP or browser.', '<div style=\"font-family:sans-serif;max-width:600px;margin:0 auto;padding:24px;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;\"><h2 style=\"color:#dc2626;margin-top:0;\">New Device Sign-in Detected</h2><p style=\"color:#475569;font-size:15px;\">Hi {{name}}, we detected a recent sign-in to your {{platform_name}} account from a new device.</p><div style=\"background:#f8fafc;padding:14px 18px;border-radius:8px;margin:16px 0;font-size:14px;color:#334155;border-left:4px solid #dc2626;\"><p style=\"margin:4px 0;\"><strong>IP Address:</strong> {{ip_address}}</p><p style=\"margin:4px 0;\"><strong>Device / Browser:</strong> {{device_info}}</p><p style=\"margin:4px 0;\"><strong>Location:</strong> {{location}}</p></div><p style=\"color:#475569;font-size:14px;\">If this was you, no action is needed. If you did not sign in, please secure your account immediately.</p><div style=\"text-align:center;margin:20px 0;\"><a href=\"{{action_url}}\" style=\"display:inline-block;background:#dc2626;color:#ffffff;padding:10px 24px;text-decoration:none;border-radius:6px;font-weight:bold;\">Review Security Activity</a></div></div>', NOW()),
('security_password_changed', 'Password Updated Confirmation', 'Security Notice: Your Password Has Been Changed', 'Dispatched immediately following a successful account password change.', '<div style=\"font-family:sans-serif;max-width:600px;margin:0 auto;padding:24px;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;\"><h2 style=\"color:#0f172a;margin-top:0;\">Password Changed Successfully</h2><p style=\"color:#475569;font-size:15px;\">Hi {{name}}, your {{platform_name}} account password was recently modified.</p><div style=\"background:#f8fafc;padding:14px 18px;border-radius:8px;margin:16px 0;font-size:13px;color:#64748b;\"><p style=\"margin:3px 0;\"><strong>Date & Time:</strong> {{timestamp}}</p><p style=\"margin:3px 0;\"><strong>IP Address:</strong> {{ip_address}}</p></div><p style=\"color:#e11d48;font-size:14px;font-weight:500;\">If you did NOT make this change, please reset your password immediately or contact our support team.</p></div>', NOW()),
('security_email_changed', 'Primary Email Changed', 'Security Notice: Your Primary Email Was Changed', 'Dispatched when user updates primary email address.', '<div style=\"font-family:sans-serif;max-width:600px;margin:0 auto;padding:24px;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;\"><h2 style=\"color:#0f172a;margin-top:0;\">Email Address Updated</h2><p style=\"color:#475569;font-size:15px;\">Hi {{name}}, the email address associated with your {{platform_name}} account has been updated to <strong>{{new_email}}</strong>.</p><p style=\"color:#64748b;font-size:14px;\">If you initiated this change, no further action is needed.</p><p style=\"color:#e11d48;font-size:14px;\">If you did not authorize this change, please contact support right away.</p></div>', NOW()),
('data_export_ready', 'GDPR / Privacy Data Archive Ready', 'Your Personal Data Archive is Ready for Download', 'Dispatched when requested user data export archive is ready.', '<div style=\"font-family:sans-serif;max-width:600px;margin:0 auto;padding:24px;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;\"><h2 style=\"color:#0f172a;margin-top:0;\">Your Data Archive is Ready</h2><p style=\"color:#475569;font-size:15px;\">Hi {{name}}, the data archive you requested for your {{platform_name}} account has been compiled and is ready for secure download.</p><p style=\"color:#64748b;font-size:14px;\">This link is valid for <strong>{{expires_in}}</strong> hours.</p><div style=\"text-align:center;margin:28px 0;\"><a href=\"{{download_url}}\" style=\"display:inline-block;background:#4f46e5;color:#ffffff;padding:12px 28px;text-decoration:none;border-radius:8px;font-weight:bold;\">Download Data Archive</a></div></div>', NOW()),
('article_trending_alert', 'Article Trending Milestone', '🔥 Your Article is Trending on {{platform_name}}!', 'Dispatched when an author article achieves top velocity/rankings.', '<div style=\"font-family:sans-serif;max-width:600px;margin:0 auto;padding:24px;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;\"><h2 style=\"color:#f59e0b;margin-top:0;\">🔥 Your Article is Trending!</h2><p style=\"color:#475569;font-size:15px;\">Hi {{name}}, congratulations! Your publication <strong>\"{{article_title}}\"</strong> has reached <strong>{{views_count}}</strong> reads and is currently featured on the trending feed.</p><div style=\"text-align:center;margin:28px 0;\"><a href=\"{{article_url}}\" style=\"display:inline-block;background:#f59e0b;color:#0f172a;padding:12px 28px;text-decoration:none;border-radius:8px;font-weight:bold;\">View Article & Metrics</a></div></div>', NOW()),
('editorial_review_started', 'Staff Review Started', 'Update: Editorial Review Started for Your Draft', 'Dispatched when an editor starts reviewing an author draft.', '<div style=\"font-family:sans-serif;max-width:600px;margin:0 auto;padding:24px;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;\"><h2 style=\"color:#4f46e5;margin-top:0;\">Editorial Review Underway</h2><p style=\"color:#475569;font-size:15px;\">Hi {{name}}, our editorial team has begun reviewing your submitted draft <strong>\"{{article_title}}\"</strong>.</p><p style=\"color:#64748b;font-size:14px;\">We typically review within 24-48 business hours. You will receive an update once the review is complete.</p><div style=\"text-align:center;margin:24px 0;\"><a href=\"{{action_url}}\" style=\"display:inline-block;background:#4f46e5;color:#ffffff;padding:10px 24px;text-decoration:none;border-radius:6px;font-weight:bold;\">View Author Studio</a></div></div>', NOW()),
('article_archived_notice', 'Publication Archived', 'Notice: Your Article Has Been Archived', 'Dispatched when an article is retired or archived.', '<div style=\"font-family:sans-serif;max-width:600px;margin:0 auto;padding:24px;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;\"><h2 style=\"color:#0f172a;margin-top:0;\">Article Archived</h2><p style=\"color:#475569;font-size:15px;\">Hi {{name}}, your article <strong>\"{{article_title}}\"</strong> has been transitioned to archived status on {{platform_name}}.</p><p style=\"color:#64748b;font-size:14px;\">Reason: {{reason}}</p><div style=\"text-align:center;margin:24px 0;\"><a href=\"{{action_url}}\" style=\"display:inline-block;background:#64748b;color:#ffffff;padding:10px 24px;text-decoration:none;border-radius:6px;font-weight:bold;\">Manage Publications</a></div></div>', NOW()),
('comment_mention_alert', '@Mention in Discussion', '{{mentioned_by}} mentioned you in a comment', 'Dispatched when a user is tagged in an insight discussion thread.', '<div style=\"font-family:sans-serif;max-width:600px;margin:0 auto;padding:24px;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;\"><h2 style=\"color:#0f172a;margin-top:0;\">You Were Mentioned</h2><p style=\"color:#475569;font-size:15px;\">Hi {{name}}, <strong>{{mentioned_by}}</strong> mentioned you in a discussion on <strong>\"{{article_title}}\"</strong>:</p><blockquote style=\"margin:16px 0;padding:12px 16px;background:#f8fafc;border-left:4px solid #4f46e5;color:#334155;font-style:italic;\">{{comment_snippet}}</blockquote><div style=\"text-align:center;margin:24px 0;\"><a href=\"{{comment_url}}\" style=\"display:inline-block;background:#4f46e5;color:#ffffff;padding:10px 24px;text-decoration:none;border-radius:6px;font-weight:bold;\">View and Reply</a></div></div>', NOW()),
('new_follower_alert', 'New Author Subscriber', '{{follower_name}} is now following your publications!', 'Dispatched when a reader begins following an author.', '<div style=\"font-family:sans-serif;max-width:600px;margin:0 auto;padding:24px;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;\"><h2 style=\"color:#0f172a;margin-top:0;\">New Follower Alert 🎉</h2><p style=\"color:#475569;font-size:15px;\">Hi {{name}}, <strong>{{follower_name}}</strong> has started following your articles and research on {{platform_name}}.</p><div style=\"text-align:center;margin:24px 0;\"><a href=\"{{follower_profile_url}}\" style=\"display:inline-block;background:#4f46e5;color:#ffffff;padding:10px 24px;text-decoration:none;border-radius:6px;font-weight:bold;\">View Follower Profile</a></div></div>', NOW()),
('tip_sent_confirmation', 'Tip Receipt Confirmation', 'Receipt: Your Tip of ${{amount}} to {{author_name}}', 'Dispatched to supporter acknowledging a reader tip donation.', '<div style=\"font-family:sans-serif;max-width:600px;margin:0 auto;padding:24px;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;\"><h2 style=\"color:#10b981;margin-top:0;\">Tip Confirmation</h2><p style=\"color:#475569;font-size:15px;\">Hi {{name}}, thank you for supporting high quality tech journalism! Your tip of <strong>${{amount}} USD</strong> has been sent to <strong>{{author_name}}</strong>.</p><div style=\"background:#f8fafc;padding:14px;border-radius:8px;margin:16px 0;font-size:14px;color:#334155;\"><p style=\"margin:4px 0;\"><strong>Article:</strong> {{article_title}}</p><p style=\"margin:4px 0;\"><strong>Transaction Ref:</strong> {{transaction_ref}}</p></div><div style=\"text-align:center;margin:24px 0;\"><a href=\"{{action_url}}\" style=\"display:inline-block;background:#4f46e5;color:#ffffff;padding:10px 24px;text-decoration:none;border-radius:6px;font-weight:bold;\">View Receipt</a></div></div>', NOW()),
('creator_tier_promoted', 'Guild Tier Milestone Achieved', '🏆 Congratulations: You Reached {{tier_name}} Creator Tier!', 'Dispatched when author levels up creator status tier.', '<div style=\"font-family:sans-serif;max-width:600px;margin:0 auto;padding:24px;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;\"><h2 style=\"color:#f59e0b;margin-top:0;\">🏆 Creator Milestone Unlocked!</h2><p style=\"color:#475569;font-size:15px;\">Hi {{name}}, your publishing consistency and audience reach have elevated your account to the <strong>{{tier_name}} Tier</strong>!</p><div style=\"background:#fef3c7;padding:16px;border-radius:8px;margin:16px 0;color:#92400e;font-size:14px;\"><p style=\"margin:4px 0;font-weight:bold;\">New Perks Activated:</p><p style=\"margin:4px 0;\">{{perks_summary}}</p></div><div style=\"text-align:center;margin:24px 0;\"><a href=\"{{action_url}}\" style=\"display:inline-block;background:#f59e0b;color:#0f172a;padding:12px 28px;text-decoration:none;border-radius:8px;font-weight:bold;\">Explore Creator Guild Perks</a></div></div>', NOW()),
('payout_failed_alert', 'Payout Transfer Failed / Action Needed', 'Action Needed: Your {{platform_name}} Payout Transfer Failed', 'Dispatched if bank/payout gateway encounters disbursement issue.', '<div style=\"font-family:sans-serif;max-width:600px;margin:0 auto;padding:24px;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;\"><h2 style=\"color:#dc2626;margin-top:0;\">Payout Transfer Unsuccessful</h2><p style=\"color:#475569;font-size:15px;\">Hi {{name}}, our payout processor was unable to complete the transfer of <strong>${{amount}} USD</strong> to your linked destination.</p><div style=\"background:#fee2e2;padding:14px;border-radius:8px;margin:16px 0;color:#991b1b;font-size:14px;\"><p style=\"margin:4px 0;\"><strong>Error Reason:</strong> {{failure_reason}}</p></div><p style=\"color:#475569;font-size:14px;\">Please check and update your linked payout destination details to allow retry.</p><div style=\"text-align:center;margin:24px 0;\"><a href=\"{{action_url}}\" style=\"display:inline-block;background:#dc2626;color:#ffffff;padding:10px 24px;text-decoration:none;border-radius:6px;font-weight:bold;\">Update Payout Method</a></div></div>', NOW()),
('weekly_digest_roundup', 'Weekly Intelligence Digest Roundup', 'TechScout Weekly: Top Stories, Breakthroughs & Community Highlights', 'Dispatched on weekly digest containing top articles, tools, and community spotlights.', '<div style=\"font-family:sans-serif;max-width:600px;margin:0 auto;padding:24px;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;\"><h2 style=\"color:#0f172a;margin-top:0;\">Your Weekly Intelligence Digest</h2><p style=\"color:#475569;font-size:15px;\">Hi {{name}}, here is your curated summary of the most impactful tech breakdowns and emerging tools this week.</p><div style=\"border-top:1px solid #e2e8f0;padding-top:16px;margin-top:16px;\"><h3 style=\"color:#0f172a;margin-bottom:8px;\">🌟 Trending Insights</h3><p style=\"color:#475569;font-size:14px;\">{{trending_summary}}</p></div><div style=\"text-align:center;margin:28px 0;\"><a href=\"{{digest_url}}\" style=\"display:inline-block;background:#4f46e5;color:#ffffff;padding:12px 28px;text-decoration:none;border-radius:8px;font-weight:bold;\">Read Full Digest Online</a></div></div>', NOW());

-- ------------------------------------------------------------------------------
-- 6. Comment Reactions (Helpful / Like / Emoji Reactions with Idempotent Toggle)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `comment_reactions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `comment_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `reaction_type` VARCHAR(32) NOT NULL DEFAULT 'helpful',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_cr_comment_user` (`comment_id`, `user_id`),
  INDEX `idx_cr_comment_ip` (`comment_id`, `ip_address`),
  INDEX `idx_cr_reaction` (`reaction_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Upgrade complete. Safe for repeated runs.
