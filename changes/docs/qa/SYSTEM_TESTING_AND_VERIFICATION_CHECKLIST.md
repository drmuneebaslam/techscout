# TechScout Production System Testing & Dual-Theme Device Verification Checklist

## Overview
This master verification checklist provides step-by-step procedures for testing all platform capabilities across **Dark Mode**, **Light Mode**, and all screen viewports (**Mobile**, **Tablet**, **Laptop/Desktop**).

---

## 1. Responsive Viewports & Theme Matrix
For every step below, verify in:
- **Dark Mode**: Default dark theme (`[data-theme="dark"]`)
- **Light Mode**: Click sun/moon toggle in top navigation (`[data-theme="light"]`)
- **Mobile Viewport**: 375px &times; 667px (iPhone SE) and 390px &times; 844px (iPhone 12/14/15)
- **Tablet Viewport**: 768px &times; 1024px (iPad Portrait) & 1024px &times; 768px (iPad Landscape)
- **Laptop / Desktop**: 1366px &times; 768px & 1920px &times; 1080px

---

## 2. Testing Suite 1: 100x Incoming Webhooks Engine (`/admin/incoming-webhooks`)

| Step | Action | Expected Result | Device / Theme Checklist |
| :--- | :--- | :--- | :--- |
| **1.1** | Navigate to `/admin/incoming-webhooks` | Page loads without PHP errors; stat cards show total webhooks, total events, successful rate, and payload security tier. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |
| **1.2** | Click **"New Ingestion Webhook 🚀"** | Modal opens with smooth animation; slug auto-generates in real-time as you type the webhook name. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |
| **1.3** | Create new webhook with target table `prompts` | Webhook is created instantly via AJAX; modal closes; card appears in grid. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |
| **1.4** | Click **"Edit"** on any Webhook Card | Edit Webhook Modal opens populated with current webhook name, slug, target table, HMAC secret, and active status. Submit via AJAX and verify instant update. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |
| **1.5** | Click **"Ingestion Blueprint 📖"** | 4-tab interactive modal opens (Single Entity, Multi-Table, Tag Sync, Security HMAC); click through all tabs and test code copy buttons. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |
| **1.6** | Click **"All Tables Sample Data ⚡"** | Table selector lists all tables; selecting a table fetches schema-valid sample JSON with one-click copy and simulator loader. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |
| **1.7** | Test **Multi-Table Webhook Ingestion** in Simulator | In Simulation Drawer, load multi-table sample payload (contents + prompts + tags); click "Simulate Payload Ingestion". Verify HTTP 200 and records updated without MySQL warning 1265. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |

---

## 3. Testing Suite 2: 100x Custom API Gateway (`/admin/custom-api`)

| Step | Action | Expected Result | Device / Theme Checklist |
| :--- | :--- | :--- | :--- |
| **2.1** | Navigate to `/admin/custom-api` | Introspector displays all database tables dynamically without hardcoded limits. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |
| **2.2** | Click **"New Endpoint 🚀"** | Create New API Endpoint Modal opens; supports Table Data, Schema Inspector, Table List, and Custom JSON. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |
| **2.3** | Click **"Edit"** on any endpoint card | Edit API Endpoint Modal opens populated with endpoint settings; updating saves via AJAX with no page flicker. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |
| **2.4** | Click **"Multi-Table Query 🔗"** | Relational Multi-Table Query Builder opens; select primary entity (`prompts`) and toggle joins (`contents`, `categories`, `users`); generated URL updates live. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |
| **2.5** | Click **"Dispatch in Sandbox Live Runner"** | Opens live API sandbox drawer and executes multi-table query returning consolidated JSON with parent content and author in < 30ms. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |
| **2.6** | Click **"All Tables JSON Catalog ⚡"** | Previews production-ready sample payloads for all database tables with one-click copy and sandbox test. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |
| **2.7** | Select table in Introspector and click **"Schema Blueprint"** | Introspects column types, nullable flags, defaults, and keys instantly. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |

---

## 4. Testing Suite 3: Prompt Single Page & Monetized Copy Flow (`/prompts/{slug}`)

| Step | Action | Expected Result | Device / Theme Checklist |
| :--- | :--- | :--- | :--- |
| **3.1** | Open any prompt page (e.g. `/prompts/photorealistic-cyberpunk-street`) | Teaser prompt is visible with blur; verify small auto-injected copy button is **NOT** present before unlocking. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |
| **3.2** | Inspect `#main-gallery-caption` in Light Mode and Dark Mode | Caption bar renders with crisp high-contrast text and frosted glass background in both themes with zero text wash-out. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |
| **3.3** | Verify Blueprint Metadata & Dynamic Variables | Only renders sections if data exists; hidden completely if empty or unconfigured. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |
| **3.4** | Click large **"📋 Copy Prompt & Open Studio"** button | Sponsor ad opens in a new tab via `window.open(adUrl, '_blank')`; prompt is automatically copied to user's system clipboard; button text changes to **"✓ Prompt Copied to Clipboard! 🎉"**; prompt box copy button appears. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |
| **3.5** | Paste clipboard into a text editor | Verifies exact prompt text was copied correctly. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |

---

## 5. Testing Suite 4: Keyset Cursor Pagination & Anti-Duplication (`/articles/{slug}` comments)

| Step | Action | Expected Result | Device / Theme Checklist |
| :--- | :--- | :--- | :--- |
| **4.1** | Open article or prompt discussion section | First 5-10 root comment threads load; initial load takes < 40ms. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |
| **4.2** | Post a new comment | Comment appears instantly; counter increments; cache tag `comments:{id}` is automatically invalidated. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |
| **4.3** | Click **"Load More Comments"** | Fetches next batch via Keyset cursor (`/api/v1/content/{id}/comments/cursor?cursor=...`). | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |
| **4.4** | Anti-Duplication Verification | Newly loaded comments check existing DOM IDs (`#comment-node-{id}`) and never duplicate existing comments on the page. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |

---

## 6. Testing Suite 5: High-Speed Cache & Low-Resource Optimization (2 vCPU, 2GB RAM)

| Step | Action | Expected Result | Device / Theme Checklist |
| :--- | :--- | :--- | :--- |
| **5.1** | Load homepage or blog index | Initial load populates cache in `storage/cache/`; subsequent refreshes serve in < 15ms. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |
| **5.2** | Edit an article in Admin (`/admin/articles`) | Saving article calls `Content::invalidateCache($id, $slug)` and `Cache::invalidateTag('articles')`. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |
| **5.3** | Check public article page | Immediately reflects the update (no stale cache). | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |
| **5.4** | Verify MySQL connection count | Database queries per cached page drop from 20+ down to 0-2 queries, keeping MySQL thread usage well below 2GB RAM limits. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |

---

## 7. Testing Suite 6: System Error Logs & Forensics (`/admin/system-logs`)

| Step | Action | Expected Result | Device / Theme Checklist |
| :--- | :--- | :--- | :--- |
| **7.1** | Navigate to `/admin/system-logs` | Stat cards display total exceptions, unresolved errors, fatal errors, and database exceptions. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |
| **7.2** | Click **"✓ Mark All Resolved"** button | All unresolved error logs are updated to resolved status with success banner. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |
| **7.3** | Trigger tipping test (`/api/v1/tips/submit`) | Notifications table insert includes `event_type` and does not throw MySQL error 1364. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |
| **7.4** | Check Firewall / Security Anomalies | Queries cast limits to integers; no MySQL 1064 syntax error near `''50''`. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |
| **7.5** | Trigger Webhook Ingestion simulation | `incoming_webhook_logs` records status without MySQL warning 1265 truncation. | [ ] Dark [ ] Light<br>[ ] Mobile [ ] Laptop [ ] Tablet |
