<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-main">
  <!-- Topbar -->
  <div class="admin-topbar">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-cyan">FORENSIC TELEMETRY</span>
        <span style="font-size: 0.8rem; color: #64748b;">Immutable Security Trail, Administrative Actions & User Telemetry</span>
      </div>
      <h1 style="font-size: 1.6rem; color: #f8fafc; margin: 0;">Live System Audit Logs</h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <button type="button" onclick="location.reload()" class="ts-btn ts-btn-outline ts-btn-sm" title="Refresh Log Stream">
        <i data-lucide="rotate-cw"></i> Refresh Stream 🔄
      </button>
      <button type="button" onclick="exportAuditCsv()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #34d399; border-color: rgba(16,185,129,0.3);">
        <i data-lucide="download"></i> Export CSV 📊
      </button>
      <a href="<?= url('/admin/system') ?>" class="ts-btn ts-btn-outline ts-btn-sm">
        <i data-lucide="settings"></i> System Settings
      </a>
      <a href="<?= url('/admin/system/backup') ?>" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #6366f1, #38bdf8);">
        <i data-lucide="download"></i> Export SQL Backup
      </a>
    </div>
  </div>

  <!-- Telemetry Stat Grid -->
  <div class="admin-stat-grid">
    <div class="admin-stat-card">
      <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div class="admin-stat-label">Total Audit Events</div>
        <div style="padding: 0.35rem; background: rgba(56,189,248,0.1); border-radius: 8px; color: #38bdf8;">
          <i data-lucide="shield" style="width: 18px; height: 18px;"></i>
        </div>
      </div>
      <div class="admin-stat-val" style="color: #38bdf8;"><?= number_format($stats['total']) ?></div>
      <div style="font-size: 0.8rem; color: #94a3b8;">Platform lifetime immutable log entries</div>
    </div>

    <div class="admin-stat-card">
      <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div class="admin-stat-label">Tracked Actors</div>
        <div style="padding: 0.35rem; background: rgba(99,102,241,0.1); border-radius: 8px; color: #818cf8;">
          <i data-lucide="users" style="width: 18px; height: 18px;"></i>
        </div>
      </div>
      <div class="admin-stat-val" style="color: #818cf8;"><?= number_format($stats['unique_users']) ?></div>
      <div style="font-size: 0.8rem; color: #34d399;">Unique active administrators &amp; users</div>
    </div>

    <div class="admin-stat-card">
      <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div class="admin-stat-label">Distinct Action Types</div>
        <div style="padding: 0.35rem; background: rgba(236,72,153,0.1); border-radius: 8px; color: #ec4899;">
          <i data-lucide="activity" style="width: 18px; height: 18px;"></i>
        </div>
      </div>
      <div class="admin-stat-val" style="color: #ec4899;"><?= number_format($stats['unique_actions']) ?></div>
      <div style="font-size: 0.8rem; color: #94a3b8;">Security, approval, and content vectors</div>
    </div>

    <div class="admin-stat-card">
      <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div class="admin-stat-label">Events Today</div>
        <div style="padding: 0.35rem; background: rgba(16,185,129,0.1); border-radius: 8px; color: #10b981;">
          <i data-lucide="clock" style="width: 18px; height: 18px;"></i>
        </div>
      </div>
      <div class="admin-stat-val" style="color: #10b981;"><?= number_format($stats['today']) ?></div>
      <div style="font-size: 0.8rem; color: #34d399;">Live events recorded today (PKT)</div>
    </div>
  </div>

  <!-- Multi-Facet Filters Bar (User, Action, Entity & Live Search) -->
  <div style="background: rgba(15,23,42,0.6); border: 1px solid rgba(99,102,241,0.2); border-radius: 10px; padding: 1.25rem; margin-bottom: 1.75rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
      
      <!-- Live Client Search -->
      <div style="flex: 1; min-width: 240px;">
        <input type="text" id="auditSearchInput" onkeyup="filterAuditTable()" placeholder="🔍 Fast search in loaded telemetry (Action, User, IP, Entity)..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.45rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.85rem;">
      </div>

      <!-- User Selector -->
      <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
        <span style="font-size: 0.82rem; font-weight: 600; color: #94a3b8; display: flex; align-items: center; gap: 0.3rem;">
          <i data-lucide="user" style="width: 14px; height: 14px; color: #38bdf8;"></i> Filter User:
        </span>
        <select onchange="applyAuditFilters('user_id', this.value)" style="background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.45rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.85rem;">
          <option value="">-- All Users &amp; Actors --</option>
          <?php foreach ($allUsers as $u): ?>
            <option value="<?= $u['id'] ?>" <?= ($selectedUserId === (int)$u['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($u['display_name']) ?> (@<?= htmlspecialchars($u['username']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Action Type Selector -->
      <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
        <span style="font-size: 0.82rem; font-weight: 600; color: #94a3b8; display: flex; align-items: center; gap: 0.3rem;">
          <i data-lucide="zap" style="width: 14px; height: 14px; color: #f59e0b;"></i> Action:
        </span>
        <select onchange="applyAuditFilters('action', this.value)" style="background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.45rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.85rem;">
          <option value="all">-- All Action Types --</option>
          <?php foreach ($distinctActions as $act): ?>
            <option value="<?= htmlspecialchars($act) ?>" <?= ($selectedAction === $act) ? 'selected' : '' ?>>
              <?= htmlspecialchars($act) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Reset Filters -->
      <?php if (!empty($selectedUserId) || ($selectedAction !== 'all' && !empty($selectedAction))): ?>
        <div>
          <a href="<?= url('/admin/audit-logs') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.4);">
            <i data-lucide="x"></i> Clear
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Detailed Audit Records Table -->
  <div class="admin-table-wrap">
    <table class="admin-table" id="auditLogsTable" data-paginate="true" data-search-id="auditSearchInput" data-page-size="15">
      <thead>
        <tr>
          <th>Timestamp (PKT)</th>
          <th>User / Operator</th>
          <th>Action & Event</th>
          <th>Target Entity</th>
          <th>IP & Origin Country</th>
          <th>Device & Browser</th>
          <th>Payload</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($logs)): ?>
          <?php foreach ($logs as $log): ?>
            <tr>
              <!-- Time -->
              <td style="font-family: var(--admin-font-mono); font-size: 0.78rem; color: #94a3b8; white-space: nowrap;">
                <div style="font-weight: 600; color: #f8fafc;"><?= date('H:i:s', strtotime($log['created_at'])) ?></div>
                <div style="font-size: 0.72rem; color: #64748b;"><?= date('M j, Y', strtotime($log['created_at'])) ?></div>
              </td>

              <!-- User -->
              <td>
                <?php if (!empty($log['username'])): ?>
                  <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div style="width: 28px; height: 28px; border-radius: 50%; background: #1e293b; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #38bdf8; font-size: 0.75rem;">
                      <?= strtoupper(substr($log['display_name'] ?? $log['username'], 0, 1)) ?>
                    </div>
                    <div>
                      <a href="<?= url('/admin/audit-logs?user_id=' . $log['user_id']) ?>" style="font-weight: 700; color: #f8fafc; text-decoration: none; font-size: 0.88rem;" title="View all actions by this user">
                        <?= htmlspecialchars($log['display_name'] ?? $log['username']) ?>
                      </a>
                      <div style="font-size: 0.72rem; color: #64748b;">
                        ID #<?= (int)$log['user_id'] ?> &bull; <span class="ts-badge" style="font-size: 0.6rem; padding: 0.05rem 0.3rem;"><?= strtoupper($log['user_role'] ?? 'USER') ?></span>
                      </div>
                    </div>
                  </div>
                <?php else: ?>
                  <span style="color: #64748b; font-size: 0.82rem;">Anonymous / System Worker</span>
                <?php endif; ?>
              </td>

              <!-- Action -->
              <td>
                <?php 
                  $badgeClass = 'ts-badge-cyan';
                  if (str_contains($log['action'], 'delete') || str_contains($log['action'], 'ban') || str_contains($log['action'], 'strike')) {
                    $badgeClass = 'strike-badge';
                  } elseif (str_contains($log['action'], 'approve') || str_contains($log['action'], 'create')) {
                    $badgeClass = 'ts-badge-green';
                  } elseif (str_contains($log['action'], 'update') || str_contains($log['action'], 'edit')) {
                    $badgeClass = 'ts-badge-pink';
                  }
                ?>
                <span class="ts-badge <?= $badgeClass ?>" style="font-family: var(--admin-font-mono); font-size: 0.72rem;">
                  <?= htmlspecialchars($log['action']) ?>
                </span>
              </td>

              <!-- Target Entity -->
              <td>
                <?php if (!empty($log['entity_type'])): ?>
                  <span style="font-size: 0.8rem; color: #cbd5e1; font-weight: 600;">
                    <?= htmlspecialchars(str_replace('_', ' ', $log['entity_type'])) ?>
                  </span>
                  <?php if (!empty($log['entity_id'])): ?>
                    <span style="color: #38bdf8; font-family: var(--admin-font-mono); font-size: 0.78rem;">#<?= (int)$log['entity_id'] ?></span>
                  <?php endif; ?>
                <?php else: ?>
                  <span style="color: #64748b; font-size: 0.8rem;">-</span>
                <?php endif; ?>
              </td>

              <!-- Location & IP -->
              <td>
                <div style="font-family: var(--admin-font-mono); font-size: 0.8rem; color: #38bdf8;">
                  <?= htmlspecialchars($log['ip_address']) ?>
                </div>
                <div style="font-size: 0.75rem; color: #10b981; font-weight: 600; margin-top: 0.15rem;">
                  <?= htmlspecialchars($log['country'] ?? 'Pakistan 🇵🇰') ?>
                </div>
              </td>

              <!-- Device & Browser -->
              <td style="max-width: 220px;">
                <div style="font-size: 0.8rem; color: #e2e8f0; line-height: 1.35;">
                  <?= htmlspecialchars($log['device_info'] ?? 'Desktop Client') ?>
                </div>
              </td>

              <!-- Details & Diff Modal Button -->
              <td>
                <div style="display: flex; gap: 0.35rem; align-items: center;">
                  <?php if (!empty($log['before_state']) || !empty($log['after_state'])): ?>
                    <button type="button" onclick="inspectAuditDiff(<?= htmlspecialchars(json_encode($log['before_state'] ?? null), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode($log['after_state'] ?? null), ENT_QUOTES, 'UTF-8') ?>, '<?= htmlspecialchars(addslashes($log['action'])) ?>', '<?= htmlspecialchars(addslashes($log['username'] ?? 'Staff')) ?>')" class="ts-btn ts-btn-sm" style="padding: 0.2rem 0.55rem; font-size: 0.72rem; background: rgba(99,102,241,0.2); border: 1px solid rgba(99,102,241,0.5); color: #818cf8;">
                      Diff ⚖️
                    </button>
                  <?php endif; ?>
                  <?php if (!empty($log['details'])): ?>
                    <button type="button" onclick="inspectAuditPayload(<?= htmlspecialchars(json_encode($log['details']), ENT_QUOTES, 'UTF-8') ?>, '<?= htmlspecialchars(addslashes($log['action'])) ?>', '<?= htmlspecialchars(addslashes($log['username'] ?? 'User')) ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.55rem; font-size: 0.72rem;">
                      Payload 🔍
                    </button>
                  <?php elseif (empty($log['before_state']) && empty($log['after_state'])): ?>
                    <span style="color: #64748b; font-size: 0.72rem;">Logged</span>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" style="text-align: center; padding: 3rem; color: #64748b;">
              No audit records found matching the active filters.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Audit Log Payload & State Diff Inspector Modal -->
<div id="auditPayloadModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.88); backdrop-filter: blur(10px); z-index: 999999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="ts-card" style="width: 100%; max-width: 780px; padding: 2rem; background: #090d16; border: 1px solid rgba(99,102,241,0.4); box-shadow: 0 25px 60px rgba(0,0,0,0.95); max-height: 90vh; overflow-y: auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid rgba(148,163,184,0.15); padding-bottom: 0.75rem;">
      <div>
        <h3 id="modalAuditTitle" style="font-size: 1.15rem; color: #f8fafc; margin: 0;">Audit Event Payload</h3>
        <span id="modalAuditSubtitle" style="font-size: 0.78rem; color: #38bdf8;">Action Details</span>
      </div>
      <button onclick="document.getElementById('auditPayloadModal').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer;">&times;</button>
    </div>

    <!-- Diff Container -->
    <div id="modalDiffContainer" style="display: none; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
      <div>
        <div style="font-size: 0.8rem; font-weight: 700; color: #f87171; margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.35rem;">
          <span>🔴</span> Before State (Original)
        </div>
        <pre id="modalBeforeJson" style="background: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.25); border-radius: 8px; padding: 1rem; color: #fca5a5; font-family: var(--admin-font-mono); font-size: 0.8rem; max-height: 300px; overflow: auto; white-space: pre-wrap; word-break: break-all;"></pre>
      </div>
      <div>
        <div style="font-size: 0.8rem; font-weight: 700; color: #34d399; margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.35rem;">
          <span>🟢</span> After State (Modified)
        </div>
        <pre id="modalAfterJson" style="background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.25); border-radius: 8px; padding: 1rem; color: #6ee7b7; font-family: var(--admin-font-mono); font-size: 0.8rem; max-height: 300px; overflow: auto; white-space: pre-wrap; word-break: break-all;"></pre>
      </div>
    </div>

    <!-- Raw Payload Container -->
    <div id="modalPayloadContainer">
      <pre id="modalAuditJson" style="background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 1.25rem; color: #38bdf8; font-family: var(--admin-font-mono); font-size: 0.85rem; max-height: 350px; overflow: auto; white-space: pre-wrap; word-break: break-all;"></pre>
    </div>

    <div style="display: flex; justify-content: flex-end; margin-top: 1.25rem;">
      <button type="button" onclick="document.getElementById('auditPayloadModal').style.display='none'" class="ts-btn ts-btn-primary ts-btn-sm">Close Inspector</button>
    </div>
  </div>
</div>

<script>
function inspectAuditDiff(beforeState, afterState, action, username) {
  document.getElementById('modalAuditTitle').innerText = action + ' (State Diff)';
  document.getElementById('modalAuditSubtitle').innerText = `Executed by: ${username}`;
  
  document.getElementById('modalDiffContainer').style.display = 'grid';
  document.getElementById('modalPayloadContainer').style.display = 'none';

  document.getElementById('modalBeforeJson').innerText = beforeState ? JSON.stringify(typeof beforeState === 'string' ? JSON.parse(beforeState) : beforeState, null, 2) : '(None / Created)';
  document.getElementById('modalAfterJson').innerText = afterState ? JSON.stringify(typeof afterState === 'string' ? JSON.parse(afterState) : afterState, null, 2) : '(None / Deleted)';

  document.getElementById('auditPayloadModal').style.display = 'flex';
}

function inspectAuditPayload(jsonStr, action, username) {
  document.getElementById('modalAuditTitle').innerText = action;
  document.getElementById('modalAuditSubtitle').innerText = `Executed by: ${username}`;
  
  document.getElementById('modalDiffContainer').style.display = 'none';
  document.getElementById('modalPayloadContainer').style.display = 'block';

  let formatted = jsonStr;
  try {
    const obj = typeof jsonStr === 'string' ? JSON.parse(jsonStr) : jsonStr;
    formatted = JSON.stringify(obj, null, 2);
  } catch (e) {}

  document.getElementById('modalAuditJson').innerText = formatted;
  document.getElementById('auditPayloadModal').style.display = 'flex';
}

function filterAuditTable() {
  if (window.auditLogsPager) {
    window.auditLogsPager.refresh();
  }
}

document.addEventListener('DOMContentLoaded', function() {
  window.auditLogsPager = window.paginateAdminTable('auditLogsTable', {
    pageSize: 15,
    searchInputId: 'auditSearchInput'
  });
});

function exportAuditCsv() {
  const rows = document.querySelectorAll('.admin-table tr');
  let csvContent = "data:text/csv;charset=utf-8,";
  rows.forEach(row => {
    const cols = row.querySelectorAll('th, td');
    const rowData = [];
    cols.forEach(col => {
      let data = col.innerText.replace(/(\r\n|\n|\r)/gm, " ").replace(/"/g, '""');
      rowData.push(`"${data}"`);
    });
    csvContent += rowData.join(",") + "\r\n";
  });

  const encodedUri = encodeURI(csvContent);
  const link = document.createElement("a");
  link.setAttribute("href", encodedUri);
  link.setAttribute("download", `techscout_audit_telemetry_${Date.now()}.csv`);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
