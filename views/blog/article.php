<?php require __DIR__ . '/../layouts/header.php'; ?>

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
    <!-- Breadcrumbs & Topic Follow Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; font-size: 0.85rem; color: var(--ts-text-tertiary); margin-bottom: 1.25rem;">
      <div style="display: flex; gap: 0.5rem; align-items: center;">
        <a href="<?= url('/') ?>" style="color: var(--ts-text-tertiary); text-decoration: none;">Home</a> <span>/</span>
        <a href="<?= url('/blog') ?>" style="color: var(--ts-text-tertiary); text-decoration: none;">Blog</a> <span>/</span>
        <a href="<?= url('/blog/category/' . ($article['category_slug'] ?? '')) ?>" style="color: var(--ts-accent-light); font-weight: 600; text-decoration: none;">
          <?= htmlspecialchars($article['category_name'] ?? 'Architecture') ?>
        </a>
      </div>

      <?php if (!empty($article['topic_name'])): ?>
        <button type="button" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.15rem 0.6rem; font-size: 0.75rem; border-radius: 9999px;" onclick="toggleTopicSubscription(<?= (int)$article['topic_id'] ?>, this)">
          <i data-lucide="plus" style="width: 12px; height: 12px;"></i>
          <span>Follow <?= htmlspecialchars($article['topic_name']) ?></span>
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

      <a href="<?= url('/category/' . ($article['category_slug'] ?? 'ai')) ?>" class="ts-badge ts-badge-cyan" style="text-decoration: none;">
        <?= htmlspecialchars($article['category_name'] ?? 'Artificial Intelligence') ?>
      </a>
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

    <!-- Table of Contents Container -->
    <div id="article-toc-card" class="article-toc-card" style="background: var(--ts-card-bg); border: 1px solid var(--ts-border-main); border-radius: var(--ts-radius-md); padding: 1.25rem; margin-bottom: 2rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
        <h3 style="font-size: 0.95rem; font-weight: 700; color: var(--ts-text-primary); margin: 0; text-transform: uppercase; letter-spacing: 0.05em;">Table of Contents</h3>
        <button type="button" onclick="toggleMobileToc()" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
          <span id="toc-toggle-text">Show Sections</span> ▾
        </button>
      </div>
      <div id="article-toc-wrapper" class="article-toc-list-collapsible">
        <ul id="article-toc-list" class="article-toc-list" style="list-style: none; padding-left: 0; margin: 0; display: flex; flex-direction: column; gap: 0.4rem;"></ul>
      </div>
    </div>

    <!-- Main Editorial Content -->
    <div class="article-body-content" style="font-size: 1.08rem; line-height: 1.85; color: var(--ts-text-primary); width: 100%;">
      <?= $article['body_html'] ?>
    </div>

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
        <button class="reaction-btn" data-content-id="<?= $article['id'] ?>" data-reaction="like">🔥 Fire <span class="reaction-count"><?= $article['reaction_count'] ?></span></button>
        <button class="reaction-btn" data-content-id="<?= $article['id'] ?>" data-reaction="rocket">🚀 Rocket</button>
        <button class="reaction-btn" data-content-id="<?= $article['id'] ?>" data-reaction="insightful">💡 Insightful</button>
        <button class="reaction-btn" data-content-id="<?= $article['id'] ?>" data-reaction="mind_blown">🤯 Mind Blown</button>
        <button class="reaction-btn" data-content-id="<?= $article['id'] ?>" data-reaction="clap">👏 Clap</button>
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

  const isClosed = wrapper.classList.contains('closed');
  if (isClosed) {
    wrapper.classList.remove('closed');
    if (toggleText) toggleText.innerText = 'Hide Sections';
  } else {
    wrapper.classList.add('closed');
    if (toggleText) toggleText.innerText = 'Show Sections';
  }
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
    tocToggleText.innerText = `Hide (${headings.length} Sections)`;
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
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
