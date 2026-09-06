<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 2.5rem; margin-bottom: 5rem;">
  <div style="text-align: center; margin-bottom: 3rem;">
    <span class="ts-badge ts-badge-green" style="margin-bottom: 0.75rem;">LLM System Prompts</span>
    <h1 style="font-size: 2.75rem; margin-bottom: 0.5rem;">Engineering Prompt Library</h1>
    <p style="color: var(--ts-text-secondary); max-width: 640px; margin: 0 auto;">
      Battle-tested, model-optimized prompt blueprints with dynamic variable injectors and one-click copy.
    </p>
  </div>

  <!-- Interactive Filter & Sorting Command HUD -->
  <div class="ts-card" style="margin-bottom: 2.25rem; padding: 1.25rem 1.5rem; background: rgba(13,21,39,0.75); border: 1px solid rgba(16,185,129,0.25); border-radius: 14px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
      
      <!-- Search Input -->
      <div style="flex: 1; min-width: 240px; position: relative;">
        <i data-lucide="search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: #64748b;"></i>
        <input type="text" id="promptSearchInput" value="<?= htmlspecialchars($currentSearch ?? '') ?>" placeholder="Search system prompts, few-shot patterns, xml tags, agents..." 
               style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 8px; padding: 0.6rem 2.2rem 0.6rem 2.2rem; color: #f8fafc; font-size: 0.88rem;"
               onkeydown="if(event.key==='Enter') applyPromptFilters()">
        <?php if (!empty($currentSearch)): ?>
          <button onclick="document.getElementById('promptSearchInput').value=''; applyPromptFilters();" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 1rem;">&times;</button>
        <?php endif; ?>
      </div>

      <!-- Controls: Category, Model & Sort -->
      <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
        <!-- Category Dropdown -->
        <div>
          <select id="promptCategorySelect" onchange="applyPromptFilters()" style="background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 8px; padding: 0.6rem 0.85rem; color: #f8fafc; font-size: 0.88rem; cursor: pointer;">
            <option value="">All Prompt Domains</option>
            <?php foreach (($categories ?? []) as $c): ?>
              <option value="<?= htmlspecialchars($c['slug']) ?>" <?= ($currentCategory ?? '') === $c['slug'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Target LLM Model Filter -->
        <div>
          <select id="promptModelSelect" onchange="applyPromptFilters()" style="background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 8px; padding: 0.6rem 0.85rem; color: #f8fafc; font-size: 0.88rem; cursor: pointer;">
            <option value="">All LLM Models</option>
            <option value="Claude" <?= ($currentModel ?? '') === 'Claude' ? 'selected' : '' ?>>Anthropic Claude</option>
            <option value="ChatGPT" <?= ($currentModel ?? '') === 'ChatGPT' ? 'selected' : '' ?>>OpenAI ChatGPT / GPT-4o</option>
            <option value="DeepSeek" <?= ($currentModel ?? '') === 'DeepSeek' ? 'selected' : '' ?>>DeepSeek R1 / V3</option>
            <option value="Gemini" <?= ($currentModel ?? '') === 'Gemini' ? 'selected' : '' ?>>Google Gemini Pro</option>
          </select>
        </div>

        <!-- Sort Dropdown -->
        <div style="display: flex; align-items: center; gap: 0.4rem;">
          <span style="font-size: 0.8rem; color: #64748b; text-transform: uppercase; font-weight: 700;">Sort:</span>
          <select id="promptSortSelect" onchange="applyPromptFilters()" style="background: #05070b; border: 1px solid rgba(16,185,129,0.35); border-radius: 8px; padding: 0.6rem 0.85rem; color: #10b981; font-weight: 600; font-size: 0.88rem; cursor: pointer;">
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
        <span style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 700;">Active Filters:</span>
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
            Model: <?= htmlspecialchars($currentModel) ?>
            <a href="javascript:void(0)" onclick="clearPromptModel()" style="color: inherit; text-decoration: none;">&times;</a>
          </span>
        <?php endif; ?>
        <?php if (!empty($currentSort) && $currentSort !== 'popular'): ?>
          <span class="ts-badge ts-badge-green" style="font-size: 0.75rem;">
            Sorted: <?= ucfirst(htmlspecialchars($currentSort)) ?>
          </span>
        <?php endif; ?>
        <a href="<?= url('/prompts') ?>" style="font-size: 0.75rem; color: #ef4444; margin-left: 0.5rem; text-decoration: underline; cursor: pointer;">
          Reset All
        </a>
      </div>
    <?php endif; ?>
  </div>

  <div class="ts-grid-3">
    <?php if (!empty($prompts)): ?>
      <?php foreach ($prompts as $p): ?>
        <div class="ts-card" style="display: flex; flex-direction: column;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
            <span class="ts-badge ts-badge-green"><?= htmlspecialchars($p['category_name'] ?? 'Prompt') ?></span>
            <span style="font-size: 0.8rem; color: var(--ts-text-tertiary);"><i data-lucide="copy" style="width: 12px; display: inline;"></i> <?= number_format((int)($p['copy_count'] ?? 0)) ?> copies</span>
          </div>
          <h3 style="font-size: 1.15rem; margin-bottom: 0.5rem;">
            <a href="<?= url('/prompts/' . $p['slug']) ?>"><?= htmlspecialchars($p['title']) ?></a>
          </h3>
          <p style="font-size: 0.875rem; color: var(--ts-text-secondary); flex: 1; margin-bottom: 1.25rem;">
            <?= htmlspecialchars($p['summary']) ?>
          </p>
          <a href="<?= url('/prompts/' . $p['slug']) ?>" class="ts-btn ts-btn-primary ts-btn-sm" style="width: 100%;">
            Inject Variables &amp; Copy ⚡
          </a>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="ts-card" style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
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

  const params = new URLSearchParams();
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
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
