<?php
$title = ($article ? 'Edit Help Article: ' . htmlspecialchars($article['title']) : 'Create New Help Article') . ' | TechScout Admin';
require __DIR__ . '/../layouts/admin_header.php';

$steps = [];
if (!empty($article['steps_json'])) {
    $decoded = json_decode($article['steps_json'], true);
    if (is_array($decoded)) $steps = $decoded;
}

$faqs = [];
if (!empty($article['faqs_json'])) {
    $decoded = json_decode($article['faqs_json'], true);
    if (is_array($decoded)) $faqs = $decoded;
}
?>

<div class="admin-content-inner">
  <!-- Page Header -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <a href="<?= url('/admin/help-articles') ?>" style="color: #38bdf8; text-decoration: none; font-size: 0.8rem; font-weight: 600;">
          &larr; Back to Help Articles List
        </a>
      </div>
      <h1 style="font-size: 1.65rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        <?= $article ? '✏️ Edit Help Article' : '➕ Create New Help Article' ?>
      </h1>
      <p style="color: #94a3b8; font-size: 0.85rem; margin: 0.25rem 0 0 0;">
        Configure title, YouTube/TikTok style breakdown steps, interactive FAQs, and rich HTML guides.
      </p>
    </div>

    <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.5rem; align-items: center;">
      <?php if (!empty($article['slug'])): ?>
        <a href="<?= url('/help/' . $article['slug']) ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8;">
          <i data-lucide="external-link"></i> Preview Live ↗
        </a>
      <?php endif; ?>
      <button type="submit" form="helpArticleForm" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #10b981, #059669); font-weight: 800; padding: 0.55rem 1.5rem;">
        <i data-lucide="save"></i> Save Article 💾
      </button>
    </div>
  </div>

  <form action="<?= url('/admin/help-articles/save') ?>" method="POST" id="helpArticleForm">
    <input type="hidden" name="id" value="<?= (int)($article['id'] ?? 0) ?>">

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: start;">
      
      <!-- Left Column: Core Content, Steps & FAQs -->
      <div style="flex-wrap: wrap; align-items: center; display: flex; flex-direction: column; gap: 1.5rem;">
        
        <!-- Main Details Card -->
        <div class="admin-table-wrap" style="padding: 1.75rem;">
          <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 1.25rem 0; font-weight: 700; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 0.75rem;">
            📝 Article Header &amp; Overview
          </h3>

          <div style="margin-bottom: 1.25rem;">
            <label class="admin-modal-label">Article Title <span style="color: #ef4444;">*</span></label>
            <input type="text" name="title" id="art_title_input" oninput="autoGenerateSlug(this.value)" value="<?= htmlspecialchars($article['title'] ?? '') ?>" placeholder="e.g. How to Enable Two-Factor Authentication (2FA TOTP)" class="admin-modal-input" required>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">URL Slug <span style="color: #ef4444;">*</span></label>
              <input type="text" name="slug" id="art_slug_input" value="<?= htmlspecialchars($article['slug'] ?? '') ?>" placeholder="e.g. how-to-enable-2fa" class="admin-modal-input" style="font-family: monospace;" required>
              <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.25rem;">Accessible at: /help/<strong>slug</strong></div>
            </div>
            <div>
              <label class="admin-modal-label">Badge Tag</label>
              <input type="text" name="badge" value="<?= htmlspecialchars($article['badge'] ?? 'GUIDE') ?>" placeholder="e.g. SECURITY, PRODUCTIVITY, CORE OVERVIEW" class="admin-modal-input">
            </div>
          </div>

          <div style="margin-bottom: 1.25rem;">
            <label class="admin-modal-label">Summary / Description</label>
            <textarea name="summary" rows="3" class="admin-modal-input" placeholder="Brief 1-2 sentence overview shown on index cards and meta description..."><?= htmlspecialchars($article['summary'] ?? '') ?></textarea>
          </div>

          <div style="margin-bottom: 0;">
            <label class="admin-modal-label">Key Takeaway Box (Highlighted Callout)</label>
            <textarea name="key_takeaway" rows="2" class="admin-modal-input" placeholder="Core takeaway highlighted in prominent callout box at the top of the article..."><?= htmlspecialchars($article['key_takeaway'] ?? '') ?></textarea>
          </div>
        </div>

        <!-- Step-by-Step Instructions Builder (YouTube / TikTok Style) -->
        <div class="admin-table-wrap" style="padding: 1.75rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 0.75rem;">
            <div>
              <h3 style="font-size: 1.15rem; color: #38bdf8; margin: 0; font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
                <i data-lucide="list-ordered"></i> Step-by-Step Instructions Builder
              </h3>
              <p style="font-size: 0.8rem; color: #94a3b8; margin: 0.2rem 0 0 0;">Numbered step cards that render identically to YouTube help guides.</p>
            </div>
            <button type="button" onclick="addStepRow()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.35);">
              + Add Step
            </button>
          </div>

          <div id="stepsContainer" style="flex-wrap: wrap; align-items: center; display: flex; flex-direction: column; gap: 1rem;">
            <?php if (!empty($steps)): ?>
              <?php foreach ($steps as $i => $s): ?>
                <div class="step-card-row" style="background: rgba(15,23,42,0.8); border: 1px solid var(--admin-border-subtle); border-radius: 8px; padding: 1rem; position: relative;">
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #38bdf8;">Step #<span class="step-num-label"><?= $i + 1 ?></span></span>
                    <button type="button" onclick="this.closest('.step-card-row').remove(); reindexSteps();" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.7rem; color: #ef4444;">Remove</button>
                  </div>
                  <input type="text" name="step_titles[]" value="<?= htmlspecialchars($s['title'] ?? '') ?>" placeholder="Step Title (e.g. Open Security Settings)" class="admin-modal-input" style="margin-bottom: 0.5rem;" required>
                  <textarea name="step_descs[]" rows="2" placeholder="Step detailed instructions and navigation path..." class="admin-modal-input"><?= htmlspecialchars($s['desc'] ?? '') ?></textarea>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="step-card-row" style="background: rgba(15,23,42,0.8); border: 1px solid var(--admin-border-subtle); border-radius: 8px; padding: 1rem; position: relative;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                  <span style="font-size: 0.8rem; font-weight: 700; color: #38bdf8;">Step #<span class="step-num-label">1</span></span>
                  <button type="button" onclick="this.closest('.step-card-row').remove(); reindexSteps();" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.7rem; color: #ef4444;">Remove</button>
                </div>
                <input type="text" name="step_titles[]" placeholder="Step Title (e.g. Open Security Settings)" class="admin-modal-input" style="margin-bottom: 0.5rem;">
                <textarea name="step_descs[]" rows="2" placeholder="Step detailed instructions and navigation path..." class="admin-modal-input"></textarea>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Interactive FAQs Builder -->
        <div class="admin-table-wrap" style="padding: 1.75rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 0.75rem;">
            <div>
              <h3 style="font-size: 1.15rem; color: #c084fc; margin: 0; font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
                <i data-lucide="help-circle"></i> In-Article FAQs (Collapsible Accordions)
              </h3>
              <p style="font-size: 0.8rem; color: #94a3b8; margin: 0.2rem 0 0 0;">Common troubleshooting questions rendered inside expandable drawers.</p>
            </div>
            <button type="button" onclick="addFaqRow()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #c084fc; border-color: rgba(192,132,252,0.35);">
              + Add FAQ
            </button>
          </div>

          <div id="faqsContainer" style="flex-wrap: wrap; align-items: center; display: flex; flex-direction: column; gap: 1rem;">
            <?php if (!empty($faqs)): ?>
              <?php foreach ($faqs as $j => $f): ?>
                <div class="faq-card-row" style="background: rgba(15,23,42,0.8); border: 1px solid var(--admin-border-subtle); border-radius: 8px; padding: 1rem; position: relative;">
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #c084fc;">Question #<span class="faq-num-label"><?= $j + 1 ?></span></span>
                    <button type="button" onclick="this.closest('.faq-card-row').remove(); reindexFaqs();" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.7rem; color: #ef4444;">Remove</button>
                  </div>
                  <input type="text" name="faq_questions[]" value="<?= htmlspecialchars($f['q'] ?? '') ?>" placeholder="Question (e.g. What if I lose my authenticator app?)" class="admin-modal-input" style="margin-bottom: 0.5rem;">
                  <textarea name="faq_answers[]" rows="2" placeholder="Detailed answer provided to the user..." class="admin-modal-input"><?= htmlspecialchars($f['a'] ?? '') ?></textarea>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="faq-card-row" style="background: rgba(15,23,42,0.8); border: 1px solid var(--admin-border-subtle); border-radius: 8px; padding: 1rem; position: relative;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                  <span style="font-size: 0.8rem; font-weight: 700; color: #c084fc;">Question #<span class="faq-num-label">1</span></span>
                  <button type="button" onclick="this.closest('.faq-card-row').remove(); reindexFaqs();" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.7rem; color: #ef4444;">Remove</button>
                </div>
                <input type="text" name="faq_questions[]" placeholder="Question (e.g. Is TechScout free to use for developers?)" class="admin-modal-input" style="margin-bottom: 0.5rem;">
                <textarea name="faq_answers[]" rows="2" placeholder="Detailed answer provided to the user..." class="admin-modal-input"></textarea>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Rich Extended HTML Content (Optional In-Depth Guide) -->
        <div class="admin-table-wrap" style="padding: 1.75rem;">
          <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 0.5rem 0; font-weight: 700;">
            📄 In-Depth Content &amp; HTML Guide (Optional)
          </h3>
          <p style="font-size: 0.8rem; color: #94a3b8; margin: 0 0 1rem 0;">
            If the article requires long-form markdown/HTML paragraphs, code fences, or embedded screenshots, enter the rich content below:
          </p>
          <textarea name="content_html" rows="10" class="admin-modal-input" style="font-family: monospace; font-size: 0.88rem; line-height: 1.6;" placeholder="<p>Full HTML content...</p>"><?= htmlspecialchars($article['content_html'] ?? '') ?></textarea>
        </div>

      </div>

      <!-- Right Column: Taxonomy, Category, Publishing & Sorting -->
      <div style="flex-wrap: wrap; align-items: center; display: flex; flex-direction: column; gap: 1.5rem;">
        
        <!-- Category & Classification -->
        <div class="admin-table-wrap" style="padding: 1.75rem;">
          <h3 style="font-size: 1.1rem; color: #f8fafc; margin: 0 0 1rem 0; font-weight: 700; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 0.5rem;">
            📂 Category Taxonomy
          </h3>

          <div style="margin-bottom: 1rem;">
            <label class="admin-modal-label">Category Name <span style="color: #ef4444;">*</span></label>
            <input type="text" name="category_name" id="cat_name_input" value="<?= htmlspecialchars($article['category_name'] ?? 'Getting Started & Account') ?>" list="existingCategoryNames" class="admin-modal-input" onchange="autoFillCategorySlug(this.value)" required>
            <datalist id="existingCategoryNames">
              <?php foreach ($existingCategories as $ec): ?>
                <option value="<?= htmlspecialchars($ec['category_name']) ?>"><?= htmlspecialchars($ec['category_slug']) ?></option>
              <?php endforeach; ?>
            </datalist>
          </div>

          <div style="margin-bottom: 1rem;">
            <label class="admin-modal-label">Category Slug Key <span style="color: #ef4444;">*</span></label>
            <input type="text" name="category_slug" id="cat_slug_input" value="<?= htmlspecialchars($article['category_slug'] ?? 'getting_started') ?>" placeholder="e.g. getting_started" class="admin-modal-input" style="font-family: monospace;" required>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
            <div>
              <label class="admin-modal-label">Lucide Icon</label>
              <input type="text" name="category_icon" value="<?= htmlspecialchars($article['category_icon'] ?? 'sparkles') ?>" placeholder="e.g. sparkles, cpu, wallet" class="admin-modal-input">
            </div>
            <div>
              <label class="admin-modal-label">Category Accent Color</label>
              <input type="text" name="category_color" value="<?= htmlspecialchars($article['category_color'] ?? '#6366f1') ?>" placeholder="#6366f1" class="admin-modal-input">
            </div>
          </div>

          <div style="margin-bottom: 0;">
            <label class="admin-modal-label">Category Tagline / Description</label>
            <textarea name="category_desc" rows="2" class="admin-modal-input" placeholder="Brief category description shown on category hub..."><?= htmlspecialchars($article['category_desc'] ?? '') ?></textarea>
          </div>
        </div>

        <!-- Publishing & Sorting Controls -->
        <div class="admin-table-wrap" style="padding: 1.75rem;">
          <h3 style="font-size: 1.1rem; color: #f8fafc; margin: 0 0 1rem 0; font-weight: 700; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 0.5rem;">
            ⚙️ Publishing &amp; Placement
          </h3>

          <div style="margin-bottom: 1rem;">
            <label class="admin-modal-label">Publication Status</label>
            <select name="is_published" class="admin-modal-select">
              <option value="1" <?= (!isset($article['is_published']) || (int)$article['is_published'] === 1) ? 'selected' : '' ?>>✅ Published (Live on /help)</option>
              <option value="0" <?= (isset($article['is_published']) && (int)$article['is_published'] === 0) ? 'selected' : '' ?>>Draft / Hidden</option>
            </select>
          </div>

          <div style="margin-bottom: 1rem;">
            <label class="admin-modal-label">Featured Spotlight</label>
            <label style="display: flex; align-items: center; gap: 0.5rem; color: #cbd5e1; font-size: 0.85rem; cursor: pointer;">
              <input type="checkbox" name="is_featured" value="1" <?= !empty($article['is_featured']) ? 'checked' : '' ?> style="width: 16px; height: 16px;">
              <span>Feature on Help Center Top Grid (🔥 Most Visited)</span>
            </label>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">Estimated Read Time</label>
              <input type="text" name="read_time" value="<?= htmlspecialchars($article['read_time'] ?? '3 min read') ?>" placeholder="e.g. 3 min read" class="admin-modal-input">
            </div>
            <div>
              <label class="admin-modal-label">Display Sort Order</label>
              <input type="number" name="sort_order" value="<?= (int)($article['sort_order'] ?? 0) ?>" class="admin-modal-input">
            </div>
          </div>

          <button type="submit" class="ts-btn ts-btn-primary" style="width: 100%; justify-content: center; background: linear-gradient(135deg, #10b981, #059669); font-weight: 800; padding: 0.65rem;">
            <i data-lucide="save"></i> Save Help Article 💾
          </button>
        </div>

      </div>

    </div>
  </form>
</div>

<script>
function autoGenerateSlug(val) {
  const slugInput = document.getElementById('art_slug_input');
  if (slugInput && !slugInput.dataset.manual) {
    slugInput.value = val.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
  }
}
document.getElementById('art_slug_input')?.addEventListener('input', function() {
  this.dataset.manual = 'true';
});

function autoFillCategorySlug(name) {
  const catSlug = document.getElementById('cat_slug_input');
  if (!catSlug.value || catSlug.value === 'getting_started') {
    catSlug.value = name.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
  }
}

function addStepRow() {
  const container = document.getElementById('stepsContainer');
  const count = container.querySelectorAll('.step-card-row').length + 1;
  const div = document.createElement('div');
  div.className = 'step-card-row';
  div.style = 'background: rgba(15,23,42,0.8); border: 1px solid var(--admin-border-subtle); border-radius: 8px; padding: 1rem; position: relative;';
  div.innerHTML = `
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
      <span style="font-size: 0.8rem; font-weight: 700; color: #38bdf8;">Step #<span class="step-num-label">${count}</span></span>
      <button type="button" onclick="this.closest('.step-card-row').remove(); reindexSteps();" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.7rem; color: #ef4444;">Remove</button>
    </div>
    <input type="text" name="step_titles[]" placeholder="Step Title" class="admin-modal-input" style="margin-bottom: 0.5rem;" required>
    <textarea name="step_descs[]" rows="2" placeholder="Step detailed instructions..." class="admin-modal-input"></textarea>
  `;
  container.appendChild(div);
  if (typeof lucide !== 'undefined') lucide.createIcons();
}

function reindexSteps() {
  document.querySelectorAll('#stepsContainer .step-card-row').forEach((row, idx) => {
    const lbl = row.querySelector('.step-num-label');
    if (lbl) lbl.textContent = idx + 1;
  });
}

function addFaqRow() {
  const container = document.getElementById('faqsContainer');
  const count = container.querySelectorAll('.faq-card-row').length + 1;
  const div = document.createElement('div');
  div.className = 'faq-card-row';
  div.style = 'background: rgba(15,23,42,0.8); border: 1px solid var(--admin-border-subtle); border-radius: 8px; padding: 1rem; position: relative;';
  div.innerHTML = `
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
      <span style="font-size: 0.8rem; font-weight: 700; color: #c084fc;">Question #<span class="faq-num-label">${count}</span></span>
      <button type="button" onclick="this.closest('.faq-card-row').remove(); reindexFaqs();" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.7rem; color: #ef4444;">Remove</button>
    </div>
    <input type="text" name="faq_questions[]" placeholder="Question" class="admin-modal-input" style="margin-bottom: 0.5rem;" required>
    <textarea name="faq_answers[]" rows="2" placeholder="Answer..." class="admin-modal-input" required></textarea>
  `;
  container.appendChild(div);
  if (typeof lucide !== 'undefined') lucide.createIcons();
}

function reindexFaqs() {
  document.querySelectorAll('#faqsContainer .faq-card-row').forEach((row, idx) => {
    const lbl = row.querySelector('.faq-num-label');
    if (lbl) lbl.textContent = idx + 1;
  });
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
