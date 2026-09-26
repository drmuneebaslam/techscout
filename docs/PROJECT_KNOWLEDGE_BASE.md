# TechScout Project Knowledge Base & Architecture Blueprint
**Version:** 3.0.0 Enterprise  
**Platform Status:** Multi-Tenant AI Aggregator & Operations Engine (40k+ LOC)  
**Production Domains:**  
- **AWS EC2 Production (Primary):** `https://techscout.online`
- **InfinityFree Hosted Mirror:** `https://funwithcoding.free.nf`
- **Local Dev Server:** `http://localhost/techscout.n` or `http://127.0.0.1:8000`

---

## 1. Credentials, Deployment & Remote System Registry
> [!IMPORTANT]
> Always verify these parameters before running any git push, FTP transfer, or server sync operation. Never query or alter credentials directly on client responses.

| Component | Host / Endpoint | Username / Target | Secrets / Tokens |
| :--- | :--- | :--- | :--- |
| **Git Repository** | `https://github.com/drmuneebaslam/techscout.git` | Branch: `main` | Authenticated Git Origin |
| **FTP Remote Deployment** | `ftpupload.net:21` | `if0_40102772` | `Fp9QFWfj5fm` (Target: `/htdocs/`) |
| **Hosted InfinityFree DB** | `sql306.infinityfree.com` | `if0_40102772` | Database: `if0_40102772_techscoutmainafterallchanges` |
| **EC2 MySQL Database** | `127.0.0.1:3306` | `techscout_user` | Database: `techscout_db` |
| **EC2 Deploy & Sync Webhook** | `POST https://techscout.online/api/v1/deploy/sync?token=ts_deploy_sec_99182374619a8b` | Admin Runner | Token: `ts_deploy_sec_99182374619a8b` |
| **Local MySQL Database** | `127.0.0.1:3306` | `root` | Database: `techscout` (no password) |

---

## 2. Directory Structure & Architecture Boundaries

```
c:\xampp\htdocs\techscout.n\
├── app/
│   ├── Controllers/          # Request orchestrators (AdminController, ContentController, etc.)
│   ├── Core/                 # Engine primitives (Auth, Database, Router, IncomingWebhookEngine, etc.)
│   ├── Middleware/           # Guard pipelines (AdminGuard, CsrfMiddleware, ThrottleMiddleware)
│   ├── Models/               # Active Record & query builders (Content, User, Prompt, Tool, etc.)
│   └── Helpers/              # Pure utilities (Sanitizer, DateHelper, ViewHelper)
├── assets/
│   ├── css/                  # admin.css, admin-modals.css, style.css, cyber-hud tokens
│   └── js/                   # PWA sw.js, telemetry-error-logger.js, admin runtime scripts
├── database/
│   ├── migrations/           # Sequential SQL migrations (executed automatically on EC2)
│   └── schema.sql            # Canonical database baseline
├── docs/                     # Living platform knowledge base & test specifications
│   ├── MISTAKES_LEARNED.md   # Architectural registry of past bugs & prevention rules
│   ├── PROJECT_KNOWLEDGE_BASE.md # This document
│   ├── ab/                   # A/B testing suite & statistical evaluation frameworks
│   └── qa/                   # 100+ feature exhaustive QA testing matrices
├── waste/                    # Isolated holding directory for superseded / archived documents
│   └── olddocs/              # Preserved historical docs
├── views/                    # Dual-theme PHP templates (admin/, layouts/, public/, creator/)
└── changes/                  # Pre-deployment staging folder for modified core files
```

---

## 3. Database Architecture & Relational Topology

### Core Entity Inheritance (Polymorphic `contents` Table)
TechScout uses a unified `contents` table as the polymorphic base for all content types (`prompt`, `ai_tool`, `article`, `gpt_listing`, `campaign`, `job`).
- **Base table (`contents`):** `id`, `uuid`, `title`, `slug`, `summary`, `body_html`, `featured_image_url`, `content_type`, `author_id`, `category_id`, `status` ('published'|'draft'|'pending'|'archived'), `views_count`, `created_at`, `updated_at`.
- **Extension tables (1-to-1 with `contents.id` as foreign key):**
  - `prompts`: `content_id`, `prompt_template`, `negative_prompt`, `aspect_ratio`, `ai_model`, `model_version`, `cfg_scale`, `seed`, `sampler`.
  - `ai_tools`: `content_id`, `pricing_type` ('free'|'freemium'|'paid'), `pricing_details`, `website_url`, `pros_json`, `cons_json`, `rating_avg`, `verified_status`.
  - `articles`: `content_id`, `reading_time_minutes`, `canonical_url`, `meta_title`, `meta_description`.
  - `gpt_listings`: `content_id`, `developer_name`, `direct_chat_url`, `system_prompt_preview`, `capabilities_json`.
  - `jobs`: `content_id`, `company_name`, `company_logo_url`, `location`, `job_type`, `salary_range`, `apply_url`.

### Platform Operations & Auxiliary Tables
- `incoming_webhooks`: Ingestion endpoints, dynamic mapping rules (`mapping_rules_json`), authentication tokens (`secret_key`), active toggle.
- `incoming_webhook_logs`: Captured raw JSON payloads, response codes, HTTP status, client IP address, records affected.
- `custom_api_endpoints`: Dynamically introspected table gateways, column filtering, ordering rules, and custom template projections.
- `users`: Credentials, RBAC roles (`admin`, `editor`, `creator`, `subscriber`), reputation points, wallet balances.
- `categories`: Recursive hierarchical taxonomy with infinite nesting depth (`parent_id`, `level`, `slug`).
- `comments`: Threaded discussions with parent/child relations, sentiment score, and spam classification.
- `comment_reactions`: Relational toggle ledger for idempotency (prevents infinite increment exploits).
- `site_settings`: Global key-value system settings (`enable_bg_grid_lines`, `default_theme`, `smtp_config`).

---

## 4. Webhook & Custom API Gateway Architecture

### Strict Mapping & Validation Flow (`IncomingWebhookEngine`)
1. **Authentication Check:**
   - `none`: Open public ingestion.
   - `bearer`: Requires `Authorization: Bearer <secret_key>`.
   - `signature`: Requires `X-Ts-Signature: sha256=hash_hmac('sha256', $rawBody, $secret_key)`.
2. **Payload Extraction:** Supports standard JSON payloads and multi-table atomic transactions (`tables: { ... }`).
3. **Strict Zero-Auto-Map Isolation:** If mapping rules are configured, **any key not explicitly mapped in `mapping_rules_json` is completely ignored** to prevent arbitrary database column pollution.
4. **Validation Rules Execution:**
   - `required`: Field must exist and not be empty.
   - `string`: Must be string or scalar representation.
   - `integer`: Must be valid numeric integer.
   - `url`: Must pass `filter_var(..., FILTER_VALIDATE_URL)`.
   - `email`: Must pass `filter_var(..., FILTER_VALIDATE_EMAIL)`.
   - `min:N` / `max:N`: Length boundary enforcement.
   - Validation failure rejects the payload with `HTTP 422 Unprocessable Entity` without altering the database.
5. **Captured Payloads Inspector:** The admin UI queries `GET /admin/incoming-webhooks/inspect/{slug}` to extract real keys seen in recent executions, allowing the admin to create field mappings with a single click.

---

## 5. UI Theme System & Modal Protocol
- **Default Theme:** Obsidian Dark (`:root`).
- **Light Theme:** Strictly triggered by `[data-theme="light"]` or `.theme-light`.
- **CSS Variable Contract:** All backgrounds, cards, typography, and borders MUST use `--admin-bg-*`, `--admin-border-*`, and `--admin-text-*`.
- **Modal Display Architecture:**
  - Classes: `.admin-modal`, `.admin-modal-backdrop`, `.admin-modal-content`.
  - Properties: `position: fixed; inset: 0; z-index: 99999; backdrop-filter: blur(6px); display: flex; align-items: center; justify-content: center;`.
  - Never allow modal markup to render as regular in-flow document elements.

---

## 6. Mandatory Deployment & Verification Sequence
For **every change** made to the project:
1. `changes/` folder is wiped clean.
2. Code edits are implemented and verified against `docs/MISTAKES_LEARNED.md`.
3. Newly modified core files are copied to `changes/` preserving directory paths.
4. Git commit is performed with explicit details and pushed to `origin/main`.
5. Modified files are deployed via FTP to `ftpupload.net:21` `/htdocs/` without touching pre-existing hosting directories.
6. Deployment webhook is dispatched to EC2: `POST https://techscout.online/api/v1/deploy/sync?token=ts_deploy_sec_99182374619a8b` to run migrations and sync production.
