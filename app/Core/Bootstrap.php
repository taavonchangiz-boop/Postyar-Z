<?php
namespace WHCM\Core;

/**
 * کلاس راه‌اندازی سامانه (Bootstrap)
 *
 * @package WHCM.Core
 */
class Bootstrap {
    /** @var array */
    private static $config = [];

    /** @var \PDO|null */
    private static $db = null;

    /**
     * اجرای اولیه سیستم — فقط بارگذاری و اتصال دیتابیس
     */
    public static function run() {
        // ۱. مدیریت خطاها
        self::setupErrorReporting();

        // ۲. ریجستر کردن Autoloader سفارشی
        spl_autoload_register([self::class, 'autoload']);

        // ۳. بارگذاری پیکربندی
        self::$config = require __DIR__ . '/../../config/config.php';

        // ۴. تنظیم منطقه زمانی
        date_default_timezone_set(self::$config['app']['timezone'] ?? 'Asia/Tehran');

        // ۵. شروع سشن امن
        Session::start();

        // ۶. ایجاد دایرکتوری‌های مورد نیاز
        self::ensureDirectories();

        // ۷. راه‌اندازی دیتابیس و اجرای مایگریشن‌ها (فقط اولین بار)
        self::initDatabase();

        // ۸. اعمال Security Headers
        self::sendSecurityHeaders();
    }

    /**
     * تنظیمات گزارش خطا بر اساس محیط
     */
    private static function setupErrorReporting(): void {
        $env = self::$config['app']['env'] ?? 'production';
        if ($env === 'development') {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', 0);
            ini_set('display_startup_errors', 0);
            error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
            ini_set('log_errors', 1);
        }
    }

    /**
     * اعمال هدرهای امنیتی HTTP
     */
    public static function sendSecurityHeaders(): void {
        // جلوگیری از Clickjacking
        if (!headers_sent()) {
            header('X-Frame-Options: SAMEORIGIN');
            header('X-Content-Type-Options: nosniff');
            header('X-XSS-Protection: 1; mode=block');
            header('Referrer-Policy: strict-origin-when-cross-origin');
            header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

            // HSTS — فقط اگر HTTPS باشد
            $is_secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || ($_SERVER['SERVER_PORT'] ?? 80) == 443;
            if ($is_secure) {
                header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
            }
        }
    }

    /**
     * دریافت تنظیمات سیستم
     */
    public static function getConfig(?string $key = null, $default = null) {
        if ($key === 'app.url') {
            $configured = self::$config['app']['url'] ?? 'http://localhost:8000';
            if (($configured === 'http://localhost:8000' || empty($configured)) && isset($_SERVER['HTTP_HOST'])) {
                $is_secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? 80) == 443;
                $scheme = $is_secure ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'];
                $script = $_SERVER['SCRIPT_NAME'] ?? '';
                $dir = dirname($script);
                if ($dir === '/' || $dir === '\\') {
                    $dir = '';
                }
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
    private static function ensureDirectories(): void {
        $dirs = [
            __DIR__ . '/../../storage',
            __DIR__ . '/../../storage/db',
            __DIR__ . '/../../storage/uploads',
            __DIR__ . '/../../storage/logs',
            __DIR__ . '/../../public/assets/uploads',
            __DIR__ . '/../../public/assets/plans',
            __DIR__ . '/../../public/assets/receipts',
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
    private static function initDatabase(): void {
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

            // اجرای مایگریشن‌ها فقط در اولین اجرا
            self::checkAndRunMigrations();

            // اجرای مایگریشن‌های نسخه‌دار (ارتقای تدریجی)
            self::runVersionedMigrations();

        } catch (\PDOException $e) {
            // در production فقط لاگ کن، اطلاعات حساس را نمایش نده
            if ((self::$config['app']['env'] ?? 'production') === 'development') {
                die("خطا در اتصال به دیتابیس: " . $e->getMessage());
            } else {
                die("خطای سیستمی. لطفاً بعداً تلاش کنید.");
            }
        }
    }

    /**
     * ایجاد جدول‌ها در صورت خالی بودن دیتابیس (فقط اولین بار)
     */
    private static function checkAndRunMigrations(): void {
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
            $driver = self::getConfig('database.driver', 'sqlite');
            $filename = ($driver === 'mysql') ? 'install_mysql.sql' : 'install.sql';
            $migration_file = __DIR__ . '/../../migrations/' . $filename;

            if (file_exists($migration_file)) {
                $sql = file_get_contents($migration_file);
                $queries = self::splitSqlQueries($sql);
                foreach ($queries as $query) {
                    $query = trim($query);
                    if (!empty($query)) {
                        $db->exec($query);
                    }
                }
            }

            // پس از نصب اولیه، نسخه فعلی مایگریشن ثبت شود
            self::setMigrationVersion('schema_initial');
        }
    }

    /**
     * مایگریشن‌های نسخه‌دار — هر نسخه فقط یک‌بار اجرا می‌شود
     */
    private static function runVersionedMigrations(): void {
        $migrations = [
            'v2_add_plan_columns' => function($db) {
                $cols = ['payment_url TEXT NULL', 'image_url TEXT NULL', 'description TEXT NULL',
                         'early_renewal_discount INTEGER DEFAULT 0', 'general_discount INTEGER DEFAULT 0',
                         'discount_badge_text VARCHAR(150) NULL', 'is_featured INTEGER DEFAULT 0'];
                foreach ($cols as $col) {
                    try { $db->exec("ALTER TABLE plans ADD COLUMN $col"); } catch (\Exception $e) {}
                }
            },
            'v2_add_user_columns' => function($db) {
                try { $db->exec("ALTER TABLE users ADD COLUMN business_name VARCHAR(150) NULL"); } catch (\Exception $e) {}
                try { $db->exec("ALTER TABLE users ADD COLUMN business_type VARCHAR(150) NULL"); } catch (\Exception $e) {}
            },
            'v2_add_ticket_columns' => function($db) {
                try { $db->exec("ALTER TABLE tickets ADD COLUMN attachment TEXT NULL"); } catch (\Exception $e) {}
                try { $db->exec("ALTER TABLE tickets ADD COLUMN assigned_to INTEGER NULL"); } catch (\Exception $e) {}
            },
            'v2_create_tickets_table' => function($db) {
                $driver = self::getConfig('database.driver', 'sqlite');
                try {
                    if ($driver === 'mysql') {
                        $db->exec("CREATE TABLE IF NOT EXISTS tickets (
                            id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL,
                            subject VARCHAR(255) NOT NULL, category VARCHAR(100) NOT NULL,
                            message TEXT NOT NULL, status VARCHAR(50) DEFAULT 'open',
                            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
                    } else {
                        $db->exec("CREATE TABLE IF NOT EXISTS tickets (
                            id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NOT NULL,
                            subject VARCHAR(255) NOT NULL, category VARCHAR(100) NOT NULL,
                            message TEXT NOT NULL, status VARCHAR(50) DEFAULT 'open',
                            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                        );");
                    }
                } catch (\Exception $e) {}
            },
        ];

        foreach ($migrations as $version => $callback) {
            if (!self::hasMigrationRun($version)) {
                $callback(self::$db);
                self::setMigrationVersion($version);
            }
        }
    }

    /**
     * بررسی اینکه آیا مایگریشن خاصی قبلاً اجرا شده یا خیر
     */
    private static function hasMigrationRun(string $version): bool {
        try {
            $db = self::$db;
            // بررسی وجود جدول schema_migrations
            if (self::getConfig('database.driver') === 'sqlite') {
                $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='schema_migrations'");
            } else {
                $stmt = $db->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'schema_migrations'");
            }
            if (!$stmt->fetch()) {
                return false;
            }

            $stmt = $db->prepare("SELECT 1 FROM schema_migrations WHERE version = ? LIMIT 1");
            $stmt->execute([$version]);
            return (bool)$stmt->fetch();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * ثبت نسخه مایگریشن به عنوان اجرا شده
     */
    private static function setMigrationVersion(string $version): void {
        $db = self::$db;
        try {
            $db->exec("CREATE TABLE IF NOT EXISTS schema_migrations (
                version VARCHAR(100) PRIMARY KEY, executed_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )");
            $stmt = $db->prepare("INSERT OR IGNORE INTO schema_migrations (version) VALUES (?)");
            $stmt->execute([$version]);
        } catch (\Exception $e) {
            // در MySQL از ON DUPLICATE KEY استفاده می‌شود
            try {
                $stmt = $db->prepare("INSERT IGNORE INTO schema_migrations (version) VALUES (?)");
                $stmt->execute([$version]);
            } catch (\Exception $e2) {}
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
     * تولید آدرس پایدار با ساختار پارامتری
     */
    public static function getRouteUrl(string $path) {
        $app_url = rtrim(self::getConfig('app.url'), '/');
        $parts = explode('?', ltrim($path, '/'), 2);
        $route = '/' . $parts[0];
        $query = isset($parts[1]) ? '&' . $parts[1] : '';
        return $app_url . '/index.php?route=' . urlencode($route) . $query;
    }

    /**
     * تولید آدرس تصویر پلن اشتراک
     */
    public static function getPlanImageUrl($url) {
        if (empty($url)) {
            return '';
        }
        if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
            $parts = explode('/assets/', $url);
            if (count($parts) > 1) {
                return self::getAssetsUrl() . '/' . $parts[1];
            }
            return $url;
        }

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

            if (!$in_line_comment && !$in_block_comment) {
                if (!$in_string && ($char === "'" || $char === '"') ) {
                    $in_string = true;
                    $string_char = $char;
                } elseif ($in_string && $char === $string_char && $prev !== '\\') {
                    $in_string = false;
                    $string_char = '';
                }
            }

            if (!$in_string && !$in_block_comment && $char === '-' && $next === '-') {
                $in_line_comment = true;
            }
            if ($in_line_comment && $char === "\n") {
                $in_line_comment = false;
            }

            if (!$in_string && !$in_line_comment && $char === '/' && $next === '*') {
                $in_block_comment = true;
            }
            if ($in_block_comment && $prev === '*' && $char === '/') {
                $in_block_comment = false;
            }

            if ($char === ';' && !$in_string && !$in_line_comment && !$in_block_comment) {
                $queries[] = $current;
                $current = '';
                continue;
            }

            $current .= $char;
        }

        if (trim($current) !== '') {
            $queries[] = $current;
        }

        return $queries;
    }

    /**
     * پاکسازی دیسک — فقط از طریق Cron Job فراخوانی شود
     * تصاویر قدیمی‌تر از N روز حذف می‌شوند.
     */
    public static function cleanupOldUploads(int $days = 30): int {
        $count = 0;
        $uploads_dir = __DIR__ . '/../../public/assets/uploads/';
        $now = time();
        $max_age = $days * 86400;

        if (!file_exists($uploads_dir)) {
            return 0;
        }

        $files = glob($uploads_dir . '*.{webp,jpg,jpeg,png,gif}', GLOB_BRACE);
        foreach ($files as $file) {
            if (is_file($file) && ($now - filemtime($file)) > $max_age) {
                unlink($file);
                $count++;
            }
        }

        return $count;
    }
}
