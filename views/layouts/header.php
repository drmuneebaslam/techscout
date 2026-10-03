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
          btn.innerHTML = '<i data-lucide="' + (theme === 'light' ? 'sun' : 'moon') + '" class="ts-auto-style-1"></i>';
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
    <div id="ts-impersonation-sticky-bar" class="ts-auto-style-2">
      <div class="ts-auto-style-3">
        <span class="ts-auto-style-4">⚠️</span>
        <span>
          <strong>ADMIN IMPERSONATION MODE:</strong> Viewing platform as 
          <strong class="ts-auto-style-5"><?= htmlspecialchars($impersonatedUser['display_name'] ?? $impersonatedUser['username'] ?? '') ?></strong> 
          (@<?= htmlspecialchars($impersonatedUser['username'] ?? '') ?> &bull; <?= strtoupper($impersonatedUser['role'] ?? 'MEMBER') ?>).
          <span class="ts-auto-style-6">Original Admin: @<?= htmlspecialchars($impAdmin['username'] ?? 'Admin') ?></span>
        </span>
      </div>
      <a href="<?= url('/auth/impersonate/exit') ?>" class="ts-auto-style-7">
        <span>Exit Impersonation Mode &rarr;</span>
      </a>
    </div>
  <?php endif; ?>

  <?php if (!empty($siteSettings['site_maintenance_mode']) && $siteSettings['site_maintenance_mode'] === '1' && (Auth::isAdmin() || !empty($_SESSION['maintenance_bypass']))): ?>
    <div class="maint-admin-indicator-banner">
      <div class="container ts-auto-style-8">
        <span class="ts-auto-style-9">
          <i data-lucide="alert-triangle" class="ts-auto-style-10"></i>
          <span><strong>MAINTENANCE MODE ACTIVE:</strong> Public visitors receive HTTP 503. You are bypassing this as Administrator.</span>
        </span>
        <div class="ts-auto-style-11">
          <a href="<?= url('/admin/settings?tab=general') ?>" class="ts-btn ts-btn-xs ts-btn-outline ts-auto-style-12">⚙️ Settings</a>
          <a href="<?= url('/?preview_503=1') ?>" target="_blank" class="ts-btn ts-btn-xs ts-btn-primary">👁️ Preview 503 Screen ↗</a>
          <a href="<?= url('/?simulate_guest=1') ?>" class="ts-btn ts-btn-xs ts-btn-outline ts-auto-style-13">🔒 Test As Visitor (Lockout)</a>
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
    <div id="site-announcement-<?= $banner['id'] ?>" class="ts-announcement-banner ts-auto-style-14">
      <span><?= htmlspecialchars($banner['message']) ?></span>
      <?php if (!empty($banner['action_url'])): ?>
        <a href="<?= htmlspecialchars($banner['action_url']) ?>" class="ts-auto-style-15">
          <?= htmlspecialchars($banner['action_label'] ?: 'Learn More') ?> &rarr;
        </a>
      <?php endif; ?>
      <button type="button" onclick="dismissBanner(<?= (int)$banner['id'] ?>)" class="ts-auto-style-16" title="Dismiss announcement">&times;</button>
    </div>
  <?php endforeach; ?>

  <!-- Top Navigation Bar -->
  <header class="ts-navbar">
    <div class="nav-inner ts-auto-style-17">
      <div class="ts-auto-style-3">
        <!-- Mobile Sidebar Toggle -->
        <button id="mobile-menu-toggle" class="ts-mobile-menu-btn" aria-label="Toggle Mobile Navigation">
          <i data-lucide="menu" class="ts-auto-style-18"></i>
        </button>

        <!-- Brand Logo -->
        <a href="<?= url('/') ?>" class="ts-logo">
          <div class="logo-icon"><i data-lucide="radar"></i></div>
          <span>Tech<span class="ts-auto-style-19">Scout</span></span>
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
                <span class="ts-badge ts-badge-<?= htmlspecialchars($nav['badge_color'] ?? 'cyan') ?> ts-auto-style-20">
                  <?= htmlspecialchars($nav['badge_text']) ?>
                </span>
              <?php endif; ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>

      <!-- Right Action Items -->
      <div class="ts-auto-style-3">
        <!-- Instant Search Trigger (Cmd+K) -->
        <button class="ts-btn ts-btn-outline ts-btn-sm trigger-search-modal" title="Search (Cmd+K)">
          <i data-lucide="search" class="ts-auto-style-1"></i>
          <span class="desktop-only-text ts-auto-style-21">⌘K</span>
        </button>

        <!-- Theme Toggle -->
        <button id="theme-toggle" type="button" onclick="toggleTheme()" class="ts-btn ts-btn-outline ts-btn-sm" title="Toggle Theme">
          <i data-lucide="moon" class="ts-auto-style-1"></i>
        </button>



        <?php if ($user): ?>
          <?php $unreadNotifCount = \App\Core\NotificationHelper::getUnreadCount((int)$user['id']); ?>
          <!-- In-App Notification Center Trigger -->
          <div class="ts-profile-menu-wrap" id="notif-center-wrap" class="ts-auto-style-22">
            <button class="ts-btn ts-btn-outline ts-btn-sm" id="notif-bell-btn" onclick="toggleNotifDropdown()" title="Notifications" class="ts-auto-style-23">
              <i data-lucide="bell" class="ts-auto-style-24"></i>
              <span id="notif-badge" class="notif-badge-pill ts-auto-style-25">
                <?= $unreadNotifCount > 99 ? '99+' : $unreadNotifCount ?>
              </span>
            </button>

            <!-- Notifications Dropdown Popup with True Glassmorphism -->
            <div id="notif-dropdown" class="ts-glassmorphism-panel ts-auto-style-26">
              
              <!-- Header Bar -->
              <div class="notif-dropdown-header ts-auto-style-27">
                <div class="ts-auto-style-28">
                  <div class="ts-auto-style-29">
                    <i data-lucide="bell" class="ts-auto-style-30"></i>
                  </div>
                  <div>
                    <strong class="ts-auto-style-31">Notifications</strong>
                    <span id="notif-dropdown-unread-pill" class="ts-auto-style-32"></span>
                  </div>
                </div>
                <div class="ts-auto-style-28">
                  <button type="button" onclick="markAllNotifsRead()" class="ts-glass-btn-sm" title="Mark all read" class="ts-auto-style-33">
                    <i data-lucide="check-check" class="ts-auto-style-34"></i> Mark all read
                  </button>
                </div>
              </div>

              <!-- Filter Tabs Strip -->
              <div class="notif-dropdown-filter-strip ts-auto-style-35">
                <button type="button" onclick="switchDropdownFilter('all', this)" class="notif-dd-tab active ts-auto-style-36">All</button>
                <button type="button" onclick="switchDropdownFilter('unread', this)" class="notif-dd-tab ts-auto-style-36">Unread</button>
                <button type="button" onclick="switchDropdownFilter('replies', this)" class="notif-dd-tab ts-auto-style-36">Replies</button>
                <button type="button" onclick="switchDropdownFilter('system', this)" class="notif-dd-tab ts-auto-style-36">System</button>
                <button type="button" onclick="switchDropdownFilter('rewards', this)" class="notif-dd-tab ts-auto-style-36">Rewards</button>
              </div>

              <!-- Notifications Items Container -->
              <div id="notif-items-container" class="ts-auto-style-37">
                <div class="ts-auto-style-38">
                  <div class="ts-auto-style-39"></div>
                  <div>Loading live alerts...</div>
                </div>
              </div>

              <!-- Footer Bar -->
              <div class="notif-dropdown-footer ts-auto-style-40">
                <span id="notif-dd-total-count" class="ts-auto-style-41">Live Stream</span>
                <a href="<?= url('/notifications') ?>" class="ts-auto-style-42">
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
              <i data-lucide="chevron-down" class="ts-avatar-chevron ts-auto-style-43"></i>
            </button>

            <!-- Profile Dropdown Popup -->
            <div class="ts-profile-dropdown-menu" id="user-profile-dropdown">
              <div class="ts-dropdown-header">
                <div class="ts-dropdown-user-info">
                  <strong><?= htmlspecialchars($user['display_name'] ?? $user['username'] ?? 'User') ?></strong>
                  <span>@<?= htmlspecialchars($user['username'] ?? 'user') ?></span>
                </div>
                <span class="ts-badge ts-badge-cyan ts-auto-style-44">
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
                <a href="<?= url('/author/editor') ?>" class="ts-dropdown-item ts-auto-style-45">
                  <i data-lucide="pen-tool" class="ts-auto-style-46"></i>
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
                  <a href="<?= url('/author/apply') ?>" class="ts-dropdown-item ts-auto-style-47">
                    <i data-lucide="alert-triangle" class="ts-auto-style-48"></i>
                    <span>Author Application: Revisions Needed ⚠️</span>
                  </a>
                <?php elseif ($appStat === 'pending'): ?>
                  <a href="<?= url('/author/apply') ?>" class="ts-dropdown-item ts-auto-style-49">
                    <i data-lucide="clock" class="ts-auto-style-46"></i>
                    <span>Application Under Review ⏳</span>
                  </a>
                <?php else: ?>
                  <a href="<?= url('/author/apply') ?>" class="ts-dropdown-item ts-auto-style-50">
                    <i data-lucide="sparkles" class="ts-auto-style-51"></i>
                    <span>Apply to Become an Author ✨</span>
                  </a>
                <?php endif; ?>
              <?php endif; ?>

              <!-- User Standard Actions (Ordered from Most Used to Least Used) -->
              <a href="<?= url('/bookmarks') ?>" class="ts-dropdown-item ts-auto-style-46">
                <i data-lucide="bookmark" class="ts-auto-style-46"></i>
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
                <i data-lucide="sparkles" class="ts-auto-style-52"></i>
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

              <a href="<?= url('/account/settings') ?>" class="ts-dropdown-item ts-auto-style-46">
                <i data-lucide="settings" class="ts-auto-style-46"></i>
                <span>Account Settings &amp; Devices 🛡️</span>
              </a>

              <div class="ts-dropdown-divider"></div>

              <a href="<?= url('/auth/logout') ?>" class="ts-dropdown-item logout-link ts-auto-style-53">
                <i data-lucide="log-out" class="ts-auto-style-54"></i>
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
    <div class="container ts-auto-style-55">
      <?= $siteSettings['ad_slot_top'] ?>
    </div>
  <?php endif; ?>

  <!-- Collapsible Mobile Sidebar Drawer -->
  <div id="mobile-drawer-backdrop" class="ts-mobile-backdrop"></div>
  <aside id="mobile-drawer" class="ts-mobile-drawer">
    <div class="mobile-drawer-header">
      <a href="<?= url('/') ?>" class="ts-logo ts-auto-style-56">
        <div class="logo-icon ts-auto-style-57"><i data-lucide="radar"></i></div>
        <span>Tech<span class="ts-auto-style-19">Scout</span></span>
      </a>
      <button id="mobile-drawer-close" class="ts-btn ts-btn-outline ts-btn-sm" aria-label="Close Navigation">
        <i data-lucide="x" class="ts-auto-style-58"></i>
      </button>
    </div>

    <!-- Quick Search trigger in drawer -->
    <div class="ts-auto-style-59">
      <button class="ts-btn ts-btn-outline trigger-search-modal ts-auto-style-60">
        <i data-lucide="search" class="ts-auto-style-61"></i>
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

      <div class="mobile-nav-section-title ts-auto-style-62">User & Workspace</div>
      <?php if ($user): ?>
        <div class="mobile-user-card">
          <div class="ts-auto-style-3">
            <?php if (!empty($user['author_avatar'] ?? $user['avatar_url'] ?? '')): ?>
              <img src="<?= htmlspecialchars(image_url($user['author_avatar'] ?? $user['avatar_url'] ?? '')) ?>" alt="Avatar" class="ts-avatar-img ts-auto-style-63">
            <?php else: ?>
              <span class="ts-avatar-initials ts-auto-style-64">
                <?= strtoupper(substr($user['display_name'] ?? $user['username'] ?? 'U', 0, 2)) ?>
              </span>
            <?php endif; ?>
            <div>
              <div class="ts-auto-style-65"><?= htmlspecialchars($user['display_name'] ?? $user['username']) ?></div>
              <div class="ts-auto-style-66"><?= htmlspecialchars($user['email'] ?? '') ?></div>
            </div>
          </div>
        </div>

        <?php if (Auth::isAdmin()): ?>
          <a href="<?= url('/admin/dashboard') ?>" class="mobile-nav-link ts-auto-style-50">
            <i data-lucide="shield-alert"></i>
            <span>Admin Command Center</span>
          </a>
        <?php endif; ?>

        <?php if (Auth::isAuthor()): ?>
          <a href="<?= url('/author/editor') ?>" class="mobile-nav-link ts-auto-style-45">
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
            <a href="<?= url('/author/apply') ?>" class="mobile-nav-link ts-auto-style-48">
              <i data-lucide="alert-triangle"></i>
              <span>Author Application: Revisions Needed ⚠️</span>
            </a>
          <?php elseif ($mAppStat === 'pending'): ?>
            <a href="<?= url('/author/apply') ?>" class="mobile-nav-link ts-auto-style-46">
              <i data-lucide="clock"></i>
              <span>Application Under Review ⏳</span>
            </a>
          <?php else: ?>
            <a href="<?= url('/author/apply') ?>" class="mobile-nav-link ts-auto-style-50">
              <i data-lucide="sparkles"></i>
              <span>Apply to Become an Author ✨</span>
            </a>
          <?php endif; ?>
        <?php endif; ?>

        <a href="<?= url('/bookmarks') ?>" class="mobile-nav-link ts-auto-style-46">
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
          <i data-lucide="sparkles" class="ts-auto-style-52"></i>
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

        <a href="<?= url('/account/settings') ?>" class="mobile-nav-link ts-auto-style-46">
          <i data-lucide="settings"></i>
          <span>Account Settings &amp; Devices 🛡️</span>
        </a>

        <a href="<?= url('/auth/logout') ?>" class="mobile-nav-link ts-auto-style-67">
          <i data-lucide="log-out"></i>
          <span>Sign Out</span>
        </a>
      <?php else: ?>
        <div class="ts-auto-style-68">
          <a href="<?= url('/auth/login') ?>" class="ts-btn ts-btn-outline ts-auto-style-69">Sign In</a>
          <a href="<?= url('/auth/register') ?>" class="ts-btn ts-btn-primary ts-auto-style-69">Join TechScout</a>
        </div>
      <?php endif; ?>

      <div class="mobile-nav-section-title ts-auto-style-62">Platform Info</div>
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
        <i data-lucide="search" class="ts-auto-style-70"></i>
        <input type="text" id="modal-search-input" placeholder="Search technical articles, AI tools, prompts, GPTs..." autocomplete="off">
        <span class="ts-auto-style-71">ESC to close</span>
      </div>
      <div id="modal-search-results" class="search-results-list">
        <div class="ts-auto-style-72">
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
            <div class="ts-auto-style-73">
              <div class="ts-auto-style-74">
                <i data-lucide="bell-off" class="ts-auto-style-18"></i>
              </div>
              <div class="ts-auto-style-75">All Caught Up!</div>
              <div class="ts-auto-style-76">No ${filter !== 'all' ? filter : ''} notifications at this time.</div>
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
              <div id="dd-notif-row-${n.id}" class="notif-dropdown-item ${isUnread ? 'is-unread' : ''} ts-auto-style-77">
                
                <!-- Icon Badge with Glow -->
                <div class="notif-icon-box ts-auto-style-78">
                  <i data-lucide="${iconName}" class="ts-auto-style-24"></i>
                </div>

                <!-- Text Content -->
                <div class="ts-auto-style-79" onclick="clickNotif(${n.id}, '${n.action_url || ''}')">
                  <div class="ts-auto-style-80">
                    <span class="ts-badge ${badgeClass} ts-auto-style-81">${typeLabel}</span>
                    <strong class="notif-item-title ts-auto-style-82">${n.title}</strong>
                    ${isUnread ? '<span class="ts-auto-style-83"></span>' : ''}
                  </div>
                  <div class="notif-item-message ts-auto-style-84">${n.message}</div>
                  <div class="ts-auto-style-85">
                    <span>${relTime}</span>
                    ${n.action_url ? `<span class="ts-auto-style-86">${actionLabel}</span>` : ''}
                  </div>
                </div>

                <!-- Quick Action Buttons -->
                <div class="ts-auto-style-87" onclick="event.stopPropagation();">
                  <button type="button" onclick="toggleDropdownSingleRead(${n.id})" title="${isUnread ? 'Mark read' : 'Mark unread'}" class="ts-auto-style-88">
                    <i data-lucide="${isUnread ? 'check' : 'mail'}" class="ts-auto-style-34"></i>
                  </button>
                  <button type="button" onclick="dismissDropdownNotif(${n.id})" title="Dismiss" class="ts-auto-style-89" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'">
                    <i data-lucide="x" class="ts-auto-style-34"></i>
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

