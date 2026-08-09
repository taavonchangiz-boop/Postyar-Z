const CACHE_NAME = 'whcm-saas-v1';
const ASSETS_TO_CACHE = [
  '/',
  '/manifest.json',
  '/assets/css/admin.css',
  '/assets/css/dashboard.css',
  '/assets/css/home.css'
];

// نصب سرویس ورکر و کش کردن فایل‌های اولیه
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        return cache.addAll(ASSETS_TO_CACHE);
      })
      .then(() => self.skipWaiting())
  );
});

// فعال‌سازی و پاکسازی کش‌های قدیمی
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cache => {
          if (cache !== CACHE_NAME) {
            return caches.delete(cache);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// استراتژی کش: اول شبکه، در صورت نبود شبکه بازیابی از کش (Network First)
self.addEventListener('fetch', event => {
  event.respondWith(
    fetch(event.request)
      .catch(() => {
        return caches.match(event.request);
      })
  );
});

// دریافت و نمایش اعلان‌های زنده ارسالی از سمت سرور (Web Push)
self.addEventListener('push', event => {
  let data = { title: 'اعلان جدید', body: 'شما یک پیام جدید در پلتفرم مدیریت کانال‌ها دارید.' };
  
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
    data: {
      url: data.url || '/'
    }
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
      for (var i = 0; i < windowClients.length; i++) {
        var client = windowClients[i];
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
