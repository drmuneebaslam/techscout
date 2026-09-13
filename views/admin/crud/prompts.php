<?php require __DIR__ . '/../../layouts/admin_header.php'; ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <h1 style="font-size: 1.75rem; color: var(--admin-text-primary, #f8fafc); margin: 0 0 0.35rem 0; font-family: 'Outfit', sans-serif; font-weight: 700;">
      ⚡ Prompt Blueprints &amp; Generation Engineering (Enterprise CRUD)
    </h1>
    <p style="color: var(--admin-text-secondary, #94a3b8); font-size: 0.88rem; margin: 0;">
      Create, test, benchmark text/coding system prompts, variable schemas, and image generation blueprints.
    </p>
  </div>

  <div style="display: flex; gap: 0.6rem; align-items: center;">
    <button onclick="openNewPromptModal()" class="ts-btn ts-btn-primary" style="font-weight: 700;">
      <i data-lucide="plus-circle" style="width: 16px; height: 16px;"></i> Create Prompt Blueprint
    </button>
  </div>
</div>

<!-- Top KPI Summary Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
  <div class="admin-table-wrap" style="padding: 1.25rem; border-top: 3px solid #38bdf8; background: var(--admin-card-bg, #090d16); border-radius: 12px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
      <span style="font-size: 0.75rem; color: var(--admin-text-secondary, #94a3b8); text-transform: uppercase; font-weight: 700;">⚡ Total Blueprints</span>
      <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem;">Active</span>
    </div>
    <div style="font-size: 1.85rem; font-weight: 800; color: #38bdf8; font-family: var(--admin-font-mono, monospace);">
      <?= number_format($kpiStats['total'] ?? count($prompts)) ?>
    </div>
    <div style="font-size: 0.75rem; color: var(--admin-text-secondary, #64748b); margin-top: 0.25rem;">
      Curated production blueprints
    </div>
  </div>

  <div class="admin-table-wrap" style="padding: 1.25rem; border-top: 3px solid #10b981; background: var(--admin-card-bg, #090d16); border-radius: 12px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
      <span style="font-size: 0.75rem; color: var(--admin-text-secondary, #94a3b8); text-transform: uppercase; font-weight: 700;">📝 Text &amp; Code</span>
      <span class="ts-badge ts-badge-green" style="font-size: 0.65rem;">Reasoning</span>
    </div>
    <div style="font-size: 1.85rem; font-weight: 800; color: #10b981; font-family: var(--admin-font-mono, monospace);">
      <?= number_format($kpiStats['text_count'] ?? 0) ?>
    </div>
    <div style="font-size: 0.75rem; color: var(--admin-text-secondary, #64748b); margin-top: 0.25rem;">
      System personas, coding &amp; SQL
    </div>
  </div>

  <div class="admin-table-wrap" style="padding: 1.25rem; border-top: 3px solid #ec4899; background: var(--admin-card-bg, #090d16); border-radius: 12px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
      <span style="font-size: 0.75rem; color: var(--admin-text-secondary, #94a3b8); text-transform: uppercase; font-weight: 700;">🎨 Visual &amp; Image</span>
      <span class="ts-badge ts-badge-pink" style="font-size: 0.65rem;">Diffusion</span>
    </div>
    <div style="font-size: 1.85rem; font-weight: 800; color: #ec4899; font-family: var(--admin-font-mono, monospace);">
      <?= number_format($kpiStats['image_count'] ?? 0) ?>
    </div>
    <div style="font-size: 0.75rem; color: var(--admin-text-secondary, #64748b); margin-top: 0.25rem;">
      Midjourney, Flux.1 &amp; SDXL
    </div>
  </div>

  <div class="admin-table-wrap" style="padding: 1.25rem; border-top: 3px solid #f59e0b; background: var(--admin-card-bg, #090d16); border-radius: 12px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
      <span style="font-size: 0.75rem; color: var(--admin-text-secondary, #94a3b8); text-transform: uppercase; font-weight: 700;">📋 Total Copies</span>
      <span class="ts-badge ts-badge-amber" style="font-size: 0.65rem;">Engagement</span>
    </div>
    <div style="font-size: 1.85rem; font-weight: 800; color: #f59e0b; font-family: var(--admin-font-mono, monospace);">
      <?= number_format($kpiStats['total_copies'] ?? 0) ?>
    </div>
    <div style="font-size: 0.75rem; color: var(--admin-text-secondary, #64748b); margin-top: 0.25rem;">
      Times copied to engineer clipboards
    </div>
  </div>
</div>

<!-- Filter Bar & Search Container -->
<div class="admin-table-wrap" style="padding: 1rem 1.25rem; margin-bottom: 1.5rem; background: var(--admin-card-bg, #090d16); border-radius: 12px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
  <!-- Filter Pills -->
  <div style="display: flex; gap: 0.4rem; flex-wrap: wrap; align-items: center;">
    <button type="button" onclick="filterPromptsByType('all', this)" class="ts-btn ts-btn-sm ts-btn-primary prompt-filter-pill">
      All Blueprints
    </button>
    <button type="button" onclick="filterPromptsByType('text', this)" class="ts-btn ts-btn-sm ts-btn-outline prompt-filter-pill">
      📝 Text &amp; Code
    </button>
    <button type="button" onclick="filterPromptsByType('image', this)" class="ts-btn ts-btn-sm ts-btn-outline prompt-filter-pill">
      🎨 Visual &amp; Image
    </button>
    <button type="button" onclick="filterPromptsByType('featured', this)" class="ts-btn ts-btn-sm ts-btn-outline prompt-filter-pill">
      ⭐ Featured
    </button>
    <button type="button" onclick="filterPromptsByType('trending', this)" class="ts-btn ts-btn-sm ts-btn-outline prompt-filter-pill">
      🔥 Trending
    </button>
  </div>

  <!-- Search Input -->
  <div style="position: relative; min-width: 280px;">
    <i data-lucide="search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; color: var(--admin-text-secondary, #94a3b8);"></i>
    <input type="text" id="promptSearchInput" placeholder="Search title, variables, models..." style="width: 100%; padding: 0.5rem 0.75rem 0.5rem 2.1rem; font-size: 0.85rem; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.2)); border-radius: 8px; color: var(--admin-text-primary, #f8fafc);">
  </div>
</div>

<!-- Multi-Tab Enterprise Prompt Modal -->
<div id="promptFormModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-table-wrap" style="width: 100%; max-width: 960px; max-height: 92vh; overflow-y: auto; padding: 2rem; background: var(--admin-card-bg, #090d16); border: 1px solid rgba(56,189,248,0.35); box-shadow: 0 25px 60px rgba(0,0,0,0.9); border-radius: 14px;">
    
    <!-- Modal Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--admin-border-color, rgba(148,163,184,0.12)); padding-bottom: 0.75rem;">
      <div style="display: flex; align-items: center; gap: 0.65rem;">
        <div style="width: 34px; height: 34px; border-radius: 8px; background: rgba(56,189,248,0.15); color: #38bdf8; display: flex; align-items: center; justify-content: center;">
          <i data-lucide="terminal" style="width: 18px; height: 18px;"></i>
        </div>
        <div>
          <h2 id="modalPromptTitle" style="font-size: 1.35rem; color: var(--admin-text-primary, #f8fafc); margin: 0; font-family: 'Outfit', sans-serif; font-weight: 700;">
            Create Prompt Blueprint
          </h2>
          <span style="font-size: 0.78rem; color: var(--admin-text-secondary, #64748b);">Enterprise schema covering variable injections, model targets, hyperparameters &amp; verified outputs</span>
        </div>
      </div>
      <button type="button" onclick="closePromptModal()" style="background: none; border: none; color: var(--admin-text-secondary, #94a3b8); font-size: 1.6rem; cursor: pointer;">&times;</button>
    </div>

    <!-- Modal Navigation Tabs -->
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding-bottom: 0.5rem; overflow-x: auto;" id="promptModalTabs">
      <button type="button" onclick="switchPromptTab('prompt-tab-identity')" id="btn-prompt-tab-identity" class="ts-btn ts-btn-sm ts-btn-primary">1. Identity &amp; Classification</button>
      <button type="button" onclick="switchPromptTab('prompt-tab-architecture')" id="btn-prompt-tab-architecture" class="ts-btn ts-btn-sm ts-btn-outline">2. Architecture &amp; Variables</button>
      <button type="button" onclick="switchPromptTab('prompt-tab-hyperparameters')" id="btn-prompt-tab-hyperparameters" class="ts-btn ts-btn-sm ts-btn-outline">3. Target Models &amp; Tuning</button>
      <button type="button" onclick="switchPromptTab('prompt-tab-playground')" id="btn-prompt-tab-playground" class="ts-btn ts-btn-sm ts-btn-outline">4. Live Playground &amp; Output</button>
    </div>

    <form id="adminPromptForm" action="<?= url('/admin/prompts/save') ?>" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" id="prompt_id" value="">

      <!-- TAB 1: IDENTITY & CLASSIFICATION -->
      <div id="prompt-tab-identity" class="prompt-modal-tab">
        <!-- Prompt Type Segmented Cards -->
        <div style="margin-bottom: 1.5rem; background: var(--admin-card-bg-subtle, rgba(255,255,255,0.02)); padding: 1.15rem; border-radius: 10px; border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15));">
          <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.6rem;">
            Select Prompt Engine Classification * (Strict segregation of code/text prompts from diffusion imagery)
          </label>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
            <label id="type_opt_text" onclick="setPromptType('text')" style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.85rem; border-radius: 8px; border: 2px solid #38bdf8; background: rgba(56,189,248,0.06); cursor: pointer; transition: all 0.2s ease;">
              <input type="radio" name="prompt_type" id="prompt_type_text" value="text" checked style="margin-top: 0.2rem;">
              <div>
                <strong style="color: var(--admin-text-primary, #f8fafc); font-size: 0.95rem; display: block;">📝 Text, Code &amp; System Personas</strong>
                <span style="font-size: 0.78rem; color: var(--admin-text-secondary, #94a3b8); line-height: 1.4; display: block; margin-top: 0.2rem;">
                  LLM system prompts, SQL optimizers, Docker builders, code refactoring. No image attached.
                </span>
              </div>
            </label>

            <label id="type_opt_image" onclick="setPromptType('image')" style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.85rem; border-radius: 8px; border: 1px solid var(--admin-border-color, rgba(148,163,184,0.2)); background: transparent; cursor: pointer; transition: all 0.2s ease;">
              <input type="radio" name="prompt_type" id="prompt_type_image" value="image" style="margin-top: 0.2rem;">
              <div>
                <strong style="color: var(--admin-text-primary, #f8fafc); font-size: 0.95rem; display: block;">🎨 Visual &amp; Image Generation</strong>
                <span style="font-size: 0.78rem; color: var(--admin-text-secondary, #94a3b8); line-height: 1.4; display: block; margin-top: 0.2rem;">
                  Midjourney v6, Flux.1, SDXL, DALL-E 3 with negative prompts &amp; showcase output rendering.
                </span>
              </div>
            </label>
          </div>
        </div>

        <!-- Title & Category Row -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.4rem;">
              Blueprint Title *
            </label>
            <input type="text" name="title" id="prompt_title" placeholder="e.g. Enterprise Production PostgreSQL Optimizer &amp; Slow Query Analyzer" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.75rem 1rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc); font-size: 1rem;" required>
          </div>

          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.4rem;">Primary Category *</label>
            <select name="category_id" id="prompt_category_id" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.75rem 1rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc);">
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Conditional Visual Showcase Container: Strictly for Image Generation Prompts -->
        <div id="imageUploadContainer" style="display: none; margin-bottom: 1.5rem; background: rgba(236,72,153,0.04); border: 1px dashed rgba(236,72,153,0.3); border-radius: 10px; padding: 1.25rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.6rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
              <i data-lucide="image" style="width: 16px; height: 16px; color: #ec4899;"></i>
              <span style="font-weight: 700; color: var(--admin-text-primary, #f8fafc); font-size: 0.88rem;">Visual Generation Showcase Image</span>
            </div>
            <span class="ts-badge ts-badge-pink" style="font-size: 0.7rem;">Image Prompts Only</span>
          </div>
          <p style="font-size: 0.78rem; color: var(--admin-text-secondary, #94a3b8); margin: 0 0 1rem 0;">
            Upload a high-fidelity output render sample demonstrating this prompt in action (PNG, JPG, WEBP up to 10MB).
          </p>

          <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 1rem; align-items: center;">
            <div>
              <label class="ts-btn ts-btn-outline" style="cursor: pointer; width: 100%; text-align: center; padding: 0.75rem; border-color: rgba(236,72,153,0.4); color: #ec4899; display: block; border-radius: 8px;">
                <i data-lucide="upload-cloud" style="width: 16px; height: 16px; display: inline; margin-right: 0.35rem;"></i>
                <span id="uploadButtonText">Choose Showcase Image File</span>
                <input type="file" name="image_file" id="prompt_image_file" accept="image/png,image/jpeg,image/webp,image/gif" style="display: none;" onchange="previewSelectedPromptImage(this)">
              </label>
              <div style="margin-top: 0.65rem;">
                <label style="display: block; font-size: 0.75rem; color: var(--admin-text-secondary, #94a3b8); margin-bottom: 0.25rem;">Or Direct Image URL / Storage Path:</label>
                <input type="text" name="featured_image_url" id="prompt_featured_image_url" placeholder="e.g. /storage/uploads/prompts/... or https://..." style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.5rem 0.75rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc); font-size: 0.82rem;" oninput="previewUrlPromptImage(this.value)">
              </div>
            </div>

            <!-- Live Image Preview Card -->
            <div id="promptImagePreviewBox" style="height: 140px; border-radius: 8px; border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); background: var(--admin-input-bg, #05070b); display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;">
              <img id="promptImagePreviewImg" src="" alt="Prompt Visual" style="display: none; width: 100%; height: 100%; object-fit: cover;">
              <div id="promptImagePreviewPlaceholder" style="text-align: center; color: var(--admin-text-secondary, #94a3b8); font-size: 0.8rem;">
                <i data-lucide="image" style="width: 28px; height: 28px; margin-bottom: 0.3rem; opacity: 0.5;"></i>
                <div>No Image Attached</div>
              </div>
              <button type="button" id="removeImageBtn" onclick="clearPromptImagePreview()" style="display: none; position: absolute; top: 6px; right: 6px; background: rgba(239,68,68,0.85); color: #fff; border: none; border-radius: 4px; padding: 0.2rem 0.45rem; font-size: 0.7rem; cursor: pointer;">Remove</button>
            </div>
          </div>
        </div>

        <!-- Text Prompt Notice -->
        <div id="textPromptNotice" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.25rem; background: rgba(56,189,248,0.05); border: 1px solid rgba(56,189,248,0.2); border-radius: 8px; padding: 0.65rem 1rem;">
          <i data-lucide="info" style="width: 15px; height: 15px; color: #38bdf8; flex-shrink: 0;"></i>
          <span style="font-size: 0.78rem; color: var(--admin-text-secondary, #94a3b8);">
            Text blueprints strictly have <strong>no image attached</strong>. They render pure code cards with syntax-highlighted variable parameters and 1-click clipboard injectors.
          </span>
        </div>

        <!-- Summary -->
        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.4rem;">
            Executive Blueprint Summary * <span style="font-size: 0.75rem; color: var(--admin-text-secondary, #94a3b8); font-weight: normal;">(Max 300 chars)</span>
          </label>
          <textarea name="summary" id="prompt_summary" rows="2" maxlength="300" placeholder="Concise overview explaining how engineers should deploy this blueprint..." style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.75rem 1rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc); font-size: 0.9rem;" required></textarea>
        </div>

        <!-- Badges & Visibility Row -->
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.4rem;">Custom Editorial Badge</label>
            <input type="text" name="custom_badge" id="prompt_custom_badge" placeholder="e.g. ⚡ Production Tested" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.65rem 0.85rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc); font-size: 0.88rem;">
          </div>

          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.4rem;">Author / Engineer Handle</label>
            <input type="text" name="param_author_handle" id="prompt_author_handle" value="@techscout" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.65rem 0.85rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc); font-size: 0.88rem;">
          </div>

          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.4rem;">Discovery Visibility</label>
            <div style="display: flex; gap: 0.75rem; align-items: center; margin-top: 0.5rem;">
              <label style="display: inline-flex; align-items: center; gap: 0.35rem; cursor: pointer; font-size: 0.85rem; color: var(--admin-text-primary, #f8fafc);">
                <input type="checkbox" name="is_featured" id="prompt_is_featured" value="1">
                ⭐ Featured
              </label>
              <label style="display: inline-flex; align-items: center; gap: 0.35rem; cursor: pointer; font-size: 0.85rem; color: var(--admin-text-primary, #f8fafc);">
                <input type="checkbox" name="is_trending" id="prompt_is_trending" value="1">
                🔥 Trending
              </label>
            </div>
          </div>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
          <button type="button" onclick="switchPromptTab('prompt-tab-architecture')" class="ts-btn ts-btn-primary">
            Next: Architecture &amp; Variables →
          </button>
        </div>
      </div>

      <!-- TAB 2: ARCHITECTURE & VARIABLES BUILDER -->
      <div id="prompt-tab-architecture" class="prompt-modal-tab" style="display: none;">
        <!-- Blueprint Template Editor -->
        <div style="margin-bottom: 1.5rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
            <label style="font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc);">
              Master Prompt Template (Use <code style="color: #38bdf8; background: rgba(56,189,248,0.1); padding: 0.1rem 0.3rem; border-radius: 4px;">{{variable_name}}</code> syntax) *
            </label>
            <span id="promptTokenCounter" style="font-size: 0.75rem; color: var(--admin-text-secondary, #94a3b8); font-family: var(--admin-font-mono, monospace);">
              0 chars | ~0 tokens
            </span>
          </div>
          
          <!-- Variable Inserter Pill Cloud -->
          <div id="variableInsertPills" style="display: flex; gap: 0.35rem; flex-wrap: wrap; margin-bottom: 0.5rem; min-height: 24px;">
            <!-- Generated dynamically -->
          </div>

          <textarea name="prompt_template" id="prompt_template_body" rows="9" oninput="updateTemplateStats(); autoDetectVariables();" placeholder="Act as a {{role}}. Review the following {{target_code}} and produce {{output_format}}..." style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.85rem 1rem; border-radius: 8px; color: var(--admin-text-primary, #f8fafc); font-family: var(--admin-font-mono, monospace); font-size: 0.88rem; line-height: 1.6;" required></textarea>
        </div>

        <!-- Interactive Variable Builder -->
        <div style="background: var(--admin-card-bg-subtle, rgba(255,255,255,0.02)); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); border-radius: 10px; padding: 1.25rem; margin-bottom: 1.5rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
            <div>
              <strong style="color: var(--admin-text-primary, #f8fafc); font-size: 0.92rem; display: flex; align-items: center; gap: 0.4rem;">
                <i data-lucide="sliders" style="width: 16px; height: 16px; color: #38bdf8;"></i> Dynamic Prompt Variable Builder
              </strong>
              <span style="font-size: 0.75rem; color: var(--admin-text-secondary, #94a3b8);">Defines the input form rendered to users when configuring this blueprint</span>
            </div>
            <button type="button" onclick="addVariableRow()" class="ts-btn ts-btn-sm ts-btn-outline" style="border-color: rgba(56,189,248,0.4); color: #38bdf8;">
              <i data-lucide="plus" style="width: 14px; height: 14px;"></i> Add Variable
            </button>
          </div>

          <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
              <thead>
                <tr style="border-bottom: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); color: var(--admin-text-secondary, #94a3b8); text-align: left;">
                  <th style="padding: 0.5rem; width: 25%;">Variable Name ({{tag}})</th>
                  <th style="padding: 0.5rem; width: 30%;">User Label</th>
                  <th style="padding: 0.5rem; width: 35%;">Default Value</th>
                  <th style="padding: 0.5rem; width: 10%; text-align: right;">Action</th>
                </tr>
              </thead>
              <tbody id="variablesTableBody">
                <!-- Populated via Javascript -->
              </tbody>
            </table>
          </div>

          <input type="hidden" name="template_variables" id="prompt_variables" value="[]">
        </div>

        <div style="display: flex; justify-content: space-between; margin-top: 1.5rem;">
          <button type="button" onclick="switchPromptTab('prompt-tab-identity')" class="ts-btn ts-btn-outline">
            ← Back: Identity
          </button>
          <button type="button" onclick="switchPromptTab('prompt-tab-hyperparameters')" class="ts-btn ts-btn-primary">
            Next: Target Models &amp; Tuning →
          </button>
        </div>
      </div>

      <!-- TAB 3: TARGET MODELS & TUNING -->
      <div id="prompt-tab-hyperparameters" class="prompt-modal-tab" style="display: none;">
        <!-- Target Models Selector -->
        <div style="margin-bottom: 1.5rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
            <label style="font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc);">
              Target LLM &amp; Diffusion Models JSON Array
            </label>
            <span style="font-size: 0.72rem; color: var(--admin-text-secondary, #94a3b8);">Click pills below to toggle</span>
          </div>
          <input type="text" name="target_models" id="prompt_models" value='["ChatGPT 4o", "Claude 3.5 Sonnet"]' style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.65rem 1rem; border-radius: 6px; color: #38bdf8; font-family: var(--admin-font-mono, monospace); font-size: 0.85rem; margin-bottom: 0.6rem;">
          
          <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
            <button type="button" onclick="toggleModelTag('ChatGPT 4o')" class="ts-badge" style="cursor: pointer;">+ ChatGPT 4o</button>
            <button type="button" onclick="toggleModelTag('Claude 3.5 Sonnet')" class="ts-badge" style="cursor: pointer;">+ Claude 3.5 Sonnet</button>
            <button type="button" onclick="toggleModelTag('DeepSeek R1')" class="ts-badge" style="cursor: pointer;">+ DeepSeek R1</button>
            <button type="button" onclick="toggleModelTag('OpenAI o3-mini')" class="ts-badge" style="cursor: pointer;">+ o3-mini</button>
            <button type="button" onclick="toggleModelTag('Midjourney v6')" class="ts-badge ts-badge-pink" style="cursor: pointer;">+ Midjourney v6</button>
            <button type="button" onclick="toggleModelTag('Flux.1 Dev')" class="ts-badge ts-badge-pink" style="cursor: pointer;">+ Flux.1 Dev</button>
            <button type="button" onclick="toggleModelTag('DALL-E 3')" class="ts-badge ts-badge-pink" style="cursor: pointer;">+ DALL-E 3</button>
            <button type="button" onclick="toggleModelTag('Stable Diffusion XL')" class="ts-badge ts-badge-pink" style="cursor: pointer;">+ SDXL</button>
            <button type="button" onclick="toggleModelTag('Llama 3.3 70B')" class="ts-badge ts-badge-indigo" style="cursor: pointer;">+ Llama 3.3</button>
          </div>
        </div>

        <!-- Hyperparameters Tuning Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; background: var(--admin-card-bg-subtle, rgba(255,255,255,0.02)); padding: 1.25rem; border-radius: 10px; border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15));">
          <!-- Temperature -->
          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
              <label style="font-size: 0.8rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc);">Temperature</label>
              <span id="tempValueDisplay" style="font-size: 0.8rem; color: #38bdf8; font-weight: 700; font-family: var(--admin-font-mono, monospace);">0.70</span>
            </div>
            <input type="range" name="param_temperature" id="prompt_temperature" min="0" max="2" step="0.05" value="0.7" oninput="document.getElementById('tempValueDisplay').textContent = parseFloat(this.value).toFixed(2)" style="width: 100%; accent-color: #38bdf8;">
            <div style="display: flex; justify-content: space-between; font-size: 0.68rem; color: var(--admin-text-secondary, #64748b);">
              <span>0.0 (Deterministic)</span>
              <span>2.0 (Creative)</span>
            </div>
          </div>

          <!-- Top_P -->
          <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.35rem;">Top_P Sampling</label>
            <input type="number" name="param_top_p" id="prompt_top_p" min="0" max="1" step="0.05" value="0.95" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.45rem 0.75rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc); font-size: 0.85rem;">
          </div>

          <!-- Max Tokens -->
          <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.35rem;">Max Tokens</label>
            <input type="number" name="param_max_tokens" id="prompt_max_tokens" min="256" max="32768" step="256" value="4096" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.45rem 0.75rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc); font-size: 0.85rem;">
          </div>

          <!-- Difficulty -->
          <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.35rem;">Complexity Tier</label>
            <select name="param_difficulty" id="prompt_difficulty" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.45rem 0.75rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc); font-size: 0.85rem;">
              <option value="Beginner">Beginner Friendly</option>
              <option value="Intermediate" selected>Intermediate Engineer</option>
              <option value="Advanced">Advanced Architecture</option>
              <option value="Expert">Expert / Production</option>
            </select>
          </div>
        </div>

        <!-- Diffusion Prompt Settings (Negative Prompt & Aspect Ratio) -->
        <div id="diffusionParamContainer" style="display: none; background: rgba(236,72,153,0.03); border: 1px solid rgba(236,72,153,0.25); border-radius: 10px; padding: 1.25rem; margin-bottom: 1.5rem;">
          <div style="font-weight: 700; color: #ec4899; font-size: 0.88rem; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.4rem;">
            <i data-lucide="palette" style="width: 16px; height: 16px;"></i> Diffusion Model Hyperparameters
          </div>
          <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
            <div>
              <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.35rem;">Negative Prompt (--no parameters)</label>
              <input type="text" name="param_negative_prompt" id="prompt_negative_prompt" placeholder="e.g. text, watermark, blur, distorted fingers, oversaturated" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.5rem 0.75rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc); font-size: 0.85rem;">
            </div>
            <div>
              <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.35rem;">Aspect Ratio (--ar)</label>
              <select name="param_aspect_ratio" id="prompt_aspect_ratio" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.5rem 0.75rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc); font-size: 0.85rem;">
                <option value="16:9" selected>16:9 (Cinematic Landscape)</option>
                <option value="1:1">1:1 (Square Avatar / Asset)</option>
                <option value="9:16">9:16 (Vertical Story / Mobile)</option>
                <option value="4:5">4:5 (Social Portrait)</option>
                <option value="21:9">21:9 (Ultrawide Panoramic)</option>
              </select>
            </div>
          </div>
        </div>

        <div style="display: flex; justify-content: space-between; margin-top: 1.5rem;">
          <button type="button" onclick="switchPromptTab('prompt-tab-architecture')" class="ts-btn ts-btn-outline">
            ← Back: Variables
          </button>
          <button type="button" onclick="switchPromptTab('prompt-tab-playground')" class="ts-btn ts-btn-primary">
            Next: Live Playground &amp; Output →
          </button>
        </div>
      </div>

      <!-- TAB 4: LIVE PLAYGROUND & OUTPUT -->
      <div id="prompt-tab-playground" class="prompt-modal-tab" style="display: none;">
        <!-- Interactive Variable Test Simulator -->
        <div style="background: var(--admin-card-bg-subtle, rgba(255,255,255,0.02)); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); border-radius: 10px; padding: 1.25rem; margin-bottom: 1.5rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
            <div>
              <strong style="color: var(--admin-text-primary, #f8fafc); font-size: 0.92rem; display: flex; align-items: center; gap: 0.4rem;">
                <i data-lucide="play" style="width: 16px; height: 16px; color: #10b981;"></i> Live Variable Injection Simulator
              </strong>
              <span style="font-size: 0.75rem; color: var(--admin-text-secondary, #94a3b8);">Test how this blueprint renders in real-time before saving</span>
            </div>
            <button type="button" onclick="renderPlaygroundSimulation()" class="ts-btn ts-btn-sm ts-btn-primary" style="background: #10b981; border-color: #10b981;">
              ⚡ Run Test Injection
            </button>
          </div>

          <div id="playgroundVariableFields" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0.75rem; margin-bottom: 1rem;">
            <!-- Generated dynamically based on variable schema -->
          </div>

          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
              <label style="font-size: 0.78rem; font-weight: 600; color: var(--admin-text-secondary, #94a3b8);">Rendered Prompt Preview:</label>
              <button type="button" onclick="copyRenderedPlaygroundPrompt()" class="ts-btn ts-btn-sm ts-btn-outline" style="padding: 0.2rem 0.5rem; font-size: 0.72rem;">
                📋 Copy Rendered
              </button>
            </div>
            <pre id="playgroundRenderedOutput" style="background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); border-radius: 6px; padding: 0.75rem 1rem; color: #38bdf8; font-family: var(--admin-font-mono, monospace); font-size: 0.84rem; white-space: pre-wrap; margin: 0; min-height: 80px; max-height: 180px; overflow-y: auto;">Click "Run Test Injection" to render output...</pre>
          </div>
        </div>

        <!-- Sample Verified Model Output -->
        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.4rem;">
            Verified Model Response / Output Sample (Optional)
          </label>
          <textarea name="sample_output" id="prompt_sample_output" rows="4" placeholder="Optional verified output sample demonstrating the high-fidelity result achieved by this blueprint..." style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.75rem 1rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc); font-family: var(--admin-font-mono, monospace); font-size: 0.85rem;"></textarea>
        </div>

        <!-- Quality & Publishing Status -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.4rem;">
              Quality Benchmark Score (0 - 100)
            </label>
            <input type="number" name="quality_score" id="prompt_quality_score" min="0" max="100" step="0.5" value="96.5" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.65rem 0.85rem; border-radius: 6px; color: #10b981; font-weight: 700; font-size: 0.92rem;">
          </div>

          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.4rem;">
              Publication Status
            </label>
            <select name="status" id="prompt_status" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.65rem 0.85rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc); font-size: 0.92rem;">
              <option value="published" selected>Published &amp; Active</option>
              <option value="draft">Internal Draft</option>
              <option value="in_review">Editorial Review</option>
            </select>
          </div>
        </div>

        <!-- Save Button Footer -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding-top: 1.25rem;">
          <button type="button" onclick="switchPromptTab('prompt-tab-hyperparameters')" class="ts-btn ts-btn-outline">
            ← Back: Tuning
          </button>
          <div style="display: flex; gap: 0.75rem;">
            <button type="button" onclick="closePromptModal()" class="ts-btn ts-btn-outline">Cancel</button>
            <button type="submit" class="ts-btn ts-btn-primary" style="font-weight: 700;">
              Save Prompt Blueprint ⚡
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Standalone Quick Prompt Playground Drawer/Modal -->
<div id="promptPlaygroundModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-table-wrap" style="width: 100%; max-width: 780px; max-height: 90vh; overflow-y: auto; padding: 2rem; background: var(--admin-card-bg, #090d16); border: 1px solid rgba(16,185,129,0.35); box-shadow: 0 25px 60px rgba(0,0,0,0.9); border-radius: 14px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--admin-border-color, rgba(148,163,184,0.12)); padding-bottom: 0.75rem;">
      <div style="display: flex; align-items: center; gap: 0.65rem;">
        <div style="width: 34px; height: 34px; border-radius: 8px; background: rgba(16,185,129,0.15); color: #10b981; display: flex; align-items: center; justify-content: center;">
          <i data-lucide="play" style="width: 18px; height: 18px;"></i>
        </div>
        <div>
          <h2 id="quickPlaygroundTitle" style="font-size: 1.25rem; color: var(--admin-text-primary, #f8fafc); margin: 0; font-family: 'Outfit', sans-serif; font-weight: 700;">
            Prompt Variable Testing Playground
          </h2>
          <span id="quickPlaygroundSubtitle" style="font-size: 0.75rem; color: var(--admin-text-secondary, #64748b);">Inject custom variables and copy ready-to-run prompt</span>
        </div>
      </div>
      <button type="button" onclick="closeQuickPlayground()" style="background: none; border: none; color: var(--admin-text-secondary, #94a3b8); font-size: 1.6rem; cursor: pointer;">&times;</button>
    </div>

    <!-- Quick Form Fields -->
    <div id="quickPlaygroundFields" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.25rem;">
      <!-- Populated dynamically -->
    </div>

    <!-- Quick Output Area -->
    <div style="margin-bottom: 1.25rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
        <label style="font-size: 0.8rem; font-weight: 700; color: var(--admin-text-primary, #f8fafc);">Ready-to-Deploy Prompt:</label>
        <span id="quickTokenCount" style="font-size: 0.72rem; color: var(--admin-text-secondary, #94a3b8); font-family: var(--admin-font-mono, monospace);"></span>
      </div>
      <pre id="quickPlaygroundOutput" style="background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.2)); border-radius: 8px; padding: 1rem; color: #38bdf8; font-family: var(--admin-font-mono, monospace); font-size: 0.85rem; white-space: pre-wrap; margin: 0; max-height: 280px; overflow-y: auto; line-height: 1.6;"></pre>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding-top: 1.25rem;">
      <div id="quickExternalLinks" style="display: flex; gap: 0.5rem;">
        <a id="btnOpenChatGPT" href="https://chatgpt.com" target="_blank" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.75rem;">
          Open ChatGPT ↗
        </a>
        <a id="btnOpenClaude" href="https://claude.ai" target="_blank" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.75rem;">
          Open Claude ↗
        </a>
      </div>
      <div style="display: flex; gap: 0.5rem;">
        <button type="button" onclick="closeQuickPlayground()" class="ts-btn ts-btn-outline">Close</button>
        <button type="button" onclick="copyQuickPlaygroundPrompt()" class="ts-btn ts-btn-primary" style="background: #10b981; border-color: #10b981; font-weight: 700;">
          📋 Copy Prompt
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Main Prompts Index Table -->
<div class="admin-table-wrap">
  <table class="admin-table" id="promptsCrudTable" data-paginate="true" data-search-id="promptSearchInput" data-page-size="10">
    <thead>
      <tr>
        <th style="width: 60px;">Visual</th>
        <th>Prompt Name &amp; Summary</th>
        <th>Classification</th>
        <th>Category</th>
        <th>Target Models</th>
        <th>Telemetry</th>
        <th style="text-align: right;">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($prompts as $p): ?>
        <?php
          $isImage = (($p['prompt_type'] ?? '') === 'image');
          $imgUrl = !empty($p['featured_image_url']) ? image_url($p['featured_image_url']) : '';
          $variables = json_decode($p['template_variables'] ?? '[]', true) ?: [];
          $models = json_decode($p['target_models'] ?? '[]', true) ?: [];
          $notes = json_decode($p['internal_notes'] ?? '{}', true) ?: [];
          $isFeatured = !empty($p['is_featured']);
          $isTrending = !empty($p['is_trending']);
        ?>
        <tr data-type="<?= $isImage ? 'image' : 'text' ?>" data-featured="<?= $isFeatured ? '1' : '0' ?>" data-trending="<?= $isTrending ? '1' : '0' ?>">
          <td>
            <?php if ($isImage && !empty($imgUrl)): ?>
              <div style="width: 46px; height: 46px; border-radius: 8px; overflow: hidden; border: 1px solid rgba(236,72,153,0.3); background: #000; display: flex; align-items: center; justify-content: center;">
                <img src="<?= htmlspecialchars($imgUrl) ?>" alt="Visual" style="width: 100%; height: 100%; object-fit: cover;">
              </div>
            <?php else: ?>
              <div style="width: 46px; height: 46px; border-radius: 8px; background: rgba(56,189,248,0.08); border: 1px solid rgba(56,189,248,0.25); display: flex; align-items: center; justify-content: center; color: #38bdf8;">
                <i data-lucide="terminal" style="width: 22px; height: 22px;"></i>
              </div>
            <?php endif; ?>
          </td>
          <td>
            <div style="font-weight: 700; color: var(--admin-text-primary, #f8fafc); font-size: 0.95rem; display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
              <a href="<?= url('/prompts/' . $p['slug']) ?>" target="_blank" style="color: var(--admin-text-primary, #f8fafc); text-decoration: none;">
                <?= htmlspecialchars($p['title']) ?> ↗
              </a>
              <?php if (!empty($p['custom_badge'])): ?>
                <span class="ts-badge ts-badge-amber" style="font-size: 0.65rem;"><?= htmlspecialchars($p['custom_badge']) ?></span>
              <?php endif; ?>
              <?php if ($isFeatured): ?>
                <span class="ts-badge ts-badge-pink" style="font-size: 0.65rem;">⭐ Featured</span>
              <?php endif; ?>
            </div>
            <div style="font-size: 0.78rem; color: var(--admin-text-secondary, #94a3b8); margin-top: 0.25rem; line-height: 1.4;">
              <?= htmlspecialchars(mb_substr($p['summary'], 0, 85)) ?>...
            </div>
            <?php if (!empty($variables)): ?>
              <div style="margin-top: 0.35rem; display: flex; gap: 0.3rem; flex-wrap: wrap;">
                <?php foreach (array_slice($variables, 0, 3) as $var): ?>
                  <span style="font-size: 0.68rem; color: #38bdf8; background: rgba(56,189,248,0.08); padding: 0.1rem 0.35rem; border-radius: 4px; font-family: var(--admin-font-mono, monospace);">
                    {{<?= htmlspecialchars($var['name'] ?? 'var') ?>}}
                  </span>
                <?php endforeach; ?>
                <?php if (count($variables) > 3): ?>
                  <span style="font-size: 0.68rem; color: var(--admin-text-secondary, #64748b);">+<?= count($variables) - 3 ?> more</span>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </td>
          <td>
            <div style="display: flex; flex-direction: column; gap: 0.25rem; align-items: flex-start;">
              <span class="ts-badge <?= $isImage ? 'ts-badge-pink' : 'ts-badge-cyan' ?>" style="font-size: 0.72rem; font-weight: 700;">
                <?= $isImage ? '🎨 Diffusion' : '📝 Text / Code' ?>
              </span>
              <span style="font-size: 0.7rem; color: var(--admin-text-secondary, #64748b);">
                <?= count($variables) ?> variable(s)
              </span>
            </div>
          </td>
          <td>
            <span class="ts-badge" style="font-size: 0.75rem;"><?= htmlspecialchars($p['category_name'] ?? 'Prompt') ?></span>
          </td>
          <td>
            <div style="display: flex; gap: 0.25rem; flex-wrap: wrap; max-width: 190px;">
              <?php if (!empty($models)): ?>
                <?php foreach (array_slice($models, 0, 2) as $m): ?>
                  <span class="ts-badge ts-badge-indigo" style="font-size: 0.68rem;"><?= htmlspecialchars($m) ?></span>
                <?php endforeach; ?>
                <?php if (count($models) > 2): ?>
                  <span class="ts-badge" style="font-size: 0.68rem;">+<?= count($models) - 2 ?></span>
                <?php endif; ?>
              <?php else: ?>
                <span class="ts-badge" style="font-size: 0.68rem;">Universal</span>
              <?php endif; ?>
            </div>
          </td>
          <td>
            <div style="display: flex; flex-direction: column; gap: 0.2rem;">
              <span style="font-size: 0.85rem; font-weight: 700; color: #10b981; font-family: var(--admin-font-mono, monospace);">
                📋 <?= number_format($p['copy_count'] ?? 0) ?>
              </span>
              <span style="font-size: 0.7rem; color: var(--admin-text-secondary, #64748b);">
                Quality: <?= number_format($p['quality_score'] ?? 95.0, 1) ?>%
              </span>
            </div>
          </td>
          <td style="text-align: right;">
            <div style="display: inline-flex; gap: 0.35rem; flex-wrap: wrap; justify-content: flex-end;">
              <button onclick='openQuickPlayground(<?= json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; color: #10b981; border-color: rgba(16,185,129,0.35);" title="Test in Playground">
                ⚡ Test
              </button>
              <a href="<?= url('/admin/analytics/content/' . $p['id']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; color: #38bdf8; border-color: rgba(56,189,248,0.3);" title="Telemetry &amp; Audit">
                📊 Stats
              </a>
              <button onclick='editPrompt(<?= json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                Edit
              </button>
              <form action="<?= url('/admin/prompts/delete/' . $p['id']) ?>" method="POST" onsubmit="return confirm('Permanently delete this prompt blueprint?');" style="margin: 0;">
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
// Tab Switching System
function switchPromptTab(tabId) {
  document.querySelectorAll('.prompt-modal-tab').forEach(el => el.style.display = 'none');
  const target = document.getElementById(tabId);
  if (target) target.style.display = 'block';

  document.querySelectorAll('#promptModalTabs button').forEach(btn => {
    btn.classList.remove('ts-btn-primary');
    btn.classList.add('ts-btn-outline');
  });
  const activeBtn = document.getElementById('btn-' + tabId);
  if (activeBtn) {
    activeBtn.classList.remove('ts-btn-outline');
    activeBtn.classList.add('ts-btn-primary');
  }

  if (tabId === 'prompt-tab-playground') {
    populatePlaygroundSimulator();
  }
}

// Prompt Type Toggle
function setPromptType(type) {
  const isImage = (type === 'image');
  document.getElementById('prompt_type_text').checked = !isImage;
  document.getElementById('prompt_type_image').checked = isImage;

  const optText = document.getElementById('type_opt_text');
  const optImage = document.getElementById('type_opt_image');
  const uploadContainer = document.getElementById('imageUploadContainer');
  const textNotice = document.getElementById('textPromptNotice');
  const diffusionParams = document.getElementById('diffusionParamContainer');

  if (isImage) {
    optImage.style.borderColor = '#ec4899';
    optImage.style.background = 'rgba(236,72,153,0.06)';
    optText.style.borderColor = 'var(--admin-border-color, rgba(148,163,184,0.2))';
    optText.style.background = 'transparent';
    uploadContainer.style.display = 'block';
    textNotice.style.display = 'none';
    if (diffusionParams) diffusionParams.style.display = 'block';
  } else {
    optText.style.borderColor = '#38bdf8';
    optText.style.background = 'rgba(56,189,248,0.06)';
    optImage.style.borderColor = 'var(--admin-border-color, rgba(148,163,184,0.2))';
    optImage.style.background = 'transparent';
    uploadContainer.style.display = 'none';
    textNotice.style.display = 'flex';
    if (diffusionParams) diffusionParams.style.display = 'none';
  }
}

// Image Preview Handlers
function previewSelectedPromptImage(input) {
  if (input.files && input.files[0]) {
    const file = input.files[0];
    document.getElementById('uploadButtonText').textContent = file.name;
    const reader = new FileReader();
    reader.onload = function(e) {
      const img = document.getElementById('promptImagePreviewImg');
      img.src = e.target.result;
      img.style.display = 'block';
      document.getElementById('promptImagePreviewPlaceholder').style.display = 'none';
      document.getElementById('removeImageBtn').style.display = 'block';
    };
    reader.readAsDataURL(file);
  }
}

function previewUrlPromptImage(url) {
  const img = document.getElementById('promptImagePreviewImg');
  const placeholder = document.getElementById('promptImagePreviewPlaceholder');
  const removeBtn = document.getElementById('removeImageBtn');

  if (url && url.trim().length > 4) {
    img.src = url;
    img.style.display = 'block';
    placeholder.style.display = 'none';
    removeBtn.style.display = 'block';
  } else {
    img.style.display = 'none';
    placeholder.style.display = 'block';
    removeBtn.style.display = 'none';
  }
}

function clearPromptImagePreview() {
  document.getElementById('prompt_image_file').value = '';
  document.getElementById('prompt_featured_image_url').value = '';
  document.getElementById('uploadButtonText').textContent = 'Choose Showcase Image File';
  document.getElementById('promptImagePreviewImg').style.display = 'none';
  document.getElementById('promptImagePreviewImg').src = '';
  document.getElementById('promptImagePreviewPlaceholder').style.display = 'block';
  document.getElementById('removeImageBtn').style.display = 'none';
}

// Target Models Cloud Toggle
function toggleModelTag(modelName) {
  const input = document.getElementById('prompt_models');
  let models = [];
  try {
    models = JSON.parse(input.value || '[]');
    if (!Array.isArray(models)) models = [];
  } catch (e) {
    models = [];
  }

  const idx = models.indexOf(modelName);
  if (idx > -1) {
    models.splice(idx, 1);
  } else {
    models.push(modelName);
  }
  input.value = JSON.stringify(models);
}

// Variables Table System
let currentVariables = [];

function renderVariablesTable() {
  const tbody = document.getElementById('variablesTableBody');
  tbody.innerHTML = '';

  currentVariables.forEach((v, index) => {
    const tr = document.createElement('tr');
    tr.style.borderBottom = '1px solid var(--admin-border-color, rgba(148,163,184,0.1))';
    tr.innerHTML = `
      <td style="padding: 0.5rem;">
        <input type="text" value="${escapeHtml(v.name || '')}" onchange="updateVariableField(${index}, 'name', this.value)" placeholder="variable_name" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.2)); padding: 0.35rem 0.5rem; border-radius: 4px; color: #38bdf8; font-family: var(--admin-font-mono, monospace); font-size: 0.8rem;">
      </td>
      <td style="padding: 0.5rem;">
        <input type="text" value="${escapeHtml(v.label || '')}" onchange="updateVariableField(${index}, 'label', this.value)" placeholder="User Facing Label" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.2)); padding: 0.35rem 0.5rem; border-radius: 4px; color: var(--admin-text-primary, #f8fafc); font-size: 0.8rem;">
      </td>
      <td style="padding: 0.5rem;">
        <input type="text" value="${escapeHtml(v.default || '')}" onchange="updateVariableField(${index}, 'default', this.value)" placeholder="Default value" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.2)); padding: 0.35rem 0.5rem; border-radius: 4px; color: var(--admin-text-primary, #f8fafc); font-size: 0.8rem;">
      </td>
      <td style="padding: 0.5rem; text-align: right;">
        <button type="button" onclick="removeVariableRow(${index})" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 0.2rem 0.4rem; font-size: 1rem;">&times;</button>
      </td>
    `;
    tbody.appendChild(tr);
  });

  syncVariablesJson();
  renderVariablePills();
}

function addVariableRow(name = '', label = '', defaultValue = '') {
  currentVariables.push({
    name: name || 'var_' + (currentVariables.length + 1),
    label: label || 'Custom Parameter',
    default: defaultValue || ''
  });
  renderVariablesTable();
}

function removeVariableRow(index) {
  currentVariables.splice(index, 1);
  renderVariablesTable();
}

function updateVariableField(index, field, value) {
  if (currentVariables[index]) {
    currentVariables[index][field] = value;
    syncVariablesJson();
    renderVariablePills();
  }
}

function syncVariablesJson() {
  document.getElementById('prompt_variables').value = JSON.stringify(currentVariables);
}

function renderVariablePills() {
  const container = document.getElementById('variableInsertPills');
  if (!container) return;
  container.innerHTML = '<span style="font-size: 0.72rem; color: var(--admin-text-secondary, #94a3b8); margin-right: 0.25rem;">Insert into template:</span>';

  currentVariables.forEach(v => {
    if (!v.name) return;
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'ts-badge ts-badge-cyan';
    btn.style.cursor = 'pointer';
    btn.textContent = '+ {{' + v.name + '}}';
    btn.onclick = function() { insertTagIntoTemplate('{{' + v.name + '}}'); };
    container.appendChild(btn);
  });
}

function insertTagIntoTemplate(tag) {
  const textarea = document.getElementById('prompt_template_body');
  const startPos = textarea.selectionStart;
  const endPos = textarea.selectionEnd;
  textarea.value = textarea.value.substring(0, startPos) + tag + textarea.value.substring(endPos, textarea.value.length);
  textarea.focus();
  textarea.selectionStart = startPos + tag.length;
  textarea.selectionEnd = startPos + tag.length;
  updateTemplateStats();
}

function autoDetectVariables() {
  const template = document.getElementById('prompt_template_body').value;
  const matches = template.match(/\{\{([a-zA-Z0-9_\-]+)\}\}/g);
  if (!matches) return;

  const foundNames = [...new Set(matches.map(m => m.replace(/[\{\}]/g, '').trim()))];
  let changed = false;

  foundNames.forEach(name => {
    if (!currentVariables.some(v => v.name === name)) {
      currentVariables.push({
        name: name,
        label: name.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()),
        default: ''
      });
      changed = true;
    }
  });

  if (changed) {
    renderVariablesTable();
  }
}

function updateTemplateStats() {
  const text = document.getElementById('prompt_template_body').value;
  const charCount = text.length;
  const approxTokens = Math.ceil(charCount / 4);
  document.getElementById('promptTokenCounter').textContent = `${charCount} chars | ~${approxTokens} tokens`;
}

// Live Variable Injection Simulator (Modal Tab 4)
function populatePlaygroundSimulator() {
  const container = document.getElementById('playgroundVariableFields');
  container.innerHTML = '';

  if (currentVariables.length === 0) {
    container.innerHTML = '<span style="color: var(--admin-text-secondary, #94a3b8); font-size: 0.8rem; grid-column: 1 / -1;">No variables defined yet. Add variables in Tab 2 to preview live injection.</span>';
    return;
  }

  currentVariables.forEach(v => {
    const div = document.createElement('div');
    div.innerHTML = `
      <label style="display: block; font-size: 0.75rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.25rem;">${escapeHtml(v.label || v.name)}</label>
      <input type="text" data-var="${escapeHtml(v.name)}" value="${escapeHtml(v.default || '')}" placeholder="Value..." oninput="renderPlaygroundSimulation()" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.2)); padding: 0.4rem 0.6rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc); font-size: 0.82rem;">
    `;
    container.appendChild(div);
  });

  renderPlaygroundSimulation();
}

function renderPlaygroundSimulation() {
  let template = document.getElementById('prompt_template_body').value;
  const inputs = document.querySelectorAll('#playgroundVariableFields input[data-var]');

  inputs.forEach(inp => {
    const varName = inp.getAttribute('data-var');
    const val = inp.value || `[${varName}]`;
    const regex = new RegExp(`\\{\\{\\s*${varName}\\s*\\}\\}`, 'g');
    template = template.replace(regex, val);
  });

  document.getElementById('playgroundRenderedOutput').textContent = template;
}

function copyRenderedPlaygroundPrompt() {
  const text = document.getElementById('playgroundRenderedOutput').textContent;
  navigator.clipboard.writeText(text).then(() => {
    if (window.Toast) Toast.success('Rendered prompt copied to clipboard!');
    else alert('Rendered prompt copied to clipboard!');
  });
}

// Quick Playground Standalone Modal
let quickPromptData = null;

function openQuickPlayground(p) {
  quickPromptData = p;
  document.getElementById('quickPlaygroundTitle').textContent = p.title || 'Prompt Playground';
  
  let variables = [];
  try {
    variables = JSON.parse(p.template_variables || '[]');
  } catch(e) { variables = []; }

  const fieldsContainer = document.getElementById('quickPlaygroundFields');
  fieldsContainer.innerHTML = '';

  if (variables.length > 0) {
    variables.forEach(v => {
      const div = document.createElement('div');
      div.innerHTML = `
        <label style="display: block; font-size: 0.78rem; font-weight: 600; color: var(--admin-text-primary, #f8fafc); margin-bottom: 0.25rem;">${escapeHtml(v.label || v.name)}</label>
        <input type="text" data-quick-var="${escapeHtml(v.name)}" value="${escapeHtml(v.default || '')}" oninput="updateQuickPlayground()" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.2)); padding: 0.45rem 0.65rem; border-radius: 6px; color: var(--admin-text-primary, #f8fafc); font-size: 0.85rem;">
      `;
      fieldsContainer.appendChild(div);
    });
  } else {
    fieldsContainer.innerHTML = '<span style="color: var(--admin-text-secondary, #94a3b8); font-size: 0.8rem; grid-column: 1 / -1;">Static blueprint template with no dynamic variables.</span>';
  }

  updateQuickPlayground();
  document.getElementById('promptPlaygroundModal').style.display = 'flex';
}

function updateQuickPlayground() {
  if (!quickPromptData) return;
  let template = quickPromptData.prompt_template || '';
  const inputs = document.querySelectorAll('#quickPlaygroundFields input[data-quick-var]');

  inputs.forEach(inp => {
    const varName = inp.getAttribute('data-quick-var');
    const val = inp.value || `[${varName}]`;
    const regex = new RegExp(`\\{\\{\\s*${varName}\\s*\\}\\}`, 'g');
    template = template.replace(regex, val);
  });

  const outputEl = document.getElementById('quickPlaygroundOutput');
  outputEl.textContent = template;
  document.getElementById('quickTokenCount').textContent = `${template.length} chars | ~${Math.ceil(template.length / 4)} tokens`;
}

function copyQuickPlaygroundPrompt() {
  const text = document.getElementById('quickPlaygroundOutput').textContent;
  navigator.clipboard.writeText(text).then(() => {
    if (window.Toast) Toast.success('Prompt blueprint copied to clipboard!');
    else alert('Prompt blueprint copied to clipboard!');
  });
}

function closeQuickPlayground() {
  document.getElementById('promptPlaygroundModal').style.display = 'none';
  quickPromptData = null;
}

// Main Modal Open/Close/Edit
function openNewPromptModal() {
  document.getElementById('adminPromptForm').reset();
  document.getElementById('prompt_id').value = '';
  document.getElementById('modalPromptTitle').textContent = 'Create Prompt Blueprint';
  clearPromptImagePreview();
  setPromptType('text');

  currentVariables = [
    { name: 'framework', label: 'Target Framework / Language', default: 'PHP 8.2 / MySQL' }
  ];
  renderVariablesTable();

  document.getElementById('prompt_models').value = '["ChatGPT 4o", "Claude 3.5 Sonnet"]';
  document.getElementById('prompt_temperature').value = '0.7';
  document.getElementById('tempValueDisplay').textContent = '0.70';
  document.getElementById('prompt_top_p').value = '0.95';
  document.getElementById('prompt_max_tokens').value = '4096';
  document.getElementById('prompt_quality_score').value = '96.5';
  document.getElementById('prompt_status').value = 'published';

  switchPromptTab('prompt-tab-identity');
  document.getElementById('promptFormModal').style.display = 'flex';
}

function editPrompt(p) {
  document.getElementById('adminPromptForm').reset();
  document.getElementById('prompt_id').value = p.id;
  document.getElementById('modalPromptTitle').textContent = 'Edit Blueprint #' + p.id + ' — ' + (p.title || '');

  document.getElementById('prompt_title').value = p.title || '';
  document.getElementById('prompt_category_id').value = p.category_id || 1;
  document.getElementById('prompt_summary').value = p.summary || '';
  document.getElementById('prompt_custom_badge').value = p.custom_badge || '';
  document.getElementById('prompt_is_featured').checked = (parseInt(p.is_featured) === 1);
  document.getElementById('prompt_is_trending').checked = (parseInt(p.is_trending) === 1);
  document.getElementById('prompt_quality_score').value = p.quality_score || 95.0;
  document.getElementById('prompt_status').value = p.status || 'published';

  const promptType = (p.prompt_type === 'image') ? 'image' : 'text';
  setPromptType(promptType);

  if (p.featured_image_url) {
    document.getElementById('prompt_featured_image_url').value = p.featured_image_url;
    previewUrlPromptImage(p.featured_image_url);
  } else {
    clearPromptImagePreview();
  }

  document.getElementById('prompt_template_body').value = p.prompt_template || '';
  updateTemplateStats();

  try {
    currentVariables = JSON.parse(p.template_variables || '[]');
    if (!Array.isArray(currentVariables)) currentVariables = [];
  } catch(e) {
    currentVariables = [];
  }
  renderVariablesTable();

  document.getElementById('prompt_models').value = p.target_models || '["ChatGPT 4o"]';
  document.getElementById('prompt_sample_output').value = p.sample_output || '';

  // Internal Notes parse
  try {
    const notes = JSON.parse(p.internal_notes || '{}');
    if (notes.temperature !== undefined) {
      document.getElementById('prompt_temperature').value = notes.temperature;
      document.getElementById('tempValueDisplay').textContent = parseFloat(notes.temperature).toFixed(2);
    }
    if (notes.top_p !== undefined) document.getElementById('prompt_top_p').value = notes.top_p;
    if (notes.max_tokens !== undefined) document.getElementById('prompt_max_tokens').value = notes.max_tokens;
    if (notes.difficulty !== undefined) document.getElementById('prompt_difficulty').value = notes.difficulty;
    if (notes.author_handle !== undefined) document.getElementById('prompt_author_handle').value = notes.author_handle;
    if (notes.negative_prompt !== undefined) document.getElementById('prompt_negative_prompt').value = notes.negative_prompt;
    if (notes.aspect_ratio !== undefined) document.getElementById('prompt_aspect_ratio').value = notes.aspect_ratio;
  } catch(e) {}

  switchPromptTab('prompt-tab-identity');
  document.getElementById('promptFormModal').style.display = 'flex';
}

function closePromptModal() {
  document.getElementById('promptFormModal').style.display = 'none';
}

// Filter Functionality
function filterPromptsByType(filterType, btn) {
  document.querySelectorAll('.prompt-filter-pill').forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });
  btn.classList.remove('ts-btn-outline');
  btn.classList.add('ts-btn-primary');

  const rows = document.querySelectorAll('#promptsCrudTable tbody tr');
  rows.forEach(row => {
    const type = row.getAttribute('data-type');
    const isFeatured = row.getAttribute('data-featured') === '1';
    const isTrending = row.getAttribute('data-trending') === '1';

    let match = true;
    if (filterType === 'text') match = (type === 'text');
    else if (filterType === 'image') match = (type === 'image');
    else if (filterType === 'featured') match = isFeatured;
    else if (filterType === 'trending') match = isTrending;

    row.style.display = match ? '' : 'none';
  });
}

function escapeHtml(text) {
  if (!text) return '';
  return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

document.addEventListener('DOMContentLoaded', function() {
  if (window.paginateAdminTable) {
    window.promptsPager = window.paginateAdminTable('promptsCrudTable', {
      pageSize: 10,
      searchInputId: 'promptSearchInput'
    });
  }
  if (window.lucide) {
    lucide.createIcons();
  }
});
</script>

<?php require __DIR__ . '/../../layouts/admin_footer.php'; ?>
