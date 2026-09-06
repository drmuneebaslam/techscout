<?php
namespace App\Core;

use Throwable;

class ContentFilter {
    public static function sanitize(string $text): string {
        try {
            $filters = Database::query("SELECT * FROM content_filters WHERE action = 'replace'");
            foreach ($filters as $f) {
                $pattern = $f['pattern'];
                $replacement = $f['replacement'] ?: '***';

                if ($f['match_type'] === 'exact') {
                    $text = preg_replace('/\b' . preg_quote($pattern, '/') . '\b/i', $replacement, $text);
                } elseif ($f['match_type'] === 'regex') {
                    $text = @preg_replace($pattern, $replacement, $text) ?: $text;
                } else {
                    $text = str_ireplace($pattern, $replacement, $text);
                }
            }
        } catch (Throwable $e) {}

        return $text;
    }

    public static function checkQuarantine(string $text): bool {
        try {
            $filters = Database::query("SELECT * FROM content_filters WHERE action = 'auto_quarantine'");
            foreach ($filters as $f) {
                if (stripos($text, $f['pattern']) !== false) {
                    return true;
                }
            }
        } catch (Throwable $e) {}

        return false;
    }
}
