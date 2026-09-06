<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 3rem; margin-bottom: 5rem; max-width: 960px;">
  <div class="ts-card" style="background: radial-gradient(circle at 10% 20%, rgba(99,102,241,0.12), transparent 45%), var(--ts-bg-surface); border-color: rgba(99,102,241,0.3); padding: 2.5rem; margin-bottom: 2.5rem;">
    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
      <span class="ts-badge ts-badge-pink">DEVELOPER CODE OF CONDUCT</span>
      <span style="font-size: 0.8rem; color: var(--ts-text-secondary);">Last Updated: <?= $updatedAt ?></span>
    </div>
    <h1 style="font-size: 2.4rem; color: var(--ts-text-primary); margin: 0 0 0.75rem 0; font-weight: 800; font-family: 'Outfit', sans-serif;">
      Community Guidelines &amp; Code of Conduct
    </h1>
    <p style="font-size: 1.05rem; color: var(--ts-text-secondary); line-height: 1.6; margin: 0;">
      TechScout is a collaborative, intellectually rigorous community for software engineers, AI researchers, architects, and technology creators.
    </p>
  </div>

  <div class="ts-card" style="background: var(--ts-bg-surface); border-color: var(--ts-border-main); padding: 2.5rem; line-height: 1.7; color: var(--ts-text-secondary);">
    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 0; margin-bottom: 0.75rem;">1. Respectful &amp; Constructive Collaboration</h2>
    <p>We welcome spirited technical debates regarding architectural patterns, language paradigms, and AI models. However, personal harassment, condescending gatekeeping, discrimination, and hate speech are strictly prohibited and result in permanent suspension.</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;">2. Authenticity &amp; No Commercial Spam</h2>
    <p>Do not use comment sections or prompt submissions solely to drop affiliate referral links, promote token launch scams, or spam external search engines. Commercial promotion must be conducted through verified sponsorship channels.</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;">3. Safe &amp; Responsible AI Prompting</h2>
    <p>Prompts and Custom GPT agents submitted to the TechScout registry must adhere to safety benchmarks. Prompts intended to generate malware, automated exploits, non-consensual imagery, or bypass security barriers without authorization will be removed immediately.</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;">4. Reporting Violations</h2>
    <p>Any member can flag inappropriate comments, spam tools, or malicious code using the in-app reporting dialog or by emailing <a href="mailto:moderation@techscout.online" style="color: var(--ts-primary);">moderation@techscout.online</a>.</p>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
