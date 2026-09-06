<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Content;
use App\Models\Author;
use App\Core\Database;

class HomeController {
    public function index(Request $request): void {
        $user = \App\Core\Auth::user();
        $userId = $user ? (int)$user['id'] : 0;
        $feedMode = $request->query('feed', 'for_you'); // 'for_you', 'trending', 'latest'

        // Check if user has active topic/category subscriptions
        $hasSubscriptions = false;
        if ($userId > 0) {
            $subCount = Database::fetch("SELECT COUNT(*) as cnt FROM user_subscriptions WHERE user_id = ?", [$userId]);
            $hasSubscriptions = !empty($subCount['cnt']) && (int)$subCount['cnt'] > 0;
        }

        // Load CMS Homepage Section Configuration
        $homepageSections = Database::query("SELECT * FROM homepage_sections ORDER BY sort_order ASC");
        $sectionsMap = [];
        foreach ($homepageSections as $sec) {
            $sectionsMap[$sec['section_key']] = $sec;
        }

        $featuredLimit = (int)($sectionsMap['featured']['item_limit'] ?? 3);
        $trendingLimit = (int)($sectionsMap['trending']['item_limit'] ?? 6);
        $latestLimit = (int)($sectionsMap['latest']['item_limit'] ?? 8);
        $toolsLimit = (int)($sectionsMap['tools']['item_limit'] ?? 4);
        $promptsLimit = (int)($sectionsMap['prompts']['item_limit'] ?? 4);
        $gptsLimit = (int)($sectionsMap['ai_tools']['item_limit'] ?? 3);
        $authorsLimit = (int)($sectionsMap['authors']['item_limit'] ?? 4);
        $campaignsLimit = (int)($sectionsMap['campaigns']['item_limit'] ?? 2);

        $featuredArticles = Content::getFeed('article', null, 'featured', $featuredLimit);
        $trendingArticles = Content::getFeed('article', null, 'trending', $trendingLimit);
        $latestArticles = Content::getFeed('article', null, 'latest', $latestLimit);
        $personalizedArticles = $hasSubscriptions ? Content::getPersonalizedFeed($userId, 'article', $featuredLimit * 2) : [];
        $personalizedTools = $hasSubscriptions ? Content::getPersonalizedFeed($userId, 'ai_tool', $toolsLimit) : [];
        $personalizedGpts = $hasSubscriptions ? Content::getPersonalizedFeed($userId, 'gpt_listing', $gptsLimit) : [];
        $personalizedPrompts = $hasSubscriptions ? Content::getPersonalizedFeed($userId, 'prompt', $promptsLimit) : [];

        $allTopics = Database::query("SELECT * FROM topics ORDER BY sort_order ASC, name ASC");

        $featuredTools = !empty($personalizedTools) ? $personalizedTools : Content::getFeed('ai_tool', null, 'featured', $toolsLimit);
        if (empty($featuredTools)) {
            $featuredTools = Content::getFeed('ai_tool', null, 'latest', $toolsLimit);
        }
        $latestTools = Content::getFeed('ai_tool', null, 'latest', $toolsLimit);
        
        $featuredGpts = !empty($personalizedGpts) ? $personalizedGpts : Content::getFeed('gpt_listing', null, 'popular', $gptsLimit);
        if (empty($featuredGpts)) {
            $featuredGpts = Content::getFeed('gpt_listing', null, 'latest', $gptsLimit);
        }
        
        $trendingPrompts = !empty($personalizedPrompts) ? $personalizedPrompts : Content::getFeed('prompt', null, 'popular', $promptsLimit);
        $activeCampaigns = Content::getFeed('campaign', null, 'latest', $campaignsLimit);
        $topAuthors = Author::getTopAuthors($authorsLimit);
        $featuredCategories = Database::query("SELECT * FROM categories WHERE is_featured_home = 1 ORDER BY sort_order ASC, name ASC");
        if (empty($featuredCategories)) {
            $featuredCategories = Database::query("SELECT * FROM categories ORDER BY sort_order ASC, name ASC LIMIT 6");
        }
        $allCategories = Database::query("SELECT c.*, p.name as parent_name, t.name as topic_name FROM categories c LEFT JOIN categories p ON c.parent_id = p.id LEFT JOIN topics t ON c.topic_id = t.id ORDER BY c.sort_order ASC, c.name ASC");

        Response::view('public/home', [
            'title' => 'TechScout — The Frontier of Tech, Scouted for Builders',
            'featuredArticles' => $featuredArticles ?? [],
            'trendingArticles' => $trendingArticles ?? [],
            'latestArticles' => $latestArticles ?? [],
            'personalizedArticles' => $personalizedArticles ?? [],
            'hasSubscriptions' => $hasSubscriptions,
            'allTopics' => $allTopics ?? [],
            'feedMode' => $feedMode,
            'featuredGpts' => $featuredGpts ?? [],
            'featuredTools' => $featuredTools ?? [],
            'latestTools' => $latestTools ?? [],
            'trendingPrompts' => $trendingPrompts ?? [],
            'activeCampaigns' => $activeCampaigns ?? [],
            'topAuthors' => $topAuthors ?? [],
            'featuredCategories' => $featuredCategories ?? [],
            'allCategories' => $allCategories ?? [],
            'homepageSections' => $homepageSections ?? [],
            'sectionsMap' => $sectionsMap ?? []
        ]);
    }

    public function about(Request $request): void {
        Response::view('public/about', ['title' => 'About TechScout | Engineering Intelligence']);
    }

    public function contact(Request $request): void {
        Response::view('public/contact', ['title' => 'Contact Us | TechScout']);
    }

    public function submitContact(Request $request): void {
        $name = mb_substr(trim((string)$request->post('name', '')), 0, 70);
        $email = mb_substr(trim((string)$request->post('email', '')), 0, 100);
        $subject = mb_substr(trim((string)$request->post('subject', 'General Inquiry')), 0, 150);
        $message = mb_substr(trim((string)$request->post('message', '')), 0, 2000);
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';

        if (empty($name) || empty($email) || empty($message)) {
            \App\Core\Session::flash('error', 'Please fill in all contact fields.');
            Response::redirect('/contact');
            return;
        }

        $userId = \App\Core\Auth::id();
        Database::execute("INSERT INTO contact_messages (user_id, name, email, subject, message, ip_address) VALUES (?, ?, ?, ?, ?, ?)", [
            $userId, $name, $email, $subject, $message, $ip
        ]);

        \App\Core\Session::flash('success', 'Thank you! Your message has been received by our editorial team. You can also track updates in your Inbox.');
        Response::redirect('/contact');
    }

    public function privacy(Request $request): void {
        Response::view('public/privacy', ['title' => 'Privacy Policy | TechScout']);
    }

    public function terms(Request $request): void {
        Response::view('public/terms', ['title' => 'Terms of Service | TechScout']);
    }

    public function notifications(Request $request): void {
        $user = \App\Core\Auth::user();
        if (!$user) {
            \App\Core\Session::flash('error', 'Please sign in to view your notifications.');
            Response::redirect('/auth/login');
            return;
        }

        $userId = (int)$user['id'];
        $filter = (string)$request->query('filter', 'all');
        $page = max(1, (int)$request->query('page', 1));
        $perPage = max(5, min(50, (int)$request->query('per_page', 15)));

        $paginated = \App\Core\NotificationHelper::getPaginated($userId, $page, $perPage, $filter);
        $counts = \App\Core\NotificationHelper::getCountsByFilter($userId);

        Response::view('notifications/index', [
            'title' => 'Notification Center | TechScout',
            'notifications' => $paginated['items'],
            'total' => $paginated['total'],
            'currentPage' => $paginated['page'],
            'perPage' => $paginated['perPage'],
            'totalPages' => $paginated['totalPages'],
            'unreadCount' => $paginated['unreadCount'],
            'currentFilter' => $filter,
            'counts' => $counts
        ]);
    }

    public function bookmarks(Request $request): void {
        $user = \App\Core\Auth::user();
        if (!$user) {
            \App\Core\Session::flash('error', 'Please sign in to view your saved bookmarks.');
            Response::redirect('/auth/login');
            return;
        }

        $userId = (int)$user['id'];
        $typeFilter = (string)$request->query('type', 'all');
        $page = max(1, (int)$request->query('page', 1));
        $perPage = 9;

        // Base query conditions
        $whereSql = "b.user_id = ?";
        $params = [$userId];

        if ($typeFilter !== 'all') {
            $whereSql .= " AND c.content_type = ?";
            $params[] = $typeFilter;
        }

        // Count total for pagination
        $countSql = "SELECT COUNT(*) as c FROM bookmarks b JOIN contents c ON b.content_id = c.id WHERE {$whereSql}";
        $totalCount = (int)(\App\Core\Database::fetch($countSql, $params)['c'] ?? 0);
        $totalPages = max(1, (int)ceil($totalCount / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT b.id as bookmark_id, b.created_at as saved_at, c.*, a.display_name as author_name, a.slug as author_slug, a.avatar_url as author_avatar, cat.name as category_name, cat.slug as category_slug
                FROM bookmarks b
                JOIN contents c ON b.content_id = c.id
                LEFT JOIN authors a ON c.author_id = a.id
                LEFT JOIN categories cat ON c.category_id = cat.id
                WHERE {$whereSql}
                ORDER BY b.created_at DESC
                LIMIT {$perPage} OFFSET {$offset}";
        $bookmarks = \App\Core\Database::query($sql, $params);

        Response::view('account/bookmarks', [
            'title' => 'My Bookmarks & Saved Intelligence | TechScout',
            'bookmarks' => $bookmarks,
            'currentType' => $typeFilter,
            'page' => $page,
            'perPage' => $perPage,
            'totalCount' => $totalCount,
            'totalPages' => $totalPages
        ]);
    }

    public function readingHistory(Request $request): void {
        $user = \App\Core\Auth::user();
        if (!$user) {
            \App\Core\Session::flash('error', 'Please sign in to view your reading history.');
            Response::redirect('/auth/login');
            return;
        }

        $userId = (int)$user['id'];
        $page = max(1, (int)$request->query('page', 1));
        $perPage = 10;

        $totalCount = (int)(\App\Core\Database::fetch("SELECT COUNT(*) as c FROM reading_history WHERE user_id = ?", [$userId])['c'] ?? 0);
        $totalPages = max(1, (int)ceil($totalCount / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $history = \App\Core\Database::query(
            "SELECT rh.*, c.title, c.slug, c.content_type, c.summary, c.featured_image_url, a.display_name as author_name, a.slug as author_slug, cat.name as category_name
             FROM reading_history rh
             JOIN contents c ON rh.content_id = c.id
             LEFT JOIN authors a ON c.author_id = a.id
             LEFT JOIN categories cat ON c.category_id = cat.id
             WHERE rh.user_id = ?
             ORDER BY rh.last_read_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            [$userId]
        );

        Response::view('account/history', [
            'title' => 'My Reading History & Progress | TechScout',
            'history' => $history,
            'page' => $page,
            'perPage' => $perPage,
            'totalCount' => $totalCount,
            'totalPages' => $totalPages
        ]);
    }
}
