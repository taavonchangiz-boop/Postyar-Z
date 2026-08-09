<?php
/**
 * فایل تشخیصی — بعد از رفع مشکل حتماً پاک کنید!
 */
header('Content-Type: text/html; charset=utf-8');
echo '<h2>🔍 تشخیص سرور پُست‌یار</h2>';
echo '<div style="font-family:Tahoma,Arial;direction:rtl;line-height:2.2;">';

// 1. PHP Version
echo '<b>نسخه PHP:</b> ' . PHP_VERSION . '<br>';
if (version_compare(PHP_VERSION, '7.4', '<')) {
    echo '<span style="color:red;font-weight:bold;">❌ نسخه PHP باید 7.4 یا بالاتر باشد!</span><br>';
} else {
    echo '<span style="color:green;">✅ نسخه PHP مناسب است</span><br>';
}

// 2. Extensions
$exts = ['pdo', 'pdo_sqlite', 'pdo_mysql', 'json', 'mbstring', 'curl', 'openssl', 'session'];
echo '<br><b>افزونه‌های مورد نیاز:</b><br>';
foreach ($exts as $ext) {
    $loaded = extension_loaded($ext);
    echo ($loaded ? '✅' : '❌') . ' ' . $ext . '<br>';
}

// 3. mod_rewrite
echo '<br><b>mod_rewrite:</b> ';
echo (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules())) ? '✅ فعال' : '⚠️ بررسی نشد (طبیعی در بعضی هاست‌ها)';
echo '<br>';

// 4. Config file
$config_path = __DIR__ . '/../config/config.php';
echo '<br><b>فایل تنظیمات:</b> ';
if (file_exists($config_path)) {
    echo '<span style="color:green;">✅ وجود دارد</span><br>';
    $config = @include $config_path;
    if (is_array($config)) {
        echo '&nbsp;&nbsp;✅ فرمت صحیح است<br>';
        if (!empty($config['app']['url'])) {
            echo '&nbsp;&nbsp;آدرس: ' . htmlspecialchars($config['app']['url']) . '<br>';
        }
        if (!empty($config['database']['driver'])) {
            echo '&nbsp;&nbsp;درایور دیتابیس: ' . htmlspecialchars($config['database']['driver']) . '<br>';
        }
    } else {
        echo '&nbsp;&nbsp;<span style="color:red;">❌ فایل config.php خطای سینتکس دارد!</span><br>';
    }
} else {
    echo '<span style="color:red;">❌ فایل config.php وجود ندارد!</span><br>';
}

// 5. Writable directories
$dirs = [
    __DIR__ . '/../storage' => 'storage/',
    __DIR__ . '/../storage/db' => 'storage/db/',
    __DIR__ . '/../storage/logs' => 'storage/logs/',
    __DIR__ . '/assets/uploads' => 'public/assets/uploads/',
];
echo '<br><b>دایرکتوری‌ها (قابلیت نوشتن):</b><br>';
foreach ($dirs as $path => $label) {
    $exists = is_dir($path);
    $writable = is_writable($path);
    $icon = (!$exists) ? '❌' : (!$writable) ? '⚠️' : '✅';
    echo $icon . ' ' . $label;
    if (!$exists) echo ' (وجود ندارد)';
    if ($exists && !$writable) echo ' (بدون اجازه نوشتن)';
    echo '<br>';
}

// 6. Try loading Bootstrap
echo '<br><b>تست بارگذاری Bootstrap:</b> ';
$bootstrap_path = __DIR__ . '/../app/Core/Bootstrap.php';
if (file_exists($bootstrap_path)) {
    echo '<span style="color:green;">✅ فایل وجود دارد</span><br>';
} else {
    echo '<span style="color:red;">❌ فایل Bootstrap.php پیدا نشد!</span><br>';
}

// 7. Apache version
echo '<br><b>سرور:</b> ' . $_SERVER['SERVER_SOFTWARE'] . '<br>';

// 8. Document root
echo '<b>Document Root:</b> ' . $_SERVER['DOCUMENT_ROOT'] . '<br>';

echo '<br><span style="color:red;font-weight:bold;">⚠️ بعد از رفع مشکل، حتماً فایل check.php را پاک کنید!</span>';
echo '</div>';
