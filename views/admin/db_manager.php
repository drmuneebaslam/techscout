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
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
