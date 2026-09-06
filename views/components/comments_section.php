<?php
/**
 * Reddit-Grade Multi-Level Nested Community Discussion & Reply Tree Component
 * 
 * @var array $comments
 * @var array $content (or $article / $tool / $gpt)
 */

$contentItem = $article ?? $tool ?? $gpt ?? $prompt ?? $campaign ?? [];
$contentId = (int)($contentItem['id'] ?? 0);
$contentAuthorId = (int)($contentItem['author_id'] ?? 0);
$totalComments = count($comments);
$currentUser = \App\Core\Auth::user();

if (!function_exists('renderRedditCommentNode')) {
    function renderRedditCommentNode(array $c, int $contentId, int $contentAuthorId, ?array $currentUser, int $depth = 0): void {
        $isAuthorComment = ($contentAuthorId && (int)$c['user_id'] === $contentAuthorId);
        $upvotes = (int)($c['reaction_count'] ?? 0);
        $commentId = (int)$c['id'];
        $hasReplies = !empty($c['replies']);
        $replyCount = $hasReplies ? count($c['replies']) : 0;
        $maxDepthIndent = min($depth * 0.85, 3.5); // compact visual boundary
        ?>
        <div class="comment-node-wrapper <?= $depth === 0 ? 'comment-item' : 'comment-reply-node' ?>" 
             id="comment-node-<?= $commentId ?>" 
             data-upvotes="<?= $upvotes ?>" 
             data-timestamp="<?= strtotime($c['created_at']) ?>"
             style="<?= $depth > 0 ? "margin-left: {$maxDepthIndent}rem; border-left: 2px solid rgba(99,102,241,0.35); padding-left: 0.75rem; margin-top: 0.5rem;" : "margin-bottom: 0.75rem;" ?>">
          
          <div class="ts-card comment-card-inner" style="padding: 0.85rem 1.15rem; border-radius: 8px;">
            <!-- Top Header & Collapse Toggle -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.45rem;">
              <div style="display: flex; align-items: center; gap: 0.45rem; flex-wrap: wrap;">
                <!-- Collapse / Expand Thread Button -->
                <button type="button" onclick="toggleCollapseThread(<?= $commentId ?>)" id="collapse-btn-<?= $commentId ?>" style="background: none; border: none; color: #64748b; font-family: var(--ts-font-mono); font-size: 0.75rem; cursor: pointer; padding: 0 0.15rem;" title="Collapse/Expand thread">[-]</button>
                
                <img src="<?= htmlspecialchars(image_url($c['avatar_url'] ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80')) ?>" alt="<?= htmlspecialchars($c['username']) ?>" style="width: 26px; height: 26px; border-radius: 50%; object-fit: cover; border: 1px solid rgba(148,163,184,0.25);">
                
                <span class="comment-author-name" style="font-size: 0.88rem;">
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

              <div style="font-size: 0.72rem; color: #64748b; font-family: var(--ts-font-mono);">
                <?= \App\Core\DateHelper::format($c['created_at']) ?>
              </div>
            </div>

            <!-- Collapsible Body Content -->
            <div id="comment-collapsible-body-<?= $commentId ?>">
              <div class="comment-body-text" style="font-size: 0.92rem; line-height: 1.6; margin-bottom: 0.65rem; white-space: pre-wrap; word-break: break-word;"><?= htmlspecialchars($c['comment_text']) ?></div>

              <!-- Bottom Action Bar (Upvote, Reply, Delete) -->
              <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; border-top: 1px solid rgba(148,163,184,0.1); padding-top: 0.45rem;">
                <button type="button" onclick="upvoteComment(<?= $commentId ?>)" class="ts-badge ts-badge-cyan" id="upvote-badge-<?= $commentId ?>" style="cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.72rem; padding: 0.15rem 0.5rem;">
                  <i data-lucide="thumbs-up" style="width: 11px; height: 11px;"></i> Helpful (<span id="upvote-count-<?= $commentId ?>"><?= $upvotes ?></span>)
                </button>

                <?php if ($currentUser): ?>
                  <button type="button" onclick="toggleReplyBox(<?= $commentId ?>, '<?= htmlspecialchars($c['username']) ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.55rem; font-size: 0.72rem;">
                    <i data-lucide="corner-down-right" style="width: 11px; height: 11px;"></i> Reply
                  </button>
                <?php endif; ?>

                <?php if ($hasReplies): ?>
                  <span style="color: #64748b; font-size: 0.72rem; margin-left: 0.2rem;">
                    <i data-lucide="message-square" style="width: 11px; height: 11px; display: inline;"></i> <?= $replyCount ?> <?= $replyCount === 1 ? 'reply' : 'replies' ?>
                  </span>
                <?php endif; ?>

                <button type="button" onclick="openReportModal('comment', <?= $commentId ?>, 'Comment by @<?= htmlspecialchars($c['username']) ?>', window.location.href)" style="background: none; border: none; color: #64748b; font-size: 0.72rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.2rem; margin-left: auto;" title="Report comment to moderators">
                  <i data-lucide="flag" style="width: 11px; height: 11px; color: #ef4444;"></i> Report
                </button>

                <?php if ($currentUser && (\App\Core\Auth::isAdmin() || (int)$c['user_id'] === (int)$currentUser['id'])): ?>
                  <button type="button" onclick="deleteComment(<?= $commentId ?>)" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.12rem 0.45rem; font-size: 0.7rem; color: #ef4444;">
                    <i data-lucide="trash-2" style="width: 10px; height: 10px;"></i> Delete
                  </button>
                <?php endif; ?>
              </div>

              <!-- Inline Nested Reply Composer Box -->
              <div id="reply-box-<?= $commentId ?>" class="comment-reply-box" style="display: none; margin-top: 0.75rem; padding: 0.75rem 1rem; border-radius: 8px;">
                <form onsubmit="submitComment(event, <?= $contentId ?>, <?= $commentId ?>, 'reply-text-<?= $commentId ?>')">
                  <textarea id="reply-text-<?= $commentId ?>" class="comment-composer-textarea" rows="2" placeholder="Write your reply to @<?= htmlspecialchars($c['username']) ?>..." style="width: 100%; border-radius: 6px; padding: 0.5rem 0.75rem; font-size: 0.88rem; resize: vertical;" required></textarea>
                  <div style="display: flex; justify-content: flex-end; gap: 0.4rem; margin-top: 0.5rem;">
                    <button type="button" onclick="toggleReplyBox(<?= $commentId ?>)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.55rem;">Cancel</button>
                    <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.75rem;">Post Reply 💬</button>
                  </div>
                </form>
              </div>

              <!-- Nested Sub-Replies Thread Container -->
              <div id="replies-container-<?= $commentId ?>" class="replies-sub-tree">
                <?php if ($hasReplies): ?>
                  <?php foreach ($c['replies'] as $subReply): ?>
                    <?php renderRedditCommentNode($subReply, $contentId, $contentAuthorId, $currentUser, $depth + 1); ?>
                  <?php endforeach; ?>
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
      <h3 style="font-size: 1.3rem; color: var(--ts-text-primary); margin: 0 0 0.15rem 0; display: flex; align-items: center; gap: 0.5rem; font-family: 'Outfit', sans-serif;">
        <i data-lucide="message-square" style="color: var(--ts-primary); width: 20px; height: 20px;"></i> Technical Discussion &amp; Insights
        <span class="ts-badge ts-badge-cyan" id="total-comments-count" style="font-size: 0.75rem;"><?= $totalComments ?></span>
      </h3>
      <p style="font-size: 0.82rem; color: var(--ts-text-tertiary); margin: 0;">Recursive threaded comments, peer architecture reviews, and benchmark discussions.</p>
    </div>

    <!-- Sorting Controls -->
    <div class="comment-sort-bar" style="display: flex; gap: 0.3rem; padding: 0.25rem; border-radius: 6px;">
      <button type="button" onclick="sortComments('top')" id="sort-btn-top" class="ts-btn ts-btn-sm ts-btn-primary" style="font-size: 0.72rem; padding: 0.2rem 0.55rem; border-radius: 4px;">
        🔥 Most Helpful
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
  <div class="ts-card comment-composer-card" style="margin-bottom: 1.75rem; padding: 1.25rem; border-radius: 10px;">
    <?php if ($currentUser): ?>
      <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
        <img src="<?= htmlspecialchars(image_url($currentUser['author_avatar'] ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80')) ?>" alt="<?= htmlspecialchars($currentUser['username']) ?>" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 1px solid var(--ts-primary);">
        <div>
          <div style="font-weight: 600; color: var(--ts-text-primary); font-size: 0.9rem;">
            <?= htmlspecialchars($currentUser['display_name'] ?? $currentUser['username']) ?>
            <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem; padding: 0.05rem 0.35rem;"><?= htmlspecialchars($currentUser['role']) ?></span>
          </div>
          <div style="font-size: 0.75rem; color: #64748b;">Post as verified builder</div>
        </div>
      </div>
    <?php endif; ?>

    <!-- Formatting Ribbon for Comments -->
    <div class="comment-formatting-ribbon" style="display: flex; gap: 0.35rem; margin-bottom: 0.5rem; padding: 0.4rem; border-radius: 6px; flex-wrap: wrap;">
      <button type="button" onclick="insertCommentFormat('bold', 'main-comment-text')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.75rem; font-weight: 800;" title="Bold">B</button>
      <button type="button" onclick="insertCommentFormat('italic', 'main-comment-text')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.75rem; font-style: italic;" title="Italic">I</button>
      <button type="button" onclick="insertCommentFormat('code', 'main-comment-text')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.75rem; font-family: monospace;" title="Inline Code">&lt;/&gt;</button>
      <button type="button" onclick="insertCommentFormat('codeblock', 'main-comment-text')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.75rem; font-family: monospace;" title="Code Block">```</button>
      <button type="button" onclick="insertCommentFormat('quote', 'main-comment-text')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;" title="Quote">"</button>
      <span style="font-size: 0.75rem; color: #64748b; margin-left: auto; align-self: center;">Markdown & Code Supported</span>
    </div>

    <form id="main-comment-form" onsubmit="submitComment(event, <?= $contentId ?>, null, 'main-comment-text')">
      <textarea id="main-comment-text" name="comment_text" maxlength="1000" class="comment-composer-textarea" rows="3" placeholder="<?= $currentUser ? 'Share your architectural critique, benchmarks, or question...' : 'Sign in to share your technical feedback or ask a question...' ?>" style="width: 100%; border-radius: 6px; padding: 0.85rem 1rem; font-size: 0.95rem; line-height: 1.5; resize: vertical;" required <?= $currentUser ? '' : 'disabled' ?>></textarea>

      <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.85rem; flex-wrap: wrap; gap: 0.75rem;">
        <div style="font-size: 0.75rem; color: #64748b;">
          💡 Tip: Markdown formatted code blocks, benchmarks, and nested replies supported. (Max 1,000 chars)
        </div>

        <?php if ($currentUser): ?>
          <button type="submit" id="main-submit-btn" class="ts-btn ts-btn-primary" style="padding: 0.5rem 1.25rem; font-size: 0.9rem;">
            <i data-lucide="send" style="width: 14px;"></i> Post Technical Thought
          </button>
        <?php else: ?>
          <a href="<?= url('/auth/login') ?>" class="ts-btn ts-btn-primary" style="padding: 0.5rem 1.25rem; font-size: 0.9rem;">
            <i data-lucide="log-in" style="width: 14px;"></i> Sign in to Join Discussion
          </a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- Comments Live Stream Thread Tree -->
  <div id="comments-stream-container" style="display: flex; flex-direction: column;">
    <?php if (!empty($comments)): ?>
      <?php foreach ($comments as $index => $c): ?>
        <div class="root-comment-wrapper" style="<?= $index >= 10 ? 'display:none;' : '' ?>">
          <?php renderRedditCommentNode($c, $contentId, $contentAuthorId, $currentUser, 0); ?>
        </div>
      <?php endforeach; ?>

      <?php if (count($comments) > 10): ?>
        <div id="load-more-comments-wrap" style="text-align: center; margin-top: 1.5rem;">
          <button type="button" onclick="loadMoreRootComments()" class="ts-btn ts-btn-outline" style="padding: 0.65rem 2rem;">
            <i data-lucide="chevrons-down" style="width: 16px;"></i> Load More Comments (<?= count($comments) - 10 ?> remaining)
          </button>
        </div>
      <?php endif; ?>
    <?php else: ?>
      <div id="no-comments-prompt" class="ts-card" style="text-align: center; padding: 3rem; color: #64748b;">
        <i data-lucide="message-circle" style="width: 36px; height: 36px; color: var(--ts-primary); margin-bottom: 0.75rem;"></i>
        <h4 style="font-size: 1.1rem; color: var(--ts-text-primary); margin-bottom: 0.25rem;">No technical insights yet</h4>
        <p style="font-size: 0.85rem; margin: 0;">Be the first to analyze this architecture or share benchmarks!</p>
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
  btn.textContent = isCollapsed ? '[-]' : '[+] (collapsed)';
}

function loadMoreRootComments() {
  const hiddenRoots = document.querySelectorAll('.root-comment-wrapper[style*="display:none"], .root-comment-wrapper[style*="display: none"]');
  let shown = 0;
  hiddenRoots.forEach(node => {
    if (shown < 10) {
      node.style.display = 'block';
      shown++;
    }
  });
  if (hiddenRoots.length <= 10) {
    const wrap = document.getElementById('load-more-comments-wrap');
    if (wrap) wrap.style.display = 'none';
  }
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
    case 'codeblock':
      replacement = `\n\`\`\`javascript\n${selected || '// benchmark code here'}\n\`\`\`\n`;
      break;
    case 'quote':
      replacement = `\n> ${selected || 'Quote text'}\n`;
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
      if (typeof tsModal !== 'undefined') {
        tsModal.alert(data.error, 'Discussion Notice');
      } else if (typeof showToast === 'function') {
        showToast(data.error, 'error');
      }
      return;
    }

    if (typeof showToast === 'function') showToast('Technical comment posted successfully! 🚀', 'success');
    textarea.value = '';

    if (parentId) {
      toggleReplyBox(parentId);
      // Append nested reply node dynamically into replies container
      const replyContainer = document.getElementById('replies-container-' + parentId);
      if (replyContainer) {
        const replyId = data.comment.id;
        const replyHtml = `
          <div class="comment-node-wrapper comment-reply-node" id="comment-node-${replyId}" data-upvotes="0" data-timestamp="${Math.floor(Date.now()/1000)}" style="margin-left: 1.5rem; border-left: 2px solid rgba(99,102,241,0.35); padding-left: 0.75rem; margin-top: 0.5rem;">
            <div class="ts-card comment-card-inner" style="padding: 0.85rem 1.15rem; border-radius: 8px;">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.45rem;">
                <div style="display: flex; align-items: center; gap: 0.45rem;">
                  <button type="button" onclick="toggleCollapseThread(${replyId})" id="collapse-btn-${replyId}" style="background: none; border: none; color: #64748b; font-family: var(--ts-font-mono); font-size: 0.75rem; cursor: pointer; padding: 0 0.15rem;">[-]</button>
                  <img src="${data.comment.avatar_url || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80'}" alt="${data.comment.username}" style="width: 26px; height: 26px; border-radius: 50%; object-fit: cover;">
                  <span class="comment-author-name" style="font-size: 0.88rem;">${data.comment.display_name || data.comment.username}</span>
                </div>
                <div style="font-size: 0.72rem; color: #64748b; font-family: var(--ts-font-mono);">Just now</div>
              </div>
              <div id="comment-collapsible-body-${replyId}">
                <div class="comment-body-text" style="font-size: 0.92rem; line-height: 1.6; margin-bottom: 0.65rem; white-space: pre-wrap; word-break: break-word;">${escapeHtml(data.comment.comment_text)}</div>
                <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; border-top: 1px solid rgba(148,163,184,0.1); padding-top: 0.45rem;">
                  <button type="button" onclick="upvoteComment(${replyId})" class="ts-badge ts-badge-cyan" id="upvote-badge-${replyId}" style="cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.72rem; padding: 0.15rem 0.5rem;">
                    <i data-lucide="thumbs-up" style="width: 11px; height: 11px;"></i> Helpful (<span id="upvote-count-${replyId}">0</span>)
                  </button>
                  <button type="button" onclick="toggleReplyBox(${replyId}, '${escapeHtml(data.comment.username)}')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.55rem; font-size: 0.72rem;">
                    <i data-lucide="corner-down-right" style="width: 11px; height: 11px;"></i> Reply
                  </button>
                </div>
                <div id="reply-box-${replyId}" class="comment-reply-box" style="display: none; margin-top: 0.75rem; padding: 0.75rem 1rem; border-radius: 8px;">
                  <form onsubmit="submitComment(event, ${contentId}, ${replyId}, 'reply-text-${replyId}')">
                    <textarea id="reply-text-${replyId}" class="comment-composer-textarea" rows="2" placeholder="Write your reply..." style="width: 100%; border-radius: 6px; padding: 0.5rem 0.75rem; font-size: 0.88rem;" required></textarea>
                    <div style="display: flex; justify-content: flex-end; gap: 0.4rem; margin-top: 0.5rem;">
                      <button type="button" onclick="toggleReplyBox(${replyId})" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.55rem;">Cancel</button>
                      <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.75rem;">Post Reply 💬</button>
                    </div>
                  </form>
                </div>
                <div id="replies-container-${replyId}" class="replies-sub-tree"></div>
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
      const nodeWrapper = document.getElementById('comment-node-' + commentId);
      if (nodeWrapper) nodeWrapper.setAttribute('data-upvotes', data.upvotes);
      if (typeof showToast === 'function') {
        showToast('Helpful upvote registered! 💡', 'success');
      }
    } else {
      if (typeof showToast === 'function') {
        showToast(data.error || 'Error upvoting comment', 'error');
      }
    }
  } catch (err) {
    if (typeof showToast === 'function') {
      showToast('Error upvoting comment: ' + err.message, 'error');
    }
  }
}

async function deleteComment(commentId) {
  let confirmed = false;
  if (typeof tsModal !== 'undefined') {
    confirmed = await tsModal.confirm('Permanently delete this comment and its replies?', 'Delete Comment');
  } else {
    confirmed = confirm('Permanently delete this comment?');
  }
  if (!confirmed) return;

  try {
    const baseUrl = getCommentsApiBase();
    const res = await fetch(`${baseUrl}/api/v1/comments/${commentId}/delete`, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    const data = await res.json();
    if (data.success) {
      const node = document.getElementById('comment-node-' + commentId);
      if (node) node.remove();
      if (typeof showToast === 'function') showToast('Comment deleted', 'success');
    }
  } catch (err) {
    if (typeof showToast === 'function') showToast('Error deleting comment', 'error');
  }
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
