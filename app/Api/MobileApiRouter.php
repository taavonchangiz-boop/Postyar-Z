<?php
namespace WHCM\Api;

use WHCM\Core\Bootstrap;
use WHCM\Core\RateLimit;

/**
 * مسیردهی اختصاصی API موبایل
 *
 * تمام مسیرهای /api/v1/... در این کلاس ثبت می‌شوند.
 * این کلاس مستقل از Router اصلی سایت کار می‌کند و هیچ تاثیری روی آن ندارد.
 *
 * نحوه کار:
 * 1. public/index.php بررسی می‌کند آیا درخواست با /api/v1/ شروع می‌شود
 * 2. اگر بله، کنترل را به این کلاس می‌سپارد
 * 3. این کلاس مسیر را تطبیق و کنترلر مناسب را صدا می‌زند
 *
 * @package WHCM\Api
 */
class MobileApiRouter {

    private static array $routes = [];
    private static array $globalMiddleware = [];

    /**
     * ثبت مسیر GET
     */
    public static function get(string $path, string $handler, array $middleware = []): void {
        self::$routes['GET'][] = ['path' => $path, 'handler' => $handler, 'middleware' => $middleware];
    }

    /**
     * ثبت مسیر POST
     */
    public static function post(string $path, string $handler, array $middleware = []): void {
        self::$routes['POST'][] = ['path' => $path, 'handler' => $handler, 'middleware' => $middleware];
    }

    /**
     * ثبت مسیر PUT
     */
    public static function put(string $path, string $handler, array $middleware = []): void {
        self::$routes['PUT'][] = ['path' => $path, 'handler' => $handler, 'middleware' => $middleware];
    }

    /**
     * ثبت مسیر DELETE
     */
    public static function delete(string $path, string $handler, array $middleware = []): void {
        self::$routes['DELETE'][] = ['path' => $path, 'handler' => $handler, 'middleware' => $middleware];
    }

    /**
     * ثبت middleware سراسری
     */
    public static function middleware(callable $middleware): void {
        self::$globalMiddleware[] = $middleware;
    }

    /**
     * پردازش درخواست API
     * این متد از public/index.php صدا زده می‌شود
     */
    public static function dispatch(string $method, string $uri): void {
        // حذف /api/v1 از ابتدای URI
        $path = preg_replace('#^/api/v1/?#', '', $uri);
        $path = '/' . $path;

        $method = strtoupper($method);
        $routeList = self::$routes[$method] ?? [];

        foreach ($routeList as $route) {
            $pattern = self::buildPattern($route['path']);
            if (preg_match($pattern, $path, $matches)) {
                // اجرای middleware سراسری
                foreach (self::$globalMiddleware as $mw) {
                    $result = $mw();
                    if ($result === false) {
                        return; // middleware مسیر را متوقف کرد
                    }
                }

                // اجرای middleware مسیر
                foreach ($route['middleware'] as $mwName) {
                    $result = self::runMiddleware($mwName);
                    if ($result === false) {
                        return;
                    }
                }

                // استخراج پارامترها
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // فراخوانی handler
                self::callHandler($route['handler'], $params);
                return;
            }
        }

        MobileApiResponse::notFound('مسیر API یافت نشد.');
    }

    /**
     * ساخت regex از مسیر
     */
    private static function buildPattern(string $path): string {
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    /**
     * اجرای named middleware
     */
    private static function runMiddleware(string $name): bool {
        switch ($name) {
            case 'auth':
                $user = MobileApiAuth::validate();
                if (!$user) {
                    MobileApiResponse::unauthorized();
                    return false;
                }
                // تزریق session برای Domainهای موجود
                MobileApiAuth::injectSession($user['id']);
                return true;

            case 'admin':
                $user = MobileApiAuth::validate();
                if (!$user || ($user['role'] !== 'superadmin' && $user['role'] !== 'support_agent')) {
                    MobileApiResponse::forbidden();
                    return false;
                }
                MobileApiAuth::injectSession($user['id']);
                return true;

            case 'superadmin':
                $user = MobileApiAuth::validate();
                if (!$user || $user['role'] !== 'superadmin') {
                    MobileApiResponse::forbidden();
                    return false;
                }
                MobileApiAuth::injectSession($user['id']);
                return true;

            case 'rate_limit':
                if (!RateLimit::check('api_general', 120, 60)) {
                    MobileApiResponse::tooManyRequests();
                    return false;
                }
                return true;

            default:
                return true;
        }
    }

    /**
     * فراخوانی handler
     */
    private static function callHandler(string $handler, array $params): void {
        // فرمت: "ClassName@method" یا "ClassName::method"
        if (strpos($handler, '@') !== false || strpos($handler, '::') !== false) {
            $sep = strpos($handler, '@') !== false ? '@' : '::';
            [$class, $method] = explode($sep, $handler, 2);
            $fullClass = strpos($class, '\\') === 0 ? $class : '\\WHCM\\Api\\Controllers\\' . $class;

            if (!class_exists($fullClass)) {
                MobileApiResponse::serverError('کنترلر ' . $class . ' یافت نشد.');
                return;
            }

            $instance = new $fullClass();
            if (!method_exists($instance, $method)) {
                MobileApiResponse::serverError('متد ' . $method . ' در کنترلر ' . $class . ' وجود ندارد.');
                return;
            }

            $instance->$method(...array_values($params));
        } else {
            // تابع callback
            if (is_callable($handler)) {
                $handler(...array_values($params));
            } else {
                MobileApiResponse::serverError('Handler نامعتبر است.');
            }
        }
    }

    /**
     * دریافت ورودی JSON از بدنه درخواست
     */
    public static function jsonInput(): array {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    /**
     * دریافت یک فیلد از ورودی JSON (با fallback به POST)
     */
    public static function input(string $key, mixed $default = null): mixed {
        $json = self::jsonInput();
        if (isset($json[$key])) {
            return $json[$key];
        }
        return $_POST[$key] ?? $default;
    }

    /**
     * دریافت کاربر فعلی API (از توکن)
     */
    public static function currentUser(): ?array {
        return MobileApiAuth::validate();
    }

    /**
     * دریافت user_id کاربر فعلی
     */
    public static function currentUserId(): ?int {
        $user = self::currentUser();
        return $user ? $user['id'] : null;
    }
}
