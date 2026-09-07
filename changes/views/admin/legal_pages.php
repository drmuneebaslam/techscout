<?php
$title = 'Legal & Compliance Pages CMS | TechScout Admin';
require __DIR__ . '/../layouts/admin_header.php';
?>

<div class="admin-content-inner">
  <!-- Page Header -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
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

    <div style="display: flex; gap: 0.5rem; align-items: center;">
      <a href="<?= url('/legal') ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.35);">
        <i data-lucide="external-link"></i> Live Legal Hub ↗
      </a>
    </div>
  </div>

  <!-- Legal Pages Grid -->
  <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 340px), 1fr)); gap: 1.25rem; margin-bottom: 2.5rem;">
    <?php foreach ($pages as $p): ?>
      <div class="ts-card" style="background: rgba(15,23,42,0.7); border: 1px solid var(--admin-border-subtle); display: flex; flex-direction: column; justify-content: space-between; border-radius: 12px; padding: 1.5rem;">
        <div>
          <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
            <span class="ts-badge ts-badge-<?= htmlspecialchars($p['badge_color'] ?: 'indigo') ?>" style="font-size: 0.68rem;">
              <?= htmlspecialchars($p['badge'] ?: 'LEGAL') ?>
            </span>
            <span style="font-family: monospace; font-size: 0.75rem; color: #94a3b8;">
              v<?= htmlspecialchars($p['version']) ?>
            </span>
          </div>

          <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 0.35rem 0; font-weight: 700;">
            <?= htmlspecialchars($p['title']) ?>
          </h3>

          <div style="font-family: monospace; font-size: 0.78rem; color: #38bdf8; margin-bottom: 0.75rem;">
            /<?= htmlspecialchars($p['slug']) ?>
          </div>

          <?php if (!empty($p['subtitle'])): ?>
            <p style="color: #94a3b8; font-size: 0.82rem; line-height: 1.5; margin: 0 0 1rem 0;">
              <?= htmlspecialchars($p['subtitle']) ?>
            </p>
          <?php endif; ?>

          <div style="font-size: 0.78rem; color: #64748b; margin-bottom: 1rem;">
            Effective: <strong><?= htmlspecialchars($p['effective_date']) ?></strong> &bull; Status: 
            <?= !empty($p['is_published']) ? '<span style="color:#10b981; font-weight:700;">Active</span>' : '<span style="color:#ef4444; font-weight:700;">Disabled</span>' ?>
          </div>
        </div>

        <div style="border-top: 1px solid rgba(148,163,184,0.12); padding-top: 0.85rem; display: flex; justify-content: space-between; align-items: center;">
          <a href="<?= url('/' . $p['slug']) ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; color: #38bdf8;">
            👁️ View Live ↗
          </a>
          <a href="<?= url('/admin/legal-pages/edit/' . $p['id']) ?>" class="ts-btn ts-btn-primary ts-btn-sm" style="font-size: 0.75rem; font-weight: 700; background: linear-gradient(135deg, #a855f7, #6366f1);">
            ✏️ Edit Content &rarr;
          </a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
