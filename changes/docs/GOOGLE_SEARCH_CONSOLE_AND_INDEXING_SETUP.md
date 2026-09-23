# TechScout Google Search Console, Indexing & SEO Setup Guide

This operational manual documents complete procedures for domain property verification, dynamic XML sitemap indexing scaling past 200,000+ entities, and configuring the automated Google Indexing API for instant crawl updates.

---

## 1. Google Search Console Property Verification

TechScout supports two verification methods for `https://techscout.online`:

### Method A: HTML Verification Meta Tag (Default)
The root layout header (`views/layouts/header.php`) includes the standard verification meta tag:
```html
<meta name="google-site-verification" content="google4b998188e6e58b8d.html" />
```
*(If a custom Google verification string is assigned in Google Search Console, update the content attribute above or set `google_site_verification` in `site_settings`.)*

### Method B: DNS TXT Record (Recommended for Apex + Subdomains)
In your DNS provider (e.g., Cloudflare, Route53, or Namecheap):
- **Type**: `TXT`
- **Host**: `@`
- **Value**: `google-site-verification=YOUR_GOOGLE_VERIFICATION_TOKEN`
- **TTL**: `Auto` / `300`

---

## 2. Dynamic Partitioned XML Sitemaps (Scales to 200,000+ Entities)

Google Search Console enforces strict sitemap limits: maximum 50,000 URLs or 50MB uncompressed per file.
TechScout implements a dynamic partition generator (`App\Controllers\FeedController`) chunking entities into 5,000 URLs per partition.

### Master Sitemap Index
Submit this single URL to Google Search Console under **Sitemaps > Add a new sitemap**:
```text
https://techscout.online/sitemap.xml
```

Google will automatically crawl the sitemap index and discover all child partitions:
- **Static Core Pages**: `https://techscout.online/sitemap-static.xml`
- **Taxonomy Categories**: `https://techscout.online/sitemap-categories.xml`
- **AI Tools (Chunked)**: `https://techscout.online/sitemap-tools-1.xml`, `sitemap-tools-2.xml`, ...
- **Prompt Blueprints (Chunked)**: `https://techscout.online/sitemap-prompts-1.xml`, `sitemap-prompts-2.xml`, ...
- **Custom GPTs (Chunked)**: `https://techscout.online/sitemap-gpts-1.xml`, `sitemap-gpts-2.xml`, ...
- **Editorial Articles (Chunked)**: `https://techscout.online/sitemap-articles-1.xml`, `sitemap-articles-2.xml`, ...

### Testing Sitemap Response Locally or via CLI
```bash
curl -I https://techscout.online/sitemap.xml
curl -s https://techscout.online/sitemap.xml | head -n 30
```

---

## 3. Google Indexing API Automation (Instant URL Notification)

For real-time discovery of newly published AI tools, articles, or prompt updates, use the Google Indexing API.

### Step 1: Create a Service Account in Google Cloud Console
1. Navigate to [Google Cloud Console](https://console.cloud.google.com/).
2. Create or select project **TechScout Production**.
3. Enable the **Web Search Indexing API**.
4. Go to **IAM & Admin > Service Accounts > Create Service Account**:
   - Name: `techscout-indexing-bot`
   - Role: `Owner` or `Service Account Token Creator`
5. Generate a JSON private key and save it securely as `storage/keys/google_service_account.json`.

### Step 2: Grant Permissions in Google Search Console
1. Copy the service account client email (e.g., `techscout-indexing-bot@techscout-production.iam.gserviceaccount.com`).
2. In Google Search Console, go to **Settings > Users and permissions > Add user**.
3. Paste the client email and assign **Owner** permissions.

### Step 3: Trigger URL Notifications on Publish
When content is published or updated in `AdminController` or `AuthorStudioController`, dispatch an HTTP request to the Google Indexing endpoint:
```http
POST https://indexing.googleapis.com/v3/urlNotifications:publish
Authorization: Bearer <ACCESS_TOKEN>
Content-Type: application/json

{
  "url": "https://techscout.online/tools/my-new-tool",
  "type": "URL_UPDATED"
}
```

---

## 4. Robots.txt and Crawl Directives

The platform generates dynamic robots directives via `App\Core\SeoEngine::getRobotsTxt()`:
```text
User-agent: *
Allow: /
Disallow: /admin/
Disallow: /author/studio/
Disallow: /api/
Disallow: /auth/
Sitemap: https://techscout.online/sitemap.xml
```

All direct access to repository metadata (`/.git`) is blocked at the Apache layer with HTTP 404 via `.htaccess`.
