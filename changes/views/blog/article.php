<?php 
require __DIR__ . '/../layouts/header.php'; 

// Fetch Community Poll for this article [TS-SOC-11]
$articlePoll = null;
$articlePollOptions = [];
$userVotedOptionId = null;
try {
    $articlePoll = \App\Core\Database::queryOne("SELECT * FROM article_polls WHERE article_id = ? AND is_active = 1 LIMIT 1", [(int)$article['id']]);
    if ($articlePoll) {
        $articlePollOptions = \App\Core\Database::query("SELECT * FROM article_poll_options WHERE poll_id = ? ORDER BY sort_order ASC", [(int)$articlePoll['id']]);
        $currentUserId = \App\Core\Auth::id();
        $ip = \App\Core\Request::ip();
        if ($currentUserId) {
            $voted = \App\Core\Database::queryOne("SELECT option_id FROM article_poll_votes WHERE poll_id = ? AND user_id = ?", [(int)$articlePoll['id'], $currentUserId]);
        } else {
            $voted = \App\Core\Database::queryOne("SELECT option_id FROM article_poll_votes WHERE poll_id = ? AND user_id IS NULL AND ip_address = ?", [(int)$articlePoll['id'], $ip]);
        }
        $userVotedOptionId = $voted ? (int)$voted['option_id'] : null;
    }
} catch (\Throwable $e) {}

// Multi-taxonomy relationships (Primary + Secondary Categories)
$articleCategories = [];
try {
    $articleCategories = \App\Core\Database::query(
        "SELECT cat.id, cat.name, cat.slug, cat.icon_name, cat.color_hex, cc.is_primary 
         FROM content_categories cc 
         JOIN categories cat ON cc.category_id = cat.id 
         WHERE cc.content_id = ? 
         ORDER BY cc.is_primary DESC, cat.name ASC",
        [(int)$article['id']]
    );
} catch (\Throwable $e) {}

$primaryCat = null;
$secondaryCats = [];
foreach ($articleCategories as $ac) {
    if ((int)$ac['is_primary'] === 1 && !$primaryCat) {
        $primaryCat = $ac;
    } else {
        $secondaryCats[] = $ac;
    }
}
if (!$primaryCat && !empty($article['category_name'])) {
    $primaryCat = [
        'id' => $article['category_id'] ?? 0,
        'name' => $article['category_name'],
        'slug' => $article['category_slug'] ?? 'ai',
        'color_hex' => '#38bdf8'
    ];
}

// Platform treasury wallets for author tipping [TS-CR-05]
$tipWallets = [
    'BTC' => \App\Core\Database::queryScalar("SELECT key_value FROM site_settings WHERE key_name = 'crypto_wallet_btc' LIMIT 1") ?: 'bc1qtechscoutcrypto7482910482910btcxx',
    'ETH' => \App\Core\Database::queryScalar("SELECT key_value FROM site_settings WHERE key_name = 'crypto_wallet_eth' LIMIT 1") ?: '0x71C8A1bB722956276f5789129486c9A199859187',
    'USDT' => \App\Core\Database::queryScalar("SELECT key_value FROM site_settings WHERE key_name = 'crypto_wallet_usdt' LIMIT 1") ?: '0x71C8A1bB722956276f5789129486c9A199859187',
    'SOL' => \App\Core\Database::queryScalar("SELECT key_value FROM site_settings WHERE key_name = 'crypto_wallet_sol' LIMIT 1") ?: 'So11111111111111111111111111111111111111112',
];

$userArticleReaction = null;
if (\App\Core\Auth::check()) {
    $userArticleReaction = \App\Core\Database::queryScalar(
        "SELECT reaction_type FROM reactions WHERE user_id = ? AND content_id = ? LIMIT 1",
        [(int)\App\Core\Auth::id(), (int)$article['id']]
    );
}
?>

<!-- Schema.org JSON-LD Structured Data -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "TechArticle",
  "headline": <?= json_encode($article['title']) ?>,
  "description": <?= json_encode($article['summary']) ?>,
  "image": <?= json_encode($article['featured_image_url'] ?? '') ?>,
  "datePublished": <?= json_encode($article['published_at'] ?? date('c')) ?>,
  "author": {
    "@type": "Person",
    "name": <?= json_encode($article['author_name'] ?? 'TechScout Author') ?>
  }
}
</script>

<?php if (($article['status'] ?? 'published') !== 'published'): ?>
  <div style="background: rgba(236,72,153,0.15); border-bottom: 1px solid rgba(236,72,153,0.4); padding: 0.85rem 1rem; text-align: center; color: #f472b6; font-size: 0.9rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
    <i data-lucide="shield-alert" style="width: 16px;"></i>
    <strong>STAFF & AUTHOR EDITORIAL PREVIEW:</strong> This article is currently <span class="ts-badge ts-badge-pink" style="text-transform: uppercase;"><?= htmlspecialchars($article['status']) ?></span> and is hidden from the public audience.
    <?php if (\App\Core\Auth::isAdmin()): ?>
      <form action="<?= url('/admin/content/action/' . $article['id']) ?>" method="POST" style="display: inline; margin-left: 0.75rem;">
        <button type="submit" name="action" value="approve" class="ts-btn ts-btn-primary ts-btn-sm" style="padding: 0.2rem 0.6rem; font-size: 0.75rem;">Approve & Publish Now ⚡</button>
      </form>
    <?php endif; ?>
  </div>
<?php endif; ?>

<!-- Top Reading Progress Indicator -->
<div id="article-reading-progress-track" style="position: fixed; top: 0; left: 0; width: 100%; height: 3px; background: rgba(148,163,184,0.1); z-index: 999999;">
  <div id="article-reading-progress-bar" style="height: 100%; width: <?= max(0, (int)($savedProgress ?? 0)) ?>%; background: linear-gradient(90deg, #6366f1, #38bdf8, #ec4899); transition: width 0.15s ease;"></div>
</div>

<div class="container" style="padding-top: 2rem; margin-bottom: 5rem;">
  <article class="article-single-column" style="max-width: 860px; margin: 0 auto;">
    <!-- Breadcrumbs & Category Follow Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; font-size: 0.85rem; color: var(--ts-text-tertiary); margin-bottom: 1.25rem;">
      <div style="display: flex; gap: 0.5rem; align-items: center;">
        <a href="<?= url('/') ?>" style="color: var(--ts-text-tertiary); text-decoration: none;">Home</a> <span>/</span>
        <a href="<?= url('/blog') ?>" style="color: var(--ts-text-tertiary); text-decoration: none;">Articles</a> <span>/</span>
        <a href="<?= url('/category/' . ($primaryCat['slug'] ?? 'ai') . '?entity=article') ?>" style="color: var(--ts-accent-light); font-weight: 600; text-decoration: none;">
          <?= htmlspecialchars($primaryCat['name'] ?? 'Architecture') ?>
        </a>
      </div>

      <?php if (!empty($primaryCat['id'])): ?>
        <button type="button" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.65rem; font-size: 0.74rem; border-radius: 9999px;" onclick="toggleCategorySubscription(<?= (int)$primaryCat['id'] ?>, this)">
          <i data-lucide="bell" style="width: 12px; height: 12px;"></i>
          <span>Follow <?= htmlspecialchars($primaryCat['name']) ?></span>
        </button>
      <?php endif; ?>
    </div>

    <!-- Category & Content Type Badges -->
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem; align-items: center; flex-wrap: wrap;">
      <?php if (!empty($article['is_pinned'])): ?>
        <span class="ts-badge" style="background: rgba(245,158,11,0.15); color: #f59e0b; border: 1px solid rgba(245,158,11,0.4); display: inline-flex; align-items: center; gap: 0.3rem; font-weight: 700;">
          <i data-lucide="pin" style="width: 12px; height: 12px;"></i> PINNED
        </span>
      <?php endif; ?>

      <?php if (!empty($article['custom_badge'])): ?>
        <span class="ts-badge" style="background: <?= htmlspecialchars($article['badge_bg'] ?: 'rgba(56,189,248,0.15)') ?>; color: <?= htmlspecialchars($article['badge_color'] ?: '#38bdf8') ?>; border: 1px solid <?= htmlspecialchars($article['badge_color'] ?: '#38bdf8') ?>55; display: inline-flex; align-items: center; gap: 0.3rem; font-weight: 700;">
          <i data-lucide="award" style="width: 12px; height: 12px;"></i> <?= htmlspecialchars($article['custom_badge']) ?>
        </span>
      <?php endif; ?>

      <a href="<?= url('/category/' . ($primaryCat['slug'] ?? 'ai') . '?entity=article') ?>" class="ts-badge ts-badge-cyan" style="text-decoration: none; font-weight: 700;" title="Primary Category">
        ⭐ <?= htmlspecialchars($primaryCat['name'] ?? 'Artificial Intelligence') ?>
      </a>

      <?php foreach ($secondaryCats as $sc): ?>
        <a href="<?= url('/category/' . $sc['slug'] . '?entity=article') ?>" class="ts-badge ts-badge-indigo" style="text-decoration: none; font-size: 0.76rem;" title="Secondary Category">
          #<?= htmlspecialchars($sc['name']) ?>
        </a>
      <?php endforeach; ?>

      <span class="ts-badge ts-badge-indigo"><?= strtoupper(htmlspecialchars($article['content_type'] ?? 'article')) ?></span>

      <span id="reading-session-badge" class="ts-badge ts-badge-green" style="display: none; font-size: 0.72rem;"></span>
    </div>

    <!-- Title & Summary -->
    <h1 style="font-size: 2.5rem; margin-bottom: 1.25rem; line-height: 1.25; font-family: 'Outfit', sans-serif; color: var(--ts-text-primary); font-weight: 800;">
      <?= htmlspecialchars($article['title']) ?>
    </h1>

    <?php if (!empty($article['summary'])): ?>
      <p style="font-size: 1.15rem; color: var(--ts-text-secondary); line-height: 1.6; margin-bottom: 1.5rem;">
        <?= htmlspecialchars($article['summary']) ?>
      </p>
    <?php endif; ?>

    <!-- Author & Published Meta Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--ts-border-main); border-bottom: 1px solid var(--ts-border-main); padding: 1rem 0; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;">
      <div style="display: flex; align-items: center; gap: 0.85rem;">
        <?php 
          $authorProfileUrl = url('/author/profile/' . ($article['author_slug'] ?? $article['author_id'] ?? 'staff'));
        ?>
        <a href="<?= $authorProfileUrl ?>" style="text-decoration: none;">
          <?php if (!empty($article['author_avatar'])): ?>
            <img src="<?= htmlspecialchars(image_url($article['author_avatar'])) ?>" alt="<?= htmlspecialchars($article['author_name']) ?>" style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover;">
          <?php else: ?>
            <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--ts-primary); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700;">
              <?= strtoupper(substr($article['author_name'] ?? 'S', 0, 1)) ?>
            </div>
          <?php endif; ?>
        </a>
        <div>
          <div style="font-weight: 700; color: var(--ts-text-primary); font-size: 0.95rem; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
            <a href="<?= $authorProfileUrl ?>" style="color: inherit; text-decoration: none;">
              <?= htmlspecialchars($article['author_name'] ?? 'Editorial Team') ?> ↗
            </a>
          </div>
          <div style="font-size: 0.8rem; color: var(--ts-text-tertiary); display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap;">
            <span>Published <?= \App\Core\DateHelper::render($article['published_at'] ?? $article['created_at'] ?? 'now', 'M j, Y • g:i A', 'datetime') ?></span>
            <?php if (!empty($article['updated_at']) && strtotime($article['updated_at']) > strtotime($article['published_at'] ?? $article['created_at'] ?? 'now') + 60): ?>
              <span style="color: #38bdf8; font-weight: 500;">&bull; Updated <?= \App\Core\DateHelper::render($article['updated_at'], 'M j, Y • g:i A', 'datetime') ?></span>
            <?php endif; ?>
            <span>&bull; <?= $article['reading_time_mins'] ?? 5 ?> min read</span>
          </div>

          <!-- Co-Authors Multi-Avatar Strip -->
          <?php if (!empty($coAuthors) && count($coAuthors) > 1): ?>
            <div style="display: flex; align-items: center; gap: 0.4rem; margin-top: 0.35rem; font-size: 0.78rem; color: #94a3b8;">
              <span style="font-weight: 600; color: #38bdf8;">👥 Co-Authored with:</span>
              <?php foreach ($coAuthors as $ca): ?>
                <?php if ($ca['role'] !== 'lead_author'): ?>
                  <a href="<?= url('/author/profile/' . ($ca['slug'] ?? '')) ?>" style="display: inline-flex; align-items: center; gap: 0.25rem; color: #cbd5e1; text-decoration: none; background: rgba(56,189,248,0.1); padding: 0.1rem 0.45rem; border-radius: 4px; border: 1px solid rgba(56,189,248,0.2);">
                    <span><?= htmlspecialchars($ca['display_name']) ?></span>
                    <span style="font-size: 0.68rem; color: #38bdf8;">(<?= (float)$ca['revenue_share_percentage'] ?>%)</span>
                  </a>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div style="display: flex; gap: 0.6rem; align-items: center; flex-wrap: wrap;">

        <button type="button" onclick="toggleBookmark(<?= (int)$article['id'] ?>)" id="top-bookmark-btn" class="ts-btn <?= !empty($isBookmarked) ? 'ts-btn-primary' : 'ts-btn-outline' ?> ts-btn-sm" style="<?= !empty($isBookmarked) ? 'background:#ec4899; border-color:#ec4899;' : 'color:#ec4899; border-color:rgba(236,72,153,0.3);' ?>">
          <span><?= !empty($isBookmarked) ? '🔖 Saved' : '🔖 Save' ?></span>
        </button>
        <span class="ts-badge"><i data-lucide="eye" style="width: 13px;"></i> <?= number_format($article['view_count']) ?> reads</span>
        <span class="ts-badge ts-badge-pink"><i data-lucide="flame" style="width: 13px;"></i> <?= $article['reaction_count'] ?></span>
      </div>
    </div>

    <!-- Featured Hero Image -->
    <?php if (!empty($article['featured_image_url'])): ?>
      <div style="width: 100%; margin: 0 0 2.5rem 0;">
        <img src="<?= htmlspecialchars(image_url($article['featured_image_url'])) ?>" alt="<?= htmlspecialchars($article['title']) ?>" style="width: 100%; max-height: 520px; object-fit: cover; border-radius: var(--ts-radius-lg); border: 1px solid var(--ts-border-main); display: block;">
      </div>
    <?php endif; ?>

    <!-- [TS-ED-02] Table of Contents Container (Collapsed by Default per Specification) -->
    <div id="article-toc-card" class="article-toc-card" style="background: var(--ts-card-bg); border: 1px solid var(--ts-border-main); border-radius: var(--ts-radius-md); padding: 1rem 1.25rem; margin-bottom: 2rem;">
      <div style="display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 0.92rem; font-weight: 700; color: var(--ts-text-primary); margin: 0; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.4rem;">
          <i data-lucide="list" style="width: 16px; height: 16px; color: var(--ts-primary);"></i> Table of Contents
        </h3>
        <button type="button" onclick="toggleMobileToc()" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.2rem 0.6rem;">
          <span id="toc-toggle-text">Show Sections</span> ▾
        </button>
      </div>
      <div id="article-toc-wrapper" class="article-toc-list-collapsible closed" style="display: none; margin-top: 0.75rem; border-top: 1px solid var(--ts-border-subtle); padding-top: 0.75rem;">
        <ul id="article-toc-list" class="article-toc-list" style="list-style: none; padding-left: 0; margin: 0; display: flex; flex-direction: column; gap: 0.4rem;"></ul>
      </div>
    </div>

    <!-- Main Editorial Content -->
    <div class="article-body-content" style="font-size: 1.08rem; line-height: 1.85; color: var(--ts-text-primary); width: 100%;">
      <?= $article['body_html'] ?>
    </div>

    <!-- [TS-SOC-11] Community Polls & Interactive Feedback Widgets -->
    <?php if ($articlePoll && !empty($articlePollOptions)): 
      $totalPollVotes = array_sum(array_column($articlePollOptions, 'vote_count'));
    ?>
      <div id="article-poll-box-<?= $articlePoll['id'] ?>" class="ts-card" style="margin: 2.5rem 0; padding: 1.5rem; border: 1px solid var(--ts-border-main); border-radius: 14px; background: var(--ts-bg-surface-elevated);">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
          <span class="ts-badge ts-badge-indigo" style="font-size: 0.72rem;">COMMUNITY POLL</span>
          <span style="font-size: 0.78rem; color: var(--ts-text-muted);"><span id="poll-total-votes"><?= $totalPollVotes ?></span> total votes</span>
        </div>
        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--ts-text-primary); margin: 0 0 1.25rem 0;">
          <?= htmlspecialchars($articlePoll['question']) ?>
        </h3>

        <div style="display: flex; flex-direction: column; gap: 0.75rem;" id="poll-options-container">
          <?php foreach ($articlePollOptions as $opt): 
            $pct = $totalPollVotes > 0 ? round(((int)$opt['vote_count'] / $totalPollVotes) * 100, 1) : 0;
            $isSelected = ($userVotedOptionId === (int)$opt['id']);
          ?>
            <div class="poll-option-row" onclick="submitPollVote(<?= (int)$articlePoll['id'] ?>, <?= (int)$opt['id'] ?>)" style="position: relative; padding: 0.85rem 1rem; border-radius: 10px; border: 1px solid <?= $isSelected ? 'var(--ts-primary)' : 'var(--ts-border-subtle)' ?>; cursor: pointer; overflow: hidden; background: var(--ts-bg-surface); transition: transform 0.15s ease;">
              <!-- Visual Percentage Progress Bar Fill -->
              <div style="position: absolute; top: 0; left: 0; bottom: 0; width: <?= $pct ?>%; background: <?= $isSelected ? 'rgba(56,189,248,0.2)' : 'rgba(99,102,241,0.12)' ?>; transition: width 0.4s ease; z-index: 1;"></div>
              
              <div style="position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center; font-size: 0.92rem;">
                <span style="font-weight: <?= $isSelected ? '700' : '500' ?>; color: var(--ts-text-primary);">
                  <?= $isSelected ? '✓ ' : '' ?><?= htmlspecialchars($opt['option_text']) ?>
                </span>
                <span style="font-weight: 700; color: var(--ts-primary); font-size: 0.88rem;"><?= $pct ?>%</span>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Tags -->
    <?php if (!empty($tags)): ?>
      <div style="display: flex; gap: 0.5rem; margin-top: 2.5rem; flex-wrap: wrap; align-items: center;">
        <span style="font-size: 0.8rem; color: #64748b; font-weight: 600;">TAGS:</span>
        <?php foreach ($tags as $tag): ?>
          <a href="<?= url('/tag/' . $tag['slug']) ?>" class="ts-badge ts-badge-cyan" style="text-decoration: none; transition: transform 0.15s ease;" title="Explore content tagged with #<?= htmlspecialchars($tag['name']) ?>">
            #<?= htmlspecialchars($tag['name']) ?>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- 6-Reaction Bar & Report Trigger -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin: 2.5rem 0 1.5rem 0; padding-top: 1.5rem; border-top: 1px solid var(--ts-border-main);">
      <div class="reaction-bar" style="margin: 0; display: flex; gap: 0.4rem; flex-wrap: wrap;">
        <span style="font-size: 0.9rem; font-weight: 600; color: var(--ts-text-primary); margin-right: 0.5rem; align-self: center;">React:</span>
        <button class="reaction-btn <?= ($userArticleReaction === 'like') ? 'active' : '' ?>" data-content-id="<?= $article['id'] ?>" data-reaction="like">🔥 Fire <span class="reaction-count"><?= $article['reaction_count'] ?></span></button>
        <button class="reaction-btn <?= ($userArticleReaction === 'rocket') ? 'active' : '' ?>" data-content-id="<?= $article['id'] ?>" data-reaction="rocket">🚀 Rocket</button>
        <button class="reaction-btn <?= ($userArticleReaction === 'insightful') ? 'active' : '' ?>" data-content-id="<?= $article['id'] ?>" data-reaction="insightful">💡 Insightful</button>
        <button class="reaction-btn <?= ($userArticleReaction === 'mind_blown') ? 'active' : '' ?>" data-content-id="<?= $article['id'] ?>" data-reaction="mind_blown">🤯 Mind Blown</button>
        <button class="reaction-btn <?= ($userArticleReaction === 'clap') ? 'active' : '' ?>" data-content-id="<?= $article['id'] ?>" data-reaction="clap">👏 Clap</button>
        <button type="button" class="reaction-btn" onclick="toggleBookmark(<?= $article['id'] ?>)" id="article-bottom-bookmark-btn" style="<?= !empty($isBookmarked) ? 'border-color: #ec4899; color: #f472b6;' : '' ?>">
          <span><?= !empty($isBookmarked) ? '🔖 Saved' : '🔖 Bookmark' ?></span>
        </button>
      </div>

      <button type="button" onclick="openReportModal('article', <?= (int)$article['id'] ?>, '<?= htmlspecialchars(addslashes($article['title'])) ?>', window.location.href)" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #94a3b8; padding: 0.35rem 0.75rem; font-size: 0.78rem;" title="Report this article to editorial moderation">
        <i data-lucide="flag" style="width: 13px; height: 13px; color: #ef4444;"></i> Report Article
      </button>
    </div>

    <!-- Social Sharing Distribution Bar -->
    <?php require __DIR__ . '/../components/social_share.php'; ?>

    <!-- Author Card Box -->
    <div class="ts-card" style="display: flex; gap: 1.25rem; align-items: center; margin: 2.5rem 0; background: linear-gradient(135deg, rgba(15,23,42,0.9), rgba(10,18,30,0.85)); border: 1px solid rgba(99,102,241,0.3); border-radius: 14px; padding: 1.5rem;">
      <a href="<?= $authorProfileUrl ?>" style="text-decoration: none;">
        <?php if (!empty($article['author_avatar'])): ?>
          <img src="<?= htmlspecialchars(image_url($article['author_avatar'])) ?>" alt="<?= htmlspecialchars($article['author_name']) ?>" style="width: 68px; height: 68px; border-radius: 50%; object-fit: cover; border: 2px solid var(--ts-primary);">
        <?php else: ?>
          <div style="width: 68px; height: 68px; border-radius: 50%; background: var(--ts-primary); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.5rem; font-weight: 700;">
            <?= strtoupper(substr($article['author_name'] ?? 'S', 0, 1)) ?>
          </div>
        <?php endif; ?>
      </a>
      <div style="flex: 1;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.25rem;">
          <h3 style="font-size: 1.2rem; margin: 0; font-weight: 800;">
            <a href="<?= $authorProfileUrl ?>" style="color: var(--ts-text-primary); text-decoration: none;">
              Written by <?= htmlspecialchars($article['author_name'] ?? 'Staff') ?> ↗
            </a>
          </h3>
        </div>
        <p style="font-size: 0.88rem; color: var(--ts-text-secondary); margin-bottom: 0.6rem;"><?= htmlspecialchars($article['author_headline'] ?? 'TechScout Technical Contributor & Systems Architect') ?></p>
        <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
          <a href="<?= $authorProfileUrl ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.2rem 0.6rem;">
            View Author Profile &amp; Research →
          </a>

          <!-- [TS-CR-05] Creator Revenue Share & Crypto Tip Jar Button -->
          <button type="button" onclick="openAuthorTipModal()" class="ts-btn ts-btn-primary ts-btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.75rem; background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 12px rgba(245,158,11,0.35);">
            <i data-lucide="zap" style="width: 12px; height: 12px;"></i> Tip Author (Crypto)
          </button>
        </div>
      </div>
    </div>

    <!-- [TS-CR-05] Author Tip Jar Modal -->
    <div id="author-tip-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 99999; align-items: center; justify-content: center; padding: 1.5rem;">
      <div class="ts-card" style="max-width: 520px; width: 100%; padding: 2rem; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 16px; position: relative;">
        <button type="button" onclick="closeAuthorTipModal()" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.25rem; color: var(--ts-text-muted); cursor: pointer;">✕</button>
        
        <div style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 0.5rem;">
          <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(245,158,11,0.2); color: #f59e0b; display: flex; align-items: center; justify-content: center;">
            <i data-lucide="zap" style="width: 20px; height: 20px;"></i>
          </div>
          <div>
            <h3 style="font-size: 1.25rem; margin: 0; color: var(--ts-text-primary);">Tip Author with Crypto</h3>
            <span style="font-size: 0.78rem; color: var(--ts-text-muted);">Direct creator support via verified platform treasury</span>
          </div>
        </div>

        <div style="background: rgba(245,158,11,0.06); border: 1px solid rgba(245,158,11,0.25); border-radius: 8px; padding: 0.75rem; margin: 1rem 0; font-size: 0.8rem; color: var(--ts-text-secondary); line-height: 1.45;">
          ℹ️ <strong>Platform Treasury Security:</strong> Tips are received at central platform wallets. The platform keeps a minimal 5% protocol maintenance fee, and <strong>95%</strong> is credited directly to <strong><?= htmlspecialchars($article['author_name'] ?? 'the author') ?></strong>'s payout ledger.
        </div>

        <form onsubmit="submitAuthorTip(event)">
          <input type="hidden" name="article_id" value="<?= (int)$article['id'] ?>">
          <input type="hidden" name="author_id" value="<?= (int)($article['author_id'] ?? 0) ?>">
          <input type="hidden" name="article_url" value="<?= htmlspecialchars(url('/article/' . ($article['slug'] ?? ''))) ?>">
          <input type="hidden" name="author_username" value="<?= htmlspecialchars($article['author_name'] ?? 'author') ?>">

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem;">
            <div>
              <label class="ts-form-label">Select Cryptocurrency</label>
              <select name="crypto_symbol" id="tip_crypto_symbol" class="ts-form-input" onchange="updateTipWalletDisplay()">
                <option value="SOL">Solana (SOL)</option>
                <option value="ETH">Ethereum (ETH)</option>
                <option value="USDT">Tether USD (USDT-ERC20)</option>
                <option value="BTC">Bitcoin (BTC)</option>
              </select>
            </div>
            <div>
              <label class="ts-form-label">Estimated USD Value ($)</label>
              <input type="number" name="amount_usd" min="1" step="0.5" value="10.00" class="ts-form-input" required>
            </div>
          </div>

          <div style="margin-bottom: 0.75rem;">
            <label class="ts-form-label">Send Tip to Platform Treasury Address:</label>
            <div style="display: flex; gap: 0.4rem;">
              <input type="text" id="tip_wallet_address" class="ts-form-input" readonly style="font-family: monospace; font-size: 0.78rem; background: var(--ts-bg-surface);">
              <button type="button" onclick="copyTipWallet()" class="ts-btn ts-btn-outline" style="font-size: 0.75rem; padding: 0.35rem 0.75rem;">Copy</button>
            </div>
          </div>

          <div style="margin-bottom: 0.75rem;">
            <label class="ts-form-label">Blockchain Transaction Hash (TXID)</label>
            <input type="text" name="txid" class="ts-form-input" placeholder="Paste on-chain transaction hash..." required style="font-family: monospace; font-size: 0.8rem;">
          </div>

          <div style="margin-bottom: 1.25rem;">
            <label class="ts-form-label">Feedback / Message for Author (Optional)</label>
            <textarea name="message" class="ts-form-input" rows="2" placeholder="Send a note of appreciation or feedback..."></textarea>
          </div>

          <button type="submit" class="ts-btn ts-btn-primary" style="width: 100%; padding: 0.75rem; font-weight: 700; background: linear-gradient(135deg, #f59e0b, #d97706);">
            Submit Tip for Verification ⚡
          </button>
        </form>
      </div>
    </div>

    <!-- [TS-SOC-08] Floating Quote Share Pill -->
    <div id="quote-share-pill" style="display: none; position: absolute; z-index: 9999; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 20px; padding: 0.3rem 0.85rem; box-shadow: 0 8px 24px rgba(0,0,0,0.35); cursor: pointer; font-size: 0.78rem; font-weight: 700; color: var(--ts-text-primary); display: none; align-items: center; gap: 0.4rem;">
      <i data-lucide="share-2" style="width: 12px; height: 12px; color: var(--ts-primary);"></i>
      <span>Share Quote Card</span>
    </div>

    <!-- Quote Share Preview Modal -->
    <div id="quote-share-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 999999; align-items: center; justify-content: center; padding: 1.5rem;">
      <div class="ts-card" style="max-width: 580px; width: 100%; padding: 2rem; background: var(--ts-bg-surface-elevated); border: 1px solid var(--ts-border-main); border-radius: 16px; position: relative;">
        <button type="button" onclick="document.getElementById('quote-share-modal').style.display='none'" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.25rem; color: var(--ts-text-muted); cursor: pointer;">✕</button>
        
        <h3 style="font-size: 1.2rem; margin: 0 0 1rem 0; color: var(--ts-text-primary);">Branded Social Quote Card</h3>
        
        <!-- Rendered Card Preview Canvas -->
        <div id="quote-card-canvas" style="background: linear-gradient(135deg, #090d16, #0f172a); border: 1px solid rgba(56,189,248,0.3); border-radius: 12px; padding: 1.75rem; margin-bottom: 1.25rem; position: relative; overflow: hidden;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.65rem;">
            <span style="font-weight: 800; font-size: 0.92rem; color: #38bdf8; letter-spacing: 0.05em;">TECONSCOUT // INSIGHT</span>
            <span style="font-size: 0.72rem; color: #94a3b8;"><?= htmlspecialchars($article['category_name'] ?? 'Tech') ?></span>
          </div>
          <blockquote id="quote-card-text" style="font-size: 1.15rem; font-style: italic; line-height: 1.6; color: #f8fafc; margin: 0 0 1.25rem 0; border-left: 3px solid #38bdf8; padding-left: 1rem;">
            "..."
          </blockquote>
          <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.78rem; color: #94a3b8;">
            <span>— <?= htmlspecialchars($article['author_name'] ?? 'TechScout') ?></span>
            <span style="font-family: monospace; font-size: 0.7rem; color: #38bdf8;"><?= htmlspecialchars(parse_url(url('/'), PHP_URL_HOST)) ?></span>
          </div>
        </div>

        <div style="display: flex; gap: 0.75rem;">
          <button type="button" onclick="copyQuoteText()" class="ts-btn ts-btn-outline" style="flex: 1; padding: 0.6rem; font-size: 0.85rem;">
            📋 Copy Quote Text
          </button>
          <button type="button" onclick="tweetQuoteCard()" class="ts-btn ts-btn-primary" style="flex: 1; padding: 0.6rem; font-size: 0.85rem; background: #1d9bf0;">
            🐦 Share on X (Twitter)
          </button>
        </div>
      </div>
    </div>

    <!-- Community Discussion & Nested Comments -->
    <?php require __DIR__ . '/../components/comments_section.php'; ?>
  </article>
</div>

<script>
function toggleMobileToc() {
  const wrapper = document.getElementById('article-toc-wrapper');
  const toggleText = document.getElementById('toc-toggle-text');
  if (!wrapper) return;

  const isClosed = wrapper.classList.contains('closed') || wrapper.style.display === 'none';
  if (isClosed) {
    wrapper.classList.remove('closed');
    wrapper.style.display = 'block';
    if (toggleText) toggleText.innerText = 'Hide Sections ▴';
  } else {
    wrapper.classList.add('closed');
    wrapper.style.display = 'none';
    if (toggleText) toggleText.innerText = 'Show Sections ▾';
  }
}

// [TS-SOC-11] Community Poll Vote Handler
async function submitPollVote(pollId, optionId) {
  try {
    const res = await fetch(`<?= url('/api/v1/polls/') ?>${pollId}/vote`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ option_id: optionId })
    });
    const data = await res.json();
    if (data.success && data.options) {
      const container = document.getElementById('poll-options-container');
      const totalEl = document.getElementById('poll-total-votes');
      if (totalEl) totalEl.innerText = data.total_votes;

      let html = '';
      data.options.forEach(opt => {
        const isSel = opt.id == optionId;
        html += `
          <div class="poll-option-row" onclick="submitPollVote(${pollId}, ${opt.id})" style="position: relative; padding: 0.85rem 1rem; border-radius: 10px; border: 1px solid ${isSel ? 'var(--ts-primary)' : 'var(--ts-border-subtle)'}; cursor: pointer; overflow: hidden; background: var(--ts-bg-surface); transition: transform 0.15s ease;">
            <div style="position: absolute; top: 0; left: 0; bottom: 0; width: ${opt.percent}%; background: ${isSel ? 'rgba(56,189,248,0.2)' : 'rgba(99,102,241,0.12)'}; transition: width 0.4s ease; z-index: 1;"></div>
            <div style="position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center; font-size: 0.92rem;">
              <span style="font-weight: ${isSel ? '700' : '500'}; color: var(--ts-text-primary);">${isSel ? '✓ ' : ''}${escapeHtml(opt.option_text)}</span>
              <span style="font-weight: 700; color: var(--ts-primary); font-size: 0.88rem;">${opt.percent}%</span>
            </div>
          </div>
        `;
      });
      if (container) container.innerHTML = html;
      if (typeof showToast === 'function') showToast('Vote recorded! 📊', 'success');
    } else if (data.error) {
      if (typeof showToast === 'function') showToast(data.error, 'info');
    }
  } catch (e) {}
}

// [TS-CR-05] Tip Author Modal Handlers
const tipWallets = <?= json_encode($tipWallets) ?>;
function openAuthorTipModal() {
  document.getElementById('author-tip-modal').style.display = 'flex';
  updateTipWalletDisplay();
}
function closeAuthorTipModal() {
  document.getElementById('author-tip-modal').style.display = 'none';
}
function updateTipWalletDisplay() {
  const sym = document.getElementById('tip_crypto_symbol').value;
  const input = document.getElementById('tip_wallet_address');
  if (input && tipWallets[sym]) {
    input.value = tipWallets[sym];
  }
}
function copyTipWallet() {
  const input = document.getElementById('tip_wallet_address');
  if (!input) return;
  navigator.clipboard.writeText(input.value).then(() => {
    if (typeof showToast === 'function') showToast('Treasury address copied! 📋', 'success');
  });
}
async function submitAuthorTip(e) {
  e.preventDefault();
  const form = e.target;
  const data = {
    article_id: form.article_id.value,
    author_id: form.author_id.value,
    article_url: form.article_url.value,
    author_username: form.author_username.value,
    crypto_symbol: form.crypto_symbol.value,
    amount_usd: form.amount_usd.value,
    txid: form.txid.value,
    message: form.message.value
  };
  try {
    const res = await fetch('<?= url('/api/v1/tips/submit') ?>', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(data)
    });
    const json = await res.json();
    if (json.success) {
      closeAuthorTipModal();
      if (typeof showToast === 'function') showToast('Tip submitted! Pending blockchain confirmation & admin credit. 🚀', 'success');
      form.reset();
    } else {
      if (typeof showToast === 'function') showToast(json.error || 'Failed to submit tip.', 'error');
    }
  } catch (err) {
    if (typeof showToast === 'function') showToast('Network error submitting tip.', 'error');
  }
}

// [TS-SOC-08] Quote Share Card Engine
let activeQuoteSelected = '';
document.addEventListener('selectionchange', () => {
  const sel = window.getSelection();
  const pill = document.getElementById('quote-share-pill');
  if (!sel || sel.isCollapsed || !pill) {
    if (pill) pill.style.display = 'none';
    return;
  }
  const text = sel.toString().trim();
  if (text.length >= 15 && text.length <= 400) {
    const range = sel.getRangeAt(0);
    const rect = range.getBoundingClientRect();
    activeQuoteSelected = text;
    pill.style.left = (window.scrollX + rect.left + rect.width / 2 - 60) + 'px';
    pill.style.top = (window.scrollY + rect.top - 40) + 'px';
    pill.style.display = 'inline-flex';
  } else {
    pill.style.display = 'none';
  }
});

const quotePill = document.getElementById('quote-share-pill');
if (quotePill) {
  quotePill.addEventListener('click', () => {
    if (!activeQuoteSelected) return;
    const modal = document.getElementById('quote-share-modal');
    const quoteTextEl = document.getElementById('quote-card-text');
    if (quoteTextEl) quoteTextEl.innerText = `"${activeQuoteSelected}"`;
    if (modal) modal.style.display = 'flex';
    quotePill.style.display = 'none';
  });
}

function copyQuoteText() {
  if (!activeQuoteSelected) return;
  const citation = `"${activeQuoteSelected}" — <?= htmlspecialchars(addslashes($article['author_name'] ?? 'TechScout')) ?> in <?= htmlspecialchars(addslashes($article['title'])) ?> (<?= url('/article/' . ($article['slug'] ?? '')) ?>)`;
  navigator.clipboard.writeText(citation).then(() => {
    if (typeof showToast === 'function') showToast('Quote copied to clipboard! 📋', 'success');
  });
}

function tweetQuoteCard() {
  if (!activeQuoteSelected) return;
  const tweetText = `"${activeQuoteSelected.substring(0, 200)}..." via @TechScout`;
  const tweetUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(tweetText)}&url=${encodeURIComponent(window.location.href)}`;
  window.open(tweetUrl, '_blank');
}

document.addEventListener('DOMContentLoaded', () => {
  const content = document.querySelector('.article-body-content');
  const tocList = document.getElementById('article-toc-list');
  const tocCard = document.getElementById('article-toc-card');
  const tocToggleText = document.getElementById('toc-toggle-text');

  if (!content || !tocList) return;

  const headings = content.querySelectorAll('h2, h3');
  if (headings.length <= 1) {
    if (tocCard) tocCard.style.display = 'none';
    return;
  }

  if (tocToggleText) {
    // Collapsed by default per specification [TS-ED-02]
    tocToggleText.innerText = `Show Sections (${headings.length}) ▾`;
  }

  headings.forEach((heading, index) => {
    let id = heading.id;
    if (!id) {
      id = heading.innerText.toLowerCase().trim()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
      if (!id) id = `section-${index + 1}`;
      heading.id = id;
    }
    heading.style.scrollMarginTop = '100px';

    const isH3 = heading.tagName.toLowerCase() === 'h3';
    const li = document.createElement('li');

    const cleanTitle = heading.innerText.trim();
    const a = document.createElement('a');
    a.href = `#${id}`;
    a.innerText = isH3 ? ('↳ ' + cleanTitle) : cleanTitle;
    a.style.color = isH3 ? 'var(--ts-text-secondary)' : 'var(--ts-text-primary)';
    a.style.fontSize = isH3 ? '0.85rem' : '0.92rem';
    a.style.paddingLeft = isH3 ? '0.75rem' : '0';
    a.style.textDecoration = 'none';
    a.style.display = 'block';

    a.addEventListener('click', (e) => {
      e.preventDefault();
      const target = document.getElementById(id);
      if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        history.pushState(null, null, `#${id}`);
      }
    });

    li.appendChild(a);
    tocList.appendChild(li);
  });

  // =========================================================================
  // MULTI-SESSION RE-READ & SCROLL TELEMETRY ENGINE
  // =========================================================================
  const isAuth = <?= \App\Core\Auth::check() ? 'true' : 'false' ?>;
  const contentId = <?= (int)$article['id'] ?>;
  const progressBar = document.getElementById('article-reading-progress-bar');
  const sessionBadge = document.getElementById('reading-session-badge');

  if (isAuth && contentId) {
    let currentHistoryId = 0;
    let currentSessionNumber = 1;
    let localMaxProgress = 0;
    let sessionSeconds = 0;
    let lastRecordedProgress = -1;
    let isFlushing = false;

    // Unique per-tab reading session token
    let clientSessionId = sessionStorage.getItem('ts_reading_token_' + contentId);
    if (!clientSessionId) {
      clientSessionId = 'sess_' + Date.now() + '_' + Math.random().toString(36).substring(2, 9);
      sessionStorage.setItem('ts_reading_token_' + contentId, clientSessionId);
    }

    // Initialize session with backend
    fetch('<?= url('/api/v1/history/init-session') ?>', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({
        content_id: contentId,
        session_id: clientSessionId
      })
    })
    .then(r => r.json())
    .then(data => {
      if (data && data.success) {
        currentHistoryId = data.history_id;
        currentSessionNumber = data.session_number;
        localMaxProgress = data.starting_progress || 0;

        if (progressBar) {
          progressBar.style.width = Math.max(localMaxProgress, 0) + '%';
        }

        if (sessionBadge) {
          if (data.is_reread) {
            sessionBadge.innerHTML = `<i data-lucide="repeat" style="width: 11px; height: 11px;"></i> Re-reading &bull; Session #${currentSessionNumber}`;
            sessionBadge.className = 'ts-badge ts-badge-pink';
          } else if (currentSessionNumber > 1) {
            sessionBadge.innerHTML = `<i data-lucide="book-open" style="width: 11px; height: 11px;"></i> Session #${currentSessionNumber}`;
          } else {
            sessionBadge.innerHTML = `<i data-lucide="book-open" style="width: 11px; height: 11px;"></i> Reading Session #1`;
          }
          sessionBadge.style.display = 'inline-flex';
          sessionBadge.style.alignItems = 'center';
          sessionBadge.style.gap = '0.3rem';
          if (window.lucide) lucide.createIcons();
        }
      }
    })
    .catch(err => console.error('Reading session init error:', err));

    // Active reading timer
    let timerActive = true;
    document.addEventListener('visibilitychange', () => {
      timerActive = (document.visibilityState === 'visible');
    });

    setInterval(() => {
      if (timerActive) {
        sessionSeconds += 1;
        // Periodically sync progress every 6 seconds if reading or changed
        if (sessionSeconds % 6 === 0) {
          syncProgress();
        }
      }
    }, 1000);

    // 10-Second Verified Read Beacon (Monetization & Analytics Quality Gate)
    let dwell10sPingSent = false;
    let totalDwellTimer = 0;
    setInterval(() => {
      if (timerActive && !dwell10sPingSent) {
        totalDwellTimer += 1;
        if (totalDwellTimer >= 10) {
          dwell10sPingSent = true;
          fetch('<?= url('/api/v1/content/read-ping') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
              content_id: contentId,
              duration_seconds: 10
            })
          }).then(r => r.json()).then(res => {
            if (res && res.verified_10s_read) {
              console.log('✓ 10s Verified Read Credited towards Monetization');
            }
          }).catch(() => {});
        }
      }
    }, 1000);

    // Scroll progress calculator
    const articleBody = document.querySelector('.article-body-content') || document.querySelector('article');
    function calculateScrollProgress() {
      if (!articleBody) return 0;
      const rect = articleBody.getBoundingClientRect();
      const windowHeight = window.innerHeight;
      const totalHeight = rect.height;
      
      const scrolled = windowHeight - rect.top;
      if (scrolled <= 0) return 0;
      const pct = Math.min(100, Math.max(0, Math.round((scrolled / (totalHeight + windowHeight * 0.3)) * 100)));
      return pct;
    }

    let scrollTimeout = null;
    window.addEventListener('scroll', () => {
      const currentPct = calculateScrollProgress();
      if (currentPct > localMaxProgress) {
        localMaxProgress = currentPct;
        if (progressBar) {
          progressBar.style.width = localMaxProgress + '%';
        }
      }

      if (scrollTimeout) clearTimeout(scrollTimeout);
      scrollTimeout = setTimeout(() => {
        syncProgress();
      }, 800);
    }, { passive: true });

    function syncProgress() {
      if (!currentHistoryId && !clientSessionId) return;
      if (localMaxProgress === lastRecordedProgress && sessionSeconds < 5) return;

      const secondsDelta = sessionSeconds;
      sessionSeconds = 0; // reset delta
      lastRecordedProgress = localMaxProgress;

      fetch('<?= url('/api/v1/history/record') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
          content_id: contentId,
          history_id: currentHistoryId,
          session_id: clientSessionId,
          progress_percent: localMaxProgress,
          read_seconds: secondsDelta
        })
      })
      .then(r => r.json())
      .catch(() => {});
    }

    // Flush remaining read seconds when leaving page
    window.addEventListener('beforeunload', () => {
      if (sessionSeconds > 0 || localMaxProgress > lastRecordedProgress) {
        const payload = new URLSearchParams({
          content_id: contentId,
          history_id: currentHistoryId,
          session_id: clientSessionId,
          progress_percent: localMaxProgress,
          read_seconds: sessionSeconds
        });
        navigator.sendBeacon('<?= url('/api/v1/history/record') ?>', payload);
      }
    });
  }

  // [TS-AUT-70] Code Block Interactive Copy Buttons & Telemetry Tracking
  const codeBlocks = document.querySelectorAll('.article-body-content pre');
  codeBlocks.forEach((pre) => {
    if (pre.querySelector('.ts-code-copy-btn')) return;
    pre.style.position = 'relative';
    const codeEl = pre.querySelector('code') || pre;

    const copyBtn = document.createElement('button');
    copyBtn.type = 'button';
    copyBtn.className = 'ts-code-copy-btn';
    copyBtn.innerHTML = '📋 Copy';
    copyBtn.setAttribute('title', 'Copy code snippet');
    copyBtn.style.cssText = 'position: absolute; top: 8px; right: 8px; font-size: 0.72rem; padding: 4px 10px; background: rgba(15,23,42,0.85); color: #94a3b8; border: 1px solid rgba(148,163,184,0.3); border-radius: 6px; cursor: pointer; backdrop-filter: blur(6px); transition: all 0.2s ease; z-index: 10; font-family: var(--ts-font-mono, monospace);';

    copyBtn.addEventListener('mouseenter', () => {
      copyBtn.style.color = '#38bdf8';
      copyBtn.style.borderColor = '#38bdf8';
      copyBtn.style.boxShadow = '0 0 10px rgba(56,189,248,0.2)';
    });
    copyBtn.addEventListener('mouseleave', () => {
      if (copyBtn.innerHTML !== '✅ Copied!') {
        copyBtn.style.color = '#94a3b8';
        copyBtn.style.borderColor = 'rgba(148,163,184,0.3)';
        copyBtn.style.boxShadow = 'none';
      }
    });

    copyBtn.addEventListener('click', async (e) => {
      e.preventDefault();
      const rawText = codeEl.innerText || codeEl.textContent || '';
      try {
        await navigator.clipboard.writeText(rawText);
        copyBtn.innerHTML = '✅ Copied!';
        copyBtn.style.color = '#34d399';
        copyBtn.style.borderColor = '#34d399';
        setTimeout(() => {
          copyBtn.innerHTML = '📋 Copy';
          copyBtn.style.color = '#94a3b8';
          copyBtn.style.borderColor = 'rgba(148,163,184,0.3)';
          copyBtn.style.boxShadow = 'none';
        }, 2200);

        // Send telemetry event to backend
        const contentId = '<?= (int)$article['id'] ?>';
        const langMatch = (codeEl.className || '').match(/language-([a-zA-Z0-9_-]+)/);
        const lang = langMatch ? langMatch[1] : 'code';
        fetch('<?= url('/api/v1/telemetry/code-copy') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            content_id: contentId,
            language: lang,
            snippet_length: rawText.length,
            code_preview: rawText.substring(0, 100)
          })
        }).catch(() => {});
      } catch (err) {
        console.warn('Clipboard write error:', err);
      }
    });

    pre.appendChild(copyBtn);
  });

  // Auto-trigger Tip Modal if URL has #tip or ?tip=1
  const tipParams = new URLSearchParams(window.location.search);
  if (window.location.hash === '#tip' || tipParams.get('tip') === '1') {
    setTimeout(() => {
      if (typeof openAuthorTipModal === 'function') {
        openAuthorTipModal();
      }
    }, 350);
  }
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
