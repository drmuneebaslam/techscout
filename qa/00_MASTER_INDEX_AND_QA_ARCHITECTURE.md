# 🌐 TechScout QA Test Suite — Master Index & Architecture

> **Platform**: TechScout Universal Knowledge & Creator Engineering Platform  
> **Environment**: `http://localhost/techscout.n`  
> **Test Harness**: Dual Theme Engine (Dark `[data-theme="dark"]` & Light `[data-theme="light"]`)  
> **Role Isolation**: Guest, Member, Author, Admin  
> **Directory**: `qa/`  

---

## 📑 QA Test Suite Navigation

This comprehensive Quality Assurance documentation covers all 92+ platform screens, modal interfaces, and operational subfunctions:

| File Name | Test Scope & Module Overview | Primary URL / Routes Tested |
|---|---|---|
| [00_MASTER_INDEX_AND_QA_ARCHITECTURE.md](file:///c:/xampp/htdocs/techscout.n/qa/00_MASTER_INDEX_AND_QA_ARCHITECTURE.md) | Architectural invariants, test role matrix, theme switcher, global search | Global controls & test parameters |
| [01_GUEST_AND_PUBLIC_SCREENS.md](file:///c:/xampp/htdocs/techscout.n/qa/01_GUEST_AND_PUBLIC_SCREENS.md) | Guest discovery, articles feed, AI tools, prompts, GPTs, campaigns, auth | `/`, `/blog`, `/tools`, `/prompts`, `/gpts` |
| [02_MEMBER_PORTAL_SCREENS.md](file:///c:/xampp/htdocs/techscout.n/qa/02_MEMBER_PORTAL_SCREENS.md) | Member settings, profile bio, avatar upload, device isolation, strikes, reading history | `/account/settings`, `/bookmarks`, `/history` |
| [03_CREATOR_AND_AUTHOR_STUDIO.md](file:///c:/xampp/htdocs/techscout.n/qa/03_CREATOR_AND_AUTHOR_STUDIO.md) | Author studio, technical editor, external referrers & device breakdown, payouts | `/author/dashboard`, `/author/editor`, `/author/analytics` |
| [04_ADMIN_GOVERNANCE_AND_SYSTEM_SCREENS.md](file:///c:/xampp/htdocs/techscout.n/qa/04_ADMIN_GOVERNANCE_AND_SYSTEM_SCREENS.md) | Users roster, password change, reset links, account merge, badges, strikes, drawer, CMS | `/admin/dashboard`, `/admin/users`, `/admin/moderation` |
| [05_FINANCIAL_REVENUE_DISTRIBUTION_AND_LEDGER.md](file:///c:/xampp/htdocs/techscout.n/qa/05_FINANCIAL_REVENUE_DISTRIBUTION_AND_LEDGER.md) | Royalty pool distribution engine, searchable creator balance adjustments, ledger | `/admin/revenue` (tabs: pool, grant, ledger, payouts) |
| [06_SECURITY_FIREWALL_AND_AUDIT_TRAILS.md](file:///c:/xampp/htdocs/techscout.n/qa/06_SECURITY_FIREWALL_AND_AUDIT_TRAILS.md) | Web application firewall, rate limits, suspicious activity triggers, session isolation | `/admin/security`, `/admin/settings`, audit logs |

---

## 👥 Authentication Roles & Session Parameters

To test any screen under a specific role without logging in and out, the application supports seamless URL parameter auth:

| Role Key | Permissions Scope | Test Credentials / QA Param | Default Avatar Behavior |
|---|---|---|---|
| **Guest** | Browse public publications, directories, search, and view profiles | Unauthenticated or `?qa_auth=guest` | Generic silhouette or initials |
| **Member** | Bookmark, comment, react, edit profile bio, upload avatar, apply to write | `?qa_auth=member` (or username `asdf`) | Uploaded avatar photo or remote URL fallback |
| **Author** | Publish articles, track telemetry, access creator rewards, refer creators | `?qa_auth=author` (or username `alexvance`) | Author profile avatar with badge halo ring |
| **Admin** | Full system governance, revenue pool distribution, user adjustments, security | `?qa_auth=admin` (or username `superadmin`) | Staff verification badge & avatar |

---

## ☀️ Dual Theme Engine Verification Standards

Every screen must be verified in both themes:
1. **Dark Theme (`[data-theme="dark"]`)**:
   - Deep background `#070a13` / `#0b0f19`
   - Surface cards `rgba(15, 23, 42, 0.7)` with border `rgba(148, 163, 184, 0.12)`
   - Primary text `#f8fafc`, secondary text `#94a3b8`
   - Glowing neon accents (`#38bdf8` cyan, `#10b981` emerald, `#ec4899` pink)
2. **Light Theme (`[data-theme="light"]`)**:
   - Clean background `#f8fafc` / `#f1f5f9`
   - Surface cards `#ffffff` with crisp borders `rgba(148, 163, 184, 0.25)`
   - Primary text `#0f172a`, secondary text `#475569`
   - Vibrant contrast badges and visible hover states (especially on navigation buttons like `.settings-nav-btn`)

---

## 🛡️ Core Financial, Governance & Data Invariants
1. **Double-Entry Ledger Integrity**: Every balance adjustment, payout, or royalty pool disbursal MUST generate an immutable row in `financial_ledger` with `reference_no`, `balance_before`, `balance_after`, and `reason`.
2. **5% Base Platform Tax + Bank Withholding Invariant**:
   - Every creator payout withdrawal automatically incurs a **5.0% base platform tax** deducted from the gross claim.
   - If a bank rail is selected (`bank_wire`, `iban_sepa`), an additional **2.5% bank withholding tax** plus a **$5.00 wire surcharge** is deducted from the user.
   - Payout requests store `tax_deducted`, `bank_tax_deducted`, and `net_amount` explicitly in `author_payouts` and `financial_ledger`.
3. **Royalty Pool Safeguard**: The royalty pool distribution engine MUST calculate payouts based strictly on verified reads at base CPM ($0.80 USD). If a period has high platform revenue ($1,000 USD) but only one or few creators with low reads, the creator must NOT receive the entire pool; unearned surplus must remain in the **Platform Reserve Retained / Treasury**.
4. **Universal Admin Table Auto-Pagination**:
   - All administrative tables (`table.admin-table`) MUST be automatically paginated via `assets/js/admin_table_paginator.js`.
   - Includes real-time debounced search, page size selection (10, 15, 25, 50, 100 entries), and active numbered pagination pills.
5. **Pure Numerical Engagement Simulation (Zero Account Pollution)**:
   - Simulation in Engagement Studio (`/admin/engagement-studio`) MUST update numerical display counters directly (`view_count`, `reaction_count`, `bookmark_count`, `follower_count`, `reputation_score`).
   - Engagement Studio MUST NEVER bind or inject real registered members (`users.id`) as fake followers in `author_followers` or fake readers in `reading_history`.
6. **Audience Telemetry Access & Attribution Invariant**:
   - The "Audience & Readers Roster" / real-time reader telemetry tab is strictly restricted to Administrators (`/admin/analytics/content/{id}`). It is completely hidden from Authors (`/author/analytics/content/{id}`).
   - Reader telemetry MUST report multi-channel traffic attribution: **Internal** (with exact referring page), **External** (domain), or **User Referral Campaign** (`ref_...`).
7. **Identity Validation & Live Availability Check**:
   - Names must be between 2 and 60 characters. Usernames must be 3–30 characters matching `/^[a-zA-Z0-9_]{3,30}$/`.
   - Registration forms feature debounced AJAX availability checking via `GET /api/v1/auth/check-availability`.
8. **Database Schema & Seeds Portability**:
   - All 76 tables are synchronized in `database/schema.sql`.
   - All core relational fixtures (1,500+ records across 37 tables) are pre-seeded in `database/seeds.sql`.
