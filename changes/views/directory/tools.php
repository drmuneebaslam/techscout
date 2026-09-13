<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 2.5rem; margin-bottom: 5rem;">
  <div style="text-align: center; margin-bottom: 3rem;">
    <span class="ts-badge ts-badge-pink" style="margin-bottom: 0.75rem;">AI & Software Ecosystem</span>
    <h1 style="font-size: 2.75rem; margin-bottom: 0.5rem;">AI Tools & Software Directory</h1>
    <p style="color: var(--ts-text-secondary); max-width: 640px; margin: 0 auto;">
      Discover, benchmark, and compare modern developer tools, generative AI models, and SaaS applications.
    </p>
    <div style="display: inline-flex; gap: 0.5rem; margin-top: 1.25rem;">
      <a href="<?= url('/tools') ?>" class="ts-btn ts-btn-sm ts-btn-primary" style="border-radius: 20px; padding: 0.45rem 1.2rem; text-decoration: none;">
        ⚡ All Tools Directory
      </a>
      <a href="<?= url('/tools/categories') ?>" class="ts-btn ts-btn-sm ts-btn-outline" style="border-radius: 20px; padding: 0.45rem 1.2rem; text-decoration: none;">
        📁 Browse Tool Categories &amp; Counts ↗
      </a>
    </div>
  </div>

  <!-- Interactive Filter & Sorting Command HUD -->
  <div class="ts-card ts-filter-hud-card" style="margin-bottom: 2.25rem; padding: 1.25rem 1.5rem; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main); border-radius: 14px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
      
      <!-- Search Input -->
      <div style="flex: 1; min-width: 240px; position: relative;">
        <i data-lucide="search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--ts-text-tertiary);"></i>
        <input type="text" id="toolSearchInput" value="<?= htmlspecialchars($currentSearch ?? '') ?>" placeholder="Search AI tools, LLMs, code assistants, devops..." 
               style="width: 100%; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 8px; padding: 0.6rem 2.2rem 0.6rem 2.2rem; color: var(--ts-text-primary); font-size: 0.88rem;"
               onkeydown="if(event.key==='Enter') applyToolFilters()">
        <?php if (!empty($currentSearch)): ?>
          <button onclick="document.getElementById('toolSearchInput').value=''; applyToolFilters();" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--ts-text-secondary); cursor: pointer; font-size: 1rem;">&times;</button>
        <?php endif; ?>
      </div>

      <!-- Controls: Category, Pricing & Sort -->
      <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
        <!-- Category Dropdown -->
        <div>
          <select id="toolCategorySelect" onchange="applyToolFilters()" style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 8px; padding: 0.6rem 0.85rem; color: var(--ts-text-primary); font-size: 0.88rem; cursor: pointer;">
            <option value="">All Tool Domains</option>
            <?php foreach (($categories ?? []) as $c): ?>
              <option value="<?= htmlspecialchars($c['slug']) ?>" <?= ($currentCategory ?? '') === $c['slug'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Pricing Filter -->
        <div>
          <select id="toolPricingSelect" onchange="applyToolFilters()" style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 8px; padding: 0.6rem 0.85rem; color: var(--ts-text-primary); font-size: 0.88rem; cursor: pointer;">
            <option value="">All Pricing Models</option>
            <option value="free" <?= strtolower($currentPricing ?? '') === 'free' ? 'selected' : '' ?>>🟢 100% Free</option>
            <option value="freemium" <?= strtolower($currentPricing ?? '') === 'freemium' ? 'selected' : '' ?>>🟡 Freemium</option>
            <option value="paid" <?= strtolower($currentPricing ?? '') === 'paid' ? 'selected' : '' ?>>💎 Paid / Commercial</option>
            <option value="open_source" <?= strtolower($currentPricing ?? '') === 'open_source' ? 'selected' : '' ?>>💻 Open Source</option>
          </select>
        </div>

        <!-- Sort Dropdown -->
        <div style="display: flex; align-items: center; gap: 0.4rem;">
          <span style="font-size: 0.8rem; color: var(--ts-text-tertiary); text-transform: uppercase; font-weight: 700;">Sort:</span>
          <select id="toolSortSelect" onchange="applyToolFilters()" style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 8px; padding: 0.6rem 0.85rem; color: var(--ts-secondary); font-weight: 600; font-size: 0.88rem; cursor: pointer;">
            <option value="popular" <?= ($currentSort ?? 'popular') === 'popular' ? 'selected' : '' ?>>Most Popular</option>
            <option value="rating" <?= ($currentSort ?? '') === 'rating' ? 'selected' : '' ?>>Top Rated ⭐</option>
            <option value="trending" <?= ($currentSort ?? '') === 'trending' ? 'selected' : '' ?>>Trending Now 🔥</option>
            <option value="latest" <?= ($currentSort ?? '') === 'latest' ? 'selected' : '' ?>>Recently Scouted</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Active Filter Badges -->
    <?php 
      $hasActiveFilters = !empty($currentCategory) || !empty($currentSearch) || !empty($currentPricing) || ($currentSort !== 'popular' && !empty($currentSort));
    ?>
    <?php if ($hasActiveFilters): ?>
      <div style="display: flex; gap: 0.5rem; align-items: center; margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid rgba(148,163,184,0.1); flex-wrap: wrap;">
        <span style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 700;">Active Filters:</span>
        <?php if (!empty($currentSearch)): ?>
          <span class="ts-badge ts-badge-cyan" style="font-size: 0.75rem; gap: 0.35rem;">
            Search: "<?= htmlspecialchars($currentSearch) ?>"
            <a href="javascript:void(0)" onclick="clearSearchFilter()" style="color: inherit; text-decoration: none;">&times;</a>
          </span>
        <?php endif; ?>
        <?php if (!empty($currentCategory)): ?>
          <span class="ts-badge ts-badge-pink" style="font-size: 0.75rem; gap: 0.35rem;">
            Category: <?= htmlspecialchars($currentCategory) ?>
            <a href="javascript:void(0)" onclick="clearCategoryFilter()" style="color: inherit; text-decoration: none;">&times;</a>
          </span>
        <?php endif; ?>
        <?php if (!empty($currentPricing)): ?>
          <span class="ts-badge" style="background: rgba(16,185,129,0.15); color: #10b981; font-size: 0.75rem; gap: 0.35rem;">
            Pricing: <?= ucfirst(htmlspecialchars($currentPricing)) ?>
            <a href="javascript:void(0)" onclick="clearPricingFilter()" style="color: inherit; text-decoration: none;">&times;</a>
          </span>
        <?php endif; ?>
        <?php if (!empty($currentSort) && $currentSort !== 'popular'): ?>
          <span class="ts-badge ts-badge-green" style="font-size: 0.75rem;">
            Sorted: <?= ucfirst(htmlspecialchars($currentSort)) ?>
          </span>
        <?php endif; ?>
        <a href="<?= url('/tools') ?>" style="font-size: 0.75rem; color: #ef4444; margin-left: 0.5rem; text-decoration: underline; cursor: pointer;">
          Reset All
        </a>
      </div>
    <?php endif; ?>
  </div>

  <!-- Tools Grid -->
  <div class="ts-grid-3">
    <?php if (!empty($tools)): ?>
      <?php foreach ($tools as $tool): ?>
        <div class="ts-card" style="display: flex; flex-direction: column;">
          <?php if (!empty($tool['featured_image_url'])): ?>
            <img src="<?= htmlspecialchars(image_url($tool['featured_image_url'])) ?>" alt="<?= htmlspecialchars($tool['title']) ?>" class="article-card-img" style="height: 160px; object-fit: cover; border-radius: var(--ts-radius-md); margin-bottom: 0.75rem;">
          <?php endif; ?>
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <span class="ts-badge ts-badge-pink"><?= htmlspecialchars($tool['pricing_model'] ?? 'Freemium') ?></span>
            <span style="color: #f59e0b; font-weight: 700; font-size: 0.9rem;">⭐ <?= number_format((float)($tool['tool_rating'] ?? 5.0), 1) ?></span>
          </div>
          <h3 style="font-size: 1.2rem; margin-bottom: 0.5rem;">
            <a href="<?= url('/tools/' . $tool['slug']) ?>"><?= htmlspecialchars($tool['title']) ?></a>
          </h3>
          <p style="font-size: 0.875rem; color: var(--ts-text-secondary); flex: 1; margin-bottom: 1.25rem;">
            <?= htmlspecialchars($tool['summary']) ?>
          </p>
          <div style="display: flex; gap: 0.5rem;">
            <a href="<?= url('/tools/' . $tool['slug']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="flex: 1;">Review & Specs</a>
            <a href="<?= url('/go/' . $tool['slug']) ?>" target="_blank" class="ts-btn ts-btn-accent ts-btn-sm">
              Visit Tool ↗
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="ts-card" style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
        <i data-lucide="cpu" style="width: 48px; height: 48px; color: var(--ts-primary); margin-bottom: 1rem;"></i>
        <h3>No AI Tools in this Category Yet</h3>
        <p style="color: var(--ts-text-secondary); margin-top: 0.5rem;">Check back soon as our radar is continuously scouting.</p>
        <a href="<?= url('/tools') ?>" class="ts-btn ts-btn-primary" style="margin-top: 1rem;">View All Tools</a>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
function applyToolFilters() {
  const search = document.getElementById('toolSearchInput').value.trim();
  const cat = document.getElementById('toolCategorySelect').value;
  const pricing = document.getElementById('toolPricingSelect').value;
  const sort = document.getElementById('toolSortSelect').value;

  const params = new URLSearchParams();
  if (sort && sort !== 'popular') params.set('sort', sort);
  if (cat) params.set('category', cat);
  if (pricing) params.set('pricing', pricing);
  if (search) params.set('search', search);

  const query = params.toString();
  window.location.href = '<?= url('/tools') ?>' + (query ? '?' + query : '');
}

function clearSearchFilter() {
  document.getElementById('toolSearchInput').value = '';
  applyToolFilters();
}

function clearCategoryFilter() {
  document.getElementById('toolCategorySelect').value = '';
  applyToolFilters();
}

function clearPricingFilter() {
  document.getElementById('toolPricingSelect').value = '';
  applyToolFilters();
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
