# TechScout Comprehensive Functions List

This document provides a highly detailed, hand-analyzed overview of the core architectural functions, methods, and routines powering the TechScout platform based on codebase and commit history.

## 1. App\Core\Database
* `public static function getConnection(): \PDO`
  - Initializes the single PDO instance using credentials dynamically resolved via `Config::getEnvironment()`. Enforces UTF8MB4 charset and throws PDOExceptions.
* `public static function query(string $sql, array $params = []): array`
  - Prepares and executes standard `SELECT` statements, returning an associative array of results. Defends against SQL injection via parameterized bindings.
* `public static function execute(string $sql, array $params = []): bool`
  - Prepares and executes DML (`INSERT`, `UPDATE`, `DELETE`) operations, returning a boolean success state.
* `public static function executeSilent(string $sql, array $params = []): bool`
  - Executes telemetry/synthetic data inserts (e.g., `post_views`) and suppresses errors to avoid crashing user requests.

## 2. App\Core\Config
* `public static function getEnvironment(): string`
  - Inspects `__DIR__`, `HTTP_HOST`, and `APP_ENV` to ascertain if the runtime is Local, InfinityFree (`free.nf`), or AWS EC2 (`techscout.online`).
* `public static function detectBaseUrl(): string`
  - Evaluates `HTTPS`, `HTTP_X_FORWARDED_PROTO`, and Cloudflare headers to dynamically construct the application's root URL.
* `public static function url(string $path = ''): string`
  - Generates absolute URLs, preventing double-slashes and routing internal links accurately.

## 3. App\Core\PopupManager
* `public static function renderActivePopup(): string`
  - Scans `custom_popups` for active modals. Evaluates strict datetime bounds (`start_date`, `end_date`) accounting for MySQL 8 strict mode to avoid `1525 Incorrect DATETIME value` crashes. Outputs raw HTML injected into `footer.php`.

## 4. App\Core\NotificationHelper
* `public static function getUnreadCount(int $userId): int`
  - Performs a lightweight `COUNT(*)` query on the notifications table for the header badge.
* `public static function create(int $userId, string $type, string $message, string $url): bool`
  - Dispatches an internal system notification alert to a specific user.

## 5. App\Controllers\AuthController
* `public function login()`
  - Validates `$_POST` credentials against hashed passwords in the `users` table. Rejects unverified accounts. Logs successful IPs.
* `public function register()`
  - Creates a new user record. Enforces unique email constraints. Fires the `user.registered` event trigger which queues the OTP verification email.
* `public function verifyEmail()`
  - Compares submitted OTPs against the `verification_tokens` table. Upgrades user status from `pending` to `active`.

## 6. App\Controllers\AdminController
* `public function dashboard()`
  - Aggregates system metrics, unread applications, and telemetry data for the main Admin Hub overview.
* `public function deployPull()`
  - Triggered via `/admin/system/deploy-pull`. Initiates the EC2 shell execution (`ec2_deploy_and_migrate.sh`) to sync GitHub `origin/main` and run migrations.
* `public function resolveSystemLog(int $id)`
  - Marks an error in the forensics database as resolved.

## 7. App\Models\Content
* `public static function getActivePrompts(int $limit = 10, int $offset = 0): array`
  - Executes a complex `LEFT JOIN` linking `contents`, `categories`, `users` (authors), and `prompts`. Explicitly maps columns while omitting deprecated fields like `p.aspect_ratio`.
* `public static function getFeed(string $type): array`
  - Specifically designed for the `DirectoryController` and RSS XML exporters. Retrieves partitioned content (Tools vs. Image Prompts) adhering to strict entity separation.

## 8. App\Controllers\AuthorStudioController
* `public function dashboard()`
  - Renders the dual-theme enabled Creator Studio. Aggregates the author's published content, pending reviews, and tips.
* `public function saveTipWallets()`
  - Sanitizes and updates the fixed crypto wallet configuration in the `author_profiles` table, enabling the 95/5 patronage split.
* `public function importDocx()`
  - Accepts `.docx` uploads, parses XML structure, and converts to draft markdown for the multi-tab modal editor.

## 9. App\Core\MailEngine
* `public static function dispatch(string $to, string $templateId, array $variables = [])`
  - Fetches a visual template from the database, runs the 10-category variable engine parser, wraps the content in the Universal Mail Envelope, and fires SMTP execution.
* `public static function logDelivery(int $templateId, string $status, string $response)`
  - Writes success/failure heuristics into `mail_logs` ensuring the auto-healing schema handles potential JSON constraint errors.

## 10. Frontend JavaScript Engines (App/Public)
* `toggleTheme()`
  - Uses debounce re-entrancy guards to prevent rapid double-clicks from inverting the dark/light dual-theme logic asynchronously.
* `syncAdminThemeIcon()`
  - Re-binds icon attributes cleanly after PJAX / SPA navigation events across the admin console.
* `initVisualLinkScanner()`
  - Scans DOM elements containing `.comment-body` and flags potentially malicious domains via regex heuristics.

---
*(Analysis compiled from comprehensive system review and TechScout Git history logs)*
