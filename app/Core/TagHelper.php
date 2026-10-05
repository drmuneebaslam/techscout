<?php
namespace App\Core;

class TagHelper {
    public static function syncTags(int $contentId, string|array|null $rawTags): void {
        if ($contentId <= 0) return;

        $tagNames = [];
        if (is_array($rawTags)) {
            $tagNames = $rawTags;
        } elseif (is_string($rawTags)) {
            $tagNames = explode(',', $rawTags);
        }

        $tagIds = [];
        foreach ($tagNames as $name) {
            $name = trim((string)$name);
            if ($name === '') continue;

            $slug = Sanitizer::slug($name);
            if (empty($slug)) continue;

            Database::execute("INSERT IGNORE INTO categories (name, slug, applicable_to, created_at) VALUES (?, ?, 'all', NOW())", [$name, $slug]);
            $cat = Database::fetch("SELECT id FROM categories WHERE slug = ?", [$slug]);
            if ($cat) {
                $tagIds[] = (int)$cat['id'];
            }
        }

        // Clean & Re-associate
        Database::execute("DELETE FROM content_categories WHERE content_id = ? AND is_primary = 0", [$contentId]);
        foreach (array_unique($tagIds) as $tId) {
            Database::execute("INSERT IGNORE INTO content_categories (content_id, category_id, is_primary) VALUES (?, ?, 0)", [$contentId, $tId]);
        }
    }

    public static function getTagsForContent(int $contentId): array {
        if ($contentId <= 0) return [];
        return Database::query("SELECT c.id, c.name, c.slug 
                                FROM categories c 
                                JOIN content_categories cc ON c.id = cc.category_id 
                                WHERE cc.content_id = ? AND cc.is_primary = 0
                                ORDER BY c.name ASC", [$contentId]);
    }

    public static function getTagsString(int $contentId): string {
        $tags = self::getTagsForContent($contentId);
        $names = array_column($tags, 'name');
        return implode(', ', $names);
    }
}
