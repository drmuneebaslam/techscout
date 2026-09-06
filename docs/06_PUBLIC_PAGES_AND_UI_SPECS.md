# TechScout — Public Website Pages & UI Specifications

> **Tasks Covered**: 61–114 (Phase 3 — Public Website Pages), 781–792 (Phase 31 — Mobile & PWA), 793–803 (Phase 32 — Accessibility & UI Polish)  
> **Design Language**: Glassmorphic dark theme, responsive flex/grid layouts, mobile-first navigation, WCAG 2.1 AA accessible.

---

## 1. Master Public Page Catalog (Tasks 61–114)

All 54 public pages are unified under single-domain routing with clean semantic markup:

| Route Path | Page Title & Purpose | Key UI Components |
|---|---|---|
| `/` (Task 61) | **Homepage** | Hero banner with search, AI Tool radar carousel, Featured Editorial highlights, Trending prompts grid, Newsletter capture. |
| `/blog` (Task 62) | **Blog Homepage** | Magazine grid layout, Category pill filter bar, Editor's Picks sidebar, Infinite/Paginated article cards. |
| `/blog/latest` (Task 63) | **Latest Articles** | Reverse-chronological article stream with real-time "minutes ago" timestamps. |
| `/blog/trending` (Task 64) | **Trending Articles** | Velocity-ranked articles (top reads past 24h & 7d) with fire engagement badges. |
| `/blog/popular` (Task 65) | **Popular Articles** | All-time highest read & highest-reacted technical guides. |
| `/blog/featured` (Task 66) | **Featured Articles** | Editorially curated deep dives with expansive full-width cover banners. |
| `/categories` (Task 67) | **Categories Hub** | Interactive card grid showing all 12 core tech categories with content counts and icons. |
| `/blog/category/{slug}` (Task 68) | **Category Landing** | Category header banner, Subcategory tab bar, Top articles in category, Curated AI tools. |
| `/tags` (Task 70) | **Tags Directory** | Alphabetical and popularity-weighted tag cloud with search filter. |
| `/blog/tag/{slug}` (Task 71) | **Tag Feed** | Stream of all articles, prompts, and tools tagged with target keyword. |
| `/search` (Tasks 72 & 73) | **Global Search Results** | Instant search with multi-tab filtering (All, Articles, AI Tools, Prompts, Authors), highlighting query matches. |
| `/authors` (Task 74) | **Authors Directory** | Creator leaderboard, top verified experts, sortable by reputation score and article count. |
| `/author/{username}` (Tasks 75–77)| **Author Profile** | Creator bio, verification badge, follower count, total reads, social links, authored content tabs. |
| `/tools` (Tasks 78 & 79) | **AI Tools Directory & Single** | Filterable SaaS directory (Pricing, Features, Category) + Deep benchmark breakdown page. |
| `/gpts` (Tasks 80 & 81) | **GPT Directory & Single** | Custom GPT repository, prompt starters, OpenAI launcher, community ratings. |
| `/prompts` (Tasks 82–84) | **Prompts Hub & Single** | Copyable prompt library, dynamic variable injector form, model compatibility matrix. |
| `/campaigns` (Tasks 91 & 92) | **Campaigns & Hackathons** | Active tech challenges, sponsor banners, prize pools, submission portal. |
| `/newsletter` (Task 93) | **Weekly Digest Landing** | Email subscription form, sample newsletter preview, topic customization checkboxes. |
| `/about`, `/contact`, `/careers` (Tasks 94–96) | **Company Pages** | Mission statement, team credits, inquiry contact form, open engineering roles. |
| `/editorial-policy`, `/author-guidelines` (Tasks 97–99)| **Creator Guidelines** | Quality standards, non-cash reward explanation, monetization rules, plagiarism policy. |
| `/privacy`, `/terms`, `/cookies`, `/dmca` (Tasks 103–107)| **Legal & Compliance** | GDPR/CCPA compliant privacy terms, cookie consent policy, DMCA takedown notice form. |
| `/404`, `/403`, `/500`, `/maintenance` (Tasks 110–114) | **Error & State Pages** | Branded high-tech error pages with search bar and quick recovery navigation. |

---

## 2. Core UI Layout & Wireframe Architecture

```
+----------------------------------------------------------------------------------------------------+
| [HEADER]: Logo [TechScout] | Nav: Articles, Tools, Prompts, Campaigns | Search [Ctrl+K] | [Sign In]|
+----------------------------------------------------------------------------------------------------+
|                                                                                                    |
|  [HERO / PAGE TITLE BAR]                                                                           |
|  Dynamic Breadcrumbs: Home > AI & Machine Learning > RAG Architecture                              |
|  Headline: "Building Production-Grade RAG Pipelines with PHP 8.2 and Vector DBs"                    |
|  Author: Alex Vance [Verified] | Date: Oct 24, 2026 | Read Time: 8 min | 65 Claps | 12 Comments    |
|                                                                                                    |
+------------------------------------+---------------------------------------------------------------+
|  [LEFT SIDEBAR (STICKY - 260px)]  |  [MAIN EDITORIAL CONTENT AREA (780px)]                        |
|                                    |                                                               |
|  - Table of Contents               |  Lead summary paragraph with highlighted key takeaways.       |
|    1. Introduction to RAG          |                                                               |
|    2. Embedding Generation         |  [Interactive Code Runner / Copy Block]                       |
|    3. Vector Indexing in MySQL     |  ```php                                                       |
|    4. Query Optimization           |  $vector = $aiService->generateEmbedding($userPrompt);        |
|    5. Conclusion                   |  ```                                                          |
|                                    |                                                               |
|  - Author Quick Card               |  High-resolution architectural diagram with zoom modal.       |
|  - Social Share Buttons (X, LI)    |                                                               |
|  - 6-Reaction Quick Bar            |  [Inline Callout Note: Best Practices for Chunking]          |
|                                    |                                                               |
|                                    |  -----------------------------------------------------------  |
|                                    |  [6-Reaction Interactive Bar]: [🔥 34] [🚀 18] [💡 27] [❤️ 12]  |
|                                    |  -----------------------------------------------------------  |
|                                    |  [2-Level Nested Comment Section]                             |
|                                    |  - Comment Form (Markdown supported)                          |
|                                    |  - Pinned Comment by Author                                   |
|                                    |  - Threaded Replies                                           |
+------------------------------------+---------------------------------------------------------------+
| [FOOTER]: Brand Mission | Quick Discovery Links | Creator Portal | Legal & Privacy Policy           |
+----------------------------------------------------------------------------------------------------+
```

---

## 3. Progressive Web App (PWA) & Mobile System (Tasks 781–792)

### Task 789: PWA Manifest (`manifest.json`)
```json
{
  "name": "TechScout — Tech Intelligence & AI Directory",
  "short_name": "TechScout",
  "description": "The frontier of technology, AI tools, and coding prompts scouted for builders.",
  "start_url": "/",
  "display": "standalone",
  "background_color": "#07090e",
  "theme_color": "#6366f1",
  "icons": [
    { "src": "/assets/icons/icon-192.png", "sizes": "192x192", "type": "image/png" },
    { "src": "/assets/icons/icon-512.png", "sizes": "512x512", "type": "image/png" }
  ]
}
```

### Tasks 790 & 791: Service Worker & Offline Fallback (`sw.js`)
- Caches critical CSS design tokens, fonts, and core JavaScript libraries.
- Implements **Stale-While-Revalidate** strategy for public article pages and an offline branded fallback page when internet connectivity is lost.

---

## 4. Accessibility & UI States (Tasks 793–803 & 921–930)

### Accessibility Standards (Tasks 793–803)
1. **WCAG 2.1 AA Compliance**: All text elements maintain a minimum contrast ratio of `4.5:1` against background surfaces.
2. **Keyboard Navigation**: Complete tab-order coverage with high-visibility neon outline focus rings (`outline: 2px solid var(--ts-accent); outline-offset: 2px;`).
3. **Screen Reader Optimization**: Semantic HTML tags (`<main>`, `<article>`, `<aside>`, `<nav>`) and descriptive `aria-label` attributes on all icon buttons.
4. **Reduced Motion Support**:
   ```css
   @media (prefers-reduced-motion: reduce) {
     *, *::before, *::after {
       animation-duration: 0.01ms !important;
       transition-duration: 0.01ms !important;
     }
   }
   ```

### UI Micro-States (Tasks 921–930)
- **Empty States (Task 921)**: Branded illustrations with clear calls-to-action (e.g., *"No bookmarks saved yet. Explore trending articles to build your library"*).
- **Skeleton Loaders (Task 923)**: Animated shimmering gradient placeholders during fetch cycles.
- **Toast Notifications (Task 926)**: Dispatched via Toastify for copy confirmations, bookmark saves, and submission notices.
- **Confirmation Modals (Task 927)**: Styled SweetAlert2 modals for destructive actions (e.g., deleting drafts).
