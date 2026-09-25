<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
  $adUrl = $adDirectUrl ?? 'https://omg10.com/4/11737440';
  $totalPrompts = array_sum(array_column($imageCategories, 'prompt_count')) + array_sum(array_column($textCategories, 'prompt_count'));
?>

<div class="container" style="padding-top: 2rem; margin-bottom: 5rem;">
  
  <!-- Breadcrumbs Hierarchy -->
  <div style="font-size: 0.85rem; color: var(--ts-text-muted); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.45rem; flex-wrap: wrap;">
    <a href="<?= url('/prompts') ?>" style="color: var(--ts-text-secondary); text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;" class="ts-breadcrumb-link">
      <i data-lucide="terminal" style="width: 14px; height: 14px; color: var(--ts-primary);"></i>
      <span>Prompts Hub</span>
    </a>
    <span>&rsaquo;</span>
    <span style="color: var(--ts-text-primary); font-weight: 700;">All Prompt Categories</span>
  </div>

  <!-- Hero Header -->
  <div style="text-align: center; margin-bottom: 2.5rem;">
    <span class="ts-badge ts-badge-cyan" style="margin-bottom: 0.75rem;">Prompt Blueprint Taxonomy</span>
    <h1 style="font-size: clamp(2rem, 4vw, 2.75rem); font-weight: 800; margin-bottom: 0.5rem;">
      Prompt Categories &amp; Architectures
    </h1>
    <p style="color: var(--ts-text-secondary); max-width: 680px; margin: 0 auto; font-size: 1.05rem;">
      Browse specialized prompt blueprint taxonomies strictly segregated into visual diffusion imagery and LLM software system engineering.
    </p>

    <!-- Navigation Switcher -->
    <div style="display: inline-flex; background: var(--ts-bg-surface-elevated); padding: 0.35rem; border-radius: 30px; border: 1px solid var(--ts-border-main); margin-top: 1.5rem; gap: 0.35rem; flex-wrap: wrap; justify-content: center;">
      <a href="<?= url('/prompts/images/categories') ?>" class="ts-btn ts-btn-sm ts-btn-outline" style="border-radius: 24px; padding: 0.45rem 1.25rem; font-weight: 700; text-decoration: none; border-color: #ec4899; color: #ec4899;">
        🎨 Only Image Categories ↗
      </a>
      <a href="<?= url('/prompts/categories') ?>" class="ts-btn ts-btn-sm ts-btn-primary" style="border-radius: 24px; padding: 0.45rem 1.25rem; font-weight: 700; text-decoration: none;">
        ⚡ All Prompt Categories (Active)
      </a>
      <a href="<?= url('/prompts/images') ?>" class="ts-btn ts-btn-sm ts-btn-ghost" style="border-radius: 24px; padding: 0.45rem 1.25rem; font-weight: 600; text-decoration: none;">
        🖼️ Image Gallery
      </a>
      <a href="<?= url('/prompts') ?>" class="ts-btn ts-btn-sm ts-btn-ghost" style="border-radius: 24px; padding: 0.45rem 1.25rem; font-weight: 600; text-decoration: none;">
        📚 Prompts Library
      </a>
    </div>
  </div>

  <!-- SECTION 1: AI IMAGE GENERATION CATEGORIES -->
  <div style="margin-bottom: 3.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.5rem; border-bottom: 1px solid var(--ts-border-main); padding-bottom: 0.75rem;">
      <div style="display: flex; align-items: center; gap: 0.6rem;">
        <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(236,72,153,0.15); color: #ec4899; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
          🎨
        </div>
        <div>
          <h2 style="font-size: 1.35rem; font-weight: 800; margin: 0; color: var(--ts-text-primary);">
            AI Image Generation Categories (<?= count($imageCategories) ?>)
          </h2>
          <span style="font-size: 0.75rem; color: var(--ts-text-secondary);">Diffusion models, artistic aesthetics, photorealism &amp; 3D renders</span>
        </div>
      </div>

      <a href="<?= url('/prompts/images/categories') ?>" class="ts-btn ts-btn-sm ts-btn-outline" style="border-color: #ec4899; color: #ec4899; font-size: 0.8rem;">
        View Dedicated Image Categories Page ↗
      </a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.25rem;">
      <?php foreach ($imageCategories as $cat): ?>
        <?php
          $targetUrl = url('/prompts/images?category=' . urlencode($cat['slug']));
          $count = (int)($cat['prompt_count'] ?? 0);
          $copies = (int)($cat['total_copies'] ?? 0);
        ?>
        <div class="ts-card" onclick="window.location.href = '<?= $targetUrl ?>'"
             style="padding: 1.25rem; border-radius: 12px; border: 1px solid var(--ts-border-main); background: var(--ts-bg-surface); cursor: pointer; transition: transform 0.2s ease; display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
              <span class="ts-badge ts-badge-pink" style="font-size: 0.75rem; font-weight: 800;">
                🎨 Image Engine
              </span>
              <span class="ts-badge" style="background: rgba(56,189,248,0.12); color: #38bdf8; font-weight: 800; font-family: var(--ts-font-mono); font-size: 0.8rem;">
                <?= number_format($count) ?> <?= $count === 1 ? 'Prompt' : 'Prompts' ?>
              </span>
            </div>

            <h3 style="font-size: 1.05rem; font-weight: 800; margin: 0 0 0.35rem 0; color: var(--ts-text-primary);">
              <?= htmlspecialchars($cat['name']) ?>
            </h3>
            <p style="font-size: 0.82rem; color: var(--ts-text-secondary); line-height: 1.45; margin: 0 0 0.75rem 0;">
              <?= htmlspecialchars($cat['description'] ?: 'High-fidelity generative visual blueprints.') ?>
            </p>
          </div>

          <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: var(--ts-text-tertiary); border-top: 1px solid rgba(148,163,184,0.1); padding-top: 0.65rem;">
            <span><i data-lucide="copy" style="width: 11px; height: 11px; display: inline;"></i> <?= number_format($copies) ?> copies</span>
            <span style="color: #ec4899; font-weight: 600;">Browse Blueprints &rarr;</span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- SECTION 2: LLM & SYSTEM ARCHITECTURE PROMPTS CATEGORIES -->
  <div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.5rem; border-bottom: 1px solid var(--ts-border-main); padding-bottom: 0.75rem;">
      <div style="display: flex; align-items: center; gap: 0.6rem;">
        <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(56,189,248,0.15); color: #38bdf8; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
          ⚡
        </div>
        <div>
          <h2 style="font-size: 1.35rem; font-weight: 800; margin: 0; color: var(--ts-text-primary);">
            LLM &amp; Code Engineering Categories (<?= count($textCategories) ?>)
          </h2>
          <span style="font-size: 0.75rem; color: var(--ts-text-secondary);">System personas, SQL optimization, Dockerfiles, and AppSec</span>
        </div>
      </div>

      <a href="<?= url('/prompts') ?>" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.8rem;">
        Browse All Text Prompts &rarr;
      </a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.25rem;">
      <?php foreach ($textCategories as $cat): ?>
        <?php
          $targetUrl = url('/prompts?category=' . urlencode($cat['slug']));
          $count = (int)($cat['prompt_count'] ?? 0);
          $copies = (int)($cat['total_copies'] ?? 0);
        ?>
        <div class="ts-card" onclick="window.location.href = '<?= $targetUrl ?>'"
             style="padding: 1.25rem; border-radius: 12px; border: 1px solid var(--ts-border-main); background: var(--ts-bg-surface); cursor: pointer; transition: transform 0.2s ease; display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
              <span class="ts-badge ts-badge-green" style="font-size: 0.75rem; font-weight: 800;">
                ⚡ System Code
              </span>
              <span class="ts-badge" style="background: rgba(16,185,129,0.12); color: #10b981; font-weight: 800; font-family: var(--ts-font-mono); font-size: 0.8rem;">
                <?= number_format($count) ?> <?= $count === 1 ? 'Prompt' : 'Prompts' ?>
              </span>
            </div>

            <h3 style="font-size: 1.05rem; font-weight: 800; margin: 0 0 0.35rem 0; color: var(--ts-text-primary);">
              <?= htmlspecialchars($cat['name']) ?>
            </h3>
            <p style="font-size: 0.82rem; color: var(--ts-text-secondary); line-height: 1.45; margin: 0 0 0.75rem 0;">
              <?= htmlspecialchars($cat['description'] ?: 'Production engineering prompt blueprints.') ?>
            </p>
          </div>

          <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: var(--ts-text-tertiary); border-top: 1px solid rgba(148,163,184,0.1); padding-top: 0.65rem;">
            <span><i data-lucide="copy" style="width: 11px; height: 11px; display: inline;"></i> <?= number_format($copies) ?> copies</span>
            <span style="color: #38bdf8; font-weight: 600;">Browse Prompts &rarr;</span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  if (window.lucide) lucide.createIcons();
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
