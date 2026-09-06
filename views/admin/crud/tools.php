<?php require __DIR__ . '/../../layouts/admin_header.php'; ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <h1 style="font-size: 1.75rem; color: #f8fafc; margin: 0 0 0.25rem 0;">AI Tools &amp; Software Directory (Enterprise CRUD)</h1>
    <p style="color: #64748b; font-size: 0.9rem; margin: 0;">Comprehensive management of AI models, developer tools, pricing engines, ratings, screenshots, and affiliate links.</p>
  </div>

  <button onclick="openNewToolModal()" class="ts-btn ts-btn-primary">
    <i data-lucide="plus-circle"></i> Add New AI Tool
  </button>
</div>

<!-- Enterprise Multi-Tab AI Tool Modal -->
<div id="toolFormModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-table-wrap" style="width: 100%; max-width: 960px; max-height: 92vh; overflow-y: auto; padding: 2rem; background: #090d16; border: 1px solid rgba(236,72,153,0.35); box-shadow: 0 25px 60px rgba(0,0,0,0.9); border-radius: 12px;">
    
    <!-- Modal Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid rgba(148,163,184,0.12); padding-bottom: 0.75rem;">
      <div>
        <h2 id="modalToolTitle" style="font-size: 1.4rem; color: #f8fafc; margin: 0;">Add New AI Tool Specification</h2>
        <span style="font-size: 0.8rem; color: #64748b;">Enterprise schema covering 11 metadata systems</span>
      </div>
      <button onclick="closeToolModal()" style="background: none; border: none; color: #94a3b8; font-size: 1.6rem; cursor: pointer;">&times;</button>
    </div>

    <!-- Modal Navigation Tabs -->
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(148,163,184,0.15); padding-bottom: 0.5rem; overflow-x: auto;" id="toolModalTabs">
      <button type="button" onclick="switchToolTab('tab-basic')" id="btn-tab-basic" class="ts-btn ts-btn-sm ts-btn-primary">1. Identity &amp; Taxonomy</button>
      <button type="button" onclick="switchToolTab('tab-pricing')" id="btn-tab-pricing" class="ts-btn ts-btn-sm ts-btn-outline">2. Pricing &amp; Affiliate</button>
      <button type="button" onclick="switchToolTab('tab-links')" id="btn-tab-links" class="ts-btn ts-btn-sm ts-btn-outline">3. Official Links</button>
      <button type="button" onclick="switchToolTab('tab-features')" id="btn-tab-features" class="ts-btn ts-btn-sm ts-btn-outline">4. Features &amp; Platforms</button>
      <button type="button" onclick="switchToolTab('tab-comparison')" id="btn-tab-comparison" class="ts-btn ts-btn-sm ts-btn-outline">5. Alternatives &amp; Compare</button>
      <button type="button" onclick="switchToolTab('tab-reviews')" id="btn-tab-reviews" class="ts-btn ts-btn-sm ts-btn-outline">6. Reviews &amp; Ratings</button>
      <button type="button" onclick="switchToolTab('tab-media')" id="btn-tab-media" class="ts-btn ts-btn-sm ts-btn-outline">7. Media &amp; Deep Dive</button>
    </div>

    <form id="adminToolForm" action="<?= url('/admin/tools/save') ?>" method="POST">
      <input type="hidden" name="id" id="tool_id" value="">

      <!-- TAB 1: IDENTITY & TAXONOMY -->
      <div id="tab-basic" class="tool-modal-tab">
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Tool Name * <span style="font-size: 0.75rem; color: #94a3b8; font-weight: normal;">(Max 100 chars)</span></label>
            <input type="text" name="title" id="tool_title" maxlength="100" placeholder="e.g. Cursor AI Code Editor" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc; font-size: 1rem;" required>
          </div>
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Primary Category *</label>
            <select name="category_id" id="tool_category_id" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
              <label style="font-size: 0.85rem; font-weight: 600; color: #f8fafc;">Tool Logo / Icon URL</label>
              <label class="ts-btn ts-btn-outline ts-btn-sm" style="cursor: pointer; padding: 0.15rem 0.5rem; font-size: 0.72rem; border-color: #38bdf8; color: #38bdf8; display: inline-flex; align-items: center; gap: 0.25rem;">
                <i data-lucide="upload" style="width: 12px; height: 12px;"></i> Upload
                <input type="file" accept="image/*" onchange="uploadImageToInput(this, 'tool_featured_image_url', 'tools')" style="display: none;">
              </label>
            </div>
            <input type="url" name="featured_image_url" id="tool_featured_image_url" placeholder="https://.../logo.png" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
          </div>
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Use-Case Tags (comma-separated)</label>
            <input type="text" name="use_cases" id="tool_use_cases" placeholder="Code Generation, Autocomplete, Refactoring" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
          </div>
        </div>

        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Directory Summary (1–2 punchy sentences) * <span style="font-size: 0.75rem; color: #94a3b8; font-weight: normal;">(Max 300 chars)</span></label>
          <textarea name="summary" id="tool_summary" maxlength="300" rows="3" placeholder="AI-first code editor built for pair-programming with Claude 3.5 Sonnet and GPT-4o..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;" required></textarea>
        </div>

        <div style="display: flex; gap: 2rem; margin-top: 1rem; background: rgba(15,23,42,0.5); padding: 1rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.06);">
          <label style="display: flex; align-items: center; gap: 0.5rem; color: #f8fafc; cursor: pointer;">
            <input type="checkbox" name="verified_by_techscout" id="tool_verified" value="1" checked style="accent-color: #10b981;"> 
            <span><strong>TechScout Verified Badge</strong> (Official Editorial Stamp)</span>
          </label>
          <label style="display: flex; align-items: center; gap: 0.5rem; color: #f8fafc; cursor: pointer;">
            <input type="checkbox" name="api_available" id="tool_api_available" value="1" style="accent-color: #38bdf8;"> 
            <span><strong>Developer API Available</strong></span>
          </label>
        </div>
      </div>

      <!-- TAB 2: PRICING & AFFILIATE ENGINE -->
      <div id="tab-pricing" class="tool-modal-tab" style="display: none;">
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Pricing Model *</label>
            <select name="pricing_model" id="tool_pricing_model" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
              <option value="free">Free Forever</option>
              <option value="freemium" selected>Freemium (Free tier + Paid)</option>
              <option value="paid">Paid</option>
              <option value="subscription">Subscription Recurring</option>
              <option value="free_trial">Free Trial Available</option>
              <option value="open_source">Open Source</option>
              <option value="contact_pricing">Contact for Enterprise Pricing</option>
            </select>
          </div>
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Starting Price</label>
            <input type="text" name="pricing_starting_at" id="tool_pricing_starting_at" placeholder="e.g. $20/mo or $0" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
          </div>
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Billing Period</label>
            <select name="pricing_billing_cycle" id="tool_pricing_billing_cycle" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
              <option value="monthly" selected>Monthly Billing</option>
              <option value="annual">Annual / Yearly Billing</option>
              <option value="one_time">One-Time Lifetime License</option>
              <option value="usage_based">Usage-Based / Pay-as-you-go</option>
            </select>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Free Trial Offer</label>
            <div style="display: flex; align-items: center; gap: 0.5rem; background: #05070b; padding: 0.75rem; border-radius: 6px; border: 1px solid rgba(148,163,184,0.15);">
              <input type="checkbox" name="free_trial_available" id="tool_free_trial_available" value="1" checked style="accent-color: #38bdf8;">
              <input type="number" name="free_trial_days" id="tool_free_trial_days" value="14" min="0" max="365" style="width: 60px; background: transparent; border: none; color: #38bdf8; font-weight: 700;"> days trial
            </div>
          </div>
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Discount / Promo Code</label>
            <input type="text" name="discount_code" id="tool_discount_code" placeholder="e.g. TECHSCOUT20" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc; font-family: monospace;">
          </div>
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Discount Headline / Deal</label>
            <input type="text" name="discount_deal" id="tool_discount_deal" placeholder="e.g. 20% OFF First 3 Months" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
          </div>
        </div>

        <div style="background: rgba(99,102,241,0.06); border: 1px solid rgba(99,102,241,0.2); border-radius: 8px; padding: 1.25rem; margin-top: 1rem;">
          <h4 style="margin: 0 0 1rem 0; color: #38bdf8; font-size: 0.95rem;"><i data-lucide="dollar-sign" style="width: 15px; display: inline-block; vertical-align: -2px;"></i> Affiliate Tracking &amp; Monetization</h4>
          <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem;">
            <div>
              <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #e2e8f0; margin-bottom: 0.3rem;">Affiliate Referral URL</label>
              <input type="url" name="affiliate_url" id="tool_affiliate_url" placeholder="https://partner.cursor.com/?via=techscout" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.65rem 0.85rem; border-radius: 6px; color: #f8fafc;">
            </div>
            <div>
              <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #e2e8f0; margin-bottom: 0.3rem;">Cloaked Link Slug</label>
              <input type="text" name="custom_affiliate_slug" id="tool_custom_affiliate_slug" placeholder="e.g. cursor-ai" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.65rem 0.85rem; border-radius: 6px; color: #f8fafc;">
            </div>
            <div>
              <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #e2e8f0; margin-bottom: 0.3rem;">Commission Rate</label>
              <input type="text" name="affiliate_commission_rate" id="tool_affiliate_commission_rate" placeholder="e.g. 20% Recurring" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.65rem 0.85rem; border-radius: 6px; color: #f8fafc;">
            </div>
          </div>
        </div>
      </div>

      <!-- TAB 3: OFFICIAL LINKS -->
      <div id="tab-links" class="tool-modal-tab" style="display: none;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Official Website URL *</label>
            <input type="url" name="website_url" id="tool_website_url" placeholder="https://cursor.com" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;" required>
          </div>
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Documentation / API Specs URL</label>
            <input type="url" name="docs_url" id="tool_docs_url" placeholder="https://docs.cursor.com" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">GitHub / Repository URL</label>
            <input type="url" name="open_source_repo" id="tool_open_source_repo" placeholder="https://github.com/..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
          </div>
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Discord / Community URL</label>
            <input type="url" name="discord_url" id="tool_discord_url" placeholder="https://discord.gg/..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
          </div>
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Twitter / 𝕏 Handle</label>
            <input type="text" name="twitter_handle" id="tool_twitter_handle" placeholder="@cursor_ai" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
          </div>
        </div>

        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Software License Type</label>
          <input type="text" name="license_type" id="tool_license_type" placeholder="Proprietary, MIT, Apache-2.0, AGPL-3.0" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
        </div>
      </div>

      <!-- TAB 4: FEATURES & PLATFORM ECOSYSTEM -->
      <div id="tab-features" class="tool-modal-tab" style="display: none;">
        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Key Features &amp; Capabilities (One per line)</label>
          <textarea name="features_list" id="tool_features_list" rows="5" placeholder="Multi-file codebase indexing with embeddings
Terminal agent execution with self-healing
Direct integration with Claude 3.5 Sonnet &amp; GPT-4o
Privacy mode with zero telemetry retention" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc; font-family: monospace; font-size: 0.9rem;"></textarea>
        </div>

        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Supported Platforms &amp; Client Environments</label>
          <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem; background: rgba(15,23,42,0.5); padding: 1rem; border-radius: 8px;">
            <?php 
              $platforms = ['Web App', 'macOS Desktop', 'Windows Desktop', 'Linux Desktop', 'iOS App', 'Android App', 'Chrome Extension', 'VS Code Extension', 'Docker / Self-Hosted', 'Terminal CLI', 'Raycast Plugin', 'Figma Plugin'];
              foreach ($platforms as $idx => $plat):
            ?>
              <label style="display: flex; align-items: center; gap: 0.4rem; color: #cbd5e1; font-size: 0.85rem; cursor: pointer;">
                <input type="checkbox" name="platforms_supported[]" value="<?= $plat ?>" class="plat-cb" style="accent-color: #38bdf8;"> <?= $plat ?>
              </label>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- TAB 5: ALTERNATIVES & COMPARISON -->
      <div id="tab-comparison" class="tool-modal-tab" style="display: none;">
        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Top Alternative Competitor Tools (comma-separated names or slugs)</label>
          <input type="text" name="alternatives_list" id="tool_alternatives_list" placeholder="GitHub Copilot, Windsurf, Void, Cline, Continue.dev" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;">
        </div>

        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Comparison Highlights &amp; Key Differentiators</label>
          <textarea name="comparison_highlights" id="tool_comparison_highlights" rows="4" placeholder="Unlike GitHub Copilot which only completes inline lines, Cursor indexes your entire repository in vector memory and applies multi-file diffs automatically." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;"></textarea>
        </div>
      </div>

      <!-- TAB 6: REVIEWS, PROS/CONS & RATINGS -->
      <div id="tab-reviews" class="tool-modal-tab" style="display: none;">
        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Editorial Verdict &amp; Expert Review Summary</label>
          <textarea name="editorial_review" id="tool_editorial_review" rows="3" placeholder="TechScout Verdict: Currently the gold standard in AI-assisted developer IDEs. Fast indexing and exceptional Sonnet integration make it worth every dollar for senior engineers." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc;"></textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #10b981; margin-bottom: 0.4rem;">Pros (One item per line)</label>
            <textarea name="pros_list" id="tool_pros_list" rows="4" placeholder="Blazing fast repository vector search
Seamless multi-file diff application
Privacy mode support
Direct custom API key usage" style="width: 100%; background: #05070b; border: 1px solid rgba(16,185,129,0.3); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc; font-family: monospace; font-size: 0.88rem;"></textarea>
          </div>
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #ef4444; margin-bottom: 0.4rem;">Cons (One item per line)</label>
            <textarea name="cons_list" id="tool_cons_list" rows="4" placeholder="Heavy memory usage on large monorepos
Requires separate subscription from Copilot
Limited offline capability" style="width: 100%; background: #05070b; border: 1px solid rgba(239,68,68,0.3); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc; font-family: monospace; font-size: 0.88rem;"></textarea>
          </div>
        </div>

        <!-- 5-Dimensional Ratings Breakdown -->
        <div style="background: rgba(15,23,42,0.6); padding: 1.25rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.08);">
          <h4 style="margin: 0 0 1rem 0; color: #f59e0b; font-size: 0.95rem;">⭐ 5-Dimensional Benchmark Ratings (Scale 0.0 – 5.0)</h4>
          <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
            <div>
              <label style="display: block; font-size: 0.8rem; color: #cbd5e1; margin-bottom: 0.3rem;">Overall Rating ⭐</label>
              <input type="number" step="0.1" min="0" max="5.0" name="rating_avg" id="tool_rating_avg" value="4.9" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem; border-radius: 6px; color: #f59e0b; font-weight: 700;">
            </div>
            <div>
              <label style="display: block; font-size: 0.8rem; color: #cbd5e1; margin-bottom: 0.3rem;">Ease of Use ⚡</label>
              <input type="number" step="0.1" min="0" max="5.0" name="rating_ease_of_use" id="tool_rating_ease_of_use" value="4.8" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem; border-radius: 6px; color: #38bdf8;">
            </div>
            <div>
              <label style="display: block; font-size: 0.8rem; color: #cbd5e1; margin-bottom: 0.3rem;">Value for Money 💎</label>
              <input type="number" step="0.1" min="0" max="5.0" name="rating_value_for_money" id="tool_rating_value_for_money" value="4.7" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem; border-radius: 6px; color: #38bdf8;">
            </div>
            <div>
              <label style="display: block; font-size: 0.8rem; color: #cbd5e1; margin-bottom: 0.3rem;">Feature Depth 🛠️</label>
              <input type="number" step="0.1" min="0" max="5.0" name="rating_features" id="tool_rating_features" value="4.9" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem; border-radius: 6px; color: #38bdf8;">
            </div>
            <div>
              <label style="display: block; font-size: 0.8rem; color: #cbd5e1; margin-bottom: 0.3rem;">Speed &amp; Performance 🚀</label>
              <input type="number" step="0.1" min="0" max="5.0" name="rating_performance" id="tool_rating_performance" value="4.85" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem; border-radius: 6px; color: #38bdf8;">
            </div>
            <div>
              <label style="display: block; font-size: 0.8rem; color: #cbd5e1; margin-bottom: 0.3rem;">Support &amp; Community 💬</label>
              <input type="number" step="0.1" min="0" max="5.0" name="rating_support" id="tool_rating_support" value="4.6" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.65rem; border-radius: 6px; color: #38bdf8;">
            </div>
          </div>
        </div>
      </div>

      <!-- TAB 7: SCREENSHOTS & DEEP DIVE HTML -->
      <div id="tab-media" class="tool-modal-tab" style="display: none;">
        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">Screenshots Gallery URLs (One image URL per line)</label>
          <textarea name="screenshots_gallery" id="tool_screenshots_gallery" rows="4" placeholder="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe
https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc; font-family: monospace; font-size: 0.88rem;"></textarea>
        </div>

        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.4rem;">In-Depth Architecture, Guide &amp; Technical Deep Dive HTML</label>
          <textarea name="body_html" id="tool_body_html" rows="7" placeholder="<h3>Architectural Breakdown</h3><p>Detailed technical documentation...</p>" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1rem; border-radius: 6px; color: #f8fafc; font-family: var(--ts-font-mono); font-size: 0.9rem;"></textarea>
        </div>
      </div>

      <!-- Modal Footer -->
      <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.75rem; border-top: 1px solid rgba(148,163,184,0.15); padding-top: 1rem;">
        <button type="button" onclick="closeToolModal()" class="ts-btn ts-btn-outline">Cancel</button>
        <div style="display: flex; gap: 0.75rem;">
          <button type="submit" class="ts-btn ts-btn-primary" style="font-weight: 700;">
            <i data-lucide="check"></i> Save Tool Specification
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Tools Table Directory -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
  <div style="font-weight: 700; color: #f8fafc; font-size: 1.1rem; display: flex; align-items: center; gap: 0.5rem;">
    <i data-lucide="wrench" style="width: 18px; height: 18px; color: #10b981;"></i> Catalog Index (<?= count($tools) ?> Tools Registered)
  </div>
  <input type="text" id="toolSearchInput" placeholder="🔍 Search tools, categories, pricing..." class="ts-input" style="padding: 0.45rem 0.85rem; font-size: 0.84rem; width: 280px; background: #05070b; border: 1px solid rgba(148,163,184,0.25); border-radius: 8px; color: #f8fafc;">
</div>

<div class="admin-table-wrap">
  <table class="admin-table" id="toolsCrudTable" data-paginate="true" data-search-id="toolSearchInput" data-page-size="10">
    <thead>
      <tr>
        <th>Tool / Application</th>
        <th>Category</th>
        <th>Pricing &amp; Cycle</th>
        <th>Rating Breakdown</th>
        <th>Affiliate / Deal</th>
        <th>Verified</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($tools as $t): ?>
        <tr>
          <td>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
              <?php if (!empty($t['featured_image_url'])): ?>
                <img src="<?= htmlspecialchars(image_url($t['featured_image_url'])) ?>" alt="Logo" style="width: 38px; height: 38px; border-radius: 8px; object-fit: cover; border: 1px solid rgba(255,255,255,0.1);">
              <?php else: ?>
                <div style="width: 38px; height: 38px; border-radius: 8px; background: rgba(99,102,241,0.2); display: flex; align-items: center; justify-content: center; color: #38bdf8; font-weight: 700;">
                  <?= strtoupper(substr($t['title'], 0, 1)) ?>
                </div>
              <?php endif; ?>
              <div>
                <div style="font-weight: 600; color: #f8fafc; font-size: 0.95rem;">
                  <a href="<?= url('/tools/' . $t['slug']) ?>" target="_blank" style="color: #f8fafc; text-decoration: none;">
                    <?= htmlspecialchars($t['title']) ?> ↗
                  </a>
                </div>
                <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.15rem;">
                  <a href="<?= htmlspecialchars($t['website_url'] ?? '') ?>" target="_blank" style="color: #38bdf8; text-decoration: none;">
                    <?= htmlspecialchars(parse_url($t['website_url'] ?? '', PHP_URL_HOST) ?: $t['website_url'] ?? '') ?>
                  </a>
                </div>
              </div>
            </div>
          </td>
          <td>
            <span class="ts-badge ts-badge-cyan"><?= htmlspecialchars($t['category_name'] ?? 'General') ?></span>
          </td>
          <td>
            <span class="ts-badge ts-badge-pink" style="text-transform: capitalize;"><?= htmlspecialchars($t['pricing_model'] ?? 'freemium') ?></span>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.2rem;">
              <?= htmlspecialchars($t['pricing_starting_at'] ?? '$0') ?> / <?= htmlspecialchars($t['pricing_billing_cycle'] ?? 'mo') ?>
            </div>
          </td>
          <td>
            <div style="color: #f59e0b; font-weight: 700; font-size: 0.95rem;">
              ⭐ <?= number_format((float)($t['rating_avg'] ?? 5.0), 1) ?>
            </div>
            <div style="font-size: 0.72rem; color: #64748b; font-family: monospace;">
              Ease: <?= number_format((float)($t['rating_ease_of_use'] ?? 4.8), 1) ?> | Perf: <?= number_format((float)($t['rating_performance'] ?? 4.8), 1) ?>
            </div>
          </td>
          <td>
            <?php if (!empty($t['affiliate_url'])): ?>
              <span class="ts-badge ts-badge-green" title="<?= htmlspecialchars($t['affiliate_url']) ?>">Active 🔗</span>
              <?php if (!empty($t['discount_code'])): ?>
                <div style="font-size: 0.7rem; color: #38bdf8; font-family: monospace; margin-top: 0.2rem;">Code: <?= htmlspecialchars($t['discount_code']) ?></div>
              <?php endif; ?>
            <?php else: ?>
              <span class="ts-badge" style="color: #64748b;">None</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if (!empty($t['verified_by_techscout'])): ?>
              <span class="ts-badge ts-badge-green">Verified ✓</span>
            <?php else: ?>
              <span class="ts-badge">Community</span>
            <?php endif; ?>
          </td>
          <td>
            <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
              <a href="<?= url('/admin/analytics/content/' . $t['id']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; color: #38bdf8; border-color: rgba(56,189,248,0.3);" title="View Telemetry">
                📊 Stats
              </a>
              <button type="button" onclick='editTool(<?= json_encode($t, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                Edit
              </button>
              <form action="<?= url('/admin/tools/delete/' . $t['id']) ?>" method="POST" onsubmit="return confirm('Delete this tool from directory?');">
                <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; color: #ef4444;">
                  Delete
                </button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script>
function switchToolTab(tabId) {
  document.querySelectorAll('.tool-modal-tab').forEach(el => el.style.display = 'none');
  document.querySelectorAll('#toolModalTabs button').forEach(el => {
    el.classList.remove('ts-btn-primary');
    el.classList.add('ts-btn-outline');
  });

  const targetTab = document.getElementById(tabId);
  const targetBtn = document.getElementById('btn-' + tabId);
  if (targetTab) targetTab.style.display = 'block';
  if (targetBtn) {
    targetBtn.classList.remove('ts-btn-outline');
    targetBtn.classList.add('ts-btn-primary');
  }
}

function openNewToolModal() {
  document.getElementById('adminToolForm').reset();
  document.getElementById('tool_id').value = '';
  document.getElementById('modalToolTitle').innerText = 'Add New AI Tool Specification';
  
  // Uncheck platform checkboxes
  document.querySelectorAll('.plat-cb').forEach(cb => cb.checked = false);
  
  switchToolTab('tab-basic');
  document.getElementById('toolFormModal').style.display = 'flex';
  if (window.lucide) lucide.createIcons();
}

function closeToolModal() {
  document.getElementById('toolFormModal').style.display = 'none';
}

function editTool(t) {
  document.getElementById('tool_id').value = t.id || '';
  document.getElementById('tool_title').value = t.title || '';
  document.getElementById('tool_category_id').value = t.category_id || '';
  document.getElementById('tool_featured_image_url').value = t.featured_image_url || '';
  document.getElementById('tool_summary').value = t.summary || '';
  document.getElementById('tool_body_html').value = t.body_html || '';
  
  // Official Links
  document.getElementById('tool_website_url').value = t.website_url || '';
  document.getElementById('tool_docs_url').value = t.docs_url || '';
  document.getElementById('tool_open_source_repo').value = t.open_source_repo || '';
  document.getElementById('tool_discord_url').value = t.discord_url || '';
  document.getElementById('tool_twitter_handle').value = t.twitter_handle || '';
  document.getElementById('tool_license_type').value = t.license_type || 'Proprietary';

  // Pricing & Affiliate
  document.getElementById('tool_pricing_model').value = t.pricing_model || 'freemium';
  document.getElementById('tool_pricing_starting_at').value = t.pricing_starting_at || '$0';
  document.getElementById('tool_pricing_billing_cycle').value = t.pricing_billing_cycle || 'monthly';
  document.getElementById('tool_free_trial_available').checked = (t.free_trial_available == 1);
  document.getElementById('tool_free_trial_days').value = t.free_trial_days || 14;
  document.getElementById('tool_discount_code').value = t.discount_code || '';
  document.getElementById('tool_discount_deal').value = t.discount_deal || '';
  document.getElementById('tool_affiliate_url').value = t.affiliate_url || '';
  document.getElementById('tool_custom_affiliate_slug').value = t.custom_affiliate_slug || '';
  document.getElementById('tool_affiliate_commission_rate').value = t.affiliate_commission_rate || '';

  // Ratings
  document.getElementById('tool_rating_avg').value = t.rating_avg || 4.85;
  document.getElementById('tool_rating_ease_of_use').value = t.rating_ease_of_use || 4.80;
  document.getElementById('tool_rating_value_for_money').value = t.rating_value_for_money || 4.70;
  document.getElementById('tool_rating_features').value = t.rating_features || 4.90;
  document.getElementById('tool_rating_performance').value = t.rating_performance || 4.85;
  document.getElementById('tool_rating_support').value = t.rating_support || 4.60;

  // Flags
  document.getElementById('tool_api_available').checked = (t.api_available == 1);
  document.getElementById('tool_verified').checked = (t.verified_by_techscout == 1);

  // Reviews, Pros, Cons, Comparison
  document.getElementById('tool_editorial_review').value = t.editorial_review || '';
  document.getElementById('tool_comparison_highlights').value = t.comparison_highlights || '';

  // Parse JSON lists to lines
  const parseJsonToList = (raw) => {
    if (!raw) return '';
    try {
      const arr = typeof raw === 'string' ? JSON.parse(raw) : raw;
      if (Array.isArray(arr)) return arr.join('\n');
    } catch(e) {}
    return raw;
  };

  const parseJsonToComma = (raw) => {
    if (!raw) return '';
    try {
      const arr = typeof raw === 'string' ? JSON.parse(raw) : raw;
      if (Array.isArray(arr)) return arr.join(', ');
    } catch(e) {}
    return raw;
  };

  document.getElementById('tool_features_list').value = parseJsonToList(t.features_list);
  document.getElementById('tool_pros_list').value = parseJsonToList(t.pros_list);
  document.getElementById('tool_cons_list').value = parseJsonToList(t.cons_list);
  document.getElementById('tool_screenshots_gallery').value = parseJsonToList(t.screenshots_gallery);
  document.getElementById('tool_alternatives_list').value = parseJsonToComma(t.alternatives_list);
  document.getElementById('tool_use_cases').value = parseJsonToComma(t.use_cases);

  // Platforms Checkboxes
  let platArr = [];
  try {
    platArr = typeof t.platforms_supported === 'string' ? JSON.parse(t.platforms_supported) : (t.platforms_supported || []);
  } catch(e) {}
  document.querySelectorAll('.plat-cb').forEach(cb => {
    cb.checked = platArr.includes(cb.value);
  });

  document.getElementById('modalToolTitle').innerText = 'Edit AI Tool: ' + (t.title || '');
  switchToolTab('tab-basic');
  document.getElementById('toolFormModal').style.display = 'flex';
  if (window.lucide) lucide.createIcons();
}

document.addEventListener('DOMContentLoaded', function() {
  window.toolsPager = window.paginateAdminTable('toolsCrudTable', {
    pageSize: 10,
    searchInputId: 'toolSearchInput'
  });
});
</script>

<?php require __DIR__ . '/../../layouts/admin_footer.php'; ?>
