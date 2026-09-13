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

### 🔴 MISTAKE-15: Non-Isolated FTP Sync & Mutation of Existing Hosting Directories
* **Root Cause:** 
  - Using broad FTP sync scripts, root directory wiping, or un-scoped directory mirroring when deploying to hosted accounts containing existing subdirectories (such as `old/`, `old2/`, `oldlat/`, `tech/`, subdomain folders, or legacy files).
* **Consequences:**
  - Accidental deletion or corruption of existing historical archives, secondary domains, or unrelated hosted applications.
* **Mandatory Rule:**
  - FTP deployment scripts must strictly target `/htdocs/` and upload ONLY the specific files tracked in `changes/` that were modified during the task.
  - It is strictly forbidden to run destructive file deletions, recursive wipes, or modify any directories outside the active project scope.
  - Ensure all uploaded configuration files (`app/Core/Config.php`) contain hosted production credentials.

---

### 🔴 MISTAKE-16: Session Flash Premature Consumption in UI Views
* **Root Cause:** 
  - Calling `\App\Core\Session::getFlash('query_results')` multiple times inside a single view (e.g., first inside a `<textarea>` to restore the query SQL, and later in an `if (!empty($queryFlash))` check).
  - In `Session::getFlash($key)`, retrieving a flash key unsets it from `$_SESSION['_flash'][$key]`.
* **Consequences:**
  - The first call consumes and destroys the flash data, causing the subsequent block to always receive `null`.
  - As a result, database query outputs, validation payloads, or success grids fail to display ("when i run query it does not show anything").
* **Mandatory Rule:**
  - In view templates, always retrieve flash data ONCE at the top of the relevant block into a local variable (e.g. `$queryFlash = \App\Core\Session::getFlash('query_results');`), and reference that variable for all subsequent checks and field bindings.

---

### 🔴 MISTAKE-17: Modal Dark-Mode Inversion from Hardcoded Inline Styles & Generic Overrides
* **Root Cause:** 
  - Applying hardcoded dark inline styles (`background: #090d16; color: #f8fafc;`) to modal elements while generic stylesheet rules (e.g. `[data-theme="light"] .admin-modal-box { background: #ffffff !important; }`) indiscriminately override the element when the body or HTML tag has `theme-light` active.
  - Using hardcoded colors prevents modal components from responding smoothly to dual theme changes.
* **Consequences:**
  - Modals (such as Audit Log Forensic Inspector and Error Logs Inspector) appear inverted, white, or broken in dark mode ("admin-modal-box in audit log inspector is weired and light even in dark mode").
* **Mandatory Rule:**
  - Create dedicated semantic classes (e.g. `.audit-inspector-box`, `.audit-inspector-card`, `.audit-inspector-code`) using CSS variables (`var(--admin-card-bg)`, `var(--admin-text-main)`, `var(--admin-border-color)`).
  - Decouple specialized inspector telemetry modals from generic class overrides (`.admin-modal-box`), ensuring complete visual fidelity in both Obsidian Dark and Clean Light modes.

---

### 🔴 MISTAKE-18: Missing Role Auth Methods & Unscoped Form Control Fallback to User-Agent White
* **Root Cause:**
  - Invoking `Auth::requireRole(...)` across administrative controllers without declaring the method in `App\Core\Auth`, triggering fatal 500 errors on routes like `/admin/mail/trigger/create` and `/admin/revenue/distribute-pool`.
  - Defining theme variables with subtle name mismatches (`--ts-bg` vs `--ts-bg-base`, `--ts-card-bg` vs `--ts-bg-surface`) and omitting default `:root` styling for form controls (`input`, `select`, `textarea`). In the absence of dark-mode base definitions, browser user-agent stylesheets apply `#ffffff` background and black text to inputs even in dark mode.
* **Consequences:**
  - Admins experience 500 Internal Server Errors when creating automation triggers or executing role-protected actions.
  - Public visitors see inputs, textareas, dropdowns, and cards rendering as jarring white boxes in dark mode ("public site all things are light even in dark mode except a few things all look weird").
* **Mandatory Rule:**
  - Always implement `Auth::requireRole(array $roles, string $redirect = '/auth/login'): void` supporting both AJAX JSON responses (HTTP 403) and session redirect fallback.
  - In `assets/css/style.css`, specify `color-scheme: dark;` on `:root` and `color-scheme: light;` on `[data-theme="light"]`. Define explicit default dark styling and background colors for all `input`, `textarea`, and `select` elements in `:root`, alongside alias tokens (`--ts-bg`, `--ts-card-bg`, `--ts-border-subtle`, `--ts-text-muted`) to ensure consistent dual-theme rendering across all public and admin views.

---

### 🔴 MISTAKE-19: Duplicate Click Event Triggers & Immediate Inversion on Theme Toggling
* **Root Cause:**
  - Having both an inline `onclick="toggleTheme()"` in HTML and a programmatic `addEventListener('click', window.toggleTheme)` in JavaScript on the same button element.
  - When the user clicks the button once, the browser dispatches the inline handler (dark -> light) followed immediately by the event listener (light -> dark) in the exact same event cycle (within 0.1ms).
  - Also omitting an early theme restoration script in the admin header `<head>`, causing admin pages to desynchronize with `localStorage`.
* **Consequences:**
  - The theme button appears completely unresponsive or broken ("toggle theme button is not working") because the theme is toggled twice in rapid succession, reverting instantly to the original state.
* **Mandatory Rule:**
  - In `window.toggleTheme()`, implement a strict debounce/re-entrancy guard (`isToggling` timeout window of 250ms) to ignore any duplicate triggers during the same click event.
  - In `assets/js/main.js`, do not attach programmatic click listeners to buttons that already declare an inline `onclick` attribute.
  - Synchronize theme button icons using `btn.innerHTML = '<i data-lucide="..."></i>'` followed by `lucide.createIcons()` so that Lucide re-renders a fresh SVG rather than failing on pre-existing `<svg>` nodes.
  - Include the early theme initialization script in both `views/layouts/header.php` and `views/layouts/admin_header.php`.

---

### 🔴 MISTAKE-20: Direct Outbound Links Bypassing Referral / Affiliate Tracking Gateway
* **Root Cause:**
  - Directly embedding external third-party URLs (`$tool['website_url']`, `$gpt['openai_url']`, `$tool['affiliate_url']`) into `<a href="...">` on detail pages or card feeds instead of routing through the dedicated `/go/{slug}` tracking gateway.
* **Consequences:**
  - Outbound traffic leaves the site without analytics telemetry logging (`Analytics::logEvent('click_outbound')`), affiliate attribution parameters are lost, and users are exposed to unmasked third-party domains.
* **Mandatory Rule:**
  - All external outbound links for tools, custom GPTs, and directory items must be routed through `url('/go/' . $slug)` (with optional `?type=docs`, `?type=repo`, `?type=discord` for sub-resources).
  - The gateway at `DirectoryController::affiliateRedirect` handles telemetry, affiliate resolution, and safe 302 redirects.

---

### 🔴 MISTAKE-21: Undefined Admin Theme Icon Helpers & Premature Inline Action Dispatch Before Script Evaluation
* **Root Cause:**
  - In `views/layouts/admin_footer.php`, calling `syncAdminThemeIcon()` during sidebar initialization when only `window.syncAllThemeButtons` had been defined in `admin_header.php`, producing `Uncaught ReferenceError: syncAdminThemeIcon is not defined`.
  - In `views/admin/mail.php`, defining navigation tab switching functions (`switchMailTab`) inside a bottom `<script>` block 1,800 lines below the HTML navigation buttons, preceded by `json_encode($templateLibrary)` which could produce a fatal JS syntax error (`var MAIL_TEMPLATES = window.MAIL_TEMPLATES = ;`) if encoding failed.
  - Calling `Auth::requireRole` without defensive existence checks or failing to deploy `app/Core/Auth.php` to the hosted environment alongside `AdminController.php`.
* **Consequences:**
  - Telemetry error logs flooded with `ReferenceError: syncAdminThemeIcon is not defined`, `ReferenceError: switchMailTab is not defined`, and `Call to undefined method App\Core\Auth::requireRole()`.
  - Admin UI buttons fail to respond on initial clicks and telemetry auto-refresh endpoints return 500 fatal errors.
* **Mandatory Rule:**
  - Define all global helper methods and aliases (`syncAdminThemeIcon`) in both header and footer scopes to ensure they can never throw reference errors regardless of load order.
  - Declare interactive tab handlers (`window.switchMailTab`) in an early `<script>` block immediately above the markup containing their inline `onclick` handlers, and safeguard dynamic JSON output with `(json_encode(...) ?: '{}')`.
  - Use defensive method checks (`method_exists(Auth::class, 'requireRole')`) with fallback to `requireAdmin()` in administrative controllers, and ensure `app/Core/Auth.php` is deployed with every release.

---

### 🔴 MISTAKE-22: Bypassing Timezone Localization Engine with Raw PHP `date()` and Missing Client-Side `<time>` DOM Synchronization
* **Root Cause:**
  - Administrative views and table templates used raw PHP `date('H:i:s', strtotime(...))` and `date('M j, Y', strtotime(...))` instead of the centralized `DateHelper` localization engine.
  - MySQL stores `DATETIME` records in Pakistan Standard Time (`Asia/Karachi`, UTC+5) and `index.php` initializes `date_default_timezone_set('Asia/Karachi')`. Calling raw `date()` directly evaluated under the server's default timezone, displaying Pakistan Standard Time to every user worldwide.
  - Browser timezone detection (`Intl.DateTimeFormat().resolvedOptions().timeZone`) only set a cookie on initial load, leaving static HTML rendered in PKT until page refresh, with no client-side DOM synchronization for `<time>` elements.
  - Using hardcoded inline colors (e.g. `style="color: #f8fafc"`) in timestamp markup rather than semantic CSS variables, violating Rule 3 (Dual Theme Preservation).
* **Consequences:**
  - Global visitors saw mismatched times for error logs, audit forensics, payouts, newsletter queues, and content moderation.
  - First-time visitors without an established cookie saw server default timestamps with no real-time client correction.
  - Light mode theme displayed illegible white text if inline styles bypassed theme CSS variables.
* **Mandatory Rule:**
  - Always render database timestamps using `\App\Core\DateHelper::render($time, $format, $formatType)` or `\App\Core\DateHelper::renderTwoLines($time)`.
  - Always output semantic `<time class="ts-time" datetime="YYYY-MM-DDTHH:mm:ss+05:00" data-format-type="...">` wrappers without hardcoded inline tag colors. Use CSS classes (`.ts-time-part`, `.ts-date-part`, `.ts-tz-abbr`) defined with CSS custom properties (`var(--admin-text-primary)`).
  - Implement dual-layer timezone localization:
    1. **Server-side:** `DateHelper::timezone()` checks user profile, `$_COOKIE['user_timezone']`, and automatically resolves the visitor's timezone from their IP location via `DateHelper::detectTimezoneByIp()`. Session caching, Cloudflare country code headers, and fast `ip-api` lookups guarantee 0ms latency after initial detection.
    2. **Client-side:** `window.localizeAllTimestamps()` and `window.detectAndSyncIpTimezone()` query `/api/v1/geo/timezone`, cache in `localStorage`, persist the `user_timezone` cookie, and dynamically adjust all `<time class="ts-time">` DOM elements with `{ timeZone: userTz }`.

### 🔴 MISTAKE-23: Missing File Upload Buttons & Single-Image Constraints Where Multi-Upload Is Needed
* **Root Cause:** Forms and creation studios across the platform (such as prompts, articles, tools, sponsor ads, and user profiles) either lacked an explicit file upload button (expecting users to manually copy/paste remote image URLs) or arbitrarily limited image uploads to a single file when the feature domain naturally benefits from multiple showcase images (e.g. image prompts with multiple variations, software screenshots, ad creatives, author portfolio samples).
* **Consequences:**
  - Degraded user experience: users were forced to host images elsewhere and paste URLs.
  - Generative AI prompt outputs could only show one variation instead of side-by-side or multi-sample generations.
  - Image directory listings could break or render empty image placeholders for text-only prompts.
* **Mandatory Rule:**
  - **Everywhere an image is required or supported across the site**, always provide an explicit, styled `<input type="file">` upload button with drag-and-drop support, client-side preview, file size/type validation, and automatic WebP conversion.
  - **Multi-Image Support:** Wherever multiple images make sense (e.g., prompt output variations, tool screenshots, article gallery), support `multiple` file uploads, storing image arrays or related child records (`prompt_images`, `content_media`), instead of restricting to 1 image.
  - **Graceful Non-Image Handling:** In directory listings, never render broken image frames or generic placeholders for prompts/tools that do not have images. Differentiate visual cards from text/code prompt cards.

### MISTAKE-24: HTML5 `type="url"` Rejection on Relative Uploaded Storage Paths
* **Issue**:
  - In form inputs meant for image URLs or creative paths (e.g., ad manager, sponsor campaign, author avatar, tool logo), the field was defined as `<input type="url">`.
  - When the user uploaded an image via the file upload button, the AJAX handler saved the file and returned a local relative path (e.g., `/storage/uploads/ads/ad_123.png`).
  - Upon clicking "Save" or "Submit", the browser blocked form submission with a native tooltip error: *"Please enter a URL"*, because browser validation for `type="url"` strictly requires an absolute protocol scheme (`http://` or `https://`).
* **Consequences**:
  - Users were blocked from submitting ads, tools, and content after successfully uploading images.
  - Frustration as the path was visibly filled in the input box, yet rejected by browser constraint validation.
* **Mandatory Rule**:
  - **Never use `type="url"` for media/image inputs** where uploaded relative asset paths (e.g. `/storage/uploads/...`, `/assets/...`, `uploads/...`) can be populated.
  - Always use `<input type="text">` with a descriptive placeholder: `e.g. https://... or /storage/uploads/...`.
  - On both frontend validation and backend controllers, permit both valid absolute URLs (`http://`, `https://`) and valid relative site storage paths starting with `/` or containing supported image extensions (`.png`, `.jpg`, `.jpeg`, `.webp`, `.gif`, `.svg`).

---

### 🔴 MISTAKE-25: Static Invocation on Non-Static Instance Methods in PHP 8+
* **Root Cause**: In `Request.php`, `ip()` was declared as an instance method `public function ip(): string`. In various controllers and views (e.g. `settings.php:204`), it was invoked statically as `Request::ip()`.
* **Consequences**:
  - In PHP 8.0+, calling non-static methods statically throws a fatal `Error: Non-static method App\Core\Request::ip() cannot be called statically`.
  - The request crashes completely with a 500 fatal error.
* **Mandatory Rule**:
  - Utility and context-interrogation methods on `Request` (such as `Request::ip()`, `Request::userAgent()`) that can logically be consumed statically must be declared with `public static function`.
  - Always verify static vs instance method declarations in core framework classes.

---

### 🔴 MISTAKE-26: Swallowed Exceptions Bypassing Critical Middleware (Maintenance Mode)
* **Root Cause**: Maintenance mode middleware wrapped in `try { ... } catch (\Throwable $e) {}` invoked non-existent methods or encountered fatal errors (e.g. `Request::getUri()` returning full URI or throwing undefined method), silently aborting maintenance mode checks and proceeding directly to normal routing.
* **Consequences**:
  - Maintenance mode appeared completely broken and failed to render the 503 template.
  - Admins had no feedback why the site was online or offline.
* **Mandatory Rule**:
  - Always log caught exceptions in core bootstrap middleware to error telemetry instead of silent empty catch blocks.
  - Normalize request paths using `$request->getPath()` rather than raw URI query strings.
  - Support instant previewing with query parameters (`?preview_503=1`) and render a persistent, dual-theme administrative indicator banner on all pages when maintenance mode is active.

---

### 🔴 MISTAKE-27: Service Worker RespondWith Promise Null/Undefined Value Conversion
* **Root Cause**: In `sw.js`, `event.respondWith()` was passed a fetch promise chained with `.catch(() => caches.match(event.request))`. When offline or on network failure, if the cache did not contain the requested resource, `caches.match()` resolved to `undefined`.
* **Consequences**:
  - Threw uncaught browser exception: `sw.js:1 Uncaught (in promise) TypeError: Failed to convert value to 'Response'`.
  - The browser treated the request as a failed fetch event and broke navigation.
* **Mandatory Rule**:
  - In service workers, always ensure `.catch()` handlers resolve to a valid `Response` object (e.g. a fallback cached offline page `Response` or an informative SVG/JSON fallback) rather than resolving to `undefined`.
  - Exclude admin paths (`/admin`), authentication (`/auth`, `/login`), telemetry, and hosting cookies (`?i=1`) from service worker interception.

---

### 🔴 MISTAKE-28: Apache 404 Rewrite Fallback Intercepting Manifest and Service Worker
* **Root Cause**: Single-page framework `.htaccess` rules routed non-file requests to `index.php`. When browsers requested `/manifest.json` or `/sw.js` without physical file existence or explicit exemptions, the router returned an HTML 404 page with `<!DOCTYPE html>`.
* **Consequences**:
  - Browser JSON parser threw: `manifest.json:1 Manifest: Line: 1, column: 1, Syntax error`.
* **Mandatory Rule**:
  - In `index.php`, intercept root PWA assets (`manifest.json`, `sw.js`) at the very top of execution before routing, serving physical files directly with strict MIME headers (`application/manifest+json`, `application/javascript`) and exiting immediately.

---

### 🔴 MISTAKE-29: Gating Multi-Image Media Displays by Strict Entity Subtype
* **Root Cause**: In `views/directory/prompt_single.php`, visual render showcase and thumbnail gallery components were conditioned on `$isImageEngine = (($prompt['prompt_type'] ?? '') === 'image');`. Prompts categorized as text, code, or workflows that had screenshots or visual architecture diagrams in `featured_image_url` or `prompt_images` were completely blocked from rendering any media.
* **Consequences**:
  - Attached images and interface screenshots failed to display for users, giving the appearance of lost or broken media.
  - Creators who uploaded diagrams or workflow captures could not preview or show their assets.
* **Mandatory Rule**:
  - Always evaluate gallery display based on the presence of media assets (`!empty($allImages)` or `featured_image_url != ''` or `prompt_images` count > 0) rather than restricting display to a specific classification or engine subtype.

---

### 🔴 MISTAKE-30: Shallow Flattening & Unhandled Recursion in Taxonomy/Category CRUD
* **Root Cause**: Category CRUD forms restricted parent selection to `WHERE parent_id IS NULL`, preventing multi-level nesting beyond level 1. Furthermore, hierarchical rendering lacked recursion and cycle detection, causing deep subcategories to either disappear or disrupt layout hierarchy.
* **Consequences**:
  - Admins could not organize complex category trees (e.g. `Prompts > AI Image Prompts > Midjourney > Photorealism`).
  - Circular parenting references caused database lookup deadlock or infinite recursion.
* **Mandatory Rule**:
  - Support arbitrary, unlimited category nesting with recursion safeguards (depth limits, visited IDs cycle detection, and self-parenting prevention).
  - Provide dedicated sub-tabs by entity scope (`All`, `Articles`, `AI Tools`, `Custom GPTs`, `Prompt Blueprints`, `Challenges`) with hierarchical indentation (`↳`, level badges) and instant "+ Add Subcategory" action buttons.

---

### 🔴 MISTAKE-31: Dual-Schema Mismatch & Non-Atomic Tipping Verification
* **Root Cause**: The `author_tips` table status ENUM is `('completed','pending','refunded')`. Admin verification methods attempting to set status to `'verified'` or `'rejected'` violated MySQL strict ENUM constraints, causing silent updates or database exceptions. In addition, tip balance crediting and ledger records were not synchronized atomically.
* **Consequences**:
  - Tip status updates silently failed or stored empty strings.
  - Creator balances were desynchronized from the financial ledger and incoming tips queue.
* **Mandatory Rule**:
  - Always synchronize tip status values strictly with the database schema (`'completed'` for verified/paid, `'refunded'` for rejected).
  - Tipping approvals must run with dual-schema compatibility (`amount_usd` vs `gross_amount`, `net_amount_usd` vs `net_author_amount`), atomically crediting `author_tip_balances`, recording a credit entry in `financial_ledger`, generating an in-app notification, and logging to `audit_logs`.

### 🔴 MISTAKE-32: Unreachable Remote DB Host Lockout & Admin Listing Query Bloat
* **Root Cause**: 
  1. When backups are downloaded from hosted production (e.g. InfinityFree) and launched locally, remote database hosts (`sql306.infinityfree.com`) block incoming connections via hosting firewalls. Without connection timeouts (`PDO::ATTR_TIMEOUT`), PDO hangs for 30–60 seconds, triggering PHP execution timeouts and emitting a blank screen before routing.
  2. The presence of `storage/installed.lock` in downloaded FTP backups previously locked the setup wizard, preventing developers from configuring local credentials.
  3. Admin panel listing queries used `SELECT c.*`, pulling megabytes of `body_html` into memory for table views that only display title, status, and metadata, while multiple independent `SELECT COUNT(*)` queries caused excessive database round-trips.
* **Consequences**:
  - Downloaded backups crashed on first local launch with a fatal blank screen.
  - Setup wizard was inaccessible due to stale `installed.lock` files from production.
  - Admin panel navigation was sluggish due to redundant queries and massive payload sizes.
* **Mandatory Rule**:
  - Always enforce `PDO::ATTR_TIMEOUT => 2` or `3` across all PDO connections to prevent script hanging.
  - In `Config.php`, intelligently detect local development environments and bridge remote InfinityFree credentials to local MySQL fallback (`127.0.0.1`).
### 🔴 MISTAKE-33: False Installation Lockout on First-Run FTP Restorations & Missing Custom SQL Uploader
* **Root Cause**:
  1. When downloading a full repository backup from an existing production server, `storage/installed.lock` is included in the archive.
  2. In the setup wizard, `InstallController::isInstalled()` checked solely for the presence of `storage/installed.lock`. When the user configured a new local database, `Database::hasConnection()` was established, but the wizard was locked because `isInstalled()` returned `true` without checking whether database tables actually existed.
  3. Attempting to submit the wizard in Step 5 threw `403 Forbidden: Setup wizard is locked. Authorization required.`, preventing setup completion.
  4. The installer only supported executing a hardcoded `database/schema.sql` and `seeds.sql`, lacking support for importing custom `.sql` database dumps exported from phpMyAdmin or prior backups.
* **Consequences**:
  - Developers restoring FTP archives could not complete the setup wizard on fresh environments without manually deleting `storage/installed.lock`.
  - Database could not be populated with custom user backups through the UI installer.
* **Mandatory Rule**:
  - `InstallController::isInstalled()` must verify not just `storage/installed.lock`, but whether the database is actually populated (e.g., table `contents` exists). If tables do not exist, the platform must be considered uninstalled.
  - `InstallController::process()` must allow installation when `!$isInstalled` OR `!empty($request->post('force_reinstall'))` from the installer form.
  - Automatically create target databases using server-level connection (`CREATE DATABASE IF NOT EXISTS \`{$dbname}\``) so setup never fails due to missing databases on fresh setups.
### 🔴 MISTAKE-34: Uninitialized Bootstrap Variable Access in Platform Entrypoint (`index.php`)
* **Root Cause**:
  - In `index.php`, code evaluating `$isFullyInstalled = file_exists($lockPath) && ...` was placed before `$lockPath = __DIR__ . '/storage/installed.lock'` was declared.
  - When PHP error reporting is configured to display warnings or when run under strict error tracking, this triggers `PHP Warning: Undefined variable $lockPath in index.php on line 146`.
* **Consequences**:
  - Emits PHP warning notices on bootstrap that can break JSON API responses, corrupt HTTP headers, or display ugly warnings to end users on custom or local installations.
* **Mandatory Rule**:
  - In `index.php`, all bootstrap state variables (`$lockPath`, `$reqUri`, `$isStaticAsset`, `$isInstallRoute`, `$isDbPopulated`) must be strictly initialized and declared *before* any installation checks, conditions, or redirects are executed.
  - Always run `php -r "error_reporting(E_ALL); require_once 'index.php';"` during verification to guarantee zero undefined variable notices, deprecation warnings, or fatal exceptions.

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
15. [ ] **Isolated Direct FTP Deployment & Zero Mutation of Unrelated Remote Folders**: Are uploads to `/htdocs/` via FTP restricted solely to the modified files, strictly preserving all pre-existing folders (`old/`, `old2/`, `oldlat/`, `tech/`, etc.) untouched?
16. [ ] **Single-Consumption Session Flash Extraction**: Is `Session::getFlash()` called exactly once per key and assigned to a local variable to prevent early wipeout before rendering?
17. [ ] **Semantic Dual-Theme Modals & CSS Variables**: Are modal dialogs and inspectors styled using semantic classes and CSS variables without hardcoded tag colors or unwanted generic class overrides?
18. [ ] **Comprehensive Role Auth & Dual-Mode Form Controls**: Is `Auth::requireRole` implemented and are base form controls explicitly themed with dark `:root` and light tokens to prevent user-agent white background leaks?
19. [ ] **Debounced Theme Toggling & Single Event Listener Enforcement**: Does `window.toggleTheme()` contain a debounce re-entrancy guard, avoiding double-invocation from combined inline and programmatic event listeners?
20. [ ] **Branded Outbound /go/{slug} Gateway Routing**: Are all external tool, custom GPT, and directory destination links routed via `/go/{slug}` instead of exposing direct third-party URLs?
21. [ ] **Early Script Declarations & Robust Global Helpers**: Are interactive UI handlers declared early before markup, dynamic JSON safely defaulted, and theme sync helpers universally available?
22. [ ] **Universal Timezone Auto-Detection & Dual-Layer Localization**: Are all timestamps rendered with `DateHelper::render()` or `DateHelper::renderTwoLines()` using ISO-8601 `<time class="ts-time">` markup, backed by `Intl.DateTimeFormat` client synchronization and cookie persistence without hardcoded inline styles?
23. [ ] **Explicit Image Upload Buttons & Multi-Image Gallery Support**: Are image inputs equipped with direct file upload buttons with multi-file capabilities where appropriate, with non-image items rendered gracefully without broken image frames?
24. [ ] **No `type="url"` on Relative Media Storage Inputs (MISTAKE-24)**: Are all media path inputs set to `type="text"` to prevent native browser form submission rejections on valid relative storage paths?
25. [ ] **Static Call on Instance Method Prevention (MISTAKE-25)**: Are all methods called statically declared as `public static function`?
26. [ ] **Non-Swallowed Middleware Exceptions & Normalized Paths (MISTAKE-26)**: Are middleware paths parsed with `$request->getPath()` with instant preview support?
27. [ ] **Service Worker Guaranteed Response Conversion (MISTAKE-27)**: Do all `.catch()` service worker branches return valid `Response` instances?
28. [ ] **Direct PWA Asset Interception (MISTAKE-28)**: Are `manifest.json` and `sw.js` served directly with proper MIME types before framework routing?
29. [ ] **Universal Media Showcase on Single Detail Pages (MISTAKE-29)**: Are all associated media items rendered whenever available, rather than being gated by entity subtype?
30. [ ] **Unlimited Recursive Taxonomy Nesting & Anti-Cycle Guards (MISTAKE-30)**: Does category CRUD support arbitrary hierarchical depth with cycle prevention and parent pre-selection?
31. [ ] **Atomic Tip Verification & Schema Synchronization (MISTAKE-31)**: Are tip statuses synced with schema constraints (`'completed'`, `'refunded'`) and balanced atomically with ledger and notification updates?
32. [ ] **Zero-Crash Remote DB Lockout & Admin Query Optimization (MISTAKE-32)**: Are timeouts enforced, remote credentials bridged locally, setup wizard unlocked when disconnected, and listing queries stripped of `body_html`?
33. [ ] **Auto-DB Creation, Unlocked First-Run FTP Restores & Custom SQL Support (MISTAKE-33)**: Does the installer verify actual table population instead of blindly relying on `installed.lock`, auto-create the database, allow custom `.sql` dump uploads, and bypass false locks on first-time setup?
34. [ ] **Early Bootstrap Variable Initialization & Zero-Warning Cleanliness (MISTAKE-34)**: Are `$lockPath`, `$reqUri`, `$isStaticAsset`, and other router bootstrap variables declared strictly prior to usage to prevent PHP undefined variable warnings?













