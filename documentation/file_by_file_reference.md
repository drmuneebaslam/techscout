# TechScout Complete 201-File Technical Reference

This master reference catalog indexes **every single one of the 201 files** in the TechScout repository, detailing file paths, file sizes, primary purposes, exported classes/symbols, and dependencies.

---

## 1. Root & Configuration Files (8 Files)

| File Path | Size | Primary Purpose | Key Symbols / Exported Classes |
| :--- | :--- | :--- | :--- |
| [`index.php`](file:///c:/xampp/htdocs/techscout.n/index.php) | 47,122 B | Single Entry Point & Router Dispatcher | Front Controller, Dynamic Base URL Auto-Healing, Route Registry |
| [`app/autoload.php`](file:///c:/xampp/htdocs/techscout.n/app/autoload.php) | 430 B | Class Autoloader & Helper Bootstrap | `spl_autoload_register()`, `App\` namespace autoloader |
| [`.htaccess`](file:///c:/xampp/htdocs/techscout.n/.htaccess) | 2,039 B | Apache Web Server Configuration | Security headers, URL rewriting, Directory protection, Gzip |
| [`.gitignore`](file:///c:/xampp/htdocs/techscout.n/.gitignore) | 35 B | Git Exclusion List | Excludes temporary logs, session caches, and raw uploads |
| [`credentials.txt`](file:///c:/xampp/htdocs/techscout.n/credentials.txt) | 1,433 B | User Account Registry | System access accounts listing |
| [`CHANGELOG.md`](file:///c:/xampp/htdocs/techscout.n/CHANGELOG.md) | 16,608 B | Platform Release Notes | Comprehensive history of platform upgrades and bug fixes |
| [`TECHSCOUT_MAIL_AND_PROXY_COMPLETE_GUIDE.md`](file:///c:/xampp/htdocs/techscout.n/TECHSCOUT_MAIL_AND_PROXY_COMPLETE_GUIDE.md) | 23,944 B | Email & SMTP Proxy Documentation | SMTP & Python proxy deployment guide |
| [`favicon.ico`](file:///c:/xampp/htdocs/techscout.n/favicon.ico) | 1,150 B | Site Favicon Icon | ICO Favicon asset |

---

## 2. Core Framework Engine (`app/Core/`) (25 Files)

| File Path | Size | Primary Purpose | Key Symbols / Exported Classes |
| :--- | :--- | :--- | :--- |
| [`app/Core/Auth.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Auth.php) | 14,025 B | Authentication & Access Control Guard | `App\Core\Auth` (`check()`, `user()`, `role()`, `requireAdmin()`) |
| [`app/Core/BannerHelper.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/BannerHelper.php) | 1,213 B | Announcement & Promo Banner Renderer | `App\Core\BannerHelper` (`renderActiveBanner()`) |
| [`app/Core/Config.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Config.php) | 1,892 B | Configuration Store | `App\Core\Config` (`get()`, `set()`) |
| [`app/Core/ContentFilter.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/ContentFilter.php) | 1,289 B | Profanity & Spam Filter | `App\Core\ContentFilter` (`containsProfanity()`, `maskProfanity()`) |
| [`app/Core/CredentialsManager.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/CredentialsManager.php) | 3,395 B | Secret Keys Loader | `App\Core\CredentialsManager` (`get()`) |
| [`app/Core/Database.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Database.php) | 110,950 B | PDO Database Abstraction Layer | `App\Core\Database` (`query()`, `fetch()`, `fetchAll()`, `execute()`) |
| [`app/Core/DateHelper.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/DateHelper.php) | 2,861 B | PST Time & Relative Date Utilities | `App\Core\DateHelper` (`timeAgo()`, `formatDate()`) |
| [`app/Core/DocxParser.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/DocxParser.php) | 18,228 B | Word (.docx) Document Parser | `App\Core\DocxParser` (`parseToHtml()`) |
| [`app/Core/ErrorLogger.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/ErrorLogger.php) | 24,125 B | Global Exception & Crash Handler | `App\Core\ErrorLogger` (`register()`, `logException()`) |
| [`app/Core/FeatureFlag.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/FeatureFlag.php) | 2,705 B | Dynamic Feature Toggle Engine | `App\Core\FeatureFlag` (`isEnabled()`) |
| [`app/Core/Firewall.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Firewall.php) | 2,735 B | IP Rate Limiter & Threat Defense | `App\Core\Firewall` (`checkRateLimit()`, `isBannedIp()`) |
| [`app/Core/Mailer.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Mailer.php) | 116,394 B | Multi-Transport Email Engine | `App\Core\Mailer` (`send()`) |
| [`app/Core/NotificationHelper.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/NotificationHelper.php) | 16,569 B | In-App Notification Manager | `App\Core\NotificationHelper` (`send()`, `getUnreadCount()`) |
| [`app/Core/Request.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Request.php) | 3,726 B | HTTP Request Input Handler | `App\Core\Request` (`getMethod()`, `get()`, `post()`, `json()`) |
| [`app/Core/Response.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Response.php) | 1,789 B | HTTP Response & JSON Generator | `App\Core\Response` (`json()`, `redirect()`) |
| [`app/Core/Router.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Router.php) | 4,361 B | Dynamic URL Router | `App\Core\Router` (`get()`, `post()`, `dispatch()`) |
| [`app/Core/Sanitizer.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Sanitizer.php) | 1,428 B | XSS Sanitization & Slug Generator | `App\Core\Sanitizer` (`clean()`, `cleanHtml()`, `slug()`) |
| [`app/Core/SeoEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/SeoEngine.php) | 3,906 B | Meta Tags & JSON-LD Generator | `App\Core\SeoEngine` (`generateTags()`, `generateJsonLd()`) |
| [`app/Core/Session.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Session.php) | 2,020 B | Secure Session Wrapper | `App\Core\Session` (`start()`, `get()`, `set()`, `flash()`, `csrfToken()`) |
| [`app/Core/SmtpClient.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/SmtpClient.php) | 12,726 B | Raw Socket SMTP Client | `App\Core\SmtpClient` (`sendMail()`) |
| [`app/Core/SocialHelper.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/SocialHelper.php) | 9,786 B | Social Sharing Link Generator | `App\Core\SocialHelper` (`getShareLinks()`) |
| [`app/Core/SvgHelper.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/SvgHelper.php) | 7,209 B | Inline SVG Icon Catalog Renderer | `App\Core\SvgHelper` (`render()`) |
| [`app/Core/TagHelper.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/TagHelper.php) | 1,814 B | Tag Parser & Cloud Generator | `App\Core\TagHelper` (`parseTags()`, `renderCloud()`) |
| [`app/Core/WebhookEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/WebhookEngine.php) | 5,976 B | Outbound Webhook Dispatcher | `App\Core\WebhookEngine` (`trigger()`) |
| [`app/Core/helpers.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/helpers.php) | 3,614 B | Global Convenience Functions | `e()`, `url()`, `asset()`, `auth()`, `session()`, `old()` |

---

## 3. Application Controllers (`app/Controllers/`) (14 Files)

| File Path | Size | Primary Purpose | Key Symbols / Exported Classes |
| :--- | :--- | :--- | :--- |
| [`app/Controllers/AdminController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/AdminController.php) | 366,220 B | Enterprise Administration Hub | `App\Controllers\AdminController` (Dashboard, Users, Moderation, Mail, Financials) |
| [`app/Controllers/ApiController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/ApiController.php) | 50,774 B | RESTful API Endpoint Handlers | `App\Controllers\ApiController` (Content, Comments, Upvotes, Search APIs) |
| [`app/Controllers/AuthController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/AuthController.php) | 31,417 B | Authentication & User Lifecycle | `App\Controllers\AuthController` (Login, Register, Logout, Reset Password, 2FA) |
| [`app/Controllers/AuthorApplicationController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/AuthorApplicationController.php) | 6,176 B | Author Application Submission | `App\Controllers\AuthorApplicationController` (`showForm()`, `submit()`) |
| [`app/Controllers/AuthorProfileController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/AuthorProfileController.php) | 7,663 B | Public Author Showcase | `App\Controllers\AuthorProfileController` (`show()`, `followers()`) |
| [`app/Controllers/AuthorStudioController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/AuthorStudioController.php) | 54,857 B | Author Content Creation Studio | `App\Controllers\AuthorStudioController` (Editor, Analytics, Earnings) |
| [`app/Controllers/BlogController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/BlogController.php) | 7,129 B | Public Blog & Article Reader | `App\Controllers\BlogController` (`index()`, `show()`, `category()`, `tag()`) |
| [`app/Controllers/DirectoryController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/DirectoryController.php) | 24,691 B | AI Directories & Affiliate Links | `App\Controllers\DirectoryController` (Tools, Prompts, GPTs, Campaigns, Jobs, `/go/{slug}`) |
| [`app/Controllers/HomeController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/HomeController.php) | 11,615 B | Home Portal & Contact Form | `App\Controllers\HomeController` (`index()`, `contact()`, `submitContact()`) |
| [`app/Controllers/InboxController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/InboxController.php) | 3,794 B | Direct Peer Messaging | `App\Controllers\InboxController` (`index()`, `thread()`) |
| [`app/Controllers/PageController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/PageController.php) | 5,772 B | Static CMS Pages Delivery | `App\Controllers\PageController` (`show()`) |
| [`app/Controllers/SearchController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/SearchController.php) | 648 B | Global Search Query Engine | `App\Controllers\SearchController` (`search()`) |
| [`app/Controllers/SupportController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/SupportController.php) | 19,284 B | Customer Support Desk | `App\Controllers\SupportController` (Tickets, Messaging) |
| [`app/Controllers/TopicController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/TopicController.php) | 4,525 B | Topic Explorer Hub | `App\Controllers\TopicController` (`index()`, `show()`) |

---

## 4. Data Models & Business Engines (`app/Models/`) (18 Files)

| File Path | Size | Primary Purpose | Key Symbols / Exported Classes |
| :--- | :--- | :--- | :--- |
| [`app/Models/Analytics.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/Analytics.php) | 13,861 B | Traffic Tracking & Page Analytics | `App\Models\Analytics` (`recordPageView()`, `getAuthorDashboardStats()`) |
| [`app/Models/AuditLog.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/AuditLog.php) | 6,248 B | Security & Compliance Audit Log | `App\Models\AuditLog` (`log()`, `getClientIp()`) |
| [`app/Models/Author.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/Author.php) | 6,162 B | Author Creator Profiles | `App\Models\Author` (`getByUserId()`, `getBySlug()`, `addReputation()`) |
| [`app/Models/AuthorTierEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/AuthorTierEngine.php) | 12,725 B | 20-Sublevel Author Tier Progression | `App\Models\AuthorTierEngine` (`evaluateAuthorTier()`, `getTierHierarchy()`) |
| [`app/Models/CoAuthorEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/CoAuthorEngine.php) | 6,214 B | Multi-Author Collaboration | `App\Models\CoAuthorEngine` (`addCoAuthor()`, `getCoAuthors()`) |
| [`app/Models/Comment.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/Comment.php) | 2,013 B | Multi-Level Comment Threads | `App\Models\Comment` (`getForContent()`, `addComment()`) |
| [`app/Models/Content.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/Content.php) | 13,935 B | Unified Content Query Entity | `App\Models\Content` (`getLatestArticles()`, `getTrendingContent()`) |
| [`app/Models/CreatorReferral.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/CreatorReferral.php) | 6,947 B | Referral Signup & Credit Reward Engine | `App\Models\CreatorReferral` (`trackConversion()`) |
| [`app/Models/CreatorTipping.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/CreatorTipping.php) | 7,179 B | Member Author Tipping System | `App\Models\CreatorTipping` (`sendTip()`) |
| [`app/Models/DatabaseBackupManager.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/DatabaseBackupManager.php) | 7,392 B | Database Backup & Retention Manager | `App\Models\DatabaseBackupManager` (`createBackup()`, `restoreBackup()`) |
| [`app/Models/FinancialLedger.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/FinancialLedger.php) | 17,206 B | Double-Entry Credit Ledger | `App\Models\FinancialLedger` (`recordTransaction()`, `getAuthorBalance()`) |
| [`app/Models/PersonaFollowerEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/PersonaFollowerEngine.php) | 13,642 B | Author Follower Activity Feeds | `App\Models\PersonaFollowerEngine` (`follow()`, `unfollow()`, `getFollowers()`) |
| [`app/Models/PlagiarismEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/PlagiarismEngine.php) | 6,328 B | Similarity & Plagiarism Detector | `App\Models\PlagiarismEngine` (`checkSimilarity()`) |
| [`app/Models/RBAC.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/RBAC.php) | 7,867 B | Role-Based Access Matrix | `App\Models\RBAC` (`hasPermission()`, `requirePermission()`) |
| [`app/Models/SecurityAnomalyEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/SecurityAnomalyEngine.php) | 6,853 B | Anomaly & Attack Scanner | `App\Models\SecurityAnomalyEngine` (`detectAnomalies()`) |
| [`app/Models/SessionManager.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/SessionManager.php) | 7,391 B | User Session & Device Manager | `App\Models\SessionManager` (`registerSession()`, `revokeSession()`) |
| [`app/Models/SupportEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/SupportEngine.php) | 7,363 B | Support Ticket Lifecycle | `App\Models\SupportEngine` (`createTicket()`, `addMessage()`) |
| [`app/Models/User.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/User.php) | 20,014 B | User Account Entity Model | `App\Models\User` (`findByEmail()`, `create()`, `updateProfile()`, `setRole()`, `issueStrike()`) |

---

## 5. Database Layer (`database/`) (5 Files)

| File Path | Size | Primary Purpose | Key Symbols / Entity Tables |
| :--- | :--- | :--- | :--- |
| [`database/schema.sql`](file:///c:/xampp/htdocs/techscout.n/database/schema.sql) | 68,298 B | Master Database Schema Script | Defines all 76 core SQL tables, indexes, and FK constraints |
| [`database/seeds.sql`](file:///c:/xampp/htdocs/techscout.n/database/seeds.sql) | 273,327 B | Database Data Seed Script | Populates initial super-admin, sample articles, tools, and categories |
| [`database/database_upgrade_v2.sql`](file:///c:/xampp/htdocs/techscout.n/database/database_upgrade_v2.sql) | 2,703 B | Schema Migration v2 | Adds author tier sublevel columns and co-author foreign keys |
| [`database/help_legal_upgrade.sql`](file:///c:/xampp/htdocs/techscout.n/database/help_legal_upgrade.sql) | 36,834 B | Legal & Help Center Schema Migration | CMS content pages and help center category tables |
| [`database/mail_tracking_upgrade.sql`](file:///c:/xampp/htdocs/techscout.n/database/mail_tracking_upgrade.sql) | 3,480 B | Email Analytics Schema Migration | Adds open pixel tracking and link click analytics table |

---

## 6. Frontend Assets (`assets/`) (7 Files)

| File Path | Size | Primary Purpose | Key Symbols / Component Styles |
| :--- | :--- | :--- | :--- |
| [`assets/css/style.css`](file:///c:/xampp/htdocs/techscout.n/assets/css/style.css) | 65,135 B | Main Platform CSS Design System | Glassmorphism, CSS Custom Properties, Dark Mode, Typography |
| [`assets/css/admin.css`](file:///c:/xampp/htdocs/techscout.n/assets/css/admin.css) | 29,994 B | Admin Dashboard CSS Stylesheet | Dark sidebar layout, data table badges, modal animations |
| [`assets/js/main.js`](file:///c:/xampp/htdocs/techscout.n/assets/js/main.js) | 28,623 B | Primary Client JavaScript Engine | Dynamic search, theme toggler, reaction upvotes, toast alerts |
| [`assets/js/content-engine.js`](file:///c:/xampp/htdocs/techscout.n/assets/js/content-engine.js) | 6,023 B | Directory & Blog AJAX Filter | Infinite scrolling, live search filtration, category filters |
| [`assets/js/admin_table_paginator.js`](file:///c:/xampp/htdocs/techscout.n/assets/js/admin_table_paginator.js) | 11,140 B | Admin Data Table Paginator | Table pagination, search filtering, client sorting |
| [`assets/js/lucide.min.js`](file:///c:/xampp/htdocs/techscout.n/assets/js/lucide.min.js) | 632,343 B | Lucide SVG Icon Pack Library | Native SVG icon rendering engine |
| [`assets/favicon.svg`](file:///c:/xampp/htdocs/techscout.n/assets/favicon.svg) | 794 B | Vector SVG Favicon Icon | SVG branding icon |

---

## 7. Views Architecture (`views/`) (124 Files)

### Layouts & Components (8 Files)
- [`views/layouts/header.php`](file:///c:/xampp/htdocs/techscout.n/views/layouts/header.php) (45,703 B)
- [`views/layouts/footer.php`](file:///c:/xampp/htdocs/techscout.n/views/layouts/footer.php) (9,368 B)
- [`views/layouts/admin_header.php`](file:///c:/xampp/htdocs/techscout.n/views/layouts/admin_header.php) (29,976 B)
- [`views/layouts/admin_footer.php`](file:///c:/xampp/htdocs/techscout.n/views/layouts/admin_footer.php) (27,330 B)
- [`views/components/category_explorer.php`](file:///c:/xampp/htdocs/techscout.n/views/components/category_explorer.php) (4,348 B)
- [`views/components/comments_section.php`](file:///c:/xampp/htdocs/techscout.n/views/components/comments_section.php) (27,460 B)
- [`views/components/report_modal.php`](file:///c:/xampp/htdocs/techscout.n/views/components/report_modal.php) (6,867 B)
- [`views/components/social_share.php`](file:///c:/xampp/htdocs/techscout.n/views/components/social_share.php) (9,451 B)

### Account Views (3 Files)
- [`views/account/bookmarks.php`](file:///c:/xampp/htdocs/techscout.n/views/account/bookmarks.php) (8,013 B)
- [`views/account/history.php`](file:///c:/xampp/htdocs/techscout.n/views/account/history.php) (10,840 B)
- [`views/account/settings.php`](file:///c:/xampp/htdocs/techscout.n/views/account/settings.php) (42,354 B)

### Admin Panel Views (49 Files)
- [`views/admin/advertisements.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/advertisements.php) (9,490 B)
- [`views/admin/analytics.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/analytics.php) (34,575 B)
- [`views/admin/announcements.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/announcements.php) (13,483 B)
- [`views/admin/api_keys.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/api_keys.php) (7,963 B)
- [`views/admin/audit_logs.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/audit_logs.php) (36,275 B)
- [`views/admin/author_applications.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/author_applications.php) (24,328 B)
- [`views/admin/contact_messages.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/contact_messages.php) (7,703 B)
- [`views/admin/content_analytics.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/content_analytics.php) (32,271 B)
- [`views/admin/content_filters.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/content_filters.php) (6,550 B)
- [`views/admin/content_queue.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/content_queue.php) (53,559 B)
- [`views/admin/credentials.txt`](file:///c:/xampp/htdocs/techscout.n/views/admin/credentials.txt) (1,433 B)
- [`views/admin/dashboard.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/dashboard.php) (29,072 B)
- [`views/admin/db_manager.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/db_manager.php) (25,732 B)
- [`views/admin/engagement_studio.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/engagement_studio.php) (51,261 B)
- [`views/admin/feature_flags.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/feature_flags.php) (14,877 B)
- [`views/admin/firewall.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/firewall.php) (28,514 B)
- [`views/admin/help_article_editor.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/help_article_editor.php) (21,523 B)
- [`views/admin/help_articles.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/help_articles.php) (9,142 B)
- [`views/admin/homepage.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/homepage.php) (11,022 B)
- [`views/admin/job_applications.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/job_applications.php) (9,529 B)
- [`views/admin/legal_page_editor.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/legal_page_editor.php) (8,051 B)
- [`views/admin/legal_pages.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/legal_pages.php) (4,062 B)
- [`views/admin/mail.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/mail.php) (159,196 B)
- [`views/admin/mail_template_editor.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/mail_template_editor.php) (17,067 B)
- [`views/admin/media.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/media.php) (21,795 B)
- [`views/admin/moderation.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/moderation.php) (22,372 B)
- [`views/admin/navigation.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/navigation.php) (14,810 B)
- [`views/admin/newsletters.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/newsletters.php) (93,690 B)
- [`views/admin/rbac_matrix.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/rbac_matrix.php) (12,151 B)
- [`views/admin/revenue.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/revenue.php) (145,681 B)
- [`views/admin/seo.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/seo.php) (24,204 B)
- [`views/admin/seo_studio.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/seo_studio.php) (4,082 B)
- [`views/admin/settings.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/settings.php) (61,421 B)
- [`views/admin/subscribers.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/subscribers.php) (23,506 B)
- [`views/admin/support_categories.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/support_categories.php) (29,219 B)
- [`views/admin/system.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/system.php) (18,867 B)
- [`views/admin/system_logs.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/system_logs.php) (40,154 B)
- [`views/admin/tickets.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/tickets.php) (10,755 B)
- [`views/admin/users.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/users.php) (105,646 B)
- [`views/admin/webhooks.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/webhooks.php) (20,161 B)
- [`views/admin/components/editorial_badge_modal.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/components/editorial_badge_modal.php) (11,443 B)
- [`views/admin/crud/articles.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/crud/articles.php) (33,529 B)
- [`views/admin/crud/campaigns.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/crud/campaigns.php) (31,039 B)
- [`views/admin/crud/categories.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/crud/categories.php) (14,653 B)
- [`views/admin/crud/gpts.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/crud/gpts.php) (9,784 B)
- [`views/admin/crud/jobs.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/crud/jobs.php) (11,148 B)
- [`views/admin/crud/prompts.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/crud/prompts.php) (9,891 B)
- [`views/admin/crud/tools.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/crud/tools.php) (38,771 B)
- [`views/admin/crud/topics.php`](file:///c:/xampp/htdocs/techscout.n/views/admin/crud/topics.php) (10,883 B)

### Auth & Onboarding Views (7 Files)
- [`views/auth/forgot_password.php`](file:///c:/xampp/htdocs/techscout.n/views/auth/forgot_password.php) (3,197 B)
- [`views/auth/login.php`](file:///c:/xampp/htdocs/techscout.n/views/auth/login.php) (2,459 B)
- [`views/auth/onboarding.php`](file:///c:/xampp/htdocs/techscout.n/views/auth/onboarding.php) (13,257 B)
- [`views/auth/register.php`](file:///c:/xampp/htdocs/techscout.n/views/auth/register.php) (14,399 B)
- [`views/auth/reset_password.php`](file:///c:/xampp/htdocs/techscout.n/views/auth/reset_password.php) (2,890 B)
- [`views/auth/two_factor.php`](file:///c:/xampp/htdocs/techscout.n/views/auth/two_factor.php) (4,247 B)
- [`views/auth/verify_email.php`](file:///c:/xampp/htdocs/techscout.n/views/auth/verify_email.php) (7,385 B)

### Author Studio Views (5 Files)
- [`views/author/analytics.php`](file:///c:/xampp/htdocs/techscout.n/views/author/analytics.php) (17,173 B)
- [`views/author/content_analytics.php`](file:///c:/xampp/htdocs/techscout.n/views/author/content_analytics.php) (28,805 B)
- [`views/author/dashboard.php`](file:///c:/xampp/htdocs/techscout.n/views/author/dashboard.php) (154,547 B)
- [`views/author/editor.php`](file:///c:/xampp/htdocs/techscout.n/views/author/editor.php) (75,595 B)
- [`views/author/rewards.php`](file:///c:/xampp/htdocs/techscout.n/views/author/rewards.php) (4,829 B)

### Blog Views (2 Files)
- [`views/blog/article.php`](file:///c:/xampp/htdocs/techscout.n/views/blog/article.php) (25,198 B)
- [`views/blog/index.php`](file:///c:/xampp/htdocs/techscout.n/views/blog/index.php) (11,395 B)

### Directory Views (6 Files)
- [`views/directory/gpt_single.php`](file:///c:/xampp/htdocs/techscout.n/views/directory/gpt_single.php) (11,551 B)
- [`views/directory/gpts.php`](file:///c:/xampp/htdocs/techscout.n/views/directory/gpts.php) (10,838 B)
- [`views/directory/prompt_single.php`](file:///c:/xampp/htdocs/techscout.n/views/directory/prompt_single.php) (6,444 B)
- [`views/directory/prompts.php`](file:///c:/xampp/htdocs/techscout.n/views/directory/prompts.php) (9,658 B)
- [`views/directory/tool_single.php`](file:///c:/xampp/htdocs/techscout.n/views/directory/tool_single.php) (30,535 B)
- [`views/directory/tools.php`](file:///c:/xampp/htdocs/techscout.n/views/directory/tools.php) (10,308 B)

### Error Views (9 Files)
- [`views/errors/400.php`](file:///c:/xampp/htdocs/techscout.n/views/errors/400.php) (2,334 B)
- [`views/errors/401.php`](file:///c:/xampp/htdocs/techscout.n/views/errors/401.php) (2,463 B)
- [`views/errors/403.php`](file:///c:/xampp/htdocs/techscout.n/views/errors/403.php) (2,502 B)
- [`views/errors/404.php`](file:///c:/xampp/htdocs/techscout.n/views/errors/404.php) (2,739 B)
- [`views/errors/419.php`](file:///c:/xampp/htdocs/techscout.n/views/errors/419.php) (2,492 B)
- [`views/errors/429.php`](file:///c:/xampp/htdocs/techscout.n/views/errors/429.php) (2,435 B)
- [`views/errors/500.php`](file:///c:/xampp/htdocs/techscout.n/views/errors/500.php) (2,400 B)
- [`views/errors/502.php`](file:///c:/xampp/htdocs/techscout.n/views/errors/502.php) (2,441 B)
- [`views/errors/503.php`](file:///c:/xampp/htdocs/techscout.n/views/errors/503.php) (7,777 B)

### Messaging & Notification Views (2 Files)
- [`views/inbox/index.php`](file:///c:/xampp/htdocs/techscout.n/views/inbox/index.php) (16,526 B)
- [`views/notifications/index.php`](file:///c:/xampp/htdocs/techscout.n/views/notifications/index.php) (39,303 B)

### Pages Views (15 Files)
- [`views/pages/about.php`](file:///c:/xampp/htdocs/techscout.n/views/pages/about.php) (7,140 B)
- [`views/pages/acceptable_use.php`](file:///c:/xampp/htdocs/techscout.n/views/pages/acceptable_use.php) (2,411 B)
- [`views/pages/accessibility.php`](file:///c:/xampp/htdocs/techscout.n/views/pages/accessibility.php) (2,935 B)
- [`views/pages/author_guidelines.php`](file:///c:/xampp/htdocs/techscout.n/views/pages/author_guidelines.php) (5,018 B)
- [`views/pages/community_guidelines.php`](file:///c:/xampp/htdocs/techscout.n/views/pages/community_guidelines.php) (2,986 B)
- [`views/pages/cookies.php`](file:///c:/xampp/htdocs/techscout.n/views/pages/cookies.php) (2,745 B)
- [`views/pages/copyright.php`](file:///c:/xampp/htdocs/techscout.n/views/pages/copyright.php) (2,897 B)
- [`views/pages/disclosure.php`](file:///c:/xampp/htdocs/techscout.n/views/pages/disclosure.php) (2,471 B)
- [`views/pages/editorial.php`](file:///c:/xampp/htdocs/techscout.n/views/pages/editorial.php) (4,089 B)
- [`views/pages/help.php`](file:///c:/xampp/htdocs/techscout.n/views/pages/help.php) (53,983 B)
- [`views/pages/legal.php`](file:///c:/xampp/htdocs/techscout.n/views/pages/legal.php) (13,518 B)
- [`views/pages/legal_page.php`](file:///c:/xampp/htdocs/techscout.n/views/pages/legal_page.php) (3,019 B)
- [`views/pages/privacy.php`](file:///c:/xampp/htdocs/techscout.n/views/pages/privacy.php) (4,284 B)
- [`views/pages/security_policy.php`](file:///c:/xampp/htdocs/techscout.n/views/pages/security_policy.php) (2,639 B)
- [`views/pages/terms.php`](file:///c:/xampp/htdocs/techscout.n/views/pages/terms.php) (3,137 B)

### Public Frontend Views (15 Files)
- [`views/public/about.php`](file:///c:/xampp/htdocs/techscout.n/views/public/about.php) (1,486 B)
- [`views/public/author_apply.php`](file:///c:/xampp/htdocs/techscout.n/views/public/author_apply.php) (25,007 B)
- [`views/public/author_profile.php`](file:///c:/xampp/htdocs/techscout.n/views/public/author_profile.php) (25,517 B)
- [`views/public/campaign_single.php`](file:///c:/xampp/htdocs/techscout.n/views/public/campaign_single.php) (38,975 B)
- [`views/public/campaigns.php`](file:///c:/xampp/htdocs/techscout.n/views/public/campaigns.php) (10,109 B)
- [`views/public/contact.php`](file:///c:/xampp/htdocs/techscout.n/views/public/contact.php) (3,122 B)
- [`views/public/home.php`](file:///c:/xampp/htdocs/techscout.n/views/public/home.php) (37,509 B)
- [`views/public/job_single.php`](file:///c:/xampp/htdocs/techscout.n/views/public/job_single.php) (9,035 B)
- [`views/public/jobs.php`](file:///c:/xampp/htdocs/techscout.n/views/public/jobs.php) (4,234 B)
- [`views/public/member_profile.php`](file:///c:/xampp/htdocs/techscout.n/views/public/member_profile.php) (7,093 B)
- [`views/public/privacy.php`](file:///c:/xampp/htdocs/techscout.n/views/public/privacy.php) (1,220 B)
- [`views/public/search.php`](file:///c:/xampp/htdocs/techscout.n/views/public/search.php) (2,902 B)
- [`views/public/terms.php`](file:///c:/xampp/htdocs/techscout.n/views/public/terms.php) (1,091 B)
- [`views/public/topic_single.php`](file:///c:/xampp/htdocs/techscout.n/views/public/topic_single.php) (8,075 B)
- [`views/public/topics.php`](file:///c:/xampp/htdocs/techscout.n/views/public/topics.php) (10,660 B)

### Support Views (2 Files)
- [`views/support/index.php`](file:///c:/xampp/htdocs/techscout.n/views/support/index.php) (17,077 B)
- [`views/support/ticket.php`](file:///c:/xampp/htdocs/techscout.n/views/support/ticket.php) (18,378 B)
