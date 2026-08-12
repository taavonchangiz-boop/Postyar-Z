<?php
namespace WHCM\Api;

use WHCM\Core\Bootstrap;
use WHCM\Core\RateLimit;

/**
 * سیستم احراز هویت API موبایل (Token-Based)
 *
 * این کلاس مسئول تولید، اعتبارسنجی و مدیریت توکن‌های API است.
 * توکن‌ها برای احراز هویت اپلیکیشن اندروید استفاده می‌شوند،
 * در حالی که وب‌سایت به PHP Session خود ادامه می‌دهد.
 *
 * امنیت:
 * - توکن خام فقط یک بار به کلاینت برگردانده می‌شود
 * - فقط هش (SHA-256) توکن در دیتابیس ذخیره می‌شود
 * - توکن‌ها تاریخ انقضا دارند
 * - هر توکن متعلق به یک دستگاه خاص است
 *
 * @package WHCM\Api
 */
class MobileApiAuth {

    /**
     * دریافت هدر Authorization و استخراج توکن
     */
    public static function getTokenFromRequest(): ?string {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (empty($header)) {
            return null;
        }
        // فرمت: "Bearer <token>"
        if (preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    /**
     * تولید توکن تصادفی امن
     */
    public static function generateToken(): string {
        return bin2hex(random_bytes(32)); // 64 کاراکتر هگزادسیمال
    }

    /**
     * هش کردن توکن برای ذخیره در دیتابیس
     */
    public static function hashToken(string $token): string {
        return hash('sha256', $token);
    }

    /**
     * ورود کاربر و تولید توکن API
     *
     * @return array ['success'=>bool, 'token'=>?string, 'user'=>?array, 'message'=>string]
     */
    public static function authenticate(string $email, string $password, string $deviceName = 'android'): array {
        // Rate Limiting
        if (!RateLimit::check('api_login', 10, 300)) {
            return [
                'success' => false,
                'token' => null,
                'user' => null,
                'message' => 'تعداد تلاش‌های ناموفق بیش از حد مجاز است. ۵ دقیقه صبر کنید.'
            ];
        }

        $db = Bootstrap::getDB();

        // جستجوی کاربر
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            RateLimit::hit('api_login', 300);
            return [
                'success' => false,
                'token' => null,
                'user' => null,
                'message' => 'ایمیل یا کلمه عبور نادرست است.'
            ];
        }

        // بررسی وضعیت کاربر
        if ($user['status'] !== 'active') {
            return [
                'success' => false,
                'token' => null,
                'user' => null,
                'message' => 'حساب کاربری شما غیرفعال یا معلق شده است. با پشتیبانی تماس بگیرید.'
            ];
        }

        // بررسی رمز عبور
        if (!password_verify($password, $user['password'])) {
            RateLimit::hit('api_login', 300);
            return [
                'success' => false,
                'token' => null,
                'user' => null,
                'message' => 'ایمیل یا کلمه عبور نادرست است.'
            ];
        }

        // تولید توکن جدید
        $token = self::generateToken();
        $tokenHash = self::hashToken($token);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));

        // ذخیره توکن در دیتابیس
        $stmt = $db->prepare("
            INSERT INTO api_tokens (user_id, token_hash, device_name, created_at, last_used_at, expires_at)
            VALUES (?, ?, ?, datetime('now'), datetime('now'), ?)
        ");
        $stmt->execute([$user['id'], $tokenHash, $deviceName, $expiresAt]);

        // پاک کردن rate limit
        RateLimit::clear('api_login');

        // حذف توکن‌های قدیمی این کاربر (نگه‌داشتن حداکثر 5 توکن فعال)
        $stmt = $db->prepare("
            DELETE FROM api_tokens 
            WHERE user_id = ? AND id NOT IN (
                SELECT id FROM api_tokens WHERE user_id = ? ORDER BY created_at DESC LIMIT 5
            )
        ");
        $stmt->execute([$user['id'], $user['id']]);

        return [
            'success' => true,
            'token' => $token,
            'user' => self::sanitizeUser($user),
            'message' => 'ورود موفقیت‌آمیز بود.'
        ];
    }

    /**
     * اعتبارسنجی توکن و بازگرداندن کاربر
     *
     * @return array|null اطلاعات کاربر یا null اگر توکن نامعتبر باشد
     */
    public static function validate(): ?array {
        $token = self::getTokenFromRequest();
        if (empty($token)) {
            return null;
        }

        $tokenHash = self::hashToken($token);
        $db = Bootstrap::getDB();

        // جستجوی توکن فعال و منقضی‌نشده
        $stmt = $db->prepare("
            SELECT t.*, u.* 
            FROM api_tokens t 
            JOIN users u ON t.user_id = u.id 
            WHERE t.token_hash = ? AND t.revoked_at IS NULL AND t.expires_at > datetime('now')
            LIMIT 1
        ");
        $stmt->execute([$tokenHash]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        // بررسی وضعیت کاربر
        if ($row['status'] !== 'active') {
            return null;
        }

        // بروزرسانی آخرین استفاده
        $stmt = $db->prepare("UPDATE api_tokens SET last_used_at = datetime('now') WHERE id = ?");
        $stmt->execute([$row['id']]);

        return self::sanitizeUser($row);
    }

    /**
     * ابطال توکن فعلی (خروج)
     */
    public static function revokeCurrentToken(): bool {
        $token = self::getTokenFromRequest();
        if (empty($token)) {
            return false;
        }

        $tokenHash = self::hashToken($token);
        $db = Bootstrap::getDB();

        $stmt = $db->prepare("UPDATE api_tokens SET revoked_at = datetime('now') WHERE token_hash = ?");
        $stmt->execute([$tokenHash]);

        return $stmt->rowCount() > 0;
    }

    /**
     * ابطال تمام توکن‌های یک کاربر
     */
    public static function revokeAllUserTokens(int $userId): int {
        $db = Bootstrap::getDB();
        $stmt = $db->prepare("UPDATE api_tokens SET revoked_at = datetime('now') WHERE user_id = ? AND revoked_at IS NULL");
        $stmt->execute([$userId]);
        return $stmt->rowCount();
    }

    /**
     * پاکسازی اطلاعات حساس کاربر قبل از ارسال به کلاینت
     */
    public static function sanitizeUser(array $user): array {
        return [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'status' => $user['status'],
            'business_name' => $user['business_name'] ?? null,
            'business_type' => $user['business_type'] ?? null,
            'phone' => $user['phone'] ?? null,
            'birthday' => $user['birthday'] ?? null,
            'referral_code' => $user['referral_code'] ?? null,
            'referral_points' => (float)($user['referral_points'] ?? 0),
            'wallet_balance' => (float)($user['wallet_balance'] ?? 0),
            'created_at' => $user['created_at']
        ];
    }

    /**
     * تزریق کاربر API به سشن (برای استفاده از Domain‌های موجود)
     *
     * این متد اجازه می‌دهد کدهای موجود که از Auth::tenantId() استفاده می‌کنند
     * بدون تغییر با API کار کنند. فقط موقتاً user_id را در سشن قرار می‌دهد.
     */
    public static function injectSession(int $userId): void {
        $_SESSION['user_id'] = $userId;
    }

    /**
     * پاکسازی سشن تزریق‌شده
     */
    public static function clearInjectedSession(): void {
        unset($_SESSION['user_id']);
    }
}
