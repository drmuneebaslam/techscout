<?php
$title = 'System Error Logs & Crash Forensics | TechScout Admin';
require __DIR__ . '/../layouts/admin_header.php';
?>

<div class="admin-content-inner">
  <!-- Page Header -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-pink" style="font-size: 0.68rem; font-weight: 800;">CRASH TELEMETRY HUD</span>
        <span style="color: #64748b; font-size: 0.8rem;">Real-time Exception Interception</span>
      </div>
      <h1 style="font-size: 1.65rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif;">System Error Logs &amp; Forensics</h1>
    </div>

    <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
      <button type="button" onclick="exportErrorsCsv()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3);">
        📥 Export Telemetry CSV
      </button>
      <form action="<?= url('/admin/system-logs/clear') ?>" method="POST" onsubmit="return confirm('Purge and clear all system error logs?');" style="display: inline;">
        <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.3);">
          🗑 Clear All Logs
        </button>
      </form>
    </div>
  </div>

  <!-- Telemetry HUD Stats Grid -->
  <div class="admin-stats-grid" style="margin-bottom: 2rem;">
    <div class="admin-stat-card" style="border-top: 3px solid #6366f1;">
      <div class="admin-stat-label">Total Error Events</div>
      <div class="admin-stat-value" style="color: #f8fafc; font-family: var(--admin-font-mono);"><?= number_format($stats['total']) ?></div>
      <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 0.25rem;">Recorded runtime exceptions</div>
    </div>

    <div class="admin-stat-card" style="border-top: 3px solid #ef4444;">
      <div class="admin-stat-label">Unresolved Errors</div>
      <div class="admin-stat-value" style="color: #ef4444; font-family: var(--admin-font-mono);"><?= number_format($stats['unresolved']) ?></div>
      <div style="font-size: 0.72rem; color: #f87171; margin-top: 0.25rem;">Requiring engineer review</div>
    </div>

    <div class="admin-stat-card" style="border-top: 3px solid #ec4899;">
      <div class="admin-stat-label">Fatal / Crash Level</div>
      <div class="admin-stat-value" style="color: #ec4899; font-family: var(--admin-font-mono);"><?= number_format($stats['fatal']) ?></div>
      <div style="font-size: 0.72rem; color: #cbd5e1; margin-top: 0.25rem;">Critical script terminations</div>
    </div>

    <div class="admin-stat-card" style="border-top: 3px solid #f59e0b;">
      <div class="admin-stat-label">Database Exceptions</div>
      <div class="admin-stat-value" style="color: #f59e0b; font-family: var(--admin-font-mono);"><?= number_format($stats['db_errors']) ?></div>
      <div style="font-size: 0.72rem; color: #cbd5e1; margin-top: 0.25rem;">SQLState query exceptions</div>
    </div>
  </div>

  <!-- Filter & Search Bar -->
  <div class="admin-table-wrap" style="padding: 1.25rem; margin-bottom: 1.5rem;">
    <form action="<?= url('/admin/system-logs') ?>" method="GET" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
      <div style="flex: 1; min-width: 240px;">
        <input type="text" name="q" value="<?= htmlspecialchars($activeSearch) ?>" placeholder="🔍 Search error message, file, or URI..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.5rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.85rem;">
      </div>

      <div style="min-width: 180px;">
        <select name="type" onchange="this.form.submit()" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.5rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.85rem;">
          <option value="">All Error Types</option>
          <?php foreach ($distinctTypes as $dt): ?>
            <option value="<?= htmlspecialchars($dt['error_type']) ?>" <?= $activeType === $dt['error_type'] ? 'selected' : '' ?>><?= htmlspecialchars($dt['error_type']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div style="min-width: 150px;">
        <select name="status" onchange="this.form.submit()" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.5rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.85rem;">
          <option value="all" <?= $activeStatus === 'all' ? 'selected' : '' ?>>All Statuses</option>
          <option value="unresolved" <?= $activeStatus === 'unresolved' ? 'selected' : '' ?>>Unresolved Only</option>
          <option value="resolved" <?= $activeStatus === 'resolved' ? 'selected' : '' ?>>Resolved</option>
        </select>
      </div>

      <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="padding: 0.5rem 1rem;">Filter</button>
      <?php if ($activeSearch !== '' || $activeType !== '' || $activeStatus !== 'all'): ?>
        <a href="<?= url('/admin/system-logs') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.3);">Reset</a>
      <?php endif; ?>
    </form>
  </div>

  <!-- Detailed Error Table -->
  <div class="admin-table-wrap">
    <div style="overflow-x: auto;">
      <table class="admin-table" id="errorLogsTable">
        <thead>
          <tr>
            <th style="width: 130px;">Error Type</th>
            <th>Error Message &amp; Source File</th>
            <th>Request URI &amp; Context</th>
            <th>Client &amp; IP</th>
            <th>Logged At</th>
            <th style="width: 160px; text-align: right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($logs)): ?>
            <?php foreach ($logs as $log): ?>
              <?php 
                $isFatal = in_array($log['error_type'], ['FATAL_ERROR', 'E_ERROR', 'E_COMPILE_ERROR'], true);
                $isDb = $log['error_type'] === 'DATABASE_ERROR' || str_contains($log['error_type'], 'PDO');
                $isResolved = (int)$log['resolved'] === 1;
              ?>
              <tr style="<?= $isResolved ? 'opacity: 0.6;' : '' ?>">
                <td>
                  <span class="ts-badge <?= $isFatal ? 'ts-badge-pink' : ($isDb ? 'ts-badge-cyan' : 'ts-badge-indigo') ?>" style="font-size: 0.7rem; font-weight: 700;">
                    <?= htmlspecialchars($log['error_type']) ?>
                  </span>
                  <?php if ($isResolved): ?>
                    <div style="margin-top: 0.3rem;"><span class="ts-badge ts-badge-green" style="font-size: 0.6rem;">RESOLVED</span></div>
                  <?php endif; ?>
                </td>
                <td>
                  <div style="font-weight: 700; color: #f8fafc; font-size: 0.88rem; line-height: 1.4; max-width: 480px; word-break: break-word;">
                    <?= htmlspecialchars($log['message']) ?>
                  </div>
                  <?php if (!empty($log['file'])): ?>
                    <div style="font-size: 0.75rem; color: #38bdf8; font-family: var(--admin-font-mono); margin-top: 0.3rem;">
                      📄 <?= htmlspecialchars(basename($log['file'])) ?><?= $log['line'] ? ':' . (int)$log['line'] : '' ?>
                    </div>
                  <?php endif; ?>
                </td>
                <td>
                  <div style="font-family: var(--admin-font-mono); font-size: 0.78rem; color: #cbd5e1;">
                    <span style="color: #10b981; font-weight: 700;"><?= htmlspecialchars($log['request_method'] ?? 'GET') ?></span>
                    <?= htmlspecialchars($log['request_uri'] ?? '/') ?>
                  </div>
                </td>
                <td style="font-size: 0.78rem;">
                  <div style="font-family: var(--admin-font-mono); color: #94a3b8;"><?= htmlspecialchars($log['ip_address'] ?? '127.0.0.1') ?></div>
                  <?php if (!empty($log['user_id'])): ?>
                    <div style="color: #38bdf8; margin-top: 0.2rem;">User #<?= (int)$log['user_id'] ?></div>
                  <?php endif; ?>
                </td>
                <td style="font-size: 0.78rem; color: #64748b; font-family: var(--admin-font-mono); white-space: nowrap;">
                  <?= date('M j, Y H:i:s', strtotime($log['created_at'])) ?>
                </td>
                <td style="text-align: right;">
                  <div style="display: inline-flex; gap: 0.4rem; justify-content: flex-end;">
                    <button type="button" onclick='inspectErrorTrace(<?= json_encode($log['stack_trace'] ?? '', JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>, <?= json_encode($log['message'] ?? '', JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>, <?= json_encode($log['file'] ?? '', JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>, <?= (int)($log['line'] ?? 0) ?>)' class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem; color: #38bdf8;">
                      🔍 Trace
                    </button>
                    <?php if (!$isResolved): ?>
                      <form action="<?= url('/admin/system-logs/resolve/' . $log['id']) ?>" method="POST" style="display: inline;">
                        <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem; color: #10b981; border-color: rgba(16,185,129,0.3);" title="Mark as Resolved">
                          ✓ Fix
                        </button>
                      </form>
                    <?php endif; ?>
                    <form action="<?= url('/admin/system-logs/delete/' . $log['id']) ?>" method="POST" onsubmit="return confirm('Permanently delete this error log entry?');" style="display: inline; margin: 0;">
                      <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.45rem; color: #ef4444; border-color: rgba(239,68,68,0.3);" title="Delete Record">
                        🗑️
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" style="text-align: center; padding: 3rem; color: #64748b;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">🎉</div>
                <div style="font-size: 1rem; color: #10b981; font-weight: 700;">Zero Runtime Errors Encountered!</div>
                <div style="font-size: 0.85rem; color: #94a3b8; margin-top: 0.25rem;">All application systems and query handlers are executing without exceptions.</div>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: STACK TRACE & FORENSICS INSPECTOR -->
<!-- ========================================================================= -->
<div id="errorTraceModal" class="admin-modal-overlay" style="display: none;">
  <div class="admin-modal-box modal-crimson" style="max-width: 800px; max-height: 85vh; display: flex; flex-direction: column;">
    <div class="admin-modal-header" style="flex: 0 0 auto;">
      <div>
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.2rem;">
          <span class="ts-badge ts-badge-pink" style="font-size: 0.68rem;">EXCEPTION FORENSICS</span>
          <span id="modalErrorFile" style="font-size: 0.78rem; color: #38bdf8; font-family: var(--admin-font-mono);"></span>
        </div>
        <h3 class="admin-modal-title" id="modalErrorMessage" style="color: #f87171; font-size: 1.05rem; word-break: break-word;"></h3>
      </div>
      <button type="button" onclick="document.getElementById('errorTraceModal').style.display='none'" class="admin-modal-close">✕</button>
    </div>

    <div style="padding: 1.5rem; overflow-y: auto; flex: 1 1 auto;">
      <label class="admin-modal-label" style="display: flex; justify-content: space-between;">
        <span>Call Stack Execution Path</span>
        <span style="font-size: 0.7rem; color: #64748b;">Top-to-Bottom Execution Flow</span>
      </label>
      <pre id="modalErrorTrace" style="background: #05070b; border: 1px solid rgba(239,68,68,0.2); border-radius: 8px; padding: 1.25rem; color: #cbd5e1; font-family: var(--admin-font-mono); font-size: 0.8rem; line-height: 1.5; white-space: pre-wrap; word-break: break-all; max-height: 380px; overflow-y: auto;"></pre>
    </div>

    <div class="admin-modal-footer" style="flex: 0 0 auto; display: flex; justify-content: flex-end;">
      <button type="button" onclick="document.getElementById('errorTraceModal').style.display='none'" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #334155; border-color: #334155; font-weight: 700;">
        Close Inspector
      </button>
    </div>
  </div>
</div>

<script>
function inspectErrorTrace(trace, message, file, line) {
  document.getElementById('modalErrorMessage').innerText = message || 'Exception Trace';
  document.getElementById('modalErrorFile').innerText = file ? `${file}:${line}` : 'Source Unknown';
  document.getElementById('modalErrorTrace').innerText = trace || 'No stack trace captured for this error event.';
  document.getElementById('errorTraceModal').style.display = 'flex';
}

function exportErrorsCsv() {
  const table = document.getElementById('errorLogsTable');
  if (!table) return;
  
  let csv = [];
  const rows = table.querySelectorAll('tr');
  for (let i = 0; i < rows.length; i++) {
    const row = [];
    const cols = rows[i].querySelectorAll('td, th');
    for (let j = 0; j < cols.length - 1; j++) {
      let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, ' ').replace(/"/g, '""').trim();
      row.push('"' + data + '"');
    }
    if (row.length > 0) csv.push(row.join(','));
  }
  
  const csvFile = new Blob([csv.join('\n')], { type: 'text/csv' });
  const link = document.createElement('a');
  link.download = `techscout_system_errors_${Date.now()}.csv`;
  link.href = window.URL.createObjectURL(csvFile);
  link.style.display = 'none';
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
