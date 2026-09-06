<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 3rem; margin-bottom: 5rem; max-width: 960px;">
  <div class="ts-card" style="background: radial-gradient(circle at 10% 20%, rgba(99,102,241,0.12), transparent 45%), var(--ts-bg-surface); border-color: rgba(99,102,241,0.3); padding: 2.5rem; margin-bottom: 2.5rem;">
    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
      <span class="ts-badge ts-badge-amber">SECURITY &amp; USAGE</span>
      <span style="font-size: 0.8rem; color: var(--ts-text-secondary);">Last Updated: <?= $updatedAt ?></span>
    </div>
    <h1 style="font-size: 2.4rem; color: var(--ts-text-primary); margin: 0 0 0.75rem 0; font-weight: 800; font-family: 'Outfit', sans-serif;">
      Acceptable Use Policy
    </h1>
    <p style="font-size: 1.05rem; color: var(--ts-text-secondary); line-height: 1.6; margin: 0;">
      Rules and operational boundaries governing the use of the TechScout platform, API endpoints, and community systems.
    </p>
  </div>

  <div class="ts-card" style="background: var(--ts-bg-surface); border-color: var(--ts-border-main); padding: 2.5rem; line-height: 1.7; color: var(--ts-text-secondary);">
    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 0; margin-bottom: 0.75rem;">1. Prohibited Network &amp; Server Activities</h2>
    <p>Users and automated systems are strictly forbidden from:</p>
    <ul style="padding-left: 1.5rem; margin-bottom: 1.5rem;">
      <li>Conducting Layer 7 denial-of-service (DoS/DDoS) floods or abusive API polling outside published rate limits.</li>
      <li>Distributing malicious binaries, remote access trojans (RATs), ransomware, or obfuscated crypto drainers.</li>
      <li>Attempting privilege escalation, SQL injection, cross-site scripting (XSS), or exploiting zero-day vulnerabilities without responsible disclosure.</li>
    </ul>

    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;">2. Automated Scraping &amp; Crawler Limits</h2>
    <p>Automated indexers must respect `robots.txt` directives and throttle requests to a maximum of 2 requests per second. Aggressive scraping that degrades system responsiveness will trigger automated IP banishment in the Cyber Firewall.</p>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
