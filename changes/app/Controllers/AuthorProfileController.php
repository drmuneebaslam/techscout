<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;
use App\Models\Author;
use App\Models\Content;

class AuthorProfileController {
    public function show(Request $request, array $params): void {
        $slug = trim((string)($params['slug'] ?? ''));
        if (in_array(strtolower($slug), ['apply', 'dashboard', 'editor', 'analytics', 'rewards', 'payouts', 'save'], true)) {
            Response::redirect('/author/' . strtolower($slug));
            return;
        }

        $author = Author::findBySlug($slug);

        if (!$author) {
            // Fallback: try matching username or author ID
            $author = Database::fetch("SELECT a.*, u.email, u.username, u.role FROM authors a JOIN users u ON a.user_id = u.id WHERE u.username = ? OR a.id = ?", [$slug, (int)$slug]);
        }

        if (!$author) {
            // Check if standard member profile exists
            $member = Database::fetch("SELECT u.*, a.display_name, a.headline, a.bio, a.avatar_url, a.verification_badge, a.custom_badge_label, a.custom_badge_color FROM users u LEFT JOIN authors a ON u.id = a.user_id WHERE u.username = ? OR u.id = ?", [$slug, (int)$slug]);
            if ($member) {
                $comments = Database::query("
                    SELECT c.*, cont.title as content_title, cont.slug as content_slug 
                    FROM comments c 
                    JOIN contents cont ON c.content_id = cont.id 
                    WHERE c.user_id = ? 
                    ORDER BY c.created_at DESC LIMIT 20
                ", [$member['id']]);

                Response::view('public/member_profile', [
                    'title' => ($member['display_name'] ?? $member['username']) . ' — Community Member | TechScout',
                    'member' => $member,
                    'comments' => $comments
                ]);
                return;
            }

            http_response_code(404);
            Response::view('errors/404', ['title' => 'User Profile Not Found | TechScout']);
            return;
        }

        $authorId = (int)$author['id'];

        // Published Articles
        $articles = Database::query("SELECT c.*, cat.name as category_name, cat.slug as category_slug, art.reading_time_mins 
                                    FROM contents c 
                                    LEFT JOIN categories cat ON c.category_id = cat.id 
                                    LEFT JOIN articles art ON c.id = art.content_id 
                                    WHERE c.author_id = ? AND c.content_type = 'article' AND c.status = 'published' 
                                    ORDER BY c.published_at DESC", [$authorId]);

        // Published Custom GPTs
        $gpts = Database::query("SELECT c.*, gpt.model_base, gpt.openai_url 
                                FROM contents c 
                                LEFT JOIN gpt_listings gpt ON c.id = gpt.content_id 
                                WHERE c.author_id = ? AND c.content_type = 'gpt_listing' AND c.status = 'published' 
                                ORDER BY c.published_at DESC", [$authorId]);

        // Published Prompts
        $prompts = Database::query("SELECT c.*, p.copy_count, p.template_variables 
                                   FROM contents c 
                                   LEFT JOIN prompts p ON c.id = p.content_id 
                                   WHERE c.author_id = ? AND c.content_type = 'prompt' AND c.status = 'published' 
                                   ORDER BY c.published_at DESC", [$authorId]);

        // Published Tools
        $tools = Database::query("SELECT c.*, tool.pricing_model, tool.rating_avg, tool.website_url 
                                 FROM contents c 
                                 LEFT JOIN ai_tools tool ON c.id = tool.content_id 
                                 WHERE c.author_id = ? AND c.content_type = 'ai_tool' AND c.status = 'published' 
                                 ORDER BY c.published_at DESC", [$authorId]);

        // Aggregate statistics
        $stats = Database::fetch("SELECT COUNT(id) as total_published, 
                                         COALESCE(SUM(view_count), 0) as total_views, 
                                         COALESCE(SUM(reaction_count), 0) as total_reactions, 
                                         COALESCE(SUM(comment_count), 0) as total_comments 
                                  FROM contents 
                                  WHERE author_id = ? AND status = 'published'", [$authorId]);

        // Reflect boosted author total_views if higher than content sum
        $authorTotalViews = (int)($author['total_views'] ?? 0);
        if ($authorTotalViews > (int)($stats['total_views'] ?? 0)) {
            $stats['total_views'] = $authorTotalViews;
        }

        $activeTab = $request->query('tab', 'articles');

        $isFollowing = false;
        if (\App\Core\Auth::check()) {
            $isFollowing = (bool)Database::fetch("SELECT id FROM author_followers WHERE author_id = ? AND user_id = ?", [$authorId, \App\Core\Auth::id()]);
        }

        $isAdmin = \App\Core\Auth::isAdmin();

        // Followers & Following lists are strictly restricted to Administrators
        $followers = [];
        $following = [];

        if ($isAdmin) {
            $followers = Database::query("
                SELECT af.created_at as followed_at, u.id as user_id, u.username, COALESCE(a.display_name, u.username) as display_name, a.avatar_url,
                       a.id as author_id, a.slug as author_slug, a.headline, a.verification_badge, a.reputation_score
                FROM author_followers af
                JOIN users u ON af.user_id = u.id
                LEFT JOIN authors a ON a.user_id = u.id
                WHERE af.author_id = ?
                ORDER BY af.created_at DESC
                LIMIT 60
            ", [$authorId]);

            $following = Database::query("
                SELECT af.created_at as followed_at, a.id as author_id, a.display_name, a.slug as author_slug, a.avatar_url, a.headline, a.verification_badge, a.reputation_score,
                       a.follower_count as target_follower_count
                FROM author_followers af
                JOIN authors a ON af.author_id = a.id
                WHERE af.user_id = ?
                ORDER BY af.created_at DESC
                LIMIT 60
            ", [(int)$author['user_id']]);
        }

        // Calculate verified follower count from authors.follower_count + real followers
        $authorFollowerCount = (int)($author['follower_count'] ?? 0);
        $dbFollowers = (int)(Database::fetch("SELECT COUNT(*) as c FROM author_followers WHERE author_id = ?", [$authorId])['c'] ?? 0);
        $followerCount = max($authorFollowerCount, $dbFollowers);
        $followingCount = (int)(Database::fetch("SELECT COUNT(*) as c FROM author_followers WHERE user_id = ?", [(int)$author['user_id']])['c'] ?? 0);

        // Extract author's configured crypto tip wallets (95% creator share / 5% platform fee)
        $authorSocials = [];
        if (!empty($author['social_links'])) {
            $authorSocials = json_decode($author['social_links'], true) ?: [];
        }
        $tipWallets = [
            'USDT' => $authorSocials['tip_wallet_usdt'] ?? '0x71C8A60e46b38cE1241F8308D649372F7505a76E',
            'SOL' => $authorSocials['tip_wallet_sol'] ?? 'So11111111111111111111111111111111111111112',
            'ETH' => $authorSocials['tip_wallet_eth'] ?? '0x71C8A60e46b38cE1241F8308D649372F7505a76E',
            'BTC' => $authorSocials['tip_wallet_btc'] ?? 'bc1q9v80n73qg3cm94n59tcqv04q6vt6vg00508h7e'
        ];

        Response::view('public/author_profile', [
            'title' => ($author['display_name'] ?? $author['username']) . ' — Author & Contributor Profile | TechScout',
            'author' => $author,
            'articles' => $articles,
            'gpts' => $gpts,
            'prompts' => $prompts,
            'tools' => $tools,
            'stats' => $stats,
            'activeTab' => $activeTab,
            'isFollowing' => $isFollowing,
            'followerCount' => $followerCount,
            'followingCount' => $followingCount,
            'followers' => $followers,
            'following' => $following,
            'isAdmin' => $isAdmin,
            'tipWallets' => $tipWallets
        ]);
    }
}
