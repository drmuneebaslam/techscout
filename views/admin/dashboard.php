<?php 
$title = $title ?? 'Admin Command Center & Operations HUD | TechScout';
require __DIR__ . '/../layouts/admin_header.php'; 
?>

<!-- Ops Command Master Banner -->
<div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; flex-wrap: wrap; gap: 1.5rem;">
  <div>
    <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.4rem; flex-wrap: wrap;">
      <span class="ts-badge ts-badge-pink" style="font-size: 0.72rem; letter-spacing: 0.05em; font-weight: 700;">COMMAND HUD v2.5</span>
      <span class="ts-badge ts-badge-green" style="font-size: 0.72rem;">ALL SYSTEMS OPERATIONAL</span>
      <?php if (!empty($stats['unresolved_errors']) && $stats['unresolved_errors'] > 0): ?>
        <a href="<?= url('/admin/system-logs') ?>" class="ts-badge" style="background: rgba(239,68,68,0.2); color: #f87171; border-color: rgba(239,68,68,0.4); text-decoration: none;">
          ⚠️ <?= $stats['unresolved_errors'] ?> Error Logs
        </a>
      <?php endif; ?>
    </div>
    <h1 style="font-size: 2.25rem; font-weight: 800; color: #f8fafc; margin: 0; line-height: 1.2; font-family: 'Outfit', sans-serif;">
      Operations Command &amp; Control Center
    </h1>
    <p style="color: #64748b; font-size: 0.95rem; margin-top: 0.35rem;">
      Unified telemetry, editorial pipeline queues, creator payouts, and system diagnostics.
    </p>
  </div>

  <!-- Quick Action Switchboard -->
  <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
    <a href="<?= url('/admin/newsletters') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3);">
      <i data-lucide="send" style="width: 13px;"></i> Dispatch Mail 🚀
    </a>
    <a href="<?= url('/admin/system-logs') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #f472b6; border-color: rgba(236,72,153,0.3);">
      <i data-lucide="shield-alert" style="width: 13px;"></i> Error Forensics 🛡️
    </a>
    <a href="<?= url('/admin/system/backup') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="border-color: rgba(16,185,129,0.3); color: #34d399;">
      <i data-lucide="database" style="width: 13px;"></i> Backup DB 💾
    </a>
    <div style="position: relative; display: inline-block;">
      <button type="button" onclick="toggleQuickAddMenu()" id="btnQuickAdd" class="ts-btn ts-btn-primary ts-btn-sm" style="font-weight: 700;">
        <i data-lucide="plus-circle" style="width: 14px;"></i> Quick Publish ▾
      </button>
      <div id="quickAddMenu" style="display: none; position: absolute; right: 0; top: 110%; background: #0f172a; border: 1px solid rgba(148,163,184,0.2); border-radius: 10px; padding: 0.5rem; width: 190px; box-shadow: 0 10px 30px rgba(0,0,0,0.6); z-index: 9999;">
        <a href="<?= url('/admin/articles') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; color: #f8fafc; text-decoration: none; font-size: 0.82rem; border-radius: 6px;" onmouseover="this.style.background='rgba(56,189,248,0.1)'" onmouseout="this.style.background='transparent'">
          📝 New Article
        </a>
        <a href="<?= url('/admin/tools') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; color: #f8fafc; text-decoration: none; font-size: 0.82rem; border-radius: 6px;" onmouseover="this.style.background='rgba(56,189,248,0.1)'" onmouseout="this.style.background='transparent'">
          🛠️ New AI Tool
        </a>
        <a href="<?= url('/admin/prompts') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; color: #f8fafc; text-decoration: none; font-size: 0.82rem; border-radius: 6px;" onmouseover="this.style.background='rgba(56,189,248,0.1)'" onmouseout="this.style.background='transparent'">
          ⚡ New Prompt Blueprint
        </a>
        <a href="<?= url('/admin/gpts') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; color: #f8fafc; text-decoration: none; font-size: 0.82rem; border-radius: 6px;" onmouseover="this.style.background='rgba(56,189,248,0.1)'" onmouseout="this.style.background='transparent'">
          🤖 New Custom GPT
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Server Health & Live Telemetry HUD -->
<div class="server-health-hud" style="margin-bottom: 2rem;">
  <div class="health-metric">
    <div style="display: flex; justify-content: space-between; font-size: 0.82rem; font-weight: 600;">
      <span style="color: #94a3b8;"><i data-lucide="activity" style="width: 13px; display: inline; vertical-align: middle;"></i> Server Engine</span>
      <strong style="color: #34d399;">PHP <?= PHP_VERSION ?> &bull; Optimal</strong>
    </div>
    <div class="health-bar-track">
      <div class="health-bar-fill" style="width: 22%; background: #10b981;"></div>
    </div>
  </div>

  <div class="health-metric">
    <div style="display: flex; justify-content: space-between; font-size: 0.82rem; font-weight: 600;">
      <span style="color: #94a3b8;"><i data-lucide="cpu" style="width: 13px; display: inline; vertical-align: middle;"></i> PHP Memory / Peak</span>
      <strong style="color: #38bdf8;"><?= round(memory_get_usage(true) / 1024 / 1024, 1) ?> MB Active</strong>
    </div>
    <div class="health-bar-track">
      <div class="health-bar-fill" style="width: 28%; background: #38bdf8;"></div>
    </div>
  </div>

  <div class="health-metric">
    <div style="display: flex; justify-content: space-between; font-size: 0.82rem; font-weight: 600;">
      <span style="color: #94a3b8;"><i data-lucide="database" style="width: 13px; display: inline; vertical-align: middle;"></i> MySQL Database Engine</span>
      <strong style="color: #818cf8;">InnoDB &bull; Connected</strong>
    </div>
    <div class="health-bar-track">
      <div class="health-bar-fill" style="width: 45%; background: #6366f1;"></div>
    </div>
  </div>

  <div class="health-metric">
    <div style="display: flex; justify-content: space-between; font-size: 0.82rem; font-weight: 600;">
      <span style="color: #94a3b8;"><i data-lucide="mail" style="width: 13px; display: inline; vertical-align: middle;"></i> Active Subscribers</span>
      <strong style="color: #ec4899;"><?= number_format((int)($stats['active_subscribers'] ?? 0)) ?> Active</strong>
    </div>
    <div class="health-bar-track">
      <div class="health-bar-fill" style="width: 65%; background: #ec4899;"></div>
    </div>
  </div>
</div>

<!-- 8-Metric Primary Operations KPI Matrix -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
  <div class="admin-stat-card" style="border-top: 3px solid #6366f1;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
      <div class="admin-stat-label">Registered Members</div>
      <i data-lucide="users" style="width: 18px; color: #818cf8;"></i>
    </div>
    <div class="admin-stat-val" style="font-family: var(--admin-font-mono); font-size: 1.75rem;"><?= number_format($stats['total_users']) ?></div>
    <div style="font-size: 0.72rem; color: #34d399; margin-top: 0.2rem;">Verified community members</div>
  </div>

  <div class="admin-stat-card" style="border-top: 3px solid #38bdf8;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
      <div class="admin-stat-label">Published Articles</div>
      <i data-lucide="book-open" style="width: 18px; color: #38bdf8;"></i>
    </div>
    <div class="admin-stat-val" style="font-family: var(--admin-font-mono); font-size: 1.75rem; color: #38bdf8;"><?= number_format($stats['total_articles']) ?></div>
    <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 0.2rem;">Architectural guides</div>
  </div>

  <div class="admin-stat-card" style="border-top: 3px solid #a855f7;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
      <div class="admin-stat-label">Tools &amp; Prompts</div>
      <i data-lucide="cpu" style="width: 18px; color: #c084fc;"></i>
    </div>
    <div class="admin-stat-val" style="font-family: var(--admin-font-mono); font-size: 1.75rem; color: #c084fc;"><?= number_format($stats['total_tools'] + $stats['total_prompts'] + $stats['total_gpts']) ?></div>
    <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 0.2rem;">Directory blueprints</div>
  </div>

  <div class="admin-stat-card" style="border-top: 3px solid #10b981;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
      <div class="admin-stat-label">Platform Reads</div>
      <i data-lucide="eye" style="width: 18px; color: #10b981;"></i>
    </div>
    <div class="admin-stat-val" style="font-family: var(--admin-font-mono); font-size: 1.75rem; color: #10b981;"><?= number_format($stats['total_views']) ?></div>
    <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.2rem;">Excludes admin/self visits</div>
  </div>

  <div class="admin-stat-card" style="border-top: 3px solid #f59e0b; background: rgba(245,158,11,0.04);">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
      <div class="admin-stat-label" style="color: #f59e0b;">Editorial Queue</div>
      <i data-lucide="clock" style="width: 18px; color: #f59e0b;"></i>
    </div>
    <div class="admin-stat-val" style="font-family: var(--admin-font-mono); font-size: 1.75rem; color: #fbbf24;"><?= number_format($stats['pending_reviews']) ?></div>
    <div style="font-size: 0.72rem; color: #f59e0b; margin-top: 0.2rem;">Requires review</div>
  </div>

  <div class="admin-stat-card" style="border-top: 3px solid #ec4899; background: rgba(236,72,153,0.04);">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
      <div class="admin-stat-label" style="color: #ec4899;">Author Applications</div>
      <i data-lucide="user-check" style="width: 18px; color: #ec4899;"></i>
    </div>
    <div class="admin-stat-val" style="font-family: var(--admin-font-mono); font-size: 1.75rem; color: #f472b6;"><?= number_format($stats['pending_applications']) ?></div>
    <div style="font-size: 0.72rem; color: #ec4899; margin-top: 0.2rem;">Creator candidates</div>
  </div>

  <div class="admin-stat-card" style="border-top: 3px solid #10b981; background: rgba(16,185,129,0.04);">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
      <div class="admin-stat-label" style="color: #34d399;">Pending Payouts</div>
      <i data-lucide="credit-card" style="width: 18px; color: #34d399;"></i>
    </div>
    <div class="admin-stat-val" style="font-family: var(--admin-font-mono); font-size: 1.75rem; color: #34d399;">$<?= number_format($stats['pending_payouts_sum'], 2) ?></div>
    <div style="font-size: 0.72rem; color: #34d399; margin-top: 0.2rem;"><?= $stats['pending_payouts'] ?> claims waiting</div>
  </div>

  <div class="admin-stat-card" style="border-top: 3px solid <?= ($stats['unresolved_errors'] ?? 0) > 0 ? '#ef4444' : '#64748b' ?>;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
      <div class="admin-stat-label" style="color: <?= ($stats['unresolved_errors'] ?? 0) > 0 ? '#f87171' : '#94a3b8' ?>;">System Errors</div>
      <i data-lucide="shield-alert" style="width: 18px; color: <?= ($stats['unresolved_errors'] ?? 0) > 0 ? '#f87171' : '#64748b' ?>;"></i>
    </div>
    <div class="admin-stat-val" style="font-family: var(--admin-font-mono); font-size: 1.75rem; color: <?= ($stats['unresolved_errors'] ?? 0) > 0 ? '#f87171' : '#94a3b8' ?>;"><?= number_format($stats['unresolved_errors']) ?></div>
    <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.2rem;">Unresolved exceptions</div>
  </div>
</div>

<!-- Charts & Traffic Deck -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem; align-items: stretch;">
  <!-- Line Chart -->
  <div class="admin-table-wrap" style="padding: 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
      <div>
        <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 700;">
          📈 14-Day Platform Readership Telemetry
        </h3>
        <p style="font-size: 0.78rem; color: #64748b; margin: 0.2rem 0 0 0;">Excludes administrative preview &amp; self-author hits</p>
      </div>
      <a href="<?= url('/admin/analytics') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem;">Full Analytics HUD →</a>
    </div>
    <div style="height: 220px; position: relative;">
      <canvas id="adminTrafficChart"></canvas>
    </div>
  </div>

  <!-- Content Distribution Donut -->
  <div class="admin-table-wrap" style="padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between;">
    <div>
      <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 0.5rem 0; font-family: 'Outfit', sans-serif; font-weight: 700;">
        🧩 Content Ecosystem Mix
      </h3>
      <p style="font-size: 0.78rem; color: #64748b; margin: 0 0 1rem 0;">Published items across polymorphic schemas</p>
    </div>
    <div style="height: 180px; position: relative;">
      <canvas id="adminContentDonut"></canvas>
    </div>
  </div>
</div>

<!-- Operational Workflow Queue Deck (Tabbed Switcher) -->
<div class="admin-table-wrap" style="margin-bottom: 2rem;">
  <div style="display: flex; gap: 0.5rem; padding: 0.85rem 1.25rem; border-bottom: 1px solid var(--admin-border-main); overflow-x: auto; align-items: center;">
    <button type="button" onclick="switchOpQueue('editorial')" id="qtab_editorial" class="ts-btn ts-btn-sm ts-btn-primary" style="font-size: 0.8rem; padding: 0.4rem 0.85rem; font-weight: 700;">
      📝 Editorial Queue (<?= count($pendingReviews) ?>)
    </button>
    <button type="button" onclick="switchOpQueue('applications')" id="qtab_applications" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.8rem; padding: 0.4rem 0.85rem; font-weight: 700; color: #ec4899; border-color: rgba(236,72,153,0.3);">
      ✍️ Author Applications (<?= count($pendingApplications) ?>)
    </button>
    <button type="button" onclick="switchOpQueue('payouts')" id="qtab_payouts" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.8rem; padding: 0.4rem 0.85rem; font-weight: 700; color: #10b981; border-color: rgba(16,185,129,0.3);">
      💳 Payout Claims (<?= count($pendingPayouts) ?>)
    </button>
    <button type="button" onclick="switchOpQueue('reports')" id="qtab_reports" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.8rem; padding: 0.4rem 0.85rem; font-weight: 700; color: #f59e0b; border-color: rgba(245,158,11,0.3);">
      🚩 Flagged Reports (<?= count($pendingReports) ?>)
    </button>
  </div>

  <!-- PANE 1: EDITORIAL QUEUE -->
  <div id="qpane_editorial">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Submission &amp; Author</th>
          <th>Type</th>
          <th>Quality Score</th>
          <th>Submitted Date</th>
          <th style="text-align: right;">Review Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($pendingReviews)): ?>
          <?php foreach ($pendingReviews as $rev): ?>
            <tr>
              <td>
                <div style="font-weight: 700; font-size: 0.95rem; color: #f8fafc;">
                  <a href="<?= content_url($rev) ?>" target="_blank" style="color: #f8fafc; text-decoration: none;">
                    <?= htmlspecialchars($rev['title']) ?> <span style="font-size: 0.75rem; color: #38bdf8;">↗</span>
                  </a>
                </div>
                <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.15rem;">By <?= htmlspecialchars($rev['author_name'] ?? 'Staff') ?></div>
              </td>
              <td><span class="ts-badge ts-badge-cyan"><?= strtoupper(htmlspecialchars($rev['content_type'])) ?></span></td>
              <td>
                <strong style="color: #34d399; font-size: 0.95rem; font-family: var(--admin-font-mono);"><?= $rev['quality_score'] ?></strong>
                <span style="font-size: 0.72rem; color: #64748b;">/ 100</span>
              </td>
              <td style="font-size: 0.78rem; color: #64748b; font-family: var(--admin-font-mono);">
                <?= date('M j, Y H:i', strtotime($rev['created_at'])) ?>
              </td>
              <td style="text-align: right;">
                <a href="<?= url('/admin/articles') ?>" class="ts-btn ts-btn-primary ts-btn-sm" style="padding: 0.3rem 0.75rem; font-size: 0.78rem;">
                  Review &amp; Approve ⚡
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="5" style="text-align: center; padding: 2.5rem; color: #64748b;">Editorial pipeline is clear! All submissions processed. ✨</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- PANE 2: AUTHOR APPLICATIONS -->
  <div id="qpane_applications" style="display: none;">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Applicant</th>
          <th>Specialization</th>
          <th>Requested Permissions</th>
          <th>Applied Date</th>
          <th style="text-align: right;">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($pendingApplications)): ?>
          <?php foreach ($pendingApplications as $app): ?>
            <tr>
              <td>
                <div style="font-weight: 700; font-size: 0.95rem; color: #f8fafc;">
                  <?= htmlspecialchars($app['display_name'] ?? $app['username']) ?>
                  <span style="font-size: 0.75rem; color: #64748b;">@<?= htmlspecialchars($app['username']) ?></span>
                </div>
                <div style="font-size: 0.75rem; color: #38bdf8;"><?= htmlspecialchars($app['headline'] ?? 'Technical Contributor') ?></div>
              </td>
              <td style="font-size: 0.82rem; color: #cbd5e1;"><?= htmlspecialchars($app['specializations'] ?? 'Software Architecture') ?></td>
              <td>
                <div style="display: flex; gap: 0.25rem;">
                  <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem;">Articles</span>
                  <?php if (!empty($app['requested_prompts'])): ?><span class="ts-badge ts-badge-pink" style="font-size: 0.65rem;">Prompts</span><?php endif; ?>
                  <?php if (!empty($app['requested_gpts'])): ?><span class="ts-badge ts-badge-pink" style="font-size: 0.65rem;">GPTs</span><?php endif; ?>
                </div>
              </td>
              <td style="font-size: 0.78rem; color: #64748b; font-family: var(--admin-font-mono);">
                <?= date('M j, Y', strtotime($app['created_at'])) ?>
              </td>
              <td style="text-align: right;">
                <form action="<?= url('/admin/applications/action/' . $app['id']) ?>" method="POST" style="display: inline;" onsubmit="return confirm('Approve this author application and grant initial creator badge?');">
                  <input type="hidden" name="action" value="approve">
                  <button type="submit" class="ts-btn ts-btn-sm" style="background: #10b981; color: #fff; font-weight: 700; padding: 0.3rem 0.75rem;">
                    Approve ⚡
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="5" style="text-align: center; padding: 2.5rem; color: #64748b;">No pending author applications. All onboarded! ✨</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- PANE 3: PAYOUT CLAIMS -->
  <div id="qpane_payouts" style="display: none;">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Author Creator</th>
          <th>Requested Gross</th>
          <th>Disbursal Destination</th>
          <th>Claim Date</th>
          <th style="text-align: right;">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($pendingPayouts)): ?>
          <?php foreach ($pendingPayouts as $pay): ?>
            <tr>
              <td>
                <div style="font-weight: 700; color: #f8fafc;"><?= htmlspecialchars($pay['author_name']) ?></div>
                <div style="font-size: 0.75rem; color: #64748b;"><?= htmlspecialchars($pay['user_email']) ?></div>
              </td>
              <td>
                <strong style="color: #10b981; font-size: 1.05rem; font-family: var(--admin-font-mono);">$<?= number_format((float)$pay['amount_usd'], 2) ?></strong>
              </td>
              <td>
                <span class="ts-badge ts-badge-indigo" style="font-size: 0.65rem; text-transform: uppercase;"><?= htmlspecialchars($pay['payout_method']) ?></span>
                <div style="font-size: 0.75rem; color: #94a3b8; font-family: var(--admin-font-mono); max-width: 220px; overflow: hidden; text-overflow: ellipsis;">
                  <?= htmlspecialchars($pay['payout_address']) ?>
                </div>
              </td>
              <td style="font-size: 0.78rem; color: #64748b; font-family: var(--admin-font-mono);">
                <?= date('M j, Y H:i', strtotime($pay['created_at'])) ?>
              </td>
              <td style="text-align: right;">
                <a href="<?= url('/admin/revenue?tab=payouts') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3);">
                  Process Disbursal 💳
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="5" style="text-align: center; padding: 2.5rem; color: #64748b;">No pending payout claims on record. All disbursed! ✨</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- PANE 4: FLAGGED REPORTS -->
  <div id="qpane_reports" style="display: none;">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Entity Target</th>
          <th>Flag Reason</th>
          <th>Reporter</th>
          <th>Date</th>
          <th style="text-align: right;">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($pendingReports)): ?>
          <?php foreach ($pendingReports as $rep): ?>
            <tr>
              <td>
                <div style="font-weight: 700; color: #f8fafc; font-size: 0.9rem;"><?= htmlspecialchars($rep['entity_title'] ?: strtoupper($rep['entity_type']) . ' #' . $rep['entity_id']) ?></div>
                <div style="font-size: 0.75rem; color: #38bdf8;"><a href="<?= htmlspecialchars($rep['entity_url']) ?>" target="_blank" style="color: #38bdf8;">View Target Entity ↗</a></div>
              </td>
              <td><span class="ts-badge ts-badge-pink"><?= htmlspecialchars($rep['reason']) ?></span></td>
              <td style="font-size: 0.8rem; color: #94a3b8;">@<?= htmlspecialchars($rep['reporter_username'] ?? 'guest') ?></td>
              <td style="font-size: 0.78rem; color: #64748b; font-family: var(--admin-font-mono);"><?= date('M j, Y', strtotime($rep['created_at'])) ?></td>
              <td style="text-align: right;">
                <a href="<?= url('/admin/moderation') ?>" class="ts-btn ts-btn-outline ts-btn-sm">Review Report</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="5" style="text-align: center; padding: 2.5rem; color: #64748b;">No flagged reports pending review. Community is clean! ✨</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Live Immutable Audit Trail Strip -->
<div class="admin-table-wrap" style="padding: 1.5rem; margin-bottom: 2rem;">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.5rem;">
    <div>
      <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 700;">
        🛡️ Live System Audit Trail &amp; Forensic Logs
      </h3>
      <p style="font-size: 0.78rem; color: #64748b; margin: 0.2rem 0 0 0;">Immutable real-time audit ledger tracking security and data modifications</p>
    </div>
    <a href="<?= url('/admin/system-logs') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem;">All Forensics →</a>
  </div>

  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 0.75rem;">
    <?php foreach ($auditLogs as $log): ?>
      <div style="background: rgba(13, 21, 39, 0.6); border: 1px solid var(--admin-border-subtle); border-radius: 8px; padding: 0.75rem 1rem;">
        <div style="display: flex; justify-content: space-between; font-size: 0.72rem; margin-bottom: 0.25rem;">
          <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem; padding: 0.1rem 0.35rem;"><?= htmlspecialchars($log['action']) ?></span>
          <span style="font-family: var(--admin-font-mono); color: #64748b;"><?= date('H:i:s M j', strtotime($log['created_at'])) ?></span>
        </div>
        <div style="color: #f8fafc; font-size: 0.82rem; font-weight: 600;">
          Entity: <?= htmlspecialchars($log['entity_type'] ?? 'system') ?> #<?= $log['entity_id'] ?? '' ?>
        </div>
        <div style="color: #64748b; font-family: var(--admin-font-mono); font-size: 0.72rem; margin-top: 0.15rem;">
          IP: <?= htmlspecialchars($log['ip_address']) ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
function toggleQuickAddMenu() {
  const menu = document.getElementById('quickAddMenu');
  if (menu) menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', (e) => {
  const btn = document.getElementById('btnQuickAdd');
  const menu = document.getElementById('quickAddMenu');
  if (btn && menu && !btn.contains(e.target) && !menu.contains(e.target)) {
    menu.style.display = 'none';
  }
});

function switchOpQueue(queue) {
  const tabs = ['editorial', 'applications', 'payouts', 'reports'];
  tabs.forEach(t => {
    const pane = document.getElementById('qpane_' + t);
    const tabBtn = document.getElementById('qtab_' + t);
    if (pane) pane.style.display = (t === queue) ? 'block' : 'none';
    if (tabBtn) {
      if (t === queue) {
        tabBtn.classList.add('ts-btn-primary');
        tabBtn.classList.remove('ts-btn-outline');
      } else {
        tabBtn.classList.remove('ts-btn-primary');
        tabBtn.classList.add('ts-btn-outline');
      }
    }
  });
}

document.addEventListener('DOMContentLoaded', () => {
  // 1. Traffic Chart
  const ctxTraffic = document.getElementById('adminTrafficChart');
  if (ctxTraffic && typeof Chart !== 'undefined') {
    const trafficData = <?= json_encode($trafficMap) ?>;
    const labels = Object.keys(trafficData).map(d => d.substring(5));
    const values = Object.values(trafficData);

    new Chart(ctxTraffic, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Platform Reads',
          data: values,
          borderColor: '#38bdf8',
          backgroundColor: 'rgba(56, 189, 248, 0.08)',
          borderWidth: 2,
          fill: true,
          tension: 0.35,
          pointRadius: 2,
          pointHoverRadius: 5
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { color: 'rgba(148, 163, 184, 0.08)' }, ticks: { color: '#64748b', font: { size: 10 } } },
          y: { beginAtZero: true, grid: { color: 'rgba(148, 163, 184, 0.08)' }, ticks: { color: '#64748b', font: { size: 10 }, precision: 0 } }
        }
      }
    });
  }

  // 2. Content Donut Chart
  const ctxDonut = document.getElementById('adminContentDonut');
  if (ctxDonut && typeof Chart !== 'undefined') {
    const dist = <?= json_encode($contentDistribution) ?>;
    new Chart(ctxDonut, {
      type: 'doughnut',
      data: {
        labels: ['Articles', 'AI Tools', 'Prompts', 'GPTs'],
        datasets: [{
          data: [dist.articles, dist.tools, dist.prompts, dist.gpts],
          backgroundColor: ['#38bdf8', '#10b981', '#a855f7', '#ec4899'],
          borderColor: '#0f172a',
          borderWidth: 3
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'right',
            labels: { color: '#cbd5e1', font: { size: 10 }, boxWidth: 10 }
          }
        }
      }
    });
  }
});
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
