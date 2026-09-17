<?php
/**
 * Compact & Sleek Category Explorer Component
 * Clean single-row design with quick topics & compact dropdown for 100+ categories
 *
 * @var string $entityRoute (e.g. 'gpts', 'tools', 'prompts', 'blog')
 * @var array $categories
 * @var string|null $currentCategory
 * @var array|null $categoryInfo
 */

$entityRoute = $entityRoute ?? 'blog';
$currentCategory = $currentCategory ?? null;
$categoryInfo = $categoryInfo ?? null;

if (!isset($categories) || !is_array($categories)) {
    $scopeMap = ['blog' => 'article', 'gpts' => 'gpt_listing', 'tools' => 'ai_tool', 'prompts' => 'prompt'];
    $scope = $scopeMap[$entityRoute] ?? 'all';
    $categories = \App\Core\Database::query("SELECT * FROM categories WHERE applicable_to IN ('all', ?) ORDER BY sort_order ASC, name ASC", [$scope]);
}

// Featured/Top categories to show inline (max 7)
$featuredCats = array_slice($categories, 0, 7);
?>

<div class="category-explorer-widget" style="margin-top: 1rem; margin-bottom: 2rem;">
  <!-- Active Filter Notification Bar (if filtered) -->
  <?php if (!empty($currentCategory)): ?>
    <div style="display: inline-flex; align-items: center; gap: 0.6rem; background: rgba(99,102,241,0.12); border: 1px solid rgba(99,102,241,0.3); padding: 0.35rem 0.9rem; border-radius: 9999px; margin-bottom: 1rem;">
      <span style="font-size: 0.8rem; color: #a5b4fc;">Filtered Taxonomy:</span>
      <strong style="color: #f8fafc; font-size: 0.85rem;">
        <?= htmlspecialchars($categoryInfo['name'] ?? ucwords(str_replace('-', ' ', $currentCategory))) ?>
      </strong>
      <a href="<?= url('/' . $entityRoute) ?>" class="ts-badge ts-badge-pink" style="text-decoration: none; padding: 0.1rem 0.4rem; font-size: 0.7rem; cursor: pointer;" title="Clear filter">
        Clear ✕
      </a>
    </div>
  <?php endif; ?>

  <!-- Compact Control Bar -->
  <div style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; flex-wrap: wrap; max-width: 980px; margin: 0 auto;">
    
    <!-- All Link -->
    <a href="<?= url('/' . $entityRoute) ?>" class="ts-badge <?= empty($currentCategory) ? 'ts-badge-pink' : '' ?>" style="padding: 0.4rem 0.9rem; text-decoration: none; font-size: 0.82rem; cursor: pointer; border-radius: 9999px;">
      All
    </a>

    <!-- Top Featured Categories Inline (Max 6) -->
    <?php foreach ($featuredCats as $cat): ?>
      <?php 
        $isActive = ($currentCategory ?? '') === $cat['slug'];
        $cleanCatUrl = url('/' . $entityRoute . '/category/' . $cat['slug']);
      ?>
      <a href="<?= $cleanCatUrl ?>" class="ts-badge <?= $isActive ? 'ts-badge-pink' : 'ts-badge-cyan' ?>" style="padding: 0.4rem 0.85rem; text-decoration: none; font-size: 0.82rem; cursor: pointer; border-radius: 9999px; transition: all 0.15s ease;">
        <?php if (!empty($cat['icon_name'])): ?>
          <i data-lucide="<?= htmlspecialchars($cat['icon_name']) ?>" style="width: 12px; height: 12px; display: inline; vertical-align: middle;"></i>
        <?php endif; ?>
        <?= htmlspecialchars($cat['name']) ?>
      </a>
    <?php endforeach; ?>

    <!-- Category Dropdown Selector for 100+ categories -->
    <div style="position: relative; display: inline-block;">
      <select onchange="if(this.value) window.location.href=this.value;" style="background: #05070b; border: 1px solid rgba(148,163,184,0.25); border-radius: 9999px; padding: 0.38rem 0.85rem; color: #cbd5e1; font-size: 0.8rem; outline: none; cursor: pointer;">
        <option value="">More Categories (<?= count($categories) ?>+)...</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= url('/' . $entityRoute . '/category/' . $cat['slug']) ?>" <?= ($currentCategory ?? '') === $cat['slug'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- All Categories Hub Button -->
    <a href="<?= url('/categories') ?>" class="ts-badge" style="background: rgba(99,102,241,0.15); border: 1px solid rgba(99,102,241,0.3); color: #a5b4fc; text-decoration: none; padding: 0.4rem 0.9rem; font-size: 0.82rem; border-radius: 9999px; display: inline-flex; align-items: center; gap: 0.35rem;">
      <i data-lucide="layers" style="width: 13px; height: 13px;"></i>
      <span>All Categories →</span>
    </a>

  </div>
</div>
