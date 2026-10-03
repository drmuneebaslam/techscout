<?php
use App\Core\Auth;
use App\Core\Session;
use App\Core\Config;
use App\Core\Database;
$user = Auth::user();
$appUrl = rtrim(Config::get('app_url'), '/');
$siteSettings = [];
try {
    $rows = Database::query("SELECT key_name, key_value FROM site_settings");
    foreach ($rows as $r) {
        $siteSettings[$r['key_name']] = $r['key_value'];
    }
} catch (\Throwable $e) {}

// Dynamic Active Navigation Detector
$reqUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$basePath = parse_url($appUrl, PHP_URL_PATH) ?? '';
$cleanPath = '/' . trim(str_replace($basePath, '', $reqUri), '/');
if ($cleanPath === '//') $cleanPath = '/';

$isNavActive = function(string $route) use ($cleanPath): bool {
    if ($route === '/') {
        return $cleanPath === '/' || $cleanPath === '';
    }
    return str_starts_with($cleanPath, $route);
};
// Determine Active Theme with strict priority: URL query param (?theme=) > Cookie (ts_theme) > Default light
$activeTheme = (!empty($_GET['theme']) && $_GET['theme'] === 'dark')
    ? 'dark'
    : ((!empty($_GET['theme']) && $_GET['theme'] === 'light')
        ? 'light'
        : ((isset($_COOKIE['ts_theme']) && $_COOKIE['ts_theme'] === 'dark') ? 'dark' : (Config::get('default_theme') ?: 'light')));
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?= $activeTheme ?>" class="<?= $activeTheme === 'light' ? 'theme-light' : '' ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
  <meta name="google-site-verification" content="<?= htmlspecialchars($siteSettings['google_site_verification'] ?? 'google-site-verification-techscout-online') ?>">
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
      var cookieMatch = document.cookie.match(/(^|;)\s*ts_theme=([^;]+)/);
      var cookieTheme = cookieMatch ? cookieMatch[2].trim() : null;
      var storedTheme = null;
      try { storedTheme = localStorage.getItem('ts_theme'); } catch(e) {}
      var t = urlParams.get('theme') || storedTheme || cookieTheme || '<?= $activeTheme ?>';
      if (t !== 'light' && t !== 'dark') t = 'light';
      document.documentElement.setAttribute('data-theme', t);
      if (t === 'light') {
        document.documentElement.classList.add('theme-light');
      } else {
        document.documentElement.classList.remove('theme-light');
      }
      window.TS_BASE_URL = '<?= rtrim(\App\Core\Config::url('/'), '/') ?>';

      var isToggling = false;
      window.syncAllThemeButtons = function(theme) {
        document.querySelectorAll('#theme-toggle, #adminThemeToggleBtn, .ts-theme-toggle').forEach(function(btn) {
          btn.innerHTML = '<i data-lucide="' + (theme === 'light' ? 'sun' : 'moon') + '" style="width: 15px; height: 15px;"></i>';
        });
        if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
          lucide.createIcons();
        }
      };

      window.toggleTheme = function() {
        if (isToggling) return;
        isToggling = true;
        setTimeout(function() { isToggling = false; }, 250);

        var html = document.documentElement;
        var body = document.body;
        var current = (html.getAttribute('data-theme') === 'light' || html.classList.contains('theme-light')) ? 'light' : 'dark';
        var next = current === 'dark' ? 'light' : 'dark';

        html.setAttribute('data-theme', next);
        if (next === 'light') {
          html.classList.add('theme-light');
        } else {
          html.classList.remove('theme-light');
        }

        if (body) {
          body.setAttribute('data-theme', next);
          if (next === 'light') {
            body.classList.add('theme-light');
          } else {
            body.classList.remove('theme-light');
          }
        }

        try {
          localStorage.setItem('ts_theme', next);
          document.cookie = "ts_theme=" + next + "; path=/; max-age=31536000; SameSite=Lax";
        } catch(e) {}

        window.syncAllThemeButtons(next);
        window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: next } }));
      };
    })();
  </script>
  <script src="<?= url('/assets/js/telemetry-error-logger.js') ?>" defer></script>
  <title><?= htmlspecialchars($title ?? 'TechScout — The Frontier of Tech, Scouted for Builders') ?></title>
  
  <!-- Network & CDN Preconnect Hints -->
  <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
  <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
  <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
  <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">

  <!-- Favicon Assets -->
  <link rel="icon" type="image/svg+xml" href="<?= url('assets/favicon.svg') ?>">
  
  <?php if (!empty($siteSettings['ad_header_code'])): ?>
    <?= $siteSettings['ad_header_code'] ?>
  <?php endif; ?>
  
  <!-- Modern Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
  
  <?php 
    $cssV = filemtime(__DIR__ . '/../../assets/css/style.css'); 
    if (!empty($user) && $user['role'] === 'admin') $cssV = time(); 
  ?>
  <link rel="stylesheet" href="<?= url('/assets/css/style.css?v=' . $cssV) ?>">
  
  <!-- Local High-Performance Deferred Scripts & Styles -->
  <script src="<?= url('/assets/js/lucide.min.js') ?>" defer onerror="this.onerror=null;this.src='https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.js';"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" media="print" onload="this.media='all'">
  <script src="https://cdn.jsdelivr.net/npm/toastify-js" defer></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" defer></script>

  <?php
    $currentUri = $_SERVER['REQUEST_URI'] ?? '';
    $needsPrism = str_contains($currentUri, '/article/') 
      || str_contains($currentUri, '/prompt/') 
      || str_contains($currentUri, '/editor')
      || (!empty($article['body_html']) && str_contains($article['body_html'], '<pre'));
  ?>
  <?php if ($needsPrism): ?>
    <!-- Conditionally Loaded Prism Syntax Highlighting -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" media="print" onload="this.media='all'">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js" defer></script>
  <?php endif; ?>
  

  
  <!-- Dynamic Base Url for JS -->
  <script>window.TS_BASE_URL = "<?= $appUrl ?>";</script>

  <style>
    .maint-admin-indicator-banner {
      background: #7f1d1d;
      color: #fee2e2;
      padding: 0.5rem 1rem;
      font-size: 0.85rem;
      position: relative;
      z-index: 1000;
      border-bottom: 1px solid rgba(239, 68, 68, 0.4);
    }
    [data-theme="light"] .maint-admin-indicator-banner,
    .theme-light .maint-admin-indicator-banner {
      background: #fee2e2;
      color: #991b1b;
      border-bottom: 1px solid rgba(239, 68, 68, 0.2);
    }
  </style>
</head><body data-theme="<?= $activeTheme ?>" class="<?= $activeTheme === 'light' ? 'theme-light' : '' ?> <?= (!empty($siteSettings['enable_bg_grid_lines']) && $siteSettings['enable_bg_grid_lines'] === '1') ? 'ts-bg-grid-lines' : '' ?>">
<script>
  (function() {
    var t = document.documentElement.getAttribute('data-theme') || 'dark';
    document.body.setAttribute('data-theme', t);
    if (t === 'light') {
      document.body.classList.add('theme-light');
    } else {
      document.body.classList.remove('theme-light');
    }
  })();
</script>

  <?php if (\App\Core\Auth::isImpersonating()): ?>
    <?php 
      $impAdmin = \App\Core\Auth::getImpersonatorAdmin(); 
      $impersonatedUser = \App\Core\Auth::user();
    ?>
    <div id="ts-impersonation-sticky-bar" style="position: sticky; top: 0; left: 0; right: 0; z-index: 999999; background: linear-gradient(90deg, #ea580c, #f97316); color: #ffffff; padding: 0.65rem 1.5rem; display: flex; align-items: center; justify-content: space-between; font-family: 'Inter', sans-serif; font-size: 0.88rem; font-weight: 600; box-shadow: 0 4px 20px rgba(234, 88, 12, 0.45); border-bottom: 2px solid #c2410c;">
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

  <?php if (!empty($siteSettings['site_maintenance_mode']) && $siteSettings['site_maintenance_mode'] === '1' && (Auth::isAdmin() || !empty($_SESSION['maintenance_bypass']))): ?>
    <div class="maint-admin-indicator-banner">
      <div class="container" style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
        <span style="display: flex; align-items: center; gap: 0.5rem; font-weight: 700;">
          <i data-lucide="alert-triangle" style="width: 18px; height: 18px; color: #f59e0b;"></i>
          <span><strong>MAINTENANCE MODE ACTIVE:</strong> Public visitors receive HTTP 503. You are bypassing this as Administrator.</span>
        </span>
        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
          <a href="<?= url('/admin/settings?tab=general') ?>" class="ts-btn ts-btn-xs ts-btn-outline" style="border-color: currentColor;">⚙️ Settings</a>
          <a href="<?= url('/?preview_503=1') ?>" target="_blank" class="ts-btn ts-btn-xs ts-btn-primary">👁️ Preview 503 Screen ↗</a>
          <a href="<?= url('/?simulate_guest=1') ?>" class="ts-btn ts-btn-xs ts-btn-outline" style="background: rgba(239,68,68,0.2); border-color: rgba(239,68,68,0.5); color: #fca5a5;">🔒 Test As Visitor (Lockout)</a>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <?php
    // Run real-time security perimeter check
    \App\Core\Firewall::check();

    // Render active global announcement banners
    $activeBanners = \App\Core\BannerHelper::getActiveBanners();
    foreach ($activeBanners as $banner):
  ?>
    <div id="site-announcement-<?= $banner['id'] ?>" class="ts-announcement-banner" style="background: <?= htmlspecialchars($banner['banner_color']) ?>; color: <?= htmlspecialchars($banner['text_color']) ?>; padding: 0.55rem 1rem; font-size: 0.85rem; text-align: center; position: relative; z-index: 100; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 0.75rem; flex-wrap: wrap;">
      <span><?= htmlspecialchars($banner['message']) ?></span>
      <?php if (!empty($banner['action_url'])): ?>
        <a href="<?= htmlspecialchars($banner['action_url']) ?>" style="background: rgba(0,0,0,0.25); color: #ffffff; padding: 0.2rem 0.65rem; border-radius: 9999px; text-decoration: none; font-size: 0.78rem; border: 1px solid rgba(255,255,255,0.3);">
          <?= htmlspecialchars($banner['action_label'] ?: 'Learn More') ?> &rarr;
        </a>
      <?php endif; ?>
      <button type="button" onclick="dismissBanner(<?= (int)$banner['id'] ?>)" style="background: rgba(0,0,0,0.2); border: none; color: <?= htmlspecialchars($banner['text_color']) ?>; width: 22px; height: 22px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; opacity: 0.85; cursor: pointer; font-size: 0.95rem; line-height: 1; padding: 0; margin-left: 0.5rem; transition: all 0.2s ease;" title="Dismiss announcement">&times;</button>
    </div>
  <?php endforeach; ?>

  <!-- Top Navigation Bar -->
  <header class="ts-navbar">
    <div class="nav-inner" style="width: 100%; max-width: 100%; padding: 0 2rem;">
      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <!-- Mobile Sidebar Toggle -->
        <button id="mobile-menu-toggle" class="ts-mobile-menu-btn" aria-label="Toggle Mobile Navigation">
          <i data-lucide="menu" style="width: 22px; height: 22px;"></i>
        </button>

        <!-- Brand Logo -->
        <a href="<?= url('/') ?>" class="ts-logo">
          <div class="logo-icon"><i data-lucide="radar"></i></div>
          <span>Tech<span style="color: var(--ts-primary-light);">Scout</span></span>
        </a>
      </div>

      <!-- Desktop Navigation Links (Dynamic from CMS Navigation Studio, Active Indicator System) -->
      <?php
      $dynamicNavItems = [];
      $activeFlags = [];
      try {
          $dynamicNavItems = \App\Core\Database::query("SELECT * FROM navigation_menus WHERE location = 'main' AND is_active = 1 ORDER BY sort_order ASC, id ASC");
          $flagsRows = \App\Core\Database::query("SELECT flag_key, is_enabled FROM feature_flags");
          foreach ($flagsRows as $fr) {
              $activeFlags[$fr['flag_key']] = (int)$fr['is_enabled'] === 1;
          }
      } catch (\Throwable $e) {}

      if (empty($dynamicNavItems)) {
          $dynamicNavItems = [
              ['label' => 'Home', 'url' => '/', 'target' => '_self'],
              ['label' => 'Articles', 'url' => '/blog', 'target' => '_self'],
              ['label' => 'Categories', 'url' => '/categories', 'target' => '_self'],
              ['label' => 'AI Tools', 'url' => '/tools', 'target' => '_self'],
              ['label' => 'Prompts', 'url' => '/prompts', 'target' => '_self'],
              ['label' => 'GPTs', 'url' => '/gpts', 'target' => '_self'],
              ['label' => 'Challenges', 'url' => '/campaigns', 'target' => '_self'],
          ];
      }

      $isNavVisible = function($url) use ($activeFlags) {
          $trimmed = trim($url, '/');
          if ($trimmed === 'tools' && isset($activeFlags['enable_ai_tools']) && !$activeFlags['enable_ai_tools']) return false;
          if ($trimmed === 'prompts' && isset($activeFlags['enable_prompts']) && !$activeFlags['enable_prompts']) return false;
          if ($trimmed === 'gpts' && isset($activeFlags['enable_gpts']) && !$activeFlags['enable_gpts']) return false;
          if ($trimmed === 'campaigns' && isset($activeFlags['enable_challenges']) && !$activeFlags['enable_challenges']) return false;
          if ($trimmed === 'jobs' && isset($activeFlags['enable_jobs']) && !$activeFlags['enable_jobs']) return false;
          return true;
      };
      ?>
      <ul class="ts-nav-links">
        <?php foreach ($dynamicNavItems as $nav): ?>
          <?php 
            $navLabel = trim((string)($nav['label'] ?? ''));
            $navRawUrl = trim((string)($nav['url'] ?? ''));
            if (strcasecmp($navLabel, 'topics') === 0 || strcasecmp($navLabel, 'topic') === 0) continue;
            if (trim($navRawUrl, '/') === 'topics' || str_contains($navRawUrl, '/topics')) continue;
            if (!$isNavVisible($nav['url'])) continue;
            $navUrl = str_starts_with($nav['url'], 'http') ? $nav['url'] : url($nav['url']); 
            $isActive = $isNavActive($nav['url']);
          ?>
          <li>
            <a href="<?= htmlspecialchars($navUrl) ?>" target="<?= htmlspecialchars($nav['target'] ?? '_self') ?>" class="<?= $isActive ? 'active' : '' ?>">
              <?= htmlspecialchars($nav['label']) ?>
              <?php if (!empty($nav['badge_text'])): ?>
                <span class="ts-badge ts-badge-<?= htmlspecialchars($nav['badge_color'] ?? 'cyan') ?>" style="font-size:0.6rem; padding:0.05rem 0.35rem; margin-left:0.25rem;">
                  <?= htmlspecialchars($nav['badge_text']) ?>
                </span>
              <?php endif; ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>

      <!-- Right Action Items -->
      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <!-- Instant Search Trigger (Cmd+K) -->
        <button class="ts-btn ts-btn-outline ts-btn-sm trigger-search-modal" title="Search (Cmd+K)">
          <i data-lucide="search" style="width: 15px; height: 15px;"></i>
          <span class="desktop-only-text" style="color: var(--ts-text-tertiary); font-size: 0.78rem; margin-left: 0.2rem;">⌘K</span>
        </button>

        <!-- Theme Toggle -->
        <button id="theme-toggle" type="button" onclick="toggleTheme()" class="ts-btn ts-btn-outline ts-btn-sm" title="Toggle Theme">
          <i data-lucide="moon" style="width: 15px; height: 15px;"></i>
        </button>



        <?php if ($user): ?>
          <?php $unreadNotifCount = \App\Core\NotificationHelper::getUnreadCount((int)$user['id']); ?>
          <!-- In-App Notification Center Trigger -->
          <div class="ts-profile-menu-wrap" id="notif-center-wrap" style="position: relative;">
            <button class="ts-btn ts-btn-outline ts-btn-sm" id="notif-bell-btn" onclick="toggleNotifDropdown()" title="Notifications" style="position: relative; padding: 0.4rem 0.6rem;">
              <i data-lucide="bell" style="width: 16px; height: 16px;"></i>
              <span id="notif-badge" class="notif-badge-pill" style="<?= $unreadNotifCount > 0 ? 'display: flex;' : 'display: none;' ?> position: absolute; top: -5px; right: -5px; background: #ec4899; color: #fff; font-size: 0.65rem; font-weight: 800; border-radius: 999px; min-width: 18px; height: 18px; align-items: center; justify-content: center; padding: 0 4px; box-shadow: 0 0 10px rgba(236,72,153,0.6);">
                <?= $unreadNotifCount > 99 ? '99+' : $unreadNotifCount ?>
              </span>
            </button>

            <!-- Notifications Dropdown Popup with True Glassmorphism -->
            <div id="notif-dropdown" class="ts-glassmorphism-panel" style="display: none; position: absolute; top: calc(100% + 12px); right: 0; width: 400px; max-width: 90vw; border-radius: 16px; z-index: 99999; overflow: hidden; animation: tsDropdownFadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);">
              
              <!-- Header Bar -->
              <div class="notif-dropdown-header" style="display: flex; justify-content: space-between; align-items: center; padding: 0.9rem 1.15rem; border-bottom: 1px solid var(--ts-border-subtle);">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                  <div style="width: 28px; height: 28px; border-radius: 8px; background: linear-gradient(135deg, #6366f1, #38bdf8); display: flex; align-items: center; justify-content: center; color: #fff; box-shadow: 0 2px 8px rgba(56,189,248,0.3);">
                    <i data-lucide="bell" style="width: 14px; height: 14px;"></i>
                  </div>
                  <div>
                    <strong style="font-size: 0.92rem; font-family: 'Outfit', sans-serif;">Notifications</strong>
                    <span id="notif-dropdown-unread-pill" style="font-size: 0.68rem; color: #38bdf8; margin-left: 0.35rem; font-weight: 700;"></span>
                  </div>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                  <button type="button" onclick="markAllNotifsRead()" class="ts-glass-btn-sm" title="Mark all read" style="background: rgba(56,189,248,0.12); border: 1px solid rgba(56,189,248,0.3); color: #38bdf8; font-size: 0.72rem; padding: 0.25rem 0.6rem; border-radius: 6px; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem;">
                    <i data-lucide="check-check" style="width: 12px; height: 12px;"></i> Mark all read
                  </button>
                </div>
              </div>

              <!-- Filter Tabs Strip -->
              <div class="notif-dropdown-filter-strip" style="display: flex; gap: 0.35rem; padding: 0.5rem 0.85rem; border-bottom: 1px solid var(--ts-border-subtle); overflow-x: auto; scrollbar-width: none;">
                <button type="button" onclick="switchDropdownFilter('all', this)" class="notif-dd-tab active" style="padding: 0.25rem 0.65rem; font-size: 0.72rem; border-radius: 999px; cursor: pointer; font-weight: 600; white-space: nowrap;">All</button>
                <button type="button" onclick="switchDropdownFilter('unread', this)" class="notif-dd-tab" style="padding: 0.25rem 0.65rem; font-size: 0.72rem; border-radius: 999px; cursor: pointer; font-weight: 600; white-space: nowrap;">Unread</button>
                <button type="button" onclick="switchDropdownFilter('replies', this)" class="notif-dd-tab" style="padding: 0.25rem 0.65rem; font-size: 0.72rem; border-radius: 999px; cursor: pointer; font-weight: 600; white-space: nowrap;">Replies</button>
                <button type="button" onclick="switchDropdownFilter('system', this)" class="notif-dd-tab" style="padding: 0.25rem 0.65rem; font-size: 0.72rem; border-radius: 999px; cursor: pointer; font-weight: 600; white-space: nowrap;">System</button>
                <button type="button" onclick="switchDropdownFilter('rewards', this)" class="notif-dd-tab" style="padding: 0.25rem 0.65rem; font-size: 0.72rem; border-radius: 999px; cursor: pointer; font-weight: 600; white-space: nowrap;">Rewards</button>
              </div>

              <!-- Notifications Items Container -->
              <div id="notif-items-container" style="max-height: 380px; overflow-y: auto; display: flex; flex-direction: column; scrollbar-width: thin;">
                <div style="text-align: center; padding: 2.5rem 1rem; color: #94a3b8; font-size: 0.85rem;">
                  <div style="display: inline-block; width: 20px; height: 20px; border: 2px solid rgba(56,189,248,0.3); border-top-color: #38bdf8; border-radius: 50%; animation: tsSpin 0.8s linear infinite; margin-bottom: 0.5rem;"></div>
                  <div>Loading live alerts...</div>
                </div>
              </div>

              <!-- Footer Bar -->
              <div class="notif-dropdown-footer" style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1.15rem; border-top: 1px solid var(--ts-border-subtle);">
                <span id="notif-dd-total-count" style="font-size: 0.74rem; color: #64748b;">Live Stream</span>
                <a href="<?= url('/notifications') ?>" style="font-size: 0.8rem; color: #38bdf8; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem;">
                  Full Notification Roster &rarr;
                </a>
              </div>
            </div>
          </div>

          <!-- Profile Dropdown Trigger -->
          <div class="ts-profile-menu-wrap" id="user-profile-dropdown-wrap">
            <button class="ts-profile-avatar-btn" id="user-profile-menu-btn" aria-expanded="false" title="<?= htmlspecialchars($user['display_name'] ?? $user['username'] ?? 'User') ?>">
              <?php if (!empty($user['author_avatar'] ?? $user['avatar_url'] ?? '')): ?>
                <img src="<?= htmlspecialchars(image_url($user['author_avatar'] ?? $user['avatar_url'] ?? '')) ?>" alt="Avatar" class="ts-avatar-img">
              <?php else: ?>
                <span class="ts-avatar-initials">
                  <?= strtoupper(substr($user['display_name'] ?? $user['username'] ?? 'U', 0, 2)) ?>
                </span>
              <?php endif; ?>
              <span class="ts-avatar-badge-dot"></span>
              <i data-lucide="chevron-down" class="ts-avatar-chevron" style="width: 14px; height: 14px; margin-left: 2px;"></i>
            </button>

            <!-- Profile Dropdown Popup -->
            <div class="ts-profile-dropdown-menu" id="user-profile-dropdown">
              <div class="ts-dropdown-header">
                <div class="ts-dropdown-user-info">
                  <strong><?= htmlspecialchars($user['display_name'] ?? $user['username'] ?? 'User') ?></strong>
                  <span>@<?= htmlspecialchars($user['username'] ?? 'user') ?></span>
                </div>
                <span class="ts-badge ts-badge-cyan" style="font-size: 0.68rem; padding: 0.15rem 0.5rem;">
                  <?= strtoupper($user['role'] ?? 'MEMBER') ?>
                </span>
              </div>

              <div class="ts-dropdown-divider"></div>

              <?php if (Auth::isAdmin()): ?>
                <a href="<?= url('/admin/dashboard') ?>" class="ts-dropdown-item admin-link">
                  <i data-lucide="shield-alert"></i>
                  <span>Admin Command Center</span>
                </a>
                <a href="<?= url('/admin/moderation') ?>" class="ts-dropdown-item">
                  <i data-lucide="shield"></i>
                  <span>Moderation Station</span>
                </a>
                <a href="<?= url('/admin/tickets') ?>" class="ts-dropdown-item">
                  <i data-lucide="life-buoy"></i>
                  <span>Support Tickets Queue</span>
                </a>
                <div class="ts-dropdown-divider"></div>
              <?php endif; ?>

              <?php if (Auth::isAuthor()): ?>
                <a href="<?= url('/author/editor') ?>" class="ts-dropdown-item" style="color: #38bdf8; font-weight: 700;">
                  <i data-lucide="pen-tool" style="color: #38bdf8;"></i>
                  <span>New Technical Post</span>
                </a>
                <a href="<?= url('/author/dashboard') ?>" class="ts-dropdown-item">
                  <i data-lucide="layout-dashboard"></i>
                  <span>Creator Studio</span>
                </a>

                <a href="<?= url('/author/analytics') ?>" class="ts-dropdown-item">
                  <i data-lucide="bar-chart-2"></i>
                  <span>Telemetry &amp; Views</span>
                </a>
              <?php else: ?>
                <?php $appStat = Auth::authorApplicationStatus(); ?>
                <?php if ($appStat === 'rejected'): ?>
                  <a href="<?= url('/author/apply') ?>" class="ts-dropdown-item" style="color: #f59e0b; background: rgba(245,158,11,0.08);">
                    <i data-lucide="alert-triangle" style="color: #f59e0b;"></i>
                    <span>Author Application: Revisions Needed ⚠️</span>
                  </a>
                <?php elseif ($appStat === 'pending'): ?>
                  <a href="<?= url('/author/apply') ?>" class="ts-dropdown-item" style="color: #38bdf8; background: rgba(56,189,248,0.08);">
                    <i data-lucide="clock" style="color: #38bdf8;"></i>
                    <span>Application Under Review ⏳</span>
                  </a>
                <?php else: ?>
                  <a href="<?= url('/author/apply') ?>" class="ts-dropdown-item" style="color: #f472b6; font-weight: 700;">
                    <i data-lucide="sparkles" style="color: #f472b6;"></i>
                    <span>Apply to Become an Author ✨</span>
                  </a>
                <?php endif; ?>
              <?php endif; ?>

              <!-- User Standard Actions (Ordered from Most Used to Least Used) -->
              <a href="<?= url('/bookmarks') ?>" class="ts-dropdown-item" style="color: #38bdf8;">
                <i data-lucide="bookmark" style="color: #38bdf8;"></i>
                <span>Saved Bookmarks 🔖</span>
              </a>

              <a href="<?= url('/history') ?>" class="ts-dropdown-item">
                <i data-lucide="history"></i>
                <span>Reading History 🕒</span>
              </a>

              <a href="<?= url('/inbox') ?>" class="ts-dropdown-item">
                <i data-lucide="inbox"></i>
                <span>Communications &amp; Inbox 📥</span>
              </a>

              <a href="<?= url('/notifications') ?>" class="ts-dropdown-item">
                <i data-lucide="bell"></i>
                <span>Notifications Center</span>
              </a>

              <a href="<?= url('/support') ?>" class="ts-dropdown-item">
                <i data-lucide="life-buoy"></i>
                <span>Support &amp; Helpdesk</span>
              </a>

              <a href="<?= url('/categories') ?>" class="ts-dropdown-item">
                <i data-lucide="sparkles" style="color: #ec4899;"></i>
                <span>Explore Categories &amp; Follow 🌟</span>
              </a>

              <?php if (Auth::isAuthor()): ?>
                <a href="<?= url('/author/dashboard') ?>#profileModal" onclick="if(window.location.pathname.includes('/author/dashboard')){ openProfileModal(); return false; }" class="ts-dropdown-item">
                  <i data-lucide="user-cog"></i>
                  <span>Edit Creator Profile ⚙️</span>
                </a>
              <?php else: ?>
                <a href="<?= url('/account/profile') ?>" class="ts-dropdown-item">
                  <i data-lucide="user"></i>
                  <span>My Profile 👤</span>
                </a>
              <?php endif; ?>

              <a href="<?= url('/account/settings') ?>" class="ts-dropdown-item" style="color: #38bdf8;">
                <i data-lucide="settings" style="color: #38bdf8;"></i>
                <span>Account Settings &amp; Devices 🛡️</span>
              </a>

              <div class="ts-dropdown-divider"></div>

              <a href="<?= url('/auth/logout') ?>" class="ts-dropdown-item logout-link" style="color: #ef4444; font-weight: 700;">
                <i data-lucide="log-out" style="color: #ef4444;"></i>
                <span>Sign Out</span>
              </a>
            </div>
          </div>
        <?php else: ?>
          <a href="<?= url('/auth/login') ?>" class="ts-btn ts-btn-outline ts-btn-sm">Sign In</a>
          <a href="<?= url('/auth/register') ?>" class="ts-btn ts-btn-primary ts-btn-sm desktop-only-flex">Join</a>
        <?php endif; ?>
      </div>
  </header>

  <?php if (!empty($siteSettings['ad_slot_top'])): ?>
    <div class="container" style="margin-top: 1rem; margin-bottom: 0.5rem; text-align: center; overflow: hidden;">
      <?= $siteSettings['ad_slot_top'] ?>
    </div>
  <?php endif; ?>

  <!-- Collapsible Mobile Sidebar Drawer -->
  <div id="mobile-drawer-backdrop" class="ts-mobile-backdrop"></div>
  <aside id="mobile-drawer" class="ts-mobile-drawer">
    <div class="mobile-drawer-header">
      <a href="<?= url('/') ?>" class="ts-logo" style="font-size: 1.25rem;">
        <div class="logo-icon" style="width: 32px; height: 32px;"><i data-lucide="radar"></i></div>
        <span>Tech<span style="color: var(--ts-primary-light);">Scout</span></span>
      </a>
      <button id="mobile-drawer-close" class="ts-btn ts-btn-outline ts-btn-sm" aria-label="Close Navigation">
        <i data-lucide="x" style="width: 18px; height: 18px;"></i>
      </button>
    </div>

    <!-- Quick Search trigger in drawer -->
    <div style="padding: 1rem 1.25rem 0.5rem 1.25rem;">
      <button class="ts-btn ts-btn-outline trigger-search-modal" style="width: 100%; justify-content: flex-start; gap: 0.75rem; color: var(--ts-text-secondary); background: var(--ts-bg-surface-elevated);">
        <i data-lucide="search" style="width: 16px; height: 16px; color: var(--ts-primary);"></i>
        <span>Search anything... (⌘K)</span>
      </button>
    </div>

    <!-- Mobile Nav Links -->
    <nav class="mobile-drawer-nav">
      <div class="mobile-nav-section-title">Explore Intelligence</div>
      <a href="<?= url('/') ?>" class="mobile-nav-link">
        <i data-lucide="home"></i>
        <span>Home Hub</span>
      </a>
      <a href="<?= url('/blog') ?>" class="mobile-nav-link">
        <i data-lucide="file-text"></i>
        <span>Technical Articles</span>
      </a>
      <a href="<?= url('/categories') ?>" class="mobile-nav-link">
        <i data-lucide="layers"></i>
        <span>Categories &amp; Subcategories</span>
      </a>
      <a href="<?= url('/tools') ?>" class="mobile-nav-link">
        <i data-lucide="cpu"></i>
        <span>AI Tools & Radar</span>
      </a>
      <a href="<?= url('/prompts') ?>" class="mobile-nav-link">
        <i data-lucide="terminal"></i>
        <span>Prompt Blueprints</span>
      </a>
      <a href="<?= url('/gpts') ?>" class="mobile-nav-link">
        <i data-lucide="bot"></i>
        <span>Custom GPT Agents</span>
      </a>
      <a href="<?= url('/campaigns') ?>" class="mobile-nav-link">
        <i data-lucide="flag"></i>
        <span>Developer Challenges</span>
      </a>

      <div class="mobile-nav-section-title" style="margin-top: 1.5rem;">User & Workspace</div>
      <?php if ($user): ?>
        <div class="mobile-user-card">
          <div style="display: flex; align-items: center; gap: 0.75rem;">
            <?php if (!empty($user['author_avatar'] ?? $user['avatar_url'] ?? '')): ?>
              <img src="<?= htmlspecialchars(image_url($user['author_avatar'] ?? $user['avatar_url'] ?? '')) ?>" alt="Avatar" class="ts-avatar-img" style="width: 38px; height: 38px;">
            <?php else: ?>
              <span class="ts-avatar-initials" style="width: 38px; height: 38px; font-size: 0.9rem;">
                <?= strtoupper(substr($user['display_name'] ?? $user['username'] ?? 'U', 0, 2)) ?>
              </span>
            <?php endif; ?>
            <div>
              <div style="font-weight: 700; color: var(--ts-text-primary); font-size: 0.95rem;"><?= htmlspecialchars($user['display_name'] ?? $user['username']) ?></div>
              <div style="font-size: 0.75rem; color: var(--ts-text-tertiary);"><?= htmlspecialchars($user['email'] ?? '') ?></div>
            </div>
          </div>
        </div>

        <?php if (Auth::isAdmin()): ?>
          <a href="<?= url('/admin/dashboard') ?>" class="mobile-nav-link" style="color: #f472b6; font-weight: 700;">
            <i data-lucide="shield-alert"></i>
            <span>Admin Command Center</span>
          </a>
        <?php endif; ?>

        <?php if (Auth::isAuthor()): ?>
          <a href="<?= url('/author/editor') ?>" class="mobile-nav-link" style="color: #38bdf8; font-weight: 700;">
            <i data-lucide="pen-tool"></i>
            <span>New Technical Post</span>
          </a>
          <a href="<?= url('/author/dashboard') ?>" class="mobile-nav-link">
            <i data-lucide="layout-dashboard"></i>
            <span>Creator Studio</span>
          </a>

          <a href="<?= url('/author/analytics') ?>" class="mobile-nav-link">
            <i data-lucide="bar-chart-2"></i>
            <span>Telemetry &amp; Views</span>
          </a>
        <?php else: ?>
          <?php $mAppStat = Auth::authorApplicationStatus(); ?>
          <?php if ($mAppStat === 'rejected'): ?>
            <a href="<?= url('/author/apply') ?>" class="mobile-nav-link" style="color: #f59e0b;">
              <i data-lucide="alert-triangle"></i>
              <span>Author Application: Revisions Needed ⚠️</span>
            </a>
          <?php elseif ($mAppStat === 'pending'): ?>
            <a href="<?= url('/author/apply') ?>" class="mobile-nav-link" style="color: #38bdf8;">
              <i data-lucide="clock"></i>
              <span>Application Under Review ⏳</span>
            </a>
          <?php else: ?>
            <a href="<?= url('/author/apply') ?>" class="mobile-nav-link" style="color: #f472b6; font-weight: 700;">
              <i data-lucide="sparkles"></i>
              <span>Apply to Become an Author ✨</span>
            </a>
          <?php endif; ?>
        <?php endif; ?>

        <a href="<?= url('/bookmarks') ?>" class="mobile-nav-link" style="color: #38bdf8;">
          <i data-lucide="bookmark"></i>
          <span>Saved Bookmarks 🔖</span>
        </a>

        <a href="<?= url('/history') ?>" class="mobile-nav-link">
          <i data-lucide="history"></i>
          <span>Reading History 🕒</span>
        </a>

        <a href="<?= url('/inbox') ?>" class="mobile-nav-link">
          <i data-lucide="inbox"></i>
          <span>Communications &amp; Inbox 📥</span>
        </a>

        <a href="<?= url('/notifications') ?>" class="mobile-nav-link">
          <i data-lucide="bell"></i>
          <span>Notifications Center</span>
        </a>

        <a href="<?= url('/support') ?>" class="mobile-nav-link">
          <i data-lucide="life-buoy"></i>
          <span>Support &amp; Helpdesk</span>
        </a>

        <a href="<?= url('/categories') ?>" class="mobile-nav-link">
          <i data-lucide="sparkles" style="color: #ec4899;"></i>
          <span>Explore Categories &amp; Follow 🌟</span>
        </a>

        <?php if (Auth::isAuthor()): ?>
          <a href="<?= url('/author/dashboard') ?>#profileModal" class="mobile-nav-link">
            <i data-lucide="user-cog"></i>
            <span>Edit Creator Profile ⚙️</span>
          </a>
        <?php else: ?>
          <a href="<?= url('/account/profile') ?>" class="mobile-nav-link">
            <i data-lucide="user"></i>
            <span>My Profile 👤</span>
          </a>
        <?php endif; ?>

        <a href="<?= url('/account/settings') ?>" class="mobile-nav-link" style="color: #38bdf8;">
          <i data-lucide="settings"></i>
          <span>Account Settings &amp; Devices 🛡️</span>
        </a>

        <a href="<?= url('/auth/logout') ?>" class="mobile-nav-link" style="color: #ef4444; font-weight: 700; margin-top: 0.5rem;">
          <i data-lucide="log-out"></i>
          <span>Sign Out</span>
        </a>
      <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 0.75rem; padding: 0.5rem 1.25rem;">
          <a href="<?= url('/auth/login') ?>" class="ts-btn ts-btn-outline" style="width: 100%;">Sign In</a>
          <a href="<?= url('/auth/register') ?>" class="ts-btn ts-btn-primary" style="width: 100%;">Join TechScout</a>
        </div>
      <?php endif; ?>

      <div class="mobile-nav-section-title" style="margin-top: 1.5rem;">Platform Info</div>
      <a href="<?= url('/about') ?>" class="mobile-nav-link">
        <i data-lucide="info"></i>
        <span>About Us</span>
      </a>
      <a href="<?= url('/contact') ?>" class="mobile-nav-link">
        <i data-lucide="mail"></i>
        <span>Contact & Support</span>
      </a>
    </nav>
  </aside>

  <!-- Global Instant Search Modal -->
  <div id="search-modal" class="ts-search-modal">
    <div class="search-box-card">
      <div class="search-input-wrap">
        <i data-lucide="search" style="color: var(--ts-primary);"></i>
        <input type="text" id="modal-search-input" placeholder="Search technical articles, AI tools, prompts, GPTs..." autocomplete="off">
        <span style="font-size: 0.75rem; color: var(--ts-text-tertiary); background: var(--ts-bg-surface-elevated); padding: 0.2rem 0.5rem; border-radius: 4px;">ESC to close</span>
      </div>
      <div id="modal-search-results" class="search-results-list">
        <div style="padding: 1.5rem; text-align: center; color: var(--ts-text-tertiary);">
          Type to instantly search across all TechScout modules...
        </div>
      </div>
    </div>
  </div>

  <?php if ($flashSuccess = \App\Core\Session::flash('success')): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashSuccess) ?>, 'success'));</script>
  <?php endif; ?>
  <?php if ($flashError = \App\Core\Session::flash('error')): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashError) ?>, 'error'));</script>
  <?php endif; ?>
  <?php if ($flashInfo = \App\Core\Session::flash('info')): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashInfo) ?>, 'info'));</script>
  <?php endif; ?>

  <script>
  let currentDropdownFilter = 'all';

  async function toggleNotifDropdown() {
    const dd = document.getElementById('notif-dropdown');
    if (!dd) return;
    const isShowing = dd.style.display === 'block';
    dd.style.display = isShowing ? 'none' : 'block';

    if (!isShowing) {
      loadDropdownNotifs(currentDropdownFilter);
    }
  }

  function switchDropdownFilter(filter, btn) {
    currentDropdownFilter = filter;
    document.querySelectorAll('.notif-dd-tab').forEach(el => {
      el.style.border = '1px solid rgba(148,163,184,0.15)';
      el.style.background = 'transparent';
      el.style.color = '#94a3b8';
    });
    if (btn) {
      btn.style.border = '1px solid rgba(99,102,241,0.4)';
      btn.style.background = 'rgba(99,102,241,0.25)';
      btn.style.color = '#f8fafc';
    }
    loadDropdownNotifs(filter);
  }

  async function loadDropdownNotifs(filter = 'all') {
    const container = document.getElementById('notif-items-container');
    if (!container) return;

    try {
      const baseUrl = typeof getTsApiBase === 'function' ? getTsApiBase() : '<?= url('') ?>';
      const filterParam = filter !== 'all' ? `&filter=${encodeURIComponent(filter)}` : '';
      const res = await fetch(`${baseUrl}/api/v1/notifications?per_page=20${filterParam}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      const data = await res.json();

      if (data.success && data.notifications) {
        // Update unread badge in header
        const badge = document.getElementById('notif-badge');
        if (badge) {
          badge.innerText = data.unread_count > 99 ? '99+' : data.unread_count;
          badge.style.display = data.unread_count > 0 ? 'flex' : 'none';
        }

        const unreadPill = document.getElementById('notif-dropdown-unread-pill');
        if (unreadPill) {
          unreadPill.innerText = data.unread_count > 0 ? `(${data.unread_count} unread)` : '';
        }

        const totalLabel = document.getElementById('notif-dd-total-count');
        if (totalLabel) {
          totalLabel.innerText = `${data.total || data.notifications.length} alerts`;
        }

        if (data.notifications.length === 0) {
          container.innerHTML = `
            <div style="text-align: center; padding: 3rem 1.5rem; color: #94a3b8;">
              <div style="width: 44px; height: 44px; border-radius: 50%; background: rgba(148,163,184,0.08); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 0.75rem; color: #64748b;">
                <i data-lucide="bell-off" style="width: 22px; height: 22px;"></i>
              </div>
              <div style="font-size: 0.9rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.2rem;">All Caught Up!</div>
              <div style="font-size: 0.78rem; color: #64748b;">No ${filter !== 'all' ? filter : ''} notifications at this time.</div>
            </div>
          `;
        } else {
          let html = '';
          data.notifications.forEach(n => {
            const isUnread = n.is_read == 0;
            const iconName = n.icon || 'bell';
            const iconColor = n.color || '#38bdf8';
            const typeLabel = n.type_label || 'UPDATE';
            const badgeClass = n.badge_class || 'ts-badge-blue';
            const actionLabel = n.action_label || 'View ↗';
            const relTime = n.relative_time || (n.created_at ? n.created_at.substring(0, 16) : '');

            html += `
              <div id="dd-notif-row-${n.id}" class="notif-dropdown-item ${isUnread ? 'is-unread' : ''}" style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.85rem 1rem; border-bottom: 1px solid var(--ts-border-subtle); background: ${isUnread ? 'rgba(56,189,248,0.06)' : 'transparent'}; border-left: 3px solid ${isUnread ? '#38bdf8' : 'transparent'}; transition: all 0.15s ease; position: relative;">
                
                <!-- Icon Badge with Glow -->
                <div class="notif-icon-box" style="width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--ts-border-subtle); display: flex; align-items: center; justify-content: center; color: ${iconColor}; flex-shrink: 0; margin-top: 2px;">
                  <i data-lucide="${iconName}" style="width: 16px; height: 16px;"></i>
                </div>

                <!-- Text Content -->
                <div style="flex: 1; min-width: 0; cursor: pointer;" onclick="clickNotif(${n.id}, '${n.action_url || ''}')">
                  <div style="display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.2rem; flex-wrap: wrap;">
                    <span class="ts-badge ${badgeClass}" style="font-size: 0.6rem; padding: 0.05rem 0.35rem; font-weight: 700;">${typeLabel}</span>
                    <strong class="notif-item-title" style="font-size: 0.84rem; font-weight: ${isUnread ? '700' : '600'}; line-height: 1.3;">${n.title}</strong>
                    ${isUnread ? '<span style="width: 6px; height: 6px; border-radius: 50%; background: #ec4899; flex-shrink: 0;"></span>' : ''}
                  </div>
                  <div class="notif-item-message" style="font-size: 0.78rem; line-height: 1.4; margin-bottom: 0.35rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">${n.message}</div>
                  <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; font-size: 0.68rem; color: #64748b;">
                    <span>${relTime}</span>
                    ${n.action_url ? `<span style="color: #38bdf8; font-weight: 600;">${actionLabel}</span>` : ''}
                  </div>
                </div>

                <!-- Quick Action Buttons -->
                <div style="display: flex; flex-direction: column; gap: 0.25rem; flex-shrink: 0;" onclick="event.stopPropagation();">
                  <button type="button" onclick="toggleDropdownSingleRead(${n.id})" title="${isUnread ? 'Mark read' : 'Mark unread'}" style="background: rgba(148,163,184,0.08); border: 1px solid var(--ts-border-subtle); border-radius: 4px; padding: 0.2rem; color: ${isUnread ? '#38bdf8' : '#64748b'}; cursor: pointer;">
                    <i data-lucide="${isUnread ? 'check' : 'mail'}" style="width: 12px; height: 12px;"></i>
                  </button>
                  <button type="button" onclick="dismissDropdownNotif(${n.id})" title="Dismiss" style="background: transparent; border: none; padding: 0.2rem; color: #ef4444; cursor: pointer; opacity: 0.6;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'">
                    <i data-lucide="x" style="width: 12px; height: 12px;"></i>
                  </button>
                </div>
              </div>
            `;
          });
          container.innerHTML = html;
        }
      }
    } catch (e) {
      console.error('Load notifs error:', e);
    }
    if (typeof lucide !== 'undefined') lucide.createIcons();
  }

  async function toggleDropdownSingleRead(id) {
    try {
      const baseUrl = typeof getTsApiBase === 'function' ? getTsApiBase() : '<?= url('') ?>';
      const fd = new FormData();
      fd.append('id', id);
      fd.append('toggle', '1');
      await fetch(`${baseUrl}/api/v1/notifications/read`, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      loadDropdownNotifs(currentDropdownFilter);
    } catch (e) {}
  }

  async function dismissDropdownNotif(id) {
    try {
      const baseUrl = typeof getTsApiBase === 'function' ? getTsApiBase() : '<?= url('') ?>';
      const row = document.getElementById(`dd-notif-row-${id}`);
      if (row) {
        row.style.opacity = '0';
        row.style.transform = 'translateX(10px)';
        setTimeout(() => row.remove(), 150);
      }
      await fetch(`${baseUrl}/api/v1/notifications/delete/${id}`, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      loadDropdownNotifs(currentDropdownFilter);
    } catch (e) {}
  }

  async function clickNotif(id, actionUrl) {
    try {
      const baseUrl = typeof getTsApiBase === 'function' ? getTsApiBase() : '<?= url('') ?>';
      const fd = new FormData();
      fd.append('id', id);
      await fetch(`${baseUrl}/api/v1/notifications/read`, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    } catch (e) {}

    if (actionUrl && actionUrl.trim() !== '') {
      let dest = actionUrl.trim();
      // Auto-heal /tech/ or /techscout.n/ path moved to root domain (https://funwithcoding.free.nf)
      try {
        if (dest.startsWith('http://') || dest.startsWith('https://')) {
          const u = new URL(dest);
          let p = u.pathname.replace(/^\/(techscout\.n|tech)(\/|$)/i, '/');
          dest = window.location.origin + p + u.search + u.hash;
        } else {
          let p = dest.replace(/^\/(techscout\.n|tech)(\/|$)/i, '/');
          if (!p.startsWith('/')) p = '/' + p;
          dest = window.location.origin + p;
        }
      } catch(e) {
        dest = window.location.origin + (dest.startsWith('/') ? dest : '/' + dest);
      }
      window.location.href = dest;
    } else {
      window.location.href = '<?= url('/notifications') ?>';
    }
  }

  async function markAllNotifsRead() {
    try {
      const baseUrl = typeof getTsApiBase === 'function' ? getTsApiBase() : '<?= url('') ?>';
      const fd = new FormData();
      fd.append('all', '1');
      fd.append('filter', currentDropdownFilter);
      await fetch(`${baseUrl}/api/v1/notifications/read-all`, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      loadDropdownNotifs(currentDropdownFilter);
    } catch (e) {}
  }

  document.addEventListener('click', (e) => {
    const wrap = document.getElementById('notif-center-wrap');
    const dd = document.getElementById('notif-dropdown');
    if (wrap && dd && !wrap.contains(e.target)) {
      dd.style.display = 'none';
    }
  });
  </script>

  <!-- Display Banner Advertisement: Header Slot -->
  <?= \App\Core\AdRenderer::renderSlot('header_banner') ?>

