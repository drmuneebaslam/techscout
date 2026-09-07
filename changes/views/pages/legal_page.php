<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 3rem; margin-bottom: 5rem; max-width: 960px;">
  <!-- Hero Banner -->
  <div class="ts-card" style="background: radial-gradient(circle at 10% 20%, rgba(99,102,241,0.12), transparent 45%), var(--ts-bg-surface); border-color: rgba(99,102,241,0.3); padding: 2.5rem; margin-bottom: 2.5rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.75rem;">
      <div style="display: flex; align-items: center; gap: 0.5rem;">
        <span class="ts-badge ts-badge-<?= htmlspecialchars($page['badge_color'] ?? 'indigo') ?>"><?= htmlspecialchars($page['badge'] ?? 'LEGAL & COMPLIANCE') ?></span>
        <span style="font-size: 0.8rem; color: var(--ts-text-secondary);">Effective: <?= htmlspecialchars($page['effective_date'] ?? 'September 2026') ?></span>
      </div>
      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <span style="font-size: 0.78rem; color: #94a3b8; font-family: monospace;">Version <?= htmlspecialchars($page['version'] ?? '1.0') ?></span>
        <?php if (\App\Core\Auth::isAdmin()): ?>
          <a href="<?= url('/admin/legal-pages/edit/' . ($page['id'] ?? '')) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem; border-color: rgba(56,189,248,0.4); color: #38bdf8;">
            ✏️ Edit in CMS
          </a>
        <?php endif; ?>
      </div>
    </div>
    <h1 style="font-size: 2.4rem; color: var(--ts-text-primary); margin: 0 0 0.75rem 0; font-weight: 800; font-family: 'Outfit', sans-serif;">
      <?= htmlspecialchars($page['title']) ?>
    </h1>
    <?php if (!empty($page['subtitle'])): ?>
      <p style="font-size: 1.05rem; color: var(--ts-text-secondary); line-height: 1.6; margin: 0;">
        <?= htmlspecialchars($page['subtitle']) ?>
      </p>
    <?php endif; ?>
  </div>

  <!-- Main Legal Document Body -->
  <div class="ts-card" style="background: var(--ts-bg-surface); border-color: var(--ts-border-main); padding: 2.5rem; line-height: 1.75; color: var(--ts-text-secondary); font-size: 0.98rem;">
    <?= $page['content_html'] ?>
  </div>

  <!-- Bottom Navigation / Other Legal Pages -->
  <div style="margin-top: 2.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; border-top: 1px solid var(--ts-border-subtle); padding-top: 1.5rem;">
    <a href="<?= url('/legal') ?>" style="color: #38bdf8; font-size: 0.88rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
      &larr; Return to Legal &amp; Compliance Hub
    </a>
    <div style="font-size: 0.8rem; color: var(--ts-text-secondary);">
      Inquiries: <a href="mailto:legal@techscout.online" style="color: var(--ts-text-primary); text-decoration: underline;">legal@techscout.online</a>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
