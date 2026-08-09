<?php
/**
 * پُست‌یار — ابزار عیب‌یابی و تشخیصی
 *
 * ⚠️  این فایل فقط در محیط development قابل دسترسی است.
 *      در محیط production به صورت خودکار غیرفعال می‌شود.
 *
 * @package WHCM_SaaS
 */

// بلوک دسترسی در محیط production
$config_file = __DIR__ . '/config/config.php';
if (file_exists($config_file)) {
    $cfg = require $config_file;
    $app_env = $cfg['app']['env'] ?? 'production';
} else {
    $app_env = 'production';
}

if ($app_env !== 'development') {
    http_response_code(404);
    exit('این صفحه وجود ندارد.');
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo '<div style="direction: rtl; font-family: Tahoma, Arial, sans-serif; padding: 2rem; background: #0f172a; color: #f1f5f9; border-radius: 16px; max-width: 800px; margin: 2rem auto; box-shadow: 0 10px 25px rgba(0,0,0,0.5); border-top: 5px solid #6366f1;">';
echo '<h1 style="color: #6366f1; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.75rem;">🔍 ابزار عیب‌یابی و تشخیصی پُست‌یار</h1>';

echo '<p style="margin-top: 1rem;"><strong>نسخه پی‌اچ‌پی سرور:</strong> ' . phpversion() . '</p>';
echo '<p><strong>وضعیت فعال بودن PDO:</strong> ' . (class_exists('PDO') ? '<span style="color: #10b981;">فعال ✔</span>' : '<span style="color: #ef4444;">غیرفعال ❌</span>') . '</p>';

if (class_exists('PDO')) {
    echo '<p><strong>درایورهای فعال PDO:</strong> ' . implode(', ', \PDO::getAvailableDrivers()) . '</p>';
}

echo '<h2 style="color: #f59e0b; margin-top: 2rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem;">⚙ بررسی بارگذاری هسته برنامه...</h2>';

try {
    require_once __DIR__ . '/app/Core/Bootstrap.php';
    \WHCM\Core\Bootstrap::run();
    echo '<p style="color: #10b981; font-weight: bold; margin-top: 1rem;">✔ هسته پُست‌یار با موفقیت بارگذاری و اجرا شد و خطایی وجود ندارد!</p>';
    echo '<p>برای ورود به پُست‌یار، روی لینک زیر کلیک کنید:</p>';
    echo '<p><a href="/" style="color: #6366f1; font-weight: bold; text-decoration: none;">🏠 ورود به صفحه اصلی پُست‌یار</a></p>';
} catch (\Throwable $e) {
    echo '<div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); padding: 1.5rem; border-radius: 12px; margin-top: 1rem; color: #fca5a5;">';
    echo '<h3 style="margin-top: 0; color: #ef4444;">❌ بروز خطا در زمان راه‌اندازی:</h3>';
    echo '<p><strong>پیام خطا:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p><strong>در فایل:</strong> ' . htmlspecialchars($e->getFile()) . '</p>';
    echo '<p><strong>در خط شماره:</strong> ' . $e->getLine() . '</p>';
    echo '<h4 style="margin-top: 1rem; color: #ffffff;">ردیابی خطا (Stack Trace):</h4>';
    echo '<pre style="direction: ltr; text-align: left; background: #020617; padding: 1rem; border-radius: 8px; overflow-x: auto; color: #cbd5e1; font-size: 0.85rem;">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</div>';
}

echo '</div>';
