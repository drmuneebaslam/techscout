<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 3rem; margin-bottom: 5rem; max-width: 1080px;">

  <!-- Header -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-pink">PRIVATE TO YOUR ACCOUNT</span>
        <span style="font-size: 0.8rem; color: #64748b;">Only visible to you &amp; verified admins</span>
      </div>
      <h1 style="font-size: 2.2rem; color: #f8fafc; font-weight: 800; font-family: 'Outfit', sans-serif; margin: 0;">
        🔖 Saved Bookmarks &amp; Reading List
      </h1>
    </div>

    <div style="display: flex; gap: 0.5rem;">
      <a href="<?= url('/history') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8;">
        <i data-lucide="history"></i> View Reading History &rarr;
      </a>
    </div>
  </div>

  <!-- Filter Types -->
  <div style="display: flex; gap: 0.5rem; margin-bottom: 1.75rem; flex-wrap: wrap;">
    <a href="<?= url('/bookmarks') ?>" class="ts-btn ts-btn-sm <?= empty($currentType) || $currentType === 'all' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="border-radius: 999px;">
      All Items (<?= count($bookmarks) ?>)
    </a>
    <a href="<?= url('/bookmarks?type=article') ?>" class="ts-btn ts-btn-sm <?= ($currentType ?? '') === 'article' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="border-radius: 999px;">
      Articles
    </a>
    <a href="<?= url('/bookmarks?type=ai_tool') ?>" class="ts-btn ts-btn-sm <?= ($currentType ?? '') === 'ai_tool' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="border-radius: 999px;">
      AI Tools
    </a>
    <a href="<?= url('/bookmarks?type=prompt') ?>" class="ts-btn ts-btn-sm <?= ($currentType ?? '') === 'prompt' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="border-radius: 999px;">
      Prompt Blueprints
    </a>
    <a href="<?= url('/bookmarks?type=gpt') ?>" class="ts-btn ts-btn-sm <?= ($currentType ?? '') === 'gpt' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="border-radius: 999px;">
      Custom GPTs
    </a>
  </div>

  <!-- Bookmarks Grid -->
  <?php if (!empty($bookmarks)): ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 320px), 1fr)); gap: 1.25rem;">
      <?php foreach ($bookmarks as $b): ?>
        <?php
          $targetUrl = content_url($b);
        ?>
        <div class="ts-card" id="bookmark-card-<?= (int)$b['id'] ?>" style="background: rgba(15,23,42,0.65); border: 1px solid rgba(148,163,184,0.15); border-radius: 12px; padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; transition: all 0.2s ease;">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
              <span class="ts-badge ts-badge-cyan" style="font-size: 0.68rem; text-transform: uppercase;">
                <?= htmlspecialchars(str_replace('_', ' ', $b['content_type'])) ?>
              </span>
              <button type="button" onclick="removeBookmark(<?= (int)$b['id'] ?>)" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.72rem; color: #ef4444; border-color: rgba(239,68,68,0.3);" title="Remove from bookmarks">
                🗑 Remove
              </button>
            </div>

            <h3 style="font-size: 1.15rem; color: #f8fafc; font-family: 'Outfit', sans-serif; font-weight: 700; margin: 0 0 0.5rem 0; line-height: 1.4;">
              <a href="<?= $targetUrl ?>" style="color: #f8fafc; text-decoration: none;">
                <?= htmlspecialchars($b['title']) ?>
              </a>
            </h3>

            <p style="color: #94a3b8; font-size: 0.86rem; line-height: 1.5; margin: 0 0 1rem 0;">
              <?= htmlspecialchars(mb_strimwidth($b['summary'] ?? '', 0, 110, '...')) ?>
            </p>
          </div>

          <div style="border-top: 1px solid rgba(148,163,184,0.1); padding-top: 0.75rem; display: flex; justify-content: space-between; align-items: center; font-size: 0.78rem; color: #64748b;">
            <span>Saved on <?= date('M j, Y', strtotime($b['saved_at'])) ?></span>
            <a href="<?= $targetUrl ?>" style="color: #38bdf8; text-decoration: none; font-weight: 600;">Open &rarr;</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Pagination Controls -->
    <?php if (($totalPages ?? 1) > 1): ?>
      <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2.5rem; padding-top: 1.5rem; border-top: 1px solid var(--ts-border-subtle); flex-wrap: wrap; gap: 1rem;">
        <div style="font-size: 0.85rem; color: var(--ts-text-tertiary);">
          Showing Page <strong style="color: var(--ts-text-primary);"><?= (int)$page ?></strong> of <?= (int)$totalPages ?> (<?= (int)$totalCount ?> total bookmarks)
        </div>

        <div style="display: flex; align-items: center; gap: 0.4rem;">
          <?php if ($page > 1): ?>
            <a href="<?= url('/bookmarks?type=' . urlencode($currentType ?? 'all') . '&page=' . ($page - 1)) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="border-radius: 8px;">
              &larr; Prev
            </a>
          <?php endif; ?>

          <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <?php if ($p === 1 || $p === $totalPages || abs($p - $page) <= 2): ?>
              <a href="<?= url('/bookmarks?type=' . urlencode($currentType ?? 'all') . '&page=' . $p) ?>" class="ts-btn ts-btn-sm <?= $p === $page ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="border-radius: 8px; min-width: 36px; text-align: center;">
                <?= $p ?>
              </a>
            <?php elseif ($p === 2 || $p === $totalPages - 1): ?>
              <span style="color: var(--ts-text-tertiary); padding: 0 0.25rem;">...</span>
            <?php endif; ?>
          <?php endfor; ?>

          <?php if ($page < $totalPages): ?>
            <a href="<?= url('/bookmarks?type=' . urlencode($currentType ?? 'all') . '&page=' . ($page + 1)) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="border-radius: 8px;">
              Next &rarr;
            </a>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  <?php else: ?>
    <div class="ts-card" style="text-align: center; padding: 4.5rem 2rem; background: rgba(15,23,42,0.6); border: 1px dashed rgba(148,163,184,0.2); border-radius: 16px;">
      <i data-lucide="bookmark" style="width: 48px; height: 48px; color: #475569; margin-bottom: 1rem;"></i>
      <h3 style="color: #f8fafc; font-size: 1.35rem; margin: 0 0 0.5rem 0;">No saved bookmarks yet</h3>
      <p style="color: #94a3b8; font-size: 0.95rem; max-width: 500px; margin: 0 auto 1.5rem auto;">
        As you explore deep-dive articles, AI tools, and prompt blueprints, click the <strong>🔖 Save</strong> button on any publication to build your private research library.
      </p>
      <a href="<?= url('/blog') ?>" class="ts-btn ts-btn-primary" style="font-weight: 700;">
        Explore Articles &rarr;
      </a>
    </div>
  <?php endif; ?>

</div>

<script>
async function removeBookmark(contentId) {
  try {
    const fd = new FormData();
    fd.append('content_id', contentId);
    const r = await fetch('<?= url('/api/v1/bookmark') ?>', { method: 'POST', body: fd });
    const d = await r.json();
    if (d.success) {
      const el = document.getElementById(`bookmark-card-${contentId}`);
      if (el) {
        el.style.opacity = '0';
        el.style.transform = 'scale(0.95)';
        setTimeout(() => el.remove(), 250);
      }
      if (typeof showToast === 'function') {
        showToast('Bookmark removed from library.', 'info');
      }
    }
  } catch (e) {
    alert('Error updating bookmark.');
  }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
