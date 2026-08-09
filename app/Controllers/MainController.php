<?php
namespace WHCM\Controllers;

use WHCM\Core\Bootstrap;
use WHCM\Core\Auth;
use WHCM\Core\Csrf;
use WHCM\Core\RateLimit;
use WHCM\Domain\TextFormat;
use WHCM\Domain\Quota;
use WHCM\Domain\ChannelManager;
use WHCM\Domain\GoldTicker;
use WHCM\Domain\Inbox;
use WHCM\Domain\Sender;

/**
 * کنترلر اصلی هدایت‌کننده و پردازشگر درخواست‌های وب
 *
 * تمام متدهای مشترک (redirect، render، flash، checkAuth، uploadAndConvertToWebp،
 * jalaliToGregorian) در BaseController قرار دارند.
 *
 * @package WHCM\Controllers
 */
class MainController extends BaseController {

    /**
     * صفحه اصلی یا فرم ورود/ثبت‌نام (همراه با کپچای محاسباتی پویا)
     */
    public function index() {
        // جلوگیری از کش شدن لندینگ پیج و فرم‌ها در هاست اشتراکی و مرورگر کاربر
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        if (Auth::check()) {
            if (Auth::isSuperAdmin()) {
                $this->redirect('/hnnh');
            } else {
                $this->redirect('/dashboard');
            }
        }

        // تولید کپچای ریاضی پویا (random_int — مقاوم در برابر پیش‌بینی)
        $num1 = random_int(1, 9);
        $num2 = random_int(1, 9);
        $_SESSION['captcha_answer'] = $num1 + $num2;
        $captcha_question = "حاصل جمع " . TextFormat::fa_digits($num1) . " + " . TextFormat::fa_digits($num2) . " چقدر می‌شود؟";

        // دریافت لیست پلن‌ها جهت نمایش در لندینگ پیج
        $db = Bootstrap::getDB();
        $plans = $db->query("SELECT * FROM plans ORDER BY price ASC")->fetchAll();

        $this->render('home', [
            'title' => 'پُست‌یار | سامانه هوشمند مدیریت و انتشار کانال‌ها',
            'plans' => $plans,
            'captcha_question' => $captcha_question,
            'csrf_field' => Csrf::field(),
            'message' => $this->getFlashMessage()
        ]);
    }

    /**
     * عملیات ورود کاربر
     */
    public function handleLogin() {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            $this->setFlashMessage('خطای امنیتی! توکن نامعتبر است.');
            $this->redirect('/');
        }

        // بررسی کپچای ریاضی ضد ربات
        $captcha = (int)($_POST['captcha'] ?? 0);
        $saved_captcha = isset($_SESSION['captcha_answer']) ? (int)$_SESSION['captcha_answer'] : null;
        if ($saved_captcha === null || $captcha !== $saved_captcha) {
            $this->setFlashMessage('پاسخ سوال امنیتی (کپچا) نادرست است.');
            $this->redirect('/');
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!RateLimit::check('login_web', 5, 60)) {
            $this->setFlashMessage('تعداد تلاش‌های ناموفق شما بیش از حد مجاز است. لطفاً ۱ دقیقه صبر کنید.');
            $this->redirect('/');
        }

        $res = Auth::login($email, $password);
        if ($res['success']) {
            RateLimit::clear('login_web');
            if (Auth::isSuperAdmin()) {
                $this->redirect('/hnnh');
            } else {
                $this->redirect('/dashboard');
            }
        } else {
            RateLimit::hit('login_web', 60);
            $this->setFlashMessage($res['message']);
            $this->redirect('/');
        }
    }

    /**
     * عملیات ثبت‌نام کاربر
     */
    public function handleRegister() {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            $this->setFlashMessage('خطای امنیتی! توکن نامعتبر است.');
            $this->redirect('/');
        }

        // بررسی کپچای ریاضی ضد ربات
        $captcha = (int)($_POST['captcha'] ?? 0);
        $saved_captcha = isset($_SESSION['captcha_answer']) ? (int)$_SESSION['captcha_answer'] : null;
        if ($saved_captcha === null || $captcha !== $saved_captcha) {
            $this->setFlashMessage('پاسخ سوال امنیتی (کپچا) نادرست است.');
            $this->redirect('/');
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        $business_name = trim($_POST['business_name'] ?? '');
        $business_type = trim($_POST['business_type'] ?? '');

        if (empty($name) || empty($email) || empty($password)) {
            $this->setFlashMessage('لطفاً تمامی فیلدها را با دقت تکمیل کنید.');
            $this->redirect('/');
        }

        if ($password !== $password_confirm) {
            $this->setFlashMessage('کلمه عبور با تکرار آن مطابقت ندارد.');
            $this->redirect('/');
        }

        $res = Auth::register($name, $email, $password, $business_name, $business_type);
        if ($res['success']) {
            // ورود خودکار کاربر بلافاصله پس از ثبت‌نام موفق (اگر قبلاً لاگین نشده باشد)
            if (!Auth::check()) {
                Auth::login($email, $password);
            }
            $this->setFlashMessage('ثبت‌نام شما با موفقیت انجام شد و به صورت خودکار وارد حساب شدید! ✨');
            if (Auth::isSuperAdmin()) {
                $this->redirect('/hnnh');
            } else {
                $this->redirect('/dashboard');
            }
        } else {
            $this->setFlashMessage($res['message']);
            $this->redirect('/');
        }
    }

    /**
     * خروج از سیستم
     */
    public function logout() {
        Auth::logout();
        $this->setFlashMessage('شما با موفقیت از سیستم خارج شدید.');
        $this->redirect('/');
    }

    /**
     * پنل کاربری (داشبورد مستاجر)
     */
    public function dashboard() {
        // جلوگیری از کش شدن پیشخوان در هاست اشتراکی و مرورگر کاربر
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        $this->checkAuth();

        $tenant_id = Auth::tenantId();
        $quota = Quota::getTenantQuota($tenant_id);
        $channels = ChannelManager::getTenantChannels($tenant_id);

        $db = Bootstrap::getDB();

        // دریافت اطلاعات یک کانال خاص جهت ویرایش در صورت انتخاب
        $edit_channel = null;
        $edit_channel_id = (int)($_GET['edit_channel'] ?? 0);
        if ($edit_channel_id > 0) {
            $edit_channel = ChannelManager::getChannel($edit_channel_id, $tenant_id);
        }

        // دریافت تنظیمات اختصاصی مستأجر (مثلا تنظیمات ربات طلا)
        $stmt = $db->prepare("SELECT key_name, key_value FROM settings WHERE tenant_id = ?");
        $stmt->execute([$tenant_id]);
        $settings_rows = $stmt->fetchAll();
        $settings = [];
        foreach ($settings_rows as $row) {
            $settings[$row['key_name']] = $row['key_value'];
        }

        // دریافت لیست کامل کلمات کلیدی پاسخگوی خودکار مستأجر
        $stmt = $db->prepare("SELECT ar.*, c.name as channel_name FROM auto_replies ar JOIN channels c ON ar.channel_id = c.id WHERE ar.tenant_id = ? ORDER BY ar.id DESC");
        $stmt->execute([$tenant_id]);
        $auto_replies = $stmt->fetchAll();

        // دریافت تاریخچه پست‌های ارسالی مستأجر — LIMIT 50 + LEFT JOIN بهینه (بدون N+1)
        $stmt = $db->prepare("
            SELECT p.*, 
                   COALESCE(cl.clicks, 0) as clicks,
                   COALESCE(cl.unique_clicks, 0) as unique_clicks
            FROM posts p 
            LEFT JOIN (
                SELECT post_id, COUNT(*) as clicks, COUNT(DISTINCT ip) as unique_clicks 
                FROM clicks_log GROUP BY post_id
            ) cl ON cl.post_id = p.id
            WHERE p.tenant_id = ? 
            ORDER BY p.id DESC
            LIMIT 50
        ");
        $stmt->execute([$tenant_id]);
        $posts = $stmt->fetchAll();

        // دریافت کدهای تخفیف اختصاصی کاربر
        $stmt = $db->prepare("SELECT do.*, p.title as plan_title FROM discount_offers do JOIN plans p ON do.plan_id = p.id WHERE do.user_id = ? AND do.used = 0");
        $stmt->execute([$tenant_id]);
        $offers = $stmt->fetchAll();

        // دریافت صندوق پیام
        $stmt = $db->prepare("SELECT i.*, c.name as channel_name FROM inbox i JOIN channels c ON i.channel_id = c.id WHERE i.tenant_id = ? ORDER BY i.id DESC LIMIT 15");
        $stmt->execute([$tenant_id]);
        $inbox = $stmt->fetchAll();

        // دریافت لیست تیکت‌های پشتیبانی کاربر — LIMIT 50
        $stmt = $db->prepare("SELECT * FROM tickets WHERE user_id = ? ORDER BY id DESC LIMIT 50");
        $stmt->execute([$tenant_id]);
        $tickets = $stmt->fetchAll();

        // دریافت اعلان همگانی درون‌برنامه‌ای ادمین ارشد پُست‌یار
        $stmt = $db->prepare("SELECT key_value FROM settings WHERE tenant_id = 0 AND key_name = 'global_announcement' LIMIT 1");
        $stmt->execute();
        $announcement_json = $stmt->fetchColumn();
        $announcement = $announcement_json ? json_decode($announcement_json, true) : null;

        // دریافت لیست پلن‌ها جهت خرید/ارتقا
        $plans = $db->query("SELECT * FROM plans ORDER BY price ASC")->fetchAll();

        $this->render('dashboard', [
            'title' => 'داشبورد کاربری',
            'user' => Auth::user(),
            'quota' => $quota,
            'channels' => $channels,
            'edit_channel' => $edit_channel,
            'settings' => $settings,
            'auto_replies' => $auto_replies,
            'posts' => $posts,
            'offers' => $offers,
            'inbox' => $inbox,
            'tickets' => $tickets,
            'announcement' => $announcement,
            'plans' => $plans,
            'csrf_field' => Csrf::field(),
            'message' => $this->getFlashMessage()
        ]);
    }

    /**
     * بروزرسانی مشخصات کاربری (نام و ایمیل)
     */
    public function handleUpdateProfile() {
        $this->checkAuth();
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            $this->setFlashMessage('خطای امنیتی! توکن نامعتبر است.');
            $this->redirect('/dashboard');
        }

        $tenant_id = Auth::tenantId();
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (empty($name) || empty($email)) {
            $this->setFlashMessage('تمامی فیلدها الزامی هستند.');
            $this->redirect('/dashboard');
        }

        $db = Bootstrap::getDB();
        // چک کردن یکتایی ایمیل برای دیگران
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1");
        $stmt->execute([$email, $tenant_id]);
        if ($stmt->fetch()) {
            $this->setFlashMessage('این نشانی ایمیل قبلاً توسط کاربر دیگری ثبت شده است.');
            $this->redirect('/dashboard');
        }

        $stmt = $db->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->execute([$name, $email, $tenant_id]);

        $this->setFlashMessage('پروفایل کاربری شما با موفقیت بروزرسانی شد. ✔');
        $this->redirect('/dashboard');
    }

    /**
     * تغییر رمز عبور کاربر با تایید رمز فعلی
     */
    public function handleChangePassword() {
        $this->checkAuth();
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            $this->setFlashMessage('خطای امنیتی! توکن نامعتبر است.');
            $this->redirect('/dashboard');
        }

        $tenant_id = Auth::tenantId();
        $current_pass = $_POST['current_password'] ?? '';
        $new_pass = $_POST['new_password'] ?? '';
        $confirm_pass = $_POST['confirm_password'] ?? '';

        if (empty($current_pass) || empty($new_pass) || empty($confirm_pass)) {
            $this->setFlashMessage('پر کردن تمامی فیلدهای کلمه عبور الزامی است.');
            $this->redirect('/dashboard');
        }

        if ($new_pass !== $confirm_pass) {
            $this->setFlashMessage('رمز عبور جدید با تکرار آن مطابقت ندارد.');
            $this->redirect('/dashboard');
        }

        $db = Bootstrap::getDB();
        $stmt = $db->prepare("SELECT password FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$tenant_id]);
        $user_pass = $stmt->fetchColumn();

        if (!password_verify($current_pass, $user_pass)) {
            $this->setFlashMessage('کلمه عبور فعلی شما نادرست است.');
            $this->redirect('/dashboard');
        }

        $hashed = password_hash($new_pass, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed, $tenant_id]);

        $this->setFlashMessage('کلمه عبور شما با موفقیت تغییر یافت. ✔');
        $this->redirect('/dashboard');
    }

    /**
     * ویرایش کامل کانال و لینک‌ها و دکمه‌های شیشه‌ای تعاملی
     */
    public function handleEditChannel() {
        $this->checkAuth();
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            $this->setFlashMessage('خطای امنیتی! توکن نامعتبر است.');
            $this->redirect('/dashboard');
        }

        $tenant_id = Auth::tenantId();
        $id = (int)($_POST['channel_id'] ?? 0);

        $channel = ChannelManager::getChannel($id, $tenant_id);
        if (!$channel) {
            $this->setFlashMessage('کانال مورد نظر یافت نشد.');
            $this->redirect('/dashboard');
        }

        $name = trim($_POST['name'] ?? '');
        $platform = trim($_POST['platform'] ?? '');
        $channel_id = trim($_POST['channel_id_val'] ?? '');
        $token = trim($_POST['token'] ?? '');

        if (empty($name) || empty($channel_id) || empty($token)) {
            $this->setFlashMessage('تمامی فیلدهای اصلی کانال را تکمیل کنید.');
            $this->redirect('/dashboard?edit_channel=' . $id);
        }

        // پردازش ساختار لینک‌های سه‌گانه
        $links = [
            ['name' => trim($_POST['link_name_1'] ?? ''), 'url' => trim($_POST['link_url_1'] ?? '')],
            ['name' => trim($_POST['link_name_2'] ?? ''), 'url' => trim($_POST['link_url_2'] ?? '')],
            ['name' => trim($_POST['link_name_3'] ?? ''), 'url' => trim($_POST['link_url_3'] ?? '')]
        ];

        // پردازش دکمه‌های شیشه‌ای تعاملی
        $buttons_active = isset($_POST['buttons_active']) ? true : false;
        $buttons = [
            ['text' => trim($_POST['btn_text_1'] ?? ''), 'url' => trim($_POST['btn_url_1'] ?? '')],
            ['text' => trim($_POST['btn_text_2'] ?? ''), 'url' => trim($_POST['btn_url_2'] ?? '')]
        ];

        $button_config = [
            'active' => $buttons_active,
            'buttons' => $buttons
        ];

        $db = Bootstrap::getDB();

        // اگر آیدی کانال یا پلتفرم عوض شده باشد، باید چک ضد تقلب بکنیم
        if ($channel['channel_id'] !== $channel_id || $channel['platform'] !== $platform) {
            $stmt = $db->prepare("SELECT owner_user_id FROM channel_registry WHERE platform = ? AND channel_id = ? LIMIT 1");
            $stmt->execute([$platform, $channel_id]);
            $reg = $stmt->fetch();
            if ($reg && (int)$reg['owner_user_id'] !== $tenant_id) {
                $this->setFlashMessage('این شناسه کانال قبلاً توسط کاربر دیگری ثبت شده و قفل است.');
                $this->redirect('/dashboard?edit_channel=' . $id);
            }
            if (!$reg) {
                // ثبت در رجیستری جهانی
                $stmt = $db->prepare("INSERT INTO channel_registry (platform, channel_id, owner_user_id) VALUES (?, ?, ?)");
                $stmt->execute([$platform, $channel_id, $tenant_id]);
            }
        }

        // بروزرسانی کانال در جدول مستاجر
        $stmt = $db->prepare("
            UPDATE channels 
            SET name = ?, platform = ?, channel_id = ?, token = ?, link_config = ?, button_config = ? 
            WHERE id = ? AND tenant_id = ?
        ");
        $stmt->execute([
            $name,
            $platform,
            $channel_id,
            $token,
            json_encode($links, JSON_UNESCAPED_UNICODE),
            json_encode($button_config, JSON_UNESCAPED_UNICODE),
            $id,
            $tenant_id
        ]);

        $this->setFlashMessage('تنظیمات کانال با موفقیت بروزرسانی شد. ✔');
        $this->redirect('/dashboard');
    }

    /**
     * عملیات افزودن کانال جدید
     */
    public function handleAddChannel() {
        $this->checkAuth();

        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            $this->setFlashMessage('خطای امنیتی! توکن نامعتبر است.');
            $this->redirect('/dashboard');
        }

        $name = trim($_POST['name'] ?? '');
        $platform = trim($_POST['platform'] ?? '');
        $channel_id = trim($_POST['channel_id'] ?? '');
        $token = trim($_POST['token'] ?? '');

        $res = ChannelManager::addChannel($name, $platform, $channel_id, $token);
        $this->setFlashMessage($res['message']);
        $this->redirect('/dashboard');
    }

    /**
     * عملیات حذف کانال (POST با CSRF)
     */
    public function handleDeleteChannel() {
        $this->checkAuth();
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            $this->setFlashMessage('خطای امنیتی! توکن نامعتبر است.');
            $this->redirect('/dashboard');
        }

        $id = (int)($_POST['channel_id'] ?? 0);

        if (ChannelManager::deleteChannel($id)) {
            $this->setFlashMessage('کانال با موفقیت حذف گردید (شناسه کانال به جهت قوانین ضدتقلب قفل باقی می‌ماند).');
        } else {
            $this->setFlashMessage('امکان حذف کانال وجود ندارد یا کانال متعلق به شما نیست.');
        }

        $this->redirect('/dashboard');
    }

    /**
     * عملیات ثبت پرداخت رسید مستقیم (کارت به کارت / بلو بانک)
     */
    public function handlePaymentSubmit() {
        $this->checkAuth();

        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            $this->setFlashMessage('خطای امنیتی! توکن نامعتبر است.');
            $this->redirect('/dashboard');
        }

        $tenant_id = Auth::tenantId();
        $plan_id = (int)$_POST['plan_id'];
        $amount = (float)$_POST['amount'];
        $ref_num = trim($_POST['reference_num'] ?? '');
        
        // آپلود خودکار عکس رسید پرداخت به فرمت وب‌پی
        $receipt_photo = $this->uploadAndConvertToWebp('receipt_photo', 'receipts');

        $db = Bootstrap::getDB();

        // ثبت پرداخت با وضعیت در انتظار تایید به همراه عکس رسید
        $stmt = $db->prepare("INSERT INTO payments (user_id, plan_id, amount, reference_num, payment_method, status, receipt_photo) VALUES (?, ?, ?, ?, 'card_to_card', 'pending', ?)");
        $stmt->execute([$tenant_id, $plan_id, $amount, $ref_num, $receipt_photo]);

        $this->setFlashMessage('رسید پرداخت شما با موفقیت ثبت شد و در صف تایید مدیر قرار گرفت. پس از بررسی، اشتراک شما فعال خواهد شد.');
        $this->redirect('/dashboard');
    }

    /**
     * پنل مدیریت کل (Super Admin)
     */
    public function admin() {
        // جلوگیری از کش شدن پنل مدیریت در هاست اشتراکی و مرورگر کاربر
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        $this->checkSuperAdmin();

        $db = Bootstrap::getDB();

        // سیستم Pagination — تعداد آیتم در هر صفحه
        $per_page = 20;
        $current_page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($current_page - 1) * $per_page;

        // ۱. لیست کاربران با JOIN بهینه (بدون N+1 subquery) + Pagination
        $admin_id = Auth::tenantId() ?: 0;
        $stmt_users = $db->prepare("
            SELECT u.id, u.name, u.email, u.role, u.status, u.created_at,
                   u.business_name, u.business_type,
                   COALESCE(c.cnt, 0) as channel_count,
                   s.end_date,
                   p.title as plan_title
            FROM users u
            LEFT JOIN (SELECT tenant_id, COUNT(*) as cnt FROM channels GROUP BY tenant_id) c ON c.tenant_id = u.id
            LEFT JOIN subscriptions s ON s.user_id = u.id AND s.status = 'active'
                 AND s.id = (SELECT MAX(id) FROM subscriptions WHERE user_id = u.id AND status = 'active')
            LEFT JOIN plans p ON s.plan_id = p.id
            WHERE u.id != ?
            ORDER BY u.id DESC
            LIMIT ? OFFSET ?
        ");
        $stmt_users->bindValue(1, $admin_id, \PDO::PARAM_INT);
        $stmt_users->bindValue(2, $per_page, \PDO::PARAM_INT);
        $stmt_users->bindValue(3, $offset, \PDO::PARAM_INT);
        $stmt_users->execute();
        $users = $stmt_users->fetchAll();

        // تعداد کل کاربران برای pagination (prepared statement — جلوگیری از SQL injection)
        $stmt_count = $db->prepare("SELECT COUNT(*) FROM users WHERE id != ?");
        $stmt_count->execute([$admin_id]);
        $total_users = (int)$stmt_count->fetchColumn();
        $total_user_pages = max(1, (int)ceil($total_users / $per_page));

        // ۲. پرداخت‌ها — فقط ۵۰ رکورد آخر (با pagination مشابه)
        $stmt_payments = $db->prepare("
            SELECT p.*, u.name as user_name, u.email as user_email, pl.title as plan_title 
            FROM payments p 
            JOIN users u ON p.user_id = u.id 
            JOIN plans pl ON p.plan_id = pl.id 
            ORDER BY p.id DESC
            LIMIT ? OFFSET ?
        ");
        $stmt_payments->bindValue(1, $per_page, \PDO::PARAM_INT);
        $stmt_payments->bindValue(2, $offset, \PDO::PARAM_INT);
        $stmt_payments->execute();
        $payments = $stmt_payments->fetchAll();

        $stmt_count_payments = $db->prepare("SELECT COUNT(*) FROM payments");
        $stmt_count_payments->execute();
        $total_payments = (int)$stmt_count_payments->fetchColumn();
        $total_payment_pages = max(1, (int)ceil($total_payments / $per_page));

        // ۳. لیست پلن‌های فعال
        $plans = $db->query("SELECT * FROM plans ORDER BY price ASC")->fetchAll();

        // ۴. بررسی وجود درخواست ویرایش یک پلن اشتراکی خاص
        $edit_plan = null;
        $edit_plan_id = (int)($_GET['edit_plan'] ?? 0);
        if ($edit_plan_id > 0) {
            $stmt = $db->prepare("SELECT * FROM plans WHERE id = ? LIMIT 1");
            $stmt->execute([$edit_plan_id]);
            $edit_plan = $stmt->fetch() ?: null;
        }

        // ۵. تیکت‌ها — فقط ۵۰ رکورد آخر
        $stmt_tickets = $db->prepare("
            SELECT t.*, u.name as user_name, u.email as user_email 
            FROM tickets t 
            JOIN users u ON t.user_id = u.id 
            ORDER BY (t.status = 'open') DESC, t.id DESC
            LIMIT ? OFFSET ?
        ");
        $stmt_tickets->bindValue(1, $per_page, \PDO::PARAM_INT);
        $stmt_tickets->bindValue(2, $offset, \PDO::PARAM_INT);
        $stmt_tickets->execute();
        $tickets = $stmt_tickets->fetchAll();

        $stmt_count_tickets = $db->prepare("SELECT COUNT(*) FROM tickets");
        $stmt_count_tickets->execute();
        $total_tickets = (int)$stmt_count_tickets->fetchColumn();
        $total_ticket_pages = max(1, (int)ceil($total_tickets / $per_page));

        $this->render('admin', [
            'title' => 'پنل مدیریت ارشد کل',
            'users' => $users,
            'total_user_pages' => $total_user_pages,
            'current_page' => $current_page,
            'payments' => $payments,
            'plans' => $plans,
            'edit_plan' => $edit_plan,
            'tickets' => $tickets,
            'csrf_field' => Csrf::field(),
            'message' => $this->getFlashMessage()
        ]);
    }

    public function handleApprovePayment(){ return (new \WHCM\Modules\Billing\Controllers\PaymentController)->approve(); }
    public function handleCreatePlan(){ return (new \WHCM\Modules\Billing\Controllers\PlanController)->create(); }
    /**
     * ردیابی کلیک و انتقال به لینک مقصد نهایی
     */
    public function handleClick() {
        $post_id = (int)($_GET['p'] ?? 0);
        $channel_id = (int)($_GET['c'] ?? 0);

        if ($post_id > 0 && $channel_id > 0) {
            $db = Bootstrap::getDB();

            // ثبت لاگ کلیک برای آنالیتیکس فوق‌حرفه‌ای تفکیکی
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $stmt = $db->prepare("INSERT INTO clicks_log (post_id, channel_id, ip, user_agent) VALUES (?, ?, ?, ?)");
            $stmt->execute([$post_id, $channel_id, $ip, $ua]);

            // بروزرسانی آمار تجمیعی کلیک‌ها
            $stmt = $db->prepare("UPDATE post_channel_stats SET clicks = clicks + 1 WHERE post_id = ? AND channel_id = ?");
            $stmt->execute([$post_id, $channel_id]);

            // دریافت لینک هدف متناظر با کانال
            $stmt = $db->prepare("SELECT link_config FROM channels WHERE id = ? LIMIT 1");
            $stmt->execute([$channel_id]);
            $channel = $stmt->fetch();

            if ($channel) {
                $links = json_decode($channel['link_config'] ?? '[]', true);
                // لینک دوم معمولاً لینک مستقیم فروشگاه یا ارجاعی اول است
                $url = !empty($links[0]['url']) ? $links[0]['url'] : '/';
                $this->redirect($url);
            }
        }

        $this->redirect('/');
    }

    /**
     * دریافت اطلاعات وبهوک‌های ربات‌ها (با اعتبارسنجی امنیتی secret_token)
     */
    public function handleApiWebhook() {
        $channel_id = (int)($_GET['channel_id'] ?? 0);
        if ($channel_id <= 0) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'شناسه کانال نامعتبر است.']);
            exit;
        }

        $db = Bootstrap::getDB();
        $stmt = $db->prepare("SELECT * FROM channels WHERE id = ? LIMIT 1");
        $stmt->execute([$channel_id]);
        $channel = $stmt->fetch();

        if (!$channel) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'کانال یافت نشد.']);
            exit;
        }

        // ---- اعتبارسنجی secret_token تلگرام ----
        if ($channel['platform'] === 'telegram' && !empty($channel['webhook_secret'])) {
            $header_secret = $_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN'] ?? '';
            if (!hash_equals($channel['webhook_secret'], $header_secret)) {
                http_response_code(403);
                echo json_encode(['ok' => false, 'error' => 'توکن امنیتی نامعتبر است.']);
                exit;
            }
        }

        Inbox::handleWebhook($channel);
        echo json_encode(['ok' => true]);
        exit;
    }

    /**
     * ذخیره تنظیمات ربات هوشمند طلا و سکه مستأجر
     */
    public function handleSaveGoldSettings() {
        $this->checkAuth();
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            $this->setFlashMessage('خطای امنیتی! توکن نامعتبر است.');
            $this->redirect('/dashboard');
        }

        $tenant_id = Auth::tenantId();
        $db = Bootstrap::getDB();

        $schedule = trim($_POST['gold_schedule'] ?? 'manual');
        $api_url = trim($_POST['gold_api_url'] ?? '');
        $currency = trim($_POST['gold_currency'] ?? 'toman');
        $template = trim($_POST['gold_template'] ?? '');
        
        // آپلود فیزیکی عکس طلا به صورت خودکار به وب‌پی
        $image_url = $this->uploadAndConvertToWebp('gold_image', 'uploads');
        if (empty($image_url)) {
            // حفظ تصویر قبلی در صورت عدم آپلود تصویر جدید
            $stmt = $db->prepare("SELECT key_value FROM settings WHERE tenant_id = ? AND key_name = 'gold_image_url' LIMIT 1");
            $stmt->execute([$tenant_id]);
            $image_url = $stmt->fetchColumn() ?: '';
        }

        $channel_ids = isset($_POST['gold_channels']) ? array_map('intval', $_POST['gold_channels']) : [];

        // تراکنش ایمن جهت ذخیره‌سازی گروهی تنظیمات مستأجر
        $settings_to_save = [
            'gold_schedule' => $schedule,
            'gold_api_url' => $api_url,
            'gold_currency' => $currency,
            'gold_template' => $template,
            'gold_image_url' => $image_url,
            'gold_auto_channels' => json_encode($channel_ids)
        ];

        $this->saveSettingsBatch($tenant_id, $settings_to_save);

        $this->setFlashMessage('تنظیمات ربات نرخ طلا با موفقیت ذخیره گردید. 🪙');
        $this->redirect('/dashboard');
    }

    /**
     * شبیه‌سازی و تست انتشار دستی و آنی نرخ طلا توسط مستأجر
     */
    public function handleTriggerGoldPublish() {
        $this->checkAuth();
        $tenant_id = Auth::tenantId();

        $db = Bootstrap::getDB();
        // دریافت آدرس API مستأجر
        $stmt = $db->prepare("SELECT key_value FROM settings WHERE tenant_id = ? AND key_name = 'gold_api_url' LIMIT 1");
        $stmt->execute([$tenant_id]);
        $custom_api = $stmt->fetchColumn();

        $vals = GoldTicker::fetchValues($custom_api ?: '');
        if (!$vals['success']) {
            $this->setFlashMessage('خطا در دریافت نرخ از API: ' . $vals['message']);
            $this->redirect('/dashboard');
        }

        // دریافت کانال‌های هدف
        $stmt = $db->prepare("SELECT key_value FROM settings WHERE tenant_id = ? AND key_name = 'gold_auto_channels' LIMIT 1");
        $stmt->execute([$tenant_id]);
        $channels_json = $stmt->fetchColumn();
        $channel_ids = $channels_json ? json_decode($channels_json, true) : [];

        if (empty($channel_ids)) {
            // ارسال به همه کانال‌های فعال در صورت عدم انتخاب اختصاصی
            $stmt = $db->prepare("SELECT id FROM channels WHERE tenant_id = ?");
            $stmt->execute([$tenant_id]);
            $channel_ids = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        }

        if (empty($channel_ids)) {
            $this->setFlashMessage('خطا: هیچ کانالی برای ارسال خودکار طلا متصل یا انتخاب نشده است.');
            $this->redirect('/dashboard');
        }

        $title = 'اعلام نرخ لحظه‌ای بازار طلا و سکه';
        $content = GoldTicker::buildMessage($tenant_id, $vals);
        
        // دریافت عکس پیش‌فرض طلا
        $stmt = $db->prepare("SELECT key_value FROM settings WHERE tenant_id = ? AND key_name = 'gold_image_url' LIMIT 1");
        $stmt->execute([$tenant_id]);
        $image = $stmt->fetchColumn() ?: '';

        $res = Sender::sendPostToChannels($tenant_id, $channel_ids, $title, $content, $image);

        if ($res['success']) {
            $this->setFlashMessage('انتشار آنی و موفقیت‌آمیز نرخ طلا به تمام کانال‌های هدف انجام گردید! ⚡🪙');
        } else {
            $this->setFlashMessage('ارسال پیام با خطا مواجه شد. جزئیات کانال‌ها را بررسی کنید.');
        }

        $this->redirect('/dashboard');
    }

    /**
     * افزودن پاسخ خودکار جدید
     */
    public function handleAddAutoReply() {
        $this->checkAuth();
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            $this->setFlashMessage('خطای امنیتی! توکن نامعتبر است.');
            $this->redirect('/dashboard');
        }

        $tenant_id = Auth::tenantId();
        $channel_id = (int)$_POST['channel_id'];
        $keyword = trim($_POST['keyword'] ?? '');
        $reply_text = trim($_POST['reply_text'] ?? '');

        if (empty($keyword) || empty($reply_text) || $channel_id <= 0) {
            $this->setFlashMessage('تمامی فیلدها الزامی هستند.');
            $this->redirect('/dashboard');
        }

        $db = Bootstrap::getDB();
        $stmt = $db->prepare("INSERT INTO auto_replies (tenant_id, channel_id, keyword, reply_text, active) VALUES (?, ?, ?, ?, 1)");
        $stmt->execute([$tenant_id, $channel_id, $keyword, $reply_text]);

        $this->setFlashMessage('پاسخ خودکار جدید با موفقیت اضافه شد. 🤖');
        $this->redirect('/dashboard');
    }

    /**
     * حذف کلمه کلیدی پاسخگو (POST با CSRF)
     */
    public function handleDeleteAutoReply() {
        $this->checkAuth();
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            $this->setFlashMessage('خطای امنیتی! توکن نامعتبر است.');
            $this->redirect('/dashboard');
        }

        $tenant_id = Auth::tenantId();
        $id = (int)($_POST['reply_id'] ?? 0);

        $db = Bootstrap::getDB();
        $stmt = $db->prepare("DELETE FROM auto_replies WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $tenant_id]);

        $this->setFlashMessage('پاسخ خودکار کلمه کلیدی با موفقیت حذف گردید.');
        $this->redirect('/dashboard');
    }

    /**
     * ایجاد، ارسال آنی یا زمان‌بندی پیام‌ها به شبکه‌های اجتماعی هدف (تلگرام و بله)
     */
    public function handleCreatePost() {
        $this->checkAuth();
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            $this->setFlashMessage('خطای امنیتی! توکن نامعتبر است.');
            $this->redirect('/dashboard');
        }

        $tenant_id = Auth::tenantId();
        
        // بررسی سهمیه پست باقیمانده مستأجر
        $quota = Quota::getTenantQuota($tenant_id);
        if (!$quota['can_send_post']) {
            $this->setFlashMessage('خطا: سهمیه ارسال پست شما در این دوره به اتمام رسیده است. لطفا اشتراک خود را تمدید یا ارتقا دهید.');
            $this->redirect('/dashboard');
        }

        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $send_type = trim($_POST['send_type'] ?? 'instant');
        
        // دریافت تاریخ شمسی بازشو و تبدیل به ساختار دیتابیس
        $scheduled_at = '';
        if ($send_type === 'scheduled') {
            $sched_date = trim($_POST['sched_date'] ?? '');
            if (!empty($sched_date)) {
                list($s_year, $s_month, $s_day) = explode('/', $sched_date);
                $s_year = (int)$s_year;
                $s_month = (int)$s_month;
                $s_day = (int)$s_day;
            } else {
                $s_year = 1405;
                $s_month = 1;
                $s_day = 1;
            }
            $s_hour = trim($_POST['sched_hour'] ?? '00');
            $s_minute = trim($_POST['sched_minute'] ?? '00');
            
            // ابتدا تاریخ شمسی منتخب را به میلادی تبدیل می‌کنیم تا در دیتابیس به صورت کاملاً استاندارد ذخیره شود!
            // پُست‌یار مجهز به تبدیل معکوس جلالی به میلادی فوق‌حرفه‌ای است:
            $g_date = self::jalaliToGregorian($s_year, $s_month, $s_day);
            $scheduled_at = $g_date['year'] . '-' . str_pad($g_date['month'], 2, '0', STR_PAD_LEFT) . '-' . str_pad($g_date['day'], 2, '0', STR_PAD_LEFT) . ' ' . $s_hour . ':' . $s_minute . ':00';
        }

        // آپلود خودکار و فیزیکی فایل تصویر پست و تبدیل به فرمت بهینه وب‌پی
        $media_url = $this->uploadAndConvertToWebp('media_file', 'uploads');
        
        $channel_ids = isset($_POST['post_channels']) ? array_map('intval', $_POST['post_channels']) : [];

        if (empty($title) || empty($content)) {
            $this->setFlashMessage('تمامی فیلدهای عنوان و محتوای پست الزامی هستند.');
            $this->redirect('/dashboard');
        }

        if (empty($channel_ids)) {
            $this->setFlashMessage('خطا: حداقل یک کانال هدف جهت انتشار پست انتخاب کنید.');
            $this->redirect('/dashboard');
        }

        $db = Bootstrap::getDB();

        // ثبت رکورد اولیه پست در پایگاه داده مستأجر
        $status = ($send_type === 'scheduled') ? 'scheduled' : 'draft';
        $sched_date = !empty($scheduled_at) ? $scheduled_at : null;

        $stmt = $db->prepare("INSERT INTO posts (tenant_id, title, content, media_url, status, scheduled_at) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$tenant_id, $title, $content, $media_url, $status, $sched_date]);
        $post_id = (int)$db->lastInsertId();

        if ($send_type === 'instant') {
            // ارسال همگام و آنی به تمامی کانال‌های منتخب
            $res = Sender::sendPostToChannels($tenant_id, $channel_ids, $title, $content, $media_url, $post_id);
            
            if ($res['success']) {
                $db->prepare("UPDATE posts SET status = 'sent' WHERE id = ?")->execute([$post_id]);
                $this->setFlashMessage('پست شما با موفقیت به تمام کانال‌های هدف ارسال گردید! ⚡✈');
            } else {
                $db->prepare("UPDATE posts SET status = 'failed' WHERE id = ?")->execute([$post_id]);
                $this->setFlashMessage('ارسال پست با خطا مواجه شد. جزئیات کانال‌ها را بررسی نمایید.');
            }
        } else {
            $this->setFlashMessage('پست شما با موفقیت برای تاریخ شمسی تعیین شده زمان‌بندی گردید. ⏰');
        }

        $this->redirect('/dashboard');
    }

    public function handleEditPlan(){ return (new \WHCM\Modules\Billing\Controllers\PlanController)->edit(); }
    public function handleDeletePlan(){ return (new \WHCM\Modules\Billing\Controllers\PlanController)->delete(); }
    public function handleCreateTicket(){ return (new \WHCM\Modules\Support\Controllers\TicketController)->create(); }
    public function handleReplyTicket(){ return (new \WHCM\Modules\Support\Controllers\TicketController)->reply(); }

    public function handleCloseTicketUser(){ return (new \WHCM\Modules\Support\Controllers\TicketController)->closeUser(); }

    public function handleUserReplyTicket(){ return (new \WHCM\Modules\Support\Controllers\TicketController)->userReply(); }
    public function handleAssignTicket(){ 
        $this->checkSuperAdmin();
        $tid=(int)($_POST['ticket_id'] ?? 0);
        $aid=(int)($_POST['assigned_to'] ?? 0);
        if($tid>0){
            $db=\WHCM\Core\Bootstrap::getDB();
            $db->prepare("UPDATE tickets SET assigned_to=? WHERE id=?")->execute([$aid?:null,$tid]);
            $this->setFlashMessage('تیکت با موفقیت ارجاع داده شد. ✔');
        }
        $this->redirect('/hnnh');
    }

    public function handleCloseTicketAdmin(){ return (new \WHCM\Modules\Support\Controllers\TicketController)->closeAdmin(); }

    /**
     * بازیابی کلمه عبور با متد ایمن (توکن یکبار مصرف)
     */
    public function handleResetPassword() {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            $this->setFlashMessage('خطای امنیتی! توکن نامعتبر است.');
            $this->redirect('/');
        }

        $email = trim($_POST['email'] ?? '');
        if (empty($email)) {
            $this->setFlashMessage('نشانی ایمیل را وارد کنید.');
            $this->redirect('/');
        }

        $db = Bootstrap::getDB();
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user_id = $stmt->fetchColumn();

        if (!$user_id) {
            // برای جلوگیری از افشای وجود ایمیل، پیام یکسان برمی‌گردانیم
            $this->setFlashMessage('در صورت وجود حساب، دستورالعمل بازنشانی ارسال شد.');
            $this->redirect('/');
            return;
        }

        // تولید توکن یکبار مصرف با اعتبار ۱ ساعت
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 3600);
        
        // ذخیره توکن در تنظیمات کاربر
        $stmt = $db->prepare("SELECT id FROM settings WHERE tenant_id = ? AND key_name = 'password_reset_token' LIMIT 1");
        $stmt->execute([$user_id]);
        if ($stmt->fetch()) {
            $stmt = $db->prepare("UPDATE settings SET key_value = ? WHERE tenant_id = ? AND key_name = 'password_reset_token'");
            $stmt->execute([$token . '|' . $expires, $user_id]);
        } else {
            $stmt = $db->prepare("INSERT INTO settings (tenant_id, key_name, key_value) VALUES (?, 'password_reset_token', ?)");
            $stmt->execute([$user_id, $token . '|' . $expires]);
        }

        // ارسال ایمیل با لینک بازنشانی رمز عبور
        $reset_link = Bootstrap::getConfig('app.url') . '/index.php?route=/reset-password&token=' . $token;

        // دریافت نام کاربر برای قالب ایمیل
        $stmt = $db->prepare("SELECT name FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$user_id]);
        $user_name = $stmt->fetchColumn() ?: 'کاربر';

        // ارسال ایمیل (اگر SMTP تنظیم نشده باشد، حداقل لاگ می‌شود)
        $html_body = \WHCM\Core\Mail::buildPasswordResetTemplate($user_name, $reset_link);
        $sent = \WHCM\Core\Mail::send($email, 'بازنشانی رمز عبور — پُست‌یار', $html_body);

        if (!$sent) {
            error_log('[Postyar] Failed to send password reset email to: ' . $email);
        }

        $this->setFlashMessage('در صورت وجود حساب، دستورالعمل بازنشانی به ایمیل شما ارسال شد.');
        $this->redirect('/');
    }

    /**
     * صفحه و فرم بازنشانی رمز عبور با توکن
     */
    public function showResetPasswordForm() {
        $token = trim($_GET['token'] ?? '');
        if (empty($token)) {
            $this->setFlashMessage('لینک بازنشانی نامعتبر است.');
            $this->redirect('/');
        }

        $this->render('home', [
            'title' => 'بازنشانی رمز عبور | پُست‌یار',
            'csrf_field' => Csrf::field(),
            'message' => $this->getFlashMessage(),
            'reset_token' => $token,
            'show_reset_form' => true,
        ]);
    }

    /**
     * اعمال بازنشانی رمز عبور
     */
    public function handleResetPasswordConfirm() {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            $this->setFlashMessage('خطای امنیتی! توکن نامعتبر است.');
            $this->redirect('/');
        }

        $token = trim($_POST['token'] ?? '');
        $new_pass = $_POST['new_password'] ?? '';
        $confirm_pass = $_POST['confirm_password'] ?? '';

        if (empty($token) || empty($new_pass)) {
            $this->setFlashMessage('تمامی فیلدها الزامی هستند.');
            $this->redirect('/');
        }

        if ($new_pass !== $confirm_pass) {
            $this->setFlashMessage('کلمه عبور جدید با تکرار آن مطابقت ندارد.');
            $this->redirect('/');
        }

        if (strlen($new_pass) < 6) {
            $this->setFlashMessage('کلمه عبور باید حداقل ۶ کاراکتر باشد.');
            $this->redirect('/');
        }

        $db = Bootstrap::getDB();

        // جستجوی توکن معتبر
        $stmt = $db->prepare("SELECT tenant_id, key_value FROM settings WHERE key_name = 'password_reset_token' LIMIT 1");
        $stmt->execute();
        $rows = $stmt->fetchAll();

        foreach ($rows as $row) {
            $parts = explode('|', $row['key_value'], 2);
            if (isset($parts[0]) && hash_equals($parts[0], $token)) {
                // بررسی انقضای توکن
                $expires = $parts[1] ?? '';
                if (!empty($expires) && strtotime($expires) < time()) {
                    $this->setFlashMessage('لینک بازنشانی منقضی شده است. لطفاً دوباره درخواست دهید.');
                    $this->redirect('/');
                }

                // تغییر رمز عبور
                $hashed = password_hash($new_pass, PASSWORD_BCRYPT, ['cost' => 12]);
                $user_id = (int)$row['tenant_id'];
                $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed, $user_id]);

                // حذف توکن استفاده شده
                $stmt = $db->prepare("DELETE FROM settings WHERE tenant_id = ? AND key_name = 'password_reset_token'");
                $stmt->execute([$user_id]);

                $this->setFlashMessage('کلمه عبور شما با موفقیت تغییر یافت. اکنون وارد شوید.');
                $this->redirect('/');
                return;
            }
        }

        $this->setFlashMessage('لینک بازنشانی نامعتبر یا منقضی شده است.');
        $this->redirect('/');
    }

    public function handleSuspendUser(){ return (new \WHCM\Modules\Users\Controllers\UserController)->suspend(); }
    public function handleActivateUser(){ return (new \WHCM\Modules\Users\Controllers\UserController)->activate(); }
    public function handleDeleteUser(){ return (new \WHCM\Modules\Users\Controllers\UserController)->delete(); }
    public function handleBroadcastAnnouncement(){ return (new \WHCM\Modules\Support\Controllers\BroadcastController)->announce(); }
    public function handleWipeTestData(){ return (new \WHCM\Modules\Users\Controllers\UserController)->wipeTestData(); }
    /**
     * ذخیره تنظیمات شماره کارت بانکی عمومی توسط سوپر ادمین
     */
    public function handleSaveBankSettings() {
        $this->checkSuperAdmin();
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            $this->setFlashMessage('خطای امنیتی! توکن نامعتبر است.');
            $this->redirect('/hnnh');
        }

        $card_number = trim($_POST['card_number'] ?? '');
        $card_holder = trim($_POST['card_holder'] ?? '');
        $bank_name = trim($_POST['bank_name'] ?? '');
        
        $support_tele = trim($_POST['support_telegram_url'] ?? '');
        $support_bale = trim($_POST['support_bale_url'] ?? '');
        $support_email = trim($_POST['support_email'] ?? '');

        if (empty($card_number) || empty($card_holder)) {
            $this->setFlashMessage('شماره کارت و نام صاحب حساب الزامی هستند.');
            $this->redirect('/hnnh');
        }

        $bank_settings = [
            'admin_card_number' => $card_number,
            'admin_card_holder' => $card_holder,
            'admin_bank_name' => $bank_name,
            'support_telegram_url' => $support_tele,
            'support_bale_url' => $support_bale,
            'support_email' => $support_email
        ];

        $this->saveSettingsBatch(0, $bank_settings);

        $this->setFlashMessage('تنظیمات کارت بانکی و راه‌های ارتباطی با موفقیت بروزرسانی شد! 💳✔');
        $this->redirect('/hnnh');
    }

    public function handleAddUserManual(){ return (new \WHCM\Modules\Users\Controllers\UserController)->addManual(); }
    public function handleGrantSubscriptionManual(){ return (new \WHCM\Modules\Users\Controllers\UserController)->grantSubscription(); }
    /**
     * ذخیره‌سازی تنظیمات اتوماسیون پیشرفته توسط کاربر (مستأجر)
     */
    public function handleSaveAdvancedSettings() {
        $this->checkAuth();
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            $this->setFlashMessage('خطای امنیتی! توکن نامعتبر است.');
            $this->redirect('/dashboard');
        }

        $tenant_id = Auth::tenantId();

        // ذخیره‌سازی فیلدهای دریافتی در جدول تنظیمات اختصاصی مستأجر
        $fields = [
            'auto_publish_woo' => isset($_POST['auto_publish_woo']) ? 'yes' : 'no',
            'watermark_active' => isset($_POST['watermark_active']) ? 'yes' : 'no',
            'caption_format' => trim($_POST['caption_format'] ?? 'plain'),
            'inbound_method' => trim($_POST['inbound_method'] ?? 'polling'),
            'poll_interval' => trim($_POST['poll_interval'] ?? 'every_1_minute'),
            'ai_api_key' => trim($_POST['ai_api_key'] ?? ''),
            'ai_model' => trim($_POST['ai_model'] ?? 'gpt-4o'),
            'ai_api_url' => trim($_POST['ai_api_url'] ?? 'https://api.openai.com/v1/chat/completions'),
            
            'link_1_name' => trim($_POST['link_1_name'] ?? '📢 کانال تلگرام'),
            'link_1_url' => trim($_POST['link_1_url'] ?? ''),
            'link_2_name' => trim($_POST['link_2_name'] ?? '💬 کانال بله'),
            'link_2_url' => trim($_POST['link_2_url'] ?? ''),
            'link_3_name' => trim($_POST['link_3_name'] ?? '🌐 خرید آنلاین از سایت'),
            'link_3_url' => trim($_POST['link_3_url'] ?? ''),
            
            'btn_1_text' => trim($_POST['btn_1_text'] ?? '🛒 خرید آنلاین از سایت'),
            'btn_2_text' => trim($_POST['btn_2_text'] ?? '💎 پشتیبانی VIP'),
            'btn_2_url' => trim($_POST['btn_2_url'] ?? ''),
            'btn_3_text' => trim($_POST['btn_3_text'] ?? '📢 هومن وب'),
            'btn_3_url' => trim($_POST['btn_3_url'] ?? '')
        ];

        $this->saveSettingsBatch($tenant_id, $fields);

        $this->setFlashMessage('تنظیمات اتوماسیون و پیوند‌های اختصاصی با موفقیت بروزرسانی شد! ✔🤖');
        $this->redirect('/dashboard');
    }

    /* =============================================================
     * متدهای مشترک (checkAuth، checkSuperAdmin، redirect، setFlashMessage،
     * getFlashMessage، render، uploadAndConvertToWebp، jalaliToGregorian، saveSetting)
     * در BaseController قرار دارند.
     * ============================================================= */
}
