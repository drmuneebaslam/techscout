<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
$canPrompts = !empty($author['can_publish_prompts']) || \App\Core\Auth::isAdmin();
$canGpts = !empty($author['can_publish_gpts']) || \App\Core\Auth::isAdmin();
$canTools = !empty($author['can_publish_tools']) || \App\Core\Auth::isAdmin();
$canTemplates = !empty($author['can_use_templates']) || \App\Core\Auth::isAdmin();
$currentType = $content['content_type'] ?? 'article';
?>
<script>
window.CAN_USE_TEMPLATES = <?= $canTemplates ? 'true' : 'false' ?>;
</script>

<div class="container" style="padding-top: 2rem; margin-bottom: 5rem; max-width: 1200px;">
  <!-- Header Bar -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <a href="<?= url('/author/dashboard') ?>" class="ts-badge ts-badge-cyan" style="text-decoration: none;">← Return to Studio</a>
        <span class="ts-badge ts-badge-pink">Visual Document Canvas</span>
      </div>
      <h1 style="font-size: 1.85rem; margin: 0; color: #f8fafc;">
        <?= $content ? 'Edit Publication: ' . htmlspecialchars($content['title']) : 'Authoring & Engineering Studio' ?>
      </h1>
    </div>

    <!-- Quick Action Controls -->
    <div style="display: flex; gap: 0.6rem; align-items: center; flex-wrap: wrap;">
      <button type="button" onclick="openDocxModal()" class="ts-btn ts-btn-outline" style="border-color: #38bdf8; color: #38bdf8;">
        <i data-lucide="file-up"></i> Import Word (.docx) 📄
      </button>
      <?php if ($canTemplates): ?>
        <button type="button" onclick="openTemplatesModal()" class="ts-btn ts-btn-outline" style="border-color: #ec4899; color: #f472b6;">
          <i data-lucide="layout-template"></i> Templates 📋
        </button>
        <button type="button" onclick="saveCustomTemplate()" class="ts-btn ts-btn-outline" title="Save current document as custom reusable template">
          <i data-lucide="bookmark-plus"></i> Save as Template
        </button>
      <?php endif; ?>
      <button type="button" onclick="submitEditorForm('draft')" class="ts-btn ts-btn-outline">
        <i data-lucide="save"></i> Save Draft
      </button>
      <button type="button" onclick="submitEditorForm('submit')" class="ts-btn ts-btn-primary">
        <i data-lucide="send"></i> Submit for Review 🚀
      </button>
    </div>
  </div>

  <!-- Main Authoring Form -->
  <form id="authorStudioForm" action="<?= url('/author/editor/save') ?>" method="POST">
    <input type="hidden" name="id" id="doc_id" value="<?= $content['id'] ?? '' ?>">
    <input type="hidden" name="action" id="form_action_input" value="draft">

    <?php if (!empty($content['editorial_notes'])): ?>
      <div style="background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.4); border-radius: 8px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; display: flex; align-items: flex-start; gap: 1rem;">
        <div style="padding: 0.4rem; background: rgba(245, 158, 11, 0.2); border-radius: 6px; color: #f59e0b; flex-shrink: 0;">
          <i data-lucide="alert-circle" style="width: 20px; height: 20px;"></i>
        </div>
        <div style="flex: 1;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
            <strong style="color: #fbbf24; font-size: 0.95rem;">📋 Editorial Revision Requested</strong>
            <span class="ts-badge ts-badge-pink" style="font-size: 0.65rem;">ACTION REQUIRED</span>
          </div>
          <div style="color: #e2e8f0; font-size: 0.9rem; line-height: 1.5; margin-bottom: 0.75rem;">
            <?= nl2br(htmlspecialchars($content['editorial_notes'])) ?>
          </div>
          <button type="button" onclick="submitEditorForm('submit')" class="ts-btn ts-btn-primary ts-btn-sm" style="font-weight: 700; background: #f59e0b; border-color: #f59e0b; color: #000;">
            <i data-lucide="send"></i> Submit Revisions to Editorial Staff 🚀
          </button>
        </div>
      </div>
    <?php endif; ?>

    <!-- Prominent Author Recommendation Banner: Prefer Word (.docx) Submissions -->
    <div class="ts-card ts-docx-recommendation-card" style="margin-bottom: 1.5rem; padding: 1.25rem 1.5rem; background: rgba(56, 189, 248, 0.08); border: 1px solid rgba(56, 189, 248, 0.35); border-radius: 12px; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
      <div style="display: flex; align-items: flex-start; gap: 0.85rem; flex: 1; min-width: 280px;">
        <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(56, 189, 248, 0.18); display: flex; align-items: center; justify-content: center; color: #38bdf8; flex-shrink: 0;">
          <i data-lucide="file-text" style="width: 22px; height: 22px;"></i>
        </div>
        <div>
          <div style="font-weight: 700; color: var(--ts-text-primary); font-size: 0.98rem; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
            <span>Editorial Submission Recommendation: Word (.docx) Submissions Strongly Preferred</span>
            <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem;">FASTER REVIEW</span>
          </div>
          <p style="margin: 0.25rem 0 0 0; font-size: 0.86rem; color: var(--ts-text-secondary); line-height: 1.45;">
            Authors are strongly encouraged to submit manuscripts in Microsoft Word (<code>.docx</code>) format. Word documents preserve embedded architecture diagrams, formatted tables, code listings, callouts, and heading hierarchies with 100% fidelity.
          </p>
        </div>
      </div>
      <div style="display: flex; gap: 0.5rem; align-items: center;">
        <button type="button" onclick="openDocxModal()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #0284c7, #0ea5e9); border: none; font-weight: 600; padding: 0.5rem 1.15rem; border-radius: 8px;">
          <i data-lucide="upload" style="width: 14px; height: 14px;"></i> Upload .docx File
        </button>
      </div>
    </div>

    <!-- Document Metadata & Configuration Card -->
    <div class="ts-card" style="padding: 1.75rem; margin-bottom: 1.5rem; background: rgba(13,19,34,0.85); border-color: rgba(99,102,241,0.25);">
      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 0.04em;">
          Publication Headline / Title <span style="color: #ef4444;">*</span> <span style="font-size: 0.75rem; color: #94a3b8; font-weight: normal; text-transform: none;">(Max 150 chars)</span>
        </label>
        <input type="text" name="title" id="doc_title" maxlength="150" value="<?= htmlspecialchars($content['title'] ?? '') ?>" placeholder="e.g. Architecting Distributed High-Load RAG Systems with MySQL 8.0 & PHP" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.85rem 1.25rem; border-radius: var(--ts-radius-md); color: #f8fafc; font-size: 1.2rem; font-weight: 700;" required>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
        <!-- Content Type with Permission Enforcement -->
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 0.04em;">
            Publication Format / Type
          </label>
          <select name="content_type" id="doc_content_type" onchange="onContentTypeChanged(this.value)" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.75rem 1rem; border-radius: var(--ts-radius-md); color: #f8fafc; font-size: 0.95rem;">
            <option value="article" <?= $currentType === 'article' ? 'selected' : '' ?>>
              📝 Technical Article / Architectural Tutorial
            </option>
            
            <?php if ($canPrompts): ?>
              <option value="prompt" <?= $currentType === 'prompt' ? 'selected' : '' ?>>
                ⚡ Prompt Blueprint & Template (Authorized)
              </option>
            <?php else: ?>
              <option value="prompt" disabled style="color: #64748b;">
                🔒 Prompt Blueprint (Admin Approval Required)
              </option>
            <?php endif; ?>

            <?php if ($canGpts): ?>
              <option value="gpt_listing" <?= $currentType === 'gpt_listing' ? 'selected' : '' ?>>
                🤖 Custom GPT & Agent Blueprint (Authorized)
              </option>
            <?php else: ?>
              <option value="gpt_listing" disabled style="color: #64748b;">
                🔒 Custom GPT Blueprint (Admin Approval Required)
              </option>
            <?php endif; ?>

            <?php if ($canTools): ?>
              <option value="ai_tool" <?= $currentType === 'ai_tool' ? 'selected' : '' ?>>
                🛠️ AI Tool Benchmark / Review (Authorized)
              </option>
            <?php else: ?>
              <option value="ai_tool" disabled style="color: #64748b;">
                🔒 AI Tool Review (Admin Approval Required)
              </option>
            <?php endif; ?>
          </select>
          
          <?php if (!$canPrompts || !$canGpts || !$canTools): ?>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.35rem;">
              <span>💡 By default, authors can publish articles. Contact staff to unlock Prompt/GPT publishing.</span>
            </div>
          <?php endif; ?>
        </div>

        <!-- Category -->
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 0.04em;">
            Primary Engineering Domain
          </label>
          <select name="category_id" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.75rem 1rem; border-radius: var(--ts-radius-md); color: #f8fafc; font-size: 0.95rem;">
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>" <?= ($content['category_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?> (<?= strtoupper($cat['applicable_to'] ?? 'ALL') ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 0.04em;">
            Executive Summary / Abstract <span style="color: #ef4444;">*</span> <span style="font-size: 0.75rem; color: #94a3b8; font-weight: normal; text-transform: none;">(Max 300 chars)</span>
          </label>
          <textarea name="summary" id="doc_summary" rows="2" maxlength="300" placeholder="Brief 1-2 sentence executive synopsis of what engineers will learn..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.75rem 1rem; border-radius: var(--ts-radius-md); color: #f8fafc; resize: vertical;" required><?= htmlspecialchars($content['summary'] ?? '') ?></textarea>
        </div>

        <div>
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #f8fafc; text-transform: uppercase; letter-spacing: 0.04em;">
              Cover / Featured Image
            </label>
            <label class="ts-btn ts-btn-outline ts-btn-sm" style="cursor: pointer; padding: 0.2rem 0.6rem; font-size: 0.75rem; border-color: #38bdf8; color: #38bdf8;">
              <i data-lucide="upload" style="width: 12px; height: 12px;"></i> Upload from Device 📁
              <input type="file" accept="image/*" onchange="uploadFeaturedImage(this)" style="display: none;">
            </label>
          </div>
          <div style="display: flex; gap: 0.5rem; align-items: center;">
            <input type="url" name="featured_image_url" id="doc_cover" value="<?= htmlspecialchars(image_url($content['featured_image_url'] ?? '')) ?>" placeholder="https://images.unsplash.com/... or uploaded image URL" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.75rem 1rem; border-radius: var(--ts-radius-md); color: #f8fafc;">
          </div>
        </div>
      </div>

      <!-- Multiple Tags & Keywords Field -->
      <?php $existingTagsStr = !empty($content['id']) ? \App\Core\TagHelper::getTagsString((int)$content['id']) : ''; ?>
      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 0.04em;">
          Tags & Technologies (Comma-Separated &bull; Add Unlimited Tags)
        </label>
        <input type="text" name="tags" value="<?= htmlspecialchars($existingTagsStr) ?>" placeholder="e.g. distributed-systems, python, postgresql, low-latency, performance, llm" style="width: 100%; background: #05070b; border: 1px solid rgba(99,102,241,0.3); padding: 0.75rem 1rem; border-radius: var(--ts-radius-md); color: #38bdf8; font-size: 0.95rem;">
        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.35rem;">
          Assign 1 primary category above, and attach any number of searchable technical tags.
        </div>
      </div>

      <!-- Entity-Specific Dynamic Panes -->
      <!-- Article Spec Pane -->
      <div id="pane_article_spec" style="<?= $currentType === 'article' ? 'display: block;' : 'display: none;' ?> border-top: 1px solid rgba(148,163,184,0.1); padding-top: 1rem; margin-top: 0.5rem;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #94a3b8; margin-bottom: 0.3rem;">Technical Difficulty Level</label>
            <select name="difficulty_level" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.5rem 0.75rem; border-radius: 6px; color: #f8fafc;">
              <option value="beginner" <?= ($content['difficulty_level'] ?? '') === 'beginner' ? 'selected' : '' ?>>Beginner Friendly</option>
              <option value="intermediate" <?= ($content['difficulty_level'] ?? 'intermediate') === 'intermediate' ? 'selected' : '' ?>>Intermediate</option>
              <option value="advanced" <?= ($content['difficulty_level'] ?? '') === 'advanced' ? 'selected' : '' ?>>Advanced Systems</option>
              <option value="staff_plus" <?= ($content['difficulty_level'] ?? '') === 'staff_plus' ? 'selected' : '' ?>>Staff+ Deep Dive</option>
            </select>
          </div>
          <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #94a3b8; margin-bottom: 0.3rem;">Target Audience</label>
            <input type="text" placeholder="e.g. Backend Engineers, DBA, DevOps" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.5rem 0.75rem; border-radius: 6px; color: #f8fafc;">
          </div>
        </div>
      </div>

      <!-- Prompt Spec Pane -->
      <div id="pane_prompt_spec" style="<?= $currentType === 'prompt' ? 'display: block;' : 'display: none;' ?> border-top: 1px solid rgba(148,163,184,0.1); padding-top: 1rem; margin-top: 0.5rem;">
        <div style="margin-bottom: 0.75rem;">
          <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #38bdf8; margin-bottom: 0.3rem;">
            Dynamic Template Variables (Comma-separated, e.g. <code>LANGUAGE, FRAMEWORK, DATABASE_SCHEMA</code>)
          </label>
          <input type="text" name="template_variables" value="<?= htmlspecialchars(!empty($content['template_variables']) ? (is_array($content['template_variables']) ? implode(', ', $content['template_variables']) : $content['template_variables']) : 'QUERY, SCHEMA, BUFFER_POOL') ?>" placeholder="e.g. QUERY, SCHEMA, INDEX_KEYS" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.6rem 0.85rem; border-radius: 6px; color: #f8fafc; font-family: monospace;">
        </div>
      </div>

      <!-- Custom GPT Spec Pane -->
      <div id="pane_gpt_spec" style="<?= $currentType === 'gpt_listing' ? 'display: block;' : 'display: none;' ?> border-top: 1px solid rgba(148,163,184,0.1); padding-top: 1rem; margin-top: 0.5rem;">
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 0.75rem;">
          <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #ec4899; margin-bottom: 0.3rem;">OpenAI GPT Store Link / Shared URL</label>
            <input type="url" name="openai_url" value="<?= htmlspecialchars($content['openai_url'] ?? '') ?>" placeholder="https://chatgpt.com/g/g-..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.6rem 0.85rem; border-radius: 6px; color: #f8fafc;">
          </div>
          <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #ec4899; margin-bottom: 0.3rem;">Model Base</label>
            <input type="text" name="model_base" value="<?= htmlspecialchars($content['model_base'] ?? 'GPT-4o') ?>" placeholder="GPT-4o, O3-Mini..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.6rem 0.85rem; border-radius: 6px; color: #f8fafc;">
          </div>
        </div>
        <div>
          <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #ec4899; margin-bottom: 0.3rem;">Prompt Starters (One starter per line)</label>
          <textarea name="prompt_starters" rows="3" placeholder="Review my SQL query for index bottlenecks&#10;Audit my JWT authentication pipeline&#10;Optimize my Redis streaming fanout" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.6rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.85rem;"><?= htmlspecialchars(!empty($content['prompt_starters']) ? (is_array($content['prompt_starters']) ? implode("\n", $content['prompt_starters']) : $content['prompt_starters']) : '') ?></textarea>
        </div>
      </div>

      <!-- AI Tool Spec Pane -->
      <div id="pane_tool_spec" style="<?= $currentType === 'ai_tool' ? 'display: block;' : 'display: none;' ?> border-top: 1px solid rgba(148,163,184,0.1); padding-top: 1rem; margin-top: 0.5rem;">
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 0.75rem;">
          <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #34d399; margin-bottom: 0.3rem;">Official Website URL</label>
            <input type="url" name="website_url" value="<?= htmlspecialchars($content['website_url'] ?? '') ?>" placeholder="https://toolname.com" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.6rem 0.85rem; border-radius: 6px; color: #f8fafc;">
          </div>
          <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #34d399; margin-bottom: 0.3rem;">Pricing Model</label>
            <select name="pricing_model" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.6rem 0.85rem; border-radius: 6px; color: #f8fafc;">
              <option value="freemium" <?= ($content['pricing_model'] ?? '') === 'freemium' ? 'selected' : '' ?>>Freemium</option>
              <option value="free" <?= ($content['pricing_model'] ?? '') === 'free' ? 'selected' : '' ?>>100% Free</option>
              <option value="paid" <?= ($content['pricing_model'] ?? '') === 'paid' ? 'selected' : '' ?>>Paid Subscription</option>
              <option value="open_source" <?= ($content['pricing_model'] ?? '') === 'open_source' ? 'selected' : '' ?>>Open Source</option>
            </select>
          </div>
          <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #34d399; margin-bottom: 0.3rem;">Starting Price ($/mo)</label>
            <input type="number" step="0.01" name="pricing_starting_at" value="<?= htmlspecialchars((string)($content['pricing_starting_at'] ?? '0')) ?>" placeholder="20.00" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.15); padding: 0.6rem 0.85rem; border-radius: 6px; color: #f8fafc;">
          </div>
        </div>
        <div style="display: flex; gap: 1.5rem; align-items: center;">
          <label style="display: flex; align-items: center; gap: 0.5rem; color: #f8fafc; font-size: 0.85rem; cursor: pointer;">
            <input type="checkbox" name="free_trial_available" value="1" <?= !empty($content['free_trial_available']) ? 'checked' : '' ?> style="accent-color: #34d399;"> Free Trial Available
          </label>
          <label style="display: flex; align-items: center; gap: 0.5rem; color: #f8fafc; font-size: 0.85rem; cursor: pointer;">
            <input type="checkbox" name="api_available" value="1" <?= !empty($content['api_available']) ? 'checked' : '' ?> style="accent-color: #34d399;"> REST API Available
          </label>
        </div>
      </div>
    </div>

    <!-- MICROSOFT WORD VISUAL WYSIWYG DOCUMENT STUDIO -->
    <div class="word-editor-wrapper" id="wordEditorContainer">
      <!-- Ribbon Toolbar -->
      <div class="word-ribbon" id="wordRibbon">
        <?php if ($canTemplates): ?>
          <!-- Group 0: Templates Inserter -->
          <div class="ribbon-group">
            <button type="button" class="ribbon-btn" onclick="openTemplatesModal()" style="background: rgba(236,72,153,0.2); color: #f472b6; border: 1px solid rgba(236,72,153,0.4);" title="Load Curated Blueprint Template">
              <i data-lucide="layout-template"></i> <strong>Templates</strong>
            </button>
          </div>

          <div class="ribbon-separator"></div>
        <?php endif; ?>

        <!-- Group 1: Undo / Redo -->
        <div class="ribbon-group">
          <button type="button" class="ribbon-btn" onclick="execCmd('undo')" title="Undo (Ctrl+Z)">
            <i data-lucide="undo-2"></i>
          </button>
          <button type="button" class="ribbon-btn" onclick="execCmd('redo')" title="Redo (Ctrl+Y)">
            <i data-lucide="redo-2"></i>
          </button>
        </div>

        <div class="ribbon-separator"></div>

        <!-- Group 2: Paragraph & Typography Styles -->
        <div class="ribbon-group">
          <select class="ribbon-select" id="ribbonStyleSelect" onchange="applyBlockStyle(this.value)" title="Text Style">
            <option value="p">Normal Paragraph</option>
            <option value="h1">Heading 1 (Title)</option>
            <option value="h2">Heading 2 (Section)</option>
            <option value="h3">Heading 3 (Subheading)</option>
            <option value="blockquote">Blockquote Callout</option>
            <option value="pre">Code Block</option>
          </select>

          <select class="ribbon-select" onchange="applyFontFamily(this.value)" title="Font Family">
            <option value="Inter, sans-serif">Modern Sans (Inter)</option>
            <option value="'Outfit', sans-serif">Outfit Display</option>
            <option value="'JetBrains Mono', monospace">Mono Code</option>
            <option value="Georgia, serif">Editorial Serif</option>
          </select>
        </div>

        <div class="ribbon-separator"></div>

        <!-- Group 3: Formatting (B, I, U, S) -->
        <div class="ribbon-group">
          <button type="button" class="ribbon-btn" onclick="execCmd('bold')" title="Bold (Ctrl+B)">
            <i data-lucide="bold"></i>
          </button>
          <button type="button" class="ribbon-btn" onclick="execCmd('italic')" title="Italic (Ctrl+I)">
            <i data-lucide="italic"></i>
          </button>
          <button type="button" class="ribbon-btn" onclick="execCmd('underline')" title="Underline (Ctrl+U)">
            <i data-lucide="underline"></i>
          </button>
          <button type="button" class="ribbon-btn" onclick="execCmd('strikeThrough')" title="Strikethrough">
            <i data-lucide="strikethrough"></i>
          </button>
        </div>

        <div class="ribbon-separator"></div>

        <!-- Group 4: Text Color & Highlight Marker -->
        <div class="ribbon-group">
          <label class="ribbon-btn" title="Text Color" style="cursor: pointer; position: relative;">
            <i data-lucide="palette"></i>
            <input type="color" onchange="execCmd('foreColor', this.value)" style="opacity: 0; position: absolute; inset: 0; width: 100%; height: 100%; cursor: pointer;">
          </label>

          <label class="ribbon-btn" title="Highlight Marker" style="cursor: pointer; position: relative;">
            <i data-lucide="highlighter"></i>
            <input type="color" value="#fbbf24" onchange="execCmd('hiliteColor', this.value)" style="opacity: 0; position: absolute; inset: 0; width: 100%; height: 100%; cursor: pointer;">
          </label>
        </div>

        <div class="ribbon-separator"></div>

        <!-- Group 5: Alignment & Lists -->
        <div class="ribbon-group">
          <button type="button" class="ribbon-btn" onclick="execCmd('justifyLeft')" title="Align Left">
            <i data-lucide="align-left"></i>
          </button>
          <button type="button" class="ribbon-btn" onclick="execCmd('justifyCenter')" title="Align Center">
            <i data-lucide="align-center"></i>
          </button>
          <button type="button" class="ribbon-btn" onclick="execCmd('justifyRight')" title="Align Right">
            <i data-lucide="align-right"></i>
          </button>
          <button type="button" class="ribbon-btn" onclick="execCmd('justifyFull')" title="Justify">
            <i data-lucide="align-justify"></i>
          </button>

          <button type="button" class="ribbon-btn" onclick="execCmd('insertUnorderedList')" title="Bullet List">
            <i data-lucide="list"></i>
          </button>
          <button type="button" class="ribbon-btn" onclick="execCmd('insertOrderedList')" title="Numbered List">
            <i data-lucide="list-ordered"></i>
          </button>
        </div>

        <div class="ribbon-separator"></div>

        <!-- Group 6: Rich Object Inserters (Internal Links, External Links, TOC, Image, Code, Table, Callout) -->
        <div class="ribbon-group">
          <button type="button" class="ribbon-btn" onclick="openInternalLinksModal()" title="Search & Insert Internal Link across TechScout (Articles, Prompts, Tools)">
            <i data-lucide="link-2"></i> <strong>Internal Link</strong>
          </button>
          <button type="button" class="ribbon-btn" onclick="openExternalLinkModal()" title="Insert Custom External Link with SEO Attributes">
            <i data-lucide="external-link"></i> <strong>Ext Link</strong>
          </button>
          <button type="button" class="ribbon-btn" onclick="insertEmbeddedToc()" title="Generate & Insert Table of Contents from Canvas Headings">
            <i data-lucide="list-tree"></i> <strong>TOC 📑</strong>
          </button>
          <button type="button" class="ribbon-btn" onclick="insertImageModal()" title="Insert Image">
            <i data-lucide="image"></i> Image
          </button>
          <button type="button" class="ribbon-btn" onclick="insertCodeBox()" title="Insert Code Snippet">
            <i data-lucide="code"></i> Code
          </button>
          <button type="button" class="ribbon-btn" onclick="insertTable()" title="Insert Data Table">
            <i data-lucide="table"></i> Table
          </button>
          <button type="button" class="ribbon-btn" onclick="insertCallout('tip')" title="Insert Tip Box">
            <i data-lucide="lightbulb"></i> Tip
          </button>
          <button type="button" class="ribbon-btn" onclick="insertCallout('warning')" title="Insert Warning Box">
            <i data-lucide="alert-triangle"></i> Note
          </button>
          <button type="button" class="ribbon-btn" onclick="execCmd('insertHorizontalRule')" title="Divider Line">
            <i data-lucide="minus"></i>
          </button>
        </div>

        <div class="ribbon-separator"></div>

        <!-- Real-Time Metrics HUD (Word Counter, Character Counter, Reading Time) -->
        <div class="ribbon-group" style="margin-left: auto; display: flex; align-items: center; gap: 0.6rem; background: var(--ts-bg-surface-elevated); padding: 0.3rem 0.75rem; border-radius: 8px; border: 1px solid var(--ts-border-subtle);">
          <span style="font-size: 0.78rem; color: #38bdf8; font-weight: 700; display: inline-flex; align-items: center; gap: 0.25rem;">
            <i data-lucide="file-text" style="width: 12px; height: 12px;"></i> <span id="ribbonWordCount">0</span> words
          </span>
          <span style="color: var(--ts-border-subtle);">&bull;</span>
          <span style="font-size: 0.78rem; color: var(--ts-text-secondary); font-weight: 600;">
            <span id="ribbonCharCount">0</span> chars
          </span>
          <span style="color: var(--ts-border-subtle);">&bull;</span>
          <span style="font-size: 0.78rem; color: var(--ts-success); font-weight: 700; display: inline-flex; align-items: center; gap: 0.25rem;">
            <i data-lucide="clock" style="width: 12px; height: 12px;"></i> <span id="ribbonReadTime">~1 min read</span>
          </span>
        </div>

        <div class="ribbon-separator"></div>

        <!-- Group 7: Utility Tools -->
        <div class="ribbon-group">
          <button type="button" class="ribbon-btn" onclick="execCmd('removeFormat')" title="Clear Formatting">
            <i data-lucide="eraser"></i>
          </button>
          <button type="button" class="ribbon-btn" id="toggleCodeViewBtn" onclick="toggleHtmlView()" title="Inspect Raw HTML">
            <i data-lucide="file-code"></i> HTML
          </button>
          <button type="button" class="ribbon-btn" onclick="toggleFullscreen()" title="Fullscreen Mode">
            <i data-lucide="maximize-2"></i>
          </button>
        </div>
      </div>

      <!-- Document Canvas Wrapper -->
      <div class="word-canvas-wrapper" onclick="focusCanvas(event)">
        <!-- Visual Contenteditable Canvas -->
        <div id="wordCanvas" class="word-document-canvas" contenteditable="true" spellcheck="true" data-placeholder="Start typing your technical guide or architecture breakdown here... Use toolbar ribbons above to format visually without writing HTML!">
          <?php if (!empty($content['body_html'])): ?>
            <?= $content['body_html'] ?>
          <?php else: ?>
            <h2>1. Architecture Overview & Problem Statement</h2>
            <p>Write an introductory overview explaining the problem statement, system design constraints, and technological stack decisions.</p>
            
            <?php if ($canTemplates): ?>
              <div class="word-callout word-callout-tip">
                <strong>💡 Pro Tip:</strong> Click the pink <strong>Templates</strong> button above to load complete production blueprints for Articles, Prompts, Custom GPTs, or AI Tools!
              </div>
            <?php endif; ?>

            <h2>2. Implementation Blueprint</h2>
            <p>Detail your step-by-step code implementation and architectural pipeline.</p>
            
            <pre><code>// Example implementation snippet
function executePipeline(Context $ctx): Result {
    return $ctx->benchmark();
}</code></pre>

            <h2>3. Benchmarks & Key Takeaways</h2>
            <p>Summarize latency benchmarks, throughput metrics, and conclusions for fellow builders.</p>
          <?php endif; ?>
        </div>

        <!-- Raw HTML Source Editor (Toggled via HTML button) -->
        <textarea id="rawHtmlEditor" style="display: none; width: 100%; max-width: 860px; min-height: 480px; background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: var(--ts-radius-md); padding: 2rem; color: #38bdf8; font-family: var(--ts-font-mono); font-size: 0.95rem; line-height: 1.6; resize: vertical;"></textarea>
      </div>

      <!-- Hidden Form Input to receive canvas HTML on submit -->
      <textarea name="body_html" id="body_html_hidden" style="display: none;"></textarea>

      <!-- MS Word-Style Document Status Bar -->
      <div class="word-status-bar">
        <div style="display: flex; gap: 1.5rem; align-items: center; flex-wrap: wrap;">
          <div class="word-status-item">
            <i data-lucide="file-text" style="width: 14px; height: 14px;"></i>
            <span>Words: <strong id="statWordCount">0</strong></span>
          </div>
          <div class="word-status-item">
            <span>Characters: <strong id="statCharCount">0</strong></span>
          </div>
          <div class="word-status-item">
            <i data-lucide="clock" style="width: 14px; height: 14px;"></i>
            <span>Read Time: <strong id="statReadTime">~1 min</strong></span>
          </div>
          <div class="word-status-item">
            <i data-lucide="award" style="width: 14px; height: 14px; color: #f59e0b;"></i>
            <span>Estimated Quality Score: <strong id="statQualityScore" style="color: #34d399;">85/100</strong></span>
          </div>
        </div>

        <div style="display: flex; gap: 1rem; align-items: center; font-size: 0.8rem; color: #64748b;">
          <span id="saveStatusIndicator">Autosaved to memory ✓</span>
          <span>100% Visual Canvas</span>
        </div>
      </div>
    </div>
  </form>
</div>

<?php if ($canTemplates): ?>
<!-- ========================================================================= -->
<!-- PRODUCTION BLUEPRINT TEMPLATES MODAL (1000x Enhanced)                    -->
<!-- ========================================================================= -->
<div id="templatesModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 99999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-table-wrap" style="width: 100%; max-width: 960px; max-height: 90vh; overflow-y: auto; padding: 2rem; background: #090d16; border: 1px solid rgba(236,72,153,0.4);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(148,163,184,0.15); padding-bottom: 1rem;">
      <div>
        <h2 style="font-size: 1.4rem; color: #f8fafc; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
          <i data-lucide="layout-template" style="color: #ec4899;"></i> Engineering Blueprint & Specification Templates
        </h2>
        <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Instantly populate your visual canvas with tested architecture blueprints and high-converting formats.</p>
      </div>
      <button onclick="document.getElementById('templatesModal').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer;">&times;</button>
    </div>

    <!-- Template Category Filter Tabs (Gated by Author Capabilities) -->
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
      <button type="button" onclick="filterTemplateCards('all')" id="tpl-tab-all" class="ts-btn ts-btn-sm ts-btn-primary">All Authorized Blueprints</button>
      <button type="button" onclick="filterTemplateCards('article')" id="tpl-tab-article" class="ts-btn ts-btn-sm ts-btn-outline">📝 Articles</button>
      <?php if ($canPrompts): ?>
        <button type="button" onclick="filterTemplateCards('prompt')" id="tpl-tab-prompt" class="ts-btn ts-btn-sm ts-btn-outline">⚡ Prompts</button>
      <?php endif; ?>
      <?php if ($canGpts): ?>
        <button type="button" onclick="filterTemplateCards('gpt')" id="tpl-tab-gpt" class="ts-btn ts-btn-sm ts-btn-outline">🤖 Custom GPTs</button>
      <?php endif; ?>
      <?php if ($canTools): ?>
        <button type="button" onclick="filterTemplateCards('tool')" id="tpl-tab-tool" class="ts-btn ts-btn-sm ts-btn-outline">🛠️ AI Tools</button>
      <?php endif; ?>
      <button type="button" onclick="filterTemplateCards('custom')" id="tpl-tab-custom" class="ts-btn ts-btn-sm ts-btn-outline">💾 My Custom Templates</button>
    </div>

    <!-- Template Cards Grid -->
    <div id="templateCardsGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.25rem;">
      
      <!-- Template 1: Production Architecture Deep Dive -->
      <div class="tpl-card ts-card" data-category="article" style="display: flex; flex-direction: column; background: rgba(15,23,42,0.7); border-color: rgba(99,102,241,0.25);">
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
          <span class="ts-badge ts-badge-cyan">ARTICLE</span>
          <span class="ts-badge ts-badge-pink">POPULAR</span>
        </div>
        <h3 style="font-size: 1.1rem; margin-bottom: 0.4rem; color: #f8fafc;">Production Architecture Deep Dive</h3>
        <p style="font-size: 0.8rem; color: #94a3b8; flex: 1; margin-bottom: 1rem;">Full structural breakdown with topology callouts, latency benchmark tables, and code snippets.</p>
        <button type="button" onclick="loadTemplate('article_deep_dive')" class="ts-btn ts-btn-primary ts-btn-sm" style="width: 100%;">Load Template →</button>
      </div>

      <!-- Template 2: System Scaling & Query Optimization -->
      <div class="tpl-card ts-card" data-category="article" style="display: flex; flex-direction: column; background: rgba(15,23,42,0.7); border-color: rgba(99,102,241,0.25);">
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
          <span class="ts-badge ts-badge-cyan">ARTICLE</span>
          <span class="ts-badge">PERFORMANCE</span>
        </div>
        <h3 style="font-size: 1.1rem; margin-bottom: 0.4rem; color: #f8fafc;">System Scaling & Bottlenecks Blueprint</h3>
        <p style="font-size: 0.8rem; color: #94a3b8; flex: 1; margin-bottom: 1rem;">Root cause profiling, flamegraph explanation, database indexing, and memory allocation fixes.</p>
        <button type="button" onclick="loadTemplate('article_scaling')" class="ts-btn ts-btn-primary ts-btn-sm" style="width: 100%;">Load Template →</button>
      </div>

      <!-- Template 3: XML-Tagged System Prompt -->
      <div class="tpl-card ts-card" data-category="prompt" style="display: flex; flex-direction: column; background: rgba(15,23,42,0.7); border-color: rgba(6,182,212,0.25);">
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
          <span class="ts-badge ts-badge-cyan">PROMPT</span>
          <span class="ts-badge ts-badge-green">XML BOUNDARY</span>
        </div>
        <h3 style="font-size: 1.1rem; margin-bottom: 0.4rem; color: #f8fafc;">XML-Boundary System Architecture Prompt</h3>
        <p style="font-size: 0.8rem; color: #94a3b8; flex: 1; margin-bottom: 1rem;">Strictly bounded system prompt with role definition, explicit rules, few-shot reasoning, and JSON format.</p>
        <button type="button" onclick="loadTemplate('prompt_xml')" class="ts-btn ts-btn-primary ts-btn-sm" style="width: 100%;">Load Template →</button>
      </div>

      <!-- Template 4: SQL & Database Query Optimizer Prompt -->
      <div class="tpl-card ts-card" data-category="prompt" style="display: flex; flex-direction: column; background: rgba(15,23,42,0.7); border-color: rgba(6,182,212,0.25);">
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
          <span class="ts-badge ts-badge-cyan">PROMPT</span>
          <span class="ts-badge">DATABASE</span>
        </div>
        <h3 style="font-size: 1.1rem; margin-bottom: 0.4rem; color: #f8fafc;">SQL DBA Query Tuning Prompt</h3>
        <p style="font-size: 0.8rem; color: #94a3b8; flex: 1; margin-bottom: 1rem;">Prompts that take EXPLAIN PLAN output, buffer pools, and composite indexes to produce optimized queries.</p>
        <button type="button" onclick="loadTemplate('prompt_sql')" class="ts-btn ts-btn-primary ts-btn-sm" style="width: 100%;">Load Template →</button>
      </div>

      <!-- Template 5: Autonomous Code Reviewer GPT -->
      <div class="tpl-card ts-card" data-category="gpt" style="display: flex; flex-direction: column; background: rgba(15,23,42,0.7); border-color: rgba(236,72,153,0.25);">
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
          <span class="ts-badge ts-badge-pink">CUSTOM GPT</span>
          <span class="ts-badge">CODE AUDITOR</span>
        </div>
        <h3 style="font-size: 1.1rem; margin-bottom: 0.4rem; color: #f8fafc;">Autonomous Code Reviewer GPT Blueprint</h3>
        <p style="font-size: 0.8rem; color: #94a3b8; flex: 1; margin-bottom: 1rem;">System persona, safety boundary, static analysis rules, and prompt starters for PR reviews.</p>
        <button type="button" onclick="loadTemplate('gpt_reviewer')" class="ts-btn ts-btn-primary ts-btn-sm" style="width: 100%;">Load Template →</button>
      </div>

      <!-- Template 6: AI Tool Benchmark Teardown -->
      <div class="tpl-card ts-card" data-category="tool" style="display: flex; flex-direction: column; background: rgba(15,23,42,0.7); border-color: rgba(16,185,129,0.25);">
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
          <span class="ts-badge ts-badge-green">AI TOOL</span>
          <span class="ts-badge">EVALUATION</span>
        </div>
        <h3 style="font-size: 1.1rem; margin-bottom: 0.4rem; color: #f8fafc;">Developer Tool Benchmark & Matrix</h3>
        <p style="font-size: 0.8rem; color: #94a3b8; flex: 1; margin-bottom: 1rem;">Feature teardown, pricing tier analysis table, latency benchmarking, and developer verdict.</p>
        <button type="button" onclick="loadTemplate('tool_teardown')" class="ts-btn ts-btn-primary ts-btn-sm" style="width: 100%;">Load Template →</button>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- ========================================================================= -->
<!-- 1. INTERNAL LINKS SEARCH & INSERT MODAL                                  -->
<!-- ========================================================================= -->
<div id="internalLinksModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(6px); z-index: 99999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="ts-card" style="width: 100%; max-width: 620px; background: #090d16; border: 1px solid rgba(99,102,241,0.35); padding: 1.75rem; max-height: 85vh; display: flex; flex-direction: column;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid rgba(148,163,184,0.1); padding-bottom: 0.75rem;">
      <h3 style="margin: 0; font-size: 1.2rem; color: #f8fafc; display: flex; align-items: center; gap: 0.5rem;">
        <i data-lucide="link-2" style="color: #6366f1;"></i> Search & Insert Internal Link
      </h3>
      <button type="button" onclick="document.getElementById('internalLinksModal').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer;">✕</button>
    </div>

    <p style="font-size: 0.82rem; color: #94a3b8; margin: 0 0 1rem 0;">
      Link to other published technical articles, prompt blueprints, AI tools, or GPT listings across TechScout to boost SEO and knowledge graph connectivity.
    </p>

    <!-- Search Input -->
    <div style="position: relative; margin-bottom: 1.25rem;">
      <i data-lucide="search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; color: #64748b;"></i>
      <input type="text" id="internalLinkSearchInput" oninput="debounceInternalSearch(this.value)" placeholder="Search published articles, tools, prompts by title..." style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 8px; padding: 0.65rem 1rem 0.65rem 2.25rem; color: #f8fafc; font-size: 0.9rem; outline: none;">
    </div>

    <!-- Live Search Results List -->
    <div id="internalLinkResults" style="flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 0.6rem; min-height: 180px; max-height: 350px; padding-right: 0.25rem;">
      <div style="text-align: center; padding: 2.5rem 1rem; color: #64748b;">
        <i data-lucide="search" style="width: 32px; height: 32px; margin-bottom: 0.5rem; opacity: 0.5;"></i>
        <div>Type keywords above to search all published platform intelligence.</div>
      </div>
    </div>
  </div>
</div>

<!-- ========================================================================= -->
<!-- 2. EXTERNAL LINK ASSISTANT MODAL (SEO & NOFOLLOW)                        -->
<!-- ========================================================================= -->
<div id="externalLinkModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(6px); z-index: 99999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="ts-card" style="width: 100%; max-width: 520px; background: #090d16; border: 1px solid rgba(56,189,248,0.35); padding: 1.75rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid rgba(148,163,184,0.1); padding-bottom: 0.75rem;">
      <h3 style="margin: 0; font-size: 1.2rem; color: #f8fafc; display: flex; align-items: center; gap: 0.5rem;">
        <i data-lucide="external-link" style="color: #38bdf8;"></i> Insert External Link
      </h3>
      <button type="button" onclick="document.getElementById('externalLinkModal').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer;">✕</button>
    </div>

    <div style="margin-bottom: 1rem;">
      <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.35rem;">Destination URL <span style="color: #ef4444;">*</span></label>
      <input type="url" id="extLinkUrl" placeholder="https://github.com/torvalds/linux" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 6px; padding: 0.6rem 0.8rem; color: #f8fafc; font-size: 0.9rem;">
    </div>

    <div style="margin-bottom: 1.25rem;">
      <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.35rem;">Anchor Display Text (Optional - uses selection if empty)</label>
      <input type="text" id="extLinkText" placeholder="e.g. Linux Kernel Source on GitHub" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.2); border-radius: 6px; padding: 0.6rem 0.8rem; color: #f8fafc; font-size: 0.9rem;">
    </div>

    <!-- Link Attributes -->
    <div style="background: #05070b; border: 1px solid rgba(148,163,184,0.15); border-radius: 6px; padding: 0.85rem; margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 0.45rem;">
      <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.82rem; color: #f8fafc; cursor: pointer;">
        <input type="checkbox" id="extLinkTargetBlank" checked style="accent-color: #38bdf8;"> Open in new tab (`target="_blank" rel="noopener noreferrer"`)
      </label>
      <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.82rem; color: #f8fafc; cursor: pointer;">
        <input type="checkbox" id="extLinkNofollow" style="accent-color: #38bdf8;"> Add `rel="nofollow"` (Search engine crawler instruction)
      </label>
      <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.82rem; color: #f8fafc; cursor: pointer;">
        <input type="checkbox" id="extLinkSponsored" style="accent-color: #38bdf8;"> Add `rel="sponsored"` (Affiliate or sponsor link)
      </label>
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
      <button type="button" onclick="document.getElementById('externalLinkModal').style.display='none'" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
      <button type="button" onclick="insertExternalLinkSubmit()" class="ts-btn ts-btn-primary ts-btn-sm">Insert Link ↗</button>
    </div>
  </div>
</div>

<!-- ========================================================================= -->
<!-- 3. WORD (.DOCX) IMPORT STUDIO MODAL                                      -->
<!-- ========================================================================= -->
<div id="docxImportModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(6px); z-index: 99999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="ts-card" style="width: 100%; max-width: 580px; background: #090d16; border: 1px solid rgba(56,189,248,0.35); padding: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid rgba(148,163,184,0.1); padding-bottom: 0.75rem;">
      <h3 style="margin: 0; font-size: 1.25rem; color: #f8fafc; display: flex; align-items: center; gap: 0.5rem;">
        <i data-lucide="file-up" style="color: #38bdf8;"></i> Microsoft Word (.docx) Importer
      </h3>
      <button type="button" onclick="document.getElementById('docxImportModal').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer;">✕</button>
    </div>

    <p style="font-size: 0.85rem; color: #94a3b8; margin: 0 0 1.25rem 0;">
      Upload your Word article draft (.docx). Our parser will automatically extract Heading 1/2/3 hierarchies, styled lists, tables, embedded graphics, and code blocks into the visual canvas!
    </p>

    <!-- Drop Zone -->
    <div id="docxDropZone" onclick="document.getElementById('docxFileInput').click()" style="border: 2px dashed rgba(56,189,248,0.4); background: rgba(15,23,42,0.6); border-radius: 10px; padding: 2.5rem 1.5rem; text-align: center; cursor: pointer; transition: all 0.2s ease;">
      <i data-lucide="file-text" style="width: 42px; height: 42px; color: #38bdf8; margin-bottom: 0.75rem;"></i>
      <div style="font-weight: 700; color: #f8fafc; font-size: 1rem; margin-bottom: 0.25rem;">Click to select or drag & drop .docx file here</div>
      <div style="font-size: 0.78rem; color: #64748b;">Supports .docx files up to 25MB with embedded images and tables</div>
      <input type="file" id="docxFileInput" accept=".docx" style="display: none;" onchange="handleDocxUpload(this.files[0])">
    </div>

    <!-- Loading Spinner / Status -->
    <div id="docxImportStatus" style="display: none; margin-top: 1.25rem; text-align: center;">
      <div style="color: #38bdf8; font-size: 0.9rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
        <i data-lucide="loader-2" class="spin-animation" style="width: 18px; height: 18px;"></i>
        <span>Parsing document structure & extracting embedded assets...</span>
      </div>
    </div>
  </div>
</div>

<!-- ========================================================================= -->
<!-- JAVASCRIPT VISUAL WYSIWYG CONTROLLER & TEMPLATES ENGINE                   -->
<!-- ========================================================================= -->
<script>
const canvas = document.getElementById('wordCanvas');
const rawHtmlEditor = document.getElementById('rawHtmlEditor');
const hiddenInput = document.getElementById('body_html_hidden');
let isRawHtmlMode = false;

// 1. Template Presets
const TEMPLATE_PRESETS = {
  article_deep_dive: `
    <h2>1. Executive Architecture Summary</h2>
    <p>Provide a high-level overview of the architectural requirements, trade-offs, and throughput goals.</p>
    
    <div class="word-callout word-callout-tip">
      <strong>💡 Architecture Rule:</strong> Always separate high-write streaming ingest from analytical read paths to protect primary replica CPU availability.
    </div>

    <h2>2. System Topology & Request Lifecycle</h2>
    <p>Describe how client requests traverse the edge reverse proxy, application workers, cache layer, and database partitions.</p>

    <pre><code>// 1. Message Ingest Worker
final class StreamIngestWorker {
    public function handle(EventPayload $payload): IngestReceipt {
        // High-throughput async stream commit
        return IngestReceipt::success($payload->id);
    }
}</code></pre>

    <h2>3. Performance Benchmarks & Latency Profiling</h2>
    <table class="word-table">
      <thead>
        <tr>
          <th>Metric / Test Case</th>
          <th>Baseline Setup</th>
          <th>Optimized Pipeline</th>
          <th>Improvement</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>P99 Response Latency</td>
          <td>185 ms</td>
          <td>18 ms</td>
          <td><strong>-90.2% ⚡</strong></td>
        </tr>
        <tr>
          <td>Peak Concurrent Sockets</td>
          <td>10,000</td>
          <td>125,000</td>
          <td><strong>12.5x Capacity</strong></td>
        </tr>
      </tbody>
    </table>

    <h2>4. Failure Modes & Graceful Degradation</h2>
    <p>Detail circuit breaking, retry storms protection, and distributed fallback behaviors.</p>
  `,

  article_scaling: `
    <h2>1. The Scaling Bottleneck & Symptom Analysis</h2>
    <p>Detail the CPU spikes, lock contention, or memory saturation observed during peak load.</p>

    <div class="word-callout word-callout-warning">
      <strong>⚠️ Warning:</strong> Table scans on unindexed foreign keys cause row lock escalations that stall entire worker pools.
    </div>

    <h2>2. Flamegraph Profiling & Root Cause Discovery</h2>
    <p>Analyze profiler data, slow query logs, and thread allocation bottlenecks.</p>

    <h2>3. The Structural Refactoring Solution</h2>
    <pre><code>-- Composite index to eliminate filesort
ALTER TABLE contents ADD INDEX idx_status_published (status, published_at DESC);</code></pre>

    <h2>4. Production Verification & SRE Playbook</h2>
    <p>Verification metrics post-deployment and ongoing observability alerts.</p>
  `,

  prompt_xml: `
    <h2>System Prompt Specification</h2>
    <p>Paste the exact XML-bounded prompt blueprint below. Engineers can copy and inject parameters directly.</p>

    <pre><code>&lt;system_instructions&gt;
  &lt;role&gt;Principal Distributed Systems Architect&lt;/role&gt;
  &lt;context&gt;You analyze backend architectures, SQL queries, and API schemas for sub-millisecond efficiency.&lt;/context&gt;
  
  &lt;strict_rules&gt;
    1. Never output generic advice. Always cite specific MySQL/PostgreSQL index patterns.
    2. Format all code blocks with strict typing and PHP 8.2 / Go / Rust syntax.
    3. Include time complexity (Big-O) and memory allocation trade-offs.
  &lt;/strict_rules&gt;

  &lt;input_schema&gt;
    - Query: {{QUERY}}
    - Schema: {{SCHEMA}}
    - Buffer Pool: {{BUFFER_POOL}}
  &lt;/input_schema&gt;

  &lt;output_format&gt;
    Output a structured JSON payload containing:
    { "optimized_query": string, "required_indexes": array, "estimated_cost_reduction": string }
  &lt;/output_format&gt;
&lt;/system_instructions&gt;</code></pre>
  `,

  prompt_sql: `
    <h2>SQL Query Optimizer & DBA Blueprint</h2>
    <p>This prompt takes an active EXPLAIN PLAN and table DDL to design zero-copy composite indexes.</p>

    <div class="word-callout word-callout-tip">
      <strong>💡 Tested Models:</strong> GPT-4o, Claude 3.5 Sonnet, DeepSeek R1.
    </div>

    <pre><code>You are a MySQL 8.0 / InnoDB Internals DBA Specialist.
Analyze the following slow query and recommend composite indexes respecting the leftmost prefix rule:

EXPLAIN EXTENDED:
{{EXPLAIN_OUTPUT}}

TABLE DDL:
{{TABLE_DDL}}</code></pre>
  `,

  gpt_reviewer: `
    <h2>Agent Persona & Core Capabilities</h2>
    <p>Autonomous senior staff reviewer that enforces zero-vulnerability security checks, architectural hygiene, and test coverage.</p>

    <h2>System Instructions</h2>
    <pre><code>You are the TechScout Lead Code Reviewer Agent.
When reviewing code diffs, evaluate:
1. SQL Injection / OWASP Top 10 vulnerabilities.
2. Memory leaks and unclosed stream handles.
3. Strict type declarations and idempotency.</code></pre>
  `,

  tool_teardown: `
    <h2>1. Tool Executive Verdict</h2>
    <p>A comprehensive engineering review assessing throughput, developer ergonomics, and pricing fairness.</p>

    <h2>2. Feature Comparison Matrix</h2>
    <table class="word-table">
      <thead>
        <tr>
          <th>Capability</th>
          <th>Free Tier</th>
          <th>Pro Tier ($20/mo)</th>
          <th>Enterprise</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Multi-file Context</td>
          <td>Limited</td>
          <td>Full Codebase</td>
          <td>Full Codebase + SSO</td>
        </tr>
        <tr>
          <td>Fine-tuned Models</td>
          <td>No</td>
          <td>Included</td>
          <td>Custom Weights</td>
        </tr>
      </tbody>
    </table>

    <h2>3. Final Engineering Score</h2>
    <p>Verdict: Recommended for production teams building full-stack applications.</p>
  `
};

function onContentTypeChanged(val) {
  document.getElementById('pane_article_spec').style.display = (val === 'article') ? 'block' : 'none';
  document.getElementById('pane_prompt_spec').style.display = (val === 'prompt') ? 'block' : 'none';
  document.getElementById('pane_gpt_spec').style.display = (val === 'gpt_listing') ? 'block' : 'none';
  document.getElementById('pane_tool_spec').style.display = (val === 'ai_tool') ? 'block' : 'none';
}

async function openTemplatesModal() {
  if (!window.CAN_USE_TEMPLATES) {
    if (window.tsModal) {
      await tsModal.alert('Curated blueprint templates require author template permissions granted by an administrator.', 'Template Feature Restricted');
    }
    return;
  }
  const modal = document.getElementById('templatesModal');
  if (modal) modal.style.display = 'flex';
}

function filterTemplateCards(cat) {
  document.querySelectorAll('[id^="tpl-tab-"]').forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });
  const activeBtn = document.getElementById('tpl-tab-' + cat);
  if (activeBtn) {
    activeBtn.classList.remove('ts-btn-outline');
    activeBtn.classList.add('ts-btn-primary');
  }

  const cards = document.querySelectorAll('#templateCardsGrid .tpl-card');
  cards.forEach(c => {
    const cardCat = c.getAttribute('data-category');
    if (cat === 'all' || cardCat === cat) {
      c.style.display = 'flex';
    } else {
      c.style.display = 'none';
    }
  });
}

async function loadTemplate(tplKey) {
  if (TEMPLATE_PRESETS[tplKey]) {
    const confirmed = await tsModal.confirm('Load this template into your visual canvas? Current canvas text will be replaced.', 'Load Template');
    if (confirmed) {
      canvas.innerHTML = TEMPLATE_PRESETS[tplKey].trim();
      syncCanvasToHidden();
      document.getElementById('templatesModal').style.display = 'none';
      showToast('Template loaded into canvas! 🚀', 'success');
      updateStats();
    }
  }
}

async function saveCustomTemplate() {
  if (!window.CAN_USE_TEMPLATES) {
    await tsModal.alert('Saving custom templates requires author template permissions.', 'Permission Required');
    return;
  }
  const title = await tsModal.prompt('Enter a name for this custom template:', 'My Custom Architecture Template', 'Save Custom Template');
  if (!title) return;
  const customTemplates = JSON.parse(localStorage.getItem('ts_custom_templates') || '[]');
  customTemplates.push({
    title: title,
    html: canvas.innerHTML,
    date: new Date().toLocaleDateString()
  });
  localStorage.setItem('ts_custom_templates', JSON.stringify(customTemplates));
  showToast(`Template "${title}" saved to local storage! 💾`, 'success');
}

// 2. Visual WYSIWYG Ribbon Command Helpers
function execCmd(command, value = null) {
  canvas.focus();
  document.execCommand(command, false, value);
  syncCanvasToHidden();
  updateStats();
}

function applyBlockStyle(tag) {
  canvas.focus();
  if (tag === 'pre') {
    insertCodeBox();
  } else {
    document.execCommand('formatBlock', false, '<' + tag + '>');
  }
  syncCanvasToHidden();
  updateStats();
}

function applyFontFamily(font) {
  canvas.focus();
  document.execCommand('fontName', false, font);
  syncCanvasToHidden();
}

async function insertLinkModal() {
  openExternalLinkModal();
}

async function insertImageModal() {
  const url = await tsModal.prompt('Enter image URL (Unsplash or direct image):', 'https://images.unsplash.com/', 'Insert Media Image');
  if (url) {
    const caption = (await tsModal.prompt('Enter image caption / diagram title (Optional):', 'Figure 1: Architectural System Design', 'Image Caption')) || '';
    const figureHtml = `<figure style="margin: 1.5rem 0; text-align: center;"><img src="${url}" alt="${caption}" style="max-width: 100%; border-radius: 8px; border: 1px solid rgba(148,163,184,0.2);"><figcaption style="font-size: 0.85rem; color: #94a3b8; margin-top: 0.5rem; font-style: italic;">${caption}</figcaption></figure><p></p>`;
    document.execCommand('insertHTML', false, figureHtml);
    syncCanvasToHidden();
  }
}

function insertCodeBox() {
  const codeHtml = `<pre><code>// Insert code or benchmark snippet here\nfunction execute(): void {\n    // Code...\n}</code></pre><p></p>`;
  document.execCommand('insertHTML', false, codeHtml);
  syncCanvasToHidden();
  updateStats();
}

function insertTable() {
  const tableHtml = `
    <table class="word-table">
      <thead>
        <tr>
          <th>Architecture Layer</th>
          <th>Technology</th>
          <th>P99 Latency</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Edge Proxy</td>
          <td>NGINX / OpenResty</td>
          <td>1.2 ms</td>
        </tr>
        <tr>
          <td>Application Workers</td>
          <td>PHP 8.2 Swoole</td>
          <td>4.5 ms</td>
        </tr>
        <tr>
          <td>Persistence</td>
          <td>MySQL 8.0 InnoDB</td>
          <td>2.8 ms</td>
        </tr>
      </tbody>
    </table><p></p>
  `;
  document.execCommand('insertHTML', false, tableHtml);
  syncCanvasToHidden();
  updateStats();
}

function insertCallout(type) {
  const isTip = (type === 'tip');
  const icon = isTip ? '💡 Pro Tip:' : '⚠️ Important Note:';
  const calloutHtml = `
    <div class="word-callout word-callout-${type}">
      <strong>${icon}</strong> Write your highlighted insight, rule, or precaution here.
    </div><p></p>
  `;
  document.execCommand('insertHTML', false, calloutHtml);
  syncCanvasToHidden();
}

// ===========================================================================
// 4. ADVANCED INTERNAL LINKS ASSISTANT (Live DB Search)
// ===========================================================================
let internalSearchTimer = null;
let savedEditorSelection = null;

function openInternalLinksModal() {
  saveSelection();
  document.getElementById('internalLinksModal').style.display = 'flex';
  document.getElementById('internalLinkSearchInput').focus();
  searchInternalLinks('');
}

function debounceInternalSearch(q) {
  clearTimeout(internalSearchTimer);
  internalSearchTimer = setTimeout(() => {
    searchInternalLinks(q);
  }, 250);
}

async function searchInternalLinks(query) {
  const container = document.getElementById('internalLinkResults');
  container.innerHTML = `<div style="text-align: center; padding: 2rem; color: #64748b;"><i data-lucide="loader-2" class="spin-animation" style="width: 24px; height: 24px;"></i> Searching...</div>`;
  lucide.createIcons();

  try {
    const res = await fetch(`<?= url('/api/v1/search/internal-links') ?>?q=${encodeURIComponent(query)}`);
    const data = await res.json();
    if (!data.success || !data.results.length) {
      container.innerHTML = `
        <div style="text-align: center; padding: 2rem 1rem; color: #64748b;">
          <i data-lucide="help-circle" style="width: 28px; height: 28px; margin-bottom: 0.5rem; opacity: 0.6;"></i>
          <div>No published items match "${query}". Try different keywords.</div>
        </div>
      `;
      lucide.createIcons();
      return;
    }

    let html = '';
    data.results.forEach(item => {
      const typeBadgeClass = item.type === 'article' ? 'ts-badge-cyan' : (item.type === 'prompt' ? 'ts-badge-pink' : 'ts-badge-green');
      html += `
        <div onclick="insertInternalLink('${item.url}', '${escapeHtml(item.title)}', '${item.type}')" style="background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.15); border-radius: 6px; padding: 0.75rem 1rem; cursor: pointer; transition: all 0.15s ease; display: flex; justify-content: space-between; align-items: center;" onmouseover="this.style.borderColor='#6366f1'; this.style.background='rgba(99,102,241,0.08)';" onmouseout="this.style.borderColor='rgba(148,163,184,0.15)'; this.style.background='rgba(15,23,42,0.6)';">
          <div>
            <div style="display: flex; gap: 0.4rem; align-items: center; margin-bottom: 0.25rem;">
              <span class="ts-badge ${typeBadgeClass}" style="font-size: 0.65rem; padding: 0.1rem 0.35rem;">${item.type.toUpperCase()}</span>
              <strong style="color: #f8fafc; font-size: 0.9rem;">${escapeHtml(item.title)}</strong>
            </div>
            <div style="font-size: 0.75rem; color: #64748b;">${item.url}</div>
          </div>
          <button type="button" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.65rem;">
            Insert Link 🔗
          </button>
        </div>
      `;
    });
    container.innerHTML = html;
    lucide.createIcons();
  } catch (err) {
    container.innerHTML = `<div style="color: #ef4444; text-align: center; padding: 1.5rem;">Failed to search internal records.</div>`;
  }
}

function insertInternalLink(targetUrl, title, type) {
  document.getElementById('internalLinksModal').style.display = 'none';
  restoreSelection();
  canvas.focus();

  const selectedText = window.getSelection().toString().trim();
  const anchorText = selectedText ? selectedText : title;
  const linkHtml = `<a href="${targetUrl}" class="ts-internal-link" data-internal="true" title="${title}">${anchorText}</a>`;

  document.execCommand('insertHTML', false, linkHtml);
  syncCanvasToHidden();
  updateStats();
  showToast(`Internal link to "${title}" inserted! 🔗`, 'success');
}

// ===========================================================================
// 5. EXTERNAL LINK ASSISTANT (SEO Nofollow & Sponsored)
// ===========================================================================
function openExternalLinkModal() {
  saveSelection();
  const selectedText = window.getSelection().toString().trim();
  document.getElementById('extLinkText').value = selectedText;
  document.getElementById('extLinkUrl').value = '';
  document.getElementById('externalLinkModal').style.display = 'flex';
  document.getElementById('extLinkUrl').focus();
}

function insertExternalLinkSubmit() {
  const url = document.getElementById('extLinkUrl').value.trim();
  if (!url) {
    showToast('Please enter a valid destination URL.', 'warning');
    document.getElementById('extLinkUrl').focus();
    return;
  }

  let text = document.getElementById('extLinkText').value.trim();
  if (!text) text = url;

  const targetBlank = document.getElementById('extLinkTargetBlank').checked;
  const nofollow = document.getElementById('extLinkNofollow').checked;
  const sponsored = document.getElementById('extLinkSponsored').checked;

  let relAttrs = [];
  if (targetBlank) relAttrs.push('noopener', 'noreferrer');
  if (nofollow) relAttrs.push('nofollow');
  if (sponsored) relAttrs.push('sponsored');

  const relStr = relAttrs.length > 0 ? ` rel="${relAttrs.join(' ')}"` : '';
  const targetStr = targetBlank ? ' target="_blank"' : '';

  const linkHtml = `<a href="${url}"${targetStr}${relStr} class="ts-external-link">${escapeHtml(text)}</a>`;

  document.getElementById('externalLinkModal').style.display = 'none';
  restoreSelection();
  canvas.focus();
  document.execCommand('insertHTML', false, linkHtml);
  syncCanvasToHidden();
  updateStats();
  showToast('External link inserted! ↗', 'success');
}

// ===========================================================================
// 6. DYNAMIC TABLE OF CONTENTS (TOC) GENERATOR
// ===========================================================================
function insertEmbeddedToc() {
  canvas.focus();
  const headings = canvas.querySelectorAll('h2, h3');
  if (!headings.length) {
    showToast('No <h2> or <h3> headings found in document to generate TOC. Add section headings first!', 'warning');
    return;
  }

  // Remove existing TOC if present
  const existingToc = canvas.querySelector('.article-toc-box');
  if (existingToc) existingToc.remove();

  let tocListHtml = '';
  headings.forEach((h, index) => {
    const text = h.innerText.trim();
    if (!text) return;
    const slug = text.toLowerCase().replace(/[^\w\s-]/g, '').replace(/[\s_-]+/g, '-').replace(/^-+|-+$/g, '') || `section-${index+1}`;
    h.id = slug;

    const isH3 = (h.tagName.toLowerCase() === 'h3');
    const indent = isH3 ? 'margin-left: 1rem; font-size: 0.76rem; color: #94a3b8;' : 'font-weight: 600; font-size: 0.82rem; color: #f8fafc;';
    const bullet = isH3 ? '↳ ' : '• ';

    tocListHtml += `
      <li style="margin-bottom: 0.25rem; line-height: 1.35; ${indent}">
        <a href="#${slug}" style="color: inherit; text-decoration: none;">${bullet}${escapeHtml(text)}</a>
      </li>
    `;
  });

  const tocCardHtml = `
    <nav class="article-toc-box" style="background: rgba(15,23,42,0.75); border: 1px solid rgba(99,102,241,0.25); border-radius: 8px; padding: 0.85rem 1.15rem; margin: 1.25rem 0 1.5rem 0; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
      <div style="font-weight: 700; color: #f8fafc; font-size: 0.85rem; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.45rem;">
        <span>📑 Table of Contents</span>
      </div>
      <ul style="list-style: none; padding: 0; margin: 0;">
        ${tocListHtml}
      </ul>
    </nav><p></p>
  `;

  // Insert TOC at top or at cursor
  document.execCommand('insertHTML', false, tocCardHtml);
  syncCanvasToHidden();
  updateStats();
  showToast(`Table of Contents generated with ${headings.length} sections! 📑`, 'success');
}

// Selection helpers
function saveSelection() {
  if (window.getSelection) {
    const sel = window.getSelection();
    if (sel.getRangeAt && sel.rangeCount) {
      savedEditorSelection = sel.getRangeAt(0);
    }
  }
}

function restoreSelection() {
  if (savedEditorSelection && window.getSelection) {
    const sel = window.getSelection();
    sel.removeAllRanges();
    sel.addRange(savedEditorSelection);
  }
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.innerText = text;
  return div.innerHTML;
}

// ===========================================================================
// 7. MICROSOFT WORD (.DOCX) IMPORT & DRAG-AND-DROP PARSER
// ===========================================================================
function openDocxModal() {
  document.getElementById('docxImportModal').style.display = 'flex';
  document.getElementById('docxImportStatus').style.display = 'none';
}

async function handleDocxUpload(file) {
  if (!file) return;
  if (!file.name.toLowerCase().endsWith('.docx')) {
    showToast('Please upload a Microsoft Word .docx file.', 'error');
    return;
  }

  const statusEl = document.getElementById('docxImportStatus');
  statusEl.style.display = 'block';

  const formData = new FormData();
  formData.append('file', file);

  try {
    const res = await fetch('<?= url('/api/v1/author/import-docx') ?>', {
      method: 'POST',
      body: formData
    });
    const data = await res.json();
    statusEl.style.display = 'none';

    if (!data.success) {
      showToast(data.error || 'Failed to parse .docx document.', 'error');
      return;
    }

    // Populate editor fields
    if (data.title && (!document.getElementById('doc_title').value.trim() || confirm(`Apply imported document title: "${data.title}"?`))) {
      document.getElementById('doc_title').value = data.title;
    }

    if (data.summary && !document.getElementById('doc_summary').value.trim()) {
      document.getElementById('doc_summary').value = data.summary;
    }

    if (data.body_html) {
      canvas.innerHTML = data.body_html;
      syncCanvasToHidden();
      updateStats();
    }

    document.getElementById('docxImportModal').style.display = 'none';
    showToast(`Successfully imported "${data.filename}" (${data.word_count} words)! 🎉`, 'success');
  } catch (err) {
    statusEl.style.display = 'none';
    showToast('Server error while parsing Word document.', 'error');
  }
}

// Drag and drop support on visual canvas and modal dropzone
const wordDropZone = document.getElementById('docxDropZone');
['dragenter', 'dragover'].forEach(eventName => {
  wordDropZone.addEventListener(eventName, e => { e.preventDefault(); wordDropZone.style.borderColor = '#38bdf8'; wordDropZone.style.background = 'rgba(56,189,248,0.1)'; }, false);
  canvas.addEventListener(eventName, e => { e.preventDefault(); canvas.style.outline = '2px dashed #38bdf8'; }, false);
});

['dragleave', 'drop'].forEach(eventName => {
  wordDropZone.addEventListener(eventName, e => { e.preventDefault(); wordDropZone.style.borderColor = 'rgba(56,189,248,0.4)'; wordDropZone.style.background = 'rgba(15,23,42,0.6)'; }, false);
  canvas.addEventListener(eventName, e => { e.preventDefault(); canvas.style.outline = 'none'; }, false);
});

wordDropZone.addEventListener('drop', e => {
  const dt = e.dataTransfer;
  const files = dt.files;
  if (files.length) handleDocxUpload(files[0]);
});

canvas.addEventListener('drop', e => {
  const dt = e.dataTransfer;
  const files = dt.files;
  if (files.length && files[0].name.toLowerCase().endsWith('.docx')) {
    e.preventDefault();
    if (confirm(`Import Word document "${files[0].name}" into canvas?`)) {
      handleDocxUpload(files[0]);
    }
  }
});

// 8. Raw HTML Mode Toggle
function toggleHtmlView() {
  isRawHtmlMode = !isRawHtmlMode;
  const toggleBtn = document.getElementById('toggleCodeViewBtn');

  if (isRawHtmlMode) {
    rawHtmlEditor.value = canvas.innerHTML;
    canvas.style.display = 'none';
    rawHtmlEditor.style.display = 'block';
    toggleBtn.classList.add('active');
    toggleBtn.style.color = '#38bdf8';
  } else {
    canvas.innerHTML = rawHtmlEditor.value;
    rawHtmlEditor.style.display = 'none';
    canvas.style.display = 'block';
    toggleBtn.classList.remove('active');
    toggleBtn.style.color = '';
    syncCanvasToHidden();
    updateStats();
  }
}

function toggleFullscreen() {
  const container = document.getElementById('wordEditorContainer');
  container.classList.toggle('word-fullscreen');
}

function focusCanvas(e) {
  if (e.target === e.currentTarget && !isRawHtmlMode) {
    canvas.focus();
  }
}

function syncCanvasToHidden() {
  if (isRawHtmlMode) {
    hiddenInput.value = rawHtmlEditor.value;
  } else {
    hiddenInput.value = canvas.innerHTML;
  }
}

function updateStats() {
  const text = isRawHtmlMode ? rawHtmlEditor.value.replace(/<[^>]*>?/gm, '') : (canvas.innerText || '');
  const words = text.trim() ? text.trim().split(/\s+/).length : 0;
  const chars = text.length;
  const readMins = Math.max(1, Math.ceil(words / 200));

  const wordCountEl = document.getElementById('statWordCount');
  const charCountEl = document.getElementById('statCharCount');
  const readTimeEl = document.getElementById('statReadTime');
  if (wordCountEl) wordCountEl.innerText = words.toLocaleString();
  if (charCountEl) charCountEl.innerText = chars.toLocaleString();
  if (readTimeEl) readTimeEl.innerText = `~${readMins} min`;

  // Top ribbon counters
  const ribbonWords = document.getElementById('ribbonWordCount');
  const ribbonChars = document.getElementById('ribbonCharCount');
  const ribbonTime = document.getElementById('ribbonReadTime');
  if (ribbonWords) ribbonWords.innerText = words.toLocaleString();
  if (ribbonChars) ribbonChars.innerText = chars.toLocaleString();
  if (ribbonTime) ribbonTime.innerText = `~${readMins} min read`;

  // Quality score calculation
  let score = 50;
  if (words > 300) score += 15;
  if (words > 600) score += 15;
  if (canvas.innerHTML.includes('<pre') || canvas.innerHTML.includes('<code')) score += 10;
  if (canvas.innerHTML.includes('<table')) score += 5;
  if (canvas.innerHTML.includes('word-callout')) score += 5;
  score = Math.min(100, score);

  const scoreEl = document.getElementById('statQualityScore');
  if (scoreEl) {
    scoreEl.innerText = `${score}/100`;
    scoreEl.style.color = score >= 80 ? '#34d399' : (score >= 60 ? '#f59e0b' : '#ef4444');
  }
}

function submitEditorForm(action) {
  syncCanvasToHidden();
  document.getElementById('form_action_input').value = action;
  const title = document.getElementById('doc_title').value.trim();
  if (!title) {
    showToast('Please specify a title before saving.', 'warning');
    document.getElementById('doc_title').focus();
    return;
  }
  document.getElementById('authorStudioForm').submit();
}

// 9. Listeners & Initialization
canvas.addEventListener('input', () => {
  syncCanvasToHidden();
  updateStats();
});

rawHtmlEditor.addEventListener('input', () => {
  syncCanvasToHidden();
  updateStats();
});

async function uploadFeaturedImage(input) {
  if (!input.files || !input.files[0]) return;
  const file = input.files[0];
  const formData = new FormData();
  formData.append('image', file);

  showToast('Uploading cover image to server...', 'info');
  try {
    const res = await fetch('<?= url('/api/v1/upload/image') ?>', {
      method: 'POST',
      body: formData
    });
    const data = await res.json();
    if (data.success && data.url) {
      document.getElementById('doc_cover').value = data.url;
      showToast('Cover image uploaded successfully! 📸', 'success');
    } else {
      showToast(data.error || 'Failed to upload image.', 'error');
    }
  } catch (err) {
    showToast('Image upload failed. Check connection.', 'error');
  }
}

document.addEventListener('DOMContentLoaded', () => {
  syncCanvasToHidden();
  updateStats();
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
