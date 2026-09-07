# TechScout Complete PHP Classes & JS Functions Catalog

This document provides a comprehensive, exhaustive listing of **all PHP classes, their methods, and signatures**, along with **all JavaScript functions** across the entire TechScout repository.

---

## Table of Contents
1. [Core Framework PHP Classes & Methods (`app/Core/`)](#1-core-framework-php-classes--methods-appcore)
2. [HTTP Controller PHP Classes & Action Methods (`app/Controllers/`)](#2-http-controller-php-classes--action-methods-appcontrollers)
3. [Data Models & Engine PHP Classes & Methods (`app/Models/`)](#3-data-models--engine-php-classes--methods-appmodels)
4. [Global Helper PHP Functions (`app/Core/helpers.php`)](#4-global-helper-php-functions-appcorehelpersphp)
5. [Client-Side JavaScript Functions (`assets/js/`)](#5-client-side-javascript-functions-assetsjs)

---

## 1. Core Framework PHP Classes & Methods (`app/Core/`)

### Class `App\Core\Auth`
- **Location**: [`app/Core/Auth.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Auth.php)
- **Methods**:
  - `public static check(): bool`: Validates current user session and session token in database.
  - `public static id(): ?int`: Returns authenticated user ID or `null`.
  - `public static user(): ?array`: Fetches user record joined with author profile.
  - `public static role(): string`: Returns active user role string (`super_admin`, `admin`, `editor`, `author`, `member`, `guest`).
  - `public static authorApplicationStatus(): ?string`: Checks author application state.
  - `public static hasRole(array $allowedRoles): bool`: Evaluates role hierarchy.
  - `public static isSuperAdmin(): bool`: Checks if user is super admin.
  - `public static isAdmin(): bool`: Checks if user has admin privileges.
  - `public static isEditor(): bool`: Checks if user has editor privileges.
  - `public static isAuthor(): bool`: Checks if user has author publishing permissions.
  - `public static requireAuth(string $redirect = '/auth/login'): void`: Middleware guard requiring authentication.
  - `public static requireAdmin(string $redirect = '/auth/login'): void`: Middleware guard requiring admin rights.
  - `public static requireAuthor(string $redirect = '/auth/login'): void`: Middleware guard requiring author status.
  - `public static loginUser(array $user): void`: Initializes session and database session record.
  - `public static logout(): void`: Clears session and revokes database tokens.
  - `public static impersonate(int $userId): void`: Initiates admin impersonation session.
  - `public static stopImpersonating(): void`: Ends impersonation and restores original admin session.
  - `public static isImpersonating(): bool`: Checks if current session is impersonated.

### Class `App\Core\BannerHelper`
- **Location**: [`app/Core/BannerHelper.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/BannerHelper.php)
- **Methods**:
  - `public static renderActiveBanner(): string`: Renders active site-wide announcement bar HTML.

### Class `App\Core\Config`
- **Location**: [`app/Core/Config.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Config.php)
- **Methods**:
  - `public static get(string $key, $default = null)`: Fetches config setting.
  - `public static set(string $key, $value): void`: Sets setting in memory.
  - `public static url(string $path = ''): string`: Appends path to base site URL.
  - `public static asset(string $path = ''): string`: Appends cache-busted path to static asset URL.

### Class `App\Core\ContentFilter`
- **Location**: [`app/Core/ContentFilter.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/ContentFilter.php)
- **Methods**:
  - `public static containsProfanity(string $text): bool`: Screens text for prohibited terms.
  - `public static maskProfanity(string $text): string`: Replaces bad words with asterisk masks.
  - `public static validate(string $text): array`: Validates content for spam signatures.

### Class `App\Core\CredentialsManager`
- **Location**: [`app/Core/CredentialsManager.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/CredentialsManager.php)
- **Methods**:
  - `public static get(string $key, $default = null)`: Fetches credential key from environment or file.
  - `public static parseFile(): array`: Parses `credentials.txt` line format.

### Class `App\Core\Database`
- **Location**: [`app/Core/Database.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Database.php)
- **Methods**:
  - `public static getConnection(): PDO`: Returns singleton PDO connection instance.
  - `public static query(string $sql, array $params = []): array`: Executes SQL query and returns all rows.
  - `public static fetch(string $sql, array $params = []): ?array`: Fetches single row.
  - `public static fetchAll(string $sql, array $params = []): array`: Fetches all rows.
  - `public static execute(string $sql, array $params = []): int`: Executes non-query statement and returns affected row count.
  - `public static lastInsertId(): string`: Returns last auto-increment ID.
  - `public static beginTransaction(): bool`: Starts database transaction.
  - `public static commit(): bool`: Commits transaction.
  - `public static rollBack(): bool`: Rolls back transaction.
  - `public static tableExists(string $tableName): bool`: Introspects database schema for table existence.

### Class `App\Core\DateHelper`
- **Location**: [`app/Core/DateHelper.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/DateHelper.php)
- **Methods**:
  - `public static timeAgo($datetime): string`: Formats timestamp as relative time string (e.g., `"2 hours ago"`).
  - `public static formatDate($datetime, string $format = 'M j, Y'): string`: Formats date string.
  - `public static formatDateTime($datetime): string`: Formats date and time string in PST.

### Class `App\Core\DocxParser`
- **Location**: [`app/Core/DocxParser.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/DocxParser.php)
- **Methods**:
  - `public static parseToHtml(string $filepath): string`: Unzips `.docx` binary and converts Word XML to HTML.
  - `public static extractText(string $filepath): string`: Extracts plain text body from Word document.

### Class `App\Core\ErrorLogger`
- **Location**: [`app/Core/ErrorLogger.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/ErrorLogger.php)
- **Methods**:
  - `public static register(): void`: Registers global PHP error, exception, and shutdown crash handlers.
  - `public static logException(\Throwable $e): void`: Formats stack trace and writes to error log file and database.
  - `public static logError(int $errno, string $errstr, string $errfile, int $errline): void`: Handles PHP warnings/errors.

### Class `App\Core\FeatureFlag`
- **Location**: [`app/Core/FeatureFlag.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/FeatureFlag.php)
- **Methods**:
  - `public static isEnabled(string $featureKey, ?int $userId = null): bool`: Evaluates feature flag status.
  - `public static getAll(): array`: Retrieves list of all configured feature flags.

### Class `App\Core\Firewall`
- **Location**: [`app/Core/Firewall.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Firewall.php)
- **Methods**:
  - `public static checkRateLimit(string $key, int $maxRequests = 60, int $decaySeconds = 60): bool`: Rate-limits request IP.
  - `public static isBannedIp(string $ip): bool`: Checks if client IP is present in `banned_ips` blacklist.
  - `public static banIp(string $ip, string $reason): bool`: Adds IP to blacklist.

### Class `App\Core\Mailer`
- **Location**: [`app/Core/Mailer.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Mailer.php)
- **Methods**:
  - `public static send(string $to, string $subject, string $bodyHtml, array $options = []): bool`: Compiles and sends email via SMTP or Python proxy.
  - `public static getSettings(): array`: Fetches mail settings from `site_settings`.
  - `public static getTemplates(): array`: Returns array of 20+ built-in email templates.
  - `public static compileTemplate(string $templateKey, array $data): array`: Replaces placeholders in HTML template.

### Class `App\Core\NotificationHelper`
- **Location**: [`app/Core/NotificationHelper.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/NotificationHelper.php)
- **Methods**:
  - `public static send(int $userId, string $type, string $title, string $message, ?string $actionUrl = null): bool`: Inserts in-app notification.
  - `public static getUnreadCount(int $userId): int`: Fetches unread notification count badge.
  - `public static getNotifications(int $userId, int $limit = 20): array`: Retrieves notifications list.
  - `public static markAsRead(int $notificationId, int $userId): bool`: Marks notification read.
  - `public static markAllAsRead(int $userId): bool`: Marks all user notifications read.

### Class `App\Core\Request`
- **Location**: [`app/Core/Request.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Request.php)
- **Methods**:
  - `public getMethod(): string`: Returns HTTP verb (`GET`, `POST`, etc.).
  - `public getPath(): string`: Returns cleaned URL path.
  - `public get(string $key, $default = null)`: Fetches sanitized GET parameter.
  - `public post(string $key, $default = null)`: Fetches sanitized POST parameter.
  - `public input(string $key, $default = null)`: Fetches parameter from POST or GET.
  - `public json(): array`: Parses JSON request payload.
  - `public isAjax(): bool`: Returns true if request is AJAX.

### Class `App\Core\Response`
- **Location**: [`app/Core/Response.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Response.php)
- **Methods**:
  - `public static json(array $data, int $statusCode = 200): void`: Emits JSON response.
  - `public static redirect(string $url, int $statusCode = 302): void`: Performs HTTP redirect.
  - `public static setStatusCode(int $code): void`: Sets HTTP status code.

### Class `App\Core\Router`
- **Location**: [`app/Core/Router.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Router.php)
- **Methods**:
  - `public get(string $path, array|callable $handler): void`: Registers GET route.
  - `public post(string $path, array|callable $handler): void`: Registers POST route.
  - `public addRoute(string $method, string $path, array|callable $handler): void`: Compiles route regex pattern.
  - `public dispatch(Request $request): void`: Matches request URI and executes controller action.

### Class `App\Core\Sanitizer`
- **Location**: [`app/Core/Sanitizer.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Sanitizer.php)
- **Methods**:
  - `public static clean(string $data): string`: Escapes HTML entities.
  - `public static cleanHtml(string $html): string`: Sanitizes HTML while preserving basic formatting.
  - `public static slug(string $text): string`: Converts string into URL-friendly slug.

### Class `App\Core\SeoEngine`
- **Location**: [`app/Core/SeoEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/SeoEngine.php)
- **Methods**:
  - `public static generateTags(array $metadata): string`: Generates HTML meta & OpenGraph tags.
  - `public static generateJsonLd(string $type, array $data): string`: Renders Schema.org JSON-LD scripts.

### Class `App\Core\Session`
- **Location**: [`app/Core/Session.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Session.php)
- **Methods**:
  - `public static start(): void`: Starts PHP session with secure options.
  - `public static set(string $key, $value): void`: Sets session value.
  - `public static get(string $key, $default = null)`: Fetches session value.
  - `public static has(string $key): bool`: Checks session key existence.
  - `public static remove(string $key): void`: Unsets session key.
  - `public static flash(string $key, string $message): void`: Sets flash message.
  - `public static getFlash(string $key): ?string`: Consumes flash message.
  - `public static csrfToken(): string`: Generates or returns CSRF token.
  - `public static verifyCsrfToken(?string $token): bool`: Validates CSRF token.

### Class `App\Core\SmtpClient`
- **Location**: [`app/Core/SmtpClient.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/SmtpClient.php)
- **Methods**:
  - `public static sendMail(string $to, string $subject, string $bodyHtml, array $headers = []): bool`: Performs raw socket SMTP delivery.

### Class `App\Core\SocialHelper`
- **Location**: [`app/Core/SocialHelper.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/SocialHelper.php)
- **Methods**:
  - `public static getShareLinks(string $url, string $title): array`: Builds social media sharing URLs.

### Class `App\Core\SvgHelper`
- **Location**: [`app/Core/SvgHelper.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/SvgHelper.php)
- **Methods**:
  - `public static render(string $name, string $class = 'w-5 h-5'): string`: Renders inline SVG icon string.

### Class `App\Core\TagHelper`
- **Location**: [`app/Core/TagHelper.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/TagHelper.php)
- **Methods**:
  - `public static parseTags(string $tagsString): array`: Splits and cleans tag string.
  - `public static renderCloud(array $tags): string`: Renders HTML tag cloud pill links.

### Class `App\Core\WebhookEngine`
- **Location**: [`app/Core/WebhookEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/WebhookEngine.php)
- **Methods**:
  - `public static trigger(string $event, array $payload): void`: Dispatches event webhooks with HMAC signatures.

---

## 2. HTTP Controller PHP Classes & Action Methods (`app/Controllers/`)

### Class `App\Controllers\AdminController`
- **Location**: [`app/Controllers/AdminController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/AdminController.php)
- **Methods**:
  - `dashboard(Request $request): void`
  - `articles(Request $request): void`
  - `saveArticle(Request $request): void`
  - `reviewArticle(Request $request, array $params): void`
  - `deleteArticle(Request $request, array $params): void`
  - `tools(Request $request): void`
  - `saveTool(Request $request): void`
  - `deleteTool(Request $request, array $params): void`
  - `prompts(Request $request): void`
  - `savePrompt(Request $request): void`
  - `deletePrompt(Request $request, array $params): void`
  - `gpts(Request $request): void`
  - `saveGpt(Request $request): void`
  - `deleteGpt(Request $request, array $params): void`
  - `campaigns(Request $request): void`
  - `saveCampaign(Request $request): void`
  - `gradeCampaignSubmission(Request $request): void`
  - `deleteCampaign(Request $request, array $params): void`
  - `categories(Request $request): void`
  - `saveCategory(Request $request): void`
  - `deleteCategory(Request $request, array $params): void`
  - `topics(Request $request): void`
  - `saveTopic(Request $request): void`
  - `deleteTopic(Request $request, array $params): void`
  - `contentQueue(Request $request): void`
  - `contentAction(Request $request, array $params): void`
  - `users(Request $request): void`
  - `userDetailJson(Request $request, array $params): void`
  - `createUser(Request $request): void`
  - `editUser(Request $request, array $params): void`
  - `deleteUser(Request $request, array $params): void`
  - `userAction(Request $request, array $params): void`
  - `broadcastNotification(Request $request): void`
  - `impersonateUser(Request $request, array $params): void`
  - `issueStrike(Request $request, array $params): void`
  - `pardonStrike(Request $request, array $params): void`
  - `mergeUsers(Request $request): void`
  - `quickResetLink(Request $request, array $params): void`
  - `clearSuspicious(Request $request, array $params): void`
  - `userAuditTrailJson(Request $request, array $params): void`
  - `setAuthorTier(Request $request, array $params): void`
  - `setCustomBadge(Request $request, array $params): void`
  - `bulkEmail(Request $request): void`
  - `revokeUserSessions(Request $request, array $params): void`
  - `mailCenter(Request $request): void`
  - `saveMailSettings(Request $request): void`
  - `sendCustomMail(Request $request): void`
  - `saveMailTemplate(Request $request): void`
  - `assignTemplateCredential(Request $request): void`
  - `batchSaveTemplateCredentials(Request $request): void`
  - `financialLedger(Request $request): void`
  - `adjustCredits(Request $request): void`
  - `processPayout(Request $request, array $params): void`
  - `securityLogs(Request $request): void`
  - `auditTrail(Request $request): void`
  - `databaseBackup(Request $request): void`
  - `triggerBackup(Request $request): void`
  - `restoreBackup(Request $request): void`

### Class `App\Controllers\ApiController`
- **Location**: [`app/Controllers/ApiController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/ApiController.php)
- **Methods**:
  - `getContent(Request $request): void`
  - `getContentSingle(Request $request, array $params): void`
  - `postComment(Request $request): void`
  - `postReaction(Request $request): void`
  - `search(Request $request): void`
  - `followAuthor(Request $request): void`
  - `getNotifications(Request $request): void`
  - `markNotificationRead(Request $request): void`

### Class `App\Controllers\AuthController`
- **Location**: [`app/Controllers/AuthController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/AuthController.php)
- **Methods**:
  - `showLogin(Request $request): void`
  - `login(Request $request): void`
  - `showRegister(Request $request): void`
  - `register(Request $request): void`
  - `logout(Request $request): void`
  - `showForgotPassword(Request $request): void`
  - `sendResetLink(Request $request): void`
  - `showResetPassword(Request $request): void`
  - `resetPassword(Request $request): void`
  - `showVerifyEmail(Request $request): void`
  - `verifyEmail(Request $request): void`
  - `resendVerifyEmail(Request $request): void`
  - `showTwoFactor(Request $request): void`
  - `twoFactor(Request $request): void`
  - `showOnboarding(Request $request): void`
  - `completeOnboarding(Request $request): void`

### Class `App\Controllers\AuthorApplicationController`
- **Location**: [`app/Controllers/AuthorApplicationController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/AuthorApplicationController.php)
- **Methods**:
  - `showForm(Request $request): void`
  - `submit(Request $request): void`
  - `status(Request $request): void`

### Class `App\Controllers\AuthorProfileController`
- **Location**: [`app/Controllers/AuthorProfileController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/AuthorProfileController.php)
- **Methods**:
  - `show(Request $request, array $params): void`
  - `followers(Request $request, array $params): void`

### Class `App\Controllers\AuthorStudioController`
- **Location**: [`app/Controllers/AuthorStudioController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/AuthorStudioController.php)
- **Methods**:
  - `dashboard(Request $request): void`
  - `articles(Request $request): void`
  - `createArticle(Request $request): void`
  - `saveArticle(Request $request): void`
  - `editArticle(Request $request, array $params): void`
  - `deleteArticle(Request $request, array $params): void`
  - `tools(Request $request): void`
  - `createTool(Request $request): void`
  - `saveTool(Request $request): void`
  - `prompts(Request $request): void`
  - `createPrompt(Request $request): void`
  - `savePrompt(Request $request): void`
  - `gpts(Request $request): void`
  - `createGpt(Request $request): void`
  - `saveGpt(Request $request): void`
  - `analytics(Request $request): void`
  - `earnings(Request $request): void`
  - `coAuthors(Request $request): void`
  - `inviteCoAuthor(Request $request): void`

### Class `App\Controllers\BlogController`
- **Location**: [`app/Controllers/BlogController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/BlogController.php)
- **Methods**:
  - `index(Request $request): void`
  - `show(Request $request, array $params): void`
  - `category(Request $request, array $params): void`
  - `tag(Request $request, array $params): void`
  - `trending(Request $request): void`

### Class `App\Controllers\DirectoryController`
- **Location**: [`app/Controllers/DirectoryController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/DirectoryController.php)
- **Methods**:
  - `tools(Request $request): void`
  - `toolSingle(Request $request, array $params): void`
  - `prompts(Request $request): void`
  - `promptSingle(Request $request, array $params): void`
  - `gpts(Request $request): void`
  - `gptSingle(Request $request, array $params): void`
  - `campaigns(Request $request): void`
  - `campaignSingle(Request $request, array $params): void`
  - `submitCampaignProject(Request $request, array $params): void`
  - `voteCampaignSubmission(Request $request, array $params): void`
  - `jobs(Request $request): void`
  - `jobSingle(Request $request, array $params): void`
  - `applyJob(Request $request, array $params): void`
  - `affiliateRedirect(Request $request, array $params): void`

### Class `App\Controllers\HomeController`
- **Location**: [`app/Controllers/HomeController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/HomeController.php)
- **Methods**:
  - `index(Request $request): void`
  - `contact(Request $request): void`
  - `submitContact(Request $request): void`

### Class `App\Controllers\InboxController`
- **Location**: [`app/Controllers/InboxController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/InboxController.php)
- **Methods**:
  - `index(Request $request): void`
  - `thread(Request $request, array $params): void`

### Class `App\Controllers\PageController`
- **Location**: [`app/Controllers/PageController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/PageController.php)
- **Methods**:
  - `show(Request $request, array $params): void`

### Class `App\Controllers\SearchController`
- **Location**: [`app/Controllers/SearchController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/SearchController.php)
- **Methods**:
  - `search(Request $request): void`

### Class `App\Controllers\SupportController`
- **Location**: [`app/Controllers/SupportController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/SupportController.php)
- **Methods**:
  - `index(Request $request): void`
  - `create(Request $request): void`
  - `store(Request $request): void`
  - `view(Request $request, array $params): void`
  - `reply(Request $request, array $params): void`

### Class `App\Controllers\TopicController`
- **Location**: [`app/Controllers/TopicController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/TopicController.php)
- **Methods**:
  - `index(Request $request): void`
  - `show(Request $request, array $params): void`

---

## 3. Data Models & Engine PHP Classes & Methods (`app/Models/`)

### Class `App\Models\Analytics`
- **Location**: [`app/Models/Analytics.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/Analytics.php)
- **Methods**:
  - `public static recordPageView(string $url, ?int $userId, string $ip, ?string $referrer): void`
  - `public static getAuthorDashboardStats(int $authorId): array`
  - `public static getSiteOverviewStats(): array`

### Class `App\Models\AuditLog`
- **Location**: [`app/Models/AuditLog.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/AuditLog.php)
- **Methods**:
  - `public static log(string $action, string $entityType, ?int $entityId = null, ?array $details = null): bool`
  - `public static getClientIp(): string`
  - `public static getRecentLogs(int $limit = 100): array`

### Class `App\Models\Author`
- **Location**: [`app/Models/Author.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/Author.php)
- **Methods**:
  - `public static getByUserId(int $userId): ?array`
  - `public static getBySlug(string $slug): ?array`
  - `public static addReputation(int $authorId, int $points): bool`
  - `public static updatePromoCredits(int $authorId, float $amount, string $reason): bool`

### Class `App\Models\AuthorTierEngine`
- **Location**: [`app/Models/AuthorTierEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/AuthorTierEngine.php)
- **Methods**:
  - `public static getTierHierarchy(): array`
  - `public static getTierProgression(): array`
  - `public static evaluateAuthorTier(int $authorId): array`

### Class `App\Models\CoAuthorEngine`
- **Location**: [`app/Models/CoAuthorEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/CoAuthorEngine.php)
- **Methods**:
  - `public static addCoAuthor(int $articleId, int $authorId, float $splitPercent): bool`
  - `public static getCoAuthors(int $articleId): array`
  - `public static removeCoAuthor(int $articleId, int $authorId): bool`

### Class `App\Models\Comment`
- **Location**: [`app/Models/Comment.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/Comment.php)
- **Methods**:
  - `public static getForContent(string $contentType, int $contentId): array`
  - `public static addComment(array $data): int`
  - `public static deleteComment(int $commentId): bool`

### Class `App\Models\Content`
- **Location**: [`app/Models/Content.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/Content.php)
- **Methods**:
  - `public static getLatestArticles(int $limit = 10): array`
  - `public static getTrendingContent(string $type, int $limit = 6): array`
  - `public static incrementViews(string $type, int $id): bool`
  - `public static find(string $type, int $id): ?array`

### Class `App\Models\CreatorReferral`
- **Location**: [`app/Models/CreatorReferral.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/CreatorReferral.php)
- **Methods**:
  - `public static trackConversion(int $referrerUserId, int $newUserId): bool`
  - `public static getReferrals(int $userId): array`

### Class `App\Models\CreatorTipping`
- **Location**: [`app/Models/CreatorTipping.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/CreatorTipping.php)
- **Methods**:
  - `public static sendTip(int $fromUserId, int $toAuthorId, float $amount, ?string $message = null): bool`
  - `public static getTipsReceived(int $authorId): array`

### Class `App\Models\DatabaseBackupManager`
- **Location**: [`app/Models/DatabaseBackupManager.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/DatabaseBackupManager.php)
- **Methods**:
  - `public static createBackup(): string`
  - `public static restoreBackup(string $filename): bool`
  - `public static listBackups(): array`
  - `public static deleteBackup(string $filename): bool`

### Class `App\Models\FinancialLedger`
- **Location**: [`app/Models/FinancialLedger.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/FinancialLedger.php)
- **Methods**:
  - `public static recordTransaction(int $userId, string $type, float $amount, string $description): int`
  - `public static getAuthorBalance(int $authorId): float`
  - `public static getPayoutHistory(int $userId): array`

### Class `App\Models\PersonaFollowerEngine`
- **Location**: [`app/Models/PersonaFollowerEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/PersonaFollowerEngine.php)
- **Methods**:
  - `public static follow(int $followerUserId, int $authorId): bool`
  - `public static unfollow(int $followerUserId, int $authorId): bool`
  - `public static isFollowing(int $followerUserId, int $authorId): bool`
  - `public static getFollowers(int $authorId): array`

### Class `App\Models\PlagiarismEngine`
- **Location**: [`app/Models/PlagiarismEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/PlagiarismEngine.php)
- **Methods**:
  - `public static checkSimilarity(string $text): float`
  - `public static generateHashes(string $text): array`

### Class `App\Models\RBAC`
- **Location**: [`app/Models/RBAC.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/RBAC.php)
- **Methods**:
  - `public static hasPermission(string $role, string $permission): bool`
  - `public static requirePermission(string $permission): void`
  - `public static getRolePermissions(string $role): array`

### Class `App\Models\SecurityAnomalyEngine`
- **Location**: [`app/Models/SecurityAnomalyEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/SecurityAnomalyEngine.php)
- **Methods**:
  - `public static detectAnomalies(string $ip, ?int $userId = null): array`
  - `public static logAnomaly(string $ip, string $type, string $details): void`

### Class `App\Models\SessionManager`
- **Location**: [`app/Models/SessionManager.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/SessionManager.php)
- **Methods**:
  - `public static registerSession(int $userId, string $token, string $ip, ?string $userAgent): void`
  - `public static revokeSession(int $sessionId): bool`
  - `public static terminateOtherSessions(int $userId, ?string $currentToken): void`
  - `public static getUserSessions(int $userId): array`

### Class `App\Models\SupportEngine`
- **Location**: [`app/Models/SupportEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/SupportEngine.php)
- **Methods**:
  - `public static createTicket(array $data): int`
  - `public static addMessage(int $ticketId, int $userId, string $message, bool $isStaff): bool`
  - `public static getUserTickets(int $userId): array`

### Class `App\Models\User`
- **Location**: [`app/Models/User.php`](file:///c:/xampp/htdocs/techscout.n/app/Models/User.php)
- **Methods**:
  - `public static all(int $limit = 50, int $offset = 0): array`
  - `public static find(int $id): ?array`
  - `public static findByEmail(string $email): ?array`
  - `public static findByUsername(string $username): ?array`
  - `public static create(array $data): int`
  - `public static updateProfile(int $userId, array $data): bool`
  - `public static setRole(int $userId, string $role): bool`
  - `public static updateStatus(int $id, string $status, ?string $suspendedUntil = null): bool`
  - `public static issueStrike(int $userId, string $reason, ?string $penaltyType = null, ?string $expiresAt = null, ?int $issuedBy = null, ?string $notes = null): array`

---

## 4. Global Helper PHP Functions (`app/Core/helpers.php`)

- **Location**: [`app/Core/helpers.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/helpers.php)
- **Functions**:
  - `e($string): string`: HTML entity escaping wrapper (`htmlspecialchars`).
  - `url(string $path = ''): string`: Prefixes path with site base URL.
  - `asset(string $path = ''): string`: Appends cache-busting timestamp query parameter to static assets.
  - `auth(): ?array`: Shorthand for `App\Core\Auth::user()`.
  - `session(string $key, $default = null)`: Shorthand for `App\Core\Session::get()`.
  - `old(string $key, $default = null)`: Form field sticky state recovery.

---

## 5. Client-Side JavaScript Functions (`assets/js/`)

### File `assets/js/main.js`
- **Location**: [`assets/js/main.js`](file:///c:/xampp/htdocs/techscout.n/assets/js/main.js)
- **Functions**:
  - `initSearchAutocomplete()`: Binds live input events to header search bar for `/api/v1/search` queries.
  - `toggleTheme()`: Toggles dark and light mode stylesheets and updates `localStorage`.
  - `handleUpvote(contentType, contentId, element)`: Sends AJAX reaction POST request and increments button counter.
  - `handleBookmark(contentType, contentId, element)`: Toggles user bookmark state via API.
  - `copyToClipboard(text, message)`: Writes string to system clipboard and triggers toast popup.
  - `showToast(message, type)`: Constructs and animates floating notification banner in UI.

### File `assets/js/content-engine.js`
- **Location**: [`assets/js/content-engine.js`](file:///c:/xampp/htdocs/techscout.n/assets/js/content-engine.js)
- **Functions**:
  - `initContentEngine()`: Binds directory filter listeners and category selectors.
  - `fetchDirectoryContent(filters)`: Async `fetch()` call requesting `/api/v1/content` payload.
  - `renderContentCards(data)`: Renders HTML cards for AI tools, prompts, GPTs, and articles.
  - `handleCategoryFilterChange(categorySlug)`: Pushes filter state to browser URL history and updates list.

### File `assets/js/admin_table_paginator.js`
- **Location**: [`assets/js/admin_table_paginator.js`](file:///c:/xampp/htdocs/techscout.n/assets/js/admin_table_paginator.js)
- **Functions**:
  - `initTablePaginator(tableId, options)`: Attaches client-side search bar and pagination controls to table.
  - `paginateTable(table, pageNumber, pageSize)`: Computes visible `<tr>` row ranges.
  - `filterTableBySearch(table, searchQuery)`: Hides non-matching table rows based on text query.
  - `sortTableByColumn(table, columnIndex, direction)`: Sorts table rows alphabetically or numerically.

### File `assets/js/lucide.min.js`
- **Location**: [`assets/js/lucide.min.js`](file:///c:/xampp/htdocs/techscout.n/assets/js/lucide.min.js)
- **Functions**:
  - `lucide.createIcons()`: Scans document DOM for elements containing `data-lucide` attribute and replaces them with inline SVG icons.
