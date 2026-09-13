<?php require __DIR__ . '/../../layouts/admin_header.php'; ?>

<?php
$currentScope = $currentScope ?? 'all';
$scopeCounts = [
    'all' => count($categories),
    'article' => 0,
    'ai_tool' => 0,
    'gpt_listing' => 0,
    'prompt' => 0,
    'campaign' => 0
];
$rootCount = 0;
$maxDepth = 0;
$totalAssignedItems = 0;

foreach ($categories as $cat) {
    $scope = $cat['applicable_to'] ?? 'all';
    if (isset($scopeCounts[$scope])) {
        $scopeCounts[$scope]++;
    }
    if (empty($cat['parent_id']) || (int)$cat['depth'] === 0) {
        $rootCount++;
    }
    if ((int)($cat['depth'] ?? 0) > $maxDepth) {
        $maxDepth = (int)$cat['depth'];
    }
    $totalAssignedItems += (int)($cat['total_items'] ?? 0);
}
?>

<div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <div style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 0.35rem;">
      <h1 style="font-size: 1.75rem; color: var(--admin-text-primary); margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        Taxonomies, Categories &amp; Unlimited Subcategories
      </h1>
      <span class="admin-pill-badge badge-cyan" style="font-size: 0.72rem;">Hierarchy Engine Active</span>
    </div>
    <p style="color: var(--admin-text-secondary); font-size: 0.9rem; margin: 0;">
      Organize recursive multi-level taxonomies across Prompts, AI Tools, Custom GPTs, and Technical Articles with unlimited nesting.
    </p>
  </div>

  <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
    <button onclick="openNewCategoryModal(null, 'all')" class="admin-btn admin-btn-primary" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.65rem 1.25rem; font-weight: 700;">
      <i data-lucide="plus-circle" style="width: 16px; height: 16px;"></i>
      <span>Create Root Category</span>
    </button>
  </div>
</div>

<!-- Telemetry & Taxonomy Metrics HUD -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.75rem;">
  <div class="admin-card" style="padding: 1.15rem; border-radius: 12px; background: var(--admin-bg-surface); border: 1px solid var(--admin-border-main);">
    <div style="font-size: 0.75rem; color: var(--admin-text-muted); text-transform: uppercase; font-weight: 700;">Total Taxonomies</div>
    <div style="font-size: 1.5rem; font-weight: 800; color: var(--admin-text-primary); margin-top: 0.25rem; font-family: var(--admin-font-mono);">
      <?= number_format(count($categories)) ?>
    </div>
    <div style="font-size: 0.75rem; color: #38bdf8; margin-top: 0.2rem;">All scopes combined</div>
  </div>

  <div class="admin-card" style="padding: 1.15rem; border-radius: 12px; background: var(--admin-bg-surface); border: 1px solid var(--admin-border-main);">
    <div style="font-size: 0.75rem; color: var(--admin-text-muted); text-transform: uppercase; font-weight: 700;">Root Categories</div>
    <div style="font-size: 1.5rem; font-weight: 800; color: #10b981; margin-top: 0.25rem; font-family: var(--admin-font-mono);">
      <?= number_format($rootCount) ?>
    </div>
    <div style="font-size: 0.75rem; color: var(--admin-text-secondary); margin-top: 0.2rem;">Top-level parent pillars</div>
  </div>

  <div class="admin-card" style="padding: 1.15rem; border-radius: 12px; background: var(--admin-bg-surface); border: 1px solid var(--admin-border-main);">
    <div style="font-size: 0.75rem; color: var(--admin-text-muted); text-transform: uppercase; font-weight: 700;">Max Hierarchy Depth</div>
    <div style="font-size: 1.5rem; font-weight: 800; color: #ec4899; margin-top: 0.25rem; font-family: var(--admin-font-mono);">
      Level <?= $maxDepth ?>
    </div>
    <div style="font-size: 0.75rem; color: var(--admin-text-secondary); margin-top: 0.2rem;">Unlimited recursive nesting</div>
  </div>

  <div class="admin-card" style="padding: 1.15rem; border-radius: 12px; background: var(--admin-bg-surface); border: 1px solid var(--admin-border-main);">
    <div style="font-size: 0.75rem; color: var(--admin-text-muted); text-transform: uppercase; font-weight: 700;">Associated Entities</div>
    <div style="font-size: 1.5rem; font-weight: 800; color: #f59e0b; margin-top: 0.25rem; font-family: var(--admin-font-mono);">
      <?= number_format($totalAssignedItems) ?>
    </div>
    <div style="font-size: 0.75rem; color: var(--admin-text-secondary); margin-top: 0.2rem;">Active blueprints &amp; content</div>
  </div>
</div>

<!-- Scope Sub-Tabs -->
<div style="display: flex; gap: 0.5rem; border-bottom: 1px solid var(--admin-border-main); margin-bottom: 1.5rem; overflow-x: auto; padding-bottom: 0.25rem;">
  <a href="<?= url('/admin/categories?scope=all') ?>" class="ts-subtab-link <?= $currentScope === 'all' ? 'active' : '' ?>">
    🌐 All Taxonomies (<?= $scopeCounts['all'] ?>)
  </a>
  <a href="<?= url('/admin/categories?scope=prompt') ?>" class="ts-subtab-link <?= $currentScope === 'prompt' ? 'active' : '' ?>">
    ⚡ Prompts &amp; Visual Blueprints (<?= $scopeCounts['prompt'] ?>)
  </a>
  <a href="<?= url('/admin/categories?scope=ai_tool') ?>" class="ts-subtab-link <?= $currentScope === 'ai_tool' ? 'active' : '' ?>">
    🛠️ AI Tools Radar (<?= $scopeCounts['ai_tool'] ?>)
  </a>
  <a href="<?= url('/admin/categories?scope=gpt_listing') ?>" class="ts-subtab-link <?= $currentScope === 'gpt_listing' ? 'active' : '' ?>">
    🤖 Custom GPTs &amp; Agents (<?= $scopeCounts['gpt_listing'] ?>)
  </a>
  <a href="<?= url('/admin/categories?scope=article') ?>" class="ts-subtab-link <?= $currentScope === 'article' ? 'active' : '' ?>">
    📝 Technical Articles (<?= $scopeCounts['article'] ?>)
  </a>
  <a href="<?= url('/admin/categories?scope=campaign') ?>" class="ts-subtab-link <?= $currentScope === 'campaign' ? 'active' : '' ?>">
    🏆 Challenges &amp; Hackathons (<?= $scopeCounts['campaign'] ?>)
  </a>
</div>

<!-- Real-time Filter Toolbar -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
  <div style="display: flex; gap: 0.75rem; flex: 1; min-width: 280px; align-items: center;">
    <div style="position: relative; flex: 1; max-width: 380px;">
      <input type="text" id="categorySearchInput" oninput="filterCategoryRows()" placeholder="Search categories by name, slug, or parent..." class="admin-input" style="padding-left: 2rem;">
      <i data-lucide="search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: var(--admin-text-muted);"></i>
    </div>

    <select id="filterHierarchyLevel" onchange="filterCategoryRows()" class="admin-input" style="width: auto; font-size: 0.82rem;">
      <option value="all">All Depth Levels</option>
      <option value="roots_only">Root Categories Only (Level 0)</option>
      <option value="subcategories_only">Subcategories Only (Level 1+)</option>
    </select>
  </div>

  <div style="font-size: 0.82rem; color: var(--admin-text-muted);" id="categoryFilterCountText">
    Showing all <?= count($categories) ?> categories
  </div>
</div>

<!-- Category Hierarchy Table Wrap -->
<div class="admin-table-wrap">
  <table class="admin-table" id="categoriesAdminTable">
    <thead>
      <tr>
        <th style="min-width: 280px;">Category &amp; Hierarchical Lineage</th>
        <th>Depth &amp; Scope</th>
        <th>Clean SEO Gateway</th>
        <th>Icon &amp; Visual</th>
        <th>Homepage</th>
        <th>Entities</th>
        <th style="text-align: right; min-width: 170px;">Taxonomy Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($categories)): ?>
        <?php foreach ($categories as $cat): ?>
          <?php
            $depth = (int)($cat['depth'] ?? 0);
            $scope = $cat['applicable_to'] ?? 'all';
            $matchesScope = ($currentScope === 'all' || $scope === $currentScope || $scope === 'all');
            $rowStyle = $matchesScope ? '' : 'display: none;';
          ?>
          <tr class="category-row-item" 
              data-cat-id="<?= $cat['id'] ?>" 
              data-name="<?= htmlspecialchars(strtolower($cat['name'])) ?>" 
              data-slug="<?= htmlspecialchars(strtolower($cat['slug'])) ?>" 
              data-scope="<?= htmlspecialchars($scope) ?>"
              data-depth="<?= $depth ?>"
              style="<?= $rowStyle ?>">
            <td>
              <div style="display: flex; align-items: center; gap: 0.4rem; padding-left: <?= ($depth * 1.5) ?>rem;">
                <?php if ($depth > 0): ?>
                  <span style="color: #6366f1; font-weight: 800; font-size: 0.95rem; user-select: none;">
                    <?= str_repeat('↳', min($depth, 4)) ?>
                  </span>
                <?php endif; ?>
                
                <div>
                  <div style="font-weight: <?= $depth === 0 ? '700' : '600' ?>; color: var(--admin-text-primary); font-size: <?= $depth === 0 ? '0.98rem' : '0.9rem' ?>; display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
                    <span><?= htmlspecialchars($cat['name']) ?></span>
                    <?php if (!empty($cat['has_children'])): ?>
                      <span class="admin-pill-badge badge-cyan" style="font-size: 0.65rem; padding: 0.1rem 0.35rem;">
                        <?= (int)$cat['children_count'] ?> Subcategories
                      </span>
                    <?php endif; ?>
                  </div>

                  <?php if (!empty($cat['breadcrumb_text']) && $depth > 0): ?>
                    <div style="font-size: 0.72rem; color: #818cf8; margin-top: 0.15rem; font-family: var(--admin-font-mono);">
                      <?= htmlspecialchars($cat['breadcrumb_text']) ?>
                    </div>
                  <?php endif; ?>

                  <?php if (!empty($cat['description'])): ?>
                    <div style="font-size: 0.75rem; color: var(--admin-text-muted); margin-top: 0.15rem; max-width: 360px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                      <?= htmlspecialchars($cat['description']) ?>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </td>

            <td>
              <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                <div>
                  <?php if ($depth === 0): ?>
                    <span class="admin-pill-badge badge-success" style="font-size: 0.65rem;">ROOT LEVEL 0</span>
                  <?php elseif ($depth === 1): ?>
                    <span class="admin-pill-badge badge-cyan" style="font-size: 0.65rem;">LEVEL 1 SUB</span>
                  <?php elseif ($depth === 2): ?>
                    <span class="admin-pill-badge badge-warning" style="font-size: 0.65rem;">LEVEL 2 SUB</span>
                  <?php else: ?>
                    <span class="admin-pill-badge badge-purple" style="font-size: 0.65rem;">LEVEL <?= $depth ?> DEEP</span>
                  <?php endif; ?>
                </div>

                <div>
                  <?php
                    $badgeClass = 'badge-secondary';
                    if ($scope === 'prompt') $badgeClass = 'badge-cyan';
                    elseif ($scope === 'ai_tool') $badgeClass = 'badge-success';
                    elseif ($scope === 'gpt_listing') $badgeClass = 'badge-pink';
                    elseif ($scope === 'article') $badgeClass = 'badge-warning';
                    elseif ($scope === 'all') $badgeClass = 'badge-indigo';
                  ?>
                  <span class="admin-pill-badge <?= $badgeClass ?>" style="font-size: 0.65rem;">
                    <?= strtoupper(htmlspecialchars($scope)) ?>
                  </span>
                </div>
              </div>
            </td>

            <td>
              <?php 
                $entityRoute = 'category';
                if ($scope === 'gpt_listing') $entityRoute = 'gpts/category';
                elseif ($scope === 'ai_tool') $entityRoute = 'tools/category';
                elseif ($scope === 'prompt') $entityRoute = 'prompts?category=' . urlencode($cat['slug']);
                elseif ($scope === 'article') $entityRoute = 'blog/category';
                $fullRouteUrl = url('/' . $entityRoute . ($scope !== 'prompt' ? '/' . $cat['slug'] : ''));
              ?>
              <a href="<?= $fullRouteUrl ?>" target="_blank" style="color: #38bdf8; font-size: 0.8rem; font-family: var(--admin-font-mono); text-decoration: none;">
                <?= htmlspecialchars(substr($cat['slug'], 0, 24)) ?> ↗
              </a>
            </td>

            <td>
              <div style="display: flex; align-items: center; gap: 0.45rem;">
                <span style="display: inline-block; width: 14px; height: 14px; border-radius: 50%; background: <?= htmlspecialchars($cat['color_hex'] ?: '#6366f1') ?>; border: 1px solid rgba(255,255,255,0.2);"></span>
                <span style="font-size: 0.78rem; font-family: var(--admin-font-mono); color: var(--admin-text-secondary); display: inline-flex; align-items: center; gap: 0.25rem;">
                  <i data-lucide="<?= htmlspecialchars($cat['icon_name'] ?: 'folder') ?>" style="width: 12px; height: 12px;"></i>
                  <span><?= htmlspecialchars($cat['icon_name'] ?: 'folder') ?></span>
                </span>
              </div>
            </td>

            <td>
              <?php if (!empty($cat['is_featured_home'])): ?>
                <span class="admin-pill-badge badge-warning" style="font-size: 0.65rem;">Featured ⭐</span>
              <?php else: ?>
                <span style="color: var(--admin-text-muted); font-size: 0.75rem;">Standard</span>
              <?php endif; ?>
            </td>

            <td>
              <strong style="color: #38bdf8; font-family: var(--admin-font-mono); font-size: 0.9rem;">
                <?= number_format((int)($cat['total_items'] ?? $cat['content_count'] ?? 0)) ?>
              </strong>
            </td>

            <td style="text-align: right;">
              <div style="display: inline-flex; gap: 0.35rem; align-items: center;">
                <!-- 1000x Quick Add Subcategory Hook -->
                <button type="button" 
                        onclick="openNewCategoryModal(<?= (int)$cat['id'] ?>, '<?= htmlspecialchars(addslashes($scope)) ?>')" 
                        class="admin-btn admin-btn-outline admin-btn-sm" 
                        style="color: #10b981; border-color: rgba(16,185,129,0.35); font-size: 0.72rem; padding: 0.25rem 0.5rem;" 
                        title="Add child subcategory directly under <?= htmlspecialchars($cat['name']) ?>">
                  <i data-lucide="corner-down-right" style="width: 11px; height: 11px;"></i>
                  <span>+ Sub</span>
                </button>

                <button type="button" 
                        onclick='editCat(<?= json_encode($cat, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' 
                        class="admin-btn admin-btn-outline admin-btn-sm" 
                        style="font-size: 0.72rem; padding: 0.25rem 0.5rem;">
                  Edit
                </button>

                <form action="<?= url('/admin/categories/delete/' . $cat['id']) ?>" method="POST" onsubmit="return confirm('Delete category \'<?= htmlspecialchars(addslashes($cat['name'])) ?>\'? Any child items will remain in the database.');" style="margin: 0;">
                  <button type="submit" class="admin-btn admin-btn-outline admin-btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.3); font-size: 0.72rem; padding: 0.25rem 0.5rem;">
                    Delete
                  </button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="7" style="text-align: center; padding: 3rem; color: var(--admin-text-secondary);">
            No categories defined yet. Create your first top-level root category above!
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Modal: Create & Edit Category & Subcategory -->
<div id="catFormModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-card" style="width: 100%; max-width: 740px; max-height: 92vh; overflow-y: auto; padding: 2rem; background: var(--admin-bg-surface); border: 1px solid var(--admin-border-main); border-radius: 16px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-main); padding-bottom: 1rem;">
      <div style="display: flex; align-items: center; gap: 0.5rem;">
        <i data-lucide="layers" style="width: 20px; height: 20px; color: var(--admin-accent-primary);"></i>
        <h2 id="modalCatTitle" style="font-size: 1.25rem; color: var(--admin-text-primary); margin: 0; font-family: 'Outfit', sans-serif;">
          Create Taxonomy Specification
        </h2>
      </div>
      <button type="button" onclick="closeCategoryModal()" style="background: none; border: none; color: var(--admin-text-muted); font-size: 1.5rem; cursor: pointer; line-height: 1;">&times;</button>
    </div>

    <form id="adminCatForm" action="<?= url('/admin/categories/save') ?>" method="POST">
      <input type="hidden" name="id" id="cat_id" value="">

      <!-- Row 1: Name & Live Slug Generator -->
      <div style="display: grid; grid-template-columns: 3fr 2fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
            Category Name <span style="color: #ef4444;">*</span>
          </label>
          <input type="text" name="name" id="cat_name" oninput="autoGenerateCategorySlug(this.value)" placeholder="e.g. Midjourney Styles or Autonomous Code" class="admin-input" required>
        </div>

        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
            SEO Slug Path
          </label>
          <input type="text" name="slug" id="cat_slug" placeholder="midjourney-styles" class="admin-input" style="font-family: var(--admin-font-mono);">
        </div>
      </div>

      <!-- Row 2: Parent Category (Unlimited Nesting) & Applicable Scope -->
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
            Parent Category (Nesting Tree)
          </label>
          <select name="parent_id" id="cat_parent_id" class="admin-input">
            <option value="">None (Top-Level Root Taxonomy)</option>
            <?php foreach ($categories as $pCat): ?>
              <?php
                $pDepth = (int)($pCat['depth'] ?? 0);
                $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $pDepth);
                $prefix = $pDepth > 0 ? str_repeat('↳', min($pDepth, 4)) . ' ' : '';
              ?>
              <option value="<?= $pCat['id'] ?>" data-scope="<?= htmlspecialchars($pCat['applicable_to'] ?? 'all') ?>">
                <?= $indent ?><?= $prefix ?><?= htmlspecialchars($pCat['name']) ?> (<?= strtoupper(htmlspecialchars($pCat['applicable_to'] ?? 'all')) ?>)
              </option>
            <?php endforeach; ?>
          </select>
          <div style="font-size: 0.72rem; color: var(--admin-text-muted); margin-top: 0.25rem;">
            Select any existing category to nest arbitrarily deep (sub-categories of sub-categories).
          </div>
        </div>

        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
            Applicable Entity Scope <span style="color: #ef4444;">*</span>
          </label>
          <select name="applicable_to" id="cat_applicable_to" class="admin-input" required>
            <option value="all">🌐 Global (Applicable to All Modules)</option>
            <option value="prompt">⚡ Prompt Blueprints &amp; Images Only</option>
            <option value="ai_tool">🛠️ AI Tools Radar Only</option>
            <option value="gpt_listing">🤖 Custom GPTs &amp; Agents Only</option>
            <option value="article">📝 Technical Articles Only</option>
            <option value="campaign">🏆 Challenges &amp; Hackathons Only</option>
          </select>
          <div style="font-size: 0.72rem; color: var(--admin-text-muted); margin-top: 0.25rem;">
            Defines which directory hub and editor filters this category appears under.
          </div>
        </div>
      </div>

      <!-- Row 3: Topic Domain & Sort Order -->
      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
            Parent Topic Domain
          </label>
          <select name="topic_id" id="cat_topic_id" class="admin-input">
            <option value="">None / Unassigned Topic</option>
            <?php foreach ($topics ?? [] as $top): ?>
              <option value="<?= $top['id'] ?>"><?= htmlspecialchars($top['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
            Display Sort Order
          </label>
          <input type="number" name="sort_order" id="cat_sort_order" value="0" class="admin-input">
        </div>
      </div>

      <!-- Row 4: Icon & Brand Color with Fast Pickers -->
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
            Lucide Icon Identifier
          </label>
          <div style="display: flex; gap: 0.5rem; align-items: center;">
            <input type="text" name="icon_name" id="cat_icon" value="folder" class="admin-input" style="font-family: var(--admin-font-mono);">
            <div style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
              <?php foreach (['folder', 'terminal', 'image', 'cpu', 'bot', 'sparkles', 'code', 'database'] as $ic): ?>
                <button type="button" onclick="document.getElementById('cat_icon').value='<?= $ic ?>'" class="admin-btn admin-btn-outline admin-btn-sm" style="padding: 0.2rem 0.4rem; font-size: 0.68rem;" title="<?= $ic ?>">
                  <i data-lucide="<?= $ic ?>" style="width: 11px; height: 11px;"></i>
                </button>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
            Accent Palette Color
          </label>
          <div style="display: flex; gap: 0.5rem; align-items: center;">
            <input type="color" name="color_hex" id="cat_color" value="#6366f1" style="height: 38px; width: 44px; padding: 2px; border-radius: 6px; border: 1px solid var(--admin-border-main); background: var(--admin-bg-surface-elevated); cursor: pointer;">
            <div style="display: flex; gap: 0.35rem; align-items: center;">
              <?php foreach (['#6366f1', '#38bdf8', '#ec4899', '#10b981', '#f59e0b', '#8b5cf6'] as $hex): ?>
                <span onclick="document.getElementById('cat_color').value='<?= $hex ?>'" style="display: inline-block; width: 18px; height: 18px; border-radius: 50%; background: <?= $hex ?>; cursor: pointer; border: 1px solid rgba(255,255,255,0.2);"></span>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Row 5: Description -->
      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
          Description / Scope Summary
        </label>
        <textarea name="description" id="cat_desc" rows="2" placeholder="Brief 1-2 sentence description explaining this taxonomy..." class="admin-input"></textarea>
      </div>

      <!-- Row 6: Homepage Featured Toggle -->
      <div style="margin-bottom: 1.5rem; background: var(--admin-bg-surface-elevated); padding: 1rem; border-radius: 8px; border: 1px solid var(--admin-border-main);">
        <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
          <input type="checkbox" name="is_featured_home" id="cat_is_featured_home" value="1" style="width: 18px; height: 18px; accent-color: var(--admin-accent-primary);">
          <div>
            <strong style="color: var(--admin-text-primary); font-size: 0.9rem;">Feature on Homepage Curated Explorer Grid</strong>
            <div style="font-size: 0.75rem; color: var(--admin-text-muted);">
              Pins this category into the hero taxonomy badges and homepage exploration modules.
            </div>
          </div>
        </label>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
        <button type="button" onclick="closeCategoryModal()" class="admin-btn admin-btn-outline">Cancel</button>
        <button type="submit" class="admin-btn admin-btn-primary" style="padding: 0.65rem 1.5rem; font-weight: 700;">
          Save Category Specification 🚀
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function autoGenerateCategorySlug(name) {
    const slugInp = document.getElementById('cat_slug');
    if (!slugInp) return;
    // Auto-generate if empty or matches slugified old value
    const generated = name.toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-|-$/g, '');
    slugInp.value = generated;
}

function openNewCategoryModal(preselectedParentId = null, preselectedScope = 'all') {
    document.getElementById('cat_id').value = '';
    document.getElementById('cat_name').value = '';
    document.getElementById('cat_slug').value = '';
    document.getElementById('cat_topic_id').value = '';
    
    const parentSelect = document.getElementById('cat_parent_id');
    if (parentSelect) {
        parentSelect.value = preselectedParentId ? String(preselectedParentId) : '';
    }

    const scopeSelect = document.getElementById('cat_applicable_to');
    if (scopeSelect && preselectedScope && preselectedScope !== 'all') {
        scopeSelect.value = preselectedScope;
    } else if (scopeSelect) {
        scopeSelect.value = 'all';
    }

    document.getElementById('cat_is_featured_home').checked = false;
    document.getElementById('cat_icon').value = 'folder';
    document.getElementById('cat_color').value = '#6366f1';
    document.getElementById('cat_sort_order').value = '0';
    document.getElementById('cat_desc').value = '';

    const modalTitle = document.getElementById('modalCatTitle');
    if (preselectedParentId) {
        modalTitle.innerText = 'Create Subcategory Under #' + preselectedParentId;
    } else {
        modalTitle.innerText = 'Create Top-Level Root Taxonomy';
    }

    document.getElementById('catFormModal').style.display = 'flex';
}

function editCat(cat) {
    document.getElementById('cat_id').value = cat.id;
    document.getElementById('cat_name').value = cat.name || '';
    document.getElementById('cat_slug').value = cat.slug || '';
    document.getElementById('cat_topic_id').value = cat.topic_id || '';
    document.getElementById('cat_parent_id').value = cat.parent_id || '';
    document.getElementById('cat_applicable_to').value = cat.applicable_to || 'all';
    document.getElementById('cat_is_featured_home').checked = (parseInt(cat.is_featured_home) === 1);
    document.getElementById('cat_icon').value = cat.icon_name || 'folder';
    document.getElementById('cat_color').value = cat.color_hex || '#6366f1';
    document.getElementById('cat_sort_order').value = cat.sort_order || 0;
    document.getElementById('cat_desc').value = cat.description || '';

    document.getElementById('modalCatTitle').innerText = 'Edit Category #' + cat.id + ': ' + (cat.name || '');
    document.getElementById('catFormModal').style.display = 'flex';
}

function closeCategoryModal() {
    document.getElementById('catFormModal').style.display = 'none';
}

function filterCategoryRows() {
    const q = (document.getElementById('categorySearchInput').value || '').toLowerCase().trim();
    const hierarchyFilter = document.getElementById('filterHierarchyLevel').value;
    const rows = document.querySelectorAll('.category-row-item');

    let visibleCount = 0;
    rows.forEach(row => {
        const name = row.getAttribute('data-name') || '';
        const slug = row.getAttribute('data-slug') || '';
        const depth = parseInt(row.getAttribute('data-depth') || '0');

        let matchQuery = (!q || name.includes(q) || slug.includes(q));
        let matchHierarchy = true;
        if (hierarchyFilter === 'roots_only') {
            matchHierarchy = (depth === 0);
        } else if (hierarchyFilter === 'subcategories_only') {
            matchHierarchy = (depth > 0);
        }

        if (matchQuery && matchHierarchy) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    const countText = document.getElementById('categoryFilterCountText');
    if (countText) {
        countText.innerText = 'Showing ' + visibleCount + ' matching categories';
    }
}
</script>

<?php require __DIR__ . '/../../layouts/admin_footer.php'; ?>
