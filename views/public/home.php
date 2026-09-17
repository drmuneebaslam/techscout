<?php 
  require __DIR__ . '/../layouts/header.php'; 
  $isLoggedIn = \App\Core\Auth::check();
  $showForYouActive = $isLoggedIn && !empty($hasSubscriptions) && (!isset($sectionsMap['recommended']) || (int)$sectionsMap['recommended']['is_enabled'] === 1);
  
  $heroSec = $sectionsMap['hero'] ?? ['is_enabled' => 1, 'title' => 'The Frontier of Technology, Scouted for Architects & Builders', 'subtitle' => 'Explore production-grade technical articles, benchmarked AI tools, modular LLM prompt blueprints, and developer hackathons — all on a single unified platform.'];
  $heroCfg = !empty($heroSec['config_json']) ? (is_array($heroSec['config_json']) ? $heroSec['config_json'] : json_decode($heroSec['config_json'], true)) : [];
?>

<!-- 1. Hero Section (Controlled by CMS Homepage Manager) -->
<?php if ((int)($heroSec['is_enabled'] ?? 1) === 1): ?>
<section class="ts-hero">
  <div class="container">
    <div style="text-align: center; max-width: 860px; margin: 0 auto;">
      <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(236,72,153,0.15); border: 1px solid rgba(236,72,153,0.4); padding: 0.35rem 1rem; border-radius: 9999px; margin-bottom: 1.5rem; box-shadow: 0 0 20px rgba(236,72,153,0.15);">
        <span style="width: 8px; height: 8px; border-radius: 50%; background: #ec4899; display: inline-block; box-shadow: 0 0 8px #ec4899;"></span>
        <span style="font-size: 0.8rem; font-weight: 700; color: #f472b6; font-family: var(--ts-font-mono); letter-spacing: 0.05em;"><?= htmlspecialchars($heroCfg['badge_text'] ?? 'NEXT-GEN DEVELOPER INTELLIGENCE • ACTIVE RADAR') ?></span>
      </div>
      <h1 class="ts-hero-title">
        <?= nl2br(htmlspecialchars($heroSec['title'] ?? 'The Frontier of Technology, Scouted for Architects & Builders')) ?>
      </h1>
      <p class="ts-hero-sub">
        <?= htmlspecialchars($heroSec['subtitle'] ?? 'Explore production-grade technical articles, benchmarked AI tools, modular LLM prompt blueprints, and developer hackathons — all on a single unified platform.') ?>
      </p>

      <div class="ts-hero-actions">
        <a href="<?= url('/blog') ?>" class="ts-btn ts-btn-primary">
          <i data-lucide="compass"></i> <?= htmlspecialchars($heroCfg['cta_label'] ?? 'Explore Articles') ?>
        </a>
        <a href="<?= url('/tools') ?>" class="ts-btn ts-btn-outline">
          <i data-lucide="cpu"></i> AI Tools Radar
        </a>
        <a href="<?= url('/prompts') ?>" class="ts-btn ts-btn-accent">
          <i data-lucide="terminal"></i> Prompt Library
        </a>
        <a href="<?= url('/gpts') ?>" class="ts-btn ts-btn-outline">
          <i data-lucide="bot"></i> Custom GPTs
        </a>
      </div>

      <!-- Quick Topic Domain Badges -->
      <?php if (!isset($sectionsMap['topics']) || (int)$sectionsMap['topics']['is_enabled'] === 1): ?>
        <div style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap; margin-top: 1.75rem;">
          <?php foreach (array_slice($allTopics ?? [], 0, 5) as $t): ?>
            <a href="<?= url('/category/' . $t['slug']) ?>" class="ts-badge" style="background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); color: var(--ts-text-secondary); text-decoration: none; padding: 0.35rem 0.75rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.35rem; border-radius: 9999px;">
              <i data-lucide="<?= htmlspecialchars($t['icon_name'] ?: 'layers') ?>" style="width: 12px; height: 12px; color: <?= htmlspecialchars($t['color_hex'] ?: '#6366f1') ?>;"></i>
              <?= htmlspecialchars($t['name']) ?>
            </a>
          <?php endforeach; ?>
          <a href="<?= url('/categories') ?>" class="ts-badge ts-badge-pink" style="text-decoration: none; padding: 0.35rem 0.75rem; font-size: 0.8rem; border-radius: 9999px;">
            + All 100+ Categories →
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Main Container: Feed & Modular Discovery Stream -->
<div class="container" style="margin-bottom: 5rem;">

  <!-- 2. Editorial Feeds (Trending, Featured, Latest & For You) -->
  <?php 
    $showTrending = !isset($sectionsMap['trending']) || (int)$sectionsMap['trending']['is_enabled'] === 1;
    $showFeatured = !isset($sectionsMap['featured']) || (int)$sectionsMap['featured']['is_enabled'] === 1;
    $showLatest = !isset($sectionsMap['latest']) || (int)$sectionsMap['latest']['is_enabled'] === 1;
    $showRecommended = !isset($sectionsMap['recommended']) || (int)$sectionsMap['recommended']['is_enabled'] === 1;
  ?>

  <?php if ($showTrending || $showFeatured || $showLatest || ($isLoggedIn && $showRecommended)): ?>
    <div style="margin-bottom: 1.75rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid var(--ts-border-main); padding-bottom: 1rem; margin-top: 2rem;">
      <div>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
          <span class="ts-badge ts-badge-cyan">RADAR FEED</span>
          <h2 style="font-size: 1.5rem; margin: 0; color: var(--ts-text-primary); font-family: 'Outfit', sans-serif;">
            <?= htmlspecialchars($sectionsMap['latest']['title'] ?? $sectionsMap['trending']['title'] ?? 'Engineering Publications & Architecture') ?>
          </h2>
        </div>
        <p style="color: var(--ts-text-tertiary); font-size: 0.85rem; margin: 0.25rem 0 0 0;">
          <?= htmlspecialchars($sectionsMap['latest']['subtitle'] ?? $sectionsMap['trending']['subtitle'] ?? 'Curated technical journalism, prompt blueprints, and production tutorials.') ?>
        </p>
      </div>

      <!-- Feed Switch Tabs -->
      <div style="display: flex; gap: 0.35rem; background: var(--ts-bg-surface-elevated); padding: 0.25rem; border-radius: 8px; border: 1px solid var(--ts-border-main);">
        <?php if ($isLoggedIn && $showRecommended): ?>
          <button type="button" id="tab-for-you-btn" onclick="switchHomeFeedTab('for-you')" class="ts-btn ts-btn-sm <?= $showForYouActive ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="padding: 0.35rem 0.85rem; font-size: 0.8rem; <?= !$showForYouActive ? 'border:none;' : '' ?>">
            🌟 For You
          </button>
        <?php endif; ?>

        <?php if ($showTrending): ?>
          <button type="button" id="tab-trending-btn" onclick="switchHomeFeedTab('trending')" class="ts-btn ts-btn-sm <?= !$showForYouActive ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="padding: 0.35rem 0.85rem; font-size: 0.8rem; <?= $showForYouActive ? 'border:none;' : '' ?>">
            🔥 Trending
          </button>
        <?php endif; ?>

        <?php if ($showFeatured): ?>
          <button type="button" id="tab-featured-btn" onclick="switchHomeFeedTab('featured')" class="ts-btn ts-btn-sm ts-btn-outline" style="padding: 0.35rem 0.85rem; font-size: 0.8rem; border: none;">
            ⚡ Featured
          </button>
        <?php endif; ?>

        <?php if ($showLatest): ?>
          <button type="button" id="tab-latest-btn" onclick="switchHomeFeedTab('latest')" class="ts-btn ts-btn-sm ts-btn-outline" style="padding: 0.35rem 0.85rem; font-size: 0.8rem; border: none;">
            ⚡ Latest
          </button>
        <?php endif; ?>
      </div>
    </div>

    <?php if ($isLoggedIn && !$hasSubscriptions): ?>
      <!-- Topic Follow Banner (Only for logged in users with 0 subs) -->
      <div class="ts-card" style="background: var(--ts-bg-surface); border-color: var(--ts-border-main); padding: 1.25rem 1.5rem; margin-bottom: 1.75rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 0.85rem;">
          <div style="display: flex; align-items: center; gap: 0.4rem;">
            <i data-lucide="sparkles" style="color: #ec4899; width: 16px;"></i>
            <strong style="color: var(--ts-text-primary); font-size: 0.95rem;">Personalize Your Developer Feed:</strong>
            <span style="font-size: 0.82rem; color: var(--ts-text-secondary);">Follow categories you work with to build your custom radar.</span>
          </div>
          <a href="<?= url('/categories') ?>" style="color: var(--ts-text-accent); font-size: 0.8rem; text-decoration: none;">View All 100+ Categories →</a>
        </div>

        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
          <?php foreach (array_slice($allTopics ?? [], 0, 8) as $top): ?>
            <button type="button" class="ts-btn ts-btn-sm ts-btn-outline" style="padding: 0.25rem 0.65rem; font-size: 0.75rem; border-radius: 9999px;" onclick="quickFollowTopic(<?= $top['id'] ?>, this)">
              <i data-lucide="plus" style="width: 11px; height: 11px;"></i>
              <span><?= htmlspecialchars($top['name']) ?></span>
            </button>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($isLoggedIn && $showRecommended): ?>
      <!-- Pane 1: Personalized For You Articles -->
      <div id="feed-pane-for-you" class="ts-grid-3" style="display: <?= $showForYouActive ? 'grid' : 'none' ?>;">
        <?php if (!empty($personalizedArticles)): ?>
          <?php foreach ($personalizedArticles as $art): ?>
            <article class="ts-card">
              <?php if (!empty($art['featured_image_url'])): ?>
                <img src="<?= htmlspecialchars(image_url($art['featured_image_url'])) ?>" alt="<?= htmlspecialchars($art['title']) ?>" class="article-card-img">
              <?php endif; ?>
              <div style="display: flex; gap: 0.45rem; margin-bottom: 0.75rem; flex-wrap: wrap; align-items: center;">
                <?php if (!empty($art['is_pinned'])): ?>
                  <span class="ts-badge" style="background: rgba(245,158,11,0.15); color: #f59e0b; border: 1px solid rgba(245,158,11,0.4); font-weight: 700; display: inline-flex; align-items: center; gap: 0.25rem;">
                    📌 PINNED
                  </span>
                <?php endif; ?>
                <?php if (!empty($art['custom_badge'])): ?>
                  <span class="ts-badge" style="background: <?= htmlspecialchars($art['badge_bg'] ?: 'rgba(56,189,248,0.15)') ?>; color: <?= htmlspecialchars($art['badge_color'] ?: '#38bdf8') ?>; border: 1px solid <?= htmlspecialchars($art['badge_color'] ?: '#38bdf8') ?>50; font-weight: 700;">
                    ⭐ <?= htmlspecialchars($art['custom_badge']) ?>
                  </span>
                <?php endif; ?>
                <span class="ts-badge"><?= htmlspecialchars($art['category_name'] ?? 'Editorial') ?></span>
                <span class="ts-badge ts-badge-cyan"><?= $art['reading_time_mins'] ?? 5 ?> min read</span>
              </div>
              <h3 class="article-card-title">
                <a href="<?= url('/article/' . $art['slug']) ?>"><?= htmlspecialchars($art['title']) ?></a>
              </h3>
              <p style="font-size: 0.9rem; color: var(--ts-text-secondary); line-height: 1.5;">
                <?= htmlspecialchars($art['summary']) ?>
              </p>
              <div class="article-card-meta">
                <?php if (!empty($art['author_avatar'])): ?>
                  <img src="<?= htmlspecialchars(image_url($art['author_avatar'])) ?>" alt="" loading="lazy" onerror="this.style.display='none'">
                <?php endif; ?>
                <span class="meta-author" title="<?= htmlspecialchars($art['author_name'] ?? 'Staff Writer') ?>"><?= htmlspecialchars($art['author_name'] ?? 'Staff Writer') ?></span>
                <span class="meta-dot">&bull;</span>
                <span class="meta-stat"><i data-lucide="flame" style="width: 13px; height: 13px; display: inline; vertical-align: middle; color: #ef4444;"></i> <?= $art['reaction_count'] ?></span>
              </div>
            </article>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="ts-card" style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
            <i data-lucide="sparkles" style="width: 40px; height: 40px; color: var(--ts-primary); margin-bottom: 0.75rem;"></i>
            <h3>No articles match your followed categories yet</h3>
            <p style="color: var(--ts-text-secondary); font-size: 0.88rem; margin-top: 0.25rem;">Follow more categories from the Categories hub to expand your feed.</p>
            <a href="<?= url('/categories') ?>" class="ts-btn ts-btn-primary ts-btn-sm" style="margin-top: 0.75rem;">Browse Categories →</a>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php if ($showTrending): ?>
      <!-- Pane 2: Trending Articles -->
      <div id="feed-pane-trending" class="ts-grid-3" style="display: <?= !$showForYouActive ? 'grid' : 'none' ?>;">
        <?php foreach ($trendingArticles as $art): ?>
          <article class="ts-card">
            <?php if (!empty($art['featured_image_url'])): ?>
              <img src="<?= htmlspecialchars(image_url($art['featured_image_url'])) ?>" alt="<?= htmlspecialchars($art['title']) ?>" class="article-card-img">
            <?php endif; ?>
            <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem;">
              <span class="ts-badge ts-badge-pink"><?= htmlspecialchars($art['category_name'] ?? 'Trending') ?></span>
              <span class="ts-badge ts-badge-cyan"><?= $art['reading_time_mins'] ?? 5 ?> min</span>
            </div>
            <h3 class="article-card-title">
              <a href="<?= url('/article/' . $art['slug']) ?>"><?= htmlspecialchars($art['title']) ?></a>
            </h3>
            <p style="font-size: 0.9rem; color: var(--ts-text-secondary); line-height: 1.5;">
              <?= htmlspecialchars($art['summary']) ?>
            </p>
            <div class="article-card-meta">
              <?php if (!empty($art['author_avatar'])): ?>
                <img src="<?= htmlspecialchars(image_url($art['author_avatar'])) ?>" alt="" loading="lazy" onerror="this.style.display='none'">
              <?php endif; ?>
              <span class="meta-author" title="<?= htmlspecialchars($art['author_name'] ?? 'Staff Writer') ?>"><?= htmlspecialchars($art['author_name'] ?? 'Staff Writer') ?></span>
              <span class="meta-dot">&bull;</span>
              <span class="meta-stat"><?= number_format($art['view_count']) ?> reads</span>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if ($showFeatured): ?>
      <!-- Pane 3: Featured Articles -->
      <div id="feed-pane-featured" class="ts-grid-3" style="display: none;">
        <?php foreach ($featuredArticles as $art): ?>
          <article class="ts-card">
            <?php if (!empty($art['featured_image_url'])): ?>
              <img src="<?= htmlspecialchars(image_url($art['featured_image_url'])) ?>" alt="<?= htmlspecialchars($art['title']) ?>" class="article-card-img">
            <?php endif; ?>
            <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem;">
              <span class="ts-badge"><?= htmlspecialchars($art['category_name'] ?? 'Featured') ?></span>
              <span class="ts-badge ts-badge-cyan"><?= $art['reading_time_mins'] ?? 5 ?> min</span>
            </div>
            <h3 class="article-card-title">
              <a href="<?= url('/article/' . $art['slug']) ?>"><?= htmlspecialchars($art['title']) ?></a>
            </h3>
            <p style="font-size: 0.9rem; color: var(--ts-text-secondary); line-height: 1.5;">
              <?= htmlspecialchars($art['summary']) ?>
            </p>
            <div class="article-card-meta">
              <?php if (!empty($art['author_avatar'])): ?>
                <img src="<?= htmlspecialchars(image_url($art['author_avatar'])) ?>" alt="" loading="lazy" onerror="this.style.display='none'">
              <?php endif; ?>
              <span class="meta-author" title="<?= htmlspecialchars($art['author_name'] ?? 'Staff Writer') ?>"><?= htmlspecialchars($art['author_name'] ?? 'Staff Writer') ?></span>
              <span class="meta-dot">&bull;</span>
              <span class="meta-stat"><?= date('M j, Y', strtotime($art['published_at'] ?? 'now')) ?></span>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if ($showLatest): ?>
      <!-- Pane 4: Latest Articles -->
      <div id="feed-pane-latest" class="ts-grid-3" style="display: none;">
        <?php foreach ($latestArticles as $art): ?>
          <article class="ts-card">
            <?php if (!empty($art['featured_image_url'])): ?>
              <img src="<?= htmlspecialchars(image_url($art['featured_image_url'])) ?>" alt="<?= htmlspecialchars($art['title']) ?>" class="article-card-img">
            <?php endif; ?>
            <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem;">
              <span class="ts-badge ts-badge-cyan"><?= htmlspecialchars($art['category_name'] ?? 'Latest') ?></span>
              <span class="ts-badge"><?= $art['reading_time_mins'] ?? 5 ?> min</span>
            </div>
            <h3 class="article-card-title">
              <a href="<?= url('/article/' . $art['slug']) ?>"><?= htmlspecialchars($art['title']) ?></a>
            </h3>
            <p style="font-size: 0.9rem; color: var(--ts-text-secondary); line-height: 1.5;">
              <?= htmlspecialchars($art['summary']) ?>
            </p>
            <div class="article-card-meta">
              <?php if (!empty($art['author_avatar'])): ?>
                <img src="<?= htmlspecialchars(image_url($art['author_avatar'])) ?>" alt="" loading="lazy" onerror="this.style.display='none'">
              <?php endif; ?>
              <span class="meta-author" title="<?= htmlspecialchars($art['author_name'] ?? 'Staff Writer') ?>"><?= htmlspecialchars($art['author_name'] ?? 'Staff Writer') ?></span>
              <span class="meta-dot">&bull;</span>
              <span class="meta-stat"><?= date('M j, Y', strtotime($art['published_at'] ?? 'now')) ?></span>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>

  <!-- 3. Custom GPTs & Autonomous Agents (Section: ai_tools) -->
  <?php if (!isset($sectionsMap['ai_tools']) || (int)$sectionsMap['ai_tools']['is_enabled'] === 1): ?>
    <?php $gptsSec = $sectionsMap['ai_tools'] ?? []; ?>
    <div style="margin-top: 4.5rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem;">
      <div>
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
          <span class="ts-badge ts-badge-pink">OpenAI &amp; LLM Agents</span>
          <span class="ts-badge ts-badge-cyan">Architecture Blueprints</span>
        </div>
        <h2 style="font-size: 1.75rem; margin-bottom: 0.25rem; font-family: 'Outfit', sans-serif;">
          <?= htmlspecialchars($gptsSec['title'] ?? 'Autonomous Custom GPTs & Specialized Agents') ?>
        </h2>
        <p style="color: var(--ts-text-tertiary); font-size: 0.9rem;">
          <?= htmlspecialchars($gptsSec['subtitle'] ?? 'Engineered assistant personas with prompt architectures and direct launch deep-links.') ?>
        </p>
      </div>
      <a href="<?= url('/gpts') ?>" class="ts-btn ts-btn-outline ts-btn-sm">Explore All Custom GPTs →</a>
    </div>

    <div class="ts-grid-3">
      <?php foreach ($featuredGpts as $gpt): ?>
        <div class="ts-card gpt-feature-card" style="display: flex; flex-direction: column; position: relative; border-color: rgba(236,72,153,0.3);">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.85rem;">
            <span class="ts-badge ts-badge-pink"><?= htmlspecialchars($gpt['model_base'] ?? 'GPT-4o') ?></span>
            <span style="font-size: 0.8rem; color: #94a3b8;"><i data-lucide="eye" style="width: 13px; height: 13px; display: inline; vertical-align: middle;"></i> <?= number_format($gpt['view_count'] ?? 0) ?> views</span>
          </div>
          <h3 style="font-size: 1.2rem; margin-bottom: 0.6rem;">
            <a href="<?= url('/gpts/' . $gpt['slug']) ?>" style="color: var(--ts-text-primary); text-decoration: none;"><?= htmlspecialchars($gpt['title']) ?></a>
          </h3>
          <p style="font-size: 0.88rem; color: var(--ts-text-secondary); line-height: 1.5; flex: 1; margin-bottom: 1.25rem;">
            <?= htmlspecialchars($gpt['summary']) ?>
          </p>
          <div style="display: flex; gap: 0.75rem; align-items: center; border-top: 1px solid var(--ts-border-main); padding-top: 1rem; margin-top: auto;">
            <a href="<?= url('/gpts/' . $gpt['slug']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="flex: 1;">
              View Blueprint &amp; Prompts
            </a>
            <?php if (!empty($gpt['openai_url'])): ?>
              <a href="<?= url('/go/' . $gpt['slug']) ?>" target="_blank" class="ts-btn ts-btn-primary ts-btn-sm" title="Launch in ChatGPT">
                <i data-lucide="bot" style="width: 14px;"></i> Launch ↗
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- 4. AI Tools Radar (Section: tools) -->
  <?php if (!isset($sectionsMap['tools']) || (int)$sectionsMap['tools']['is_enabled'] === 1): ?>
    <?php $toolsSec = $sectionsMap['tools'] ?? []; ?>
    <div style="margin-top: 4.5rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
      <div>
        <h2 style="font-size: 1.75rem; font-family: 'Outfit', sans-serif; margin-bottom: 0.25rem;">
          <?= htmlspecialchars($toolsSec['title'] ?? 'AI Tools & Software Radar') ?>
        </h2>
        <p style="color: var(--ts-text-tertiary); font-size: 0.9rem; margin: 0;">
          <?= htmlspecialchars($toolsSec['subtitle'] ?? 'Tested and benchmarked developer applications.') ?>
        </p>
      </div>
      <a href="<?= url('/tools') ?>" class="ts-btn ts-btn-outline ts-btn-sm">Browse Directory →</a>
    </div>

    <div class="ts-grid-4">
      <?php 
        $toolsToDisplay = !empty($featuredTools) ? $featuredTools : (!empty($latestTools) ? $latestTools : []);
        foreach ($toolsToDisplay as $tool): 
      ?>
        <div class="ts-card" style="display: flex; flex-direction: column;">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
            <span class="ts-badge ts-badge-pink"><?= htmlspecialchars($tool['pricing_model'] ?? 'Freemium') ?></span>
            <span style="font-size: 0.85rem; color: #f59e0b; font-weight: 700;">⭐ <?= number_format((float)($tool['rating_avg'] ?? 5.0), 1) ?></span>
          </div>
          <h3 style="font-size: 1.15rem; margin-bottom: 0.5rem;">
            <a href="<?= url('/tools/' . $tool['slug']) ?>"><?= htmlspecialchars($tool['title']) ?></a>
          </h3>
          <p style="font-size: 0.85rem; color: var(--ts-text-secondary); flex: 1; margin-bottom: 1rem;">
            <?= htmlspecialchars($tool['summary']) ?>
          </p>
          <div style="display: flex; gap: 0.5rem;">
            <a href="<?= url('/tools/' . $tool['slug']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="flex: 1;">Details</a>
            <a href="<?= url('/go/' . $tool['slug']) ?>" target="_blank" class="ts-btn ts-btn-accent ts-btn-sm"><i data-lucide="external-link" style="width: 14px;"></i></a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- 5. Trending Engineering Prompts (Section: prompts) -->
  <?php if (!isset($sectionsMap['prompts']) || (int)$sectionsMap['prompts']['is_enabled'] === 1): ?>
    <?php $promptsSec = $sectionsMap['prompts'] ?? []; ?>
    <div style="margin-top: 4.5rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
      <div>
        <h2 style="font-size: 1.75rem; font-family: 'Outfit', sans-serif; margin-bottom: 0.25rem;">
          <?= htmlspecialchars($promptsSec['title'] ?? 'Engineering Prompt Library') ?>
        </h2>
        <p style="color: var(--ts-text-tertiary); font-size: 0.9rem; margin: 0;">
          <?= htmlspecialchars($promptsSec['subtitle'] ?? 'Copyable, tested prompt blueprints with variable injectors.') ?>
        </p>
      </div>
      <a href="<?= url('/prompts') ?>" class="ts-btn ts-btn-outline ts-btn-sm">Explore All Prompts →</a>
    </div>

    <div class="ts-grid-4">
      <?php foreach ($trendingPrompts as $prompt): ?>
        <div class="ts-card">
          <span class="ts-badge ts-badge-green" style="margin-bottom: 0.75rem;">Prompt Template</span>
          <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem;">
            <a href="<?= url('/prompts/' . $prompt['slug']) ?>"><?= htmlspecialchars($prompt['title']) ?></a>
          </h3>
          <p style="font-size: 0.85rem; color: var(--ts-text-secondary); margin-bottom: 1rem;">
            <?= htmlspecialchars($prompt['summary']) ?>
          </p>
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 0.8rem; color: var(--ts-text-tertiary);"><i data-lucide="copy" style="width: 12px; display: inline;"></i> <?= $prompt['copy_count'] ?? 0 ?> copies</span>
            <a href="<?= url('/prompts/' . $prompt['slug']) ?>" class="ts-btn ts-btn-primary ts-btn-sm">Inject &amp; Copy ⚡</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- 6. Top Curators & Authors (Section: authors) -->
  <?php if (!isset($sectionsMap['authors']) || (int)$sectionsMap['authors']['is_enabled'] === 1): ?>
    <?php $authorsSec = $sectionsMap['authors'] ?? []; ?>
    <div style="margin-top: 4.5rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
      <div>
        <h2 style="font-size: 1.75rem; font-family: 'Outfit', sans-serif; margin-bottom: 0.25rem;">
          <?= htmlspecialchars($authorsSec['title'] ?? 'Top Curators & Staff Authors') ?>
        </h2>
        <p style="color: var(--ts-text-tertiary); font-size: 0.9rem; margin: 0;">
          <?= htmlspecialchars($authorsSec['subtitle'] ?? 'Featured engineering creators, builders, and technical researchers.') ?>
        </p>
      </div>
      <a href="<?= url('/author/apply') ?>" class="ts-btn ts-btn-outline ts-btn-sm">Become an Author →</a>
    </div>

    <div class="ts-grid-4">
      <?php foreach ($topAuthors as $author): ?>
        <div class="ts-card" style="text-align: center; padding: 1.5rem 1rem;">
          <img src="<?= htmlspecialchars(image_url($author['avatar_url'] ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120')) ?>" alt="<?= htmlspecialchars($author['display_name']) ?>" style="width: 64px; height: 64px; border-radius: 50%; object-fit: cover; margin: 0 auto 0.75rem auto; border: 2px solid var(--ts-primary);">
          <h3 style="font-size: 1.05rem; margin-bottom: 0.25rem;">
            <a href="<?= url('/author/profile/' . $author['slug']) ?>" style="color: var(--ts-text-primary); text-decoration: none;"><?= htmlspecialchars($author['display_name']) ?></a>
          </h3>
          <div style="font-size: 0.75rem; color: var(--ts-text-tertiary); margin-bottom: 0.75rem;"><?= htmlspecialchars($author['headline'] ?: 'Technical Writer & Scout') ?></div>
          <a href="<?= url('/author/profile/' . $author['slug']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%; font-size: 0.75rem;">View Profile ↗</a>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- 7. Campaigns & Challenges (Section: campaigns) -->
  <?php if ((!isset($sectionsMap['campaigns']) || (int)$sectionsMap['campaigns']['is_enabled'] === 1) && !empty($activeCampaigns)): ?>
    <?php $campSec = $sectionsMap['campaigns'] ?? []; ?>
    <div style="margin-top: 4.5rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
      <div>
        <h2 style="font-size: 1.75rem; font-family: 'Outfit', sans-serif; margin-bottom: 0.25rem;">
          <?= htmlspecialchars($campSec['title'] ?? 'Developer Bounties & Hackathons') ?>
        </h2>
        <p style="color: var(--ts-text-tertiary); font-size: 0.9rem; margin: 0;">
          <?= htmlspecialchars($campSec['subtitle'] ?? 'Active prize pools, engineering challenges, and competitions.') ?>
        </p>
      </div>
      <a href="<?= url('/campaigns') ?>" class="ts-btn ts-btn-outline ts-btn-sm">Explore All Bounties →</a>
    </div>

    <div class="ts-grid-2">
      <?php foreach ($activeCampaigns as $camp): ?>
        <div class="ts-card" style="display: flex; flex-direction: column; justify-content: space-between; border-color: rgba(16,185,129,0.3);">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
              <span class="ts-badge ts-badge-green">Active Challenge</span>
              <span style="font-weight: 700; color: #10b981; font-size: 0.9rem;">🏆 $<?= number_format((float)($camp['prize_pool_usd'] ?? 500)) ?> Prize</span>
            </div>
            <h3 style="font-size: 1.2rem; margin-bottom: 0.5rem;">
              <a href="<?= url('/campaigns/' . $camp['slug']) ?>" style="color: var(--ts-text-primary); text-decoration: none;"><?= htmlspecialchars($camp['title']) ?></a>
            </h3>
            <p style="font-size: 0.85rem; color: var(--ts-text-secondary); line-height: 1.5;">
              <?= htmlspecialchars($camp['summary']) ?>
            </p>
          </div>
          <div style="margin-top: 1rem; border-top: 1px solid var(--ts-border-main); padding-top: 0.75rem; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 0.75rem; color: var(--ts-text-tertiary);">Deadline: <?= htmlspecialchars($camp['end_date'] ?? 'Ongoing') ?></span>
            <a href="<?= url('/campaigns/' . $camp['slug']) ?>" class="ts-btn ts-btn-primary ts-btn-sm">Join Challenge 🚀</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- 8. Newsletter Dispatch Widget (Section: newsletter) -->
  <?php if (!isset($sectionsMap['newsletter']) || (int)$sectionsMap['newsletter']['is_enabled'] === 1): ?>
    <?php 
      $newsSec = $sectionsMap['newsletter'] ?? []; 
      $newsCfg = !empty($newsSec['config_json']) ? (is_array($newsSec['config_json']) ? $newsSec['config_json'] : json_decode($newsSec['config_json'], true)) : [];
    ?>
    <div class="ts-card" style="margin-top: 4.5rem; background: linear-gradient(135deg, rgba(99,102,241,0.15), rgba(56,189,248,0.1)); border: 1px solid rgba(99,102,241,0.35); border-radius: 16px; padding: 2.5rem 2rem; text-align: center;">
      <span class="ts-badge ts-badge-cyan" style="margin-bottom: 0.75rem;">WEEKLY RADAR DISPATCH</span>
      <h2 style="font-size: 1.85rem; color: var(--ts-text-primary); margin: 0 0 0.5rem 0; font-family: 'Outfit', sans-serif;">
        <?= htmlspecialchars($newsSec['title'] ?? 'TechScout Dispatch Newsletter') ?>
      </h2>
      <p style="color: var(--ts-text-secondary); font-size: 0.95rem; max-width: 600px; margin: 0 auto 1.5rem auto; line-height: 1.5;">
        <?= htmlspecialchars($newsCfg['pitch'] ?? $newsSec['subtitle'] ?? 'Join 48,000+ software engineers receiving our curated breakdown of top AI tools, production prompts, and architecture patterns.') ?>
      </p>
      <form onsubmit="handleHomeNewsletter(event, this)" style="display: flex; gap: 0.5rem; max-width: 480px; margin: 0 auto; flex-wrap: wrap;">
        <input type="email" name="email" id="home_newsletter_email" placeholder="Enter your email (e.g. builder@scale.io)" style="flex: 1; min-width: 220px; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-main); border-radius: 8px; padding: 0.75rem 1rem; color: var(--ts-text-primary); font-size: 0.9rem;" required>
        <button type="submit" id="home_newsletter_btn" class="ts-btn ts-btn-primary" style="padding: 0.75rem 1.25rem; font-weight: 700;">Subscribe ✉️</button>
      </form>
      <div id="home_newsletter_msg" style="display: none; margin-top: 0.75rem; font-size: 0.88rem; font-weight: 600;"></div>
    </div>
  <?php endif; ?>

  <script>
  function handleHomeNewsletter(e, form) {
    e.preventDefault();
    const input = form.querySelector('input[name="email"]');
    const btn = form.querySelector('button[type="submit"]');
    const msgBox = document.getElementById('home_newsletter_msg');
    const email = input.value.trim();
    if (!email) return;

    btn.disabled = true;
    btn.innerText = 'Subscribing...';

    fetch('<?= url('/api/v1/newsletter/subscribe') ?>', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ email: email, subscriber_type: 'homepage_dispatch' })
    })
    .then(r => r.json())
    .then(data => {
      btn.disabled = false;
      btn.innerText = 'Subscribe ✉️';
      if (data.success) {
        input.value = '';
        if (msgBox) {
          msgBox.style.display = 'block';
          msgBox.style.color = '#34d399';
          msgBox.innerText = data.message;
        }
        if (typeof showToast === 'function') {
          showToast(data.message, 'success');
        }
      } else {
        if (msgBox) {
          msgBox.style.display = 'block';
          msgBox.style.color = '#f87171';
          msgBox.innerText = data.error || 'Subscription failed.';
        }
        if (typeof showToast === 'function') {
          showToast(data.error || 'Failed to subscribe.', 'error');
        }
      }
    })
    .catch(err => {
      btn.disabled = false;
      btn.innerText = 'Subscribe ✉️';
      if (typeof showToast === 'function') {
        showToast('Network error while subscribing.', 'error');
      }
    });
  }
  </script>

  <!-- 9. Creator Studio CTA (Section: cta) -->
  <?php if (!isset($sectionsMap['cta']) || (int)$sectionsMap['cta']['is_enabled'] === 1): ?>
    <?php 
      $ctaSec = $sectionsMap['cta'] ?? []; 
      $ctaCfg = !empty($ctaSec['config_json']) ? (is_array($ctaSec['config_json']) ? $ctaSec['config_json'] : json_decode($ctaSec['config_json'], true)) : [];
      $ctaTarget = $ctaCfg['target_url'] ?? '/author/apply';
    ?>
    <div class="ts-card" style="margin-top: 3rem; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 16px; padding: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
      <div>
        <h3 style="font-size: 1.4rem; color: var(--ts-text-primary); margin: 0 0 0.35rem 0; font-family: 'Outfit', sans-serif;">
          <?= htmlspecialchars($ctaSec['title'] ?? 'Are you a Technical Author or AI Researcher?') ?>
        </h3>
        <p style="color: var(--ts-text-secondary); font-size: 0.9rem; margin: 0;">
          <?= htmlspecialchars($ctaSec['subtitle'] ?? 'Publish deep-dives, earn creator rewards, and showcase your engineering radar to 100k+ builders.') ?>
        </p>
      </div>
      <a href="<?= url($ctaTarget) ?>" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #10b981, #06b6d4); font-weight: 700; padding: 0.75rem 1.5rem; font-size: 0.95rem;">
        Apply for Creator Verification ✍️
      </a>
    </div>
  <?php endif; ?>

</div>

<script>
function switchHomeFeedTab(tab) {
  const panes = {
    'for-you': document.getElementById('feed-pane-for-you'),
    'trending': document.getElementById('feed-pane-trending'),
    'featured': document.getElementById('feed-pane-featured'),
    'latest': document.getElementById('feed-pane-latest')
  };
  const btns = {
    'for-you': document.getElementById('tab-for-you-btn'),
    'trending': document.getElementById('tab-trending-btn'),
    'featured': document.getElementById('tab-featured-btn'),
    'latest': document.getElementById('tab-latest-btn')
  };

  Object.keys(panes).forEach(k => {
    if (panes[k]) panes[k].style.display = (k === tab) ? 'grid' : 'none';
    if (btns[k]) {
      if (k === tab) {
        btns[k].classList.remove('ts-btn-outline');
        btns[k].classList.add('ts-btn-primary');
      } else {
        btns[k].classList.remove('ts-btn-primary');
        btns[k].classList.add('ts-btn-outline');
      }
    }
  });
}

async function quickFollowTopic(topicId, btn) {
  try {
    const res = await fetch('<?= url('/category/subscribe/') ?>' + topicId, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' }
    });
    const data = await res.json();
    if (data.success) {
      if (data.is_subscribed) {
        btn.classList.remove('ts-btn-outline');
        btn.classList.add('ts-btn-primary');
        btn.innerHTML = `<i data-lucide="check" style="width: 12px; height: 12px;"></i> Following`;
        showToast('Category added to your personalized radar! 🚀', 'success');
      } else {
        btn.classList.remove('ts-btn-primary');
        btn.classList.add('ts-btn-outline');
        btn.innerHTML = `<i data-lucide="plus" style="width: 12px; height: 12px;"></i> Follow`;
        showToast('Category unfollowed.', 'info');
      }
      if (typeof lucide !== 'undefined') lucide.createIcons();
    }
  } catch (err) {
    showToast('Failed to update subscription.', 'error');
  }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
