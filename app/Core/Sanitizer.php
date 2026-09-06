<?php
namespace App\Core;

class Sanitizer {
    public static function clean(string $data): string {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    public static function slug(string $title): string {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug ?: 'item-' . time();
    }

    public static function calculateReadTime(string $content): int {
        $wordCount = str_word_count(strip_tags($content));
        $codeBlockCount = substr_count($content, '<pre') + substr_count($content, '```');
        $minutes = ceil(($wordCount / 200) + (($codeBlockCount * 15) / 60));
        return max(1, (int)$minutes);
    }

    public static function truncate(string $text, int $limit = 150): string {
        $clean = strip_tags($text);
        if (mb_strlen($clean) <= $limit) {
            return $clean;
        }
        return mb_substr($clean, 0, $limit) . '...';
    }

    public static function timeAgo(string $datetime): string {
        $time = strtotime($datetime);
        $diff = time() - $time;
        if ($diff < 60) return 'Just now';
        if ($diff < 3600) return floor($diff / 60) . 'm ago';
        if ($diff < 86400) return floor($diff / 3600) . 'h ago';
        if ($diff < 2592000) return floor($diff / 86400) . 'd ago';
        return date('M j, Y', $time);
    }
}
