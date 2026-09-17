<?php require __DIR__ . '/../../layouts/admin_header.php'; ?>

<div class="admin-main">
  <div class="admin-topbar">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-cyan">EDITORIAL PIPELINE</span>
        <span style="font-size: 0.8rem; color: #64748b;">Review Submissions, Request Author Changes & Publish</span>
      </div>
      <h1 style="font-size: 1.6rem; color: #f8fafc; margin: 0;">Articles Management & Editorial Queue</h1>
    </div>

    <button onclick="openCreateArticleModal()" class="ts-btn ts-btn-primary">
      <i data-lucide="plus-circle"></i> Create New Article
    </button>
  </div>

  <?php
    $totalArticles = count($articles);
    $inReviewCount = count(array_filter($articles, fn($a) => $a['status'] === 'in_review'));
    $publishedCount = count(array_filter($articles, fn($a) => $a['status'] === 'published'));
    $draftCount = count(array_filter($articles, fn($a) => $a['status'] === 'draft'));
  ?>

  <!-- Stat Grid -->
  <div class="admin-stat-grid">
    <div class="admin-stat-card">
      <div class="admin-stat-label">Total Articles</div>
      <div class="admin-stat-val" style="color: #38bdf8;"><?= number_format($totalArticles) ?></div>
      <div style="font-size: 0.8rem; color: #94a3b8;">All editorial blueprints & guides</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Pending Review</div>
      <div class="admin-stat-val" style="color: #f59e0b;"><?= number_format($inReviewCount) ?></div>
      <div style="font-size: 0.8rem; color: #fbbf24;">Submitted by authors awaiting review</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Live Published</div>
      <div class="admin-stat-val" style="color: #10b981;"><?= number_format($publishedCount) ?></div>
      <div style="font-size: 0.8rem; color: #34d399;">Publicly discoverable</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Unpublished Drafts</div>
      <div class="admin-stat-val" style="color: #64748b;"><?= number_format($draftCount) ?></div>
      <div style="font-size: 0.8rem; color: #94a3b8;">Work in progress</div>
    </div>
  </div>

  <!-- Filter Ribbon -->
  <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
      <button type="button" onclick="filterArticles('all')" class="ts-btn ts-btn-sm ts-btn-primary art-filter-btn" id="filter-btn-all">All Articles (<?= $totalArticles ?>)</button>
      <button type="button" onclick="filterArticles('in_review')" class="ts-btn ts-btn-sm ts-btn-outline art-filter-btn" id="filter-btn-in_review" style="color: #f59e0b;">Pending Review (<?= $inReviewCount ?>)</button>
      <button type="button" onclick="filterArticles('published')" class="ts-btn ts-btn-sm ts-btn-outline art-filter-btn" id="filter-btn-published" style="color: #10b981;">Published (<?= $publishedCount ?>)</button>
      <button type="button" onclick="filterArticles('draft')" class="ts-btn ts-btn-sm ts-btn-outline art-filter-btn" id="filter-btn-draft">Drafts (<?= $draftCount ?>)</button>
    </div>
    <div style="position: relative;">
      <input type="text" id="articleSearchInput" placeholder="🔍 Search articles, authors, slugs..." class="ts-input" style="padding: 0.45rem 0.85rem; font-size: 0.84rem; width: 280px; background: #05070b; border: 1px solid rgba(148,163,184,0.25); border-radius: 8px; color: #f8fafc;">
    </div>
  </div>

  <!-- Articles Table -->
  <div class="admin-table-wrap">
    <table class="admin-table" id="articlesCrudTable" data-paginate="true" data-search-id="articleSearchInput" data-page-size="10">
      <thead>
        <tr>
          <th>Article Details</th>
          <th>Category</th>
          <th>Author Attribution</th>
          <th>Quality & Metrics</th>
          <th>Status & Feedback</th>
          <th>Editorial Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($articles as $art): ?>
          <tr class="article-row" data-status="<?= htmlspecialchars($art['status']) ?>">
            <td>
              <div style="display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap; margin-bottom: 0.25rem;">
                <?php if (!empty($art['is_pinned'])): ?>
                  <span class="ts-badge" style="background: rgba(245,158,11,0.15); color: #f59e0b; border: 1px solid rgba(245,158,11,0.4); font-size: 0.65rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.2rem;">
                    📌 PINNED
                  </span>
                <?php endif; ?>

                <?php if (!empty($art['custom_badge'])): ?>
                  <span class="ts-badge" style="background: <?= htmlspecialchars($art['badge_bg'] ?: 'rgba(56,189,248,0.15)') ?>; color: <?= htmlspecialchars($art['badge_color'] ?: '#38bdf8') ?>; border: 1px solid <?= htmlspecialchars($art['badge_color'] ?: '#38bdf8') ?>50; font-size: 0.65rem; font-weight: 700;">
                    ⭐ <?= htmlspecialchars($art['custom_badge']) ?>
                  </span>
                <?php endif; ?>
              </div>

              <div style="font-weight: 600; color: #f8fafc; font-size: 0.95rem;">
                <?php if ($art['status'] === 'published'): ?>
                  <a href="<?= url('/article/' . $art['slug']) ?>" target="_blank" style="color: #f8fafc; text-decoration: none;">
                    <?= htmlspecialchars($art['title']) ?> <i data-lucide="external-link" style="width: 12px; height: 12px; display: inline;"></i>
                  </a>
                <?php else: ?>
                  <span><?= htmlspecialchars($art['title']) ?></span>
                <?php endif; ?>
              </div>
              <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">
                Slug: <code><?= htmlspecialchars($art['slug']) ?></code> &bull; <?= $art['reading_time_mins'] ?? 5 ?> min read
              </div>
              <?php if (!empty($art['internal_notes'])): ?>
                <div style="margin-top: 0.35rem; font-size: 0.73rem; background: rgba(99,102,241,0.1); border-left: 3px solid #818cf8; padding: 0.25rem 0.5rem; color: #c7d2fe; border-radius: 0 4px 4px 0;" title="<?= htmlspecialchars($art['internal_notes']) ?>">
                  <i data-lucide="lock" style="width: 10px; height: 10px; display: inline;"></i> <strong>Internal Note:</strong> <?= htmlspecialchars(mb_strimwidth($art['internal_notes'], 0, 50, '...')) ?>
                </div>
              <?php endif; ?>
            </td>

            <td><span class="ts-badge"><?= htmlspecialchars($art['category_name'] ?? 'Editorial') ?></span></td>

            <td>
              <div style="color: #38bdf8; font-weight: 600; font-size: 0.85rem;">
                <?= htmlspecialchars($art['author_name'] ?? 'Staff Contributor') ?>
              </div>
            </td>

            <td>
              <div style="font-size: 0.8rem; color: #94a3b8;">
                Score: <strong style="color: #34d399;"><?= round((float)$art['quality_score']) ?>%</strong>
              </div>
              <div style="font-size: 0.75rem; color: #64748b;">
                👁 <?= number_format($art['view_count']) ?> &bull; 🔥 <?= $art['reaction_count'] ?> &bull; 💬 <?= $art['comment_count'] ?>
              </div>
            </td>

            <td>
              <?php 
                $statusColor = match($art['status']) {
                  'published' => 'ts-badge-green',
                  'in_review' => 'ts-badge-pink',
                  'rejected' => 'strike-badge',
                  default => ''
                };
              ?>
              <span class="ts-badge <?= $statusColor ?>"><?= strtoupper(str_replace('_', ' ', $art['status'])) ?></span>
              <?php if (!empty($art['editorial_notes'])): ?>
                <div style="font-size: 0.72rem; color: #f59e0b; margin-top: 0.25rem; max-width: 180px;" title="<?= htmlspecialchars($art['editorial_notes']) ?>">
                  <i data-lucide="message-square" style="width: 11px; display: inline;"></i> Notes: <?= htmlspecialchars(mb_strimwidth($art['editorial_notes'], 0, 35, '...')) ?>
                </div>
              <?php endif; ?>
            </td>

            <td>
              <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                <a href="<?= url('/admin/analytics/content/' . $art['id']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; color: #38bdf8; border-color: rgba(56,189,248,0.3);" title="View deep telemetry and analytics">
                  📊 Stats
                </a>

                <!-- Quick Pin to Top (Feature 4) -->
                <button type="button" onclick="togglePinDirect(<?= (int)$art['id'] ?>, this)" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.45rem; font-size: 0.75rem; <?= !empty($art['is_pinned']) ? 'color: #f59e0b; border-color: rgba(245,158,11,0.6); background: rgba(245,158,11,0.1);' : 'color: #64748b;' ?>" title="<?= !empty($art['is_pinned']) ? 'Pinned to top (Click to unpin)' : 'Click to pin to top of category & feed' ?>">
                  📌
                </button>

                <!-- Custom Editorial Badge (Feature 10) -->
                <button type="button" onclick="openBadgePickerModal(<?= (int)$art['id'] ?>, '<?= htmlspecialchars(addslashes($art['custom_badge'] ?? '')) ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.45rem; font-size: 0.75rem; <?= !empty($art['custom_badge']) ? 'color: ' . htmlspecialchars($art['badge_color']) . '; border-color: ' . htmlspecialchars($art['badge_color']) . '60;' : 'color: #94a3b8;' ?>" title="Assign Custom Editorial Badge">
                  ⭐
                </button>

                <button type="button" onclick="openReviewModal(<?= (int)$art['id'] ?>)" class="ts-btn ts-btn-primary ts-btn-sm" style="padding: 0.25rem 0.6rem; font-size: 0.75rem; background: linear-gradient(135deg, #6366f1, #38bdf8);">
                  <i data-lucide="check-square" style="width: 12px; height: 12px;"></i> Review 👁️
                </button>

                <button type="button" onclick="editArticleById(<?= (int)$art['id'] ?>)" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                  Edit
                </button>

                <form action="<?= url('/admin/articles/delete/' . $art['id']) ?>" method="POST" onsubmit="return confirm('Permanently delete this article?');" style="display: inline;">
                  <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; color: #ef4444; border-color: rgba(239,68,68,0.3);">
                    Delete
                  </button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Dedicated Editorial Review & Decision Modal -->
<div id="articleReviewModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 99999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-table-wrap" style="width: 100%; max-width: 820px; max-height: 90vh; overflow-y: auto; padding: 2rem; background: #090d16; border: 1px solid rgba(99,102,241,0.35); box-shadow: 0 25px 60px rgba(0,0,0,0.9);">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem; border-bottom: 1px solid rgba(148,163,184,0.15); padding-bottom: 0.75rem;">
      <div>
        <span class="ts-badge ts-badge-cyan" style="font-size: 0.7rem; text-transform: uppercase;">Editorial Review Station</span>
        <h2 id="reviewArticleTitle" style="font-size: 1.35rem; color: #f8fafc; margin: 0.25rem 0 0 0;">Article Review</h2>
        <div id="reviewArticleMeta" style="font-size: 0.8rem; color: #64748b; margin-top: 0.2rem;">By Author • Category</div>
      </div>
      <button onclick="document.getElementById('articleReviewModal').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer;">&times;</button>
    </div>

    <!-- Summary Box -->
    <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(148,163,184,0.12); border-radius: 8px; padding: 1rem; margin-bottom: 1.25rem;">
      <div style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.35rem;">Summary / Abstract:</div>
      <p id="reviewArticleSummary" style="color: #cbd5e1; font-size: 0.9rem; margin: 0; line-height: 1.5;"></p>
    </div>

    <!-- Scrollable Content Preview -->
    <div style="margin-bottom: 1.5rem;">
      <div style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.35rem;">Full Document Body Preview:</div>
      <div id="reviewArticleBodyPreview" class="preview-article-body" style="background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 1.5rem; max-height: 480px; overflow-y: auto; color: #e2e8f0; font-size: 0.95rem; line-height: 1.7;"></div>
    </div>

    <!-- FEATURE 9 & 10 & 4: EDITORIAL INTERNAL SCRATCHPAD & BADGE / PIN CONTROLS -->
    <div style="background: rgba(99,102,241,0.08); border: 1px solid rgba(99,102,241,0.3); border-radius: 8px; padding: 1rem; margin-bottom: 1.25rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
        <span style="font-size: 0.78rem; text-transform: uppercase; color: #818cf8; font-weight: 700; display: flex; align-items: center; gap: 0.35rem;">
          <i data-lucide="lock" style="width: 12px; height: 12px;"></i> Internal Scratchpad (Private to Admins)
        </span>
        <span id="artScratchpadSaved" style="font-size: 0.72rem; color: #10b981; display: none; font-weight: 700;">Saved ✓</span>
      </div>
      <textarea id="artInternalNotes" rows="2" placeholder="Private internal comments for staff only (never shown to author or public)..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 6px; padding: 0.5rem; color: #f8fafc; font-size: 0.85rem; margin-bottom: 0.55rem;"></textarea>
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
        <button type="button" onclick="saveArtInternalNotes()" class="ts-btn ts-btn-sm ts-btn-primary" style="font-size: 0.75rem; padding: 0.3rem 0.75rem; background: #6366f1;">
          Save Private Notes 📝
        </button>
        <div style="display: flex; gap: 0.4rem; align-items: center; flex-wrap: wrap;">
          <span id="artCurrentBadgeDisplay"></span>
          <button type="button" id="artAssignBadgeBtn" onclick="openBadgePickerModal(window.currentReviewedArticleId)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.55rem; color: #38bdf8; border-color: rgba(56,189,248,0.4);">
            ⭐ Custom Badge
          </button>
          <button type="button" id="artPinBtn" onclick="togglePinDirect(window.currentReviewedArticleId, this)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.55rem; color: #f59e0b; border-color: rgba(245,158,11,0.4);">
            📌 Pin to Top
          </button>
        </div>
      </div>
    </div>

    <!-- Decision Form -->
    <form id="reviewDecisionForm" method="POST" action="">
      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">
          <i data-lucide="message-square" style="width: 14px; display: inline; color: #f59e0b;"></i> Editorial Feedback / Change Request Notes (Visible to Author):
        </label>
        <textarea name="editorial_notes" id="reviewEditorialNotes" rows="3" placeholder="Provide specific feedback to author (e.g. 'Please add architectural diagram, expand section 2 with benchmark results, and fix code block formatting...')" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.75rem; border-radius: 6px; color: #f8fafc; font-size: 0.9rem;"></textarea>
      </div>

      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; border-top: 1px solid rgba(148,163,184,0.12); padding-top: 1.25rem;">
        <button type="submit" name="decision" value="reject" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.4);" onclick="return confirm('Reject this article submission?');">
          ❌ Reject Submission
        </button>

        <div style="display: flex; gap: 0.75rem;">
          <button type="submit" name="decision" value="request_changes" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #f59e0b; border-color: rgba(245,158,11,0.5);">
            📋 Request Changes from Author
          </button>
          <button type="submit" name="decision" value="publish" class="ts-btn ts-btn-primary ts-btn-sm" style="font-weight: 700; background: #10b981; border-color: #10b981;">
            🚀 Approve & Publish Live
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Article Create/Edit Modal -->
<div id="articleFormModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-table-wrap" style="width: 100%; max-width: 900px; max-height: 90vh; overflow-y: auto; padding: 2rem; background: #090d16; border: 1px solid rgba(99,102,241,0.3);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
      <h2 id="modalArticleTitle" style="font-size: 1.35rem; color: #f8fafc; margin: 0;">Create / Edit Article</h2>
      <button onclick="document.getElementById('articleFormModal').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer;">&times;</button>
    </div>

    <form id="adminArticleForm" action="<?= url('/admin/articles/save') ?>" method="POST">
      <input type="hidden" name="id" id="article_id" value="">

      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Article Title <span style="font-size: 0.75rem; color: #94a3b8; font-weight: normal;">(Max 150 chars)</span></label>
        <input type="text" name="title" id="article_title" maxlength="150" placeholder="e.g. Architecting Distributed Microservices on AWS" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc; font-size: 1rem;" required>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Category</label>
          <select name="category_id" id="article_category_id" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Author Attribution</label>
          <select name="author_id" id="article_author_id" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
            <?php foreach ($authors as $aut): ?>
              <option value="<?= $aut['id'] ?>"><?= htmlspecialchars($aut['display_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
          <label style="font-size: 0.85rem; font-weight: 600; color: #f8fafc;">Featured Cover Image URL</label>
          <label class="ts-btn ts-btn-outline ts-btn-sm" style="cursor: pointer; padding: 0.15rem 0.5rem; font-size: 0.72rem; border-color: #38bdf8; color: #38bdf8; display: inline-flex; align-items: center; gap: 0.25rem;">
            <i data-lucide="upload" style="width: 12px; height: 12px;"></i> Upload Cover
            <input type="file" accept="image/*" onchange="uploadImageToInput(this, 'article_featured_image_url', 'covers')" style="display: none;">
          </label>
        </div>
        <input type="text" name="featured_image_url" id="article_featured_image_url" placeholder="https://images.unsplash.com/... or /storage/uploads/..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Summary / Excerpt <span style="font-size: 0.75rem; color: #94a3b8; font-weight: normal;">(Max 300 chars)</span></label>
        <textarea name="summary" id="article_summary" maxlength="300" rows="2" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc; resize: vertical;" required></textarea>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Article Body (HTML / Markdown Supported)</label>
        <textarea name="body_html" id="article_body_html" rows="10" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc; font-family: var(--ts-font-mono); font-size: 0.9rem; resize: vertical;" required></textarea>
      </div>

      <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
        <div>
          <label style="display: block; font-size: 0.8rem; color: #64748b; margin-bottom: 0.3rem;">Status</label>
          <select name="status" id="article_status" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.5rem; border-radius: 6px; color: #f8fafc;">
            <option value="published">Published</option>
            <option value="draft">Draft</option>
            <option value="in_review">In Review</option>
          </select>
        </div>

        <div>
          <label style="display: block; font-size: 0.8rem; color: #64748b; margin-bottom: 0.3rem;">Difficulty</label>
          <select name="difficulty_level" id="article_difficulty" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.5rem; border-radius: 6px; color: #f8fafc;">
            <option value="beginner">Beginner</option>
            <option value="intermediate" selected>Intermediate</option>
            <option value="advanced">Advanced</option>
            <option value="expert">Expert</option>
          </select>
        </div>

        <div style="display: flex; align-items: center; gap: 0.4rem; padding-top: 1.2rem;">
          <input type="checkbox" name="is_featured" id="article_is_featured" value="1" style="accent-color: #6366f1;">
          <label for="article_is_featured" style="font-size: 0.85rem; color: #f8fafc; cursor: pointer;">Featured ★</label>
        </div>

        <div style="display: flex; align-items: center; gap: 0.4rem; padding-top: 1.2rem;">
          <input type="checkbox" name="is_trending" id="article_is_trending" value="1" style="accent-color: #ec4899;">
          <label for="article_is_trending" style="font-size: 0.85rem; color: #f8fafc; cursor: pointer;">Trending 🔥</label>
        </div>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
        <button type="button" onclick="document.getElementById('articleFormModal').style.display='none'" class="ts-btn ts-btn-outline">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary">Save & Publish Article 🚀</button>
      </div>
    </form>
  </div>
</div>

<script>
function filterArticles(status) {
  document.querySelectorAll('.art-filter-btn').forEach(btn => {
    btn.classList.remove('ts-btn-primary');
    btn.classList.add('ts-btn-outline');
  });
  const activeBtn = document.getElementById(`filter-btn-${status}`);
  if (activeBtn) {
    activeBtn.classList.remove('ts-btn-outline');
    activeBtn.classList.add('ts-btn-primary');
  }

  document.querySelectorAll('.article-row').forEach(row => {
    if (status === 'all' || row.dataset.status === status) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
}

function openReviewModal(art) {
  const modal = document.getElementById('articleReviewModal');
  if (typeof art === 'number' || typeof art === 'string') {
    modal.style.display = 'flex';
    document.getElementById('reviewArticleTitle').innerText = 'Loading article details...';
    document.getElementById('reviewArticleMeta').innerText = '';
    document.getElementById('reviewArticleSummary').innerText = 'Retrieving summary...';
    document.getElementById('reviewArticleBodyPreview').innerHTML = '<div style="text-align:center; padding: 2rem; color: #94a3b8;"><i data-lucide="loader-2" style="animation: spin 1s linear infinite; display: inline-block;"></i> Loading preview...</div>';
    if (window.lucide) lucide.createIcons();
    
    const previewPath = '<?= parse_url(url('/api/v1/content/preview'), PHP_URL_PATH) ?>/' + art;
    fetch(previewPath)
      .then(res => res.json())
      .then(data => {
        if (!data.success || !data.content) {
          alert('Failed to load article details.');
          modal.style.display = 'none';
          return;
        }
        const c = data.content;
        window.currentReviewedArticleId = c.id;

        document.getElementById('reviewArticleTitle').innerText = c.title || 'Untitled Article';
        document.getElementById('reviewArticleMeta').innerText = `Author: ${c.author_name || 'Staff'} • Category: ${c.category_name || 'General'} • Quality: ${Math.round(c.quality_score || 85)}%`;
        document.getElementById('reviewArticleSummary').innerText = c.summary || 'No summary provided.';
        document.getElementById('reviewArticleBodyPreview').innerHTML = c.body_html || c.summary || '<p style="color: #64748b;">No body content.</p>';
        document.getElementById('reviewEditorialNotes').value = c.editorial_notes || '';
        document.getElementById('reviewDecisionForm').action = `<?= parse_url(url('/admin/articles/review'), PHP_URL_PATH) ?>/${c.id}`;

        // FEATURE 9: Populate Scratchpad
        const scratchpad = document.getElementById('artInternalNotes');
        if (scratchpad) scratchpad.value = c.internal_notes || '';
        const savedNotice = document.getElementById('artScratchpadSaved');
        if (savedNotice) savedNotice.style.display = 'none';

        // FEATURE 10: Populate Custom Badge
        const badgeDisplay = document.getElementById('artCurrentBadgeDisplay');
        if (badgeDisplay) {
          if (c.custom_badge) {
            badgeDisplay.innerHTML = `<span class="ts-badge" style="background: ${c.badge_bg || 'rgba(56,189,248,0.15)'}; color: ${c.badge_color || '#38bdf8'}; border: 1px solid ${c.badge_color || '#38bdf8'}50; font-weight: 700; font-size: 0.75rem;">⭐ ${c.custom_badge}</span>`;
          } else {
            badgeDisplay.innerHTML = `<span class="ts-badge" style="color: #64748b; border: 1px dashed #64748b; font-size: 0.72rem;">No Badge</span>`;
          }
        }

        // FEATURE 4: Populate Pin Button
        const pinBtn = document.getElementById('artPinBtn');
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

        if (window.lucide) lucide.createIcons();
      })
      .catch(err => {
        alert('Error fetching article: ' + err.message);
        modal.style.display = 'none';
      });
    return;
  }

  document.getElementById('reviewArticleTitle').innerText = art.title || 'Untitled Article';
  document.getElementById('reviewArticleMeta').innerText = `Author: ${art.author_name || 'Staff'} • Category: ${art.category_name || 'General'}`;
  document.getElementById('reviewArticleSummary').innerText = art.summary || 'No summary provided.';
  document.getElementById('reviewArticleBodyPreview').innerHTML = art.body_html || '<p style="color: #64748b;">No body content.</p>';
  document.getElementById('reviewEditorialNotes').value = art.editorial_notes || '';
  
  document.getElementById('reviewDecisionForm').action = `<?= parse_url(url('/admin/articles/review'), PHP_URL_PATH) ?>/${art.id}`;
  modal.style.display = 'flex';
}

function saveArtInternalNotes() {
  if (!window.currentReviewedArticleId) return;
  const notes = document.getElementById('artInternalNotes').value;
  saveInternalNoteDirect(window.currentReviewedArticleId, notes, (success, msg) => {
    if (success) {
      const el = document.getElementById('artScratchpadSaved');
      if (el) {
        el.style.display = 'inline';
        setTimeout(() => { el.style.display = 'none'; }, 2500);
      }
      if (typeof showToast === 'function') showToast(msg, 'success');
    } else {
      alert('Error saving notes: ' + msg);
    }
  });
}

function openCreateArticleModal() {
  document.getElementById('adminArticleForm').reset();
  document.getElementById('article_id').value = '';
  document.getElementById('modalArticleTitle').innerText = 'Create New Technical Article';
  document.getElementById('articleFormModal').style.display = 'flex';
}

function editArticleById(artId) {
  const previewPath = '<?= parse_url(url('/api/v1/content/preview'), PHP_URL_PATH) ?>/' + artId;
  fetch(previewPath)
    .then(res => res.json())
    .then(data => {
      if (!data.success || !data.content) {
        alert('Failed to load article for editing.');
        return;
      }
      editArticle(data.content);
    })
    .catch(err => alert('Error loading article: ' + err.message));
}

function editArticle(art) {
  document.getElementById('article_id').value = art.id;
  document.getElementById('article_title').value = art.title || '';
  document.getElementById('article_category_id').value = art.category_id || 1;
  document.getElementById('article_author_id').value = art.author_id || 1;
  document.getElementById('article_featured_image_url').value = art.featured_image_url || '';
  document.getElementById('article_summary').value = art.summary || '';
  document.getElementById('article_body_html').value = art.body_html || '';
  document.getElementById('article_status').value = art.status || 'published';
  document.getElementById('article_difficulty').value = art.difficulty_level || 'intermediate';
  document.getElementById('article_is_featured').checked = !!parseInt(art.is_featured);
  document.getElementById('article_is_trending').checked = !!parseInt(art.is_trending);
  document.getElementById('modalArticleTitle').innerText = 'Edit Article #' + art.id;
  document.getElementById('articleFormModal').style.display = 'flex';
}

function filterArticles(status) {
  document.querySelectorAll('.art-filter-btn').forEach(btn => {
    btn.classList.remove('ts-btn-primary');
    btn.classList.add('ts-btn-outline');
  });
  const activeBtn = document.getElementById('filter-btn-' + status);
  if (activeBtn) {
    activeBtn.classList.remove('ts-btn-outline');
    activeBtn.classList.add('ts-btn-primary');
  }

  const rows = document.querySelectorAll('#articlesCrudTable tbody tr.article-row');
  rows.forEach(row => {
    if (status === 'all' || row.getAttribute('data-status') === status) {
      row.classList.remove('no-paginate');
    } else {
      row.classList.add('no-paginate');
    }
  });

  if (window.articlesPager) {
    window.articlesPager.refresh();
  }
}

document.addEventListener('DOMContentLoaded', function() {
  window.articlesPager = window.paginateAdminTable('articlesCrudTable', {
    pageSize: 10,
    searchInputId: 'articleSearchInput'
  });

  const form = document.getElementById('adminArticleForm');
  if (form) {
    form.addEventListener('submit', function(e) {
      const title = (document.getElementById('article_title').value || '').trim();
      if (title.length < 3) {
        e.preventDefault();
        alert('Article title must be at least 3 characters.');
        document.getElementById('article_title').focus();
        return false;
      }
      const cat = document.getElementById('article_category_id').value;
      if (!cat) {
        e.preventDefault();
        alert('Please select an article category.');
        return false;
      }
      const summary = (document.getElementById('article_summary').value || '').trim();
      if (summary.length < 10) {
        e.preventDefault();
        alert('Please provide an article summary of at least 10 characters.');
        document.getElementById('article_summary').focus();
        return false;
      }
      const body = (document.getElementById('article_body_html').value || '').trim();
      if (body.length < 20) {
        e.preventDefault();
        alert('Article body content is too short. Please provide comprehensive technical documentation.');
        document.getElementById('article_body_html').focus();
        return false;
      }
      return true;
    });
  }
});
</script>

<?php require __DIR__ . '/../components/editorial_badge_modal.php'; ?>

<?php require __DIR__ . '/../../layouts/admin_footer.php'; ?>
