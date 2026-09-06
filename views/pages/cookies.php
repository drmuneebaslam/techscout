<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 3rem; margin-bottom: 5rem; max-width: 960px;">
  <div class="ts-card" style="background: radial-gradient(circle at 10% 20%, rgba(99,102,241,0.12), transparent 45%), var(--ts-bg-surface); border-color: rgba(99,102,241,0.3); padding: 2.5rem; margin-bottom: 2.5rem;">
    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
      <span class="ts-badge ts-badge-indigo">COMPLIANCE &amp; PRIVACY</span>
      <span style="font-size: 0.8rem; color: var(--ts-text-secondary);">Last Updated: <?= $updatedAt ?></span>
    </div>
    <h1 style="font-size: 2.4rem; color: var(--ts-text-primary); margin: 0 0 0.75rem 0; font-weight: 800; font-family: 'Outfit', sans-serif;">
      Cookie &amp; Tracking Technologies Policy
    </h1>
    <p style="font-size: 1.05rem; color: var(--ts-text-secondary); line-height: 1.6; margin: 0;">
      How TechScout utilizes essential cookies, local storage tokens, and telemetry signals to power session persistence, dark mode preferences, and DDoS prevention.
    </p>
  </div>

  <div class="ts-card" style="background: var(--ts-bg-surface); border-color: var(--ts-border-main); padding: 2.5rem; line-height: 1.7; color: var(--ts-text-secondary);">
    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 0; margin-bottom: 0.75rem;">1. What Are Cookies?</h2>
    <p>Cookies and browser local storage items are small data fragments saved onto your device that allow web applications to remember stateful preferences across HTTP requests.</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;">2. Cookie Categories Used on TechScout</h2>
    <ul style="padding-left: 1.5rem; margin-bottom: 1.5rem;">
      <li><strong>Strictly Necessary:</strong> Secure authentication tokens (`PHPSESSID`, `ts_token`) and CSRF defense headers required to log into the author studio and comment.</li>
      <li><strong>Functional Preferences:</strong> Theme switcher preference (`ts_theme` &rarr; dark/light) and collapsible sidebar state.</li>
      <li><strong>Security &amp; Rate-Limiting:</strong> Anonymous tokens used by our Cyber Firewall to distinguish legitimate developers from malicious bot swarms.</li>
    </ul>

    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;">3. Controlling Your Cookies</h2>
    <p>You can adjust your cookie preferences directly in your browser settings. Disabling functional cookies may disable persistent theme selection and automated session authentication.</p>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
