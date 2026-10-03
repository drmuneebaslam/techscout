<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php
$currentAvatar = image_url($user['author_avatar'] ?? $user['avatar_url'] ?? '');
$userDisplayName = $user['display_name'] ?? $user['username'] ?? 'User';
$userBadgeLabel = ($user['custom_badge_label'] ?? '') ?: ($user['verification_badge'] ?? '');
?>

<main class="container ts-settings-page-main">
  <!-- Account Settings Header: Compact & Branded -->
  <div class="ts-account-header-card">
    <div class="ts-account-header-flex">
      <div class="ts-account-user-meta">
        <div class="ts-avatar-wrap">
          <?php if (!empty($currentAvatar)): ?>
            <img src="<?= htmlspecialchars($currentAvatar) ?>" alt="Avatar" class="ts-header-avatar" onerror="this.src='https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80'">
          <?php else: ?>
            <div class="ts-header-avatar ts-avatar-fallback">
              <?= strtoupper(substr($userDisplayName, 0, 2)) ?>
            </div>
          <?php endif; ?>
          <span class="ts-avatar-status-badge" title="Account Active"></span>
        </div>

        <div class="ts-user-details">
          <div class="ts-user-title-row">
            <h1 class="ts-account-name">
              <?= htmlspecialchars($userDisplayName) ?>
            </h1>
            <span class="ts-badge ts-badge-cyan">
              <?= strtoupper($user['role'] ?? 'MEMBER') ?>
            </span>
            <?php if (!empty($user['verification_badge']) && $user['verification_badge'] !== 'none'): ?>
              <span class="ts-badge ts-badge-green">
                ✓ <?= htmlspecialchars($userBadgeLabel) ?>
              </span>
            <?php endif; ?>
          </div>
          <div class="ts-account-subline">
            @<?= htmlspecialchars($user['username'] ?? 'user') ?> &bull; <?= htmlspecialchars($user['email'] ?? '') ?>
          </div>
        </div>
      </div>

      <!-- Quick Action Buttons -->
      <div class="ts-account-header-actions">
        <a href="<?= url('/profile/' . urlencode($user['author_slug'] ?? $user['username'])) ?>" class="ts-btn ts-btn-outline ts-btn-xs" title="View Public Profile">
          <i data-lucide="external-link" style="width: 13px; height: 13px;"></i> <span>View Profile</span>
        </a>
        <?php if ($user['role'] === 'member'): ?>
          <a href="<?= url('/author/apply') ?>" class="ts-btn ts-btn-primary ts-btn-xs ts-btn-write-gradient" title="Apply for Creator Scopes">
            <i data-lucide="sparkles" style="width: 13px; height: 13px;"></i> <span>Write ✨</span>
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Main Settings Container with Responsive Tabbed Layout -->
  <div class="ts-settings-layout">
    <!-- Responsive Navigation (Horizontal Sticky Pill Strip on Mobile, Sidebar on Desktop) -->
    <nav class="ts-settings-nav-card" aria-label="Settings navigation">
      <div class="ts-settings-nav-strip">
        <button type="button" onclick="switchSettingsTab('profile', this)" class="settings-nav-btn active" id="nav-btn-profile">
          <i data-lucide="user" style="width: 14px; height: 14px;"></i>
          <span>Profile</span>
        </button>
        <button type="button" onclick="switchSettingsTab('security', this)" class="settings-nav-btn" id="nav-btn-security">
          <i data-lucide="key" style="width: 14px; height: 14px;"></i>
          <span>Security</span>
        </button>
        <button type="button" onclick="switchSettingsTab('devices', this)" class="settings-nav-btn" id="nav-btn-devices">
          <i data-lucide="smartphone" style="width: 14px; height: 14px;"></i>
          <span>Devices</span>
          <span class="ts-badge ts-badge-count"><?= count($sessions) ?></span>
        </button>
        <button type="button" onclick="switchSettingsTab('strikes', this)" class="settings-nav-btn" id="nav-btn-strikes">
          <i data-lucide="shield-alert" style="width: 14px; height: 14px;"></i>
          <span>Standing</span>
          <span class="ts-badge <?= empty($activeStrikes) ? 'ts-badge-green' : 'ts-badge-amber' ?>" style="font-size: 0.65rem; padding: 0.1rem 0.35rem;">
            <?= count($activeStrikes) ?>/3
          </span>
        </button>
        <button type="button" onclick="switchSettingsTab('activity', this)" class="settings-nav-btn" id="nav-btn-activity">
          <i data-lucide="activity" style="width: 14px; height: 14px;"></i>
          <span>Activity</span>
        </button>
        <button type="button" onclick="switchSettingsTab('preferences', this)" class="settings-nav-btn" id="nav-btn-preferences">
          <i data-lucide="bell" style="width: 14px; height: 14px;"></i>
          <span>Alerts</span>
        </button>
      </div>

      <div class="ts-settings-nav-footer">
        <a href="<?= url('/auth/logout') ?>" class="ts-btn ts-btn-outline ts-btn-xs" style="width: 100%; color: #ef4444; border-color: rgba(239,68,68,0.3); font-weight: 600; justify-content: center;">
          <i data-lucide="log-out" style="width: 14px; height: 14px;"></i> <span>Sign Out</span>
        </a>
      </div>
    </nav>

    <!-- Content Tabs Panels -->
    <div class="ts-settings-content-card">
      
      <!-- TAB 1: Profile & Biography -->
      <div id="tab-profile" class="settings-tab-panel">
        <div class="ts-tab-header">
          <h2 class="ts-tab-title">Personal Profile &amp; Biography</h2>
          <p class="ts-tab-desc">
            Manage your identity, avatar, headline, and bio visible to the TechScout community.
          </p>
        </div>

        <form action="<?= url('/account/profile/update') ?>" method="POST" enctype="multipart/form-data">
          <!-- Compact Avatar Upload Box -->
          <div class="ts-avatar-upload-box">
            <div class="ts-avatar-preview-wrap">
              <img id="avatar_live_preview" src="<?= !empty($currentAvatar) ? htmlspecialchars($currentAvatar) : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80' ?>" alt="Avatar Preview" class="ts-avatar-preview-img" onerror="this.src='https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80'">
            </div>
            <div class="ts-avatar-controls">
              <label class="ts-input-label" style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem;">
                <i data-lucide="camera" style="width: 14px; height: 14px; color: #38bdf8;"></i> Upload Photo
              </label>
              <input type="file" name="avatar_file" id="avatar_file_input" accept="image/png,image/jpeg,image/webp,image/gif" class="ts-form-input ts-file-input" onchange="previewAvatarImage(this)">
              <div class="ts-help-text">
                PNG, JPG, WEBP, GIF up to 5MB. Rendered across author cards.
              </div>
            </div>
          </div>

          <div class="ts-form-grid-2">
            <div>
              <label class="ts-input-label">Display Name <span class="ts-label-sub">(Max 60 chars)</span></label>
              <input type="text" name="display_name" value="<?= htmlspecialchars($userDisplayName) ?>" maxlength="60" class="ts-form-input" required>
            </div>
            <div>
              <label class="ts-input-label">Username <span class="ts-label-sub">(Permanent handle)</span></label>
              <input type="text" value="@<?= htmlspecialchars($user['username'] ?? '') ?>" class="ts-form-input" disabled style="opacity: 0.6; cursor: not-allowed;">
            </div>
          </div>

          <div class="ts-form-row">
            <label class="ts-input-label">Professional Headline / Tagline <span class="ts-label-sub">(Max 100 chars)</span></label>
            <input type="text" name="headline" value="<?= htmlspecialchars($user['author_headline'] ?? $user['headline'] ?? '') ?>" maxlength="100" placeholder="e.g. Distributed Systems Engineer &amp; AI Practitioner" class="ts-form-input">
          </div>

          <div class="ts-form-row">
            <label class="ts-input-label">Remote Avatar URL <span class="ts-label-sub">(Optional CDN Link)</span></label>
            <input type="text" name="avatar_url" id="avatar_url_field" value="<?= htmlspecialchars($currentAvatar) ?>" placeholder="https://example.com/avatar.jpg or /storage/uploads/..." class="ts-form-input" oninput="previewAvatarUrl(this.value)">
          </div>

          <div class="ts-form-row" style="margin-bottom: 1.15rem;">
            <label class="ts-input-label">Biography <span class="ts-label-sub">(Max 500 chars)</span></label>
            <textarea name="bio" rows="3" maxlength="500" placeholder="Share your technical background, projects, or research focus..." class="ts-form-input ts-form-textarea"><?= htmlspecialchars($user['author_bio'] ?? $user['bio'] ?? '') ?></textarea>
          </div>

          <button type="submit" class="ts-btn ts-btn-primary ts-btn-submit">
            Save Profile Changes ✓
          </button>
        </form>
      </div>

      <!-- TAB 2: Password & Security -->
      <div id="tab-security" class="settings-tab-panel" style="display: none;">
        <div class="ts-tab-header">
          <h2 class="ts-tab-title">Account Password &amp; Credentials</h2>
          <p class="ts-tab-desc">
            Keep your credentials secure. Option to revoke other sessions on password update.
          </p>
        </div>

        <form action="<?= url('/user/change-password') ?>" method="POST" class="ts-compact-form">
          <div class="ts-form-row">
            <label class="ts-input-label">Current Password</label>
            <input type="password" name="current_password" required class="ts-form-input" placeholder="••••••••">
          </div>

          <div class="ts-form-row">
            <label class="ts-input-label">New Password <span class="ts-label-sub">(Min. 8 characters)</span></label>
            <input type="password" name="new_password" required minlength="8" class="ts-form-input" placeholder="••••••••">
          </div>

          <div class="ts-form-row">
            <label class="ts-input-label">Confirm New Password</label>
            <input type="password" name="confirm_password" required minlength="8" class="ts-form-input" placeholder="••••••••">
          </div>

          <div class="ts-security-revoke-banner">
            <input type="checkbox" name="terminate_other_sessions" id="terminate_other_sessions" value="1" checked style="accent-color: #38bdf8; width: 15px; height: 15px; margin-top: 2px;">
            <label for="terminate_other_sessions" style="color: #cbd5e1; font-size: 0.8rem; cursor: pointer; line-height: 1.35;">
              <strong>Revoke Remote Sessions:</strong> Log out all other devices upon password update.
            </label>
          </div>

          <button type="submit" class="ts-btn ts-btn-primary ts-btn-amber-submit">
            Update Password 🔒
          </button>
        </form>
      </div>

      <!-- TAB 3: Logged-in Devices & Active Sessions -->
      <div id="tab-devices" class="settings-tab-panel" style="display: none;">
        <div class="ts-tab-header-flex">
          <div>
            <h2 class="ts-tab-title">Active Devices &amp; Sessions</h2>
            <p class="ts-tab-desc">
              Manage browser sessions currently authenticated to your account.
            </p>
          </div>

          <?php if (count($sessions) > 1): ?>
            <form action="<?= url('/user/sessions/terminate-others') ?>" method="POST" onsubmit="return confirm('Terminate all remote sessions on other devices?');">
              <button type="submit" class="ts-btn ts-btn-outline ts-btn-xs" style="color: #ef4444; border-color: rgba(239,68,68,0.4); font-weight: 700;">
                <i data-lucide="shield-x" style="width: 13px; height: 13px;"></i> Log Out Others
              </button>
            </form>
          <?php endif; ?>
        </div>

        <div class="ts-notice-banner">
          <i data-lucide="info" style="width: 14px; height: 14px; flex-shrink: 0;"></i>
          <span>Viewing active sessions for <code>@<?= htmlspecialchars($user['username']) ?></code>. Revoke unrecognized devices immediately.</span>
        </div>

        <div class="ts-sessions-list">
          <?php if (!empty($sessions)): ?>
            <?php foreach ($sessions as $s): ?>
              <div class="ts-session-card <?= !empty($s['is_current_client']) ? 'ts-session-card-current' : '' ?>">
                <div class="ts-session-main">
                  <div class="ts-session-icon <?= !empty($s['is_current_client']) ? 'active' : '' ?>">
                    <i data-lucide="<?= $s['icon'] ?? 'monitor' ?>" style="width: 17px; height: 17px;"></i>
                  </div>
                  <div class="ts-session-info">
                    <div class="ts-session-title">
                      <span><?= htmlspecialchars($s['browser_name'] ?? 'Browser') ?> on <?= htmlspecialchars($s['os_name'] ?? 'OS') ?></span>
                      <?php if (!empty($s['is_current_client'])): ?>
                        <span class="ts-badge ts-badge-green" style="font-size: 0.62rem; padding: 0.05rem 0.35rem;">CURRENT</span>
                      <?php endif; ?>
                    </div>
                    <div class="ts-session-meta">
                      IP: <?= htmlspecialchars($s['ip_address']) ?> &bull; <?= htmlspecialchars($s['location_hint'] ?? 'Local') ?> &bull; <?= date('M j, H:i', strtotime($s['last_active_at'])) ?>
                    </div>
                  </div>
                </div>

                <div class="ts-session-actions">
                  <?php if (!empty($s['is_current_client'])): ?>
                    <span style="font-size: 0.76rem; color: #10b981; font-weight: 700;">Active Now</span>
                  <?php else: ?>
                    <form action="<?= url('/user/sessions/revoke/' . $s['id']) ?>" method="POST" onsubmit="return confirm('Revoke this remote device session?');">
                      <button type="submit" class="ts-btn ts-btn-outline ts-btn-xs" style="color: #ef4444; border-color: rgba(239,68,68,0.3); padding: 0.25rem 0.65rem; font-size: 0.72rem;">
                        Revoke ✕
                      </button>
                    </form>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p style="color: #64748b; text-align: center; padding: 1.5rem; font-size: 0.85rem;">No active device sessions found.</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- TAB 4: Community Standing & Strikes -->
      <div id="tab-strikes" class="settings-tab-panel" style="display: none;">
        <div class="ts-tab-header-flex">
          <div>
            <h2 class="ts-tab-title">Guidelines &amp; Strike Standing</h2>
            <p class="ts-tab-desc">
              TechScout 3-strike policy (1st Warning, 2nd Suspension, 3rd Ban) protects technical signal.
            </p>
          </div>
          <span class="ts-badge <?= empty($activeStrikes) ? 'ts-badge-green' : (count($activeStrikes) === 1 ? 'ts-badge-amber' : 'ts-badge-pink') ?>" style="font-size: 0.75rem; padding: 0.2rem 0.55rem;">
            <?= empty($activeStrikes) ? 'PRISTINE (0 STRIKES)' : (count($activeStrikes) . ' ACTIVE STRIKE(S)') ?>
          </span>
        </div>

        <!-- Compact Strike Escalation Meter -->
        <div class="ts-strikes-grid">
          <div class="ts-strike-card <?= count($activeStrikes) >= 1 ? 'active-amber' : '' ?>">
            <div class="ts-strike-icon">⚠️</div>
            <div class="ts-strike-title <?= count($activeStrikes) >= 1 ? 'c-amber' : '' ?>">1st Strike</div>
            <div class="ts-strike-sub">Official Warning</div>
          </div>

          <div class="ts-strike-card <?= count($activeStrikes) >= 2 ? 'active-orange' : '' ?>">
            <div class="ts-strike-icon">🛑</div>
            <div class="ts-strike-title <?= count($activeStrikes) >= 2 ? 'c-orange' : '' ?>">2nd Strike</div>
            <div class="ts-strike-sub">14-Day Suspension</div>
          </div>

          <div class="ts-strike-card <?= count($activeStrikes) >= 3 ? 'active-red' : '' ?>">
            <div class="ts-strike-icon">⛔</div>
            <div class="ts-strike-title <?= count($activeStrikes) >= 3 ? 'c-red' : '' ?>">3rd Strike</div>
            <div class="ts-strike-sub">Permanent Ban</div>
          </div>
        </div>

        <?php if (!empty($activeStrikes)): ?>
          <h3 style="font-size: 0.95rem; color: #f8fafc; margin: 1rem 0 0.5rem 0;">Active Strike Record</h3>
          <div style="display: flex; flex-direction: column; gap: 0.5rem;">
            <?php foreach ($activeStrikes as $stk): ?>
              <div style="background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.25); border-radius: 8px; padding: 0.75rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem; font-size: 0.8rem;">
                  <span style="font-weight: 700; color: #f87171;">Strike #<?= $stk['strike_level'] ?>: <?= strtoupper($stk['penalty_type']) ?></span>
                  <span style="color: #94a3b8; font-size: 0.72rem;"><?= date('M j, Y', strtotime($stk['created_at'])) ?></span>
                </div>
                <div style="font-size: 0.8rem; color: #cbd5e1;"><?= htmlspecialchars($stk['reason']) ?></div>
                <?php if (!empty($stk['expires_at'])): ?>
                  <div style="font-size: 0.72rem; color: #f59e0b; margin-top: 0.25rem;">Expires: <?= date('M j, Y H:i', strtotime($stk['expires_at'])) ?></div>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="ts-clean-record-card">
            ✓ Pristine account standing. Zero strikes on record.
          </div>
        <?php endif; ?>
      </div>

      <!-- TAB 5: Activity & Metrics -->
      <div id="tab-activity" class="settings-tab-panel" style="display: none;">
        <div class="ts-tab-header">
          <h2 class="ts-tab-title">Activity &amp; Library Telemetry</h2>
          <p class="ts-tab-desc">
            Quick metrics summarizing your bookmarks, comments, and reading trajectory.
          </p>
        </div>

        <div class="ts-stats-activity-grid">
          <div class="ts-stat-activity-card">
            <div class="ts-stat-num c-cyan"><?= $stats['bookmarks'] ?></div>
            <div class="ts-stat-label">Saved Bookmarks</div>
            <a href="<?= url('/bookmarks') ?>" class="ts-stat-link c-cyan">
              View Bookmarks &rarr;
            </a>
          </div>

          <div class="ts-stat-activity-card">
            <div class="ts-stat-num c-purple"><?= $stats['history'] ?></div>
            <div class="ts-stat-label">Articles Read</div>
            <a href="<?= url('/history') ?>" class="ts-stat-link c-purple">
              View History &rarr;
            </a>
          </div>

          <div class="ts-stat-activity-card">
            <div class="ts-stat-num c-green"><?= $stats['comments'] ?></div>
            <div class="ts-stat-label">Comments Posted</div>
            <span class="ts-stat-badge">Verified Member</span>
          </div>
        </div>
      </div>

      <!-- TAB 6: Notifications & Digest Preferences -->
      <div id="tab-preferences" class="settings-tab-panel" style="display: none;">
        <div class="ts-tab-header">
          <h2 class="ts-tab-title">Alerts &amp; Digest Preferences</h2>
          <p class="ts-tab-desc">
            Tailor what alerts, newsletter dispatches, and milestone updates you receive.
          </p>
        </div>

        <div class="ts-pref-list">
          <div class="ts-pref-item">
            <div class="ts-pref-text">
              <strong class="ts-pref-title">In-App Notification Alerts</strong>
              <span class="ts-pref-sub">Receive bell alerts for comment replies, mentions, and updates.</span>
            </div>
            <label class="ts-switch">
              <input type="checkbox" checked onchange="this.parentElement.classList.toggle('off', !this.checked)">
              <span class="ts-slider"></span>
            </label>
          </div>

          <div class="ts-pref-item">
            <div class="ts-pref-text">
              <strong class="ts-pref-title">Weekly Editorial Digest</strong>
              <span class="ts-pref-sub">Curated top AI breakthroughs and teardowns delivered Mondays.</span>
            </div>
            <label class="ts-switch">
              <input type="checkbox" checked onchange="this.parentElement.classList.toggle('off', !this.checked)">
              <span class="ts-slider"></span>
            </label>
          </div>

          <div class="ts-pref-item">
            <div class="ts-pref-text">
              <strong class="ts-pref-title">Creator Rewards &amp; Payout Alerts</strong>
              <span class="ts-pref-sub">Notices when royalties, tip jars, or bounties are credited.</span>
            </div>
            <label class="ts-switch">
              <input type="checkbox" checked onchange="this.parentElement.classList.toggle('off', !this.checked)">
              <span class="ts-slider"></span>
            </label>
          </div>

          <!-- [TS-TZ-04] Localized Date & Time Format Preferences -->
          <div class="ts-pref-item" style="flex-direction: column; align-items: flex-start; gap: 0.75rem;">
            <div class="ts-pref-text">
              <strong class="ts-pref-title">🌐 Date &amp; Time Display Preferences [TS-TZ-04]</strong>
              <span class="ts-pref-sub">Customize how timestamps, publish dates, and logs are rendered across your sessions.</span>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; width: 100%;">
              <div>
                <label class="ts-input-label">Clock Format</label>
                <select id="pref_time_format" class="ts-form-input" onchange="saveTimeFormatPref('clock', this.value)">
                  <option value="12">12-Hour (e.g. 02:30 PM)</option>
                  <option value="24">24-Hour (e.g. 14:30)</option>
                </select>
              </div>
              <div>
                <label class="ts-input-label">Date Syntax Convention</label>
                <select id="pref_date_syntax" class="ts-form-input" onchange="saveTimeFormatPref('syntax', this.value)">
                  <option value="middle">Middle-Endian (MM/DD/YYYY - US Standard)</option>
                  <option value="little">Little-Endian (DD/MM/YYYY - International)</option>
                  <option value="iso">ISO-8601 (YYYY-MM-DD - Engineering)</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</main>

<style>
/* ==========================================================================
   TECHSCOUT BRANDED & COMPACT SETTINGS STYLING
   ========================================================================== */

.ts-settings-page-main {
  padding-top: 1.25rem;
  padding-bottom: 3rem;
  min-height: 80vh;
}

/* 1. Account Header Card: Sleek & Compact */
.ts-account-header-card {
  background: linear-gradient(135deg, rgba(15, 23, 42, 0.9), rgba(11, 15, 25, 0.95));
  border: 1px solid rgba(56, 189, 248, 0.18);
  border-radius: 12px;
  padding: 1rem 1.25rem;
  margin-bottom: 1rem;
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  box-shadow: 0 4px 20px rgba(0,0,0,0.35);
}
[data-theme="light"] .ts-account-header-card,
body.light-mode .ts-account-header-card {
  background: #ffffff !important;
  border-color: #e2e8f0 !important;
  box-shadow: 0 2px 12px rgba(0,0,0,0.05) !important;
}

.ts-account-header-flex {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.ts-account-user-meta {
  display: flex;
  align-items: center;
  gap: 0.85rem;
  min-width: 0;
}

.ts-avatar-wrap {
  position: relative;
  flex-shrink: 0;
}

.ts-header-avatar {
  width: 52px;
  height: 52px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #38bdf8;
  box-shadow: 0 0 12px rgba(56,189,248,0.25);
  display: block;
}

.ts-avatar-fallback {
  background: linear-gradient(135deg, #0284c7, #6366f1);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  font-weight: 800;
  color: #ffffff;
  border: 2px solid rgba(255,255,255,0.2);
}

.ts-avatar-status-badge {
  position: absolute;
  bottom: 1px;
  right: 1px;
  width: 11px;
  height: 11px;
  background: #10b981;
  border-radius: 50%;
  border: 2px solid var(--ts-bg-surface, #0f172a);
}

.ts-user-details {
  min-width: 0;
}

.ts-user-title-row {
  display: flex;
  align-items: center;
  gap: 0.45rem;
  flex-wrap: wrap;
}

.ts-account-name {
  font-size: 1.2rem;
  color: var(--ts-text-primary, #f8fafc);
  margin: 0;
  font-family: 'Outfit', sans-serif;
  font-weight: 800;
  line-height: 1.2;
}

.ts-account-subline {
  font-size: 0.78rem;
  color: var(--ts-text-muted, #94a3b8);
  margin-top: 0.15rem;
  word-break: break-all;
}

.ts-account-header-actions {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.ts-btn-write-gradient {
  background: linear-gradient(135deg, #ec4899, #8b5cf6) !important;
  border: none !important;
  color: #ffffff !important;
  font-weight: 700 !important;
}

/* 2. Responsive Settings Layout */
.ts-settings-layout {
  display: grid;
  grid-template-columns: 230px 1fr;
  gap: 1.25rem;
  align-items: start;
}

/* Nav Card: Sidebar on Desktop */
.ts-settings-nav-card {
  background: rgba(11, 15, 25, 0.85);
  border: 1px solid rgba(56, 189, 248, 0.15);
  border-radius: 12px;
  padding: 0.65rem;
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
}
[data-theme="light"] .ts-settings-nav-card,
body.light-mode .ts-settings-nav-card {
  background: #ffffff !important;
  border-color: #e2e8f0 !important;
}

.ts-settings-nav-strip {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.settings-nav-btn {
  display: flex;
  align-items: center;
  gap: 0.55rem;
  width: 100%;
  min-height: 38px;
  padding: 0.5rem 0.75rem;
  border-radius: 8px;
  background: transparent;
  border: 1px solid transparent;
  color: var(--ts-text-muted, #94a3b8);
  font-size: 0.82rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
  font-family: inherit;
  text-align: left;
  box-sizing: border-box;
}
.settings-nav-btn:hover {
  background: rgba(56, 189, 248, 0.08);
  color: #38bdf8 !important;
}
.settings-nav-btn.active {
  background: rgba(56, 189, 248, 0.14);
  color: #38bdf8 !important;
  border-color: rgba(56, 189, 248, 0.35);
  font-weight: 700;
}
[data-theme="light"] .settings-nav-btn.active {
  background: #0284c7;
  color: #ffffff !important;
}
.settings-nav-btn span:first-of-type {
  flex: 1;
}

.ts-badge-count {
  font-size: 0.65rem;
  padding: 0.1rem 0.4rem;
  background: rgba(56,189,248,0.2);
  color: #38bdf8;
  border-radius: 999px;
}

.ts-settings-nav-footer {
  border-top: 1px solid rgba(255,255,255,0.06);
  margin-top: 0.65rem;
  padding-top: 0.65rem;
}

/* 3. Settings Content Card */
.ts-settings-content-card {
  background: rgba(11, 15, 25, 0.85);
  border: 1px solid rgba(56, 189, 248, 0.15);
  border-radius: 12px;
  padding: 1.5rem;
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  min-width: 0;
  box-shadow: 0 4px 25px rgba(0,0,0,0.3);
}
[data-theme="light"] .ts-settings-content-card,
body.light-mode .ts-settings-content-card {
  background: #ffffff !important;
  border-color: #e2e8f0 !important;
  box-shadow: 0 2px 10px rgba(0,0,0,0.04) !important;
}

.ts-tab-header {
  margin-bottom: 1.15rem;
}
.ts-tab-header-flex {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  flex-wrap: wrap;
  gap: 0.75rem;
  margin-bottom: 1.15rem;
}

.ts-tab-title {
  font-size: 1.15rem;
  color: var(--ts-text-primary, #f8fafc);
  margin-top: 0;
  margin-bottom: 0.25rem;
  font-family: 'Outfit', sans-serif;
  font-weight: 700;
  line-height: 1.25;
}

.ts-tab-desc {
  color: var(--ts-text-muted, #94a3b8);
  font-size: 0.8rem;
  margin: 0;
  line-height: 1.4;
}

/* Compact Avatar Upload Component */
.ts-avatar-upload-box {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.15rem;
  padding: 0.85rem 1rem;
  background: rgba(255,255,255,0.02);
  border: 1px dashed rgba(56, 189, 248, 0.25);
  border-radius: 10px;
}
[data-theme="light"] .ts-avatar-upload-box {
  background: #f8fafc;
  border-color: #cbd5e1;
}

.ts-avatar-preview-wrap {
  position: relative;
  flex-shrink: 0;
}

.ts-avatar-preview-img {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #38bdf8;
  box-shadow: 0 0 10px rgba(56,189,248,0.25);
  display: block;
}

.ts-avatar-controls {
  flex: 1;
  min-width: 180px;
}

.ts-help-text {
  font-size: 0.72rem;
  color: var(--ts-text-muted, #94a3b8);
  margin-top: 0.2rem;
}

/* Form Controls: Compact & Touch-Friendly */
.ts-form-grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.85rem;
  margin-bottom: 0.85rem;
}

.ts-form-row {
  margin-bottom: 0.85rem;
}

.ts-input-label {
  display: block;
  font-size: 0.78rem;
  color: var(--ts-text-secondary, #cbd5e1);
  font-weight: 600;
  margin-bottom: 0.25rem;
}
[data-theme="light"] .ts-input-label {
  color: #334155;
}

.ts-label-sub {
  font-size: 0.72rem;
  color: #94a3b8;
  font-weight: normal;
}

.ts-form-input {
  width: 100%;
  min-height: 38px;
  background: #090d16;
  border: 1px solid rgba(148, 163, 184, 0.2);
  border-radius: 8px;
  padding: 0.45rem 0.75rem;
  color: var(--ts-text-primary, #f8fafc);
  font-size: 16px; /* Prevents auto-zoom on mobile safari */
  font-family: inherit;
  outline: none;
  box-sizing: border-box;
  transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
[data-theme="light"] .ts-form-input {
  background: #f8fafc;
  border-color: #cbd5e1;
  color: #0f172a;
}
.ts-form-input:focus {
  border-color: #38bdf8;
  box-shadow: 0 0 0 2px rgba(56,189,248,0.2);
}

.ts-file-input {
  padding: 0.35rem 0.5rem;
  font-size: 0.78rem;
}

.ts-form-textarea {
  resize: vertical;
  min-height: 70px;
  line-height: 1.4;
}

.ts-btn-submit {
  background: linear-gradient(135deg, #0284c7, #38bdf8);
  color: #020617;
  font-weight: 700;
  width: 100%;
  max-width: 220px;
  min-height: 40px;
  border-radius: 8px;
  font-size: 0.85rem;
}

.ts-btn-amber-submit {
  background: linear-gradient(135deg, #f59e0b, #d97706);
  border-color: #f59e0b;
  color: #000000;
  font-weight: 700;
  width: 100%;
  max-width: 220px;
  min-height: 40px;
  border-radius: 8px;
  font-size: 0.85rem;
}

.ts-compact-form {
  max-width: 480px;
}

.ts-security-revoke-banner {
  margin-bottom: 1.15rem;
  display: flex;
  align-items: flex-start;
  gap: 0.55rem;
  background: rgba(56,189,248,0.06);
  padding: 0.65rem 0.85rem;
  border-radius: 8px;
  border: 1px solid rgba(56,189,248,0.2);
}

/* 4. Active Devices Tab */
.ts-notice-banner {
  background: rgba(56, 189, 248, 0.08);
  border: 1px solid rgba(56, 189, 248, 0.2);
  border-radius: 8px;
  padding: 0.5rem 0.75rem;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #38bdf8;
  font-size: 0.76rem;
}

.ts-sessions-list {
  display: flex;
  flex-direction: column;
  gap: 0.65rem;
}

.ts-session-card {
  background: rgba(15, 23, 42, 0.6);
  border: 1px solid rgba(148, 163, 184, 0.15);
  border-radius: 8px;
  padding: 0.65rem 0.85rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.65rem;
}
.ts-session-card-current {
  border-color: #38bdf8;
  background: rgba(56,189,248,0.05);
}
[data-theme="light"] .ts-session-card {
  background: #ffffff;
  border-color: #e2e8f0;
}
[data-theme="light"] .ts-session-card-current {
  background: #f0f9ff;
  border-color: #7dd3fc;
}

.ts-session-main {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  min-width: 0;
}

.ts-session-icon {
  width: 36px;
  height: 36px;
  border-radius: 6px;
  background: #090d16;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #94a3b8;
  border: 1px solid rgba(255,255,255,0.08);
  flex-shrink: 0;
}
.ts-session-icon.active {
  color: #38bdf8;
  border-color: rgba(56,189,248,0.3);
}
[data-theme="light"] .ts-session-icon {
  background: #f1f5f9;
  border-color: #e2e8f0;
  color: #64748b;
}
[data-theme="light"] .ts-session-icon.active {
  color: #0284c7;
  border-color: #bae6fd;
}

.ts-session-info {
  min-width: 0;
}

.ts-session-title {
  font-weight: 700;
  color: #f8fafc;
  font-size: 0.85rem;
  display: flex;
  align-items: center;
  gap: 0.4rem;
  flex-wrap: wrap;
}

.ts-session-meta {
  font-size: 0.72rem;
  color: #64748b;
  margin-top: 0.1rem;
  word-break: break-all;
}

/* 5. Community Standing */
.ts-strikes-grid {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 0.65rem;
  margin-bottom: 0.85rem;
}
.ts-strike-card {
  background: rgba(15,23,42,0.6);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 8px;
  padding: 0.65rem 0.4rem;
  text-align: center;
}
.ts-strike-card.active-amber { border-color: #f59e0b; background: rgba(245,158,11,0.08); }
.ts-strike-card.active-orange { border-color: #ea580c; background: rgba(234,88,12,0.08); }
.ts-strike-card.active-red { border-color: #ef4444; background: rgba(239,68,68,0.08); }

[data-theme="light"] .ts-strike-card { background: #ffffff; border-color: #e2e8f0; }
[data-theme="light"] .ts-strike-card.active-amber { background: #fffbeb; }
[data-theme="light"] .ts-strike-card.active-orange { background: #fff7ed; }
[data-theme="light"] .ts-strike-card.active-red { background: #fef2f2; }

.ts-strike-icon { font-size: 1.2rem; line-height: 1; margin-bottom: 0.2rem; }
.ts-strike-title { font-weight: 700; font-size: 0.78rem; color: #94a3b8; }
.ts-strike-sub { font-size: 0.68rem; color: #64748b; margin-top: 0.15rem; }
[data-theme="light"] .ts-strike-title { color: #334155; }
[data-theme="light"] .ts-strike-sub { color: #64748b; }
.c-amber { color: #f59e0b !important; }
.c-orange { color: #ea580c !important; }
.c-red { color: #ef4444 !important; }

.ts-clean-record-card {
  text-align: center;
  padding: 1.25rem 1rem;
  color: #10b981;
  font-size: 0.85rem;
  font-weight: 600;
  background: rgba(16,185,129,0.06);
  border: 1px solid rgba(16,185,129,0.2);
  border-radius: 8px;
}
[data-theme="light"] .ts-clean-record-card {
  background: #ecfdf5;
  border-color: #a7f3d0;
  color: #059669;
}

/* 6. Activity Metrics */
.ts-stats-activity-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.65rem;
  margin-bottom: 1.5rem;
}
.ts-stat-activity-card {
  background: rgba(15,23,42,0.6);
  border: 1px solid rgba(148, 163, 184, 0.15);
  border-radius: 8px;
  padding: 0.85rem 0.65rem;
  text-align: center;
}
[data-theme="light"] .ts-stat-activity-card {
  background: #ffffff;
  border-color: #e2e8f0;
}
.ts-stat-num { font-size: 1.4rem; font-weight: 800; font-family: monospace; line-height: 1; }
.c-cyan { color: #38bdf8; }
.c-purple { color: #a855f7; }
.c-green { color: #10b981; }

.ts-stat-label { color: #cbd5e1; font-size: 0.75rem; font-weight: 600; margin-top: 0.35rem; }
[data-theme="light"] .ts-stat-label { color: #475569; }
.ts-stat-link { font-size: 0.7rem; text-decoration: none; margin-top: 0.45rem; display: inline-block; font-weight: 600; }
.ts-stat-badge { color: #64748b; font-size: 0.68rem; display: inline-block; margin-top: 0.45rem; }

/* 7. Preferences / Toggle Switches */
.ts-pref-list {
  display: flex;
  flex-direction: column;
  gap: 0.65rem;
  max-width: 580px;
}
.ts-pref-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0.85rem;
  background: rgba(15,23,42,0.6);
  border: 1px solid rgba(148, 163, 184, 0.15);
  border-radius: 8px;
  gap: 0.75rem;
}
.ts-pref-text { min-width: 0; }
.ts-pref-title { color: #f8fafc; font-size: 0.82rem; display: block; }
.ts-pref-sub { color: #94a3b8; font-size: 0.72rem; display: block; margin-top: 0.1rem; }

/* iOS style toggle */
.ts-switch {
  position: relative;
  display: inline-block;
  width: 38px;
  height: 22px;
  flex-shrink: 0;
}
.ts-switch input { opacity: 0; width: 0; height: 0; }
.ts-slider {
  position: absolute;
  cursor: pointer;
  inset: 0;
  background: #38bdf8;
  border-radius: 22px;
  transition: .25s;
}
.ts-slider:before {
  position: absolute;
  content: "";
  height: 16px;
  width: 16px;
  left: 3px;
  bottom: 3px;
  background: #ffffff;
  border-radius: 50%;
  transition: .25s;
}
.ts-switch.off .ts-slider { background: #334155; }
.ts-switch.off .ts-slider:before { transform: translateX(0); }
.ts-switch:not(.off) .ts-slider:before { transform: translateX(16px); }

/* ==========================================================================
   MOBILE RESPONSIVE BREAKPOINT (< 768px): COMPACT & VISUALLY CONTAINED
   ========================================================================== */
@media (max-width: 768px) {
  .ts-settings-page-main {
    padding-top: 0.5rem;
    padding-bottom: 2rem;
    padding-left: 0.5rem !important;
    padding-right: 0.5rem !important;
    max-width: 100% !important;
    overflow-x: hidden !important;
    box-sizing: border-box !important;
  }

  /* Compact Header Card */
  .ts-account-header-card {
    padding: 0.65rem 0.75rem;
    margin-bottom: 0.65rem;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
    overflow: hidden !important;
  }
  .ts-account-user-meta {
    width: 100% !important;
    min-width: 0 !important;
  }
  .ts-user-details {
    min-width: 0 !important;
    flex: 1 1 auto !important;
  }
  .ts-header-avatar {
    width: 40px;
    height: 40px;
  }
  .ts-avatar-fallback {
    font-size: 0.95rem;
  }
  .ts-account-name {
    font-size: 1rem;
    word-break: break-word !important;
  }
  .ts-account-subline {
    font-size: 0.7rem;
    word-break: break-all !important;
  }
  .ts-account-header-actions {
    width: 100% !important;
    justify-content: flex-start !important;
  }
  .ts-account-header-actions .ts-btn {
    padding: 0.2rem 0.5rem;
    font-size: 0.7rem;
    min-height: 28px;
    flex: 1 1 auto;
    justify-content: center;
  }

  /* Single Column Layout */
  .ts-settings-layout {
    grid-template-columns: 100% !important;
    width: 100% !important;
    max-width: 100% !important;
    gap: 0.65rem;
    box-sizing: border-box !important;
    overflow-x: hidden !important;
  }

  /* Sticky Horizontal Pill Strip */
  .ts-settings-nav-card {
    padding: 0.25rem 0.35rem;
    position: sticky;
    top: 56px;
    z-index: 30;
    box-shadow: 0 4px 15px rgba(0,0,0,0.5);
    background: rgba(8, 12, 22, 0.94);
    border-radius: 999px;
    width: 100% !important;
    max-width: 100% !important;
    overflow: hidden !important;
    box-sizing: border-box !important;
  }

  .ts-settings-nav-strip {
    flex-direction: row;
    overflow-x: auto;
    gap: 0.3rem;
    -webkit-overflow-scrolling: touch;
    padding: 0.15rem;
    scrollbar-width: none;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
  }
  .ts-settings-nav-strip::-webkit-scrollbar {
    display: none;
  }

  .settings-nav-btn {
    flex-shrink: 0;
    width: auto;
    min-height: 32px;
    padding: 0.35rem 0.65rem;
    font-size: 0.76rem;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    color: #cbd5e1;
    white-space: nowrap;
    gap: 0.35rem;
  }
  .settings-nav-btn.active {
    background: linear-gradient(135deg, #0284c7, #38bdf8);
    color: #020617 !important;
    border-color: transparent;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(56, 189, 248, 0.3);
  }
  .settings-nav-btn.active .ts-badge {
    background: rgba(0,0,0,0.25);
    color: #020617;
  }

  .ts-settings-nav-footer {
    display: none;
  }

  /* Compact Content Card */
  .ts-settings-content-card {
    margin-top: 3rem !important;
    padding: 0.75rem;
    border-radius: 10px;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
    overflow-x: hidden !important;
  }

  .ts-tab-header, .ts-tab-header-flex {
    margin-bottom: 0.65rem;
    width: 100% !important;
  }
  .ts-tab-title {
    font-size: 1rem;
  }
  .ts-tab-desc {
    font-size: 0.72rem;
  }

  .ts-form-grid-2 {
    grid-template-columns: 100% !important;
    gap: 0.6rem;
    margin-bottom: 0.6rem;
    width: 100% !important;
    box-sizing: border-box !important;
  }

  .ts-form-row {
    margin-bottom: 0.6rem;
    width: 100% !important;
    box-sizing: border-box !important;
  }

  .ts-avatar-upload-box {
    padding: 0.65rem;
    gap: 0.65rem;
    margin-bottom: 0.65rem;
    width: 100% !important;
    box-sizing: border-box !important;
    overflow: hidden !important;
  }
  .ts-avatar-controls {
    min-width: 0 !important;
    flex: 1 1 auto !important;
    width: 100% !important;
    box-sizing: border-box !important;
  }
  .ts-avatar-preview-img {
    width: 44px;
    height: 44px;
  }

  .ts-btn-submit, .ts-btn-amber-submit {
    max-width: 100% !important;
    width: 100% !important;
    min-height: 42px;
  }

  .ts-session-card {
    flex-direction: column;
    align-items: flex-start;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
  }
  .ts-session-main {
    width: 100% !important;
    min-width: 0 !important;
  }
  .ts-session-info {
    width: 100% !important;
    min-width: 0 !important;
  }
  .ts-session-title {
    word-break: break-word !important;
  }
  .ts-session-actions {
    width: 100% !important;
    text-align: right;
  }
  .ts-session-actions .ts-btn {
    width: 100% !important;
    min-height: 36px;
    justify-content: center;
  }

  .ts-strikes-grid {
    grid-template-columns: 1fr 1fr 1fr;
    gap: 0.35rem !important;
    width: 100% !important;
    box-sizing: border-box !important;
  }
  .ts-strike-card {
    padding: 0.45rem 0.2rem !important;
    min-width: 0 !important;
  }
  .ts-strike-title {
    font-size: 0.72rem !important;
  }
  .ts-strike-sub {
    font-size: 0.6rem !important;
    line-height: 1.15 !important;
  }

  .ts-stats-activity-grid {
    grid-template-columns: 1fr 1fr 1fr;
    gap: 0.35rem !important;
    width: 100% !important;
    box-sizing: border-box !important;
  }
  .ts-stat-activity-card {
    padding: 0.5rem 0.25rem !important;
    min-width: 0 !important;
  }
  .ts-stat-num {
    font-size: 1.1rem;
  }
  .ts-stat-label {
    font-size: 0.66rem;
  }
  .ts-stat-link {
    font-size: 0.62rem;
  }

  .ts-pref-item {
    width: 100% !important;
    box-sizing: border-box !important;
  }
}
</style>

<script>
function previewAvatarImage(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      const p = document.getElementById('avatar_live_preview');
      if (p) p.src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
  }
}

function previewAvatarUrl(url) {
  if (url && (url.startsWith('http://') || url.startsWith('https://') || url.startsWith('/'))) {
    const p = document.getElementById('avatar_live_preview');
    if (p) p.src = url;
  }
}

function switchSettingsTab(tabName, btn) {
  document.querySelectorAll('.settings-tab-panel').forEach(p => p.style.display = 'none');
  document.querySelectorAll('.settings-nav-btn').forEach(b => b.classList.remove('active'));
  
  const target = document.getElementById('tab-' + tabName);
  if (target) target.style.display = 'block';
  
  if (btn) {
    btn.classList.add('active');
    btn.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
  } else {
    const defaultBtn = document.getElementById('nav-btn-' + tabName);
    if (defaultBtn) {
      defaultBtn.classList.add('active');
      defaultBtn.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    }
  }

  history.replaceState(null, '', window.location.pathname + '?tab=' + tabName);
  if (typeof lucide !== 'undefined') lucide.createIcons();
}

document.addEventListener('DOMContentLoaded', () => {
  const urlParams = new URLSearchParams(window.location.search);
  const hash = window.location.hash ? window.location.hash.replace('#', '') : null;
  const tab = urlParams.get('tab') || hash;
  
  if (tab && document.getElementById('tab-' + tab)) {
    const btn = document.getElementById('nav-btn-' + tab);
    switchSettingsTab(tab, btn);
  }

  // Load saved date/time preferences [TS-TZ-04]
  const clockPref = localStorage.getItem('ts_clock_format') || '12';
  const syntaxPref = localStorage.getItem('ts_date_syntax') || 'middle';
  const clockEl = document.getElementById('pref_time_format');
  const syntaxEl = document.getElementById('pref_date_syntax');
  if (clockEl) clockEl.value = clockPref;
  if (syntaxEl) syntaxEl.value = syntaxPref;
});

function saveTimeFormatPref(type, val) {
  if (type === 'clock') {
    localStorage.setItem('ts_clock_format', val);
  } else if (type === 'syntax') {
    localStorage.setItem('ts_date_syntax', val);
    document.cookie = "ts_date_syntax=" + val + ";path=/;max-age=31536000;SameSite=Lax";
  }
  if (typeof window.localizeAllTimestamps === 'function') {
    window.localizeAllTimestamps();
  }
  if (typeof showToast === 'function') {
    showToast('Date & time preference updated! 🌐', 'success');
  }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
