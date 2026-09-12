<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-main">
  <!-- Topbar -->
  <div class="admin-topbar">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-amber">AUTO-MODERATION &bull; CONTENT SAFETY</span>
        <span style="font-size: 0.8rem; color: #64748b;">Keyword Blacklist, Prohibited Term Replacements &amp; Spam Shields</span>
      </div>
      <h1 style="font-size: 1.6rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        Content Filters &amp; Word Blacklist
      </h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <button type="button" onclick="openNewFilterModal()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #f59e0b, #ec4899); font-weight: 700;">
        <i data-lucide="shield"></i> Add Filter Rule 🛡️
      </button>
    </div>
  </div>

  <div class="admin-table-wrap" style="padding: 1.5rem;">
    <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 1rem 0;">Registered Blacklist &amp; Content Rules</h3>

    <table class="admin-table">
      <thead>
        <tr>
          <th>Keyword / Regex Pattern</th>
          <th>Match Type</th>
          <th>Replacement Text</th>
          <th>Trigger Action</th>
          <th>Severity Tier</th>
          <th>Date Added</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($filters)): ?>
          <?php foreach ($filters as $f): ?>
            <tr>
              <td>
                <code style="color: #f87171; background: #05070b; padding: 3px 8px; border-radius: 4px; font-family: monospace; font-size: 0.88rem;">
                  <?= htmlspecialchars($f['pattern']) ?>
                </code>
              </td>
              <td>
                <span class="ts-badge ts-badge-cyan" style="font-size: 0.68rem; text-transform: uppercase;">
                  <?= htmlspecialchars($f['match_type']) ?>
                </span>
              </td>
              <td>
                <span style="color: #cbd5e1; font-family: monospace; font-size: 0.85rem;"><?= htmlspecialchars($f['replacement']) ?></span>
              </td>
              <td>
                <span class="ts-badge <?= $f['action'] === 'auto_quarantine' ? 'ts-badge-pink' : 'ts-badge-cyan' ?>">
                  <?= strtoupper(str_replace('_', ' ', $f['action'])) ?>
                </span>
              </td>
              <td>
                <span class="strike-badge" style="font-size: 0.68rem;"><?= strtoupper($f['severity']) ?></span>
              </td>
              <td style="font-family: var(--admin-font-mono); font-size: 0.75rem; color: #94a3b8;">
                <?= \App\Core\DateHelper::render($f['created_at'], 'Y-m-d', 'date') ?>
              </td>
              <td>
                <form action="<?= url('/admin/content-filters/delete/' . $f['id']) ?>" method="POST" onsubmit="return confirm('Delete this content filter rule?');">
                  <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; color: #ef4444;">
                    Delete ✕
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" style="text-align: center; padding: 2.5rem; color: #64748b;">No content moderation filter rules configured.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal: New Content Filter -->
<div id="filterModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-amber" style="max-width: 500px;">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #f59e0b;"><i data-lucide="shield"></i> Add Word / Pattern Filter</h3>
      <button type="button" onclick="closeFilterModal()" class="admin-modal-close">✕</button>
    </div>

    <form action="<?= url('/admin/content-filters/save') ?>" method="POST">
      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Prohibited Word or Regular Expression</label>
        <input type="text" name="pattern" placeholder="e.g. malicious_link.com or spam keyword" class="admin-modal-input" required>
      </div>

      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Match Strategy</label>
        <select name="match_type" class="admin-modal-select">
          <option value="contains">Contains Substring (Case Insensitive)</option>
          <option value="exact">Exact Whole Word Match</option>
          <option value="regex">Regular Expression Pattern (e.g. /pattern/i)</option>
        </select>
      </div>

      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Replacement Mask</label>
        <input type="text" name="replacement" value="***" class="admin-modal-input">
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label class="admin-modal-label">Action on Detection</label>
          <select name="action" class="admin-modal-select">
            <option value="replace">Mask with Replacement (***)</option>
            <option value="flag_review">Flag for Moderator Review</option>
            <option value="auto_quarantine">Auto-Quarantine Submission</option>
          </select>
        </div>

        <div>
          <label class="admin-modal-label">Severity Level</label>
          <select name="severity" class="admin-modal-select">
            <option value="medium">Medium</option>
            <option value="low">Low</option>
            <option value="high">High</option>
            <option value="critical">Critical</option>
          </select>
        </div>
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="closeFilterModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #f59e0b; border-color: #f59e0b; color: #000; font-weight: 700;">Enforce Filter 🛡️</button>
      </div>
    </form>
  </div>
</div>

<script>
function openNewFilterModal() {
  document.getElementById('filterModal').style.display = 'flex';
}
function closeFilterModal() {
  document.getElementById('filterModal').style.display = 'none';
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
