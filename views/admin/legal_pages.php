<?php
$title = 'Legal & Compliance Pages CMS | TechScout Admin';
require __DIR__ . '/../layouts/admin_header.php';
?>

<div class="admin-content-inner">
  <!-- Page Header -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-purple" style="font-size: 0.68rem; font-weight: 800;">REGULATORY &amp; COMPLIANCE CMS</span>
        <span style="color: #64748b; font-size: 0.8rem;">GDPR, CCPA, DMCA, AUP &amp; Terms Governance</span>
      </div>
      <h1 style="font-size: 1.65rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        ⚖️ Legal Pages Content Management
      </h1>
      <p style="color: #94a3b8; font-size: 0.85rem; margin: 0.25rem 0 0 0;">
        Fully detailed editor for all platform legal policies, terms of service, disclosures, and compliance agreements.
      </p>
    </div>

    <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.5rem; align-items: center;">
      <a href="<?= url('/legal') ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.35);">
        <i data-lucide="external-link"></i> Live Legal Hub ↗
      </a>
    </div>
  </div>

  <!-- Legal Pages Table -->
  <div class="admin-table-wrap" style="background: rgba(13,21,39,0.7); border: 1px solid rgba(99,102,241,0.2); border-radius: 12px; overflow: hidden; margin-bottom: 2.5rem;">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width: 300px;">Policy Name & Route</th>
          <th>Summary</th>
          <th>Effective Date</th>
          <th>Version</th>
          <th>Status</th>
          <th style="text-align: right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pages as $p): ?>
          <tr>
            <td>
              <div style="display: flex; align-items: center; gap: 0.75rem;">
                <span class="ts-badge ts-badge-<?= htmlspecialchars($p['badge_color'] ?: 'indigo') ?>" style="font-size: 0.65rem;">
                  <?= htmlspecialchars($p['badge'] ?: 'LEGAL') ?>
                </span>
                <div>
                  <strong style="color: #f8fafc; font-size: 0.95rem; display: block; margin-bottom: 0.15rem;"><?= htmlspecialchars($p['title']) ?></strong>
                  <code style="background: rgba(15,23,42,0.8); color: #38bdf8; padding: 0.1rem 0.35rem; border-radius: 4px; font-size: 0.75rem; border: 1px solid rgba(56,189,248,0.2);">
                    /<?= htmlspecialchars($p['slug']) ?>
                  </code>
                </div>
              </div>
            </td>
            <td>
              <div style="font-size: 0.82rem; color: #94a3b8; max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($p['subtitle'] ?? '') ?>">
                <?= htmlspecialchars($p['subtitle'] ?: 'No subtitle provided') ?>
              </div>
            </td>
            <td>
              <span style="font-family: monospace; font-size: 0.85rem; color: #cbd5e1;">
                <?= htmlspecialchars($p['effective_date']) ?>
              </span>
            </td>
            <td>
              <span style="font-family: monospace; font-size: 0.85rem; color: #94a3b8;">
                v<?= htmlspecialchars($p['version']) ?>
              </span>
            </td>
            <td>
              <?= !empty($p['is_published']) ? '<span class="ts-badge ts-badge-green" style="font-size: 0.7rem;">ACTIVE</span>' : '<span class="ts-badge ts-badge-pink" style="font-size: 0.7rem;">DISABLED</span>' ?>
            </td>
            <td style="text-align: right;">
              <div style="display: inline-flex; gap: 0.4rem; align-items: center;">
                <a href="<?= url('/' . $p['slug']) ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.35rem 0.65rem; font-size: 0.78rem; color: #38bdf8; border-color: rgba(56,189,248,0.3);">
                  <i data-lucide="eye" style="width: 14px; height: 14px; margin-right: 4px;"></i> View
                </a>
                <a href="<?= url('/admin/legal-pages/edit/' . $p['id']) ?>" class="ts-btn ts-btn-primary ts-btn-sm" style="font-weight: 700; padding: 0.35rem 0.65rem; font-size: 0.78rem; background: linear-gradient(135deg, #a855f7, #6366f1);">
                  <i data-lucide="edit-3" style="width: 14px; height: 14px; margin-right: 4px;"></i> Edit
                </a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
