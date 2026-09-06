# 🚀 InfinityFree Hosting Deployment Guide for TechScout

This comprehensive, step-by-step guide explains how to deploy the complete **TechScout** platform to **InfinityFree Free Hosting** (or any cPanel/vPanel shared LAMP stack).

---

## 📋 Table of Contents
1. [Prerequisites & Account Creation](#1-prerequisites--account-creation)
2. [Database Migration (Export & Import)](#2-database-migration-export--import)
3. [File Uploading via FTP](#3-file-uploading-via-ftp)
4. [Configuring Environment & Database Credentials](#4-configuring-environment--database-credentials)
5. [Configuring URL Rewriting & `.htaccess`](#5-configuring-url-rewriting--htaccess)
6. [Mailing Service Setup (Overcoming InfinityFree Mail Restrictions)](#6-mailing-service-setup-overcoming-infinityfree-mail-restrictions)
7. [Directory Permissions & Verification](#7-directory-permissions--verification)
8. [Troubleshooting Common Issues](#8-troubleshooting-common-issues)

---

## 1. Prerequisites & Account Creation

1. **Sign Up for InfinityFree**:
   - Go to [https://www.infinityfree.com](https://www.infinityfree.com) and register for a free account.
2. **Create a Hosting Account**:
   - In the InfinityFree Client Area, click **"Create Account"**.
   - Choose a domain:
     - **Free Subdomain**: e.g., `techscout.great-site.net` or `techscout.rf.gd`.
     - **Custom Domain**: Enter your custom domain (point your domain's nameservers to `ns1.epizy.com` and `ns2.epizy.com`).
3. **Note Your Credentials**:
   - Once the account is created, go to the account details page and record:
     - **FTP Hostname**: (e.g., `ftpupload.net`)
     - **FTP Username**: (e.g., `epiz_34567890`)
     - **FTP Password**: (click Show / Copy Password)
     - **MySQL Hostname**: (e.g., `sql200.infinityfree.com` or `sqlXXX.epizy.com`)
     - **MySQL Username**: (e.g., `epiz_34567890`)
     - **MySQL Password**: (same as FTP password)

---

## 2. Database Migration (Export & Import)

### A. Export Your Local Database
1. Open your local XAMPP control panel and ensure MySQL is running.
2. Open your browser and go to `http://localhost/phpmyadmin/`.
3. Select the `techscout_db` database on the left sidebar.
4. Click the **Export** tab in the top navigation.
5. Choose **Custom - display all possible options**:
   - Tables: Ensure **all tables** are checked.
   - Output: Save output to a file (`techscout_export.sql`).
   - Format: **SQL**.
   - Object creation options: Check `Add DROP TABLE / VIEW / PROCEDURE / FUNCTION / EVENT / TRIGGER statement`.
6. Click **Export** to download the `.sql` file to your computer.

### B. Create Database on InfinityFree
1. Log into your InfinityFree Control Panel (cPanel/vPanel).
2. Under the **Databases** section, click **MySQL Databases**.
3. In **Create a New Database**, enter a database name suffix (e.g., `techscout` or `db`).
4. Click **Create Database**.
5. Your full database name will be generated with your account prefix, for example: `epiz_34567890_techscout`.

### C. Import SQL into InfinityFree
1. Back on the **MySQL Databases** page, find your newly created database in the list.
2. Click the **phpMyAdmin** button next to your database.
3. Once phpMyAdmin opens, select your database from the left menu (`epiz_34567890_techscout`).
4. Click the **Import** tab at the top.
5. Click **Choose File** and select your exported `techscout_export.sql`.
6. Set format to **SQL** and click **Import** (or **Go**) at the bottom.
7. Verify that all 26+ tables (such as `users`, `contents`, `support_tickets`, `support_categories`, `user_sessions`, etc.) are listed.

---

## 3. File Uploading via FTP

InfinityFree does not allow uploading large zip files through the file manager; therefore, using **FileZilla** FTP client is the fastest and most reliable method.

1. Download and install [FileZilla Client](https://filezilla-project.org/).
2. Open FileZilla and enter your connection details:
   - **Host**: `ftpupload.net` (or your assigned FTP host from InfinityFree)
   - **Username**: `epiz_34567890`
   - **Password**: Your InfinityFree hosting password
   - **Port**: `21`
3. Click **Quickconnect**.
4. In the **Remote site** window (right pane), navigate inside the **`htdocs/`** directory.
   > ⚠️ **IMPORTANT**: Delete any default files inside `htdocs/` (such as `index2.html` or `default.php`). All TechScout files MUST be uploaded inside `htdocs/`.
5. In the **Local site** window (left pane), navigate to your local project folder:
   `c:\xampp\htdocs\techscout.n\`
6. Select all files and folders:
   - `app/`
   - `assets/`
   - `database/`
   - `docs/`
   - `views/`
   - `.htaccess`
   - `index.php`
   - `robots.txt`
   *(You can exclude `.git/`, `.agents/`, and local temporary test files).*
7. Right-click and select **Upload**.
8. Wait for the upload queue to complete. Check the **Failed transfers** tab at the bottom; if any files failed, right-click and choose **Reset and requeue all**.

---

## 4. Configuring Environment & Database Credentials

After uploading the files, update `app/Core/Config.php` with your InfinityFree MySQL credentials:

You can edit this file via FTP or through the InfinityFree Online File Manager:
1. Open `app/Core/Config.php`.
2. Locate the `$settings` array (around lines 5–16):

```php
    public static array $settings = [
        'app_name' => 'TechScout',
        'app_tagline' => 'The Frontier of Tech, Scouted for Builders.',
        'app_url' => 'https://your-domain.infinityfreeapp.com', // Replace with your InfinityFree domain
        'db_host' => 'sql200.infinityfree.com',                // Replace with your MySQL Hostname
        'db_port' => '3306',
        'db_name' => 'epiz_34567890_techscout',               // Replace with your full database name
        'db_user' => 'epiz_34567890',                         // Replace with your MySQL Username
        'db_pass' => 'YOUR_INFINITYFREE_PASSWORD',             // Replace with your hosting password
        'session_lifetime' => 86400 * 7,
        'environment' => 'production'                          // Set to production
    ];
```

> 💡 **Dynamic Base URL**: TechScout's `index.php` also auto-detects `$_SERVER['HTTP_HOST']` and protocol dynamically, ensuring all assets and routes seamlessly adapt to your domain without broken links.

---

## 5. Configuring URL Rewriting & `.htaccess`

InfinityFree uses Apache with `mod_rewrite` enabled. For proper routing to work at the domain root:

1. Open `.htaccess` located in the root of `htdocs/`.
2. Ensure `RewriteBase` is set to `/` (not `/techscout.n/`):

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /

    # Security & Performance Headers
    <IfModule mod_headers.c>
        Header set X-Content-Type-Options "nosniff"
        Header set X-Frame-Options "SAMEORIGIN"
        Header set X-XSS-Protection "1; mode=block"
        Header set Referrer-Policy "strict-origin-when-cross-origin"
    </IfModule>

    # Disable direct access to sensitive directories
    RewriteRule ^(app|database|docs|scratch)/ - [F,L,NC]

    # Route all requests to index.php if file/dir does not exist
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>

# Gzip & Deflate Compression for High-Speed Asset Delivery
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE text/javascript
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
    AddOutputFilterByType DEFLATE application/json
    AddOutputFilterByType DEFLATE image/svg+xml
</IfModule>

# Browser Caching Headers for Static Assets
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/webp "access plus 1 month"
    ExpiresByType image/svg+xml "access plus 1 month"
    ExpiresByType text/css "access plus 1 week"
    ExpiresByType application/javascript "access plus 1 week"
    ExpiresByType application/x-javascript "access plus 1 week"
    ExpiresByType font/woff2 "access plus 1 month"
    ExpiresByType font/woff "access plus 1 month"
</IfModule>
```

3. Save the `.htaccess` file.

---

## 6. Mailing Service Setup (Overcoming InfinityFree Mail Restrictions)

> ⚠️ **CRITICAL HOSTING NOTE**:
> InfinityFree **disables the native PHP `mail()` function** and blocks outgoing connections on port 25 to prevent spam abuse. 
> Direct localhost mail will fail. You must use an **external SMTP provider** (such as Brevo/Sendinblue, SendGrid, Mailgun, or Gmail App Password) operating on port **587 (TLS)** or port **465 (SSL)**, or a webhook-based REST mail microservice (such as PythonAnywhere).

### Recommended: Brevo (Sendinblue) or SendGrid (100% Free)
1. Sign up for a free account at [Brevo.com](https://www.brevo.com/) (300 free emails/day).
2. Go to **SMTP & API** settings and retrieve:
   - **SMTP Server**: `smtp-relay.brevo.com`
   - **SMTP Port**: `587`
   - **SMTP Login**: `your-account-email@example.com`
   - **SMTP Master Key / Password**: `xsmtpib-xxxxxxxxxxxxxxxxxxxx`
3. Configure these credentials in TechScout:
   - Option A: Log in to your TechScout Admin Dashboard (`/admin/settings` or `/admin/mail-settings`) and enter the SMTP credentials.
   - Option B: Insert directly into the `site_settings` database table:
     ```sql
     REPLACE INTO site_settings (key_name, key_value) VALUES 
     ('smtp_host', 'smtp-relay.brevo.com'),
     ('smtp_port', '587'),
     ('smtp_user', 'your-account-email@example.com'),
     ('smtp_pass', 'xsmtpib-xxxxxxxxxxxxxxxxxxxx'),
     ('smtp_secure', 'tls'),
     ('mail_from_address', 'support@yourdomain.com'),
     ('mail_from_name', 'TechScout Support');
     ```

TechScout's built-in `App\Core\Mailer` engine will now automatically route password resets, email verifications, and notification dispatches over TLS without exposing reset tokens on the frontend.

---

## 7. Directory Permissions & Verification

1. Ensure the upload directories are writable:
   - `assets/uploads/`
   - `assets/uploads/avatars/`
   - `assets/uploads/covers/`
   - Set file permissions to `755` (or `777` if required by the host) via FileZilla right-click $\rightarrow$ **File permissions**.
2. Visit your domain:
   - `https://your-domain.infinityfreeapp.com/`
   - Verify that the homepage loads with CSS, icons, and theme switcher functioning.
3. Test key workflows:
   - **Login / Register**: `https://your-domain.infinityfreeapp.com/login`
   - **Technical Blog**: `https://your-domain.infinityfreeapp.com/blog` (test search, category, reading time, and sorting filters)
   - **Tools Directory**: `https://your-domain.infinityfreeapp.com/tools` (test pricing and rating sorting)
   - **Prompts & GPTs Hub**: `https://your-domain.infinityfreeapp.com/prompts` and `/gpts`
   - **Developer Campaigns**: `https://your-domain.infinityfreeapp.com/campaigns`
   - **Support Desk**: `https://your-domain.infinityfreeapp.com/support` (verify cascading category selection and ticket submission)
   - **Admin Management**: `https://your-domain.infinityfreeapp.com/admin/support/categories`

---

## 8. Troubleshooting Common Issues

### Issue 1: 500 Internal Server Error
- **Cause**: Typo in `.htaccess` or invalid MySQL credentials.
- **Fix**: Check `app/Core/Config.php` database settings. Ensure `.htaccess` contains `RewriteBase /` and that no syntax errors exist. Check `error_log` in the file manager.

### Issue 2: Broken Images or 404 on Sub-pages (e.g. `/blog` or `/login`)
- **Cause**: `mod_rewrite` rules not triggering or incorrect `RewriteBase`.
- **Fix**: Open `.htaccess` and confirm `RewriteEngine On` and `RewriteRule ^(.*)$ index.php [QSA,L]` are present. Ensure the file is named `.htaccess` (with the leading dot).

### Issue 3: Database Connection Error (`Access denied for user...`)
- **Cause**: InfinityFree prefixes database names and usernames with `epiz_XXXXXXX`.
- **Fix**: In `app/Core/Config.php`, ensure `db_name` is the full name (e.g. `epiz_34567890_techscout`), not just `techscout`. Do NOT use `localhost` for `db_host` on InfinityFree—use the exact hostname shown in your panel (e.g. `sql200.infinityfree.com`).

### Issue 4: Emails Not Arriving (Password Reset / Verification)
- **Cause**: InfinityFree blocks PHP `mail()`.
- **Fix**: Configure external SMTP (Brevo or SendGrid) on port 587 in `site_settings`. TechScout uses authenticated SMTP, guaranteeing deliverability.

---

🎉 **Congratulations!** Your TechScout platform is now deployed and running on InfinityFree!
