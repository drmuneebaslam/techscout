/**
 * TechScout Admin Modals & Dialogs Engine (Code-Split / Optimized)
 * High-performance, zero-blocking, scoped icon rendering, keyboard-accessible
 */
(function() {
  'use strict';

  var activeModals = [];

  var AdminModal = {
    /**
     * Open a modal dialog with hardware-accelerated class transitions
     * @param {string|HTMLElement} target - ID or DOM element of the modal overlay
     * @param {Object} [options] - Configuration options
     */
    open: function(target, options) {
      options = options || {};
      var el = typeof target === 'string' ? document.getElementById(target) : target;
      if (!el) {
        console.warn('AdminModal.open: Element not found', target);
        return;
      }

      // Ensure base overlay class
      if (!el.classList.contains('ts-modal-overlay')) {
        el.classList.add('ts-modal-overlay');
      }

      // Ensure inline style doesn't block display
      el.style.display = 'flex';

      // Push to active stack if not already there
      if (activeModals.indexOf(el) === -1) {
        activeModals.push(el);
      }

      document.body.classList.add('ts-modal-open');

      // Double-rAF ensures clean CSS transition start
      requestAnimationFrame(function() {
        requestAnimationFrame(function() {
          el.classList.add('is-active');

          // Scoped icon creation - ONLY parse icons inside this modal!
          if (window.lucide && typeof window.lucide.createIcons === 'function') {
            try {
              window.lucide.createIcons({ root: el });
            } catch (iconErr) {
              // Fallback
            }
          }

          // Auto-focus first visible input
          if (options.autofocus !== false) {
            var firstInput = el.querySelector('input:not([type="hidden"]):not([disabled]), textarea:not([disabled]), select:not([disabled])');
            if (firstInput) {
              firstInput.focus();
              if (typeof firstInput.select === 'function' && firstInput.value) {
                firstInput.select();
              }
            }
          }

          if (typeof options.onOpen === 'function') {
            options.onOpen(el);
          }
        });
      });
    },

    /**
     * Close a modal dialog
     * @param {string|HTMLElement} [target] - ID or DOM element; if omitted, closes topmost active modal
     * @param {Object} [options] - Configuration options
     */
    close: function(target, options) {
      options = options || {};
      var el;
      if (target) {
        el = typeof target === 'string' ? document.getElementById(target) : target;
      } else {
        el = activeModals.pop();
      }

      if (!el) return;

      var idx = activeModals.indexOf(el);
      if (idx !== -1) {
        activeModals.splice(idx, 1);
      }

      el.classList.remove('is-active');

      setTimeout(function() {
        if (!el.classList.contains('is-active')) {
          el.style.display = 'none';
        }
        if (activeModals.length === 0) {
          document.body.classList.remove('ts-modal-open');
        }
        if (typeof options.onClose === 'function') {
          options.onClose(el);
        }
      }, 170);
    },

    /**
     * Close all active modals
     */
    closeAll: function() {
      while (activeModals.length > 0) {
        AdminModal.close();
      }
    },

    /**
     * High-speed Tab Switcher for in-page or in-modal views
     * @param {string} containerSelector - Parent container selector containing tab triggers & panels
     * @param {string} activeTabKey - Target data-tab key
     * @param {Object} [options] - URL sync options
     */
    switchTab: function(containerSelector, activeTabKey, options) {
      options = options || {};
      var container = typeof containerSelector === 'string' ? document.querySelector(containerSelector) : containerSelector;
      if (!container) return;

      var tabButtons = container.querySelectorAll('[data-tab-trigger]');
      var tabPanels = container.querySelectorAll('[data-tab-panel]');

      tabButtons.forEach(function(btn) {
        var key = btn.getAttribute('data-tab-trigger');
        if (key === activeTabKey) {
          btn.classList.add('active');
        } else {
          btn.classList.remove('active');
        }
      });

      tabPanels.forEach(function(panel) {
        var key = panel.getAttribute('data-tab-panel');
        if (key === activeTabKey) {
          panel.style.display = '';
          panel.classList.add('active');
        } else {
          panel.style.display = 'none';
          panel.classList.remove('active');
        }
      });

      // Optional URL sync without full reload
      if (options.urlParam) {
        try {
          var url = new URL(window.location.href);
          url.searchParams.set(options.urlParam, activeTabKey);
          window.history.replaceState({}, '', url.toString());
        } catch (e) {}
      }

      if (typeof options.onSwitch === 'function') {
        options.onSwitch(activeTabKey);
      }
    }
  };

  // Keyboard accessibility: Escape key closes topmost modal
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' || e.keyCode === 27) {
      if (activeModals.length > 0) {
        e.preventDefault();
        AdminModal.close();
      }
    }
  });

  // Click outside to close (delegated on document)
  document.addEventListener('click', function(e) {
    if (e.target.classList && e.target.classList.contains('ts-modal-overlay')) {
      AdminModal.close(e.target);
    }
  });

  window.AdminModal = AdminModal;
})();
