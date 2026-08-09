<?php
/**
 * پیکربندی سامانه مستقل مدیریت هوشمند کانال‌ها (SaaS)
 *
 * ⚠️  هشدار: این فایل حاوی اطلاعات حساس است.
 *      هرگز آن را در گیت‌هاب یا محل عمومی آپلود نکنید.
 *      برای الگو، فایل config.example.php را ببینید.
 *
 * @package WHCM_SaaS
 */

return [
    // تنظیمات عمومی
    'app' => [
        'name' => 'پُست‌یار',
        'url' => 'https://belitia.ir/wh', // آدرس پیش‌فرض (در زمان اجرا بازنویسی می‌شود)
        'locale' => 'fa',
        'timezone' => 'Asia/Tehran',
        'env' => 'production', // 'production' یا 'development'
    ],

    // تنظیمات دیتابیس (پشتیبانی از SQLite و MySQL از طریق PDO)
    'database' => [
        'driver' => 'sqlite', // 'sqlite' یا 'mysql'
        'sqlite' => [
            'path' => __DIR__ . '/../storage/db/whcm_saas.sqlite',
        ],
        'mysql' => [
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'belitia_whcm',
            'username' => 'belitia_whcm',
            'password' => '', // رمز عبور را از فایل .env یا مستقیماً وارد کنید
            'charset' => 'utf8mb4',
        ],
    ],

    // تنظیمات امنیتی
    'security' => [
        'salt' => 'CHANGE_THIS_TO_A_RANDOM_64_CHAR_STRING!',
        'session_lifetime' => 86400, // ۲۴ ساعت به ثانیه
        'trusted_proxies' => [], // لیست IP پروکسی‌های معتبر (برای RateLimit)
        'admin_ip_whitelist' => [], // خالی = بدون محدودیت IP برای ادمین
    ],

    // تنظیمات آپلود
    'upload' => [
        'max_size_mb' => 5,
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
    ],

    // ویژگی‌های پیش‌فرض پلتفرم
    'defaults' => [
        'gold_api_url' => 'https://api.tgju.org/v1/data/sana/home',
        'gold_currency' => 'toman',
    ],

    // تنظیمات SMTP برای ارسال ایمیل
    'mail' => [
        'enabled' => false,
        'host' => 'smtp.example.com',
        'port' => 587,
        'username' => '',
        'password' => '',
        'encryption' => 'tls',
        'from_address' => 'noreply@your-domain.ir',
        'from_name' => 'پُست‌یار',
    ],
];
