<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
$isImageEngine = (($prompt['prompt_type'] ?? '') === 'image');
$notes = json_decode($prompt['internal_notes'] ?? '{}', true) ?: [];

$allImages = [];
$featuredImage = !empty($prompt['featured_image_url']) ? image_url($prompt['featured_image_url']) : '';
if (!empty($featuredImage) && $featuredImage !== 'https://funwithcoding.free.nf/' && $featuredImage !== url('/')) {
    $allImages[] = ['image_url' => $featuredImage, 'caption' => 'Master Generation / Architecture Sample'];
}
if (!empty($galleryImages)) {
    foreach ($galleryImages as $gi) {
        $giUrl = image_url($gi['image_url']);
        if ($giUrl !== $featuredImage) {
            $allImages[] = ['image_url' => $giUrl, 'caption' => $gi['caption'] ?? 'Visual Reference Sample'];
        }
    }
}
$hasGallery = !empty($allImages);
$adUrl = $adDirectUrl ?? 'https://omg10.com/4/11737440';

// Related Prompts in this category
$relatedPrompts = [];
try {
    $relatedPrompts = \App\Models\Content::getFeed('prompt', $prompt['category_slug'] ?? null, 'popular', 4, 0, [
        'prompt_kind' => $isImageEngine ? 'image' : 'text'
    ]);
    $relatedPrompts = array_values(array_filter($relatedPrompts, fn($rp) => (int)$rp['id'] !== (int)$prompt['id']));
    $relatedPrompts = array_slice($relatedPrompts, 0, 3);
} catch (\Throwable $e) {}
?>

<div class="container" style="padding-top: 2rem; margin-bottom: 5rem;">
  <div style="max-width: 960px; margin: 0 auto;">

    <!-- Category & Subcategory Breadcrumbs Hierarchy -->
    <div style="font-size: 0.85rem; color: var(--ts-text-muted); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.45rem; flex-wrap: wrap;">
      <a href="<?= url('/prompts') ?>" style="color: var(--ts-text-secondary); text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;" class="ts-breadcrumb-link">
        <i data-lucide="terminal" style="width: 14px; height: 14px; color: var(--ts-primary);"></i>
        <span>Prompts Hub</span>
      </a>
      <span>&rsaquo;</span>
      <?php if (!empty($prompt['parent_category_name']) && !empty($prompt['parent_category_slug'])): ?>
        <?php 
          $parentUrl = ($prompt['parent_category_slug'] === 'ai-image-prompts') 
            ? url('/prompts/images') 
            : url('/prompts?category=' . urlencode($prompt['parent_category_slug']));
        ?>
        <a href="<?= $parentUrl ?>" style="color: var(--ts-text-secondary); text-decoration: none;" class="ts-breadcrumb-link">
          <?= htmlspecialchars($prompt['parent_category_name']) ?>
        </a>
        <span>&rsaquo;</span>
      <?php endif; ?>
      <a href="<?= url('/prompts?category=' . urlencode($prompt['category_slug'] ?? '')) ?>" style="color: var(--ts-primary); font-weight: 700; text-decoration: none;" class="ts-breadcrumb-link">
        <?= htmlspecialchars($prompt['category_name'] ?? 'Library') ?>
      </a>
      <span>&rsaquo;</span>
      <span style="color: var(--ts-text-primary); font-weight: 700;"><?= htmlspecialchars($prompt['title']) ?></span>
    </div>

    <!-- Prompt Header -->
    <div style="margin-bottom: 2rem;">
      <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap;">
        <span class="ts-badge <?= $isImageEngine ? 'ts-badge-pink' : 'ts-badge-green' ?>" style="font-weight: 800;">
          <?= $isImageEngine ? '🎨 Visual Generative Blueprint' : '⚡ System / LLM Prompt Blueprint' ?>
        </span>
        <a href="<?= url('/prompts?category=' . urlencode($prompt['category_slug'] ?? '')) ?>" class="ts-badge ts-badge-cyan" style="text-decoration: none;">
          <?= htmlspecialchars($prompt['category_name'] ?? 'AI Prompt') ?>
        </a>
        <?php if (!empty($prompt['custom_badge'])): ?>
          <span class="ts-badge ts-badge-amber"><?= htmlspecialchars($prompt['custom_badge']) ?></span>
        <?php endif; ?>
      </div>

      <h1 style="font-size: clamp(1.85rem, 3.5vw, 2.5rem); margin-bottom: 0.6rem; line-height: 1.25; font-family: 'Outfit', sans-serif; font-weight: 800; color: var(--ts-text-primary);">
        <?= htmlspecialchars($prompt['title']) ?>
      </h1>

      <div style="font-size: 0.82rem; color: var(--ts-text-tertiary); margin-bottom: 1rem; display: flex; gap: 0.85rem; flex-wrap: wrap; align-items: center;">
        <span>Published <?= \App\Core\DateHelper::render($prompt['created_at'] ?? 'now', 'M j, Y • g:i A', 'datetime') ?></span>
        <?php if (!empty($prompt['updated_at']) && strtotime($prompt['updated_at']) > strtotime($prompt['created_at'] ?? 'now') + 60): ?>
          <span style="color: #38bdf8;">&bull; Updated <?= \App\Core\DateHelper::render($prompt['updated_at'], 'M j, Y • g:i A', 'datetime') ?></span>
        <?php endif; ?>
        <span>&bull; <i data-lucide="copy" style="width: 12px; height: 12px; display: inline;"></i> <?= number_format((int)($prompt['copy_count'] ?? 0)) ?> copies</span>
        <?php if (!empty($prompt['quality_score'])): ?>
          <span style="color: #10b981; font-weight: 700;">&bull; <?= number_format((float)$prompt['quality_score'], 1) ?>% Quality Benchmark</span>
        <?php endif; ?>
      </div>

      <p style="color: var(--ts-text-secondary); font-size: 1.05rem; line-height: 1.6; margin: 0;">
        <?= htmlspecialchars($prompt['summary']) ?>
      </p>
    </div>

    <!-- 1. Visual Showcase & Image Lightbox (Universal Multi-Image Gallery) -->
    <?php if ($hasGallery): ?>
      <div class="ts-card" style="margin-bottom: 2.25rem; padding: 1.25rem; border-radius: 16px; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
          <div style="font-weight: 700; font-size: 0.95rem; color: var(--ts-text-primary); display: flex; align-items: center; gap: 0.5rem;">
            <i data-lucide="image" style="width: 18px; height: 18px; color: #ec4899;"></i>
            <span><?= $isImageEngine ? 'Visual Render Samples' : 'Architecture & Workflow Visuals' ?> (<?= count($allImages) ?> <?= count($allImages) === 1 ? 'Asset' : 'Assets' ?>)</span>
          </div>
          <button type="button" class="ts-btn ts-btn-outline ts-btn-sm" onclick="handleShowcaseImageClick(0)">
            <i data-lucide="maximize-2" style="width: 13px; height: 13px;"></i> Lightbox Zoom
          </button>
        </div>

        <!-- Hero Primary Image Viewport: Clicking triggers Popunder Ad AND Lightbox -->
        <div style="position: relative; border-radius: 12px; overflow: hidden; max-height: 520px; background: #070a12; text-align: center; cursor: pointer;" onclick="handleShowcaseImageClick(0)">
          <img id="main-gallery-img" src="<?= htmlspecialchars($allImages[0]['image_url']) ?>" alt="<?= htmlspecialchars($prompt['title']) ?>" style="max-height: 520px; width: 100%; object-fit: contain; transition: transform 0.35s ease;">
          <div style="position: absolute; bottom: 0; inset-x: 0; background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, transparent 100%); padding: 1rem; text-align: left; display: flex; justify-content: space-between; align-items: flex-end;">
            <span id="main-gallery-caption" style="color: #ffffff; font-size: 0.85rem; font-weight: 600;">
              <?= htmlspecialchars($allImages[0]['caption']) ?>
            </span>
            <span class="ts-badge ts-badge-cyan" style="font-size: 0.72rem; backdrop-filter: blur(8px);">
              🔍 Click to Enlarge (HD)
            </span>
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

    <!-- 2. Generation & Hyperparameters HUD -->
    <div class="ts-card" style="margin-bottom: 2rem; border-radius: 14px; padding: 1.25rem 1.5rem; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main);">
      <div style="font-weight: 700; font-size: 0.92rem; color: var(--ts-text-primary); margin-bottom: 0.85rem; display: flex; align-items: center; gap: 0.45rem;">
        <i data-lucide="sliders" style="width: 16px; height: 16px; color: var(--ts-primary);"></i>
        <span>Blueprint Parameters &amp; Engine Metadata</span>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
        <!-- Models -->
        <div>
          <span style="font-size: 0.75rem; color: var(--ts-text-tertiary); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 0.35rem;">Target Engine(s)</span>
          <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
            <?php foreach ($models as $m): ?>
              <span class="ts-badge <?= $isImageEngine ? 'ts-badge-pink' : 'ts-badge-cyan' ?>" style="font-size: 0.78rem;"><?= htmlspecialchars($m) ?></span>
            <?php endforeach; ?>
          </div>
        </div>

        <?php if ($isImageEngine): ?>
          <!-- Aspect Ratio -->
          <div>
            <span style="font-size: 0.75rem; color: var(--ts-text-tertiary); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 0.35rem;">Aspect Ratio</span>
            <span class="ts-badge ts-badge-indigo" style="font-family: var(--ts-font-mono); font-size: 0.8rem;">
              --ar <?= htmlspecialchars($notes['aspect_ratio'] ?? '16:9') ?>
            </span>
          </div>

          <!-- Negative Prompt -->
          <?php if (!empty($notes['negative_prompt'])): ?>
            <div style="grid-column: 1 / -1; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); padding: 0.75rem 1rem; border-radius: 8px;">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.3rem;">
                <span style="font-size: 0.75rem; color: #ec4899; font-weight: 700;">Negative Prompt (--no parameters):</span>
                <button type="button" onclick="copyTextDirect('<?= htmlspecialchars(addslashes($notes['negative_prompt'])) ?>')" class="ts-btn ts-btn-sm ts-btn-outline" style="padding: 0.15rem 0.45rem; font-size: 0.7rem;">
                  📋 Copy
                </button>
              </div>
              <code style="color: var(--ts-text-secondary); font-size: 0.82rem; word-break: break-all;"><?= htmlspecialchars($notes['negative_prompt']) ?></code>
            </div>
          <?php endif; ?>

        <?php else: ?>
          <!-- LLM Parameters -->
          <div>
            <span style="font-size: 0.75rem; color: var(--ts-text-tertiary); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 0.35rem;">Sampling Temperature</span>
            <span style="font-family: var(--ts-font-mono); font-weight: 700; color: #38bdf8; font-size: 0.88rem;">
              <?= number_format((float)($notes['temperature'] ?? 0.7), 2) ?>
            </span>
          </div>
          <div>
            <span style="font-size: 0.75rem; color: var(--ts-text-tertiary); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 0.35rem;">Top_P Sampling</span>
            <span style="font-family: var(--ts-font-mono); font-weight: 700; color: #10b981; font-size: 0.88rem;">
              <?= number_format((float)($notes['top_p'] ?? 0.95), 2) ?>
            </span>
          </div>
          <div>
            <span style="font-size: 0.75rem; color: var(--ts-text-tertiary); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 0.35rem;">Complexity Tier</span>
            <span class="ts-badge ts-badge-green" style="font-size: 0.78rem;">
              <?= htmlspecialchars($notes['difficulty'] ?? 'Production') ?>
            </span>
          </div>
        <?php endif; ?>

        <?php if (!empty($notes['custom_links']) && is_array($notes['custom_links'])): ?>
          <div style="grid-column: 1 / -1; padding-top: 0.75rem; border-top: 1px solid var(--ts-border-subtle); display: flex; gap: 0.6rem; flex-wrap: wrap; align-items: center;">
            <span style="font-size: 0.75rem; color: var(--ts-text-tertiary); text-transform: uppercase; font-weight: 700;">Additional Links:</span>
            <?php foreach ($notes['custom_links'] as $cLink): ?>
              <?php if (!empty($cLink['name']) && !empty($cLink['url'])): ?>
                <a href="<?= htmlspecialchars($cLink['url']) ?>" target="_blank" rel="noopener noreferrer" class="ts-badge ts-badge-cyan" style="text-decoration: none; font-size: 0.78rem;">
                  <i data-lucide="link-2" style="width: 11px; height: 11px; display: inline-block; vertical-align: -1px;"></i> <?= htmlspecialchars($cLink['name']) ?> ↗
                </a>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Hidden Raw Template for JS Injector -->
    <textarea id="raw-prompt-template" style="display: none;"><?= htmlspecialchars($prompt['prompt_template'] ?? '') ?></textarea>

    <!-- 3. Dynamic Variable Customizer Box (If Applicable) -->
    <?php if (!empty($variables)): ?>
      <div class="ts-card" style="margin-bottom: 2rem; border-color: rgba(16, 185, 129, 0.4); border-radius: 14px;">
        <h3 style="font-size: 1.15rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; color: #34d399;">
          <i data-lucide="sliders" style="width: 18px;"></i> Dynamic Variable Injector
        </h3>
        <p style="font-size: 0.85rem; color: var(--ts-text-tertiary); margin-bottom: 1.25rem;">
          Customize the input values below. The prompt output updates instantly in real-time.
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
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

    <!-- Tab Header: Blueprint Script vs. Creator Crypto Tip Jar -->
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--ts-border-main); padding-bottom: 0.5rem; flex-wrap: wrap; align-items: center; justify-content: space-between;">
      <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <button type="button" id="tab-btn-blueprint" onclick="switchPromptPane('blueprint')" class="ts-btn ts-btn-primary ts-btn-sm" style="font-weight: 700;">
          <i data-lucide="terminal" style="width: 14px; height: 14px;"></i>
          <span>Blueprint Script</span>
        </button>
        <button type="button" id="tab-btn-tipjar" onclick="switchPromptPane('tipjar')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-weight: 700; color: #f59e0b; border-color: rgba(245,158,11,0.35);">
          <i data-lucide="coins" style="width: 14px; height: 14px;"></i>
          <span>⚡ Creator Crypto Tip Jar</span>
        </button>
      </div>

      <div style="display: flex; gap: 0.4rem; align-items: center;">
        <button type="button" onclick="toggleBookmark(<?= (int)$prompt['id'] ?>)" id="content-bookmark-btn" class="ts-btn <?= !empty($isBookmarked) ? 'ts-btn-primary' : 'ts-btn-outline' ?> ts-btn-sm" style="<?= !empty($isBookmarked) ? 'background:#ec4899; border-color:#ec4899;' : 'color:#ec4899; border-color:rgba(236,72,153,0.3);' ?>">
          <span><?= !empty($isBookmarked) ? '🔖 Saved' : '🔖 Save' ?></span>
        </button>
        <button type="button" onclick="switchPromptPane('tipjar');" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #f59e0b; border-color: rgba(245,158,11,0.3);" title="Send a crypto tip to the creator">
          <span>🪙 Tip Creator</span>
        </button>
        <button type="button" onclick="openReportModal('prompt', <?= (int)$prompt['id'] ?>, '<?= htmlspecialchars(addslashes($prompt['title'])) ?>', window.location.href)" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #94a3b8; font-size: 0.78rem;" title="Report this prompt template">
          <i data-lucide="flag" style="width: 12px; height: 12px; color: #ef4444;"></i>
        </button>
      </div>
    </div>

    <!-- PANE 1: Generated Prompt Blueprint with Half-Visible Teaser & Direct Ad Trigger -->
    <div id="pane-blueprint" class="ts-card" style="margin-bottom: 2.25rem; border-radius: 16px; border: 1px solid var(--ts-border-main); background: var(--ts-bg-surface); padding: 1.75rem; position: relative;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.5rem;">
        <div style="display: flex; align-items: center; gap: 0.6rem;">
          <span style="font-family: var(--ts-font-mono); font-size: 0.8rem; font-weight: 700; color: #818cf8; background: rgba(99,102,241,0.12); padding: 0.2rem 0.55rem; border-radius: 6px;">
            BLUEPRINT OUTPUT
          </span>
          <h3 style="font-size: 1.15rem; margin: 0; color: var(--ts-text-primary);">Configured Prompt Script</h3>
        </div>
        <span class="ts-badge ts-badge-cyan" style="font-size: 0.72rem;">Live Engine Ready</span>
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
        <button id="btn-copy-prompt" data-prompt-id="<?= $prompt['id'] ?>" data-ad-url="<?= htmlspecialchars($adUrl) ?>" class="ts-btn ts-btn-primary" style="padding: 0.85rem 2.2rem; font-size: 1.05rem; font-weight: 800; border-radius: 12px; background: linear-gradient(135deg, #6366f1, #06b6d4); box-shadow: 0 4px 20px rgba(99,102,241,0.4); display: inline-flex; align-items: center; gap: 0.65rem; cursor: pointer; transition: transform 0.2s ease, box-shadow 0.2s ease;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
          <i data-lucide="unlock" id="copy-btn-icon" style="width: 20px; height: 20px;"></i>
          <span id="copy-btn-label">Unlock Full Blueprint &amp; Copy Prompt 🚀</span>
        </button>
        <span style="font-size: 0.78rem; color: var(--ts-text-muted);">
          1-Click unlocks complete variable template, triggers direct launcher, and copies to clipboard.
        </span>
      </div>
    </div>

    <!-- PANE 2: 1000x Enhanced Creator Crypto Tip Jar & Direct Patronage -->
    <div id="pane-tipjar" class="ts-card" style="display: none; margin-bottom: 2.25rem; border-radius: 16px; border: 1px solid rgba(245,158,11,0.3); background: var(--ts-bg-surface); padding: 1.75rem;">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; gap: 1rem; align-items: center;">
          <div style="width: 54px; height: 54px; border-radius: 50%; overflow: hidden; border: 2px solid #f59e0b; flex-shrink: 0; background: #0c1222;">
            <img src="<?= htmlspecialchars(image_url($prompt['author_avatar'] ?? '/assets/images/default-avatar.png')) ?>" alt="<?= htmlspecialchars($prompt['author_name'] ?? 'Creator') ?>" style="width: 100%; height: 100%; object-fit: cover;">
          </div>
          <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
              <h3 style="font-size: 1.25rem; margin: 0; color: var(--ts-text-primary);">
                <?= htmlspecialchars($prompt['author_name'] ?? $prompt['author_username'] ?? 'TechScout Contributor') ?>
              </h3>
              <span class="ts-badge ts-badge-amber" style="font-size: 0.72rem; font-weight: 700;">
                ⚡ 95% Creator Share &bull; 5% Platform
              </span>
            </div>
            <p style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: var(--ts-text-secondary); line-height: 1.4;">
              <?= htmlspecialchars($prompt['author_headline'] ?? 'Autonomous Engineering Research & Generative Blueprints Contributor') ?>
            </p>
          </div>
        </div>

        <a href="<?= url('/author/profile/' . ($prompt['author_slug'] ?? $prompt['author_username'] ?? '')) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.35);">
          View Full Creator Profile &rarr;
        </a>
      </div>

      <!-- Currency Network Selector -->
      <div style="margin-bottom: 1.5rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--ts-text-primary); margin-bottom: 0.6rem;">
          Select Payout Network &amp; Cryptocurrency:
        </label>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 0.6rem;">
          <button type="button" onclick="switchTipCurrency('USDT')" id="tip-curr-btn-USDT" class="tip-currency-tab-btn active" style="padding: 0.65rem; border-radius: 10px; border: 1px solid #10b981; background: rgba(16,185,129,0.12); color: #10b981; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.4rem;">
            <span>₮ USDT</span>
            <span style="font-size: 0.65rem; opacity: 0.85;">TRC/ERC</span>
          </button>
          <button type="button" onclick="switchTipCurrency('SOL')" id="tip-curr-btn-SOL" class="tip-currency-tab-btn" style="padding: 0.65rem; border-radius: 10px; border: 1px solid var(--ts-border-main); background: var(--ts-bg-surface-elevated); color: var(--ts-text-secondary); font-weight: 700; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.4rem;">
            <span>◎ SOL</span>
            <span style="font-size: 0.65rem; opacity: 0.85;">SPL</span>
          </button>
          <button type="button" onclick="switchTipCurrency('ETH')" id="tip-curr-btn-ETH" class="tip-currency-tab-btn" style="padding: 0.65rem; border-radius: 10px; border: 1px solid var(--ts-border-main); background: var(--ts-bg-surface-elevated); color: var(--ts-text-secondary); font-weight: 700; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.4rem;">
            <span>Ξ ETH</span>
            <span style="font-size: 0.65rem; opacity: 0.85;">Arbitrum/EVM</span>
          </button>
          <button type="button" onclick="switchTipCurrency('BTC')" id="tip-curr-btn-BTC" class="tip-currency-tab-btn" style="padding: 0.65rem; border-radius: 10px; border: 1px solid var(--ts-border-main); background: var(--ts-bg-surface-elevated); color: var(--ts-text-secondary); font-weight: 700; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.4rem;">
            <span>₿ BTC</span>
            <span style="font-size: 0.65rem; opacity: 0.85;">Native SegWit</span>
          </button>
        </div>
      </div>

      <!-- Live Wallet Address & QR Code Box -->
      <div style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem; display: flex; gap: 1.25rem; align-items: center; flex-wrap: wrap;">
        <!-- Dynamic QR Code -->
        <div style="background: #ffffff; padding: 0.5rem; border-radius: 8px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
          <img id="tip-qr-image" src="https://api.qrserver.com/v1/create-qr-code/?size=130x130&data=<?= urlencode($tipWallets['USDT'] ?? '0x71C8A60e46b38cE1241F8308D649372F7505a76E') ?>" alt="Crypto Tip QR" style="width: 110px; height: 110px; display: block;">
        </div>

        <div style="flex: 1; min-width: 240px;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
            <span id="tip-wallet-network-label" style="font-size: 0.78rem; font-weight: 700; color: #10b981; text-transform: uppercase;">
              USDT Deposit Address
            </span>
            <span style="font-size: 0.75rem; color: var(--ts-text-muted);">Verified Author Wallet</span>
          </div>
          
          <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.6rem;">
            <input type="text" id="tip-wallet-address-input" readonly value="<?= htmlspecialchars($tipWallets['USDT'] ?? '0x71C8A60e46b38cE1241F8308D649372F7505a76E') ?>" style="flex: 1; background: #060913; border: 1px solid var(--ts-border-main); border-radius: 8px; padding: 0.6rem 0.8rem; font-family: var(--ts-font-mono); font-size: 0.82rem; color: #38bdf8; font-weight: 700;">
            <button type="button" onclick="copyTipAddress()" class="ts-btn ts-btn-primary ts-btn-sm" style="padding: 0.6rem 1rem; font-weight: 700; white-space: nowrap;">
              <i data-lucide="copy" style="width: 14px; height: 14px;"></i> Copy
            </button>
          </div>

          <div style="font-size: 0.75rem; color: var(--ts-text-secondary); line-height: 1.4;">
            Send tips on-chain to this address. Funds are confirmed by network consensus and 95% is routed to the creator's withdrawable balance.
          </div>
        </div>
      </div>

      <!-- Preset Tip Chips & Split Simulator -->
      <div style="background: rgba(15,23,42,0.4); border: 1px solid var(--ts-border-main); border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem;">
        <div style="font-size: 0.82rem; font-weight: 700; color: var(--ts-text-primary); margin-bottom: 0.6rem;">
          Select or Enter Tip Amount ($ USD):
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1rem;">
          <button type="button" onclick="selectTipChip(5)" class="tip-chip-btn" style="padding: 0.4rem 0.85rem; border-radius: 8px; border: 1px solid var(--ts-border-main); background: var(--ts-bg-surface-elevated); color: var(--ts-text-primary); font-weight: 700; cursor: pointer;">$5</button>
          <button type="button" onclick="selectTipChip(10)" class="tip-chip-btn active" style="padding: 0.4rem 0.85rem; border-radius: 8px; border: 1px solid #f59e0b; background: rgba(245,158,11,0.15); color: #f59e0b; font-weight: 700; cursor: pointer;">$10</button>
          <button type="button" onclick="selectTipChip(25)" class="tip-chip-btn" style="padding: 0.4rem 0.85rem; border-radius: 8px; border: 1px solid var(--ts-border-main); background: var(--ts-bg-surface-elevated); color: var(--ts-text-primary); font-weight: 700; cursor: pointer;">$25</button>
          <button type="button" onclick="selectTipChip(50)" class="tip-chip-btn" style="padding: 0.4rem 0.85rem; border-radius: 8px; border: 1px solid var(--ts-border-main); background: var(--ts-bg-surface-elevated); color: var(--ts-text-primary); font-weight: 700; cursor: pointer;">$50</button>
          <button type="button" onclick="selectTipChip(100)" class="tip-chip-btn" style="padding: 0.4rem 0.85rem; border-radius: 8px; border: 1px solid var(--ts-border-main); background: var(--ts-bg-surface-elevated); color: var(--ts-text-primary); font-weight: 700; cursor: pointer;">$100</button>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.85rem; background: #060913; border-radius: 8px; padding: 0.85rem 1rem; border: 1px solid var(--ts-border-main); font-size: 0.82rem;">
          <div>
            <span style="color: var(--ts-text-muted);">Gross Reader Tip:</span>
            <div id="tip-breakdown-gross" style="font-weight: 800; font-size: 1.05rem; color: var(--ts-text-primary); font-family: var(--ts-font-mono);">$10.00</div>
          </div>
          <div>
            <span style="color: var(--ts-text-muted);">5% Relay &amp; Network Fee:</span>
            <div id="tip-breakdown-fee" style="font-weight: 700; font-size: 1.05rem; color: #f59e0b; font-family: var(--ts-font-mono);">-$0.50</div>
          </div>
          <div>
            <span style="color: var(--ts-text-muted);">95% Direct to Creator:</span>
            <div id="tip-breakdown-net" style="font-weight: 800; font-size: 1.05rem; color: #10b981; font-family: var(--ts-font-mono);">+$9.50</div>
          </div>
        </div>
      </div>

      <!-- On-Chain TXID Submission Form -->
      <form id="inlineCreatorTipForm" onsubmit="submitCreatorTipAjax(event, 'inlineCreatorTipForm')">
        <input type="hidden" name="author_id" value="<?= (int)($prompt['author_id'] ?? 0) ?>">
        <input type="hidden" name="author_username" value="<?= htmlspecialchars($prompt['author_username'] ?? '') ?>">
        <input type="hidden" name="article_url" value="<?= htmlspecialchars(url('/prompts/' . $prompt['slug'])) ?>">
        <input type="hidden" name="article_id" value="<?= (int)$prompt['id'] ?>">
        <input type="hidden" name="currency" id="tip-submit-currency" value="USDT">
        <input type="hidden" name="amount" id="tip-submit-amount" value="10">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--ts-text-primary);">
              Your Name / Handle (Optional)
            </label>
            <input type="text" name="sender_name" placeholder="e.g. SatoshiFan or Anonymous" class="ts-form-input" style="width: 100%; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); padding: 0.65rem 0.8rem; border-radius: 8px; color: var(--ts-text-primary);">
          </div>
          <div>
            <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--ts-text-primary);">
              Your Sending Wallet Address (Optional)
            </label>
            <input type="text" name="sender_wallet" placeholder="0x... or bc1q..." class="ts-form-input" style="width: 100%; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); padding: 0.65rem 0.8rem; border-radius: 8px; color: var(--ts-text-primary); font-family: var(--ts-font-mono); font-size: 0.82rem;">
          </div>
        </div>

        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--ts-text-primary);">
            Blockchain Transaction Hash (TXID) <span style="color: #ef4444;">*</span>
          </label>
          <input type="text" name="txid" required placeholder="Paste the on-chain TXID or transaction receipt hash here..." class="ts-form-input" style="width: 100%; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); padding: 0.65rem 0.8rem; border-radius: 8px; color: #38bdf8; font-family: var(--ts-font-mono); font-size: 0.85rem;">
          <div style="font-size: 0.75rem; color: var(--ts-text-muted); margin-top: 0.3rem;">
            Once you execute the transaction in your crypto wallet, copy the TXID hash and submit it here. Our treasury automated engine validates network confirmations and credits the creator.
          </div>
        </div>

        <div id="tip-submit-feedback" style="display: none; margin-bottom: 1.25rem; padding: 1rem; border-radius: 8px; font-size: 0.88rem; font-weight: 600;"></div>

        <button type="submit" id="tip-submit-btn" class="ts-btn ts-btn-primary" style="width: 100%; padding: 0.85rem 1.5rem; font-weight: 800; font-size: 1rem; background: linear-gradient(135deg, #f59e0b, #d97706); border: none; box-shadow: 0 4px 15px rgba(245,158,11,0.35); cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
          <i data-lucide="send" style="width: 16px; height: 16px;"></i>
          <span>Verify &amp; Confirm Patronage Tip 🚀</span>
        </button>
      </form>
    </div>

    <!-- Verified Model Response Sample (If available) -->
    <?php if (!empty($prompt['sample_output'])): ?>
      <div class="ts-card" style="margin-bottom: 2rem; border-radius: 14px; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main); padding: 1.25rem 1.5rem;">
        <h4 style="font-size: 0.95rem; margin-bottom: 0.6rem; color: var(--ts-text-primary); display: flex; align-items: center; gap: 0.4rem;">
          <i data-lucide="check-circle" style="width: 16px; height: 16px; color: #10b981;"></i>
          <span>Verified Engine Output Sample</span>
        </h4>
        <pre style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 8px; padding: 1rem; color: #38bdf8; font-family: var(--ts-font-mono); font-size: 0.84rem; white-space: pre-wrap; margin: 0; max-height: 250px; overflow-y: auto;"><?= htmlspecialchars($prompt['sample_output']) ?></pre>
      </div>
    <?php endif; ?>

    <!-- Technical Tags -->
    <?php $pTags = \App\Core\TagHelper::getTagsForContent((int)$prompt['id']); ?>
    <?php if (!empty($pTags)): ?>
      <div class="ts-card" style="margin-bottom: 2.5rem; border-radius: 14px; padding: 1rem 1.25rem;">
        <h4 style="font-size: 0.85rem; margin-bottom: 0.6rem; color: var(--ts-text-secondary); text-transform: uppercase; font-weight: 700;">Blueprint Tags</h4>
        <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
          <?php foreach ($pTags as $pt): ?>
            <a href="<?= url('/tag/' . $pt['slug']) ?>" class="ts-badge ts-badge-green" style="text-decoration: none;">
              #<?= htmlspecialchars($pt['name']) ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Related Prompts Grid Section -->
    <?php if (!empty($relatedPrompts)): ?>
      <div style="margin-bottom: 3rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
          <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0; color: var(--ts-text-primary);">
            <?= $isImageEngine ? '🎨 Related Visual Blueprints' : '⚡ Related Prompt Blueprints' ?>
          </h3>
          <a href="<?= $isImageEngine ? url('/prompts/images') : url('/prompts') ?>" style="font-size: 0.85rem; color: var(--ts-primary); text-decoration: none; font-weight: 600;">
            View All &rarr;
          </a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.25rem;">
          <?php foreach ($relatedPrompts as $rp): ?>
            <?php
              $rpIsImage = (($rp['prompt_type'] ?? '') === 'image');
              $rpImg = !empty($rp['featured_image_url']) ? image_url($rp['featured_image_url']) : '';
              $rpUrl = url('/prompts/' . $rp['slug']);
            ?>
            <?php if ($rpIsImage && !empty($rpImg) && $rpImg !== 'https://funwithcoding.free.nf/'): ?>
              <!-- Image Card -->
              <div onclick="openPromptWithAd(event, '<?= $rpUrl ?>', '<?= htmlspecialchars($adUrl) ?>')"
                   style="position: relative; height: 260px; border-radius: 12px; overflow: hidden; cursor: pointer; border: 1px solid var(--ts-border-main); background: #000; transition: transform 0.2s ease;">
                <img src="<?= htmlspecialchars($rpImg) ?>" alt="<?= htmlspecialchars($rp['title']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                <div style="position: absolute; inset-x: 0; bottom: 0; background: linear-gradient(to top, rgba(0,0,0,0.95), transparent); padding: 1rem;">
                  <span class="ts-badge ts-badge-pink" style="font-size: 0.65rem; margin-bottom: 0.25rem;"><?= htmlspecialchars($rp['category_name'] ?? 'Image') ?></span>
                  <div style="font-size: 0.85rem; font-weight: 700; color: #fff; line-height: 1.3; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    <?= htmlspecialchars($rp['title']) ?>
                  </div>
                </div>
              </div>
            <?php else: ?>
              <!-- Text Card -->
              <div class="ts-card" style="padding: 1rem; border-radius: 12px; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                  <span class="ts-badge ts-badge-green" style="font-size: 0.68rem; margin-bottom: 0.4rem;"><?= htmlspecialchars($rp['category_name'] ?? 'Prompt') ?></span>
                  <h4 style="font-size: 0.95rem; margin: 0 0 0.35rem 0; line-height: 1.3;">
                    <a href="<?= $rpUrl ?>" onclick="openPromptWithAd(event, '<?= $rpUrl ?>', '<?= htmlspecialchars($adUrl) ?>')" style="color: var(--ts-text-primary); text-decoration: none;">
                      <?= htmlspecialchars($rp['title']) ?>
                    </a>
                  </h4>
                </div>
                <div style="font-size: 0.75rem; color: var(--ts-text-tertiary); margin-top: 0.5rem;">
                  📋 <?= number_format((int)($rp['copy_count'] ?? 0)) ?> copies
                </div>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- 5. Social Discussion Section -->
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

<!-- Author Crypto Tip Modal (Fallback) -->
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

    <form id="creatorTipFormModal" onsubmit="submitCreatorTipAjax(event, 'creatorTipFormModal')">
      <input type="hidden" name="author_id" value="<?= (int)($prompt['author_id'] ?? 0) ?>">
      <input type="hidden" name="article_url" id="tip_article_url" value="">
      <input type="hidden" name="author_username" id="tip_author_username" value="">
      <input type="hidden" name="article_id" value="<?= (int)$prompt['id'] ?>">

      <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--ts-text-primary);">Central Deposit Address (USDT / ETH / SOL)</label>
        <div style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); padding: 0.65rem 0.85rem; border-radius: 8px; font-family: var(--ts-font-mono); font-size: 0.82rem; word-break: break-all; color: #38bdf8;">
          <?= htmlspecialchars($tipWallets['USDT'] ?? '0x71C8A60e46b38cE1241F8308D649372F7505a76E') ?>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--ts-text-primary);">Tip Amount ($ USD)</label>
          <input type="number" step="1" name="amount" min="1" value="10" class="ts-form-input" required style="width: 100%; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); padding: 0.6rem; border-radius: 8px; color: var(--ts-text-primary);">
        </div>
        <div>
          <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--ts-text-primary);">Currency</label>
          <select name="currency" class="ts-form-input" style="width: 100%; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); padding: 0.6rem; border-radius: 8px; color: var(--ts-text-primary);">
            <option value="USDT">USDT (Tether)</option>
            <option value="ETH">Ethereum (ETH)</option>
            <option value="SOL">Solana (SOL)</option>
            <option value="BTC">Bitcoin (BTC)</option>
          </select>
        </div>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--ts-text-primary);">Transaction Hash (TXID) *</label>
        <input type="text" name="txid" class="ts-form-input" placeholder="Paste transaction hash or payment reference..." required style="width: 100%; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); padding: 0.6rem; border-radius: 8px; color: var(--ts-text-primary); font-family: var(--ts-font-mono);">
      </div>

      <div id="tip-submit-feedback" style="display: none; margin-bottom: 1rem; padding: 0.75rem; border-radius: 8px; font-size: 0.84rem;"></div>

      <button type="submit" class="ts-btn ts-btn-primary" style="width: 100%; padding: 0.75rem; font-weight: 700;">
        Send Tip Verification Request 🚀
      </button>
    </form>
  </div>
</div>

<script>
const galleryImagesData = <?= json_encode($allImages) ?>;
const siteAdUrl = <?= json_encode($adUrl) ?>;
const tipWalletsData = <?= json_encode($tipWallets ?? []) ?>;

function switchPromptPane(pane) {
    const paneBlueprint = document.getElementById('pane-blueprint');
    const paneTipjar = document.getElementById('pane-tipjar');
    const btnBlueprint = document.getElementById('tab-btn-blueprint');
    const btnTipjar = document.getElementById('tab-btn-tipjar');

    if (pane === 'tipjar') {
        if (paneBlueprint) paneBlueprint.style.display = 'none';
        if (paneTipjar) paneTipjar.style.display = 'block';
        if (btnBlueprint) {
            btnBlueprint.className = 'ts-btn ts-btn-outline ts-btn-sm';
            btnBlueprint.style.color = '';
            btnBlueprint.style.borderColor = '';
        }
        if (btnTipjar) {
            btnTipjar.className = 'ts-btn ts-btn-primary ts-btn-sm';
            btnTipjar.style.background = '#f59e0b';
            btnTipjar.style.borderColor = '#f59e0b';
            btnTipjar.style.color = '#000';
        }
        if (paneTipjar) paneTipjar.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    } else {
        if (paneBlueprint) paneBlueprint.style.display = 'block';
        if (paneTipjar) paneTipjar.style.display = 'none';
        if (btnBlueprint) {
            btnBlueprint.className = 'ts-btn ts-btn-primary ts-btn-sm';
            btnBlueprint.style.background = '';
            btnBlueprint.style.borderColor = '';
            btnBlueprint.style.color = '';
        }
        if (btnTipjar) {
            btnTipjar.className = 'ts-btn ts-btn-outline ts-btn-sm';
            btnTipjar.style.background = '';
            btnTipjar.style.borderColor = 'rgba(245,158,11,0.35)';
            btnTipjar.style.color = '#f59e0b';
        }
    }
}

function switchTipCurrency(curr) {
    const addr = tipWalletsData[curr] || '0x71C8A60e46b38cE1241F8308D649372F7505a76E';
    const inputEl = document.getElementById('tip-wallet-address-input');
    const qrImg = document.getElementById('tip-qr-image');
    const labelEl = document.getElementById('tip-wallet-network-label');
    const hiddenCurr = document.getElementById('tip-submit-currency');

    if (inputEl) inputEl.value = addr;
    if (qrImg) qrImg.src = 'https://api.qrserver.com/v1/create-qr-code/?size=130x130&data=' + encodeURIComponent(addr);
    if (labelEl) labelEl.innerText = curr + ' Deposit Address';
    if (hiddenCurr) hiddenCurr.value = curr;

    document.querySelectorAll('.tip-currency-tab-btn').forEach(btn => {
        btn.style.borderColor = 'var(--ts-border-main)';
        btn.style.background = 'var(--ts-bg-surface-elevated)';
        btn.style.color = 'var(--ts-text-secondary)';
    });
    const activeBtn = document.getElementById('tip-curr-btn-' + curr);
    if (activeBtn) {
        activeBtn.style.borderColor = '#10b981';
        activeBtn.style.background = 'rgba(16,185,129,0.15)';
        activeBtn.style.color = '#10b981';
    }
}

function selectTipChip(amt) {
    const gross = parseFloat(amt) || 10;
    const fee = (gross * 0.05).toFixed(2);
    const net = (gross * 0.95).toFixed(2);

    const grossEl = document.getElementById('tip-breakdown-gross');
    const feeEl = document.getElementById('tip-breakdown-fee');
    const netEl = document.getElementById('tip-breakdown-net');
    const hiddenAmt = document.getElementById('tip-submit-amount');

    if (grossEl) grossEl.innerText = '$' + gross.toFixed(2);
    if (feeEl) feeEl.innerText = '-$' + fee;
    if (netEl) netEl.innerText = '+$' + net;
    if (hiddenAmt) hiddenAmt.value = gross;

    document.querySelectorAll('.tip-chip-btn').forEach(b => {
        b.style.borderColor = 'var(--ts-border-main)';
        b.style.background = 'var(--ts-bg-surface-elevated)';
        b.style.color = 'var(--ts-text-primary)';
    });
    if (window.event && window.event.currentTarget) {
        window.event.currentTarget.style.borderColor = '#f59e0b';
        window.event.currentTarget.style.background = 'rgba(245,158,11,0.15)';
        window.event.currentTarget.style.color = '#f59e0b';
    }
}

function copyTipAddress() {
    const inp = document.getElementById('tip-wallet-address-input');
    if (!inp) return;
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(inp.value).then(() => {
            if (typeof showToast === 'function') {
                showToast('Creator crypto address copied to clipboard! 📋', 'success');
            } else {
                alert('Creator crypto address copied to clipboard!');
            }
        });
    }
}

function submitCreatorTipAjax(e, formId) {
    e.preventDefault();
    const form = document.getElementById(formId);
    if (!form) return;

    const feedbackEl = form.querySelector('#tip-submit-feedback') || document.getElementById('tip-submit-feedback');
    const submitBtn = form.querySelector('button[type="submit"]');
    const origBtnText = submitBtn ? submitBtn.innerHTML : '';

    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Verifying Transaction Hash... ⏳';
    }

    const formData = new FormData(form);

    fetch('<?= url('/api/v1/tips/submit') ?>', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (feedbackEl) {
                feedbackEl.style.display = 'block';
                feedbackEl.style.background = 'rgba(16,185,129,0.15)';
                feedbackEl.style.border = '1px solid rgba(16,185,129,0.35)';
                feedbackEl.style.color = '#10b981';
                feedbackEl.innerHTML = '🎉 ' + (data.message || 'Tip submitted successfully!') + 
                    '<div style="font-size: 0.8rem; margin-top: 0.35rem; font-family: monospace; color: #f8fafc;">Reference: ' + (data.ref_no || '') + '</div>';
            }
            if (typeof showToast === 'function') {
                showToast('Tip verified & queued for author credit! Thank you! 🪙', 'success');
            }
            form.reset();
        } else {
            if (feedbackEl) {
                feedbackEl.style.display = 'block';
                feedbackEl.style.background = 'rgba(239,68,68,0.15)';
                feedbackEl.style.border = '1px solid rgba(239,68,68,0.35)';
                feedbackEl.style.color = '#ef4444';
                feedbackEl.innerText = data.error || 'Failed to submit tip verification.';
            }
        }
    })
    .catch(err => {
        if (feedbackEl) {
            feedbackEl.style.display = 'block';
            feedbackEl.style.background = 'rgba(239,68,68,0.15)';
            feedbackEl.style.border = '1px solid rgba(239,68,68,0.35)';
            feedbackEl.style.color = '#ef4444';
            feedbackEl.innerText = 'Network error. Please try again.';
        }
    })
    .finally(() => {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = origBtnText;
        }
    });
}

function openAuthorTipModal(authorName, articleUrl) {
    // Switch to tip jar tab directly for maximum richness
    const paneTipjar = document.getElementById('pane-tipjar');
    if (paneTipjar) {
        switchPromptPane('tipjar');
    } else {
        const modal = document.getElementById('authorTipModal');
        const userInp = document.getElementById('tip_author_username');
        const artInp = document.getElementById('tip_article_url');
        if (userInp) userInp.value = authorName;
        if (artInp) artInp.value = articleUrl;
        if (modal) modal.style.display = 'flex';
    }
}

function copyTextDirect(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
            if (typeof showToast === 'function') showToast('Copied to clipboard!', 'success');
            else alert('Copied to clipboard!');
        });
    }
}

function openPromptWithAd(e, targetUrl, adUrl) {
    e.preventDefault();
    if (adUrl) {
        try {
            const adWin = window.open(adUrl, '_blank');
            if (adWin) {
                try { adWin.blur(); window.focus(); } catch(err) {}
            }
        } catch(err) {
            window.open(adUrl, '_blank');
        }
    }
    window.location.href = targetUrl;
}

// Variable Injector Live Sync
function updateRenderedPrompt() {
    const rawTemplateEl = document.getElementById('raw-prompt-template');
    const renderedEl = document.getElementById('rendered-prompt-output');
    if (!rawTemplateEl || !renderedEl) return;

    let text = rawTemplateEl.value;
    const varInputs = document.querySelectorAll('.prompt-var-input');
    varInputs.forEach(inp => {
        const varName = inp.getAttribute('data-var-name');
        const val = inp.value || `[${varName}]`;
        const regex = new RegExp(`\\{\\{\\s*${varName}\\s*\\}\\}`, 'g');
        text = text.replace(regex, val);
    });

    renderedEl.textContent = text;
}

document.querySelectorAll('.prompt-var-input').forEach(inp => {
    inp.addEventListener('input', updateRenderedPrompt);
});
updateRenderedPrompt();

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
            const adUrl = copyBtn.getAttribute('data-ad-url') || siteAdUrl;

            // 1. Trigger Popunder / Direct Link Ad SYNCHRONOUSLY
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
