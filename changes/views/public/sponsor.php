<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="max-width: 960px; margin: 3rem auto 6rem auto; padding: 0 1.5rem;">

  <!-- Hero Header -->
  <div style="text-align: center; margin-bottom: 3rem;">
    <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.3); padding: 0.35rem 0.85rem; border-radius: 9999px; margin-bottom: 1rem;">
      <i data-lucide="zap" style="width: 15px; height: 15px; color: #f59e0b;"></i>
      <span style="font-size: 0.78rem; font-weight: 800; color: #f59e0b; text-transform: uppercase;">
        SPONSORED PLACEMENTS &bull; CRYPTO TREASURY
      </span>
    </div>
    <h1 style="font-size: 2.75rem; font-weight: 800; color: var(--ts-text-primary); margin: 0 0 0.75rem 0; font-family: 'Outfit', sans-serif; line-height: 1.2;">
      Accelerate Your AI Reach with Sponsored Placements
    </h1>
    <p style="color: var(--ts-text-secondary); font-size: 1.05rem; max-width: 680px; margin: 0 auto; line-height: 1.6;">
      Get your AI tool or product featured prominently before 250,000+ developers, tech founders, and AI prompt engineers worldwide. Fast 24-hour editorial turnaround.
    </p>
  </div>

  <?php if (!empty($flashSuccess)): ?>
    <div style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); border-radius: 12px; padding: 1.25rem 1.5rem; margin-bottom: 2rem; color: #34d399; font-size: 0.95rem; display: flex; align-items: center; gap: 0.75rem;">
      <i data-lucide="check-circle" style="width: 24px; height: 24px; flex-shrink: 0;"></i>
      <div>
        <strong>Submission Received!</strong> <?= htmlspecialchars($flashSuccess) ?>
      </div>
    </div>
  <?php endif; ?>

  <?php if (!empty($flashError)): ?>
    <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-radius: 12px; padding: 1.25rem 1.5rem; margin-bottom: 2rem; color: #f87171; font-size: 0.95rem; display: flex; align-items: center; gap: 0.75rem;">
      <i data-lucide="alert-circle" style="width: 24px; height: 24px; flex-shrink: 0;"></i>
      <div>
        <strong>Error:</strong> <?= htmlspecialchars($flashError) ?>
      </div>
    </div>
  <?php endif; ?>

  <!-- Two Main Sponsorship Program Options -->
  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2.5rem;">
    
    <!-- Option A: Sponsored AI Tool -->
    <div class="ts-card <?= ($currentTab ?? 'tool') === 'tool' ? 'tier-selected' : '' ?>" id="card_option_tool" onclick="switchSponsorTab('tool')" style="padding: 1.75rem; border-radius: 14px; border: 2px solid var(--ts-border-main); background: var(--ts-bg-surface); cursor: pointer; transition: all 0.25s ease; position: relative;">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(56,189,248,0.12); color: var(--ts-primary); display: flex; align-items: center; justify-content: center;">
          <i data-lucide="cpu" style="width: 24px; height: 24px;"></i>
        </div>
        <span class="ts-badge ts-badge-cyan" style="font-weight: 800; font-size: 0.85rem;">$149 USD</span>
      </div>
      <h3 style="font-size: 1.35rem; color: var(--ts-text-primary); margin: 0 0 0.35rem 0;">Sponsored AI Tool</h3>
      <p style="color: var(--ts-text-secondary); font-size: 0.85rem; margin: 0 0 1rem 0; line-height: 1.5;">
        Pinned #1 featured placement in AI tools directory, verified author badge, and do-follow external link.
      </p>
      <ul style="padding-left: 1.2rem; margin: 0; font-size: 0.82rem; color: var(--ts-text-tertiary); line-height: 1.7;">
        <li>⭐ Top placement for 30 consecutive days</li>
        <li>🔗 Verified Do-Follow backlink to your website</li>
        <li>📢 Featured in weekly newsletter blast</li>
        <li>⚡ 24-hour instant activation upon confirmation</li>
      </ul>
    </div>

    <!-- Option B: Sponsored Editorial Post -->
    <div class="ts-card <?= ($currentTab ?? '') === 'article' ? 'tier-selected' : '' ?>" id="card_option_article" onclick="switchSponsorTab('article')" style="padding: 1.75rem; border-radius: 14px; border: 2px solid var(--ts-border-main); background: var(--ts-bg-surface); cursor: pointer; transition: all 0.25s ease; position: relative;">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(245,158,11,0.12); color: #f59e0b; display: flex; align-items: center; justify-content: center;">
          <i data-lucide="file-text" style="width: 24px; height: 24px;"></i>
        </div>
        <span class="ts-badge ts-badge-gold" style="font-weight: 800; font-size: 0.85rem;">$299 USD</span>
      </div>
      <h3 style="font-size: 1.35rem; color: var(--ts-text-primary); margin: 0 0 0.35rem 0;">Sponsored Article / Post</h3>
      <p style="color: var(--ts-text-secondary); font-size: 0.85rem; margin: 0 0 1rem 0; line-height: 1.5;">
        Permanent thought-leadership article published on TechScout with homepage banner placement and social broadcast.
      </p>
      <ul style="padding-left: 1.2rem; margin: 0; font-size: 0.82rem; color: var(--ts-text-tertiary); line-height: 1.7;">
        <li>📰 Permanent publication on TechScout with DO-FOLLOW link</li>
        <li>⭐ Featured banner ribbon on homepage for 14 days</li>
        <li>📄 Word (.docx) &amp; Markdown submissions welcome</li>
        <li>🎯 Indexed across Google News &amp; search engines</li>
      </ul>
    </div>

  </div>

  <!-- Main Submission Form Container -->
  <div class="ts-card" style="padding: 2.5rem; border: 1px solid var(--ts-border-main); background: var(--ts-bg-surface); border-radius: 16px; box-shadow: var(--ts-shadow-xl);">

    <!-- =================================================================== -->
    <!-- FORM 1: SPONSORED AI TOOL SUBMISSION -->
    <!-- =================================================================== -->
    <form id="form_sponsor_tool" action="<?= url('/sponsor/submit-tool') ?>" method="POST" enctype="multipart/form-data" style="<?= ($currentTab ?? 'tool') === 'tool' ? 'display:block;' : 'display:none;' ?>">
      <div style="border-bottom: 1px solid var(--ts-border-subtle); padding-bottom: 1rem; margin-bottom: 1.5rem;">
        <h2 style="font-size: 1.4rem; color: var(--ts-text-primary); margin: 0 0 0.25rem 0;">
          🚀 Submit Sponsored AI Tool Listing
        </h2>
        <p style="color: var(--ts-text-secondary); font-size: 0.88rem; margin: 0;">
          Provide your tool details. After submitting with your payment TXID, our editorial team reviews and activates within 24 hours.
        </p>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
        <div>
          <label class="ts-form-label">Tool Name</label>
          <input type="text" name="title" class="ts-form-input" placeholder="e.g. CodeForge AI" required>
        </div>
        <div>
          <label class="ts-form-label">Destination Website URL</label>
          <input type="url" name="target_url" class="ts-form-input" placeholder="https://codeforge.ai" required>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
        <div>
          <label class="ts-form-label">Category</label>
          <select name="category_id" class="ts-form-input">
            <?php foreach (($categories ?? []) as $cat): ?>
              <option value="<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="ts-form-label">Your Company / Brand Name</label>
          <input type="text" name="author_name" class="ts-form-input" placeholder="e.g. Forge Technologies Inc" required>
        </div>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label class="ts-form-label">Contact / Billing Email</label>
        <input type="email" name="author_email" class="ts-form-input" placeholder="founder@codeforge.ai" required>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label class="ts-form-label">Short Tagline (max 160 characters)</label>
        <input type="text" name="summary" maxlength="160" class="ts-form-input" placeholder="AI-powered code refactoring and automated test generator for enterprise teams." required>
      </div>

      <div style="margin-bottom: 1.5rem;">
        <label class="ts-form-label">Full Tool Description &amp; Key Features</label>
        <textarea name="body_content" rows="4" class="ts-form-input" placeholder="Describe core features, pricing tiers, API access, integrations, and target audience..." required></textarea>
      </div>

      <div style="margin-bottom: 2rem;">
        <label class="ts-form-label">Logo Image URL</label>
        <input type="url" name="image_url" class="ts-form-input" placeholder="https://codeforge.ai/logo.png">
      </div>

      <!-- Crypto Treasury Payment Strip -->
      <?php require __DIR__ . '/components/crypto_payment_box.php'; ?>

      <button type="submit" class="ts-btn ts-btn-primary" style="width: 100%; padding: 0.85rem; font-weight: 800; font-size: 1rem; background: linear-gradient(135deg, #0284c7, #38bdf8); box-shadow: 0 4px 15px rgba(56,189,248,0.35);">
        🚀 Submit Sponsored Tool ($149 USD) &bull; Confirm Payment
      </button>
    </form>


    <!-- =================================================================== -->
    <!-- FORM 2: SPONSORED ARTICLE / POST SUBMISSION -->
    <!-- =================================================================== -->
    <form id="form_sponsor_article" action="<?= url('/sponsor/submit-post') ?>" method="POST" enctype="multipart/form-data" style="<?= ($currentTab ?? '') === 'article' ? 'display:block;' : 'display:none;' ?>">
      <div style="border-bottom: 1px solid var(--ts-border-subtle); padding-bottom: 1rem; margin-bottom: 1.5rem;">
        <h2 style="font-size: 1.4rem; color: var(--ts-text-primary); margin: 0 0 0.25rem 0;">
          📝 Submit Sponsored Article or Product Review
        </h2>
        <p style="color: var(--ts-text-secondary); font-size: 0.88rem; margin: 0;">
          Publish a comprehensive thought-leadership piece or sponsored product review with permanent do-follow links.
        </p>
      </div>

      <!-- Author Recommendation Alert for Word Docx Submissions -->
      <div style="background: rgba(56,189,248,0.06); border: 1px solid rgba(56,189,248,0.3); border-radius: 10px; padding: 1.15rem; margin-bottom: 1.5rem; display: flex; align-items: flex-start; gap: 0.85rem;">
        <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(56,189,248,0.15); color: var(--ts-primary); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
          <i data-lucide="file-check" style="width: 20px; height: 20px;"></i>
        </div>
        <div>
          <strong style="color: var(--ts-text-primary); font-size: 0.92rem; display: block; margin-bottom: 0.2rem;">
            📌 Author Recommendation: We Strongly Prefer Word (.docx) Submissions
          </strong>
          <p style="color: var(--ts-text-secondary); font-size: 0.82rem; margin: 0; line-height: 1.5;">
            For optimal editorial fidelity, authors should prefer submitting drafts in Microsoft Word (<code>.docx</code>) format. Word documents preserve embedded images, callouts, tables, footnotes, and code snippets seamlessly during review.
          </p>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
        <div>
          <label class="ts-form-label">Article Headline / Title</label>
          <input type="text" name="title" class="ts-form-input" placeholder="e.g. How Next-Gen Synthetic LLMs Are Revolutionizing Legal Research" required>
        </div>
        <div>
          <label class="ts-form-label">Category</label>
          <select name="category_id" class="ts-form-input">
            <?php foreach (($categories ?? []) as $cat): ?>
              <option value="<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
        <div>
          <label class="ts-form-label">Author / Brand Byline Name</label>
          <input type="text" name="author_name" class="ts-form-input" placeholder="e.g. Dr. Sarah Chen, LexAI" required>
        </div>
        <div>
          <label class="ts-form-label">Contact / Editorial Email</label>
          <input type="email" name="author_email" class="ts-form-input" placeholder="editor@lexai.io" required>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
        <div>
          <label class="ts-form-label">Primary Brand / Target DoFollow URL</label>
          <input type="url" name="target_url" class="ts-form-input" placeholder="https://lexai.io/enterprise">
        </div>
        <div>
          <label class="ts-form-label">Featured Hero Image URL</label>
          <input type="url" name="image_url" class="ts-form-input" placeholder="https://images.unsplash.com/...">
        </div>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label class="ts-form-label">Executive Abstract / Summary (2-3 sentences)</label>
        <textarea name="summary" rows="2" class="ts-form-input" placeholder="Provide a brief compelling summary that will appear on homepage and discovery feeds..." required></textarea>
      </div>

      <!-- Optional Word Docx File Upload -->
      <div style="padding: 1.25rem; background: var(--ts-bg-surface-elevated); border: 2px dashed var(--ts-border-main); border-radius: 10px; margin-bottom: 1.5rem; text-align: center;">
        <i data-lucide="file-up" style="width: 32px; height: 32px; color: var(--ts-primary); margin-bottom: 0.5rem;"></i>
        <div style="font-weight: 700; color: var(--ts-text-primary); font-size: 0.92rem; margin-bottom: 0.25rem;">
          Upload Article Manuscript (.docx preferred)
        </div>
        <div style="font-size: 0.78rem; color: var(--ts-text-muted); margin-bottom: 0.75rem;">
          Upload your Word document (.docx) or manuscript directly for instant editorial ingestion.
        </div>
        <input type="file" name="article_docx" accept=".docx,.doc,.txt" style="font-size: 0.85rem; color: var(--ts-text-secondary);">
      </div>

      <div style="margin-bottom: 2rem;">
        <label class="ts-form-label">Or Paste Full Article HTML / Markdown Body</label>
        <textarea name="body_content" rows="8" class="ts-form-input" placeholder="Paste article draft here if not uploading Word document..."></textarea>
      </div>

      <!-- Crypto Treasury Payment Strip -->
      <?php require __DIR__ . '/components/crypto_payment_box.php'; ?>

      <button type="submit" class="ts-btn ts-btn-primary" style="width: 100%; padding: 0.85rem; font-weight: 800; font-size: 1rem; background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 15px rgba(245,158,11,0.35);">
        📝 Submit Sponsored Post ($299 USD) &bull; Confirm Payment
      </button>
    </form>

  </div>

</div>

<style>
.tier-selected {
  border-color: var(--ts-primary) !important;
  box-shadow: 0 0 20px rgba(56,189,248,0.2) !important;
}
.ts-form-label {
  display: block;
  font-size: 0.82rem;
  font-weight: 700;
  color: var(--ts-text-secondary);
  margin-bottom: 0.4rem;
  text-transform: uppercase;
}
.ts-form-input {
  width: 100%;
  background: var(--ts-bg-surface-elevated);
  border: 1px solid var(--ts-border-main);
  border-radius: 8px;
  padding: 0.65rem 0.95rem;
  color: var(--ts-text-primary);
  font-size: 0.9rem;
  outline: none;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.ts-form-input:focus {
  border-color: var(--ts-primary);
  box-shadow: 0 0 0 3px rgba(56,189,248,0.15);
}
</style>

<script>
function switchSponsorTab(tab) {
  const cardTool = document.getElementById('card_option_tool');
  const cardArticle = document.getElementById('card_option_article');
  const formTool = document.getElementById('form_sponsor_tool');
  const formArticle = document.getElementById('form_sponsor_article');

  if (tab === 'tool') {
    cardTool?.classList.add('tier-selected');
    cardArticle?.classList.remove('tier-selected');
    if (formTool) formTool.style.display = 'block';
    if (formArticle) formArticle.style.display = 'none';
  } else {
    cardArticle?.classList.add('tier-selected');
    cardTool?.classList.remove('tier-selected');
    if (formArticle) formArticle.style.display = 'block';
    if (formTool) formTool.style.display = 'none';
  }
}

function copyCryptoAddress(id, btn) {
  const input = document.getElementById(id);
  if (!input) return;
  navigator.clipboard.writeText(input.value).then(() => {
    const orig = btn.innerText;
    btn.innerText = 'Copied! ✓';
    btn.style.color = '#34d399';
    setTimeout(() => {
      btn.innerText = orig;
      btn.style.color = '';
    }, 1500);
  });
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
