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
$totalComments = count($comments);
$currentUser = \App\Core\Auth::user();

// Query current user / IP upvoted comments for toggle state tracking
$userUpvotedCommentIds = [];
try {
    $currentUserId = \App\Core\Auth::id();
    $clientIp = \App\Core\Request::ip();
    if ($currentUserId) {
        $rows = \App\Core\Database::query(
            "SELECT comment_id FROM comment_reactions WHERE user_id = ?",
            [$currentUserId]
        );
    } else {
        $rows = \App\Core\Database::query(
            "SELECT comment_id FROM comment_reactions WHERE user_id IS NULL AND ip_address = ?",
            [$clientIp]
        );
    }
    $userUpvotedCommentIds = array_map('intval', array_column($rows, 'comment_id'));
} catch (\Throwable $e) {
    $userUpvotedCommentIds = [];
}

if (!function_exists('renderRedditCommentNode')) {
    function renderRedditCommentNode(array $c, int $contentId, int $contentAuthorId, ?array $currentUser, int $depth = 0, array $userUpvotedCommentIds = []): void {
        $isAuthorComment = ($contentAuthorId && (int)$c['user_id'] === $contentAuthorId);
        $upvotes = (int)($c['reaction_count'] ?? 0);
        $commentId = (int)$c['id'];
        $hasReplies = !empty($c['replies']);
        $replyCount = $hasReplies ? count($c['replies']) : 0;
        $maxDepthIndent = min($depth * 1.5, 4.5);
        $isUpvoted = in_array($commentId, $userUpvotedCommentIds, true);
        $avatarSize = $depth === 0 ? 34 : 26;
        ?>
        <div class="comment-node-wrapper <?= $depth === 0 ? 'comment-item' : 'comment-reply-node' ?>" 
             id="comment-node-<?= $commentId ?>" 
             data-upvotes="<?= $upvotes ?>" 
             data-timestamp="<?= strtotime($c['created_at']) ?>"
             style="<?= $depth > 0 ? "margin-left: {$maxDepthIndent}rem; border-left: 2px solid var(--ts-border-subtle); padding-left: 0.85rem; margin-top: 0.5rem;" : "margin-bottom: 0.85rem;" ?>">
          
          <div style="display: flex; gap: 0.65rem; align-items: flex-start;">
            <!-- Social Avatar -->
            <img src="<?= htmlspecialchars(image_url($c['avatar_url'] ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80')) ?>" 
                 alt="<?= htmlspecialchars($c['username']) ?>" 
                 style="width: <?= $avatarSize ?>px; height: <?= $avatarSize ?>px; border-radius: 50%; object-fit: cover; border: 1px solid var(--ts-border-main); flex-shrink: 0; margin-top: 2px;">

            <div style="flex: 1; max-width: calc(100% - <?= $avatarSize + 12 ?>px);">
              <!-- Compact Social Speech Bubble (Instagram / FB Style) -->
              <div class="ts-comment-bubble" style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-subtle); border-radius: 16px; padding: 0.55rem 0.95rem; display: inline-block; max-width: 100%;">
                <div style="display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.15rem; flex-wrap: wrap;">
                  <span style="font-weight: 700; font-size: 0.85rem; color: var(--ts-text-primary);">
                    <?= htmlspecialchars($c['display_name'] ?? $c['username']) ?>
                  </span>

                  <?php if ($isAuthorComment): ?>
                    <span class="ts-badge ts-badge-pink" style="font-size: 0.6rem; padding: 0.05rem 0.35rem;">Author ✍️</span>
                  <?php elseif (($c['role'] ?? '') === 'super_admin' || ($c['role'] ?? '') === 'admin'): ?>
                    <span class="ts-badge ts-badge-cyan" style="font-size: 0.6rem; padding: 0.05rem 0.35rem;">Staff 🛡️</span>
                  <?php endif; ?>

                  <?php if (!empty($c['is_pinned'])): ?>
                    <span class="ts-badge ts-badge-green" style="font-size: 0.6rem; padding: 0.05rem 0.35rem;">Pinned ⭐</span>
                  <?php endif; ?>
                </div>

                <div class="comment-body-text" style="font-size: 0.88rem; line-height: 1.5; color: var(--ts-text-primary); word-break: break-word; white-space: pre-wrap;"><?= htmlspecialchars($c['comment_text']) ?></div>
              </div>

              <!-- Compact Social Action Row Underneath Bubble -->
              <div style="display: flex; align-items: center; gap: 0.85rem; font-size: 0.74rem; color: var(--ts-text-tertiary); margin-top: 0.25rem; padding-left: 0.5rem; flex-wrap: wrap;">
                <span><?= \App\Core\DateHelper::render($c['created_at'], 'relative') ?></span>

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
              <div id="reply-box-<?= $commentId ?>" class="comment-reply-box" style="display: none; margin-top: 0.55rem; padding: 0.65rem 0.85rem; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-subtle); border-radius: 12px;">
                <form onsubmit="submitComment(event, <?= $contentId ?>, <?= $commentId ?>, 'reply-text-<?= $commentId ?>')">
                  <textarea id="reply-text-<?= $commentId ?>" rows="2" placeholder="Write a reply to @<?= htmlspecialchars($c['username']) ?>..." style="width: 100%; border-radius: 8px; padding: 0.45rem 0.75rem; font-size: 0.85rem; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main); color: var(--ts-text-primary); resize: vertical;" required></textarea>
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
                      <?php renderRedditCommentNode($subReply, $contentId, $contentAuthorId, $currentUser, $depth + 1, $userUpvotedCommentIds); ?>
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
      <div style="position: relative;">
        <textarea id="main-comment-text" name="comment_text" maxlength="1000" rows="2" placeholder="<?= $currentUser ? 'Write a thoughtful comment or insight...' : 'Sign in to join the conversation...' ?>" style="width: 100%; border-radius: 12px; padding: 0.75rem 1rem; font-size: 0.9rem; line-height: 1.45; resize: vertical; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); color: var(--ts-text-primary);" required <?= $currentUser ? '' : 'disabled' ?>></textarea>
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
          <?php renderRedditCommentNode($c, $contentId, $contentAuthorId, $currentUser, 0, $userUpvotedCommentIds); ?>
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
</script>
