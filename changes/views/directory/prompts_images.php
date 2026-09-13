<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
  $adUrl = $adDirectUrl ?? 'https://omg10.com/4/11737440';
  $activeCat = $currentCategory ?? '';
  $activeEngine = $currentModel ?? '';
?>

<div class="container" style="padding-top: 2rem; margin-bottom: 5rem;">
  
  <!-- Breadcrumbs Hierarchy -->
  <div style="font-size: 0.85rem; color: var(--ts-text-muted); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.45rem; flex-wrap: wrap;">
    <a href="<?= url('/prompts') ?>" style="color: var(--ts-text-secondary); text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;" class="ts-breadcrumb-link">
      <i data-lucide="terminal" style="width: 14px; height: 14px; color: var(--ts-primary);"></i>
      <span>Prompts Hub</span>
    </a>
    <span>&rsaquo;</span>
    <a href="<?= url('/prompts/images') ?>" style="color: #ec4899; text-decoration: none; font-weight: 600;" class="ts-breadcrumb-link">
      <span>AI Image Prompts</span>
    </a>
    <?php if (!empty($categoryInfo)): ?>
      <span>&rsaquo;</span>
      <span style="color: var(--ts-text-primary); font-weight: 700;"><?= htmlspecialchars($categoryInfo['name']) ?></span>
    <?php endif; ?>
  </div>

  <!-- Header Banner -->
  <div style="text-align: center; margin-bottom: 2.25rem;">
    <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(236,72,153,0.1); border: 1px solid rgba(236,72,153,0.3); padding: 0.35rem 0.95rem; border-radius: 20px; margin-bottom: 0.75rem;">
      <span style="font-size: 1rem;">🎨</span>
      <span style="font-size: 0.8rem; font-weight: 800; color: #ec4899; text-transform: uppercase; letter-spacing: 0.05em;">AI Image Prompts &amp; Visual Blueprints</span>
    </div>
    
    <h1 style="font-size: clamp(2rem, 4vw, 2.85rem); font-weight: 900; margin-bottom: 0.5rem; letter-spacing: -0.02em;">
      Visual Generation Image Gallery
    </h1>
    <p style="color: var(--ts-text-secondary); max-width: 680px; margin: 0 auto; font-size: 1.02rem; line-height: 1.6;">
      Click any image showcase to open the complete blueprint, customize variables, and deploy high-fidelity prompts to Midjourney, Flux.1, DALL-E 3, and SDXL.
    </p>

    <!-- Sub-Directory Navigation Switcher -->
    <div style="display: inline-flex; background: var(--ts-bg-surface-elevated); padding: 0.35rem; border-radius: 30px; border: 1px solid var(--ts-border-main); margin-top: 1.5rem; gap: 0.35rem;">
      <a href="<?= url('/prompts') ?>" class="ts-btn ts-btn-sm ts-btn-ghost" style="border-radius: 24px; padding: 0.45rem 1.2rem; font-weight: 600; text-decoration: none;">
        ⚡ LLM &amp; System Prompts
      </a>
      <a href="<?= url('/prompts/images') ?>" class="ts-btn ts-btn-sm ts-btn-primary" style="border-radius: 24px; padding: 0.45rem 1.2rem; font-weight: 700; text-decoration: none; background: linear-gradient(135deg, #ec4899, #8b5cf6); box-shadow: 0 4px 15px rgba(236,72,153,0.35);">
        🎨 AI Image Prompts Gallery
      </a>
    </div>
  </div>

  <!-- Specialized Engine Filter Bar -->
  <div style="display: flex; justify-content: center; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
    <a href="<?= url('/prompts/images') ?>" 
       class="ts-badge <?= (empty($activeCat) && empty($activeEngine)) ? 'ts-badge-pink' : 'ts-badge-cyan' ?>" 
       style="padding: 0.45rem 1rem; font-size: 0.85rem; text-decoration: none; border-radius: 20px; font-weight: 700;">
      🌟 All Engines
    </a>
    <a href="<?= url('/prompts/images?category=flux') ?>" 
       class="ts-badge <?= ($activeCat === 'flux') ? 'ts-badge-pink' : 'ts-badge-cyan' ?>" 
       style="padding: 0.45rem 1rem; font-size: 0.85rem; text-decoration: none; border-radius: 20px; font-weight: 700;">
      ⚡ Flux.1 Dev/Schnell
    </a>
    <a href="<?= url('/prompts/images?category=midjourney') ?>" 
       class="ts-badge <?= ($activeCat === 'midjourney') ? 'ts-badge-pink' : 'ts-badge-cyan' ?>" 
       style="padding: 0.45rem 1rem; font-size: 0.85rem; text-decoration: none; border-radius: 20px; font-weight: 700;">
      🔮 Midjourney v6
    </a>
    <a href="<?= url('/prompts/images?category=dall-e') ?>" 
       class="ts-badge <?= ($activeCat === 'dall-e') ? 'ts-badge-pink' : 'ts-badge-cyan' ?>" 
       style="padding: 0.45rem 1rem; font-size: 0.85rem; text-decoration: none; border-radius: 20px; font-weight: 700;">
      🎨 DALL-E 3
    </a>
    <a href="<?= url('/prompts/images?category=stable-diffusion') ?>" 
       class="ts-badge <?= ($activeCat === 'stable-diffusion') ? 'ts-badge-pink' : 'ts-badge-cyan' ?>" 
       style="padding: 0.45rem 1rem; font-size: 0.85rem; text-decoration: none; border-radius: 20px; font-weight: 700;">
      🌊 Stable Diffusion XL
    </a>
  </div>

  <!-- Search & Filter HUD -->
  <div class="ts-card" style="margin-bottom: 2rem; padding: 1rem 1.25rem; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main); border-radius: 12px;">
    <form method="GET" action="<?= url('/prompts/images') ?>" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
      <?php if (!empty($activeCat)): ?>
        <input type="hidden" name="category" value="<?= htmlspecialchars($activeCat) ?>">
      <?php endif; ?>
      <div style="flex: 1; min-width: 240px; position: relative;">
        <i data-lucide="search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--ts-text-tertiary);"></i>
        <input type="text" name="search" value="<?= htmlspecialchars($currentSearch ?? '') ?>" placeholder="Search visual style tokens, camera gear, lighting, aspect ratios..." 
               style="width: 100%; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 8px; padding: 0.55rem 2rem 0.55rem 2.2rem; color: var(--ts-text-primary); font-size: 0.88rem;">
      </div>

      <div style="display: flex; gap: 0.6rem; align-items: center;">
        <select name="sort" onchange="this.form.submit()" style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 8px; padding: 0.55rem 0.85rem; color: var(--ts-success); font-weight: 600; font-size: 0.85rem; cursor: pointer;">
          <option value="popular" <?= ($currentSort ?? 'popular') === 'popular' ? 'selected' : '' ?>>Most Copied ⚡</option>
          <option value="trending" <?= ($currentSort ?? '') === 'trending' ? 'selected' : '' ?>>Trending Now 🔥</option>
          <option value="latest" <?= ($currentSort ?? '') === 'latest' ? 'selected' : '' ?>>Recently Added</option>
        </select>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="padding: 0.55rem 1rem;">Filter</button>
      </div>
    </form>
  </div>

  <!-- PURE IMAGE GRID: Civitai / Pinterest / Lexica Style -->
  <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.25rem;" id="aiImagePromptsGrid">
    <?php if (!empty($prompts)): ?>
      <?php foreach ($prompts as $p): ?>
        <?php
          $promptText = $p['prompt_template'] ?? '';
          $imageUrl = !empty($p['featured_image_url']) ? image_url($p['featured_image_url']) : '';
          $models = !empty($p['target_models']) ? (is_array($p['target_models']) ? $p['target_models'] : json_decode($p['target_models'], true)) : ['Flux.1'];
          $primaryModel = is_array($models) ? ($models[0] ?? 'Flux.1') : 'Flux.1';
          $singlePromptUrl = url('/prompts/' . $p['slug']);
          $notes = json_decode($p['internal_notes'] ?? '{}', true) ?: [];
          $aspectRatio = $notes['aspect_ratio'] ?? '16:9';
        ?>
        <!-- Interactive Image Card: Click opens single prompt and triggers popunder ad -->
        <div class="prompt-image-grid-card" 
             onclick="openImagePromptWithAd(event, '<?= $singlePromptUrl ?>', '<?= htmlspecialchars($adUrl) ?>')"
             style="position: relative; height: 350px; border-radius: 14px; overflow: hidden; cursor: pointer; border: 1px solid var(--ts-border-main); background: #070a12; transition: transform 0.25s ease, box-shadow 0.25s ease;">
          
          <!-- Image Asset -->
          <?php if (!empty($imageUrl) && $imageUrl !== 'https://funwithcoding.free.nf/'): ?>
            <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($p['title']) ?>" 
                 loading="lazy"
                 style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;"
                 onmouseover="this.style.transform='scale(1.06)'"
                 onmouseout="this.style.transform='scale(1)'">
          <?php else: ?>
            <div style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; background: linear-gradient(135deg, rgba(236,72,153,0.15), rgba(99,102,241,0.15)); color: #ec4899;">
              <span style="font-size: 3rem;">🎨</span>
              <span style="font-size: 0.8rem; font-weight: 700; margin-top: 0.5rem; color: var(--ts-text-secondary);"><?= htmlspecialchars($primaryModel) ?></span>
            </div>
          <?php endif; ?>

          <!-- Top Floating Action Pill & Model Badge -->
          <div style="position: absolute; top: 0.75rem; left: 0.75rem; right: 0.75rem; display: flex; justify-content: space-between; align-items: center; pointer-events: none; z-index: 2;">
            <span class="ts-badge ts-badge-pink" style="font-size: 0.72rem; font-weight: 800; backdrop-filter: blur(8px); background: rgba(236,72,153,0.85); color: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.4);">
              <?= htmlspecialchars($primaryModel) ?>
            </span>
            
            <!-- Quick Copy Button (Stops card click) -->
            <button type="button" 
                    class="btn-copy-card-prompt"
                    title="Quick Copy Prompt"
                    onclick="copyCardPrompt(event, '<?= htmlspecialchars(addslashes($promptText)) ?>')"
                    style="pointer-events: auto; background: rgba(5,7,11,0.75); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 8px; padding: 0.35rem 0.6rem; font-size: 0.75rem; display: flex; align-items: center; gap: 0.3rem; cursor: pointer; transition: all 0.2s ease;"
                    onmouseover="this.style.background='rgba(56,189,248,0.9)'; this.style.color='#000';"
                    onmouseout="this.style.background='rgba(5,7,11,0.75)'; this.style.color='#fff';">
              <i data-lucide="copy" style="width: 12px; height: 12px;"></i>
              <span>Copy</span>
            </button>
          </div>

          <!-- Bottom Gradient Scrim Overlay with Title & Metadata -->
          <div style="position: absolute; inset-x: 0; bottom: 0; background: linear-gradient(to top, rgba(5,7,11,0.95) 0%, rgba(5,7,11,0.7) 65%, transparent 100%); padding: 1.25rem 1rem 0.85rem 1rem; z-index: 2; pointer-events: none;">
            <div style="display: flex; gap: 0.4rem; align-items: center; margin-bottom: 0.35rem;">
              <span class="ts-badge" style="font-size: 0.65rem; padding: 0.1rem 0.45rem; background: rgba(56,189,248,0.2); color: #38bdf8;">
                <?= htmlspecialchars($p['category_name'] ?? 'Diffusion') ?>
              </span>
              <span style="font-size: 0.68rem; color: #cbd5e1; font-family: var(--ts-font-mono);">
                --ar <?= htmlspecialchars($aspectRatio) ?>
              </span>
            </div>

            <h3 style="font-size: 0.95rem; font-weight: 700; color: #ffffff; line-height: 1.35; margin: 0 0 0.35rem 0; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
              <?= htmlspecialchars($p['title']) ?>
            </h3>

            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.72rem; color: #94a3b8;">
              <span><i data-lucide="copy" style="width: 10px; height: 10px; display: inline;"></i> <?= number_format((int)($p['copy_count'] ?? 0)) ?> copies</span>
              <span style="color: #38bdf8; font-weight: 600; display: flex; align-items: center; gap: 0.2rem;">
                View Prompt <i data-lucide="arrow-right" style="width: 10px; height: 10px;"></i>
              </span>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="ts-card" style="grid-column: 1 / -1; text-align: center; padding: 4rem 1.5rem;">
        <i data-lucide="palette" style="width: 52px; height: 52px; color: #ec4899; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1.35rem; margin-bottom: 0.5rem;">No AI Image Prompts in this Filter</h3>
        <p style="color: var(--ts-text-secondary); max-width: 480px; margin: 0 auto 1.5rem auto;">
          Try adjusting your search tokens or explore all prompt blueprints across all AI engines.
        </p>
        <a href="<?= url('/prompts/images') ?>" class="ts-btn ts-btn-outline">Clear Filters</a>
      </div>
    <?php endif; ?>
  </div>

</div>

<script>
// Click on image card opens the single prompt and fires the popunder ad
function openImagePromptWithAd(e, targetUrl, adUrl) {
  // If clicked directly on the Copy button, ignore card navigation
  if (e.target.closest('.btn-copy-card-prompt')) {
    return;
  }
  
  e.preventDefault();
  
  // 1. Trigger Popunder Ad in new background window
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

  // 2. Open single prompt page in current window
  window.location.href = targetUrl;
}

// Quick 1-click prompt copy from image tile
function copyCardPrompt(e, promptText) {
  e.stopPropagation();
  e.preventDefault();
  
  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(promptText).then(() => {
      showCopyToast();
    }).catch(() => {
      copyFallback(promptText);
    });
  } else {
    copyFallback(promptText);
  }
}

function showCopyToast() {
  if (typeof showToast === 'function') {
    showToast('Prompt blueprint copied to clipboard! 🎨', 'success');
  } else if (window.Toast) {
    Toast.success('Prompt blueprint copied to clipboard! 🎨');
  } else {
    alert('Prompt blueprint copied to clipboard! 🎨');
  }
}

function copyFallback(text) {
  const temp = document.createElement('textarea');
  temp.value = text;
  temp.style.position = 'fixed';
  temp.style.left = '-9999px';
  document.body.appendChild(temp);
  temp.select();
  try {
    document.execCommand('copy');
    showCopyToast();
  } catch(e) {}
  document.body.removeChild(temp);
}

document.addEventListener('DOMContentLoaded', () => {
  if (window.lucide) lucide.createIcons();
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
