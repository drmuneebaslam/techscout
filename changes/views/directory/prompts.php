<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 2.5rem; margin-bottom: 5rem;">
  <div style="text-align: center; margin-bottom: 2.5rem;">
    <span class="ts-badge ts-badge-green" style="margin-bottom: 0.75rem;">AI Prompt Library &amp; Model Blueprints</span>
    <h1 style="font-size: 2.75rem; margin-bottom: 0.5rem;">Prompt Engineering Blueprints</h1>
    <p style="color: var(--ts-text-secondary); max-width: 680px; margin: 0 auto; font-size: 1.05rem;">
      Battle-tested system prompts for LLMs and state-of-the-art visual blueprints for Midjourney, DALL-E, Flux, and Stable Diffusion.
    </p>

    <!-- Top Primary Mode Tabs -->
    <div style="display: inline-flex; background: var(--ts-bg-surface-elevated); padding: 0.35rem; border-radius: 30px; border: 1px solid var(--ts-border-main); margin-top: 1.75rem; gap: 0.35rem;">
      <a href="<?= url('/prompts?tab=text') ?>" class="ts-btn ts-btn-sm <?= ($activeTab ?? 'all') === 'text' ? 'ts-btn-primary' : 'ts-btn-ghost' ?>" style="border-radius: 24px; padding: 0.5rem 1.25rem; font-weight: 600; text-decoration: none;">
        ⚡ LLM &amp; System Prompts
      </a>
      <a href="<?= url('/prompts?tab=image') ?>" class="ts-btn ts-btn-sm <?= ($activeTab ?? '') === 'image' ? 'ts-btn-primary' : 'ts-btn-ghost' ?>" style="border-radius: 24px; padding: 0.5rem 1.25rem; font-weight: 600; text-decoration: none;">
        🎨 AI Image Prompts (Midjourney, DALL-E, Flux)
      </a>
      <a href="<?= url('/prompts') ?>" class="ts-btn ts-btn-sm <?= ($activeTab ?? 'all') === 'all' ? 'ts-btn-primary' : 'ts-btn-ghost' ?>" style="border-radius: 24px; padding: 0.5rem 1.25rem; font-weight: 600; text-decoration: none;">
        🌟 All Prompts
      </a>
    </div>
  </div>

  <?php if (($activeTab ?? '') === 'image'): ?>
    <!-- Dedicated Subcategory Pills for Image Prompts -->
    <div style="display: flex; justify-content: center; gap: 0.6rem; margin-bottom: 2rem; flex-wrap: wrap;">
      <a href="<?= url('/prompts?tab=image') ?>" class="ts-badge <?= empty($currentCategory) || $currentCategory === 'ai-image-prompts' ? 'ts-badge-pink' : 'ts-badge-cyan' ?>" style="padding: 0.45rem 1rem; font-size: 0.85rem; text-decoration: none; border-radius: 20px;">
        🌟 All Image Engines
      </a>
      <a href="<?= url('/prompts?tab=image&category=midjourney') ?>" class="ts-badge <?= ($currentCategory ?? '') === 'midjourney' ? 'ts-badge-pink' : 'ts-badge-cyan' ?>" style="padding: 0.45rem 1rem; font-size: 0.85rem; text-decoration: none; border-radius: 20px;">
        🔮 Midjourney v6
      </a>
      <a href="<?= url('/prompts?tab=image&category=dall-e') ?>" class="ts-badge <?= ($currentCategory ?? '') === 'dall-e' ? 'ts-badge-pink' : 'ts-badge-cyan' ?>" style="padding: 0.45rem 1rem; font-size: 0.85rem; text-decoration: none; border-radius: 20px;">
        🎨 DALL-E 3
      </a>
      <a href="<?= url('/prompts?tab=image&category=flux') ?>" class="ts-badge <?= ($currentCategory ?? '') === 'flux' ? 'ts-badge-pink' : 'ts-badge-cyan' ?>" style="padding: 0.45rem 1rem; font-size: 0.85rem; text-decoration: none; border-radius: 20px;">
        ⚡ Flux.1 Dev / Schnell
      </a>
      <a href="<?= url('/prompts?tab=image&category=stable-diffusion') ?>" class="ts-badge <?= ($currentCategory ?? '') === 'stable-diffusion' ? 'ts-badge-pink' : 'ts-badge-cyan' ?>" style="padding: 0.45rem 1rem; font-size: 0.85rem; text-decoration: none; border-radius: 20px;">
        🌊 Stable Diffusion XL
      </a>
    </div>
  <?php endif; ?>

  <!-- Interactive Filter & Sorting Command HUD -->
  <div class="ts-card ts-filter-hud-card" style="margin-bottom: 2.25rem; padding: 1.25rem 1.5rem; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main); border-radius: 14px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
      
      <!-- Search Input -->
      <div style="flex: 1; min-width: 240px; position: relative;">
        <i data-lucide="search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--ts-text-tertiary);"></i>
        <input type="text" id="promptSearchInput" value="<?= htmlspecialchars($currentSearch ?? '') ?>" placeholder="<?= ($activeTab ?? '') === 'image' ? 'Search Midjourney styles, DALL-E lighting, Flux portraits, SDXL...' : 'Search system prompts, few-shot patterns, xml tags, agents...' ?>" 
               style="width: 100%; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 8px; padding: 0.6rem 2.2rem 0.6rem 2.2rem; color: var(--ts-text-primary); font-size: 0.88rem;"
               onkeydown="if(event.key==='Enter') applyPromptFilters()">
        <?php if (!empty($currentSearch)): ?>
          <button onclick="document.getElementById('promptSearchInput').value=''; applyPromptFilters();" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--ts-text-secondary); cursor: pointer; font-size: 1rem;">&times;</button>
        <?php endif; ?>
      </div>

      <!-- Controls: Category, Model & Sort -->
      <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
        <!-- Category Dropdown -->
        <div>
          <select id="promptCategorySelect" onchange="applyPromptFilters()" style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 8px; padding: 0.6rem 0.85rem; color: var(--ts-text-primary); font-size: 0.88rem; cursor: pointer;">
            <option value="">All Categories</option>
            <?php foreach (($categories ?? []) as $c): ?>
              <option value="<?= htmlspecialchars($c['slug']) ?>" <?= ($currentCategory ?? '') === $c['slug'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Target LLM / Engine Filter -->
        <div>
          <select id="promptModelSelect" onchange="applyPromptFilters()" style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 8px; padding: 0.6rem 0.85rem; color: var(--ts-text-primary); font-size: 0.88rem; cursor: pointer;">
            <option value="">All Models / Engines</option>
            <optgroup label="AI Image Engines">
              <option value="Midjourney" <?= ($currentModel ?? '') === 'Midjourney' ? 'selected' : '' ?>>Midjourney v6</option>
              <option value="DALL-E" <?= ($currentModel ?? '') === 'DALL-E' ? 'selected' : '' ?>>OpenAI DALL-E 3</option>
              <option value="Flux" <?= ($currentModel ?? '') === 'Flux' ? 'selected' : '' ?>>Flux.1 Dev / Schnell</option>
              <option value="Stable Diffusion" <?= ($currentModel ?? '') === 'Stable Diffusion' ? 'selected' : '' ?>>Stable Diffusion XL</option>
            </optgroup>
            <optgroup label="LLM &amp; Code Models">
              <option value="Claude" <?= ($currentModel ?? '') === 'Claude' ? 'selected' : '' ?>>Anthropic Claude</option>
              <option value="ChatGPT" <?= ($currentModel ?? '') === 'ChatGPT' ? 'selected' : '' ?>>OpenAI ChatGPT / GPT-4o</option>
              <option value="DeepSeek" <?= ($currentModel ?? '') === 'DeepSeek' ? 'selected' : '' ?>>DeepSeek R1 / V3</option>
              <option value="Gemini" <?= ($currentModel ?? '') === 'Gemini' ? 'selected' : '' ?>>Google Gemini Pro</option>
            </optgroup>
          </select>
        </div>

        <!-- Sort Dropdown -->
        <div style="display: flex; align-items: center; gap: 0.4rem;">
          <span style="font-size: 0.8rem; color: var(--ts-text-tertiary); text-transform: uppercase; font-weight: 700;">Sort:</span>
          <select id="promptSortSelect" onchange="applyPromptFilters()" style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 8px; padding: 0.6rem 0.85rem; color: var(--ts-success); font-weight: 600; font-size: 0.88rem; cursor: pointer;">
            <option value="popular" <?= ($currentSort ?? 'popular') === 'popular' ? 'selected' : '' ?>>Most Copied ⚡</option>
            <option value="trending" <?= ($currentSort ?? '') === 'trending' ? 'selected' : '' ?>>Trending Now 🔥</option>
            <option value="latest" <?= ($currentSort ?? '') === 'latest' ? 'selected' : '' ?>>Recently Added</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Active Filter Badges -->
    <?php 
      $hasActiveFilters = !empty($currentCategory) || !empty($currentSearch) || !empty($currentModel) || ($currentSort !== 'popular' && !empty($currentSort));
    ?>
    <?php if ($hasActiveFilters): ?>
      <div style="display: flex; gap: 0.5rem; align-items: center; margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid rgba(148,163,184,0.1); flex-wrap: wrap;">
        <span style="font-size: 0.75rem; color: var(--ts-text-tertiary); text-transform: uppercase; font-weight: 700;">Active Filters:</span>
        <?php if (!empty($currentSearch)): ?>
          <span class="ts-badge ts-badge-cyan" style="font-size: 0.75rem; gap: 0.35rem;">
            Search: "<?= htmlspecialchars($currentSearch) ?>"
            <a href="javascript:void(0)" onclick="clearPromptSearch()" style="color: inherit; text-decoration: none;">&times;</a>
          </span>
        <?php endif; ?>
        <?php if (!empty($currentCategory)): ?>
          <span class="ts-badge ts-badge-pink" style="font-size: 0.75rem; gap: 0.35rem;">
            Category: <?= htmlspecialchars($currentCategory) ?>
            <a href="javascript:void(0)" onclick="clearPromptCategory()" style="color: inherit; text-decoration: none;">&times;</a>
          </span>
        <?php endif; ?>
        <?php if (!empty($currentModel)): ?>
          <span class="ts-badge" style="background: rgba(16,185,129,0.15); color: #10b981; font-size: 0.75rem; gap: 0.35rem;">
            Model/Engine: <?= htmlspecialchars($currentModel) ?>
            <a href="javascript:void(0)" onclick="clearPromptModel()" style="color: inherit; text-decoration: none;">&times;</a>
          </span>
        <?php endif; ?>
        <?php if (!empty($currentSort) && $currentSort !== 'popular'): ?>
          <span class="ts-badge ts-badge-green" style="font-size: 0.75rem;">
            Sorted: <?= ucfirst(htmlspecialchars($currentSort)) ?>
          </span>
        <?php endif; ?>
        <a href="<?= url('/prompts' . (($activeTab ?? '') === 'image' ? '?tab=image' : '')) ?>" style="font-size: 0.75rem; color: #ef4444; margin-left: 0.5rem; text-decoration: underline; cursor: pointer;">
          Reset All
        </a>
      </div>
    <?php endif; ?>
  </div>

  <!-- Prompts Grid Display -->
  <div class="ts-grid-3">
    <?php if (!empty($prompts)): ?>
      <?php foreach ($prompts as $p): ?>
        <?php
          $isImagePrompt = false;
          $modelsStr = is_string($p['target_models'] ?? '') ? $p['target_models'] : json_encode($p['target_models'] ?? []);
          if (stripos($modelsStr, 'Midjourney') !== false || stripos($modelsStr, 'DALL-E') !== false || stripos($modelsStr, 'Flux') !== false || stripos($modelsStr, 'Stable Diffusion') !== false || in_array($p['category_slug'] ?? '', ['ai-image-prompts', 'midjourney', 'dall-e', 'flux', 'stable-diffusion'])) {
              $isImagePrompt = true;
          }
          $promptText = $p['prompt_template'] ?? '';
          $imageUrl = image_url($p['featured_image_url'] ?? '');
        ?>
        <div class="ts-card" style="display: flex; flex-direction: column; overflow: hidden; padding: 0; border: 1px solid var(--ts-border-main); border-radius: 12px; transition: transform 0.2s ease, box-shadow 0.2s ease;">
          
          <?php if (($activeTab ?? '') === 'image' && !empty($imageUrl) && $imageUrl !== 'https://funwithcoding.free.nf/'): ?>
            <!-- Card Thumbnail with Lightbox trigger (Exclusive to Image Tab or verified image prompts) -->
            <div style="position: relative; height: 190px; overflow: hidden; background: var(--ts-bg-surface-elevated); cursor: pointer;" onclick="openPromptLightbox(<?= htmlspecialchars(json_encode([
              'title' => $p['title'],
              'image' => $imageUrl,
              'prompt' => $promptText,
              'category' => $p['category_name'] ?? 'Prompt',
              'slug' => $p['slug'],
              'models' => $modelsStr
            ])) ?>)">
              <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($p['title']) ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
              <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 60%); display: flex; align-items: flex-end; padding: 0.75rem;">
                <span class="ts-badge ts-badge-cyan" style="font-size: 0.72rem; backdrop-filter: blur(4px);">
                  <i data-lucide="maximize-2" style="width: 12px; height: 12px; display: inline;"></i> Click to Enlarge / Lightbox
                </span>
              </div>
            </div>
          <?php endif; ?>

          <div style="padding: 1.25rem; display: flex; flex-direction: column; flex: 1;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.6rem;">
              <span class="ts-badge <?= $isImagePrompt ? 'ts-badge-pink' : 'ts-badge-green' ?>">
                <?= htmlspecialchars($p['category_name'] ?? 'Prompt') ?>
              </span>
              <span style="font-size: 0.8rem; color: var(--ts-text-tertiary);">
                <i data-lucide="eye" style="width: 12px; display: inline;"></i> <?= number_format((int)($p['copy_count'] ?? 0)) ?> views
              </span>
            </div>

            <h3 style="font-size: 1.12rem; margin-bottom: 0.45rem; line-height: 1.35;">
              <a href="<?= url('/prompts/' . $p['slug']) ?>" style="color: var(--ts-text-primary); text-decoration: none;">
                <?= htmlspecialchars($p['title']) ?>
              </a>
            </h3>

            <p style="font-size: 0.86rem; color: var(--ts-text-secondary); line-height: 1.5; margin-bottom: 0.9rem; flex: 1;">
              <?= htmlspecialchars($p['summary']) ?>
            </p>

            <!-- Preview Snippet (No Direct Copy Button in Directory) -->
            <div style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 8px; padding: 0.5rem 0.75rem; margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
              <span style="font-family: var(--ts-font-mono); font-size: 0.76rem; color: var(--ts-text-tertiary); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1;">
                <?= htmlspecialchars(substr($promptText, 0, 75)) ?>...
              </span>
              <span class="ts-badge ts-badge-cyan" style="font-size: 0.68rem; padding: 0.15rem 0.45rem; flex-shrink: 0;">
                <i data-lucide="lock" style="width: 10px; height: 10px; display: inline;"></i> Unlock
              </span>
            </div>

            <div style="display: flex; gap: 0.5rem;">
              <a href="<?= url('/prompts/' . $p['slug']) ?>" class="ts-btn ts-btn-primary ts-btn-sm" style="flex: 1; text-align: center;">
                <?= $isImagePrompt ? 'View Parameters &amp; Unlock 🎨' : 'View Blueprint &amp; Copy ⚡' ?>
              </a>
              <?php if (!empty($imageUrl) && $imageUrl !== 'https://funwithcoding.free.nf/'): ?>
                <button type="button" class="ts-btn ts-btn-outline ts-btn-sm" onclick="openPromptLightbox(<?= htmlspecialchars(json_encode([
                  'title' => $p['title'],
                  'image' => $imageUrl,
                  'prompt' => $promptText,
                  'category' => $p['category_name'] ?? 'Prompt',
                  'slug' => $p['slug'],
                  'models' => $modelsStr
                ])) ?>)" title="View High-Resolution Image Lightbox">
                  <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                </button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="ts-card" style="grid-column: 1 / -1; text-align: center; padding: 3.5rem;">
        <i data-lucide="image" style="width: 48px; height: 48px; color: var(--ts-primary); margin-bottom: 1rem;"></i>
        <h3>No Prompt Blueprints Found</h3>
        <p style="color: var(--ts-text-secondary); margin-top: 0.5rem;">Check other categories or reset search filters.</p>
        <a href="<?= url('/prompts') ?>" class="ts-btn ts-btn-primary" style="margin-top: 1rem;">View All Prompts</a>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- ========================================================================= -->
<!-- IMAGE ATTACHMENT LIGHTBOX MODAL                                           -->
<!-- ========================================================================= -->
<div id="promptLightboxModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.92); backdrop-filter: blur(10px); z-index: 99999; align-items: center; justify-content: center; padding: 1.5rem;" onclick="if(event.target===this) closePromptLightbox()">
  <div class="ts-card" style="width: 100%; max-width: 920px; max-height: 92vh; overflow-y: auto; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main); border-radius: 16px; padding: 1.5rem; position: relative;" onclick="event.stopPropagation()">
    
    <!-- Close Button -->
    <button type="button" onclick="closePromptLightbox()" style="position: absolute; right: 1rem; top: 1rem; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); color: var(--ts-text-primary); border-radius: 50%; width: 34px; height: 34px; font-size: 1.1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10;">
      &times;
    </button>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; align-items: start;">
      <!-- High-Res Image Display -->
      <div style="border-radius: 10px; overflow: hidden; background: #000; border: 1px solid var(--ts-border-main); text-align: center;">
        <img id="lbImage" src="" alt="Prompt Visual" style="width: 100%; max-height: 520px; object-fit: contain; display: block;">
      </div>

      <!-- Prompt Details & Copy Studio -->
      <div style="display: flex; flex-direction: column;">
        <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.6rem;">
          <span class="ts-badge ts-badge-pink" id="lbCategory">Image Prompt</span>
          <span class="ts-badge ts-badge-cyan" id="lbEngine">AI Engine</span>
        </div>

        <h2 id="lbTitle" style="font-size: 1.35rem; margin-bottom: 0.75rem; color: var(--ts-text-primary); line-height: 1.3;"></h2>

        <label style="font-size: 0.8rem; font-weight: 700; color: var(--ts-text-tertiary); text-transform: uppercase; margin-bottom: 0.35rem; display: block;">
          Engine Prompt &amp; Parameter Command:
        </label>
        
        <div style="position: relative; margin-bottom: 1.25rem;">
          <textarea id="lbPromptText" readonly style="width: 100%; height: 180px; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 8px; padding: 0.85rem; color: var(--ts-text-primary); font-family: var(--ts-font-mono); font-size: 0.84rem; line-height: 1.5; resize: none;"></textarea>
        </div>

        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
          <button type="button" onclick="copyLightboxPrompt()" class="ts-btn ts-btn-primary" style="flex: 1;">
            <i data-lucide="copy" style="width: 15px; height: 15px;"></i> Copy Prompt to Clipboard ⚡
          </button>
          <a id="lbBlueprintLink" href="#" class="ts-btn ts-btn-outline">
            Full Blueprint Details →
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function applyPromptFilters() {
  const search = document.getElementById('promptSearchInput').value.trim();
  const cat = document.getElementById('promptCategorySelect').value;
  const model = document.getElementById('promptModelSelect').value;
  const sort = document.getElementById('promptSortSelect').value;
  const tab = '<?= htmlspecialchars($activeTab ?? 'all') ?>';

  const params = new URLSearchParams();
  if (tab && tab !== 'all') params.set('tab', tab);
  if (sort && sort !== 'popular') params.set('sort', sort);
  if (cat) params.set('category', cat);
  if (model) params.set('model', model);
  if (search) params.set('search', search);

  const query = params.toString();
  window.location.href = '<?= url('/prompts') ?>' + (query ? '?' + query : '');
}

function clearPromptSearch() {
  document.getElementById('promptSearchInput').value = '';
  applyPromptFilters();
}

function clearPromptCategory() {
  document.getElementById('promptCategorySelect').value = '';
  applyPromptFilters();
}

function clearPromptModel() {
  document.getElementById('promptModelSelect').value = '';
  applyPromptFilters();
}

// Lightbox controller
function openPromptLightbox(data) {
  document.getElementById('lbImage').src = data.image;
  document.getElementById('lbTitle').innerText = data.title;
  document.getElementById('lbPromptText').value = data.prompt;
  document.getElementById('lbCategory').innerText = data.category || 'AI Image';
  
  let engine = 'Midjourney / Diffusion';
  try {
    const m = JSON.parse(data.models);
    if (Array.isArray(m) && m.length) engine = m.join(' • ');
  } catch(e) {
    if (data.models) engine = data.models;
  }
  document.getElementById('lbEngine').innerText = engine;
  document.getElementById('lbBlueprintLink').href = '<?= url('/prompts/') ?>' + data.slug;

  const modal = document.getElementById('promptLightboxModal');
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
  if (window.lucide) lucide.createIcons();
}

function closePromptLightbox() {
  const modal = document.getElementById('promptLightboxModal');
  modal.style.display = 'none';
  document.body.style.overflow = '';
}

document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') closePromptLightbox();
});

async function copyLightboxPrompt() {
  const text = document.getElementById('lbPromptText').value;
  try {
    await navigator.clipboard.writeText(text);
    if (typeof showToast === 'function') {
      showToast('Prompt copied to clipboard! Ready to paste into Midjourney / DALL-E / Flux ⚡', 'success');
    } else {
      alert('Prompt copied to clipboard!');
    }
  } catch(e) {
    document.getElementById('lbPromptText').select();
    document.execCommand('copy');
    alert('Prompt copied!');
  }
}

async function quickCopyPrompt(id, text) {
  try {
    await navigator.clipboard.writeText(text);
    if (typeof showToast === 'function') {
      showToast('Prompt blueprint copied! ⚡', 'success');
    } else {
      alert('Prompt copied to clipboard!');
    }
    // Increment telemetry copy count
    fetch('<?= url('/api/v1/prompts/') ?>' + id + '/copy', { method: 'POST' }).catch(() => {});
  } catch(e) {
    navigator.clipboard.writeText(text);
  }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
