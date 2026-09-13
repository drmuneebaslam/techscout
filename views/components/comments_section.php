<?php
/**
 * Social-Grade Compact Threaded Discussion Component (Instagram / Facebook Style)
 * 
 * @var array $comments
 * @var array $content (or $article / $tool / $gpt)
 */

$contentItem = $article ?? $tool ?? $gpt ?? $prompt ?? $campaign ?? [];
$contentId = (int)($contentItem['id'] ?? 0);
$contentAuthorId = (int)($contentItem['author_id'] ?? 0);
$comments = (isset($comments) && is_array($comments)) ? $comments : [];
$totalComments = count($comments);
$currentUser = \App\Core\Auth::user();

// Query current user / IP upvoted comments & reactions for toggle state tracking
$userUpvotedCommentIds = [];
$userCommentReactions = [];
try {
    $currentUserId = \App\Core\Auth::id();
    $clientIp = \App\Core\Request::ip();
    if ($currentUserId) {
        $rows = \App\Core\Database::querySilent(
            "SELECT comment_id, reaction_type FROM comment_reactions WHERE user_id = ?",
            [$currentUserId]
        );
    } else {
        $rows = \App\Core\Database::querySilent(
            "SELECT comment_id, reaction_type FROM comment_reactions WHERE user_id IS NULL AND ip_address = ?",
            [$clientIp]
        );
    }
    foreach ($rows as $r) {
        $cid = (int)$r['comment_id'];
        $rtype = $r['reaction_type'] ?? 'helpful';
        if ($rtype === 'helpful') {
            $userUpvotedCommentIds[] = $cid;
        } else {
            $userCommentReactions[$cid] = $rtype;
        }
    }
} catch (\Throwable $e) {
    $userUpvotedCommentIds = [];
    $userCommentReactions = [];
}

if (!function_exists('renderRedditCommentNode')) {
    function renderRedditCommentNode(array $c, int $contentId, int $contentAuthorId, ?array $currentUser, int $depth = 0, array $userUpvotedCommentIds = [], array $userCommentReactions = []): void {
        $isAuthorComment = ($contentAuthorId && (int)$c['user_id'] === $contentAuthorId);
        $upvotes = (int)($c['reaction_count'] ?? 0);
        $commentId = (int)$c['id'];
        $hasReplies = !empty($c['replies']);
        $replyCount = $hasReplies ? count($c['replies']) : 0;
        $maxDepthIndent = min($depth * 1.5, 4.5);
        $isUpvoted = in_array($commentId, $userUpvotedCommentIds, true);
        $avatarSize = $depth === 0 ? 36 : 28;
        $isPinned = !empty($c['is_pinned']);
        $canPin = $currentUser && ((int)$currentUser['id'] === $contentAuthorId || \App\Core\Auth::isAdmin());

        // Parse reactions
        $reactions = [];
        if (!empty($c['reactions_json'])) {
            $reactions = is_string($c['reactions_json']) ? json_decode($c['reactions_json'], true) : $c['reactions_json'];
        }
        if (!is_array($reactions)) $reactions = [];
        $emojiMap = ['heart' => '❤️', 'rocket' => '🚀', 'idea' => '💡', 'fire' => '🔥', 'eyes' => '👀'];

        // Reputation / Trust Tier Badges [TS-SOC-05]
        $reputation = (int)($c['reputation_points'] ?? 0);
        $trustBadge = '';
        $trustColor = 'var(--ts-text-muted)';
        if (($c['role'] ?? '') === 'super_admin' || ($c['role'] ?? '') === 'admin') {
            $trustBadge = '🛡️ Mod';
            $trustColor = '#38bdf8';
        } elseif ($reputation >= 500) {
            $trustBadge = '🏆 Gold';
            $trustColor = '#f59e0b';
        } elseif ($reputation >= 150) {
            $trustBadge = '🥈 Silver';
            $trustColor = '#94a3b8';
        } elseif ($reputation >= 30) {
            $trustBadge = '🥉 Bronze';
            $trustColor = '#d97706';
        }
        ?>
        <div class="comment-node-wrapper <?= $depth === 0 ? 'comment-item' : 'comment-reply-node' ?> <?= $isPinned ? 'comment-pinned-highlight' : '' ?>" 
             id="comment-node-<?= $commentId ?>" 
             data-upvotes="<?= $upvotes ?>" 
             data-timestamp="<?= strtotime($c['created_at']) ?>"
             style="<?= $depth > 0 ? "margin-left: {$maxDepthIndent}rem; border-left: 2px solid var(--ts-border-subtle); padding-left: 0.85rem; margin-top: 0.5rem;" : "margin-bottom: 0.95rem;" ?><?= $isPinned ? ' border: 2px solid #eab308; background: rgba(234, 179, 8, 0.04); border-radius: 14px; padding: 0.75rem;' : '' ?>">
          
          <?php if ($isPinned): ?>
            <div style="display: flex; align-items: center; gap: 0.35rem; color: #eab308; font-size: 0.75rem; font-weight: 700; margin-bottom: 0.4rem; padding-left: 0.2rem;">
              <i data-lucide="pin" style="width: 14px; height: 14px;"></i> Author's Choice — Top Insight
            </div>
          <?php endif; ?>

          <div style="display: flex; gap: 0.65rem; align-items: flex-start;">
            <!-- Social Avatar -->
            <img src="<?= htmlspecialchars(image_url($c['avatar_url'] ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80')) ?>" 
                 alt="<?= htmlspecialchars($c['username']) ?>" 
                 style="width: <?= $avatarSize ?>px; height: <?= $avatarSize ?>px; border-radius: 50%; object-fit: cover; border: 1px solid var(--ts-border-main); flex-shrink: 0; margin-top: 2px;">

            <div style="flex: 1; max-width: calc(100% - <?= $avatarSize + 12 ?>px);">
              <!-- Compact Social Speech Bubble -->
              <div class="ts-comment-bubble" style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-subtle); border-radius: 16px; padding: 0.6rem 1rem; display: inline-block; max-width: 100%; position: relative;">
                <div style="display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.2rem; flex-wrap: wrap;">
                  <span style="font-weight: 700; font-size: 0.85rem; color: var(--ts-text-primary);">
                    <?= htmlspecialchars($c['display_name'] ?? $c['username']) ?>
                  </span>

                  <?php if ($trustBadge): ?>
                    <span class="ts-badge" style="font-size: 0.6rem; padding: 0.05rem 0.35rem; border: 1px solid <?= $trustColor ?>; color: <?= $trustColor ?>; background: transparent;">
                      <?= $trustBadge ?>
                    </span>
                  <?php endif; ?>

                  <?php if ($isAuthorComment): ?>
                    <span class="ts-badge ts-badge-pink" style="font-size: 0.6rem; padding: 0.05rem 0.35rem;">Author ✍️</span>
                  <?php endif; ?>

                  <?php if ($isPinned): ?>
                    <span class="ts-badge ts-badge-amber" style="font-size: 0.6rem; padding: 0.05rem 0.35rem; background: rgba(234,179,8,0.15); color: #eab308;">Pinned ⭐</span>
                  <?php endif; ?>
                </div>

                <div class="comment-body-text" style="font-size: 0.88rem; line-height: 1.5; color: var(--ts-text-primary); word-break: break-word; white-space: pre-wrap;"><?= htmlspecialchars($c['comment_text']) ?></div>

                <!-- Live Multi-Emoji Reactions Pill Row [TS-SOC-04] -->
                <div id="reactions-display-<?= $commentId ?>" style="display: flex; gap: 0.3rem; margin-top: 0.4rem; flex-wrap: wrap;">
                  <?php 
                    $userEmoji = $userCommentReactions[$commentId] ?? null;
                    if ($userEmoji === 'idea') $userEmoji = 'lightbulb';
                  ?>
                  <?php foreach ($emojiMap as $eKey => $eSymbol): ?>
                    <?php 
                      $cnt = (int)($reactions[$eKey] ?? 0); 
                      $isActive = ($userEmoji === $eKey);
                    ?>
                    <?php if ($cnt > 0): ?>
                      <span class="reaction-pill-badge <?= $isActive ? 'active-emoji-pill' : '' ?>" id="pill-<?= $commentId ?>-<?= $eKey ?>" style="font-size: 0.72rem; padding: 0.12rem 0.45rem; border-radius: 12px; background: <?= $isActive ? 'rgba(99,102,241,0.22)' : 'var(--ts-bg-surface)' ?>; border: 1px solid <?= $isActive ? 'var(--ts-primary)' : 'var(--ts-border-subtle)' ?>; color: <?= $isActive ? '#ffffff' : 'inherit' ?>; display: inline-flex; align-items: center; gap: 0.25rem; cursor: pointer; transition: all 0.15s ease;" onclick="sendEmojiReaction(<?= $commentId ?>, '<?= $eKey ?>')" title="<?= $isActive ? 'Click to remove reaction' : 'Click to react' ?>">
                        <?= $eSymbol ?> <span class="cnt" style="font-weight: <?= $isActive ? '700' : '400' ?>;"><?= $cnt ?></span>
                      </span>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </div>
              </div>

              <!-- Compact Social Action Row Underneath Bubble with Multi-Emoji Popover -->
              <div style="display: flex; align-items: center; gap: 0.85rem; font-size: 0.74rem; color: var(--ts-text-tertiary); margin-top: 0.3rem; padding-left: 0.5rem; flex-wrap: wrap; position: relative;">
                <span class="ts-time" data-timestamp="<?= strtotime($c['created_at']) ?>"><?= \App\Core\DateHelper::render($c['created_at'], 'relative') ?></span>

                <!-- Multi-Emoji Reaction Trigger Bar [TS-SOC-04] -->
                <div class="emoji-reaction-wrapper" style="position: relative; display: inline-block;">
                  <button type="button" class="reaction-trigger-btn" onclick="toggleEmojiBar(<?= $commentId ?>)" style="background: none; border: none; font-weight: 600; color: var(--ts-text-secondary); cursor: pointer; padding: 0; font-size: 0.74rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                    <span>React</span> 😊
                  </button>

                  <!-- Floating Reaction Bar (Desktop Hover / Mobile Click) -->
                  <div id="emoji-bar-<?= $commentId ?>" class="emoji-reaction-bar" style="display: none; position: absolute; bottom: 120%; left: 0; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 30px; padding: 0.25rem 0.5rem; box-shadow: 0 8px 24px rgba(0,0,0,0.3); z-index: 50; white-space: nowrap; gap: 0.35rem;">
                    <?php foreach ($emojiMap as $eKey => $eSymbol): ?>
                      <button type="button" onclick="sendEmojiReaction(<?= $commentId ?>, '<?= $eKey ?>')" title="<?= ucfirst($eKey) ?>" style="background: none; border: none; font-size: 1.15rem; cursor: pointer; padding: 0.15rem 0.35rem; transition: transform 0.15s ease;" onmouseover="this.style.transform='scale(1.35)'" onmouseout="this.style.transform='scale(1)'">
                        <?= $eSymbol ?>
                      </button>
                    <?php endforeach; ?>
                  </div>
                </div>

                <!-- Helpful / Upvote Button -->
                <button type="button" onclick="upvoteComment(<?= $commentId ?>)" id="upvote-badge-<?= $commentId ?>" data-upvoted="<?= $isUpvoted ? '1' : '0' ?>" style="background: none; border: none; font-weight: 600; color: <?= $isUpvoted ? 'var(--ts-success)' : 'var(--ts-text-secondary)' ?>; cursor: pointer; padding: 0; font-size: 0.74rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                  <i data-lucide="thumbs-up" style="width: 11px; height: 11px;"></i> Helpful (<span id="upvote-count-<?= $commentId ?>"><?= $upvotes ?></span>)
                </button>

                <!-- Reply Button -->
                <?php if ($currentUser): ?>
                  <button type="button" onclick="toggleReplyBox(<?= $commentId ?>, '<?= htmlspecialchars($c['username']) ?>')" style="background: none; border: none; font-weight: 600; color: var(--ts-text-secondary); cursor: pointer; padding: 0; font-size: 0.74rem;">
                    Reply
                  </button>
                <?php endif; ?>

                <?php if ($canPin): ?>
                  <!-- Pin/Unpin Button [TS-SOC-07] -->
                  <button type="button" onclick="togglePinComment(<?= $commentId ?>)" id="pin-btn-<?= $commentId ?>" style="background: none; border: none; font-weight: 600; color: <?= $isPinned ? '#eab308' : 'var(--ts-text-secondary)' ?>; cursor: pointer; padding: 0; font-size: 0.74rem;">
                    <?= $isPinned ? '⭐ Unpin' : '📌 Pin Insight' ?>
                  </button>
                <?php endif; ?>

                <?php if ($hasReplies): ?>
                  <button type="button" onclick="toggleCollapseThread(<?= $commentId ?>)" id="collapse-btn-<?= $commentId ?>" style="background: none; border: none; color: var(--ts-text-tertiary); cursor: pointer; padding: 0; font-size: 0.74rem;">
                    <?= $replyCount ?> <?= $replyCount === 1 ? 'reply' : 'replies' ?>
                  </button>
                <?php endif; ?>

                <!-- Report Action -->
                <button type="button" onclick="openReportModal('comment', <?= $commentId ?>, 'Comment by @<?= htmlspecialchars($c['username']) ?>', window.location.href)" style="background: none; border: none; color: var(--ts-text-tertiary); cursor: pointer; padding: 0; font-size: 0.74rem; margin-left: auto;" title="Report comment">
                  Report
                </button>

                <!-- Delete Action -->
                <?php if ($currentUser && (\App\Core\Auth::isAdmin() || (int)$c['user_id'] === (int)$currentUser['id'])): ?>
                  <button type="button" onclick="deleteComment(<?= $commentId ?>)" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 0; font-size: 0.74rem;">
                    Delete
                  </button>
                <?php endif; ?>
              </div>

              <!-- Inline Nested Reply Composer Box -->
              <div id="reply-box-<?= $commentId ?>" class="comment-reply-box" style="display: none; margin-top: 0.55rem; padding: 0.65rem 0.85rem; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-subtle); border-radius: 12px; position: relative;">
                <form onsubmit="submitComment(event, <?= $contentId ?>, <?= $commentId ?>, 'reply-text-<?= $commentId ?>')">
                  <div style="position: relative;">
                    <textarea id="reply-text-<?= $commentId ?>" rows="2" onkeyup="handleMentionLookup(event, 'reply-text-<?= $commentId ?>', 'reply-mention-dropdown-<?= $commentId ?>')" placeholder="Write a reply to @<?= htmlspecialchars($c['username']) ?>..." style="width: 100%; border-radius: 8px; padding: 0.45rem 0.75rem; font-size: 0.85rem; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main); color: var(--ts-text-primary); resize: vertical;" required></textarea>
                    <div id="reply-mention-dropdown-<?= $commentId ?>" class="ts-mention-dropdown" style="display: none; position: absolute; bottom: 100%; left: 0; width: 220px; max-height: 180px; overflow-y: auto; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 8px; box-shadow: 0 6px 18px rgba(0,0,0,0.3); z-index: 100;"></div>
                  </div>
                  <div style="display: flex; justify-content: flex-end; gap: 0.4rem; margin-top: 0.4rem;">
                    <button type="button" onclick="toggleReplyBox(<?= $commentId ?>)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.15rem 0.55rem;">Cancel</button>
                    <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="font-size: 0.72rem; padding: 0.15rem 0.75rem;">Post Reply 💬</button>
                  </div>
                </form>
              </div>

              <!-- Nested Sub-Replies Thread Container -->
              <div id="replies-container-<?= $commentId ?>" class="replies-sub-tree" style="margin-top: 0.45rem;">
                <?php if ($hasReplies): ?>
                  <div id="comment-collapsible-body-<?= $commentId ?>">
                    <?php foreach ($c['replies'] as $subReply): ?>
                      <?php renderRedditCommentNode($subReply, $contentId, $contentAuthorId, $currentUser, $depth + 1, $userUpvotedCommentIds, $userCommentReactions); ?>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
        <?php
    }
}
?>

<section class="comments-section" style="margin-top: 2.5rem; border-top: 1px solid var(--ts-border-main); padding-top: 1.75rem;">
  <!-- Section Header with Sorting -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
    <div>
      <h3 style="font-size: 1.25rem; color: var(--ts-text-primary); margin: 0 0 0.15rem 0; display: flex; align-items: center; gap: 0.5rem; font-family: 'Outfit', sans-serif;">
        <i data-lucide="message-circle" style="color: var(--ts-primary); width: 20px; height: 20px;"></i> Discussion &amp; Feedback
        <span class="ts-badge ts-badge-cyan" id="total-comments-count" style="font-size: 0.75rem;"><?= $totalComments ?></span>
      </h3>
      <p style="font-size: 0.82rem; color: var(--ts-text-tertiary); margin: 0;">Compact social discussion feed. Join the conversation below.</p>
    </div>

    <!-- Sorting Controls -->
    <div class="comment-sort-bar" style="display: flex; gap: 0.3rem; padding: 0.2rem; border-radius: 6px;">
      <button type="button" onclick="sortComments('top')" id="sort-btn-top" class="ts-btn ts-btn-sm ts-btn-primary" style="font-size: 0.72rem; padding: 0.2rem 0.55rem; border-radius: 4px;">
        🔥 Top
      </button>
      <button type="button" onclick="sortComments('newest')" id="sort-btn-newest" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.72rem; padding: 0.2rem 0.55rem; border-radius: 4px;">
        ⚡ Newest
      </button>
      <button type="button" onclick="sortComments('oldest')" id="sort-btn-oldest" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.72rem; padding: 0.2rem 0.55rem; border-radius: 4px;">
        ⏳ Oldest
      </button>
    </div>
  </div>

  <!-- Primary Comment Composer Box -->
  <div class="ts-card comment-composer-card" style="margin-bottom: 1.75rem; padding: 1rem 1.25rem; border-radius: 14px; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main);">
    <?php if ($currentUser): ?>
      <div style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 0.75rem;">
        <img src="<?= htmlspecialchars(image_url($currentUser['author_avatar'] ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80')) ?>" alt="<?= htmlspecialchars($currentUser['username']) ?>" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1px solid var(--ts-primary);">
        <div>
          <div style="font-weight: 600; color: var(--ts-text-primary); font-size: 0.88rem;">
            <?= htmlspecialchars($currentUser['display_name'] ?? $currentUser['username']) ?>
            <span class="ts-badge ts-badge-cyan" style="font-size: 0.62rem; padding: 0.05rem 0.35rem;"><?= htmlspecialchars($currentUser['role']) ?></span>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <form id="main-comment-form" onsubmit="submitComment(event, <?= $contentId ?>, null, 'main-comment-text')">
      <!-- Anti-Spam Notice [TS-SOC-03] -->
      <div style="font-size: 0.74rem; color: var(--ts-text-muted); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.35rem;">
        <i data-lucide="shield-alert" style="width: 13px; height: 13px; color: #f59e0b;"></i>
        <span><strong>Spam Protection:</strong> No links or email addresses are permitted in comments. Type <code>@</code> to mention members.</span>
      </div>

      <div style="position: relative;">
        <textarea id="main-comment-text" name="comment_text" maxlength="1000" rows="2" onkeyup="handleMentionLookup(event, 'main-comment-text', 'main-mention-dropdown')" placeholder="<?= $currentUser ? 'Write a thoughtful comment or insight (Type @ to tag someone)...' : 'Sign in to join the conversation...' ?>" style="width: 100%; border-radius: 12px; padding: 0.75rem 1rem; font-size: 0.9rem; line-height: 1.45; resize: vertical; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); color: var(--ts-text-primary);" required <?= $currentUser ? '' : 'disabled' ?>></textarea>
        <!-- @Username Mention Autocomplete Dropdown [TS-SOC-01] -->
        <div id="main-mention-dropdown" class="ts-mention-dropdown" style="display: none; position: absolute; bottom: 100%; left: 0; width: 240px; max-height: 190px; overflow-y: auto; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 8px; box-shadow: 0 6px 20px rgba(0,0,0,0.3); z-index: 100;"></div>
      </div>

      <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.6rem; flex-wrap: wrap; gap: 0.5rem;">
        <!-- Formatting Ribbon -->
        <div style="display: flex; gap: 0.25rem;">
          <button type="button" onclick="insertCommentFormat('bold', 'main-comment-text')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.72rem; font-weight: 800;" title="Bold">B</button>
          <button type="button" onclick="insertCommentFormat('italic', 'main-comment-text')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.72rem; font-style: italic;" title="Italic">I</button>
          <button type="button" onclick="insertCommentFormat('code', 'main-comment-text')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.72rem; font-family: monospace;" title="Code">&lt;/&gt;</button>
        </div>

        <?php if ($currentUser): ?>
          <button type="submit" id="main-submit-btn" class="ts-btn ts-btn-primary ts-btn-sm" style="padding: 0.35rem 1.15rem; font-size: 0.85rem; border-radius: 20px;">
            <i data-lucide="send" style="width: 13px;"></i> Post Comment
          </button>
        <?php else: ?>
          <a href="<?= url('/auth/login') ?>" class="ts-btn ts-btn-primary ts-btn-sm" style="padding: 0.35rem 1rem; font-size: 0.85rem; border-radius: 20px;">
            <i data-lucide="log-in" style="width: 13px;"></i> Sign in to Comment
          </a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- Comments Live Stream Thread Tree (Lazy Loaded 5 at a time) -->
  <div id="comments-stream-container" style="display: flex; flex-direction: column;">
    <?php if (!empty($comments)): ?>
      <?php foreach ($comments as $index => $c): ?>
        <div class="root-comment-wrapper" style="<?= $index >= 5 ? 'display:none;' : '' ?>">
          <?php renderRedditCommentNode($c, $contentId, $contentAuthorId, $currentUser, 0, $userUpvotedCommentIds, $userCommentReactions); ?>
        </div>
      <?php endforeach; ?>

      <?php if (count($comments) > 5): ?>
        <div id="load-more-comments-wrap" style="text-align: center; margin-top: 1.25rem;">
          <button type="button" onclick="loadMoreRootComments()" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.55rem 1.75rem; border-radius: 20px; font-weight: 600;">
            <i data-lucide="chevrons-down" style="width: 15px; display: inline;"></i> Load More Comments (<span id="remaining-comments-count"><?= count($comments) - 5 ?></span> remaining)
          </button>
        </div>
      <?php endif; ?>
    <?php else: ?>
      <div id="no-comments-prompt" class="ts-card" style="text-align: center; padding: 2.5rem; color: var(--ts-text-tertiary); background: var(--ts-bg-surface); border: 1px dashed var(--ts-border-main); border-radius: 12px;">
        <i data-lucide="message-circle" style="width: 32px; height: 32px; color: var(--ts-primary); margin-bottom: 0.6rem;"></i>
        <h4 style="font-size: 1.05rem; color: var(--ts-text-primary); margin-bottom: 0.2rem;">No comments yet</h4>
        <p style="font-size: 0.85rem; margin: 0;">Be the first to share your thoughts!</p>
      </div>
    <?php endif; ?>
  </div>
</section>

<script>
function toggleCollapseThread(commentId) {
  const body = document.getElementById('comment-collapsible-body-' + commentId);
  const btn = document.getElementById('collapse-btn-' + commentId);
  if (!body || !btn) return;
  const isCollapsed = body.style.display === 'none';
  body.style.display = isCollapsed ? 'block' : 'none';
  if (isCollapsed) {
    btn.innerText = btn.getAttribute('data-original-text') || 'Collapse replies';
  } else {
    if (!btn.getAttribute('data-original-text')) btn.setAttribute('data-original-text', btn.innerText);
    btn.innerText = 'Show replies';
  }
}

function loadMoreRootComments() {
  const hiddenRoots = document.querySelectorAll('.root-comment-wrapper[style*="display:none"], .root-comment-wrapper[style*="display: none"]');
  let shown = 0;
  hiddenRoots.forEach(node => {
    if (shown < 5) {
      node.style.display = 'block';
      shown++;
    }
  });
  
  const remaining = hiddenRoots.length - shown;
  const countSpan = document.getElementById('remaining-comments-count');
  if (countSpan) countSpan.innerText = remaining;

  if (remaining <= 0) {
    const wrap = document.getElementById('load-more-comments-wrap');
    if (wrap) wrap.style.display = 'none';
  }

  if (window.lucide) lucide.createIcons();
  if (window.localizeAllTimestamps) window.localizeAllTimestamps();
}

function insertCommentFormat(type, textareaId) {
  const el = document.getElementById(textareaId);
  if (!el) return;
  const start = el.selectionStart;
  const end = el.selectionEnd;
  const selected = el.value.substring(start, end);
  let replacement = '';

  switch (type) {
    case 'bold':
      replacement = `**${selected || 'bold text'}**`;
      break;
    case 'italic':
      replacement = `*${selected || 'italic text'}*`;
      break;
    case 'code':
      replacement = `\`${selected || 'code'}\``;
      break;
  }

  el.value = el.value.substring(0, start) + replacement + el.value.substring(end);
  el.focus();
}

function getCommentsApiBase() {
  if (window.TS_BASE_URL && window.TS_BASE_URL.startsWith('http')) {
    try {
      const u = new URL(window.TS_BASE_URL);
      if (u.hostname !== window.location.hostname || u.port !== window.location.port) {
        return u.pathname.replace(/\/$/, '');
      }
    } catch(e) {}
    return window.TS_BASE_URL.replace(/\/$/, '');
  }
  return window.location.pathname.startsWith('/techscout.n') ? '/techscout.n' : '';
}

function toggleReplyBox(commentId, username = '') {
  const box = document.getElementById('reply-box-' + commentId);
  if (!box) return;
  const isHidden = box.style.display === 'none' || !box.style.display;
  box.style.display = isHidden ? 'block' : 'none';
  if (isHidden && username) {
    const txt = document.getElementById('reply-text-' + commentId);
    if (txt && !txt.value) {
      txt.value = `@${username} `;
      txt.focus();
    }
  }
}

async function submitComment(e, contentId, parentId, textareaId) {
  e.preventDefault();
  const textarea = document.getElementById(textareaId);
  const text = textarea ? textarea.value.trim() : '';

  if (text.length < 3) {
    if (typeof showToast === 'function') showToast('Comment must be at least 3 characters.', 'warning');
    return;
  }

  try {
    const baseUrl = getCommentsApiBase();
    const res = await fetch(`${baseUrl}/api/v1/content/${contentId}/comments`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ comment_text: text, parent_id: parentId })
    });

    const data = await res.json();
    if (data.error) {
      if (typeof showToast === 'function') showToast(data.error, 'error');
      return;
    }

    if (typeof showToast === 'function') showToast('Comment posted! 🚀', 'success');
    textarea.value = '';

    if (parentId) {
      toggleReplyBox(parentId);
      const replyContainer = document.getElementById('replies-container-' + parentId);
      if (replyContainer) {
        const replyId = data.comment.id;
        const replyHtml = `
          <div class="comment-node-wrapper comment-reply-node" id="comment-node-${replyId}" data-upvotes="0" data-timestamp="${Math.floor(Date.now()/1000)}" style="margin-left: 1.5rem; border-left: 2px solid var(--ts-border-subtle); padding-left: 0.85rem; margin-top: 0.5rem;">
            <div style="display: flex; gap: 0.65rem; align-items: flex-start;">
              <img src="${data.comment.avatar_url || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80'}" alt="${escapeHtml(data.comment.username)}" style="width: 26px; height: 26px; border-radius: 50%; object-fit: cover; border: 1px solid var(--ts-border-main); flex-shrink: 0; margin-top: 2px;">
              <div style="flex: 1; max-width: calc(100% - 38px);">
                <div class="ts-comment-bubble" style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-subtle); border-radius: 16px; padding: 0.55rem 0.95rem; display: inline-block; max-width: 100%;">
                  <div style="font-weight: 700; font-size: 0.85rem; color: var(--ts-text-primary); margin-bottom: 0.15rem;">${escapeHtml(data.comment.display_name || data.comment.username)}</div>
                  <div class="comment-body-text" style="font-size: 0.88rem; line-height: 1.5; color: var(--ts-text-primary); word-break: break-word; white-space: pre-wrap;">${escapeHtml(data.comment.comment_text)}</div>
                </div>
                <div style="display: flex; align-items: center; gap: 0.85rem; font-size: 0.74rem; color: var(--ts-text-tertiary); margin-top: 0.25rem; padding-left: 0.5rem;">
                  <span>Just now</span>
                  <button type="button" onclick="upvoteComment(${replyId})" id="upvote-badge-${replyId}" style="background: none; border: none; font-weight: 600; color: var(--ts-text-secondary); cursor: pointer; padding: 0; font-size: 0.74rem;">
                    Helpful (<span id="upvote-count-${replyId}">0</span>)
                  </button>
                </div>
              </div>
            </div>
          </div>
        `;
        replyContainer.insertAdjacentHTML('beforeend', replyHtml);
        if (typeof lucide !== 'undefined') lucide.createIcons();
      }
    } else {
      setTimeout(() => location.reload(), 500);
    }
  } catch (err) {
    if (typeof showToast === 'function') showToast('Error posting comment. Please try again.', 'error');
  }
}

async function upvoteComment(commentId) {
  try {
    const baseUrl = getCommentsApiBase();
    const res = await fetch(`${baseUrl}/api/v1/comments/${commentId}/upvote`, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    const data = await res.json();
    if (data && data.success) {
      const countEl = document.getElementById('upvote-count-' + commentId);
      if (countEl) countEl.innerText = data.upvotes;
      const badgeEl = document.getElementById('upvote-badge-' + commentId);
      if (badgeEl) {
        if (data.upvoted) {
          badgeEl.style.color = 'var(--ts-success)';
          badgeEl.setAttribute('data-upvoted', '1');
        } else {
          badgeEl.style.color = 'var(--ts-text-secondary)';
          badgeEl.setAttribute('data-upvoted', '0');
        }
      }
      if (typeof showToast === 'function') {
        showToast(data.upvoted ? 'Marked helpful! 💡' : 'Vote removed.', 'info');
      }
    }
  } catch (err) {}
}

async function deleteComment(commentId) {
  if (!confirm('Permanently delete this comment?')) return;
  try {
    const baseUrl = getCommentsApiBase();
    const res = await fetch(`${baseUrl}/api/v1/comments/${commentId}/delete`, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    if (data.success) {
      const node = document.getElementById('comment-node-' + commentId);
      if (node) node.remove();
      if (typeof showToast === 'function') showToast('Comment deleted', 'success');
    }
  } catch (err) {}
}

function sortComments(type) {
  const container = document.getElementById('comments-stream-container');
  if (!container) return;
  const nodes = Array.from(container.children).filter(n => n.classList && (n.classList.contains('comment-item') || n.classList.contains('root-comment-wrapper')));

  document.querySelectorAll('[id^="sort-btn-"]').forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });
  const activeBtn = document.getElementById('sort-btn-' + type);
  if (activeBtn) {
    activeBtn.classList.remove('ts-btn-outline');
    activeBtn.classList.add('ts-btn-primary');
  }

  if (type === 'top') {
    nodes.sort((a, b) => {
      const itemA = a.querySelector('.comment-item') || a;
      const itemB = b.querySelector('.comment-item') || b;
      return parseInt(itemB.getAttribute('data-upvotes') || 0) - parseInt(itemA.getAttribute('data-upvotes') || 0);
    });
  } else if (type === 'newest') {
    nodes.sort((a, b) => {
      const itemA = a.querySelector('.comment-item') || a;
      const itemB = b.querySelector('.comment-item') || b;
      return parseInt(itemB.getAttribute('data-timestamp') || 0) - parseInt(itemA.getAttribute('data-timestamp') || 0);
    });
  } else if (type === 'oldest') {
    nodes.sort((a, b) => {
      const itemA = a.querySelector('.comment-item') || a;
      const itemB = b.querySelector('.comment-item') || b;
      return parseInt(itemA.getAttribute('data-timestamp') || 0) - parseInt(itemB.getAttribute('data-timestamp') || 0);
    });
  }

  nodes.forEach(n => container.appendChild(n));
}

// [TS-SOC-04] Multi-Emoji Reaction Handlers
function toggleEmojiBar(commentId) {
  const bar = document.getElementById('emoji-bar-' + commentId);
  if (!bar) return;
  const isHidden = bar.style.display === 'none' || !bar.style.display;
  // Close any other open emoji bars
  document.querySelectorAll('.emoji-reaction-bar').forEach(b => b.style.display = 'none');
  bar.style.display = isHidden ? 'flex' : 'none';
}

async function sendEmojiReaction(commentId, emojiKey) {
  try {
    const baseUrl = getCommentsApiBase();
    const res = await fetch(`${baseUrl}/api/v1/comments/${commentId}/react-emoji`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ emoji: emojiKey })
    });
    const data = await res.json();
    if (data && data.success) {
      const reactions = data.reactions || {};
      const userReaction = data.user_reaction || null;
      const container = document.getElementById('reactions-display-' + commentId);
      if (container) {
        const emojiMap = { heart: '❤️', rocket: '🚀', lightbulb: '💡', fire: '🔥', eyes: '👀' };
        let html = '';
        for (const [k, sym] of Object.entries(emojiMap)) {
          const cnt = reactions[k] || 0;
          if (cnt > 0) {
            const isActive = (userReaction === k);
            const activeBg = isActive ? 'rgba(99,102,241,0.22)' : 'var(--ts-bg-surface)';
            const activeBorder = isActive ? 'var(--ts-primary)' : 'var(--ts-border-subtle)';
            const activeColor = isActive ? '#ffffff' : 'inherit';
            const activeWeight = isActive ? '700' : '400';
            html += `<span class="reaction-pill-badge ${isActive ? 'active-emoji-pill' : ''}" id="pill-${commentId}-${k}" style="font-size: 0.72rem; padding: 0.12rem 0.45rem; border-radius: 12px; background: ${activeBg}; border: 1px solid ${activeBorder}; color: ${activeColor}; display: inline-flex; align-items: center; gap: 0.25rem; cursor: pointer; transition: all 0.15s ease;" onclick="sendEmojiReaction(${commentId}, '${k}')" title="${isActive ? 'Click to remove reaction' : 'Click to react'}">${sym} <span class="cnt" style="font-weight: ${activeWeight};">${cnt}</span></span>`;
          }
        }
        container.innerHTML = html;
      }
      const bar = document.getElementById('emoji-bar-' + commentId);
      if (bar) bar.style.display = 'none';
      if (typeof showToast === 'function') {
        if (data.action === 'removed') {
          showToast('Reaction removed', 'info');
        } else if (data.action === 'switched') {
          showToast(`Switched reaction to ${emojiKey}! ⚡`, 'success');
        } else {
          showToast(`Reacted with ${emojiKey}! 🚀`, 'success');
        }
      }
    } else if (data && data.error) {
      if (typeof showToast === 'function') showToast(data.error, 'warning');
    }
  } catch (err) {
    console.error(err);
  }
}

// [TS-SOC-07] Pin "Best Answer" / Top Insight by Author or Admin
async function togglePinComment(commentId) {
  try {
    const baseUrl = getCommentsApiBase();
    const res = await fetch(`${baseUrl}/api/v1/comments/${commentId}/pin`, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    if (data && data.success) {
      if (typeof showToast === 'function') {
        showToast(data.is_pinned ? 'Insight pinned to top! ⭐' : 'Insight unpinned.', 'success');
      }
      setTimeout(() => location.reload(), 600);
    } else if (data.error) {
      if (typeof showToast === 'function') showToast(data.error, 'error');
    }
  } catch (err) {
    console.error(err);
  }
}

// [TS-SOC-01] @Username Autocomplete Mentions
let mentionDebounceTimer = null;
function handleMentionLookup(e, textareaId, dropdownId) {
  const textarea = document.getElementById(textareaId);
  const dropdown = document.getElementById(dropdownId);
  if (!textarea || !dropdown) return;

  const text = textarea.value.substring(0, textarea.selectionStart);
  const match = text.match(/@([a-zA-Z0-9_-]{1,20})$/);

  if (!match) {
    dropdown.style.display = 'none';
    return;
  }

  const query = match[1];
  clearTimeout(mentionDebounceTimer);
  mentionDebounceTimer = setTimeout(async () => {
    try {
      const baseUrl = getCommentsApiBase();
      const res = await fetch(`${baseUrl}/api/v1/users/mentions?q=${encodeURIComponent(query)}`);
      const users = await res.json();
      if (!Array.isArray(users) || users.length === 0) {
        dropdown.style.display = 'none';
        return;
      }

      dropdown.innerHTML = users.map(u => `
        <div onclick="selectMention('${escapeHtml(u.username)}', '${textareaId}', '${dropdownId}')" style="padding: 0.45rem 0.75rem; display: flex; align-items: center; gap: 0.5rem; cursor: pointer; border-bottom: 1px solid var(--ts-border-subtle); transition: background 0.15s ease;" onmouseover="this.style.background='var(--ts-bg-surface)'" onmouseout="this.style.background='transparent'">
          <img src="${u.author_avatar || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=60&auto=format&fit=crop&q=80'}" style="width: 20px; height: 20px; border-radius: 50%; object-fit: cover;">
          <span style="font-weight: 600; font-size: 0.82rem; color: var(--ts-text-primary);">@${escapeHtml(u.username)}</span>
          <span style="font-size: 0.72rem; color: var(--ts-text-muted); margin-left: auto;">${escapeHtml(u.display_name || '')}</span>
        </div>
      `).join('');
      dropdown.style.display = 'block';
    } catch (err) {
      dropdown.style.display = 'none';
    }
  }, 250);
}

function selectMention(username, textareaId, dropdownId) {
  const textarea = document.getElementById(textareaId);
  const dropdown = document.getElementById(dropdownId);
  if (!textarea) return;
  const text = textarea.value;
  const cursor = textarea.selectionStart;
  const textBefore = text.substring(0, cursor).replace(/@([a-zA-Z0-9_-]+)$/, `@${username} `);
  textarea.value = textBefore + text.substring(cursor);
  if (dropdown) dropdown.style.display = 'none';
  textarea.focus();
}

// [TS-SOC-06] Long Polling Stream (60-120 second gentle interval)
let lastPolledCommentTimestamp = Math.floor(Date.now() / 1000);
function startCommentLongPolling(contentId) {
  if (!contentId) return;
  // 60-second gentle interval to prevent spam and false requests
  setInterval(async () => {
    try {
      const baseUrl = getCommentsApiBase();
      const res = await fetch(`${baseUrl}/api/v1/content/${contentId}/comments/poll?since=${lastPolledCommentTimestamp}`);
      if (!res.ok) return;
      const data = await res.json();
      if (data && data.has_new && Array.isArray(data.comments) && data.comments.length > 0) {
        lastPolledCommentTimestamp = Math.floor(Date.now() / 1000);
        const container = document.getElementById('comments-stream-container');
        const emptyPrompt = document.getElementById('no-comments-prompt');
        if (emptyPrompt) emptyPrompt.remove();

        data.comments.forEach(c => {
          if (document.getElementById('comment-node-' + c.id)) return; // Avoid duplicate
          const newNode = document.createElement('div');
          newNode.className = 'root-comment-wrapper';
          newNode.style.opacity = '0';
          newNode.style.transform = 'translateY(-12px)';
          newNode.style.transition = 'all 0.5s ease';
          newNode.innerHTML = `
            <div class="comment-node-wrapper comment-item" id="comment-node-${c.id}" data-upvotes="0" data-timestamp="${Math.floor(Date.now()/1000)}" style="margin-bottom: 0.95rem;">
              <div style="display: flex; gap: 0.65rem; align-items: flex-start;">
                <img src="${c.avatar_url || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80'}" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 1px solid var(--ts-border-main); flex-shrink: 0; margin-top: 2px;">
                <div style="flex: 1;">
                  <div class="ts-comment-bubble" style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-subtle); border-radius: 16px; padding: 0.6rem 1rem; display: inline-block;">
                    <div style="font-weight: 700; font-size: 0.85rem; color: var(--ts-text-primary);">${escapeHtml(c.display_name || c.username)}</div>
                    <div class="comment-body-text" style="font-size: 0.88rem; line-height: 1.5; color: var(--ts-text-primary); word-break: break-word;">${escapeHtml(c.comment_text)}</div>
                  </div>
                  <div style="font-size: 0.74rem; color: var(--ts-text-tertiary); margin-top: 0.3rem; padding-left: 0.5rem;">
                    <span class="ts-time" data-timestamp="${Math.floor(Date.now()/1000)}">Just now</span>
                  </div>
                </div>
              </div>
            </div>
          `;
          if (container.firstChild) {
            container.insertBefore(newNode, container.firstChild);
          } else {
            container.appendChild(newNode);
          }
          setTimeout(() => {
            newNode.style.opacity = '1';
            newNode.style.transform = 'translateY(0)';
          }, 50);
        });

        // Update count badge
        const badge = document.getElementById('total-comments-count');
        if (badge) {
          const currentCount = parseInt(badge.textContent) || 0;
          badge.textContent = currentCount + data.comments.length;
        }
      }
    } catch (e) {
      // Gentle catch, no console spam
    }
  }, 60000); // 60-second timer
}

document.addEventListener('DOMContentLoaded', () => {
  startCommentLongPolling(<?= $contentId ?>);
});
</script>

