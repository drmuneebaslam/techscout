<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 2.5rem; margin-bottom: 5rem; max-width: 1200px;">

  <!-- Hero Header -->
  <div class="ts-card" style="background: radial-gradient(circle at 10% 20%, rgba(99,102,241,0.12), transparent 45%), var(--ts-bg-surface); border-color: rgba(99,102,241,0.3); padding: 2.5rem; margin-bottom: 2rem; position: relative; overflow: hidden; border-radius: 16px;">
    <div style="max-width: 820px; position: relative; z-index: 2;">
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
        <span class="ts-badge ts-badge-cyan">Architecture &amp; Taxonomy Hub</span>
        <span style="font-size: 0.8rem; color: var(--ts-text-secondary);"><?= count($rootCategories) ?> Root Pillars &bull; <?= count($subsByParent, COUNT_RECURSIVE) - count($subsByParent) ?> Subcategories</span>
      </div>
      <h1 style="font-size: 2.4rem; color: var(--ts-text-primary); margin-bottom: 0.75rem; line-height: 1.2; font-family: 'Outfit', sans-serif; font-weight: 800;">
        Explore by <span style="background: linear-gradient(135deg, #38bdf8, #6366f1); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Categories &amp; Subcategories</span>
      </h1>
      <p style="font-size: 1.05rem; color: var(--ts-text-secondary); line-height: 1.6; margin-bottom: 1.5rem;">
        Subscribe to root categories or specialized subcategories to personalize your engineering radar. Following any category curates technical deep dives, prompts, tools, and GPT blueprints into your personalized discovery tracks.
      </p>

      <!-- Instant Live Search Box -->
      <div style="position: relative; max-width: 560px;">
        <i data-lucide="search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: var(--ts-text-secondary);"></i>
        <input type="text" id="categoryHubSearch" placeholder="Search across all categories & subcategories (e.g. LLMs, Next.js, Midjourney, Agents)..." value="<?= htmlspecialchars($searchQuery) ?>" style="width: 100%; background: var(--ts-bg-base); border: 1px solid var(--ts-border); border-radius: var(--ts-radius-md); padding: 0.85rem 1rem 0.85rem 2.75rem; color: var(--ts-text-primary); font-size: 0.95rem;" oninput="filterCategoriesLive(this.value)">
      </div>
    </div>
  </div>

  <!-- Scope Filter Ribbon -->
  <div style="display: flex; gap: 0.5rem; margin-bottom: 2rem; overflow-x: auto; padding-bottom: 0.5rem;">
    <a href="<?= url('/categories?entity=all') ?>" class="ts-btn ts-btn-sm <?= $currentScope === 'all' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="white-space: nowrap;">
      🌐 All Entities
    </a>
    <a href="<?= url('/categories?entity=article') ?>" class="ts-btn ts-btn-sm <?= $currentScope === 'article' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="white-space: nowrap;">
      📝 Technical Articles
    </a>
    <a href="<?= url('/categories?entity=prompt') ?>" class="ts-btn ts-btn-sm <?= $currentScope === 'prompt' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="white-space: nowrap;">
      ⚡ Text Prompts
    </a>
    <a href="<?= url('/categories?entity=image_prompt') ?>" class="ts-btn ts-btn-sm <?= $currentScope === 'image_prompt' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="white-space: nowrap;">
      🎨 Image Prompts
    </a>
    <a href="<?= url('/categories?entity=ai_tool') ?>" class="ts-btn ts-btn-sm <?= $currentScope === 'ai_tool' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="white-space: nowrap;">
      🛠️ AI Tools
    </a>
    <a href="<?= url('/categories?entity=gpt_listing') ?>" class="ts-btn ts-btn-sm <?= $currentScope === 'gpt_listing' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="white-space: nowrap;">
      🤖 Custom GPTs
    </a>
    <a href="<?= url('/categories?entity=campaign') ?>" class="ts-btn ts-btn-sm <?= $currentScope === 'campaign' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="white-space: nowrap;">
      🏆 Challenges
    </a>
  </div>

  <!-- Categories Grid -->
  <div id="categoriesGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap: 1.5rem;">
    <?php if (empty($rootCategories)): ?>
      <div class="ts-card" style="grid-column: 1 / -1; padding: 3rem; text-align: center;">
        <i data-lucide="folder-search" style="width: 44px; height: 44px; color: var(--ts-text-muted); margin-bottom: 0.75rem;"></i>
        <h3 style="color: var(--ts-text-primary);">No categories match your filter</h3>
        <p style="color: var(--ts-text-secondary); font-size: 0.9rem;">Try selecting a different entity scope or clear your search query.</p>
        <a href="<?= url('/categories') ?>" class="ts-btn ts-btn-primary ts-btn-sm" style="margin-top: 1rem;">View All Categories</a>
      </div>
    <?php else: ?>
      <?php foreach ($rootCategories as $root): ?>
        <?php 
          $subs = $subsByParent[$root['id']] ?? [];
          $isSubbed = in_array((int)$root['id'], $subscribedCatIds, true);
          $followers = $subscriberCounts[$root['id']] ?? 0;
        ?>
        <div class="category-pillar-card ts-card" id="cat-card-<?= $root['id'] ?>" style="background: var(--ts-bg-surface); border-color: rgba(99,102,241,0.2); padding: 1.75rem; display: flex; flex-direction: column; border-radius: 14px;" data-cat-name="<?= strtolower(htmlspecialchars($root['name'])) ?>" data-cat-desc="<?= strtolower(htmlspecialchars($root['description'] ?? '')) ?>">
          
          <!-- Category Header -->
          <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; gap: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
              <div style="width: 44px; height: 44px; border-radius: 10px; background: <?= htmlspecialchars($root['color_hex'] ?: '#6366f1') ?>22; border: 1px solid <?= htmlspecialchars($root['color_hex'] ?: '#6366f1') ?>44; display: flex; align-items: center; justify-content: center; color: <?= htmlspecialchars($root['color_hex'] ?: '#6366f1') ?>;">
                <i data-lucide="<?= htmlspecialchars($root['icon_name'] ?: 'folder') ?>" style="width: 22px; height: 22px;"></i>
              </div>
              <div>
                <h2 style="font-size: 1.2rem; color: var(--ts-text-primary); margin: 0; line-height: 1.2; font-family: 'Outfit', sans-serif;">
                  <a href="<?= url('/category/' . $root['slug']) ?>" style="color: var(--ts-text-primary); text-decoration: none;">
                    <?= htmlspecialchars($root['name']) ?>
                  </a>
                </h2>
                <span style="font-size: 0.75rem; color: var(--ts-text-secondary);">
                  <?= count($subs) ?> Subcategories &bull; <span class="follower-count-<?= $root['id'] ?>"><?= number_format($followers) ?></span> subscribers
                </span>
              </div>
            </div>

            <!-- Subscribe Button -->
            <button type="button" class="ts-btn ts-btn-sm <?= $isSubbed ? 'ts-btn-primary' : 'ts-btn-outline' ?> cat-sub-btn" data-cat-id="<?= $root['id'] ?>" onclick="toggleCategorySubscription(<?= $root['id'] ?>, this)" style="padding: 0.28rem 0.7rem; font-size: 0.76rem; border-radius: 8px;">
              <i data-lucide="<?= $isSubbed ? 'check' : 'bell' ?>" style="width: 12px; height: 12px;"></i>
              <span><?= $isSubbed ? 'Following' : 'Follow' ?></span>
            </button>
          </div>

          <!-- Description -->
          <p style="font-size: 0.86rem; color: var(--ts-text-secondary); line-height: 1.5; margin-bottom: 1.25rem; flex: 0 0 auto;">
            <?= htmlspecialchars($root['description'] ?: 'Foundational engineering pillar, deep architectural patterns, prompts, and tool benchmarks.') ?>
          </p>

          <!-- Nested Subcategories Cloud -->
          <div class="subcategory-pills-wrap" style="display: flex; flex-wrap: wrap; gap: 0.45rem; margin-top: auto; border-top: 1px solid var(--ts-border); padding-top: 1rem;">
            <?php if (!empty($subs)): ?>
              <?php foreach ($subs as $sub): ?>
                <?php $isChildSubbed = in_array((int)$sub['id'], $subscribedCatIds, true); ?>
                <div class="subcat-pill-item" data-sub-name="<?= strtolower(htmlspecialchars($sub['name'])) ?>" style="display: inline-flex; align-items: center; gap: 0.35rem; background: var(--ts-bg-base); border: 1px solid var(--ts-border); border-radius: 6px; padding: 0.3rem 0.55rem; font-size: 0.78rem;">
                  <a href="<?= url('/category/' . $sub['slug']) ?>" style="color: var(--ts-text-secondary); text-decoration: none; display: flex; align-items: center; gap: 0.3rem;">
                    <i data-lucide="<?= htmlspecialchars($sub['icon_name'] ?: 'tag') ?>" style="width: 11px; height: 11px; color: <?= htmlspecialchars($sub['color_hex'] ?: '#38bdf8') ?>;"></i>
                    <span><?= htmlspecialchars($sub['name']) ?></span>
                  </a>
                  <button type="button" title="Subscribe to subcategory" onclick="toggleCategorySubscription(<?= $sub['id'] ?>, this)" style="background: transparent; border: none; cursor: pointer; padding: 0; color: <?= $isChildSubbed ? '#10b981' : 'var(--ts-text-muted)' ?>; display: inline-flex; align-items: center;">
                    <i data-lucide="<?= $isChildSubbed ? 'check-circle-2' : 'plus' ?>" style="width: 12px; height: 12px;"></i>
                  </button>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <span style="font-size: 0.78rem; color: var(--ts-text-muted);">No subcategories attached yet.</span>
            <?php endif; ?>
          </div>

        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

</div>

<script>
function filterCategoriesLive(term) {
    term = term.toLowerCase().trim();
    var cards = document.querySelectorAll('.category-pillar-card');

    cards.forEach(function(card) {
        var name = card.getAttribute('data-cat-name') || '';
        var desc = card.getAttribute('data-cat-desc') || '';
        var pills = card.querySelectorAll('.subcat-pill-item');
        var pillMatch = false;

        pills.forEach(function(pill) {
            var subName = pill.getAttribute('data-sub-name') || '';
            if (!term || subName.indexOf(term) !== -1) {
                pill.style.display = '';
                pillMatch = true;
            } else {
                pill.style.display = 'none';
            }
        });

        if (!term || name.indexOf(term) !== -1 || desc.indexOf(term) !== -1 || pillMatch) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

function toggleCategorySubscription(catId, btn) {
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
        var countEls = document.querySelectorAll('.follower-count-' + catId);
        countEls.forEach(function(el) {
            el.innerText = data.follower_count;
        });

        // Update button UI
        if (btn.classList.contains('cat-sub-btn')) {
            if (isSubbed) {
                btn.className = 'ts-btn ts-btn-sm ts-btn-primary cat-sub-btn';
                btn.innerHTML = '<i data-lucide="check" style="width: 12px; height: 12px;"></i><span>Following</span>';
            } else {
                btn.className = 'ts-btn ts-btn-sm ts-btn-outline cat-sub-btn';
                btn.innerHTML = '<i data-lucide="bell" style="width: 12px; height: 12px;"></i><span>Follow</span>';
            }
        } else {
            // Small pill button
            if (isSubbed) {
                btn.style.color = '#10b981';
                btn.innerHTML = '<i data-lucide="check-circle-2" style="width: 12px; height: 12px;"></i>';
            } else {
                btn.style.color = 'var(--ts-text-muted)';
                btn.innerHTML = '<i data-lucide="plus" style="width: 12px; height: 12px;"></i>';
            }
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
