# 🛡️ QA Specification: Admin Governance & Enterprise CMS Screens

> **Scope**: Administrator command center, user governance subfunctions, moderation station, editorial taxonomy, support desk, advertising, media library, SEO, newsletters, and security firewall.  
> **Target Environment**: `http://localhost/techscout.n`  
> **Auth Session**: `?qa_auth=admin` or login as `superadmin`  

---

## 📋 Administrator Screen Matrix

| ID | Screen Title | Route Path | Core Modules Tested |
|---|---|---|---|
| 4.1 | Admin Command Center HUD | `GET /admin/dashboard` | Global telemetry, revenue counters, engagement simulator |
| 4.2 | Users Governance & Roster | `GET /admin/users` | User management, filtering, searching, and 11 administrative actions |
| 4.3 | Editorial Moderation Station | `GET /admin/moderation` | Article review queue, approval, revisions request, rejection |
| 4.4 | Categories & Taxonomy Hub | `GET /admin/categories` | 100+ topic management, slug editor, icon assignment, reorder |
| 4.5 | Support Tickets Queue | `GET /admin/tickets` | Member inquiries, staff assignment, priority tags, reply stream |
| 4.6 | Advertisements & Banners | `GET /admin/advertisements` | Ad slot locations, impression counts, click tracking, budget |
| 4.7 | Homepage Modular Builder | `GET /admin/homepage-builder` | Drag-and-drop layout blocks, pinned featured publications |
| 4.8 | Digital Asset Media DAM | `GET /admin/media` | Image upload studio, asset metadata, copy CDN links |
| 4.9 | SEO Studio & Redirects | `GET /admin/seo` | Title tags, meta descriptions, 301 redirects, XML sitemap generator |
| 4.10 | Newsletter Mail Broadcasts | `GET /admin/newsletters` | Email template builder, subscriber lists, test dispatch |
| 4.11 | Cyber Security Firewall & WAF | `GET /admin/security` | IP blacklist/whitelist, rate limit triggers, suspicious activity flags |
| 4.12 | System Settings HUD | `GET /admin/settings` | Maintenance mode, SMTP parameters, registration controls |

---

## 🔍 Detailed Functional Test Cases: Users Governance (`/admin/users`)

The `/admin/users` hub contains 11 core administrative subfunctions:

### Action 1: View User Detail Hub (`/admin/users/{id}`)
- **Trigger**: Click *"View"* button or user handle in the roster table.
- **Verification**: Opens dedicated user profile view showing registration date, last login IP, account status, authored posts count, comments count, and active strikes.

### Action 2: Change User Password Modal
- **Trigger**: Click *"Change Password"* in user actions dropdown.
- **Modal Input**: Input new password & confirmation.
- **Verification**: Submits `POST /admin/users/change-password`. Password hash is updated securely in database without exposing plain text.

### Action 3: Quick 24-Hour Password Reset Link Generator
- **Trigger**: Click *"Quick Reset Link"* button.
- **Verification**:
  1. Opens modal generating a secure 24-hour one-time login token link:
     `http://localhost/techscout.n/auth/reset-password?token=...`
  2. Click *"Copy Link"*. Clipboard contains link.
  3. Verify endpoint responds with HTTP 200 JSON and valid URL.

### Action 4: Account Merging Modal
- **Trigger**: Click *"Merge Accounts"* button in user row.
- **Modal Inputs**: Select secondary duplicate user account to merge into this primary ID.
- **Verification**:
  1. Submits `POST /admin/users/merge`.
  2. Reassigns all authored articles, comments, bookmarks, and reading history to primary ID.
  3. Marks secondary account as `deleted` or `merged` in `users` table.

### Action 5: Author Tier & Custom Verification Badge Modal
- **Trigger**: Click *"Tier & Badge"* action.
- **Modal Inputs**:
  - Verification Badge: `verified_builder`, `core_maintainer`, `staff`, `ai_innovator`, or `none`.
  - Custom Badge Label (e.g. `Lead Distributed Architect`).
  - Custom Badge Color (Hex color picker).
  - Manual Tier Override (Apprentice, Master Scout, Elite).
- **Verification**:
  1. Submits `POST /admin/users/tier-badge`.
  2. Author profile displays custom badge and label.
  3. **CPM Invariant**: Badge assignment has NO effect on CPM calculations.

### Action 6: Editorial Strike & Penalty Issuance Modal
- **Trigger**: Click *"Issue Strike"* button.
- **Modal Inputs**:
  - Strike Severity: Warning (Strike 1), 7-Day Suspension (Strike 2), Permanent Ban (Strike 3).
  - Guideline Category: Plagiarism, Spam/Abuse, Misleading Content.
  - Reason & Evidence Notes.
- **Verification**:
  1. Submits `POST /admin/users/strike`.
  2. Increments `users.strike_count`. If Strike 3, automatically sets `users.status = 'banned'`.
  3. Records entry in `user_strikes` table and dispatches notification.

### Action 7: User Security Audit Trail Drawer
- **Trigger**: Click *"Security Drawer"* or *"Audit Trail"*.
- **UI Element**: Slide-out drawer on the right side of the screen.
- **Verification**:
  1. Displays last 20 login events, IP addresses, country flags, and browser user agents.
  2. Displays recent security changes (password updates, 2FA status, strike issuance).

### Action 8: Bulk Email Broadcast Dispatcher Modal
- **Trigger**: Click *"Bulk Email Dispatch"* button at the top of the users roster.
- **Modal Inputs**: Recipient segment filter (All Users, Authors Only, Suspended Users), Subject line, HTML message body.
- **Verification**: Dispatches queued broadcast via `Mailer` and logs audit event.

### Action 9: Admin Impersonation Mode ("Login As User")
- **Trigger**: Click *"Impersonate"* button on any author or member row.
- **Verification**:
  1. Immediately switches active session to target user without requiring password.
  2. Renders sticky orange exit bar at top of screen:
     `Impersonation Mode Active: Browsing as @username [Exit Impersonation ✕]`.
  3. Click *"Exit Impersonation ✕"* &rarr; Session immediately restored to admin at `/admin/users`.

---

## 🔍 Detailed Functional Test Cases: Challenge Campaigns & Submissions Hub (`/admin/campaigns`)

### Tab 1: Active Challenges & Competitions
1. **Challenge Requirements Customizer**:
   - In Create/Edit Challenge modal, admin can define submission criteria checkboxes:
     - `Require GitHub Repository Link`
     - `Require Live Demo URL`
     - `Require Video Walkthrough URL`
     - `Require Linked TechScout Publication`
     - `Require Tech Stack Tags`
     - `Enable Viral Referral Engine (?ref=)`
     - `Enable Public Community Voting`
2. **Persistence**:
   - Verify `submission_fields` JSON, `enable_referral_system`, and `enable_public_voting` are saved in `campaigns` table.

### Tab 2: Participant Submissions Hub & Grading Desk
1. **Submissions Roster Table**:
   - Displays all participant submissions with filter by Challenge, Search by Title/Author, and status badges (`pending`, `under_review`, `approved`, `winner`, `runner_up`, `rejected`).
   - Table columns: Participant, Challenge, Title & Artifacts (GitHub, Demo, Video, Article links), Referral & Vote Telemetry, Score, Status, Actions.
2. **Review & Grade Modal**:
   - **Trigger**: Click *"Review / Grade"* button on any submission row.
   - **Modal Inputs**:
     - Review Status dropdown (`Approved`, `Under Review`, `Winner`, `Runner Up`, `Rejected`).
     - Judge Score slider/input (0 to 100).
     - Awarded Prize Amount ($ USD).
     - Official Judge Feedback & Evaluation Notes textarea.
   - **Verification**:
      - Submits `POST /admin/campaigns/submission/grade`.
      - Updates database record in `challenge_submissions`.
      - Dispatches instant in-app notification to the participant with score and feedback notes.

---

## 🔍 Detailed Functional Test Cases: Engagement Studio v3.0 (`/admin/engagement-studio`)

### Screen 4.13: Pure Numerical Telemetry & Multi-Channel Simulation Studio
1. **Unified Multi-Channel Target Resolver**:
   - The studio features a multi-type selector:
     - `article`: Technical Publications & Guides
     - `tool`: Directory Developer Tools
     - `prompt`: AI Engineering Prompts
     - `gpt`: Custom AI Agents & Models
     - `campaign`: Hackathon & Bounty Challenges
     - `author`: Creator Profiles
   - Resolver accepts versatile target inputs:
     - **Numeric Database ID**: e.g., `1`, `42`
     - **Full Local/Public URL**: e.g., `http://localhost/techscout.n/article/real-time-ai-agents` or `/author/alexvance`
     - **Slug Identifier**: e.g., `real-time-ai-agents`
     - **Author Handle**: e.g., `@alexvance` or `alexvance`
   - Auto-fetches current metrics and loads live telemetry cards immediately upon resolution.
2. **Pure Numerical Telemetry Engine (Zero Account Pollution Guarantee)**:
   - **Critical System Invariant**: Simulation boosts and adjustments **strictly modify numerical counters** (`view_count`, `reaction_count`, `bookmark_count`, `follower_count`, `reputation_score`).
   - **Zero User Account Pollution**:
     - The simulator **never** inserts fake follow relationships into `author_followers` or `follows`.
     - The simulator **never** fabricates fake reading entries in `reading_history` assigned to genuine registered user accounts (`superadmin`, `asdf`, etc.).
     - Reading simulations create anonymous, session-only telemetry in `analytics_events` with `user_id = NULL` to simulate realistic reader concurrency.
3. **Dual-Mode Metric Adjustment**:
   - **Preset Quick-Boost Buttons**:
     - Reads/Views: `+50`, `+250`, `+1,000`, `+5,000`, `+25,000`
     - Reactions: `+10`, `+50`, `+200`, `+1,000`
     - Bookmarks: `+5`, `+25`, `+100`, `+500`
     - Creator Followers: `+25`, `+100`, `+500`, `+1.0k`, `+5.0k`
     - Creator XP: `+100`, `+500`, `+2,500`
   - **Exact Target Value Inputs**:
     - Admins can input an exact numerical value (e.g., set views to `12,500`, set followers to `1,337`, set XP to `8,500`).
     - Submitting immediately updates the counter to that exact number without side effects.
4. **Real-Time Active Readers Telemetry (Admin-Only Telemetry)**:
   - Live stream of reader activity showing:
     - Multi-channel traffic attribution (Internal Page, External Domain, Referral Campaign Code).
     - Estimated read progress and reading velocity.
     - Device classification (Desktop, Mobile, Tablet).
   - This panel is completely isolated from regular author viewports.

---

## 🔍 Detailed Functional Test Cases: Universal Admin Table Pagination

### Universal Architecture Specification (`/admin/*`)
1. **Global Auto-Attachment**:
   - All administrator tables styled with `.admin-table` automatically instantiate interactive client-side pagination.
   - Modules covered:
     - `/admin/users`: User Directory & Member Roster
     - `/admin/moderation`: Publication Editorial Queue
     - `/admin/campaigns`: Challenge Submissions Hub
     - `/admin/revenue`: Payout Requests Queue & Financial Ledger
     - `/admin/audit-logs`: System Audit Telemetry Log
     - `/admin/firewall`: Threat Detection, Lockouts, & Anomalies
2. **Feature Requirements**:
   - **Debounced Live Search Input**: Real-time filtering with 200ms debounce that matches any column in the row.
   - **Page Size Selector**: Configurable dropdown supporting `10`, `25`, `50`, and `100` rows per page.
   - **Active Page Pill Indicators**: Numbered pills with active glow indicator for current page, ellipsis truncation for large record sets, and Prev/Next step buttons.
   - **Telemetry Counter**: Live readout displaying `"Showing X to Y of Z entries"`.
   - **Zero-Flicker DOM Swapping**: Client-side state maintains seamless table row rendering.

---

## 🔍 Detailed Functional Test Cases: Admin Dashboard Ergonomics & Visual Layout

### Layout & Spacing Requirements (`/admin/dashboard`)
1. **Tabbed & Sub-Tabbed Architecture**:
   - Navigation hierarchy cleanly separates operational domains:
     - Overview HUD & Metric Counters
     - Content & Editorial Station
     - User Governance & Permissions
     - Financial Revenue & Payout Ledger
     - Cyber Security Firewall & Audit
2. **Card Spacing & Non-Overlapping Invariant**:
   - Minimum 24px grid gutter between metric cards and analytic charts.
   - Ribbon elements use CSS Flexbox with `gap: 1.25rem; flex-wrap: wrap;` ensuring zero content overlapping on viewports from 1200px to 3840px (4K).
   - Metric pill badges use overflow ellipsis to prevent text clipping.
