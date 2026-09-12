<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Config;
use App\Core\Database;

class FeedController {
    
    // RSS 2.0 Prompts Feed [TS-API-05]
    public function promptsXml(Request $request): void {
        header('Content-Type: application/xml; charset=utf-8');
        $prompts = Database::query("SELECT p.*, c.name as category_name FROM prompts p LEFT JOIN categories c ON p.category_id = c.id WHERE p.status = 'published' ORDER BY p.created_at DESC LIMIT 50");
        $baseUrl = rtrim(Config::url('/'), '/');

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
        echo '<channel>' . "\n";
        echo '  <title>TechScout — AI Prompts &amp; Engineering Blueprints</title>' . "\n";
        echo '  <link>' . $baseUrl . '/prompts</link>' . "\n";
        echo '  <description>Battle-tested system prompts and visual art blueprints for modern AI models.</description>' . "\n";
        echo '  <language>en-us</language>' . "\n";
        echo '  <atom:link href="' . $baseUrl . '/feed/prompts.xml" rel="self" type="application/rss+xml" />' . "\n";

        foreach ($prompts as $p) {
            $link = $baseUrl . '/prompts/' . $p['slug'];
            $pubDate = date(DATE_RSS, strtotime($p['created_at'] ?? 'now'));
            echo '  <item>' . "\n";
            echo '    <title>' . htmlspecialchars($p['title']) . '</title>' . "\n";
            echo '    <link>' . $link . '</link>' . "\n";
            echo '    <guid>' . $link . '</guid>' . "\n";
            echo '    <pubDate>' . $pubDate . '</pubDate>' . "\n";
            echo '    <category>' . htmlspecialchars($p['category_name'] ?? 'Prompt') . '</category>' . "\n";
            echo '    <description>' . htmlspecialchars($p['summary'] ?? '') . '</description>' . "\n";
            echo '  </item>' . "\n";
        }

        echo '</channel>' . "\n";
        echo '</rss>';
        exit;
    }

    // RSS 2.0 Articles Feed
    public function articlesXml(Request $request): void {
        header('Content-Type: application/xml; charset=utf-8');
        $articles = Database::query("SELECT a.*, c.name as category_name, u.display_name as author_name FROM articles a LEFT JOIN categories c ON a.category_id = c.id LEFT JOIN users u ON a.author_id = u.id WHERE a.status = 'published' ORDER BY a.created_at DESC LIMIT 50");
        $baseUrl = rtrim(Config::url('/'), '/');

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
        echo '<channel>' . "\n";
        echo '  <title>TechScout — Latest Engineering Articles &amp; Insights</title>' . "\n";
        echo '  <link>' . $baseUrl . '/blog</link>' . "\n";
        echo '  <description>Deep-dive analysis on AI, cloud infrastructure, system design, and emerging tech.</description>' . "\n";
        echo '  <language>en-us</language>' . "\n";
        echo '  <atom:link href="' . $baseUrl . '/feed/articles.xml" rel="self" type="application/rss+xml" />' . "\n";

        foreach ($articles as $a) {
            $link = $baseUrl . '/article/' . $a['slug'];
            $pubDate = date(DATE_RSS, strtotime($a['published_at'] ?? $a['created_at'] ?? 'now'));
            echo '  <item>' . "\n";
            echo '    <title>' . htmlspecialchars($a['title']) . '</title>' . "\n";
            echo '    <link>' . $link . '</link>' . "\n";
            echo '    <guid>' . $link . '</guid>' . "\n";
            echo '    <pubDate>' . $pubDate . '</pubDate>' . "\n";
            echo '    <author>' . htmlspecialchars($a['author_name'] ?? 'TechScout') . '</author>' . "\n";
            echo '    <category>' . htmlspecialchars($a['category_name'] ?? 'General') . '</category>' . "\n";
            echo '    <description>' . htmlspecialchars($a['summary'] ?? '') . '</description>' . "\n";
            echo '  </item>' . "\n";
        }

        echo '</channel>' . "\n";
        echo '</rss>';
        exit;
    }

    // JSON Feed Specification v1.1
    public function articlesJson(Request $request): void {
        header('Content-Type: application/feed+json; charset=utf-8');
        $articles = Database::query("SELECT a.*, c.name as category_name, u.display_name as author_name FROM articles a LEFT JOIN categories c ON a.category_id = c.id LEFT JOIN users u ON a.author_id = u.id WHERE a.status = 'published' ORDER BY a.created_at DESC LIMIT 50");
        $baseUrl = rtrim(Config::url('/'), '/');

        $items = [];
        foreach ($articles as $a) {
            $items[] = [
                'id' => $baseUrl . '/article/' . $a['slug'],
                'url' => $baseUrl . '/article/' . $a['slug'],
                'title' => $a['title'],
                'summary' => $a['summary'] ?? '',
                'date_published' => date('c', strtotime($a['published_at'] ?? $a['created_at'] ?? 'now')),
                'author' => [
                    'name' => $a['author_name'] ?? 'TechScout Editorial'
                ],
                'tags' => [$a['category_name'] ?? 'Tech']
            ];
        }

        echo json_encode([
            'version' => 'https://jsonfeed.org/version/1.1',
            'title' => 'TechScout Intelligence Feed',
            'home_page_url' => $baseUrl,
            'feed_url' => $baseUrl . '/feed/articles.json',
            'description' => 'The frontier of modern technology scouted for developers.',
            'items' => $items
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    // XML Sitemap Index [TS-API-07]
    public function sitemapIndex(Request $request): void {
        header('Content-Type: application/xml; charset=utf-8');
        $baseUrl = rtrim(Config::url('/'), '/');
        $now = date('Y-m-d');

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        echo "  <sitemap><loc>{$baseUrl}/sitemap-articles.xml</loc><lastmod>{$now}</lastmod></sitemap>\n";
        echo "  <sitemap><loc>{$baseUrl}/sitemap-tools.xml</loc><lastmod>{$now}</lastmod></sitemap>\n";
        echo "  <sitemap><loc>{$baseUrl}/sitemap-prompts.xml</loc><lastmod>{$now}</lastmod></sitemap>\n";
        echo '</sitemapindex>';
        exit;
    }

    public function sitemapArticles(Request $request): void {
        header('Content-Type: application/xml; charset=utf-8');
        $articles = Database::query("SELECT slug, updated_at, created_at FROM articles WHERE status = 'published' ORDER BY id DESC LIMIT 5000");
        $baseUrl = rtrim(Config::url('/'), '/');

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($articles as $a) {
            $date = date('Y-m-d', strtotime($a['updated_at'] ?? $a['created_at'] ?? 'now'));
            echo "  <url><loc>{$baseUrl}/article/{$a['slug']}</loc><lastmod>{$date}</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>\n";
        }
        echo '</urlset>';
        exit;
    }

    public function sitemapTools(Request $request): void {
        header('Content-Type: application/xml; charset=utf-8');
        $tools = Database::query("SELECT slug, updated_at, created_at FROM ai_tools WHERE status = 'published' ORDER BY id DESC LIMIT 5000");
        $baseUrl = rtrim(Config::url('/'), '/');

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($tools as $t) {
            $date = date('Y-m-d', strtotime($t['updated_at'] ?? $t['created_at'] ?? 'now'));
            echo "  <url><loc>{$baseUrl}/tools/{$t['slug']}</loc><lastmod>{$date}</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>\n";
        }
        echo '</urlset>';
        exit;
    }

    public function sitemapPrompts(Request $request): void {
        header('Content-Type: application/xml; charset=utf-8');
        $prompts = Database::query("SELECT slug, updated_at, created_at FROM prompts WHERE status = 'published' ORDER BY id DESC LIMIT 5000");
        $baseUrl = rtrim(Config::url('/'), '/');

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($prompts as $p) {
            $date = date('Y-m-d', strtotime($p['updated_at'] ?? $p['created_at'] ?? 'now'));
            echo "  <url><loc>{$baseUrl}/prompts/{$p['slug']}</loc><lastmod>{$date}</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>\n";
        }
        echo '</urlset>';
        exit;
    }
}
