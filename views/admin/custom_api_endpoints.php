<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<!-- Topbar -->
<div class="admin-topbar">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-indigo">ENTERPRISE API BUILDER &bull; DATA GATEWAY</span>
        <span style="font-size: 0.8rem; color: var(--admin-text-secondary);">Dynamic Table Introspection, Whole DB Blueprint &amp; Custom REST Endpoints</span>
      </div>
      <h1 style="font-size: 1.6rem; color: var(--admin-text-primary); margin: 0; font-family: var(--admin-font-heading); font-weight: 800;">
        Custom API Gateway &amp; Schema Inspector
      </h1>
    </div>

    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
      <a href="<?= url('/admin/incoming-webhooks') ?>" class="admin-btn admin-btn-outline admin-btn-sm" style="font-weight: 600;">
        <i data-lucide="arrow-down-to-line"></i> Webhooks
      </a>
      <button type="button" onclick="openMultiTableQueryModal()" class="admin-btn admin-btn-outline admin-btn-sm" style="font-weight: 700; color: #a855f7;">
        <i data-lucide="git-merge"></i> Multi-Table Query 🔗
      </button>
      <button type="button" onclick="openAllTablesCatalogModal()" class="admin-btn admin-btn-outline admin-btn-sm" style="font-weight: 700; color: #10b981;">
        <i data-lucide="code"></i> All Tables JSON Catalog ⚡
      </button>
      <button type="button" onclick="loadWholeDatabaseBlueprint()" class="admin-btn admin-btn-outline admin-btn-sm" style="font-weight: 600; color: #38bdf8;">
        <i data-lucide="database"></i> Describe DB 📐
      </button>
      <button type="button" onclick="openNewEndpointModal()" class="admin-btn admin-btn-primary admin-btn-sm" style="font-weight: 700;">
        <i data-lucide="plus"></i> New Endpoint 🚀
      </button>
    </div>
  </div>

  <!-- Stat Cards -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <div class="admin-card" style="padding: 1.25rem; border-radius: 12px; border: 1px solid var(--admin-border-main); background: var(--admin-card-bg);">
      <div style="font-size: 0.78rem; color: var(--admin-text-secondary); text-transform: uppercase; font-weight: 700;">Total Endpoints</div>
      <div style="font-size: 1.8rem; font-weight: 800; color: #6366f1; margin: 0.25rem 0;"><?= count($endpoints ?? []) ?></div>
      <div style="font-size: 0.75rem; color: var(--admin-text-secondary);">Configured custom REST endpoints</div>
    </div>
    <div class="admin-card" style="padding: 1.25rem; border-radius: 12px; border: 1px solid var(--admin-border-main); background: var(--admin-card-bg);">
      <div style="font-size: 0.78rem; color: var(--admin-text-secondary); text-transform: uppercase; font-weight: 700;">Total Tables In Database</div>
      <div style="font-size: 1.8rem; font-weight: 800; color: #38bdf8; margin: 0.25rem 0;">
        <?= count($allTablesMetadata ?? $allowedTables ?? []) ?>
      </div>
      <div style="font-size: 0.75rem; color: var(--admin-text-secondary);">Dynamic introspection &bull; No whitelisting</div>
    </div>
    <div class="admin-card" style="padding: 1.25rem; border-radius: 12px; border: 1px solid var(--admin-border-main); background: var(--admin-card-bg);">
      <div style="font-size: 0.78rem; color: var(--admin-text-secondary); text-transform: uppercase; font-weight: 700;">Database Records (Estimated)</div>
      <div style="font-size: 1.8rem; font-weight: 800; color: #10b981; margin: 0.25rem 0;">
        <?= number_format(array_sum(array_column($allTablesMetadata ?? [], 'table_rows'))) ?>
      </div>
      <div style="font-size: 0.75rem; color: var(--admin-text-secondary);">Across all platform tables</div>
    </div>
    <div class="admin-card" style="padding: 1.25rem; border-radius: 12px; border: 1px solid var(--admin-border-main); background: var(--admin-card-bg);">
      <div style="font-size: 0.78rem; color: var(--admin-text-secondary); text-transform: uppercase; font-weight: 700;">API Access Guard</div>
      <div style="font-size: 1.8rem; font-weight: 800; color: #f59e0b; margin: 0.25rem 0;">Admin Only</div>
      <div style="font-size: 0.75rem; color: var(--admin-text-secondary);">Strictly restricted from regular users</div>
    </div>
  </div>

  <!-- DATABASE BLUEPRINT & LIVE SCHEMA INSPECTOR PANEL -->
  <div class="admin-card" style="padding: 1.5rem; border-radius: 14px; border: 1px solid var(--admin-border-main); background: var(--admin-card-bg); margin-bottom: 2.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
      <div>
        <h3 style="margin: 0; color: var(--admin-text-primary); font-size: 1.15rem; display: flex; align-items: center; gap: 0.5rem;">
          <i data-lucide="layers" style="color: #6366f1;"></i> Dynamic Database Blueprint &amp; Table Introspector
        </h3>
        <p style="margin: 0.25rem 0 0 0; color: var(--admin-text-secondary); font-size: 0.82rem;">
          Select any table in the database to instantly introspect its columns, data types, indexes, and sample rows.
        </p>
      </div>

      <div style="display: flex; gap: 0.5rem; align-items: center;">
        <select id="quickTableSelect" onchange="inspectSelectedTable(this.value)" class="admin-input" style="min-width: 240px; font-weight: 600;">
          <option value="">-- Choose Table to Describe --</option>
          <?php foreach ($allowedTables as $tblName => $tblDesc): ?>
            <option value="<?= htmlspecialchars($tblName) ?>"><?= htmlspecialchars($tblName) ?> (<?= htmlspecialchars($tblDesc) ?>)</option>
          <?php endforeach; ?>
        </select>
        <button type="button" onclick="inspectSelectedTable(document.getElementById('quickTableSelect').value)" class="admin-btn admin-btn-outline admin-btn-sm" style="color: #6366f1;">
          <i data-lucide="search"></i> Inspect
        </button>
      </div>
    </div>

    <!-- Active Table Schema Display Box -->
    <div id="tableInspectionBox" style="display: none; background: var(--admin-card-bg-elevated); border: 1px solid var(--admin-border-main); border-radius: 10px; padding: 1.25rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
        <div>
          <h4 id="inspectTableName" style="margin: 0; font-size: 1.1rem; color: #38bdf8; font-family: var(--admin-font-mono);">table_name</h4>
          <span id="inspectTableStats" style="font-size: 0.75rem; color: var(--admin-text-secondary);">0 columns &bull; 0 records</span>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
          <button type="button" onclick="testTableEndpoint('query')" class="admin-btn admin-btn-outline admin-btn-sm" style="font-size: 0.75rem;">
            🔍 Query All (GET)
          </button>
          <button type="button" onclick="testTableEndpoint('get_by_id')" class="admin-btn admin-btn-outline admin-btn-sm" style="font-size: 0.75rem; color: #38bdf8;">
            🎯 Get by ID (GET)
          </button>
          <button type="button" onclick="testTableEndpoint('insert')" class="admin-btn admin-btn-outline admin-btn-sm" style="font-size: 0.75rem; color: #10b981;">
            ➕ Insert Record (POST)
          </button>
          <button type="button" onclick="testTableEndpoint('schema')" class="admin-btn admin-btn-outline admin-btn-sm" style="font-size: 0.75rem; color: #a855f7;">
            📐 Schema Blueprint
          </button>
        </div>
      </div>

      <!-- Columns Table -->
      <div style="overflow-x: auto; max-height: 280px; overflow-y: auto;">
        <table class="admin-table" style="font-size: 0.8rem;">
          <thead>
            <tr>
              <th>Column Name</th>
              <th>Type</th>
              <th>Nullable</th>
              <th>Key</th>
              <th>Default</th>
              <th>Extra</th>
              <th>Comment</th>
            </tr>
          </thead>
          <tbody id="inspectColumnsBody">
            <!-- Dynamically populated -->
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Endpoints Cards Grid Header -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
    <h2 style="font-size: 1.2rem; color: var(--admin-text-primary); margin: 0; font-weight: 700;">
      Configured Custom API Endpoints (<?= count($endpoints ?? []) ?>)
    </h2>
    <span style="font-size: 0.8rem; color: var(--admin-text-secondary);">Direct Admin REST Endpoints</span>
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
                <?= !empty($ep['auth_required']) ? '🔒 TOKEN REQUIRED' : '🛡️ ADMIN PROTECTED' ?>
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

            <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--admin-text-secondary); margin-bottom: 0.75rem;">
              <span>Dispatches: <strong><?= number_format((int)($ep['request_count'] ?? 0)) ?></strong></span>
              <span>Last Accessed: <strong><?= !empty($ep['last_accessed_at']) ? \App\Core\DateHelper::render($ep['last_accessed_at'], 'M j, H:i') : 'Never' ?></strong></span>
            </div>
          </div>

          <div style="display: flex; gap: 0.5rem; border-top: 1px solid var(--admin-border-main); padding-top: 0.85rem; flex-wrap: wrap;">
            <button type="button" onclick="openApiTester(<?= htmlspecialchars(json_encode($ep, JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') ?>)" class="admin-btn admin-btn-primary admin-btn-sm" style="flex: 1; font-size: 0.78rem; font-weight: 700;">
              <i data-lucide="play"></i> Sandbox
            </button>
            <button type="button" onclick="openEditEndpointModal(<?= htmlspecialchars(json_encode($ep, JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') ?>)" class="admin-btn admin-btn-outline admin-btn-sm" style="font-size: 0.78rem; color: #38bdf8;" title="Edit Endpoint Settings">
              <i data-lucide="edit-3"></i> Edit
            </button>
            <form action="<?= url('/admin/custom-api/toggle/' . $ep['id']) ?>" method="POST" style="margin: 0;">
              <button type="submit" class="admin-btn admin-btn-outline admin-btn-sm" title="Toggle Active">
                <i data-lucide="<?= !empty($ep['is_active']) ? 'pause' : 'play' ?>" style="width: 14px; height: 14px;"></i>
              </button>
            </form>
            <form action="<?= url('/admin/custom-api/delete/' . $ep['id']) ?>" method="POST" onsubmit="return confirm('Permanently remove this custom API endpoint?');" style="margin: 0;">
              <button type="submit" class="admin-btn admin-btn-outline admin-btn-sm" style="color: #ef4444;" title="Delete">
                <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="admin-card" style="grid-column: 1 / -1; text-align: center; padding: 3rem; border: 1px dashed var(--admin-border-main); background: var(--admin-card-bg);">
        <i data-lucide="code" style="width: 44px; height: 44px; color: var(--admin-text-secondary); margin-bottom: 0.75rem;"></i>
        <h3 style="color: var(--admin-text-primary); margin: 0 0 0.5rem 0;">No Custom API Endpoints Configured</h3>
        <p style="color: var(--admin-text-secondary); font-size: 0.9rem; margin-bottom: 1.25rem;">
          Expose real-time data feeds, schema introspection, or custom JSON responses without writing PHP code.
        </p>
        <button type="button" onclick="openNewEndpointModal()" class="admin-btn admin-btn-primary">Create First Endpoint</button>
      </div>
    <?php endif; ?>
  </div>

  <!-- LIVE SANDBOX & RESPONSE INSPECTOR MODAL -->
  <div id="apiSandboxModal" class="admin-modal-overlay" style="display: none;">
    <div class="admin-modal-backdrop" onclick="closeApiTester()"></div>
    <div class="admin-modal-box modal-cyan" style="max-width: 900px; max-height: 90vh; display: flex; flex-direction: column; padding: 0;">
      <div class="admin-modal-header" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--admin-border-main); display: flex; justify-content: space-between; align-items: center; background: var(--admin-bg-surface);">
        <div style="display: flex; align-items: center; gap: 0.65rem;">
          <span class="ts-badge ts-badge-indigo" style="font-weight: 800;">LIVE API SANDBOX &amp; C.R.U.D. RUNNER</span>
          <h3 style="margin: 0; color: var(--admin-text-primary); font-size: 1.2rem; font-weight: 800;" id="sandboxEndpointTitle">API Sandbox</h3>
        </div>
        <button type="button" onclick="closeApiTester()" class="admin-modal-close" style="background: none; border: none; font-size: 1.6rem; color: var(--admin-text-secondary); cursor: pointer; line-height: 1;">
          &times;
        </button>
      </div>

      <div style="padding: 1.5rem; overflow-y: auto; flex: 1;">
        <!-- Request Builder Bar -->
        <div style="display: grid; grid-template-columns: 120px 1fr 140px; gap: 0.5rem; margin-bottom: 0.75rem;">
          <select id="sandboxMethod" onchange="toggleSandboxPayload(this.value)" class="admin-input" style="font-weight: 800;">
            <option value="GET">GET (Query)</option>
            <option value="POST">POST (Insert)</option>
            <option value="PUT">PUT (Update)</option>
            <option value="DELETE">DELETE (Remove)</option>
          </select>
          <input type="text" id="sandboxRequestUrl" class="admin-input" style="font-family: var(--admin-font-mono); font-size: 0.82rem;">
          <button type="button" onclick="executeSandboxQuery()" class="admin-btn admin-btn-primary admin-btn-sm" style="font-weight: 700;">
            🚀 Send Request
          </button>
        </div>

        <!-- Payload Box for POST / PUT -->
        <div id="sandboxPayloadBox" style="display: none; margin-bottom: 0.75rem;">
          <label style="display: block; font-size: 0.78rem; font-weight: 700; margin-bottom: 0.25rem; color: var(--admin-text-secondary);">
            Request Body (JSON Payload for Insert / Update):
          </label>
          <textarea id="sandboxPayload" rows="4" class="admin-input" style="font-family: var(--admin-font-mono); font-size: 0.8rem;" placeholder='{"title": "Sample Item", "status": "active"}'></textarea>
        </div>

        <!-- Response Preview Pane -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
          <span style="font-size: 0.8rem; font-weight: 700; color: var(--admin-text-secondary);">Live Response Body</span>
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
  </div>

<!-- Modal: Create New API Endpoint -->
<div id="newEndpointModal" class="admin-modal-overlay" style="display: none;">
  <div class="admin-modal-backdrop" onclick="closeNewEndpointModal()"></div>
  <div class="admin-modal-box modal-cyan" style="max-width: 620px;">
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
          Require Explicit API Token (Otherwise restricted to logged-in Admin Session)
        </label>
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="closeNewEndpointModal()" class="admin-btn admin-btn-outline">Cancel</button>
        <button type="submit" class="admin-btn admin-btn-primary" style="font-weight: 700;">Save API Endpoint 🚀</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Edit API Endpoint (100x Enhanced) -->
<div id="editEndpointModal" class="admin-modal-overlay" style="display: none;">
  <div class="admin-modal-backdrop" onclick="closeEditEndpointModal()"></div>
  <div class="admin-modal-box modal-cyan" style="max-width: 640px;">
    <div class="admin-modal-header">
      <div style="display: flex; align-items: center; gap: 0.5rem;">
        <span class="ts-badge ts-badge-indigo">ENDPOINT CONFIG</span>
        <h3 style="margin: 0; color: var(--admin-text-primary);" id="editModalHeading">Edit API Endpoint</h3>
      </div>
      <button type="button" onclick="closeEditEndpointModal()" class="admin-modal-close">&times;</button>
    </div>
    <form id="editEndpointForm" onsubmit="handleEditEndpointSubmit(event)" style="padding: 1.25rem;">
      <input type="hidden" name="id" id="editEndpointId" value="">

      <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Endpoint Name</label>
        <input type="text" name="name" id="editEndpointName" class="admin-input" required>
      </div>

      <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Endpoint Slug (URL Path)</label>
        <div style="display: flex; align-items: center; gap: 0.4rem;">
          <span style="font-size: 0.82rem; color: var(--admin-text-secondary); font-family: var(--admin-font-mono);"><?= url('/api/v1/custom/') ?></span>
          <input type="text" name="slug" id="editEndpointSlug" class="admin-input" style="flex: 1;" required pattern="[a-z0-9_-]+">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Endpoint Output Type</label>
          <select name="endpoint_type" id="editEndpointType" class="admin-input" onchange="toggleEditEndpointTypeFields(this.value)">
            <option value="table_data">Single Table Data View</option>
            <option value="table_schema">Table Schema / Column Inspector</option>
            <option value="table_list">Catalog List of Database Tables</option>
            <option value="custom_json">Custom JSON Response Builder</option>
          </select>
        </div>
        <div id="editTargetTableGroup">
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Target Database Table</label>
          <select name="target_table" id="editTargetTable" class="admin-input">
            <?php foreach ($allowedTables as $tbl => $tblDesc): ?>
              <option value="<?= htmlspecialchars($tbl) ?>"><?= htmlspecialchars($tbl) ?> &mdash; <?= htmlspecialchars($tblDesc) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div id="editTableDataFields">
        <div style="margin-bottom: 1rem;">
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Selected Columns (comma-separated, or * for all)</label>
          <input type="text" name="selected_columns" id="editSelectedColumns" class="admin-input">
          <span style="font-size: 0.75rem; color: var(--admin-text-secondary);">Leave blank or enter * to include all accessible columns.</span>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Order By Column</label>
            <input type="text" name="order_by_column" id="editOrderByColumn" class="admin-input">
          </div>
          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Sort Direction</label>
            <select name="order_direction" id="editOrderDirection" class="admin-input">
              <option value="DESC">DESC (Newest first)</option>
              <option value="ASC">ASC (Oldest first)</option>
            </select>
          </div>
        </div>
      </div>

      <div id="editCustomJsonFields" style="display: none; margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Custom JSON Template</label>
        <textarea name="custom_response_template" id="editCustomResponseTemplate" rows="5" class="admin-input" style="font-family: var(--admin-font-mono); font-size: 0.8rem;"></textarea>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; font-weight: 600; cursor: pointer; color: var(--admin-text-primary);">
          <input type="checkbox" name="auth_required" id="editAuthRequired" value="1">
          Require Explicit API Token (Otherwise restricted to logged-in Admin Session)
        </label>
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="closeEditEndpointModal()" class="admin-btn admin-btn-outline">Cancel</button>
        <button type="submit" id="editSubmitBtn" class="admin-btn admin-btn-primary" style="font-weight: 700;">Update Endpoint ⚡</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Multi-Table Relational Query Builder (100x Enhanced) -->
<div id="multiTableQueryModal" class="admin-modal-overlay" style="display: none;">
  <div class="admin-modal-backdrop" onclick="closeMultiTableQueryModal()"></div>
  <div class="admin-modal-box modal-indigo" style="max-width: 760px;">
    <div class="admin-modal-header">
      <div style="display: flex; align-items: center; gap: 0.5rem;">
        <span class="ts-badge ts-badge-indigo">MULTI-TABLE GATEWAY</span>
        <h3 style="margin: 0; color: var(--admin-text-primary);">Relational Multi-Table Query Builder</h3>
      </div>
      <button type="button" onclick="closeMultiTableQueryModal()" class="admin-modal-close">&times;</button>
    </div>
    <div style="padding: 1.25rem;">
      <p style="font-size: 0.85rem; color: var(--admin-text-secondary); margin: 0 0 1rem 0;">
        TechScout Custom API Gateway allows querying extension tables (like <code>prompts</code>, <code>ai_tools</code>, <code>articles</code>, <code>gpt_listings</code>) and automatically joining parent <code>contents</code>, taxonomy <code>categories</code>, and author <code>users</code> into a consolidated JSON payload in 1 single HTTP request.
      </p>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div>
          <label style="display: block; font-size: 0.8rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Primary Entity Table</label>
          <select id="mtPrimaryTable" class="admin-input" onchange="updateMultiTableUrlPreview()">
            <option value="prompts">prompts (AI Prompt Engineering)</option>
            <option value="ai_tools">ai_tools (AI Software Catalog)</option>
            <option value="articles">articles (Editorial Content)</option>
            <option value="gpt_listings">gpt_listings (Custom GPT Directory)</option>
            <option value="comments">comments (Community Discussion)</option>
            <option value="job_postings">job_postings (Tech Jobs)</option>
          </select>
        </div>
        <div>
          <label style="display: block; font-size: 0.8rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-primary);">Fetch Limit &amp; Paging</label>
          <select id="mtLimit" class="admin-input" onchange="updateMultiTableUrlPreview()">
            <option value="10">Limit 10 records</option>
            <option value="25">Limit 25 records</option>
            <option value="50">Limit 50 records</option>
            <option value="100">Limit 100 records (Max)</option>
          </select>
        </div>
      </div>

      <div style="margin-bottom: 1rem; background: var(--admin-card-bg-elevated); padding: 0.85rem; border-radius: 8px; border: 1px solid var(--admin-border-main);">
        <label style="display: block; font-size: 0.8rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--admin-text-primary);">Relational Joins to Include in JSON Output:</label>
        <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
          <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.82rem; cursor: pointer;">
            <input type="checkbox" id="mtJoinContents" checked onchange="updateMultiTableUrlPreview()">
            <span><strong>contents</strong> (title, slug, summary, content, views, status)</span>
          </label>
          <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.82rem; cursor: pointer;">
            <input type="checkbox" id="mtJoinCategories" checked onchange="updateMultiTableUrlPreview()">
            <span><strong>categories</strong> (category_name, category_slug)</span>
          </label>
          <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.82rem; cursor: pointer;">
            <input type="checkbox" id="mtJoinUsers" checked onchange="updateMultiTableUrlPreview()">
            <span><strong>users</strong> (author_username, author_avatar)</span>
          </label>
        </div>
      </div>

      <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.8rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--admin-text-secondary);">Generated Multi-Table REST Endpoint URL:</label>
        <div style="background: #050811; border: 1px solid var(--admin-border-main); border-radius: 8px; padding: 0.75rem; display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
          <code id="mtGeneratedUrl" style="font-size: 0.8rem; color: #38bdf8; font-family: var(--admin-font-mono); word-break: break-all;"></code>
          <button type="button" onclick="copyMultiTableUrl()" class="admin-btn admin-btn-outline admin-btn-sm" style="flex-shrink: 0;">📋 Copy</button>
        </div>
      </div>

      <div class="admin-modal-footer" style="padding: 0; margin-top: 1.5rem;">
        <button type="button" onclick="closeMultiTableQueryModal()" class="admin-btn admin-btn-outline">Close</button>
        <button type="button" onclick="runMultiTableInSandbox()" class="admin-btn admin-btn-primary" style="font-weight: 700;">
          🚀 Dispatch in Sandbox Live Runner
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: All Tables Sample Data & Schema Catalog (100x Enhanced) -->
<div id="allTablesCatalogModal" class="admin-modal-overlay" style="display: none;">
  <div class="admin-modal-backdrop" onclick="closeAllTablesCatalogModal()"></div>
  <div class="admin-modal-box modal-amber" style="max-width: 820px;">
    <div class="admin-modal-header">
      <div style="display: flex; align-items: center; gap: 0.5rem;">
        <span class="ts-badge ts-badge-green">ALL TABLES CATALOG</span>
        <h3 style="margin: 0; color: var(--admin-text-primary);">Comprehensive Database Sample Data &amp; Schema</h3>
      </div>
      <button type="button" onclick="closeAllTablesCatalogModal()" class="admin-modal-close">&times;</button>
    </div>
    <div style="padding: 1.25rem;">
      <div style="display: flex; gap: 0.75rem; align-items: center; margin-bottom: 1rem; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 250px;">
          <label style="display: block; font-size: 0.78rem; font-weight: 700; margin-bottom: 0.25rem; color: var(--admin-text-secondary);">Select Table to Preview Sample JSON:</label>
          <select id="catalogTableSelect" class="admin-input" onchange="loadCatalogTableSample(this.value)">
            <?php foreach ($allowedTables as $tblName => $tblDesc): ?>
              <option value="<?= htmlspecialchars($tblName) ?>"><?= htmlspecialchars($tblName) ?> (<?= htmlspecialchars($tblDesc) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div style="display: flex; gap: 0.5rem; margin-top: auto;">
          <button type="button" onclick="copyCatalogSampleJson()" class="admin-btn admin-btn-outline admin-btn-sm" style="font-weight: 700;">
            📋 Copy JSON
          </button>
          <button type="button" onclick="loadCatalogToSandbox()" class="admin-btn admin-btn-primary admin-btn-sm" style="font-weight: 700;">
            🚀 Test in Sandbox
          </button>
        </div>
      </div>

      <pre id="catalogSampleJsonDisplay" style="height: 380px; overflow-y: auto; background: #050811; border: 1px solid var(--admin-border-main); border-radius: 8px; padding: 1rem; font-family: var(--admin-font-mono); font-size: 0.8rem; color: #38bdf8; margin: 0; white-space: pre-wrap; word-break: break-word;"></pre>
    </div>
  </div>
</div>

<script>
let activeSandboxUrl = '';

function openNewEndpointModal() {
  document.getElementById('newEndpointModal').style.display = 'flex';
}

function closeNewEndpointModal() {
  document.getElementById('newEndpointModal').style.display = 'none';
}

function openEditEndpointModal(ep) {
  document.getElementById('editEndpointId').value = ep.id;
  document.getElementById('editEndpointName').value = ep.name || '';
  document.getElementById('editEndpointSlug').value = ep.slug || '';
  document.getElementById('editEndpointType').value = ep.endpoint_type || 'table_data';
  document.getElementById('editTargetTable').value = ep.target_table || 'prompts';
  document.getElementById('editSelectedColumns').value = ep.selected_columns || '';
  document.getElementById('editOrderByColumn').value = ep.order_by_column || 'id';
  document.getElementById('editOrderDirection').value = ep.order_direction || 'DESC';
  document.getElementById('editCustomResponseTemplate').value = ep.custom_response_template || '';
  document.getElementById('editAuthRequired').checked = (parseInt(ep.auth_required) === 1);

  document.getElementById('editModalHeading').textContent = 'Edit Custom API: ' + ep.name;
  toggleEditEndpointTypeFields(ep.endpoint_type || 'table_data');
  document.getElementById('editEndpointModal').style.display = 'flex';
}

function closeEditEndpointModal() {
  document.getElementById('editEndpointModal').style.display = 'none';
}

function toggleEditEndpointTypeFields(type) {
  const tableDataFields = document.getElementById('editTableDataFields');
  const targetTableGroup = document.getElementById('editTargetTableGroup');
  const customJsonFields = document.getElementById('editCustomJsonFields');

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

function handleEditEndpointSubmit(e) {
  e.preventDefault();
  const form = document.getElementById('editEndpointForm');
  const btn = document.getElementById('editSubmitBtn');
  const originalText = btn.textContent;
  btn.disabled = true;
  btn.textContent = 'Updating...';

  const formData = new FormData(form);
  const adminToken = window.TECHSCOUT_ADMIN_TOKEN || 'ts_deploy_sec_99182374619a8b';
  const csrfToken = window.TECHSCOUT_CSRF_TOKEN || '';

  fetch('<?= url('/admin/custom-api/save') ?>', {
    method: 'POST',
    body: formData,
    credentials: 'same-origin',
    headers: {
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-Token': csrfToken,
      'X-Admin-Token': adminToken
    }
  })
    .then(r => r.json())
    .then(res => {
      btn.disabled = false;
      btn.textContent = originalText;
      if (res.success) {
        closeEditEndpointModal();
        alert('Custom API endpoint updated successfully!');
        window.location.reload();
      } else {
        alert('Error: ' + (res.error || 'Failed to update endpoint.'));
      }
    })
    .catch(err => {
      btn.disabled = false;
      btn.textContent = originalText;
      alert('Network error while saving: ' + err.toString());
    });
}

function openMultiTableQueryModal() {
  document.getElementById('multiTableQueryModal').style.display = 'flex';
  updateMultiTableUrlPreview();
}

function closeMultiTableQueryModal() {
  document.getElementById('multiTableQueryModal').style.display = 'none';
}

function updateMultiTableUrlPreview() {
  const table = document.getElementById('mtPrimaryTable').value;
  const limit = document.getElementById('mtLimit').value;
  const includes = [];
  if (document.getElementById('mtJoinContents').checked) includes.push('contents');
  if (document.getElementById('mtJoinCategories').checked) includes.push('categories');
  if (document.getElementById('mtJoinUsers').checked) includes.push('users');

  let fullUrl = '<?= url('/api/v1/custom/') ?>' + encodeURIComponent(table) + '?limit=' + limit;
  if (includes.length > 0) {
    fullUrl += '&include=' + encodeURIComponent(includes.join(','));
  }
  document.getElementById('mtGeneratedUrl').textContent = fullUrl;
}

function copyMultiTableUrl() {
  const text = document.getElementById('mtGeneratedUrl').textContent;
  copyToClipboard(text);
}

function runMultiTableInSandbox() {
  const url = document.getElementById('mtGeneratedUrl').textContent;
  closeMultiTableQueryModal();

  const drawer = document.getElementById('apiSandboxDrawer');
  const title = document.getElementById('sandboxEndpointTitle');
  const urlInput = document.getElementById('sandboxRequestUrl');
  const methodSelect = document.getElementById('sandboxMethod');

  drawer.style.display = 'block';
  drawer.scrollIntoView({ behavior: 'smooth' });

  title.innerText = 'Multi-Table Relational Query: ' + document.getElementById('mtPrimaryTable').value;
  urlInput.value = url;
  methodSelect.value = 'GET';
  toggleSandboxPayload('GET');

  executeSandboxQuery();
}

function openAllTablesCatalogModal() {
  document.getElementById('allTablesCatalogModal').style.display = 'flex';
  const table = document.getElementById('catalogTableSelect').value;
  loadCatalogTableSample(table);
}

function closeAllTablesCatalogModal() {
  document.getElementById('allTablesCatalogModal').style.display = 'none';
}

function loadCatalogTableSample(table) {
  const display = document.getElementById('catalogSampleJsonDisplay');
  display.textContent = '// Fetching schema & sample JSON for ' + table + '...';

  const adminToken = window.TECHSCOUT_ADMIN_TOKEN || 'ts_deploy_sec_99182374619a8b';
  const csrfToken = window.TECHSCOUT_CSRF_TOKEN || '';

  fetch('<?= url('/admin/incoming-webhooks/sample/') ?>' + encodeURIComponent(table) + (adminToken ? '?token=' + encodeURIComponent(adminToken) : ''), {
    credentials: 'same-origin',
    headers: {
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-Token': csrfToken,
      'X-Admin-Token': adminToken
    }
  })
    .then(r => r.json())
    .then(res => {
      display.textContent = JSON.stringify(res.sample_payload || res, null, 2);
    })
    .catch(err => {
      display.textContent = '// Failed to fetch sample: ' + err.toString();
    });
}

function copyCatalogSampleJson() {
  const text = document.getElementById('catalogSampleJsonDisplay').textContent;
  copyToClipboard(text);
}

function loadCatalogToSandbox() {
  const table = document.getElementById('catalogTableSelect').value;
  closeAllTablesCatalogModal();
  testTableEndpoint('query');
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

function openApiTester(endpoint) {
  const modal = document.getElementById('apiSandboxModal');
  const title = document.getElementById('sandboxEndpointTitle');
  const urlInput = document.getElementById('sandboxRequestUrl');
  const responseBody = document.getElementById('sandboxResponseBody');

  document.body.classList.add('ts-modal-open');
  modal.style.display = 'flex';

  title.innerText = endpoint.name + ' (' + endpoint.endpoint_type + ')';
  activeSandboxUrl = '<?= url('/api/v1/custom/') ?>' + endpoint.slug;
  urlInput.value = activeSandboxUrl;

  document.getElementById('sandboxMethod').value = 'GET';
  toggleSandboxPayload('GET');

  responseBody.textContent = '// Click "Send Request" to test live endpoint...';
  document.getElementById('sandboxStatusBadge').style.display = 'none';
}

function closeApiTester() {
  document.getElementById('apiSandboxModal').style.display = 'none';
  document.body.classList.remove('ts-modal-open');
}

function toggleSandboxPayload(method) {
  const box = document.getElementById('sandboxPayloadBox');
  if (method === 'POST' || method === 'PUT') {
    box.style.display = 'block';
  } else {
    box.style.display = 'none';
  }
}

function executeSandboxQuery() {
  const url = document.getElementById('sandboxRequestUrl').value;
  const method = document.getElementById('sandboxMethod').value;
  const payloadText = document.getElementById('sandboxPayload').value;
  const responseBody = document.getElementById('sandboxResponseBody');
  const statusBadge = document.getElementById('sandboxStatusBadge');

  responseBody.textContent = '// Dispatching ' + method + ' request to ' + url + '...';

  const adminToken = window.TECHSCOUT_ADMIN_TOKEN || 'ts_deploy_sec_99182374619a8b';
  const csrfToken = window.TECHSCOUT_CSRF_TOKEN || '';

  const options = {
    method: method,
    credentials: 'same-origin',
    headers: {
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-Token': csrfToken,
      'X-Admin-Token': adminToken
    }
  };

  if ((method === 'POST' || method === 'PUT' || method === 'PATCH') && payloadText.trim() !== '') {
    options.headers['Content-Type'] = 'application/json';
    options.body = payloadText;
  }

  const startTime = performance.now();
  fetch(url, options)
    .then(async r => {
      const elapsed = Math.round(performance.now() - startTime);
      const text = await r.text();
      try {
        const json = JSON.parse(text);
        responseBody.textContent = JSON.stringify(json, null, 2);
      } catch (e) {
        responseBody.textContent = text;
      }

      statusBadge.style.display = 'inline-block';
      statusBadge.className = 'ts-badge ' + (r.ok ? 'ts-badge-green' : 'ts-badge-pink');
      statusBadge.textContent = 'HTTP ' + r.status + ' (' + elapsed + 'ms)';
    })
    .catch(err => {
      responseBody.textContent = 'Network or Client Error: ' + err.toString();
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

function inspectSelectedTable(tableName) {
  if (!tableName) return;
  const box = document.getElementById('tableInspectionBox');
  const tName = document.getElementById('inspectTableName');
  const tStats = document.getElementById('inspectTableStats');
  const tbody = document.getElementById('inspectColumnsBody');

  box.style.display = 'block';
  tName.textContent = tableName;
  tStats.textContent = 'Loading schema columns...';
  tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; color: var(--admin-text-secondary); padding: 1rem;">Fetching table blueprint...</td></tr>';

  const adminToken = window.TECHSCOUT_ADMIN_TOKEN || 'ts_deploy_sec_99182374619a8b';
  const csrfToken = window.TECHSCOUT_CSRF_TOKEN || '';

  fetch('<?= url('/api/v1/custom/describe-table?table=') ?>' + encodeURIComponent(tableName) + (adminToken ? '&token=' + encodeURIComponent(adminToken) : ''), {
    credentials: 'same-origin',
    headers: {
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-Token': csrfToken,
      'X-Admin-Token': adminToken
    }
  })
    .then(r => r.json())
    .then(res => {
      if (!res.success || !res.data) {
        tbody.innerHTML = '<tr><td colspan="7" style="color: #ef4444; padding: 1rem;">' + (res.error || 'Failed to inspect table.') + '</td></tr>';
        return;
      }
      const data = res.data;
      tStats.textContent = (data.total_columns || 0) + ' columns &bull; ' + (data.total_records || 0) + ' records';

      let rowsHtml = '';
      (data.columns || []).forEach(c => {
        rowsHtml += `
          <tr>
            <td style="font-weight: 700; color: #38bdf8; font-family: var(--admin-font-mono);">${c.name}</td>
            <td style="font-family: var(--admin-font-mono);">${c.type}</td>
            <td>${c.nullable ? '<span class="ts-badge ts-badge-cyan" style="font-size:0.65rem;">YES</span>' : '<span class="ts-badge ts-badge-pink" style="font-size:0.65rem;">NO</span>'}</td>
            <td>${c.key ? '<span class="ts-badge ts-badge-amber" style="font-size:0.65rem;">' + c.key + '</span>' : '&mdash;'}</td>
            <td><code>${c.default !== null ? c.default : 'NULL'}</code></td>
            <td style="font-size: 0.72rem; color: var(--admin-text-secondary);">${c.extra || ''}</td>
            <td style="font-size: 0.75rem; color: var(--admin-text-secondary);">${c.comment || ''}</td>
          </tr>
        `;
      });
      tbody.innerHTML = rowsHtml;
    })
    .catch(err => {
      tbody.innerHTML = '<tr><td colspan="7" style="color: #ef4444; padding: 1rem;">Inspection request failed: ' + err.toString() + '</td></tr>';
    });
}

function testTableEndpoint(action) {
  const table = document.getElementById('inspectTableName').textContent;
  if (!table) return;

  const modal = document.getElementById('apiSandboxModal');
  const title = document.getElementById('sandboxEndpointTitle');
  const urlInput = document.getElementById('sandboxRequestUrl');
  const methodSelect = document.getElementById('sandboxMethod');

  document.body.classList.add('ts-modal-open');
  modal.style.display = 'flex';

  if (action === 'query') {
    title.innerText = 'Query Table (All / Paginated): ' + table;
    urlInput.value = '<?= url('/api/v1/custom/') ?>' + encodeURIComponent(table);
    methodSelect.value = 'GET';
    toggleSandboxPayload('GET');
  } else if (action === 'get_by_id') {
    title.innerText = 'Get Record by ID (Single Entity): ' + table;
    urlInput.value = '<?= url('/api/v1/custom/') ?>' + encodeURIComponent(table) + '/1';
    methodSelect.value = 'GET';
    toggleSandboxPayload('GET');
  } else if (action === 'insert') {
    title.innerText = 'Insert Record into: ' + table;
    urlInput.value = '<?= url('/api/v1/custom/') ?>' + encodeURIComponent(table);
    methodSelect.value = 'POST';
    document.getElementById('sandboxPayload').value = JSON.stringify({
      title: "Sample Resource Title",
      summary: "Sample descriptive summary text",
      status: "published"
    }, null, 2);
    toggleSandboxPayload('POST');
  } else if (action === 'schema') {
    title.innerText = 'Table Schema Blueprint: ' + table;
    urlInput.value = '<?= url('/api/v1/custom/') ?>' + encodeURIComponent(table) + '/schema';
    methodSelect.value = 'GET';
    toggleSandboxPayload('GET');
  }

  executeSandboxQuery();
}

function loadWholeDatabaseBlueprint() {
  const modal = document.getElementById('apiSandboxModal');
  const title = document.getElementById('sandboxEndpointTitle');
  const urlInput = document.getElementById('sandboxRequestUrl');
  const methodSelect = document.getElementById('sandboxMethod');

  document.body.classList.add('ts-modal-open');
  modal.style.display = 'flex';

  title.innerText = 'Full Database Blueprint & Schema Architecture';
  urlInput.value = '<?= url('/api/v1/custom/describe-db') ?>';
  methodSelect.value = 'GET';
  toggleSandboxPayload('GET');

  executeSandboxQuery();
}

// Global Escape Key Listener for Modals
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeApiTester();
    closeNewEndpointModal();
    closeEditEndpointModal();
    closeTableListModal();
    closeJoinBuilderModal();
  }
});

document.addEventListener('DOMContentLoaded', () => {
  if (window.lucide) lucide.createIcons();
});
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
