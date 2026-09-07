# TechScout Database Schemas & SQL Architecture

This document details the database architecture, 76 SQL tables, foreign key relationships, indexing strategies, seed scripts, and migration files located in `database/`.

---

## 1. Schema Overview

- **Database Name**: `techscout_db`
- **Default Character Set**: `utf8mb4`
- **Default Collation**: `utf8mb4_unicode_ci`
- **Storage Engine**: `InnoDB` (ACID compliant with foreign key constraints)
- **Primary Schema File**: [`database/schema.sql`](file:///c:/xampp/htdocs/techscout.n/database/schema.sql)
- **Total Tables**: 76

---

## 2. Table Categorization & Entity Relationships

```
                            +-------------------+
                            |       users       |
                            +---------+---------+
                                      |
             +------------------------+------------------------+
             |                        |                        |
             v                        v                        v
    +-----------------+      +-----------------+      +-----------------+
    |     authors     |      |  user_sessions  |      |   audit_logs    |
    +--------+--------+      +-----------------+      +-----------------+
             |
             v
    +-----------------+
    |    contents     |  <-- Polymorphic Base Content Table
    +--------+--------+
             |
      +------+------+------------------------+------------------------+
      |             |                        |                        |
      v             v                        v                        v
+-----------+ +-----------+            +-----------+            +-----------+
| articles  | | ai_tools  |            |   gpts    |            |  prompts  |
+-----------+ +-----------+            +-----------+            +-----------+
```

---

## 3. Core Database Tables Summary

### User & Authentication Domain
- **`users`**: Core user entity storing `email`, `username`, `password_hash`, `role` (`super_admin`, `admin`, `editor`, `author`, `member`), `status` (`active`, `suspended`, `banned`, `unverified`), and 2FA secrets.
- **`user_sessions`**: Session token registry storing `session_token_hash`, `ip_address`, `user_agent`, and `last_active_at`.
- **`audit_logs`**: System audit trail capturing `user_id`, `action`, `entity_type`, `entity_id`, `ip_address`, `old_values`, `new_values`.
- **`roles`**, **`permissions`**, **`role_permissions`**, **`admin_roles`**, **`admin_role_permissions`**: Granular RBAC tables mapping roles to specific privilege strings.

### Author & Monetization Domain
- **`authors`**: Creator profile storing `display_name`, `slug`, `headline`, `reputation_score`, `reputation_level`, `current_tier_code`, `tier_multiplier`, `promo_credits`, and badge attributes.
- **`author_tier_history`**: Audit table logging every tier advancement event, previous/new tier code, and CPM multiplier.
- **`financial_ledger`**: Double-entry financial transaction ledger recording payouts, promotional credit grants, and tips.
- **`author_payouts`**: Creator payout requests (`pending`, `approved`, `processed`, `rejected`) storing tax forms and payment details.
- **`creator_tips`**: Direct member-to-author tipping log.
- **`creator_referrals`**: Conversion tracking for author and member referral signups.
- **`article_coauthors`**: Maps secondary co-authors to published articles with custom revenue/credit splits.

### Content & Directories Domain
- **`contents`**: Central polymorphic base table for all site content items storing `title`, `slug`, `content_type` (`article`, `tool`, `gpt`, `prompt`, `campaign`, `job`), `author_id`, `status` (`published`, `draft`, `under_review`, `archived`), `view_count`, `upvote_count`.
- **`articles`**: Article-specific metadata (`body_markdown`, `body_html`, `reading_time_minutes`, `featured_image`, `docx_imported`).
- **`ai_tools`**: AI Tool directory details (`website_url`, `pricing_model`, `affiliate_url`, `pros_list`, `cons_list`, `rating_avg`).
- **`gpts`**: Custom GPT details (`gpt_url`, `author_name`, `instructions_preview`, `capabilities_list`).
- **`prompts`**: Copyable prompt templates (`prompt_text`, `variables_json`, `ai_model_compatibility`).
- **`campaigns`**: Hackathon and promo campaign specifications (`prize_pool_usd`, `start_date`, `end_date`, `rules_html`).
- **`campaign_submissions`**: Member project submissions for active campaigns with voting tallies.
- **`jobs`**: Tech job board listings (`company_name`, `location`, `job_type`, `salary_range`, `apply_url`).

### Email & Communication Domain
- **`email_logs`**: Outbound email log tracking subject, recipient, status (`queued`, `sent`, `failed`), and SMTP response.
- **`mail_tracking`**: Open pixel tracking (`opened_at`, `open_count`) and link click tracking (`clicked_at`, `target_url`).
- **`email_templates`**: HTML email templates with dynamic placeholder variables (`{{user_name}}`, `{{reset_link}}`).
- **`notifications`**: In-app notifications storing `user_id`, `type`, `title`, `message`, `action_url`, `is_read`.
- **`support_tickets`**, **`support_messages`**: Support desk ticketing threads.
- **`conversations`**, **`messages`**: Direct peer-to-peer user messaging system.

### System, Security & Telemetry Domain
- **`banned_ips`**: IP address blacklist table.
- **`security_anomalies`**: Log of flagged security events (credential stuffing, SQL injection attempts, rate limit breaches).
- **`error_logs`**: Central database store for uncaught PHP exceptions and fatal crashes.
- **`site_settings`**: Global configuration key-value storage (`domain_app_url`, `site_name`, `smtp_host`, `smtp_port`, `maintenance_mode`).

---

## 4. SQL Migration & Upgrade Scripts

- **`database/schema.sql`**: Full baseline table schema creation script.
- **`database/seeds.sql`**: Data seed script populating default admin user, default categories, default topics, initial AI tools, sample blog posts, and site settings.
- **`database/database_upgrade_v2.sql`**: Migration script adding 20-sublevel author tier columns and co-author foreign keys.
- **`database/help_legal_upgrade.sql`**: Adds legal page CMS content, Help Center categories, and FAQ structures.
- **`database/mail_tracking_upgrade.sql`**: Adds `mail_tracking` table for email opens and link click analytics.
