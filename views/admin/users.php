<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

  <!-- Topbar Header -->
  <div class="admin-topbar" style="margin-bottom: 2rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-cyan">IDENTITY &amp; ACCESS GOVERNANCE</span>
        <span style="font-size: 0.8rem; color: #64748b;">Member Profiles, Publishing Scopes, Multi-Tab User Audits &amp; Role Management</span>
      </div>
      <h1 style="font-size: 1.75rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        CMS User &amp; Creator Management
      </h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <button type="button" onclick="openCreateUserModal()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #10b981, #38bdf8); font-weight: 700;">
        <i data-lucide="user-plus"></i> Add User +
      </button>
      <button type="button" onclick="openMergeModal()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3);">
        <i data-lucide="git-merge"></i> Merge Accounts 🔀
      </button>
      <button type="button" onclick="openBulkEmailModal()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #f59e0b; border-color: rgba(245,158,11,0.3);">
        <i data-lucide="mail"></i> Bulk Email Dispatcher ✉️
      </button>
      <button type="button" onclick="openBroadcastModal()" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #a855f7; border-color: rgba(168,85,247,0.3);">
        <i data-lucide="radio"></i> Broadcast Notification 📢
      </button>
      <a href="<?= url('/admin/mail') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #94a3b8; border-color: rgba(148,163,184,0.3);">
        <i data-lucide="settings"></i> Mail Center
      </a>
    </div>
  </div>

  <!-- Segment Filter Tabs Strip (9 Segments) -->
  <div class="admin-table-wrap" style="padding: 0.5rem; margin-bottom: 1.5rem; overflow-x: auto;">
    <div style="display: flex; gap: 0.35rem; min-width: max-content; align-items: center;">
      <?php
      $segments = [
        'all' => ['label' => 'All Users', 'icon' => 'users', 'count' => $counts['all']],
        'members' => ['label' => 'Members', 'icon' => 'user', 'count' => $counts['members']],
        'authors' => ['label' => 'Authors', 'icon' => 'edit-3', 'count' => $counts['authors']],
        'editors' => ['label' => 'Editors', 'icon' => 'check-circle', 'count' => $counts['editors']],
        'admins' => ['label' => 'Administrators', 'icon' => 'shield-alert', 'count' => $counts['admins']],
        'verified' => ['label' => 'Verified Users', 'icon' => 'award', 'count' => $counts['verified']],
        'suspicious' => ['label' => 'Suspicious / Flagged', 'icon' => 'alert-octagon', 'count' => $counts['suspicious']],
        'suspended' => ['label' => 'Suspended', 'icon' => 'alert-triangle', 'count' => $counts['suspended']],
        'banned' => ['label' => 'Banned', 'icon' => 'slash', 'count' => $counts['banned']],
      ];
      ?>

      <?php foreach ($segments as $k => $s): ?>
        <a href="<?= url('/admin/users?segment=' . $k . (!empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '')) ?>" class="ts-btn ts-btn-sm <?= $activeSegment === $k ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.8rem; padding: 0.4rem 0.85rem; display: flex; align-items: center; gap: 0.4rem; text-decoration: none; <?= $k === 'suspicious' && $s['count'] > 0 ? 'border-color: #ef4444; color: #f87171;' : '' ?>">
          <i data-lucide="<?= $s['icon'] ?>" style="width: 14px; height: 14px;"></i>
          <span><?= $s['label'] ?></span>
          <span class="ts-badge" style="font-size: 0.68rem; padding: 0.1rem 0.4rem; background: rgba(255,255,255,0.12);"><?= $s['count'] ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Search & Action Ribbon -->
  <div class="admin-table-wrap" style="padding: 1rem 1.5rem; margin-bottom: 1.5rem;">
    <form method="GET" action="<?= url('/admin/users') ?>" style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
      <input type="hidden" name="segment" value="<?= htmlspecialchars($activeSegment) ?>">
      <div style="position: relative; flex: 1; min-width: 280px;">
        <i data-lucide="search" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: #64748b;"></i>
        <input type="text" name="q" value="<?= htmlspecialchars($searchQuery) ?>" placeholder="Search by username, email, or display name..." class="admin-modal-input" style="padding-left: 2.4rem;">
      </div>
      <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm">Search Users</button>
      <?php if (!empty($searchQuery)): ?>
        <a href="<?= url('/admin/users?segment=' . urlencode($activeSegment)) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #94a3b8;">Clear ✕</a>
      <?php endif; ?>
    </form>
  </div>

  <!-- Users Table -->
  <div class="admin-table-wrap">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--admin-border-subtle);">
      <div>
        <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0;">
          <?= htmlspecialchars($segments[$activeSegment]['label'] ?? 'All Users') ?>
          <span style="font-size: 0.85rem; color: #64748b; font-weight: normal;">(<?= count($users) ?> loaded)</span>
        </h3>
      </div>
    </div>

    <table class="admin-table">
      <thead>
        <tr>
          <th>User Identity</th>
          <th>Role, Tier &amp; Badges</th>
          <th>Publications &amp; Comments</th>
          <th>Reputation / Credits</th>
          <th>Status &amp; Strikes</th>
          <th>Joined</th>
          <th>Governance Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($users)): ?>
          <?php foreach ($users as $u): ?>
            <tr style="<?= !empty($u['is_flagged_suspicious']) ? 'background: rgba(239,68,68,0.06);' : '' ?>">
              <td>
                <div style="display: flex; align-items: center; gap: 0.65rem;">
                  <div style="width: 38px; height: 38px; border-radius: 50%; background: #1e293b; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #38bdf8; font-size: 0.9rem; border: 1px solid rgba(56,189,248,0.25); flex-shrink: 0;">
                    <?= strtoupper(substr($u['display_name'] ?? $u['username'], 0, 1)) ?>
                  </div>
                  <div>
                    <div style="font-weight: 700; color: #f8fafc; font-size: 0.92rem; display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap;">
                      <a href="javascript:void(0)" onclick="openUserDetailModal(<?= $u['id'] ?>)" style="color: #f8fafc; text-decoration: none;">
                        <?= htmlspecialchars($u['display_name'] ?? $u['username']) ?>
                      </a>
                      <?php if (!empty($u['verification_badge']) && $u['verification_badge'] !== 'none'): ?>
                        <span class="ts-badge ts-badge-cyan" style="font-size: 0.62rem; padding: 0.08rem 0.35rem;">
                          <?= htmlspecialchars($u['verification_badge']) ?>
                        </span>
                      <?php endif; ?>
                      <?php if (!empty($u['custom_badge_label'])): ?>
                        <span class="ts-badge" style="font-size: 0.62rem; padding: 0.08rem 0.35rem; background: rgba(56,189,248,0.15); color: <?= htmlspecialchars($u['custom_badge_color'] ?: '#38bdf8') ?>; border: 1px solid <?= htmlspecialchars($u['custom_badge_color'] ?: '#38bdf8') ?>;">
                          ✓ <?= htmlspecialchars($u['custom_badge_label']) ?>
                        </span>
                      <?php endif; ?>
                    </div>
                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.15rem; font-family: var(--admin-font-mono);">
                      @<?= htmlspecialchars($u['username']) ?> &bull; <?= htmlspecialchars($u['email']) ?>
                    </div>

                    <?php if (!empty($u['is_flagged_suspicious'])): ?>
                      <div style="margin-top: 0.35rem; display: flex; align-items: center; gap: 0.4rem;">
                        <span class="ts-badge ts-badge-pink" style="font-size: 0.62rem; padding: 0.1rem 0.4rem; display: inline-flex; align-items: center; gap: 0.25rem;" title="<?= htmlspecialchars($u['suspicious_reason'] ?? 'High frequency activities detected') ?>">
                          ⚠️ FLAGGED SUSPICIOUS
                        </span>
                        <form action="<?= url('/admin/users/clear-suspicious/' . $u['id']) ?>" method="POST" style="display: inline;">
                          <button type="submit" class="ts-btn ts-btn-xs ts-btn-outline" style="font-size: 0.62rem; padding: 0.05rem 0.35rem; color: #10b981; border-color: rgba(16,185,129,0.3);" title="Clear Suspicious Flag">
                            Pardon ✓
                          </button>
                        </form>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              </td>

              <td>
                <div style="display: flex; flex-direction: column; gap: 0.35rem;">
                  <form action="<?= url('/admin/users/action/' . $u['id']) ?>" method="POST" style="display: inline-block;">
                    <input type="hidden" name="action" value="update_role">
                    <select name="role" onchange="this.form.submit()" class="admin-modal-select" style="font-size: 0.76rem; padding: 0.2rem 0.4rem; min-width: 95px;">
                      <option value="member" <?= $u['role'] === 'member' ? 'selected' : '' ?>>Member</option>
                      <option value="author" <?= $u['role'] === 'author' ? 'selected' : '' ?>>Author</option>
                      <option value="editor" <?= $u['role'] === 'editor' ? 'selected' : '' ?>>Editor</option>
                      <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                      <option value="super_admin" <?= $u['role'] === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                    </select>
                  </form>

                  <?php if (!empty($u['tier_title']) || !empty($u['tier_override'])): ?>
                    <div style="font-size: 0.72rem; color: #f59e0b; font-weight: 700;">
                      🎖️ <?= htmlspecialchars($u['tier_title'] ?: $u['tier_override']) ?>
                    </div>
                  <?php endif; ?>

                  <button type="button" onclick='openTierBadgeModal(<?= json_encode($u) ?>)' class="ts-btn ts-btn-outline ts-btn-xs" style="font-size: 0.65rem; padding: 0.1rem 0.35rem; color: #38bdf8; border-color: rgba(56,189,248,0.25); text-align: left; width: fit-content;">
                    ⚙️ Tier / Badge
                  </button>
                </div>
              </td>

              <td style="font-size: 0.82rem; color: #cbd5e1;">
                <div>📄 <strong><?= (int)($u['content_count'] ?? 0) ?></strong> publications</div>
                <div style="color: #64748b; font-size: 0.75rem; margin-top: 0.15rem;">💬 <?= (int)($u['comment_count'] ?? 0) ?> comments</div>
              </td>

              <td>
                <div style="font-size: 0.85rem; font-weight: 700; color: #10b981; font-family: var(--admin-font-mono);">
                  $<?= number_format((float)($u['promo_credits'] ?? 0), 2) ?>
                </div>
                <div style="font-size: 0.75rem; color: #f59e0b; font-family: var(--admin-font-mono); margin-top: 0.15rem;">
                  ⭐ <?= number_format((int)($u['reputation_score'] ?? 0)) ?> rep
                </div>
              </td>

              <td>
                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                  <form action="<?= url('/admin/users/action/' . $u['id']) ?>" method="POST" style="display: inline-block;">
                    <input type="hidden" name="action" value="toggle_ban">
                    <button type="submit" class="ts-badge <?= $u['status'] === 'active' ? 'ts-badge-green' : ($u['status'] === 'suspended' ? 'ts-badge-amber' : 'ts-badge-pink') ?>" style="cursor: pointer; border: none;" title="Click to toggle status">
                      <?= strtoupper(htmlspecialchars($u['status'])) ?>
                    </button>
                  </form>

                  <!-- Structured Strike Counter Badge -->
                  <?php $stkCount = (int)($u['active_strikes'] ?? $u['strike_count'] ?? 0); ?>
                  <div style="display: flex; align-items: center; gap: 0.25rem;">
                    <span class="ts-badge <?= $stkCount === 0 ? 'ts-badge-green' : ($stkCount === 1 ? 'ts-badge-amber' : 'ts-badge-pink') ?>" style="font-size: 0.65rem; padding: 0.08rem 0.35rem;" title="<?= $stkCount ?> active strike(s) on file">
                      <?= $stkCount ?>/3 Strikes
                    </span>
                  </div>
                </div>
              </td>

              <td style="font-size: 0.78rem; color: #94a3b8;">
                <?= \App\Core\DateHelper::render($u['created_at'], 'M j, Y', 'date') ?>
              </td>

              <td>
                <div style="display: flex; gap: 0.3rem; flex-wrap: wrap;">
                  <!-- View Detail Hub Modal Trigger -->
                  <button type="button" onclick="openUserDetailModal(<?= $u['id'] ?>)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.45rem; color: #38bdf8; border-color: rgba(56,189,248,0.35);" title="View Comprehensive User Detail Hub">
                    👁️ View
                  </button>

                  <!-- Change Password Modal Trigger -->
                  <button type="button" onclick="openChangePasswordModal(<?= $u['id'] ?>, '<?= htmlspecialchars($u['username']) ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.45rem; color: #f59e0b; border-color: rgba(245,158,11,0.35);" title="Change User Password">
                    🔑 Password
                  </button>

                  <!-- Quick Reset Link Generator -->
                  <button type="button" onclick="generateQuickResetLink(<?= $u['id'] ?>, '<?= htmlspecialchars($u['username']) ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.45rem; color: #10b981; border-color: rgba(16,185,129,0.35);" title="Copy 24h One-Time Access Link">
                    ⚡ Reset
                  </button>

                  <!-- Issue Strike Modal Trigger -->
                  <button type="button" onclick="openStrikeModal(<?= $u['id'] ?>, '<?= htmlspecialchars($u['username']) ?>', <?= $stkCount ?>)" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.45rem; color: #ef4444; border-color: rgba(239,68,68,0.35);" title="Issue Editorial Strike (1-3)">
                    ⚠️ Strike
                  </button>

                  <!-- Audit Trail Drawer Trigger -->
                  <button type="button" onclick="openAuditTrailDrawer(<?= $u['id'] ?>, '<?= htmlspecialchars($u['username']) ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.45rem; color: #a855f7; border-color: rgba(168,85,247,0.35);" title="Slide-Out User Audit Trail Drawer">
                    📜 Audit
                  </button>

                  <!-- Impersonate Mode -->
                  <a href="<?= url('/admin/users/impersonate/' . $u['id']) ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.45rem; color: #f97316; border-color: rgba(249,115,22,0.4);" title="Login As User (Impersonate)">
                    👤 Login As
                  </a>

                  <!-- Terminate All Devices -->
                  <form action="<?= url('/admin/users/revoke-sessions/' . $u['id']) ?>" method="POST" onsubmit="return confirm('Terminate all active sessions across all devices for @<?= htmlspecialchars($u['username']) ?>?');" style="display: inline;">
                    <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.45rem; color: #ea580c; border-color: rgba(234,88,12,0.35);" title="Terminate Active Sessions Across All Devices">
                      ⛔ Revoke
                    </button>
                  </form>

                  <!-- Direct Notification Modal Trigger -->
                  <button type="button" onclick="openSingleNotificationModal(<?= $u['id'] ?>, '<?= htmlspecialchars($u['username'], ENT_QUOTES) ?>', '<?= htmlspecialchars($u['email'], ENT_QUOTES) ?>')" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.45rem; color: #c084fc; border-color: rgba(192,132,252,0.35);" title="Send In-App Notification directly to @<?= htmlspecialchars($u['username']) ?>">
                    📢 Notify
                  </button>

                  <!-- Edit Profile Modal Trigger -->
                  <button type="button" onclick='openEditUserModal(<?= json_encode($u) ?>)' class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.45rem; color: #cbd5e1;" title="Edit Profile">
                    ✏️ Edit
                  </button>


                  <!-- Delete User -->
                  <form action="<?= url('/admin/users/delete/' . $u['id']) ?>" method="POST" onsubmit="return confirm('Permanently erase user @<?= htmlspecialchars($u['username']) ?>? This action cannot be undone.');" style="display: inline;">
                    <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.45rem; color: #ef4444;" title="Delete User">
                      🗑
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" style="text-align: center; padding: 2.5rem; color: #64748b;">No users found in this segment.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ========================================================================= -->
<!-- COMPREHENSIVE 9-TAB USER DETAIL MODAL -->
<!-- ========================================================================= -->
<div id="userDetailModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-cyan" style="max-width: 900px; max-height: 90vh; display: flex; flex-direction: column;">
    <div class="admin-modal-header" style="flex: 0 0 auto;">
      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <div id="ud_avatar" style="width: 42px; height: 42px; border-radius: 50%; background: #0f172a; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #38bdf8; font-size: 1.1rem; border: 1px solid #38bdf8;"></div>
        <div>
          <h3 class="admin-modal-title" id="ud_name_header" style="color: #f8fafc; margin: 0; font-size: 1.25rem;">User Detail Hub</h3>
          <div style="font-size: 0.78rem; color: #64748b;" id="ud_sub_header">@username &bull; email@domain.com</div>
        </div>
      </div>
      <button type="button" onclick="closeUserDetailModal()" class="admin-modal-close">✕</button>
    </div>

    <!-- 10 Sub-Panel Navigation Strip -->
    <div id="ud_tabs_strip" style="display: flex; gap: 0.25rem; overflow-x: auto; background: rgba(15,23,42,0.8); padding: 0.5rem 1rem; border-bottom: 1px solid var(--admin-border-subtle); flex: 0 0 auto;">
      <?php
      $detailTabs = [
        'profile' => 'Profile',
        'content' => 'Content & Radar',
        'comments' => 'Comments',
        'bookmarks' => 'Saved Bookmarks 🔖',
        'reading_history' => 'Reading History 🕒',
        'tickets' => 'Support Tickets',
        'applications' => 'Author Apps',
        'subscriptions' => 'Subscriptions',
        'activity' => 'Activity Telemetry',
        'reports' => 'Reports & Flags',
        'membership' => 'Membership & RBAC',
        'payments' => 'Revenue & Payouts',
        'moderation' => 'Moderation & Strikes',
        'permissions' => 'Direct Permissions',
      ];
      ?>
      <?php foreach ($detailTabs as $dtKey => $dtLabel): ?>
        <button type="button" onclick="switchDetailSubTab('<?= $dtKey ?>')" id="udtab_<?= $dtKey ?>" class="ts-btn ts-btn-sm <?= $dtKey === 'profile' ? 'ts-btn-primary' : 'ts-btn-outline' ?>" style="font-size: 0.75rem; padding: 0.3rem 0.65rem; white-space: nowrap;">
          <?= $dtLabel ?>
        </button>
      <?php endforeach; ?>
    </div>

    <!-- Instant Shell High-Tech Glowing Pulse & Spinner Skeleton (0ms Perception) -->
    <div id="ud_modal_skeleton" class="ts-modal-skeleton-wrap" style="display: none; flex: 1 1 auto; overflow-y: auto;">
      <div class="ts-modal-skeleton-pulse">
        <div class="ts-modal-skeleton-spinner"></div>
        <div style="font-size: 0.88rem; color: #38bdf8; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;">
          Synchronizing User Telemetry...
        </div>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-top: 0.5rem;">
        <div class="skeleton-shimmer" style="height: 44px; border-radius: 8px;"></div>
        <div class="skeleton-shimmer" style="height: 44px; border-radius: 8px;"></div>
        <div class="skeleton-shimmer" style="height: 44px; border-radius: 8px;"></div>
        <div class="skeleton-shimmer" style="height: 44px; border-radius: 8px;"></div>
        <div class="skeleton-shimmer" style="height: 44px; border-radius: 8px; grid-column: 1 / -1;"></div>
        <div class="skeleton-shimmer" style="height: 80px; border-radius: 8px; grid-column: 1 / -1;"></div>
      </div>
    </div>

    <!-- Scrollable Modal Body -->
    <div style="overflow-y: auto; padding: 1.5rem; flex: 1 1 auto;" id="ud_modal_content_body">
      <!-- 1. Profile Tab -->
      <div class="ud-pane" id="udpane_profile">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
          <div>
            <label class="admin-modal-label">Display Name</label>
            <div id="ud_display_name" class="admin-modal-input" style="background: rgba(15,23,42,0.6); color: #f8fafc;"></div>
          </div>
          <div>
            <label class="admin-modal-label">Email Address</label>
            <div id="ud_email" class="admin-modal-input" style="background: rgba(15,23,42,0.6); color: #38bdf8;"></div>
          </div>
          <div>
            <label class="admin-modal-label">Username Handle</label>
            <div id="ud_username" class="admin-modal-input" style="background: rgba(15,23,42,0.6); color: #cbd5e1;"></div>
          </div>
          <div>
            <label class="admin-modal-label">Account Joined Date</label>
            <div id="ud_created_at" class="admin-modal-input" style="background: rgba(15,23,42,0.6); color: #cbd5e1;"></div>
          </div>
          <div style="grid-column: 1 / -1;">
            <label class="admin-modal-label">Headline</label>
            <div id="ud_headline" class="admin-modal-input" style="background: rgba(15,23,42,0.6); color: #cbd5e1;"></div>
          </div>
          <div style="grid-column: 1 / -1;">
            <label class="admin-modal-label">Biography</label>
            <div id="ud_bio" class="admin-modal-textarea" style="background: rgba(15,23,42,0.6); color: #cbd5e1; min-height: 80px;"></div>
          </div>
        </div>
      </div>

      <!-- 2. Content Tab -->
      <div class="ud-pane" id="udpane_content" style="display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
          <h4 style="color: #f8fafc; font-size: 1rem; margin: 0;">Authored Content &amp; Publications</h4>
          <span style="font-size: 0.78rem; color: #94a3b8;" id="ud_content_count_label"></span>
        </div>
        <div id="ud_content_list"></div>
      </div>

      <!-- 3. Comments Tab -->
      <div class="ud-pane" id="udpane_comments" style="display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
          <h4 style="color: #f8fafc; font-size: 1rem; margin: 0;">Discussion Comments &amp; Peer Reviews</h4>
          <span style="font-size: 0.78rem; color: #94a3b8;" id="ud_comments_count_label"></span>
        </div>
        <div id="ud_comments_list"></div>
      </div>

      <!-- Bookmarks & Saved Items Tab (Private User Activity visible to Admin) -->
      <div class="ud-pane" id="udpane_bookmarks" style="display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
          <h4 style="color: #f8fafc; font-size: 1rem; margin: 0;">🔖 Saved Bookmarks &amp; Private Reading List</h4>
          <span style="font-size: 0.78rem; color: #94a3b8;" id="ud_bookmarks_count_label"></span>
        </div>
        <div id="ud_bookmarks_list"></div>
      </div>

      <!-- Reading History & Scroll Progress Tab (Private User Activity visible to Admin) -->
      <div class="ud-pane" id="udpane_reading_history" style="display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
          <h4 style="color: #f8fafc; font-size: 1rem; margin: 0;">🕒 Reading History &amp; Progress Telemetry</h4>
          <span style="font-size: 0.78rem; color: #94a3b8;" id="ud_history_count_label"></span>
        </div>
        <div id="ud_history_list"></div>
      </div>

      <!-- 4. Support Tickets Tab -->
      <div class="ud-pane" id="udpane_tickets" style="display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
          <h4 style="color: #f8fafc; font-size: 1rem; margin: 0;">Submitted Support Tickets &amp; Help Desk</h4>
          <a href="<?= url('/admin/tickets') ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem; color: #38bdf8;">
            Open Tickets Queue ↗
          </a>
        </div>
        <div id="ud_tickets_list"></div>
      </div>

      <!-- 5. Author Applications Tab -->
      <div class="ud-pane" id="udpane_applications" style="display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
          <h4 style="color: #f8fafc; font-size: 1rem; margin: 0;">Author Creator Applications</h4>
          <a href="<?= url('/admin/applications') ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem; color: #ec4899;">
            Review Applications Hub ↗
          </a>
        </div>
        <div id="ud_applications_list"></div>
      </div>

      <!-- 6. Subscriptions Tab -->
      <div class="ud-pane" id="udpane_subscriptions" style="display: none;">
        <h4 style="color: #f8fafc; font-size: 1rem; margin-top: 0; margin-bottom: 0.75rem;">Subscribed Topics &amp; Followed Authors</h4>
        <div id="ud_subscriptions_list"></div>
      </div>

      <!-- 7. Activity Tab -->
      <div class="ud-pane" id="udpane_activity" style="display: none;">
        <h4 style="color: #f8fafc; font-size: 1rem; margin-top: 0; margin-bottom: 0.75rem;">Recent Audit Telemetry &amp; Logins</h4>
        <div id="ud_activity_list"></div>
      </div>

      <!-- 8. Reports Tab -->
      <div class="ud-pane" id="udpane_reports" style="display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
          <h4 style="color: #f8fafc; font-size: 1rem; margin: 0;">Entity Reports &amp; Flags</h4>
          <a href="<?= url('/admin/moderation') ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.2rem 0.5rem; color: #ef4444;">
            Moderation Station ↗
          </a>
        </div>
        <div id="ud_reports_list"></div>
      </div>

      <!-- 9. Membership Tab -->
      <div class="ud-pane" id="udpane_membership" style="display: none;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
          <div>
            <label class="admin-modal-label">Account Role</label>
            <div id="ud_role_badge" class="admin-modal-input" style="background: rgba(15,23,42,0.6); color: #a855f7; font-weight: 700;"></div>
          </div>
          <div>
            <label class="admin-modal-label">Account Status</label>
            <div id="ud_status_badge" class="admin-modal-input" style="background: rgba(15,23,42,0.6); color: #10b981; font-weight: 700;"></div>
          </div>
          <div>
            <label class="admin-modal-label">Verification Badge</label>
            <div id="ud_verif_badge" class="admin-modal-input" style="background: rgba(15,23,42,0.6); color: #38bdf8;"></div>
          </div>
          <div>
            <label class="admin-modal-label">Strike Count</label>
            <div id="ud_strike_count" class="admin-modal-input" style="background: rgba(15,23,42,0.6); color: #ef4444; font-weight: 700;"></div>
          </div>
        </div>
      </div>

      <!-- 10. Payments Tab -->
      <div class="ud-pane" id="udpane_payments" style="display: none;">
        <h4 style="color: #f8fafc; font-size: 1rem; margin-top: 0; margin-bottom: 0.75rem;">Author Payouts &amp; Revenue Ledger</h4>
        <div id="ud_payments_list"></div>
      </div>

      <!-- 11. Moderation Tab -->
      <div class="ud-pane" id="udpane_moderation" style="display: none;">
        <h4 style="color: #f8fafc; font-size: 1rem; margin-top: 0; margin-bottom: 0.75rem;">Moderation History &amp; Strikes</h4>
        <div id="ud_moderation_list"></div>
      </div>

      <!-- 12. Permissions Tab -->
      <div class="ud-pane" id="udpane_permissions" style="display: none;">
        <form id="ud_perm_form" action="" method="POST">
          <input type="hidden" name="action" value="update_permissions">
          <h4 style="color: #f8fafc; font-size: 1rem; margin-top: 0; margin-bottom: 1rem;">Direct RBAC &amp; Publishing Permissions</h4>
          <div style="display: flex; flex-direction: column; gap: 0.85rem; background: rgba(15,23,42,0.6); padding: 1.25rem; border-radius: 8px; border: 1px solid var(--admin-border-subtle); margin-bottom: 1.25rem;">
            <label style="display: flex; align-items: center; gap: 0.65rem; color: #f8fafc; cursor: pointer;">
              <input type="checkbox" name="can_use_templates" id="ud_perm_templates" value="1" style="accent-color: #6366f1; width: 16px; height: 16px;">
              <span><strong>Curated Templates Studio:</strong> Author can view, select, and save custom publication templates</span>
            </label>
            <label style="display: flex; align-items: center; gap: 0.65rem; color: #f8fafc; cursor: pointer;">
              <input type="checkbox" name="can_publish_prompts" id="ud_perm_prompts" value="1" style="accent-color: #10b981; width: 16px; height: 16px;">
              <span><strong>Prompt Blueprints:</strong> Author can publish to public Prompt Library</span>
            </label>
            <label style="display: flex; align-items: center; gap: 0.65rem; color: #f8fafc; cursor: pointer;">
              <input type="checkbox" name="can_publish_tools" id="ud_perm_tools" value="1" style="accent-color: #38bdf8; width: 16px; height: 16px;">
              <span><strong>AI Tools Radar:</strong> Author can submit and list developer AI tools</span>
            </label>
            <label style="display: flex; align-items: center; gap: 0.65rem; color: #f8fafc; cursor: pointer;">
              <input type="checkbox" name="can_publish_gpts" id="ud_perm_gpts" value="1" style="accent-color: #ec4899; width: 16px; height: 16px;">
              <span><strong>Custom GPTs Directory:</strong> Author can publish autonomous agents</span>
            </label>
          </div>
          <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #38bdf8; color: #000; font-weight: 700;">Save Permissions ✓</button>
        </form>
      </div>
    </div>
    </div>

    <div class="admin-modal-footer" style="flex: 0 0 auto;">
      <button type="button" onclick="closeUserDetailModal()" class="ts-btn ts-btn-outline ts-btn-sm">Close</button>
    </div>
  </div>
</div>

<!-- Modal: Create User -->
<div id="createUserModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-cyan" style="max-width: 500px;">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #38bdf8;"><i data-lucide="user-plus"></i> Add New Platform User</h3>
      <button type="button" onclick="closeCreateUserModal()" class="admin-modal-close">✕</button>
    </div>
    <form action="<?= url('/admin/users/create') ?>" method="POST">
      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Display Name</label>
        <input type="text" name="display_name" placeholder="e.g. Sarah Connor" class="admin-modal-input" required>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div>
          <label class="admin-modal-label">Username Handle</label>
          <input type="text" name="username" placeholder="sarah_c" class="admin-modal-input" required>
        </div>
        <div>
          <label class="admin-modal-label">Email Address</label>
          <input type="email" name="email" placeholder="sarah@example.com" class="admin-modal-input" required>
        </div>
      </div>
      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Initial Password</label>
        <input type="password" name="password" placeholder="••••••••" class="admin-modal-input" required minlength="6">
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label class="admin-modal-label">Account Role</label>
          <select name="role" class="admin-modal-select">
            <option value="member">Member</option>
            <option value="author">Author</option>
            <option value="editor">Editor</option>
            <option value="admin">Administrator</option>
          </select>
        </div>
        <div>
          <label class="admin-modal-label">Status</label>
          <select name="status" class="admin-modal-select">
            <option value="active">Active</option>
            <option value="suspended">Suspended</option>
          </select>
        </div>
      </div>
      <div class="admin-modal-footer">
        <button type="button" onclick="closeCreateUserModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #38bdf8; color: #000; font-weight: 700;">Create User ✓</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Edit User -->
<div id="editUserModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-cyan" style="max-width: 580px;">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #38bdf8;"><i data-lucide="edit-2"></i> Edit User Profile &amp; Governance</h3>
      <button type="button" onclick="closeEditUserModal()" class="admin-modal-close">✕</button>
    </div>

    <!-- Instant Shell Glowing Skeleton (0ms Perception) -->
    <div id="eu_modal_skeleton" class="ts-modal-skeleton-wrap" style="display: none;">
      <div class="ts-modal-skeleton-pulse">
        <div class="ts-modal-skeleton-spinner"></div>
        <div style="font-size: 0.85rem; color: #38bdf8; font-weight: 700;">Loading Profile Data...</div>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 0.5rem;">
        <div class="skeleton-shimmer" style="height: 42px; border-radius: 6px;"></div>
        <div class="skeleton-shimmer" style="height: 42px; border-radius: 6px;"></div>
        <div class="skeleton-shimmer" style="height: 42px; border-radius: 6px;"></div>
        <div class="skeleton-shimmer" style="height: 42px; border-radius: 6px;"></div>
        <div class="skeleton-shimmer" style="height: 42px; border-radius: 6px; grid-column: 1 / -1;"></div>
      </div>
    </div>

    <form id="editUserForm" action="" method="POST">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div>
          <label class="admin-modal-label">Display Name</label>
          <input type="text" name="display_name" id="eu_display_name" class="admin-modal-input" required>
        </div>
        <div>
          <label class="admin-modal-label">Email Address</label>
          <input type="email" name="email" id="eu_email" class="admin-modal-input" required>
        </div>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div>
          <label class="admin-modal-label">System Role</label>
          <select name="role" id="eu_role" class="admin-modal-select">
            <option value="member">Member</option>
            <option value="author">Author</option>
            <option value="editor">Editor</option>
            <option value="admin">Admin</option>
            <option value="super_admin">Super Admin</option>
          </select>
        </div>
        <div>
          <label class="admin-modal-label">Account Status</label>
          <select name="status" id="eu_status" class="admin-modal-select">
            <option value="active">Active</option>
            <option value="suspended">Suspended</option>
            <option value="banned">Banned</option>
          </select>
        </div>
        <div>
          <label class="admin-modal-label">Verification Badge</label>
          <select name="verification_badge" id="eu_badge" class="admin-modal-select">
            <option value="none">None</option>
            <option value="verified_builder">Verified Builder</option>
            <option value="expert">Expert</option>
            <option value="staff">Staff</option>
            <option value="top_creator">Top Creator</option>
          </select>
        </div>
      </div>
      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Headline</label>
        <input type="text" name="headline" id="eu_headline" class="admin-modal-input">
      </div>
      <div style="margin-bottom: 1.25rem;">
        <label class="admin-modal-label">Biography</label>
        <textarea name="bio" id="eu_bio" rows="3" class="admin-modal-textarea"></textarea>
      </div>
      <div class="admin-modal-footer">
        <button type="button" onclick="closeEditUserModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #38bdf8; color: #000; font-weight: 700;">Save Changes ✓</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Change Password -->
<div id="changePasswordModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-cyan" style="max-width: 460px;">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #f59e0b;"><i data-lucide="key"></i> Reset User Password</h3>
      <button type="button" onclick="closeChangePasswordModal()" class="admin-modal-close">✕</button>
    </div>
    <form id="changePasswordForm" action="" method="POST">
      <input type="hidden" name="action" value="change_password">
      <p style="font-size: 0.85rem; color: #94a3b8; margin-top: 0;" id="cp_user_label"></p>
      
      <div style="margin-bottom: 1.25rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
          <label class="admin-modal-label" style="margin: 0;">New Password</label>
          <button type="button" onclick="generateSecurePassword()" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.72rem; padding: 0.15rem 0.5rem; color: #38bdf8; border-color: rgba(56,189,248,0.3);">
            ⚡ Generate Password
          </button>
        </div>
        <input type="text" name="new_password" id="cp_new_pass_input" placeholder="Minimum 6 characters" class="admin-modal-input" required minlength="6" style="font-family: var(--admin-font-mono);">
        <div id="cp_copied_msg" style="display: none; font-size: 0.75rem; color: #10b981; margin-top: 0.35rem; font-weight: 600;">
          ✓ Strong password generated &amp; copied to clipboard!
        </div>
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="closeChangePasswordModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #f59e0b; border-color: #f59e0b; color: #000; font-weight: 700;">Update Password ✓</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Broadcast Notification -->
<div id="broadcastModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-cyan" style="max-width: 540px;">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #a855f7;"><i data-lucide="radio"></i> In-App Notification Dispatcher</h3>
      <button type="button" onclick="closeBroadcastModal()" class="admin-modal-close">✕</button>
    </div>
    <form action="<?= url('/admin/users/broadcast-notification') ?>" method="POST">
      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Target Audience Cohort</label>
        <select name="target_audience" id="notif_target_audience" onchange="toggleNotifAudience(this.value)" class="admin-modal-select">
          <optgroup label="Direct / Individual">
            <option value="single">Single Specific User 👤</option>
          </optgroup>
          <optgroup label="Creators & Content">
            <option value="authors">All Content Authors</option>
            <option value="verified_authors">Verified Guild Authors Only (Expert / Pro)</option>
            <option value="applicants">Pending Author Applicants</option>
          </optgroup>
          <optgroup label="Community & Readers">
            <option value="all" selected>All Active Users (Platform Broadcast)</option>
            <option value="members">Community Members Only</option>
            <option value="subscribers">Newsletter Opted-In Subscribers</option>
          </optgroup>
          <optgroup label="Governance & Standing">
            <option value="staff">Staff & Administrators (Admins & Mods)</option>
            <option value="strikes">Users with Warning Strikes (Editorial Notice)</option>
            <option value="inactive">Inactive Accounts (Dormant 30+ Days)</option>
          </optgroup>
        </select>
      </div>

      <!-- Single User Selection Box (Dynamic) -->
      <div id="box_notif_single_user" style="display: none; margin-bottom: 1rem; background: rgba(168,85,247,0.06); border: 1px solid rgba(168,85,247,0.25); border-radius: 8px; padding: 0.85rem;">
        <label class="admin-modal-label" style="color: #c084fc; margin-bottom: 0.35rem;">Select Direct User Recipient</label>
        <select name="single_user_id" id="notif_single_user_id" class="admin-modal-select" style="margin-bottom: 0.5rem;">
          <option value="">-- Choose User from Roster --</option>
          <?php foreach ($users as $optU): ?>
            <option value="<?= $optU['id'] ?>">#<?= $optU['id'] ?> - @<?= htmlspecialchars($optU['username']) ?> &bull; <?= htmlspecialchars($optU['email']) ?> (<?= strtoupper($optU['role']) ?>)</option>
          <?php endforeach; ?>
        </select>
        <div style="font-size: 0.75rem; color: #94a3b8; margin-bottom: 0.25rem;">Or type username, email or User ID directly:</div>
        <input type="text" name="single_user_search" id="notif_single_user_search" placeholder="e.g. alexvance or user@domain.com" class="admin-modal-input" style="font-size: 0.82rem;">
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
        <div>
          <label class="admin-modal-label">Notification Type</label>
          <select name="notif_type" class="admin-modal-select">
            <option value="announcement">📢 Announcement</option>
            <option value="system">⚡ System Alert</option>
            <option value="direct">💬 Direct Staff Message</option>
            <option value="reward">🏆 Reward / Milestone</option>
            <option value="moderation">🛡️ Moderation Notice</option>
          </select>
        </div>
        <div>
          <label class="admin-modal-label">Action URL (Optional)</label>
          <input type="text" name="notif_action_url" placeholder="/blog or /dashboard" class="admin-modal-input">
        </div>
      </div>

      <div style="margin-bottom: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
          <label class="admin-modal-label" style="margin: 0;">Notification Title <span style="color:#ef4444;">*</span></label>
          <span style="font-size: 0.72rem; color: #64748b;">Max 150 chars</span>
        </div>
        <input type="text" name="notif_title" id="notif_title_input" placeholder="e.g. Platform Architecture Update ⚡" class="admin-modal-input" maxlength="150" required>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
          <label class="admin-modal-label" style="margin: 0;">Notification Body <span style="color:#ef4444;">*</span></label>
          <span style="font-size: 0.72rem; color: #64748b;">Max 1000 chars</span>
        </div>
        <textarea name="notif_message" id="notif_message_input" rows="3" placeholder="Message content..." class="admin-modal-textarea" maxlength="1000" required></textarea>
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="closeBroadcastModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #a855f7; border-color: #a855f7; color: #fff; font-weight: 700;">Dispatch Notification 📢</button>
      </div>
    </form>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: MERGE ACCOUNTS (ITEM 13) -->
<!-- ========================================================================= -->
<div id="mergeAccountsModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-cyan" style="max-width: 580px;">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #38bdf8;"><i data-lucide="git-merge"></i> Consolidate Duplicate User Accounts</h3>
      <button type="button" onclick="closeMergeModal()" class="admin-modal-close">✕</button>
    </div>
    <form action="<?= url('/admin/users/merge') ?>" method="POST" onsubmit="return confirm('Consolidate these two accounts? The source user will be permanently deactivated and all their articles, bookmarks, comments, and history reassigned to the target user.');">
      <div style="background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.25); border-radius: 8px; padding: 0.85rem 1rem; margin-bottom: 1.25rem; font-size: 0.82rem; color: #f59e0b; line-height: 1.5;">
        ⚠️ <strong>Important:</strong> All published posts, draft content, comments, saved bookmarks, reading history, and tickets belonging to the <strong>Source Account</strong> will be safely reassigned to the <strong>Target Primary Account</strong>.
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
          <label class="admin-modal-label">Source User (To Merge &amp; Deactivate)</label>
          <select name="source_user_id" class="admin-modal-select" required>
            <option value="">-- Select Source User --</option>
            <?php foreach ($users as $optU): ?>
              <option value="<?= $optU['id'] ?>">#<?= $optU['id'] ?> - @<?= htmlspecialchars($optU['username']) ?> (<?= htmlspecialchars($optU['email']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="admin-modal-label">Target Primary User (To Keep)</label>
          <select name="target_user_id" class="admin-modal-select" required>
            <option value="">-- Select Target User --</option>
            <?php foreach ($users as $optU): ?>
              <option value="<?= $optU['id'] ?>">#<?= $optU['id'] ?> - @<?= htmlspecialchars($optU['username']) ?> (<?= htmlspecialchars($optU['email']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="closeMergeModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #38bdf8; color: #000; font-weight: 700;">Consolidate &amp; Merge Accounts 🔀</button>
      </div>
    </form>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: BULK EMAIL NOTIFICATION DISPATCHER (ITEM 19) -->
<!-- ========================================================================= -->
<div id="bulkEmailModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-cyan" style="max-width: 620px;">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #f59e0b;"><i data-lucide="mail"></i> Bulk Email Notification Dispatcher</h3>
      <button type="button" onclick="closeBulkEmailModal()" class="admin-modal-close">✕</button>
    </div>
    <form action="<?= url('/admin/users/bulk-email') ?>" method="POST">
      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Target Recipient Cohort</label>
        <select name="email_cohort" class="admin-modal-select">
          <option value="all">All Active Community Members &amp; Authors</option>
          <option value="authors">Verified Content Authors Only</option>
          <option value="members">Standard Community Members Only</option>
          <option value="editors">Editorial Staff &amp; Editors</option>
          <option value="suspended">Suspended Accounts Only (Re-engagement / Notice)</option>
        </select>
      </div>

      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Email Subject</label>
        <input type="text" name="email_subject" placeholder="e.g. Important Engineering Updates from TechScout" class="admin-modal-input" required>
      </div>

      <div style="margin-bottom: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
          <label class="admin-modal-label" style="margin: 0;">Email Message Body (Plain Text or HTML)</label>
          <span style="font-size: 0.72rem; color: #64748b;">Placeholders: <code>{username}</code>, <code>{email}</code>, <code>{app_name}</code></span>
        </div>
        <textarea name="email_body" rows="6" placeholder="Hello {username},\n\nWe are pleased to announce..." class="admin-modal-textarea" required></textarea>
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="closeBulkEmailModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #f59e0b; border-color: #f59e0b; color: #000; font-weight: 700;">Send Bulk Announcement ✉️</button>
      </div>
    </form>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: ISSUE STRUCTURED STRIKE (ITEM 12) -->
<!-- ========================================================================= -->
<div id="issueStrikeModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-cyan" style="max-width: 500px;">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #ef4444;"><i data-lucide="shield-alert"></i> Issue Editorial Strike &amp; Penalty</h3>
      <button type="button" onclick="closeStrikeModal()" class="admin-modal-close">✕</button>
    </div>
    <form id="issueStrikeForm" action="" method="POST">
      <p style="font-size: 0.85rem; color: #94a3b8; margin-top: 0;" id="stk_user_label"></p>

      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Penalty Escalation Level</label>
        <select name="penalty_type" id="stk_penalty_type" class="admin-modal-select">
          <option value="warning">1st Strike: Formal Written Warning (Remains Active)</option>
          <option value="suspension">2nd Strike: Account Suspension (14-Day Lockout)</option>
          <option value="ban">3rd Strike: Permanent Account Ban &amp; Session Termination</option>
        </select>
      </div>

      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Violation Reason (Sent to User)</label>
        <input type="text" name="strike_reason" placeholder="e.g. Plagiarism of copyrighted tutorial or repetitive spam" class="admin-modal-input" required>
      </div>

      <div style="margin-bottom: 1rem;">
        <label class="admin-modal-label">Expiration Date (Optional)</label>
        <input type="datetime-local" name="expires_at" class="admin-modal-input">
        <span style="font-size: 0.72rem; color: #64748b;">Leave blank for default duration (Warning: 30 days, Suspension: 14 days, Ban: Permanent).</span>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label class="admin-modal-label">Internal Staff Notes</label>
        <textarea name="strike_notes" rows="2" placeholder="Internal notes for audit trail..." class="admin-modal-textarea"></textarea>
      </div>

      <div class="admin-modal-footer">
        <button type="button" onclick="closeStrikeModal()" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #ef4444; border-color: #ef4444; color: #fff; font-weight: 700;">Issue Strike ⚠️</button>
      </div>
    </form>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: TIER PROGRESSION & CUSTOM BADGE (ITEMS 17 & 18) -->
<!-- ========================================================================= -->
<div id="tierBadgeModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-cyan" style="max-width: 540px;">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #38bdf8;"><i data-lucide="award"></i> Author Tier &amp; Custom Badge Manager</h3>
      <button type="button" onclick="closeTierBadgeModal()" class="admin-modal-close">✕</button>
    </div>

    <!-- Tier Promotion Form -->
    <form id="tierForm" action="" method="POST" style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1.25rem;">
      <h4 style="color: #f59e0b; margin: 0 0 0.75rem 0; font-size: 0.95rem;">Author Tier Progression (Item 17)</h4>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div>
          <label class="admin-modal-label">Preset Tier Hierarchy</label>
          <select name="tier_code" id="tb_tier_code" class="admin-modal-select" onchange="updateTierInputs(this.value)">
            <option value="junior">Junior Scout</option>
            <option value="contributor">Contributing Scout</option>
            <option value="staff">Staff Author</option>
            <option value="fellow">Distinguished Fellow</option>
            <option value="custom">Custom Tier Override...</option>
          </select>
        </div>
        <div>
          <label class="admin-modal-label">Custom Tier Title</label>
          <input type="text" name="tier_title" id="tb_tier_title" class="admin-modal-input" placeholder="Title">
        </div>
      </div>
      <div style="display: flex; justify-content: flex-end;">
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #f59e0b; color: #000; font-weight: 700;">Update Author Tier ✓</button>
      </div>
    </form>

    <!-- Custom Verification Badge Form -->
    <form id="badgeForm" action="" method="POST">
      <h4 style="color: #38bdf8; margin: 0 0 0.75rem 0; font-size: 0.95rem;">Custom Verification Checkmark Badges (Item 18)</h4>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div>
          <label class="admin-modal-label">Preset Badge</label>
          <select name="verification_badge" id="tb_badge_preset" class="admin-modal-select">
            <option value="none">None</option>
            <option value="verified_builder">Verified Builder</option>
            <option value="expert">Expert</option>
            <option value="staff">Staff</option>
            <option value="top_creator">Top Creator</option>
          </select>
        </div>
        <div>
          <label class="admin-modal-label">Custom Checkmark Badge Label</label>
          <input type="text" name="custom_badge_label" id="tb_custom_label" placeholder="e.g. Core Contributor, Security Researcher" class="admin-modal-input">
        </div>
      </div>
      <div style="margin-bottom: 1.25rem;">
        <label class="admin-modal-label">Badge Color</label>
        <input type="color" name="custom_badge_color" id="tb_custom_color" value="#38bdf8" style="width: 100%; height: 38px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.15); background: transparent; cursor: pointer;">
      </div>
      <div class="admin-modal-footer">
        <button type="button" onclick="closeTierBadgeModal()" class="ts-btn ts-btn-outline ts-btn-sm">Close</button>
        <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #38bdf8; color: #000; font-weight: 700;">Save Custom Badge ✓</button>
      </div>
    </form>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: QUICK PASSWORD RESET LINK GENERATOR (ITEM 14) -->
<!-- ========================================================================= -->
<div id="quickResetModal" class="admin-modal-overlay">
  <div class="admin-modal-box modal-cyan" style="max-width: 520px;">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title" style="color: #10b981;"><i data-lucide="key"></i> 24-Hour One-Time Access Link</h3>
      <button type="button" onclick="closeQuickResetModal()" class="admin-modal-close">✕</button>
    </div>
    <div style="padding: 0.5rem 0;">
      <p style="font-size: 0.85rem; color: #cbd5e1; margin-top: 0;">
        A secure 24-hour single-use token was created for <strong id="qr_username_label" style="color: #38bdf8;"></strong>. You can share this link with users locked out of their inbox.
      </p>

      <div style="margin-bottom: 1.25rem;">
        <label class="admin-modal-label">1-Click Quick Login Link (Direct Access)</label>
        <div style="display: flex; gap: 0.5rem;">
          <input type="text" id="qr_quick_login_input" readonly class="admin-modal-input" style="font-family: monospace; font-size: 0.8rem; background: #090d16;">
          <button type="button" onclick="copyQuickInput('qr_quick_login_input')" class="ts-btn ts-btn-primary ts-btn-sm" style="background: #10b981; border-color: #10b981; color: #000; font-weight: 700; white-space: nowrap;">
            Copy Link 📋
          </button>
        </div>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label class="admin-modal-label">Password Reset URL (Sets New Password)</label>
        <div style="display: flex; gap: 0.5rem;">
          <input type="text" id="qr_reset_input" readonly class="admin-modal-input" style="font-family: monospace; font-size: 0.8rem; background: #090d16;">
          <button type="button" onclick="copyQuickInput('qr_reset_input')" class="ts-btn ts-btn-outline ts-btn-sm" style="white-space: nowrap;">
            Copy 📋
          </button>
        </div>
      </div>

      <div id="qr_copied_alert" style="display: none; background: rgba(16,185,129,0.1); border: 1px solid #10b981; border-radius: 6px; padding: 0.5rem; font-size: 0.8rem; color: #10b981; text-align: center; font-weight: 600;">
        ✓ Link copied to clipboard!
      </div>
    </div>
    <div class="admin-modal-footer">
      <button type="button" onclick="closeQuickResetModal()" class="ts-btn ts-btn-outline ts-btn-sm">Done</button>
    </div>
  </div>
</div>

<!-- ========================================================================= -->
<!-- SLIDE-OUT USER AUDIT TRAIL DRAWER (ITEM 16) -->
<!-- ========================================================================= -->
<div id="userAuditBackdrop" onclick="closeAuditTrailDrawer()" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); z-index: 999998; transition: opacity 0.3s ease;"></div>

<div id="userAuditDrawer" style="position: fixed; top: 0; right: -650px; width: 620px; max-width: 95vw; height: 100vh; background: #0b1120; border-left: 1px solid var(--admin-border-subtle); z-index: 999999; box-shadow: -15px 0 40px rgba(0,0,0,0.75); transition: right 0.35s cubic-bezier(0.16, 1, 0.3, 1); display: flex; flex-direction: column; overflow: hidden;">
  <!-- Drawer Header -->
  <div style="padding: 1.25rem 1.5rem; background: #0f172a; border-bottom: 1px solid var(--admin-border-subtle); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.5rem;">
        <span class="ts-badge ts-badge-purple" style="font-size: 0.65rem;">SECURITY &amp; AUDIT TRAIL</span>
        <span style="font-size: 0.75rem; color: #64748b;" id="uad_user_handle">@username</span>
      </div>
      <h3 style="font-size: 1.25rem; color: #f8fafc; margin: 0.25rem 0 0 0; font-family: 'Outfit', sans-serif;" id="uad_title">User Audit Trail</h3>
    </div>
    <button type="button" onclick="closeAuditTrailDrawer()" class="admin-modal-close" style="font-size: 1.2rem;">✕</button>
  </div>

  <!-- Drawer Tab Buttons Strip -->
  <div style="display: flex; gap: 0.25rem; background: #090d16; padding: 0.5rem 1rem; border-bottom: 1px solid var(--admin-border-subtle); overflow-x: auto; flex-shrink: 0;">
    <button type="button" onclick="switchDrawerTab('sessions', this)" class="ts-btn ts-btn-sm ts-btn-primary" style="font-size: 0.75rem; padding: 0.3rem 0.65rem;">
      💻 Logins &amp; Devices (20)
    </button>
    <button type="button" onclick="switchDrawerTab('timeline', this)" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.75rem; padding: 0.3rem 0.65rem;">
      📜 Security Log
    </button>
    <button type="button" onclick="switchDrawerTab('anomalies', this)" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.75rem; padding: 0.3rem 0.65rem;">
      ⚠️ Anomalies
    </button>
    <button type="button" onclick="switchDrawerTab('strikes', this)" class="ts-btn ts-btn-sm ts-btn-outline" style="font-size: 0.75rem; padding: 0.3rem 0.65rem;">
      🛑 Strikes Log
    </button>
  </div>

  <!-- Drawer Scrollable Body -->
  <div style="flex: 1; overflow-y: auto; padding: 1.5rem;">
    <!-- Subtab 1: Logins & Sessions -->
    <div id="drawer-tab-sessions">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <span style="font-size: 0.85rem; color: #94a3b8; font-weight: 600;">Last 20 Logins, IP Changes &amp; User Agents</span>
        <div id="uad_device_revoke_wrap"></div>
      </div>
      <div id="uad_sessions_list" style="display: flex; flex-direction: column; gap: 0.75rem;">
        <div style="text-align: center; color: #64748b; padding: 2rem;">Loading session telemetry...</div>
      </div>
    </div>

    <!-- Subtab 2: Security Timeline & Events -->
    <div id="drawer-tab-timeline" style="display: none;">
      <div style="font-size: 0.85rem; color: #94a3b8; font-weight: 600; margin-bottom: 1rem;">Security Updates &amp; Authorization Events</div>
      <div id="uad_timeline_list" style="display: flex; flex-direction: column; gap: 0.75rem;">
        <div style="text-align: center; color: #64748b; padding: 2rem;">Loading timeline...</div>
      </div>
    </div>

    <!-- Subtab 3: Security Anomalies -->
    <div id="drawer-tab-anomalies" style="display: none;">
      <div style="font-size: 0.85rem; color: #94a3b8; font-weight: 600; margin-bottom: 1rem;">Anomaly Detection &amp; Threat Forensics</div>
      <div id="uad_anomalies_list" style="display: flex; flex-direction: column; gap: 0.75rem;">
        <div style="text-align: center; color: #64748b; padding: 2rem;">Loading anomalies...</div>
      </div>
    </div>

    <!-- Subtab 4: Strikes History -->
    <div id="drawer-tab-strikes" style="display: none;">
      <div style="font-size: 0.85rem; color: #94a3b8; font-weight: 600; margin-bottom: 1rem;">Editorial Strikes &amp; Penalties History</div>
      <div id="uad_strikes_list" style="display: flex; flex-direction: column; gap: 0.75rem;">
        <div style="text-align: center; color: #64748b; padding: 2rem;">Loading strikes...</div>
      </div>
    </div>
  </div>
</div>

<script>
function openCreateUserModal() {
  document.getElementById('createUserModal').style.display = 'flex';
}
function closeCreateUserModal() {
  document.getElementById('createUserModal').style.display = 'none';
}

function openEditUserModal(u) {
  const modal = document.getElementById('editUserModal');
  const skeleton = document.getElementById('eu_modal_skeleton');
  const form = document.getElementById('editUserForm');

  // Instant Shell Display (0ms Perception)
  modal.style.display = 'flex';

  if (typeof u === 'object' && u !== null) {
    if (skeleton) skeleton.style.display = 'none';
    if (form) form.style.display = 'block';
    document.getElementById('editUserForm').action = `<?= url('/admin/users/edit/') ?>${u.id}`;
    document.getElementById('eu_display_name').value = u.display_name || u.username || '';
    document.getElementById('eu_email').value = u.email || '';
    document.getElementById('eu_role').value = u.role || 'member';
    document.getElementById('eu_status').value = u.status || 'active';
    document.getElementById('eu_badge').value = u.verification_badge || 'none';
    document.getElementById('eu_headline').value = u.headline || '';
    document.getElementById('eu_bio').value = u.bio || '';
    if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') lucide.createIcons();
  } else {
    // If user ID was passed, show high-tech glowing pulse skeleton immediately
    if (skeleton) skeleton.style.display = 'block';
    if (form) form.style.display = 'none';
    fetch(`<?= url('/admin/users/detail/') ?>${u}`)
      .then(r => r.json())
      .then(d => {
        if (skeleton) skeleton.style.display = 'none';
        if (form) form.style.display = 'block';
        if (d.success && d.user) {
          const usr = d.user;
          document.getElementById('editUserForm').action = `<?= url('/admin/users/edit/') ?>${usr.id}`;
          document.getElementById('eu_display_name').value = usr.display_name || usr.username || '';
          document.getElementById('eu_email').value = usr.email || '';
          document.getElementById('eu_role').value = usr.role || 'member';
          document.getElementById('eu_status').value = usr.status || 'active';
          document.getElementById('eu_badge').value = usr.verification_badge || 'none';
          document.getElementById('eu_headline').value = usr.headline || '';
          document.getElementById('eu_bio').value = usr.bio || '';
          if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') lucide.createIcons();
        }
      })
      .catch(err => {
        if (skeleton) skeleton.style.display = 'none';
        if (form) form.style.display = 'block';
      });
  }
}
function closeEditUserModal() {
  document.getElementById('editUserModal').style.display = 'none';
  const skeleton = document.getElementById('eu_modal_skeleton');
  if (skeleton) skeleton.style.display = 'none';
}

function openChangePasswordModal(userId, username) {
  document.getElementById('changePasswordForm').action = `<?= url('/admin/users/action/') ?>${userId}`;
  document.getElementById('cp_user_label').innerText = `Set a new secure password for @${username}.`;
  document.getElementById('cp_copied_msg').style.display = 'none';
  document.getElementById('cp_new_pass_input').value = '';
  document.getElementById('changePasswordModal').style.display = 'flex';
}
function closeChangePasswordModal() {
  document.getElementById('changePasswordModal').style.display = 'none';
}

function generateSecurePassword() {
  const chars = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$%^&*()_+';
  let pass = '';
  for (let i = 0; i < 14; i++) {
    pass += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  const input = document.getElementById('cp_new_pass_input');
  input.value = pass;
  input.focus();
  
  if (navigator.clipboard) {
    navigator.clipboard.writeText(pass).then(() => {
      const msg = document.getElementById('cp_copied_msg');
      msg.style.display = 'block';
      setTimeout(() => { msg.style.display = 'none'; }, 4000);
    });
  }
}

function openBroadcastModal() {
  document.getElementById('broadcastModal').style.display = 'flex';
}
function closeBroadcastModal() {
  document.getElementById('broadcastModal').style.display = 'none';
}

function openSingleNotificationModal(userId, username, email) {
  openBroadcastModal();
  const audSelect = document.getElementById('notif_target_audience');
  if (audSelect) {
    audSelect.value = 'single';
    toggleNotifAudience('single');
  }
  const userSelect = document.getElementById('notif_single_user_id');
  if (userSelect) {
    userSelect.value = userId;
  }
  const searchInput = document.getElementById('notif_single_user_search');
  if (searchInput) {
    searchInput.value = username || email || userId;
  }
  const titleInput = document.getElementById('notif_title_input');
  if (titleInput && (!titleInput.value || titleInput.value.startsWith('Notice for @'))) {
    titleInput.value = `Notice for @${username}`;
  }
}

function toggleNotifAudience(val) {
  const boxSingle = document.getElementById('box_notif_single_user');
  if (boxSingle) {
    boxSingle.style.display = (val === 'single') ? 'block' : 'none';
  }
}

// 9-Tab User Detail Modal Fetcher with 0ms Instant Shell & Skeleton Pulse
function openUserDetailModal(userId) {
  const modal = document.getElementById('userDetailModal');
  const skeleton = document.getElementById('ud_modal_skeleton');
  const body = document.getElementById('ud_modal_content_body');
  const tabsStrip = document.getElementById('ud_tabs_strip');

  // Instant Shell Trigger: Immediately display modal (0ms perception)
  modal.style.display = 'flex';
  if (skeleton) skeleton.style.display = 'block';
  if (body) body.style.display = 'none';
  if (tabsStrip) tabsStrip.style.display = 'none';

  document.getElementById('ud_name_header').innerText = 'Loading Member Profile #' + userId + '...';
  document.getElementById('ud_sub_header').innerText = 'Synchronizing telemetry, publishing history & security log...';
  document.getElementById('ud_avatar').innerHTML = '<span class="ts-spinner" style="width: 18px; height: 18px; border: 2px solid rgba(56,189,248,0.3); border-top-color: #38bdf8; border-radius: 50%; display: inline-block; animation: tsSpin 0.7s linear infinite;"></span>';

  fetch(`<?= url('/admin/users/detail/') ?>${userId}`)
    .then(r => r.json())
    .then(d => {
      // Replace skeleton with loaded data
      if (skeleton) skeleton.style.display = 'none';
      if (body) body.style.display = 'block';
      if (tabsStrip) tabsStrip.style.display = 'flex';

      if (!d.success) {
        alert(d.message || 'Error loading user details.');
        return;
      }
      const u = d.user;
      document.getElementById('ud_avatar').innerText = (u.display_name || u.username).substring(0, 1).toUpperCase();
      document.getElementById('ud_name_header').innerText = (u.display_name || u.username) + ` (#${u.id})`;
      document.getElementById('ud_sub_header').innerText = `@${u.username} • ${u.email} • Role: ${u.role.toUpperCase()}`;

      // Profile tab
      document.getElementById('ud_display_name').innerText = u.display_name || 'Not configured';
      document.getElementById('ud_email').innerText = u.email;
      document.getElementById('ud_username').innerText = '@' + u.username;
      document.getElementById('ud_created_at').innerText = u.created_at;
      document.getElementById('ud_headline').innerText = u.headline || 'No headline set.';
      document.getElementById('ud_bio').innerText = u.bio || 'No biography written.';

      // Membership tab
      document.getElementById('ud_role_badge').innerText = u.role.toUpperCase();
      document.getElementById('ud_status_badge').innerText = u.status.toUpperCase();
      document.getElementById('ud_verif_badge').innerText = u.verification_badge || 'none';
      document.getElementById('ud_strike_count').innerText = u.strike_count || 0;

      // Permissions form
      document.getElementById('ud_perm_form').action = `<?= url('/admin/users/action/') ?>${u.id}`;
      document.getElementById('ud_perm_templates').checked = parseInt(u.can_use_templates, 10) === 1;
      document.getElementById('ud_perm_prompts').checked = parseInt(u.can_publish_prompts, 10) === 1;
      document.getElementById('ud_perm_tools').checked = parseInt(u.can_publish_tools, 10) === 1;
      document.getElementById('ud_perm_gpts').checked = parseInt(u.can_publish_gpts, 10) === 1;

      // 1. Content List with direct live & admin links
      const cList = document.getElementById('ud_content_list');
      const cCountLabel = document.getElementById('ud_content_count_label');
      if (d.contents && d.contents.length > 0) {
        if (cCountLabel) cCountLabel.innerText = `${d.contents.length} publications`;
        cList.innerHTML = '<table class="admin-table"><thead><tr><th>Title &amp; Live Link</th><th>Type</th><th>Status</th><th>Views</th><th>Created</th><th>Actions</th></tr></thead><tbody>' +
          d.contents.map(c => {
            let liveUrl = `<?= url('/article/') ?>${c.slug}`;
            if (c.content_type === 'ai_tool') liveUrl = `<?= url('/tools/') ?>${c.slug}`;
            else if (c.content_type === 'prompt') liveUrl = `<?= url('/prompts/') ?>${c.slug}`;
            else if (c.content_type === 'gpt_listing') liveUrl = `<?= url('/gpts/') ?>${c.slug}`;
            else if (c.content_type === 'campaign') liveUrl = `<?= url('/campaigns/') ?>${c.slug}`;

            return `<tr>
              <td>
                <div style="font-weight:600; color:#f8fafc; margin-bottom:0.15rem;">${c.title}</div>
                <a href="${liveUrl}" target="_blank" style="font-size:0.75rem; color:#38bdf8; text-decoration:none; display:inline-flex; align-items:center; gap:0.25rem;">
                  ${liveUrl} ↗
                </a>
              </td>
              <td><span class="ts-badge ts-badge-cyan" style="font-size:0.7rem;">${c.content_type}</span></td>
              <td><span class="ts-badge ${c.status === 'published' ? 'ts-badge-green' : (c.status === 'in_review' ? 'ts-badge-pink' : 'ts-badge-amber')}" style="font-size:0.7rem;">${c.status.toUpperCase()}</span></td>
              <td style="color:#38bdf8; font-weight:700;">${c.view_count || 0}</td>
              <td style="font-size:0.78rem; color:#64748b;">${c.created_at ? c.created_at.substring(0, 10) : ''}</td>
              <td>
                <div style="display:flex; gap:0.35rem;">
                  <a href="${liveUrl}" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size:0.7rem; padding:0.15rem 0.45rem; color:#38bdf8;" title="View Live Page">
                    Open ↗
                  </a>
                  <a href="<?= url('/admin/articles') ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size:0.7rem; padding:0.15rem 0.45rem; color:#cbd5e1;" title="Manage in Admin">
                    Edit ✏️
                  </a>
                </div>
              </td>
            </tr>`;
          }).join('') + '</tbody></table>';
      } else {
        if (cCountLabel) cCountLabel.innerText = '0 publications';
        cList.innerHTML = '<div style="text-align:center; padding:2rem; color:#64748b;">No publications authored by this user yet.</div>';
      }

      // 2. Comments List with contextual publication links
      const comList = document.getElementById('ud_comments_list');
      const comCountLabel = document.getElementById('ud_comments_count_label');
      if (d.comments && d.comments.length > 0) {
        if (comCountLabel) comCountLabel.innerText = `${d.comments.length} comments`;
        comList.innerHTML = '<div style="display:flex; flex-direction:column; gap:0.75rem;">' +
          d.comments.map(cm => `
            <div style="background:rgba(15,23,42,0.6); padding:1rem; border-radius:8px; border:1px solid var(--admin-border-subtle);">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.4rem; flex-wrap:wrap; gap:0.5rem;">
                <div style="font-size:0.82rem; font-weight:700;">
                  <span style="color:#64748b;">Posted on:</span>
                  <a href="<?= url('/article/') ?>${cm.content_slug || ''}" target="_blank" style="color:#38bdf8; text-decoration:none; margin-left:0.25rem;">
                    ${cm.content_title || 'Publication'} ↗
                  </a>
                </div>
                <div style="display:flex; align-items:center; gap:0.5rem;">
                  <span class="ts-badge ts-badge-cyan" style="font-size:0.68rem;">👍 ${cm.reaction_count || 0} Helpful</span>
                  <span style="font-size:0.72rem; color:#64748b;">${cm.created_at}</span>
                </div>
              </div>
              <div style="color:#cbd5e1; font-size:0.88rem; line-height:1.5; background:rgba(0,0,0,0.25); padding:0.65rem 0.85rem; border-radius:6px;">
                ${cm.body}
              </div>
            </div>
          `).join('') + '</div>';
      } else {
        if (comCountLabel) comCountLabel.innerText = '0 comments';
        comList.innerHTML = '<div style="text-align:center; padding:2rem; color:#64748b;">No discussion comments posted by this user.</div>';
      }

      // 2.1 Private Bookmarks
      const bmList = document.getElementById('ud_bookmarks_list');
      const bmCountLabel = document.getElementById('ud_bookmarks_count_label');
      if (d.bookmarks && d.bookmarks.length > 0) {
        if (bmCountLabel) bmCountLabel.innerText = `${d.bookmarks.length} saved items`;
        bmList.innerHTML = '<table class="admin-table"><thead><tr><th>Saved Content Title</th><th>Type</th><th>Saved On</th><th>Action</th></tr></thead><tbody>' +
          d.bookmarks.map(b => {
            let bUrl = `<?= url('/article/') ?>${b.slug}`;
            if (b.content_type === 'ai_tool') bUrl = `<?= url('/tools/') ?>${b.slug}`;
            else if (b.content_type === 'prompt') bUrl = `<?= url('/prompts/') ?>${b.slug}`;
            else if (b.content_type === 'gpt_listing') bUrl = `<?= url('/gpts/') ?>${b.slug}`;
            return `<tr>
              <td style="font-weight:600; color:#f8fafc;">${b.title}</td>
              <td><span class="ts-badge ts-badge-cyan" style="font-size:0.68rem;">${b.content_type.toUpperCase()}</span></td>
              <td style="font-size:0.78rem; color:#64748b;">${b.created_at}</td>
              <td><a href="${bUrl}" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size:0.7rem; padding:0.15rem 0.45rem; color:#38bdf8;">Open ↗</a></td>
            </tr>`;
          }).join('') + '</tbody></table>';
      } else {
        if (bmCountLabel) bmCountLabel.innerText = '0 items';
        bmList.innerHTML = '<div style="text-align:center; padding:2rem; color:#64748b;">User has no saved bookmarks.</div>';
      }

      // 2.2 Private Reading History
      const rhList = document.getElementById('ud_history_list');
      const rhCountLabel = document.getElementById('ud_history_count_label');
      if (d.readingHistory && d.readingHistory.length > 0) {
        if (rhCountLabel) rhCountLabel.innerText = `${d.readingHistory.length} tracked items`;
        rhList.innerHTML = '<table class="admin-table"><thead><tr><th>Content Title</th><th>Progress</th><th>Read Duration</th><th>Last Read At</th><th>Action</th></tr></thead><tbody>' +
          d.readingHistory.map(h => {
            let hUrl = `<?= url('/article/') ?>${h.slug}`;
            if (h.content_type === 'ai_tool') hUrl = `<?= url('/tools/') ?>${h.slug}`;
            else if (h.content_type === 'prompt') hUrl = `<?= url('/prompts/') ?>${h.slug}`;
            return `<tr>
              <td style="font-weight:600; color:#f8fafc;">${h.title}</td>
              <td><span class="ts-badge ts-badge-green" style="font-size:0.68rem;">${h.progress_percent || 0}% read</span></td>
              <td style="font-size:0.78rem; color:#cbd5e1;">${Math.round((h.read_seconds || 0) / 60)} mins</td>
              <td style="font-size:0.78rem; color:#64748b;">${h.last_read_at}</td>
              <td><a href="${hUrl}" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size:0.7rem; padding:0.15rem 0.45rem; color:#38bdf8;">Open ↗</a></td>
            </tr>`;
          }).join('') + '</tbody></table>';
      } else {
        if (rhCountLabel) rhCountLabel.innerText = '0 items';
        rhList.innerHTML = '<div style="text-align:center; padding:2rem; color:#64748b;">No reading history records found for this user.</div>';
      }

      // 3. Support Tickets with live conversation links
      const tList = document.getElementById('ud_tickets_list');
      if (d.tickets && d.tickets.length > 0) {
        tList.innerHTML = '<table class="admin-table"><thead><tr><th>Ticket #</th><th>Subject</th><th>Priority</th><th>Status</th><th>Messages</th><th>Created</th><th>Action</th></tr></thead><tbody>' +
          d.tickets.map(t => `<tr>
            <td style="font-family:var(--admin-font-mono); font-weight:700; color:#38bdf8;">#${t.ticket_number}</td>
            <td style="font-weight:600; color:#f8fafc;">${t.subject}</td>
            <td><span class="ts-badge ${t.priority === 'urgent' ? 'ts-badge-pink' : (t.priority === 'high' ? 'ts-badge-amber' : 'ts-badge-cyan')}" style="font-size:0.68rem;">${t.priority.toUpperCase()}</span></td>
            <td><span class="ts-badge ${t.status === 'open' ? 'ts-badge-pink' : (t.status === 'resolved' ? 'ts-badge-green' : 'ts-badge-cyan')}" style="font-size:0.68rem;">${t.status.toUpperCase()}</span></td>
            <td style="text-align:center; color:#cbd5e1;">${t.message_count || 1}</td>
            <td style="font-size:0.78rem; color:#64748b;">${t.created_at ? t.created_at.substring(0, 10) : ''}</td>
            <td>
              <a href="<?= url('/support/ticket/') ?>${t.ticket_number}" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size:0.7rem; padding:0.15rem 0.5rem; color:#38bdf8;">
                View Thread ↗
              </a>
            </td>
          </tr>`).join('') + '</tbody></table>';
      } else {
        tList.innerHTML = '<div style="text-align:center; padding:2rem; color:#64748b;">No support tickets opened by this user.</div>';
      }

      // 4. Author Applications
      const appList = document.getElementById('ud_applications_list');
      if (d.applications && d.applications.length > 0) {
        appList.innerHTML = '<div style="display:flex; flex-direction:column; gap:0.75rem;">' +
          d.applications.map(ap => `
            <div style="background:rgba(15,23,42,0.6); padding:1.25rem; border-radius:8px; border:1px solid var(--admin-border-subtle);">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
                <strong style="color:#f8fafc; font-size:0.95rem;">${ap.headline || 'Author Application'}</strong>
                <span class="ts-badge ${ap.status === 'approved' ? 'ts-badge-green' : (ap.status === 'rejected' ? 'strike-badge' : 'ts-badge-pink')}">${ap.status.toUpperCase()}</span>
              </div>
              <div style="font-size:0.85rem; color:#cbd5e1; line-height:1.5; margin-bottom:0.75rem;">
                ${ap.sample_pitch || 'No sample pitch provided.'}
              </div>
              ${ap.portfolio_url ? `<div style="font-size:0.8rem; margin-bottom:0.5rem;"><a href="${ap.portfolio_url}" target="_blank" style="color:#38bdf8;">Portfolio Link ↗</a></div>` : ''}
              <div style="font-size:0.75rem; color:#64748b;">Applied on: ${ap.created_at}</div>
            </div>
          `).join('') + '</div>';
      } else {
        appList.innerHTML = '<div style="text-align:center; padding:2rem; color:#64748b;">No author applications on record for this user.</div>';
      }

      // 5. Subscriptions (Topics & Authors)
      const subList = document.getElementById('ud_subscriptions_list');
      if (d.subscriptions && d.subscriptions.length > 0) {
        subList.innerHTML = '<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:0.75rem;">' +
          d.subscriptions.map(s => {
            const isTopic = s.subscribable_type === 'topic';
            const name = isTopic ? s.topic_name : s.author_name;
            const destUrl = isTopic ? `<?= url('/topic/') ?>${s.topic_slug}` : `<?= url('/author/profile/') ?>${s.author_slug}`;
            return `
              <div style="background:rgba(15,23,42,0.6); padding:0.85rem 1rem; border-radius:8px; border:1px solid var(--admin-border-subtle); display:flex; justify-content:space-between; align-items:center;">
                <div>
                  <span class="ts-badge ${isTopic ? 'ts-badge-cyan' : 'ts-badge-pink'}" style="font-size:0.65rem; margin-bottom:0.25rem;">
                    ${isTopic ? 'TOPIC' : 'AUTHOR'}
                  </span>
                  <div style="font-weight:700; color:#f8fafc; font-size:0.88rem;">${name || 'Subscription'}</div>
                </div>
                <a href="${destUrl}" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size:0.7rem; padding:0.15rem 0.45rem; color:#38bdf8;">
                  Open ↗
                </a>
              </div>
            `;
          }).join('') + '</div>';
      } else {
        subList.innerHTML = '<div style="text-align:center; padding:2rem; color:#64748b;">No topic or creator subscriptions registered.</div>';
      }

      // 6. Activity List
      const aList = document.getElementById('ud_activity_list');
      if (d.activity && d.activity.length > 0) {
        aList.innerHTML = '<table class="admin-table"><thead><tr><th>Action</th><th>Entity</th><th>IP Address</th><th>Timestamp</th></tr></thead><tbody>' +
          d.activity.map(a => `<tr>
            <td style="font-weight:600; color:#38bdf8; font-family:var(--admin-font-mono);">${a.action}</td>
            <td>${a.entity_type} #${a.entity_id || '0'}</td>
            <td style="color:#94a3b8; font-family:var(--admin-font-mono);">${a.ip_address || '—'}</td>
            <td style="font-size:0.78rem; color:#64748b;">${a.created_at}</td>
          </tr>`).join('') + '</tbody></table>';
      } else {
        aList.innerHTML = '<p style="color:#64748b; font-size:0.88rem;">No activity events logged.</p>';
      }

      // 7. Reports List
      const repList = document.getElementById('ud_reports_list');
      if (d.reports && d.reports.length > 0) {
        repList.innerHTML = '<table class="admin-table"><thead><tr><th>Entity Target</th><th>Reason</th><th>Status</th><th>Submitted</th><th>Action</th></tr></thead><tbody>' +
          d.reports.map(r => `<tr>
            <td style="color:#f8fafc; font-weight:600;">${r.entity_type || 'Content'} #${r.entity_id || ''}</td>
            <td><span class="ts-badge ts-badge-amber" style="font-size:0.68rem;">${r.reason || 'Flagged'}</span></td>
            <td><span class="ts-badge ${r.status === 'actioned' ? 'ts-badge-green' : (r.status === 'pending' ? 'ts-badge-pink' : 'ts-badge-cyan')}" style="font-size:0.68rem;">${r.status.toUpperCase()}</span></td>
            <td style="font-size:0.78rem; color:#64748b;">${r.created_at}</td>
            <td>
              <a href="<?= url('/admin/moderation') ?>" target="_blank" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size:0.7rem; padding:0.15rem 0.5rem; color:#ef4444;">
                Review in Station ↗
              </a>
            </td>
          </tr>`).join('') + '</tbody></table>';
      } else {
        repList.innerHTML = '<div style="text-align:center; padding:2rem; color:#64748b;">No entity reports submitted or flagged for this user.</div>';
      }

      // 8. Payments List
      const pList = document.getElementById('ud_payments_list');
      let pOut = `<div style="margin-bottom:1rem; font-size:0.9rem; color:#10b981; font-weight:700;">Promo Credits Balance: $${parseFloat(u.promo_credits || 0).toFixed(2)}</div>`;
      if (d.payouts && d.payouts.length > 0) {
        pOut += '<table class="admin-table"><thead><tr><th>Amount</th><th>Method</th><th>Status</th><th>Tx Reference</th></tr></thead><tbody>' +
          d.payouts.map(p => `<tr>
            <td style="font-weight:700; color:#10b981;">$${parseFloat(p.amount_usd).toFixed(2)}</td>
            <td>${p.payout_method}</td>
            <td><span class="ts-badge ${p.status === 'paid' ? 'ts-badge-green' : 'ts-badge-amber'}">${p.status.toUpperCase()}</span></td>
            <td style="font-size:0.78rem; font-family:var(--admin-font-mono);">${p.transaction_reference || '—'}</td>
          </tr>`).join('') + '</tbody></table>';
      } else {
        pOut += '<p style="color:#64748b; font-size:0.88rem;">No withdrawal requests found.</p>';
      }
      pList.innerHTML = pOut;

      // 9. Moderation History & Strikes
      const modList = document.getElementById('ud_moderation_list');
      if (d.moderationLogs && d.moderationLogs.length > 0) {
        modList.innerHTML = '<table class="admin-table"><thead><tr><th>Action</th><th>Details</th><th>IP</th><th>Date</th></tr></thead><tbody>' +
          d.moderationLogs.map(m => `<tr>
            <td style="font-weight:700; color:#ef4444;">${m.action}</td>
            <td style="font-size:0.82rem; color:#cbd5e1;">${typeof m.metadata === 'object' ? JSON.stringify(m.metadata) : (m.metadata || '—')}</td>
            <td style="font-family:var(--admin-font-mono); color:#94a3b8;">${m.ip_address || '—'}</td>
            <td style="font-size:0.78rem; color:#64748b;">${m.created_at}</td>
          </tr>`).join('') + '</tbody></table>';
      } else {
        modList.innerHTML = '<div style="text-align:center; padding:2rem; color:#10b981;">✓ Clean moderation record. No strikes or disciplinary actions on file.</div>';
      }

      switchDetailSubTab('profile');
      if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') lucide.createIcons();
    })
    .catch(err => {
      const skeleton = document.getElementById('ud_modal_skeleton');
      const body = document.getElementById('ud_modal_content_body');
      const tabsStrip = document.getElementById('ud_tabs_strip');
      if (skeleton) skeleton.style.display = 'none';
      if (body) body.style.display = 'block';
      if (tabsStrip) tabsStrip.style.display = 'flex';
      console.error(err);
      if (typeof showToast === 'function') {
        showToast('Failed to load user details: ' + (err.message || 'Server exception'), 'error');
      } else {
        alert('Failed to load user details: ' + (err.message || 'Server exception'));
      }
    });
}

function switchDetailSubTab(tabKey) {
  document.querySelectorAll('[id^="udtab_"]').forEach(b => {
    b.classList.remove('ts-btn-primary');
    b.classList.add('ts-btn-outline');
  });
  const activeBtn = document.getElementById('udtab_' + tabKey);
  if (activeBtn) {
    activeBtn.classList.remove('ts-btn-outline');
    activeBtn.classList.add('ts-btn-primary');
  }

  document.querySelectorAll('.ud-pane').forEach(p => p.style.display = 'none');
  const target = document.getElementById('udpane_' + tabKey);
  if (target) target.style.display = 'block';
}

function closeUserDetailModal() {
  document.getElementById('userDetailModal').style.display = 'none';
  const skeleton = document.getElementById('ud_modal_skeleton');
  const body = document.getElementById('ud_modal_content_body');
  const tabsStrip = document.getElementById('ud_tabs_strip');
  if (skeleton) skeleton.style.display = 'none';
  if (body) body.style.display = 'block';
  if (tabsStrip) tabsStrip.style.display = 'flex';
}

// ---------------------------------------------------------------------------
// Item 13: Merge Duplicate Accounts
// ---------------------------------------------------------------------------
function openMergeModal() {
  document.getElementById('mergeAccountsModal').style.display = 'flex';
}
function closeMergeModal() {
  document.getElementById('mergeAccountsModal').style.display = 'none';
}

// ---------------------------------------------------------------------------
// Item 19: Bulk Email Notification Dispatcher
// ---------------------------------------------------------------------------
function openBulkEmailModal() {
  document.getElementById('bulkEmailModal').style.display = 'flex';
}
function closeBulkEmailModal() {
  document.getElementById('bulkEmailModal').style.display = 'none';
}

// ---------------------------------------------------------------------------
// Item 12: Editorial Strikes & Penalties Log
// ---------------------------------------------------------------------------
function openStrikeModal(userId, username, currentStrikes) {
  document.getElementById('issueStrikeForm').action = `<?= url('/admin/users/strike/') ?>${userId}`;
  document.getElementById('stk_user_label').innerHTML = `Issuing structured penalty to <strong style="color: #38bdf8;">@${username}</strong>. Current Active Strikes: <strong>${currentStrikes}</strong>.`;
  
  const select = document.getElementById('stk_penalty_type');
  if (currentStrikes === 0) select.value = 'warning';
  else if (currentStrikes === 1) select.value = 'suspension';
  else select.value = 'ban';

  document.getElementById('issueStrikeModal').style.display = 'flex';
}
function closeStrikeModal() {
  document.getElementById('issueStrikeModal').style.display = 'none';
}

// ---------------------------------------------------------------------------
// Items 17 & 18: Author Tier Progression & Custom Verification Badges
// ---------------------------------------------------------------------------
function openTierBadgeModal(u) {
  document.getElementById('tierForm').action = `<?= url('/admin/authors/set-tier/') ?>${u.id}`;
  document.getElementById('badgeForm').action = `<?= url('/admin/authors/set-badge/') ?>${u.id}`;

  document.getElementById('tb_tier_code').value = u.current_tier_code || u.tier_code || 'junior';
  document.getElementById('tb_tier_title').value = u.tier_title || '';
  document.getElementById('tb_badge_preset').value = u.verification_badge || 'none';
  document.getElementById('tb_custom_label').value = u.custom_badge_label || '';
  document.getElementById('tb_custom_color').value = u.custom_badge_color || '#38bdf8';

  document.getElementById('tierBadgeModal').style.display = 'flex';
}
function closeTierBadgeModal() {
  document.getElementById('tierBadgeModal').style.display = 'none';
}

function updateTierInputs(val) {
  const titleInput = document.getElementById('tb_tier_title');
  if (val === 'junior') titleInput.value = 'Junior Scout';
  else if (val === 'contributor') titleInput.value = 'Contributing Scout';
  else if (val === 'staff') titleInput.value = 'Staff Author';
  else if (val === 'fellow') titleInput.value = 'Distinguished Fellow';
}

// ---------------------------------------------------------------------------
// Item 14: Quick Password Reset Link Generator
// ---------------------------------------------------------------------------
function generateQuickResetLink(userId, username) {
  document.getElementById('qr_username_label').innerText = '@' + username;
  document.getElementById('qr_quick_login_input').value = 'Generating secure token...';
  document.getElementById('qr_reset_input').value = 'Generating secure token...';
  document.getElementById('qr_copied_alert').style.display = 'none';
  document.getElementById('quickResetModal').style.display = 'flex';

  fetch(`<?= url('/admin/users/quick-reset-link/') ?>${userId}`, {
    method: 'POST',
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) {
      document.getElementById('qr_quick_login_input').value = d.quick_login_url;
      document.getElementById('qr_reset_input').value = d.reset_url;
    } else {
      alert(d.message || 'Could not generate reset link');
      closeQuickResetModal();
    }
  })
  .catch(err => {
    alert('Request failed: ' + err.message);
    closeQuickResetModal();
  });
}
function closeQuickResetModal() {
  document.getElementById('quickResetModal').style.display = 'none';
}
function copyQuickInput(inputId) {
  const el = document.getElementById(inputId);
  if (!el) return;
  el.select();
  navigator.clipboard.writeText(el.value).then(() => {
    const alertBox = document.getElementById('qr_copied_alert');
    if (alertBox) {
      alertBox.style.display = 'block';
      setTimeout(() => { alertBox.style.display = 'none'; }, 3500);
    }
  });
}

// ---------------------------------------------------------------------------
// Item 16: User Audit Trail Drawer
// ---------------------------------------------------------------------------
function openAuditTrailDrawer(userId, username) {
  document.getElementById('uad_user_handle').innerText = '@' + username;
  document.getElementById('uad_title').innerText = `Audit Trail: @${username}`;
  document.getElementById('userAuditBackdrop').style.display = 'block';
  
  const revokeWrap = document.getElementById('uad_device_revoke_wrap');
  revokeWrap.innerHTML = `
    <form action="<?= url('/admin/users/revoke-sessions/') ?>${userId}" method="POST" onsubmit="return confirm('Terminate all active sessions for @${username}?');" style="display:inline;">
      <button type="submit" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size:0.72rem; color:#ea580c; border-color:rgba(234,88,12,0.4);">
        ⛔ Revoke All Remote Sessions
      </button>
    </form>
  `;

  document.getElementById('uad_sessions_list').innerHTML = '<div style="text-align:center; padding:2rem; color:#64748b;">Loading session telemetry...</div>';
  document.getElementById('uad_timeline_list').innerHTML = '<div style="text-align:center; padding:2rem; color:#64748b;">Loading timeline...</div>';
  document.getElementById('uad_anomalies_list').innerHTML = '<div style="text-align:center; padding:2rem; color:#64748b;">Loading anomalies...</div>';
  document.getElementById('uad_strikes_list').innerHTML = '<div style="text-align:center; padding:2rem; color:#64748b;">Loading strikes...</div>';

  const drawer = document.getElementById('userAuditDrawer');
  drawer.style.right = '0';

  fetch(`<?= url('/admin/users/audit-trail/') ?>${userId}`)
    .then(r => r.json())
    .then(d => {
      if (!d.success) {
        alert(d.message || 'Failed to fetch audit data');
        return;
      }
      
      // Render Sessions
      const sWrap = document.getElementById('uad_sessions_list');
      if (d.sessions && d.sessions.length > 0) {
        sWrap.innerHTML = d.sessions.map(s => `
          <div style="background: rgba(15,23,42,0.7); border: 1px solid var(--admin-border-subtle); border-radius: 8px; padding: 0.85rem 1rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.35rem;">
              <div>
                <strong style="color: #f8fafc; font-size: 0.88rem;">${s.device_type || 'Desktop Device'}</strong>
                <span style="font-size: 0.72rem; color: #64748b; margin-left: 0.5rem;">${s.platform || ''} • ${s.browser || ''}</span>
              </div>
              <span class="ts-badge ${s.is_current ? 'ts-badge-green' : 'ts-badge-cyan'}" style="font-size: 0.65rem;">
                ${s.is_current ? 'CURRENT' : 'ACTIVE'}
              </span>
            </div>
            <div style="font-size: 0.76rem; color: #94a3b8; font-family: monospace; margin-bottom: 0.25rem;">
              IP: ${s.ip_address || '127.0.0.1'}
            </div>
            <div style="font-size: 0.72rem; color: #64748b; word-break: break-all; margin-bottom: 0.35rem;">
              ${s.user_agent || 'Unknown User Agent'}
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.7rem; color: #64748b;">
              <span>Logged In: ${s.created_at || '—'}</span>
              <span>Last Active: ${s.last_active_at || '—'}</span>
            </div>
          </div>
        `).join('');
      } else {
        sWrap.innerHTML = '<div style="text-align: center; color: #64748b; padding: 2rem;">No logged active sessions found.</div>';
      }

      // Render Security Timeline
      const tWrap = document.getElementById('uad_timeline_list');
      if (d.timeline && d.timeline.length > 0) {
        tWrap.innerHTML = d.timeline.map(t => `
          <div style="background: rgba(15,23,42,0.7); border: 1px solid var(--admin-border-subtle); border-radius: 8px; padding: 0.85rem 1rem; border-left: 3px solid #38bdf8;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
              <strong style="color: #38bdf8; font-size: 0.85rem; font-family: monospace;">${t.action}</strong>
              <span style="font-size: 0.72rem; color: #64748b;">${t.created_at}</span>
            </div>
            <div style="font-size: 0.78rem; color: #cbd5e1; margin-bottom: 0.25rem;">
              Target: ${t.entity_type} #${t.entity_id || '0'}
            </div>
            <div style="font-size: 0.72rem; color: #94a3b8; font-family: monospace;">
              IP: ${t.ip_address || '—'}
            </div>
          </div>
        `).join('');
      } else {
        tWrap.innerHTML = '<div style="text-align: center; color: #64748b; padding: 2rem;">No security events logged.</div>';
      }

      // Render Anomalies
      const aWrap = document.getElementById('uad_anomalies_list');
      if (d.anomalies && d.anomalies.length > 0) {
        aWrap.innerHTML = d.anomalies.map(a => `
          <div style="background: rgba(239,68,68,0.06); border: 1px solid rgba(239,68,68,0.3); border-radius: 8px; padding: 0.85rem 1rem; border-left: 3px solid #ef4444;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
              <strong style="color: #ef4444; font-size: 0.85rem;">⚠️ ${a.anomaly_type || 'Suspicious Activity'}</strong>
              <span style="font-size: 0.72rem; color: #ef4444;">${a.severity ? a.severity.toUpperCase() : 'ALERT'}</span>
            </div>
            <div style="font-size: 0.8rem; color: #fca5a5; margin-bottom: 0.35rem;">
              ${a.description || 'Rapid repeated submissions or anomaly trigger'}
            </div>
            <div style="font-size: 0.72rem; color: #64748b;">
              Detected: ${a.detected_at || a.created_at || 'Recently'}
            </div>
          </div>
        `).join('');
      } else {
        aWrap.innerHTML = '<div style="text-align: center; color: #10b981; padding: 2rem;">✓ No suspicious activity or security anomalies detected.</div>';
      }

      // Render Strikes History
      const stkWrap = document.getElementById('uad_strikes_list');
      if (d.strikes && d.strikes.length > 0) {
        stkWrap.innerHTML = d.strikes.map(s => `
          <div style="background: rgba(15,23,42,0.7); border: 1px solid var(--admin-border-subtle); border-radius: 8px; padding: 0.85rem 1rem; border-left: 3px solid ${s.is_active ? '#ef4444' : '#64748b'};">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
              <span class="ts-badge ${s.is_active ? 'strike-badge' : 'ts-badge-cyan'}" style="font-size: 0.7rem;">
                STRIKE #${s.strike_level}: ${s.penalty_type.toUpperCase()}
              </span>
              <span style="font-size: 0.72rem; color: ${s.is_active ? '#ef4444' : '#64748b'}; font-weight: 600;">
                ${s.is_active ? 'ACTIVE' : 'PARDONED / EXPIRED'}
              </span>
            </div>
            <div style="font-weight: 600; color: #f8fafc; font-size: 0.85rem; margin-bottom: 0.25rem;">
              ${s.reason}
            </div>
            ${s.notes ? `<div style="font-size: 0.76rem; color: #94a3b8; margin-bottom: 0.35rem;"><em>Note: ${s.notes}</em></div>` : ''}
            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.7rem; color: #64748b;">
              <span>Issued: ${s.created_at}</span>
              <span>Expires: ${s.expires_at || 'Never (Permanent)'}</span>
            </div>
            ${s.is_active ? `
              <div style="margin-top: 0.5rem; text-align: right;">
                <form action="<?= url('/admin/users/strike/pardon/') ?>${s.id}" method="POST" onsubmit="return confirm('Pardon and clear this strike penalty?');" style="display: inline;">
                  <button type="submit" class="ts-btn ts-btn-outline ts-btn-xs" style="font-size: 0.68rem; color: #10b981; border-color: rgba(16,185,129,0.4);">
                    ✓ Pardon Strike
                  </button>
                </form>
              </div>
            ` : ''}
          </div>
        `).join('');
      } else {
        stkWrap.innerHTML = '<div style="text-align: center; color: #10b981; padding: 2rem;">✓ Clean editorial record. No strikes on file.</div>';
      }
    })
    .catch(err => {
      alert('Failed to load audit trail: ' + err.message);
    });
}

function closeAuditTrailDrawer() {
  document.getElementById('userAuditDrawer').style.right = '-650px';
  document.getElementById('userAuditBackdrop').style.display = 'none';
}

function switchDrawerTab(tabKey, btn) {
  ['sessions', 'timeline', 'anomalies', 'strikes'].forEach(k => {
    const el = document.getElementById('drawer-tab-' + k);
    if (el) el.style.display = (k === tabKey) ? 'block' : 'none';
  });

  const parent = btn.parentElement;
  if (parent) {
    parent.querySelectorAll('button').forEach(b => {
      b.classList.remove('ts-btn-primary');
      b.classList.add('ts-btn-outline');
    });
  }
  btn.classList.remove('ts-btn-outline');
  btn.classList.add('ts-btn-primary');
}

// Auto-trigger modal/drawer for visual QA matrix
window.addEventListener('DOMContentLoaded', () => {
  const p = new URLSearchParams(window.location.search);
  const openTarget = p.get('qa_open');
  if (openTarget === 'view') {
    openUserDetailModal(2);
  } else if (openTarget === 'password') {
    openChangePasswordModal(2, 'alexvance');
    generateSecurePassword();
  } else if (openTarget === 'edit') {
    openEditUserModal({
      id: 2,
      username: 'alexvance',
      display_name: 'Alex Vance',
      email: 'alex@techscout.online',
      role: 'author',
      status: 'active',
      verification_badge: 'expert',
      headline: 'Systems Architect & Lead Technical Writer',
      bio: 'Senior Cloud Systems Architect and specialized AI engineer.'
    });
  } else if (openTarget === 'audit') {
    openAuditTrailDrawer(2, 'alexvance');
  } else if (openTarget === 'merge') {
    openMergeModal();
  } else if (openTarget === 'bulk_email') {
    openBulkEmailModal();
  } else if (openTarget === 'reset') {
    document.getElementById('qr_username_label').innerText = '@alexvance';
    document.getElementById('qr_quick_login_input').value = '<?= url('/auth/quick-login?token=b4d858349fa8172901c0b396b1892ef0') ?>';
    document.getElementById('qr_reset_input').value = '<?= url('/reset-password?token=b4d858349fa8172901c0b396b1892ef0') ?>';
    document.getElementById('quickResetModal').style.display = 'flex';
  } else if (openTarget === 'strike') {
    openStrikeModal(2, 'alexvance', 1);
  } else if (openTarget === 'tier') {
    openTierBadgeModal({
      id: 2,
      username: 'alexvance',
      display_name: 'Alex Vance',
      current_tier_code: 'contributor',
      tier_title: 'Contributing Scout',
      verification_badge: 'expert',
      custom_badge_label: 'Tech Scout Architect',
      custom_badge_color: '#38bdf8'
    });
  }
});

// Global Window Scope Function Binding (Guarantees button onclick handlers always resolve)
window.openCreateUserModal = openCreateUserModal;
window.closeCreateUserModal = closeCreateUserModal;
window.openEditUserModal = openEditUserModal;
window.closeEditUserModal = closeEditUserModal;
window.openChangePasswordModal = openChangePasswordModal;
window.closeChangePasswordModal = closeChangePasswordModal;
window.generateSecurePassword = generateSecurePassword;
window.openBroadcastModal = openBroadcastModal;
window.closeBroadcastModal = closeBroadcastModal;
window.openSingleNotificationModal = openSingleNotificationModal;
window.toggleNotifAudience = toggleNotifAudience;
window.openUserDetailModal = openUserDetailModal;
window.closeUserDetailModal = closeUserDetailModal;
window.switchDetailSubTab = switchDetailSubTab;
window.openAuditTrailDrawer = openAuditTrailDrawer;
window.closeAuditTrailDrawer = closeAuditTrailDrawer;
window.switchDrawerTab = switchDrawerTab;
window.openMergeModal = openMergeModal;
window.closeMergeModal = closeMergeModal;
window.openBulkEmailModal = openBulkEmailModal;
window.closeBulkEmailModal = closeBulkEmailModal;
window.openStrikeModal = openStrikeModal;
window.closeStrikeModal = closeStrikeModal;
window.openTierBadgeModal = openTierBadgeModal;
window.closeTierBadgeModal = closeTierBadgeModal;
window.closeQuickResetModal = closeQuickResetModal;
window.copyQuickInput = copyQuickInput;
window.generateQuickResetLink = generateQuickResetLink;

</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
