<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;
use App\Models\Content;
use App\Models\Comment;

class BlogController {
    public function index(Request $request): void {
        $sort = $request->query('sort', 'latest');
        $category = $request->query('category', null);
        $search = trim((string)$request->query('search', ''));
        $readingTime = $request->query('reading_time', null);
        $feed = $request->query('feed', null);
        $user = \App\Core\Auth::user();
        $userId = $user ? (int)$user['id'] : 0;

        $filters = [
            'search' => $search,
            'reading_time' => $readingTime
        ];

        if ($feed === 'following' || $sort === 'following' || $sort === 'for_you') {
            $articles = Content::getPersonalizedFeed($userId, 'article', 15);
            $currentSort = 'following';
        } else {
            $articles = Content::getFeed('article', $category, $sort, 15, 0, $filters);
            $currentSort = $sort;
        }

        $topics = Database::query("SELECT * FROM topics ORDER BY sort_order ASC, name ASC");
        $categories = Database::query("SELECT c.*, t.name as topic_name FROM categories c LEFT JOIN topics t ON c.topic_id = t.id WHERE c.applicable_to IN ('all', 'article') ORDER BY c.sort_order ASC, c.name ASC");

        $hasSubscriptions = false;
        if ($userId > 0) {
            $subCount = Database::fetch("SELECT COUNT(*) as cnt FROM user_subscriptions WHERE user_id = ?", [$userId]);
            $hasSubscriptions = !empty($subCount['cnt']) && (int)$subCount['cnt'] > 0;
        }

        Response::view('blog/index', [
            'title' => 'TechScout Editorial — Deep Dives, Architecture & AI',
            'articles' => $articles,
            'topics' => $topics,
            'categories' => $categories,
            'currentSort' => $currentSort,
            'currentCategory' => $category,
            'currentSearch' => $search,
            'currentReadingTime' => $readingTime,
            'hasSubscriptions' => $hasSubscriptions
        ]);
    }

    public function trending(Request $request): void {
        $articles = Content::getFeed('article', null, 'trending', 15);
        $categories = Database::query("SELECT * FROM categories WHERE applicable_to IN ('all', 'article') ORDER BY sort_order ASC, name ASC");
        Response::view('blog/index', [
            'title' => 'Trending Tech Articles | TechScout',
            'articles' => $articles,
            'categories' => $categories,
            'currentSort' => 'trending',
            'currentCategory' => null
        ]);
    }

    public function category(Request $request, array $params): void {
        $slug = $params['slug'] ?? '';
        $category = Database::fetch("SELECT * FROM categories WHERE slug = ?", [$slug]);
        if (!$category) {
            Response::redirect('/blog');
        }

        $articles = Content::getFeed('article', $slug, 'latest', 12);
        $categories = Database::query("SELECT * FROM categories WHERE applicable_to IN ('all', 'article') ORDER BY sort_order ASC, name ASC");
        Response::view('blog/index', [
            'title' => htmlspecialchars($category['name']) . ' | TechScout',
            'articles' => $articles,
            'categories' => $categories,
            'currentCategory' => $slug,
            'categoryInfo' => $category
        ]);
    }

    public function tag(Request $request, array $params): void {
        $slug = $params['slug'] ?? '';
        $tag = Database::fetch("SELECT * FROM categories WHERE slug = ?", [$slug]);
        if (!$tag) {
            Response::redirect('/blog');
            return;
        }

        $articles = Content::getByTag($slug, 18);
        Response::view('blog/index', [
            'title' => '#' . htmlspecialchars($tag['name']) . ' | TechScout',
            'articles' => $articles,
            'currentCategory' => '#' . $tag['name'],
            'currentSort' => 'tag'
        ]);
    }

    public function show(Request $request, array $params): void {
        $slug = $params['slug'] ?? '';
        $article = Content::findBySlug($slug);

        if (!$article) {
            http_response_code(404);
            Response::view('errors/404', ['title' => 'Article Not Found | TechScout']);
            return;
        }

        // If this slug belongs to another content type (e.g. ai_tool, gpt_listing, prompt, campaign), gracefully redirect to its dedicated directory URL
        if ($article['content_type'] !== 'article') {
            Response::redirect(content_url($article));
            return;
        }

        // Access Control: Non-approved content is strictly restricted to Admins, Editors, and the Author owner
        if (($article['status'] ?? 'published') !== 'published') {
            $user = \App\Core\Auth::user();
            $canPreview = false;
            if ($user) {
                if (\App\Core\Auth::isAdmin() || \App\Core\Auth::isEditor()) {
                    $canPreview = true;
                } else {
                    $authorRow = Database::fetch("SELECT id FROM authors WHERE user_id = ?", [$user['id']]);
                    if ($authorRow && (int)$authorRow['id'] === (int)$article['author_id']) {
                        $canPreview = true;
                    }
                }
            }
            if (!$canPreview) {
                http_response_code(404);
                Response::view('errors/404', ['title' => 'Article Not Found | TechScout']);
                return;
            }
        }

        // Increment views for published content
        if (($article['status'] ?? '') === 'published') {
            Content::incrementViews((int)$article['id']);
        }

        $tags = Content::getTagsForContent((int)$article['id']);
        $comments = Comment::getTreeForContent((int)$article['id']);
        $relatedArticles = Content::getFeed('article', $article['category_slug'] ?? null, 'latest', 3);

        $isBookmarked = false;
        $savedProgress = 0;
        if (\App\Core\Auth::check()) {
            $uid = \App\Core\Auth::id();
            $bm = \App\Core\Database::fetch("SELECT id FROM bookmarks WHERE user_id = ? AND content_id = ?", [$uid, (int)$article['id']]);
            $isBookmarked = !empty($bm);
            $rh = \App\Core\Database::fetch("SELECT progress_percent FROM reading_history WHERE user_id = ? AND content_id = ?", [$uid, (int)$article['id']]);
            if ($rh) {
                $savedProgress = (int)$rh['progress_percent'];
            }
        }

        // Fetch Co-Authors
        $coAuthors = \App\Models\CoAuthorEngine::getArticleCoAuthors((int)$article['id']);

        Response::view('blog/article', [
            'title' => $article['title'] . ' | TechScout',
            'article' => $article,
            'tags' => $tags,
            'comments' => $comments,
            'relatedArticles' => $relatedArticles,
            'isBookmarked' => $isBookmarked,
            'savedProgress' => $savedProgress,
            'coAuthors' => $coAuthors
        ]);
    }
}
