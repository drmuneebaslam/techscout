<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-main">
  <div style="margin-bottom: 2rem;">
    <h1 style="font-size: 2rem; color: var(--admin-text-primary); margin: 0 0 0.25rem 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
      System Diagnostics &amp; Disaster Recovery HUD
    </h1>
    <p style="color: var(--admin-text-secondary); font-size: 0.95rem; margin: 0;">Configure platform features, customize footer social icons &amp; channels, manage ad placements, and review audit telemetry.</p>
  </div>

  <!-- Site Settings & Social Media Customization -->
  <div class="admin-table-wrap" style="padding: 2rem; margin-bottom: 2.5rem; border-color: rgba(99,102,241,0.3);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1rem;">
      <div>
        <h3 style="font-size: 1.3rem; color: var(--admin-text-primary); margin: 0; font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
          <i data-lucide="share-2" style="color: var(--ts-primary);"></i> Footer Social Channels, Custom Icons &amp; Feature Controls
        </h3>
        <p style="color: var(--admin-text-secondary); font-size: 0.88rem; margin: 0.25rem 0 0 0;">
          Enable or disable individual social networks (YouTube, Facebook, Twitter/X, GitHub, Discord, LinkedIn, Telegram, etc.), customize Lucide icon names, brand colors, and add unlimited custom footer links.
        </p>
      </div>
    </div>

    <form action="<?= url('/admin/system/settings') ?>" method="POST">
      <?php
        $networks = [
          'youtube' => ['name' => 'YouTube', 'domain' => 'youtube.com', 'icon' => 'youtube', 'default_url' => 'https://youtube.com/@techscout', 'default_color' => '#ef4444'],
          'facebook' => ['name' => 'Facebook', 'domain' => 'facebook.com', 'icon' => 'facebook', 'default_url' => 'https://facebook.com/techscout', 'default_color' => '#1877f2'],
          'twitter' => ['name' => 'Twitter / X', 'domain' => 'x.com', 'icon' => 'twitter', 'default_url' => 'https://twitter.com/techscout', 'default_color' => '#38bdf8'],
          'github' => ['name' => 'GitHub', 'domain' => 'github.com', 'icon' => 'github', 'default_url' => 'https://github.com/techscout', 'default_color' => '#f8fafc'],
          'linkedin' => ['name' => 'LinkedIn', 'domain' => 'linkedin.com', 'icon' => 'linkedin', 'default_url' => 'https://linkedin.com/company/techscout', 'default_color' => '#0a66c2'],
          'discord' => ['name' => 'Discord', 'domain' => 'discord.com', 'icon' => 'message-square', 'default_url' => 'https://discord.gg/techscout', 'default_color' => '#5865f2'],
          'telegram' => ['name' => 'Telegram', 'domain' => 'telegram.org', 'icon' => 'send', 'default_url' => 'https://t.me/techscout', 'default_color' => '#229ed9'],
          'reddit' => ['name' => 'Reddit', 'domain' => 'reddit.com', 'icon' => 'message-circle', 'default_url' => 'https://reddit.com/r/techscout', 'default_color' => '#ff4500'],
          'instagram' => ['name' => 'Instagram', 'domain' => 'instagram.com', 'icon' => 'instagram', 'default_url' => 'https://instagram.com/techscout', 'default_color' => '#e1306c'],
          'tiktok' => ['name' => 'TikTok', 'domain' => 'tiktok.com', 'icon' => 'video', 'default_url' => 'https://tiktok.com/@techscout', 'default_color' => '#ff0050'],
          'twitch' => ['name' => 'Twitch', 'domain' => 'twitch.tv', 'icon' => 'tv', 'default_url' => 'https://twitch.tv/techscout', 'default_color' => '#9146ff'],
          'rss' => ['name' => 'RSS Feed', 'domain' => 'rss.com', 'icon' => 'rss', 'default_url' => url('/feed.xml'), 'default_color' => '#f59e0b']
        ];
      ?>

      <!-- Grid of Social Networks Controls -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        <?php foreach ($networks as $netKey => $netMeta): ?>
          <?php
            $urlVal = $settings['social_' . $netKey] ?? $netMeta['default_url'];
            $enabledVal = isset($settings['social_enabled_' . $netKey]) ? ($settings['social_enabled_' . $netKey] === '1') : (!empty($urlVal));
            $iconVal = $settings['social_icon_' . $netKey] ?? $netMeta['icon'];
            $colorVal = $settings['social_color_' . $netKey] ?? $netMeta['default_color'];
            $faviconUrl = \App\Core\SocialHelper::getFaviconUrl($urlVal ?: $netMeta['domain']);
          ?>
          <div class="social-net-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
              <label style="display: flex; align-items: center; gap: 0.65rem; color: var(--admin-text-primary); font-weight: 700; font-size: 0.95rem; cursor: pointer; margin: 0;">
                <input type="checkbox" name="social_enabled_<?= $netKey ?>" value="1" <?= $enabledVal ? 'checked' : '' ?> style="width: 17px; height: 17px; accent-color: var(--ts-primary);">
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; background: var(--admin-bg-elevated); border-radius: 6px; border: 1px solid var(--admin-border-subtle);">
                  <img src="<?= htmlspecialchars($faviconUrl) ?>" alt="<?= htmlspecialchars($netMeta['name']) ?>" style="width: 16px; height: 16px; object-fit: contain;" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                  <i data-lucide="<?= htmlspecialchars($iconVal) ?>" style="width: 14px; height: 14px; display: none; color: <?= htmlspecialchars($colorVal) ?>;"></i>
                </span>
                <span><?= htmlspecialchars($netMeta['name']) ?></span>
              </label>

              <span class="ts-badge <?= $enabledVal ? 'ts-badge-green' : 'ts-badge-gray' ?>" style="font-size: 0.65rem;">
                <?= $enabledVal ? 'ENABLED' : 'DISABLED' ?>
              </span>
            </div>

            <div style="margin-bottom: 0.65rem;">
              <label class="system-subtext">Target Profile URL</label>
              <input type="text" name="social_<?= $netKey ?>" value="<?= htmlspecialchars($urlVal) ?>" placeholder="https://..." class="admin-modal-input" style="font-size: 0.85rem; padding: 0.5rem 0.75rem;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
              <div>
                <label class="system-subtext">Icon Name (Lucide)</label>
                <input type="text" name="social_icon_<?= $netKey ?>" value="<?= htmlspecialchars($iconVal) ?>" placeholder="<?= htmlspecialchars($netMeta['icon']) ?>" class="admin-modal-input" style="font-size: 0.8rem; padding: 0.45rem 0.65rem; font-family: var(--admin-font-mono);">
              </div>
              <div>
                <label class="system-subtext">Brand Color Hex</label>
                <input type="text" name="social_color_<?= $netKey ?>" value="<?= htmlspecialchars($colorVal) ?>" placeholder="<?= htmlspecialchars($netMeta['default_color']) ?>" class="admin-modal-input" style="font-size: 0.8rem; padding: 0.45rem 0.65rem; font-family: var(--admin-font-mono);">
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Platform Timezone & Feature Controls -->
      <div class="system-card-box">
        <h4 style="color: var(--admin-text-primary); margin: 0 0 1rem 0; font-size: 1rem;">Platform Standard Timezone &amp; Feature Flags</h4>

        <div style="margin-bottom: 1.25rem;">
          <label class="system-label">
            <i data-lucide="clock" style="width: 14px; display: inline; color: #10b981;"></i> Platform Standard Timezone
          </label>
          <select name="default_timezone" class="admin-modal-select" style="padding: 0.7rem 1rem; font-size: 0.9rem;">
            <option value="Asia/Karachi" <?= ($settings['default_timezone'] ?? 'Asia/Karachi') === 'Asia/Karachi' ? 'selected' : '' ?>>Asia/Karachi (Pakistan Standard Time UTC+5) - Default</option>
            <option value="UTC" <?= ($settings['default_timezone'] ?? '') === 'UTC' ? 'selected' : '' ?>>UTC (Coordinated Universal Time)</option>
            <option value="America/New_York" <?= ($settings['default_timezone'] ?? '') === 'America/New_York' ? 'selected' : '' ?>>America/New_York (Eastern Time)</option>
            <option value="Europe/London" <?= ($settings['default_timezone'] ?? '') === 'Europe/London' ? 'selected' : '' ?>>Europe/London (GMT/BST)</option>
            <option value="Asia/Dubai" <?= ($settings['default_timezone'] ?? '') === 'Asia/Dubai' ? 'selected' : '' ?>>Asia/Dubai (Gulf Standard Time UTC+4)</option>
            <option value="Asia/Kolkata" <?= ($settings['default_timezone'] ?? '') === 'Asia/Kolkata' ? 'selected' : '' ?>>Asia/Kolkata (IST UTC+5:30)</option>
          </select>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.85rem;">
          <label style="display: flex; align-items: center; gap: 0.75rem; color: var(--admin-text-primary); font-size: 0.9rem; cursor: pointer;">
            <input type="checkbox" name="enable_bg_grid_lines" value="1" <?= (!empty($settings['enable_bg_grid_lines']) && $settings['enable_bg_grid_lines'] === '1') ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--ts-primary);">
            <span><strong>Aesthetic Blueprint Background Grid Lines</strong> (Renders subtle, high-tech architectural blueprint background grid across public and admin pages)</span>
          </label>

          <label style="display: flex; align-items: center; gap: 0.75rem; color: var(--admin-text-primary); font-size: 0.9rem; cursor: pointer;">
            <input type="checkbox" name="enable_jobs_section" value="1" <?= (!empty($settings['enable_jobs_section'])) ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--ts-primary);">
            <span><strong>Enable Tech Jobs Board Module on Homepage</strong> (Shows active engineering roles)</span>
          </label>

          <label style="display: flex; align-items: center; gap: 0.75rem; color: var(--admin-text-primary); font-size: 0.9rem; cursor: pointer;">
            <input type="checkbox" name="allow_all_authors_templates" value="1" <?= (!empty($settings['allow_all_authors_templates'])) ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--ts-primary);">
            <span><strong>Allow All Authors to Save &amp; Use Custom Templates</strong></span>
          </label>
        </div>
      </div>

      <!-- Monetag & Google AdSense Ad Slot Embeds -->
      <div class="system-card-box">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
          <span class="ts-badge ts-badge-pink">Monetization Engine</span>
          <h4 style="color: var(--admin-text-primary); margin: 0; font-size: 1.05rem;">Monetag &amp; Google AdSense Ad Slots</h4>
        </div>
        <p style="color: var(--admin-text-secondary); font-size: 0.85rem; margin: 0 0 1.25rem 0;">Paste your Monetag scripts, Google AdSense tags, or custom banner HTML.</p>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
          <div>
            <label class="system-label">Header Script / Site Verification Code (&lt;head&gt; code)</label>
            <textarea name="ad_header_code" rows="2" placeholder="&lt;script src=&quot;https://pagead2.googlesyndication.com/...&quot;&gt;&lt;/script&gt;" class="admin-modal-textarea" style="font-family: var(--admin-font-mono); font-size: 0.8rem;"><?= htmlspecialchars($settings['ad_header_code'] ?? '') ?></textarea>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div>
              <label class="system-label">Top Banner Ad Slot (Beneath Header)</label>
              <textarea name="ad_slot_top" rows="2" placeholder="&lt;ins class=&quot;adsbygoogle&quot;...&gt;&lt;/ins&gt;" class="admin-modal-textarea" style="font-family: var(--admin-font-mono); font-size: 0.8rem;"><?= htmlspecialchars($settings['ad_slot_top'] ?? '') ?></textarea>
            </div>
            <div>
              <label class="system-label">In-Article Midpoint Ad Slot</label>
              <textarea name="ad_slot_in_article" rows="2" placeholder="&lt;ins class=&quot;adsbygoogle&quot;...&gt;&lt;/ins&gt;" class="admin-modal-textarea" style="font-family: var(--admin-font-mono); font-size: 0.8rem;"><?= htmlspecialchars($settings['ad_slot_in_article'] ?? '') ?></textarea>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div>
              <label class="system-label">Right Sidebar Widget Ad Slot</label>
              <textarea name="ad_slot_sidebar" rows="2" placeholder="&lt;ins class=&quot;adsbygoogle&quot;...&gt;&lt;/ins&gt;" class="admin-modal-textarea" style="font-family: var(--admin-font-mono); font-size: 0.8rem;"><?= htmlspecialchars($settings['ad_slot_sidebar'] ?? '') ?></textarea>
            </div>
            <div>
              <label class="system-label">Footer Banner Ad Slot</label>
              <textarea name="ad_slot_footer" rows="2" placeholder="&lt;ins class=&quot;adsbygoogle&quot;...&gt;&lt;/ins&gt;" class="admin-modal-textarea" style="font-family: var(--admin-font-mono); font-size: 0.8rem;"><?= htmlspecialchars($settings['ad_slot_footer'] ?? '') ?></textarea>
            </div>
          </div>
        </div>
      </div>

      <button type="submit" class="ts-btn ts-btn-primary" style="padding: 0.75rem 2.5rem; font-weight: 700; font-size: 0.95rem; background: linear-gradient(135deg, #6366f1, #38bdf8);">
        <i data-lucide="save"></i> Save All Site Settings, Social Channels &amp; Links 🚀
      </button>
    </form>
  </div>

  <!-- Dedicated Audit Station Quick Launcher (Replaces legacy embedded table) -->
  <div class="admin-table-wrap" style="padding: 1.5rem 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; border-color: rgba(56,189,248,0.25);">
    <div style="display: flex; align-items: center; gap: 1rem;">
      <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(56,189,248,0.12); border: 1px solid rgba(56,189,248,0.3); display: flex; align-items: center; justify-content: center;">
        <i data-lucide="shield-check" style="color: #38bdf8; width: 24px; height: 24px;"></i>
      </div>
      <div>
        <h3 style="font-size: 1.1rem; color: var(--admin-text-primary); margin: 0; font-weight: 700;">
          Full System Audit Logs &amp; Forensics Station
        </h3>
        <p style="color: var(--admin-text-secondary); font-size: 0.85rem; margin: 0.2rem 0 0 0;">
          All administrative actions, security modifications, and authentication telemetry are archived in the centralized Audit Station.
        </p>
      </div>
    </div>
    <a href="<?= url('/admin/audit-logs') ?>" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #0284c7, #38bdf8); color: #fff; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.65rem 1.4rem;">
      <i data-lucide="external-link" style="width: 15px; height: 15px;"></i> Launch Dedicated Audit Station ↗
    </a>
  </div>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
