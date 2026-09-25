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
        $chunkSize = 5000;

        $db = Database::getConnection();

        // Calculate partition counts for each major entity type
        $toolsCount = (int)($db->query("SELECT COUNT(*) FROM contents WHERE content_type = 'tool' AND status = 'published'")->fetchColumn() ?: 0);
        $promptsCount = (int)($db->query("SELECT COUNT(*) FROM contents WHERE content_type = 'prompt' AND status = 'published'")->fetchColumn() ?: 0);
        $gptsCount = (int)($db->query("SELECT COUNT(*) FROM contents WHERE content_type = 'custom_gpt' AND status = 'published'")->fetchColumn() ?: 0);
        $articlesCount = (int)($db->query("SELECT COUNT(*) FROM contents WHERE content_type = 'article' AND status = 'published'")->fetchColumn() ?: 0);

        $toolPartitions = max(1, (int)ceil($toolsCount / $chunkSize));
        $promptPartitions = max(1, (int)ceil($promptsCount / $chunkSize));
        $gptPartitions = max(1, (int)ceil($gptsCount / $chunkSize));
        $articlePartitions = max(1, (int)ceil($articlesCount / $chunkSize));

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Static routes sitemap
        echo "  <sitemap><loc>{$baseUrl}/sitemap-static.xml</loc><lastmod>{$now}</lastmod></sitemap>\n";
        echo "  <sitemap><loc>{$baseUrl}/sitemap-categories.xml</loc><lastmod>{$now}</lastmod></sitemap>\n";

        // Articles partitions
        for ($i = 1; $i <= $articlePartitions; $i++) {
            echo "  <sitemap><loc>{$baseUrl}/sitemap-articles-{$i}.xml</loc><lastmod>{$now}</lastmod></sitemap>\n";
        }

        // Tools partitions
        for ($i = 1; $i <= $toolPartitions; $i++) {
            echo "  <sitemap><loc>{$baseUrl}/sitemap-tools-{$i}.xml</loc><lastmod>{$now}</lastmod></sitemap>\n";
        }

        // Prompts partitions
        for ($i = 1; $i <= $promptPartitions; $i++) {
            echo "  <sitemap><loc>{$baseUrl}/sitemap-prompts-{$i}.xml</loc><lastmod>{$now}</lastmod></sitemap>\n";
        }

        // Custom GPTs partitions
        for ($i = 1; $i <= $gptPartitions; $i++) {
            echo "  <sitemap><loc>{$baseUrl}/sitemap-gpts-{$i}.xml</loc><lastmod>{$now}</lastmod></sitemap>\n";
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
     * Dynamic Partitioned Sitemap Dispatcher
     * Supports:
     * - /sitemap-articles-{page}.xml
     * - /sitemap-tools-{page}.xml
     * - /sitemap-prompts-{page}.xml
     * - /sitemap-gpts-{page}.xml
     * - /sitemap-articles.xml (defaults to page 1)
     * - /sitemap-tools.xml (defaults to page 1)
     * - /sitemap-prompts.xml (defaults to page 1)
     * - /sitemap-gpts.xml (defaults to page 1)
     */
    public function sitemapPartition(Request $request, array $params): void {
        header('Content-Type: application/xml; charset=utf-8');
        $type = strtolower(trim((string)($params['type'] ?? 'tools')));
        $page = max(1, (int)($params['page'] ?? 1));
        $chunkSize = 5000;
        $offset = ($page - 1) * $chunkSize;

        $contentTypeMap = [
            'tools' => 'tool',
            'prompts' => 'prompt',
            'gpts' => 'custom_gpt',
            'articles' => 'article'
        ];

        $routePrefixMap = [
            'tools' => '/tools/',
            'prompts' => '/prompts/',
            'gpts' => '/gpts/',
            'articles' => '/article/'
        ];

        if (!isset($contentTypeMap[$type])) {
            http_response_code(404);
            echo '<?xml version="1.0" encoding="UTF-8"?><error>Invalid sitemap type</error>';
            exit;
        }

        $contentType = $contentTypeMap[$type];
        $prefix = $routePrefixMap[$type];
        $baseUrl = rtrim(Config::url('/'), '/');

        $db = Database::getConnection();
        // MISTAKE-35: Explicit integer casting in LIMIT/OFFSET
        $stmt = $db->prepare("
            SELECT slug, updated_at, created_at 
            FROM contents 
            WHERE content_type = :ctype AND status = 'published'
            ORDER BY id ASC
            LIMIT " . (int)$offset . ", " . (int)$chunkSize . "
        ");
        $stmt->execute([':ctype' => $contentType]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($rows as $row) {
            $date = date('Y-m-d', strtotime($row['updated_at'] ?? $row['created_at'] ?? 'now'));
            $loc = $baseUrl . $prefix . $row['slug'];
            echo "  <url><loc>{$loc}</loc><lastmod>{$date}</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>\n";
        }

        echo '</urlset>';
        exit;
    }

    public function sitemapArticles(Request $request): void {
        $this->sitemapPartition($request, ['type' => 'articles', 'page' => 1]);
    }

    public function sitemapTools(Request $request): void {
        $this->sitemapPartition($request, ['type' => 'tools', 'page' => 1]);
    }

    public function sitemapPrompts(Request $request): void {
        $this->sitemapPartition($request, ['type' => 'prompts', 'page' => 1]);
    }

    public function sitemapGpts(Request $request): void {
        $this->sitemapPartition($request, ['type' => 'gpts', 'page' => 1]);
    }
}

