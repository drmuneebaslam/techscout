<?php require __DIR__ . '/../../layouts/admin_header.php'; ?>

<?php
$currentScope = $currentScope ?? 'all';
$activeTab = $activeTab ?? 'categories';
$filterParent = $filterParent ?? 0;
$categories = $categories ?? [];
$subcategories = $subcategories ?? [];
$parentCategories = $parentCategories ?? [];

// Category metrics
if (!isset($catScopeCounts)) {
    $catScopeCounts = ['all' => count($categories), 'article' => 0, 'ai_tool' => 0, 'gpt_listing' => 0, 'prompt' => 0, 'image_prompt' => 0, 'campaign' => 0];
    foreach ($categories as $cat) {
        $sc = $cat['applicable_to'] ?? 'all';
        if (isset($catScopeCounts[$sc])) {
            $catScopeCounts[$sc]++;
        }
    }
}
$totalSubcategoriesCount = 0;
$totalCategoryItems = 0;
$featuredCategoryCount = 0;
foreach ($categories as $cat) {
    $totalSubcategoriesCount += (int)($cat['subcategories_count'] ?? 0);
    $totalCategoryItems += (int)($cat['total_items'] ?? 0);
    if (!empty($cat['is_featured_home'])) {
        $featuredCategoryCount++;
    }
}

// Subcategory metrics
if (!isset($subScopeCounts)) {
    $subScopeCounts = ['all' => count($subcategories), 'article' => 0, 'ai_tool' => 0, 'gpt_listing' => 0, 'prompt' => 0, 'image_prompt' => 0, 'campaign' => 0];
    foreach ($subcategories as $sub) {
        $sc = $sub['applicable_to'] ?? 'all';
        if (isset($subScopeCounts[$sc])) {
            $subScopeCounts[$sc]++;
        }
    }
}
$totalSubcategoryItems = 0;
$featuredSubcategoryCount = 0;
$parentsRepresented = [];
foreach ($subcategories as $sub) {
    $totalSubcategoryItems += (int)($sub['total_items'] ?? 0);
    if (!empty($sub['is_featured_home'])) {
        $featuredSubcategoryCount++;
    }
    if (!empty($sub['parent_id'])) {
        $parentsRepresented[$sub['parent_id']] = true;
    }
}
?>

<!-- Header Title & Action Buttons -->
<div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.65rem; margin-bottom: 0.35rem;">
      <h1 style="font-size: 1.75rem; color: var(--admin-text-primary); margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        Taxonomy Architecture
      </h1>
      <span class="admin-pill-badge badge-cyan" style="font-size: 0.72rem;">Unified Taxonomy Hub</span>
    </div>
    <p style="color: var(--admin-text-secondary); font-size: 0.9rem; margin: 0;">
      Organize foundational root categories and child subcategories across Prompts, AI Tools, Custom GPTs, Articles, and Challenges.
    </p>
  </div>

  <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
    <button type="button" onclick="switchTaxonomyTab('add_category')" class="admin-btn admin-btn-outline" style="display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 600;">
      <i data-lucide="folder-plus" style="width: 15px; height: 15px; color: #10b981;"></i>
      <span>+ Add Category</span>
    </button>
    <button type="button" onclick="switchTaxonomyTab('add_subcategory')" class="admin-btn admin-btn-primary" style="display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 700;">
      <i data-lucide="corner-down-right" style="width: 15px; height: 15px;"></i>
      <span>+ Add Subcategory</span>
    </button>
  </div>
</div>

<!-- Primary Navigation Tabs (Same Screen Unified Architecture) -->
<div class="admin-nav-tabs" style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.5rem; border-bottom: 2px solid var(--admin-border-main); margin-bottom: 1.75rem; overflow-x: auto;">
  <button type="button" id="tab-btn-categories" onclick="switchTaxonomyTab('categories')" class="ts-subtab-link <?= $activeTab === 'categories' ? 'active' : '' ?>">
    <i data-lucide="folder" style="width: 15px; height: 15px;"></i>
    <span>📁 Root Categories (<?= count($categories) ?>)</span>
  </button>
  <button type="button" id="tab-btn-subcategories" onclick="switchTaxonomyTab('subcategories')" class="ts-subtab-link <?= $activeTab === 'subcategories' ? 'active' : '' ?>">
    <i data-lucide="folder-tree" style="width: 15px; height: 15px;"></i>
    <span>↳ Subcategories (<?= count($subcategories) ?>)</span>
  </button>
  <button type="button" id="tab-btn-add_category" onclick="switchTaxonomyTab('add_category')" class="ts-subtab-link <?= $activeTab === 'add_category' ? 'active' : '' ?>">
    <i data-lucide="plus-circle" style="width: 15px; height: 15px; color: #10b981;"></i>
    <span>➕ Add Root Category</span>
  </button>
  <button type="button" id="tab-btn-add_subcategory" onclick="switchTaxonomyTab('add_subcategory')" class="ts-subtab-link <?= $activeTab === 'add_subcategory' ? 'active' : '' ?>">
    <i data-lucide="corner-down-right" style="width: 15px; height: 15px; color: #38bdf8;"></i>
    <span>➕ Add Subcategory</span>
  </button>
</div>

<!-- =========================================================================
     TAB 1: ROOT CATEGORIES PANEL
     ========================================================================= -->
<div id="panel-categories" class="taxonomy-panel" style="<?= $activeTab === 'categories' ? 'display: block;' : 'display: none;' ?>">
  <!-- Telemetry HUD -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.75rem;">
    <div class="admin-card" style="padding: 1.15rem; border-radius: 12px;">
      <div style="font-size: 0.75rem; color: var(--admin-text-muted); text-transform: uppercase; font-weight: 700;">Root Categories</div>
      <div style="font-size: 1.5rem; font-weight: 800; color: #10b981; margin-top: 0.25rem; font-family: var(--admin-font-mono);">
        <?= number_format(count($categories)) ?>
      </div>
      <div style="font-size: 0.75rem; color: var(--admin-text-secondary); margin-top: 0.2rem;">Foundational pillars</div>
    </div>

    <div class="admin-card" style="padding: 1.15rem; border-radius: 12px;">
      <div style="font-size: 0.75rem; color: var(--admin-text-muted); text-transform: uppercase; font-weight: 700;">Total Subcategories</div>
      <div style="font-size: 1.5rem; font-weight: 800; color: #38bdf8; margin-top: 0.25rem; font-family: var(--admin-font-mono);">
        <?= number_format($totalSubcategoriesCount) ?>
      </div>
      <div style="font-size: 0.75rem; color: var(--admin-text-secondary); margin-top: 0.2rem;">Attached child taxonomies</div>
    </div>

    <div class="admin-card" style="padding: 1.15rem; border-radius: 12px;">
      <div style="font-size: 0.75rem; color: var(--admin-text-muted); text-transform: uppercase; font-weight: 700;">Associated Entities</div>
      <div style="font-size: 1.5rem; font-weight: 800; color: #ec4899; margin-top: 0.25rem; font-family: var(--admin-font-mono);">
        <?= number_format($totalCategoryItems) ?>
      </div>
      <div style="font-size: 0.75rem; color: var(--admin-text-secondary); margin-top: 0.2rem;">Articles, tools, prompts, GPTs</div>
    </div>

    <div class="admin-card" style="padding: 1.15rem; border-radius: 12px;">
      <div style="font-size: 0.75rem; color: var(--admin-text-muted); text-transform: uppercase; font-weight: 700;">Homepage Featured</div>
      <div style="font-size: 1.5rem; font-weight: 800; color: #f59e0b; margin-top: 0.25rem; font-family: var(--admin-font-mono);">
        <?= number_format($featuredCategoryCount) ?>
      </div>
      <div style="font-size: 0.75rem; color: var(--admin-text-secondary); margin-top: 0.2rem;">Curated discovery badges</div>
    </div>
  </div>

  <!-- Scope Sub-Tabs -->
  <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.5rem; border-bottom: 1px solid var(--admin-border-main); margin-bottom: 1.5rem; overflow-x: auto; padding-bottom: 0.25rem;">
    <a href="<?= url('/admin/categories?tab=categories&scope=all') ?>" class="ts-subtab-link <?= $currentScope === 'all' ? 'active' : '' ?>">
      🌐 All Scopes (<?= $catScopeCounts['all'] ?>)
    </a>
    <a href="<?= url('/admin/categories?tab=categories&scope=prompt') ?>" class="ts-subtab-link <?= $currentScope === 'prompt' ? 'active' : '' ?>">
      ⚡ Prompts (<?= $catScopeCounts['prompt'] ?>)
    </a>
    <a href="<?= url('/admin/categories?tab=categories&scope=image_prompt') ?>" class="ts-subtab-link <?= $currentScope === 'image_prompt' ? 'active' : '' ?>">
      🎨 Image Prompts (<?= $catScopeCounts['image_prompt'] ?? 0 ?>)
    </a>
    <a href="<?= url('/admin/categories?tab=categories&scope=ai_tool') ?>" class="ts-subtab-link <?= $currentScope === 'ai_tool' ? 'active' : '' ?>">
      🛠️ AI Tools (<?= $catScopeCounts['ai_tool'] ?>)
    </a>
    <a href="<?= url('/admin/categories?tab=categories&scope=gpt_listing') ?>" class="ts-subtab-link <?= $currentScope === 'gpt_listing' ? 'active' : '' ?>">
      🤖 Custom GPTs (<?= $catScopeCounts['gpt_listing'] ?>)
    </a>
    <a href="<?= url('/admin/categories?tab=categories&scope=article') ?>" class="ts-subtab-link <?= $currentScope === 'article' ? 'active' : '' ?>">
      📝 Articles (<?= $catScopeCounts['article'] ?>)
    </a>
    <a href="<?= url('/admin/categories?tab=categories&scope=campaign') ?>" class="ts-subtab-link <?= $currentScope === 'campaign' ? 'active' : '' ?>">
      🏆 Challenges (<?= $catScopeCounts['campaign'] ?>)
    </a>
  </div>

  <!-- Search Toolbar -->
  <div class="admin-card" style="padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
    <div style="display: flex; gap: 1rem; align-items: center; justify-content: space-between; flex-wrap: wrap;">
      <div style="position: relative; flex: 1; min-width: 280px;">
        <i data-lucide="search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--admin-text-muted);"></i>
        <input type="text" id="categorySearchInput" oninput="filterCategoryTable()" placeholder="Search root categories by name or slug..." class="admin-input" style="padding-left: 2.25rem;">
      </div>
      <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.75rem;">
        <span id="categoryRowCountText" style="font-size: 0.8rem; color: var(--admin-text-muted); font-family: var(--admin-font-mono);">
          Showing <?= count($categories) ?> categories
        </span>
        <button type="button" onclick="AdminModal.open('categoryModal', { autofocus: true }); resetCategoryForm();" class="admin-btn admin-btn-primary admin-btn-sm" style="font-weight: 700;">
          + New Root Category
        </button>
      </div>
    </div>
  </div>

  <!-- Categories Table -->
  <div class="admin-card" style="border-radius: 14px; overflow: hidden; padding: 0;">
    <div style="overflow-x: auto;">
      <table class="admin-table" style="width: 100%; border-collapse: collapse;">
        <thead>
          <tr>
            <th style="width: 50px; text-align: center;">Icon</th>
            <th>Category Name</th>
            <th>SEO Slug</th>
            <th>Scope</th>
            <th style="text-align: center;">Subcategories</th>
            <th style="text-align: center;">Entities</th>
            <th style="text-align: center;">Featured</th>
            <th style="text-align: center;">Order</th>
            <th style="text-align: right; width: 130px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($categories)): ?>
            <tr>
              <td colspan="9" style="text-align: center; padding: 3rem 1.5rem; color: var(--admin-text-muted);">
                <i data-lucide="folder" style="width: 42px; height: 42px; opacity: 0.4; margin-bottom: 0.75rem;"></i>
                <div style="font-weight: 700; font-size: 1.05rem; color: var(--admin-text-primary);">No root categories found</div>
                <div style="font-size: 0.85rem; margin-top: 0.25rem;">Create your first top-level category pillar to get started.</div>
                <div style="margin-top: 1rem;">
                  <button type="button" onclick="switchTaxonomyTab('add_category')" class="admin-btn admin-btn-primary admin-btn-sm">
                    + Create First Category
                  </button>
                </div>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($categories as $cat): ?>
              <tr class="category-table-row" data-name="<?= htmlspecialchars(strtolower($cat['name'])) ?>" data-slug="<?= htmlspecialchars(strtolower($cat['slug'])) ?>">
                <td style="text-align: center;">
                  <div style="width: 34px; height: 34px; border-radius: 8px; background: <?= htmlspecialchars($cat['color_hex'] ?? '#6366f1') ?>22; color: <?= htmlspecialchars($cat['color_hex'] ?? '#6366f1') ?>; display: inline-flex; align-items: center; justify-content: center; border: 1px solid <?= htmlspecialchars($cat['color_hex'] ?? '#6366f1') ?>44;">
                    <i data-lucide="<?= htmlspecialchars($cat['icon_name'] ?: 'folder') ?>" style="width: 16px; height: 16px;"></i>
                  </div>
                </td>
                <td>
                  <div style="font-weight: 700; color: var(--admin-text-primary); font-size: 0.95rem;">
                    <?= htmlspecialchars($cat['name']) ?>
                  </div>
                  <?php if (!empty($cat['description'])): ?>
                    <div style="font-size: 0.76rem; color: var(--admin-text-muted); margin-top: 0.15rem; max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                      <?= htmlspecialchars($cat['description']) ?>
                    </div>
                  <?php endif; ?>
                </td>
                <td>
                  <code style="background: var(--admin-bg-surface-elevated); padding: 0.2rem 0.45rem; border-radius: 4px; font-size: 0.78rem; border: 1px solid var(--admin-border-subtle); color: var(--admin-text-secondary);">
                    /<?= htmlspecialchars($cat['slug']) ?>
                  </code>
                </td>
                <td>
                  <?php
                    $scLbl = [
                      'all' => ['Global', 'badge-cyan'],
                      'prompt' => ['Prompt', 'badge-purple'],
                      'ai_tool' => ['AI Tool', 'badge-blue'],
                      'gpt_listing' => ['GPT', 'badge-emerald'],
                      'article' => ['Article', 'badge-amber'],
                      'campaign' => ['Challenge', 'badge-rose']
                    ][$cat['applicable_to'] ?? 'all'] ?? ['Global', 'badge-cyan'];
                  ?>
                  <span class="admin-pill-badge <?= $scLbl[1] ?>" style="font-size: 0.7rem;">
                    <?= $scLbl[0] ?>
                  </span>
                </td>
                <td style="text-align: center;">
                  <button type="button" onclick="filterSubcategoriesByParent(<?= $cat['id'] ?>)" class="admin-pill-badge badge-blue" style="cursor: pointer; border: none; font-size: 0.75rem; font-family: var(--admin-font-mono);">
                    <?= number_format((int)($cat['subcategories_count'] ?? 0)) ?> subcategories ›
                  </button>
                </td>
                <td style="text-align: center;">
                  <span class="admin-pill-badge badge-gray" style="font-size: 0.75rem; font-family: var(--admin-font-mono);">
                    <?= number_format((int)($cat['total_items'] ?? 0)) ?>
                  </span>
                </td>
                <td style="text-align: center;">
                  <?php if (!empty($cat['is_featured_home'])): ?>
                    <span class="admin-pill-badge badge-amber" title="Featured on Homepage Explorer">
                      <i data-lucide="star" style="width: 10px; height: 10px; fill: currentColor;"></i> Featured
                    </span>
                  <?php else: ?>
                    <span style="color: var(--admin-text-muted); font-size: 0.75rem;">—</span>
                  <?php endif; ?>
                </td>
                <td style="text-align: center; font-family: var(--admin-font-mono); font-size: 0.8rem; color: var(--admin-text-muted);">
                  <?= (int)$cat['sort_order'] ?>
                </td>
                <td style="text-align: right;">
                  <div style="display: inline-flex; gap: 0.35rem; align-items: center;">
                    <button type="button" onclick='editCategory(<?= json_encode($cat, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="admin-btn admin-btn-outline admin-btn-sm" title="Edit Category" style="padding: 0.35rem 0.55rem;">
                      <i data-lucide="edit-3" style="width: 13px; height: 13px; color: var(--admin-accent-primary);"></i>
                    </button>
                    <form action="<?= url('/admin/categories/delete/' . $cat['id']) ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete category \'<?= htmlspecialchars(addslashes($cat['name'])) ?>\'? Subcategories under it will become detached.');" style="display: inline-block; margin: 0;">
                      <button type="submit" class="admin-btn admin-btn-outline admin-btn-sm" style="padding: 0.35rem 0.55rem; color: #ef4444;" title="Delete Category">
                        <i data-lucide="trash-2" style="width: 13px; height: 13px;"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- =========================================================================
     TAB 2: SUBCATEGORIES PANEL
     ========================================================================= -->
<div id="panel-subcategories" class="taxonomy-panel" style="<?= $activeTab === 'subcategories' ? 'display: block;' : 'display: none;' ?>">
  <!-- Telemetry HUD -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.75rem;">
    <div class="admin-card" style="padding: 1.15rem; border-radius: 12px;">
      <div style="font-size: 0.75rem; color: var(--admin-text-muted); text-transform: uppercase; font-weight: 700;">Subcategories</div>
      <div style="font-size: 1.5rem; font-weight: 800; color: #38bdf8; margin-top: 0.25rem; font-family: var(--admin-font-mono);">
        <?= number_format(count($subcategories)) ?>
      </div>
      <div style="font-size: 0.75rem; color: var(--admin-text-secondary); margin-top: 0.2rem;">Child taxonomy units</div>
    </div>

    <div class="admin-card" style="padding: 1.15rem; border-radius: 12px;">
      <div style="font-size: 0.75rem; color: var(--admin-text-muted); text-transform: uppercase; font-weight: 700;">Parents Represented</div>
      <div style="font-size: 1.5rem; font-weight: 800; color: #10b981; margin-top: 0.25rem; font-family: var(--admin-font-mono);">
        <?= number_format(count($parentsRepresented)) ?> / <?= count($parentCategories) ?>
      </div>
      <div style="font-size: 0.75rem; color: var(--admin-text-secondary); margin-top: 0.2rem;">Active parent roots</div>
    </div>

    <div class="admin-card" style="padding: 1.15rem; border-radius: 12px;">
      <div style="font-size: 0.75rem; color: var(--admin-text-muted); text-transform: uppercase; font-weight: 700;">Assigned Entities</div>
      <div style="font-size: 1.5rem; font-weight: 800; color: #ec4899; margin-top: 0.25rem; font-family: var(--admin-font-mono);">
        <?= number_format($totalSubcategoryItems) ?>
      </div>
      <div style="font-size: 0.75rem; color: var(--admin-text-secondary); margin-top: 0.2rem;">Articles, tools, prompts, GPTs</div>
    </div>

    <div class="admin-card" style="padding: 1.15rem; border-radius: 12px;">
      <div style="font-size: 0.75rem; color: var(--admin-text-muted); text-transform: uppercase; font-weight: 700;">Featured Highlighted</div>
      <div style="font-size: 1.5rem; font-weight: 800; color: #f59e0b; margin-top: 0.25rem; font-family: var(--admin-font-mono);">
        <?= number_format($featuredSubcategoryCount) ?>
      </div>
      <div style="font-size: 0.75rem; color: var(--admin-text-secondary); margin-top: 0.2rem;">Promoted filter badges</div>
    </div>
  </div>

  <!-- Scope Sub-Tabs -->
  <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.5rem; border-bottom: 1px solid var(--admin-border-main); margin-bottom: 1.5rem; overflow-x: auto; padding-bottom: 0.25rem;">
    <a href="<?= url('/admin/categories?tab=subcategories&scope=all' . ($filterParent ? '&parent_id=' . $filterParent : '')) ?>" class="ts-subtab-link <?= $currentScope === 'all' ? 'active' : '' ?>">
      🌐 All Scopes (<?= $subScopeCounts['all'] ?>)
    </a>
    <a href="<?= url('/admin/categories?tab=subcategories&scope=prompt' . ($filterParent ? '&parent_id=' . $filterParent : '')) ?>" class="ts-subtab-link <?= $currentScope === 'prompt' ? 'active' : '' ?>">
      ⚡ Prompts (<?= $subScopeCounts['prompt'] ?>)
    </a>
    <a href="<?= url('/admin/categories?tab=subcategories&scope=image_prompt' . ($filterParent ? '&parent_id=' . $filterParent : '')) ?>" class="ts-subtab-link <?= $currentScope === 'image_prompt' ? 'active' : '' ?>">
      🎨 Image Prompts (<?= $subScopeCounts['image_prompt'] ?? 0 ?>)
    </a>
    <a href="<?= url('/admin/categories?tab=subcategories&scope=ai_tool' . ($filterParent ? '&parent_id=' . $filterParent : '')) ?>" class="ts-subtab-link <?= $currentScope === 'ai_tool' ? 'active' : '' ?>">
      🛠️ AI Tools (<?= $subScopeCounts['ai_tool'] ?>)
    </a>
    <a href="<?= url('/admin/categories?tab=subcategories&scope=gpt_listing' . ($filterParent ? '&parent_id=' . $filterParent : '')) ?>" class="ts-subtab-link <?= $currentScope === 'gpt_listing' ? 'active' : '' ?>">
      🤖 Custom GPTs (<?= $subScopeCounts['gpt_listing'] ?>)
    </a>
    <a href="<?= url('/admin/categories?tab=subcategories&scope=article' . ($filterParent ? '&parent_id=' . $filterParent : '')) ?>" class="ts-subtab-link <?= $currentScope === 'article' ? 'active' : '' ?>">
      📝 Articles (<?= $subScopeCounts['article'] ?>)
    </a>
    <a href="<?= url('/admin/categories?tab=subcategories&scope=campaign' . ($filterParent ? '&parent_id=' . $filterParent : '')) ?>" class="ts-subtab-link <?= $currentScope === 'campaign' ? 'active' : '' ?>">
      🏆 Challenges (<?= $subScopeCounts['campaign'] ?>)
    </a>
  </div>

  <!-- Search & Parent Filter Toolbar -->
  <div class="admin-card" style="padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
    <div style="display: flex; gap: 1rem; align-items: center; justify-content: space-between; flex-wrap: wrap;">
      <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.75rem; align-items: center; flex: 1; min-width: 280px;">
        <div style="position: relative; flex: 1;">
          <i data-lucide="search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--admin-text-muted);"></i>
          <input type="text" id="subcategorySearchInput" oninput="filterSubcategoryTable()" placeholder="Search subcategories by name, parent, or slug..." class="admin-input" style="padding-left: 2.25rem;">
        </div>

        <div style="min-width: 220px;">
          <select id="subParentFilterSelect" onchange="window.location.href='<?= url('/admin/categories?tab=subcategories&scope=' . urlencode($currentScope)) ?>' + (this.value ? '&parent_id=' + this.value : '')" class="admin-input">
            <option value="">📁 Filter by Parent Root (All)</option>
            <?php foreach ($parentCategories as $parent): ?>
              <option value="<?= $parent['id'] ?>" <?= ($filterParent === (int)$parent['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($parent['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.5rem;">
        <span id="subcategoryRowCountText" style="font-size: 0.8rem; color: var(--admin-text-muted); font-family: var(--admin-font-mono);">
          Showing <?= count($subcategories) ?> subcategories
        </span>
        <?php if ($filterParent > 0): ?>
          <a href="<?= url('/admin/categories?tab=subcategories&scope=' . urlencode($currentScope)) ?>" class="admin-btn admin-btn-outline admin-btn-sm" style="font-size: 0.75rem;">
            Clear Parent Filter
          </a>
        <?php endif; ?>
        <button type="button" onclick="switchTaxonomyTab('add_subcategory')" class="admin-btn admin-btn-primary admin-btn-sm" style="font-weight: 700;">
          + New Subcategory
        </button>
      </div>
    </div>
  </div>

  <!-- Subcategories Table -->
  <div class="admin-card" style="border-radius: 14px; overflow: hidden; padding: 0;">
    <div style="overflow-x: auto;">
      <table class="admin-table" style="width: 100%; border-collapse: collapse;">
        <thead>
          <tr>
            <th style="width: 50px; text-align: center;">Icon</th>
            <th>Subcategory &amp; Lineage</th>
            <th>SEO Slug</th>
            <th>Scope</th>
            <th style="text-align: center;">Entities</th>
            <th style="text-align: center;">Featured</th>
            <th style="text-align: center;">Order</th>
            <th style="text-align: right; width: 130px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($subcategories)): ?>
            <tr>
              <td colspan="8" style="text-align: center; padding: 3rem 1.5rem; color: var(--admin-text-muted);">
                <i data-lucide="folder-tree" style="width: 42px; height: 42px; opacity: 0.4; margin-bottom: 0.75rem;"></i>
                <div style="font-weight: 700; font-size: 1.05rem; color: var(--admin-text-primary);">No subcategories found</div>
                <div style="font-size: 0.85rem; margin-top: 0.25rem;">No subcategories match the selected filter.</div>
                <div style="margin-top: 1rem;">
                  <button type="button" onclick="switchTaxonomyTab('add_subcategory')" class="admin-btn admin-btn-primary admin-btn-sm">
                    + Create First Subcategory
                  </button>
                </div>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($subcategories as $sub): ?>
              <tr class="subcategory-table-row" 
                  data-name="<?= htmlspecialchars(strtolower($sub['name'])) ?>" 
                  data-parent="<?= htmlspecialchars(strtolower($sub['parent_name'] ?? '')) ?>"
                  data-slug="<?= htmlspecialchars(strtolower($sub['slug'])) ?>">
                
                <td style="text-align: center;">
                  <div style="width: 34px; height: 34px; border-radius: 8px; background: <?= htmlspecialchars($sub['color_hex'] ?? '#38bdf8') ?>22; color: <?= htmlspecialchars($sub['color_hex'] ?? '#38bdf8') ?>; display: inline-flex; align-items: center; justify-content: center; border: 1px solid <?= htmlspecialchars($sub['color_hex'] ?? '#38bdf8') ?>44;">
                    <i data-lucide="<?= htmlspecialchars($sub['icon_name'] ?: 'tag') ?>" style="width: 16px; height: 16px;"></i>
                  </div>
                </td>

                <td>
                  <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.35rem; font-size: 0.78rem; color: var(--admin-text-muted); margin-bottom: 0.15rem;">
                    <i data-lucide="folder" style="width: 12px; height: 12px;"></i>
                    <span style="color: var(--admin-text-secondary); font-weight: 600;">
                      <?= htmlspecialchars($sub['parent_name'] ?? 'Root') ?>
                    </span>
                    <span>›</span>
                  </div>
                  <div style="font-weight: 700; color: var(--admin-text-primary); font-size: 0.95rem;">
                    <?= htmlspecialchars($sub['name']) ?>
                  </div>
                  <?php if (!empty($sub['description'])): ?>
                    <div style="font-size: 0.76rem; color: var(--admin-text-muted); margin-top: 0.15rem; max-width: 340px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                      <?= htmlspecialchars($sub['description']) ?>
                    </div>
                  <?php endif; ?>
                </td>

                <td>
                  <code style="background: var(--admin-bg-surface-elevated); padding: 0.2rem 0.45rem; border-radius: 4px; font-size: 0.78rem; border: 1px solid var(--admin-border-subtle); color: var(--admin-text-secondary);">
                    /<?= htmlspecialchars($sub['slug']) ?>
                  </code>
                </td>

                <td>
                  <?php
                    $scLbl = [
                      'all' => ['Global', 'badge-cyan'],
                      'prompt' => ['Prompt', 'badge-purple'],
                      'ai_tool' => ['AI Tool', 'badge-blue'],
                      'gpt_listing' => ['GPT', 'badge-emerald'],
                      'article' => ['Article', 'badge-amber'],
                      'campaign' => ['Challenge', 'badge-rose']
                    ][$sub['applicable_to'] ?? 'all'] ?? ['Global', 'badge-cyan'];
                  ?>
                  <span class="admin-pill-badge <?= $scLbl[1] ?>" style="font-size: 0.7rem;">
                    <?= $scLbl[0] ?>
                  </span>
                </td>

                <td style="text-align: center;">
                  <span class="admin-pill-badge badge-gray" style="font-size: 0.75rem; font-family: var(--admin-font-mono);">
                    <?= number_format((int)($sub['total_items'] ?? 0)) ?>
                  </span>
                </td>

                <td style="text-align: center;">
                  <?php if (!empty($sub['is_featured_home'])): ?>
                    <span class="admin-pill-badge badge-amber" title="Highlighted in discovery tracks">
                      <i data-lucide="star" style="width: 10px; height: 10px; fill: currentColor;"></i> Featured
                    </span>
                  <?php else: ?>
                    <span style="color: var(--admin-text-muted); font-size: 0.75rem;">—</span>
                  <?php endif; ?>
                </td>

                <td style="text-align: center; font-family: var(--admin-font-mono); font-size: 0.8rem; color: var(--admin-text-muted);">
                  <?= (int)$sub['sort_order'] ?>
                </td>

                <td style="text-align: right;">
                  <div style="display: inline-flex; gap: 0.35rem; align-items: center;">
                    <button type="button" 
                            onclick='editSubcategory(<?= json_encode($sub, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' 
                            class="admin-btn admin-btn-outline admin-btn-sm" 
                            title="Edit Subcategory"
                            style="padding: 0.35rem 0.55rem;">
                      <i data-lucide="edit-3" style="width: 13px; height: 13px; color: var(--admin-accent-primary);"></i>
                    </button>

                    <form action="<?= url('/admin/subcategories/delete/' . $sub['id']) ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete subcategory \'<?= htmlspecialchars(addslashes($sub['name'])) ?>\'?');" style="display: inline-block; margin: 0;">
                      <button type="submit" class="admin-btn admin-btn-outline admin-btn-sm" style="padding: 0.35rem 0.55rem; color: #ef4444;" title="Delete Subcategory">
                        <i data-lucide="trash-2" style="width: 13px; height: 13px;"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- =========================================================================
     TAB 3: ADD ROOT CATEGORY PANEL
     ========================================================================= -->
<div id="panel-add_category" class="taxonomy-panel" style="<?= $activeTab === 'add_category' ? 'display: block;' : 'display: none;' ?>">
  <div class="admin-card" style="max-width: 820px; margin: 0 auto 2.5rem auto; padding: 2rem; border-radius: 16px;">
    <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.65rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-main); padding-bottom: 1rem;">
      <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(99, 102, 241, 0.15); display: flex; align-items: center; justify-content: center; color: var(--admin-accent-primary);">
        <i data-lucide="folder-plus" style="width: 20px; height: 20px;"></i>
      </div>
      <div>
        <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--admin-text-primary); margin: 0; font-family: 'Outfit', sans-serif;">
          Create New Root Category
        </h2>
        <p style="font-size: 0.82rem; color: var(--admin-text-secondary); margin: 0.15rem 0 0 0;">
          Root categories serve as top-level pillars. Subcategories can be nested beneath them.
        </p>
      </div>
    </div>

    <form action="<?= url('/admin/categories/save') ?>" method="POST">
      <input type="hidden" name="id" value="">

      <!-- Row 1: Name & Live Slug Generator -->
      <div style="display: grid; grid-template-columns: 3fr 2fr; gap: 1.25rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
            Category Name <span style="color: #ef4444;">*</span>
          </label>
          <input type="text" name="name" id="tab_cat_name" oninput="autoGenerateCategorySlug(this.value, 'tab_cat_slug')" placeholder="e.g. Artificial Intelligence, Web Engineering" class="admin-input" required>
        </div>

        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
            SEO Slug Path
          </label>
          <input type="text" name="slug" id="tab_cat_slug" placeholder="e.g. artificial-intelligence" class="admin-input" style="font-family: var(--admin-font-mono);">
        </div>
      </div>

      <!-- Row 2: Applicable Scope & Sort Order -->
      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
            Applicable Scope <span style="color: #ef4444;">*</span>
          </label>
          <select name="applicable_to" class="admin-input" required>
            <option value="all">🌐 Global (All Sections &amp; Directories)</option>
            <option value="prompt">⚡ Text / Code Prompt Blueprints Only</option>
            <option value="image_prompt">🎨 Image Gen Prompts Only</option>
            <option value="ai_tool">🛠️ AI Tools Radar Only</option>
            <option value="gpt_listing">🤖 Custom GPTs &amp; Agents Only</option>
            <option value="article">📝 Technical Articles Only</option>
            <option value="campaign">🏆 Challenges &amp; Hackathons Only</option>
          </select>
        </div>

        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
            Sort Order
          </label>
          <input type="number" name="sort_order" value="0" class="admin-input">
        </div>
      </div>

      <!-- Row 3: Icon & Accent Color -->
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
            Lucide Icon Name
          </label>
          <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.5rem; align-items: center;">
            <input type="text" name="icon_name" id="tab_cat_icon" value="folder" class="admin-input" style="font-family: var(--admin-font-mono);">
            <div style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
              <?php foreach (['folder', 'cpu', 'code', 'terminal', 'bot', 'sparkles', 'database', 'globe'] as $ic): ?>
                <button type="button" onclick="document.getElementById('tab_cat_icon').value='<?= $ic ?>'" class="admin-btn admin-btn-outline admin-btn-sm" style="padding: 0.25rem 0.45rem; font-size: 0.7rem;" title="<?= $ic ?>">
                  <i data-lucide="<?= $ic ?>" style="width: 12px; height: 12px;"></i>
                </button>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
            Accent Color
          </label>
          <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.5rem; align-items: center;">
            <input type="color" name="color_hex" id="tab_cat_color" value="#6366f1" style="height: 38px; width: 44px; padding: 2px; border-radius: 6px; border: 1px solid var(--admin-border-main); background: var(--admin-bg-surface-elevated); cursor: pointer;">
            <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.35rem; align-items: center;">
              <?php foreach (['#6366f1', '#38bdf8', '#ec4899', '#10b981', '#f59e0b', '#8b5cf6'] as $hex): ?>
                <span onclick="document.getElementById('tab_cat_color').value='<?= $hex ?>'" style="display: inline-block; width: 20px; height: 20px; border-radius: 50%; background: <?= $hex ?>; cursor: pointer; border: 1px solid rgba(255,255,255,0.2);"></span>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Row 4: Description -->
      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
          Description / Scope Summary
        </label>
        <textarea name="description" rows="2" placeholder="Brief summary of topics and resources contained in this category..." class="admin-input"></textarea>
      </div>

      <!-- Row 5: Homepage Featured Toggle -->
      <div style="margin-bottom: 1.5rem; background: var(--admin-bg-surface-elevated); padding: 1rem; border-radius: 8px; border: 1px solid var(--admin-border-main);">
        <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; margin: 0;">
          <input type="checkbox" name="is_featured_home" value="1" style="width: 18px; height: 18px; accent-color: var(--admin-accent-primary);">
          <div>
            <strong style="color: var(--admin-text-primary); font-size: 0.9rem;">Feature on Homepage Curated Explorer</strong>
            <div style="font-size: 0.75rem; color: var(--admin-text-muted);">
              Pins this category to hero explorer badges and discovery tracks.
            </div>
          </div>
        </label>
      </div>

      <div style="flex-wrap: wrap; align-items: center; display: flex; justify-content: flex-end; gap: 0.75rem;">
        <button type="button" onclick="switchTaxonomyTab('categories')" class="admin-btn admin-btn-outline">Cancel</button>
        <button type="submit" class="admin-btn admin-btn-primary" style="padding: 0.65rem 1.75rem; font-weight: 700;">
          Create Root Category 🚀
        </button>
      </div>
    </form>
  </div>
</div>

<!-- =========================================================================
     TAB 4: ADD SUBCATEGORY PANEL
     ========================================================================= -->
<div id="panel-add_subcategory" class="taxonomy-panel" style="<?= $activeTab === 'add_subcategory' ? 'display: block;' : 'display: none;' ?>">
  <div class="admin-card" style="max-width: 840px; margin: 0 auto 2.5rem auto; padding: 2rem; border-radius: 16px;">
    <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.65rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-main); padding-bottom: 1rem;">
      <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(56, 189, 248, 0.15); display: flex; align-items: center; justify-content: center; color: #38bdf8;">
        <i data-lucide="corner-down-right" style="width: 20px; height: 20px;"></i>
      </div>
      <div>
        <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--admin-text-primary); margin: 0; font-family: 'Outfit', sans-serif;">
          Create New Subcategory
        </h2>
        <p style="font-size: 0.82rem; color: var(--admin-text-secondary); margin: 0.15rem 0 0 0;">
          Subcategories provide specific indexing nested directly beneath a root parent category.
        </p>
      </div>
    </div>

    <form action="<?= url('/admin/subcategories/save') ?>" method="POST">
      <input type="hidden" name="id" value="">

      <!-- Parent Category Selection -->
      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
          Parent Root Category <span style="color: #ef4444;">*</span>
        </label>
        <select name="parent_id" id="tab_sub_parent" class="admin-input" required style="font-weight: 600;" onchange="handleParentScopeChange(this, 'tab_sub_scope')">
          <option value="" data-scope="all">-- Select Root Parent Category --</option>
          <?php foreach ($parentCategories as $parent): ?>
            <option value="<?= $parent['id'] ?>" data-scope="<?= htmlspecialchars($parent['applicable_to'] ?? 'all') ?>" <?= ($filterParent === (int)$parent['id']) ? 'selected' : '' ?>>
              📁 <?= htmlspecialchars($parent['name']) ?> (Scope: <?= htmlspecialchars($parent['applicable_to']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Row 1: Subcategory Name & Live Slug -->
      <div style="display: grid; grid-template-columns: 3fr 2fr; gap: 1.25rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
            Subcategory Name <span style="color: #ef4444;">*</span>
          </label>
          <input type="text" name="name" id="tab_sub_name" oninput="autoGenerateSubcategorySlug(this.value, 'tab_sub_slug')" placeholder="e.g. Machine Learning, React Framework" class="admin-input" required>
        </div>

        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
            SEO Slug Path
          </label>
          <input type="text" name="slug" id="tab_sub_slug" placeholder="e.g. machine-learning" class="admin-input" style="font-family: var(--admin-font-mono);">
        </div>
      </div>

      <!-- Row 2: Applicable Scope & Sort Order -->
      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
            Applicable Scope <span style="color: #ef4444;">*</span>
          </label>
          <select name="applicable_to" id="tab_sub_scope" class="admin-input" required>
            <option value="all">🌐 Inherit / Global (All Sections)</option>
            <option value="prompt">⚡ Text / Code Prompt Blueprints Only</option>
            <option value="image_prompt">🎨 Image Gen Prompts Only</option>
            <option value="ai_tool">🛠️ AI Tools Radar Only</option>
            <option value="gpt_listing">🤖 Custom GPTs &amp; Agents Only</option>
            <option value="article">📝 Technical Articles Only</option>
            <option value="campaign">🏆 Challenges &amp; Hackathons Only</option>
          </select>
        </div>

        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
            Sort Order
          </label>
          <input type="number" name="sort_order" value="0" class="admin-input">
        </div>
      </div>

      <!-- Row 3: Icon & Accent Color -->
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
            Lucide Icon Name
          </label>
          <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.5rem; align-items: center;">
            <input type="text" name="icon_name" id="tab_sub_icon" value="tag" class="admin-input" style="font-family: var(--admin-font-mono);">
            <div style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
              <?php foreach (['tag', 'bookmark', 'layers', 'code', 'terminal', 'cpu', 'sparkles', 'git-branch'] as $ic): ?>
                <button type="button" onclick="document.getElementById('tab_sub_icon').value='<?= $ic ?>'" class="admin-btn admin-btn-outline admin-btn-sm" style="padding: 0.25rem 0.45rem; font-size: 0.7rem;" title="<?= $ic ?>">
                  <i data-lucide="<?= $ic ?>" style="width: 12px; height: 12px;"></i>
                </button>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
            Accent Color
          </label>
          <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.5rem; align-items: center;">
            <input type="color" name="color_hex" id="tab_sub_color" value="#38bdf8" style="height: 38px; width: 44px; padding: 2px; border-radius: 6px; border: 1px solid var(--admin-border-main); background: var(--admin-bg-surface-elevated); cursor: pointer;">
            <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.35rem; align-items: center;">
              <?php foreach (['#38bdf8', '#6366f1', '#ec4899', '#10b981', '#f59e0b', '#8b5cf6', '#14b8a6'] as $hex): ?>
                <span onclick="document.getElementById('tab_sub_color').value='<?= $hex ?>'" style="display: inline-block; width: 20px; height: 20px; border-radius: 50%; background: <?= $hex ?>; cursor: pointer; border: 1px solid rgba(255,255,255,0.2);"></span>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Row 4: Description -->
      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
          Description / Scope Summary
        </label>
        <textarea name="description" rows="2" placeholder="Brief summary of sub-discipline or taxonomy branch..." class="admin-input"></textarea>
      </div>

      <!-- Row 5: Homepage Featured Toggle -->
      <div style="margin-bottom: 1.5rem; background: var(--admin-bg-surface-elevated); padding: 1rem; border-radius: 8px; border: 1px solid var(--admin-border-main);">
        <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; margin: 0;">
          <input type="checkbox" name="is_featured_home" value="1" style="width: 18px; height: 18px; accent-color: var(--admin-accent-primary);">
          <div>
            <strong style="color: var(--admin-text-primary); font-size: 0.9rem;">Highlight in Discovery Modules</strong>
            <div style="font-size: 0.75rem; color: var(--admin-text-muted);">
              Surfaces this subcategory in trending pill filters and recommendation feeds.
            </div>
          </div>
        </label>
      </div>

      <div style="flex-wrap: wrap; align-items: center; display: flex; justify-content: flex-end; gap: 0.75rem;">
        <button type="button" onclick="switchTaxonomyTab('subcategories')" class="admin-btn admin-btn-outline">Cancel</button>
        <button type="submit" class="admin-btn admin-btn-primary" style="padding: 0.65rem 1.75rem; font-weight: 700;">
          Create Subcategory 🚀
        </button>
      </div>
    </form>
  </div>
</div>

<!-- =========================================================================
     MODAL 1: FAST EDIT ROOT CATEGORY
     ========================================================================= -->
<div id="categoryModal" class="ts-modal-overlay" aria-hidden="true" role="dialog" aria-labelledby="catModalHeaderTitle">
  <div class="ts-modal-card" style="max-width: 640px;">
    <div class="ts-modal-header">
      <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.5rem;">
        <div style="width: 28px; height: 28px; border-radius: 6px; background: rgba(99, 102, 241, 0.15); display: flex; align-items: center; justify-content: center; color: var(--admin-accent-primary);">
          <i data-lucide="folder" style="width: 16px; height: 16px;"></i>
        </div>
        <h3 id="catModalHeaderTitle" class="ts-modal-title">Category Specification</h3>
      </div>
      <button type="button" class="ts-modal-close" onclick="AdminModal.close('categoryModal')" aria-label="Close dialog">
        <i data-lucide="x" style="width: 18px; height: 18px;"></i>
      </button>
    </div>

    <form action="<?= url('/admin/categories/save') ?>" method="POST">
      <input type="hidden" name="id" id="modal_cat_id" value="">

      <div class="ts-modal-body">
        <!-- Row 1: Name & Slug -->
        <div style="display: grid; grid-template-columns: 3fr 2fr; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
              Category Name <span style="color: #ef4444;">*</span>
            </label>
            <input type="text" name="name" id="modal_cat_name" oninput="autoGenerateCategorySlug(this.value, 'modal_cat_slug')" placeholder="e.g. Artificial Intelligence" class="admin-input" required>
          </div>

          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
              SEO Slug Path
            </label>
            <input type="text" name="slug" id="modal_cat_slug" placeholder="e.g. artificial-intelligence" class="admin-input" style="font-family: var(--admin-font-mono);">
          </div>
        </div>

        <!-- Row 2: Scope & Sort Order -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
              Applicable Scope
            </label>
            <select name="applicable_to" id="modal_cat_scope" class="admin-input" required>
              <option value="all">🌐 Global (All Sections)</option>
              <option value="prompt">⚡ Text / Code Prompt Blueprints Only</option>
              <option value="image_prompt">🎨 Image Gen Prompts Only</option>
              <option value="ai_tool">🛠️ AI Tools Only</option>
              <option value="gpt_listing">🤖 Custom GPTs Only</option>
              <option value="article">📝 Articles Only</option>
              <option value="campaign">🏆 Challenges Only</option>
            </select>
          </div>

          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
              Sort Order
            </label>
            <input type="number" name="sort_order" id="modal_cat_sort" value="0" class="admin-input">
          </div>
        </div>

        <!-- Row 3: Icon & Color -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
              Lucide Icon Name
            </label>
            <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.5rem; align-items: center;">
              <input type="text" name="icon_name" id="modal_cat_icon" value="folder" class="admin-input" style="font-family: var(--admin-font-mono);">
              <div style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
                <?php foreach (['folder', 'cpu', 'code', 'terminal', 'bot', 'sparkles', 'database', 'globe'] as $ic): ?>
                  <button type="button" onclick="document.getElementById('modal_cat_icon').value='<?= $ic ?>'" class="admin-btn admin-btn-outline admin-btn-sm" style="padding: 0.2rem 0.4rem; font-size: 0.68rem;" title="<?= $ic ?>">
                    <i data-lucide="<?= $ic ?>" style="width: 11px; height: 11px;"></i>
                  </button>
                <?php endforeach; ?>
              </div>
            </div>
          </div>

          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
              Accent Color
            </label>
            <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.5rem; align-items: center;">
              <input type="color" name="color_hex" id="modal_cat_color" value="#6366f1" style="height: 38px; width: 44px; padding: 2px; border-radius: 6px; border: 1px solid var(--admin-border-main); background: var(--admin-bg-surface-elevated); cursor: pointer;">
              <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.35rem; align-items: center;">
                <?php foreach (['#6366f1', '#38bdf8', '#ec4899', '#10b981', '#f59e0b', '#8b5cf6'] as $hex): ?>
                  <span onclick="document.getElementById('modal_cat_color').value='<?= $hex ?>'" style="display: inline-block; width: 18px; height: 18px; border-radius: 50%; background: <?= $hex ?>; cursor: pointer; border: 1px solid rgba(255,255,255,0.2);"></span>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Row 4: Description -->
        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
            Description / Summary
          </label>
          <textarea name="description" id="modal_cat_desc" rows="2" placeholder="Brief 1-2 sentence description explaining this category..." class="admin-input"></textarea>
        </div>

        <!-- Row 5: Homepage Featured Toggle -->
        <div style="background: var(--admin-bg-surface-elevated); padding: 0.85rem 1rem; border-radius: 8px; border: 1px solid var(--admin-border-main);">
          <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; margin: 0;">
            <input type="checkbox" name="is_featured_home" id="modal_cat_featured" value="1" style="width: 18px; height: 18px; accent-color: var(--admin-accent-primary);">
            <div>
              <strong style="color: var(--admin-text-primary); font-size: 0.88rem;">Feature on Homepage Curated Explorer</strong>
              <div style="font-size: 0.75rem; color: var(--admin-text-muted);">
                Pins this category to hero explorer badges and discovery tracks.
              </div>
            </div>
          </label>
        </div>
      </div>

      <div class="ts-modal-footer">
        <button type="button" onclick="AdminModal.close('categoryModal')" class="admin-btn admin-btn-outline">Cancel</button>
        <button type="submit" class="admin-btn admin-btn-primary" style="padding: 0.65rem 1.5rem; font-weight: 700;">
          Save Category Specification 🚀
        </button>
      </div>
    </form>
  </div>
</div>

<!-- =========================================================================
     MODAL 2: FAST EDIT SUBCATEGORY
     ========================================================================= -->
<div id="subcategoryModal" class="ts-modal-overlay" aria-hidden="true" role="dialog" aria-labelledby="subModalHeaderTitle">
  <div class="ts-modal-card" style="max-width: 640px;">
    <div class="ts-modal-header">
      <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.5rem;">
        <div style="width: 28px; height: 28px; border-radius: 6px; background: rgba(56, 189, 248, 0.15); display: flex; align-items: center; justify-content: center; color: #38bdf8;">
          <i data-lucide="corner-down-right" style="width: 16px; height: 16px;"></i>
        </div>
        <h3 id="subModalHeaderTitle" class="ts-modal-title">Subcategory Configuration</h3>
      </div>
      <button type="button" class="ts-modal-close" onclick="AdminModal.close('subcategoryModal')" aria-label="Close dialog">
        <i data-lucide="x" style="width: 18px; height: 18px;"></i>
      </button>
    </div>

    <form action="<?= url('/admin/subcategories/save') ?>" method="POST">
      <input type="hidden" name="id" id="modal_sub_id" value="">

      <div class="ts-modal-body">
        <!-- Parent Category -->
        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
            Parent Root Category <span style="color: #ef4444;">*</span>
          </label>
          <select name="parent_id" id="modal_sub_parent" class="admin-input" required style="font-weight: 600;" onchange="handleParentScopeChange(this, 'modal_sub_scope')">
            <option value="" data-scope="all">-- Choose Parent Category --</option>
            <?php foreach ($parentCategories as $parent): ?>
              <option value="<?= $parent['id'] ?>" data-scope="<?= htmlspecialchars($parent['applicable_to'] ?? 'all') ?>">📁 <?= htmlspecialchars($parent['name']) ?> (Scope: <?= htmlspecialchars($parent['applicable_to']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Name & Slug -->
        <div style="display: grid; grid-template-columns: 3fr 2fr; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
              Subcategory Name <span style="color: #ef4444;">*</span>
            </label>
            <input type="text" name="name" id="modal_sub_name" oninput="autoGenerateSubcategorySlug(this.value, 'modal_sub_slug')" placeholder="e.g. Prompt Engineering" class="admin-input" required>
          </div>

          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
              SEO Slug Path
            </label>
            <input type="text" name="slug" id="modal_sub_slug" placeholder="e.g. prompt-engineering" class="admin-input" style="font-family: var(--admin-font-mono);">
          </div>
        </div>

        <!-- Scope & Sort Order -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
              Applicable Scope
            </label>
            <select name="applicable_to" id="modal_sub_scope" class="admin-input" required>
              <option value="all">🌐 Inherit / Global (All Sections)</option>
              <option value="prompt">⚡ Text / Code Prompt Blueprints Only</option>
              <option value="image_prompt">🎨 Image Gen Prompts Only</option>
              <option value="ai_tool">🛠️ AI Tools Only</option>
              <option value="gpt_listing">🤖 Custom GPTs Only</option>
              <option value="article">📝 Articles Only</option>
              <option value="campaign">🏆 Challenges Only</option>
            </select>
          </div>

          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
              Sort Order
            </label>
            <input type="number" name="sort_order" id="modal_sub_sort" value="0" class="admin-input">
          </div>
        </div>

        <!-- Icon & Color -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
              Lucide Icon Name
            </label>
            <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.5rem; align-items: center;">
              <input type="text" name="icon_name" id="modal_sub_icon" value="tag" class="admin-input" style="font-family: var(--admin-font-mono);">
              <div style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
                <?php foreach (['tag', 'bookmark', 'layers', 'code', 'terminal', 'cpu', 'sparkles', 'git-branch'] as $ic): ?>
                  <button type="button" onclick="document.getElementById('modal_sub_icon').value='<?= $ic ?>'" class="admin-btn admin-btn-outline admin-btn-sm" style="padding: 0.2rem 0.4rem; font-size: 0.68rem;" title="<?= $ic ?>">
                    <i data-lucide="<?= $ic ?>" style="width: 11px; height: 11px;"></i>
                  </button>
                <?php endforeach; ?>
              </div>
            </div>
          </div>

          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
              Accent Color
            </label>
            <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.5rem; align-items: center;">
              <input type="color" name="color_hex" id="modal_sub_color" value="#38bdf8" style="height: 38px; width: 44px; padding: 2px; border-radius: 6px; border: 1px solid var(--admin-border-main); background: var(--admin-bg-surface-elevated); cursor: pointer;">
              <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.35rem; align-items: center;">
                <?php foreach (['#38bdf8', '#6366f1', '#ec4899', '#10b981', '#f59e0b', '#8b5cf6', '#14b8a6'] as $hex): ?>
                  <span onclick="document.getElementById('modal_sub_color').value='<?= $hex ?>'" style="display: inline-block; width: 18px; height: 18px; border-radius: 50%; background: <?= $hex ?>; cursor: pointer; border: 1px solid rgba(255,255,255,0.2);"></span>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Description -->
        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
            Description / Summary
          </label>
          <textarea name="description" id="modal_sub_desc" rows="2" placeholder="Brief 1-2 sentence description explaining this subcategory..." class="admin-input"></textarea>
        </div>

        <!-- Featured Toggle -->
        <div style="background: var(--admin-bg-surface-elevated); padding: 0.85rem 1rem; border-radius: 8px; border: 1px solid var(--admin-border-main);">
          <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; margin: 0;">
            <input type="checkbox" name="is_featured_home" id="modal_sub_featured" value="1" style="width: 18px; height: 18px; accent-color: var(--admin-accent-primary);">
            <div>
              <strong style="color: var(--admin-text-primary); font-size: 0.88rem;">Highlight in Discovery Modules</strong>
              <div style="font-size: 0.75rem; color: var(--admin-text-muted);">
                Surfaces this subcategory in trending pill filters and recommendation feeds.
              </div>
            </div>
          </label>
        </div>
      </div>

      <div class="ts-modal-footer">
        <button type="button" onclick="AdminModal.close('subcategoryModal')" class="admin-btn admin-btn-outline">Cancel</button>
        <button type="submit" class="admin-btn admin-btn-primary" style="padding: 0.65rem 1.5rem; font-weight: 700;">
          Save Subcategory 🚀
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function switchTaxonomyTab(tabKey) {
    // 1. Hide all panels
    document.querySelectorAll('.taxonomy-panel').forEach(function(panel) {
        panel.style.display = 'none';
    });

    // 2. Remove active class from all tab buttons
    document.querySelectorAll('.admin-nav-tabs .ts-subtab-link').forEach(function(btn) {
        btn.classList.remove('active');
    });

    // 3. Show target panel
    var targetPanel = document.getElementById('panel-' + tabKey);
    if (targetPanel) {
        targetPanel.style.display = 'block';
    }

    // 4. Activate target button
    var targetBtn = document.getElementById('tab-btn-' + tabKey);
    if (targetBtn) {
        targetBtn.classList.add('active');
    }

    // 5. Update browser URL history without reloading
    var currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('tab', tabKey);
    window.history.replaceState({ tab: tabKey }, '', currentUrl.toString());

    // 6. Refresh Lucide icons inside target panel
    if (window.lucide && typeof window.lucide.createIcons === 'function' && targetPanel) {
        window.lucide.createIcons({ root: targetPanel });
    }
}

function filterSubcategoriesByParent(parentId) {
    switchTaxonomyTab('subcategories');
    var select = document.getElementById('subParentFilterSelect');
    if (select) {
        select.value = parentId;
        window.location.href = '<?= url("/admin/categories?tab=subcategories&scope=" . urlencode($currentScope)) ?>&parent_id=' + parentId;
    }
}

function autoGenerateCategorySlug(name, targetId) {
    var target = document.getElementById(targetId);
    if (!target) return;
    target.value = name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
}

function autoGenerateSubcategorySlug(name, targetId) {
    var target = document.getElementById(targetId);
    if (!target) return;
    target.value = name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
}

function resetCategoryForm() {
    document.getElementById('modal_cat_id').value = '';
    document.getElementById('modal_cat_name').value = '';
    document.getElementById('modal_cat_slug').value = '';
    document.getElementById('modal_cat_scope').value = 'all';
    document.getElementById('modal_cat_sort').value = '0';
    document.getElementById('modal_cat_icon').value = 'folder';
    document.getElementById('modal_cat_color').value = '#6366f1';
    document.getElementById('modal_cat_desc').value = '';
    document.getElementById('modal_cat_featured').checked = false;
    document.getElementById('catModalHeaderTitle').innerText = 'Create Root Category';
}

function editCategory(cat) {
    document.getElementById('modal_cat_id').value = cat.id;
    document.getElementById('modal_cat_name').value = cat.name || '';
    document.getElementById('modal_cat_slug').value = cat.slug || '';
    document.getElementById('modal_cat_scope').value = cat.applicable_to || 'all';
    document.getElementById('modal_cat_sort').value = cat.sort_order || 0;
    document.getElementById('modal_cat_icon').value = cat.icon_name || 'folder';
    document.getElementById('modal_cat_color').value = cat.color_hex || '#6366f1';
    document.getElementById('modal_cat_desc').value = cat.description || '';
    document.getElementById('modal_cat_featured').checked = (parseInt(cat.is_featured_home) === 1);

    document.getElementById('catModalHeaderTitle').innerText = 'Edit Category #' + cat.id + ': ' + (cat.name || '');
    AdminModal.open('categoryModal', { autofocus: true });
}

function resetSubcategoryForm() {
    document.getElementById('modal_sub_id').value = '';
    document.getElementById('modal_sub_parent').value = '<?= $filterParent > 0 ? $filterParent : "" ?>';
    document.getElementById('modal_sub_name').value = '';
    document.getElementById('modal_sub_slug').value = '';
    document.getElementById('modal_sub_scope').value = 'all';
    document.getElementById('modal_sub_sort').value = '0';
    document.getElementById('modal_sub_icon').value = 'tag';
    document.getElementById('modal_sub_color').value = '#38bdf8';
    document.getElementById('modal_sub_desc').value = '';
    document.getElementById('modal_sub_featured').checked = false;
    document.getElementById('subModalHeaderTitle').innerText = 'Create Subcategory';
}

function editSubcategory(sub) {
    document.getElementById('modal_sub_id').value = sub.id;
    document.getElementById('modal_sub_parent').value = sub.parent_id || '';
    document.getElementById('modal_sub_name').value = sub.name || '';
    document.getElementById('modal_sub_slug').value = sub.slug || '';
    document.getElementById('modal_sub_scope').value = sub.applicable_to || 'all';
    document.getElementById('modal_sub_sort').value = sub.sort_order || 0;
    document.getElementById('modal_sub_icon').value = sub.icon_name || 'tag';
    document.getElementById('modal_sub_color').value = sub.color_hex || '#38bdf8';
    document.getElementById('modal_sub_desc').value = sub.description || '';
    document.getElementById('modal_sub_featured').checked = (parseInt(sub.is_featured_home) === 1);

    document.getElementById('subModalHeaderTitle').innerText = 'Edit Subcategory #' + sub.id + ': ' + (sub.name || '');
    AdminModal.open('subcategoryModal', { autofocus: true });
}

function filterCategoryTable() {
    var q = (document.getElementById('categorySearchInput').value || '').toLowerCase().trim();
    var rows = document.querySelectorAll('.category-table-row');
    var visible = 0;

    rows.forEach(function(row) {
        var name = row.getAttribute('data-name') || '';
        var slug = row.getAttribute('data-slug') || '';
        if (!q || name.indexOf(q) !== -1 || slug.indexOf(q) !== -1) {
            row.style.display = '';
            visible++;
        } else {
            row.style.display = 'none';
        }
    });

    var countText = document.getElementById('categoryRowCountText');
    if (countText) {
        countText.innerText = 'Showing ' + visible + ' matching categories';
    }
}

function filterSubcategoryTable() {
    var q = (document.getElementById('subcategorySearchInput').value || '').toLowerCase().trim();
    var rows = document.querySelectorAll('.subcategory-table-row');
    var visible = 0;

    rows.forEach(function(row) {
        var name = row.getAttribute('data-name') || '';
        var parent = row.getAttribute('data-parent') || '';
        var slug = row.getAttribute('data-slug') || '';
        if (!q || name.indexOf(q) !== -1 || parent.indexOf(q) !== -1 || slug.indexOf(q) !== -1) {
            row.style.display = '';
            visible++;
        } else {
            row.style.display = 'none';
        }
    });

    var countText = document.getElementById('subcategoryRowCountText');
    if (countText) {
        countText.innerText = 'Showing ' + visible + ' matching subcategories';
    }
}

function handleParentScopeChange(parentSelect, childScopeSelectId) {
    var selOpt = parentSelect.options[parentSelect.selectedIndex];
    var pScope = selOpt ? selOpt.getAttribute('data-scope') : 'all';
    var childScopeSelect = document.getElementById(childScopeSelectId);
    if (!childScopeSelect) return;

    if (pScope && pScope !== 'all') {
        childScopeSelect.value = pScope;
    }
}
</script>

<?php require __DIR__ . '/../../layouts/admin_footer.php'; ?>
