<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;
use App\Core\Auth;

class TopicController {

    public function index(Request $request): void {
        $q = trim((string)$request->query('q', ''));
        $user = Auth::user();
        $userId = $user ? (int)$user['id'] : 0;

        // Fetch all topics
        $topics = Database::query("SELECT * FROM topics ORDER BY sort_order ASC, name ASC");

        // Fetch all categories grouped by topic
        $categories = Database::query("SELECT c.*, t.name as topic_name, t.slug as topic_slug 
                                      FROM categories c 
                                      LEFT JOIN topics t ON c.topic_id = t.id 
                                      ORDER BY c.sort_order ASC, c.name ASC");

        // Fetch user subscriptions if logged in
        $subscribedTopicIds = [];
        $subscribedCatIds = [];
        if ($userId > 0) {
            $topicSubs = Database::query("SELECT subscribable_id FROM user_subscriptions WHERE user_id = ? AND subscribable_type = 'topic'", [$userId]);
            $subscribedTopicIds = array_column($topicSubs, 'subscribable_id');

            $catSubs = Database::query("SELECT subscribable_id FROM user_subscriptions WHERE user_id = ? AND subscribable_type = 'category'", [$userId]);
            $subscribedCatIds = array_column($catSubs, 'subscribable_id');
        }

        // Group categories by topic_id
        $grouped = [];
        foreach ($topics as $topic) {
            $grouped[$topic['id']] = [
                'topic' => $topic,
                'categories' => [],
                'is_subscribed' => in_array($topic['id'], $subscribedTopicIds, true)
            ];
        }

        $unassignedCats = [];
        foreach ($categories as $cat) {
            $tId = $cat['topic_id'];
            $cat['is_subscribed'] = in_array($cat['id'], $subscribedCatIds, true);
            if ($tId && isset($grouped[$tId])) {
                $grouped[$tId]['categories'][] = $cat;
            } else {
                $unassignedCats[] = $cat;
            }
        }

        Response::view('public/topics', [
            'title' => 'Explore Topics & Engineering Domains | TechScout',
            'topics' => $topics,
            'grouped' => $grouped,
            'unassignedCats' => $unassignedCats,
            'searchQuery' => $q,
            'totalCategories' => count($categories)
        ]);
    }

    public function show(Request $request, array $params = []): void {
        $slug = $params['slug'] ?? '';
        $topic = Database::fetch("SELECT * FROM topics WHERE slug = ?", [$slug]);

        if (!$topic) {
            Response::redirect('/topics');
            return;
        }

        $user = Auth::user();
        $userId = $user ? (int)$user['id'] : 0;
        $isSubscribed = false;
        if ($userId > 0) {
            $sub = Database::fetch("SELECT id FROM user_subscriptions WHERE user_id = ? AND subscribable_type = 'topic' AND subscribable_id = ?", [$userId, $topic['id']]);
            $isSubscribed = !empty($sub);
        }

        // Fetch categories under this topic
        $categories = Database::query("SELECT * FROM categories WHERE topic_id = ? OR parent_id = ? ORDER BY sort_order ASC, name ASC", [$topic['id'], $topic['id']]);

        // Category IDs for content query
        $catIds = array_column($categories, 'id');
        if (empty($catIds)) {
            $catIds = [-1];
        }
        $placeholders = implode(',', array_fill(0, count($catIds), '?'));

        // Fetch top contents in this topic
        $articles = Database::query("SELECT c.*, a.display_name as author_name, a.slug as author_slug, a.avatar_url as author_avatar, cat.name as category_name 
                                    FROM contents c 
                                    LEFT JOIN authors a ON c.author_id = a.id 
                                    LEFT JOIN categories cat ON c.category_id = cat.id 
                                    WHERE c.category_id IN ({$placeholders}) AND c.status = 'published' 
                                    ORDER BY c.published_at DESC LIMIT 12", $catIds);

        Response::view('public/topic_single', [
            'title' => $topic['name'] . ' — Engineering Blueprints & Radar | TechScout',
            'topic' => $topic,
            'categories' => $categories,
            'articles' => $articles,
            'isSubscribed' => $isSubscribed
        ]);
    }
}
