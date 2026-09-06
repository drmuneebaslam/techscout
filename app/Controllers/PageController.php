<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;

class PageController {

    // ========================================================================
    // 1. LEGAL & COMPLIANCE HUB
    // ========================================================================
    public function legal(Request $request): void {
        Response::view('pages/legal', [
            'title' => 'Legal & Compliance Center | TechScout Policies',
            'updatedAt' => 'August 2026'
        ]);
    }

    public function privacy(Request $request): void {
        Response::view('pages/privacy', [
            'title' => 'Privacy Policy & Data Protection | TechScout',
            'updatedAt' => 'August 2026'
        ]);
    }

    public function terms(Request $request): void {
        Response::view('pages/terms', [
            'title' => 'Terms of Service & Usage Agreement | TechScout',
            'updatedAt' => 'August 2026'
        ]);
    }

    public function cookies(Request $request): void {
        Response::view('pages/cookies', [
            'title' => 'Cookie & Tracking Technologies Policy | TechScout',
            'updatedAt' => 'August 2026'
        ]);
    }

    public function communityGuidelines(Request $request): void {
        Response::view('pages/community_guidelines', [
            'title' => 'Community Guidelines & Code of Conduct | TechScout',
            'updatedAt' => 'August 2026'
        ]);
    }

    public function copyright(Request $request): void {
        Response::view('pages/copyright', [
            'title' => 'DMCA & Copyright Policy | TechScout',
            'updatedAt' => 'August 2026'
        ]);
    }

    public function accessibility(Request $request): void {
        Response::view('pages/accessibility', [
            'title' => 'Accessibility Statement (WCAG 2.1 AA) | TechScout',
            'updatedAt' => 'August 2026'
        ]);
    }

    public function acceptableUse(Request $request): void {
        Response::view('pages/acceptable_use', [
            'title' => 'Acceptable Use Policy | TechScout',
            'updatedAt' => 'August 2026'
        ]);
    }

    public function securityPolicy(Request $request): void {
        Response::view('pages/security_policy', [
            'title' => 'Security Policy & Vulnerability Disclosure | TechScout',
            'updatedAt' => 'August 2026'
        ]);
    }

    public function disclosure(Request $request): void {
        Response::view('pages/disclosure', [
            'title' => 'Affiliate & Sponsored Content Disclosure | TechScout',
            'updatedAt' => 'August 2026'
        ]);
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
