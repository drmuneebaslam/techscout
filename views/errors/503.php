<?php require __DIR__ . '/../layouts/header.php'; ?>

<style>
/* ==========================================================================
   TechScout Dual-Theme HTTP 503 Maintenance Interface [TS-INF-09]
   ========================================================================== */
.maint-page-wrap {
  min-height: 85vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 4rem 1.25rem;
  position: relative;
  overflow: hidden;
  background-color: var(--ts-bg-base, #070c18);
  transition: background-color 0.3s ease;
}

[data-theme="light"] .maint-page-wrap,
.theme-light .maint-page-wrap {
  background-color: #f8fafc !important;
}

/* Ambient Background Glow */
.maint-glow {
  position: absolute;
  top: 15%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 760px;
  height: 440px;
  background: radial-gradient(circle, rgba(56, 189, 248, 0.14) 0%, rgba(99, 102, 241, 0.08) 50%, transparent 70%);
  pointer-events: none;
  z-index: 0;
}

[data-theme="light"] .maint-glow,
.theme-light .maint-glow {
  background: radial-gradient(circle, rgba(2, 132, 199, 0.12) 0%, rgba(79, 70, 229, 0.08) 50%, transparent 70%);
}

/* Status Radar Pill */
.maint-status-pill {
  display: inline-flex;
  align-items: center;
  gap: 0.65rem;
  background: rgba(56,189,248,0.1);
  border: 1px solid rgba(56,189,248,0.35);
  padding: 0.45rem 1.25rem;
  border-radius: 9999px;
  margin-bottom: 1.75rem;
  box-shadow: 0 0 20px rgba(56,189,248,0.15);
}

[data-theme="light"] .maint-status-pill,
.theme-light .maint-status-pill {
  background: rgba(2, 132, 199, 0.08);
  border-color: rgba(2, 132, 199, 0.3);
  box-shadow: 0 2px 10px rgba(2, 132, 199, 0.08);
}

.maint-status-text {
  font-size: 0.8rem;
  font-weight: 700;
  color: #38bdf8;
  font-family: var(--ts-font-mono, monospace);
  letter-spacing: 0.06em;
  text-transform: uppercase;
}

[data-theme="light"] .maint-status-text,
.theme-light .maint-status-text {
  color: #0284c7;
}

/* Heading */
.maint-heading {
  font-size: clamp(2.2rem, 5vw, 3.75rem);
  font-weight: 900;
  font-family: 'Outfit', -apple-system, sans-serif;
  line-height: 1.15;
  margin: 0 0 1.25rem 0;
  letter-spacing: -0.03em;
}

.maint-heading-gradient {
  background: linear-gradient(135deg, #f8fafc 20%, #38bdf8 65%, #818cf8 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

[data-theme="light"] .maint-heading-gradient,
.theme-light .maint-heading-gradient {
  background: linear-gradient(135deg, #0f172a 20%, #0284c7 65%, #4f46e5 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

/* Description paragraph */
.maint-description {
  color: var(--ts-text-secondary, #94a3b8);
  font-size: clamp(1rem, 2vw, 1.12rem);
  line-height: 1.65;
  max-width: 700px;
  margin: 0 auto 2.25rem auto;
}

[data-theme="light"] .maint-description,
.theme-light .maint-description {
  color: #334155 !important;
}

/* Live Countdown Container & Unit Boxes */
.maint-countdown-strip {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-bottom: 2.25rem;
  flex-wrap: wrap;
}

.maint-countdown-box {
  background: rgba(15, 23, 42, 0.85);
  border: 1px solid rgba(56, 189, 248, 0.3);
  border-radius: 14px;
  padding: 1rem 1.4rem;
  min-width: 95px;
  text-align: center;
  backdrop-filter: blur(12px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
  transition: transform 0.2s ease, border-color 0.2s ease;
}

.maint-countdown-box:hover {
  transform: translateY(-2px);
  border-color: rgba(56, 189, 248, 0.6);
}

[data-theme="light"] .maint-countdown-box,
.theme-light .maint-countdown-box {
  background: #ffffff !important;
  border-color: #cbd5e1 !important;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
}

.maint-countdown-number {
  font-size: 2.3rem;
  font-weight: 900;
  font-family: var(--ts-font-mono, monospace);
  line-height: 1;
  color: #38bdf8;
  margin-bottom: 0.35rem;
}

[data-theme="light"] .maint-countdown-number,
.theme-light .maint-countdown-number {
  color: #0284c7 !important;
}

.maint-countdown-label {
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--ts-text-tertiary, #64748b);
}

[data-theme="light"] .maint-countdown-label,
.theme-light .maint-countdown-label {
  color: #64748b !important;
}

/* Timezone Matrix Card */
.maint-timezone-card {
  max-width: 720px;
  margin: 0 auto 2.5rem auto;
  background: rgba(15, 23, 42, 0.75);
  border: 1px solid rgba(56, 189, 248, 0.2);
  border-radius: 14px;
  padding: 1.25rem 1.75rem;
  backdrop-filter: blur(10px);
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 1.25rem;
  text-align: left;
}

[data-theme="light"] .maint-timezone-card,
.theme-light .maint-timezone-card {
  background: #ffffff !important;
  border: 1px solid #cbd5e1 !important;
  box-shadow: 0 4px 20px rgba(0,0,0,0.06);
}

.maint-tz-meta-title {
  font-size: 0.75rem;
  text-transform: uppercase;
  font-weight: 700;
  letter-spacing: 0.06em;
  color: var(--ts-text-tertiary, #94a3b8);
  margin-bottom: 0.35rem;
  display: flex;
  align-items: center;
  gap: 0.4rem;
}

.maint-tz-val {
  font-size: 0.96rem;
  font-weight: 700;
  font-family: var(--ts-font-mono, monospace);
  color: #f8fafc;
}

[data-theme="light"] .maint-tz-val,
.theme-light .maint-tz-val {
  color: #0f172a !important;
}

.maint-tz-pill {
  display: inline-block;
  font-size: 0.7rem;
  padding: 0.15rem 0.5rem;
  border-radius: 6px;
  background: rgba(56, 189, 248, 0.15);
  color: #38bdf8;
  font-family: var(--ts-font-mono, monospace);
  margin-top: 0.3rem;
}

[data-theme="light"] .maint-tz-pill,
.theme-light .maint-tz-pill {
  background: rgba(2, 132, 199, 0.1);
  color: #0284c7;
}

/* Secondary Telemetry Grid */
.maint-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1.25rem;
  margin-bottom: 2.5rem;
  text-align: left;
}

.maint-card {
  background: rgba(15, 23, 42, 0.65);
  border: 1px solid rgba(56, 189, 248, 0.18);
  border-radius: 12px;
  padding: 1.25rem;
  backdrop-filter: blur(8px);
}

[data-theme="light"] .maint-card,
.theme-light .maint-card {
  background: #f8fafc !important;
  border: 1px solid #e2e8f0 !important;
}

.maint-card-label {
  font-size: 0.74rem;
  color: #94a3b8;
  text-transform: uppercase;
  font-weight: 700;
  letter-spacing: 0.05em;
  margin-bottom: 0.35rem;
}

[data-theme="light"] .maint-card-label,
.theme-light .maint-card-label {
  color: #64748b !important;
}

.maint-card-sub {
  font-size: 0.75rem;
  color: #64748b;
  margin-top: 0.35rem;
}

/* Diagnostic Box */
.maint-ping-box {
  display: none;
  max-width: 520px;
  margin: 1.5rem auto 0 auto;
  background: rgba(15, 23, 42, 0.9);
  border: 1px solid rgba(56, 189, 248, 0.35);
  border-radius: 12px;
  padding: 1.15rem 1.5rem;
  font-family: var(--ts-font-mono, monospace);
  font-size: 0.84rem;
  color: #38bdf8;
  text-align: left;
}

[data-theme="light"] .maint-ping-box,
.theme-light .maint-ping-box {
  background: #ffffff !important;
  border-color: #0284c7 !important;
  color: #0369a1 !important;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}
</style>

<section class="maint-page-wrap">
  <!-- Ambient High-Tech Glow Backdrop -->
  <div class="maint-glow"></div>

  <div class="container" style="position: relative; z-index: 1; max-width: 860px; margin: 0 auto; text-align: center;">
    
    <!-- Status Radar Pill -->
    <div class="maint-status-pill">
      <span style="position: relative; display: flex; height: 10px; width: 10px;">
        <span style="animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite; position: absolute; display: inline-flex; height: 100%; width: 100%; border-radius: 9999px; background-color: #38bdf8; opacity: 0.75;"></span>
        <span style="position: relative; display: inline-flex; border-radius: 9999px; height: 10px; width: 10px; background-color: #0284c7;"></span>
      </span>
      <span class="maint-status-text">
        HTTP 503 &bull; Core Infrastructure Maintenance Mode
      </span>
    </div>

    <!-- Main Heading -->
    <h1 class="maint-heading">
      <span class="maint-heading-gradient">
        Platform Scaling &amp; Upgrades Underway
      </span>
    </h1>

    <p class="maint-description">
      <?= !empty($customMessage) ? htmlspecialchars($customMessage) : 'TechScout core engineering systems are currently executing scheduled platform upgrades, database migrations, and edge node enhancements.' ?>
    </p>

    <!-- Multi-Unit Real-Time Countdown Strip -->
    <div class="maint-countdown-strip">
      <div class="maint-countdown-box">
        <div class="maint-countdown-number" id="cd_days">00</div>
        <div class="maint-countdown-label">Days</div>
      </div>
      <div class="maint-countdown-box">
        <div class="maint-countdown-number" id="cd_hours">00</div>
        <div class="maint-countdown-label">Hours</div>
      </div>
      <div class="maint-countdown-box">
        <div class="maint-countdown-number" id="cd_mins">00</div>
        <div class="maint-countdown-label">Minutes</div>
      </div>
      <div class="maint-countdown-box">
        <div class="maint-countdown-number" id="cd_secs">00</div>
        <div class="maint-countdown-label">Seconds</div>
      </div>
    </div>

    <!-- Universal Multi-Timezone HUD -->
    <div class="maint-timezone-card">
      <div>
        <div class="maint-tz-meta-title">
          <i data-lucide="clock" style="width: 14px; height: 14px; color: #38bdf8;"></i>
          <span>Your Local Timezone Estimated Return</span>
        </div>
        <div class="maint-tz-val" id="ts_local_time_display">Syncing timezone...</div>
        <div class="maint-tz-pill" id="ts_local_tz_pill">Local System Time</div>
      </div>

      <div>
        <div class="maint-tz-meta-title">
          <i data-lucide="globe" style="width: 14px; height: 14px; color: #10b981;"></i>
          <span>Universal Standard Time (UTC) Reference</span>
        </div>
        <?php 
          $targetTimestamp = !empty($maintenanceUntilUtc) ? strtotime($maintenanceUntilUtc . ' UTC') : (time() + ((int)($countdownMinutes ?? 45) * 60));
          $utcFormatted = gmdate('M j, Y • H:i:s \U\T\C', $targetTimestamp);
          $targetUtcIso = gmdate('Y-m-d\TH:i:s\Z', $targetTimestamp);
        ?>
        <div class="maint-tz-val" style="color: #10b981;"><?= htmlspecialchars($utcFormatted) ?></div>
        <div class="maint-tz-pill" style="background: rgba(16,185,129,0.15); color: #10b981;">Zero-Drift ISO-8601</div>
      </div>
    </div>

    <!-- Secondary Telemetry Cards -->
    <div class="maint-grid">
      <!-- Security Shield -->
      <div class="maint-card" style="border-color: rgba(16,185,129,0.25);">
        <div class="maint-card-label">Database &amp; Data Integrity</div>
        <div style="font-size: 1.15rem; font-weight: 800; color: #10b981; display: flex; align-items: center; gap: 0.45rem;">
          <i data-lucide="shield-check" style="width: 18px; height: 18px;"></i> 100% Safe &amp; Intact
        </div>
        <div class="maint-card-sub">Zero data loss guaranteed</div>
      </div>

      <!-- Perimeter Protection -->
      <div class="maint-card" style="border-color: rgba(168,85,247,0.25);">
        <div class="maint-card-label">Perimeter Protection</div>
        <div style="font-size: 1.15rem; font-weight: 800; color: #a855f7; display: flex; align-items: center; gap: 0.45rem;">
          <i data-lucide="lock" style="width: 18px; height: 18px;"></i> Edge Shield Active
        </div>
        <div class="maint-card-sub">Layer 7 rate limiting enforced</div>
      </div>

      <!-- Auto Reconnect -->
      <div class="maint-card" style="border-color: rgba(56,189,248,0.25);">
        <div class="maint-card-label">Client Synchronization</div>
        <div style="font-size: 1.15rem; font-weight: 800; color: #38bdf8; display: flex; align-items: center; gap: 0.45rem;">
          <i data-lucide="refresh-cw" style="width: 18px; height: 18px;"></i> Auto Handshake
        </div>
        <div class="maint-card-sub">Auto reloads upon window conclusion</div>
      </div>
    </div>

    <!-- Actions & Gateway Probe -->
    <div style="display: flex; justify-content: center; align-items: center; gap: 1rem; flex-wrap: wrap;">
      <button type="button" onclick="performDiagnosticPing()" id="pingBtn" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #0284c7, #4f46e5); font-weight: 700; color: #ffffff; padding: 0.7rem 1.6rem; border-radius: 8px;">
        <i data-lucide="activity" style="width: 16px; height: 16px;"></i> Test Live Gateway
      </button>

      <button type="button" onclick="location.reload()" class="ts-btn ts-btn-outline" style="padding: 0.7rem 1.5rem; border-radius: 8px;">
        <i data-lucide="rotate-cw" style="width: 16px; height: 16px;"></i> Refresh Status
      </button>

      <a href="<?= url('/login') ?>" class="ts-btn ts-btn-outline" style="padding: 0.7rem 1.5rem; border-radius: 8px; border-color: rgba(99,102,241,0.4); color: #818cf8;">
        <i data-lucide="key" style="width: 16px; height: 16px;"></i> Staff Gateway ↗
      </a>
    </div>

    <!-- Diagnostic Ping Feedback Box -->
    <div id="pingResultBox" class="maint-ping-box"></div>

  </div>
</section>

<script>
(function() {
  const targetIsoUtc = "<?= $targetUtcIso ?>";
  const targetTimeMs = new Date(targetIsoUtc).getTime();

  // Detect visitor local timezone and format target datetime
  function renderVisitorTimezone() {
    try {
      const targetDate = new Date(targetTimeMs);
      const userTz = Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC';
      const pillElem = document.getElementById('ts_local_tz_pill');
      const displayElem = document.getElementById('ts_local_time_display');

      if (pillElem) {
        pillElem.innerText = userTz;
      }

      if (displayElem) {
        const options = {
          weekday: 'short',
          month: 'short',
          day: 'numeric',
          year: 'numeric',
          hour: '2-digit',
          minute: '2-digit',
          second: '2-digit',
          hour12: true
        };
        displayElem.innerText = targetDate.toLocaleString(undefined, options);
      }
    } catch (e) {
      const displayElem = document.getElementById('ts_local_time_display');
      if (displayElem) displayElem.innerText = new Date(targetTimeMs).toLocaleString();
    }
  }

  function pad(num) {
    return num.toString().padStart(2, '0');
  }

  function updateCountdown() {
    const now = Date.now();
    const diff = targetTimeMs - now;

    const daysElem = document.getElementById('cd_days');
    const hoursElem = document.getElementById('cd_hours');
    const minsElem = document.getElementById('cd_mins');
    const secsElem = document.getElementById('cd_secs');

    if (diff <= 0) {
      if (daysElem) daysElem.innerText = '00';
      if (hoursElem) hoursElem.innerText = '00';
      if (minsElem) minsElem.innerText = '00';
      if (secsElem) secsElem.innerText = '00';
      // Time concluded: auto-refresh page to resume normal live site
      setTimeout(function() { location.reload(); }, 1200);
      return;
    }

    const totalSeconds = Math.floor(diff / 1000);
    const days = Math.floor(totalSeconds / 86400);
    const hours = Math.floor((totalSeconds % 86400) / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = totalSeconds % 60;

    if (daysElem) daysElem.innerText = pad(days);
    if (hoursElem) hoursElem.innerText = pad(hours);
    if (minsElem) minsElem.innerText = pad(minutes);
    if (secsElem) secsElem.innerText = pad(seconds);
  }

  renderVisitorTimezone();
  updateCountdown();
  setInterval(updateCountdown, 1000);
})();

// Live diagnostic ping
async function performDiagnosticPing() {
  const btn = document.getElementById('pingBtn');
  const box = document.getElementById('pingResultBox');
  btn.disabled = true;
  btn.innerHTML = '<i data-lucide="loader" class="ts-spin" style="width: 16px; height: 16px;"></i> Pinging Gateway...';
  if (window.lucide) lucide.createIcons();

  const start = performance.now();
  try {
    const res = await fetch('<?= url('/api/v1/diagnostic-ping') ?>', { cache: 'no-store' });
    const latency = Math.round(performance.now() - start);
    box.style.display = 'block';
    if (res.ok) {
      box.innerHTML = `<span style="color: #10b981; font-weight: 700;">✓ Gateway Online</span> &bull; Latency: <strong>${latency}ms</strong> &bull; Status: HTTP ${res.status}`;
    } else {
      box.innerHTML = `<span style="color: #38bdf8; font-weight: 700;">⚡ Upgrade In Progress</span> &bull; Latency: <strong>${latency}ms</strong> &bull; Status: HTTP ${res.status}`;
    }
  } catch (err) {
    box.style.display = 'block';
    box.innerHTML = `<span style="color: #f59e0b; font-weight: 700;">⚡ Upgrade In Progress</span> &bull; Edge node executing migrations. Please standby.`;
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i data-lucide="activity" style="width: 16px; height: 16px;"></i> Test Live Gateway';
    if (window.lucide) lucide.createIcons();
  }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
