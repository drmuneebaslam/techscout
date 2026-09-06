# TechScout — System Architecture & Unified Content Engine

> **Tasks Covered**: 31 through 60 (Phase 1 — Product Architecture & Phase 2 — Information Architecture)  
> **Architecture Style**: Single-Domain Monolith with RESTful API Micro-Modules (PHP 8.2+, MySQL 8.0, Native JS)  
> **Key Innovation**: Unified Polymorphic Content Engine powering 8 entity types.

---

## 1. Unified Single-Domain Product Architecture (Tasks 31–40)

### Task 31: TechScout Product Ecosystem
The TechScout ecosystem unifies 6 core user experiences inside a single web domain (`techscout.online` or `http://localhost/techscout.n`):

```
+----------------------------------------------------------------------------------------------------+
|                                    TECHSCOUT UNIFIED PLATFORM                                      |
+----------------------------------------------------------------------------------------------------+
|                                                                                                    |
|  [Public Discovery]        [Blog & Editorial]         [AI Tools & Directory]    [Prompt Engine]   |
|  / (Homepage)              /blog                      /tools                   /prompts           |
|  /about, /contact          /blog/latest               /tools/{slug}            /prompts/{slug}    |
|  /search                   /blog/trending             /gpts                    /prompts/submit    |
|  /campaigns                /article/{slug}            /gpts/{slug}             /campaigns/{slug}  |
|                                                                                                    |
|  [Author & Creator Studio]                            [Admin & CMS Control Panel]                  |
|  /author/dashboard                                    /admin/dashboard                             |
|  /author/editor                                       /admin/content & /admin/moderation           |
|  /author/articles                                     /admin/users & /admin/authors                |
|  /author/media                                        /admin/monetization & /admin/campaigns       |
|  /author/analytics                                    /admin/seo & /admin/system                   |
|                                                                                                    |
|  [Member & Reader Portal]                             [RESTful API Engine]                         |
|  /member/profile, /member/bookmarks                   /api/v1/content, /api/v1/search              |
|  /member/history, /member/following                   /api/v1/author, /api/v1/admin                |
+----------------------------------------------------------------------------------------------------+
```

### Tasks 32–36: Subsystem Architecture without Subdomains
To satisfy the strict **no-subdomain single-domain** constraint, each subsystem is mapped to distinct path prefixes with dedicated role-based middleware:

1. **Blog & Editorial System (`/blog` and `/article/{slug}`)** (Task 32):
   - Fast server-side rendered (SSR) article views with inline JSON-LD schema, dynamic Table of Contents, interactive code runner/copy blocks, and dynamic nested comments.
2. **Main Discovery Hub (`/`, `/tools`, `/gpts`, `/prompts`, `/campaigns`)** (Task 33):
   - Searchable, filterable interactive directories powered by client-side caching and fast JSON endpoints.
3. **Author Studio (`/author/*`)** (Task 34):
   - Authenticated portal for registered creators. Includes WYSIWYG/Markdown editor with auto-save, media asset uploader, submission tracker, real-time analytics, and non-cash badge rewards.
4. **Member Experience (`/member/*`)** (Task 35):
   - Personal reader dashboard with saved bookmarks, reading history progress, following feed, and notification center.
5. **Admin Control Center (`/admin/*`)** (Task 36):
   - Super-admin and editorial dashboard with review queues, user permission manager, spam detection, advertisement scheduler, and system health monitors.

### Tasks 37–40: API, Database, Media & URL Architecture
- **API Architecture (Task 37)**: Stateless REST API under `/api/v1/*` using JSON payloads, HTTP status codes, Bearer JWT / Secure HttpOnly Session Cookies, and sliding-window rate limiting.
- **Database Architecture (Task 38)**: MySQL 8.0 with InnoDB, foreign key constraints, composite indexes on `(status, published_at)`, and FullText indexes for search.
- **Media-Storage Architecture (Task 39)**: Structured file system storage (`/uploads/articles/{year}/{month}/`, `/uploads/avatars/`, `/uploads/tools/`) with auto-conversion to WebP and thumbnail generation.
- **URL Architecture (Task 40)**: Clean, semantic, human-readable slugs with lowercase hyphenation (e.g., `/article/building-production-rag-pipelines-with-php-mysql`).

---

## 2. The Unified Content Engine Model

Instead of building 8 disparate database tables and admin handlers for articles, AI tools, GPTs, prompts, software, resources, collections, and campaigns, TechScout implements a **Polymorphic Content Engine**.

```
                           +------------------------+
                           |     contents (Base)    |
                           +------------------------+
                           | id, uuid, title, slug  |
                           | content_type (ENUM)    |
                           | author_id, category_id |
                           | summary, body_html     |
                           | status, published_at   |
                           | views, unique_views    |
                           | reactions, comments    |
                           | bookmarks, shares      |
                           | seo_title, seo_desc    |
                           +-----------+------------+
                                       |
       +-------------------------------+-------------------------------+
       |                               |                               |
+------v-------+               +-------v------+                +-------v------+
|   articles   |               |   ai_tools   |                |   prompts    |
+--------------+               +--------------+                +--------------+
| reading_time |               | pricing_type |                | prompt_text  |
| difficulty   |               | free_tier_ok |                | variables_js |
| toc_json     |               | website_url  |                | target_model |
| co_authors   |               | rating_avg   |                | copy_count   |
+--------------+               +--------------+                +--------------+
```

### Supported Content Types:
1. `article`: Deep-dive technical articles, tutorials, case studies.
2. `ai_tool`: AI SaaS, models, developer APIs, desktop & cloud applications.
3. `gpt_listing`: Custom OpenAI GPTs, Claude Projects, HuggingFace spaces.
4. `prompt`: Reusable system & user prompt templates with dynamic variables.
5. `software`: Open-source libraries, dev tools, CLI utilities.
6. `resource`: Cheat sheets, whitepapers, datasets, curated repos.
7. `collection`: Groupings of articles, tools, or prompts curated by editors/authors.
8. `campaign`: Sponsoring, developer challenges, community hackathons.

---

## 3. Information Architecture & Taxonomies (Tasks 41–60)

### Task 41: Global Navigation Architecture
- **Top Header Bar**:
  - *Left*: TechScout Logo (Icon + Wordmark).
  - *Center*: Primary Navigation Links:
    - **Articles** (`/blog`) — Dropdown: *Latest, Trending, AI & ML, Web Dev, Cloud & DevOps, Architecture*.
    - **AI Directory** (`/tools`) — Dropdown: *Top AI Tools, Custom GPTs, Dev Tools, Benchmarks*.
    - **Prompts** (`/prompts`) — Dropdown: *Coding Prompts, System Prompts, Marketing, Curated Bundles*.
    - **Campaigns** (`/campaigns`).
  - *Right*: Instant Search Trigger (`Cmd + K`), Dark/Light Mode Toggle, Author/Member Action button (`Write` / `Dashboard` or `Sign In`).

### Task 42: Footer Architecture
- **Column 1 (Brand)**: TechScout logo, mission statement, social links (X, GitHub, LinkedIn, Discord), copyright.
- **Column 2 (Discovery)**: AI Tools Directory, Prompt Library, Custom GPTs, Trending Articles, Newsletter Digest.
- **Column 3 (Creators)**: Author Platform, Submission Guidelines, Creator Rewards, Monetization Policy, Leaderboard.
- **Column 4 (Company & Legal)**: About Us, Editorial Policy, Privacy Policy, Terms of Service, DMCA, Cookie Settings, Contact.

### Task 43: Breadcrumb System
Dynamic semantic schema breadcrumbs rendered on every child page:
- Home > Blog > Artificial Intelligence > Building Autonomous Multi-Agent Workflows
- Home > AI Tools > Developer Tools > Cursor AI

### Tasks 44–60: Global Taxonomies
1. **Primary Categories (Task 45)**:
   - `ai-machine-learning`: LLMs, Neural Networks, PyTorch, RAG, Computer Vision.
   - `web-engineering`: Frontend Architecture, PHP/Backend, Modern JS, APIs.
   - `cloud-devops`: Docker, Kubernetes, CI/CD, Serverless, AWS/GCP.
   - `software-architecture`: Microservices, Database Design, System Design, Scalability.
   - `security-crypto`: Cybersecurity, AppSec, Cryptography, Zero-Trust.
   - `indie-hacking`: SaaS Building, Monetization, Bootstrapping, Product Growth.
2. **Subcategories & Topics (Tasks 46 & 47)**: Granular nesting (e.g., `ai-machine-learning` -> `rag-systems` -> `vector-databases`).
3. **Tags (Task 48)**: Flat folksonomy keywords (e.g., `#mysql8`, `#php8`, `#jwt`, `#ollama`, `#chatgpt`).
4. **Author Taxonomy (Task 49)**: Author tier tags: `Staff Writer`, `Guest Expert`, `Verified Builder`, `Community Contributor`.
5. **Trending & Featured Taxonomy (Tasks 59 & 60)**: Automated status flags calculated hourly based on 24h/7d velocity algorithms.
