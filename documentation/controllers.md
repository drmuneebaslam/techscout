# TechScout HTTP Controllers & API Endpoints

This document provides a complete reference for all 14 HTTP Controller classes in TechScout (`app/Controllers/`), detailing request handling, input validation, access constraints, and view rendering.

---

## 1. `AdminController`
- **Location**: [`app/Controllers/AdminController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/AdminController.php)
- **Access Guard**: `Auth::requireAdmin()` / `RBAC::requirePermission()`
- **Primary Responsibilities**: Enterprise management dashboard, content moderation queue, user management, audit trails, mail center, financial ledgering, analytics, system configuration, database backup management.

### Key Action Methods:
- **`dashboard(Request $request)`**: Renders admin dashboard view with system-wide analytics (total users, published items, author count, mail logs, database metrics).
- **`articles(Request $request)` / `saveArticle()` / `reviewArticle()` / `deleteArticle()`**: Editorial management for blog posts. Supports publishing, drafting, rejecting, scheduled releases, and article deletion.
- **`tools(Request $request)` / `saveTool()` / `deleteTool()`**: AI Tool directory administration (submission approval, affiliate link creation, categorizations).
- **`prompts(Request $request)` / `savePrompt()` / `deletePrompt()`**: Prompt directory moderation and metadata editing.
- **`gpts(Request $request)` / `saveGpt()` / `deleteGpt()`**: Custom GPTs directory administration.
- **`campaigns(Request $request)` / `saveCampaign()` / `gradeCampaignSubmission()` / `deleteCampaign()`**: Promotional campaign management, grading member submissions, and disbursing reward credits.
- **`categories(Request $request)` / `topics(Request $request)`**: Directory taxonomy and tagging controls.
- **`contentQueue(Request $request)` / `contentAction()`**: Content moderation queue for pending community/author submissions.
- **`users(Request $request)` / `createUser()` / `editUser()` / `deleteUser()` / `userAction()`**: Full user lifecycle management. Supports role adjustments, account banning, suspension, unbanning, and merging duplicate accounts.
- **`broadcastNotification(Request $request)`**: Sends site-wide in-app notifications or push alerts to all active users or targeted role groups.
- **`impersonateUser()`**: Allows super-admins to initiate impersonation sessions to troubleshoot user issues.
- **`issueStrike()` / `pardonStrike()`**: User discipline engine for tracking moderation strikes and automating suspensions.
- **`setAuthorTier()` / `setCustomBadge()`**: Overrides author reward tiers and assigns custom verification badges.
- **`mailCenter(Request $request)` / `saveMailSettings()` / `sendCustomMail()` / `saveMailTemplate()`**: Complete email operations hub. Manages SMTP credentials, proxy failovers, email campaign dispatch, HTML templates, tracking pixels, and bounce logs.
- **`financialLedger(Request $request)` / `adjustCredits()`**: Financial ledger audit panel tracking author reward payouts, promotional credits, and tipping histories.
- **`securityLogs(Request $request)` / `auditTrail()`**: Anomaly detection dashboard viewing suspicious IP attempts, failed login spikes, and system audit logs.
- **`databaseBackup(Request $request)` / `triggerBackup()` / `restoreBackup()`**: Database backup manager triggering automated SQL dumps and restoring schemas.

---

## 2. `ApiController`
- **Location**: [`app/Controllers/ApiController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/ApiController.php)
- **Access Guard**: API Token authentication or CSRF-verified web requests.
- **Primary Responsibilities**: Serves RESTful JSON payloads for mobile/external integrations and AJAX client interactions.

### Key API Endpoints:
- **`GET /api/v1/content`**: Returns paginated list of published articles, tools, GPTs, or prompts with filtering support.
- **`GET /api/v1/content/{id}`**: Returns detailed JSON object for single item including author metadata and comments.
- **`POST /api/v1/comments`**: Submits new comment on content item.
- **`POST /api/v1/reactions`**: Toggles upvote/downvote reaction on content items or comments.
- **`GET /api/v1/search`**: Auto-complete search endpoint for instant header search queries.
- **`POST /api/v1/author/follow`**: Follows/unfollows an author profile.
- **`GET /api/v1/notifications`**: Fetches user's unread notification list and unread count badge.
- **`POST /api/v1/notifications/read`**: Marks specified notification or all notifications as read.

---

## 3. `AuthController`
- **Location**: [`app/Controllers/AuthController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/AuthController.php)
- **Primary Responsibilities**: User onboarding, login authentication, registration, password recovery, email verification, 2FA, session teardown.

### Key Action Methods:
- **`showLogin()` / `login(Request $request)`**: Displays login form and processes credentials. Verifies password hashes (`password_verify`), rate-limits failed attempts, handles 2FA challenges, and establishes session.
- **`showRegister()` / `register(Request $request)`**: Processes new user signups. Validates email format, checks username availability, hashes password (`password_hash`), dispatches verification email, and logs user in.
- **`logout(Request $request)`**: Clears session tokens, revokes database session records, and redirects to home page.
- **`showForgotPassword()` / `sendResetLink()` / `showResetPassword()` / `resetPassword()`**: Self-service password reset flow utilizing cryptographically secure time-limited reset tokens.
- **`showVerifyEmail()` / `verifyEmail()` / `resendVerifyEmail()`**: Handles email address confirmation.
- **`showTwoFactor()` / `twoFactor()`**: Time-based One-Time Password (TOTP) / email OTP verification challenge handling.
- **`showOnboarding()` / `completeOnboarding()`**: Multi-step account setup wizard for new members (selecting interests, setting up profile).

---

## 4. `AuthorApplicationController`
- **Location**: [`app/Controllers/AuthorApplicationController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/AuthorApplicationController.php)
- **Access Guard**: `Auth::requireAuth()`
- **Primary Responsibilities**: Manages member applications to become verified content authors on TechScout.

### Key Action Methods:
- **`showForm()`**: Renders author application form requiring portfolio links, sample writing, and domain expertise selection.
- **`submit(Request $request)`**: Validates submission, inserts record into `author_applications` table with status `pending`, and notifies editorial staff.
- **`status()`**: Displays application review status (`pending`, `approved`, `rejected`) to the applicant.

---

## 5. `AuthorProfileController`
- **Location**: [`app/Controllers/AuthorProfileController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/AuthorProfileController.php)
- **Primary Responsibilities**: Renders public author profiles, portfolio showcases, and follower interactions.

### Key Action Methods:
- **`show(Request $request, array $params)`**: Fetches author profile by slug (`/author/{slug}`). Displays author biography, verification badges, reputation level, published articles, submitted tools/prompts/GPTs, and follower count.
- **`followers(Request $request, array $params)`**: Displays paginated list of members following the specified author.

---

## 6. `AuthorStudioController`
- **Location**: [`app/Controllers/AuthorStudioController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/AuthorStudioController.php)
- **Access Guard**: `Auth::requireAuthor()`
- **Primary Responsibilities**: Author creator studio for drafting, publishing, managing co-authors, tracking performance analytics, and viewing credit earnings.

### Key Action Methods:
- **`dashboard()`**: Author dashboard overview showing total article views, upvotes, current author tier (`AuthorTierEngine`), promo credit balance, and draft items.
- **`articles()` / `createArticle()` / `saveArticle()` / `editArticle()` / `deleteArticle()`**: Complete article drafting and self-publishing workspace with DOCX import support.
- **`tools()` / `createTool()` / `saveTool()`**: Tool directory submission workspace.
- **`prompts()` / `createPrompt()` / `savePrompt()`**: Prompt directory submission workspace.
- **`gpts()` / `createGpt()` / `saveGpt()`**: Custom GPT submission workspace.
- **`analytics()`**: Detailed content analytics broken down by views, reader demographics, traffic sources, and performance charts.
- **`earnings()`**: Earnings and credit ledger panel showing tipping income, referral bonuses, and payout history.
- **`coAuthors()` / `inviteCoAuthor()`**: Co-author management for collaborative writing.

---

## 7. `BlogController`
- **Location**: [`app/Controllers/BlogController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/BlogController.php)
- **Primary Responsibilities**: Public blog delivery, article listing, category/tag filtration, and single article reader views.

### Key Action Methods:
- **`index(Request $request)`**: Renders main blog index with featured post hero, category filters, and paginated article feed.
- **`show(Request $request, array $params)`**: Displays single article (`/article/{slug}`). Increments view counter, parses Markdown/HTML content, embeds author box, related articles, and comments thread.
- **`category(Request $request, array $params)`**: Filtered blog feed by category slug.
- **`tag(Request $request, array $params)`**: Filtered blog feed by tag slug.
- **`trending(Request $request)`**: Shows top trending articles sorted by view velocity and engagement score.

---

## 8. `DirectoryController`
- **Location**: [`app/Controllers/DirectoryController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/DirectoryController.php)
- **Primary Responsibilities**: Serves directory listings for AI Tools, Prompts, GPTs, Promotional Campaigns, Tech Jobs, and Affiliate Link Redirection.

### Key Action Methods:
- **`tools()` / `toolSingle()`**: AI Tools catalog with search, pricing filters (Free, Freemium, Paid), and tool detail pages.
- **`prompts()` / `promptSingle()`**: Prompts library with copy-to-clipboard prompt templates.
- **`gpts()` / `gptSingle()`**: Custom GPTs directory with category tags.
- **`campaigns()` / `campaignSingle()` / `submitCampaignProject()` / `voteCampaignSubmission()`**: Promotional hackathons/campaigns where members submit projects and community members vote.
- **`jobs()` / `jobSingle()` / `applyJob()`**: Tech job board with job detail pages and applicant application submission.
- **`affiliateRedirect(Request $request, array $params)`**: Handles dynamic outbound links (`/go/{slug}`), records link click analytics in database, and performs HTTP 302 redirect.

---

## 9. `HomeController`
- **Location**: [`app/Controllers/HomeController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/HomeController.php)
- **Primary Responsibilities**: Renders home landing page, contact page, and general portal marketing sections.

### Key Action Methods:
- **`index()`**: Home page aggregating hero sections, trending tools, top articles, active campaigns, author leaderboards, and newsletter signup block.
- **`contact()` / `submitContact()`**: Public contact form rendering and contact email dispatch to site admins.

---

## 10. `InboxController`
- **Location**: [`app/Controllers/InboxController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/InboxController.php)
- **Access Guard**: `Auth::requireAuth()`
- **Primary Responsibilities**: Direct messaging / internal inbox system between members, authors, and admins.

### Key Action Methods:
- **`index()`**: Displays user's active conversation threads and message previews.
- **`thread(Request $request, array $params)`**: Renders specific conversation thread and handles sending new replies.

---

## 11. `PageController`
- **Location**: [`app/Controllers/PageController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/PageController.php)
- **Primary Responsibilities**: Renders static/dynamic CMS pages (About Us, Terms of Service, Privacy Policy, Cookie Policy, Help Center).

### Key Action Methods:
- **`show(Request $request, array $params)`**: Fetches CMS page by slug from `pages` table and renders layout.

---

## 12. `SearchController`
- **Location**: [`app/Controllers/SearchController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/SearchController.php)
- **Primary Responsibilities**: Global search engine querying articles, tools, GPTs, prompts, and authors simultaneously.

### Key Action Methods:
- **`search(Request $request)`**: Receives query parameter `q`, performs SQL `LIKE` or FULLTEXT searches across content tables, groups results by content type, and renders search result page.

---

## 13. `SupportController`
- **Location**: [`app/Controllers/SupportController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/SupportController.php)
- **Access Guard**: `Auth::requireAuth()`
- **Primary Responsibilities**: Customer support ticketing system.

### Key Action Methods:
- **`index()`**: Lists user's submitted support tickets and resolution statuses.
- **`create()` / `store()`**: Support ticket submission form.
- **`view(Request $request, array $params)` / `reply()`**: Viewing ticket conversation and submitting staff/user responses.

---

## 14. `TopicController`
- **Location**: [`app/Controllers/TopicController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/TopicController.php)
- **Primary Responsibilities**: Exploratory topic hub grouping articles, tools, and prompts by broad technical topics (e.g. Machine Learning, Web Development, DevOps).

### Key Action Methods:
- **`index()`**: Overview of all topics with item counts.
- **`show(Request $request, array $params)`**: Topic detail page displaying aggregated content associated with the specified topic.
