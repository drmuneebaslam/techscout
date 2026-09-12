// TechScout Enterprise Service Worker [TS-UI-05, TS2-UI-01]
// Provides offline caching, background sync resilience, and progressive web app capabilities.

const CACHE_NAME = 'techscout-v2';
const PRECACHE_ASSETS = [
  '/',
  '/favicon.ico'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(PRECACHE_ASSETS).catch(() => {});
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
      );
    }).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  // Only intercept GET requests
  if (event.request.method !== 'GET') return;

  const url = new URL(event.request.url);

  // Strictly only intercept same-origin requests
  if (url.origin !== self.location.origin) return;

  // Never intercept admin panel, API routes, telemetry, ads, or authentication
  if (
    url.pathname.startsWith('/admin') ||
    url.pathname.startsWith('/api/') ||
    url.pathname.startsWith('/auth/') ||
    url.pathname.includes('telemetry') ||
    url.pathname.includes('logout') ||
    url.searchParams.has('i') // InfinityFree cookie validation parameter
  ) {
    return;
  }

  // Stale-While-Revalidate Strategy for HTML and Content
  event.respondWith(
    caches.match(event.request).then((cachedResponse) => {
      const fetchPromise = fetch(event.request)
        .then((networkResponse) => {
          if (networkResponse && networkResponse.status === 200 && networkResponse.type === 'basic') {
            const responseToCache = networkResponse.clone();
            caches.open(CACHE_NAME).then((cache) => {
              cache.put(event.request, responseToCache).catch(() => {});
            });
          }
          return networkResponse;
        })
        .catch(async () => {
          if (cachedResponse) {
            return cachedResponse;
          }
          // If HTML navigation, return clean offline fallback page
          if (event.request.mode === 'navigate') {
            const rootCached = await caches.match('/');
            if (rootCached) return rootCached;

            return new Response(
              '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>TechScout Offline</title><style>body{background:#05070b;color:#f8fafc;font-family:system-ui,-apple-system,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;padding:1.5rem;text-align:center;box-sizing:border-box}.box{background:#0d1527;border:1px solid rgba(56,189,248,0.25);border-radius:16px;padding:2.5rem 2rem;max-width:480px;box-shadow:0 20px 50px rgba(0,0,0,0.5)}h2{color:#38bdf8;margin:0 0 0.75rem 0;font-size:1.5rem}p{color:#94a3b8;font-size:0.95rem;line-height:1.6;margin:0 0 1.75rem 0}.btn{display:inline-block;background:linear-gradient(135deg,#38bdf8,#6366f1);color:#05070b;font-weight:700;text-decoration:none;padding:0.75rem 1.75rem;border-radius:8px;border:none;cursor:pointer;font-size:0.95rem}</style></head><body><div class="box"><h2>⚡ Offline Reading Mode</h2><p>You are currently disconnected from the internet. Your saved and previously loaded prompt blueprints remain available offline.</p><button class="btn" onclick="location.reload()">Retry Connection</button></div></body></html>',
              {
                status: 200,
                headers: { 'Content-Type': 'text/html; charset=utf-8' }
              }
            );
          }
          return new Response('', { status: 503, statusText: 'Offline' });
        });

      return cachedResponse || fetchPromise;
    })
  );
});
