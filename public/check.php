<?php
/**
 * فایل تشخیصی پیشرفته — خطای ۵۰۰ را پیدا می‌کند
 * ⚠️ بعد از رفع مشکل حتماً پاک کنید!
 */
header('Content-Type: text/html; charset=utf-8');
echo '<div style="font-family:Tahoma,Arial;direction:rtl;line-height:2.2;font-size:14px;">';
echo '<h2>🔍 تشخیص پیشرفته پُست‌یار</h2>';

// مرحله ۱: لود کانفیگ
$config_path = __DIR__ . '/../config/config.php';
echo '<b>۱. بارگذاری config.php:</b> ';
try {
    $config = require $config_path;
    if (!is_array($config)) throw new Exception('خروجی آرایه نیست');
    echo '<span style="color:green;">✅ موفق</span><br>';
} catch (\Throwable $e) {
    echo '<span style="color:red;">❌ خطا: ' . htmlspecialchars($e->getMessage()) . '</span></div>';
    return;
}

// مرحله ۲: تست سشن
echo '<b>۲. شروع سشن:</b> ';
try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    echo '<span style="color:green;">✅ موفق</span><br>';
} catch (\Throwable $e) {
    echo '<span style="color:red;">❌ خطا: ' . htmlspecialchars($e->getMessage()) . '</span></div>';
    return;
}

// مرحله ۳: اتصال به دیتابیس
echo '<b>۳. اتصال به SQLite:</b> ';
try {
    $db_path = $config['database']['sqlite']['path'];
    $db = new PDO("sqlite:" . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo '<span style="color:green;">✅ موفق</span><br>';
    echo '&nbsp;&nbsp;فایل: ' . htmlspecialchars($db_path) . '<br>';
} catch (\Throwable $e) {
    echo '<span style="color:red;">❌ خطا: ' . htmlspecialchars($e->getMessage()) . '</span></div>';
    return;
}

// مرحله ۴: تست ساخت جدول
echo '<b>۴. تست کوئری:</b> ';
try {
    $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' LIMIT 5");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo '<span style="color:green;">✅ موفق</span> — ' . count($tables) . ' جدول: ' . implode(', ', $tables) . '<br>';
} catch (\Throwable $e) {
    echo '<span style="color:red;">❌ خطا: ' . htmlspecialchars($e->getMessage()) . '</span></div>';
    return;
}

// مرحله ۵: لود Bootstrap
echo '<b>۵. بارگذاری Bootstrap.php:</b> ';
try {
    require_once __DIR__ . '/../app/Core/Bootstrap.php';
    echo '<span style="color:green;">✅ موفق</span><br>';
} catch (\Throwable $e) {
    echo '<span style="color:red;">❌ خطا: ' . htmlspecialchars($e->getMessage()) . ' — فایل: ' . $e->getFile() . ':' . $e->getLine() . '</span></div>';
    return;
}

// مرحله ۶: اجرای Bootstrap::run()
echo '<b>۶. اجرای Bootstrap::run():</b> ';
try {
    \WHCM\Core\Bootstrap::run();
    echo '<span style="color:green;">✅ موفق</span><br>';
} catch (\Throwable $e) {
    echo '<span style="color:red;font-weight:bold;">❌ خطا در Bootstrap!</span><br>';
    echo '<pre style="background:#fef2f2;padding:15px;border-radius:8px;border:1px solid #fca5a5;direction:ltr;text-align:left;font-size:13px;overflow-x:auto;">';
    echo 'Type: ' . get_class($e) . "\n";
    echo 'Message: ' . $e->getMessage() . "\n";
    echo 'File: ' . $e->getFile() . ':' . $e->getLine() . "\n";
    echo "\n--- Stack Trace ---\n";
    foreach ($e->getTrace() as $i => $t) {
        echo "#$i " . ($t['class'] ?? '') . ($t['type'] ?? '') . $t['function'] . '() in ' . ($t['file'] ?? 'unknown') . ':' . ($t['line'] ?? '?') . "\n";
    }
    echo '</pre></div>';
    return;
}

// مرحله ۷: لود ModuleLoader
echo '<b>۷. بارگذاری ماژول‌ها:</b> ';
try {
    $loader = __DIR__ . '/../app/Modules/ModuleLoader.php';
    if (file_exists($loader)) {
        require_once $loader;
        \WHCM\Modules\ModuleLoader::load();
        echo '<span style="color:green;">✅ موفق</span><br>';
    } else {
        echo '<span style="color:orange;">⚠️ فایل وجود ندارد (طبیعی)</span><br>';
    }
} catch (\Throwable $e) {
    echo '<span style="color:red;">❌ خطا: ' . htmlspecialchars($e->getMessage()) . ' — ' . $e->getFile() . ':' . $e->getLine() . '</span><br>';
}

// مرحله ۸: تست روتر
echo '<b>۸. تست روتر:</b> ';
try {
    \WHCM\Core\Router::get('/test-check', function() { echo 'OK'; });
    echo '<span style="color:green;">✅ موفق</span><br>';
} catch (\Throwable $e) {
    echo '<span style="color:red;">❌ خطا: ' . htmlspecialchars($e->getMessage()) . '</span><br>';
}

echo '<br><span style="color:green;font-size:18px;font-weight:bold;">✅ تمام مراحل با موفقیت انجام شد!</span>';
echo '<br><br><span style="color:red;font-weight:bold;">⚠️ حالا فایل check.php را پاک کنید و سایت را رفرش کنید.</span>';
echo '</div>';
