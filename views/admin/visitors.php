<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<style>
/* ==========================================================================
   TechScout Visitor Intelligence & Real-Time Forensics [TS-VISIT-01]
   ========================================================================== */
.visitor-topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 2rem;
  padding-bottom: 1.25rem;
  border-bottom: 1px solid var(--admin-border-subtle, rgba(148,163,184,0.12));
}

.visitor-pulse-dot {
  width: 8px;
  height: 8px;
  border-radius: 9999px;
  background-color: #10b981;
  display: inline-block;
  box-shadow: 0 0 10px #10b981;
  animation: visitorPulse 1.8s infinite;
}

@keyframes visitorPulse {
  0% { transform: scale(0.95); opacity: 0.7; }
  50% { transform: scale(1.35); opacity: 1; }
  100% { transform: scale(0.95); opacity: 0.7; }
}

.visitor-filter-bar {
  background: var(--admin-card-bg, #0f172a);
  border: 1px solid var(--admin-border-subtle, rgba(148,163,184,0.15));
  border-radius: 12px;
  padding: 1.15rem 1.4rem;
  margin-bottom: 1.75rem;
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  align-items: flex-end;
}

.visitor-filter-item {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  flex: 1 1 180px;
}

.visitor-filter-label {
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--ts-text-secondary, #94a3b8);
}

.visitor-table-card {
  background: var(--admin-card-bg, #0f172a);
  border: 1px solid var(--admin-border-subtle, rgba(148,163,184,0.15));
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0,0,0,0.25);
  margin-bottom: 2rem;
}

.visitor-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
  font-size: 0.85rem;
}

.visitor-table th {
  background: var(--admin-bg-surface, rgba(15,23,42,0.85));
  color: var(--ts-text-secondary, #94a3b8);
  font-weight: 700;
  font-size: 0.74rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  padding: 0.85rem 1rem;
  border-bottom: 1px solid var(--admin-border-subtle, rgba(148,163,184,0.15));
}

.visitor-table td {
  padding: 0.85rem 1rem;
  border-bottom: 1px solid var(--admin-border-subtle, rgba(148,163,184,0.08));
  vertical-align: middle;
}

.visitor-table tr:hover td {
  background: var(--admin-hover-bg, rgba(56,189,248,0.03));
}

.visitor-status-badge {
  display: inline-flex;
  align-items: center;
  font-size: 0.72rem;
  font-weight: 700;
  padding: 0.15rem 0.5rem;
  border-radius: 6px;
  font-family: var(--admin-font-mono, monospace);
}

.status-2xx { background: rgba(16,185,129,0.15); color: #10b981; }
.status-3xx { background: rgba(56,189,248,0.15); color: #38bdf8; }
.status-4xx { background: rgba(245,158,11,0.15); color: #f59e0b; }
.status-5xx { background: rgba(239,68,68,0.15); color: #ef4444; }

.visitor-method-tag {
  display: inline-block;
  font-size: 0.68rem;
  font-weight: 800;
  padding: 0.1rem 0.4rem;
  border-radius: 4px;
  font-family: var(--admin-font-mono, monospace);
  margin-right: 0.35rem;
}

.method-get { background: rgba(56,189,248,0.15); color: #38bdf8; }
.method-post { background: rgba(16,185,129,0.15); color: #10b981; }
.method-put { background: rgba(245,158,11,0.15); color: #f59e0b; }
.method-delete { background: rgba(239,68,68,0.15); color: #ef4444; }

.visitor-badge-bot {
  background: rgba(244,63,94,0.15);
  color: #f43f5e;
  border: 1px solid rgba(244,63,94,0.3);
}

.visitor-badge-human {
  background: rgba(16,185,129,0.12);
  color: #10b981;
  border: 1px solid rgba(16,185,129,0.25);
}

.visitor-intel-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.25rem;
  margin-bottom: 2rem;
}

.visitor-intel-box {
  background: var(--admin-card-bg, #0f172a);
  border: 1px solid var(--admin-border-subtle, rgba(148,163,184,0.15));
  border-radius: 12px;
  padding: 1.25rem 1.4rem;
}

.visitor-intel-title {
  font-size: 0.82rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--ts-text-secondary, #94a3b8);
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.visitor-intel-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.45rem 0;
  border-bottom: 1px solid var(--admin-border-subtle, rgba(148,163,184,0.06));
  font-size: 0.82rem;
}

.visitor-intel-item:last-child {
  border-bottom: none;
}

/* Forensic Modal */
.forensic-modal-backdrop {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.7);
  backdrop-filter: blur(8px);
  z-index: 99999;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
}

.forensic-modal-content {
  background: var(--admin-card-bg, #0f172a);
  border: 1px solid var(--admin-border-subtle, rgba(56,189,248,0.3));
  border-radius: 16px;
  max-width: 680px;
  width: 100%;
  max-height: 85vh;
  overflow-y: auto;
  box-shadow: 0 10px 40px rgba(0,0,0,0.5);
  padding: 1.75rem;
}

.forensic-kv-row {
  display: flex;
  padding: 0.55rem 0;
  border-bottom: 1px solid var(--admin-border-subtle, rgba(148,163,184,0.1));
  font-size: 0.82rem;
}

.forensic-kv-key {
  width: 150px;
  flex-shrink: 0;
  font-weight: 700;
  color: var(--ts-text-secondary, #94a3b8);
}

.forensic-kv-val {
  flex: 1;
  font-family: var(--admin-font-mono, monospace);
  word-break: break-all;
  color: var(--ts-text-primary, #f8fafc);
}
</style>

<!-- Topbar -->
<div class="visitor-topbar">
  <div>
    <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.35rem;">
      <span class="ts-badge ts-badge-cyan">TRAFFIC INTELLIGENCE &bull; FORENSIC ENGINE</span>
      <span style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.76rem; color: #10b981; font-weight: 700;">
        <span class="visitor-pulse-dot" id="livePulseIndicator"></span>
        <span id="livePulseText">Real-Time Sensor Active</span>
      </span>
    </div>
    <h1 style="font-size: 1.65rem; font-family: 'Outfit', sans-serif; font-weight: 800; margin: 0; color: var(--ts-text-primary, #f8fafc);">
      Visitor Intelligence &amp; Traffic Forensics
    </h1>
  </div>

  <div style="display: flex; gap: 0.65rem; flex-wrap: wrap; align-items: center;">
    <button type="button" onclick="toggleLiveFeed()" id="feedToggleBtn" class="ts-btn ts-btn-outline ts-btn-sm" style="border-color: rgba(16,185,129,0.35); color: #10b981;">
      <i data-lucide="radio" id="feedToggleIcon"></i> <span id="feedToggleLabel">Pause Stream</span>
    </button>
    
    <a href="<?= url('/admin/visitors/export') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.35);">
      <i data-lucide="download"></i> Export Forensic CSV
    </a>

    <button type="button" onclick="openPurgeModal()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.35);">
      <i data-lucide="trash-2"></i> Purge Logs
    </button>
  </div>
</div>

<!-- High-Level Metric Stat Cards -->
<div class="admin-stat-grid" style="margin-bottom: 2rem;">
  <!-- Total Visits -->
  <div class="admin-stat-card">
    <div class="admin-stat-label">Total Recorded Visits</div>
    <div class="admin-stat-val" style="color: #38bdf8;"><?= number_format($metrics['total_visits'] ?? 0) ?></div>
    <div style="font-size: 0.78rem; color: var(--ts-text-secondary, #94a3b8);">
      <strong><?= number_format($metrics['today_visits'] ?? 0) ?></strong> visits today (UTC)
    </div>
  </div>

  <!-- Unique Visitors -->
  <div class="admin-stat-card">
    <div class="admin-stat-label">Unique Device Hashes</div>
    <div class="admin-stat-val" style="color: #10b981;"><?= number_format($metrics['unique_visitors'] ?? 0) ?></div>
    <div style="font-size: 0.78rem; color: var(--ts-text-secondary, #94a3b8);">
      Across <strong><?= number_format($metrics['unique_ips'] ?? 0) ?></strong> unique IP addresses
    </div>
  </div>

  <!-- Bot vs Human -->
  <div class="admin-stat-card">
    <div class="admin-stat-label">Human vs Bot Ratio</div>
    <?php 
      $totalAnalyzed = ($metrics['human_count'] + $metrics['bot_count']) ?: 1;
      $humanPct = round(($metrics['human_count'] / $totalAnalyzed) * 100);
      $botPct = round(($metrics['bot_count'] / $totalAnalyzed) * 100);
    ?>
    <div class="admin-stat-val" style="color: #a855f7;">
      <?= $humanPct ?>% <span style="font-size: 0.85rem; font-weight: 500; color: var(--ts-text-tertiary, #64748b);">/ <?= $botPct ?>% Bot</span>
    </div>
    <div style="font-size: 0.78rem; color: var(--ts-text-secondary, #94a3b8);">
      <?= number_format($metrics['human_count']) ?> Humans &bull; <?= number_format($metrics['bot_count']) ?> Bots
    </div>
  </div>

  <!-- Average Response Time -->
  <div class="admin-stat-card">
    <div class="admin-stat-label">Average Server Latency</div>
    <div class="admin-stat-val" style="color: <?= ($metrics['avg_latency'] < 150) ? '#10b981' : (($metrics['avg_latency'] < 400) ? '#f59e0b' : '#ef4444') ?>;">
      <?= number_format($metrics['avg_latency'] ?? 0, 1) ?> ms
    </div>
    <div style="font-size: 0.78rem; color: var(--ts-text-secondary, #94a3b8);">
      Desktop: <?= number_format($metrics['desktop_count']) ?> &bull; Mobile: <?= number_format($metrics['mobile_count']) ?>
    </div>
  </div>
</div>

<!-- Secondary Intel Breakdown Grid -->
<div class="visitor-intel-grid">
  <!-- Top Geographies -->
  <div class="visitor-intel-box">
    <div class="visitor-intel-title">
      <span>Top Visitor Origins</span>
      <i data-lucide="globe" style="width: 16px; height: 16px; color: #38bdf8;"></i>
    </div>
    <?php if (empty($topCountries)): ?>
      <div style="color: var(--ts-text-tertiary, #64748b); font-size: 0.8rem; padding: 1rem 0; text-align: center;">No country telemetry recorded yet.</div>
    <?php else: ?>
      <?php foreach ($topCountries as $c): ?>
        <div class="visitor-intel-item">
          <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span class="ts-badge" style="font-size: 0.65rem; padding: 0.1rem 0.35rem; font-family: var(--admin-font-mono);"><?= htmlspecialchars($c['country_code'] ?? 'XX') ?></span>
            <span style="font-weight: 600;"><?= htmlspecialchars($c['country_name'] ?? 'Unknown') ?></span>
          </div>
          <strong style="color: #38bdf8; font-family: var(--admin-font-mono);"><?= number_format($c['visit_count']) ?></strong>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- Top Visited Routes -->
  <div class="visitor-intel-box">
    <div class="visitor-intel-title">
      <span>Top Visited URIs</span>
      <i data-lucide="compass" style="width: 16px; height: 16px; color: #10b981;"></i>
    </div>
    <?php if (empty($topPaths)): ?>
      <div style="color: var(--ts-text-tertiary, #64748b); font-size: 0.8rem; padding: 1rem 0; text-align: center;">No path telemetry recorded yet.</div>
    <?php else: ?>
      <?php foreach ($topPaths as $p): ?>
        <div class="visitor-intel-item">
          <div style="max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-family: var(--admin-font-mono);" title="<?= htmlspecialchars($p['request_uri']) ?>">
            <?= htmlspecialchars($p['request_uri']) ?>
          </div>
          <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span style="font-size: 0.72rem; color: var(--ts-text-tertiary, #64748b);"><?= round((float)$p['avg_ms'], 1) ?>ms</span>
            <strong style="color: #10b981; font-family: var(--admin-font-mono);"><?= number_format($p['hit_count']) ?></strong>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- Top Browsers -->
  <div class="visitor-intel-box">
    <div class="visitor-intel-title">
      <span>Top User Browsers</span>
      <i data-lucide="cpu" style="width: 16px; height: 16px; color: #a855f7;"></i>
    </div>
    <?php if (empty($topBrowsers)): ?>
      <div style="color: var(--ts-text-tertiary, #64748b); font-size: 0.8rem; padding: 1rem 0; text-align: center;">No client browser data available.</div>
    <?php else: ?>
      <?php foreach ($topBrowsers as $b): ?>
        <div class="visitor-intel-item">
          <span style="font-weight: 600;"><?= htmlspecialchars($b['browser'] ?? 'Unknown') ?></span>
          <strong style="color: #a855f7; font-family: var(--admin-font-mono);"><?= number_format($b['hit_count']) ?></strong>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<!-- Filters & Search Toolbar -->
<form method="GET" action="<?= url('/admin/visitors') ?>" class="visitor-filter-bar">
  <!-- Search -->
  <div class="visitor-filter-item" style="flex: 2 1 240px;">
    <label class="visitor-filter-label">Forensic Search</label>
    <input type="text" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" placeholder="IP, URI, user, country, OS, user agent..." class="admin-modal-input" style="font-size: 0.84rem;">
  </div>

  <!-- Device Type -->
  <div class="visitor-filter-item">
    <label class="visitor-filter-label">Device Type</label>
    <select name="device" class="admin-modal-input" style="font-size: 0.84rem;">
      <option value="all" <?= ($filters['device'] ?? '') === 'all' ? 'selected' : '' ?>>All Devices</option>
      <option value="desktop" <?= ($filters['device'] ?? '') === 'desktop' ? 'selected' : '' ?>>Desktop</option>
      <option value="mobile" <?= ($filters['device'] ?? '') === 'mobile' ? 'selected' : '' ?>>Mobile</option>
      <option value="tablet" <?= ($filters['device'] ?? '') === 'tablet' ? 'selected' : '' ?>>Tablet</option>
      <option value="bot" <?= ($filters['device'] ?? '') === 'bot' ? 'selected' : '' ?>>Bots &amp; Crawlers</option>
    </select>
  </div>

  <!-- Status Code -->
  <div class="visitor-filter-item">
    <label class="visitor-filter-label">HTTP Status</label>
    <select name="status" class="admin-modal-input" style="font-size: 0.84rem;">
      <option value="all" <?= ($filters['status'] ?? '') === 'all' ? 'selected' : '' ?>>All Statuses</option>
      <option value="200" <?= ($filters['status'] ?? '') === '200' ? 'selected' : '' ?>>200 OK</option>
      <option value="301" <?= ($filters['status'] ?? '') === '301' ? 'selected' : '' ?>>301 Redirect</option>
      <option value="403" <?= ($filters['status'] ?? '') === '403' ? 'selected' : '' ?>>403 Forbidden</option>
      <option value="404" <?= ($filters['status'] ?? '') === '404' ? 'selected' : '' ?>>404 Not Found</option>
      <option value="500" <?= ($filters['status'] ?? '') === '500' ? 'selected' : '' ?>>500 Error</option>
      <option value="503" <?= ($filters['status'] ?? '') === '503' ? 'selected' : '' ?>>503 Maintenance</option>
    </select>
  </div>

  <!-- Bot Classification -->
  <div class="visitor-filter-item">
    <label class="visitor-filter-label">Traffic Type</label>
    <select name="bot" class="admin-modal-input" style="font-size: 0.84rem;">
      <option value="all" <?= ($filters['bot'] ?? '') === 'all' ? 'selected' : '' ?>>All Traffic</option>
      <option value="only_humans" <?= ($filters['bot'] ?? '') === 'only_humans' ? 'selected' : '' ?>>Humans Only</option>
      <option value="only_bots" <?= ($filters['bot'] ?? '') === 'only_bots' ? 'selected' : '' ?>>Automated Bots Only</option>
    </select>
  </div>

  <!-- Submit Buttons -->
  <div style="display: flex; gap: 0.5rem; align-items: center;">
    <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="padding: 0.65rem 1.15rem; font-weight: 700;">
      <i data-lucide="filter"></i> Filter
    </button>
    <?php if (!empty($filters['search']) || ($filters['device'] ?? 'all') !== 'all' || ($filters['status'] ?? 'all') !== 'all' || ($filters['bot'] ?? 'all') !== 'all'): ?>
      <a href="<?= url('/admin/visitors') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.65rem 0.9rem;" title="Reset Filters">
        <i data-lucide="x"></i>
      </a>
    <?php endif; ?>
  </div>
</form>

<!-- Visitor Telemetry Logs Table Card -->
<div class="visitor-table-card">
  <div style="padding: 1.15rem 1.4rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--admin-border-subtle, rgba(148,163,184,0.12));">
    <div>
      <strong style="font-size: 0.95rem; color: var(--ts-text-primary, #f8fafc);">Live Event Feed</strong>
      <span style="font-size: 0.78rem; color: var(--ts-text-secondary, #94a3b8); margin-left: 0.5rem;">
        Showing <?= count($logs) ?> of <?= number_format($totalLogs) ?> total events
      </span>
    </div>
    <div style="font-size: 0.76rem; font-family: var(--admin-font-mono); color: #38bdf8;">
      Server Reference: <?= gmdate('Y-m-d H:i:s \U\T\C') ?>
    </div>
  </div>

  <div style="overflow-x: auto;">
    <table class="visitor-table">
      <thead>
        <tr>
          <th>Timestamp (UTC)</th>
          <th>IP &amp; Geolocation</th>
          <th>Identity</th>
          <th>Request URI &amp; Route</th>
          <th>Client &amp; OS</th>
          <th>Traffic Type</th>
          <th>HTTP Status &amp; Time</th>
          <th style="text-align: right;">Forensics</th>
        </tr>
      </thead>
      <tbody id="visitorFeedTbody">
        <?php if (empty($logs)): ?>
          <tr>
            <td colspan="8" style="text-align: center; padding: 3rem 1rem; color: var(--ts-text-tertiary, #64748b);">
              <i data-lucide="inbox" style="width: 36px; height: 36px; margin-bottom: 0.5rem; opacity: 0.4;"></i>
              <div>No visitor events matched your active criteria.</div>
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($logs as $log): ?>
            <?php 
              $logJson = htmlspecialchars(json_encode($log), ENT_QUOTES, 'UTF-8');
              $statusCode = (int)$log['http_status'];
              $statusClass = ($statusCode >= 500) ? 'status-5xx' : (($statusCode >= 400) ? 'status-4xx' : (($statusCode >= 300) ? 'status-3xx' : 'status-2xx'));
              $methodLower = strtolower($log['request_method'] ?? 'get');
              $methodClass = 'method-' . $methodLower;
            ?>
            <tr id="log-row-<?= $log['id'] ?>" data-log-id="<?= $log['id'] ?>">
              <!-- Timestamp -->
              <td style="font-family: var(--admin-font-mono); font-size: 0.78rem; white-space: nowrap;">
                <div style="color: var(--ts-text-primary, #f8fafc); font-weight: 600;"><?= substr($log['created_at'], 11, 8) ?> UTC</div>
                <div style="font-size: 0.7rem; color: var(--ts-text-tertiary, #64748b);"><?= substr($log['created_at'], 0, 10) ?></div>
              </td>

              <!-- IP & Geo -->
              <td>
                <div style="font-family: var(--admin-font-mono); font-weight: 700; color: #38bdf8; font-size: 0.82rem;">
                  <?= htmlspecialchars($log['ip_address']) ?>
                </div>
                <div style="font-size: 0.74rem; color: var(--ts-text-secondary, #94a3b8); display: flex; align-items: center; gap: 0.35rem; margin-top: 0.15rem;">
                  <span class="ts-badge" style="font-size: 0.65rem; padding: 0.05rem 0.3rem;"><?= htmlspecialchars($log['country_code'] ?? 'XX') ?></span>
                  <span><?= htmlspecialchars($log['country_name'] ?? 'Global') ?></span>
                </div>
              </td>

              <!-- Identity -->
              <td>
                <?php if (!empty($log['username'])): ?>
                  <div style="font-weight: 700; color: #10b981; display: flex; align-items: center; gap: 0.3rem;">
                    <i data-lucide="user-check" style="width: 14px; height: 14px;"></i>
                    <span><?= htmlspecialchars($log['username']) ?></span>
                  </div>
                  <div style="font-size: 0.7rem; color: var(--ts-text-tertiary, #64748b); font-family: var(--admin-font-mono);">
                    UID #<?= $log['user_id'] ?>
                  </div>
                <?php else: ?>
                  <div style="color: var(--ts-text-secondary, #94a3b8); font-size: 0.8rem;">Guest Visitor</div>
                  <div style="font-size: 0.7rem; color: var(--ts-text-tertiary, #64748b); font-family: var(--admin-font-mono);">
                    <?= substr($log['session_id'], 0, 12) ?>...
                  </div>
                <?php endif; ?>
              </td>

              <!-- Request URI & Route -->
              <td style="max-width: 280px;">
                <div style="display: flex; align-items: center;">
                  <span class="visitor-method-tag <?= $methodClass ?>"><?= htmlspecialchars($log['request_method']) ?></span>
                  <span style="font-family: var(--admin-font-mono); font-size: 0.82rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--ts-text-primary, #f8fafc);" title="<?= htmlspecialchars($log['request_uri']) ?>">
                    <?= htmlspecialchars($log['request_uri']) ?>
                  </span>
                </div>
                <?php if (!empty($log['referrer_domain'])): ?>
                  <div style="font-size: 0.7rem; color: var(--ts-text-tertiary, #64748b); margin-top: 0.15rem;">
                    via <?= htmlspecialchars($log['referrer_domain']) ?>
                  </div>
                <?php endif; ?>
              </td>

              <!-- Client & OS -->
              <td>
                <div style="font-size: 0.82rem; font-weight: 600; color: var(--ts-text-primary, #f8fafc);">
                  <?= htmlspecialchars($log['browser'] ?? 'Unknown') ?>
                  <?php if (!empty($log['browser_version'])): ?>
                    <span style="font-size: 0.72rem; color: var(--ts-text-tertiary, #64748b);"><?= htmlspecialchars(explode('.', $log['browser_version'])[0]) ?></span>
                  <?php endif; ?>
                </div>
                <div style="font-size: 0.72rem; color: var(--ts-text-secondary, #94a3b8); margin-top: 0.15rem;">
                  <?= htmlspecialchars($log['os'] ?? 'Unknown OS') ?> &bull; <?= ucfirst($log['device_type'] ?? 'desktop') ?>
                </div>
              </td>

              <!-- Traffic Type -->
              <td>
                <?php if ($log['is_bot']): ?>
                  <span class="ts-badge visitor-badge-bot">
                    <i data-lucide="bot" style="width: 12px; height: 12px; margin-right: 0.25rem;"></i>
                    <?= htmlspecialchars($log['bot_name'] ?: 'Bot') ?>
                  </span>
                <?php else: ?>
                  <span class="ts-badge visitor-badge-human">
                    <i data-lucide="user" style="width: 12px; height: 12px; margin-right: 0.25rem;"></i>
                    Human
                  </span>
                <?php endif; ?>
              </td>

              <!-- Status Code & Time -->
              <td>
                <div style="display: flex; align-items: center; gap: 0.4rem;">
                  <span class="visitor-status-badge <?= $statusClass ?>">HTTP <?= $log['http_status'] ?></span>
                </div>
                <div style="font-size: 0.72rem; font-family: var(--admin-font-mono); color: var(--ts-text-tertiary, #64748b); margin-top: 0.15rem;">
                  <?= number_format((float)$log['response_time_ms'], 1) ?> ms
                </div>
              </td>

              <!-- Actions -->
              <td style="text-align: right; white-space: nowrap;">
                <button type="button" onclick="inspectForensicEvent(<?= $logJson ?>)" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.35rem 0.6rem;" title="Deep Forensic Inspection">
                  <i data-lucide="search" style="width: 14px; height: 14px;"></i>
                </button>

                <form method="POST" action="<?= url('/admin/visitors/block-ip') ?>" style="display: inline-block; margin: 0;" onsubmit="return confirm('Block IP address <?= htmlspecialchars($log['ip_address']) ?> in firewall perimeter?');">
                  <input type="hidden" name="ip_address" value="<?= htmlspecialchars($log['ip_address']) ?>">
                  <input type="hidden" name="reason" value="Enforced from Visitor Hub for <?= htmlspecialchars($log['request_uri']) ?>">
                  <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.35rem 0.6rem; color: #ef4444; border-color: rgba(239,68,68,0.3);" title="Block IP in Firewall">
                    <i data-lucide="shield-ban" style="width: 14px; height: 14px;"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination Bar -->
  <?php if ($totalPages > 1): ?>
    <div style="padding: 1.15rem 1.4rem; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--admin-border-subtle, rgba(148,163,184,0.12));">
      <div style="font-size: 0.8rem; color: var(--ts-text-secondary, #94a3b8);">
        Page <strong><?= $page ?></strong> of <strong><?= $totalPages ?></strong>
      </div>
      <div style="display: flex; gap: 0.5rem;">
        <?php 
          $queryParams = $_GET; 
          $queryParams['page'] = max(1, $page - 1);
          $prevUrl = url('/admin/visitors') . '?' . http_build_query($queryParams);
          $queryParams['page'] = min($totalPages, $page + 1);
          $nextUrl = url('/admin/visitors') . '?' . http_build_query($queryParams);
        ?>
        <?php if ($page > 1): ?>
          <a href="<?= $prevUrl ?>" class="ts-btn ts-btn-outline ts-btn-sm">&larr; Previous</a>
        <?php endif; ?>
        <?php if ($page < $totalPages): ?>
          <a href="<?= $nextUrl ?>" class="ts-btn ts-btn-outline ts-btn-sm">Next &rarr;</a>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<!-- Deep Forensic Inspection Modal -->
<div id="forensicModal" class="forensic-modal-backdrop" onclick="if(event.target === this) closeForensicModal();">
  <div class="forensic-modal-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--admin-border-subtle, rgba(148,163,184,0.15)); padding-bottom: 0.75rem;">
      <div>
        <span class="ts-badge ts-badge-cyan" style="font-size: 0.7rem;">FORENSIC PACKET INSPECTION</span>
        <h3 style="margin: 0.25rem 0 0 0; font-size: 1.25rem; font-family: 'Outfit', sans-serif; font-weight: 800;">
          Event #<span id="f_log_id">--</span>
        </h3>
      </div>
      <button type="button" onclick="closeForensicModal()" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.35rem 0.65rem;">
        <i data-lucide="x"></i>
      </button>
    </div>

    <div id="forensicModalBody">
      <!-- Populated via JS -->
    </div>

    <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem;">
      <button type="button" onclick="closeForensicModal()" class="ts-btn ts-btn-outline ts-btn-sm">
        Close Inspector
      </button>
    </div>
  </div>
</div>

<!-- Purge Confirmation Modal -->
<div id="purgeModal" class="forensic-modal-backdrop" onclick="if(event.target === this) closePurgeModal();">
  <div class="forensic-modal-content" style="max-width: 480px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
      <h3 style="margin: 0; font-size: 1.2rem; font-family: 'Outfit', sans-serif; font-weight: 800; color: #ef4444;">
        Purge Visitor Telemetry
      </h3>
      <button type="button" onclick="closePurgeModal()" class="ts-btn ts-btn-outline ts-btn-sm">
        <i data-lucide="x"></i>
      </button>
    </div>

    <p style="color: var(--ts-text-secondary, #94a3b8); font-size: 0.85rem; margin-bottom: 1.25rem;">
      Select the retention window to purge. Purged logs cannot be recovered.
    </p>

    <form method="POST" action="<?= url('/admin/visitors/purge') ?>">
      <div style="margin-bottom: 1.25rem;">
        <label class="visitor-filter-label" style="margin-bottom: 0.5rem; display: block;">Retention Window</label>
        <select name="retention" class="admin-modal-input">
          <option value="90_days">Delete logs older than 90 days</option>
          <option value="30_days" selected>Delete logs older than 30 days</option>
          <option value="7_days">Delete logs older than 7 days</option>
          <option value="all">Purge ALL logs (Truncate Table)</option>
        </select>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
        <button type="button" onclick="closePurgeModal()" class="ts-btn ts-btn-outline ts-btn-sm">
          Cancel
        </button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #ef4444; border-color: #ef4444; font-weight: 700;">
          Confirm Purge
        </button>
      </div>
    </form>
  </div>
</div>

<script>
// Real-time Traffic Stream Engine
let isLiveFeedActive = true;
let streamTimer = null;
let highestLogId = <?= !empty($logs[0]['id']) ? (int)$logs[0]['id'] : 0 ?>;

function toggleLiveFeed() {
  isLiveFeedActive = !isLiveFeedActive;
  const label = document.getElementById('feedToggleLabel');
  const btn = document.getElementById('feedToggleBtn');
  const pulse = document.getElementById('livePulseIndicator');
  const pulseText = document.getElementById('livePulseText');

  if (isLiveFeedActive) {
    label.innerText = 'Pause Stream';
    btn.style.color = '#10b981';
    btn.style.borderColor = 'rgba(16,185,129,0.35)';
    pulse.style.backgroundColor = '#10b981';
    pulse.style.boxShadow = '0 0 10px #10b981';
    pulseText.innerText = 'Real-Time Sensor Active';
    pulseText.style.color = '#10b981';
    startStreamPolling();
  } else {
    label.innerText = 'Resume Stream';
    btn.style.color = '#f59e0b';
    btn.style.borderColor = 'rgba(245,158,11,0.35)';
    pulse.style.backgroundColor = '#64748b';
    pulse.style.boxShadow = 'none';
    pulseText.innerText = 'Live Sensor Paused';
    pulseText.style.color = '#94a3b8';
    if (streamTimer) clearTimeout(streamTimer);
  }
}

async function pollLiveVisitors() {
  if (!isLiveFeedActive) return;
  try {
    const res = await fetch('<?= url('/admin/visitors/stream') ?>?since_id=' + highestLogId);
    if (res.ok) {
      const data = await res.json();
      if (data.success && data.logs && data.logs.length > 0) {
        prependNewLogs(data.logs);
      }
    }
  } catch (e) {}

  if (isLiveFeedActive) {
    streamTimer = setTimeout(pollLiveVisitors, 4000);
  }
}

function startStreamPolling() {
  if (streamTimer) clearTimeout(streamTimer);
  streamTimer = setTimeout(pollLiveVisitors, 4000);
}

function prependNewLogs(newLogs) {
  const tbody = document.getElementById('visitorFeedTbody');
  if (!tbody) return;

  newLogs.forEach(log => {
    if (log.id > highestLogId) {
      highestLogId = log.id;
    }
    const statusCode = parseInt(log.http_status, 10) || 200;
    const statusClass = (statusCode >= 500) ? 'status-5xx' : ((statusCode >= 400) ? 'status-4xx' : ((statusCode >= 300) ? 'status-3xx' : 'status-2xx'));
    const methodLower = (log.request_method || 'get').toLowerCase();
    const logJson = JSON.stringify(log).replace(/"/g, '&quot;');

    const tr = document.createElement('tr');
    tr.id = 'log-row-' + log.id;
    tr.setAttribute('data-log-id', log.id);
    tr.style.backgroundColor = 'rgba(56, 189, 248, 0.08)';
    tr.style.transition = 'background-color 1.5s ease';

    tr.innerHTML = `
      <td style="font-family: var(--admin-font-mono); font-size: 0.78rem; white-space: nowrap;">
        <div style="color: #38bdf8; font-weight: 700;">${log.created_at.substr(11, 8)} UTC</div>
        <div style="font-size: 0.7rem; color: var(--ts-text-tertiary, #64748b);">${log.created_at.substr(0, 10)}</div>
      </td>
      <td>
        <div style="font-family: var(--admin-font-mono); font-weight: 700; color: #38bdf8; font-size: 0.82rem;">${log.ip_address}</div>
        <div style="font-size: 0.74rem; color: var(--ts-text-secondary, #94a3b8); display: flex; align-items: center; gap: 0.35rem; margin-top: 0.15rem;">
          <span class="ts-badge" style="font-size: 0.65rem; padding: 0.05rem 0.3rem;">${log.country_code || 'XX'}</span>
          <span>${log.country_name || 'Global'}</span>
        </div>
      </td>
      <td>
        ${log.username ? `
          <div style="font-weight: 700; color: #10b981; display: flex; align-items: center; gap: 0.3rem;">
            <i data-lucide="user-check" style="width: 14px; height: 14px;"></i>
            <span>${log.username}</span>
          </div>
          <div style="font-size: 0.7rem; color: var(--ts-text-tertiary, #64748b); font-family: var(--admin-font-mono);">UID #${log.user_id}</div>
        ` : `
          <div style="color: var(--ts-text-secondary, #94a3b8); font-size: 0.8rem;">Guest Visitor</div>
          <div style="font-size: 0.7rem; color: var(--ts-text-tertiary, #64748b); font-family: var(--admin-font-mono);">${log.session_id.substr(0, 12)}...</div>
        `}
      </td>
      <td style="max-width: 280px;">
        <div style="display: flex; align-items: center;">
          <span class="visitor-method-tag method-${methodLower}">${log.request_method}</span>
          <span style="font-family: var(--admin-font-mono); font-size: 0.82rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--ts-text-primary, #f8fafc);" title="${log.request_uri}">${log.request_uri}</span>
        </div>
      </td>
      <td>
        <div style="font-size: 0.82rem; font-weight: 600; color: var(--ts-text-primary, #f8fafc);">${log.browser || 'Unknown'}</div>
        <div style="font-size: 0.72rem; color: var(--ts-text-secondary, #94a3b8); margin-top: 0.15rem;">${log.os || 'Unknown OS'} &bull; ${log.device_type || 'desktop'}</div>
      </td>
      <td>
        ${log.is_bot == 1 ? `
          <span class="ts-badge visitor-badge-bot"><i data-lucide="bot" style="width: 12px; height: 12px; margin-right: 0.25rem;"></i> ${log.bot_name || 'Bot'}</span>
        ` : `
          <span class="ts-badge visitor-badge-human"><i data-lucide="user" style="width: 12px; height: 12px; margin-right: 0.25rem;"></i> Human</span>
        `}
      </td>
      <td>
        <span class="visitor-status-badge ${statusClass}">HTTP ${log.http_status}</span>
        <div style="font-size: 0.72rem; font-family: var(--admin-font-mono); color: var(--ts-text-tertiary, #64748b); margin-top: 0.15rem;">${parseFloat(log.response_time_ms || 0).toFixed(1)} ms</div>
      </td>
      <td style="text-align: right; white-space: nowrap;">
        <button type="button" onclick='inspectForensicEvent(${logJson})' class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.35rem 0.6rem;" title="Deep Forensic Inspection">
          <i data-lucide="search" style="width: 14px; height: 14px;"></i>
        </button>
      </td>
    `;

    tbody.insertBefore(tr, tbody.firstChild);

    setTimeout(() => {
      tr.style.backgroundColor = '';
    }, 2000);
  });

  if (window.lucide && typeof lucide.createIcons === 'function') {
    lucide.createIcons();
  }
}

// Modal inspection handlers
function inspectForensicEvent(log) {
  const modal = document.getElementById('forensicModal');
  const body = document.getElementById('forensicModalBody');
  document.getElementById('f_log_id').innerText = log.id;

  body.innerHTML = `
    <div class="forensic-kv-row"><div class="forensic-kv-key">UTC Timestamp</div><div class="forensic-kv-val">${log.created_at} UTC</div></div>
    <div class="forensic-kv-row"><div class="forensic-kv-key">IP Address</div><div class="forensic-kv-val" style="color: #38bdf8;">${log.ip_address}</div></div>
    <div class="forensic-kv-row"><div class="forensic-kv-key">Geolocation</div><div class="forensic-kv-val">${log.city || 'Unknown'}, ${log.region || ''} ${log.country_name || 'Global'} (${log.country_code || 'XX'})</div></div>
    <div class="forensic-kv-row"><div class="forensic-kv-key">Visitor Hash</div><div class="forensic-kv-val">${log.visitor_hash}</div></div>
    <div class="forensic-kv-row"><div class="forensic-kv-key">Session ID</div><div class="forensic-kv-val">${log.session_id}</div></div>
    <div class="forensic-kv-row"><div class="forensic-kv-key">User Identity</div><div class="forensic-kv-val">${log.username ? `${log.username} (ID: ${log.user_id})` : 'Anonymous Guest'}</div></div>
    <div class="forensic-kv-row"><div class="forensic-kv-key">Request URI</div><div class="forensic-kv-val" style="color: #10b981;">[${log.request_method}] ${log.request_uri}</div></div>
    <div class="forensic-kv-row"><div class="forensic-kv-key">Referrer</div><div class="forensic-kv-val">${log.http_referer || 'Direct Navigation (No Referrer)'}</div></div>
    <div class="forensic-kv-row"><div class="forensic-kv-key">HTTP Status / Latency</div><div class="forensic-kv-val">HTTP ${log.http_status} &bull; ${log.response_time_ms} ms</div></div>
    <div class="forensic-kv-row"><div class="forensic-kv-key">Device / OS / Browser</div><div class="forensic-kv-val">${log.device_type} &bull; ${log.os} &bull; ${log.browser} ${log.browser_version || ''}</div></div>
    <div class="forensic-kv-row"><div class="forensic-kv-key">Bot Classification</div><div class="forensic-kv-val">${log.is_bot == 1 ? `<span style="color: #ef4444; font-weight: 700;">BOT (${log.bot_name || 'Generic'})</span>` : '<span style="color: #10b981; font-weight: 700;">HUMAN VISITOR</span>'}</div></div>
    <div class="forensic-kv-row"><div class="forensic-kv-key">User Agent String</div><div class="forensic-kv-val" style="font-size: 0.75rem; color: #94a3b8;">${log.user_agent || 'Unknown'}</div></div>
    <div class="forensic-kv-row"><div class="forensic-kv-key">Client Timezone</div><div class="forensic-kv-val">${log.client_timezone || 'UTC'}</div></div>
    <div class="forensic-kv-row"><div class="forensic-kv-key">Client Language</div><div class="forensic-kv-val">${log.client_language || 'N/A'}</div></div>
  `;

  modal.style.display = 'flex';
  if (window.lucide && typeof lucide.createIcons === 'function') {
    lucide.createIcons();
  }
}

function closeForensicModal() {
  document.getElementById('forensicModal').style.display = 'none';
}

function openPurgeModal() {
  document.getElementById('purgeModal').style.display = 'flex';
}

function closePurgeModal() {
  document.getElementById('purgeModal').style.display = 'none';
}

// Start Stream Poller
document.addEventListener('DOMContentLoaded', function() {
  startStreamPolling();
});
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
