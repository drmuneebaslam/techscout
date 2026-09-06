<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 3rem; margin-bottom: 5rem; max-width: 960px;">
  <div class="ts-card" style="background: radial-gradient(circle at 10% 20%, rgba(99,102,241,0.12), transparent 45%), var(--ts-bg-surface); border-color: rgba(99,102,241,0.3); padding: 2.5rem; margin-bottom: 2.5rem;">
    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
      <span class="ts-badge ts-badge-cyan">INTELLECTUAL PROPERTY</span>
      <span style="font-size: 0.8rem; color: var(--ts-text-secondary);">Last Updated: <?= $updatedAt ?></span>
    </div>
    <h1 style="font-size: 2.4rem; color: var(--ts-text-primary); margin: 0 0 0.75rem 0; font-weight: 800; font-family: 'Outfit', sans-serif;">
      DMCA &amp; Copyright Policy
    </h1>
    <p style="font-size: 1.05rem; color: var(--ts-text-secondary); line-height: 1.6; margin: 0;">
      TechScout respects intellectual property rights and complies with the Digital Millennium Copyright Act (17 U.S.C. § 512).
    </p>
  </div>

  <div class="ts-card" style="background: var(--ts-bg-surface); border-color: var(--ts-border-main); padding: 2.5rem; line-height: 1.7; color: var(--ts-text-secondary);">
    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 0; margin-bottom: 0.75rem;">1. Filing a Notice of Infringement (Takedown Notice)</h2>
    <p>If you believe that copyrighted material belonging to you or an entity you represent has been reproduced on TechScout without authorization, submit a written notification containing:</p>
    <ul style="padding-left: 1.5rem; margin-bottom: 1.5rem;">
      <li>Identification of the copyrighted work claimed to have been infringed.</li>
      <li>The exact URL of the material on TechScout that is to be removed or disabled.</li>
      <li>Your full contact information including physical address, telephone number, and official email.</li>
      <li>A statement that you have a good faith belief that the disputed use is not authorized by the copyright owner, its agent, or the law.</li>
      <li>A physical or electronic signature of the authorized agent.</li>
    </ul>

    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;">2. Designated Copyright Agent Contact</h2>
    <p>Send all formal DMCA notices to: <strong>dmca@techscout.online</strong> (Subject: DMCA Takedown Notice &bull; Attention: Legal &amp; Copyright Department).</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;">3. Counter-Notification Procedure</h2>
    <p>If you believe your content was removed erroneously or through misidentification, you may submit a formal counter-notification under 17 U.S.C. § 512(g)(3).</p>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
