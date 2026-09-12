<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 2rem; margin-bottom: 4rem;">
  <!-- Header & Category Pills -->
  <div style="margin-bottom: 2.5rem; text-align: center;">
    <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">TechScout Editorial Hub</h1>
    <p style="color: var(--ts-text-secondary); max-width: 600px; margin: 0 auto 1.5rem auto;">
      Authoritative technical journalism, systems design blueprints, and production-tested tutorials.
    </p>

  </div>

  <!-- Interactive Filter & Sorting Command HUD -->
  <div class="ts-card ts-filter-hud-card" style="margin-bottom: 2rem; padding: 1.25rem 1.5rem; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main); border-radius: 14px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
      
      <!-- Search Input -->
      <div style="flex: 1; min-width: 240px; position: relative;">
        <i data-lucide="search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--ts-text-tertiary);"></i>
        <input type="text" id="blogSearchInput" value="<?= htmlspecialchars($currentSearch ?? '') ?>" placeholder="Search technical articles, kubernetes, LLMs, cloud..." 
               style="width: 100%; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 8px; padding: 0.6rem 2.2rem 0.6rem 2.2rem; color: var(--ts-text-primary); font-size: 0.88rem;"
               onkeydown="if(event.key==='Enter') applyBlogFilters()">
        <?php if (!empty($currentSearch)): ?>
          <button onclick="document.getElementById('blogSearchInput').value=''; applyBlogFilters();" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--ts-text-secondary); cursor: pointer; font-size: 1rem;">&times;</button>
        <?php endif; ?>
      </div>

      <!-- Controls: Category, Reading Time & Sort -->
      <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
        <!-- Category Dropdown -->
        <div>
          <select id="blogCategorySelect" onchange="applyBlogFilters()" style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 8px; padding: 0.6rem 0.85rem; color: var(--ts-text-primary); font-size: 0.88rem; cursor: pointer;">
            <option value="">All Categories &amp; Domains</option>
            <?php foreach (($categories ?? []) as $c): ?>
              <option value="<?= htmlspecialchars($c['slug']) ?>" <?= ($currentCategory ?? '') === $c['slug'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Reading Time Filter -->
        <div>
          <select id="blogReadingTimeSelect" onchange="applyBlogFilters()" style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 8px; padding: 0.6rem 0.85rem; color: var(--ts-text-primary); font-size: 0.88rem; cursor: pointer;">
            <option value="">Any Reading Duration</option>
            <option value="short" <?= ($currentReadingTime ?? '') === 'short' ? 'selected' : '' ?>>⚡ Quick Read (&le; 5 min)</option>
            <option value="medium" <?= ($currentReadingTime ?? '') === 'medium' ? 'selected' : '' ?>>📖 Standard (5–10 min)</option>
            <option value="deep" <?= ($currentReadingTime ?? '') === 'deep' ? 'selected' : '' ?>>🔬 Deep Dive (&gt; 10 min)</option>
          </select>
        </div>

        <!-- Sort Dropdown -->
        <div style="display: flex; align-items: center; gap: 0.4rem;">
          <span style="font-size: 0.8rem; color: var(--ts-text-tertiary); text-transform: uppercase; font-weight: 700;">Sort:</span>
          <select id="blogSortSelect" onchange="applyBlogFilters()" style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 8px; padding: 0.6rem 0.85rem; color: var(--ts-text-accent); font-weight: 600; font-size: 0.88rem; cursor: pointer;">
            <option value="latest" <?= ($currentSort ?? 'latest') === 'latest' ? 'selected' : '' ?>>Latest Publications</option>
            <option value="popular" <?= ($currentSort ?? '') === 'popular' ? 'selected' : '' ?>>Most Popular (Views &amp; Upvotes)</option>
            <option value="trending" <?= ($currentSort ?? '') === 'trending' ? 'selected' : '' ?>>Trending Now 🔥</option>
            <option value="featured" <?= ($currentSort ?? '') === 'featured' ? 'selected' : '' ?>>Editor's Choice ⭐</option>
            <option value="reading_time" <?= ($currentSort ?? '') === 'reading_time' ? 'selected' : '' ?>>Fastest Reads</option>
            <?php if (!empty($hasSubscriptions)): ?>
              <option value="following" <?= ($currentSort ?? '') === 'following' ? 'selected' : '' ?>>Following &amp; For You</option>
            <?php endif; ?>
          </select>
        </div>
      </div>
    </div>

    <!-- Active Filter Badges -->
    <?php 
      $hasActiveFilters = !empty($currentCategory) || !empty($currentSearch) || !empty($currentReadingTime) || ($currentSort !== 'latest' && !empty($currentSort));
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
        <?php if (!empty($currentReadingTime)): ?>
          <span class="ts-badge" style="background: rgba(245,158,11,0.2); color: #f59e0b; font-size: 0.75rem; gap: 0.35rem;">
            Duration: <?= ucfirst(htmlspecialchars($currentReadingTime)) ?>
            <a href="javascript:void(0)" onclick="clearReadingTimeFilter()" style="color: inherit; text-decoration: none;">&times;</a>
          </span>
        <?php endif; ?>
        <?php if (!empty($currentSort) && $currentSort !== 'latest'): ?>
          <span class="ts-badge ts-badge-green" style="font-size: 0.75rem;">
            Sorted: <?= ucfirst(htmlspecialchars($currentSort)) ?>
          </span>
        <?php endif; ?>
        <a href="<?= url('/blog') ?>" style="font-size: 0.75rem; color: #ef4444; margin-left: 0.5rem; text-decoration: underline; cursor: pointer;">
          Reset All
        </a>
      </div>
    <?php endif; ?>
  </div>

  <!-- Articles Stream Grid -->
  <?php if (!empty($articles)): ?>
    <div class="ts-grid-3">
      <?php foreach ($articles as $art): ?>
        <article class="ts-card">
          <?php if (!empty($art['featured_image_url'])): ?>
            <img src="<?= htmlspecialchars(image_url($art['featured_image_url'])) ?>" alt="<?= htmlspecialchars($art['title']) ?>" class="article-card-img">
          <?php endif; ?>
          <div style="display: flex; gap: 0.45rem; margin-bottom: 0.75rem; flex-wrap: wrap; align-items: center;">
            <?php if (!empty($art['is_pinned'])): ?>
              <span class="ts-badge" style="background: rgba(245,158,11,0.15); color: #f59e0b; border: 1px solid rgba(245,158,11,0.4); font-weight: 700; display: inline-flex; align-items: center; gap: 0.25rem;">
                📌 PINNED
              </span>
            <?php endif; ?>
            <?php if (!empty($art['custom_badge'])): ?>
              <span class="ts-badge" style="background: <?= htmlspecialchars($art['badge_bg'] ?: 'rgba(56,189,248,0.15)') ?>; color: <?= htmlspecialchars($art['badge_color'] ?: '#38bdf8') ?>; border: 1px solid <?= htmlspecialchars($art['badge_color'] ?: '#38bdf8') ?>50; font-weight: 700;">
                ⭐ <?= htmlspecialchars($art['custom_badge']) ?>
              </span>
            <?php endif; ?>
            <span class="ts-badge"><?= htmlspecialchars($art['category_name'] ?? 'Editorial') ?></span>
            <span class="ts-badge ts-badge-cyan"><?= $art['reading_time_mins'] ?? 5 ?> min read</span>
          </div>
          <h3 class="article-card-title">
            <a href="<?= url('/article/' . $art['slug']) ?>"><?= htmlspecialchars($art['title']) ?></a>
          </h3>
          <p style="font-size: 0.9rem; color: var(--ts-text-secondary); line-height: 1.5;">
            <?= htmlspecialchars($art['summary']) ?>
          </p>
          <div class="article-card-meta">
            <?php if (!empty($art['author_avatar'])): ?>
              <img src="<?= htmlspecialchars(image_url($art['author_avatar'])) ?>" alt="" loading="lazy" onerror="this.style.display='none'">
            <?php endif; ?>
            <span class="meta-author" title="<?= htmlspecialchars($art['author_name'] ?? 'Staff Writer') ?>"><?= htmlspecialchars($art['author_name'] ?? 'Staff Writer') ?></span>
            <span class="meta-dot">&bull;</span>
            <span class="meta-stat"><?= date('M j, Y', strtotime($art['published_at'] ?? 'now')) ?></span>
            <span class="meta-dot">&bull;</span>
            <span class="meta-stat"><?= number_format((int)($art['view_count'] ?? 0)) ?> reads</span>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="ts-card" style="text-align: center; padding: 3rem;">
      <i data-lucide="book-open" style="width: 48px; height: 48px; color: var(--ts-primary); margin-bottom: 1rem;"></i>
      <h3>No articles published in this category yet</h3>
      <p style="color: var(--ts-text-secondary); margin-top: 0.5rem;">Check back soon or explore other technology categories.</p>
    </div>
  <?php endif; ?>
</div>

<script>
function applyBlogFilters() {
  const search = document.getElementById('blogSearchInput').value.trim();
  const cat = document.getElementById('blogCategorySelect').value;
  const reading = document.getElementById('blogReadingTimeSelect').value;
  const sort = document.getElementById('blogSortSelect').value;

  const params = new URLSearchParams();
  if (sort && sort !== 'latest') params.set('sort', sort);
  if (cat) params.set('category', cat);
  if (search) params.set('search', search);
  if (reading) params.set('reading_time', reading);

  const query = params.toString();
  window.location.href = '<?= url('/blog') ?>' + (query ? '?' + query : '');
}

function clearSearchFilter() {
  document.getElementById('blogSearchInput').value = '';
  applyBlogFilters();
}

function clearCategoryFilter() {
  document.getElementById('blogCategorySelect').value = '';
  applyBlogFilters();
}

function clearReadingTimeFilter() {
  document.getElementById('blogReadingTimeSelect').value = '';
  applyBlogFilters();
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
