<?php
use App\Core\Auth;
use App\Core\Database;

$currentUser = Auth::user();
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
$adminSiteSettings = [];
try {
    $rows = Database::query("SELECT key_name, key_value FROM site_settings WHERE key_name = 'enable_bg_grid_lines'");
    foreach ($rows as $r) {
        $adminSiteSettings[$r['key_name']] = $r['key_value'];
    }
} catch (\Throwable $e) {}

// Determine Active Admin Theme: URL param (?theme=) > Cookie (ts_theme) > Default dark
$activeAdminTheme = (!empty($_GET['theme']) && $_GET['theme'] === 'light') 
    ? 'light' 
    : ((!empty($_GET['theme']) && $_GET['theme'] === 'dark')
        ? 'dark'
        : ((isset($_COOKIE['ts_theme']) && $_COOKIE['ts_theme'] === 'light') ? 'light' : 'dark'));

// Admin Top Header

?>
<!DOCTYPE html>
<html lang="en" data-theme="<?= $activeAdminTheme ?>" class="<?= $activeAdminTheme === 'light' ? 'theme-light' : '' ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script>
    (function() {
      try {
        const tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
        if (tz) {
          const match = document.cookie.match(/(^|;)\s*user_timezone=([^;]+)/);
          const currentTz = match ? decodeURIComponent(match[2]) : null;
          if (currentTz !== tz) {
            document.cookie = "user_timezone=" + encodeURIComponent(tz) + ";path=/;max-age=31536000;SameSite=Lax";
          }
        }
      } catch (e) {}
    })();
  </script>
  <title><?= htmlspecialchars($title ?? 'Admin Command Center | TechScout') ?></title>
  
  <!-- Favicon Assets -->
  <link rel="icon" type="image/svg+xml" href="<?= url('assets/favicon.svg') ?>">
  <link rel="alternate icon" href="<?= url('favicon.ico') ?>">
  
  <!-- Modern Typography -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
  
  <!-- CSS Framework & Cyber-HUD Admin Style -->
  <link rel="stylesheet" href="<?= url('assets/css/style.css?v=' . time()) ?>">
  <link rel="stylesheet" href="<?= url('assets/css/admin.css?v=' . time()) ?>">

  <!-- PJAX SPA Navigation Styles -->
  <style>
    #adminPjaxProgressBar {
      position: fixed;
      top: 0;
      left: 0;
      height: 3px;
      width: 0%;
      background: linear-gradient(90deg, #06b6d4, #6366f1, #ec4899);
      box-shadow: 0 0 12px rgba(6, 182, 212, 0.8), 0 0 20px rgba(236, 72, 153, 0.6);
      z-index: 99999999;
      pointer-events: none;
      transition: width 0.22s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.25s ease;
      opacity: 0;
    }
    .admin-content-wrapper {
      transition: opacity 0.18s cubic-bezier(0.4, 0, 0.2, 1), transform 0.18s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .admin-content-wrapper.pjax-anim-out {
      opacity: 0.15;
      transform: translateY(4px);
    }
    .admin-content-wrapper.pjax-anim-in {
      opacity: 1;
      transform: translateY(0);
    }
  </style>

  <!-- Toastify Notifications -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
  <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

  <!-- Local High-Performance Lucide Icons & Chart.js -->
  <script src="<?= url('assets/js/lucide.min.js') ?>" onerror="this.onerror=null;this.src='https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.js';"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

  <script>
    (function() {
      try {
        const tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
        if (tz) {
          const match = document.cookie.match(/(^|;)\s*user_timezone=([^;]+)/);
          const currentTz = match ? decodeURIComponent(match[2]) : null;
          if (currentTz !== tz) {
            document.cookie = "user_timezone=" + encodeURIComponent(tz) + ";path=/;max-age=31536000;SameSite=Lax";
          }
        }
      } catch (e) {}
      var urlParams = new URLSearchParams(window.location.search);
      var cookieTheme = (document.cookie.match(/ts_theme=([^;]+)/) || [])[1];
      var t = urlParams.get('theme') || cookieTheme || localStorage.getItem('ts_theme') || '<?= $activeAdminTheme ?>';
      document.documentElement.setAttribute('data-theme', t);
      if (t === 'light') {
        document.documentElement.classList.add('theme-light');
      } else {
        document.documentElement.classList.remove('theme-light');
      }
    })();
    if (localStorage.getItem('admin_sidebar_collapsed') === 'true') {
      document.documentElement.classList.add('sidebar-init-collapsed');
    }
  </script>
</head>
<body data-theme="<?= $activeAdminTheme ?>" class="admin-body <?= $activeAdminTheme === 'light' ? 'theme-light' : '' ?> <?= (!empty($adminSiteSettings['enable_bg_grid_lines']) && $adminSiteSettings['enable_bg_grid_lines'] === '1') ? 'ts-bg-grid-lines' : '' ?>">
  <!-- Top Slim High-Tech Progress Bar for SPA PJAX Transitions -->
  <div id="adminPjaxProgressBar"></div>
  <?php if (\App\Core\Auth::isImpersonating()): ?>
    <?php 
      $impAdmin = \App\Core\Auth::getImpersonatorAdmin(); 
      $impersonatedUser = \App\Core\Auth::user();
    ?>
    <div id="ts-impersonation-sticky-bar-admin" style="position: sticky; top: 0; left: 0; right: 0; z-index: 999999; background: linear-gradient(90deg, #ea580c, #f97316); color: #ffffff; padding: 0.65rem 1.5rem; display: flex; align-items: center; justify-content: space-between; font-family: 'Inter', sans-serif; font-size: 0.88rem; font-weight: 600; box-shadow: 0 4px 20px rgba(234, 88, 12, 0.45); border-bottom: 2px solid #c2410c;">
      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <span style="font-size: 1.25rem; line-height: 1;">⚠️</span>
        <span>
          <strong>ADMIN IMPERSONATION MODE:</strong> Viewing platform as 
          <strong style="text-decoration: underline;"><?= htmlspecialchars($impersonatedUser['display_name'] ?? $impersonatedUser['username'] ?? '') ?></strong> 
          (@<?= htmlspecialchars($impersonatedUser['username'] ?? '') ?> &bull; <?= strtoupper($impersonatedUser['role'] ?? 'MEMBER') ?>).
          <span style="opacity: 0.9; font-size: 0.78rem; margin-left: 0.5rem; background: rgba(0,0,0,0.2); padding: 0.15rem 0.45rem; border-radius: 4px;">Original Admin: @<?= htmlspecialchars($impAdmin['username'] ?? 'Admin') ?></span>
        </span>
      </div>
      <a href="<?= url('/auth/impersonate/exit') ?>" style="background: #ffffff; color: #c2410c; padding: 0.4rem 1.1rem; border-radius: 6px; text-decoration: none; font-weight: 800; font-size: 0.82rem; display: inline-flex; align-items: center; gap: 0.4rem; box-shadow: 0 2px 6px rgba(0,0,0,0.2); transition: all 0.2s ease;">
        <span>Exit Impersonation Mode &rarr;</span>
      </a>
    </div>
  <?php endif; ?>
  <div class="admin-shell" id="adminShell">
    <!-- Mobile Backdrop Drawer Overlay -->
    <div id="adminMobileBackdrop" class="admin-mobile-backdrop" onclick="toggleAdminSidebar()"></div>

    <!-- Sidebar Navigation -->
    <aside class="admin-sidebar" id="adminSidebar">
      <div class="sidebar-header-strip" style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--admin-border-subtle); padding: 0.75rem 0.85rem;">
        <a href="<?= url('/admin/dashboard') ?>" class="admin-brand" style="border: none; padding: 0.25rem 0;">
          <div class="logo-icon" style="background: linear-gradient(135deg, #ec4899, #6366f1);"><i data-lucide="shield-alert"></i></div>
          <div class="brand-text">
            <div style="font-family: 'Outfit', sans-serif; font-size: 1.15rem; font-weight: 800; color: #f8fafc; line-height: 1.2;">
              TechScout <span style="color: #ec4899;">HQ</span>
            </div>
            <div class="hud-status">
              <span class="live-pulse"></span>
              100x Operations Engine
            </div>
          </div>
        </a>

        <button type="button" onclick="toggleAdminSidebar()" class="ts-btn ts-btn-outline ts-btn-sm sidebar-collapse-trigger" style="padding: 0.25rem 0.45rem; border-color: rgba(148,163,184,0.15);" title="Collapse / Expand Sidebar">
          <i data-lucide="panel-left-close" id="collapseIcon" style="width: 16px; height: 16px;"></i>
        </button>
      </div>

      <!-- Navigation Links with Collapsible Group Accordions -->
      <?php
      $pendingAppsCount = (int)(\App\Core\Database::fetch("SELECT COUNT(*) as c FROM author_applications WHERE status = 'pending'")['c'] ?? 0);
      $pendingQueueCount = (int)(\App\Core\Database::fetch("SELECT COUNT(*) as c FROM contents WHERE status = 'in_review'")['c'] ?? 0);

      // RBAC Permission Helper for Navigation Rendering
      $userRole = $currentUser['role'] ?? '';
      $adminRoleId = (int)($currentUser['admin_role_id'] ?? 0);
      $isSuperAdmin = in_array($userRole, ['super_admin', 'superadmin'], true) || ($userRole === 'admin' && $adminRoleId <= 0);

      $canAccess = function(string $permKey) use ($currentUser, $isSuperAdmin): bool {
          if ($isSuperAdmin) return true;
          return \App\Models\RBAC::hasPermission($permKey, (int)($currentUser['id'] ?? 0));
      };

      // Calculate visibility for each group
      $showCommandGroup = true; // Dashboard HUD is universally accessible to any authenticated admin/staff
      $showContentGroup = $isSuperAdmin || $canAccess('articles.view') || $canAccess('articles.create') || $canAccess('articles.edit') || $canAccess('community.moderation');
      $showMarketingGroup = $isSuperAdmin || $canAccess('revenue.view') || $canAccess('cms.settings');
      $showLayoutGroup = $isSuperAdmin || $canAccess('cms.settings') || $canAccess('system.flags');
      $showCommunityGroup = $isSuperAdmin || $canAccess('authors.view') || $canAccess('community.reports') || $canAccess('community.moderation') || $canAccess('support.tickets') || $canAccess('support.kb') || $canAccess('support.reply');
      $showEnterpriseGroup = $isSuperAdmin || $canAccess('system.rbac') || $canAccess('security.firewall') || $canAccess('system.logs') || $canAccess('system.backups') || $canAccess('cms.settings');
      ?>
      <nav class="admin-nav" id="adminNavScrollable">
        
        <!-- 1. Command & Intelligence -->
        <?php if ($showCommandGroup): ?>
        <details class="admin-nav-group" id="group_command" open>
          <summary class="nav-section-label">
            <span>Command &amp; Intelligence</span>
            <i data-lucide="chevron-down" class="group-caret"></i>
          </summary>
          <div class="nav-group-items">
            <a href="<?= url('/admin/dashboard') ?>" class="admin-nav-link <?= str_contains($currentPath, '/dashboard') || $currentPath === '/admin' ? 'active' : '' ?>" data-tooltip="Dashboard HUD">
              <i data-lucide="layout-dashboard"></i> <span>Dashboard HUD</span>
            </a>
            <?php if ($isSuperAdmin || $canAccess('articles.view')): ?>
              <a href="<?= url('/admin/analytics') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/analytics') ? 'active' : '' ?>" data-tooltip="CMS Analytics HUD">
                <i data-lucide="bar-chart-3"></i> <span>CMS Analytics HUD</span>
              </a>
            <?php endif; ?>
            <?php if ($isSuperAdmin || $canAccess('articles.publish') || $canAccess('articles.edit')): ?>
              <a href="<?= url('/admin/content') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/content') ? 'active' : '' ?>" data-tooltip="Content Review Queue">
                <i data-lucide="check-square"></i> <span>Content Queue</span>
                <?php if ($pendingQueueCount > 0): ?>
                  <span class="ts-badge ts-badge-cyan" style="margin-left: auto; font-size: 0.7rem; padding: 0.1rem 0.4rem;"><?= $pendingQueueCount ?></span>
                <?php endif; ?>
              </a>
            <?php endif; ?>
            <?php if ($isSuperAdmin || $canAccess('system.flags')): ?>
              <a href="<?= url('/admin/engagement-studio') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/engagement-studio') ? 'active' : '' ?>" data-tooltip="Engagement &amp; Telemetry Simulator">
                <i data-lucide="sparkles"></i> <span>Engagement Simulator</span>
                <span class="ts-badge ts-badge-pink" style="margin-left: auto; font-size: 0.65rem;">SIM</span>
              </a>
            <?php endif; ?>
          </div>
        </details>
        <?php endif; ?>

        <!-- 2. Content CRUD Engines -->
        <?php if ($showContentGroup): ?>
        <details class="admin-nav-group" id="group_content" open>
          <summary class="nav-section-label">
            <span>Content CRUD Engines</span>
            <i data-lucide="chevron-down" class="group-caret"></i>
          </summary>
          <div class="nav-group-items">
            <?php if ($isSuperAdmin || $canAccess('articles.view')): ?>
              <a href="<?= url('/admin/articles') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/articles') ? 'active' : '' ?>" data-tooltip="Articles CRUD">
                <i data-lucide="file-text"></i> <span>Articles CRUD</span>
              </a>
              <a href="<?= url('/admin/tools') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/tools') ? 'active' : '' ?>" data-tooltip="AI Tools CRUD">
                <i data-lucide="cpu"></i> <span>AI Tools CRUD</span>
              </a>
              <a href="<?= url('/admin/prompts') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/prompts') ? 'active' : '' ?>" data-tooltip="Prompts CRUD">
                <i data-lucide="terminal"></i> <span>Prompts CRUD</span>
              </a>
              <a href="<?= url('/admin/gpts') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/gpts') ? 'active' : '' ?>" data-tooltip="Custom GPTs CRUD">
                <i data-lucide="bot"></i> <span>Custom GPTs CRUD</span>
              </a>
              <a href="<?= url('/admin/campaigns') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/campaigns') ? 'active' : '' ?>" data-tooltip="Campaigns & Bounties">
                <i data-lucide="flag"></i> <span>Campaigns CRUD</span>
              </a>
              <a href="<?= url('/admin/jobs') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/jobs') ? 'active' : '' ?>" data-tooltip="Tech Jobs Board">
                <i data-lucide="briefcase"></i> <span>Tech Jobs Board</span>
              </a>
              <a href="<?= url('/admin/topics') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/topics') ? 'active' : '' ?>" data-tooltip="Topics Taxonomy">
                <i data-lucide="layers"></i> <span>Topics Taxonomy</span>
              </a>
              <a href="<?= url('/admin/categories') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/categories') ? 'active' : '' ?>" data-tooltip="Categories CRUD">
                <i data-lucide="folder-tree"></i> <span>Categories CRUD</span>
              </a>
              <a href="<?= url('/admin/media') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/media') ? 'active' : '' ?>" data-tooltip="Media Library &amp; DAM">
                <i data-lucide="image"></i> <span>Media Library (DAM)</span>
              </a>
            <?php endif; ?>
            <?php if ($isSuperAdmin || $canAccess('community.moderation')): ?>
              <a href="<?= url('/admin/content-filters') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/content-filters') ? 'active' : '' ?>" data-tooltip="Auto-Moderation Filters">
                <i data-lucide="filter"></i> <span>Content Blacklist</span>
              </a>
            <?php endif; ?>
          </div>
        </details>
        <?php endif; ?>

        <!-- 3. Marketing & Communications -->
        <?php if ($showMarketingGroup): ?>
        <details class="admin-nav-group" id="group_marketing" open>
          <summary class="nav-section-label">
            <span>Marketing &amp; Growth</span>
            <i data-lucide="chevron-down" class="group-caret"></i>
          </summary>
          <div class="nav-group-items">
            <?php if ($isSuperAdmin || $canAccess('revenue.view')): ?>
              <a href="<?= url('/admin/revenue') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/revenue') ? 'active' : '' ?>" data-tooltip="Creator Revenue & Payouts">
                <i data-lucide="wallet"></i> <span>Creator Payouts</span>
              </a>
            <?php endif; ?>
            <?php if ($isSuperAdmin || $canAccess('cms.settings')): ?>
              <a href="<?= url('/admin/newsletters') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/newsletters') ? 'active' : '' ?>" data-tooltip="CMS Newsletters">
                <i data-lucide="mail"></i> <span>CMS Newsletters</span>
              </a>
              <a href="<?= url('/admin/subscribers') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/subscribers') ? 'active' : '' ?>" data-tooltip="Audience Subscribers">
                <i data-lucide="user-plus"></i> <span>Audience Subscribers</span>
              </a>
              <a href="<?= url('/admin/announcements') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/announcements') ? 'active' : '' ?>" data-tooltip="Broadcast Ribbons">
                <i data-lucide="megaphone"></i> <span>Broadcast Ribbons</span>
              </a>
              <a href="<?= url('/admin/ad-manager') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/ad') ? 'active' : '' ?>" data-tooltip="Ad Placements & Sponsorships">
                <i data-lucide="badge-dollar-sign"></i> <span>Ad Placements &amp; Sponsors</span>
              </a>
            <?php endif; ?>
          </div>
        </details>
        <?php endif; ?>

        <!-- 4. Navigation & Layout -->
        <?php if ($showLayoutGroup): ?>
        <details class="admin-nav-group" id="group_layout" open>
          <summary class="nav-section-label">
            <span>Navigation &amp; Layout</span>
            <i data-lucide="chevron-down" class="group-caret"></i>
          </summary>
          <div class="nav-group-items">
            <a href="<?= url('/admin/homepage') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/homepage') ? 'active' : '' ?>" data-tooltip="Homepage Manager">
              <i data-lucide="layout-template"></i> <span>Homepage Manager</span>
            </a>
            <a href="<?= url('/admin/navigation') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/navigation') ? 'active' : '' ?>" data-tooltip="CMS Navigation Studio">
              <i data-lucide="compass"></i> <span>Navigation Studio</span>
            </a>
            <a href="<?= url('/admin/legal-pages') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/legal-pages') ? 'active' : '' ?>" data-tooltip="Legal &amp; Compliance CMS">
              <i data-lucide="scale"></i> <span>Legal Pages CMS</span>
            </a>
            <a href="<?= url('/admin/seo') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/seo') ? 'active' : '' ?>" data-tooltip="SEO Suite &amp; Sitemaps">
              <i data-lucide="trending-up"></i> <span>SEO Suite (12 Tools)</span>
            </a>
            <a href="<?= url('/admin/seo-studio') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/seo-studio') ? 'active' : '' ?>" data-tooltip="SEO Diagnostics & Sitemap">
              <i data-lucide="search-check"></i> <span>SEO Studio &amp; Robots</span>
            </a>
            <?php if ($isSuperAdmin || $canAccess('system.flags')): ?>
              <a href="<?= url('/admin/feature-flags') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/feature-flags') ? 'active' : '' ?>" data-tooltip="Feature Flags">
                <i data-lucide="toggle-left"></i> <span>Feature Flags</span>
              </a>
            <?php endif; ?>
          </div>
        </details>
        <?php endif; ?>

        <!-- 5. Community & Support -->
        <?php if ($showCommunityGroup): ?>
        <details class="admin-nav-group" id="group_community" open>
          <summary class="nav-section-label">
            <span>Community &amp; Support</span>
            <i data-lucide="chevron-down" class="group-caret"></i>
          </summary>
          <div class="nav-group-items">
            <?php if ($isSuperAdmin || $canAccess('authors.view')): ?>
              <a href="<?= url('/admin/users') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/users') ? 'active' : '' ?>" data-tooltip="Users & Creators">
                <i data-lucide="users"></i> <span>Users &amp; Creators</span>
              </a>
              <a href="<?= url('/admin/applications') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/applications') ? 'active' : '' ?>" data-tooltip="Author Applications">
                <i data-lucide="award"></i> <span>Author Applications</span>
                <?php if (!empty($pendingAppsCount)): ?>
                  <span class="ts-badge ts-badge-red" style="padding: 0.15rem 0.45rem; font-size: 0.68rem; margin-left: auto;"><?= $pendingAppsCount ?></span>
                <?php endif; ?>
              </a>
              <a href="<?= url('/admin/jobs/applications') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/jobs/applications') ? 'active' : '' ?>" data-tooltip="Job Candidates">
                <i data-lucide="user-check"></i> <span>Job Candidates</span>
              </a>
            <?php endif; ?>
            <?php if ($isSuperAdmin || $canAccess('support.reply')): ?>
              <a href="<?= url('/admin/contact-messages') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/contact-messages') ? 'active' : '' ?>" data-tooltip="Inbox Inquiries">
                <i data-lucide="inbox"></i> <span>Inbox Inquiries</span>
              </a>
            <?php endif; ?>
            <?php if ($isSuperAdmin || $canAccess('community.reports') || $canAccess('community.moderation')): ?>
              <a href="<?= url('/admin/moderation') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/moderation') ? 'active' : '' ?>" data-tooltip="Moderation Station">
                <i data-lucide="shield"></i> <span>Moderation Station</span>
              </a>
            <?php endif; ?>
            <?php if ($isSuperAdmin || $canAccess('support.tickets')): ?>
              <a href="<?= url('/admin/tickets') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/tickets') ? 'active' : '' ?>" data-tooltip="Support Tickets">
                <i data-lucide="life-buoy"></i> <span>Support Tickets</span>
              </a>
              <a href="<?= url('/admin/support/categories') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/support/categories') ? 'active' : '' ?>" data-tooltip="Support Categories &amp; SLAs">
                <i data-lucide="layers"></i> <span>Support Categories &amp; SLAs</span>
              </a>
            <?php endif; ?>
            <?php if ($isSuperAdmin || $canAccess('support.kb')): ?>
              <a href="<?= url('/admin/help-articles') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/help-articles') ? 'active' : '' ?>" data-tooltip="Help Center Articles CMS">
                <i data-lucide="book-open"></i> <span>Help Articles CMS</span>
              </a>
            <?php endif; ?>
          </div>
        </details>
        <?php endif; ?>

        <!-- 6. Enterprise, Security & API -->
        <?php if ($showEnterpriseGroup): ?>
        <details class="admin-nav-group" id="group_enterprise" open>
          <summary class="nav-section-label">
            <span>System &amp; Enterprise</span>
            <i data-lucide="chevron-down" class="group-caret"></i>
          </summary>
          <div class="nav-group-items">
            <?php if ($isSuperAdmin || $canAccess('cms.settings')): ?>
              <a href="<?= url('/admin/settings') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/settings') ? 'active' : '' ?>" data-tooltip="CMS System Settings">
                <i data-lucide="sliders"></i> <span>CMS System Settings</span>
              </a>
            <?php endif; ?>
            <?php if ($isSuperAdmin || $canAccess('system.rbac')): ?>
              <a href="<?= url('/admin/roles') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/roles') ? 'active' : '' ?>" data-tooltip="Staff RBAC & Roles">
                <i data-lucide="shield-check"></i> <span>Staff Roles &amp; RBAC</span>
              </a>
            <?php endif; ?>
            <?php if ($isSuperAdmin || $canAccess('security.firewall')): ?>
              <a href="<?= url('/admin/security') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/security') || str_contains($currentPath, '/admin/firewall') ? 'active' : '' ?>" data-tooltip="Cyber Firewall">
                <i data-lucide="shield-alert"></i> <span>Cyber Firewall &amp; WAF</span>
              </a>
            <?php endif; ?>
            <?php if ($isSuperAdmin || $canAccess('cms.settings')): ?>
              <a href="<?= url('/admin/api-keys') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/api-keys') ? 'active' : '' ?>" data-tooltip="REST API Keys">
                <i data-lucide="key"></i> <span>Developer API Keys</span>
              </a>
              <a href="<?= url('/admin/webhooks') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/webhooks') ? 'active' : '' ?>" data-tooltip="Webhooks Engine">
                <i data-lucide="webhook"></i> <span>Webhooks Engine</span>
              </a>
              <a href="<?= url('/admin/mail') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/mail') ? 'active' : '' ?>" data-tooltip="Mail & Templates">
                <i data-lucide="mail-check"></i> <span>Mail &amp; Template Studio</span>
              </a>
            <?php endif; ?>
            <?php if ($isSuperAdmin || $canAccess('system.backups')): ?>
              <a href="<?= url('/admin/db-manager') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/db-manager') ? 'active' : '' ?>" data-tooltip="Database Health & Snapshots">
                <i data-lucide="database"></i> <span>Database &amp; Snapshots</span>
              </a>
            <?php endif; ?>
            <?php if ($isSuperAdmin || $canAccess('system.logs')): ?>
              <a href="<?= url('/admin/audit-logs') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/audit-logs') ? 'active' : '' ?>" data-tooltip="Audit Telemetry">
                <i data-lucide="file-clock"></i> <span>Audit Telemetry</span>
              </a>
              <a href="<?= url('/admin/system-logs') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/system-logs') || str_contains($currentPath, '/admin/error-logs') ? 'active' : '' ?>" data-tooltip="Crash Forensics & Error Logs">
                <i data-lucide="alert-octagon"></i> <span>System Error Logs</span>
              </a>
            <?php endif; ?>
            <?php if ($isSuperAdmin || $canAccess('system.flags') || $canAccess('cms.settings')): ?>
              <a href="<?= url('/admin/system') ?>" class="admin-nav-link <?= str_contains($currentPath, '/admin/system') ? 'active' : '' ?>" data-tooltip="System Settings & Diagnostics">
                <i data-lucide="cpu"></i> <span>System Diagnostics</span>
              </a>
            <?php endif; ?>
            <a href="<?= url('/') ?>" class="admin-nav-link" target="_blank" style="color: #38bdf8;" data-tooltip="View Live Site">
              <i data-lucide="external-link"></i> <span>Live Site ↗</span>
            </a>
          </div>
        </details>
        <?php endif; ?>

      </nav>
    </aside>

    <!-- Main Content Area -->
    <main class="admin-main">
      <header class="admin-topbar">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
          <button type="button" onclick="toggleAdminSidebar()" class="ts-btn ts-btn-outline ts-btn-sm" style="padding: 0.35rem 0.65rem;" title="Toggle Sidebar Width">
            <i data-lucide="menu" style="width: 16px; height: 16px;"></i>
          </button>

          <!-- Topbar Command Search Trigger -->
          <button type="button" onclick="openAdminCommandPalette()" class="ts-btn ts-btn-outline ts-btn-sm" style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; padding: 0.35rem 0.75rem; border-color: rgba(148,163,184,0.25);">
            <i data-lucide="search" style="width: 14px; height: 14px; color: #38bdf8;"></i>
            <span style="display: none; @media(min-width:768px){display:inline;}">Search admin commands &amp; tools...</span>
            <kbd style="background: rgba(148,163,184,0.15); border: 1px solid rgba(148,163,184,0.25); border-radius: 4px; padding: 0.1rem 0.35rem; font-size: 0.65rem; color: #94a3b8; font-family: monospace;">Ctrl K</kbd>
          </button>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
          <!-- Theme Mode Switcher Button -->
          <button type="button" onclick="toggleTheme()" class="ts-btn ts-btn-outline ts-btn-sm" id="adminThemeToggleBtn" style="padding: 0.35rem 0.65rem;" title="Toggle Dark / Light Theme">
            <i data-lucide="moon" id="adminThemeIcon" style="width: 15px; height: 15px;"></i>
          </button>

          <a href="<?= url('/admin/content') ?>" class="ts-btn ts-btn-outline ts-btn-sm" style="font-size: 0.75rem;">
            <i data-lucide="layers"></i> Queue (<?= $pendingQueueCount ?>)
          </a>
          <a href="<?= url('/admin/mail') ?>" class="ts-btn ts-btn-primary ts-btn-sm" style="font-size: 0.75rem; background: linear-gradient(135deg, #6366f1, #38bdf8);">
            <i data-lucide="send"></i> Mail Studio 🚀
          </a>
        </div>
      </header>

      <div class="admin-content-wrapper">
