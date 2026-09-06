<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 3rem; margin-bottom: 5rem; max-width: 1100px;">

  <!-- Hero Header Banner -->
  <div class="ts-card" style="background: radial-gradient(circle at 10% 20%, rgba(99,102,241,0.15), transparent 45%), var(--ts-bg-surface); border-color: rgba(99,102,241,0.3); padding: 3rem 2rem; margin-bottom: 2.5rem; text-align: center;">
    <span class="ts-badge ts-badge-cyan" style="margin-bottom: 0.75rem;">TRUST, COMPLIANCE &amp; POLICIES</span>
    <h1 style="font-size: 2.6rem; color: var(--ts-text-primary); margin: 0 0 0.75rem 0; font-weight: 800; font-family: 'Outfit', sans-serif;">
      Legal &amp; Compliance Center
    </h1>
    <p style="font-size: 1.1rem; color: var(--ts-text-secondary); max-width: 720px; margin: 0 auto; line-height: 1.6;">
      Everything you need to know about our data protection standards, creator intellectual property rights, terms of service, and platform safety guidelines.
    </p>
  </div>

  <!-- Primary Foundations Grid -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 320px), 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
    
    <!-- 1. Privacy Policy -->
    <div class="ts-card" style="display: flex; flex-direction: column; justify-content: space-between; border-color: rgba(56,189,248,0.3); background: var(--ts-card-bg);">
      <div>
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
          <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(56,189,248,0.15); display: flex; align-items: center; justify-content: center; color: #38bdf8;">
            <i data-lucide="shield-check" style="width: 22px; height: 22px;"></i>
          </div>
          <span class="ts-badge ts-badge-cyan">GDPR / CCPA</span>
        </div>
        <h3 style="font-size: 1.25rem; color: var(--ts-text-primary); margin: 0 0 0.5rem 0; font-family: 'Outfit', sans-serif;">Privacy Policy</h3>
        <p style="color: var(--ts-text-secondary); font-size: 0.9rem; line-height: 1.5; margin-bottom: 1.25rem;">
          Comprehensive breakdown of what data we collect, encryption at rest, telemetric tracking, and your data ownership rights.
        </p>
      </div>
      <a href="<?= url('/privacy') ?>" class="ts-btn ts-btn-primary ts-btn-sm" style="width: 100%; justify-content: center;">
        Read Privacy Policy &rarr;
      </a>
    </div>

    <!-- 2. Terms of Service -->
    <div class="ts-card" style="display: flex; flex-direction: column; justify-content: space-between; border-color: rgba(99,102,241,0.3); background: var(--ts-card-bg);">
      <div>
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
          <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(99,102,241,0.15); display: flex; align-items: center; justify-content: center; color: #818cf8;">
            <i data-lucide="file-text" style="width: 22px; height: 22px;"></i>
          </div>
          <span class="ts-badge">USER AGREEMENT</span>
        </div>
        <h3 style="font-size: 1.25rem; color: var(--ts-text-primary); margin: 0 0 0.5rem 0; font-family: 'Outfit', sans-serif;">Terms of Service</h3>
        <p style="color: var(--ts-text-secondary); font-size: 0.9rem; line-height: 1.5; margin-bottom: 1.25rem;">
          User rights, account eligibility, acceptable platform conduct, content ownership, licensing, and warranty disclaimers.
        </p>
      </div>
      <a href="<?= url('/terms') ?>" class="ts-btn ts-btn-primary ts-btn-sm" style="width: 100%; justify-content: center;">
        Read Terms of Service &rarr;
      </a>
    </div>

    <!-- 3. DMCA & Copyright -->
    <div class="ts-card" style="display: flex; flex-direction: column; justify-content: space-between; border-color: rgba(236,72,153,0.3); background: var(--ts-card-bg);">
      <div>
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
          <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(236,72,153,0.15); display: flex; align-items: center; justify-content: center; color: #f472b6;">
            <i data-lucide="scale" style="width: 22px; height: 22px;"></i>
          </div>
          <span class="ts-badge ts-badge-pink">IP &amp; DMCA</span>
        </div>
        <h3 style="font-size: 1.25rem; color: var(--ts-text-primary); margin: 0 0 0.5rem 0; font-family: 'Outfit', sans-serif;">DMCA &amp; Copyright Policy</h3>
        <p style="color: var(--ts-text-secondary); font-size: 0.9rem; line-height: 1.5; margin-bottom: 1.25rem;">
          Notice and takedown procedure, counter-notices, intellectual property defense, and repeat infringer policies.
        </p>
      </div>
      <a href="<?= url('/copyright') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%; justify-content: center;">
        Read DMCA Policy &rarr;
      </a>
    </div>

  </div>

  <!-- Detailed Compliance Documents & Guidelines -->
  <div style="margin-bottom: 1.5rem;">
    <h2 style="font-size: 1.6rem; color: var(--ts-text-primary); margin-bottom: 0.5rem; font-family: 'Outfit', sans-serif;">All Compliance Documents &amp; Standards</h2>
    <p style="color: var(--ts-text-tertiary); font-size: 0.9rem;">Browse specific policies, accessibility compliance, and editorial integrity charters.</p>
  </div>

  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 320px), 1fr)); gap: 1.25rem;">
    
    <!-- Cookies -->
    <a href="<?= url('/cookies') ?>" class="ts-card" style="text-decoration: none; display: flex; gap: 1rem; align-items: flex-start; border-color: var(--ts-border-subtle); transition: transform 0.2s, border-color 0.2s;">
      <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(245,158,11,0.15); display: flex; align-items: center; justify-content: center; color: #f59e0b; flex-shrink: 0;">
        <i data-lucide="cookie" style="width: 18px; height: 18px;"></i>
      </div>
      <div>
        <h4 style="color: var(--ts-text-primary); margin: 0 0 0.25rem 0; font-size: 1.05rem;">Cookie Policy</h4>
        <p style="color: var(--ts-text-secondary); font-size: 0.83rem; line-height: 1.45; margin: 0;">Session tokens, local preferences, and telemetry tracking technologies.</p>
      </div>
    </a>

    <!-- Community Guidelines -->
    <a href="<?= url('/community-guidelines') ?>" class="ts-card" style="text-decoration: none; display: flex; gap: 1rem; align-items: flex-start; border-color: var(--ts-border-subtle); transition: transform 0.2s, border-color 0.2s;">
      <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(16,185,129,0.15); display: flex; align-items: center; justify-content: center; color: #10b981; flex-shrink: 0;">
        <i data-lucide="users" style="width: 18px; height: 18px;"></i>
      </div>
      <div>
        <h4 style="color: var(--ts-text-primary); margin: 0 0 0.25rem 0; font-size: 1.05rem;">Community Guidelines</h4>
        <p style="color: var(--ts-text-secondary); font-size: 0.83rem; line-height: 1.45; margin: 0;">Respectful discourse, comment etiquette, anti-harassment, and safety.</p>
      </div>
    </a>

    <!-- Security Policy -->
    <a href="<?= url('/security') ?>" class="ts-card" style="text-decoration: none; display: flex; gap: 1rem; align-items: flex-start; border-color: var(--ts-border-subtle); transition: transform 0.2s, border-color 0.2s;">
      <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(56,189,248,0.15); display: flex; align-items: center; justify-content: center; color: #38bdf8; flex-shrink: 0;">
        <i data-lucide="lock" style="width: 18px; height: 18px;"></i>
      </div>
      <div>
        <h4 style="color: var(--ts-text-primary); margin: 0 0 0.25rem 0; font-size: 1.05rem;">Security Policy</h4>
        <p style="color: var(--ts-text-secondary); font-size: 0.83rem; line-height: 1.45; margin: 0;">Infrastructure defenses, vulnerability disclosure, and bug reporting.</p>
      </div>
    </a>

    <!-- Acceptable Use -->
    <a href="<?= url('/acceptable-use') ?>" class="ts-card" style="text-decoration: none; display: flex; gap: 1rem; align-items: flex-start; border-color: var(--ts-border-subtle); transition: transform 0.2s, border-color 0.2s;">
      <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(239,68,68,0.15); display: flex; align-items: center; justify-content: center; color: #ef4444; flex-shrink: 0;">
        <i data-lucide="alert-octagon" style="width: 18px; height: 18px;"></i>
      </div>
      <div>
        <h4 style="color: var(--ts-text-primary); margin: 0 0 0.25rem 0; font-size: 1.05rem;">Acceptable Use Policy</h4>
        <p style="color: var(--ts-text-secondary); font-size: 0.83rem; line-height: 1.45; margin: 0;">Prohibited automated scraping, malicious prompts, and unauthorized abuse.</p>
      </div>
    </a>

    <!-- Accessibility -->
    <a href="<?= url('/accessibility') ?>" class="ts-card" style="text-decoration: none; display: flex; gap: 1rem; align-items: flex-start; border-color: var(--ts-border-subtle); transition: transform 0.2s, border-color 0.2s;">
      <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(168,85,247,0.15); display: flex; align-items: center; justify-content: center; color: #c084fc; flex-shrink: 0;">
        <i data-lucide="eye" style="width: 18px; height: 18px;"></i>
      </div>
      <div>
        <h4 style="color: var(--ts-text-primary); margin: 0 0 0.25rem 0; font-size: 1.05rem;">Accessibility (WCAG 2.1 AA)</h4>
        <p style="color: var(--ts-text-secondary); font-size: 0.83rem; line-height: 1.45; margin: 0;">Keyboard navigability, screen reader semantics, and contrast ratios.</p>
      </div>
    </a>

    <!-- Editorial Principles -->
    <a href="<?= url('/editorial') ?>" class="ts-card" style="text-decoration: none; display: flex; gap: 1rem; align-items: flex-start; border-color: var(--ts-border-subtle); transition: transform 0.2s, border-color 0.2s;">
      <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(14,165,233,0.15); display: flex; align-items: center; justify-content: center; color: #0284c7; flex-shrink: 0;">
        <i data-lucide="award" style="width: 18px; height: 18px;"></i>
      </div>
      <div>
        <h4 style="color: var(--ts-text-primary); margin: 0 0 0.25rem 0; font-size: 1.05rem;">Editorial Principles</h4>
        <p style="color: var(--ts-text-secondary); font-size: 0.83rem; line-height: 1.45; margin: 0;">Fact-checking standards, AI disclosure, and technical review rigor.</p>
      </div>
    </a>

    <!-- Author Guidelines -->
    <a href="<?= url('/write/guidelines') ?>" class="ts-card" style="text-decoration: none; display: flex; gap: 1rem; align-items: flex-start; border-color: var(--ts-border-subtle); transition: transform 0.2s, border-color 0.2s;">
      <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(236,72,153,0.15); display: flex; align-items: center; justify-content: center; color: #ec4899; flex-shrink: 0;">
        <i data-lucide="pen-tool" style="width: 18px; height: 18px;"></i>
      </div>
      <div>
        <h4 style="color: var(--ts-text-primary); margin: 0 0 0.25rem 0; font-size: 1.05rem;">Author Publishing Guidelines</h4>
        <p style="color: var(--ts-text-secondary); font-size: 0.83rem; line-height: 1.45; margin: 0;">Formatting rules, code snippet standards, and review workflows.</p>
      </div>
    </a>

    <!-- Affiliate Disclosure -->
    <a href="<?= url('/disclosure') ?>" class="ts-card" style="text-decoration: none; display: flex; gap: 1rem; align-items: flex-start; border-color: var(--ts-border-subtle); transition: transform 0.2s, border-color 0.2s;">
      <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(245,158,11,0.15); display: flex; align-items: center; justify-content: center; color: #f59e0b; flex-shrink: 0;">
        <i data-lucide="info" style="width: 18px; height: 18px;"></i>
      </div>
      <div>
        <h4 style="color: var(--ts-text-primary); margin: 0 0 0.25rem 0; font-size: 1.05rem;">Affiliate &amp; Sponsored Disclosure</h4>
        <p style="color: var(--ts-text-secondary); font-size: 0.83rem; line-height: 1.45; margin: 0;">Transparency regarding sponsored links, affiliate tools, and ads.</p>
      </div>
    </a>

    <!-- Help Center -->
    <a href="<?= url('/help') ?>" class="ts-card" style="text-decoration: none; display: flex; gap: 1rem; align-items: flex-start; border-color: var(--ts-border-subtle); transition: transform 0.2s, border-color 0.2s;">
      <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(99,102,241,0.15); display: flex; align-items: center; justify-content: center; color: #818cf8; flex-shrink: 0;">
        <i data-lucide="help-circle" style="width: 18px; height: 18px;"></i>
      </div>
      <div>
        <h4 style="color: var(--ts-text-primary); margin: 0 0 0.25rem 0; font-size: 1.05rem;">Help Center &amp; FAQs</h4>
        <p style="color: var(--ts-text-secondary); font-size: 0.83rem; line-height: 1.45; margin: 0;">Guides, account setup, creator payouts, and support desk tickets.</p>
      </div>
    </a>

  </div>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
