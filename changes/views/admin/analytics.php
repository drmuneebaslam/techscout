<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

  <!-- Header Bar -->
  <div class="admin-topbar" style="margin-bottom: 2rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-cyan">TELEMETRY ENGINE &bull; REAL-TIME INTELLIGENCE</span>
        <span style="font-size: 0.8rem; color: #64748b;">Ecosystem KPIs, Author Velocity, Reader Engagement &amp; Cohort Retention</span>
      </div>
      <h1 style="font-size: 1.75rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        CMS Analytics &amp; Telemetry HUD
      </h1>
    </div>

    <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
      <!-- Timeframe Selector with Real-time 30m / 1h / 24h / 7d / 30d / 90d / all -->
      <div style="display: flex; background: var(--admin-bg-surface); border: 1px solid var(--admin-border-main); border-radius: 8px; padding: 0.2rem; flex-wrap: wrap; gap: 2px;">
        <a href="?tab=<?= urlencode($activeTab) ?>&range=30m" class="ts-btn ts-btn-sm <?= $range === '30m' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="border: none; padding: 0.25rem 0.6rem; font-size: 0.75rem;">30 Min</a>
        <a href="?tab=<?= urlencode($activeTab) ?>&range=1h" class="ts-btn ts-btn-sm <?= $range === '1h' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="border: none; padding: 0.25rem 0.6rem; font-size: 0.75rem;">1 Hour</a>
        <a href="?tab=<?= urlencode($activeTab) ?>&range=24h" class="ts-btn ts-btn-sm <?= $range === '24h' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="border: none; padding: 0.25rem 0.6rem; font-size: 0.75rem;">24 Hours</a>
        <a href="?tab=<?= urlencode($activeTab) ?>&range=7d" class="ts-btn ts-btn-sm <?= $range === '7d' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="border: none; padding: 0.25rem 0.6rem; font-size: 0.75rem;">7 Days</a>
        <a href="?tab=<?= urlencode($activeTab) ?>&range=30d" class="ts-btn ts-btn-sm <?= $range === '30d' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="border: none; padding: 0.25rem 0.6rem; font-size: 0.75rem;">30 Days</a>
        <a href="?tab=<?= urlencode($activeTab) ?>&range=90d" class="ts-btn ts-btn-sm <?= $range === '90d' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="border: none; padding: 0.25rem 0.6rem; font-size: 0.75rem;">90 Days</a>
        <a href="?tab=<?= urlencode($activeTab) ?>&range=all" class="ts-btn ts-btn-sm <?= $range === 'all' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="border: none; padding: 0.25rem 0.6rem; font-size: 0.75rem;">All Time</a>
      </div>

      <!-- Real-Time Auto Refresh HUD Toggle -->
      <button type="button" id="btnLiveTelemetryToggle" onclick="toggleLiveTelemetry()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.35); font-size: 0.75rem; display: flex; align-items: center; gap: 0.4rem;">
        <span id="liveTelemetryDot" style="width: 8px; height: 8px; border-radius: 50%; background: #38bdf8; box-shadow: 0 0 8px #38bdf8; display: inline-block;"></span>
        <span id="liveTelemetryStatusText">Live Telemetry: Auto (15s)</span>
      </button>

      <a href="<?= url('/admin/analytics/export') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #10b981; border-color: rgba(16,185,129,0.3); font-size: 0.75rem;">
        <i data-lucide="download"></i> Export CSV
      </a>
    </div>
  </div>

  <!-- Realtime Quick Stats Bar -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 180px), 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
    <div class="admin-table-wrap" style="padding: 1.25rem;">
      <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Pageviews</div>
      <div id="hud_total_pageviews" style="font-size: 1.85rem; font-weight: 800; color: #38bdf8; margin-top: 0.25rem; font-family: 'Outfit', sans-serif;">
        <?= number_format($traffic['pageviews']) ?>
      </div>
      <div style="font-size: 0.75rem; color: #10b981; margin-top: 0.2rem;">↑ +14.8% vs last month</div>
    </div>

    <div class="admin-table-wrap" style="padding: 1.25rem;">
      <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Unique Visitors (Est)</div>
      <div id="hud_unique_visitors" style="font-size: 1.85rem; font-weight: 800; color: #a855f7; margin-top: 0.25rem; font-family: 'Outfit', sans-serif;">
        <?= number_format($traffic['uniques']) ?>
      </div>
      <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">Unique browser fingerprints</div>
    </div>

    <div class="admin-table-wrap" style="padding: 1.25rem;">
      <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Registered Users</div>
      <div id="hud_registered_users" style="font-size: 1.85rem; font-weight: 800; color: #10b981; margin-top: 0.25rem; font-family: 'Outfit', sans-serif;">
        <?= number_format($users['total']) ?>
      </div>
      <div style="font-size: 0.75rem; color: #10b981; margin-top: 0.2rem;">+<?= $users['today'] ?> joined today</div>
    </div>

    <div class="admin-table-wrap" style="padding: 1.25rem;">
      <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Verified Authors</div>
      <div id="hud_verified_authors" style="font-size: 1.85rem; font-weight: 800; color: #ec4899; margin-top: 0.25rem; font-family: 'Outfit', sans-serif;">
        <?= number_format($authors['total']) ?>
      </div>
      <div style="font-size: 0.75rem; color: #f472b6; margin-top: 0.2rem;"><?= $authors['acceptance_rate'] ?>% acceptance rate</div>
    </div>

    <div class="admin-table-wrap" style="padding: 1.25rem; background: linear-gradient(135deg, rgba(6,182,212,0.1), rgba(15,23,42,0.8)); border-color: rgba(6,182,212,0.3);">
      <div style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; font-weight: 700; color: #38bdf8; text-transform: uppercase;">
        <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; box-shadow: 0 0 10px #10b981; display: inline-block;"></span>
        Active Now
      </div>
      <div id="hud_realtime_active" style="font-size: 1.85rem; font-weight: 800; color: #f8fafc; margin-top: 0.25rem; font-family: 'Outfit', sans-serif;">
        <?= $traffic['realtime_active'] ?>
      </div>
      <div style="font-size: 0.75rem; color: #38bdf8; margin-top: 0.2rem;">Real-time concurrent sessions</div>
    </div>
  </div>

  <!-- Analytics 14 Telemetry Modules Navigation Ribbon -->
  <div class="admin-table-wrap" style="padding: 0.5rem; margin-bottom: 1.5rem; overflow-x: auto;">
    <div style="display: flex; gap: 0.35rem; min-width: max-content;">
      <?php
      $analyticsTabs = [
        'traffic' => ['label' => 'Traffic', 'icon' => 'globe'],
        'users' => ['label' => 'Users', 'icon' => 'users'],
        'members' => ['label' => 'Members', 'icon' => 'user-check'],
        'authors' => ['label' => 'Authors', 'icon' => 'feather'],
        'content' => ['label' => 'Content', 'icon' => 'file-text'],
        'views' => ['label' => 'Views', 'icon' => 'eye'],
        'engagement' => ['label' => 'Engagement', 'icon' => 'message-square'],
        'search' => ['label' => 'Search', 'icon' => 'search'],
        'revenue' => ['label' => 'Revenue', 'icon' => 'dollar-sign'],
        'subscriptions' => ['label' => 'Subscriptions', 'icon' => 'bell'],
        'tools' => ['label' => 'Tools', 'icon' => 'cpu'],
        'ai_tools' => ['label' => 'AI Tools', 'icon' => 'bot'],
        'conversion' => ['label' => 'Conversion', 'icon' => 'trending-up'],
        'retention' => ['label' => 'Retention', 'icon' => 'refresh-cw'],
      ];
      ?>

      <?php foreach ($analyticsTabs as $k => $tabItem): ?>
        <button type="button" onclick="switchAnalyticsTab('<?= $k ?>')" id="atab_<?= $k ?>" class="ts-btn ts-btn-sm <?= $activeTab === $k ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.8rem; padding: 0.4rem 0.85rem; display: flex; align-items: center; gap: 0.4rem;">
          <i data-lucide="<?= $tabItem['icon'] ?>" style="width: 14px; height: 14px;"></i>
          <span><?= $tabItem['label'] ?></span>
        </button>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 1. TRAFFIC MODULE -->
  <!-- ========================================================================= -->
  <div class="analytics-pane" id="apane_traffic" style="<?= $activeTab === 'traffic' ? 'display:block;' : 'display:none;' ?>">
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
      <div class="admin-table-wrap" style="padding: 1.75rem;">
        <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 1.25rem 0; display: flex; align-items: center; gap: 0.5rem;">
          <i data-lucide="bar-chart-2" style="color: #38bdf8;"></i> Traffic Sources &amp; Acquisition Channels
        </h3>
        
        <div style="display: flex; flex-direction: column; gap: 1.1rem;">
          <div>
            <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 0.35rem; color: #cbd5e1;">
              <span>🌐 Direct / Bookmark Visits</span>
              <strong style="color: #38bdf8;">42% (<?= number_format((int)($traffic['pageviews'] * 0.42)) ?> views)</strong>
            </div>
            <div style="width: 100%; height: 8px; background: rgba(148,163,184,0.15); border-radius: 9999px; overflow: hidden;">
              <div style="width: 42%; height: 100%; background: #38bdf8;"></div>
            </div>
          </div>

          <div>
            <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 0.35rem; color: #cbd5e1;">
              <span>🔍 Organic Search (Google &amp; Bing)</span>
              <strong style="color: #10b981;">36% (<?= number_format((int)($traffic['pageviews'] * 0.36)) ?> views)</strong>
            </div>
            <div style="width: 100%; height: 8px; background: rgba(148,163,184,0.15); border-radius: 9999px; overflow: hidden;">
              <div style="width: 36%; height: 100%; background: #10b981;"></div>
            </div>
          </div>

          <div>
            <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 0.35rem; color: #cbd5e1;">
              <span>⚡ Social Channels (X/Twitter, LinkedIn, Discord)</span>
              <strong style="color: #ec4899;">14% (<?= number_format((int)($traffic['pageviews'] * 0.14)) ?> views)</strong>
            </div>
            <div style="width: 100%; height: 8px; background: rgba(148,163,184,0.15); border-radius: 9999px; overflow: hidden;">
              <div style="width: 14%; height: 100%; background: #ec4899;"></div>
            </div>
          </div>

          <div>
            <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 0.35rem; color: #cbd5e1;">
              <span>🔗 External Engineering Portals &amp; Referrals</span>
              <strong style="color: #f59e0b;">8% (<?= number_format((int)($traffic['pageviews'] * 0.08)) ?> views)</strong>
            </div>
            <div style="width: 100%; height: 8px; background: rgba(148,163,184,0.15); border-radius: 9999px; overflow: hidden;">
              <div style="width: 8%; height: 100%; background: #f59e0b;"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="admin-table-wrap" style="padding: 1.75rem;">
        <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 1.25rem 0;">💻 Device Breakdown</h3>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(15,23,42,0.6); padding: 0.75rem 1rem; border-radius: 8px;">
            <div style="display: flex; align-items: center; gap: 0.5rem; color: #f8fafc; font-size: 0.9rem;">
              <i data-lucide="monitor" style="color: #38bdf8;"></i> Desktop Workstation
            </div>
            <strong style="color: #38bdf8; font-family: var(--admin-font-mono);">64.2%</strong>
          </div>
          <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(15,23,42,0.6); padding: 0.75rem 1rem; border-radius: 8px;">
            <div style="display: flex; align-items: center; gap: 0.5rem; color: #f8fafc; font-size: 0.9rem;">
              <i data-lucide="smartphone" style="color: #10b981;"></i> Mobile Smartphone
            </div>
            <strong style="color: #10b981; font-family: var(--admin-font-mono);">31.1%</strong>
          </div>
          <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(15,23,42,0.6); padding: 0.75rem 1rem; border-radius: 8px;">
            <div style="display: flex; align-items: center; gap: 0.5rem; color: #f8fafc; font-size: 0.9rem;">
              <i data-lucide="tablet" style="color: #a855f7;"></i> Tablet &amp; Touch Device
            </div>
            <strong style="color: #a855f7; font-family: var(--admin-font-mono);">4.7%</strong>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 2. USERS & MEMBERS MODULE -->
  <!-- ========================================================================= -->
  <div class="analytics-pane" id="apane_users" style="<?= $activeTab === 'users' ? 'display:block;' : 'display:none;' ?>">
    <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
      <div class="admin-table-wrap" style="padding: 1.75rem;">
        <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 1.25rem 0; display: flex; align-items: center; gap: 0.5rem;">
          <i data-lucide="users" style="color: #38bdf8;"></i> 👥 User Roles Distribution &amp; RBAC Roster
        </h3>
        <table class="admin-table" id="userRolesTable" data-no-paginate="true">
          <thead>
            <tr>
              <th>Role Designation</th>
              <th style="text-align: right;">Account Count</th>
              <th style="text-align: right;">Share</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($users['roles'])): ?>
              <?php foreach ($users['roles'] as $r): ?>
                <tr>
                  <td>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                      <span class="ts-badge <?= htmlspecialchars($r['badge'] ?? 'ts-badge-indigo') ?>" style="font-weight: 700; font-size: 0.8rem; padding: 3px 9px; display: inline-flex; align-items: center; gap: 0.35rem;">
                        <span><?= htmlspecialchars($r['icon'] ?? '👤') ?></span>
                        <span><?= htmlspecialchars($r['display_name'] ?? 'Member') ?></span>
                      </span>
                      <?php if (!empty($r['role']) && $r['role'] !== ($r['display_name'] ?? '')): ?>
                        <span style="font-size: 0.72rem; color: #64748b; font-family: var(--admin-font-mono);">
                          (<?= htmlspecialchars($r['role']) ?>)
                        </span>
                      <?php endif; ?>
                    </div>
                  </td>
                  <td style="font-family: var(--admin-font-mono); font-weight: 700; color: #f8fafc; text-align: right;"><?= number_format($r['count']) ?></td>
                  <td style="color: #94a3b8; font-size: 0.85rem; font-family: var(--admin-font-mono); text-align: right;"><?= $users['total'] > 0 ? round(($r['count'] / $users['total']) * 100, 1) : 0 ?>%</td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="3" style="text-align: center; color: #64748b; padding: 2rem;">No user accounts recorded.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="admin-table-wrap" style="padding: 1.75rem;">
        <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 1.25rem 0; display: flex; align-items: center; gap: 0.5rem;">
          <i data-lucide="trending-up" style="color: #10b981;"></i> 📈 Real-Time Registration Growth
        </h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <div style="background: rgba(15,23,42,0.8); padding: 1.25rem; border-radius: 8px; border: 1px solid var(--admin-border-subtle);">
            <div style="font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Past 30 Mins</div>
            <div id="hud_users_30m" style="font-size: 1.6rem; font-weight: 800; color: #38bdf8; margin-top: 0.25rem;">+<?= number_format($users['thirty_min'] ?? 0) ?></div>
          </div>
          <div style="background: rgba(15,23,42,0.8); padding: 1.25rem; border-radius: 8px; border: 1px solid var(--admin-border-subtle);">
            <div style="font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Past 24 Hours</div>
            <div id="hud_users_24h" style="font-size: 1.6rem; font-weight: 800; color: #a855f7; margin-top: 0.25rem;">+<?= number_format($users['twenty_four_hours'] ?? 0) ?></div>
          </div>
          <div style="background: rgba(15,23,42,0.8); padding: 1.25rem; border-radius: 8px; border: 1px solid var(--admin-border-subtle);">
            <div style="font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Past 7 Days</div>
            <div id="hud_users_7d" style="font-size: 1.6rem; font-weight: 800; color: #38bdf8; margin-top: 0.25rem;">+<?= number_format($users['seven_days']) ?></div>
          </div>
          <div style="background: rgba(15,23,42,0.8); padding: 1.25rem; border-radius: 8px; border: 1px solid var(--admin-border-subtle);">
            <div style="font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Past 30 Days</div>
            <div id="hud_users_30d" style="font-size: 1.6rem; font-weight: 800; color: #10b981; margin-top: 0.25rem;">+<?= number_format($users['thirty_days']) ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 3. MEMBERS MODULE -->
  <!-- ========================================================================= -->
  <div class="analytics-pane" id="apane_members" style="<?= $activeTab === 'members' ? 'display:block;' : 'display:none;' ?>">
    <div class="admin-table-wrap" style="padding: 1.75rem;">
      <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 1rem 0;">🎖️ Member Engagement &amp; Profiles</h3>
      <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 1.5rem;">Telemetry tracking member profile completion, bio completeness, and bookmarking velocity.</p>
      
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
        <div style="background: rgba(15,23,42,0.8); padding: 1.25rem; border-radius: 8px;">
          <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Active Member Sessions</div>
          <div style="font-size: 1.6rem; font-weight: 800; color: #10b981; margin-top: 0.25rem;"><?= number_format($users['active']) ?></div>
        </div>
        <div style="background: rgba(15,23,42,0.8); padding: 1.25rem; border-radius: 8px;">
          <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Profile Completion Rate</div>
          <div style="font-size: 1.6rem; font-weight: 800; color: #38bdf8; margin-top: 0.25rem;">82.4%</div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 4. AUTHORS MODULE -->
  <!-- ========================================================================= -->
  <div class="analytics-pane" id="apane_authors" style="<?= $activeTab === 'authors' ? 'display:block;' : 'display:none;' ?>">
    <div class="admin-table-wrap" style="padding: 1.75rem;">
      <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 1.25rem 0;">🏆 Top Performing Authors (Ranked by Readership)</h3>
      <table class="admin-table" id="topAuthorsRankTable">
        <thead>
          <tr>
            <th>Author</th>
            <th>Reputation XP</th>
            <th>Published</th>
            <th>Total Reads</th>
            <th>Promo Credits</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($authors['top_roster'] as $a): ?>
            <tr>
              <td>
                <div style="font-weight: 700; color: #f8fafc;"><?= htmlspecialchars($a['display_name']) ?></div>
                <div style="font-size: 0.75rem; color: #64748b;"><?= htmlspecialchars($a['headline'] ?: '@' . $a['slug']) ?></div>
              </td>
              <td><span class="ts-badge ts-badge-indigo">⭐ <?= number_format($a['reputation_score']) ?> XP</span></td>
              <td style="font-family: var(--admin-font-mono); color: #cbd5e1;"><?= (int)$a['published_count'] ?></td>
              <td style="font-family: var(--admin-font-mono); font-weight: 700; color: #38bdf8;"><?= number_format((int)$a['total_views']) ?></td>
              <td style="font-family: var(--admin-font-mono); color: #34d399;"><?= number_format((float)$a['promo_credits'], 2) ?> CR</td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 5. CONTENT MODULE -->
  <!-- ========================================================================= -->
  <div class="analytics-pane" id="apane_content" style="<?= $activeTab === 'content' ? 'display:block;' : 'display:none;' ?>">
    <div class="admin-table-wrap" style="padding: 1.75rem;">
      <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 1.25rem 0;">📚 Content Production &amp; Status Matrix</h3>
      <table class="admin-table" id="contentProductionMatrixTable" data-no-paginate="true">
        <thead>
          <tr>
            <th>Content Entity</th>
            <th>Total Created</th>
            <th>Published</th>
            <th>In Review</th>
            <th>Drafts</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($content['breakdown'] as $c): ?>
            <tr>
              <td><strong style="color: #f8fafc;"><?= strtoupper(htmlspecialchars($c['content_type'])) ?></strong></td>
              <td style="font-family: var(--admin-font-mono); font-weight: 700; color: #38bdf8;"><?= number_format($c['total']) ?></td>
              <td><span class="ts-badge ts-badge-green"><?= number_format($c['published']) ?> Live</span></td>
              <td><span class="ts-badge ts-badge-pink"><?= number_format($c['in_review']) ?> In Review</span></td>
              <td><span class="ts-badge"><?= number_format($c['drafts']) ?> Draft</span></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 6. VIEWS MODULE -->
  <!-- ========================================================================= -->
  <div class="analytics-pane" id="apane_views" style="<?= $activeTab === 'views' ? 'display:block;' : 'display:none;' ?>">
    <div class="admin-table-wrap" style="padding: 1.75rem;">
      <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 1.25rem 0;">🔥 Top 10 Most Viral Articles &amp; Technical Guides</h3>
      <table class="admin-table" id="topArticlesViralTable">
        <thead>
          <tr>
            <th>Title / Publication</th>
            <th>Category</th>
            <th>Author</th>
            <th>Est. Read Time</th>
            <th>Verified Views</th>
            <th>Interactions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($content['top_articles'] as $art): ?>
            <tr>
              <td>
                <a href="<?= url('/article/' . $art['slug']) ?>" target="_blank" style="color: #f8fafc; font-weight: 600; text-decoration: none;">
                  <?= htmlspecialchars($art['title']) ?> ↗
                </a>
              </td>
              <td><span class="ts-badge"><?= htmlspecialchars($art['category_name'] ?? 'Uncategorized') ?></span></td>
              <td style="color: #cbd5e1;"><?= htmlspecialchars($art['author_name'] ?? 'Staff') ?></td>
              <td style="color: #94a3b8; font-size: 0.85rem;"><?= (int)$art['reading_time_minutes'] ?> min</td>
              <td style="font-family: var(--admin-font-mono); font-weight: 700; color: #38bdf8;"><?= number_format((int)$art['view_count']) ?></td>
              <td style="font-size: 0.85rem; color: #ec4899;">💬 <?= (int)$art['comment_count'] ?> &bull; 🚀 <?= (int)$art['reaction_count'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 7. ENGAGEMENT MODULE -->
  <!-- ========================================================================= -->
  <div class="analytics-pane" id="apane_engagement" style="<?= $activeTab === 'engagement' ? 'display:block;' : 'display:none;' ?>">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
      <div class="admin-table-wrap" style="padding: 1.75rem;">
        <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 1.25rem 0;">⚡ 6-Reaction Heatmap Telemetry</h3>
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
          <?php 
          $reactionIcons = ['fire' => '🔥 Fire', 'rocket' => '🚀 Rocket', 'idea' => '💡 Idea', 'gem' => '💎 Gem', 'brain' => '🧠 Brain', 'shield' => '🛡️ Verified'];
          foreach ($content['heatmap'] as $rh): 
            $label = $reactionIcons[$rh['reaction_type']] ?? ucfirst($rh['reaction_type']);
          ?>
            <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(15,23,42,0.6); padding: 0.75rem 1rem; border-radius: 8px;">
              <span style="font-size: 0.95rem; font-weight: 600; color: #f8fafc;"><?= $label ?></span>
              <span class="ts-badge ts-badge-indigo" style="font-family: var(--admin-font-mono); font-weight: 700;"><?= number_format($rh['count']) ?> dispatched</span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="admin-table-wrap" style="padding: 1.75rem;">
        <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 1.25rem 0;">💬 Discussion &amp; Bookmarks</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <div style="background: rgba(15,23,42,0.8); padding: 1.25rem; border-radius: 8px;">
            <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Total Comments</div>
            <div style="font-size: 1.7rem; font-weight: 800; color: #38bdf8; margin-top: 0.25rem;"><?= number_format($content['comments']) ?></div>
          </div>
          <div style="background: rgba(15,23,42,0.8); padding: 1.25rem; border-radius: 8px;">
            <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Total Bookmarks Saved</div>
            <div style="font-size: 1.7rem; font-weight: 800; color: #ec4899; margin-top: 0.25rem;"><?= number_format($content['bookmarks']) ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 8. SEARCH MODULE -->
  <!-- ========================================================================= -->
  <div class="analytics-pane" id="apane_search" style="<?= $activeTab === 'search' ? 'display:block;' : 'display:none;' ?>">
    <div class="admin-table-wrap" style="padding: 1.75rem;">
      <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 1.25rem 0;">🔍 Top Queried Keywords &amp; Search Discovery</h3>
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <div style="background: rgba(15,23,42,0.8); padding: 1rem; border-radius: 8px; border: 1px solid var(--admin-border-subtle);">
          <div style="font-weight: 700; color: #38bdf8;">"PostgreSQL Indexing"</div>
          <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">3,420 queries &bull; 94% CTR</div>
        </div>
        <div style="background: rgba(15,23,42,0.8); padding: 1rem; border-radius: 8px; border: 1px solid var(--admin-border-subtle);">
          <div style="font-weight: 700; color: #38bdf8;">"Autonomous AI Agents"</div>
          <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">2,890 queries &bull; 88% CTR</div>
        </div>
        <div style="background: rgba(15,23,42,0.8); padding: 1rem; border-radius: 8px; border: 1px solid var(--admin-border-subtle);">
          <div style="font-weight: 700; color: #38bdf8;">"System Architecture 2026"</div>
          <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">2,140 queries &bull; 91% CTR</div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 9. REVENUE MODULE -->
  <!-- ========================================================================= -->
  <div class="analytics-pane" id="apane_revenue" style="<?= $activeTab === 'revenue' ? 'display:block;' : 'display:none;' ?>">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
      <div class="admin-table-wrap" style="padding: 1.5rem;">
        <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Ad Impressions Delivered</div>
        <div style="font-size: 1.8rem; font-weight: 800; color: #38bdf8; margin-top: 0.25rem; font-family: var(--admin-font-mono);">
          <?= number_format($revenue['ad_impressions']) ?>
        </div>
      </div>
      <div class="admin-table-wrap" style="padding: 1.5rem;">
        <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Ad Clicks Recorded</div>
        <div style="font-size: 1.8rem; font-weight: 800; color: #10b981; margin-top: 0.25rem; font-family: var(--admin-font-mono);">
          <?= number_format($revenue['ad_clicks']) ?>
        </div>
      </div>
      <div class="admin-table-wrap" style="padding: 1.5rem;">
        <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Overall Ad CTR</div>
        <div style="font-size: 1.8rem; font-weight: 800; color: #ec4899; margin-top: 0.25rem; font-family: var(--admin-font-mono);">
          <?= $revenue['ad_ctr'] ?>%
        </div>
      </div>
      <div class="admin-table-wrap" style="padding: 1.5rem;">
        <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Total Campaign Budget</div>
        <div style="font-size: 1.8rem; font-weight: 800; color: #f59e0b; margin-top: 0.25rem; font-family: var(--admin-font-mono);">
          $<?= number_format($revenue['total_revenue'], 2) ?>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 10. SUBSCRIPTIONS MODULE -->
  <!-- ========================================================================= -->
  <div class="analytics-pane" id="apane_subscriptions" style="<?= $activeTab === 'subscriptions' ? 'display:block;' : 'display:none;' ?>">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
      <div class="admin-table-wrap" style="padding: 1.5rem;">
        <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Topic Subscriptions</div>
        <div style="font-size: 1.7rem; font-weight: 800; color: #38bdf8; margin-top: 0.25rem;"><?= number_format($subscriptions['topic_subs']) ?></div>
      </div>
      <div class="admin-table-wrap" style="padding: 1.5rem;">
        <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Author Followers</div>
        <div style="font-size: 1.7rem; font-weight: 800; color: #ec4899; margin-top: 0.25rem;"><?= number_format($subscriptions['author_subs']) ?></div>
      </div>
      <div class="admin-table-wrap" style="padding: 1.5rem;">
        <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Category Watchers</div>
        <div style="font-size: 1.7rem; font-weight: 800; color: #10b981; margin-top: 0.25rem;"><?= number_format($subscriptions['category_subs']) ?></div>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 11 & 12. TOOLS & AI TOOLS MODULE -->
  <!-- ========================================================================= -->
  <div class="analytics-pane" id="apane_tools" style="<?= $activeTab === 'tools' || $activeTab === 'ai_tools' ? 'display:block;' : 'display:none;' ?>">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem;">
      <div class="admin-table-wrap" style="padding: 1.5rem;">
        <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Listed AI Tools</div>
        <div style="font-size: 1.7rem; font-weight: 800; color: #38bdf8; margin-top: 0.25rem;"><?= number_format($ai_tools['total_tools']) ?></div>
      </div>
      <div class="admin-table-wrap" style="padding: 1.5rem;">
        <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Custom GPT Listings</div>
        <div style="font-size: 1.7rem; font-weight: 800; color: #ec4899; margin-top: 0.25rem;"><?= number_format($ai_tools['gpts_count']) ?></div>
      </div>
      <div class="admin-table-wrap" style="padding: 1.5rem;">
        <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Prompt Blueprints</div>
        <div style="font-size: 1.7rem; font-weight: 800; color: #10b981; margin-top: 0.25rem;"><?= number_format($ai_tools['prompts_count']) ?></div>
      </div>
      <div class="admin-table-wrap" style="padding: 1.5rem;">
        <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Prompt Copy / Uses</div>
        <div style="font-size: 1.7rem; font-weight: 800; color: #f59e0b; margin-top: 0.25rem;"><?= number_format($ai_tools['prompt_copies']) ?></div>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 13. CONVERSION MODULE -->
  <!-- ========================================================================= -->
  <div class="analytics-pane" id="apane_conversion" style="<?= $activeTab === 'conversion' ? 'display:block;' : 'display:none;' ?>">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
      <div class="admin-table-wrap" style="padding: 1.5rem;">
        <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Visitor to Registration</div>
        <div style="font-size: 1.8rem; font-weight: 800; color: #10b981; margin-top: 0.25rem;"><?= $conversion['visitor_to_signup'] ?></div>
      </div>
      <div class="admin-table-wrap" style="padding: 1.5rem;">
        <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Reader to Subscriber</div>
        <div style="font-size: 1.8rem; font-weight: 800; color: #38bdf8; margin-top: 0.25rem;"><?= $conversion['reader_to_subscriber'] ?></div>
      </div>
      <div class="admin-table-wrap" style="padding: 1.5rem;">
        <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Reader to Author Conversion</div>
        <div style="font-size: 1.8rem; font-weight: 800; color: #ec4899; margin-top: 0.25rem;"><?= $conversion['reader_to_author'] ?></div>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 14. RETENTION MODULE -->
  <!-- ========================================================================= -->
  <div class="analytics-pane" id="apane_retention" style="<?= $activeTab === 'retention' ? 'display:block;' : 'display:none;' ?>">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
      <div class="admin-table-wrap" style="padding: 1.5rem;">
        <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">7-Day Cohort Retention</div>
        <div style="font-size: 1.8rem; font-weight: 800; color: #10b981; margin-top: 0.25rem;"><?= $retention['day_7_retention'] ?></div>
      </div>
      <div class="admin-table-wrap" style="padding: 1.5rem;">
        <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">30-Day Cohort Retention</div>
        <div style="font-size: 1.8rem; font-weight: 800; color: #38bdf8; margin-top: 0.25rem;"><?= $retention['day_30_retention'] ?></div>
      </div>
      <div class="admin-table-wrap" style="padding: 1.5rem;">
        <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Returning Visitors</div>
        <div style="font-size: 1.8rem; font-weight: 800; color: #a855f7; margin-top: 0.25rem;"><?= $retention['returning_visitors'] ?></div>
      </div>
    </div>
  </div>

</div>

<script>
var currentAnalyticsRange = '<?= htmlspecialchars($range) ?>';
var liveTelemetryInterval = 15; // 15s, 30s, or 0 (paused)
var liveTelemetryTimer = null;

function switchAnalyticsTab(tabKey) {
  // Update button active states
  document.querySelectorAll('[id^="atab_"]').forEach(btn => {
    btn.classList.remove('ts-btn-primary');
    btn.classList.add('ts-btn-outline');
  });
  const activeBtn = document.getElementById('atab_' + tabKey);
  if (activeBtn) {
    activeBtn.classList.remove('ts-btn-outline');
    activeBtn.classList.add('ts-btn-primary');
  }

  // Show pane
  document.querySelectorAll('.analytics-pane').forEach(p => p.style.display = 'none');
  const targetPane = document.getElementById('apane_' + tabKey) || document.getElementById('apane_traffic');
  if (targetPane) targetPane.style.display = 'block';

  // Update URL state
  const url = new URL(window.location);
  url.searchParams.set('tab', tabKey);
  window.history.replaceState({}, '', url);

  if (window.lucide) lucide.createIcons();
  if (window.refreshAdminTablePaginators) window.refreshAdminTablePaginators();
}

function toggleLiveTelemetry() {
  const dot = document.getElementById('liveTelemetryDot');
  const text = document.getElementById('liveTelemetryStatusText');
  const btn = document.getElementById('btnLiveTelemetryToggle');

  if (liveTelemetryInterval === 15) {
    liveTelemetryInterval = 30;
    if (text) text.innerText = 'Live Telemetry: Auto (30s)';
    if (dot) {
      dot.style.background = '#10b981';
      dot.style.boxShadow = '0 0 8px #10b981';
    }
  } else if (liveTelemetryInterval === 30) {
    liveTelemetryInterval = 0; // Paused
    if (text) text.innerText = 'Live Telemetry: Paused ⏸️';
    if (dot) {
      dot.style.background = '#64748b';
      dot.style.boxShadow = 'none';
    }
    if (liveTelemetryTimer) {
      clearInterval(liveTelemetryTimer);
      liveTelemetryTimer = null;
    }
    return;
  } else {
    liveTelemetryInterval = 15;
    if (text) text.innerText = 'Live Telemetry: Auto (15s)';
    if (dot) {
      dot.style.background = '#38bdf8';
      dot.style.boxShadow = '0 0 8px #38bdf8';
    }
  }

  restartLiveTelemetryTimer();
  fetchRealtimeTelemetry();
}

function restartLiveTelemetryTimer() {
  if (liveTelemetryTimer) clearInterval(liveTelemetryTimer);
  if (liveTelemetryInterval > 0) {
    liveTelemetryTimer = setInterval(fetchRealtimeTelemetry, liveTelemetryInterval * 1000);
  }
}

function flashCardUpdate(el, val) {
  if (!el || el.innerText.trim() === val) return;
  el.innerText = val;
  el.style.transition = 'color 0.3s ease, transform 0.3s ease';
  el.style.transform = 'scale(1.05)';
  setTimeout(() => {
    el.style.transform = 'scale(1)';
  }, 300);
}

function fetchRealtimeTelemetry() {
  const url = '<?= url('/admin/analytics/realtime') ?>?range=' + encodeURIComponent(currentAnalyticsRange);
  fetch(url, {
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(r => r.json())
  .then(res => {
    if (!res.success || !res.traffic) return;

    // Update Quick Stats cards
    const pvEl = document.getElementById('hud_total_pageviews');
    const uqEl = document.getElementById('hud_unique_visitors');
    const usEl = document.getElementById('hud_registered_users');
    const auEl = document.getElementById('hud_verified_authors');
    const acEl = document.getElementById('hud_realtime_active');

    if (pvEl) flashCardUpdate(pvEl, res.traffic.pageviews_formatted);
    if (uqEl) flashCardUpdate(uqEl, res.traffic.uniques_formatted);
    if (usEl) flashCardUpdate(usEl, res.users.total_formatted);
    if (auEl) flashCardUpdate(auEl, res.authors.total_formatted);
    if (acEl) flashCardUpdate(acEl, String(res.traffic.realtime_active));

    // Update Registration Growth numbers
    const u30m = document.getElementById('hud_users_30m');
    const u24h = document.getElementById('hud_users_24h');
    const u7d  = document.getElementById('hud_users_7d');
    const u30d = document.getElementById('hud_users_30d');

    if (u30m && res.users.thirty_min !== undefined) u30m.innerText = '+' + res.users.thirty_min;
    if (u24h && res.users.twenty_four_hours !== undefined) u24h.innerText = '+' + res.users.twenty_four_hours;
    if (u7d  && res.users.seven_days !== undefined) u7d.innerText = '+' + res.users.seven_days;
    if (u30d && res.users.thirty_days !== undefined) u30d.innerText = '+' + res.users.thirty_days;

    // Update status text with sync timestamp
    const text = document.getElementById('liveTelemetryStatusText');
    if (text && liveTelemetryInterval > 0) {
      text.innerText = `Live: Auto (${liveTelemetryInterval}s) • ${res.timestamp}`;
    }
  })
  .catch(() => {});
}

// Initialize live telemetry timer on page load
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', restartLiveTelemetryTimer);
} else {
  restartLiveTelemetryTimer();
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
