<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-main">
  <!-- Topbar -->
  <div class="admin-topbar">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-cyan">SEARCH INTELLIGENCE &bull; SCHEMA.ORG</span>
        <span style="font-size: 0.8rem; color: #64748b;">Dynamic Sitemap Generation, Robots.txt &amp; OpenGraph Controls</span>
      </div>
      <h1 style="font-size: 1.6rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        SEO Studio &amp; Search Indexing
      </h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <a href="<?= url('/sitemap.xml') ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8;">
        <i data-lucide="file-code"></i> View Dynamic sitemap.xml ↗
      </a>
      <a href="<?= url('/robots.txt') ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm">
        <i data-lucide="file-text"></i> View robots.txt ↗
      </a>
    </div>
  </div>

  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Robots.txt Live Editor -->
    <div class="admin-table-wrap" style="padding: 1.75rem;">
      <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
        <i data-lucide="bot" style="color: #38bdf8;"></i> Dynamic robots.txt Configurator
      </h3>
      <p style="color: #64748b; font-size: 0.82rem; margin: 0 0 1rem 0;">Directly instruct Googlebot, Bingbot, and AI crawlers which directories to index.</p>

      <form action="<?= url('/admin/seo-studio/save') ?>" method="POST">
        <textarea name="custom_robots_txt" rows="10" class="admin-modal-textarea" style="font-family: monospace; font-size: 0.85rem; line-height: 1.5; margin-bottom: 1rem;"><?= htmlspecialchars($robotsTxt) ?></textarea>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #38bdf8; border-color: #38bdf8; color: #000; font-weight: 700;">
          <i data-lucide="save"></i> Update robots.txt ⚡
        </button>
      </form>
    </div>

    <!-- Dynamic Sitemap Stats & Structured Schema -->
    <div class="admin-table-wrap" style="padding: 1.75rem;">
      <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
        <i data-lucide="layers" style="color: #a855f7;"></i> Automated XML Sitemap Feeds
      </h3>
      <p style="color: #64748b; font-size: 0.82rem; margin: 0 0 1rem 0;">TechScout auto-indexes published technical articles, AI tools, custom GPTs, and categories in real time.</p>

      <div style="background: #04070e; border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 1.25rem; margin-bottom: 1.25rem; font-size: 0.85rem;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
          <span style="color: #cbd5e1;">Live Endpoint:</span>
          <code style="color: #38bdf8;"><?= url('/sitemap.xml') ?></code>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
          <span style="color: #cbd5e1;">Schema Standard:</span>
          <span class="ts-badge ts-badge-cyan" style="font-size: 0.7rem;">Sitemaps 0.9 XML</span>
        </div>
        <div style="display: flex; justify-content: space-between;">
          <span style="color: #cbd5e1;">Google Search Console Status:</span>
          <span class="ts-badge ts-badge-green" style="font-size: 0.7rem;">READY FOR SUBMISSION</span>
        </div>
      </div>

      <div style="font-size: 0.82rem; color: #94a3b8; line-height: 1.55;">
        💡 <strong>Pro Tip:</strong> Submit <code><?= url('/sitemap.xml') ?></code> into Google Search Console and Bing Webmaster Tools for immediate crawling of all new publications.
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
