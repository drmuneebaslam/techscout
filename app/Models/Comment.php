<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Cache;
use App\Core\CursorPaginator;

class Comment {
    /**
     * Keyset / Cursor-Based Comment Threads (Scales to 10,000+ comments with zero lag)
     * 
     * @param int $contentId Content entity ID
     * @param int $limit Number of top-level threads to retrieve
     * @param string|null $cursor Opaque cursor token
     * @return array ['threads' => array, 'next_cursor' => string|null, 'has_more' => bool, 'total_root_count' => int]
     */
    public static function getCursorThreads(int $contentId, int $limit = 10, ?string $cursor = null): array {
        $cacheKey = "comments:cursor:{$contentId}:" . ($cursor ? md5($cursor) : 'p1') . ":l{$limit}";

        return Cache::remember($cacheKey, 180, function() use ($contentId, $limit, $cursor) {
            $baseSql = "SELECT c.*, u.username, u.role, a.display_name, a.avatar_url, a.verification_badge 
                        FROM comments c 
                        JOIN users u ON c.user_id = u.id 
                        LEFT JOIN authors a ON u.id = a.user_id 
                        WHERE c.content_id = ? AND c.parent_id IS NULL AND c.status IN ('visible', 'flagged')";

            $result = CursorPaginator::paginate(
                $baseSql,
                [$contentId],
                $limit,
                $cursor,
                'id',
                'DESC',
                'c'
            );

            $rootComments = $result['items'];
            if (empty($rootComments)) {
                return [
                    'threads' => [],
                    'next_cursor' => null,
                    'has_more' => false,
                    'total_count' => (int)(Database::queryScalar("SELECT COUNT(*) FROM comments WHERE content_id = ? AND status IN ('visible', 'flagged')", [$contentId]) ?? 0)
                ];
            }

            // Batch fetch replies for retrieved root threads in a single indexed query
            $rootIds = array_column($rootComments, 'id');
            $inPlaceholders = implode(',', array_fill(0, count($rootIds), '?'));

            $replies = Database::query(
                "SELECT c.*, u.username, u.role, a.display_name, a.avatar_url, a.verification_badge 
                 FROM comments c 
                 JOIN users u ON c.user_id = u.id 
                 LEFT JOIN authors a ON u.id = a.user_id 
                 WHERE c.parent_id IN ({$inPlaceholders}) AND c.status IN ('visible', 'flagged') 
                 ORDER BY c.created_at ASC",
                $rootIds
            );

            $repliesByParent = [];
            foreach ($replies as $rep) {
                $pId = (int)$rep['parent_id'];
                $repliesByParent[$pId][] = $rep;
            }

            foreach ($rootComments as &$root) {
                $root['replies'] = $repliesByParent[(int)$root['id']] ?? [];
            }

            return [
                'threads' => $rootComments,
                'next_cursor' => $result['next_cursor'],
                'has_more' => $result['has_more'],
                'count' => count($rootComments)
            ];
        }, ['comments:' . $contentId]);
    }

    /**
     * Legacy tree loader with safety cap and high-speed caching
     */
    public static function getTreeForContent(int $contentId): array {
        $cacheKey = "comments:tree:{$contentId}";

        return Cache::remember($cacheKey, 180, function() use ($contentId) {
            // Safety cap: fetch max 150 top comments to prevent memory exhaustion
            $comments = Database::query(
                "SELECT c.*, u.username, u.role, a.display_name, a.avatar_url, a.verification_badge 
                 FROM comments c 
                 JOIN users u ON c.user_id = u.id 
                 LEFT JOIN authors a ON u.id = a.user_id 
                 WHERE c.content_id = ? AND c.status IN ('visible', 'flagged') 
                 ORDER BY c.is_pinned DESC, c.id DESC LIMIT 150", 
                [$contentId]
            );

            $itemsById = [];
            foreach ($comments as $c) {
                $c['replies'] = [];
                $itemsById[$c['id']] = $c;
            }

            $tree = [];
            foreach ($itemsById as $id => &$item) {
                if (!empty($item['parent_id']) && isset($itemsById[$item['parent_id']])) {
                    $itemsById[$item['parent_id']]['replies'][] = &$item;
                } else {
                    $tree[] = &$item;
                }
            }

            return $tree;
        }, ['comments:' . $contentId]);
    }

    public static function add(int $contentId, int $userId, string $text, ?int $parentId = null): int {
        Database::execute("INSERT INTO comments (content_id, user_id, parent_id, comment_text, status) VALUES (?, ?, ?, ?, 'visible')", [
            $contentId, $userId, $parentId, $text
        ]);
        $newId = (int)Database::lastInsertId();
        Database::execute("UPDATE contents SET comment_count = comment_count + 1 WHERE id = ?", [$contentId]);
        if ($parentId) {
            Database::execute("UPDATE comments SET reply_count = reply_count + 1 WHERE id = ?", [$parentId]);
        }

        // Targeted cache invalidation for this content's comments
        Cache::invalidateTag('comments:' . $contentId);
        Cache::invalidateTag('contents');

        return $newId;
    }

    public static function moderate(int $id, string $status): bool {
        $comm = Database::fetch("SELECT content_id FROM comments WHERE id = ?", [$id]);
        $res = Database::execute("UPDATE comments SET status = ? WHERE id = ?", [$status, $id]);
        if ($comm && !empty($comm['content_id'])) {
            Cache::invalidateTag('comments:' . $comm['content_id']);
        }
        return $res;
    }
}
