# QA Test Suite 05: Admin Panel, CMS Settings, Newsletters & Navigation

## 1. Test Suite Metadata
* **Module:** Admin Command Center, 14-Module CMS Analytics, 19-Category System Settings, Newsletters Hub, Navigation Studio, Cyber Firewall
* **Target Routes:** `/admin/analytics`, `/admin/settings`, `/admin/newsletters`, `/admin/navigation`, `/admin/security`, `/admin/webhooks`, `/admin/api-keys`

---

## 2. Detailed Test Cases

### TC-ADMIN-01: CMS Telemetry Analytics HUD (14 Modules)
* **Preconditions:** Admin visiting `/admin/analytics`.
* **Test Steps:**
  1. Load `/admin/analytics` and verify absence of any SQL exceptions (specifically checking `view_count` queries).
  2. Switch between telemetry tabs: Traffic, Users, Members, Authors, Content, Views, Engagement, Search, Revenue, Subscriptions, Tools, AI Tools, Conversion, Retention.
  3. Click "Export Analytics CSV".
* **Expected Result:**
  - Zero SQL errors; live KPI cards populate with formatted data; CSV file downloads immediately.

### TC-ADMIN-02: CMS Unified System Settings (19 Categories)
* **Preconditions:** Admin visiting `/admin/settings`.
* **Test Steps:**
  1. Navigate through all 19 tabs (General, Branding, Domain, Email, Authentication, Payments, Storage, CDN, Search, SEO, Analytics, Notifications, Moderation, Security, API, Integrations, Feature Flags, Localization, System Logs).
  2. Modify a setting (e.g. `site_name` or `social_twitter`) and click "Save Configuration".
* **Expected Result:**
  - Settings are saved to `site_settings` table in MySQL and reflected across the frontend.

### TC-ADMIN-03: Newsletter Broadcast Composer & Test Email Dispatch
* **Preconditions:** Admin visiting `/admin/newsletters`.
* **Test Steps:**
  1. Click "Compose Issue ⚡".
  2. Enter issue title, subject line, and preheader text.
  3. Use the "Quick Inserter" to insert a published article block into the newsletter body.
  4. Enter a test recipient email and click "⚡ Send Test Email".
  5. Save the issue.
* **Expected Result:**
  - Test email dispatch executes via Mailer class; issue is saved with status `draft` or `scheduled` in `newsletter_issues` table.

### TC-ADMIN-04: Multi-Tier Navigation Studio (7 Modules)
* **Preconditions:** Admin visiting `/admin/navigation`.
* **Test Steps:**
  1. Switch between tabs: Main, Secondary, Footer, Mobile, Dropdowns, Mega Menus, External.
  2. Click "+ Add Navigation Link", enter label "Community Bounties", URL "/campaigns", icon "flag", badge "Prizes", color "amber".
  3. Save link and verify it appears in the navigation table.
  4. Edit and delete an item.
* **Expected Result:**
  - Full CRUD matrix operates seamlessly; sort orders and badge glow colors persist in `navigation_menus` table.
