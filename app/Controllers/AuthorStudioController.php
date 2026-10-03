<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Auth;
use App\Core\Session;
use App\Core\Database;
use App\Core\Sanitizer;
use App\Core\NotificationHelper;
use App\Models\Author;
use App\Models\Content;
use App\Models\Analytics;
use App\Models\AuditLog;
use App\Models\FinancialLedger;

class AuthorStudioController {
    public function dashboard(Request $request): void {
        if (!Auth::isAuthor()) {
            Response::redirect('/author/apply');
            return;
        }
        $user = Auth::user();
        $authorId = $user['author_id'] ?? null;

        if (!$authorId) {
            $slug = Sanitizer::slug($user['username']);
            Database::execute("INSERT INTO authors (user_id, display_name, slug, reputation_score) VALUES (?, ?, ?, 100)", [$user['id'], $user['username'], $slug]);
            $authorId = (int)Database::lastInsertId();
        }

        $author = Database::fetch("SELECT a.*, u.username, u.email, u.role FROM authors a JOIN users u ON a.user_id = u.id WHERE a.id = ?", [$authorId]);
        
        // Extract configured creator crypto tip jar wallets
        $decodedSocials = [];
        if (!empty($author['social_links'])) {
            $decodedSocials = json_decode($author['social_links'], true) ?: [];
        }
        $author['tip_wallet_btc'] = $decodedSocials['tip_wallet_btc'] ?? null;
        $author['tip_wallet_eth'] = $decodedSocials['tip_wallet_eth'] ?? null;
        $author['tip_wallet_usdt'] = $decodedSocials['tip_wallet_usdt'] ?? null;
        $author['tip_wallet_sol'] = $decodedSocials['tip_wallet_sol'] ?? null;
        
        // Fetch all contents by author with categories and tags
        $contents = Database::query(
            "SELECT c.*, cat.name as category_name, cat.slug as category_slug 
             FROM contents c 
             LEFT JOIN categories cat ON c.category_id = cat.id 
             WHERE c.author_id = ? 
             ORDER BY c.updated_at DESC", 
            [$authorId]
        );

        // Group counts
        $publishedCount = 0;
        $inReviewCount = 0;
        $changesReqCount = 0;
        $draftCount = 0;
        $totalWords = 0;

        $changesRequiredItems = [];

        foreach ($contents as $c) {
            if ($c['status'] === 'published') $publishedCount++;
            elseif ($c['status'] === 'in_review') $inReviewCount++;
            elseif ($c['status'] === 'changes_required') {
                $changesReqCount++;
                $changesRequiredItems[] = $c;
            }
            elseif ($c['status'] === 'draft') $draftCount++;

            $totalWords += str_word_count(strip_tags($c['body_html'] ?? ''));
        }

        $stats = Database::fetch(
            "SELECT SUM(view_count) as total_views, SUM(reaction_count) as total_reactions, SUM(comment_count) as total_comments 
             FROM contents WHERE author_id = ?", 
            [$authorId]
        );

        // Progression & Reputation Tier Calculation
        $repScore = (int)($author['reputation_score'] ?? 100);
        if ($repScore < 250) {
            $tierName = 'Scout Initiate';
            $nextTier = 'Technical Scribe';
            $tierMax = 250;
            $tierMin = 0;
            $badgeColor = '#94a3b8';
        } elseif ($repScore < 750) {
            $tierName = 'Technical Scribe';
            $nextTier = 'Systems Architect';
            $tierMax = 750;
            $tierMin = 250;
            $badgeColor = '#38bdf8';
        } elseif ($repScore < 2000) {
            $tierName = 'Systems Architect';
            $nextTier = 'Vanguard Scout';
            $tierMax = 2000;
            $tierMin = 750;
            $badgeColor = '#a855f7';
        } elseif ($repScore < 5000) {
            $tierName = 'Vanguard Scout';
            $nextTier = 'Scout Fellow';
            $tierMax = 5000;
            $tierMin = 2000;
            $badgeColor = '#ec4899';
        } else {
            $tierName = 'Scout Fellow';
            $nextTier = 'Master Legend';
            $tierMax = 10000;
            $tierMin = 5000;
            $badgeColor = '#f59e0b';
        }

        $progressPercent = min(100, max(5, round((($repScore - $tierMin) / max(1, ($tierMax - $tierMin))) * 100)));
        $estReadTimeMinutes = max(1, round($totalWords / 200));
        $payouts = Database::query("SELECT * FROM author_payouts WHERE author_id = ? ORDER BY id DESC", [$authorId]);
        $topContents = Database::query("SELECT c.*, cat.name as category_name FROM contents c LEFT JOIN categories cat ON c.category_id = cat.id WHERE c.author_id = ? AND c.status = 'published' ORDER BY c.view_count DESC LIMIT 5", [$authorId]);
        $bookmarksCount = (int)(Database::fetch("SELECT COUNT(*) as c FROM bookmarks b JOIN contents c ON b.content_id = c.id WHERE c.author_id = ?", [$authorId])['c'] ?? 0);
        $stats['total_bookmarks'] = $bookmarksCount;

        // Author 7-Day Views Trend
        $dailyViewsRows = Database::query("
            SELECT DATE(event_time) as log_date, COUNT(*) as c 
            FROM analytics_events e 
            JOIN contents c ON e.content_id = c.id 
            WHERE c.author_id = ? AND e.event_time >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
            GROUP BY DATE(event_time) 
            ORDER BY log_date ASC
        ", [$authorId]);
        $authorDailyViews = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $authorDailyViews[$d] = 0;
        }
        foreach ($dailyViewsRows as $r) {
            if (isset($authorDailyViews[$r['log_date']])) {
                $authorDailyViews[$r['log_date']] = (int)$r['c'];
            }
        }

        $followers = Database::query("
            SELECT af.created_at as followed_at, u.id as user_id, u.username, COALESCE(a.display_name, u.username) as display_name, a.avatar_url,
                   a.id as author_id, a.slug as author_slug, a.headline, a.verification_badge, a.reputation_score
            FROM author_followers af
            JOIN users u ON af.user_id = u.id
            LEFT JOIN authors a ON a.user_id = u.id
            WHERE af.author_id = ?
            ORDER BY af.created_at DESC
            LIMIT 50
        ", [$authorId]);

        $following = Database::query("
            SELECT af.created_at as followed_at, a.id as author_id, a.display_name, a.slug as author_slug, a.avatar_url, a.headline, a.verification_badge, a.reputation_score,
                   (SELECT COUNT(*) FROM author_followers WHERE author_id = a.id) as target_follower_count
            FROM author_followers af
            JOIN authors a ON af.author_id = a.id
            WHERE af.user_id = ?
            ORDER BY af.created_at DESC
            LIMIT 50
        ", [(int)$author['user_id']]);

        $monetization = Author::getMonetizationStatus($authorId);
        $payoutMethods = FinancialLedger::getPayoutMethods($authorId);
        $defaultPayoutMethod = FinancialLedger::getDefaultPayoutMethod($authorId);
        $recentLedger = FinancialLedger::getAuthorLedger($authorId, 30);

        // Features Integration
        $ambassadorData = \App\Models\CreatorReferral::getAmbassadorStats($authorId);
        $collaborations = Database::query(
            "SELECT aca.*, art.title, art.slug, a.display_name as lead_name 
             FROM article_co_authors aca 
             JOIN contents art ON aca.article_id = art.id 
             JOIN authors a ON art.author_id = a.id 
             WHERE aca.author_id = ? AND aca.role != 'lead_author' 
             ORDER BY aca.created_at DESC", 
            [$authorId]
        );

        $payoutFeeSettings = FinancialLedger::getPayoutFeeSettings();
        $allowedPayoutMethods = FinancialLedger::getAllowedPayoutMethods();

        // Prompts Studio Data
        $authorPrompts = Database::querySilent(
            "SELECT c.*, COALESCE(p.prompt_type, 'text') as prompt_type, p.prompt_template, p.template_variables, p.target_models, p.copy_count, p.sample_output, cat.name as category_name 
             FROM contents c 
             JOIN prompts p ON c.id = p.content_id 
             LEFT JOIN categories cat ON c.category_id = cat.id 
             WHERE c.author_id = ? 
             ORDER BY c.created_at DESC", 
            [$authorId]
        ) ?: [];

        // Reader Feedback & Comments Data
        $recentComments = Database::querySilent(
            "SELECT cm.*, c.title as content_title, c.slug as content_slug, u.username, u.avatar_url 
             FROM comments cm 
             JOIN contents c ON cm.content_id = c.id 
             LEFT JOIN users u ON cm.user_id = u.id 
             WHERE c.author_id = ? 
             ORDER BY cm.created_at DESC 
             LIMIT 25", 
            [$authorId]
        ) ?: [];

        // Active Device Sessions
        $userSessions = Database::querySilent(
            "SELECT * FROM user_sessions WHERE user_id = ? ORDER BY last_activity DESC LIMIT 10", 
            [$user['id']]
        ) ?: [];

        // Recent Author Tips & Peer Patronage (95% creator share / 5% platform fee)
        $authorTips = [];
        try {
            $authorTips = Database::querySilent(
                "SELECT * FROM author_tips WHERE author_id = ? ORDER BY created_at DESC LIMIT 30", 
                [$authorId]
            ) ?: [];
        } catch (\Throwable $e) {}

        Response::view('author/dashboard', [
            'title' => 'Creator Studio & Intelligence Dashboard | TechScout',
            'user' => $user,
            'author' => $author,
            'stats' => $stats,
            'contents' => $contents,
            'payouts' => $payouts,
            'payoutMethods' => $payoutMethods,
            'defaultPayoutMethod' => $defaultPayoutMethod,
            'payoutFeeSettings' => $payoutFeeSettings,
            'allowedPayoutMethods' => $allowedPayoutMethods,
            'recentLedger' => $recentLedger,
            'topContents' => $topContents,
            'authorDailyViews' => $authorDailyViews,
            'followers' => $followers,
            'following' => $following,
            'publishedCount' => $publishedCount,
            'inReviewCount' => $inReviewCount,
            'changesReqCount' => $changesReqCount,
            'draftCount' => $draftCount,
            'totalWords' => $totalWords,
            'estReadTimeMinutes' => $estReadTimeMinutes,
            'tierName' => $tierName,
            'nextTier' => 'Senior Technical Contributor',
            'tierMax' => $tierMax,
            'badgeColor' => $badgeColor,
            'progressPercent' => $progressPercent,
            'ambassadorData' => $ambassadorData,
            'collaborations' => $collaborations,
            'changesRequiredItems' => $changesRequiredItems,
            'monetization' => $monetization,
            'authorPrompts' => $authorPrompts,
            'recentComments' => $recentComments,
            'userSessions' => $userSessions,
            'authorTips' => $authorTips
        ]);
    }

    public function editor(Request $request, array $params = []): void {
        if (!Auth::isAuthor()) {
            Response::redirect('/author/apply');
            return;
        }
        $user = Auth::user();
        $authorId = $user['author_id'] ?? null;
        $contentId = $params['id'] ?? null;
        $content = null;

        if ($contentId) {
            $content = Database::fetch("SELECT c.*, 
                                               art.reading_time_mins, art.difficulty_level,
                                               tool.pricing_model, tool.pricing_starting_at, tool.free_trial_available, tool.api_available, tool.website_url, tool.affiliate_url,
                                               gpt.openai_url, gpt.model_base, gpt.prompt_starters, gpt.use_case,
                                               p.prompt_template, p.template_variables, p.target_models, p.sample_output
                                        FROM contents c 
                                        LEFT JOIN articles art ON c.id = art.content_id 
                                        LEFT JOIN ai_tools tool ON c.id = tool.content_id 
                                        LEFT JOIN gpt_listings gpt ON c.id = gpt.content_id 
                                        LEFT JOIN prompts p ON c.id = p.content_id 
                                        WHERE c.id = ? AND c.author_id = ?", [$contentId, $authorId]);
            if (!$content) {
                Session::flash('error', 'Content not found or unauthorized.');
                Response::redirect('/author/dashboard');
                return;
            }
        }

        $author = Database::fetch("SELECT * FROM authors WHERE id = ?", [$authorId]);
        $categories = Database::query("SELECT * FROM categories ORDER BY sort_order ASC, name ASC");

        Response::view('author/editor', [
            'title' => ($content ? 'Edit: ' . $content['title'] : 'New Technical Publication') . ' | Creator Studio',
            'content' => $content,
            'author' => $author,
            'categories' => $categories
        ]);
    }

    public function save(Request $request): void {
        $user = Auth::user();
        $authorId = (int)$user['author_id'];
        $author = Database::fetch("SELECT * FROM authors WHERE id = ?", [$authorId]);

        $id = $request->post('id');
        $title = mb_substr(trim((string)$request->post('title', '')), 0, 150);
        $type = (string)$request->post('content_type', 'article');
        $categoryId = (int)$request->post('category_id');
        $summary = mb_substr(trim((string)$request->post('summary', '')), 0, 300);
        $bodyHtml = (string)$request->post('body_html', '');
        $featuredImage = image_url(trim((string)$request->post('featured_image_url', '')));
        if ($type === 'prompt') {
            $promptType = (string)$request->post('prompt_type', 'text');
            if ($promptType !== 'image') {
                $featuredImage = null; // Text prompts strictly have no image attached
            }
        }
        $action = (string)$request->post('action', 'draft'); // 'draft' or 'submit'

        if ($title === '') {
            Session::flash('error', 'Title is required.');
            Response::redirect('/author/editor' . ($id ? "/{$id}" : ''));
            return;
        }

        // Suspicious Activity Check: Auto-flag accounts publishing multiple submissions within seconds
        if (empty($id)) {
            $recentSubmissions = (int)(Database::fetch(
                "SELECT COUNT(*) as c FROM contents WHERE author_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 15 SECOND)",
                [$authorId]
            )['c'] ?? 0);

            if ($recentSubmissions >= 2) {
                \App\Models\User::flagSuspicious((int)$user['id'], 'Rapid publication flooding detected (multiple submissions within seconds)');
                Session::flash('error', 'Hold on! You are publishing too fast. Account flagged for security verification. Please wait before submitting.');
                Response::redirect('/author/editor');
                return;
            }
        }

        // Enforce publishing permissions
        $isAdmin = in_array($user['role'] ?? '', ['admin', 'super_admin', 'editor'], true);
        if (!$isAdmin) {
            if ($type === 'prompt' && empty($author['can_publish_prompts'])) {
                Session::flash('error', 'You do not have permission to publish Prompt templates. Please request access from an administrator.');
                Response::redirect('/author/editor' . ($id ? "/{$id}" : ''));
                return;
            }
            if ($type === 'gpt_listing' && empty($author['can_publish_gpts'])) {
                Session::flash('error', 'You do not have permission to publish Custom GPTs. Please request access from an administrator.');
                Response::redirect('/author/editor' . ($id ? "/{$id}" : ''));
                return;
            }
            if ($type === 'ai_tool' && empty($author['can_publish_tools'])) {
                Session::flash('error', 'You do not have permission to publish AI Tools. Please request access from an administrator.');
                Response::redirect('/author/editor' . ($id ? "/{$id}" : ''));
                return;
            }
        }

        $status = ($action === 'submit') ? 'in_review' : 'draft';
        $baseSlug = Sanitizer::slug($title);
        if ($baseSlug === '') $baseSlug = 'content-' . time();
        $slug = $baseSlug;
        $counter = 1;
        while (true) {
            $existing = Database::fetch(
                "SELECT id FROM contents WHERE slug = ? " . ($id ? "AND id != ?" : ""),
                $id ? [$slug, (int)$id] : [$slug]
            );
            if (!$existing) {
                break;
            }
            $counter++;
            $slug = "{$baseSlug}-{$counter}";
        }

        $readTime = Sanitizer::calculateReadTime($bodyHtml);

        // Quality score estimation
        $wordCount = str_word_count(strip_tags($bodyHtml));
        $qualityScore = min(100.0, 50.0 + ($wordCount > 500 ? 20 : 5) + (str_contains($bodyHtml, '<pre') ? 15 : 0) + (strlen($summary) > 50 ? 15 : 0));

        if ($id) {
            // Update existing
            Database::execute("UPDATE contents SET title = ?, slug = ?, content_type = ?, category_id = ?, summary = ?, body_html = ?, featured_image_url = ?, status = ?, quality_score = ?, updated_at = NOW() WHERE id = ? AND author_id = ?", [
                $title, $slug, $type, $categoryId, $summary, $bodyHtml, $featuredImage, $status, $qualityScore, $id, $authorId
            ]);
            $contentId = (int)$id;
        } else {
            // Insert new
            $uuid = bin2hex(random_bytes(16));
            Database::execute("INSERT INTO contents (uuid, content_type, author_id, category_id, title, slug, summary, body_html, featured_image_url, status, quality_score) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
                $uuid, $type, $authorId, $categoryId, $title, $slug, $summary, $bodyHtml, $featuredImage, $status, $qualityScore
            ]);
            $contentId = (int)Database::lastInsertId();
        }

        // Synchronize Arbitrary Content Tags
        \App\Core\TagHelper::syncTags($contentId, $request->post('tags', ''));

        // Entity Extension Records
        if ($type === 'article') {
            $difficulty = $request->post('difficulty_level', 'intermediate');
            
            // Extract Table of Contents JSON from HTML Headings
            $toc = [];
            if (preg_match_all('/<h([2-3])[^>]*>(.*?)<\/h\1>/i', $bodyHtml, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $level = (int)$match[1];
                    $hText = trim(strip_tags($match[2]));
                    if ($hText !== '') {
                        $toc[] = [
                            'level' => $level,
                            'title' => $hText,
                            'slug' => Sanitizer::slug($hText)
                        ];
                    }
                }
            }
            $tocJson = !empty($toc) ? json_encode($toc) : null;

            Database::execute("INSERT INTO articles (content_id, reading_time_mins, difficulty_level, table_of_contents) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE reading_time_mins = VALUES(reading_time_mins), difficulty_level = VALUES(difficulty_level), table_of_contents = VALUES(table_of_contents)", [
                $contentId, $readTime, $difficulty, $tocJson
            ]);
        } elseif ($type === 'prompt') {
            $promptType = (string)$request->post('prompt_type', 'text');
            if ($promptType !== 'image') $promptType = 'text';
            $promptTemplate = $request->post('prompt_template', strip_tags($bodyHtml));
            $variables = $request->post('template_variables') ? json_encode(explode(',', (string)$request->post('template_variables'))) : null;
            $models = json_encode(['ChatGPT', 'Claude', 'DeepSeek']);
            Database::execute("INSERT INTO prompts (content_id, prompt_type, prompt_template, template_variables, target_models) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE prompt_type = VALUES(prompt_type), prompt_template = VALUES(prompt_template), template_variables = VALUES(template_variables)", [
                $contentId, $promptType, $promptTemplate, $variables, $models
            ]);
        } elseif ($type === 'gpt_listing') {
            $openaiUrl = trim((string)$request->post('openai_url', 'https://chatgpt.com/g/'));
            $modelBase = trim((string)$request->post('model_base', 'GPT-4o'));
            $promptStarters = $request->post('prompt_starters') ? json_encode(array_filter(array_map('trim', explode("\n", (string)$request->post('prompt_starters'))))) : null;
            Database::execute("INSERT INTO gpt_listings (content_id, openai_url, model_base, prompt_starters) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE openai_url = VALUES(openai_url), model_base = VALUES(model_base), prompt_starters = VALUES(prompt_starters)", [
                $contentId, $openaiUrl, $modelBase, $promptStarters
            ]);
        } elseif ($type === 'ai_tool') {
            $websiteUrl = trim((string)$request->post('website_url', ''));
            $pricingModel = trim((string)$request->post('pricing_model', 'freemium'));
            $pricingStartingAt = (float)$request->post('pricing_starting_at', 0);
            $freeTrial = $request->post('free_trial_available') ? 1 : 0;
            $apiAvailable = $request->post('api_available') ? 1 : 0;
            $affiliateUrl = trim((string)$request->post('affiliate_url', ''));
            Database::execute("INSERT INTO ai_tools (content_id, website_url, pricing_model, pricing_starting_at, free_trial_available, api_available, affiliate_url) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE website_url = VALUES(website_url), pricing_model = VALUES(pricing_model), pricing_starting_at = VALUES(pricing_starting_at), free_trial_available = VALUES(free_trial_available), api_available = VALUES(api_available), affiliate_url = VALUES(affiliate_url)", [
                $contentId, $websiteUrl, $pricingModel, $pricingStartingAt, $freeTrial, $apiAvailable, $affiliateUrl
            ]);
        }

        if ($status === 'in_review') {
            Session::flash('success', 'Content submitted for editorial review! Our staff will verify and publish shortly.');
        } else {
            Session::flash('success', 'Draft saved successfully.');
        }

        Response::redirect('/author/dashboard');
    }

    public function updateProfile(Request $request): void {
        $user = Auth::user();
        $authorId = $user['author_id'] ?? null;

        if (!$authorId) {
            $existing = Database::fetch("SELECT id FROM authors WHERE user_id = ?", [$user['id']]);
            if ($existing) {
                $authorId = (int)$existing['id'];
            } else {
                $slug = Sanitizer::slug($user['username']);
                Database::execute("INSERT INTO authors (user_id, display_name, slug, reputation_score) VALUES (?, ?, ?, 100)", [$user['id'], $user['username'], $slug]);
                $authorId = (int)Database::lastInsertId();
            }
        }

        // Public Profile Fields with strict character limits
        $displayName = mb_substr(trim((string)$request->post('display_name', '')), 0, 60);
        $bio = mb_substr(trim((string)$request->post('bio', '')), 0, 500);
        $avatarUrl = image_url(trim((string)$request->post('avatar_url', '')));
        $coverImageUrl = image_url(trim((string)$request->post('cover_image_url', '')));
        $headline = mb_substr(trim((string)$request->post('headline', '')), 0, 100);
        $websiteUrl = mb_substr(trim((string)$request->post('website_url', '')), 0, 200);
        $githubUrl = mb_substr(trim((string)$request->post('github_url', '')), 0, 200);
        $twitterUrl = mb_substr(trim((string)$request->post('twitter_url', '')), 0, 200);
        $linkedinUrl = mb_substr(trim((string)$request->post('linkedin_url', '')), 0, 200);
        $discordHandle = mb_substr(trim((string)$request->post('discord_handle', '')), 0, 100);

        // Banking & Payout Settings
        $payoutMethod = (string)$request->post('payout_method', 'paypal');
        $paypalEmail = trim((string)$request->post('paypal_email', ''));
        $bankName = trim((string)$request->post('bank_name', ''));
        $bankAccountName = trim((string)$request->post('bank_account_name', ''));
        $bankAccountNumber = trim((string)$request->post('bank_account_number', ''));
        $bankRoutingOrSwift = trim((string)$request->post('bank_routing_or_swift', ''));
        $bankCountry = trim((string)$request->post('bank_country', ''));
        $cryptoWalletAddress = trim((string)$request->post('crypto_wallet_address', ''));
        $cryptoNetwork = (string)$request->post('crypto_network', 'ERC-20');
        $taxIdOrSsn = trim((string)$request->post('tax_id_or_ssn', ''));
        $taxCountry = trim((string)$request->post('tax_country', ''));

        // Creator Crypto Tip Jar & Patronage Payout Wallets (95% creator share / 5% platform fee)
        $tipWalletBtc = trim((string)$request->post('tip_wallet_btc', ''));
        $tipWalletEth = trim((string)$request->post('tip_wallet_eth', ''));
        $tipWalletUsdt = trim((string)$request->post('tip_wallet_usdt', ''));
        $tipWalletSol = trim((string)$request->post('tip_wallet_sol', ''));

        // Handle Avatar File Upload
        $avatarFile = $_FILES['avatar_file'] ?? null;
        if ($avatarFile && !empty($avatarFile['tmp_name']) && $avatarFile['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($avatarFile['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
                Session::flash('error', 'Avatar must be a valid image file (PNG, JPG, WEBP, GIF).');
                Response::redirect('/author/dashboard');
                return;
            }

            $avatarDir = __DIR__ . '/../../assets/uploads/avatars';
            if (!is_dir($avatarDir)) mkdir($avatarDir, 0755, true);

            $filename = 'avatar_' . $authorId . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            if (move_uploaded_file($avatarFile['tmp_name'], $avatarDir . '/' . $filename)) {
                $avatarUrl = url('/assets/uploads/avatars/' . $filename);
            }
        }

        // Handle Cover Image File Upload
        $coverFile = $_FILES['cover_file'] ?? null;
        if ($coverFile && !empty($coverFile['tmp_name']) && $coverFile['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($coverFile['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                $mediaDir = __DIR__ . '/../../assets/uploads/media';
                if (!is_dir($mediaDir)) mkdir($mediaDir, 0755, true);

                $filename = 'cover_' . $authorId . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                if (move_uploaded_file($coverFile['tmp_name'], $mediaDir . '/' . $filename)) {
                    $coverImageUrl = url('/assets/uploads/media/' . $filename);
                }
            }
        }

        if (empty($displayName)) {
            Session::flash('error', 'Display name cannot be blank.');
            Response::redirect('/author/dashboard');
            return;
        }

        // Parse Dynamic Social Links & Channels
        $dynamicSocialItems = [];
        if (!empty($_POST['social_platforms']) && is_array($_POST['social_platforms'])) {
            $platforms = $_POST['social_platforms'];
            $urls = $_POST['social_urls'] ?? [];
            $labels = $_POST['social_labels'] ?? [];
            for ($i = 0; $i < count($platforms); $i++) {
                $p = strtolower(trim((string)($platforms[$i] ?? 'custom')));
                $u = trim((string)($urls[$i] ?? ''));
                $lbl = trim((string)($labels[$i] ?? ''));
                if (!empty($u)) {
                    $dynamicSocialItems[] = [
                        'platform' => $p,
                        'url' => $u,
                        'label' => $lbl
                    ];
                }
            }
        }

        // Fallback legacy support if no dynamic items posted
        if (empty($dynamicSocialItems)) {
            if (!empty($websiteUrl)) $dynamicSocialItems[] = ['platform' => 'website', 'url' => $websiteUrl, 'label' => 'Website'];
            if (!empty($githubUrl)) $dynamicSocialItems[] = ['platform' => 'github', 'url' => $githubUrl, 'label' => 'GitHub'];
            if (!empty($twitterUrl)) $dynamicSocialItems[] = ['platform' => 'twitter', 'url' => $twitterUrl, 'label' => 'X (Twitter)'];
            if (!empty($linkedinUrl)) $dynamicSocialItems[] = ['platform' => 'linkedin', 'url' => $linkedinUrl, 'label' => 'LinkedIn'];
            if (!empty($discordHandle)) $dynamicSocialItems[] = ['platform' => 'discord', 'url' => $discordHandle, 'label' => 'Discord'];
        }

        $socialsData = [
            'items' => $dynamicSocialItems,
            'tax_id_or_ssn' => $taxIdOrSsn,
            'tax_country' => $taxCountry,
            'tip_wallet_btc' => $tipWalletBtc,
            'tip_wallet_eth' => $tipWalletEth,
            'tip_wallet_usdt' => $tipWalletUsdt,
            'tip_wallet_sol' => $tipWalletSol
        ];
        foreach ($dynamicSocialItems as $item) {
            if (in_array($item['platform'], ['website', 'github', 'twitter', 'linkedin', 'discord'])) {
                $socialsData[$item['platform']] = $item['url'];
            }
        }
        $socialsJson = json_encode($socialsData, JSON_UNESCAPED_SLASHES);

        Database::execute(
            "UPDATE authors SET 
                display_name = ?, 
                headline = ?, 
                bio = ?, 
                avatar_url = COALESCE(NULLIF(?, ''), avatar_url), 
                cover_image_url = COALESCE(NULLIF(?, ''), cover_image_url),
                social_links = ?,
                updated_at = NOW() 
             WHERE id = ?",
            [
                $displayName, 
                $headline, 
                $bio, 
                $avatarUrl, 
                $coverImageUrl,
                $socialsJson, 
                $authorId
            ]
        );

        // Sync with user record
        Database::execute("UPDATE users SET display_name = ?, headline = ?, avatar_url = COALESCE(NULLIF(?, ''), avatar_url) WHERE id = ?", [$displayName, $headline, $avatarUrl, $user['id']]);

        // Dispatch author_profile_updated event automation
        try {
            \App\Core\EventTriggerEngine::fire('author_profile_updated', [
                'email' => $user['email'],
                'name' => $displayName ?: $user['username'],
                'headline' => $headline ?: 'Verified Contributor',
                'action_url' => url('/author/' . ($author['slug'] ?? $user['username']))
            ]);
        } catch (\Throwable $e) {}

        Session::flash('success', 'Creator public profile, developer channels & crypto tip jar wallets saved successfully! 🌟');
        Response::redirect('/author/dashboard');
    }

    public function saveTipWallets(Request $request): void {
        $user = Auth::user();
        $authorId = $user['author_id'] ?? null;
        if (!$authorId) {
            $existing = Database::fetch("SELECT id FROM authors WHERE user_id = ?", [$user['id']]);
            $authorId = $existing ? (int)$existing['id'] : null;
        }
        if (!$authorId) {
            Response::json(['success' => false, 'error' => 'Author profile not found.'], 404);
            return;
        }

        $btc = trim((string)$request->post('tip_wallet_btc', ''));
        $eth = trim((string)$request->post('tip_wallet_eth', ''));
        $usdt = trim((string)$request->post('tip_wallet_usdt', ''));
        $sol = trim((string)$request->post('tip_wallet_sol', ''));

        // Sanity validation
        if ($btc && strlen($btc) < 20) {
            Response::json(['success' => false, 'error' => 'Invalid Bitcoin address format. Please enter a valid Native SegWit (bc1q), Taproot (bc1p), or Legacy address.'], 422);
            return;
        }
        if ($eth && (!str_starts_with(strtolower($eth), '0x') || strlen($eth) !== 42)) {
            Response::json(['success' => false, 'error' => 'Ethereum / EVM address must start with 0x and be exactly 42 characters long.'], 422);
            return;
        }
        if ($sol && (strlen($sol) < 32 || strlen($sol) > 44)) {
            Response::json(['success' => false, 'error' => 'Solana address must be a valid SPL base58 public key (32-44 characters).'], 422);
            return;
        }

        $author = Database::fetch("SELECT social_links FROM authors WHERE id = ?", [$authorId]);
        $socials = json_decode($author['social_links'] ?? '{}', true) ?: [];
        $socials['tip_wallet_btc'] = $btc;
        $socials['tip_wallet_eth'] = $eth;
        $socials['tip_wallet_usdt'] = $usdt;
        $socials['tip_wallet_sol'] = $sol;

        Database::execute(
            "UPDATE authors SET social_links = ?, updated_at = NOW() WHERE id = ?",
            [json_encode($socials, JSON_UNESCAPED_SLASHES), $authorId]
        );

        Response::json([
            'success' => true,
            'message' => 'Crypto payout addresses saved successfully! 95% of reader tips will be routed to these destination addresses.',
            'wallets' => [
                'btc' => $btc,
                'eth' => $eth,
                'usdt' => $usdt,
                'sol' => $sol
            ]
        ]);
    }

    public function requestPayout(Request $request): void {
        $user = Auth::user();
        $authorId = (int)$user['author_id'];
        $author = Database::fetch("SELECT * FROM authors WHERE id = ?", [$authorId]);

        $credits = (float)($author['promo_credits'] ?? 0);
        $amount = (float)$request->post('amount', 0);
        $methodId = (int)$request->post('payout_method_id', 0);
        $notes = trim((string)$request->post('notes', ''));

        // 1. Mandatory Linked Payout Method Check
        $linkedMethod = null;
        if ($methodId > 0) {
            $linkedMethod = Database::fetch("SELECT * FROM author_payout_methods WHERE id = ? AND author_id = ? AND status != 'disabled'", [$methodId, $authorId]);
        } else {
            $linkedMethod = FinancialLedger::getDefaultPayoutMethod($authorId);
        }

        if (!$linkedMethod) {
            Session::flash('error', '⚠️ Payout method required: You must link and verify a Bank Account, PayPal, or Crypto Wallet before requesting a withdrawal.');
            Response::redirect('/author/dashboard');
            return;
        }

        if ($amount <= 0 || $amount > $credits) {
            Session::flash('error', "Invalid payout amount requested. Available creator balance: \${$credits} USD.");
            Response::redirect('/author/dashboard');
            return;
        }

        if ($amount < 25.00) {
            Session::flash('error', 'Minimum cash withdrawal threshold is $25.00 USD.');
            Response::redirect('/author/dashboard');
            return;
        }

        // 2. Format verified destination string
        $methodType = $linkedMethod['method_type'];
        $destination = '';
        if ($methodType === 'paypal') {
            $destination = "PayPal: " . ($linkedMethod['account_email'] ?: $user['email']);
        } elseif ($methodType === 'bank_wire' || $methodType === 'iban_sepa') {
            $destination = "Bank: {$linkedMethod['bank_name']} (Holder: {$linkedMethod['account_holder_name']}) | IBAN/Acct: {$linkedMethod['account_number_or_iban']} | SWIFT: {$linkedMethod['routing_or_swift']}";
        } elseif ($methodType === 'crypto_usdc') {
            $destination = "USDC ({$linkedMethod['crypto_network']}): {$linkedMethod['crypto_wallet_address']}";
        } else {
            $details = [];
            if (!empty($linkedMethod['account_holder_name'])) $details[] = "Holder: " . $linkedMethod['account_holder_name'];
            if (!empty($linkedMethod['account_number_or_iban'])) $details[] = "Acct/ID: " . $linkedMethod['account_number_or_iban'];
            if (!empty($linkedMethod['account_email'])) $details[] = "Email: " . $linkedMethod['account_email'];
            if (!empty($linkedMethod['crypto_wallet_address'])) $details[] = "Addr: " . $linkedMethod['crypto_wallet_address'];
            $destDetail = !empty($details) ? implode(' | ', $details) : "Method #{$linkedMethod['id']}";
            $destination = strtoupper(str_replace('_', ' ', $methodType)) . ": {$destDetail}";
        }

        // Calculate dynamic platform and bank fees from FinancialLedger settings
        $feeSettings = FinancialLedger::getPayoutFeeSettings();
        $allowedMethods = FinancialLedger::getAllowedPayoutMethods();

        $platformFeePercent = (float)($feeSettings['platform_fee_percent'] ?? 5.0);
        $bankFeePercent = (float)($feeSettings['bank_fee_percent'] ?? 2.5);
        $bankWireSurcharge = (float)($feeSettings['bank_wire_surcharge'] ?? 5.0);

        // Determine fee policy for this method
        $feePolicy = 'standard';
        foreach ($allowedMethods as $am) {
            if (($am['key'] ?? '') === $methodType) {
                $feePolicy = $am['fee_policy'] ?? 'standard';
                break;
            }
        }
        if ($methodType === 'bank_wire' || $methodType === 'iban_sepa' || strpos($methodType, 'bank') !== false) {
            $feePolicy = 'bank';
        }

        $platformTax = 0.00;
        $bankTax = 0.00;
        $bankFee = 0.00;

        if ($feePolicy !== 'none') {
            $platformTax = round($amount * ($platformFeePercent / 100), 2);
        }

        if ($feePolicy === 'bank') {
            $bankTax = round($amount * ($bankFeePercent / 100), 2);
            $bankFee = $bankWireSurcharge;
        }

        $totalFeeDeducted = round($platformTax + $bankTax + $bankFee, 2);
        $netAmount = max(0, round($amount - $totalFeeDeducted, 2));

        // 3. Record Immutable Financial Ledger Debit Entry
        $ledgerRes = FinancialLedger::recordEntry(
            $authorId,
            'debit',
            'payout_withdrawal',
            $amount,
            "Withdrawal request via " . strtoupper(str_replace('_', ' ', $methodType)) . " ({$destination}) [Platform Fee ({$platformFeePercent}%): \${$platformTax}, Bank Fee ({$bankFeePercent}%+\${$bankWireSurcharge}): \$" . ($bankTax + $bankFee) . "]",
            (int)$user['id'],
            'author',
            [
                'payout_method_id' => $linkedMethod['id'],
                'payout_method_details' => $destination,
                'status' => 'pending',
                'internal_notes' => $notes,
                'metadata' => [
                    'platform_tax' => $platformTax,
                    'platform_fee_percent' => $platformFeePercent,
                    'bank_tax' => $bankTax,
                    'bank_fee_percent' => $bankFeePercent,
                    'bank_fee' => $bankFee,
                    'fee_deducted' => $totalFeeDeducted,
                    'net_amount' => $netAmount,
                    'method_type' => $methodType,
                    'fee_policy' => $feePolicy
                ]
            ]
        );

        if (empty($ledgerRes['success'])) {
            Session::flash('error', $ledgerRes['error'] ?? 'Failed to process ledger debit.');
            Response::redirect('/author/dashboard');
            return;
        }

        $refNo = $ledgerRes['reference_no'];

        // 4. Insert into author_payouts queue
        Database::execute(
            "INSERT INTO author_payouts (ledger_reference_no, author_id, payout_method_id, amount_usd, tax_deducted, bank_tax_deducted, fee_deducted, net_amount, credits_deducted, payout_method, payout_address, status, admin_notes, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, NOW())",
            [$refNo, $authorId, $linkedMethod['id'], $amount, $platformTax, ($bankTax + $bankFee), $totalFeeDeducted, $netAmount, $amount, $methodType, $destination, $notes]
        );
        $payoutId = (int)Database::lastInsertId();

        // 5. Notify Finance Admin Staff
        $admins = Database::query("SELECT id FROM users WHERE role IN ('admin', 'superadmin')");
        foreach ($admins as $adm) {
            NotificationHelper::send((int)$adm['id'], 'revenue', "New Withdrawal Request 💳 #{$payoutId}", "Creator {$author['display_name']} requested \${$amount} USD via " . strtoupper($methodType), "/admin/revenue?tab=payouts");
        }

        // 6. Send Email Confirmation to Author
        try {
            if (!empty($user['email'])) {
                \App\Core\Mailer::sendTemplate('payout_requested', $user['email'], $user['name'] ?? $author['display_name'], [
                    'name' => $user['name'] ?? $author['display_name'],
                    'amount' => number_format($amount, 2),
                    'net_amount' => number_format($netAmount, 2),
                    'fee_deducted' => number_format($totalFeeDeducted, 2),
                    'payout_id' => (string)$payoutId,
                    'ref_no' => (string)$refNo,
                    'method_type' => strtoupper(str_replace('_', ' ', $methodType)),
                    'destination' => (string)$destination,
                    'action_url' => url('/author/dashboard?tab=payouts')
                ]);
            }
        } catch (\Throwable $e) {}

        Session::flash('success', "Payout request #{$payoutId} for \${$amount} USD submitted successfully! Reference: {$refNo}. Status: PENDING financial review.");
        Response::redirect('/author/dashboard');
    }

    public function savePayoutMethod(Request $request): void {
        $user = Auth::user();
        $authorId = (int)$user['author_id'];

        $methodType = (string)$request->post('method_type', 'bank_wire');

        // Verify that the requested method is allowed and enabled by administration
        $allowedMethods = FinancialLedger::getAllowedPayoutMethods();
        $isAllowed = false;
        foreach ($allowedMethods as $am) {
            if ($am['key'] === $methodType && !empty($am['enabled'])) {
                $isAllowed = true;
                break;
            }
        }
        if (!$isAllowed) {
            Session::flash('error', '⚠️ This payout method is currently not enabled or accepted by platform administration.');
            Response::redirect('/author/dashboard');
            return;
        }

        $holder = trim((string)($request->post('account_holder_name') ?: $request->post('account_holder_name_custom', '')));
        $bank = trim((string)$request->post('bank_name', ''));
        $accNum = trim((string)($request->post('account_number_or_iban') ?: $request->post('account_number_custom', '')));
        $email = trim((string)($request->post('account_email') ?: $request->post('account_email_custom', '')));
        $wallet = trim((string)$request->post('crypto_wallet_address', ''));

        if ($methodType === 'paypal' && empty($email)) {
            Session::flash('error', 'Valid PayPal email address is required.');
            Response::redirect('/author/dashboard');
            return;
        }

        if (($methodType === 'bank_wire' || $methodType === 'iban_sepa') && (empty($holder) || empty($accNum))) {
            Session::flash('error', 'Account holder name and IBAN / Account number are required.');
            Response::redirect('/author/dashboard');
            return;
        }

        if ($methodType === 'crypto_usdc' && empty($wallet)) {
            Session::flash('error', 'USDC wallet address is required.');
            Response::redirect('/author/dashboard');
            return;
        }

        // Custom or alternative method validation
        if (!in_array($methodType, ['paypal', 'bank_wire', 'iban_sepa', 'crypto_usdc'], true)) {
            if (empty($accNum) && empty($email) && empty($wallet) && empty($holder)) {
                Session::flash('error', 'Please provide your account details (Email, Account ID, or Holder Name) for this payout destination.');
                Response::redirect('/author/dashboard');
                return;
            }
        }

        $saveData = array_merge($request->all(), [
            'account_holder_name' => $holder,
            'account_number_or_iban' => $accNum,
            'account_email' => $email,
            'crypto_wallet_address' => $wallet
        ]);

        FinancialLedger::savePayoutMethod($authorId, $saveData);

        // Dispatch payout_method_linked event automation
        try {
            $identifier = $email ?: ($accNum ? (substr($accNum, 0, 4) . '••••' . substr($accNum, -4)) : ($wallet ? (substr($wallet, 0, 6) . '...' . substr($wallet, -4)) : $holder));
            \App\Core\EventTriggerEngine::fire('payout_method_linked', [
                'email' => $user['email'],
                'name' => $user['display_name'] ?? $user['username'],
                'method_type' => strtoupper(str_replace('_', ' ', $methodType)),
                'account_identifier' => $identifier ?: 'Verified Account',
                'created_at' => date('F j, Y, g:i a'),
                'action_url' => url('/author/dashboard')
            ]);
        } catch (\Throwable $e) {}

        Session::flash('success', 'Payout method linked and verified successfully! 💳');
        Response::redirect('/author/dashboard');
    }

    public function deletePayoutMethod(Request $request, array $params): void {
        $user = Auth::user();
        $authorId = (int)$user['author_id'];
        $methodId = (int)($params['id'] ?? 0);

        FinancialLedger::deletePayoutMethod($authorId, $methodId);

        // Dispatch payout_method_removed event automation
        try {
            \App\Core\EventTriggerEngine::fire('payout_method_removed', [
                'email' => $user['email'],
                'name' => $user['display_name'] ?? $user['username'],
                'method_type' => 'Configured Payout Method',
                'action_url' => url('/author/dashboard')
            ]);
        } catch (\Throwable $e) {}

        Session::flash('success', 'Payout method removed.');
        Response::redirect('/author/dashboard');
    }

    public function setDefaultPayoutMethod(Request $request, array $params): void {
        $user = Auth::user();
        $authorId = (int)$user['author_id'];
        $methodId = (int)($params['id'] ?? 0);

        Database::execute("UPDATE author_payout_methods SET is_default = 0 WHERE author_id = ?", [$authorId]);
        Database::execute("UPDATE author_payout_methods SET is_default = 1 WHERE id = ? AND author_id = ?", [$methodId, $authorId]);

        Session::flash('success', 'Default payout destination updated.');
        Response::redirect('/author/dashboard');
    }

    public function redeemRewards(Request $request): void {
        $user = Auth::user();
        $authorId = (int)$user['author_id'];
        $author = Database::fetch("SELECT * FROM authors WHERE id = ?", [$authorId]);

        $credits = (float)($author['promo_credits'] ?? 0);
        $amount = (float)$request->post('amount', 0);
        $rewardType = (string)$request->post('reward_type', 'featured_boost');

        if ($rewardType === 'cash_payout') {
            $this->requestPayout($request);
            return;
        }

        if ($amount <= 0 || $amount > $credits) {
            Session::flash('error', 'Invalid credit redemption amount requested.');
            Response::redirect('/author/dashboard');
            return;
        }

        // Deduct credits and log record
        Database::execute("UPDATE authors SET promo_credits = promo_credits - ? WHERE id = ?", [$amount, $authorId]);
        Database::execute(
            "INSERT INTO revenue_records (author_id, description, credits_granted, amount_usd, transaction_type, created_at) VALUES (?, ?, ?, ?, ?, NOW())",
            [$authorId, "Promo boost claim ({$rewardType})", -$amount, 0, $rewardType]
        );

        Session::flash('success', "Redemption claim of {$amount} CR registered! Feature boost activated.");
        Response::redirect('/author/dashboard');
    }

    public function deleteDraft(Request $request, array $params): void {
        $user = Auth::user();
        $authorId = (int)$user['author_id'];
        $contentId = (int)($params['id'] ?? 0);

        $item = Database::fetch("SELECT * FROM contents WHERE id = ? AND author_id = ? AND status = 'draft'", [$contentId, $authorId]);
        if ($item) {
            Database::execute("DELETE FROM contents WHERE id = ?", [$contentId]);
            Session::flash('success', 'Draft removed.');
        } else {
            Session::flash('error', 'Draft could not be removed.');
        }

        Response::redirect('/author/dashboard');
    }

    public function importDocx(Request $request): void {
        if (!Auth::isAuthor()) {
            Response::json(['success' => false, 'error' => 'Unauthorized'], 403);
            return;
        }

        if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            Response::json(['success' => false, 'error' => 'No valid .docx file received.'], 400);
            return;
        }

        $file = $_FILES['file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'docx') {
            Response::json(['success' => false, 'error' => 'Only .docx files are supported.'], 400);
            return;
        }

        $tempPath = $file['tmp_name'];
        $parsed = \App\Core\DocxParser::parse($tempPath);

        if (!$parsed['success']) {
            Response::json(['success' => false, 'error' => $parsed['error'] ?? 'Could not parse document.'], 422);
            return;
        }

        Response::json([
            'success' => true,
            'title' => $parsed['title'],
            'summary' => $parsed['summary'],
            'body_html' => $parsed['body_html'],
            'word_count' => $parsed['word_count'],
            'filename' => htmlspecialchars($file['name'])
        ]);
    }


    public function rewards(Request $request): void {
        $user = Auth::user();
        $authorId = (int)$user['author_id'];
        $author = Database::fetch("SELECT * FROM authors WHERE id = ?", [$authorId]);
        $transactions = Database::query("SELECT * FROM revenue_records WHERE author_id = ? ORDER BY created_at DESC LIMIT 15", [$authorId]);
        $monetization = Author::getMonetizationStatus($authorId);

        Response::view('author/rewards', [
            'title' => 'Creator Rewards & Promo Credits | TechScout',
            'author' => $author,
            'transactions' => $transactions,
            'monetization' => $monetization
        ]);
    }

    public function applyMonetization(Request $request): void {
        $user = Auth::user();
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || $request->isAjax();
        
        $authorId = (int)($user['author_id'] ?? 0);
        if ($authorId <= 0) {
            if ($isAjax) {
                Response::json(['success' => false, 'error' => 'Author profile not found. Please apply as an author first.'], 403);
                return;
            }
            Response::redirect('/author/apply');
            return;
        }

        $author = Database::fetch("SELECT * FROM authors WHERE id = ?", [$authorId]);
        if (!$author) {
            if ($isAjax) {
                Response::json(['success' => false, 'error' => 'Author record not found.'], 404);
                return;
            }
            Response::redirect('/author/apply');
            return;
        }

        if (!empty($author['is_monetized'])) {
            $msg = 'You are already an active verified Creator Partner! 🎉';
            if ($isAjax) {
                Response::json(['success' => false, 'error' => $msg]);
                return;
            }
            Session::flash('info', $msg);
            Response::redirect('/author/dashboard');
            return;
        }

        if (($author['monetization_status'] ?? '') === 'applied') {
            $msg = 'Your monetization application is already submitted and under active review ⏳';
            if ($isAjax) {
                Response::json(['success' => false, 'error' => $msg]);
                return;
            }
            Session::flash('info', $msg);
            Response::redirect('/author/dashboard');
            return;
        }

        $status = Author::getMonetizationStatus($authorId);
        if (!$status['is_eligible']) {
            $msg = 'You have not yet unlocked all monetization requirements (' . ($status['overall_progress'] ?? 0) . '% unlocked). Continue publishing to unlock partner review!';
            if ($isAjax) {
                Response::json(['success' => false, 'error' => $msg]);
                return;
            }
            Session::flash('error', $msg);
            Response::redirect('/author/dashboard');
            return;
        }

        Database::execute("UPDATE authors SET monetization_status = 'applied', monetization_applied_at = NOW() WHERE id = ?", [$authorId]);
        AuditLog::record('apply_monetization', 'authors', $authorId, [
            'author_id' => $authorId, 
            'followers' => $status['metrics']['followers']['current'] ?? 0, 
            'qualified_views' => $status['metrics']['qualified_views']['current'] ?? 0
        ]);

        // Dispatch notifications to Author & Staff
        NotificationHelper::send((int)$user['id'], 'system', 'Partner Monetization Application Queued ⏳', 'Your application is under active editorial review. You will receive an update once verified.', '/author/dashboard');
        
        $admins = Database::query("SELECT id FROM users WHERE role IN ('admin', 'superadmin', 'editor')");
        foreach ($admins as $adm) {
            NotificationHelper::send((int)$adm['id'], 'monetization', "New Partner Monetization Application 💎", "Creator {$author['display_name']} (@{$author['slug']}) has applied for Partner Monetization.", "/admin/revenue?tab=monetization");
        }

        $successMsg = 'Your Creator Partner Monetization Application has been submitted to editorial administration for review! 🚀';
        if ($isAjax) {
            Response::json(['success' => true, 'message' => $successMsg]);
            return;
        }

        Session::flash('success', $successMsg);
        Response::redirect('/author/dashboard');
    }

    public function analytics(Request $request): void {
        $user = Auth::user();
        $authorId = (int)($user['author_id'] ?? 0);
        $author = Database::fetch("SELECT * FROM authors WHERE id = ?", [$authorId]);

        if (!$author) {
            Session::flash('error', 'Creator privileges required to view telemetry.');
            Response::redirect('/author/apply');
            return;
        }

        // Fetch all publications across all entity types
        $contents = Database::query("
            SELECT c.*, cat.name as category_name,
                   (SELECT COUNT(*) FROM bookmarks WHERE content_id = c.id) as bookmarks_count,
                   (SELECT COUNT(*) FROM comments WHERE content_id = c.id AND status = 'approved') as comments_count
            FROM contents c 
            LEFT JOIN categories cat ON c.category_id = cat.id 
            WHERE c.author_id = ? 
            ORDER BY c.view_count DESC, c.created_at DESC
        ", [$authorId]);

        // Aggregate Telemetry
        $totalViews = (int)(Database::fetch("SELECT SUM(view_count) as s FROM contents WHERE author_id = ?", [$authorId])['s'] ?? 0);
        $totalReactions = (int)(Database::fetch("SELECT SUM(reaction_count) as s FROM contents WHERE author_id = ?", [$authorId])['s'] ?? 0);
        $totalBookmarks = (int)(Database::fetch("SELECT COUNT(*) as c FROM bookmarks b JOIN contents c ON b.content_id = c.id WHERE c.author_id = ?", [$authorId])['c'] ?? 0);
        $totalComments = (int)(Database::fetch("SELECT COUNT(*) as c FROM comments cm JOIN contents c ON cm.content_id = c.id WHERE c.author_id = ? AND cm.status = 'approved'", [$authorId])['c'] ?? 0);

        // Daily Views Breakdown (Past 30 Days)
        $dailyRows = Database::query("
            SELECT DATE(e.event_time) as log_date, COUNT(*) as count 
            FROM analytics_events e 
            JOIN contents c ON e.content_id = c.id 
            WHERE c.author_id = ? AND e.event_time >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(e.event_time)
            ORDER BY log_date ASC
        ", [$authorId]);

        $dailyMap = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $dailyMap[$d] = 0;
        }
        foreach ($dailyRows as $dr) {
            if (isset($dailyMap[$dr['log_date']])) {
                $dailyMap[$dr['log_date']] = (int)$dr['count'];
            }
        }

        // Entity Types Breakdown
        $entityTypeStats = Database::query("
            SELECT content_type, COUNT(*) as entity_count, SUM(view_count) as total_views, SUM(reaction_count) as total_reactions 
            FROM contents 
            WHERE author_id = ? 
            GROUP BY content_type
        ", [$authorId]);

        // Top Referrers
        $topReferrers = Database::query("
            SELECT COALESCE(e.referrer_domain, 'direct') as domain, COUNT(*) as count 
            FROM analytics_events e 
            JOIN contents c ON e.content_id = c.id 
            WHERE c.author_id = ? 
            GROUP BY domain 
            ORDER BY count DESC 
            LIMIT 8
        ", [$authorId]);

        // Format top referrers from database
        foreach ($topReferrers as &$ref) {
            $d = strtolower($ref['domain']);
            if (str_contains($d, 'google')) {
                $ref['channel'] = 'Organic Search'; $ref['icon'] = 'search'; $ref['color'] = '#38bdf8';
            } elseif (str_contains($d, 'github')) {
                $ref['channel'] = 'Developer Repos'; $ref['icon'] = 'code-2'; $ref['color'] = '#818cf8';
            } elseif (str_contains($d, 'twitter') || str_contains($d, 'x.com') || str_contains($d, 't.co')) {
                $ref['channel'] = 'Social Radar'; $ref['icon'] = 'share-2'; $ref['color'] = '#06b6d4';
            } elseif (str_contains($d, 'ycombinator') || str_contains($d, 'hackernews')) {
                $ref['channel'] = 'Tech Aggregator'; $ref['icon'] = 'zap'; $ref['color'] = '#fb923c';
            } elseif ($d === 'direct' || $d === 'localhost') {
                $ref['channel'] = 'Direct / Bookmarks'; $ref['icon'] = 'globe'; $ref['color'] = '#34d399';
            } else {
                $ref['channel'] = 'External Referral'; $ref['icon'] = 'external-link'; $ref['color'] = '#a78bfa';
            }
        }
        unset($ref);

        // Device Breakdown (Real Database Query)
        $deviceRows = Database::query("
            SELECT e.device_type, COUNT(*) as count 
            FROM analytics_events e 
            JOIN contents c ON e.content_id = c.id 
            WHERE c.author_id = ? 
            GROUP BY e.device_type
        ", [$authorId]);
        $devices = ['desktop' => 0, 'mobile' => 0, 'tablet' => 0];
        foreach ($deviceRows as $dr) {
            if (isset($devices[$dr['device_type']])) {
                $devices[$dr['device_type']] = (int)$dr['count'];
            }
        }

        Response::view('author/analytics', [
            'title' => 'Creator Telemetry & Entity Intelligence Hub | TechScout',
            'author' => $author,
            'contents' => $contents,
            'totalViews' => $totalViews,
            'totalReactions' => $totalReactions,
            'totalBookmarks' => $totalBookmarks,
            'totalComments' => $totalComments,
            'dailyViews' => $dailyMap,
            'entityTypeStats' => $entityTypeStats,
            'topReferrers' => $topReferrers,
            'devices' => $devices
        ]);
    }

    public function contentAnalytics(Request $request, array $params): void {
        $user = Auth::user();
        $authorId = (int)($user['author_id'] ?? 0);
        $id = (int)($params['id'] ?? 0);

        $analytics = Analytics::getEntityAnalytics($id);
        if (empty($analytics) || (int)($analytics['content']['author_id'] ?? 0) !== $authorId) {
            Session::flash('error', 'Publication telemetry not found or access restricted.');
            Response::redirect('/author/dashboard');
            return;
        }

        $author = Database::fetch("SELECT * FROM authors WHERE id = ?", [$authorId]);

        Response::view('author/content_analytics', [
            'title' => 'Publication Telemetry: ' . $analytics['content']['title'] . ' | Creator Studio',
            'analytics' => $analytics,
            'author' => $author
        ]);
    }

    /**
     * Respond to a Co-Authoring Invitation (Accept / Decline)
     */
    public function respondCoAuthorInvite(Request $request): void {
        $user = Auth::user();
        $authorId = (int)($user['author_id'] ?? 0);
        $inviteId = (int)$request->input('invite_id', 0);
        $action = $request->input('action', 'accepted'); // accepted or declined

        $invite = Database::fetch("SELECT * FROM article_co_authors WHERE id = ? AND author_id = ?", [$inviteId, $authorId]);
        if (!$invite) {
            Session::flash('error', 'Invitation not found or unauthorized.');
            Response::redirect('/author/dashboard');
            return;
        }

        $newStatus = ($action === 'accepted') ? 'accepted' : 'declined';
        Database::execute("UPDATE article_co_authors SET invitation_status = ? WHERE id = ?", [$newStatus, $inviteId]);

        Session::flash('success', "Co-author invitation marked as " . ucfirst($newStatus) . ".");
        Response::redirect('/author/dashboard');
    }
    /**
     * Submit platform feedback from Author Dashboard
     */
    public function submitFeedback(Request $request): void {
        $user = Auth::user();
        if (!$user) {
            Session::flash('error', 'Authentication required.');
            Response::redirect('/auth/login');
            return;
        }

        $subject = trim((string)$request->input('subject'));
        $message = trim((string)$request->input('message'));

        if (empty($subject) || empty($message)) {
            Session::flash('error', 'Subject and Message are required.');
            Response::redirect('/author/dashboard?tab=feedback');
            return;
        }

        Database::execute(
            "INSERT INTO author_feedbacks (author_id, subject, message, status) VALUES (?, ?, ?, 'pending')",
            [$user['id'], $subject, $message]
        );

        Session::flash('success', 'Your feedback has been submitted successfully to the administration team.');
        Response::redirect('/author/dashboard?tab=feedback');
    }
}
