<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-main">
  <!-- Topbar -->
  <div class="admin-topbar">
    <div>
      <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-cyan">EVENT STREAM &bull; HMAC-SHA256</span>
        <span style="font-size: 0.8rem; color: #64748b;">Outbound Webhooks &amp; Real-Time Integration Pipelines</span>
      </div>
      <h1 style="font-size: 1.6rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        Webhooks &amp; Event Stream
      </h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <button type="button" onclick="openNewWebhookModal()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #6366f1, #38bdf8); font-weight: 700;">
        <i data-lucide="plus"></i> Register Webhook Endpoint 🚀
      </button>
    </div>
  </div>

  <!-- Webhooks Roster -->
  <div class="admin-table-wrap" style="padding: 1.5rem; margin-bottom: 2rem;">
    <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 1rem 0;">Configured Webhook Endpoints</h3>

    <table class="admin-table">
      <thead>
        <tr>
          <th>Name &amp; Target URL</th>
          <th>Subscribed Events</th>
          <th>Secret Key</th>
          <th>Deliveries</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($webhooks)): ?>
          <?php foreach ($webhooks as $wh): ?>
            <tr>
              <td>
                <strong style="color: #f8fafc; font-size: 0.95rem;"><?= htmlspecialchars($wh['name']) ?></strong>
                <div style="font-size: 0.78rem; color: #38bdf8; font-family: var(--admin-font-mono); margin-top: 0.2rem; word-break: break-all;">
                  <?= htmlspecialchars($wh['target_url']) ?>
                </div>
              </td>
              <td>
                <?php 
                  $evs = json_decode($wh['events_json'] ?? '[]', true) ?: [];
                  foreach ($evs as $e):
                ?>
                  <span class="ts-badge ts-badge-cyan" style="font-size: 0.68rem; font-family: monospace;"><?= htmlspecialchars($e) ?></span>
                <?php endforeach; ?>
              </td>
              <td>
                <code style="font-size: 0.75rem; color: #94a3b8; background: #05070b; padding: 2px 6px; border-radius: 4px;">
                  <?= htmlspecialchars(substr($wh['secret_key'], 0, 10)) ?>...
                </code>
              </td>
              <td>
                <span class="ts-badge" style="background: rgba(99,102,241,0.2); color: #a5b4fc; font-weight: 700;">
                  <?= number_format($wh['delivery_count'] ?? 0) ?>
                </span>
              </td>
              <td>
                <span class="ts-badge <?= !empty($wh['is_active']) ? 'ts-badge-green' : 'ts-badge-pink' ?>">
                  <?= !empty($wh['is_active']) ? 'ACTIVE' : 'PAUSED' ?>
                </span>
              </td>
              <td>
                <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.4rem;">
                  <button type="button" onclick='editWebhook(<?= json_encode($wh) ?>)' class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.55rem; color: #38bdf8;">
                    ✏️
                  </button>
                  <button type="button" onclick="testWebhook(<?= $wh['id'] ?>)" class="ts-btn ts-btn-primary ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.55rem; background: #10b981; border-color: #10b981;">
                    ⚡ Test Ping
                  </button>
                  <form action="<?= url('/admin/webhooks/delete/' . $wh['id']) ?>" method="POST" onsubmit="return confirm('Delete this webhook endpoint?');" style="display: inline;">
                    <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.55rem; color: #ef4444;">
                      🗑
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6" style="text-align: center; padding: 2.5rem; color: #64748b;">No webhook endpoints registered yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Recent Deliveries Stream -->
  <div class="admin-table-wrap" style="padding: 1.5rem;">
    <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 1rem 0;">Recent Outbound Delivery Logs</h3>

    <table class="admin-table">
      <thead>
        <tr>
          <th>Timestamp</th>
          <th>Webhook</th>
          <th>Event</th>
          <th>Response Code</th>
          <th>Latency</th>
          <th>Status</th>
          <th>Payload &amp; Logs</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($recentDeliveries)): ?>
          <?php foreach ($recentDeliveries as $del): ?>
            <tr>
              <td style="font-family: var(--admin-font-mono); font-size: 0.75rem; color: #94a3b8;">
                <?= htmlspecialchars($del['created_at']) ?>
              </td>
              <td>
                <strong style="color: #f8fafc; font-size: 0.85rem;"><?= htmlspecialchars($del['webhook_name']) ?></strong>
              </td>
              <td>
                <span class="ts-badge ts-badge-cyan" style="font-size: 0.7rem; font-family: monospace;"><?= htmlspecialchars($del['event']) ?></span>
              </td>
              <td>
                <strong style="color: <?= $del['response_code'] >= 200 && $del['response_code'] < 300 ? '#34d399' : '#f87171' ?>; font-family: monospace;">
                  HTTP <?= (int)$del['response_code'] ?>
                </strong>
              </td>
              <td style="font-family: var(--admin-font-mono); font-size: 0.75rem; color: #94a3b8;">
                <?= (int)$del['duration_ms'] ?>ms
              </td>
              <td>
                <span class="ts-badge <?= $del['status'] === 'success' ? 'ts-badge-green' : 'ts-badge-pink' ?>">
                  <?= strtoupper($del['status']) ?>
                </span>
              </td>
              <td>
                <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.35rem; align-items: center;">
                  <button type="button" onclick='inspectDelivery(<?= htmlspecialchars(json_encode($del), ENT_QUOTES, 'UTF-8') ?>)' class="ts-btn ts-btn-outline ts-btn-xs" style="color: #38bdf8; border-color: rgba(56,189,248,0.3); padding: 0.2rem 0.5rem; font-size: 0.72rem;">
                    🔍 Inspect
                  </button>
                  <button type="button" onclick="retryDelivery(<?= (int)$del['id'] ?>)" class="ts-btn ts-btn-outline ts-btn-xs" style="color: #f59e0b; border-color: rgba(245,158,11,0.3); padding: 0.2rem 0.5rem; font-size: 0.72rem;" title="Retry this delivery payload">
                    🔄 Retry
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" style="text-align: center; padding: 2rem; color: #64748b;">No recent webhook deliveries recorded.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal: New/Edit Webhook -->
<div id="webhookModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-cyan" style="max-width: 620px;">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #38bdf8;"><i data-lucide="webhook"></i> Register Webhook Endpoint</h3>
      <button type="button" onclick="closeWebhookModal()" class="admin-modal-close">✕</button>
    </div>

    <!-- Dual Presets Bar: PythonAnywhere & Cloudflare Worker -->
    <div style="background: rgba(56,189,248,0.06); border: 1px solid rgba(56,189,248,0.22); border-radius: 8px; padding: 0.85rem 1rem; margin-bottom: 1.25rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
        <span style="font-size: 0.82rem; font-weight: 700; color: #f8fafc;">🚀 Quick Integration Presets:</span>
        <span style="font-size: 0.72rem; color: #94a3b8;">1-click automated configuration</span>
      </div>
      <div style="display: flex; gap: 0.6rem; flex-wrap: wrap;">
        <button type="button" onclick="applyPythonAnywherePreset()" class="ts-btn ts-btn-xs" style="background: linear-gradient(135deg, #0284c7, #38bdf8); color: #020617; font-weight: 700; border: none;">
          🐍 PythonAnywhere Mail Endpoint
        </button>
        <button type="button" onclick="applyCloudflareWorkerPreset()" class="ts-btn ts-btn-xs" style="background: linear-gradient(135deg, #f97316, #fbbf24); color: #020617; font-weight: 700; border: none;">
          ☁️ Cloudflare Worker Edge Proxy
        </button>
      </div>
    </div>

    <form id="adminWebhookForm" action="<?= url('/admin/webhooks/save') ?>" method="POST">
      <input type="hidden" name="id" id="wh_id" value="">

      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Endpoint Display Name</label>
        <input type="text" name="name" id="wh_name" placeholder="e.g. PythonAnywhere Mail Microservice" class="admin-modal-input" required>
      </div>

      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Target Payload URL (HTTPS)</label>
        <input type="url" name="target_url" id="wh_target_url" placeholder="https://yourusername.pythonanywhere.com/api/v1/webhook/mail" class="admin-modal-input" required>
      </div>

      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">HMAC-SHA256 Secret Key (Leave blank to auto-generate)</label>
        <input type="text" name="secret_key" id="wh_secret_key" placeholder="techscout_webhook_secure_secret_2026" class="admin-modal-input" style="font-family: monospace;">
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label class="admin-modal-label">Subscribe to Platform Events</label>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.82rem; color: #cbd5e1; background: #04070e; padding: 0.75rem; border-radius: 6px; border: 1px solid rgba(148,163,184,0.15);">
          <label><input type="checkbox" name="events[]" value="*" id="ev_all" checked> <strong>All Events (*)</strong></label>
          <label><input type="checkbox" name="events[]" value="mail.dispatch" id="ev_mail_dispatch"> ✉️ mail.dispatch</label>
          <label><input type="checkbox" name="events[]" value="mail.sent" id="ev_mail_sent"> ✅ mail.sent</label>
          <label><input type="checkbox" name="events[]" value="mail.failed" id="ev_mail_failed"> ⚠️ mail.failed</label>
          <label><input type="checkbox" name="events[]" value="user.registered" id="ev_user_reg"> 👤 user.registered</label>
          <label><input type="checkbox" name="events[]" value="author.applied" id="ev_author_app"> 📝 author.applied</label>
          <label><input type="checkbox" name="events[]" value="strike.issued" id="ev_strike"> 🛡️ strike.issued</label>
          <label><input type="checkbox" name="events[]" value="article.published" id="ev_art_pub"> 🚀 article.published</label>
          <label><input type="checkbox" name="events[]" value="ticket.opened" id="ev_ticket"> 🎫 ticket.opened</label>
          <label><input type="checkbox" name="events[]" value="report.created" id="ev_report"> 🚩 report.created</label>
        </div>
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="closeWebhookModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #38bdf8; border-color: #38bdf8; color: #000; font-weight: 700;">Save Endpoint ⚡</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Delivery Payload Inspector -->
<div id="deliveryInspectModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-cyan" style="max-width: 700px; max-height: 85vh; display: flex; flex-direction: column;">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #38bdf8;"><i data-lucide="binary"></i> Webhook Delivery Telemetry Inspector</h3>
      <button type="button" onclick="closeInspectModal()" class="admin-modal-close">✕</button>
    </div>

    <div style="overflow-y: auto; padding-right: 0.5rem; flex: 1;">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; font-size: 0.82rem; background: #04070e; padding: 0.85rem; border-radius: 8px; margin-bottom: 1rem; border: 1px solid rgba(148,163,184,0.15);">
        <div><span style="color: #64748b;">Target Endpoint:</span> <strong id="ins_webhook_name" style="color: #f8fafc;"></strong></div>
        <div><span style="color: #64748b;">Event Type:</span> <span id="ins_event" class="ts-badge ts-badge-cyan" style="font-family: monospace;"></span></div>
        <div><span style="color: #64748b;">HTTP Response:</span> <strong id="ins_http_code"></strong></div>
        <div><span style="color: #64748b;">Roundtrip Latency:</span> <span id="ins_duration" style="font-family: monospace; color: #38bdf8;"></span></div>
        <div style="grid-column: span 2;"><span style="color: #64748b;">Delivery Timestamp:</span> <span id="ins_timestamp" style="font-family: monospace; color: #cbd5e1;"></span></div>
      </div>

      <h4 style="font-size: 0.85rem; color: #38bdf8; margin: 0 0 0.4rem 0;">Request JSON Payload Transmitted</h4>
      <pre id="ins_payload" style="background: #05070b; border: 1px solid rgba(148,163,184,0.18); border-radius: 6px; padding: 0.85rem; font-family: monospace; font-size: 0.78rem; color: #34d399; max-height: 200px; overflow-y: auto; white-space: pre-wrap; word-break: break-all; margin-bottom: 1rem;"></pre>

      <h4 style="font-size: 0.85rem; color: #38bdf8; margin: 0 0 0.4rem 0;">Target Server Response Body</h4>
      <pre id="ins_response" style="background: #05070b; border: 1px solid rgba(148,163,184,0.18); border-radius: 6px; padding: 0.85rem; font-family: monospace; font-size: 0.78rem; color: #cbd5e1; max-height: 150px; overflow-y: auto; white-space: pre-wrap; word-break: break-all;"></pre>
    </div>

    <div class="admin-modal-footer" style="margin-top: 1rem; padding-top: 0.75rem;">
      <button type="button" onclick="closeInspectModal()" class="ts-btn ts-btn-outline ts-btn-sm">Close Inspector</button>
    </div>
  </div>
</div>

<script>
function openNewWebhookModal() {
  document.getElementById('adminWebhookForm').reset();
  document.getElementById('wh_id').value = '';
  document.getElementById('webhookModal').style.display = 'flex';
}
function editWebhook(wh) {
  document.getElementById('adminWebhookForm').reset();
  document.getElementById('wh_id').value = wh.id;
  document.getElementById('wh_name').value = wh.name || '';
  document.getElementById('wh_target_url').value = wh.target_url || '';
  document.getElementById('wh_secret_key').value = wh.secret_key || '';
  
  // Uncheck all first
  document.querySelectorAll('#adminWebhookForm input[name="events[]"]').forEach(cb => cb.checked = false);
  
  let events = [];
  try {
    events = JSON.parse(wh.events_json || '[]');
  } catch(e) {}
  
  events.forEach(ev => {
    document.querySelectorAll('#adminWebhookForm input[name="events[]"]').forEach(cb => {
      if (cb.value === ev) cb.checked = true;
    });
  });
  
  document.getElementById('webhookModal').style.display = 'flex';
}
function closeWebhookModal() {
  document.getElementById('webhookModal').style.display = 'none';
}

function applyPythonAnywherePreset() {
  document.getElementById('wh_name').value = 'PythonAnywhere Mail & Telemetry Microservice';
  if (!document.getElementById('wh_target_url').value || document.getElementById('wh_target_url').value.includes('example.com')) {
    document.getElementById('wh_target_url').value = 'https://yourusername.pythonanywhere.com/api/v1/webhook/mail';
  }
  if (!document.getElementById('wh_secret_key').value) {
    document.getElementById('wh_secret_key').value = 'techscout_webhook_secure_secret_2026';
  }
  
  // Select essential events for Python microservice
  document.querySelectorAll('#adminWebhookForm input[name="events[]"]').forEach(cb => cb.checked = false);
  ['mail.dispatch', 'mail.sent', 'mail.failed', 'user.registered', 'author.applied', 'strike.issued'].forEach(ev => {
    document.querySelectorAll('#adminWebhookForm input[name="events[]"]').forEach(cb => {
      if (cb.value === ev) cb.checked = true;
    });
  });
}

function applyCloudflareWorkerPreset() {
  document.getElementById('wh_name').value = 'Cloudflare Worker Mail & Edge Proxy';
  if (!document.getElementById('wh_target_url').value || document.getElementById('wh_target_url').value.includes('example.com') || document.getElementById('wh_target_url').value.includes('pythonanywhere')) {
    document.getElementById('wh_target_url').value = 'https://techscout-mail-proxy.yoursubdomain.workers.dev/api/v1/webhook/mail';
  }
  if (!document.getElementById('wh_secret_key').value) {
    document.getElementById('wh_secret_key').value = 'techscout_webhook_secure_secret_2026';
  }
  
  // Select essential events
  document.querySelectorAll('#adminWebhookForm input[name="events[]"]').forEach(cb => cb.checked = false);
  ['mail.dispatch', 'mail.sent', 'mail.failed', 'user.registered', 'author.applied', 'strike.issued'].forEach(ev => {
    document.querySelectorAll('#adminWebhookForm input[name="events[]"]').forEach(cb => {
      if (cb.value === ev) cb.checked = true;
    });
  });
}

function retryDelivery(id) {
  if (!confirm('Re-send this webhook delivery payload now?')) return;
  fetch('<?= url('/admin/webhooks/retry/') ?>' + id, { method: 'POST' })
    .then(r => r.json())
    .then(d => {
      if (d.success) {
        alert('Retry executed! Target responded with HTTP ' + d.result.code + ' (' + d.result.duration_ms + 'ms)');
        location.reload();
      } else {
        alert('Retry failed: ' + (d.message || 'Error'));
      }
    })
    .catch(err => alert('Network error retrying delivery: ' + err));
}

function inspectDelivery(del) {
  document.getElementById('ins_webhook_name').innerText = del.webhook_name || 'Webhook #' + del.webhook_id;
  document.getElementById('ins_event').innerText = del.event || '';
  document.getElementById('ins_http_code').innerText = 'HTTP ' + (del.response_code || '0');
  document.getElementById('ins_http_code').style.color = (del.response_code >= 200 && del.response_code < 300) ? '#34d399' : '#f87171';
  document.getElementById('ins_duration').innerText = (del.duration_ms || 0) + 'ms';
  document.getElementById('ins_timestamp').innerText = del.created_at || '';

  // Format JSON payload nicely
  try {
    const parsed = JSON.parse(del.payload_json || '{}');
    document.getElementById('ins_payload').innerText = JSON.stringify(parsed, null, 2);
  } catch(e) {
    document.getElementById('ins_payload').innerText = del.payload_json || 'Empty payload';
  }

  document.getElementById('ins_response').innerText = del.response_body || 'No response body recorded';
  document.getElementById('deliveryInspectModal').style.display = 'flex';
}

function closeInspectModal() {
  document.getElementById('deliveryInspectModal').style.display = 'none';
}

function testWebhook(id) {
  fetch('<?= url('/admin/webhooks/test/') ?>' + id, { method: 'POST' })
    .then(r => r.json())
    .then(d => {
      if (d.success) {
        alert('Test Webhook Dispatched! Target responded with HTTP ' + d.result.code + ' (' + d.result.duration_ms + 'ms).\n\nCheck the Recent Outbound Delivery Logs table to inspect the payload.');
        location.reload();
      } else {
        alert('Test failed: ' + (d.message || 'Error'));
      }
    })
    .catch(err => alert('Network error executing test: ' + err));
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
