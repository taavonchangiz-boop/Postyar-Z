<?php
namespace WHCM\Core;

/**
 * کلاس راه‌اندازی سامانه (Bootstrap)
 *
 * @package WHCM\Core
 */
class Bootstrap {
    /** @var array */
    private static $config = [];

    /** @var \PDO|null */
    private static $db = null;

    /**
     * اجرای اولیه سیستم
     */
    public static function run() {
        // ۱. مدیریت خطاها بر اساس محیط (production vs development)
        $app_env = self::$config['app']['env'] ?? 'production';
        if ($app_env === 'development') {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', 0);
            ini_set('display_startup_errors', 0);
            error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
            ini_set('log_errors', 1);
        }

        // ۲. ریجستر کردن Autoloader سفارشی
        spl_autoload_register([self::class, 'autoload']);

        // ۳. بارگذاری پیکربندی
        self::$config = require __DIR__ . '/../../config/config.php';

        // ۳.۵. مدیریت خطاها بر اساس محیط
        $app_env = self::$config['app']['env'] ?? 'production';
        if ($app_env === 'development') {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', 0);
            ini_set('display_startup_errors', 0);
            error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
            ini_set('log_errors', 1);
        }

        // ۴. تنظیم منطقه زمانی
        date_default_timezone_set(self::$config['app']['timezone'] ?? 'Asia/Tehran');

        // ۵. شروع سشن امن
        Session::start();

        // ۶. ایجاد دایرکتوری‌های مورد نیاز در صورت عدم وجود
        self::ensureDirectories();

        // ۷. راه‌اندازی دیتابیس و اجرای مایگریشن‌ها در صورت اولین اجرا
        self::initDatabase();

        // ۸. اجرای اتوماتیک ارتقای جداول دیتابیس (بررسی و افزودن ستون‌های جدید)
        try {
            self::$db->exec("ALTER TABLE plans ADD COLUMN payment_url TEXT NULL;");
        } catch (\Exception $e) {
            // نادیده گرفتن خطا
        }

        try {
            self::$db->exec("ALTER TABLE plans ADD COLUMN image_url TEXT NULL;");
        } catch (\Exception $e) {
            // نادیده گرفتن خطا
        }

        try {
            self::$db->exec("ALTER TABLE plans ADD COLUMN description TEXT NULL;");
        } catch (\Exception $e) {
            // نادیده گرفتن خطا
        }

        try {
            self::$db->exec("ALTER TABLE users ADD COLUMN business_name VARCHAR(150) NULL;");
        } catch (\Exception $e) {
            // نادیده گرفتن خطا
        }

        try {
            self::$db->exec("ALTER TABLE users ADD COLUMN business_type VARCHAR(150) NULL;");
        } catch (\Exception $e) {
            // نادیده گرفتن خطا
        }

        try {
            self::$db->exec("ALTER TABLE plans ADD COLUMN early_renewal_discount INTEGER DEFAULT 0;");
        } catch (\Exception $e) {
            // نادیده گرفتن خطا
        }

        try {
            self::$db->exec("ALTER TABLE plans ADD COLUMN general_discount INTEGER DEFAULT 0;");
        } catch (\Exception $e) {
            // نادیده گرفتن خطا
        }

        try {
            self::$db->exec("ALTER TABLE plans ADD COLUMN discount_badge_text VARCHAR(150) NULL;");
        } catch (\Exception $e) {
            // نادیده گرفتن خطا
        }

        try {
            self::$db->exec("ALTER TABLE plans ADD COLUMN is_featured INTEGER DEFAULT 0;");
        } catch (\Exception $e) {
            // نادیده گرفتن خطا
        }


        try {
            self::$db->exec("ALTER TABLE tickets ADD COLUMN attachment TEXT NULL;");
        } catch (\Exception $e) {}
        try {
            self::$db->exec("ALTER TABLE tickets ADD COLUMN assigned_to INTEGER NULL;");
        } catch (\Exception $e) {}

        // ۹. ایجاد خودکار جدول تیکت‌های پشتیبانی داخلی (مایگریشن پویا)
        try {
            $driver = self::getConfig('database.driver', 'sqlite');
            if ($driver === 'mysql') {
                self::$db->exec("
                    CREATE TABLE IF NOT EXISTS tickets (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        user_id INT NOT NULL,
                        subject VARCHAR(255) NOT NULL,
                        category VARCHAR(100) NOT NULL,
                        message TEXT NOT NULL,
                        status VARCHAR(50) DEFAULT 'open',
                        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                ");
            } else {
                self::$db->exec("
                    CREATE TABLE IF NOT EXISTS tickets (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        user_id INTEGER NOT NULL,
                        subject VARCHAR(255) NOT NULL,
                        category VARCHAR(100) NOT NULL,
                        message TEXT NOT NULL,
                        status VARCHAR(50) DEFAULT 'open',
                        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                    );
                ");
            }
        } catch (\Exception $e) {
            // نادیده گرفتن خطا
        }

        // ۱۰. سیستم خوددرمانی دیسک هاست: پاکسازی اتوماتیک تصاویر آپلود شده‌ی قدیمی‌تر از ۳۰ روز (اجرای بهینه فقط یک‌بار در روز برای سرعت ماورایی!)
        try {
            if (empty($_SESSION['last_disk_cleanup_time']) || (time() - $_SESSION['last_disk_cleanup_time'] > 86400)) {
                $_SESSION['last_disk_cleanup_time'] = time();
                $uploads_dir = __DIR__ . '/../../public/assets/uploads/';
                if (file_exists($uploads_dir)) {
                    $files = glob($uploads_dir . '*.webp');
                    $now = time();
                    foreach ($files as $file) {
                        if (is_file($file)) {
                            // اگر سن فایل بیش از ۳۰ روز باشد، آن را خودکار حذف کن
                            if ($now - filemtime($file) > 30 * 86400) {
                                unlink($file);
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // نادیده گرفتن خطا
        }

        // ۱۱. خوددرمانی رسانه‌ای پُست‌یار: تبدیل خودکار تصاویر سنگین به فرمت بهینه و سئو شده‌ی WebP (تحت استانداردهای ۲۰۲۶ روز دنیا)
        try {
            $jpg_path = __DIR__ . '/../../public/assets/images/hero_rocket.jpg';
            $webp_path = __DIR__ . '/../../public/assets/images/hero_rocket.webp';
            if (file_exists($jpg_path) && !file_exists($webp_path) && function_exists('imagecreatefromjpeg')) {
                $img = @imagecreatefromjpeg($jpg_path);
                if ($img) {
                    imagewebp($img, $webp_path, 80);
                    imagedestroy($img);
                }
            }
        } catch (\Exception $e) {
            // نادیده گرفتن خطا
        }
    }

    /**
     * دریافت تنظیمات سیستم (همراه با مکانیزم فوق‌العاده هوشمند تشخیص خودکار URL در هاست اشتراکی)
     */
    public static function getConfig(?string $key = null, $default = null) {
        if ($key === 'app.url') {
            $configured = self::$config['app']['url'] ?? 'http://localhost:8000';
            // اگر آدرس به صورت پیش‌فرض localhost باشد، به طور هوشمند آدرس دامنه‌ی واقعی هاست را شناسایی می‌کنیم
            if (($configured === 'http://localhost:8000' || empty($configured)) && isset($_SERVER['HTTP_HOST'])) {
                $is_secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? 80) == 443;
                $scheme = $is_secure ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'];
                $script = $_SERVER['SCRIPT_NAME'] ?? '';
                $dir = dirname($script);
                if ($dir === '/' || $dir === '\\') {
                    $dir = '';
                }
                // حذف بخش پابلیک در صورتی که فایل از درون آن اجرا شده باشد
                if (substr($dir, -7) === '/public') {
                    $dir = substr($dir, 0, -7);
                }
                return $scheme . '://' . $host . rtrim($dir, '/');
            }
        }

        if ($key === null) {
            return self::$config;
        }

        $parts = explode('.', $key);
        $current = self::$config;

        foreach ($parts as $part) {
            if (!is_array($current) || !isset($current[$part])) {
                return $default;
            }
            $current = $current[$part];
        }

        return $current;
    }

    /**
     * دریافت کانکشن دیتابیس PDO به صورت Singleton
     */
    public static function getDB(): \PDO {
        if (self::$db === null) {
            self::initDatabase();
        }
        return self::$db;
    }

    /**
     * مکانیزم بارگذاری خودکار کلاس‌ها (PSR-4)
     */
    private static function autoload(string $class) {
        // فضای نام پیش‌فرض پلتفرم: WHCM
        $prefix = 'WHCM\\';
        $base_dir = __DIR__ . '/../';

        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }

        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    }

    /**
     * بررسی و ساخت پوشه‌های مورد نیاز
     */
    private static function ensureDirectories() {
        $dirs = [
            __DIR__ . '/../../storage',
            __DIR__ . '/../../storage/db',
            __DIR__ . '/../../storage/uploads',
            __DIR__ . '/../../storage/logs',
        ];

        foreach ($dirs as $dir) {
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }

    /**
     * راه‌اندازی دیتابیس
     */
    private static function initDatabase() {
        if (self::$db !== null) {
            return;
        }

        $driver = self::getConfig('database.driver', 'sqlite');

        try {
            if ($driver === 'sqlite') {
                $path = self::getConfig('database.sqlite.path');
                self::$db = new \PDO("sqlite:" . $path);
                self::$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                self::$db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
                // فعال‌سازی کلیدهای خارجی در SQLite
                self::$db->exec("PRAGMA foreign_keys = ON;");
            } else {
                $host = self::getConfig('database.mysql.host');
                $port = self::getConfig('database.mysql.port', '3306');
                $dbname = self::getConfig('database.mysql.database');
                $user = self::getConfig('database.mysql.username');
                $pass = self::getConfig('database.mysql.password');
                $charset = self::getConfig('database.mysql.charset', 'utf8mb4');

                self::$db = new \PDO("mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}", $user, $pass);
                self::$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                self::$db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            }

            // اجرای فایل نصب/مایگریشن در صورتی که دیتابیس خالی باشد
            self::checkAndRunMigrations();

        } catch (\PDOException $e) {
            die("خطا در اتصال به دیتابیس: " . $e->getMessage());
        }
    }

    /**
     * ایجاد جدول‌ها در صورت خالی بودن دیتابیس
     */
    private static function checkAndRunMigrations() {
        $db = self::$db;
        $hasTable = false;

        if (self::getConfig('database.driver') === 'sqlite') {
            $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'");
            $hasTable = (bool) $stmt->fetch();
        } else {
            $dbname = self::getConfig('database.mysql.database');
            $stmt = $db->prepare("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'users'");
            $stmt->execute([$dbname]);
            $hasTable = (bool) $stmt->fetch();
        }

        if (!$hasTable) {
            // بارگذاری و اجرای فایل مایگریشن اصلی بر اساس نوع درایور دیتابیس
            $driver = self::getConfig('database.driver', 'sqlite');
            $filename = ($driver === 'mysql') ? 'install_mysql.sql' : 'install.sql';
            $migration_file = __DIR__ . '/../../migrations/' . $filename;
            
            if (file_exists($migration_file)) {
                $sql = file_get_contents($migration_file);
                // پارسر هوشمند SQL: تقسیم بر اساس سمی‌کالن با در نظر گرفتن commentها و stringها
                $queries = self::splitSqlQueries($sql);
                foreach ($queries as $query) {
                    $query = trim($query);
                    if (!empty($query)) {
                        $db->exec($query);
                    }
                }
            }
        }
    }

    /**
     * دریافت آدرس هوشمند و کاملاً پویا برای دارایی‌های عمومی (Assets)
     */
    public static function getAssetsUrl() {
        $app_url = rtrim(self::getConfig('app.url'), '/');
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        
        if (strpos($script, '/public/') !== false) {
            return $app_url . '/public/assets';
        } else {
            $filename = $_SERVER['SCRIPT_FILENAME'] ?? '';
            $name = $_SERVER['SCRIPT_NAME'] ?? '';
            
            if (strpos($filename, 'public/index.php') !== false && strpos($name, '/public/') === false) {
                return $app_url . '/assets';
            }
            
            return $app_url . '/public/assets';
        }
    }

    /**
     * تولید آدرس پایدار با ساختار پارامتری جهت تضمین عدم برخورد با خطای ۴۰۴ در زمان غیرفعال بودن ری‌رایت
     */
    public static function getRouteUrl(string $path) {
        $app_url = rtrim(self::getConfig('app.url'), '/');
        // تفکیک مسیر و پارامترهای کوئری
        $parts = explode('?', ltrim($path, '/'), 2);
        $route = '/' . $parts[0];
        $query = isset($parts[1]) ? '&' . $parts[1] : '';
        return $app_url . '/index.php?route=' . urlencode($route) . $query;
    }

    /**
     * فرمت‌دهی پویا و پایداری مطلق آدرس تصویر پلن اشتراک
     */
    public static function getPlanImageUrl($url) {
        if (empty($url)) {
            return '';
        }
        // در صورتی که آدرس کامل باشد
        if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
            $parts = explode('/assets/', $url);
            if (count($parts) > 1) {
                return self::getAssetsUrl() . '/' . $parts[1];
            }
            return $url;
        }
        
        // در صورتی که آدرس نسبی حاوی assets باشد، آن را فیلتر می‌کنیم تا تکرار نشود
        $parts = explode('/assets/', $url);
        if (count($parts) > 1) {
            return self::getAssetsUrl() . '/' . $parts[1];
        }
        
        return self::getAssetsUrl() . '/' . ltrim($url, '/');
    }

    /**
     * پارسر هوشمند SQL: تقسیم کوئری‌ها با در نظر گرفتن commentها و stringها
     */
    private static function splitSqlQueries(string $sql): array {
        $queries = [];
        $current = '';
        $in_string = false;
        $string_char = '';
        $in_line_comment = false;
        $in_block_comment = false;
        $len = strlen($sql);

        for ($i = 0; $i < $len; $i++) {
            $char = $sql[$i];
            $next = ($i + 1 < $len) ? $sql[$i + 1] : '';
            $prev = ($i > 0) ? $sql[$i - 1] : '';

            // مدیریت stringها
            if (!$in_line_comment && !$in_block_comment) {
                if (!$in_string && ($char === "'" || $char === '"') ) {
                    $in_string = true;
                    $string_char = $char;
                } elseif ($in_string && $char === $string_char && $prev !== '\\') {
                    $in_string = false;
                    $string_char = '';
                }
            }

            // مدیریت commentهای خطی (-- )
            if (!$in_string && !$in_block_comment && $char === '-' && $next === '-') {
                $in_line_comment = true;
            }
            if ($in_line_comment && $char === "\n") {
                $in_line_comment = false;
            }

            // مدیریت commentهای بلوکی (/* */)
            if (!$in_string && !$in_line_comment && $char === '/' && $next === '*') {
                $in_block_comment = true;
            }
            if ($in_block_comment && $prev === '*' && $char === '/') {
                $in_block_comment = false;
            }

            // تقسیم بر اساس سمی‌کالن (فقط اگر داخل string یا comment نباشیم)
            if ($char === ';' && !$in_string && !$in_line_comment && !$in_block_comment) {
                $queries[] = $current;
                $current = '';
                continue;
            }

            $current .= $char;
        }

        // اضافه کردن آخرین کوئری اگر باقی مانده باشد
        if (trim($current) !== '') {
            $queries[] = $current;
        }

        return $queries;
    }
}
