# TechScout Comprehensive Features List

This document provides a highly detailed, hand-analyzed, and comprehensive list of every active feature, sub-system, and capability present within the TechScout platform based on extensive codebase and Git log analysis.

## 1. Authentication & Identity Management
### 1.1 Secure Login & Registration
* **Post-Signup OTP Verification:** Users must verify their email via a one-time password delivered through the unified mail envelope to activate their accounts.
* **Granular RBAC (Role-Based Access Control):** Delineates permissions between Guests, Members, Authors, Moderators, and Super Admins. Access is gated at the routing level using middleware.
* **Password Management:** Secure password resets using time-limited tokens and click-to-reset links directly from the admin dashboard.
* **Session Revocation:** Users and admins can terminate active sessions across devices, forcing logouts on compromised clients.

### 1.2 User Profile & Account Settings
* **Author Profiles:** Dedicated public profiles showcasing an author's articles, tools, GPTs, and custom prompts.
* **Theme Preferences:** Persistent dual-theme toggling (Dark/Light mode) saved to the user's session and browser local storage.
* **Notification Preferences:** Users can opt-in to newsletters, direct messages, and system alerts.

## 2. Admin Hub & Enterprise Dashboard
### 2.1 Real-Time Telemetry & Analytics HUD
* **Live Heatmap Telemetry:** Tracks user clicks, scroll depth, and interaction points across public pages.
* **Client JS & 404 Error Telemetry:** Automatically logs client-side JavaScript crashes and broken link (404) attempts to the admin database for forensic analysis.
* **Automatic Timezone Detection:** Resolves visitor locations via IP to dynamically localize timestamps across the admin console.

### 2.2 Database Management Engine
* **SQL Query Terminal:** A secure, embedded terminal for running read-only diagnostic queries against the active database.
* **Zero-Crash FTP Backup Recovery:** Automated and manual routines for exporting SQLite or full SQL dumps directly from the UI.
* **Migrations Engine:** Panel-driven database schema migrations replacing traditional runtime DDL. Supports re-runnable batches.

### 2.3 Taxonomies & Categorization
* **Strict Entity Separation:** Independent database architectures for Categories vs. Subcategories.
* **Interactive Main Tabs & Sub-Tabs:** Unified taxonomy management interface allowing admins to switch contexts without page reloads (PJAX).
* **Parent-Child Scope Locking:** Prevents circular logic in category assignment.
* **Dedicated Image Prompt Categories:** Live asset counts displayed alongside prompt categories.

## 3. Creator Studio & Author Tooling
### 3.1 Content Editors
* **Multi-Tab Modal Editors:** Distraction-free writing environments for Authors, featuring auto-save and draft versioning.
* **Live Playgrounds & Variable Builders:** Authors can test "Prompt Blueprints" interactively before publishing.
* **Single Prompt Multi-Images:** Upload up to 4 preview images for a single AI prompt listing.
* **Universal Image Uploads:** Drag-and-drop asset management with hardware-accelerated rendering.

### 3.2 Monetization & Patronage
* **Creator Tip Jar:** Integration of fixed crypto wallet configuration for authors to receive direct tips.
* **95/5 Revenue Split:** Hardcoded 95% creator share and 5% platform fee for patronage features.
* **Admin Tipping Ledger:** Centralized dashboard for admins to verify on-chain payment hashes (TXIDs) and process tips.
* **Real-Time Crypto Pricing APIs:** Pulls live market rates for accurate fiat-to-crypto conversion calculations during transactions.

## 4. Mail Studio & Automations
### 4.1 Visual Mail Template Maker
* **Full-Screen 2-Column Editor:** Side-by-side editing with a live HTML iframe preview.
* **Block-Based Component Builder:** Drag-and-drop elements for standardizing transactional emails.
* **10 Variable Categories Engine:** Insert dynamic fields (e.g., {{user.name}}, {{receipt.txid}}) directly into templates.
* **Universal Mail Envelope:** Ensures consistent branding, headers, and footers across all outgoing platform emails.

### 4.2 Event Triggers & Routing
* **Zero-Code Visual Trigger Studio:** Admins can bind specific mail templates to platform events (e.g., `user.registered`, `payout.approved`) without writing PHP.
* **Visual Custom URL Pattern Triggers:** Trigger webhooks or internal emails when a user visits a matching URL.
* **Deduplication Engine:** Prevents the same event from triggering duplicate emails within a specified timeframe.

## 5. Webhooks & Integrations
### 5.1 Outbound Webhooks
* **Live Delivery Logs:** Tracks successful pushes and HTTP response codes.
* **Retry Mechanism:** Manual and automated delivery retries for failed webhook payloads.

### 5.2 Referral & Outbound Link Tracking
* **Gateway Routing (`/go/{slug}`):** All external links to AI Tools, Custom GPTs, and Affiliate resources pass through a tracking middleware.
* **Parameter Support:** Passes dynamic `?type=` arguments for granular affiliate tracking.

## 6. Directories & Content Discovery
### 6.1 AI Tools & Custom GPTs
* **Segregated Taxonomies:** Tools and GPTs operate in independent category trees from Blog Articles.
* **Pure Image Grid:** Visually dense, Pinterest-style masonry grid for browsing Image Prompts.
* **Hierarchical Breadcrumbs:** Enhances SEO and user navigation deep within nested tool directories.

### 6.2 Engagements & Social
* **Anti-Spam Comment Reactions:** Rate-limited and idempotent like/dislike buttons on content.
* **Visual Link Scanner:** Validates URLs posted in comments to protect against malicious domains.

## 7. Advertising & Sponsorships
### 7.1 Crypto Ads System
* **On-Chain Payment Verification:** Admins cross-reference advertiser TXIDs against supported blockchains.
* **Popunder Ad Integration:** Unobtrusive ad delivery triggered on card clicks and blueprint actions.
* **Ad Placements CRUD:** Manage banner lifecycle, impressions, and click-through rates.

## 8. Maintenance & System Health
### 8.1 Diagnostics
* **Diagnostic Ping:** Secured endpoint for external uptime monitors.
* **Error Forensics Inspector:** Deep-dive UI mapping logged SQL/PHP exceptions to specific users and IP addresses.
* **Dual-Theme Fallbacks:** Ensures error pages and maintenance 503 screens gracefully respect the user's Dark/Light preference.

## 9. Security & Compliance
* **Help Articles & Legal Pages CMS:** Dedicated Markdown editors for Terms, Privacy, and Community Guidelines.
* **Entity Linking in Audit Logs:** Every sensitive action (deletes, permission changes) is logged with immutable entity references.
* **Debounce Re-Entrancy Guards:** Prevents race conditions on sensitive UI toggles (e.g., Theme switching, Tip submissions).

---
*(Analysis compiled from comprehensive system review and TechScout Git history logs)*
