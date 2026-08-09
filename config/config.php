<?php
/**
 * پیکربندی سامانه پُست‌یار
 *
 * ⚠️  این فایل حاوی اطلاعات حساس است.
 *      هرگز آن را در گیت‌هاب آپلود نکنید.
 *      برای الگو، فایل config.example.php را ببینید.
 *
 * @package WHCM_SaaS
 */

return [
    // تنظیمات عمومی
    'app' => [
        'name' => 'پُست‌یار',
        'url' => 'http://localhost:8000', // خالی یا localhost = تشخیص خودکار آدرس از سرور
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
            'database' => '',
            'username' => '',
            'password' => '',
            'charset' => 'utf8mb4',
        ],
    ],

    // تنظیمات امنیتی
    'security' => [
        'salt' => 'CHANGE_THIS_TO_A_RANDOM_64_CHAR_STRING!',
        'session_lifetime' => 86400,
        'trusted_proxies' => [],
        'admin_ip_whitelist' => [],
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

    // تنظیمات پیامک (SMS.ir)
    'sms' => [
        'enabled' => false,
        'provider' => 'smsir',
        'api_key' => '',
        'line_number' => '',
    ],
];
