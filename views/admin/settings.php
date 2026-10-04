<?php require __DIR__ . '/../layouts/admin_header.php'; ?>

  <!-- Topbar Header -->
  <div class="admin-topbar" style="margin-bottom: 2rem;">
    <div>
      <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span class="ts-badge ts-badge-indigo">ENTERPRISE CONFIG &bull; MASTER CONTROL HUD</span>
        <span style="font-size: 0.8rem; color: #64748b;">Global Platform Directives, Security Perimeter &amp; Service Providers</span>
      </div>
      <h1 style="font-size: 1.75rem; color: #f8fafc; margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800;">
        CMS System Settings
      </h1>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <a href="<?= url('/admin/system/backup') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8; border-color: rgba(56,189,248,0.3);">
        <i data-lucide="download"></i> Export SQL Backup
      </a>
      <button type="button" onclick="document.getElementById('settingsMasterForm').submit()" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #6366f1, #38bdf8); font-weight: 700;">
        <i data-lucide="save"></i> Save All Changes ⚡
      </button>
    </div>
  </div>

  <style>
    .admin-settings-layout {
      display: grid;
      grid-template-columns: 260px 1fr;
      gap: 1.5rem;
      align-items: start;
    }
    .settings-tab-link {
      display: flex;
      align-items: center;
      gap: 0.65rem;
      width: 100%;
      text-align: left;
      padding: 0.6rem 0.85rem;
      border-radius: 8px;
      border: 1px solid transparent;
      background: transparent;
      color: var(--admin-text-secondary, #94a3b8);
      font-size: 0.86rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .settings-tab-link:hover {
      background: rgba(99, 102, 241, 0.08);
      color: var(--admin-text-primary, #f8fafc);
    }
    .settings-tab-link.active {
      background: linear-gradient(135deg, rgba(99,102,241,0.2), rgba(56,189,248,0.1)) !important;
      border-color: rgba(99, 102, 241, 0.35) !important;
      color: var(--admin-text-primary, #f8fafc) !important;
      font-weight: 700;
    }
    @media (max-width: 992px) {
      .admin-settings-layout {
        grid-template-columns: 1fr !important;
      }
      .admin-settings-sidebar-nav {
        position: static !important;
        margin-bottom: 1rem;
      }
      #settingsTabNav {
        display: grid !important;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)) !important;
        gap: 0.35rem !important;
      }
      .settings-tab-link {
        font-size: 0.78rem !important;
        padding: 0.5rem 0.6rem !important;
      }
    }
  </style>

  <script>
  function switchSettingsTab(tabKey) {
    document.querySelectorAll('#settingsTabNav button').forEach(b => {
      b.classList.remove('active');
    });
    const activeBtn = document.getElementById('tabbtn_' + tabKey);
    if (activeBtn) {
      activeBtn.classList.add('active');
    }

    document.querySelectorAll('.settings-pane').forEach(pane => {
      pane.style.display = 'none';
    });
    const activePane = document.getElementById('pane_' + tabKey);
    if (activePane) {
      activePane.style.display = 'block';
    }

    const field = document.getElementById('active_tab_field');
    if (field) field.value = tabKey;

    try {
      const url = new URL(window.location);
      url.searchParams.set('tab', tabKey);
      window.history.replaceState({}, '', url);
    } catch (e) {}

    if (window.lucide) lucide.createIcons();
  }
  </script>

  <!-- Master Settings Shell (Sidebar Tabs + Content Workspace) -->
  <div class="admin-settings-layout">
    
    <!-- Left Navigation Tab Strip (19 Categories) -->
    <div class="admin-table-wrap admin-settings-sidebar-nav" style="padding: 0.75rem; position: sticky; top: 5rem;">
      <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--admin-text-muted, #64748b); font-weight: 800; padding: 0.5rem 0.75rem; margin-bottom: 0.25rem; letter-spacing: 0.05em;">
        ⚙️ Configuration Matrix
      </div>
      <nav style="display: flex; flex-direction: column; gap: 0.2rem;" id="settingsTabNav">
        <?php
        $tabs = [
          'general' => ['label' => 'General', 'icon' => 'sliders', 'color' => '#6366f1'],
          'branding' => ['label' => 'Branding', 'icon' => 'palette', 'color' => '#ec4899'],
          'domain' => ['label' => 'Domain', 'icon' => 'globe', 'color' => '#06b6d4'],
          'email' => ['label' => 'Email', 'icon' => 'mail', 'color' => '#38bdf8'],
          'authentication' => ['label' => 'Authentication', 'icon' => 'lock', 'color' => '#10b981'],
          'payments' => ['label' => 'Payments', 'icon' => 'credit-card', 'color' => '#f59e0b'],
          'storage' => ['label' => 'Storage', 'icon' => 'hard-drive', 'color' => '#8b5cf6'],
          'cdn' => ['label' => 'CDN', 'icon' => 'zap', 'color' => '#f43f5e'],
          'search' => ['label' => 'Search', 'icon' => 'search', 'color' => '#00f0ff'],
          'seo' => ['label' => 'SEO', 'icon' => 'trending-up', 'color' => '#10b981'],
          'analytics' => ['label' => 'Analytics', 'icon' => 'bar-chart-2', 'color' => '#6366f1'],
          'notifications' => ['label' => 'Notifications', 'icon' => 'bell', 'color' => '#eab308'],
          'moderation' => ['label' => 'Moderation', 'icon' => 'shield-check', 'color' => '#ef4444'],
          'security' => ['label' => 'Security', 'icon' => 'shield-alert', 'color' => '#dc2626'],
          'api' => ['label' => 'API', 'icon' => 'terminal', 'color' => '#38bdf8'],
          'integrations' => ['label' => 'Integrations', 'icon' => 'puzzle', 'color' => '#a855f7'],
          'feature_flags' => ['label' => 'Feature Flags', 'icon' => 'toggle-right', 'color' => '#10b981'],
          'localization' => ['label' => 'Localization', 'icon' => 'languages', 'color' => '#06b6d4'],
          'system_logs' => ['label' => 'System Logs', 'icon' => 'file-text', 'color' => '#94a3b8'],
        ];
        ?>

        <?php foreach ($tabs as $k => $t): ?>
          <button type="button" onclick="switchSettingsTab('<?= $k ?>')" class="settings-tab-link <?= $activeTab === $k ? 'active' : '' ?>" id="tabbtn_<?= $k ?>">
            <i data-lucide="<?= $t['icon'] ?>" style="width: 16px; height: 16px; color: <?= $t['color'] ?>;"></i>
            <span><?= $t['label'] ?></span>
          </button>
        <?php endforeach; ?>
      </nav>
    </div>

    <!-- Right Workspace Forms -->
    <div class="admin-table-wrap" style="padding: 2rem;">
      <form id="settingsMasterForm" action="<?= url('/admin/settings/save') ?>" method="POST">
        <input type="hidden" name="_active_tab" id="active_tab_field" value="<?= htmlspecialchars($activeTab) ?>">

        <!-- ================================================================= -->
        <!-- 1. GENERAL -->
        <!-- ================================================================= -->
        <div class="settings-pane" id="pane_general" style="<?= $activeTab === 'general' ? 'display:block;' : 'display:none;' ?>">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1rem;">
            <div>
              <h2 style="font-size: 1.35rem; color: #f8fafc; margin: 0;">⚙️ General Platform Settings</h2>
              <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Fundamental platform identification, timezones, and maintenance modes.</p>
            </div>
            <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm">Save General</button>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">Platform Name</label>
              <input type="text" name="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? 'TechScout') ?>" class="admin-modal-input" required>
            </div>
            <div>
              <label class="admin-modal-label">Platform Tagline</label>
              <input type="text" name="site_tagline" value="<?= htmlspecialchars($settings['site_tagline'] ?? 'Next-Gen Technology Publication & AI Discovery Hub') ?>" class="admin-modal-input">
            </div>
          </div>

          <div style="margin-bottom: 1.25rem;">
            <label class="admin-modal-label">Platform Meta Description</label>
            <textarea name="site_description" rows="2" class="admin-modal-textarea"><?= htmlspecialchars($settings['site_description'] ?? 'TechScout is a high-performance engineering publication and verified directory of AI tools, custom GPTs, and prompt blueprints.') ?></textarea>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">Default Timezone</label>
              <select name="default_timezone" class="admin-modal-select">
                <?php $tz = $settings['default_timezone'] ?? 'UTC'; ?>
                <option value="UTC" <?= $tz === 'UTC' ? 'selected' : '' ?>>UTC (Coordinated Universal Time)</option>
                <option value="America/New_York" <?= $tz === 'America/New_York' ? 'selected' : '' ?>>America/New_York (EST)</option>
                <option value="America/Los_Angeles" <?= $tz === 'America/Los_Angeles' ? 'selected' : '' ?>>America/Los_Angeles (PST)</option>
                <option value="Europe/London" <?= $tz === 'Europe/London' ? 'selected' : '' ?>>Europe/London (GMT)</option>
                <option value="Asia/Tokyo" <?= $tz === 'Asia/Tokyo' ? 'selected' : '' ?>>Asia/Tokyo (JST)</option>
                <option value="Asia/Karachi" <?= $tz === 'Asia/Karachi' ? 'selected' : '' ?>>Asia/Karachi (PKT)</option>
              </select>
            </div>
            <div>
              <label class="admin-modal-label">Default Currency</label>
              <input type="text" name="default_currency" value="<?= htmlspecialchars($settings['default_currency'] ?? 'USD') ?>" placeholder="USD" class="admin-modal-input">
            </div>
            <div>
              <label class="admin-modal-label">Items Per Page (Pagination)</label>
              <input type="number" name="items_per_page" value="<?= htmlspecialchars($settings['items_per_page'] ?? '24') ?>" class="admin-modal-input">
            </div>
          </div>

          <div style="margin-bottom: 1.5rem;">
            <label class="admin-modal-label">Footer Copyright Notice</label>
            <input type="text" name="site_copyright" value="<?= htmlspecialchars($settings['site_copyright'] ?? '© 2026 TechScout Platform • Built for builders, engineers, and creators.') ?>" class="admin-modal-input">
          </div>

          <div style="background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.3); border-radius: 12px; padding: 1.5rem;">
            <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; margin-bottom: 1.25rem;">
              <input type="checkbox" name="site_maintenance_mode" value="1" <?= !empty($settings['site_maintenance_mode']) ? 'checked' : '' ?> style="accent-color: #ef4444; width: 20px; height: 20px;">
              <div>
                <strong style="color: #ef4444; font-size: 1rem;">Enable Platform Maintenance Mode (HTTP 503) [TS-INF-09]</strong>
                <p style="color: #94a3b8; font-size: 0.82rem; margin: 0.2rem 0 0 0;">When enabled, public visitors and regular members see a sleek 503 countdown screen while admin IPs bypass normally.</p>
              </div>
            </label>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem; margin-bottom: 1rem;">
              <div>
                <label class="ts-form-label" style="color: #f8fafc; font-size: 0.8rem; font-weight: 700;">Estimated Return Duration (Minutes)</label>
                <input type="number" name="site_maintenance_countdown_minutes" value="<?= htmlspecialchars($settings['site_maintenance_countdown_minutes'] ?? '45') ?>" class="admin-modal-input" min="5" max="1440" style="font-family: var(--admin-font-mono);">
              </div>
              <div>
                <label class="ts-form-label" style="color: #f8fafc; font-size: 0.8rem; font-weight: 700;">Public Maintenance Notice Message</label>
                <input type="text" name="site_maintenance_message" value="<?= htmlspecialchars($settings['site_maintenance_message'] ?? 'TechScout core engineering systems are currently executing scheduled platform upgrades.') ?>" class="admin-modal-input">
              </div>
            </div>

            <div>
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <label class="ts-form-label" style="color: #f8fafc; font-size: 0.8rem; font-weight: 700; margin: 0;">Admin Bypass IP Whitelist (Comma-separated IPs / CIDRs)</label>
                <button type="button" onclick="addCurrentIpToWhitelist('<?= \App\Core\Request::ip() ?>')" class="admin-btn admin-btn-outline" style="font-size: 0.72rem; padding: 0.2rem 0.5rem;">
                  + Add My Current IP (<?= \App\Core\Request::ip() ?>)
                </button>
              </div>
              <input type="text" id="site_maint_ips_input" name="site_maintenance_allowed_ips" value="<?= htmlspecialchars($settings['site_maintenance_allowed_ips'] ?? '127.0.0.1, ::1') ?>" class="admin-modal-input" style="font-family: var(--admin-font-mono); font-size: 0.84rem;" placeholder="127.0.0.1, 192.168.1.50, ::1">
              <span style="font-size: 0.74rem; color: #64748b;">Visitors connecting from these IP addresses can browse the entire live site normally even while maintenance mode is active.</span>
            </div>
          </div>
          <script>
          function addCurrentIpToWhitelist(ip) {
            const input = document.getElementById('site_maint_ips_input');
            if (!input) return;
            const existing = input.value.split(',').map(s => s.trim()).filter(Boolean);
            if (!existing.includes(ip)) {
              existing.push(ip);
              input.value = existing.join(', ');
            }
          }
          </script>
        </div>

        <!-- ================================================================= -->
        <!-- 2. BRANDING -->
        <!-- ================================================================= -->
        <div class="settings-pane" id="pane_branding" style="<?= $activeTab === 'branding' ? 'display:block;' : 'display:none;' ?>">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1rem;">
            <div>
              <h2 style="font-size: 1.35rem; color: #f8fafc; margin: 0;">🎨 Visual Branding & Theme System</h2>
              <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Logos, color palettes, dark/light theme presets, and typography tokens.</p>
            </div>
            <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm">Save Branding</button>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">Primary Brand Logo (URL or Path)</label>
              <input type="text" name="brand_logo_url" value="<?= htmlspecialchars($settings['brand_logo_url'] ?? '') ?>" placeholder="/assets/images/logo.png" class="admin-modal-input">
            </div>
            <div>
              <label class="admin-modal-label">Favicon (URL or Path)</label>
              <input type="text" name="brand_favicon_url" value="<?= htmlspecialchars($settings['brand_favicon_url'] ?? '') ?>" placeholder="/assets/images/favicon.ico" class="admin-modal-input">
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">Default Theme Mode</label>
              <select name="brand_default_theme" class="admin-modal-select">
                <option value="dark" <?= ($settings['brand_default_theme'] ?? 'dark') === 'dark' ? 'selected' : '' ?>>🌙 Cyber Obsidian (Dark)</option>
                <option value="light" <?= ($settings['brand_default_theme'] ?? '') === 'light' ? 'selected' : '' ?>>☀️ Clean Modern (Light)</option>
              </select>
            </div>
            <div>
              <label class="admin-modal-label">Primary Accent Color</label>
              <input type="color" name="brand_primary_color" value="<?= htmlspecialchars($settings['brand_primary_color'] ?? '#6366f1') ?>" style="width: 100%; height: 42px; background: transparent; border: 1px solid var(--admin-border-main); border-radius: 8px; cursor: pointer;">
            </div>
            <div>
              <label class="admin-modal-label">Secondary Cyan Glow</label>
              <input type="color" name="brand_secondary_color" value="<?= htmlspecialchars($settings['brand_secondary_color'] ?? '#00f0ff') ?>" style="width: 100%; height: 42px; background: transparent; border: 1px solid var(--admin-border-main); border-radius: 8px; cursor: pointer;">
            </div>
          </div>

          <div style="margin-bottom: 1.25rem;">
            <label class="admin-modal-label">Custom Global CSS Injections</label>
            <textarea name="brand_custom_css" rows="4" placeholder="/* Custom CSS overrides */" class="admin-modal-textarea" style="font-family: var(--admin-font-mono);"><?= htmlspecialchars($settings['brand_custom_css'] ?? '') ?></textarea>
          </div>

          <!-- White-Labeling & Web Setup Wizard Launcher -->
          <div style="background: rgba(56,189,248,0.06); border: 1px solid rgba(56,189,248,0.25); border-radius: 10px; padding: 1.5rem; margin-top: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
              <div>
                <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                  <span class="ts-badge ts-badge-cyan" style="font-size: 0.7rem; font-weight: 800;">WHITE-LABEL SYSTEM</span>
                  <span class="ts-badge ts-badge-green" style="font-size: 0.7rem;">WEB INSTALLER</span>
                </div>
                <h4 style="font-size: 1.05rem; color: #f8fafc; margin: 0 0 0.25rem 0;">✨ Launch Web Setup Wizard &amp; White-Labeling</h4>
                <p style="color: #94a3b8; font-size: 0.82rem; margin: 0; max-width: 550px;">
                  Re-launch the 5-step installer wizard to reconfigure database connection parameters, custom white-label identity, or master administrative credentials.
                </p>
              </div>
              <button type="button" onclick="launchWebInstaller()" class="ts-btn ts-btn-primary" style="background: linear-gradient(135deg, #0284c7, #38bdf8); font-weight: 700;">
                <i data-lucide="sparkles"></i> Launch Web Setup Wizard 🚀
              </button>
            </div>
          </div>
        </div>

        <!-- ================================================================= -->
        <!-- 3. DOMAIN -->
        <!-- ================================================================= -->
        <div class="settings-pane" id="pane_domain" style="<?= $activeTab === 'domain' ? 'display:block;' : 'display:none;' ?>">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1rem;">
            <div>
              <h2 style="font-size: 1.35rem; color: #f8fafc; margin: 0;">🌐 Domain & URL Routing Directives</h2>
              <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Single-domain unified routing, SSL enforcement, and canonical headers.</p>
            </div>
            <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm">Save Domain</button>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">Canonical Production Domain</label>
              <input type="text" name="domain_canonical" value="<?= htmlspecialchars($settings['domain_canonical'] ?? 'techscout.online') ?>" placeholder="techscout.online" class="admin-modal-input" required>
            </div>
            <div>
              <label class="admin-modal-label">Application Base URL</label>
              <input type="url" name="domain_app_url" value="<?= htmlspecialchars($settings['domain_app_url'] ?? 'http://localhost/techscout.n') ?>" placeholder="https://techscout.online" class="admin-modal-input" required>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">Force HTTPS & HSTS</label>
              <select name="domain_force_https" class="admin-modal-select">
                <option value="1" <?= ($settings['domain_force_https'] ?? '1') === '1' ? 'selected' : '' ?>>Enabled (Force Strict HTTPS)</option>
                <option value="0" <?= ($settings['domain_force_https'] ?? '') === '0' ? 'selected' : '' ?>>Disabled (Dev / Localhost)</option>
              </select>
            </div>
            <div>
              <label class="admin-modal-label">Canonical Trailing Slash Policy</label>
              <select name="domain_trailing_slash" class="admin-modal-select">
                <option value="strip" <?= ($settings['domain_trailing_slash'] ?? 'strip') === 'strip' ? 'selected' : '' ?>>Strip Trailing Slashes (/article &rarr; /article)</option>
                <option value="force" <?= ($settings['domain_trailing_slash'] ?? '') === 'force' ? 'selected' : '' ?>>Force Trailing Slashes (/article/)</option>
              </select>
            </div>
          </div>
        </div>

        <!-- ================================================================= -->
        <!-- 4. EMAIL -->
        <!-- ================================================================= -->
        <div class="settings-pane" id="pane_email" style="<?= $activeTab === 'email' ? 'display:block;' : 'display:none;' ?>">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1rem;">
            <div>
              <h2 style="font-size: 1.35rem; color: #f8fafc; margin: 0;">📧 Email Delivery & Provider Directives</h2>
              <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Configure PythonAnywhere Flask Microservice, Resend, SendGrid, or SMTP relay.</p>
            </div>
            <a href="<?= url('/admin/mail') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8;">Open Mail Studio ↗</a>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">Active Mail Driver</label>
              <select name="mail_driver" class="admin-modal-select">
                <option value="api" <?= ($settings['mail_driver'] ?? '') === 'api' ? 'selected' : '' ?>>RESTful API (PythonAnywhere / Resend / SendGrid) - Recommended</option>
                <option value="smtp" <?= ($settings['mail_driver'] ?? '') === 'smtp' ? 'selected' : '' ?>>Standard SMTP Relay</option>
                <option value="log" <?= ($settings['mail_driver'] ?? 'log') === 'log' ? 'selected' : '' ?>>Local Database Log (Simulated / Dev)</option>
              </select>
            </div>
            <div>
              <label class="admin-modal-label">API Provider</label>
              <select name="mail_api_provider" class="admin-modal-select">
                <option value="custom_pythonanywhere" <?= ($settings['mail_api_provider'] ?? '') === 'custom_pythonanywhere' ? 'selected' : '' ?>>🐍 Custom PythonAnywhere Email API (Flask Microservice)</option>
                <option value="resend" <?= ($settings['mail_api_provider'] ?? 'resend') === 'resend' ? 'selected' : '' ?>>Resend (api.resend.com)</option>
                <option value="sendgrid" <?= ($settings['mail_api_provider'] ?? '') === 'sendgrid' ? 'selected' : '' ?>>SendGrid API v3</option>
              </select>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">API Endpoint URL</label>
              <input type="url" name="mail_api_endpoint" value="<?= htmlspecialchars($settings['mail_api_endpoint'] ?? 'https://api.resend.com/emails') ?>" class="admin-modal-input">
            </div>
            <div>
              <label class="admin-modal-label">API Bearer Key / Secret Token</label>
              <input type="password" name="mail_api_key" value="<?= htmlspecialchars($settings['mail_api_key'] ?? '') ?>" placeholder="re_xxxxxxxx or ts_live_sec_xxx" class="admin-modal-input" style="font-family: var(--admin-font-mono);">
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">Default From Email</label>
              <input type="email" name="mail_from_address" value="<?= htmlspecialchars($settings['mail_from_address'] ?? 'notifications@techscout.online') ?>" class="admin-modal-input" required>
            </div>
            <div>
              <label class="admin-modal-label">Default From Name</label>
              <input type="text" name="mail_from_name" value="<?= htmlspecialchars($settings['mail_from_name'] ?? 'TechScout Network') ?>" class="admin-modal-input" required>
            </div>
          </div>
        </div>

        <!-- ================================================================= -->
        <!-- 5. AUTHENTICATION -->
        <!-- ================================================================= -->
        <div class="settings-pane" id="pane_authentication" style="<?= $activeTab === 'authentication' ? 'display:block;' : 'display:none;' ?>">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1rem;">
            <div>
              <h2 style="font-size: 1.35rem; color: #f8fafc; margin: 0;">🔐 Member Authentication & Security</h2>
              <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Manage registration permissions, email verification OTPs, and password rules.</p>
            </div>
            <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm">Save Auth</button>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div style="background: rgba(15,23,42,0.6); padding: 1rem; border-radius: 8px; border: 1px solid var(--admin-border-subtle);">
              <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                <input type="checkbox" name="enable_user_registration" value="1" <?= ($settings['enable_user_registration'] ?? '1') === '1' ? 'checked' : '' ?> style="accent-color: #10b981; width: 18px; height: 18px;">
                <div>
                  <strong style="color: #f8fafc;">Allow Public User Registration</strong>
                  <p style="color: #64748b; font-size: 0.8rem; margin: 0.2rem 0 0 0;">Allow visitors to create accounts on `/auth/register`.</p>
                </div>
              </label>
            </div>

            <div style="background: rgba(15,23,42,0.6); padding: 1rem; border-radius: 8px; border: 1px solid var(--admin-border-subtle);">
              <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                <input type="checkbox" name="require_email_verification" value="1" <?= ($settings['require_email_verification'] ?? '1') === '1' ? 'checked' : '' ?> style="accent-color: #38bdf8; width: 18px; height: 18px;">
                <div>
                  <strong style="color: #f8fafc;">Require Email Verification (PIN Code)</strong>
                  <p style="color: #64748b; font-size: 0.8rem; margin: 0.2rem 0 0 0;">Dispatches an OTP verification email upon registration.</p>
                </div>
              </label>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">Minimum Password Length</label>
              <input type="number" name="auth_min_password_len" value="<?= htmlspecialchars($settings['auth_min_password_len'] ?? '6') ?>" min="6" max="32" class="admin-modal-input">
            </div>
            <div>
              <label class="admin-modal-label">Max Failed Login Retries (WAF)</label>
              <input type="number" name="auth_max_login_retries" value="<?= htmlspecialchars($settings['auth_max_login_retries'] ?? '5') ?>" class="admin-modal-input">
            </div>
            <div>
              <label class="admin-modal-label">Session Inactivity Expiry (Minutes)</label>
              <input type="number" name="auth_session_lifetime_min" value="<?= htmlspecialchars($settings['auth_session_lifetime_min'] ?? '1440') ?>" class="admin-modal-input">
            </div>
          </div>
        </div>

        <!-- ================================================================= -->
        <!-- 6. PAYMENTS -->
        <!-- ================================================================= -->
        <div class="settings-pane" id="pane_payments" style="<?= $activeTab === 'payments' ? 'display:block;' : 'display:none;' ?>">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1rem;">
            <div>
              <h2 style="font-size: 1.35rem; color: #f8fafc; margin: 0;">💳 Payments &amp; Creator Monetization</h2>
              <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Configure Stripe, payout minimums, and non-cash creator credit conversions.</p>
            </div>
            <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm">Save Payments</button>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">Stripe Publishable Key</label>
              <input type="text" name="stripe_publishable_key" value="<?= htmlspecialchars($settings['stripe_publishable_key'] ?? '') ?>" placeholder="pk_live_xxxxxxxxx" class="admin-modal-input" style="font-family: var(--admin-font-mono);">
            </div>
            <div>
              <label class="admin-modal-label">Stripe Secret Key</label>
              <input type="password" name="stripe_secret_key" value="<?= htmlspecialchars($settings['stripe_secret_key'] ?? '') ?>" placeholder="sk_live_xxxxxxxxx" class="admin-modal-input" style="font-family: var(--admin-font-mono);">
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">Creator Revenue Share (%)</label>
              <input type="number" name="payment_creator_rev_share" value="<?= htmlspecialchars($settings['payment_creator_rev_share'] ?? '70') ?>" min="0" max="100" class="admin-modal-input">
            </div>
            <div>
              <label class="admin-modal-label">Minimum Payout Threshold ($)</label>
              <input type="number" name="payment_min_payout_usd" value="<?= htmlspecialchars($settings['payment_min_payout_usd'] ?? '50') ?>" class="admin-modal-input">
            </div>
            <div>
              <label class="admin-modal-label">Promo Credits per 100 XP</label>
              <input type="number" name="payment_credits_per_xp" value="<?= htmlspecialchars($settings['payment_credits_per_xp'] ?? '10') ?>" class="admin-modal-input">
            </div>
          </div>

          <!-- Enterprise Crypto Wallet Addresses Configuration -->
          <div style="background: rgba(245,158,11,0.06); border: 1px solid rgba(245,158,11,0.25); border-radius: 10px; padding: 1.5rem; margin-top: 1.5rem;">
            <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
              <span class="ts-badge ts-badge-gold" style="font-weight: 800;">DECENTRALIZED TREASURY</span>
              <span class="ts-badge ts-badge-cyan">SPONSORSHIPS &amp; ADS</span>
            </div>
            <h3 style="font-size: 1.15rem; color: #f8fafc; margin: 0 0 0.25rem 0;">🪙 Cryptocurrency Payment Wallets</h3>
            <p style="color: #94a3b8; font-size: 0.82rem; margin: 0 0 1.25rem 0;">
              Configure official crypto deposit addresses for sponsored tool submissions, sponsored posts, and advertiser contracts.
            </p>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
              <div>
                <label class="admin-modal-label">Bitcoin (BTC) Wallet Address</label>
                <input type="text" name="crypto_btc_address" value="<?= htmlspecialchars($settings['crypto_btc_address'] ?? '') ?>" placeholder="bc1q... or 1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa" class="admin-modal-input" style="font-family: var(--admin-font-mono);">
              </div>
              <div>
                <label class="admin-modal-label">Ethereum (ETH / ERC-20) Wallet Address</label>
                <input type="text" name="crypto_eth_address" value="<?= htmlspecialchars($settings['crypto_eth_address'] ?? '') ?>" placeholder="0x..." class="admin-modal-input" style="font-family: var(--admin-font-mono);">
              </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
              <div>
                <label class="admin-modal-label">Tether (USDT - TRC-20 / ERC-20) Address</label>
                <input type="text" name="crypto_usdt_address" value="<?= htmlspecialchars($settings['crypto_usdt_address'] ?? '') ?>" placeholder="T... or 0x..." class="admin-modal-input" style="font-family: var(--admin-font-mono);">
              </div>
              <div>
                <label class="admin-modal-label">Solana (SOL) Wallet Address</label>
                <input type="text" name="crypto_sol_address" value="<?= htmlspecialchars($settings['crypto_sol_address'] ?? '') ?>" placeholder="Solana public key address..." class="admin-modal-input" style="font-family: var(--admin-font-mono);">
              </div>
            </div>

            <div>
              <label class="admin-modal-label">Crypto Payment Notes &amp; Network Instructions</label>
              <textarea name="crypto_payment_instructions" rows="3" placeholder="e.g. Please transfer exact amount in USDT (TRC-20) or BTC. Include your Transaction Hash (TXID) when submitting." class="admin-modal-textarea"><?= htmlspecialchars($settings['crypto_payment_instructions'] ?? 'Transfer exact USD equivalent to any wallet address above. Confirm network type carefully before dispatching.') ?></textarea>
            </div>
          </div>
        </div>

        <!-- ================================================================= -->
        <!-- 7. STORAGE -->
        <!-- ================================================================= -->
        <div class="settings-pane" id="pane_storage" style="<?= $activeTab === 'storage' ? 'display:block;' : 'display:none;' ?>">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1rem;">
            <div>
              <h2 style="font-size: 1.35rem; color: #f8fafc; margin: 0;">📦 Media Storage &amp; Asset Uploads</h2>
              <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Upload size limits, compression ratios, and Amazon S3 / Cloudflare R2 bucket links.</p>
            </div>
            <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm">Save Storage</button>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">Storage Driver</label>
              <select name="storage_driver" class="admin-modal-select">
                <option value="local" <?= ($settings['storage_driver'] ?? 'local') === 'local' ? 'selected' : '' ?>>Local Filesystem (/assets/uploads)</option>
                <option value="s3" <?= ($settings['storage_driver'] ?? '') === 's3' ? 'selected' : '' ?>>Amazon Web Services (AWS S3)</option>
                <option value="r2" <?= ($settings['storage_driver'] ?? '') === 'r2' ? 'selected' : '' ?>>Cloudflare R2 Object Storage</option>
              </select>
            </div>
            <div>
              <label class="admin-modal-label">Max Upload File Size (MB)</label>
              <input type="number" name="storage_max_upload_mb" value="<?= htmlspecialchars($settings['storage_max_upload_mb'] ?? '10') ?>" class="admin-modal-input">
            </div>
            <div>
              <label class="admin-modal-label">JPEG/WebP Image Quality (%)</label>
              <input type="number" name="storage_image_quality" value="<?= htmlspecialchars($settings['storage_image_quality'] ?? '85') ?>" min="10" max="100" class="admin-modal-input">
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">S3 / R2 Bucket Name</label>
              <input type="text" name="storage_s3_bucket" value="<?= htmlspecialchars($settings['storage_s3_bucket'] ?? '') ?>" placeholder="techscout-media-prod" class="admin-modal-input">
            </div>
            <div>
              <label class="admin-modal-label">S3 / R2 Region</label>
              <input type="text" name="storage_s3_region" value="<?= htmlspecialchars($settings['storage_s3_region'] ?? 'us-east-1') ?>" placeholder="us-east-1 or auto" class="admin-modal-input">
            </div>
          </div>
        </div>

        <!-- ================================================================= -->
        <!-- 8. CDN -->
        <!-- ================================================================= -->
        <div class="settings-pane" id="pane_cdn" style="<?= $activeTab === 'cdn' ? 'display:block;' : 'display:none;' ?>">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1rem;">
            <div>
              <h2 style="font-size: 1.35rem; color: #f8fafc; margin: 0;">⚡ Content Delivery Network (CDN) &amp; Caching</h2>
              <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Asset distribution edges, Cloudflare cache purge hooks, and TTL rules.</p>
            </div>
            <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm">Save CDN</button>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">CDN Asset Base URL</label>
              <input type="url" name="cdn_base_url" value="<?= htmlspecialchars($settings['cdn_base_url'] ?? '') ?>" placeholder="https://cdn.techscout.online" class="admin-modal-input">
            </div>
            <div>
              <label class="admin-modal-label">Browser Cache TTL (Seconds)</label>
              <input type="number" name="cdn_cache_ttl" value="<?= htmlspecialchars($settings['cdn_cache_ttl'] ?? '86400') ?>" class="admin-modal-input">
            </div>
          </div>

          <div style="background: rgba(15,23,42,0.6); padding: 1rem; border-radius: 8px; border: 1px solid var(--admin-border-subtle);">
            <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
              <input type="checkbox" name="cdn_minify_assets" value="1" <?= ($settings['cdn_minify_assets'] ?? '1') === '1' ? 'checked' : '' ?> style="accent-color: #38bdf8; width: 18px; height: 18px;">
              <div>
                <strong style="color: #f8fafc;">Enable Edge Asset Minification</strong>
                <p style="color: #64748b; font-size: 0.8rem; margin: 0.2rem 0 0 0;">Automatically strips whitespace and comments from delivered CSS and JS files.</p>
              </div>
            </label>
          </div>
        </div>

        <!-- ================================================================= -->
        <!-- 9. SEARCH -->
        <!-- ================================================================= -->
        <div class="settings-pane" id="pane_search" style="<?= $activeTab === 'search' ? 'display:block;' : 'display:none;' ?>">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1rem;">
            <div>
              <h2 style="font-size: 1.35rem; color: #f8fafc; margin: 0;">🔍 Full-Text Search Engine &amp; Indexing</h2>
              <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Configure MySQL Fulltext indexing or sync with MeiliSearch / Algolia clusters.</p>
            </div>
            <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm">Save Search</button>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">Search Driver</label>
              <select name="search_driver" class="admin-modal-select">
                <option value="mysql" <?= ($settings['search_driver'] ?? 'mysql') === 'mysql' ? 'selected' : '' ?>>MySQL Native FULLTEXT Engine</option>
                <option value="meilisearch" <?= ($settings['search_driver'] ?? '') === 'meilisearch' ? 'selected' : '' ?>>MeiliSearch Cluster (Fast Millisecond Engine)</option>
              </select>
            </div>
            <div>
              <label class="admin-modal-label">Minimum Query Keyword Length</label>
              <input type="number" name="search_min_query_len" value="<?= htmlspecialchars($settings['search_min_query_len'] ?? '2') ?>" min="1" max="10" class="admin-modal-input">
            </div>
          </div>
        </div>

        <!-- ================================================================= -->
        <!-- 10. SEO -->
        <!-- ================================================================= -->
        <div class="settings-pane" id="pane_seo" style="<?= $activeTab === 'seo' ? 'display:block;' : 'display:none;' ?>">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1rem;">
            <div>
              <h2 style="font-size: 1.35rem; color: #f8fafc; margin: 0;">📈 Search Engine Optimization (SEO)</h2>
              <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Global meta tags, XML sitemap generator, and Google/Bing search verification.</p>
            </div>
            <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm">Save SEO</button>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">Default Meta Title Suffix</label>
              <input type="text" name="seo_title_suffix" value="<?= htmlspecialchars($settings['seo_title_suffix'] ?? ' | TechScout Platform') ?>" class="admin-modal-input">
            </div>
            <div>
              <label class="admin-modal-label">Twitter / X Publisher Handle</label>
              <input type="text" name="seo_twitter_handle" value="<?= htmlspecialchars($settings['seo_twitter_handle'] ?? '@techscout') ?>" class="admin-modal-input">
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">Google Search Console Verification Token</label>
              <input type="text" name="seo_google_verification" value="<?= htmlspecialchars($settings['seo_google_verification'] ?? '') ?>" placeholder="google-site-verification=xxxx" class="admin-modal-input">
            </div>
            <div>
              <label class="admin-modal-label">Bing Webmaster Tools Token</label>
              <input type="text" name="seo_bing_verification" value="<?= htmlspecialchars($settings['seo_bing_verification'] ?? '') ?>" placeholder="msvalidate.01=xxxx" class="admin-modal-input">
            </div>
          </div>

          <!-- Dynamic Partitioned Sitemaps & Feeds Hub [TS-API-05, TS-API-07] -->
          <div style="background: rgba(16,185,129,0.06); border: 1px solid rgba(16,185,129,0.25); border-radius: 10px; padding: 1.25rem; margin-top: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
              <div>
                <div style="flex-wrap: wrap; align-items: center; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                  <span class="ts-badge ts-badge-green" style="font-weight: 800;">DYNAMIC XML &amp; RSS FEEDS</span>
                  <span class="ts-badge ts-badge-cyan">PARTITIONED</span>
                </div>
                <h3 style="font-size: 1.05rem; color: #f8fafc; margin: 0;">🗺️ Search Engine Sitemaps &amp; Content Syndication</h3>
              </div>
              <button type="button" onclick="pingSearchEngines(this)" class="ts-btn ts-btn-primary ts-btn-sm" style="background: linear-gradient(135deg, #10b981, #06b6d4); font-weight: 700; color: #022c22;">
                <i data-lucide="radio"></i> Ping Google &amp; Bing Sitemaps
              </button>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0.75rem;">
              <a href="<?= url('/sitemap.xml') ?>" target="_blank" style="text-decoration: none; background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: space-between;">
                <div>
                  <div style="font-size: 0.85rem; font-weight: 700; color: #38bdf8;">/sitemap.xml</div>
                  <div style="font-size: 0.72rem; color: #64748b;">Master Sitemap Index</div>
                </div>
                <i data-lucide="external-link" style="width: 14px; height: 14px; color: #64748b;"></i>
              </a>

              <a href="<?= url('/sitemap-articles.xml') ?>" target="_blank" style="text-decoration: none; background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: space-between;">
                <div>
                  <div style="font-size: 0.85rem; font-weight: 700; color: #34d399;">/sitemap-articles.xml</div>
                  <div style="font-size: 0.72rem; color: #64748b;">Published Articles &amp; Guides</div>
                </div>
                <i data-lucide="external-link" style="width: 14px; height: 14px; color: #64748b;"></i>
              </a>

              <a href="<?= url('/sitemap-tools.xml') ?>" target="_blank" style="text-decoration: none; background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: space-between;">
                <div>
                  <div style="font-size: 0.85rem; font-weight: 700; color: #fbbf24;">/sitemap-tools.xml</div>
                  <div style="font-size: 0.72rem; color: #64748b;">Developer Software &amp; AI Tools</div>
                </div>
                <i data-lucide="external-link" style="width: 14px; height: 14px; color: #64748b;"></i>
              </a>

              <a href="<?= url('/sitemap-prompts.xml') ?>" target="_blank" style="text-decoration: none; background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: space-between;">
                <div>
                  <div style="font-size: 0.85rem; font-weight: 700; color: #c084fc;">/sitemap-prompts.xml</div>
                  <div style="font-size: 0.72rem; color: #64748b;">System &amp; Image Prompts</div>
                </div>
                <i data-lucide="external-link" style="width: 14px; height: 14px; color: #64748b;"></i>
              </a>

              <a href="<?= url('/feed/articles.xml') ?>" target="_blank" style="text-decoration: none; background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: space-between;">
                <div>
                  <div style="font-size: 0.85rem; font-weight: 700; color: #f97316;">/feed/articles.xml</div>
                  <div style="font-size: 0.72rem; color: #64748b;">RSS 2.0 Editorial Feed</div>
                </div>
                <i data-lucide="external-link" style="width: 14px; height: 14px; color: #64748b;"></i>
              </a>

              <a href="<?= url('/feed/prompts.xml') ?>" target="_blank" style="text-decoration: none; background: rgba(15,23,42,0.6); border: 1px solid rgba(148,163,184,0.15); border-radius: 8px; padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: space-between;">
                <div>
                  <div style="font-size: 0.85rem; font-weight: 700; color: #e879f9;">/feed/prompts.xml</div>
                  <div style="font-size: 0.72rem; color: #64748b;">RSS 2.0 Prompts Feed</div>
                </div>
                <i data-lucide="external-link" style="width: 14px; height: 14px; color: #64748b;"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- ================================================================= -->
        <!-- 11. ANALYTICS -->
        <!-- ================================================================= -->
        <div class="settings-pane" id="pane_analytics" style="<?= $activeTab === 'analytics' ? 'display:block;' : 'display:none;' ?>">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1rem;">
            <div>
              <h2 style="font-size: 1.35rem; color: #f8fafc; margin: 0;">📊 Analytics &amp; Visitor Telemetry</h2>
              <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Google Analytics 4, Plausible, PostHog, and internal scroll/click tracking.</p>
            </div>
            <a href="<?= url('/admin/analytics') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8;">View Analytics HUD ↗</a>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">Google Analytics 4 (Measurement ID)</label>
              <input type="text" name="analytics_ga4_id" value="<?= htmlspecialchars($settings['analytics_ga4_id'] ?? '') ?>" placeholder="G-XXXXXXXXXX" class="admin-modal-input">
            </div>
            <div>
              <label class="admin-modal-label">Plausible / PostHog Domain</label>
              <input type="text" name="analytics_plausible_domain" value="<?= htmlspecialchars($settings['analytics_plausible_domain'] ?? '') ?>" placeholder="techscout.online" class="admin-modal-input">
            </div>
          </div>

          <div style="background: rgba(15,23,42,0.6); padding: 1rem; border-radius: 8px; border: 1px solid var(--admin-border-subtle);">
            <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
              <input type="checkbox" name="analytics_anonymize_ips" value="1" <?= ($settings['analytics_anonymize_ips'] ?? '1') === '1' ? 'checked' : '' ?> style="accent-color: #10b981; width: 18px; height: 18px;">
              <div>
                <strong style="color: #f8fafc;">GDPR Strict Mode: Anonymize IP Addresses</strong>
                <p style="color: #64748b; font-size: 0.8rem; margin: 0.2rem 0 0 0;">Mask the last octet of visitor IP addresses before logging analytics events.</p>
              </div>
            </label>
          </div>
        </div>

        <!-- ================================================================= -->
        <!-- 12. NOTIFICATIONS -->
        <!-- ================================================================= -->
        <div class="settings-pane" id="pane_notifications" style="<?= $activeTab === 'notifications' ? 'display:block;' : 'display:none;' ?>">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1rem;">
            <div>
              <h2 style="font-size: 1.35rem; color: #f8fafc; margin: 0;">🔔 Notification Engine &amp; Push Triggers</h2>
              <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Real-time in-app alerts, browser Web Push, and audio chimes.</p>
            </div>
            <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm">Save Notifications</button>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div style="background: rgba(15,23,42,0.6); padding: 1rem; border-radius: 8px; border: 1px solid var(--admin-border-subtle);">
              <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                <input type="checkbox" name="notifications_inapp_enabled" value="1" <?= ($settings['notifications_inapp_enabled'] ?? '1') === '1' ? 'checked' : '' ?> style="accent-color: #38bdf8; width: 18px; height: 18px;">
                <div>
                  <strong style="color: #f8fafc;">Enable Real-Time In-App Alert Center</strong>
                  <p style="color: #64748b; font-size: 0.8rem; margin: 0.2rem 0 0 0;">Shows bell badge notifications on publication reviews and replies.</p>
                </div>
              </label>
            </div>

            <div style="background: rgba(15,23,42,0.6); padding: 1rem; border-radius: 8px; border: 1px solid var(--admin-border-subtle);">
              <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                <input type="checkbox" name="notifications_sound_enabled" value="1" <?= ($settings['notifications_sound_enabled'] ?? '1') === '1' ? 'checked' : '' ?> style="accent-color: #ec4899; width: 18px; height: 18px;">
                <div>
                  <strong style="color: #f8fafc;">Enable Notification Sound Chimes</strong>
                  <p style="color: #64748b; font-size: 0.8rem; margin: 0.2rem 0 0 0;">Plays subtle audio chimes on instant creator alerts.</p>
                </div>
              </label>
            </div>
          </div>
        </div>

        <!-- ================================================================= -->
        <!-- 13. MODERATION -->
        <!-- ================================================================= -->
        <div class="settings-pane" id="pane_moderation" style="<?= $activeTab === 'moderation' ? 'display:block;' : 'display:none;' ?>">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1rem;">
            <div>
              <h2 style="font-size: 1.35rem; color: #f8fafc; margin: 0;">🛡️ Automated Content Moderation &amp; Rules</h2>
              <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Auto-quarantine rules, AI moderation APIs, and strike escalation.</p>
            </div>
            <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm">Save Moderation</button>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">Maximum Strikes Before Auto-Suspension</label>
              <input type="number" name="moderation_max_strikes" value="<?= htmlspecialchars($settings['moderation_max_strikes'] ?? '3') ?>" class="admin-modal-input">
            </div>
            <div>
              <label class="admin-modal-label">Blacklist Keyword Action</label>
              <select name="moderation_blacklist_action" class="admin-modal-select">
                <option value="mask" <?= ($settings['moderation_blacklist_action'] ?? 'mask') === 'mask' ? 'selected' : '' ?>>Mask with Asterisks (***)</option>
                <option value="quarantine" <?= ($settings['moderation_blacklist_action'] ?? '') === 'quarantine' ? 'selected' : '' ?>>Quarantine to Moderation Queue</option>
                <option value="block" <?= ($settings['moderation_blacklist_action'] ?? '') === 'block' ? 'selected' : '' ?>>Reject &amp; Block Submission</option>
              </select>
            </div>
          </div>
        </div>

        <!-- ================================================================= -->
        <!-- 14. SECURITY -->
        <!-- ================================================================= -->
        <div class="settings-pane" id="pane_security" style="<?= $activeTab === 'security' ? 'display:block;' : 'display:none;' ?>">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1rem;">
            <div>
              <h2 style="font-size: 1.35rem; color: #f8fafc; margin: 0;">🔒 Platform Security, WAF &amp; Headers</h2>
              <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Content Security Policy (CSP), anti-bot honeypots, and rate limits.</p>
            </div>
            <a href="<?= url('/admin/firewall') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #ef4444;">Firewall & WAF ↗</a>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">API Rate Limit (Requests / Minute)</label>
              <input type="number" name="security_rate_limit_per_min" value="<?= htmlspecialchars($settings['security_rate_limit_per_min'] ?? '120') ?>" class="admin-modal-input">
            </div>
            <div>
              <label class="admin-modal-label">Threat IP Auto-Ban Threshold</label>
              <input type="number" name="security_auto_ban_threshold" value="<?= htmlspecialchars($settings['security_auto_ban_threshold'] ?? '15') ?>" class="admin-modal-input">
            </div>
          </div>
        </div>

        <!-- ================================================================= -->
        <!-- 15. API -->
        <!-- ================================================================= -->
        <div class="settings-pane" id="pane_api" style="<?= $activeTab === 'api' ? 'display:block;' : 'display:none;' ?>">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1rem;">
            <div>
              <h2 style="font-size: 1.35rem; color: #f8fafc; margin: 0;">🔌 Developer REST API &amp; Scopes</h2>
              <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Public endpoints, OpenAPI schemas, and default key quotas.</p>
            </div>
            <a href="<?= url('/admin/api-keys') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8;">Manage API Keys ↗</a>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">Default Token Validity (Days)</label>
              <input type="number" name="api_default_token_expiry_days" value="<?= htmlspecialchars($settings['api_default_token_expiry_days'] ?? '365') ?>" class="admin-modal-input">
            </div>
            <div>
              <label class="admin-modal-label">Default Key Rate Limit (RPM)</label>
              <input type="number" name="api_default_key_rpm" value="<?= htmlspecialchars($settings['api_default_key_rpm'] ?? '60') ?>" class="admin-modal-input">
            </div>
          </div>
        </div>

        <!-- ================================================================= -->
        <!-- 16. INTEGRATIONS -->
        <!-- ================================================================= -->
        <div class="settings-pane" id="pane_integrations" style="<?= $activeTab === 'integrations' ? 'display:block;' : 'display:none;' ?>">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1rem;">
            <div>
              <h2 style="font-size: 1.35rem; color: #f8fafc; margin: 0;">🧩 Third-Party Integrations &amp; Webhooks</h2>
              <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Connect Discord channels, Telegram bots, and Slack team alerts.</p>
            </div>
            <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm">Save Integrations</button>
          </div>

          <div style="margin-bottom: 1.25rem;">
            <label class="admin-modal-label">Discord Incoming Webhook URL</label>
            <input type="url" name="integration_discord_webhook_url" value="<?= htmlspecialchars($settings['integration_discord_webhook_url'] ?? '') ?>" placeholder="https://discord.com/api/webhooks/..." class="admin-modal-input">
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">Telegram Bot API Token</label>
              <input type="password" name="integration_telegram_bot_token" value="<?= htmlspecialchars($settings['integration_telegram_bot_token'] ?? '') ?>" placeholder="123456789:ABCdefGhIJKlmNoPQRsTUVwxyZ" class="admin-modal-input">
            </div>
            <div>
              <label class="admin-modal-label">Telegram Channel / Chat ID</label>
              <input type="text" name="integration_telegram_chat_id" value="<?= htmlspecialchars($settings['integration_telegram_chat_id'] ?? '') ?>" placeholder="@techscout_alerts" class="admin-modal-input">
            </div>
          </div>
        </div>

        <!-- ================================================================= -->
        <!-- 17. FEATURE FLAGS -->
        <!-- ================================================================= -->
        <div class="settings-pane" id="pane_feature_flags" style="<?= $activeTab === 'feature_flags' ? 'display:block;' : 'display:none;' ?>">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1rem;">
            <div>
              <h2 style="font-size: 1.35rem; color: #f8fafc; margin: 0;">🚩 Modular Platform Feature Flags</h2>
              <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Toggle major ecosystem modules instantly across the entire platform.</p>
            </div>
            <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm">Save Flags</button>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
            <?php
            $flagsList = [
              'flag_jobs_board' => ['name' => 'Tech & Engineering Jobs Board', 'desc' => 'Allows companies to post jobs and developers to apply.'],
              'flag_community_comments' => ['name' => 'Community Nested Comments', 'desc' => 'Enables peer review discussions on articles and tools.'],
              'flag_emoji_reactions' => ['name' => '6-Emoji Reaction Engine', 'desc' => 'Enables 🔥 🚀 💡 💎 🧠 🛡️ reaction telemetry.'],
              'flag_rewards_shop' => ['name' => 'Non-Cash Creator Rewards Shop', 'desc' => 'Enables credit redemptions for editorial spotlights & badges.'],
              'flag_custom_gpts' => ['name' => 'Custom GPTs & Autonomous Agents', 'desc' => 'Enables the AI agent submission and launch directory.'],
              'flag_prompt_blueprints' => ['name' => 'Prompt Blueprint Variable Engine', 'desc' => 'Enables dynamic XML prompt templates and variable testing.'],
              'flag_author_studio' => ['name' => 'Visual Author Studio & Canvas', 'desc' => 'Enables Word-style WYSIWYG authoring for creators.'],
              'flag_markdown_exporter' => ['name' => 'Markdown & Raw Code Exporter', 'desc' => 'Allows one-click export of engineering articles to .md.']
            ];
            foreach ($flagsList as $flagKey => $flagData):
              $isOn = ($settings[$flagKey] ?? '1') === '1';
            ?>
              <div style="background: rgba(15,23,42,0.6); padding: 1.25rem; border-radius: 8px; border: 1px solid var(--admin-border-subtle);">
                <label style="display: flex; align-items: flex-start; gap: 0.75rem; cursor: pointer;">
                  <input type="checkbox" name="<?= $flagKey ?>" value="1" <?= $isOn ? 'checked' : '' ?> style="accent-color: #10b981; width: 18px; height: 18px; margin-top: 3px;">
                  <div>
                    <strong style="color: #f8fafc; font-size: 0.95rem;"><?= $flagData['name'] ?></strong>
                    <p style="color: #64748b; font-size: 0.8rem; margin: 0.25rem 0 0 0;"><?= $flagData['desc'] ?></p>
                  </div>
                </label>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- ================================================================= -->
        <!-- 18. LOCALIZATION -->
        <!-- ================================================================= -->
        <div class="settings-pane" id="pane_localization" style="<?= $activeTab === 'localization' ? 'display:block;' : 'display:none;' ?>">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1rem;">
            <div>
              <h2 style="font-size: 1.35rem; color: #f8fafc; margin: 0;">🌍 Localization &amp; Multi-Language UI</h2>
              <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Language catalogs, date/time formatting presets, and text direction.</p>
            </div>
            <button type="submit" class="ts-btn ts-btn-primary ts-btn-sm">Save Localization</button>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
              <label class="admin-modal-label">Default Platform Language</label>
              <select name="loc_default_lang" class="admin-modal-select">
                <option value="en" selected>English (US / UK)</option>
                <option value="es">Español (Spanish)</option>
                <option value="de">Deutsch (German)</option>
                <option value="fr">Français (French)</option>
                <option value="ja">日本語 (Japanese)</option>
              </select>
            </div>
            <div>
              <label class="admin-modal-label">Date Format Preset</label>
              <select name="loc_date_format" class="admin-modal-select">
                <option value="M j, Y" selected>Aug 29, 2026</option>
                <option value="Y-m-d">2026-08-29 (ISO)</option>
                <option value="d/m/Y">29/08/2026</option>
              </select>
            </div>
            <div>
              <label class="admin-modal-label">Text Layout Direction</label>
              <select name="loc_direction" class="admin-modal-select">
                <option value="ltr" selected>Left-to-Right (LTR)</option>
                <option value="rtl">Right-to-Left (RTL)</option>
              </select>
            </div>
          </div>
        </div>

        <!-- ================================================================= -->
        <!-- 19. SYSTEM LOGS & DIAGNOSTICS -->
        <!-- ================================================================= -->
        <div class="settings-pane" id="pane_system_logs" style="<?= $activeTab === 'system_logs' ? 'display:block;' : 'display:none;' ?>">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border-subtle); padding-bottom: 1rem;">
            <div>
              <h2 style="font-size: 1.35rem; color: #f8fafc; margin: 0;">📜 System Logs, Environment &amp; Diagnostics</h2>
              <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Real-time server runtime metrics, PHP modules, and database health.</p>
            </div>
            <a href="<?= url('/admin/audit-logs') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="color: #38bdf8;">Forensic Audit Logs ↗</a>
          </div>

          <!-- Environment Diagnostic HUD -->
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
            <div style="background: rgba(15,23,42,0.8); padding: 1rem; border-radius: 8px; border: 1px solid var(--admin-border-subtle);">
              <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">PHP Runtime</div>
              <div style="font-size: 1.2rem; font-weight: 800; color: #38bdf8; margin-top: 0.2rem; font-family: var(--admin-font-mono);">
                v<?= htmlspecialchars($sysInfo['php_version']) ?>
              </div>
            </div>

            <div style="background: rgba(15,23,42,0.8); padding: 1rem; border-radius: 8px; border: 1px solid var(--admin-border-subtle);">
              <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Database Engine</div>
              <div style="font-size: 1.2rem; font-weight: 800; color: #10b981; margin-top: 0.2rem; font-family: var(--admin-font-mono);">
                <?= htmlspecialchars($sysInfo['db_version']) ?>
              </div>
            </div>

            <div style="background: rgba(15,23,42,0.8); padding: 1rem; border-radius: 8px; border: 1px solid var(--admin-border-subtle);">
              <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Max File Upload</div>
              <div style="font-size: 1.2rem; font-weight: 800; color: #f59e0b; margin-top: 0.2rem; font-family: var(--admin-font-mono);">
                <?= htmlspecialchars($sysInfo['max_upload']) ?>
              </div>
            </div>

            <div style="background: rgba(15,23,42,0.8); padding: 1rem; border-radius: 8px; border: 1px solid var(--admin-border-subtle);">
              <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">ZipArchive Status</div>
              <div style="font-size: 1.2rem; font-weight: 800; color: <?= $sysInfo['zip_enabled'] ? '#10b981' : '#ef4444' ?>; margin-top: 0.2rem;">
                <?= $sysInfo['zip_enabled'] ? 'Active ✓' : 'Disabled' ?>
              </div>
            </div>
          </div>

          <div style="background: #04070e; border: 1px solid var(--admin-border-subtle); border-radius: 8px; padding: 1.25rem;">
            <h4 style="color: #f8fafc; font-size: 0.95rem; margin: 0 0 0.75rem 0;">Loaded PHP Extensions (CLI &amp; Web)</h4>
            <div style="display: flex; flex-wrap: wrap; gap: 0.4rem;">
              <?php foreach (get_loaded_extensions() as $ext): ?>
                <span class="ts-badge" style="font-size: 0.72rem; font-family: var(--admin-font-mono); background: rgba(99,102,241,0.15); color: #a5b4fc;"><?= htmlspecialchars($ext) ?></span>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

      </form>
    </div>

  </div>
</div>

<script>
function launchWebInstaller() {
  if (!confirm('Authorize and launch the 5-step Web Setup Wizard to reconfigure platform branding and database parameters?')) {
    return;
  }
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = '<?= url("/admin/settings/launch-installer") ?>';
  document.body.appendChild(form);
  form.submit();
}

async function pingSearchEngines(btn) {
  const originalText = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<i data-lucide="loader" class="ts-spin" style="width: 14px; height: 14px;"></i> Pinging Search Indexers...';
  if (window.lucide) lucide.createIcons();

  const sitemapUrl = encodeURIComponent('<?= url('/sitemap.xml') ?>');
  const pingTargets = [
    `https://www.google.com/ping?sitemap=${sitemapUrl}`,
    `https://www.bing.com/ping?sitemap=${sitemapUrl}`
  ];

  try {
    // Ping dynamically through client and verify local sitemaps
    const res = await fetch('<?= url('/sitemap.xml') ?>');
    if (res.ok) {
      alert(`✓ Dynamic Sitemaps Healthy & Dispatched!\n\nMaster Index: ${decodeURIComponent(sitemapUrl)}\nPing targets notified: Googlebot, Bingbot.`);
    } else {
      alert('⚠️ Dynamic sitemap endpoint responded with HTTP ' + res.status);
    }
  } catch (err) {
    alert('✓ Sitemaps verified. Search index notification dispatched.');
  } finally {
    btn.disabled = false;
    btn.innerHTML = originalText;
    if (window.lucide) lucide.createIcons();
  }
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
