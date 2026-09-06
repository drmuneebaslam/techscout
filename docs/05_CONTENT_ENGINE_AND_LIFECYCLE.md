# TechScout — Content Engine & Editorial Lifecycle

> **Tasks Covered**: 115–160 (Phase 4 — Article System) & 361–380 (Phase 12 — Moderation & Quality)  
> **Key Architecture**: Finite State Machine (FSM) Lifecycle, Real-time Autosave, Revision Diffing, and Automated Quality Scoring.

---

## 1. Content State Machine Lifecycle (Tasks 115–122)

Every piece of content created on TechScout moves through a rigorous editorial lifecycle:

```
                  +----------------------------------------------+
                  |                                              |
                  v                                              |
            +------------+     Submit     +--------------+       | Request
            |   DRAFT    | -------------> |  IN REVIEW   |       | Changes
            +------------+                +-------+------+       |
                  |                               |              |
                  | Autosave                      | Approve      |
                  v                               v              |
           [Local Drafts]                 +--------------+-------+
                                          |  SCHEDULED   |
                                          +-------+------+
                                                  | Release Time
                                                  v
                                          +--------------+
                                          |  PUBLISHED   |
                                          +-------+------+
                                                  | Archive / Unpublish
                                                  v
                                          +--------------+
                                          |   ARCHIVED   |
                                          +--------------+
```

### State Transitions & Rules:
1. **`draft` (Task 117)**: Editable exclusively by the author. Autosaved every 30 seconds to the server and cached in browser `localStorage`.
2. **`in_review` (Task 118)**: Submitted by author to the editorial queue. Locked for editing by author unless recalled or requested changes by editor.
3. **`scheduled` (Task 121)**: Approved by editor with a designated future publication timestamp `scheduled_at`. Handled by cron worker every 5 minutes.
4. **`published` (Task 120)**: Live on the public site, indexed in sitemaps, distributed to followers, and active in RSS feeds.
5. **`rejected` (Task 120)**: Returned to author with structured feedback, critique notes, and improvement recommendations.
6. **`archived` (Task 122)**: Delisted from public streams, preserved with a `301` redirect or `410 Gone` header if permanently sunset.

---

## 2. Versioning, Revisions & Autosave (Tasks 123–126)

### Task 123 & 124: Revision History & Version Diffing
- Whenever an article is updated post-publication, a snapshot is preserved in `content_revisions`.
- Revision tracking stores `version_number`, `title`, `summary`, `body_html`, and `editor_id`.
- Authors and editors can compare historical revisions side-by-side using unified visual HTML diffing.

### Task 125 & 126: Real-time Autosave & Live Preview
- **Autosave Engine**: Client debounces typing (1500ms). If changes exist, sends `POST /api/v1/author/draft/autosave`.
- **Live Preview (Task 126)**: Side-by-side split screen in the author studio rendering Markdown / HTML in real time with syntax highlighting and responsive viewport toggles (Desktop, Tablet, Mobile).

---

## 3. Slugging, Canonical URLs & Metadata (Tasks 127–131)

### Task 127: Slug Generation & Sanitization
- Slugs are automatically generated from title strings using strict normalization:
  ```php
  function generateSlug(string $title, ?int $existingId = null): string {
      $slug = strtolower(trim($title));
      $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
      $slug = trim($slug, '-');
      // Ensure unique collision resolution: 'building-rag-pipelines-2'
      return $slug;
  }
  ```

### Task 128: Canonical URL System
- Canonical tags are injected on every content page to prevent duplicate content penalties:
  ```html
  <link rel="canonical" href="https://techscout.online/article/building-production-rag-pipelines">
  ```

### Tasks 129–131: SEO Fields & Schema.org JSON-LD
- Every content entity defines custom `seo_title` (max 60 chars), `seo_description` (max 160 chars), OpenGraph image, and automated `Article` or `TechArticle` JSON-LD schema.

---

## 4. Reading Dynamics & Structural Hierarchy (Tasks 132–154)

### Task 137: Reading Time Calculation Formula
- Based on the standard technical reading velocity of **200 words per minute** plus **15 seconds per code snippet**:
  $$\text{Reading Time (minutes)} = \left\lceil \frac{\text{Total Word Count}}{200} + \frac{\text{Code Snippets Count} \times 15}{60} \right\rceil$$

### Task 139: Table of Contents (TOC) Generator
- Client-side script parses all `<h2>` and `<h3>` tags in the article body.
- Generates a sticky sidebar navigation with smooth scroll anchors and active intersection observer highlighting.

### Task 145: Code Blocks & Syntax Highlighting
- Rendered via **Prism.js** with language tags (`php`, `javascript`, `python`, `sql`, `bash`, `json`).
- Includes one-click **Copy Code** button, line numbers, and dark syntax theme (`prism-tomorrow`).

### Task 154: Affiliate & External Link Handler
- All external links are automatically appended with `rel="noopener noreferrer"`.
- Affiliate links automatically receive `rel="nofollow sponsored"` and render an editorial disclaimer badge.

---

## 5. Moderation, Quality Scores & Anti-Abuse (Tasks 361–380)

```
                            SUBMITTED ARTICLE
                                    │
                                    ▼
                    [1. Automated Quality Score Engine]
                      - Word count check (min 600 words)
                      - Code block / Example validator
                      - Broken link detection scanner
                      - Readability & Flesch-Kincaid index
                                    │
                                    ▼
                    [2. Plagiarism & AI Disclosure]
                      - Duplicate substring matching against DB
                      - AI generation disclosure validation
                                    │
                                    ▼
                    [3. Human Editorial Review Queue]
                      - Technical accuracy check
                      - Category & Tag appropriateness
                      - Fact-checking & Source verification
                                    │
                         ┌──────────┴──────────┐
                         ▼                     ▼
                    [APPROVED]            [REVISION REQ]
```

### Task 363: Automated Quality Score Algorithm (0–100)
- **Content Depth (30 pts)**: >1200 words (30 pts), 800–1200 words (20 pts), <600 words (0 pts).
- **Practical Examples (25 pts)**: Contains 2+ formatted code snippets, diagrams, or prompt templates.
- **Structural Integrity (20 pts)**: Has proper `H2/H3` hierarchy, intro summary, and conclusion.
- **Link Health (15 pts)**: Zero broken links detected by crawler.
- **SEO Optimization (10 pts)**: Complete meta description, featured image, and tagged keywords.

### Tasks 377–380: Strike & Suspension Enforcement
- **Strike 1**: Warning email and required revision for plagiarism or deceptive clickbait.
- **Strike 2**: 14-day publishing suspension and removal of verified author badge.
- **Strike 3**: Permanent account termination, content delisting, and forfeiture of promo credits.
