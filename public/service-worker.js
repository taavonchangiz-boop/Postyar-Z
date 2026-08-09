const CACHE_NAME = 'postyar-pwa-v3';

// فقط فایل‌های استاتیک کش می‌شوند — صفحات دینامیک هرگز کش نمی‌شوند
const STATIC_ASSETS = [
  '/manifest.json',
  '/assets/css/admin.css',
  '/assets/css/dashboard.css',
  '/assets/css/home.css',
  '/assets/css/components.css',
  '/assets/js/admin.js',
  '/assets/js/dashboard.js',
  '/assets/js/home.js',
  '/assets/js/utils.js',
  '/assets/js/pwa-install.js',
  '/assets/images/logo.webp',
  '/assets/images/hero_rocket.webp',
  '/assets/icons/icon-192x192.png',
  '/assets/icons/icon-512x512.png',
  '/assets/icons/apple-touch-icon.png',
  '/assets/icons/favicon-32x32.png'
];

// نصب سرویس ورکر و کش فایل‌های استاتیک
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(STATIC_ASSETS))
      .then(() => self.skipWaiting())
  );
});

// فعال‌سازی و پاکسازی کش‌های قدیمی
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames
          .filter(cache => cache !== CACHE_NAME)
          .map(cache => caches.delete(cache))
      );
    }).then(() => self.clients.claim())
  );
});

// استراتژی کش: Cache First برای فایل‌های استاتیک، Network Only برای صفحات دینامیک
self.addEventListener('fetch', event => {
  const url = new URL(event.request.url);
  const isStaticAsset = event.request.method === 'GET' && (
    url.pathname.endsWith('.css') ||
    url.pathname.endsWith('.js') ||
    url.pathname.endsWith('.webp') ||
    url.pathname.endsWith('.png') ||
    url.pathname.endsWith('.jpg') ||
    url.pathname.endsWith('.svg') ||
    url.pathname.endsWith('.woff2') ||
    url.pathname.endsWith('.woff') ||
    url.pathname === '/manifest.json'
  );

  if (isStaticAsset) {
    event.respondWith(
      caches.match(event.request).then(cached => {
        if (cached) return cached;
        return fetch(event.request).then(response => {
          if (response.ok) {
            const clone = response.clone();
            caches.open(CACHE_NAME).then(cache => cache.put(event.request, clone));
          }
          return response;
        });
      })
    );
    return;
  }

  // صفحات HTML و API — همیشه Network
  event.respondWith(
    fetch(event.request).catch(() => {
      if (event.request.mode === 'navigate') {
        return caches.match('/');
      }
      return new Response('آفلاین هستید', { status: 503, statusText: 'Service Unavailable' });
    })
  );
});

// دریافت و نمایش اعلان‌های زنده ارسالی از سمت سرور (Web Push)
self.addEventListener('push', event => {
  let data = { title: 'اعلان جدید', body: 'شما یک پیام جدید در پُست‌یار دارید.' };
  
  if (event.data) {
    try {
      data = event.data.json();
    } catch (e) {
      data = { title: 'اعلان جدید', body: event.data.text() };
    }
  }

  const options = {
    body: data.body,
    icon: '/assets/icons/icon-192x192.png',
    badge: '/assets/icons/icon-192x192.png',
    vibrate: [100, 50, 100],
    data: { url: data.url || '/' }
  };

  event.waitUntil(
    self.registration.showNotification(data.title, options)
  );
});

// باز کردن لینک مربوطه هنگام کلیک روی اعلان موبایل
self.addEventListener('notificationclick', event => {
  event.notification.close();
  event.waitUntil(
    clients.matchAll({ type: 'window' }).then(windowClients => {
      for (const client of windowClients) {
        if (client.url === event.notification.data.url && 'focus' in client) {
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(event.notification.data.url);
      }
    })
  );
});
