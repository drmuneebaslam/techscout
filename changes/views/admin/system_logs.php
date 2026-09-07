<?php
$title = 'System Error Logs & Crash Forensics | TechScout Admin';
require __DIR__ . '/../layouts/admin_header.php';
?>

<div class="admin-content-inner">
  <!-- Page Header -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-pink" style="font-size: 0.68rem; font-weight: 800;">CRASH TELEMETRY &amp; FORENSICS HUD</span>
        <span style="color: #64748b; font-size: 0.8rem;">Deep Request, Device, User Context &amp; Stack Inspection</span>
      </div>
      <h1 style="font-size: 1.65rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif;">System Error Logs &amp; Forensics</h1>
    </div>

    <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
      <a href="<?= url('/admin/db-manager?tab=health') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #10b981; border-color: rgba(16,185,129,0.3);">
        🩺 DB Schema Health Check
      </a>
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
        <input type="text" name="q" value="<?= htmlspecialchars($activeSearch) ?>" placeholder="🔍 Search error message, file, SQL, user or URI..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.5rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.85rem;">
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
            <th style="width: 150px;">Error Type</th>
            <th>Error Message &amp; Source File</th>
            <th>Request &amp; Context</th>
            <th>Client, Device &amp; User</th>
            <th>Logged At</th>
            <th style="width: 160px; text-align: right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($logs)): ?>
            <?php foreach ($logs as $log): ?>
              <?php 
                $rawType = trim((string)($log['error_type'] ?? ''));
                $displayType = !empty($rawType) ? $rawType : 'SYSTEM_ERROR';
                
                $isFatal = in_array($displayType, ['FATAL_ERROR', 'E_ERROR', 'E_COMPILE_ERROR'], true);
                $isDb = $displayType === 'DATABASE_ERROR' || str_contains($displayType, 'PDO') || str_contains($displayType, 'SQL');
                $isWarning = in_array($displayType, ['E_WARNING', 'E_USER_WARNING'], true);
                $isNotice = in_array($displayType, ['E_NOTICE', 'E_USER_NOTICE', 'E_DEPRECATED'], true);
                $isResolved = (int)($log['resolved'] ?? 0) === 1;

                // Decode JSON fields if present
                $locData = !empty($log['location_details']) ? @json_decode($log['location_details'], true) : null;
                $devData = !empty($log['device_details']) ? @json_decode($log['device_details'], true) : null;
                $userData = !empty($log['user_details']) ? @json_decode($log['user_details'], true) : null;
                $reqData = !empty($log['request_data']) ? @json_decode($log['request_data'], true) : null;

                $countryDisplay = $locData['country_name'] ?? 'Local/PK 🇵🇰';
                $browserDisplay = $devData['browser'] ?? 'Browser';
                $osDisplay = $devData['os'] ?? 'OS';

                // User details resolution
                $hasUser = !empty($log['user_id']) && (int)$log['user_id'] > 0;
                $usernameDisplay = $userData['username'] ?? ($log['fallback_username'] ?? null);
                $userRoleDisplay = $userData['role'] ?? ($log['fallback_role'] ?? 'member');

                // Serialize entire log entry safely for JS inspector
                $logPayload = [
                    'id' => (int)$log['id'],
                    'error_type' => $displayType,
                    'message' => $log['message'] ?? '',
                    'file' => $log['file'] ?? '',
                    'line' => (int)($log['line'] ?? 0),
                    'stack_trace' => $log['stack_trace'] ?? '',
                    'request_uri' => $log['request_uri'] ?? '/',
                    'request_method' => $log['request_method'] ?? 'GET',
                    'request_data' => $reqData ?: ($log['request_data'] ?? null),
                    'request_headers' => !empty($log['request_headers']) ? @json_decode($log['request_headers'], true) : null,
                    'user_id' => $log['user_id'] ? (int)$log['user_id'] : null,
                    'user_details' => $userData ?: ($hasUser ? ['username' => $usernameDisplay, 'role' => $userRoleDisplay, 'id' => (int)$log['user_id']] : null),
                    'ip_address' => $log['ip_address'] ?? '127.0.0.1',
                    'location_details' => $locData,
                    'device_details' => $devData,
                    'resolved' => $isResolved,
                    'created_at' => $log['created_at'] ?? ''
                ];
              ?>
              <tr style="<?= $isResolved ? 'opacity: 0.6;' : '' ?>">
                <!-- 1. ERROR TYPE (PROMINENT & VISIBLE) -->
                <td>
                  <?php if ($isFatal): ?>
                    <span class="ts-badge ts-badge-red" style="font-size: 0.72rem; font-weight: 800; display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.25rem 0.6rem; border: 1px solid rgba(239,68,68,0.5); background: rgba(239,68,68,0.18); color: #f87171;">
                      <span>💀</span> <?= htmlspecialchars($displayType) ?>
                    </span>
                  <?php elseif ($isDb): ?>
                    <span class="ts-badge ts-badge-amber" style="font-size: 0.72rem; font-weight: 800; display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.25rem 0.6rem; border: 1px solid rgba(245,158,11,0.5); background: rgba(245,158,11,0.18); color: #fbbf24;">
                      <span>🗄️</span> <?= htmlspecialchars($displayType) ?>
                    </span>
                  <?php elseif ($isWarning): ?>
                    <span class="ts-badge ts-badge-amber" style="font-size: 0.72rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.25rem 0.6rem; border: 1px solid rgba(245,158,11,0.4); background: rgba(245,158,11,0.12); color: #f59e0b;">
                      <span>⚠️</span> <?= htmlspecialchars($displayType) ?>
                    </span>
                  <?php elseif ($isNotice): ?>
                    <span class="ts-badge ts-badge-cyan" style="font-size: 0.72rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.25rem 0.6rem; border: 1px solid rgba(56,189,248,0.4); background: rgba(56,189,248,0.12); color: #38bdf8;">
                      <span>ℹ️</span> <?= htmlspecialchars($displayType) ?>
                    </span>
                  <?php else: ?>
                    <span class="ts-badge ts-badge-indigo" style="font-size: 0.72rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.25rem 0.6rem; border: 1px solid rgba(129,140,248,0.4); background: rgba(99,102,241,0.15); color: #818cf8;">
                      <span>⚡</span> <?= htmlspecialchars($displayType) ?>
                    </span>
                  <?php endif; ?>

                  <?php if ($isResolved): ?>
                    <div style="margin-top: 0.35rem;">
                      <span class="ts-badge ts-badge-green" style="font-size: 0.62rem; font-weight: 800; padding: 0.15rem 0.45rem;">✓ RESOLVED</span>
                    </div>
                  <?php endif; ?>
                </td>

                <!-- 2. ERROR MESSAGE & SOURCE -->
                <td>
                  <div style="font-weight: 700; color: #f8fafc; font-size: 0.88rem; line-height: 1.4; max-width: 480px; word-break: break-word;">
                    <?= htmlspecialchars($log['message']) ?>
                  </div>
                  <?php if (!empty($log['file'])): ?>
                    <div style="font-size: 0.75rem; color: #38bdf8; font-family: var(--admin-font-mono); margin-top: 0.3rem; display: flex; align-items: center; gap: 0.35rem;">
                      <span>📄 <?= htmlspecialchars(basename($log['file'])) ?><?= $log['line'] ? ':' . (int)$log['line'] : '' ?></span>
                    </div>
                  <?php endif; ?>
                </td>

                <!-- 3. REQUEST METHOD & URI -->
                <td>
                  <div style="font-family: var(--admin-font-mono); font-size: 0.78rem; color: #cbd5e1;">
                    <span class="ts-badge" style="font-size: 0.68rem; font-weight: 800; padding: 0.15rem 0.45rem; background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.3);">
                      <?= htmlspecialchars($log['request_method'] ?? 'GET') ?>
                    </span>
                    <span style="margin-left: 0.3rem; color: #f8fafc;"><?= htmlspecialchars($log['request_uri'] ?? '/') ?></span>
                  </div>
                  <?php if (!empty($reqData['body_params'])): ?>
                    <div style="margin-top: 0.3rem; font-size: 0.7rem; color: #94a3b8; font-family: var(--admin-font-mono);">
                      Payload: <?= count($reqData['body_params']) ?> field(s) captured
                    </div>
                  <?php endif; ?>
                </td>

                <!-- 4. CLIENT, DEVICE & USER DETAILS -->
                <td style="font-size: 0.78rem;">
                  <!-- IP & Location -->
                  <div style="display: flex; align-items: center; gap: 0.4rem; font-family: var(--admin-font-mono);">
                    <span style="color: #38bdf8; font-weight: 600;"><?= htmlspecialchars($log['ip_address'] ?? '127.0.0.1') ?></span>
                    <span style="font-size: 0.75rem; color: #94a3b8;"><?= htmlspecialchars($countryDisplay) ?></span>
                  </div>

                  <!-- Device / OS -->
                  <div style="color: #cbd5e1; font-size: 0.72rem; margin-top: 0.2rem;">
                    🖥️ <?= htmlspecialchars($browserDisplay) ?> on <?= htmlspecialchars($osDisplay) ?>
                  </div>

                  <!-- Authenticated User or Guest -->
                  <?php if ($hasUser): ?>
                    <div style="margin-top: 0.3rem; display: flex; align-items: center; gap: 0.35rem;">
                      <span class="ts-badge ts-badge-indigo" style="font-size: 0.65rem; padding: 0.1rem 0.4rem;">
                        👤 <?= htmlspecialchars($usernameDisplay ?: 'User #' . (int)$log['user_id']) ?>
                      </span>
                      <span style="color: #64748b; font-size: 0.68rem;">(<?= htmlspecialchars(strtoupper($userRoleDisplay)) ?>)</span>
                    </div>
                  <?php else: ?>
                    <div style="color: #64748b; font-size: 0.7rem; margin-top: 0.25rem;">
                      👤 Anonymous Guest
                    </div>
                  <?php endif; ?>
                </td>

                <!-- 5. TIMESTAMP -->
                <td style="font-size: 0.78rem; color: #64748b; font-family: var(--admin-font-mono); white-space: nowrap;">
                  <div style="color: #f8fafc; font-weight: 600;"><?= date('H:i:s', strtotime($log['created_at'])) ?></div>
                  <div style="font-size: 0.7rem; color: #94a3b8;"><?= date('M j, Y', strtotime($log['created_at'])) ?></div>
                </td>

                <!-- 6. ACTIONS -->
                <td style="text-align: right;">
                  <div style="display: inline-flex; gap: 0.4rem; justify-content: flex-end;">
                    <button type="button" 
                            data-log="<?= htmlspecialchars(json_encode($logPayload, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP), ENT_QUOTES, 'UTF-8') ?>" 
                            onclick="openFullErrorInspector(this)" 
                            class="ts-btn ts-btn-outline ts-btn-sm" 
                            style="font-size: 0.75rem; padding: 0.25rem 0.65rem; color: #38bdf8; border-color: rgba(56,189,248,0.4); background: rgba(56,189,248,0.1); font-weight: 700;">
                      🔍 Inspect
                    </button>
                    <?php if (!$isResolved): ?>
                      <form action="<?= url('/admin/system-logs/resolve/' . $log['id']) ?>" method="POST" style="display: inline;">
                        <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.5rem; color: #10b981; border-color: rgba(16,185,129,0.3);" title="Mark as Resolved">
                          ✓ Fix
                        </button>
                      </form>
                    <?php endif; ?>
                    <form action="<?= url('/admin/system-logs/delete/' . $log['id']) ?>" method="POST" onsubmit="return confirm('Permanently delete this error log entry?');" style="display: inline; margin: 0;">
                      <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.45rem; color: #ef4444; border-color: rgba(239,68,68,0.3);" title="Delete Record">
                        🗑️
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" style="text-align: center; padding: 3.5rem; color: #64748b;">
                <div style="font-size: 2.2rem; margin-bottom: 0.5rem;">🎉</div>
                <div style="font-size: 1.1rem; color: #10b981; font-weight: 700;">Zero Runtime Errors Encountered!</div>
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
<!-- COMPREHENSIVE SUB-TAB FORENSIC INSPECTOR MODAL -->
<!-- ========================================================================= -->
<div id="errorInspectorModal" class="admin-modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(8px); z-index: 99999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-modal-box" style="max-width: 980px; width: 100%; max-height: 90vh; background: #090d16; border: 1px solid rgba(239,68,68,0.35); border-radius: 14px; box-shadow: 0 20px 60px rgba(0,0,0,0.9), 0 0 35px rgba(239,68,68,0.15); display: flex; flex-direction: column; overflow: hidden;">
    
    <!-- Modal Top Header -->
    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(148,163,184,0.12); background: #0c1220; display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex: 0 0 auto;">
      <div>
        <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.35rem; flex-wrap: wrap;">
          <span id="modalErrorTypeBadge" class="ts-badge ts-badge-red" style="font-size: 0.82rem; font-weight: 800; text-transform: uppercase; padding: 0.3rem 0.75rem; letter-spacing: 0.5px;"></span>
          <span id="modalFileLocation" style="font-size: 0.8rem; color: #38bdf8; font-family: var(--admin-font-mono); background: rgba(56,189,248,0.1); padding: 0.2rem 0.5rem; border-radius: 6px; border: 1px solid rgba(56,189,248,0.2);"></span>
          <span id="modalLoggedTime" style="font-size: 0.75rem; color: #94a3b8; font-family: var(--admin-font-mono);"></span>
        </div>
        <h3 id="modalErrorMessage" style="color: #f87171; font-size: 1.05rem; margin: 0; line-height: 1.45; word-break: break-word; font-family: 'Outfit', sans-serif;"></h3>
      </div>
      <button type="button" onclick="closeErrorInspector()" style="background: transparent; border: none; color: #94a3b8; font-size: 1.4rem; cursor: pointer; padding: 0.2rem 0.5rem; line-height: 1;">✕</button>
    </div>

    <!-- Inspector Navigation Sub-Tabs -->
    <div style="display: flex; border-bottom: 1px solid rgba(148,163,184,0.12); background: #070a12; padding: 0 1.5rem; overflow-x: auto; gap: 0.5rem; flex: 0 0 auto;">
      <button type="button" class="inspector-tab-btn active" onclick="switchInspectorTab('tab-trace')" id="tabBtn-trace">
        💥 Exception &amp; Trace
      </button>
      <button type="button" class="inspector-tab-btn" onclick="switchInspectorTab('tab-request')" id="tabBtn-request">
        🌐 Request &amp; Payload
      </button>
      <button type="button" class="inspector-tab-btn" onclick="switchInspectorTab('tab-headers')" id="tabBtn-headers">
        📋 HTTP Headers
      </button>
      <button type="button" class="inspector-tab-btn" onclick="switchInspectorTab('tab-device')" id="tabBtn-device">
        💻 Device &amp; Location
      </button>
      <button type="button" class="inspector-tab-btn" onclick="switchInspectorTab('tab-user')" id="tabBtn-user">
        👤 User Context
      </button>
    </div>

    <!-- Inspector Tab Content Panels -->
    <div style="padding: 1.5rem; overflow-y: auto; flex: 1 1 auto; background: #090d16;">
      
      <!-- SUB-TAB 1: TRACE & SOURCE CODE -->
      <div id="panel-tab-trace" class="inspector-panel active">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
          <label style="font-size: 0.85rem; font-weight: 700; color: #f8fafc; text-transform: uppercase; letter-spacing: 0.5px;">Call Stack Execution Trail</label>
          <button type="button" onclick="copyTraceToClipboard()" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem;">📋 Copy Trace</button>
        </div>
        <pre id="modalErrorTrace" style="background: #05070b; border: 1px solid rgba(239,68,68,0.25); border-radius: 8px; padding: 1.25rem; color: #cbd5e1; font-family: var(--admin-font-mono); font-size: 0.8rem; line-height: 1.6; white-space: pre-wrap; word-break: break-all; max-height: 420px; overflow-y: auto; margin: 0;"></pre>
      </div>

      <!-- SUB-TAB 2: REQUEST & PAYLOAD -->
      <div id="panel-tab-request" class="inspector-panel" style="display: none;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.25rem;">
          <div style="background: #05070b; padding: 0.85rem 1rem; border-radius: 8px; border: 1px solid rgba(148,163,184,0.1);">
            <div style="font-size: 0.7rem; color: #64748b; text-transform: uppercase; font-weight: 700;">HTTP Method</div>
            <div id="modalReqMethod" style="font-size: 1rem; font-weight: 800; color: #34d399; font-family: var(--admin-font-mono); margin-top: 0.2rem;"></div>
          </div>
          <div style="background: #05070b; padding: 0.85rem 1rem; border-radius: 8px; border: 1px solid rgba(148,163,184,0.1);">
            <div style="font-size: 0.7rem; color: #64748b; text-transform: uppercase; font-weight: 700;">Request URI</div>
            <div id="modalReqUri" style="font-size: 0.92rem; font-weight: 700; color: #38bdf8; font-family: var(--admin-font-mono); margin-top: 0.2rem; word-break: break-all;"></div>
          </div>
        </div>

        <div style="margin-bottom: 1.25rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <label style="font-size: 0.85rem; font-weight: 700; color: #f8fafc;">Captured Request Body &amp; Parameters</label>
            <span style="font-size: 0.7rem; color: #94a3b8;">Passwords &amp; secrets automatically masked</span>
          </div>
          <pre id="modalReqPayload" style="background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 1.25rem; color: #e2e8f0; font-family: var(--admin-font-mono); font-size: 0.8rem; line-height: 1.5; white-space: pre-wrap; word-break: break-all; max-height: 320px; overflow-y: auto; margin: 0;"></pre>
        </div>
      </div>

      <!-- SUB-TAB 3: HTTP HEADERS -->
      <div id="panel-tab-headers" class="inspector-panel" style="display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
          <label style="font-size: 0.85rem; font-weight: 700; color: #f8fafc;">Captured Client Request Headers</label>
          <button type="button" onclick="copyHeadersToClipboard()" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem;">📋 Copy Headers</button>
        </div>
        <div id="modalHeadersTableWrap" style="background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; max-height: 420px; overflow-y: auto;">
          <table class="admin-table" style="margin: 0; font-size: 0.8rem;">
            <thead>
              <tr>
                <th style="width: 220px;">Header Key</th>
                <th>Header Value</th>
              </tr>
            </thead>
            <tbody id="modalHeadersBody"></tbody>
          </table>
        </div>
      </div>

      <!-- SUB-TAB 4: DEVICE & LOCATION -->
      <div id="panel-tab-device" class="inspector-panel" style="display: none;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; margin-bottom: 1.25rem;">
          <!-- IP & Country -->
          <div style="background: #05070b; padding: 1rem; border-radius: 8px; border: 1px solid rgba(148,163,184,0.1);">
            <div style="font-size: 0.7rem; color: #64748b; text-transform: uppercase; font-weight: 700;">Client IP &amp; Geolocation</div>
            <div id="modalClientIp" style="font-size: 1.05rem; font-weight: 800; color: #38bdf8; font-family: var(--admin-font-mono); margin-top: 0.2rem;"></div>
            <div id="modalLocationName" style="font-size: 0.85rem; color: #34d399; font-weight: 600; margin-top: 0.3rem;"></div>
            <div id="modalRegionCity" style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.15rem;"></div>
          </div>

          <!-- OS & Platform -->
          <div style="background: #05070b; padding: 1rem; border-radius: 8px; border: 1px solid rgba(148,163,184,0.1);">
            <div style="font-size: 0.7rem; color: #64748b; text-transform: uppercase; font-weight: 700;">Operating System</div>
            <div id="modalDeviceOs" style="font-size: 1.05rem; font-weight: 800; color: #f8fafc; margin-top: 0.2rem;"></div>
            <div id="modalDeviceType" style="font-size: 0.82rem; color: #c084fc; font-weight: 600; margin-top: 0.3rem;"></div>
          </div>

          <!-- Web Browser -->
          <div style="background: #05070b; padding: 1rem; border-radius: 8px; border: 1px solid rgba(148,163,184,0.1);">
            <div style="font-size: 0.7rem; color: #64748b; text-transform: uppercase; font-weight: 700;">Browser Client</div>
            <div id="modalDeviceBrowser" style="font-size: 1.05rem; font-weight: 800; color: #38bdf8; margin-top: 0.2rem;"></div>
            <div id="modalBrowserVersion" style="font-size: 0.75rem; color: #94a3b8; font-family: var(--admin-font-mono); margin-top: 0.3rem;"></div>
          </div>
        </div>

        <div>
          <label style="font-size: 0.85rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.4rem; display: block;">Raw Client User-Agent Header</label>
          <pre id="modalRawUserAgent" style="background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 1rem; color: #cbd5e1; font-family: var(--admin-font-mono); font-size: 0.78rem; line-height: 1.5; white-space: pre-wrap; word-break: break-all; margin: 0;"></pre>
        </div>
      </div>

      <!-- SUB-TAB 5: USER CONTEXT -->
      <div id="panel-tab-user" class="inspector-panel" style="display: none;">
        <div id="modalUserContainer"></div>
      </div>

    </div>

    <!-- Modal Footer Actions -->
    <div style="padding: 1rem 1.5rem; border-top: 1px solid rgba(148,163,184,0.12); background: #070a12; display: flex; justify-content: space-between; align-items: center; flex: 0 0 auto;">
      <button type="button" onclick="copyFullForensicJson()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3);">
        📋 Copy Complete Forensic JSON
      </button>
      <button type="button" onclick="closeErrorInspector()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #334155; border-color: #334155; font-weight: 700;">
        Close Inspector
      </button>
    </div>

  </div>
</div>

<style>
.inspector-tab-btn {
  background: transparent;
  border: none;
  border-bottom: 2px solid transparent;
  color: #94a3b8;
  font-size: 0.85rem;
  font-weight: 700;
  padding: 0.85rem 1rem;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}
.inspector-tab-btn:hover {
  color: #f8fafc;
  background: rgba(255,255,255,0.02);
}
.inspector-tab-btn.active {
  color: #38bdf8;
  border-bottom-color: #38bdf8;
  background: rgba(56,189,248,0.06);
}
.inspector-panel {
  animation: fadeIn 0.15s ease;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(4px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
let currentInspectedLog = null;

function openFullErrorInspector(btn) {
  try {
    const rawData = btn.getAttribute('data-log');
    if (!rawData) return;
    const log = JSON.parse(rawData);
    currentInspectedLog = log;

    // 1. Header & Error Type
    const typeBadge = document.getElementById('modalErrorTypeBadge');
    typeBadge.innerText = log.error_type || 'SYSTEM_ERROR';
    if (log.error_type === 'DATABASE_ERROR' || log.error_type.includes('PDO')) {
      typeBadge.className = 'ts-badge ts-badge-amber';
      typeBadge.innerText = '🗄️ ' + log.error_type;
    } else if (log.error_type === 'FATAL_ERROR' || log.error_type === 'E_ERROR') {
      typeBadge.className = 'ts-badge ts-badge-red';
      typeBadge.innerText = '💀 ' + log.error_type;
    } else if (log.error_type.includes('WARNING')) {
      typeBadge.className = 'ts-badge ts-badge-amber';
      typeBadge.innerText = '⚠️ ' + log.error_type;
    } else {
      typeBadge.className = 'ts-badge ts-badge-pink';
      typeBadge.innerText = '⚡ ' + log.error_type;
    }

    document.getElementById('modalErrorMessage').innerText = log.message || 'Exception occurred';
    document.getElementById('modalFileLocation').innerText = log.file ? `📄 ${log.file.split(/[\\\\/]/).pop()}:${log.line}` : 'Source Location Unknown';
    document.getElementById('modalLoggedTime').innerText = `🕒 ${log.created_at || 'Just now'}`;

    // 2. Tab 1: Trace
    document.getElementById('modalErrorTrace').innerText = log.stack_trace || 'No execution call stack trace captured for this event.';

    // 3. Tab 2: Request & Payload
    document.getElementById('modalReqMethod').innerText = log.request_method || 'GET';
    document.getElementById('modalReqUri').innerText = log.request_uri || '/';
    
    let payloadText = 'No request body payload or parameters recorded for this HTTP event.';
    if (log.request_data) {
      if (typeof log.request_data === 'string') {
        try {
          payloadText = JSON.stringify(JSON.parse(log.request_data), null, 2);
        } catch(e) {
          payloadText = log.request_data;
        }
      } else {
        payloadText = JSON.stringify(log.request_data, null, 2);
      }
    }
    document.getElementById('modalReqPayload').innerText = payloadText;

    // 4. Tab 3: HTTP Headers
    const headersBody = document.getElementById('modalHeadersBody');
    headersBody.innerHTML = '';
    let headersObj = log.request_headers;
    if (typeof headersObj === 'string') {
      try { headersObj = JSON.parse(headersObj); } catch(e) { headersObj = null; }
    }

    if (headersObj && Object.keys(headersObj).length > 0) {
      for (const [k, v] of Object.entries(headersObj)) {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td style="font-family: var(--admin-font-mono); color: #38bdf8; font-weight: 700;">${escapeHtml(k)}</td>
          <td style="font-family: var(--admin-font-mono); color: #e2e8f0; word-break: break-all;">${escapeHtml(String(v))}</td>
        `;
        headersBody.appendChild(tr);
      }
    } else {
      headersBody.innerHTML = '<tr><td colspan="2" style="text-align: center; color: #64748b; padding: 1.5rem;">No headers captured for this entry.</td></tr>';
    }

    // 5. Tab 4: Device & Location
    const loc = log.location_details || {};
    const dev = log.device_details || {};

    document.getElementById('modalClientIp').innerText = log.ip_address || '127.0.0.1';
    document.getElementById('modalLocationName').innerText = loc.country_name || 'Pakistan 🇵🇰';
    document.getElementById('modalRegionCity').innerText = [loc.city, loc.region, loc.timezone].filter(Boolean).join(' • ') || 'Local / Unknown City';

    document.getElementById('modalDeviceOs').innerText = dev.os || 'Unknown OS';
    document.getElementById('modalDeviceType').innerText = dev.device_type || 'Desktop Form Factor';
    document.getElementById('modalDeviceBrowser').innerText = dev.browser || 'Unknown Browser';
    document.getElementById('modalBrowserVersion').innerText = dev.browser_version ? `Version: ${dev.browser_version}` : 'Version not detected';
    document.getElementById('modalRawUserAgent').innerText = dev.raw_user_agent || 'User-Agent header not recorded';

    // 6. Tab 5: User Context
    const userWrap = document.getElementById('modalUserContainer');
    const u = log.user_details;
    if (log.user_id && log.user_id > 0) {
      const username = (u && u.username) ? u.username : ('User #' + log.user_id);
      const role = (u && u.role) ? u.role.toUpperCase() : 'MEMBER';
      const email = (u && u.email) ? u.email : 'Email not available';
      const status = (u && u.status) ? u.status : 'active';
      
      userWrap.innerHTML = `
        <div style="background: #05070b; border: 1px solid rgba(99,102,241,0.3); border-radius: 10px; padding: 1.5rem;">
          <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.25rem;">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #38bdf8); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; font-weight: 800; color: #ffffff;">
              ${username.charAt(0).toUpperCase()}
            </div>
            <div>
              <div style="display: flex; align-items: center; gap: 0.5rem;">
                <h4 style="color: #f8fafc; margin: 0; font-size: 1.15rem;">${escapeHtml(username)}</h4>
                <span class="ts-badge ts-badge-indigo" style="font-size: 0.68rem; font-weight: 800;">${escapeHtml(role)}</span>
                <span class="ts-badge ts-badge-green" style="font-size: 0.68rem;">${escapeHtml(status)}</span>
              </div>
              <div style="font-size: 0.8rem; color: #94a3b8; font-family: var(--admin-font-mono); margin-top: 0.2rem;">${escapeHtml(email)} &bull; User ID #${log.user_id}</div>
            </div>
          </div>
          <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 1rem;">
            <a href="${window.location.origin}/admin/users?q=${encodeURIComponent(username)}" target="_blank" class="ts-btn ts-btn-primary ts-btn-sm" style="font-size: 0.78rem;">
              👤 View User Account in Admin ↗
            </a>
            <a href="${window.location.origin}/admin/audit-logs?user_id=${log.user_id}" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.78rem; color: #38bdf8;">
              📜 Filter User Audit Logs ↗
            </a>
          </div>
        </div>
      `;
    } else {
      userWrap.innerHTML = `
        <div style="background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 10px; padding: 2rem; text-align: center;">
          <div style="font-size: 2rem; margin-bottom: 0.5rem;">👤</div>
          <h4 style="color: #f8fafc; margin: 0 0 0.35rem 0; font-size: 1.05rem;">Unauthenticated Guest Visitor</h4>
          <p style="color: #94a3b8; font-size: 0.85rem; margin: 0; max-width: 480px; margin: 0 auto;">
            This exception was triggered during an anonymous visitor session without an active authenticated account session.
          </p>
          <div style="margin-top: 1rem; font-family: var(--admin-font-mono); font-size: 0.75rem; color: #64748b;">
            Client IP: ${escapeHtml(log.ip_address || '127.0.0.1')}
          </div>
        </div>
      `;
    }

    // Default to Trace tab
    switchInspectorTab('tab-trace');
    document.getElementById('errorInspectorModal').style.display = 'flex';
  } catch (err) {
    console.error('Inspector render error:', err);
  }
}

function closeErrorInspector() {
  document.getElementById('errorInspectorModal').style.display = 'none';
}

function switchInspectorTab(tabId) {
  document.querySelectorAll('.inspector-tab-btn').forEach(btn => btn.classList.remove('active'));
  document.querySelectorAll('.inspector-panel').forEach(p => {
    p.style.display = 'none';
    p.classList.remove('active');
  });

  const activeBtn = document.getElementById('tabBtn-' + tabId.replace('tab-', ''));
  if (activeBtn) activeBtn.classList.add('active');

  const panel = document.getElementById('panel-' + tabId);
  if (panel) {
    panel.style.display = 'block';
    panel.classList.add('active');
  }
}

function copyTraceToClipboard() {
  if (!currentInspectedLog) return;
  navigator.clipboard.writeText(currentInspectedLog.stack_trace || '').then(() => alert('Execution stack trace copied to clipboard!'));
}

function copyHeadersToClipboard() {
  if (!currentInspectedLog) return;
  const h = currentInspectedLog.request_headers;
  const str = typeof h === 'string' ? h : JSON.stringify(h, null, 2);
  navigator.clipboard.writeText(str).then(() => alert('HTTP Request Headers copied to clipboard!'));
}

function copyFullForensicJson() {
  if (!currentInspectedLog) return;
  navigator.clipboard.writeText(JSON.stringify(currentInspectedLog, null, 2)).then(() => alert('Complete Forensic Telemetry JSON copied to clipboard!'));
}

function escapeHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
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
  link.download = `techscout_error_forensics_${Date.now()}.csv`;
  link.href = window.URL.createObjectURL(csvFile);
  link.style.display = 'none';
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

// Close on Escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeErrorInspector();
  }
});
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
