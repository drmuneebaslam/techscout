<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 2.5rem; margin-bottom: 5rem;">
  
  <!-- Breadcrumbs & Directory Header -->
  <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; font-size: 0.85rem; color: var(--ts-text-muted);">
    <a href="<?= url('/prompts') ?>" style="color: var(--ts-text-muted); text-decoration: none;">Prompts</a>
    <span>/</span>
    <span style="color: #f472b6; font-weight: 700;">AI Image Prompts Sub-Directory</span>
  </div>

  <div style="text-align: center; margin-bottom: 2.5rem;">
    <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(236,72,153,0.12); border: 1px solid rgba(236,72,153,0.3); padding: 0.35rem 0.9rem; border-radius: 20px; margin-bottom: 0.75rem;">
      <span style="font-size: 1rem;">🎨</span>
      <span style="font-size: 0.8rem; font-weight: 800; color: #ec4899; text-transform: uppercase; letter-spacing: 0.05em;">Specialized Image Studio Directory</span>
    </div>
    
    <h1 style="font-size: clamp(2rem, 4vw, 3rem); font-weight: 900; margin-bottom: 0.65rem; letter-spacing: -0.02em;">
      Visual AI Prompts &amp; Art Blueprints
    </h1>
    <p style="color: var(--ts-text-secondary); max-width: 720px; margin: 0 auto; font-size: 1.05rem; line-height: 1.6;">
      Curated high-fidelity prompts with camera parameters, lighting tokens, and aspect ratio ratios for Midjourney, DALL-E 3, Flux.1, and SDXL.
    </p>

    <!-- Sub-Directory Navigation Bar -->
    <div style="display: inline-flex; background: var(--ts-bg-surface-elevated); padding: 0.35rem; border-radius: 30px; border: 1px solid var(--ts-border-main); margin-top: 1.75rem; gap: 0.35rem;">
      <a href="<?= url('/prompts') ?>" class="ts-btn ts-btn-sm ts-btn-ghost" style="border-radius: 24px; padding: 0.5rem 1.25rem; font-weight: 600; text-decoration: none;">
        ⚡ All Prompts &amp; LLMs
      </a>
      <a href="<?= url('/prompts/images') ?>" class="ts-btn ts-btn-sm ts-btn-primary" style="border-radius: 24px; padding: 0.5rem 1.25rem; font-weight: 700; text-decoration: none; background: linear-gradient(135deg, #ec4899, #8b5cf6);">
        🎨 AI Image Prompts Sub-Directory
      </a>
    </div>
  </div>

  <!-- Specialized Engine Filter Bar -->
  <div style="display: flex; justify-content: center; gap: 0.5rem; margin-bottom: 1.75rem; flex-wrap: wrap;">
    <?php
      $engines = [
        '' => '🌟 All Engines',
        'Midjourney' => '🔮 Midjourney v6',
        'DALL-E' => '🎨 DALL-E 3',
        'Flux' => '⚡ Flux.1 Dev',
        'Stable Diffusion' => '🌊 SDXL Turbo',
        'Leonardo' => '🦁 Leonardo AI'
      ];
      $activeEngine = $currentModel ?? '';
    ?>
    <?php foreach ($engines as $engKey => $engLabel): ?>
      <a href="<?= url('/prompts/images' . (!empty($engKey) ? '?model=' . urlencode($engKey) : '')) ?>" 
         class="ts-badge <?= ($activeEngine === $engKey) ? 'ts-badge-pink' : 'ts-badge-cyan' ?>" 
         style="padding: 0.45rem 1rem; font-size: 0.85rem; text-decoration: none; border-radius: 20px; font-weight: <?= ($activeEngine === $engKey) ? '800' : '600' ?>;">
        <?= $engLabel ?>
      </a>
    <?php endforeach; ?>
  </div>

  <!-- Dedicated Image Sub-Categories Horizontal Scroller -->
  <div style="display: flex; gap: 0.6rem; overflow-x: auto; padding-bottom: 0.75rem; margin-bottom: 2rem; scrollbar-width: thin;">
    <?php
      $imageSubCats = [
        '' => 'All Art Styles',
        'photorealism' => '📸 Photorealism & Portraits',
        '3d-renders' => '🧊 3D Renders & UE5',
        'cyberpunk' => '🌆 Cyberpunk & Sci-Fi',
        'anime' => '🌸 Anime & Manga Art',
        'architecture' => '🏛️ Architecture & Interiors',
        'fantasy' => '🐉 Fantasy & Mythological',
        'cinematic' => '🎬 Cinematic Stills',
        'vector' => '✒️ Vector Art & Logos'
      ];
      $activeCat = $currentCategory ?? '';
    ?>
    <?php foreach ($imageSubCats as $catKey => $catLabel): ?>
      <a href="<?= url('/prompts/images' . (!empty($catKey) ? '?category=' . urlencode($catKey) : '')) ?>" 
         style="white-space: nowrap; padding: 0.4rem 0.95rem; border-radius: 8px; font-size: 0.82rem; font-weight: 600; text-decoration: none; border: 1px solid <?= ($activeCat === $catKey) ? 'var(--ts-primary)' : 'var(--ts-border-main)' ?>; background: <?= ($activeCat === $catKey) ? 'rgba(56,189,248,0.1)' : 'var(--ts-bg-surface-elevated)' ?>; color: <?= ($activeCat === $catKey) ? '#38bdf8' : 'var(--ts-text-secondary)' ?>; transition: all 0.2s ease;">
        <?= $catLabel ?>
      </a>
    <?php endforeach; ?>
  </div>

  <!-- Search & Aspect Ratio Command Bar -->
  <div class="ts-card" style="margin-bottom: 2.25rem; padding: 1.25rem 1.5rem; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main); border-radius: 14px;">
    <form method="GET" action="<?= url('/prompts/images') ?>" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
      <div style="flex: 1; min-width: 240px; position: relative;">
        <i data-lucide="search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--ts-text-tertiary);"></i>
        <input type="text" name="search" value="<?= htmlspecialchars($currentSearch ?? '') ?>" placeholder="Search Midjourney stylize tokens, lighting, camera models, aspect ratios..." 
               style="width: 100%; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 8px; padding: 0.6rem 2.2rem 0.6rem 2.2rem; color: var(--ts-text-primary); font-size: 0.88rem;">
      </div>

      <div style="display: flex; gap: 0.75rem; align-items: center;">
        <select name="sort" onchange="this.form.submit()" style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 8px; padding: 0.6rem 0.85rem; color: var(--ts-success); font-weight: 600; font-size: 0.88rem; cursor: pointer;">
          <option value="popular" <?= ($currentSort ?? 'popular') === 'popular' ? 'selected' : '' ?>>Most Copied ⚡</option>
          <option value="trending" <?= ($currentSort ?? '') === 'trending' ? 'selected' : '' ?>>Trending Now 🔥</option>
          <option value="latest" <?= ($currentSort ?? '') === 'latest' ? 'selected' : '' ?>>Recently Added</option>
        </select>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="padding: 0.6rem 1rem;">Filter</button>
      </div>
    </form>
  </div>

  <!-- Image Prompts Visual Grid (NO Lightbox, Direct Link to Single View) -->
  <div class="ts-grid-3">
    <?php if (!empty($prompts)): ?>
      <?php foreach ($prompts as $p): ?>
        <?php
          $promptText = $p['prompt_template'] ?? '';
          $imageUrl = !empty($p['featured_image_url']) ? image_url($p['featured_image_url']) : '';
          $models = !empty($p['target_models']) ? (is_array($p['target_models']) ? $p['target_models'] : json_decode($p['target_models'], true)) : ['Midjourney'];
          $primaryModel = is_array($models) ? ($models[0] ?? 'Midjourney') : 'Midjourney';
        ?>
        <div class="ts-card" style="display: flex; flex-direction: column; overflow: hidden; padding: 0; border: 1px solid var(--ts-border-main); border-radius: 12px; transition: transform 0.2s ease, box-shadow 0.2s ease;">
          
          <!-- Visual Hero Thumbnail (Direct Click to Blueprint) -->
          <a href="<?= url('/prompts/' . $p['slug']) ?>" style="position: relative; height: 220px; overflow: hidden; background: var(--ts-bg-surface-elevated); display: block; text-decoration: none;">
            <?php if (!empty($imageUrl) && $imageUrl !== 'https://funwithcoding.free.nf/'): ?>
              <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($p['title']) ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.35s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
            <?php else: ?>
              <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, rgba(236,72,153,0.1), rgba(139,92,246,0.1)); color: #ec4899; font-size: 2.5rem;">
                🎨
              </div>
            <?php endif; ?>

            <div style="position: absolute; top: 0.75rem; left: 0.75rem; display: flex; gap: 0.35rem;">
              <span class="ts-badge ts-badge-pink" style="font-size: 0.68rem; font-weight: 800; backdrop-filter: blur(8px);">
                <?= htmlspecialchars($primaryModel) ?>
              </span>
              <span class="ts-badge ts-badge-cyan" style="font-size: 0.68rem; font-weight: 800; backdrop-filter: blur(8px);">
                --ar 16:9
              </span>
            </div>

            <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 55%); display: flex; align-items: flex-end; padding: 0.85rem;">
              <span style="font-size: 0.78rem; color: #38bdf8; font-weight: 700; display: flex; align-items: center; gap: 0.35rem;">
                <i data-lucide="sparkles" style="width: 13px; height: 13px;"></i> View Blueprint &amp; Settings &rarr;
              </span>
            </div>
          </a>

          <div style="padding: 1.25rem; display: flex; flex-direction: column; flex: 1;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
              <span class="ts-badge ts-badge-green" style="font-size: 0.72rem;">
                <?= htmlspecialchars($p['category_name'] ?? 'Visual Prompt') ?>
              </span>
              <span style="font-size: 0.78rem; color: var(--ts-text-tertiary);">
                <i data-lucide="copy" style="width: 11px; display: inline;"></i> <?= number_format((int)($p['copy_count'] ?? 0)) ?> copies
              </span>
            </div>

            <h3 style="font-size: 1.08rem; margin-bottom: 0.45rem; line-height: 1.35;">
              <a href="<?= url('/prompts/' . $p['slug']) ?>" style="color: var(--ts-text-primary); text-decoration: none;">
                <?= htmlspecialchars($p['title']) ?>
              </a>
            </h3>

            <p style="font-size: 0.85rem; color: var(--ts-text-secondary); line-height: 1.5; margin-bottom: 1rem; flex: 1;">
              <?= htmlspecialchars($p['summary']) ?>
            </p>

            <a href="<?= url('/prompts/' . $p['slug']) ?>" class="ts-btn ts-btn-primary ts-btn-sm" style="text-align: center; font-weight: 700;">
              Unlock Prompt Blueprint ⚡
            </a>
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

<?php require __DIR__ . '/../layouts/footer.php'; ?>
