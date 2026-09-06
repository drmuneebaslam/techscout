<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 3rem; margin-bottom: 5rem; max-width: 960px;">
  <!-- Hero Banner -->
  <div class="ts-card" style="background: radial-gradient(circle at 10% 20%, rgba(99,102,241,0.12), transparent 45%), var(--ts-bg-surface); border-color: rgba(99,102,241,0.3); padding: 2.5rem; margin-bottom: 2.5rem;">
    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
      <span class="ts-badge ts-badge-indigo">LEGAL &amp; COMPLIANCE</span>
      <span style="font-size: 0.8rem; color: var(--ts-text-secondary);">Last Updated: <?= $updatedAt ?></span>
    </div>
    <h1 style="font-size: 2.4rem; color: var(--ts-text-primary); margin: 0 0 0.75rem 0; font-weight: 800; font-family: 'Outfit', sans-serif;">
      Privacy Policy &amp; Data Protection
    </h1>
    <p style="font-size: 1.05rem; color: var(--ts-text-secondary); line-height: 1.6; margin: 0;">
      TechScout is dedicated to safeguarding developer privacy, encrypted data governance, GDPR compliance, and transparent telemetry handling across our global engineering network.
    </p>
  </div>

  <div class="ts-card" style="background: var(--ts-bg-surface); border-color: var(--ts-border-main); padding: 2.5rem; line-height: 1.7; color: var(--ts-text-secondary);">
    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 0; margin-bottom: 0.75rem;">1. Information We Collect</h2>
    <p>We collect information directly from you when you register an account, publish articles, submit AI tools or prompts, subscribe to our engineering newsletters, or interact with developer challenges:</p>
    <ul style="padding-left: 1.5rem; margin-bottom: 1.5rem;">
      <li><strong>Account Credentials:</strong> Name, username, email address, password hashes (Argon2id/Bcrypt), avatar, and biographical headlines.</li>
      <li><strong>Creator Data:</strong> Published articles, code repositories, prompt templates, GPT configurations, and verified social handles.</li>
      <li><strong>Telemetry &amp; Usage:</strong> IP address, browser user-agent, read time, bookmarked entries, and interaction telemetry.</li>
    </ul>

    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;">2. How We Use Collected Data</h2>
    <p>Your data is processed strictly for legitimate platform purposes:</p>
    <ul style="padding-left: 1.5rem; margin-bottom: 1.5rem;">
      <li>Delivering the personalized "For You" technical reading radar and topic recommendations.</li>
      <li>Distributing weekly technical digests, security alerts, and creator payout notifications.</li>
      <li>Protecting platform infrastructure against DDoS attacks, automated scraping, and unauthorized intrusions via our Cyber Firewall.</li>
      <li>Calculating author view counts and disbursement of creator revenue rewards.</li>
    </ul>

    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;">3. Data Sharing &amp; Third-Party Services</h2>
    <p>TechScout never sells personal developer information to third-party data brokers. We only transmit minimal required payload data to vetted infrastructure providers (e.g. Cloudflare for DDoS protection, Stripe/PayPal for creator payouts, and Resend/Amazon SES for transactional emails).</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;">4. Your Rights (GDPR &amp; CCPA)</h2>
    <p>Regardless of your geographic location, you retain full ownership of your data, including the right to request a complete machine-readable export (JSON) of your account data or invoke account erasure by contacting <a href="mailto:privacy@techscout.online" style="color: var(--ts-primary);">privacy@techscout.online</a>.</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;">5. Security &amp; Encryption Standards</h2>
    <p>All traffic is strictly routed over TLS 1.3 encryption. Passwords and sensitive credentials are encrypted using modern key-derivation algorithms with automated rate-limiting.</p>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
