<?php 
$title = 'Engagement Studio & Telemetry Generator | Admin Command';
require __DIR__ . '/../layouts/admin_header.php'; 
?>

  <!-- Top Bar -->
  <div class="admin-topbar" style="margin-bottom: 2rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem; flex-wrap: wrap;">
        <span class="ts-badge ts-badge-pink" style="font-weight: 700; letter-spacing: 0.04em;">TELEMETRY &amp; ENGAGEMENT SIMULATOR</span>
        <span class="ts-badge ts-badge-cyan">v2.8 UNIFIED HUD</span>
        <span style="font-size: 0.82rem; color: #64748b;">Synthetic Community Metrics, Dwell Telemetry &amp; Traffic Generator</span>
      </div>
      <h1 style="font-size: 1.85rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800; line-height: 1.2;">
        Engagement Studio &amp; Telemetry Generator
      </h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
      <a href="<?= url('/admin/engagement-studio') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #94a3b8;">
        <i data-lucide="refresh-cw" style="width: 14px;"></i> Reset / Browse Entities
      </a>
      <a href="<?= url('/admin/analytics') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3);">
        <i data-lucide="bar-chart-3" style="width: 14px;"></i> Live Analytics HUD
      </a>
    </div>
  </div>

  <?php if (!empty($flashSuccess = \App\Core\Session::flash('success'))): ?>
    <div style="flex-wrap: wrap; align-items: center; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.4); border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1.75rem; color: #34d399; display: flex; align-items: center; gap: 0.75rem;">
      <i data-lucide="check-circle" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
      <div style="font-size: 0.92rem; font-weight: 600;"><?= htmlspecialchars($flashSuccess) ?></div>
    </div>
  <?php endif; ?>

  <?php if (!empty($flashError = \App\Core\Session::flash('error'))): ?>
    <div style="flex-wrap: wrap; align-items: center; background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.4); border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1.75rem; color: #f87171; display: flex; align-items: center; gap: 0.75rem;">
      <i data-lucide="alert-circle" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
      <div style="font-size: 0.92rem; font-weight: 600;"><?= htmlspecialchars($flashError) ?></div>
    </div>
  <?php endif; ?>

  <!-- Pure Numerical Simulation Guarantee Card -->
  <div style="background: rgba(56,189,248,0.06); border: 1px solid rgba(56,189,248,0.25); border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.75rem;">
      <span style="font-size: 1.3rem;">🛡️</span>
      <div>
        <strong style="color: #38bdf8; font-size: 0.92rem; display: block;">Pure Numerical Telemetry Engine Active</strong>
        <span style="color: #94a3b8; font-size: 0.8rem;">All engagement boosts and telemetry simulations directly update public numerical counters and aggregates. Registered member accounts, user reading histories, and genuine follower rosters remain completely untouched.</span>
      </div>
    </div>
    <span class="ts-badge ts-badge-cyan" style="font-size: 0.72rem;">ZERO ACCOUNT POLLUTION GUARANTEE</span>
  </div>

  <!-- ========================================================================= -->
  <!-- 1. MULTI-CHANNEL UNIFIED ENTITY RESOLVER & SELECTOR -->
  <!-- ========================================================================= -->
  <div style="background: linear-gradient(135deg, rgba(15,23,42,0.9), rgba(8,13,24,0.95)); border: 1px solid rgba(99,102,241,0.3); border-radius: 16px; padding: 1.75rem; margin-bottom: 2rem; box-shadow: 0 12px 36px rgba(0,0,0,0.5);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
      <div>
        <div style="flex-wrap: wrap; align-items: center; font-weight: 800; color: #f8fafc; font-size: 1.15rem; display: flex; align-items: center; gap: 0.6rem; font-family: 'Outfit', sans-serif;">
          <span style="width: 32px; height: 32px; border-radius: 8px; background: rgba(56,189,248,0.15); display: flex; align-items: center; justify-content: center; color: #38bdf8;">
            <i data-lucide="target" style="width: 18px; height: 18px;"></i>
          </span>
          Target Entity Selector &amp; Direct Resolver
        </div>
        <p style="font-size: 0.82rem; color: #94a3b8; margin: 0.25rem 0 0 0;">
          Select or paste any Article, Tool, Prompt, Custom GPT, Campaign, or Author profile to inject high-signal engagement.
        </p>
      </div>

      <!-- Type Filter Badges -->
      <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
        <button type="button" onclick="setResolverType('all')" id="rt_all" class="ts-btn ts-btn-sm <?= ($selectedType === 'all') ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.65rem;">All Types</button>
        <button type="button" onclick="setResolverType('article')" id="rt_article" class="ts-btn ts-btn-sm <?= ($selectedType === 'article') ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.65rem;">Articles</button>
        <button type="button" onclick="setResolverType('tool')" id="rt_tool" class="ts-btn ts-btn-sm <?= ($selectedType === 'tool') ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.65rem;">AI Tools</button>
        <button type="button" onclick="setResolverType('prompt')" id="rt_prompt" class="ts-btn ts-btn-sm <?= ($selectedType === 'prompt') ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.65rem;">Prompts</button>
        <button type="button" onclick="setResolverType('gpt')" id="rt_gpt" class="ts-btn ts-btn-sm <?= ($selectedType === 'gpt') ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.65rem;">GPTs</button>
        <button type="button" onclick="setResolverType('author')" id="rt_author" class="ts-btn ts-btn-sm <?= ($selectedType === 'author') ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.65rem;">Authors</button>
      </div>
    </div>

    <!-- Quick Lookup Form (by Link, ID, or Slug) -->
    <form action="<?= url('/admin/engagement-studio') ?>" method="GET" style="display: grid; grid-template-columns: minmax(130px, 180px) 1fr auto; gap: 0.75rem; margin-bottom: 1.5rem;" id="resolverForm">
      <input type="hidden" name="type" id="res_type" value="<?= htmlspecialchars($selectedType) ?>">
      
      <div>
        <select name="lookup_by" id="res_lookup_by" class="admin-modal-select" style="padding: 0.65rem 0.75rem; font-size: 0.85rem; height: 100%;">
          <option value="auto" <?= $lookupMode === 'auto' ? 'selected' : '' ?>>🔎 Auto-Detect</option>
          <option value="id" <?= $lookupMode === 'id' ? 'selected' : '' ?>># By Entity ID</option>
          <option value="slug" <?= $lookupMode === 'slug' ? 'selected' : '' ?>>🔗 By Slug</option>
          <option value="link" <?= $lookupMode === 'link' ? 'selected' : '' ?>>🌐 By Direct URL</option>
          <option value="author" <?= $lookupMode === 'author' ? 'selected' : '' ?>>👤 By @Username</option>
        </select>
      </div>

      <div>
        <input type="text" name="lookup_query" id="res_query" value="<?= htmlspecialchars($lookupQuery) ?>" placeholder="Paste live link (e.g. /article/frontier-ai), enter slug, numeric ID (#12), or @username..." class="admin-modal-input" style="padding: 0.65rem 1rem; font-size: 0.88rem; width: 100%;">
      </div>

      <button type="submit" class="ts-btn ts-btn-primary" style="padding: 0.65rem 1.35rem; font-weight: 700; background: linear-gradient(135deg, #6366f1, #38bdf8); display: flex; align-items: center; gap: 0.45rem;">
        <i data-lucide="crosshair" style="width: 15px;"></i> Resolve &amp; Load 🎯
      </button>
    </form>

    <!-- Fast Dual Dropdown Switchboard -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; border-top: 1px solid rgba(148,163,184,0.12); padding-top: 1.25rem;">
      <div>
        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #94a3b8; margin-bottom: 0.35rem; text-transform: uppercase;">
          Or Select From Published Catalog (<?= count($contents) ?> Items):
        </label>
        <form action="<?= url('/admin/engagement-studio') ?>" method="GET">
          <select name="content_id" onchange="this.form.submit()" class="admin-modal-select" style="width: 100%; padding: 0.55rem 0.75rem; font-size: 0.85rem;">
            <option value="0">-- Select Catalog Publication --</option>
            <?php foreach ($contents as $c): ?>
              <option value="<?= $c['id'] ?>" <?= $selectedContentId === (int)$c['id'] ? 'selected' : '' ?>>
                [<?= strtoupper($c['type'] ?? 'ARTICLE') ?>] #<?= $c['id'] ?> &bull; <?= htmlspecialchars(mb_strimwidth($c['title'], 0, 48, '...')) ?> (👁️ <?= number_format($c['view_count']) ?> | 💬 <?= (int)$c['comment_count'] ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </form>
      </div>

      <div>
        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #94a3b8; margin-bottom: 0.35rem; text-transform: uppercase;">
          Or Select Creator Profile (<?= count($authors) ?> Authors):
        </label>
        <form action="<?= url('/admin/engagement-studio') ?>" method="GET">
          <select name="author_id" onchange="this.form.submit()" class="admin-modal-select" style="width: 100%; padding: 0.55rem 0.75rem; font-size: 0.85rem;">
            <option value="0">-- Select Verified Author --</option>
            <?php foreach ($authors as $a): ?>
              <option value="<?= $a['id'] ?>" <?= $selectedAuthorId === (int)$a['id'] ? 'selected' : '' ?>>
                👤 #<?= $a['id'] ?> &bull; <?= htmlspecialchars($a['display_name']) ?> (⭐ <?= number_format($a['reputation_score']) ?> XP | 👥 <?= number_format($a['follower_count']) ?> followers)
              </option>
            <?php endforeach; ?>
          </select>
        </form>
      </div>
    </div>
  </div>

  <?php if ($selectedContent): ?>
    <!-- ========================================================================= -->
    <!-- 2. ACTIVE TARGET CONTENT TELEMETRY HUD -->
    <!-- ========================================================================= -->
    <div style="background: linear-gradient(135deg, rgba(15,23,42,0.95), rgba(10,18,30,0.9)); border: 1px solid rgba(56,189,248,0.35); border-radius: 16px; padding: 1.75rem; margin-bottom: 2rem;">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
          <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.4rem; flex-wrap: wrap;">
            <span class="ts-badge ts-badge-cyan" style="font-weight: 800; font-size: 0.72rem;"><?= strtoupper($selectedContent['content_type'] ?? 'ARTICLE') ?></span>
            <span class="ts-badge ts-badge-green" style="font-weight: 700; font-size: 0.72rem;"><?= strtoupper($selectedContent['status']) ?></span>
            <span class="ts-badge" style="background: rgba(148,163,184,0.15); color: #cbd5e1; font-family: monospace;">ID #<?= $selectedContent['id'] ?></span>
          </div>
          <h2 style="font-size: 1.45rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
            <?= htmlspecialchars($selectedContent['title']) ?>
          </h2>
          <div style="font-size: 0.85rem; color: #94a3b8; margin-top: 0.35rem; display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <span>Author: <strong style="color: #38bdf8;"><?= htmlspecialchars($selectedContent['author_name'] ?? 'Editorial Staff') ?></strong></span>
            <span>Slug: <code style="color: #a5b4fc; background: rgba(99,102,241,0.1); padding: 0.15rem 0.4rem; border-radius: 4px;"><?= htmlspecialchars($selectedContent['slug']) ?></code></span>
          </div>
        </div>

        <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.5rem;">
          <a href="<?= url('/' . ($selectedContent['content_type'] ?? 'article') . '/' . $selectedContent['slug']) ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.4);">
            <i data-lucide="external-link" style="width: 14px;"></i> View Live Page ↗
          </a>
          <a href="<?= url('/admin/content/analytics/' . $selectedContent['id']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #34d399; border-color: rgba(16,185,129,0.3);">
            <i data-lucide="activity" style="width: 14px;"></i> Content Telemetry
          </a>
        </div>
      </div>

      <!-- Telemetry Counters -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 1rem;">
        <div style="background: #05070b; border: 1px solid rgba(56,189,248,0.25); border-radius: 12px; padding: 1.1rem; text-align: center;">
          <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; font-weight: 700;">Views / Reads</div>
          <div style="font-size: 1.75rem; font-weight: 800; color: #38bdf8; font-family: var(--ts-font-mono); margin: 0.2rem 0;">
            <?= number_format((int)$selectedContent['view_count']) ?>
          </div>
          <span style="font-size: 0.72rem; color: #64748b;">Telemetry Reads</span>
        </div>

        <div style="background: #05070b; border: 1px solid rgba(236,72,153,0.25); border-radius: 12px; padding: 1.1rem; text-align: center;">
          <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; font-weight: 700;">Reactions &amp; Claps</div>
          <div style="font-size: 1.75rem; font-weight: 800; color: #ec4899; font-family: var(--ts-font-mono); margin: 0.2rem 0;">
            <?= number_format((int)$selectedContent['reaction_count']) ?>
          </div>
          <span style="font-size: 0.72rem; color: #64748b;">Community Claps</span>
        </div>

        <div style="background: #05070b; border: 1px solid rgba(168,85,247,0.25); border-radius: 12px; padding: 1.1rem; text-align: center;">
          <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; font-weight: 700;">Bookmarks</div>
          <div style="font-size: 1.75rem; font-weight: 800; color: #c084fc; font-family: var(--ts-font-mono); margin: 0.2rem 0;">
            <?= number_format((int)$selectedContent['bookmark_count']) ?>
          </div>
          <span style="font-size: 0.72rem; color: #64748b;">Engineer Saves</span>
        </div>

        <div style="background: #05070b; border: 1px solid rgba(16,185,129,0.25); border-radius: 12px; padding: 1.1rem; text-align: center;">
          <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; font-weight: 700;">Discussions &amp; Comments</div>
          <div style="font-size: 1.75rem; font-weight: 800; color: #10b981; font-family: var(--ts-font-mono); margin: 0.2rem 0;">
            <?= number_format((int)$selectedContent['comment_count']) ?>
          </div>
          <span style="font-size: 0.72rem; color: #64748b;">Community Replies</span>
        </div>
      </div>
    </div>

    <!-- Simulator Control Stations Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
      
      <!-- Panel 1: Telemetry View Booster -->
      <div class="ts-card" style="padding: 1.5rem; border: 1px solid rgba(56,189,248,0.3); border-radius: 14px;">
        <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.65rem; margin-bottom: 1.25rem;">
          <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(56,189,248,0.15); display: flex; align-items: center; justify-content: center; color: #38bdf8;">
            <i data-lucide="zap"></i>
          </div>
          <div>
            <h3 style="font-size: 1.1rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 700;">
              View &amp; Read Telemetry Multiplier
            </h3>
            <p style="font-size: 0.78rem; color: #94a3b8; margin: 0;">Generates distributed historical read logs for analytics charts</p>
          </div>
        </div>

        <form action="<?= url('/admin/engagement-studio/simulate') ?>" method="POST">
          <input type="hidden" name="sim_action" value="boost_views">
          <input type="hidden" name="content_id" value="<?= $selectedContent['id'] ?>">

          <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; margin-bottom: 1rem;">
            <button type="button" onclick="document.getElementById('view_input').value=50" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.78rem;">+50 Reads</button>
            <button type="button" onclick="document.getElementById('view_input').value=250" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.78rem;">+250 Reads</button>
            <button type="button" onclick="document.getElementById('view_input').value=1000" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.78rem;">+1.0k Reads</button>
            <button type="button" onclick="document.getElementById('view_input').value=5000" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.78rem;">+5.0k Reads</button>
            <button type="button" onclick="document.getElementById('view_input').value=25000" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.78rem;">+25k Reads</button>
            <button type="button" onclick="document.getElementById('view_input').value=100000" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.78rem;">+100k Reads</button>
          </div>

          <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.5rem; align-items: center;">
            <input type="number" name="view_increment" id="view_input" value="250" min="1" max="1000000" style="flex: 1; background: #05070b; border: 1px solid rgba(148,163,184,0.25); color: #f8fafc; padding: 0.6rem 0.75rem; border-radius: 8px; font-size: 0.9rem;">
            <button type="submit" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #0284c7, #38bdf8); font-weight: 700; white-space: nowrap;">
              + Boost Views ⚡
            </button>
          </div>
        </form>

        <div style="margin-top: 1rem; padding-top: 0.85rem; border-top: 1px solid rgba(148,163,184,0.1);">
          <form action="<?= url('/admin/engagement-studio/simulate') ?>" method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
            <input type="hidden" name="sim_action" value="set_views">
            <input type="hidden" name="content_id" value="<?= $selectedContent['id'] ?>">
            <div style="flex: 1;">
              <span style="font-size: 0.72rem; color: #94a3b8; display: block; margin-bottom: 0.2rem;">Or Set Exact View Count:</span>
              <input type="number" name="exact_views" value="<?= (int)$selectedContent['view_count'] ?>" min="0" max="10000000" class="admin-modal-input" style="padding: 0.45rem 0.65rem; font-size: 0.85rem; width: 100%;">
            </div>
            <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="margin-top: 1.1rem; color: #38bdf8; border-color: rgba(56,189,248,0.4); font-weight: 700; white-space: nowrap;">
              Set Number 🎯
            </button>
          </form>
        </div>
      </div>

      <!-- Panel 2: Anonymous Dwell & Traffic Origins Simulator -->
      <div class="ts-card" style="padding: 1.5rem; border: 1px solid rgba(16,185,129,0.35); border-radius: 14px;">
        <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.65rem; margin-bottom: 1.25rem;">
          <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(16,185,129,0.15); display: flex; align-items: center; justify-content: center; color: #10b981;">
            <i data-lucide="activity"></i>
          </div>
          <div>
            <h3 style="font-size: 1.1rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 700;">
              Anonymous Traffic &amp; Dwell Simulator
            </h3>
            <p style="font-size: 0.78rem; color: #94a3b8; margin: 0;">Simulate verified dwell metrics &amp; referrers (never pollutes member accounts)</p>
          </div>
        </div>

        <form action="<?= url('/admin/engagement-studio/simulate') ?>" method="POST">
          <input type="hidden" name="sim_action" value="simulate_reads">
          <input type="hidden" name="content_id" value="<?= $selectedContent['id'] ?>">

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
            <div>
              <label style="display: block; font-size: 0.75rem; color: #94a3b8; margin-bottom: 0.25rem;">Sessions to Simulate:</label>
              <input type="number" name="read_count" value="15" min="1" max="100" class="admin-modal-input" style="padding: 0.5rem 0.75rem; font-size: 0.85rem; width: 100%;">
            </div>
            <div>
              <label style="display: block; font-size: 0.75rem; color: #94a3b8; margin-bottom: 0.25rem;">Traffic Source:</label>
              <select name="referrer_origin" class="admin-modal-select" style="padding: 0.5rem 0.75rem; font-size: 0.85rem; width: 100%;">
                <option value="internal">TechScout (Internal Pages)</option>
                <option value="external">External (Google, X, HackerNews)</option>
                <option value="campaign">User Referral Campaign</option>
                <option value="random">Mixed Organic Traffic</option>
              </select>
            </div>
          </div>

          <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.5rem; align-items: center;">
            <input type="text" name="custom_referrer" placeholder="Optional custom referrer (e.g. /search, x.com)..." class="admin-modal-input" style="flex: 1; padding: 0.55rem 0.75rem; font-size: 0.82rem;">
            <button type="submit" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #10b981, #059669); font-weight: 700; white-space: nowrap;">
              Simulate Reads ⚡
            </button>
          </div>
        </form>
      </div>

      <!-- Panel 3: Reactions & Bookmarks Studio -->
      <div class="ts-card" style="padding: 1.5rem; border: 1px solid rgba(236,72,153,0.3); border-radius: 14px;">
        <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.65rem; margin-bottom: 1.25rem;">
          <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(236,72,153,0.15); display: flex; align-items: center; justify-content: center; color: #ec4899;">
            <i data-lucide="heart"></i>
          </div>
          <div>
            <h3 style="font-size: 1.1rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 700;">
              Reactions, Claps &amp; Bookmarks
            </h3>
            <p style="font-size: 0.78rem; color: #94a3b8; margin: 0;">Boost social proof, community claps and bookmarks</p>
          </div>
        </div>

        <div style="flex-wrap: wrap; align-items: center; display: flex; flex-direction: column; gap: 1rem;">
          <!-- Boost Reactions Form -->
          <form action="<?= url('/admin/engagement-studio/simulate') ?>" method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
            <input type="hidden" name="sim_action" value="boost_reactions">
            <input type="hidden" name="content_id" value="<?= $selectedContent['id'] ?>">
            <div style="flex: 1;">
              <span style="font-size: 0.75rem; color: #94a3b8; display: block; margin-bottom: 0.2rem;">Add Claps / Reactions:</span>
              <input type="number" name="reaction_increment" value="25" min="1" max="10000" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.25); color: #f8fafc; padding: 0.5rem 0.65rem; border-radius: 6px; font-size: 0.85rem;">
            </div>
            <button type="submit" class="ts-btn ts-btn-primary" style="margin-top: 1.1rem; background: linear-gradient(135deg, #db2777, #ec4899); font-size: 0.82rem; font-weight: 700;">
              + Claps ❤️
            </button>
          </form>

          <!-- Boost Bookmarks Form -->
          <form action="<?= url('/admin/engagement-studio/simulate') ?>" method="POST" style="display: flex; gap: 0.5rem; align-items: center; padding-top: 0.75rem; border-top: 1px solid rgba(148,163,184,0.1);">
            <input type="hidden" name="sim_action" value="boost_bookmarks">
            <input type="hidden" name="content_id" value="<?= $selectedContent['id'] ?>">
            <div style="flex: 1;">
              <span style="font-size: 0.75rem; color: #94a3b8; display: block; margin-bottom: 0.2rem;">Add Saved Bookmarks:</span>
              <input type="number" name="bookmark_increment" value="15" min="1" max="5000" style="width: 100%; background: #05070b; border: 1px solid rgba(148,163,184,0.25); color: #f8fafc; padding: 0.5rem 0.65rem; border-radius: 6px; font-size: 0.85rem;">
            </div>
            <button type="submit" class="ts-btn ts-btn-primary" style="margin-top: 1.1rem; background: linear-gradient(135deg, #7c3aed, #a855f7); font-size: 0.82rem; font-weight: 700;">
              + Bookmarks 🔖
            </button>
          </form>

          <!-- Set Exact Reactions Form -->
          <div style="padding-top: 0.75rem; border-top: 1px solid rgba(148,163,184,0.1);">
            <form action="<?= url('/admin/engagement-studio/simulate') ?>" method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
              <input type="hidden" name="sim_action" value="set_reactions">
              <input type="hidden" name="content_id" value="<?= $selectedContent['id'] ?>">
              <div style="flex: 1;">
                <span style="font-size: 0.72rem; color: #94a3b8; display: block; margin-bottom: 0.2rem;">Or Set Exact Reactions:</span>
                <input type="number" name="exact_reactions" value="<?= (int)$selectedContent['reaction_count'] ?>" min="0" max="1000000" class="admin-modal-input" style="padding: 0.45rem 0.65rem; font-size: 0.85rem; width: 100%;">
              </div>
              <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="margin-top: 1.1rem; color: #ec4899; border-color: rgba(236,72,153,0.4); font-weight: 700; white-space: nowrap;">
                Set Exact
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Panel 4: Developer Comment & Discussion Simulator -->
    <div class="ts-card" style="padding: 1.75rem; border: 1px solid rgba(99,102,241,0.3); border-radius: 16px; margin-bottom: 2.5rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
        <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.6rem;">
          <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(99,102,241,0.15); display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 1.2rem;">
            💬
          </div>
          <div>
            <h3 style="font-size: 1.25rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
              Simulate Community Discussion &amp; Comments
            </h3>
            <p style="font-size: 0.8rem; color: #94a3b8; margin: 0;">Inject realistic, high-signal developer comments and replies</p>
          </div>
        </div>

        <span class="ts-badge ts-badge-cyan" style="font-size: 0.75rem;">
          <?= count($contentComments) ?> Existing Comments
        </span>
      </div>

      <!-- Comment Quick Preset Chips -->
      <div style="margin-bottom: 1.25rem;">
        <span style="font-size: 0.78rem; font-weight: 700; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 0.4rem;">
          Quick Realistic Engineering Presets (Click to Load):
        </span>
        <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
          <button type="button" onclick="setCommentPreset(0)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.55rem;">
            🚀 "Benchmarked this in production..."
          </button>
          <button type="button" onclick="setCommentPreset(1)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.55rem;">
            ⚙️ "How does this handle pod autoscaling?"
          </button>
          <button type="button" onclick="setCommentPreset(2)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.55rem;">
            🤖 "Tested this against Claude &amp; GPT-4o..."
          </button>
          <button type="button" onclick="setCommentPreset(3)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.55rem;">
            ⚡ "Cleanest token budget allocation..."
          </button>
          <button type="button" onclick="setCommentPreset(4)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.55rem;">
            🛡️ "Zero-trust session invalidation..."
          </button>
        </div>
      </div>

      <form action="<?= url('/admin/engagement-studio/simulate') ?>" method="POST">
        <input type="hidden" name="sim_action" value="add_comment">
        <input type="hidden" name="content_id" value="<?= $selectedContent['id'] ?>">

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
          <div>
            <label style="display: block; font-size: 0.78rem; font-weight: 600; color: #94a3b8; margin-bottom: 0.35rem;">
              Simulated Persona Author:
            </label>
            <select name="user_id" class="admin-modal-select" style="width: 100%; padding: 0.55rem 0.75rem; font-size: 0.85rem;">
              <?php foreach ($users as $u): ?>
                <option value="<?= $u['id'] ?>">
                  👤 <?= htmlspecialchars($u['display_name']) ?> (@<?= htmlspecialchars($u['username']) ?>) [<?= strtoupper($u['role']) ?>]
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label style="display: block; font-size: 0.78rem; font-weight: 600; color: #94a3b8; margin-bottom: 0.35rem;">
              Simulated Timestamp Offset:
            </label>
            <select name="time_offset" class="admin-modal-select" style="width: 100%; padding: 0.55rem 0.75rem; font-size: 0.85rem;">
              <option value="now">Just Now (Real-time)</option>
              <option value="1_hour_ago">1 Hour Ago</option>
              <option value="4_hours_ago">4 Hours Ago</option>
              <option value="1_day_ago">1 Day Ago</option>
              <option value="2_days_ago">2 Days Ago</option>
              <option value="5_days_ago">5 Days Ago</option>
            </select>
          </div>

          <div>
            <label style="display: block; font-size: 0.78rem; font-weight: 600; color: #94a3b8; margin-bottom: 0.35rem;">
              Initial Seed Claps / Upvotes:
            </label>
            <input type="number" name="reaction_count" value="7" min="0" max="500" class="admin-modal-input" style="width: 100%; padding: 0.55rem 0.75rem; font-size: 0.85rem;">
          </div>
        </div>

        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.78rem; font-weight: 600; color: #94a3b8; margin-bottom: 0.35rem;">
            Comment Technical Contribution:
          </label>
          <textarea name="comment_text" id="sim_comment_textarea" rows="3" placeholder="Write realistic developer commentary or pick a preset above..." class="admin-modal-input" style="width: 100%; padding: 0.75rem; font-size: 0.88rem; line-height: 1.5;" required></textarea>
        </div>

        <div style="display: flex; justify-content: flex-end;">
          <button type="submit" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #10b981, #38bdf8); font-weight: 700;">
            Inject Community Discussion 💬
          </button>
        </div>
      </form>
    </div>
  <?php elseif ($selectedAuthor): ?>
    <!-- ========================================================================= -->
    <!-- 3. AUTHOR PROFILE ENGAGEMENT HUB -->
    <!-- ========================================================================= -->
    <div style="background: linear-gradient(135deg, rgba(15,23,42,0.9), rgba(10,18,30,0.9)); border: 1px solid rgba(168,85,247,0.35); border-radius: 16px; padding: 1.75rem; margin-bottom: 2rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 1rem;">
          <div style="width: 54px; height: 54px; border-radius: 50%; border: 2px solid #a855f7; overflow: hidden; background: #000;">
            <img src="<?= htmlspecialchars(image_url($selectedAuthor['avatar_url'] ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&h=100&fit=crop')) ?>" style="width: 100%; height: 100%; object-fit: cover;">
          </div>
          <div>
            <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.5rem;">
              <h2 style="font-size: 1.45rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
                <?= htmlspecialchars($selectedAuthor['display_name']) ?>
              </h2>
              <span class="ts-badge ts-badge-purple">VERIFIED CREATOR</span>
            </div>
            <div style="font-size: 0.85rem; color: #94a3b8; margin-top: 0.2rem;">
              <?= htmlspecialchars($selectedAuthor['headline'] ?: 'Engineering Contributor') ?> &bull; @<?= htmlspecialchars($selectedAuthor['slug']) ?>
            </div>
          </div>
        </div>

        <a href="<?= url('/author/' . $selectedAuthor['slug']) ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #c084fc; border-color: rgba(192,132,252,0.4);">
          <i data-lucide="external-link" style="width: 14px;"></i> View Public Author Dossier ↗
        </a>
      </div>

      <!-- Author Boost Station -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.25rem;">
        <div class="ts-card" style="padding: 1.25rem; border: 1px solid rgba(168,85,247,0.25); border-radius: 12px;">
          <h4 style="font-size: 1rem; color: #f8fafc; margin: 0 0 0.5rem 0;">⭐ Creator Reputation Points (XP)</h4>
          <div style="font-size: 1.6rem; font-weight: 800; color: #c084fc; font-family: var(--ts-font-mono); margin-bottom: 0.75rem;">
            <?= number_format((int)$selectedAuthor['reputation_score']) ?> XP
          </div>
          
          <!-- Incremental Boost -->
          <form action="<?= url('/admin/engagement-studio/simulate') ?>" method="POST" style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem;">
            <input type="hidden" name="sim_action" value="boost_author">
            <input type="hidden" name="author_id" value="<?= $selectedAuthor['id'] ?>">
            <input type="number" name="xp_increment" value="250" min="10" max="10000" class="admin-modal-input" style="flex: 1; padding: 0.45rem 0.65rem; font-size: 0.85rem;">
            <button type="submit" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #7c3aed, #a855f7); font-weight: 700; font-size: 0.85rem;">
              + Boost XP ⭐
            </button>
          </form>

          <!-- Set Exact XP -->
          <div style="padding-top: 0.75rem; border-top: 1px solid rgba(148,163,184,0.1);">
            <form action="<?= url('/admin/engagement-studio/simulate') ?>" method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
              <input type="hidden" name="sim_action" value="set_author_xp">
              <input type="hidden" name="author_id" value="<?= $selectedAuthor['id'] ?>">
              <div style="flex: 1;">
                <span style="font-size: 0.72rem; color: #94a3b8; display: block; margin-bottom: 0.2rem;">Or Set Exact XP:</span>
                <input type="number" name="exact_xp" value="<?= (int)$selectedAuthor['reputation_score'] ?>" min="0" max="1000000" class="admin-modal-input" style="padding: 0.45rem 0.65rem; font-size: 0.85rem; width: 100%;">
              </div>
              <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="margin-top: 1.1rem; color: #c084fc; border-color: rgba(192,132,252,0.4); font-weight: 700; white-space: nowrap;">
                Set Exact
              </button>
            </form>
          </div>
        </div>

        <div class="ts-card" style="padding: 1.25rem; border: 1px solid rgba(56,189,248,0.25); border-radius: 12px;">
          <h4 style="font-size: 1rem; color: #f8fafc; margin: 0 0 0.5rem 0;">👥 Author Followers Injector</h4>
          <div style="font-size: 1.6rem; font-weight: 800; color: #38bdf8; font-family: var(--ts-font-mono); margin-bottom: 0.5rem;">
            <?= number_format((int)$selectedAuthor['follower_count']) ?> Followers
          </div>
          <div style="font-size: 0.75rem; color: #94a3b8; margin-bottom: 0.75rem;">
            Boost numerical counters or generate realistic persona profiles (with avatars, bios, and handles) in the follower roster
          </div>

          <!-- Quick Presets -->
          <div style="display: flex; gap: 0.35rem; flex-wrap: wrap; margin-bottom: 0.75rem;">
            <button type="button" onclick="document.getElementById('follower_input').value=25" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.45rem;">+25</button>
            <button type="button" onclick="document.getElementById('follower_input').value=100" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.45rem;">+100</button>
            <button type="button" onclick="document.getElementById('follower_input').value=500" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.45rem;">+500</button>
            <button type="button" onclick="document.getElementById('follower_input').value=1000" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.45rem;">+1.0k</button>
            <button type="button" onclick="document.getElementById('follower_input').value=5000" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.45rem;">+5.0k</button>
          </div>

          <!-- Incremental Boost Form -->
          <form action="<?= url('/admin/engagement-studio/simulate') ?>" method="POST" style="margin-bottom: 0.85rem;">
            <input type="hidden" name="sim_action" value="boost_followers">
            <input type="hidden" name="author_id" value="<?= $selectedAuthor['id'] ?>">
            <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.5rem; margin-bottom: 0.45rem;">
              <input type="number" name="follower_count" id="follower_input" value="100" min="1" max="1000000" class="admin-modal-input" style="flex: 1; padding: 0.45rem 0.65rem; font-size: 0.85rem;">
              <button type="submit" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #0284c7, #38bdf8); font-weight: 700; font-size: 0.85rem; white-space: nowrap;">
                + Boost Followers 👥
              </button>
            </div>
            <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.73rem; color: #cbd5e1; cursor: pointer;">
              <input type="checkbox" name="with_personas" value="1" checked style="accent-color: #38bdf8;">
              <span>Also inject realistic tech personas into follower list (avatars & headlines)</span>
            </label>
          </form>

          <!-- Quick Persona Profile Injector -->
          <div style="padding: 0.65rem 0.75rem; background: rgba(56, 189, 248, 0.05); border: 1px dashed rgba(56, 189, 248, 0.25); border-radius: 8px; margin-bottom: 0.85rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
              <span style="font-size: 0.73rem; font-weight: 700; color: #38bdf8;">✨ Inject Persona Followers</span>
              <span style="font-size: 0.68rem; color: #94a3b8;">Avatars • Bios • Roster</span>
            </div>
            <div style="flex-wrap: wrap; align-items: center; display: flex; gap: 0.4rem;">
              <form action="<?= url('/admin/engagement-studio/simulate') ?>" method="POST" style="flex: 1;">
                <input type="hidden" name="sim_action" value="inject_personas">
                <input type="hidden" name="author_id" value="<?= $selectedAuthor['id'] ?>">
                <input type="hidden" name="persona_count" value="5">
                <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%; font-size: 0.72rem; padding: 0.25rem 0.4rem; color: #38bdf8; border-color: rgba(56, 189, 248, 0.4);">
                  +5 Personas
                </button>
              </form>
              <form action="<?= url('/admin/engagement-studio/simulate') ?>" method="POST" style="flex: 1;">
                <input type="hidden" name="sim_action" value="inject_personas">
                <input type="hidden" name="author_id" value="<?= $selectedAuthor['id'] ?>">
                <input type="hidden" name="persona_count" value="10">
                <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%; font-size: 0.72rem; padding: 0.25rem 0.4rem; color: #38bdf8; border-color: rgba(56, 189, 248, 0.4);">
                  +10 Personas
                </button>
              </form>
              <form action="<?= url('/admin/engagement-studio/simulate') ?>" method="POST" style="flex: 1;">
                <input type="hidden" name="sim_action" value="inject_personas">
                <input type="hidden" name="author_id" value="<?= $selectedAuthor['id'] ?>">
                <input type="hidden" name="persona_count" value="20">
                <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%; font-size: 0.72rem; padding: 0.25rem 0.4rem; color: #38bdf8; border-color: rgba(56, 189, 248, 0.4);">
                  +20 All Personas
                </button>
              </form>
            </div>
          </div>

          <!-- Set Exact Followers -->
          <div style="padding-top: 0.75rem; border-top: 1px solid rgba(148,163,184,0.1);">
            <form action="<?= url('/admin/engagement-studio/simulate') ?>" method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
              <input type="hidden" name="sim_action" value="set_followers">
              <input type="hidden" name="author_id" value="<?= $selectedAuthor['id'] ?>">
              <div style="flex: 1;">
                <span style="font-size: 0.72rem; color: #94a3b8; display: block; margin-bottom: 0.2rem;">Or Set Exact Followers Counter:</span>
                <input type="number" name="exact_followers" value="<?= (int)$selectedAuthor['follower_count'] ?>" min="0" max="10000000" class="admin-modal-input" style="padding: 0.45rem 0.65rem; font-size: 0.85rem; width: 100%;">
              </div>
              <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="margin-top: 1.1rem; color: #38bdf8; border-color: rgba(56,189,248,0.4); font-weight: 700; white-space: nowrap;">
                Set Exact 🎯
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <!-- ========================================================================= -->
    <!-- 4. NO ENTITY SELECTED: RICH OVERVIEW GALLERY (NEVER BLANK SCREEN!) -->
    <!-- ========================================================================= -->
    <div style="margin-bottom: 2.5rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <div>
          <h3 style="font-size: 1.25rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
            🚀 Quick-Pick Catalog to Simulate
          </h3>
          <p style="font-size: 0.82rem; color: #94a3b8; margin: 0.2rem 0 0 0;">
            Click any published entity below to instantly open the simulation cockpit.
          </p>
        </div>
        <span class="ts-badge ts-badge-cyan"><?= count($contents) ?> Published Items Available</span>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
        <?php foreach (array_slice($contents, 0, 8) as $item): ?>
          <div class="ts-card" style="flex-wrap: wrap; align-items: center; padding: 1.15rem; background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.15); border-radius: 12px; display: flex; flex-direction: column; justify-content: space-between; gap: 0.75rem;">
            <div>
              <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.4rem;">
                <span class="ts-badge ts-badge-cyan" style="font-size: 0.68rem;"><?= strtoupper($item['type'] ?? 'ARTICLE') ?></span>
                <span style="font-size: 0.75rem; color: #64748b; font-family: monospace;">#<?= $item['id'] ?></span>
              </div>
              <h4 style="font-size: 0.95rem; color: #f8fafc; margin: 0 0 0.35rem 0; line-height: 1.35;">
                <?= htmlspecialchars(mb_strimwidth($item['title'], 0, 55, '...')) ?>
              </h4>
              <div style="font-size: 0.78rem; color: #94a3b8;">
                By <?= htmlspecialchars($item['author_name'] ?? 'Staff') ?> &bull; 👁️ <?= number_format($item['view_count']) ?> reads
              </div>
            </div>

            <a href="<?= url('/admin/engagement-studio?content_id=' . $item['id']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="width: 100%; text-align: center; justify-content: center; font-size: 0.8rem; color: #38bdf8; border-color: rgba(56,189,248,0.3);">
              ⚡ Select &amp; Simulate
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <!-- ========================================================================= -->
  <!-- 5. PLATFORM-WIDE ORGANIC TRAFFIC MULTIPLIER -->
  <!-- ========================================================================= -->
  <div style="background: rgba(15,23,42,0.65); border: 1px solid rgba(148,163,184,0.18); border-radius: 16px; padding: 1.5rem; margin-top: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
      <div>
        <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
          <i data-lucide="globe" style="width: 18px; color: #38bdf8;"></i> Platform-Wide Organic Traffic Multiplier
        </h3>
        <p style="font-size: 0.82rem; color: #94a3b8; margin: 0.25rem 0 0 0;">
          Applies a uniform telemetry boost across all published articles, custom GPTs, prompts and tools.
        </p>
      </div>

      <form action="<?= url('/admin/engagement-studio/simulate') ?>" method="POST" onsubmit="return confirm('Apply platform-wide organic boost across all published entities?');" style="display: flex; gap: 0.5rem; align-items: center;">
        <input type="hidden" name="sim_action" value="bulk_organic_boost">
        <select name="bulk_views" class="admin-modal-select" style="padding: 0.45rem 0.75rem; font-size: 0.85rem;">
          <option value="50">+50 views / entity</option>
          <option value="150" selected>+150 views / entity</option>
          <option value="500">+500 views / entity</option>
          <option value="2000">+2,000 views / entity</option>
        </select>
        <button type="submit" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #6366f1, #38bdf8); font-weight: 700; font-size: 0.85rem;">
          Launch Global Boost 🚀
        </button>
      </form>
    </div>
  </div>

<script>
const COMMENT_PRESETS = [
  "We benchmarked this exact approach in our production cluster last week. The latency gains on the vector query stage were roughly ~3.4x faster compared to standard cosine indexing. Excellent architecture breakdown!",
  "Great deep dive. One architectural consideration for teams operating at scale: how does this handle horizontal pod autoscaling under spiky load? Have you considered introducing Redis Streams as an intermediate buffer?",
  "Tested this prompt pipeline against GPT-4o and Claude 3.5 Sonnet. The JSON schema adherence was 100% across 500 test iterations. The few-shot framing is particularly elegant.",
  "Phenomenal technical write-up! The breakdown of token budget allocation and system message boundaries is by far the cleanest implementation I've seen in the ecosystem.",
  "Implemented this zero-trust protocol within our auth gateway. The session invalidation token hash lookup adds virtually zero overhead to PDO requests while giving us instant remote logout capability. High recommend!"
];

function setCommentPreset(index) {
  const textarea = document.getElementById('sim_comment_textarea');
  if (textarea && COMMENT_PRESETS[index]) {
    textarea.value = COMMENT_PRESETS[index];
    textarea.focus();
  }
}

function setResolverType(type) {
  document.getElementById('res_type').value = type;
  ['all', 'article', 'tool', 'prompt', 'gpt', 'author'].forEach(t => {
    const btn = document.getElementById('rt_' + t);
    if (btn) {
      if (t === type) {
        btn.classList.remove('ts-btn-outline');
        btn.classList.add('ts-btn-primary');
      } else {
        btn.classList.remove('ts-btn-primary');
        btn.classList.add('ts-btn-outline');
      }
    }
  });

  const queryInput = document.getElementById('res_query');
  if (queryInput) {
    if (type === 'author') {
      queryInput.placeholder = 'Enter @username, author slug, or author numeric ID...';
    } else if (type === 'article') {
      queryInput.placeholder = 'Paste /article/slug link, enter slug or numeric ID...';
    } else if (type === 'tool') {
      queryInput.placeholder = 'Paste /tool/slug link, enter slug or numeric ID...';
    } else {
      queryInput.placeholder = 'Paste live link (e.g. /article/frontier-ai), enter slug, numeric ID (#12), or @username...';
    }
  }
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
