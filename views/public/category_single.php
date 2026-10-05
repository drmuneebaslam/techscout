<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 2rem; margin-bottom: 5rem; max-width: 1200px;">

  <!-- Breadcrumbs -->
  <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: var(--ts-text-muted); margin-bottom: 1.5rem; flex-wrap: wrap;">
    <a href="<?= url('/') ?>" style="color: var(--ts-text-secondary); text-decoration: none;">Home</a>
    <span>›</span>
    <a href="<?= url('/categories') ?>" style="color: var(--ts-text-secondary); text-decoration: none;">Categories</a>
    <?php if (!empty($category['parent_name'])): ?>
      <span>›</span>
      <a href="<?= url('/category/' . $category['parent_slug']) ?>" style="color: var(--ts-text-secondary); text-decoration: none;">
        <?= htmlspecialchars($category['parent_name']) ?>
      </a>
    <?php endif; ?>
    <span>›</span>
    <span style="color: var(--ts-text-primary); font-weight: 600;"><?= htmlspecialchars($category['name']) ?></span>
  </nav>

  <!-- Category Hero Card -->
  <div class="ts-card" style="background: var(--ts-bg-surface); border-color: rgba(99,102,241,0.25); padding: 2rem; margin-bottom: 2rem; border-radius: 16px; position: relative;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1.5rem;">
      <div style="display: flex; align-items: flex-start; gap: 1.25rem; max-width: 780px;">
        <div style="width: 56px; height: 56px; border-radius: 14px; background: <?= htmlspecialchars($category['color_hex'] ?: '#6366f1') ?>22; border: 1px solid <?= htmlspecialchars($category['color_hex'] ?: '#6366f1') ?>44; display: flex; align-items: center; justify-content: center; color: <?= htmlspecialchars($category['color_hex'] ?: '#6366f1') ?>; flex-shrink: 0;">
          <i data-lucide="<?= htmlspecialchars($category['icon_name'] ?: 'folder') ?>" style="width: 28px; height: 28px;"></i>
        </div>
        <div>
          <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.35rem; flex-wrap: wrap;">
            <h1 style="font-size: 1.85rem; color: var(--ts-text-primary); margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800; line-height: 1.2;">
              <?= htmlspecialchars($category['name']) ?>
            </h1>
            <?php if (!empty($category['parent_name'])): ?>
              <span class="admin-pill-badge badge-blue" style="font-size: 0.72rem;">
                Subcategory of <?= htmlspecialchars($category['parent_name']) ?>
              </span>
            <?php else: ?>
              <span class="admin-pill-badge badge-cyan" style="font-size: 0.72rem;">Root Category Pillar</span>
            <?php endif; ?>
          </div>

          <p style="font-size: 0.95rem; color: var(--ts-text-secondary); line-height: 1.6; margin: 0.4rem 0 0.75rem 0;">
            <?= htmlspecialchars($category['description'] ?: 'Curated technical resources, blueprints, and architecture benchmarks categorized under ' . $category['name'] . '.') ?>
          </p>

          <div style="display: flex; align-items: center; gap: 1rem; font-size: 0.82rem; color: var(--ts-text-muted);">
            <span><strong style="color: var(--ts-text-primary);" class="follower-count-val"><?= number_format($followerCount) ?></strong> Subscribers</span>
            <span>&bull;</span>
            <span><strong style="color: var(--ts-text-primary);"><?= count($items) ?></strong> Published Items</span>
          </div>
        </div>
      </div>

      <!-- Subscribe Button -->
      <div>
        <button type="button" id="singleCategorySubBtn" class="ts-btn ts-btn-md <?= $isSubscribed ? 'ts-btn-primary' : 'ts-btn-outline' ?>" onclick="toggleSingleSubscription(<?= $category['id'] ?>)" style="padding: 0.6rem 1.4rem; font-weight: 700; border-radius: 10px;">
          <i data-lucide="<?= $isSubscribed ? 'check' : 'bell' ?>" style="width: 16px; height: 16px;"></i>
          <span id="singleSubBtnText"><?= $isSubscribed ? 'Following Category' : 'Follow Category' ?></span>
        </button>
      </div>
    </div>

    <!-- Sibling / Child Subcategories Bar -->
    <?php if (!empty($subcategories)): ?>
      <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--ts-border); display: flex; align-items: center; gap: 0.5rem; overflow-x: auto;">
        <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--ts-text-muted); font-weight: 700; white-space: nowrap;">
          <?= empty($category['parent_id']) ? 'Subcategories:' : 'Related Subcategories:' ?>
        </span>
        <?php foreach ($subcategories as $sub): ?>
          <a href="<?= url('/category/' . $sub['slug']) ?>" class="ts-btn ts-btn-sm ts-btn-outline" style="white-space: nowrap; font-size: 0.78rem; padding: 0.25rem 0.65rem;">
            <i data-lucide="<?= htmlspecialchars($sub['icon_name'] ?: 'tag') ?>" style="width: 11px; height: 11px; color: <?= htmlspecialchars($sub['color_hex'] ?: '#38bdf8') ?>;"></i>
            <?= htmlspecialchars($sub['name']) ?>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Entities Grid -->
  <?php if (empty($items)): ?>
    <div class="ts-card" style="padding: 3rem; text-align: center; border-radius: 14px;">
      <i data-lucide="inbox" style="width: 44px; height: 44px; color: var(--ts-text-muted); margin-bottom: 0.75rem;"></i>
      <h3 style="color: var(--ts-text-primary); margin: 0 0 0.35rem 0;">No resources found</h3>
      <p style="color: var(--ts-text-secondary); font-size: 0.9rem; margin: 0;">
        No published content has been categorized under this section for the selected entity scope.
      </p>
      <?php if ($currentEntity !== 'all'): ?>
        <a href="<?= url('/category/' . $category['slug'] . '?entity=all') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="margin-top: 1rem;">
          View All Entities in <?= htmlspecialchars($category['name']) ?>
        </a>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.5rem;">
      <?php foreach ($items as $item): ?>
        <?php
          $itemUrl = url('/' . ($item['content_type'] === 'prompt' ? 'prompt' : ($item['content_type'] === 'article' ? 'blog' : $item['content_type'])) . '/' . $item['slug']);
          $badge = [
            'article' => ['Article', 'badge-amber'],
            'prompt' => ['Prompt', 'badge-purple'],
            'image_prompt' => ['Image Prompt', 'badge-rose'],
            'ai_tool' => ['AI Tool', 'badge-blue'],
            'gpt_listing' => ['Custom GPT', 'badge-emerald'],
            'campaign' => ['Challenge', 'badge-cyan']
          ][$item['content_type']] ?? ['Resource', 'badge-cyan'];
        ?>
        <div class="ts-card" style="display: flex; flex-direction: column; padding: 1.5rem; border-radius: 12px; background: var(--ts-bg-surface); border: 1px solid var(--ts-border);">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
            <span class="admin-pill-badge <?= $badge[1] ?>" style="font-size: 0.7rem;">
              <?= $badge[0] ?>
            </span>
            <?php if (!empty($item['category_is_primary'])): ?>
              <span style="font-size: 0.7rem; color: #10b981; font-weight: 700;">★ Primary Category</span>
            <?php else: ?>
              <span style="font-size: 0.7rem; color: var(--ts-text-muted);">Secondary Tag</span>
            <?php endif; ?>
          </div>

          <h3 style="font-size: 1.15rem; color: var(--ts-text-primary); margin: 0 0 0.5rem 0; line-height: 1.35;">
            <a href="<?= $itemUrl ?>" style="color: var(--ts-text-primary); text-decoration: none;">
              <?= htmlspecialchars($item['title']) ?>
            </a>
          </h3>

          <p style="font-size: 0.85rem; color: var(--ts-text-secondary); line-height: 1.5; margin: 0 0 1rem 0; flex: 1;">
            <?= htmlspecialchars($item['summary'] ?: substr(strip_tags($item['body_html'] ?? ''), 0, 120) . '...') ?>
          </p>

          <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.78rem; color: var(--ts-text-muted); border-top: 1px solid var(--ts-border); padding-top: 0.75rem;">
            <span>By <?= htmlspecialchars($item['author_name'] ?? 'TechScout') ?></span>
            <a href="<?= $itemUrl ?>" style="color: var(--admin-accent-primary); text-decoration: none; font-weight: 600;">
              Open <?= $badge[0] ?> &rarr;
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>

<script>
function toggleSingleSubscription(catId) {
    var btn = document.getElementById('singleCategorySubBtn');
    var btnText = document.getElementById('singleSubBtnText');

    fetch('<?= url('/category/subscribe/') ?>' + catId, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(function(res) {
        if (res.status === 401) {
            window.location.href = '<?= url("/auth/login") ?>';
            return null;
        }
        return res.json();
    })
    .then(function(data) {
        if (!data) return;
        if (!data.success) {
            alert(data.error || 'Could not update subscription');
            return;
        }

        var isSubbed = data.is_subscribed;
        var countEl = document.querySelector('.follower-count-val');
        if (countEl) {
            countEl.innerText = data.follower_count;
        }

        if (isSubbed) {
            btn.className = 'ts-btn ts-btn-md ts-btn-primary';
            btnText.innerText = 'Following Category';
            btn.querySelector('i').setAttribute('data-lucide', 'check');
        } else {
            btn.className = 'ts-btn ts-btn-md ts-btn-outline';
            btnText.innerText = 'Follow Category';
            btn.querySelector('i').setAttribute('data-lucide', 'bell');
        }

        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons({ root: btn });
        }
    })
    .catch(function(err) {
        console.error('Subscription error:', err);
    });
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
