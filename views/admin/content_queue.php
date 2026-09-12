<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-main">
  <!-- Top Bar -->
  <div class="admin-topbar">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-cyan">EDITORIAL PIPELINE</span>
        <span style="font-size: 0.8rem; color: #64748b;">Universal Moderation & Multi-Entity Quality Review</span>
      </div>
      <h1 style="font-size: 1.6rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        Universal Content Review Queue
      </h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <!-- [TS-ED-07] Broken Link Scanner (Admin Only) -->
      <button type="button" onclick="openBrokenLinkScanner()" class="ts-btn ts-btn-outline ts-btn-sm" style="border-color: #ef4444; color: #f87171;">
        <i data-lucide="link-2-off"></i> Broken Link & Outdated URL Scanner 🔗
      </button>
      <a href="<?= url('/admin/moderation') ?>" class="ts-btn ts-btn-outline ts-btn-sm">
        <i data-lucide="shield-alert"></i> Moderation Reports
      </a>
      <a href="<?= url('/author/editor') ?>" target="_blank" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #6366f1, #38bdf8);">
        <i data-lucide="plus-circle"></i> Create New Article
      </a>
    </div>
  </div>

  <!-- Metric Stat Cards -->
  <div class="admin-stat-grid">
    <div class="admin-stat-card">
      <div class="admin-stat-label">Total Submissions</div>
      <div class="admin-stat-val" style="color: #38bdf8;"><?= number_format($counts['all'] ?? 0) ?></div>
      <div style="font-size: 0.8rem; color: #94a3b8;">Articles, Tools & GPTs</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Pending Staff Review</div>
      <div class="admin-stat-val" style="color: #f59e0b;"><?= number_format($counts['in_review'] ?? 0) ?></div>
      <div style="font-size: 0.8rem; color: #fbbf24;">Awaiting editorial action</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Live Published</div>
      <div class="admin-stat-val" style="color: #10b981;"><?= number_format($counts['published'] ?? 0) ?></div>
      <div style="font-size: 0.8rem; color: #34d399;">Active on platform</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Changes Required</div>
      <div class="admin-stat-val" style="color: #ec4899;"><?= number_format($counts['changes_required'] ?? 0) ?></div>
      <div style="font-size: 0.8rem; color: #f472b6;">Sent back to author</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Not Approved / Rejected</div>
      <div class="admin-stat-val" style="color: #ef4444;"><?= number_format($counts['rejected'] ?? 0) ?></div>
      <div style="font-size: 0.8rem; color: #f87171;">Declined submissions</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Drafts</div>
      <div class="admin-stat-val" style="color: #94a3b8;"><?= number_format($counts['draft'] ?? 0) ?></div>
      <div style="font-size: 0.8rem; color: #64748b;">In-progress works</div>
    </div>
  </div>

  <!-- Filters & Search Toolbar -->
  <div style="background: rgba(15,23,42,0.65); border: 1px solid rgba(148,163,184,0.15); border-radius: 10px; padding: 1.25rem; margin-bottom: 1.75rem;">
    <!-- Type Filter Tabs -->
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap; align-items: center;">
      <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-right: 0.25rem;">Entity Type:</span>
      <a href="<?= url('/admin/content?status=' . ($currentStatus ?? 'all') . '&type=all' . (!empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '')) ?>" class="ts-btn ts-btn-sm <?= empty($currentType) || $currentType === 'all' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.78rem;">
        All Types (<?= $counts['all'] ?? 0 ?>)
      </a>
      <a href="<?= url('/admin/content?status=' . ($currentStatus ?? 'all') . '&type=article' . (!empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '')) ?>" class="ts-btn ts-btn-sm <?= ($currentType ?? '') === 'article' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.78rem;">
        📝 Articles (<?= $counts['articles'] ?? 0 ?>)
      </a>
      <a href="<?= url('/admin/content?status=' . ($currentStatus ?? 'all') . '&type=ai_tool' . (!empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '')) ?>" class="ts-btn ts-btn-sm <?= ($currentType ?? '') === 'ai_tool' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.78rem;">
        ⚡ AI Tools (<?= $counts['tools'] ?? 0 ?>)
      </a>
      <a href="<?= url('/admin/content?status=' . ($currentStatus ?? 'all') . '&type=gpt_listing' . (!empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '')) ?>" class="ts-btn ts-btn-sm <?= ($currentType ?? '') === 'gpt_listing' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.78rem;">
        🤖 Custom GPTs (<?= $counts['gpts'] ?? 0 ?>)
      </a>
      <a href="<?= url('/admin/content?status=' . ($currentStatus ?? 'all') . '&type=prompt' . (!empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '')) ?>" class="ts-btn ts-btn-sm <?= ($currentType ?? '') === 'prompt' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.78rem;">
        💡 Prompts (<?= $counts['prompts'] ?? 0 ?>)
      </a>
      <a href="<?= url('/admin/content?status=' . ($currentStatus ?? 'all') . '&type=campaign' . (!empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '')) ?>" class="ts-btn ts-btn-sm <?= ($currentType ?? '') === 'campaign' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.78rem;">
        🏆 Challenges (<?= $counts['campaigns'] ?? 0 ?>)
      </a>
    </div>

    <!-- Status Filter Badges & Search Box -->
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
      <div style="display: flex; gap: 0.4rem; flex-wrap: wrap; align-items: center;">
        <span style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-right: 0.25rem;">Status:</span>
        <a href="<?= url('/admin/content?status=all&type=' . ($currentType ?? 'all') . (!empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '')) ?>" class="ts-btn ts-btn-sm <?= empty($currentStatus) || $currentStatus === 'all' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.78rem;">
          All
        </a>
        <a href="<?= url('/admin/content?status=in_review&type=' . ($currentType ?? 'all') . (!empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '')) ?>" class="ts-btn ts-btn-sm <?= $currentStatus === 'in_review' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.78rem; border-color: rgba(245,158,11,0.4); color: <?= $currentStatus === 'in_review' ? '#fff' : '#f59e0b' ?>;">
          ⏳ In Review (<?= $counts['in_review'] ?? 0 ?>)
        </a>
        <a href="<?= url('/admin/content?status=published&type=' . ($currentType ?? 'all') . (!empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '')) ?>" class="ts-btn ts-btn-sm <?= $currentStatus === 'published' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.78rem; border-color: rgba(16,185,129,0.4); color: <?= $currentStatus === 'published' ? '#fff' : '#34d399' ?>;">
          ✓ Published (<?= $counts['published'] ?? 0 ?>)
        </a>
        <a href="<?= url('/admin/content?status=changes_required&type=' . ($currentType ?? 'all') . (!empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '')) ?>" class="ts-btn ts-btn-sm <?= $currentStatus === 'changes_required' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.78rem; border-color: rgba(236,72,153,0.4); color: <?= $currentStatus === 'changes_required' ? '#fff' : '#f472b6' ?>;">
          📋 Changes Requested (<?= $counts['changes_required'] ?? 0 ?>)
        </a>
        <a href="<?= url('/admin/content?status=rejected&type=' . ($currentType ?? 'all') . (!empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '')) ?>" class="ts-btn ts-btn-sm <?= $currentStatus === 'rejected' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.78rem; border-color: rgba(239,68,68,0.4); color: <?= $currentStatus === 'rejected' ? '#fff' : '#f87171' ?>;">
          ❌ Not Approved (<?= $counts['rejected'] ?? 0 ?>)
        </a>
        <a href="<?= url('/admin/content?status=draft&type=' . ($currentType ?? 'all') . (!empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '')) ?>" class="ts-btn ts-btn-sm <?= $currentStatus === 'draft' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.78rem;">
          📄 Drafts (<?= $counts['draft'] ?? 0 ?>)
        </a>
        <a href="<?= url('/admin/content?status=archived&type=' . ($currentType ?? 'all') . (!empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '')) ?>" class="ts-btn ts-btn-sm <?= $currentStatus === 'archived' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.78rem;">
          📦 Archived (<?= $counts['archived'] ?? 0 ?>)
        </a>
      </div>

      <!-- Search Input -->
      <form action="<?= url('/admin/content') ?>" method="GET" style="display: flex; gap: 0.4rem; min-width: 260px;">
        <input type="hidden" name="status" value="<?= htmlspecialchars($currentStatus ?? 'all') ?>">
        <input type="hidden" name="type" value="<?= htmlspecialchars($currentType ?? 'all') ?>">
        <input type="text" name="q" value="<?= htmlspecialchars($searchQuery ?? '') ?>" placeholder="Search title, author, category..." style="background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.35rem 0.75rem; border-radius: 6px; color: #f8fafc; font-size: 0.82rem; width: 100%;">
        <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.35rem 0.65rem;">
          <i data-lucide="search" style="width: 14px;"></i>
        </button>
        <?php if (!empty($searchQuery)): ?>
          <a href="<?= url('/admin/content?status=' . ($currentStatus ?? 'all') . '&type=' . ($currentType ?? 'all')) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.35rem 0.65rem; color: #ef4444;" title="Clear Search">
            ✕
          </a>
        <?php endif; ?>
      </form>
    </div>
  </div>

  <!-- Content Items Table -->
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Title & Details</th>
          <th>Entity Type</th>
          <th>Taxonomy Category</th>
          <th>Review Status</th>
          <th>Score & Signals</th>
          <th>Editorial Decisions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($items)): ?>
          <?php foreach ($items as $item): ?>
            <tr>
              <td>
                <div style="display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap; margin-bottom: 0.25rem;">
                  <?php if (!empty($item['is_pinned'])): ?>
                    <span class="ts-badge" style="background: rgba(245,158,11,0.15); color: #f59e0b; border: 1px solid rgba(245,158,11,0.4); font-size: 0.65rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.2rem;">
                      📌 PINNED
                    </span>
                  <?php endif; ?>

                  <?php if (!empty($item['custom_badge'])): ?>
                    <span class="ts-badge" style="background: <?= htmlspecialchars($item['badge_bg'] ?: 'rgba(56,189,248,0.15)') ?>; color: <?= htmlspecialchars($item['badge_color'] ?: '#38bdf8') ?>; border: 1px solid <?= htmlspecialchars($item['badge_color'] ?: '#38bdf8') ?>50; font-size: 0.65rem; font-weight: 700;">
                      ⭐ <?= htmlspecialchars($item['custom_badge']) ?>
                    </span>
                  <?php endif; ?>
                </div>

                <div style="font-weight: 700; color: #f8fafc; font-size: 0.95rem;">
                  <a href="<?= content_url($item) ?>" target="_blank" style="color: #f8fafc; text-decoration: none;" title="Open live preview in new tab">
                    <?= htmlspecialchars($item['title']) ?> ↗
                  </a>
                </div>
                <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">
                  Author: <strong style="color: #38bdf8;"><?= htmlspecialchars($item['author_name'] ?? 'Staff') ?></strong>
                  &bull; Created: <?= \App\Core\DateHelper::render($item['created_at'], 'M j, Y', 'date') ?>
                  <?php if (!empty($item['published_at'])): ?>
                    &bull; Published: <?= \App\Core\DateHelper::render($item['published_at'], 'M j, Y', 'date') ?>
                  <?php endif; ?>
                </div>
                <?php if (!empty($item['internal_notes'])): ?>
                  <div style="margin-top: 0.35rem; font-size: 0.73rem; background: rgba(99,102,241,0.1); border-left: 3px solid #818cf8; padding: 0.25rem 0.5rem; color: #c7d2fe; border-radius: 0 4px 4px 0;" title="<?= htmlspecialchars($item['internal_notes']) ?>">
                    <i data-lucide="lock" style="width: 10px; height: 10px; display: inline;"></i> <strong>Internal Note:</strong> <?= htmlspecialchars(mb_strimwidth($item['internal_notes'], 0, 50, '...')) ?>
                  </div>
                <?php endif; ?>
                <?php if (!empty($item['editorial_notes'])): ?>
                  <div style="margin-top: 0.35rem; font-size: 0.73rem; background: rgba(245,158,11,0.1); border-left: 3px solid #f59e0b; padding: 0.25rem 0.5rem; color: #fcd34d; border-radius: 0 4px 4px 0;">
                    <strong>Author Note:</strong> <?= htmlspecialchars($item['editorial_notes']) ?>
                  </div>
                <?php endif; ?>
              </td>

              <td>
                <span class="ts-badge ts-badge-cyan" style="font-size: 0.68rem; text-transform: uppercase; font-family: monospace;">
                  <?= htmlspecialchars(str_replace('_', ' ', $item['content_type'])) ?>
                </span>
              </td>

              <td>
                <span style="font-size: 0.85rem; color: #cbd5e1;">
                  <?= htmlspecialchars($item['category_name'] ?? 'Unassigned') ?>
                </span>
              </td>

              <td>
                <?php if ($item['status'] === 'published'): ?>
                  <span class="ts-badge ts-badge-green">✓ PUBLISHED</span>
                <?php elseif ($item['status'] === 'in_review'): ?>
                  <?php if (!empty($item['editorial_notes'])): ?>
                    <span class="ts-badge ts-badge-pink" style="background: rgba(236,72,153,0.2); color: #f472b6; border: 1px solid rgba(236,72,153,0.4);">CHANGES REQ</span>
                  <?php else: ?>
                    <span class="ts-badge" style="background: rgba(245,158,11,0.2); color: #f59e0b; border: 1px solid rgba(245,158,11,0.4);">PENDING REVIEW</span>
                  <?php endif; ?>
                <?php elseif ($item['status'] === 'rejected'): ?>
                  <span class="ts-badge" style="background: rgba(239,68,68,0.2); color: #ef4444; border: 1px solid rgba(239,68,68,0.4);">NOT APPROVED</span>
                <?php elseif ($item['status'] === 'draft'): ?>
                  <span class="ts-badge" style="background: rgba(148,163,184,0.15); color: #94a3b8;">DRAFT</span>
                <?php else: ?>
                  <span class="ts-badge"><?= strtoupper(htmlspecialchars($item['status'])) ?></span>
                <?php endif; ?>
              </td>

              <td>
                <div style="display: flex; align-items: center; gap: 0.4rem;">
                  <strong style="color: #34d399; font-size: 0.95rem;"><?= number_format((float)$item['quality_score'], 1) ?></strong>
                  <span style="font-size: 0.72rem; color: #64748b;">/ 100</span>
                </div>
                <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 0.2rem;">
                  <?= number_format((int)$item['view_count']) ?> views &bull; <?= number_format((int)$item['reaction_count']) ?> likes
                </div>
              </td>

              <td>
                <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                  <!-- Full Inspection & Live Review Trigger -->
                  <button type="button" onclick="openReviewStation(<?= (int)$item['id'] ?>)" class="ts-btn ts-btn-primary ts-btn-sm" style="padding: 0.22rem 0.6rem; font-size: 0.72rem; background: linear-gradient(135deg, #6366f1, #38bdf8); border: none; font-weight: 700;" title="Open Universal Review & Inspection Station">
                    Review 👁️
                  </button>

                  <!-- Approve / Publish -->
                  <?php if ($item['status'] !== 'published'): ?>
                    <form action="<?= url('/admin/content/action/' . $item['id']) ?>" method="POST">
                      <button type="submit" name="action" value="approve" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.22rem 0.55rem; font-size: 0.72rem; color: #10b981; border-color: rgba(16,185,129,0.4);">
                        Approve ⚡
                      </button>
                    </form>
                  <?php endif; ?>

                  <!-- Request Changes Modal Trigger -->
                  <button type="button" onclick="openChangesModal(<?= $item['id'] ?>, '<?= htmlspecialchars(addslashes($item['title'])) ?>', '<?= htmlspecialchars(addslashes($item['editorial_notes'] ?? '')) ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.22rem 0.55rem; font-size: 0.72rem; color: #f59e0b; border-color: rgba(245,158,11,0.4);" title="Request Editorial Changes">
                    Feedback 📋
                  </button>

                  <!-- Reject Modal Trigger -->
                  <?php if ($item['status'] !== 'rejected'): ?>
                    <button type="button" onclick="openRejectModal(<?= $item['id'] ?>, '<?= htmlspecialchars(addslashes($item['title'])) ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.22rem 0.55rem; font-size: 0.72rem; color: #ef4444; border-color: rgba(239,68,68,0.4);" title="Reject / Do Not Approve">
                      Reject ❌
                    </button>
                  <?php endif; ?>

                  <!-- Feature Toggle -->
                  <form action="<?= url('/admin/content/action/' . $item['id']) ?>" method="POST">
                    <button type="submit" name="action" value="toggle_featured" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.22rem 0.5rem; font-size: 0.72rem; <?= $item['is_featured'] ? 'color: #f59e0b; border-color: #f59e0b;' : '' ?>" title="Toggle Featured on Homepage">
                      <?= $item['is_featured'] ? '★' : '☆' ?>
                    </button>
                  </form>

                  <!-- Quick Pin to Top Toggle (Feature 4) -->
                  <button type="button" onclick="togglePinDirect(<?= (int)$item['id'] ?>, this)" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.22rem 0.45rem; font-size: 0.72rem; <?= !empty($item['is_pinned']) ? 'color: #f59e0b; border-color: rgba(245,158,11,0.6); background: rgba(245,158,11,0.1);' : 'color: #64748b;' ?>" title="<?= !empty($item['is_pinned']) ? 'Pinned to top (Click to unpin)' : 'Click to pin to top of category & feeds' ?>">
                    📌
                  </button>

                  <!-- Assign Custom Editorial Badge (Feature 10) -->
                  <button type="button" onclick="openBadgePickerModal(<?= (int)$item['id'] ?>, '<?= htmlspecialchars(addslashes($item['custom_badge'] ?? '')) ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.22rem 0.45rem; font-size: 0.72rem; <?= !empty($item['custom_badge']) ? 'color: ' . htmlspecialchars($item['badge_color']) . '; border-color: ' . htmlspecialchars($item['badge_color']) . '60;' : 'color: #94a3b8;' ?>" title="Assign Custom Editorial Badge">
                    ⭐
                  </button>

                  <!-- Plagiarism & Originality Scan -->
                  <button type="button" onclick="runPlagiarismScan(<?= $item['id'] ?>, '<?= htmlspecialchars(addslashes($item['title'])) ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.22rem 0.55rem; font-size: 0.72rem; color: #818cf8; border-color: rgba(99,102,241,0.4);" title="Run N-Gram Originality & Plagiarism Scan">
                    Scan 🔍
                  </button>

                  <!-- Delete -->
                  <form action="<?= url('/admin/content/action/' . $item['id']) ?>" method="POST" onsubmit="return confirm('Permanently delete this content submission?');">
                    <button type="submit" name="action" value="delete" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.22rem 0.45rem; font-size: 0.72rem; color: #64748b;" title="Delete Entity">
                      🗑
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" style="text-align: center; padding: 3.5rem; color: #64748b;">
              <i data-lucide="layers" style="width: 36px; height: 36px; margin: 0 auto 0.75rem auto; display: block; opacity: 0.4;"></i>
              No content items matching this filter criteria.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- MODAL: REQUEST EDITORIAL CHANGES -->
<div id="changesModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-amber">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #f59e0b;">
        <i data-lucide="edit-3"></i> Request Editorial Changes
      </h3>
      <button type="button" onclick="closeChangesModal()" class="admin-modal-close" title="Close Modal">✕</button>
    </div>

    <p style="color: #cbd5e1; font-size: 0.85rem; margin: 0 0 1.25rem 0; line-height: 1.5;">
      Provide actionable feedback to the author. The submission status will change to <strong>In Review (Changes Requested)</strong> and an instant notification will be dispatched to their dashboard.
    </p>

    <form id="changesForm" action="" method="POST">
      <input type="hidden" name="action" value="request_changes">
      <div style="margin-bottom: 1.25rem;">
        <label class="admin-modal-label">Target Submission Title</label>
        <input type="text" id="changesTitle" readonly class="admin-modal-input" style="opacity: 0.8; cursor: not-allowed;">
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label class="admin-modal-label">Editorial Feedback &amp; Required Fixes</label>
        <textarea name="feedback_notes" id="changesNotes" rows="4" placeholder="Specify code benchmarks, typos, formatting, missing sections..." class="admin-modal-textarea" required></textarea>
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="closeChangesModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #f59e0b; border-color: #f59e0b; font-weight: 700; color: #000;">
          Send Feedback &amp; Request Changes 📋
        </button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: REJECT CONTENT SUBMISSION -->
<div id="rejectModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-rose">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #ef4444;">
        <i data-lucide="x-circle"></i> Decline &amp; Reject Submission
      </h3>
      <button type="button" onclick="closeRejectModal()" class="admin-modal-close" title="Close Modal">✕</button>
    </div>

    <p style="color: #cbd5e1; font-size: 0.85rem; margin: 0 0 1.25rem 0; line-height: 1.5;">
      Decline this submission. The status will update to <strong>Not Approved (Rejected)</strong> and notification feedback will be logged to the author.
    </p>

    <form id="rejectForm" action="" method="POST">
      <input type="hidden" name="action" value="reject">
      <div style="margin-bottom: 1.25rem;">
        <label class="admin-modal-label">Target Submission Title</label>
        <input type="text" id="rejectTitle" readonly class="admin-modal-input" style="opacity: 0.8; cursor: not-allowed;">
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label class="admin-modal-label">Decline Reason &amp; Staff Explanation</label>
        <textarea name="feedback_notes" rows="3" placeholder="Explain why the submission does not meet publication standards..." class="admin-modal-textarea" required></textarea>
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="closeRejectModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #ef4444; border-color: #ef4444; font-weight: 700;">
          Confirm Rejection ❌
        </button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: PLAGIARISM & ORIGINALITY REPORT -->
<div id="plagiarismModal" class="admin-modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.88); backdrop-filter: blur(10px); z-index: 999999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="ts-card" style="width: 100%; max-width: 680px; padding: 2rem; background: #090d16; border: 1px solid rgba(99,102,241,0.4); box-shadow: 0 25px 60px rgba(0,0,0,0.95); max-height: 90vh; overflow-y: auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid rgba(148,163,184,0.15); padding-bottom: 0.75rem;">
      <div style="display: flex; align-items: center; gap: 0.5rem;">
        <span style="font-size: 1.25rem;">🔍</span>
        <div>
          <h3 id="plagModalTitle" style="font-size: 1.15rem; color: #f8fafc; margin: 0;">Plagiarism & Originality Forensic Report</h3>
          <span id="plagModalSub" style="font-size: 0.78rem; color: #38bdf8;">Scanning content repository...</span>
        </div>
      </div>
      <button onclick="document.getElementById('plagiarismModal').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer;">&times;</button>
    </div>

    <!-- Spinner -->
    <div id="plagLoading" style="text-align: center; padding: 3rem 1rem;">
      <div style="display: inline-block; width: 36px; height: 36px; border: 3px solid rgba(99,102,241,0.2); border-radius: 50%; border-top-color: #818cf8; animation: spin 0.8s linear infinite;"></div>
      <div style="margin-top: 1rem; font-size: 0.85rem; color: #94a3b8;">Generating N-Gram shingles and computing Jaccard similarity...</div>
    </div>

    <!-- Results Body -->
    <div id="plagResults" style="display: none;">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
        <div style="background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 10px; padding: 1.25rem; text-align: center;">
          <div style="font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Originality Score</div>
          <div id="plagScoreVal" style="font-size: 2rem; font-weight: 800; color: #34d399; margin-top: 0.25rem;">--%</div>
        </div>
        <div style="background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 10px; padding: 1.25rem; text-align: center;">
          <div style="font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Verdict</div>
          <div id="plagVerdictBadge" style="font-size: 1.1rem; font-weight: 800; margin-top: 0.5rem;">--</div>
        </div>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <h4 style="font-size: 0.9rem; color: #f8fafc; margin-bottom: 0.5rem;">Matching Internal Sources</h4>
        <div id="plagSourcesList" style="display: flex; flex-direction: column; gap: 0.5rem;"></div>
      </div>

      <div>
        <h4 style="font-size: 0.9rem; color: #f8fafc; margin-bottom: 0.5rem;">Sample Matching Shingle Snippets</h4>
        <div id="plagSnippetsList" style="background: #05070b; border: 1px solid rgba(148,163,184,0.12); border-radius: 8px; padding: 0.75rem; max-height: 160px; overflow-y: auto; font-family: var(--admin-font-mono); font-size: 0.78rem; color: #f87171;"></div>
      </div>
    </div>

    <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem; border-top: 1px solid rgba(148,163,184,0.12); padding-top: 1rem;">
      <button type="button" onclick="document.getElementById('plagiarismModal').style.display='none'" class="ts-btn ts-btn-primary ts-btn-sm">Done Reviewing</button>
    </div>
  </div>
</div>

<!-- MODAL: UNIVERSAL EDITORIAL REVIEW & INSPECTION STATION -->
<div id="reviewStationModal" class="admin-modal-overlay" style="display: none; align-items: center; justify-content: center; z-index: 999999;">
  <div class="admin-modal-box" style="max-width: 1040px; width: 95%; max-height: 90vh; overflow-y: auto; background: #090d16; border: 1px solid rgba(99,102,241,0.4); border-radius: 16px; padding: 2rem; box-shadow: 0 25px 70px rgba(0,0,0,0.9);">
    
    <!-- Modal Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid rgba(148,163,184,0.15); padding-bottom: 1.25rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
      <div>
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
          <span class="ts-badge ts-badge-pink" id="revModalType">ARTICLE</span>
          <span class="ts-badge ts-badge-cyan" id="revModalStatus">IN REVIEW</span>
          <span style="font-size: 0.78rem; color: #64748b;" id="revModalId">ID #0</span>
        </div>
        <h2 id="revModalTitle" style="font-size: 1.4rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800; line-height: 1.3;">
          Loading submission content...
        </h2>
      </div>

      <div style="display: flex; gap: 0.5rem; align-items: center;">
        <a id="revLivePreviewLink" href="#" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3); font-size: 0.75rem;">
          Live Page ↗
        </a>
        <button type="button" onclick="closeReviewStation()" class="admin-modal-close" style="font-size: 1.4rem; padding: 0.2rem 0.5rem;" title="Close Review Station">✕</button>
      </div>
    </div>

    <!-- Review Loading State -->
    <div id="revLoadingState" style="text-align: center; padding: 3rem; color: #94a3b8;">
      <i data-lucide="loader-2" style="width: 36px; height: 36px; animation: spin 1s linear infinite; margin: 0 auto 0.75rem auto; display: block; color: #6366f1;"></i>
      <p style="font-size: 0.9rem; margin: 0;">Retrieving full markdown text, SEO payload, and author metadata...</p>
    </div>

    <!-- Review Content Body Grid -->
    <div id="revContentBody" style="display: none;">
      
      <!-- Top Diagnostic Telemetry Strip -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 0.75rem; margin-bottom: 1.5rem;">
        <div style="background: #05070b; border: 1px solid rgba(148,163,184,0.12); border-radius: 8px; padding: 0.75rem; text-align: center;">
          <div style="font-size: 0.7rem; color: #64748b; text-transform: uppercase;">Word Count</div>
          <div id="revWordCount" style="font-size: 1.25rem; font-weight: 800; color: #38bdf8; font-family: var(--ts-font-mono);">0</div>
        </div>
        <div style="background: #05070b; border: 1px solid rgba(148,163,184,0.12); border-radius: 8px; padding: 0.75rem; text-align: center;">
          <div style="font-size: 0.7rem; color: #64748b; text-transform: uppercase;">Est. Read Time</div>
          <div id="revReadTime" style="font-size: 1.25rem; font-weight: 800; color: #34d399; font-family: var(--ts-font-mono);">1 min</div>
        </div>
        <div style="background: #05070b; border: 1px solid rgba(148,163,184,0.12); border-radius: 8px; padding: 0.75rem; text-align: center;">
          <div style="font-size: 0.7rem; color: #64748b; text-transform: uppercase;">Quality Score</div>
          <div id="revQualityScore" style="font-size: 1.25rem; font-weight: 800; color: #fbbf24; font-family: var(--ts-font-mono);">88 / 100</div>
        </div>
        <div style="background: #05070b; border: 1px solid rgba(148,163,184,0.12); border-radius: 8px; padding: 0.75rem; text-align: center;">
          <div style="font-size: 0.7rem; color: #64748b; text-transform: uppercase;">Author Level</div>
          <div id="revAuthorLevel" style="font-size: 1.25rem; font-weight: 800; color: #c084fc; font-family: var(--ts-font-mono);">Verified</div>
        </div>
      </div>

      <!-- Main Columns: Content Inspection & Metadata -->
      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
        
        <!-- Left: Full Text / Markdown / Code Preview -->
        <div>
          <div style="background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 10px; padding: 1.25rem; margin-bottom: 1rem;">
            <div style="font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 0.5rem;">Summary / Excerpt:</div>
            <p id="revSummary" style="color: #cbd5e1; font-size: 0.88rem; line-height: 1.5; margin: 0;"></p>
          </div>

          <div style="background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 10px; padding: 1.5rem; max-height: 480px; overflow-y: auto;">
            <div style="font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 0.75rem;">Full Document Body Preview:</div>
            <div id="revFullContent" class="preview-article-body" style="color: #e2e8f0; font-size: 0.95rem; line-height: 1.7; word-break: break-word;"></div>
          </div>
        </div>

        <!-- Right: Author, Taxonomy & SEO Metas -->
        <div style="display: flex; flex-direction: column; gap: 1rem;">
          <!-- Author Card -->
          <div style="background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 10px; padding: 1.25rem;">
            <div style="font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 0.5rem;">Submitting Creator:</div>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
              <div id="revAuthorAvatarBox" style="width: 42px; height: 42px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #38bdf8); display: flex; align-items: center; justify-content: center; font-weight: 800; color: #fff;">A</div>
              <div>
                <div id="revAuthorName" style="color: #f8fafc; font-weight: 700; font-size: 0.95rem;">Author</div>
                <div id="revAuthorSlug" style="font-size: 0.75rem; color: #38bdf8; font-family: var(--ts-font-mono);">@slug</div>
              </div>
            </div>
            <div id="revAuthorScore" style="font-size: 0.78rem; color: #fbbf24; margin-top: 0.5rem;">⭐ 0 XP Reputation</div>
          </div>

          <!-- Metadata & Taxonomy -->
          <div style="background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 10px; padding: 1.25rem;">
            <div style="font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 0.5rem;">Taxonomy &amp; Destination:</div>
            <div style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 0.35rem;">Category: <strong id="revCategory" style="color: #f8fafc;">General</strong></div>
            <div style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 0.35rem;">Slug: <code id="revSlugCode" style="color: #38bdf8;">slug</code></div>
            <div id="revExternalUrlBox" style="font-size: 0.8rem; color: #94a3b8; margin-top: 0.5rem; word-break: break-all;">
              External Demo/Repo: <a id="revExternalUrl" href="#" target="_blank" style="color: #ec4899;">None</a>
            </div>
          </div>

          <!-- Editorial Notes if any -->
          <div id="revExistingNotesBox" style="background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.3); border-radius: 10px; padding: 1rem; display: none;">
            <div style="font-size: 0.75rem; text-transform: uppercase; color: #f59e0b; font-weight: 700; margin-bottom: 0.35rem;">Author Revisions Feedback:</div>
            <p id="revExistingNotes" style="font-size: 0.82rem; color: #fde68a; margin: 0; line-height: 1.4;"></p>
          </div>

          <!-- FEATURE 9: EDITORIAL INTERNAL NOTES SCRATCHPAD (PRIVATE TO ADMINS) -->
          <div style="background: rgba(99,102,241,0.08); border: 1px solid rgba(99,102,241,0.3); border-radius: 10px; padding: 1rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
              <div style="font-size: 0.75rem; text-transform: uppercase; color: #818cf8; font-weight: 700; display: flex; align-items: center; gap: 0.35rem;">
                <i data-lucide="lock" style="width: 12px; height: 12px;"></i> Internal Scratchpad (Admins Only)
              </div>
              <span id="revScratchpadSaved" style="font-size: 0.72rem; color: #10b981; display: none; font-weight: 700;">Saved ✓</span>
            </div>
            <textarea id="revInternalNotes" placeholder="Private notes for staff only (never shared with authors or public)..." style="width: 100%; height: 75px; background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 6px; padding: 0.5rem; color: #f8fafc; font-size: 0.82rem; resize: vertical; margin-bottom: 0.45rem;"></textarea>
            <button type="button" onclick="saveInternalNotesFromModal()" class="ts-btn ts-btn-sm ts-btn-primary" style="width: 100%; font-size: 0.75rem; padding: 0.3rem; background: #6366f1;">
              Save Private Scratchpad 📝
            </button>
          </div>

          <!-- FEATURE 10: CUSTOM EDITORIAL BADGE SELECTOR -->
          <div style="background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 10px; padding: 1rem;">
            <div style="font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 0.5rem;">
              Custom Editorial Badge:
            </div>
            <div id="revCurrentBadgeDisplay" style="margin-bottom: 0.65rem;">
              <span class="ts-badge" style="color: #64748b; border: 1px dashed #64748b; font-size: 0.75rem;">No Editorial Badge</span>
            </div>
            <button type="button" onclick="openBadgePickerModal(window.currentReviewedContentId)" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%; font-size: 0.75rem; padding: 0.3rem; color: #38bdf8; border-color: rgba(56,189,248,0.4);">
              <i data-lucide="award"></i> Assign / Edit Badge ⭐
            </button>
          </div>
        </div>
      </div>

      <!-- Bottom Review Action Toolbar -->
      <div style="border-top: 1px solid rgba(148,163,184,0.15); padding-top: 1.25rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
          <a id="revEditorLink" href="#" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3);">
            <i data-lucide="edit"></i> Open in Editor ↗
          </a>
          <button type="button" id="revPinBtn" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #f59e0b; border-color: rgba(245,158,11,0.4);">
            📌 Pin to Top
          </button>
          <button type="button" id="revPlagiarismBtn" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #818cf8; border-color: rgba(99,102,241,0.3);">
            <i data-lucide="shield-check"></i> Plagiarism Scan
          </button>
        </div>

        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
          <!-- Request Changes Form -->
          <button type="button" id="revFeedbackBtn" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #f59e0b; border-color: rgba(245,158,11,0.4); font-weight: 700;">
            Request Changes 📋
          </button>

          <!-- Reject Form -->
          <button type="button" id="revRejectBtn" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.4); font-weight: 700;">
            Reject ❌
          </button>

          <!-- Approve Form -->
          <form id="revApproveForm" action="" method="POST" style="margin: 0;">
            <button type="submit" name="action" value="approve" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #059669, #10b981); border: none; font-weight: 800; padding: 0.45rem 1.1rem;">
              Approve &amp; Publish Live ⚡
            </button>
          </form>
        </div>
      </div>

    </div>
  </div>
</div>

<style>
@keyframes spin { to { transform: rotate(360deg); } }
.preview-article-body h1, .preview-article-body h2, .preview-article-body h3 {
  color: #f8fafc;
  margin-top: 1.25rem;
  margin-bottom: 0.5rem;
  font-weight: 700;
}
.preview-article-body p {
  margin-bottom: 0.85rem;
  line-height: 1.7;
}
.preview-article-body pre {
  background: #020408;
  border: 1px solid rgba(148,163,184,0.15);
  border-radius: 8px;
  padding: 1rem;
  overflow-x: auto;
  margin: 1rem 0;
  font-family: var(--ts-font-mono);
  font-size: 0.85rem;
  color: #38bdf8;
}
.preview-article-body img {
  max-width: 100%;
  border-radius: 8px;
  margin: 1rem 0;
}
.preview-article-body blockquote {
  border-left: 3px solid #6366f1;
  padding-left: 1rem;
  margin: 1rem 0;
  color: #94a3b8;
  font-style: italic;
}
.preview-article-body .word-callout {
  background: rgba(99,102,241,0.1);
  border: 1px solid rgba(99,102,241,0.3);
  border-radius: 8px;
  padding: 0.75rem 1rem;
  margin: 1rem 0;
}
</style>

<script>
function openReviewStation(contentId) {
  const modal = document.getElementById('reviewStationModal');
  if (!modal) return;

  modal.style.display = 'flex';
  document.getElementById('revLoadingState').style.display = 'block';
  document.getElementById('revContentBody').style.display = 'none';

  const previewPath = '<?= parse_url(url('/api/v1/content/preview'), PHP_URL_PATH) ?>/' + contentId;
  fetch(previewPath)
    .then(res => res.json())
    .then(data => {
      document.getElementById('revLoadingState').style.display = 'none';
      if (!data.success || !data.content) {
        alert('Failed to load content preview: ' + (data.error || 'Unknown error'));
        modal.style.display = 'none';
        return;
      }

      const c = data.content;
      const diag = data.diagnostics || {};

      document.getElementById('revModalType').innerText = (c.content_type || c.type || 'ARTICLE').toUpperCase();
      document.getElementById('revModalStatus').innerText = (c.status || 'DRAFT').toUpperCase();
      document.getElementById('revModalId').innerText = 'ID #' + c.id;
      document.getElementById('revModalTitle').innerText = c.title || 'Untitled Submission';

      const typeSlug = (c.content_type === 'article' || !c.content_type) ? 'article' : (c.content_type === 'ai_tool' ? 'tool' : (c.content_type === 'prompt' ? 'prompt' : 'gpt'));
      document.getElementById('revLivePreviewLink').href = '<?= parse_url(url('/'), PHP_URL_PATH) ?>' + typeSlug + '/' + (c.slug || '');
      document.getElementById('revWordCount').innerText = Number(diag.word_count || 0).toLocaleString();
      document.getElementById('revReadTime').innerText = (diag.read_time_minutes || 1) + ' min';
      document.getElementById('revQualityScore').innerText = (parseFloat(c.quality_score || 85)).toFixed(1) + ' / 100';

      document.getElementById('revSummary').innerText = c.summary || 'No summary excerpt provided by author.';
      document.getElementById('revFullContent').innerHTML = c.body_html || c.body_markdown || c.summary || '<p style="color: #64748b;">(No body content text)</p>';

      document.getElementById('revAuthorName').innerText = c.author_name || 'Staff Editorial';
      document.getElementById('revAuthorSlug').innerText = '@' + (c.author_slug || 'author');
      document.getElementById('revAuthorScore').innerText = '⭐ ' + Number(c.reputation_score || 100).toLocaleString() + ' XP Reputation';

      document.getElementById('revCategory').innerText = c.category_name || 'General';
      document.getElementById('revSlugCode').innerText = c.slug || '';

      const extBox = document.getElementById('revExternalUrlBox');
      const extLink = document.getElementById('revExternalUrl');
      if (c.website_url || c.demo_url || c.github_url) {
        const target = c.website_url || c.demo_url || c.github_url;
        extLink.href = target;
        extLink.innerText = target;
        extBox.style.display = 'block';
      } else {
        extBox.style.display = 'none';
      }

      // Editorial Notes
      const notesBox = document.getElementById('revExistingNotesBox');
      if (c.editorial_notes && c.editorial_notes.trim()) {
        document.getElementById('revExistingNotes').innerText = c.editorial_notes;
        notesBox.style.display = 'block';
      } else {
        notesBox.style.display = 'none';
      }

      window.currentReviewedContentId = c.id;

      // FEATURE 9: Populate Internal Scratchpad (Private to Admins)
      const scratchpad = document.getElementById('revInternalNotes');
      if (scratchpad) scratchpad.value = c.internal_notes || '';
      const savedNotice = document.getElementById('revScratchpadSaved');
      if (savedNotice) savedNotice.style.display = 'none';

      // FEATURE 10: Populate Custom Editorial Badge
      const badgeDisplay = document.getElementById('revCurrentBadgeDisplay');
      if (badgeDisplay) {
        if (c.custom_badge) {
          badgeDisplay.innerHTML = `<span class="ts-badge" style="background: ${c.badge_bg || 'rgba(56,189,248,0.15)'}; color: ${c.badge_color || '#38bdf8'}; border: 1px solid ${c.badge_color || '#38bdf8'}50; font-weight: 700; font-size: 0.8rem;">⭐ ${c.custom_badge}</span>`;
        } else {
          badgeDisplay.innerHTML = `<span class="ts-badge" style="color: #64748b; border: 1px dashed #64748b; font-size: 0.75rem;">No Editorial Badge</span>`;
        }
      }

      // FEATURE 4: Populate Pin Button State
      const pinBtn = document.getElementById('revPinBtn');
      if (pinBtn) {
        if (Number(c.is_pinned) === 1) {
          pinBtn.innerHTML = '📌 Pinned to Top';
          pinBtn.style.color = '#f59e0b';
          pinBtn.style.borderColor = 'rgba(245,158,11,0.6)';
          pinBtn.style.background = 'rgba(245,158,11,0.12)';
        } else {
          pinBtn.innerHTML = '📌 Pin to Top';
          pinBtn.style.color = '#94a3b8';
          pinBtn.style.borderColor = 'rgba(148,163,184,0.3)';
          pinBtn.style.background = 'transparent';
        }
        pinBtn.onclick = () => togglePinDirect(c.id, pinBtn);
      }

      // Setup actions
      document.getElementById('revEditorLink').href = '<?= url('/author/editor') ?>?edit_id=' + c.id;
      document.getElementById('revApproveForm').action = '<?= parse_url(url('/admin/content/action/'), PHP_URL_PATH) ?>' + c.id;

      document.getElementById('revFeedbackBtn').onclick = () => {
        closeReviewStation();
        openChangesModal(c.id, c.title, c.editorial_notes || '');
      };

      document.getElementById('revRejectBtn').onclick = () => {
        closeReviewStation();
        openRejectModal(c.id, c.title);
      };

      document.getElementById('revPlagiarismBtn').onclick = () => {
        runPlagiarismScan(c.id, c.title);
      };

      document.getElementById('revContentBody').style.display = 'block';
      if (window.lucide) lucide.createIcons();
    })
    .catch(err => {
      document.getElementById('revLoadingState').style.display = 'none';
      alert('Error fetching submission details from API: ' + err.message);
      modal.style.display = 'none';
    });
}

function closeReviewStation() {
  const modal = document.getElementById('reviewStationModal');
  if (modal) modal.style.display = 'none';
}

function openChangesModal(id, title, notes) {
  document.getElementById('changesForm').action = '<?= url('/admin/content/action/') ?>' + id;
  document.getElementById('changesTitle').value = title;
  document.getElementById('changesNotes').value = notes || '';
  const modal = document.getElementById('changesModal');
  modal.style.display = 'flex';
}

function closeChangesModal() {
  document.getElementById('changesModal').style.display = 'none';
}

function openRejectModal(id, title) {
  document.getElementById('rejectForm').action = '<?= url('/admin/content/action/') ?>' + id;
  document.getElementById('rejectTitle').value = title;
  const modal = document.getElementById('rejectModal');
  modal.style.display = 'flex';
}

function closeRejectModal() {
  document.getElementById('rejectModal').style.display = 'none';
}

function runPlagiarismScan(contentId, title) {
  document.getElementById('plagModalSub').innerText = title;
  document.getElementById('plagLoading').style.display = 'block';
  document.getElementById('plagResults').style.display = 'none';
  document.getElementById('plagiarismModal').style.display = 'flex';

  fetch('<?= url('/admin/content/scan-plagiarism/') ?>' + contentId, {
    method: 'POST',
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(res => res.json())
  .then(data => {
    document.getElementById('plagLoading').style.display = 'none';
    if (!data.success || !data.scan) {
      alert('Plagiarism scan failed: ' + (data.message || 'Unknown error'));
      document.getElementById('plagiarismModal').style.display = 'none';
      return;
    }

    const scan = data.scan;
    const score = parseFloat(scan.originality_score || 100);
    const scoreElem = document.getElementById('plagScoreVal');
    scoreElem.innerText = score.toFixed(1) + '%';
    scoreElem.style.color = score >= 80 ? '#34d399' : (score >= 60 ? '#f59e0b' : '#ef4444');

    const verdictElem = document.getElementById('plagVerdictBadge');
    if (scan.verdict === 'clean') {
      verdictElem.innerHTML = '<span class="ts-badge ts-badge-green">CLEAN & ORIGINAL ✓</span>';
    } else if (scan.verdict === 'acceptable') {
      verdictElem.innerHTML = '<span class="ts-badge ts-badge-cyan">ACCEPTABLE VARIATION</span>';
    } else if (scan.verdict === 'suspicious') {
      verdictElem.innerHTML = '<span class="ts-badge" style="background: rgba(245,158,11,0.2); color: #f59e0b; border: 1px solid rgba(245,158,11,0.4);">⚠️ SUSPICIOUS OVERLAP</span>';
    } else {
      verdictElem.innerHTML = '<span class="ts-badge" style="background: rgba(239,68,68,0.2); color: #ef4444; border: 1px solid rgba(239,68,68,0.4);">🚨 FLAGGED PLAGIARISM</span>';
    }

    // Matching sources
    const sourcesContainer = document.getElementById('plagSourcesList');
    if (scan.matching_sources && scan.matching_sources.length > 0) {
      sourcesContainer.innerHTML = scan.matching_sources.map(s => `
        <div style="background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 6px; padding: 0.75rem; display: flex; justify-content: space-between; align-items: center;">
          <div>
            <div style="color: #f8fafc; font-weight: 600; font-size: 0.85rem;">${s.title}</div>
            <div style="font-size: 0.75rem; color: #64748b;">${s.matching_shingle_count} overlapping shingles</div>
          </div>
          <span class="ts-badge ts-badge-pink">${s.overlap_percent}% Overlap</span>
        </div>
      `).join('');
    } else {
      sourcesContainer.innerHTML = '<div style="color: #34d399; font-size: 0.85rem;">No significant internal overlaps detected.</div>';
    }

    // Snippets
    const snippetsContainer = document.getElementById('plagSnippetsList');
    if (scan.overlapping_snippets && scan.overlapping_snippets.length > 0) {
      snippetsContainer.innerHTML = scan.overlapping_snippets.map(snp => `<div>&bull; "${snp}"</div>`).join('');
    } else {
      snippetsContainer.innerHTML = '<div style="color: #94a3b8;">No matching snippets.</div>';
    }

    document.getElementById('plagResults').style.display = 'block';
  })
  .catch(err => {
    document.getElementById('plagLoading').style.display = 'none';
    alert('Error connecting to scan service.');
    document.getElementById('plagiarismModal').style.display = 'none';
  });
}

function saveInternalNotesFromModal() {
  if (!window.currentReviewedContentId) return;
  const notes = document.getElementById('revInternalNotes').value;
  saveInternalNoteDirect(window.currentReviewedContentId, notes, (success, msg) => {
    if (success) {
      const savedNotice = document.getElementById('revScratchpadSaved');
      if (savedNotice) {
        savedNotice.style.display = 'inline';
        setTimeout(() => { savedNotice.style.display = 'none'; }, 2500);
      }
      if (typeof showToast === 'function') showToast(msg, 'success');
    } else {
      alert('Error saving notes: ' + msg);
    }
  });
}
// [TS-ED-07] Automated Broken Link & Outdated URL Checker (ADMIN ONLY)
function openBrokenLinkScanner() {
  document.getElementById('brokenLinkScannerModal').style.display = 'flex';
  const resultsContainer = document.getElementById('brokenLinkResultsList');
  const loading = document.getElementById('brokenLinkLoading');
  resultsContainer.innerHTML = '';
  loading.style.display = 'block';

  fetch('<?= url('/admin/content/scan-broken-links') ?>')
    .then(r => r.json())
    .then(data => {
      loading.style.display = 'none';
      if (!data.success || !data.issues || data.issues.length === 0) {
        resultsContainer.innerHTML = `
          <div style="text-align: center; padding: 2rem; color: #10b981;">
            <i data-lucide="check-circle-2" style="width: 36px; height: 36px; margin-bottom: 0.5rem;"></i>
            <div style="font-weight: 700;">No broken links, insecure HTTP embeds, or expired URLs detected!</div>
            <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem;">All publication links and assets are healthy.</div>
          </div>
        `;
        if (typeof lucide !== 'undefined') lucide.createIcons();
        return;
      }

      let html = '';
      data.issues.forEach(issue => {
        html += `
          <div style="padding: 0.85rem; border: 1px solid rgba(239,68,68,0.3); border-radius: 8px; background: rgba(239,68,68,0.06); margin-bottom: 0.75rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.35rem;">
              <span class="ts-badge ts-badge-red" style="font-size: 0.68rem;">${issue.issue_label}</span>
              <span style="font-size: 0.75rem; color: #94a3b8;">Article ID #${issue.content_id}</span>
            </div>
            <div style="font-weight: 700; color: #f8fafc; font-size: 0.88rem; margin-bottom: 0.2rem;">${escapeHtml(issue.title)}</div>
            <div style="font-size: 0.8rem; font-family: monospace; color: #f87171; word-break: break-all; margin-bottom: 0.4rem;">${escapeHtml(issue.url)}</div>
            <div style="font-size: 0.75rem; color: #cbd5e1; display: flex; justify-content: space-between; align-items: center;">
              <span>💡 ${escapeHtml(issue.recommendation)}</span>
              <a href="<?= url('/author/editor?id=') ?>${issue.content_id}" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.15rem 0.5rem;">Edit Draft ↗</a>
            </div>
          </div>
        `;
      });
      resultsContainer.innerHTML = html;
      if (typeof lucide !== 'undefined') lucide.createIcons();
    })
    .catch(err => {
      loading.style.display = 'none';
      resultsContainer.innerHTML = `<div style="color: #ef4444; padding: 1rem;">Failed to complete scan. Check server connection.</div>`;
    });
}
</script>

<!-- [TS-ED-07] Broken Link Scanner Modal (Admin Console Only) -->
<div id="brokenLinkScannerModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 999999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="ts-card" style="width: 100%; max-width: 720px; background: #090d16; border: 1px solid rgba(239,68,68,0.4); padding: 2rem; border-radius: 16px; max-height: 85vh; display: flex; flex-direction: column;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
      <h3 style="margin: 0; font-size: 1.25rem; color: #f8fafc; display: flex; align-items: center; gap: 0.5rem;">
        <i data-lucide="link-2-off" style="color: #ef4444;"></i> Automated Broken Link &amp; Outdated URL Checker
      </h3>
      <button type="button" onclick="document.getElementById('brokenLinkScannerModal').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.25rem; cursor: pointer;">✕</button>
    </div>
    <p style="font-size: 0.84rem; color: #94a3b8; margin: 0 0 1rem 0;">
      Scans manuscript drafts and publications across TechScout to identify 404 links, insecure HTTP embeds, and outdated URLs before public indexing.
    </p>

    <div id="brokenLinkLoading" style="display: none; text-align: center; padding: 2.5rem; color: #38bdf8;">
      <i data-lucide="loader-2" class="spin-animation" style="width: 32px; height: 32px; margin-bottom: 0.5rem;"></i>
      <div>Scanning database drafts &amp; publication HTML bodies...</div>
    </div>

    <div id="brokenLinkResultsList" style="flex: 1; overflow-y: auto; max-height: 420px; padding-right: 0.25rem;"></div>

    <div style="display: flex; justify-content: flex-end; margin-top: 1rem; border-top: 1px solid rgba(148,163,184,0.1); padding-top: 0.75rem;">
      <button type="button" onclick="document.getElementById('brokenLinkScannerModal').style.display='none'" class="ts-btn ts-btn-outline ts-btn-sm">Close</button>
    </div>
  </div>
</div>

<?php require __DIR__ . '/components/editorial_badge_modal.php'; ?>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>

