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

            Database::execute("INSERT IGNORE INTO tags (name, slug) VALUES (?, ?)", [$name, $slug]);
            $tag = Database::fetch("SELECT id FROM tags WHERE slug = ?", [$slug]);
            if ($tag) {
                $tagIds[] = (int)$tag['id'];
            }
        }

        // Clean & Re-associate
        Database::execute("DELETE FROM content_tags WHERE content_id = ?", [$contentId]);
        foreach (array_unique($tagIds) as $tId) {
            Database::execute("INSERT IGNORE INTO content_tags (content_id, tag_id) VALUES (?, ?)", [$contentId, $tId]);
        }
    }

    public static function getTagsForContent(int $contentId): array {
        if ($contentId <= 0) return [];
        return Database::query("SELECT t.id, t.name, t.slug 
                                FROM tags t 
                                JOIN content_tags ct ON t.id = ct.tag_id 
                                WHERE ct.content_id = ? 
                                ORDER BY t.name ASC", [$contentId]);
    }

    public static function getTagsString(int $contentId): string {
        $tags = self::getTagsForContent($contentId);
        $names = array_column($tags, 'name');
        return implode(', ', $names);
    }
}
