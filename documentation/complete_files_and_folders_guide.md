# TechScout Comprehensive Directory, File, Feature & Function Guide

This master guide provides a full, detailed breakdown of **every single directory, folder, and file** across the TechScout repository, outlining their specific architecture, capabilities, features, and exported functions.

---

## Table of Contents
1. [Root Directory & Configuration Files](#1-root-directory--configuration-files)
2. [`app/Core/` Core Framework Engine](#2-appcore-core-framework-engine)
3. [`app/Controllers/` HTTP Controllers](#3-appcontrollers-http-controllers)
4. [`app/Models/` Data Models & Business Engines](#4-appmodels-data-models--business-engines)
5. [`database/` Database Schemas & Migration Scripts](#5-database-database-schemas--migration-scripts)
6. [`assets/` CSS Stylesheets, Client Scripts & Icons](#6-assets-css-stylesheets-client-scripts--icons)
7. [`views/` View Templates & Component Architecture](#7-views-view-templates--component-architecture)

---

## 1. Root Directory & Configuration Files

- **`/` (Root Folder)**: Main repository directory containing the Front Controller, global web server configuration, autoloader, release documentation, and system secrets registry.

### Root Files
- **[`index.php`](file:///c:/xampp/htdocs/techscout.n/index.php)**
  - **Purpose**: Single Front Controller entry point for all HTTP traffic.
  - **Features**: Error logger initialization, timezone enforcement (`Asia/Karachi`), session startup, QA visual auth helper (`/index.php?qa_login=1`), dynamic base URL auto-healing, route registration, and dispatch execution via `App\Core\Router`.
  - **Functions**: Registers routes for all platform sections and handles front controller dispatching.

- **[`app/autoload.php`](file:///c:/xampp/htdocs/techscout.n/app/autoload.php)**
  - **Purpose**: Class autoloader complying with PSR-4 standards.
  - **Features**: Automatically maps `App\` namespace calls to physical PHP file paths inside `app/` without manual `require` or `include` statements.
  - **Functions**: Registers `spl_autoload_register()` closure.

- **[`.htaccess`](file:///c:/xampp/htdocs/techscout.n/.htaccess)**
  - **Purpose**: Apache web server runtime configuration.
  - **Features**: Mod_rewrite rules redirecting non-file/non-directory requests to `index.php`, directory browsing disabling (`Options -Indexes`), Gzip MIME type compression, and security headers (`X-Frame-Options`, `X-XSS-Protection`, `X-Content-Type-Options`).

- **[`.gitignore`](file:///c:/xampp/htdocs/techscout.n/.gitignore)**
  - **Purpose**: Excludes transient local development cache and logs from Git tracking.

- **[`credentials.txt`](file:///c:/xampp/htdocs/techscout.n/credentials.txt)**
  - **Purpose**: System access registry storing administrative credentials, super admin access accounts, and initial seed user information.

- **[`CHANGELOG.md`](file:///c:/xampp/htdocs/techscout.n/CHANGELOG.md)**
  - **Purpose**: Version release history tracking platform milestones, security patches, database upgrades, and feature releases.

- **[`TECHSCOUT_MAIL_AND_PROXY_COMPLETE_GUIDE.md`](file:///c:/xampp/htdocs/techscout.n/TECHSCOUT_MAIL_AND_PROXY_COMPLETE_GUIDE.md)**
  - **Purpose**: Operations deployment manual for setting up local socket SMTP mail and configuring fallback Python proxy mailers.

- **[`favicon.ico`](file:///c:/xampp/htdocs/techscout.n/favicon.ico)**
  - **Purpose**: Binary ICO browser tab icon.

---

## 2. `app/Core/` Core Framework Engine

- **[`app/Core/`](file:///c:/xampp/htdocs/techscout.n/app/Core)**: Contains the custom framework kernel components including database access, routing, session security, mail transport, sanitization, and helper classes.

### Core Files
- **[`Auth.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Auth.php)**
  - **Features**: Multi-role RBAC authorization (`super_admin`, `admin`, `editor`, `author`, `member`), session token validation, auto-creation of author records for staff, admin user impersonation.
  - **Methods**: `check()`, `id()`, `user()`, `role()`, `authorApplicationStatus()`, `hasRole()`, `isSuperAdmin()`, `isAdmin()`, `isEditor()`, `isAuthor()`, `requireAuth()`, `requireAdmin()`, `requireAuthor()`, `loginUser()`, `logout()`, `impersonate()`, `stopImpersonating()`, `isImpersonating()`.

- **[`BannerHelper.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/BannerHelper.php)**
  - **Features**: Evaluates active announcements in `site_settings` and outputs dismissal-aware HTML alert banners.
  - **Methods**: `renderActiveBanner()`.

- **[`Config.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Config.php)**
  - **Features**: Key-value store for application settings, dynamic base URL resolver, asset versioning.
  - **Methods**: `get()`, `set()`, `url()`, `asset()`.

- **[`ContentFilter.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/ContentFilter.php)**
  - **Features**: Profanity screening, automated spam pattern detection, comment/submission validation.
  - **Methods**: `containsProfanity()`, `maskProfanity()`, `validate()`.

- **[`CredentialsManager.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/CredentialsManager.php)**
  - **Features**: Secret key parser reading credentials from environment variables or local secret files.
  - **Methods**: `get()`, `parseFile()`.

- **[`Database.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Database.php)**
  - **Features**: Singleton PDO abstraction layer, auto-creation of missing databases, auto-execution of `schema.sql` and `seeds.sql`, live incremental schema migrations, timezone forcing (`+05:00`), prepared statement execution.
  - **Methods**: `getConnection()`, `query()`, `fetch()`, `fetchAll()`, `execute()`, `lastInsertId()`, `beginTransaction()`, `commit()`, `rollBack()`, `tableExists()`.

- **[`DateHelper.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/DateHelper.php)**
  - **Features**: PST time conversion, human-readable relative timestamps (`"3 hours ago"`), formatted date output.
  - **Methods**: `timeAgo()`, `formatDate()`, `formatDateTime()`.

- **[`DocxParser.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/DocxParser.php)**
  - **Features**: Binary ZIP archive parser extracting XML text and converting Microsoft Word `.docx` documents into structured HTML for the article editor.
  - **Methods**: `parseToHtml()`, `extractText()`.

- **[`ErrorLogger.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/ErrorLogger.php)**
  - **Features**: Global error, exception, and fatal shutdown handler, formatting stack traces into log files and rendering clean error pages.
  - **Methods**: `register()`, `logException()`, `logError()`.

- **[`FeatureFlag.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/FeatureFlag.php)**
  - **Features**: Dynamic runtime feature toggling, percentage rollouts, user-specific feature gating.
  - **Methods**: `isEnabled()`, `getAll()`.

- **[`Firewall.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Firewall.php)**
  - **Features**: IP rate-limiting slider using decay windows, threat detection, IP ban enforcement.
  - **Methods**: `checkRateLimit()`, `isBannedIp()`, `banIp()`.

- **[`Mailer.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Mailer.php)**
  - **Features**: Multi-transport transactional mail engine, template engine supporting 20+ notification types, open pixel tracking, link click analytics, SMTP socket delivery, Python proxy fallback.
  - **Methods**: `send()`, `getSettings()`, `getTemplates()`, `compileTemplate()`.

- **[`NotificationHelper.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/NotificationHelper.php)**
  - **Features**: Dispatches real-time in-app user notifications, unread badge counters, mark-as-read state updates.
  - **Methods**: `send()`, `getUnreadCount()`, `getNotifications()`, `markAsRead()`, `markAllAsRead()`.

- **[`Request.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Request.php)**
  - **Features**: Encapsulates `$_GET`, `$_POST`, `$_SERVER`, and raw JSON input bodies into a clean, sanitized HTTP request object.
  - **Methods**: `getMethod()`, `getPath()`, `get()`, `post()`, `input()`, `json()`, `isAjax()`.

- **[`Response.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Response.php)**
  - **Features**: Generates JSON API responses, status header setters, HTTP redirection helpers.
  - **Methods**: `json()`, `redirect()`, `setStatusCode()`.

- **[`Router.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Router.php)**
  - **Features**: Compiles parametric routes into regular expressions, dispatches matching requests to target controller classes and actions.
  - **Methods**: `get()`, `post()`, `addRoute()`, `dispatch()`.

- **[`Sanitizer.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Sanitizer.php)**
  - **Features**: XSS HTML entity escaping, safe HTML filtering for rich text, slug generation.
  - **Methods**: `clean()`, `cleanHtml()`, `slug()`.

- **[`SeoEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/SeoEngine.php)**
  - **Features**: Generates dynamic HTML meta tags, OpenGraph preview cards, Twitter cards, and Schema.org JSON-LD structured data.
  - **Methods**: `generateTags()`, `generateJsonLd()`.

- **[`Session.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Session.php)**
  - **Features**: Secure cookie flags (`HttpOnly`, `SameSite=Lax`), session lifecycle management, flash memory notifications, CSRF token generation and validation.
  - **Methods**: `start()`, `set()`, `get()`, `has()`, `remove()`, `flash()`, `getFlash()`, `csrfToken()`, `verifyCsrfToken()`.

- **[`SmtpClient.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/SmtpClient.php)**
  - **Features**: Low-level socket connection to SMTP servers (`fsockopen`), handling EHLO, STARTTLS, AUTH LOGIN, and DATA streams directly.
  - **Methods**: `sendMail()`.

- **[`SocialHelper.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/SocialHelper.php)**
  - **Features**: Builds share links for Twitter/X, LinkedIn, Facebook, Reddit, WhatsApp, and Telegram.
  - **Methods**: `getShareLinks()`.

- **[`SvgHelper.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/SvgHelper.php)**
  - **Features**: Renders inline SVG code for Lucide platform icons.
  - **Methods**: `render()`.

- **[`TagHelper.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/TagHelper.php)**
  - **Features**: Parses comma-separated tag lists and renders pill-style HTML tag clouds.
  - **Methods**: `parseTags()`, `renderCloud()`.

- **[`WebhookEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/WebhookEngine.php)**
  - **Features**: Asynchronously dispatches outbound HTTP POST payloads with HMAC SHA-256 signatures to registered partner webhooks.
  - **Methods**: `trigger()`.

- **[`helpers.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/helpers.php)**
  - **Features**: Provides global convenience functions (`e()`, `url()`, `asset()`, `auth()`, `session()`, `old()`).
  - **Functions**: `e()`, `url()`, `asset()`, `auth()`, `session()`, `old()`.

---

## 3. `app/Controllers/` HTTP Controllers

- **[`app/Controllers/`](file:///c:/xampp/htdocs/techscout.n/app/Controllers)**: Contains all HTTP Request Controller classes handling application routes, processing input, querying models, and rendering view templates.

### Controller Files
- **[`AdminController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/AdminController.php)**
  - **Features**: Comprehensive administration dashboard, editorial content review queue, CRUD for tools, prompts, GPTs, campaigns, jobs, categories, and topics, user moderation, strike issuing, account bans, user merging, impersonation, broadcast messaging, mail settings & templates, double-entry financial ledgering, payout processing, security anomaly auditing, and database backup management.

- **[`ApiController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/ApiController.php)**
  - **Features**: RESTful JSON endpoints supplying data for AJAX frontend components, content listings, single content inspection, nested comment posting, reaction upvoting, global search autocomplete, author following, and notifications.

- **[`AuthController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/AuthController.php)**
  - **Features**: Authentication flow handling login, account registration, password reset requests, email verification OTP code processing, 2FA code verification, and user onboarding steps.

- **[`AuthorApplicationController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/AuthorApplicationController.php)**
  - **Features**: Handles public member applications to join the verified creator guild, storing portfolio links and track records for editorial review.

- **[`AuthorProfileController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/AuthorProfileController.php)**
  - **Features**: Displays public creator profiles, showcase portfolios, author badges, reputation scores, and follower lists.

- **[`AuthorStudioController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/AuthorStudioController.php)**
  - **Features**: Author Creator Studio dashboard, WYSIWYG article editor, `.docx` import, multi-author co-authorship invitations, submission tracking, real-time pageview analytics, and financial earnings withdrawal claims.

- **[`BlogController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/BlogController.php)**
  - **Features**: Public tech blog feed, article reader view, category & tag filtering, and trending content rankings.

- **[`DirectoryController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/DirectoryController.php)**
  - **Features**: AI Directories showcasing AI Tools, Prompts, GPTs, Hackathon Campaigns, and Tech Jobs, handling project submissions, voting, and `/go/{slug}` affiliate click tracking.

- **[`HomeController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/HomeController.php)**
  - **Features**: Landing page displaying hero banners, featured topics, trending tools, latest articles, community highlights, and processing contact messages.

- **[`InboxController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/InboxController.php)**
  - **Features**: Peer-to-peer direct messaging center enabling member conversations and support inquiries.

- **[`PageController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/PageController.php)**
  - **Features**: Dynamic CMS controller delivering static legal and informational pages (Terms, Privacy, About, Editorial Policy).

- **[`SearchController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/SearchController.php)**
  - **Features**: Handles global search queries across articles, tools, prompts, GPTs, and author profiles.

- **[`SupportController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/SupportController.php)**
  - **Features**: Customer support ticketing portal allowing users to create tickets, view staff replies, and submit follow-up responses.

- **[`TopicController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/TopicController.php)**
  - **Features**: Topic hub displaying technology topics (AI, Cloud, Security, Web Engineering) and their associated content feeds.

---

## 4. `app/Models/` Data Models & Business Engines

- **[`app/Models/`](file:///c:/xampp/htdocs/techscout.n/app/Models)**: Database entity models and core business logic engines encapsulating data queries, user state, financial accounting, tier progression, and security scanning.

### Model Files
- **[`Analytics.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/Analytics.php)**: Pageview tracking, IP logging, referrer analytics, and creator dashboard metrics calculations.
- **[`AuditLog.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/AuditLog.php)**: Structured system event logging for administrative actions, security changes, and user status updates.
- **[`Author.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/Author.php)**: Creator profile data management, reputation points calculation, custom editorial badge assignments, and promo credit balances.
- **[`AuthorTierEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/AuthorTierEngine.php)**: Implements the 20-sublevel author progression hierarchy across 5 Leagues (Bronze, Silver, Gold, Diamond, Partner Elite) with dynamic CPM multipliers (1.00x to 2.50x).
- **[`CoAuthorEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/CoAuthorEngine.php)**: Manages multi-author collaboration, co-authorship invitations, and revenue percentage splits.
- **[`Comment.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/Comment.php)**: Hierarchical threaded comment storage, moderation state management, and user comment fetching.
- **[`Content.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/Content.php)**: Unified data access layer querying articles, tools, prompts, GPTs, campaigns, and jobs.
- **[`CreatorReferral.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/CreatorReferral.php)**: Tracks referral signup links and credits bonus rewards to referring creators.
- **[`CreatorTipping.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/CreatorTipping.php)**: Direct member patronage tipping engine transferring credits from readers to authors.
- **[`DatabaseBackupManager.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/DatabaseBackupManager.php)**: Automated SQL database export generator, backup file retention, and database restoration engine.
- **[`FinancialLedger.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/FinancialLedger.php)**: Double-entry credit ledger recording author earnings, platform fees, tips, promo credit redemptions, and payout withdrawal claims.
- **[`PersonaFollowerEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/PersonaFollowerEngine.php)**: Social follower relationship graph enabling members to follow authors and receive activity notifications.
- **[`PlagiarismEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/PlagiarismEngine.php)**: N-gram text hashing and similarity scoring algorithm detecting duplicate submissions.
- **[`RBAC.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/RBAC.php)**: Role-Based Access Control matrix defining fine-grained permissions for platform administrative modules.
- **[`SecurityAnomalyEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/SecurityAnomalyEngine.php)**: Real-time threat detection scanner analyzing request rates, brute-force login attempts, and suspicious IP behaviors.
- **[`SessionManager.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/SessionManager.php)**: Active user session registry, device tracking, and emergency remote session revocation.
- **[`SupportEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/SupportEngine.php)**: Support ticket thread lifecycle, status transitions (Open, Pending, Resolved), and staff assignment.
- **[`User.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/User.php)**: Core user account model handling user creation, authentication credentials, profile updates, status changes, and 3-strike disciplinary enforcement (Warning -> 14-Day Suspension -> Permanent Ban).

---

## 5. `database/` Database Schemas & Migration Scripts

- **[`database/`](file:///c:/xampp/htdocs/techscout.n/database)**: Stores database architecture definitions, relational schemas, seed data scripts, and incremental version upgrade scripts.

### Database Files
- **[`schema.sql`](file:///c:/xampp/htdocs/techscout.n/database/schema.sql)**: Master SQL DDL script creating all 76 core InnoDB tables with indexes, unique constraints, and foreign keys (`users`, `authors`, `contents`, `comments`, `site_settings`, `financial_ledger`, `author_strikes`, etc.).
- **[`seeds.sql`](file:///c:/xampp/htdocs/techscout.n/database/seeds.sql)**: Initial data population script inserting default super-admin accounts, sample categories, topics, AI tools, prompts, GPTs, and platform configuration options.
- **[`database_upgrade_v2.sql`](file:///c:/xampp/htdocs/techscout.n/database/database_upgrade_v2.sql)**: Version 2 schema update script adding author tier sublevel columns, co-author relationship tables, and revenue sharing fields.
- **[`help_legal_upgrade.sql`](file:///c:/xampp/htdocs/techscout.n/database/help_legal_upgrade.sql)**: CMS upgrade script adding help center categories, dynamic legal page tables, and editorial guideline content.
- **[`mail_tracking_upgrade.sql`](file:///c:/xampp/htdocs/techscout.n/database/mail_tracking_upgrade.sql)**: Email analytics migration script adding email open pixel tracking tables and link click logs.

---

## 6. `assets/` CSS Stylesheets, Client Scripts & Icons

- **[`assets/`](file:///c:/xampp/htdocs/techscout.n/assets)**: Public frontend static assets directory containing CSS design tokens, client JavaScript engines, and vector branding icons.

### Assets Subdirectories & Files
- **[`assets/css/`](file:///c:/xampp/htdocs/techscout.n/assets/css)**
  - **[`style.css`](file:///c:/xampp/htdocs/techscout.n/assets/css/style.css)**: Main public design system featuring CSS Custom Properties (variables), dark mode tokens, glassmorphism card styling, flexbox/grid layouts, responsive breakpoints, and typography.
  - **[`admin.css`](file:///c:/xampp/htdocs/techscout.n/assets/css/admin.css)**: Administration interface stylesheet supplying sidebar layouts, data table badge indicators, form controls, and modal animations.

- **[`assets/js/`](file:///c:/xampp/htdocs/techscout.n/assets/js)**
  - **[`main.js`](file:///c:/xampp/htdocs/techscout.n/assets/js/main.js)**: Core client script handling search autocomplete, dark/light theme switching, reaction upvoting, bookmarking, clipboard copying, and toast notifications.
  - **[`content-engine.js`](file:///c:/xampp/htdocs/techscout.n/assets/js/content-engine.js)**: AJAX directory script driving live search filtering, category selection, and dynamic card rendering without full page reloads.
  - **[`admin_table_paginator.js`](file:///c:/xampp/htdocs/techscout.n/assets/js/admin_table_paginator.js)**: Administrative client table utility providing instant text searching, column sorting, and pagination across large data tables.
  - **[`lucide.min.js`](file:///c:/xampp/htdocs/techscout.n/assets/js/lucide.min.js)**: Embedded Lucide SVG icon rendering library.

- **[`assets/favicon.svg`](file:///c:/xampp/htdocs/techscout.n/assets/favicon.svg)**: Vector SVG favicon branding graphic.

---

## 7. `views/` View Templates & Component Architecture

- **[`views/`](file:///c:/xampp/htdocs/techscout.n/views)**: Contains 124 HTML/PHP view template files organized across 14 specialized subdirectories.

### Views Subdirectories & Files

#### 1. `views/layouts/` (Master Page Wrappers)
- **[`header.php`](file:///c:/xampp/htdocs/techscout.n/views/layouts/header.php)**: Top navigation bar, site search, theme toggler, user dropdown, unread notification badge, active promo banner, and HTML `<head>` tags.
- **[`footer.php`](file:///c:/xampp/htdocs/techscout.n/views/layouts/footer.php)**: Public site footer containing topic links, legal page links, copyright notice, and script includes.
- **[`admin_header.php`](file:///c:/xampp/htdocs/techscout.n/views/layouts/admin_header.php)**: Administrative dashboard top bar, sidebar navigation links, and administrative breadcrumbs.
- **[`admin_footer.php`](file:///c:/xampp/htdocs/techscout.n/views/layouts/admin_footer.php)**: Admin footer container and administrative JS includes.

#### 2. `views/components/` (Reusable UI Partials)
- **[`category_explorer.php`](file:///c:/xampp/htdocs/techscout.n/views/components/category_explorer.php)**: Category selection pills bar.
- **[`comments_section.php`](file:///c:/xampp/htdocs/techscout.n/views/components/comments_section.php)**: Nested hierarchical comment thread view with submission forms and upvote buttons.
- **[`report_modal.php`](file:///c:/xampp/htdocs/techscout.n/views/components/report_modal.php)**: User modal interface for reporting content policy violations.
- **[`social_share.php`](file:///c:/xampp/htdocs/techscout.n/views/components/social_share.php)**: Article social media share button row.

#### 3. `views/account/` (Member Account Management)
- **[`bookmarks.php`](file:///c:/xampp/htdocs/techscout.n/views/account/bookmarks.php)**: User saved bookmarks library.
- **[`history.php`](file:///c:/xampp/htdocs/techscout.n/views/account/history.php)**: Reading history feed.
- **[`settings.php`](file:///c:/xampp/htdocs/techscout.n/views/account/settings.php)**: Account profile editing, avatar upload, password changes, and 2FA configuration.

#### 4. `views/admin/` (Enterprise Admin Control Panel - 49 Files)
- **[`dashboard.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/dashboard.php)**: Admin analytics summary, system status cards, quick action panels.
- **[`users.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/users.php)**: User management table with search, filtering, role assignment, strike issuing, and status controls.
- **[`content_queue.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/content_queue.php)**: Editorial queue for reviewing submitted articles, tools, and prompts.
- **[`mail.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/mail.php)**: Central mail center managing SMTP settings, email logs, test email delivery, and template assignment.
- **[`revenue.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/revenue.php)**: Financial ledger dashboard, payout withdrawal claim processing, credit adjustments.
- **[`firewall.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/firewall.php)**: Security firewall control panel, rate limiting settings, IP blacklist management.
- **[`audit_logs.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/audit_logs.php)**: System event audit trail log.
- **[`db_manager.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/db_manager.php)**: Database backup generation, list, download, and restore interface.
- **[`engagement_studio.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/engagement_studio.php)**: Platform engagement metrics and user retention graphs.
- **[`rbac_matrix.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/rbac_matrix.php)**: Role-Based Access Control permission assignment matrix.
- **[`newsletters.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/newsletters.php)**: Subscriber broadcast newsletter engine.
- **[`crud/articles.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/crud/articles.php)**, **[`tools.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/crud/tools.php)**, **[`prompts.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/crud/prompts.php)**, **[`gpts.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/crud/gpts.php)**, **[`campaigns.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/crud/campaigns.php)**, **[`jobs.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/crud/jobs.php)**, **[`categories.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/crud/categories.php)**, **[`topics.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/crud/topics.php)**: Admin CRUD management forms and data tables.

#### 5. `views/auth/` (Authentication & Security)
- **[`login.php`](file:///c:/xampp/htdocs/techscout.n/views/auth/login.php)**: Login form interface.
- **[`register.php`](file:///c:/xampp/htdocs/techscout.n/views/auth/register.php)**: Account registration form.
- **[`forgot_password.php`](file:///c:/xampp/htdocs/techscout.n/views/auth/forgot_password.php)**: Password reset request form.
- **[`reset_password.php`](file:///c:/xampp/htdocs/techscout.n/views/auth/reset_password.php)**: Password reset code input form.
- **[`verify_email.php`](file:///c:/xampp/htdocs/techscout.n/views/auth/verify_email.php)**: Email verification OTP PIN form.
- **[`two_factor.php`](file:///c:/xampp/htdocs/techscout.n/views/auth/two_factor.php)**: Two-Factor Authentication OTP form.
- **[`onboarding.php`](file:///c:/xampp/htdocs/techscout.n/views/auth/onboarding.php)**: New user profile setup & topic selection wizard.

#### 6. `views/author/` (Creator Studio Views)
- **[`dashboard.php`](file:///c:/xampp/htdocs/techscout.n/views/author/dashboard.php)**: Author Creator Studio main dashboard, tier progression gauge, and quick metrics.
- **[`editor.php`](file:///c:/xampp/htdocs/techscout.n/views/author/editor.php)**: WYSIWYG article composition editor with `.docx` document upload.
- **[`analytics.php`](file:///c:/xampp/htdocs/techscout.n/views/author/analytics.php)**: Creator pageview traffic charts and reader engagement analytics.
- **[`content_analytics.php`](file:///c:/xampp/htdocs/techscout.n/views/author/content_analytics.php)**: Single article analytics breakdown.
- **[`rewards.php`](file:///c:/xampp/htdocs/techscout.n/views/author/rewards.php)**: Earnings dashboard, balance breakdown, and payout withdrawal claim form.

#### 7. `views/blog/` (Public Articles & Feed)
- **[`index.php`](file:///c:/xampp/htdocs/techscout.n/views/blog/index.php)**: Main blog feed with category and tag filters.
- **[`article.php`](file:///c:/xampp/htdocs/techscout.n/views/blog/article.php)**: Single article reader layout with table of contents, author badge, co-authors, reader tips, and comments.

#### 8. `views/directory/` (AI Directories)
- **[`tools.php`](file:///c:/xampp/htdocs/techscout.n/views/directory/tools.php)** / **[`tool_single.php`](file:///c:/xampp/htdocs/techscout.n/views/directory/tool_single.php)**: AI Tools directory list and single tool details page.
- **[`prompts.php`](file:///c:/xampp/htdocs/techscout.n/views/directory/prompts.php)** / **[`prompt_single.php`](file:///c:/xampp/htdocs/techscout.n/views/directory/prompt_single.php)**: AI Prompts directory list and single prompt view with copy-to-clipboard button.
- **[`gpts.php`](file:///c:/xampp/htdocs/techscout.n/views/directory/gpts.php)** / **[`gpt_single.php`](file:///c:/xampp/htdocs/techscout.n/views/directory/gpt_single.php)**: Custom GPTs directory list and details view.

#### 9. `views/errors/` (HTTP Error Pages)
- **[`400.php`](file:///c:/xampp/htdocs/techscout.n/views/errors/400.php)** (Bad Request), **[`401.php`](file:///c:/xampp/htdocs/techscout.n/views/errors/401.php)** (Unauthorized), **[`403.php`](file:///c:/xampp/htdocs/techscout.n/views/errors/403.php)** (Forbidden), **[`404.php`](file:///c:/xampp/htdocs/techscout.n/views/errors/404.php)** (Not Found), **[`419.php`](file:///c:/xampp/htdocs/techscout.n/views/errors/419.php)** (CSRF Expired), **[`429.php`](file:///c:/xampp/htdocs/techscout.n/views/errors/429.php)** (Rate Limit Exceeded), **[`500.php`](file:///c:/xampp/htdocs/techscout.n/views/errors/500.php)** (Internal Server Error), **[`502.php`](file:///c:/xampp/htdocs/techscout.n/views/errors/502.php)** (Bad Gateway), **[`503.php`](file:///c:/xampp/htdocs/techscout.n/views/errors/503.php)** (Service Unavailable).

#### 10. `views/inbox/` & `views/notifications/`
- **[`inbox/index.php`](file:///c:/xampp/htdocs/techscout.n/views/inbox/index.php)**: Member direct messaging interface.
- **[`notifications/index.php`](file:///c:/xampp/htdocs/techscout.n/views/notifications/index.php)**: In-app user notifications history center.

#### 11. `views/pages/` (CMS Informational Pages)
- **[`about.php`](file:///c:/xampp/htdocs/techscout.n/views/pages/about.php)**, **[`terms.php`](file:///c:/xampp/htdocs/techscout.n/views/pages/terms.php)**, **[`privacy.php`](file:///c:/xampp/htdocs/techscout.n/views/pages/privacy.php)**, **[`help.php`](file:///c:/xampp/htdocs/techscout.n/views/pages/help.php)**, **[`editorial.php`](file:///c:/xampp/htdocs/techscout.n/views/pages/editorial.php)**, **[`community_guidelines.php`](file:///c:/xampp/htdocs/techscout.n/views/pages/community_guidelines.php)**, **[`security_policy.php`](file:///c:/xampp/htdocs/techscout.n/views/pages/security_policy.php)**: CMS rendered pages.

#### 12. `views/public/` (Public Portal Views)
- **[`home.php`](file:///c:/xampp/htdocs/techscout.n/views/public/home.php)**: Main homepage view.
- **[`author_profile.php`](file:///c:/xampp/htdocs/techscout.n/views/public/author_profile.php)**: Public author profile layout.
- **[`author_apply.php`](file:///c:/xampp/htdocs/techscout.n/views/public/author_apply.php)**: Public author verification application form.
- **[`campaigns.php`](file:///c:/xampp/htdocs/techscout.n/views/public/campaigns.php)** / **[`campaign_single.php`](file:///c:/xampp/htdocs/techscout.n/views/public/campaign_single.php)**: Hackathon campaigns and submission voting.
- **[`jobs.php`](file:///c:/xampp/htdocs/techscout.n/views/public/jobs.php)** / **[`job_single.php`](file:///c:/xampp/htdocs/techscout.n/views/public/job_single.php)**: Tech job board and application form.
- **[`search.php`](file:///c:/xampp/htdocs/techscout.n/views/public/search.php)**: Global platform search result view.

#### 13. `views/support/` (Support Desk)
- **[`index.php`](file:///c:/xampp/htdocs/techscout.n/views/support/index.php)**: Member support tickets list and creation form.
- **[`ticket.php`](file:///c:/xampp/htdocs/techscout.n/views/support/ticket.php)**: Ticket conversation thread with staff reply box.
