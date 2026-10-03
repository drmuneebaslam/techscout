<?php
namespace App\Models;

use App\Core\Database;

class Content {
    public static function getFeed(?string $type = null, ?string $category = null, string $sort = 'latest', int $limit = 12, int $offset = 0, array $filters = []): array {
        $sql = "SELECT c.*, 
                       cat.name as category_name, cat.slug as category_slug, cat.color_hex as category_color, cat.parent_id,
                       pcat.name as parent_category_name, pcat.slug as parent_category_slug,
                       a.display_name as author_name, a.slug as author_slug, a.avatar_url as author_avatar, a.verification_badge,
                       art.reading_time_mins, art.difficulty_level,
                       tool.pricing_model, tool.rating_avg as tool_rating, tool.website_url,
                       gpt.openai_url, gpt.model_base, gpt.prompt_starters,
                       p.prompt_type, p.prompt_template, p.copy_count, p.template_variables, p.target_models,
                       camp.prize_pool, camp.end_date, camp.start_date, camp.campaign_type
                FROM contents c
                LEFT JOIN categories cat ON c.category_id = cat.id
                LEFT JOIN categories pcat ON cat.parent_id = pcat.id
                LEFT JOIN authors a ON c.author_id = a.id
                LEFT JOIN articles art ON c.id = art.content_id
                LEFT JOIN ai_tools tool ON c.id = tool.content_id
                LEFT JOIN gpt_listings gpt ON c.id = gpt.content_id
                LEFT JOIN prompts p ON c.id = p.content_id
                LEFT JOIN campaigns camp ON c.id = camp.content_id
                WHERE c.status = 'published' AND c.visibility = 'public'";
        
        $params = [];
        if ($type && $type !== 'all') {
            $sql .= " AND c.content_type = ?";
            $params[] = $type;
        }

        if ($category && $category !== 'all') {
            $sql .= " AND (cat.slug = ? OR cat.parent_id IN (SELECT subc.id FROM categories subc WHERE subc.slug = ?))";
            $params[] = $category;
            $params[] = $category;
        }

        // Prompt Kind Filter (Text vs Image)
        if (!empty($filters['prompt_kind']) && $filters['prompt_kind'] !== 'all') {
            if ($filters['prompt_kind'] === 'image') {
                $sql .= " AND (p.prompt_type = 'image' 
                              OR cat.slug IN ('ai-image-prompts', 'midjourney', 'dall-e', 'flux', 'stable-diffusion', 'photorealism', '3d-renders', 'cyberpunk', 'anime', 'architecture', 'landscapes', 'fantasy') 
                              OR cat.parent_id IN (SELECT subc.id FROM categories subc WHERE subc.slug = 'ai-image-prompts')
                              OR p.target_models LIKE '%Midjourney%' 
                              OR p.target_models LIKE '%DALL-E%' 
                              OR p.target_models LIKE '%Flux%' 
                              OR p.target_models LIKE '%Stable Diffusion%'
                              OR p.target_models LIKE '%Leonardo%'
                              OR p.target_models LIKE '%Ideogram%')";
            } elseif ($filters['prompt_kind'] === 'text') {
                $sql .= " AND (p.prompt_type = 'text' 
                              OR (p.prompt_type IS NULL 
                                  AND cat.slug NOT IN ('ai-image-prompts', 'midjourney', 'dall-e', 'flux', 'stable-diffusion', 'photorealism', '3d-renders', 'cyberpunk', 'anime', 'architecture', 'landscapes', 'fantasy') 
                                  AND (p.target_models IS NULL OR (p.target_models NOT LIKE '%Midjourney%' AND p.target_models NOT LIKE '%Flux%' AND p.target_models NOT LIKE '%Stable Diffusion%'))))";
            }
        }

        // Search Filter
        if (!empty($filters['search'])) {
            $sql .= " AND (c.title LIKE ? OR c.summary LIKE ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }

        // Pricing Filter (AI Tools)
        if (!empty($filters['pricing']) && $filters['pricing'] !== 'all') {
            $sql .= " AND LOWER(tool.pricing_model) = ?";
            $params[] = strtolower($filters['pricing']);
        }

        // Reading Time Filter (Articles)
        if (!empty($filters['reading_time']) && $filters['reading_time'] !== 'all') {
            if ($filters['reading_time'] === 'short') {
                $sql .= " AND art.reading_time_mins <= 5";
            } elseif ($filters['reading_time'] === 'medium') {
                $sql .= " AND art.reading_time_mins > 5 AND art.reading_time_mins <= 10";
            } elseif ($filters['reading_time'] === 'deep') {
                $sql .= " AND art.reading_time_mins > 10";
            }
        }

        // Target Model Filter (Prompts & GPTs)
        if (!empty($filters['model']) && $filters['model'] !== 'all') {
            $sql .= " AND (p.target_models LIKE ? OR gpt.model_base LIKE ?)";
            $params[] = '%' . $filters['model'] . '%';
            $params[] = '%' . $filters['model'] . '%';
        }

        // Campaign Status Filter
        if (!empty($filters['campaign_status']) && $filters['campaign_status'] !== 'all') {
            if ($filters['campaign_status'] === 'active') {
                $sql .= " AND (camp.end_date IS NULL OR camp.end_date >= NOW())";
            } elseif ($filters['campaign_status'] === 'completed') {
                $sql .= " AND camp.end_date < NOW()";
            }
        }

        switch ($sort) {
            case 'trending':
                $sql .= " ORDER BY c.is_pinned DESC, c.is_trending DESC, c.reaction_count DESC, c.view_count DESC, c.published_at DESC";
                break;
            case 'popular':
                $sql .= " ORDER BY c.is_pinned DESC, c.view_count DESC, c.reaction_count DESC";
                break;
            case 'featured':
                $sql .= " ORDER BY c.is_pinned DESC, c.is_featured DESC, c.published_at DESC";
                break;
            case 'rating':
                $sql .= " ORDER BY c.is_pinned DESC, tool.rating_avg DESC, c.view_count DESC";
                break;
            case 'copies':
                $sql .= " ORDER BY c.is_pinned DESC, p.copy_count DESC, c.view_count DESC";
                break;
            case 'reading_time':
                $sql .= " ORDER BY c.is_pinned DESC, art.reading_time_mins ASC, c.published_at DESC";
                break;
            case 'algorithmic':
            case 'latest':
            default:
                // Advanced Engagement & Freshness Algorithm with Hourly Rotation
                // Prevents static feeds by combining views, reactions, and an hourly random seed
                $sql .= " ORDER BY c.is_pinned DESC, ((COALESCE(c.reaction_count, 0) * 10) + COALESCE(c.view_count, 0) + 1) * RAND(UNIX_TIMESTAMP(NOW()) DIV 3600) DESC, c.published_at DESC";
                break;
        }

        $sql .= " LIMIT {$limit} OFFSET {$offset}";
        return Database::cachedQuery($sql, $params, 300, ['contents', 'feed']);
    }

    public static function findBySlug(string $slug): ?array {
        $sql = "SELECT c.*, 
                       cat.name as category_name, cat.slug as category_slug, cat.color_hex as category_color, cat.topic_id, cat.parent_id,
                       pcat.name as parent_category_name, pcat.slug as parent_category_slug, pcat.id as parent_category_id,
                       t.name as topic_name, t.slug as topic_slug, t.icon_name as topic_icon, t.color_hex as topic_color,
                       a.id as author_id, a.display_name as author_name, a.slug as author_slug, a.headline as author_headline, a.bio as author_bio, a.avatar_url as author_avatar, a.verification_badge, a.reputation_score, a.follower_count as author_followers,
                       art.reading_time_mins, art.difficulty_level, art.table_of_contents, art.citation_sources,
                       tool.*, tool.rating_avg as tool_rating, tool.rating_count as tool_reviews,
                       gpt.openai_url, gpt.model_base, gpt.prompt_starters,
                       p.prompt_type, p.prompt_template, p.template_variables, p.target_models, p.copy_count, p.sample_output,
                       camp.campaign_type, camp.start_date, camp.end_date, camp.prize_pool, camp.rules_markdown,
                       c.slug as slug, c.id as id
                FROM contents c
                LEFT JOIN categories cat ON c.category_id = cat.id
                LEFT JOIN categories pcat ON cat.parent_id = pcat.id
                LEFT JOIN topics t ON cat.topic_id = t.id
                LEFT JOIN authors a ON c.author_id = a.id
                LEFT JOIN articles art ON c.id = art.content_id
                LEFT JOIN ai_tools tool ON c.id = tool.content_id
                LEFT JOIN gpt_listings gpt ON c.id = gpt.content_id
                LEFT JOIN prompts p ON c.id = p.content_id
                LEFT JOIN campaigns camp ON c.id = camp.content_id
                WHERE c.slug = ?";
        
        return Database::cachedFetch($sql, [$slug], 600, ['contents', 'content:slug:' . $slug]);
    }

    public static function invalidateCache(?int $id = null, ?string $slug = null): void {
        \App\Core\Cache::invalidateTag('contents');
        \App\Core\Cache::invalidateTag('feed');
        if ($id) {
            \App\Core\Cache::invalidateTag('content:' . $id);
        }
        if ($slug) {
            \App\Core\Cache::invalidateTag('content:slug:' . $slug);
        }
    }

    public static function incrementViews(int $id): void {
        // Admin views do NOT count towards analytics
        if (\App\Core\Auth::isAdmin()) {
            return;
        }

        // Author views of own content do NOT count towards analytics
        $userId = \App\Core\Auth::id();
        if ($userId) {
            $item = Database::fetch("SELECT c.author_id, a.user_id FROM contents c LEFT JOIN authors a ON c.author_id = a.id WHERE c.id = ?", [$id]);
            if ($item && !empty($item['user_id']) && (int)$item['user_id'] === (int)$userId) {
                return;
            }
        }

        Database::execute("UPDATE contents SET view_count = view_count + 1 WHERE id = ?", [$id]);
    }

    public static function getTagsForContent(int $contentId): array {
        return Database::query("SELECT t.name, t.slug FROM tags t INNER JOIN content_tags ct ON t.id = ct.tag_id WHERE ct.content_id = ?", [$contentId]);
    }

    public static function search(string $query, string $type = 'all', int $limit = 20): array {
        $term = "%{$query}%";
        $sql = "SELECT c.*, cat.name as category_name, cat.slug as category_slug, a.display_name as author_name, a.slug as author_slug
                FROM contents c
                LEFT JOIN categories cat ON c.category_id = cat.id
                LEFT JOIN authors a ON c.author_id = a.id
                WHERE c.status = 'published' AND (c.title LIKE ? OR c.summary LIKE ? OR c.body_html LIKE ?)";
        $params = [$term, $term, $term];

        if ($type !== 'all') {
            $sql .= " AND c.content_type = ?";
            $params[] = $type;
        }

        $sql .= " ORDER BY c.view_count DESC LIMIT {$limit}";
        return Database::query($sql, $params);
    }

    /**
     * Get personalized feed based on user subscriptions (topics, categories, authors)
     */
    public static function getPersonalizedFeed(int $userId, ?string $type = null, int $limit = 12): array {
        if ($userId <= 0) {
            return self::getFeed($type, null, 'trending', $limit);
        }

        // 1. Get subscribed topics
        $topicSubs = Database::query("SELECT subscribable_id FROM user_subscriptions WHERE user_id = ? AND subscribable_type = 'topic'", [$userId]);
        $topicIds = array_column($topicSubs, 'subscribable_id');

        // 2. Get categories under subscribed topics
        $topicCatIds = [];
        if (!empty($topicIds)) {
            $topicPlaceholders = implode(',', array_fill(0, count($topicIds), '?'));
            $topicCats = Database::query("SELECT id FROM categories WHERE topic_id IN ({$topicPlaceholders}) OR parent_id IN ({$topicPlaceholders})", array_merge($topicIds, $topicIds));
            $topicCatIds = array_column($topicCats, 'id');
        }

        // 3. Get directly subscribed categories
        $catSubs = Database::query("SELECT subscribable_id FROM user_subscriptions WHERE user_id = ? AND subscribable_type = 'category'", [$userId]);
        $directCatIds = array_column($catSubs, 'subscribable_id');

        $allTargetCategoryIds = array_unique(array_merge($topicCatIds, $directCatIds));

        // 4. Get followed authors
        $authorSubs = Database::query("SELECT subscribable_id FROM user_subscriptions WHERE user_id = ? AND subscribable_type = 'author' 
                                       UNION 
                                       SELECT following_author_id FROM follows WHERE follower_user_id = ?", [$userId, $userId]);
        $targetAuthorIds = array_unique(array_column($authorSubs, 'subscribable_id'));

        if (empty($allTargetCategoryIds) && empty($targetAuthorIds)) {
            // No subscriptions yet: return featured/trending items
            return self::getFeed($type, null, 'trending', $limit);
        }

        $conditions = [];
        $params = [];

        if (!empty($allTargetCategoryIds)) {
            $catPlaceholders = implode(',', array_fill(0, count($allTargetCategoryIds), '?'));
            $conditions[] = "c.category_id IN ({$catPlaceholders})";
            $params = array_merge($params, array_values($allTargetCategoryIds));
        }

        if (!empty($targetAuthorIds)) {
            $authorPlaceholders = implode(',', array_fill(0, count($targetAuthorIds), '?'));
            $conditions[] = "c.author_id IN ({$authorPlaceholders})";
            $params = array_merge($params, array_values($targetAuthorIds));
        }

        $whereClause = "(" . implode(' OR ', $conditions) . ")";

        $typeClause = "";
        if ($type && $type !== 'all') {
            $typeClause = " AND c.content_type = ?";
            $params[] = $type;
        }

        $sql = "SELECT c.*, 
                       cat.name as category_name, cat.slug as category_slug, cat.color_hex as category_color,
                       a.display_name as author_name, a.slug as author_slug, a.avatar_url as author_avatar, a.verification_badge,
                       art.reading_time_mins, art.difficulty_level,
                       tool.pricing_model, tool.rating_avg as tool_rating, tool.website_url,
                       gpt.openai_url, gpt.model_base, gpt.prompt_starters,
                       p.copy_count, p.template_variables,
                       camp.prize_pool, camp.end_date
                FROM contents c
                LEFT JOIN categories cat ON c.category_id = cat.id
                LEFT JOIN authors a ON c.author_id = a.id
                LEFT JOIN articles art ON c.id = art.content_id
                LEFT JOIN ai_tools tool ON c.id = tool.content_id
                LEFT JOIN gpt_listings gpt ON c.id = gpt.content_id
                LEFT JOIN prompts p ON c.id = p.content_id
                LEFT JOIN campaigns camp ON c.id = camp.content_id
                WHERE c.status = 'published' AND c.visibility = 'public' AND {$whereClause} {$typeClause}
                ORDER BY c.published_at DESC 
                LIMIT {$limit}";

        $results = Database::query($sql, $params);
        if (empty($results)) {
            return self::getFeed($type, null, 'trending', $limit);
        }
        return $results;
    }

    public static function getByTag(string $tagSlug, int $limit = 20): array {
        $sql = "SELECT c.*, 
                       cat.name as category_name, cat.slug as category_slug, cat.color_hex as category_color,
                       a.display_name as author_name, a.slug as author_slug, a.avatar_url as author_avatar,
                       art.reading_time_mins, art.difficulty_level,
                       tool.pricing_model, tool.rating_avg as tool_rating, tool.website_url,
                       gpt.openai_url, gpt.model_base,
                       p.copy_count
                FROM contents c
                JOIN content_tags ct ON c.id = ct.content_id
                JOIN tags t ON ct.tag_id = t.id
                LEFT JOIN categories cat ON c.category_id = cat.id
                LEFT JOIN authors a ON c.author_id = a.id
                LEFT JOIN articles art ON c.id = art.content_id
                LEFT JOIN ai_tools tool ON c.id = tool.content_id
                LEFT JOIN gpt_listings gpt ON c.id = gpt.content_id
                LEFT JOIN prompts p ON c.id = p.content_id
                WHERE t.slug = ? AND c.status = 'published' AND c.visibility = 'public'
                ORDER BY c.published_at DESC LIMIT {$limit}";
        return Database::query($sql, [$tagSlug]);
    }
}

