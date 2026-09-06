<?php
namespace App\Core;

use Throwable;

class FeatureFlag {
    private static array $cache = [];
    private static bool $loaded = false;

    // Key aliases mapping
    private static array $aliases = [
        'flag_jobs_board' => 'enable_jobs',
        'enable_jobs' => 'flag_jobs_board',
        'flag_community_comments' => 'enable_comments',
        'enable_comments' => 'flag_community_comments',
        'flag_custom_gpts' => 'enable_gpts',
        'enable_gpts' => 'flag_custom_gpts',
        'flag_prompt_blueprints' => 'enable_prompts',
        'enable_prompts' => 'flag_prompt_blueprints',
        'flag_ai_tools' => 'enable_ai_tools',
        'enable_ai_tools' => 'flag_ai_tools',
        'flag_author_studio' => 'enable_author_registration',
        'enable_author_registration' => 'flag_author_studio'
    ];

    public static function isEnabled(string $flagKey, bool $default = true): bool {
        self::loadAll();
        
        if (isset(self::$cache[$flagKey])) {
            return (bool)self::$cache[$flagKey]['is_enabled'];
        }

        // Check alias
        if (isset(self::$aliases[$flagKey]) && isset(self::$cache[self::$aliases[$flagKey]])) {
            return (bool)self::$cache[self::$aliases[$flagKey]]['is_enabled'];
        }

        return $default;
    }

    public static function getAll(): array {
        self::loadAll();
        return self::$cache;
    }

    public static function setEnabled(string $flagKey, bool $enabled): bool {
        try {
            $val = $enabled ? 1 : 0;
            Database::execute("UPDATE feature_flags SET is_enabled = ?, updated_at = NOW() WHERE flag_key = ?", [
                $val,
                $flagKey
            ]);

            // If alias exists, update it too if present in table
            if (isset(self::$aliases[$flagKey])) {
                $alias = self::$aliases[$flagKey];
                Database::execute("UPDATE feature_flags SET is_enabled = ?, updated_at = NOW() WHERE flag_key = ?", [
                    $val,
                    $alias
                ]);
            }

            self::$loaded = false;
            self::loadAll();
            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    private static function loadAll(): void {
        if (self::$loaded) return;
        try {
            $rows = Database::query("SELECT * FROM feature_flags ORDER BY category ASC, name ASC");
            self::$cache = [];
            foreach ($rows as $r) {
                self::$cache[$r['flag_key']] = $r;
            }
            self::$loaded = true;
        } catch (Throwable $e) {
            self::$loaded = true;
        }
    }
}
