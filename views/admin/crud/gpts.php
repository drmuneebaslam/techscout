<?php require __DIR__ . '/../../layouts/admin_header.php'; ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <h1 style="font-size: 1.75rem; color: var(--admin-text-primary, #f8fafc); margin: 0 0 0.35rem 0; font-family: 'Outfit', sans-serif; font-weight: 700;">
      🤖 Custom GPTs &amp; Autonomous Agents (Enterprise CRUD)
    </h1>
    <p style="color: var(--admin-text-secondary, #94a3b8); font-size: 0.88rem; margin: 0;">
      Curate, audit, and benchmark OpenAI custom GPTs, tool capabilities, conversation starters, and direct agent launches.
    </p>
  </div>

  <div style="display: flex; gap: 0.6rem; align-items: center;">
    <button onclick="openNewGptModal()" class="ts-btn ts-btn-primary" style="font-weight: 700;">
      <i data-lucide="plus-circle" style="width: 16px; height: 16px;"></i> Add Custom GPT Agent
    </button>
  </div>
</div>

<!-- Top KPI Summary Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
  <div class="admin-table-wrap" style="padding: 1.25rem; border-top: 3px solid #38bdf8; background: var(--admin-card-bg, #090d16); border-radius: 12px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
      <span style="font-size: 0.75rem; color: var(--admin-text-secondary, #94a3b8); text-transform: uppercase; font-weight: 700;">🤖 Total Agents</span>
      <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem;">Active</span>
    </div>
    <div style="font-size: 1.85rem; font-weight: 800; color: #38bdf8; font-family: var(--admin-font-mono, monospace);">
      <?= number_format($kpiStats['total'] ?? count($gpts)) ?>
    </div>
    <div style="font-size: 0.75rem; color: var(--admin-text-secondary, #64748b); margin-top: 0.25rem;">
      Published AI GPT assistants
    </div>
  </div>

  <div class="admin-table-wrap" style="padding: 1.25rem; border-top: 3px solid #10b981; background: var(--admin-card-bg, #090d16); border-radius: 12px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
      <span style="font-size: 0.75rem; color: var(--admin-text-secondary, #94a3b8); text-transform: uppercase; font-weight: 700;">🛡️ Verified Builders</span>
      <span class="ts-badge ts-badge-green" style="font-size: 0.65rem;">Quality</span>
    </div>
    <div style="font-size: 1.85rem; font-weight: 800; color: #10b981; font-family: var(--admin-font-mono, monospace);">
      <?= number_format($kpiStats['verified_count'] ?? 0) ?>
    </div>
    <div style="font-size: 0.75rem; color: var(--admin-text-secondary, #64748b); margin-top: 0.25rem;">
      Editorial verified stamps
    </div>
  </div>

  <div class="admin-table-wrap" style="padding: 1.25rem; border-top: 3px solid #a855f7; background: var(--admin-card-bg, #090d16); border-radius: 12px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
      <span style="font-size: 0.75rem; color: var(--admin-text-secondary, #94a3b8); text-transform: uppercase; font-weight: 700;">⚡ Actions &amp; APIs</span>
      <span class="ts-badge ts-badge-indigo" style="font-size: 0.65rem;">Tools</span>
    </div>
    <div style="font-size: 1.85rem; font-weight: 800; color: #a855f7; font-family: var(--admin-font-mono, monospace);">
      <?= number_format($kpiStats['actions_count'] ?? 0) ?>
    </div>
    <div style="font-size: 0.75rem; color: var(--admin-text-secondary, #64748b); margin-top: 0.25rem;">
      Custom OpenAPI plugins attached
    </div>
  </div>

  <div class="admin-table-wrap" style="padding: 1.25rem; border-top: 3px solid #ec4899; background: var(--admin-card-bg, #090d16); border-radius: 12px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
      <span style="font-size: 0.75rem; color: var(--admin-text-secondary, #94a3b8); text-transform: uppercase; font-weight: 700;">🚀 Direct Launches</span>
      <span class="ts-badge ts-badge-pink" style="font-size: 0.65rem;">Engagement</span>
    </div>
    <div style="font-size: 1.85rem; font-weight: 800; color: #ec4899; font-family: var(--admin-font-mono, monospace);">
      <?= number_format($kpiStats['total_launches'] ?? 0) ?>
    </div>
    <div style="font-size: 0.75rem; color: var(--admin-text-secondary, #64748b); margin-top: 0.25rem;">
      Times launched in ChatGPT
    </div>
  </div>
</div>

<!-- Multi-Tab Enterprise GPT Modal -->
<div id="gptFormModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-table-wrap" style="width: 100%; max-width: 960px; max-height: 92vh; overflow-y: auto; padding: 2rem; background: var(--admin-card-bg, #090d16); border: 1px solid rgba(168,85,247,0.35); box-shadow: 0 25px 60px rgba(0,0,0,0.9); border-radius: 14px;">
    
    <!-- Modal Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--admin-border-color, rgba(148,163,184,0.12)); padding-bottom: 0.75rem;">
      <div style="display: flex; align-items: center; gap: 0.65rem;">
        <div style="width: 34px; height: 34px; border-radius: 8px; background: rgba(168,85,247,0.15); color: #a855f7; display: flex; align-items: center; justify-content: center;">
          <i data-lucide="bot" style="width: 18px; height: 18px;"></i>
        </div>
        <div>
          <h2 id="modalGptTitle" style="font-size: 1.35rem; color: var(--admin-text-primary, #f8fafc); margin: 0; font-family: 'Outfit', sans-serif; font-weight: 700;">
            Add New Custom GPT Specification
          </h2>
          <span style="font-size: 0.78rem; color: var(--admin-text-secondary, #64748b);">Enterprise schema covering capabilities, model bases, conversation openers &amp; deep-links</span>
        </div>
      </div>
      <button type="button" onclick="closeGptModal()" style="background: none; border: none; color: var(--admin-text-secondary, #94a3b8); font-size: 1.6rem; cursor: pointer;">&times;</button>
    </div>

    <!-- Modal Navigation Tabs -->
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding-bottom: 0.5rem; overflow-x: auto;" id="gptModalTabs">
      <button type="button" onclick="switchGptTab('gpt-tab-basic')" id="btn-gpt-tab-basic" class="ts-btn ts-btn-sm ts-btn-primary">1. Identity &amp; Taxonomy</button>
      <button type="button" onclick="switchGptTab('gpt-tab-capabilities')" id="btn-gpt-tab-capabilities" class="ts-btn ts-btn-sm ts-btn-outline">2. Model &amp; Tool Capabilities</button>
      <button type="button" onclick="switchGptTab('gpt-tab-starters')" id="btn-gpt-tab-starters" class="ts-btn ts-btn-sm ts-btn-outline">3. Conversation Starters</button>
      <button type="button" onclick="switchGptTab('gpt-tab-architecture')" id="btn-gpt-tab-architecture" class="ts-btn ts-btn-sm ts-btn-outline">4. Architecture &amp; Directives</button>
      <button type="button" onclick="switchGptTab('gpt-tab-access')" id="btn-gpt-tab-access" class="ts-btn ts-btn-sm ts-btn-outline">5. Access &amp; Benchmark</button>
    </div>

    <form id="adminGptForm" action="<?= url('/admin/gpts/save') ?>" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" id="gpt_id" value="">

      <!-- TAB 1: IDENTITY & TAXONOMY -->
      <div id="gpt-tab-basic" class="gpt-modal-tab">
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.4rem;">
              Agent Name * <span style="font-size: 0.75rem; color: var(--admin-text-secondary, #94a3b8); font-weight: normal;">(Max 100 chars)</span>
            </label>
            <input type="text" name="title" id="gpt_title" maxlength="100" placeholder="e.g. Distributed Database Reliability Architect" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.75rem 1rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc); font-size: 1rem;" required>
          </div>
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.4rem;">Primary Category *</label>
            <select name="category_id" id="gpt_category_id" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.75rem 1rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc);">
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.4rem;">OpenAI Direct Deep Link URL *</label>
            <input type="url" name="openai_url" id="gpt_openai_url" placeholder="https://chatgpt.com/g/g-..." style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.75rem 1rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc);" required>
          </div>
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.4rem;">Creator Handle</label>
            <input type="text" name="creator_handle" id="gpt_creator_handle" value="@techscout" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.75rem 1rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc);">
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
              <label style="font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc);">Agent Avatar / Icon URL</label>
              <label class="ts-btn ts-btn-outline ts-btn-sm" style="cursor: pointer; padding: 0.15rem 0.5rem; font-size: 0.72rem; border-color: #38bdf8; color: #38bdf8; display: inline-flex; align-items: center; gap: 0.25rem;">
                <i data-lucide="upload" style="width: 12px; height: 12px;"></i> Upload Icon
                <input type="file" name="image_file" accept="image/*" onchange="previewGptAvatarFile(this)" style="display: none;">
              </label>
            </div>
            <input type="text" name="featured_image_url" id="gpt_featured_image_url" placeholder="https://.../icon.png or /storage/uploads/..." style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.75rem 1rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc);" oninput="previewGptAvatarUrl(this.value)">
          </div>

          <!-- Avatar Preview Box -->
          <div style="display: flex; align-items: center; gap: 0.75rem; background: rgba(15,23,42,0.4); padding: 0.5rem 0.75rem; border-radius: 8px; border: 1px solid var(--admin-border-color, rgba(148,163,184,0.1));">
            <div id="gptAvatarBox" style="width: 48px; height: 48px; border-radius: 10px; background: rgba(168,85,247,0.15); border: 1px solid rgba(168,85,247,0.3); display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0;">
              <img id="gptAvatarImg" src="" style="width: 100%; height: 100%; object-fit: cover; display: none;">
              <i id="gptAvatarPlaceholder" data-lucide="bot" style="width: 24px; height: 24px; color: #a855f7;"></i>
            </div>
            <div style="font-size: 0.75rem; color: var(--admin-text-secondary, #94a3b8);">
              Recommended: 1:1 square icon PNG/WebP (min 256x256)
            </div>
          </div>
        </div>

        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.4rem;">
            Directory Summary (1–2 punchy sentences) * <span style="font-size: 0.75rem; color: var(--admin-text-secondary, #94a3b8); font-weight: normal;">(Max 300 chars)</span>
          </label>
          <textarea name="summary" id="gpt_summary" maxlength="300" rows="3" placeholder="AI assistant trained to analyze database execution plans, detect lock contention, and generate optimal composite indexes..." style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.75rem 1rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc);" required></textarea>
        </div>

        <!-- Badges & Verification Strip -->
        <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; background: rgba(15,23,42,0.5); padding: 1rem; border-radius: 8px; border: 1px solid var(--admin-border-color, rgba(255,255,255,0.06));">
          <label style="display: flex; align-items: center; gap: 0.5rem; color: var(--admin-text-primary, #f8fafc); cursor: pointer;">
            <input type="checkbox" name="custom_badge" id="gpt_verified" value="Verified Agent" checked style="accent-color: #10b981;"> 
            <span><strong>TechScout Verified Badge</strong> (Official Editorial Stamp)</span>
          </label>
          <label style="display: flex; align-items: center; gap: 0.5rem; color: var(--admin-text-primary, #f8fafc); cursor: pointer;">
            <input type="checkbox" name="is_featured" id="gpt_featured" value="1" style="accent-color: #38bdf8;"> 
            <span><strong>Featured on Homepage</strong></span>
          </label>
          <label style="display: flex; align-items: center; gap: 0.5rem; color: var(--admin-text-primary, #f8fafc); cursor: pointer;">
            <input type="checkbox" name="is_trending" id="gpt_trending" value="1" style="accent-color: #f59e0b;"> 
            <span><strong>Trending Agent Pill</strong></span>
          </label>
        </div>
      </div>

      <!-- TAB 2: MODEL & TOOL CAPABILITIES -->
      <div id="gpt-tab-capabilities" class="gpt-modal-tab" style="display: none;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.5rem;">
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.4rem;">
              Foundation Model Base *
            </label>
            <select name="model_base" id="gpt_model_base" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.75rem 1rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc);">
              <option value="GPT-4o" selected>GPT-4o (Omni Multimodal &amp; Fast Code)</option>
              <option value="OpenAI o1">OpenAI o1 (Deep Reasoning &amp; Complex Math)</option>
              <option value="OpenAI o3-mini">OpenAI o3-mini (High-Speed Reasoning)</option>
              <option value="GPT-4o-mini">GPT-4o-mini (Lightweight &amp; Cost-Effective)</option>
              <option value="GPT-4 Turbo">GPT-4 Turbo (Classic 128k Context)</option>
              <option value="Fine-Tuned Specialized">Custom Domain Fine-Tune</option>
            </select>
          </div>

          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.4rem;">
              Domain Use-Case Classification
            </label>
            <input type="text" name="use_case" id="gpt_use_case" placeholder="e.g. Database Architecture, SRE, Code Review" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.75rem 1rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc);">
          </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.75rem;">
            Native OpenAI Tool Capabilities Enabled
          </label>

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.85rem;">
            <label style="display: flex; align-items: center; gap: 0.6rem; padding: 0.75rem 1rem; background: var(--admin-card-bg-subtle, rgba(255,255,255,0.03)); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); border-radius: 8px; cursor: pointer;">
              <input type="checkbox" name="capabilities[]" value="web_browsing" id="cap_web_browsing" checked style="accent-color: #38bdf8;">
              <span style="font-size: 0.88rem; color: var(--admin-text-primary, #f8fafc);">🌐 Web Browsing</span>
            </label>

            <label style="display: flex; align-items: center; gap: 0.6rem; padding: 0.75rem 1rem; background: var(--admin-card-bg-subtle, rgba(255,255,255,0.03)); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); border-radius: 8px; cursor: pointer;">
              <input type="checkbox" name="capabilities[]" value="code_interpreter" id="cap_code_interpreter" checked style="accent-color: #10b981;">
              <span style="font-size: 0.88rem; color: var(--admin-text-primary, #f8fafc);">🐍 Code Interpreter</span>
            </label>

            <label style="display: flex; align-items: center; gap: 0.6rem; padding: 0.75rem 1rem; background: var(--admin-card-bg-subtle, rgba(255,255,255,0.03)); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); border-radius: 8px; cursor: pointer;">
              <input type="checkbox" name="capabilities[]" value="dalle" id="cap_dalle" style="accent-color: #ec4899;">
              <span style="font-size: 0.88rem; color: var(--admin-text-primary, #f8fafc);">🎨 DALL-E 3 Image Gen</span>
            </label>

            <label style="display: flex; align-items: center; gap: 0.6rem; padding: 0.75rem 1rem; background: var(--admin-card-bg-subtle, rgba(255,255,255,0.03)); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); border-radius: 8px; cursor: pointer;">
              <input type="checkbox" name="capabilities[]" value="canvas" id="cap_canvas" checked style="accent-color: #6366f1;">
              <span style="font-size: 0.88rem; color: var(--admin-text-primary, #f8fafc);">📑 Canvas Workspace</span>
            </label>

            <label style="display: flex; align-items: center; gap: 0.6rem; padding: 0.75rem 1rem; background: var(--admin-card-bg-subtle, rgba(255,255,255,0.03)); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); border-radius: 8px; cursor: pointer;">
              <input type="checkbox" name="capabilities[]" value="actions" id="cap_actions" style="accent-color: #f59e0b;">
              <span style="font-size: 0.88rem; color: var(--admin-text-primary, #f8fafc);">⚡ Custom API Actions</span>
            </label>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem;">
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.4rem;">
              Custom Actions Count
            </label>
            <input type="number" name="actions_count" id="gpt_actions_count" min="0" max="20" value="0" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.75rem 1rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc);">
          </div>
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.4rem;">
              Action Names / Integrated APIs (comma-separated)
            </label>
            <input type="text" name="actions_list" id="gpt_actions_list" placeholder="e.g. GitHub REST API, Zapier Webhook, PostgreSQL Schema Sync" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.75rem 1rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc);">
          </div>
        </div>
      </div>

      <!-- TAB 3: CONVERSATION STARTERS BUILDER -->
      <div id="gpt-tab-starters" class="gpt-modal-tab" style="display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
          <div>
            <h3 style="font-size: 1.05rem; color: var(--admin-text-primary, #f8fafc); margin: 0; font-weight: 700;">
              Suggested Conversation Openers
            </h3>
            <span style="font-size: 0.78rem; color: var(--admin-text-secondary, #64748b);">Prompt pills presented to users when opening the agent</span>
          </div>
          <button type="button" onclick="addStarterInput()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3);">
            <i data-lucide="plus" style="width: 12px; height: 12px;"></i> Add Starter
          </button>
        </div>

        <div id="startersContainer" style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem;">
          <!-- Starter inputs dynamically inserted here -->
        </div>

        <div style="background: rgba(56,189,248,0.05); border: 1px dashed rgba(56,189,248,0.25); border-radius: 8px; padding: 0.85rem 1rem; font-size: 0.8rem; color: var(--admin-text-secondary, #94a3b8); line-height: 1.45;">
          💡 <strong>Tip:</strong> Provide clear, actionable triggers like <em>"Analyze this query execution plan for sequential scans"</em> or <em>"Refactor this Dockerfile for minimum layer cache size"</em>.
        </div>
      </div>

      <!-- TAB 4: ARCHITECTURE & DIRECTIVES -->
      <div id="gpt-tab-architecture" class="gpt-modal-tab" style="display: none;">
        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.4rem;">
            Deep-Dive Architecture Teardown &amp; System Directives (HTML / Markdown)
          </label>
          <textarea name="body_html" id="gpt_body_html" rows="7" placeholder="Detailed engineering teardown of the agent's multi-step chain of thought, safety boundaries, and capabilities..." style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.75rem 1rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc); font-family: var(--admin-font-mono, monospace); font-size: 0.88rem;"></textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.4rem;">
              Target Audience / Personas
            </label>
            <input type="text" name="target_audience" id="gpt_target_audience" placeholder="e.g. SREs, DBA, Full-Stack Engineers" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.75rem 1rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc);">
          </div>
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.4rem;">
              Creator Portfolio / Official Link
            </label>
            <input type="url" name="creator_website" id="gpt_creator_website" placeholder="https://github.com/... or personal site" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.75rem 1rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc);">
          </div>
        </div>
      </div>

      <!-- TAB 5: ACCESS LEVEL & BENCHMARK -->
      <div id="gpt-tab-access" class="gpt-modal-tab" style="display: none;">
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.4rem;">
              Access &amp; Subscription Tier *
            </label>
            <select name="access_level" id="gpt_access_level" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.75rem 1rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc);">
              <option value="free">Free Tier (Open to All ChatGPT Users)</option>
              <option value="plus" selected>ChatGPT Plus Required</option>
              <option value="team">Team / Enterprise Workspace Required</option>
            </select>
          </div>

          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.4rem;">
              Editorial Benchmark Rating (1–5)
            </label>
            <input type="number" step="0.1" min="1" max="5" name="rating" id="gpt_rating" value="4.8" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.75rem 1rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc);">
          </div>

          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.4rem;">
              Quality Score (0–100)
            </label>
            <input type="number" step="0.1" min="0" max="100" name="quality_score" id="gpt_quality_score" value="96.0" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.75rem 1rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc);">
          </div>
        </div>

        <div style="margin-bottom: 1rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.4rem;">
            Publication Status
          </label>
          <select name="status" id="gpt_status" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.75rem 1rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc);">
            <option value="published" selected>Published (Public in Directory)</option>
            <option value="draft">Draft (Private in Studio)</option>
            <option value="in_review">In Review (Pending Editorial Audit)</option>
          </select>
        </div>
      </div>

      <!-- Modal Footer -->
      <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding-top: 1.25rem; margin-top: 1.5rem;">
        <button type="button" onclick="closeGptModal()" class="ts-btn ts-btn-outline">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary" style="font-weight: 700; background: linear-gradient(135deg, #a855f7, #6366f1);">
          Save GPT Agent Blueprint ⚡
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Quick Agent Preview Modal -->
<div id="gptPreviewModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 10000; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-table-wrap" style="width: 100%; max-width: 680px; padding: 2rem; background: var(--admin-card-bg, #090d16); border: 1px solid rgba(168,85,247,0.4); border-radius: 14px; position: relative;">
    <button onclick="document.getElementById('gptPreviewModal').style.display='none'" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; color: var(--admin-text-secondary); font-size: 1.4rem; cursor: pointer;">&times;</button>
    
    <div style="display: flex; gap: 1.25rem; align-items: center; margin-bottom: 1.25rem;">
      <div id="previewGptAvatar" style="width: 64px; height: 64px; border-radius: 12px; background: rgba(168,85,247,0.2); display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0;"></div>
      <div>
        <div style="display: flex; gap: 0.4rem; align-items: center; margin-bottom: 0.25rem;">
          <span id="previewGptModel" class="ts-badge ts-badge-cyan" style="font-size: 0.72rem;"></span>
          <span id="previewGptBadge" class="ts-badge ts-badge-green" style="font-size: 0.7rem;"></span>
        </div>
        <h3 id="previewGptTitle" style="font-size: 1.3rem; margin: 0; color: var(--admin-text-primary, #f8fafc); font-family: 'Outfit', sans-serif;"></h3>
        <span id="previewGptCreator" style="font-size: 0.8rem; color: var(--admin-text-secondary, #94a3b8);"></span>
      </div>
    </div>

    <p id="previewGptSummary" style="font-size: 0.9rem; color: var(--admin-text-secondary, #cbd5e1); line-height: 1.6; margin-bottom: 1.25rem;"></p>

    <div style="margin-bottom: 1.25rem;">
      <strong style="display: block; font-size: 0.82rem; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.5rem; text-transform: uppercase;">
        💬 Conversation Starters (Click to copy)
      </strong>
      <div id="previewGptStarters" style="display: flex; flex-direction: column; gap: 0.5rem;"></div>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding-top: 1rem; margin-top: 1rem;">
      <a id="previewGptPublicLink" href="#" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3);">
        🌐 Public Page ↗
      </a>
      <a id="previewGptOpenAiBtn" href="#" target="_blank" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #a855f7, #6366f1); font-weight: 700;">
        🚀 Launch in ChatGPT ↗
      </a>
    </div>
  </div>
</div>

<!-- Filter Bar & Search -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
  <div style="display: flex; gap: 0.4rem; flex-wrap: wrap; align-items: center;">
    <button type="button" onclick="filterGpts('all')" class="ts-badge ts-badge-indigo filter-gpt-pill" data-filter="all" style="cursor: pointer; padding: 0.35rem 0.75rem; font-size: 0.78rem;">All Agents</button>
    <button type="button" onclick="filterGpts('gpt4o')" class="ts-badge filter-gpt-pill" data-filter="gpt4o" style="cursor: pointer; padding: 0.35rem 0.75rem; font-size: 0.78rem;">🤖 GPT-4o</button>
    <button type="button" onclick="filterGpts('reasoning')" class="ts-badge filter-gpt-pill" data-filter="reasoning" style="cursor: pointer; padding: 0.35rem 0.75rem; font-size: 0.78rem;">🧠 Reasoning (o1/o3)</button>
    <button type="button" onclick="filterGpts('actions')" class="ts-badge filter-gpt-pill" data-filter="actions" style="cursor: pointer; padding: 0.35rem 0.75rem; font-size: 0.78rem;">⚡ With Actions</button>
    <button type="button" onclick="filterGpts('featured')" class="ts-badge filter-gpt-pill" data-filter="featured" style="cursor: pointer; padding: 0.35rem 0.75rem; font-size: 0.78rem;">⭐ Featured</button>
  </div>

  <input type="text" id="gptSearchInput" placeholder="🔍 Search GPTs, models, creators, tools..." class="ts-input" style="padding: 0.5rem 0.9rem; font-size: 0.85rem; width: 300px; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.25)); border-radius: 8px; color: var(--admin-text-primary, #f8fafc);">
</div>

<!-- Enhanced GPTs Table -->
<div class="admin-table-wrap">
  <table class="admin-table" id="gptsCrudTable" data-paginate="true" data-search-id="gptSearchInput" data-page-size="10">
    <thead>
      <tr>
        <th style="width: 55px;">Avatar</th>
        <th>Agent Name &amp; Description</th>
        <th>Model Base</th>
        <th>Capabilities &amp; Tools</th>
        <th>Creator</th>
        <th>Launches</th>
        <th style="text-align: right;">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($gpts as $g): ?>
        <?php
          $meta = json_decode($g['internal_notes'] ?? '{}', true) ?: [];
          $caps = $meta['capabilities'] ?? ['web_browsing', 'code_interpreter'];
          $starters = json_decode($g['prompt_starters'] ?? '[]', true) ?: [];
          $isFeatured = !empty($g['is_featured']);
          $isReasoning = str_contains(strtolower($g['model_base'] ?? ''), 'o1') || str_contains(strtolower($g['model_base'] ?? ''), 'o3');
          $hasActions = in_array('actions', $caps, true) || !empty($meta['actions_count']);
        ?>
        <tr data-model="<?= htmlspecialchars(strtolower($g['model_base'] ?? '')) ?>" 
            data-is-reasoning="<?= $isReasoning ? '1' : '0' ?>"
            data-has-actions="<?= $hasActions ? '1' : '0' ?>"
            data-is-featured="<?= $isFeatured ? '1' : '0' ?>">
          <td>
            <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(168,85,247,0.15); border: 1px solid rgba(168,85,247,0.3); display: flex; align-items: center; justify-content: center; overflow: hidden;">
              <?php if (!empty($g['featured_image_url'])): ?>
                <img src="<?= htmlspecialchars(image_url($g['featured_image_url'])) ?>" alt="Icon" style="width: 100%; height: 100%; object-fit: cover;">
              <?php else: ?>
                <i data-lucide="bot" style="width: 22px; height: 22px; color: #a855f7;"></i>
              <?php endif; ?>
            </div>
          </td>
          <td>
            <div style="font-weight: 700; font-size: 0.95rem; display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
              <a href="<?= url('/gpts/' . $g['slug']) ?>" target="_blank" style="color: var(--admin-text-primary, #f8fafc); text-decoration: none;">
                <?= htmlspecialchars($g['title']) ?>
              </a>
              <?php if (!empty($g['custom_badge'])): ?>
                <span class="ts-badge ts-badge-green" style="font-size: 0.65rem; padding: 0.1rem 0.4rem;">
                  <?= htmlspecialchars($g['custom_badge']) ?>
                </span>
              <?php endif; ?>
              <?php if ($isFeatured): ?>
                <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem; padding: 0.1rem 0.4rem;">⭐ Featured</span>
              <?php endif; ?>
            </div>
            <div style="font-size: 0.75rem; color: var(--admin-text-secondary, #64748b); margin-top: 0.25rem;">
              <?= htmlspecialchars(substr($g['summary'], 0, 85)) ?>...
            </div>
          </td>
          <td>
            <span class="ts-badge ts-badge-cyan" style="font-size: 0.75rem; font-weight: 600;">
              <?= htmlspecialchars($g['model_base'] ?? 'GPT-4o') ?>
            </span>
          </td>
          <td>
            <div style="display: flex; gap: 0.3rem; flex-wrap: wrap;">
              <?php if (in_array('web_browsing', $caps, true)): ?>
                <span class="ts-badge" style="font-size: 0.68rem; padding: 0.1rem 0.35rem;" title="Web Browsing">🌐 Web</span>
              <?php endif; ?>
              <?php if (in_array('code_interpreter', $caps, true)): ?>
                <span class="ts-badge ts-badge-green" style="font-size: 0.68rem; padding: 0.1rem 0.35rem;" title="Code Interpreter">🐍 Code</span>
              <?php endif; ?>
              <?php if (in_array('dalle', $caps, true)): ?>
                <span class="ts-badge ts-badge-pink" style="font-size: 0.68rem; padding: 0.1rem 0.35rem;" title="DALL-E 3">🎨 Art</span>
              <?php endif; ?>
              <?php if ($hasActions): ?>
                <span class="ts-badge ts-badge-indigo" style="font-size: 0.68rem; padding: 0.1rem 0.35rem;" title="Custom Actions">⚡ APIs</span>
              <?php endif; ?>
            </div>
          </td>
          <td>
            <strong style="color: var(--admin-text-secondary, #94a3b8); font-size: 0.85rem; font-family: var(--admin-font-mono, monospace);">
              <?= htmlspecialchars($g['creator_handle'] ?? '@techscout') ?>
            </strong>
          </td>
          <td>
            <strong style="color: #ec4899; font-family: var(--admin-font-mono, monospace);">
              <?= number_format((int)($g['view_count'] ?? 0)) ?>
            </strong>
          </td>
          <td style="text-align: right;">
            <div style="display: inline-flex; gap: 0.35rem; flex-wrap: wrap; justify-content: flex-end;">
              <a href="<?= htmlspecialchars($g['openai_url']) ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.72rem; color: #a855f7; border-color: rgba(168,85,247,0.35);" title="Direct Launch in OpenAI">
                🚀 Launch ↗
              </a>
              <button type="button" onclick='openGptPreview(<?= json_encode($g, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.72rem; color: #38bdf8; border-color: rgba(56,189,248,0.3);" title="Quick Preview">
                👁️ Preview
              </button>
              <a href="<?= url('/admin/analytics/content/' . $g['id']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.72rem; color: #10b981; border-color: rgba(16,185,129,0.3);" title="View Telemetry">
                📊 Stats
              </a>
              <button type="button" onclick='editGpt(<?= json_encode($g, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.72rem;">
                Edit
              </button>
              <form action="<?= url('/admin/gpts/delete/' . $g['id']) ?>" method="POST" onsubmit="return confirm('Delete this custom GPT agent blueprint?');" style="margin: 0;">
                <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.72rem; color: #ef4444;">
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
function switchGptTab(tabId) {
  document.querySelectorAll('.gpt-modal-tab').forEach(el => el.style.display = 'none');
  document.querySelectorAll('#gptModalTabs button').forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });

  const target = document.getElementById(tabId);
  const btn = document.getElementById('btn-' + tabId);
  if (target) target.style.display = 'block';
  if (btn) {
    btn.classList.remove('ts-btn-outline');
    btn.classList.add('ts-btn-primary');
  }
}

function openNewGptModal() {
  document.getElementById('adminGptForm').reset();
  document.getElementById('gpt_id').value = '';
  document.getElementById('modalGptTitle').innerText = 'Add New Custom GPT Specification';
  document.getElementById('gptAvatarImg').style.display = 'none';
  document.getElementById('gptAvatarPlaceholder').style.display = 'block';
  
  // Set default starters
  renderStarterInputs([
    'Analyze this PostgreSQL schema for missing foreign keys and index bloat',
    'Generate production Dockerfile multi-stage build optimized for caching',
    'Audit this API endpoint for N+1 query leaks and latency bottlenecks'
  ]);

  switchGptTab('gpt-tab-basic');
  document.getElementById('gptFormModal').style.display = 'flex';
}

function editGpt(g) {
  document.getElementById('adminGptForm').reset();
  document.getElementById('gpt_id').value = g.id;
  document.getElementById('gpt_title').value = g.title || '';
  document.getElementById('gpt_category_id').value = g.category_id || '';
  document.getElementById('gpt_openai_url').value = g.openai_url || '';
  document.getElementById('gpt_model_base').value = g.model_base || 'GPT-4o';
  document.getElementById('gpt_creator_handle').value = g.creator_handle || '@techscout';
  document.getElementById('gpt_use_case').value = g.use_case || '';
  document.getElementById('gpt_summary').value = g.summary || '';
  document.getElementById('gpt_body_html').value = g.body_html || '';
  document.getElementById('gpt_featured_image_url').value = g.featured_image_url || '';

  // Avatar preview
  if (g.featured_image_url) {
    previewGptAvatarUrl(g.featured_image_url);
  } else {
    document.getElementById('gptAvatarImg').style.display = 'none';
    document.getElementById('gptAvatarPlaceholder').style.display = 'block';
  }

  // Badges & flags
  document.getElementById('gpt_verified').checked = (g.custom_badge && g.custom_badge.length > 0);
  document.getElementById('gpt_featured').checked = (parseInt(g.is_featured) === 1);
  document.getElementById('gpt_trending').checked = (parseInt(g.is_trending) === 1);
  if (document.getElementById('gpt_status')) document.getElementById('gpt_status').value = g.status || 'published';
  if (document.getElementById('gpt_quality_score')) document.getElementById('gpt_quality_score').value = g.quality_score || '95.0';

  // Parse internal_notes JSON
  let meta = {};
  try {
    meta = JSON.parse(g.internal_notes || '{}');
  } catch(e) {}

  // Capabilities checkboxes
  const caps = meta.capabilities || ['web_browsing', 'code_interpreter'];
  ['web_browsing', 'code_interpreter', 'dalle', 'canvas', 'actions'].forEach(c => {
    const el = document.getElementById('cap_' + c);
    if (el) el.checked = caps.includes(c);
  });

  if (document.getElementById('gpt_actions_count')) document.getElementById('gpt_actions_count').value = meta.actions_count || 0;
  if (document.getElementById('gpt_actions_list')) document.getElementById('gpt_actions_list').value = meta.actions_list || '';
  if (document.getElementById('gpt_access_level')) document.getElementById('gpt_access_level').value = meta.access_level || 'free';
  if (document.getElementById('gpt_rating')) document.getElementById('gpt_rating').value = meta.rating || 4.8;
  if (document.getElementById('gpt_target_audience')) document.getElementById('gpt_target_audience').value = meta.target_audience || '';
  if (document.getElementById('gpt_creator_website')) document.getElementById('gpt_creator_website').value = meta.creator_website || '';

  // Parse starters
  let starters = [];
  try {
    starters = JSON.parse(g.prompt_starters || '[]');
  } catch(e) {}
  if (!Array.isArray(starters) || starters.length === 0) {
    starters = ['Ask me to audit your system architecture', 'Optimize my code implementation'];
  }
  renderStarterInputs(starters);

  document.getElementById('modalGptTitle').innerText = 'Edit GPT #' + g.id + ' — ' + (g.title || '');
  switchGptTab('gpt-tab-basic');
  document.getElementById('gptFormModal').style.display = 'flex';
}

function closeGptModal() {
  document.getElementById('gptFormModal').style.display = 'none';
}

function renderStarterInputs(starters) {
  const container = document.getElementById('startersContainer');
  container.innerHTML = '';
  starters.forEach((text, i) => {
    appendStarterInputRow(text, i + 1);
  });
}

function appendStarterInputRow(text, idx) {
  const container = document.getElementById('startersContainer');
  const div = document.createElement('div');
  div.style.cssText = 'display: flex; gap: 0.5rem; align-items: center;';
  div.innerHTML = `
    <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; width: 22px;">#${idx}</span>
    <input type="text" name="prompt_starters[]" value="${escapeHtml(text)}" placeholder="Enter suggested conversation prompt..." style="flex: 1; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.65rem 0.85rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc); font-size: 0.88rem;">
    <button type="button" onclick="this.parentElement.remove()" style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #f87171; border-radius: 6px; padding: 0.55rem 0.75rem; cursor: pointer;">✕</button>
  `;
  container.appendChild(div);
}

function addStarterInput() {
  const count = document.getElementById('startersContainer').children.length + 1;
  appendStarterInputRow('', count);
}

function previewGptAvatarUrl(url) {
  const img = document.getElementById('gptAvatarImg');
  const icon = document.getElementById('gptAvatarPlaceholder');
  if (url && url.length > 4) {
    img.src = url;
    img.style.display = 'block';
    icon.style.display = 'none';
  } else {
    img.style.display = 'none';
    icon.style.display = 'block';
  }
}

function previewGptAvatarFile(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      previewGptAvatarUrl(e.target.result);
    };
    reader.readAsDataURL(input.files[0]);
  }
}

function openGptPreview(g) {
  document.getElementById('previewGptTitle').innerText = g.title || 'Untitled Agent';
  document.getElementById('previewGptModel').innerText = g.model_base || 'GPT-4o';
  document.getElementById('previewGptBadge').innerText = g.custom_badge || 'Verified Builder';
  document.getElementById('previewGptCreator').innerText = 'Architected by ' + (g.creator_handle || '@techscout');
  document.getElementById('previewGptSummary').innerText = g.summary || '';
  document.getElementById('previewGptPublicLink').href = '<?= url('/gpts/') ?>' + (g.slug || '');
  document.getElementById('previewGptOpenAiBtn').href = g.openai_url || '#';

  // Avatar
  const avatarBox = document.getElementById('previewGptAvatar');
  if (g.featured_image_url) {
    avatarBox.innerHTML = `<img src="${escapeHtml(g.featured_image_url)}" style="width: 100%; height: 100%; object-fit: cover;">`;
  } else {
    avatarBox.innerHTML = `<i data-lucide="bot" style="width: 32px; height: 32px; color: #a855f7;"></i>`;
  }

  // Starters
  let starters = [];
  try { starters = JSON.parse(g.prompt_starters || '[]'); } catch(e) {}
  const startersBox = document.getElementById('previewGptStarters');
  startersBox.innerHTML = '';
  if (starters.length > 0) {
    starters.forEach(s => {
      const d = document.createElement('div');
      d.style.cssText = 'background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 0.65rem 0.85rem; font-size: 0.84rem; color: #f8fafc; cursor: pointer; display: flex; justify-content: space-between; align-items: center; transition: all 0.2s;';
      d.innerHTML = `<span>${escapeHtml(s)}</span><span style="font-size: 0.72rem; color: #38bdf8;">📋 Copy</span>`;
      d.onclick = () => {
        navigator.clipboard.writeText(s);
        if (typeof showToast === 'function') showToast('Copied starter prompt!', 'info');
      };
      startersBox.appendChild(d);
    });
  } else {
    startersBox.innerHTML = '<span style="font-size: 0.8rem; color: #64748b;">No conversation starters defined yet.</span>';
  }

  if (typeof lucide !== 'undefined') lucide.createIcons();
  document.getElementById('gptPreviewModal').style.display = 'flex';
}

function filterGpts(type) {
  document.querySelectorAll('.filter-gpt-pill').forEach(b => {
    if (b.getAttribute('data-filter') === type) {
      b.classList.add('ts-badge-indigo');
    } else {
      b.classList.remove('ts-badge-indigo');
    }
  });

  const rows = document.querySelectorAll('#gptsCrudTable tbody tr');
  rows.forEach(r => {
    let show = true;
    if (type === 'gpt4o') {
      show = (r.getAttribute('data-model') || '').includes('gpt-4o');
    } else if (type === 'reasoning') {
      show = (r.getAttribute('data-is-reasoning') === '1');
    } else if (type === 'actions') {
      show = (r.getAttribute('data-has-actions') === '1');
    } else if (type === 'featured') {
      show = (r.getAttribute('data-is-featured') === '1');
    }
    r.style.display = show ? '' : 'none';
  });
}

function escapeHtml(str) {
  if (!str) return '';
  return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}

document.addEventListener('DOMContentLoaded', function() {
  if (typeof paginateAdminTable === 'function') {
    window.gptsPager = paginateAdminTable('gptsCrudTable', {
      pageSize: 10,
      searchInputId: 'gptSearchInput'
    });
  }
  if (typeof lucide !== 'undefined') lucide.createIcons();
});
</script>

<?php require __DIR__ . '/../../layouts/admin_footer.php'; ?>
