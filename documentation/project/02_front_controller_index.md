# 02. Front Controller & Bootstrapping (`index.php`)

## File Location & Metadata
- **File Path**: [`index.php`](file:///c:/xampp/htdocs/techscout.n/index.php)
- **Primary Role**: Single Front Controller entry point for all application HTTP requests.
- **Dependencies**: [`app/autoload.php`](file:///c:/xampp/htdocs/techscout.n/app/autoload.php), [`app/Core/ErrorLogger.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/ErrorLogger.php), [`app/Core/Session.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Session.php), [`app/Core/Router.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Router.php), [`app/Core/Database.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Database.php).

---

## Detailed Bootstrap Sequence

```
index.php Execution Order
┌─────────────────────────────────────────────────────────┐
│ 1. ErrorLogger::register() - Global Exception Handler   │
├─────────────────────────────────────────────────────────┤
│ 2. date_default_timezone_set('Asia/Karachi') (PST)      │
├─────────────────────────────────────────────────────────┤
│ 3. require_once 'app/autoload.php' (PSR-4 Autoloader)   │
├─────────────────────────────────────────────────────────┤
│ 4. Session::start() - Secure Cookie & Session Boot      │
├─────────────────────────────────────────────────────────┤
│ 5. QA Auth Visual Testing Helper ($_GET['qa_auth'])     │
├─────────────────────────────────────────────────────────┤
│ 6. Dynamic Base URL Self-Healing Engine                 │
├─────────────────────────────────────────────────────────┤
│ 7. Route Registration (80+ Routes Registered)           │
├─────────────────────────────────────────────────────────┤
│ 8. $router->dispatch($request) Execution                │
└─────────────────────────────────────────────────────────┘
```

---

## Technical Deep Dive into Key Components

### 1. Global Error Logging Registration
```php
use App\Core\ErrorLogger;
use App\Core\Session;
use App\Core\Router;
use App\Core\Request;
use App\Core\Database;

ErrorLogger::register();
```
`ErrorLogger::register()` intercepts PHP errors (`E_ERROR`, `E_WARNING`, `E_NOTICE`), uncaught exceptions, and fatal shutdown events, converting them into structured log entries written to both file storage (`logs/app.log`) and database table `system_logs`.

### 2. Timezone Standardized Enforcement
```php
date_default_timezone_set('Asia/Karachi');
```
Sets application timezone to Pakistan Standard Time (PST, UTC+5). This guarantees that timestamps stored in database queries (`NOW()`, `created_at`, `updated_at`) strictly match application business logic across all server operating systems (Windows, Linux, macOS).

### 3. Session Initialization & Security Flags
```php
Session::start();
```
Initializes PHP session engine with forced security parameters:
- `session.cookie_httponly = 1`: Prevents JavaScript `document.cookie` access to session IDs, eliminating Session Hijacking via XSS.
- `session.cookie_samesite = 'Lax'`: Mitigates Cross-Site Request Forgery (CSRF) attacks.
- `session.use_only_cookies = 1`: Disallows URL session ID passing.

### 4. Dynamic Base URL Auto-Healing Algorithm
```php
$scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$scriptDir = rtrim(str_replace('\\', '/', $scriptDir), '/');
$currentAppUrl = $scheme . '://' . $host . $scriptDir;

$db = Database::getInstance();
$storedUrl = $db->fetch("SELECT setting_value FROM site_settings WHERE setting_key = 'domain_app_url'");

if (!$storedUrl || $storedUrl['setting_value'] !== $currentAppUrl) {
    $db->execute(
        "INSERT INTO site_settings (setting_key, setting_value) VALUES ('domain_app_url', ?) 
         ON DUPLICATE KEY UPDATE setting_value = ?",
        [$currentAppUrl, $currentAppUrl]
    );
}
```
**Why this matters**: In local development (XAMPP/WAMP), developers frequently change ports (`localhost`, `localhost:8080`, `127.0.0.1`, IP addresses). The self-healing algorithm dynamically detects environment changes and updates MySQL config tables automatically without manual configuration edits.

---

## Comprehensive Route Registration Catalog

| Route Pattern | HTTP Method | Target Controller & Action | Required Authorization |
| :--- | :--- | :--- | :--- |
| `/` | `GET` | `HomeController@index` | Public Guest |
| `/blog` | `GET` | `BlogController@index` | Public Guest |
| `/blog/{slug}` | `GET` | `BlogController@show` | Public Guest |
| `/directory/tools` | `GET` | `DirectoryController@tools` | Public Guest |
| `/directory/prompts` | `GET` | `DirectoryController@prompts` | Public Guest |
| `/directory/gpts` | `GET` | `DirectoryController@gpts` | Public Guest |
| `/go/{slug}` | `GET` | `DirectoryController@affiliateRedirect` | Public Guest |
| `/login` | `GET`, `POST` | `AuthController@login` | Guest Only |
| `/register` | `GET`, `POST` | `AuthController@register` | Guest Only |
| `/author/studio` | `GET` | `AuthorStudioController@dashboard` | Verified Author Role |
| `/author/studio/editor` | `GET`, `POST` | `AuthorStudioController@editor` | Verified Author Role |
| `/admin` | `GET` | `AdminController@dashboard` | Super Admin / Admin Role |
| `/admin/users` | `GET`, `POST` | `AdminController@users` | Super Admin / Admin Role |
| `/admin/content-queue`| `GET`, `POST` | `AdminController@articles` | Staff Editor / Admin Role |
| `/admin/revenue` | `GET`, `POST` | `AdminController@financialLedger` | Super Admin Role |
| `/admin/firewall` | `GET`, `POST` | `AdminController@firewall` | Super Admin Role |

---

## Detailed QA Test Suite for Front Controller

### Test Case 1: Dynamic Base URL Auto-Healing Verification
- **QA Test ID**: `TC-BOOT-001`
- **Feature Target**: Base URL self-healing in `index.php`.
- **Preconditions**: Database running on XAMPP.
- **Execution Steps**:
  1. Open MySQL database table `site_settings`.
  2. Set `domain_app_url` to `http://invalid-old-domain.com`.
  3. Load `http://localhost/techscout.n/index.php` in browser.
  4. Query `site_settings` again:
     ```sql
     SELECT setting_value FROM site_settings WHERE setting_key = 'domain_app_url';
     ```
- **Expected Result**: `setting_value` updates to `http://localhost/techscout.n`.
- **Assertion**: `setting_value == http://localhost/techscout.n`.

### Test Case 2: Uncaught Exception Error Logging
- **QA Test ID**: `TC-BOOT-002`
- **Feature Target**: `ErrorLogger::register()` exception trapping.
- **Execution Steps**:
  1. Temporarily trigger an unhandled Exception in a controller action.
  2. Load route in browser.
- **Expected Result**: Renders clean 500 Error template (`views/errors/500.php`) without exposing DB passwords or secret keys, writing stack trace to `system_logs`.
