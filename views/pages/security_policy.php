<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 3rem; margin-bottom: 5rem; max-width: 960px;">
  <div class="ts-card" style="background: radial-gradient(circle at 10% 20%, rgba(99,102,241,0.12), transparent 45%), var(--ts-bg-surface); border-color: rgba(99,102,241,0.3); padding: 2.5rem; margin-bottom: 2.5rem;">
    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
      <span class="ts-badge ts-badge-pink">CYBER DEFENSE &amp; DISCLOSURE</span>
      <span style="font-size: 0.8rem; color: var(--ts-text-secondary);">Last Updated: <?= $updatedAt ?></span>
    </div>
    <h1 style="font-size: 2.4rem; color: var(--ts-text-primary); margin: 0 0 0.75rem 0; font-weight: 800; font-family: 'Outfit', sans-serif;">
      Security Policy &amp; Vulnerability Disclosure
    </h1>
    <p style="font-size: 1.05rem; color: var(--ts-text-secondary); line-height: 1.6; margin: 0;">
      Our commitment to application security, infrastructure hardening, and safe harbor guidelines for ethical security researchers.
    </p>
  </div>

  <div class="ts-card" style="background: var(--ts-bg-surface); border-color: var(--ts-border-main); padding: 2.5rem; line-height: 1.7; color: var(--ts-text-secondary);">
    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 0; margin-bottom: 0.75rem;">1. Coordinated Vulnerability Disclosure &amp; Safe Harbor</h2>
    <p>TechScout welcomes security researchers to test and responsibly disclose potential vulnerabilities in our systems. We promise not to pursue legal action against researchers who adhere to the following principles:</p>
    <ul style="padding-left: 1.5rem; margin-bottom: 1.5rem;">
      <li>Perform testing without compromising user privacy, corrupting production data, or degrading service availability.</li>
      <li>Give our security engineering team a reasonable window (minimum 30 days) to patch reported vulnerabilities before public disclosure.</li>
      <li>Do not execute social engineering, phishing, or physical attacks against TechScout staff or facilities.</li>
    </ul>

    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;">2. How to Submit a Vulnerability Report</h2>
    <p>Please submit full reproduction steps, proof-of-concept payloads, and impacted endpoints to: <strong>security@techscout.online</strong> (PGP key available upon request). Valid findings qualify for recognition on our Security Hall of Fame and milestone bounties.</p>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
