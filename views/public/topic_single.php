<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 2.5rem; margin-bottom: 5rem; max-width: 1200px;">

  <!-- Breadcrumbs -->
  <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem;">
    <a href="<?= url('/topics') ?>" class="ts-badge ts-badge-cyan" style="text-decoration: none;">← All Topics & Categories</a>
    <span class="ts-badge ts-badge-pink"><?= htmlspecialchars($topic['name']) ?></span>
  </div>

  <!-- Topic Hero Banner -->
  <div class="ts-card" style="background: radial-gradient(circle at 10% 20%, rgba(99,102,241,0.15), transparent 45%), var(--ts-bg-surface); border-color: rgba(99,102,241,0.3); padding: 2.5rem; margin-bottom: 2.5rem; position: relative;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1.5rem;">
      <div style="max-width: 750px;">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
          <div style="width: 52px; height: 52px; border-radius: 12px; background: rgba(99,102,241,0.15); border: 1px solid <?= htmlspecialchars($topic['color_hex'] ?: '#6366f1') ?>44; display: flex; align-items: center; justify-content: center; color: <?= htmlspecialchars($topic['color_hex'] ?: '#6366f1') ?>;">
            <i data-lucide="<?= htmlspecialchars($topic['icon_name'] ?: 'layers') ?>" style="width: 28px; height: 28px;"></i>
          </div>
          <div>
            <h1 style="font-size: 2.2rem; color: var(--ts-text-primary); margin: 0; line-height: 1.2;">
              <?= htmlspecialchars($topic['name']) ?>
            </h1>
            <span style="font-size: 0.85rem; color: var(--ts-text-secondary);"><?= count($categories) ?> Specialized Subcategories &bull; <?= number_format($topic['follower_count'] ?? 0) ?> Followers</span>
          </div>
        </div>

        <p style="font-size: 1.05rem; color: var(--ts-text-secondary); line-height: 1.6; margin-bottom: 1.5rem;">
          <?= htmlspecialchars($topic['description'] ?? 'Curated engineering articles, system blueprints, prompts, and tools.') ?>
        </p>

        <!-- Subcategories Under Topic -->
        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
          <?php foreach ($categories as $cat): ?>
            <a href="<?= url('/blog/category/' . $cat['slug']) ?>" class="ts-badge" style="background: var(--ts-bg-base); border: 1px solid var(--ts-border); color: var(--ts-text-secondary); text-decoration: none; padding: 0.35rem 0.65rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.35rem;">
              <i data-lucide="<?= htmlspecialchars($cat['icon_name'] ?: 'folder') ?>" style="width: 12px; height: 12px; color: <?= htmlspecialchars($cat['color_hex'] ?: '#6366f1') ?>;"></i>
              <?= htmlspecialchars($cat['name']) ?> (<?= $cat['content_count'] ?? 0 ?>)
            </a>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Follow Topic CTA -->
      <div>
        <button type="button" class="ts-btn ts-btn-lg <?= $isSubscribed ? 'ts-btn-primary' : 'ts-btn-outline' ?>" onclick="toggleSubscription('topic', <?= $topic['id'] ?>, this)">
          <i data-lucide="<?= $isSubscribed ? 'check' : 'plus' ?>"></i>
          <span><?= $isSubscribed ? 'Following Topic ✓' : 'Follow Topic Feed' ?></span>
        </button>
      </div>
    </div>
  </div>

  <!-- Articles & Content in Topic -->
  <div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
    <h2 style="font-size: 1.4rem; color: #f8fafc; margin: 0;">Featured Articles & Publications</h2>
    <span style="font-size: 0.85rem; color: #94a3b8;">Showing recent deep dives</span>
  </div>

  <?php if (!empty($articles)): ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.5rem;">
      <?php foreach ($articles as $art): ?>
        <div class="ts-card" style="background: #090d16; padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <?php if (!empty($art['featured_image_url'])): ?>
              <img src="<?= htmlspecialchars(image_url($art['featured_image_url'])) ?>" alt="<?= htmlspecialchars($art['title']) ?>" style="width: 100%; height: 160px; object-fit: cover; border-radius: 6px; margin-bottom: 1rem; border: 1px solid rgba(148,163,184,0.15);">
            <?php endif; ?>

            <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem;">
              <span class="ts-badge ts-badge-cyan" style="font-size: 0.7rem;"><?= htmlspecialchars($art['category_name'] ?? 'Domain') ?></span>
              <span style="font-size: 0.75rem; color: #64748b;"><?= date('M j, Y', strtotime($art['published_at'] ?? 'now')) ?></span>
            </div>

            <h3 style="font-size: 1.15rem; color: #f8fafc; margin-bottom: 0.5rem; line-height: 1.3;">
              <a href="<?= url('/article/' . $art['slug']) ?>" style="color: #f8fafc; text-decoration: none;">
                <?= htmlspecialchars($art['title']) ?>
              </a>
            </h3>

            <p style="font-size: 0.85rem; color: #94a3b8; line-height: 1.5; margin-bottom: 1rem;">
              <?= htmlspecialchars($art['summary']) ?>
            </p>
          </div>

          <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(148,163,184,0.1); padding-top: 0.75rem; margin-top: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
              <?php if (!empty($art['author_avatar'])): ?>
                <img src="<?= htmlspecialchars(image_url($art['author_avatar'])) ?>" alt="Avatar" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover;">
              <?php endif; ?>
              <span style="font-size: 0.8rem; color: #cbd5e1;"><?= htmlspecialchars($art['author_name'] ?? 'Author') ?></span>
            </div>
            <a href="<?= url('/article/' . $art['slug']) ?>" class="ts-btn ts-btn-sm ts-btn-outline" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;">Read →</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="ts-card" style="text-align: center; padding: 3rem 1.5rem; color: #64748b;">
      <i data-lucide="folder-open" style="width: 42px; height: 42px; margin-bottom: 0.75rem; color: var(--ts-primary);"></i>
      <h3 style="font-size: 1.1rem; color: #f8fafc; margin-bottom: 0.25rem;">No publications in this topic domain yet</h3>
      <p style="font-size: 0.85rem; margin: 0;">Be the first author to publish a technical blueprint here!</p>
    </div>
  <?php endif; ?>

</div>

<script>
async function toggleSubscription(type, id, btn) {
  <?php if (!\App\Core\Auth::check()): ?>
    if (confirm('Please sign in or create an account to follow topics.')) {
      window.location.href = '<?= url('/auth/login?redirect=' . urlencode('/topic/' . $topic['slug'])) ?>';
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
      if (data.is_subscribed) {
        btn.classList.remove('ts-btn-outline');
        btn.classList.add('ts-btn-primary');
        btn.innerHTML = `<i data-lucide="check"></i> <span>Following Topic ✓</span>`;
        showToast('Subscribed to topic! ⚡', 'success');
      } else {
        btn.classList.remove('ts-btn-primary');
        btn.classList.add('ts-btn-outline');
        btn.innerHTML = `<i data-lucide="plus"></i> <span>Follow Topic Feed</span>`;
        showToast('Topic unfollowed.', 'info');
      }
      lucide.createIcons();
    }
  } catch (err) {
    showToast('Failed to update subscription.', 'error');
  }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
