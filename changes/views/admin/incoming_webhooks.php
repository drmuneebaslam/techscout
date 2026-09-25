<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-main">
  <!-- Topbar -->
  <div class="admin-topbar">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-cyan">INCOMING DATA GATEWAY &bull; LIVE JSON LISTENER</span>
        <span style="font-size: 0.8rem; color: var(--admin-text-secondary);">Dynamic Database Field Mapper &amp; Ingestion Pipelines</span>
      </div>
      <h1 style="font-size: 1.6rem; color: var(--admin-text-primary); margin: 0; font-family: var(--admin-font-heading); font-weight: 800;">
        Incoming Webhooks &amp; Live JSON Listener
      </h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <a href="<?= url('/admin/webhooks') ?>" class="admin-btn admin-btn-outline admin-btn-sm" style="font-weight: 600;">
        <i data-lucide="arrow-up-right"></i> Outbound Webhooks
      </a>
      <button type="button" onclick="openNewIncomingModal()" class="admin-btn admin-btn-primary admin-btn-sm" style="font-weight: 700;">
        <i data-lucide="plus"></i> New Ingestion Webhook 🚀
      </button>
    </div>
  </div>

  <!-- Endpoints Overview Cards -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
    <?php if (!empty($incomingWebhooks)): ?>
      <?php foreach ($incomingWebhooks as $wh): 
        $endpointUrl = url('/api/v1/webhook/' . $wh['slug']);
        $rules = json_decode($wh['mapping_rules_json'] ?? '[]', true) ?: [];
      ?>
        <div class="admin-card" style="padding: 1.25rem; border-radius: 12px; border: 1px solid var(--admin-border-main); background: var(--admin-card-bg); display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
              <h3 style="font-size: 1.05rem; margin: 0; color: var(--admin-text-primary); font-weight: 700;">
                <?= htmlspecialchars($wh['name']) ?>
              </h3>
              <span class="ts-badge <?= !empty($wh['is_active']) ? 'ts-badge-green' : 'ts-badge-pink' ?>" style="font-size: 0.68rem;">
                <?= !empty($wh['is_active']) ? 'ACTIVE' : 'PAUSED' ?>
              </span>
            </div>

            <div style="display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.75rem; flex-wrap: wrap;">
              <span class="ts-badge ts-badge-cyan" style="font-size: 0.7rem; font-family: var(--admin-font-mono);">
                Table: <?= htmlspecialchars($wh['target_table']) ?>
              </span>
              <span class="ts-badge ts-badge-amber" style="font-size: 0.7rem;">
                Auth: <?= strtoupper(htmlspecialchars($wh['auth_type'])) ?>
              </span>
              <span class="ts-badge" style="font-size: 0.7rem; background: rgba(99,102,241,0.15); color: #818cf8;">
                <?= count($rules) ?> Mapped Fields
              </span>
            </div>

            <!-- Endpoint URL with Copy -->
            <div style="background: var(--admin-card-bg-elevated); border: 1px solid var(--admin-border-main); border-radius: 8px; padding: 0.5rem 0.75rem; display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.75rem;">
              <code style="font-size: 0.75rem; color: #38bdf8; font-family: var(--admin-font-mono); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1;">
                <?= htmlspecialchars($endpointUrl) ?>
              </code>
              <button type="button" onclick="copyToClipboard('<?= htmlspecialchars(addslashes($endpointUrl)) ?>')" class="admin-btn admin-btn-outline admin-btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;" title="Copy Endpoint URL">
                <i data-lucide="copy" style="width: 12px; height: 12px;"></i>
              </button>
            </div>

            <div style="font-size: 0.75rem; color: var(--admin-text-secondary); margin-bottom: 0.75rem;">
              Received: <strong><?= number_format((int)($wh['received_count'] ?? 0)) ?></strong> payloads &bull; Last: <?= !empty($wh['last_received_at']) ? htmlspecialchars($wh['last_received_at']) : 'Never' ?>
            </div>
          </div>

          <div style="display: flex; gap: 0.5rem; border-top: 1px solid var(--admin-border-main); padding-top: 0.75rem;">
            <button type="button" onclick='openLiveListener(<?= json_encode($wh) ?>)' class="admin-btn admin-btn-primary admin-btn-sm" style="flex: 1; font-weight: 700; font-size: 0.78rem;">
              <i data-lucide="radio"></i> Live Listener &amp; Mapper
            </button>
            <form action="<?= url('/admin/incoming-webhooks/toggle/' . $wh['id']) ?>" method="POST" style="margin: 0;">
              <button type="submit" class="admin-btn admin-btn-outline admin-btn-sm" title="Toggle Active Status">
                <i data-lucide="<?= !empty($wh['is_active']) ? 'pause' : 'play' ?>" style="width: 14px; height: 14px;"></i>
              </button>
            </form>
            <form action="<?= url('/admin/incoming-webhooks/delete/' . $wh['id']) ?>" method="POST" onsubmit="return confirm('Permanently delete this incoming webhook endpoint?');" style="margin: 0;">
              <button type="submit" class="admin-btn admin-btn-outline admin-btn-sm" style="color: #ef4444;" title="Delete Webhook">
                <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="admin-card" style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
        <i data-lucide="inbox" style="width: 44px; height: 44px; color: var(--admin-text-secondary); margin-bottom: 0.75rem;"></i>
        <h3 style="color: var(--admin-text-primary); margin: 0 0 0.5rem 0;">No Ingestion Webhooks Configured</h3>
        <p style="color: var(--admin-text-secondary); font-size: 0.9rem; margin-bottom: 1.25rem;">Create a custom webhook URL to ingest JSON data from Zapier, Airtable, Stripe, GitHub, or custom microservices.</p>
        <button type="button" onclick="openNewIncomingModal()" class="admin-btn admin-btn-primary">Create First Ingestion Webhook</button>
      </div>
    <?php endif; ?>
  </div>

  <!-- Interactive Live JSON Listener & Dynamic Field Mapper Studio -->
  <div id="listenerStudio" class="admin-table-wrap" style="padding: 1.75rem; margin-bottom: 2.5rem; display: none; border: 2px solid var(--ts-primary, #6366f1); border-radius: 14px; background: var(--admin-card-bg);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <div style="width: 12px; height: 12px; border-radius: 50%; background: #10b981; animation: pulseGlow 1.5s infinite;"></div>
        <div>
          <h2 style="font-size: 1.25rem; margin: 0; color: var(--admin-text-primary); font-family: var(--admin-font-heading);">
            Live JSON Listener: <span id="studioWebhookName" style="color: #38bdf8;"></span>
          </h2>
          <span style="font-size: 0.78rem; color: var(--admin-text-secondary);">Target DB Table: <strong id="studioTargetTable" style="color: #a855f7;"></strong> &bull; Endpoint: <code id="studioEndpointUrl" style="color: #38bdf8;"></code></span>
        </div>
      </div>

      <div style="display: flex; gap: 0.5rem; align-items: center;">
        <button type="button" onclick="pollLatestLogs()" class="admin-btn admin-btn-outline admin-btn-sm" style="font-size: 0.75rem;">
          <i data-lucide="refresh-cw"></i> Refresh Stream
        </button>
        <button type="button" onclick="closeLiveListener()" class="admin-btn admin-btn-outline admin-btn-sm" style="color: #ef4444;">
          &times; Close Studio
        </button>
      </div>
    </div>

    <!-- Dual Pane Studio Grid: Left = Live Ingestion Stream / Test Simulator | Right = Interactive Field Mapper -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
      <!-- Left Column: Live JSON Payload Stream & Simulator -->
      <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
          <h4 style="font-size: 0.92rem; color: var(--admin-text-primary); margin: 0; display: flex; align-items: center; gap: 0.4rem;">
            <i data-lucide="terminal" style="width: 15px; color: #38bdf8;"></i> Incoming Payloads Stream
          </h4>
          <span id="streamCounter" class="ts-badge ts-badge-cyan" style="font-size: 0.68rem;">0 payloads received</span>
        </div>

        <!-- Real-time Payload Viewer Box -->
        <div id="livePayloadViewer" style="height: 280px; overflow-y: auto; background: #050811; border: 1px solid var(--admin-border-main); border-radius: 8px; padding: 1rem; font-family: var(--admin-font-mono); font-size: 0.8rem; color: #34d399; margin-bottom: 1rem;">
          <span style="color: #64748b;">// Waiting for incoming HTTP POST requests to this webhook...</span><br>
          <span style="color: #64748b;">// Click "Send Test Payload" below to simulate an incoming event.</span>
        </div>

        <!-- Simulator Form -->
        <div style="background: var(--admin-card-bg-elevated); border: 1px solid var(--admin-border-main); border-radius: 8px; padding: 1rem;">
          <div style="font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.5rem; display: flex; justify-content: space-between;">
            <span>⚡ Send Test Simulation Payload</span>
            <a href="javascript:void(0)" onclick="loadSamplePayload()" style="font-size: 0.75rem; color: #38bdf8; text-decoration: none;">Load Sample Data</a>
          </div>
          <textarea id="simPayloadInput" rows="5" class="admin-input" style="font-family: var(--admin-font-mono); font-size: 0.78rem; width: 100%; margin-bottom: 0.6rem;"><?= htmlspecialchars(json_encode([
            "title" => "Autonomous AI Agent Hub",
            "summary" => "State-of-the-art framework for orchestrating multi-agent systems.",
            "content_type" => "ai_tool",
            "status" => "published",
            "metadata" => [
              "author" => "Engineering Lab",
              "stars" => 4500
            ]
          ], JSON_PRETTY_PRINT)) ?></textarea>
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <button type="button" onclick="sendTestSimulation()" class="admin-btn admin-btn-primary admin-btn-sm" style="font-weight: 700;">
              🚀 Execute Test Dispatch
            </button>
            <span id="simStatusMsg" style="font-size: 0.78rem; color: var(--admin-text-secondary);"></span>
          </div>
        </div>
      </div>

      <!-- Right Column: Dynamic Field Mapper to Database -->
      <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
          <h4 style="font-size: 0.92rem; color: var(--admin-text-primary); margin: 0; display: flex; align-items: center; gap: 0.4rem;">
            <i data-lucide="database" style="width: 15px; color: #10b981;"></i> Dynamic Field Mapping Matrix
          </h4>
          <button type="button" onclick="addMappingRow()" class="admin-btn admin-btn-outline admin-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem;">
            + Add Field Map
          </button>
        </div>

        <p style="font-size: 0.78rem; color: var(--admin-text-secondary); margin: 0 0 0.85rem 0;">
          Map incoming JSON keys (supports dotted nesting like <code>metadata.author</code>) to target table columns.
        </p>

        <form id="mappingForm" onsubmit="saveFieldMappings(event)">
          <input type="hidden" id="mappingWebhookId" name="webhook_id">
          
          <div id="mappingRowsContainer" style="display: flex; flex-direction: column; gap: 0.6rem; max-height: 340px; overflow-y: auto; padding-right: 0.4rem; margin-bottom: 1rem;">
            <!-- Rows injected dynamically via JS -->
          </div>

          <button type="submit" class="admin-btn admin-btn-primary" style="width: 100%; font-weight: 700; padding: 0.65rem;">
            💾 Save Field Mapping Rules
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Recent Ingestion Activity Log Table -->
  <div class="admin-table-wrap" style="padding: 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
      <h3 style="font-size: 1.15rem; color: var(--admin-text-primary); margin: 0;">Recent Incoming Execution Logs</h3>
      <form action="<?= url('/admin/incoming-webhooks/clear-logs') ?>" method="POST" onsubmit="return confirm('Clear all incoming webhook execution logs?');" style="margin: 0;">
        <button type="submit" class="admin-btn admin-btn-outline admin-btn-sm" style="color: #ef4444; font-size: 0.75rem;">
          <i data-lucide="trash"></i> Clear Logs
        </button>
      </form>
    </div>

    <table class="admin-table">
      <thead>
        <tr>
          <th>Timestamp</th>
          <th>Endpoint Slug</th>
          <th>Status</th>
          <th>HTTP Code</th>
          <th>Affected Rows</th>
          <th>Client IP</th>
          <th>Payload Preview</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($incomingLogs)): ?>
          <?php foreach ($incomingLogs as $log): ?>
            <tr>
              <td>
                <span style="font-size: 0.8rem; font-family: var(--admin-font-mono); color: var(--admin-text-secondary);">
                  <?= htmlspecialchars($log['created_at']) ?>
                </span>
              </td>
              <td>
                <span class="ts-badge ts-badge-cyan" style="font-family: var(--admin-font-mono); font-size: 0.72rem;">
                  /<?= htmlspecialchars($log['endpoint_slug']) ?>
                </span>
              </td>
              <td>
                <span class="ts-badge <?= $log['status'] === 'success' ? 'ts-badge-green' : 'ts-badge-pink' ?>">
                  <?= strtoupper(htmlspecialchars($log['status'])) ?>
                </span>
              </td>
              <td>
                <code style="font-size: 0.8rem; font-weight: 700; color: <?= $log['response_code'] < 400 ? '#10b981' : '#ef4444' ?>;">
                  <?= (int)$log['response_code'] ?>
                </code>
              </td>
              <td>
                <span style="font-size: 0.85rem; color: var(--admin-text-primary); font-weight: 600;">
                  <?= (int)$log['records_affected'] ?>
                </span>
              </td>
              <td>
                <span style="font-size: 0.78rem; color: var(--admin-text-secondary); font-family: var(--admin-font-mono);">
                  <?= htmlspecialchars($log['ip_address'] ?? '127.0.0.1') ?>
                </span>
              </td>
              <td>
                <div style="max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-family: var(--admin-font-mono); font-size: 0.75rem; color: #94a3b8;" title="<?= htmlspecialchars($log['payload_json']) ?>">
                  <?= htmlspecialchars(substr($log['payload_json'], 0, 80)) ?>...
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" style="text-align: center; padding: 2rem; color: var(--admin-text-secondary);">No incoming webhook payloads logged yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal: Create New Incoming Webhook -->
<div id="newIncomingModal" class="admin-modal" style="display: none;">
  <div class="admin-modal-backdrop" onclick="closeNewIncomingModal()"></div>
  <div class="admin-modal-content" style="max-width: 540px;">
    <div class="admin-modal-header">
      <h3 style="margin: 0; color: var(--admin-text-primary);">Create Ingestion Webhook Endpoint</h3>
      <button type="button" onclick="closeNewIncomingModal()" class="admin-modal-close">&times;</button>
    </div>
    <form action="<?= url('/admin/incoming-webhooks/save') ?>" method="POST" style="padding: 1.25rem;">
      <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Webhook Name</label>
        <input type="text" name="name" placeholder="e.g. Stripe Donations Sync, GitHub Release Bot" class="admin-input" required>
      </div>

      <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Endpoint Slug (URL Path)</label>
        <div style="display: flex; align-items: center; gap: 0.4rem;">
          <span style="font-size: 0.82rem; color: var(--admin-text-secondary); font-family: var(--admin-font-mono);"><?= url('/api/v1/webhook/') ?></span>
          <input type="text" name="slug" placeholder="zapier-tools" class="admin-input" style="flex: 1;" required pattern="[a-z0-9_-]+">
        </div>
      </div>

      <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Target Database Table</label>
        <select name="target_table" class="admin-input" required>
          <?php foreach ($permittedTables as $tblName => $tblMeta): 
            $colSlice = array_slice($tblMeta['columns'] ?? [], 0, 6);
            $colPreview = implode(', ', $colSlice);
            if (count($tblMeta['columns'] ?? []) > 6) $colPreview .= '...';
          ?>
            <option value="<?= htmlspecialchars($tblName) ?>"><?= htmlspecialchars($tblName) ?> (<?= htmlspecialchars($colPreview) ?> &bull; <?= (int)($tblMeta['total_rows'] ?? 0) ?> rows)</option>
          <?php endforeach; ?>
        </select>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Authentication Type</label>
          <select name="auth_type" class="admin-input">
            <option value="none">None (Public Open Ingest)</option>
            <option value="bearer">Bearer Token (Authorization Header)</option>
            <option value="signature">HMAC-SHA256 (X-Ts-Signature Header)</option>
          </select>
        </div>
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Secret / Bearer Key</label>
          <input type="text" name="secret_key" value="whsec_ts_live_<?= bin2hex(random_bytes(16)) ?>" class="admin-input" style="font-family: var(--admin-font-mono); font-size: 0.78rem;">
        </div>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
        <button type="button" onclick="closeNewIncomingModal()" class="admin-btn admin-btn-outline">Cancel</button>
        <button type="submit" class="admin-btn admin-btn-primary" style="font-weight: 700;">Create Ingestion Webhook 🚀</button>
      </div>
    </form>
  </div>
</div>

<style>
@keyframes pulseGlow {
  0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
  70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
  100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}
</style>

<script>
let currentActiveWebhook = null;
let pollInterval = null;
let lastLogId = 0;
const PERMITTED_TABLES = <?= json_encode($permittedTables, JSON_HEX_TAG | JSON_HEX_AMP) ?>;

function openNewIncomingModal() {
  document.getElementById('newIncomingModal').style.display = 'flex';
}
function closeNewIncomingModal() {
  document.getElementById('newIncomingModal').style.display = 'none';
}

function openLiveListener(wh) {
  currentActiveWebhook = wh;
  document.getElementById('studioWebhookName').textContent = wh.name;
  document.getElementById('studioTargetTable').textContent = wh.target_table;
  document.getElementById('studioEndpointUrl').textContent = '<?= url('/api/v1/webhook/') ?>' + wh.slug;
  document.getElementById('mappingWebhookId').value = wh.id;
  document.getElementById('listenerStudio').style.display = 'block';
  document.getElementById('listenerStudio').scrollIntoView({ behavior: 'smooth' });

  // Render Existing Mapping Rules
  renderMappingRows(wh.mapping_rules_json, wh.target_table);

  // Poll for logs
  lastLogId = 0;
  pollLatestLogs();
  if (pollInterval) clearInterval(pollInterval);
  pollInterval = setInterval(pollLatestLogs, 3000);
}

function closeLiveListener() {
  document.getElementById('listenerStudio').style.display = 'none';
  if (pollInterval) clearInterval(pollInterval);
  currentActiveWebhook = null;
}

function renderMappingRows(rulesJson, targetTable) {
  const container = document.getElementById('mappingRowsContainer');
  container.innerHTML = '';

  let rules = [];
  try {
    rules = typeof rulesJson === 'string' ? JSON.parse(rulesJson) : rulesJson;
  } catch(e) {}
  if (!Array.isArray(rules) || !rules.length) {
    rules = [{ json_key: 'title', target_column: 'title' }];
  }

  const tableDef = PERMITTED_TABLES[targetTable] || { columns: ['title', 'slug', 'summary'] };

  rules.forEach((r, idx) => {
    container.appendChild(createRowElement(r.json_key || '', r.target_column || '', tableDef.columns));
  });
}

function addMappingRow() {
  const container = document.getElementById('mappingRowsContainer');
  const targetTable = currentActiveWebhook ? currentActiveWebhook.target_table : 'contents';
  const tableDef = PERMITTED_TABLES[targetTable] || { columns: ['title', 'slug', 'summary'] };
  container.appendChild(createRowElement('', '', tableDef.columns));
}

function createRowElement(jsonKey, targetCol, columns) {
  const div = document.createElement('div');
  div.style.cssText = 'display: flex; gap: 0.5rem; align-items: center; background: var(--admin-card-bg-elevated); padding: 0.4rem 0.6rem; border-radius: 6px; border: 1px solid var(--admin-border-main);';

  let optionsHtml = '';
  columns.forEach(col => {
    optionsHtml += `<option value="${col}" ${col === targetCol ? 'selected' : ''}>${col}</option>`;
  });

  div.innerHTML = `
    <input type="text" name="json_keys[]" value="${escapeHtml(jsonKey)}" placeholder="e.g. data.title" class="admin-input" style="flex: 1; font-family: var(--admin-font-mono); font-size: 0.78rem;" required>
    <span style="color: #64748b;">&rarr;</span>
    <select name="target_cols[]" class="admin-input" style="flex: 1; font-size: 0.78rem;">
      ${optionsHtml}
    </select>
    <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 0.2rem 0.4rem; font-size: 1rem;">&times;</button>
  `;
  return div;
}

function escapeHtml(text) {
  if (text === null || text === undefined) return '';
  return String(text).replace(/[&<>"']/g, function(m) {
    return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
  });
}

async function saveFieldMappings(e) {
  e.preventDefault();
  if (!currentActiveWebhook) return;

  const form = document.getElementById('mappingForm');
  const fd = new FormData(form);
  const jsonKeys = fd.getAll('json_keys[]');
  const targetCols = fd.getAll('target_cols[]');

  const rules = [];
  for (let i = 0; i < jsonKeys.length; i++) {
    if (jsonKeys[i].trim() !== '') {
      rules.push({ json_key: jsonKeys[i].trim(), target_column: targetCols[i] });
    }
  }

  const res = await fetch('<?= url('/admin/incoming-webhooks/save-mappings') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      id: currentActiveWebhook.id,
      mapping_rules_json: JSON.stringify(rules)
    })
  });
  const data = await res.json();
  if (data.success) {
    currentActiveWebhook.mapping_rules_json = JSON.stringify(rules);
    alert('✓ Mapping rules saved successfully!');
  } else {
    alert('Error saving mappings: ' + (data.error || 'Unknown error'));
  }
}

async function pollLatestLogs() {
  if (!currentActiveWebhook) return;
  try {
    const res = await fetch('<?= url('/api/v1/webhook/live-stream/') ?>' + currentActiveWebhook.slug + '?since_id=' + lastLogId);
    const data = await res.json();
    if (data.success && data.logs && data.logs.length) {
      const viewer = document.getElementById('livePayloadViewer');
      data.logs.forEach(log => {
        if (log.id > lastLogId) lastLogId = log.id;
        const entry = document.createElement('div');
        entry.style.cssText = 'border-bottom: 1px dashed rgba(148,163,184,0.15); padding: 0.5rem 0;';
        entry.innerHTML = `
          <div style="color: #38bdf8; font-size: 0.72rem;">[${log.created_at}] Status: ${log.status.toUpperCase()} &bull; Code: ${log.response_code} &bull; Affected: ${log.records_affected}</div>
          <pre style="margin: 0.2rem 0; color: #f8fafc; font-size: 0.75rem; white-space: pre-wrap;">${escapeHtml(log.payload_json)}</pre>
        `;
        viewer.prepend(entry);
      });
      document.getElementById('streamCounter').textContent = data.logs.length + ' payloads synced';
    }
  } catch(e) {}
}

async function sendTestSimulation() {
  if (!currentActiveWebhook) return;
  const rawBody = document.getElementById('simPayloadInput').value;
  const statusEl = document.getElementById('simStatusMsg');
  statusEl.textContent = 'Sending payload...';

  try {
    const fd = new FormData();
    fd.append('slug', currentActiveWebhook.slug);
    fd.append('payload', rawBody);
    if (currentActiveWebhook.auth_type === 'bearer') {
      fd.append('auth_header', 'Bearer ' + currentActiveWebhook.secret_key);
    }

    const res = await fetch('<?= url('/api/v1/webhook/simulate') ?>', {
      method: 'POST',
      body: fd
    });
    const data = await res.json();
    if (data.success) {
      statusEl.innerHTML = `<span style="color: #10b981;">✓ 200 OK: Processed (${data.records_affected} rows affected)</span>`;
      pollLatestLogs();
    } else {
      statusEl.innerHTML = `<span style="color: #ef4444;">✗ ${data.code || 400}: ${data.error}</span>`;
    }
  } catch(e) {
    statusEl.innerHTML = `<span style="color: #ef4444;">Error: ${e.message}</span>`;
  }
}

function loadSamplePayload() {
  document.getElementById('simPayloadInput').value = JSON.stringify({
    "title": "State-of-the-Art Deep Learning Framework",
    "summary": "Accelerated neural network inference engine with dynamic quantization.",
    "content_type": "ai_tool",
    "status": "published",
    "website_url": "https://github.com/techscout/inference-core",
    "pricing_type": "free"
  }, null, 2);
}

function copyToClipboard(text) {
  navigator.clipboard.writeText(text).then(() => {
    alert('✓ Webhook endpoint copied to clipboard:\n' + text);
  });
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
