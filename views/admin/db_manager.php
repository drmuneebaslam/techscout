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
  <!-- TAB 1: TABLES BREAKDOWN -->
  <!-- ========================================================================= -->
  <?php if ($activeTab === 'tables'): ?>
    <div class="admin-table-wrap" style="padding: 1.5rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
        <div>
          <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 0.25rem 0;">Schema Tables Breakdown &amp; Footprint</h3>
          <p style="color: #64748b; font-size: 0.82rem; margin: 0;">Database: <code style="color: #38bdf8;"><?= htmlspecialchars($dbName) ?></code> &bull; Server Engine: <code style="color: #a855f7;"><?= htmlspecialchars($mysqlVersion) ?></code></p>
        </div>
        <div style="min-width: 260px;">
          <input type="text" id="tableFilterInput" onkeyup="filterDbTables()" placeholder="🔍 Fast search table name..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.45rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.85rem;">
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
              <th style="width: 220px; text-align: right;">Operations</th>
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
                  <strong style="color: #f8fafc; font-family: var(--admin-font-mono); font-size: 0.92rem;"><?= htmlspecialchars($tName) ?></strong>
                  <?php if (in_array($tName, ['users', 'system_error_logs', 'audit_logs', 'contents'], true)): ?>
                    <span class="ts-badge ts-badge-indigo" style="font-size: 0.6rem; margin-left: 0.35rem;">CORE</span>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="ts-badge ts-badge-cyan" style="font-size: 0.7rem; font-family: var(--admin-font-mono);"><?= htmlspecialchars($t['ENGINE'] ?? 'InnoDB') ?></span>
                </td>
                <td>
                  <span style="color: #cbd5e1; font-family: var(--admin-font-mono); font-size: 0.88rem;"><?= number_format((int)$t['TABLE_ROWS']) ?></span>
                </td>
                <td style="font-family: var(--admin-font-mono); font-size: 0.82rem; color: #94a3b8;">
                  <?= round((int)$t['DATA_LENGTH'] / 1024, 2) ?> KB
                </td>
                <td style="font-family: var(--admin-font-mono); font-size: 0.82rem; color: #94a3b8;">
                  <?= round((int)$t['INDEX_LENGTH'] / 1024, 2) ?> KB
                </td>
                <td>
                  <strong style="color: <?= $isBig ? '#f59e0b' : '#38bdf8' ?>; font-family: var(--admin-font-mono); font-size: 0.88rem;">
                    <?= $isBig ? round($totalKb / 1024, 2) . ' MB' : $totalKb . ' KB' ?>
                  </strong>
                </td>
                <td style="text-align: right;">
                  <div style="display: inline-flex; gap: 0.35rem; justify-content: flex-end;">
                    <form action="<?= url('/admin/db-manager/optimize-table/' . $tName) ?>" method="POST" style="margin: 0;">
                      <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem; color: #34d399; border-color: rgba(16,185,129,0.3);" title="Optimize Table & Defragment">
                        ⚡ Opt
                      </button>
                    </form>
                    <form action="<?= url('/admin/db-manager/check-table/' . $tName) ?>" method="POST" style="margin: 0;">
                      <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem; color: #38bdf8; border-color: rgba(56,189,248,0.3);" title="Run CHECK TABLE">
                        🔍 Check
                      </button>
                    </form>
                    <form action="<?= url('/admin/db-manager/repair-table/' . $tName) ?>" method="POST" style="margin: 0;">
                      <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem; color: #f59e0b; border-color: rgba(245,158,11,0.3);" title="Run REPAIR TABLE">
                        🔧 Repair
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
          <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 0.25rem 0;">💾 Disaster Recovery &amp; Automated SQL Snapshots</h3>
          <p style="color: #64748b; font-size: 0.82rem; margin: 0;">Zero-downtime full schema dumps, indices and transaction row data</p>
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
                  <div style="font-size: 0.72rem; color: #64748b; font-family: var(--admin-font-mono);">SHA256: <?= substr($b['checksum'] ?? '', 0, 16) ?>...</div>
                </td>
                <td>
                  <span class="ts-badge ts-badge-cyan" style="font-family: var(--admin-font-mono);"><?= htmlspecialchars($b['file_size_formatted']) ?></span>
                </td>
                <td style="font-size: 0.82rem; color: #cbd5e1;">
                  <?= (int)$b['table_count'] ?> tables &bull; <?= number_format((int)$b['total_rows']) ?> rows
                </td>
                <td style="font-size: 0.82rem; color: #94a3b8;">
                  <?= htmlspecialchars($b['creator_name'] ?? 'System / Admin') ?>
                </td>
                <td style="font-size: 0.78rem; color: #64748b; font-family: var(--admin-font-mono); white-space: nowrap;">
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
              <td colspan="6" style="text-align: center; padding: 2.5rem; color: #64748b;">
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
          <h3 style="font-size: 1.2rem; color: #f8fafc; margin: 0 0 0.25rem 0;">🩺 Schema Health &amp; Missing Column Diagnostics</h3>
          <p style="color: #64748b; font-size: 0.85rem; margin: 0;">Validates live MySQL table structures against application model requirements</p>
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
          <div style="background: #05070b; border: 1px solid <?= $isHealthy ? 'rgba(16,185,129,0.3)' : ($isCritical ? 'rgba(239,68,68,0.4)' : 'rgba(245,158,11,0.4)') ?>; border-radius: 10px; padding: 1.25rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
              <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                <span style="font-size: 1.1rem;"><?= $isHealthy ? '✅' : ($isCritical ? '❌' : '⚠️') ?></span>
                <strong style="color: #f8fafc; font-size: 0.95rem;"><?= htmlspecialchars($check['name']) ?></strong>
                <span class="ts-badge <?= $isHealthy ? 'ts-badge-green' : ($isCritical ? 'ts-badge-red' : 'ts-badge-amber') ?>" style="font-size: 0.65rem;">
                  <?= strtoupper($check['status']) ?>
                </span>
              </div>
              <div style="color: #94a3b8; font-size: 0.82rem; margin-left: 1.7rem;">
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
      <div style="background: #05070b; border: 1px solid rgba(99,102,241,0.3); border-radius: 10px; padding: 1.25rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
          <h4 style="color: #f8fafc; margin: 0; font-size: 0.95rem;">Manual SQL Upgrade / ALTER Statements (For phpMyAdmin / Terminal)</h4>
          <button type="button" onclick="copyAlterStatements()" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem; color: #38bdf8;">
            📋 Copy SQL Statements
          </button>
        </div>
        <pre id="alterStatementsPre" style="background: #020408; border: 1px solid rgba(148,163,184,0.15); border-radius: 6px; padding: 1rem; color: #cbd5e1; font-family: var(--admin-font-mono); font-size: 0.78rem; line-height: 1.6; margin: 0; white-space: pre-wrap;">-- 1. Upgrade Users Table for 6-Digit Email Verification OTP PIN
ALTER TABLE `users` 
  MODIFY COLUMN `status` ENUM('pending_verification','unverified','active','suspended','banned','deleted') NOT NULL DEFAULT 'pending_verification',
  ADD COLUMN `verification_code` VARCHAR(20) DEFAULT NULL AFTER `suspended_until`;

-- 2. Upgrade system_error_logs for Deep Crash Forensics & Telemetry
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
  <!-- TAB 4: SQL MAINTENANCE TERMINAL -->
  <!-- ========================================================================= -->
  <?php elseif ($activeTab === 'query'): ?>
    <div class="admin-table-wrap" style="padding: 1.75rem;">
      <h3 style="font-size: 1.2rem; color: #f8fafc; margin: 0 0 0.35rem 0;">⚡ SQL Diagnostic &amp; Read-Only Terminal</h3>
      <p style="color: #64748b; font-size: 0.85rem; margin: 0 0 1.25rem 0;">
        Execute safe diagnostic queries (<code style="color: #38bdf8;">SELECT</code>, <code style="color: #38bdf8;">SHOW</code>, <code style="color: #38bdf8;">EXPLAIN</code>, <code style="color: #38bdf8;">DESCRIBE</code>, <code style="color: #38bdf8;">CHECK</code>) directly from the browser.
      </p>

      <!-- Quick Query Buttons -->
      <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1rem;">
        <button type="button" onclick="setQuery('SHOW TABLE STATUS')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem;">Table Status</button>
        <button type="button" onclick="setQuery('SHOW PROCESSLIST')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem;">Active Threads</button>
        <button type="button" onclick="setQuery('SELECT id, email, username, role, status, verification_code FROM users ORDER BY id DESC LIMIT 10')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem;">Recent Users</button>
        <button type="button" onclick="setQuery('SELECT id, error_type, message, file, line, created_at FROM system_error_logs ORDER BY id DESC LIMIT 10')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem;">Recent Errors</button>
        <button type="button" onclick="setQuery('SHOW VARIABLES LIKE \'%timeout%\'')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem;">Timeout Config</button>
      </div>

      <form action="<?= url('/admin/db-manager/query') ?>" method="POST" style="margin-bottom: 1.5rem;">
        <textarea name="sql_query" id="sqlQueryInput" rows="4" placeholder="SELECT * FROM users LIMIT 10;" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 8px; padding: 1rem; color: #38bdf8; font-family: var(--admin-font-mono); font-size: 0.85rem; line-height: 1.5;"><?= htmlspecialchars(\App\Core\Session::getFlash('query_results')['sql'] ?? '') ?></textarea>
        <div style="margin-top: 0.75rem; display: flex; justify-content: flex-end;">
          <button type="submit" class="ts-btn ts-btn-primary" style="padding: 0.5rem 1.75rem; font-weight: 700;">
            ▶ Execute Diagnostic Query
          </button>
        </div>
      </form>

      <!-- Query Results -->
      <?php 
        $queryFlash = \App\Core\Session::getFlash('query_results');
        if (!empty($queryFlash)):
          $rows = $queryFlash['rows'] ?? [];
          $durationMs = $queryFlash['duration_ms'] ?? 0;
          $totalCount = $queryFlash['total_count'] ?? count($rows);
      ?>
        <div style="background: #05070b; border: 1px solid rgba(56,189,248,0.25); border-radius: 8px; padding: 1.25rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <div style="font-size: 0.85rem; color: #cbd5e1; font-family: var(--admin-font-mono);">
              <span style="color: #34d399; font-weight: 700;">✓ Query executed:</span> <?= $totalCount ?> row(s) returned in <strong><?= $durationMs ?> ms</strong>
            </div>
          </div>

          <?php if (!empty($rows)): ?>
            <div style="overflow-x: auto; max-height: 480px;">
              <table class="admin-table" style="margin: 0; font-size: 0.8rem;">
                <thead>
                  <tr>
                    <?php foreach (array_keys($rows[0]) as $col): ?>
                      <th style="font-family: var(--admin-font-mono); color: #38bdf8;"><?= htmlspecialchars((string)$col) ?></th>
                    <?php endforeach; ?>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($rows as $r): ?>
                    <tr>
                      <?php foreach ($r as $val): ?>
                        <td style="font-family: var(--admin-font-mono); color: #e2e8f0; word-break: break-all;">
                          <?= htmlspecialchars((string)$val) ?>
                        </td>
                      <?php endforeach; ?>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div style="color: #64748b; font-size: 0.85rem; padding: 1rem; text-align: center;">Empty set returned (0 rows).</div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

</div>

<style>
.ts-subtab-link {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.75rem 1.25rem;
  color: #94a3b8;
  font-weight: 700;
  font-size: 0.88rem;
  text-decoration: none;
  border-bottom: 2px solid transparent;
  transition: all 0.2s ease;
  white-space: nowrap;
}
.ts-subtab-link:hover {
  color: #f8fafc;
  background: rgba(255,255,255,0.02);
}
.ts-subtab-link.active {
  color: #38bdf8;
  border-bottom-color: #38bdf8;
  background: rgba(56,189,248,0.06);
}
</style>

<script>
function filterDbTables() {
  const q = document.getElementById('tableFilterInput').value.toLowerCase();
  const rows = document.querySelectorAll('#dbTablesTable tbody tr');
  rows.forEach(tr => {
    const text = tr.children[0].innerText.toLowerCase();
    tr.style.display = text.includes(q) ? '' : 'none';
  });
}

function setQuery(sql) {
  const textarea = document.getElementById('sqlQueryInput');
  if (textarea) {
    textarea.value = sql;
    textarea.focus();
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
