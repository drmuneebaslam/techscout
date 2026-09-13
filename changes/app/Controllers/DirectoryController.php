<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;
use App\Models\Content;
use App\Models\Analytics;

class DirectoryController {
    // AI Tools Directory
    public function tools(Request $request, array $params = []): void {
        $category = $params['slug'] ?? $request->query('category');
        $pricing = $request->query('pricing');
        $sort = $request->query('sort', 'popular');
        $search = trim((string)$request->query('search', ''));

        $filters = [
            'pricing' => $pricing,
            'search' => $search
        ];

        $tools = Content::getFeed('ai_tool', $category, $sort, 24, 0, $filters);
        $categories = Database::query("SELECT c.*, p.name as parent_name FROM categories c LEFT JOIN categories p ON c.parent_id = p.id WHERE c.applicable_to IN ('all', 'ai_tool') ORDER BY c.sort_order ASC, c.name ASC");

        $categoryInfo = null;
        if ($category && $category !== 'all') {
            $categoryInfo = Database::fetch("SELECT * FROM categories WHERE slug = ?", [$category]);
        }

        Response::view('directory/tools', [
            'title' => ($categoryInfo ? $categoryInfo['name'] . ' — ' : '') . 'AI Tools Directory & Benchmarks | TechScout',
            'tools' => $tools,
            'categories' => $categories,
            'currentCategory' => $category,
            'categoryInfo' => $categoryInfo,
            'currentPricing' => $pricing,
            'currentSort' => $sort,
            'currentSearch' => $search
        ]);
    }

    public function toolSingle(Request $request, array $params): void {
        $slug = $params['slug'] ?? '';
        $tool = Content::findBySlug($slug);

        if (!$tool || $tool['content_type'] !== 'ai_tool') {
            http_response_code(404);
            Response::view('errors/404', ['title' => 'Tool Not Found']);
            return;
        }

        // Access Control: Non-approved content is strictly restricted to Admins, Editors, and the Author owner
        if (($tool['status'] ?? 'published') !== 'published') {
            $user = \App\Core\Auth::user();
            $canPreview = false;
            if ($user && (\App\Core\Auth::isAdmin() || \App\Core\Auth::isEditor() || (!empty($user['author_id']) && (int)$user['author_id'] === (int)$tool['author_id']))) {
                $canPreview = true;
            }
            if (!$canPreview) {
                http_response_code(404);
                Response::view('errors/404', ['title' => 'Tool Not Found']);
                return;
            }
        }

        if (($tool['status'] ?? '') === 'published') {
            Content::incrementViews((int)$tool['id']);
            Analytics::logEvent('view', (int)$tool['id'], !empty($tool['author_id']) ? (int)$tool['author_id'] : null);
        }
        $relatedTools = Content::getFeed('ai_tool', $tool['category_slug'] ?? null, 'latest', 4);

        $isBookmarked = false;
        if (\App\Core\Auth::check()) {
            $uid = \App\Core\Auth::id();
            $bm = Database::fetch("SELECT id FROM bookmarks WHERE user_id = ? AND content_id = ?", [$uid, (int)$tool['id']]);
            $isBookmarked = !empty($bm);
        }

        Response::view('directory/tool_single', [
            'title' => $tool['title'] . ' — Review & Pricing | TechScout',
            'tool' => $tool,
            'relatedTools' => $relatedTools,
            'isBookmarked' => $isBookmarked
        ]);
    }

    // Prompts Library
    public function prompts(Request $request, array $params = []): void {
        $category = $params['slug'] ?? $request->query('category');
        $sort = $request->query('sort', 'popular');
        $search = trim((string)$request->query('search', ''));
        $model = $request->query('model');
        $tab = $request->query('tab', '');

        $imageCategorySlugs = ['ai-image-prompts', 'midjourney', 'dall-e', 'flux', 'stable-diffusion'];
        $isImageCategory = $category && in_array($category, $imageCategorySlugs, true);

        if ($tab === 'image' || $isImageCategory) {
            $activeTab = 'image';
        } elseif ($tab === 'all') {
            $activeTab = 'all';
        } else {
            // Default to text / system / code prompts to strictly segregate from image generation prompts
            $activeTab = 'text';
        }

        $filters = [
            'search' => $search,
            'model' => $model,
            'prompt_kind' => ($activeTab === 'image') ? 'image' : (($activeTab === 'all') ? 'all' : 'text')
        ];

        $prompts = Content::getFeed('prompt', $category, $sort, 24, 0, $filters);
        $categories = Database::query("SELECT c.*, p.name as parent_name FROM categories c LEFT JOIN categories p ON c.parent_id = p.id WHERE c.applicable_to IN ('all', 'prompt') ORDER BY c.sort_order ASC, c.name ASC");

        $imageCategories = Database::query("SELECT * FROM categories WHERE slug IN ('midjourney', 'dall-e', 'flux', 'stable-diffusion') OR parent_id = 30 ORDER BY sort_order ASC");

        $categoryInfo = null;
        if ($category && $category !== 'all') {
            $categoryInfo = Database::fetch("SELECT * FROM categories WHERE slug = ?", [$category]);
        }

        Response::view('directory/prompts', [
            'title' => ($categoryInfo ? $categoryInfo['name'] . ' — ' : '') . 'Engineering Prompt Library & Variable Injector | TechScout',
            'prompts' => $prompts,
            'categories' => $categories,
            'imageCategories' => $imageCategories,
            'currentCategory' => $category,
            'categoryInfo' => $categoryInfo,
            'currentSort' => $sort,
            'currentSearch' => $search,
            'currentModel' => $model,
            'activeTab' => $activeTab
        ]);
    }

    public function imagePrompts(Request $request, array $params = []): void {
        $category = $params['category'] ?? $request->query('category');
        $sort = $request->query('sort', 'popular');
        $search = trim((string)$request->query('search', ''));
        $model = $request->query('model');

        $filters = [
            'type' => 'prompt',
            'search' => $search,
            'category' => $category,
            'model' => $model,
            'prompt_kind' => 'image'
        ];

        $prompts = Content::getFeed('prompt', $category, $sort, 50, 0, $filters);
        $categories = Database::query("SELECT c.*, p.name as parent_name FROM categories c LEFT JOIN categories p ON c.parent_id = p.id WHERE c.applicable_to IN ('all', 'prompt') ORDER BY c.sort_order ASC, c.name ASC");

        Response::view('directory/prompts_images', [
            'title' => 'AI Image Prompts Sub-Directory & Visual Blueprints | TechScout',
            'prompts' => $prompts,
            'categories' => $categories,
            'currentCategory' => $category,
            'currentSort' => $sort,
            'currentSearch' => $search,
            'currentModel' => $model
        ]);
    }

    public function promptSingle(Request $request, array $params): void {
        $slug = $params['slug'] ?? '';
        $prompt = Content::findBySlug($slug);

        if (!$prompt || $prompt['content_type'] !== 'prompt') {
            http_response_code(404);
            Response::view('errors/404', ['title' => 'Prompt Not Found']);
            return;
        }

        // Access Control: Non-approved content is strictly restricted
        if (($prompt['status'] ?? 'published') !== 'published') {
            $user = \App\Core\Auth::user();
            $canPreview = false;
            if ($user && (\App\Core\Auth::isAdmin() || \App\Core\Auth::isEditor() || (!empty($user['author_id']) && (int)$user['author_id'] === (int)$prompt['author_id']))) {
                $canPreview = true;
            }
            if (!$canPreview) {
                http_response_code(404);
                Response::view('errors/404', ['title' => 'Prompt Not Found']);
                return;
            }
        }

        if (($prompt['status'] ?? '') === 'published') {
            Content::incrementViews((int)$prompt['id']);
            Analytics::logEvent('view', (int)$prompt['id'], !empty($prompt['author_id']) ? (int)$prompt['author_id'] : null);
        }
        $variables = !empty($prompt['template_variables']) ? json_decode($prompt['template_variables'], true) : [];
        $models = !empty($prompt['target_models']) ? json_decode($prompt['target_models'], true) : ['ChatGPT', 'Claude'];

        $isBookmarked = false;
        if (\App\Core\Auth::check()) {
            $uid = \App\Core\Auth::id();
            $bm = Database::fetch("SELECT id FROM bookmarks WHERE user_id = ? AND content_id = ?", [$uid, (int)$prompt['id']]);
            $isBookmarked = !empty($bm);
        }

        $galleryImages = [];
        try {
            $galleryImages = Database::query("SELECT * FROM prompt_images WHERE prompt_id = ? ORDER BY sort_order ASC, id ASC", [(int)$prompt['id']]);
        } catch (\Throwable $e) {}

        $adDirectUrl = 'https://omg10.com/4/11737440';
        try {
            $adRow = Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'ad_direct_link_url'");
            if (!empty($adRow['key_value'])) {
                $adDirectUrl = $adRow['key_value'];
            }
        } catch (\Throwable $e) {}

        $comments = [];
        try {
            $comments = \App\Models\Comment::getTreeForContent((int)$prompt['id']);
        } catch (\Throwable $e) {}

        Response::view('directory/prompt_single', [
            'title' => $prompt['title'] . ' — Prompt Blueprint | TechScout',
            'prompt' => $prompt,
            'variables' => $variables,
            'models' => $models,
            'isBookmarked' => $isBookmarked,
            'galleryImages' => $galleryImages,
            'adDirectUrl' => $adDirectUrl,
            'comments' => $comments
        ]);
    }

    // Custom GPTs Hub
    public function gpts(Request $request, array $params = []): void {
        $category = $params['slug'] ?? $request->query('category');
        $sort = $request->query('sort', 'popular');
        $search = trim((string)$request->query('search', ''));
        $model = $request->query('model');

        $filters = [
            'search' => $search,
            'model' => $model
        ];

        $gpts = Content::getFeed('gpt_listing', $category, $sort, 24, 0, $filters);
        $categories = Database::query("SELECT c.*, p.name as parent_name FROM categories c LEFT JOIN categories p ON c.parent_id = p.id WHERE c.applicable_to IN ('all', 'gpt_listing') ORDER BY c.sort_order ASC, c.name ASC");

        $categoryInfo = null;
        if ($category && $category !== 'all') {
            $categoryInfo = Database::fetch("SELECT * FROM categories WHERE slug = ?", [$category]);
        }

        Response::view('directory/gpts', [
            'title' => ($categoryInfo ? $categoryInfo['name'] . ' — ' : '') . 'Custom GPTs & Autonomous Agents Directory | TechScout',
            'gpts' => $gpts,
            'categories' => $categories,
            'currentCategory' => $category,
            'categoryInfo' => $categoryInfo,
            'currentSort' => $sort,
            'currentSearch' => $search,
            'currentModel' => $model
        ]);
    }

    public function gptSingle(Request $request, array $params): void {
        $slug = $params['slug'] ?? '';
        $gpt = Content::findBySlug($slug);

        if (!$gpt || $gpt['content_type'] !== 'gpt_listing') {
            http_response_code(404);
            Response::view('errors/404', ['title' => 'Custom GPT Not Found | TechScout']);
            return;
        }

        // Access Control: Non-approved content is strictly restricted
        if (($gpt['status'] ?? 'published') !== 'published') {
            $user = \App\Core\Auth::user();
            $canPreview = false;
            if ($user && (\App\Core\Auth::isAdmin() || \App\Core\Auth::isEditor() || (!empty($user['author_id']) && (int)$user['author_id'] === (int)$gpt['author_id']))) {
                $canPreview = true;
            }
            if (!$canPreview) {
                http_response_code(404);
                Response::view('errors/404', ['title' => 'Custom GPT Not Found | TechScout']);
                return;
            }
        }

        if (($gpt['status'] ?? '') === 'published') {
            Content::incrementViews((int)$gpt['id']);
            Analytics::logEvent('view', (int)$gpt['id'], !empty($gpt['author_id']) ? (int)$gpt['author_id'] : null);
        }
        $relatedGpts = Content::getFeed('gpt_listing', $gpt['category_slug'] ?? null, 'latest', 3);
        $tags = Content::getTagsForContent((int)$gpt['id']);
        
        $promptStarters = [];
        if (!empty($gpt['prompt_starters'])) {
            $decoded = json_decode($gpt['prompt_starters'], true);
            $promptStarters = is_array($decoded) ? $decoded : [$gpt['prompt_starters']];
        }

        $isBookmarked = false;
        if (\App\Core\Auth::check()) {
            $uid = \App\Core\Auth::id();
            $bm = Database::fetch("SELECT id FROM bookmarks WHERE user_id = ? AND content_id = ?", [$uid, (int)$gpt['id']]);
            $isBookmarked = !empty($bm);
        }

        Response::view('directory/gpt_single', [
            'title' => $gpt['title'] . ' — Custom GPT Agent Blueprint | TechScout',
            'gpt' => $gpt,
            'promptStarters' => $promptStarters,
            'tags' => $tags,
            'relatedGpts' => $relatedGpts,
            'isBookmarked' => $isBookmarked
        ]);
    }

    // Campaigns & Hackathons
    public function campaigns(Request $request, array $params = []): void {
        $category = $params['slug'] ?? $request->query('category');
        $status = $request->query('status', 'all');
        $sort = $request->query('sort', 'latest');
        $search = trim((string)$request->query('search', ''));

        $filters = [
            'campaign_status' => $status,
            'search' => $search
        ];

        $campaigns = Content::getFeed('campaign', $category, $sort, 18, 0, $filters);
        $categories = Database::query("SELECT c.*, p.name as parent_name FROM categories c LEFT JOIN categories p ON c.parent_id = p.id WHERE c.applicable_to IN ('all', 'campaign') ORDER BY c.sort_order ASC, c.name ASC");

        Response::view('public/campaigns', [
            'title' => 'Tech Challenges & Developer Hackathons | TechScout',
            'campaigns' => $campaigns,
            'categories' => $categories,
            'currentCategory' => $category,
            'currentStatus' => $status,
            'currentSort' => $sort,
            'currentSearch' => $search
        ]);
    }

    public function campaignSingle(Request $request, array $params): void {
        $slug = $params['slug'] ?? '';
        $campaign = Content::findBySlug($slug);

        if (!$campaign || $campaign['content_type'] !== 'campaign') {
            http_response_code(404);
            Response::view('errors/404', ['title' => 'Campaign Not Found']);
            return;
        }

        $campaignMeta = Database::fetch("SELECT * FROM campaigns WHERE content_id = ?", [$campaign['id']]);
        if ($campaignMeta) {
            $campaign = array_merge($campaign, $campaignMeta);
        }

        // Referral tracking for campaign challenge
        $refCode = trim((string)$request->query('ref', ''));
        if (!empty($refCode)) {
            Database::execute("
                UPDATE challenge_submissions 
                SET referral_clicks = referral_clicks + 1 
                WHERE referral_code = ? AND campaign_id = ?
            ", [$refCode, $campaign['id']]);
            \App\Core\Session::set('campaign_ref', $refCode);
        }

        if (($campaign['status'] ?? '') === 'published') {
            Content::incrementViews((int)$campaign['id']);
            Analytics::logEvent('view', (int)$campaign['id'], !empty($campaign['author_id']) ? (int)$campaign['author_id'] : null);
        }

        $user = \App\Core\Auth::user();
        $userSubmission = null;
        $userArticles = [];
        $userVotedIds = [];

        if ($user) {
            $userSubmission = Database::fetch("
                SELECT * FROM challenge_submissions 
                WHERE campaign_id = ? AND user_id = ? 
                LIMIT 1
            ", [$campaign['id'], $user['id']]);

            // User's authored articles (strictly restricted to articles owned by logged in author)
            $userArticles = Database::query("
                SELECT c.id, c.title, c.slug, c.view_count, c.published_at 
                FROM contents c 
                JOIN authors a ON c.author_id = a.id 
                WHERE a.user_id = ? AND c.content_type = 'article' AND c.status = 'published'
                ORDER BY c.published_at DESC, c.id DESC
            ", [$user['id']]);

            $votes = Database::query("SELECT submission_id FROM challenge_votes WHERE user_id = ?", [$user['id']]);
            $userVotedIds = array_column($votes, 'submission_id');
        }

        // Community Showcase Submissions
        $showcaseSubmissions = Database::query("
            SELECT cs.*, u.username, u.email,
                   COALESCE(a.display_name, u.username) as submitter_name,
                   COALESCE(a.avatar_url, '') as submitter_avatar,
                   art.title as linked_article_title, art.slug as linked_article_slug
            FROM challenge_submissions cs
            JOIN contents c ON cs.campaign_id = c.id
            JOIN users u ON cs.user_id = u.id
            LEFT JOIN authors a ON u.id = a.user_id
            LEFT JOIN contents art ON cs.linked_article_id = art.id
            WHERE cs.campaign_id = ? AND cs.status != 'rejected'
            ORDER BY cs.votes_count DESC, cs.created_at DESC
        ", [$campaign['id']]);

        Response::view('public/campaign_single', [
            'title' => $campaign['title'] . ' — Developer Challenge & Hackathon | TechScout',
            'campaign' => $campaign,
            'userSubmission' => $userSubmission,
            'userArticles' => $userArticles,
            'showcaseSubmissions' => $showcaseSubmissions,
            'userVotedIds' => $userVotedIds,
            'activeTab' => $request->query('tab', $userSubmission ? 'submission' : 'overview')
        ]);
    }

    public function submitCampaignProject(Request $request, array $params): void {
        $user = \App\Core\Auth::user();
        if (!$user) {
            \App\Core\Session::flash('error', 'Please sign in to submit your project to this challenge.');
            Response::redirect('/auth/login?redirect=' . urlencode('/campaigns/' . ($params['slug'] ?? '')));
            return;
        }

        $slug = $params['slug'] ?? '';
        $campaign = Content::findBySlug($slug);
        if (!$campaign || $campaign['content_type'] !== 'campaign') {
            Response::redirect('/campaigns');
            return;
        }

        $userId = (int)$user['id'];
        $campaignId = (int)$campaign['id'];

        $projectTitle = trim((string)$request->post('project_title', ''));
        $summary = trim((string)$request->post('summary', ''));
        $detailedDesc = trim((string)$request->post('detailed_description', ''));
        $techStack = trim((string)$request->post('tech_stack', ''));
        $githubUrl = trim((string)$request->post('github_repo_url', ''));
        $demoUrl = trim((string)$request->post('live_demo_url', ''));
        $videoUrl = trim((string)$request->post('video_demo_url', ''));
        $linkedArticleId = (int)$request->post('linked_article_id', 0) ?: null;

        // Verify that linkedArticleId actually belongs to this author
        if ($linkedArticleId) {
            $ownsArticle = Database::fetch("
                SELECT c.id FROM contents c 
                JOIN authors a ON c.author_id = a.id 
                WHERE c.id = ? AND a.user_id = ? AND c.content_type = 'article' AND c.status = 'published'
                LIMIT 1
            ", [$linkedArticleId, $userId]);
            if (!$ownsArticle) {
                $linkedArticleId = null; // Reject attempt to link other authors' articles
            }
        }

        if (empty($projectTitle) || empty($summary)) {
            \App\Core\Session::flash('error', 'Project title and executive summary are required.');
            Response::redirect('/campaigns/' . $slug . '?tab=submit');
            return;
        }

        // Check if submission already exists
        $existing = Database::fetch("SELECT id, referral_code FROM challenge_submissions WHERE campaign_id = ? AND user_id = ?", [$campaignId, $userId]);

        if ($existing) {
            Database::execute("
                UPDATE challenge_submissions SET
                    project_title = ?,
                    summary = ?,
                    detailed_description = ?,
                    tech_stack = ?,
                    github_repo_url = ?,
                    live_demo_url = ?,
                    video_demo_url = ?,
                    linked_article_id = ?,
                    updated_at = NOW()
                WHERE id = ?
            ", [
                $projectTitle, $summary, $detailedDesc, $techStack,
                $githubUrl, $demoUrl, $videoUrl, $linkedArticleId,
                $existing['id']
            ]);
            \App\Core\Session::flash('success', "Your challenge submission has been successfully updated! 🚀");
        } else {
            $refCode = 'c_' . bin2hex(random_bytes(4));
            Database::execute("
                INSERT INTO challenge_submissions 
                (campaign_id, user_id, project_title, summary, detailed_description, tech_stack, github_repo_url, live_demo_url, video_demo_url, linked_article_id, referral_code, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'submitted', NOW(), NOW())
            ", [
                $campaignId, $userId, $projectTitle, $summary, $detailedDesc, $techStack,
                $githubUrl, $demoUrl, $videoUrl, $linkedArticleId, $refCode
            ]);

            // Dispatch campaign_submission_received email automation
            try {
                \App\Core\EventTriggerEngine::fire('campaign_submission_received', [
                    'email' => $user['email'],
                    'name' => $user['display_name'] ?? $user['username'],
                    'campaign_title' => $campaign['title'] ?? 'AI Challenge Campaign',
                    'project_title' => $projectTitle,
                    'action_url' => url('/campaigns/' . $slug . '?tab=submission')
                ]);
            } catch (\Throwable $e) {}

            \App\Core\Session::flash('success', "Congratulations! Your project has been officially submitted to the challenge! 🏆 Use your personal referral link to share and track votes.");
        }

        Response::redirect('/campaigns/' . $slug . '?tab=submission');
    }

    public function voteCampaignSubmission(Request $request, array $params): void {
        $user = \App\Core\Auth::user();
        if (!$user) {
            Response::json(['success' => false, 'error' => 'Please sign in to upvote project submissions.'], 401);
            return;
        }

        $submissionId = (int)($params['id'] ?? $request->post('submission_id', 0));
        if ($submissionId <= 0) {
            Response::json(['success' => false, 'error' => 'Invalid submission ID.'], 400);
            return;
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $userId = (int)$user['id'];

        $existing = Database::fetch("SELECT id FROM challenge_votes WHERE submission_id = ? AND user_id = ?", [$submissionId, $userId]);
        if ($existing) {
            Database::execute("DELETE FROM challenge_votes WHERE id = ?", [$existing['id']]);
            Database::execute("UPDATE challenge_submissions SET votes_count = GREATEST(0, votes_count - 1) WHERE id = ?", [$submissionId]);
            $sub = Database::fetch("SELECT votes_count FROM challenge_submissions WHERE id = ?", [$submissionId]);
            Response::json(['success' => true, 'voted' => false, 'votes_count' => (int)($sub['votes_count'] ?? 0)]);
            return;
        }

        Database::execute("INSERT INTO challenge_votes (submission_id, user_id, ip_address, created_at) VALUES (?, ?, ?, NOW())", [$submissionId, $userId, $ip]);
        Database::execute("UPDATE challenge_submissions SET votes_count = votes_count + 1 WHERE id = ?", [$submissionId]);
        $sub = Database::fetch("SELECT votes_count FROM challenge_submissions WHERE id = ?", [$submissionId]);
        Response::json(['success' => true, 'voted' => true, 'votes_count' => (int)($sub['votes_count'] ?? 0)]);
    }

    // Tech Jobs Board Hub
    public function jobs(Request $request): void {
        $jobs = Database::query("SELECT * FROM jobs WHERE is_active = 1 ORDER BY is_featured DESC, created_at DESC");
        Response::view('public/jobs', [
            'title' => 'Tech & Engineering Jobs Board | TechScout',
            'jobs' => $jobs
        ]);
    }

    public function jobSingle(Request $request, array $params): void {
        $slug = $params['slug'] ?? '';
        $job = Database::fetch("SELECT * FROM jobs WHERE slug = ? AND is_active = 1", [$slug]);

        if (!$job) {
            http_response_code(404);
            Response::view('errors/404', ['title' => 'Job Opportunity Not Found | TechScout']);
            return;
        }

        Response::view('public/job_single', [
            'title' => $job['title'] . ' at ' . $job['company_name'] . ' | TechScout Jobs',
            'job' => $job
        ]);
    }

    public function applyJob(Request $request, array $params): void {
        $slug = $params['slug'] ?? '';
        $job = Database::fetch("SELECT * FROM jobs WHERE slug = ? AND is_active = 1", [$slug]);

        if (!$job) {
            http_response_code(404);
            Response::redirect('/jobs');
            return;
        }

        $applicantName = trim((string)$request->post('applicant_name', ''));
        $applicantEmail = trim((string)$request->post('applicant_email', ''));
        $portfolioUrl = trim((string)$request->post('portfolio_url', ''));
        $resumeText = trim((string)$request->post('resume_text', ''));
        $coverLetter = trim((string)$request->post('cover_letter', ''));
        $userId = \App\Core\Auth::id();

        if (empty($applicantName) || empty($applicantEmail) || empty($resumeText)) {
            \App\Core\Session::flash('error', 'Please provide your name, email, and resume / experience details.');
            Response::redirect('/jobs/' . $slug);
            return;
        }

        Database::execute("INSERT INTO job_applications (job_id, user_id, applicant_name, applicant_email, portfolio_url, resume_text_or_url, cover_letter, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')", [
            $job['id'], $userId, $applicantName, $applicantEmail, $portfolioUrl, $resumeText, $coverLetter
        ]);

        \App\Core\Session::flash('success', "Application for {$job['title']} submitted successfully! Our engineering leads will review your background.");
        Response::redirect('/jobs/' . $slug);
    }

    // Universal Affiliate & Outbound Link Redirect Gateway (/go/{slug})
    public function affiliateRedirect(Request $request, array $params): void {
        $slug = trim((string)($params['slug'] ?? ''));

        // Support direct outbound redirect via parameter: /go?url=https%3A%2F%2F...
        $directUrl = trim((string)$request->query('url', ''));
        if (!empty($directUrl) && (str_starts_with($directUrl, 'http://') || str_starts_with($directUrl, 'https://'))) {
            Response::redirect($directUrl, 302);
            return;
        }

        if (empty($slug)) {
            Response::redirect('/tools');
            return;
        }

        $type = trim((string)$request->query('type', ''));

        $content = Content::findBySlug($slug);
        if ($content) {
            try {
                Analytics::logEvent('click_outbound', (int)$content['id']);
            } catch (\Throwable $e) {}

            // Handle type-specific deep external redirects (docs, source repo, discord community)
            if ($type === 'docs' && !empty($content['docs_url'])) {
                Response::redirect($content['docs_url'], 302);
                return;
            }
            if (($type === 'repo' || $type === 'github') && !empty($content['open_source_repo'])) {
                Response::redirect($content['open_source_repo'], 302);
                return;
            }
            if ($type === 'discord' && !empty($content['discord_url'])) {
                Response::redirect($content['discord_url'], 302);
                return;
            }

            if ($content['content_type'] === 'gpt_listing') {
                $target = !empty($content['openai_url']) ? $content['openai_url'] : 'https://chatgpt.com/g/';
                Response::redirect($target, 302);
                return;
            }

            $target = !empty($content['affiliate_url']) ? $content['affiliate_url'] : (!empty($content['website_url']) ? $content['website_url'] : url('/tools/' . $slug));
            Response::redirect($target, 302);
            return;
        }

        // Direct table fallback for AI Tools if not in unified contents view
        try {
            $tool = Database::fetch("SELECT * FROM ai_tools WHERE slug = ?", [$slug]);
            if ($tool) {
                if ($type === 'docs' && !empty($tool['docs_url'])) {
                    Response::redirect($tool['docs_url'], 302);
                    return;
                }
                if (($type === 'repo' || $type === 'github') && !empty($tool['open_source_repo'])) {
                    Response::redirect($tool['open_source_repo'], 302);
                    return;
                }
                if ($type === 'discord' && !empty($tool['discord_url'])) {
                    Response::redirect($tool['discord_url'], 302);
                    return;
                }
                $target = !empty($tool['affiliate_url']) ? $tool['affiliate_url'] : (!empty($tool['website_url']) ? $tool['website_url'] : url('/tools'));
                Response::redirect($target, 302);
                return;
            }

            // Direct table fallback for GPT Listings
            $gpt = Database::fetch("SELECT * FROM gpt_listings WHERE slug = ?", [$slug]);
            if ($gpt) {
                $target = !empty($gpt['openai_url']) ? $gpt['openai_url'] : 'https://chatgpt.com/g/';
                Response::redirect($target, 302);
                return;
            }

            // Direct table fallback for Jobs
            $job = Database::fetch("SELECT * FROM jobs WHERE slug = ? AND is_active = 1", [$slug]);
            if ($job && !empty($job['apply_url'])) {
                Response::redirect($job['apply_url'], 302);
                return;
            }
        } catch (\Throwable $e) {}

        Response::redirect('/tools');
    }
}
