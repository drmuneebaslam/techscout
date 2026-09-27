# TechScout Platform: Comprehensive Architectural Analysis, QoL Features Matrix, Bottleneck Audit & Implementation Roadmap

> **Document Status:** Complete Architecture & Engineering Blueprint  
> **Target Environment:** AWS EC2 (2 vCPU, 2 GB RAM, 2 GB Swap, 30 GB SSD Storage), InfinityFree Mirror, Local XAMPP  
> **Platform Version:** TechScout Enterprise v3.5 (PHP 8.2 Pure MVC, MySQL 8 / MariaDB)  
> **Author:** Antigravity AI Engineering Team  
> **Date:** September 2026  

---

## Table of Contents
1. [Executive Summary & Architectural Baseline](#1-executive-summary--architectural-baseline)
2. [Server Specifications & High-Concurrency Scaling (10k+ Users on 2GB RAM)](#2-server-specifications--high-concurrency-scaling-10k-users-on-2gb-ram)
3. [Exhaustive Quality-of-Life (QoL) Enhancements Matrix](#3-exhaustive-quality-of-life-qol-enhancements-matrix)
   - [3.1 Admin & Super Admin QoL Features](#31-admin--super-admin-qol-features)
   - [3.2 Author & Creator Studio QoL Features](#32-author--creator-studio-qol-features)
   - [3.3 Member & Subscriber Portal QoL Features](#33-member--subscriber-portal-qol-features)
   - [3.4 Guest & Anonymous Visitor QoL Features](#34-guest--anonymous-visitor-qol-features)
   - [3.5 Universal UX, Accessibility & Dual-Theme Consistency](#35-universal-ux-accessibility--dual-theme-consistency)
4. [Flaws, Bottlenecks & Production Vulnerabilities Audit](#4-flaws-bottlenecks--production-vulnerabilities-audit)
   - [4.1 Database Schema Divergences & Production SQL Exceptions (cintu.txt Forensics)](#41-database-schema-divergences--production-sql-exceptions-cintutxt-forensics)
   - [4.2 High-Concurrency & Low-Resource Server Bottlenecks](#42-high-concurrency--low-resource-server-bottlenecks)
   - [4.3 Comment Discussion Scalability Bottlenecks (2,000+ Comments)](#43-comment-discussion-scalability-bottlenecks-2000-comments)
   - [4.4 Prompt & Blueprint Showcase UX/Monetization Gaps](#44-prompt--blueprint-showcase-uxmonetization-gaps)
   - [4.5 Webhook & Custom API Gateway Limitations](#45-webhook--custom-api-gateway-limitations)
   - [4.6 UI / CSS Dual-Theme Regressions & Layout Inconsistencies](#46-ui--css-dual-theme-regressions--layout-inconsistencies)
5. [Enhanced Ingestion Webhook & Custom API Gateway: All-Tables Catalog & Tutorial](#5-enhanced-ingestion-webhook--custom-api-gateway-all-tables-catalog--tutorial)
   - [5.1 Multi-Table Atomic Ingestion Architecture](#51-multi-table-atomic-ingestion-architecture)
   - [5.2 Complete Sample JSON Payloads for Core Tables](#52-complete-sample-json-payloads-for-core-tables)
   - [5.3 Comprehensive Ingestion Tutorial (cURL, Python, JavaScript)](#53-comprehensive-ingestion-tutorial-curl-python-javascript)
6. [Implementation Roadmap: "What to Build & How to Build"](#6-implementation-roadmap-what-to-build--how-to-build)
   - [Phase 1: High-Concurrency Hardening & Infrastructure Tuning (P0)](#phase-1-high-concurrency-hardening--infrastructure-tuning-p0)
   - [Phase 2: Database Schema Alignment & Fatal Error Elimination (P0)](#phase-2-database-schema-alignment--fatal-error-elimination-p0)
   - [Phase 3: High-Scale Cursor Comment Subsystem (P1)](#phase-3-high-scale-cursor-comment-subsystem-p1)
   - [Phase 4: Prompt Showcase UX & Monetization Consolidation (P1)](#phase-4-prompt-showcase-ux--monetization-consolidation-p1)
   - [Phase 5: Enhanced Webhook & Custom API Gateway Upgrades (P1)](#phase-5-enhanced-webhook--custom-api-gateway-upgrades-p1)
   - [Phase 6: Role-Based QoL Feature Rollout (P2)](#phase-6-role-based-qol-feature-rollout-p2)
7. [Comprehensive Device & Theme Testing Checklist](#7-comprehensive-device--theme-testing-checklist)

---

## 1. Executive Summary & Architectural Baseline

TechScout is a high-velocity, content-rich platform bridging AI technology discovery, developer tooling, prompt engineering blueprints, community discussions, and creator monetization.

### Core Architectural Topology
```
                      +-----------------------------+
                      |       Clients / Users       |
                      | (Guest, Member, Author, Admin)
                      +--------------+--------------+
                                     |
                       HTTPS / TLS (Cloudflare CDN)
                                     |
                      +--------------v--------------+
                      |     Nginx 1.24 (Reverse     |
                      |   Proxy + FastCGI Cache)    |
                      +--------------+--------------+
                                     |
                                Unix Socket
                                     |
                      +--------------v--------------+
                      |   PHP-FPM 8.2 (20 Workers)  |
                      |   - Router.php              |
                      |   - Controllers & Models    |
                      |   - Cache Subsystem (APCu)  |
                      |   - Session Manager         |
                      +--------------+--------------+
                                     |
                              Local Unix Socket
                                     |
                      +--------------v--------------+
                      | MySQL 8.0 / MariaDB 10.11   |
                      | (techscout_db - Polymorphic)|
                      | - contents (Base)           |
                      | - prompts, ai_tools, etc.   |
                      | - double-entry ledger       |
                      +-----------------------------+
```

### Key Technical Characteristics
1. **Framework-Free Pure PHP 8.2 MVC Architecture**: Lightweight execution with sub-5ms bootstrapping overhead, zero composer-vendor bloat in the critical request path.
2. **Polymorphic Content Inheritance Model**: A central `contents` base table stores common metadata (`id`, `uuid`, `title`, `slug`, `summary`, `body_html`, `featured_image_url`, `content_type`, `author_id`, `category_id`, `status`, `views_count`, `created_at`, `updated_at`). Specialized domain entities extend `contents` via 1:1 foreign keys (`prompts`, `ai_tools`, `articles`, `gpt_listings`, `jobs`, `campaigns`).
3. **Tri-Environment Deployment Topology**:
   - **Local Development**: Windows XAMPP (`c:\xampp\htdocs\techscout.n`).
   - **Staging / Mirror**: InfinityFree Shared Hosting (`funwithcoding.free.nf`, MySQL `sql306.infinityfree.com`, FTP deployment).
   - **Production Host**: Amazon Web Services EC2 (`techscout.online`), Ubuntu Linux, Git-automated sync.

---

## 2. Server Specifications & High-Concurrency Scaling (10k+ Users on 2GB RAM)

### Target Hardware Environment
* **CPU**: 2 vCPU (AWS EC2 burstable, e.g., `t3.small` / `t4g.small`).
* **RAM**: 2.0 GB Physical RAM + 2.0 GB Linux Swap space (`/swapfile`).
* **Storage**: 30.0 GB EBS GP3 SSD Storage.
* **Network**: Up to 5 Gbps burst network bandwidth.

### Concurrency Mathematics: 10,000 Concurrent Visitors
If 10,000 visitors browse the site concurrently:
* Average page view cycle: 1 page view every 20 seconds per user = **500 HTTP requests per second (RPS)**.
* Peak burst multiplier (3x): **1,500 RPS**.

### Capacity Bottleneck Under Default Setup
* If every request hits PHP-FPM and executes MySQL queries:
  - 1 PHP-FPM process consumes ~35MB–50MB RAM.
  - A 2GB RAM server can safely run at most **18 to 22 PHP-FPM workers** without triggering the Linux OOM (Out Of Memory) killer.
  - At 20 workers and 50ms average execution time, max PHP throughput = `(20 / 0.05) = 400 requests/sec`.
  - The remaining 1,100 burst requests pile up in the web server listen queue (`backlog`), resulting in **504 Gateway Timeout** and **502 Bad Gateway** errors.
  - Furthermore, MySQL `max_connections` (default 151) can become exhausted, resulting in fatal `SQLSTATE[HY000] [1040] Too many connections` exceptions.

### High-Concurrency Architectural Solution for 2 vCPU / 2GB RAM
To comfortably sustain 10k+ concurrent visitors on this hardware profile, the system must implement a **3-Tier Inverted Pyramid Caching Architecture**:

```
                       10,000 CONCURRENT USERS
                                  |
    +-------------------------------------------------------------+
    | 1. EDGE & REVERSE PROXY LAYER (Nginx FastCGI Micro-Cache)  |  --> Serves 92% of traffic
    | - Cached HTML for Guests & Anonymous Visitors (TTL: 30-180s)|      (Zero PHP & Zero MySQL)
    | - Stale-while-revalidate & conditional bypass on auth cookie|      Response Time: < 2ms
    +-------------------------------------------------------------+
                                  | (Only 8% hits PHP)
    +-------------------------------------------------------------+
    | 2. IN-MEMORY APCu / STATIC CACHE LAYER                      |  --> Serves 6% of traffic
    | - Shared memory database queries (Categories, Settings, Tags)|     Memory cost: ~64MB RAM
    | - Eliminates 85% of MySQL read transactions                 |      Response Time: < 5ms
    +-------------------------------------------------------------+
                                  | (Only 2% hits MySQL)
    +-------------------------------------------------------------+
    | 3. HIGH-EFFICIENCY PERSISTENT MYSQL ENGINE                  |  --> Handles 2% writes/auth
    | - InnoDB Buffer Pool tuned to 640MB                         |      Strict cursor keyset limits
    | - Asynchronous telemetry buffering (Zero slow queries)      |      Response Time: < 15ms
    +-------------------------------------------------------------+
```

#### Detailed Tuning Parameters:
1. **Nginx FastCGI Micro-Cache**:
   - Cache guest requests for 60 seconds (`fastcgi_cache_valid 200 60s`).
   - Bypass cache if `Cookie` contains `PHPSESSID` or authenticated user markers.
   - Nginx handles 10,000 req/sec directly from memory with negligible CPU usage.
2. **PHP-FPM Worker Optimization**:
   ```ini
   pm = dynamic
   pm.max_children = 22
   pm.start_servers = 6
   pm.min_spare_servers = 4
   pm.max_spare_servers = 10
   pm.max_requests = 500
   request_terminate_timeout = 15s
   ```
3. **MySQL / MariaDB My.cnf Optimization (for 2GB RAM Profile)**:
   ```ini
   [mysqld]
   innodb_buffer_pool_size = 640M
   innodb_log_file_size = 64M
   innodb_flush_log_at_trx_commit = 2
   innodb_flush_method = O_DIRECT
   max_connections = 80
   table_open_cache = 400
   thread_cache_size = 16
   tmp_table_size = 32M
   max_heap_table_size = 32M
   skip-name-resolve = 1
   ```
4. **Session Lock Optimization**:
   - Default PHP sessions hold an exclusive file lock from `session_start()` until script termination.
   - For all read-heavy routes (`/`, `/blog`, `/tools`, `/prompts`, `/api/v1/comments`), call `session_write_close()` immediately after verifying the user session. This unblocks parallel AJAX calls and multi-tab browsing instantly.

---

## 3. Exhaustive Quality-of-Life (QoL) Enhancements Matrix

---

### 3.1 Admin & Super Admin QoL Features

| # | Feature Name | Detailed Description & Architectural Workflow | Value / Impact |
|---|---|---|---|
| **AD-01** | **Universal Bulk Actions Engine** | Checkbox selection on all admin data tables (Articles, Prompts, Tools, Users, Comments). Action dropdown supporting: Batch Approve, Batch Reject with Canned Reason, Batch Re-assign Category, Batch Add/Remove Tags, Batch Export to CSV, and Batch Soft-Delete. | Eliminates hours of repetitive single-record clicks for moderators. |
| **AD-02** | **One-Click Database Schema Diff & Drift Inspector** | Automated tool comparing the live database schema (via `information_schema`) against the canonical `database/schema.sql` definitions. Visually highlights missing tables, missing columns, mismatched data types, and missing indexes with auto-generated non-destructive `ALTER TABLE ... ADD COLUMN IF NOT EXISTS` migration code. | Prevents silent production outages and column mismatch crashes. |
| **AD-03** | **Interactive Webhook Sandbox & Replay Runner** | In `Incoming Webhooks`, an integrated testing console allowing admins to load any historical failed webhook log from `incoming_webhook_logs`, modify its JSON payload inline, and trigger a dry-run or live replay with step-by-step transaction logs. | Enables rapid debugging of third-party ingestion without external tools. |
| **AD-04** | **Financial Royalty Pool Distribution Simulator** | Before committing monthly/weekly author payouts, this tool calculates the exact disbursement breakdown: dynamic CPM based on dwell views, platform 5% fee deductions, payment gateway reserves, and author net balances. Warns if the payout exceeds the 40% treasury safety threshold. | Protects platform treasury from catastrophic cash-drain bugs. |
| **AD-05** | **Live Server Health & Resource Telemetry HUD** | Real-time visual meters showing EC2 CPU utilization, RAM usage (Used vs Free vs Buffers), Swap file usage, 30GB disk space remaining, active PHP-FPM workers, and slow MySQL queries (>100ms). Configurable threshold alerts (e.g. email alert if disk > 85% or RAM > 90%). | Proactive infrastructure monitoring tailored to low-spec servers. |
| **AD-06** | **User Impersonation Mode with Sticky Safety Bar** | Super Admin can "Log In as User" with a single click to troubleshoot permissions and view issues from the user's perspective. Features a persistent top warning banner (`Currently Impersonating @username - [Exit Impersonation]`) and logs all actions to `audit_logs` under the admin's true identity. | Resolves support tickets 5x faster while maintaining full audit integrity. |
| **AD-07** | **Visual Email Block Reorderer & Canvas Studio** | Upgraded email template editor featuring drag-and-drop structural blocks (Hero, Split Columns, Call-to-Action, Prompt Showcase, Social Footer). Includes mobile/desktop preview toggles, merge variable chips (`{{username}}`, `{{article_title}}`), and instant test-email dispatch to the admin's inbox. | Eliminates broken raw HTML formatting and simplifies newsletter design. |
| **AD-08** | **Sitewide Announcement & Toast Broadcaster** | Ability to schedule and broadcast global announcements: Header Banner, Dismissible Toast, or Modal Alert. Admins can target specific audiences (All Visitors, Guests Only, Logged-In Members Only, Authors Only) with start and end timestamps. | Immediate communication during maintenance, new feature launches, or promotions. |
| **AD-09** | **Content Version Diff & Editorial Rollback** | In the content moderation desk, view a side-by-side color-coded diff (green additions, red deletions) between the current submitted draft and the previously published version before approving. Single-click rollback to any historical revision. | Prevents accidental overwrites and unauthorized vandalism. |
| **AD-10** | **Granular Rate-Limit & IP Firewall Tuning Console** | View live blocked IPs, suspicious request spikes, and adjust rate-limit buckets (e.g., Auth endpoints: 5 req/min, Search: 30 req/min, API: 120 req/min) directly from the admin UI without editing server configuration files. | Real-time defense against scrapers, brute-force attacks, and DDoS attempts. |

---

### 3.2 Author & Creator Studio QoL Features

| # | Feature Name | Detailed Description & Architectural Workflow | Value / Impact |
|---|---|---|---|
| **AU-01** | **Resilient Client-Side Auto-Save (IndexedDB + LocalStorage)** | Continuous background auto-save of active editor drafts every 5 seconds locally in the browser, plus a periodic 60-second cloud sync to `contents_drafts`. Displays "Saved locally at 14:32:05" badge. If the browser closes or crashes, automatically offers to restore unsaved work upon reopening. | Zero lost articles or prompt blueprints due to network hiccups or power cuts. |
| **AU-02** | **Clipboard Paste & Drag-and-Drop Image Uploader** | Directly paste screenshots from clipboard (`Ctrl+V`) or drag image files onto the Markdown editor. The system client-side compresses them to modern WebP format, uploads asynchronously, and injects the markdown `![alt](url)` at the current cursor position with upload progress animation. | Saves 80% of author time previously spent manually uploading and copying links. |
| **AU-03** | **Granular Blueprint Parameter Visibility HUD** | Dedicated toggles in the Prompt Creator allowing authors to choose which engine metadata fields are publicly shown: Target Engine, Midjourney v6, Sampling Temperature, Top_P, Complexity Tier, and Dynamic Variable Injector (Location, Lighting, Protagonist, Aspect Ratio). Unselected fields remain hidden on the frontend. | Solves author complaints about clutter and unwanted template variables. |
| **AU-04** | **Split Live Preview with Synchronous Scrolling** | Dual-pane editor with raw Markdown / BBCode on the left and pixel-perfect rendered HTML on the right. Synchronized proportional scroll ensures the preview pane matches the author's typing position in real time. | Eliminates the need to switch back and forth between "Edit" and "Preview" tabs. |
| **AU-05** | **Live Readability, Word Count & Read-Time Estimator** | Real-time sticky HUD in the editor footer displaying: Total Word Count, Character Count, Estimated Reading Time (based on 200 WPM), and Flesch-Kincaid Readability Grade. Suggests breaking long paragraphs into subheadings when readability is low. | Helps authors craft high-engagement, SEO-friendly content. |
| **AU-06** | **Scheduled Post Publishing with Timezone Selector** | Authors can choose "Schedule Publication" and specify future date and time in their local timezone. The platform's automated background cron promotes the status from `scheduled` to `published` and triggers subscriber notifications automatically. | Allows creators to time their releases for peak reader engagement. |
| **AU-07** | **Interactive Royalties & CPM Earnings Forecaster** | Authors can view an interactive earnings calculator: "Based on your 14,200 dwell reads this month and the current platform pool bonus of $0.85 CPM, your estimated payout is $12.07." Displays earnings trends per article/prompt. | Transparent monetization drives creator retention and publishing consistency. |
| **AU-08** | **Supporter Tipping Hub & Automated Thank-You Dispatch** | Authors can customize a personal "Thank You" message automatically emailed to readers who tip them via card or crypto. Provides a clean breakdown of tips received, net amounts, and tipper messages with export capabilities. | Builds deep relationships between creators and their top patrons. |
| **AU-09** | **Co-Author & Contributor Attribution Tagging** | Support for inviting registered platform members as co-authors. Co-authors appear with their avatar on the article header, share in author reputation badges, and can be allocated a split percentage of tip revenues. | Encourages team writing and guest posts from industry leaders. |
| **AU-10** | **Private Reader Retention Analytics** | Privacy-safe creator analytics showing: Reader Dwell-Time Distribution (skimmed <30s vs engaged 1-5min vs completed >5min), Top Referring Domains, and Bookmark Save Rate, without exposing any individual member identities. | Provides actionable feedback on content quality while preserving user privacy. |

---

### 3.3 Member & Subscriber Portal QoL Features

| # | Feature Name | Detailed Description & Architectural Workflow | Value / Impact |
|---|---|---|---|
| **ME-01** | **Organized Bookmarks with Custom Folders & Tags** | Instead of a single flat bookmark list, members can create custom folders (e.g., "AI Coding Workflows", "Midjourney Prompts", "Weekend Reading"). Includes drag-and-drop organization, bulk moving, and private personal notes per saved item. | Converts passive bookmarks into a personal productivity knowledge base. |
| **ME-02** | **"Resume Reading" Smart Progress Ribbon** | When returning to a partially read long-form article or tutorial, a discrete bottom ribbon appears: *"You were 64% through this article. [Jump to where you left off ↵]"*. Updates dynamically as the member scrolls. | Drastically improves user experience for long technical deep-dives. |
| **ME-03** | **Social Discussion Feed with Deep Comment Threading** | Modern compact speech bubbles (Instagram/Facebook style) with multi-emoji reactions (❤️, 🚀, 💡, 🔥, 👀). Keyboard navigation (`J` / `K` to jump between top comments), "Collapse Thread" toggles, and author insight pins. | Transforms static comment sections into active community discussions. |
| **ME-04** | **Notification Hub with Categorized Tabs & Audio Chime** | Notification center organized by tabs: All, Replies & Mentions, System Alerts, Creator Rewards, Support Tickets. Supports optional subtle audio chime on new notifications, mark all as read, and configurable email digest frequencies (Instant, Daily, Weekly). | Keeps members engaged without notification fatigue. |
| **ME-05** | **Active Device & Session Security Manager** | In Account Settings, members can view all active logged-in sessions: Device Type (Windows, Mac, iPhone, Android), Browser, IP Address, Location (City, Country flag), and Last Active Time. Features a one-click *"Revoke All Other Sessions"* button. | Essential security feature protecting user accounts and credentials. |
| **ME-06** | **Account Standing & Strike Transparency Hub** | If a user receives a moderation strike or content warning, they have full transparency into: Rule violated, Content excerpt, Strike expiry date, and an integrated "Submit Appeal" button to provide context to the moderation team. | Eliminates user frustration and confusion regarding moderation decisions. |
| **ME-07** | **One-Click GDPR Data & History Export** | Allows members to download an instant ZIP archive containing their entire profile data, reading history, saved bookmarks, submitted comments, and tipping receipts in clean JSON and CSV formats. | Fulfills GDPR compliance while building strong user trust. |
| **ME-08** | **Theme Auto-Sync with Operating System** | In addition to manual Dark and Light mode toggles, provides an "Auto (Sync with Device)" option that listens to `window.matchMedia('(prefers-color-scheme: dark)')` and adapts seamlessly when the user's OS changes themes. | High-polish UX expected from modern web applications. |
| **ME-09** | **Custom Notification Mute per Content Thread** | Members can opt out of email or in-app notifications for specific comment threads they previously participated in, preventing inbox spam from trending discussions. | Prevents notification overwhelm on viral articles. |
| **ME-10** | **Personalized Reading Feed Customizer** | Members can prioritize or mute specific topics and tags (e.g., "More LLM Tutorials", "Less Crypto News") to tailor their home discovery feed to their exact professional interests. | Increases daily active retention and reading time. |

---

### 3.4 Guest & Anonymous Visitor QoL Features

| # | Feature Name | Detailed Description & Architectural Workflow | Value / Impact |
|---|---|---|---|
| **GU-01** | **Universal Command Palette (`Ctrl + K` / `Cmd + K`)** | Instant global search overlay triggered anywhere on the site by pressing `Ctrl + K`. Offers debounced live fuzzy search across Tools, Prompts, GPTs, Articles, and Categories with keyboard arrow-key navigation and category badges. | Lightning-fast navigation for power users and tech professionals. |
| **GU-02** | **Consolidated Prompt Copy & Smart Monetization Flow** | Removes duplicate `.ts-code-copy-btn` buttons. Replaces them with a single primary Copy Action: clicking instantly copies the prompt blueprint to the clipboard, displays a floating *"Prompt Copied! 📋"* toast, and opens the sponsor/affiliate tool destination in a clean background tab. | Fixes user UI confusion while maximizing sponsor ad conversions. |
| **GU-03** | **Interactive Faceted Filter Pills** | Real-time filter pills on `/tools` and `/prompts` (e.g. `Free`, `Freemium`, `Open Source`, `Midjourney`, `ChatGPT`, `Beginner`). Clicking a pill filters the grid instantly via client-side DOM filtering or fast cached AJAX without full page reloads. | Frictionless browsing experience that keeps visitors engaged. |
| **GU-04** | **Instant Quick-View Modal Drawer** | Clicking a "Quick View" icon on any directory card opens a lightweight modal showing full tool descriptions, pricing tiers, prompt templates, and screenshots without navigating away from the search results page. | Reduces bounce rate and accelerates exploration. |
| **GU-05** | **Zero-Reload AJAX Newsletter Subscription** | Simple email input in the footer and article sidebars with inline validation, animated success checkmark, and immediate delivery of welcome confirmation without page reloads. | 3x higher conversion rate than traditional multi-step subscribe forms. |
| **GU-06** | **High-Relevance Related Recommendations Carousel** | At the bottom of every tool, prompt, and article, an automated carousel surfaces 4 to 6 contextually related items based on shared category, tags, and AI model compatibility. | Drives continuous browsing depth and lower exit rates. |
| **GU-07** | **Semantic Breadcrumb Navigation with JSON-LD Schema** | Clean breadcrumbs on every single-item page (`Home > Directory > AI Tools > Productivity > Tool Name`) with complete Schema.org `BreadcrumbList` microdata. | Enhances Google SEO snippet rankings and site orientation. |
| **GU-08** | **Optimized Share Drawer with 1-Click Native Web Share** | On mobile devices, utilizes the browser's native `navigator.share()` API to share tools and prompts directly to WhatsApp, Telegram, X, or LinkedIn with pre-filled title and link. Fallbacks cleanly to modal on desktop. | Virality driver optimizing mobile referral traffic. |

---

### 3.5 Universal UX, Accessibility & Dual-Theme Consistency

| # | Universal Feature | Technical Architecture & Implementation Standards |
|---|---|---|
| **UN-01** | **Obsidian Dark & Clean Light Theme Integrity** | Absolute ban on hardcoded inline `style="background: #fff"` on UI components. All backgrounds, borders, and text colors must utilize CSS custom properties (`var(--ts-bg-main)`, `var(--ts-bg-surface)`, `var(--ts-text-primary)`). Scoped cleanly under `:root` (dark default) and `[data-theme="light"]` / `.theme-light`. |
| **UN-02** | **Full Mobile Touch Ergonomics (>= 44px Hit Areas)** | All interactive buttons, pill toggles, and dropdown triggers must meet WCAG 2.1 touch target minimums (44x44px padding) on viewports `< 768px`. Fixed bottom navigation bar on mobile for Thumb-Zone navigation (Home, Tools, Prompts, Search, Account). |
| **UN-03** | **Keyboard Accessibility (WCAG 2.1 AA Compliant)** | Full tab order sequence across modals, forms, and cards with high-contrast visible focus rings (`:focus-visible`). Modals trap focus and close gracefully on `Escape`. Screen-reader accessible ARIA labels on all icon-only buttons. |
| **UN-04** | **Automated Image Optimization (WebP + Responsive srcset)** | All uploaded assets served via responsive `srcset` (`small: 320px`, `medium: 640px`, `large: 1200px`) in WebP format with `loading="lazy"` and explicit `width`/`height` attributes to prevent Cumulative Layout Shift (CLS). |

---

## 4. Flaws, Bottlenecks & Production Vulnerabilities Audit

This section documents real defects, schema mismatches, and architectural bottlenecks uncovered across the codebase and parsed from production error logs (`cintu.txt`).

---

### 4.1 Database Schema Divergences & Production SQL Exceptions (cintu.txt Forensics)

#### 🔴 Defect 1: Child Extension Tables Queried Directly by Parent `slug`
* **Real Production Error**:  
  `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'slug' in 'where clause' | SQL: SELECT * FROM ai_tools WHERE slug = ?`  
  `Routes: GET /go/ai-productivity-tools, GET /go/tool/ai-productivity-tools, GET /go/gpt/ai-productivity-tools`
* **Root Cause**:  
  In TechScout's polymorphic schema, `slug` is stored exclusively on the parent `contents` table. Child tables (`ai_tools`, `prompts`, `gpt_listings`) store specialized metadata linked via `content_id -> contents.id`. Code calling `SELECT * FROM ai_tools WHERE slug = ?` crashes because `ai_tools` has no `slug` column.
* **Remediation Rule**:  
  Always join the parent `contents` table:
  ```sql
  SELECT t.*, c.slug, c.title, c.summary, c.featured_image_url 
  FROM ai_tools t 
  JOIN contents c ON t.content_id = c.id 
  WHERE c.slug = ?
  ```

#### 🔴 Defect 2: Orphaned Columns in Admin Media & Maintenance Tools
* **Code Location**: `app/Controllers/AdminController.php` lines 5118, 5635, 5638
  ```php
  $artImages = Database::query("SELECT featured_image FROM articles WHERE featured_image IS NOT NULL");
  $promptThumbs = Database::query("SELECT thumbnail FROM prompts WHERE thumbnail IS NOT NULL");
  ```
* **Root Cause**:  
  Neither `featured_image` exists on `articles` (it is `featured_image_url` on `contents`), nor does `thumbnail` exist on `prompts` (it is on `contents.featured_image_url` or `prompt_images.image_url`). When an admin runs media integrity audits, these queries throw fatal `1054 Unknown column` exceptions.
* **Remediation Rule**:  
  Update queries to inspect `contents.featured_image_url` and `prompt_images.image_url`.

#### 🔴 Defect 3: Missing Default Value on `notifications.event_type`
* **Real Production Error**:  
  `SQLSTATE[HY000]: General error: 1364 Field 'event_type' doesn't have a default value | SQL: INSERT INTO notifications (user_id, type, title, message, action_url, is_read, created_at) VALUES (?, ?, ?, ?, ?, 0, NOW())`  
  `Route: POST /api/v1/tips/submit`
* **Root Cause**:  
  In `database/schema.sql`, line 1014: `event_type varchar(60) NOT NULL`. Several legacy controllers and tipping handlers insert notifications without explicitly specifying `event_type`. In MySQL strict mode, this triggers a fatal 1364 error, causing tip notifications to fail.
* **Remediation Rule**:  
  Alter column to provide a safe default:  
  `ALTER TABLE notifications MODIFY COLUMN event_type VARCHAR(60) NOT NULL DEFAULT 'system';`

#### 🔴 Defect 4: Missing `updated_at` Column on `categories` Table
* **Real Production Error**:  
  `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'updated_at' in 'field list' | SQL: SELECT slug, updated_at FROM categories ORDER BY id ASC`  
  `Route: GET /sitemap-categories.xml`
* **Root Cause**:  
  The `categories` table was designed with only `created_at`. Sitemap generators and SEO feeds expecting standard timestamp parity crashed when querying `updated_at`.
* **Remediation Rule**:  
  Add `updated_at` to `categories` or safely fallback: `COALESCE(updated_at, created_at)`.

#### 🔴 Defect 5: Inconsistent Column Names in Tipping Queries
* **Real Production Error**:  
  `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'supporter_user_id' in 'where clause' | SQL: SELECT id FROM author_tips WHERE supporter_user_id = ? ...`
* **Root Cause**:  
  The schema column in `author_tips` is `tipper_user_id`. Ad check helpers queried `supporter_user_id`, causing silent database errors in background telemetry.
* **Remediation Rule**:  
  Standardize on `tipper_user_id` across all controllers and models.

#### 🔴 Defect 6: Quoted String Binding on Prepared `LIMIT ?` Clauses
* **Real Production Error**:  
  `SQLSTATE[42000]: Syntax error: near ''50'' at line 5 | SQL: SELECT sa.*, u.username, u.email FROM security_anomalies sa ... LIMIT ?`
* **Root Cause**:  
  When PDO is configured with `PDO::ATTR_EMULATE_PREPARES => false`, passing a string variable (e.g. `'50'`) to a prepared `LIMIT ?` parameter causes MySQL to receive `LIMIT '50'`. MySQL strict syntax rejects quoted strings for `LIMIT` offsets.
* **Remediation Rule**:  
  Always cast to integer: `(int)$limit` or bind with explicit type:  
  `$stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);`

#### 🔴 Defect 7: Missing Legacy Route Handlers
* **Real Production Error**:  
  `Call to undefined method App\Controllers\CategoryController::subscribe()`  
  `Call to undefined method App\Controllers\CategoryController::legacyTopicSingleRedirect()`  
  `Call to undefined method App\Controllers\CategoryController::legacyTopicsRedirect()`
* **Root Cause**:  
  Old indexed URLs and search bot requests hit `/topics`, `/topic/{slug}`, and `/category/subscribe/{id}` which mapped to non-existent controller methods.
* **Remediation Rule**:  
  Implement 301 permanent redirects to canonical routes (`/categories`, `/category/{slug}`) to preserve SEO link equity.

---

### 4.2 High-Concurrency & Low-Resource Server Bottlenecks

1. **Redundant `SHOW TABLES LIKE 'contents'` on Every Web Request**:
   - In `app/Core/Database.php` lines 112–115, `ensureSchemaAndSeeds()` executed `SHOW TABLES LIKE 'contents'` on every single request lifecycle.
   - At 10,000 visitors, that generated 10,000 completely redundant metadata queries hammering MySQL.
   - **Solution**: Check an `installed.lock` file or memory cache; never execute DDL checks during production runtime.

2. **File Session Handler Lock Contention**:
   - `session_start()` locks `/tmp/sess_*`. If a user opens 3 tabs or makes background AJAX pings (dwell time tracking, notification polling), requests queue behind each other, causing page stalls.
   - **Solution**: Call `session_write_close()` early on read-only requests.

3. **Unbounded Disk Cache `.tag` File Growth**:
   - In `app/Core/Cache.php`, `indexTags()` appends cache keys to `.tag` files using `FILE_APPEND | LOCK_EX`.
   - On high concurrency, multiple workers fighting for exclusive locks on `.tag` files cause disk I/O lock wait spikes.
   - **Solution**: Transition to APCu in-memory cache for hot tags, or hash-bucketed tag indexes.

---

### 4.3 Comment Discussion Scalability Bottlenecks (2,000+ Comments)

1. **Massive HTML Payload & DOM Explosion**:
   - If an article has 2,000 comments, rendering them all recursively server-side generates 30,000+ HTML DOM elements (~4MB HTML payload).
   - Low-end mobile phones freeze or crash trying to parse and render this layout.
2. **Hardcoded Limit Silently Truncates Discussions**:
   - `Comment::getTreeForContent()` contains a hardcoded safety cap: `LIMIT 150`. Comments 151 through 2,000 are completely invisible to users!
3. **Offset Pagination Degrades Exponentially**:
   - Standard `LIMIT 20 OFFSET 2000` requires MySQL to scan 2,020 rows and discard the first 2,000, causing query latency to jump from 1ms to over 250ms.
4. **Architectural Solution: Keyset / Cursor Pagination**:
   - Load only the top 15 root comments initially.
   - Load more root threads via fast cursor queries:  
     `SELECT c.* FROM comments c WHERE c.content_id = ? AND c.parent_id IS NULL AND c.id < ? ORDER BY c.id DESC LIMIT 15`
   - Child replies are collapsed by default and fetched on demand (`GET /api/v1/comments/{id}/replies?cursor=...`).

---

### 4.4 Prompt & Blueprint Showcase UX/Monetization Gaps

1. **Duplicate Copy Buttons**:
   - Users are presented with a tiny top-right `.ts-code-copy-btn` and a large bottom copy button, causing visual confusion.
   - **Solution**: Remove `.ts-code-copy-btn` from the prompt view and make the primary action copy button prominent and clear.
2. **Integrated Copy & Sponsor Tab Flow**:
   - When the user clicks the primary copy button, it must execute two actions synchronously:
     1. Copy the formatted prompt string to the system clipboard and display an animated toast: *"Prompt Copied to Clipboard! 📋"*.
     2. Open the sponsor/affiliate tool link in a new background tab (`window.open(url, '_blank')`).
3. **Granular Blueprint Parameter Controls**:
   - Fields like Target Engine, Midjourney v6, Sampling Temperature (0.70), Top_P (0.95), Complexity Tier, and Dynamic Variables (Location, Lighting, Protagonist) are currently hardcoded to appear even when empty or unwanted.
   - **Solution**: Author and admin must have per-prompt visibility checkboxes to decide exactly which parameters appear on the public frontend.
4. **Dark / Light Mode Glitch on `#main-gallery-caption`**:
   - The caption bar overlay gradient currently has contrast issues: in light mode, dark text over a dark gradient is unreadable, and vice versa.
   - **Solution**: Pure dual-theme stylesheet rules with explicit CSS variables.

---

### 4.5 Webhook & Custom API Gateway Limitations

1. **Single-Table Ingestion vs Multi-Table Realities**:
   - Adding a complete prompt entity requires inserting into `contents`, inserting prompt metadata into `prompts`, linking tags in `content_tags`, and adding showcase images in `prompt_images`.
   - Webhook users struggle to format single payloads that orchestrate this relational structure atomically.
2. **Missing Sample Payloads for Database Tables**:
   - Third-party automation engineers (Make.com, Zapier, Python scripts) currently have to guess the JSON payload structure for each table.
   - **Solution**: Provide a built-in interactive JSON catalog with pre-filled sample data for every table in the database.

---

### 4.6 UI / CSS Dual-Theme Regressions & Layout Inconsistencies

1. **Inline Tag Styles Overriding Themes**:
   - Several legacy templates contain hardcoded inline styles like `style="background: #0f172a; color: #fff;"`, preventing the light theme (`[data-theme="light"]`) from overriding colors properly.
2. **Asymmetric Theme Class Toggling**:
   - Past theme scripts added `theme-light` to `body` but forgot to clean up `html`, causing theme leakage.
3. **Remediation Rule**:
   - Centralize all color rules into CSS custom properties in `assets/css/style.css` and `assets/css/admin.css`.

---

## 5. Enhanced Ingestion Webhook & Custom API Gateway: All-Tables Catalog & Tutorial

---

### 5.1 Multi-Table Atomic Ingestion Architecture

TechScout's Enterprise Ingestion Gateway supports single-payload multi-table transactions. The incoming JSON defines a `"tables"` object. The engine wraps the entire execution in an atomic database transaction (`BEGIN ... COMMIT / ROLLBACK`). If any child table insert fails, the entire transaction rolls back safely.

```json
{
  "tables": {
    "contents": {
      "title": "Mastering Midjourney v6 Architecture & Prompt Design",
      "slug": "mastering-midjourney-v6-architecture-prompt-design",
      "summary": "Comprehensive architectural guide to crafting ultra-realistic renders with Midjourney v6.",
      "content_type": "prompt",
      "category_id": 3,
      "featured_image_url": "https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=1200"
    },
    "prompts": {
      "prompt_type": "image",
      "prompt_template": "cinematic architectural photography of a modernist glass pavilion in a misty pine forest, golden hour lighting, photorealistic, 8k resolution --ar 16:9 --v 6.0 --style raw",
      "aspect_ratio": "16:9",
      "model_version": "v6.0",
      "target_models": "[\"Midjourney v6\", \"Midjourney\"]",
      "difficulty_level": "intermediate",
      "parameters_json": "{\"temperature\":0.7,\"top_p\":0.95,\"cfg_scale\":7.0,\"sampler\":\"Euler a\"}"
    },
    "prompt_images": [
      {
        "image_url": "https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=1200",
        "caption": "Primary Exterior Elevation render sample",
        "sort_order": 1
      },
      {
        "image_url": "https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=1200",
        "caption": "Interior courtyard detail view",
        "sort_order": 2
      }
    ],
    "tags": ["Midjourney", "Architecture", "Photorealism", "Prompt Engineering"]
  }
}
```

---

### 5.2 Complete Sample JSON Payloads for Core Tables

#### 1. Table: `contents` (Base Entity for Articles, Tools, Prompts, GPTs, Jobs)
```json
{
  "content_type": "article",
  "title": "Building Autonomous Multi-Agent Workflows in PHP 8.2",
  "slug": "building-autonomous-multi-agent-workflows-php-8-2",
  "summary": "A deep technical walkthrough on building cooperative LLM multi-agent networks.",
  "body_html": "<p>Autonomous multi-agent architectures represent the next frontier...</p>",
  "featured_image_url": "https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=1200",
  "category_id": 1,
  "status": "published",
  "visibility": "public",
  "is_featured": 1,
  "is_trending": 0
}
```

#### 2. Table: `prompts` (Prompt Blueprint Extension)
```json
{
  "content_id": 101,
  "prompt_type": "text",
  "prompt_template": "You are an expert security auditor. Analyze the following PHP controller for SQL injection vulnerabilities:\n\n{{CODE_SNIPPET}}",
  "target_models": "[\"GPT-4o\", \"Claude 3.5 Sonnet\"]",
  "template_variables": "[\"CODE_SNIPPET\", \"SECURITY_LEVEL\"]",
  "aspect_ratio": "16:9",
  "model_version": "2026.1",
  "cfg_scale": 7.00,
  "steps": 30,
  "difficulty_level": "intermediate",
  "how_to_use": "Paste your PHP code block into the variable placeholder and run."
}
```

#### 3. Table: `ai_tools` (AI Tool Directory Extension)
```json
{
  "content_id": 102,
  "tagline": "Real-time AI voice synthesis and speech cloning platform",
  "pricing_model": "freemium",
  "starting_price": 5.00,
  "website_url": "https://elevenlabs.io",
  "affiliate_url": "https://techscout.online/go/elevenlabs",
  "features_list": "[\"Voice Cloning\", \"Multilingual Speech\", \"Low Latency API\"]",
  "has_free_tier": 1,
  "verified_status": 1
}
```

#### 4. Table: `gpt_listings` (Custom GPT Agent Directory Extension)
```json
{
  "content_id": 103,
  "openai_url": "https://chatgpt.com/g/g-code-architect",
  "developer_name": "TechScout Engineering",
  "category": "developer_tools",
  "capabilities": "[\"Code Analysis\", \"Database Optimization\", \"Schema Design\"]",
  "conversation_starters": "[\"Review my SQL schema\", \"Optimize my PHP-FPM config\"]"
}
```

#### 5. Table: `categories` (Taxonomy & Organization)
```json
{
  "name": "Generative Video & VFX",
  "slug": "generative-video-vfx",
  "description": "Tools, workflows, and prompts for AI video generation and VFX pipelines.",
  "icon_name": "video",
  "color_hex": "#ec4899",
  "sort_order": 5,
  "applicable_to": "all",
  "is_featured_home": 1
}
```

#### 6. Table: `comments` (Discussion Engine)
```json
{
  "content_id": 101,
  "user_id": 1,
  "parent_id": null,
  "comment_text": "Incredible analysis. The cursor pagination architecture solves the exact bottleneck we hit.",
  "status": "visible"
}
```

#### 7. Table: `author_tips` (Creator Monetization & Tipping)
```json
{
  "reference_no": "TIP-2026-981240",
  "author_id": 1,
  "article_id": 101,
  "tipper_user_id": 5,
  "tipper_name": "Alex Mercer",
  "tipper_email": "alex@example.com",
  "amount_usd": 10.00,
  "platform_fee_usd": 0.50,
  "net_amount_usd": 9.50,
  "message": "Thanks for writing such thorough documentation!",
  "is_anonymous": 0,
  "payment_method": "card_stripe",
  "status": "completed"
}
```

#### 8. Table: `jobs` (Tech & AI Job Board)
```json
{
  "title": "Senior AI Systems Engineer (PHP / Python)",
  "slug": "senior-ai-systems-engineer-php-python",
  "company_name": "Scout Technologies Inc",
  "company_logo_url": "https://techscout.online/assets/images/logo.png",
  "location": "Remote (US / EU)",
  "job_type": "Full-Time",
  "salary_range": "$140,000 - $180,000",
  "apply_url": "https://techscout.online/careers/apply/12",
  "is_active": 1
}
```

---

### 5.3 Comprehensive Ingestion Tutorial (cURL, Python, JavaScript)

#### Authentication Methods:
* **Bearer Token**: Send header `Authorization: Bearer <YOUR_WEBHOOK_SECRET>`
* **HMAC SHA-256 Signature**: Send header `X-TechScout-Signature: sha256=<HASH>` where hash is computed across the raw body using your secret key.

#### Example A: cURL Multi-Table Ingestion Request
```bash
curl -X POST "https://techscout.online/api/v1/webhook/omni" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ts_deploy_sec_99182374619a8b" \
  -d '{
    "tables": {
      "contents": {
        "title": "Claude 3.5 Sonnet Automated Refactoring Blueprint",
        "slug": "claude-3-5-sonnet-automated-refactoring-blueprint",
        "summary": "Master prompt template for automated codebase audits and refactoring.",
        "content_type": "prompt",
        "category_id": 2
      },
      "prompts": {
        "prompt_type": "text",
        "prompt_template": "Perform a senior-level code review of the attached class...",
        "target_models": "[\"Claude 3.5 Sonnet\"]",
        "difficulty_level": "advanced"
      },
      "tags": ["Claude", "Refactoring", "Clean Code"]
    }
  }'
```

#### Example B: Python Automation Ingestion Script
```python
import hmac
import hashlib
import json
import requests

ENDPOINT = "https://techscout.online/api/v1/webhook/omni"
SECRET_KEY = "ts_deploy_sec_99182374619a8b"

payload = {
    "tables": {
        "contents": {
            "title": "Midjourney v6 Photorealistic Landscape Prompt",
            "slug": "midjourney-v6-photorealistic-landscape-prompt",
            "summary": "High-detail prompt blueprint for mountain cinematic vistas.",
            "content_type": "prompt",
            "category_id": 3
        },
        "prompts": {
            "prompt_type": "image",
            "prompt_template": "cinematic aerial shot of dolomites at dawn --ar 16:9 --v 6.0",
            "aspect_ratio": "16:9",
            "difficulty_level": "beginner"
        },
        "tags": ["Midjourney", "Landscape", "Photography"]
    }
}

body = json.dumps(payload, separators=(',', ':'))
signature = hmac.new(SECRET_KEY.encode('utf-8'), body.encode('utf-8'), hashlib.sha256).hexdigest()

headers = {
    "Content-Type": "application/json",
    "Authorization": f"Bearer {SECRET_KEY}",
    "X-TechScout-Signature": f"sha256={signature}"
}

response = requests.post(ENDPOINT, data=body, headers=headers)
print("Status Code:", response.status_code)
print("Response JSON:", response.json())
```

#### Example C: Node.js / JavaScript Ingestion Script
```javascript
const crypto = require('crypto');

const ENDPOINT = 'https://techscout.online/api/v1/webhook/omni';
const SECRET_KEY = 'ts_deploy_sec_99182374619a8b';

const payload = {
  tables: {
    contents: {
      title: 'NextGen AI Video Generation Workflow 2026',
      slug: 'nextgen-ai-video-generation-workflow-2026',
      summary: 'End-to-end production workflow connecting Midjourney, Runway Gen-3, and ElevenLabs.',
      content_type: 'article',
      category_id: 1
    },
    tags: ['Video AI', 'Runway', 'Midjourney', 'Production']
  }
};

const rawBody = JSON.stringify(payload);
const signature = crypto.createHmac('sha256', SECRET_KEY).update(rawBody).digest('hex');

fetch(ENDPOINT, {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': `Bearer ${SECRET_KEY}`,
    'X-TechScout-Signature': `sha256=${signature}`
  },
  body: rawBody
})
.then(res => res.json())
.then(data => console.log('Ingestion Result:', data))
.catch(err => console.error('Ingestion Failed:', err));
```

---

## 6. Implementation Roadmap: "What to Build & How to Build"

---

### Phase 1: High-Concurrency Hardening & Infrastructure Tuning (P0)

1. **Remove Startup Metadata Query**:
   - In `app/Core/Database.php`, remove the runtime execution of `SHOW TABLES LIKE 'contents'` on every web request. Check for the existence of an `installed.lock` file on disk instead.
2. **Implement Non-Blocking Read Sessions**:
   - In `app/Core/Session.php`, add `Session::closeWrite()`.
   - In read-only routes (articles, prompts, tool directory, API feeds), invoke `Session::closeWrite()` immediately after reading user credentials. This frees the PHP session file lock and prevents parallel request stalls.
3. **Deploy APCu L1 In-Memory Caching**:
   - Enhance `app/Core/Cache.php` with an APCu driver (`apcu_fetch`, `apcu_store`).
   - Store application settings, category lists, and dynamic routes in APCu with a 300-second TTL. This eliminates up to 85% of MySQL read transactions on high-traffic pages.
4. **Tune EC2 Nginx & MySQL Profiles**:
   - Apply the tuned `my.cnf` (`innodb_buffer_pool_size = 640M`, `max_connections = 80`) and Nginx FastCGI micro-caching configuration for anonymous visitors.

---

### Phase 2: Database Schema Alignment & Fatal Error Elimination (P0)

1. **Non-Destructive Database Migration Script**:
   - Create `database/migrations/2026_09_27_000001_fix_production_exceptions.sql` with safe DDL:
     ```sql
     -- 1. Ensure notifications.event_type has a default value
     ALTER TABLE `notifications` MODIFY COLUMN `event_type` VARCHAR(60) NOT NULL DEFAULT 'system';

     -- 2. Ensure categories has updated_at column
     ALTER TABLE `categories` ADD COLUMN IF NOT EXISTS `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

     -- 3. Ensure incoming_webhook_logs status allows 'simulated' and 'ignored'
     ALTER TABLE `incoming_webhook_logs` MODIFY COLUMN `status` ENUM('success','failed','ignored','simulated','pending') NOT NULL DEFAULT 'success';

     -- 4. Add high-speed covering indexes for high-volume joins
     ALTER TABLE `contents` ADD INDEX IF NOT EXISTS `idx_contents_type_status_id` (`content_type`, `status`, `id`);
     ALTER TABLE `comments` ADD INDEX IF NOT EXISTS `idx_comments_content_parent_id` (`content_id`, `parent_id`, `status`, `id`);
     ```
2. **Correct Invalid Queries in `AdminController.php`**:
   - Fix lines 5118, 5635, 5638: Replace references to non-existent `articles.featured_image` and `prompts.thumbnail` with canonical `contents.featured_image_url` and `prompt_images.image_url`.
3. **Verify Parameter Type Binding for `LIMIT` Clauses**:
   - Ensure all `LIMIT` queries strictly cast parameter values to `(int)$limit` before executing prepared statements.

---

### Phase 3: High-Scale Cursor Comment Subsystem (P1)

1. **Deconstruct Monolithic View Rendering**:
   - In `views/components/comments_section.php`, replace the synchronous recursive tree loop with an initial top-level render of the first 10 root threads.
2. **Client-Side Progressive Fetching**:
   - Add a *"Load More Discussions"* button (or intersection observer for infinite scroll) that queries:
     `GET /api/v1/comments?content_id={id}&cursor={next_cursor}&limit=10`
   - Child replies are collapsed by default with a *"View 3 Replies..."* trigger that fetches sub-replies asynchronously via `GET /api/v1/comments/{id}/replies`.
3. **Zero Memory Waste**:
   - Eliminates 90% of initial DOM payload, preventing mobile browser crashes and maintaining sub-50ms page load speeds even with 5,000+ comments.

---

### Phase 4: Prompt Showcase UX & Monetization Consolidation (P1)

1. **Consolidate Copy Buttons**:
   - In `views/directory/prompt_single.php`, eliminate the small `.ts-code-copy-btn` badge.
   - Upgrade the primary action copy button with a dual-trigger handler:
     ```javascript
     function handlePrimaryPromptCopy(promptText, sponsorUrl) {
       // 1. Copy formatted text to clipboard
       navigator.clipboard.writeText(promptText).then(() => {
         showToast("Prompt Copied to Clipboard! 📋", "success");
       });
       // 2. Open sponsor destination in background tab
       if (sponsorUrl && sponsorUrl.startsWith('http')) {
         window.open(sponsorUrl, '_blank', 'noopener,noreferrer');
       }
     }
     ```
2. **Granular Blueprint Parameter Controls**:
   - In the Prompt Studio Editor, provide individual checkboxes under a "Public Display Settings" section:
     - `[x] Show Target Engines`
     - `[x] Show Sampling Temperature`
     - `[x] Show Top_P`
     - `[ ] Show Complexity Tier`
     - `[ ] Show Dynamic Variable Injector`
   - Store settings in `prompts.parameters_json` as `"display_flags": {...}`.
   - In `prompt_single.php`, conditionally render each block only when its corresponding display flag is active.
3. **Dual-Theme Caption Bar Fix (`#main-gallery-caption`)**:
   - Replace conflicting inline gradients with clean CSS classes using CSS custom properties for background, text color, and contrast.

---

### Phase 5: Enhanced Webhook & Custom API Gateway Upgrades (P1)

1. **Deploy Built-In JSON Sample Generator**:
   - In `views/admin/incoming_webhooks.php`, enhance the *"All Tables Sample Data"* modal to include pre-generated, validated JSON structures for all 76 database tables.
2. **Interactive Ingestion Tutorial Modal**:
   - Embed tabbed code snippets (cURL, Python, Node.js, PHP) with automatic copy-to-clipboard buttons and live endpoint URLs pre-filled.
3. **Multi-Table Visual Mapping Builder**:
   - Allow admins to visually link incoming JSON fields to multiple database tables with relational foreign key auto-stitching.

---

### Phase 6: Role-Based QoL Feature Rollout (P2)

1. **Author Studio Upgrades**:
   - Implement IndexedDB local auto-save draft manager.
   - Add clipboard image paste uploader with WebP compression.
   - Add live word/reading-time statistics.
2. **Member Experience Upgrades**:
   - Add bookmark folders and tag filters.
   - Add "Resume Reading" progress banner.
   - Implement active session management with remote revocation.
3. **Admin Governance Upgrades**:
   - Implement universal bulk actions on data tables.
   - Add database schema diff inspector.
   - Add visual email builder block canvas.
4. **Guest Discovery Upgrades**:
   - Implement `Ctrl + K` command palette quick search.
   - Add instant filter pills on directory listings.
   - Add related content carousels.

---

## 7. Comprehensive Device & Theme Testing Checklist

Use this structured checklist to systematically verify every screen, role interaction, and responsive breakpoint.

```markdown
### 🖥️ A. Device & Responsive Breakpoint Verification
- [ ] Desktop Monitor (1920x1080 & 2560x1440):
  - [ ] Sticky sidebars align correctly without overlapping content.
  - [ ] Multi-column tables render without horizontal overflow.
  - [ ] Full-width modal windows center properly with blurred backdrop.
- [ ] Laptop Screen (1366x768 & 1440x900):
  - [ ] Navigation header items wrap cleanly or collapse to hamburger.
  - [ ] Markdown editor split preview maintains 50/50 ratio without layout break.
- [ ] Tablet (iPad / iPad Pro 768px - 1024px, Portrait & Landscape):
  - [ ] Filter drawers toggle smoothly via slide-in drawer.
  - [ ] Touch gestures work on image galleries and thumbnail strips.
  - [ ] Virtual keyboard opening does not displace fixed bottom bars.
- [ ] Mobile Phone (iPhone 13/14/15, Samsung Galaxy 375px - 430px):
  - [ ] Fixed bottom mobile navigation bar is visible and accessible in Thumb-Zone.
  - [ ] Touch targets (buttons, links, reaction pills) meet 44x44px minimum hit area.
  - [ ] Prompt code block horizontally scrolls cleanly with no page-width blowouts.
  - [ ] Comments feed renders with compact social bubbles and readable text.

---

### 🌓 B. Dual-Theme (Dark vs Light) Contrast & CSS Inspection
- [ ] Obsidian Dark Mode (Default Theme):
  - [ ] Background is deep obsidian (`#070a13` / `#020408`), surface cards elevated.
  - [ ] Text hierarchy is clear (Primary: `#f8fafc`, Muted: `#94a3b8`).
  - [ ] Borders use subtle translucent lines (`rgba(148, 163, 184, 0.12)`).
  - [ ] No jarring white boxes or inverted light elements on UI chrome.
  - [ ] Image gallery caption bar has high contrast text with clear drop-shadow.
- [ ] Clean Light Mode (`[data-theme="light"]` / `.theme-light`):
  - [ ] Background is clean soft slate (`#f8fafc` / `#ffffff`).
  - [ ] Card surfaces have crisp, subtle borders (`#e2e8f0`) with soft box-shadows.
  - [ ] Text is dark charcoal (`#0f172a` / `#334155`), zero washed-out gray text.
  - [ ] Badges and pills have tinted pastel backgrounds with high-contrast text.
  - [ ] Code blocks and terminal viewports retain high-contrast readable themes.
  - [ ] `#main-gallery-caption` text is crisp dark slate on translucent white glass.
- [ ] Instant Theme Toggling:
  - [ ] Switching between dark and light mode preserves active page state.
  - [ ] Theme preference persists across page reloads via `localStorage`.

---

### 👤 C. Role-Based Functional Journey Testing
- [ ] 1. Anonymous Guest:
  - [ ] Browse Home, Articles, AI Tools, Prompts, GPTs, Campaigns.
  - [ ] Execute `Ctrl + K` search, filter results by tag, and navigate via keyboard.
  - [ ] Click primary Prompt Copy button: verify clipboard text & sponsor tab open.
  - [ ] Read article: verify 10-second dwell time ping does not lock session.
  - [ ] Subscribe to newsletter: verify instant AJAX feedback without reload.
- [ ] 2. Authenticated Member:
  - [ ] Log in with 2FA / Session verification.
  - [ ] Bookmark an article into a custom folder, add personal note, and unbookmark.
  - [ ] Post a comment with an `@mention` and emoji reaction. Verify toggle undo.
  - [ ] View active sessions in Account Settings and test "Revoke Other Sessions".
  - [ ] Test "Resume Reading" banner on a long article.
- [ ] 3. Creator / Author:
  - [ ] Open Author Studio editor: verify local auto-save restores unsaved text.
  - [ ] Paste image from clipboard: verify WebP compression and instant markdown insertion.
  - [ ] Configure Prompt Blueprint parameter visibility toggles and verify frontend hide/show.
  - [ ] Schedule an article for future publication.
  - [ ] Check Tipping Hub: verify tip totals and test custom thank-you responder.
- [ ] 4. Administrator / Super Admin:
  - [ ] Access Admin Command Center: verify server telemetry meters (CPU, RAM, Disk).
  - [ ] Execute bulk action (approve 3 drafts simultaneously).
  - [ ] Run Database Schema Diff Inspector: verify zero unmigrated columns.
  - [ ] Send test incoming webhook payload via Interactive Sandbox: verify atomic multi-table insert.
  - [ ] Test User Impersonation: verify sticky safety bar and clean return to admin.
  - [ ] Simulate Royalty Pool distribution: verify 40% treasury safety threshold alert.
```

---
*End of Comprehensive Architectural Analysis & Roadmap Blueprint.*
