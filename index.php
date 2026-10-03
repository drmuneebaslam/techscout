<?php
declare(strict_types=1);

// Direct High-Performance Favicon, Manifest & Service Worker Serving
if (isset($_SERVER['REQUEST_URI'])) {
    $cleanUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '';
    if (str_ends_with($cleanUri, 'favicon.ico')) {
        $icoPath = __DIR__ . '/favicon.ico';
        if (file_exists($icoPath)) {
            header('Content-Type: image/x-icon');
            header('Content-Length: ' . filesize($icoPath));
            header('Cache-Control: public, max-age=604800');
            readfile($icoPath);
            exit;
        }
    } elseif (str_ends_with($cleanUri, 'manifest.json')) {
        header('Content-Type: application/json; charset=utf-8');
        echo '{}';
        exit;
    } elseif (str_ends_with($cleanUri, 'sw.js')) {
        header('Content-Type: application/javascript; charset=utf-8');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        echo 'self.addEventListener("install", function() { self.skipWaiting(); }); self.addEventListener("activate", function() { self.registration.unregister(); });';
        exit;
    }
}

// Direct Mail Attachment Delivery & Streaming Engine (Prevents any 404 when clicking email attachments)
if (preg_match('~/(?:assets/|public/)?(?:uploads/mail|mail/attachment)/([^/?#]+)~i', $_SERVER['REQUEST_URI'] ?? '', $attMatches)) {
    $attFilename = basename(urldecode($attMatches[1]));
    $candidatePaths = [
        __DIR__ . '/assets/uploads/mail/' . $attFilename,
        __DIR__ . '/public/uploads/mail/' . $attFilename,
        __DIR__ . '/uploads/mail/' . $attFilename,
        __DIR__ . '/storage/uploads/mail/' . $attFilename
    ];
    foreach ($candidatePaths as $cp) {
        if (file_exists($cp) && is_file($cp)) {
            $ext = strtolower(pathinfo($cp, PATHINFO_EXTENSION));
            $mimes = [
                'pdf' => 'application/pdf',
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'webp' => 'image/webp',
                'gif' => 'image/gif',
                'zip' => 'application/zip',
                'txt' => 'text/plain',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'csv' => 'text/csv'
            ];
            $contentType = $mimes[$ext] ?? 'application/octet-stream';
            header('Content-Type: ' . $contentType);
            header('Content-Disposition: inline; filename="' . $attFilename . '"');
            header('Content-Length: ' . filesize($cp));
            header('Cache-Control: public, max-age=86400');
            readfile($cp);
            exit;
        }
    }
}

require_once __DIR__ . '/app/autoload.php';

// Register Global Error, Exception & Fatal Crash Logger
\App\Core\ErrorLogger::register();

// Standard Platform Timezone: Pakistani Standard Time (UTC+5 / Asia/Karachi)
date_default_timezone_set('UTC');

use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Core\Session;
use App\Core\Config;
use App\Controllers\HomeController;
use App\Controllers\BlogController;
use App\Controllers\DirectoryController;
use App\Controllers\SearchController;
use App\Controllers\AuthController;
use App\Controllers\AuthorStudioController;
use App\Controllers\AuthorApplicationController;
use App\Controllers\AdminController;
use App\Controllers\AuthorProfileController;
use App\Controllers\TopicController;
use App\Controllers\CategoryController;
use App\Controllers\ApiController;
use App\Controllers\SupportController;
use App\Controllers\InboxController;
use App\Controllers\PageController;
use App\Controllers\InstallController;
use App\Controllers\CustomApiController;

// Start secure session
Session::start();

// Local QA Visual Testing Session Helper (Restricted strictly to localhost)
if (!empty($_GET['qa_auth']) && in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
    if ($_GET['qa_auth'] === 'admin') {
        $qaUser = \App\Core\Database::fetch("SELECT * FROM users WHERE role IN ('admin', 'super_admin') LIMIT 1");
        if ($qaUser) \App\Core\Auth::loginUser($qaUser);
    } elseif ($_GET['qa_auth'] === 'author') {
        $qaAuthor = \App\Core\Database::fetch("SELECT * FROM users WHERE role = 'author' LIMIT 1");
        if ($qaAuthor) \App\Core\Auth::loginUser($qaAuthor);
    } elseif ($_GET['qa_auth'] === 'impersonate') {
        $qaAdmin = \App\Core\Database::fetch("SELECT * FROM users WHERE role IN ('admin', 'super_admin') LIMIT 1");
        if ($qaAdmin) {
            \App\Core\Auth::loginUser($qaAdmin);
            \App\Core\Auth::impersonate(2);
        }
    } elseif ($_GET['qa_auth'] === 'member') {
        $qaMember = \App\Core\Database::fetch("SELECT * FROM users WHERE role = 'member' LIMIT 1")
            ?: \App\Core\Database::fetch("SELECT * FROM users WHERE role != 'admin' LIMIT 1");
        if ($qaMember) \App\Core\Auth::loginUser($qaMember);
    }
}

// Dynamic Base URL Resolution: Supports reverse proxies (Cloudflare, CloudFront, InfinityFree) and multi-environment hosting
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
    || (!empty($_SERVER['HTTP_CLOUDFRONT_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_CLOUDFRONT_FORWARDED_PROTO']) === 'https')
    || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) === 'on')
    || (!empty($_SERVER['HTTP_CF_VISITOR']) && str_contains($_SERVER['HTTP_CF_VISITOR'], 'https'))
    || ((int)($_SERVER['SERVER_PORT'] ?? 0) === 443)
    || (isset($_SERVER['HTTP_HOST']) && str_contains($_SERVER['HTTP_HOST'], 'techscout.online'));
$protocol = $isHttps ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = trim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$detectedBaseUrl = $protocol . '://' . $host . ($scriptDir ? '/' . $scriptDir : '');

$baseUrl = $detectedBaseUrl;

// Safe Database Connectivity Verification
$dbHasConnection = \App\Core\Database::hasConnection();

if ($dbHasConnection) {
    try {
        $cmsDomain = \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'domain_app_url' LIMIT 1");
        $cmsUrl = !empty($cmsDomain['key_value']) ? rtrim(trim((string)$cmsDomain['key_value']), '/') : '';
        if ($isHttps && str_starts_with($cmsUrl, 'http://')) {
            $cmsUrl = 'https://' . substr($cmsUrl, 7);
        }
        $cmsHost = $cmsUrl ? parse_url($cmsUrl, PHP_URL_HOST) : '';
        $cmsPath = $cmsUrl ? trim(parse_url($cmsUrl, PHP_URL_PATH) ?? '', '/') : '';
        $detectedPath = trim($scriptDir, '/');

        // Only trust the database setting if host matches AND path matches current deployment.
        // If site was moved from a subfolder (e.g. /tech) to root, auto-heal database setting to root.
        if ($cmsUrl && $cmsHost && (
            strcasecmp($cmsHost, $host) === 0 ||
            (in_array($host, ['localhost', '127.0.0.1', '::1'], true) && in_array($cmsHost, ['localhost', '127.0.0.1', '::1'], true))
        ) && ($cmsPath === $detectedPath || empty($detectedPath))) {
            // If detectedPath is empty (site is at root) and cmsPath has /tech, heal to detectedBaseUrl
            if (empty($detectedPath) && !empty($cmsPath) && ($cmsPath === 'tech' || str_ends_with($cmsUrl, '/tech'))) {
                $baseUrl = $detectedBaseUrl;
                \App\Core\Database::execute("UPDATE site_settings SET key_value = ? WHERE key_name = 'domain_app_url'", [$detectedBaseUrl]);
                try {
                    \App\Core\Database::execute("UPDATE notifications SET action_url = REPLACE(action_url, '/tech/', '/') WHERE action_url LIKE '%/tech/%'");
                    \App\Core\Database::execute("UPDATE notifications SET action_url = REPLACE(action_url, 'funwithcoding.free.nf/tech', 'funwithcoding.free.nf') WHERE action_url LIKE '%funwithcoding.free.nf/tech%'");
                } catch (\Throwable $ne) {}
            } else {
                $baseUrl = $cmsUrl;
            }
        } else {
            // Auto-heal setting in database to match current environment seamlessly
            $baseUrl = $detectedBaseUrl;
            \App\Core\Database::execute("UPDATE site_settings SET key_value = ? WHERE key_name = 'domain_app_url'", [$detectedBaseUrl]);
        }
    } catch (\Throwable $e) {
        $baseUrl = $detectedBaseUrl;
    }
}
Config::set('app_url', $baseUrl);

// First-Run Web Installer & Database Recovery Auto-Detector
$lockPath = __DIR__ . '/storage/installed.lock';
$reqUri = $_SERVER['REQUEST_URI'] ?? '/';
$isStaticAsset = str_starts_with($reqUri, '/assets/') 
    || str_contains($reqUri, 'favicon.ico')
    || str_contains($reqUri, 'robots.txt');
$isInstallRoute = str_contains($reqUri, '/install');

// Check if core database tables actually exist
$isDbPopulated = false;
if ($dbHasConnection) {
    try {
        $pdo = \App\Core\Database::getConnection();
        $check = $pdo->query("SHOW TABLES LIKE 'contents'")->fetchAll();
        $isDbPopulated = !empty($check);
    } catch (\Throwable $e) {
        $isDbPopulated = false;
    }
}

// Platform is only truly installed if lock exists, database is connected, AND core tables exist
$isFullyInstalled = file_exists($lockPath) && $dbHasConnection && $isDbPopulated;

if (!$isFullyInstalled && !$isStaticAsset && !$isInstallRoute) {
    $redirectUrl = $baseUrl . '/install' . (!$dbHasConnection ? '?recovery=1' : '');
    header('Location: ' . $redirectUrl);
    exit;
}

$router = new Router();
$request = new Request();

// ----------------------------------------------------------------------------
// 0. WEB INSTALLER & SETUP WIZARD ROUTES
// ----------------------------------------------------------------------------
$router->get('/install', [InstallController::class, 'index']);
$router->post('/install/test-db', [InstallController::class, 'testDb']);
$router->post('/install/process', [InstallController::class, 'process']);

// ----------------------------------------------------------------------------
// 1. PUBLIC DISCOVERY & STATIC ROUTES
// ----------------------------------------------------------------------------
$router->get('/', [HomeController::class, 'index']);
$router->get('/contact', [HomeController::class, 'contact']);
$router->post('/contact', [HomeController::class, 'submitContact']);
$router->get('/sponsor', [HomeController::class, 'sponsor']);
$router->get('/advertise', [HomeController::class, 'sponsor']);
$router->post('/sponsor/submit-tool', [HomeController::class, 'submitSponsoredTool']);
$router->post('/sponsor/submit-post', [HomeController::class, 'submitSponsoredPost']);

$router->get('/ad/click/{id}', [HomeController::class, 'trackAdClick']);

// ----------------------------------------------------------------------------
// 2. BLOG & EDITORIAL ROUTES
// ----------------------------------------------------------------------------
$router->get('/blog', [BlogController::class, 'index']);
$router->get('/blog/latest', [BlogController::class, 'index']);
$router->get('/blog/trending', [BlogController::class, 'trending']);
$router->get('/blog/category/{slug}', [BlogController::class, 'category']);
$router->get('/tag/{slug}', [BlogController::class, 'tag']);
$router->get('/article/{slug}', [BlogController::class, 'show']);
$router->get('/articles/{slug}', [BlogController::class, 'show']);
$router->get('/articles', [BlogController::class, 'index']);

// ----------------------------------------------------------------------------
// 3. DIRECTORIES: TOOLS, GPTS, PROMPTS, CAMPAIGNS & AFFILIATES
// ----------------------------------------------------------------------------
$router->get('/topics', [CategoryController::class, 'legacyTopicsRedirect']);
$router->get('/topic/{slug}', [CategoryController::class, 'legacyTopicSingleRedirect']);
$router->get('/categories', [CategoryController::class, 'index']);
$router->get('/category/{slug}', [CategoryController::class, 'show']);
$router->post('/category/subscribe/{id}', [CategoryController::class, 'subscribe']);
$router->get('/category/subscribe/{id}', [CategoryController::class, 'subscribe']);

$router->get('/tools/categories', [DirectoryController::class, 'toolCategories']);
$router->get('/tools', [DirectoryController::class, 'tools']);
$router->get('/tools/category/{slug}', [DirectoryController::class, 'tools']);
$router->get('/tools/{slug}', [DirectoryController::class, 'toolSingle']);

$router->get('/prompts/images/categories', [DirectoryController::class, 'imagePromptCategories']);
$router->get('/prompts/categories', [DirectoryController::class, 'promptCategories']);
$router->get('/prompts', [DirectoryController::class, 'prompts']);
$router->get('/prompts/images', [DirectoryController::class, 'imagePrompts']);
$router->get('/prompts/images/{category}', [DirectoryController::class, 'imagePrompts']);
$router->get('/prompts/image-prompts', [DirectoryController::class, 'imagePrompts']);
$router->get('/prompts/category/{slug}', [DirectoryController::class, 'prompts']);
$router->get('/prompts/{slug}', [DirectoryController::class, 'promptSingle']);

$router->get('/gpts/categories', [DirectoryController::class, 'gptCategories']);
$router->get('/gpts', [DirectoryController::class, 'gpts']);
$router->get('/gpts/category/{slug}', [DirectoryController::class, 'gpts']);
$router->get('/gpts/{slug}', [DirectoryController::class, 'gptSingle']);

$router->get('/campaigns', [DirectoryController::class, 'campaigns']);
$router->get('/campaigns/category/{slug}', [DirectoryController::class, 'campaigns']);
$router->get('/campaigns/{slug}', [DirectoryController::class, 'campaignSingle']);
$router->post('/campaigns/{slug}/submit', [DirectoryController::class, 'submitCampaignProject']);
$router->post('/campaigns/{slug}/vote/{id}', [DirectoryController::class, 'voteCampaignSubmission']);

$router->get('/jobs', [DirectoryController::class, 'jobs']);
$router->get('/jobs/{slug}', [DirectoryController::class, 'jobSingle']);
$router->post('/jobs/{slug}/apply', [DirectoryController::class, 'applyJob']);

$router->get('/category/{slug}', [CategoryController::class, 'show']);
$router->get('/go/gpt/{slug}', [DirectoryController::class, 'affiliateRedirect']);
$router->get('/go/tool/{slug}', [DirectoryController::class, 'affiliateRedirect']);
$router->get('/go/{slug}', [DirectoryController::class, 'affiliateRedirect']);
$router->get('/go', [DirectoryController::class, 'affiliateRedirect']);

// ----------------------------------------------------------------------------
// 4. GLOBAL SEARCH & TOPIC EXPLORER
// ----------------------------------------------------------------------------
$router->get('/search', [SearchController::class, 'search']);

// ----------------------------------------------------------------------------
// 5. AUTHENTICATION & ONBOARDING ROUTES
// ----------------------------------------------------------------------------
$router->get('/auth/login', [AuthController::class, 'showLogin']);
$router->post('/auth/login', [AuthController::class, 'login']);
$router->get('/auth/register', [AuthController::class, 'showRegister']);
$router->post('/auth/register', [AuthController::class, 'register']);
$router->get('/auth/logout', [AuthController::class, 'logout']);
$router->post('/auth/logout', [AuthController::class, 'logout']);

$router->get('/auth/forgot-password', [AuthController::class, 'showForgotPassword']);
$router->get('/forgot-password', [AuthController::class, 'showForgotPassword']);
$router->post('/forgot-password', [AuthController::class, 'sendResetLink']);
$router->get('/reset-password', [AuthController::class, 'showResetPassword']);
$router->post('/reset-password', [AuthController::class, 'resetPassword']);

$router->get('/verify-email', [AuthController::class, 'showVerifyEmail']);
$router->post('/verify-email', [AuthController::class, 'verifyEmail']);
$router->post('/verify-email/resend', [AuthController::class, 'resendVerifyEmail']);

$router->get('/two-factor-authentication', [AuthController::class, 'showTwoFactor']);
$router->post('/two-factor-authentication', [AuthController::class, 'twoFactor']);
$router->get('/two-factor', [AuthController::class, 'showTwoFactor']);

$router->get('/onboarding', [AuthController::class, 'showOnboarding']);
$router->post('/onboarding/complete', [AuthController::class, 'completeOnboarding']);

// User Security, Password & Session Routes
$router->post('/user/change-password', [AuthController::class, 'changePassword']);
$router->post('/user/sessions/revoke/{id}', [AuthController::class, 'revokeSession']);
$router->post('/user/sessions/terminate-others', [AuthController::class, 'terminateOtherSessions']);

// Member Account Settings, Profile Hub & Session Management
$router->get('/account/settings', [AuthController::class, 'accountSettings']);
$router->get('/account/profile', [AuthController::class, 'accountProfile']);
$router->post('/account/profile/update', [AuthController::class, 'updateAccountProfile']);
$router->get('/auth/quick-login', [AuthController::class, 'quickLogin']);
$router->get('/auth/impersonate/exit', [AuthController::class, 'exitImpersonation']);
$router->get('/admin/impersonate/exit', [AuthController::class, 'exitImpersonation']);

// ----------------------------------------------------------------------------
// 5.1 AUTHOR APPLICATION & CREATOR STUDIO ROUTES (Must precede wildcard /author/{slug})
// ----------------------------------------------------------------------------
// Author Application Portal (Open to all authenticated members)
$router->get('/author/apply', [AuthorApplicationController::class, 'showForm']);
$router->post('/author/apply', [AuthorApplicationController::class, 'submit']);
$router->get('/apply-author', [AuthorApplicationController::class, 'showForm']);

// 6. AUTHOR & CREATOR STUDIO ROUTES (Protected by 'author' middleware)
$router->get('/author', [AuthorStudioController::class, 'dashboard'], ['author']);
$router->get('/author/dashboard', [AuthorStudioController::class, 'dashboard'], ['author']);
$router->get('/author/editor', [AuthorStudioController::class, 'editor'], ['author']);
$router->get('/author/editor/{id}', [AuthorStudioController::class, 'editor'], ['author']);
$router->post('/author/save', [AuthorStudioController::class, 'save'], ['author']);
$router->post('/author/editor/save', [AuthorStudioController::class, 'save'], ['author']);
$router->post('/author/editor/import-docx', [AuthorStudioController::class, 'importDocx'], ['author']);
$router->get('/author/analytics', [AuthorStudioController::class, 'analytics'], ['author']);
$router->get('/author/analytics/content/{id}', [AuthorStudioController::class, 'contentAnalytics'], ['author']);
$router->post('/author/profile/update', [AuthorStudioController::class, 'updateProfile'], ['author']);
$router->post('/author/tip-wallets/save', [AuthorStudioController::class, 'saveTipWallets'], ['author']);
$router->post('/author/payouts/request', [AuthorStudioController::class, 'requestPayout'], ['author']);
$router->post('/author/payout-methods/save', [AuthorStudioController::class, 'savePayoutMethod'], ['author']);
$router->post('/author/payout-methods/delete/{id}', [AuthorStudioController::class, 'deletePayoutMethod'], ['author']);
$router->post('/author/payout-methods/set-default/{id}', [AuthorStudioController::class, 'setDefaultPayoutMethod'], ['author']);
$router->post('/author/collaborations/respond', [AuthorStudioController::class, 'respondCoAuthorInvite'], ['author']);
$router->post('/author/monetization/apply', [AuthorStudioController::class, 'applyMonetization'], ['author']);
$router->post('/author/content/delete/{id}', [AuthorStudioController::class, 'deleteDraft'], ['author']);
$router->post('/author/feedback/submit', [AuthorStudioController::class, 'submitFeedback'], ['author']);

// ----------------------------------------------------------------------------
// 6.1 PUBLIC AUTHOR & CREATOR PROFILES
// ----------------------------------------------------------------------------
$router->get('/author/profile/{slug}', [AuthorProfileController::class, 'show']);
$router->get('/author/{slug}', [AuthorProfileController::class, 'show']);
$router->get('/profile/{slug}', [AuthorProfileController::class, 'show']);
$router->get('/user/{slug}', [AuthorProfileController::class, 'show']);
$router->get('/u/{slug}', [AuthorProfileController::class, 'show']);

// ----------------------------------------------------------------------------
// 7. 100X ENHANCED ADMIN SYSTEM ROUTES (Complete CRUD for All Entities)
// ----------------------------------------------------------------------------
$router->get('/admin', [AdminController::class, 'dashboard'], ['admin']);
$router->get('/admin/dashboard', [AdminController::class, 'dashboard'], ['admin']);

// Author Applications Review Hub
$router->get('/admin/applications', [AdminController::class, 'applications'], ['admin']);
$router->post('/admin/applications/action/{id}', [AdminController::class, 'applicationAction'], ['admin']);

// Articles CRUD & Editorial Review Queue
$router->get('/admin/articles', [AdminController::class, 'articles'], ['admin']);
$router->post('/admin/articles/save', [AdminController::class, 'saveArticle'], ['admin']);
$router->post('/admin/articles/review/{id}', [AdminController::class, 'reviewArticle'], ['admin']);
$router->post('/admin/articles/delete/{id}', [AdminController::class, 'deleteArticle'], ['admin']);

// AI Tools CRUD
$router->get('/admin/tools', [AdminController::class, 'tools'], ['admin']);
$router->post('/admin/tools/save', [AdminController::class, 'saveTool'], ['admin']);
$router->post('/admin/tools/delete/{id}', [AdminController::class, 'deleteTool'], ['admin']);

// Prompts CRUD
$router->get('/admin/prompts', [AdminController::class, 'prompts'], ['admin']);
$router->post('/admin/prompts/save', [AdminController::class, 'savePrompt'], ['admin']);
$router->post('/admin/prompts/delete/{id}', [AdminController::class, 'deletePrompt'], ['admin']);

// GPTs CRUD
$router->get('/admin/gpts', [AdminController::class, 'gpts'], ['admin']);
$router->post('/admin/gpts/save', [AdminController::class, 'saveGpt'], ['admin']);
$router->post('/admin/gpts/delete/{id}', [AdminController::class, 'deleteGpt'], ['admin']);

// Campaigns CRUD
$router->get('/admin/campaigns', [AdminController::class, 'campaigns'], ['admin']);
$router->post('/admin/campaigns/save', [AdminController::class, 'saveCampaign'], ['admin']);
$router->post('/admin/campaigns/delete/{id}', [AdminController::class, 'deleteCampaign'], ['admin']);
$router->post('/admin/campaigns/submission/grade', [AdminController::class, 'gradeCampaignSubmission'], ['admin']);

// Topics CRUD
$router->get('/admin/topics', [AdminController::class, 'topics'], ['admin']);
$router->post('/admin/topics/save', [AdminController::class, 'saveTopic'], ['admin']);
$router->post('/admin/topics/delete/{id}', [AdminController::class, 'deleteTopic'], ['admin']);

// Categories CRUD
$router->get('/admin/categories', [AdminController::class, 'categories'], ['admin']);
$router->get('/admin/categories/save', function() { \App\Core\Response::redirect('/admin/categories'); }, ['admin']);
$router->post('/admin/categories/save', [AdminController::class, 'saveCategory'], ['admin']);
$router->post('/admin/categories/delete/{id}', [AdminController::class, 'deleteCategory'], ['admin']);

// Subcategories CRUD
$router->get('/admin/subcategories', [AdminController::class, 'subcategories'], ['admin']);
$router->get('/admin/subcategories/save', function() { \App\Core\Response::redirect('/admin/categories?tab=subcategories'); }, ['admin']);
$router->post('/admin/subcategories/save', [AdminController::class, 'saveSubcategory'], ['admin']);
$router->post('/admin/subcategories/delete/{id}', [AdminController::class, 'deleteSubcategory'], ['admin']);

// Content Review Queue & Actions
$router->get('/admin/content', [AdminController::class, 'contentQueue'], ['admin']);
$router->post('/admin/content/action/{id}', [AdminController::class, 'contentAction'], ['admin']);

// Users, Password Management, User Detail Hub & Broadcast Notifications
$router->get('/admin/users', [AdminController::class, 'users'], ['admin']);
$router->get('/admin/users/detail/{id}', [AdminController::class, 'userDetailJson'], ['admin']);
$router->post('/admin/users/create', [AdminController::class, 'createUser'], ['admin']);
$router->post('/admin/users/edit/{id}', [AdminController::class, 'editUser'], ['admin']);
$router->post('/admin/users/delete/{id}', [AdminController::class, 'deleteUser'], ['admin']);
$router->post('/admin/users/action/{id}', [AdminController::class, 'userAction'], ['admin']);
$router->post('/admin/users/broadcast-notification', [AdminController::class, 'broadcastNotification'], ['admin']);

// Administrative Identity Governance, Impersonation & Security Modules
$router->get('/admin/users/impersonate/{id}', [AdminController::class, 'impersonateUser'], ['admin']);
$router->post('/admin/users/impersonate/{id}', [AdminController::class, 'impersonateUser'], ['admin']);
$router->post('/admin/users/strike/{id}', [AdminController::class, 'issueStrike'], ['admin']);
$router->post('/admin/users/strike/pardon/{id}', [AdminController::class, 'pardonStrike'], ['admin']);
$router->post('/admin/users/merge', [AdminController::class, 'mergeUsers'], ['admin']);
$router->post('/admin/users/quick-reset-link/{id}', [AdminController::class, 'quickResetLink'], ['admin']);
$router->get('/admin/users/quick-reset-link/{id}', [AdminController::class, 'quickResetLink'], ['admin']);
$router->post('/admin/users/clear-suspicious/{id}', [AdminController::class, 'clearSuspicious'], ['admin']);
$router->get('/admin/users/audit-trail/{id}', [AdminController::class, 'userAuditTrailJson'], ['admin']);
$router->post('/admin/authors/set-tier/{id}', [AdminController::class, 'setAuthorTier'], ['admin']);
$router->post('/admin/authors/set-badge/{id}', [AdminController::class, 'setCustomBadge'], ['admin']);
$router->post('/admin/users/bulk-email', [AdminController::class, 'bulkEmail'], ['admin']);
$router->post('/admin/users/revoke-sessions/{id}', [AdminController::class, 'revokeUserSessions'], ['admin']);

// Creator & Author Applications Governance
$router->get('/admin/applications', [AdminController::class, 'applications'], ['admin']);
$router->post('/admin/applications/action/{id}', [AdminController::class, 'applicationAction'], ['admin']);

// Audience & Topic/Author Subscribers
$router->get('/admin/subscribers', [AdminController::class, 'subscribers'], ['admin']);
$router->get('/admin/subscribers/json', [AdminController::class, 'subscriberListJson'], ['admin']);
$router->post('/admin/subscribers/record/delete/{id}', [AdminController::class, 'deleteUserSubscription'], ['admin']);

// Moderation & Reports
$router->get('/admin/moderation', [AdminController::class, 'moderation'], ['admin']);
$router->post('/admin/moderation/action/{id}', [AdminController::class, 'reportAction'], ['admin']);
$router->post('/admin/moderation/update/{id}', [AdminController::class, 'updateReport'], ['admin']);
$router->post('/admin/moderation/strike/{id}', [AdminController::class, 'issueReportStrike'], ['admin']);
$router->post('/admin/moderation/message/{id}', [AdminController::class, 'messageReporter'], ['admin']);

// Support Tickets (Admin Queue)
$router->get('/admin/tickets', [AdminController::class, 'tickets'], ['admin']);

// Author Feedbacks
$router->get('/admin/author-feedbacks', [AdminController::class, 'authorFeedbacks'], ['admin']);
$router->post('/admin/author-feedbacks/update/{id}', [AdminController::class, 'updateAuthorFeedback'], ['admin']);

// Advertisements CRUD & Quick Status Toggle
$router->get('/admin/advertisements', [AdminController::class, 'advertisements'], ['admin']);
$router->post('/admin/advertisements/save', [AdminController::class, 'saveAdvertisement'], ['admin']);
$router->post('/admin/advertisements/delete/{id}', [AdminController::class, 'deleteAdvertisement'], ['admin']);
$router->post('/admin/advertisements/toggle/{id}', [AdminController::class, 'toggleAdStatus'], ['admin']);
$router->post('/admin/advertisements/settings', [AdminController::class, 'saveAdSettings'], ['admin']);
$router->post('/admin/advertisements/pricing-settings', [AdminController::class, 'savePricingSettings'], ['admin']);
$router->post('/admin/advertisements/verify-txid', [AdminController::class, 'verifySubmissionTxidOnChain'], ['admin']);
$router->post('/admin/advertisements/upload-image', [AdminController::class, 'uploadAdImage'], ['admin']);
$router->post('/admin/advertisements/tips/verify/{id}', [AdminController::class, 'verifyAuthorTip'], ['admin']);
$router->post('/admin/advertisements/tips/reject/{id}', [AdminController::class, 'rejectAuthorTip'], ['admin']);
$router->post('/admin/advertisements/payouts/process/{id}', [AdminController::class, 'processAuthorTipPayout'], ['admin']);
$router->post('/admin/sponsored/approve/{id}', [AdminController::class, 'approveSponsoredSubmission'], ['admin']);
$router->post('/admin/sponsored/reject/{id}', [AdminController::class, 'rejectSponsoredSubmission'], ['admin']);

// Ad Banner Manager & Sponsored Placements
$router->get('/admin/ad-manager', [AdminController::class, 'adManager'], ['admin']);
$router->post('/admin/ad-manager/save', [AdminController::class, 'saveAdSlot'], ['admin']);
$router->post('/admin/ad-manager/delete/{id}', [AdminController::class, 'deleteAdSlot'], ['admin']);
$router->post('/admin/ad-manager/toggle/{id}', [AdminController::class, 'toggleAdStatus'], ['admin']);

// Announcements & Broadcast Banner Ribbon CRUD
$router->get('/admin/announcements', [AdminController::class, 'announcements'], ['admin']);
$router->post('/admin/announcements/save', [AdminController::class, 'saveAnnouncement'], ['admin']);
$router->post('/admin/announcements/delete/{id}', [AdminController::class, 'deleteAnnouncement'], ['admin']);

// CMS Homepage Management (15 Discovery Modules)
$router->get('/admin/homepage', [AdminController::class, 'homepage'], ['admin']);
$router->post('/admin/homepage/save', [AdminController::class, 'saveHomepage'], ['admin']);

// CMS Media Library & DAM
$router->get('/admin/media', [AdminController::class, 'media'], ['admin']);
$router->post('/admin/media/upload', [AdminController::class, 'uploadMedia'], ['admin']);
$router->post('/admin/media/update/{id}', [AdminController::class, 'updateMedia'], ['admin']);
$router->post('/admin/media/delete/{id}', [AdminController::class, 'deleteMedia'], ['admin']);

// SEO & 301 Redirects CRUD
$router->get('/admin/seo', [AdminController::class, 'seo'], ['admin']);
$router->post('/admin/seo/redirects', [AdminController::class, 'addRedirect'], ['admin']);
$router->post('/admin/seo/settings/save', [AdminController::class, 'saveSeoSettings'], ['admin']);
$router->post('/admin/seo/delete/{id}', [AdminController::class, 'deleteRedirect'], ['admin']);

// Jobs Board CRUD & Applications
$router->get('/admin/jobs', [AdminController::class, 'jobs'], ['admin']);
$router->post('/admin/jobs/save', [AdminController::class, 'saveJob'], ['admin']);
$router->post('/admin/jobs/toggle/{id}', [AdminController::class, 'toggleJob'], ['admin']);
$router->post('/admin/jobs/delete/{id}', [AdminController::class, 'deleteJob'], ['admin']);
$router->get('/admin/jobs/applications', [AdminController::class, 'jobApplications'], ['admin']);
$router->post('/admin/jobs/applications/update/{id}', [AdminController::class, 'updateJobApplication'], ['admin']);
$router->post('/admin/jobs/applications/delete/{id}', [AdminController::class, 'deleteJobApplication'], ['admin']);

// Inbox & Contact Messages CRUD
$router->get('/admin/contact-messages', [AdminController::class, 'contactMessages'], ['admin']);
$router->post('/admin/contact-messages/reply/{id}', [AdminController::class, 'replyContactMessage'], ['admin']);
$router->post('/admin/contact-messages/delete/{id}', [AdminController::class, 'deleteContactMessage'], ['admin']);

// Mail Provider API, Multi-Provider Credentials & HTML Template Center
$router->get('/admin/mail', [AdminController::class, 'mailCenter'], ['admin']);
$router->get('/admin/mail/template/editor', [AdminController::class, 'mailTemplateEditor'], ['admin']);
$router->post('/admin/mail/settings', [AdminController::class, 'saveMailSettings'], ['admin']);
$router->post('/admin/mail/send', [AdminController::class, 'sendCustomMail'], ['admin']);
$router->post('/admin/mail/test', [AdminController::class, 'sendTestMail'], ['admin']);
$router->post('/admin/mail/template/save', [AdminController::class, 'saveMailTemplate'], ['admin']);
$router->post('/admin/mail/template/delete/{id}', [AdminController::class, 'deleteMailTemplate'], ['admin']);
$router->post('/admin/mail/template/assign-credential', [AdminController::class, 'assignTemplateCredential'], ['admin']);
$router->post('/admin/mail/templates/batch-save-credentials', [AdminController::class, 'batchSaveTemplateCredentials'], ['admin']);
$router->post('/admin/mail/logs/clear', [AdminController::class, 'clearMailLogs'], ['admin']);
$router->post('/admin/mail/log/delete/{id}', [AdminController::class, 'deleteMailLog'], ['admin']);
$router->get('/admin/mail/log/{id}', [AdminController::class, 'viewMailLog'], ['admin']);
$router->post('/admin/mail/credentials/save', [AdminController::class, 'saveMailCredential'], ['admin']);
$router->post('/admin/mail/credentials/duplicate/{id}', [AdminController::class, 'duplicateMailCredential'], ['admin']);
$router->post('/admin/mail/credentials/delete/{id}', [AdminController::class, 'deleteMailCredential'], ['admin']);
$router->post('/admin/mail/credentials/set-default/{id}', [AdminController::class, 'setDefaultMailCredential'], ['admin']);
$router->post('/admin/mail/credentials/test', [AdminController::class, 'testMailCredential'], ['admin']);
$router->post('/admin/mail/event-triggers/save', [AdminController::class, 'saveEventTriggerMap'], ['admin']);
$router->post('/admin/mail/event-triggers/test', [AdminController::class, 'testEventTrigger'], ['admin']);
$router->post('/admin/mail/trigger/create', [AdminController::class, 'createCustomTrigger'], ['admin']);
$router->post('/admin/mail/trigger/delete', [AdminController::class, 'deleteCustomTrigger'], ['admin']);

// Help Center & Knowledge Base Article CMS
$router->get('/admin/help-articles', [AdminController::class, 'helpArticles'], ['admin']);
$router->get('/admin/help-articles/create', [AdminController::class, 'editHelpArticle'], ['admin']);
$router->get('/admin/help-articles/edit/{id}', [AdminController::class, 'editHelpArticle'], ['admin']);
$router->post('/admin/help-articles/save', [AdminController::class, 'saveHelpArticle'], ['admin']);
$router->post('/admin/help-articles/delete/{id}', [AdminController::class, 'deleteHelpArticle'], ['admin']);

// Legal & Compliance Content Management CMS
$router->get('/admin/legal-pages', [AdminController::class, 'legalPages'], ['admin']);
$router->get('/admin/legal-pages/edit/{id}', [AdminController::class, 'editLegalPage'], ['admin']);
$router->post('/admin/legal-pages/save', [AdminController::class, 'saveLegalPage'], ['admin']);

// Feature Flags & Dynamic Capability Controls
$router->get('/admin/feature-flags', [AdminController::class, 'featureFlags'], ['admin']);
$router->post('/admin/feature-flags/save', [AdminController::class, 'saveFeatureFlags'], ['admin']);
$router->post('/admin/feature-flags/toggle', [AdminController::class, 'toggleFeatureFlag'], ['admin']);

// Outbound Webhooks & Integrations
$router->get('/admin/webhooks', [AdminController::class, 'webhooks'], ['admin']);
$router->post('/admin/webhooks/save', [AdminController::class, 'saveWebhook'], ['admin']);
$router->post('/admin/webhooks/delete/{id}', [AdminController::class, 'deleteWebhook'], ['admin']);
$router->post('/admin/webhooks/test/{id}', [AdminController::class, 'testWebhook'], ['admin']);
$router->post('/admin/webhooks/retry/{id}', [AdminController::class, 'retryWebhookDelivery'], ['admin']);

// Incoming Webhooks Engine with Live JSON Listener
$router->get('/admin/incoming-webhooks', [AdminController::class, 'incomingWebhooks'], ['admin']);
$router->post('/admin/incoming-webhooks/save', [AdminController::class, 'saveIncomingWebhook'], ['admin']);
$router->post('/admin/incoming-webhooks/save-mappings', [AdminController::class, 'saveIncomingWebhookMappings'], ['admin']);
$router->post('/admin/incoming-webhooks/delete/{id}', [AdminController::class, 'deleteIncomingWebhook'], ['admin']);
$router->post('/admin/incoming-webhooks/toggle/{id}', [AdminController::class, 'toggleIncomingWebhook'], ['admin']);
$router->post('/admin/incoming-webhooks/toggle-mode/{id}', [AdminController::class, 'toggleIncomingWebhookMode'], ['admin']);
$router->post('/admin/incoming-webhooks/clear-logs', [AdminController::class, 'clearIncomingWebhookLogs'], ['admin']);

// Custom API Endpoints Builder & Database Schema Introspector
$router->get('/admin/custom-api', [AdminController::class, 'customApiEndpoints'], ['admin']);
$router->post('/admin/custom-api/save', [AdminController::class, 'saveCustomApiEndpoint'], ['admin']);
$router->post('/admin/custom-api/toggle/{id}', [AdminController::class, 'toggleCustomApiEndpoint'], ['admin']);
$router->post('/admin/custom-api/delete/{id}', [AdminController::class, 'deleteCustomApiEndpoint'], ['admin']);

// Developer API Keys & Access Scopes
$router->get('/admin/api-keys', [AdminController::class, 'apiKeys'], ['admin']);
$router->post('/admin/api-keys/generate', [AdminController::class, 'generateApiKey'], ['admin']);
$router->post('/admin/api-keys/revoke/{id}', [AdminController::class, 'revokeApiKey'], ['admin']);

// Cyber Security Firewall & Threat Defense
$router->get('/admin/firewall', [AdminController::class, 'firewall'], ['admin']);
$router->get('/admin/security', [AdminController::class, 'firewall'], ['admin']);
$router->post('/admin/firewall/rules/save', [AdminController::class, 'saveFirewallRule'], ['admin']);
$router->post('/admin/firewall/rules/delete/{id}', [AdminController::class, 'deleteFirewallRule'], ['admin']);
$router->post('/admin/firewall/logs/clear', [AdminController::class, 'clearFirewallLogs'], ['admin']);

// Global Announcement Ribbons & Alerts
$router->get('/admin/announcements', [AdminController::class, 'announcements'], ['admin']);
$router->post('/admin/announcements/save', [AdminController::class, 'saveAnnouncement'], ['admin']);
$router->post('/admin/announcements/toggle/{id}', [AdminController::class, 'toggleAnnouncement'], ['admin']);
$router->post('/admin/announcements/delete/{id}', [AdminController::class, 'deleteAnnouncement'], ['admin']);

// Custom Dynamic Popup Maker
$router->get('/admin/popups', [AdminController::class, 'popups'], ['admin']);
$router->post('/admin/popups/save', [AdminController::class, 'savePopup'], ['admin']);
$router->post('/admin/popups/delete/{id}', [AdminController::class, 'deletePopup'], ['admin']);
$router->post('/admin/popups/toggle/{id}', [AdminController::class, 'togglePopup'], ['admin']);

// Content Blacklist & Auto-Moderation Filters
$router->get('/admin/content-filters', [AdminController::class, 'contentFilters'], ['admin']);
$router->post('/admin/content-filters/save', [AdminController::class, 'saveContentFilter'], ['admin']);
$router->post('/admin/content-filters/delete/{id}', [AdminController::class, 'deleteContentFilter'], ['admin']);

// SEO Studio, Dynamic Sitemap & Robots.txt
$router->get('/admin/seo-studio', [AdminController::class, 'seoStudio'], ['admin']);
$router->post('/admin/seo-studio/save', [AdminController::class, 'saveSeoSettings'], ['admin']);

// Database Health, Schema Diagnostics & Table Optimizer
$router->get('/admin/db-manager', [AdminController::class, 'dbManager'], ['admin']);
$router->post('/admin/db-manager/optimize', [AdminController::class, 'optimizeTables'], ['admin']);
$router->post('/admin/db-manager/optimize-table/{name}', [AdminController::class, 'optimizeTable'], ['admin']);
$router->post('/admin/db-manager/check-table/{name}', [AdminController::class, 'checkTable'], ['admin']);
$router->post('/admin/db-manager/repair-table/{name}', [AdminController::class, 'repairTable'], ['admin']);
$router->post('/admin/db-manager/auto-repair-schema', [AdminController::class, 'autoRepairSchema'], ['admin']);
$router->post('/admin/db-manager/query', [AdminController::class, 'executeDbQuery'], ['admin']);
$router->get('/admin/db-manager/table-rows', [AdminController::class, 'getTableRowsJson'], ['admin']);
$router->post('/admin/db-manager/custom-export', [AdminController::class, 'downloadCustomExport'], ['admin']);
$router->get('/admin/db-manager/export-sqlite', [AdminController::class, 'exportSqliteDb'], ['admin']);
$router->post('/admin/db-manager/migrations/run', [AdminController::class, 'runMigrations'], ['admin']);
$router->post('/admin/db-manager/migrations/rollback', [AdminController::class, 'rollbackMigration'], ['admin']);
$router->post('/admin/db-manager/garbage/purge', [AdminController::class, 'purgeOrphanedMedia'], ['admin']);
$router->post('/admin/db-manager/apply-index', [AdminController::class, 'applyIndexAdvisor'], ['admin']);

// System Error Logs, Crash Forensics & Real-time Diagnostics
$router->get('/admin/system-logs', [AdminController::class, 'systemLogs'], ['admin']);
$router->get('/admin/error-logs', [AdminController::class, 'systemLogs'], ['admin']);
$router->post('/admin/system-logs/resolve/{id}', [AdminController::class, 'resolveSystemLog'], ['admin']);
$router->post('/admin/system-logs/resolve-all', [AdminController::class, 'resolveAllSystemLogs'], ['admin']);
$router->post('/admin/system-logs/delete/{id}', [AdminController::class, 'deleteSystemLog'], ['admin']);
$router->post('/admin/system-logs/clear', [AdminController::class, 'clearSystemLogs'], ['admin']);

// CMS Master Settings & Analytics Telemetry HUD
$router->get('/admin/settings', [AdminController::class, 'settings'], ['admin']);
$router->post('/admin/settings/save', [AdminController::class, 'saveUnifiedSettings'], ['admin']);
$router->post('/admin/settings/launch-installer', [AdminController::class, 'launchInstaller'], ['admin']);
$router->get('/admin/analytics', [AdminController::class, 'analytics'], ['admin']);
$router->get('/admin/analytics/realtime', [AdminController::class, 'realtimeTelemetryJson'], ['admin']);
$router->get('/admin/analytics/content/{id}', [AdminController::class, 'contentAnalytics'], ['admin']);
$router->get('/admin/analytics/export', [AdminController::class, 'exportAnalyticsCsv'], ['admin']);

// Staff RBAC Roles & Granular Capability Matrix
$router->get('/admin/roles', [AdminController::class, 'rolesAndPermissions'], ['admin']);
$router->post('/admin/roles/save', [AdminController::class, 'saveRole'], ['admin']);
$router->post('/admin/roles/assign', [AdminController::class, 'assignUserRole'], ['admin']);

// CMS Newsletters & Email Broadcast Hub
$router->get('/admin/newsletters', [AdminController::class, 'newsletters'], ['admin']);
$router->post('/admin/newsletters/save', [AdminController::class, 'saveNewsletterIssue'], ['admin']);
$router->post('/admin/newsletters/delete/{id}', [AdminController::class, 'deleteNewsletterIssue'], ['admin']);
$router->post('/admin/newsletters/dispatch-test', [AdminController::class, 'dispatchNewsletterTest'], ['admin']);
$router->post('/admin/newsletters/send-message', [AdminController::class, 'sendNewsletterMessage'], ['admin']);
$router->post('/admin/newsletters/toggle-status/{id}', [AdminController::class, 'toggleSubscriberStatus'], ['admin']);
$router->post('/admin/subscribers/save', [AdminController::class, 'saveSubscriber'], ['admin']);
$router->post('/admin/subscribers/delete/{id}', [AdminController::class, 'deleteSubscriber'], ['admin']);
$router->get('/admin/subscribers/export', [AdminController::class, 'exportSubscribersCsv'], ['admin']);
$router->post('/admin/newsletters/retry-delivery/{id}', [AdminController::class, 'retryNewsletterDelivery'], ['admin']);

// Newsletter Open & Click Tracking (Public Unauthenticated Webhooks)
$router->get('/newsletter/track/open/{token}', [AdminController::class, 'trackNewsletterOpen']);
$router->get('/newsletter/track/click/{token}', [AdminController::class, 'trackNewsletterClick']);

// General Platform Mail Open & Click Tracking (Public Unauthenticated Webhooks)
$router->get('/mail/track/open/{token}', [AdminController::class, 'trackMailOpen']);
$router->get('/mail/track/click/{token}', [AdminController::class, 'trackMailClick']);

// CMS Navigation & Multi-Tier Layout Builder
$router->get('/admin/navigation', [AdminController::class, 'navigation'], ['admin']);
$router->post('/admin/navigation/save', [AdminController::class, 'saveNavigationItem'], ['admin']);
$router->post('/admin/navigation/delete/{id}', [AdminController::class, 'deleteNavigationItem'], ['admin']);
$router->post('/admin/navigation/reorder', [AdminController::class, 'reorderNavigationItems'], ['admin']);

// CMS Creator Revenue & Payouts Engine
$router->get('/admin/revenue', [AdminController::class, 'revenue'], ['admin']);
$router->post('/admin/revenue/grant-reward', [AdminController::class, 'grantAuthorReward'], ['admin']);
$router->post('/admin/revenue/adjust-balance', [AdminController::class, 'adjustAuthorBalance'], ['admin']);
$router->post('/admin/revenue/distribute-pool', [AdminController::class, 'distributeRevenuePool'], ['admin']);
$router->post('/admin/revenue/process-payout', [AdminController::class, 'processAuthorPayout'], ['admin']);
$router->post('/admin/revenue/delete-payout/{id}', [AdminController::class, 'deletePayout'], ['admin']);
$router->post('/admin/revenue/monetization/{action}/{id}', [AdminController::class, 'monetizationAction'], ['admin']);
$router->post('/admin/revenue/save-settings', [AdminController::class, 'saveRevenueSettings'], ['admin']);
$router->get('/admin/revenue/export', [AdminController::class, 'exportRevenueCsv'], ['admin']);

// Legal, Editorial, Guidelines, Help & Corporate Pages
$router->get('/legal', [PageController::class, 'legal']);
$router->get('/privacy', [PageController::class, 'privacy']);
$router->get('/terms', [PageController::class, 'terms']);
$router->get('/cookies', [PageController::class, 'cookies']);
$router->get('/community-guidelines', [PageController::class, 'communityGuidelines']);
$router->get('/copyright', [PageController::class, 'copyright']);
$router->get('/accessibility', [PageController::class, 'accessibility']);
$router->get('/acceptable-use', [PageController::class, 'acceptableUse']);
$router->get('/security', [PageController::class, 'securityPolicy']);
$router->get('/disclosure', [PageController::class, 'disclosure']);
$router->get('/qa-testing', function() {
    \App\Core\Response::view('pages/qa_testing');
});

$router->get('/help', [PageController::class, 'help']);
$router->get('/help/{slug}', [PageController::class, 'help']);
$router->get('/write/guidelines', [PageController::class, 'authorGuidelines']);
$router->get('/editorial', [PageController::class, 'editorial']);
$router->get('/about', [PageController::class, 'about']);
$router->get('/status', [PageController::class, 'systemStatus'], ['admin']);
$router->get('/api/v1/diagnostic-ping', [PageController::class, 'diagnosticPing'], ['admin']);

// Feeds & Dynamic Partitioned Sitemaps [TS-API-05, TS-API-07]
$router->get('/feed/prompts.xml', [\App\Controllers\FeedController::class, 'promptsXml']);
$router->get('/feed/articles.xml', [\App\Controllers\FeedController::class, 'articlesXml']);
$router->get('/feed/articles.json', [\App\Controllers\FeedController::class, 'articlesJson']);
$router->get('/feed.xml', [\App\Controllers\FeedController::class, 'articlesXml']);
$router->get('/sitemap.xml', [\App\Controllers\FeedController::class, 'sitemapIndex']);
$router->get('/sitemap-static.xml', [\App\Controllers\FeedController::class, 'sitemapStatic']);
$router->get('/sitemap-categories.xml', [\App\Controllers\FeedController::class, 'sitemapCategories']);
$router->get('/sitemap-articles.xml', [\App\Controllers\FeedController::class, 'sitemapArticles']);
$router->get('/sitemap-tools.xml', [\App\Controllers\FeedController::class, 'sitemapTools']);
$router->get('/sitemap-prompts.xml', [\App\Controllers\FeedController::class, 'sitemapPrompts']);
$router->get('/sitemap-gpts.xml', [\App\Controllers\FeedController::class, 'sitemapGpts']);
$router->get('/sitemap-{type}-{page}.xml', [\App\Controllers\FeedController::class, 'sitemapPartition']);


// System, Audit Logs & Backup Exporter
$router->get('/admin/audit-logs', [AdminController::class, 'auditLogs'], ['admin']);
$router->post('/admin/audit-logs/delete/{id}', [AdminController::class, 'deleteAuditLog'], ['admin']);
$router->get('/admin/system', [AdminController::class, 'system'], ['admin']);
$router->post('/admin/system/settings', [AdminController::class, 'saveSettings'], ['admin']);
$router->get('/admin/system/backup', [AdminController::class, 'exportBackup'], ['admin']);
$router->post('/admin/system/deploy-pull', [AdminController::class, 'deployPull'], ['admin']);

// Custom Dynamic Popup Maker & Conversion Banners
$router->get('/admin/popups', [AdminController::class, 'popups'], ['admin']);
$router->post('/admin/popups/save', [AdminController::class, 'savePopup'], ['admin']);
$router->post('/admin/popups/delete/{id}', [AdminController::class, 'deletePopup'], ['admin']);
$router->post('/admin/popups/toggle/{id}', [AdminController::class, 'togglePopup'], ['admin']);

// Incoming Webhooks Live JSON Listener Hub
$router->get('/admin/incoming-webhooks', [AdminController::class, 'incomingWebhooks'], ['admin']);
$router->post('/admin/incoming-webhooks/save', [AdminController::class, 'saveIncomingWebhook'], ['admin']);
$router->post('/admin/incoming-webhooks/delete/{id}', [AdminController::class, 'deleteIncomingWebhook'], ['admin']);
$router->post('/admin/incoming-webhooks/toggle/{id}', [AdminController::class, 'toggleIncomingWebhook'], ['admin']);
$router->post('/admin/incoming-webhooks/test/{id}', [AdminController::class, 'testIncomingWebhook'], ['admin']);

// Engagement & Telemetry Simulator Hub
$router->get('/admin/engagement-studio', [AdminController::class, 'engagementStudio'], ['admin']);
$router->post('/admin/engagement-studio/simulate', [AdminController::class, 'simulateEngagement'], ['admin']);
$router->get('/api/v1/content/preview/{id}', [ApiController::class, 'contentPreview'], ['admin']);
$router->get('/api/v1/auth/check-availability', [ApiController::class, 'checkAuthAvailability']);
$router->get('/api/v1/geo/timezone', [ApiController::class, 'getIpTimezone']);


$router->post('/api/v1/telemetry/csp', [ApiController::class, 'cspTelemetry']);

// Dynamic Robots.txt
$router->get('/robots.txt', function() {
    header('Content-Type: text/plain; charset=utf-8');
    echo \App\Core\SeoEngine::getRobotsTxt();
    exit;
});

// ----------------------------------------------------------------------------
// 7. USER NOTIFICATIONS, BOOKMARKS, INBOX & SUPPORT DESK
// ----------------------------------------------------------------------------
$router->get('/inbox', [InboxController::class, 'index']);
$router->get('/author/inbox', [InboxController::class, 'index']);
$router->get('/notifications', [HomeController::class, 'notifications']);
$router->get('/bookmarks', [HomeController::class, 'bookmarks']);
$router->get('/saved', [HomeController::class, 'bookmarks']);
$router->get('/history', [HomeController::class, 'readingHistory']);
$router->get('/support', [SupportController::class, 'index']);
$router->post('/support/ticket/create', [SupportController::class, 'create']);
$router->get('/support/ticket/{ticket_number}', [SupportController::class, 'show']);
$router->post('/support/ticket/{ticket_number}/reply', [SupportController::class, 'reply']);
$router->get('/api/v1/support/categories', [SupportController::class, 'apiCategories']);
$router->get('/admin/support/categories', [SupportController::class, 'adminCategories'], ['admin']);
$router->post('/admin/support/categories/save', [SupportController::class, 'saveCategory'], ['admin']);
$router->post('/admin/support/categories/{id}/delete', [SupportController::class, 'deleteCategory'], ['admin']);
$router->post('/admin/support/categories/{id}/subcategory/add', [SupportController::class, 'addSubcategory'], ['admin']);
$router->post('/admin/support/categories/{id}/subcategory/delete', [SupportController::class, 'deleteSubcategory'], ['admin']);

// ----------------------------------------------------------------------------
// 8. RESTful API v1 ENDPOINTS
// ----------------------------------------------------------------------------
// Enterprise Incoming Webhooks Engine (Admin endpoints & Universal Omni-Webhook)
$router->get('/admin/incoming-webhooks/live-stream/{slug}', [\App\Controllers\IncomingWebhookController::class, 'liveStream'], ['admin']);
$router->get('/admin/incoming-webhooks/sample/{table}', [\App\Controllers\IncomingWebhookController::class, 'samplePayload'], ['admin']);
$router->get('/admin/incoming-webhooks/inspect/{slug}', [\App\Controllers\IncomingWebhookController::class, 'inspectPayloads'], ['admin']);
$router->get('/api/v1/webhook/sample/{table}', [\App\Controllers\IncomingWebhookController::class, 'samplePayload']);
$router->get('/api/v1/webhook/inspect/{slug}', [\App\Controllers\IncomingWebhookController::class, 'inspectPayloads']);
$router->post('/admin/incoming-webhooks/simulate', [\App\Controllers\IncomingWebhookController::class, 'simulate'], ['admin']);
$router->post('/api/v1/webhook/simulate', [\App\Controllers\IncomingWebhookController::class, 'simulate']);
$router->get('/api/v1/webhook/live-stream/{slug}', [\App\Controllers\IncomingWebhookController::class, 'liveStream']);
$router->post('/api/v1/webhook/omni', [\App\Controllers\IncomingWebhookController::class, 'handleOmni']);
$router->get('/api/v1/webhook/omni', [\App\Controllers\IncomingWebhookController::class, 'handleOmni']);
$router->post('/api/v1/webhook/universal', [\App\Controllers\IncomingWebhookController::class, 'handleOmni']);
$router->get('/api/v1/webhook/universal', [\App\Controllers\IncomingWebhookController::class, 'handleOmni']);
$router->post('/api/v1/webhook/import', [\App\Controllers\IncomingWebhookController::class, 'handleOmni']);
$router->get('/api/v1/webhook/import', [\App\Controllers\IncomingWebhookController::class, 'handleOmni']);
$router->post('/api/v1/webhook/{endpoint_slug}', [\App\Controllers\IncomingWebhookController::class, 'handle']);
$router->get('/api/v1/webhook/{endpoint_slug}', [\App\Controllers\IncomingWebhookController::class, 'handle']);

// Dynamic Custom REST API Gateway (Single Record by ID, UUID, or Slug for Any Table)
$router->get('/api/v1/custom/{table}/schema', [\App\Controllers\CustomApiController::class, 'handleTableSchema']);
$router->get('/api/v1/custom/{table}/describe', [\App\Controllers\CustomApiController::class, 'handleTableSchema']);
$router->get('/api/v1/custom/{table}/{id}', [\App\Controllers\CustomApiController::class, 'handleRecord']);
$router->put('/api/v1/custom/{table}/{id}', [\App\Controllers\CustomApiController::class, 'handleRecord']);
$router->patch('/api/v1/custom/{table}/{id}', [\App\Controllers\CustomApiController::class, 'handleRecord']);
$router->delete('/api/v1/custom/{table}/{id}', [\App\Controllers\CustomApiController::class, 'handleRecord']);

// Dynamic Custom REST API Gateway & Table Schema Introspector (Collection / Endpoint)
$router->get('/api/v1/custom/{endpoint_slug}', [\App\Controllers\CustomApiController::class, 'handle']);
$router->post('/api/v1/custom/{endpoint_slug}', [\App\Controllers\CustomApiController::class, 'handle']);
$router->put('/api/v1/custom/{endpoint_slug}', [\App\Controllers\CustomApiController::class, 'handle']);
$router->patch('/api/v1/custom/{endpoint_slug}', [\App\Controllers\CustomApiController::class, 'handle']);
$router->delete('/api/v1/custom/{endpoint_slug}', [\App\Controllers\CustomApiController::class, 'handle']);

// Enterprise REST API Gateway (Bearer Auth Protected)
$router->get('/api/v1/tools', [ApiController::class, 'v1Tools']);
$router->get('/api/v1/prompts', [ApiController::class, 'v1Prompts']);
$router->get('/api/v1/gpts', [ApiController::class, 'v1Gpts']);
$router->get('/api/v1/articles', [ApiController::class, 'v1Articles']);
$router->get('/api/v1/admin/telemetry', [ApiController::class, 'v1AdminTelemetry']);

// Dynamic Popup Analytics
$router->post('/api/v1/popup-impression', [ApiController::class, 'v1PopupImpression']);
$router->post('/api/v1/popup-click', [ApiController::class, 'v1PopupClick']);

// EC2 Production Deploy & Migration Sync Trigger
$router->post('/api/v1/deploy/sync', [ApiController::class, 'v1DeploySync']);
$router->get('/api/v1/deploy/sync', [ApiController::class, 'v1DeploySync']);

$router->get('/api/v1/force-migrate', function() {
    $pdo = \App\Core\Database::getConnection();
    
    // Explicitly fix author_feedbacks table schema
    $pdo->exec("DROP TABLE IF EXISTS `author_feedbacks`");
    $pdo->exec("CREATE TABLE `author_feedbacks` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `author_id` bigint(20) unsigned NOT NULL,
      `subject` varchar(255) NOT NULL,
      `message` text NOT NULL,
      `status` enum('pending','reviewed','resolved') DEFAULT 'pending',
      `admin_notes` text DEFAULT NULL,
      `created_at` datetime DEFAULT current_timestamp(),
      `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      PRIMARY KEY (`id`),
      KEY `author_id` (`author_id`),
      CONSTRAINT `fk_author_feedbacks_user` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Re-run unapplied migrations
    $pdo->exec("CREATE TABLE IF NOT EXISTS `migrations` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `migration_name` varchar(255) NOT NULL,
      `batch` int(11) NOT NULL DEFAULT 1,
      `applied_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `idx_migration_name` (`migration_name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    
    $applied = $pdo->query("SELECT migration_name FROM migrations")->fetchAll(PDO::FETCH_COLUMN) ?: [];
    $migrationFiles = glob(__DIR__ . "/database/migrations/*.sql") ?: [];
    sort($migrationFiles);
    $maxBatch = (int)($pdo->query("SELECT MAX(batch) FROM migrations")->fetchColumn() ?: 0);
    $newBatch = $maxBatch + 1;
    $executed = 0;
    foreach ($migrationFiles as $mf) {
        $baseName = basename($mf, ".sql");
        if (!in_array($baseName, $applied, true)) {
            $sql = file_get_contents($mf);
            $stmts = array_filter(array_map("trim", explode(";", $sql)));
            foreach ($stmts as $s) {
                $sClean = trim(preg_replace("/^\s*--[^\n]*/m", "", $s));
                if (!empty($sClean)) {
                    try { $pdo->exec($sClean); } catch (\Throwable $e) {}
                }
            }
            $stmt = $pdo->prepare("INSERT INTO migrations (migration_name, batch, applied_at) VALUES (?, ?, NOW())");
            $stmt->execute([$baseName, $newBatch]);
            $executed++;
        }
    }
    echo json_encode(["status" => "ok", "executed" => $executed, "fixed_author_feedbacks" => true]);
});


$router->post('/api/v1/content/{id}/react', [ApiController::class, 'react']);
$router->get('/api/v1/content/{id}/comments/cursor', [ApiController::class, 'getCommentsCursor']);
$router->get('/api/v1/content/{id}/comments', [ApiController::class, 'getCommentsCursor']);
$router->get('/api/v1/comments/cursor', [ApiController::class, 'getCommentsCursor']);
$router->post('/api/v1/content/{id}/comments', [ApiController::class, 'addComment']);
$router->get('/api/v1/content/{id}/comments/poll', [ApiController::class, 'pollComments']);
$router->post('/api/v1/comments/{id}/upvote', [ApiController::class, 'upvoteComment']);
$router->post('/api/v1/comments/{id}/delete', [ApiController::class, 'deleteComment']);
$router->post('/api/v1/comments/{id}/pin', [ApiController::class, 'togglePinComment']);
$router->post('/api/v1/comments/{id}/react-emoji', [ApiController::class, 'reactCommentEmoji']);
$router->get('/api/v1/users/mentions', [ApiController::class, 'searchMentions']);
$router->post('/api/v1/tips/submit', [ApiController::class, 'submitCreatorTip']);
$router->post('/api/v1/polls/{id}/vote', [ApiController::class, 'votePoll']);
$router->post('/api/v1/prompts/{id}/copy', [ApiController::class, 'copyPrompt']);
$router->post('/api/v1/author/draft/autosave', [ApiController::class, 'autosaveDraft']);
$router->get('/api/v1/search', [ApiController::class, 'instantSearch']);
$router->get('/api/v1/search/internal-links', [ApiController::class, 'internalLinksSearch']);
$router->post('/api/v1/subscriptions/toggle', [ApiController::class, 'toggleSubscription']);
$router->post('/api/v1/author/import-docx', [ApiController::class, 'importDocx']);
$router->post('/api/v1/upload/image', [ApiController::class, 'uploadImage']);
$router->post('/api/v1/analytics/event', [ApiController::class, 'trackEvent']);
$router->post('/api/v1/content/read-ping', [ApiController::class, 'recordReadDwellPing']);
$router->post('/api/v1/track', [ApiController::class, 'trackEvent']);
$router->post('/api/v1/telemetry/client-error', [ApiController::class, 'logClientError']);
$router->post('/api/v1/telemetry/code-copy', [ApiController::class, 'logCodeCopy']);
$router->post('/api/v1/report', [ApiController::class, 'submitReport']);
$router->get('/api/v1/notifications', [ApiController::class, 'getNotifications']);
$router->post('/api/v1/notifications/read', [ApiController::class, 'markNotificationRead']);
$router->post('/api/v1/notifications/read-all', [ApiController::class, 'markAllNotificationsRead']);
$router->post('/api/v1/notifications/delete/{id}', [ApiController::class, 'deleteNotification']);
$router->post('/api/v1/notifications/clear-all', [ApiController::class, 'clearAllNotifications']);
$router->post('/notifications/read-all', [ApiController::class, 'markAllNotificationsRead']);
$router->post('/api/v1/bookmark', [ApiController::class, 'toggleBookmark']);
$router->post('/api/v1/bookmarks/toggle', [ApiController::class, 'toggleBookmark']);
$router->post('/api/v1/bookmarks', [ApiController::class, 'toggleBookmark']);
$router->post('/bookmark/toggle', [ApiController::class, 'toggleBookmark']);
$router->post('/api/v1/author/{id}/follow', [ApiController::class, 'toggleAuthorFollow']);
$router->get('/api/v1/author/{id}/followers', [ApiController::class, 'getAuthorFollowers']);
$router->get('/api/v1/author/{id}/following', [ApiController::class, 'getAuthorFollowing']);
$router->post('/api/v1/history/record', [ApiController::class, 'recordHistory']);
$router->post('/api/v1/history/clear', [ApiController::class, 'clearHistory']);
$router->post('/api/v1/history/delete/{id}', [ApiController::class, 'deleteHistoryItem']);
$router->get('/api/v1/analytics/entity/{id}', [ApiController::class, 'entityAnalyticsJson']);
$router->post('/api/v1/newsletter/subscribe', [ApiController::class, 'subscribeNewsletter']);
$router->post('/newsletter/subscribe', [ApiController::class, 'subscribeNewsletter']);
$router->post('/admin/newsletters/dispatch-batch/{id}', [AdminController::class, 'dispatchNewsletterBatch'], ['admin']);
$router->get('/admin/newsletters/dispatch-status/{id}', [AdminController::class, 'getNewsletterDispatchStatus'], ['admin']);

// Live Pricing & Currency Exchange API Endpoints [TS-API-PRICING]
$router->get('/api/v1/pricing/crypto-rates', [ApiController::class, 'getCryptoRates']);
$router->post('/api/v1/pricing/crypto-rates/refresh', [ApiController::class, 'getCryptoRates']);
$router->post('/api/v1/pricing/test', [ApiController::class, 'testPricingApi'], ['admin']);

// Blockchain Payment Transaction Verification API [TS-API-PAYMENT-VERIFY]
$router->get('/api/v1/payments/verify-txid', [ApiController::class, 'verifyPaymentTxid']);
$router->post('/api/v1/payments/verify-txid', [ApiController::class, 'verifyPaymentTxid']);

// Multi-session reading history & Re-read Engine
$router->post('/api/v1/history/init-session', [ApiController::class, 'initReadingSession']);

// Editorial Features: Content Pinning, Scratchpad & Custom Badges
$router->post('/admin/content/toggle-pin/{id}', [AdminController::class, 'togglePinContent'], ['admin']);
$router->post('/admin/content/save-internal-note', [AdminController::class, 'saveInternalNote'], ['admin']);
$router->get('/admin/editorial-badges', [AdminController::class, 'getEditorialBadges'], ['admin']);
$router->post('/admin/editorial-badges/create', [AdminController::class, 'createEditorialBadge'], ['admin']);
$router->post('/admin/content/assign-badge', [AdminController::class, 'assignEditorialBadge'], ['admin']);

// ----------------------------------------------------------------------------
// 9. ENTERPRISE SECURITY, BACKUPS, PLAGIARISM & ACTIVE SESSIONS
// ----------------------------------------------------------------------------
$router->post('/admin/content/scan-plagiarism/{id}', [AdminController::class, 'scanPlagiarism'], ['admin']);
$router->post('/admin/db-manager/backup', [AdminController::class, 'createBackup'], ['admin']);
$router->get('/admin/db-manager/download/{id}', [AdminController::class, 'downloadBackup'], ['admin']);
$router->post('/admin/db-manager/restore/{id}', [AdminController::class, 'restoreBackup'], ['admin']);
$router->post('/admin/db-manager/delete/{id}', [AdminController::class, 'deleteBackup'], ['admin']);
$router->post('/admin/tickets/macros/save', [AdminController::class, 'saveTicketMacro'], ['admin']);
$router->post('/admin/tickets/macros/delete/{id}', [AdminController::class, 'deleteTicketMacro'], ['admin']);
$router->post('/admin/tickets/notes/save', [AdminController::class, 'addTicketInternalNote'], ['admin']);
$router->post('/admin/security/unlock-ip', [AdminController::class, 'unlockIp'], ['admin']);
$router->post('/admin/security/resolve-anomaly/{id}', [AdminController::class, 'resolveAnomaly'], ['admin']);

// User Session Devices
$router->post('/user/sessions/revoke/{id}', [AuthController::class, 'revokeSession']);
$router->post('/user/sessions/terminate-others', [AuthController::class, 'terminateOtherSessions']);

// ----------------------------------------------------------------------------
// 10. PLATFORM MAINTENANCE MODE & READ-ONLY INTERCEPTOR
// ----------------------------------------------------------------------------
try {
    $maintSetting = \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'site_maintenance_mode'");
    $val = strtolower((string)($maintSetting['key_value'] ?? '0'));
    $isFullMaint = in_array($val, ['1', 'true', 'on', 'yes', 'full', 'lockout'], true);
    $isReadOnly = in_array($val, ['read_only', 'readonly'], true);

    $path = $request->getPath();
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $isAdmin = \App\Core\Auth::isAdmin();
    $isAdminPath = str_starts_with($path, '/admin') || str_starts_with($path, '/login') || str_starts_with($path, '/logout') || str_starts_with($path, '/assets') || str_starts_with($path, '/auth');
    
    // Simulate guest view test or preview
    if ($request->query('simulate_guest') === '1') {
        \App\Core\Session::remove('maintenance_bypass');
        $hasBypass = false;
        $isAdmin = false;
    } else {
        $hasBypass = \App\Core\Session::get('maintenance_bypass') || ($request->query('bypass') === 'techscout_ops');
    }
    
    // Check Client IP against Maintenance Allowed IPs whitelist [TS-INF-09]
    if (!$hasBypass) {
        $clientIp = Request::ip();
        $ipRow = \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'site_maintenance_allowed_ips'");
        $allowedIps = array_filter(array_map('trim', explode(',', (string)($ipRow['key_value'] ?? ''))));
        foreach ($allowedIps as $ip) {
            if ($ip === $clientIp || $ip === '*' || (str_contains($ip, '/') && \App\Core\Firewall::ipInRange($clientIp, $ip))) {
                $hasBypass = true;
                break;
            }
        }
    }

    if ($request->query('bypass') === 'techscout_ops') {
        \App\Core\Session::set('maintenance_bypass', true);
        $hasBypass = true;
    }

    $isPreview503 = ($request->query('preview_503') === '1' || $request->query('test_maint') === '1');

    if (($isFullMaint && !$isAdmin && !$isAdminPath && !$hasBypass) || $isPreview503) {
        http_response_code(503);
        header('Retry-After: 300');
        $msgRow = \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'site_maintenance_message'");
        $customMessage = $msgRow['key_value'] ?? 'TechScout core engineering systems are currently executing scheduled platform upgrades.';
        $countdownRow = \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'site_maintenance_countdown_minutes'");
        $countdownMinutes = (int)($countdownRow['key_value'] ?? 45);

        \App\Core\Response::view('errors/503', [
            'title' => 'Platform Upgrades Underway (HTTP 503) | TechScout',
            'customMessage' => $customMessage,
            'countdownMinutes' => $countdownMinutes
        ]);
        exit;
    } elseif ($isReadOnly && $method === 'POST' && !$isAdmin && !$isAdminPath && !$hasBypass) {
        if (str_starts_with($uri, '/api/')) {
            \App\Core\Response::json(['success' => false, 'message' => 'Platform is currently in Read-Only Maintenance Mode. Write actions are temporarily suspended.'], 423);
            exit;
        } else {
            \App\Core\Session::flash('error', 'Platform is currently in Read-Only Maintenance Mode. Write actions are temporarily suspended.');
            \App\Core\Response::redirect($request->referrer() ?: '/');
            exit;
        }
    }
} catch (\Throwable $e) {}

// Strict Rate-Limiting Engine on Public APIs and Search [TS-SEC-010]
try {
    $uriPath = $request->getUri();
    if (str_starts_with($uriPath, '/api/') && !str_starts_with($uriPath, '/api/v1/diagnostic-ping')) {
        \App\Core\RateLimiter::guard('api', 120, 60);
    } elseif (str_starts_with($uriPath, '/search')) {
        \App\Core\RateLimiter::guard('search', 40, 60);
    }
} catch (\Throwable $rlEx) {}

// Dispatch the current request
$router->dispatch($request);
