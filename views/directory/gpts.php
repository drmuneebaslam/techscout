<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 2.5rem; margin-bottom: 5rem;">
  <div style="text-align: center; margin-bottom: 3rem;">
    <span class="ts-badge ts-badge-pink" style="margin-bottom: 0.75rem;">AI Agents Hub</span>
    <h1 style="font-size: 2.75rem; margin-bottom: 0.5rem;">Custom GPTs & Autonomous Agents</h1>
    <p style="color: var(--ts-text-secondary); max-width: 640px; margin: 0 auto;">
      Curated specialized agents, system designers, and code architects engineered for maximum builder throughput.
    </p>

    <?php if (!empty($currentCategory)): ?>
      <div style="display: inline-flex; align-items: center; gap: 0.6rem; background: rgba(99,102,241,0.12); border: 1px solid rgba(99,102,241,0.3); padding: 0.35rem 0.9rem; border-radius: 9999px; margin-top: 1rem;">
        <span style="font-size: 0.85rem; color: #a5b4fc;">Filtered Agent Domain:</span>
        <strong style="color: #f8fafc; font-size: 0.9rem;"><?= htmlspecialchars($currentCategory) ?></strong>
        <a href="<?= url('/gpts') ?>" class="ts-badge ts-badge-pink" style="text-decoration: none; padding: 0.1rem 0.4rem; font-size: 0.7rem;">Clear ✕</a>
      </div>
    <?php endif; ?>
  </div>

  <!-- Interactive Filter & Sorting Command HUD -->
  <div class="ts-card" style="margin-bottom: 2.25rem; padding: 1.25rem 1.5rem; background: rgba(13,21,39,0.75); border: 1px solid rgba(236,72,153,0.25); border-radius: 14px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
      
      <!-- Search Input -->
      <div style="flex: 1; min-width: 240px; position: relative;">
        <i data-lucide="search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: #64748b;"></i>
        <input type="text" id="gptSearchInput" value="<?= htmlspecialchars($currentSearch ?? '') ?>" placeholder="Search specialized GPTs, agents, system architects, assistants..." 
               style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 8px; padding: 0.6rem 2.2rem 0.6rem 2.2rem; color: #f8fafc; font-size: 0.88rem;"
               onkeydown="if(event.key==='Enter') applyGptFilters()">
        <?php if (!empty($currentSearch)): ?>
          <button onclick="document.getElementById('gptSearchInput').value=''; applyGptFilters();" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 1rem;">&times;</button>
        <?php endif; ?>
      </div>

      <!-- Controls: Category, Target Model & Sort -->
      <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
        <!-- Category Dropdown -->
        <div>
          <select id="gptCategorySelect" onchange="applyGptFilters()" style="background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 8px; padding: 0.6rem 0.85rem; color: #f8fafc; font-size: 0.88rem; cursor: pointer;">
            <option value="">All Agent Domains</option>
            <?php foreach (($categories ?? []) as $c): ?>
              <option value="<?= htmlspecialchars($c['slug']) ?>" <?= ($currentCategory ?? '') === $c['slug'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Target Model Architecture Filter -->
        <div>
          <select id="gptModelSelect" onchange="applyGptFilters()" style="background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 8px; padding: 0.6rem 0.85rem; color: #f8fafc; font-size: 0.88rem; cursor: pointer;">
            <option value="">All Architectures</option>
            <option value="GPT-4o" <?= ($currentModel ?? '') === 'GPT-4o' ? 'selected' : '' ?>>OpenAI GPT-4o / Canvas</option>
            <option value="Claude" <?= ($currentModel ?? '') === 'Claude' ? 'selected' : '' ?>>Claude 3.5 Sonnet Artifacts</option>
            <option value="DeepSeek" <?= ($currentModel ?? '') === 'DeepSeek' ? 'selected' : '' ?>>DeepSeek R1 Agent</option>
            <option value="Custom" <?= ($currentModel ?? '') === 'Custom' ? 'selected' : '' ?>>Autonomous Multi-Agent</option>
          </select>
        </div>

        <!-- Sort Dropdown -->
        <div style="display: flex; align-items: center; gap: 0.4rem;">
          <span style="font-size: 0.8rem; color: #64748b; text-transform: uppercase; font-weight: 700;">Sort:</span>
          <select id="gptSortSelect" onchange="applyGptFilters()" style="background: #05070b; border: 1px solid rgba(236,72,153,0.35); border-radius: 8px; padding: 0.6rem 0.85rem; color: #ec4899; font-weight: 600; font-size: 0.88rem; cursor: pointer;">
            <option value="popular" <?= ($currentSort ?? 'popular') === 'popular' ? 'selected' : '' ?>>Most Popular ⚡</option>
            <option value="trending" <?= ($currentSort ?? '') === 'trending' ? 'selected' : '' ?>>Trending Now 🔥</option>
            <option value="latest" <?= ($currentSort ?? '') === 'latest' ? 'selected' : '' ?>>Recently Launched</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Active Filter Badges -->
    <?php 
      $hasActiveFilters = !empty($currentCategory) || !empty($currentSearch) || !empty($currentModel) || ($currentSort ?? 'popular') !== 'popular';
    ?>
    <?php if ($hasActiveFilters): ?>
      <div style="margin-top: 1rem; padding-top: 0.85rem; border-top: 1px solid rgba(148,163,184,0.1); display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
        <span style="font-size: 0.8rem; color: #94a3b8;">Active Filters:</span>
        
        <?php if (!empty($currentSearch)): ?>
          <span class="ts-badge ts-badge-cyan" style="font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.35rem;">
            Keyword: "<?= htmlspecialchars($currentSearch) ?>"
            <a href="javascript:void(0)" onclick="document.getElementById('gptSearchInput').value=''; applyGptFilters();" style="color: inherit; text-decoration: none; font-weight: bold;">✕</a>
          </span>
        <?php endif; ?>

        <?php if (!empty($currentCategory)): ?>
          <span class="ts-badge ts-badge-indigo" style="font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.35rem;">
            Domain: <?= htmlspecialchars($categoryInfo['name'] ?? $currentCategory) ?>
            <a href="javascript:void(0)" onclick="document.getElementById('gptCategorySelect').value=''; applyGptFilters();" style="color: inherit; text-decoration: none; font-weight: bold;">✕</a>
          </span>
        <?php endif; ?>

        <?php if (!empty($currentModel)): ?>
          <span class="ts-badge ts-badge-amber" style="font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.35rem;">
            Model: <?= htmlspecialchars($currentModel) ?>
            <a href="javascript:void(0)" onclick="document.getElementById('gptModelSelect').value=''; applyGptFilters();" style="color: inherit; text-decoration: none; font-weight: bold;">✕</a>
          </span>
        <?php endif; ?>

        <?php if (($currentSort ?? 'popular') !== 'popular'): ?>
          <span class="ts-badge ts-badge-pink" style="font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.35rem;">
            Sort: <?= ucfirst(htmlspecialchars($currentSort)) ?>
            <a href="javascript:void(0)" onclick="document.getElementById('gptSortSelect').value='popular'; applyGptFilters();" style="color: inherit; text-decoration: none; font-weight: bold;">✕</a>
          </span>
        <?php endif; ?>

        <a href="<?= url('/gpts') ?>" style="font-size: 0.78rem; color: #ef4444; margin-left: 0.5rem; text-decoration: none;">Reset All</a>
      </div>
    <?php endif; ?>
  </div>

  <div class="ts-grid-3">
    <?php if (!empty($gpts)): ?>
      <?php foreach ($gpts as $gpt): ?>
        <div class="ts-card" style="display: flex; flex-direction: column;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
            <span class="ts-badge ts-badge-cyan"><?= htmlspecialchars($gpt['model_base'] ?? 'GPT-4o') ?></span>
            <span style="font-size: 0.8rem; color: var(--ts-text-tertiary);">By <?= htmlspecialchars($gpt['author_name'] ?? 'TechScout Builder') ?></span>
          </div>
          <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem;">
            <a href="<?= url('/gpts/' . $gpt['slug']) ?>"><?= htmlspecialchars($gpt['title']) ?></a>
          </h3>
          <p style="font-size: 0.875rem; color: var(--ts-text-secondary); flex: 1; margin-bottom: 1.25rem; line-height: 1.5;">
            <?= htmlspecialchars($gpt['summary']) ?>
          </p>
          <div style="display: flex; gap: 0.5rem;">
            <a href="<?= url('/gpts/' . $gpt['slug']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="flex: 1;">
              View Details & Blueprint
            </a>
            <?php if (!empty($gpt['openai_url'])): ?>
              <a href="<?= url('/go/' . $gpt['slug']) ?>" target="_blank" class="ts-btn ts-btn-accent ts-btn-sm" title="Launch GPT in OpenAI">
                <i data-lucide="external-link" style="width: 14px;"></i>
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="ts-card" style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
        <i data-lucide="bot" style="width: 48px; height: 48px; color: var(--ts-primary); margin-bottom: 1rem;"></i>
        <h3>No Custom GPTs Found</h3>
        <p style="color: var(--ts-text-secondary); margin-top: 0.5rem;">Try adjusting your search query, agent domain, or architecture filters.</p>
        <a href="<?= url('/gpts') ?>" class="ts-btn ts-btn-primary" style="margin-top: 1rem;">Clear Filters</a>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
function applyGptFilters() {
  const search = document.getElementById('gptSearchInput')?.value.trim() || '';
  const category = document.getElementById('gptCategorySelect')?.value || '';
  const model = document.getElementById('gptModelSelect')?.value || '';
  const sort = document.getElementById('gptSortSelect')?.value || 'popular';

  const params = new URLSearchParams();
  if (search) params.set('search', search);
  if (category) params.set('category', category);
  if (model) params.set('model', model);
  if (sort && sort !== 'popular') params.set('sort', sort);

  const queryStr = params.toString();
  window.location.href = '<?= url('/gpts') ?>' + (queryStr ? '?' + queryStr : '');
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
