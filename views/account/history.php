<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 3rem; margin-bottom: 5rem; max-width: 960px;">

  <!-- Header -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-cyan">PRIVATE BROWSING ACTIVITY</span>
        <span style="font-size: 0.8rem; color: #64748b;">Only visible to you &amp; verified admins</span>
      </div>
      <h1 style="font-size: 2.2rem; color: #f8fafc; font-weight: 800; font-family: 'Outfit', sans-serif; margin: 0;">
        🕒 Reading History &amp; Progress
      </h1>
    </div>

    <?php if (!empty($history)): ?>
      <button type="button" onclick="clearReadingHistory()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.3);">
        <i data-lucide="trash-2"></i> Clear History
      </button>
    <?php endif; ?>
  </div>

  <!-- History Items List -->
  <?php if (!empty($history)): ?>
    <div class="ts-card" style="padding: 0; overflow: hidden; background: var(--ts-bg-surface, rgba(15,23,42,0.7)); border: 1px solid var(--ts-border-main, rgba(148,163,184,0.15)); border-radius: 14px;" id="historyListCard">
      <div style="display: flex; flex-direction: column;">
        <?php foreach ($history as $h): ?>
          <?php 
            $targetUrl = content_url($h);
            
            $progress = (int)($h['progress_percent'] ?? 0);
            $isComplete = ($progress >= 90 || ($h['status'] ?? '') === 'completed');
            $sessionNum = (int)($h['session_number'] ?? 1);
            $isReread = ($sessionNum > 1 || ($h['status'] ?? '') === 're_reading');
            
            $readSeconds = (int)($h['read_seconds'] ?? 0);
            $durationStr = $readSeconds > 60 ? floor($readSeconds / 60) . 'm ' . ($readSeconds % 60) . 's' : $readSeconds . 's';
          ?>
          <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--ts-border-subtle, rgba(148,163,184,0.1)); transition: background 0.15s ease;" onmouseover="this.style.background='rgba(99,102,241,0.06)'" onmouseout="this.style.background='transparent'">
            <div style="display: flex; align-items: flex-start; gap: 1rem; flex: 1;">
              <div style="width: 42px; height: 42px; border-radius: 10px; background: <?= $isComplete ? 'rgba(16,185,129,0.12)' : 'rgba(56,189,248,0.12)' ?>; display: flex; align-items: center; justify-content: center; color: <?= $isComplete ? '#10b981' : '#38bdf8' ?>; flex-shrink: 0; margin-top: 2px;">
                <i data-lucide="<?= $isComplete ? 'check-circle' : 'book-open' ?>" style="width: 20px; height: 20px;"></i>
              </div>

              <div style="flex: 1;">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem; flex-wrap: wrap;">
                  <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem; text-transform: uppercase;">
                    <?= htmlspecialchars(str_replace('_', ' ', $h['content_type'])) ?>
                  </span>

                  <?php if ($isReread): ?>
                    <span class="ts-badge ts-badge-pink" style="font-size: 0.68rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.25rem;">
                      <i data-lucide="repeat" style="width: 10px; height: 10px;"></i> Re-read Session #<?= $sessionNum ?>
                    </span>
                  <?php else: ?>
                    <span class="ts-badge" style="font-size: 0.68rem; background: rgba(99,102,241,0.12); color: #818cf8; border: 1px solid rgba(99,102,241,0.25);">
                      Session #1
                    </span>
                  <?php endif; ?>

                  <?php if ($isComplete): ?>
                    <span class="ts-badge ts-badge-green" style="font-size: 0.68rem; font-weight: 700;">FINISHED 100% ✓</span>
                  <?php else: ?>
                    <span class="ts-badge ts-badge-amber" style="font-size: 0.68rem; font-weight: 700;"><?= $progress ?>% IN PROGRESS</span>
                  <?php endif; ?>

                  <span style="font-size: 0.75rem; color: var(--ts-text-tertiary); font-family: var(--ts-font-mono);">
                    ⏱ <?= $durationStr ?> spent
                  </span>
                </div>

                <h3 style="font-size: 1.05rem; margin: 0 0 0.35rem 0; font-weight: 700;">
                  <a href="<?= $targetUrl ?>" style="color: var(--ts-text-primary); text-decoration: none;">
                    <?= htmlspecialchars($h['title']) ?>
                  </a>
                </h3>

                <!-- Timestamps and Session Metadata -->
                <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; font-size: 0.75rem; color: var(--ts-text-tertiary); margin-bottom: 0.45rem;">
                  <span>
                    <strong>Started:</strong> <?= !empty($h['started_at']) ? date('M j, Y • H:i', strtotime($h['started_at'])) : date('M j, Y • H:i', strtotime($h['last_read_at'])) ?>
                  </span>
                  <span>
                    <strong>Last active:</strong> <?= date('M j, Y • H:i', strtotime($h['last_read_at'])) ?>
                  </span>
                  <?php if (!empty($h['completed_at'])): ?>
                    <span style="color: #10b981;">
                      <strong>Finished:</strong> <?= date('M j, Y • H:i', strtotime($h['completed_at'])) ?>
                    </span>
                  <?php endif; ?>
                </div>

                <!-- Reading Progress Bar -->
                <div style="display: flex; align-items: center; gap: 0.65rem; max-width: 320px;">
                  <div style="flex: 1; height: 6px; background: rgba(148,163,184,0.15); border-radius: 999px; overflow: hidden;">
                    <div style="width: <?= max(4, $progress) ?>%; height: 100%; background: <?= $isComplete ? '#10b981' : 'linear-gradient(90deg, #6366f1, #38bdf8)' ?>; border-radius: 999px;"></div>
                  </div>
                  <span style="font-size: 0.72rem; color: <?= $isComplete ? '#10b981' : '#38bdf8' ?>; font-family: monospace; font-weight: 700;"><?= $progress ?>% read</span>
                </div>
              </div>
            </div>

            <div style="display: flex; gap: 0.5rem; align-items: center; margin-left: 1rem;">
              <a href="<?= $targetUrl ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.35rem 0.8rem; color: <?= $isComplete ? '#10b981' : '#38bdf8' ?>; border-color: <?= $isComplete ? 'rgba(16,185,129,0.4)' : 'rgba(56,189,248,0.4)' ?>;">
                <?= $isComplete ? 'Read Again 🔄' : 'Resume &rarr;' ?>
              </a>
              <button type="button" onclick="deleteHistoryEntry(<?= (int)$h['id'] ?>)" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.35rem 0.55rem; font-size: 0.75rem; color: #ef4444; border-color: rgba(239,68,68,0.25);" title="Delete reading record">
                <i data-lucide="trash-2" style="width: 13px; height: 13px;"></i>
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Pagination Controls -->
    <?php if (($totalPages ?? 1) > 1): ?>
      <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--ts-border-subtle); flex-wrap: wrap; gap: 1rem;">
        <div style="font-size: 0.85rem; color: var(--ts-text-tertiary);">
          Showing Page <strong style="color: var(--ts-text-primary);"><?= (int)$page ?></strong> of <?= (int)$totalPages ?> (<?= (int)$totalCount ?> reading sessions)
        </div>

        <div style="display: flex; align-items: center; gap: 0.4rem;">
          <?php if ($page > 1): ?>
            <a href="<?= url('/history?page=' . ($page - 1)) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="border-radius: 8px;">
              &larr; Prev
            </a>
          <?php endif; ?>

          <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <?php if ($p === 1 || $p === $totalPages || abs($p - $page) <= 2): ?>
              <a href="<?= url('/history?page=' . $p) ?>" class="ts-btn ts-btn-sm <?= $p === $page ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="border-radius: 8px; min-width: 36px; text-align: center;">
                <?= $p ?>
              </a>
            <?php elseif ($p === 2 || $p === $totalPages - 1): ?>
              <span style="color: var(--ts-text-tertiary); padding: 0 0.25rem;">...</span>
            <?php endif; ?>
          <?php endfor; ?>

          <?php if ($page < $totalPages): ?>
            <a href="<?= url('/history?page=' . ($page + 1)) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="border-radius: 8px;">
              Next &rarr;
            </a>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  <?php else: ?>
    <div class="ts-card" style="text-align: center; padding: 4.5rem 2rem; background: rgba(15,23,42,0.6); border: 1px dashed rgba(148,163,184,0.2); border-radius: 16px;">
      <i data-lucide="history" style="width: 48px; height: 48px; color: #475569; margin-bottom: 1rem;"></i>
      <h3 style="color: #f8fafc; font-size: 1.35rem; margin: 0 0 0.5rem 0;">No reading history recorded</h3>
      <p style="color: #94a3b8; font-size: 0.95rem; max-width: 500px; margin: 0 auto 1.5rem auto;">
        As you read publications on TechScout, your reading progress and last-visited positions will automatically appear here.
      </p>
      <a href="<?= url('/blog') ?>" class="ts-btn ts-btn-primary" style="font-weight: 700;">
        Start Reading &rarr;
      </a>
    </div>
  <?php endif; ?>

</div>

<script>
async function deleteHistoryEntry(historyId) {
  try {
    const r = await fetch('<?= url('/api/v1/history/delete/') ?>' + historyId, { method: 'POST' });
    const d = await r.json();
    if (d.success) {
      location.reload();
    }
  } catch (e) {
    if (typeof showToast === 'function') {
      showToast('Error removing history entry.', 'error');
    }
  }
}

async function clearReadingHistory() {
  if (!confirm('Clear all your reading history records?')) return;
  try {
    const r = await fetch('<?= url('/api/v1/history/clear') ?>', { method: 'POST' });
    const d = await r.json();
    if (d.success) {
      location.reload();
    }
  } catch (e) {
    if (typeof showToast === 'function') {
      showToast('Error clearing history.', 'error');
    }
  }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
