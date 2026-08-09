# پُست‌یار — سامانه هوشمند مدیریت کانال‌های تلگرام و بله

> پلتفرم SaaS چندکاربره فارسی برای مدیریت، زمان‌بندی و انتشار پست در کانال‌های تلگرام و بله.

---

## ✅ ویژگی‌های پیاده‌سازی شده

| ویژگی | وضعیت |
|--------|--------|
| مدیریت چندکاناله تلگرام و بله | ✅ |
| زمان‌بندی شمسی پست‌ها | ✅ |
| سیستم اشتراک و سهمیه (پلن‌ها) | ✅ |
| پاسخگوی خودکار کلمات کلیدی | ✅ |
| صندوق پیام (وب‌هوک + Polling) | ✅ |
| نرخ لحظه‌ای طلا | ✅ |
| PWA (نصب روی موبایل/تبلت) | ✅ |
| اپلیکیشن اندروید (WebView) | ✅ |
| سیستم رفرال و زیرمجموعه‌گیری | ✅ |
| کیف پول و کش‌بک | ✅ |
| پیامک (SMS.ir) | ✅ |
| سیستم ایمیل حرفه‌ای (SMTP) | ✅ |
| ردیابی لینک و آمار کلیک | ✅ |
| بازیابی رمز عبور (SMS) | ✅ |
| پشتیبانی از SQLite و MySQL | ✅ |
| پشتیبانی از LiteSpeed و Apache | ✅ |

---

## 📁 ساختار پروژه

```
postyar/
├── .htaccess                 # هدایت ترافیک روت → public/
├── config/
│   ├── .htaccess             # محافظت از پوشه تنظیمات
│   ├── config.example.php    # الگوی تنظیمات (این را کپی کنید)
│   └── config.php            # تنظیمات واقعی (بعد از کپی بسازید)
├── public/                   # ← روت وب سرور (Document Root)
│   ├── .htaccess             # بازنویسی URL + امنیت
│   ├── index.php             # روتر ورودی اصلی
│   ├── manifest.json         # مانیفست PWA
│   ├── service-worker.js     # سرویس ورکر PWA
│   ├── check.php             # فایل تشخیصی (بعد از نصب حذف کنید!)
│   └── assets/
│       ├── css/              # استایل‌های جدا شده
│       ├── js/               # اسکریپت‌های جدا شده + pwa-install.js
│       ├── icons/            # آیکون‌های PWA (8 سایز)
│       ├── images/           # لوگوها (logo.webp, logo-full.webp)
│       ├── uploads/          # آپلود‌های کاربران
│       ├── plans/            # تصاویر پلن‌ها
│       └── receipts/         # رسیدهای پرداخت
├── app/
│   ├── Core/                 # هسته (Bootstrap, Auth, Csrf, Session, Router, Sms, EmailTemplate, Mail)
│   ├── Domain/               # لایه بیزینس (Quota, Sender, Referral, Wallet, LinkTracker, ...)
│   ├── Controllers/          # کنترلرها
│   ├── Views/                # صفحات (home, dashboard, admin, errors)
│   └── Modules/              # ماژول‌ها (AI, AutoResponder, Billing, Channels, Support, ...)
├── storage/
│   ├── db/                   # فایل دیتابیس SQLite
│   └── logs/                 # لاگ‌ها
├── migrations/               # فایل‌های SQL نصب
├── android-webview-app/      # سورس اپلیکیشن اندروید
└── cron.php                  # اجرای زمان‌بندی‌شده
```

---

## 🚀 راهنمای نصب روی هاست اشتراکی (LiteSpeed / Apache)

### روش ۱: دانلود مستقیم از گیت‌هاب (توصیه شده)

1. **دانلود پروژه:**
   ```bash
   git clone https://github.com/taavonchangiz-boop/Postyar-Z.git
   ```
   یا از صفحه گیت‌هاب فایل ZIP را دانلود کنید.

2. **آپلود روی هاست:**
   - تمام فایل‌های دانلود شده را در روت هاست (`public_html/`) آپلود کنید.
   - ساختار نهایی روی هاست باید این‌طور باشد:
     ```
     public_html/          ← Document Root هاست
     ├── .htaccess
     ├── config/
     ├── public/           ← پوشه public پروژه
     ├── app/
     ├── storage/
     └── ...
     ```

3. **تنظیمات دیتابیس:**
   ```bash
   cd public_html/config
   cp config.example.php config.php
   ```
   - فایل `config.php` را باز کنید.
   - برای **SQLite** (پیش‌فرض): هیچ تنظیمی لازم نیست، دیتابیس خودکار ساخته می‌شود.
   - برای **MySQL**: بخش `database.mysql` را پر کنید.
   - یک رشته ۶۴ کاراکتری تصادفی برای `security.salt` بسازید:
     ```php
     // در PHP:
     echo bin2hex(random_bytes(32));
     ```
   - `app.url` را `http://localhost:8000` بگذارید (تشخیص خودکار).

4. **تنظیم دسترسی‌ها:**
   ```bash
   chmod 755 storage/db/
   chmod 755 storage/logs/
   chmod 755 public/assets/uploads/
   chmod 755 public/assets/plans/
   chmod 755 public/assets/receipts/
   ```

5. **حذف فایل تشخیصی (بعد از اطمینان از کارکرد):**
   ```bash
   rm public/check.php
   ```

6. **سایت را باز کنید:** `https://your-domain.com` — سیستم به‌صورت خودکار دیتابیس را می‌سازد.

---

### روش ۲: تغییر Document Root به پوشه public (اختیاری)

اگر هاست شما اجازه تغییر Document Root را می‌دهد:

1. Document Root را از `public_html/` به `public_html/public/` تغییر دهید.
2. در این حالت `.htaccess` روت نیاز نیست.
3. `config.php` → `'url' => 'http://localhost:8000'` (تشخیص خودکار).

---

## 📱 راهنمای نصب PWA روی موبایل

### اندروید:
1. سایت را در Chrome باز کنید.
2. بنر «Add to Home Screen» خودکار نمایش داده می‌شود.
3. یا از منوی مرورگر → «Add to Home Screen» استفاده کنید.

### آیفون (iOS):
1. سایت را در Safari باز کنید.
2. دکمه Share (آیکون مربع با فلش بالا) را بزنید.
3. گزینه «Add to Home Screen» را انتخاب کنید.
4. نام «پُست‌یار» نمایش داده می‌شود.
5. روی «Add» بزنید.

> **توجه:** PWA فقط روی موبایل و تبلت فعال است. روی دسکتاپ هیچ بنری نمایش داده نمی‌شود.

---

## 🤖 ساخت اپلیکیشن اندروید

1. **Android Studio** را باز کنید.
2. `android-webview-app/` را باز کنید.
3. فایل `MainActivity.java` را باز کنید.
4. آدرس سایت خود را تنظیم کنید:
   ```java
   private static final String APP_URL = "https://your-domain.com/";
   ```
5. **Build > Build APK** را بزنید.
6. فایل APK در `app/build/outputs/apk/` ساخته می‌شود.

---

## ⚙️ تنظیمات بعد از نصب

### ساخت ادمین:
پس از ثبت‌ناول در سایت، در دیتابیس نقش کاربر را تغییر دهید:
```sql
UPDATE users SET role = 'superadmin' WHERE email = 'your@email.com';
```

### تنظیم Cron Job:
```bash
* * * * * php /home/your-user/public_html/cron.php >> /dev/null 2>&1
```

### تست‌های یکپارچگی:
```bash
php tests/IntegrationTest.php
```

---

## 🔧 راهنمای عیب‌یابی

### خطای 500:
1. فایل `public/check.php` را باز کنید — تمام بررسی‌ها باید سبز باشند.
2. مطمئن شوید `.htaccess` روت و `public/.htaccess` آپلود شده‌اند.
3. `config/config.php` وجود دارد و فرمت صحیح دارد.
4. دسترسی پوشه‌ها (chmod 755) تنظیم شده.

### آیکون‌ها بارگذاری نمی‌شوند:
- مطمئن شوید `public/assets/icons/` و `public/assets/images/` آپلود شده‌اند.

### PWA نصب نمی‌شود:
- سایت باید **HTTPS** داشته باشد.
- `manifest.json` و `service-worker.js` باید در دسترس باشند.
- Safari (آیفون) حتماً از Share → Add to Home Screen استفاده کنید.

---

## 📋 نیازمندی‌های سرور

- PHP 7.4+
- افزونه‌ها: `pdo`, `pdo_sqlite` (یا `pdo_mysql`), `curl`, `json`, `mbstring`, `openssl`, `session`, `fileinfo`
- `mod_rewrite` فعال (Apache/LiteSpeed)
- HTTPS برای PWA
