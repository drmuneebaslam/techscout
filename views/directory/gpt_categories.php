<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 2rem; margin-bottom: 5rem;">
  
  <!-- Breadcrumbs Hierarchy -->
  <div style="font-size: 0.85rem; color: var(--ts-text-muted); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.45rem; flex-wrap: wrap;">
    <a href="<?= url('/gpts') ?>" style="color: var(--ts-text-secondary); text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;" class="ts-breadcrumb-link">
      <i data-lucide="bot" style="width: 14px; height: 14px; color: var(--ts-primary);"></i>
      <span>Custom GPTs Hub</span>
    </a>
    <span>&rsaquo;</span>
    <span style="color: var(--ts-text-primary); font-weight: 700;">Agent Categories</span>
  </div>

  <div style="text-align: center; margin-bottom: 2.5rem;">
    <span class="ts-badge ts-badge-pink" style="margin-bottom: 0.75rem;">Autonomous Agents Taxonomy</span>
    <h1 style="font-size: clamp(2rem, 4vw, 2.75rem); font-weight: 800; margin-bottom: 0.5rem;">
      Custom GPT &amp; Agent Categories
    </h1>
    <p style="color: var(--ts-text-secondary); max-width: 680px; margin: 0 auto; font-size: 1.05rem;">
      Explore custom GPT assistants and tool-calling agents categorized by autonomous coding, systems architecture, application security, and data science.
    </p>

    <!-- Navigation Switcher -->
    <div style="display: inline-flex; background: var(--ts-bg-surface-elevated); padding: 0.35rem; border-radius: 30px; border: 1px solid var(--ts-border-main); margin-top: 1.5rem; gap: 0.35rem; flex-wrap: wrap; justify-content: center;">
      <a href="<?= url('/gpts/categories') ?>" class="ts-btn ts-btn-sm ts-btn-primary" style="border-radius: 24px; padding: 0.45rem 1.25rem; font-weight: 700; text-decoration: none; background: linear-gradient(135deg, #a855f7, #6366f1);">
        🤖 Agent Categories (Active)
      </a>
      <a href="<?= url('/gpts') ?>" class="ts-btn ts-btn-sm ts-btn-ghost" style="border-radius: 24px; padding: 0.45rem 1.25rem; font-weight: 600; text-decoration: none;">
        ⚡ All Custom GPTs Directory ↗
      </a>
      <a href="<?= url('/prompts/categories') ?>" class="ts-btn ts-btn-sm ts-btn-ghost" style="border-radius: 24px; padding: 0.45rem 1.25rem; font-weight: 600; text-decoration: none;">
        🎨 Prompt Categories
      </a>
      <a href="<?= url('/tools/categories') ?>" class="ts-btn ts-btn-sm ts-btn-ghost" style="border-radius: 24px; padding: 0.45rem 1.25rem; font-weight: 600; text-decoration: none;">
        📁 Tool Categories
      </a>
    </div>
  </div>

  <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
    <?php foreach ($categories as $cat): ?>
      <?php
        $targetUrl = url('/gpts?category=' . urlencode($cat['slug']));
        $count = (int)($cat['gpt_count'] ?? 0);
        $icon = !empty($cat['icon_name']) ? $cat['icon_name'] : 'bot';
      ?>
      <div class="ts-card" onclick="window.location.href='<?= $targetUrl ?>'"
           style="padding: 1.5rem; border-radius: 14px; border: 1px solid var(--ts-border-main); background: var(--ts-bg-surface); cursor: pointer; transition: transform 0.2s ease; display: flex; flex-direction: column; justify-content: space-between;">
        <div>
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem;">
            <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(168,85,247,0.12); color: #a855f7; display: flex; align-items: center; justify-content: center;">
              <i data-lucide="<?= htmlspecialchars($icon) ?>" style="width: 20px; height: 20px;"></i>
            </div>
            
            <!-- Live Agent Count Pill -->
            <span class="ts-badge" style="background: rgba(168,85,247,0.15); color: #a855f7; font-weight: 800; font-family: var(--ts-font-mono); font-size: 0.82rem; padding: 0.25rem 0.65rem;">
              <?= number_format($count) ?> <?= $count === 1 ? 'Agent' : 'Agents' ?>
            </span>
          </div>

          <h3 style="font-size: 1.15rem; font-weight: 800; margin: 0 0 0.45rem 0; color: var(--ts-text-primary);">
            <?= htmlspecialchars($cat['name']) ?>
          </h3>
          <p style="font-size: 0.85rem; color: var(--ts-text-secondary); line-height: 1.5; margin: 0 0 1rem 0;">
            <?= htmlspecialchars($cat['description'] ?: 'Autonomous GPT assistants, system personas, and tool integrations.') ?>
          </p>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.78rem; color: #a855f7; font-weight: 700; border-top: 1px solid rgba(148,163,184,0.1); padding-top: 0.75rem;">
          <span>Explore Agents</span>
          <span>&rarr;</span>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  if (window.lucide) lucide.createIcons();
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
