# TechScout AI Engineering: Mistakes Learned & Prevention Rules

This document serves as the permanent registry of architectural and implementation mistakes identified across production releases, their root causes, and mandatory prevention rules. Every response and code modification must be audited against these rules prior to delivery.

---

## 1. Registry of Mistakes & Architectural Rules

### 🔴 MISTAKE-01: Runtime DDL in Application Request Lifecycle
* **Root Cause:** Executing DDL (`ALTER TABLE`, `CREATE TABLE`) dynamically inside web request handlers (controllers, mail dispatchers, telemetry loops) or during `Database::runIncrementalMigrations`.
* **Consequences:** 
  - Causes `SQLSTATE[42S21]: 1060 Duplicate column name` when columns already exist or during race conditions.
  - Causes table metadata locking and slows down user requests.
  - Free hosting or restricted database users (e.g. InfinityFree) often deny or throttle DDL during runtime.
  - An exception inside an incremental migration bails out the entire chain, leaving subsequent tables (e.g. `post_views`, `mail_templates`) uncreated.
* **Mandatory Rule:**
  - **NEVER** execute runtime `ALTER TABLE` or `CREATE TABLE` inside application controllers, service classes, or mail delivery functions.
  - When database changes or new tables/columns are needed, provide a dedicated, non-destructive upgrade SQL script (using `IF NOT EXISTS`, safe column additions, preserving all existing user records) for execution in phpMyAdmin / MySQL CLI.

---

### 🔴 MISTAKE-02: Silent Database Error Logging & Root-Cause Elimination
* **Root Cause:** Queries in `Database::executeSilent()` or fallback paths failed due to unmigrated columns, non-canonical table names, or duplicate index definitions, while either completely hiding genuine database corruptions or polluting error tables with benign warnings.
* **Consequences:**
  - Masking errors completely leaves developers unaware when queries are broken.
  - Logging non-fatal, expected idempotent operations (such as duplicate indexes) creates noise.
* **Mandatory Rule:**
  - **Eliminate Root Causes First:** Ensure schema, column names, and queries are verified so that silent errors do not occur in normal application flow.
  - **Selective Telemetry Logging:** `Database::executeSilent()` should log genuine, unexpected query failures to `system_error_logs` with contextual SQL and error details, but benign idempotent DDL warnings (e.g., duplicate index/column) should be filtered so production logs only contain actionable defects.

---

### 🔴 MISTAKE-03: Unsanitized JSON Serialization & Strict Check Constraints
* **Root Cause:** In MariaDB, `JSON` is an alias for `LONGTEXT ... CHECK (json_valid(col))`. If `json_encode()` encountered non-UTF8 strings or returned `false`, PDO bound `false` as `""` (empty string). MariaDB evaluates `json_valid('') = 0`, failing constraint 4025: `CONSTRAINT mail_logs.attachments_json failed`.
* **Consequences:**
  - Email logging failed whenever attachments or telemetry were dispatched, triggering silent error loops.
* **Mandatory Rule:**
  - In PHP: Always ensure UTF-8 encoding before `json_encode()`. If `json_encode()` fails or yields empty, default to `'[]'`. Never pass `false` or `""` into a JSON column.
  - In SQL: Modify `attachments_json` to `LONGTEXT NULL` and avoid strict check constraints that can reject edge-case payloads.

---

### 🔴 MISTAKE-04: Missing Tables in Production Without Upgrade SQL
* **Root Cause:** New telemetry features (like `post_views` synthetic view logging in `AdminController::simulateEngagement`) were added to code while the production database did not yet have the table.
* **Consequences:**
  - Threw fatal `SQLSTATE[42S02]: Base table or view not found: 1146 Table 'post_views' doesn't exist`.
* **Mandatory Rule:**
  - Application code querying auxiliary/telemetry tables must handle table absence gracefully (e.g. check table existence or safe try/catch without throwing 500 errors).
  - All new tables must be included in `database/schema.sql` and shipped in a standalone non-destructive upgrade script.

---

### 🔴 MISTAKE-05: Querying Obsolete or Divergent Table Names
* **Root Cause:** A previous revision referenced `custom_mail_templates` in `AdminController::mailTemplateEditor` when the official table in the system is `mail_templates`.
* **Consequences:**
  - Produced `SQLSTATE[42S02]: Base table not found: 1146 Table 'custom_mail_templates' doesn't exist`.
* **Mandatory Rule:**
  - Always cross-reference table and column names with canonical schema definitions (`database/schema.sql`).
  - Standardize on single canonical table names across all controllers, views, and models.

---

### 🔴 MISTAKE-06: Unclosed JavaScript Blocks Breaking View Execution Scope
* **Root Cause:** Inline `<script>` tags in PHP view templates containing unclosed `try...catch` blocks or orphaned brackets (e.g., `views/admin/mail.php` line 2806 and `views/layouts/admin_footer.php` line 448).
* **Consequences:**
  - Causes browser parsing failures: `Uncaught SyntaxError: Unexpected end of input` or `Unexpected token ')'`.
  - Halts script execution before global window methods are exported (`window.switchMailTab`, `window.loadTemplateToEditor`), leading to cascading HTML inline handler failures (`ReferenceError: switchMailTab is not defined`).
* **Mandatory Rule:**
  - All JavaScript written inside PHP view files must be syntax-checked with Node.js parser (`node -c`) before staging.
  - Global functions called by inline HTML attributes (`onclick`, `onchange`) must be explicitly declared and protected against parsing halts.

---

### 🔴 MISTAKE-07: Unchecked Reaction Counters Lacking Idempotent Toggle / Undo
* **Root Cause:** Comment upvote/helpful actions simply ran `UPDATE comments SET reaction_count = reaction_count + 1` without recording individual user or guest IP associations.
* **Consequences:**
  - A single user or spammer clicking the helpful button multiple times caused the counter to increase infinitely.
  - Users had no ability to undo or revoke an accidental reaction.
* **Mandatory Rule:**
  - All reaction and helpful interactions must maintain a relational record (e.g., `comment_reactions` with `comment_id`, `user_id`, and `ip_address`).
  - The API endpoint must act as an idempotent toggle: if a record exists for the user/IP, remove it and decrement the counter (`GREATEST(0, count - 1)`); if not, insert it and increment the counter.

### 🔴 MISTAKE-08: Hardcoded Static Error Pages Omitting Core Branding & Dynamic PHP
* **Root Cause:** Creating standalone `.html` files for error pages without bootstrapping the application or including `views/layouts/header.php` and `footer.php`.
* **Consequences:**
  - Users visiting error pages (403, 404, 405, 429, 500) saw disconnected, isolated screens lacking platform navigation, active session context, search, and corporate branding.
  - Adding Apache `ErrorDocument` directives into `.htaccess` conflicts with hosting control panels (e.g. InfinityFree / cPanel) where custom error pages are configured directly.
* **Mandatory Rule:**
  - Custom error pages must be executable PHP files that load application bootstrap and embed the official `header.php` and `footer.php` templates.
  - Do not force Apache `ErrorDocument` directives inside `.htaccess` when the hosting environment requires manual control panel mapping.

---

### 🔴 MISTAKE-09: Dual-Mode WYSIWYG & HTML Editor Insertion Failure & View Duplication
* **Root Cause:** 
  - Template editors containing both a visual WYSIWYG canvas (`contenteditable`) and a raw HTML `<textarea>` only manipulated `textarea.value` with `selectionStart`/`selectionEnd`. When in visual WYSIWYG mode (where the textarea is hidden), clicks on 1-click blocks or dynamic variable tags failed invisibly because the caret was in the WYSIWYG canvas, and subsequent edits wiped out textarea changes.
  - Duplicated layout HTML concatenated at the end of view templates resulted in duplicate DOM IDs (`#tmpl_html_input`, `#tmpl_wysiwyg_editor`), causing JavaScript to target orphaned nodes.
* **Consequences:**
  - "1-Click Insert Responsive Email Blocks" and "Click to Insert Tag" appeared completely broken to users in visual mode.
  - Dark mode and light mode discrepancies caused jarring visual contrast across template studio canvases and modals.
* **Mandatory Rule:**
  - All template insertion helpers (`insertTag`, `insertBlockComponent`) must support dual-mode caret insertion:
    - If WYSIWYG is active: insert HTML at the cursor using `window.getSelection()` and `Range` API with fallback, then immediately sync from WYSIWYG to the raw HTML textarea.
    - If raw HTML mode is active: insert into `textarea.value` at `selectionStart`, advance caret, and immediately sync from textarea to the WYSIWYG canvas.
  - Maintain clean light-theme visual consistency across all studio modals, toolbars, and preview canvases without dark/white theme conflicts.
  - Always verify template view files to ensure no duplicated template markup or ID collisions exist.

### 🔴 MISTAKE-10: Forcing Light Mode on Web UI Components & Inlining Tag Styles Instead of Dual-Theme CSS
* **Root Cause:** 
  - Confusing "light/white email boilerplate" with the web application's user interface. The dispatched email HTML and the inner preview canvas (`#wysiwyg_canvas` / `#preview_pane`) must be clean white, but the web page, studio toolbars, sidebar panels, modals, and container chrome must honor the platform's active dark or light theme.
  - Applying hardcoded inline `style="background: #fff; color: #000;"` directly onto individual HTML tags prevents the platform's theme classes (`data-theme="light"` / dark mode defaults) from functioning, breaking dark mode entirely.
* **Consequences:**
  - In dark mode, admin pages or modals appear with glaring, mismatched white boxes and broken visual hierarchy.
  - Maintainability is compromised because styles are scattered across individual tag attributes instead of centralized CSS rules.
* **Mandatory Rule:**
  - All web application components, modals, panels, toolbars, and inputs must use CSS classes and CSS variables (in `assets/css/admin.css` or scoped stylesheet blocks) that define explicit rules for both default dark mode and `[data-theme="light"]`.
  - Never hardcode inline `style="..."` colors on UI tags.
  - ONLY the email canvas preview card (representing the email viewport) and the actual dispatched email HTML are styled with a clean white email body.

### 🔴 MISTAKE-11: Unaliased Table Columns in Dynamic WHERE Clauses
* **Root Cause:** 
  - Applying SQL WHERE filter conditions with hardcoded table alias prefixes (e.g. `AND c.created_at >= ...`) to queries where the FROM table was NOT assigned that alias (e.g., `SELECT COUNT(*) as c FROM contents WHERE content_type = 'ai_tool' AND c.created_at >= ...`).
* **Consequences:**
  - MySQL / MariaDB throws `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'c.created_at' in 'WHERE'`, crashing admin analytics, telemetry HUDs, or background query dispatchers.
* **Mandatory Rule:**
  - When constructing dynamic SQL conditions, ensure the table alias used in the WHERE condition exactly matches the table alias defined in the FROM clause of each individual query.
  - For unaliased single-table queries, use unqualified column names (e.g. `created_at`) or qualify with the full table name (`contents.created_at`).
  - For joined multi-table queries where column name collision is possible (such as multiple tables having `created_at`), define an explicit aliased condition (e.g. `c.created_at`) and ensure the FROM table explicitly declares that alias (`FROM contents c`).
  - Never share a single aliased filter string across both aliased and unaliased queries.

### 🔴 MISTAKE-12: Assuming Extension Tables Mirror Parent Columns in Telemetry Queries
* **Root Cause:** 
  - Querying `SELECT SUM(copy_count) FROM prompts WHERE created_at >= ...` when `prompts` is an extension table linked to `contents` (`content_id -> contents.id`) and stores prompt-specific metadata, while timestamps (`created_at`, `updated_at`) historically lived exclusively on the parent `contents` table.
* **Consequences:**
  - MySQL / MariaDB throws `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'created_at' in 'WHERE'`, crashing `GET /admin/analytics`.
* **Mandatory Rule:**
  - In extension / child tables, verify whether timestamp columns reside on the child or the parent table. Always join the parent table (e.g. `prompts p LEFT JOIN contents c ON p.content_id = c.id WHERE c.created_at >= ...`) when querying creation dates.
  - Auxiliary and telemetry queries must be wrapped in safe try/catch blocks with sensible fallback values so minor metric calculations never crash primary analytics dashboards.
  - Provide a standalone, non-destructive SQL upgrade script (`database/prompts_telemetry_fix.sql`) and keep `database/schema.sql` synchronized.

---

### 🔴 MISTAKE-13: Asymmetric Theme Class Toggling & Un-Scoped Global CSS Variable Leakage
* **Root Cause:** 
  - `window.toggleTheme()` in `assets/js/main.js` toggled `theme-light` on `document.body` but failed to remove it from `document.documentElement`, leaving `html.theme-light` persistently active.
  - In `assets/css/style.css`, CSS variables were bound to `html.theme-light` and un-scoped `[data-theme="light"]`, causing the entire page's custom properties (`--ts-bg-main: #f8fafc`, `--ts-bg-surface: #ffffff`) to be stuck in light mode even when `data-theme="dark"` was set.
  - In `views/layouts/header.php`, the `<head>` script initialized before `<body>` existed, allowing `<body>` to retain server-rendered `data-theme="light"` and `class="theme-light"` indefinitely.
  - Invasive attribute selectors (`[data-theme="light"] [style*="background: #020408"]`) matched all dark inline containers when `theme-light` leaked, turning public page cards, tabs, and feeds white in dark mode.
* **Consequences:**
  - Dark mode rendered as a broken, hybrid state ("much weird and just like light in all public pages") with white cards and washed-out surfaces.
* **Mandatory Rule:**
  - All theme toggling routines (`main.js`, `header.php`, `admin_footer.php`) must symmetrically update both `data-theme` attribute and `theme-light` class on BOTH `document.documentElement` and `document.body`.
  - Include an immediate post-body micro-script in layouts to synchronize `document.body` before first paint.
  - Scope all light mode CSS variables strictly to root (`html[data-theme="light"], html.theme-light, body[data-theme="light"], body.theme-light`).
  - Keep default Obsidian dark mode (`#07090e`) and light mode (`#f8fafc`) 100% decoupled and isolated.

---

### 🔴 MISTAKE-14: Environment Disconnect Between Local Development and Cloud Hosting Database Credentials
* **Root Cause:** 
  - Placing static cloud database credentials (`sql306.infinityfree.com`) directly into the local development config causes local execution (XAMPP Apache, CLI tests) to hang or exhaust memory because cloud hosting providers (like InfinityFree) block remote external database access.
  - Conversely, pushing local credentials (`127.0.0.1`, `root`, blank password) to production breaks the live hosted platform.
* **Consequences:**
  - Local diagnostic and automated tests fail or crash PHP with memory exhaustion.
  - Deploying mismatched configs causes 500 errors in production.
* **Mandatory Rule:**
  - Local repository configuration (`app/Core/Config.php` in main workspace) must use local development credentials (`127.0.0.1`, `techscout_db`, `root`, ``, or auto-environment detection) for fast and reliable local execution.
  - The `changes/` staging folder (`changes/app/Core/Config.php`) must strictly preserve the production hosted InfinityFree credentials (`sql306.infinityfree.com`, `if0_40102772_techscoutmainafterallchanges`, `if0_40102772`, `Fp9QFWfj5fm`, `https://funwithcoding.free.nf`) so deployments remain fully functional without manual intervention.

---

## 2. Response Audit Protocol
At the conclusion of any code generation, the agent must cross-check every modified file against:
1. [ ] **No Runtime DDL**: Are there any `ALTER TABLE` or `CREATE TABLE` calls in application request paths?
2. [ ] **No Unchecked Silent Errors**: Are database operations verified so silent errors do not occur, while keeping meaningful error logging intact?
3. [ ] **Safe JSON Handling**: Are all JSON column values guaranteed to be valid JSON strings (defaulting to `'[]'` or valid JSON, never `false` or empty string)?
4. [ ] **Graceful Telemetry Operations**: Are auxiliary telemetry queries (like `post_views`, `prompts`) guarded with try/catch and sensible fallbacks so they never crash requests?
5. [ ] **Non-Destructive Upgrade SQL Provided**: Has an upgrade SQL script been provided for any database schema changes that preserves all existing data?
6. [ ] **Syntax-Verified Scripts**: Are all JavaScript view scripts and inline blocks verified with a JS parser to prevent `SyntaxError` and `ReferenceError`?
7. [ ] **Idempotent Reaction Toggling**: Do user reaction/helpful counters maintain state via user/IP tracking with toggle (add/undo) functionality?
8. [ ] **Branded Dynamic Error Pages**: Are error pages written as dynamic PHP scripts incorporating official header/footer layouts?
9. [ ] **Dual-Mode Editor Insertion & Cohesive Theme**: Do editor tag/block insertion tools support both WYSIWYG caret and raw HTML textarea modes with light theme consistency?
10. [ ] **Dual-Theme (Dark/Light) UI Preservation & No Hardcoded Inline Tag Styles**: Are web components styled via CSS with both dark and light modes supported, reserving white backgrounds strictly for the email preview canvas and email body?
11. [ ] **Accurate Table Aliases in Dynamic SQL**: Are WHERE filter clauses strictly verified against the FROM clause of each query to ensure alias consistency and prevent 1054 Unknown Column errors?
12. [ ] **Extension Table Parent Column Awareness**: Do queries on child/extension tables (e.g. `prompts`, `articles`) join their parent `contents` table when filtering by timestamps or common metadata?
13. [ ] **Symmetric Theme Toggling & Clean Dual-Mode Isolation**: Are `data-theme` and `theme-light` updated symmetrically on both `document.documentElement` and `document.body`, with light theme tokens strictly scoped to root selectors to prevent dark mode leakage?
14. [ ] **Segregated Local vs. Hosted Environment Credentials**: Are local configurations configured for localhost execution while `changes/app/Core/Config.php` preserves hosted production credentials?



