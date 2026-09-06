<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 2.5rem; margin-bottom: 5rem;">
  <div style="text-align: center; margin-bottom: 3rem;">
    <span class="ts-badge ts-badge-pink" style="margin-bottom: 0.75rem;">Hackathons & Sponsoring</span>
    <h1 style="font-size: 2.75rem; margin-bottom: 0.5rem;">Developer Challenges & Campaigns</h1>
    <p style="color: var(--ts-text-secondary); max-width: 640px; margin: 0 auto;">
      Participate in global engineering hackathons, build open-source agents, and win prize pools and API credits.
    </p>
  </div>

  <!-- Interactive Hackathon & Campaign Command HUD -->
  <div class="ts-card" style="margin-bottom: 2.25rem; padding: 1.25rem 1.5rem; background: rgba(13,21,39,0.75); border: 1px solid rgba(244,63,94,0.25); border-radius: 14px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
      
      <!-- Search Input -->
      <div style="flex: 1; min-width: 240px; position: relative;">
        <i data-lucide="search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: #64748b;"></i>
        <input type="text" id="campaignSearchInput" value="<?= htmlspecialchars($currentSearch ?? '') ?>" placeholder="Search hackathons, sponsor bounties, prize pools..." 
               style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 8px; padding: 0.6rem 2.2rem 0.6rem 2.2rem; color: #f8fafc; font-size: 0.88rem;"
               onkeydown="if(event.key==='Enter') applyCampaignFilters()">
        <?php if (!empty($currentSearch)): ?>
          <button onclick="document.getElementById('campaignSearchInput').value=''; applyCampaignFilters();" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 1rem;">&times;</button>
        <?php endif; ?>
      </div>

      <!-- Controls: Category, Status & Sort -->
      <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
        <!-- Category Dropdown -->
        <div>
          <select id="campaignCategorySelect" onchange="applyCampaignFilters()" style="background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 8px; padding: 0.6rem 0.85rem; color: #f8fafc; font-size: 0.88rem; cursor: pointer;">
            <option value="">All Challenge Domains</option>
            <?php foreach (($categories ?? []) as $c): ?>
              <option value="<?= htmlspecialchars($c['slug']) ?>" <?= ($currentCategory ?? '') === $c['slug'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Challenge Status Filter -->
        <div>
          <select id="campaignStatusSelect" onchange="applyCampaignFilters()" style="background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 8px; padding: 0.6rem 0.85rem; color: #f8fafc; font-size: 0.88rem; cursor: pointer;">
            <option value="all" <?= ($currentStatus ?? 'all') === 'all' ? 'selected' : '' ?>>All Statuses</option>
            <option value="active" <?= ($currentStatus ?? '') === 'active' ? 'selected' : '' ?>>Active Challenges 🔥</option>
            <option value="completed" <?= ($currentStatus ?? '') === 'completed' ? 'selected' : '' ?>>Archived / Concluded</option>
          </select>
        </div>

        <!-- Sort Dropdown -->
        <div style="display: flex; align-items: center; gap: 0.4rem;">
          <span style="font-size: 0.8rem; color: #64748b; text-transform: uppercase; font-weight: 700;">Sort:</span>
          <select id="campaignSortSelect" onchange="applyCampaignFilters()" style="background: #05070b; border: 1px solid rgba(244,63,94,0.35); border-radius: 8px; padding: 0.6rem 0.85rem; color: #f43f5e; font-weight: 600; font-size: 0.88rem; cursor: pointer;">
            <option value="latest" <?= ($currentSort ?? 'latest') === 'latest' ? 'selected' : '' ?>>Recently Launched</option>
            <option value="popular" <?= ($currentSort ?? '') === 'popular' ? 'selected' : '' ?>>Most Submissions ⚡</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Active Filter Badges -->
    <?php 
      $hasActiveFilters = !empty($currentCategory) || !empty($currentSearch) || (($currentStatus ?? 'all') !== 'all') || ($currentSort ?? 'latest') !== 'latest';
    ?>
    <?php if ($hasActiveFilters): ?>
      <div style="margin-top: 1rem; padding-top: 0.85rem; border-top: 1px solid rgba(148,163,184,0.1); display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
        <span style="font-size: 0.8rem; color: #94a3b8;">Active Filters:</span>
        
        <?php if (!empty($currentSearch)): ?>
          <span class="ts-badge ts-badge-cyan" style="font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.35rem;">
            Keyword: "<?= htmlspecialchars($currentSearch) ?>"
            <a href="javascript:void(0)" onclick="document.getElementById('campaignSearchInput').value=''; applyCampaignFilters();" style="color: inherit; text-decoration: none; font-weight: bold;">✕</a>
          </span>
        <?php endif; ?>

        <?php if (!empty($currentCategory)): ?>
          <span class="ts-badge ts-badge-indigo" style="font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.35rem;">
            Domain: <?= htmlspecialchars($currentCategory) ?>
            <a href="javascript:void(0)" onclick="document.getElementById('campaignCategorySelect').value=''; applyCampaignFilters();" style="color: inherit; text-decoration: none; font-weight: bold;">✕</a>
          </span>
        <?php endif; ?>

        <?php if (($currentStatus ?? 'all') !== 'all'): ?>
          <span class="ts-badge ts-badge-emerald" style="font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.35rem;">
            Status: <?= ucfirst(htmlspecialchars($currentStatus)) ?>
            <a href="javascript:void(0)" onclick="document.getElementById('campaignStatusSelect').value='all'; applyCampaignFilters();" style="color: inherit; text-decoration: none; font-weight: bold;">✕</a>
          </span>
        <?php endif; ?>

        <?php if (($currentSort ?? 'latest') !== 'latest'): ?>
          <span class="ts-badge ts-badge-pink" style="font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.35rem;">
            Sort: <?= ucfirst(htmlspecialchars($currentSort)) ?>
            <a href="javascript:void(0)" onclick="document.getElementById('campaignSortSelect').value='latest'; applyCampaignFilters();" style="color: inherit; text-decoration: none; font-weight: bold;">✕</a>
          </span>
        <?php endif; ?>

        <a href="<?= url('/campaigns') ?>" style="font-size: 0.78rem; color: #ef4444; margin-left: 0.5rem; text-decoration: none;">Reset All</a>
      </div>
    <?php endif; ?>
  </div>

  <div class="ts-grid-3">
    <?php if (!empty($campaigns)): ?>
      <?php foreach ($campaigns as $camp): ?>
        <div class="ts-card" style="display: flex; flex-direction: column;">
          <?php if (!empty($camp['featured_image_url'])): ?>
            <img src="<?= htmlspecialchars(image_url($camp['featured_image_url'])) ?>" alt="<?= htmlspecialchars($camp['title']) ?>" class="article-card-img" style="height: 180px;">
          <?php endif; ?>
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
            <span class="ts-badge <?= ($camp['status'] ?? 'published') === 'published' ? 'ts-badge-pink' : 'ts-badge-gray' ?>">
              <?= !empty($camp['start_date']) && !empty($camp['end_date']) && strtotime($camp['end_date']) < time() ? 'Concluded' : 'Active Challenge' ?>
            </span>
            <span style="font-size: 0.85rem; font-weight: 700; color: #34d399;"><?= htmlspecialchars($camp['prize_pool'] ?? '$15,000') ?></span>
          </div>
          <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem;">
            <a href="<?= url('/campaigns/' . $camp['slug']) ?>"><?= htmlspecialchars($camp['title']) ?></a>
          </h3>
          <p style="font-size: 0.9rem; color: var(--ts-text-secondary); flex: 1; margin-bottom: 1.25rem; line-height: 1.5;">
            <?= htmlspecialchars($camp['summary']) ?>
          </p>
          <a href="<?= url('/campaigns/' . $camp['slug']) ?>" class="ts-btn ts-btn-primary" style="width: 100%;">
            View Challenge & Rules 🚀
          </a>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="ts-card" style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
        <i data-lucide="trophy" style="width: 48px; height: 48px; color: var(--ts-primary); margin-bottom: 1rem;"></i>
        <h3>No Campaigns Found</h3>
        <p style="color: var(--ts-text-secondary); margin-top: 0.5rem;">Try clearing your search query or selecting all challenge statuses.</p>
        <a href="<?= url('/campaigns') ?>" class="ts-btn ts-btn-primary" style="margin-top: 1rem;">Clear Filters</a>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
function applyCampaignFilters() {
  const search = document.getElementById('campaignSearchInput')?.value.trim() || '';
  const category = document.getElementById('campaignCategorySelect')?.value || '';
  const status = document.getElementById('campaignStatusSelect')?.value || 'all';
  const sort = document.getElementById('campaignSortSelect')?.value || 'latest';

  const params = new URLSearchParams();
  if (search) params.set('search', search);
  if (category) params.set('category', category);
  if (status && status !== 'all') params.set('status', status);
  if (sort && sort !== 'latest') params.set('sort', sort);

  const queryStr = params.toString();
  window.location.href = '<?= url('/campaigns') ?>' + (queryStr ? '?' + queryStr : '');
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
