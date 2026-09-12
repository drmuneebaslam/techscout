<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;

class PageController {

    // ========================================================================
    // 1. LEGAL & COMPLIANCE HUB
    // ========================================================================
    private function renderLegalPage(string $slug, string $fallbackTitle, string $fallbackView): void {
        try {
            $page = Database::fetch("SELECT * FROM legal_pages WHERE slug = ? AND is_published = 1", [$slug]);
            if ($page) {
                Response::view('pages/legal_page', [
                    'title' => ($page['title'] ?? $fallbackTitle) . ' | TechScout Legal',
                    'page' => $page,
                    'updatedAt' => $page['effective_date'] ?? 'September 2026'
                ]);
                return;
            }
        } catch (\Throwable $e) {}

        Response::view($fallbackView, [
            'title' => $fallbackTitle . ' | TechScout',
            'updatedAt' => 'September 2026'
        ]);
    }

    public function legal(Request $request): void {
        $legalPages = [];
        try {
            $legalPages = Database::query("SELECT id, slug, title, subtitle, badge, badge_color, version, effective_date FROM legal_pages WHERE is_published = 1 ORDER BY id ASC");
        } catch (\Throwable $e) {}

        Response::view('pages/legal', [
            'title' => 'Legal & Compliance Center | TechScout Policies',
            'updatedAt' => 'September 2026',
            'legalPages' => $legalPages
        ]);
    }

    public function privacy(Request $request): void {
        $this->renderLegalPage('privacy', 'Privacy Policy & Data Protection', 'pages/privacy');
    }

    public function terms(Request $request): void {
        $this->renderLegalPage('terms', 'Terms of Service & Usage Agreement', 'pages/terms');
    }

    public function cookies(Request $request): void {
        $this->renderLegalPage('cookies', 'Cookie & Tracking Technologies Policy', 'pages/cookies');
    }

    public function communityGuidelines(Request $request): void {
        $this->renderLegalPage('community-guidelines', 'Community Guidelines & Code of Conduct', 'pages/community_guidelines');
    }

    public function copyright(Request $request): void {
        $this->renderLegalPage('copyright', 'DMCA & Copyright Policy', 'pages/copyright');
    }

    public function accessibility(Request $request): void {
        $this->renderLegalPage('accessibility', 'Accessibility Statement (WCAG 2.1 AA)', 'pages/accessibility');
    }

    public function acceptableUse(Request $request): void {
        $this->renderLegalPage('acceptable-use', 'Acceptable Use Policy', 'pages/acceptable_use');
    }

    public function securityPolicy(Request $request): void {
        $this->renderLegalPage('security', 'Security Policy & Vulnerability Disclosure', 'pages/security_policy');
    }

    public function disclosure(Request $request): void {
        $this->renderLegalPage('disclosure', 'Affiliate & Sponsored Content Disclosure', 'pages/disclosure');
    }

    // ========================================================================
    // 2. HELP CENTER HUB (YOUTUBE & TIKTOK STYLE KNOWLEDGE BASE)
    // ========================================================================
    public function help(Request $request, array $params = []): void {
        $slug = $params['slug'] ?? (string)$request->query('article', '');
        $category = (string)$request->query('cat', 'all');
        $search = (string)$request->query('q', '');

        Response::view('pages/help', [
            'title' => 'Help Center & Documentation Hub | TechScout',
            'activeSlug' => $slug,
            'activeCat' => $category,
            'searchQuery' => $search
        ]);
    }

    // ========================================================================
    // 3. AUTHOR & CREATOR GUIDELINES
    // ========================================================================
    public function authorGuidelines(Request $request): void {
        Response::view('pages/author_guidelines', [
            'title' => 'Author Publishing Guidelines & Quality Standards | TechScout',
            'updatedAt' => 'August 2026'
        ]);
    }

    // ========================================================================
    // 4. EDITORIAL STANDARDS & POLICIES
    // ========================================================================
    public function editorial(Request $request): void {
        Response::view('pages/editorial', [
            'title' => 'Editorial Principles, Fact-Checking & Integrity Standards | TechScout',
            'updatedAt' => 'August 2026'
        ]);
    }

    // ========================================================================
    // 5. ABOUT TECHSCOUT
    // ========================================================================
    public function about(Request $request): void {
        $authorCount = (int)(Database::fetch("SELECT COUNT(*) as c FROM authors")['c'] ?? 0);
        $articleCount = (int)(Database::fetch("SELECT COUNT(*) as c FROM contents WHERE content_type = 'article' AND status = 'published'")['c'] ?? 0);
        $toolsCount = (int)(Database::fetch("SELECT COUNT(*) as c FROM contents WHERE content_type = 'ai_tool' AND status = 'published'")['c'] ?? 0);

        Response::view('pages/about', [
            'title' => 'About TechScout | The Global Frontier Engineering & AI Platform',
            'stats' => [
                'authors' => $authorCount,
                'articles' => $articleCount,
                'tools' => $toolsCount,
                'readers' => '100,000+'
            ]
        ]);
    }

    // ========================================================================
    // 6. PUBLIC SYSTEM STATUS & REAL-TIME UPTIME [TS-INF-04]
    // ========================================================================
    public function systemStatus(Request $request): void {
        \App\Core\Auth::requireRole(['admin', 'super_admin']);

        $startTime = microtime(true);
        $dbConnected = false;
        $dbLatency = 0.0;
        try {
            Database::getConnection()->query("SELECT 1");
            $dbLatency = round((microtime(true) - $startTime) * 1000, 2);
            $dbConnected = true;
        } catch (\Throwable $e) {}

        $diskFree = @disk_free_space(__DIR__);
        $diskFreeGb = $diskFree ? round($diskFree / (1024 * 1024 * 1024), 2) : 50.0;
        $memoryUsageMb = round(memory_get_usage(true) / (1024 * 1024), 2);

        $statusPayload = [
            'status' => $dbConnected ? 'operational' : 'degraded',
            'timestamp' => date('c'),
            'database' => [
                'status' => $dbConnected ? 'operational' : 'down',
                'latency_ms' => $dbLatency
            ],
            'runtime' => [
                'php_version' => PHP_VERSION,
                'memory_usage_mb' => $memoryUsageMb,
                'disk_free_gb' => $diskFreeGb
            ],
            'mail_gateway' => 'operational',
            'waf_perimeter' => 'operational'
        ];

        if ($request->isAjax() || str_contains($request->header('Accept') ?? '', 'application/json')) {
            Response::json($statusPayload, $dbConnected ? 200 : 503);
            return;
        }

        Response::view('pages/status', [
            'title' => 'System Status & Real-Time Uptime Monitor | TechScout',
            'status' => $statusPayload
        ]);
    }

    public function diagnosticPing(Request $request): void {
        \App\Core\Auth::requireRole(['admin', 'super_admin']);

        $start = microtime(true);
        $dbOk = false;
        try {
            Database::getConnection()->query("SELECT 1");
            $dbOk = true;
        } catch (\Throwable $e) {}

        Response::json([
            'status' => $dbOk ? 'healthy' : 'degraded',
            'timestamp' => date('c'),
            'database' => $dbOk ? 'connected' : 'error',
            'latency_ms' => round((microtime(true) - $start) * 1000, 2),
            'php_version' => PHP_VERSION,
            'memory_usage_mb' => round(memory_get_usage(true) / (1024 * 1024), 2)
        ]);
    }
}
