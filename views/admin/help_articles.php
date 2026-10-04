<?php
$title = 'Help Center Articles Management | TechScout Admin';
require __DIR__ . '/../layouts/admin_header.php';
?>

<div class="admin-content-inner">
  <!-- Page Header -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-cyan" style="font-size: 0.68rem; font-weight: 800;">KNOWLEDGE BASE CMS</span>
        <span style="color: #64748b; font-size: 0.8rem;">YouTube &amp; TikTok Style Documentation Engine</span>
      </div>
      <h1 style="font-size: 1.65rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        📚 Help Center Article Management
      </h1>
      <p style="color: #94a3b8; font-size: 0.85rem; margin: 0.25rem 0 0 0;">
        Create, edit, organize categories, step-by-step guides, interactive FAQs, and badges displayed on <a href="<?= url('/help') ?>" target="_blank" style="color: #38bdf8; text-decoration: underline;">/help ↗</a>.
      </p>
    </div>

    <div style="display: flex; gap: 0.65rem; align-items: center; flex-wrap: wrap;">
      <a href="<?= url('/help') ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.35);">
        <i data-lucide="external-link"></i> Live Help Center ↗
      </a>
      <a href="<?= url('/admin/help-articles/create') ?>" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #6366f1, #38bdf8); font-weight: 700;">
        <i data-lucide="plus-circle"></i> + New Help Article
      </a>
    </div>
  </div>

  <!-- Search & Filter Controls -->
  <div class="admin-table-wrap" style="padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; background: rgba(15,23,42,0.7);">
    <form action="<?= url('/admin/help-articles') ?>" method="GET" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
      <div style="flex: 1; min-width: 240px;">
        <input type="text" name="q" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Search articles by title, slug, summary..." class="admin-modal-input" style="font-size: 0.85rem; padding: 0.45rem 0.85rem;">
      </div>

      <div style="min-width: 180px;">
        <select name="cat" class="admin-modal-select" style="font-size: 0.85rem; padding: 0.45rem 0.85rem;">
          <option value="">-- All Categories (<?= count($articles) ?>) --</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= htmlspecialchars($cat['category_slug']) ?>" <?= ($selectedCategory ?? '') === $cat['category_slug'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['category_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div style="min-width: 140px;">
        <select name="status" class="admin-modal-select" style="font-size: 0.85rem; padding: 0.45rem 0.85rem;">
          <option value="">All Statuses</option>
          <option value="published" <?= ($selectedStatus ?? '') === 'published' ? 'selected' : '' ?>>Published Only</option>
          <option value="draft" <?= ($selectedStatus ?? '') === 'draft' ? 'selected' : '' ?>>Drafts Only</option>
        </select>
      </div>

      <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #38bdf8; color: #020617; font-weight: 700;">
        <i data-lucide="filter"></i> Filter
      </button>

      <?php if (!empty($search) || !empty($selectedCategory) || !empty($selectedStatus)): ?>
        <a href="<?= url('/admin/help-articles') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #94a3b8;">
          Clear Filters
        </a>
      <?php endif; ?>
    </form>
  </div>

  <!-- Articles Table -->
  <div class="admin-table-wrap">
    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
      <h3 style="font-size: 1.1rem; color: #f8fafc; margin: 0; font-weight: 700;">
        Article Directory (<?= count($articles) ?> Total)
      </h3>
      <span style="font-size: 0.8rem; color: #64748b;">Sorted by Category &amp; Sort Order</span>
    </div>

    <div style="overflow-x: auto;">
      <table class="admin-table">
        <thead>
          <tr>
            <th style="width: 50px;">Order</th>
            <th>Article Title &amp; Slug</th>
            <th>Category</th>
            <th>Badge</th>
            <th>Read Time</th>
            <th>Status</th>
            <th>Featured</th>
            <th style="text-align: right; width: 140px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($articles)): ?>
            <?php foreach ($articles as $art): ?>
              <tr>
                <td style="color: #64748b; font-family: monospace; font-size: 0.8rem;">
                  <?= (int)$art['sort_order'] ?>
                </td>
                <td>
                  <div style="font-weight: 700; color: #f8fafc; font-size: 0.92rem; margin-bottom: 0.15rem;">
                    <?= htmlspecialchars($art['title']) ?>
                  </div>
                  <div style="font-family: monospace; font-size: 0.72rem; color: #38bdf8;">
                    /help/<?= htmlspecialchars($art['slug']) ?>
                  </div>
                  <?php if (!empty($art['summary'])): ?>
                    <div style="color: #94a3b8; font-size: 0.78rem; margin-top: 0.2rem; max-width: 450px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                      <?= htmlspecialchars($art['summary']) ?>
                    </div>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="ts-badge" style="background: rgba(99,102,241,0.12); color: <?= htmlspecialchars($art['category_color'] ?? '#6366f1') ?>; border: 1px solid <?= htmlspecialchars($art['category_color'] ?? '#6366f1') ?>40; font-size: 0.72rem;">
                    <?= htmlspecialchars($art['category_name']) ?>
                  </span>
                </td>
                <td>
                  <span class="ts-badge ts-badge-cyan" style="font-size: 0.7rem;">
                    <?= htmlspecialchars($art['badge'] ?: 'GUIDE') ?>
                  </span>
                </td>
                <td style="color: #94a3b8; font-size: 0.8rem;">
                  <?= htmlspecialchars($art['read_time'] ?: '3 min') ?>
                </td>
                <td>
                  <?php if (!empty($art['is_published'])): ?>
                    <span class="ts-badge ts-badge-green" style="font-size: 0.7rem;">PUBLISHED</span>
                  <?php else: ?>
                    <span class="ts-badge ts-badge-amber" style="font-size: 0.7rem;">DRAFT</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (!empty($art['is_featured'])): ?>
                    <span style="color: #f59e0b; font-size: 0.85rem;" title="Featured Article">★ Yes</span>
                  <?php else: ?>
                    <span style="color: #64748b; font-size: 0.8rem;">—</span>
                  <?php endif; ?>
                </td>
                <td style="text-align: right;">
                  <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.35rem; justify-content: flex-end;">
                    <a href="<?= url('/help/' . $art['slug']) ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.72rem; color: #38bdf8;" title="View Live">
                      👁️
                    </a>
                    <a href="<?= url('/admin/help-articles/edit/' . $art['id']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.55rem; font-size: 0.72rem; color: #a855f7;" title="Edit Article">
                      ✏️ Edit
                    </a>
                    <form action="<?= url('/admin/help-articles/delete/' . $art['id']) ?>" method="POST" onsubmit="return confirm('Permanently delete article \'<?= htmlspecialchars(addslashes($art['title'])) ?>\'?');" style="display: inline;">
                      <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.72rem; color: #ef4444;" title="Delete Article">
                        🗑️
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" style="text-align: center; padding: 3rem; color: #64748b;">
                No help articles found matching your criteria. <a href="<?= url('/admin/help-articles/create') ?>" style="color: #38bdf8;">Create one now &rarr;</a>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
