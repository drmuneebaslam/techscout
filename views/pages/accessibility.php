<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 3rem; margin-bottom: 5rem; max-width: 960px;">
  <div class="ts-card" style="background: radial-gradient(circle at 10% 20%, rgba(99,102,241,0.12), transparent 45%), var(--ts-bg-surface); border-color: rgba(99,102,241,0.3); padding: 2.5rem; margin-bottom: 2.5rem;">
    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
      <span class="ts-badge ts-badge-green">INCLUSIVE ACCESSIBILITY</span>
      <span style="font-size: 0.8rem; color: var(--ts-text-secondary);">Last Updated: <?= $updatedAt ?></span>
    </div>
    <h1 style="font-size: 2.4rem; color: var(--ts-text-primary); margin: 0 0 0.75rem 0; font-weight: 800; font-family: 'Outfit', sans-serif;">
      Accessibility Statement (WCAG 2.1 AA)
    </h1>
    <p style="font-size: 1.05rem; color: var(--ts-text-secondary); line-height: 1.6; margin: 0;">
      TechScout is engineered to ensure seamless digital accessibility for developers and researchers of all abilities across mobile and desktop environments.
    </p>
  </div>

  <div class="ts-card" style="background: var(--ts-bg-surface); border-color: var(--ts-border-main); padding: 2.5rem; line-height: 1.7; color: var(--ts-text-secondary);">
    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 0; margin-bottom: 0.75rem;">1. Standards &amp; Conformance</h2>
    <p>We actively strive to adhere to the Web Content Accessibility Guidelines (WCAG) 2.1 Level AA conformance criteria established by the W3C Web Accessibility Initiative.</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;">2. Platform Features for Accessibility</h2>
    <ul style="padding-left: 1.5rem; margin-bottom: 1.5rem;">
      <li><strong>Keyboard Navigation:</strong> Full keyboard accessibility across search modals (Cmd+K / Ctrl+K), tab navigation, and author editor ribbons.</li>
      <li><strong>Contrast &amp; Themes:</strong> High-contrast color tokens and instant Light/Dark mode toggling designed to prevent visual strain.</li>
      <li><strong>Screen Readers:</strong> Semantic HTML5 landmarks (`<header>`, `<nav>`, `<main>`, `<footer>`, `<aside>`), descriptive ARIA labels, and structured heading hierarchies.</li>
      <li><strong>Code Readability:</strong> High-legibility monospaced typography with customizable syntax contrast.</li>
    </ul>

    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;">3. Feedback &amp; Assistance</h2>
    <p>If you encounter an accessibility barrier on any part of our platform, please reach out to our accessibility working group at <a href="mailto:accessibility@techscout.online" style="color: var(--ts-primary);">accessibility@techscout.online</a>.</p>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
