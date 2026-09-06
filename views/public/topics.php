<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 2.5rem; margin-bottom: 5rem; max-width: 1200px;">

  <!-- Hero Header -->
  <div class="ts-card" style="background: radial-gradient(circle at 10% 20%, rgba(99,102,241,0.12), transparent 45%), var(--ts-bg-surface); border-color: rgba(99,102,241,0.3); padding: 2.5rem; margin-bottom: 2rem; position: relative; overflow: hidden;">
    <div style="max-width: 780px; position: relative; z-index: 2;">
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
        <span class="ts-badge ts-badge-pink">Domain Taxonomy</span>
        <span style="font-size: 0.8rem; color: var(--ts-text-secondary);"><?= $totalCategories ?>+ Curated Engineering Categories</span>
      </div>
      <h1 style="font-size: 2.4rem; color: var(--ts-text-primary); margin-bottom: 0.75rem; line-height: 1.2;">
        Explore by <span style="background: linear-gradient(135deg, #ec4899, #6366f1); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Topics & Specialties</span>
      </h1>
      <p style="font-size: 1.05rem; color: var(--ts-text-secondary); line-height: 1.6; margin-bottom: 1.5rem;">
        Subscribe to parent engineering topics to personalize your reading radar. Following a topic automatically curates all relevant subcategories, deep dives, prompts, and architectural benchmarks into your <strong>For You Feed</strong>.
      </p>

      <!-- Instant Live Search Box -->
      <div style="position: relative; max-width: 540px;">
        <i data-lucide="search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: var(--ts-text-secondary);"></i>
        <input type="text" id="categoryHubSearch" placeholder="Search across 100+ categories (e.g. RAG, Kubernetes, PHP, OWASP)..." value="<?= htmlspecialchars($searchQuery) ?>" style="width: 100%; background: var(--ts-bg-base); border: 1px solid var(--ts-border); border-radius: var(--ts-radius-md); padding: 0.85rem 1rem 0.85rem 2.75rem; color: var(--ts-text-primary); font-size: 0.95rem;" oninput="filterCategoriesLive(this.value)">
      </div>
    </div>
  </div>

  <!-- Topic Quick Filter Ribbon -->
  <div style="display: flex; gap: 0.5rem; margin-bottom: 2rem; overflow-x: auto; padding-bottom: 0.5rem;" id="topicPillFilters">
    <button class="ts-btn ts-btn-sm ts-btn-primary" onclick="filterByTopicTab('all', this)">All Domains (<?= count($topics) ?>)</button>
    <?php foreach ($topics as $t): ?>
      <button class="ts-btn ts-btn-sm ts-btn-outline" style="white-space: nowrap;" onclick="filterByTopicTab('topic-card-<?= $t['id'] ?>', this)">
        <i data-lucide="<?= htmlspecialchars($t['icon_name'] ?: 'layers') ?>" style="width: 13px; height: 13px; color: <?= htmlspecialchars($t['color_hex'] ?: '#6366f1') ?>;"></i>
        <?= htmlspecialchars($t['name']) ?>
      </button>
    <?php endforeach; ?>
  </div>

  <!-- Topics & Categories Grid -->
  <div id="topicsGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap: 1.5rem;">
    <?php foreach ($grouped as $topicId => $group): ?>
      <?php 
        $topic = $group['topic'];
        $categories = $group['categories'];
        $isSubbed = !empty($group['is_subscribed']);
        $catCount = count($categories);
      ?>
      <div class="topic-group-card ts-card" id="topic-card-<?= $topic['id'] ?>" style="background: var(--ts-bg-surface); border-color: rgba(99,102,241,0.2); padding: 1.75rem; display: flex; flex-direction: column;" data-topic-name="<?= strtolower(htmlspecialchars($topic['name'])) ?>" data-topic-desc="<?= strtolower(htmlspecialchars($topic['description'] ?? '')) ?>">
        
        <!-- Topic Header Row -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; gap: 0.75rem;">
          <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(99,102,241,0.12); border: 1px solid <?= htmlspecialchars($topic['color_hex'] ?: '#6366f1') ?>33; display: flex; align-items: center; justify-content: center; color: <?= htmlspecialchars($topic['color_hex'] ?: '#6366f1') ?>;">
              <i data-lucide="<?= htmlspecialchars($topic['icon_name'] ?: 'layers') ?>" style="width: 22px; height: 22px;"></i>
            </div>
            <div>
              <h2 style="font-size: 1.2rem; color: var(--ts-text-primary); margin: 0; line-height: 1.2;">
                <a href="<?= url('/topic/' . $topic['slug']) ?>" style="color: var(--ts-text-primary); text-decoration: none;">
                  <?= htmlspecialchars($topic['name']) ?>
                </a>
              </h2>
              <span style="font-size: 0.75rem; color: var(--ts-text-secondary);"><?= $catCount ?> Categories &bull; <?= number_format($topic['follower_count'] ?? 0) ?> followers</span>
            </div>
          </div>

          <!-- Subscribe to Topic Button -->
          <button type="button" class="ts-btn ts-btn-sm <?= $isSubbed ? 'ts-btn-primary' : 'ts-btn-outline' ?> sub-btn" data-type="topic" data-id="<?= $topic['id'] ?>" onclick="toggleSubscription('topic', <?= $topic['id'] ?>, this)" style="padding: 0.25rem 0.65rem; font-size: 0.75rem;">
            <i data-lucide="<?= $isSubbed ? 'check' : 'plus' ?>" style="width: 12px; height: 12px;"></i>
            <span><?= $isSubbed ? 'Following' : 'Follow' ?></span>
          </button>
        </div>

        <p style="font-size: 0.85rem; color: var(--ts-text-secondary); line-height: 1.5; margin-bottom: 1.25rem; flex: 0 0 auto;">
          <?= htmlspecialchars($topic['description'] ?? 'Curated engineering articles, architectures, prompts, and tool benchmarks.') ?>
        </p>

        <!-- Categories Cloud in Topic -->
        <div class="category-pills-wrap" style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: auto; border-top: 1px solid var(--ts-border); padding-top: 1rem;">
          <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $cat): ?>
              <div class="cat-pill-item" data-cat-name="<?= strtolower(htmlspecialchars($cat['name'])) ?>" data-cat-desc="<?= strtolower(htmlspecialchars($cat['description'] ?? '')) ?>" style="display: inline-flex; align-items: center; gap: 0.35rem; background: var(--ts-bg-base); border: 1px solid var(--ts-border); border-radius: 6px; padding: 0.35rem 0.6rem; font-size: 0.8rem; transition: border-color 0.2s;">
                <a href="<?= url('/blog/category/' . $cat['slug']) ?>" style="color: var(--ts-text-secondary); text-decoration: none; display: flex; align-items: center; gap: 0.3rem;">
                  <i data-lucide="<?= htmlspecialchars($cat['icon_name'] ?: 'folder') ?>" style="width: 12px; height: 12px; color: <?= htmlspecialchars($cat['color_hex'] ?: '#6366f1') ?>;"></i>
                  <span><?= htmlspecialchars($cat['name']) ?></span>
                </a>
                <span style="font-size: 0.7rem; color: var(--ts-text-tertiary); margin-left: 0.15rem;">(<?= $cat['content_count'] ?? 0 ?>)</span>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <span style="font-size: 0.8rem; color: #64748b;">No direct subcategories mapped yet.</span>
          <?php endif; ?>
        </div>

      </div>
    <?php endforeach; ?>
  </div>

</div>

<script>
// Real-time live search across topics and 100+ categories
function filterCategoriesLive(query) {
  const q = query.trim().toLowerCase();
  const topicCards = document.querySelectorAll('.topic-group-card');

  topicCards.forEach(card => {
    const topicName = card.getAttribute('data-topic-name') || '';
    const topicDesc = card.getAttribute('data-topic-desc') || '';
    const catPills = card.querySelectorAll('.cat-pill-item');

    let hasMatchingCategory = false;

    catPills.forEach(pill => {
      const catName = pill.getAttribute('data-cat-name') || '';
      const catDesc = pill.getAttribute('data-cat-desc') || '';
      if (!q || catName.includes(q) || catDesc.includes(q)) {
        pill.style.display = 'inline-flex';
        hasMatchingCategory = true;
      } else {
        pill.style.display = 'none';
      }
    });

    if (!q || topicName.includes(q) || topicDesc.includes(q) || hasMatchingCategory) {
      card.style.display = 'flex';
    } else {
      card.style.display = 'none';
    }
  });
}

// Topic tab filter
function filterByTopicTab(targetCardId, btn) {
  document.querySelectorAll('#topicPillFilters button').forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });
  btn.classList.remove('ts-btn-outline');
  btn.classList.add('ts-btn-primary');

  const topicCards = document.querySelectorAll('.topic-group-card');
  if (targetCardId === 'all') {
    topicCards.forEach(c => c.style.display = 'flex');
  } else {
    topicCards.forEach(c => {
      c.style.display = (c.id === targetCardId) ? 'flex' : 'none';
    });
  }
}

// Subscribe / Follow Topic or Category
async function toggleSubscription(type, id, btn) {
  <?php if (!\App\Core\Auth::check()): ?>
    if (confirm('Please sign in or create an account to follow topics and build your personalized feed.')) {
      window.location.href = '<?= url('/auth/login?redirect=' . urlencode('/topics')) ?>';
    }
    return;
  <?php endif; ?>

  try {
    const res = await fetch('<?= url('/api/v1/subscriptions/toggle') ?>', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ type: type, id: id })
    });
    const data = await res.json();
    if (data.success) {
      const isSub = data.is_subscribed;
      if (isSub) {
        btn.classList.remove('ts-btn-outline');
        btn.classList.add('ts-btn-primary');
        btn.innerHTML = `<i data-lucide="check" style="width: 12px; height: 12px;"></i> <span>Following</span>`;
        showToast(`Subscribed to topic! It will now appear in your For You feed. ⚡`, 'success');
      } else {
        btn.classList.remove('ts-btn-primary');
        btn.classList.add('ts-btn-outline');
        btn.innerHTML = `<i data-lucide="plus" style="width: 12px; height: 12px;"></i> <span>Follow</span>`;
        showToast('Topic unfollowed.', 'info');
      }
      lucide.createIcons();
    } else {
      showToast(data.message || 'Could not update subscription', 'error');
    }
  } catch (err) {
    showToast('Failed to update subscription.', 'error');
  }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
