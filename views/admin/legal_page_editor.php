<?php
$title = 'Edit Legal Page: ' . htmlspecialchars($page['title']) . ' | TechScout Admin';
require __DIR__ . '/../layouts/admin_header.php';
?>

<div class="admin-content-inner">
  <!-- Page Header -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <a href="<?= url('/admin/legal-pages') ?>" style="color: #38bdf8; text-decoration: none; font-size: 0.8rem; font-weight: 600;">
          &larr; Back to Legal Pages Hub
        </a>
      </div>
      <h1 style="font-size: 1.65rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        ⚖️ Edit: <?= htmlspecialchars($page['title']) ?>
      </h1>
      <p style="color: #94a3b8; font-size: 0.85rem; margin: 0.25rem 0 0 0;">
        Modifying legal agreement content for live endpoint: <a href="<?= url('/' . $page['slug']) ?>" target="_blank" style="color: #38bdf8; font-family: monospace;">/<?= htmlspecialchars($page['slug']) ?> ↗</a>
      </p>
    </div>

    <div style="display: flex; gap: 0.5rem; align-items: center;">
      <a href="<?= url('/' . $page['slug']) ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8;">
        <i data-lucide="external-link"></i> Preview Live ↗
      </a>
      <button type="submit" form="legalPageForm" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #10b981, #059669); font-weight: 800; padding: 0.55rem 1.5rem;">
        <i data-lucide="save"></i> Save Legal Page 💾
      </button>
    </div>
  </div>

  <form action="<?= url('/admin/legal-pages/save') ?>" method="POST" id="legalPageForm">
    <input type="hidden" name="id" value="<?= (int)$page['id'] ?>">

    <div style="display: grid; grid-template-columns: 2.2fr 1fr; gap: 1.5rem; align-items: start;">
      
      <!-- Main Content Column -->
      <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        <div class="admin-table-wrap" style="padding: 1.75rem;">
          <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 1.25rem 0; font-weight: 700; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 0.75rem;">
            📜 Policy Details &amp; Hero Section
          </h3>

          <div style="margin-bottom: 1.25rem;">
            <label class="admin-modal-label">Policy Title <span style="color: #ef4444;">*</span></label>
            <input type="text" name="title" value="<?= htmlspecialchars($page['title']) ?>" class="admin-modal-input" required>
          </div>

          <div style="margin-bottom: 1.25rem;">
            <label class="admin-modal-label">Subtitle / Executive Summary</label>
            <textarea name="subtitle" rows="3" class="admin-modal-input" placeholder="Prominent introductory paragraph rendered inside the hero banner..."><?= htmlspecialchars($page['subtitle'] ?? '') ?></textarea>
          </div>

          <div style="margin-bottom: 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
              <label class="admin-modal-label" style="margin-bottom: 0;">Complete Legal Document Content (HTML) <span style="color: #ef4444;">*</span></label>
              <span style="font-size: 0.75rem; color: #94a3b8;">Supports HTML tags, tables, headings &amp; lists</span>
            </div>
            <textarea name="content_html" rows="24" class="admin-modal-input" style="font-family: 'JetBrains Mono', monospace; font-size: 0.88rem; line-height: 1.65; white-space: pre;" required><?= htmlspecialchars($page['content_html']) ?></textarea>
          </div>
        </div>

      </div>

      <!-- Settings & Metadata Column -->
      <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        <div class="admin-table-wrap" style="padding: 1.75rem;">
          <h3 style="font-size: 1.1rem; color: #f8fafc; margin: 0 0 1rem 0; font-weight: 700; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 0.5rem;">
            ⚙️ Compliance Metadata
          </h3>

          <div style="margin-bottom: 1rem;">
            <label class="admin-modal-label">Permanent Slug Key</label>
            <input type="text" value="<?= htmlspecialchars($page['slug']) ?>" class="admin-modal-input" style="font-family: monospace; background: rgba(0,0,0,0.4);" readonly>
            <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.25rem;">Slug routing is immutable for compliance preservation.</div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
            <div>
              <label class="admin-modal-label">Badge Label</label>
              <input type="text" name="badge" value="<?= htmlspecialchars($page['badge'] ?? 'LEGAL & COMPLIANCE') ?>" class="admin-modal-input">
            </div>
            <div>
              <label class="admin-modal-label">Badge Color</label>
              <select name="badge_color" class="admin-modal-select">
                <option value="indigo" <?= ($page['badge_color'] ?? '') === 'indigo' ? 'selected' : '' ?>>Indigo (Standard)</option>
                <option value="cyan" <?= ($page['badge_color'] ?? '') === 'cyan' ? 'selected' : '' ?>>Cyan (Intellectual)</option>
                <option value="pink" <?= ($page['badge_color'] ?? '') === 'pink' ? 'selected' : '' ?>>Pink (Conduct)</option>
                <option value="emerald" <?= ($page['badge_color'] ?? '') === 'emerald' ? 'selected' : '' ?>>Emerald (Security)</option>
                <option value="amber" <?= ($page['badge_color'] ?? '') === 'amber' ? 'selected' : '' ?>>Amber (Policy)</option>
                <option value="purple" <?= ($page['badge_color'] ?? '') === 'purple' ? 'selected' : '' ?>>Purple (Transparency)</option>
              </select>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
            <div>
              <label class="admin-modal-label">Document Version</label>
              <input type="text" name="version" value="<?= htmlspecialchars($page['version'] ?? '1.0') ?>" placeholder="e.g. 2.1" class="admin-modal-input" required>
            </div>
            <div>
              <label class="admin-modal-label">Effective Date</label>
              <input type="text" name="effective_date" value="<?= htmlspecialchars($page['effective_date'] ?? date('F Y')) ?>" placeholder="e.g. September 2026" class="admin-modal-input" required>
            </div>
          </div>

          <div style="margin-bottom: 1.25rem;">
            <label class="admin-modal-label">Publication Status</label>
            <select name="is_published" class="admin-modal-select">
              <option value="1" <?= (!isset($page['is_published']) || (int)$page['is_published'] === 1) ? 'selected' : '' ?>>✅ Published (Publicly Accessible)</option>
              <option value="0" <?= (isset($page['is_published']) && (int)$page['is_published'] === 0) ? 'selected' : '' ?>>Disabled / Hidden</option>
            </select>
          </div>

          <div style="margin-bottom: 1.5rem;">
            <label class="admin-modal-label">SEO Meta Description</label>
            <textarea name="meta_description" rows="3" class="admin-modal-input" placeholder="Meta description for search engine compliance indexing..."><?= htmlspecialchars($page['meta_description'] ?? '') ?></textarea>
          </div>

          <button type="submit" class="ts-btn ts-btn-primary" style="width: 100%; justify-content: center; background: linear-gradient(135deg, #10b981, #059669); font-weight: 800; padding: 0.65rem;">
            <i data-lucide="save"></i> Save Legal Policy 💾
          </button>
        </div>

      </div>

    </div>
  </form>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
