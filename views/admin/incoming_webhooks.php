<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-main">
  <!-- Topbar -->
  <div class="admin-topbar">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-cyan">100X INGESTION GATEWAY &bull; MULTI-TABLE SYNC</span>
        <span style="font-size: 0.8rem; color: var(--admin-text-secondary);">Dynamic Database Field Mapper, Schema Blueprints &amp; Live Ingestion Pipelines</span>
      </div>
      <h1 style="font-size: 1.6rem; color: var(--admin-text-primary); margin: 0; font-family: var(--admin-font-heading); font-weight: 800;">
        Incoming Webhooks &amp; Live Ingestion Gateway
      </h1>
    </div>

    <div style="display: flex; gap: 0.6rem; flex-wrap: wrap; align-items: center;">
      <a href="<?= url('/admin/webhooks') ?>" class="admin-btn admin-btn-outline admin-btn-sm" style="font-weight: 600;">
        <i data-lucide="arrow-up-right"></i> Outbound Webhooks
      </a>
      <button type="button" onclick="openTutorialModal()" class="admin-btn admin-btn-outline admin-btn-sm" style="font-weight: 600; color: #38bdf8; border-color: rgba(56,189,248,0.4);">
        <i data-lucide="book-open"></i> Ingestion Tutorial 📘
      </button>
      <button type="button" onclick="openSampleGeneratorModal()" class="admin-btn admin-btn-outline admin-btn-sm" style="font-weight: 600; color: #a855f7; border-color: rgba(168,85,247,0.4);">
        <i data-lucide="sparkles"></i> All Tables Sample Data ⚡
      </button>
      <button type="button" onclick="openNewIncomingModal()" class="admin-btn admin-btn-primary admin-btn-sm" style="font-weight: 700;">
        <i data-lucide="plus"></i> New Ingestion Webhook 🚀
      </button>
    </div>
  </div>

  <!-- Endpoints Overview Cards -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
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
              <span class="ts-badge ts-badge-green" style="font-size: 0.7rem;">
                Multi-Table Ready
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

          <div style="display: flex; gap: 0.4rem; border-top: 1px solid var(--admin-border-main); padding-top: 0.75rem; flex-wrap: wrap;">
            <button type="button" onclick="openLiveListener(<?= htmlspecialchars(json_encode($wh, JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') ?>)" class="admin-btn admin-btn-primary admin-btn-sm" style="flex: 1; font-weight: 700; font-size: 0.76rem;">
              <i data-lucide="radio"></i> Live Listener
            </button>
            <button type="button" onclick="openEditIncomingModal(<?= htmlspecialchars(json_encode($wh, JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') ?>)" class="admin-btn admin-btn-outline admin-btn-sm" style="font-size: 0.76rem; color: #38bdf8;" title="Edit Webhook Settings">
              <i data-lucide="edit-3" style="width: 13px; height: 13px;"></i> Edit
            </button>
            <button type="button" onclick="quickLoadTableSample('<?= htmlspecialchars(addslashes($wh['target_table'])) ?>')" class="admin-btn admin-btn-outline admin-btn-sm" style="font-size: 0.76rem; color: #a855f7;" title="Load Sample Payload for <?= htmlspecialchars($wh['target_table']) ?>">
              <i data-lucide="code" style="width: 13px; height: 13px;"></i> Sample
            </button>
            <form action="<?= url('/admin/incoming-webhooks/toggle/' . $wh['id']) ?>" method="POST" style="margin: 0;">
              <button type="submit" class="admin-btn admin-btn-outline admin-btn-sm" title="Toggle Active Status">
                <i data-lucide="<?= !empty($wh['is_active']) ? 'pause' : 'play' ?>" style="width: 13px; height: 13px;"></i>
              </button>
            </form>
            <form action="<?= url('/admin/incoming-webhooks/delete/' . $wh['id']) ?>" method="POST" onsubmit="return confirm('Permanently delete this incoming webhook endpoint?');" style="margin: 0;">
              <button type="submit" class="admin-btn admin-btn-outline admin-btn-sm" style="color: #ef4444;" title="Delete Webhook">
                <i data-lucide="trash-2" style="width: 13px; height: 13px;"></i>
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

      <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
        <button type="button" onclick="pollLatestLogs()" class="admin-btn admin-btn-outline admin-btn-sm" style="font-size: 0.75rem;">
          <i data-lucide="refresh-cw"></i> Refresh Stream
        </button>
        <button type="button" onclick="openTutorialModal()" class="admin-btn admin-btn-outline admin-btn-sm" style="font-size: 0.75rem; color: #38bdf8;">
          <i data-lucide="help-circle"></i> Ingestion Guide
        </button>
        <button type="button" onclick="closeLiveListener()" class="admin-btn admin-btn-outline admin-btn-sm" style="color: #ef4444;">
          &times; Close Studio
        </button>
      </div>
    </div>

    <!-- Dual Pane Studio Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 1.5rem;">
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
          <div style="font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
            <span>⚡ Send Test Simulation Payload</span>
            <div style="display: flex; gap: 0.5rem;">
              <a href="javascript:void(0)" onclick="loadTableSampleIntoSimulator()" style="font-size: 0.75rem; color: #38bdf8; text-decoration: none;">Load Table Sample</a>
              <span style="color: var(--admin-text-secondary);">&bull;</span>
              <a href="javascript:void(0)" onclick="loadMultiTableSampleIntoSimulator()" style="font-size: 0.75rem; color: #a855f7; text-decoration: none;">Multi-Table Sample</a>
            </div>
          </div>
          <textarea id="simPayloadInput" rows="6" class="admin-input" style="font-family: var(--admin-font-mono); font-size: 0.78rem; width: 100%; margin-bottom: 0.6rem;"></textarea>
          <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
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
          Map incoming JSON keys (supports dotted nesting like <code>metadata.author</code>) to target table columns. Unmapped matching keys automatically map.
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
                <span class="ts-badge <?= in_array(strtolower($log['status']), ['success', 'processed']) ? 'ts-badge-green' : 'ts-badge-pink' ?>">
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

<!-- ========================================================================= -->
<!-- MODAL 1: CREATE NEW INGESTION WEBHOOK                                     -->
<!-- ========================================================================= -->
<div id="newIncomingModal" class="admin-modal" style="display: none;">
  <div class="admin-modal-backdrop" onclick="closeNewIncomingModal()"></div>
  <div class="admin-modal-content" style="max-width: 580px;">
    <div class="admin-modal-header">
      <h3 style="margin: 0; color: var(--admin-text-primary); font-size: 1.15rem; display: flex; align-items: center; gap: 0.5rem;">
        <i data-lucide="plus-circle" style="color: #38bdf8;"></i> Create Ingestion Webhook Endpoint
      </h3>
      <button type="button" onclick="closeNewIncomingModal()" class="admin-modal-close">&times;</button>
    </div>
    <form action="<?= url('/admin/incoming-webhooks/save') ?>" method="POST" style="padding: 1.25rem;">
      <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Webhook Name</label>
        <input type="text" name="name" id="new_wh_name" placeholder="e.g. Midjourney Prompts Ingest, Stripe Donations Sync" class="admin-input" required oninput="autoSuggestSlug(this.value, 'new_wh_slug')">
      </div>

      <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Endpoint Slug (URL Path)</label>
        <div style="display: flex; align-items: center; gap: 0.4rem;">
          <span style="font-size: 0.82rem; color: var(--admin-text-secondary); font-family: var(--admin-font-mono);"><?= url('/api/v1/webhook/') ?></span>
          <input type="text" name="slug" id="new_wh_slug" placeholder="midjourney-prompts" class="admin-input" style="flex: 1;" required pattern="[a-z0-9_-]+">
        </div>
      </div>

      <div style="margin-bottom: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
          <label style="font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin: 0;">Target Database Table</label>
          <a href="javascript:void(0)" onclick="quickLoadTableSample(document.getElementById('new_wh_table').value)" style="font-size: 0.72rem; color: #38bdf8; text-decoration: none;">View Sample JSON</a>
        </div>
        <select name="target_table" id="new_wh_table" class="admin-input" required>
          <?php foreach ($permittedTables as $tblName => $tblMeta): 
            $colSlice = array_slice($tblMeta['columns'] ?? [], 0, 5);
            $colPreview = implode(', ', $colSlice);
            if (count($tblMeta['columns'] ?? []) > 5) $colPreview .= '...';
          ?>
            <option value="<?= htmlspecialchars($tblName) ?>" <?= $tblName === 'prompts' ? 'selected' : '' ?>>
              <?= htmlspecialchars($tblName) ?> (<?= htmlspecialchars($colPreview) ?> &bull; <?= (int)($tblMeta['total_rows'] ?? 0) ?> rows)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Authentication Type</label>
          <select name="auth_type" class="admin-input">
            <option value="none">None (Public Open Ingest)</option>
            <option value="bearer" selected>Bearer Token (Authorization Header)</option>
            <option value="signature">HMAC-SHA256 (X-Ts-Signature Header)</option>
          </select>
        </div>
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Secret / Bearer Key</label>
          <input type="text" name="secret_key" value="whsec_ts_live_<?= bin2hex(random_bytes(16)) ?>" class="admin-input" style="font-family: var(--admin-font-mono); font-size: 0.78rem;">
        </div>
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="closeNewIncomingModal()" class="admin-btn admin-btn-outline">Cancel</button>
        <button type="submit" class="admin-btn admin-btn-primary" style="font-weight: 700;">Create Ingestion Webhook 🚀</button>
      </div>
    </form>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: 100X ENHANCED TABBED WEBHOOK STUDIO (STRICT VALIDATION & MAPPING) -->
<!-- ========================================================================= -->
<div id="editIncomingModal" class="admin-modal" style="display: none; position: fixed; inset: 0; z-index: 99999; background: rgba(3, 6, 12, 0.88); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); align-items: center; justify-content: center; padding: 1.25rem;">
  <div class="admin-modal-backdrop" onclick="closeEditIncomingModal()" style="position: absolute; inset: 0; z-index: 1;"></div>
  <div class="admin-modal-content" style="position: relative; z-index: 2; width: 100%; max-width: 900px; max-height: 90vh; display: flex; flex-direction: column; background: var(--admin-bg-surface); border: 1px solid var(--admin-border-main); border-radius: 16px; box-shadow: 0 25px 60px rgba(0,0,0,0.8), 0 0 30px rgba(99,102,241,0.2); overflow: hidden; padding: 0;">
    
    <!-- Modal Header -->
    <div class="admin-modal-header" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--admin-border-main); display: flex; justify-content: space-between; align-items: center; background: var(--admin-bg-elevated);">
      <div style="display: flex; align-items: center; gap: 0.65rem;">
        <span class="ts-badge ts-badge-cyan" style="font-size: 0.72rem; font-weight: 700;">WEBHOOK STUDIO</span>
        <h3 style="margin: 0; color: var(--admin-text-primary); font-size: 1.2rem; font-family: var(--admin-font-heading); font-weight: 800;" id="editModalHeading">
          Manage Ingestion Webhook
        </h3>
      </div>
      <button type="button" onclick="closeEditIncomingModal()" class="admin-modal-close" style="background: none; border: none; font-size: 1.5rem; color: var(--admin-text-secondary); cursor: pointer; line-height: 1;">&times;</button>
    </div>

    <!-- Studio Tab Navigation Bar -->
    <div style="display: flex; gap: 0.5rem; padding: 0.65rem 1.5rem; border-bottom: 1px solid var(--admin-border-main); background: var(--admin-bg-surface); overflow-x: auto; flex-shrink: 0;">
      <button type="button" onclick="switchWebhookTab('general')" id="whTabBtn_general" class="admin-btn admin-btn-primary admin-btn-sm" style="font-weight: 700;">
        ⚙️ General &amp; Auth
      </button>
      <button type="button" onclick="switchWebhookTab('captured')" id="whTabBtn_captured" class="admin-btn admin-btn-outline admin-btn-sm" style="font-weight: 700; color: #38bdf8;">
        📡 Captured Payloads &amp; Detected Keys
      </button>
      <button type="button" onclick="switchWebhookTab('mapping')" id="whTabBtn_mapping" class="admin-btn admin-btn-outline admin-btn-sm" style="font-weight: 700; color: #10b981;">
        🗂️ Field Mapping &amp; Strict Validation
      </button>
      <button type="button" onclick="switchWebhookTab('simulator')" id="whTabBtn_simulator" class="admin-btn admin-btn-outline admin-btn-sm" style="font-weight: 700; color: #a855f7;">
        ⚡ Test Sandbox
      </button>
    </div>

    <!-- Scrollable Body for Tabs -->
    <div style="padding: 1.5rem; overflow-y: auto; flex: 1;">
      
      <!-- TAB 1: General & Auth Settings -->
      <div id="whTabPane_general">
        <form id="editGeneralForm" onsubmit="submitEditWebhookForm(event)">
          <input type="hidden" name="id" id="edit_wh_id">
          <input type="hidden" name="mapping_rules_json" id="edit_wh_mapping_rules_json">

          <div style="margin-bottom: 1.15rem;">
            <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Webhook Name</label>
            <input type="text" name="name" id="edit_wh_name" class="admin-input" required placeholder="e.g. Midjourney Prompts Ingest">
          </div>

          <div style="margin-bottom: 1.15rem;">
            <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Endpoint Slug (URL Path)</label>
            <div style="display: flex; align-items: center; gap: 0.4rem;">
              <span style="font-size: 0.82rem; color: var(--admin-text-secondary); font-family: var(--admin-font-mono);"><?= url('/api/v1/webhook/') ?></span>
              <input type="text" name="slug" id="edit_wh_slug" class="admin-input" style="flex: 1;" required pattern="[a-z0-9_-]+">
            </div>
          </div>

          <div style="margin-bottom: 1.15rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
              <label style="font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin: 0;">Target Database Table</label>
              <span style="font-size: 0.72rem; color: var(--admin-text-secondary);">Table where records will be validated and inserted</span>
            </div>
            <select name="target_table" id="edit_wh_table" class="admin-input" onchange="onTargetTableChanged(this.value)" required>
              <?php foreach ($permittedTables as $tblName => $tblMeta): ?>
                <option value="<?= htmlspecialchars($tblName) ?>"><?= htmlspecialchars($tblName) ?> (<?= count($tblMeta['columns'] ?? []) ?> columns)</option>
              <?php endforeach; ?>
            </select>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.15rem;">
            <div>
              <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Authentication Type</label>
              <select name="auth_type" id="edit_wh_auth" class="admin-input">
                <option value="none">None (Public Open Ingest)</option>
                <option value="bearer">Bearer Token (Authorization Header)</option>
                <option value="signature">HMAC-SHA256 (X-Ts-Signature Header)</option>
              </select>
            </div>
            <div>
              <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Secret / Bearer Key</label>
              <input type="text" name="secret_key" id="edit_wh_secret" class="admin-input" style="font-family: var(--admin-font-mono); font-size: 0.78rem;">
            </div>
          </div>

          <div style="margin-bottom: 1.5rem;">
            <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; font-weight: 600; cursor: pointer; color: var(--admin-text-primary);">
              <input type="checkbox" name="is_active" id="edit_wh_active" value="1">
              Active Endpoint (Accept and process incoming HTTP POST payloads)
            </label>
          </div>

          <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--admin-border-main); padding-top: 1.25rem;">
            <button type="button" onclick="closeEditIncomingModal()" class="admin-btn admin-btn-outline">Cancel</button>
            <button type="submit" class="admin-btn admin-btn-primary" style="font-weight: 700;">Save Webhook Settings 💾</button>
          </div>
        </form>
      </div>

      <!-- TAB 2: Captured Payloads & Detected Keys -->
      <div id="whTabPane_captured" style="display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
          <div>
            <h4 style="margin: 0; color: #38bdf8; font-size: 1rem;">Detected Keys from Actual Payloads</h4>
            <p style="margin: 0.25rem 0 0 0; font-size: 0.78rem; color: var(--admin-text-secondary);">
              Inspect the exact JSON keys received in real HTTP requests to this webhook endpoint. Click "+ Map This Field" to create a strict validation &amp; insertion mapping.
            </p>
          </div>
          <button type="button" onclick="reloadCapturedKeysForActiveWebhook()" class="admin-btn admin-btn-outline admin-btn-sm" style="font-size: 0.75rem;">
            <i data-lucide="refresh-cw"></i> Refresh Inspector
          </button>
        </div>

        <!-- Detected Keys Badge Cloud -->
        <div style="background: var(--admin-card-bg-elevated); border: 1px solid var(--admin-border-main); border-radius: 10px; padding: 1rem; margin-bottom: 1.25rem;">
          <div style="font-size: 0.8rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.5rem;">
            Actual Incoming JSON Keys:
          </div>
          <div id="detectedKeysContainer" style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
            <span style="color: var(--admin-text-secondary); font-size: 0.8rem;">Loading detected keys from recent logs...</span>
          </div>
        </div>

        <!-- Recent Payload Inspection Box -->
        <div>
          <h5 style="margin: 0 0 0.5rem 0; font-size: 0.85rem; color: var(--admin-text-primary);">Most Recent Raw Ingested Payload:</h5>
          <pre id="latestCapturedPayloadDisplay" style="height: 220px; overflow-y: auto; background: #050811; border: 1px solid var(--admin-border-main); border-radius: 8px; padding: 0.85rem; font-family: var(--admin-font-mono); font-size: 0.78rem; color: #38bdf8; margin: 0; white-space: pre-wrap; word-break: break-all;">No payloads logged yet.</pre>
        </div>
      </div>

      <!-- TAB 3: Strict Field Mapping & Validation Rules Matrix -->
      <div id="whTabPane_mapping" style="display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
          <div>
            <h4 style="margin: 0; color: #10b981; font-size: 1rem;">Strict Field Mapping &amp; Validation Engine</h4>
            <p style="margin: 0.25rem 0 0 0; font-size: 0.78rem; color: var(--admin-text-secondary);">
              <strong>Zero unmapped data:</strong> Only explicitly mapped fields are processed. Each field is validated against rules (e.g. <code>required</code>, <code>url</code>, <code>integer</code>, <code>min:3</code>, <code>max:255</code>) before inserting into the target table.
            </p>
          </div>
          <button type="button" onclick="addModalMappingRow('', '', 'required', '')" class="admin-btn admin-btn-outline admin-btn-sm" style="font-weight: 700; color: #10b981; border-color: rgba(16,185,129,0.4);">
            + Add Field Rule
          </button>
        </div>

        <!-- Mapping Rules Table -->
        <div style="background: var(--admin-card-bg-elevated); border: 1px solid var(--admin-border-main); border-radius: 8px; padding: 0.75rem; margin-bottom: 1.25rem;">
          <div style="display: grid; grid-template-columns: 2fr 2fr 1.5fr 1fr 36px; gap: 0.5rem; font-size: 0.75rem; font-weight: 700; color: var(--admin-text-secondary); text-transform: uppercase; margin-bottom: 0.5rem; padding-bottom: 0.4rem; border-bottom: 1px solid var(--admin-border-main);">
            <div>Incoming JSON Key</div>
            <div>Target DB Column</div>
            <div>Validation Rule</div>
            <div>Default Value</div>
            <div></div>
          </div>
          <div id="modalMappingRowsContainer" style="display: flex; flex-direction: column; gap: 0.5rem; max-height: 280px; overflow-y: auto; padding-right: 0.25rem;">
            <!-- Rows injected dynamically via JS -->
          </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
          <div style="font-size: 0.75rem; color: var(--admin-text-secondary);">
            Supported rules: <code>required</code>, <code>string</code>, <code>integer</code>, <code>url</code>, <code>email</code>, <code>min:N</code>, <code>max:N</code>, <code>in:val1,val2</code>
          </div>
          <button type="button" onclick="saveModalFieldMappings()" class="admin-btn admin-btn-primary" style="font-weight: 700;">
            💾 Save Validation &amp; Mapping Rules
          </button>
        </div>
      </div>

      <!-- TAB 4: Test Sandbox & Simulator -->
      <div id="whTabPane_simulator" style="display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
          <div>
            <h4 style="margin: 0; color: #a855f7; font-size: 1rem;">Payload Ingestion Test Sandbox</h4>
            <p style="margin: 0.25rem 0 0 0; font-size: 0.78rem; color: var(--admin-text-secondary);">
              Dispatch a test JSON payload to this endpoint and verify validation, mapping, and database write outcomes.
            </p>
          </div>
          <div style="display: flex; gap: 0.4rem;">
            <button type="button" onclick="loadSampleIntoModalSimulator()" class="admin-btn admin-btn-outline admin-btn-sm" style="font-size: 0.75rem; color: #38bdf8;">
              Load Table Sample
            </button>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
          <div>
            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">Request Payload (JSON)</label>
            <textarea id="modalSimPayloadInput" rows="10" class="admin-input" style="font-family: var(--admin-font-mono); font-size: 0.78rem; width: 100%; height: 240px;"></textarea>
          </div>
          <div>
            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.35rem;">Execution Response</label>
            <pre id="modalSimResponseOutput" style="height: 240px; overflow-y: auto; background: #050811; border: 1px solid var(--admin-border-main); border-radius: 8px; padding: 0.75rem; font-family: var(--admin-font-mono); font-size: 0.78rem; color: #34d399; margin: 0; white-space: pre-wrap;">// Response will appear here after execution</pre>
          </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
          <span id="modalSimStatusMsg" style="font-size: 0.8rem; font-weight: 600;"></span>
          <button type="button" onclick="executeModalSimDispatch()" class="admin-btn admin-btn-primary" style="font-weight: 700;">
            🚀 Dispatch Ingestion Request
          </button>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 3: INTERACTIVE INGESTION TUTORIAL & BLUEPRINTS                      -->
<!-- ========================================================================= -->
<div id="tutorialModal" class="admin-modal" style="display: none;">
  <div class="admin-modal-backdrop" onclick="closeTutorialModal()"></div>
  <div class="admin-modal-content" style="max-width: 820px; max-height: 85vh; display: flex; flex-direction: column;">
    <div class="admin-modal-header">
      <h3 style="margin: 0; color: var(--admin-text-primary); font-size: 1.2rem; display: flex; align-items: center; gap: 0.5rem;">
        <i data-lucide="book-open" style="color: #38bdf8;"></i> Ingestion Tutorial &amp; Architecture Blueprint
      </h3>
      <button type="button" onclick="closeTutorialModal()" class="admin-modal-close">&times;</button>
    </div>

    <!-- Tabs Header -->
    <div style="display: flex; gap: 0.5rem; padding: 0.75rem 1.25rem; border-bottom: 1px solid var(--admin-border-main); background: var(--admin-card-bg-elevated); overflow-x: auto;">
      <button type="button" onclick="switchTutorialTab('prompts')" id="tut-tab-prompts" class="admin-btn admin-btn-primary admin-btn-sm" style="font-weight: 700;">
        🖼️ Prompts Ingestion
      </button>
      <button type="button" onclick="switchTutorialTab('multitable')" id="tut-tab-multitable" class="admin-btn admin-btn-outline admin-btn-sm" style="font-weight: 700;">
        🗄️ Multi-Table Ingest
      </button>
      <button type="button" onclick="switchTutorialTab('tools')" id="tut-tab-tools" class="admin-btn admin-btn-outline admin-btn-sm" style="font-weight: 700;">
        ⚙️ AI Tools &amp; Articles
      </button>
      <button type="button" onclick="switchTutorialTab('integrations')" id="tut-tab-integrations" class="admin-btn admin-btn-outline admin-btn-sm" style="font-weight: 700;">
        ⚡ Zapier / Make / cURL
      </button>
    </div>

    <!-- Tab Contents Container -->
    <div style="padding: 1.25rem; overflow-y: auto; flex: 1;">
      <!-- TAB 1: Prompts -->
      <div id="tut-pane-prompts">
        <h4 style="color: #38bdf8; margin: 0 0 0.5rem 0;">How to Ingest AI Prompts &amp; Image Prompts</h4>
        <p style="font-size: 0.85rem; color: var(--admin-text-secondary); line-height: 1.5; margin-bottom: 1rem;">
          TechScout’s unified architecture links parent <code>contents</code> (for search, taxonomy, and authoring) with child <code>prompts</code> (for generation parameters). You do NOT need to write complex SQL: sending the payload below to <code>/api/v1/webhook/{slug}</code> automatically creates the content record, prompts row, tags, and category relations!
        </p>

        <div style="background: #050811; border: 1px solid var(--admin-border-main); border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <span style="font-family: var(--admin-font-mono); font-size: 0.75rem; color: #34d399;">PROMPT INGESTION PAYLOAD (JSON)</span>
            <button type="button" onclick="copySnippet('tut-prompt-json')" class="admin-btn admin-btn-outline admin-btn-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">Copy JSON</button>
          </div>
          <pre id="tut-prompt-json" style="margin: 0; color: #f8fafc; font-family: var(--admin-font-mono); font-size: 0.78rem; white-space: pre-wrap;"><?= htmlspecialchars(json_encode(\App\Core\IncomingWebhookEngine::getSamplePayloadForTable('prompts'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) ?></pre>
        </div>

        <ul style="font-size: 0.82rem; color: var(--admin-text-secondary); line-height: 1.6; margin: 0; padding-left: 1.2rem;">
          <li><code>prompt_type</code>: Set to <code>image</code> (Midjourney/Flux/DALL-E) or <code>text</code> (ChatGPT/Claude system prompts).</li>
          <li><code>category</code>: Enter category name e.g. <code>Generative Art</code>; the engine automatically links or creates it.</li>
          <li><code>tags</code>: Pass an array of strings e.g. <code>["midjourney", "architecture"]</code>. The engine automatically creates tags in <code>tags</code> and links them in <code>content_tags</code>.</li>
        </ul>
      </div>

      <!-- TAB 2: Multi-Table -->
      <div id="tut-pane-multitable" style="display: none;">
        <h4 style="color: #a855f7; margin: 0 0 0.5rem 0;">Multi-Table Atomic Ingestion Architecture</h4>
        <p style="font-size: 0.85rem; color: var(--admin-text-secondary); line-height: 1.5; margin-bottom: 1rem;">
          Need to insert or update across multiple tables in a single HTTP request? Use the top-level <code>tables</code> key. The engine wraps the entire execution in a MySQL database transaction with atomic rollback if any constraint fails.
        </p>

        <div style="background: #050811; border: 1px solid var(--admin-border-main); border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <span style="font-family: var(--admin-font-mono); font-size: 0.75rem; color: #a855f7;">MULTI-TABLE TRANSACTION SCHEMA</span>
            <button type="button" onclick="copySnippet('tut-multi-json')" class="admin-btn admin-btn-outline admin-btn-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">Copy JSON</button>
          </div>
          <pre id="tut-multi-json" style="margin: 0; color: #f8fafc; font-family: var(--admin-font-mono); font-size: 0.78rem; white-space: pre-wrap;"><?= htmlspecialchars(json_encode(\App\Core\IncomingWebhookEngine::getMultiTableSamplePayload(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) ?></pre>
        </div>
      </div>

      <!-- TAB 3: Tools & Articles -->
      <div id="tut-pane-tools" style="display: none;">
        <h4 style="color: #10b981; margin: 0 0 0.5rem 0;">Ingesting AI Tools &amp; Editorial Articles</h4>
        <p style="font-size: 0.85rem; color: var(--admin-text-secondary); line-height: 1.5; margin-bottom: 1rem;">
          Ingesting AI tools creates entries in the directory catalog with outbound affiliate links, pricing tiers, and badges. Ingesting articles publishes content to the TechScout Blog with HTML markdown and reading time calculation.
        </p>

        <div style="background: #050811; border: 1px solid var(--admin-border-main); border-radius: 8px; padding: 1rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <span style="font-family: var(--admin-font-mono); font-size: 0.75rem; color: #10b981;">AI TOOL INGESTION PAYLOAD (JSON)</span>
            <button type="button" onclick="copySnippet('tut-tools-json')" class="admin-btn admin-btn-outline admin-btn-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">Copy JSON</button>
          </div>
          <pre id="tut-tools-json" style="margin: 0; color: #f8fafc; font-family: var(--admin-font-mono); font-size: 0.78rem; white-space: pre-wrap;"><?= htmlspecialchars(json_encode(\App\Core\IncomingWebhookEngine::getSamplePayloadForTable('ai_tools'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) ?></pre>
        </div>
      </div>

      <!-- TAB 4: Integrations -->
      <div id="tut-pane-integrations" style="display: none;">
        <h4 style="color: #f59e0b; margin: 0 0 0.5rem 0;">Developer &amp; No-Code Integration Snippets</h4>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
          <div>
            <span style="font-size: 0.8rem; font-weight: 700; color: var(--admin-text-primary);">cURL Command:</span>
            <pre style="background: #050811; border: 1px solid var(--admin-border-main); border-radius: 6px; padding: 0.75rem; color: #38bdf8; font-size: 0.75rem; margin-top: 0.35rem; overflow-x: auto;">curl -X POST "<?= url('/api/v1/webhook/your-webhook-slug') ?>" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_WEBHOOK_SECRET_KEY" \
  -d '{"title": "Sample Item", "summary": "Sample summary", "table": "prompts"}'</pre>
          </div>

          <div>
            <span style="font-size: 0.8rem; font-weight: 700; color: var(--admin-text-primary);">Python (requests):</span>
            <pre style="background: #050811; border: 1px solid var(--admin-border-main); border-radius: 6px; padding: 0.75rem; color: #34d399; font-size: 0.75rem; margin-top: 0.35rem; overflow-x: auto;">import requests

url = "<?= url('/api/v1/webhook/your-webhook-slug') ?>"
headers = {
    "Content-Type": "application/json",
    "Authorization": "Bearer YOUR_WEBHOOK_SECRET_KEY"
}
payload = {
    "title": "Autonomous Agent Tool",
    "table": "prompts",
    "prompt_template": "System instructions here..."
}
response = requests.post(url, json=payload, headers=headers)
print(response.status_code, response.json())</pre>
          </div>

          <div>
            <span style="font-size: 0.8rem; font-weight: 700; color: var(--admin-text-primary);">Node.js (fetch):</span>
            <pre style="background: #050811; border: 1px solid var(--admin-border-main); border-radius: 6px; padding: 0.75rem; color: #a855f7; font-size: 0.75rem; margin-top: 0.35rem; overflow-x: auto;">const res = await fetch("<?= url('/api/v1/webhook/your-webhook-slug') ?>", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
    "Authorization": "Bearer YOUR_WEBHOOK_SECRET_KEY"
  },
  body: JSON.stringify({
    title: "Autonomous Agent Tool",
    table: "prompts",
    prompt_template: "System instructions here..."
  })
});
const data = await res.json();
console.log(data);</pre>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 4: ALL TABLES SAMPLE DATA GENERATOR & SIMULATOR                     -->
<!-- ========================================================================= -->
<div id="sampleGeneratorModal" class="admin-modal" style="display: none;">
  <div class="admin-modal-backdrop" onclick="closeSampleGeneratorModal()"></div>
  <div class="admin-modal-content" style="max-width: 760px; max-height: 85vh; display: flex; flex-direction: column;">
    <div class="admin-modal-header">
      <h3 style="margin: 0; color: var(--admin-text-primary); font-size: 1.15rem; display: flex; align-items: center; gap: 0.5rem;">
        <i data-lucide="sparkles" style="color: #a855f7;"></i> All Tables Sample Data Generator
      </h3>
      <button type="button" onclick="closeSampleGeneratorModal()" class="admin-modal-close">&times;</button>
    </div>
    
    <div style="padding: 1.25rem; overflow-y: auto; flex: 1;">
      <div style="display: flex; gap: 0.75rem; align-items: center; margin-bottom: 1rem; flex-wrap: wrap;">
        <label style="font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary);">Select Target Table:</label>
        <select id="sampleModalTableSelect" onchange="fetchTableSample(this.value)" class="admin-input" style="flex: 1; min-width: 220px; font-weight: 600;">
          <option value="prompts">prompts &bull; AI Prompts &amp; Generation Blueprints</option>
          <option value="multi">tables (Multi-Table Ingest Transaction)</option>
          <option value="ai_tools">ai_tools &bull; Directory AI Software &amp; Models</option>
          <option value="articles">articles &bull; Blog &amp; Editorial Posts</option>
          <option value="gpt_listings">gpt_listings &bull; Custom GPT Directory</option>
          <option value="jobs">jobs &bull; Tech Jobs Board</option>
          <option value="categories">categories &bull; Taxonomy Categories</option>
          <option value="announcements">announcements &bull; Top Broadcast Ribbons</option>
          <?php foreach ($permittedTables as $tblName => $tblMeta): 
            if (in_array($tblName, ['prompts', 'ai_tools', 'articles', 'gpt_listings', 'jobs', 'categories', 'announcements'], true)) continue;
          ?>
            <option value="<?= htmlspecialchars($tblName) ?>"><?= htmlspecialchars($tblName) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="button" onclick="fetchTableSample(document.getElementById('sampleModalTableSelect').value)" class="admin-btn admin-btn-outline admin-btn-sm" style="color: #38bdf8;">
          <i data-lucide="refresh-cw"></i> Refresh
        </button>
      </div>

      <!-- JSON Preview Box -->
      <div style="background: #050811; border: 1px solid var(--admin-border-main); border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
          <span id="samplePayloadHeading" style="font-family: var(--admin-font-mono); font-size: 0.75rem; color: #34d399;">SAMPLE PAYLOAD: prompts</span>
          <div style="display: flex; gap: 0.5rem;">
            <button type="button" onclick="copySnippet('sampleModalPayloadBox')" class="admin-btn admin-btn-outline admin-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem;">
              📋 Copy JSON
            </button>
            <button type="button" onclick="loadSampleModalIntoSimulator()" class="admin-btn admin-btn-primary admin-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.6rem; font-weight: 700;">
              ⚡ Load into Simulator
            </button>
          </div>
        </div>
        <pre id="sampleModalPayloadBox" style="height: 300px; overflow-y: auto; margin: 0; color: #f8fafc; font-family: var(--admin-font-mono); font-size: 0.78rem; white-space: pre-wrap;"><?= htmlspecialchars(json_encode(\App\Core\IncomingWebhookEngine::getSamplePayloadForTable('prompts'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) ?></pre>
      </div>
    </div>
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

let activeWebhookTab = 'general';
let activeModalWebhook = null;

function switchWebhookTab(tabName) {
  activeWebhookTab = tabName;
  ['general', 'captured', 'mapping', 'simulator'].forEach(t => {
    const btn = document.getElementById('whTabBtn_' + t);
    const pane = document.getElementById('whTabPane_' + t);
    if (!btn || !pane) return;
    if (t === tabName) {
      btn.className = 'admin-btn admin-btn-primary admin-btn-sm';
      pane.style.display = 'block';
    } else {
      btn.className = 'admin-btn admin-btn-outline admin-btn-sm';
      pane.style.display = 'none';
    }
  });

  if (tabName === 'captured') {
    reloadCapturedKeysForActiveWebhook();
  } else if (tabName === 'simulator') {
    loadSampleIntoModalSimulator();
  }
}

function openEditIncomingModal(wh) {
  activeModalWebhook = wh;
  document.getElementById('edit_wh_id').value = wh.id;
  document.getElementById('edit_wh_name').value = wh.name;
  document.getElementById('edit_wh_slug').value = wh.slug;
  document.getElementById('edit_wh_table').value = wh.target_table;
  document.getElementById('edit_wh_auth').value = wh.auth_type;
  document.getElementById('edit_wh_secret').value = wh.secret_key;
  document.getElementById('edit_wh_active').checked = (parseInt(wh.is_active, 10) === 1);
  document.getElementById('edit_wh_mapping_rules_json').value = wh.mapping_rules_json || '[]';
  document.getElementById('editModalHeading').textContent = 'Webhook Studio: ' + wh.name;

  // Initialize rules in Tab 3
  renderModalFieldMappings(wh);

  // Switch to general tab by default
  switchWebhookTab('general');

  // Open modal
  document.getElementById('editIncomingModal').style.display = 'flex';
}

function closeEditIncomingModal() {
  document.getElementById('editIncomingModal').style.display = 'none';
  activeModalWebhook = null;
}

function onTargetTableChanged(newTable) {
  if (activeModalWebhook) {
    activeModalWebhook.target_table = newTable;
    renderModalFieldMappings(activeModalWebhook);
  }
}

async function reloadCapturedKeysForActiveWebhook() {
  if (!activeModalWebhook) return;
  const container = document.getElementById('detectedKeysContainer');
  const previewBox = document.getElementById('latestCapturedPayloadDisplay');
  container.innerHTML = '<span style="color: var(--admin-text-secondary); font-size: 0.8rem;">Scanning recent captured payloads...</span>';

  try {
    const res = await fetch('<?= url('/admin/incoming-webhooks/inspect/') ?>' + activeModalWebhook.slug, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-Token': window.TECHSCOUT_CSRF_TOKEN || '',
        'X-Admin-Token': window.TECHSCOUT_ADMIN_TOKEN || ''
      }
    });
    const data = await res.json();
    if (data.success) {
      if (data.keys && data.keys.length > 0) {
        container.innerHTML = '';
        data.keys.forEach(k => {
          const badge = document.createElement('div');
          badge.style.cssText = 'display: inline-flex; align-items: center; gap: 0.4rem; background: var(--admin-card-bg); border: 1px solid var(--admin-border-main); border-radius: 6px; padding: 0.25rem 0.5rem;';
          badge.innerHTML = `
            <code style="font-family: var(--admin-font-mono); font-size: 0.76rem; color: #38bdf8;">${escapeHtml(k)}</code>
            <button type="button" onclick="addModalMappingRow('${escapeHtml(k)}', '', 'required', '')" class="admin-btn admin-btn-outline admin-btn-sm" style="font-size: 0.68rem; padding: 0.15rem 0.4rem; color: #10b981; border-color: rgba(16,185,129,0.4);" title="Add strict mapping for ${escapeHtml(k)}">
              + Map Field
            </button>
          `;
          container.appendChild(badge);
        });
      } else {
        container.innerHTML = '<span style="color: var(--admin-text-secondary); font-size: 0.8rem;">No payloads captured yet for this endpoint. Send a test payload to detect keys automatically!</span>';
      }

      if (data.payloads && data.payloads.length > 0) {
        previewBox.textContent = JSON.stringify(data.payloads[0].payload, null, 2);
      } else {
        previewBox.textContent = '// No incoming requests logged yet.';
      }
    } else {
      container.innerHTML = `<span style="color: #ef4444; font-size: 0.8rem;">Error: ${data.error || 'Failed to inspect payloads'}</span>`;
    }
  } catch(e) {
    container.innerHTML = `<span style="color: #ef4444; font-size: 0.8rem;">Error loading keys: ${e.message}</span>`;
  }
}

function renderModalFieldMappings(wh) {
  const container = document.getElementById('modalMappingRowsContainer');
  container.innerHTML = '';

  const rules = JSON.parse(wh.mapping_rules_json || '[]');
  if (rules.length === 0) {
    // Show one empty row
    addModalMappingRow('', '', 'required', '');
  } else {
    rules.forEach(r => {
      addModalMappingRow(r.json_key || '', r.target_column || '', r.rule || 'required', r.default || '');
    });
  }
}

function addModalMappingRow(jsonKey = '', targetCol = '', rule = 'required', defVal = '') {
  const container = document.getElementById('modalMappingRowsContainer');
  const targetTable = activeModalWebhook ? activeModalWebhook.target_table : (document.getElementById('edit_wh_table')?.value || 'prompts');
  const tableMeta = PERMITTED_TABLES[targetTable] || { columns: [] };

  const row = document.createElement('div');
  row.className = 'modal-map-row';
  row.style.cssText = 'display: grid; grid-template-columns: 2fr 2fr 1.5fr 1fr 36px; gap: 0.5rem; align-items: center; background: var(--admin-card-bg); padding: 0.4rem; border-radius: 6px; border: 1px solid var(--admin-border-main);';

  let colOptions = '<option value="">-- Choose Column --</option>';
  tableMeta.columns.forEach(c => {
    const sel = (c === targetCol) ? 'selected' : '';
    colOptions += `<option value="${escapeHtml(c)}" ${sel}>${escapeHtml(c)}</option>`;
  });

  const ruleOptions = [
    { val: 'required', label: 'Required (Any)' },
    { val: 'string', label: 'String' },
    { val: 'integer', label: 'Integer' },
    { val: 'url', label: 'Valid URL' },
    { val: 'email', label: 'Valid Email' },
    { val: 'min:3', label: 'Min 3 Chars' },
    { val: 'max:255', label: 'Max 255 Chars' }
  ].map(r => `<option value="${r.val}" ${r.val === rule ? 'selected' : ''}>${r.label}</option>`).join('');

  row.innerHTML = `
    <input type="text" class="admin-input map-json-key" placeholder="JSON Key (e.g. data.title)" value="${escapeHtml(jsonKey)}" style="font-family: var(--admin-font-mono); font-size: 0.78rem;">
    <select class="admin-input map-target-col" style="font-family: var(--admin-font-mono); font-size: 0.78rem;">
      ${colOptions}
    </select>
    <select class="admin-input map-field-rule" style="font-size: 0.78rem;">
      ${ruleOptions}
    </select>
    <input type="text" class="admin-input map-default-val" placeholder="Optional" value="${escapeHtml(defVal)}" style="font-size: 0.78rem;">
    <button type="button" onclick="this.parentElement.remove()" class="admin-btn admin-btn-outline admin-btn-sm" style="color: #ef4444; padding: 0.2rem 0.4rem;" title="Remove Field">&times;</button>
  `;

  container.appendChild(row);
}

async function saveModalFieldMappings() {
  if (!activeModalWebhook) return;
  const rows = document.querySelectorAll('#modalMappingRowsContainer .modal-map-row');
  const rules = [];

  rows.forEach(r => {
    const key = r.querySelector('.map-json-key').value.trim();
    const col = r.querySelector('.map-target-col').value.trim();
    const rule = r.querySelector('.map-field-rule').value.trim();
    const def = r.querySelector('.map-default-val').value.trim();
    if (key && col) {
      rules.push({ json_key: key, target_column: col, rule: rule, default: def });
    }
  });

  const rulesJson = JSON.stringify(rules);
  document.getElementById('edit_wh_mapping_rules_json').value = rulesJson;
  activeModalWebhook.mapping_rules_json = rulesJson;

  try {
    const res = await fetch('<?= url('/admin/incoming-webhooks/save-mappings') ?>', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-Token': window.TECHSCOUT_CSRF_TOKEN || ''
      },
      body: JSON.stringify({
        id: activeModalWebhook.id,
        mapping_rules_json: rulesJson
      })
    });
    const data = await res.json();
    if (data.success) {
      alert('✓ Strict mapping and validation rules saved successfully!');
    } else {
      alert('Error saving rules: ' + (data.error || 'Failed'));
    }
  } catch(e) {
    alert('Network error saving rules: ' + e.message);
  }
}

async function loadSampleIntoModalSimulator() {
  if (!activeModalWebhook) return;
  const table = activeModalWebhook.target_table || 'prompts';
  try {
    const res = await fetch('<?= url('/admin/incoming-webhooks/sample/') ?>' + table, {
      headers: { 'Accept': 'application/json', 'X-Admin-Token': window.TECHSCOUT_ADMIN_TOKEN || '' }
    });
    if (res.ok) {
      const data = await res.json();
      document.getElementById('modalSimPayloadInput').value = JSON.stringify(data, null, 2);
    }
  } catch(e) {}
}

async function executeModalSimDispatch() {
  if (!activeModalWebhook) return;
  const rawBody = document.getElementById('modalSimPayloadInput').value;
  const statusEl = document.getElementById('modalSimStatusMsg');
  const outputBox = document.getElementById('modalSimResponseOutput');
  statusEl.innerHTML = '<span style="color: #38bdf8;">Dispatching payload...</span>';

  try {
    const adminToken = window.TECHSCOUT_ADMIN_TOKEN || '';
    const csrfToken = window.TECHSCOUT_CSRF_TOKEN || '';
    const fd = new FormData();
    fd.append('slug', activeModalWebhook.slug);
    fd.append('payload', rawBody);
    fd.append('token', adminToken);
    fd.append('csrf_token', csrfToken);
    if (activeModalWebhook.auth_type === 'bearer') {
      fd.append('auth_header', 'Bearer ' + activeModalWebhook.secret_key);
    }

    const res = await fetch('<?= url('/admin/incoming-webhooks/simulate') ?>', {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-Token': csrfToken,
        'X-Admin-Token': adminToken
      },
      body: fd
    });
    const data = await res.json();
    outputBox.textContent = JSON.stringify(data, null, 2);
    if (data.success) {
      statusEl.innerHTML = `<span style="color: #10b981;">✓ 200 OK: Processed (${data.records_affected} rows inserted/updated)</span>`;
    } else {
      statusEl.innerHTML = `<span style="color: #ef4444;">✗ HTTP ${data.code || 400}: ${data.error}</span>`;
    }
  } catch(e) {
    statusEl.innerHTML = `<span style="color: #ef4444;">Error: ${e.message}</span>`;
    outputBox.textContent = e.message;
  }
}

async function submitEditWebhookForm(e) {
  e.preventDefault();
  const form = e.target;
  const fd = new FormData(form);
  try {
    const res = await fetch(form.action, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-Token': window.TECHSCOUT_CSRF_TOKEN || ''
      },
      body: fd
    });
    const data = await res.json();
    if (data.success) {
      alert('✓ Webhook updated successfully!');
      location.reload();
    } else {
      alert('Error updating webhook: ' + (data.error || 'Failed'));
    }
  } catch(err) {
    alert('Error saving webhook: ' + err.message);
  }
}

function openTutorialModal() {
  document.getElementById('tutorialModal').style.display = 'flex';
}
function closeTutorialModal() {
  document.getElementById('tutorialModal').style.display = 'none';
}

function switchTutorialTab(tab) {
  const tabs = ['prompts', 'multitable', 'tools', 'integrations'];
  tabs.forEach(t => {
    const btn = document.getElementById('tut-tab-' + t);
    const pane = document.getElementById('tut-pane-' + t);
    if (t === tab) {
      btn.className = 'admin-btn admin-btn-primary admin-btn-sm';
      pane.style.display = 'block';
    } else {
      btn.className = 'admin-btn admin-btn-outline admin-btn-sm';
      pane.style.display = 'none';
    }
  });
}

function openSampleGeneratorModal() {
  document.getElementById('sampleGeneratorModal').style.display = 'flex';
}
function closeSampleGeneratorModal() {
  document.getElementById('sampleGeneratorModal').style.display = 'none';
}

async function fetchTableSample(table) {
  try {
    const res = await fetch('<?= url('/admin/incoming-webhooks/sample/') ?>' + table, {
      headers: { 'Accept': 'application/json', 'X-Admin-Token': window.TECHSCOUT_ADMIN_TOKEN || '' }
    });
    if (res.ok) {
      const data = await res.json();
      document.getElementById('sampleModalPayloadBox').textContent = JSON.stringify(data, null, 2);
      document.getElementById('samplePayloadHeading').textContent = 'SAMPLE PAYLOAD: ' + table.toUpperCase();
    }
  } catch(e) {}
}

function loadSampleModalIntoSimulator() {
  const payload = document.getElementById('sampleModalPayloadBox').textContent;
  closeSampleGeneratorModal();
  if (!document.getElementById('listenerStudio') || document.getElementById('listenerStudio').style.display === 'none') {
    const firstWh = <?= !empty($incomingWebhooks) ? json_encode($incomingWebhooks[0], JSON_HEX_TAG | JSON_HEX_AMP) : 'null' ?>;
    if (firstWh) openLiveListener(firstWh);
  }
  document.getElementById('simPayloadInput').value = payload;
}

function quickLoadTableSample(table) {
  openSampleGeneratorModal();
  document.getElementById('sampleModalTableSelect').value = table;
  fetchTableSample(table);
}

function autoSuggestSlug(val, targetInputId) {
  const slug = val.toLowerCase().replace(/[^a-z0-9_-]/g, '-').replace(/-+/g, '-').replace(/^-|-$/g, '');
  const el = document.getElementById(targetInputId);
  if (el) el.value = slug;
}

function openLiveListener(wh) {
  currentActiveWebhook = wh;
  document.getElementById('studioWebhookName').textContent = wh.name;
  document.getElementById('studioTargetTable').textContent = wh.target_table;
  document.getElementById('studioEndpointUrl').textContent = '<?= url('/api/v1/webhook/') ?>' + wh.slug;
  document.getElementById('mappingWebhookId').value = wh.id;
  document.getElementById('listenerStudio').style.display = 'block';

  // Load appropriate sample for this webhook's target table
  loadTableSampleIntoSimulator();

  // Populate Field Mappings
  renderFieldMappings(wh);

  // Start Realtime Poller
  lastLogId = 0;
  pollLatestLogs();
  if (pollInterval) clearInterval(pollInterval);
  pollInterval = setInterval(pollLatestLogs, 3000);

  document.getElementById('listenerStudio').scrollIntoView({ behavior: 'smooth' });
}

function closeLiveListener() {
  document.getElementById('listenerStudio').style.display = 'none';
  if (pollInterval) {
    clearInterval(pollInterval);
    pollInterval = null;
  }
  currentActiveWebhook = null;
}

async function loadTableSampleIntoSimulator() {
  if (!currentActiveWebhook) return;
  const table = currentActiveWebhook.target_table || 'prompts';
  try {
    const res = await fetch('<?= url('/admin/incoming-webhooks/sample/') ?>' + table, {
      headers: { 'Accept': 'application/json', 'X-Admin-Token': window.TECHSCOUT_ADMIN_TOKEN || '' }
    });
    if (res.ok) {
      const data = await res.json();
      document.getElementById('simPayloadInput').value = JSON.stringify(data, null, 2);
    }
  } catch(e) {}
}

async function loadMultiTableSampleIntoSimulator() {
  try {
    const res = await fetch('<?= url('/admin/incoming-webhooks/sample/multi') ?>', {
      headers: { 'Accept': 'application/json', 'X-Admin-Token': window.TECHSCOUT_ADMIN_TOKEN || '' }
    });
    if (res.ok) {
      const data = await res.json();
      document.getElementById('simPayloadInput').value = JSON.stringify(data, null, 2);
    }
  } catch(e) {}
}

function renderFieldMappings(wh) {
  const container = document.getElementById('mappingRowsContainer');
  container.innerHTML = '';

  const rules = JSON.parse(wh.mapping_rules_json || '[]');
  const table = wh.target_table;
  const tableMeta = PERMITTED_TABLES[table] || { columns: [] };

  if (rules.length === 0) {
    // Inject default initial row
    addMappingRow('', '');
  } else {
    rules.forEach(r => addMappingRow(r.json_key, r.target_column));
  }
}

function addMappingRow(jsonKey = '', targetCol = '') {
  const container = document.getElementById('mappingRowsContainer');
  const table = currentActiveWebhook ? currentActiveWebhook.target_table : 'prompts';
  const tableMeta = PERMITTED_TABLES[table] || { columns: [] };

  const row = document.createElement('div');
  row.style.cssText = 'display: flex; gap: 0.5rem; align-items: center; background: var(--admin-card-bg-elevated); padding: 0.4rem 0.6rem; border-radius: 6px; border: 1px solid var(--admin-border-main);';

  let colOptions = '';
  tableMeta.columns.forEach(c => {
    const sel = (c === targetCol) ? 'selected' : '';
    colOptions += `<option value="${escapeHtml(c)}" ${sel}>${escapeHtml(c)}</option>`;
  });

  row.innerHTML = `
    <input type="text" class="admin-input map-json-key" placeholder="JSON Key (e.g. data.title)" value="${escapeHtml(jsonKey)}" style="flex: 1; font-family: var(--admin-font-mono); font-size: 0.78rem;">
    <span style="color: var(--admin-text-secondary);">&rarr;</span>
    <select class="admin-input map-target-col" style="flex: 1; font-family: var(--admin-font-mono); font-size: 0.78rem;">
      ${colOptions}
    </select>
    <button type="button" onclick="this.parentElement.remove()" class="admin-btn admin-btn-outline admin-btn-sm" style="color: #ef4444; padding: 0.2rem 0.4rem;" title="Remove Row">&times;</button>
  `;

  container.appendChild(row);
}

async function saveFieldMappings(e) {
  e.preventDefault();
  if (!currentActiveWebhook) return;

  const rows = document.querySelectorAll('#mappingRowsContainer > div');
  const rules = [];

  rows.forEach(r => {
    const key = r.querySelector('.map-json-key').value.trim();
    const col = r.querySelector('.map-target-col').value.trim();
    if (key && col) {
      rules.push({ json_key: key, target_column: col });
    }
  });

  try {
    const res = await fetch('<?= url('/admin/incoming-webhooks/save-mappings') ?>', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-Token': window.TECHSCOUT_CSRF_TOKEN || ''
      },
      body: JSON.stringify({
        id: currentActiveWebhook.id,
        mapping_rules_json: JSON.stringify(rules)
      })
    });
    const data = await res.json();
    if (data.success) {
      currentActiveWebhook.mapping_rules_json = JSON.stringify(rules);
      alert('✓ Field mapping rules updated successfully!');
    } else {
      alert('Error updating field rules: ' + data.error);
    }
  } catch(err) {
    alert('Error: ' + err.message);
  }
}

async function pollLatestLogs() {
  if (!currentActiveWebhook) return;
  try {
    const adminToken = window.TECHSCOUT_ADMIN_TOKEN || '';
    const res = await fetch('<?= url('/admin/incoming-webhooks/live-stream/') ?>' + currentActiveWebhook.slug + '?since_id=' + lastLogId + '&token=' + adminToken, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-Token': window.TECHSCOUT_CSRF_TOKEN || '',
        'X-Admin-Token': adminToken
      }
    });
    if (!res.ok) return;
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
    const adminToken = window.TECHSCOUT_ADMIN_TOKEN || '';
    const csrfToken = window.TECHSCOUT_CSRF_TOKEN || '';
    const fd = new FormData();
    fd.append('slug', currentActiveWebhook.slug);
    fd.append('payload', rawBody);
    fd.append('token', adminToken);
    fd.append('csrf_token', csrfToken);
    if (currentActiveWebhook.auth_type === 'bearer') {
      fd.append('auth_header', 'Bearer ' + currentActiveWebhook.secret_key);
    }

    const res = await fetch('<?= url('/admin/incoming-webhooks/simulate') ?>', {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-Token': csrfToken,
        'X-Admin-Token': adminToken
      },
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

function copySnippet(id) {
  const text = document.getElementById(id).innerText;
  copyToClipboard(text);
}

function copyToClipboard(text) {
  navigator.clipboard.writeText(text).then(() => {
    alert('✓ Copied to clipboard:\n' + (text.length > 100 ? text.substring(0, 100) + '...' : text));
  });
}

function escapeHtml(str) {
  if (str === null || str === undefined) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
