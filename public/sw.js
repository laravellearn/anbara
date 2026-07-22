// ─── Anbara Service Worker — PWA Offline Support ───────────────────────────
const CACHE_NAME = 'anbara-v1';
const STATIC_ASSETS = [
  '/',
  '/dashboard',
  '/manifest.json',
  '/css/app.css',
  '/js/app.js',
  '/frest/css/core.css',
  '/frest/js/core.js',
];

// ─── نصب: کش کردن منابع استاتیک ───────────────────────────────────────────
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(STATIC_ASSETS)).catch(() => {})
  );
  self.skipWaiting();
});

// ─── فعال‌سازی: پاک کردن کش‌های قدیمی ─────────────────────────────────────
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
    )
  );
  self.clients.claim();
});

// ─── درخواست: network-first با fallback به کش ──────────────────────────────
self.addEventListener('fetch', event => {
  // درخواست‌های POST، API، و admin را bypass کن
  if (
    event.request.method !== 'GET' ||
    event.request.url.includes('/api/') ||
    event.request.url.includes('/superadmin') ||
    event.request.url.includes('/sanctum/')
  ) {
    return;
  }

  event.respondWith(
    fetch(event.request)
      .then(response => {
        // کش کردن پاسخ موفق
        if (response && response.status === 200 && response.type === 'basic') {
          const cloned = response.clone();
          caches.open(CACHE_NAME).then(cache => cache.put(event.request, cloned));
        }
        return response;
      })
      .catch(() => caches.match(event.request))
  );
});
