<?php
/**
 * اسکریپت Cron Job پُست‌یار
 *
 * این فایل مستقیماً از طریق Cron اجرا می‌شود و شامل:
 *   ۱. بررسی و ارسال خودکار نرخ طلا
 *   ۲. Polling پیام‌های دریافتی (کانال‌های بدون وب‌هوک)
 *   ۳. پاکسازی فایل‌های قدیمی آپلود شده
 *
 * ⚠️  این فایل نباید از طریق وب قابل دسترسی باشد.
 *      فایل .htaccess آن را محافظت می‌کند.
 *
 * Cron: * * * * * php /path/to/cron.php >> /dev/null 2>&1
 *
 * @package WHCM_SaaS
 */

// فقط از طریق CLI اجرا شود
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('دسترسی غیرمجاز.');
}

// بارگذاری Bootstrap
require_once __DIR__ . '/app/Core/Bootstrap.php';

use WHCM\Core\Bootstrap;
use WHCM\Domain\GoldTicker;
use WHCM\Domain\Inbox;

Bootstrap::run();

// ---- ۱. ارسال خودکار نرخ طلا ----
try {
    GoldTicker::tickAll();
} catch (\Throwable $e) {
    error_log('[Postyar Cron] GoldTicker error: ' . $e->getMessage());
}

// ---- ۲. Polling پیام‌های دریافتی ----
try {
    Inbox::pollAllActive();
} catch (\Throwable $e) {
    error_log('[Postyar Cron] Inbox Polling error: ' . $e->getMessage());
}

// ---- ۳. پاکسازی فایل‌های قدیمی ----
try {
    $cleaned = Bootstrap::cleanupOldUploads(30);
    if ($cleaned > 0) {
        error_log('[Postyar Cron] Cleaned ' . $cleaned . ' old upload files.');
    }
} catch (\Throwable $e) {
    error_log('[Postyar Cron] Disk cleanup error: ' . $e->getMessage());
}
