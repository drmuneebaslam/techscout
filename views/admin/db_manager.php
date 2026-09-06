<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-main">
  <!-- Topbar -->
  <div class="admin-topbar">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-cyan">STORAGE TELEMETRY &bull; INNODB OPTIMIZER</span>
        <span style="font-size: 0.8rem; color: #64748b;">Table Size Breakdown, Index Statistics &amp; Optimization</span>
      </div>
      <h1 style="font-size: 1.6rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        Database Health &amp; Schema Inspector
      </h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <form action="<?= url('/admin/db-manager/backup') ?>" method="POST">
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #6366f1, #38bdf8); font-weight: 700;">
          <i data-lucide="database"></i> Generate SQL Snapshot Now ⚡
        </button>
      </form>
      <form action="<?= url('/admin/db-manager/optimize') ?>" method="POST" onsubmit="return confirm('Recalculate indices and optimize all database tables?');">
        <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm">
          <i data-lucide="zap"></i> Optimize Tables
        </button>
      </form>
    </div>
  </div>

  <!-- Metric Stat Cards -->
  <div class="admin-stat-grid">
    <div class="admin-stat-card">
      <div class="admin-stat-label">Total Database Size</div>
      <div class="admin-stat-val" style="color: #38bdf8; font-family: monospace;">
        <?= round($totalDbSize / (1024 * 1024), 2) ?> MB
      </div>
      <div style="font-size: 0.8rem; color: #94a3b8;">Data + Index Footprint</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Active Schema Tables</div>
      <div class="admin-stat-val" style="color: #a855f7;"><?= count($tables) ?></div>
      <div style="font-size: 0.8rem; color: #c084fc;">InnoDB relational tables</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Total Relational Rows</div>
      <div class="admin-stat-val" style="color: #10b981;"><?= number_format($totalRows) ?></div>
      <div style="font-size: 0.8rem; color: #34d399;">Entities indexed</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Database Target</div>
      <div class="admin-stat-val" style="color: #ec4899; font-size: 1.3rem; font-family: monospace;">
        <?= htmlspecialchars($dbName) ?>
      </div>
      <div style="font-size: 0.8rem; color: #f472b6;">MySQL / MariaDB Core</div>
    </div>
  </div>

  <!-- Tables Detailed Breakdown -->
  <div class="admin-table-wrap" style="padding: 1.5rem;">
    <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 1rem 0;">Schema Tables &amp; Allocation</h3>

    <table class="admin-table">
      <thead>
        <tr>
          <th>Table Name</th>
          <th>Storage Engine</th>
          <th>Estimated Rows</th>
          <th>Data Size</th>
          <th>Index Size</th>
          <th>Total Footprint</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($tables as $t): ?>
          <tr>
            <td>
              <strong style="color: #f8fafc; font-family: monospace; font-size: 0.92rem;"><?= htmlspecialchars($t['TABLE_NAME']) ?></strong>
            </td>
            <td>
              <span class="ts-badge ts-badge-cyan" style="font-size: 0.7rem; font-family: monospace;"><?= htmlspecialchars($t['ENGINE'] ?? 'InnoDB') ?></span>
            </td>
            <td>
              <span style="color: #cbd5e1; font-family: monospace; font-size: 0.88rem;"><?= number_format((int)$t['TABLE_ROWS']) ?></span>
            </td>
            <td style="font-family: monospace; font-size: 0.82rem; color: #94a3b8;">
              <?= round((int)$t['DATA_LENGTH'] / 1024, 2) ?> KB
            </td>
            <td style="font-family: monospace; font-size: 0.82rem; color: #94a3b8;">
              <?= round((int)$t['INDEX_LENGTH'] / 1024, 2) ?> KB
            </td>
            <td>
              <strong style="color: #38bdf8; font-family: monospace; font-size: 0.88rem;">
                <?= round((int)$t['TOTAL_SIZE'] / 1024, 2) ?> KB
              </strong>
            </td>
            <td>
              <span class="ts-badge ts-badge-green" style="font-size: 0.68rem;">HEALTHY</span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <?php
    $backups = \App\Models\DatabaseBackupManager::getBackups();
  ?>
  <!-- Disaster Recovery Snapshots Section -->
  <div class="admin-table-wrap" style="padding: 1.5rem; margin-top: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
      <div>
        <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 0.25rem 0;">💾 Disaster Recovery &amp; Automated SQL Snapshots</h3>
        <p style="font-size: 0.8rem; color: #94a3b8; margin: 0;">Full database dumps with schema definitions and compressed row data</p>
      </div>
      <form action="<?= url('/admin/db-manager/backup') ?>" method="POST">
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #10b981, #059669);">
          <i data-lucide="plus-circle"></i> Create New Snapshot
        </button>
      </form>
    </div>

    <table class="admin-table">
      <thead>
        <tr>
          <th>Snapshot Filename</th>
          <th>Size</th>
          <th>Tables &amp; Rows</th>
          <th>Created By</th>
          <th>Timestamp</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($backups)): ?>
          <?php foreach ($backups as $b): ?>
            <tr>
              <td>
                <strong style="color: #38bdf8; font-family: monospace; font-size: 0.85rem;"><?= htmlspecialchars($b['filename']) ?></strong>
                <div style="font-size: 0.72rem; color: #64748b; font-family: monospace;">SHA256: <?= substr($b['checksum'] ?? '', 0, 16) ?>...</div>
              </td>
              <td>
                <span class="ts-badge ts-badge-cyan"><?= htmlspecialchars($b['file_size_formatted']) ?></span>
              </td>
              <td style="font-size: 0.82rem; color: #cbd5e1;">
                <?= (int)$b['table_count'] ?> tables &bull; <?= number_format((int)$b['total_rows']) ?> rows
              </td>
              <td style="font-size: 0.82rem; color: #94a3b8;">
                <?= htmlspecialchars($b['creator_name'] ?? 'System') ?>
              </td>
              <td style="font-size: 0.78rem; color: #64748b; font-family: monospace;">
                <?= \App\Core\DateHelper::formatDateTime($b['created_at']) ?>
              </td>
              <td>
                <div style="display: flex; gap: 0.35rem;">
                  <a href="<?= url('/admin/db-manager/download/' . $b['id']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.72rem; color: #34d399; border-color: rgba(16,185,129,0.4);" title="Download SQL File">
                    Download ⬇
                  </a>
                  <form action="<?= url('/admin/db-manager/restore/' . $b['id']) ?>" method="POST" onsubmit="return confirm('WARNING: Restoring this snapshot will overwrite current database state. Proceed?');">
                    <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.72rem; color: #f59e0b; border-color: rgba(245,158,11,0.4);" title="Restore Snapshot">
                      Restore ⚡
                    </button>
                  </form>
                  <form action="<?= url('/admin/db-manager/delete/' . $b['id']) ?>" method="POST" onsubmit="return confirm('Delete this backup snapshot?');">
                    <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.72rem; color: #ef4444; border-color: rgba(239,68,68,0.4);" title="Delete Snapshot">
                      🗑
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" style="text-align: center; padding: 2rem; color: #64748b;">
              No backup snapshots generated yet. Click "Create New Snapshot" to take a zero-downtime backup.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
