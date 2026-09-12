// ============================================================================
// TECHSCOUT GLOBAL JAVASCRIPT & INTERACTIONS
// ============================================================================

function getTsApiBase() {
  if (window.location.pathname.startsWith('/techscout.n')) {
    return '/techscout.n';
  }
  if (window.TS_BASE_URL) {
    try {
      if (window.TS_BASE_URL.startsWith('http')) {
        const u = new URL(window.TS_BASE_URL);
        return u.pathname.replace(/\/$/, '');
      }
      return window.TS_BASE_URL.replace(/\/$/, '');
    } catch(e) {}
  }
  return '';
}
window.getTsApiBase = getTsApiBase;

document.addEventListener('DOMContentLoaded', () => {
  // Initialize Lucide Icons
  if (typeof lucide !== 'undefined') {
    lucide.createIcons();
  }

  const baseUrl = getTsApiBase();

  // 1. Theme Toggler (Global window function with universal icon & tab synchronization)
  function syncAllThemeButtons(theme) {
    document.querySelectorAll('#theme-toggle, #adminThemeToggleBtn, .ts-theme-toggle').forEach(btn => {
      const icon = btn.querySelector('i, svg');
      if (icon) {
        icon.setAttribute('data-lucide', theme === 'light' ? 'sun' : 'moon');
      }
    });
    if (typeof lucide !== 'undefined') lucide.createIcons();
  }

  window.toggleTheme = function() {
    const current = document.documentElement.getAttribute('data-theme') || 'dark';
    const next = current === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    if (next === 'light') {
      document.documentElement.classList.add('theme-light');
    } else {
      document.documentElement.classList.remove('theme-light');
    }

    if (document.body) {
      document.body.setAttribute('data-theme', next);
      if (next === 'light') {
        document.body.classList.add('theme-light');
      } else {
        document.body.classList.remove('theme-light');
      }
    }

    try { 
      localStorage.setItem('ts_theme', next);
      document.cookie = "ts_theme=" + next + "; path=/; max-age=31536000; SameSite=Lax";
    } catch(e) {}

    syncAllThemeButtons(next);
    window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: next } }));
  };

  const currentTheme = document.documentElement.getAttribute('data-theme') || (function() {
    try { return localStorage.getItem('ts_theme'); } catch(e) { return null; }
  })() || 'dark';

  if (currentTheme === 'light') {
    document.documentElement.setAttribute('data-theme', 'light');
    document.documentElement.classList.add('theme-light');
    if (document.body) {
      document.body.setAttribute('data-theme', 'light');
      document.body.classList.add('theme-light');
    }
  } else {
    document.documentElement.setAttribute('data-theme', 'dark');
    document.documentElement.classList.remove('theme-light');
    if (document.body) {
      document.body.setAttribute('data-theme', 'dark');
      document.body.classList.remove('theme-light');
    }
  }
  syncAllThemeButtons(currentTheme);

  const themeToggle = document.getElementById('theme-toggle');
  if (themeToggle) {
    themeToggle.addEventListener('click', window.toggleTheme);
  }

  window.addEventListener('storage', (e) => {
    if (e.key === 'ts_theme' && e.newValue) {
      const synctheme = e.newValue === 'light' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-theme', synctheme);
      if (synctheme === 'light') {
        document.documentElement.classList.add('theme-light');
        if (document.body) {
          document.body.setAttribute('data-theme', 'light');
          document.body.classList.add('theme-light');
        }
      } else {
        document.documentElement.classList.remove('theme-light');
        if (document.body) {
          document.body.setAttribute('data-theme', 'dark');
          document.body.classList.remove('theme-light');
        }
      }
      syncAllThemeButtons(synctheme);
      window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: synctheme } }));
    }
  });

  // 1b. Persistent Banner Ribbon Dismissal
  window.dismissBanner = function(bannerId) {
    const el = document.getElementById('site-announcement-' + bannerId);
    if (el) {
      el.style.opacity = '0';
      el.style.transition = 'opacity 0.2s ease, max-height 0.2s ease';
      setTimeout(() => { el.style.display = 'none'; }, 200);
    }
    try {
      localStorage.setItem('ts_dismissed_banner_' + bannerId, Date.now().toString());
    } catch (e) {}
  };

  // Check and hide previously dismissed banners (24h lifespan)
  document.querySelectorAll('.ts-announcement-banner, [id^="site-announcement-"]').forEach((bannerEl) => {
    const id = bannerEl.id.replace('site-announcement-', '');
    try {
      const dismissedTime = localStorage.getItem('ts_dismissed_banner_' + id);
      if (dismissedTime) {
        const elapsed = Date.now() - parseInt(dismissedTime, 10);
        if (elapsed < 24 * 60 * 60 * 1000) {
          bannerEl.style.display = 'none';
        } else {
          localStorage.removeItem('ts_dismissed_banner_' + id);
        }
      }
    } catch (e) {}
  });

  // Auto-open profile or password modal if requested via hash
  if (window.location.hash === '#profileModal' && typeof openProfileModal === 'function') {
    openProfileModal();
  } else if ((window.location.hash === '#changePasswordModal' || window.location.hash === '#passwordModal') && typeof openPasswordModal === 'function') {
    openPasswordModal();
  }

  // 2. User Profile Dropdown Menu
  const profileMenuBtn = document.getElementById('user-profile-menu-btn');
  const profileDropdown = document.getElementById('user-profile-dropdown');
  if (profileMenuBtn && profileDropdown) {
    profileMenuBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      const isActive = profileDropdown.classList.contains('active');
      if (isActive) {
        profileDropdown.classList.remove('active');
        profileMenuBtn.setAttribute('aria-expanded', 'false');
      } else {
        profileDropdown.classList.add('active');
        profileMenuBtn.setAttribute('aria-expanded', 'true');
        if (typeof lucide !== 'undefined') lucide.createIcons();
      }
    });

    document.addEventListener('click', (e) => {
      if (!profileDropdown.contains(e.target) && !profileMenuBtn.contains(e.target)) {
        profileDropdown.classList.remove('active');
        profileMenuBtn.setAttribute('aria-expanded', 'false');
      }
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && profileDropdown.classList.contains('active')) {
        profileDropdown.classList.remove('active');
        profileMenuBtn.setAttribute('aria-expanded', 'false');
      }
    });
  }

  // 3. Mobile Collapsible Sidebar Drawer
  const mobileToggleBtn = document.getElementById('mobile-menu-toggle');
  const mobileDrawer = document.getElementById('mobile-drawer');
  const mobileDrawerBackdrop = document.getElementById('mobile-drawer-backdrop');
  const mobileDrawerClose = document.getElementById('mobile-drawer-close');

  function openMobileDrawer() {
    if (mobileDrawer && mobileDrawerBackdrop) {
      mobileDrawer.classList.add('active');
      mobileDrawerBackdrop.classList.add('active');
      document.body.style.overflow = 'hidden';
      if (typeof lucide !== 'undefined') lucide.createIcons();
    }
  }

  function closeMobileDrawer() {
    if (mobileDrawer && mobileDrawerBackdrop) {
      mobileDrawer.classList.remove('active');
      mobileDrawerBackdrop.classList.remove('active');
      document.body.style.overflow = '';
    }
  }

  if (mobileToggleBtn) {
    mobileToggleBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      openMobileDrawer();
    });
  }

  if (mobileDrawerClose) {
    mobileDrawerClose.addEventListener('click', closeMobileDrawer);
  }

  if (mobileDrawerBackdrop) {
    mobileDrawerBackdrop.addEventListener('click', closeMobileDrawer);
  }

  // 4. Global Instant Search Modal (Cmd+K / Ctrl+K)
  const searchModal = document.getElementById('search-modal');
  const searchInput = document.getElementById('modal-search-input');
  const searchResults = document.getElementById('modal-search-results');
  const openSearchBtns = document.querySelectorAll('.trigger-search-modal');

  function openSearch() {
    closeMobileDrawer();
    if (!searchModal) return;
    searchModal.classList.add('active');
    setTimeout(() => searchInput && searchInput.focus(), 100);
  }

  function closeSearch() {
    if (!searchModal) return;
    searchModal.classList.remove('active');
  }

  openSearchBtns.forEach(btn => btn.addEventListener('click', (e) => {
    e.preventDefault();
    openSearch();
  }));

  document.addEventListener('keydown', (e) => {
    if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
      e.preventDefault();
      if (searchModal && searchModal.classList.contains('active')) {
        closeSearch();
      } else {
        openSearch();
      }
    }
    if (e.key === 'Escape' && searchModal && searchModal.classList.contains('active')) {
      closeSearch();
    }
  });

  if (searchModal) {
    searchModal.addEventListener('click', (e) => {
      if (e.target === searchModal) closeSearch();
    });
  }

  // 5. Instant Autocomplete search fetch
  let debounceTimer;
  if (searchInput && searchResults) {
    searchInput.addEventListener('input', (e) => {
      clearTimeout(debounceTimer);
      const query = e.target.value.trim();
      if (query.length < 2) {
        searchResults.innerHTML = '<div style="padding: 1.5rem; text-align: center; color: var(--ts-text-tertiary);">Type at least 2 characters to search...</div>';
        return;
      }

      debounceTimer = setTimeout(async () => {
        try {
          const fetchUrl = `${baseUrl}/api/v1/search?q=${encodeURIComponent(query)}`;
          const res = await fetch(fetchUrl);
          const data = await res.json();
          if (data.results && data.results.length > 0) {
            searchResults.innerHTML = data.results.map(item => {
              let pathPrefix = 'article';
              if (item.content_type === 'ai_tool') pathPrefix = 'tools';
              else if (item.content_type === 'prompt') pathPrefix = 'prompts';
              else if (item.content_type === 'gpt_listing') pathPrefix = 'gpts';
              else if (item.content_type === 'campaign') pathPrefix = 'campaigns';

              const itemUrl = `${baseUrl}/${pathPrefix}/${encodeURIComponent(item.slug)}`;
              const badgeLabel = item.content_type.replace('_', ' ').toUpperCase();

              return `
                <a href="${itemUrl}" class="search-result-item" style="display: flex; justify-content: space-between; align-items: center; text-decoration: none;">
                  <div>
                    <div style="font-weight: 600; color: var(--ts-text-primary); font-size: 0.95rem;">${escapeHtml(item.title)}</div>
                    <div style="font-size: 0.8rem; color: var(--ts-text-tertiary); margin-top: 2px;">${escapeHtml(item.summary || '')}</div>
                  </div>
                  <span class="ts-badge ts-badge-pink" style="font-size: 0.65rem; margin-left: 0.75rem;">${badgeLabel}</span>
                </a>
              `;
            }).join('');
          } else {
            searchResults.innerHTML = '<div style="padding: 1.5rem; text-align: center; color: var(--ts-text-tertiary);">No matching results found.</div>';
          }
        } catch (err) {
          console.error(err);
        }
      }, 250);
    });
  }
});

// HTML escaping helper
function escapeHtml(str) {
  if (!str) return '';
  return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

// Global Unified Bookmark Toggle Engine
window.toggleBookmark = async function(contentId, btnElement = null) {
  try {
    const baseUrl = getTsApiBase();
    const fd = new FormData();
    fd.append('content_id', contentId);
    const res = await fetch(`${baseUrl}/api/v1/bookmark`, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      credentials: 'same-origin',
      body: fd
    });

    let data;
    const contentType = res.headers.get('content-type') || '';
    if (contentType.includes('application/json')) {
      data = await res.json();
    } else {
      const text = await res.text();
      try {
        data = JSON.parse(text);
      } catch (parseErr) {
        if (res.status === 401) {
          if (typeof showToast === 'function') {
            showToast('Please sign in to save bookmarks.', 'warning');
          }
          return false;
        }
        throw new Error(res.statusText || 'Unable to communicate with server.');
      }
    }

    if (!data.success) {
      if (typeof showToast === 'function') {
        showToast(data.error || 'Please sign in to save bookmarks.', 'warning');
      }
      return false;
    }

    const isBookmarked = data.is_bookmarked;
    const msg = data.message || (isBookmarked ? 'Saved to your private library! 🔖' : 'Bookmark removed.');

    // Update all matching bookmark buttons on page
    document.querySelectorAll(`[data-bookmark-id="${contentId}"], #top-bookmark-btn, #content-bookmark-btn, #article-bottom-bookmark-btn`).forEach(btn => {
      const labelSpan = btn.querySelector('span') || btn;
      if (isBookmarked) {
        btn.classList.remove('ts-btn-outline');
        btn.classList.add('ts-btn-primary');
        btn.style.background = '#ec4899';
        btn.style.borderColor = '#ec4899';
        btn.style.color = '#ffffff';
        if (labelSpan) labelSpan.innerText = labelSpan.innerText.includes('Library') ? '🔖 Saved in Library' : '🔖 Saved';
      } else {
        btn.classList.remove('ts-btn-primary');
        btn.classList.add('ts-btn-outline');
        btn.style.background = 'transparent';
        btn.style.borderColor = 'rgba(236,72,153,0.3)';
        btn.style.color = '#ec4899';
        if (labelSpan) labelSpan.innerText = labelSpan.innerText.includes('Library') ? '🔖 Save Bookmark' : '🔖 Save';
      }
    });

    if (typeof showToast === 'function') {
      showToast(msg, isBookmarked ? 'success' : 'info');
    }
    return isBookmarked;
  } catch (err) {
    console.error('Bookmark error:', err);
    if (typeof showToast === 'function') {
      showToast('Unable to update bookmark. Please try again.', 'error');
    }
    return false;
  }
};

// Global Author Follow/Unfollow Engine
window.toggleAuthorFollow = async function(authorId, btnElement = null) {
  try {
    const baseUrl = getTsApiBase();
    const res = await fetch(`${baseUrl}/api/v1/author/${authorId}/follow`, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      credentials: 'same-origin'
    });

    let data;
    const contentType = res.headers.get('content-type') || '';
    if (contentType.includes('application/json')) {
      data = await res.json();
    } else {
      const text = await res.text();
      try {
        data = JSON.parse(text);
      } catch (parseErr) {
        if (res.status === 401) {
          if (typeof showToast === 'function') {
            showToast('Please sign in to follow authors.', 'warning');
          }
          return false;
        }
        throw new Error(res.statusText || 'Unable to communicate with server.');
      }
    }

    if (!data.success) {
      if (typeof showToast === 'function') {
        showToast(data.error || 'Please sign in to follow authors.', 'warning');
      }
      return false;
    }

    const isFollowing = data.is_following;
    const count = data.follower_count || 0;

    const followBtn = btnElement || document.getElementById('btn-follow-author');
    if (followBtn) {
      const label = document.getElementById('follow-author-label') || followBtn.querySelector('span');
      const counter = document.getElementById('author-follower-count');

      if (isFollowing) {
        followBtn.classList.remove('ts-btn-primary');
        followBtn.classList.add('ts-btn-outline');
        followBtn.style.color = '#38bdf8';
        followBtn.style.borderColor = '#38bdf8';
        followBtn.style.background = 'transparent';
        if (label) label.innerText = 'Following';
      } else {
        followBtn.classList.remove('ts-btn-outline');
        followBtn.classList.add('ts-btn-primary');
        followBtn.style.background = 'linear-gradient(135deg, #6366f1, #38bdf8)';
        followBtn.style.color = '#ffffff';
        followBtn.style.borderColor = 'transparent';
        if (label) label.innerText = 'Follow Author';
      }
      if (counter) {
        counter.innerText = count.toLocaleString();
      }
    }

    if (typeof showToast === 'function') {
      showToast(data.message || (isFollowing ? 'You are now following this author! 🔔' : 'Unfollowed author.'), isFollowing ? 'success' : 'info');
    }
    return isFollowing;
  } catch (err) {
    console.error('Follow error:', err);
    if (typeof showToast === 'function') {
      showToast('Unable to update author follow status. Please try again.', 'error');
    }
    return false;
  }
};

// ============================================================================
// TECHSCOUT BRANDED DIALOG & TOAST MODAL SYSTEM
// ============================================================================

// Minimal Bottom-Center Toast Notification System with Skip Button & Short Duration
window.showToast = function(message, type = 'info', duration = 2200) {
  let toastEl = document.getElementById('ts-bottom-toast');
  if (toastEl) {
    toastEl.remove();
  }

  const icons = {
    success: '✓',
    error: '✕',
    warning: '⚠️',
    info: '⚡'
  };

  const borders = {
    success: 'rgba(16, 185, 129, 0.45)',
    error: 'rgba(239, 68, 68, 0.45)',
    warning: 'rgba(245, 158, 11, 0.45)',
    info: 'rgba(56, 189, 248, 0.45)'
  };

  const colors = {
    success: '#34d399',
    error: '#f87171',
    warning: '#fbbf24',
    info: '#38bdf8'
  };

  toastEl = document.createElement('div');
  toastEl.id = 'ts-bottom-toast';
  toastEl.style.cssText = `
    position: fixed;
    bottom: 24px;
    left: 50%;
    transform: translateX(-50%) translateY(20px);
    background: rgba(10, 15, 29, 0.95);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid ${borders[type] || borders.info};
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.75), 0 0 15px ${borders[type] || borders.info};
    color: #f8fafc;
    border-radius: 9999px;
    padding: 7px 14px 7px 18px;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    z-index: 999999;
    font-size: 0.84rem;
    font-weight: 600;
    max-width: 90vw;
    opacity: 0;
    transition: opacity 0.2s cubic-bezier(0.16, 1, 0.3, 1), transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    pointer-events: auto;
  `;

  toastEl.innerHTML = `
    <span style="color: ${colors[type] || colors.info}; font-size: 0.95rem; line-height: 1; flex-shrink: 0;">${icons[type] || icons.info}</span>
    <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.3;">${message}</span>
    <button type="button" id="ts-toast-skip-btn" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12); color: #94a3b8; font-size: 0.72rem; padding: 2px 8px; border-radius: 9999px; cursor: pointer; transition: all 0.15s ease; margin-left: 4px; flex-shrink: 0;">✕ Skip</button>
  `;

  document.body.appendChild(toastEl);

  requestAnimationFrame(() => {
    toastEl.style.opacity = '1';
    toastEl.style.transform = 'translateX(-50%) translateY(0)';
  });

  const dismiss = () => {
    if (!toastEl || !toastEl.parentNode) return;
    toastEl.style.opacity = '0';
    toastEl.style.transform = 'translateX(-50%) translateY(10px)';
    setTimeout(() => {
      if (toastEl && toastEl.parentNode) toastEl.remove();
    }, 200);
  };

  const skipBtn = toastEl.querySelector('#ts-toast-skip-btn');
  if (skipBtn) {
    skipBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      dismiss();
    });
    skipBtn.addEventListener('mouseenter', () => {
      skipBtn.style.color = '#f8fafc';
      skipBtn.style.background = 'rgba(255,255,255,0.18)';
    });
    skipBtn.addEventListener('mouseleave', () => {
      skipBtn.style.color = '#94a3b8';
      skipBtn.style.background = 'rgba(255,255,255,0.08)';
    });
  }

  const timer = setTimeout(dismiss, duration);

  toastEl.addEventListener('mouseenter', () => clearTimeout(timer));
  toastEl.addEventListener('mouseleave', () => setTimeout(dismiss, 1200));
};

// Branded Modal Dialog System (replaces native alert, confirm, prompt)
window.tsModal = {
  getContainer() {
    let el = document.getElementById('ts-system-modal-wrapper');
    if (!el) {
      el = document.createElement('div');
      el.id = 'ts-system-modal-wrapper';
      el.style.cssText = 'display:none; position:fixed; inset:0; background:rgba(5,7,11,0.85); backdrop-filter:blur(8px); z-index:999999; align-items:center; justify-content:center; padding:1.5rem; animation:fadeIn 0.15s ease;';
      document.body.appendChild(el);
    }
    return el;
  },

  alert(message, title = 'TechScout System Notice') {
    return new Promise((resolve) => {
      const container = this.getContainer();
      container.innerHTML = `
        <div style="background:#090d16; border:1px solid rgba(99,102,241,0.35); border-radius:12px; padding:2rem; width:100%; max-width:480px; box-shadow:0 20px 50px rgba(0,0,0,0.7); text-align:left;">
          <div style="display:flex; align-items:center; gap:0.6rem; margin-bottom:1rem;">
            <div style="width:36px; height:36px; border-radius:8px; background:rgba(99,102,241,0.15); display:flex; align-items:center; justify-content:center; color:#6366f1; font-size:1.2rem;">⚡</div>
            <h3 style="margin:0; font-size:1.15rem; color:#f8fafc;">${escapeHtml(title)}</h3>
          </div>
          <div style="color:#cbd5e1; font-size:0.92rem; line-height:1.6; margin-bottom:1.5rem;">${escapeHtml(message)}</div>
          <div style="display:flex; justify-content:flex-end;">
            <button type="button" id="ts-modal-alert-btn" class="ts-btn ts-btn-primary ts-btn-sm" style="padding:0.5rem 1.5rem; font-weight:700;">Understood</button>
          </div>
        </div>
      `;
      container.style.display = 'flex';
      const btn = document.getElementById('ts-modal-alert-btn');
      btn.focus();
      btn.onclick = () => {
        container.style.display = 'none';
        resolve();
      };
    });
  },

  confirm(message, title = 'Confirm Action') {
    return new Promise((resolve) => {
      const container = this.getContainer();
      container.innerHTML = `
        <div style="background:#090d16; border:1px solid rgba(236,72,153,0.35); border-radius:12px; padding:2rem; width:100%; max-width:480px; box-shadow:0 20px 50px rgba(0,0,0,0.7); text-align:left;">
          <div style="display:flex; align-items:center; gap:0.6rem; margin-bottom:1rem;">
            <div style="width:36px; height:36px; border-radius:8px; background:rgba(236,72,153,0.15); display:flex; align-items:center; justify-content:center; color:#ec4899; font-size:1.2rem;">❓</div>
            <h3 style="margin:0; font-size:1.15rem; color:#f8fafc;">${escapeHtml(title)}</h3>
          </div>
          <div style="color:#cbd5e1; font-size:0.92rem; line-height:1.6; margin-bottom:1.75rem;">${escapeHtml(message)}</div>
          <div style="display:flex; justify-content:flex-end; gap:0.75rem;">
            <button type="button" id="ts-modal-cancel-btn" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
            <button type="button" id="ts-modal-confirm-btn" class="ts-btn ts-btn-primary ts-btn-sm" style="padding:0.5rem 1.25rem; font-weight:700;">Confirm</button>
          </div>
        </div>
      `;
      container.style.display = 'flex';
      const confirmBtn = document.getElementById('ts-modal-confirm-btn');
      const cancelBtn = document.getElementById('ts-modal-cancel-btn');
      confirmBtn.focus();

      confirmBtn.onclick = () => {
        container.style.display = 'none';
        resolve(true);
      };
      cancelBtn.onclick = () => {
        container.style.display = 'none';
        resolve(false);
      };
    });
  },

  prompt(message, defaultValue = '', title = 'Input Required') {
    return new Promise((resolve) => {
      const container = this.getContainer();
      container.innerHTML = `
        <div style="background:#090d16; border:1px solid rgba(56,189,248,0.35); border-radius:12px; padding:2rem; width:100%; max-width:480px; box-shadow:0 20px 50px rgba(0,0,0,0.7); text-align:left;">
          <div style="display:flex; align-items:center; gap:0.6rem; margin-bottom:1rem;">
            <div style="width:36px; height:36px; border-radius:8px; background:rgba(56,189,248,0.15); display:flex; align-items:center; justify-content:center; color:#38bdf8; font-size:1.2rem;">✍️</div>
            <h3 style="margin:0; font-size:1.15rem; color:#f8fafc;">${escapeHtml(title)}</h3>
          </div>
          <div style="color:#cbd5e1; font-size:0.92rem; line-height:1.5; margin-bottom:0.85rem;">${escapeHtml(message)}</div>
          <input type="text" id="ts-modal-prompt-input" value="${escapeHtml(defaultValue)}" style="width:100%; background:#05070b; border:1px solid rgba(148,163,184,0.25); border-radius:6px; padding:0.65rem 1rem; color:#f8fafc; font-size:0.95rem; margin-bottom:1.5rem; outline:none;" />
          <div style="display:flex; justify-content:flex-end; gap:0.75rem;">
            <button type="button" id="ts-modal-prompt-cancel" class="ts-btn ts-btn-outline ts-btn-sm">Cancel</button>
            <button type="button" id="ts-modal-prompt-ok" class="ts-btn ts-btn-primary ts-btn-sm" style="padding:0.5rem 1.25rem; font-weight:700;">Submit</button>
          </div>
        </div>
      `;
      container.style.display = 'flex';
      const input = document.getElementById('ts-modal-prompt-input');
      const okBtn = document.getElementById('ts-modal-prompt-ok');
      const cancelBtn = document.getElementById('ts-modal-prompt-cancel');
      input.focus();
      input.select();

      input.onkeydown = (e) => {
        if (e.key === 'Enter') okBtn.click();
        if (e.key === 'Escape') cancelBtn.click();
      };

      okBtn.onclick = () => {
        const val = input.value;
        container.style.display = 'none';
        resolve(val);
      };
      cancelBtn.onclick = () => {
        container.style.display = 'none';
        resolve(null);
      };
    });
  }
};

// Global 1-Click Topic / Category Subscription Handler
window.toggleTopicSubscription = async function(topicId, btn) {
  const baseUrl = getTsApiBase();
  try {
    const res = await fetch(`${baseUrl}/api/v1/subscriptions/toggle`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ type: 'topic', id: topicId })
    });
    const data = await res.json();
    if (data.error) {
      if (typeof tsModal !== 'undefined') {
        tsModal.alert(data.error, 'Subscription Notice');
      } else {
        showToast(data.error, 'warning');
      }
      return;
    }
    if (data.success) {
      if (data.is_subscribed) {
        btn.classList.remove('ts-btn-outline');
        btn.classList.add('ts-btn-primary');
        btn.innerHTML = `<i data-lucide="check" style="width:11px; height:11px; vertical-align:middle;"></i> <span>Following</span>`;
        showToast('Topic added to your personalized stream! ⚡', 'success');
      } else {
        btn.classList.remove('ts-btn-primary');
        btn.classList.add('ts-btn-outline');
        btn.innerHTML = `<i data-lucide="plus" style="width:11px; height:11px; vertical-align:middle;"></i> <span>Follow Topic</span>`;
        showToast('Topic unfollowed.', 'info');
      }
      if (typeof lucide !== 'undefined') lucide.createIcons();
    }
  } catch (err) {
    showToast('Failed to update subscription', 'error');
  }
};

// Universal Image File Uploader (Anywhere an image upload option is required)
window.uploadImageToInput = async function(fileInput, targetInputId, folder = 'general') {
  if (!fileInput.files || !fileInput.files[0]) return;
  const file = fileInput.files[0];
  const formData = new FormData();
  formData.append('image', file);
  formData.append('folder', folder);

  const target = document.getElementById(targetInputId);
  const origPlaceholder = target ? target.placeholder : '';
  if (target) {
    target.placeholder = 'Uploading image... ⏳';
    target.style.opacity = '0.6';
  }

  const baseUrl = getTsApiBase();
  try {
    const res = await fetch(`${baseUrl}/api/v1/upload/image`, {
      method: 'POST',
      body: formData
    });
    const data = await res.json();
    if (data.success && data.url) {
      if (target) {
        target.value = data.url;
        target.dispatchEvent(new Event('input', { bubbles: true }));
        target.dispatchEvent(new Event('change', { bubbles: true }));
      }
      if (typeof showToast === 'function') {
        showToast('Image uploaded successfully! 📸', 'success');
      } else {
        alert('Image uploaded: ' + data.url);
      }
    } else {
      if (typeof showToast === 'function') {
        showToast(data.error || 'Image upload failed.', 'error');
      } else {
        alert(data.error || 'Image upload failed.');
      }
    }
  } catch (err) {
    if (typeof showToast === 'function') {
      showToast('Image upload network error.', 'error');
    } else {
      alert('Image upload network error: ' + err.message);
    }
  } finally {
    if (target) {
      target.placeholder = origPlaceholder;
      target.style.opacity = '1';
    }
    fileInput.value = '';
  }
};
