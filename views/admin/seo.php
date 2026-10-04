<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-main">
  <!-- Topbar -->
  <div class="admin-topbar" style="margin-bottom: 2rem;">
    <div>
      <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-cyan">SEARCH ENGINE OPTIMIZATION &bull; SCHEMA &bull; REDIRECTS</span>
        <span style="font-size: 0.8rem; color: #64748b;">Global Meta, Schema.org JSON-LD, Sitemaps, Canonicals &amp; 301 Rules</span>
      </div>
      <h1 style="font-size: 1.8rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        CMS SEO &amp; Search Performance Suite
      </h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <a href="<?= url('/sitemap.xml') ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8;">
        <i data-lucide="file-code"></i> Dynamic sitemap.xml ↗
      </a>
      <a href="<?= url('/robots.txt') ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm">
        <i data-lucide="file-text"></i> robots.txt ↗
      </a>
      <button type="submit" form="seoMasterForm" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #6366f1, #38bdf8); font-weight: 700;">
        <i data-lucide="save"></i> Save SEO Settings ✓
      </button>
    </div>
  </div>

  <!-- 12-Module SEO Navigation Ribbon -->
  <div class="ts-card" style="background: rgba(13,19,34,0.7); margin-bottom: 2rem; padding: 0.75rem; border-radius: 12px; overflow-x: auto;">
    <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.35rem; min-width: 980px;">
      <?php
      $seoTabs = [
        'global' => '🌐 Global SEO',
        'pages' => '📄 Page SEO',
        'articles' => '📰 Article SEO',
        'topics' => '🏷️ Topic SEO',
        'tools' => '🛠️ Tool SEO',
        'authors' => '✍️ Author SEO',
        'sitemap' => '🗺️ Sitemap',
        'redirects' => '🔀 Redirects (301)',
        'canonicals' => '🔗 Canonicals',
        'structured' => '📐 Structured Data',
        'indexing' => '🤖 Indexing & Robots',
        'performance' => '📊 Search Performance',
      ];
      ?>
      <?php foreach ($seoTabs as $tabKey => $tabLabel): ?>
        <button type="button" onclick="switchSeoTab('<?= $tabKey ?>')" id="seotab_<?= $tabKey ?>" class="ts-btn ts-btn-sm <?= $tabKey === ($activeTab ?? 'global') ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.75rem; padding: 0.35rem 0.65rem; white-space: nowrap;">
          <?= $tabLabel ?>
        </button>
      <?php endforeach; ?>
    </div>
  </div>

  <form id="seoMasterForm" action="<?= url('/admin/seo/settings/save') ?>" method="POST">
    
    <!-- 1. Global SEO Tab -->
    <div class="seo-pane" id="seopane_global">
      <div class="ts-card" style="background: rgba(13,19,34,0.7); padding: 1.75rem; border-radius: 12px; margin-bottom: 2rem;">
        <h3 style="font-size: 1.15rem; color: #f8fafc; margin-top: 0; margin-bottom: 1.25rem;">🌐 Global Meta &amp; OpenGraph Fallbacks</h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
          <div style="grid-column: 1 / -1;">
            <label class="admin-modal-label">Default Platform Meta Title Template</label>
            <input type="text" name="seo_global_title" value="<?= htmlspecialchars($siteSettings['seo_global_title'] ?? 'TechScout — The Frontier of Tech, Scouted for Builders') ?>" class="admin-modal-input">
            <small style="color: #64748b; font-size: 0.75rem;">Supported tags: {title}, {site_name}, {category}, {author}</small>
          </div>

          <div style="grid-column: 1 / -1;">
            <label class="admin-modal-label">Default Meta Description (Max 160 chars recommended)</label>
            <textarea name="seo_global_description" rows="3" class="admin-modal-textarea"><?= htmlspecialchars($siteSettings['seo_global_description'] ?? 'Discover frontier AI tools, production prompt blueprints, custom GPTs, and high-performance system architecture guides.') ?></textarea>
          </div>

          <div>
            <label class="admin-modal-label">Default Meta Keywords</label>
            <input type="text" name="seo_global_keywords" value="<?= htmlspecialchars($siteSettings['seo_global_keywords'] ?? 'AI tools, software engineering, prompts, LLMs, tech news, architecture') ?>" class="admin-modal-input">
          </div>

          <div>
            <label class="admin-modal-label">Default OpenGraph Image URL</label>
            <input type="text" name="seo_global_og_image" value="<?= htmlspecialchars($siteSettings['seo_global_og_image'] ?? 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=1200') ?>" class="admin-modal-input">
          </div>

          <div>
            <label class="admin-modal-label">Twitter / X Card Format</label>
            <select name="seo_twitter_card" class="admin-modal-select">
              <option value="summary_large_image">summary_large_image (Large Hero Banner)</option>
              <option value="summary">summary (Square Thumbnail)</option>
            </select>
          </div>

          <div>
            <label class="admin-modal-label">Search Engine Robots Default Directive</label>
            <select name="seo_default_robots" class="admin-modal-select">
              <option value="index, follow">index, follow (Allow Full Crawling &amp; Link Passing)</option>
              <option value="noindex, follow">noindex, follow</option>
              <option value="noindex, nofollow">noindex, nofollow</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- 2. Page SEO Tab -->
    <div class="seo-pane" id="seopane_pages" style="display: none;">
      <div class="ts-card" style="background: rgba(13,19,34,0.7); padding: 1.75rem; border-radius: 12px; margin-bottom: 2rem;">
        <h3 style="font-size: 1.15rem; color: #f8fafc; margin-top: 0; margin-bottom: 1.25rem;">📄 Static Pages Custom SEO Index</h3>
        
        <table class="admin-table">
          <thead>
            <tr>
              <th>Page Route</th>
              <th>Target Meta Title</th>
              <th>Meta Description</th>
              <th>Canonical URL</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $staticPages = [
              ['/', 'Home Discovery Hub', 'TechScout — Autonomous Tech Intelligence & Builder Hub', 'https://techscout.online/'],
              ['/blog', 'Articles Directory', 'Technical Articles & Deep Dives | TechScout', 'https://techscout.online/blog'],
              ['/tools', 'AI Tools Radar', 'AI Tools Radar & Developer Directory | TechScout', 'https://techscout.online/tools'],
              ['/prompts', 'Prompts Blueprints', 'Engineering Prompt Blueprints | TechScout', 'https://techscout.online/prompts'],
              ['/gpts', 'Custom GPTs Directory', 'Custom GPTs & Autonomous Agent Directory | TechScout', 'https://techscout.online/gpts'],
              ['/campaigns', 'Developer Bounties', 'Developer Challenges, Bounties & Hackathons | TechScout', 'https://techscout.online/campaigns'],
              ['/topics', '100+ Topics Taxonomy', 'Explore 100+ Topics & Engineering Taxonomies | TechScout', 'https://techscout.online/topics'],
              ['/about', 'About TechScout', 'About TechScout Platform & Engineering Mission', 'https://techscout.online/about'],
              ['/contact', 'Contact & Inquiries', 'Contact Staff & Technical Support | TechScout', 'https://techscout.online/contact'],
            ];
            ?>
            <?php foreach ($staticPages as $sp): ?>
              <tr>
                <td style="font-family: var(--admin-font-mono); color: #38bdf8; font-weight: 700;"><?= $sp[0] ?></td>
                <td><input type="text" name="pages_seo[<?= md5($sp[0]) ?>][title]" value="<?= htmlspecialchars($sp[2]) ?>" class="admin-modal-input" style="font-size: 0.82rem; padding: 0.35rem 0.65rem;"></td>
                <td><input type="text" name="pages_seo[<?= md5($sp[0]) ?>][description]" value="Curated intelligence on <?= $sp[1] ?>" class="admin-modal-input" style="font-size: 0.82rem; padding: 0.35rem 0.65rem;"></td>
                <td><input type="text" name="pages_seo[<?= md5($sp[0]) ?>][canonical]" value="<?= htmlspecialchars($sp[3]) ?>" class="admin-modal-input" style="font-size: 0.82rem; padding: 0.35rem 0.65rem;"></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 3. Article SEO Tab -->
    <div class="seo-pane" id="seopane_articles" style="display: none;">
      <div class="ts-card" style="background: rgba(13,19,34,0.7); padding: 1.75rem; border-radius: 12px; margin-bottom: 2rem;">
        <h3 style="font-size: 1.15rem; color: #f8fafc; margin-top: 0; margin-bottom: 1rem;">📰 Technical Article Schema &amp; OpenGraph Rules</h3>
        <p style="color: #94a3b8; font-size: 0.85rem; margin-bottom: 1.25rem;">Automated Schema.org <code>Article</code>, <code>TechArticle</code> and OpenGraph meta tag rules for all published editorial deep dives.</p>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
          <div>
            <label class="admin-modal-label">Schema.org Article Type</label>
            <select name="seo_article_schema_type" class="admin-modal-select">
              <option value="TechArticle">TechArticle (Software &amp; Deep-Tech Specification)</option>
              <option value="Article">Article (Standard Editorial)</option>
              <option value="NewsArticle">NewsArticle (Industry Scoops &amp; Releases)</option>
            </select>
          </div>
          <div>
            <label class="admin-modal-label">Auto-Include Author Schema.org <code>Person</code></label>
            <select name="seo_article_include_author" class="admin-modal-select">
              <option value="1">Enabled (Includes author name, profile URL, reputation score)</option>
              <option value="0">Disabled</option>
            </select>
          </div>
          <div style="grid-column: 1 / -1;">
            <label class="admin-modal-label">Default Article Title Pattern</label>
            <input type="text" name="seo_article_title_pattern" value="{title} — TechScout Engineering" class="admin-modal-input">
          </div>
        </div>
      </div>
    </div>

    <!-- 4. Topic SEO Tab -->
    <div class="seo-pane" id="seopane_topics" style="display: none;">
      <div class="ts-card" style="background: rgba(13,19,34,0.7); padding: 1.75rem; border-radius: 12px; margin-bottom: 2rem;">
        <h3 style="font-size: 1.15rem; color: #f8fafc; margin-top: 0; margin-bottom: 1rem;">🏷️ Topic &amp; Category Taxonomies SEO</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
          <div>
            <label class="admin-modal-label">Topic Title Format</label>
            <input type="text" name="seo_topic_title_format" value="{topic_name} Architecture, Tools &amp; Guides | TechScout" class="admin-modal-input">
          </div>
          <div>
            <label class="admin-modal-label">Topic Meta Description Template</label>
            <input type="text" name="seo_topic_desc_format" value="Explore curated articles, production tools, and code blueprints for {topic_name} on TechScout." class="admin-modal-input">
          </div>
        </div>
      </div>
    </div>

    <!-- 5. Tool SEO Tab -->
    <div class="seo-pane" id="seopane_tools" style="display: none;">
      <div class="ts-card" style="background: rgba(13,19,34,0.7); padding: 1.75rem; border-radius: 12px; margin-bottom: 2rem;">
        <h3 style="font-size: 1.15rem; color: #f8fafc; margin-top: 0; margin-bottom: 1rem;">🛠️ AI Tool Schema.org <code>SoftwareApplication</code> Settings</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
          <div>
            <label class="admin-modal-label">Software Application Category</label>
            <input type="text" name="seo_tool_app_category" value="DeveloperApplication, ArtificialIntelligence" class="admin-modal-input">
          </div>
          <div>
            <label class="admin-modal-label">Pricing Specification Schema</label>
            <select name="seo_tool_include_pricing" class="admin-modal-select">
              <option value="1">Enabled (Includes free/freemium/paid aggregate rating)</option>
              <option value="0">Disabled</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- 6. Author SEO Tab -->
    <div class="seo-pane" id="seopane_authors" style="display: none;">
      <div class="ts-card" style="background: rgba(13,19,34,0.7); padding: 1.75rem; border-radius: 12px; margin-bottom: 2rem;">
        <h3 style="font-size: 1.15rem; color: #f8fafc; margin-top: 0; margin-bottom: 1rem;">✍️ Creator &amp; Author <code>ProfilePage</code> Schema</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
          <div>
            <label class="admin-modal-label">Author Profile Meta Title</label>
            <input type="text" name="seo_author_title_format" value="{author_name} (@{username}) — Author &amp; Creator | TechScout" class="admin-modal-input">
          </div>
          <div>
            <label class="admin-modal-label">Social Profile Link Ingestion (sameAs)</label>
            <select name="seo_author_sameas" class="admin-modal-select">
              <option value="1">Enabled (Ingest GitHub, Twitter &amp; LinkedIn URLs)</option>
              <option value="0">Disabled</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- 7. Dynamic Sitemap Tab -->
    <div class="seo-pane" id="seopane_sitemap" style="display: none;">
      <div class="ts-card" style="background: rgba(13,19,34,0.7); padding: 1.75rem; border-radius: 12px; margin-bottom: 2rem;">
        <h3 style="font-size: 1.15rem; color: #f8fafc; margin-top: 0; margin-bottom: 1rem;">🗺️ Dynamic XML Sitemap Feeds</h3>
        <div style="background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 1.25rem; margin-bottom: 1.5rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <span style="color: #cbd5e1;">Live Endpoint:</span>
            <code style="color: #38bdf8;"><?= url('/sitemap.xml') ?></code>
          </div>
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="color: #cbd5e1;">Index Coverage:</span>
            <span class="ts-badge ts-badge-green">Articles, Tools, GPTs, Prompts &amp; Categories</span>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
          <div>
            <label class="admin-modal-label">Article Sitemap Priority</label>
            <input type="text" name="sitemap_article_priority" value="0.9" class="admin-modal-input">
          </div>
          <div>
            <label class="admin-modal-label">Article Change Frequency</label>
            <select name="sitemap_article_freq" class="admin-modal-select">
              <option value="daily">daily</option>
              <option value="weekly">weekly</option>
              <option value="monthly">monthly</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- 8. Redirects Tab -->
    <div class="seo-pane" id="seopane_redirects" style="display: none;">
      <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; margin-bottom: 2rem;">
        <!-- Add Redirect Box -->
        <div class="ts-card" style="background: rgba(13,19,34,0.7); padding: 1.5rem; border-radius: 12px;">
          <h3 style="font-size: 1.1rem; color: #f8fafc; margin-top: 0; margin-bottom: 1rem;">Add 301/302 Redirect</h3>
          <div style="margin-bottom: 1rem;">
            <label class="admin-modal-label">Source Path (e.g. /old-slug)</label>
            <input type="text" name="new_redirect_source" placeholder="/old-article-url" class="admin-modal-input">
          </div>
          <div style="margin-bottom: 1rem;">
            <label class="admin-modal-label">Target Path (e.g. /article/new-slug)</label>
            <input type="text" name="new_redirect_target" placeholder="/article/new-slug" class="admin-modal-input">
          </div>
          <div style="margin-bottom: 1.25rem;">
            <label class="admin-modal-label">HTTP Code</label>
            <select name="new_redirect_code" class="admin-modal-select">
              <option value="301">301 Permanent Redirect</option>
              <option value="302">302 Temporary Redirect</option>
            </select>
          </div>
          <button type="submit" name="action_add_redirect" value="1" class="ts-btn ts-btn-primary ts-btn-sm" style="width: 100%;">
            Add Rule ⚡
          </button>
        </div>

        <!-- Existing Redirects Table -->
        <div class="ts-card" style="background: rgba(13,19,34,0.7); padding: 1.5rem; border-radius: 12px;">
          <h3 style="font-size: 1.1rem; color: #f8fafc; margin-top: 0; margin-bottom: 1rem;">Active Redirect Rules</h3>
          <table class="admin-table">
            <thead>
              <tr>
                <th>Source</th>
                <th>Target</th>
                <th>Code</th>
                <th>Hits</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($redirects)): ?>
                <?php foreach ($redirects as $r): ?>
                  <tr>
                    <td style="font-family: var(--admin-font-mono); color: #f8fafc; font-size: 0.8rem;"><?= htmlspecialchars($r['source_path']) ?></td>
                    <td style="font-family: var(--admin-font-mono); color: #38bdf8; font-size: 0.8rem;"><?= htmlspecialchars($r['target_path']) ?></td>
                    <td><span class="ts-badge ts-badge-cyan"><?= $r['status_code'] ?></span></td>
                    <td style="color: #10b981; font-weight: 700;"><?= number_format($r['hit_count']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="4" style="text-align: center; padding: 2rem; color: #64748b;">No redirect rules active.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- 9. Canonicals Tab -->
    <div class="seo-pane" id="seopane_canonicals" style="display: none;">
      <div class="ts-card" style="background: rgba(13,19,34,0.7); padding: 1.75rem; border-radius: 12px; margin-bottom: 2rem;">
        <h3 style="font-size: 1.15rem; color: #f8fafc; margin-top: 0; margin-bottom: 1rem;">🔗 Canonical URL Normalization Strategy</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
          <div>
            <label class="admin-modal-label">Canonical Protocol &amp; Domain Policy</label>
            <select name="seo_canonical_protocol" class="admin-modal-select">
              <option value="force_https">Enforce HTTPS &amp; Non-WWW Canonical</option>
              <option value="auto">Auto-Detect Dynamic Domain</option>
            </select>
          </div>
          <div>
            <label class="admin-modal-label">Trailing Slash Policy</label>
            <select name="seo_trailing_slash" class="admin-modal-select">
              <option value="strip">Strip Trailing Slashes (/article/slug)</option>
              <option value="keep">Preserve Trailing Slashes (/article/slug/)</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- 10. Structured Data Tab -->
    <div class="seo-pane" id="seopane_structured" style="display: none;">
      <div class="ts-card" style="background: rgba(13,19,34,0.7); padding: 1.75rem; border-radius: 12px; margin-bottom: 2rem;">
        <h3 style="font-size: 1.15rem; color: #f8fafc; margin-top: 0; margin-bottom: 1rem;">📐 Schema.org JSON-LD Global Graph</h3>
        <textarea name="seo_custom_jsonld" rows="8" class="admin-modal-textarea" style="font-family: monospace; font-size: 0.85rem;"><?= htmlspecialchars($siteSettings['seo_custom_jsonld'] ?? '{\n  "@context": "https://schema.org",\n  "@type": "WebSite",\n  "name": "TechScout",\n  "url": "https://techscout.online",\n  "potentialAction": {\n    "@type": "SearchAction",\n    "target": "https://techscout.online/search?q={search_term_string}",\n    "query-input": "required name=search_term_string"\n  }\n}') ?></textarea>
      </div>
    </div>

    <!-- 11. Indexing Tab -->
    <div class="seo-pane" id="seopane_indexing" style="display: none;">
      <div class="ts-card" style="background: rgba(13,19,34,0.7); padding: 1.75rem; border-radius: 12px; margin-bottom: 2rem;">
        <h3 style="font-size: 1.15rem; color: #f8fafc; margin-top: 0; margin-bottom: 1rem;">🤖 Dynamic robots.txt Configurator</h3>
        <textarea name="custom_robots_txt" rows="8" class="admin-modal-textarea" style="font-family: monospace; font-size: 0.85rem; margin-bottom: 1rem;"><?= htmlspecialchars($robotsTxt ?? "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /auth/\nSitemap: https://techscout.online/sitemap.xml") ?></textarea>
      </div>
    </div>

    <!-- 12. Search Performance Tab -->
    <div class="seo-pane" id="seopane_performance" style="display: none;">
      <div class="ts-card" style="background: rgba(13,19,34,0.7); padding: 1.75rem; border-radius: 12px; margin-bottom: 2rem;">
        <h3 style="font-size: 1.15rem; color: #f8fafc; margin-top: 0; margin-bottom: 1.25rem;">📊 Google Search Console &amp; Index Telemetry</h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
          <div style="background: #05070b; padding: 1rem; border-radius: 8px; border: 1px solid rgba(148,163,184,0.15);">
            <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase;">Indexed URLs</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: #38bdf8; margin-top: 0.25rem;">1,420</div>
          </div>
          <div style="background: #05070b; padding: 1rem; border-radius: 8px; border: 1px solid rgba(148,163,184,0.15);">
            <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase;">Avg. Organic CTR</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: #10b981; margin-top: 0.25rem;">8.4%</div>
          </div>
          <div style="background: #05070b; padding: 1rem; border-radius: 8px; border: 1px solid rgba(148,163,184,0.15);">
            <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase;">Average Position</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: #a855f7; margin-top: 0.25rem;">4.2</div>
          </div>
          <div style="background: #05070b; padding: 1rem; border-radius: 8px; border: 1px solid rgba(148,163,184,0.15);">
            <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase;">Crawl Health</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: #34d399; margin-top: 0.25rem;">99.8%</div>
          </div>
        </div>
      </div>
    </div>

  </form>
</div>

<script>
function switchSeoTab(tabKey) {
  document.querySelectorAll('[id^="seotab_"]').forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });
  const activeBtn = document.getElementById('seotab_' + tabKey);
  if (activeBtn) {
    activeBtn.classList.remove('ts-btn-outline');
    activeBtn.classList.add('ts-btn-primary');
  }

  document.querySelectorAll('.seo-pane').forEach(p => p.style.display = 'none');
  const target = document.getElementById('seopane_' + tabKey);
  if (target) target.style.display = 'block';
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
