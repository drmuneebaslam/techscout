<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 3rem; margin-bottom: 5rem; max-width: 960px;">
  <!-- Header Banner -->
  <div class="ts-card" style="background: radial-gradient(circle at 10% 20%, rgba(99,102,241,0.15), transparent 45%), var(--ts-bg-surface); border-color: rgba(99,102,241,0.3); padding: 2.5rem; margin-bottom: 2.5rem;">
    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
      <span class="ts-badge ts-badge-cyan">AUTHOR GUILD</span>
      <span style="font-size: 0.8rem; color: var(--ts-text-secondary);">Last Updated: <?= $updatedAt ?></span>
    </div>
    <h1 style="font-size: 2.4rem; color: var(--ts-text-primary); margin: 0 0 0.75rem 0; font-weight: 800; font-family: 'Outfit', sans-serif;">
      Author Publishing Guidelines &amp; Quality Standards
    </h1>
    <p style="font-size: 1.05rem; color: var(--ts-text-secondary); line-height: 1.6; margin: 0;">
      A comprehensive guide for developers, AI researchers, and engineering leaders writing technical publications on TechScout.
    </p>
  </div>

  <div class="ts-card" style="background: var(--ts-bg-surface); border-color: var(--ts-border-main); padding: 2.5rem; line-height: 1.7; color: var(--ts-text-secondary);">
    <h2 style="color: var(--ts-text-primary); font-size: 1.35rem; margin-top: 0; margin-bottom: 0.6rem;">1. Who Can Publish</h2>
    <p>Any verified author approved through our Author Application Portal (`/author/apply`) may draft and submit technical articles, AI tool benchmarks, prompt blueprints, and custom GPT models to TechScout.</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.35rem; margin-top: 2rem; margin-bottom: 0.6rem;">2. Content Requirements</h2>
    <p>Publications must focus on software engineering, distributed systems architecture, artificial intelligence, cyber security, DevOps/SRE, database internals, or frontier developer workflows. Articles must be original and provide actionable value to developers.</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.35rem; margin-top: 2rem; margin-bottom: 0.6rem;">3. Quality Standards &amp; Depth</h2>
    <p>We prioritize thorough, reproducible engineering deep dives (minimum 800–1,500 words) over superficial listicles. Include code snippets, architectural benchmarks, error handling strategies, and tradeoffs.</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.35rem; margin-top: 2rem; margin-bottom: 0.6rem;">4. Formatting &amp; Markdown Rules</h2>
    <p>Use clear hierarchical headings (H2, H3), bulleted lists, and syntax-highlighted code blocks with explicit language identifiers (e.g. ````python`, ````go`, ````sql`).</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.35rem; margin-top: 2rem; margin-bottom: 0.6rem;">5. Images, Diagrams &amp; Media</h2>
    <p>Include high-resolution architecture diagrams, Mermaid flowcharts, or system schemas. All images must have meaningful alt text for accessibility.</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.35rem; margin-top: 2rem; margin-bottom: 0.6rem;">6. Citations &amp; Academic Attribution</h2>
    <p>Directly link to original research papers (arXiv), GitHub source repositories, and official documentation when referencing third-party libraries or benchmarks.</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.35rem; margin-top: 2rem; margin-bottom: 0.6rem;">7. AI Content Policy</h2>
    <p>We permit the use of AI tools (e.g. LLMs) for outlining, grammar refinement, and research assistance. However, raw, unedited AI-generated text without human engineering review is strictly rejected.</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.35rem; margin-top: 2rem; margin-bottom: 0.6rem;">8. Copyright &amp; Plagiarism</h2>
    <p>Plagiarism results in immediate disqualification and permanent ban from the Author Guild. Ensure all published code snippets and diagrams are either original or properly licensed.</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.35rem; margin-top: 2rem; margin-bottom: 0.6rem;">9. Prohibited Content</h2>
    <p>Promotions of illegal exploit payloads, pyramid investment schemes, unverified affiliate spams, or deceptive benchmarks are strictly prohibited.</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.35rem; margin-top: 2rem; margin-bottom: 0.6rem;">10. Review Process</h2>
    <p>Submitted drafts enter our Editorial Review Queue (`/admin/content`). Our staff checks technical veracity and formatting within 24 to 48 hours.</p>

    <h2 style="color: var(--ts-text-primary); font-size: 1.35rem; margin-top: 2rem; margin-bottom: 0.6rem;">11. Publishing &amp; Distribution</h2>
    <p>Once approved, your article goes live across the home feed, relevant topic hubs, the RSS feed, and is eligible for inclusion in the TechScout Weekly Newsletter Dispatch.</p>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
