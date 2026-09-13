<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
  $adUrl = $adDirectUrl ?? 'https://omg10.com/4/11737440';
?>

<div class="container" style="padding-top: 2rem; margin-bottom: 5rem;">
  
  <!-- Breadcrumbs Hierarchy -->
  <div style="font-size: 0.85rem; color: var(--ts-text-muted); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.45rem; flex-wrap: wrap;">
    <a href="<?= url('/prompts') ?>" style="color: var(--ts-text-secondary); text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;" class="ts-breadcrumb-link">
      <i data-lucide="terminal" style="width: 14px; height: 14px; color: var(--ts-primary);"></i>
      <span>Prompts Hub</span>
    </a>
    <span>&rsaquo;</span>
    <a href="<?= url('/prompts/images') ?>" style="color: var(--ts-text-secondary); text-decoration: none;" class="ts-breadcrumb-link">
      <span>AI Image Prompts</span>
    </a>
    <span>&rsaquo;</span>
    <span style="color: #ec4899; font-weight: 700;">Image Categories &amp; Engines</span>
  </div>

  <!-- Hero Header -->
  <div style="text-align: center; margin-bottom: 2.5rem;">
    <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(236,72,153,0.1); border: 1px solid rgba(236,72,153,0.3); padding: 0.35rem 0.95rem; border-radius: 20px; margin-bottom: 0.75rem;">
      <span style="font-size: 1rem;">🎨</span>
      <span style="font-size: 0.8rem; font-weight: 800; color: #ec4899; text-transform: uppercase; letter-spacing: 0.05em;">Specialized Visual Taxonomy</span>
    </div>
    
    <h1 style="font-size: clamp(2rem, 4.5vw, 2.9rem); font-weight: 900; margin-bottom: 0.6rem; letter-spacing: -0.02em;">
      AI Image Generation Categories
    </h1>
    <p style="color: var(--ts-text-secondary); max-width: 720px; margin: 0 auto; font-size: 1.05rem; line-height: 1.6;">
      Explore prompt blueprints curated by foundation image diffusion engines, cinematic photography, photorealism, and 3D architectural rendering models.
    </p>

    <!-- Navigation Switcher -->
    <div style="display: inline-flex; background: var(--ts-bg-surface-elevated); padding: 0.35rem; border-radius: 30px; border: 1px solid var(--ts-border-main); margin-top: 1.5rem; gap: 0.35rem; flex-wrap: wrap; justify-content: center;">
      <a href="<?= url('/prompts/images/categories') ?>" class="ts-btn ts-btn-sm ts-btn-primary" style="border-radius: 24px; padding: 0.45rem 1.25rem; font-weight: 700; text-decoration: none; background: linear-gradient(135deg, #ec4899, #8b5cf6); box-shadow: 0 4px 15px rgba(236,72,153,0.35);">
        🎨 Image Categories (Active)
      </a>
      <a href="<?= url('/prompts/categories') ?>" class="ts-btn ts-btn-sm ts-btn-ghost" style="border-radius: 24px; padding: 0.45rem 1.25rem; font-weight: 600; text-decoration: none;">
        ⚡ All Prompt Categories
      </a>
      <a href="<?= url('/prompts/images') ?>" class="ts-btn ts-btn-sm ts-btn-ghost" style="border-radius: 24px; padding: 0.45rem 1.25rem; font-weight: 600; text-decoration: none;">
        🖼️ Image Prompts Gallery ↗
      </a>
      <a href="<?= url('/prompts') ?>" class="ts-btn ts-btn-sm ts-btn-ghost" style="border-radius: 24px; padding: 0.45rem 1.25rem; font-weight: 600; text-decoration: none;">
        📚 Prompts Library
      </a>
    </div>
  </div>

  <!-- Top Metrics HUD -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 2.5rem;">
    <div class="ts-card" style="padding: 1.25rem 1.5rem; border-radius: 12px; border-top: 3px solid #ec4899; display: flex; align-items: center; gap: 1rem;">
      <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(236,72,153,0.12); color: #ec4899; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
        🎨
      </div>
      <div>
        <div style="font-size: 1.75rem; font-weight: 800; color: #ec4899; font-family: var(--ts-font-mono); line-height: 1;">
          <?= count($categories) ?>
        </div>
        <div style="font-size: 0.78rem; color: var(--ts-text-secondary); margin-top: 0.2rem; font-weight: 600;">
          Specialized Image Categories
        </div>
      </div>
    </div>

    <div class="ts-card" style="padding: 1.25rem 1.5rem; border-radius: 12px; border-top: 3px solid #38bdf8; display: flex; align-items: center; gap: 1rem;">
      <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(56,189,248,0.12); color: #38bdf8; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
        ⚡
      </div>
      <div>
        <div style="font-size: 1.75rem; font-weight: 800; color: #38bdf8; font-family: var(--ts-font-mono); line-height: 1;">
          <?= number_format($totalImagePrompts ?? 0) ?>
        </div>
        <div style="font-size: 0.78rem; color: var(--ts-text-secondary); margin-top: 0.2rem; font-weight: 600;">
          Published Image Blueprints
        </div>
      </div>
    </div>

    <div class="ts-card" style="padding: 1.25rem 1.5rem; border-radius: 12px; border-top: 3px solid #10b981; display: flex; align-items: center; gap: 1rem;">
      <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(16,185,129,0.12); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
        📋
      </div>
      <div>
        <div style="font-size: 1.75rem; font-weight: 800; color: #10b981; font-family: var(--ts-font-mono); line-height: 1;">
          <?= number_format($totalCopies ?? 0) ?>
        </div>
        <div style="font-size: 0.78rem; color: var(--ts-text-secondary); margin-top: 0.2rem; font-weight: 600;">
          Total Engineer Copies
        </div>
      </div>
    </div>
  </div>

  <!-- Dedicated Categories Grid with Prominent Live Numbers -->
  <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;" id="imageCategoriesGrid">
    <?php foreach ($categories as $cat): ?>
      <?php
        $targetUrl = url('/prompts/images?category=' . urlencode($cat['slug']));
        $count = (int)($cat['prompt_count'] ?? 0);
        $copies = (int)($cat['total_copies'] ?? 0);
        $sampleImg = !empty($cat['sample_image']) ? image_url($cat['sample_image']) : '';
        $icon = !empty($cat['icon_name']) ? $cat['icon_name'] : 'palette';
        $color = !empty($cat['color_hex']) ? $cat['color_hex'] : '#ec4899';
      ?>
      <!-- Category Card -->
      <div class="ts-card image-cat-card" 
           onclick="openCategoryWithAd(event, '<?= $targetUrl ?>', '<?= htmlspecialchars($adUrl) ?>')"
           style="padding: 0; overflow: hidden; border-radius: 14px; border: 1px solid var(--ts-border-main); background: var(--ts-bg-surface); cursor: pointer; transition: transform 0.25s ease, box-shadow 0.25s ease; display: flex; flex-direction: column;">
        
        <!-- Header Art Banner with Live Numbers Badge -->
        <div style="position: relative; height: 135px; background: linear-gradient(135deg, <?= $color ?>25, rgba(15,23,42,0.9)); overflow: hidden; border-bottom: 1px solid var(--ts-border-main);">
          <?php if (!empty($sampleImg)): ?>
            <img src="<?= htmlspecialchars($sampleImg) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.55; filter: saturate(1.2); transition: transform 0.35s ease;">
          <?php endif; ?>
          <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(5,7,11,0.85) 0%, transparent 80%);"></div>

          <!-- Top Badge Bar with Real-time Count Number -->
          <div style="position: absolute; top: 0.75rem; left: 0.75rem; right: 0.75rem; display: flex; justify-content: space-between; align-items: center;">
            <div style="width: 34px; height: 34px; border-radius: 8px; background: rgba(5,7,11,0.7); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; color: <?= $color ?>;">
              <i data-lucide="<?= htmlspecialchars($icon) ?>" style="width: 18px; height: 18px;"></i>
            </div>
            
            <!-- PROMINENT NUMBER BADGE -->
            <div class="ts-badge" style="background: rgba(5,7,11,0.8); backdrop-filter: blur(8px); border: 1px solid rgba(236,72,153,0.4); color: #fff; font-size: 0.8rem; font-weight: 800; padding: 0.25rem 0.65rem; border-radius: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.5);">
              <span style="color: #38bdf8; font-family: var(--ts-font-mono); font-size: 0.95rem; margin-right: 0.25rem;"><?= number_format($count) ?></span>
              <span><?= $count === 1 ? 'Blueprint' : 'Blueprints' ?></span>
            </div>
          </div>

          <!-- Bottom Title Overlay -->
          <div style="position: absolute; bottom: 0.75rem; left: 0.85rem; right: 0.85rem;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #ffffff; text-shadow: 0 2px 6px rgba(0,0,0,0.8);">
              <?= htmlspecialchars($cat['name']) ?>
            </h3>
          </div>
        </div>

        <!-- Body & Taxonomy Description -->
        <div style="padding: 1.15rem 1.25rem; display: flex; flex-direction: column; flex: 1; justify-content: space-between;">
          <p style="font-size: 0.85rem; color: var(--ts-text-secondary); line-height: 1.5; margin: 0 0 1rem 0;">
            <?= htmlspecialchars($cat['description'] ?: 'High-fidelity visual art blueprints, camera parameters, and prompt syntax engineering.') ?>
          </p>

          <div style="border-top: 1px solid rgba(148,163,184,0.1); padding-top: 0.85rem; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 0.75rem; color: var(--ts-text-tertiary); display: flex; align-items: center; gap: 0.3rem;">
              <i data-lucide="copy" style="width: 12px; height: 12px; color: #10b981;"></i>
              <strong><?= number_format($copies) ?></strong> copies
            </span>

            <span style="font-size: 0.8rem; color: #ec4899; font-weight: 700; display: flex; align-items: center; gap: 0.25rem;">
              Explore Blueprints (<?= $count ?>) &rarr;
            </span>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</div>

<script>
function openCategoryWithAd(e, targetUrl, adUrl) {
  e.preventDefault();
  if (adUrl) {
    try {
      const adWin = window.open(adUrl, '_blank');
      if (adWin) {
        try {
          adWin.blur();
          window.focus();
        } catch(err) {}
      }
    } catch(err) {
      window.open(adUrl, '_blank');
    }
  }
  window.location.href = targetUrl;
}

document.addEventListener('DOMContentLoaded', () => {
  if (window.lucide) lucide.createIcons();
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
