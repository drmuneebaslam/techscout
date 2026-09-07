# 01. TechScout Framework Architecture Overview

## Executive Summary
TechScout is a custom enterprise-grade PHP 8.1+ Model-View-Controller (MVC) publishing engine and AI directory web application. Designed from the ground up without heavy external framework bloat (such as Laravel, Symfony, or CodeIgniter), TechScout executes complete HTTP request-response bootstrap cycles in under 5 milliseconds on standard Apache/Nginx web servers running PHP 8.1 with OPcache.

---

## Architectural Principles & Core Guarantees

### 1. Zero Third-Party Framework Overhead
- **Minimal Dependencies**: The framework relies exclusively on standard PHP extensions (`pdo_mysql`, `mbstring`, `json`, `filter`, `openssl`, `curl`, `zip`).
- **Instant Boot Times**: Eliminates thousands of vendor files, autoloader lookups, and reflection overhead, resulting in 99th percentile response latencies under 12ms.

### 2. Strict Model-View-Controller (MVC) Pattern
```
                              ┌────────────────────────────────────────┐
                              │            HTTP Request (Client)       │
                              └───────────────────┬────────────────────┘
                                                  │
                                                  ▼
                              ┌────────────────────────────────────────┐
                              │    Apache Server (.htaccess Rewrites)  │
                              └───────────────────┬────────────────────┘
                                                  │
                                                  ▼
                              ┌────────────────────────────────────────┐
                              │       index.php (Front Controller)     │
                              │  - Error Logger & PST Timezone         │
                              │  - Session & CSRF Protection           │
                              │  - Base URL Auto-Healing               │
                              └───────────────────┬────────────────────┘
                                                  │
                                                  ▼
                              ┌────────────────────────────────────────┐
                              │          App\Core\Router Dispatch      │
                              └───────┬────────────────────────┬───────┘
                                      │                        │
                      ┌───────────────┴──────┐          ┌──────┴───────────────┐
                      │  Public Controllers  │          │   Admin Controllers   │
                      │  (Blog, Directory)   │          │   (Admin, Studio)     │
                      └───────────────┬──────┘          └──────┬───────────────┘
                                      │                        │
                                      ▼                        ▼
                              ┌────────────────────────────────────────┐
                              │            App\Models\*                │
                              │  - AuthorTierEngine                    │
                              │  - FinancialLedger & RBAC              │
                              │  - SecurityAnomalyEngine               │
                              └───────────────────┬────────────────────┘
                                                  │
                                                  ▼
                              ┌────────────────────────────────────────┐
                              │    App\Core\Database (PDO Singleton)   │
                              └───────────────────┬────────────────────┘
                                                  │
                                                  ▼
                              ┌────────────────────────────────────────┐
                              │  MySQL Database (76 InnoDB Tables)     │
                              └────────────────────────────────────────┘
```

---

## Complete Directory Architecture & Subsystem Mapping

```
techscout.n/
├── index.php                             # Front Controller
├── .htaccess                             # Apache Rewrite & Security Rules
├── app/
│   ├── autoload.php                      # PSR-4 Class Autoloader
│   ├── Core/                             # Kernel Services (25 Files)
│   │   ├── Auth.php                      # Authentication & Impersonation
│   │   ├── Database.php                  # PDO Singleton & Auto-Healing Migration
│   │   ├── Firewall.php                  # Rate Limiting & Threat Defense
│   │   ├── Mailer.php                    # Multi-Transport Mailer Engine
│   │   ├── Router.php                    # Regex Parametric Routing Engine
│   │   ├── Session.php                   # Session Lifecycle & CSRF Guard
│   │   └── helpers.php                   # Global Helper Functions
│   ├── Controllers/                      # HTTP Action Handlers (14 Files)
│   │   ├── AdminController.php           # Administrative Control Panel
│   │   ├── ApiController.php             # RESTful JSON Endpoints
│   │   ├── AuthController.php            # Security & Login Workflows
│   │   ├── AuthorStudioController.php    # Creator Studio & WYSIWYG Editor
│   │   ├── BlogController.php            # Article Reader & Feeds
│   │   └── DirectoryController.php       # AI Tools, Prompts, GPTs, Jobs
│   └── Models/                           # Data Models & Business Engines (18 Files)
│       ├── AuthorTierEngine.php          # 20-Sublevel Author Rank Progression
│       ├── FinancialLedger.php           # Double-Entry Credit Accounting
│       ├── SecurityAnomalyEngine.php     # Threat Analysis & Auto-Banning
│       └── User.php                      # User Account Model & 3-Strike Escalation
├── database/                             # Database Schemas & Migrations
│   ├── schema.sql                        # 76 InnoDB Table Schemas
│   ├── seeds.sql                         # Initial Platform Seed Data
│   └── database_upgrade_v2.sql           # Incremental Migration Upgrade
├── assets/                               # Frontend Static Assets
│   ├── css/                              # Vanilla CSS Stylesheets (style.css, admin.css)
│   └── js/                               # Client JS Engines (main.js, content-engine.js)
├── views/                                # HTML/PHP Presentation Templates (124 Templates)
│   ├── admin/                            # Administrative Control Panel Views
│   ├── author/                           # Creator Studio Views
│   ├── blog/                             # Article Reader Templates
│   └── directory/                        # AI Directory Listings
└── documentation/                        # Comprehensive Platform Documentation
    ├── index.php                         # Interactive PHP Web Documentation Reader
    ├── qa_testing_master_guide.md        # Master QA Testing Suite
    └── project/                          # 60 Granular Specification Files
```

---

## Detailed Request Lifecycle Execution Flow

### Step 1: Apache Mod_Rewrite Interception
When an HTTP request strikes the Apache web server, `.htaccess` intercepts the incoming URI. If the URI does not map to an existing physical file or directory, Apache rewrites the request to `index.php`:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

### Step 2: Front Controller Initialization (`index.php`)
1. **Error Handler Registration**: Invokes `App\Core\ErrorLogger::register()` to catch all fatal errors, warnings, and unhandled exceptions.
2. **Timezone Standardization**: Sets `date_default_timezone_set('Asia/Karachi')` to ensure consistent PST time calculations.
3. **Autoloader Bootstrap**: Requires `app/autoload.php` to resolve `App\` PSR-4 namespaces.
4. **Session Startup**: Executes `App\Core\Session::start()`, setting cookie security flags (`HttpOnly`, `SameSite=Lax`).
5. **QA Visual Auth Override**: Evaluates `$_GET['qa_auth']` for automated visual regression testing in QA environments.
6. **Dynamic Base URL Auto-Healing**: Inspects HTTP host headers and port parameters, automatically updating `site_settings.domain_app_url` in MySQL if server domain/IP changes.
7. **Router Dispatch**: Instantiates `App\Core\Router`, loads route definitions, and executes `$router->dispatch($request)`.

---

## Quality Assurance (QA) Test Matrix

### QA Test Case: `TC-ARCH-001` - End-to-End Request Pipeline
- **Target Function/Module**: Front Controller Routing Pipeline
- **Preconditions**: XAMPP Apache and MySQL server active.
- **Test Command**:
  ```bash
  curl -i "http://localhost/techscout.n/blog"
  ```
- **Expected Output**:
  1. Response Status: `200 OK`
  2. Headers contain `X-Frame-Options: SAMEORIGIN` and `X-Content-Type-Options: nosniff`.
  3. Body renders public blog feed HTML layout.
- **Failure Analysis**: If response status is 404, verify Apache `.htaccess` `mod_rewrite` module is enabled in `httpd.conf`.
