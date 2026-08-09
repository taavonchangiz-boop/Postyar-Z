<?php
/**
 * پیکربندی سامانه مستقل مدیریت هوشمند کانال‌ها (SaaS)
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
            'password' => 'Hoomans@8702',
            'charset' => 'utf8mb4',
        ],
    ],

    // تنظیمات امنیتی
    'security' => [
        'salt' => 'WHCM_SUPER_SECURE_SALT_2026_CHANGE_ME_IN_PRODUCTION!',
        'session_lifetime' => 86400, // ۲۴ ساعت به ثانیه
        'trusted_proxies' => [], // لیست IP پروکسی‌های معتبر (برای RateLimit)
    ],

    // ویژگی‌های پیش‌فرض پلتفرم
    'defaults' => [
        'gold_api_url' => 'https://api.tgju.org/v1/data/sana/home', // یک نمونه API فرضی یا واقعی
        'gold_currency' => 'toman',
    ]
];
