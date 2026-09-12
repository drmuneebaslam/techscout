<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-main">
  <!-- Topbar -->
  <div class="admin-topbar">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-cyan">ENTERPRISE STORAGE &bull; INNODB TELEMETRY</span>
        <span style="font-size: 0.8rem; color: #64748b;">Table Defragmentation, SQL Snapshots &amp; Schema Integrity</span>
      </div>
      <h1 style="font-size: 1.6rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        Database Health, Diagnostics &amp; Snapshots
      </h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <form action="<?= url('/admin/db-manager/backup') ?>" method="POST" style="margin: 0;">
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #6366f1, #38bdf8); font-weight: 700;">
          <i data-lucide="database"></i> Generate SQL Snapshot Now ⚡
        </button>
      </form>
      <form action="<?= url('/admin/db-manager/auto-repair-schema') ?>" method="POST" onsubmit="return confirm('Execute automated schema alignment and missing column migration?');" style="margin: 0;">
        <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #10b981; border-color: rgba(16,185,129,0.4);">
          <i data-lucide="wrench"></i> Auto-Repair Schema
        </button>
      </form>
      <form action="<?= url('/admin/db-manager/optimize') ?>" method="POST" onsubmit="return confirm('Recalculate indices and optimize all database tables?');" style="margin: 0;">
        <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm">
          <i data-lucide="zap"></i> Optimize All Tables
        </button>
      </form>
    </div>
  </div>

  <!-- Metric Stat Cards -->
  <div class="admin-stat-grid">
    <div class="admin-stat-card">
      <div class="admin-stat-label">Total Database Footprint</div>
      <div class="admin-stat-val" style="color: #38bdf8; font-family: var(--admin-font-mono);">
        <?= round($totalDbSize / (1024 * 1024), 2) ?> MB
      </div>
      <div style="font-size: 0.8rem; color: #94a3b8;">Active Data &amp; Index Allocation</div>
    </div>

    <div class="admin-stat-card">
      <div class="admin-stat-label">Schema Relational Tables</div>
      <div class="admin-stat-val" style="color: #a855f7; font-family: var(--admin-font-mono);"><?= count($tables) ?></div>
      <div style="font-size: 0.8rem; color: #c084fc;">InnoDB normalized tables</div>
    </div>

    <div class="admin-stat-card">
      <div class="admin-stat-label">Total Entities Indexed</div>
      <div class="admin-stat-val" style="color: #10b981; font-family: var(--admin-font-mono);"><?= number_format($totalRows) ?></div>
      <div style="font-size: 0.8rem; color: #34d399;">Estimated row count across tables</div>
    </div>

    <div class="admin-stat-card">
      <div class="admin-stat-label">Schema Integrity Status</div>
      <?php if ($unhealthyCount > 0): ?>
        <div class="admin-stat-val" style="color: #ef4444; font-size: 1.35rem; font-weight: 800;">
          ⚠️ <?= $unhealthyCount ?> Issue<?= $unhealthyCount > 1 ? 's' : '' ?>
        </div>
        <div style="font-size: 0.8rem; color: #f87171;">Action required in Health tab</div>
      <?php else: ?>
        <div class="admin-stat-val" style="color: #10b981; font-size: 1.35rem; font-weight: 800;">
          ✓ Fully Aligned
        </div>
        <div style="font-size: 0.8rem; color: #34d399;">Schema columns match codebase</div>
      <?php endif; ?>
    </div>
  </div>

  <!-- DB Manager Navigation Sub-Tabs -->
  <div style="display: flex; border-bottom: 1px solid rgba(148,163,184,0.15); margin-bottom: 1.5rem; gap: 0.5rem; overflow-x: auto;">
    <a href="<?= url('/admin/db-manager?tab=tables') ?>" class="ts-subtab-link <?= ($activeTab === 'tables') ? 'active' : '' ?>">
      🗄️ Tables &amp; Allocation (<?= count($tables) ?>)
    </a>
    <a href="<?= url('/admin/db-manager?tab=snapshots') ?>" class="ts-subtab-link <?= ($activeTab === 'snapshots') ? 'active' : '' ?>">
      💾 Snapshots &amp; Disaster Recovery (<?= count($backups) ?>)
    </a>
    <a href="<?= url('/admin/db-manager?tab=health') ?>" class="ts-subtab-link <?= ($activeTab === 'health') ? 'active' : '' ?>">
      🩺 Schema Health &amp; Column Alignment <?= ($unhealthyCount > 0) ? '<span class="ts-badge ts-badge-red" style="font-size: 0.6rem;">' . $unhealthyCount . '</span>' : '✓' ?>
    </a>
    <a href="<?= url('/admin/db-manager?tab=query') ?>" class="ts-subtab-link <?= ($activeTab === 'query') ? 'active' : '' ?>">
      ⚡ SQL Maintenance Terminal
    </a>
    <a href="<?= url('/admin/db-manager?tab=custom_export') ?>" class="ts-subtab-link <?= ($activeTab === 'custom_export') ? 'active' : '' ?>" style="color: #38bdf8; font-weight: 700;">
      📦 Custom Export Studio (Folders, Schema &amp; Granular Seeds) ⚡
    </a>
  </div>

  <!-- ========================================================================= -->
  <!-- ========================================================================= -->
  <!-- TAB 1: TABLES BREAKDOWN -->
  <!-- ========================================================================= -->
  <?php if ($activeTab === 'tables'): ?>
    <div class="admin-table-wrap" style="padding: 1.5rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
        <div>
          <h3 style="font-size: 1.15rem; color: var(--admin-text-main); margin: 0 0 0.25rem 0;">Schema Tables Breakdown &amp; Footprint</h3>
          <p style="color: var(--admin-text-muted); font-size: 0.82rem; margin: 0;">Database: <code style="color: #38bdf8;"><?= htmlspecialchars($dbName) ?></code> &bull; Server Engine: <code style="color: #a855f7;"><?= htmlspecialchars($mysqlVersion) ?></code></p>
        </div>
        <div style="min-width: 260px;">
          <input type="text" id="tableFilterInput" onkeyup="filterDbTables()" placeholder="🔍 Fast search table name..." style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.2)); padding: 0.45rem 0.85rem; border-radius: 6px; color: var(--admin-text-main); font-size: 0.85rem;">
        </div>
      </div>

      <div style="overflow-x: auto;">
        <table class="admin-table" id="dbTablesTable">
          <thead>
            <tr>
              <th>Table Name</th>
              <th>Engine</th>
              <th>Estimated Rows</th>
              <th>Data Size</th>
              <th>Index Size</th>
              <th>Total Footprint</th>
              <th style="width: 260px; text-align: right;">Operations</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($tables as $t): ?>
              <?php
                $tName = $t['TABLE_NAME'];
                $totalKb = round((int)$t['TOTAL_SIZE'] / 1024, 2);
                $isBig = $totalKb > 1024;
              ?>
              <tr>
                <td>
                  <strong style="color: var(--admin-text-main); font-family: var(--admin-font-mono); font-size: 0.92rem;"><?= htmlspecialchars($tName) ?></strong>
                  <?php if (in_array($tName, ['users', 'system_error_logs', 'audit_logs', 'contents'], true)): ?>
                    <span class="ts-badge ts-badge-indigo" style="font-size: 0.6rem; margin-left: 0.35rem;">CORE</span>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="ts-badge ts-badge-cyan" style="font-size: 0.7rem; font-family: var(--admin-font-mono);"><?= htmlspecialchars($t['ENGINE'] ?? 'InnoDB') ?></span>
                </td>
                <td>
                  <span style="color: var(--admin-text-main); font-family: var(--admin-font-mono); font-size: 0.88rem;"><?= number_format((int)$t['TABLE_ROWS']) ?></span>
                </td>
                <td style="font-family: var(--admin-font-mono); font-size: 0.82rem; color: var(--admin-text-muted);">
                  <?= round((int)$t['DATA_LENGTH'] / 1024, 2) ?> KB
                </td>
                <td style="font-family: var(--admin-font-mono); font-size: 0.82rem; color: var(--admin-text-muted);">
                  <?= round((int)$t['INDEX_LENGTH'] / 1024, 2) ?> KB
                </td>
                <td>
                  <strong style="color: <?= $isBig ? '#f59e0b' : '#38bdf8' ?>; font-family: var(--admin-font-mono); font-size: 0.88rem;">
                    <?= $isBig ? round($totalKb / 1024, 2) . ' MB' : $totalKb . ' KB' ?>
                  </strong>
                </td>
                <td style="text-align: right;">
                  <div style="display: inline-flex; gap: 0.35rem; justify-content: flex-end; align-items: center;">
                    <a href="javascript:void(0)" onclick="quickSelectTable('<?= htmlspecialchars($tName, ENT_QUOTES) ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;" title="Query in SQL Terminal">
                      ⚡ Query
                    </a>
                    <form action="<?= url('/admin/db-manager/optimize-table/' . $tName) ?>" method="POST" style="margin: 0;">
                      <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem; color: #34d399; border-color: rgba(16,185,129,0.3);" title="Optimize Table &amp; Defragment">
                        Opt
                      </button>
                    </form>
                    <form action="<?= url('/admin/db-manager/check-table/' . $tName) ?>" method="POST" style="margin: 0;">
                      <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem; color: #38bdf8; border-color: rgba(56,189,248,0.3);" title="Run CHECK TABLE">
                        Check
                      </button>
                    </form>
                    <form action="<?= url('/admin/db-manager/repair-table/' . $tName) ?>" method="POST" style="margin: 0;">
                      <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem; color: #f59e0b; border-color: rgba(245,158,11,0.3);" title="Run REPAIR TABLE">
                        Repair
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

  <!-- ========================================================================= -->
  <!-- TAB 2: DISASTER RECOVERY SNAPSHOTS -->
  <!-- ========================================================================= -->
  <?php elseif ($activeTab === 'snapshots'): ?>
    <div class="admin-table-wrap" style="padding: 1.5rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
        <div>
          <h3 style="font-size: 1.15rem; color: var(--admin-text-main); margin: 0 0 0.25rem 0;">💾 Disaster Recovery &amp; Automated SQL Snapshots</h3>
          <p style="color: var(--admin-text-muted); font-size: 0.82rem; margin: 0;">Zero-downtime full schema dumps, indices and transaction row data</p>
        </div>
        <form action="<?= url('/admin/db-manager/backup') ?>" method="POST" style="margin: 0;">
          <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #10b981, #059669); font-weight: 700;">
            <i data-lucide="plus-circle"></i> Create New Snapshot Now ⚡
          </button>
        </form>
      </div>

      <table class="admin-table">
        <thead>
          <tr>
            <th>Snapshot Filename</th>
            <th>File Size</th>
            <th>Tables &amp; Total Rows</th>
            <th>Created By</th>
            <th>Timestamp (PKT)</th>
            <th style="width: 220px; text-align: right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($backups)): ?>
            <?php foreach ($backups as $b): ?>
              <tr>
                <td>
                  <strong style="color: #38bdf8; font-family: var(--admin-font-mono); font-size: 0.85rem;"><?= htmlspecialchars($b['filename']) ?></strong>
                  <div style="font-size: 0.72rem; color: var(--admin-text-muted); font-family: var(--admin-font-mono);">SHA256: <?= substr($b['checksum'] ?? '', 0, 16) ?>...</div>
                </td>
                <td>
                  <span class="ts-badge ts-badge-cyan" style="font-family: var(--admin-font-mono);"><?= htmlspecialchars($b['file_size_formatted']) ?></span>
                </td>
                <td style="font-size: 0.82rem; color: var(--admin-text-main);">
                  <?= (int)$b['table_count'] ?> tables &bull; <?= number_format((int)$b['total_rows']) ?> rows
                </td>
                <td style="font-size: 0.82rem; color: var(--admin-text-muted);">
                  <?= htmlspecialchars($b['creator_name'] ?? 'System / Admin') ?>
                </td>
                <td style="font-size: 0.78rem; color: var(--admin-text-muted); font-family: var(--admin-font-mono); white-space: nowrap;">
                  <?= \App\Core\DateHelper::formatDateTime($b['created_at']) ?>
                </td>
                <td style="text-align: right;">
                  <div style="display: inline-flex; gap: 0.35rem; justify-content: flex-end;">
                    <a href="<?= url('/admin/db-manager/download/' . $b['id']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.55rem; font-size: 0.72rem; color: #34d399; border-color: rgba(16,185,129,0.4);" title="Download SQL File">
                      Download ⬇
                    </a>
                    <form action="<?= url('/admin/db-manager/restore/' . $b['id']) ?>" method="POST" onsubmit="return confirm('WARNING: Restoring this snapshot will overwrite current database state. Proceed?');" style="display: inline; margin: 0;">
                      <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.55rem; font-size: 0.72rem; color: #f59e0b; border-color: rgba(245,158,11,0.4);" title="Restore Snapshot">
                        Restore ⚡
                      </button>
                    </form>
                    <form action="<?= url('/admin/db-manager/delete/' . $b['id']) ?>" method="POST" onsubmit="return confirm('Delete this backup snapshot?');" style="display: inline; margin: 0;">
                      <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.45rem; font-size: 0.72rem; color: #ef4444; border-color: rgba(239,68,68,0.4);" title="Delete Snapshot">
                        🗑
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--admin-text-muted);">
                No backup snapshots found in database registry. Click "Create New Snapshot Now" to capture a zero-downtime backup.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  <!-- ========================================================================= -->
  <!-- TAB 3: SCHEMA HEALTH & ALIGNMENT -->
  <!-- ========================================================================= -->
  <?php elseif ($activeTab === 'health'): ?>
    <div class="admin-table-wrap" style="padding: 1.75rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
          <h3 style="font-size: 1.2rem; color: var(--admin-text-main); margin: 0 0 0.25rem 0;">🩺 Schema Health &amp; Missing Column Diagnostics</h3>
          <p style="color: var(--admin-text-muted); font-size: 0.85rem; margin: 0;">Validates live MySQL table structures against application model requirements</p>
        </div>

        <form action="<?= url('/admin/db-manager/auto-repair-schema') ?>" method="POST" onsubmit="return confirm('Execute automated schema repair and add all missing columns?');">
          <button type="submit" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #10b981, #059669); font-weight: 700;">
            🚀 Run Automated Schema Repair Now
          </button>
        </form>
      </div>

      <!-- Health Checklist -->
      <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2rem;">
        <?php foreach ($healthChecks as $check): ?>
          <?php
            $isHealthy = $check['status'] === 'healthy';
            $isCritical = $check['status'] === 'critical';
          ?>
          <div class="audit-inspector-card" style="border-color: <?= $isHealthy ? 'rgba(16,185,129,0.3)' : ($isCritical ? 'rgba(239,68,68,0.4)' : 'rgba(245,158,11,0.4)') ?>; padding: 1.25rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
              <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                <span style="font-size: 1.1rem;"><?= $isHealthy ? '✅' : ($isCritical ? '❌' : '⚠️') ?></span>
                <strong style="color: var(--admin-text-main); font-size: 0.95rem;"><?= htmlspecialchars($check['name']) ?></strong>
                <span class="ts-badge <?= $isHealthy ? 'ts-badge-green' : ($isCritical ? 'ts-badge-red' : 'ts-badge-amber') ?>" style="font-size: 0.65rem;">
                  <?= strtoupper($check['status']) ?>
                </span>
              </div>
              <div style="color: var(--admin-text-muted); font-size: 0.82rem; margin-left: 1.7rem;">
                <?= htmlspecialchars($check['description']) ?>
              </div>
            </div>

            <?php if (!$isHealthy): ?>
              <form action="<?= url('/admin/db-manager/auto-repair-schema') ?>" method="POST">
                <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #34d399; border-color: rgba(16,185,129,0.4);">
                  Fix Column ⚡
                </button>
              </form>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- SQL ALTER Statements Box -->
      <div class="audit-inspector-card" style="border-color: rgba(99,102,241,0.3); padding: 1.25rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
          <h4 style="color: var(--admin-text-main); margin: 0; font-size: 0.95rem;">Manual SQL Upgrade / ALTER Statements (For phpMyAdmin / Terminal)</h4>
          <button type="button" onclick="copyAlterStatements()" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem; color: #38bdf8;">
            📋 Copy SQL Statements
          </button>
        </div>
        <pre id="alterStatementsPre" class="audit-inspector-code" style="margin: 0; font-size: 0.78rem; line-height: 1.6;">-- 1. Upgrade Users Table for 6-Digit Email Verification OTP PIN
ALTER TABLE `users` 
  MODIFY COLUMN `status` ENUM('pending_verification','unverified','active','suspended','banned','deleted') NOT NULL DEFAULT 'pending_verification',
  ADD COLUMN `verification_code` VARCHAR(20) DEFAULT NULL AFTER `suspended_until`;

-- 2. Upgrade system_error_logs for Deep Crash Forensics &amp; Telemetry
ALTER TABLE `system_error_logs`
  MODIFY COLUMN `error_type` VARCHAR(100) NOT NULL DEFAULT 'ERROR',
  ADD COLUMN `request_data` LONGTEXT NULL AFTER `request_method`,
  ADD COLUMN `request_headers` LONGTEXT NULL AFTER `request_data`,
  ADD COLUMN `user_details` LONGTEXT NULL AFTER `user_id`,
  ADD COLUMN `location_details` LONGTEXT NULL AFTER `ip_address`,
  ADD COLUMN `device_details` LONGTEXT NULL AFTER `location_details`;</pre>
      </div>
    </div>

  <!-- ========================================================================= -->
  <!-- ========================================================================= -->
  <!-- TAB 4: SQL MAINTENANCE & DIAGNOSTIC TERMINAL -->
  <!-- ========================================================================= -->
  <?php elseif ($activeTab === 'query'): ?>
    <?php 
      // Extract flash once at the top of the tab so it is not consumed prematurely
      $queryFlash = \App\Core\Session::getFlash('query_results');
      $flashError = \App\Core\Session::getFlash('error');
      $flashSuccess = \App\Core\Session::getFlash('success');
      $persistedSql = $queryFlash['sql'] ?? '';
    ?>
    <div class="admin-table-wrap" style="padding: 1.75rem;">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
        <div>
          <h3 style="font-size: 1.25rem; color: var(--admin-text-main); margin: 0 0 0.35rem 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
            ⚡ SQL Diagnostic &amp; Analytical Terminal
          </h3>
          <p style="color: var(--admin-text-muted); font-size: 0.85rem; margin: 0;">
            Execute live safe queries (<code style="color: #38bdf8;">SELECT</code>, <code style="color: #38bdf8;">SHOW</code>, <code style="color: #38bdf8;">EXPLAIN</code>, <code style="color: #38bdf8;">DESCRIBE</code>, <code style="color: #38bdf8;">CHECK</code>, <code style="color: #38bdf8;">OPTIMIZE</code>) with interactive grid results.
          </p>
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
          <span class="ts-badge ts-badge-cyan" style="font-family: var(--admin-font-mono); font-size: 0.72rem;">Ctrl+Enter to Execute</span>
        </div>
      </div>

      <?php if (!empty($flashError)): ?>
        <div class="ts-alert ts-alert-error" style="margin-bottom: 1.25rem; border-radius: 8px; font-size: 0.88rem;">
          <strong>⚠️ Execution Error:</strong> <?= htmlspecialchars($flashError) ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($flashSuccess)): ?>
        <div class="ts-alert ts-alert-success" style="margin-bottom: 1.25rem; border-radius: 8px; font-size: 0.88rem;">
          <strong>✓ Success:</strong> <?= htmlspecialchars($flashSuccess) ?>
        </div>
      <?php endif; ?>

      <!-- Quick Diagnostic Shortcuts -->
      <div style="margin-bottom: 1.25rem;">
        <div style="font-size: 0.75rem; color: var(--admin-text-muted); font-weight: 700; text-transform: uppercase; margin-bottom: 0.5rem; letter-spacing: 0.5px;">
          Quick Diagnostic Presets:
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
          <button type="button" onclick="setQuery('SHOW TABLE STATUS')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem;">📊 Table Status</button>
          <button type="button" onclick="setQuery('SHOW PROCESSLIST')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem;">🧵 Active Threads</button>
          <button type="button" onclick="setQuery('SELECT id, email, username, role, status, created_at FROM users ORDER BY id DESC LIMIT 15')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem;">👤 Recent Users</button>
          <button type="button" onclick="setQuery('SELECT id, error_type, message, file, line, created_at FROM system_error_logs ORDER BY id DESC LIMIT 15')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem;">💥 Recent Errors</button>
          <button type="button" onclick="setQuery('SELECT id, user_id, action, entity_type, ip_address, created_at FROM audit_logs ORDER BY id DESC LIMIT 15')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem;">🛡️ Audit Events</button>
          <button type="button" onclick="setQuery('SELECT id, title, content_type, author_id, view_count, created_at FROM contents ORDER BY id DESC LIMIT 15')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem;">📄 Recent Content</button>
          <button type="button" onclick="setQuery('SHOW VARIABLES LIKE \'%timeout%\'')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem;">⏱️ Timeout Config</button>
          <button type="button" onclick="setQuery('SHOW VARIABLES LIKE \'%version%\'')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem;">ℹ️ Engine Version</button>
        </div>
      </div>

      <!-- Query Form -->
      <form id="dbQueryForm" action="<?= url('/admin/db-manager/query') ?>" method="POST" onsubmit="return handleQuerySubmit(event)" style="margin-bottom: 1.5rem;">
        <div style="position: relative;">
          <textarea name="sql_query" id="sqlQueryInput" rows="5" placeholder="SELECT * FROM users ORDER BY id DESC LIMIT 15;" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.2)); border-radius: 8px; padding: 1rem; color: #38bdf8; font-family: var(--admin-font-mono); font-size: 0.88rem; line-height: 1.6; box-sizing: border-box; resize: vertical;"><?= htmlspecialchars($persistedSql) ?></textarea>
        </div>
        <div style="margin-top: 0.75rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
          <div style="display: flex; gap: 0.5rem; align-items: center;">
            <button type="button" onclick="formatSqlInInput()" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem;">
              ✨ Format Query
            </button>
            <button type="button" onclick="clearQueryInput()" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem;">
              🗑️ Clear
            </button>
          </div>
          <button type="submit" id="btnExecuteSql" class="ts-btn ts-btn-primary" style="padding: 0.55rem 1.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.5rem;">
            <span id="btnExecuteIcon">▶</span> <span id="btnExecuteText">Execute SQL Query</span>
          </button>
        </div>
      </form>

      <!-- Live Asynchronous Error Box -->
      <div id="liveQueryErrorBox" class="ts-alert ts-alert-error" style="display: none; margin-bottom: 1.25rem; border-radius: 8px; font-size: 0.88rem;"></div>

      <!-- Query Results Container -->
      <div id="queryResultsContainer">
        <?php 
          if (!empty($queryFlash)):
            $rows = $queryFlash['rows'] ?? [];
            $durationMs = $queryFlash['duration_ms'] ?? 0;
            $totalCount = $queryFlash['total_count'] ?? count($rows);
            $columns = !empty($rows) ? array_keys($rows[0]) : [];
        ?>
          <div class="audit-inspector-card" style="padding: 1.25rem; border-color: rgba(56,189,248,0.25);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
              <div style="font-size: 0.88rem; color: var(--admin-text-main); font-family: var(--admin-font-mono);">
                <span style="color: #34d399; font-weight: 800;">✓ Query executed:</span> 
                <strong><?= number_format($totalCount) ?></strong> row(s) returned in <strong style="color: #38bdf8;"><?= $durationMs ?> ms</strong>
                <?php if ($totalCount > count($rows)): ?>
                  <span style="color: var(--admin-text-muted); font-size: 0.75rem;">(previewing first <?= count($rows) ?> rows)</span>
                <?php endif; ?>
              </div>
              
              <?php if (!empty($rows)): ?>
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                  <input type="text" id="queryGridFilter" onkeyup="filterQueryResults()" placeholder="🔍 Filter results..." style="background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.2)); padding: 0.35rem 0.65rem; border-radius: 6px; color: var(--admin-text-main); font-size: 0.78rem; width: 170px;">
                  <button type="button" onclick="exportQueryResultsCsv()" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.35rem 0.65rem;">
                    📥 CSV
                  </button>
                  <button type="button" onclick="exportQueryResultsJson()" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.35rem 0.65rem;">
                    📋 JSON
                  </button>
                </div>
              <?php endif; ?>
            </div>

            <?php if (!empty($rows)): ?>
              <div style="overflow-x: auto; max-height: 520px; border-radius: 8px; border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15));">
                <table class="admin-table" id="sqlResultsGrid" style="margin: 0; font-size: 0.8rem;">
                  <thead style="position: sticky; top: 0; z-index: 5;">
                    <tr>
                      <th style="width: 45px; text-align: center; color: var(--admin-text-muted); font-family: var(--admin-font-mono);">#</th>
                      <?php foreach ($columns as $col): ?>
                        <th style="font-family: var(--admin-font-mono); color: #38bdf8; white-space: nowrap;"><?= htmlspecialchars((string)$col) ?></th>
                      <?php endforeach; ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($rows as $idx => $r): ?>
                      <tr>
                        <td style="text-align: center; color: var(--admin-text-muted); font-family: var(--admin-font-mono); font-size: 0.75rem;"><?= $idx + 1 ?></td>
                        <?php foreach ($columns as $col): ?>
                          <?php $val = $r[$col] ?? null; ?>
                          <td style="font-family: var(--admin-font-mono); color: var(--admin-text-main); word-break: break-all; max-width: 320px;" title="Click to copy value" onclick="copyCellValue(this)">
                            <?php if ($val === null): ?>
                              <span style="color: #64748b; font-style: italic;">NULL</span>
                            <?php else: ?>
                              <?= htmlspecialchars((string)$val) ?>
                            <?php endif; ?>
                          </td>
                        <?php endforeach; ?>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <div style="color: var(--admin-text-muted); font-size: 0.85rem; padding: 1.5rem; text-align: center; background: var(--admin-card-bg, #05070b); border-radius: 8px;">
                Empty set returned (0 rows).
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>

    </div>

  <!-- ========================================================================= -->
  <!-- TAB 5: CUSTOM EXPORT STUDIO (FOLDERS, SCHEMA & GRANULAR SEEDS) -->
  <!-- ========================================================================= -->
  <?php elseif ($activeTab === 'custom_export'): ?>
    <?php
      $dirInfo = $directoryTree ?? \App\Models\CustomExporter::getDirectoryTree();
      $tablesMeta = $tablesWithMeta ?? \App\Models\CustomExporter::getTablesMetadata();
    ?>
    <form id="customExportForm" action="<?= url('/admin/db-manager/custom-export') ?>" method="POST">
      <input type="hidden" name="tables_config" id="tables_config_json" value="{}">

      <!-- Studio Header & Quick Presets -->
      <div class="admin-table-wrap" style="padding: 1.75rem; margin-bottom: 1.5rem; background: var(--admin-card-bg); border: 1px solid var(--admin-border-color); border-radius: 12px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
          <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
              <span class="ts-badge ts-badge-cyan" style="font-weight: 800;">ENTERPRISE DEPLOYMENT STUDIO</span>
              <span class="ts-badge ts-badge-green">GRANULAR SEED SELECTOR</span>
            </div>
            <h2 style="font-size: 1.4rem; color: var(--admin-text-main); margin: 0 0 0.25rem 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
              📦 Selective Workspace &amp; Database Exporter
            </h2>
            <p style="color: var(--admin-text-muted); font-size: 0.85rem; margin: 0;">
              Select exactly which project folders to package, live database table schemas to extract, and pick specific records as SQL seeds.
            </p>
          </div>

          <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <button type="button" onclick="selectPreset('recommended')" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3);">
              ✨ Production Core Preset
            </button>
            <button type="button" onclick="selectPreset('all')" class="ts-btn ts-btn-outline ts-btn-sm">
              📁 Select All Files &amp; Schemas
            </button>
            <button type="button" onclick="selectPreset('reset')" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.3);">
              🔄 Reset
            </button>
          </div>
        </div>
      </div>

      <!-- Grid: Section 1 (Directories) & Section 2 (Tables / Seeds) -->
      <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
        
        <!-- =================================================================== -->
        <!-- 1. DIRECTORY TREE SELECTOR -->
        <!-- =================================================================== -->
        <div class="admin-table-wrap" style="padding: 1.5rem; background: var(--admin-card-bg); border: 1px solid var(--admin-border-color); border-radius: 12px;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
            <div>
              <h3 style="font-size: 1.1rem; color: var(--admin-text-main); margin: 0 0 0.25rem 0; display: flex; align-items: center; gap: 0.5rem;">
                <span>📁 Workspace Project Folders</span>
                <span id="selected_folders_count_badge" class="ts-badge ts-badge-cyan" style="font-size: 0.7rem;">0 folders selected</span>
              </h3>
              <p style="color: var(--admin-text-muted); font-size: 0.8rem; margin: 0;">Choose root directories to compress into the deployment archive</p>
            </div>
            <div style="display: flex; gap: 0.5rem;">
              <button type="button" onclick="toggleAllFolders(true)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem;">Check All</button>
              <button type="button" onclick="toggleAllFolders(false)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem;">Uncheck All</button>
            </div>
          </div>

          <!-- Directory Cards Grid -->
          <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; margin-bottom: 1.25rem;">
            <?php foreach (($dirInfo['directories'] ?? []) as $dirKey => $dir): ?>
              <label class="folder-select-card" style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 1rem; border-radius: 8px; border: 1px solid var(--admin-border-color); background: var(--admin-input-bg); cursor: pointer; transition: all 0.2s ease;">
                <input type="checkbox" name="folders[]" value="<?= htmlspecialchars($dirKey) ?>" class="folder-checkbox" <?= !empty($dir['recommended']) ? 'checked' : '' ?> onchange="updateExportSummary()" style="margin-top: 0.25rem; width: 16px; height: 16px; accent-color: #38bdf8;">
                <div style="flex: 1;">
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
                    <strong style="color: var(--admin-text-main); font-size: 0.92rem;"><?= htmlspecialchars($dir['name']) ?></strong>
                    <?php if (!empty($dir['recommended'])): ?>
                      <span class="ts-badge ts-badge-indigo" style="font-size: 0.58rem;">CORE</span>
                    <?php endif; ?>
                  </div>
                  <div style="font-size: 0.75rem; color: var(--admin-text-muted); margin-bottom: 0.45rem;">
                    <?= htmlspecialchars($dir['desc']) ?>
                  </div>
                  <div style="display: flex; gap: 0.75rem; font-size: 0.72rem; color: #38bdf8; font-family: var(--admin-font-mono);">
                    <span>📄 <?= number_format($dir['file_count']) ?> files</span>
                    <span>💾 <?= htmlspecialchars($dir['size_formatted']) ?></span>
                  </div>
                </div>
              </label>
            <?php endforeach; ?>
          </div>

          <!-- Root Files Checkbox Strip -->
          <div style="padding: 0.85rem 1rem; background: rgba(56,189,248,0.04); border: 1px dashed rgba(56,189,248,0.25); border-radius: 8px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
            <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer; margin: 0;">
              <input type="checkbox" name="include_root_files" value="1" checked onchange="updateExportSummary()" style="width: 16px; height: 16px; accent-color: #38bdf8;">
              <div>
                <strong style="color: var(--admin-text-main); font-size: 0.85rem;">Include Root Deployment Files</strong>
                <span style="font-size: 0.78rem; color: var(--admin-text-muted); margin-left: 0.35rem;">
                  (<code>index.php</code>, <code>.htaccess</code>, <code>favicon.ico</code>, <code>robots.txt</code>, <code>composer.json</code>)
                </span>
              </div>
            </label>
            <span style="font-size: 0.78rem; color: #38bdf8; font-family: var(--admin-font-mono);">
              <?= count($dirInfo['root_files'] ?? []) ?> files (~<?= $dirInfo['root_files_total_size'] ?? '0 KB' ?>)
            </span>
          </div>
        </div>

        <!-- =================================================================== -->
        <!-- 2. DATABASE SCHEMA & GRANULAR SEED EXPORTER -->
        <!-- =================================================================== -->
        <div class="admin-table-wrap" style="padding: 1.5rem; background: var(--admin-card-bg); border: 1px solid var(--admin-border-color); border-radius: 12px;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
            <div>
              <h3 style="font-size: 1.1rem; color: var(--admin-text-main); margin: 0 0 0.25rem 0; display: flex; align-items: center; gap: 0.5rem;">
                <span>🗄️ Database Schemas &amp; Granular Seed Picker</span>
                <span id="selected_schemas_count_badge" class="ts-badge ts-badge-green" style="font-size: 0.7rem;">0 schemas</span>
                <span id="selected_seeds_count_badge" class="ts-badge ts-badge-pink" style="font-size: 0.7rem;">0 seeds</span>
              </h3>
              <p style="color: var(--admin-text-muted); font-size: 0.8rem; margin: 0;">Extract real DDL CREATE TABLE structures and pick exact records as INSERT INTO seeds</p>
            </div>
            
            <!-- Quick Filter & Fast Selectors -->
            <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
              <input type="text" id="exportTableSearch" onkeyup="filterExportTables()" placeholder="🔍 Filter table..." style="background: var(--admin-input-bg); border: 1px solid var(--admin-border-color); padding: 0.35rem 0.75rem; border-radius: 6px; color: var(--admin-text-main); font-size: 0.82rem; width: 170px;">
              <button type="button" onclick="setAllSchemas(true)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem;">All Schemas</button>
              <button type="button" onclick="setAllSchemas(false)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem;">No Schemas</button>
              <button type="button" onclick="setAllSeedsMode('none')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem;">Clear Seeds</button>
            </div>
          </div>

          <!-- Tables & Seed Mode List -->
          <div style="overflow-x: auto; max-height: 600px; border: 1px solid var(--admin-border-color); border-radius: 8px;">
            <table class="admin-table" id="exportTablesGrid" style="margin: 0;">
              <thead style="position: sticky; top: 0; z-index: 5; background: var(--admin-card-bg);">
                <tr>
                  <th style="width: 45px; text-align: center;">DDL</th>
                  <th>Table Name</th>
                  <th>Total Rows</th>
                  <th>Engine / Footprint</th>
                  <th style="width: 380px;">Seed Generation Mode</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($tablesMeta as $tblName => $tm): ?>
                  <tr class="export-table-row" data-table="<?= htmlspecialchars($tblName) ?>">
                    <td style="text-align: center;">
                      <input type="checkbox" id="schema_<?= htmlspecialchars($tblName) ?>" class="table-schema-checkbox" checked onchange="updateTableConfig('<?= htmlspecialchars($tblName) ?>')" style="width: 16px; height: 16px; accent-color: #10b981;" title="Include CREATE TABLE schema">
                    </td>
                    <td>
                      <label for="schema_<?= htmlspecialchars($tblName) ?>" style="cursor: pointer; margin: 0;">
                        <strong style="color: var(--admin-text-main); font-family: var(--admin-font-mono); font-size: 0.88rem;"><?= htmlspecialchars($tblName) ?></strong>
                        <?php if (in_array($tblName, ['users', 'contents', 'site_settings', 'categories', 'mail_templates'], true)): ?>
                          <span class="ts-badge ts-badge-cyan" style="font-size: 0.58rem; margin-left: 0.25rem;">KEY SEED</span>
                        <?php endif; ?>
                      </label>
                    </td>
                    <td>
                      <span style="font-family: var(--admin-font-mono); font-size: 0.84rem; color: var(--admin-text-main);">
                        <?= number_format($tm['rows']) ?>
                      </span>
                    </td>
                    <td>
                      <span style="font-size: 0.75rem; color: var(--admin-text-muted); font-family: var(--admin-font-mono);">
                        <?= htmlspecialchars($tm['engine']) ?> &bull; <?= htmlspecialchars($tm['size_formatted']) ?>
                      </span>
                    </td>
                    <td>
                      <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                        <label style="display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.78rem; cursor: pointer; margin: 0; color: var(--admin-text-muted);">
                          <input type="radio" name="seed_mode_<?= htmlspecialchars($tblName) ?>" value="none" checked onchange="setTableSeedMode('<?= htmlspecialchars($tblName) ?>', 'none')" style="accent-color: #94a3b8;">
                          <span>No Seeds</span>
                        </label>
                        <?php if ($tm['rows'] > 0): ?>
                          <label style="display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.78rem; cursor: pointer; margin: 0; color: #10b981;">
                            <input type="radio" name="seed_mode_<?= htmlspecialchars($tblName) ?>" value="all" onchange="setTableSeedMode('<?= htmlspecialchars($tblName) ?>', 'all')" style="accent-color: #10b981;">
                            <span>All (<?= number_format($tm['rows']) ?>)</span>
                          </label>
                          <label style="display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.78rem; cursor: pointer; margin: 0; color: #38bdf8;">
                            <input type="radio" name="seed_mode_<?= htmlspecialchars($tblName) ?>" value="selected" onchange="setTableSeedMode('<?= htmlspecialchars($tblName) ?>', 'selected')" style="accent-color: #38bdf8;">
                            <span>Selective</span>
                          </label>
                          <button type="button" id="btn_pick_<?= htmlspecialchars($tblName) ?>" onclick="openSeedPickerModal('<?= htmlspecialchars($tblName) ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="display: none; font-size: 0.7rem; padding: 0.15rem 0.5rem; color: #f43f5e; border-color: rgba(244,63,94,0.3);">
                            🔍 Pick Rows (<span id="count_label_<?= htmlspecialchars($tblName) ?>">0</span>)
                          </button>
                        <?php else: ?>
                          <span style="font-size: 0.72rem; color: #64748b; font-style: italic;">(Table is empty)</span>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>

      <!-- Floating Sticky Action Dock -->
      <div style="position: sticky; bottom: 1.5rem; z-index: 50; padding: 1.25rem 1.75rem; background: var(--admin-card-bg); border: 1px solid rgba(56,189,248,0.3); border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; backdrop-filter: blur(12px);">
        <div>
          <div style="display: flex; align-items: center; gap: 0.6rem; font-size: 0.95rem; font-weight: 700; color: var(--admin-text-main);">
            <span>🚀 Custom Export Summary:</span>
            <span id="hud_folders_count" style="color: #38bdf8;">0 Folders</span>
            <span>&bull;</span>
            <span id="hud_schemas_count" style="color: #10b981;">0 Schemas</span>
            <span>&bull;</span>
            <span id="hud_seeds_count" style="color: #f43f5e;">0 Seeds</span>
          </div>
          <div style="font-size: 0.78rem; color: var(--admin-text-muted); margin-top: 0.2rem;">
            Ready to generate packaged deployment archive with manifest and custom SQL file.
          </div>
        </div>

        <button type="submit" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #0284c7, #38bdf8); padding: 0.75rem 1.75rem; font-weight: 800; font-size: 1rem; box-shadow: 0 4px 15px rgba(56,189,248,0.35);">
          <i data-lucide="download"></i> Download Customized Archive (.ZIP) ⚡
        </button>
      </div>
    </form>

    <!-- Interactive Seed Row Selection Modal -->
    <div id="seedPickerModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
      <div class="admin-table-wrap" style="width: 100%; max-width: 850px; max-height: 85vh; display: flex; flex-direction: column; padding: 1.75rem; background: var(--admin-card-bg); border: 1px solid rgba(56,189,248,0.4); border-radius: 12px; box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid var(--admin-border-color); padding-bottom: 0.75rem;">
          <div>
            <h3 style="font-size: 1.2rem; color: var(--admin-text-main); margin: 0;" id="seedModalTitle">Select Specific Seed Rows</h3>
            <span style="font-size: 0.78rem; color: var(--admin-text-muted);" id="seedModalSubtitle">Table: <code>users</code></span>
          </div>
          <button type="button" onclick="closeSeedPickerModal()" style="background: none; border: none; color: var(--admin-text-muted); font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; gap: 0.75rem; flex-wrap: wrap;">
          <input type="text" id="seedRowSearchInput" onkeyup="searchSeedRowsDebounced()" placeholder="🔍 Search records..." style="background: var(--admin-input-bg); border: 1px solid var(--admin-border-color); padding: 0.4rem 0.85rem; border-radius: 6px; color: var(--admin-text-main); font-size: 0.82rem; flex: 1; min-width: 200px;">
          <div style="display: flex; gap: 0.4rem;">
            <button type="button" onclick="toggleAllModalRows(true)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem;">Select All Visible</button>
            <button type="button" onclick="toggleAllModalRows(false)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem;">Deselect All Visible</button>
          </div>
        </div>

        <div id="seedModalLoading" style="padding: 2.5rem; text-align: center; color: var(--admin-text-muted); font-size: 0.9rem;">
          Loading records from database...
        </div>

        <div id="seedModalTableContainer" style="overflow-y: auto; flex: 1; border: 1px solid var(--admin-border-color); border-radius: 6px; display: none;">
          <table class="admin-table" id="seedModalRowsTable" style="margin: 0; font-size: 0.8rem;">
            <thead style="position: sticky; top: 0; z-index: 2; background: var(--admin-card-bg);">
              <tr id="seedModalThead"></tr>
            </thead>
            <tbody id="seedModalTbody"></tbody>
          </table>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.25rem; border-top: 1px solid var(--admin-border-color); padding-top: 0.75rem;">
          <span id="seedModalSelectedCount" style="font-size: 0.85rem; color: #38bdf8; font-weight: 700;">0 rows selected</span>
          <div style="display: flex; gap: 0.5rem;">
            <button type="button" onclick="closeSeedPickerModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
            <button type="button" onclick="saveSeedPickerSelection()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #10b981; font-weight: 700;">
              Confirm Selected Seeds ✓
            </button>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

</div>

<style>
.ts-subtab-link {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.75rem 1.25rem;
  color: var(--admin-text-muted, #94a3b8);
  font-weight: 700;
  font-size: 0.88rem;
  text-decoration: none;
  border-bottom: 2px solid transparent;
  transition: all 0.2s ease;
  white-space: nowrap;
}
.ts-subtab-link:hover {
  color: var(--admin-text-main, #f8fafc);
  background: rgba(255,255,255,0.02);
}
.ts-subtab-link.active {
  color: #38bdf8;
  border-bottom-color: #38bdf8;
  background: rgba(56,189,248,0.06);
}
#sqlResultsGrid tbody tr:hover {
  background: rgba(56,189,248,0.04);
}
#sqlResultsGrid td {
  cursor: pointer;
  transition: background 0.15s ease;
}
#sqlResultsGrid td:hover {
  background: rgba(56,189,248,0.12);
}
</style>

<script>
let lastQueryData = null;

function filterDbTables() {
  const q = (document.getElementById('tableFilterInput')?.value || '').toLowerCase();
  const rows = document.querySelectorAll('#dbTablesTable tbody tr');
  rows.forEach(tr => {
    const text = tr.children[0]?.innerText.toLowerCase() || '';
    tr.style.display = text.includes(q) ? '' : 'none';
  });
}

function setQuery(sql) {
  const textarea = document.getElementById('sqlQueryInput');
  if (textarea) {
    textarea.value = sql;
    textarea.focus();
    // highlight and pulse
    textarea.style.boxShadow = '0 0 15px rgba(56,189,248,0.4)';
    setTimeout(() => { textarea.style.boxShadow = 'none'; }, 600);
  }
}

function clearQueryInput() {
  const textarea = document.getElementById('sqlQueryInput');
  if (textarea) {
    textarea.value = '';
    textarea.focus();
  }
}

function formatSqlInInput() {
  const textarea = document.getElementById('sqlQueryInput');
  if (!textarea || !textarea.value.trim()) return;
  let sql = textarea.value.trim();
  const keywords = ['SELECT', 'FROM', 'WHERE', 'LEFT JOIN', 'RIGHT JOIN', 'INNER JOIN', 'JOIN', 'ORDER BY', 'GROUP BY', 'HAVING', 'LIMIT', 'OFFSET', 'UNION', 'AND', 'OR'];
  keywords.forEach(kw => {
    const regex = new RegExp('\\b' + kw + '\\b', 'gi');
    sql = sql.replace(regex, kw);
  });
  textarea.value = sql;
}

// Keyboard shortcut: Ctrl+Enter or Cmd+Enter to execute SQL
document.addEventListener('DOMContentLoaded', () => {
  const textarea = document.getElementById('sqlQueryInput');
  if (textarea) {
    textarea.addEventListener('keydown', (e) => {
      if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        e.preventDefault();
        const form = document.getElementById('dbQueryForm');
        if (form) {
          handleQuerySubmit(new Event('submit', { cancelable: true }));
        }
      }
    });
  }
});

// AJAX SQL Execution
async function handleQuerySubmit(e) {
  if (e && e.preventDefault) e.preventDefault();
  const textarea = document.getElementById('sqlQueryInput');
  const sql = textarea ? textarea.value.trim() : '';
  const errorBox = document.getElementById('liveQueryErrorBox');
  const resultsContainer = document.getElementById('queryResultsContainer');
  const btn = document.getElementById('btnExecuteSql');
  const btnIcon = document.getElementById('btnExecuteIcon');
  const btnText = document.getElementById('btnExecuteText');

  if (errorBox) {
    errorBox.style.display = 'none';
    errorBox.innerHTML = '';
  }

  if (!sql) {
    if (errorBox) {
      errorBox.style.display = 'block';
      errorBox.innerHTML = '<strong>⚠️ Error:</strong> Please enter a SQL query to execute.';
    }
    return false;
  }

  // Set loading state
  if (btn) btn.disabled = true;
  if (btnIcon) btnIcon.innerHTML = '⏳';
  if (btnText) btnText.innerHTML = 'Executing Query...';

  try {
    const formData = new FormData();
    formData.append('sql_query', sql);
    formData.append('format', 'json');

    const res = await fetch('<?= url("/admin/db-manager/query") ?>', {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    });

    const json = await res.json();
    if (!res.ok || !json.success) {
      throw new Error(json.message || 'Execution failed with status ' + res.status);
    }

    lastQueryData = json;
    renderLiveResults(json);
  } catch (err) {
    if (errorBox) {
      errorBox.style.display = 'block';
      errorBox.innerHTML = '<strong>⚠️ Execution Error:</strong> ' + escapeHtml(err.message);
    }
  } finally {
    if (btn) btn.disabled = false;
    if (btnIcon) btnIcon.innerHTML = '▶';
    if (btnText) btnText.innerHTML = 'Execute SQL Query';
  }

  return false;
}

function renderLiveResults(data) {
  const container = document.getElementById('queryResultsContainer');
  if (!container) return;

  const rows = data.rows || [];
  const cols = data.columns || [];
  const duration = data.duration_ms || 0;
  const total = data.total_count || rows.length;

  if (rows.length === 0) {
    container.innerHTML = `
      <div class="audit-inspector-card" style="padding: 1.25rem; border-color: rgba(56,189,248,0.25);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
          <div style="font-size: 0.88rem; color: var(--admin-text-main); font-family: var(--admin-font-mono);">
            <span style="color: #34d399; font-weight: 800;">✓ Query executed:</span> 
            <strong>0</strong> rows returned in <strong style="color: #38bdf8;">${duration} ms</strong>
          </div>
        </div>
        <div style="color: var(--admin-text-muted); font-size: 0.85rem; padding: 1.5rem; text-align: center; background: var(--admin-card-bg, #05070b); border-radius: 8px;">
          Empty set returned (0 rows).
        </div>
      </div>
    `;
    return;
  }

  let headHtml = '<th style="width: 45px; text-align: center; color: var(--admin-text-muted); font-family: var(--admin-font-mono);">#</th>';
  cols.forEach(c => {
    headHtml += `<th style="font-family: var(--admin-font-mono); color: #38bdf8; white-space: nowrap;">${escapeHtml(c)}</th>`;
  });

  let bodyHtml = '';
  rows.forEach((r, idx) => {
    bodyHtml += '<tr>';
    bodyHtml += `<td style="text-align: center; color: var(--admin-text-muted); font-family: var(--admin-font-mono); font-size: 0.75rem;">${idx + 1}</td>`;
    cols.forEach(c => {
      const val = r[c];
      if (val === null || val === undefined) {
        bodyHtml += `<td style="font-family: var(--admin-font-mono); color: var(--admin-text-main); word-break: break-all; max-width: 320px;" title="Click to copy value" onclick="copyCellValue(this)"><span style="color: #64748b; font-style: italic;">NULL</span></td>`;
      } else {
        bodyHtml += `<td style="font-family: var(--admin-font-mono); color: var(--admin-text-main); word-break: break-all; max-width: 320px;" title="Click to copy value" onclick="copyCellValue(this)">${escapeHtml(String(val))}</td>`;
      }
    });
    bodyHtml += '</tr>';
  });

  container.innerHTML = `
    <div class="audit-inspector-card" style="padding: 1.25rem; border-color: rgba(56,189,248,0.25);">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
        <div style="font-size: 0.88rem; color: var(--admin-text-main); font-family: var(--admin-font-mono);">
          <span style="color: #34d399; font-weight: 800;">✓ Query executed:</span> 
          <strong>${Number(total).toLocaleString()}</strong> row(s) returned in <strong style="color: #38bdf8;">${duration} ms</strong>
          ${total > rows.length ? `<span style="color: var(--admin-text-muted); font-size: 0.75rem;">(previewing first ${rows.length} rows)</span>` : ''}
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
          <input type="text" id="queryGridFilter" onkeyup="filterQueryResults()" placeholder="🔍 Filter results..." style="background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.2)); padding: 0.35rem 0.65rem; border-radius: 6px; color: var(--admin-text-main); font-size: 0.78rem; width: 170px;">
          <button type="button" onclick="exportQueryResultsCsv()" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.35rem 0.65rem;">
            📥 CSV
          </button>
          <button type="button" onclick="exportQueryResultsJson()" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.35rem 0.65rem;">
            📋 JSON
          </button>
        </div>
      </div>

      <div style="overflow-x: auto; max-height: 520px; border-radius: 8px; border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15));">
        <table class="admin-table" id="sqlResultsGrid" style="margin: 0; font-size: 0.8rem;">
          <thead style="position: sticky; top: 0; z-index: 5;">
            <tr>${headHtml}</tr>
          </thead>
          <tbody>${bodyHtml}</tbody>
        </table>
      </div>
    </div>
  `;
}

function filterQueryResults() {
  const q = (document.getElementById('queryGridFilter')?.value || '').toLowerCase();
  const rows = document.querySelectorAll('#sqlResultsGrid tbody tr');
  rows.forEach(tr => {
    const text = tr.innerText.toLowerCase();
    tr.style.display = text.includes(q) ? '' : 'none';
  });
}

function copyCellValue(td) {
  const text = td.innerText.trim();
  navigator.clipboard.writeText(text).then(() => {
    const orig = td.style.background;
    td.style.background = 'rgba(56,189,248,0.3)';
    setTimeout(() => { td.style.background = orig; }, 300);
  });
}

function exportQueryResultsCsv() {
  const table = document.getElementById('sqlResultsGrid');
  if (!table) return;
  const rows = Array.from(table.querySelectorAll('tr'));
  const csv = rows.map(row => {
    const cols = Array.from(row.querySelectorAll('th, td'));
    return cols.map(c => `"${c.innerText.replace(/"/g, '""')}"`).join(',');
  }).join('\n');

  const blob = new Blob([csv], { type: 'text/csv' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `query_result_${Date.now()}.csv`;
  a.click();
  URL.revokeObjectURL(url);
}

function exportQueryResultsJson() {
  const table = document.getElementById('sqlResultsGrid');
  if (!table) return;
  const headerCells = Array.from(table.querySelectorAll('thead th')).slice(1);
  const headers = headerCells.map(th => th.innerText.trim());
  const bodyRows = Array.from(table.querySelectorAll('tbody tr'));
  const data = bodyRows.map(tr => {
    const obj = {};
    const cells = Array.from(tr.querySelectorAll('td')).slice(1);
    cells.forEach((td, i) => {
      obj[headers[i] || `col_${i}`] = td.innerText.trim();
    });
    return obj;
  });

  const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `query_result_${Date.now()}.json`;
  a.click();
  URL.revokeObjectURL(url);
}

function escapeHtml(str) {
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function quickSelectTable(tableName) {
  const tabQueryUrl = '<?= url("/admin/db-manager?tab=query") ?>';
  if (window.location.href.indexOf('tab=query') !== -1) {
    setQuery(`SELECT * FROM \`${tableName}\` ORDER BY id DESC LIMIT 25;`);
  } else {
    window.location.href = tabQueryUrl;
  }
}

function copyAlterStatements() {
  const pre = document.getElementById('alterStatementsPre');
  if (!pre) return;
  navigator.clipboard.writeText(pre.innerText).then(() => {
    alert('SQL ALTER Statements copied to clipboard! Paste into phpMyAdmin or MySQL terminal.');
  });
}

/* ========================================================================== */
/* CUSTOM EXPORT STUDIO JAVASCRIPT STATE ENGINE                               */
/* ========================================================================== */
const exportState = {
  tables: {},
  activeModalTable: null,
  activeModalRows: [],
  selectedIdsTemp: new Set()
};

function initExportState() {
  document.querySelectorAll('.export-table-row').forEach(tr => {
    const tbl = tr.getAttribute('data-table');
    const hasSchema = document.getElementById('schema_' + tbl)?.checked ?? true;
    exportState.tables[tbl] = {
      include_schema: hasSchema,
      seed_mode: 'none',
      selected_ids: []
    };
  });
  updateExportSummary();
}

function updateTableConfig(tbl) {
  if (!exportState.tables[tbl]) {
    exportState.tables[tbl] = { include_schema: true, seed_mode: 'none', selected_ids: [] };
  }
  exportState.tables[tbl].include_schema = !!document.getElementById('schema_' + tbl)?.checked;
  updateExportSummary();
}

function setTableSeedMode(tbl, mode) {
  if (!exportState.tables[tbl]) {
    exportState.tables[tbl] = { include_schema: true, seed_mode: 'none', selected_ids: [] };
  }
  exportState.tables[tbl].seed_mode = mode;
  const btn = document.getElementById('btn_pick_' + tbl);
  if (btn) {
    btn.style.display = (mode === 'selected') ? 'inline-flex' : 'none';
  }
  if (mode === 'selected' && (!exportState.tables[tbl].selected_ids || !exportState.tables[tbl].selected_ids.length)) {
    openSeedPickerModal(tbl);
  }
  updateExportSummary();
}

function toggleAllFolders(checked) {
  document.querySelectorAll('.folder-checkbox').forEach(cb => cb.checked = checked);
  updateExportSummary();
}

function setAllSchemas(checked) {
  document.querySelectorAll('.table-schema-checkbox').forEach(cb => {
    cb.checked = checked;
    const tbl = cb.id.replace('schema_', '');
    if (exportState.tables[tbl]) exportState.tables[tbl].include_schema = checked;
  });
  updateExportSummary();
}

function setAllSeedsMode(mode) {
  Object.keys(exportState.tables).forEach(tbl => {
    const radio = document.querySelector(`input[name="seed_mode_${tbl}"][value="${mode}"]`);
    if (radio) {
      radio.checked = true;
      setTableSeedMode(tbl, mode);
    }
  });
  updateExportSummary();
}

function selectPreset(preset) {
  if (preset === 'recommended') {
    const core = ['app', 'views', 'assets', 'database', 'public'];
    document.querySelectorAll('.folder-checkbox').forEach(cb => {
      cb.checked = core.includes(cb.value);
    });
    const rootCb = document.querySelector('input[name="include_root_files"]');
    if (rootCb) rootCb.checked = true;
    setAllSchemas(true);
    setAllSeedsMode('none');
    ['site_settings', 'categories'].forEach(tbl => {
      const radio = document.querySelector(`input[name="seed_mode_${tbl}"][value="all"]`);
      if (radio) {
        radio.checked = true;
        setTableSeedMode(tbl, 'all');
      }
    });
  } else if (preset === 'all') {
    toggleAllFolders(true);
    const rootCb = document.querySelector('input[name="include_root_files"]');
    if (rootCb) rootCb.checked = true;
    setAllSchemas(true);
    setAllSeedsMode('all');
  } else if (preset === 'reset') {
    toggleAllFolders(false);
    const rootCb = document.querySelector('input[name="include_root_files"]');
    if (rootCb) rootCb.checked = false;
    setAllSchemas(false);
    setAllSeedsMode('none');
  }
  updateExportSummary();
}

function filterExportTables() {
  const q = (document.getElementById('exportTableSearch')?.value || '').toLowerCase();
  document.querySelectorAll('.export-table-row').forEach(tr => {
    const name = (tr.getAttribute('data-table') || '').toLowerCase();
    tr.style.display = name.includes(q) ? '' : 'none';
  });
}

function updateExportSummary() {
  const foldersChecked = document.querySelectorAll('.folder-checkbox:checked').length;
  const rootChecked = document.querySelector('input[name="include_root_files"]')?.checked ? 1 : 0;
  const totalFoldersLabel = `${foldersChecked} folder${foldersChecked !== 1 ? 's' : ''}` + (rootChecked ? ' + root files' : '');
  
  const selectedFoldersBadge = document.getElementById('selected_folders_count_badge');
  if (selectedFoldersBadge) selectedFoldersBadge.innerText = totalFoldersLabel;
  const hudFolders = document.getElementById('hud_folders_count');
  if (hudFolders) hudFolders.innerText = `${foldersChecked + rootChecked} Dirs/Files`;

  let schemasCount = 0;
  let seedsCount = 0;
  Object.keys(exportState.tables).forEach(tbl => {
    const t = exportState.tables[tbl];
    if (t.include_schema) schemasCount++;
    if (t.seed_mode === 'all') {
      const rowText = document.querySelector(`tr[data-table="${tbl}"] td:nth-child(3)`)?.innerText.replace(/,/g, '') || '0';
      seedsCount += parseInt(rowText, 10) || 0;
    } else if (t.seed_mode === 'selected') {
      seedsCount += (t.selected_ids ? t.selected_ids.length : 0);
    }
    const countLabel = document.getElementById('count_label_' + tbl);
    if (countLabel) countLabel.innerText = (t.selected_ids ? t.selected_ids.length : 0);
  });

  const schemasBadge = document.getElementById('selected_schemas_count_badge');
  if (schemasBadge) schemasBadge.innerText = `${schemasCount} schemas`;
  const hudSchemas = document.getElementById('hud_schemas_count');
  if (hudSchemas) hudSchemas.innerText = `${schemasCount} Schemas`;

  const seedsBadge = document.getElementById('selected_seeds_count_badge');
  if (seedsBadge) seedsBadge.innerText = `${seedsCount} seeds`;
  const hudSeeds = document.getElementById('hud_seeds_count');
  if (hudSeeds) hudSeeds.innerText = `${seedsCount} Seeds`;

  const jsonField = document.getElementById('tables_config_json');
  if (jsonField) jsonField.value = JSON.stringify(exportState.tables);
}

/* Modal Granular Row Selector */
let searchDebounceTimer = null;
function searchSeedRowsDebounced() {
  clearTimeout(searchDebounceTimer);
  searchDebounceTimer = setTimeout(() => {
    const q = document.getElementById('seedRowSearchInput')?.value || '';
    if (exportState.activeModalTable) {
      loadTableRows(exportState.activeModalTable, q);
    }
  }, 300);
}

function openSeedPickerModal(tableName) {
  exportState.activeModalTable = tableName;
  const modal = document.getElementById('seedPickerModal');
  if (!modal) return;
  modal.style.display = 'flex';
  document.getElementById('seedModalTitle').innerText = `Select Seed Rows from \`${tableName}\``;
  document.getElementById('seedModalSubtitle').innerHTML = `Table: <code>${tableName}</code> &bull; Checked rows will be compiled into INSERT statements`;
  document.getElementById('seedRowSearchInput').value = '';

  exportState.selectedIdsTemp = new Set(exportState.tables[tableName]?.selected_ids || []);
  updateModalSelectedCounter();
  loadTableRows(tableName, '');
}

function closeSeedPickerModal() {
  const modal = document.getElementById('seedPickerModal');
  if (modal) modal.style.display = 'none';
  exportState.activeModalTable = null;
}

function loadTableRows(tableName, search) {
  const loader = document.getElementById('seedModalLoading');
  const container = document.getElementById('seedModalTableContainer');
  if (loader) loader.style.display = 'block';
  if (container) container.style.display = 'none';

  const url = `<?= url("/admin/db-manager/table-rows") ?>?table=${encodeURIComponent(tableName)}&search=${encodeURIComponent(search)}&limit=100`;
  fetch(url, { credentials: 'same-origin' })
    .then(r => r.json())
    .then(res => {
      if (loader) loader.style.display = 'none';
      if (!res.success || !res.data) {
        if (loader) {
          loader.innerText = 'Failed to load records.';
          loader.style.display = 'block';
        }
        return;
      }
      exportState.activeModalRows = res.data.rows || [];
      renderSeedModalRows(res.data);
      if (container) container.style.display = 'block';
    })
    .catch(err => {
      if (loader) {
        loader.innerText = 'Network error loading records.';
        loader.style.display = 'block';
      }
    });
}

function renderSeedModalRows(data) {
  const thead = document.getElementById('seedModalThead');
  const tbody = document.getElementById('seedModalTbody');
  if (!thead || !tbody) return;

  const pk = data.pk || 'id';
  const previewCols = data.preview_columns || [];

  let thHtml = `<th style="width: 40px; text-align: center;"><input type="checkbox" id="modalSelectAllHeader" onchange="toggleAllModalRows(this.checked)" style="width: 15px; height: 15px; accent-color: #38bdf8;"></th>`;
  thHtml += `<th style="width: 80px;">${escapeHtml(pk)}</th>`;
  previewCols.forEach(c => {
    if (c !== pk) thHtml += `<th>${escapeHtml(c)}</th>`;
  });
  thead.innerHTML = thHtml;

  if (!data.rows || !data.rows.length) {
    tbody.innerHTML = `<tr><td colspan="${previewCols.length + 2}" style="text-align: center; color: var(--admin-text-muted); padding: 2rem;">No matching rows found in table.</td></tr>`;
    return;
  }

  let trHtml = '';
  data.rows.forEach(r => {
    const rowId = String(r[pk] ?? '');
    const isChecked = exportState.selectedIdsTemp.has(rowId);
    trHtml += `<tr>`;
    trHtml += `<td style="text-align: center;"><input type="checkbox" value="${escapeHtml(rowId)}" class="seed-row-cb" ${isChecked ? 'checked' : ''} onchange="toggleModalRowId('${escapeHtml(rowId)}', this.checked)" style="width: 15px; height: 15px; accent-color: #38bdf8;"></td>`;
    trHtml += `<td style="font-family: var(--admin-font-mono); font-weight: 700; color: #38bdf8;">${escapeHtml(rowId)}</td>`;
    previewCols.forEach(c => {
      if (c !== pk) {
        const val = r[c] !== null ? String(r[c]) : '<span style="color: #64748b; font-style: italic;">NULL</span>';
        const displayVal = val.length > 50 ? escapeHtml(val.substring(0, 50)) + '...' : escapeHtml(val);
        trHtml += `<td>${displayVal}</td>`;
      }
    });
    trHtml += `</tr>`;
  });
  tbody.innerHTML = trHtml;
}

function toggleModalRowId(rowId, isChecked) {
  if (isChecked) {
    exportState.selectedIdsTemp.add(rowId);
  } else {
    exportState.selectedIdsTemp.delete(rowId);
  }
  updateModalSelectedCounter();
}

function toggleAllModalRows(checked) {
  document.querySelectorAll('.seed-row-cb').forEach(cb => {
    cb.checked = checked;
    const rowId = cb.value;
    if (checked) exportState.selectedIdsTemp.add(rowId);
    else exportState.selectedIdsTemp.delete(rowId);
  });
  updateModalSelectedCounter();
}

function updateModalSelectedCounter() {
  const counter = document.getElementById('seedModalSelectedCount');
  if (counter) counter.innerText = `${exportState.selectedIdsTemp.size} row(s) selected`;
}

function saveSeedPickerSelection() {
  if (!exportState.activeModalTable) return;
  const tbl = exportState.activeModalTable;
  exportState.tables[tbl].selected_ids = Array.from(exportState.selectedIdsTemp);
  exportState.tables[tbl].seed_mode = exportState.tables[tbl].selected_ids.length > 0 ? 'selected' : 'none';
  
  const radio = document.querySelector(`input[name="seed_mode_${tbl}"][value="${exportState.tables[tbl].seed_mode}"]`);
  if (radio) radio.checked = true;

  closeSeedPickerModal();
  updateExportSummary();
}

// Auto-initialize export state on page load if tab is active
document.addEventListener('DOMContentLoaded', () => {
  if (window.location.search.includes('tab=custom_export')) {
    initExportState();
  }
});
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
