<?php 
require __DIR__ . '/../layouts/header.php'; 

$socialLinks = json_decode($author['social_links'] ?? '{}', true) ?: [];

$getEntityUrl = function($type, $slug) {
    if ($type === 'gpt_listing') return url('/gpts/' . $slug);
    if ($type === 'ai_tool') return url('/tools/' . $slug);
    if ($type === 'prompt') return url('/prompts/' . $slug);
    return url('/article/' . $slug);
};
?>
<style>
/* ========================================================================= */
/* DUAL THEME ENFORCEMENT FOR AUTHOR DASHBOARD (DARK + LIGHT THEMES)         */
/* ========================================================================= */
.author-dashboard-wrap {
  min-height: 80vh;
}

/* Light Theme Overrides */
[data-theme="light"] .author-dashboard-wrap,
.theme-light .author-dashboard-wrap {
  --author-card-bg: #ffffff;
  --author-card-border: #e2e8f0;
  --author-subcard-bg: #f8fafc;
  --author-text-primary: #0f172a;
  --author-text-secondary: #334155;
  --author-text-muted: #64748b;
  --author-table-th-bg: #f1f5f9;
  --author-table-border: #e2e8f0;
  --author-input-bg: #ffffff;
  --author-input-border: #cbd5e1;
}

[data-theme="light"] .author-dashboard-wrap .ts-card,
.theme-light .author-dashboard-wrap .ts-card {
  background: #ffffff !important;
  border-color: #e2e8f0 !important;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05) !important;
}

/* High Contrast Headings in Light Mode */
[data-theme="light"] .author-dashboard-wrap h1,
[data-theme="light"] .author-dashboard-wrap h2,
[data-theme="light"] .author-dashboard-wrap h3,
[data-theme="light"] .author-dashboard-wrap h4,
[data-theme="light"] .author-dashboard-wrap h5,
.theme-light .author-dashboard-wrap h1,
.theme-light .author-dashboard-wrap h2,
.theme-light .author-dashboard-wrap h3,
.theme-light .author-dashboard-wrap h4,
.theme-light .author-dashboard-wrap h5 {
  color: #0f172a !important;
}

/* High Contrast Text & Paragraphs in Light Mode */
[data-theme="light"] .author-dashboard-wrap p,
.theme-light .author-dashboard-wrap p {
  color: #334155 !important;
}

/* High Contrast Metric Labels & Values in Light Mode */
[data-theme="light"] .author-dashboard-wrap .author-hud-metric-val,
.theme-light .author-dashboard-wrap .author-hud-metric-val {
  color: #0f172a !important;
}

/* High Contrast Sub-Cards & Panels in Light Mode */
[data-theme="light"] .author-dashboard-wrap div[style*="background: rgba(15,23,42"],
[data-theme="light"] .author-dashboard-wrap div[style*="background: #04070e"],
[data-theme="light"] .author-dashboard-wrap div[style*="background: linear-gradient(135deg, rgba(13,21,39"],
[data-theme="light"] .author-dashboard-wrap div[style*="background: linear-gradient(135deg, rgba(15,23,42"],
.theme-light .author-dashboard-wrap div[style*="background: rgba(15,23,42"],
.theme-light .author-dashboard-wrap div[style*="background: #04070e"],
.theme-light .author-dashboard-wrap div[style*="background: linear-gradient(135deg, rgba(13,21,39"],
.theme-light .author-dashboard-wrap div[style*="background: linear-gradient(135deg, rgba(15,23,42"] {
  background: #ffffff !important;
  border-color: #e2e8f0 !important;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04) !important;
}

/* Table Light Mode Overrides */
[data-theme="light"] .author-dashboard-wrap .ts-table th,
.theme-light .author-dashboard-wrap .ts-table th {
  background: #f1f5f9 !important;
  color: #475569 !important;
  border-color: #e2e8f0 !important;
}

[data-theme="light"] .author-dashboard-wrap .ts-table td,
.theme-light .author-dashboard-wrap .ts-table td {
  border-color: #e2e8f0 !important;
  color: #1e293b !important;
}

[data-theme="light"] .author-dashboard-wrap .ts-table strong,
.theme-light .author-dashboard-wrap .ts-table strong {
  color: #0f172a !important;
}

/* Light Mode Input Elements */
[data-theme="light"] .author-dashboard-wrap input[type="text"],
[data-theme="light"] .author-dashboard-wrap input[type="password"],
[data-theme="light"] .author-dashboard-wrap input[type="number"],
[data-theme="light"] .author-dashboard-wrap input[type="url"],
[data-theme="light"] .author-dashboard-wrap select,
[data-theme="light"] .author-dashboard-wrap textarea,
.theme-light .author-dashboard-wrap input[type="text"],
.theme-light .author-dashboard-wrap input[type="password"],
.theme-light .author-dashboard-wrap input[type="number"],
.theme-light .author-dashboard-wrap input[type="url"],
.theme-light .author-dashboard-wrap select,
.theme-light .author-dashboard-wrap textarea {
  background: #ffffff !important;
  border-color: #cbd5e1 !important;
  color: #0f172a !important;
}

/* Light Mode Navigation Tab Strip */
[data-theme="light"] .author-tabs-strip,
.theme-light .author-tabs-strip {
  border-color: #e2e8f0 !important;
}

[data-theme="light"] .author-tabs-strip .ts-btn-outline,
.theme-light .author-tabs-strip .ts-btn-outline {
  background: #ffffff !important;
  border-color: #cbd5e1 !important;
  color: #334155 !important;
}

[data-theme="light"] .author-tabs-strip .ts-btn-outline:hover,
.theme-light .author-tabs-strip .ts-btn-outline:hover {
  background: #f1f5f9 !important;
  color: #0f172a !important;
}

/* Modal Boxes in Light Mode */
[data-theme="light"] .admin-modal-box,
.theme-light .admin-modal-box {
  background: #ffffff !important;
  color: #0f172a !important;
  border-color: #cbd5e1 !important;
  box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15) !important;
}

[data-theme="light"] .admin-modal-title,
.theme-light .admin-modal-title {
  color: #0f172a !important;
}

[data-theme="light"] .admin-modal-label,
.theme-light .admin-modal-label {
  color: #334155 !important;
}

[data-theme="light"] .admin-modal-input,
[data-theme="light"] .admin-modal-textarea,
[data-theme="light"] .admin-modal-select,
.theme-light .admin-modal-input,
.theme-light .admin-modal-textarea,
.theme-light .admin-modal-select {
  background: #ffffff !important;
  color: #0f172a !important;
  border-color: #cbd5e1 !important;
}
</style>

<div class="container author-dashboard-wrap" style="padding-top: 2rem; margin-bottom: 5rem;">
  
  <!-- ========================================================================= -->
  <!-- 1. AUTHOR CAREER PROGRESSION & COMMAND HUD -->
  <!-- ========================================================================= -->
  <div class="ts-card" style="background: linear-gradient(135deg, rgba(13,21,39,0.95), rgba(7,13,24,0.95)); border: 1px solid rgba(99,102,241,0.35); padding: 2rem; border-radius: 16px; margin-bottom: 2rem; box-shadow: 0 10px 40px rgba(0,0,0,0.6);">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1.5rem; margin-bottom: 1.75rem;">
      <div style="display: flex; gap: 1.25rem; align-items: center;">
        <!-- Avatar with Glow Ring -->
        <div style="position: relative;">
          <?php if (!empty($author['avatar_url'])): ?>
            <img src="<?= htmlspecialchars(image_url($author['avatar_url'])) ?>" alt="<?= htmlspecialchars($author['display_name']) ?>" style="width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 2px solid <?= $badgeColor ?>; box-shadow: 0 0 20px <?= $badgeColor ?>44;">
          <?php else: ?>
            <div style="width: 72px; height: 72px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #ec4899); display: flex; align-items: center; justify-content: center; font-size: 1.75rem; font-weight: 800; color: #ffffff; box-shadow: 0 0 20px rgba(99,102,241,0.4);">
              <?= strtoupper(substr($author['display_name'] ?? 'A', 0, 1)) ?>
            </div>
          <?php endif; ?>
          <span style="position: absolute; bottom: 0; right: 0; background: #10b981; width: 16px; height: 16px; border-radius: 50%; border: 3px solid #070d18;" title="Verified Creator Online"></span>
        </div>

        <div>
          <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
            <h1 style="font-size: 1.85rem; font-weight: 800; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif;">
              <?= htmlspecialchars($author['display_name'] ?? $user['username']) ?>
            </h1>
            <?= $tierBadgeHtml ?? '' ?>
            <?php if (!empty($author['verification_badge'])): ?>
              <span class="ts-badge ts-badge-green" style="font-size: 0.68rem;">VERIFIED PIONEER</span>
            <?php endif; ?>
          </div>

          <p style="color: #94a3b8; font-size: 0.88rem; margin: 0.35rem 0 0 0; max-width: 600px; line-height: 1.45;">
            <?= htmlspecialchars($author['bio'] ?: 'Technical author & systems architect exploring frontier AI and software design patterns.') ?>
          </p>
        </div>
      </div>

      <!-- Action Buttons -->
      <div style="display: flex; gap: 0.6rem; flex-wrap: wrap;">
        <button type="button" onclick="openProfileModal()" class="ts-btn ts-btn-outline ts-btn-sm">
          <i data-lucide="user"></i> Edit Profile ⚙️
        </button>
        <a href="<?= url('/author/editor') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="border-color: #38bdf8; color: #38bdf8;">
          <i data-lucide="file-up"></i> Import Word (.docx) 📄
        </a>
        <a href="<?= url('/author/editor') ?>" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #6366f1, #38bdf8); font-weight: 700;">
          <i data-lucide="plus-circle"></i> Create Publication ⚡
        </a>
      </div>
    </div>

    <!-- Career Progression Bar -->
    <div style="background: #04070e; border: 1px solid rgba(148,163,184,0.12); border-radius: 10px; padding: 1rem 1.25rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem; font-size: 0.82rem; flex-wrap: wrap; gap: 0.5rem;">
        <span style="color: #cbd5e1;">
          Rank: <strong style="color: <?= $badgeColor ?>;"><?= htmlspecialchars($tierName) ?></strong> 
          &bull; Reputation: <strong style="color: #f59e0b; font-family: monospace;"><?= number_format($author['reputation_score'] ?? 100) ?> XP</strong>
        </span>
        <span style="color: #64748b; font-size: 0.78rem;">
          Next Milestone: <strong style="color: #f8fafc;"><?= htmlspecialchars($nextTier) ?></strong> &bull; <?= $progressPercent ?>% Complete
        </span>
      </div>
      <div style="width: 100%; height: 8px; background: rgba(148,163,184,0.15); border-radius: 9999px; overflow: hidden;">
        <div style="width: <?= $progressPercent ?>%; height: 100%; background: linear-gradient(90deg, #6366f1, <?= $badgeColor ?>); border-radius: 9999px; transition: width 0.6s ease;"></div>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 2. TELEMETRY INTELLIGENCE ROW -->
  <!-- ========================================================================= -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 200px), 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
    <div class="ts-card" style="padding: 1.25rem;">
      <div style="font-size: 0.78rem; text-transform: uppercase; color: var(--ts-text-tertiary); font-weight: 700;">Total Reads</div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #38bdf8; margin-top: 0.25rem; font-family: 'Outfit', sans-serif;">
        <?= number_format((int)($stats['total_views'] ?? 0)) ?>
      </div>
      <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">Verified reader visits</div>
    </div>

    <div class="ts-card" style="padding: 1.25rem;">
      <div style="font-size: 0.78rem; text-transform: uppercase; color: var(--ts-text-tertiary); font-weight: 700;">Community Reactions</div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #ec4899; margin-top: 0.25rem; font-family: 'Outfit', sans-serif;">
        <?= number_format((int)($stats['total_reactions'] ?? 0)) ?>
      </div>
      <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">Upvotes &amp; bookmarks</div>
    </div>

    <div class="ts-card" style="padding: 1.25rem;">
      <div style="font-size: 0.78rem; text-transform: uppercase; color: var(--ts-text-tertiary); font-weight: 700;">Discussions</div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #a855f7; margin-top: 0.25rem; font-family: 'Outfit', sans-serif;">
        <?= number_format((int)($stats['total_comments'] ?? 0)) ?>
      </div>
      <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">Comments received</div>
    </div>

    <div class="ts-card" style="padding: 1.25rem;">
      <div style="font-size: 0.78rem; text-transform: uppercase; color: var(--ts-text-tertiary); font-weight: 700;">Reading Time Delivered</div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #10b981; margin-top: 0.25rem; font-family: 'Outfit', sans-serif;">
        <?= round($estReadTimeMinutes / 60, 1) ?>h
      </div>
      <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">~<?= number_format($totalWords) ?> words published</div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 3. CREATOR STUDIO MASTER TABS NAVIGATION STRIP -->
  <!-- ========================================================================= -->
  <div class="author-tabs-strip" style="display: flex; gap: 0.5rem; border-bottom: 1px solid rgba(148,163,184,0.15); margin-bottom: 2rem; flex-wrap: wrap;">
    <button type="button" onclick="switchAuthorMainTab('publications')" id="amaintab_publications" class="ts-btn ts-btn-sm ts-btn-primary" style="padding: 0.5rem 1.15rem; font-weight: 700;">
      📝 Publications (<?= count($contents) ?>)
    </button>
    <button type="button" onclick="switchAuthorMainTab('prompts')" id="amaintab_prompts" class="ts-btn ts-btn-sm ts-btn-outline" style="padding: 0.5rem 1.15rem; font-weight: 700; color: #a855f7; border-color: rgba(168,85,247,0.3);">
      🎨 Prompts Studio (<?= count($authorPrompts ?? []) ?>)
    </button>
    <button type="button" onclick="switchAuthorMainTab('payouts')" id="amaintab_payouts" class="ts-btn ts-btn-sm ts-btn-outline" style="padding: 0.5rem 1.15rem; font-weight: 700; color: #34d399; border-color: rgba(16,185,129,0.3);">
      💳 Royalties &amp; Ledger (<?= count($payouts) ?>)
    </button>
    <button type="button" onclick="switchAuthorMainTab('tipjar')" id="amaintab_tipjar" class="ts-btn ts-btn-sm ts-btn-outline" style="padding: 0.5rem 1.15rem; font-weight: 700; color: #f59e0b; border-color: rgba(245,158,11,0.3);">
      ⚡ Crypto Tip Jar
    </button>
    <button type="button" onclick="switchAuthorMainTab('ambassador')" id="amaintab_ambassador" class="ts-btn ts-btn-sm ts-btn-outline" style="padding: 0.5rem 1.15rem; font-weight: 700; color: #fbbf24; border-color: rgba(245,158,11,0.3);">
      🤝 Ambassador Hub (<?= $ambassadorData['total_referrals'] ?? 0 ?>)
    </button>
    <button type="button" onclick="switchAuthorMainTab('collaborations')" id="amaintab_collaborations" class="ts-btn ts-btn-sm ts-btn-outline" style="padding: 0.5rem 1.15rem; font-weight: 700; color: #38bdf8; border-color: rgba(56,189,248,0.3);">
      👥 Co-Authoring (<?= count($collaborations ?? []) ?>)
    </button>
    <button type="button" onclick="switchAuthorMainTab('analytics')" id="amaintab_analytics" class="ts-btn ts-btn-sm ts-btn-outline" style="padding: 0.5rem 1.15rem; font-weight: 700; color: #38bdf8; border-color: rgba(56,189,248,0.3);">
      📊 Analytics &amp; Telemetry
    </button>
    <button type="button" onclick="switchAuthorMainTab('feedback')" id="amaintab_feedback" class="ts-btn ts-btn-sm ts-btn-outline" style="padding: 0.5rem 1.15rem; font-weight: 700; color: #ec4899; border-color: rgba(236,72,153,0.3);">
      💬 Feedback (<?= count($recentComments ?? []) ?>)
    </button>
    <button type="button" onclick="switchAuthorMainTab('community')" id="amaintab_community" class="ts-btn ts-btn-sm ts-btn-outline" style="padding: 0.5rem 1.15rem; font-weight: 700; color: #a855f7; border-color: rgba(168,85,247,0.3);">
      👥 Community (<?= count($followers ?? []) ?>)
    </button>
    <button type="button" onclick="switchAuthorMainTab('security')" id="amaintab_security" class="ts-btn ts-btn-sm ts-btn-outline" style="padding: 0.5rem 1.15rem; font-weight: 700; color: #10b981; border-color: rgba(16,185,129,0.3);">
      🛡️ Security &amp; Devices (<?= count($userSessions ?? []) ?>)
    </button>
    <button type="button" onclick="openProfileModal()" class="ts-btn ts-btn-sm ts-btn-outline" style="padding: 0.5rem 1.1rem; font-weight: 700; margin-left: auto;">
      ⚙️ Settings
    </button>
  </div>

  <!-- PANE 1: PUBLICATIONS HUB -->
  <div id="amainpane_publications">
    <!-- ========================================================================= -->
    <!-- CREATOR PARTNER MONETIZATION PROGRAM & CRITERIA TRACKER HUD -->
    <!-- ========================================================================= -->
    <?php if (!empty($monetization)): ?>
      <div class="ts-card" style="padding: 1.75rem; margin-bottom: 2rem; background: linear-gradient(135deg, rgba(15,23,42,0.92), rgba(30,41,59,0.7)); border: 1px solid rgba(56,189,248,0.25); border-left: 4px solid <?= $monetization['is_monetized'] ? '#10b981' : ($monetization['status'] === 'applied' ? '#f59e0b' : ($monetization['is_eligible'] ? '#38bdf8' : '#6366f1')) ?>; border-radius: 14px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.35rem;">
              <span style="font-size: 1.2rem;">💎</span>
              <h3 style="font-size: 1.25rem; margin: 0; color: #f8fafc; font-family: 'Outfit', sans-serif; font-weight: 700;">
                TechScout Creator Partner Monetization Program
              </h3>
            </div>
            <p style="font-size: 0.88rem; color: #94a3b8; margin: 0; max-width: 680px;">
              Publish high-impact technical articles, build your follower network, and unlock direct reader royalties, promotional rev-share, and cash disbursals.
            </p>
          </div>

          <div>
            <?php if (!empty($monetization['is_monetized'])): ?>
              <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.35rem;">
                <span class="ts-badge ts-badge-green" style="font-size: 0.82rem; padding: 0.4rem 0.85rem;">
                  ACTIVE PARTNER ✅ &bull; Earning Monetization
                </span>
                <?php if (!empty($monetization['approved_at'])): ?>
                  <span style="font-size: 0.72rem; color: #34d399; font-family: var(--ts-font-mono);">
                    Approved <?= \App\Core\DateHelper::formatDateTime($monetization['approved_at']) ?>
                  </span>
                <?php endif; ?>
              </div>
            <?php elseif ($monetization['status'] === 'applied'): ?>
              <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.35rem;">
                <span class="ts-badge ts-badge-pink" style="font-size: 0.82rem; padding: 0.4rem 0.85rem; background: rgba(245,158,11,0.15); color: #fbbf24; border-color: rgba(245,158,11,0.4);">
                  APPLICATION UNDER REVIEW ⏳
                </span>
                <?php if (!empty($monetization['applied_at'])): ?>
                  <span style="font-size: 0.72rem; color: #fbbf24; font-family: var(--ts-font-mono);">
                    Submitted <?= \App\Core\DateHelper::formatDateTime($monetization['applied_at']) ?>
                  </span>
                <?php endif; ?>
              </div>
            <?php elseif ($monetization['is_eligible']): ?>
              <form action="<?= url('/author/monetization/apply') ?>" method="POST" id="monetizeApplyForm" onsubmit="handleMonetizationApplySubmit(event)">
                <button type="submit" id="monetizeApplyBtn" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #10b981, #38bdf8); font-weight: 800; border: none; box-shadow: 0 0 20px rgba(16,185,129,0.35); padding: 0.55rem 1.4rem; cursor: pointer;">
                  🚀 Apply for Partner Monetization
                </button>
              </form>
            <?php else: ?>
              <span class="ts-badge ts-badge-indigo" style="font-size: 0.82rem; padding: 0.4rem 0.85rem;">
                PROGRESS: <?= $monetization['overall_progress'] ?>% UNLOCKED 📈
              </span>
            <?php endif; ?>
          </div>
        </div>

        <?php if ($monetization['status'] === 'applied'): ?>
          <div style="background: rgba(245,158,11,0.08); border: 1px dashed rgba(245,158,11,0.3); border-radius: 8px; padding: 0.85rem 1.15rem; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.75rem;">
            <div style="font-size: 1.25rem;">⏳</div>
            <div style="font-size: 0.82rem; color: #fde68a; line-height: 1.45;">
              <strong>Application Successfully Queued:</strong> Your Partner Monetization application is locked and under active editorial review. Our partner team verifies follower authentic engagement and technical architecture quality before finalizing approval.
            </div>
          </div>
        <?php endif; ?>

        <!-- 4 Core Criteria Progress Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1rem;">
          <!-- 1. Followers -->
          <?php $mf = $monetization['metrics']['followers']; ?>
          <div style="background: rgba(15,23,42,0.6); padding: 1rem; border-radius: 10px; border: 1px solid rgba(148,163,184,0.12);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
              <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">1. Verified Followers</span>
              <span style="font-size: 0.72rem; color: <?= $mf['met'] ? '#10b981' : '#f59e0b' ?>; font-weight: 700;"><?= $mf['met'] ? '✓ UNLOCKED' : number_format($mf['percent'], 1) . '%' ?></span>
            </div>
            <div style="font-size: 1.15rem; font-weight: 800; color: #f8fafc; font-family: var(--ts-font-mono); margin-bottom: 0.4rem;">
              <?= number_format($mf['current']) ?> <span style="font-size: 0.75rem; color: #64748b;">/ 1,000 req</span>
            </div>
            <div style="width: 100%; height: 5px; background: rgba(148,163,184,0.1); border-radius: 999px; overflow: hidden;">
              <div style="width: <?= min(100, max(4, $mf['percent'])) ?>%; height: 100%; background: <?= $mf['met'] ? '#10b981' : '#38bdf8' ?>; border-radius: 999px;"></div>
            </div>
          </div>

          <!-- 2. 100k Qualified Views (≥10s read) -->
          <?php $mv = $monetization['metrics']['qualified_views']; ?>
          <div style="background: rgba(15,23,42,0.6); padding: 1rem; border-radius: 10px; border: 1px solid rgba(148,163,184,0.12);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
              <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">2. Qualified Reads (&ge;10s)</span>
              <span style="font-size: 0.72rem; color: <?= $mv['met'] ? '#10b981' : '#f59e0b' ?>; font-weight: 700;"><?= $mv['met'] ? '✓ UNLOCKED' : number_format($mv['percent'], 1) . '%' ?></span>
            </div>
            <div style="font-size: 1.15rem; font-weight: 800; color: #38bdf8; font-family: var(--ts-font-mono); margin-bottom: 0.4rem;">
              <?= number_format($mv['current']) ?> <span style="font-size: 0.75rem; color: #64748b;">/ 100,000 req</span>
            </div>
            <div style="width: 100%; height: 5px; background: rgba(148,163,184,0.1); border-radius: 999px; overflow: hidden;">
              <div style="width: <?= min(100, max(4, $mv['percent'])) ?>%; height: 100%; background: <?= $mv['met'] ? '#10b981' : '#38bdf8' ?>; border-radius: 999px;"></div>
            </div>
          </div>

          <!-- 3. Published Articles -->
          <?php $mp = $monetization['metrics']['published_articles']; ?>
          <div style="background: rgba(15,23,42,0.6); padding: 1rem; border-radius: 10px; border: 1px solid rgba(148,163,184,0.12);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
              <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">3. Active Publications</span>
              <span style="font-size: 0.72rem; color: <?= $mp['met'] ? '#10b981' : '#f59e0b' ?>; font-weight: 700;"><?= $mp['met'] ? '✓ UNLOCKED' : $mp['current'] . '/5' ?></span>
            </div>
            <div style="font-size: 1.15rem; font-weight: 800; color: #c084fc; font-family: var(--ts-font-mono); margin-bottom: 0.4rem;">
              <?= $mp['current'] ?> <span style="font-size: 0.75rem; color: #64748b;">/ 5 req</span>
            </div>
            <div style="width: 100%; height: 5px; background: rgba(148,163,184,0.1); border-radius: 999px; overflow: hidden;">
              <div style="width: <?= min(100, max(4, $mp['percent'])) ?>%; height: 100%; background: <?= $mp['met'] ? '#10b981' : '#a855f7' ?>; border-radius: 999px;"></div>
            </div>
          </div>

          <!-- 4. Quality Score -->
          <?php $mq = $monetization['metrics']['quality_score']; ?>
          <div style="background: rgba(15,23,42,0.6); padding: 1rem; border-radius: 10px; border: 1px solid rgba(148,163,184,0.12);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
              <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">4. Quality Score</span>
              <span style="font-size: 0.72rem; color: <?= $mq['met'] ? '#10b981' : '#f59e0b' ?>; font-weight: 700;"><?= $mq['met'] ? '✓ MET' : 'Needs &ge;70%' ?></span>
            </div>
            <div style="font-size: 1.15rem; font-weight: 800; color: #34d399; font-family: var(--ts-font-mono); margin-bottom: 0.4rem;">
              <?= round((float)$mq['current']) ?>% <span style="font-size: 0.75rem; color: #64748b;">/ 70% min</span>
            </div>
            <div style="width: 100%; height: 5px; background: rgba(148,163,184,0.1); border-radius: 999px; overflow: hidden;">
              <div style="width: <?= min(100, max(4, $mq['percent'])) ?>%; height: 100%; background: <?= $mq['met'] ? '#10b981' : '#10b981' ?>; border-radius: 999px;"></div>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  <!-- ========================================================================= -->
  <!-- 3. EDITORIAL DIRECT FEEDBACK STATION (IF REVISIONS NEEDED) -->
  <!-- ========================================================================= -->
  <?php if (!empty($changesRequiredItems)): ?>
    <div style="background: rgba(245,158,11,0.1); border: 1px solid #f59e0b; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem;">
      <h3 style="color: #f59e0b; margin: 0 0 0.75rem 0; font-size: 1.15rem; display: flex; align-items: center; gap: 0.5rem;">
        <i data-lucide="alert-circle"></i> Action Required: Editorial Feedback on Your Submissions
      </h3>
      <div style="display: flex; flex-direction: column; gap: 1rem;">
        <?php foreach ($changesRequiredItems as $cr): ?>
          <div style="background: rgba(15,23,42,0.85); border: 1px solid rgba(245,158,11,0.3); border-radius: 8px; padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
              <div style="font-weight: 700; color: #f8fafc; font-size: 1rem; margin-bottom: 0.35rem;"><?= htmlspecialchars($cr['title']) ?></div>
              <?php if (!empty($cr['editorial_notes'])): ?>
                <div style="color: #fcd34d; font-size: 0.85rem; background: rgba(245,158,11,0.08); padding: 0.4rem 0.8rem; border-radius: 6px; border-left: 3px solid #f59e0b;">
                  <strong>Editor Note:</strong> <?= htmlspecialchars($cr['editorial_notes']) ?>
                </div>
              <?php endif; ?>
            </div>
            <a href="<?= url('/author/editor/' . $cr['id']) ?>" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #f59e0b; border-color: #f59e0b; color: #000; font-weight: 700;">
              Revise &amp; Resubmit ✏️
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

<?php
$canPrompts = !empty($author['can_publish_prompts']) || \App\Core\Auth::isAdmin();
$canGpts = !empty($author['can_publish_gpts']) || \App\Core\Auth::isAdmin();
$canTools = !empty($author['can_publish_tools']) || \App\Core\Auth::isAdmin();
$canTemplates = !empty($author['can_use_templates']) || \App\Core\Auth::isAdmin();
?>

  <!-- ========================================================================= -->
  <!-- 4. QUICK LAUNCHPAD ACTION DECK (DYNAMIC BASED ON PERMISSIONS) -->
  <!-- ========================================================================= -->
  <div style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
      <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0;">Creator Launchpad &amp; Authoring Capabilities</h3>
      <div style="display: flex; gap: 0.4rem; align-items: center; flex-wrap: wrap;">
        <span class="ts-badge ts-badge-cyan" style="font-size: 0.7rem;">Articles: Authorized ✓</span>
        <?php if ($canTools): ?>
          <span class="ts-badge ts-badge-green" style="font-size: 0.7rem;">Tools: Authorized ✓</span>
        <?php endif; ?>
        <?php if ($canGpts): ?>
          <span class="ts-badge ts-badge-pink" style="font-size: 0.7rem;">GPTs: Authorized ✓</span>
        <?php endif; ?>
        <?php if ($canPrompts): ?>
          <span class="ts-badge ts-badge-cyan" style="font-size: 0.7rem;">Prompts: Authorized ✓</span>
        <?php endif; ?>
        <?php if ($canTemplates): ?>
          <span class="ts-badge" style="font-size: 0.7rem; background: rgba(168,85,247,0.2); color: #c084fc; border: 1px solid rgba(168,85,247,0.4);">Templates: Active 📋</span>
        <?php endif; ?>
      </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 200px), 1fr)); gap: 1rem;">
      <!-- Technical Article (Always available for verified authors) -->
      <a href="<?= url('/author/editor') ?>?type=article" class="ts-card" style="text-decoration: none; padding: 1.25rem; border-color: rgba(99,102,241,0.3); transition: transform 0.2s ease;">
        <div style="font-size: 1.75rem; margin-bottom: 0.5rem;">✍️</div>
        <strong style="color: #f8fafc; display: block; font-size: 0.95rem;">Technical Article</strong>
        <span style="font-size: 0.78rem; color: #94a3b8;">System blueprints &amp; deep dives</span>
      </a>

      <?php if ($canTools): ?>
        <a href="<?= url('/author/editor') ?>?type=ai_tool" class="ts-card" style="text-decoration: none; padding: 1.25rem; border-color: rgba(0,240,255,0.3); transition: transform 0.2s ease;">
          <div style="font-size: 1.75rem; margin-bottom: 0.5rem;">⚡</div>
          <strong style="color: #f8fafc; display: block; font-size: 0.95rem;">Submit AI Tool</strong>
          <span style="font-size: 0.78rem; color: #94a3b8;">Index in Developer Hub</span>
        </a>
      <?php endif; ?>

      <?php if ($canGpts): ?>
        <a href="<?= url('/author/editor') ?>?type=gpt_listing" class="ts-card" style="text-decoration: none; padding: 1.25rem; border-color: rgba(236,72,153,0.3); transition: transform 0.2s ease;">
          <div style="font-size: 1.75rem; margin-bottom: 0.5rem;">🤖</div>
          <strong style="color: #f8fafc; display: block; font-size: 0.95rem;">Custom GPT</strong>
          <span style="font-size: 0.78rem; color: #94a3b8;">Publish verified GPT listing</span>
        </a>
      <?php endif; ?>

      <?php if ($canPrompts): ?>
        <a href="<?= url('/author/editor') ?>?type=prompt" class="ts-card" style="text-decoration: none; padding: 1.25rem; border-color: rgba(16,185,129,0.3); transition: transform 0.2s ease;">
          <div style="font-size: 1.75rem; margin-bottom: 0.5rem;">📑</div>
          <strong style="color: #f8fafc; display: block; font-size: 0.95rem;">Prompt Architecture</strong>
          <span style="font-size: 0.78rem; color: #94a3b8;">LLM system prompts &amp; recipes</span>
        </a>
      <?php endif; ?>

      <a href="<?= url('/campaigns') ?>" class="ts-card" style="text-decoration: none; padding: 1.25rem; border-color: rgba(245,158,11,0.3); transition: transform 0.2s ease;">
        <div style="font-size: 1.75rem; margin-bottom: 0.5rem;">🏆</div>
        <strong style="color: #f8fafc; display: block; font-size: 0.95rem;">Creator Bounties</strong>
        <span style="font-size: 0.78rem; color: #94a3b8;">Participate in hackathons</span>
      </a>

      <?php if (!$canTools || !$canGpts || !$canPrompts): ?>
        <div class="ts-card" style="padding: 1.25rem; border-color: rgba(148,163,184,0.15); background: rgba(15,23,42,0.4); display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <div style="font-size: 1.5rem; margin-bottom: 0.4rem;">🔒</div>
            <strong style="color: #cbd5e1; display: block; font-size: 0.88rem;">Need Tool / GPT Access?</strong>
            <span style="font-size: 0.75rem; color: #64748b;">Publish 2+ verified articles to unlock specialized formats.</span>
          </div>
          <a href="<?= url('/support') ?>" style="font-size: 0.75rem; color: #38bdf8; text-decoration: none; margin-top: 0.5rem; font-weight: 600;">Request Permission Upgrade &rarr;</a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 5. UNIFIED MULTI-ENTITY CONTENT MATRIX & FILTER BAR -->
  <!-- ========================================================================= -->
  <div class="ts-card" style="padding: 1.75rem; margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
      <div>
        <h3 style="font-size: 1.25rem; color: #f8fafc; margin: 0;">My Publications &amp; Submissions</h3>
        <p style="color: #64748b; font-size: 0.85rem; margin: 0.2rem 0 0 0;">Manage your entire technical catalog across articles, tools, GPTs, and prompt architectures.</p>
      </div>

      <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <input type="text" id="contentSearchInput" onkeyup="filterAuthorContent()" placeholder="Search your titles..." style="background: #05070b; border: 1px solid rgba(148,163,184,0.2); padding: 0.45rem 0.85rem; border-radius: 6px; color: #f8fafc; font-size: 0.85rem; min-width: 220px;">
      </div>
    </div>

    <!-- Status & Type Filter Pills -->
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap; border-bottom: 1px solid rgba(148,163,184,0.12); padding-bottom: 1rem;">
      <button type="button" onclick="filterByStatus('all')" class="ts-btn ts-btn-sm ts-btn-primary author-status-btn" id="sbtn-all">
        All Works (<?= count($contents) ?>)
      </button>
      <button type="button" onclick="filterByStatus('published')" class="ts-btn ts-btn-sm ts-btn-outline author-status-btn" id="sbtn-published">
        Published (<?= $publishedCount ?>)
      </button>
      <button type="button" onclick="filterByStatus('in_review')" class="ts-btn ts-btn-sm ts-btn-outline author-status-btn" id="sbtn-in_review">
        In Review (<?= $inReviewCount ?>)
      </button>
      <button type="button" onclick="filterByStatus('changes_required')" class="ts-btn ts-btn-sm ts-btn-outline author-status-btn" id="sbtn-changes_required" style="color: #f59e0b;">
        Needs Revisions (<?= $changesReqCount ?>)
      </button>
      <button type="button" onclick="filterByStatus('draft')" class="ts-btn ts-btn-sm ts-btn-outline author-status-btn" id="sbtn-draft">
        Drafts (<?= $draftCount ?>)
      </button>
    </div>

    <!-- Content Items List -->
    <div id="authorContentList" style="display: flex; flex-direction: column; gap: 1rem;">
      <?php if (!empty($contents)): ?>
        <?php foreach ($contents as $item): ?>
          <div class="author-content-card ts-card" data-status="<?= htmlspecialchars($item['status']) ?>" data-type="<?= htmlspecialchars($item['content_type']) ?>" data-title="<?= htmlspecialchars(strtolower($item['title'])) ?>" style="padding: 1.25rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            
            <div style="flex: 1; min-width: 280px;">
              <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.4rem; flex-wrap: wrap;">
                <span class="ts-badge ts-badge-cyan" style="font-size: 0.68rem; text-transform: uppercase;">
                  <?= htmlspecialchars(str_replace('_', ' ', $item['content_type'])) ?>
                </span>
                
                <?php if ($item['status'] === 'published'): ?>
                  <span class="ts-badge ts-badge-green" style="font-size: 0.68rem;">PUBLISHED ✓</span>
                <?php elseif ($item['status'] === 'in_review'): ?>
                  <span class="ts-badge ts-badge-pink" style="font-size: 0.68rem;">IN REVIEW ⏳</span>
                <?php elseif ($item['status'] === 'changes_required'): ?>
                  <span class="strike-badge" style="font-size: 0.68rem;">CHANGES REQUESTED ⚠️</span>
                <?php else: ?>
                  <span class="ts-badge" style="font-size: 0.68rem;">DRAFT 📝</span>
                <?php endif; ?>

                <?php if (!empty($item['category_name'])): ?>
                  <span style="font-size: 0.75rem; color: #94a3b8;">&bull; <?= htmlspecialchars($item['category_name']) ?></span>
                <?php endif; ?>
              </div>

              <h4 style="font-size: 1.1rem; color: #f8fafc; margin: 0 0 0.35rem 0;">
                <?php if ($item['status'] === 'published'): ?>
                  <a href="<?= $getEntityUrl($item['content_type'], $item['slug']) ?>" target="_blank" style="color: inherit;">
                    <?= htmlspecialchars($item['title']) ?> ↗
                  </a>
                <?php else: ?>
                  <a href="<?= url('/author/editor/' . $item['id']) ?>" style="color: inherit;">
                    <?= htmlspecialchars($item['title']) ?>
                  </a>
                <?php endif; ?>
              </h4>

              <div style="display: flex; gap: 1rem; font-size: 0.78rem; color: #64748b; flex-wrap: wrap;">
                <span>Updated <?= date('M j, Y', strtotime($item['updated_at'])) ?></span>
                <span>&bull; <?= number_format($item['view_count']) ?> reads</span>
                <span>&bull; <?= $item['reaction_count'] ?> reactions</span>
                <span>&bull; Quality Score: <strong style="color: #38bdf8;"><?= round((float)($item['quality_score'] ?? 80)) ?>/100</strong></span>
              </div>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 0.4rem; align-items: center; flex-wrap: wrap;">
              <a href="<?= url('/author/analytics/content/' . $item['id']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; color: #38bdf8; border-color: rgba(56,189,248,0.3);" title="View detailed readership and engagement analytics">
                <i data-lucide="bar-chart-2" style="width: 13px;"></i> Stats
              </a>
              <a href="<?= url('/author/editor/' . $item['id']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem;">
                <i data-lucide="edit-3" style="width: 14px;"></i> Edit
              </a>
              <?php if ($item['status'] === 'published'): ?>
                <a href="<?= $getEntityUrl($item['content_type'], $item['slug']) ?>" target="_blank" class="ts-btn ts-btn-primary ts-btn-sm" style="font-size: 0.75rem; background: #6366f1; border-color: #6366f1;">
                  View Live ↗
                </a>
              <?php elseif ($item['status'] === 'draft'): ?>
                <form action="<?= url('/author/content/delete/' . $item['id']) ?>" method="POST" onsubmit="return confirm('Delete this draft?');" style="display: inline;">
                  <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; color: #ef4444;" title="Delete Draft">
                    🗑
                  </button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div style="text-align: center; padding: 3rem; color: #64748b;">
          <i data-lucide="feather" style="width: 48px; height: 48px; color: #6366f1; margin-bottom: 1rem;"></i>
          <h3>No publications created yet</h3>
          <p style="color: #94a3b8; font-size: 0.9rem; margin-top: 0.5rem;">Launch your first technical article or AI tool review to begin earning creator reputation!</p>
          <a href="<?= url('/author/editor') ?>" class="ts-btn ts-btn-primary" style="margin-top: 1rem;">Launch Editor ⚡</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
  </div><!-- /amainpane_publications -->

  <?php
    $userSessions = \App\Models\SessionManager::getUserSessions((int)$user['id'], \App\Core\Session::get('auth_session_token'));
  ?>
  <!-- ========================================================================= -->
  <!-- 13. PANE 7: SECURITY, ACTIVE SESSION DEVICES & REMOTE REVOCATION HUB -->
  <!-- ========================================================================= -->
  <div id="amainpane_security" style="display: none;">
    <div class="ts-card" style="margin-bottom: 2rem; padding: 1.75rem; background: linear-gradient(135deg, rgba(15,23,42,0.9), rgba(10,18,30,0.85)); border: 1px solid rgba(16,185,129,0.35); border-radius: 16px;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
        <div>
          <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
            <span class="ts-badge ts-badge-green">ACCOUNT PERIMETER</span>
            <span style="font-size: 0.8rem; color: #94a3b8;">Active Browser Logins &amp; Real-time Session Security</span>
          </div>
          <h3 style="font-size: 1.25rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
            🛡️ Security Station &amp; Active Devices (<?= count($userSessions) ?>)
          </h3>
        </div>

        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
          <button type="button" onclick="openPasswordModal()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #6366f1, #38bdf8); border: none; font-weight: 700;">
            <i data-lucide="key"></i> Change Password 🔐
          </button>
          <?php if (count($userSessions) > 1): ?>
            <form action="<?= url('/user/sessions/terminate-others') ?>" method="POST" onsubmit="return confirm('Terminate all other active browser sessions? You will stay logged in on this device.');">
              <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.4); font-weight: 700;">
                <i data-lucide="log-out"></i> Terminate All Other Sessions 🔒
              </button>
            </form>
          <?php endif; ?>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
        <?php foreach ($userSessions as $sess): ?>
          <div style="background: #05070b; border: 1px solid <?= !empty($sess['is_current_client']) ? 'rgba(16,185,129,0.5)' : 'rgba(148,163,184,0.15)' ?>; border-radius: 12px; padding: 1.25rem; position: relative; box-shadow: <?= !empty($sess['is_current_client']) ? '0 0 20px rgba(16,185,129,0.15)' : 'none' ?>;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
              <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 40px; height: 40px; border-radius: 8px; background: rgba(99,102,241,0.15); display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 1.2rem;">
                  <?= $sess['device_type'] === 'mobile' ? '📱' : ($sess['device_type'] === 'tablet' ? '📟' : '💻') ?>
                </div>
                <div>
                  <div style="color: #f8fafc; font-weight: 700; font-size: 0.95rem;"><?= htmlspecialchars($sess['os_name']) ?></div>
                  <div style="font-size: 0.78rem; color: #38bdf8;"><?= htmlspecialchars($sess['browser_name']) ?></div>
                </div>
              </div>

              <?php if (!empty($sess['is_current_client'])): ?>
                <span class="ts-badge ts-badge-green" style="font-size: 0.65rem; padding: 0.15rem 0.5rem;">THIS DEVICE 🟢</span>
              <?php endif; ?>
            </div>

            <div style="font-size: 0.78rem; color: #94a3b8; margin-bottom: 0.35rem;">
              <strong>IP Address:</strong> <code style="color: #cbd5e1;"><?= htmlspecialchars($sess['ip_address']) ?></code>
            </div>
            <div style="font-size: 0.78rem; color: #64748b; margin-bottom: 0.75rem;">
              <strong>Last Active:</strong> <?= \App\Core\DateHelper::formatDateTime($sess['last_active_at']) ?>
            </div>

            <?php if (empty($sess['is_current_client'])): ?>
              <div style="border-top: 1px solid rgba(148,163,184,0.1); padding-top: 0.65rem; display: flex; justify-content: flex-end;">
                <form action="<?= url('/user/sessions/revoke/' . $sess['id']) ?>" method="POST">
                  <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.2rem 0.6rem; font-size: 0.72rem; color: #ef4444; border-color: rgba(239,68,68,0.3);">
                    Revoke Device ✕
                  </button>
                </form>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 4. PANE 2: AUTHOR REVENUE, BANKING & PAYOUT LEDGER -->
  <!-- ========================================================================= -->
  <div id="amainpane_payouts" style="display: none;">
    <!-- 1. LINKED PAYOUT METHODS & SETTLEMENT HUB -->
    <div class="ts-card" style="margin-bottom: 2rem; padding: 1.75rem; background: linear-gradient(135deg, rgba(15,23,42,0.9), rgba(30,41,59,0.7)); border: 1px solid rgba(56,189,248,0.25);">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
        <div>
          <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
            <span class="ts-badge ts-badge-cyan">SETTLEMENT DESTINATIONS</span>
            <span style="font-size: 0.8rem; color: #94a3b8;">Bank Accounts, PayPal, and USDC Wallets</span>
          </div>
          <h3 style="font-size: 1.25rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
            🏦 Linked Payout Methods
          </h3>
        </div>

        <button type="button" onclick="openAddPayoutMethodModal()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.4); font-weight: 700;">
          <i data-lucide="plus"></i> Link Payout Method
        </button>
      </div>

      <?php if (!empty($payoutMethods)): ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
          <?php foreach ($payoutMethods as $pm): ?>
            <div style="background: rgba(15,23,42,0.8); border: 1px solid <?= !empty($pm['is_default']) ? 'rgba(16,185,129,0.4)' : 'rgba(148,163,184,0.15)' ?>; border-radius: 10px; padding: 1.25rem; position: relative;">
              <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                  <?php if ($pm['method_type'] === 'bank_wire' || $pm['method_type'] === 'iban_sepa'): ?>
                    <span style="font-size: 1.25rem;">🏦</span>
                    <div>
                      <div style="font-weight: 800; color: #f8fafc; font-size: 0.92rem;"><?= htmlspecialchars($pm['bank_name'] ?: 'Bank Wire / SEPA') ?></div>
                      <div style="font-size: 0.75rem; color: #94a3b8;">Holder: <?= htmlspecialchars($pm['account_holder_name']) ?></div>
                    </div>
                  <?php elseif ($pm['method_type'] === 'paypal'): ?>
                    <span style="font-size: 1.25rem;">🅿️</span>
                    <div>
                      <div style="font-weight: 800; color: #f8fafc; font-size: 0.92rem;">PayPal Settlement</div>
                      <div style="font-size: 0.75rem; color: #38bdf8; font-family: monospace;"><?= htmlspecialchars($pm['account_email']) ?></div>
                    </div>
                  <?php elseif ($pm['method_type'] === 'crypto_usdc'): ?>
                    <span style="font-size: 1.25rem;">🪙</span>
                    <div>
                      <div style="font-weight: 800; color: #f8fafc; font-size: 0.92rem;">USDC (<?= htmlspecialchars($pm['crypto_network']) ?>)</div>
                      <div style="font-size: 0.75rem; color: #34d399; font-family: monospace; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($pm['crypto_wallet_address']) ?></div>
                    </div>
                  <?php else: ?>
                    <span style="font-size: 1.25rem;">💳</span>
                    <div>
                      <div style="font-weight: 800; color: #f8fafc; font-size: 0.92rem;"><?= htmlspecialchars(strtoupper(str_replace('_', ' ', $pm['method_type']))) ?></div>
                      <div style="font-size: 0.75rem; color: #38bdf8; font-family: monospace; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        <?= htmlspecialchars($pm['account_email'] ?: ($pm['account_number_or_iban'] ?: ($pm['crypto_wallet_address'] ?: $pm['account_holder_name']))) ?>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>

                <?php if (!empty($pm['is_default'])): ?>
                  <span class="ts-badge ts-badge-green" style="font-size: 0.68rem; padding: 0.15rem 0.5rem;">DEFAULT ✅</span>
                <?php endif; ?>
              </div>

              <?php if ($pm['method_type'] === 'bank_wire' || $pm['method_type'] === 'iban_sepa'): ?>
                <div style="font-size: 0.8rem; color: #cbd5e1; font-family: monospace; margin-bottom: 0.5rem;">
                  IBAN / Account: <?= htmlspecialchars(substr($pm['account_number_or_iban'], 0, 4) . ' **** ' . substr($pm['account_number_or_iban'], -4)) ?>
                </div>
              <?php endif; ?>

              <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(148,163,184,0.1); padding-top: 0.65rem; margin-top: 0.5rem;">
                <span style="font-size: 0.72rem; color: #10b981; font-weight: 600;">Verified &amp; Active ✓</span>
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                  <?php if (empty($pm['is_default'])): ?>
                    <form action="<?= url('/author/payout-methods/set-default/' . $pm['id']) ?>" method="POST" style="display: inline;">
                      <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">Set Default</button>
                    </form>
                  <?php endif; ?>
                  <form action="<?= url('/author/payout-methods/delete/' . $pm['id']) ?>" method="POST" onsubmit="return confirm('Remove this payout method?')" style="display: inline;">
                    <button type="submit" style="background: none; border: none; color: #f43f5e; font-size: 0.75rem; cursor: pointer; padding: 0.2rem 0.4rem;" title="Remove Method">
                      <i data-lucide="trash-2" style="width: 13px; height: 13px;"></i>
                    </button>
                  </form>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div style="background: rgba(245,158,11,0.08); border: 1px dashed rgba(245,158,11,0.3); border-radius: 10px; padding: 1.5rem; text-align: center;">
          <div style="font-size: 1.5rem; margin-bottom: 0.4rem;">⚠️</div>
          <h4 style="color: #fbbf24; font-size: 1rem; margin: 0 0 0.35rem 0;">No Linked Payout Method Found</h4>
          <p style="color: #94a3b8; font-size: 0.85rem; max-width: 500px; margin: 0 auto 1rem auto;">
            Withdrawals require a verified linked Bank Account, PayPal address, or Crypto USDC wallet. Link your method now to unlock cash payouts.
          </p>
          <button type="button" onclick="openAddPayoutMethodModal()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #10b981, #38bdf8); font-weight: 700;">
            <i data-lucide="plus-circle"></i> Link Bank / Payout Method Now
          </button>
        </div>
      <?php endif; ?>
    </div>

    <!-- 2. WITHDRAWAL & CASH PAYOUT REQUESTS QUEUE -->
    <div class="ts-card" style="margin-bottom: 2rem; padding: 1.75rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
        <div>
          <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
            <span class="ts-badge ts-badge-green">CREATOR ROYALTY CASHOUTS</span>
            <span style="font-size: 0.8rem; color: var(--ts-text-tertiary);">Transparent Double-Entry Ledger &amp; Settlement Audits</span>
          </div>
          <h3 style="font-size: 1.25rem; color: var(--ts-text-primary); margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
            💳 Withdrawal Requests History
          </h3>
        </div>

        <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
          <button type="button" onclick="openAddPayoutMethodModal()" class="ts-btn ts-btn-outline ts-btn-sm" style="font-weight: 600;">
            <i data-lucide="settings"></i> Payout Methods
          </button>
          <button type="button" onclick="openPayoutModal()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #10b981, #059669); border: none; font-weight: 700;">
            <i data-lucide="arrow-down-right"></i> Request Cash Payout 💵
          </button>
        </div>
      </div>

      <!-- Payout Ledger Table -->
      <?php if (!empty($payouts)): ?>
        <div style="overflow-x: auto; border-radius: 12px; border: 1px solid var(--ts-border-main);">
          <table class="admin-table" style="margin: 0; width: 100%;">
            <thead>
              <tr>
                <th style="white-space: nowrap;">Payout ID</th>
                <th style="white-space: nowrap;">Gross USD</th>
                <th style="white-space: nowrap;">Tax &amp; Deductions</th>
                <th style="white-space: nowrap;">Net Settlement</th>
                <th style="min-width: 210px;">Method &amp; Destination</th>
                <th style="min-width: 200px;">Status &amp; Verification Details</th>
                <th style="white-space: nowrap;">Tx Reference / Refund</th>
                <th style="white-space: nowrap;">Receipt Proof</th>
                <th style="white-space: nowrap;">Settlement Dossier</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($payouts as $p): ?>
                <?php 
                  $isRejected = ($p['status'] === 'rejected');
                  $isPaid = ($p['status'] === 'paid');
                  $isProcessing = ($p['status'] === 'processing');
                  $isPending = ($p['status'] === 'pending');
                  $net = (float)(isset($p['net_amount']) && (float)$p['net_amount'] > 0 ? $p['net_amount'] : (float)($p['amount_usd'] ?? 0) - (float)($p['fee_deducted'] ?? 0));
                  $taxVal = (float)($p['tax_deducted'] ?? round((float)($p['amount_usd'] ?? 0) * 0.05, 2));
                  $bankTaxVal = (float)($p['bank_tax_deducted'] ?? max(0, (float)($p['fee_deducted'] ?? 0) - $taxVal));
                ?>
                <tr style="<?= $isRejected ? 'background: rgba(239,68,68,0.03);' : '' ?>">
                  <td style="font-family: var(--ts-font-mono); font-weight: 700; color: #38bdf8;">
                    #<?= (int)$p['id'] ?>
                  </td>
                  <td style="font-weight: 800; color: var(--ts-text-primary); font-size: 0.95rem;">
                    $<?= number_format((float)$p['amount_usd'], 2) ?>
                  </td>
                  <td style="color: var(--ts-text-tertiary); font-size: 0.85rem;">
                    <span style="color: #f59e0b; font-weight: 600;">-$<?= number_format((float)($p['fee_deducted'] ?? 0), 2) ?></span>
                    <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.15rem;">
                      (5% Tax: $<?= number_format($taxVal, 2) ?><?= $bankTaxVal > 0 ? " + Bank: \${$bankTaxVal}" : '' ?>)
                    </div>
                  </td>
                  <td style="font-weight: 800; color: <?= $isRejected ? '#ef4444' : '#10b981' ?>; font-size: 0.95rem;">
                    $<?= number_format($net, 2) ?>
                  </td>
                  <td>
                    <div style="display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.25rem;">
                      <span class="ts-badge ts-badge-cyan" style="font-size: 0.68rem; text-transform: uppercase;">
                        <?= htmlspecialchars($p['payout_method']) ?>
                      </span>
                    </div>
                    <div style="font-size: 0.78rem; color: var(--ts-text-secondary); max-width: 220px; word-break: break-all; line-height: 1.4;">
                      <?= htmlspecialchars($p['payout_address']) ?>
                    </div>
                  </td>
                  <td>
                    <?php if ($isPaid): ?>
                      <span class="ts-badge ts-badge-green" style="font-size: 0.7rem; font-weight: 700;">PAID &amp; COMPLETED ✅</span>
                      <div style="font-size: 0.72rem; color: var(--ts-text-tertiary); margin-top: 0.25rem;">
                        Disbursed on <?= !empty($p['processed_at']) ? \App\Core\DateHelper::formatDateTime($p['processed_at']) : \App\Core\DateHelper::formatDateTime($p['created_at']) ?>
                      </div>
                    <?php elseif ($isProcessing): ?>
                      <span class="ts-badge ts-badge-cyan" style="font-size: 0.7rem; font-weight: 700;">PROCESSING 🔄</span>
                      <div style="font-size: 0.72rem; color: #38bdf8; margin-top: 0.25rem;">Transfer in transit (24-48h)</div>
                    <?php elseif ($isRejected): ?>
                      <div>
                        <span class="strike-badge" style="font-size: 0.7rem; font-weight: 700; background: rgba(239,68,68,0.15); color: #ef4444; border: 1px solid rgba(239,68,68,0.35); padding: 0.2rem 0.5rem; border-radius: 6px; display: inline-flex; align-items: center; gap: 0.3rem;">
                          REJECTED ❌
                        </span>
                      </div>
                      <div style="margin-top: 0.35rem;">
                        <button type="button" onclick="openPayoutAuditModal(<?= htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8') ?>)" class="ts-btn ts-btn-sm" style="font-size: 0.7rem; padding: 0.2rem 0.55rem; background: rgba(239,68,68,0.12); color: #f87171; border: 1px solid rgba(239,68,68,0.3); border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem; font-weight: 600;">
                          <i data-lucide="help-circle" style="width: 12px; height: 12px;"></i> Why Rejected?
                        </button>
                      </div>
                      <?php if (!empty($p['admin_notes'])): ?>
                        <div style="margin-top: 0.35rem; font-size: 0.74rem; color: #f87171; background: rgba(239,68,68,0.06); padding: 0.35rem 0.55rem; border-radius: 6px; border-left: 2px solid #ef4444; max-width: 250px; line-height: 1.35;" title="<?= htmlspecialchars($p['admin_notes']) ?>">
                          <strong>Reason:</strong> <?= htmlspecialchars(mb_strimwidth($p['admin_notes'], 0, 70, '...')) ?>
                        </div>
                      <?php endif; ?>
                      <div style="margin-top: 0.25rem; font-size: 0.7rem; color: #10b981; font-weight: 600; display: flex; align-items: center; gap: 0.25rem;">
                        <i data-lucide="check-circle" style="width: 11px; height: 11px;"></i> 100% Refunded to Balance
                      </div>
                    <?php else: ?>
                      <span class="ts-badge ts-badge-amber" style="font-size: 0.7rem; font-weight: 700;">PENDING REVIEW ⏳</span>
                      <div style="font-size: 0.72rem; color: var(--ts-text-tertiary); margin-top: 0.25rem;">Queued in finance buffer</div>
                    <?php endif; ?>
                  </td>
                  <td style="font-family: var(--ts-font-mono); font-size: 0.78rem; color: var(--ts-text-secondary);">
                    <?php if ($isRejected): ?>
                      <span style="color: #10b981; font-weight: 600; font-size: 0.75rem;">CREDIT REFUND ✓</span>
                    <?php else: ?>
                      <?= htmlspecialchars($p['transaction_reference'] ?: 'Pending Wire ID') ?>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if (!empty($p['receipt_url'])): ?>
                      <a href="<?= url($p['receipt_url']) ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.6rem; color: #38bdf8; border-color: rgba(56,189,248,0.4);" title="Download Payment Receipt">
                        <i data-lucide="file-check"></i> Receipt 📄
                      </a>
                    <?php else: ?>
                      <span style="font-size: 0.75rem; color: var(--ts-text-tertiary);">Pending Wire</span>
                    <?php endif; ?>
                  </td>
                  <td style="white-space: nowrap;">
                    <button type="button" onclick="openPayoutAuditModal(<?= htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8') ?>)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.55rem; color: var(--ts-text-primary);" title="View Full Settlement Dossier &amp; Logs">
                      <i data-lucide="file-text" style="width: 12px; height: 12px;"></i> View Audit 🔍
                    </button>
                    <div style="font-size: 0.72rem; color: var(--ts-text-tertiary); margin-top: 0.25rem; font-family: var(--ts-font-mono);">
                      <?= \App\Core\DateHelper::formatDateTime($p['created_at']) ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div style="text-align: center; padding: 2rem; background: rgba(15,23,42,0.4); border: 1px dashed rgba(148,163,184,0.15); border-radius: 10px;">
          <i data-lucide="wallet" style="width: 32px; height: 32px; color: #475569; margin-bottom: 0.35rem;"></i>
          <h4 style="color: var(--ts-text-primary); font-size: 0.95rem; margin: 0 0 0.2rem 0;">No withdrawal requests on record</h4>
          <p style="color: var(--ts-text-secondary); font-size: 0.82rem; margin: 0;">Once your publications accumulate readership royalties (min $25.00), you can request immediate cashouts here.</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- 3. PERSONAL DOUBLE-ENTRY FINANCIAL LEDGER -->
    <div class="ts-card" style="margin-bottom: 3rem; padding: 1.75rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
        <div>
          <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
            <span class="ts-badge ts-badge-purple">DOUBLE-ENTRY LEDGER</span>
            <span style="font-size: 0.8rem; color: #64748b;">Immutable Statement of All Account Credits &amp; Debits</span>
          </div>
          <h3 style="font-size: 1.25rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
            📜 Personal Financial Statement &amp; Audit Log
          </h3>
        </div>
      </div>

      <?php if (!empty($recentLedger)): ?>
        <div style="overflow-x: auto;">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Reference #</th>
                <th>Operation &amp; Category</th>
                <th>Amount</th>
                <th>Balance Snapshot</th>
                <th>Reason / Description</th>
                <th>Status</th>
                <th>Timestamp (AM/PM)</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentLedger as $rEntry): ?>
                <tr>
                  <td>
                    <span style="font-family: var(--ts-font-mono); font-size: 0.78rem; font-weight: 700; color: #38bdf8; background: rgba(56,189,248,0.1); padding: 0.2rem 0.45rem; border-radius: 4px;">
                      <?= htmlspecialchars($rEntry['reference_no']) ?>
                    </span>
                  </td>
                  <td>
                    <?php if ($rEntry['entry_type'] === 'credit'): ?>
                      <span class="ts-badge ts-badge-green" style="font-size: 0.68rem; font-weight: 800;">+ CREDIT</span>
                    <?php else: ?>
                      <span class="ts-badge ts-badge-pink" style="font-size: 0.68rem; font-weight: 800; background: rgba(244,63,94,0.15); color: #fb7185;">- DEBIT</span>
                    <?php endif; ?>
                    <span style="font-size: 0.75rem; color: #94a3b8; margin-left: 0.35rem; text-transform: uppercase;">
                      <?= htmlspecialchars(str_replace('_', ' ', $rEntry['category'])) ?>
                    </span>
                  </td>
                  <td>
                    <span style="font-family: var(--ts-font-mono); font-weight: 800; font-size: 0.95rem; color: <?= $rEntry['entry_type'] === 'credit' ? '#10b981' : '#fb7185' ?>;">
                      <?= $rEntry['entry_type'] === 'credit' ? '+' : '-' ?>$<?= number_format((float)$rEntry['amount_usd'], 2) ?>
                    </span>
                  </td>
                  <td>
                    <div style="font-family: var(--ts-font-mono); font-size: 0.75rem; color: #94a3b8;">
                      <span style="color: #64748b;">$<?= number_format((float)$rEntry['balance_before'], 2) ?></span>
                      <span style="color: #38bdf8;"> &rarr; </span>
                      <strong style="color: #f8fafc;">$<?= number_format((float)$rEntry['balance_after'], 2) ?></strong>
                    </div>
                  </td>
                  <td style="color: #cbd5e1; font-size: 0.82rem; max-width: 280px;">
                    <?= htmlspecialchars($rEntry['reason']) ?>
                  </td>
                  <td>
                    <span class="ts-badge ts-badge-green" style="font-size: 0.68rem;">COMPLETED ✓</span>
                  </td>
                  <td style="font-size: 0.76rem; color: #94a3b8; font-family: var(--ts-font-mono); white-space: nowrap;">
                    <?= \App\Core\DateHelper::formatDateTime($rEntry['created_at']) ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div style="text-align: center; padding: 2rem; background: rgba(15,23,42,0.4); border: 1px dashed rgba(148,163,184,0.15); border-radius: 10px;">
          <p style="color: #94a3b8; font-size: 0.85rem; margin: 0;">No ledger statement events recorded yet. Earnings and withdrawals will appear here in real-time.</p>
        </div>
      <?php endif; ?>
    </div>
  </div><!-- /amainpane_payouts -->

  <!-- ========================================================================= -->
  <!-- 5. PANE 3: AUDIENCE & READERSHIP ANALYTICS -->
  <!-- ========================================================================= -->
  <div id="amainpane_analytics" style="display: none;">
    <div class="ts-card" style="margin-bottom: 3rem; padding: 2rem;">
      <div style="margin-bottom: 1.5rem;">
        <span class="ts-badge ts-badge-purple">AUDIENCE INTELLIGENCE</span>
        <h3 style="font-size: 1.35rem; color: #f8fafc; margin: 0.25rem 0 0 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
          📊 Technical Readership &amp; Growth Telemetry
        </h3>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(148,163,184,0.12); border-radius: 10px; padding: 1.25rem;">
          <div style="font-size: 0.78rem; color: #94a3b8; text-transform: uppercase; font-weight: 700;">Avg Read Time</div>
          <div style="font-size: 1.75rem; font-weight: 800; color: #38bdf8; margin-top: 0.25rem;">
            <?= count($contents) > 0 ? round(($estReadTimeMinutes / max(1, count($contents))), 1) : 0 ?> min
          </div>
          <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">Per published publication</div>
        </div>

        <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(148,163,184,0.12); border-radius: 10px; padding: 1.25rem;">
          <div style="font-size: 0.78rem; color: #94a3b8; text-transform: uppercase; font-weight: 700;">Engagement Rate</div>
          <div style="font-size: 1.75rem; font-weight: 800; color: #ec4899; margin-top: 0.25rem;">
            <?= (int)($stats['total_views'] ?? 0) > 0 ? round((((int)($stats['total_reactions'] ?? 0) + (int)($stats['total_comments'] ?? 0)) / max(1, (int)($stats['total_views'] ?? 1))) * 100, 1) : 0 ?>%
          </div>
          <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">Reactions &amp; comments per view</div>
        </div>

        <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(148,163,184,0.12); border-radius: 10px; padding: 1.25rem;">
          <div style="font-size: 0.78rem; color: #94a3b8; text-transform: uppercase; font-weight: 700;">Saved Bookmarks</div>
          <div style="font-size: 1.75rem; font-weight: 800; color: #10b981; margin-top: 0.25rem; font-family: monospace;">
            <?= number_format((int)($stats['total_bookmarks'] ?? 0)) ?>
          </div>
          <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">In reader personal libraries</div>
        </div>

        <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(148,163,184,0.12); border-radius: 10px; padding: 1.25rem;">
          <div style="font-size: 0.78rem; color: #94a3b8; text-transform: uppercase; font-weight: 700;">Reputation XP</div>
          <div style="font-size: 1.75rem; font-weight: 800; color: #f59e0b; margin-top: 0.25rem; font-family: monospace;">
            <?= number_format($author['reputation_score'] ?? 100) ?> XP
          </div>
          <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">Tier Rank: <?= htmlspecialchars($tierName) ?></div>
        </div>
      </div>

      <!-- 7-Day Readership Graph & Top Performing Articles Grid -->
      <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem; align-items: stretch;">
        <!-- 7-Day Line Chart -->
        <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(148,163,184,0.12); border-radius: 12px; padding: 1.5rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <div>
              <h4 style="font-size: 1.05rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif;">📈 7-Day Readership Telemetry</h4>
              <p style="font-size: 0.75rem; color: #64748b; margin: 0.15rem 0 0 0;">Daily unique reader hits on your catalog</p>
            </div>
            <span class="ts-badge ts-badge-cyan" style="font-size: 0.68rem;">Live Events</span>
          </div>
          <div style="height: 180px; position: relative;">
            <canvas id="author7dChart"></canvas>
          </div>
        </div>

        <!-- Top Performing Publications -->
        <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(148,163,184,0.12); border-radius: 12px; padding: 1.5rem;">
          <h4 style="font-size: 1.05rem; color: #f8fafc; margin: 0 0 1rem 0; font-family: 'Outfit', sans-serif;">🏆 Top Performing Publications</h4>
          <?php if (!empty($topContents)): ?>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
              <?php foreach ($topContents as $tc): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.75rem; padding-bottom: 0.6rem; border-bottom: 1px solid rgba(148,163,184,0.08);">
                  <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    <a href="<?= url('/author/analytics/content/' . $tc['id']) ?>" style="font-size: 0.85rem; color: #f8fafc; font-weight: 600; text-decoration: none;">
                      <?= htmlspecialchars($tc['title']) ?>
                    </a>
                    <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.1rem;">
                      <?= number_format($tc['view_count']) ?> reads &bull; <?= $tc['reaction_count'] ?> reactions
                    </div>
                  </div>
                  <a href="<?= url('/author/analytics/content/' . $tc['id']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem; color: #38bdf8; border-color: rgba(56,189,248,0.3); flex-shrink: 0;">
                    Stats 📊
                  </a>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p style="color: #64748b; font-size: 0.82rem; margin: 0;">Publish articles to populate impact rankings.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div><!-- /amainpane_analytics -->

  <!-- ========================================================================= -->
  <!-- 5. PANE 3B: AMBASSADOR & PLATFORM GROWTH REFERRAL HUB -->
  <!-- ========================================================================= -->
  <div id="amainpane_ambassador" style="display: none;">
    <div class="ts-card" style="margin-bottom: 2.5rem; padding: 2rem; background: linear-gradient(135deg, rgba(15,23,42,0.95), rgba(30,41,59,0.7)); border: 1px solid rgba(245,158,11,0.3); border-radius: 14px;">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
          <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
            <span class="ts-badge ts-badge-amber" style="font-weight: 800;">GROWTH AMBASSADOR ENGINE</span>
            <span style="font-size: 0.8rem; color: #94a3b8;">Invite Engineering Authors &amp; Earn 5% Commission</span>
          </div>
          <h3 style="font-size: 1.45rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
            🤝 Creator Referral &amp; Growth Program
          </h3>
          <p style="color: #94a3b8; font-size: 0.88rem; margin: 0.35rem 0 0 0; max-width: 650px;">
            Share your unique creator invite link. When invited technical authors get verified and publish articles, you earn a <strong>5% bonus commission</strong> on all their readership earnings for 6 months, funded by the platform pool.
          </p>
        </div>

        <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(245,158,11,0.3); border-radius: 10px; padding: 1rem 1.25rem; min-width: 260px;">
          <div style="font-size: 0.72rem; color: #94a3b8; text-transform: uppercase; font-weight: 700; margin-bottom: 0.35rem;">Your Ambassador Link</div>
          <div style="display: flex; gap: 0.4rem; align-items: center;">
            <input type="text" id="ambassadorLinkInput" readonly value="<?= url('/author/apply?ref=' . ($ambassadorData['referral_code'] ?? 'creator')) ?>" style="background: #04070e; border: 1px solid rgba(148,163,184,0.2); border-radius: 6px; padding: 0.35rem 0.5rem; color: #38bdf8; font-size: 0.75rem; font-family: monospace; width: 100%;">
            <button type="button" onclick="copyAmbassadorLink()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #f59e0b; border: none; color: #000; font-weight: 800; padding: 0.35rem 0.65rem;">
              Copy
            </button>
          </div>
        </div>
      </div>

      <!-- Ambassador Metrics Grid -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div style="background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.12); border-radius: 10px; padding: 1.25rem;">
          <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Total Referrals</div>
          <div style="font-size: 1.75rem; font-weight: 800; color: #f8fafc; font-family: monospace; margin-top: 0.25rem;">
            <?= $ambassadorData['total_referrals'] ?? 0 ?>
          </div>
          <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.2rem;">Signups through link</div>
        </div>

        <div style="background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.12); border-radius: 10px; padding: 1.25rem;">
          <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Active Creators</div>
          <div style="font-size: 1.75rem; font-weight: 800; color: #38bdf8; font-family: monospace; margin-top: 0.25rem;">
            <?= $ambassadorData['active_authors'] ?? 0 ?>
          </div>
          <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.2rem;">Published verified works</div>
        </div>

        <div style="background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.12); border-radius: 10px; padding: 1.25rem;">
          <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Commission Rate</div>
          <div style="font-size: 1.75rem; font-weight: 800; color: #10b981; font-family: monospace; margin-top: 0.25rem;">
            5.0%
          </div>
          <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.2rem;">On author royalties</div>
        </div>

        <div style="background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.12); border-radius: 10px; padding: 1.25rem;">
          <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Commission Earned</div>
          <div style="font-size: 1.75rem; font-weight: 800; color: #fbbf24; font-family: monospace; margin-top: 0.25rem;">
            $<?= number_format((float)($ambassadorData['total_earned_usd'] ?? 0), 2) ?>
          </div>
          <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.2rem;">Disbursed directly to ledger</div>
        </div>
      </div>

      <!-- Referred Authors Roster -->
      <h4 style="font-size: 1.1rem; color: #f8fafc; margin: 0 0 1rem 0; font-family: 'Outfit', sans-serif;">
        📋 Referred Creators &amp; Authors Roster
      </h4>
      <div style="overflow-x: auto;">
        <table class="ts-table" style="width: 100%; border-collapse: collapse;">
          <thead>
            <tr style="border-bottom: 1px solid rgba(148,163,184,0.15); text-align: left; font-size: 0.78rem; color: #94a3b8;">
              <th style="padding: 0.75rem;">Author</th>
              <th style="padding: 0.75rem;">Status</th>
              <th style="padding: 0.75rem;">Articles Published</th>
              <th style="padding: 0.75rem;">Total Views</th>
              <th style="padding: 0.75rem;">Commission Window</th>
              <th style="padding: 0.75rem;">Commission Generated</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($ambassadorData['referrals'])): ?>
              <?php foreach ($ambassadorData['referrals'] as $ref): ?>
                <tr style="border-bottom: 1px solid rgba(148,163,184,0.08); font-size: 0.85rem;">
                  <td style="padding: 0.75rem;">
                    <strong style="color: #f8fafc;"><?= htmlspecialchars($ref['author_name'] ?: $ref['username']) ?></strong>
                  </td>
                  <td style="padding: 0.75rem;">
                    <span class="ts-badge ts-badge-green" style="font-size: 0.7rem; text-transform: uppercase;">
                      <?= htmlspecialchars($ref['status']) ?>
                    </span>
                  </td>
                  <td style="padding: 0.75rem; color: #cbd5e1; font-family: monospace;">
                    <?= (int)($ref['published_articles'] ?? 0) ?>
                  </td>
                  <td style="padding: 0.75rem; color: #38bdf8; font-family: monospace;">
                    <?= number_format((int)($ref['total_views'] ?? 0)) ?>
                  </td>
                  <td style="padding: 0.75rem; font-size: 0.78rem; color: #94a3b8;">
                    Until <?= date('M d, Y', strtotime($ref['commission_expires_at'])) ?>
                  </td>
                  <td style="padding: 0.75rem; font-weight: 700; color: #10b981; font-family: monospace;">
                    +$<?= number_format((float)($ref['commission_earned_usd'] ?? 0), 2) ?> USD
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" style="text-align: center; padding: 2.5rem; color: #64748b;">
                  Share your link with creators to start tracking your referral roster!
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 5. PANE 3C: CO-AUTHORING & MULTI-AUTHOR ROYALTY COLLABORATIONS -->
  <!-- ========================================================================= -->
  <div id="amainpane_collaborations" style="display: none;">
    <div class="ts-card" style="margin-bottom: 2.5rem; padding: 2rem; background: linear-gradient(135deg, rgba(15,23,42,0.95), rgba(30,41,59,0.7)); border: 1px solid rgba(56,189,248,0.3); border-radius: 14px;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
          <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
            <span class="ts-badge ts-badge-cyan" style="font-weight: 800;">MULTI-AUTHOR ROYALTIES</span>
            <span style="font-size: 0.8rem; color: #94a3b8;">Collaborative Publishing &amp; Automated Revenue Splitting</span>
          </div>
          <h3 style="font-size: 1.45rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
            👥 Co-Authoring &amp; Collaborations
          </h3>
        </div>
      </div>

      <div style="overflow-x: auto;">
        <table class="ts-table" style="width: 100%; border-collapse: collapse;">
          <thead>
            <tr style="border-bottom: 1px solid rgba(148,163,184,0.15); text-align: left; font-size: 0.78rem; color: #94a3b8;">
              <th style="padding: 0.75rem;">Publication</th>
              <th style="padding: 0.75rem;">Lead Author</th>
              <th style="padding: 0.75rem;">Role</th>
              <th style="padding: 0.75rem;">Revenue Split %</th>
              <th style="padding: 0.75rem;">Invitation Status</th>
              <th style="padding: 0.75rem;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($collaborations)): ?>
              <?php foreach ($collaborations as $collab): ?>
                <tr style="border-bottom: 1px solid rgba(148,163,184,0.08); font-size: 0.85rem;">
                  <td style="padding: 0.75rem;">
                    <a href="<?= url('/articles/' . ($collab['slug'] ?? '')) ?>" target="_blank" style="color: #38bdf8; font-weight: 600; text-decoration: none;">
                      <?= htmlspecialchars($collab['title']) ?> ↗
                    </a>
                  </td>
                  <td style="padding: 0.75rem; color: #f8fafc;">
                    <?= htmlspecialchars($collab['lead_name'] ?? 'Lead Author') ?>
                  </td>
                  <td style="padding: 0.75rem;">
                    <span class="ts-badge ts-badge-indigo" style="font-size: 0.7rem; text-transform: uppercase;">
                      <?= htmlspecialchars(str_replace('_', ' ', $collab['role'])) ?>
                    </span>
                  </td>
                  <td style="padding: 0.75rem; font-weight: 800; color: #10b981; font-family: monospace;">
                    <?= (float)$collab['revenue_share_percentage'] ?>% Split
                  </td>
                  <td style="padding: 0.75rem;">
                    <span class="ts-badge <?= ($collab['invitation_status'] === 'accepted') ? 'ts-badge-green' : (($collab['invitation_status'] === 'pending') ? 'ts-badge-amber' : 'ts-badge-red') ?>" style="font-size: 0.7rem; text-transform: uppercase;">
                      <?= htmlspecialchars($collab['invitation_status']) ?>
                    </span>
                  </td>
                  <td style="padding: 0.75rem;">
                    <?php if ($collab['invitation_status'] === 'pending'): ?>
                      <form action="<?= url('/author/collaborations/respond') ?>" method="POST" style="display: inline-flex; gap: 0.35rem;">
                        <input type="hidden" name="invite_id" value="<?= $collab['id'] ?>">
                        <button type="submit" name="action" value="accepted" class="ts-btn ts-btn-primary ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem; background: #10b981; border-color: #10b981;">Accept</button>
                        <button type="submit" name="action" value="declined" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem; color: #ef4444; border-color: rgba(239,68,68,0.3);">Decline</button>
                      </form>
                    <?php else: ?>
                      <span style="color: #64748b; font-size: 0.78rem;">Active Co-Author</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" style="text-align: center; padding: 2.5rem; color: #64748b;">
                  No co-authoring collaborations recorded yet. When other authors invite you to collaborate, they will appear here.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 5. PANE 4: CREATOR COMMUNITY NETWORK & FOLLOWERS ROSTER -->
  <!-- ========================================================================= -->
  <div id="amainpane_community" style="display: none;">
    <div class="ts-card" style="margin-bottom: 3rem; padding: 2rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1rem;">
        <div>
          <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
            <span class="ts-badge ts-badge-purple">COMMUNITY SUBSCRIBERS</span>
            <span style="font-size: 0.8rem; color: #64748b;">Developers &amp; technical readers subscribed to your feed</span>
          </div>
          <h3 style="font-size: 1.35rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
            👥 Creator Followers &amp; Network Roster
          </h3>
        </div>

        <div style="display: flex; gap: 0.75rem; align-items: center;">
          <span class="ts-badge ts-badge-cyan" style="font-size: 0.82rem; padding: 0.35rem 0.75rem;">
            <?= count($followers ?? []) ?> Verified Followers
          </span>
          <span class="ts-badge ts-badge-pink" style="font-size: 0.82rem; padding: 0.35rem 0.75rem;">
            <?= count($following ?? []) ?> Authors Followed
          </span>
        </div>
      </div>

      <!-- Followers Grid -->
      <h4 style="font-size: 1.05rem; color: #f8fafc; margin: 0 0 1rem 0; font-family: 'Outfit', sans-serif;">
        📢 Your Active Followers (<?= count($followers ?? []) ?>)
      </h4>

      <?php if (!empty($followers)): ?>
        <div class="ts-grid-3" style="margin-bottom: 2.5rem;">
          <?php foreach ($followers as $flw): ?>
            <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(148,163,184,0.12); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; gap: 0.75rem;">
              <div style="display: flex; gap: 0.75rem; align-items: center;">
                <?php if (!empty($flw['avatar_url'])): ?>
                  <img src="<?= htmlspecialchars(image_url($flw['avatar_url'])) ?>" alt="<?= htmlspecialchars($flw['display_name'] ?? $flw['username']) ?>" style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 1px solid #38bdf8;">
                <?php else: ?>
                  <div style="width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #38bdf8); display: flex; align-items: center; justify-content: center; font-weight: 800; color: #fff; font-size: 1.1rem;">
                    <?= strtoupper(substr($flw['display_name'] ?? $flw['username'] ?? 'U', 0, 1)) ?>
                  </div>
                <?php endif; ?>

                <div style="overflow: hidden; flex: 1;">
                  <div style="font-weight: 700; color: #f8fafc; font-size: 0.95rem;">
                    <?php if (!empty($flw['author_slug'])): ?>
                      <a href="<?= url('/author/profile/' . $flw['author_slug']) ?>" target="_blank" style="color: #f8fafc; text-decoration: none;">
                        <?= htmlspecialchars($flw['display_name'] ?? $flw['username']) ?> ↗
                      </a>
                    <?php else: ?>
                      <?= htmlspecialchars($flw['display_name'] ?? $flw['username']) ?>
                    <?php endif; ?>
                  </div>
                  <div style="font-size: 0.75rem; color: #64748b; font-family: var(--ts-font-mono);">
                    @<?= htmlspecialchars($flw['username']) ?>
                  </div>
                </div>
              </div>

              <?php if (!empty($flw['headline'])): ?>
                <p style="font-size: 0.8rem; color: #94a3b8; line-height: 1.4; margin: 0; flex: 1;">
                  <?= htmlspecialchars($flw['headline']) ?>
                </p>
              <?php endif; ?>

              <div style="font-size: 0.72rem; color: #64748b; padding-top: 0.4rem; border-top: 1px solid rgba(148,163,184,0.08);">
                Joined feed <?= date('M j, Y', strtotime($flw['followed_at'])) ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div style="background: rgba(15,23,42,0.5); border: 1px dashed rgba(148,163,184,0.15); border-radius: 12px; padding: 2.5rem; text-align: center; color: #64748b; margin-bottom: 2.5rem;">
          <i data-lucide="users" style="width: 36px; height: 36px; color: #38bdf8; margin-bottom: 0.5rem;"></i>
          <p style="margin: 0;">Share your technical articles across social channels to build your follower base towards the 1,000 follower monetization requirement!</p>
        </div>
      <?php endif; ?>

      <!-- Authors Followed by You -->
      <h4 style="font-size: 1.05rem; color: #f8fafc; margin: 0 0 1rem 0; font-family: 'Outfit', sans-serif;">
        🔗 Authors You Follow (<?= count($following ?? []) ?>)
      </h4>

      <?php if (!empty($following)): ?>
        <div class="ts-grid-3">
          <?php foreach ($following as $fw): ?>
            <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(148,163,184,0.12); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; gap: 0.75rem;">
              <div style="display: flex; gap: 0.75rem; align-items: center;">
                <?php if (!empty($fw['avatar_url'])): ?>
                  <img src="<?= htmlspecialchars(image_url($fw['avatar_url'])) ?>" alt="<?= htmlspecialchars($fw['display_name']) ?>" style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 1px solid #c084fc;">
                <?php else: ?>
                  <div style="width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, #a855f7, #ec4899); display: flex; align-items: center; justify-content: center; font-weight: 800; color: #fff; font-size: 1.1rem;">
                    <?= strtoupper(substr($fw['display_name'] ?? 'A', 0, 1)) ?>
                  </div>
                <?php endif; ?>

                <div style="overflow: hidden; flex: 1;">
                  <div style="font-weight: 700; color: #f8fafc; font-size: 0.95rem;">
                    <a href="<?= url('/author/profile/' . $fw['author_slug']) ?>" target="_blank" style="color: #f8fafc; text-decoration: none;">
                      <?= htmlspecialchars($fw['display_name']) ?> ↗
                    </a>
                  </div>
                  <div style="font-size: 0.72rem; color: #c084fc;">
                    ⭐ <?= number_format($fw['reputation_score'] ?? 100) ?> XP &bull; <?= number_format((int)($fw['target_follower_count'] ?? 0)) ?> followers
                  </div>
                </div>
              </div>

              <?php if (!empty($fw['headline'])): ?>
                <p style="font-size: 0.8rem; color: #94a3b8; line-height: 1.4; margin: 0; flex: 1;">
                  <?= htmlspecialchars($fw['headline']) ?>
                </p>
              <?php endif; ?>

              <div style="display: flex; justify-content: space-between; align-items: center; margin-top: auto; padding-top: 0.4rem; border-top: 1px solid rgba(148,163,184,0.08);">
                <a href="<?= url('/author/profile/' . $fw['author_slug']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.7rem; padding: 0.2rem 0.6rem; width: 100%; text-align: center;">
                  View Profile ↗
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div style="background: rgba(15,23,42,0.5); border: 1px dashed rgba(148,163,184,0.15); border-radius: 12px; padding: 2.5rem; text-align: center; color: #64748b;">
          <p style="margin: 0;">You haven't followed any fellow authors yet. Visit public author profiles to follow creators!</p>
        </div>
      <?php endif; ?>
    </div>
  </div><!-- /amainpane_community -->

  <!-- ========================================================================= -->
  <!-- 4. PANE: AI PROMPTS STUDIO & RUNNER -->
  <!-- ========================================================================= -->
  <div id="amainpane_prompts" style="display: none;">
    <div class="ts-card" style="margin-bottom: 3rem; padding: 2rem;">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.75rem;">
        <div>
          <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
            <span class="ts-badge ts-badge-purple">FRONTIER AI STUDIO</span>
            <span style="font-size: 0.8rem; color: #64748b;">Prompt Engineering &amp; Variable Injection Hub</span>
          </div>
          <h3 style="font-size: 1.45rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
            🎨 Author AI Prompts Studio
          </h3>
          <p style="color: #94a3b8; font-size: 0.88rem; margin: 0.35rem 0 0 0; max-width: 650px;">
            Publish parameter-driven prompt blueprints and diffusion recipes. Prompts earn discovery XP and reader engagement metrics across the platform.
          </p>
        </div>

        <div style="display: flex; gap: 0.6rem; align-items: center; flex-wrap: wrap;">
          <a href="<?= url('/author/editor?type=prompt') ?>" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #a855f7, #6366f1); font-weight: 700; border: none; padding: 0.55rem 1.25rem;">
            <i data-lucide="sparkles"></i> New Prompt Blueprint ⚡
          </a>
        </div>
      </div>

      <!-- Quick Metrics Counters -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.75rem;">
        <div class="author-subcard" style="background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.12); border-radius: 10px; padding: 1.25rem;">
          <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Total Prompts</div>
          <div style="font-size: 1.75rem; font-weight: 800; color: #a855f7; font-family: 'Outfit', sans-serif; margin-top: 0.25rem;">
            <?= count($authorPrompts ?? []) ?>
          </div>
          <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.2rem;">Published in catalog</div>
        </div>

        <div class="author-subcard" style="background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.12); border-radius: 10px; padding: 1.25rem;">
          <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Text Blueprints</div>
          <div style="font-size: 1.75rem; font-weight: 800; color: #38bdf8; font-family: 'Outfit', sans-serif; margin-top: 0.25rem;">
            <?= count(array_filter($authorPrompts ?? [], fn($p) => ($p['prompt_type'] ?? 'text') !== 'image')) ?>
          </div>
          <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.2rem;">Code, reasoning &amp; system prompts</div>
        </div>

        <div class="author-subcard" style="background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.12); border-radius: 10px; padding: 1.25rem;">
          <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Image Gen Prompts</div>
          <div style="font-size: 1.75rem; font-weight: 800; color: #ec4899; font-family: 'Outfit', sans-serif; margin-top: 0.25rem;">
            <?= count(array_filter($authorPrompts ?? [], fn($p) => ($p['prompt_type'] ?? '') === 'image')) ?>
          </div>
          <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.2rem;">Diffusion &amp; Midjourney prompts</div>
        </div>

        <div class="author-subcard" style="background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.12); border-radius: 10px; padding: 1.25rem;">
          <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Total Copies &amp; Runs</div>
          <div style="font-size: 1.75rem; font-weight: 800; color: #10b981; font-family: monospace; margin-top: 0.25rem;">
            <?= number_format(array_sum(array_column($authorPrompts ?? [], 'copy_count'))) ?>
          </div>
          <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.2rem;">Reader usage executions</div>
        </div>
      </div>

      <!-- Filters & Search -->
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
        <div style="display: flex; gap: 0.4rem;">
          <button type="button" onclick="filterAuthorPrompts('all')" id="apfilter_all" class="ts-btn ts-btn-primary ts-btn-sm" style="font-size: 0.78rem; padding: 0.3rem 0.8rem;">All (<?= count($authorPrompts ?? []) ?>)</button>
          <button type="button" onclick="filterAuthorPrompts('text')" id="apfilter_text" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.78rem; padding: 0.3rem 0.8rem;">📝 Text Only</button>
          <button type="button" onclick="filterAuthorPrompts('image')" id="apfilter_image" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.78rem; padding: 0.3rem 0.8rem;">🎨 Image Gen Only</button>
        </div>
        <span style="font-size: 0.78rem; color: #64748b;">Text prompts are strictly clean code/terminal blueprints without card images.</span>
      </div>

      <!-- Prompts Grid -->
      <?php if (!empty($authorPrompts)): ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.25rem;">
          <?php foreach ($authorPrompts as $ap): ?>
            <?php 
              $isImgPrompt = ($ap['prompt_type'] ?? '') === 'image';
              $vars = json_decode($ap['template_variables'] ?? '[]', true) ?: [];
              $models = json_decode($ap['target_models'] ?? '[]', true) ?: [];
            ?>
            <div class="author-prompt-card author-subcard" data-prompt-type="<?= $isImgPrompt ? 'image' : 'text' ?>" style="background: rgba(15,23,42,0.8); border: 1px solid rgba(148,163,184,0.14); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; gap: 0.85rem; position: relative;">
              <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem;">
                <div>
                  <span class="ts-badge <?= $isImgPrompt ? 'ts-badge-purple' : 'ts-badge-cyan' ?>" style="font-size: 0.68rem; text-transform: uppercase;">
                    <?= $isImgPrompt ? '🎨 Image Gen' : '📝 Text Blueprint' ?>
                  </span>
                  <?php if (!empty($ap['category_name'])): ?>
                    <span style="font-size: 0.72rem; color: #64748b; margin-left: 0.35rem;"><?= htmlspecialchars($ap['category_name']) ?></span>
                  <?php endif; ?>
                </div>
                <span style="font-size: 0.72rem; font-family: monospace; color: #10b981; font-weight: 700;">
                  <?= (int)($ap['copy_count'] ?? 0) ?> copies
                </span>
              </div>

              <h4 style="margin: 0; font-size: 1.05rem; font-weight: 700; color: #f8fafc; font-family: 'Outfit', sans-serif;">
                <a href="<?= url('/prompts/' . $ap['slug']) ?>" target="_blank" style="color: inherit; text-decoration: none;">
                  <?= htmlspecialchars($ap['title']) ?> ↗
                </a>
              </h4>

              <!-- Template Preview Snippet (Pure Text Blueprint) -->
              <div style="background: #04070e; border: 1px solid rgba(148,163,184,0.12); border-radius: 6px; padding: 0.75rem; font-family: var(--ts-font-mono); font-size: 0.78rem; color: #cbd5e1; max-height: 85px; overflow: hidden; position: relative; line-height: 1.45;">
                <?= htmlspecialchars(mb_strimwidth($ap['prompt_template'] ?? '', 0, 160, '...')) ?>
                <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 25px; background: linear-gradient(transparent, #04070e);"></div>
              </div>

              <!-- Variables & Models -->
              <div style="display: flex; flex-wrap: wrap; gap: 0.35rem;">
                <?php foreach (array_slice($models, 0, 3) as $m): ?>
                  <span style="font-size: 0.68rem; padding: 0.15rem 0.45rem; border-radius: 4px; background: rgba(99,102,241,0.15); color: #818cf8; border: 1px solid rgba(99,102,241,0.3);"><?= htmlspecialchars($m) ?></span>
                <?php endforeach; ?>
                <?php foreach (array_slice($vars, 0, 2) as $v): ?>
                  <span style="font-size: 0.68rem; padding: 0.15rem 0.45rem; border-radius: 4px; background: rgba(56,189,248,0.1); color: #38bdf8; font-family: monospace;">{<?= htmlspecialchars($v['name'] ?? $v) ?>}</span>
                <?php endforeach; ?>
              </div>

              <!-- Action Buttons -->
              <div style="display: flex; justify-content: space-between; align-items: center; margin-top: auto; padding-top: 0.6rem; border-top: 1px solid rgba(148,163,184,0.08); gap: 0.5rem;">
                <button type="button" onclick="copyAuthorPromptTemplate(<?= htmlspecialchars(json_encode($ap['prompt_template'] ?? '')) ?>)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.65rem;">
                  <i data-lucide="copy"></i> Copy Prompt
                </button>
                <div style="display: flex; gap: 0.35rem;">
                  <a href="<?= url('/author/editor/' . $ap['id']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.65rem; color: #38bdf8; border-color: rgba(56,189,248,0.3);">
                    Edit ⚙️
                  </a>
                  <a href="<?= url('/prompts/' . $ap['slug']) ?>" target="_blank" class="ts-btn ts-btn-primary ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.65rem;">
                    View ↗
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div style="background: rgba(15,23,42,0.5); border: 1px dashed rgba(148,163,184,0.15); border-radius: 12px; padding: 3rem 2rem; text-align: center; color: #64748b;">
          <div style="font-size: 2.25rem; margin-bottom: 0.5rem;">🎨</div>
          <h4 style="color: #f8fafc; font-size: 1.15rem; margin: 0 0 0.5rem 0;">No AI Prompts Published Yet</h4>
          <p style="margin: 0 0 1.25rem 0; font-size: 0.88rem; max-width: 500px; margin-left: auto; margin-right: auto;">
            Create high-accuracy coding prompts, architectural blueprints, or diffusion prompts. Every prompt is indexed for technical search and AI tools discovery.
          </p>
          <a href="<?= url('/author/editor?type=prompt') ?>" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #a855f7, #6366f1); font-weight: 700; padding: 0.55rem 1.4rem;">
            + Create Your First AI Prompt
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div><!-- /amainpane_prompts -->

  <!-- ========================================================================= -->
  <!-- 5. PANE: CREATOR CRYPTO TIP JAR & PEER PATRONAGE -->
  <!-- ========================================================================= -->
  <div id="amainpane_tipjar" style="display: none;">
    <div class="ts-card" style="margin-bottom: 3rem; padding: 2rem; background: linear-gradient(135deg, rgba(15,23,42,0.95), rgba(24,20,38,0.7)); border: 1px solid rgba(245,158,11,0.3); border-radius: 14px;">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.75rem;">
        <div>
          <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
            <span class="ts-badge ts-badge-amber" style="font-weight: 800;">DIRECT CREATOR PATRONAGE</span>
            <span style="font-size: 0.8rem; color: #94a3b8;">95% Direct Creator Payout &bull; 5% Platform Treasury &amp; Infrastructure Fee</span>
          </div>
          <h3 style="font-size: 1.45rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
            ⚡ Creator Crypto Tip Jar &amp; Peer Patronage
          </h3>
          <p style="color: #94a3b8; font-size: 0.88rem; margin: 0.35rem 0 0 0; max-width: 720px; line-height: 1.6;">
            Let your technical readers support your open-source research and engineering tutorials directly. Readers tip in Bitcoin, Ethereum, USDT, or Solana. TechScout protocol reserves a 5% infrastructure contribution to cover gas subsidies and network validator relays, with 95% of gross tips routed directly to your personal crypto wallet.
          </p>
        </div>
        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.5rem;">
          <span class="ts-badge ts-badge-green" style="font-size: 0.8rem; padding: 0.4rem 0.8rem; font-weight: 800;">
            95% CREATOR SHARE (5% PLATFORM FEE) ✓
          </span>
          <button type="button" onclick="openCryptoWalletModal()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #f59e0b, #ec4899); border: none; font-weight: 700; color: #000; font-size: 0.8rem; padding: 0.4rem 0.85rem;">
            ⚡ Quick-Configure All Wallets
          </button>
        </div>
      </div>

      <!-- Real-Time Protocol Fee Calculator Simulator -->
      <div style="background: rgba(15,23,42,0.7); border: 1px solid rgba(245,158,11,0.2); border-radius: 10px; padding: 1rem 1.25rem; margin-bottom: 1.75rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
          <span style="font-size: 1.5rem;">🧮</span>
          <div>
            <div style="font-size: 0.85rem; font-weight: 700; color: #f8fafc;">Live Distribution Simulator:</div>
            <div style="font-size: 0.78rem; color: #94a3b8;">Simulate gross tip revenue payout breakdown</div>
          </div>
        </div>
        <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
          <div style="display: flex; align-items: center; gap: 0.4rem;">
            <span style="font-size: 0.85rem; color: #94a3b8;">Reader Tips:</span>
            <input type="number" id="tipSimInput" value="50" min="1" step="5" oninput="updateTipSimulator(this.value)" style="width: 80px; background: #04070e; border: 1px solid rgba(245,158,11,0.4); border-radius: 6px; padding: 0.35rem 0.5rem; color: #f59e0b; font-weight: 800; font-family: monospace; text-align: center;">
            <span style="font-size: 0.85rem; color: #94a3b8;">USD</span>
          </div>
          <div style="display: flex; gap: 0.75rem; font-size: 0.85rem;">
            <span class="ts-badge ts-badge-green" id="tipSimNet" style="font-size: 0.82rem; font-weight: 700; padding: 0.35rem 0.65rem;">
              You Receive (95%): $47.50
            </span>
            <span class="ts-badge ts-badge-amber" id="tipSimFee" style="font-size: 0.82rem; font-weight: 700; padding: 0.35rem 0.65rem;">
              Platform Treasury (5%): $2.50
            </span>
          </div>
        </div>
      </div>

      <!-- Configured Wallets Grid -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        
        <!-- BTC -->
        <div class="author-subcard" style="background: rgba(15,23,42,0.6); border: 1px solid rgba(245,158,11,0.25); border-radius: 10px; padding: 1.25rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <strong style="color: #f59e0b; font-size: 0.95rem;">₿ Bitcoin (BTC)</strong>
            <span class="ts-badge <?= !empty($author['tip_wallet_btc']) ? 'ts-badge-green' : 'ts-badge-amber' ?>" style="font-size: 0.65rem;">
              <?= !empty($author['tip_wallet_btc']) ? 'Active Payout ✓' : 'Native SegWit / Taproot' ?>
            </span>
          </div>
          <div style="position: relative; margin-bottom: 0.65rem;">
            <div style="font-family: monospace; font-size: 0.78rem; color: <?= !empty($author['tip_wallet_btc']) ? '#34d399' : '#94a3b8' ?>; word-break: break-all; background: #04070e; padding: 0.55rem 2rem 0.55rem 0.65rem; border-radius: 6px; border: 1px solid rgba(245,158,11,0.2); min-height: 48px; display: flex; align-items: center;">
              <?= htmlspecialchars($author['tip_wallet_btc'] ?? 'bc1q... (Not configured)') ?>
            </div>
            <?php if (!empty($author['tip_wallet_btc'])): ?>
              <button type="button" onclick="navigator.clipboard.writeText('<?= htmlspecialchars(addslashes($author['tip_wallet_btc'])) ?>'); showToast('Bitcoin address copied!', 'success');" style="position: absolute; right: 6px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #f59e0b; cursor: pointer; padding: 4px;" title="Copy Bitcoin Address">
                📋
              </button>
            <?php endif; ?>
          </div>
          <button type="button" onclick="openCryptoWalletModal('btc')" class="ts-btn <?= !empty($author['tip_wallet_btc']) ? 'ts-btn-primary' : 'ts-btn-outline' ?> ts-btn-sm" style="width: 100%; font-size: 0.75rem; padding: 0.35rem; <?= !empty($author['tip_wallet_btc']) ? 'background: #f59e0b; color: #000; font-weight: 700;' : 'color: #f59e0b; border-color: rgba(245,158,11,0.4); font-weight: 700;' ?>">
            <?= !empty($author['tip_wallet_btc']) ? 'Edit BTC Wallet ⚙️' : 'Configure BTC Wallet ⚙️' ?>
          </button>
        </div>

        <!-- ETH -->
        <div class="author-subcard" style="background: rgba(15,23,42,0.6); border: 1px solid rgba(99,102,241,0.25); border-radius: 10px; padding: 1.25rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <strong style="color: #818cf8; font-size: 0.95rem;">Ξ Ethereum / EVM</strong>
            <span class="ts-badge <?= !empty($author['tip_wallet_eth']) ? 'ts-badge-green' : 'ts-badge-indigo' ?>" style="font-size: 0.65rem;">
              <?= !empty($author['tip_wallet_eth']) ? 'Active Payout ✓' : 'ERC-20 / Arbitrum' ?>
            </span>
          </div>
          <div style="position: relative; margin-bottom: 0.65rem;">
            <div style="font-family: monospace; font-size: 0.78rem; color: <?= !empty($author['tip_wallet_eth']) ? '#34d399' : '#94a3b8' ?>; word-break: break-all; background: #04070e; padding: 0.55rem 2rem 0.55rem 0.65rem; border-radius: 6px; border: 1px solid rgba(99,102,241,0.2); min-height: 48px; display: flex; align-items: center;">
              <?= htmlspecialchars($author['tip_wallet_eth'] ?? '0x71C... (Not configured)') ?>
            </div>
            <?php if (!empty($author['tip_wallet_eth'])): ?>
              <button type="button" onclick="navigator.clipboard.writeText('<?= htmlspecialchars(addslashes($author['tip_wallet_eth'])) ?>'); showToast('Ethereum address copied!', 'success');" style="position: absolute; right: 6px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #818cf8; cursor: pointer; padding: 4px;" title="Copy Ethereum Address">
                📋
              </button>
            <?php endif; ?>
          </div>
          <button type="button" onclick="openCryptoWalletModal('eth')" class="ts-btn <?= !empty($author['tip_wallet_eth']) ? 'ts-btn-primary' : 'ts-btn-outline' ?> ts-btn-sm" style="width: 100%; font-size: 0.75rem; padding: 0.35rem; <?= !empty($author['tip_wallet_eth']) ? 'background: #6366f1; color: #fff; font-weight: 700;' : 'color: #818cf8; border-color: rgba(99,102,241,0.4); font-weight: 700;' ?>">
            <?= !empty($author['tip_wallet_eth']) ? 'Edit ETH / EVM Wallet ⚙️' : 'Configure ETH / EVM Wallet ⚙️' ?>
          </button>
        </div>

        <!-- USDT -->
        <div class="author-subcard" style="background: rgba(15,23,42,0.6); border: 1px solid rgba(16,185,129,0.25); border-radius: 10px; padding: 1.25rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <strong style="color: #34d399; font-size: 0.95rem;">₮ Tether (USDT)</strong>
            <span class="ts-badge <?= !empty($author['tip_wallet_usdt']) ? 'ts-badge-green' : 'ts-badge-green' ?>" style="font-size: 0.65rem;">
              <?= !empty($author['tip_wallet_usdt']) ? 'Active Payout ✓' : 'TRC-20 / ERC-20' ?>
            </span>
          </div>
          <div style="position: relative; margin-bottom: 0.65rem;">
            <div style="font-family: monospace; font-size: 0.78rem; color: <?= !empty($author['tip_wallet_usdt']) ? '#34d399' : '#94a3b8' ?>; word-break: break-all; background: #04070e; padding: 0.55rem 2rem 0.55rem 0.65rem; border-radius: 6px; border: 1px solid rgba(16,185,129,0.2); min-height: 48px; display: flex; align-items: center;">
              <?= htmlspecialchars($author['tip_wallet_usdt'] ?? 'TXa9... (Not configured)') ?>
            </div>
            <?php if (!empty($author['tip_wallet_usdt'])): ?>
              <button type="button" onclick="navigator.clipboard.writeText('<?= htmlspecialchars(addslashes($author['tip_wallet_usdt'])) ?>'); showToast('USDT address copied!', 'success');" style="position: absolute; right: 6px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #34d399; cursor: pointer; padding: 4px;" title="Copy USDT Address">
                📋
              </button>
            <?php endif; ?>
          </div>
          <button type="button" onclick="openCryptoWalletModal('usdt')" class="ts-btn <?= !empty($author['tip_wallet_usdt']) ? 'ts-btn-primary' : 'ts-btn-outline' ?> ts-btn-sm" style="width: 100%; font-size: 0.75rem; padding: 0.35rem; <?= !empty($author['tip_wallet_usdt']) ? 'background: #10b981; color: #000; font-weight: 700;' : 'color: #34d399; border-color: rgba(16,185,129,0.4); font-weight: 700;' ?>">
            <?= !empty($author['tip_wallet_usdt']) ? 'Edit USDT Address ⚙️' : 'Configure USDT Address ⚙️' ?>
          </button>
        </div>

        <!-- SOL -->
        <div class="author-subcard" style="background: rgba(15,23,42,0.6); border: 1px solid rgba(168,85,247,0.25); border-radius: 10px; padding: 1.25rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <strong style="color: #c084fc; font-size: 0.95rem;">◎ Solana (SOL)</strong>
            <span class="ts-badge <?= !empty($author['tip_wallet_sol']) ? 'ts-badge-green' : 'ts-badge-purple' ?>" style="font-size: 0.65rem;">
              <?= !empty($author['tip_wallet_sol']) ? 'Active Payout ✓' : 'SPL Network' ?>
            </span>
          </div>
          <div style="position: relative; margin-bottom: 0.65rem;">
            <div style="font-family: monospace; font-size: 0.78rem; color: <?= !empty($author['tip_wallet_sol']) ? '#34d399' : '#94a3b8' ?>; word-break: break-all; background: #04070e; padding: 0.55rem 2rem 0.55rem 0.65rem; border-radius: 6px; border: 1px solid rgba(168,85,247,0.2); min-height: 48px; display: flex; align-items: center;">
              <?= htmlspecialchars($author['tip_wallet_sol'] ?? 'So11... (Not configured)') ?>
            </div>
            <?php if (!empty($author['tip_wallet_sol'])): ?>
              <button type="button" onclick="navigator.clipboard.writeText('<?= htmlspecialchars(addslashes($author['tip_wallet_sol'])) ?>'); showToast('Solana address copied!', 'success');" style="position: absolute; right: 6px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #c084fc; cursor: pointer; padding: 4px;" title="Copy Solana Address">
                📋
              </button>
            <?php endif; ?>
          </div>
          <button type="button" onclick="openCryptoWalletModal('sol')" class="ts-btn <?= !empty($author['tip_wallet_sol']) ? 'ts-btn-primary' : 'ts-btn-outline' ?> ts-btn-sm" style="width: 100%; font-size: 0.75rem; padding: 0.35rem; <?= !empty($author['tip_wallet_sol']) ? 'background: #a855f7; color: #fff; font-weight: 700;' : 'color: #c084fc; border-color: rgba(168,85,247,0.4); font-weight: 700;' ?>">
            <?= !empty($author['tip_wallet_sol']) ? 'Edit SOL Address ⚙️' : 'Configure SOL Address ⚙️' ?>
          </button>
        </div>
      </div>

      <!-- Embeddable Reader Badge Snippet -->
      <div style="background: rgba(15,23,42,0.7); border: 1px solid rgba(148,163,184,0.15); border-radius: 10px; padding: 1.25rem; margin-bottom: 2rem;">
        <h4 style="color: #f8fafc; font-size: 1rem; margin: 0 0 0.5rem 0; font-family: 'Outfit', sans-serif;">
          📢 Embed Reader Tip Badge in Your Markdown Articles &amp; GitHub
        </h4>
        <p style="color: #94a3b8; font-size: 0.82rem; margin: 0 0 0.75rem 0;">
          Copy and paste this markdown badge into your publications, tutorials, or open-source GitHub READMEs to invite reader patronage:
        </p>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
          <input type="text" id="tipMeBadgeSnippet" readonly value="[![Support with Crypto](https://img.shields.io/badge/TechScout-Tip_Author-6366f1?logo=bitcoin)](<?= url('/author/' . ($author['slug'] ?? '')) ?>#tip)" class="admin-modal-input" style="font-family: monospace; font-size: 0.8rem; flex: 1;">
          <button type="button" onclick="copySnippet('tipMeBadgeSnippet')" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #f59e0b; border: none; color: #000; font-weight: 700; white-space: nowrap;">
            Copy Markdown 📋
          </button>
        </div>
      </div>

      <!-- Live Patronage & Tips Activity Ledger -->
      <div style="background: rgba(15,23,42,0.7); border: 1px solid rgba(148,163,184,0.15); border-radius: 10px; padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
          <div>
            <h4 style="color: #f8fafc; font-size: 1.1rem; margin: 0 0 0.25rem 0; font-family: 'Outfit', sans-serif; display: flex; align-items: center; gap: 0.5rem;">
              <i data-lucide="history" style="width: 18px; height: 18px; color: #f59e0b;"></i>
              Recent Patronage &amp; Crypto Tips Ledger
            </h4>
            <span style="font-size: 0.78rem; color: #94a3b8;">Transparent on-chain tipping records: 95% net creator earnings &amp; 5% protocol treasury</span>
          </div>
          <span class="ts-badge" style="background: rgba(245,158,11,0.15); color: #f59e0b; font-weight: 700; font-family: monospace;">
            <?= count($authorTips ?? []) ?> Records Logged
          </span>
        </div>

        <?php if (!empty($authorTips)): ?>
          <div class="admin-table-wrap">
            <table class="admin-table" style="font-size: 0.82rem;">
              <thead>
                <tr>
                  <th>Ref / Date</th>
                  <th>Tipper / Donor</th>
                  <th>Method &amp; TXID</th>
                  <th style="text-align: right;">Gross Tip</th>
                  <th style="text-align: right; color: #f59e0b;">Fee (5%)</th>
                  <th style="text-align: right; color: #10b981;">Creator Net (95%)</th>
                  <th style="text-align: center;">Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($authorTips as $tip): ?>
                  <?php
                    $gross = (float)($tip['amount_usd'] ?? $tip['gross_amount'] ?? 0);
                    $fee = (float)($tip['platform_fee_usd'] ?? $tip['platform_fee'] ?? ($gross * 0.05));
                    $net = (float)($tip['net_amount_usd'] ?? $tip['net_author_amount'] ?? ($gross * 0.95));
                    $status = strtolower($tip['status'] ?? 'completed');
                    $method = strtoupper(str_replace('crypto_', '', (string)($tip['payment_method'] ?? 'USDT')));
                  ?>
                  <tr>
                    <td>
                      <strong style="color: #f8fafc; font-family: monospace;"><?= htmlspecialchars($tip['reference_no'] ?? ('TIP-' . $tip['id'])) ?></strong>
                      <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.15rem;">
                        <?= \App\Core\DateHelper::render($tip['created_at'] ?? 'now', 'M j, Y • g:i A', 'datetime') ?>
                      </div>
                    </td>
                    <td>
                      <span style="color: #cbd5e1; font-weight: 600;"><?= htmlspecialchars($tip['tipper_name'] ?? $tip['sender_name'] ?? 'Anonymous Supporter') ?></span>
                      <?php if (!empty($tip['message'])): ?>
                        <div style="font-size: 0.72rem; color: #94a3b8; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($tip['message']) ?>">
                          <?= htmlspecialchars($tip['message']) ?>
                        </div>
                      <?php endif; ?>
                    </td>
                    <td>
                      <span class="ts-badge ts-badge-indigo" style="font-size: 0.68rem; font-weight: 700;"><?= htmlspecialchars($method) ?></span>
                      <?php if (!empty($tip['txid'])): ?>
                        <div style="font-size: 0.7rem; font-family: monospace; color: #38bdf8; margin-top: 0.2rem; word-break: break-all;">
                          TX: <?= htmlspecialchars(substr($tip['txid'], 0, 10)) ?>...<?= htmlspecialchars(substr($tip['txid'], -6)) ?>
                        </div>
                      <?php endif; ?>
                    </td>
                    <td style="text-align: right; font-weight: 700; color: #f8fafc; font-family: monospace;">
                      $<?= number_format($gross, 2) ?>
                    </td>
                    <td style="text-align: right; font-weight: 600; color: #f59e0b; font-family: monospace;">
                      -$<?= number_format($fee, 2) ?>
                    </td>
                    <td style="text-align: right; font-weight: 800; color: #10b981; font-family: monospace; font-size: 0.9rem;">
                      +$<?= number_format($net, 2) ?>
                    </td>
                    <td style="text-align: center;">
                      <?php if ($status === 'completed' || $status === 'paid'): ?>
                        <span class="ts-badge ts-badge-green" style="font-size: 0.68rem;">Dispatched ✓</span>
                      <?php elseif ($status === 'pending'): ?>
                        <span class="ts-badge ts-badge-amber" style="font-size: 0.68rem;">In Verification ⏳</span>
                      <?php else: ?>
                        <span class="ts-badge" style="font-size: 0.68rem; background: rgba(148,163,184,0.2);"><?= htmlspecialchars(ucfirst($status)) ?></span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div style="text-align: center; padding: 2rem 1rem; color: #94a3b8;">
            <div style="font-size: 2rem; margin-bottom: 0.5rem;">🪙</div>
            <h5 style="color: #cbd5e1; font-size: 1rem; margin: 0 0 0.35rem 0;">No Crypto Tips Logged Yet</h5>
            <p style="font-size: 0.82rem; margin: 0; max-width: 480px; margin: 0 auto;">
              Your tipping jar is active! Once readers scan your wallet QR or send peer patronage, transactions appear here with 95% net creator earnings.
            </p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div><!-- /amainpane_tipjar -->

  <!-- ========================================================================= -->
  <!-- 6. PANE: TECHNICAL READER FEEDBACK & COMMENT STREAM -->
  <!-- ========================================================================= -->
  <div id="amainpane_feedback" style="display: none;">
    <div class="ts-card" style="margin-bottom: 3rem; padding: 2rem;">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.75rem;">
        <div>
          <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
            <span class="ts-badge ts-badge-pink">READER DISCUSSIONS</span>
            <span style="font-size: 0.8rem; color: #64748b;">Feedback &amp; Peer Reviews Across Your Catalog</span>
          </div>
          <h3 style="font-size: 1.45rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
            💬 Reader Feedback &amp; Discussion Stream
          </h3>
          <p style="color: #94a3b8; font-size: 0.88rem; margin: 0.35rem 0 0 0; max-width: 650px;">
            Engage with developers who comment on your tutorials, prompt blueprints, and tools. Active responses boost creator reputation XP.
          </p>
        </div>
      </div>

      <?php if (!empty($recentComments)): ?>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
          <?php foreach ($recentComments as $cm): ?>
            <div class="author-subcard" style="background: rgba(15,23,42,0.8); border: 1px solid rgba(148,163,184,0.12); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; gap: 0.65rem;">
              <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                  <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #ec4899, #8b5cf6); display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 700; color: #fff;">
                    <?= strtoupper(substr($cm['username'] ?? 'R', 0, 1)) ?>
                  </div>
                  <div>
                    <strong style="color: #f8fafc; font-size: 0.88rem;"><?= htmlspecialchars($cm['username'] ?? 'Anonymous Reader') ?></strong>
                    <span style="font-size: 0.72rem; color: #64748b; margin-left: 0.4rem;">commented on</span>
                    <a href="<?= url('/article/' . ($cm['content_slug'] ?? '')) ?>#comments" target="_blank" style="color: #38bdf8; font-weight: 600; font-size: 0.85rem; text-decoration: none;">
                      <?= htmlspecialchars($cm['content_title'] ?? 'Publication') ?> ↗
                    </a>
                  </div>
                </div>
                <span style="font-size: 0.72rem; color: #64748b; font-family: var(--ts-font-mono);">
                  <?= \App\Core\DateHelper::formatDateTime($cm['created_at']) ?>
                </span>
              </div>

              <div style="background: #04070e; border-left: 3px solid #ec4899; padding: 0.75rem 1rem; border-radius: 4px; font-size: 0.85rem; color: #cbd5e1; line-height: 1.5;">
                <?= nl2br(htmlspecialchars($cm['comment'] ?? $cm['content'] ?? '')) ?>
              </div>

              <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                <a href="<?= url('/article/' . ($cm['content_slug'] ?? '')) ?>#comments" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.65rem; color: #38bdf8; border-color: rgba(56,189,248,0.3);">
                  Reply on Article &rarr;
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div style="background: rgba(15,23,42,0.5); border: 1px dashed rgba(148,163,184,0.15); border-radius: 12px; padding: 3rem 2rem; text-align: center; color: #64748b;">
          <div style="font-size: 2.25rem; margin-bottom: 0.5rem;">💬</div>
          <h4 style="color: #f8fafc; font-size: 1.15rem; margin: 0 0 0.5rem 0;">No Reader Discussions Yet</h4>
          <p style="margin: 0; font-size: 0.88rem;">
            When readers comment or ask architectural questions on your published technical publications, they will stream here in real time!
          </p>
        </div>
      <?php endif; ?>
    </div>
  </div><!-- /amainpane_feedback -->

  <!-- ========================================================================= -->
  <!-- 7. PANE: SECURITY, CREDENTIALS & ACTIVE SESSIONS HUD -->
  <!-- ========================================================================= -->
  <div id="amainpane_security" style="display: none;">
    <div class="ts-card" style="margin-bottom: 3rem; padding: 2rem;">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.75rem;">
        <div>
          <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
            <span class="ts-badge ts-badge-green">ACCOUNT GOVERNANCE</span>
            <span style="font-size: 0.8rem; color: #64748b;">Device Sessions, Multi-Factor &amp; Credential Hygiene</span>
          </div>
          <h3 style="font-size: 1.45rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
            🛡️ Creator Security &amp; Device Manager
          </h3>
          <p style="color: #94a3b8; font-size: 0.88rem; margin: 0.35rem 0 0 0; max-width: 650px;">
            Protect your publication portfolio and royalty payout destinations with hardened security credentials and session oversight.
          </p>
        </div>

        <div>
          <button type="button" onclick="openPasswordModal()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #6366f1, #38bdf8); font-weight: 700; border: none; padding: 0.55rem 1.25rem;">
            <i data-lucide="key"></i> Change Password 🔑
          </button>
        </div>
      </div>

      <!-- Security Scorecard -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        <div class="author-subcard" style="background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.12); border-radius: 10px; padding: 1.25rem;">
          <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Password Status</div>
          <div style="font-size: 1.25rem; font-weight: 800; color: #10b981; font-family: 'Outfit', sans-serif; margin-top: 0.25rem;">
            ACTIVE &amp; HARDENED ✓
          </div>
          <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.2rem;">Encrypted with bcrypt (Cost 12)</div>
        </div>

        <div class="author-subcard" style="background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.12); border-radius: 10px; padding: 1.25rem;">
          <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Active Devices</div>
          <div style="font-size: 1.25rem; font-weight: 800; color: #38bdf8; font-family: monospace; margin-top: 0.25rem;">
            <?= max(1, count($userSessions ?? [])) ?> Active Session<?= count($userSessions ?? []) !== 1 ? 's' : '' ?>
          </div>
          <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.2rem;">Under continuous IP audit</div>
        </div>

        <div class="author-subcard" style="background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.12); border-radius: 10px; padding: 1.25rem;">
          <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Payout Lock Status</div>
          <div style="font-size: 1.25rem; font-weight: 800; color: #10b981; font-family: 'Outfit', sans-serif; margin-top: 0.25rem;">
            PROTECTED 🔒
          </div>
          <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.2rem;">Changes require password re-auth</div>
        </div>
      </div>

      <!-- Active Sessions Roster -->
      <h4 style="font-size: 1.05rem; color: #f8fafc; margin: 0 0 1rem 0; font-family: 'Outfit', sans-serif;">
        🖥️ Active Authenticated Browser Sessions
      </h4>

      <div style="overflow-x: auto; margin-bottom: 1.5rem;">
        <table class="ts-table" style="width: 100%; border-collapse: collapse;">
          <thead>
            <tr style="border-bottom: 1px solid rgba(148,163,184,0.15); text-align: left; font-size: 0.78rem; color: #94a3b8;">
              <th style="padding: 0.75rem;">Device &amp; Platform</th>
              <th style="padding: 0.75rem;">IP Address</th>
              <th style="padding: 0.75rem;">Last Active</th>
              <th style="padding: 0.75rem;">Session Status</th>
            </tr>
          </thead>
          <tbody>
            <tr style="border-bottom: 1px solid rgba(148,163,184,0.08); font-size: 0.85rem;">
              <td style="padding: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                  <span style="font-size: 1.2rem;">💻</span>
                  <div>
                    <strong style="color: #f8fafc;">Current Browser Session</strong>
                    <div style="font-size: 0.72rem; color: #64748b;"><?= htmlspecialchars($_SERVER['HTTP_USER_AGENT'] ?? 'Web Browser') ?></div>
                  </div>
                </div>
              </td>
              <td style="padding: 0.75rem; font-family: monospace; color: #38bdf8;">
                <?= htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1') ?>
              </td>
              <td style="padding: 0.75rem; color: #cbd5e1; font-size: 0.8rem;">
                Just now (Active)
              </td>
              <td style="padding: 0.75rem;">
                <span class="ts-badge ts-badge-green" style="font-size: 0.7rem;">THIS DEVICE ✓</span>
              </td>
            </tr>
            <?php if (!empty($userSessions)): ?>
              <?php foreach ($userSessions as $s): ?>
                <tr style="border-bottom: 1px solid rgba(148,163,184,0.08); font-size: 0.85rem;">
                  <td style="padding: 0.75rem; color: #cbd5e1;">
                    <?= htmlspecialchars($s['user_agent'] ?? 'Secondary Device') ?>
                  </td>
                  <td style="padding: 0.75rem; font-family: monospace; color: #94a3b8;">
                    <?= htmlspecialchars($s['ip_address'] ?? '—') ?>
                  </td>
                  <td style="padding: 0.75rem; color: #64748b; font-size: 0.8rem;">
                    <?= htmlspecialchars($s['last_activity'] ?? '—') ?>
                  </td>
                  <td style="padding: 0.75rem;">
                    <span class="ts-badge ts-badge-indigo" style="font-size: 0.7rem;">AUTHENTICATED</span>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div style="display: flex; justify-content: flex-end;">
        <button type="button" onclick="openPasswordModal()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.3);">
          Terminate Other Sessions via Password Reset 🚨
        </button>
      </div>
    </div>
  </div><!-- /amainpane_security -->

</div>

<!-- ========================================================================= -->
<!-- MODAL: DEDICATED CHANGE PASSWORD MODAL -->
<!-- ========================================================================= -->
<div id="changePasswordModal" class="admin-modal-overlay" style="display: none; align-items: center; justify-content: center; z-index: 999999;">
  <div class="admin-modal-box modal-cyan" style="max-width: 480px; width: 95%; background: var(--ts-bg-surface, #090d16); border: 1px solid rgba(99,102,241,0.4); border-radius: 16px; padding: 2rem; box-shadow: 0 25px 70px rgba(0,0,0,0.85);">
    <div class="admin-modal-header" style="border-bottom: 1px solid var(--ts-border-subtle, rgba(148,163,184,0.12)); padding-bottom: 1rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: flex-start;">
      <div>
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
          <span class="ts-badge ts-badge-pink">ACCOUNT SECURITY</span>
        </div>
        <h3 class="admin-modal-title" style="color: var(--ts-text-primary, #f8fafc); font-size: 1.25rem; font-weight: 800; margin: 0;">
          🔐 Change Account Password
        </h3>
      </div>
      <button type="button" onclick="closePasswordModal()" class="admin-modal-close" style="font-size: 1.3rem;">✕</button>
    </div>

    <form action="<?= url('/user/change-password') ?>" method="POST" id="changePasswordForm">
      <div style="margin-bottom: 1.25rem;">
        <label class="admin-modal-label" style="display: block; font-size: 0.78rem; text-transform: uppercase; color: var(--ts-text-secondary, #94a3b8); font-weight: 600; margin-bottom: 0.35rem;">
          Current Password <span style="color: #ef4444;">*</span>
        </label>
        <div style="position: relative;">
          <input type="password" name="current_password" id="cp_current" class="admin-modal-input" placeholder="Enter your current password" style="width: 100%; padding-right: 2.5rem;" required>
          <button type="button" onclick="togglePassVisibility('cp_current')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #64748b; cursor: pointer;">
            👁️
          </button>
        </div>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label class="admin-modal-label" style="display: block; font-size: 0.78rem; text-transform: uppercase; color: var(--ts-text-secondary, #94a3b8); font-weight: 600; margin-bottom: 0.35rem;">
          New Password (Min. 8 characters) <span style="color: #ef4444;">*</span>
        </label>
        <div style="position: relative;">
          <input type="password" name="new_password" id="cp_new" minlength="8" class="admin-modal-input" placeholder="Enter at least 8 strong characters" style="width: 100%; padding-right: 2.5rem;" oninput="checkPassStrength(this.value)" required>
          <button type="button" onclick="togglePassVisibility('cp_new')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #64748b; cursor: pointer;">
            👁️
          </button>
        </div>
        <div id="passStrengthMeter" style="height: 4px; border-radius: 2px; background: rgba(148,163,184,0.15); margin-top: 0.5rem; overflow: hidden;">
          <div id="passStrengthBar" style="height: 100%; width: 0%; transition: all 0.3s ease;"></div>
        </div>
        <div id="passStrengthText" style="font-size: 0.72rem; color: #64748b; margin-top: 0.25rem;">Strength: Enter password</div>
      </div>

      <div style="margin-bottom: 1.5rem;">
        <label class="admin-modal-label" style="display: block; font-size: 0.78rem; text-transform: uppercase; color: var(--ts-text-secondary, #94a3b8); font-weight: 600; margin-bottom: 0.35rem;">
          Confirm New Password <span style="color: #ef4444;">*</span>
        </label>
        <div style="position: relative;">
          <input type="password" name="confirm_password" id="cp_confirm" class="admin-modal-input" placeholder="Re-enter new password" style="width: 100%; padding-right: 2.5rem;" required>
          <button type="button" onclick="togglePassVisibility('cp_confirm')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #64748b; cursor: pointer;">
            👁️
          </button>
        </div>
      </div>

      <div style="background: var(--ts-bg-surface, rgba(15,23,42,0.6)); border: 1px solid var(--ts-border-subtle, rgba(148,163,184,0.12)); border-radius: 8px; padding: 0.85rem; margin-bottom: 1.5rem;">
        <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer; font-size: 0.82rem; color: var(--ts-text-secondary, #cbd5e1); margin: 0;">
          <input type="checkbox" name="terminate_other_sessions" value="1" checked style="accent-color: #6366f1; width: 16px; height: 16px;">
          <span>Sign out of all other active devices &amp; browsers</span>
        </label>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
        <button type="button" onclick="closePasswordModal()" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.5rem 1.1rem;">
          Cancel
        </button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #6366f1, #38bdf8); border: none; font-weight: 700; padding: 0.5rem 1.3rem;">
          Update Password 🔒
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: DETAILED PAYOUT AUDIT & REJECTION RESOLUTION DOSSIER -->
<!-- ========================================================================= -->
<div id="payoutAuditModal" class="admin-modal-overlay" style="display: none; align-items: center; justify-content: center; z-index: 999999;">
  <div class="admin-modal-box modal-cyan" style="max-width: 620px; width: 95%; background: var(--ts-bg-surface, #090d16); border: 1px solid rgba(99,102,241,0.4); border-radius: 16px; padding: 2rem; box-shadow: 0 25px 70px rgba(0,0,0,0.85); max-height: 90vh; overflow-y: auto;">
    <div class="admin-modal-header" style="border-bottom: 1px solid var(--ts-border-subtle, rgba(148,163,184,0.12)); padding-bottom: 1rem; margin-bottom: 1.25rem; display: flex; justify-content: space-between; align-items: flex-start;">
      <div>
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
          <span class="ts-badge ts-badge-cyan" id="pa_id_badge">PAYOUT DOSSIER #</span>
          <span id="pa_status_badge" class="ts-badge ts-badge-pink">STATUS</span>
        </div>
        <h3 class="admin-modal-title" style="color: var(--ts-text-primary, #f8fafc); font-size: 1.25rem; font-weight: 800; margin: 0;">
          💳 Settlement Audit &amp; Resolution Dossier
        </h3>
      </div>
      <button type="button" onclick="closePayoutAuditModal()" class="admin-modal-close" style="font-size: 1.3rem;">✕</button>
    </div>

    <!-- Rejection Alert & Balance Refund Confirmation (Displayed if Rejected) -->
    <div id="pa_rejected_banner" style="display: none; margin-bottom: 1.5rem; background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.35); border-radius: 12px; padding: 1.25rem;">
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
        <span style="font-size: 1.3rem;">🚨</span>
        <h4 style="margin: 0; color: #ef4444; font-size: 1rem; font-weight: 800;">Withdrawal Request Rejected</h4>
      </div>
      <div style="font-size: 0.85rem; color: var(--ts-text-secondary); margin-bottom: 0.85rem; line-height: 1.5;">
        The finance and editorial review team was unable to process this transfer due to the following specific reason:
      </div>
      <div style="background: rgba(15,23,42,0.6); border-left: 3px solid #ef4444; border-radius: 4px; padding: 0.75rem 1rem; font-size: 0.92rem; color: #fecaca; margin-bottom: 0.85rem; font-family: var(--ts-font-sans); line-height: 1.5;" id="pa_rejection_reason">
        No administrative reason specified.
      </div>
      <div style="display: flex; align-items: center; gap: 0.5rem; background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); border-radius: 8px; padding: 0.65rem 0.85rem; font-size: 0.82rem; color: #10b981; font-weight: 600;">
        <i data-lucide="shield-check" style="width: 16px; height: 16px; flex-shrink: 0;"></i>
        <span><strong>100% Refund Guaranteed:</strong> The requested funds (<span id="pa_refund_amount">$0.00</span>) have been fully credited back to your royalty balance. No funds or fees were deducted.</span>
      </div>
    </div>

    <!-- Financial Breakdown Cards -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-bottom: 1.5rem;">
      <div class="ts-card" style="padding: 1rem; text-align: center;">
        <span style="font-size: 0.72rem; color: var(--ts-text-tertiary); text-transform: uppercase; font-weight: 600; display: block;">Gross Claimed</span>
        <strong style="font-size: 1.25rem; color: var(--ts-text-primary);" id="pa_amount_gross">$0.00</strong>
      </div>
      <div class="ts-card" style="padding: 1rem; text-align: center;">
        <span style="font-size: 0.72rem; color: var(--ts-text-tertiary); text-transform: uppercase; font-weight: 600; display: block;">Processing Fee</span>
        <strong style="font-size: 1.25rem; color: var(--ts-text-tertiary);" id="pa_amount_fee">$0.00</strong>
      </div>
      <div class="ts-card" style="padding: 1rem; text-align: center;">
        <span style="font-size: 0.72rem; color: var(--ts-text-tertiary); text-transform: uppercase; font-weight: 600; display: block;">Net Settlement</span>
        <strong style="font-size: 1.25rem; color: #10b981;" id="pa_amount_net">$0.00</strong>
      </div>
    </div>

    <!-- Payout Method & Destination -->
    <div style="background: var(--ts-bg-surface-2, rgba(99,102,241,0.06)); border: 1px solid var(--ts-border-subtle); border-radius: 10px; padding: 1rem 1.25rem; margin-bottom: 1.5rem;">
      <div style="font-size: 0.75rem; color: var(--ts-text-tertiary); text-transform: uppercase; font-weight: 700; margin-bottom: 0.4rem;">
        Destination Coordinates Used
      </div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
        <span class="ts-badge ts-badge-cyan" id="pa_method_name" style="text-transform: uppercase; font-size: 0.75rem;">BANK WIRE</span>
      </div>
      <div id="pa_address_text" style="font-family: var(--ts-font-mono); font-size: 0.85rem; color: var(--ts-text-secondary); word-break: break-all; line-height: 1.5;">
        —
      </div>
    </div>

    <!-- Processing Timestamps & Reference -->
    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; margin-bottom: 1.5rem;">
      <tr style="border-bottom: 1px solid var(--ts-border-subtle);">
        <td style="padding: 0.5rem 0; color: var(--ts-text-tertiary); width: 180px;">Requested On</td>
        <td style="padding: 0.5rem 0; font-family: var(--ts-font-mono); color: var(--ts-text-primary);" id="pa_date_requested">—</td>
      </tr>
      <tr style="border-bottom: 1px solid var(--ts-border-subtle);">
        <td style="padding: 0.5rem 0; color: var(--ts-text-tertiary);">Reviewed / Settled On</td>
        <td style="padding: 0.5rem 0; font-family: var(--ts-font-mono); color: var(--ts-text-primary);" id="pa_date_processed">—</td>
      </tr>
      <tr style="border-bottom: 1px solid var(--ts-border-subtle);">
        <td style="padding: 0.5rem 0; color: var(--ts-text-tertiary);">Transaction Reference</td>
        <td style="padding: 0.5rem 0; font-family: var(--ts-font-mono); color: var(--ts-text-primary);" id="pa_tx_ref">—</td>
      </tr>
      <tr id="pa_receipt_row" style="display: none; border-bottom: 1px solid var(--ts-border-subtle);">
        <td style="padding: 0.5rem 0; color: var(--ts-text-tertiary);">Official Payment Receipt</td>
        <td style="padding: 0.5rem 0;">
          <a href="#" id="pa_receipt_link" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; color: #38bdf8;">
            <i data-lucide="file-check"></i> Download Payment Slip / Receipt 📄
          </a>
        </td>
      </tr>
    </table>

    <!-- Next Steps / Resolution Guide -->
    <div id="pa_rejected_help" style="display: none; background: rgba(99,102,241,0.06); border: 1px solid rgba(99,102,241,0.25); border-radius: 10px; padding: 1rem; margin-bottom: 1.5rem;">
      <h5 style="margin: 0 0 0.35rem 0; color: var(--ts-text-primary); font-size: 0.88rem; font-weight: 700;">💡 How to Resolve &amp; Cash Out:</h5>
      <ol style="margin: 0; padding-left: 1.25rem; font-size: 0.82rem; color: var(--ts-text-secondary); line-height: 1.6;">
        <li>Review the rejection reason above and ensure your SWIFT BIC, IBAN, or PayPal address is active and verified.</li>
        <li>Open <a href="#payout-methods" onclick="closePayoutAuditModal(); switchMTab('monetization'); openAddPayoutMethodModal(); return false;" style="color: #38bdf8; text-decoration: underline;">Linked Payout Methods</a> and update your account details.</li>
        <li>Submit a fresh cashout request whenever you are ready.</li>
        <li>If you believe this was an error, open a ticket at the <a href="<?= url('/support') ?>" style="color: #ec4899; text-decoration: underline;">Support Desk</a> referencing this payout ID.</li>
      </ol>
    </div>

    <!-- Modal Footer -->
    <div style="display: flex; justify-content: flex-end; gap: 0.75rem; flex-wrap: wrap;">
      <button type="button" onclick="closePayoutAuditModal()" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.5rem 1.1rem;">
        Close Dossier
      </button>
      <div id="pa_rejected_actions" style="display: none; gap: 0.5rem;">
        <button type="button" onclick="closePayoutAuditModal(); openAddPayoutMethodModal();" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #6366f1; border-color: #6366f1;">
          <i data-lucide="settings"></i> Update Payout Method
        </button>
        <button type="button" onclick="closePayoutAuditModal(); openPayoutModal();" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #10b981; border-color: #10b981;">
          <i data-lucide="repeat"></i> Submit New Cashout 💵
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: COMPREHENSIVE PROFILE & BANKING CUSTOMIZER -->
<!-- ========================================================================= -->
<div id="profileModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-cyan" style="max-width: 680px; max-height: 90vh; display: flex; flex-direction: column;">
    <div class="admin-modal-header" style="flex: 0 0 auto;">
      <h3 class="admin-modal-title" style="color: #38bdf8;"><i data-lucide="user-cog"></i> Creator Profile &amp; Banking Setup</h3>
      <button type="button" onclick="closeProfileModal()" class="admin-modal-close">✕</button>
    </div>

    <!-- Modal Sub-Tabs Strip -->
    <div style="display: flex; gap: 0.5rem; background: rgba(15,23,42,0.8); padding: 0.5rem 1rem; border-bottom: 1px solid var(--admin-border-subtle); flex: 0 0 auto; flex-wrap: wrap;">
      <button type="button" onclick="switchProfTab('identity')" id="ptab_identity" class="ts-btn ts-btn-sm ts-btn-primary" style="font-size: 0.78rem; padding: 0.3rem 0.75rem;">
        👤 Public Identity
      </button>
      <button type="button" onclick="switchProfTab('socials')" id="ptab_socials" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.78rem; padding: 0.3rem 0.75rem;">
        🌐 Developer Socials
      </button>
      <button type="button" onclick="switchProfTab('crypto')" id="ptab_crypto" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.78rem; padding: 0.3rem 0.75rem; color: #f59e0b; border-color: rgba(245,158,11,0.4);">
        ⚡ Crypto Tip Wallets (95% Payout)
      </button>
    </div>

    <form action="<?= url('/author/profile/update') ?>" method="POST" enctype="multipart/form-data" style="overflow-y: auto; padding: 1.5rem; flex: 1 1 auto;">
      
      <!-- 1. PUBLIC IDENTITY TAB -->
      <div id="psec_identity">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
          <div>
            <label class="admin-modal-label">Public Display Name <span style="color: #ef4444;">*</span></label>
            <input type="text" name="display_name" value="<?= htmlspecialchars($author['display_name'] ?? $user['username']) ?>" class="admin-modal-input" required>
          </div>
          <div>
            <label class="admin-modal-label">Professional Role / Headline <span style="font-size: 0.72rem; color: #94a3b8; font-weight: normal;">(Max 100 chars)</span></label>
            <input type="text" name="headline" value="<?= htmlspecialchars($author['headline'] ?? '') ?>" maxlength="100" placeholder="e.g. Lead Systems Architect" class="admin-modal-input">
          </div>
        </div>

        <!-- Strict 1:1 Square Avatar Upload -->
        <div style="margin-bottom: 1.25rem;">
          <label class="admin-modal-label">Profile Avatar Photo (Strict 1:1 Square Ratio • Direct Upload Only) <span style="color: #ef4444;">*</span></label>
          <div style="display: flex; gap: 1.25rem; align-items: center; background: rgba(15,23,42,0.6); padding: 1rem; border-radius: 8px; border: 1px solid rgba(148,163,184,0.15);">
            <div id="avatar_preview_wrap" style="width: 76px; height: 76px; border-radius: 50%; overflow: hidden; border: 2px solid #38bdf8; flex-shrink: 0; background: #000; display: flex; align-items: center; justify-content: center;">
              <img id="avatar_preview_img" src="<?= htmlspecialchars(image_url($author['avatar_url'] ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&h=200&fit=crop')) ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div style="flex: 1;">
              <input type="file" name="avatar_file" id="avatar_file_input" accept="image/*" onchange="previewAndValidateSquareAvatar(this)" style="display: none;">
              <input type="hidden" name="avatar_url" id="profile_avatar_url" value="<?= htmlspecialchars(image_url($author['avatar_url'] ?? '')) ?>">
              <button type="button" onclick="document.getElementById('avatar_file_input').click()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #38bdf8; color: #000; font-weight: 700;">
                <i data-lucide="upload"></i> Upload Square Avatar 📸
              </button>
              <div id="avatar_ratio_feedback" style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.35rem;">
                PNG, JPG, or WEBP. Must be 1:1 square ratio (e.g. 500x500 px). Non-square images will be rejected.
              </div>
            </div>
          </div>
        </div>

        <!-- Cover Banner Image -->
        <div style="margin-bottom: 1.25rem;">
          <label class="admin-modal-label">Cover Header Banner (Direct Device Upload / Optional Fallback Link)</label>
          <div style="background: rgba(15,23,42,0.6); padding: 0.85rem 1rem; border-radius: 8px; border: 1px solid rgba(148,163,184,0.15);">
            <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem;">
              <input type="file" name="cover_file" id="cover_file_input" accept="image/*" onchange="previewCoverBanner(this)" style="display: none;">
              <button type="button" onclick="document.getElementById('cover_file_input').click()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #cbd5e1;">
                <i data-lucide="upload"></i> Upload Banner Image 🖼️
              </button>
              <span id="cover_filename_display" style="font-size: 0.75rem; color: #38bdf8;"></span>
            </div>
            <input type="text" name="cover_image_url" id="profile_cover_url" value="<?= htmlspecialchars(image_url($author['cover_image_url'] ?? '')) ?>" placeholder="Or optional image URL/path fallback (https://... or /storage/...)" class="admin-modal-input" style="font-size: 0.82rem;">
          </div>
        </div>

        <div style="margin-bottom: 1rem;">
          <label class="admin-modal-label">Creator Biography <span style="font-size: 0.72rem; color: #94a3b8; font-weight: normal;">(Max 500 chars)</span></label>
          <textarea name="bio" rows="3" maxlength="500" class="admin-modal-textarea" placeholder="Tell readers about your systems engineering focus, research topics, or tech stack..."><?= htmlspecialchars($author['bio'] ?? '') ?></textarea>
        </div>

        <div>
          <label class="admin-modal-label">Portfolio Website URL <span style="font-size: 0.72rem; color: #94a3b8; font-weight: normal;">(Max 200 chars)</span></label>
          <input type="url" name="website_url" maxlength="200" value="<?= htmlspecialchars($author['website_url'] ?? ($socialLinks['website'] ?? '')) ?>" placeholder="https://myportfolio.dev" class="admin-modal-input">
        </div>
      </div>

      <!-- 2. DYNAMIC SOCIALS & CHANNELS TAB -->
      <div id="psec_socials" style="display: none;">
        <div style="background: rgba(99,102,241,0.08); border: 1px solid rgba(99,102,241,0.25); border-radius: 8px; padding: 0.85rem 1rem; margin-bottom: 1.25rem;">
          <div style="font-size: 0.85rem; color: #38bdf8; font-weight: 700;">🌐 Connect Developer Socials &amp; Custom Media Links</div>
          <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 0.2rem;">
            Select from all major engineering networks (GitHub, Twitter/X, Discord, Telegram, YouTube, Twitch, Substack, Medium, Hugging Face, etc.) or add your own custom links.
          </div>
        </div>

        <?php
        $existingSocials = \App\Core\SocialHelper::normalizeLinks($author['social_links'] ?? []);
        $allPlatforms = \App\Core\SocialHelper::getAvailablePlatforms();
        ?>

        <div id="socialLinksContainer" style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1rem;">
          <?php if (!empty($existingSocials)): ?>
            <?php foreach ($existingSocials as $idx => $soc): ?>
              <div class="social-link-row" style="display: flex; gap: 0.65rem; align-items: center; background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.18); padding: 0.65rem; border-radius: 8px; flex-wrap: wrap;">
                <select name="social_platforms[]" class="admin-modal-select" onchange="updateSocialRowPlaceholder(this)" style="width: 175px; flex-shrink: 0; padding: 0.45rem 0.65rem; font-size: 0.85rem;">
                  <?php foreach ($allPlatforms as $pKey => $pMeta): ?>
                    <option value="<?= htmlspecialchars($pKey) ?>" <?= $pKey === $soc['platform'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($pMeta['label']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>

                <input type="text" name="social_urls[]" value="<?= htmlspecialchars($soc['raw_url'] ?? $soc['url']) ?>" placeholder="URL or @handle" class="admin-modal-input" style="flex: 1; min-width: 200px;" required>

                <input type="text" name="social_labels[]" value="<?= htmlspecialchars($soc['label'] !== $soc['name'] ? $soc['label'] : '') ?>" placeholder="Label (Optional)" class="admin-modal-input" style="width: 150px;">

                <button type="button" onclick="removeSocialRow(this)" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.3); padding: 0.45rem 0.65rem;" title="Remove this link">✕</button>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="social-link-row" style="display: flex; gap: 0.65rem; align-items: center; background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.18); padding: 0.65rem; border-radius: 8px; flex-wrap: wrap;">
              <select name="social_platforms[]" class="admin-modal-select" onchange="updateSocialRowPlaceholder(this)" style="width: 175px; flex-shrink: 0; padding: 0.45rem 0.65rem; font-size: 0.85rem;">
                <?php foreach ($allPlatforms as $pKey => $pMeta): ?>
                  <option value="<?= htmlspecialchars($pKey) ?>" <?= $pKey === 'github' ? 'selected' : '' ?>>
                    <?= htmlspecialchars($pMeta['label']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <input type="text" name="social_urls[]" value="" placeholder="https://github.com/username" class="admin-modal-input" style="flex: 1; min-width: 200px;">
              <input type="text" name="social_labels[]" value="" placeholder="Label (Optional)" class="admin-modal-input" style="width: 150px;">
              <button type="button" onclick="removeSocialRow(this)" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.3); padding: 0.45rem 0.65rem;" title="Remove this link">✕</button>
            </div>
          <?php endif; ?>
        </div>

        <button type="button" onclick="addSocialRow()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3); margin-top: 0.25rem;">
          + Add Another Social Link / Channel
        </button>
      </div>

      <!-- 3. CRYPTO TIP JAR & PATRONAGE WALLETS TAB (95% Creator Payout) -->
      <div id="psec_crypto" style="display: none;">
        <div style="background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.25); border-radius: 8px; padding: 0.85rem 1rem; margin-bottom: 1.25rem;">
          <div style="font-size: 0.85rem; color: #f59e0b; font-weight: 700;">⚡ Creator Crypto Patronage &amp; Tipping Payout Addresses</div>
          <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 0.2rem; line-height: 1.5;">
            Configure your on-chain crypto wallets. Readers can tip you directly from articles, tutorials, or public profiles. <strong>95% of gross tips</strong> are dispatched to these addresses, with a 5% platform protocol contribution retained for network gas subsidies.
          </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1rem;">
          <div>
            <label class="admin-modal-label" style="display: flex; justify-content: space-between;">
              <strong style="color: #f59e0b;">₿ Bitcoin (BTC) Payout Address</strong>
              <span style="font-size: 0.72rem; color: #94a3b8;">Native SegWit (bc1q), Taproot (bc1p), or Legacy</span>
            </label>
            <input type="text" name="tip_wallet_btc" id="prof_tip_wallet_btc" value="<?= htmlspecialchars($author['tip_wallet_btc'] ?? '') ?>" placeholder="e.g. bc1q9v80n7..." class="admin-modal-input" style="font-family: monospace; font-size: 0.85rem;">
          </div>

          <div>
            <label class="admin-modal-label" style="display: flex; justify-content: space-between;">
              <strong style="color: #818cf8;">Ξ Ethereum / EVM Address</strong>
              <span style="font-size: 0.72rem; color: #94a3b8;">ERC-20 (Arbitrum, Base, Optimism, Polygon)</span>
            </label>
            <input type="text" name="tip_wallet_eth" id="prof_tip_wallet_eth" value="<?= htmlspecialchars($author['tip_wallet_eth'] ?? '') ?>" placeholder="e.g. 0x71C..." class="admin-modal-input" style="font-family: monospace; font-size: 0.85rem;">
          </div>

          <div>
            <label class="admin-modal-label" style="display: flex; justify-content: space-between;">
              <strong style="color: #34d399;">₮ Tether (USDT) Address</strong>
              <span style="font-size: 0.72rem; color: #94a3b8;">TRC-20 (Tron) or ERC-20</span>
            </label>
            <input type="text" name="tip_wallet_usdt" id="prof_tip_wallet_usdt" value="<?= htmlspecialchars($author['tip_wallet_usdt'] ?? '') ?>" placeholder="e.g. TXa9... or 0x..." class="admin-modal-input" style="font-family: monospace; font-size: 0.85rem;">
          </div>

          <div>
            <label class="admin-modal-label" style="display: flex; justify-content: space-between;">
              <strong style="color: #c084fc;">◎ Solana (SOL) Address</strong>
              <span style="font-size: 0.72rem; color: #94a3b8;">SPL Network (Phantom, Solflare, Backpack)</span>
            </label>
            <input type="text" name="tip_wallet_sol" id="prof_tip_wallet_sol" value="<?= htmlspecialchars($author['tip_wallet_sol'] ?? '') ?>" placeholder="e.g. So11..." class="admin-modal-input" style="font-family: monospace; font-size: 0.85rem;">
          </div>
        </div>
      </div>

      <!-- Payout & Banking Notice Card pointing to Royalties & Ledger tab -->
      <div style="background: rgba(16,185,129,0.06); border: 1px solid rgba(16,185,129,0.25); border-radius: 8px; padding: 1rem; margin-top: 1.25rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
        <div style="display: flex; gap: 0.75rem; align-items: center;">
          <div style="width: 38px; height: 38px; border-radius: 8px; background: rgba(16,185,129,0.15); display: flex; align-items: center; justify-content: center; color: #34d399; flex-shrink: 0;">
            <i data-lucide="wallet" style="width: 20px; height: 20px;"></i>
          </div>
          <div>
            <div style="font-size: 0.86rem; font-weight: 700; color: #34d399;">Looking to link or update Bank Payout Methods?</div>
            <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 0.15rem;">
              Bank accounts, PayPal, and crypto wallets are securely managed under the <strong>Royalties &amp; Ledger</strong> tab.
            </div>
          </div>
        </div>
        <button type="button" onclick="closeEditProfileModal(); switchAuthorMainTab('payouts');" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #34d399; border-color: rgba(16,185,129,0.4); font-weight: 700; white-space: nowrap;">
          Manage Payout Methods ➔
        </button>
      </div>

      <div class="admin-modal-footer" style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid rgba(148,163,184,0.12);">
        <button type="button" onclick="closeProfileModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #38bdf8; border-color: #38bdf8; color: #000; font-weight: 700;">Save All Settings ✓</button>
      </div>
    </form>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: DEDICATED QUICK-CONFIG CRYPTO TIP WALLETS (95% CREATOR SHARE) -->
<!-- ========================================================================= -->
<div id="cryptoWalletModal" class="admin-modal-overlay" style="display: none;">
  <div class="admin-modal-box modal-amber" style="max-width: 560px;">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #f59e0b; display: flex; align-items: center; gap: 0.5rem;">
        <i data-lucide="wallet"></i> Configure Creator Crypto Tip Wallets
      </h3>
      <button type="button" onclick="closeCryptoWalletModal()" class="admin-modal-close">✕</button>
    </div>
    <div style="padding: 1.5rem;">
      <div style="background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.3); border-radius: 8px; padding: 0.85rem 1rem; margin-bottom: 1.25rem;">
        <div style="font-size: 0.85rem; color: #f59e0b; font-weight: 700;">Direct Patronage Routing • 95% Creator Share</div>
        <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 0.2rem; line-height: 1.5;">
          These destination addresses receive your 95% tip earnings. The 5% platform protocol fee covers gas subsidies and network validator relays.
        </div>
      </div>

      <form id="quickCryptoWalletForm" onsubmit="saveCryptoWalletsAjax(event)">
        <div style="margin-bottom: 1rem;">
          <label class="admin-modal-label" style="display: flex; justify-content: space-between;">
            <strong style="color: #f59e0b;">₿ Bitcoin (BTC) Payout Address</strong>
            <span style="font-size: 0.72rem; color: #94a3b8;">SegWit / Taproot</span>
          </label>
          <input type="text" name="tip_wallet_btc" id="q_tip_wallet_btc" value="<?= htmlspecialchars($author['tip_wallet_btc'] ?? '') ?>" placeholder="bc1q..." class="admin-modal-input" style="font-family: monospace; font-size: 0.85rem;">
        </div>

        <div style="margin-bottom: 1rem;">
          <label class="admin-modal-label" style="display: flex; justify-content: space-between;">
            <strong style="color: #818cf8;">Ξ Ethereum / EVM Address</strong>
            <span style="font-size: 0.72rem; color: #94a3b8;">ERC-20 (Arbitrum, Base, Polygon)</span>
          </label>
          <input type="text" name="tip_wallet_eth" id="q_tip_wallet_eth" value="<?= htmlspecialchars($author['tip_wallet_eth'] ?? '') ?>" placeholder="0x..." class="admin-modal-input" style="font-family: monospace; font-size: 0.85rem;">
        </div>

        <div style="margin-bottom: 1rem;">
          <label class="admin-modal-label" style="display: flex; justify-content: space-between;">
            <strong style="color: #34d399;">₮ Tether (USDT) Address</strong>
            <span style="font-size: 0.72rem; color: #94a3b8;">TRC-20 / ERC-20</span>
          </label>
          <input type="text" name="tip_wallet_usdt" id="q_tip_wallet_usdt" value="<?= htmlspecialchars($author['tip_wallet_usdt'] ?? '') ?>" placeholder="TXa9... or 0x..." class="admin-modal-input" style="font-family: monospace; font-size: 0.85rem;">
        </div>

        <div style="margin-bottom: 1.5rem;">
          <label class="admin-modal-label" style="display: flex; justify-content: space-between;">
            <strong style="color: #c084fc;">◎ Solana (SOL) Address</strong>
            <span style="font-size: 0.72rem; color: #94a3b8;">SPL Network</span>
          </label>
          <input type="text" name="tip_wallet_sol" id="q_tip_wallet_sol" value="<?= htmlspecialchars($author['tip_wallet_sol'] ?? '') ?>" placeholder="So11..." class="admin-modal-input" style="font-family: monospace; font-size: 0.85rem;">
        </div>

        <div id="quickWalletFeedback" style="display: none; margin-bottom: 1rem; padding: 0.75rem; border-radius: 6px; font-size: 0.85rem;"></div>

        <div class="admin-modal-footer" style="padding-top: 1rem; border-top: 1px solid rgba(148,163,184,0.12);">
          <button type="button" onclick="closeCryptoWalletModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
          <button type="submit" id="btnSaveCryptoWallets" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #f59e0b; border-color: #f59e0b; color: #000; font-weight: 700;">
            Save Crypto Wallets ✓
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: DIRECT CASH PAYOUT REQUEST (LINKED METHOD ENFORCED) -->
<!-- ========================================================================= -->
<div id="payoutModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-green" style="max-width: 540px;">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #34d399;"><i data-lucide="arrow-down-right"></i> Request Cashout Payout</h3>
      <button type="button" onclick="closePayoutModal()" class="admin-modal-close">✕</button>
    </div>

    <?php if (empty($payoutMethods)): ?>
      <div style="background: rgba(245,158,11,0.08); border: 1px dashed rgba(245,158,11,0.3); border-radius: 10px; padding: 1.75rem; text-align: center; margin-bottom: 1.5rem;">
        <div style="font-size: 2rem; margin-bottom: 0.5rem;">⚠️</div>
        <h4 style="color: #fbbf24; font-size: 1.1rem; margin: 0 0 0.5rem 0;">Payout Method Required</h4>
        <p style="color: #cbd5e1; font-size: 0.88rem; line-height: 1.5; margin: 0 0 1.25rem 0;">
          To prevent unauthorized withdrawals and comply with banking settlement standards, you must link and verify at least one <strong>Bank Account, PayPal address, or Crypto USDC wallet</strong> before requesting a withdrawal.
        </p>
        <button type="button" onclick="closePayoutModal(); openAddPayoutMethodModal();" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #10b981, #38bdf8); font-weight: 800; padding: 0.6rem 1.4rem;">
          <i data-lucide="plus-circle"></i> Link Bank / Payout Method Now &rarr;
        </button>
      </div>
      <div class="admin-modal-footer">
        <button type="button" onclick="closePayoutModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
      </div>
    <?php else: ?>
      <form action="<?= url('/author/payouts/request') ?>" method="POST">
        <div style="background: #04070e; border: 1px solid rgba(16,185,129,0.25); border-radius: 10px; padding: 1.25rem; margin-bottom: 1.25rem; text-align: center;">
          <div style="font-size: 0.78rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">Withdrawable Balance</div>
          <div style="font-size: 2.2rem; font-weight: 800; color: #34d399; font-family: 'Outfit', sans-serif;">
            $<?= number_format((float)($author['promo_credits'] ?? 0), 2) ?> <span style="font-size: 0.95rem;">USD</span>
          </div>
          <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">Minimum threshold: $25.00 USD</div>
        </div>

        <div style="margin-bottom: 1rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
            <label class="admin-modal-label" style="margin: 0;">Verified Settlement Destination</label>
            <button type="button" onclick="closePayoutModal(); openAddPayoutMethodModal();" style="background: none; border: none; color: #38bdf8; font-size: 0.75rem; cursor: pointer; text-decoration: underline;">+ Link New</button>
          </div>
          <select name="payout_method_id" id="req_payout_method_id" class="admin-modal-select" onchange="calculatePayoutSummary()" required>
            <?php foreach ($payoutMethods as $pm): ?>
              <?php
              $mType = $pm['method_type'];
              $isBank = ($mType === 'bank_wire' || $mType === 'iban_sepa' || strpos($mType, 'bank') !== false);
              
              $methodPolicy = 'standard';
              if (!empty($allowedPayoutMethods)) {
                  foreach ($allowedPayoutMethods as $apm) {
                      if (($apm['key'] ?? '') === $mType) {
                          $methodPolicy = $apm['fee_policy'] ?? 'standard';
                          break;
                      }
                  }
              }
              if ($isBank) $methodPolicy = 'bank';

              $pFeePct = (float)($payoutFeeSettings['platform_fee_percent'] ?? 5.0);
              $bFeePct = (float)($payoutFeeSettings['bank_fee_percent'] ?? 2.5);
              $bSurcharge = (float)($payoutFeeSettings['bank_wire_surcharge'] ?? 5.0);

              $feeDesc = "Platform: {$pFeePct}%";
              if ($methodPolicy === 'bank') {
                  $feeDesc .= " + Bank: {$bFeePct}% + \${$bSurcharge}";
              } elseif ($methodPolicy === 'none') {
                  $feeDesc = "Fee: 0%";
              }

              $label = '';
              if ($mType === 'paypal') $label = "PayPal: " . ($pm['account_email'] ?: $user['email']) . " ({$feeDesc})";
              elseif ($isBank) $label = "Bank: " . ($pm['bank_name'] ?: 'Wire') . " (****" . substr($pm['account_number_or_iban'] ?? '0000', -4) . ") ({$feeDesc})";
              elseif ($mType === 'crypto_usdc') $label = "USDC (" . ($pm['crypto_network'] ?: 'ERC-20') . "): " . substr($pm['crypto_wallet_address'] ?? '', 0, 8) . "... ({$feeDesc})";
              else $label = strtoupper(str_replace('_', ' ', $mType)) . ": " . ($pm['account_email'] ?: ($pm['account_number_or_iban'] ?: $pm['account_holder_name'])) . " ({$feeDesc})";
              if (!empty($pm['is_default'])) $label .= " [DEFAULT]";
              ?>
              <option value="<?= $pm['id'] ?>" data-method-type="<?= htmlspecialchars($mType) ?>" data-fee-policy="<?= htmlspecialchars($methodPolicy) ?>" <?= !empty($pm['is_default']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($label) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div style="margin-bottom: 1rem;">
          <label class="admin-modal-label">Withdrawal Amount ($ USD)</label>
          <input type="number" name="amount" id="req_payout_amount" step="0.01" min="25" max="<?= (float)($author['promo_credits'] ?? 0) ?>" value="<?= min(100, max(25, (float)($author['promo_credits'] ?? 0))) ?>" class="admin-modal-input" required oninput="calculatePayoutSummary()">
        </div>

        <!-- Live Calculation Card with Tax & Bank Deductions -->
        <div style="background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 0.85rem 1rem; margin-bottom: 1.25rem; font-size: 0.82rem;">
          <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem; color: #94a3b8;">
            <span>Gross Claim:</span>
            <span id="pout_gross" style="color: #f8fafc; font-weight: 600;">$50.00</span>
          </div>
          <div id="pout_plat_row" style="display: flex; justify-content: space-between; margin-bottom: 0.35rem; color: #94a3b8;">
            <span>Platform Fee (<span id="pout_plat_pct"><?= (float)($payoutFeeSettings['platform_fee_percent'] ?? 5.0) ?>%</span>):</span>
            <span id="pout_tax" style="color: #f59e0b; font-weight: 600;">-$2.50</span>
          </div>
          <div id="pout_bank_tax_row" style="display: flex; justify-content: space-between; margin-bottom: 0.35rem; color: #94a3b8;">
            <span>Bank Remittance &amp; Wire (<span id="pout_bank_pct"><?= (float)($payoutFeeSettings['bank_fee_percent'] ?? 2.5) ?>%</span> + $<span id="pout_wire_fee"><?= number_format((float)($payoutFeeSettings['bank_wire_surcharge'] ?? 5.0), 2) ?></span>):</span>
            <span id="pout_bank_tax" style="color: #ef4444; font-weight: 600;">-$6.25</span>
          </div>
          <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem; color: #94a3b8; border-top: 1px dashed rgba(148,163,184,0.12); padding-top: 0.35rem;">
            <span>Total Deductions:</span>
            <span id="pout_fee" style="color: #ef4444; font-weight: 600;">-$8.75</span>
          </div>
          <div style="display: flex; justify-content: space-between; border-top: 1px solid rgba(148,163,184,0.1); padding-top: 0.35rem; font-weight: 700;">
            <span style="color: #f8fafc;">Net Payout to Receive:</span>
            <span id="pout_net" style="color: #34d399; font-size: 0.95rem;">$41.25</span>
          </div>
        </div>

        <div style="margin-bottom: 1.25rem;">
          <label class="admin-modal-label">Note for Finance Team (Optional)</label>
          <input type="text" name="notes" placeholder="e.g. Monthly creator royalty withdrawal" class="admin-modal-input">
        </div>

        <div class="admin-modal-footer">
          <button type="button" onclick="closePayoutModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
          <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #10b981; border-color: #10b981; font-weight: 700;">Submit Payout Claim 💵</button>
        </div>
      </form>
    <?php endif; ?>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: LINK NEW PAYOUT METHOD -->
<!-- ========================================================================= -->
<?php
$enabledPayoutMethods = array_values(array_filter($allowedPayoutMethods ?? [], fn($m) => !empty($m['enabled'])));
if (empty($enabledPayoutMethods)) {
    $enabledPayoutMethods = [
        ['key' => 'bank_wire', 'name' => 'Bank Wire / SEPA', 'icon' => '🏦', 'fee_policy' => 'bank', 'description' => 'Direct bank transfer'],
        ['key' => 'paypal', 'name' => 'PayPal', 'icon' => '🅿️', 'fee_policy' => 'standard', 'description' => 'Fast mass payout to PayPal address'],
        ['key' => 'crypto_usdc', 'name' => 'Crypto USDC', 'icon' => '🪙', 'fee_policy' => 'standard', 'description' => 'ERC-20, Solana or Polygon stablecoin']
    ];
}
$firstMethodKey = $enabledPayoutMethods[0]['key'] ?? 'bank_wire';
?>
<div id="addPayoutMethodModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-cyan" style="max-width: 560px;">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #38bdf8;"><i data-lucide="plus-circle"></i> Link New Payout Destination</h3>
      <button type="button" onclick="closeAddPayoutMethodModal()" class="admin-modal-close">✕</button>
    </div>

    <!-- Payout Method Tabs (Dynamic from Admin Revenue Settings) -->
    <div style="display: flex; gap: 0.4rem; margin-bottom: 1.25rem; border-bottom: 1px solid rgba(148,163,184,0.12); padding-bottom: 0.75rem; flex-wrap: wrap;">
      <?php foreach ($enabledPayoutMethods as $idx => $epm): ?>
        <button type="button" 
                onclick="switchPayoutMethodTab('<?= htmlspecialchars($epm['key']) ?>')" 
                id="pmtab_<?= htmlspecialchars($epm['key']) ?>" 
                data-method-name="<?= htmlspecialchars($epm['name']) ?>"
                data-fee-policy="<?= htmlspecialchars($epm['fee_policy'] ?? 'standard') ?>"
                class="ts-btn <?= $epm['key'] === $firstMethodKey ? 'ts-btn-primary' : 'ts-btn-outline' ?> ts-btn-sm" 
                style="font-size: 0.8rem; padding: 0.35rem 0.75rem;">
          <?= htmlspecialchars($epm['icon'] ?? '💳') ?> <?= htmlspecialchars($epm['name']) ?>
        </button>
      <?php endforeach; ?>
    </div>

    <form action="<?= url('/author/payout-methods/save') ?>" method="POST" id="addPayoutMethodForm">
      <input type="hidden" name="method_type" id="apm_method_type" value="<?= htmlspecialchars($firstMethodKey) ?>">

      <!-- 1. BANK WIRE / SEPA -->
      <div id="apm_pane_bank" style="<?= ($firstMethodKey === 'bank_wire' || $firstMethodKey === 'iban_sepa') ? 'display: block;' : 'display: none;' ?>">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
          <div>
            <label class="admin-modal-label">Bank Name <span style="color: #ef4444;">*</span></label>
            <input type="text" name="bank_name" placeholder="e.g. Chase Bank, Barclays, HBL" class="admin-modal-input">
          </div>
          <div>
            <label class="admin-modal-label">Account Holder Full Name <span style="color: #ef4444;">*</span></label>
            <input type="text" name="account_holder_name" value="<?= htmlspecialchars($author['display_name']) ?>" placeholder="e.g. Alex Vance" class="admin-modal-input">
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
          <div>
            <label class="admin-modal-label">Account Number / IBAN <span style="color: #ef4444;">*</span></label>
            <input type="text" name="account_number_or_iban" placeholder="e.g. GB29NWBK60161331926819" class="admin-modal-input" style="font-family: monospace;">
          </div>
          <div>
            <label class="admin-modal-label">Routing / SWIFT / BIC Code</label>
            <input type="text" name="routing_or_swift" placeholder="e.g. CHASUS33" class="admin-modal-input" style="font-family: monospace;">
          </div>
        </div>
      </div>

      <!-- 2. PAYPAL -->
      <div id="apm_pane_paypal" style="<?= ($firstMethodKey === 'paypal') ? 'display: block;' : 'display: none;' ?>; margin-bottom: 1rem;">
        <label class="admin-modal-label">PayPal Account Email <span style="color: #ef4444;">*</span></label>
        <input type="email" name="account_email" value="<?= htmlspecialchars($user['email']) ?>" placeholder="paypal@example.com" class="admin-modal-input">
        <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.35rem;">Payments will be disbursed directly via PayPal Mass Payments.</div>
      </div>

      <!-- 3. CRYPTO USDC -->
      <div id="apm_pane_crypto" style="<?= ($firstMethodKey === 'crypto_usdc') ? 'display: block;' : 'display: none;' ?>; margin-bottom: 1rem;">
        <div style="margin-bottom: 1rem;">
          <label class="admin-modal-label">USDC / USDT Wallet Address <span style="color: #ef4444;">*</span></label>
          <input type="text" name="crypto_wallet_address" placeholder="0x71C... or Solana address" class="admin-modal-input" style="font-family: monospace;">
        </div>
        <div>
          <label class="admin-modal-label">Blockchain Network <span style="color: #ef4444;">*</span></label>
          <select name="crypto_network" class="admin-modal-select">
            <option value="ERC-20 (Ethereum)">Ethereum (ERC-20)</option>
            <option value="Solana (SPL)">Solana (SPL)</option>
            <option value="Polygon (MATIC)">Polygon (MATIC)</option>
            <option value="Arbitrum One">Arbitrum One</option>
          </select>
        </div>
      </div>

      <!-- 4. CUSTOM OR ALTERNATIVE METHOD PANE (ADMIN CONFIGURED) -->
      <div id="apm_pane_custom" style="<?= (!in_array($firstMethodKey, ['bank_wire', 'iban_sepa', 'paypal', 'crypto_usdc'])) ? 'display: block;' : 'display: none;' ?>; margin-bottom: 1rem;">
        <div style="background: rgba(56,189,248,0.06); border: 1px dashed rgba(56,189,248,0.25); border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 1rem;">
          <div style="font-weight: 700; color: #38bdf8; font-size: 0.88rem;" id="apm_custom_method_title">Custom Payout Method</div>
          <div style="font-size: 0.76rem; color: #94a3b8; margin-top: 0.2rem;" id="apm_custom_method_desc">Enter your account or identifier details for this disbursement method.</div>
        </div>

        <div style="margin-bottom: 1rem;">
          <label class="admin-modal-label">Beneficiary Full Name <span style="color: #ef4444;">*</span></label>
          <input type="text" name="account_holder_name_custom" id="apm_custom_holder" value="<?= htmlspecialchars($author['display_name']) ?>" class="admin-modal-input" placeholder="e.g. Alex Vance">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
          <div>
            <label class="admin-modal-label">Account / Wallet / User ID <span style="color: #ef4444;">*</span></label>
            <input type="text" name="account_number_custom" id="apm_custom_acc" placeholder="e.g. Account Number or Tag" class="admin-modal-input" style="font-family: monospace;">
          </div>
          <div>
            <label class="admin-modal-label">Email or Phone Identifier</label>
            <input type="text" name="account_email_custom" id="apm_custom_email" placeholder="e.g. user@domain.com" class="admin-modal-input">
          </div>
        </div>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: #cbd5e1; font-size: 0.82rem;">
          <input type="checkbox" name="is_default" value="1" checked style="accent-color: #38bdf8;">
          Set as Default Payout Destination
        </label>
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="closeAddPayoutMethodModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #38bdf8; border-color: #38bdf8; color: #000; font-weight: 700;">
          Save &amp; Link Payout Method ✓
        </button>
      </div>
    </form>
  </div>
</div>



<script>
function switchAuthorMainTab(tab) {
  document.querySelectorAll('[id^="amaintab_"]').forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });
  const btn = document.getElementById('amaintab_' + tab);
  if (btn) {
    btn.classList.remove('ts-btn-outline');
    btn.classList.add('ts-btn-primary');
  }

  const panes = ['publications', 'prompts', 'payouts', 'tipjar', 'ambassador', 'collaborations', 'analytics', 'feedback', 'community', 'security'];
  panes.forEach(p => {
    const el = document.getElementById('amainpane_' + p);
    if (el) el.style.display = (tab === p) ? 'block' : 'none';
  });

  if (window.lucide) lucide.createIcons();
}

// Auto-switch to tab if present in URL
document.addEventListener('DOMContentLoaded', () => {
  const urlParams = new URLSearchParams(window.location.search);
  const targetTab = urlParams.get('tab');
  const validPanes = ['publications', 'prompts', 'payouts', 'tipjar', 'ambassador', 'collaborations', 'analytics', 'feedback', 'community', 'security'];
  if (targetTab && validPanes.includes(targetTab)) {
    switchAuthorMainTab(targetTab);
  }
});

function filterAuthorPrompts(type) {
  const btns = ['all', 'text', 'image'];
  btns.forEach(b => {
    const el = document.getElementById('apfilter_' + b);
    if (el) {
      if (b === type) {
        el.classList.remove('ts-btn-outline');
        el.classList.add('ts-btn-primary');
      } else {
        el.classList.remove('ts-btn-primary');
        el.classList.add('ts-btn-outline');
      }
    }
  });

  const cards = document.querySelectorAll('.author-prompt-card');
  cards.forEach(card => {
    const pType = card.getAttribute('data-prompt-type');
    if (type === 'all' || pType === type) {
      card.style.display = 'flex';
    } else {
      card.style.display = 'none';
    }
  });
}

function copyAuthorPromptTemplate(text) {
  if (!text) return;
  navigator.clipboard.writeText(text).then(() => {
    if (typeof showToast === 'function') {
      showToast('Prompt blueprint copied to clipboard! 📋', 'success');
    } else {
      alert('Prompt blueprint copied to clipboard! 📋');
    }
  }).catch(() => {
    alert('Prompt copied!');
  });
}

function copySnippet(elementId) {
  const input = document.getElementById(elementId);
  if (!input) return;
  input.select();
  navigator.clipboard.writeText(input.value).then(() => {
    if (typeof showToast === 'function') {
      showToast('Markdown snippet copied to clipboard! 📋', 'success');
    } else {
      alert('Markdown snippet copied to clipboard! 📋');
    }
  });
}

function copyAmbassadorLink() {
  const input = document.getElementById('ambassadorLinkInput');
  if (input) {
    input.select();
    navigator.clipboard.writeText(input.value);
    if (typeof showToast === 'function') {
      showToast('Ambassador invite link copied to clipboard! 📋', 'success');
    } else {
      alert('Ambassador invite link copied to clipboard! 📋');
    }
  }
}

function openPasswordModal() {
  const modal = document.getElementById('changePasswordModal');
  if (modal) {
    modal.style.display = 'flex';
    const cur = document.getElementById('cp_current');
    if (cur) cur.focus();
  }
}
function closePasswordModal() {
  const modal = document.getElementById('changePasswordModal');
  if (modal) modal.style.display = 'none';
}
function togglePassVisibility(id) {
  const input = document.getElementById(id);
  if (input) {
    input.type = (input.type === 'password') ? 'text' : 'password';
  }
}
function checkPassStrength(val) {
  const bar = document.getElementById('passStrengthBar');
  const txt = document.getElementById('passStrengthText');
  if (!bar || !txt) return;
  let score = 0;
  if (val.length >= 8) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;

  if (score === 0) {
    bar.style.width = '0%';
    txt.innerText = 'Strength: Minimum 8 characters';
    txt.style.color = '#64748b';
  } else if (score <= 2) {
    bar.style.width = '40%';
    bar.style.background = '#ef4444';
    txt.innerText = 'Strength: Weak (Add numbers & symbols)';
    txt.style.color = '#ef4444';
  } else if (score === 3) {
    bar.style.width = '70%';
    bar.style.background = '#f59e0b';
    txt.innerText = 'Strength: Good';
    txt.style.color = '#f59e0b';
  } else {
    bar.style.width = '100%';
    bar.style.background = '#10b981';
    txt.innerText = 'Strength: Strong 🛡️';
    txt.style.color = '#10b981';
  }
}

function openProfileModal() {
  document.getElementById('profileModal').style.display = 'flex';
}
function closeProfileModal() {
  document.getElementById('profileModal').style.display = 'none';
}

function switchProfTab(tab) {
  document.querySelectorAll('[id^="ptab_"]').forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });
  const btn = document.getElementById('ptab_' + tab);
  if (btn) {
    btn.classList.remove('ts-btn-outline');
    btn.classList.add('ts-btn-primary');
  }

  const identitySec = document.getElementById('psec_identity');
  const socialsSec = document.getElementById('psec_socials');
  const cryptoSec = document.getElementById('psec_crypto');
  if (identitySec) identitySec.style.display = (tab === 'identity') ? 'block' : 'none';
  if (socialsSec) socialsSec.style.display = (tab === 'socials') ? 'block' : 'none';
  if (cryptoSec) cryptoSec.style.display = (tab === 'crypto') ? 'block' : 'none';
}

function openCryptoWalletModal(focusCoin) {
  const modal = document.getElementById('cryptoWalletModal');
  if (modal) {
    modal.style.display = 'flex';
    if (focusCoin) {
      const el = document.getElementById('q_tip_wallet_' + focusCoin);
      if (el) setTimeout(() => el.focus(), 100);
    }
  }
}

function closeCryptoWalletModal() {
  const modal = document.getElementById('cryptoWalletModal');
  if (modal) modal.style.display = 'none';
  const fb = document.getElementById('quickWalletFeedback');
  if (fb) fb.style.display = 'none';
}

function updateTipSimulator(val) {
  const amount = parseFloat(val) || 0;
  const net = (amount * 0.95).toFixed(2);
  const fee = (amount * 0.05).toFixed(2);
  const netEl = document.getElementById('tipSimNet');
  const feeEl = document.getElementById('tipSimFee');
  if (netEl) netEl.innerText = 'You Receive (95%): $' + net;
  if (feeEl) feeEl.innerText = 'Platform Treasury (5%): $' + fee;
}

async function saveCryptoWalletsAjax(e) {
  e.preventDefault();
  const form = document.getElementById('quickCryptoWalletForm');
  const btn = document.getElementById('btnSaveCryptoWallets');
  const fb = document.getElementById('quickWalletFeedback');
  if (!form || !btn) return;

  btn.disabled = true;
  btn.innerText = 'Saving Wallets... ⏳';
  if (fb) fb.style.display = 'none';

  const formData = new FormData(form);

  try {
    const res = await fetch('<?= url("/author/tip-wallets/save") ?>', {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    const data = await res.json();
    if (data.success) {
      if (fb) {
        fb.style.display = 'block';
        fb.style.background = 'rgba(16,185,129,0.15)';
        fb.style.color = '#34d399';
        fb.style.border = '1px solid rgba(16,185,129,0.3)';
        fb.innerText = data.message || 'Saved successfully!';
      }
      showToast('Crypto tip jar payout wallets saved!', 'success');
      setTimeout(() => {
        window.location.href = window.location.pathname + '?tab=tipjar';
      }, 750);
    } else {
      if (fb) {
        fb.style.display = 'block';
        fb.style.background = 'rgba(239,68,68,0.15)';
        fb.style.color = '#f87171';
        fb.style.border = '1px solid rgba(239,68,68,0.3)';
        fb.innerText = data.error || 'Failed to save wallets.';
      }
    }
  } catch (err) {
    if (fb) {
      fb.style.display = 'block';
      fb.style.background = 'rgba(239,68,68,0.15)';
      fb.style.color = '#f87171';
      fb.style.border = '1px solid rgba(239,68,68,0.3)';
      fb.innerText = 'Network connection error while saving.';
    }
  } finally {
    btn.disabled = false;
    btn.innerText = 'Save Crypto Wallets ✓';
  }
}

const AVAILABLE_SOCIAL_PLATFORMS = <?= json_encode(\App\Core\SocialHelper::getAvailablePlatforms(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
window.tsPlatformFeePct = <?= (float)($payoutFeeSettings['platform_fee_percent'] ?? 5.0) ?>;
window.tsBankFeePct = <?= (float)($payoutFeeSettings['bank_fee_percent'] ?? 2.5) ?>;
window.tsBankSurcharge = <?= (float)($payoutFeeSettings['bank_wire_surcharge'] ?? 5.0) ?>;

function addSocialRow() {
  const container = document.getElementById('socialLinksContainer');
  if (!container) return;

  const div = document.createElement('div');
  div.className = 'social-link-row';
  div.style.cssText = 'display: flex; gap: 0.65rem; align-items: center; background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.18); padding: 0.65rem; border-radius: 8px; flex-wrap: wrap; animation: tsDropdownFadeIn 0.2s ease;';

  let optionsHtml = '';
  for (const [key, meta] of Object.entries(AVAILABLE_SOCIAL_PLATFORMS)) {
    optionsHtml += `<option value="${key}">${meta.label}</option>`;
  }

  div.innerHTML = `
    <select name="social_platforms[]" class="admin-modal-select" onchange="updateSocialRowPlaceholder(this)" style="width: 175px; flex-shrink: 0; padding: 0.45rem 0.65rem; font-size: 0.85rem;">
      ${optionsHtml}
    </select>
    <input type="text" name="social_urls[]" placeholder="https://github.com/username" class="admin-modal-input" style="flex: 1; min-width: 200px;" required>
    <input type="text" name="social_labels[]" placeholder="Label (Optional)" class="admin-modal-input" style="width: 150px;">
    <button type="button" onclick="removeSocialRow(this)" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #ef4444; border-color: rgba(239,68,68,0.3); padding: 0.45rem 0.65rem;" title="Remove this link">✕</button>
  `;

  container.appendChild(div);
}

function removeSocialRow(btn) {
  const row = btn.closest('.social-link-row');
  if (row) row.remove();
}

function updateSocialRowPlaceholder(select) {
  const row = select.closest('.social-link-row');
  if (!row) return;
  const urlInput = row.querySelector('input[name="social_urls[]"]');
  const platform = select.value;
  const meta = AVAILABLE_SOCIAL_PLATFORMS[platform];
  if (urlInput && meta && meta.placeholder) {
    urlInput.placeholder = meta.placeholder;
  }
}

function openPayoutModal() {
  document.getElementById('payoutModal').style.display = 'flex';
  calculatePayoutSummary();
}
function closePayoutModal() {
  document.getElementById('payoutModal').style.display = 'none';
}

function openAddPayoutMethodModal() {
  document.getElementById('addPayoutMethodModal').style.display = 'flex';
  switchPayoutMethodTab('<?= htmlspecialchars($firstMethodKey) ?>');
}
function closeAddPayoutMethodModal() {
  document.getElementById('addPayoutMethodModal').style.display = 'none';
}

function switchPayoutMethodTab(key) {
  document.querySelectorAll('[id^="pmtab_"]').forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });
  const activeBtn = document.getElementById('pmtab_' + key);
  if (activeBtn) {
    activeBtn.classList.remove('ts-btn-outline');
    activeBtn.classList.add('ts-btn-primary');
  }

  const hiddenType = document.getElementById('apm_method_type');
  if (hiddenType) hiddenType.value = key;

  const paneBank = document.getElementById('apm_pane_bank');
  const panePaypal = document.getElementById('apm_pane_paypal');
  const paneCrypto = document.getElementById('apm_pane_crypto');
  const paneCustom = document.getElementById('apm_pane_custom');

  if (paneBank) paneBank.style.display = 'none';
  if (panePaypal) panePaypal.style.display = 'none';
  if (paneCrypto) paneCrypto.style.display = 'none';
  if (paneCustom) paneCustom.style.display = 'none';

  if (key === 'bank_wire' || key === 'iban_sepa') {
    if (paneBank) paneBank.style.display = 'block';
  } else if (key === 'paypal') {
    if (panePaypal) panePaypal.style.display = 'block';
  } else if (key === 'crypto_usdc') {
    if (paneCrypto) paneCrypto.style.display = 'block';
  } else {
    if (paneCustom) {
      paneCustom.style.display = 'block';
      const customTitle = document.getElementById('apm_custom_method_title');
      const customDesc = document.getElementById('apm_custom_method_desc');
      if (customTitle && activeBtn) {
        customTitle.innerText = activeBtn.innerText.trim();
      }
      if (customDesc && activeBtn) {
        const policy = activeBtn.getAttribute('data-fee-policy') || 'standard';
        if (policy === 'bank') {
          customDesc.innerText = `Bank routing fee policy applies (${window.tsBankFeePct}% + $${window.tsBankSurcharge.toFixed(2)} wire charge).`;
        } else if (policy === 'none') {
          customDesc.innerText = `Fee-exempt zero commission disbursement policy applies.`;
        } else {
          customDesc.innerText = `Standard platform fee applies (${window.tsPlatformFeePct}%).`;
        }
      }
    }
  }
}

function calculatePayoutSummary() {
  const amtInput = document.getElementById('req_payout_amount');
  const selInput = document.getElementById('req_payout_method_id');
  if (!amtInput) return;

  const amt = parseFloat(amtInput.value) || 0;
  const platPct = (typeof window.tsPlatformFeePct === 'number') ? window.tsPlatformFeePct : 5.0;
  const bankPct = (typeof window.tsBankFeePct === 'number') ? window.tsBankFeePct : 2.5;
  const bankSurcharge = (typeof window.tsBankSurcharge === 'number') ? window.tsBankSurcharge : 5.0;

  let feePolicy = 'standard';
  if (selInput && selInput.options[selInput.selectedIndex]) {
    feePolicy = selInput.options[selInput.selectedIndex].getAttribute('data-fee-policy') || 'standard';
    const mType = selInput.options[selInput.selectedIndex].getAttribute('data-method-type') || '';
    if (mType === 'bank_wire' || mType === 'iban_sepa' || mType.includes('bank')) {
      feePolicy = 'bank';
    }
  }

  let platformTax = 0.00;
  if (feePolicy !== 'none') {
    platformTax = Math.round(amt * (platPct / 100) * 100) / 100;
  }

  let bankTax = 0.00;
  let bankFee = 0.00;
  if (feePolicy === 'bank') {
    bankTax = Math.round(amt * (bankPct / 100) * 100) / 100;
    bankFee = bankSurcharge;
  }

  const totalDeductions = Math.round((platformTax + bankTax + bankFee) * 100) / 100;
  const net = Math.max(0, Math.round((amt - totalDeductions) * 100) / 100);

  const gEl = document.getElementById('pout_gross');
  const taxEl = document.getElementById('pout_tax');
  const platRow = document.getElementById('pout_plat_row');
  const bTaxEl = document.getElementById('pout_bank_tax');
  const bTaxRow = document.getElementById('pout_bank_tax_row');
  const fEl = document.getElementById('pout_fee');
  const nEl = document.getElementById('pout_net');

  if (gEl) gEl.innerText = '$' + amt.toFixed(2);
  if (taxEl) taxEl.innerText = '-$' + platformTax.toFixed(2) + ' (' + platPct + '%)';
  if (platRow) platRow.style.display = (feePolicy !== 'none') ? 'flex' : 'none';
  if (bTaxEl) bTaxEl.innerText = '-$' + (bankTax + bankFee).toFixed(2);
  if (bTaxRow) bTaxRow.style.display = (feePolicy === 'bank') ? 'flex' : 'none';
  if (fEl) fEl.innerText = '-$' + totalDeductions.toFixed(2);
  if (nEl) nEl.innerText = '$' + net.toFixed(2);
}


function filterByStatus(status) {
  document.querySelectorAll('.author-status-btn').forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });
  const activeBtn = document.getElementById(`sbtn-${status}`);
  if (activeBtn) {
    activeBtn.classList.remove('ts-btn-outline');
    activeBtn.classList.add('ts-btn-primary');
  }

  const cards = document.querySelectorAll('.author-content-card');
  cards.forEach(c => {
    const cardStatus = c.getAttribute('data-status');
    if (status === 'all' || cardStatus === status) {
      c.style.display = 'flex';
    } else {
      c.style.display = 'none';
    }
  });
}

function previewAndValidateSquareAvatar(input) {
  if (!input.files || !input.files[0]) return;
  const file = input.files[0];
  const feedback = document.getElementById('avatar_ratio_feedback');
  const previewImg = document.getElementById('avatar_preview_img');

  const img = new Image();
  const objectUrl = URL.createObjectURL(file);

  img.onload = function() {
    const w = this.width;
    const h = this.height;
    const ratio = w / h;

    if (ratio < 0.90 || ratio > 1.10) {
      feedback.innerHTML = `<span style="color: #ef4444; font-weight: 700;">⚠️ Rejected: Non-square image (${w}x${h} px). Please select a 1:1 square image.</span>`;
      input.value = '';
      URL.revokeObjectURL(objectUrl);
    } else {
      feedback.innerHTML = `<span style="color: #10b981; font-weight: 700;">✓ Perfect 1:1 square ratio verified (${w}x${h} px). Ready to save!</span>`;
      previewImg.src = objectUrl;
    }
  };

  img.src = objectUrl;
}

function previewCoverBanner(input) {
  if (!input.files || !input.files[0]) return;
  const file = input.files[0];
  document.getElementById('cover_filename_display').innerText = 'Selected: ' + file.name;
}

async function handleMonetizationApplySubmit(e) {
  e.preventDefault();
  const btn = document.getElementById('monetizeApplyBtn');
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = 'Submitting Application... ⏳';
    btn.style.opacity = '0.7';
    btn.style.cursor = 'not-allowed';
  }

  try {
    const res = await fetch('<?= url('/author/monetization/apply') ?>', {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    const data = await res.json();
    if (data.success) {
      if (typeof showToast === 'function') {
        showToast(data.message || 'Application submitted successfully! 🚀', 'success');
      } else {
        alert(data.message || 'Application submitted successfully! 🚀');
      }
      setTimeout(() => { location.reload(); }, 800);
    } else {
      alert(data.error || 'Failed to submit application.');
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = '🚀 Apply for Partner Monetization';
        btn.style.opacity = '1';
        btn.style.cursor = 'pointer';
      }
    }
  } catch (err) {
    document.getElementById('monetizeApplyForm').submit();
  }
}

function openPayoutAuditModal(p) {
  if (typeof p === 'string') {
    try { p = JSON.parse(p); } catch(e) { return; }
  }
  if (!p) return;

  const idBadge = document.getElementById('pa_id_badge');
  if (idBadge) idBadge.innerText = 'PAYOUT DOSSIER #' + (p.id || '');
  
  const stBadge = document.getElementById('pa_status_badge');
  const st = (p.status || 'pending').toLowerCase();
  
  if (stBadge) {
    stBadge.innerText = st.toUpperCase();
    if (st === 'paid') {
      stBadge.className = 'ts-badge ts-badge-green';
      stBadge.style.cssText = '';
    } else if (st === 'rejected') {
      stBadge.className = 'strike-badge';
      stBadge.style.cssText = 'background: rgba(239,68,68,0.2); color: #ef4444; border: 1px solid rgba(239,68,68,0.4); font-weight: 700;';
    } else if (st === 'processing') {
      stBadge.className = 'ts-badge ts-badge-cyan';
      stBadge.style.cssText = '';
    } else {
      stBadge.className = 'ts-badge ts-badge-amber';
      stBadge.style.cssText = '';
    }
  }

  const amt = parseFloat(p.amount_usd || 0).toFixed(2);
  const fee = parseFloat(p.fee_deducted || 0).toFixed(2);
  const net = (p.net_amount && parseFloat(p.net_amount) > 0) ? parseFloat(p.net_amount).toFixed(2) : (parseFloat(amt) - parseFloat(fee)).toFixed(2);

  const grossEl = document.getElementById('pa_amount_gross');
  if (grossEl) grossEl.innerText = '$' + amt;
  const feeEl = document.getElementById('pa_amount_fee');
  if (feeEl) feeEl.innerText = '$' + fee;
  const netEl = document.getElementById('pa_amount_net');
  if (netEl) netEl.innerText = '$' + net;

  const methodEl = document.getElementById('pa_method_name');
  if (methodEl) methodEl.innerText = (p.payout_method || 'BANK WIRE').toUpperCase();
  const addressEl = document.getElementById('pa_address_text');
  if (addressEl) addressEl.innerText = p.payout_address || 'No destination address registered';

  const dateReqEl = document.getElementById('pa_date_requested');
  if (dateReqEl) dateReqEl.innerText = p.created_at || '—';
  const dateProcEl = document.getElementById('pa_date_processed');
  if (dateProcEl) dateProcEl.innerText = p.processed_at || (st === 'pending' ? 'Pending in review queue' : '—');
  const txRefEl = document.getElementById('pa_tx_ref');
  if (txRefEl) txRefEl.innerText = (st === 'rejected' ? 'REFUNDED TO ROYALTY BALANCE ✓' : (p.transaction_reference || 'Pending Processing'));

  const receiptRow = document.getElementById('pa_receipt_row');
  const receiptLink = document.getElementById('pa_receipt_link');
  if (p.receipt_url && receiptLink && receiptRow) {
    const base = window.TS_BASE_URL || '';
    receiptLink.href = base + p.receipt_url;
    receiptRow.style.display = 'table-row';
  } else if (receiptRow) {
    receiptRow.style.display = 'none';
  }

  const rejBanner = document.getElementById('pa_rejected_banner');
  const rejHelp = document.getElementById('pa_rejected_help');
  const rejActions = document.getElementById('pa_rejected_actions');
  const rejReason = document.getElementById('pa_rejection_reason');
  const refundAmt = document.getElementById('pa_refund_amount');

  if (st === 'rejected') {
    if (rejBanner) rejBanner.style.display = 'block';
    if (rejHelp) rejHelp.style.display = 'block';
    if (rejActions) rejActions.style.display = 'flex';
    if (refundAmt) refundAmt.innerText = '$' + amt + ' USD';
    if (rejReason) {
      rejReason.innerHTML = p.admin_notes 
        ? escapeHtml(p.admin_notes).replace(/\n/g, '<br>') 
        : 'Administrative review indicated destination details (IBAN/SWIFT/PayPal account) could not be verified or were rejected by the destination banking gateway.';
    }
  } else {
    if (rejBanner) rejBanner.style.display = 'none';
    if (rejHelp) rejHelp.style.display = 'none';
    if (rejActions) rejActions.style.display = 'none';
  }

  const modal = document.getElementById('payoutAuditModal');
  if (modal) modal.style.display = 'flex';
  if (window.lucide) lucide.createIcons();
}

function closePayoutAuditModal() {
  const modal = document.getElementById('payoutAuditModal');
  if (modal) modal.style.display = 'none';
}

function escapeHtml(str) {
  if (str === null || str === undefined) return '';
  return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}

document.addEventListener('DOMContentLoaded', () => {
  const ctx7d = document.getElementById('author7dChart');
  if (ctx7d && typeof Chart !== 'undefined') {
    const data7d = <?= json_encode($authorDailyViews ?? []) ?>;
    const labels = Object.keys(data7d).map(d => d.substring(5));
    const values = Object.values(data7d);

    new Chart(ctx7d, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Reads',
          data: values,
          backgroundColor: '#38bdf8',
          borderRadius: 6,
          borderSkipped: false
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { display: false }, ticks: { color: '#64748b', font: { size: 10 } } },
          y: { beginAtZero: true, grid: { color: 'rgba(148, 163, 184, 0.08)' }, ticks: { color: '#64748b', font: { size: 10 }, precision: 0 } }
        }
      }
    });
  }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

