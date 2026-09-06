<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 3rem; margin-bottom: 5rem; max-width: 960px;">
  <!-- Header Banner -->
  <div class="ts-card" style="background: radial-gradient(circle at 10% 20%, rgba(99,102,241,0.15), transparent 45%), var(--ts-bg-surface); border-color: rgba(99,102,241,0.3); padding: 2.5rem; margin-bottom: 2.5rem;">
    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
      <span class="ts-badge ts-badge-indigo">JOURNALISTIC &amp; TECHNICAL INTEGRITY</span>
      <span style="font-size: 0.8rem; color: var(--ts-text-secondary);">Last Updated: <?= $updatedAt ?></span>
    </div>
    <h1 style="font-size: 2.4rem; color: var(--ts-text-primary); margin: 0 0 0.75rem 0; font-weight: 800; font-family: 'Outfit', sans-serif;">
      Editorial Standards &amp; Principles
    </h1>
    <p style="font-size: 1.05rem; color: var(--ts-text-secondary); line-height: 1.6; margin: 0;">
      Our institutional policies regarding technical veracity, fact-checking, AI disclosure, and independent hardware/software reviews.
    </p>
  </div>

  <div class="ts-card" style="background: var(--ts-bg-surface); border-color: var(--ts-border-main); padding: 2.5rem; line-height: 1.7; color: var(--ts-text-secondary);">
    <h2 style="color: var(--ts-text-primary); font-size: 1.35rem; margin-top: 0; margin-bottom: 0.6rem;">1. Editorial Standards</h2>
    <p>TechScout maintains an uncompromising commitment to technical precision. Our editors and peer reviewers verify code execution, test architectural assertions, and ensure that published deep dives meet professional engineering standards.</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.35rem; margin-top: 2rem; margin-bottom: 0.6rem;">2. Content Guidelines</h2>
    <p>Articles must serve developers first. We prioritize clarity, reproducible benchmarks, concrete architectural diagrams, and actionable configuration scripts over vague opinions or marketing hype.</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.35rem; margin-top: 2rem; margin-bottom: 0.6rem;">3. Fact-Checking &amp; Verification Protocol</h2>
    <p>Benchmark numbers, latency measurements, and security assessments are independently validated against official cloud documentation, research papers, and public GitHub repositories.</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.35rem; margin-top: 2rem; margin-bottom: 0.6rem;">4. Corrections Policy</h2>
    <p>When factual or code errors occur, we promptly issue transparent corrections. A clear "CORRECTION" note is appended to the top of the article detailing what was modified and when.</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.35rem; margin-top: 2rem; margin-bottom: 0.6rem;">5. AI Content Policy</h2>
    <p>We mandate transparency regarding generative AI. Authors who use AI models for code generation or structuring must disclose this, and are held 100% accountable for the accuracy of the final published code.</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.35rem; margin-top: 2rem; margin-bottom: 0.6rem;">6. Product &amp; Tool Review Policy</h2>
    <p>Tools in our AI Directory and Software Radar are benchmarked under real-world developer workloads. Positive reviews cannot be purchased.</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.35rem; margin-top: 2rem; margin-bottom: 0.6rem;">7. Sponsored Content Policy</h2>
    <p>Sponsored partner publications undergo the same editorial review for technical substance as regular articles and are explicitly tagged with clear sponsored labels.</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.35rem; margin-top: 2rem; margin-bottom: 0.6rem;">8. Disclosure &amp; Conflict of Interest Policy</h2>
    <p>Authors and staff must disclose any commercial affiliations, token holdings, or advisory roles associated with technologies or companies featured in their publications.</p>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
