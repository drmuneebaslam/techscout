# TechScout Platform — Master Architecture Index

> **Platform Version**: 1.0.0-PROD  
> **Target Domain**: `techscout.online` (Unified Single-Domain Architecture)  
> **Development Environment**: Local XAMPP (`http://localhost/techscout.n`)  
> **Core Tech Stack**: HTML5, CSS3 (Design Tokens + Glassmorphism), Vanilla ES6+ JavaScript, CDN Libraries, PHP 8.2+, MySQL 8.0+

---

## 1. Executive Summary & Vision

**TechScout** is a next-generation technology publication, AI discovery platform, and creator ecosystem. It bridges in-depth technical editorial content with an interactive directory of AI tools, custom GPTs, engineering prompt libraries, software benchmarks, and marketing campaigns.

### Core Architectural Principles
1. **Single-Domain Unified Routing**: Zero subdomains. All discovery, publishing, member, author, admin, and API experiences operate under a unified domain pathing model.
2. **Polymorphic Content Engine**: A singular foundation powers articles, tools, GPTs, prompts, collections, and campaigns with unified tags, SEO, comments, reactions, bookmarks, revisions, and analytics.
3. **High-Performance CDN & Native Web Stack**: Built with semantic HTML5, pure CSS custom property design systems, Vanilla JS micro-components, and CDN-accelerated libraries for instant load times (<1s FCP).
4. **Creator & Monetization Flywheel**: Sustainable author growth powered by non-cash gamified rewards (editorial spotlights, profile verification, promo credits) alongside direct affiliate, display, and sponsored monetization.

---

## 2. Master Documentation Directory

The entire 40-Phase, 960-Task specification is organized across specialized engineering modules:

| Document | Scope & Key Phases | Primary Tasks Covered |
|---|---|---|
| [01_BRAND_AND_DESIGN_SYSTEM.md](file:///c:/xampp/htdocs/techscout.n/docs/01_BRAND_AND_DESIGN_SYSTEM.md) | Phase 1: Brand strategy, colors, typography, tokens, CDN assets | Tasks 1–30 |
| [02_SYSTEM_ARCHITECTURE.md](file:///c:/xampp/htdocs/techscout.n/docs/02_SYSTEM_ARCHITECTURE.md) | Phase 1 & 2: Single-domain URL routing, Unified Content Engine, MVC | Tasks 31–60 |
| [03_DATABASE_SCHEMA_DDL.md](file:///c:/xampp/htdocs/techscout.n/docs/03_DATABASE_SCHEMA_DDL.md) | Phase 23: Complete 35+ table MySQL schema, foreign keys, indexes | Tasks 610–640 |
| [04_ROLES_PERMISSIONS_AUTH.md](file:///c:/xampp/htdocs/techscout.n/docs/04_ROLES_PERMISSIONS_AUTH.md) | Phase 8, 11 & 24: RBAC matrix, 2FA, session security, CSRF/XSS | Tasks 261–290, 331–360, 641–670 |
| [05_CONTENT_ENGINE_AND_LIFECYCLE.md](file:///c:/xampp/htdocs/techscout.n/docs/05_CONTENT_ENGINE_AND_LIFECYCLE.md) | Phase 4 & 12: Content state machine, slugging, revisions, quality score | Tasks 115–160, 361–380 |
| [06_PUBLIC_PAGES_AND_UI_SPECS.md](file:///c:/xampp/htdocs/techscout.n/docs/06_PUBLIC_PAGES_AND_UI_SPECS.md) | Phase 3, 31 & 32: Wireframes, component templates, responsive PWA | Tasks 61–114, 781–803 |
| [07_AI_TOOLS_GPTS_PROMPTS.md](file:///c:/xampp/htdocs/techscout.n/docs/07_AI_TOOLS_GPTS_PROMPTS.md) | Phase 5 & 16: Tool directories, prompt variables, GPT submission | Tasks 161–190, 486–500 |
| [08_AUTHOR_PLATFORM_AND_WORKFLOWS.md](file:///c:/xampp/htdocs/techscout.n/docs/08_AUTHOR_PLATFORM_AND_WORKFLOWS.md) | Phase 6, 7 & 39: Creator onboarding, WYSIWYG editor, author analytics | Tasks 191–260, 905–920 |
| [09_COMMUNITY_COMMENTS_REACTIONS.md](file:///c:/xampp/htdocs/techscout.n/docs/09_COMMUNITY_COMMENTS_REACTIONS.md) | Phase 9 & 10: Nested comments, 6-reaction engine, anti-spam filters | Tasks 291–330 |
| [10_MONETIZATION_AND_NON_CASH_REWARDS.md](file:///c:/xampp/htdocs/techscout.n/docs/10_MONETIZATION_AND_NON_CASH_REWARDS.md) | Phase 13 & 14: Ad slots, sponsorships, zero-cash-burn reward engine | Tasks 381–435 |
| [11_SEO_DISCOVERY_AND_INTERNAL_LINKING.md](file:///c:/xampp/htdocs/techscout.n/docs/11_SEO_DISCOVERY_AND_INTERNAL_LINKING.md) | Phase 15 & 30: JSON-LD schemas, XML sitemaps, internal graph engine | Tasks 436–485, 767–780 |
| [12_MARKETING_CAMPAIGNS_EMAIL_GROWTH.md](file:///c:/xampp/htdocs/techscout.n/docs/12_MARKETING_CAMPAIGNS_EMAIL_GROWTH.md) | Phase 17, 18, 19 & 37: Campaign builder, UTM tracking, email digest | Tasks 501–554, 871–890 |
| [13_NOTIFICATIONS_AND_MEDIA_PIPELINE.md](file:///c:/xampp/htdocs/techscout.n/docs/13_NOTIFICATIONS_AND_MEDIA_PIPELINE.md) | Phase 20 & 21: Realtime alert center, image compression, CDN storage | Tasks 555–585 |
| [14_REST_API_SPECIFICATION.md](file:///c:/xampp/htdocs/techscout.n/docs/14_REST_API_SPECIFICATION.md) | Phase 22: RESTful endpoints, auth headers, JSON schemas, rate limits | Tasks 586–609 |
| [15_ADMIN_OPERATIONS_AND_MODERATION.md](file:///c:/xampp/htdocs/techscout.n/docs/15_ADMIN_OPERATIONS_AND_MODERATION.md) | Phase 27, 28, 34, 35 & 36: Moderation queues, backups, SLA monitors | Tasks 716–766, 823–870 |
| [16_PERFORMANCE_SECURITY_LAUNCH_CHECKLIST.md](file:///c:/xampp/htdocs/techscout.n/docs/16_PERFORMANCE_SECURITY_LAUNCH_CHECKLIST.md) | Phase 25, 26, 33 & 40: Caching, CSP headers, audit & launch checklist | Tasks 671–715, 804–822, 921–960 |
| [17_ALL_960_TASKS_DETAILED_BREAKDOWN.md](file:///c:/xampp/htdocs/techscout.n/docs/17_ALL_960_TASKS_DETAILED_BREAKDOWN.md) | Master 960-Item Task Registry with verification criteria | All Tasks 1–960 |
| [features/00_MASTER_FEATURE_CATALOG.md](file:///c:/xampp/htdocs/techscout.n/docs/features/00_MASTER_FEATURE_CATALOG.md) | **200+ Master Non-AI Utility, Admin & Author Feature Catalog** | Dedicated Platform Feature Suite |
| [CUSTOM_EMAIL_API_PYTHONANYWHERE.md](file:///c:/xampp/htdocs/techscout.n/docs/CUSTOM_EMAIL_API_PYTHONANYWHERE.md) | Microservice: PythonAnywhere Free Tier Email Dispatcher API | Extension Specs & Deployment Guide |
| [qa/00_QA_MASTER_TEST_PLAN.md](file:///c:/xampp/htdocs/techscout.n/docs/qa/00_QA_MASTER_TEST_PLAN.md) | Comprehensive QA Test Suites & Verification Playbooks (00–06) | Full-Platform QA Testing Suite |

---

## 3. Technology Stack & CDN Strategy

```
+------------------------------------------------------------------------------------+
|                                    CLIENT TIER                                     |
|  Semantic HTML5  |  CSS3 Design Tokens + Glassmorphism  |  Vanilla ES6+ JavaScript  |
|                                                                                    |
|  [CDN Integrations]:                                                              |
|  - Lucide Icons (v0.344.0)               - Chart.js (v4.4.1)                       |
|  - EasyMDE / Quill (v2.0.0)              - Prism.js (v1.29.0 Syntax Highlighting)  |
|  - SortableJS (v1.15.2)                  - Flatpickr (v4.6.13 DateTime Picker)     |
|  - SweetAlert2 (v11.10.0)                - Toastify-JS (v1.12.0)                   |
|  - DOMPurify (v3.0.8 XSS Sanitizer)      - Confetti-JS (v0.0.18 Gamification)      |
+-----------------------------------------+------------------------------------------+
                                          |
                                    HTTP / REST API
                                          |
+-----------------------------------------v------------------------------------------+
|                                APPLICATION TIER (PHP 8.2+)                         |
|  - Single Domain Router (Front Controller: index.php)                              |
|  - PSR-4 Autoloading & Service Container                                           |
|  - Security Middleware (CSRF, XSS, RateLimiter, RBAC, SessionGuard)                |
|  - Content Engine Core (Polymorphic Model, State Machine, Versioning)              |
|  - Media Processing Service (GD / Imagick WebP Compression, Thumbnails)            |
|  - Notification & Mailer Service (PHPMailer / SMTP / Webhook dispatcher)           |
+-----------------------------------------+------------------------------------------+
                                          |
                                    PDO / SQL Wire
                                          |
+-----------------------------------------v------------------------------------------+
|                                  DATA TIER (MySQL 8.0+)                            |
|  - Strict InnoDB Engine with UTF8mb4 encoding                                      |
|  - 35+ Relational Tables with Normalized Foreign Keys & Cascading Rules             |
|  - Composite & FullText Search Indexes (ngram parser)                              |
|  - Automated Audit Logging, Event Streaming, and Daily Hot/Cold Partitioning       |
+------------------------------------------------------------------------------------+
```

---

## 4. Single-Domain URL Routing Hierarchy

```
techscout.online (or localhost/techscout.n)
│
├── /                                   -> Platform Homepage (Featured, AI Directory, Prompts)
├── /about                              -> About TechScout, Mission & Team
├── /contact                            -> Contact & Feedback Form
├── /careers                            -> Open Editorial & Engineering Roles
├── /privacy, /terms, /cookies          -> Legal & Compliance Policies
├── /sitemap.xml                        -> Dynamic XML Sitemap Index
│
├── /blog                               -> Blog Hub / Editorial Stream
│   ├── /latest                         -> Chronological Latest Content
│   ├── /trending                       -> Trending Algorithm Feed (High velocity)
│   ├── /popular                        -> All-time & Monthly Top Reads
│   ├── /featured                       -> Editor's Choice & Premium Highlights
│   ├── /category/{category_slug}       -> Category Hub (AI, Engineering, DevOps, etc.)
│   ├── /tag/{tag_slug}                 -> Tag Filtered Stream
│   └── /article/{article_slug}         -> Full Interactive Article (TOC, Comments, Reactions)
│
├── /tools                              -> AI Tools & Software Directory
│   ├── /category/{cat_slug}            -> Filtered Tool Listings
│   ├── /{tool_slug}                    -> Tool Specs, Benchmark, Alternatives, Reviews
│   └── /submit                         -> Tool Submission Form (Authors/Vendors)
│
├── /gpts                               -> Custom GPT & Agent Hub
│   ├── /{gpt_slug}                     -> GPT Capabilities, Starters, Direct Launch Link
│   └── /submit                         -> GPT Community Submission
│
├── /prompts                            -> Engineering & Creative Prompt Database
│   ├── /collection/{col_slug}          -> Curated Prompt Bundles
│   ├── /{prompt_slug}                  -> Copy-to-Clipboard, Variable Injector, Model Guide
│   └── /submit                         -> Community Prompt Submission
│
├── /campaigns                          -> Active Tech Challenges, Contests & Sponsoring
│   └── /{campaign_slug}                -> Campaign Landing, Rules, Leaderboard, Submissions
│
├── /search                             -> Global Instant Search (Articles, Tools, Prompts, Authors)
├── /author/{username}                  -> Public Author Portfolio, Stats, Badges, Articles
│
├── /auth                               -> Authentication Portal
│   ├── /login                          -> User / Author / Admin Login
│   ├── /register                       -> New Account Registration
│   ├── /verify-email                   -> Email Activation Confirmation
│   ├── /forgot-password                -> Password Recovery Request
│   ├── /reset-password                 -> Secure Token Password Reset
│   └── /logout                         -> Session Invalidation
│
├── /member                             -> Registered Reader Dashboard
│   ├── /profile                        -> User Profile & Reading Preferences
│   ├── /bookmarks                      -> Saved Articles, Tools, and Prompts
│   ├── /history                        -> Reading History & Progress
│   ├── /following                      -> Followed Authors & Notifications
│   └── /settings                       -> Account Security & 2FA Setup
│
├── /author                             -> Author & Creator Studio
│   ├── /dashboard                      -> Overview Stats, Performance Cards, Quick Actions
│   ├── /editor (or /editor/{id})       -> Rich WYSIWYG / Markdown Content Creator
│   ├── /articles                       -> Filtered Content Grid (Drafts, In-Review, Published)
│   ├── /media                          -> Personal Media Library & Asset Manager
│   ├── /analytics                      -> In-Depth Readership, Scroll Depth, CTR & Referral Data
│   ├── /rewards                        -> Non-Cash Badges, Level Progress, Promo Credit Balance
│   ├── /monetization                   -> Affiliate Stats, Ad Share, Payout Requests
│   └── /comments                       -> Unified Comment Moderation for Author's Content
│
├── /admin                              -> System Administrator & CMS Hub
│   ├── /dashboard                      -> Platform Health, Revenue, Growth & Activity
│   ├── /content                        -> Universal Content Management & Editorial Review Queue
│   ├── /users                          -> User & Author Management, Roles, Verification Badges
│   ├── /tools                          -> Tool, GPT & Prompt Moderation
│   ├── /campaigns                      -> Campaign & Advertisement Scheduler
│   ├── /monetization                   -> Revenue Engine, Invoicing, Payout Approvals
│   ├── /moderation                     -> Spam Queue, Abuse Reports, Plagiarism Audits
│   ├── /seo                            -> Dynamic Sitemaps, Redirects (301/302), Canonical Rules
│   ├── /analytics                      -> Comprehensive Platform Traffic & Funnel Analytics
│   ├── /system                         -> Global Configuration, SMTP, Backups & Audit Logs
│   └── /security                       -> Active Sessions, Failed Logins, 2FA Enforcement
│
└── /api/v1                             -> RESTful API Endpoints
    ├── /auth/*                         -> Login, Refresh Token, Register, Me
    ├── /content/*                      -> Articles, Tools, GPTs, Prompts (CRUD)
    ├── /categories, /tags              -> Taxonomies & Lookups
    ├── /comments/*                     -> Nested Threads, Post, Moderate, React
    ├── /reactions/*                    -> 6-Reaction Toggle & Counts
    ├── /search                         -> Fulltext Search Engine
    ├── /author/*                       -> Creator Studio Data & Media Uploads
    ├── /admin/*                        -> Administrative Operations & Workflows
    └── /analytics/event                -> High-Speed Client Telemetry Collector
```

---

## 5. Phased Engineering Execution Roadmap

```
PHASE 1–5: Core Engine, Brand & Database
├── Step 1: Initialize design tokens, CSS variables, typography & layout resets.
├── Step 2: Establish Single-Domain PHP Front Controller, Routing & Middleware stack.
├── Step 3: Execute full MySQL DDL schema (Tables, Indexes, Foreign Keys, Triggers).
└── Step 4: Implement Core Content Engine Model & Polymorphic abstraction.

PHASE 6–10: Authentication, Author Platform & CMS
├── Step 5: Build User/Author/Admin Auth (Bcrypt, Sessions, CSRF, 2FA, Verification).
├── Step 6: Create Author Dashboard, Media Library & Rich Editor (Quill/EasyMDE).
├── Step 7: Build Editorial Approval Workflow (Draft -> Review -> Publish -> Versioning).
└── Step 8: Build Admin Control Panel & Universal Content Manager.

PHASE 11–20: Public Discovery, AI Directory & Community
├── Step 9: Develop Public Homepage, Blog Stream, Category/Tag pages & Single Article.
├── Step 10: Develop AI Tools Directory, GPTs catalog & Interactive Prompt Library.
├── Step 11: Implement 2-level Nested Commenting, 6-Reaction Engine & Anti-Spam filters.
└── Step 12: Implement Global Instant Search with autocomplete and typo-tolerance.

PHASE 21–30: Monetization, Rewards & SEO
├── Step 13: Build Zero-Cash-Burn Non-Cash Reward Engine (Badges, Credits, Spotlights).
├── Step 14: Implement Monetization Modules (Ad slots, Sponsored labeling, Affiliate engine).
├── Step 15: Deploy JSON-LD Schema.org generators, OpenGraph tags & XML Sitemaps.
└── Step 16: Build Automated Internal Linking Engine & Recommendation Matrix.

PHASE 31–40: Security, Polish, Mobile PWA & Launch
├── Step 17: Harden Security (CSP, Rate Limiting, MIME sniffing defense, Audit logs).
├── Step 18: Optimize Performance (Critical CSS, WebP conversion, DB Query Caching).
├── Step 19: Deploy PWA Service Worker, Manifest, Offline Fallbacks & Mobile UI polish.
└── Step 20: Run 960-Task Verification Checklist & Launch Readiness Certification.
```
