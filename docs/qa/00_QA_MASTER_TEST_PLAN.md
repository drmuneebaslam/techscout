# TechScout Master QA Test Plan & Verification Protocol

## 1. Document Overview
* **Platform:** TechScout Unified Architecture (Single-Domain Developer & AI Platform)
* **Version:** 2.4.0-Production
* **Target Audience:** QA Engineers, Manual Testers, Automation Leads, Security Auditors
* **Test Scope:** Full-stack verification of all 40 platform phases, 25+ administrative CRUD modules, author studio, creator economy, legal pages, and telemetry engines.

---

## 2. Test Environments & Prerequisites

### 2.1 Host Environment
| Component | Requirement / Active Config |
| :--- | :--- |
| **Operating System** | Windows / Linux / macOS |
| **Web Server** | Apache 2.4+ (XAMPP / Nginx) |
| **PHP Runtime** | PHP 8.2+ with `pdo_mysql`, `zip`, `gd`, `curl`, `mbstring` |
| **Database** | MySQL 8.0+ / MariaDB 10.4+ (`techscout_db`) |
| **Base URL** | `http://localhost/techscout.n` or production domain |

### 2.2 Test User Personas
1. **Unauthenticated Public Visitor:** Browses articles, topics, AI tools, prompts, job board, help center, legal pages.
2. **Authenticated Member (`user`):** Can bookmark, react, comment, submit tickets, apply for author guild.
3. **Verified Author (`author` without templates):** Standard author studio, drafts, analytics, word import.
4. **Senior / Template-Authorized Author (`author` with `can_use_templates = 1`):** Studio + Curated template library.
5. **System Administrator (`admin`):** Unrestricted access to `/admin/*`, settings, telemetry, firewall, payouts, and navigation builder.

---

## 3. Defect Severity Matrix

| Severity Level | Definition | Response SLA |
| :--- | :--- | :--- |
| **P0 - Critical (Blocker)** | Data corruption, authentication bypass, SQL exceptions, server 500 crashes, payment failure. | Immediate (< 2 hours) |
| **P1 - Major** | Core feature malfunction (e.g. DOCX parser fails, article review cannot approve, email dispatch error). | < 8 hours |
| **P2 - Moderate** | Layout misalignment on specific viewports, non-blocking UI glitches, theme contrast issues. | < 24 hours |
| **P3 - Minor** | Typos, tooltip misalignments, minor styling optimizations. | Next release sprint |

---

## 4. Test Suite Index

| Test Suite Document | Functional Scope | Key Test Focus Areas |
| :--- | :--- | :--- |
| [01_AUTHENTICATION_AND_USER_MANAGEMENT_TEST_SUITE.md](file:///c:/xampp/htdocs/techscout.n/docs/qa/01_AUTHENTICATION_AND_USER_MANAGEMENT_TEST_SUITE.md) | Auth, Profiles & RBAC | Registration, Login, Argon2id, Session Persistence, Role Middleware |
| [02_AUTHOR_STUDIO_AND_PUBLISHING_TEST_SUITE.md](file:///c:/xampp/htdocs/techscout.n/docs/qa/02_AUTHOR_STUDIO_AND_PUBLISHING_TEST_SUITE.md) | Creator Studio & Word Import | WYSIWYG ribbon, DOCX Word parsing, Template permissions gating |
| [03_CONTENT_DISCOVERY_AND_TAXONOMY_TEST_SUITE.md](file:///c:/xampp/htdocs/techscout.n/docs/qa/03_CONTENT_DISCOVERY_AND_TAXONOMY_TEST_SUITE.md) | Discovery, Feeds & Tools | Articles, 100+ Topics, AI Tools radar, Prompts, GPTs, Jobs |
| [04_CREATOR_MONETIZATION_AND_PAYOUTS_TEST_SUITE.md](file:///c:/xampp/htdocs/techscout.n/docs/qa/04_CREATOR_MONETIZATION_AND_PAYOUTS_TEST_SUITE.md) | Revenue & Creator Rewards | View CPM, Bounties, Payout ledger, PayPal/Stripe/Crypto disbursements |
| [05_ADMIN_PANEL_AND_CMS_ENGINES_TEST_SUITE.md](file:///c:/xampp/htdocs/techscout.n/docs/qa/05_ADMIN_PANEL_AND_CMS_ENGINES_TEST_SUITE.md) | CMS Admin Hub & Settings | 14-Module Analytics, 19 Settings categories, Newsletters, Navigation |
| [06_LEGAL_EDITORIAL_AND_HELP_CENTER_TEST_SUITE.md](file:///c:/xampp/htdocs/techscout.n/docs/qa/06_LEGAL_EDITORIAL_AND_HELP_CENTER_TEST_SUITE.md) | Static, Legal & Responsive UI | 9 Legal pages, 14 Help modules, 2-Column mobile footer, Dark/Light mode |

---

## 5. QA Sign-Off Checklist
- [ ] 0 SQL syntax or missing column exceptions in Apache/PHP error logs.
- [ ] All 25+ administrative CRUD endpoints successfully Create, Read, Update, and Delete.
- [ ] Author Studio template buttons strictly hidden for unauthorized creators.
- [ ] Mobile footer renders in clean 2-column layout on iOS/Android viewports (<= 768px).
- [ ] Light & Dark themes switch seamlessly with no hardcoded invisible elements.
