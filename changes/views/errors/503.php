<?php require __DIR__ . '/../layouts/header.php'; ?>

<style>
/* ==========================================================================
   TechScout Dual-Theme HTTP 503 Maintenance Interface [TS-INF-09]
   ========================================================================== */
.maint-page-wrap {
  min-height: 80vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 4.5rem 1rem;
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
  width: 700px;
  height: 400px;
  background: radial-gradient(circle, rgba(56, 189, 248, 0.14) 0%, rgba(99, 102, 241, 0.08) 50%, transparent 70%);
  pointer-events: none;
  z-index: 0;
}

[data-theme="light"] .maint-glow,
.theme-light .maint-glow {
  background: radial-gradient(circle, rgba(2, 132, 199, 0.12) 0%, rgba(79, 70, 229, 0.08) 50%, transparent 70%);
}

/* Status Pill */
.maint-status-pill {
  display: inline-flex;
  align-items: center;
  gap: 0.65rem;
  background: rgba(56,189,248,0.1);
  border: 1px solid rgba(56,189,248,0.35);
  padding: 0.45rem 1.25rem;
  border-radius: 9999px;
  margin-bottom: 2rem;
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
  line-height: 1.12;
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
  font-size: clamp(1rem, 2vw, 1.15rem);
  line-height: 1.65;
  max-width: 680px;
  margin: 0 auto 2.5rem auto;
}

[data-theme="light"] .maint-description,
.theme-light .maint-description {
  color: #334155 !important;
}

/* Telemetry Grid & Cards */
.maint-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.25rem;
  margin-bottom: 2.5rem;
  text-align: left;
}

.maint-card {
  background: rgba(15, 23, 42, 0.75);
  border: 1px solid rgba(56, 189, 248, 0.2);
  border-radius: 14px;
  padding: 1.4rem;
  backdrop-filter: blur(12px);
  box-shadow: 0 4px 20px rgba(0,0,0,0.3);
  transition: transform 0.2s ease, border-color 0.2s ease;
}

.maint-card:hover {
  transform: translateY(-2px);
  border-color: rgba(56, 189, 248, 0.4);
}

[data-theme="light"] .maint-card,
.theme-light .maint-card {
  background: #ffffff !important;
  border: 1px solid #cbd5e1 !important;
  box-shadow: 0 4px 20px rgba(0,0,0,0.06);
}

.maint-card-label {
  font-size: 0.74rem;
  color: #94a3b8;
  text-transform: uppercase;
  font-weight: 700;
  letter-spacing: 0.05em;
  margin-bottom: 0.4rem;
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

[data-theme="light"] .maint-card-sub,
.theme-light .maint-card-sub {
  color: #64748b !important;
}

/* Feedback box */
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
      <?= !empty($customMessage) ? htmlspecialchars($customMessage) : 'TechScout engineering systems are currently executing scheduled database optimizations, decentralized ledger upgrades, and infrastructure scaling.' ?>
    </p>

    <!-- Interactive Countdown & Live Telemetry Grid -->
    <div class="maint-grid">
      <!-- 1. Recovery Countdown -->
      <div class="maint-card">
        <div class="maint-card-label">Estimated Recovery</div>
        <div id="maintTimer" style="font-size: 1.6rem; font-weight: 800; color: #38bdf8; font-family: var(--ts-font-mono, monospace);">
          00:14:42
        </div>
        <div class="maint-card-sub">Automatic reconnect when online</div>
      </div>

      <!-- 2. Security Shield -->
      <div class="maint-card" style="border-color: rgba(16,185,129,0.25);">
        <div class="maint-card-label">Database &amp; Drafts</div>
        <div style="font-size: 1.2rem; font-weight: 800; color: #10b981; display: flex; align-items: center; gap: 0.45rem;">
          <i data-lucide="shield-check" style="width: 18px; height: 18px;"></i> 100% Safe &amp; Intact
        </div>
        <div class="maint-card-sub">Zero data loss guaranteed</div>
      </div>

      <!-- 3. Gateway Protection -->
      <div class="maint-card" style="border-color: rgba(168,85,247,0.25);">
        <div class="maint-card-label">Perimeter Protection</div>
        <div style="font-size: 1.2rem; font-weight: 800; color: #a855f7; display: flex; align-items: center; gap: 0.45rem;">
          <i data-lucide="lock" style="width: 18px; height: 18px;"></i> Edge Shield Active
        </div>
        <div class="maint-card-sub">L7 rate limiting enforced</div>
      </div>
    </div>

    <!-- Actions & Ping HUD -->
    <div style="display: flex; justify-content: center; align-items: center; gap: 1rem; flex-wrap: wrap;">
      <button type="button" onclick="performDiagnosticPing()" id="pingBtn" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #0284c7, #4f46e5); font-weight: 700; color: #ffffff; padding: 0.7rem 1.6rem; border-radius: 8px;">
        <i data-lucide="activity" style="width: 16px; height: 16px;"></i> Test Live Gateway
      </button>

      <button type="button" onclick="location.reload()" class="ts-btn ts-btn-outline" style="padding: 0.7rem 1.5rem; border-radius: 8px;">
        <i data-lucide="rotate-cw" style="width: 16px; height: 16px;"></i> Refresh Page
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
// Animated countdown timer
var secondsLeft = <?= (isset($countdownMinutes) && (int)$countdownMinutes > 0) ? ((int)$countdownMinutes * 60) : (45 * 60) ?>;
function updateMaintTimer() {
  const mins = Math.floor(secondsLeft / 60);
  const secs = secondsLeft % 60;
  const timerElem = document.getElementById('maintTimer');
  if (timerElem) {
    timerElem.innerText = `00:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
  }
  if (secondsLeft > 0) {
    secondsLeft--;
  } else {
    location.reload();
  }
}
setInterval(updateMaintTimer, 1000);

// Live diagnostic ping
async function performDiagnosticPing() {
  const btn = document.getElementById('pingBtn');
  const box = document.getElementById('pingResultBox');
  btn.disabled = true;
  btn.innerHTML = '<i data-lucide="loader" class="ts-spin" style="width: 16px; height: 16px;"></i> Pinging Gateway...';
  if (window.lucide) lucide.createIcons();

  const start = performance.now();
  try {
    const res = await fetch('<?= url('/api/v1/search?q=ping') ?>');
    const latency = Math.round(performance.now() - start);
    box.style.display = 'block';
    box.innerHTML = `<span style="color: #10b981; font-weight: 700;">✓ Gateway Online</span> &bull; Latency: <strong>${latency}ms</strong> &bull; Status: Core node active`;
  } catch (err) {
    box.style.display = 'block';
    box.innerHTML = `<span style="color: #f59e0b; font-weight: 700;">⚡ Upgrade In Progress</span> &bull; Standby while maintenance procedures conclude.`;
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i data-lucide="activity" style="width: 16px; height: 16px;"></i> Test Live Gateway';
    if (window.lucide) lucide.createIcons();
  }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
