# 46. Master Database Schema (`schema.sql`)

## File Location
[`database/schema.sql`](file:///c:/xampp/htdocs/techscout.n/database/schema.sql)

## Database Architecture
Contains DDL statements creating 76 InnoDB tables with `utf8mb4_unicode_ci` encoding:
- `users`: Core user accounts, pass hashes, status, 2FA.
- `authors`: Creator profile metadata, tier sublevels, reputation scores.
- `contents`: Articles, AI tools, prompts, GPTs, campaigns, jobs.
- `comments`: Threaded hierarchical comment replies.
- `site_settings`: Global settings, base URL, banner announcements.
- `financial_ledger`: Double-entry financial accounting records.
- `author_strikes`: Disciplinary strike history.
- `audit_logs`: Administrative system events.
- `support_tickets` & `support_replies`: Customer support tickets.
- `mail_templates` & `mail_logs`: Email system configuration.
