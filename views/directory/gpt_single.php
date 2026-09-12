<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="padding-top: 2.5rem; margin-bottom: 5rem;">
  <div style="max-width: 960px; margin: 0 auto;">

    <!-- Breadcrumb -->
    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: var(--ts-text-tertiary); margin-bottom: 1.5rem;">
      <a href="<?= url('/') ?>">Home</a>
      <span>/</span>
      <a href="<?= url('/gpts') ?>">Custom GPTs & Agents</a>
      <span>/</span>
      <span style="color: var(--ts-text-primary);"><?= htmlspecialchars($gpt['title']) ?></span>
    </div>

    <!-- Agent Hero Card -->
    <div class="ts-card" style="margin-bottom: 2rem; border-color: rgba(99, 102, 241, 0.35); position: relative; overflow: visible;">
      <div style="display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap;">
        <!-- Agent Icon / Avatar -->
        <div style="width: 100px; height: 100px; border-radius: var(--ts-radius-lg); background: linear-gradient(135deg, #6366f1 0%, #ec4899 50%, #06b6d4 100%); display: flex; align-items: center; justify-content: center; color: #fff; box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4); flex-shrink: 0;">
          <?php if (!empty($gpt['featured_image_url'])): ?>
            <img src="<?= htmlspecialchars(image_url($gpt['featured_image_url'])) ?>" alt="<?= htmlspecialchars($gpt['title']) ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: var(--ts-radius-lg);">
          <?php else: ?>
            <i data-lucide="bot" style="width: 48px; height: 48px;"></i>
          <?php endif; ?>
        </div>

        <div style="flex: 1; min-width: 280px;">
          <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; margin-bottom: 0.5rem;">
            <span class="ts-badge ts-badge-cyan">
              <i data-lucide="cpu" style="width: 12px; height: 12px;"></i>
              <?= htmlspecialchars($gpt['model_base'] ?? 'GPT-4o Architecture') ?>
            </span>
            <?php if (!empty($gpt['category_name'])): ?>
              <span class="ts-badge ts-badge-pink"><?= htmlspecialchars($gpt['category_name']) ?></span>
            <?php endif; ?>
            <?php if (!empty($gpt['topic_name'])): ?>
              <button type="button" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.55rem; font-size: 0.72rem; border-radius: 9999px;" onclick="toggleTopicSubscription(<?= (int)$gpt['topic_id'] ?>, this)">
                <i data-lucide="plus" style="width: 11px; height: 11px;"></i>
                <span>Follow <?= htmlspecialchars($gpt['topic_name']) ?></span>
              </button>
            <?php endif; ?>
            <span style="font-size: 0.8rem; color: var(--ts-text-tertiary);">
              <i data-lucide="eye" style="width: 13px; height: 13px; display: inline-block; vertical-align: -2px;"></i> <?= number_format((int)($gpt['view_count'] ?? 0)) ?> views
            </span>
          </div>

          <h1 style="font-size: 2.2rem; margin-bottom: 0.5rem; line-height: 1.2;"><?= htmlspecialchars($gpt['title']) ?></h1>
          <div style="font-size: 0.82rem; color: var(--ts-text-tertiary); margin-bottom: 0.75rem; display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
            <span>Published <?= \App\Core\DateHelper::render($gpt['created_at'] ?? 'now', 'M j, Y • g:i A', 'datetime') ?></span>
            <?php if (!empty($gpt['updated_at']) && strtotime($gpt['updated_at']) > strtotime($gpt['created_at'] ?? 'now') + 60): ?>
              <span style="color: #38bdf8;">&bull; Updated <?= \App\Core\DateHelper::render($gpt['updated_at'], 'M j, Y • g:i A', 'datetime') ?></span>
            <?php endif; ?>
          </div>
          <p style="color: var(--ts-text-secondary); font-size: 1.05rem; line-height: 1.6; margin-bottom: 1.25rem;">
            <?= htmlspecialchars($gpt['summary']) ?>
          </p>

          <!-- Author Info -->
          <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; padding: 0.5rem 0.75rem; background: var(--ts-bg-surface-elevated); border-radius: var(--ts-radius-md); width: fit-content;">
            <?php if (!empty($gpt['author_avatar'])): ?>
              <img src="<?= htmlspecialchars(image_url($gpt['author_avatar'])) ?>" alt="Author" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover;">
            <?php endif; ?>
            <span style="font-size: 0.85rem; color: var(--ts-text-secondary);">
              Architected by <strong style="color: var(--ts-text-primary);"><?= htmlspecialchars($gpt['author_name'] ?? 'TechScout Engineer') ?></strong>
            </span>
            <span class="ts-badge ts-badge-green" style="font-size: 0.65rem; padding: 0.1rem 0.4rem;">VERIFIED AGENT</span>
          </div>

          <!-- CTAs -->
          <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <?php if (!empty($gpt['openai_url'])): ?>
              <a href="<?= url('/go/' . $gpt['slug']) ?>" target="_blank" class="ts-btn ts-btn-accent" style="padding: 0.65rem 1.5rem;">
                <i data-lucide="external-link"></i> Launch in OpenAI ↗
              </a>
            <?php endif; ?>
            <button type="button" onclick="toggleBookmark(<?= (int)$gpt['id'] ?>)" id="content-bookmark-btn" class="ts-btn <?= !empty($isBookmarked) ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="<?= !empty($isBookmarked) ? 'background:#ec4899; border-color:#ec4899;' : 'color:#ec4899; border-color:rgba(236,72,153,0.3);' ?>">
              <i data-lucide="bookmark"></i>
              <span><?= !empty($isBookmarked) ? 'Saved in Library' : 'Save Bookmark' ?></span>
            </button>
            <button onclick="navigator.clipboard.writeText(window.location.href); showToast('Agent URL copied to clipboard!', 'success');" class="ts-btn ts-btn-outline">
              <i data-lucide="share-2"></i> Share Agent
            </button>
            <button id="btn-react" data-content-id="<?= $gpt['id'] ?>" class="ts-btn ts-btn-outline">
              <i data-lucide="heart" style="color: #ec4899;"></i> 
              <span id="reaction-count"><?= number_format((int)($gpt['reaction_count'] ?? 0)) ?></span> Upvotes
            </button>
            <button type="button" onclick="openReportModal('gpt_listing', <?= (int)$gpt['id'] ?>, '<?= htmlspecialchars(addslashes($gpt['title'])) ?>', window.location.href)" class="ts-btn ts-btn-outline" style="color: #94a3b8;" title="Report this GPT agent">
              <i data-lucide="flag" style="width: 13px; height: 13px; color: #ef4444;"></i> Report
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Suggested Starter Prompts -->
    <?php if (!empty($promptStarters)): ?>
      <div class="ts-card" style="margin-bottom: 2rem;">
        <h3 style="font-size: 1.2rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; color: #38bdf8;">
          <i data-lucide="message-square-code"></i> Suggested Prompt Starters
        </h3>
        <p style="font-size: 0.85rem; color: var(--ts-text-tertiary); margin-bottom: 1.25rem;">
          Click any prompt starter below to copy and feed directly into the agent.
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 0.75rem;">
          <?php foreach ($promptStarters as $starter): ?>
            <div onclick="navigator.clipboard.writeText(<?= htmlspecialchars(json_encode($starter)) ?>); showToast('Prompt starter copied!', 'info');" style="padding: 1rem; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: var(--ts-radius-md); cursor: pointer; transition: all 0.2s ease; display: flex; justify-content: space-between; align-items: center;" onmouseover="this.style.borderColor='var(--ts-primary)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='var(--ts-border-main)'; this.style.transform='none'">
              <span style="font-size: 0.9rem; color: var(--ts-text-primary); font-family: var(--ts-font-sans);"><?= htmlspecialchars($starter) ?></span>
              <i data-lucide="copy" style="width: 16px; height: 16px; color: var(--ts-text-tertiary); flex-shrink: 0; margin-left: 0.5rem;"></i>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Deep-Dive Specifications & System Blueprint -->
    <div class="ts-card" style="margin-bottom: 2rem;">
      <h3 style="font-size: 1.25rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
        <i data-lucide="layers"></i> Agent Architecture & System Directive
      </h3>
      <div style="font-size: 1rem; line-height: 1.7; color: var(--ts-text-secondary);">
        <?php if (!empty($gpt['body_html'])): ?>
          <?= $gpt['body_html'] ?>
        <?php else: ?>
          <p>This custom GPT agent is pre-configured with multi-shot engineering instructions, defensive coding policies, and domain-specialized benchmarks.</p>
        <?php endif; ?>
      </div>

      <?php $gptTags = \App\Core\TagHelper::getTagsForContent((int)$gpt['id']); ?>
      <?php if (!empty($gptTags)): ?>
        <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--ts-border-main); display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
          <span style="font-size: 0.8rem; color: #64748b; font-weight: 600;">TAGS:</span>
          <?php foreach ($gptTags as $tag): ?>
            <a href="<?= url('/tag/' . $tag['slug']) ?>" class="ts-badge ts-badge-pink" style="text-decoration: none;">
              #<?= htmlspecialchars($tag['name']) ?>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Related Agents Grid -->
    <?php if (!empty($relatedGpts)): ?>
      <div style="margin-top: 3rem;">
        <h3 style="font-size: 1.4rem; margin-bottom: 1.25rem;">More Specialized Agents</h3>
        <div class="ts-grid-3">
          <?php foreach ($relatedGpts as $rel): ?>
            <?php if ($rel['id'] !== $gpt['id']): ?>
              <div class="ts-card" style="display: flex; flex-direction: column;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                  <span class="ts-badge ts-badge-cyan"><?= htmlspecialchars($rel['model_base'] ?? 'GPT-4o') ?></span>
                </div>
                <h4 style="font-size: 1.1rem; margin-bottom: 0.5rem;">
                  <a href="<?= url('/gpts/' . $rel['slug']) ?>"><?= htmlspecialchars($rel['title']) ?></a>
                </h4>
                <p style="font-size: 0.85rem; color: var(--ts-text-secondary); flex: 1; margin-bottom: 1rem;">
                  <?= htmlspecialchars($rel['summary']) ?>
                </p>
                <div style="display: flex; gap: 0.5rem;">
                  <a href="<?= url('/gpts/' . $rel['slug']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="flex: 1;">View Blueprint</a>
                  <?php if (!empty($rel['openai_url'])): ?>
                    <a href="<?= url('/go/' . $rel['slug']) ?>" target="_blank" class="ts-btn ts-btn-accent ts-btn-sm"><i data-lucide="external-link" style="width: 14px;"></i></a>
                  <?php endif; ?>
                </div>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
