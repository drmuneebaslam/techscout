<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Auth;
use App\Core\Database;
use App\Core\Sanitizer;
use App\Models\Comment;
use App\Models\Content;
use App\Models\Analytics;

class ApiController {
    // 6-Reaction Toggle
    public function react(Request $request, array $params): void {
        if (!Auth::check()) {
            Response::json(['error' => 'Authentication required to react.'], 401);
        }

        $contentId = (int)($params['id'] ?? 0);
        $userId = (int)Auth::id();
        $reactionType = (string)($request->json('reaction_type') ?? $request->post('reaction_type') ?? $request->get('reaction_type') ?? 'like');

        $validReactions = ['like', 'clap', 'rocket', 'insightful', 'mind_blown', 'bookmark'];
        if (!in_array($reactionType, $validReactions, true)) {
            Response::json(['error' => 'Invalid reaction type.'], 400);
        }

        // Check for any existing reaction by this user on this content item
        $existing = Database::fetch("SELECT id, reaction_type FROM reactions WHERE user_id = ? AND content_id = ?", [
            $userId, $contentId
        ]);

        $action = 'added';
        $activeReaction = $reactionType;

        if ($existing) {
            if ($existing['reaction_type'] === $reactionType) {
                // User clicked the exact same reaction -> REMOVE (Un-react)
                Database::execute("DELETE FROM reactions WHERE id = ?", [$existing['id']]);
                Database::execute("UPDATE contents SET reaction_count = GREATEST(0, reaction_count - 1) WHERE id = ?", [$contentId]);
                $action = 'removed';
                $activeReaction = null;
            } else {
                // User clicked a different reaction -> SWITCH reaction type (count remains 1)
                Database::execute("UPDATE reactions SET reaction_type = ? WHERE id = ?", [$reactionType, $existing['id']]);
                $action = 'switched';
                $activeReaction = $reactionType;
            }
        } else {
            // First time reaction by user on this content
            Database::execute("INSERT INTO reactions (user_id, content_id, reaction_type) VALUES (?, ?, ?)", [
                $userId, $contentId, $reactionType
            ]);
            Database::execute("UPDATE contents SET reaction_count = reaction_count + 1 WHERE id = ?", [$contentId]);
            $action = 'added';
            $activeReaction = $reactionType;
        }

        $totalCount = (int)(Database::fetch("SELECT reaction_count FROM contents WHERE id = ?", [$contentId])['reaction_count'] ?? 0);
        
        Response::json([
            'success' => true,
            'action' => $action,
            'active_reaction' => $activeReaction,
            'reaction_type' => $reactionType,
            'previous_reaction' => $existing['reaction_type'] ?? null,
            'total_reactions' => $totalCount
        ]);
    }

    // Post Comment
    public function addComment(Request $request, array $params): void {
        if (!Auth::check()) {
            Response::json(['error' => 'Sign in required to comment.'], 401);
        }

        $contentId = (int)($params['id'] ?? 0);
        $userId = (int)Auth::id();
        $text = mb_substr(Sanitizer::clean((string)$request->input('comment_text', '')), 0, 1000);
        $parentId = $request->input('parent_id') ? (int)$request->input('parent_id') : null;

        if (strlen($text) < 3) {
            Response::json(['error' => 'Comment must be at least 3 characters long.'], 400);
            return;
        }

        // TS-SOC-03: Strict rule: No links and no email addresses allowed in comments
        if (preg_match('/(https?:\/\/|www\.[a-z0-9\-\.]+|[a-z0-9_\.\-]+@[a-z0-9\-]+\.[a-z0-9\-\.]+)/i', $text)) {
            Response::json([
                'error' => 'Links and email addresses are strictly prohibited in comments to prevent spam and ensure community safety.'
            ], 400);
            return;
        }

        // Suspicious Activity Check: Auto-flag accounts creating more than 10 comments per minute
        $recentComments = (int)(Database::fetch(
            "SELECT COUNT(*) as c FROM comments WHERE user_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 1 MINUTE)",
            [$userId]
        )['c'] ?? 0);

        if ($recentComments >= 10) {
            \App\Models\User::flagSuspicious($userId, 'Automated comment flood detected (>10 comments/min)');
            Response::json([
                'error' => 'Rate limit exceeded: Account flagged for high-frequency comment submissions (>10/min). Please wait a moment.'
            ], 429);
            return;
        }

        $newId = Comment::add($contentId, $userId, $text, $parentId);
        $comment = Database::fetch("SELECT c.*, u.username, a.display_name, a.avatar_url, a.verification_badge FROM comments c JOIN users u ON c.user_id = u.id LEFT JOIN authors a ON u.id = a.user_id WHERE c.id = ?", [$newId]);
        if (!empty($comment['avatar_url'])) {
            $comment['avatar_url'] = image_url($comment['avatar_url']);
        }

        // Dispatch Contextual Notifications
        $content = Database::fetch("SELECT c.*, a.user_id as author_user_id FROM contents c LEFT JOIN authors a ON c.author_id = a.id WHERE c.id = ?", [$contentId]);
        if ($content) {
            $contentUrl = content_url($content) . '#comment-' . $newId;
            $commentSnippet = mb_strimwidth($text, 0, 85, '...');
            $commenterName = $comment['display_name'] ?: $comment['username'];

            // 1. If replying to a parent comment -> notify parent comment owner
            if ($parentId > 0) {
                $parentComment = Database::fetch("SELECT * FROM comments WHERE id = ?", [$parentId]);
                if ($parentComment && (int)$parentComment['user_id'] !== $userId) {
                    \App\Core\NotificationHelper::send(
                        (int)$parentComment['user_id'],
                        'reply',
                        "New Reply from @{$comment['username']}",
                        "{$commenterName} replied to your comment on \"{$content['title']}\": \"{$commentSnippet}\"",
                        $contentUrl
                    );
                }
            }

            // 2. If commenting on author's content -> notify content author
            $authorUserId = (int)($content['author_user_id'] ?? 0);
            if ($authorUserId > 0 && $authorUserId !== $userId && (empty($parentId) || (isset($parentComment) && (int)$parentComment['user_id'] !== $authorUserId))) {
                \App\Core\NotificationHelper::send(
                    $authorUserId,
                    'comment',
                    "New Comment on \"{$content['title']}\"",
                    "{$commenterName} commented on your publication \"{$content['title']}\": \"{$commentSnippet}\"",
                    $contentUrl
                );
            }

            // 3. TS-SOC-01: Detect @username mentions and notify mentioned users
            preg_match_all('/@([a-zA-Z0-9_]{3,30})/', $text, $matches);
            if (!empty($matches[1])) {
                $mentionedUsernames = array_unique($matches[1]);
                foreach ($mentionedUsernames as $mUsername) {
                    $mUser = Database::fetch("SELECT id FROM users WHERE username = ? AND status = 'active'", [$mUsername]);
                    if ($mUser && (int)$mUser['id'] !== $userId) {
                        \App\Core\NotificationHelper::send(
                            (int)$mUser['id'],
                            'mention',
                            "Mentioned by @{$comment['username']}",
                            "{$commenterName} mentioned you in a discussion on \"{$content['title']}\": \"{$commentSnippet}\"",
                            $contentUrl
                        );
                    }
                }
            }
        }

        Response::json([
            'success' => true,
            'comment' => $comment
        ], 201);
    }

    // TS-SOC-06: Long Polling Endpoint for Real-time Thread Updates (60s cycle, zero spam)
    public function pollComments(Request $request, array $params): void {
        $contentId = (int)($params['id'] ?? 0);
        $sinceId = (int)$request->query('since_id', 0);

        $newComments = [];
        try {
            $newComments = Database::query(
                "SELECT c.*, u.username, a.display_name, a.avatar_url, a.verification_badge 
                 FROM comments c 
                 JOIN users u ON c.user_id = u.id 
                 LEFT JOIN authors a ON u.id = a.user_id 
                 WHERE c.content_id = ? AND c.id > ? AND c.status = 'approved' 
                 ORDER BY c.id ASC",
                [$contentId, $sinceId]
            );
            foreach ($newComments as &$nc) {
                if (!empty($nc['avatar_url'])) {
                    $nc['avatar_url'] = image_url($nc['avatar_url']);
                }
            }
        } catch (\Throwable $e) {}

        Response::json([
            'has_updates' => !empty($newComments),
            'count' => count($newComments),
            'comments' => $newComments,
            'poll_interval' => 60000 // 60s
        ]);
    }

    // TS-SOC-07: Pin "Best Answer" / Top Insight by Author or Admin
    public function togglePinComment(Request $request, array $params): void {
        if (!Auth::check()) {
            Response::json(['error' => 'Authentication required.'], 401);
            return;
        }

        $commentId = (int)($params['id'] ?? 0);
        $comment = Database::fetch("SELECT c.*, ct.author_id as content_author_id FROM comments c JOIN contents ct ON c.content_id = ct.id WHERE c.id = ?", [$commentId]);
        if (!$comment) {
            Response::json(['error' => 'Comment not found.'], 404);
            return;
        }

        $user = Auth::user();
        $isAuthor = (!empty($user['author_id']) && (int)$user['author_id'] === (int)$comment['content_author_id']);
        $isAdmin = Auth::isAdmin();

        if (!$isAuthor && !$isAdmin) {
            Response::json(['error' => 'Only the content author or an administrator can pin comments.'], 403);
            return;
        }

        $newPinned = empty($comment['is_pinned']) ? 1 : 0;
        Database::execute("UPDATE comments SET is_pinned = ? WHERE id = ?", [$newPinned, $commentId]);

        Response::json([
            'success' => true,
            'is_pinned' => (bool)$newPinned,
            'message' => $newPinned ? 'Comment pinned to top of thread!' : 'Comment unpinned.'
        ]);
    }

    // TS-SOC-04: Multi-Emoji Reaction Bar (❤️, 🚀, 💡, 🔥, 👀)
    public function reactCommentEmoji(Request $request, array $params): void {
        $commentId = (int)($params['id'] ?? 0);
        $emoji = trim((string)($request->json('emoji') ?? $request->post('emoji') ?? ''));
        $validEmojis = ['heart', 'rocket', 'lightbulb', 'fire', 'eyes'];

        if (!in_array($emoji, $validEmojis, true)) {
            Response::json(['error' => 'Invalid emoji reaction.'], 400);
            return;
        }

        $userId = Auth::id() ? (int)Auth::id() : null;
        $ip = Request::ip();

        // Stored in reactions_json on comments
        $comment = Database::fetch("SELECT reactions_json, reaction_count FROM comments WHERE id = ?", [$commentId]);
        if (!$comment) {
            Response::json(['error' => 'Comment not found.'], 404);
            return;
        }

        $reactions = !empty($comment['reactions_json']) ? json_decode($comment['reactions_json'], true) : [];
        if (!is_array($reactions)) $reactions = [];
        $reactions[$emoji] = (int)($reactions[$emoji] ?? 0) + 1;

        Database::execute(
            "UPDATE comments SET reactions_json = ?, reaction_count = reaction_count + 1 WHERE id = ?",
            [json_encode($reactions), $commentId]
        );

        Response::json([
            'success' => true,
            'reactions' => $reactions,
            'total_count' => (int)$comment['reaction_count'] + 1
        ]);
    }

    // TS-SOC-01: Mentions Autocomplete Search
    public function searchMentions(Request $request): void {
        $q = trim((string)$request->query('q', ''));
        if (strlen($q) < 1) {
            Response::json([]);
            return;
        }

        $users = Database::query(
            "SELECT u.id, u.username, a.display_name, a.avatar_url 
             FROM users u 
             LEFT JOIN authors a ON u.id = a.user_id 
             WHERE u.username LIKE ? OR a.display_name LIKE ? 
             LIMIT 8",
            [$q . '%', $q . '%']
        );
        foreach ($users as &$u) {
            $u['avatar_url'] = image_url($u['avatar_url'] ?? '');
        }

        Response::json($users);
    }

    // TS-CR-05: Creator Crypto Tip Jar Submission (Central Treasury, 5% fee)
    public function submitCreatorTip(Request $request): void {
        $authorUsername = trim((string)$request->post('author_username', ''));
        $articleUrl = trim((string)$request->post('article_url', ''));
        $grossAmount = (float)$request->post('amount', 10.00);
        $currency = strtoupper(trim((string)$request->post('currency', 'USDT')));
        $txid = trim((string)$request->post('txid', ''));
        $senderName = trim((string)$request->post('sender_name', 'Anonymous Supporter'));

        if ($grossAmount <= 0 || empty($txid)) {
            Response::json(['error' => 'Please provide a valid tip amount and payment TXID.'], 400);
            return;
        }

        // Find author ID
        $author = null;
        if (!empty($authorUsername)) {
            $author = Database::fetch("SELECT a.id, a.user_id FROM authors a JOIN users u ON a.user_id = u.id WHERE u.username = ? OR a.display_name = ?", [$authorUsername, $authorUsername]);
        }
        $authorId = $author ? (int)$author['id'] : 1;

        // Calculate 5% platform fee and 95% net creator amount
        $platformFee = round($grossAmount * 0.05, 2);
        $netAuthor = round($grossAmount * 0.95, 2);

        Database::execute(
            "INSERT INTO author_tips (author_id, sender_name, article_url, gross_amount, platform_fee, net_author_amount, currency, txid, status) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')",
            [$authorId, $senderName ?: 'Anonymous Supporter', $articleUrl, $grossAmount, $platformFee, $netAuthor, $currency, $txid]
        );

        Response::json([
            'success' => true,
            'message' => 'Thank you! Your tip has been submitted for platform confirmation and creator credit.'
        ]);
    }

    // TS-SOC-11: Community Poll Voting
    public function votePoll(Request $request, array $params): void {
        $pollId = (int)($params['id'] ?? 0);
        $optionId = (int)$request->post('option_id', 0);
        $ip = Request::ip();
        $userId = Auth::id() ? (int)Auth::id() : null;

        if ($pollId <= 0 || $optionId <= 0) {
            Response::json(['error' => 'Invalid poll or option.'], 400);
            return;
        }

        // Check if already voted
        $alreadyVoted = Database::fetch("SELECT id FROM article_poll_votes WHERE poll_id = ? AND ip_address = ?", [$pollId, $ip]);
        if ($alreadyVoted) {
            Response::json(['error' => 'You have already voted in this poll.'], 400);
            return;
        }

        Database::execute(
            "INSERT INTO article_poll_votes (poll_id, option_id, user_id, ip_address) VALUES (?, ?, ?, ?)",
            [$pollId, $optionId, $userId, $ip]
        );
        Database::execute("UPDATE article_poll_options SET votes_count = votes_count + 1 WHERE id = ?", [$optionId]);

        $options = Database::query("SELECT id, option_text, votes_count FROM article_poll_options WHERE poll_id = ?", [$pollId]);
        $totalVotes = array_sum(array_column($options, 'votes_count'));

        Response::json([
            'success' => true,
            'total_votes' => $totalVotes,
            'options' => $options
        ]);
    }

    // Upvote / Helpful Comment Toggle with Undo Support
    public function upvoteComment(Request $request, array $params): void {
        $commentId = (int)($params['id'] ?? 0);
        if ($commentId <= 0) {
            Response::json(['success' => false, 'error' => 'Invalid comment ID'], 400);
            return;
        }

        $userId = Auth::id() ? (int)Auth::id() : null;
        $ip = Request::ip();

        try {
            // Check if user or guest IP already reacted to this comment
            $existing = null;
            if ($userId) {
                $existing = Database::fetch("SELECT id FROM comment_reactions WHERE comment_id = ? AND user_id = ?", [$commentId, $userId]);
            } else {
                $existing = Database::fetch("SELECT id FROM comment_reactions WHERE comment_id = ? AND user_id IS NULL AND ip_address = ?", [$commentId, $ip]);
            }

            $action = 'added';
            $upvoted = true;

            if ($existing) {
                // Toggle OFF: Remove existing vote and decrement count
                Database::execute("DELETE FROM comment_reactions WHERE id = ?", [$existing['id']]);
                Database::execute("UPDATE comments SET reaction_count = GREATEST(0, reaction_count - 1) WHERE id = ?", [$commentId]);
                $action = 'removed';
                $upvoted = false;
            } else {
                // Toggle ON: Add vote and increment count
                Database::execute(
                    "INSERT INTO comment_reactions (comment_id, user_id, ip_address, reaction_type) VALUES (?, ?, ?, 'helpful')",
                    [$commentId, $userId, $ip]
                );
                Database::execute("UPDATE comments SET reaction_count = reaction_count + 1 WHERE id = ?", [$commentId]);
                $action = 'added';
                $upvoted = true;
            }

            $comment = Database::fetch("SELECT reaction_count FROM comments WHERE id = ?", [$commentId]);
            $count = (int)($comment['reaction_count'] ?? 0);

            Response::json([
                'success' => true,
                'action' => $action,
                'upvoted' => $upvoted,
                'upvotes' => $count
            ]);
        } catch (\Throwable $e) {
            Response::json(['success' => false, 'error' => 'Database error: ' . $e->getMessage()], 500);
        }
    }

    // Delete Comment (Admin or Comment Owner)
    public function deleteComment(Request $request, array $params): void {
        if (!Auth::check()) {
            Response::json(['error' => 'Unauthorized'], 401);
        }
        $commentId = (int)($params['id'] ?? 0);
        $comment = Database::fetch("SELECT * FROM comments WHERE id = ?", [$commentId]);
        if (!$comment) {
            Response::json(['error' => 'Comment not found'], 404);
        }
        $userId = Auth::id();
        if (!Auth::isAdmin() && (int)$comment['user_id'] !== (int)$userId) {
            Response::json(['error' => 'Forbidden'], 403);
        }
        Database::execute("UPDATE comments SET status = 'deleted' WHERE id = ?", [$commentId]);
        Response::json(['success' => true]);
    }

    // Record Prompt Copy
    public function copyPrompt(Request $request, array $params): void {
        $id = (int)($params['id'] ?? 0);
        Database::execute("UPDATE prompts SET copy_count = copy_count + 1 WHERE content_id = ?", [$id]);
        Response::json(['success' => true]);
    }

    // Debounced Autosave for Author Studio
    public function autosaveDraft(Request $request): void {
        if (!Auth::isAuthor()) {
            Response::json(['error' => 'Unauthorized'], 403);
        }

        $authorId = (int)Auth::user()['author_id'];
        $id = $request->input('id');
        $title = trim((string)$request->input('title', 'Untitled Draft'));
        $type = (string)$request->input('content_type', 'article');
        $categoryId = (int)$request->input('category_id');
        $summary = trim((string)$request->input('summary', ''));
        $bodyHtml = (string)$request->input('body_html', '');
        $slug = Sanitizer::slug($title);

        if ($id) {
            Database::execute("UPDATE contents SET title = ?, slug = ?, category_id = ?, summary = ?, body_html = ?, updated_at = NOW() WHERE id = ? AND author_id = ?", [
                $title, $slug, $categoryId, $summary, $bodyHtml, $id, $authorId
            ]);
            $savedId = (int)$id;
        } else {
            $uuid = bin2hex(random_bytes(16));
            Database::execute("INSERT INTO contents (uuid, content_type, author_id, category_id, title, slug, summary, body_html, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'draft')", [
                $uuid, $type, $authorId, $categoryId, $title, $slug, $summary, $bodyHtml
            ]);
            $savedId = (int)Database::lastInsertId();
        }

        Response::json([
            'success' => true,
            'id' => $savedId,
            'saved_at' => date('H:i:s')
        ]);
    }

    // Instant Autocomplete Search API
    public function instantSearch(Request $request): void {
        $q = trim((string)$request->query('q', ''));
        if (strlen($q) < 2) {
            Response::json(['results' => []]);
        }

        $results = Content::search($q, 'all', 8);
        Response::json([
            'query' => $q,
            'results' => $results
        ]);
    }

    // Internal Links Search for Author Editor
    public function internalLinksSearch(Request $request): void {
        $q = trim((string)$request->query('q', ''));
        if (strlen($q) < 1) {
            $items = Database::query("SELECT c.id, c.title, c.slug, c.content_type, cat.name as category_name 
                                      FROM contents c 
                                      LEFT JOIN categories cat ON c.category_id = cat.id 
                                      WHERE c.status = 'published' 
                                      ORDER BY c.published_at DESC LIMIT 15");
        } else {
            $wildcard = "%{$q}%";
            $items = Database::query("SELECT c.id, c.title, c.slug, c.content_type, cat.name as category_name 
                                      FROM contents c 
                                      LEFT JOIN categories cat ON c.category_id = cat.id 
                                      WHERE c.status = 'published' AND (c.title LIKE ? OR c.summary LIKE ?) 
                                      ORDER BY c.view_count DESC LIMIT 20", [$wildcard, $wildcard]);
        }

        $results = [];
        foreach ($items as $item) {
            $path = '/article/' . $item['slug'];
            if ($item['content_type'] === 'ai_tool') $path = '/tools/' . $item['slug'];
            if ($item['content_type'] === 'gpt_listing') $path = '/gpts/' . $item['slug'];
            if ($item['content_type'] === 'prompt') $path = '/prompts/' . $item['slug'];
            if ($item['content_type'] === 'campaign') $path = '/campaigns/' . $item['slug'];

            $results[] = [
                'id' => (int)$item['id'],
                'title' => $item['title'],
                'slug' => $item['slug'],
                'type' => $item['content_type'],
                'category' => $item['category_name'] ?? 'General',
                'url' => \App\Core\Config::url($path),
                'relative_url' => $path
            ];
        }

        Response::json([
            'success' => true,
            'query' => $q,
            'results' => $results
        ]);
    }

    // Toggle Subscription (Topic, Category, Author)
    public function toggleSubscription(Request $request): void {
        if (!Auth::check()) {
            Response::json(['success' => false, 'error' => 'Authentication required.'], 401);
        }

        $userId = (int)Auth::id();
        $type = (string)$request->input('type', 'topic');
        $id = (int)$request->input('id', 0);

        if (!in_array($type, ['topic', 'category', 'author'], true) || $id <= 0) {
            Response::json(['success' => false, 'error' => 'Invalid subscription target.'], 400);
        }

        // Check if subscribed
        $existing = Database::fetch("SELECT id FROM user_subscriptions WHERE user_id = ? AND subscribable_type = ? AND subscribable_id = ?", [
            $userId, $type, $id
        ]);

        if ($existing) {
            Database::execute("DELETE FROM user_subscriptions WHERE id = ?", [$existing['id']]);
            $isSubscribed = false;

            if ($type === 'topic') {
                Database::execute("UPDATE topics SET follower_count = GREATEST(0, follower_count - 1) WHERE id = ?", [$id]);
            } elseif ($type === 'author') {
                Database::execute("UPDATE authors SET follower_count = GREATEST(0, follower_count - 1) WHERE id = ?", [$id]);
            }
        } else {
            Database::execute("INSERT INTO user_subscriptions (user_id, subscribable_type, subscribable_id) VALUES (?, ?, ?)", [
                $userId, $type, $id
            ]);
            $isSubscribed = true;

            if ($type === 'topic') {
                Database::execute("UPDATE topics SET follower_count = follower_count + 1 WHERE id = ?", [$id]);
            } elseif ($type === 'author') {
                Database::execute("UPDATE authors SET follower_count = follower_count + 1 WHERE id = ?", [$id]);
            }
        }

        Response::json([
            'success' => true,
            'is_subscribed' => $isSubscribed,
            'type' => $type,
            'id' => $id
        ]);
    }

    // Upload & Parse DOCX file
    public function importDocx(Request $request): void {
        if (!Auth::check()) {
            Response::json(['success' => false, 'error' => 'Authentication required.'], 401);
        }

        if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            Response::json(['success' => false, 'error' => 'Please provide a valid .docx file upload.'], 400);
        }

        $file = $_FILES['file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'docx') {
            Response::json(['success' => false, 'error' => 'Only Microsoft Word (.docx) files are supported.'], 400);
        }

        $tempPath = $file['tmp_name'];
        $parsed = \App\Core\DocxParser::parse($tempPath);

        if (!$parsed['success']) {
            Response::json(['success' => false, 'error' => $parsed['error'] ?? 'Failed to parse .docx file.'], 422);
        }

        Response::json([
            'success' => true,
            'title' => $parsed['title'],
            'summary' => $parsed['summary'],
            'body_html' => $parsed['body_html'],
            'word_count' => $parsed['word_count'],
            'filename' => htmlspecialchars($file['name'])
        ]);
    }


    // Telemetry Event Collector
    public function logTelemetry(Request $request): void {
        $type = (string)$request->input('event_type', 'pageview');
        $contentId = $request->input('content_id') ? (int)$request->input('content_id') : null;
        $authorId = $request->input('author_id') ? (int)$request->input('author_id') : null;
        $duration = (int)$request->input('duration_seconds', 0);
        $meta = $request->input('metadata', []);

        Analytics::logEvent($type, $contentId, $authorId, $duration, $meta);
        Response::json(['success' => true]);
    }

    public function entityAnalyticsJson(Request $request, array $params): void {
        $id = (int)($params['id'] ?? 0);
        if ($id <= 0) {
            Response::json(['success' => false, 'error' => 'Invalid content ID'], 400);
            return;
        }

        $analytics = Analytics::getEntityAnalytics($id);
        if (empty($analytics)) {
            Response::json(['success' => false, 'error' => 'Content not found'], 404);
            return;
        }

        Response::json([
            'success' => true,
            'data' => $analytics
        ]);
    }

    // ------------------------------------------------------------------------
    // UNIFIED ENTITY REPORTING (Articles, Tools, GPTs, Prompts, Jobs, Comments, Campaigns)
    // ------------------------------------------------------------------------
    public function submitReport(Request $request): void {
        $user = Auth::user();
        $userId = $user ? (int)$user['id'] : null;

        $entityType = (string)$request->post('entity_type', '');
        $entityId = (int)$request->post('entity_id', 0);
        $entityTitle = trim((string)$request->post('entity_title', ''));
        $entityUrl = trim((string)$request->post('entity_url', ''));
        $reason = (string)$request->post('reason', 'spam');
        $details = trim((string)$request->post('details', ''));
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        $validTypes = ['article', 'ai_tool', 'gpt_listing', 'prompt', 'job', 'comment', 'campaign', 'author'];
        $validReasons = ['spam', 'broken_or_misleading', 'plagiarism_copyright', 'inappropriate_harmful', 'malicious_prompt_injection', 'other'];

        if (!in_array($entityType, $validTypes, true) || $entityId <= 0) {
            Response::json(['success' => false, 'error' => 'Invalid entity target for report.'], 400);
            return;
        }

        if (!in_array($reason, $validReasons, true)) {
            $reason = 'other';
        }

        Database::execute("INSERT INTO entity_reports (reporter_user_id, entity_type, entity_id, entity_title, entity_url, reason, details, ip_address, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())", [
            $userId, $entityType, $entityId, $entityTitle, $entityUrl, $reason, $details, $ip
        ]);

        if ($userId) {
            $shortTitle = !empty($entityTitle) ? $entityTitle : (ucfirst($entityType) . " #{$entityId}");
            $actionUrl = !empty($entityUrl) ? $entityUrl : '/support';
            $readableReason = ucwords(str_replace('_', ' ', $reason));
            \App\Core\NotificationHelper::send(
                $userId,
                'moderation',
                "Report Submitted: {$shortTitle}",
                "Your report regarding {$entityType} \"{$shortTitle}\" (Issue: {$readableReason}) was received by moderation staff.",
                $actionUrl
            );
        }

        Response::json([
            'success' => true,
            'message' => 'Thank you for keeping TechScout safe and trustworthy. Your report has been submitted to editorial moderation staff.'
        ]);
    }

    public function toggleAuthorFollow(Request $request, array $params): void {
        $user = Auth::user();
        if (!$user) {
            Response::json(['success' => false, 'error' => 'Please sign in to follow authors.'], 401);
            return;
        }

        $userId = (int)$user['id'];
        $authorId = (int)($params['id'] ?? $request->post('author_id', 0));

        if ($authorId <= 0) {
            Response::json(['success' => false, 'error' => 'Invalid author ID.'], 400);
            return;
        }

        $existing = Database::fetch("SELECT id FROM author_followers WHERE author_id = ? AND user_id = ?", [$authorId, $userId]);
        if ($existing) {
            Database::execute("DELETE FROM author_followers WHERE id = ?", [$existing['id']]);
            Database::execute("UPDATE authors SET follower_count = GREATEST(0, follower_count - 1) WHERE id = ?", [$authorId]);
            $isFollowing = false;
        } else {
            Database::execute("INSERT INTO author_followers (author_id, user_id, created_at) VALUES (?, ?, NOW())", [$authorId, $userId]);
            Database::execute("UPDATE authors SET follower_count = follower_count + 1 WHERE id = ?", [$authorId]);
            $isFollowing = true;
        }

        $authorRow = Database::fetch("SELECT follower_count FROM authors WHERE id = ?", [$authorId]);
        $followersCount = (int)($authorRow['follower_count'] ?? 0);

        Response::json([
            'success' => true,
            'is_following' => $isFollowing,
            'follower_count' => $followersCount,
            'message' => $isFollowing ? 'You are now following this author! 🔔' : 'Unfollowed author.'
        ]);
    }

    public function getAuthorFollowers(Request $request, array $params): void {
        if (!\App\Core\Auth::isAdmin()) {
            Response::json([
                'success' => false,
                'error' => 'Follower profile rosters are strictly restricted to administrators.'
            ], 403);
            return;
        }

        $authorId = (int)($params['id'] ?? 0);
        $followers = Database::query("
            SELECT af.created_at as followed_at, u.id as user_id, u.username, COALESCE(a.display_name, u.username) as display_name, a.avatar_url,
                   a.id as author_id, a.slug as author_slug, a.headline, a.verification_badge, a.reputation_score
            FROM author_followers af
            JOIN users u ON af.user_id = u.id
            LEFT JOIN authors a ON a.user_id = u.id
            WHERE af.author_id = ?
            ORDER BY af.created_at DESC
            LIMIT 100
        ", [$authorId]);

        $currentUserId = \App\Core\Auth::id();
        foreach ($followers as &$f) {
            $f['is_following'] = false;
            if ($currentUserId && !empty($f['author_id'])) {
                $f['is_following'] = (bool)Database::fetch("SELECT id FROM author_followers WHERE author_id = ? AND user_id = ?", [$f['author_id'], $currentUserId]);
            }
        }

        $authorRow = Database::fetch("SELECT follower_count FROM authors WHERE id = ?", [$authorId]);
        $totalVerifiedFollowers = (int)($authorRow['follower_count'] ?? count($followers));

        Response::json([
            'success' => true,
            'followers' => $followers,
            'total' => count($followers),
            'verified_follower_count' => $totalVerifiedFollowers
        ]);
    }

    public function getAuthorFollowing(Request $request, array $params): void {
        if (!\App\Core\Auth::isAdmin()) {
            Response::json([
                'success' => false,
                'error' => 'Following profile rosters are strictly restricted to administrators.'
            ], 403);
            return;
        }

        $authorId = (int)($params['id'] ?? 0);
        $author = Database::fetch("SELECT user_id FROM authors WHERE id = ?", [$authorId]);
        if (!$author) {
            Response::json(['success' => false, 'error' => 'Author not found.'], 404);
            return;
        }

        $following = Database::query("
            SELECT af.created_at as followed_at, a.id as author_id, a.display_name, a.slug as author_slug, a.avatar_url, a.headline, a.verification_badge, a.reputation_score,
                   a.follower_count as target_follower_count
            FROM author_followers af
            JOIN authors a ON af.author_id = a.id
            WHERE af.user_id = ?
            ORDER BY af.created_at DESC
            LIMIT 100
        ", [(int)$author['user_id']]);

        $currentUserId = \App\Core\Auth::id();
        foreach ($following as &$fw) {
            $fw['is_following'] = false;
            if ($currentUserId) {
                $fw['is_following'] = (bool)Database::fetch("SELECT id FROM author_followers WHERE author_id = ? AND user_id = ?", [$fw['author_id'], $currentUserId]);
            }
        }

        Response::json(['success' => true, 'following' => $following, 'total' => count($following)]);
    }

    // ------------------------------------------------------------------------
    // ------------------------------------------------------------------------
    // IN-APP NOTIFICATIONS GATEWAY (PAGINATED & INTERACTIVE)
    // ------------------------------------------------------------------------
    public function getNotifications(Request $request): void {
        $user = Auth::user();
        if (!$user) {
            Response::json(['unread_count' => 0, 'notifications' => [], 'total' => 0, 'totalPages' => 1]);
            return;
        }

        $userId = (int)$user['id'];
        $filter = (string)$request->query('filter', 'all');
        $search = trim((string)$request->query('search', ''));
        $page = max(1, (int)$request->query('page', 1));
        $perPage = max(1, min(250, (int)$request->query('per_page', 20)));

        $paginated = \App\Core\NotificationHelper::getPaginated($userId, $page, $perPage, $filter !== 'all' ? $filter : null, !empty($search) ? $search : null);
        $counts = \App\Core\NotificationHelper::getCountsByFilter($userId);

        Response::json([
            'success' => true,
            'unread_count' => $paginated['unreadCount'],
            'notifications' => $paginated['items'],
            'total' => $paginated['total'],
            'page' => $paginated['page'],
            'per_page' => $paginated['perPage'],
            'total_pages' => $paginated['totalPages'],
            'counts' => $counts
        ]);
    }

    public function markNotificationRead(Request $request): void {
        $user = Auth::user();
        if (!$user) {
            Response::json(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }

        $userId = (int)$user['id'];
        $id = (int)($request->input('id') ?? $request->post('id', 0));
        $ids = (array)($request->input('ids') ?? $request->post('ids', []));
        $markAll = ($request->input('all') || $request->post('all') || $request->input('mark_all') || $request->post('mark_all')) ? true : false;
        $toggle = ($request->input('toggle') || $request->post('toggle')) ? true : false;
        $filter = (string)($request->input('filter') ?? $request->post('filter', 'all'));

        if ($markAll) {
            \App\Core\NotificationHelper::markAllAsRead($userId, $filter !== 'all' ? $filter : null);
        } elseif (!empty($ids)) {
            \App\Core\NotificationHelper::markBatchRead($ids, $userId);
        } elseif ($toggle && $id > 0) {
            \App\Core\NotificationHelper::toggleRead($id, $userId);
        } elseif ($id > 0) {
            \App\Core\NotificationHelper::markAsRead($id, $userId);
        }

        $counts = \App\Core\NotificationHelper::getCountsByFilter($userId);

        Response::json([
            'success' => true,
            'unread_count' => \App\Core\NotificationHelper::getUnreadCount($userId),
            'counts' => $counts
        ]);
    }

    public function markAllNotificationsRead(Request $request): void {
        $user = Auth::user();
        if (!$user) {
            Response::json(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }

        $userId = (int)$user['id'];
        $filter = (string)($request->input('filter') ?? $request->post('filter', 'all'));
        \App\Core\NotificationHelper::markAllAsRead($userId, $filter !== 'all' ? $filter : null);

        $counts = \App\Core\NotificationHelper::getCountsByFilter($userId);

        Response::json([
            'success' => true,
            'message' => 'All notifications marked as read.',
            'unread_count' => 0,
            'counts' => $counts
        ]);
    }

    public function deleteNotification(Request $request, array $params): void {
        $user = Auth::user();
        if (!$user) {
            Response::json(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }

        $userId = (int)$user['id'];
        $id = (int)($params['id'] ?? $request->input('id') ?? $request->post('id', 0));
        $ids = (array)($request->input('ids') ?? $request->post('ids', []));

        if (!empty($ids)) {
            \App\Core\NotificationHelper::deleteBatch($ids, $userId);
        } elseif ($id > 0) {
            \App\Core\NotificationHelper::delete($id, $userId);
        }

        $counts = \App\Core\NotificationHelper::getCountsByFilter($userId);

        Response::json([
            'success' => true,
            'message' => 'Notification(s) dismissed.',
            'unread_count' => \App\Core\NotificationHelper::getUnreadCount($userId),
            'counts' => $counts
        ]);
    }

    public function clearAllNotifications(Request $request): void {
        $user = Auth::user();
        if (!$user) {
            Response::json(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }

        $userId = (int)$user['id'];
        $filter = (string)($request->input('filter') ?? $request->post('filter', 'all'));
        \App\Core\NotificationHelper::clearAll($userId, $filter !== 'all' ? $filter : null);

        $counts = \App\Core\NotificationHelper::getCountsByFilter($userId);

        Response::json([
            'success' => true,
            'message' => 'Notifications cleared successfully.',
            'unread_count' => 0,
            'counts' => $counts
        ]);
    }

    // ------------------------------------------------------------------------
    // BOOKMARKS & SAVED CONTENT
    // ------------------------------------------------------------------------
    public function toggleBookmark(Request $request): void {
        $user = Auth::user();
        if (!$user) {
            Response::json(['success' => false, 'error' => 'Please sign in to save bookmarks.'], 401);
            return;
        }

        $userId = (int)$user['id'];
        $contentId = (int)($request->input('content_id') ?? $request->post('content_id', 0));

        if ($contentId <= 0) {
            Response::json(['success' => false, 'error' => 'Invalid content identifier.'], 400);
            return;
        }

        try {
            $existing = Database::fetch("SELECT id FROM bookmarks WHERE user_id = ? AND content_id = ?", [$userId, $contentId]);

            if ($existing) {
                Database::execute("DELETE FROM bookmarks WHERE user_id = ? AND content_id = ?", [$userId, $contentId]);
                $isBookmarked = false;
                $msg = 'Bookmark removed.';
            } else {
                Database::execute("INSERT IGNORE INTO bookmarks (user_id, content_id, created_at) VALUES (?, ?, NOW())", [$userId, $contentId]);
                $isBookmarked = true;
                $msg = 'Saved to your private bookmarks! 🔖';
            }

            $totalBookmarks = (int)(Database::fetch("SELECT COUNT(*) as c FROM bookmarks WHERE content_id = ?", [$contentId])['c'] ?? 0);

            Response::json([
                'success' => true,
                'is_bookmarked' => $isBookmarked,
                'total_bookmarks' => $totalBookmarks,
                'message' => $msg
            ]);
        } catch (\Throwable $e) {
            Response::json(['success' => false, 'error' => 'Database error: ' . $e->getMessage()], 500);
        }
    }

    // ------------------------------------------------------------------------
    // READING HISTORY & MULTI-SESSION RE-READ ENGINE
    // ------------------------------------------------------------------------
    public function initReadingSession(Request $request): void {
        $user = Auth::user();
        if (!$user) {
            Response::json(['success' => false, 'message' => 'Guest user'], 200);
            return;
        }

        $userId = (int)$user['id'];
        $contentId = (int)$request->post('content_id', 0);
        if ($contentId <= 0) {
            Response::json(['success' => false, 'error' => 'Invalid content ID'], 400);
            return;
        }

        $sessionId = trim((string)$request->post('session_id', ''));
        if (empty($sessionId)) {
            $sessionId = 'sess_' . bin2hex(random_bytes(8));
        }

        // Check user's most recent reading session for this article
        $latest = Database::fetch(
            "SELECT * FROM reading_history WHERE user_id = ? AND content_id = ? ORDER BY id DESC LIMIT 1",
            [$userId, $contentId]
        );

        $isReread = false;
        $sessionNumber = 1;
        $historyId = 0;

        if (!$latest) {
            // First time reading
            Database::execute(
                "INSERT INTO reading_history (user_id, content_id, session_id, session_number, progress_percent, read_seconds, started_at, last_read_at, status)
                 VALUES (?, ?, ?, 1, 0, 0, NOW(), NOW(), 'reading')",
                [$userId, $contentId, $sessionId]
            );
            $historyId = (int)Database::lastInsertId();
            $sessionNumber = 1;
        } else {
            // If the user already finished the article previously (>= 90% progress or status = completed),
            // opening it again starts a SEPARATE brand new reading session starting at 0%!
            if ((int)$latest['progress_percent'] >= 90 || $latest['status'] === 'completed') {
                $isReread = true;
                $sessionNumber = ((int)$latest['session_number']) + 1;
                Database::execute(
                    "INSERT INTO reading_history (user_id, content_id, session_id, session_number, progress_percent, read_seconds, started_at, last_read_at, status)
                     VALUES (?, ?, ?, ?, 0, 0, NOW(), NOW(), 're_reading')",
                    [$userId, $contentId, $sessionId, $sessionNumber]
                );
                $historyId = (int)Database::lastInsertId();
            } else {
                // Resume active uncompleted session
                $historyId = (int)$latest['id'];
                $sessionNumber = (int)$latest['session_number'];
                Database::execute(
                    "UPDATE reading_history SET session_id = ?, last_read_at = NOW() WHERE id = ?",
                    [$sessionId, $historyId]
                );
            }
        }

        $totalCompletions = (int)(Database::fetch(
            "SELECT COUNT(*) as c FROM reading_history WHERE user_id = ? AND content_id = ? AND (progress_percent >= 90 OR status = 'completed')",
            [$userId, $contentId]
        )['c'] ?? 0);

        Response::json([
            'success' => true,
            'history_id' => $historyId,
            'session_id' => $sessionId,
            'session_number' => $sessionNumber,
            'is_reread' => $isReread,
            'total_completions' => $totalCompletions,
            'starting_progress' => ($isReread || !$latest) ? 0 : (int)$latest['progress_percent']
        ]);
    }

    public function recordHistory(Request $request): void {
        $user = Auth::user();
        if (!$user) {
            Response::json(['success' => false], 401);
            return;
        }

        $userId = (int)$user['id'];
        $contentId = (int)$request->post('content_id', 0);
        $progress = min(100, max(0, (int)$request->post('progress_percent', 0)));
        $readSecondsDelta = max(0, (int)$request->post('read_seconds', 0));
        $sessionId = trim((string)$request->post('session_id', ''));
        $historyId = (int)$request->post('history_id', 0);

        if ($contentId <= 0) {
            Response::json(['success' => false, 'error' => 'Invalid content'], 400);
            return;
        }

        // Find matching active reading session row
        $target = null;
        if ($historyId > 0) {
            $target = Database::fetch("SELECT * FROM reading_history WHERE id = ? AND user_id = ?", [$historyId, $userId]);
        }
        if (!$target && !empty($sessionId)) {
            $target = Database::fetch("SELECT * FROM reading_history WHERE session_id = ? AND user_id = ?", [$sessionId, $userId]);
        }
        if (!$target) {
            $target = Database::fetch("SELECT * FROM reading_history WHERE user_id = ? AND content_id = ? ORDER BY id DESC LIMIT 1", [$userId, $contentId]);
        }

        if ($target) {
            $newProgress = max((int)$target['progress_percent'], $progress);
            $newReadSecs = (int)$target['read_seconds'] + $readSecondsDelta;
            $newStatus = ($newProgress >= 90) ? 'completed' : $target['status'];
            $completedAt = ($newProgress >= 90 && empty($target['completed_at'])) ? date('Y-m-d H:i:s') : $target['completed_at'];

            Database::execute(
                "UPDATE reading_history SET 
                    progress_percent = ?, 
                    read_seconds = ?, 
                    status = ?, 
                    completed_at = ?, 
                    last_read_at = NOW() 
                 WHERE id = ?",
                [$newProgress, $newReadSecs, $newStatus, $completedAt, (int)$target['id']]
            );

            Response::json([
                'success' => true, 
                'history_id' => (int)$target['id'], 
                'progress' => $newProgress, 
                'status' => $newStatus
            ]);
            return;
        }

        // Fallback: create fresh entry
        Database::execute(
            "INSERT INTO reading_history (user_id, content_id, session_id, session_number, progress_percent, read_seconds, started_at, last_read_at, status)
             VALUES (?, ?, ?, 1, ?, ?, NOW(), NOW(), ?)",
            [$userId, $contentId, $sessionId ?: ('sess_' . bin2hex(random_bytes(6))), $progress, $readSecondsDelta, ($progress >= 90 ? 'completed' : 'reading')]
        );

        Response::json(['success' => true]);
    }

    // ------------------------------------------------------------------------
    // CROSS-ENTITY ANALYTICS TELEMETRY EVENT LOGGER
    // ------------------------------------------------------------------------
    public function trackEvent(Request $request): void {
        $type = (string)($request->input('event_type') ?? $request->post('event_type', 'view'));
        $contentId = (int)($request->input('content_id') ?? $request->post('content_id', 0));
        $authorId = (int)($request->input('author_id') ?? $request->post('author_id', 0));
        $duration = (int)($request->input('duration') ?? $request->post('duration', 0));
        $meta = $request->input('meta') ?? [];
        if (is_string($meta)) {
            $meta = json_decode($meta, true) ?: [];
        }

        try {
            Analytics::logEvent(
                $type, 
                $contentId > 0 ? $contentId : null, 
                $authorId > 0 ? $authorId : null, 
                $duration, 
                is_array($meta) ? $meta : []
            );
            Response::json(['success' => true]);
        } catch (\Throwable $e) {
            Response::json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * 10-Second Dwell-Time Read Ping (Monetization Requirement)
     */
    public function recordReadDwellPing(Request $request): void {
        $contentId = (int)($request->input('content_id') ?? $request->post('content_id', 0));
        $duration = (int)($request->input('duration_seconds') ?? $request->post('duration_seconds', 10));

        if ($contentId <= 0) {
            Response::json(['success' => false, 'error' => 'Invalid content ID'], 400);
            return;
        }

        $content = Database::fetch("SELECT id, author_id, title FROM contents WHERE id = ?", [$contentId]);
        if (!$content) {
            Response::json(['success' => false, 'error' => 'Content not found'], 404);
            return;
        }

        $authorId = !empty($content['author_id']) ? (int)$content['author_id'] : null;
        $userId = Auth::id();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

        $credited = Analytics::logVerified10sDwell($contentId, $authorId, $userId, $ip, $ua);
        $totalVerified = Analytics::getVerified10sReads($contentId);

        Response::json([
            'success' => true,
            'verified_10s_read' => $credited,
            'total_verified_reads' => $totalVerified,
            'dwell_seconds' => $duration
        ]);
    }


    public function clearHistory(Request $request): void {
        $user = Auth::user();
        if (!$user) {
            Response::json(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }

        $userId = (int)$user['id'];
        Database::execute("DELETE FROM reading_history WHERE user_id = ?", [$userId]);

        Response::json(['success' => true, 'message' => 'Reading history cleared successfully.']);
    }

    public function deleteHistoryItem(Request $request, array $params): void {
        $user = Auth::user();
        if (!$user) {
            Response::json(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }

        $userId = (int)$user['id'];
        $historyId = (int)($params['id'] ?? 0);

        if ($historyId > 0) {
            Database::execute("DELETE FROM reading_history WHERE id = ? AND user_id = ?", [$historyId, $userId]);
        }

        Response::json(['success' => true, 'message' => 'Reading history entry removed.']);
    }

    // ------------------------------------------------------------------------
    // NEWSLETTER FAST SUBSCRIPTION ENDPOINT
    // ------------------------------------------------------------------------
    public function subscribeNewsletter(Request $request): void {
        $email = trim(strtolower((string)$request->input('email', '')));
        $type = trim((string)$request->input('subscriber_type', 'general'));

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::json(['success' => false, 'error' => 'Please provide a valid email address.'], 400);
            return;
        }

        try {
            $existing = Database::fetch("SELECT * FROM newsletter_subscriptions WHERE email = ?", [$email]);
            if ($existing) {
                if ($existing['status'] === 'unsubscribed') {
                    Database::execute("UPDATE newsletter_subscriptions SET status = 'subscribed', updated_at = NOW() WHERE id = ?", [$existing['id']]);
                    Response::json(['success' => true, 'message' => '🎉 Welcome back! Your subscription has been reactivated.']);
                    return;
                }
                Response::json(['success' => true, 'message' => '✓ You are already subscribed to the TechScout Dispatch!']);
                return;
            }

            Database::execute(
                "INSERT INTO newsletter_subscriptions (email, subscriber_type, status, created_at) VALUES (?, ?, 'subscribed', NOW())",
                [$email, $type]
            );

            // Optional audit notification
            if (class_exists('\\App\\Models\\AuditLog')) {
                \App\Models\AuditLog::log('newsletter.subscribe', 'newsletter_subscriptions', (int)Database::lastInsertId(), ['email' => $email]);
            }

            Response::json([
                'success' => true,
                'message' => '🎉 Subscription confirmed! Welcome to the TechScout Weekly Dispatch.'
            ]);
        } catch (\Throwable $e) {
            Response::json(['success' => false, 'error' => 'Unable to complete subscription: ' . $e->getMessage()], 500);
        }
    }

    // ------------------------------------------------------------------------
    // UNIVERSAL IMAGE UPLOAD ENDPOINT FOR ALL ENTITIES & EDITORS
    // ------------------------------------------------------------------------
    public function uploadImage(Request $request): void {
        if (!Auth::check()) {
            Response::json(['success' => false, 'error' => 'Authentication required to upload media.'], 401);
            return;
        }

        $file = $_FILES['image'] ?? $_FILES['file'] ?? $_FILES['media_file'] ?? null;
        if (!$file || empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            Response::json(['success' => false, 'error' => 'Please select a valid image file to upload.'], 400);
            return;
        }

        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExts, true)) {
            Response::json(['success' => false, 'error' => 'Unsupported image format. Allowed: ' . implode(', ', $allowedExts)], 400);
            return;
        }

        if ($file['size'] > 15 * 1024 * 1024) {
            Response::json(['success' => false, 'error' => 'Image file size exceeds maximum limit (15 MB).'], 400);
            return;
        }

        $folder = trim((string)$request->input('folder', 'covers'));
        if (!in_array($folder, ['covers', 'avatars', 'tools', 'gpts', 'prompts', 'general'], true)) {
            $folder = 'covers';
        }

        $targetDir = __DIR__ . '/../../assets/uploads/' . $folder . '/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $uniqueName = $folder . '_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $targetPath = $targetDir . $uniqueName;
        $relativeUrl = '/assets/uploads/' . $folder . '/' . $uniqueName;
        $fullUrl = url($relativeUrl);

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            Response::json([
                'success' => true,
                'url' => $fullUrl,
                'location' => $fullUrl,
                'relative_url' => $relativeUrl,
                'filename' => $uniqueName,
                'original_name' => basename($file['name']),
                'size' => (int)$file['size'],
                'message' => 'Image uploaded successfully! 📸'
            ]);
        } else {
            Response::json(['success' => false, 'error' => 'Failed to write uploaded image to disk.'], 500);
        }
    }

    /**
     * Preview endpoint for content queue review station
     */
    public function contentPreview(Request $request, array $params): void {
        $id = (int)($params['id'] ?? 0);
        $content = Database::fetch("
            SELECT c.*, a.display_name as author_name, a.slug as author_slug, a.avatar_url as author_avatar, a.reputation_score,
                   cat.name as category_name
            FROM contents c
            LEFT JOIN authors a ON c.author_id = a.id
            LEFT JOIN categories cat ON c.category_id = cat.id
            WHERE c.id = ?
        ", [$id]);

        if (!$content) {
            Response::json(['success' => false, 'error' => 'Content submission not found.'], 404);
            return;
        }

        // Calculate diagnostics
        $cleanText = strip_tags($content['body_html'] ?? $content['summary'] ?? '');
        $wordCount = str_word_count($cleanText);
        $readTime = max(1, (int)ceil($wordCount / 200));

        Response::json([
            'success' => true,
            'content' => $content,
            'diagnostics' => [
                'word_count' => $wordCount,
                'read_time_minutes' => $readTime,
                'character_count' => mb_strlen($cleanText)
            ]
        ]);
    }

    /**
     * Live availability checking for username & email during registration
     */
    public function checkAuthAvailability(Request $request): void {
        $type = strtolower((string)$request->get('type', ''));
        $value = trim((string)$request->get('value', ''));

        if ($type === 'username') {
            if (strlen($value) < 3 || strlen($value) > 30) {
                Response::json([
                    'available' => false,
                    'valid_format' => false,
                    'message' => 'Username must be between 3 and 30 characters.'
                ]);
                return;
            }
            if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $value)) {
                Response::json([
                    'available' => false,
                    'valid_format' => false,
                    'message' => 'Only letters, numbers, and underscores are allowed.'
                ]);
                return;
            }

            $exists = Database::fetch("SELECT id FROM users WHERE username = ?", [$value]);
            if ($exists) {
                Response::json([
                    'available' => false,
                    'valid_format' => true,
                    'message' => 'Username is already taken.'
                ]);
            } else {
                Response::json([
                    'available' => true,
                    'valid_format' => true,
                    'message' => 'Username is available!'
                ]);
            }
            return;
        }

        if ($type === 'email') {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                Response::json([
                    'available' => false,
                    'valid_format' => false,
                    'message' => 'Invalid email address format.'
                ]);
                return;
            }

            $exists = Database::fetch("SELECT id FROM users WHERE email = ?", [$value]);
            if ($exists) {
                Response::json([
                    'available' => false,
                    'valid_format' => true,
                    'message' => 'Email is already registered.'
                ]);
            } else {
                Response::json([
                    'available' => true,
                    'valid_format' => true,
                    'message' => 'Email is available!'
                ]);
            }
            return;
        }

        Response::json(['error' => 'Invalid check type. Specify type=username or type=email.'], 400);
    }

    /**
     * Ingest Client-Side JavaScript Uncaught Exceptions & Console Errors
     */
    public function logClientError(Request $request): void {
        // Read raw input payload if beacon sent application/json
        $raw = file_get_contents('php://input');
        $json = !empty($raw) ? json_decode($raw, true) : null;

        $message = trim((string)($json['message'] ?? $request->post('message', 'Unknown Client JS Error')));
        $source = trim((string)($json['source'] ?? $request->post('source', '')));
        $line = (int)($json['lineno'] ?? $request->post('lineno', 0));
        $col = (int)($json['colno'] ?? $request->post('colno', 0));
        $stack = trim((string)($json['stack'] ?? $request->post('stack', '')));
        $url = trim((string)($json['url'] ?? $request->post('url', ($_SERVER['HTTP_REFERER'] ?? '/'))));

        if (empty($message) || $message === 'Script error.' || str_contains($message, 'Extension context invalidated')) {
            Response::json(['received' => true, 'logged' => false]);
            return;
        }

        // Deduplicate flood: limit identical error reports within 30 seconds
        $cacheKey = 'js_err_' . md5($message . $source . $line);
        if (isset($_SESSION[$cacheKey]) && (time() - $_SESSION[$cacheKey]) < 30) {
            Response::json(['received' => true, 'logged' => false, 'reason' => 'rate_limited']);
            return;
        }
        $_SESSION[$cacheKey] = time();

        $errorDetails = [
            'client_url' => $url,
            'source_file' => $source,
            'column' => $col,
            'screen' => $json['screen'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ];

        \App\Core\ErrorLogger::log(
            'JAVASCRIPT_ERROR',
            $message . ($source ? " (at {$source}:{$line}:{$col})" : ""),
            $source ?: ($url ?: 'client.js'),
            $line > 0 ? $line : null,
            $stack ?: "Client JS Error on URL: {$url}",
            $errorDetails
        );

        Response::json(['received' => true, 'logged' => true]);
    }

    /**
     * Automatic IP Geolocation to Timezone Resolution API
     * Returns detected timezone for client IP with offset and current time
     */
    public function getIpTimezone(Request $request): void {
        $clientIp = \App\Core\DateHelper::getClientIp();
        $queryIp = trim((string)($request->get('ip') ?: ''));
        $targetIp = (!empty($queryIp) && filter_var($queryIp, FILTER_VALIDATE_IP)) ? $queryIp : $clientIp;
        $tz = \App\Core\DateHelper::detectTimezoneByIp($targetIp);

        if (!headers_sent() && empty($_COOKIE['user_timezone'])) {
            @setcookie('user_timezone', $tz, time() + 31536000, '/', '', false, false);
            $_COOKIE['user_timezone'] = $tz;
        }

        $now = new \DateTime('now', new \DateTimeZone($tz));

        Response::json([
            'success' => true,
            'ip' => $targetIp,
            'client_ip' => $clientIp,
            'timezone' => $tz,
            'offset' => $now->format('P'),
            'abbreviation' => $now->format('T'),
            'formatted_time' => $now->format('Y-m-d H:i:s T')
        ]);
    }
}


