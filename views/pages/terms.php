<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 3rem; margin-bottom: 5rem; max-width: 960px;">
  <div class="ts-card" style="background: radial-gradient(circle at 10% 20%, rgba(99,102,241,0.12), transparent 45%), var(--ts-bg-surface); border-color: rgba(99,102,241,0.3); padding: 2.5rem; margin-bottom: 2.5rem;">
    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
      <span class="ts-badge ts-badge-indigo">LEGAL AGREEMENT</span>
      <span style="font-size: 0.8rem; color: var(--ts-text-secondary);">Last Updated: <?= $updatedAt ?></span>
    </div>
    <h1 style="font-size: 2.4rem; color: var(--ts-text-primary); margin: 0 0 0.75rem 0; font-weight: 800; font-family: 'Outfit', sans-serif;">
      Terms of Service
    </h1>
    <p style="font-size: 1.05rem; color: var(--ts-text-secondary); line-height: 1.6; margin: 0;">
      By accessing or participating in the TechScout developer network, AI tool registry, prompt ecosystem, or author studio, you agree to these Terms of Service.
    </p>
  </div>

  <div class="ts-card" style="background: var(--ts-bg-surface); border-color: var(--ts-border-main); padding: 2.5rem; line-height: 1.7; color: var(--ts-text-secondary);">
    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 0; margin-bottom: 0.75rem;">1. User Accounts &amp; Conduct</h2>
    <p>You are responsible for maintaining the confidentiality of your credentials and all actions taken under your account. You agree not to upload malicious exploits, deceptive software, unsolicited commercial spam, or execute unauthorized scanning against the TechScout infrastructure.</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;">2. Intellectual Property &amp; Content Ownership</h2>
    <p>Authors retain 100% full intellectual property and copyright ownership of their original publications, code snippets, prompt templates, and AI agents. By submitting content to TechScout, you grant us a non-exclusive, worldwide, royalty-free license to distribute, index, and cache your publication across the network and syndication feeds.</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;">3. Creator Monetization &amp; Payout Terms</h2>
    <p>Authors enrolled in the Creator Monetization Program receive programmatic revenue disbursements based on verified read time and milestone grants. TechScout reserves the right to withhold rewards if fraudulent view inflation or automated bot traffic is detected.</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;">4. Disclaimer of Warranties &amp; Limitation of Liability</h2>
    <p>TechScout is provided on an "as is" and "as available" basis. Code snippets, prompts, architectural designs, and software tools published on TechScout are provided for informational and educational purposes without warranty of any kind.</p>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
