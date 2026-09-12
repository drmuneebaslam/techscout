<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

  <!-- Topbar Header -->
  <div class="admin-topbar" style="margin-bottom: 2rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-indigo">AUDIENCE &bull; CMS NEWSLETTER HUB</span>
        <span style="font-size: 0.8rem; color: #64748b;">Subscribers Roster, Campaign Issues, HTML Issue Composer &amp; Dynamic Variable Personalization</span>
      </div>
      <h1 style="font-size: 1.75rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        CMS Newsletter &amp; Audience Command Center
      </h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <a href="<?= url('/admin/mail') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3);">
        <i data-lucide="mail"></i> Open Mailing Studio ↗
      </a>
      <a href="<?= url('/admin/subscribers/export') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #10b981; border-color: rgba(16,185,129,0.3);">
        <i data-lucide="download"></i> Export CSV
      </a>
      <button type="button" onclick="switchNewsletterTab('composer')" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #6366f1, #38bdf8); font-weight: 700;">
        <i data-lucide="send"></i> Compose &amp; Send Studio ⚡
      </button>
    </div>
  </div>

  <!-- Telemetry Overview Bar -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 180px), 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
    <div class="admin-table-wrap" style="padding: 1.25rem;">
      <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Audience</div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #38bdf8; margin-top: 0.25rem; font-family: 'Outfit', sans-serif;">
        <?= number_format($stats['total']) ?>
      </div>
      <div style="font-size: 0.75rem; color: #10b981; margin-top: 0.2rem;">Verified opt-in readers</div>
    </div>

    <div class="admin-table-wrap" style="padding: 1.25rem;">
      <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Active Subscribers</div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #10b981; margin-top: 0.25rem; font-family: 'Outfit', sans-serif;">
        <?= number_format($stats['active']) ?>
      </div>
      <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">Ready to receive broadcasts</div>
    </div>

    <div class="admin-table-wrap" style="padding: 1.25rem;">
      <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Average Open Rate</div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #ec4899; margin-top: 0.25rem; font-family: 'Outfit', sans-serif;">
        <?= $stats['avg_open_rate'] ?>
      </div>
      <div style="font-size: 0.75rem; color: #f472b6; margin-top: 0.2rem;">Pixel-verified tracking</div>
    </div>

    <div class="admin-table-wrap" style="padding: 1.25rem;">
      <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Average CTR</div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #a855f7; margin-top: 0.25rem; font-family: 'Outfit', sans-serif;">
        <?= $stats['avg_ctr'] ?>
      </div>
      <div style="font-size: 0.75rem; color: #c084fc; margin-top: 0.2rem;">Direct link click-throughs</div>
    </div>

    <div class="admin-table-wrap" style="padding: 1.25rem;">
      <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Delivery Dispatches</div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #34d399; margin-top: 0.25rem; font-family: 'Outfit', sans-serif;">
        <?= number_format($stats['total_dispatches'] ?? 0) ?>
      </div>
      <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">
        <span style="color: #10b981; font-weight: 700;"><?= number_format(($stats['delivered_cnt'] ?? 0) + ($stats['opened_cnt'] ?? 0) + ($stats['clicked_cnt'] ?? 0)) ?> sent</span> &bull; 
        <span style="color: <?= ($stats['failed_cnt'] ?? 0) > 0 ? '#ef4444' : '#64748b' ?>; font-weight: 700;"><?= number_format($stats['failed_cnt'] ?? 0) ?> failed</span>
      </div>
    </div>
  </div>

  <!-- Newsletter Tabs Strip (7 Modules) -->
  <div class="admin-table-wrap" style="padding: 0.5rem; margin-bottom: 1.5rem; overflow-x: auto;">
    <div style="display: flex; gap: 0.35rem; min-width: max-content;">
      <?php
      $nTabs = [
        'newsletters' => ['label' => 'Newsletters & Campaigns (' . count($issues) . ')', 'icon' => 'mail'],
        'subscribers' => ['label' => 'Subscribers Roster (' . count($subscribers) . ')', 'icon' => 'users'],
        'tracking'    => ['label' => 'Delivery Logs & Telemetry (' . count($deliveryLogs ?? []) . ')', 'icon' => 'activity'],
        'composer'    => ['label' => 'Compose & Send Studio ⚡', 'icon' => 'send'],
        'templates'   => ['label' => 'Digest Presets', 'icon' => 'layout-template'],
        'scheduling'  => ['label' => 'Scheduled Queue', 'icon' => 'calendar-clock'],
        'analytics'   => ['label' => 'Audience Telemetry', 'icon' => 'bar-chart-2'],
      ];
      ?>

      <?php foreach ($nTabs as $k => $t): ?>
        <button type="button" onclick="switchNewsletterTab('<?= $k ?>')" id="ntab_<?= $k ?>" class="ts-btn ts-btn-sm <?= $activeTab === $k ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.8rem; padding: 0.45rem 0.95rem; display: flex; align-items: center; gap: 0.45rem;">
          <i data-lucide="<?= $t['icon'] ?>" style="width: 15px; height: 15px;"></i>
          <span><?= $t['label'] ?></span>
        </button>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 1. SUBSCRIBERS ROSTER TAB -->
  <!-- ========================================================================= -->
  <div class="news-pane" id="npane_subscribers" style="<?= $activeTab === 'subscribers' ? 'display:block;' : 'display:none;' ?>">
    <div class="admin-table-wrap">
      <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); flex-wrap: wrap; gap: 1rem;">
        <div>
          <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
            <span>👥 Verified Subscriber Registry</span>
            <span class="ts-badge ts-badge-indigo" style="font-size: 0.72rem;"><?= count($subscribers) ?> Displayed</span>
          </h3>
          <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Browse opt-in newsletter readers, send direct digests or messages, and toggle delivery preferences.</p>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
          <button type="button" onclick="switchNewsletterTab('newsletters')" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3);">
            <i data-lucide="mail"></i> View Campaigns (<?= count($issues) ?>) &rarr;
          </button>
          <button type="button" onclick="openAddSubscriberModal()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8;">
            <i data-lucide="user-plus"></i> Add Subscriber +
          </button>
          <button type="button" onclick="prepareBroadcastToAll()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #10b981, #38bdf8); font-weight: 700;">
            <i data-lucide="send"></i> Broadcast to All Active 🚀
          </button>
        </div>
      </div>

      <!-- Filter & Search Toolbar -->
      <div style="padding: 1rem 1.5rem; background: rgba(15,23,42,0.5); border-bottom: 1px solid var(--admin-border-subtle); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
        <form method="GET" action="<?= url('/admin/newsletters') ?>" style="display: flex; gap: 0.5rem; flex: 1; max-width: 460px;">
          <input type="hidden" name="tab" value="subscribers">
          <input type="text" name="q" value="<?= htmlspecialchars($currentSearch ?? '') ?>" placeholder="Search by email, username or name..." class="admin-modal-input" style="font-size: 0.82rem; padding: 0.4rem 0.75rem;">
          <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8;"><i data-lucide="search"></i></button>
          <?php if (!empty($currentSearch) || !empty($currentStatus) || !empty($currentSegment)): ?>
            <a href="<?= url('/admin/newsletters?tab=subscribers') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #ef4444;" title="Clear filters">✕</a>
          <?php endif; ?>
        </form>

        <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
          <!-- Status filter pills -->
          <a href="<?= url('/admin/newsletters?tab=subscribers' . ($currentSearch ? '&q=' . urlencode($currentSearch) : '')) ?>" class="ts-btn ts-btn-sm <?= empty($currentStatus) ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.6rem;">
            All Status
          </a>
          <a href="<?= url('/admin/newsletters?tab=subscribers&status=subscribed' . ($currentSearch ? '&q=' . urlencode($currentSearch) : '')) ?>" class="ts-btn ts-btn-sm <?= ($currentStatus === 'subscribed') ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.6rem; color: #10b981;">
            ✓ Subscribed
          </a>
          <a href="<?= url('/admin/newsletters?tab=subscribers&status=unsubscribed' . ($currentSearch ? '&q=' . urlencode($currentSearch) : '')) ?>" class="ts-btn ts-btn-sm <?= ($currentStatus === 'unsubscribed') ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.6rem; color: #f43f5e;">
            ✕ Unsubscribed
          </a>
        </div>
      </div>

      <div style="overflow-x: auto;">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Subscriber &amp; Linked User</th>
              <th>Email Address</th>
              <th>Subscription Segment</th>
              <th>Status</th>
              <th>Joined Date</th>
              <th style="text-align: right;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($subscribers)): ?>
              <?php foreach ($subscribers as $sub): ?>
                <?php 
                  $hasUser = !empty($sub['user_id']);
                  $displayName = !empty($sub['display_name']) ? $sub['display_name'] : ($sub['username'] ?? explode('@', $sub['email'])[0]);
                ?>
                <tr>
                  <td>
                    <div style="display: flex; align-items: center; gap: 0.6rem;">
                      <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #38bdf8); display: flex; align-items: center; justify-content: center; font-weight: 700; color: #ffffff; font-size: 0.8rem; overflow: hidden; flex-shrink: 0;">
                        <?php if (!empty($sub['avatar_url'])): ?>
                          <img src="<?= htmlspecialchars(\App\Core\Config::url($sub['avatar_url'])) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                          <?= strtoupper(substr($displayName, 0, 1)) ?>
                        <?php endif; ?>
                      </div>
                      <div>
                        <div style="font-weight: 700; color: #f8fafc; font-size: 0.9rem;">
                          <?= htmlspecialchars($displayName) ?>
                          <?php if ($hasUser): ?>
                            <span class="ts-badge ts-badge-indigo" style="font-size: 0.65rem; padding: 0.1rem 0.35rem; margin-left: 0.3rem;">@<?= htmlspecialchars($sub['username']) ?></span>
                            <?php if (!empty($sub['role'])): ?>
                              <span class="ts-badge ts-badge-cyan" style="font-size: 0.62rem; padding: 0.1rem 0.3rem;"><?= strtoupper(htmlspecialchars($sub['role'])) ?></span>
                            <?php endif; ?>
                          <?php else: ?>
                            <span style="font-size: 0.72rem; color: #64748b; margin-left: 0.3rem;">Guest Opt-In</span>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span style="font-family: var(--admin-font-mono); color: #38bdf8; font-size: 0.85rem;">
                      <?= htmlspecialchars($sub['email']) ?>
                    </span>
                  </td>
                  <td>
                    <span class="ts-badge ts-badge-cyan" style="font-size: 0.72rem;">
                      <?= strtoupper(htmlspecialchars(str_replace('_', ' ', $sub['subscriber_type'] ?: 'all'))) ?>
                    </span>
                  </td>
                  <td>
                    <span class="ts-badge <?= $sub['status'] === 'subscribed' ? 'ts-badge-green' : 'ts-badge-pink' ?>" style="font-size: 0.72rem;">
                      <?= strtoupper(htmlspecialchars($sub['status'])) ?>
                    </span>
                  </td>
                  <td style="font-size: 0.78rem; font-family: var(--admin-font-mono);">
                    <?= \App\Core\DateHelper::render($sub['created_at'], 'M j, Y H:i') ?>
                  </td>
                  <td style="text-align: right;">
                    <div style="display: inline-flex; gap: 0.4rem;">
                      <!-- Quick Send Direct Message / Digest button -->
                      <button type="button" 
                              onclick="sendDirectToSubscriber(<?= (int)$sub['id'] ?>, '<?= htmlspecialchars(addslashes($sub['email'])) ?>', '<?= htmlspecialchars(addslashes($displayName)) ?>')" 
                              class="ts-btn ts-btn-primary ts-btn-sm" 
                              style="font-size: 0.72rem; padding: 0.25rem 0.6rem; background: linear-gradient(135deg, #6366f1, #38bdf8); font-weight: 700;" 
                              title="Send direct digest or customized message to this subscriber">
                        <i data-lucide="send" style="width: 12px; height: 12px;"></i> Send Message
                      </button>

                      <!-- View Individual Delivery Logs -->
                      <a href="<?= url('/admin/newsletters?tab=tracking&log_q=' . urlencode($sub['email'])) ?>" 
                         class="ts-btn ts-btn-outline ts-btn-sm" 
                         style="font-size: 0.72rem; padding: 0.25rem 0.5rem; color: #a855f7; border-color: rgba(168,85,247,0.3);" 
                         title="View individual delivery history, open events & click tracking">
                        <i data-lucide="activity" style="width: 12px; height: 12px;"></i> Logs
                      </a>

                      <!-- Toggle Status (Subscribed / Unsubscribed) -->
                      <form action="<?= url('/admin/newsletters/toggle-status/' . $sub['id']) ?>" method="POST" style="display: inline;">
                        <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.5rem; color: <?= $sub['status'] === 'subscribed' ? '#f59e0b' : '#10b981' ?>;" title="Toggle subscription state">
                          <?= $sub['status'] === 'subscribed' ? 'Unsub' : 'Resub' ?>
                        </button>
                      </form>

                      <!-- Remove -->
                      <form action="<?= url('/admin/subscribers/delete/' . $sub['id']) ?>" method="POST" onsubmit="return confirm('Remove subscriber <?= htmlspecialchars(addslashes($sub['email'])) ?>?');" style="display: inline;">
                        <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.5rem; color: #ef4444;" title="Delete subscriber record">
                          🗑
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6" style="text-align: center; padding: 3rem; color: #64748b;">No subscribers match your search query.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- DELIVERY TELEMETRY & AUDIT LOGS TAB -->
  <!-- ========================================================================= -->
  <div class="news-pane" id="npane_tracking" style="<?= $activeTab === 'tracking' ? 'display:block;' : 'display:none;' ?>">
    <div class="admin-table-wrap" style="margin-bottom: 1.5rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); flex-wrap: wrap; gap: 1rem;">
        <div>
          <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
            <span class="ts-badge ts-badge-purple">DELIVERY AUDIT &amp; CLICK TELEMETRY</span>
            <span style="font-size: 0.8rem; color: #64748b;">Live SMTP/API relay logging &bull; 1x1 GIF tracking &bull; Link redirect tracking</span>
          </div>
          <h3 style="font-size: 1.25rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif;">
            📊 Recipient Delivery Telemetry &amp; Interaction Logs
          </h3>
          <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">
            Track exactly to whom emails delivered, who opened, what links they clicked, and exact error diagnostics for any failures.
          </p>
        </div>

        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
          <a href="<?= url('/admin/newsletters?tab=tracking') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8;">
            <i data-lucide="refresh-cw"></i> Refresh Telemetry
          </a>
          <button type="button" onclick="switchNewsletterTab('composer')" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #6366f1, #38bdf8); font-weight: 700;">
            <i data-lucide="send"></i> Compose New Broadcast ⚡
          </button>
        </div>
      </div>

      <!-- Telemetry Sub-Metrics Bar -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.75rem; padding: 1.25rem; background: rgba(15,23,42,0.5); border-bottom: 1px solid var(--admin-border-subtle);">
        <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(148,163,184,0.12); border-radius: 8px; padding: 0.85rem; text-align: center;">
          <div style="font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; font-weight: 700;">Total Dispatched</div>
          <div style="font-size: 1.45rem; font-weight: 800; color: #f8fafc; font-family: var(--admin-font-mono); margin-top: 0.2rem;">
            <?= number_format($stats['total_dispatches'] ?? 0) ?>
          </div>
        </div>

        <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(16,185,129,0.3); border-radius: 8px; padding: 0.85rem; text-align: center;">
          <div style="font-size: 0.7rem; color: #34d399; text-transform: uppercase; font-weight: 700;">Delivered</div>
          <div style="font-size: 1.45rem; font-weight: 800; color: #10b981; font-family: var(--admin-font-mono); margin-top: 0.2rem;">
            <?= number_format(($stats['delivered_cnt'] ?? 0) + ($stats['opened_cnt'] ?? 0) + ($stats['clicked_cnt'] ?? 0)) ?>
          </div>
        </div>

        <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(56,189,248,0.3); border-radius: 8px; padding: 0.85rem; text-align: center;">
          <div style="font-size: 0.7rem; color: #38bdf8; text-transform: uppercase; font-weight: 700;">Opened (<?= $stats['avg_open_rate'] ?>)</div>
          <div style="font-size: 1.45rem; font-weight: 800; color: #38bdf8; font-family: var(--admin-font-mono); margin-top: 0.2rem;">
            <?= number_format($stats['opened_cnt'] ?? 0) ?>
          </div>
        </div>

        <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(168,85,247,0.3); border-radius: 8px; padding: 0.85rem; text-align: center;">
          <div style="font-size: 0.7rem; color: #c084fc; text-transform: uppercase; font-weight: 700;">Clicked (<?= $stats['avg_ctr'] ?>)</div>
          <div style="font-size: 1.45rem; font-weight: 800; color: #a855f7; font-family: var(--admin-font-mono); margin-top: 0.2rem;">
            <?= number_format($stats['clicked_cnt'] ?? 0) ?>
          </div>
        </div>

        <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(239,68,68,0.25); border-radius: 8px; padding: 0.85rem; text-align: center;">
          <div style="font-size: 0.7rem; color: #f87171; text-transform: uppercase; font-weight: 700;">Failed / Bounced</div>
          <div style="font-size: 1.45rem; font-weight: 800; color: #ef4444; font-family: var(--admin-font-mono); margin-top: 0.2rem;">
            <?= number_format($stats['failed_cnt'] ?? 0) ?>
          </div>
        </div>

        <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(245,158,11,0.25); border-radius: 8px; padding: 0.85rem; text-align: center;">
          <div style="font-size: 0.7rem; color: #f59e0b; text-transform: uppercase; font-weight: 700;">Total Clicks</div>
          <div style="font-size: 1.45rem; font-weight: 800; color: #f59e0b; font-family: var(--admin-font-mono); margin-top: 0.2rem;">
            <?= number_format($stats['total_clicks'] ?? 0) ?>
          </div>
        </div>
      </div>

      <!-- Filter Controls Bar -->
      <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; background: rgba(2,6,23,0.4); border-bottom: 1px solid var(--admin-border-subtle); flex-wrap: wrap; gap: 1rem;">
        <form method="GET" action="<?= url('/admin/newsletters') ?>" style="display: flex; gap: 0.5rem; flex: 1; max-width: 540px; align-items: center; flex-wrap: wrap;">
          <input type="hidden" name="tab" value="tracking">
          <?php if (!empty($currentLogStatus)): ?>
            <input type="hidden" name="log_status" value="<?= htmlspecialchars($currentLogStatus) ?>">
          <?php endif; ?>
          <input type="text" name="log_q" value="<?= htmlspecialchars($currentLogSearch ?? '') ?>" placeholder="Search by recipient email, name, or subject..." class="admin-modal-input" style="flex: 1; min-width: 220px; padding: 0.4rem 0.75rem; font-size: 0.85rem;">
          
          <select name="log_issue_id" class="admin-modal-select" style="width: auto; max-width: 180px; padding: 0.4rem 0.75rem; font-size: 0.82rem;" onchange="this.form.submit()">
            <option value="">All Campaigns</option>
            <?php foreach ($issues as $issOpt): ?>
              <option value="<?= $issOpt['id'] ?>" <?= ((int)($currentLogIssueId ?? 0) === (int)$issOpt['id']) ? 'selected' : '' ?>>
                #<?= $issOpt['issue_number'] ?? $issOpt['id'] ?> <?= htmlspecialchars(substr($issOpt['title'], 0, 24)) ?>
              </option>
            <?php endforeach; ?>
          </select>

          <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">
            <i data-lucide="search" style="width: 14px; height: 14px;"></i> Filter
          </button>
          <?php if (!empty($currentLogSearch) || !empty($currentLogStatus) || !empty($currentLogIssueId)): ?>
            <a href="<?= url('/admin/newsletters?tab=tracking') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #ef4444;" title="Clear filters">✕</a>
          <?php endif; ?>
        </form>

        <!-- Status Filter Pills -->
        <div style="display: flex; gap: 0.35rem; align-items: center; flex-wrap: wrap;">
          <span style="font-size: 0.75rem; color: #64748b; margin-right: 0.25rem;">Status:</span>
          <a href="<?= url('/admin/newsletters?tab=tracking' . (!empty($currentLogSearch) ? '&log_q=' . urlencode($currentLogSearch) : '') . (!empty($currentLogIssueId) ? '&log_issue_id=' . $currentLogIssueId : '')) ?>" 
             class="ts-btn ts-btn-sm <?= empty($currentLogStatus) ? 'ts-btn-primary' : 'ts-btn-outline' ?>" 
             style="font-size: 0.72rem; padding: 0.25rem 0.6rem;">
            All
          </a>
          <a href="<?= url('/admin/newsletters?tab=tracking&log_status=delivered' . (!empty($currentLogSearch) ? '&log_q=' . urlencode($currentLogSearch) : '') . (!empty($currentLogIssueId) ? '&log_issue_id=' . $currentLogIssueId : '')) ?>" 
             class="ts-btn ts-btn-sm <?= ($currentLogStatus === 'delivered') ? 'ts-btn-primary' : 'ts-btn-outline' ?>" 
             style="font-size: 0.72rem; padding: 0.25rem 0.6rem; color: #10b981;">
            ✓ Delivered
          </a>
          <a href="<?= url('/admin/newsletters?tab=tracking&log_status=opened' . (!empty($currentLogSearch) ? '&log_q=' . urlencode($currentLogSearch) : '') . (!empty($currentLogIssueId) ? '&log_issue_id=' . $currentLogIssueId : '')) ?>" 
             class="ts-btn ts-btn-sm <?= ($currentLogStatus === 'opened') ? 'ts-btn-primary' : 'ts-btn-outline' ?>" 
             style="font-size: 0.72rem; padding: 0.25rem 0.6rem; color: #38bdf8;">
            👁️ Opened
          </a>
          <a href="<?= url('/admin/newsletters?tab=tracking&log_status=clicked' . (!empty($currentLogSearch) ? '&log_q=' . urlencode($currentLogSearch) : '') . (!empty($currentLogIssueId) ? '&log_issue_id=' . $currentLogIssueId : '')) ?>" 
             class="ts-btn ts-btn-sm <?= ($currentLogStatus === 'clicked') ? 'ts-btn-primary' : 'ts-btn-outline' ?>" 
             style="font-size: 0.72rem; padding: 0.25rem 0.6rem; color: #a855f7;">
            🖱️ Clicked
          </a>
          <a href="<?= url('/admin/newsletters?tab=tracking&log_status=failed' . (!empty($currentLogSearch) ? '&log_q=' . urlencode($currentLogSearch) : '') . (!empty($currentLogIssueId) ? '&log_issue_id=' . $currentLogIssueId : '')) ?>" 
             class="ts-btn ts-btn-sm <?= ($currentLogStatus === 'failed') ? 'ts-btn-primary' : 'ts-btn-outline' ?>" 
             style="font-size: 0.72rem; padding: 0.25rem 0.6rem; color: #ef4444;">
            ⚠️ Failed
          </a>
        </div>
      </div>

      <!-- Delivery Logs Table -->
      <div class="admin-table-container">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Recipient Profile</th>
              <th>Campaign / Subject</th>
              <th>Delivery Status &amp; Telemetry</th>
              <th>Timestamps</th>
              <th>Client Footprint</th>
              <th style="text-align: right;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($deliveryLogs)): ?>
              <?php foreach ($deliveryLogs as $log): ?>
                <tr>
                  <td>
                    <div style="display: flex; align-items: center; gap: 0.6rem;">
                      <div style="width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #1e293b, #334155); display: flex; align-items: center; justify-content: center; color: #38bdf8; font-weight: 700; font-size: 0.85rem; border: 1px solid rgba(56,189,248,0.2);">
                        <?= strtoupper(substr($log['to_name'] ?: ($log['to_email'] ?? 'U'), 0, 1)) ?>
                      </div>
                      <div>
                        <div style="font-weight: 700; color: #f8fafc; font-size: 0.9rem;">
                          <?= htmlspecialchars($log['to_name'] ?: explode('@', $log['to_email'])[0]) ?>
                        </div>
                        <div style="font-size: 0.78rem; color: #94a3b8; font-family: var(--admin-font-mono);">
                          <?= htmlspecialchars($log['to_email']) ?>
                        </div>
                      </div>
                    </div>
                  </td>

                  <td>
                    <div style="font-weight: 600; color: #e2e8f0; font-size: 0.88rem;">
                      <?= htmlspecialchars($log['campaign_title'] ?: 'Direct Broadcast') ?>
                    </div>
                    <?php if (!empty($log['issue_id'])): ?>
                      <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.15rem;">
                        <span class="ts-badge ts-badge-indigo" style="font-size: 0.65rem; padding: 0.1rem 0.35rem;">Issue #<?= $log['issue_id'] ?></span>
                      </div>
                    <?php endif; ?>
                  </td>

                  <td>
                    <div style="display: flex; flex-direction: column; gap: 0.3rem;">
                      <?php if ($log['status'] === 'clicked'): ?>
                        <span class="ts-badge" style="background: rgba(168,85,247,0.2); color: #c084fc; border: 1px solid rgba(168,85,247,0.3); font-weight: 700; font-size: 0.75rem;">
                          🖱️ CLICKED (<?= (int)$log['click_count'] ?>x)
                        </span>
                        <?php if (!empty($log['last_clicked_url'])): ?>
                          <div style="font-size: 0.72rem; color: #38bdf8; max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($log['last_clicked_url']) ?>">
                            🔗 <a href="<?= htmlspecialchars($log['last_clicked_url']) ?>" target="_blank" style="color: #38bdf8; text-decoration: underline;"><?= htmlspecialchars(parse_url($log['last_clicked_url'], PHP_URL_PATH) ?: $log['last_clicked_url']) ?></a>
                          </div>
                        <?php endif; ?>
                      <?php elseif ($log['status'] === 'opened'): ?>
                        <span class="ts-badge ts-badge-cyan" style="font-weight: 700; font-size: 0.75rem;">
                          👁️ OPENED (<?= (int)$log['open_count'] ?>x)
                        </span>
                      <?php elseif ($log['status'] === 'delivered'): ?>
                        <span class="ts-badge ts-badge-green" style="font-weight: 700; font-size: 0.75rem;">
                          ✓ DELIVERED
                        </span>
                      <?php else: ?>
                        <span class="ts-badge ts-badge-pink" style="font-weight: 700; font-size: 0.75rem;">
                          ⚠️ FAILED
                        </span>
                      <?php endif; ?>

                      <?php if (!empty($log['error_message'])): ?>
                        <div style="font-size: 0.72rem; color: #f87171; background: rgba(239,68,68,0.1); padding: 0.25rem 0.5rem; border-radius: 4px; max-width: 240px; word-break: break-all; border-left: 2px solid #ef4444;" title="<?= htmlspecialchars($log['error_message']) ?>">
                          <?= htmlspecialchars(substr($log['error_message'], 0, 90)) ?><?= strlen($log['error_message']) > 90 ? '...' : '' ?>
                        </div>
                      <?php endif; ?>
                    </div>
                  </td>

                  <td>
                    <div style="font-size: 0.78rem; color: #cbd5e1; font-family: var(--admin-font-mono);">
                      <div><strong>Dispatched:</strong> <?= \App\Core\DateHelper::render($log['dispatched_at'], 'M j, H:i:s') ?></div>
                      <?php if (!empty($log['opened_at'])): ?>
                        <div style="color: #38bdf8;"><strong>Opened:</strong> <?= \App\Core\DateHelper::render($log['opened_at'], 'M j, H:i:s') ?></div>
                      <?php endif; ?>
                      <?php if (!empty($log['clicked_at'])): ?>
                        <div style="color: #c084fc;"><strong>Clicked:</strong> <?= \App\Core\DateHelper::render($log['clicked_at'], 'M j, H:i:s') ?></div>
                      <?php endif; ?>
                    </div>
                  </td>

                  <td>
                    <div style="font-size: 0.75rem; color: #94a3b8; font-family: var(--admin-font-mono);">
                      <?php if (!empty($log['user_ip'])): ?>
                        <div><code style="color: #38bdf8;"><?= htmlspecialchars($log['user_ip']) ?></code></div>
                      <?php else: ?>
                        <span style="color: #64748b;">Pending interaction</span>
                      <?php endif; ?>
                      <?php if (!empty($log['user_agent'])): ?>
                        <div style="font-size: 0.7rem; color: #64748b; max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($log['user_agent']) ?>">
                          <?= htmlspecialchars(substr($log['user_agent'], 0, 32)) ?>
                        </div>
                      <?php endif; ?>
                    </div>
                  </td>

                  <td style="text-align: right;">
                    <div style="display: inline-flex; gap: 0.4rem;">
                      <!-- Retry Dispatch -->
                      <form action="<?= url('/admin/newsletters/retry-delivery/' . $log['id']) ?>" method="POST" style="display: inline;">
                        <button type="submit" 
                                class="ts-btn ts-btn-sm <?= $log['status'] === 'failed' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" 
                                style="font-size: 0.72rem; padding: 0.25rem 0.55rem; <?= $log['status'] === 'failed' ? 'background: #ef4444; border-color: #ef4444; font-weight: 700;' : 'color: #38bdf8;' ?>" 
                                title="Re-transmit delivery packet to this recipient">
                          <i data-lucide="refresh-cw" style="width: 12px; height: 12px;"></i> Retry ⚡
                        </button>
                      </form>

                      <!-- Compose Direct Message -->
                      <button type="button" 
                              onclick="sendDirectToSubscriber(<?= (int)($log['subscriber_id'] ?? 0) ?>, '<?= htmlspecialchars(addslashes($log['to_email'])) ?>', '<?= htmlspecialchars(addslashes($log['to_name'] ?: 'Subscriber')) ?>')" 
                              class="ts-btn ts-btn-outline ts-btn-sm" 
                              style="font-size: 0.72rem; padding: 0.25rem 0.5rem; color: #94a3b8;" 
                              title="Compose customized message to this subscriber">
                        <i data-lucide="send" style="width: 12px; height: 12px;"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" style="text-align: center; padding: 3.5rem 1.5rem; color: #64748b;">
                  <div style="font-size: 2rem; margin-bottom: 0.5rem;">📊</div>
                  <div style="font-weight: 700; color: #cbd5e1; margin-bottom: 0.25rem;">No Delivery Telemetry Records Found</div>
                  <div style="font-size: 0.85rem; max-width: 440px; margin: 0 auto;">
                    Delivery logs will populate in real time when you dispatch a campaign issue or direct broadcast from the Composer studio.
                  </div>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 2. COMPOSE & SEND STUDIO TAB (UNIFIED MAILING STUDIO IN NEWSLETTER) -->
  <!-- ========================================================================= -->
  <div class="news-pane" id="npane_composer" style="<?= $activeTab === 'composer' ? 'display:block;' : 'display:none;' ?>">
    <div class="admin-table-wrap" style="padding: 2rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
        <div>
          <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
            <span class="ts-badge ts-badge-pink">LIVE MESSAGE &amp; DIGEST DISPATCHER</span>
            <span style="font-size: 0.8rem; color: #64748b;">Dynamic variables injected per recipient</span>
          </div>
          <h3 style="font-size: 1.35rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif;" id="composerFormTitle">
            ⚡ Compose &amp; Dispatch Newsletter Digest
          </h3>
        </div>

        <!-- Live Preview Mode Toggle -->
        <div style="display: flex; gap: 0.4rem; background: rgba(15,23,42,0.8); border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 0.25rem;">
          <button type="button" onclick="setComposerMode('editor')" id="mode_btn_editor" class="ts-btn ts-btn-primary ts-btn-sm" style="font-size: 0.75rem; padding: 0.3rem 0.75rem;">
            <i data-lucide="code"></i> HTML Editor
          </button>
          <button type="button" onclick="setComposerMode('preview')" id="mode_btn_preview" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem; padding: 0.3rem 0.75rem;">
            <i data-lucide="eye"></i> Live Render Preview
          </button>
        </div>
      </div>

      <!-- Direct Message & Broadcast Form -->
      <form id="newsletterMessageForm" action="<?= url('/admin/newsletters/send-message') ?>" method="POST">
        <input type="hidden" name="single_subscriber_id" id="single_subscriber_id" value="">

        <!-- Recipient & Routing Configuration Grid -->
        <div style="background: rgba(15,23,42,0.6); border: 1px solid var(--admin-border-subtle); border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem;">
          <h4 style="font-size: 0.95rem; color: #38bdf8; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.4rem;">
            <i data-lucide="target" style="width: 16px; height: 16px;"></i> 1. Target Audience &amp; Mail Credential
          </h4>

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem;">
            <!-- Audience Selection -->
            <div>
              <label class="admin-modal-label">Dispatch Target Audience</label>
              <select name="recipient_target" id="recipient_target" class="admin-modal-select" onchange="updateRecipientTargetUI()" required>
                <option value="all_active">🚀 All Active Subscribers (<?= number_format($stats['active']) ?> verified)</option>
                <option value="single_subscriber">👤 Single Selected Subscriber</option>
                <option value="segment">🏷️ By Subscriber Segment</option>
                <option value="custom_email">✉️ Single Custom Email Address</option>
              </select>
            </div>

            <!-- Conditional: Segment Picker -->
            <div id="wrapper_segment" style="display: none;">
              <label class="admin-modal-label">Subscriber Segment</label>
              <select name="target_segment" id="target_segment" class="admin-modal-select">
                <option value="all">All Broadcasts Opt-in</option>
                <option value="weekly_digest">Weekly Engineering Digest Only</option>
                <option value="ai_tools_only">AI Tools Radar Only</option>
              </select>
            </div>

            <!-- Conditional: Single Subscriber Picker -->
            <div id="wrapper_subscriber_picker" style="display: none;">
              <label class="admin-modal-label">Select Subscriber</label>
              <select id="subscriber_dropdown" class="admin-modal-select" onchange="onSubscriberDropdownChange()">
                <option value="">-- Choose Subscriber --</option>
                <?php foreach ($subscribers as $s): ?>
                  <option value="<?= (int)$s['id'] ?>" data-email="<?= htmlspecialchars($s['email']) ?>" data-name="<?= htmlspecialchars(!empty($s['display_name']) ? $s['display_name'] : ($s['username'] ?? explode('@', $s['email'])[0])) ?>">
                    <?= htmlspecialchars($s['email']) ?> (<?= htmlspecialchars(!empty($s['display_name']) ? $s['display_name'] : ($s['username'] ?? 'Subscriber')) ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Conditional: Custom Email -->
            <div id="wrapper_custom_email" style="display: none;">
              <label class="admin-modal-label">Recipient Email Address</label>
              <input type="email" name="custom_email" id="custom_email" placeholder="engineer@example.com" class="admin-modal-input">
            </div>

            <!-- Mail Credential Provider -->
            <div>
              <label class="admin-modal-label">Mail Delivery Credential Profile</label>
              <select name="credential_id" id="credential_id" class="admin-modal-select">
                <option value="">⚡ Platform Default Active Profile</option>
                <?php foreach ($mailCredentials as $mc): ?>
                  <option value="<?= (int)$mc['id'] ?>" <?= !empty($mc['is_default']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($mc['name']) ?> (<?= strtoupper(htmlspecialchars($mc['provider'])) ?><?= !empty($mc['is_default']) ? ' &bull; Default' : '' ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>

        <!-- Digest Presets & Template Fast Selector -->
        <div style="background: rgba(15,23,42,0.4); border: 1px solid var(--admin-border-subtle); border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
            <h4 style="font-size: 0.95rem; color: #a855f7; margin: 0; display: flex; align-items: center; gap: 0.4rem;">
              <i data-lucide="layout-template" style="width: 16px; height: 16px;"></i> 2. Digest Presets &amp; Template Library
            </h4>
            <span style="font-size: 0.75rem; color: #94a3b8;">Click any preset below to auto-populate layout</span>
          </div>

          <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <button type="button" onclick="loadDigestPreset('weekly_digest')" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3);">
              ⚡ Weekly Engineering Digest
            </button>
            <button type="button" onclick="loadDigestPreset('breaking_alert')" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #f43f5e; border-color: rgba(244,63,94,0.3);">
              🚨 Breaking AI Model Radar
            </button>
            <button type="button" onclick="loadDigestPreset('tool_spotlight')" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #10b981; border-color: rgba(16,185,129,0.3);">
              🛠️ Tool &amp; GPT Spotlight
            </button>
            <button type="button" onclick="loadDigestPreset('community_broadcast')" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #ec4899; border-color: rgba(236,72,153,0.3);">
              📢 Platform Intelligence Announcement
            </button>
          </div>
        </div>

        <!-- 3. Customizable Message Variables Studio -->
        <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(99,102,241,0.25); border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
            <div>
              <h4 style="font-size: 0.95rem; color: #6366f1; margin: 0; display: flex; align-items: center; gap: 0.4rem;">
                <i data-lucide="variable" style="width: 16px; height: 16px;"></i> 3. Customizable Variables Studio (Per-Message Dynamic Values)
              </h4>
              <p style="font-size: 0.75rem; color: #94a3b8; margin: 0.2rem 0 0 0;">These values will substitute into your email template variables. Click any tag pill below to insert it into Subject or Body.</p>
            </div>
            <button type="button" onclick="toggleVariablesDrawer()" id="btn_toggle_vars" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem;">
              Expand Inputs ▾
            </button>
          </div>

          <!-- Clickable Variable Tags Bar -->
          <div style="display: flex; gap: 0.35rem; flex-wrap: wrap; margin-bottom: 1rem; padding: 0.6rem; background: rgba(2,6,23,0.6); border-radius: 8px; border: 1px dashed rgba(148,163,184,0.15);">
            <span style="font-size: 0.72rem; color: #64748b; align-self: center; margin-right: 0.3rem;">Insert Variable:</span>
            <?php
            $pills = [
              '{{name}}' => 'Recipient Name',
              '{{headline}}' => 'Digest Headline',
              '{{message}}' => 'Custom Editorial Body',
              '{{article_title}}' => 'Article Title',
              '{{action_url}}' => 'CTA Link',
              '{{action_text}}' => 'CTA Button Label',
              '{{digest_summary}}' => 'Digest Bullets',
              '{{email}}' => 'Recipient Email',
              '{{unsubscribe_url}}' => 'Unsubscribe Link',
              '{{site_name}}' => 'Site Name',
              '{{date}}' => 'Current Date'
            ];
            foreach ($pills as $varTag => $varDesc):
            ?>
              <button type="button" onclick="insertVariableTag('<?= $varTag ?>')" class="ts-badge" style="background: rgba(99,102,241,0.15); border: 1px solid rgba(99,102,241,0.3); color: #818cf8; cursor: pointer; font-size: 0.72rem; padding: 0.25rem 0.5rem;" title="<?= htmlspecialchars($varDesc) ?>">
                <?= $varTag ?>
              </button>
            <?php endforeach; ?>
          </div>

          <!-- Variable Input Fields Drawer -->
          <div id="variables_inputs_drawer" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
            <div>
              <label class="admin-modal-label">Main Headline (<code>{{headline}}</code>)</label>
              <input type="text" name="var_headline" id="var_headline" value="TechScout Architecture Digest #48" placeholder="e.g. Weekly Tech Radar & Architecture Blueprints" class="admin-modal-input" oninput="updateLivePreview()">
            </div>

            <div>
              <label class="admin-modal-label">Call-to-Action Link URL (<code>{{action_url}}</code>)</label>
              <input type="text" name="var_action_url" id="var_action_url" value="<?= url('/topics') ?>" placeholder="https://..." class="admin-modal-input" oninput="updateLivePreview()">
            </div>

            <div>
              <label class="admin-modal-label">Button Label (<code>{{action_text}}</code>)</label>
              <input type="text" name="var_action_text" id="var_action_text" value="Read Full Blueprint →" class="admin-modal-input" oninput="updateLivePreview()">
            </div>

            <div>
              <label class="admin-modal-label">Featured Article/Tool Title (<code>{{article_title}}</code>)</label>
              <input type="text" name="var_article_title" id="var_article_title" value="Zero-Downtime PostgreSQL Index Tuning" class="admin-modal-input" oninput="updateLivePreview()">
            </div>

            <div style="grid-column: 1 / -1;">
              <label class="admin-modal-label">Custom Lead Message / Editorial (<code>{{message}}</code>)</label>
              <textarea name="var_message" id="var_message" rows="2" class="admin-modal-textarea" style="font-size: 0.85rem;" oninput="updateLivePreview()">Welcome to this week's curated technical edition. In this issue we dissect high-throughput schema migrations, review top AI code agents, and benchmark local LLM token costs.</textarea>
            </div>

            <div style="grid-column: 1 / -1;">
              <label class="admin-modal-label">Digest Highlights / Bullet Points (<code>{{digest_summary}}</code>)</label>
              <textarea name="var_digest_summary" id="var_digest_summary" rows="2" class="admin-modal-textarea" style="font-size: 0.85rem;" oninput="updateLivePreview()">• Zero-Downtime Indexing under 40,000 writes/sec
• Claude 3.5 Sonnet XML Prompt Stacks for Backend Engineering
• 5 Open-Source Alternatives to OpenAI GPTs with Local Ollama Support</textarea>
            </div>
          </div>
        </div>

        <!-- 4. Quick Article Block Inserter -->
        <div style="background: rgba(15,23,42,0.4); padding: 1rem 1.25rem; border-radius: 8px; border: 1px solid var(--admin-border-subtle); margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
          <div>
            <label class="admin-modal-label" style="margin: 0;">💡 Quick Inserter: Embed Published TechScout Article Card</label>
            <span style="font-size: 0.75rem; color: #64748b;">Pick any published article to append its formatted card directly into the HTML body</span>
          </div>
          <div style="display: flex; gap: 0.5rem; flex: 1; max-width: 500px;">
            <select id="articleQuickPicker" class="admin-modal-select" style="font-size: 0.82rem;">
              <?php foreach ($recentArticles as $ra): ?>
                <option value="<?= htmlspecialchars($ra['title']) ?>" data-slug="<?= htmlspecialchars($ra['slug']) ?>" data-summary="<?= htmlspecialchars($ra['summary']) ?>">
                  <?= htmlspecialchars($ra['title']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <button type="button" onclick="insertArticleIntoNewsletter()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; white-space: nowrap;">
              + Insert Card
            </button>
          </div>
        </div>

        <!-- 5. Subject & Email Content -->
        <div style="margin-bottom: 1.25rem;">
          <label class="admin-modal-label">Email Subject Line (Inbox Display)</label>
          <input type="text" name="subject" id="composer_subject" value="⚡ TechScout Dispatch: {{headline}}" placeholder="e.g. ⚡ TechScout Intelligence: {{headline}}" class="admin-modal-input" style="font-size: 1rem; font-weight: 600;" required oninput="updateLivePreview()">
        </div>

        <!-- Dual Pane: Editor / Live Preview -->
        <div id="pane_editor_view" style="margin-bottom: 1.5rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
            <label class="admin-modal-label" style="margin: 0;">Newsletter Body (HTML Layout &amp; Content)</label>
            <span style="font-size: 0.72rem; color: #64748b;">Supports inline HTML styling and all <code>{{variable}}</code> tags</span>
          </div>
          <textarea name="body_html" id="composer_body_html" rows="14" class="admin-modal-textarea" style="font-family: var(--admin-font-mono); font-size: 0.85rem; line-height: 1.55;" oninput="updateLivePreview()"><div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #f8fafc; max-width: 600px; margin: 0 auto;">
  <h2 style="color: #38bdf8; font-size: 24px; margin: 0 0 16px 0;">⚡ {{headline}}</h2>
  <p style="font-size: 16px; line-height: 1.6; color: #cbd5e1; margin-bottom: 20px;">
    Hello {{name}},
  </p>
  <p style="font-size: 15px; line-height: 1.6; color: #cbd5e1; margin-bottom: 24px;">
    {{message}}
  </p>

  <div style="background: #0f172a; border: 1px solid #1e293b; border-radius: 8px; padding: 20px; margin-bottom: 24px;">
    <h3 style="color: #f8fafc; font-size: 16px; margin: 0 0 10px 0;">Key Takeaways in this Issue:</h3>
    <div style="color: #94a3b8; font-size: 14px; line-height: 1.6;">
      {{digest_summary}}
    </div>
  </div>

  <div style="background: #0f172a; border-left: 4px solid #38bdf8; padding: 16px; border-radius: 4px; margin-bottom: 28px;">
    <span style="color: #38bdf8; font-size: 12px; text-transform: uppercase; font-weight: 700;">Featured Technical Blueprint</span>
    <h4 style="color: #f8fafc; font-size: 18px; margin: 6px 0 8px 0;">{{article_title}}</h4>
    <p style="color: #94a3b8; font-size: 14px; line-height: 1.5; margin: 0 0 12px 0;">Comprehensive breakdown with production benchmarks and code architecture.</p>
    <a href="{{action_url}}" style="display: inline-block; background: linear-gradient(135deg, #6366f1, #38bdf8); color: #ffffff; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-weight: 700; font-size: 14px;">
      {{action_text}}
    </a>
  </div>

  <p style="font-size: 12px; color: #64748b; text-align: center; border-top: 1px solid #1e293b; padding-top: 20px; margin-top: 30px;">
    You are receiving this digest because you subscribed to {{site_name}}.<br>
    <a href="{{unsubscribe_url}}" style="color: #94a3b8; text-decoration: underline;">Manage Preferences or Unsubscribe</a>
  </p>
</div></textarea>
        </div>

        <!-- Live Render Preview Pane -->
        <div id="pane_preview_view" style="display: none; margin-bottom: 1.5rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <label class="admin-modal-label" style="margin: 0; color: #34d399;">
              <i data-lucide="monitor" style="width: 14px; height: 14px; display: inline;"></i> Live Injected HTML Preview
            </label>
            <div style="display: flex; gap: 0.4rem;">
              <button type="button" onclick="setPreviewDevice('desktop')" id="dev_btn_desktop" class="ts-btn ts-btn-sm ts-btn-primary" style="font-size: 0.72rem; padding: 0.2rem 0.5rem;">Desktop</button>
              <button type="button" onclick="setPreviewDevice('mobile')" id="dev_btn_mobile" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.72rem; padding: 0.2rem 0.5rem;">Mobile (375px)</button>
            </div>
          </div>

          <div style="background: #020617; border: 1px solid rgba(148,163,184,0.15); border-radius: 12px; padding: 1rem; display: flex; justify-content: center; min-height: 400px;">
            <div id="previewContainer" style="width: 100%; max-width: 680px; background: #0b0f17; border: 1px solid #1e293b; border-radius: 8px; padding: 1.5rem; transition: max-width 0.25s ease; color: #f8fafc;">
              <!-- Simulated Email Client Header with Dynamic Subject -->
              <div style="background: rgba(15,23,42,0.85); border: 1px solid rgba(148,163,184,0.15); border-radius: 6px; padding: 0.85rem 1rem; margin-bottom: 1.25rem; font-size: 0.82rem; line-height: 1.6;">
                <div><span style="color: #64748b; font-weight: 600;">Subject:</span> <strong style="color: #f8fafc;" id="livePreviewSubject">⚡ TechScout Dispatch</strong></div>
                <div><span style="color: #64748b; font-weight: 600;">To:</span> <span style="color: #38bdf8;">Alex Rivera &lt;alex@example.com&gt;</span></div>
                <div><span style="color: #64748b; font-weight: 600;">From:</span> <span style="color: #94a3b8;">TechScout Intelligence &lt;notifications@techscout.online&gt;</span></div>
              </div>
              <div id="livePreviewRendered"></div>
            </div>
          </div>
        </div>

        <!-- Bottom Action Bar -->
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; border-top: 1px solid var(--admin-border-subtle); padding-top: 1.25rem;">
          <!-- 1-Click Test Dispatch -->
          <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
            <input type="email" id="testRecipientEmail" placeholder="test-admin@example.com" class="admin-modal-input" style="width: 220px; font-size: 0.82rem;">
            <button type="button" onclick="sendTestNewsletter()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3);">
              <i data-lucide="zap"></i> Send Test to Me
            </button>
          </div>

          <!-- Dispatch Controls -->
          <div style="display: flex; gap: 0.6rem;">
            <button type="reset" class="ts-btn ts-btn-outline ts-btn-sm">Reset</button>
            <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #6366f1, #38bdf8); font-weight: 700; font-size: 0.85rem; padding: 0.5rem 1.25rem;">
              <i data-lucide="send"></i> Dispatch Message to Audience 🚀
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 3. CAMPAIGNS & ISSUES TAB -->
  <!-- ========================================================================= -->
  <div class="news-pane" id="npane_newsletters" style="<?= $activeTab === 'newsletters' ? 'display:block;' : 'display:none;' ?>">
    <div class="admin-table-wrap">
      <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle);">
        <div>
          <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0;">📬 Active Broadcast Campaigns</h3>
          <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Recent newsletter broadcasts delivered to subscribers across all segments.</p>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
          <button type="button" onclick="switchNewsletterTab('subscribers')" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #10b981; border-color: rgba(16,185,129,0.3);">
            <i data-lucide="users"></i> View Subscribers Roster (<?= count($subscribers) ?>) &rarr;
          </button>
          <button type="button" onclick="switchNewsletterTab('composer')" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #6366f1, #38bdf8);">
            <i data-lucide="send"></i> Compose New Issue ⚡
          </button>
        </div>
      </div>

      <table class="admin-table">
        <thead>
          <tr>
            <th>Issue Title &amp; Subject</th>
            <th>Type</th>
            <th>Recipients</th>
            <th>Opens</th>
            <th>Clicks</th>
            <th>Status</th>
            <th>Dispatched</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($issues)): ?>
            <?php foreach ($issues as $iss): ?>
              <tr>
                <td>
                  <div style="font-weight: 700; color: #f8fafc;"><?= htmlspecialchars($iss['title']) ?></div>
                  <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 0.2rem;"><?= htmlspecialchars($iss['subject_line']) ?></div>
                </td>
                <td><span class="ts-badge ts-badge-indigo"><?= strtoupper(htmlspecialchars($iss['template_type'])) ?></span></td>
                <td style="font-family: var(--admin-font-mono); color: #cbd5e1;"><?= number_format((int)$iss['total_recipients']) ?></td>
                <td style="font-family: var(--admin-font-mono); color: #38bdf8;"><?= number_format((int)$iss['open_count']) ?></td>
                <td style="font-family: var(--admin-font-mono); color: #10b981;"><?= number_format((int)$iss['click_count']) ?></td>
                <td>
                  <span class="ts-badge <?= $iss['status'] === 'sent' ? 'ts-badge-green' : ($iss['status'] === 'scheduled' ? 'ts-badge-cyan' : 'ts-badge-pink') ?>">
                    <?= strtoupper(htmlspecialchars($iss['status'])) ?>
                  </span>
                </td>
                <td style="font-size: 0.78rem; font-family: var(--admin-font-mono);"><?= !empty($iss['sent_at']) ? \App\Core\DateHelper::render($iss['sent_at'], 'M j, Y H:i') : ($iss['scheduled_at'] ? 'Sched: ' . \App\Core\DateHelper::render($iss['scheduled_at'], 'M j, H:i') : 'Draft') ?></td>
                <td>
                  <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                    <button type="button" onclick="startLiveDispatch(<?= (int)$iss['id'] ?>, '<?= htmlspecialchars(addslashes($iss['title'])) ?>')" class="ts-btn ts-btn-primary ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.55rem; background: #10b981; border-color: #10b981; font-weight: 700;" title="Dispatch live to subscribers">
                      🚀 Send Live
                    </button>
                    <a href="<?= url('/admin/newsletters?tab=tracking&log_issue_id=' . $iss['id']) ?>" 
                       class="ts-btn ts-btn-outline ts-btn-sm" 
                       style="font-size: 0.72rem; padding: 0.25rem 0.55rem; color: #a855f7; border-color: rgba(168,85,247,0.3);" 
                       title="View recipient delivery telemetry and logs for this campaign">
                      <i data-lucide="activity" style="width: 12px; height: 12px;"></i> Logs
                    </a>
                    <button type="button" onclick='loadIssueIntoComposer(<?= json_encode($iss) ?>)' class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.55rem; color: #38bdf8;">
                      ✏️ Edit
                    </button>
                    <form action="<?= url('/admin/newsletters/delete/' . $iss['id']) ?>" method="POST" onsubmit="return confirm('Delete this issue record?');" style="display: inline;">
                      <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.55rem; color: #ef4444;">
                        🗑
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="8" style="text-align: center; padding: 2.5rem; color: #64748b;">No newsletter issues composed yet. Click "Compose &amp; Send Studio" to start.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 4. TEMPLATES TAB -->
  <!-- ========================================================================= -->
  <div class="news-pane" id="npane_templates" style="<?= $activeTab === 'templates' ? 'display:block;' : 'display:none;' ?>">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
      <div class="admin-table-wrap" style="padding: 1.5rem; display: flex; flex-direction: column;">
        <span class="ts-badge ts-badge-cyan" style="align-self: flex-start; margin-bottom: 0.75rem;">WEEKLY DIGEST</span>
        <h4 style="color: #f8fafc; font-size: 1.1rem; margin: 0 0 0.4rem 0;">Engineering Radar &amp; Architecture Digest</h4>
        <p style="color: #94a3b8; font-size: 0.85rem; flex: 1; margin-bottom: 1rem;">Curated multi-story layout featuring deep dive articles, top trending AI tools, and prompt blueprints.</p>
        <button type="button" onclick="loadDigestPreset('weekly_digest')" class="ts-btn ts-btn-primary ts-btn-sm" style="width: 100%;">Use Template in Studio →</button>
      </div>

      <div class="admin-table-wrap" style="padding: 1.5rem; display: flex; flex-direction: column;">
        <span class="ts-badge ts-badge-pink" style="align-self: flex-start; margin-bottom: 0.75rem;">BREAKING NEWS</span>
        <h4 style="color: #f8fafc; font-size: 1.1rem; margin: 0 0 0.4rem 0;">Frontier AI &amp; Model Release Alert</h4>
        <p style="color: #94a3b8; font-size: 0.85rem; flex: 1; margin-bottom: 1rem;">High-impact single story alert format designed for breaking releases and zero-day vulnerabilities.</p>
        <button type="button" onclick="loadDigestPreset('breaking_alert')" class="ts-btn ts-btn-primary ts-btn-sm" style="width: 100%;">Use Template in Studio →</button>
      </div>

      <div class="admin-table-wrap" style="padding: 1.5rem; display: flex; flex-direction: column;">
        <span class="ts-badge ts-badge-green" style="align-self: flex-start; margin-bottom: 0.75rem;">PRODUCT SHOWCASE</span>
        <h4 style="color: #f8fafc; font-size: 1.1rem; margin: 0 0 0.4rem 0;">Verified Tool Spotlight &amp; Teardown</h4>
        <p style="color: #94a3b8; font-size: 0.85rem; flex: 1; margin-bottom: 1rem;">Feature comparison matrix, pricing breakdown, and verified builder teardown verdict.</p>
        <button type="button" onclick="loadDigestPreset('tool_spotlight')" class="ts-btn ts-btn-primary ts-btn-sm" style="width: 100%;">Use Template in Studio →</button>
      </div>

      <div class="admin-table-wrap" style="padding: 1.5rem; display: flex; flex-direction: column;">
        <span class="ts-badge ts-badge-indigo" style="align-self: flex-start; margin-bottom: 0.75rem;">COMMUNITY BROADCAST</span>
        <h4 style="color: #f8fafc; font-size: 1.1rem; margin: 0 0 0.4rem 0;">Platform Intelligence Announcement</h4>
        <p style="color: #94a3b8; font-size: 0.85rem; flex: 1; margin-bottom: 1rem;">Clean branded layout for platform updates, new features, and creator reward milestones.</p>
        <button type="button" onclick="loadDigestPreset('community_broadcast')" class="ts-btn ts-btn-primary ts-btn-sm" style="width: 100%;">Use Template in Studio →</button>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 5. SCHEDULING TAB -->
  <!-- ========================================================================= -->
  <div class="news-pane" id="npane_scheduling" style="<?= $activeTab === 'scheduling' ? 'display:block;' : 'display:none;' ?>">
    <div class="admin-table-wrap" style="padding: 1.75rem;">
      <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 1rem 0;">⏰ Scheduled Broadcast Queue</h3>
      <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 1.5rem;">Issues queued for automatic delivery based on scheduled timestamps.</p>

      <table class="admin-table">
        <thead>
          <tr>
            <th>Issue Title</th>
            <th>Target Audience</th>
            <th>Scheduled Time (UTC)</th>
            <th>Queue State</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $schedIssues = array_filter($issues, fn($i) => $i['status'] === 'scheduled');
          if (!empty($schedIssues)):
            foreach ($schedIssues as $si):
          ?>
            <tr>
              <td style="font-weight: 700; color: #f8fafc;"><?= htmlspecialchars($si['title']) ?></td>
              <td><span class="ts-badge ts-badge-cyan">All Active Subscribers (<?= number_format($stats['active']) ?>)</span></td>
              <td style="font-family: var(--admin-font-mono); color: #38bdf8;"><?= \App\Core\DateHelper::render($si['scheduled_at'], 'M j, Y H:i:s') ?></td>
              <td><span class="ts-badge ts-badge-green">QUEUED</span></td>
              <td>
                <button type="button" onclick='loadIssueIntoComposer(<?= json_encode($si) ?>)' class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem;">Edit Schedule</button>
              </td>
            </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="5" style="text-align: center; padding: 2rem; color: #64748b;">No broadcasts currently scheduled in queue.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- 6. ANALYTICS TAB -->
  <!-- ========================================================================= -->
  <div class="news-pane" id="npane_analytics" style="<?= $activeTab === 'analytics' ? 'display:block;' : 'display:none;' ?>">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
      <div class="admin-table-wrap" style="padding: 1.5rem;">
        <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Average Delivery Rate</div>
        <div style="font-size: 1.8rem; font-weight: 800; color: #10b981; margin-top: 0.25rem; font-family: var(--admin-font-mono);">99.4%</div>
      </div>
      <div class="admin-table-wrap" style="padding: 1.5rem;">
        <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Average Unique Opens</div>
        <div style="font-size: 1.8rem; font-weight: 800; color: #38bdf8; margin-top: 0.25rem; font-family: var(--admin-font-mono);">48.9%</div>
      </div>
      <div class="admin-table-wrap" style="padding: 1.5rem;">
        <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Click-to-Open (CTOR)</div>
        <div style="font-size: 1.8rem; font-weight: 800; color: #ec4899; margin-top: 0.25rem; font-family: var(--admin-font-mono);">34.2%</div>
      </div>
      <div class="admin-table-wrap" style="padding: 1.5rem;">
        <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Unsubscribe Rate</div>
        <div style="font-size: 1.8rem; font-weight: 800; color: #f59e0b; margin-top: 0.25rem; font-family: var(--admin-font-mono);">0.12%</div>
      </div>
    </div>
  </div>

</div>

<!-- Modal: Add Subscriber Manual -->
<div id="addSubscriberModal" class="admin-modal-overlay" style="display: none;">
  <div class="admin-modal-box modal-cyan" style="max-width: 480px;">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #38bdf8;"><i data-lucide="user-plus"></i> Add Newsletter Subscriber</h3>
      <button type="button" onclick="closeAddSubscriberModal()" class="admin-modal-close">✕</button>
    </div>
    <form action="<?= url('/admin/subscribers/save') ?>" method="POST">
      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Subscriber Email Address</label>
        <input type="email" name="email" placeholder="subscriber@example.com" class="admin-modal-input" required>
      </div>

      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Subscription Segment</label>
        <select name="subscriber_type" class="admin-modal-select">
          <option value="all">All Broadcasts &amp; Digests</option>
          <option value="weekly_digest">Weekly Digest Only</option>
          <option value="ai_tools_only">AI Tools Radar Only</option>
        </select>
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="closeAddSubscriberModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #38bdf8; border-color: #38bdf8; color: #000; font-weight: 700;">Add Subscriber ✓</button>
      </div>
    </form>
  </div>
</div>

<script>
function switchNewsletterTab(tabKey) {
  document.querySelectorAll('[id^="ntab_"]').forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });
  const activeBtn = document.getElementById('ntab_' + tabKey);
  if (activeBtn) {
    activeBtn.classList.remove('ts-btn-outline');
    activeBtn.classList.add('ts-btn-primary');
  }

  document.querySelectorAll('.news-pane').forEach(p => p.style.display = 'none');
  const targetPane = document.getElementById('npane_' + tabKey);
  if (targetPane) targetPane.style.display = 'block';

  const url = new URL(window.location);
  url.searchParams.set('tab', tabKey);
  window.history.replaceState({}, '', url);

  if (tabKey === 'composer') {
    updateLivePreview();
  }

  if (window.lucide) lucide.createIcons();
}

function updateRecipientTargetUI() {
  const target = document.getElementById('recipient_target').value;
  document.getElementById('wrapper_segment').style.display = (target === 'segment') ? 'block' : 'none';
  document.getElementById('wrapper_subscriber_picker').style.display = (target === 'single_subscriber') ? 'block' : 'none';
  document.getElementById('wrapper_custom_email').style.display = (target === 'custom_email') ? 'block' : 'none';
}

function onSubscriberDropdownChange() {
  const sel = document.getElementById('subscriber_dropdown');
  const opt = sel.options[sel.selectedIndex];
  if (opt && opt.value) {
    document.getElementById('single_subscriber_id').value = opt.value;
  }
}

function sendDirectToSubscriber(subId, subEmail, subName) {
  switchNewsletterTab('composer');
  document.getElementById('recipient_target').value = 'single_subscriber';
  updateRecipientTargetUI();

  document.getElementById('single_subscriber_id').value = subId;
  const sel = document.getElementById('subscriber_dropdown');
  sel.value = subId;

  document.getElementById('composerFormTitle').innerText = '⚡ Compose Direct Message to ' + subName + ' (' + subEmail + ')';
  updateLivePreview();
}

function prepareBroadcastToAll() {
  switchNewsletterTab('composer');
  document.getElementById('recipient_target').value = 'all_active';
  updateRecipientTargetUI();
  document.getElementById('composerFormTitle').innerText = '⚡ Compose & Dispatch Broadcast to All Active Subscribers';
  updateLivePreview();
}

function toggleVariablesDrawer() {
  const drawer = document.getElementById('variables_inputs_drawer');
  const btn = document.getElementById('btn_toggle_vars');
  if (drawer.style.display === 'none') {
    drawer.style.display = 'grid';
    btn.innerText = 'Collapse Inputs ▴';
  } else {
    drawer.style.display = 'none';
    btn.innerText = 'Expand Inputs ▾';
  }
}

function insertVariableTag(tag) {
  const textarea = document.getElementById('composer_body_html');
  const start = textarea.selectionStart || 0;
  const end = textarea.selectionEnd || 0;
  const text = textarea.value;
  textarea.value = text.substring(0, start) + tag + text.substring(end);
  textarea.focus();
  textarea.selectionStart = textarea.selectionEnd = start + tag.length;
  updateLivePreview();
}

function setComposerMode(mode) {
  const paneEditor = document.getElementById('pane_editor_view');
  const panePreview = document.getElementById('pane_preview_view');
  const btnEditor = document.getElementById('mode_btn_editor');
  const btnPreview = document.getElementById('mode_btn_preview');

  if (mode === 'preview') {
    paneEditor.style.display = 'none';
    panePreview.style.display = 'block';
    btnPreview.classList.add('ts-btn-primary');
    btnPreview.classList.remove('ts-btn-outline');
    btnEditor.classList.add('ts-btn-outline');
    btnEditor.classList.remove('ts-btn-primary');
    updateLivePreview();
  } else {
    paneEditor.style.display = 'block';
    panePreview.style.display = 'none';
    btnEditor.classList.add('ts-btn-primary');
    btnEditor.classList.remove('ts-btn-outline');
    btnPreview.classList.add('ts-btn-outline');
    btnPreview.classList.remove('ts-btn-primary');
  }
  if (window.lucide) lucide.createIcons();
}

function setPreviewDevice(device) {
  const container = document.getElementById('previewContainer');
  const btnD = document.getElementById('dev_btn_desktop');
  const btnM = document.getElementById('dev_btn_mobile');

  if (device === 'mobile') {
    container.style.maxWidth = '375px';
    btnM.classList.add('ts-btn-primary');
    btnM.classList.remove('ts-btn-outline');
    btnD.classList.add('ts-btn-outline');
    btnD.classList.remove('ts-btn-primary');
  } else {
    container.style.maxWidth = '680px';
    btnD.classList.add('ts-btn-primary');
    btnD.classList.remove('ts-btn-outline');
    btnM.classList.add('ts-btn-outline');
    btnM.classList.remove('ts-btn-primary');
  }
}

function updateLivePreview() {
  let body = document.getElementById('composer_body_html').value || '';
  const headline = document.getElementById('var_headline').value || 'TechScout Architecture Digest #48';
  const message = (document.getElementById('var_message').value || '').replace(/\n/g, '<br>');
  const articleTitle = document.getElementById('var_article_title').value || 'Zero-Downtime PostgreSQL Index Tuning';
  const actionUrl = document.getElementById('var_action_url').value || '<?= url('/topics') ?>';
  const actionText = document.getElementById('var_action_text').value || 'Read Full Blueprint →';
  const digestSummary = (document.getElementById('var_digest_summary').value || '').replace(/\n/g, '<br>');

  const map = {
    '{{name}}': 'Alex Rivera (Sample)',
    '{{username}}': 'alex_dev',
    '{{email}}': 'alex@example.com',
    '{{headline}}': headline,
    '{{message}}': message,
    '{{article_title}}': articleTitle,
    '{{action_url}}': actionUrl,
    '{{action_text}}': actionText,
    '{{digest_summary}}': digestSummary,
    '{{site_name}}': 'TechScout',
    '{{app_url}}': '<?= url() ?>',
    '{{unsubscribe_url}}': '#unsubscribe-sample',
    '{{year}}': '<?= date('Y') ?>',
    '{{date}}': '<?= date('F j, Y') ?>'
  };

  for (const [k, v] of Object.entries(map)) {
    body = body.split(k).join(v);
  }

  let renderedSubject = document.getElementById('composer_subject').value || 'TechScout Dispatch: {{headline}}';
  for (const [k, v] of Object.entries(map)) {
    renderedSubject = renderedSubject.split(k).join(v);
  }
  const subEl = document.getElementById('livePreviewSubject');
  if (subEl) subEl.innerText = renderedSubject;

  const renderedDiv = document.getElementById('livePreviewRendered');
  if (renderedDiv) {
    renderedDiv.innerHTML = body;
  }
}

function loadDigestPreset(preset) {
  switchNewsletterTab('composer');

  if (preset === 'weekly_digest') {
    document.getElementById('var_headline').value = 'TechScout Engineering Radar & Architecture Digest';
    document.getElementById('var_message').value = 'Here are the highest-signal technical publications, architecture teardowns, and verified AI developer stacks from this week.';
    document.getElementById('composer_subject').value = '⚡ Weekly Radar: {{headline}}';
  } else if (preset === 'breaking_alert') {
    document.getElementById('var_headline').value = 'Breaking AI Radar: Frontier Model Release & Architecture Specs';
    document.getElementById('var_message').value = 'A major frontier AI model release just occurred. Here are the immediate architectural benchmarks, latency tradeoffs, and deployment blueprints.';
    document.getElementById('composer_subject').value = '🚨 Breaking AI Alert: {{headline}}';
  } else if (preset === 'tool_spotlight') {
    document.getElementById('var_headline').value = 'Verified Developer Tool Spotlight: Architecture & Pricing Teardown';
    document.getElementById('var_message').value = 'We put this week\'s trending developer tool through production load testing. Here is the unvarnished engineering verdict.';
    document.getElementById('composer_subject').value = '🛠️ Tool Spotlight: {{headline}}';
  } else if (preset === 'community_broadcast') {
    document.getElementById('var_headline').value = 'TechScout Platform Intelligence & Milestone Update';
    document.getElementById('var_message').value = 'Important architectural updates, new creator reward pools, and community enhancements on the TechScout platform.';
    document.getElementById('composer_subject').value = '📢 Platform Announcement: {{headline}}';
  }

  updateLivePreview();
}

function loadIssueIntoComposer(iss) {
  switchNewsletterTab('composer');
  document.getElementById('composer_subject').value = iss.subject_line || iss.title;
  document.getElementById('composer_body_html').value = iss.body_html || '';
  document.getElementById('var_headline').value = iss.title || '';
  document.getElementById('composerFormTitle').innerText = '✏️ Edit & Re-dispatch: ' + iss.title;
  updateLivePreview();
}

function insertArticleIntoNewsletter() {
  const picker = document.getElementById('articleQuickPicker');
  const opt = picker.options[picker.selectedIndex];
  if (!opt) return;

  const title = opt.value;
  const slug = opt.getAttribute('data-slug');
  const summary = opt.getAttribute('data-summary');

  const cardHtml = `\n<div style="border: 1px solid #334155; padding: 18px; border-radius: 8px; margin: 18px 0; background: #0f172a;">
  <span style="color: #38bdf8; font-size: 11px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Curated Technical Breakdown</span>
  <h3 style="margin: 6px 0 8px 0; color: #f8fafc; font-size: 17px;">${title}</h3>
  <p style="color: #cbd5e1; font-size: 14px; line-height: 1.5; margin: 0 0 12px 0;">${summary}</p>
  <a href="<?= url('/article/') ?>${slug}" style="color: #38bdf8; font-weight: 700; font-size: 13px; text-decoration: none;">Read Publication &rarr;</a>
</div>\n`;

  const textarea = document.getElementById('composer_body_html');
  textarea.value += cardHtml;
  updateLivePreview();
  alert('Article card embedded into newsletter body!');
}

function sendTestNewsletter() {
  const email = document.getElementById('testRecipientEmail').value;
  const subject = document.getElementById('composer_subject').value || 'TechScout Test Broadcast';
  const body = document.getElementById('composer_body_html').value;
  const credId = document.getElementById('credential_id').value;

  const headline = document.getElementById('var_headline').value || 'TechScout Architecture Radar';
  const message = document.getElementById('var_message').value || '';
  const articleTitle = document.getElementById('var_article_title').value || '';
  const actionUrl = document.getElementById('var_action_url').value || '';
  const actionText = document.getElementById('var_action_text').value || '';
  const digestSummary = document.getElementById('var_digest_summary').value || '';

  if (!email) {
    alert('Please enter a test recipient email address.');
    return;
  }

  fetch('<?= url('/admin/newsletters/dispatch-test') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({
      test_email: email,
      subject: subject,
      body_html: body,
      credential_id: credId,
      var_headline: headline,
      var_message: message,
      var_article_title: articleTitle,
      var_action_url: actionUrl,
      var_action_text: actionText,
      var_digest_summary: digestSummary
    })
  })
  .then(r => r.json())
  .then(d => {
    alert(d.message);
  })
  .catch(err => {
    alert('Test dispatch network error: ' + err.message);
  });
}

function openAddSubscriberModal() {
  document.getElementById('addSubscriberModal').style.display = 'flex';
}
function closeAddSubscriberModal() {
  document.getElementById('addSubscriberModal').style.display = 'none';
}

// ============================================================================
// LIVE BATCH DISPATCH ENGINE
// ============================================================================
let isDispatchPaused = false;
let currentDispatchId = null;

function startLiveDispatch(issueId, issueTitle) {
  currentDispatchId = issueId;
  isDispatchPaused = false;

  document.getElementById('dispatchModalTitle').innerText = '🚀 Live Dispatch: ' + issueTitle;
  document.getElementById('dispatchModal').style.display = 'flex';
  document.getElementById('dispatchStreamLog').innerHTML = '<div style="color:#64748b;">Initializing live batch queue...</div>';
  document.getElementById('dispatchProgressFill').style.width = '0%';
  document.getElementById('dispatchProgressText').innerText = '0%';
  document.getElementById('dispatchBtnPause').innerText = '⏸ Pause Queue';
  document.getElementById('dispatchBtnPause').style.display = 'inline-block';

  // Query status first
  fetch('<?= url('/admin/newsletters/dispatch-status/') ?>' + issueId)
    .then(r => r.json())
    .then(d => {
      if (d.success) {
        document.getElementById('dsp_total').innerText = d.total;
        document.getElementById('dsp_sent').innerText = d.sent;
        document.getElementById('dsp_rem').innerText = d.remaining;
        document.getElementById('dsp_fail').innerText = d.failed;

        if (d.total === 0) {
          logDispatch('⚠️ No active subscribers found in mailing list.');
          return;
        }

        logDispatch(`Found ${d.total} active subscriber recipients. Starting batch transmission (20/batch)...`);
        processNextDispatchBatch(issueId, 0, 20);
      }
    })
    .catch(err => {
      logDispatch('❌ Failed to fetch queue status: ' + err.message);
    });
}

function processNextDispatchBatch(issueId, offset, batchSize) {
  if (isDispatchPaused) {
    logDispatch('⏸ Queue transmission paused by operator.');
    return;
  }

  fetch('<?= url('/admin/newsletters/dispatch-batch/') ?>' + issueId, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ offset: offset, batch_size: batchSize })
  })
  .then(r => r.json())
  .then(data => {
    if (!data.success) {
      logDispatch('❌ Batch error: ' + (data.error || 'Unknown dispatch failure'));
      return;
    }

    // Update HUD counters
    document.getElementById('dsp_total').innerText = data.total;
    document.getElementById('dsp_sent').innerText = data.sent_total || data.sent;
    document.getElementById('dsp_rem').innerText = data.remaining;
    document.getElementById('dsp_fail').innerText = data.failed || (parseInt(document.getElementById('dsp_fail').innerText) || 0) + (data.batch_failed || 0);

    document.getElementById('dispatchProgressFill').style.width = data.percent + '%';
    document.getElementById('dispatchProgressText').innerText = data.percent + '%';

    // Stream recipient logs
    if (data.dispatched_in_batch && data.dispatched_in_batch.length > 0) {
      data.dispatched_in_batch.forEach(item => {
        const icon = item.status === 'sent' ? '✓' : '✗';
        const color = item.status === 'sent' ? '#34d399' : '#ef4444';
        logDispatch(`<span style="color:${color};">[${new Date().toLocaleTimeString()}] ${icon} Delivered to ${item.email}</span>`);
      });
    }

    if (data.is_complete) {
      logDispatch(`<strong style="color: #10b981;">🎉 BROADCAST DISPATCH COMPLETED! ${data.sent_total || data.sent} delivered, ${data.batch_failed || data.failed || 0} failed.</strong>`);
      document.getElementById('dispatchBtnPause').style.display = 'none';
      if (typeof showToast === 'function') {
        showToast('🎉 Newsletter broadcast successfully dispatched!', 'success');
      }
    } else {
      setTimeout(() => {
        processNextDispatchBatch(issueId, data.next_offset, batchSize);
      }, 400);
    }
  })
  .catch(err => {
    logDispatch('❌ Network exception: ' + err.message);
  });
}

function togglePauseDispatch() {
  isDispatchPaused = !isDispatchPaused;
  const btn = document.getElementById('dispatchBtnPause');
  if (isDispatchPaused) {
    btn.innerText = '▶ Resume Queue';
    btn.classList.remove('ts-btn-outline');
    btn.classList.add('ts-btn-primary');
    logDispatch('⏸ Dispatch paused.');
  } else {
    btn.innerText = '⏸ Pause Queue';
    btn.classList.remove('ts-btn-primary');
    btn.classList.add('ts-btn-outline');
    logDispatch('▶ Resuming dispatch queue...');
    const curSent = parseInt(document.getElementById('dsp_sent').innerText) || 0;
    processNextDispatchBatch(currentDispatchId, curSent, 20);
  }
}

function logDispatch(msg) {
  const stream = document.getElementById('dispatchStreamLog');
  const row = document.createElement('div');
  row.style.cssText = 'padding: 2px 0; border-bottom: 1px dashed rgba(148,163,184,0.06);';
  row.innerHTML = msg;
  stream.appendChild(row);
  stream.scrollTop = stream.scrollHeight;
}

function closeDispatchModal() {
  isDispatchPaused = true;
  document.getElementById('dispatchModal').style.display = 'none';
  location.reload();
}

// Initialize live preview on load
document.addEventListener('DOMContentLoaded', () => {
  updateLivePreview();
  updateRecipientTargetUI();
});
</script>

<!-- ========================================================================= -->
<!-- MODAL: LIVE BATCH DISPATCH ENGINE -->
<!-- ========================================================================= -->
<div id="dispatchModal" class="admin-modal-overlay" style="display: none;">
  <div class="admin-modal-box modal-emerald" style="max-width: 680px; max-height: 90vh; display: flex; flex-direction: column;">
    <div class="admin-modal-header" style="flex: 0 0 auto;">
      <div>
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.2rem;">
          <span class="live-pulse"></span>
          <span class="ts-badge ts-badge-green" style="font-size: 0.68rem;">LIVE TRANSMISSION ENGINE</span>
        </div>
        <h3 class="admin-modal-title" id="dispatchModalTitle" style="color: #34d399; font-size: 1.15rem;">🚀 Live Broadcast Dispatch</h3>
      </div>
      <button type="button" onclick="closeDispatchModal()" class="admin-modal-close">✕</button>
    </div>

    <div style="padding: 1.5rem; overflow-y: auto; flex: 1 1 auto;">
      <!-- Stats 4-Grid -->
      <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem; margin-bottom: 1.25rem;">
        <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(148,163,184,0.12); border-radius: 8px; padding: 0.75rem; text-align: center;">
          <div style="font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; font-weight: 700;">Total Target</div>
          <div id="dsp_total" style="font-size: 1.35rem; font-weight: 800; color: #f8fafc; font-family: var(--admin-font-mono);">0</div>
        </div>
        <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(16,185,129,0.3); border-radius: 8px; padding: 0.75rem; text-align: center;">
          <div style="font-size: 0.7rem; color: #34d399; text-transform: uppercase; font-weight: 700;">Sent</div>
          <div id="dsp_sent" style="font-size: 1.35rem; font-weight: 800; color: #10b981; font-family: var(--admin-font-mono);">0</div>
        </div>
        <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(56,189,248,0.3); border-radius: 8px; padding: 0.75rem; text-align: center;">
          <div style="font-size: 0.7rem; color: #38bdf8; text-transform: uppercase; font-weight: 700;">Remaining</div>
          <div id="dsp_rem" style="font-size: 1.35rem; font-weight: 800; color: #38bdf8; font-family: var(--admin-font-mono);">0</div>
        </div>
        <div style="background: rgba(15,23,42,0.8); border: 1px solid rgba(239,68,68,0.25); border-radius: 8px; padding: 0.75rem; text-align: center;">
          <div style="font-size: 0.7rem; color: #f87171; text-transform: uppercase; font-weight: 700;">Failed</div>
          <div id="dsp_fail" style="font-size: 1.35rem; font-weight: 800; color: #ef4444; font-family: var(--admin-font-mono);">0</div>
        </div>
      </div>

      <!-- Animated Progress Bar -->
      <div style="margin-bottom: 1.25rem;">
        <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 0.35rem; color: #cbd5e1;">
          <span>Transmission Progress</span>
          <strong id="dispatchProgressText" style="color: #34d399; font-family: var(--admin-font-mono);">0%</strong>
        </div>
        <div style="width: 100%; height: 10px; background: rgba(148,163,184,0.15); border-radius: 9999px; overflow: hidden;">
          <div id="dispatchProgressFill" style="width: 0%; height: 100%; background: linear-gradient(90deg, #10b981, #38bdf8); border-radius: 9999px; transition: width 0.3s ease;"></div>
        </div>
      </div>

      <!-- Live Stream Output Terminal -->
      <div>
        <label class="admin-modal-label" style="display: flex; justify-content: space-between;">
          <span>Live Dispatch Stream Log</span>
          <span style="font-size: 0.7rem; color: #64748b;">Real-time SMTP / API Relay</span>
        </label>
        <div id="dispatchStreamLog" style="background: #020617; border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 0.85rem; height: 180px; overflow-y: auto; font-family: var(--admin-font-mono); font-size: 0.75rem; color: #94a3b8; line-height: 1.45;">
          <div style="color: #64748b;">Awaiting dispatch ignition...</div>
        </div>
      </div>
    </div>

    <div class="admin-modal-footer" style="flex: 0 0 auto; display: flex; justify-content: space-between;">
      <button type="button" onclick="togglePauseDispatch()" id="dispatchBtnPause" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #f59e0b; border-color: rgba(245,158,11,0.3);">
        ⏸ Pause Queue
      </button>
      <button type="button" onclick="closeDispatchModal()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #334155; border-color: #334155; font-weight: 700;">
        Close &amp; Finish
      </button>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
