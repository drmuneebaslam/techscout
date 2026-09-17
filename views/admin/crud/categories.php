<?php require __DIR__ . '/../../layouts/admin_header.php'; ?>

<?php
$currentScope = $currentScope ?? 'all';
$activeTab = $activeTab ?? 'list';

$scopeCounts = [
    'all' => count($categories),
    'article' => 0,
    'ai_tool' => 0,
    'gpt_listing' => 0,
    'prompt' => 0,
    'campaign' => 0
];
$totalSubcategories = 0;
$totalAssignedItems = 0;
$featuredCount = 0;

foreach ($categories as $cat) {
    $scope = $cat['applicable_to'] ?? 'all';
    if (isset($scopeCounts[$scope])) {
        $scopeCounts[$scope]++;
    }
    $totalSubcategories += (int)($cat['subcategories_count'] ?? 0);
    $totalAssignedItems += (int)($cat['total_items'] ?? 0);
    if (!empty($cat['is_featured_home'])) {
        $featuredCount++;
    }
}
?>

<div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <div style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 0.35rem;">
      <h1 style="font-size: 1.75rem; color: var(--admin-text-primary); margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        Root Categories Management
      </h1>
      <span class="admin-pill-badge badge-cyan" style="font-size: 0.72rem;">Pillars &amp; Modules</span>
    </div>
    <p style="color: var(--admin-text-secondary); font-size: 0.9rem; margin: 0;">
      Manage top-level foundational categories for Prompts, AI Tools, Custom GPTs, Articles, and Challenges.
    </p>
  </div>

  <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
    <a href="<?= url('/admin/subcategories') ?>" class="admin-btn admin-btn-outline" style="display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 600;">
      <i data-lucide="folder-tree" style="width: 15px; height: 15px; color: #38bdf8;"></i>
      <span>View Subcategories (<?= number_format($totalSubcategories) ?>)</span>
    </a>
    <button type="button" onclick="AdminModal.open('categoryModal', { autofocus: true }); resetCategoryForm();" class="admin-btn admin-btn-primary" style="display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 700;">
      <i data-lucide="plus-circle" style="width: 15px; height: 15px;"></i>
      <span>+ Add Category</span>
    </button>
  </div>
</div>

<!-- Primary Navigation Tabs (Separating Categories, Subcategories, and their Addition) -->
<div class="admin-nav-tabs" style="display: flex; gap: 0.5rem; border-bottom: 2px solid var(--admin-border-main); margin-bottom: 1.75rem;">
  <a href="<?= url('/admin/categories') ?>" class="ts-subtab-link <?= $activeTab === 'list' ? 'active' : '' ?>">
    <i data-lucide="folder" style="width: 15px; height: 15px;"></i>
    <span>📁 Root Categories (<?= number_format(count($categories)) ?>)</span>
  </a>
  <a href="<?= url('/admin/subcategories') ?>" class="ts-subtab-link">
    <i data-lucide="folder-tree" style="width: 15px; height: 15px;"></i>
    <span>↳ Subcategories (<?= number_format($totalSubcategories) ?>)</span>
  </a>
  <a href="<?= url('/admin/categories?tab=add') ?>" class="ts-subtab-link <?= $activeTab === 'add' ? 'active' : '' ?>">
    <i data-lucide="plus-circle" style="width: 15px; height: 15px; color: #10b981;"></i>
    <span>➕ Add Category</span>
  </a>
  <a href="<?= url('/admin/subcategories?tab=add') ?>" class="ts-subtab-link">
    <i data-lucide="corner-down-right" style="width: 15px; height: 15px; color: #38bdf8;"></i>
    <span>➕ Add Subcategory</span>
  </a>
</div>

<?php if ($activeTab === 'add'): ?>
<!-- =========================================================================
     DEDICATED TAB: ADD CATEGORY FORM
     ========================================================================= -->
<div class="admin-card" style="max-width: 820px; margin: 0 auto 2.5rem auto; padding: 2rem; border-radius: 16px;">
  <div style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-main); padding-bottom: 1rem;">
    <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(99, 102, 241, 0.15); display: flex; align-items: center; justify-content: center; color: var(--admin-accent-primary);">
      <i data-lucide="folder-plus" style="width: 20px; height: 20px;"></i>
    </div>
    <div>
      <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--admin-text-primary); margin: 0; font-family: 'Outfit', sans-serif;">
        Create New Root Category
      </h2>
      <p style="font-size: 0.82rem; color: var(--admin-text-secondary); margin: 0.15rem 0 0 0;">
        Root categories serve as top-level pillars. Subcategories can be attached under them.
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
        <input type="text" name="name" id="tab_cat_name" oninput="autoGenerateCategorySlug(this.value, 'tab_cat_slug')" placeholder="e.g. Artificial Intelligence, Web Engineering" class="admin-input" required autofocus>
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
          <option value="prompt">⚡ Prompt Blueprints &amp; Images Only</option>
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
        <div style="display: flex; gap: 0.5rem; align-items: center;">
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
        <div style="display: flex; gap: 0.5rem; align-items: center;">
          <input type="color" name="color_hex" id="tab_cat_color" value="#6366f1" style="height: 38px; width: 44px; padding: 2px; border-radius: 6px; border: 1px solid var(--admin-border-main); background: var(--admin-bg-surface-elevated); cursor: pointer;">
          <div style="display: flex; gap: 0.35rem; align-items: center;">
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
            Highlights this category on the homepage category explorer bar and discovery modules.
          </div>
        </div>
      </label>
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
      <a href="<?= url('/admin/categories') ?>" class="admin-btn admin-btn-outline">Cancel</a>
      <button type="submit" class="admin-btn admin-btn-primary" style="padding: 0.65rem 1.75rem; font-weight: 700;">
        Create Root Category 🚀
      </button>
    </div>
  </form>
</div>
<?php else: ?>

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
      <?= number_format($totalSubcategories) ?>
    </div>
    <div style="font-size: 0.75rem; color: var(--admin-text-secondary); margin-top: 0.2rem;">Attached child taxonomies</div>
  </div>

  <div class="admin-card" style="padding: 1.15rem; border-radius: 12px;">
    <div style="font-size: 0.75rem; color: var(--admin-text-muted); text-transform: uppercase; font-weight: 700;">Associated Entities</div>
    <div style="font-size: 1.5rem; font-weight: 800; color: #ec4899; margin-top: 0.25rem; font-family: var(--admin-font-mono);">
      <?= number_format($totalAssignedItems) ?>
    </div>
    <div style="font-size: 0.75rem; color: var(--admin-text-secondary); margin-top: 0.2rem;">Articles, tools, prompts, GPTs</div>
  </div>

  <div class="admin-card" style="padding: 1.15rem; border-radius: 12px;">
    <div style="font-size: 0.75rem; color: var(--admin-text-muted); text-transform: uppercase; font-weight: 700;">Homepage Featured</div>
    <div style="font-size: 1.5rem; font-weight: 800; color: #f59e0b; margin-top: 0.25rem; font-family: var(--admin-font-mono);">
      <?= number_format($featuredCount) ?>
    </div>
    <div style="font-size: 0.75rem; color: var(--admin-text-secondary); margin-top: 0.2rem;">Curated discovery badges</div>
  </div>
</div>

<!-- Scope Filter Bar -->
<div style="display: flex; gap: 0.5rem; border-bottom: 1px solid var(--admin-border-main); margin-bottom: 1.5rem; overflow-x: auto; padding-bottom: 0.25rem;">
  <a href="<?= url('/admin/categories?scope=all') ?>" class="ts-subtab-link <?= $currentScope === 'all' ? 'active' : '' ?>">
    🌐 All Scopes (<?= $scopeCounts['all'] ?>)
  </a>
  <a href="<?= url('/admin/categories?scope=prompt') ?>" class="ts-subtab-link <?= $currentScope === 'prompt' ? 'active' : '' ?>">
    ⚡ Prompts (<?= $scopeCounts['prompt'] ?>)
  </a>
  <a href="<?= url('/admin/categories?scope=ai_tool') ?>" class="ts-subtab-link <?= $currentScope === 'ai_tool' ? 'active' : '' ?>">
    🛠️ AI Tools (<?= $scopeCounts['ai_tool'] ?>)
  </a>
  <a href="<?= url('/admin/categories?scope=gpt_listing') ?>" class="ts-subtab-link <?= $currentScope === 'gpt_listing' ? 'active' : '' ?>">
    🤖 Custom GPTs (<?= $scopeCounts['gpt_listing'] ?>)
  </a>
  <a href="<?= url('/admin/categories?scope=article') ?>" class="ts-subtab-link <?= $currentScope === 'article' ? 'active' : '' ?>">
    📝 Articles (<?= $scopeCounts['article'] ?>)
  </a>
  <a href="<?= url('/admin/categories?scope=campaign') ?>" class="ts-subtab-link <?= $currentScope === 'campaign' ? 'active' : '' ?>">
    🏆 Challenges (<?= $scopeCounts['campaign'] ?>)
  </a>
</div>

<!-- Live Search Filter Toolbar -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
  <div style="position: relative; flex: 1; max-width: 420px;">
    <input type="text" id="categorySearchInput" oninput="filterCategoryTable()" placeholder="Search root categories by name or slug..." class="admin-input" style="padding-left: 2rem;">
    <i data-lucide="search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: var(--admin-text-muted);"></i>
  </div>

  <div style="font-size: 0.82rem; color: var(--admin-text-muted);" id="categoryRowCountText">
    Showing <?= count($categories) ?> root categories
  </div>
</div>

<!-- Root Categories Table -->
<div class="admin-table-wrap">
  <table class="admin-table" id="categoriesTable">
    <thead>
      <tr>
        <th style="min-width: 240px;">Category Specification</th>
        <th>Applicable Scope</th>
        <th>Visual Icon &amp; Color</th>
        <th>Subcategories</th>
        <th>Total Items</th>
        <th>Homepage</th>
        <th style="text-align: right; min-width: 200px;">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($categories)): ?>
        <?php foreach ($categories as $cat): ?>
          <?php
            $scope = $cat['applicable_to'] ?? 'all';
            $badgeClass = 'badge-secondary';
            if ($scope === 'prompt') $badgeClass = 'badge-cyan';
            elseif ($scope === 'ai_tool') $badgeClass = 'badge-success';
            elseif ($scope === 'gpt_listing') $badgeClass = 'badge-pink';
            elseif ($scope === 'article') $badgeClass = 'badge-warning';
            elseif ($scope === 'all') $badgeClass = 'badge-indigo';
          ?>
          <tr class="category-table-row" 
              data-name="<?= htmlspecialchars(strtolower($cat['name'])) ?>" 
              data-slug="<?= htmlspecialchars(strtolower($cat['slug'])) ?>" 
              data-scope="<?= htmlspecialchars($scope) ?>">
            <td>
              <div style="display: flex; align-items: center; gap: 0.65rem;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: <?= htmlspecialchars($cat['color_hex'] ?: '#6366f1') ?>20; border: 1px solid <?= htmlspecialchars($cat['color_hex'] ?: '#6366f1') ?>50; display: flex; align-items: center; justify-content: center; color: <?= htmlspecialchars($cat['color_hex'] ?: '#6366f1') ?>; flex-shrink: 0;">
                  <i data-lucide="<?= htmlspecialchars($cat['icon_name'] ?: 'folder') ?>" style="width: 15px; height: 15px;"></i>
                </div>
                <div>
                  <div style="font-weight: 700; color: var(--admin-text-primary); font-size: 0.95rem;">
                    <?= htmlspecialchars($cat['name']) ?>
                  </div>
                  <div style="font-size: 0.75rem; color: #38bdf8; font-family: var(--admin-font-mono);">
                    /<?= htmlspecialchars($cat['slug']) ?>
                  </div>
                  <?php if (!empty($cat['description'])): ?>
                    <div style="font-size: 0.75rem; color: var(--admin-text-muted); margin-top: 0.15rem; max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                      <?= htmlspecialchars($cat['description']) ?>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </td>

            <td>
              <span class="admin-pill-badge <?= $badgeClass ?>" style="font-size: 0.65rem;">
                <?= strtoupper(htmlspecialchars($scope)) ?>
              </span>
            </td>

            <td>
              <div style="display: inline-flex; align-items: center; gap: 0.45rem;">
                <span style="display: inline-block; width: 14px; height: 14px; border-radius: 50%; background: <?= htmlspecialchars($cat['color_hex'] ?: '#6366f1') ?>; border: 1px solid rgba(255,255,255,0.2);"></span>
                <span style="font-size: 0.78rem; font-family: var(--admin-font-mono); color: var(--admin-text-secondary);">
                  <?= htmlspecialchars($cat['icon_name'] ?: 'folder') ?>
                </span>
              </div>
            </td>

            <td>
              <a href="<?= url('/admin/subcategories?parent_id=' . $cat['id']) ?>" style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
                <span class="admin-pill-badge badge-cyan" style="font-size: 0.72rem; padding: 0.2rem 0.55rem; font-weight: 700;">
                  <?= number_format((int)($cat['subcategories_count'] ?? 0)) ?> subcategories
                </span>
              </a>
            </td>

            <td>
              <strong style="color: #38bdf8; font-family: var(--admin-font-mono); font-size: 0.9rem;">
                <?= number_format((int)($cat['total_items'] ?? 0)) ?>
              </strong>
            </td>

            <td>
              <?php if (!empty($cat['is_featured_home'])): ?>
                <span class="admin-pill-badge badge-warning" style="font-size: 0.65rem;">Featured ⭐</span>
              <?php else: ?>
                <span style="color: var(--admin-text-muted); font-size: 0.75rem;">Standard</span>
              <?php endif; ?>
            </td>

            <td style="text-align: right;">
              <div style="display: inline-flex; gap: 0.35rem; align-items: center;">
                <!-- Fast action: Add Subcategory under this root -->
                <a href="<?= url('/admin/subcategories?tab=add&parent_id=' . $cat['id']) ?>" 
                   class="admin-btn admin-btn-outline admin-btn-sm" 
                   style="color: #10b981; border-color: rgba(16,185,129,0.35); font-size: 0.72rem; padding: 0.25rem 0.5rem;" 
                   title="Add Subcategory directly under this category">
                  <i data-lucide="plus" style="width: 11px; height: 11px;"></i>
                  <span>+ Sub</span>
                </a>

                <button type="button" 
                        onclick='editCategory(<?= json_encode($cat, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' 
                        class="admin-btn admin-btn-outline admin-btn-sm" 
                        style="font-size: 0.72rem; padding: 0.25rem 0.5rem;">
                  Edit
                </button>

                <form action="<?= url('/admin/categories/delete/' . $cat['id']) ?>" method="POST" onsubmit="return confirm('Delete category \'<?= htmlspecialchars(addslashes($cat['name'])) ?>\'? Subcategories and content associations will be unlinked.');" style="margin: 0;">
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
            No root categories found. Create your first category using the button above!
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<!-- =========================================================================
     FAST MODAL: EDIT & QUICK-ADD ROOT CATEGORY (Optimized via AdminModal)
     ========================================================================= -->
<div id="categoryModal" class="ts-modal-overlay">
  <div class="ts-modal-card">
    <div class="ts-modal-header">
      <div class="ts-modal-title">
        <i data-lucide="folder" style="width: 20px; height: 20px; color: var(--admin-accent-primary);"></i>
        <span id="catModalHeaderTitle">Category Specification</span>
      </div>
      <button type="button" onclick="AdminModal.close('categoryModal')" class="ts-modal-close-btn">&times;</button>
    </div>

    <form id="catModalForm" action="<?= url('/admin/categories/save') ?>" method="POST">
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

        <!-- Row 2: Applicable Scope & Sort Order -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
              Applicable Scope <span style="color: #ef4444;">*</span>
            </label>
            <select name="applicable_to" id="modal_cat_scope" class="admin-input" required>
              <option value="all">🌐 Global (All Sections &amp; Directories)</option>
              <option value="prompt">⚡ Prompt Blueprints &amp; Images Only</option>
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
            <input type="number" name="sort_order" id="modal_cat_sort" value="0" class="admin-input">
          </div>
        </div>

        <!-- Row 3: Icon & Color -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">
              Lucide Icon Name
            </label>
            <div style="display: flex; gap: 0.5rem; align-items: center;">
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
            <div style="display: flex; gap: 0.5rem; align-items: center;">
              <input type="color" name="color_hex" id="modal_cat_color" value="#6366f1" style="height: 38px; width: 44px; padding: 2px; border-radius: 6px; border: 1px solid var(--admin-border-main); background: var(--admin-bg-surface-elevated); cursor: pointer;">
              <div style="display: flex; gap: 0.35rem; align-items: center;">
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

<script>
function autoGenerateCategorySlug(name, targetId) {
    var target = document.getElementById(targetId);
    if (!target) return;
    target.value = name.toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-|-$/g, '');
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
</script>

<?php require __DIR__ . '/../../layouts/admin_footer.php'; ?>
