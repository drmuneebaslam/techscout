<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;
use App\Core\Auth;

class CategoryController {

    /**
     * Public Categories & Subcategories Explorer
     */
    public function index(Request $request): void {
        $q = trim((string)$request->query('q', ''));
        $entityScope = trim((string)($request->query('entity') ?? $request->query('type') ?? $request->query('scope') ?? 'all'));
        $user = Auth::user();
        $userId = $user ? (int)$user['id'] : 0;

        $hasCc = false;
        try {
            $check = Database::fetch("SHOW TABLES LIKE 'content_categories'");
            $hasCc = !empty($check);
        } catch (\Throwable $e) {}

        $cntJoin = $hasCc 
            ? "LEFT JOIN content_categories cc ON cc.category_id = c.id LEFT JOIN contents cnt ON ((cnt.id = cc.content_id OR cnt.category_id = c.id) AND cnt.status = 'published')"
            : "LEFT JOIN contents cnt ON (cnt.category_id = c.id AND cnt.status = 'published')";

        // Fetch Root Categories (parent_id IS NULL)
        $rootSql = "
            SELECT c.*, 
                   COUNT(DISTINCT sub.id) as subcategories_count,
                   COUNT(DISTINCT cnt.id) as total_items
            FROM categories c
            LEFT JOIN categories sub ON sub.parent_id = c.id
            {$cntJoin}
            WHERE c.parent_id IS NULL
        ";
        $params = [];
        if ($entityScope !== 'all') {
            $rootSql .= " AND (c.applicable_to = ? OR EXISTS (SELECT 1 FROM categories sub2 WHERE sub2.parent_id = c.id AND sub2.applicable_to = ?))";
            $params[] = $entityScope;
            $params[] = $entityScope;
        }
        if ($q !== '') {
            $rootSql .= " AND (c.name LIKE ? OR c.description LIKE ?)";
            $params[] = "%{$q}%";
            $params[] = "%{$q}%";
        }
        $rootSql .= " GROUP BY c.id ORDER BY c.sort_order ASC, c.name ASC";
        $rootCategories = Database::query($rootSql, $params);

        // Fetch all Subcategories
        $subSql = "
            SELECT c.*, 
                   p.name as parent_name,
                   p.slug as parent_slug,
                   COUNT(DISTINCT cnt.id) as total_items
            FROM categories c
            JOIN categories p ON c.parent_id = p.id
            {$cntJoin}
            WHERE c.parent_id IS NOT NULL
        ";
        $subParams = [];
        if ($entityScope !== 'all') {
            $subSql .= " AND c.applicable_to = ?";
            $subParams[] = $entityScope;
        }
        $subSql .= " GROUP BY c.id ORDER BY p.name ASC, c.sort_order ASC, c.name ASC";
        $allSubcategories = Database::query($subSql, $subParams);

        // Group subcategories by parent_id
        $subsByParent = [];
        foreach ($allSubcategories as $sub) {
            $subsByParent[$sub['parent_id']][] = $sub;
        }

        // Subscriptions
        $subscribedCatIds = [];
        if ($userId > 0) {
            try {
                $catSubs = Database::query("SELECT subscribable_id FROM user_subscriptions WHERE user_id = ? AND subscribable_type = 'category'", [$userId]);
                $subscribedCatIds = array_column($catSubs, 'subscribable_id');
            } catch (\Throwable $e) {}
        }

        // Subscriber counts per category
        $subscriberCounts = [];
        try {
            $subCountsRaw = Database::query("SELECT subscribable_id, COUNT(*) as cnt FROM user_subscriptions WHERE subscribable_type = 'category' GROUP BY subscribable_id");
            foreach ($subCountsRaw as $sc) {
                $subscriberCounts[$sc['subscribable_id']] = (int)$sc['cnt'];
            }
        } catch (\Throwable $e) {}

        Response::view('public/categories', [
            'title' => 'Explore Categories & Subcategories | TechScout',
            'rootCategories' => $rootCategories,
            'subsByParent' => $subsByParent,
            'subscribedCatIds' => $subscribedCatIds,
            'subscriberCounts' => $subscriberCounts,
            'currentScope' => $entityScope,
            'searchQuery' => $q
        ]);
    }

    /**
     * Single Category or Subcategory Showcase
     */
    public function show(Request $request, array $params = []): void {
        $slug = $params['slug'] ?? '';
        $entityType = trim((string)($request->query('entity') ?? $request->query('type') ?? $request->query('scope') ?? 'all'));
        $category = Database::fetch("SELECT c.*, p.name as parent_name, p.slug as parent_slug FROM categories c LEFT JOIN categories p ON c.parent_id = p.id WHERE c.slug = ?", [$slug]);

        if (!$category) {
            Response::redirect('/categories');
            return;
        }

        $user = Auth::user();
        $userId = $user ? (int)$user['id'] : 0;
        $isSubscribed = false;
        if ($userId > 0) {
            try {
                $sub = Database::fetch("SELECT id FROM user_subscriptions WHERE user_id = ? AND subscribable_type = 'category' AND subscribable_id = ?", [$userId, $category['id']]);
                $isSubscribed = !empty($sub);
            } catch (\Throwable $e) {}
        }

        $followerCount = 0;
        try {
            $followerCount = (int)(Database::fetch("SELECT COUNT(*) as c FROM user_subscriptions WHERE subscribable_type = 'category' AND subscribable_id = ?", [$category['id']])['c'] ?? 0);
        } catch (\Throwable $e) {}

        // Fetch subcategories if this is a root category, or siblings if it is a subcategory
        $subcategories = [];
        if (empty($category['parent_id'])) {
            $subcategories = Database::query("SELECT * FROM categories WHERE parent_id = ? ORDER BY sort_order ASC, name ASC", [$category['id']]);
        } else {
            $subcategories = Database::query("SELECT * FROM categories WHERE parent_id = ? AND id != ? ORDER BY sort_order ASC, name ASC", [$category['parent_id'], $category['id']]);
        }

        $hasCc = false;
        try {
            $check = Database::fetch("SHOW TABLES LIKE 'content_categories'");
            $hasCc = !empty($check);
        } catch (\Throwable $e) {}

        // Fetch Associated Entities via junction and/or primary category_id
        if ($hasCc) {
            $contentSql = "
                SELECT DISTINCT c.*, 
                       COALESCE(cc.is_primary, 1) as category_is_primary,
                       a.display_name as author_name, 
                       a.slug as author_slug, 
                       a.avatar_url as author_avatar
                FROM contents c
                LEFT JOIN content_categories cc ON cc.content_id = c.id
                LEFT JOIN authors a ON c.author_id = a.id
                WHERE (c.category_id = ? OR cc.category_id = ?) AND c.status = 'published'
            ";
            $contentParams = [(int)$category['id'], (int)$category['id']];
        } else {
            $contentSql = "
                SELECT c.*, 
                       1 as category_is_primary,
                       a.display_name as author_name, 
                       a.slug as author_slug, 
                       a.avatar_url as author_avatar
                FROM contents c
                LEFT JOIN authors a ON c.author_id = a.id
                WHERE c.category_id = ? AND c.status = 'published'
            ";
            $contentParams = [(int)$category['id']];
        }

        if ($entityType !== 'all') {
            $contentSql .= " AND c.content_type = ?";
            $contentParams[] = $entityType;
        }
        $contentSql .= " ORDER BY c.published_at DESC LIMIT 60";

        $items = Database::query($contentSql, $contentParams);

        // Entity breakdown counts for tabs
        $entityCounts = [
            'all' => 0,
            'article' => 0,
            'prompt' => 0,
            'image_prompt' => 0,
            'ai_tool' => 0,
            'gpt_listing' => 0,
            'campaign' => 0
        ];

        if ($hasCc) {
            $allContentCounts = Database::query("
                SELECT c.content_type, COUNT(DISTINCT c.id) as cnt
                FROM contents c
                LEFT JOIN content_categories cc ON cc.content_id = c.id
                WHERE (c.category_id = ? OR cc.category_id = ?) AND c.status = 'published'
                GROUP BY c.content_type
            ", [(int)$category['id'], (int)$category['id']]);
        } else {
            $allContentCounts = Database::query("
                SELECT c.content_type, COUNT(DISTINCT c.id) as cnt
                FROM contents c
                WHERE c.category_id = ? AND c.status = 'published'
                GROUP BY c.content_type
            ", [(int)$category['id']]);
        }

        foreach ($allContentCounts as $row) {
            $t = $row['content_type'];
            if (isset($entityCounts[$t])) {
                $entityCounts[$t] = (int)$row['cnt'];
                $entityCounts['all'] += (int)$row['cnt'];
            }
        }

        Response::view('public/category_single', [
            'title' => htmlspecialchars($category['name']) . ' | Category Architecture',
            'category' => $category,
            'subcategories' => $subcategories,
            'items' => $items,
            'isSubscribed' => $isSubscribed,
            'followerCount' => $followerCount,
            'currentEntity' => $entityType,
            'entityCounts' => $entityCounts
        ]);
    }

    /**
     * AJAX Category Subscription Toggle
     */
    public function toggleSubscribe(Request $request, array $params = []): void {
        header('Content-Type: application/json; charset=utf-8');

        if (!Auth::check()) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'error' => 'Please sign in to subscribe to categories and topics.',
                'redirect' => url('/auth/login')
            ]);
            exit;
        }

        $catId = (int)($params['id'] ?? 0);
        $userId = (int)Auth::id();

        if ($catId <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid category identifier.']);
            exit;
        }

        $cat = Database::fetch("SELECT id, name FROM categories WHERE id = ?", [$catId]);
        if (!$cat) {
            echo json_encode(['success' => false, 'error' => 'Category not found.']);
            exit;
        }

        $existing = Database::fetch("SELECT id FROM user_subscriptions WHERE user_id = ? AND subscribable_type = 'category' AND subscribable_id = ?", [$userId, $catId]);

        if ($existing) {
            Database::execute("DELETE FROM user_subscriptions WHERE id = ?", [$existing['id']]);
            $isSubscribed = false;
            $msg = "Unsubscribed from {$cat['name']}.";
        } else {
            Database::execute("INSERT INTO user_subscriptions (user_id, subscribable_type, subscribable_id, created_at) VALUES (?, 'category', ?, NOW())", [$userId, $catId]);
            $isSubscribed = true;
            $msg = "Subscribed to {$cat['name']}!";
        }

        $followerCount = (int)(Database::fetch("SELECT COUNT(*) as c FROM user_subscriptions WHERE subscribable_type = 'category' AND subscribable_id = ?", [$catId])['c'] ?? 0);

        echo json_encode([
            'success' => true,
            'is_subscribed' => $isSubscribed,
            'follower_count' => $followerCount,
            'message' => $msg
        ]);
        exit;
    }

    /**
     * Legacy Topics Redirection Helpers
     */
    public function legacyTopics(): void {
        Response::redirect('/categories');
    }

    public function legacyTopicSingle(Request $request, array $params = []): void {
        $slug = $params['slug'] ?? '';
        Response::redirect('/category/' . urlencode($slug));
    }
}
