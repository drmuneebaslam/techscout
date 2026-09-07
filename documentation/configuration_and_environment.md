# TechScout Configuration & Environment Setup Guide

This document details server configuration, `.htaccess` rules, environment variables, dynamic URL self-healing, email proxy setups, and credentials management.

---

## 1. Web Server & `.htaccess` Configuration

TechScout uses Apache `mod_rewrite` to route all non-static asset requests to `index.php`.

### `.htaccess` Security & Optimization Rules
Located at [`.htaccess`](file:///c:/xampp/htdocs/techscout.n/.htaccess):

1. **Security & Protection Headers**:
   - `X-Content-Type-Options "nosniff"`: Prevents MIME-type sniffing attacks.
   - `X-Frame-Options "SAMEORIGIN"`: Prevents clickjacking by restricting framing to same origin.
   - `X-XSS-Protection "1; mode=block"`: Enforces browser XSS filter.
   - `Referrer-Policy "strict-origin-when-cross-origin"`: Protects sensitive referrer strings.

2. **Directory Protection Rules**:
   - `RewriteRule ^(app|database|docs|waste)/ - [F,L,NC]`: Blocks direct HTTP client access to core codebase directories (`403 Forbidden`).

3. **Front Controller Rewriting**:
   - `RewriteCond %{REQUEST_FILENAME} !-f`
   - `RewriteCond %{REQUEST_FILENAME} !-d`
   - `RewriteRule ^(.*)$ index.php [QSA,L]`: Routes all virtual paths to `index.php`.

4. **Gzip & Deflate Asset Compression**:
   - Compresses HTML, CSS, JS, JSON, XML, and SVG outputs for high-speed page loads.

5. **Browser Cache Control**:
   - Static images (`.png`, `.jpg`, `.svg`, `.webp`, `.ico`) cached for 1 month.
   - Stylesheets (`.css`) and JavaScript (`.js`) cached for 1 week.

---

## 2. Dynamic Base URL Resolution & Self-Healing

TechScout automatically detects its current host environment in `index.php` without requiring manual `.env` edits when deploying across different environments (e.g. `http://localhost/techscout.n`, staging servers, or production domains).

### Resolution Algorithm:
1. Determines HTTP vs HTTPS protocol checking `HTTPS`, `HTTP_X_FORWARDED_PROTO`, `HTTP_X_FORWARDED_SSL`, and Cloudflare headers (`HTTP_CF_VISITOR`).
2. Extracts hostname (`$_SERVER['HTTP_HOST']`) and script directory (`$_SERVER['SCRIPT_NAME']`).
3. Queries `site_settings` database table for `domain_app_url`.
4. **Self-Healing Mechanics**: If the host matches but the deployment path changed (e.g., folder renamed or moved from `/tech/` to root `/`), the application automatically updates `site_settings.domain_app_url` and repairs notification URLs in the database automatically.

---

## 3. Email & SMTP / Proxy Architecture

TechScout supports a dual-transport mail system capable of running in restrictive shared hosting environments (such as InfinityFree where outbound SMTP port 587/465 is blocked):

```
                        +----------------------------+
                        |     App\Core\Mailer        |
                        +--------------+-------------+
                                       |
                   +-------------------+-------------------+
                   |                                       |
                   v                                       v
        +---------------------+                 +---------------------+
        | App\Core\SmtpClient |                 | PythonAnywhere HTTP |
        |  (Direct SMTP Socket|                 | Mail Proxy Endpoint |
        +---------------------+                 +---------------------+
```

### Mail Capabilities:
- **Direct SMTP**: Connects to SMTP servers using standard socket connections with TLS/SSL encryption.
- **PythonAnywhere Proxy Fallback**: Dispatches mail payloads via encrypted HTTPS POST to an external Python microservice when local SMTP sockets are blocked by cloud providers.
- **Pixel & Click Tracking**: Every outgoing mail automatically injects open pixel tags (`/mail/track/open`) and rewrites anchor links (`/mail/track/click`).

---

## 4. Environment Credentials Management

- **Database Credentials**: Configured in `app/Core/Config.php` or `site_settings` table. Default local settings:
  - Host: `127.0.0.1` / `localhost`
  - Database: `techscout_db`
  - User: `root`
  - Password: `""` (Empty string in standard XAMPP environment)
- **Account Credentials File**: [`credentials.txt`](file:///c:/xampp/htdocs/techscout.n/credentials.txt) tracks active system accounts and role assignments for administrative reference.
