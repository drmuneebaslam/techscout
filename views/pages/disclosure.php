<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 3rem; margin-bottom: 5rem; max-width: 960px;">
  <div class="ts-card" style="background: radial-gradient(circle at 10% 20%, rgba(99,102,241,0.12), transparent 45%), var(--ts-bg-surface); border-color: rgba(99,102,241,0.3); padding: 2.5rem; margin-bottom: 2.5rem;">
    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
      <span class="ts-badge ts-badge-cyan">TRANSPARENCY</span>
      <span style="font-size: 0.8rem; color: var(--ts-text-secondary);">Last Updated: <?= $updatedAt ?></span>
    </div>
    <h1 style="font-size: 2.4rem; color: var(--ts-text-primary); margin: 0 0 0.75rem 0; font-weight: 800; font-family: 'Outfit', sans-serif;">
      Affiliate &amp; Sponsored Content Disclosure
    </h1>
    <p style="font-size: 1.05rem; color: var(--ts-text-secondary); line-height: 1.6; margin: 0;">
      Our pledge of editorial independence, FTC compliance, and transparent disclosure regarding sponsored articles and affiliate links.
    </p>
  </div>

  <div class="ts-card" style="background: var(--ts-bg-surface); border-color: var(--ts-border-main); padding: 2.5rem; line-height: 1.7; color: var(--ts-text-secondary);">
    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 0; margin-bottom: 0.75rem;">1. Editorial Independence</h2>
    <p>TechScout editorial reviews, architectural teardowns, and benchmark comparisons are conducted independently without commercial bias. We never accept financial compensation in exchange for positive product ratings or biased benchmark manipulation.</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;">2. Sponsored Content &amp; Native Advertising</h2>
    <p>Articles, newsletters, or bounties sponsored by corporate partners are always explicitly demarcated with a "SPONSORED" or "PARTNER CONTENT" badge at the top of the publication.</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem;">3. Affiliate Links</h2>
    <p>Some links to developer tools or cloud services may contain affiliate tracking codes. If you purchase through these links, TechScout may earn a small referral commission at no additional cost to you, which funds our open research and author grants.</p>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
