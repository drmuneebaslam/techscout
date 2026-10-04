<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-main">
  <!-- Topbar -->
  <div class="admin-topbar">
    <div>
      <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-pink">REST API GATEWAY &bull; BEARER AUTH</span>
        <span style="font-size: 0.8rem; color: #64748b;">Developer Access Tokens &amp; Rate Limit Quotas</span>
      </div>
      <h1 style="font-size: 1.6rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        API Keys &amp; Developer Access
      </h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <button type="button" onclick="openNewKeyModal()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #ec4899, #6366f1); font-weight: 700;">
        <i data-lucide="key"></i> Generate Developer API Key ⚡
      </button>
    </div>
  </div>

  <?php if ($newKey = \App\Core\Session::getFlash('new_api_key')): ?>
    <div style="background: rgba(16,185,129,0.15); border: 1px solid #10b981; border-radius: 8px; padding: 1.25rem; margin-bottom: 1.5rem;">
      <h4 style="color: #34d399; margin: 0 0 0.5rem 0;">⚡ New API Key Generated</h4>
      <p style="color: #cbd5e1; font-size: 0.85rem; margin: 0 0 0.75rem 0;">Copy this key now. For security purposes, it will never be displayed in plain text again.</p>
      <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.5rem; max-width: 600px;">
        <input type="text" value="<?= htmlspecialchars($newKey) ?>" id="generatedKeyField" readonly class="admin-modal-input" style="font-family: monospace; color: #34d399; font-weight: 700;">
        <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('generatedKeyField').value); alert('Copied to clipboard!');" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #10b981; border-color: #10b981; white-space: nowrap;">
          Copy Key 📋
        </button>
      </div>
    </div>
  <?php endif; ?>

  <div class="admin-table-wrap" style="padding: 1.5rem;">
    <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 1rem 0;">Active Developer Tokens</h3>

    <table class="admin-table">
      <thead>
        <tr>
          <th>Key Name &amp; Token Prefix</th>
          <th>Owner Account</th>
          <th>Authorized Scopes</th>
          <th>Rate Limit</th>
          <th>Total Requests</th>
          <th>Created</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($keys)): ?>
          <?php foreach ($keys as $k): ?>
            <tr>
              <td>
                <strong style="color: #f8fafc; font-size: 0.95rem;"><?= htmlspecialchars($k['key_name']) ?></strong>
                <div style="font-size: 0.75rem; color: #a855f7; font-family: monospace; margin-top: 0.2rem;">
                  <?= htmlspecialchars($k['api_key_prefix']) ?>••••••••••••••••
                </div>
              </td>
              <td>
                <span style="color: #f8fafc; font-size: 0.88rem;">@<?= htmlspecialchars($k['username']) ?></span>
                <div style="font-size: 0.72rem; color: #64748b;"><?= htmlspecialchars($k['email']) ?></div>
              </td>
              <td>
                <?php 
                  $scopes = json_decode($k['scopes_json'] ?? '[]', true) ?: [];
                  foreach ($scopes as $s):
                ?>
                  <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem; font-family: monospace;"><?= htmlspecialchars($s) ?></span>
                <?php endforeach; ?>
              </td>
              <td>
                <span style="font-family: monospace; font-size: 0.85rem; color: #38bdf8;">
                  <?= (int)$k['rate_limit_per_min'] ?> req/min
                </span>
              </td>
              <td>
                <span class="ts-badge" style="background: rgba(99,102,241,0.2); color: #a5b4fc;">
                  <?= number_format($k['requests_count'] ?? 0) ?>
                </span>
              </td>
              <td style="font-family: var(--admin-font-mono); font-size: 0.75rem; color: #94a3b8;">
                <?= \App\Core\DateHelper::render($k['created_at'], 'Y-m-d', 'date') ?>
              </td>
              <td>
                <form action="<?= url('/admin/api-keys/revoke/' . $k['id']) ?>" method="POST" onsubmit="return confirm('Revoke and delete this API key? Any applications using it will be blocked.');">
                  <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; color: #ef4444;">
                    Revoke ✕
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" style="text-align: center; padding: 2.5rem; color: #64748b;">No active API keys found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal: Generate API Key -->
<div id="keyModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-pink" style="max-width: 550px;">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #ec4899;"><i data-lucide="key"></i> Generate Developer API Key</h3>
      <button type="button" onclick="closeKeyModal()" class="admin-modal-close">✕</button>
    </div>

    <form action="<?= url('/admin/api-keys/generate') ?>" method="POST">
      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Key Name / Client Purpose</label>
        <input type="text" name="key_name" placeholder="e.g. Production CI/CD Crawler or Mobile App" class="admin-modal-input" required>
      </div>

      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Assign To Registered User</label>
        <select name="user_id" class="admin-modal-select">
          <?php foreach ($users as $u): ?>
            <option value="<?= $u['id'] ?>">@<?= htmlspecialchars($u['username']) ?> (<?= htmlspecialchars($u['email']) ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>

      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Rate Limit (Requests per minute)</label>
        <input type="number" name="rate_limit_per_min" value="60" min="1" max="10000" class="admin-modal-input">
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label class="admin-modal-label">Assigned Permissions &amp; Scopes</label>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.82rem; color: #cbd5e1; background: #04070e; padding: 0.75rem; border-radius: 6px; border: 1px solid rgba(148,163,184,0.15);">
          <label><input type="checkbox" name="scopes[]" value="read:articles" checked> read:articles</label>
          <label><input type="checkbox" name="scopes[]" value="read:tools" checked> read:tools</label>
          <label><input type="checkbox" name="scopes[]" value="read:gpts" checked> read:gpts</label>
          <label><input type="checkbox" name="scopes[]" value="write:prompts"> write:prompts</label>
          <label><input type="checkbox" name="scopes[]" value="admin:telemetry"> admin:telemetry</label>
        </div>
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="closeKeyModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #ec4899; border-color: #ec4899; font-weight: 700;">Issue Key ⚡</button>
      </div>
    </form>
  </div>
</div>

<script>
function openNewKeyModal() {
  document.getElementById('keyModal').style.display = 'flex';
}
function closeKeyModal() {
  document.getElementById('keyModal').style.display = 'none';
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
