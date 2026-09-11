/**
 * TechScout Client-Side JavaScript & Telemetry Error Dispatcher
 * Automatically captures uncaught exceptions, promise rejections, and console errors
 * and forwards them to the server-side forensic logging system.
 */
(function() {
  'use strict';
  const reportedErrors = new Set();

  function getBaseUrl() {
    return window.TS_BASE_URL || '';
  }

  function reportClientError(payload) {
    if (!payload || !payload.message) return;
    
    // Ignore benign browser extension noise or cross-origin script error noise
    const msg = String(payload.message);
    if (msg === 'Script error.' || msg.includes('Extension context invalidated') || msg.includes('ResizeObserver loop limit exceeded')) {
      return;
    }

    const fingerprint = [msg, payload.source, payload.lineno, payload.colno].join('::');
    if (reportedErrors.has(fingerprint)) return;
    reportedErrors.add(fingerprint);

    const endpoint = getBaseUrl() + '/api/v1/telemetry/client-error';
    const jsonStr = JSON.stringify(payload);

    try {
      if (typeof navigator.sendBeacon === 'function') {
        const blob = new Blob([jsonStr], { type: 'application/json' });
        navigator.sendBeacon(endpoint, blob);
      } else {
        fetch(endpoint, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: jsonStr,
          keepalive: true
        }).catch(() => {});
      }
    } catch (e) {
      // Never disrupt client runtime on logging failure
    }
  }

  // Global uncaught JS error listener
  window.addEventListener('error', function(e) {
    if (!e) return;
    reportClientError({
      message: e.message || 'Uncaught Error',
      source: e.filename || window.location.href,
      lineno: e.lineno || 0,
      colno: e.colno || 0,
      stack: e.error && e.error.stack ? e.error.stack : (e.message || ''),
      url: window.location.href,
      screen: `${window.innerWidth}x${window.innerHeight}`
    });
  });

  // Global unhandled promise rejection listener
  window.addEventListener('unhandledrejection', function(e) {
    if (!e) return;
    const reason = e.reason;
    const message = (reason instanceof Error) ? reason.message : (typeof reason === 'string' ? reason : 'Unhandled Promise Rejection');
    const stack = (reason instanceof Error && reason.stack) ? reason.stack : '';
    reportClientError({
      message: message,
      source: window.location.href,
      lineno: 0,
      colno: 0,
      stack: stack,
      url: window.location.href,
      screen: `${window.innerWidth}x${window.innerHeight}`
    });
  });
})();
