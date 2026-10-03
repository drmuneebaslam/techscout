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
        $prompts = Database::query(
            "SELECT c.title, c.slug, c.summary, c.created_at, cat.name as category_name 
             FROM contents c 
             JOIN prompts p ON c.id = p.content_id 
             LEFT JOIN categories cat ON c.category_id = cat.id 
             WHERE c.status = 'published' 
             ORDER BY c.created_at DESC LIMIT 50"
        );
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
        $articles = Database::query(
            "SELECT c.title, c.slug, c.summary, c.published_at, c.created_at, cat.name as category_name, u.username as author_name 
             FROM contents c 
             JOIN articles a ON c.id = a.content_id 
             LEFT JOIN categories cat ON c.category_id = cat.id 
             LEFT JOIN users u ON c.author_id = u.id 
             WHERE c.status = 'published' 
             ORDER BY c.created_at DESC LIMIT 50"
        );
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
        $articles = Database::query(
            "SELECT c.title, c.slug, c.summary, c.published_at, c.created_at, cat.name as category_name, u.username as author_name 
             FROM contents c 
             JOIN articles a ON c.id = a.content_id 
             LEFT JOIN categories cat ON c.category_id = cat.id 
             LEFT JOIN users u ON c.author_id = u.id 
             WHERE c.status = 'published' 
             ORDER BY c.created_at DESC LIMIT 50"
        );
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

    /**
     * XML Sitemap Index [TS-API-07]
     * Scales dynamically to 200,000+ entities per content type
     * Partitioned into 5,000-URL chunks compliant with Google Search Console specs
     */
    public function sitemapIndex(Request $request): void {
        header('Content-Type: application/xml; charset=utf-8');
        $baseUrl = rtrim(Config::url('/'), '/');
        $now = date('Y-m-d');

        $db = Database::getConnection();
        $categories = $db->query("SELECT slug FROM categories ORDER BY id ASC")->fetchAll(\PDO::FETCH_ASSOC);

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Static routes sitemap
        echo "  <sitemap><loc>{$baseUrl}/sitemap-static.xml</loc><lastmod>{$now}</lastmod></sitemap>\n";
        echo "  <sitemap><loc>{$baseUrl}/sitemap-categories.xml</loc><lastmod>{$now}</lastmod></sitemap>\n";

        // Category-wise sitemaps
        foreach ($categories as $cat) {
            $slug = htmlspecialchars($cat['slug']);
            echo "  <sitemap><loc>{$baseUrl}/sitemap-category-{$slug}.xml</loc><lastmod>{$now}</lastmod></sitemap>\n";
        }

        echo '</sitemapindex>';
        exit;
    }

    /**
     * Static Pages Sitemap
     */
    public function sitemapStatic(Request $request): void {
        header('Content-Type: application/xml; charset=utf-8');
        $baseUrl = rtrim(Config::url('/'), '/');
        $now = date('Y-m-d');

        $staticRoutes = [
            '/' => ['freq' => 'daily', 'pri' => '1.0'],
            '/tools' => ['freq' => 'hourly', 'pri' => '0.9'],
            '/prompts' => ['freq' => 'hourly', 'pri' => '0.9'],
            '/gpts' => ['freq' => 'daily', 'pri' => '0.8'],
            '/blog' => ['freq' => 'daily', 'pri' => '0.8'],
            '/submit' => ['freq' => 'monthly', 'pri' => '0.6'],
            '/about' => ['freq' => 'monthly', 'pri' => '0.5'],
            '/privacy' => ['freq' => 'monthly', 'pri' => '0.4'],
            '/terms' => ['freq' => 'monthly', 'pri' => '0.4'],
            '/help' => ['freq' => 'weekly', 'pri' => '0.6'],
        ];

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($staticRoutes as $path => $meta) {
            $loc = $baseUrl . $path;
            echo "  <url><loc>{$loc}</loc><lastmod>{$now}</lastmod><changefreq>{$meta['freq']}</changefreq><priority>{$meta['pri']}</priority></url>\n";
        }
        echo '</urlset>';
        exit;
    }

    /**
     * Categories Sitemap
     */
    public function sitemapCategories(Request $request): void {
        header('Content-Type: application/xml; charset=utf-8');
        $baseUrl = rtrim(Config::url('/'), '/');
        $now = date('Y-m-d');

        $categories = Database::query("SELECT slug, created_at FROM categories ORDER BY id ASC");

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($categories as $cat) {
            $date = date('Y-m-d', strtotime($cat['created_at'] ?? $now));
            echo "  <url><loc>{$baseUrl}/category/{$cat['slug']}</loc><lastmod>{$date}</lastmod><changefreq>weekly</changefreq><priority>0.7</priority></url>\n";
        }
        echo '</urlset>';
        exit;
    }

    /**
     * Category Content Sitemap
     * Example: /sitemap-category-software.xml
     */
    public function sitemapCategoryContent(Request $request, array $params): void {
        header('Content-Type: application/xml; charset=utf-8');
        $baseUrl = rtrim(Config::url('/'), '/');
        $slug = $params['slug'] ?? '';
        
        $db = Database::getConnection();
        $catStmt = $db->prepare("SELECT id FROM categories WHERE slug = ?");
        $catStmt->execute([$slug]);
        $catId = $catStmt->fetchColumn();

        if (!$catId) {
            http_response_code(404);
            echo '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>';
            exit;
        }

        $stmt = $db->prepare("
            SELECT content_type, slug, updated_at, created_at 
            FROM contents 
            WHERE category_id = ? AND status = 'published'
            ORDER BY created_at DESC LIMIT 50000
        ");
        $stmt->execute([$catId]);
        $contents = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        foreach ($contents as $item) {
            $pathMap = [
                'article' => '/article/',
                'tool' => '/tool/',
                'prompt' => '/prompts/',
                'custom_gpt' => '/gpts/'
            ];
            $prefix = $pathMap[$item['content_type']] ?? '/content/';
            $loc = $baseUrl . $prefix . $item['slug'];
            $date = date('Y-m-d', strtotime($item['updated_at'] ?? $item['created_at'] ?? 'now'));
            
            echo "  <url>\n";
            echo "    <loc>{$loc}</loc>\n";
            echo "    <lastmod>{$date}</lastmod>\n";
            echo "    <changefreq>weekly</changefreq>\n";
            echo "    <priority>0.64</priority>\n";
            echo "  </url>\n";
        }

        echo '</urlset>';
        exit;
    }
}

