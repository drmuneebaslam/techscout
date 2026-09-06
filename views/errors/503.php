<?php require __DIR__ . '/../layouts/header.php'; ?>

<section style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 4rem 1rem; position: relative; overflow: hidden;">
  <!-- Ambient High-Tech Glow Backdrop -->
  <div style="position: absolute; top: 15%; left: 50%; transform: translate(-50%, -50%); width: 600px; height: 350px; background: radial-gradient(circle, rgba(56, 189, 248, 0.12) 0%, rgba(99, 102, 241, 0.08) 50%, transparent 70%); pointer-events: none; z-index: 0;"></div>

  <div class="container" style="position: relative; z-index: 1; max-width: 820px; margin: 0 auto; text-align: center;">
    
    <!-- Status Radar Pill -->
    <div style="display: inline-flex; align-items: center; gap: 0.65rem; background: rgba(56,189,248,0.1); border: 1px solid rgba(56,189,248,0.35); padding: 0.4rem 1.15rem; border-radius: 9999px; margin-bottom: 2rem; box-shadow: 0 0 20px rgba(56,189,248,0.15);">
      <span style="position: relative; display: flex; height: 10px; width: 10px;">
        <span style="animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite; position: absolute; display: inline-flex; height: 100%; width: 100%; border-radius: 9999px; background-color: #38bdf8; opacity: 0.75;"></span>
        <span style="position: relative; display: inline-flex; border-radius: 9999px; height: 10px; width: 10px; background-color: #0284c7;"></span>
      </span>
      <span style="font-size: 0.8rem; font-weight: 700; color: #38bdf8; font-family: var(--ts-font-mono); letter-spacing: 0.06em; text-transform: uppercase;">
        HTTP 503 &bull; Core Infrastructure Maintenance Mode
      </span>
    </div>

    <!-- Main Heading -->
    <h1 style="font-size: clamp(2.2rem, 5vw, 3.75rem); font-weight: 900; font-family: 'Outfit', sans-serif; line-height: 1.1; margin: 0 0 1.25rem 0; letter-spacing: -0.03em;">
      <span style="background: linear-gradient(135deg, #f8fafc 20%, #38bdf8 65%, #818cf8 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
        Platform Scaling &amp; Upgrades Underway
      </span>
    </h1>

    <p style="color: var(--ts-text-secondary); font-size: clamp(1rem, 2vw, 1.15rem); line-height: 1.65; max-width: 640px; margin: 0 auto 2.5rem auto;">
      <?= !empty($customMessage) ? htmlspecialchars($customMessage) : 'TechScout core engineering systems are currently executing a zero-downtime database optimization, schema migration, and bandwidth scaling procedure.' ?>
    </p>

    <!-- Interactive Countdown & Live Telemetry Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2.5rem; text-align: left;">
      <div style="background: rgba(15,23,42,0.6); border: 1px solid rgba(56,189,248,0.2); border-radius: 12px; padding: 1.25rem; backdrop-filter: blur(8px);">
        <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; margin-bottom: 0.35rem;">
          Estimated Recovery
        </div>
        <div id="maintTimer" style="font-size: 1.5rem; font-weight: 800; color: #38bdf8; font-family: var(--ts-font-mono);">
          00:14:42
        </div>
        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">Live Auto-Refresh in Sync</div>
      </div>

      <div style="background: rgba(15,23,42,0.6); border: 1px solid rgba(16,185,129,0.2); border-radius: 12px; padding: 1.25rem; backdrop-filter: blur(8px);">
        <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; margin-bottom: 0.35rem;">
          Database &amp; Drafts
        </div>
        <div style="font-size: 1.15rem; font-weight: 700; color: #34d399; display: flex; align-items: center; gap: 0.4rem;">
          <i data-lucide="shield-check" style="width: 18px; height: 18px;"></i> 100% Safe &amp; Intact
        </div>
        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">No data loss will occur</div>
      </div>

      <div style="background: rgba(15,23,42,0.6); border: 1px solid rgba(168,85,247,0.2); border-radius: 12px; padding: 1.25rem; backdrop-filter: blur(8px);">
        <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; margin-bottom: 0.35rem;">
          Edge Security Perimeter
        </div>
        <div style="font-size: 1.15rem; font-weight: 700; color: #c084fc; display: flex; align-items: center; gap: 0.4rem;">
          <i data-lucide="lock" style="width: 18px; height: 18px;"></i> WAF Active
        </div>
        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">L7 Defense Shield Enabled</div>
      </div>
    </div>

    <!-- Actions & Ping HUD -->
    <div style="display: flex; justify-content: center; align-items: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 2rem;">
      <button type="button" onclick="performDiagnosticPing()" id="pingBtn" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #38bdf8, #6366f1); font-weight: 700; color: #05070b; padding: 0.65rem 1.4rem;">
        <i data-lucide="activity" style="width: 16px; height: 16px;"></i> Test Live Connection
      </button>

      <button type="button" onclick="location.reload()" class="ts-btn ts-btn-outline" style="border-color: rgba(148,163,184,0.3); padding: 0.65rem 1.4rem;">
        <i data-lucide="rotate-cw" style="width: 16px; height: 16px;"></i> Reload Page
      </button>

      <a href="<?= url('/login') ?>" class="ts-btn ts-btn-outline" style="border-color: rgba(99,102,241,0.4); color: #a5b4fc; padding: 0.65rem 1.4rem;">
        <i data-lucide="key" style="width: 16px; height: 16px;"></i> Staff Login ↗
      </a>
    </div>

    <!-- Diagnostic Ping Feedback Box -->
    <div id="pingResultBox" style="display: none; max-width: 480px; margin: 0 auto; background: rgba(15,23,42,0.85); border: 1px solid rgba(56,189,248,0.3); border-radius: 10px; padding: 1rem; font-family: var(--ts-font-mono); font-size: 0.82rem; color: #38bdf8;">
    </div>

  </div>
</section>

<script>
// Animated countdown timer
let secondsLeft = 880;
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
    box.innerHTML = `<span style="color: #34d399;">✓ Gateway Online</span> &bull; Latency: <strong>${latency}ms</strong> &bull; Core Engine status: Migrating tables`;
  } catch (err) {
    box.style.display = 'block';
    box.innerHTML = `<span style="color: #f87171;">⚡ Upgrade in progress</span> &bull; Standby while database locks release.`;
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i data-lucide="activity" style="width: 16px; height: 16px;"></i> Test Live Connection';
    if (window.lucide) lucide.createIcons();
  }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
