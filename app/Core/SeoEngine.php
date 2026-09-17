<?php
namespace App\Core;

use Throwable;

class SeoEngine {
    public static function generateSitemapXml(): string {
        $appUrl = rtrim(Config::get('app_url'), '/');
        
        $urls = [
            ['loc' => $appUrl . '/', 'priority' => '1.0', 'changefreq' => 'hourly'],
            ['loc' => $appUrl . '/blog', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['loc' => $appUrl . '/tools', 'priority' => '0.8', 'changefreq' => 'daily'],
            ['loc' => $appUrl . '/gpts', 'priority' => '0.8', 'changefreq' => 'daily'],
            ['loc' => $appUrl . '/prompts', 'priority' => '0.8', 'changefreq' => 'daily'],
            ['loc' => $appUrl . '/categories', 'priority' => '0.7', 'changefreq' => 'weekly'],
            ['loc' => $appUrl . '/jobs', 'priority' => '0.7', 'changefreq' => 'daily'],
            ['loc' => $appUrl . '/challenges', 'priority' => '0.7', 'changefreq' => 'weekly'],
            ['loc' => $appUrl . '/author/apply', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => $appUrl . '/support', 'priority' => '0.5', 'changefreq' => 'monthly']
        ];

        try {
            // Articles
            $articles = Database::query("SELECT slug, updated_at FROM contents WHERE content_type = 'article' AND status = 'published' ORDER BY id DESC LIMIT 500");
            foreach ($articles as $a) {
                $urls[] = [
                    'loc' => $appUrl . '/article/' . $a['slug'],
                    'lastmod' => date('Y-m-d', strtotime($a['updated_at'] ?? 'now')),
                    'priority' => '0.8',
                    'changefreq' => 'weekly'
                ];
            }

            // Tools
            $tools = Database::query("SELECT slug, updated_at FROM contents WHERE content_type = 'ai_tool' AND status = 'published' ORDER BY id DESC LIMIT 200");
            foreach ($tools as $t) {
                $urls[] = [
                    'loc' => $appUrl . '/tool/' . $t['slug'],
                    'lastmod' => date('Y-m-d', strtotime($t['updated_at'] ?? 'now')),
                    'priority' => '0.7',
                    'changefreq' => 'weekly'
                ];
            }

            // GPTs
            $gpts = Database::query("SELECT slug, updated_at FROM contents WHERE content_type = 'gpt_listing' AND status = 'published' ORDER BY id DESC LIMIT 200");
            foreach ($gpts as $g) {
                $urls[] = [
                    'loc' => $appUrl . '/gpt/' . $g['slug'],
                    'lastmod' => date('Y-m-d', strtotime($g['updated_at'] ?? 'now')),
                    'priority' => '0.7',
                    'changefreq' => 'weekly'
                ];
            }
        } catch (Throwable $e) {}

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($u['loc']) . "</loc>\n";
            if (!empty($u['lastmod'])) {
                $xml .= "    <lastmod>" . $u['lastmod'] . "</lastmod>\n";
            }
            $xml .= "    <changefreq>" . ($u['changefreq'] ?? 'weekly') . "</changefreq>\n";
            $xml .= "    <priority>" . ($u['priority'] ?? '0.5') . "</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        return $xml;
    }

    public static function getRobotsTxt(): string {
        try {
            $r = Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'custom_robots_txt'");
            if (!empty($r['key_value'])) {
                return $r['key_value'];
            }
        } catch (Throwable $e) {}

        $appUrl = rtrim(Config::get('app_url'), '/');
        return "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /auth/\nSitemap: {$appUrl}/sitemap.xml";
    }
}
