<?php
namespace WHCM\Modules\Support\Controllers;

use WHCM\Core\Bootstrap;
use WHCM\Core\Auth;
use WHCM\Core\Csrf;
use WHCM\Domain\TextFormat;
use WHCM\Controllers\BaseController;

/**
 * کنترلر ماژول Support — تیکت‌ها با فایل و ارجاع و بستن همزمان
 * قدم ۲-ب تکمیلی
 */
class TicketController extends BaseController
{
    private function handleAttachment(string $field = 'attachment'): string
    {
        if (empty($_FILES[$field]['tmp_name']) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
            return '';
        }
        $tmp = $_FILES[$field]['tmp_name'];
        $name = $_FILES[$field]['name'];
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        // فقط تصویر مجاز
        if (!in_array($ext, ['jpg','jpeg','png','gif','webp','pdf'])) {
            return '';
        }
        $targetDir = __DIR__ . '/../../../../public/assets/tickets/';
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $filename = uniqid('ticket_') . '.' . $ext;
        $target = $targetDir . $filename;
        // برای تصویر، سعی در تبدیل به webp نداریم — همان فایل را کپی می‌کنیم
        if (move_uploaded_file($tmp, $target)) {
            return Bootstrap::getAssetsUrl() . '/tickets/' . $filename;
        }
        return '';
    }

    public function create()
    {
        $this->checkAuth();
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            $this->setFlashMessage('خطای امنیتی! توکن نامعتبر است.');
            $this->redirect('/dashboard');
        }
        $tenant_id = Auth::tenantId();
        $subject = trim($_POST['subject'] ?? '');
        $category = trim($_POST['category'] ?? 'general');
        $message = trim($_POST['message'] ?? '');
        if (empty($subject) || empty($message)) {
            $this->setFlashMessage('عنوان تیکت و متن پیام الزامی هستند.');
            $this->redirect('/dashboard');
        }
        $attachment = $this->handleAttachment('attachment');
        if ($attachment) {
            $message .= "\n\n[پیوست: " . $attachment . "]";
        }
        $db = Bootstrap::getDB();
        $stmt = $db->prepare("INSERT INTO tickets (user_id, subject, category, message, status, attachment) VALUES (?, ?, ?, ?, 'open', ?)");
        $stmt->execute([$tenant_id, $subject, $category, $message, $attachment]);
        $this->setFlashMessage('تیکت پشتیبانی شما با موفقیت ارسال شد و در صف پاسخگویی قرار گرفت. 🎫');
        $this->redirect('/dashboard');
    }

    public function userReply()
    {
        $this->checkAuth();
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            $this->setFlashMessage('خطای امنیتی! توکن نامعتبر است.');
            $this->redirect('/dashboard');
        }
        $tenant_id = Auth::tenantId();
        $ticket_id = (int)($_POST['ticket_id'] ?? 0);
        $reply = trim($_POST['reply'] ?? '');
        $close = isset($_POST['close_after_reply']);
        if (empty($reply) || $ticket_id <= 0) {
            $this->setFlashMessage('متن پاسخ نمی‌تواند خالی باشد.');
            $this->redirect('/dashboard');
        }
        $db = Bootstrap::getDB();
        $stmt = $db->prepare("SELECT message, user_id FROM tickets WHERE id = ? LIMIT 1");
        $stmt->execute([$ticket_id]);
        $row = $stmt->fetch();
        if (!$row || (int)$row['user_id'] !== $tenant_id) {
            $this->setFlashMessage('تیکت یافت نشد یا دسترسی ندارید.');
            $this->redirect('/dashboard');
        }
        $attachment = $this->handleAttachment('attachment');
        if ($attachment) {
            $reply .= "\n\n[پیوست کاربر: " . $attachment . "]";
        }
        $new_msg = $row['message'] . "\n\n➖➖➖➖➖➖➖➖➖➖\n[پاسخ کاربر در تاریخ " . TextFormat::now_jalali() . "]:\n" . $reply;
        $status = $close ? 'closed' : 'open';
        $stmt = $db->prepare("UPDATE tickets SET message = ?, status = ?, attachment = COALESCE(?, attachment) WHERE id = ?");
        $stmt->execute([$new_msg, $status, $attachment ?: null, $ticket_id]);
        $this->setFlashMessage($close ? 'پاسخ شما ثبت و تیکت بسته شد. ✔' : 'پاسخ شما با موفقیت ثبت شد. ✔');
        $this->redirect('/dashboard');
    }

    public function reply()
    {
        $this->checkSuperAdmin();
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            $this->setFlashMessage('خطای امنیتی! توکن نامعتبر است.');
            $this->redirect('/hnnh');
        }
        $ticket_id = (int)($_POST['ticket_id'] ?? 0);
        $reply = trim($_POST['reply'] ?? '');
        $close = isset($_POST['close_after_reply']);
        $assigned_to = (int)($_POST['assigned_to'] ?? 0);
        if (empty($reply) || $ticket_id <= 0) {
            $this->setFlashMessage('متن پاسخ نمی‌تواند خالی باشد.');
            $this->redirect('/hnnh');
        }
        $db = Bootstrap::getDB();
        $stmt = $db->prepare("SELECT message FROM tickets WHERE id = ? LIMIT 1");
        $stmt->execute([$ticket_id]);
        $msg = $stmt->fetchColumn();
        if (!$msg) {
            $this->setFlashMessage('تیکت یافت نشد.');
            $this->redirect('/hnnh');
        }
        $attachment = $this->handleAttachment('attachment');
        if ($attachment) {
            $reply .= "\n\n[پیوست پشتیبان: " . $attachment . "]";
        }
        $new_msg = $msg . "\n\n➖➖➖➖➖➖➖➖➖➖\n[پاسخ پشتیبان در تاریخ " . TextFormat::now_jalali() . "]:\n" . $reply;
        $status = $close ? 'closed' : 'replied';
        $stmt = $db->prepare("UPDATE tickets SET message = ?, status = ?, assigned_to = ?, attachment = COALESCE(?, attachment) WHERE id = ?");
        $stmt->execute([$new_msg, $status, $assigned_to ?: null, $attachment ?: null, $ticket_id]);
        $this->setFlashMessage($close ? 'پاسخ ثبت و تیکت بسته شد. ✔' : 'پاسخ شما به تیکت با موفقیت ثبت شد. ✔');
        $this->redirect('/hnnh');
    }

    public function closeAdmin()
    {
        $this->checkSuperAdmin();
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            $this->setFlashMessage('خطای امنیتی! توکن نامعتبر است.');
            $this->redirect('/hnnh');
        }
        $ticket_id = (int)($_POST['ticket_id'] ?? 0);
        if ($ticket_id <= 0) {
            $this->setFlashMessage('شناسه تیکت نامعتبر است.');
            $this->redirect('/hnnh');
        }
        $db = Bootstrap::getDB();
        $stmt = $db->prepare("UPDATE tickets SET status = 'closed' WHERE id = ?");
        $stmt->execute([$ticket_id]);
        $this->setFlashMessage('تیکت با موفقیت مختومه و بسته شد. ✔');
        $this->redirect('/hnnh');
    }

    public function closeUser()
    {
        $this->checkAuth();
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            $this->setFlashMessage('خطای امنیتی! توکن نامعتبر است.');
            $this->redirect('/dashboard');
        }
        $ticket_id = (int)($_POST['ticket_id'] ?? 0);
        $tenant_id = Auth::tenantId();
        if ($ticket_id <= 0) {
            $this->setFlashMessage('شناسه تیکت نامعتبر است.');
            $this->redirect('/dashboard');
        }
        $db = Bootstrap::getDB();
        $stmt = $db->prepare("SELECT id FROM tickets WHERE id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$ticket_id, $tenant_id]);
        if (!$stmt->fetch()) {
            $this->setFlashMessage('تیکت یافت نشد یا دسترسی ندارید.');
            $this->redirect('/dashboard');
        }
        $stmt = $db->prepare("UPDATE tickets SET status = 'closed' WHERE id = ?");
        $stmt->execute([$ticket_id]);
        $this->setFlashMessage('تیکت با موفقیت بسته شد. ✔');
        $this->redirect('/dashboard');
    }
}
