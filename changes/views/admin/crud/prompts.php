<?php require __DIR__ . '/../../layouts/admin_header.php'; ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <h1 style="font-size: 1.75rem; color: var(--admin-text-primary); margin: 0 0 0.25rem 0;">Prompt Blueprints Management</h1>
    <p style="color: var(--admin-text-secondary); font-size: 0.9rem; margin: 0;">Create, edit, organize text/coding system prompts and AI image generation blueprints.</p>
  </div>

  <button onclick="openNewPromptModal()" class="ts-btn ts-btn-primary">
    <i data-lucide="plus-circle"></i> Create Prompt Blueprint
  </button>
</div>

<!-- Prompt Modal -->
<div id="promptFormModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-table-wrap" style="width: 100%; max-width: 880px; max-height: 90vh; overflow-y: auto; padding: 2rem; background: var(--admin-card-bg, #090d16); border: 1px solid var(--admin-border-color, rgba(16,185,129,0.3)); border-radius: 14px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding-bottom: 1rem;">
      <div style="display: flex; align-items: center; gap: 0.6rem;">
        <i data-lucide="terminal" style="width: 22px; height: 22px; color: var(--ts-primary, #38bdf8);"></i>
        <h2 id="modalPromptTitle" style="font-size: 1.35rem; color: var(--admin-text-primary); margin: 0;">Create / Edit Prompt Template</h2>
      </div>
      <button onclick="document.getElementById('promptFormModal').style.display='none'" style="background: none; border: none; color: var(--admin-text-secondary); font-size: 1.5rem; cursor: pointer;">&times;</button>
    </div>

    <form id="adminPromptForm" action="<?= url('/admin/prompts/save') ?>" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" id="prompt_id" value="">

      <!-- Prompt Type Segmented Selector -->
      <div style="margin-bottom: 1.5rem; background: var(--admin-card-bg-subtle, rgba(255,255,255,0.03)); padding: 1.15rem; border-radius: 10px; border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15));">
        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--admin-text-primary); margin-bottom: 0.6rem;">
          Select Prompt Type * (Strictly Segregates Text Prompts from Image Generation Prompts)
        </label>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
          <label id="type_opt_text" onclick="setPromptType('text')" style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.85rem; border-radius: 8px; border: 2px solid #38bdf8; background: rgba(56,189,248,0.06); cursor: pointer; transition: all 0.2s ease;">
            <input type="radio" name="prompt_type" id="prompt_type_text" value="text" checked style="margin-top: 0.2rem;">
            <div>
              <strong style="color: var(--admin-text-primary); font-size: 0.95rem; display: block;">📝 Text / System / Code Prompt</strong>
              <span style="font-size: 0.78rem; color: var(--admin-text-secondary); line-height: 1.4; display: block; margin-top: 0.2rem;">
                LLM system personas, reasoning chains, SQL tuning, Dockerfiles. No image attached.
              </span>
            </div>
          </label>

          <label id="type_opt_image" onclick="setPromptType('image')" style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.85rem; border-radius: 8px; border: 1px solid var(--admin-border-color, rgba(148,163,184,0.2)); background: transparent; cursor: pointer; transition: all 0.2s ease;">
            <input type="radio" name="prompt_type" id="prompt_type_image" value="image" style="margin-top: 0.2rem;">
            <div>
              <strong style="color: var(--admin-text-primary); font-size: 0.95rem; display: block;">🎨 AI Image Generation Prompt</strong>
              <span style="font-size: 0.78rem; color: var(--admin-text-secondary); line-height: 1.4; display: block; margin-top: 0.2rem;">
                Midjourney v6, Flux.1, DALL-E 3, SDXL. Supports showcase preview image upload.
              </span>
            </div>
          </label>
        </div>
      </div>

      <!-- Title & Category Row -->
      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary); margin-bottom: 0.4rem;">Prompt Title *</label>
          <input type="text" name="title" id="prompt_title" placeholder="e.g. Production MySQL Schema & Index Optimizer" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.75rem 1rem; border-radius: 6px; color: var(--admin-text-primary); font-size: 0.95rem;" required>
        </div>

        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary); margin-bottom: 0.4rem;">Category *</label>
          <select name="category_id" id="prompt_category_id" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.75rem 1rem; border-radius: 6px; color: var(--admin-text-primary);">
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <!-- Conditional Image Upload Zone: Strictly for Image Generation Prompts -->
      <div id="imageUploadContainer" style="display: none; margin-bottom: 1.5rem; background: rgba(236,72,153,0.04); border: 1px dashed rgba(236,72,153,0.3); border-radius: 10px; padding: 1.25rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.6rem;">
          <div style="display: flex; align-items: center; gap: 0.5rem;">
            <i data-lucide="image" style="width: 16px; height: 16px; color: #ec4899;"></i>
            <span style="font-weight: 700; color: var(--admin-text-primary); font-size: 0.88rem;">Visual Generation Showcase Image</span>
          </div>
          <span class="ts-badge ts-badge-pink" style="font-size: 0.7rem;">Image Prompts Only</span>
        </div>
        <p style="font-size: 0.78rem; color: var(--admin-text-secondary); margin: 0 0 1rem 0;">
          Upload a high-fidelity output render sample demonstrating this prompt in action (PNG, JPG, WEBP, GIF up to 10MB).
        </p>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; align-items: center;">
          <div>
            <label class="ts-btn ts-btn-outline" style="cursor: pointer; width: 100%; text-align: center; padding: 0.75rem; border-color: rgba(236,72,153,0.4); color: #ec4899;">
              <i data-lucide="upload-cloud" style="width: 16px; height: 16px; display: inline; margin-right: 0.35rem;"></i>
              <span id="uploadButtonText">Choose Showcase Image File</span>
              <input type="file" name="image_file" id="prompt_image_file" accept="image/png,image/jpeg,image/webp,image/gif" style="display: none;" onchange="previewSelectedPromptImage(this)">
            </label>
            <div style="margin-top: 0.65rem;">
              <label style="display: block; font-size: 0.75rem; color: var(--admin-text-secondary); margin-bottom: 0.25rem;">Or Direct Image URL / Storage Path:</label>
              <input type="text" name="featured_image_url" id="prompt_featured_image_url" placeholder="e.g. /storage/uploads/prompts/... or https://..." style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.5rem 0.75rem; border-radius: 6px; color: var(--admin-text-primary); font-size: 0.82rem;" oninput="previewUrlPromptImage(this.value)">
            </div>
          </div>

          <!-- Live Image Preview Card -->
          <div id="promptImagePreviewBox" style="height: 140px; border-radius: 8px; border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); background: var(--admin-input-bg, #05070b); display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;">
            <img id="promptImagePreviewImg" src="" alt="Prompt Visual" style="display: none; width: 100%; height: 100%; object-fit: cover;">
            <div id="promptImagePreviewPlaceholder" style="text-align: center; color: var(--admin-text-secondary); font-size: 0.8rem;">
              <i data-lucide="image" style="width: 28px; height: 28px; margin-bottom: 0.3rem; opacity: 0.5;"></i>
              <div>No Image Uploaded</div>
            </div>
            <button type="button" id="removeImageBtn" onclick="clearPromptImagePreview()" style="display: none; position: absolute; top: 6px; right: 6px; background: rgba(239,68,68,0.85); color: #fff; border: none; border-radius: 4px; padding: 0.2rem 0.45rem; font-size: 0.7rem; cursor: pointer;">Remove</button>
          </div>
        </div>
      </div>

      <!-- Text Prompt Notice: Explains no image is attached -->
      <div id="textPromptNotice" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.25rem; background: rgba(56,189,248,0.05); border: 1px solid rgba(56,189,248,0.2); border-radius: 8px; padding: 0.65rem 1rem;">
        <i data-lucide="info" style="width: 15px; height: 15px; color: #38bdf8; flex-shrink: 0;"></i>
        <span style="font-size: 0.78rem; color: var(--admin-text-secondary);">
          Text prompts have <strong>no image attached</strong>. They render pure code blueprint cards with syntax variables and 1-click copy across the library.
        </span>
      </div>

      <!-- Summary -->
      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary); margin-bottom: 0.4rem;">Summary *</label>
        <textarea name="summary" id="prompt_summary" rows="2" placeholder="Concise overview of what this prompt accomplishes..." style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.75rem 1rem; border-radius: 6px; color: var(--admin-text-primary); font-size: 0.9rem;" required></textarea>
      </div>

      <!-- Target Models & Quick Selector -->
      <div style="margin-bottom: 1.25rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
          <label style="font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary);">Target Models JSON Array</label>
          <span style="font-size: 0.72rem; color: var(--admin-text-secondary);">Click model pills below to toggle</span>
        </div>
        <input type="text" name="target_models" id="prompt_models" value='["ChatGPT 4o", "Claude 3.5 Sonnet"]' style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.65rem 1rem; border-radius: 6px; color: #38bdf8; font-family: var(--ts-font-mono); font-size: 0.85rem; margin-bottom: 0.5rem;">
        
        <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
          <button type="button" onclick="toggleModelTag('ChatGPT 4o')" class="ts-badge" style="cursor: pointer;">+ ChatGPT 4o</button>
          <button type="button" onclick="toggleModelTag('Claude 3.5 Sonnet')" class="ts-badge" style="cursor: pointer;">+ Claude 3.5 Sonnet</button>
          <button type="button" onclick="toggleModelTag('DeepSeek R1')" class="ts-badge" style="cursor: pointer;">+ DeepSeek R1</button>
          <button type="button" onclick="toggleModelTag('Midjourney v6')" class="ts-badge ts-badge-pink" style="cursor: pointer;">+ Midjourney v6</button>
          <button type="button" onclick="toggleModelTag('Flux.1 Dev')" class="ts-badge ts-badge-pink" style="cursor: pointer;">+ Flux.1 Dev</button>
          <button type="button" onclick="toggleModelTag('DALL-E 3')" class="ts-badge ts-badge-pink" style="cursor: pointer;">+ DALL-E 3</button>
          <button type="button" onclick="toggleModelTag('Stable Diffusion XL')" class="ts-badge ts-badge-pink" style="cursor: pointer;">+ SDXL</button>
        </div>
      </div>

      <!-- Prompt Template -->
      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary); margin-bottom: 0.4rem;">
          Prompt Blueprint Template (Use {{variable}} tags) *
        </label>
        <textarea name="prompt_template" id="prompt_template_body" rows="7" placeholder="Act as a {{role}}. Optimize the following {{target_entity}} according to..." style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.75rem 1rem; border-radius: 6px; color: var(--admin-text-primary); font-family: var(--ts-font-mono); font-size: 0.88rem;" required></textarea>
      </div>

      <!-- Variables Schema & Sample Output Grid -->
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary); margin-bottom: 0.4rem;">Template Variables JSON Schema</label>
          <textarea name="template_variables" id="prompt_variables" rows="3" style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.65rem 0.85rem; border-radius: 6px; color: var(--admin-text-primary); font-family: var(--ts-font-mono); font-size: 0.82rem;">[{"name":"framework","label":"Target Framework","default":"PHP/MySQL"}]</textarea>
        </div>

        <div>
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-primary); margin-bottom: 0.4rem;">Sample Output / Response (Optional)</label>
          <textarea name="sample_output" id="prompt_sample_output" rows="3" placeholder="Optional verified model response snippet..." style="width: 100%; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding: 0.65rem 0.85rem; border-radius: 6px; color: var(--admin-text-primary); font-family: var(--ts-font-mono); font-size: 0.82rem;"></textarea>
        </div>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--admin-border-color, rgba(148,163,184,0.15)); padding-top: 1.25rem;">
        <button type="button" onclick="document.getElementById('promptFormModal').style.display='none'" class="ts-btn ts-btn-outline">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary">Save Prompt Blueprint ⚡</button>
      </div>
    </form>
  </div>
</div>

<!-- Prompts Table Header & Search -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
  <div style="font-weight: 700; color: var(--admin-text-primary); font-size: 1.1rem; display: flex; align-items: center; gap: 0.5rem;">
    <i data-lucide="terminal" style="width: 18px; height: 18px; color: #f59e0b;"></i> Blueprints Index (<?= count($prompts) ?> Templates)
  </div>
  <input type="text" id="promptSearchInput" placeholder="🔍 Search prompts, models, tags..." class="ts-input" style="padding: 0.45rem 0.85rem; font-size: 0.84rem; width: 280px; background: var(--admin-input-bg, #05070b); border: 1px solid var(--admin-border-color, rgba(148,163,184,0.25)); border-radius: 8px; color: var(--admin-text-primary);">
</div>

<div class="admin-table-wrap">
  <table class="admin-table" id="promptsCrudTable" data-paginate="true" data-search-id="promptSearchInput" data-page-size="10">
    <thead>
      <tr>
        <th style="width: 60px;">Visual</th>
        <th>Prompt Name &amp; Summary</th>
        <th>Type</th>
        <th>Category</th>
        <th>Target Models</th>
        <th>Copies</th>
        <th style="text-align: right;">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($prompts as $p): ?>
        <?php
          $isImage = (($p['prompt_type'] ?? '') === 'image');
          $imgUrl = !empty($p['featured_image_url']) ? image_url($p['featured_image_url']) : '';
        ?>
        <tr>
          <td>
            <?php if ($isImage && !empty($imgUrl)): ?>
              <div style="width: 44px; height: 44px; border-radius: 6px; overflow: hidden; border: 1px solid rgba(236,72,153,0.3); background: #000;">
                <img src="<?= htmlspecialchars($imgUrl) ?>" alt="Thumb" style="width: 100%; height: 100%; object-fit: cover;">
              </div>
            <?php else: ?>
              <div style="width: 44px; height: 44px; border-radius: 6px; background: rgba(56,189,248,0.08); border: 1px solid rgba(56,189,248,0.2); display: flex; align-items: center; justify-content: center; color: #38bdf8;">
                <i data-lucide="terminal" style="width: 20px; height: 20px;"></i>
              </div>
            <?php endif; ?>
          </td>
          <td>
            <div style="font-weight: 600; color: var(--admin-text-primary); font-size: 0.95rem;">
              <a href="<?= url('/prompts/' . $p['slug']) ?>" target="_blank" style="color: var(--admin-text-primary); text-decoration: none;">
                <?= htmlspecialchars($p['title']) ?> ↗
              </a>
            </div>
            <div style="font-size: 0.75rem; color: var(--admin-text-secondary); margin-top: 0.2rem;">
              <?= htmlspecialchars(substr($p['summary'], 0, 75)) ?>...
            </div>
          </td>
          <td>
            <span class="ts-badge <?= $isImage ? 'ts-badge-pink' : 'ts-badge-cyan' ?>" style="font-size: 0.72rem;">
              <?= $isImage ? '🎨 Image' : '📝 Text' ?>
            </span>
          </td>
          <td><span class="ts-badge"><?= htmlspecialchars($p['category_name'] ?? 'Prompt') ?></span></td>
          <td>
            <span style="font-family: var(--ts-font-mono); font-size: 0.8rem; color: #38bdf8;"><?= htmlspecialchars($p['target_models'] ?? '["All"]') ?></span>
          </td>
          <td><strong style="color: #34d399;"><?= number_format($p['copy_count'] ?? 0) ?></strong></td>
          <td style="text-align: right;">
            <div style="display: inline-flex; gap: 0.35rem; flex-wrap: wrap; justify-content: flex-end;">
              <a href="<?= url('/admin/analytics/content/' . $p['id']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; color: #38bdf8; border-color: rgba(56,189,248,0.3);" title="View Telemetry">
                📊 Stats
              </a>
              <button onclick='editPrompt(<?= json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                Edit
              </button>
              <form action="<?= url('/admin/prompts/delete/' . $p['id']) ?>" method="POST" onsubmit="return confirm('Delete this prompt blueprint?');" style="margin: 0;">
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
function setPromptType(type) {
  const isImage = (type === 'image');
  document.getElementById('prompt_type_text').checked = !isImage;
  document.getElementById('prompt_type_image').checked = isImage;

  const optText = document.getElementById('type_opt_text');
  const optImage = document.getElementById('type_opt_image');
  const uploadContainer = document.getElementById('imageUploadContainer');
  const textNotice = document.getElementById('textPromptNotice');

  if (isImage) {
    optImage.style.borderColor = '#ec4899';
    optImage.style.background = 'rgba(236,72,153,0.06)';
    optText.style.borderColor = 'var(--admin-border-color, rgba(148,163,184,0.2))';
    optText.style.background = 'transparent';
    uploadContainer.style.display = 'block';
    textNotice.style.display = 'none';
  } else {
    optText.style.borderColor = '#38bdf8';
    optText.style.background = 'rgba(56,189,248,0.06)';
    optImage.style.borderColor = 'var(--admin-border-color, rgba(148,163,184,0.2))';
    optImage.style.background = 'transparent';
    uploadContainer.style.display = 'none';
    textNotice.style.display = 'flex';
  }
}

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

function openNewPromptModal() {
  document.getElementById('adminPromptForm').reset();
  document.getElementById('prompt_id').value = '';
  clearPromptImagePreview();
  setPromptType('text');
  document.getElementById('prompt_category_id').value = document.getElementById('prompt_category_id').options[0]?.value || '1';
  document.getElementById('prompt_models').value = '["ChatGPT 4o", "Claude 3.5 Sonnet"]';
  document.getElementById('prompt_variables').value = '[{"name":"framework","label":"Target Framework","default":"PHP/MySQL"}]';
  document.getElementById('modalPromptTitle').innerText = 'Create New Prompt Blueprint';
  document.getElementById('promptFormModal').style.display = 'flex';
}

function editPrompt(p) {
  document.getElementById('adminPromptForm').reset();
  document.getElementById('prompt_id').value = p.id;
  document.getElementById('prompt_title').value = p.title || '';
  document.getElementById('prompt_category_id').value = p.category_id || 1;
  document.getElementById('prompt_summary').value = p.summary || '';
  document.getElementById('prompt_template_body').value = p.prompt_template || '';
  document.getElementById('prompt_variables').value = p.template_variables || '[]';
  document.getElementById('prompt_models').value = p.target_models || '[]';
  document.getElementById('prompt_sample_output').value = p.sample_output || '';

  const promptType = (p.prompt_type === 'image') ? 'image' : 'text';
  setPromptType(promptType);

  if (p.featured_image_url) {
    document.getElementById('prompt_featured_image_url').value = p.featured_image_url;
    previewUrlPromptImage(p.featured_image_url);
  } else {
    clearPromptImagePreview();
  }

  document.getElementById('modalPromptTitle').innerText = 'Edit Prompt #' + p.id;
  document.getElementById('promptFormModal').style.display = 'flex';
}

document.addEventListener('DOMContentLoaded', function() {
  if (window.paginateAdminTable) {
    window.promptsPager = window.paginateAdminTable('promptsCrudTable', {
      pageSize: 10,
      searchInputId: 'promptSearchInput'
    });
  }
});
</script>

<?php require __DIR__ . '/../../layouts/admin_footer.php'; ?>
