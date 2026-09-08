<?php
// Standard Admin Footer
?>
      </div>
    </main>
  </div>

  <!-- Global Command Palette Modal (Ctrl + K) -->
  <div id="adminCommandPaletteModal" class="admin-command-palette-overlay" onclick="handleCpOverlayClick(event)">
    <div class="admin-command-palette-box">
      <div class="cp-search-header">
        <i data-lucide="search" style="width: 18px; height: 18px; color: #38bdf8; flex-shrink: 0;"></i>
        <input type="text" id="adminCpSearchInput" onkeyup="filterCommandPalette(event)" placeholder="Type a command or jump to page (e.g. users, mail, gpts, audit, database)..." class="cp-search-input" autocomplete="off">
        <button type="button" onclick="closeAdminCommandPalette()" class="admin-modal-close" style="font-size: 1.25rem;">&times;</button>
      </div>

      <div class="cp-results-list" id="adminCpResultsList">
        <!-- 1. Navigation Actions -->
        <a href="<?= url('/admin/dashboard') ?>" class="cp-item cp-selected">
          <span style="display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="layout-dashboard" style="width: 15px; height: 15px; color: #6366f1;"></i> Dashboard Command HUD</span>
          <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem;">CORE</span>
        </a>
        <a href="<?= url('/admin/articles') ?>" class="cp-item">
          <span style="display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="file-text" style="width: 15px; height: 15px; color: #38bdf8;"></i> Articles &amp; News Reviews</span>
          <span class="ts-badge" style="font-size: 0.65rem;">CONTENT</span>
        </a>
        <a href="<?= url('/admin/tools') ?>" class="cp-item">
          <span style="display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="wrench" style="width: 15px; height: 15px; color: #10b981;"></i> AI Tools Directory</span>
          <span class="ts-badge ts-badge-green" style="font-size: 0.65rem;">TOOLS</span>
        </a>
        <a href="<?= url('/admin/gpts') ?>" class="cp-item">
          <span style="display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="bot" style="width: 15px; height: 15px; color: #a855f7;"></i> Custom GPTs Index</span>
          <span class="ts-badge ts-badge-pink" style="font-size: 0.65rem;">GPTS</span>
        </a>
        <a href="<?= url('/admin/prompts') ?>" class="cp-item">
          <span style="display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="terminal" style="width: 15px; height: 15px; color: #f59e0b;"></i> Prompt Starters</span>
          <span class="ts-badge" style="font-size: 0.65rem;">PROMPTS</span>
        </a>
        <a href="<?= url('/admin/content') ?>" class="cp-item">
          <span style="display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="check-square" style="width: 15px; height: 15px; color: #ec4899;"></i> Content Moderation Queue</span>
          <span class="ts-badge ts-badge-pink" style="font-size: 0.65rem;">QUEUE</span>
        </a>
        <a href="<?= url('/admin/users') ?>" class="cp-item">
          <span style="display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="users" style="width: 15px; height: 15px; color: #38bdf8;"></i> User Management &amp; Permissions</span>
          <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem;">USERS</span>
        </a>
        <a href="<?= url('/admin/applications') ?>" class="cp-item">
          <span style="display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="user-check" style="width: 15px; height: 15px; color: #10b981;"></i> Author Applications</span>
          <span class="ts-badge ts-badge-green" style="font-size: 0.65rem;">CREATORS</span>
        </a>
        <a href="<?= url('/admin/tickets') ?>" class="cp-item">
          <span style="display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="life-buoy" style="width: 15px; height: 15px; color: #ef4444;"></i> Support Desk &amp; Tickets</span>
          <span class="ts-badge" style="background: rgba(239,68,68,0.2); color: #ef4444; font-size: 0.65rem;">SUPPORT</span>
        </a>
        <a href="<?= url('/admin/contact-messages') ?>" class="cp-item">
          <span style="display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="inbox" style="width: 15px; height: 15px; color: #38bdf8;"></i> Contact Inquiries</span>
          <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem;">INBOX</span>
        </a>
        <a href="<?= url('/admin/mail') ?>" class="cp-item">
          <span style="display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="mail" style="width: 15px; height: 15px; color: #a855f7;"></i> Mail Studio &amp; Template Center</span>
          <span class="ts-badge ts-badge-pink" style="font-size: 0.65rem;">MAIL</span>
        </a>
        <a href="<?= url('/admin/announcements') ?>" class="cp-item">
          <span style="display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="megaphone" style="width: 15px; height: 15px; color: #f59e0b;"></i> Broadcast Ribbons &amp; Banners</span>
          <span class="ts-badge" style="font-size: 0.65rem;">RIBBONS</span>
        </a>
        <a href="<?= url('/admin/ad-manager') ?>" class="cp-item">
          <span style="display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="badge-dollar-sign" style="width: 15px; height: 15px; color: #10b981;"></i> Ad Manager &amp; Sponsors</span>
          <span class="ts-badge ts-badge-green" style="font-size: 0.65rem;">ADS</span>
        </a>
        <a href="<?= url('/admin/security') ?>" class="cp-item">
          <span style="display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="shield-check" style="width: 15px; height: 15px; color: #ef4444;"></i> Cyber Firewall &amp; WAF Rules</span>
          <span class="ts-badge" style="background: rgba(239,68,68,0.2); color: #ef4444; font-size: 0.65rem;">SECURITY</span>
        </a>
        <a href="<?= url('/admin/webhooks') ?>" class="cp-item">
          <span style="display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="webhook" style="width: 15px; height: 15px; color: #38bdf8;"></i> Webhook Event Integrations</span>
          <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem;">API</span>
        </a>
        <a href="<?= url('/admin/api-keys') ?>" class="cp-item">
          <span style="display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="key" style="width: 15px; height: 15px; color: #f59e0b;"></i> Developer REST API Keys</span>
          <span class="ts-badge" style="font-size: 0.65rem;">KEYS</span>
        </a>
        <a href="<?= url('/admin/db-manager') ?>" class="cp-item">
          <span style="display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="database" style="width: 15px; height: 15px; color: #6366f1;"></i> Database Health &amp; Table Optimizer</span>
          <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem;">SQL</span>
        </a>
        <a href="<?= url('/admin/audit-logs') ?>" class="cp-item">
          <span style="display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="shield" style="width: 15px; height: 15px; color: #38bdf8;"></i> Forensic Audit Telemetry</span>
          <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem;">AUDIT</span>
        </a>
        <a href="<?= url('/admin/system-logs') ?>" class="cp-item">
          <span style="display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="alert-octagon" style="width: 15px; height: 15px; color: #f87171;"></i> System Error Logs &amp; Crash Forensics</span>
          <span class="ts-badge ts-badge-pink" style="font-size: 0.65rem;">ERRORS</span>
        </a>
        <a href="<?= url('/admin/settings') ?>" class="cp-item">
          <span style="display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="server" style="width: 15px; height: 15px; color: #10b981;"></i> System Settings &amp; Diagnostics</span>
          <span class="ts-badge ts-badge-green" style="font-size: 0.65rem;">SYSTEM</span>
        </a>
        <a href="<?= url('/') ?>" class="cp-item" target="_blank">
          <span style="display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="external-link" style="width: 15px; height: 15px; color: #38bdf8;"></i> Launch Public Site ↗</span>
          <span class="ts-badge ts-badge-cyan" style="font-size: 0.65rem;">FRONTEND</span>
        </a>
      </div>

      <div class="cp-footer">
        <div>Navigate with <kbd>↑</kbd> <kbd>↓</kbd> &bull; Open with <kbd>Enter</kbd></div>
        <div>Close with <kbd>Esc</kbd></div>
      </div>
    </div>
  </div>

  <script src="<?= url('/assets/js/main.js') ?>"></script>
  <script>
    // Command Palette Functions
    function openAdminCommandPalette() {
      const modal = document.getElementById('adminCommandPaletteModal');
      const input = document.getElementById('adminCpSearchInput');
      if (modal) {
        modal.style.display = 'flex';
        if (input) {
          input.value = '';
          input.focus();
          filterCommandPalette();
        }
      }
    }

    function closeAdminCommandPalette() {
      const modal = document.getElementById('adminCommandPaletteModal');
      if (modal) modal.style.display = 'none';
    }

    function handleCpOverlayClick(e) {
      if (e.target.id === 'adminCommandPaletteModal') {
        closeAdminCommandPalette();
      }
    }

    function filterCommandPalette(e) {
      const query = (document.getElementById('adminCpSearchInput')?.value || '').toLowerCase();
      const items = Array.from(document.querySelectorAll('#adminCpResultsList .cp-item'));
      let visibleCount = 0;

      if (e && e.key === 'ArrowDown') {
        e.preventDefault();
        navigateCpList(1);
        return;
      } else if (e && e.key === 'ArrowUp') {
        e.preventDefault();
        navigateCpList(-1);
        return;
      } else if (e && e.key === 'Enter') {
        e.preventDefault();
        const selected = document.querySelector('#adminCpResultsList .cp-item.cp-selected');
        if (selected) {
          const href = selected.getAttribute('href');
          closeAdminCommandPalette();
          if (window.pjaxNavigate && href && !href.startsWith('http') && href !== '#') {
            window.pjaxNavigate(href);
          } else {
            window.location.href = href;
          }
        }
        return;
      } else if (e && e.key === 'Escape') {
        closeAdminCommandPalette();
        return;
      }

      items.forEach(item => {
        const text = item.textContent.toLowerCase();
        const matches = text.includes(query);
        item.style.display = matches ? 'flex' : 'none';
        if (matches) visibleCount++;
      });

      // Highlight first visible item
      items.forEach(i => i.classList.remove('cp-selected'));
      const firstVisible = items.find(i => i.style.display !== 'none');
      if (firstVisible) firstVisible.classList.add('cp-selected');
    }

    function navigateCpList(dir) {
      const visibleItems = Array.from(document.querySelectorAll('#adminCpResultsList .cp-item')).filter(i => i.style.display !== 'none');
      if (visibleItems.length === 0) return;
      const currentIndex = visibleItems.findIndex(i => i.classList.contains('cp-selected'));
      let nextIndex = currentIndex + dir;
      if (nextIndex < 0) nextIndex = visibleItems.length - 1;
      if (nextIndex >= visibleItems.length) nextIndex = 0;

      visibleItems.forEach(i => i.classList.remove('cp-selected'));
      visibleItems[nextIndex].classList.add('cp-selected');
      visibleItems[nextIndex].scrollIntoView({ block: 'nearest' });
    }

    // Global Key Listener for Ctrl+K / Cmd+K and Esc
    document.addEventListener('keydown', function(e) {
      if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
        e.preventDefault();
        const modal = document.getElementById('adminCommandPaletteModal');
        if (modal && modal.style.display === 'flex') {
          closeAdminCommandPalette();
        } else {
          openAdminCommandPalette();
        }
      }
      if (e.key === 'Escape') {
        closeAdminCommandPalette();
      }
    });

    // Theme Switcher Sync for Admin Hub
    window.toggleTheme = function() {
      const currentTheme = document.documentElement.getAttribute('data-theme') || localStorage.getItem('ts_theme') || 'dark';
      const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-theme', nextTheme);
      if (document.body) {
        document.body.setAttribute('data-theme', nextTheme);
        if (nextTheme === 'light') {
          document.body.classList.add('theme-light');
          document.documentElement.classList.add('theme-light');
        } else {
          document.body.classList.remove('theme-light');
          document.documentElement.classList.remove('theme-light');
        }
      }
      try { 
        localStorage.setItem('ts_theme', nextTheme); 
        document.cookie = "ts_theme=" + nextTheme + "; path=/; max-age=31536000; SameSite=Lax";
      } catch(e) {}
      syncAdminThemeIcon();
    };

    function syncAdminThemeIcon() {
      const currentTheme = document.documentElement.getAttribute('data-theme') || localStorage.getItem('ts_theme') || 'dark';
      const icon = document.getElementById('adminThemeIcon');
      if (icon) {
        icon.setAttribute('data-lucide', currentTheme === 'light' ? 'sun' : 'moon');
        if (window.lucide) lucide.createIcons();
      }
    }

    // Initialize Sidebar Width & Scroll Position Retention
    function initSidebarState() {
      const shell = document.getElementById('adminShell');
      const icon = document.getElementById('collapseIcon');
      if (shell) {
        if (window.innerWidth > 992) {
          const isCollapsed = localStorage.getItem('admin_sidebar_collapsed') === 'true';
          if (isCollapsed) {
            shell.classList.add('sidebar-collapsed');
            if (icon) icon.setAttribute('data-lucide', 'panel-left-open');
          } else {
            shell.classList.remove('sidebar-collapsed');
            if (icon) icon.setAttribute('data-lucide', 'panel-left-close');
          }
        } else {
          shell.classList.remove('sidebar-collapsed');
          shell.classList.remove('mobile-sidebar-open');
        }
      }

      // Restore Nav Group Accordions State
      document.querySelectorAll('.admin-nav-group').forEach(group => {
        const id = group.getAttribute('id');
        if (id) {
          const savedState = localStorage.getItem('admin_nav_' + id);
          if (savedState !== null) {
            group.open = (savedState === 'open');
          }
          group.addEventListener('toggle', () => {
            localStorage.setItem('admin_nav_' + id, group.open ? 'open' : 'closed');
          });
        }
      });

      // Restore Sidebar Scroll Position
      const nav = document.getElementById('adminNavScrollable');
      if (nav) {
        const savedScroll = sessionStorage.getItem('admin_sidebar_nav_scroll');
        if (savedScroll) {
          nav.scrollTop = parseInt(savedScroll, 10);
        }
        nav.addEventListener('scroll', () => {
          sessionStorage.setItem('admin_sidebar_nav_scroll', nav.scrollTop.toString());
        });
      }

      syncAdminThemeIcon();
      if (window.lucide) lucide.createIcons();
    }

    function toggleAdminSidebar() {
      const shell = document.getElementById('adminShell');
      if (!shell) return;
      if (window.innerWidth <= 992) {
        shell.classList.toggle('mobile-sidebar-open');
      } else {
        const isNowCollapsed = shell.classList.toggle('sidebar-collapsed');
        localStorage.setItem('admin_sidebar_collapsed', isNowCollapsed ? 'true' : 'false');
        const icon = document.getElementById('collapseIcon');
        if (icon) {
          icon.setAttribute('data-lucide', isNowCollapsed ? 'panel-left-open' : 'panel-left-close');
          if (window.lucide) lucide.createIcons();
        }
      }
    }

    // Comprehensive Click Interceptor: Never Scroll to Top on Hash/Action Clicks
    document.addEventListener('click', function(e) {
      const link = e.target.closest('a');
      if (link) {
        const href = link.getAttribute('href');
        if (href === '#' || href === 'javascript:void(0)' || href === 'javascript:;') {
          e.preventDefault();
        }
      }

      const btn = e.target.closest('button');
      if (btn && !btn.getAttribute('type') && !btn.classList.contains('ts-btn-primary')) {
        btn.setAttribute('type', 'button');
      }
    }, { capture: true });

    document.addEventListener('DOMContentLoaded', initSidebarState);

    /* =========================================================================
       HIGH-PERFORMANCE SPA / PJAX SEAMLESS NAVIGATION ENGINE
       ========================================================================= */
    (function() {
      let pjaxActive = false;
      let pjaxProgressTimer = null;

      function startProgress() {
        const bar = document.getElementById('adminPjaxProgressBar');
        if (!bar) return;
        clearTimeout(pjaxProgressTimer);
        bar.style.transition = 'width 0.22s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.2s ease';
        bar.style.opacity = '1';
        bar.style.width = '25%';

        pjaxProgressTimer = setTimeout(() => {
          bar.style.width = '65%';
          pjaxProgressTimer = setTimeout(() => {
            bar.style.width = '85%';
          }, 250);
        }, 120);
      }

      function endProgress() {
        const bar = document.getElementById('adminPjaxProgressBar');
        if (!bar) return;
        clearTimeout(pjaxProgressTimer);
        bar.style.width = '100%';
        pjaxProgressTimer = setTimeout(() => {
          bar.style.opacity = '0';
          setTimeout(() => {
            bar.style.width = '0%';
          }, 200);
        }, 150);
      }

      // Execute script tags in global window scope and prevent duplicate identifier errors
      function executeSwappedScripts(container) {
        const scripts = Array.from(container.querySelectorAll('script'));
        scripts.forEach(oldScript => {
          const parent = oldScript.parentNode;
          const src = oldScript.getAttribute('src');
          let code = oldScript.text || oldScript.textContent || oldScript.innerHTML || '';

          // Remove the inactive script element from the swapped container
          if (parent) {
            parent.removeChild(oldScript);
          }

          if (src) {
            // External script: append to head
            const s = document.createElement('script');
            Array.from(oldScript.attributes).forEach(attr => {
              s.setAttribute(attr.name, attr.value);
            });
            document.head.appendChild(s);
          } else if (code.trim()) {
            // Transform root-level `const ` and `let ` to `var ` so re-navigating to the same page
            // never throws "SyntaxError: Identifier has already been declared"
            let safeCode = code.replace(/(^|[;\n\r])\s*(const|let)\s+([a-zA-Z0-9_$]+)/g, '$1 var $3');
            // Auto-bind function declarations to global window scope so button onclick handlers resolve instantly
            safeCode = safeCode.replace(/(^|[;\n\r])\s*function\s+([a-zA-Z0-9_$]+)\s*\(/g, '$1 var $2 = window.$2 = function $2(');

            try {
              const s = document.createElement('script');
              s.type = 'text/javascript';
              s.appendChild(document.createTextNode(safeCode));
              document.head.appendChild(s);
              document.head.removeChild(s);
            } catch (err) {
              console.warn('Script execution fallback error:', err);
              try {
                (0, eval)(safeCode);
              } catch (evalErr) {
                console.error('Fatal script eval error:', evalErr);
              }
            }
          }
        });
      }

      // Synchronize active sidebar links and expand active section
      function syncActiveNav(targetUrl) {
        try {
          const targetUrlObj = new URL(targetUrl, window.location.origin);
          const targetPath = targetUrlObj.pathname;
          const navLinks = document.querySelectorAll('#adminSidebar .admin-nav-link');
          
          let bestMatch = null;
          let longestMatchLength = 0;

          navLinks.forEach(link => {
            link.classList.remove('active');
            const href = link.getAttribute('href');
            if (!href) return;
            try {
              const linkUrl = new URL(href, window.location.origin);
              const linkPath = linkUrl.pathname;
              if (targetPath === linkPath) {
                bestMatch = link;
                longestMatchLength = linkPath.length + 1000;
              } else if (linkPath !== '/admin' && linkPath !== '/admin/dashboard' && targetPath.startsWith(linkPath)) {
                if (linkPath.length > longestMatchLength) {
                  bestMatch = link;
                  longestMatchLength = linkPath.length;
                }
              }
            } catch(e) {}
          });

          if (bestMatch) {
            bestMatch.classList.add('active');
            const parentGroup = bestMatch.closest('.admin-nav-group');
            if (parentGroup) {
              parentGroup.open = true;
            }
          }
        } catch (e) {}
      }

      // Core PJAX Navigator Function
      window.pjaxNavigate = async function(url, pushState = true) {
        if (pjaxActive) return;
      // Standard Navigation Engine (Full-page reload on screen transitions, async AJAX for same-page actions)
      window.pjaxNavigate = function(url) {
        if (url) window.location.href = url;
      };
    })();
  </script>
  <script src="<?= url('/assets/js/main.js') ?>"></script>
  <script src="<?= url('/assets/js/admin_table_paginator.js') ?>"></script>
</body>
</html>

