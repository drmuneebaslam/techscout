<?php
  // Fetch social links & site settings
  $siteSettings = [];
  try {
      $settingsRows = \App\Core\Database::query("SELECT key_name, key_value FROM site_settings");
      foreach ($settingsRows as $sr) {
          $siteSettings[$sr['key_name']] = $sr['key_value'];
      }
  } catch (\Throwable $e) {}

  // List of standard social channels with defaults
  $availableChannels = [
      'twitter'   => ['name' => 'Twitter / X', 'default_url' => 'https://twitter.com/techscout', 'default_icon' => 'twitter', 'default_color' => '#38bdf8'],
      'github'    => ['name' => 'GitHub', 'default_url' => 'https://github.com/techscout', 'default_icon' => 'github', 'default_color' => '#f8fafc'],
      'youtube'   => ['name' => 'YouTube', 'default_url' => 'https://youtube.com/@techscout', 'default_icon' => 'youtube', 'default_color' => '#ef4444'],
      'facebook'  => ['name' => 'Facebook', 'default_url' => 'https://facebook.com/techscout', 'default_icon' => 'facebook', 'default_color' => '#1877f2'],
      'linkedin'  => ['name' => 'LinkedIn', 'default_url' => 'https://linkedin.com/company/techscout', 'default_icon' => 'linkedin', 'default_color' => '#0a66c2'],
      'discord'   => ['name' => 'Discord', 'default_url' => 'https://discord.gg/techscout', 'default_icon' => 'message-square', 'default_color' => '#5865f2'],
      'telegram'  => ['name' => 'Telegram', 'default_url' => 'https://t.me/techscout', 'default_icon' => 'send', 'default_color' => '#229ed9'],
      'reddit'    => ['name' => 'Reddit', 'default_url' => 'https://reddit.com/r/techscout', 'default_icon' => 'message-circle', 'default_color' => '#ff4500'],
      'instagram' => ['name' => 'Instagram', 'default_url' => '', 'default_icon' => 'instagram', 'default_color' => '#e1306c'],
      'tiktok'    => ['name' => 'TikTok', 'default_url' => '', 'default_icon' => 'video', 'default_color' => '#ff0050'],
      'twitch'    => ['name' => 'Twitch', 'default_url' => '', 'default_icon' => 'tv', 'default_color' => '#9146ff'],
      'rss'       => ['name' => 'RSS Feed', 'default_url' => url('/feed.xml'), 'default_icon' => 'rss', 'default_color' => '#f59e0b']
  ];

  $renderedSocials = [];
  foreach ($availableChannels as $key => $meta) {
      $url = $siteSettings['social_' . $key] ?? $meta['default_url'];
      $enabled = isset($siteSettings['social_enabled_' . $key]) ? ($siteSettings['social_enabled_' . $key] === '1') : (!empty($url));
      $icon = $siteSettings['social_icon_' . $key] ?? $meta['default_icon'];
      $color = $siteSettings['social_color_' . $key] ?? $meta['default_color'];

      if ($enabled && !empty($url)) {
          $renderedSocials[] = [
              'name' => $meta['name'],
              'url' => $url,
              'icon' => $icon,
              'color' => $color
          ];
      }
  }

  // Parse custom links JSON if configured
  $customFooterLinks = [];
  if (!empty($siteSettings['custom_footer_links_json'])) {
      $decoded = json_decode($siteSettings['custom_footer_links_json'], true);
      if (is_array($decoded)) {
          $customFooterLinks = $decoded;
      }
  }
?>

  <!-- Display Banner Advertisement: Footer Slot -->
  <?= \App\Core\AdRenderer::renderSlot('footer_banner') ?>

  <!-- Optional Footer Banner Ad Slot -->
  <?php if (!empty($siteSettings['ad_slot_footer'])): ?>
    <div class="container ts-auto-style-1">
      <?= $siteSettings['ad_slot_footer'] ?>
    </div>
  <?php endif; ?>

  <!-- Footer -->
  <footer class="ts-footer">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-col">
          <a href="<?= url('/') ?>" class="ts-logo ts-auto-style-2">
            <div class="logo-icon"><i data-lucide="radar"></i></div>
            <span>Tech<span class="ts-auto-style-3">Scout</span></span>
          </a>
          <p class="ts-auto-style-4">
            The frontier of modern technology, system design, and AI resources scouted and curated for builders worldwide.
          </p>

          <!-- Dynamic Social Media Channels with Authentic Site Favicons -->
          <div class="footer-social-bar ts-auto-style-5">
            <?php foreach ($renderedSocials as $soc): ?>
              <?php $favUrl = \App\Core\SocialHelper::getFaviconUrl($soc['url']); ?>
              <a href="<?= htmlspecialchars($soc['url']) ?>" target="_blank" rel="noopener noreferrer" class="ts-social-icon-btn" title="<?= htmlspecialchars($soc['name']) ?>" class="ts-auto-style-6">
                <img src="<?= htmlspecialchars($favUrl) ?>" alt="<?= htmlspecialchars($soc['name']) ?>" class="ts-auto-style-7" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                <span class="ts-auto-style-8"><?= \App\Core\SvgHelper::render($soc['icon'] ?? 'globe', 17) ?></span>
              </a>
            <?php endforeach; ?>

            <?php foreach ($customFooterLinks as $cfl): ?>
              <?php $favUrl = \App\Core\SocialHelper::getFaviconUrl($cfl['url'] ?? ''); ?>
              <a href="<?= htmlspecialchars($cfl['url'] ?? '#') ?>" target="_blank" rel="noopener noreferrer" class="ts-social-icon-btn" title="<?= htmlspecialchars($cfl['label'] ?? 'Link') ?>" class="ts-auto-style-9">
                <img src="<?= htmlspecialchars($favUrl) ?>" alt="<?= htmlspecialchars($cfl['label'] ?? 'Link') ?>" class="ts-auto-style-7" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                <span class="ts-auto-style-8"><?= \App\Core\SvgHelper::render($cfl['icon'] ?? 'globe', 17) ?></span>
              </a>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="footer-col">
          <h4>Intelligence</h4>
          <ul class="footer-links">
            <li><a href="<?= url('/blog') ?>">Latest Articles</a></li>
            <li><a href="<?= url('/blog/trending') ?>">Trending Reads</a></li>
            <li><a href="<?= url('/tools') ?>">AI Tools Directory</a></li>
            <li><a href="<?= url('/prompts') ?>">Prompt Library</a></li>
            <li><a href="<?= url('/gpts') ?>">Custom GPTs</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <h4>Creators</h4>
          <ul class="footer-links">
            <li><a href="<?= url('/author/dashboard') ?>">Author Studio</a></li>
            <li><a href="<?= url('/author/rewards') ?>">Creator Rewards</a></li>
            <li><a href="<?= url('/campaigns') ?>">Developer Challenges</a></li>
            <li><a href="<?= url('/author/apply') ?>">Become an Author</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <h4>Platform &amp; Support</h4>
          <ul class="footer-links">
            <li><a href="<?= url('/about') ?>">About TechScout</a></li>
            <li><a href="<?= url('/help') ?>">Help Center &amp; FAQs</a></li>
            <li><a href="<?= url('/support') ?>">Support Desk &amp; Tickets</a></li>
            <li><a href="<?= url('/contact') ?>">Contact Team</a></li>
            <li><a href="<?= url('/editorial') ?>">Editorial Standards</a></li>
            <li><a href="<?= url('/privacy') ?>">Privacy Policy</a></li>
            <li><a href="<?= url('/terms') ?>">Terms of Service</a></li>
            <li><a href="<?= url('/legal') ?>" class="ts-auto-style-10">Legal &amp; Compliance Hub &rarr;</a></li>
          </ul>
        </div>
      </div>

      <div class="ts-auto-style-11">
        <div>&copy; <?= date('Y') ?> TechScout. Built for builders, engineers, and creators.</div>
        <div class="ts-auto-style-12">
          <a href="<?= url('/privacy') ?>" class="ts-auto-style-13">Privacy Policy</a>
          <span>&bull;</span>
          <a href="<?= url('/terms') ?>" class="ts-auto-style-13">Terms of Service</a>
          <span>&bull;</span>
          <a href="<?= url('/help') ?>" class="ts-auto-style-13">Help Center</a>
          <span>&bull;</span>
          <a href="<?= url('/legal') ?>" class="ts-auto-style-13">All Legal Policies</a>
          <span>&bull;</span>
          <a href="<?= url('/security') ?>" class="ts-auto-style-13">Security</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Universal Entity Reporting Component -->
  <?php require __DIR__ . '/../components/report_modal.php'; ?>

  <!-- Scripts -->
  <script src="<?= url('/assets/js/main.js') ?>"></script>
  <script src="<?= url('/assets/js/content-engine.js') ?>"></script>



  <!-- Dynamic Popup Manager Component -->
  <?= class_exists('\\App\\Core\\PopupManager') ? \App\Core\PopupManager::renderActivePopup() : '' ?>

  <!-- Advertisement Click Guard & Throttled Direct Ad Trigger -->
  <?php 
    $isExempt = class_exists('\\App\\Core\\AdRenderer') && \App\Core\AdRenderer::isUserExemptFromAds();
    $popunderUrl = \App\Core\Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'ad_popunder_url'")['key_value'] ?? 'https://omg10.com/4/11737440';
  ?>
  <?php if (!$isExempt && !empty($popunderUrl)): ?>
  <script>
  (function() {
    // Advertisement Click Guard: Strictly prevents card clicks from hijacking user navigation
    // Frequency Throttling: Limits any sponsored popunder to once per user session
    const popunderUrl = <?= json_encode($popunderUrl) ?>;
    
    // Explicit Sponsor / Ad click handler with session frequency cooldown
    window.triggerExplicitSponsoredAd = function(e, customUrl) {
      if (e) {
        e.stopPropagation();
      }
      
      const lastTrigger = sessionStorage.getItem('ts_ad_last_click');
      const now = Date.now();
      // Enforce 15-minute frequency throttling
      if (lastTrigger && (now - parseInt(lastTrigger, 10) < 900000)) {
        return;
      }
      
      const targetUrl = customUrl || popunderUrl;
      if (!targetUrl) return;
      
      sessionStorage.setItem('ts_ad_last_click', now.toString());
      try {
        const adWin = window.open(targetUrl, '_blank');
        if (adWin) {
          try {
            adWin.blur();
            window.focus();
          } catch(err) {}
        }
      } catch(err) {}
    };

    // Attach click listeners exclusively to explicit sponsored tags & ad slots
    document.addEventListener('click', function(e) {
      const sponsoredTag = e.target.closest('.ts-sponsored-badge, .ts-sponsored-tag, [data-ad-trigger="1"]');
      if (sponsoredTag) {
        const adUrl = sponsoredTag.getAttribute('data-ad-url') || popunderUrl;
        window.triggerExplicitSponsoredAd(e, adUrl);
      }
    }, false);
  })();
  </script>
  <?php endif; ?>
</body>
</html>
