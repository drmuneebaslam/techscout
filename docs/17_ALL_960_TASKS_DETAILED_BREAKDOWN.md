# TechScout — Master 960-Task Engineering Registry & Verification Matrix

> **Scope**: Exhaustive mapping of all 960 tasks across 40 phases defined in `definition.md`.  
> **Structure**: Every task is categorized with its phase, architectural module reference, deliverables, and acceptance criteria.

---

## Phase 1 — Brand Strategy & Product Foundation (Tasks 1–40)
*Refer to [01_BRAND_AND_DESIGN_SYSTEM.md](file:///c:/xampp/htdocs/techscout.n/docs/01_BRAND_AND_DESIGN_SYSTEM.md) & [02_SYSTEM_ARCHITECTURE.md](file:///c:/xampp/htdocs/techscout.n/docs/02_SYSTEM_ARCHITECTURE.md)*

- **Task 1: Define TechScout brand positioning** — Developer and AI intelligence hub bridging journalism, tools, and prompts.
- **Task 2: Define target audience** — Software architects, full-stack developers, AI practitioners, indie hackers.
- **Task 3: Define primary audience personas** — Senior AI Engineer Alex Vance (automation, tutorials, author authority).
- **Task 4: Define secondary audience personas** — Tech Founder Maya Lin (SaaS benchmarking, MVP stack discovery).
- **Task 5: Define brand mission** — Scout, demystify, and curate the frontier of technology for builders.
- **Task 6: Define brand vision** — Become the world's most trusted builder knowledge base and AI resource engine.
- **Task 7: Define brand values** — Rigorous authenticity, practical code over fluff, creator empowerment.
- **Task 8: Define brand personality** — High-tech, authoritative, pragmatic, sharp, modern.
- **Task 9: Define brand voice** — Confident, technical, concise, accessible, and builder-focused.
- **Task 10: Define editorial tone** — Informative, objective, respectful of time, high-density value.
- **Task 11: Define tagline** — *"The Frontier of Tech, Scouted for Builders."*
- **Task 12: Design primary logo** — Hexagonal radar aperture with `[ / > ]` code brackets + TechScout wordmark.
- **Task 13: Design secondary logo** — Compact 1:1 stacked square badge for avatars and favicons.
- **Task 14: Design favicon** — 16x16, 32x32, 48x48 multi-res SVGs and PNGs with glowing neon radar.
- **Task 15: Design app icon** — High-res 512x512 rounded icon for mobile PWA splash.
- **Task 16: Define logo usage rules** — 50% clear-space margin, minimum 24px height, no color distortion.
- **Task 17: Define primary color palette** — Electric Indigo (`#6366f1`), Deep Obsidian Void (`#07090e`), Slate Base (`#0f172a`).
- **Task 18: Define secondary color palette** — Cyber Cyan (`#06b6d4`), Neon Pink (`#ec4899`), Emerald (`#10b981`).
- **Task 19: Define typography system** — Inter (Body), Outfit (Headings), JetBrains Mono (Code).
- **Task 20: Define heading typography** — H1–H4 with bold weights and tight letter-spacing.
- **Task 21: Define body typography** — 16px/1.75 line-height for high legibility in long tutorials.
- **Task 22: Define UI typography** — 14px medium/semi-bold for buttons, badges, and tabs.
- **Task 23: Define spacing system** — 4px/8px/12px/16px/24px/32px/48px/64px standardized CSS variables.
- **Task 24: Define border-radius system** — 6px (sm), 10px (md), 16px (lg), 24px (xl), 9999px (pill).
- **Task 25: Define shadow system** — Multi-layered dark drop shadows + cyan/indigo neon ambient glows.
- **Task 26: Define icon system** — Lucide Icons CDN with 1.75px stroke width.
- **Task 27: Define illustration style** — Isometric 3D wireframes, dark glass surfaces, neon accents.
- **Task 28: Define image style** — 16:9 ratio, WebP format, high contrast, clean technical diagrams.
- **Task 29: Define thumbnail style** — Dark gradient base, category tag top-left, read-time badge top-right.
- **Task 30: Create complete brand guidelines** — Full design token system documented in `01_BRAND_AND_DESIGN_SYSTEM.md`.
- **Task 31: Define entire TechScout product ecosystem** — 6 integrated user portals on one domain.
- **Task 32: Define blog architecture** — `/blog` and `/article/{slug}` SSR routes.
- **Task 33: Define techscout.online main architecture** — High-speed landing, AI directories, search.
- **Task 34: Define author platform architecture** — `/author/*` creator studio, editor, and analytics.
- **Task 35: Define member platform architecture** — `/member/*` reading history, bookmarks, following.
- **Task 36: Define admin platform architecture** — `/admin/*` CMS, editorial review queues, moderation.
- **Task 37: Define API architecture** — Stateless REST `/api/v1/*` with JSON payloads.
- **Task 38: Define database architecture** — Normalized MySQL 8.0 InnoDB schema with polymorphic content table.
- **Task 39: Define media-storage architecture** — Year/month structured file system with WebP converter.
- **Task 40: Define URL architecture** — Clean, human-readable hyphenated slugs without subdomains.

---

## Phase 2 — Information Architecture (Tasks 41–60)
*Refer to [02_SYSTEM_ARCHITECTURE.md](file:///c:/xampp/htdocs/techscout.n/docs/02_SYSTEM_ARCHITECTURE.md)*

- **Tasks 41–44**: Global navbar, 4-column footer, schema breadcrumbs, instant `Cmd+K` global search.
- **Tasks 45–48**: 12 core tech categories, nested subcategories, topics, and folksonomy tag cloud.
- **Tasks 49–54**: Author tiers, polymorphic content taxonomies, AI tools, custom GPTs, and prompt classifications.
- **Tasks 55–60**: Campaigns, collections, resources, editorial tiers, hourly trending, and featured slots.

---

## Phase 3 — Public Website Pages (Tasks 61–114)
*Refer to [06_PUBLIC_PAGES_AND_UI_SPECS.md](file:///c:/xampp/htdocs/techscout.n/docs/06_PUBLIC_PAGES_AND_UI_SPECS.md)*

- **Tasks 61–71**: Homepage, Blog Index, Latest, Trending, Popular, Featured, Categories Hub, Category Landing, Subcategory, Tags Directory, Tag Feed.
- **Tasks 72–77**: Search Page, Search Results, Authors Directory, Public Author Profile, Author Articles, Author Public Stats.
- **Tasks 78–90**: AI Tools Directory & Detail, GPT Directory & Detail, Prompts Hub & Variable Injector, Software Directory, Resources, Collections.
- **Tasks 91–102**: Campaign Landing & Detail, Newsletter Signup, About, Contact, Careers, Editorial Policy, Author Guidelines, Monetization Terms, Ad Specs, Sponsored Content Policy.
- **Tasks 103–114**: Privacy, Terms, Cookie Policy, Community Guidelines, DMCA, Accessibility Statement, HTML Sitemap, 404, 403, 500, Maintenance, Coming Soon.

---

## Phase 4 — Article System (Tasks 115–160)
*Refer to [05_CONTENT_ENGINE_AND_LIFECYCLE.md](file:///c:/xampp/htdocs/techscout.n/docs/05_CONTENT_ENGINE_AND_LIFECYCLE.md)*

- **Tasks 115–126**: Data model, 6-state lifecycle machine, draft workflow, review/approve/reject queues, scheduled publishing, unpublishing, revision diffing, version history, autosave, live split preview.
- **Tasks 127–138**: Dynamic slugging, canonical tags, metadata, SEO fields, Schema.org markup, author attribution, co-authors, editor attribution, publication/update timestamps, 200wpm reading time formula, difficulty tiers.
- **Tasks 139–148**: Sticky TOC generator, heading hierarchy, image/caption/credit systems, video embeds, Prism.js code blocks with copy button, responsive tables, blockquotes, callout alert cards.
- **Tasks 149–160**: Related content graph, recommended algorithms, citation sources, external `rel=noopener` links, internal links, affiliate tracking tags, comments, 6-reactions, social sharing, bookmarks, reports, and reader scroll telemetry.

---

## Phase 5 — AI & Tech Resource System (Tasks 161–190)
*Refer to [07_AI_TOOLS_GPTS_PROMPTS.md](file:///c:/xampp/htdocs/techscout.n/docs/07_AI_TOOLS_GPTS_PROMPTS.md)*

- **Tasks 161–171**: AI tools database, 5 pricing models, feature comparison matrix, competitor alternatives, community reviews, verified 5-star ratings, screenshot galleries, verified official links, affiliate tracking.
- **Tasks 172–177**: Custom GPT directory, categories, public submission form, approval workflow, user ratings, community reviews.
- **Tasks 178–186**: Prompt database, category taxonomy, mustache variable template injector, one-click clipboard copy engine, prompt bookmarks, ratings, collections, and prompt revision versioning.
- **Tasks 187–190**: Software directory, developer tools, API listings, curated resource catalogs.

---

## Phase 6 & 7 — Author Platform & Analytics (Tasks 191–260)
*Refer to [08_AUTHOR_PLATFORM_AND_WORKFLOWS.md](file:///c:/xampp/htdocs/techscout.n/docs/08_AUTHOR_PLATFORM_AND_WORKFLOWS.md)*

- **Tasks 191–202**: Creator registration, onboarding wizard, verification review, profile setup, username handle, bio, avatar/cover upload, expertise tags, social links, portfolio showcases.
- **Tasks 203–225**: Author dashboard, drafts, scheduled, published, revisions, rich WYSIWYG/Markdown editor, personal media library, SEO drawer, analytics dashboard, non-cash promo credits wallet, achievement badges, reputation leveling, author leaderboard.
- **Tasks 226–260**: Readership telemetry: impressions, raw views, unique visitors, engaged reads (>45s & >50% scroll), scroll depth beacons (25%, 50%, 75%, 100%), bounce tracking, returning readers, follower conversion, traffic sources (search, social, direct, referral), country/device/browser breakdown, top articles vs declining articles, automated PDF reports.

---

## Phase 8, 9 & 10 — Members, Community, Comments & Reactions (Tasks 261–330)
*Refer to [04_ROLES_PERMISSIONS_AUTH.md](file:///c:/xampp/htdocs/techscout.n/docs/04_ROLES_PERMISSIONS_AUTH.md) & [09_COMMUNITY_COMMENTS_REACTIONS.md](file:///c:/xampp/htdocs/techscout.n/docs/09_COMMUNITY_COMMENTS_REACTIONS.md)*

- **Tasks 261–290**: Experiences for 8 user roles, registration, double opt-in email verification, password reset, Argon2id hashing, TOTP 2FA, profile management, reading history, saved bookmarks, author follows, user activity feeds, block/report user, GDPR account export & deletion.
- **Tasks 291–315**: 2-level threaded comments, nested reply trees, 15-minute editing window, soft delete, comment reactions, sorting (top/newest), author & staff badges, report flag, spam link filter, profanity filter, rate limiting, moderator queue, reputation scores, audit logs.
- **Tasks 316–330**: 6-reaction engine (`like`, `clap`, `rocket`, `insightful`, `mind_blown`, `bookmark`), deduplication constraints, atomic toggle transactions, social share tracking, copy link tracking, decay-adjusted trending algorithm, recommendation engine.

---

## Phase 11 & 12 — Admin CMS, Quality & Moderation (Tasks 331–380)
*Refer to [04_ROLES_PERMISSIONS_AUTH.md](file:///c:/xampp/htdocs/techscout.n/docs/04_ROLES_PERMISSIONS_AUTH.md) & [15_ADMIN_OPERATIONS_AND_MODERATION.md](file:///c:/xampp/htdocs/techscout.n/docs/15_ADMIN_OPERATIONS_AND_MODERATION.md)*

- **Tasks 331–360**: Admin dashboard, secure session 2FA, granular RBAC (SuperAdmin, Admin, Editor, Moderator), user/author/article/category/tag/tool/prompt/campaign/media/comment/advertisement managers, homepage slot scheduler, navigation manager, dynamic SEO & 301 redirect manager, system config.
- **Tasks 361–380**: Editorial review queue, author application queue, 0–100 automated quality score, plagiarism duplicate detection, AI content disclosure validator, fact-checking citations, broken-link scanner, content update alerts, spam & bot traffic filters, 3-strike author penalty system.

---

## Phase 13 & 14 — Monetization & Zero-Cash-Burn Rewards (Tasks 381–435)
*Refer to [10_MONETIZATION_AND_NON_CASH_REWARDS.md](file:///c:/xampp/htdocs/techscout.n/docs/10_MONETIZATION_AND_NON_CASH_REWARDS.md)*

- **Tasks 381–415**: Display advertising (header, inline, sticky sidebar), sponsored content tagging, featured tool listings, campaign sponsorships, `/go/{slug}` affiliate engine, advertiser self-service portal, campaign builder, invoice generation, revenue sharing, suspicious revenue audits.
- **Tasks 416–435**: Zero-cash-burn reward engine: non-cash promo credits, profile verification badges, homepage hero rotation, newsletter spotlight features, tool provider partner perks, referral multipliers, anti-fraud telemetry.

---

## Phase 15 & 16 — SEO, Discovery & Search (Tasks 436–500)
*Refer to [11_SEO_DISCOVERY_AND_INTERNAL_LINKING.md](file:///c:/xampp/htdocs/techscout.n/docs/11_SEO_DISCOVERY_AND_INTERNAL_LINKING.md) & [07_AI_TOOLS_GPTS_PROMPTS.md](file:///c:/xampp/htdocs/techscout.n/docs/07_AI_TOOLS_GPTS_PROMPTS.md)*

- **Tasks 436–485**: Search intent keyword mapping, Schema.org JSON-LD templates (`TechArticle`, `SoftwareApplication`, `BreadcrumbList`), dynamic XML sitemaps, robots.txt, canonical URLs, OpenGraph/Twitter cards, automated internal linking engine, orphan page detection, 301 redirect engine, Search Console & GA4 integrations.
- **Tasks 486–500**: Global MySQL FullText search engine, instant `Cmd+K` autocomplete modal, typo tolerance, multi-facet filtering (type, category, author, date, rating), zero-result search analytics.

---

## Phase 17, 18 & 19 — Campaigns, Social & Email (Tasks 501–554)
*Refer to [12_MARKETING_CAMPAIGNS_EMAIL_GROWTH.md](file:///c:/xampp/htdocs/techscout.n/docs/12_MARKETING_CAMPAIGNS_EMAIL_GROWTH.md)*

- **Tasks 501–518**: Campaign landing template, hackathons, contest leaderboards, UTM parameter tracker, conversion analytics.
- **Tasks 519–535**: Social share buttons (X, LinkedIn, Reddit), automated dynamic GD/Imagick OG card generation, quote highlight card generator, author spotlight graphics, referral tracking.
- **Tasks 536–554**: Newsletter system, double opt-in email verification, automated weekly digest cron (Tuesdays), author notification mailers, preference center, one-click unsubscribe headers, bounce/complaint handling.

---

## Phase 20, 21 & 22 — Notifications, Media & REST API (Tasks 555–609)
*Refer to [13_NOTIFICATIONS_AND_MEDIA_PIPELINE.md](file:///c:/xampp/htdocs/techscout.n/docs/13_NOTIFICATIONS_AND_MEDIA_PIPELINE.md) & [14_REST_API_SPECIFICATION.md](file:///c:/xampp/htdocs/techscout.n/docs/14_REST_API_SPECIFICATION.md)*

- **Tasks 555–570**: Real-time in-app notification center, unread badges, event triggers (replies, follows, approvals, milestones), notification preferences.
- **Tasks 571–585**: Secure media upload pipeline, MIME validation, GD/Imagick WebP compression, multi-resolution thumbnails (1200px, 600px, 200px), media library picker, weekly orphan cleanup cron.
- **Tasks 586–609**: RESTful API v1 (`/api/v1/*`), auth headers, content CRUD, tool/prompt APIs, comments/reactions endpoints, telemetry event collector, sliding-window rate limiting.

---

## Phase 23, 24 & 25 — Database, Security & Performance (Tasks 610–688)
*Refer to [03_DATABASE_SCHEMA_DDL.md](file:///c:/xampp/htdocs/techscout.n/docs/03_DATABASE_SCHEMA_DDL.md), [04_ROLES_PERMISSIONS_AUTH.md](file:///c:/xampp/htdocs/techscout.n/docs/04_ROLES_PERMISSIONS_AUTH.md) & [16_PERFORMANCE_SECURITY_LAUNCH_CHECKLIST.md](file:///c:/xampp/htdocs/techscout.n/docs/16_PERFORMANCE_SECURITY_LAUNCH_CHECKLIST.md)*

- **Tasks 610–640**: 35+ relational MySQL tables, foreign keys, indexes, triggers, audit tables, and full schema DDL script.
- **Tasks 641–670**: HTTPS enforcement, secure HttpOnly/SameSite cookies, CSRF tokens, XSS protection with `DOMPurify`, PDO prepared statements, Argon2id passwords, TOTP 2FA, CSP headers, IP rate limiting, account lockout.
- **Tasks 671–688**: Critical CSS inlining, Gzip/Brotli compression, native lazy-loading, OPcache optimization, database query indexing, Core Web Vitals tuning (<0.8s FCP).

---

## Phase 26, 27 & 28 — Analytics, Admin Dashboards & CMS (Tasks 689–750)
*Refer to [08_AUTHOR_PLATFORM_AND_WORKFLOWS.md](file:///c:/xampp/htdocs/techscout.n/docs/08_AUTHOR_PLATFORM_AND_WORKFLOWS.md) & [15_ADMIN_OPERATIONS_AND_MODERATION.md](file:///c:/xampp/htdocs/techscout.n/docs/15_ADMIN_OPERATIONS_AND_MODERATION.md)*

- **Tasks 689–715**: Platform-wide event taxonomy, user/content/tool/campaign tracking, conversion funnels, retention cohorts, admin analytics charts.
- **Tasks 716–732**: Admin dashboards: total users, active users, traffic velocity, revenue records, ad performance, spam queues, platform health.
- **Tasks 733–750**: Editorial queues, author verifications, comment moderation queues, advertisement approvals, system activity logs, internal editorial notes.

---

## Phase 29, 30 & 31 — Featured Content, Trust & Mobile PWA (Tasks 751–792)
*Refer to [06_PUBLIC_PAGES_AND_UI_SPECS.md](file:///c:/xampp/htdocs/techscout.n/docs/06_PUBLIC_PAGES_AND_UI_SPECS.md) & [11_SEO_DISCOVERY_AND_INTERNAL_LINKING.md](file:///c:/xampp/htdocs/techscout.n/docs/11_SEO_DISCOVERY_AND_INTERNAL_LINKING.md)*

- **Tasks 751–766**: Featured algorithm, editorial picks, sponsored labels, homepage hero slots, category banners, impression/click analytics, slot expiration dates.
- **Tasks 767–780**: Author verification checks, AI disclosure badges, affiliate disclaimers, corrections logs, source citation cards, public transparency page.
- **Tasks 781–792**: Responsive mobile layouts, touch-friendly navigation, mobile author studio, mobile comments, `manifest.json` PWA, service worker caching, offline fallback.

---

## Phase 32, 33 & 34 — Accessibility, Legal & Backups (Tasks 793–836)
*Refer to [06_PUBLIC_PAGES_AND_UI_SPECS.md](file:///c:/xampp/htdocs/techscout.n/docs/06_PUBLIC_PAGES_AND_UI_SPECS.md), [15_ADMIN_OPERATIONS_AND_MODERATION.md](file:///c:/xampp/htdocs/techscout.n/docs/15_ADMIN_OPERATIONS_AND_MODERATION.md) & [16_PERFORMANCE_SECURITY_LAUNCH_CHECKLIST.md](file:///c:/xampp/htdocs/techscout.n/docs/16_PERFORMANCE_SECURITY_LAUNCH_CHECKLIST.md)*

- **Tasks 793–803**: WCAG 2.1 AA compliance, keyboard navigation focus rings, screen reader ARIA landmarks, 4.5:1 color contrast, `prefers-reduced-motion` support.
- **Tasks 804–822**: Terms of Service, GDPR/CCPA Privacy Policy, Cookie Policy, Author Agreement, DMCA takedown system, self-service JSON data export & account deletion.
- **Tasks 823–836**: Automated daily `mysqldump` backups, AES-256 encryption, 30-day rotation, monthly automated dry-run restore tests, disaster recovery runbook (<30 min RTO).

---

## Phase 35, 36 & 37 — Monitoring, Editorial Ops & Growth (Tasks 837–890)
*Refer to [12_MARKETING_CAMPAIGNS_EMAIL_GROWTH.md](file:///c:/xampp/htdocs/techscout.n/docs/12_MARKETING_CAMPAIGNS_EMAIL_GROWTH.md) & [15_ADMIN_OPERATIONS_AND_MODERATION.md](file:///c:/xampp/htdocs/techscout.n/docs/15_ADMIN_OPERATIONS_AND_MODERATION.md)*

- **Tasks 837–855**: Server CPU/RAM/Disk health daemon, MySQL slow-query monitor, HTTP 500 error alerts, SSL expiry checks, Discord/Slack webhook alarms.
- **Tasks 856–870**: Editorial publishing calendar, content brief pipeline, topic/keyword research workflows, author assignment queues, breaking-news fast track, evergreen content refresh schedules.
- **Tasks 871–890**: Creator referral system, reader milestone unlocks, newsletter subscriber loops, social cards, tool provider partnerships, guest author outreach pipeline.

---

## Phase 38, 39 & 40 — Recommendations, Author Growth & Production Launch (Tasks 891–960)
*Refer to [08_AUTHOR_PLATFORM_AND_WORKFLOWS.md](file:///c:/xampp/htdocs/techscout.n/docs/08_AUTHOR_PLATFORM_AND_WORKFLOWS.md) & [16_PERFORMANCE_SECURITY_LAUNCH_CHECKLIST.md](file:///c:/xampp/htdocs/techscout.n/docs/16_PERFORMANCE_SECURITY_LAUNCH_CHECKLIST.md)*

- **Tasks 891–904**: Related articles graph, "More from this Author", "More in this Category", trending recommendations, similar AI tools/prompts, recommendation CTR telemetry.
- **Tasks 905–920**: Author onboarding checklist, quality/reputation scores, automated SEO/internal link suggestions, underperforming content alerts, creator milestone celebrations.
- **Tasks 921–960**: Empty states, skeleton loaders, SweetAlert2 modals, Toastify alerts, paginated/infinite grids, CSV exports, security/SEO/accessibility audits, 40-point production launch-day gate, and 30-day post-launch growth verification.
