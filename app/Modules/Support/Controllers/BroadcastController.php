<?php
namespace WHCM\Modules\Support\Controllers;

use WHCM\Core\Bootstrap;
use WHCM\Core\Csrf;
use WHCM\Domain\TextFormat;
use WHCM\Controllers\BaseController;

/**
 * کنترلر ماژول Support — اعلان همگانی
 * قدم ۲-ب
 */
class BroadcastController extends BaseController
{
    public function announce()
    {
        $this->checkSuperAdmin();
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            $this->setFlashMessage('خطای امنیتی! توکن نامعتبر است.');
            $this->redirect('/hnnh');
        }
        $title = trim($_POST['title'] ?? '');
        $message = trim($_POST['message'] ?? '');
        if (empty($title) || empty($message)) {
            $this->setFlashMessage('عنوان و متن اعلان الزامی هستند.');
            $this->redirect('/hnnh');
        }
        $announcement_data = json_encode([
            'title' => $title,
            'message' => $message,
            'date' => TextFormat::now_jalali()
        ], JSON_UNESCAPED_UNICODE);
        $db = Bootstrap::getDB();
        $stmt = $db->prepare("SELECT id FROM settings WHERE tenant_id = 0 AND key_name = 'global_announcement' LIMIT 1");
        $stmt->execute();
        if ($stmt->fetch()) {
            $stmt = $db->prepare("UPDATE settings SET key_value = ? WHERE tenant_id = 0 AND key_name = 'global_announcement'");
            $stmt->execute([$announcement_data]);
        } else {
            $stmt = $db->prepare("INSERT INTO settings (tenant_id, key_name, key_value) VALUES (0, 'global_announcement', ?)");
            $stmt->execute([$announcement_data]);
        }
        $this->setFlashMessage('اعلان درون‌برنامه‌ای با موفقیت برای تمامی کاربران ارسال گردید. 📢');
        $this->redirect('/hnnh');
    }
}
