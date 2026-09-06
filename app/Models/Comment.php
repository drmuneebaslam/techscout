<?php
namespace App\Models;

use App\Core\Database;

class Comment {
    public static function getTreeForContent(int $contentId): array {
        // Fetch all approved/visible comments for content
        $comments = Database::query("SELECT c.*, u.username, u.role, a.display_name, a.avatar_url, a.verification_badge 
                                    FROM comments c 
                                    JOIN users u ON c.user_id = u.id 
                                    LEFT JOIN authors a ON u.id = a.user_id 
                                    WHERE c.content_id = ? AND c.status IN ('visible', 'flagged') 
                                    ORDER BY c.is_pinned DESC, c.created_at ASC", [$contentId]);
        
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
        return $newId;
    }

    public static function moderate(int $id, string $status): bool {
        return Database::execute("UPDATE comments SET status = ? WHERE id = ?", [$status, $id]);
    }
}
