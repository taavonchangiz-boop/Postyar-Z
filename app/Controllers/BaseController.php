<?php
namespace WHCM\Controllers;

use WHCM\Core\Bootstrap;
use WHCM\Core\Auth;
use WHCM\Core\Csrf;

/**
 * کنترلر پایه — شامل متدهای مشترک تمام کنترلرها
 *
 * @package WHCM\Controllers
 */
abstract class BaseController {

    /**
     * بررسی احراز هویت کاربر
     */
    protected function checkAuth() {
        if (!Auth::check()) {
            $this->setFlashMessage('جهت دسترسی به این بخش ابتدا وارد حساب کاربری خود شوید.');
            $this->redirect('/');
        }
    }

    /**
     * بررسی دسترسی سوپرادمین
     */
    protected function checkSuperAdmin() {
        if (!Auth::check() || !Auth::isSuperAdmin()) {
            $this->setFlashMessage('دسترسی شما غیرمجاز است.');
            $this->redirect('/');
        }
    }

    /**
     * هدایت به مسیر دیگر
     */
    protected function redirect(string $path) {
        if (strpos($path, 'http://') !== 0 && strpos($path, 'https://') !== 0) {
            $path = Bootstrap::getRouteUrl($path);
        }
        header("Location: " . $path);
        exit;
    }

    /**
     * تنظیم پیام فلش برای نمایش در صفحه بعد
     */
    protected function setFlashMessage(string $msg) {
        $_SESSION['flash_msg'] = $msg;
    }

    /**
     * دریافت و پاک کردن پیام فلش
     */
    protected function getFlashMessage(): ?string {
        $msg = $_SESSION['flash_msg'] ?? null;
        if ($msg) {
            unset($_SESSION['flash_msg']);
        }
        return $msg;
    }

    /**
     * رندر کردن یک View با داده‌های مشخص
     */
    protected function render(string $viewName, array $data = []) {
        extract($data);
        include __DIR__ . "/../Views/{$viewName}.php";
        exit;
    }

    /**
     * آپلود فایل تصویر و تبدیل به WebP
     */
    protected function uploadAndConvertToWebp($file_input_name, $subfolder = 'uploads') {
        if (empty($_FILES[$file_input_name]['tmp_name']) || $_FILES[$file_input_name]['error'] !== UPLOAD_ERR_OK) {
            return '';
        }
        
        $tmp = $_FILES[$file_input_name]['tmp_name'];
        $name = $_FILES[$file_input_name]['name'];
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        
        $target_dir = __DIR__ . '/../../public/assets/' . $subfolder . '/';
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        
        $filename = uniqid('img_') . '.webp';
        $target_file = $target_dir . $filename;
        
        $image = null;
        if ($ext === 'jpg' || $ext === 'jpeg') {
            $image = @imagecreatefromjpeg($tmp);
        } elseif ($ext === 'png') {
            $image = @imagecreatefrompng($tmp);
        } elseif ($ext === 'gif') {
            $image = @imagecreatefromgif($tmp);
        } elseif ($ext === 'webp') {
            $image = @imagecreatefromwebp($tmp);
        }
        
        if ($image) {
            imagewebp($image, $target_file, 80);
            imagedestroy($image);
            
            $assets_url = Bootstrap::getAssetsUrl();
            return rtrim($assets_url, '/') . '/' . $subfolder . '/' . $filename;
        }
        
        return '';
    }

    /**
     * تبدیل تقویم جلالی به میلادی
     */
    protected static function jalaliToGregorian($jy, $jm, $jd) {
        $jy = (int)$jy - 979;
        $jm = (int)$jm - 1;
        $jd = (int)$jd - 1;

        $jy_day = 365 * $jy + (int)($jy / 33) * 8 + (int)(($jy % 33 + 3) / 4);
        for ($i = 0; $i < $jm; ++$i) {
            $jy_day += ($i < 6) ? 31 : 30;
        }

        $g_day = $jy_day + $jd + 79;
        $gy = 1600 + 400 * (int)($g_day / 146097);
        $g_day %= 146097;

        $leap = 1;
        if ($g_day >= 36525) {
            $g_day--;
            $gy += 100 * (int)($g_day / 36524);
            $g_day %= 36524;
            if ($g_day >= 365) {
                $g_day++;
            } else {
                $leap = 0;
            }
        }

        $gy += 4 * (int)($g_day / 1461);
        $g_day %= 1461;

        if ($g_day >= 366) {
            $leap = 0;
            $g_day--;
            $gy += (int)($g_day / 365);
            $g_day %= 365;
        }

        $g_m_d = [0, 31, 28 + $leap, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $gm = 1;
        for ($i = 1; $i <= 12; ++$i) {
            if ($g_day < $g_m_d[$i]) {
                $gm = $i;
                break;
            }
            $g_day -= $g_m_d[$i];
        }
        $gd = $g_day + 1;

        return ['year' => $gy, 'month' => $gm, 'day' => $gd];
    }

    /**
     * ذخیره تنظیمات با الگوی portable (سازگار با SQLite و MySQL)
     */
    protected function saveSetting(int $tenant_id, string $key, string $value) {
        $db = Bootstrap::getDB();
        $stmt = $db->prepare("SELECT id FROM settings WHERE tenant_id = ? AND key_name = ? LIMIT 1");
        $stmt->execute([$tenant_id, $key]);
        if ($stmt->fetch()) {
            $stmt = $db->prepare("UPDATE settings SET key_value = ? WHERE tenant_id = ? AND key_name = ?");
            $stmt->execute([$value, $tenant_id, $key]);
        } else {
            $stmt = $db->prepare("INSERT INTO settings (tenant_id, key_name, key_value) VALUES (?, ?, ?)");
            $stmt->execute([$tenant_id, $key, $value]);
        }
    }
}
