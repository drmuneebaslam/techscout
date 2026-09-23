<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
  $adUrl = $adDirectUrl ?? 'https://omg10.com/4/11737440';
?>

<div class="container" style="padding-top: 2rem; margin-bottom: 5rem;">
  
  <!-- Category / Subcategory Breadcrumbs Hierarchy (If category active) -->
  <?php if (!empty($categoryInfo)): ?>
    <div style="font-size: 0.85rem; color: var(--ts-text-muted); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.45rem; flex-wrap: wrap;">
      <a href="<?= url('/prompts') ?>" style="color: var(--ts-text-secondary); text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;" class="ts-breadcrumb-link">
        <i data-lucide="terminal" style="width: 14px; height: 14px; color: var(--ts-primary);"></i>
        <span>Prompts Hub</span>
      </a>
      <span>&rsaquo;</span>
      <?php if (!empty($categoryInfo['parent_name']) && !empty($categoryInfo['parent_slug'])): ?>
        <?php 
          $parentUrl = ($categoryInfo['parent_slug'] === 'ai-image-prompts') 
            ? url('/prompts/images') 
            : url('/prompts?category=' . urlencode($categoryInfo['parent_slug']));
        ?>
        <a href="<?= $parentUrl ?>" style="color: var(--ts-text-secondary); text-decoration: none;" class="ts-breadcrumb-link">
          <?= htmlspecialchars($categoryInfo['parent_name']) ?>
        </a>
        <span>&rsaquo;</span>
      <?php endif; ?>
      <span style="color: var(--ts-text-primary); font-weight: 700;"><?= htmlspecialchars($categoryInfo['name']) ?></span>
    </div>
  <?php endif; ?>

  <div style="text-align: center; margin-bottom: 2.25rem;">
    <span class="ts-badge ts-badge-green" style="margin-bottom: 0.75rem;">AI Prompt Library &amp; Model Blueprints</span>
    <h1 style="font-size: clamp(2rem, 4vw, 2.75rem); margin-bottom: 0.5rem; font-weight: 800;">Prompt Engineering Blueprints</h1>
    <p style="color: var(--ts-text-secondary); max-width: 680px; margin: 0 auto; font-size: 1.05rem;">
      Battle-tested system prompts for LLMs and state-of-the-art visual blueprints for Midjourney, DALL-E 3, Flux.1, and Stable Diffusion.
    </p>

    <!-- Top Primary Mode Tabs -->
    <div style="display: inline-flex; background: var(--ts-bg-surface-elevated); padding: 0.35rem; border-radius: 30px; border: 1px solid var(--ts-border-main); margin-top: 1.5rem; gap: 0.35rem; flex-wrap: wrap; justify-content: center;">
      <a href="<?= url('/prompts') ?>" class="ts-btn ts-btn-sm <?= ($activeTab ?? 'text') === 'text' ? 'ts-btn-primary' : 'ts-btn-ghost' ?>" style="border-radius: 24px; padding: 0.45rem 1.25rem; font-weight: 600; text-decoration: none;">
        ⚡ LLM &amp; System Prompts
      </a>
      <a href="<?= url('/prompts/images') ?>" class="ts-btn ts-btn-sm ts-btn-outline" style="border-radius: 24px; padding: 0.45rem 1.25rem; font-weight: 700; text-decoration: none; border-color: #ec4899; color: #ec4899;">
        🎨 AI Image Prompts Gallery ↗
      </a>
      <a href="<?= url('/prompts/images/categories') ?>" class="ts-btn ts-btn-sm ts-btn-ghost" style="border-radius: 24px; padding: 0.45rem 1.25rem; font-weight: 700; text-decoration: none; color: #f43f5e;">
        🖼️ Image Categories &amp; Counts
      </a>
      <a href="<?= url('/prompts/categories') ?>" class="ts-btn ts-btn-sm ts-btn-ghost" style="border-radius: 24px; padding: 0.45rem 1.25rem; font-weight: 600; text-decoration: none;">
        📁 All Categories Hub
      </a>
      <a href="<?= url('/prompts?tab=all') ?>" class="ts-btn ts-btn-sm <?= ($activeTab ?? '') === 'all' ? 'ts-btn-primary' : 'ts-btn-ghost' ?>" style="border-radius: 24px; padding: 0.45rem 1.25rem; font-weight: 600; text-decoration: none;">
        🌟 All Blueprints
      </a>
    </div>

    <!-- Weekly Prompt of the Week Voting Contest Spotlight -->
    <div style="max-width: 820px; margin: 1.75rem auto 0 auto; background: linear-gradient(135deg, rgba(245,158,11,0.08) 0%, rgba(236,72,153,0.08) 100%); border: 1px solid rgba(245,158,11,0.3); border-radius: 14px; padding: 1.25rem 1.75rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; text-align: left;">
      <div style="display: flex; align-items: center; gap: 1rem;">
        <span style="font-size: 2rem;">🏆</span>
        <div>
          <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span class="ts-badge ts-badge-gold" style="font-size: 0.7rem; font-weight: 800;">PROMPT OF THE WEEK CONTEST</span>
            <span style="font-size: 0.75rem; color: #f59e0b; font-weight: 700;">Live Community Voting</span>
          </div>
          <div style="font-size: 0.95rem; font-weight: 700; color: var(--ts-text-primary); margin-top: 0.2rem;">
            Autonomous Multi-Agent RAG Orchestrator (DeepSeek-R1 + LangChain)
          </div>
          <div style="font-size: 0.78rem; color: var(--ts-text-secondary);">
            Curated by <strong>@alexdev</strong> &bull; 1,420 Upvotes &bull; Spotlight creator badge awarded
          </div>
        </div>
      </div>
      <a href="<?= url('/prompts/images') ?>" class="ts-btn ts-btn-primary ts-btn-sm" style="font-size: 0.8rem; padding: 0.4rem 1rem;">
        Vote &amp; Explore ↗
      </a>
    </div>
  </div>

  <?php if (($activeTab ?? '') === 'image'): ?>
    <!-- Dedicated Subcategory Pills for Image Prompts -->
    <div style="display: flex; justify-content: center; gap: 0.5rem; margin-bottom: 2rem; flex-wrap: wrap;">
      <a href="<?= url('/prompts?tab=image') ?>" class="ts-badge <?= empty($currentCategory) || $currentCategory === 'ai-image-prompts' ? 'ts-badge-pink' : 'ts-badge-cyan' ?>" style="padding: 0.45rem 1rem; font-size: 0.85rem; text-decoration: none; border-radius: 20px;">
        🌟 All Image Engines
      </a>
      <a href="<?= url('/prompts?tab=image&category=flux') ?>" class="ts-badge <?= ($currentCategory ?? '') === 'flux' ? 'ts-badge-pink' : 'ts-badge-cyan' ?>" style="padding: 0.45rem 1rem; font-size: 0.85rem; text-decoration: none; border-radius: 20px;">
        ⚡ Flux.1 Dev / Schnell
      </a>
      <a href="<?= url('/prompts?tab=image&category=midjourney') ?>" class="ts-badge <?= ($currentCategory ?? '') === 'midjourney' ? 'ts-badge-pink' : 'ts-badge-cyan' ?>" style="padding: 0.45rem 1rem; font-size: 0.85rem; text-decoration: none; border-radius: 20px;">
        🔮 Midjourney v6
      </a>
      <a href="<?= url('/prompts?tab=image&category=dall-e') ?>" class="ts-badge <?= ($currentCategory ?? '') === 'dall-e' ? 'ts-badge-pink' : 'ts-badge-cyan' ?>" style="padding: 0.45rem 1rem; font-size: 0.85rem; text-decoration: none; border-radius: 20px;">
        🎨 DALL-E 3
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
        <input type="text" id="promptSearchInput" value="<?= htmlspecialchars($currentSearch ?? '') ?>" placeholder="<?= ($activeTab ?? '') === 'image' ? 'Search Flux portraits, Midjourney styles, DALL-E lighting, SDXL...' : 'Search system prompts, few-shot patterns, xml tags, agents...' ?>" 
               style="width: 100%; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 8px; padding: 0.6rem 2.2rem 0.6rem 2.2rem; color: var(--ts-text-primary); font-size: 0.88rem;"
               onkeydown="if(event.key==='Enter') applyPromptFilters()">
        <?php if (!empty($currentSearch)): ?>
          <button onclick="document.getElementById('promptSearchInput').value=''; applyPromptFilters();" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--ts-text-secondary); cursor: pointer; font-size: 1rem;">&times;</button>
        <?php endif; ?>
      </div>

      <!-- Controls: Category, Model & Sort -->
      <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
        <!-- Category Dropdown with Hierarchy Indentation -->
        <div>
          <select id="promptCategorySelect" onchange="applyPromptFilters()" style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 8px; padding: 0.6rem 0.85rem; color: var(--ts-text-primary); font-size: 0.88rem; cursor: pointer;">
            <option value="">All Categories</option>
            <?php foreach (($categories ?? []) as $c): ?>
              <?php $isChild = !empty($c['parent_id']); ?>
              <option value="<?= htmlspecialchars($c['slug']) ?>" <?= ($currentCategory ?? '') === $c['slug'] ? 'selected' : '' ?>>
                <?= $isChild ? '&nbsp;&nbsp;↳ ' : '' ?><?= htmlspecialchars($c['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Target LLM / Engine Filter -->
        <div>
          <select id="promptModelSelect" onchange="applyPromptFilters()" style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 8px; padding: 0.6rem 0.85rem; color: var(--ts-text-primary); font-size: 0.88rem; cursor: pointer;">
            <option value="">All Models / Engines</option>
            <optgroup label="AI Image Engines">
              <option value="Flux" <?= ($currentModel ?? '') === 'Flux' ? 'selected' : '' ?>>Flux.1 Dev / Schnell</option>
              <option value="Midjourney" <?= ($currentModel ?? '') === 'Midjourney' ? 'selected' : '' ?>>Midjourney v6</option>
              <option value="DALL-E" <?= ($currentModel ?? '') === 'DALL-E' ? 'selected' : '' ?>>OpenAI DALL-E 3</option>
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
  <div class="ts-grid-3" id="promptsDisplayGrid">
    <?php if (!empty($prompts)): ?>
      <?php foreach ($prompts as $p): ?>
        <?php
          $isImagePrompt = (($p['prompt_type'] ?? '') === 'image');
          $promptText = $p['prompt_template'] ?? '';
          $imageUrl = !empty($p['featured_image_url']) ? image_url($p['featured_image_url']) : '';
          $singlePromptUrl = url('/prompts/' . $p['slug']);
          $models = !empty($p['target_models']) ? (is_array($p['target_models']) ? $p['target_models'] : json_decode($p['target_models'], true)) : ['ChatGPT'];
          $primaryModel = is_array($models) ? ($models[0] ?? 'ChatGPT') : 'ChatGPT';
        ?>

        <?php if ($isImagePrompt): ?>
          <!-- PURE IMAGE GRID CARD -->
          <div class="prompt-image-grid-card" 
               onclick="window.location.href = '<?= $singlePromptUrl ?>'"
               style="position: relative; height: 350px; border-radius: 14px; overflow: hidden; cursor: pointer; border: 1px solid var(--ts-border-main); background: #070a12; transition: transform 0.25s ease, box-shadow 0.25s ease;">
            
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

            <!-- Top Floating Model Pill -->
            <div style="position: absolute; top: 0.75rem; left: 0.75rem; right: 0.75rem; display: flex; justify-content: space-between; align-items: center; pointer-events: none; z-index: 2;">
              <span class="ts-badge ts-badge-pink" style="font-size: 0.72rem; font-weight: 800; backdrop-filter: blur(8px); background: rgba(236,72,153,0.85); color: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.4);">
                <?= htmlspecialchars($primaryModel) ?>
              </span>
            </div>

            <!-- Bottom Gradient Scrim Overlay with Title & Metadata -->
            <div style="position: absolute; inset-x: 0; bottom: 0; background: linear-gradient(to top, rgba(5,7,11,0.95) 0%, rgba(5,7,11,0.7) 65%, transparent 100%); padding: 1.25rem 1rem 0.85rem 1rem; z-index: 2; pointer-events: none;">
              <div style="display: flex; gap: 0.4rem; align-items: center; margin-bottom: 0.35rem;">
                <span class="ts-badge" style="font-size: 0.65rem; padding: 0.1rem 0.45rem; background: rgba(56,189,248,0.2); color: #38bdf8;">
                  <?= htmlspecialchars($p['category_name'] ?? 'Image Blueprint') ?>
                </span>
              </div>

              <h3 style="font-size: 0.95rem; font-weight: 700; color: #ffffff; line-height: 1.35; margin: 0 0 0.35rem 0; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                <?= htmlspecialchars($p['title']) ?>
              </h3>

              <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.72rem; color: #94a3b8;">
                <span><i data-lucide="copy" style="width: 10px; height: 10px; display: inline;"></i> <?= number_format((int)($p['copy_count'] ?? 0)) ?> copies</span>
                <span style="color: #38bdf8; font-weight: 600; display: flex; align-items: center; gap: 0.2rem;">
                  View Blueprint <i data-lucide="arrow-right" style="width: 10px; height: 10px;"></i>
                </span>
              </div>
            </div>
          </div>

        <?php else: ?>
          <!-- PURE TEXT / CODE PROMPT BLUEPRINT CARD -->
          <div class="ts-card" style="display: flex; flex-direction: column; overflow: hidden; padding: 0; border: 1px solid var(--ts-border-main); border-radius: 12px; transition: transform 0.2s ease, box-shadow 0.2s ease;">
            <!-- Sleek Code & Terminal Header (Strictly No Image Attached) -->
            <div style="background: linear-gradient(135deg, rgba(6,182,212,0.06) 0%, rgba(99,102,241,0.06) 100%); border-bottom: 1px solid var(--ts-border-main); padding: 0.75rem 1.25rem; display: flex; align-items: center; justify-content: space-between;">
              <div style="display: flex; align-items: center; gap: 0.4rem;">
                <i data-lucide="terminal" style="width: 15px; height: 15px; color: #38bdf8;"></i>
                <span style="font-family: var(--ts-font-mono); font-size: 0.75rem; color: var(--ts-text-muted); font-weight: 600;">CODE BLUEPRINT</span>
              </div>
              <span class="ts-badge ts-badge-green" style="font-size: 0.68rem; padding: 0.15rem 0.5rem;">
                <?= htmlspecialchars($p['category_name'] ?? 'Prompt') ?>
              </span>
            </div>

            <div style="padding: 1.25rem; display: flex; flex-direction: column; flex: 1;">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.6rem;">
                <span class="ts-badge ts-badge-cyan">
                  <?= htmlspecialchars($primaryModel) ?>
                </span>
                <span style="font-size: 0.8rem; color: var(--ts-text-tertiary);">
                  <i data-lucide="copy" style="width: 12px; display: inline;"></i> <?= number_format((int)($p['copy_count'] ?? 0)) ?> copies
                </span>
              </div>

              <h3 style="font-size: 1.12rem; margin-bottom: 0.45rem; line-height: 1.35;">
                <a href="<?= $singlePromptUrl ?>" style="color: var(--ts-text-primary); text-decoration: none;">
                  <?= htmlspecialchars($p['title']) ?>
                </a>
              </h3>

              <p style="font-size: 0.86rem; color: var(--ts-text-secondary); line-height: 1.5; margin-bottom: 0.9rem; flex: 1;">
                <?= htmlspecialchars($p['summary']) ?>
              </p>

              <!-- Preview Snippet -->
              <div style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 8px; padding: 0.5rem 0.75rem; margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                <span style="font-family: var(--ts-font-mono); font-size: 0.76rem; color: var(--ts-text-tertiary); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1;">
                  <?= htmlspecialchars(substr($promptText, 0, 75)) ?>...
                </span>
                <span class="ts-badge ts-badge-cyan" style="font-size: 0.68rem; padding: 0.15rem 0.45rem; flex-shrink: 0;">
                  <i data-lucide="lock" style="width: 10px; height: 10px; display: inline;"></i> Blueprint
                </span>
              </div>

              <div style="display: flex; gap: 0.5rem;">
                <a href="<?= $singlePromptUrl ?>" 
                   class="ts-btn ts-btn-primary ts-btn-sm" style="flex: 1; text-align: center; font-weight: 700;">
                  View Blueprint &amp; Copy ⚡
                </a>
              </div>
            </div>
          </div>
        <?php endif; ?>

      <?php endforeach; ?>
    <?php else: ?>
      <div class="ts-card" style="grid-column: 1 / -1; text-align: center; padding: 3.5rem;">
        <i data-lucide="terminal" style="width: 48px; height: 48px; color: var(--ts-primary); margin-bottom: 1rem;"></i>
        <h3>No Prompt Blueprints Found</h3>
        <p style="color: var(--ts-text-secondary); margin-top: 0.5rem;">Check other categories or reset search filters.</p>
        <a href="<?= url('/prompts') ?>" class="ts-btn ts-btn-primary" style="margin-top: 1rem;">View All Prompts</a>
      </div>
    <?php endif; ?>
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

document.addEventListener('DOMContentLoaded', () => {
  if (window.lucide) lucide.createIcons();
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
