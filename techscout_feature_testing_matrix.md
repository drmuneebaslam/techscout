# TechScout Exhaustive Feature Testing Matrix

This document provides a comprehensive, full-depth checklist of every feature implemented in the TechScout platform from the very first commit to the present. Use this matrix to rigorously test each feature for functionality, **Light Mode / Dark Mode** consistency, and **Cross-Device Responsiveness** (Desktop, Tablet, Mobile).

---

## 1. System Core & Architecture

### 1.1. Core Utilities
- [ ] **Dynamic Base URL Resolution**: Test that the platform auto-resolves correctly on different domains and behind proxies (Cloudflare, InfinityFree).
- [ ] **Favicon & Web App Manifest**: Ensure `favicon.ico` serves correctly (no 404s) and `manifest.json` loads without errors.
- [ ] **Service Worker (`sw.js`)**: Verify service worker registration and caching logic.
- [ ] **Timezone Engine**: Ensure timestamps dynamically localize to the user's local timezone (via client-side detection and cookie).
- [ ] **SEO Engine**: Test dynamic `robots.txt` and generated sitemaps (articles, tools, prompts, categories, static pages).
- [ ] **Mail Attachment Streamer**: Verify clicking an attachment link in an email safely streams the file without a 404 error.

---

## 2. Public Facing Portal

### 2.1. Homepage & Discovery
- [ ] **Dynamic Homepage**: Test all 15 discovery modules on the homepage (Hero, trending articles, featured tools, etc.).
- [ ] **Global Search**: Test search functionality (`/search`) with various queries (topics, tools, articles).
- [ ] **Sponsor / Advertise Portal**: Test the sponsor page layout and the submission forms for sponsored tools and posts.

### 2.2. Blog & Editorial System
- [ ] **Blog Hub (`/blog`)**: Verify layout of the main blog page, trending articles, and latest articles.
- [ ] **Article Reader (`/article/{slug}`)**: 
  - [ ] Test typography, reading progress bar, social sharing buttons.
  - [ ] Verify Light/Dark mode contrast for code blocks and inline formatting.
- [ ] **Categories & Tags**: Test category and tag filtering for articles (`/blog/category/{slug}`).

### 2.3. Directories (Tools, Prompts, GPTs, Campaigns, Jobs)
- [ ] **Categories Hub**: Test the directory category listings (`/categories`, `/tools/categories`, etc.).
- [ ] **AI Tools Directory (`/tools`)**: Test tool listings, individual tool pages, and affiliate redirects (`/go/tool/{slug}`).
- [ ] **Prompts Directory (`/prompts`)**: Test text and image prompt listings, including copy-to-clipboard functionality.
- [ ] **GPTs Directory (`/gpts`)**: Test custom GPT listings and category filtering.
- [ ] **Campaigns & Voting (`/campaigns`)**: Test campaign single pages, submission forms, and the community voting mechanism.
- [ ] **Jobs Board (`/jobs`)**: Test job listings and the applicant submission flow (with file uploads).

---

## 3. Authentication & User Identity

### 3.1. Onboarding & Access
- [ ] **Login & Registration**: Test forms (`/auth/login`, `/auth/register`). Verify validations.
- [ ] **Email Verification (6-Digit OTP)**: Test the OTP screen (`/verify-email`), auto-advancing 6-digit inputs, pasting, and resend cooldown.
- [ ] **Password Reset**: Test "Forgot Password", the email dispatch (Click-to-Reset CTA), and the reset screen.
- [ ] **Two-Factor Authentication**: Test the 2FA challenge screen.
- [ ] **Onboarding Flow**: Test the post-registration onboarding questionnaire.

### 3.2. User Account Management
- [ ] **Account Settings**: Test updating username, email, and preferences.
- [ ] **Profile Customization**: Test updating avatars, bio, and social links.
- [ ] **Session Management**: Test revoking individual sessions and "Terminate Other Sessions" functionality.
- [ ] **Author Profile (Public)**: Test the public view of a user/author's profile (`/u/{slug}`).

---

## 4. Creator & Author Studio

### 4.1. Creator Dashboard & Editor
- [ ] **Author Dashboard (`/author`)**: Test KPI metrics, recent drafts, and quick actions.
- [ ] **Block-Based Editor**: Test the content editor (`/author/editor`).
  - [ ] Test text formatting, inserting images, tables, and code blocks.
  - [ ] Test **DOCX Import** functionality.
  - [ ] Test auto-saving and manual draft saving.
- [ ] **Content Analytics**: View detailed telemetry (views, reads, engagement) for specific published content.

### 4.2. Monetization & Rewards
- [ ] **Rewards Ledger**: Check the display of earned rewards and transaction history.
- [ ] **Tip Wallets**: Test configuring crypto/fiat tip wallets.
- [ ] **Payout Methods**: Test adding, deleting, and setting default payout methods.
- [ ] **Payout Requests**: Test submitting a payout request from the author portal.
- [ ] **Co-Author Collaboration**: Test responding to co-author invitations.

---

## 5. Admin - Command & Intelligence Hub

### 5.1. Dashboard & Telemetry
- [ ] **Executive Dashboard**: Test KPI cards, recent activity streams.
- [ ] **Real-time Analytics HUD**: Test the `/admin/analytics` and realtime JSON endpoints.

### 5.2. Content & Entity Management (CRUD)
*For all modules below, test Create, Read (List/Search), Update, and Delete actions, as well as bulk actions if applicable.*
- [ ] **Articles / Editorial Review**: Review, approve, reject articles submitted by authors.
- [ ] **AI Tools**: Manage the tools directory.
- [ ] **Prompts & GPTs**: Manage prompt libraries and custom GPTs.
- [ ] **Campaigns**: Grade submissions and manage active campaigns.
- [ ] **Topics & Categories**: Manage the hierarchical taxonomy (categories and subcategories).
- [ ] **Jobs Board**: Manage job listings and review incoming candidate applications.

### 5.3. Identity Governance & Moderation
- [ ] **User Management**: Edit users, manage roles, issue strikes, pardon strikes, merge accounts.
- [ ] **Impersonation Engine**: Test impersonating a user (`/admin/users/impersonate/{id}`) and exiting impersonation.
- [ ] **Moderation & Reports**: Review user-submitted reports, issue strikes, and message reporters.
- [ ] **Author Applications**: Review, approve, or reject incoming creator applications.
- [ ] **Subscriber Management**: View, export, and delete topic/author subscriptions.

### 5.4. Revenue & Monetization
- [ ] **Creator Revenue**: Grant custom rewards, adjust balances, and distribute reward pools.
- [ ] **Payouts Processing**: Process author payout requests, including uploading physical receipt attachments.
- [ ] **Advertisements**: Manage ad slots, toggle status, configure pricing settings, and verify on-chain TxIDs.
- [ ] **Sponsored Placements**: Approve or reject incoming sponsored submissions.

### 5.5. Mail & Communication Systems
- [ ] **Universal Mail Envelope**: Test that all dispatched emails render inside the white card envelope with grid lines.
- [ ] **Template Studio**: Use the block-based builder to edit templates (`/admin/mail/template/editor`). Verify the light-mode sanitization works.
- [ ] **Mail Credentials**: Manage SMTP/API credentials, duplicate profiles, set defaults, and run connection tests.
- [ ] **Delivery Logs & Tracking**: View the 10x Executive Telemetry HUD for sent emails. Verify Open and Click tracking counts update correctly.
- [ ] **Newsletters**: Test drafting a broadcast, saving issues, and dispatching.
- [ ] **Contact Messages**: Read and reply to inbound contact form submissions.

### 5.6. Developer & Integration Tools
- [ ] **Incoming Webhooks**: Create webhooks, map JSON payloads, use the Live JSON Listener, and toggle modes.
- [ ] **Outbound Webhooks**: Manage outbound events, test triggers, and retry deliveries.
- [ ] **Custom API Builder**: Create custom endpoints based on database schemas.
- [ ] **API Keys**: Generate and revoke developer access keys.
- [ ] **Feature Flags**: Toggle experimental features on/off instantly.

### 5.7. System, Security & SEO
- [ ] **Cyber Security Firewall**: Manage IP blocklists, view firewall logs, clear logs.
- [ ] **Database Manager**: Optimize tables, auto-repair schema, run custom queries, execute migrations.
- [ ] **System & Error Logs**: Review crash forensics, resolve errors, and clear logs.
- [ ] **SEO Studio**: Manage 301 redirects and global SEO metadata.
- [ ] **Help Center & Legal Pages CMS**: Manage knowledge base articles and legal documents (Terms, Privacy).
- [ ] **Dynamic Popups & Announcements**: Create and trigger global announcement ribbons and modal popups.

---

## 6. Personal Hub & Support

### 6.1. User Engagement
- [ ] **Notification Center (`/notifications`)**: Test the responsive grid, filter ribbon, and detailed reader modal.
- [ ] **Bookmarks & History**: Verify that saving an article adds it to bookmarks (`/bookmarks`) and reading history tracks correctly (`/history`).
- [ ] **Inbox (`/inbox`)**: Test the internal messaging/notification system for users.

### 6.2. Support Desk
- [ ] **Support Portal (`/support`)**: Test the public support desk.
- [ ] **Ticket Creation**: Submit a support ticket as a user.
- [ ] **Ticket Threading**: Reply to tickets (User side and Admin side).

---

## 7. QA Testing Protocol

When testing the features above, ensure you adhere to the following checks for every screen:
1. **Light Mode**: Verify pure light backgrounds, crisp dark text, and visible subtle borders (no ghosting or washed-out text).
2. **Dark Mode**: Verify deep slate/black backgrounds, bright readable text, and appropriate neon/gradient accents. Ensure no hardcoded inline `style="color: #000"` tags break the layout.
3. **Mobile (max-width: 480px)**: Ensure tables collapse to cards or scroll horizontally, modals fit within `95vw`, buttons are touch-friendly (min 44px height), and sidebars convert to off-canvas drawers.
4. **Tablet (max-width: 768px)**: Ensure grid layouts transition smoothly (e.g., from 3 columns down to 2).
5. **Desktop (min-width: 1024px)**: Ensure maximum widths are respected so content doesn't stretch infinitely on ultrawide monitors.
