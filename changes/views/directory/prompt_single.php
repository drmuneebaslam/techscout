<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 2.5rem; margin-bottom: 5rem;">
  <div style="max-width: 900px; margin: 0 auto;">
    <!-- Prompt Header -->
    <div style="margin-bottom: 2rem;">
      <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; flex-wrap: wrap;">
        <span class="ts-badge ts-badge-green">Interactive Prompt Blueprint</span>
        <?php if (!empty($prompt['topic_name'])): ?>
          <button type="button" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.55rem; font-size: 0.72rem; border-radius: 9999px;" onclick="toggleTopicSubscription(<?= (int)$prompt['topic_id'] ?>, this)">
            <i data-lucide="plus" style="width: 11px; height: 11px;"></i>
            <span>Follow <?= htmlspecialchars($prompt['topic_name']) ?></span>
          </button>
        <?php endif; ?>
      </div>
      <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;"><?= htmlspecialchars($prompt['title']) ?></h1>
      <div style="font-size: 0.82rem; color: var(--ts-text-tertiary); margin-bottom: 0.75rem; display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
        <span>Published <?= \App\Core\DateHelper::render($prompt['created_at'] ?? 'now', 'M j, Y • g:i A', 'datetime') ?></span>
        <?php if (!empty($prompt['updated_at']) && strtotime($prompt['updated_at']) > strtotime($prompt['created_at'] ?? 'now') + 60): ?>
          <span style="color: #38bdf8;">&bull; Updated <?= \App\Core\DateHelper::render($prompt['updated_at'], 'M j, Y • g:i A', 'datetime') ?></span>
        <?php endif; ?>
      </div>
      <p style="color: var(--ts-text-secondary); font-size: 1.1rem;"><?= htmlspecialchars($prompt['summary']) ?></p>
    </div>

    <!-- Hidden Raw Template for JS Injector -->
    <textarea id="raw-prompt-template" style="display: none;"><?= htmlspecialchars($prompt['prompt_template'] ?? '') ?></textarea>

    <!-- Dynamic Variable Customizer Box -->
    <?php if (!empty($variables)): ?>
      <div class="ts-card" style="margin-bottom: 2rem; border-color: rgba(16, 185, 129, 0.4);">
        <h3 style="font-size: 1.15rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; color: #34d399;">
          <i data-lucide="sliders" style="width: 18px;"></i> Dynamic Variable Injector
        </h3>
        <p style="font-size: 0.85rem; color: var(--ts-text-tertiary); margin-bottom: 1.25rem;">
          Customize the input values below. The prompt output on the right updates in real-time.
        </p>

        <div style="display: grid; gap: 1rem;">
          <?php foreach ($variables as $v): ?>
            <div>
              <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--ts-text-primary); margin-bottom: 0.35rem;">
                <?= htmlspecialchars($v['label'] ?? $v['name']) ?>
              </label>
              <input type="text" class="prompt-var-input" data-var-name="<?= htmlspecialchars($v['name']) ?>" value="<?= htmlspecialchars($v['default'] ?? '') ?>" style="width: 100%; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main); padding: 0.65rem 1rem; border-radius: var(--ts-radius-md); color: var(--ts-text-primary); font-size: 0.9rem; outline: none;">
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Generated Prompt Output & Copy Action -->
    <div class="ts-card" style="margin-bottom: 2rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
        <h3 style="font-size: 1.15rem; margin: 0;">Generated Prompt Blueprint</h3>
        <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
          <button type="button" onclick="toggleBookmark(<?= (int)$prompt['id'] ?>)" id="content-bookmark-btn" class="ts-btn <?= !empty($isBookmarked) ? 'ts-btn-primary' : 'ts-btn-outline' ?> ts-btn-sm" style="<?= !empty($isBookmarked) ? 'background:#ec4899; border-color:#ec4899;' : 'color:#ec4899; border-color:rgba(236,72,153,0.3);' ?>">
            <span><?= !empty($isBookmarked) ? '🔖 Saved in Library' : '🔖 Save Bookmark' ?></span>
          </button>
          <button type="button" onclick="openReportModal('prompt', <?= (int)$prompt['id'] ?>, '<?= htmlspecialchars(addslashes($prompt['title'])) ?>', window.location.href)" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #94a3b8; font-size: 0.78rem;" title="Report this prompt template">
            <i data-lucide="flag" style="width: 12px; height: 12px; color: #ef4444;"></i> Report
          </button>
          <button id="btn-copy-prompt" data-prompt-id="<?= $prompt['id'] ?>" class="ts-btn ts-btn-accent">
            <i data-lucide="copy"></i> Copy Customized Prompt
          </button>
        </div>
      </div>

      <pre style="background: #090d16; border: 1px solid var(--ts-border-main); border-radius: var(--ts-radius-md); padding: 1.25rem; overflow-x: auto; max-height: 450px;"><code id="rendered-prompt-output" class="language-markdown" style="font-family: var(--ts-font-mono); font-size: 0.9rem; white-space: pre-wrap; word-break: break-word;"></code></pre>
    </div>

    <!-- Target Models & Use Case -->
    <?php if (!empty($models)): ?>
      <div class="ts-card" style="margin-bottom: 2rem;">
        <h4 style="font-size: 0.95rem; margin-bottom: 0.75rem;">Optimized Foundation Models</h4>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
          <?php foreach ($models as $m): ?>
            <span class="ts-badge ts-badge-cyan"><?= htmlspecialchars($m) ?></span>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <?php $pTags = \App\Core\TagHelper::getTagsForContent((int)$prompt['id']); ?>
    <?php if (!empty($pTags)): ?>
      <div class="ts-card" style="margin-bottom: 2rem;">
        <h4 style="font-size: 0.95rem; margin-bottom: 0.75rem;">Blueprint Technical Tags</h4>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
          <?php foreach ($pTags as $pt): ?>
            <a href="<?= url('/tag/' . $pt['slug']) ?>" class="ts-badge ts-badge-green" style="text-decoration: none;">
              #<?= htmlspecialchars($pt['name']) ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
