<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<!-- HUD Topbar -->
<div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; flex-wrap: wrap; gap: 1.5rem;">
  <div>
    <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.4rem; flex-wrap: wrap;">
      <span class="ts-badge ts-badge-cyan" style="font-size: 0.72rem; letter-spacing: 0.05em; font-weight: 700;">100X INGESTION GATEWAY</span>
      <span class="ts-badge ts-badge-green" style="font-size: 0.72rem;">MULTI-TABLE SYNC</span>
    </div>
    <h1 style="font-size: 2.25rem; font-weight: 800; color: #f8fafc; margin: 0; line-height: 1.2; font-family: 'Outfit', sans-serif;">
      Incoming Webhooks &amp; Live Ingestion
    </h1>
    <p style="color: #64748b; font-size: 0.95rem; margin-top: 0.35rem;">
      Dynamic Database Field Mapper, Schema Blueprints &amp; Live Ingestion Pipelines.
    </p>
  </div>

  <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
    <a href="<?= url('/admin/webhooks') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3);">
      <i data-lucide="arrow-up-right" style="width: 14px;"></i> Outbound
    </a>
    <button type="button" onclick="openTutorialModal()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #f472b6; border-color: rgba(236,72,153,0.3);">
      <i data-lucide="book-open" style="width: 14px;"></i> Tutorial 📘
    </button>
    <button type="button" onclick="openSampleGeneratorModal()" class="ts-btn ts-btn-outline ts-btn-sm" style="border-color: rgba(168,85,247,0.3); color: #a855f7;">
      <i data-lucide="sparkles" style="width: 14px;"></i> Samples ⚡
    </button>
    <button type="button" onclick="openNewIncomingModal()" class="ts-btn ts-btn-primary ts-btn-sm" style="font-weight: 700;">
      <i data-lucide="plus-circle" style="width: 14px;"></i> New Webhook 🚀
    </button>
  </div>
</div>

  <!-- Page Tabs -->
  <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-main); padding-bottom: 0.5rem; flex-wrap: wrap;">
    <button type="button" onclick="switchPageTab('endpoints')" id="page_tab_endpoints" class="admin-btn admin-btn-primary admin-btn-sm" style="font-weight: 700;">
      🌐 Webhook Endpoints
    </button>
    <button type="button" onclick="switchPageTab('logs')" id="page_tab_logs" class="admin-btn admin-btn-outline admin-btn-sm" style="font-weight: 700;">
      📋 Global Activity Logs
    </button>
  </div>

<div id="page_pane_endpoints">
  <!-- Endpoints Overview Table -->
  <div class="admin-table-wrap" style="margin-bottom: 2rem;">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Name / Slug</th>
          <th>Mode &amp; Status</th>
          <th>Configuration</th>
          <th>Activity</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php if (!empty($incomingWebhooks)): ?>
          <?php foreach ($incomingWebhooks as $wh): 
            $endpointUrl = url('/api/v1/webhook/' . $wh['slug']);
            $rules = json_decode($wh['mapping_rules_json'] ?? '[]', true) ?: [];
            $ingestMode = $wh['ingestion_mode'] ?? 'live_ingest';
          ?>
            <tr>
              <td>
                <div style="font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.2rem;"><?= htmlspecialchars($wh['name']) ?></div>
                <div style="flex-wrap: wrap; align-items: center; font-size: 0.72rem; color: #38bdf8; font-family: var(--admin-font-mono); display: flex; align-items: center; gap: 0.4rem;">
                  <?= htmlspecialchars($endpointUrl) ?>
                  <i data-lucide="copy" style="width: 12px; height: 12px; cursor: pointer; color: var(--admin-text-secondary);" onclick="copyToClipboard('<?= htmlspecialchars(addslashes($endpointUrl)) ?>')" title="Copy Endpoint URL"></i>
                </div>
              </td>
              <td>
                <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
                  <span class="ts-badge <?= $ingestMode === 'listen_only' ? 'ts-badge-amber' : 'ts-badge-green' ?>" style="font-size: 0.68rem; font-weight: 800;">
                    <?= $ingestMode === 'listen_only' ? '🎧 LISTENER' : '🚀 INGEST' ?>
                  </span>
                  <span class="ts-badge <?= !empty($wh['is_active']) ? 'ts-badge-cyan' : 'ts-badge-pink' ?>" style="font-size: 0.68rem;">
                    <?= !empty($wh['is_active']) ? 'ACTIVE' : 'PAUSED' ?>
                  </span>
                </div>
              </td>
              <td>
                <div style="font-size: 0.76rem; color: var(--admin-text-secondary); margin-bottom: 0.2rem;">
                  Target: <strong style="color: #a855f7;"><?= htmlspecialchars($wh['target_table']) ?></strong>
                </div>
                <div style="flex-wrap: wrap; align-items: center; font-size: 0.72rem; display: flex; gap: 0.4rem; align-items: center;">
                  <span class="ts-badge ts-badge-amber" style="padding: 0.1rem 0.4rem;">Auth: <?= strtoupper(htmlspecialchars($wh['auth_type'])) ?></span>
                  <span class="ts-badge" style="background: rgba(99,102,241,0.15); color: #818cf8; padding: 0.1rem 0.4rem;"><?= count($rules) ?> Maps</span>
                </div>
              </td>
              <td>
                <div style="font-size: 0.76rem; color: var(--admin-text-secondary);">
                  <strong style="color: var(--admin-text-primary);"><?= number_format((int)($wh['received_count'] ?? 0)) ?></strong> logs
                </div>
                <div style="font-size: 0.68rem; color: var(--admin-text-muted);">
                  <?= !empty($wh['last_received_at']) ? htmlspecialchars($wh['last_received_at']) : 'Never' ?>
                </div>
              </td>
              <td>
                <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                  <button type="button" onclick="openLiveListener(<?= htmlspecialchars(json_encode($wh, JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') ?>)" class="admin-btn admin-btn-primary admin-btn-sm" style="padding: 0.25rem 0.5rem; font-weight: 700; font-size: 0.7rem;" title="Live Studio">
                    <i data-lucide="radio" style="width: 14px; height: 14px;"></i> Studio
                  </button>
                  <button type="button" onclick="openEditIncomingModal(<?= htmlspecialchars(json_encode($wh, JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') ?>)" class="admin-btn admin-btn-outline admin-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.7rem; color: #38bdf8;" title="Edit Settings">
                    <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i>
                  </button>
                  <button type="button" onclick="quickLoadTableSample('<?= htmlspecialchars(addslashes($wh['target_table'])) ?>')" class="admin-btn admin-btn-outline admin-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.7rem; color: #a855f7;" title="Load Sample">
                    <i data-lucide="code" style="width: 14px; height: 14px;"></i>
                  </button>
                  <form action="<?= url('/admin/incoming-webhooks/toggle-mode/' . $wh['id']) ?>" method="POST" style="margin: 0;">
                    <button type="submit" class="admin-btn admin-btn-outline admin-btn-sm" style="padding: 0.25rem 0.5rem;" title="Toggle Mode">
                      <i data-lucide="<?= $ingestMode === 'listen_only' ? 'database' : 'headphones' ?>" style="width: 14px; height: 14px; color: <?= $ingestMode === 'listen_only' ? '#10b981' : '#f59e0b' ?>;"></i>
                    </button>
                  </form>
                  <form action="<?= url('/admin/incoming-webhooks/toggle/' . $wh['id']) ?>" method="POST" style="margin: 0;">
                    <button type="submit" class="admin-btn admin-btn-outline admin-btn-sm" style="padding: 0.25rem 0.5rem;" title="Toggle Active">
                      <i data-lucide="<?= !empty($wh['is_active']) ? 'pause' : 'play' ?>" style="width: 14px; height: 14px;"></i>
                    </button>
                  </form>
                  <form action="<?= url('/admin/incoming-webhooks/delete/' . $wh['id']) ?>" method="POST" onsubmit="return confirm('Permanently delete this webhook?');" style="margin: 0;">
                    <button type="submit" class="admin-btn admin-btn-outline admin-btn-sm" style="color: #ef4444; padding: 0.25rem 0.5rem;" title="Delete">
                      <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="5" style="text-align: center; padding: 3rem;">
            <i data-lucide="inbox" style="width: 44px; height: 44px; color: var(--admin-text-secondary); margin-bottom: 0.75rem;"></i>
            <h3 style="color: var(--admin-text-primary); margin: 0 0 0.5rem 0;">No Ingestion Webhooks Configured</h3>
            <p style="color: var(--admin-text-secondary); font-size: 0.9rem; margin-bottom: 1.25rem;">Create a custom webhook URL to ingest JSON data.</p>
            <button type="button" onclick="openNewIncomingModal()" class="admin-btn admin-btn-primary">Create First Ingestion Webhook</button>
          </td>
        </tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div id="page_pane_logs" style="display: none;">
  <!-- Recent Ingestion Activity Log Table -->
  <div class="admin-table-wrap" style="padding: 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
      <h3 style="font-size: 1.15rem; color: var(--admin-text-primary); margin: 0; font-weight: 800;">Recent Ingestion Activity Logs</h3>
      <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
        <form action="<?= url('/admin/incoming-webhooks') ?>" method="GET" style="display: flex; gap: 0.5rem; align-items: center; margin: 0; flex-wrap: wrap;">
          <input type="hidden" name="tab" value="logs">
          
          <select name="webhook_id" class="admin-input" style="font-size: 0.8rem; padding: 0.35rem 0.75rem; width: 140px;">
            <option value="">All Webhooks</option>
            <?php foreach ($incomingWebhooks as $wh): ?>
              <option value="<?= $wh['id'] ?>" <?= (isset($_GET['webhook_id']) && $_GET['webhook_id'] == $wh['id']) ? 'selected' : '' ?>><?= htmlspecialchars($wh['slug']) ?></option>
            <?php endforeach; ?>
          </select>

          <select name="status" class="admin-input" style="font-size: 0.8rem; padding: 0.35rem 0.75rem; width: 120px;">
            <option value="">Any Status</option>
            <option value="success" <?= (isset($_GET['status']) && $_GET['status'] === 'success') ? 'selected' : '' ?>>Success</option>
            <option value="simulated" <?= (isset($_GET['status']) && $_GET['status'] === 'simulated') ? 'selected' : '' ?>>Simulated</option>
            <option value="failed" <?= (isset($_GET['status']) && $_GET['status'] === 'failed') ? 'selected' : '' ?>>Failed</option>
            <option value="skipped (duplicate)" <?= (isset($_GET['status']) && $_GET['status'] === 'skipped (duplicate)') ? 'selected' : '' ?>>Skipped (Dup)</option>
          </select>

          <input type="date" name="date" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>" class="admin-input" style="font-size: 0.8rem; padding: 0.35rem 0.75rem; width: 120px;">
          
          <input type="text" id="logFilterInput" onkeyup="filterActivityLogs()" placeholder="Client search..." class="admin-input" style="font-size: 0.8rem; padding: 0.35rem 0.75rem; width: 130px;">
          
          <button type="submit" class="admin-btn admin-btn-primary admin-btn-sm" style="font-size: 0.75rem; padding: 0.35rem 0.6rem;">
            <i data-lucide="filter"></i> Filter
          </button>
        </form>

        <form action="<?= url('/admin/incoming-webhooks/clear-logs') ?>" method="POST" onsubmit="return confirm('Clear all incoming webhook execution logs?');" style="margin: 0;">
          <button type="submit" class="admin-btn admin-btn-outline admin-btn-sm" style="color: #ef4444; font-size: 0.75rem;">
            <i data-lucide="trash"></i> Clear Logs
          </button>
        </form>
      </div>
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
                  <?= htmlspecialchars($log['endpoint_slug']) ?>
                </span>
              </td>
              <td>
                <?php 
                  $st = strtolower((string)$log['status']);
                  $badgeCls = ($st === 'success') ? 'ts-badge-green' : (($st === 'simulated') ? 'ts-badge-amber' : 'ts-badge-pink');
                ?>
                <span class="ts-badge <?= $badgeCls ?>" style="font-size: 0.7rem; font-weight: 800;">
                  <?= strtoupper(htmlspecialchars($log['status'])) ?>
                </span>
              </td>
              <td>
                <span style="font-family: var(--admin-font-mono); font-size: 0.8rem; font-weight: 700; color: <?= (int)$log['response_code'] === 200 ? '#10b981' : '#ef4444' ?>;">
                  <?= (int)$log['response_code'] ?>
                </span>
              </td>
              <td>
                <span style="font-family: var(--admin-font-mono); font-size: 0.8rem;">
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
<!-- MODAL 1: FLOATING LIVE LISTENER & DYNAMIC FIELD MAPPER STUDIO MODAL       -->
<!-- ========================================================================= -->
<div id="listenerStudioModal" class="admin-modal-overlay" style="display: none;">
  <div class="admin-modal-backdrop" onclick="closeLiveListener()"></div>
  <div class="admin-modal-box modal-cyan" style="max-width: 1040px; max-height: 92vh; display: flex; flex-direction: column; padding: 0;">
    
    <div class="admin-modal-header" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--admin-border-main); display: flex; justify-content: space-between; align-items: center; background: var(--admin-bg-surface);">
      <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.75rem;">
        <div style="width: 12px; height: 12px; border-radius: 50%; background: #10b981; animation: pulseGlow 1.5s infinite;"></div>
        <div>
          <h2 style="font-size: 1.2rem; margin: 0; color: var(--admin-text-primary); font-family: var(--admin-font-heading); font-weight: 800;">
            Live Ingestion Studio: <span id="studioWebhookName" style="color: #38bdf8;"></span>
          </h2>
          <span style="font-size: 0.76rem; color: var(--admin-text-secondary);">Target Table: <strong id="studioTargetTable" style="color: #a855f7;"></strong> &bull; Endpoint: <code id="studioEndpointUrl" style="color: #38bdf8;"></code></span>
        </div>
      </div>

      <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.5rem; align-items: center;">
        <button type="button" onclick="pollLatestLogs()" class="admin-btn admin-btn-outline admin-btn-sm" style="font-size: 0.75rem;">
          <i data-lucide="refresh-cw"></i> Refresh Stream
        </button>
        <button type="button" onclick="closeLiveListener()" class="admin-modal-close" style="background: none; border: none; font-size: 1.6rem; color: var(--admin-text-secondary); cursor: pointer; line-height: 1;">&times;</button>
      </div>
    </div>

    <!-- Tabs Header -->
    <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.5rem; padding: 0.75rem 1.25rem; border-bottom: 1px solid var(--admin-border-main); background: var(--admin-card-bg-elevated);">
      <button type="button" onclick="switchStudioTab('stream')" id="studio-tab-stream" class="admin-btn admin-btn-primary admin-btn-sm" style="font-weight: 700;">
        <i data-lucide="terminal"></i> Stream &amp; Simulator
      </button>
      <button type="button" onclick="switchStudioTab('mapping')" id="studio-tab-mapping" class="admin-btn admin-btn-outline admin-btn-sm" style="font-weight: 700;">
        <i data-lucide="database"></i> Field Mapping
      </button>
    </div>

    <!-- Modal Body: Tab Contents -->
    <div style="padding: 1.5rem; overflow-y: auto; flex: 1;">
      <!-- Left Column: Realtime Payloads Stream & Simulator -->
      <div id="studio-pane-stream">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
          <h4 style="font-size: 0.92rem; color: var(--admin-text-primary); margin: 0; display: flex; align-items: center; gap: 0.4rem;">
            <i data-lucide="terminal" style="width: 15px; color: #38bdf8;"></i> Captured Payloads Stream
          </h4>
          <span id="streamCounter" class="ts-badge ts-badge-cyan" style="font-size: 0.68rem;">0 payloads synced</span>
        </div>

        <div id="livePayloadViewer" style="height: 240px; overflow-y: auto; background: #050811; border: 1px solid var(--admin-border-main); border-radius: 8px; padding: 0.85rem; font-family: var(--admin-font-mono); font-size: 0.78rem; color: #34d399; margin-bottom: 1rem;">
          <span style="color: #64748b;">// Listening for incoming HTTP POST requests to this webhook endpoint...</span><br>
          <span style="color: #64748b;">// Execute a test dispatch below to verify schema extraction.</span>
        </div>

        <!-- Simulator Dispatcher Box -->
        <div style="background: var(--admin-card-bg-elevated); border: 1px solid var(--admin-border-main); border-radius: 8px; padding: 1rem;">
          <div style="font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.4rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
            <span>⚡ Test Simulation Dispatcher</span>
            <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.4rem;">
              <a href="javascript:void(0)" onclick="loadTableSampleIntoSimulator()" style="font-size: 0.72rem; color: #38bdf8; text-decoration: none;">Load Table Sample</a>
              <span style="color: var(--admin-text-secondary);">&bull;</span>
              <a href="javascript:void(0)" onclick="loadMultiTableSampleIntoSimulator()" style="font-size: 0.72rem; color: #a855f7; text-decoration: none;">Multi-Table Sample</a>
            </div>
          </div>
          <textarea id="simPayloadInput" rows="5" class="admin-input" style="font-family: var(--admin-font-mono); font-size: 0.78rem; width: 100%; margin-bottom: 0.6rem;" oninput="handleSimulatorChange()"></textarea>
          
          <div id="dynamicCurlPreviewBox" style="display: none; background: #050811; border: 1px solid var(--admin-border-main); border-radius: 6px; padding: 0.5rem; margin-bottom: 0.6rem; font-family: var(--admin-font-mono); font-size: 0.72rem;">
            <div style="color: #38bdf8; font-weight: bold; margin-bottom: 0.2rem;">Dynamic GET URL:</div>
            <div id="dynGetUrl" style="word-break: break-all; color: #94a3b8; margin-bottom: 0.4rem;"></div>
            <div style="color: #34d399; font-weight: bold; margin-bottom: 0.2rem;">Dynamic POST cURL:</div>
            <div id="dynPostCurl" style="word-break: break-all; color: #94a3b8; white-space: pre-wrap;"></div>
          </div>

          <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
            <button type="button" onclick="sendTestSimulation()" class="admin-btn admin-btn-primary admin-btn-sm" style="font-weight: 700;">
              🚀 Execute Test Dispatch
            </button>
            <span id="simStatusMsg" style="font-size: 0.76rem; color: var(--admin-text-secondary);"></span>
          </div>
        </div>
      </div>

      <!-- Right Column: Dynamic Field Mapping to Database -->
      <div id="studio-pane-mapping" style="display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
          <h4 style="font-size: 0.92rem; color: var(--admin-text-primary); margin: 0; display: flex; align-items: center; gap: 0.4rem;">
            <i data-lucide="database" style="width: 15px; color: #10b981;"></i> Dynamic Field Mapping Matrix
          </h4>
          <button type="button" onclick="addMappingRow()" class="admin-btn admin-btn-outline admin-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem;">
            + Add Field Map
          </button>
        </div>

        <p style="font-size: 0.78rem; color: var(--admin-text-secondary); margin: 0 0 0.75rem 0;">
          Map JSON payload keys (supports dotted notation like <code>data.title</code>) to target table columns. Unmapped matching keys automatically map.
        </p>

        <form id="mappingForm" onsubmit="saveFieldMappings(event)">
          <input type="hidden" id="mappingWebhookId" name="webhook_id">
          <datalist id="jsonKeysList"></datalist>
          
          <div id="mappingRowsContainer" style="flex-wrap: wrap; align-items: center; display: flex; flex-direction: column; gap: 0.5rem; max-height: 400px; overflow-y: auto; padding-right: 0.4rem; margin-bottom: 1rem;">
            <!-- Rows injected dynamically via JS -->
          </div>

          <button type="submit" class="admin-btn admin-btn-primary" style="width: 100%; font-weight: 700; padding: 0.65rem;">
            💾 Save Field Mapping Rules
          </button>
        </form>
      </div>
    </div>
    
    <script>
    function switchStudioTab(tab) {
      if (tab === 'stream') {
        document.getElementById('studio-tab-stream').className = 'admin-btn admin-btn-primary admin-btn-sm';
        document.getElementById('studio-tab-mapping').className = 'admin-btn admin-btn-outline admin-btn-sm';
        document.getElementById('studio-pane-stream').style.display = 'block';
        document.getElementById('studio-pane-mapping').style.display = 'none';
      } else {
        document.getElementById('studio-tab-stream').className = 'admin-btn admin-btn-outline admin-btn-sm';
        document.getElementById('studio-tab-mapping').className = 'admin-btn admin-btn-primary admin-btn-sm';
        document.getElementById('studio-pane-stream').style.display = 'none';
        document.getElementById('studio-pane-mapping').style.display = 'block';
      }
    }
    </script>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: CREATE NEW INGESTION WEBHOOK                                     -->
<!-- ========================================================================= -->
<div id="newIncomingModal" class="admin-modal-overlay" style="display: none;">
  <div class="admin-modal-backdrop" onclick="closeNewIncomingModal()"></div>
  <div class="admin-modal-box modal-cyan" style="max-width: 580px;">
    <div class="admin-modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
      <h3 style="margin: 0; color: var(--admin-text-primary); font-size: 1.15rem; display: flex; align-items: center; gap: 0.5rem; font-weight: 800;">
        <i data-lucide="plus-circle" style="color: #38bdf8;"></i> Create Ingestion Webhook Endpoint
      </h3>
      <button type="button" onclick="closeNewIncomingModal()" class="admin-modal-close" style="background: none; border: none; font-size: 1.5rem; color: var(--admin-text-secondary); cursor: pointer;">&times;</button>
    </div>
    <form action="<?= url('/admin/incoming-webhooks/save') ?>" method="POST">
      <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Webhook Name</label>
        <input type="text" name="name" id="new_wh_name" placeholder="e.g. Midjourney Prompts Ingest, Stripe Donations Sync" class="admin-input" required oninput="autoSuggestSlug(this.value, 'new_wh_slug')">
      </div>

      <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Endpoint Slug (URL Path)</label>
        <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.4rem;">
          <span style="font-size: 0.82rem; color: var(--admin-text-secondary); font-family: var(--admin-font-mono);"><?= url('/api/v1/webhook/') ?></span>
          <input type="text" name="slug" id="new_wh_slug" placeholder="midjourney-prompts" class="admin-input" style="flex: 1;" required pattern="[a-z0-9_\-]+">
        </div>
      </div>

      <div style="margin-bottom: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
          <label style="font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin: 0;">Target Database Table(s)</label>
          <a href="javascript:void(0)" onclick="quickLoadTableSample(document.getElementById('new_wh_table').value || document.getElementById('new_wh_table').options[0].value)" style="font-size: 0.72rem; color: #38bdf8; text-decoration: none;">View Sample JSON</a>
        </div>
        <select name="target_table[]" id="new_wh_table" class="admin-input" multiple required style="height: 100px;">
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

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.15rem;">
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

      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Initial Ingestion Mode</label>
        <select name="ingestion_mode" class="admin-input" style="font-weight: 700;">
          <option value="live_ingest" selected>🚀 Live Production Ingestion (Active Database Writes)</option>
          <option value="listen_only">🎧 Schema Discovery Listener (Listen &amp; Capture Payloads Only — No DB Writes)</option>
        </select>
      </div>

      <div class="admin-modal-footer" style="flex-wrap: wrap; align-items: center; display: flex; justify-content: flex-end; gap: 0.5rem;">
        <button type="button" onclick="closeNewIncomingModal()" class="admin-btn admin-btn-outline">Cancel</button>
        <button type="submit" class="admin-btn admin-btn-primary" style="font-weight: 700;">Create Ingestion Webhook 🚀</button>
      </div>
    </form>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 3: TABBED WEBHOOK EDIT & STRICT VALIDATION STUDIO                   -->
<!-- ========================================================================= -->
<div id="editIncomingModal" class="admin-modal-overlay" style="display: none;">
  <div class="admin-modal-backdrop" onclick="closeEditIncomingModal()"></div>
  <div class="admin-modal-box modal-cyan" style="max-width: 880px; max-height: 90vh; display: flex; flex-direction: column; padding: 0;">
    
    <!-- Modal Header -->
    <div class="admin-modal-header" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--admin-border-main); display: flex; justify-content: space-between; align-items: center; background: var(--admin-bg-surface);">
      <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.65rem;">
        <span class="ts-badge ts-badge-cyan" style="font-size: 0.72rem; font-weight: 700;">WEBHOOK SETTINGS</span>
        <h3 style="margin: 0; color: var(--admin-text-primary); font-size: 1.2rem; font-family: var(--admin-font-heading); font-weight: 800;" id="editModalHeading">
          Manage Ingestion Webhook
        </h3>
      </div>
      <button type="button" onclick="closeEditIncomingModal()" class="admin-modal-close" style="background: none; border: none; font-size: 1.5rem; color: var(--admin-text-secondary); cursor: pointer; line-height: 1;">&times;</button>
    </div>

    <!-- Scrollable Form Body -->
    <div style="padding: 1.5rem; overflow-y: auto; flex: 1;">
      <form id="editGeneralForm" action="<?= url('/admin/incoming-webhooks/save') ?>" onsubmit="submitEditWebhookForm(event)">
        <input type="hidden" name="id" id="edit_wh_id">
        <input type="hidden" name="mapping_rules_json" id="edit_wh_mapping_rules_json">

        <div style="margin-bottom: 1.15rem;">
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Webhook Name</label>
          <input type="text" name="name" id="edit_wh_name" class="admin-input" required placeholder="e.g. Midjourney Prompts Ingest">
        </div>

        <div style="margin-bottom: 1.15rem;">
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Endpoint Slug (URL Path)</label>
          <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.4rem;">
            <span style="font-size: 0.82rem; color: var(--admin-text-secondary); font-family: var(--admin-font-mono);"><?= url('/api/v1/webhook/') ?></span>
            <input type="text" name="slug" id="edit_wh_slug" class="admin-input" style="flex: 1;" required pattern="[a-z0-9_\-]+">
          </div>
        </div>

        <div style="margin-bottom: 1.15rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
            <label style="font-size: 0.82rem; font-weight: 700; color: var(--admin-text-primary); margin: 0;">Target Database Table(s)</label>
            <span style="font-size: 0.72rem; color: var(--admin-text-secondary);">Table where records will be validated and inserted</span>
          </div>
          <select name="target_table[]" id="edit_wh_table" class="admin-input" multiple required style="height: 100px;">
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

        <div style="margin-bottom: 1.15rem; background: var(--admin-card-bg-elevated); border: 1px solid var(--admin-border-main); padding: 0.85rem 1rem; border-radius: 10px;">
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Ingestion Operating Mode</label>
          <select name="ingestion_mode" id="edit_wh_mode" class="admin-input" style="font-weight: 700;">
            <option value="live_ingest">🚀 Live Production Ingestion (Execute DB Inserts &amp; Writes)</option>
            <option value="listen_only">🎧 Schema Discovery Listener (Listen &amp; Capture Payloads Only — No DB Writes)</option>
          </select>
          <p style="font-size: 0.74rem; color: var(--admin-text-secondary); margin: 0.4rem 0 0 0;">
            In <strong>Schema Discovery Listener</strong> mode, raw payloads are safely logged and schema keys are discovered without modifying database tables.
          </p>
        </div>

        <div style="margin-bottom: 1.5rem;">
          <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.88rem; font-weight: 700; color: var(--admin-text-primary);">
            <input type="checkbox" name="is_active" id="edit_wh_active" value="1" style="width: 18px; height: 18px; accent-color: #10b981;">
            <span>Webhook Endpoint is Active (Receiving Requests)</span>
          </label>
        </div>

        <div class="admin-modal-footer" style="flex-wrap: wrap; align-items: center; display: flex; justify-content: flex-end; gap: 0.5rem; border-top: 1px solid var(--admin-border-main); padding-top: 1rem;">
          <button type="button" onclick="closeEditIncomingModal()" class="admin-btn admin-btn-outline">Cancel</button>
          <button type="submit" class="admin-btn admin-btn-primary" style="font-weight: 700;">Save Webhook Configuration 🚀</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 4: INGESTION ARCHITECTURE TUTORIAL & BLUEPRINTS                     -->
<!-- ========================================================================= -->
<div id="tutorialModal" class="admin-modal-overlay" style="display: none;">
  <div class="admin-modal-backdrop" onclick="closeTutorialModal()"></div>
  <div class="admin-modal-box modal-indigo" style="max-width: 860px; max-height: 90vh; display: flex; flex-direction: column; padding: 0;">
    <div class="admin-modal-header" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--admin-border-main); display: flex; justify-content: space-between; align-items: center; background: var(--admin-bg-surface);">
      <h3 style="margin: 0; color: var(--admin-text-primary); font-size: 1.2rem; display: flex; align-items: center; gap: 0.5rem; font-weight: 800;">
        <i data-lucide="book-open" style="color: #38bdf8;"></i> Ingestion Tutorial &amp; Architecture Blueprint
      </h3>
      <button type="button" onclick="closeTutorialModal()" class="admin-modal-close" style="background: none; border: none; font-size: 1.5rem; color: var(--admin-text-secondary); cursor: pointer;">&times;</button>
    </div>

    <!-- Tabs Header -->
    <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.5rem; padding: 0.75rem 1.25rem; border-bottom: 1px solid var(--admin-border-main); background: var(--admin-card-bg-elevated); overflow-x: auto;">
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
          TechScout’s unified architecture links parent <code>contents</code> (for search, taxonomy, and authoring) with child <code>prompts</code> (for generation parameters). Sending the payload below to <code>/api/v1/webhook/{slug}</code> automatically creates the content record, prompts row, tags, and category relations!
        </p>

        <div style="background: #050811; border: 1px solid var(--admin-border-main); border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <span style="font-family: var(--admin-font-mono); font-size: 0.75rem; color: #34d399;">PROMPT INGESTION PAYLOAD (JSON)</span>
            <button type="button" onclick="copySnippet('tut-prompt-json')" class="admin-btn admin-btn-outline admin-btn-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">Copy JSON</button>
          </div>
          <pre id="tut-prompt-json" style="margin: 0; color: #f8fafc; font-family: var(--admin-font-mono); font-size: 0.78rem; white-space: pre-wrap;"><?= htmlspecialchars(json_encode(\App\Core\IncomingWebhookEngine::getSamplePayloadForTable('prompts'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) ?></pre>
        </div>
      </div>

      <!-- TAB 2: Multi-Table -->
      <div id="tut-pane-multitable" style="display: none;">
        <h4 style="color: #a855f7; margin: 0 0 0.5rem 0;">Multi-Table Atomic Ingestion Architecture</h4>
        <p style="font-size: 0.85rem; color: var(--admin-text-secondary); line-height: 1.5; margin-bottom: 1rem;">
          Need to insert across multiple tables in a single HTTP request? Use the top-level <code>tables</code> key. The engine wraps the entire execution in a MySQL database transaction with atomic rollback if any constraint fails.
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
          Ingesting AI tools creates entries in the directory catalog with outbound affiliate links, pricing tiers, and badges. Ingesting articles publishes content to the TechScout Blog with HTML markdown.
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
        <div style="flex-wrap: wrap; align-items: center; display: flex; flex-direction: column; gap: 1rem;">
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
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 5: ALL TABLES SAMPLE DATA GENERATOR                                 -->
<!-- ========================================================================= -->
<div id="sampleGeneratorModal" class="admin-modal-overlay" style="display: none;">
  <div class="admin-modal-backdrop" onclick="closeSampleGeneratorModal()"></div>
  <div class="admin-modal-box modal-amber" style="max-width: 780px; max-height: 88vh; display: flex; flex-direction: column; padding: 0;">
    <div class="admin-modal-header" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--admin-border-main); display: flex; justify-content: space-between; align-items: center; background: var(--admin-bg-surface);">
      <h3 style="margin: 0; color: var(--admin-text-primary); font-size: 1.15rem; display: flex; align-items: center; gap: 0.5rem; font-weight: 800;">
        <i data-lucide="sparkles" style="color: #a855f7;"></i> All Tables Sample Data Generator
      </h3>
      <button type="button" onclick="closeSampleGeneratorModal()" class="admin-modal-close" style="background: none; border: none; font-size: 1.5rem; color: var(--admin-text-secondary); cursor: pointer;">&times;</button>
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
          <?php foreach ($permittedTables as $tblName => $tblMeta): 
            if (in_array($tblName, ['prompts', 'ai_tools', 'articles', 'gpt_listings', 'jobs', 'categories'], true)) continue;
          ?>
            <option value="<?= htmlspecialchars($tblName) ?>"><?= htmlspecialchars($tblName) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="button" onclick="fetchTableSample(document.getElementById('sampleModalTableSelect').value)" class="admin-btn admin-btn-outline admin-btn-sm" style="color: #38bdf8;">
          <i data-lucide="refresh-cw"></i> Refresh
        </button>
      </div>

      <!-- JSON Preview Box -->
      <div style="background: #050811; border: 1px solid var(--admin-border-main); border-radius: 8px; padding: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
          <span id="samplePayloadHeading" style="font-family: var(--admin-font-mono); font-size: 0.75rem; color: #34d399;">SAMPLE PAYLOAD: prompts</span>
          <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.5rem;">
            <button type="button" onclick="copySnippet('sampleModalPayloadBox')" class="admin-btn admin-btn-outline admin-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem;">
              📋 Copy JSON
            </button>
            <button type="button" onclick="loadSampleModalIntoSimulator()" class="admin-btn admin-btn-primary admin-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.6rem; font-weight: 700;">
              ⚡ Load into Simulator
            </button>
          </div>
        </div>
        <pre id="sampleModalPayloadBox" style="height: 300px; overflow-y: auto; margin: 0; color: #f8fafc !important; font-family: var(--admin-font-mono); font-size: 0.78rem; white-space: pre-wrap;"><?= htmlspecialchars(json_encode(\App\Core\IncomingWebhookEngine::getSamplePayloadForTable('prompts'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) ?></pre>
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

const flattenJSON = (obj, prefix = '') => {
  let res = {};
  for (const k in obj) {
    if (typeof obj[k] === 'object' && obj[k] !== null && !Array.isArray(obj[k])) {
      Object.assign(res, flattenJSON(obj[k], prefix + k + '.'));
    } else {
      res[prefix + k] = obj[k];
    }
  }
  return res;
};

function updateJsonKeysDatalist(jsonObj) {
  const flatKeys = Object.keys(flattenJSON(jsonObj));
  const dl = document.getElementById('jsonKeysList');
  if (dl) {
    dl.innerHTML = '';
    flatKeys.forEach(k => {
      const opt = document.createElement('option');
      opt.value = k;
      dl.appendChild(opt);
    });
  }
}
const PERMITTED_TABLES = <?= json_encode($permittedTables, JSON_HEX_TAG | JSON_HEX_AMP) ?>;

// Global Escape Key Listener for Modals
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeLiveListener();
    closeNewIncomingModal();
    closeEditIncomingModal();
    closeTutorialModal();
    closeSampleGeneratorModal();
  }
});

function openLiveListener(wh) {
  currentActiveWebhook = wh;
  document.getElementById('studioWebhookName').textContent = wh.name;
  document.getElementById('studioTargetTable').textContent = wh.target_table;
  document.getElementById('studioEndpointUrl').textContent = '<?= url('/api/v1/webhook/') ?>' + wh.slug;
  document.getElementById('mappingWebhookId').value = wh.id;

  // Load sample for simulator
  loadTableSampleIntoSimulator();

  // Populate Field Mappings
  renderFieldMappings(wh);

  // Start Realtime Poller
  lastLogId = 0;
  pollLatestLogs();
  if (pollInterval) clearInterval(pollInterval);
  pollInterval = setInterval(pollLatestLogs, 3000);

  document.body.classList.add('ts-modal-open');
  document.getElementById('listenerStudioModal').style.display = 'flex';
}

function closeLiveListener() {
  document.getElementById('listenerStudioModal').style.display = 'none';
  document.body.classList.remove('ts-modal-open');
  if (pollInterval) {
    clearInterval(pollInterval);
    pollInterval = null;
  }
  currentActiveWebhook = null;
}

function openNewIncomingModal() {
  document.body.classList.add('ts-modal-open');
  document.getElementById('newIncomingModal').style.display = 'flex';
}
function closeNewIncomingModal() {
  document.getElementById('newIncomingModal').style.display = 'none';
  document.body.classList.remove('ts-modal-open');
}

function openEditIncomingModal(wh) {
  document.getElementById('edit_wh_id').value = wh.id;
  document.getElementById('edit_wh_name').value = wh.name || '';
  document.getElementById('edit_wh_slug').value = wh.slug || '';
  const targetTables = (wh.target_table || 'prompts').split(',').map(t => t.trim());
  const selectEl = document.getElementById('edit_wh_table');
  for (let i = 0; i < selectEl.options.length; i++) {
    selectEl.options[i].selected = targetTables.includes(selectEl.options[i].value);
  }
  document.getElementById('edit_wh_auth').value = wh.auth_type || 'none';
  document.getElementById('edit_wh_secret').value = wh.secret_key || '';
  document.getElementById('edit_wh_mode').value = wh.ingestion_mode || 'live_ingest';
  document.getElementById('edit_wh_active').checked = (parseInt(wh.is_active, 10) === 1);
  document.getElementById('editModalHeading').textContent = 'Edit Webhook: ' + (wh.name || wh.slug);

  document.body.classList.add('ts-modal-open');
  document.getElementById('editIncomingModal').style.display = 'flex';
}

function closeEditIncomingModal() {
  document.getElementById('editIncomingModal').style.display = 'none';
  document.body.classList.remove('ts-modal-open');
}

function openTutorialModal() {
  document.body.classList.add('ts-modal-open');
  document.getElementById('tutorialModal').style.display = 'flex';
}
function closeTutorialModal() {
  document.getElementById('tutorialModal').style.display = 'none';
  document.body.classList.remove('ts-modal-open');
}

function openSampleGeneratorModal() {
  document.body.classList.add('ts-modal-open');
  document.getElementById('sampleGeneratorModal').style.display = 'flex';
}
function closeSampleGeneratorModal() {
  document.getElementById('sampleGeneratorModal').style.display = 'none';
  document.body.classList.remove('ts-modal-open');
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
  if (!currentActiveWebhook) {
    const firstWh = <?= !empty($incomingWebhooks) ? json_encode($incomingWebhooks[0], JSON_HEX_TAG | JSON_HEX_AMP) : 'null' ?>;
    if (firstWh) openLiveListener(firstWh);
  }
  document.getElementById('simPayloadInput').value = payload;
  handleSimulatorChange();
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
      handleSimulatorChange();
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
      handleSimulatorChange();
    }
  } catch(e) {}
}

function renderFieldMappings(wh) {
  const container = document.getElementById('mappingRowsContainer');
  container.innerHTML = '';

  const rules = JSON.parse(wh.mapping_rules_json || '[]');
  if (rules.length === 0) {
    addMappingRow('', '');
  } else {
    rules.forEach(r => addMappingRow(r.json_key, r.target_column));
  }
}

function addMappingRow(jsonKey = '', targetCol = '') {
  const container = document.getElementById('mappingRowsContainer');
  const tableRaw = currentActiveWebhook ? currentActiveWebhook.target_table : 'prompts';
  const tables = tableRaw.split(',').map(t => t.trim());
  let colOptions = '';
  if (tables.length > 1) {
    tables.forEach(t => {
      const tableMeta = PERMITTED_TABLES[t];
      if (tableMeta && tableMeta.columns) {
        colOptions += `<optgroup label="Table: ${escapeHtml(t)}">`;
        tableMeta.columns.sort().forEach(c => {
          const val = t + '.' + c;
          const sel = (val === targetCol || c === targetCol) ? 'selected' : '';
          colOptions += `<option value="${escapeHtml(val)}" ${sel}>${escapeHtml(c)}</option>`;
        });
        colOptions += `</optgroup>`;
      }
    });
  } else {
    let allCols = [];
    tables.forEach(t => {
      const tableMeta = PERMITTED_TABLES[t];
      if (tableMeta && tableMeta.columns) allCols = allCols.concat(tableMeta.columns);
    });
    allCols = [...new Set(allCols)].sort();
    allCols.forEach(c => {
      const sel = (c === targetCol || targetCol.endsWith('.' + c)) ? 'selected' : '';
      colOptions += `<option value="${escapeHtml(c)}" ${sel}>${escapeHtml(c)}</option>`;
    });
  }

  const row = document.createElement('div');
  row.className = 'mapping-row-wrap';
  row.style.cssText = 'display: flex; flex-direction: column; gap: 0.3rem; background: var(--admin-card-bg-elevated); padding: 0.4rem 0.6rem; border-radius: 6px; border: 1px solid var(--admin-border-main);';

  row.innerHTML = `
    <div style="display: flex; gap: 0.5rem; align-items: center; width: 100%;">
      <input type="text" list="jsonKeysList" class="admin-input map-json-key" placeholder="JSON Key (e.g. data.title)" value="${escapeHtml(jsonKey)}" oninput="updateFieldPreviews()" onchange="updateFieldPreviews()" style="flex: 1; font-family: var(--admin-font-mono); font-size: 0.78rem;">
      <span style="color: var(--admin-text-secondary);">&rarr;</span>
      <select class="admin-input map-target-col" style="flex: 1; font-family: var(--admin-font-mono); font-size: 0.78rem;">
        ${colOptions}
      </select>
      <button type="button" onclick="this.closest('.mapping-row-wrap').remove(); updateFieldPreviews();" class="admin-btn admin-btn-outline admin-btn-sm" style="color: #ef4444; padding: 0.2rem 0.4rem;" title="Remove Row">&times;</button>
    </div>
    <div style="font-size: 0.72rem; color: var(--admin-text-secondary); padding-left: 0.2rem;">
      Preview: <span class="map-preview-val" style="font-family: var(--admin-font-mono); color: #94a3b8;">N/A</span>
    </div>
  `;

  container.appendChild(row);
  if (typeof updateFieldPreviews === 'function') updateFieldPreviews();
}

window.updateFieldPreviews = function() {
    const simEl = document.getElementById('simPayloadInput');
    if (!simEl) return;
    let payload = {};
    try { payload = JSON.parse(simEl.value); } catch(e) {}
    const flat = flattenJSON(payload);
    
    document.querySelectorAll('#mappingRowsContainer > .mapping-row-wrap').forEach(row => {
        const key = row.querySelector('.map-json-key').value.trim();
        const previewEl = row.querySelector('.map-preview-val');
        if (!key) {
             previewEl.textContent = 'Enter key...';
             previewEl.style.color = 'var(--admin-text-secondary)';
        } else if (flat[key] !== undefined) {
             let val = String(flat[key]);
             if (val.length > 50) val = val.substring(0, 50) + '...';
             previewEl.textContent = val;
             previewEl.style.color = '#34d399';
        } else {
             previewEl.textContent = 'Not found in sample payload';
             previewEl.style.color = '#f87171';
        }
    });
};

async function saveFieldMappings(e) {
  e.preventDefault();
  if (!currentActiveWebhook) return;

  const rows = document.querySelectorAll('#mappingRowsContainer > div');
  const rules = [];

  rows.forEach(r => {
    const key = r.querySelector('.map-json-key').value.trim();
    const colVal = r.querySelector('.map-target-col').value.trim();
    if (key && colVal) {
      if (colVal.includes('.')) {
        const parts = colVal.split('.');
        rules.push({ json_key: key, target_table: parts[0], target_column: parts[1] });
      } else {
        rules.push({ json_key: key, target_column: colVal });
      }
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
      alert('✓ Field mapping rules saved successfully!');
    } else {
      alert('Error saving rules: ' + data.error);
    }
  } catch(err) {
    alert('Error: ' + err.message);
  }
}

async function submitEditWebhookForm(e) {
  e.preventDefault();
  const form = e.target;
  const fd = new FormData(form);
  try {
    const res = await fetch(form.action || '<?= url('/admin/incoming-webhooks/save') ?>', {
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
      alert('✓ Webhook settings updated successfully!');
      location.reload();
    } else {
      alert('Error updating webhook: ' + (data.error || 'Failed'));
    }
  } catch(err) {
    alert('Error saving webhook: ' + err.message);
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
        entry.style.cssText = 'border-bottom: 1px dashed rgba(148,163,184,0.15); padding: 0.75rem 0;';
        
        let parsedPayload = {};
        try { parsedPayload = JSON.parse(log.payload_json); } catch(e) {}
        

        const flatFields = flattenJSON(parsedPayload);
        let fieldsHtml = '';
        if (Object.keys(flatFields).length > 0) {
          for (const k in flatFields) {
            fieldsHtml += `<div style="font-size: 0.76rem; margin-bottom: 0.15rem;"><strong style="color:#a855f7;">${escapeHtml(k)}</strong> <span style="color:var(--admin-text-secondary);">=</span> <span style="color:#10b981;">${escapeHtml(String(flatFields[k]).substring(0,100))}${String(flatFields[k]).length>100?'...':''}</span></div>`;
          }
        } else {
          fieldsHtml = `<pre style="margin: 0.2rem 0; color: #f8fafc; font-size: 0.75rem; white-space: pre-wrap;">${escapeHtml(log.payload_json)}</pre>`;
        }

        entry.innerHTML = `
          <div style="color: #38bdf8; font-size: 0.72rem; margin-bottom: 0.4rem; font-weight: 700;">[${log.created_at}] Status: ${log.status.toUpperCase()} &bull; Code: ${log.response_code} &bull; Affected: ${log.records_affected}</div>
          <div style="background: rgba(15,23,42,0.5); padding: 0.5rem; border-radius: 6px; border: 1px solid rgba(255,255,255,0.05);">${fieldsHtml}</div>
        `;
        viewer.innerHTML = ''; // Only show latest to prevent clutter
        viewer.appendChild(entry);
      });
      document.getElementById('streamCounter').textContent = data.logs.length + ' payloads synced';
    }
  } catch(e) {}
}

function handleSimulatorChange() {
  const val = document.getElementById('simPayloadInput').value;
  try {
    const obj = JSON.parse(val);
    updateJsonKeysDatalist(obj);
    
    if (currentActiveWebhook) {
      const baseUrl = '<?= url('/api/v1/webhook/') ?>' + currentActiveWebhook.slug;
      const box = document.getElementById('dynamicCurlPreviewBox');
      box.style.display = 'block';
      
      let curlCmd = 'curl -X POST "' + baseUrl + '" \\\n  -H "Content-Type: application/json"';
      if (currentActiveWebhook.auth_type === 'bearer') {
        curlCmd += ' \\\n  -H "Authorization: Bearer ' + currentActiveWebhook.secret_key + '"';
      }
      curlCmd += ' \\\n  -d \'' + val.replace(/'/g, "'\\''") + '\'';
      document.getElementById('dynPostCurl').textContent = curlCmd;

      let queryStr = '?';
      const flat = flattenJSON(obj);
      for (let k in flat) {
        queryStr += encodeURIComponent(k) + '=' + encodeURIComponent(flat[k]) + '&';
      }
      if (currentActiveWebhook.auth_type === 'token' || currentActiveWebhook.auth_type === 'bearer') {
        queryStr += 'token=' + encodeURIComponent(currentActiveWebhook.secret_key);
      } else {
        queryStr = queryStr.slice(0, -1);
      }
      document.getElementById('dynGetUrl').textContent = baseUrl + queryStr;
    }
  } catch(e) {
    document.getElementById('dynamicCurlPreviewBox').style.display = 'none';
  }
  if (typeof updateFieldPreviews === 'function') updateFieldPreviews();
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
function filterActivityLogs() {
  const filterVal = document.getElementById('logFilterInput').value.toLowerCase();
  const rows = document.querySelectorAll('#page_pane_logs table tbody tr');
  rows.forEach(row => {
    if (row.cells.length < 2) return; // Skip empty row
    const text = row.innerText.toLowerCase();
    row.style.display = text.includes(filterVal) ? '' : 'none';
  });
}

function switchPageTab(tab) {
  const panes = ['endpoints', 'logs'];
  panes.forEach(p => {
    const btn = document.getElementById('page_tab_' + p);
    const pane = document.getElementById('page_pane_' + p);
    if (p === tab) {
      btn.className = 'admin-btn admin-btn-primary admin-btn-sm';
      pane.style.display = 'block';
    } else {
      btn.className = 'admin-btn admin-btn-outline admin-btn-sm';
      pane.style.display = 'none';
    }
  });
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
