<?php 
require __DIR__ . '/../layouts/header.php'; 

$isOwner = (\App\Core\Auth::id() && (int)\App\Core\Auth::id() === (int)($author['user_id'] ?? 0)) || \App\Core\Auth::isAdmin();

// Platform treasury wallets for author tipping [TS-CR-05]
$tipWallets = [
    'BTC' => \App\Core\Database::queryScalar("SELECT key_value FROM site_settings WHERE key_name = 'crypto_wallet_btc' LIMIT 1") ?: 'bc1qtechscoutcrypto7482910482910btcxx',
    'ETH' => \App\Core\Database::queryScalar("SELECT key_value FROM site_settings WHERE key_name = 'crypto_wallet_eth' LIMIT 1") ?: '0x71C8A1bB722956276f5789129486c9A199859187',
    'USDT' => \App\Core\Database::queryScalar("SELECT key_value FROM site_settings WHERE key_name = 'crypto_wallet_usdt' LIMIT 1") ?: '0x71C8A1bB722956276f5789129486c9A199859187',
    'SOL' => \App\Core\Database::queryScalar("SELECT key_value FROM site_settings WHERE key_name = 'crypto_wallet_sol' LIMIT 1") ?: 'So11111111111111111111111111111111111111112',
];
?>

<div class="container" style="padding-top: 3rem; margin-bottom: 5rem;">
  <!-- Author Profile Hero Card -->
  <div class="ts-card author-profile-hero-card" style="position: relative; overflow: hidden; padding: 2.5rem; margin-bottom: 2.5rem; border: 1px solid var(--ts-border-main); border-radius: var(--ts-radius-lg);">
    <div style="display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap;">
      <!-- Avatar -->
      <div style="position: relative;">
        <img src="<?= htmlspecialchars(image_url($author['avatar_url'] ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=300&auto=format&fit=crop&q=80')) ?>" alt="<?= htmlspecialchars($author['display_name'] ?? 'Author') ?>" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid var(--ts-primary); box-shadow: 0 0 25px rgba(99,102,241,0.4);">
        <span class="ts-badge ts-badge-cyan" style="position: absolute; bottom: -5px; left: 50%; transform: translateX(-50%); font-size: 0.65rem; padding: 0.1rem 0.5rem; white-space: nowrap;">
          <?= htmlspecialchars($author['verification_badge'] ?? 'verified') ?>
        </span>
      </div>

      <!-- Author Bio & Details -->
      <div style="flex: 1; min-width: 280px;">
        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 0.25rem;">
          <h1 style="font-size: 2rem; margin: 0; font-weight: 800; color: var(--ts-text-primary);">
            <?= htmlspecialchars($author['display_name'] ?? $author['username']) ?>
          </h1>
          <span class="ts-badge ts-badge-pink" style="font-size: 0.75rem;">
            <?= htmlspecialchars($author['reputation_level'] ?? 'Scout Builder') ?> &bull; <?= number_format($author['reputation_score'] ?? 100) ?> PTS
          </span>
        </div>
        <div style="font-size: 0.9rem; color: #64748b; margin-bottom: 0.75rem;">
          @<?= htmlspecialchars($author['username']) ?> &bull; Contributor on TechScout
        </div>
        
        <p style="font-size: 1rem; color: var(--ts-text-secondary); line-height: 1.6; max-width: 700px; margin-bottom: 1.25rem;">
          <?= htmlspecialchars($author['headline'] ?? $author['bio'] ?? 'Systems architect, deep learning researcher, and open-source contributor.') ?>
        </p>

        <!-- Follow Action, Crypto Tip Trigger, Follower Counts & Privacy Controls -->
        <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap; margin-bottom: 1rem;">
          <?php if (\App\Core\Auth::id() !== (int)$author['user_id']): ?>
            <button type="button" onclick="toggleAuthorFollow(<?= (int)$author['id'] ?>, this)" id="btn-follow-author" class="ts-btn <?= !empty($isFollowing) ? 'ts-btn-outline' : 'ts-btn-primary' ?> ts-btn-sm" style="<?= !empty($isFollowing) ? 'color: #38bdf8; border-color: #38bdf8;' : 'background: linear-gradient(135deg, #6366f1, #38bdf8); font-weight: 700;' ?>">
              <i data-lucide="<?= !empty($isFollowing) ? 'check' : 'bell' ?>" style="width: 13px; height: 13px;"></i>
              <span id="follow-author-label"><?= !empty($isFollowing) ? 'Following' : 'Follow Author' ?></span>
            </button>
          <?php endif; ?>

          <!-- [TS-CR-05] Crypto Tip Author Button -->
          <button type="button" onclick="openAuthorTipModal()" id="btn-tip-author" class="ts-btn ts-btn-sm" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: #ffffff; font-weight: 700; box-shadow: 0 4px 15px rgba(245,158,11,0.35); display: inline-flex; align-items: center; gap: 0.4rem; border: none; padding: 0.35rem 0.85rem; border-radius: 8px; cursor: pointer;">
            <i data-lucide="zap" style="width: 14px; height: 14px;"></i>
            <span>Tip Author (Crypto)</span>
          </button>

          <span class="ts-badge" style="background: rgba(56,189,248,0.12); color: #38bdf8; border-color: rgba(56,189,248,0.3); padding: 0.4rem 0.75rem; font-size: 0.82rem; display: inline-flex; align-items: center; gap: 0.35rem;" title="Verified Creator Followers">
            <i data-lucide="users" style="width: 13px;"></i>
            <strong id="author-follower-count"><?= number_format((int)($followerCount ?? 0)) ?></strong> Followers
          </span>

          <span class="ts-badge" style="background: rgba(168,85,247,0.12); color: #c084fc; border-color: rgba(168,85,247,0.3); padding: 0.4rem 0.75rem; font-size: 0.82rem; display: inline-flex; align-items: center; gap: 0.35rem;" title="Creators Followed">
            <i data-lucide="user-plus" style="width: 13px;"></i>
            <strong><?= number_format((int)($followingCount ?? 0)) ?></strong> Following
          </span>

          <?php if (!empty($isAdmin)): ?>
            <button type="button" onclick="switchAuthorTab('followers')" class="ts-badge" style="cursor: pointer; background: rgba(245,158,11,0.12); color: #fbbf24; border-color: rgba(245,158,11,0.3); padding: 0.4rem 0.65rem; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.25rem;" title="Admin Inspection: View Raw Follower Profiles">
              🔒 Admin Audit View
            </button>
          <?php endif; ?>
        </div>

        <!-- Dynamic Developer Social Media Channels & Custom Links -->
        <div style="margin-top: 0.5rem;">
          <?= \App\Core\SocialHelper::renderBadges($author['social_links'] ?? []) ?>
        </div>
      </div>

      <!-- Author Stats Telemetry Box -->
      <div class="author-stats-telemetry-box" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; padding: 1.25rem; border-radius: var(--ts-radius-md); border: 1px solid var(--ts-border-main); min-width: 220px;">
        <div style="text-align: center;">
          <div style="font-size: 1.4rem; font-weight: 800; color: #38bdf8;"><?= number_format($stats['total_views'] ?? 0) ?></div>
          <div style="font-size: 0.75rem; color: #64748b;">Total Reads</div>
        </div>
        <div style="text-align: center;">
          <div style="font-size: 1.4rem; font-weight: 800; color: #ec4899;"><?= number_format($stats['total_reactions'] ?? 0) ?></div>
          <div style="font-size: 0.75rem; color: #64748b;">Reactions</div>
        </div>
        <div style="text-align: center;">
          <div style="font-size: 1.4rem; font-weight: 800; color: #34d399;"><?= number_format($stats['total_published'] ?? 0) ?></div>
          <div style="font-size: 0.75rem; color: #64748b;">Publications</div>
        </div>
        <div style="text-align: center;">
          <div style="font-size: 1.4rem; font-weight: 800; color: #f59e0b;"><?= number_format($stats['total_comments'] ?? 0) ?></div>
          <div style="font-size: 0.75rem; color: #64748b;">Discussions</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Content Type Filter Tabs -->
  <div style="display: flex; gap: 0.6rem; margin-bottom: 2rem; border-bottom: 1px solid var(--ts-border-main); padding-bottom: 0.75rem; flex-wrap: wrap;">
    <button onclick="switchAuthorTab('articles')" id="tab-btn-articles" class="ts-btn ts-btn-sm <?= $activeTab === 'articles' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="border-radius: 9999px;">
      <i data-lucide="book-open" style="width: 14px;"></i> Articles (<?= count($articles) ?>)
    </button>
    <button onclick="switchAuthorTab('gpts')" id="tab-btn-gpts" class="ts-btn ts-btn-sm <?= $activeTab === 'gpts' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="border-radius: 9999px;">
      <i data-lucide="bot" style="width: 14px;"></i> Custom GPTs (<?= count($gpts) ?>)
    </button>
    <button onclick="switchAuthorTab('prompts')" id="tab-btn-prompts" class="ts-btn ts-btn-sm <?= $activeTab === 'prompts' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="border-radius: 9999px;">
      <i data-lucide="terminal" style="width: 14px;"></i> Prompt Blueprints (<?= count($prompts) ?>)
    </button>
    <button onclick="switchAuthorTab('tools')" id="tab-btn-tools" class="ts-btn ts-btn-sm <?= $activeTab === 'tools' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="border-radius: 9999px;">
      <i data-lucide="radar" style="width: 14px;"></i> AI Tools (<?= count($tools) ?>)
    </button>
    <?php if (!empty($isAdmin)): ?>
      <button onclick="switchAuthorTab('followers')" id="tab-btn-followers" class="ts-btn ts-btn-sm <?= $activeTab === 'followers' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="border-radius: 9999px; color: #38bdf8; border-color: rgba(56,189,248,0.3);">
        <i data-lucide="users" style="width: 14px;"></i> 🔒 Followers (<?= number_format($followerCount) ?>)
      </button>
      <button onclick="switchAuthorTab('following')" id="tab-btn-following" class="ts-btn ts-btn-sm <?= $activeTab === 'following' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="border-radius: 9999px; color: #c084fc; border-color: rgba(168,85,247,0.3);">
        <i data-lucide="user-plus" style="width: 14px;"></i> 🔒 Following (<?= number_format($followingCount) ?>)
      </button>
    <?php endif; ?>
  </div>

  <!-- Tab 1: Articles -->
  <div id="author-tab-articles" class="author-tab-content" style="<?= $activeTab === 'articles' ? 'display: block;' : 'display: none;' ?>">
    <?php if (!empty($articles)): ?>
      <div class="ts-grid-3">
        <?php foreach ($articles as $art): ?>
          <article class="ts-card" style="display: flex; flex-direction: column;">
            <?php if (!empty($art['featured_image_url'])): ?>
              <img src="<?= htmlspecialchars(image_url($art['featured_image_url'])) ?>" alt="<?= htmlspecialchars($art['title']) ?>" class="article-card-img" style="height: 160px; object-fit: cover; border-radius: var(--ts-radius-md); margin-bottom: 0.75rem;">
            <?php endif; ?>
            <div style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;">
              <span class="ts-badge"><?= htmlspecialchars($art['category_name'] ?? 'Architecture') ?></span>
              <span class="ts-badge ts-badge-cyan"><?= $art['reading_time_mins'] ?? 5 ?> min read</span>
            </div>
            <h3 class="article-card-title" style="font-size: 1.15rem; margin-bottom: 0.5rem;">
              <a href="<?= url('/article/' . $art['slug']) ?>" style="color: #f8fafc; text-decoration: none;"><?= htmlspecialchars($art['title']) ?></a>
            </h3>
            <p style="font-size: 0.85rem; color: var(--ts-text-secondary); line-height: 1.5; flex: 1; margin-bottom: 1rem;">
              <?= htmlspecialchars($art['summary']) ?>
            </p>
            <div style="font-size: 0.75rem; color: #64748b; margin-top: auto;">
              Published <?= date('M j, Y', strtotime($art['published_at'])) ?> &bull; <?= number_format($art['view_count']) ?> reads
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="ts-card" style="text-align: center; padding: 3rem; color: #64748b;">
        No published articles yet.
      </div>
    <?php endif; ?>
  </div>

  <!-- Tab 2: Custom GPTs -->
  <div id="author-tab-gpts" class="author-tab-content" style="<?= $activeTab === 'gpts' ? 'display: block;' : 'display: none;' ?>">
    <?php if (!empty($gpts)): ?>
      <div class="ts-grid-3">
        <?php foreach ($gpts as $gpt): ?>
          <div class="ts-card" style="display: flex; flex-direction: column;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
              <span class="ts-badge ts-badge-cyan"><?= htmlspecialchars($gpt['model_base'] ?? 'GPT-4o') ?></span>
              <span class="ts-badge ts-badge-pink">Custom Agent</span>
            </div>
            <h3 style="font-size: 1.15rem; margin-bottom: 0.5rem;">
              <a href="<?= url('/gpts/' . $gpt['slug']) ?>" style="color: #f8fafc; text-decoration: none;"><?= htmlspecialchars($gpt['title']) ?></a>
            </h3>
            <p style="font-size: 0.85rem; color: var(--ts-text-secondary); flex: 1; margin-bottom: 1rem;">
              <?= htmlspecialchars($gpt['summary']) ?>
            </p>
            <a href="<?= url('/gpts/' . $gpt['slug']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%;">
              View Blueprint & Specs →
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="ts-card" style="text-align: center; padding: 3rem; color: #64748b;">
        No custom GPT agents published yet.
      </div>
    <?php endif; ?>
  </div>

  <!-- Tab 3: Prompts -->
  <div id="author-tab-prompts" class="author-tab-content" style="<?= $activeTab === 'prompts' ? 'display: block;' : 'display: none;' ?>">
    <?php if (!empty($prompts)): ?>
      <div class="ts-grid-3">
        <?php foreach ($prompts as $p): ?>
          <div class="ts-card" style="display: flex; flex-direction: column;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
              <span class="ts-badge ts-badge-green">Prompt Template</span>
              <span style="font-size: 0.75rem; color: #64748b;"><?= $p['copy_count'] ?? 0 ?> copies</span>
            </div>
            <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem;">
              <a href="<?= url('/prompts/' . $p['slug']) ?>" style="color: #f8fafc; text-decoration: none;"><?= htmlspecialchars($p['title']) ?></a>
            </h3>
            <p style="font-size: 0.85rem; color: var(--ts-text-secondary); flex: 1; margin-bottom: 1rem;">
              <?= htmlspecialchars($p['summary']) ?>
            </p>
            <a href="<?= url('/prompts/' . $p['slug']) ?>" class="ts-btn ts-btn-primary ts-btn-sm" style="width: 100%;">
              Inject & Copy ⚡
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="ts-card" style="text-align: center; padding: 3rem; color: #64748b;">
        No prompt blueprints published yet.
      </div>
    <?php endif; ?>
  </div>

  <!-- Tab 4: AI Tools -->
  <div id="author-tab-tools" class="author-tab-content" style="<?= $activeTab === 'tools' ? 'display: block;' : 'display: none;' ?>">
    <?php if (!empty($tools)): ?>
      <div class="ts-grid-3">
        <?php foreach ($tools as $tool): ?>
          <div class="ts-card" style="display: flex; flex-direction: column;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
              <span class="ts-badge ts-badge-pink"><?= htmlspecialchars($tool['pricing_model'] ?? 'Freemium') ?></span>
              <span style="font-size: 0.8rem; color: #f59e0b; font-weight: 700;">⭐ <?= number_format((float)($tool['rating_avg'] ?? 5.0), 1) ?></span>
            </div>
            <h3 style="font-size: 1.15rem; margin-bottom: 0.5rem;">
              <a href="<?= url('/tools/' . $tool['slug']) ?>" style="color: #f8fafc; text-decoration: none;"><?= htmlspecialchars($tool['title']) ?></a>
            </h3>
            <p style="font-size: 0.85rem; color: var(--ts-text-secondary); flex: 1; margin-bottom: 1rem;">
              <?= htmlspecialchars($tool['summary']) ?>
            </p>
            <a href="<?= url('/tools/' . $tool['slug']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%;">
              View Tool Teardown →
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="ts-card" style="text-align: center; padding: 3rem; color: #64748b;">
        No AI tools added yet.
      </div>
    <?php endif; ?>
  </div>

  <?php if (!empty($isAdmin)): ?>
    <!-- Tab 5: Followers Roster (Admin Inspection Only) -->
    <div id="author-tab-followers" class="author-tab-content" style="<?= $activeTab === 'followers' ? 'display: block;' : 'display: none;' ?>">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.5rem;">
        <div>
          <div style="display: inline-flex; align-items: center; gap: 0.4rem; background: rgba(245,158,11,0.12); color: #fbbf24; border: 1px solid rgba(245,158,11,0.3); border-radius: 9999px; padding: 0.2rem 0.65rem; font-size: 0.72rem; font-weight: 700; margin-bottom: 0.4rem;">
            🔒 ADMINISTRATOR AUDIT ROSTER • RESTRICTED FROM PUBLIC
          </div>
          <h3 style="font-size: 1.2rem; color: var(--ts-text-primary); margin: 0; font-family: 'Outfit', sans-serif;">
            👥 Creator Followers (Total: <?= number_format($followerCount) ?>)
          </h3>
          <p style="font-size: 0.8rem; color: #64748b; margin: 0.15rem 0 0 0;">
            Showing <?= count($followers ?? []) ?> direct member accounts. Public visitors and creators only see the verified numerical count badge.
          </p>
        </div>
      </div>

      <?php if (!empty($followers)): ?>
        <div class="ts-grid-3">
          <?php foreach ($followers as $flw): ?>
            <div class="ts-card" style="display: flex; flex-direction: column; gap: 0.75rem; border: 1px solid var(--ts-border-main);">
              <div style="display: flex; gap: 0.75rem; align-items: center;">
                <?php if (!empty($flw['avatar_url'])): ?>
                  <img src="<?= htmlspecialchars(image_url($flw['avatar_url'])) ?>" alt="<?= htmlspecialchars($flw['display_name'] ?? $flw['username']) ?>" style="width: 46px; height: 46px; border-radius: 50%; object-fit: cover; border: 1px solid #38bdf8;">
                <?php else: ?>
                  <div style="width: 46px; height: 46px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #38bdf8); display: flex; align-items: center; justify-content: center; font-weight: 800; color: #fff; font-size: 1.1rem;">
                    <?= strtoupper(substr($flw['display_name'] ?? $flw['username'] ?? 'U', 0, 1)) ?>
                  </div>
                <?php endif; ?>

                <div style="overflow: hidden; flex: 1;">
                  <div style="font-weight: 700; color: var(--ts-text-primary); font-size: 0.95rem; display: flex; align-items: center; gap: 0.35rem;">
                    <?php if (!empty($flw['author_slug'])): ?>
                      <a href="<?= url('/author/profile/' . $flw['author_slug']) ?>" style="color: var(--ts-text-primary); text-decoration: none;">
                        <?= htmlspecialchars($flw['display_name'] ?? $flw['username']) ?> ↗
                      </a>
                    <?php else: ?>
                      <?= htmlspecialchars($flw['display_name'] ?? $flw['username']) ?>
                    <?php endif; ?>

                    <?php if (!empty($flw['verification_badge'])): ?>
                      <span class="ts-badge ts-badge-green" style="font-size: 0.6rem; padding: 0.05rem 0.3rem;">✓</span>
                    <?php endif; ?>
                  </div>
                  <div style="font-size: 0.75rem; color: #64748b; font-family: var(--ts-font-mono);">
                    @<?= htmlspecialchars($flw['username']) ?>
                  </div>
                </div>
              </div>

              <?php if (!empty($flw['headline'])): ?>
                <p style="font-size: 0.8rem; color: var(--ts-text-secondary); line-height: 1.4; margin: 0; flex: 1;">
                  <?= htmlspecialchars($flw['headline']) ?>
                </p>
              <?php endif; ?>

              <div style="display: flex; justify-content: space-between; align-items: center; margin-top: auto; padding-top: 0.5rem; border-top: 1px solid var(--ts-border-subtle); font-size: 0.72rem; color: #64748b;">
                <span>Followed <?= date('M j, Y', strtotime($flw['followed_at'])) ?></span>
                <?php if (!empty($flw['author_id']) && \App\Core\Auth::id() !== (int)$flw['user_id']): ?>
                  <button type="button" onclick="toggleAuthorFollow(<?= (int)$flw['author_id'] ?>, this)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.68rem; padding: 0.2rem 0.5rem; color: #38bdf8; border-color: rgba(56,189,248,0.3);">
                    Follow 🔔
                  </button>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="ts-card" style="text-align: center; padding: 3rem; color: #64748b;">
          <i data-lucide="users" style="width: 40px; height: 40px; margin-bottom: 0.75rem; color: #38bdf8;"></i>
          <h4 style="color: var(--ts-text-primary); margin: 0 0 0.35rem 0;">No followers yet</h4>
          <p style="font-size: 0.85rem; color: #64748b; margin: 0;">Share your research and prompts to build your follower base!</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Tab 6: Following Authors Roster (Admin Inspection Only) -->
    <div id="author-tab-following" class="author-tab-content" style="<?= $activeTab === 'following' ? 'display: block;' : 'display: none;' ?>">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.5rem;">
        <div>
          <div style="display: inline-flex; align-items: center; gap: 0.4rem; background: rgba(245,158,11,0.12); color: #fbbf24; border: 1px solid rgba(245,158,11,0.3); border-radius: 9999px; padding: 0.2rem 0.65rem; font-size: 0.72rem; font-weight: 700; margin-bottom: 0.4rem;">
            🔒 ADMINISTRATOR AUDIT ROSTER • RESTRICTED FROM PUBLIC
          </div>
          <h3 style="font-size: 1.2rem; color: var(--ts-text-primary); margin: 0; font-family: 'Outfit', sans-serif;">
            🔗 Creators Followed (Total: <?= number_format($followingCount) ?>)
          </h3>
          <p style="font-size: 0.8rem; color: #64748b; margin: 0.15rem 0 0 0;">Technical architects and researchers in this creator's network</p>
        </div>
      </div>

      <?php if (!empty($following)): ?>
        <div class="ts-grid-3">
          <?php foreach ($following as $fw): ?>
            <div class="ts-card" style="display: flex; flex-direction: column; gap: 0.75rem; border: 1px solid var(--ts-border-main);">
              <div style="display: flex; gap: 0.75rem; align-items: center;">
                <?php if (!empty($fw['avatar_url'])): ?>
                  <img src="<?= htmlspecialchars(image_url($fw['avatar_url'])) ?>" alt="<?= htmlspecialchars($fw['display_name']) ?>" style="width: 46px; height: 46px; border-radius: 50%; object-fit: cover; border: 1px solid #c084fc;">
                <?php else: ?>
                  <div style="width: 46px; height: 46px; border-radius: 50%; background: linear-gradient(135deg, #a855f7, #ec4899); display: flex; align-items: center; justify-content: center; font-weight: 800; color: #fff; font-size: 1.1rem;">
                    <?= strtoupper(substr($fw['display_name'] ?? 'A', 0, 1)) ?>
                  </div>
                <?php endif; ?>

                <div style="overflow: hidden; flex: 1;">
                  <div style="font-weight: 700; color: var(--ts-text-primary); font-size: 0.95rem; display: flex; align-items: center; gap: 0.35rem;">
                    <a href="<?= url('/author/profile/' . $fw['author_slug']) ?>" style="color: var(--ts-text-primary); text-decoration: none;">
                      <?= htmlspecialchars($fw['display_name']) ?> ↗
                    </a>
                    <?php if (!empty($fw['verification_badge'])): ?>
                      <span class="ts-badge ts-badge-green" style="font-size: 0.6rem; padding: 0.05rem 0.3rem;">✓</span>
                    <?php endif; ?>
                  </div>
                  <div style="font-size: 0.72rem; color: #c084fc; font-weight: 600;">
                    ⭐ <?= number_format($fw['reputation_score'] ?? 100) ?> XP &bull; <?= number_format((int)($fw['target_follower_count'] ?? 0)) ?> followers
                  </div>
                </div>
              </div>

              <?php if (!empty($fw['headline'])): ?>
                <p style="font-size: 0.8rem; color: var(--ts-text-secondary); line-height: 1.4; margin: 0; flex: 1;">
                  <?= htmlspecialchars($fw['headline']) ?>
                </p>
              <?php endif; ?>

              <div style="display: flex; justify-content: space-between; align-items: center; margin-top: auto; padding-top: 0.5rem; border-top: 1px solid var(--ts-border-subtle); font-size: 0.72rem; color: #64748b;">
                <a href="<?= url('/author/profile/' . $fw['author_slug']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.7rem; padding: 0.25rem 0.6rem; width: 100%; text-align: center;">
                  View Profile ↗
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="ts-card" style="text-align: center; padding: 3rem; color: #64748b;">
          <i data-lucide="user-plus" style="width: 40px; height: 40px; margin-bottom: 0.75rem; color: #c084fc;"></i>
          <h4 style="color: var(--ts-text-primary); margin: 0 0 0.35rem 0;">Not following any creators yet</h4>
          <p style="font-size: 0.85rem; color: #64748b; margin: 0;">Explore the TechScout directory to follow authors in your domain.</p>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>

<!-- [TS-CR-05] Dual-Theme Author Crypto Tip Modal -->
<div id="authorTipModal" style="display: none; position: fixed; inset: 0; background: rgba(5,7,11,0.85); backdrop-filter: blur(8px); z-index: 999999; align-items: center; justify-content: center; padding: 1.5rem;" onclick="if(event.target===this) closeAuthorTipModal();">
  <div class="ts-card author-tip-modal-card" style="width: 100%; max-width: 540px; padding: 2rem; background: var(--ts-card-bg); border: 1px solid var(--ts-border-main); box-shadow: 0 25px 60px rgba(0,0,0,0.6); border-radius: 16px; position: relative; max-height: 90vh; overflow-y: auto;">
    <button type="button" onclick="closeAuthorTipModal()" style="position: absolute; top: 1.25rem; right: 1.25rem; background: none; border: none; font-size: 1.5rem; color: var(--ts-text-tertiary); cursor: pointer; line-height: 1;">&times;</button>

    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
      <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(245,158,11,0.15); color: #f59e0b; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
        <i data-lucide="zap" style="width: 24px; height: 24px;"></i>
      </div>
      <div>
        <h3 style="font-size: 1.35rem; color: var(--ts-text-primary); margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
          Support <?= htmlspecialchars($author['display_name'] ?? $author['username']) ?> with Crypto
        </h3>
        <p style="font-size: 0.8rem; color: var(--ts-text-tertiary); margin: 0.15rem 0 0 0;">Direct creator micro-sponsorship via verified platform treasury</p>
      </div>
    </div>

    <!-- Revenue Share & Fee Guarantee Notice -->
    <div style="background: rgba(245,158,11,0.06); border: 1px solid rgba(245,158,11,0.25); border-radius: 10px; padding: 0.85rem; margin-bottom: 1.25rem; font-size: 0.82rem; color: var(--ts-text-secondary); line-height: 1.5;">
      ⚡ <strong>Creator Revenue Guarantee:</strong> <strong>95%</strong> of your contribution is credited directly to <strong><?= htmlspecialchars($author['display_name'] ?? $author['username']) ?></strong>'s verified creator balance. The platform retains 5% for protocol verification &amp; maintenance.
    </div>

    <!-- Network Selector Tabs -->
    <div style="margin-bottom: 1.25rem;">
      <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--ts-text-secondary); text-transform: uppercase; margin-bottom: 0.4rem; letter-spacing: 0.05em;">Select Cryptocurrency Network</label>
      <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem;" id="tipCryptoTabs">
        <button type="button" onclick="selectTipCrypto('USDT')" id="tab-crypto-USDT" class="crypto-net-btn active" style="padding: 0.6rem 0.3rem; border-radius: 8px; border: 1px solid var(--ts-primary); background: rgba(99,102,241,0.15); color: var(--ts-text-primary); cursor: pointer; font-weight: 700; font-size: 0.82rem; text-align: center;">
          <div style="font-size: 1rem; margin-bottom: 0.1rem;">💵</div>
          USDT
        </button>
        <button type="button" onclick="selectTipCrypto('SOL')" id="tab-crypto-SOL" class="crypto-net-btn" style="padding: 0.6rem 0.3rem; border-radius: 8px; border: 1px solid var(--ts-border-subtle); background: var(--ts-bg-surface); color: var(--ts-text-secondary); cursor: pointer; font-weight: 700; font-size: 0.82rem; text-align: center;">
          <div style="font-size: 1rem; margin-bottom: 0.1rem;">⚡</div>
          SOL
        </button>
        <button type="button" onclick="selectTipCrypto('ETH')" id="tab-crypto-ETH" class="crypto-net-btn" style="padding: 0.6rem 0.3rem; border-radius: 8px; border: 1px solid var(--ts-border-subtle); background: var(--ts-bg-surface); color: var(--ts-text-secondary); cursor: pointer; font-weight: 700; font-size: 0.82rem; text-align: center;">
          <div style="font-size: 1rem; margin-bottom: 0.1rem;">💎</div>
          ETH
        </button>
        <button type="button" onclick="selectTipCrypto('BTC')" id="tab-crypto-BTC" class="crypto-net-btn" style="padding: 0.6rem 0.3rem; border-radius: 8px; border: 1px solid var(--ts-border-subtle); background: var(--ts-bg-surface); color: var(--ts-text-secondary); cursor: pointer; font-weight: 700; font-size: 0.82rem; text-align: center;">
          <div style="font-size: 1rem; margin-bottom: 0.1rem;">🪙</div>
          BTC
        </button>
      </div>
    </div>

    <!-- Deposit Address Box & Dynamic QR Code -->
    <div style="background: var(--ts-bg); border: 1px solid var(--ts-border-main); border-radius: 12px; padding: 1.25rem; margin-bottom: 1.25rem; text-align: center;">
      <div style="display: flex; justify-content: center; margin-bottom: 1rem;">
        <div style="background: #ffffff; padding: 8px; border-radius: 10px; display: inline-block; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
          <img id="tipQrCodeImg" src="" alt="Deposit QR Code" style="width: 140px; height: 140px; display: block;">
        </div>
      </div>
      <div style="font-size: 0.75rem; color: var(--ts-text-tertiary); margin-bottom: 0.35rem;" id="tipNetworkLabel">Platform Treasury Address (USDT - ERC20/TRC20):</div>
      <div style="display: flex; gap: 0.5rem; align-items: center;">
        <input type="text" id="tipWalletAddressInput" readonly style="flex: 1; font-family: monospace; font-size: 0.78rem; background: var(--ts-bg-surface); border: 1px solid var(--ts-border-subtle); border-radius: 6px; padding: 0.5rem 0.75rem; color: var(--ts-text-primary);" value="">
        <button type="button" onclick="copyTipAddress()" class="ts-btn ts-btn-outline ts-btn-sm" style="white-space: nowrap; font-size: 0.78rem; padding: 0.5rem 0.75rem;">
          📋 Copy
        </button>
      </div>
    </div>

    <!-- Tip Verification Form -->
    <form id="authorTipSubmissionForm" onsubmit="submitAuthorProfileTip(event)">
      <input type="hidden" name="author_username" value="<?= htmlspecialchars($author['username']) ?>">
      <input type="hidden" name="author_id" value="<?= (int)$author['id'] ?>">
      <input type="hidden" name="currency" id="tipCurrencyField" value="USDT">
      <input type="hidden" name="article_url" value="<?= htmlspecialchars(url('/author/profile/' . $author['slug'])) ?>">

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
        <div>
          <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--ts-text-primary); margin-bottom: 0.35rem;">Contribution ($ USD)</label>
          <input type="number" name="amount" id="tipAmountInput" min="1" step="1" value="15" style="width: 100%; background: var(--ts-bg); border: 1px solid var(--ts-border-main); padding: 0.6rem 0.75rem; border-radius: 6px; color: var(--ts-text-primary); font-size: 0.9rem;" required>
        </div>
        <div>
          <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--ts-text-primary); margin-bottom: 0.35rem;">Your Name / Handle</label>
          <input type="text" name="sender_name" placeholder="Anonymous Supporter" style="width: 100%; background: var(--ts-bg); border: 1px solid var(--ts-border-main); padding: 0.6rem 0.75rem; border-radius: 6px; color: var(--ts-text-primary); font-size: 0.9rem;">
        </div>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--ts-text-primary); margin-bottom: 0.35rem;">
          Transaction Hash / TXID <span style="color: #ef4444;">*</span>
        </label>
        <input type="text" name="txid" id="tipTxidInput" placeholder="Paste 0x... or Solana/Bitcoin transaction signature" style="width: 100%; background: var(--ts-bg); border: 1px solid var(--ts-border-main); padding: 0.6rem 0.75rem; border-radius: 6px; color: var(--ts-text-primary); font-size: 0.85rem; font-family: monospace;" required>
        <span style="font-size: 0.72rem; color: var(--ts-text-tertiary); margin-top: 0.25rem; display: block;">Used by platform nodes to confirm on-chain arrival and allocate 95% creator credit.</span>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem; align-items: center;">
        <button type="button" onclick="closeAuthorTipModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" id="tipSubmitBtn" class="ts-btn ts-btn-sm" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; font-weight: 700; border: none; padding: 0.55rem 1.5rem; box-shadow: 0 4px 15px rgba(245,158,11,0.35);">
          Verify &amp; Credit Author ⚡
        </button>
      </div>
    </form>
  </div>
</div>

<script>
const tipWallets = <?= json_encode($tipWallets ?? [], JSON_UNESCAPED_SLASHES) ?>;
let activeTipCrypto = 'USDT';

function switchAuthorTab(tab) {
  document.querySelectorAll('.author-tab-content').forEach(c => c.style.display = 'none');
  document.querySelectorAll('[id^="tab-btn-"]').forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });

  const content = document.getElementById('author-tab-' + tab);
  const btn = document.getElementById('tab-btn-' + tab);
  if (content) content.style.display = 'block';
  if (btn) {
    btn.classList.remove('ts-btn-outline');
    btn.classList.add('ts-btn-primary');
  }
}

function selectTipCrypto(symbol) {
  activeTipCrypto = symbol;
  const currField = document.getElementById('tipCurrencyField');
  if (currField) currField.value = symbol;

  document.querySelectorAll('.crypto-net-btn').forEach(btn => {
    btn.style.borderColor = 'var(--ts-border-subtle)';
    btn.style.background = 'var(--ts-bg-surface)';
    btn.style.color = 'var(--ts-text-secondary)';
  });
  const activeBtn = document.getElementById('tab-crypto-' + symbol);
  if (activeBtn) {
    activeBtn.style.borderColor = 'var(--ts-primary)';
    activeBtn.style.background = 'rgba(99,102,241,0.15)';
    activeBtn.style.color = 'var(--ts-text-primary)';
  }
  const addr = tipWallets[symbol] || '';
  const walletInput = document.getElementById('tipWalletAddressInput');
  if (walletInput) walletInput.value = addr;
  const label = document.getElementById('tipNetworkLabel');
  if (label) label.innerText = `Platform Treasury Address (${symbol}):`;
  const qr = document.getElementById('tipQrCodeImg');
  if (qr && addr) {
    qr.src = `https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${encodeURIComponent(addr)}`;
  }
}

function openAuthorTipModal() {
  selectTipCrypto(activeTipCrypto);
  const modal = document.getElementById('authorTipModal');
  if (modal) {
    modal.style.display = 'flex';
    if (typeof lucide !== 'undefined') lucide.createIcons();
  }
}

function closeAuthorTipModal() {
  const modal = document.getElementById('authorTipModal');
  if (modal) modal.style.display = 'none';
}

function copyTipAddress() {
  const input = document.getElementById('tipWalletAddressInput');
  if (input) {
    navigator.clipboard.writeText(input.value).then(() => {
      if (typeof showToast === 'function') showToast('Wallet address copied to clipboard! 📋', 'success');
      else alert('Wallet address copied to clipboard!');
    });
  }
}

async function submitAuthorProfileTip(e) {
  e.preventDefault();
  const btn = document.getElementById('tipSubmitBtn');
  btn.disabled = true;
  btn.innerText = 'Submitting for verification...';

  const baseUrl = (typeof getTsApiBase === 'function') ? getTsApiBase() : '';
  const form = document.getElementById('authorTipSubmissionForm');
  const formData = new FormData(form);

  try {
    const res = await fetch(`${baseUrl}/api/v1/tips/submit`, {
      method: 'POST',
      body: formData
    });
    const data = await res.json();
    if (data.success) {
      if (typeof showToast === 'function') {
        showToast(data.message || 'Tip submitted! Creator credited upon confirmation.', 'success');
      } else {
        alert(data.message || 'Tip submitted successfully!');
      }
      closeAuthorTipModal();
      form.reset();
    } else {
      if (typeof showToast === 'function') {
        showToast(data.error || 'Failed to submit tip.', 'warning');
      } else {
        alert(data.error || 'Failed to submit tip.');
      }
    }
  } catch (err) {
    console.error(err);
    if (typeof showToast === 'function') showToast('Unable to submit tip. Please try again.', 'error');
  } finally {
    btn.disabled = false;
    btn.innerText = 'Verify & Credit Author ⚡';
  }
}

// Auto-trigger modal if URL contains #tip or ?tip=1
document.addEventListener('DOMContentLoaded', () => {
  const hash = window.location.hash;
  const urlParams = new URLSearchParams(window.location.search);
  if (hash === '#tip' || urlParams.get('tip') === '1' || urlParams.get('action') === 'tip') {
    setTimeout(openAuthorTipModal, 250);
  }
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
