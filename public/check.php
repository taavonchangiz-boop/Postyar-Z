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
echo '<b>۳. اتصال به دیتابیس:</b> ';
try {
    $driver = $config['database']['driver'] ?? 'sqlite';
    if ($driver === 'sqlite') {
        $db_path = $config['database']['sqlite']['path'];
        $db = new PDO("sqlite:" . $db_path);
    } else {
        $h = $config['database']['mysql'];
        $db = new PDO("mysql:host={$h['host']};port={$h['port']};dbname={$h['database']};charset={$h['charset']}", $h['username'], $h['password']);
    }
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    echo '<span style="color:green;">✅ موفق</span> (' . $driver . ')<br>';
} catch (\Throwable $e) {
    echo '<span style="color:red;">❌ خطا: ' . htmlspecialchars($e->getMessage()) . '</span></div>';
    return;
}

// مرحله ۴: بررسی جداول
echo '<b>۴. جداول دیتابیس:</b> ';
try {
    if ($driver === 'sqlite') {
        $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
    } else {
        $stmt = $db->query("SELECT TABLE_NAME as name FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME");
    }
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $required = ['users','plans','subscriptions','channels','posts','referrals','wallet_transactions','referral_settings','sms_templates','sms_log','email_templates','email_log','link_tracking','verification_codes','settings','tickets','rate_limits','payments','discount_codes','discount_offers','channel_registry','inbox','auto_replies','push_subscriptions','channel_messages','post_channel_stats','clicks_log'];
    $missing = array_diff($required, $tables);
    if (empty($missing)) {
        echo '<span style="color:green;">✅ ' . count($tables) . ' جدول (همه مورد نیاز وجود دارد)</span><br>';
    } else {
        echo '<span style="color:red;">❌ ' . count($missing) . ' جدول مفقود: ' . implode(', ', $missing) . '</span><br>';
    }
} catch (\Throwable $e) {
    echo '<span style="color:red;">❌ خطا: ' . htmlspecialchars($e->getMessage()) . '</span></div>';
    return;
}

// مرحله ۵: بررسی ستون‌های users
echo '<b>۵. ستون‌های جدول users:</b> ';
try {
    $stmt = $db->query("PRAGMA table_info(users)");
    $cols = $stmt->fetchAll(PDO::FETCH_COLUMN, 1);
    $required_cols = ['id','name','email','password','role','status','phone','referral_code','referred_by','referral_points','wallet_balance'];
    $missing_cols = array_diff($required_cols, $cols);
    if (empty($missing_cols)) {
        echo '<span style="color:green;">✅ همه ستون‌ها وجود دارد (' . count($cols) . ' ستون)</span><br>';
    } else {
        echo '<span style="color:red;">❌ ستون مفقود: ' . implode(', ', $missing_cols) . '</span><br>';
    }
} catch (\Throwable $e) {
    echo '<span style="color:red;">❌ خطا: ' . htmlspecialchars($e->getMessage()) . '</span></div>';
    return;
}

// مرحله ۶: لود و اجرای Bootstrap
echo '<b>۶. بارگذاری و اجرای Bootstrap:</b> ';
try {
    require_once __DIR__ . '/../app/Core/Bootstrap.php';
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

// مرحله ۷: تست لود کلاس‌های ثبت‌نام
echo '<b>۷. تست لود کلاس‌های ثبت‌نام:</b><br>';
$classes_to_test = [
    'WHCM\\Core\\Auth',
    'WHCM\\Domain\\Referral',
    'WHCM\\Domain\\Wallet',
    'WHCM\\Core\\EmailTemplate',
];
foreach ($classes_to_test as $cls) {
    echo '&nbsp;&nbsp;' . $cls . ': ';
    try {
        if (class_exists($cls)) {
            echo '<span style="color:green;">✅</span><br>';
        } else {
            echo '<span style="color:red;">❌ کلاس پیدا نشد</span><br>';
        }
    } catch (\Throwable $e) {
        echo '<span style="color:red;">❌ ' . htmlspecialchars($e->getMessage()) . '</span><br>';
    }
}

// مرحله ۸: تست عملیاتی ثبت‌نام
echo '<b>۸. شبیه‌سازی عملیات ثبت‌نام (بدون درج واقعی):</b><br>';
try {
    $testDb = \WHCM\Core\Bootstrap::getDB();

    // تست SELECT از جدول plans
    $stmt = $testDb->query("SELECT COUNT(*) FROM plans");
    $planCount = $stmt->fetchColumn();
    echo '&nbsp;&nbsp;تست SELECT plans: <span style="color:green;">✅ (' . $planCount . ' پلن)</span><br>';

    // تست SELECT از جدول referral_settings
    $stmt = $testDb->query("SELECT COUNT(*) FROM referral_settings");
    $refCount = $stmt->fetchColumn();
    echo '&nbsp;&nbsp;تست SELECT referral_settings: <span style="color:green;">✅ (' . $refCount . ' تنظیم)</span><br>';

    // تست SELECT از جدول email_templates
    $stmt = $testDb->query("SELECT COUNT(*) FROM email_templates");
    $emailCount = $stmt->fetchColumn();
    echo '&nbsp;&nbsp;تست SELECT email_templates: <span style="color:green;">✅ (' . $emailCount . ' قالب)</span><br>';

    // تست آماده INSERT (بدون اجرای واقعی)
    $stmt = $testDb->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'user', 'active')");
    echo '&nbsp;&nbsp;تست PREPARE INSERT users: <span style="color:green;">✅</span><br>';

    // تست ساخت کد رفرال
    $code = \WHCM\\Domain\\Referral::generateCode();
    echo '&nbsp;&nbsp;تست تولید کد رفرال: <span style="color:green;">✅ (' . htmlspecialchars($code) . ')</span><br>';

} catch (\Throwable $e) {
    echo '&nbsp;&nbsp;<span style="color:red;font-weight:bold;">❌ خطا: ' . htmlspecialchars($e->getMessage()) . ' — فایل: ' . $e->getFile() . ':' . $e->getLine() . '</span><br>';
    echo '<pre style="background:#fef2f2;padding:10px;border-radius:8px;border:1px solid #fca5a5;direction:ltr;text-align:left;font-size:12px;overflow-x:auto;">';
    foreach ($e->getTrace() as $i => $t) {
        echo "#$i " . ($t['class'] ?? '') . ($t['type'] ?? '') . $t['function'] . '() in ' . ($t['file'] ?? 'unknown') . ':' . ($t['line'] ?? '?') . "\n";
    }
    echo '</pre>';
}

echo '<br><span style="color:green;font-size:18px;font-weight:bold;">✅ بررسی تکمیل شد!</span>';
echo '<br><br><span style="color:red;font-weight:bold;">⚠️ حالا فایل check.php و فایل دیتابیس (storage/db/) را پاک کنید و سایت را رفرش کنید.</span>';
echo '</div>';
