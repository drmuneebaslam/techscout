<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-main">
  <!-- Topbar -->
  <div class="admin-topbar">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-indigo">ENTERPRISE API BUILDER &bull; DATA GATEWAY</span>
        <span style="font-size: 0.8rem; color: var(--admin-text-secondary);">Dynamic Table Introspection &amp; Custom REST JSON Endpoints</span>
      </div>
      <h1 style="font-size: 1.6rem; color: var(--admin-text-primary); margin: 0; font-family: var(--admin-font-heading); font-weight: 800;">
        Custom API Endpoints &amp; Schema Inspector
      </h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <a href="<?= url('/admin/incoming-webhooks') ?>" class="admin-btn admin-btn-outline admin-btn-sm" style="font-weight: 600;">
        <i data-lucide="arrow-down-to-line"></i> Incoming Webhooks
      </a>
      <a href="<?= url('/admin/api-keys') ?>" class="admin-btn admin-btn-outline admin-btn-sm" style="font-weight: 600;">
        <i data-lucide="key"></i> API Keys
      </a>
      <button type="button" onclick="openNewEndpointModal()" class="admin-btn admin-btn-primary admin-btn-sm" style="font-weight: 700;">
        <i data-lucide="plus"></i> New API Endpoint ⚡
      </button>
    </div>
  </div>

  <!-- Stat Cards -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <div class="admin-card" style="padding: 1.25rem; border-radius: 12px; border: 1px solid var(--admin-border-main); background: var(--admin-card-bg);">
      <div style="font-size: 0.78rem; color: var(--admin-text-secondary); text-transform: uppercase; font-weight: 700;">Total Endpoints</div>
      <div style="font-size: 1.8rem; font-weight: 800; color: #6366f1; margin: 0.25rem 0;"><?= count($endpoints ?? []) ?></div>
      <div style="font-size: 0.75rem; color: var(--admin-text-secondary);">Active endpoints available via HTTP</div>
    </div>
    <div class="admin-card" style="padding: 1.25rem; border-radius: 12px; border: 1px solid var(--admin-border-main); background: var(--admin-card-bg);">
      <div style="font-size: 0.78rem; color: var(--admin-text-secondary); text-transform: uppercase; font-weight: 700;">Total API Dispatches</div>
      <div style="font-size: 1.8rem; font-weight: 800; color: #10b981; margin: 0.25rem 0;">
        <?= number_format(array_sum(array_column($endpoints ?? [], 'request_count'))) ?>
      </div>
      <div style="font-size: 0.75rem; color: var(--admin-text-secondary);">Total lifetime requests served</div>
    </div>
    <div class="admin-card" style="padding: 1.25rem; border-radius: 12px; border: 1px solid var(--admin-border-main); background: var(--admin-card-bg);">
      <div style="font-size: 0.78rem; color: var(--admin-text-secondary); text-transform: uppercase; font-weight: 700;">Introspection Types</div>
      <div style="font-size: 1.8rem; font-weight: 800; color: #38bdf8; margin: 0.25rem 0;">4 Modes</div>
      <div style="font-size: 0.75rem; color: var(--admin-text-secondary);">Table List, Table Schema, Data, Custom JSON</div>
    </div>
  </div>

  <!-- Endpoints Cards Grid -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.25rem; margin-bottom: 2.5rem;">
    <?php if (!empty($endpoints)): ?>
      <?php foreach ($endpoints as $ep): 
        $endpointUrl = url('/api/v1/custom/' . $ep['slug']);
        $typeBadges = [
          'table_list' => ['label' => 'TABLE CATALOG', 'class' => 'ts-badge-cyan'],
          'table_schema' => ['label' => 'SCHEMA INTROSPECT', 'class' => 'ts-badge-pink'],
          'table_data' => ['label' => 'TABLE DATA FEED', 'class' => 'ts-badge-green'],
          'custom_json' => ['label' => 'CUSTOM JSON BUILDER', 'class' => 'ts-badge-amber']
        ];
        $badge = $typeBadges[$ep['endpoint_type']] ?? ['label' => strtoupper($ep['endpoint_type']), 'class' => 'ts-badge-cyan'];
      ?>
        <div class="admin-card" style="padding: 1.25rem; border-radius: 12px; border: 1px solid var(--admin-border-main); background: var(--admin-card-bg); display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
              <h3 style="font-size: 1.05rem; margin: 0; color: var(--admin-text-primary); font-weight: 700;">
                <?= htmlspecialchars($ep['name']) ?>
              </h3>
              <span class="ts-badge <?= !empty($ep['is_active']) ? 'ts-badge-green' : 'ts-badge-pink' ?>" style="font-size: 0.68rem;">
                <?= !empty($ep['is_active']) ? 'ACTIVE' : 'PAUSED' ?>
              </span>
            </div>

            <p style="font-size: 0.8rem; color: var(--admin-text-secondary); margin: 0 0 0.75rem 0; line-height: 1.4;">
              <?= htmlspecialchars($ep['description'] ?? 'Custom JSON REST endpoint') ?>
            </p>

            <div style="display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.75rem; flex-wrap: wrap;">
              <span class="ts-badge <?= $badge['class'] ?>" style="font-size: 0.7rem; font-weight: 800;">
                <?= $badge['label'] ?>
              </span>
              <?php if (!empty($ep['target_table'])): ?>
                <span class="ts-badge ts-badge-indigo" style="font-size: 0.7rem; font-family: var(--admin-font-mono);">
                  Table: <?= htmlspecialchars($ep['target_table']) ?>
                </span>
              <?php endif; ?>
              <span class="ts-badge <?= !empty($ep['auth_required']) ? 'ts-badge-amber' : 'ts-badge-green' ?>" style="font-size: 0.7rem;">
                <?= !empty($ep['auth_required']) ? '🔒 TOKEN REQUIRED' : '🌐 PUBLIC OPEN' ?>
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
              Requests: <strong><?= number_format((int)($ep['request_count'] ?? 0)) ?></strong> &bull; 
              Last Access: 
              <?php if (!empty($ep['last_accessed_at'])): ?>
                <time class="ts-time" datetime="<?= htmlspecialchars(date('c', strtotime($ep['last_accessed_at']))) ?>">
                  <?= htmlspecialchars($ep['last_accessed_at']) ?>
                </time>
              <?php else: ?>
                Never
              <?php endif; ?>
            </div>
          </div>

          <div style="display: flex; gap: 0.5rem; border-top: 1px solid var(--admin-border-main); padding-top: 0.75rem;">
            <button type="button" onclick='openApiTester(<?= json_encode($ep) ?>)' class="admin-btn admin-btn-primary admin-btn-sm" style="flex: 1; font-weight: 700; font-size: 0.78rem;">
              <i data-lucide="play"></i> Live Sandbox Tester
            </button>
            <form action="<?= url('/admin/custom-api/toggle/' . $ep['id']) ?>" method="POST" style="margin: 0;">
              <button type="submit" class="admin-btn admin-btn-outline admin-btn-sm" title="Toggle Active Status">
                <i data-lucide="<?= !empty($ep['is_active']) ? 'pause' : 'play' ?>" style="width: 14px; height: 14px;"></i>
              </button>
            </form>
            <form action="<?= url('/admin/custom-api/delete/' . $ep['id']) ?>" method="POST" onsubmit="return confirm('Permanently delete this custom API endpoint?');" style="margin: 0;">
              <button type="submit" class="admin-btn admin-btn-outline admin-btn-sm" style="color: #ef4444;" title="Delete Endpoint">
                <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="admin-card" style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
        <i data-lucide="code" style="width: 44px; height: 44px; color: var(--admin-text-secondary); margin-bottom: 0.75rem;"></i>
        <h3 style="color: var(--admin-text-primary); margin: 0 0 0.5rem 0;">No Custom API Endpoints Yet</h3>
        <p style="color: var(--admin-text-secondary); font-size: 0.9rem; margin-bottom: 1.25rem;">
          Build dynamic REST endpoints to expose database tables, inspect table schemas, or serve custom JSON feeds.
        </p>
        <button type="button" onclick="openNewEndpointModal()" class="admin-btn admin-btn-primary">Create First API Endpoint</button>
      </div>
    <?php endif; ?>
  </div>

  <!-- Live Sandbox & Response Inspector Drawer -->
  <div id="apiSandboxDrawer" class="admin-table-wrap" style="display: none; padding: 1.5rem; margin-bottom: 2.5rem; border: 2px solid var(--ts-primary, #6366f1); border-radius: 14px; background: var(--admin-card-bg);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
      <div style="display: flex; align-items: center; gap: 0.5rem;">
        <span class="ts-badge ts-badge-indigo" style="font-weight: 800;">LIVE API SANDBOX</span>
        <h3 style="margin: 0; color: var(--admin-text-primary);" id="sandboxEndpointTitle">API Sandbox</h3>
      </div>
      <button type="button" onclick="closeApiTester()" class="admin-btn admin-btn-outline admin-btn-sm" style="color: #ef4444;">
        &times; Close Sandbox
      </button>
    </div>

    <!-- Request Builder Bar -->
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap;">
      <span class="ts-badge ts-badge-green" style="font-size: 0.82rem; font-weight: 800; padding: 0.45rem 0.75rem; display: flex; align-items: center;">GET</span>
      <input type="text" id="sandboxRequestUrl" class="admin-input" style="flex: 1; font-family: var(--admin-font-mono); font-size: 0.82rem;" readonly>
      <button type="button" onclick="executeSandboxQuery()" class="admin-btn admin-btn-primary admin-btn-sm" style="font-weight: 700; padding: 0 1.25rem;">
        🚀 Send Request
      </button>
    </div>

    <!-- Response Preview Pane -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
      <span style="font-size: 0.8rem; font-weight: 700; color: var(--admin-text-secondary);">JSON Response Body</span>
      <div style="display: flex; gap: 0.5rem; align-items: center;">
        <span id="sandboxStatusBadge" class="ts-badge" style="font-size: 0.7rem; display: none;"></span>
        <button type="button" onclick="copySandboxResponse()" class="admin-btn admin-btn-outline admin-btn-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
          📋 Copy JSON
        </button>
      </div>
    </div>
    <pre id="sandboxResponseBody" style="height: 320px; overflow-y: auto; background: #050811; border: 1px solid var(--admin-border-main); border-radius: 8px; padding: 1rem; font-family: var(--admin-font-mono); font-size: 0.82rem; color: #34d399; margin: 0;"></pre>
  </div>
</div>

<!-- Modal: Create New API Endpoint -->
<div id="newEndpointModal" class="admin-modal" style="display: none;">
  <div class="admin-modal-backdrop" onclick="closeNewEndpointModal()"></div>
  <div class="admin-modal-content" style="max-width: 600px;">
    <div class="admin-modal-header">
      <h3 style="margin: 0; color: var(--admin-text-primary);">Create Custom API Endpoint</h3>
      <button type="button" onclick="closeNewEndpointModal()" class="admin-modal-close">&times;</button>
    </div>
    <form action="<?= url('/admin/custom-api/save') ?>" method="POST" style="padding: 1.25rem;">
      <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Endpoint Name</label>
        <input type="text" name="name" placeholder="e.g. Prompts Public Catalog, Schema Inspector" class="admin-input" required>
      </div>

      <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Endpoint Slug (URL Path)</label>
        <div style="display: flex; align-items: center; gap: 0.4rem;">
          <span style="font-size: 0.82rem; color: var(--admin-text-secondary); font-family: var(--admin-font-mono);"><?= url('/api/v1/custom/') ?></span>
          <input type="text" name="slug" placeholder="prompts-catalog" class="admin-input" style="flex: 1;" required pattern="[a-z0-9_-]+">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Endpoint Output Type</label>
          <select name="endpoint_type" id="modalEndpointType" class="admin-input" onchange="toggleEndpointTypeFields(this.value)">
            <option value="table_data">Single Table Data View</option>
            <option value="table_schema">Table Schema / Column Inspector</option>
            <option value="table_list">Catalog List of Database Tables</option>
            <option value="custom_json">Custom JSON Response Builder</option>
          </select>
        </div>
        <div id="targetTableGroup">
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Target Database Table</label>
          <select name="target_table" class="admin-input">
            <?php foreach ($allowedTables as $tbl => $tblDesc): ?>
              <option value="<?= htmlspecialchars($tbl) ?>"><?= htmlspecialchars($tbl) ?> &mdash; <?= htmlspecialchars($tblDesc) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div id="tableDataFields">
        <div style="margin-bottom: 1rem;">
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Selected Columns (comma-separated, or * for all)</label>
          <input type="text" name="selected_columns" placeholder="title, slug, prompt_template, aspect_ratio" class="admin-input">
          <span style="font-size: 0.75rem; color: var(--admin-text-secondary);">Leave blank or enter * to include all accessible columns.</span>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Order By Column</label>
            <input type="text" name="order_by_column" value="id" class="admin-input">
          </div>
          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Sort Direction</label>
            <select name="order_direction" class="admin-input">
              <option value="DESC">DESC (Newest first)</option>
              <option value="ASC">ASC (Oldest first)</option>
            </select>
          </div>
        </div>
      </div>

      <div id="customJsonFields" style="display: none; margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Custom JSON Template</label>
        <textarea name="custom_response_template" rows="5" class="admin-input" style="font-family: var(--admin-font-mono); font-size: 0.8rem;"><?= htmlspecialchars(json_encode([
          "status" => "online",
          "service" => "TechScout Data Hub",
          "version" => "2.5.0",
          "generated_at" => "{{timestamp_gmt}}"
        ], JSON_PRETTY_PRINT)) ?></textarea>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; font-weight: 600; cursor: pointer; color: var(--admin-text-primary);">
          <input type="checkbox" name="auth_required" value="1">
          Require Authentication (Bearer Token or ?token=)
        </label>
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="closeNewEndpointModal()" class="admin-btn admin-btn-outline">Cancel</button>
        <button type="submit" class="admin-btn admin-btn-primary" style="font-weight: 700;">Save API Endpoint 🚀</button>
      </div>
    </form>
  </div>
</div>

<script>
function openNewEndpointModal() {
  document.getElementById('newEndpointModal').style.display = 'flex';
}

function closeNewEndpointModal() {
  document.getElementById('newEndpointModal').style.display = 'none';
}

function toggleEndpointTypeFields(type) {
  const tableDataFields = document.getElementById('tableDataFields');
  const targetTableGroup = document.getElementById('targetTableGroup');
  const customJsonFields = document.getElementById('customJsonFields');

  if (type === 'custom_json') {
    tableDataFields.style.display = 'none';
    targetTableGroup.style.display = 'none';
    customJsonFields.style.display = 'block';
  } else if (type === 'table_list') {
    tableDataFields.style.display = 'none';
    targetTableGroup.style.display = 'none';
    customJsonFields.style.display = 'none';
  } else if (type === 'table_schema') {
    tableDataFields.style.display = 'none';
    targetTableGroup.style.display = 'block';
    customJsonFields.style.display = 'none';
  } else {
    tableDataFields.style.display = 'block';
    targetTableGroup.style.display = 'block';
    customJsonFields.style.display = 'none';
  }
}

function copyToClipboard(text) {
  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(text).then(() => {
      alert('Endpoint URL copied to clipboard!');
    });
  } else {
    prompt('Copy endpoint URL:', text);
  }
}

let activeSandboxUrl = '';

function openApiTester(endpoint) {
  const drawer = document.getElementById('apiSandboxDrawer');
  const title = document.getElementById('sandboxEndpointTitle');
  const urlInput = document.getElementById('sandboxRequestUrl');
  const responseBody = document.getElementById('sandboxResponseBody');

  drawer.style.display = 'block';
  drawer.scrollIntoView({ behavior: 'smooth' });

  title.innerText = endpoint.name + ' (' + endpoint.endpoint_type + ')';
  activeSandboxUrl = '<?= url('/api/v1/custom/') ?>' + endpoint.slug;
  urlInput.value = activeSandboxUrl;

  responseBody.textContent = '// Click "Send Request" to test live endpoint...';
  document.getElementById('sandboxStatusBadge').style.display = 'none';
}

function closeApiTester() {
  document.getElementById('apiSandboxDrawer').style.display = 'none';
}

function executeSandboxQuery() {
  const url = document.getElementById('sandboxRequestUrl').value;
  const responseBody = document.getElementById('sandboxResponseBody');
  const statusBadge = document.getElementById('sandboxStatusBadge');

  responseBody.textContent = '// Fetching response...';

  const startTime = performance.now();
  fetch(url)
    .then(async r => {
      const elapsed = Math.round(performance.now() - startTime);
      const data = await r.json();
      responseBody.textContent = JSON.stringify(data, null, 2);

      statusBadge.style.display = 'inline-block';
      statusBadge.className = 'ts-badge ' + (r.ok ? 'ts-badge-green' : 'ts-badge-pink');
      statusBadge.textContent = 'HTTP ' + r.status + ' (' + elapsed + 'ms)';
    })
    .catch(err => {
      responseBody.textContent = 'Error: ' + err.toString();
      statusBadge.style.display = 'inline-block';
      statusBadge.className = 'ts-badge ts-badge-pink';
      statusBadge.textContent = 'FETCH ERROR';
    });
}

function copySandboxResponse() {
  const text = document.getElementById('sandboxResponseBody').textContent;
  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(text).then(() => {
      alert('JSON response copied to clipboard!');
    });
  }
}

document.addEventListener('DOMContentLoaded', () => {
  if (window.lucide) lucide.createIcons();
});
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
