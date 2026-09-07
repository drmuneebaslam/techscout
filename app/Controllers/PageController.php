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
}
