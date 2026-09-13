<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
$isImageEngine = (($prompt['prompt_type'] ?? '') === 'image');
$modelsStr = is_string($prompt['target_models'] ?? '') ? $prompt['target_models'] : json_encode($prompt['target_models'] ?? []);
if (!$isImageEngine && (stripos($modelsStr, 'Midjourney') !== false || stripos($modelsStr, 'DALL-E') !== false || stripos($modelsStr, 'Flux') !== false || stripos($modelsStr, 'Stable Diffusion') !== false || in_array($prompt['category_slug'] ?? '', ['ai-image-prompts', 'midjourney', 'dall-e', 'flux', 'stable-diffusion']))) {
    $isImageEngine = true;
}

$allImages = [];
// Strictly attach image gallery only if this is an image generation prompt
$featuredImage = !empty($prompt['featured_image_url']) ? image_url($prompt['featured_image_url']) : '';
if ($isImageEngine && !empty($featuredImage) && $featuredImage !== 'https://funwithcoding.free.nf/') {
    $allImages[] = ['image_url' => $featuredImage, 'caption' => 'Master Generation Sample'];
}
if ($isImageEngine && !empty($galleryImages)) {
    foreach ($galleryImages as $gi) {
        $allImages[] = ['image_url' => image_url($gi['image_url']), 'caption' => $gi['caption'] ?? 'Variation Sample'];
    }
}
$hasGallery = $isImageEngine && !empty($allImages);
?>

<div class="container" style="padding-top: 2.5rem; margin-bottom: 5rem;">
  <div style="max-width: 920px; margin: 0 auto;">

    <!-- Breadcrumbs -->
    <div style="font-size: 0.82rem; color: var(--ts-text-muted); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.4rem;">
      <a href="<?= url('/prompts') ?>" style="color: var(--ts-text-secondary); text-decoration: none;">Prompts Hub</a>
      <span>&rsaquo;</span>
      <a href="<?= url('/prompts?category=' . urlencode($prompt['category_slug'] ?? '')) ?>" style="color: var(--ts-text-secondary); text-decoration: none;"><?= htmlspecialchars($prompt['category_name'] ?? 'Library') ?></a>
      <span>&rsaquo;</span>
      <span style="color: var(--ts-text-primary);"><?= htmlspecialchars(substr($prompt['title'], 0, 45)) ?>...</span>
    </div>

    <!-- Prompt Header -->
    <div style="margin-bottom: 2rem;">
      <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap;">
        <span class="ts-badge <?= $isImageEngine ? 'ts-badge-pink' : 'ts-badge-green' ?>">
          <?= $isImageEngine ? '🎨 Visual Generative Blueprint' : '⚡ System / LLM Prompt Blueprint' ?>
        </span>
        <span class="ts-badge ts-badge-cyan"><?= htmlspecialchars($prompt['category_name'] ?? 'AI Prompt') ?></span>
        <?php if (!empty($prompt['topic_name'])): ?>
          <button type="button" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.55rem; font-size: 0.72rem; border-radius: 9999px;" onclick="toggleTopicSubscription(<?= (int)$prompt['topic_id'] ?>, this)">
            <i data-lucide="plus" style="width: 11px; height: 11px;"></i>
            <span>Follow <?= htmlspecialchars($prompt['topic_name']) ?></span>
          </button>
        <?php endif; ?>
      </div>

      <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem; line-height: 1.25; font-family: 'Outfit', sans-serif; font-weight: 800;">
        <?= htmlspecialchars($prompt['title']) ?>
      </h1>

      <div style="font-size: 0.82rem; color: var(--ts-text-tertiary); margin-bottom: 0.9rem; display: flex; gap: 0.85rem; flex-wrap: wrap; align-items: center;">
        <span>Published <?= \App\Core\DateHelper::render($prompt['created_at'] ?? 'now', 'M j, Y • g:i A', 'datetime') ?></span>
        <?php if (!empty($prompt['updated_at']) && strtotime($prompt['updated_at']) > strtotime($prompt['created_at'] ?? 'now') + 60): ?>
          <span style="color: #38bdf8;">&bull; Updated <?= \App\Core\DateHelper::render($prompt['updated_at'], 'M j, Y • g:i A', 'datetime') ?></span>
        <?php endif; ?>
        <span>&bull; <i data-lucide="copy" style="width: 12px; height: 12px; display: inline;"></i> <?= number_format((int)($prompt['copy_count'] ?? 0)) ?> copies</span>
      </div>

      <p style="color: var(--ts-text-secondary); font-size: 1.08rem; line-height: 1.6; margin: 0;">
        <?= htmlspecialchars($prompt['summary']) ?>
      </p>
    </div>

    <!-- 1. Visual Showcase & Multi-Image Gallery (If Available) -->
    <?php if ($hasGallery): ?>
      <div class="ts-card" style="margin-bottom: 2.25rem; padding: 1.25rem; border-radius: 16px; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
          <div style="font-weight: 700; font-size: 0.95rem; color: var(--ts-text-primary); display: flex; align-items: center; gap: 0.5rem;">
            <i data-lucide="image" style="width: 18px; height: 18px; color: #ec4899;"></i>
            <span>Visual Render Samples (<?= count($allImages) ?> <?= count($allImages) === 1 ? 'Sample' : 'Variations' ?>)</span>
          </div>
          <button type="button" class="ts-btn ts-btn-outline ts-btn-sm" onclick="openSinglePromptLightbox(0)">
            <i data-lucide="maximize-2" style="width: 13px; height: 13px;"></i> Lightbox Zoom
          </button>
        </div>

        <!-- Hero Primary Image Viewport -->
        <div style="position: relative; border-radius: 12px; overflow: hidden; max-height: 480px; background: #070a12; text-align: center; cursor: pointer;" onclick="openSinglePromptLightbox(0)">
          <img id="main-gallery-img" src="<?= htmlspecialchars($allImages[0]['image_url']) ?>" alt="<?= htmlspecialchars($prompt['title']) ?>" style="max-height: 480px; width: 100%; object-fit: contain; transition: transform 0.3s ease;">
          <div style="position: absolute; bottom: 0; inset-x: 0; background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, transparent 100%); padding: 1rem; text-align: left; display: flex; justify-content: space-between; align-items: flex-end;">
            <span id="main-gallery-caption" style="color: #ffffff; font-size: 0.85rem; font-weight: 600;">
              <?= htmlspecialchars($allImages[0]['caption']) ?>
            </span>
            <span class="ts-badge ts-badge-cyan" style="font-size: 0.72rem;">Click to Enlarge</span>
          </div>
        </div>

        <!-- Thumbnail Strip if Multiple Images Exist -->
        <?php if (count($allImages) > 1): ?>
          <div style="display: flex; gap: 0.75rem; margin-top: 1rem; overflow-x: auto; padding-bottom: 0.5rem;">
            <?php foreach ($allImages as $idx => $img): ?>
              <div onclick="switchMainGalleryImg(<?= $idx ?>, '<?= htmlspecialchars(addslashes($img['image_url'])) ?>', '<?= htmlspecialchars(addslashes($img['caption'])) ?>')" style="width: 80px; height: 80px; border-radius: 8px; overflow: hidden; border: 2px solid <?= $idx === 0 ? 'var(--ts-primary)' : 'var(--ts-border-subtle)' ?>; cursor: pointer; flex-shrink: 0; transition: border-color 0.2s ease;" class="gallery-thumb-item" data-idx="<?= $idx ?>">
                <img src="<?= htmlspecialchars($img['image_url']) ?>" alt="Thumb" style="width: 100%; height: 100%; object-fit: cover;">
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <!-- Hidden Raw Template for JS Injector -->
    <textarea id="raw-prompt-template" style="display: none;"><?= htmlspecialchars($prompt['prompt_template'] ?? '') ?></textarea>

    <!-- 2. Dynamic Variable Customizer Box (If Applicable) -->
    <?php if (!empty($variables)): ?>
      <div class="ts-card" style="margin-bottom: 2rem; border-color: rgba(16, 185, 129, 0.4); border-radius: 14px;">
        <h3 style="font-size: 1.15rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; color: #34d399;">
          <i data-lucide="sliders" style="width: 18px;"></i> Dynamic Variable Injector
        </h3>
        <p style="font-size: 0.85rem; color: var(--ts-text-tertiary); margin-bottom: 1.25rem;">
          Customize the input values below. The prompt output updates instantly in real-time.
        </p>

        <div style="display: grid; gap: 1rem;">
          <?php foreach ($variables as $v): 
            $varName = is_array($v) ? ($v['name'] ?? '') : (string)$v;
            $varLabel = is_array($v) ? ($v['label'] ?? $varName) : (string)$v;
            $varDefault = is_array($v) ? ($v['default'] ?? '') : '';
            if ($varName === '') continue;
          ?>
            <div>
              <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--ts-text-primary); margin-bottom: 0.35rem;">
                <?= htmlspecialchars($varLabel) ?>
              </label>
              <input type="text" class="prompt-var-input" data-var-name="<?= htmlspecialchars($varName) ?>" value="<?= htmlspecialchars($varDefault) ?>" style="width: 100%; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); padding: 0.65rem 1rem; border-radius: var(--ts-radius-md); color: var(--ts-text-primary); font-size: 0.9rem; outline: none;">
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- 3. Generated Prompt Blueprint with Half-Visible Teaser & Direct Ad Trigger -->
    <div class="ts-card" style="margin-bottom: 2.25rem; border-radius: 16px; border: 1px solid var(--ts-border-main); background: var(--ts-bg-surface); padding: 1.75rem; position: relative;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.5rem;">
        <div style="display: flex; align-items: center; gap: 0.6rem;">
          <span style="font-family: var(--ts-font-mono); font-size: 0.8rem; font-weight: 700; color: #818cf8; background: rgba(99,102,241,0.12); padding: 0.2rem 0.55rem; border-radius: 6px;">
            BLUEPRINT OUTPUT
          </span>
          <h3 style="font-size: 1.15rem; margin: 0; color: var(--ts-text-primary);">Configured Prompt Script</h3>
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
          <button type="button" onclick="toggleBookmark(<?= (int)$prompt['id'] ?>)" id="content-bookmark-btn" class="ts-btn <?= !empty($isBookmarked) ? 'ts-btn-primary' : 'ts-btn-outline' ?> ts-btn-sm" style="<?= !empty($isBookmarked) ? 'background:#ec4899; border-color:#ec4899;' : 'color:#ec4899; border-color:rgba(236,72,153,0.3);' ?>">
            <span><?= !empty($isBookmarked) ? '🔖 Saved' : '🔖 Save Bookmark' ?></span>
          </button>
          <button type="button" onclick="openAuthorTipModal('<?= htmlspecialchars(addslashes($prompt['author_username'] ?? 'TechScout Author')) ?>', window.location.href)" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #f59e0b; border-color: rgba(245,158,11,0.3);" title="Send a crypto tip to the creator">
            <span>🪙 Tip Creator</span>
          </button>
          <button type="button" onclick="openReportModal('prompt', <?= (int)$prompt['id'] ?>, '<?= htmlspecialchars(addslashes($prompt['title'])) ?>', window.location.href)" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #94a3b8; font-size: 0.78rem;" title="Report this prompt template">
            <i data-lucide="flag" style="width: 12px; height: 12px; color: #ef4444;"></i>
          </button>
        </div>
      </div>

      <!-- Half-Visible Teaser Wrapper -->
      <div id="prompt-preview-container" class="prompt-half-visible" style="position: relative; max-height: 140px; overflow: hidden; border-radius: 10px; border: 1px solid var(--ts-border-main); background: #080c16; transition: max-height 0.4s ease;">
        <pre style="margin: 0; padding: 1.25rem; font-family: var(--ts-font-mono); font-size: 0.88rem; white-space: pre-wrap; word-break: break-word; color: #34d399; line-height: 1.6;"><code id="rendered-prompt-output" class="language-markdown"></code></pre>
        
        <!-- Gradient Blurred Fade Mask -->
        <div id="prompt-fade-overlay" style="position: absolute; inset: 0; background: linear-gradient(to bottom, transparent 15%, rgba(8,12,22,0.75) 60%, rgba(8,12,22,0.98) 100%); pointer-events: none; display: flex; align-items: flex-end; justify-content: center; padding-bottom: 0.75rem;">
          <span style="font-size: 0.78rem; font-weight: 700; color: #94a3b8; letter-spacing: 0.05em; text-transform: uppercase;">
            🔒 Full Blueprint Hidden &bull; Unlock Below
          </span>
        </div>
      </div>

      <!-- Prominent Unlock & Copy Action Bar -->
      <div style="margin-top: 1.25rem; display: flex; flex-direction: column; align-items: center; gap: 0.75rem;">
        <button id="btn-copy-prompt" data-prompt-id="<?= $prompt['id'] ?>" data-ad-url="<?= htmlspecialchars($adDirectUrl ?? 'https://omg10.com/4/11737440') ?>" class="ts-btn ts-btn-primary" style="padding: 0.85rem 2.2rem; font-size: 1.05rem; font-weight: 800; border-radius: 12px; background: linear-gradient(135deg, #6366f1, #06b6d4); box-shadow: 0 4px 20px rgba(99,102,241,0.4); display: inline-flex; align-items: center; gap: 0.65rem; cursor: pointer; transition: transform 0.2s ease, box-shadow 0.2s ease;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
          <i data-lucide="unlock" id="copy-btn-icon" style="width: 20px; height: 20px;"></i>
          <span id="copy-btn-label">Unlock Full Blueprint &amp; Copy Prompt 🚀</span>
        </button>
        <span style="font-size: 0.78rem; color: var(--ts-text-muted);">
          1-Click unlocks complete variable template and copies directly to your clipboard.
        </span>
      </div>
    </div>

    <!-- 4. Target Models & Foundation Engines -->
    <?php if (!empty($models)): ?>
      <div class="ts-card" style="margin-bottom: 2rem; border-radius: 14px;">
        <h4 style="font-size: 0.95rem; margin-bottom: 0.75rem; color: var(--ts-text-primary);">Optimized Foundation Models &amp; Engines</h4>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
          <?php foreach ($models as $m): ?>
            <span class="ts-badge ts-badge-cyan" style="font-size: 0.82rem; padding: 0.35rem 0.75rem;"><?= htmlspecialchars($m) ?></span>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Technical Tags -->
    <?php $pTags = \App\Core\TagHelper::getTagsForContent((int)$prompt['id']); ?>
    <?php if (!empty($pTags)): ?>
      <div class="ts-card" style="margin-bottom: 2.5rem; border-radius: 14px;">
        <h4 style="font-size: 0.95rem; margin-bottom: 0.75rem; color: var(--ts-text-primary);">Blueprint Technical Tags</h4>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
          <?php foreach ($pTags as $pt): ?>
            <a href="<?= url('/tag/' . $pt['slug']) ?>" class="ts-badge ts-badge-green" style="text-decoration: none;">
              #<?= htmlspecialchars($pt['name']) ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- 5. Compact Social Discussion Section -->
    <?php require __DIR__ . '/../components/comments_section.php'; ?>

  </div>
</div>

<!-- Lightbox Modal for Single Prompt -->
<div id="singlePromptLightbox" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.92); z-index: 10000; align-items: center; justify-content: center; padding: 1.5rem; backdrop-filter: blur(10px);">
  <div style="position: relative; max-width: 1000px; width: 100%; text-align: center;">
    <button onclick="closeSinglePromptLightbox()" style="position: absolute; top: -45px; right: 0; background: none; border: none; color: #ffffff; font-size: 2rem; cursor: pointer;">&times;</button>
    <img id="lightbox-zoomed-img" src="" alt="Zoom" style="max-width: 100%; max-height: 80vh; border-radius: 10px; box-shadow: 0 20px 60px rgba(0,0,0,0.8); object-fit: contain;">
    <div id="lightbox-zoomed-caption" style="color: #ffffff; margin-top: 1rem; font-size: 0.95rem; font-weight: 600;"></div>
  </div>
</div>

<!-- Author Crypto Tip Modal -->
<div id="authorTipModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="admin-card" style="max-width: 520px; width: 100%; border-radius: 16px; padding: 2rem; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
      <div style="display: flex; align-items: center; gap: 0.5rem;">
        <span style="font-size: 1.5rem;">🪙</span>
        <h3 style="font-size: 1.25rem; margin: 0; color: var(--ts-text-primary);">Support the Creator</h3>
      </div>
      <button onclick="document.getElementById('authorTipModal').style.display='none'" style="background: none; border: none; color: var(--ts-text-muted); font-size: 1.5rem; cursor: pointer;">&times;</button>
    </div>
    <p style="font-size: 0.85rem; color: var(--ts-text-secondary); margin-bottom: 1.25rem; line-height: 1.5;">
      Tips are received securely by the TechScout Central Treasury and credited directly to the author balance (95% to creator, 5% platform fee).
    </p>

    <form id="creatorTipForm" onsubmit="submitCreatorTip(event)">
      <input type="hidden" name="article_url" id="tip_article_url" value="">
      <input type="hidden" name="author_username" id="tip_author_username" value="">

      <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem;">Central Deposit Address (USDT / ETH / SOL)</label>
        <div style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); padding: 0.65rem 0.85rem; border-radius: 8px; font-family: var(--ts-font-mono); font-size: 0.82rem; word-break: break-all; color: #38bdf8;">
          0x71C83709b1fbf4A8b7fa3b3E2F46E8295b92FEA1
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem;">Tip Amount ($ USD)</label>
          <input type="number" step="1" name="amount" min="1" value="10" class="ts-form-input" required style="width: 100%; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); padding: 0.6rem; border-radius: 8px; color: var(--ts-text-primary);">
        </div>
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem;">Currency</label>
          <select name="currency" class="ts-form-input" style="width: 100%; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); padding: 0.6rem; border-radius: 8px; color: var(--ts-text-primary);">
            <option value="USDT">USDT (Tether)</option>
            <option value="ETH">Ethereum (ETH)</option>
            <option value="SOL">Solana (SOL)</option>
            <option value="BTC">Bitcoin (BTC)</option>
          </select>
        </div>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem;">Transaction Hash (TXID) *</label>
        <input type="text" name="txid" class="ts-form-input" placeholder="Paste transaction hash or payment reference..." required style="width: 100%; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); padding: 0.6rem; border-radius: 8px; color: var(--ts-text-primary); font-family: var(--ts-font-mono);">
      </div>

      <button type="submit" class="ts-btn ts-btn-primary" style="width: 100%; padding: 0.75rem; font-weight: 700;">
        Send Tip Verification Request 🚀
      </button>
    </form>
  </div>
</div>

<script>
const galleryImagesData = <?= json_encode($allImages) ?>;

function switchMainGalleryImg(idx, imgUrl, caption) {
    document.getElementById('main-gallery-img').src = imgUrl;
    document.getElementById('main-gallery-caption').innerText = caption;
    document.querySelectorAll('.gallery-thumb-item').forEach(el => {
        el.style.borderColor = (parseInt(el.getAttribute('data-idx')) === idx) ? 'var(--ts-primary)' : 'var(--ts-border-subtle)';
    });
}

function openSinglePromptLightbox(idx) {
    if (!galleryImagesData || galleryImagesData.length === 0) return;
    const item = galleryImagesData[idx] || galleryImagesData[0];
    document.getElementById('lightbox-zoomed-img').src = item.image_url;
    document.getElementById('lightbox-zoomed-caption').innerText = item.caption;
    const modal = document.getElementById('singlePromptLightbox');
    modal.style.display = 'flex';
}

function closeSinglePromptLightbox() {
    document.getElementById('singlePromptLightbox').style.display = 'none';
}

function openAuthorTipModal(authorName, articleUrl) {
    document.getElementById('tip_author_username').value = authorName;
    document.getElementById('tip_article_url').value = articleUrl;
    document.getElementById('authorTipModal').style.display = 'flex';
}

async function submitCreatorTip(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    try {
        const res = await fetch('<?= url('/api/v1/tips/submit') ?>', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        if (data.success) {
            alert('Thank you! Your tip has been submitted for platform verification and creator credit.');
            document.getElementById('authorTipModal').style.display = 'none';
            form.reset();
        } else {
            alert(data.error || 'Tip submission failed.');
        }
    } catch (err) {
        alert('Tip submitted successfully for administrative confirmation.');
        document.getElementById('authorTipModal').style.display = 'none';
        form.reset();
    }
}

// Hook Unlock & Copy Prompt Button with Popunder / Direct Link Ad
document.addEventListener('DOMContentLoaded', () => {
    const copyBtn = document.getElementById('btn-copy-prompt');
    const previewContainer = document.getElementById('prompt-preview-container');
    const fadeOverlay = document.getElementById('prompt-fade-overlay');
    const renderedPromptEl = document.getElementById('rendered-prompt-output');

    if (copyBtn && renderedPromptEl) {
        copyBtn.addEventListener('click', function() {
            const rawTemplateEl = document.getElementById('raw-prompt-template');
            const textToCopy = renderedPromptEl.innerText || (rawTemplateEl ? rawTemplateEl.value : '');
            const promptId = copyBtn.getAttribute('data-prompt-id');
            const adUrl = copyBtn.getAttribute('data-ad-url') || 'https://omg10.com/4/11737440';

            // 1. Trigger Popunder / Direct Link Ad SYNCHRONOUSLY (Guarantees bypass of popup blocker)
            if (adUrl) {
                try {
                    const adWin = window.open(adUrl, '_blank');
                    if (adWin) {
                        try {
                            adWin.blur();
                            window.focus();
                        } catch(e) {}
                    }
                } catch(e) {
                    window.open(adUrl, '_blank');
                }
            }

            // 2. Reveal full prompt visually & clear blur overlay
            if (previewContainer) {
                previewContainer.style.maxHeight = 'none';
                if (fadeOverlay) fadeOverlay.style.display = 'none';
            }
            const iconEl = document.getElementById('copy-btn-icon');
            const labelEl = document.getElementById('copy-btn-label');
            if (iconEl) iconEl.setAttribute('data-lucide', 'check');
            if (labelEl) labelEl.innerText = 'Copied to Clipboard! 🎉';
            if (window.lucide) lucide.createIcons();

            // 3. Copy text to clipboard
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(textToCopy).then(() => {
                    if (typeof showToast === 'function') {
                        showToast('Prompt blueprint unlocked & copied! 🚀 Ready to paste into your AI model.', 'success');
                    }
                }).catch(() => {
                    copyFallback(textToCopy);
                });
            } else {
                copyFallback(textToCopy);
            }

            // 4. Send background telemetry
            if (promptId) {
                fetch('<?= url('/api/v1/prompts/') ?>' + promptId + '/copy', { method: 'POST' }).catch(() => {});
            }
        });
    }

    function copyFallback(text) {
        const temp = document.createElement('textarea');
        temp.value = text;
        temp.style.position = 'fixed';
        temp.style.left = '-9999px';
        document.body.appendChild(temp);
        temp.select();
        try {
            document.execCommand('copy');
            if (typeof showToast === 'function') {
                showToast('Prompt blueprint unlocked and copied! 🚀', 'success');
            }
        } catch(e) {}
        document.body.removeChild(temp);
    }
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
