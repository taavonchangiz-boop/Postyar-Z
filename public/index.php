<?php
/**
 * فایل ورودی اصلی سامانه مستقل مدیریت کانال‌ها (SaaS)
 *
 * @package WHCM_SaaS
 */

require_once __DIR__ . '/../app/Core/Bootstrap.php';

use WHCM\Core\Bootstrap;
use WHCM\Core\Router;

// راه‌اندازی و بوت‌استرپ سامانه
Bootstrap::run();

// قدم ۱ — بارگذاری اسکلت ماژولار (بدون تغییر رفتار — ایمن حتی اگر فایل روی هاست نباشد)
$__moduleLoader = __DIR__ . '/../app/Modules/ModuleLoader.php';
if (file_exists($__moduleLoader)) {
    require_once $__moduleLoader;
    if (class_exists('\\WHCM\\Modules\\ModuleLoader')) {
        \WHCM\Modules\ModuleLoader::load();
    }
}

// ثبت مسیرهای کاربری (مستاجرین) و لایه عمومی سامانه
Router::get('/', 'MainController@index');
Router::post('/login', 'MainController@handleLogin');
Router::post('/register', 'MainController@handleRegister');
Router::get('/logout', 'MainController@logout');

Router::get('/dashboard', 'MainController@dashboard');
Router::post('/dashboard/add-post', 'MainController@handleCreatePost');
Router::post('/dashboard/add-channel', 'MainController@handleAddChannel');
Router::post('/dashboard/edit-channel', 'MainController@handleEditChannel');
Router::post('/dashboard/delete-channel', 'MainController@handleDeleteChannel');
Router::post('/dashboard/submit-payment', 'MainController@handlePaymentSubmit');
Router::post('/dashboard/update-profile', 'MainController@handleUpdateProfile');
Router::post('/dashboard/change-password', 'MainController@handleChangePassword');
Router::post('/dashboard/save-gold-settings', 'MainController@handleSaveGoldSettings');
Router::post('/dashboard/save-advanced-settings', 'MainController@handleSaveAdvancedSettings');
Router::post('/dashboard/trigger-gold-publish', 'MainController@handleTriggerGoldPublish');
Router::post('/dashboard/add-auto-reply', 'MainController@handleAddAutoReply');
Router::post('/dashboard/delete-auto-reply', 'MainController@handleDeleteAutoReply');
Router::post('/dashboard/add-ticket', 'MainController@handleCreateTicket');
Router::post('/reset-password', 'MainController@handleResetPassword');

// ثبت مسیرهای مدیریت کل پلتفرم (سوپر ادمین)
Router::get('/hnnh', 'MainController@admin');
Router::post('/hnnh/reply-ticket', 'MainController@handleReplyTicket');
Router::post('/hnnh/delete-plan', 'MainController@handleDeletePlan');
Router::post('/hnnh/edit-plan', 'MainController@handleEditPlan');
Router::post('/hnnh/approve-payment', 'MainController@handleApprovePayment');
Router::post('/hnnh/create-plan', 'MainController@handleCreatePlan');
Router::post('/hnnh/delete-user', 'MainController@handleDeleteUser');
Router::post('/hnnh/suspend-user', 'MainController@handleSuspendUser');
Router::post('/hnnh/activate-user', 'MainController@handleActivateUser');
Router::post('/hnnh/wipe-test-data', 'MainController@handleWipeTestData');
Router::post('/hnnh/broadcast-announcement', 'MainController@handleBroadcastAnnouncement');
Router::post('/hnnh/save-bank-settings', 'MainController@handleSaveBankSettings');
Router::post('/hnnh/add-user-manual', 'MainController@handleAddUserManual');
Router::post('/hnnh/grant-subscription-manual', 'MainController@handleGrantSubscriptionManual');

// ثبت مسیرهای ردیابی کلیک و وب‌هوک پیام‌رسان‌ها
Router::get('/click', 'MainController@handleClick');
Router::post('/api/webhook', 'MainController@handleApiWebhook');

// پردازش درخواست جاری
Router::dispatch();
