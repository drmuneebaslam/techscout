# TechScout Architecture & Core Framework Documentation

This document provides complete, exhaustive technical documentation for TechScout's core framework components, initialization scripts, routing engine, security layers, database utilities, and helper modules.

---

## Table of Contents
1. [Bootstrap & System Initialization](#1-bootstrap--system-initialization)
   - [`index.php`](#indexphp)
   - [`app/autoload.php`](#appautoloadphp)
2. [Core Request Lifecycle & Routing](#2-core-request-lifecycle--routing)
   - [`App\Core\Router`](#appcorerouter)
   - [`App\Core\Request`](#appcorerequest)
   - [`App\Core\Response`](#appcoreresponse)
3. [Authentication & Session Security](#3-authentication--session-security)
   - [`App\Core\Auth`](#appcoreauth)
   - [`App\Core\Session`](#appcoresession)
4. [Database Abstraction Layer](#4-database-abstraction-layer)
   - [`App\Core\Database`](#appcoredatabase)
5. [Security & Error Defense](#5-security--error-defense)
   - [`App\Core\Firewall`](#appcorefirewall)
   - [`App\Core\Sanitizer`](#appcoresanitizer)
   - [`App\Core\ContentFilter`](#appcorecontentfilter)
   - [`App\Core\ErrorLogger`](#appcoreerrorlogger)
6. [Email Dispatch & Communication Engine](#6-email-dispatch--communication-engine)
   - [`App\Core\Mailer`](#appcoremailer)
   - [`App\Core\SmtpClient`](#appcoresmtpclient)
   - [`App\Core\WebhookEngine`](#appcorewebhookengine)
7. [System Utilities & Dynamic Engines](#7-system-utilities--dynamic-engines)
   - [`App\Core\Config`](#appcoreconfig)
   - [`App\Core\FeatureFlag`](#appcorefeatureflag)
   - [`App\Core\CredentialsManager`](#appcorecredentialsmanager)
   - [`App\Core\SeoEngine`](#appcoreseoengine)
   - [`App\Core\DocxParser`](#appcoredocxparser)
8. [Core UI & Data Helpers](#8-core-ui--data-helpers)
   - [`App\Core\BannerHelper`](#appcorebannerhelper)
   - [`App\Core\DateHelper`](#appcoredatehelper)
   - [`App\Core\NotificationHelper`](#appcorenotificationhelper)
   - [`App\Core\SocialHelper`](#appcoresocialhelper)
   - [`App\Core\SvgHelper`](#appcoresvghelper)
   - [`App\Core\TagHelper`](#appcoretaghelper)
   - [`app/Core/helpers.php`](#appcorehelpersphp)

---

## 1. Bootstrap & System Initialization

### `index.php`
- **Location**: [`index.php`](file:///c:/xampp/htdocs/techscout.n/index.php)
- **Role**: Single Point of Entry (Front Controller)
- **Functionality**:
  1. **Direct Favicon Handling**: Serves `favicon.ico` directly with caching headers (`max-age=604800`) before invoking heavy framework logic.
  2. **Class Autoloading**: Includes `app/autoload.php`.
  3. **Global Exception & Error Handling**: Registers `\App\Core\ErrorLogger::register()`.
  4. **Timezone Enforcement**: Sets timezone to `Asia/Karachi` (PST / UTC+5).
  5. **Session Initialization**: Calls `Session::start()`.
  6. **Local QA Auth Helper**: Evaluates `$_GET['qa_auth']` on `127.0.0.1` / `::1` for rapid testing (impersonating admin, author, member).
  7. **Dynamic Base URL Auto-Healing**: Resolves HTTPS state, host, and path dynamically. Checks `site_settings.domain_app_url` in the database, and automatically updates the database setting if the site was moved (e.g. from `/tech/` to root `/`).
  8. **Route Definitions**: Registers over 100+ endpoints across public pages, blog, directories, search, auth, author studio, admin panel, API, and mail tracking.
  9. **Dispatch Execution**: Calls `$router->dispatch($request)`.

---

### `app/autoload.php`
- **Location**: [`app/autoload.php`](file:///c:/xampp/htdocs/techscout.n/app/autoload.php)
- **Role**: PSR-4 Compatible Autoloader & Helper Includer
- **Functionality**:
  1. Mandates the inclusion of global functions in `app/Core/helpers.php`.
  2. Registers `spl_autoload_register` mapping namespace `App\` to directory `app/`. Converts backslashes `\` to forward slashes `/`.

---

## 2. Core Request Lifecycle & Routing

### `App\Core\Router`
- **Location**: [`app/Core/Router.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Router.php)
- **Role**: Request Routing & Route Parameter Matching Engine
- **Methods**:
  - `get(string $path, array|callable $handler)`: Registers HTTP GET route.
  - `post(string $path, array|callable $handler)`: Registers HTTP POST route.
  - `addRoute(string $method, string $path, array|callable $handler)`: Stores route pattern. Converts URI parameters (e.g., `{slug}`, `{id}`) into regular expressions.
  - `dispatch(Request $request)`: Matches current HTTP method and URI against registered route regexes. Instantiates controller classes, calls target action methods, passes captured URL parameters, or renders 404 error page on mismatch.

---

### `App\Core\Request`
- **Location**: [`app/Core/Request.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Request.php)
- **Role**: HTTP Request Wrapper
- **Methods**:
  - `getMethod()`: Returns uppercase HTTP method (`GET`, `POST`, `PUT`, `DELETE`). Respects `_method` override in POST forms.
  - `getPath()`: Extracts cleaned relative URL path stripped of query parameters.
  - `get(string $key, $default = null)`: Fetches sanitized value from `$_GET`.
  - `post(string $key, $default = null)`: Fetches sanitized value from `$_POST`.
  - `input(string $key, $default = null)`: Fetches from `$_POST` first, falling back to `$_GET`.
  - `json()`: Parses `php://input` JSON request body into PHP array.
  - `isAjax()`: Checks if request contains `X-Requested-With: XMLHttpRequest` header.

---

### `App\Core\Response`
- **Location**: [`app/Core/Response.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Response.php)
- **Role**: HTTP Response & Header Generator
- **Methods**:
  - `json(array $data, int $statusCode = 200)`: Emits `Content-Type: application/json`, sets HTTP response code, and echoes JSON-encoded payload.
  - `redirect(string $url, int $statusCode = 302)`: Sends `Location` header and terminates script execution.
  - `setStatusCode(int $code)`: Calls `http_response_code($code)`.

---

## 3. Authentication & Session Security

### `App\Core\Auth`
- **Location**: [`app/Core/Auth.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Auth.php)
- **Role**: Authentication & Role Authorization Manager
- **Methods**:
  - `check()`: Validates whether user is logged in. Verifies session token against `user_sessions` table (`session_token_hash`). Auto-logs out suspended/banned users.
  - `id()`: Returns current user ID or `null`.
  - `user()`: Returns full user record joined with author profile. Automatically creates an author profile for admins/staff if missing.
  - `role()`: Returns current user's role string (`super_admin`, `admin`, `editor`, `author`, `member`, `guest`).
  - `requireAuth()`, `requireAdmin()`, `requireAuthor()`: Middleware guards that enforce auth state or redirect to login.
  - `loginUser(array $user)`: Authenticates user session, regenerates session ID, generates session token, and records session in database.
  - `logout()`: Clears session data and revokes database session tokens.
  - `impersonate(int $userId)`, `stopImpersonating()`: Admin impersonation feature allowing admins to act as another user safely.

---

### `App\Core\Session`
- **Location**: [`app/Core/Session.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Session.php)
- **Role**: Secure PHP Session Wrapper
- **Methods**:
  - `start()`: Configures secure session cookie attributes (`httponly`, `samesite=Lax`, `secure` if HTTPS) and starts session.
  - `set(string $key, $value)`, `get(string $key, $default = null)`, `has(string $key)`, `remove(string $key)`: Key-value session store manipulations.
  - `flash(string $key, string $message)`, `getFlash(string $key)`: Single-request flash messaging system for UI notifications.
  - `csrfToken()`: Generates and returns a persistent cryptographic CSRF token.
  - `verifyCsrfToken(?string $token)`: Validates CSRF token using `hash_equals()`.

---

## 4. Database Abstraction Layer

### `App\Core\Database`
- **Location**: [`app/Core/Database.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Database.php)
- **Role**: PDO Database Connection & Query Execution Wrapper
- **Features**: Singleton PDO connection, automatic reconnection on connection drops, transactional control, table existence checks, and schema introspection.
- **Methods**:
  - `getInstance()`: Lazy-loads PDO connection using configuration options (`db_host`, `db_name`, `db_user`, `db_pass`).
  - `query(string $sql, array $params = [])`: Executes prepared PDO statements.
  - `fetch(string $sql, array $params = [])`: Returns single row as associative array.
  - `fetchAll(string $sql, array $params = [])`: Returns array of associative rows.
  - `execute(string $sql, array $params = [])`: Executes non-query statements (`INSERT`, `UPDATE`, `DELETE`). Returns affected row count.
  - `lastInsertId()`: Returns last auto-increment primary key ID.
  - `beginTransaction()`, `commit()`, `rollBack()`: Database transaction control.
  - `tableExists(string $tableName)`: Checks database schema for existence of specific table.

---

## 5. Security & Error Defense

### `App\Core\Firewall`
- **Location**: [`app/Core/Firewall.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Firewall.php)
- **Role**: Request Throttle & IP Rate Limiting Engine
- **Methods**:
  - `checkRateLimit(string $key, int $maxRequests = 60, int $decaySeconds = 60)`: Tracks request counts per IP in database/session. Blocks IPs exceeding thresholds with HTTP 429 Too Many Requests.
  - `isBannedIp(string $ip)`: Checks if client IP is present in `banned_ips` table.

---

### `App\Core\Sanitizer`
- **Location**: [`app/Core/Sanitizer.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Sanitizer.php)
- **Role**: Input Sanitization & String Normalization
- **Methods**:
  - `clean(string $data)`: Escapes special characters using `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.
  - `cleanHtml(string $html)`: Strips dangerous HTML tags (`<script>`, `<iframe>`, `onclick` attributes) while preserving safe formatting tags.
  - `slug(string $text)`: Generates URL-friendly lowercase slugs (e.g. `"Hello World!"` -> `"hello-world"`).

---

### `App\Core\ContentFilter`
- **Location**: [`app/Core/ContentFilter.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/ContentFilter.php)
- **Role**: Profanity & Spam Screening Engine
- **Methods**:
  - `containsProfanity(string $text)`: Checks text against configured profanity/spam word lists.
  - `maskProfanity(string $text)`: Replaces blacklisted keywords with asterisk masks (e.g. `****`).

---

### `App\Core\ErrorLogger`
- **Location**: [`app/Core/ErrorLogger.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/ErrorLogger.php)
- **Role**: Global Crash & Exception Handler
- **Methods**:
  - `register()`: Installs custom handlers via `set_error_handler`, `set_exception_handler`, and `register_shutdown_function`.
  - `logException(\Throwable $e)`: Formats exception details, stack traces, request URIs, and user IDs, logging them to `assets/backups/error.log` and database `error_logs` table.

---

## 6. Email Dispatch & Communication Engine

### `App\Core\Mailer`
- **Location**: [`app/Core/Mailer.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Mailer.php)
- **Role**: Multi-Transport Transactional Email Engine
- **Features**:
  - Supports Native PHP `mail()`, SMTP via `SmtpClient`, and Python HTTP Proxy fallback API (`CUSTOM_EMAIL_API_PYTHONANYWHERE`).
  - Automated HTML template compilation with custom layouts.
  - Open tracking pixel injection (`<img src="/mail/track/open?id=...">`).
  - Click URL rewriting (`/mail/track/click?id=...&url=...`).
- **Methods**:
  - `send(string $to, string $subject, string $bodyHtml, array $options = [])`: Compiles email, inserts tracking, and dispatches via active transport provider.

---

### `App\Core\SmtpClient`
- **Location**: [`app/Core/SmtpClient.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/SmtpClient.php)
- **Role**: Socket-Based SMTP Client
- **Features**: Performs raw socket communication (`fsockopen` / `stream_socket_client`) with SMTP hosts supporting TLS/SSL encryption, AUTH LOGIN, EHLO/HELO handshakes.

---

### `App\Core\WebhookEngine`
- **Location**: [`app/Core/WebhookEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/WebhookEngine.php)
- **Role**: Event-Driven Outbound Webhook Dispatcher
- **Methods**:
  - `trigger(string $event, array $payload)`: Fetches active webhooks for event from `webhooks` table, computes HMAC-SHA256 signatures using secret key, and sends cURL HTTP POST requests asynchronously.

---

## 7. System Utilities & Dynamic Engines

### `App\Core\Config`
- **Location**: [`app/Core/Config.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Config.php)
- **Role**: In-Memory & Database Setting Key-Value Store
- **Methods**:
  - `get(string $key, $default = null)`: Retrieves configuration setting.
  - `set(string $key, $value)`: Dynamically mutates setting value at runtime.

---

### `App\Core\FeatureFlag`
- **Location**: [`app/Core/FeatureFlag.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/FeatureFlag.php)
- **Role**: Dynamic Feature Toggle Engine
- **Methods**:
  - `isEnabled(string $featureKey, ?int $userId = null)`: Evaluates whether a feature is active globally or rolled out to specific user cohorts/roles.

---

### `App\Core\CredentialsManager`
- **Location**: [`app/Core/CredentialsManager.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/CredentialsManager.php)
- **Role**: Key Secret Loader
- **Methods**:
  - `get(string $key)`: Parses `credentials.txt` or environment variables to extract API keys, database passwords, SMTP credentials, and encryption secrets safely.

---

### `App\Core\SeoEngine`
- **Location**: [`app/Core/SeoEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/SeoEngine.php)
- **Role**: Meta Tags, OpenGraph & JSON-LD Generator
- **Methods**:
  - `generateTags(array $metadata)`: Builds meta title, description, canonical URL, OpenGraph tags, and Twitter Card tags.
  - `generateJsonLd(string $type, array $data)`: Generates Schema.org JSON-LD scripts (`Article`, `SoftwareApplication`, `JobPosting`, `BreadcrumbList`).

---

### `App\Core\DocxParser`
- **Location**: [`app/Core/DocxParser.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/DocxParser.php)
- **Role**: Word Document Parser
- **Methods**:
  - `parseToHtml(string $filepath)`: Unzips `.docx` binary files, reads internal `word/document.xml`, extracts headings, paragraphs, lists, and images, converting them into clean HTML.

---

## 8. Core UI & Data Helpers

- **`BannerHelper`** (`app/Core/BannerHelper.php`): Renders active promotional banners and announcement bars.
- **`DateHelper`** (`app/Core/DateHelper.php`): Human-readable relative time strings (e.g. `"2 hours ago"`) formatted in Pakistani Standard Time.
- **`NotificationHelper`** (`app/Core/NotificationHelper.php`): Dispatches real-time in-app alerts and tracks unread count badges.
- **`SocialHelper`** (`app/Core/SocialHelper.php`): Builds social sharing URLs (Twitter/X, LinkedIn, Facebook, WhatsApp, Reddit).
- **`SvgHelper`** (`app/Core/SvgHelper.php`): Inline SVG icon catalog renderer.
- **`TagHelper`** (`app/Core/TagHelper.php`): Normalizes and parses comma-separated tags for content items.
- **`helpers.php`** (`app/Core/helpers.php`): Global convenience functions:
  - `e($string)`: Safe HTML escaping wrapper.
  - `url($path)`: Prefixes site base URL.
  - `asset($path)`: Generates cache-busted static asset URL.
  - `auth()`: Shorthand for `App\Core\Auth::user()`.
  - `session($key, $default)`: Shorthand for session value retrieval.
  - `old($key, $default)`: Form input sticky state recovery.
