<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 3rem; margin-bottom: 5rem; max-width: 1000px;">
  <!-- Header Hero Banner -->
  <div class="ts-card" style="background: radial-gradient(circle at 10% 20%, rgba(99,102,241,0.15), transparent 45%), var(--ts-bg-surface); border-color: rgba(99,102,241,0.3); padding: 3rem 2rem; margin-bottom: 2.5rem; text-align: center;">
    <span class="ts-badge ts-badge-pink" style="margin-bottom: 0.75rem;">THE TECHSCOUT MANIFESTO</span>
    <h1 style="font-size: 2.6rem; color: var(--ts-text-primary); margin: 0 0 0.75rem 0; font-weight: 800; font-family: 'Outfit', sans-serif;">
      Frontier Intelligence for Builders &amp; Creators
    </h1>
    <p style="font-size: 1.15rem; color: var(--ts-text-secondary); max-width: 720px; margin: 0 auto; line-height: 1.6;">
      TechScout was founded to cut through the noise of modern technology, delivering deep technical architectures, verified AI tools, prompt blueprints, and developer bounties.
    </p>
  </div>

  <!-- Live Ecosystem Metrics -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 180px), 1fr)); gap: 1.25rem; margin-bottom: 2.5rem;">
    <div class="ts-card" style="background: var(--ts-bg-surface); border-color: var(--ts-border-main); padding: 1.5rem; text-align: center;">
      <div style="font-size: 2rem; font-weight: 800; color: #38bdf8; font-family: 'Outfit', sans-serif;"><?= number_format($stats['articles']) ?>+</div>
      <div style="font-size: 0.85rem; color: var(--ts-text-secondary); margin-top: 0.25rem;">Technical Publications</div>
    </div>
    <div class="ts-card" style="background: var(--ts-bg-surface); border-color: var(--ts-border-main); padding: 1.5rem; text-align: center;">
      <div style="font-size: 2rem; font-weight: 800; color: #10b981; font-family: 'Outfit', sans-serif;"><?= number_format($stats['authors']) ?>+</div>
      <div style="font-size: 0.85rem; color: var(--ts-text-secondary); margin-top: 0.25rem;">Verified Engineers</div>
    </div>
    <div class="ts-card" style="background: var(--ts-bg-surface); border-color: var(--ts-border-main); padding: 1.5rem; text-align: center;">
      <div style="font-size: 2rem; font-weight: 800; color: #ec4899; font-family: 'Outfit', sans-serif;"><?= number_format($stats['tools']) ?>+</div>
      <div style="font-size: 0.85rem; color: var(--ts-text-secondary); margin-top: 0.25rem;">Curated AI Tools</div>
    </div>
    <div class="ts-card" style="background: var(--ts-bg-surface); border-color: var(--ts-border-main); padding: 1.5rem; text-align: center;">
      <div style="font-size: 2rem; font-weight: 800; color: #a855f7; font-family: 'Outfit', sans-serif;"><?= $stats['readers'] ?></div>
      <div style="font-size: 0.85rem; color: var(--ts-text-secondary); margin-top: 0.25rem;">Monthly Dev Readers</div>
    </div>
  </div>

  <div class="ts-card" style="background: var(--ts-bg-surface); border-color: var(--ts-border-main); padding: 2.5rem; line-height: 1.75; color: var(--ts-text-secondary);">
    <!-- 1. What is TechScout -->
    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 0; margin-bottom: 0.6rem;">1. What is TechScout</h2>
    <p>TechScout is a next-generation technical publishing and discovery platform. We unify in-depth software engineering articles, open-source architectures, AI model benchmarks, prompt engineering libraries, custom GPT models, and verified job boards into a fast, single-domain hub.</p>

    <!-- 2. Mission -->
    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.6rem;">2. Our Mission</h2>
    <p>To empower builders worldwide with high-signal engineering intelligence, transparent benchmarks, and a meritocratic creator economy that directly rewards technical authors.</p>

    <!-- 3. Vision -->
    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.6rem;">3. Our Vision</h2>
    <p>We envision a decentralized knowledge collective where the world's best system architects and AI researchers collaborate, share reproducible blueprints, and advance computing technology openly.</p>

    <!-- 4. Values -->
    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.6rem;">4. Core Values</h2>
    <ul style="padding-left: 1.5rem; margin-bottom: 1.5rem;">
      <li><strong>Signal Over Noise:</strong> Rigorous technical depth over shallow hype.</li>
      <li><strong>Builder Empowerment:</strong> Authors own 100% of their content and earn direct revenue rewards.</li>
      <li><strong>Uncompromised Security &amp; Speed:</strong> Sub-second response times, TLS 1.3 encryption, and accessible UI design.</li>
      <li><strong>Open Collaboration:</strong> Free public access to knowledge, prompt blueprints, and tool radars.</li>
    </ul>

    <!-- 5. Story -->
    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.6rem;">5. Our Story</h2>
    <p>TechScout was born out of frustration with fragmented developer platforms cluttered with invasive ads, paywalls, and low-quality syndicated content. We engineered a unified, lightning-fast platform designed from first principles for modern developers.</p>

    <!-- 6. Team -->
    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.6rem;">6. The Team &amp; Community Guild</h2>
    <p>TechScout is maintained by a distributed collective of open-source contributors, infrastructure engineers, security auditors, and technical editors across San Francisco, London, Berlin, Singapore, and Tokyo.</p>

    <!-- 7. Editorial Principles -->
    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.6rem;">7. Editorial Principles</h2>
    <p>Every publication on TechScout adheres to our strict <a href="<?= url('/editorial') ?>" style="color: var(--ts-primary);">Editorial Standards</a>: independent benchmarking, verifiable assertions, and transparent AI disclosure.</p>

    <!-- 8. Contact -->
    <h2 style="color: var(--ts-text-primary); font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.6rem;">8. Contact Us</h2>
    <p>For inquiries, partnerships, security reports, or author support:</p>
    <ul style="padding-left: 1.5rem; margin-bottom: 0;">
      <li><strong>General Inquiries:</strong> <a href="mailto:hello@techscout.online" style="color: var(--ts-primary);">hello@techscout.online</a></li>
      <li><strong>Editorial &amp; Submissions:</strong> <a href="mailto:editorial@techscout.online" style="color: var(--ts-primary);">editorial@techscout.online</a></li>
      <li><strong>Security &amp; Bug Bounty:</strong> <a href="mailto:security@techscout.online" style="color: var(--ts-primary);">security@techscout.online</a></li>
      <li><strong>Helpdesk Ticket Portal:</strong> <a href="<?= url('/support') ?>" style="color: var(--ts-primary);">techscout.online/support</a></li>
    </ul>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
