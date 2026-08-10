<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title><?php echo htmlspecialchars($title ?? 'آموزش استفاده از پُست‌یار'); ?> | پُست‌یار</title>
    <meta name="theme-color" content="#6366f1">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/icons/favicon-16x16.png">
    <style>
        @font-face {
            font-family: 'Vazirmatn';
            src: url('/assets/fonts/Vazirmatn-Regular.woff2') format('woff2');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Vazirmatn';
            src: url('/assets/fonts/Vazirmatn-Medium.woff2') format('woff2');
            font-weight: 500;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Vazirmatn';
            src: url('/assets/fonts/Vazirmatn-Bold.woff2') format('woff2');
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Vazirmatn';
            src: url('/assets/fonts/Vazirmatn-Black.woff2') format('woff2');
            font-weight: 900;
            font-style: normal;
            font-display: swap;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Vazirmatn', system-ui, -apple-system, sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
            line-height: 1.8;
            direction: rtl;
            min-height: 100vh;
        }
        a { color: #818cf8; text-decoration: none; transition: color 0.2s; }
        a:hover { color: #a5b4fc; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
        .container {
            max-width: 860px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        @media (min-width: 640px) {
            .container { padding: 0 1.5rem; }
        }
        @media (min-width: 768px) {
            .container { padding: 0 2rem; }
        }
    </style>
</head>
<body>

<!-- Back to Dashboard Header -->
<header style="position:sticky; top:0; z-index:100; background:rgba(15,23,42,0.92); backdrop-filter:blur(16px); -webkit-backdrop-filter:blur(16px); border-bottom:1px solid #334155; padding:0.75rem 0;">
    <div class="container" style="display:flex; align-items:center; justify-content:space-between;">
        <a href="<?php echo \WHCM\Core\Bootstrap::getRouteUrl('/dashboard'); ?>" style="display:flex; align-items:center; gap:0.5rem; color:#94a3b8; font-size:0.9rem; font-weight:500; padding:0.4rem 0.8rem; border-radius:8px; border:1px solid #334155; transition:all 0.2s;"
           onmouseover="this.style.borderColor='#6366f1'; this.style.color='#f8fafc';"
           onmouseout="this.style.borderColor='#334155'; this.style.color='#94a3b8';">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="transform:scaleX(-1);"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            بازگشت به داشبورد
        </a>
        <span style="font-size:0.8rem; color:#64748b;">پُست‌یار</span>
    </div>
</header>

<!-- Hero Section -->
<section style="padding:3rem 0 1.5rem; text-align:center;">
    <div class="container">
        <div style="display:inline-flex; align-items:center; justify-content:center; width:64px; height:64px; border-radius:16px; background:linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); box-shadow:0 8px 32px rgba(99,102,241,0.3); margin-bottom:1.25rem;">
            <svg width="32" height="32" fill="none" stroke="#ffffff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <h1 style="font-size:1.75rem; font-weight:900; color:#f8fafc; margin-bottom:0.5rem; line-height:1.4;">آموزش استفاده از پُست‌یار</h1>
        <p style="font-size:0.95rem; color:#94a3b8; max-width:560px; margin:0 auto; line-height:1.9;">راهنمای کامل و گام‌به‌گام برای استفاده از تمام امکانات سامانه هوشمند مدیریت کانال‌های تلگرام و بله</p>
    </div>
</section>

<!-- Table of Contents -->
<section style="padding:0 0 2rem;">
    <div class="container">
        <div style="background:#1e293b; border:1px solid #334155; border-radius:16px; padding:1.5rem; box-shadow:0 4px 24px rgba(0,0,0,0.2);">
            <h2 style="font-size:1rem; font-weight:700; color:#6366f1; margin-bottom:1rem; display:flex; align-items:center; gap:0.5rem;">
                <span>📑</span> فهرست مطالب
            </h2>
            <div style="display:grid; grid-template-columns:1fr; gap:0.5rem;">
                <a href="#step-1" style="display:flex; align-items:center; gap:0.6rem; padding:0.5rem 0.75rem; border-radius:8px; font-size:0.88rem; color:#cbd5e1; transition:all 0.2s;"
                   onmouseover="this.style.background='rgba(99,102,241,0.1)'; this.style.color='#f8fafc';"
                   onmouseout="this.style.background='transparent'; this.style.color='#cbd5e1';">
                    <span style="display:flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:8px; background:rgba(99,102,241,0.15); color:#818cf8; font-size:0.75rem; font-weight:700; flex-shrink:0;">۱</span>
                    ثبت‌نام و ورود
                </a>
                <a href="#step-2" style="display:flex; align-items:center; gap:0.6rem; padding:0.5rem 0.75rem; border-radius:8px; font-size:0.88rem; color:#cbd5e1; transition:all 0.2s;"
                   onmouseover="this.style.background='rgba(16,185,129,0.1)'; this.style.color='#f8fafc';"
                   onmouseout="this.style.background='transparent'; this.style.color='#cbd5e1';">
                    <span style="display:flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:8px; background:rgba(16,185,129,0.15); color:#10b981; font-size:0.75rem; font-weight:700; flex-shrink:0;">۲</span>
                    افزودن کانال تلگرام و بله
                </a>
                <a href="#step-3" style="display:flex; align-items:center; gap:0.6rem; padding:0.5rem 0.75rem; border-radius:8px; font-size:0.88rem; color:#cbd5e1; transition:all 0.2s;"
                   onmouseover="this.style.background='rgba(245,158,11,0.1)'; this.style.color='#f8fafc';"
                   onmouseout="this.style.background='transparent'; this.style.color='#cbd5e1';">
                    <span style="display:flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:8px; background:rgba(245,158,11,0.15); color:#f59e0b; font-size:0.75rem; font-weight:700; flex-shrink:0;">۳</span>
                    تنظیم لینک در کانال
                </a>
                <a href="#step-4" style="display:flex; align-items:center; gap:0.6rem; padding:0.5rem 0.75rem; border-radius:8px; font-size:0.88rem; color:#cbd5e1; transition:all 0.2s;"
                   onmouseover="this.style.background='rgba(239,68,68,0.1)'; this.style.color='#f8fafc';"
                   onmouseout="this.style.background='transparent'; this.style.color='#cbd5e1';">
                    <span style="display:flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:8px; background:rgba(239,68,68,0.15); color:#ef4444; font-size:0.75rem; font-weight:700; flex-shrink:0;">۴</span>
                    ارسال پست جدید
                </a>
                <a href="#step-5" style="display:flex; align-items:center; gap:0.6rem; padding:0.5rem 0.75rem; border-radius:8px; font-size:0.88rem; color:#cbd5e1; transition:all 0.2s;"
                   onmouseover="this.style.background='rgba(99,102,241,0.1)'; this.style.color='#f8fafc';"
                   onmouseout="this.style.background='transparent'; this.style.color='#cbd5e1';">
                    <span style="display:flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:8px; background:rgba(99,102,241,0.15); color:#818cf8; font-size:0.75rem; font-weight:700; flex-shrink:0;">۵</span>
                    زمانبندی ارسال پست‌ها
                </a>
                <a href="#step-6" style="display:flex; align-items:center; gap:0.6rem; padding:0.5rem 0.75rem; border-radius:8px; font-size:0.88rem; color:#cbd5e1; transition:all 0.2s;"
                   onmouseover="this.style.background='rgba(16,185,129,0.1)'; this.style.color='#f8fafc';"
                   onmouseout="this.style.background='transparent'; this.style.color='#cbd5e1';">
                    <span style="display:flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:8px; background:rgba(16,185,129,0.15); color:#10b981; font-size:0.75rem; font-weight:700; flex-shrink:0;">۶</span>
                    مدیریت صندوق پیام
                </a>
                <a href="#step-7" style="display:flex; align-items:center; gap:0.6rem; padding:0.5rem 0.75rem; border-radius:8px; font-size:0.88rem; color:#cbd5e1; transition:all 0.2s;"
                   onmouseover="this.style.background='rgba(245,158,11,0.1)'; this.style.color='#f8fafc';"
                   onmouseout="this.style.background='transparent'; this.style.color='#cbd5e1';">
                    <span style="display:flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:8px; background:rgba(245,158,11,0.15); color:#f59e0b; font-size:0.75rem; font-weight:700; flex-shrink:0;">۷</span>
                    سیستم تیکت پشتیبانی
                </a>
                <a href="#step-8" style="display:flex; align-items:center; gap:0.6rem; padding:0.5rem 0.75rem; border-radius:8px; font-size:0.88rem; color:#cbd5e1; transition:all 0.2s;"
                   onmouseover="this.style.background='rgba(239,68,68,0.1)'; this.style.color='#f8fafc';"
                   onmouseout="this.style.background='transparent'; this.style.color='#cbd5e1';">
                    <span style="display:flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:8px; background:rgba(239,68,68,0.15); color:#ef4444; font-size:0.75rem; font-weight:700; flex-shrink:0;">۸</span>
                    مدیریت اشتراک و پرداخت
                </a>
                <a href="#step-9" style="display:flex; align-items:center; gap:0.6rem; padding:0.5rem 0.75rem; border-radius:8px; font-size:0.88rem; color:#cbd5e1; transition:all 0.2s;"
                   onmouseover="this.style.background='rgba(99,102,241,0.1)'; this.style.color='#f8fafc';"
                   onmouseout="this.style.background='transparent'; this.style.color='#cbd5e1';">
                    <span style="display:flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:8px; background:rgba(99,102,241,0.15); color:#818cf8; font-size:0.75rem; font-weight:700; flex-shrink:0;">۹</span>
                    زیرمجموعه‌گیری و کیف پول
                </a>
                <a href="#step-10" style="display:flex; align-items:center; gap:0.6rem; padding:0.5rem 0.75rem; border-radius:8px; font-size:0.88rem; color:#cbd5e1; transition:all 0.2s;"
                   onmouseover="this.style.background='rgba(16,185,129,0.1)'; this.style.color='#f8fafc';"
                   onmouseout="this.style.background='transparent'; this.style.color='#cbd5e1';">
                    <span style="display:flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:8px; background:rgba(16,185,129,0.15); color:#10b981; font-size:0.75rem; font-weight:700; flex-shrink:0;">۱۰</span>
                    تنظیمات پیشرفته و هوش مصنوعی
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Content Sections -->
<main style="padding:0 0 3rem;">
    <div class="container" style="display:flex; flex-direction:column; gap:1.5rem;">

        <!-- ====== STEP 1: Registration & Login ====== -->
        <section id="step-1" style="background:#1e293b; border:1px solid #334155; border-radius:16px; padding:1.75rem; box-shadow:0 4px 24px rgba(0,0,0,0.15);">
            <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1.25rem;">
                <span style="display:flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg, #6366f1 0%, #818cf8 100%); font-size:1.3rem; flex-shrink:0;">🔐</span>
                <div>
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.15rem;">
                        <span style="font-size:0.7rem; color:#6366f1; background:rgba(99,102,241,0.12); padding:0.15rem 0.5rem; border-radius:6px; font-weight:700;">مرحله ۱</span>
                    </div>
                    <h2 style="font-size:1.15rem; font-weight:800; color:#f8fafc;">ثبت‌نام و ورود</h2>
                </div>
            </div>
            <div style="color:#cbd5e1; font-size:0.9rem; line-height:2;">
                <p style="margin-bottom:1rem;">برای شروع استفاده از پُست‌یار، ابتدا باید یک حساب کاربری ایجاد کنید. فرآیند ثبت‌نام بسیار ساده است:</p>
                <div style="background:rgba(99,102,241,0.06); border:1px solid rgba(99,102,241,0.15); border-radius:12px; padding:1.25rem; margin-bottom:1rem;">
                    <p style="font-weight:700; color:#818cf8; margin-bottom:0.5rem;">📝 مراحل ثبت‌نام:</p>
                    <ol style="padding-right:1.25rem; display:flex; flex-direction:column; gap:0.4rem;">
                        <li>به صفحه اصلی پُست‌یار مراجعه کنید و روی تب <strong style="color:#f8fafc;">«ثبت‌نام»</strong> کلیک کنید.</li>
                        <li>نام، ایمیل و رمز عبور خود را وارد کنید (رمز عبور حداقل ۶ کاراکتر).</li>
                        <li>سوال امنیتی (کپچای ریاضی) را پاسخ دهید تا از ربات‌ها جلوگیری شود.</li>
                        <li>روی دکمه <strong style="color:#10b981;">«ثبت‌نام»</strong> کلیک کنید. پس از ثبت‌نام، به‌طور خودکار وارد داشبورد می‌شوید.</li>
                    </ol>
                </div>
                <div style="background:rgba(99,102,241,0.06); border:1px solid rgba(99,102,241,0.15); border-radius:12px; padding:1.25rem; margin-bottom:1rem;">
                    <p style="font-weight:700; color:#818cf8; margin-bottom:0.5rem;">🔑 نحوه ورود:</p>
                    <p>با ایمیل و رمز عبوری که هنگام ثبت‌نام وارد کردید، از تب <strong style="color:#f8fafc;">«ورود»</strong> وارد شوید. در صورت فراموشی رمز عبور، روی لینک <strong style="color:#f59e0b;">«فراموشی رمز»</strong> کلیک کنید و ایمیل خود را وارد نمایید تا لینک بازیابی ارسال شود.</p>
                </div>
                <div style="background:rgba(245,158,11,0.08); border:1px solid rgba(245,158,11,0.2); border-radius:10px; padding:1rem; display:flex; gap:0.6rem; align-items:flex-start;">
                    <span style="font-size:1.1rem; flex-shrink:0;">💡</span>
                    <p style="color:#fbbf24; font-size:0.85rem; margin:0;"><strong>نکته مهم:</strong> اگر ایمیل بازیابی را در صندوق ورودی پیدا نکردید، پوشه <strong>Spam</strong> (هرزنامه) را نیز بررسی کنید. همچنین امکان بازیابی رمز از طریق پیامک وجود دارد.</p>
                </div>
            </div>
        </section>

        <!-- ====== STEP 2: Adding Telegram & Bale Channels ====== -->
        <section id="step-2" style="background:#1e293b; border:1px solid #334155; border-radius:16px; padding:1.75rem; box-shadow:0 4px 24px rgba(0,0,0,0.15);">
            <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1.25rem;">
                <span style="display:flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg, #10b981 0%, #34d399 100%); font-size:1.3rem; flex-shrink:0;">📻</span>
                <div>
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.15rem;">
                        <span style="font-size:0.7rem; color:#10b981; background:rgba(16,185,129,0.12); padding:0.15rem 0.5rem; border-radius:6px; font-weight:700;">مرحله ۲</span>
                    </div>
                    <h2 style="font-size:1.15rem; font-weight:800; color:#f8fafc;">افزودن کانال تلگرام و بله</h2>
                </div>
            </div>
            <div style="color:#cbd5e1; font-size:0.9rem; line-height:2;">
                <p style="margin-bottom:1rem;">پُست‌یار از هر دو پلتفرم <strong style="color:#f8fafc;">تلگرام</strong> و <strong style="color:#f8fafc;">بله</strong> پشتیبانی می‌کند. برای اتصال هر کانال باید یک ربات بسازید و توکن آن را وارد کنید.</p>
                <div style="background:rgba(16,185,129,0.06); border:1px solid rgba(16,185,129,0.15); border-radius:12px; padding:1.25rem; margin-bottom:1rem;">
                    <p style="font-weight:700; color:#34d399; margin-bottom:0.5rem;">🤖 ساخت ربات در BotFather (تلگرام):</p>
                    <ol style="padding-right:1.25rem; display:flex; flex-direction:column; gap:0.4rem;">
                        <li>در تلگرام به <strong style="color:#f8fafc;">@BotFather</strong> پیام دهید.</li>
                        <li>دستور <code style="background:#0f172a; padding:0.15rem 0.5rem; border-radius:6px; font-size:0.85rem; color:#818cf8;">/newbot</code> را ارسال کنید.</li>
                        <li>یک نام برای ربات انتخاب کنید (مثلاً: ربات مدیریت کانال من).</li>
                        <li>یک نام کاربری (username) برای ربات انتخاب کنید که به <code style="background:#0f172a; padding:0.15rem 0.5rem; border-radius:6px; font-size:0.85rem; color:#818cf8;">_bot</code> ختم شود.</li>
                        <li>BotFather یک <strong style="color:#f8fafc;">توکن (Token)</strong> به شما می‌دهد. آن را کپی کنید.</li>
                        <li>ربات را به‌عنوان <strong style="color:#f8fafc;">ادمین (Administrator)</strong> در کانال خود اضافه کنید.</li>
                    </ol>
                </div>
                <div style="background:rgba(16,185,129,0.06); border:1px solid rgba(16,185,129,0.15); border-radius:12px; padding:1.25rem; margin-bottom:1rem;">
                    <p style="font-weight:700; color:#34d399; margin-bottom:0.5rem;">📡 ساخت ربات در بله:</p>
                    <ol style="padding-right:1.25rem; display:flex; flex-direction:column; gap:0.4rem;">
                        <li>به <strong style="color:#f8fafc;">ربات‌ساز بله</strong> مراجعه کنید و یک ربات جدید بسازید.</li>
                        <li>توکن ربات را کپی کنید.</li>
                        <li>ربات را به‌عنوان ادمین در کانال بله خود اضافه کنید.</li>
                    </ol>
                </div>
                <div style="background:rgba(16,185,129,0.06); border:1px solid rgba(16,185,129,0.15); border-radius:12px; padding:1.25rem; margin-bottom:1rem;">
                    <p style="font-weight:700; color:#34d399; margin-bottom:0.5rem;">➕ افزودن کانال در داشبورد:</p>
                    <ol style="padding-right:1.25rem; display:flex; flex-direction:column; gap:0.4rem;">
                        <li>در داشبورد، به بخش <strong style="color:#f8fafc;">«مدیریت کانال‌ها»</strong> بروید.</li>
                        <li>روی دکمه <strong style="color:#10b981;">«افزودن کانال»</strong> کلیک کنید.</li>
                        <li>نام کانال، آیدی کانال (مثلاً <code style="background:#0f172a; padding:0.15rem 0.5rem; border-radius:6px; font-size:0.85rem; color:#818cf8;">@mychannel</code>)، توکن ربات و نوع پلتفرم (تلگرام/بله) را وارد کنید.</li>
                        <li>پُست‌یار به‌طور خودکار وب‌هوک را تنظیم می‌کند.</li>
                    </ol>
                </div>
                <div style="background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.2); border-radius:10px; padding:1rem; display:flex; gap:0.6rem; align-items:flex-start;">
                    <span style="font-size:1.1rem; flex-shrink:0;">⚠️</span>
                    <p style="color:#fca5a5; font-size:0.85rem; margin:0;"><strong>هشدار مهم:</strong> ربات حتماً باید دسترسی <strong>ارسال پیام (Send Messages)</strong> و <strong>ویرایش پیام (Edit Messages)</strong> در کانال داشته باشد. بدون این دسترسی‌ها، پست‌ها ارسال نخواهند شد.</p>
                </div>
            </div>
        </section>

        <!-- ====== STEP 3: Channel Link Settings ====== -->
        <section id="step-3" style="background:#1e293b; border:1px solid #334155; border-radius:16px; padding:1.75rem; box-shadow:0 4px 24px rgba(0,0,0,0.15);">
            <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1.25rem;">
                <span style="display:flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); font-size:1.3rem; flex-shrink:0;">🔗</span>
                <div>
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.15rem;">
                        <span style="font-size:0.7rem; color:#f59e0b; background:rgba(245,158,11,0.12); padding:0.15rem 0.5rem; border-radius:6px; font-weight:700;">مرحله ۳</span>
                    </div>
                    <h2 style="font-size:1.15rem; font-weight:800; color:#f8fafc;">تنظیم لینک در کانال</h2>
                </div>
            </div>
            <div style="color:#cbd5e1; font-size:0.9rem; line-height:2;">
                <p style="margin-bottom:1rem;">یکی از امکانات کلیدی پُست‌یار، سیستم <strong style="color:#f8fafc;">ردیابی لینک (Link Tracking)</strong> است. با این قابلیت می‌توانید لینک‌های موجود در پست‌ها را مدیریت و آمار کلیک آن‌ها را مشاهده کنید.</p>
                <div style="background:rgba(245,158,11,0.06); border:1px solid rgba(245,158,11,0.15); border-radius:12px; padding:1.25rem; margin-bottom:1rem;">
                    <p style="font-weight:700; color:#fbbf24; margin-bottom:0.5rem;">⚙️ نحوه تنظیم link_config:</p>
                    <ol style="padding-right:1.25rem; display:flex; flex-direction:column; gap:0.4rem;">
                        <li>در بخش <strong style="color:#f8fafc;">«مدیریت کانال‌ها»</strong>، روی دکمه ویرایش (✏️) کنار کانال مورد نظر کلیک کنید.</li>
                        <li>در بخش <strong style="color:#f8fafc;">«تنظیم لینک»</strong>، آدرس لینک پیش‌فرض را وارد کنید.</li>
                        <li>می‌توانید لینک‌های جایگزین نیز تعریف کنید که به‌صورت تصادفی یا چرخشی در پست‌ها قرار می‌گیرند.</li>
                        <li>پس از ذخیره، تمام پست‌های جدید از این تنظیمات استفاده خواهند کرد.</li>
                    </ol>
                </div>
                <div style="background:rgba(245,158,11,0.08); border:1px solid rgba(245,158,11,0.2); border-radius:10px; padding:1rem; display:flex; gap:0.6rem; align-items:flex-start;">
                    <span style="font-size:1.1rem; flex-shrink:0;">💡</span>
                    <p style="color:#fbbf24; font-size:0.85rem; margin:0;"><strong>نکته:</strong> برای مشاهده آمار کلیک لینک‌ها، به بخش <strong>لینک‌استتس</strong> در داشبورد مراجعه کنید. آمار شامل تعداد کلیک، تاریخ و منبع کلیک‌کنندگان است.</p>
                </div>
            </div>
        </section>

        <!-- ====== STEP 4: Creating New Posts ====== -->
        <section id="step-4" style="background:#1e293b; border:1px solid #334155; border-radius:16px; padding:1.75rem; box-shadow:0 4px 24px rgba(0,0,0,0.15);">
            <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1.25rem;">
                <span style="display:flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg, #ef4444 0%, #f87171 100%); font-size:1.3rem; flex-shrink:0;">✉️</span>
                <div>
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.15rem;">
                        <span style="font-size:0.7rem; color:#ef4444; background:rgba(239,68,68,0.12); padding:0.15rem 0.5rem; border-radius:6px; font-weight:700;">مرحله ۴</span>
                    </div>
                    <h2 style="font-size:1.15rem; font-weight:800; color:#f8fafc;">ارسال پست جدید</h2>
                </div>
            </div>
            <div style="color:#cbd5e1; font-size:0.9rem; line-height:2;">
                <p style="margin-bottom:1rem;">بخش <strong style="color:#f8fafc;">«ارسال پست جدید»</strong> قلب اصلی پُست‌یار است. در این بخش می‌توانید پست‌های متنی، تصویری و حتی پست‌های تولیدشده با هوش مصنوعی ایجاد کنید.</p>
                <div style="background:rgba(239,68,68,0.06); border:1px solid rgba(239,68,68,0.15); border-radius:12px; padding:1.25rem; margin-bottom:1rem;">
                    <p style="font-weight:700; color:#f87171; margin-bottom:0.5rem;">📝 مراحل ارسال پست:</p>
                    <ol style="padding-right:1.25rem; display:flex; flex-direction:column; gap:0.4rem;">
                        <li>از منوی سایدبار، بخش <strong style="color:#f8fafc;">«ارسال پست جدید»</strong> را انتخاب کنید.</li>
                        <li><strong style="color:#f8fafc;">کانال مقصد</strong> را از لیست کشویی انتخاب کنید (می‌توانید چند کانال را همزمان انتخاب کنید).</li>
                        <li>متن پست خود را در ویرایشگر بنویسید. از فرمت‌بندی‌های <strong style="color:#818cf8;">Bold</strong>، <em style="color:#818cf8;">Italic</em> و لینک پشتیبانی می‌شود.</li>
                        <li>در صورت نیاز، <strong style="color:#f8fafc;">تصویر</strong> مورد نظر را آپلود کنید (فرمت‌های مجاز: JPG, PNG, GIF, WebP — حداکثر ۵ مگابایت).</li>
                        <li>روی دکمه <strong style="color:#10b981;">«انتشار»</strong> کلیک کنید تا پست فوراً ارسال شود.</li>
                    </ol>
                </div>
                <div style="background:rgba(239,68,68,0.06); border:1px solid rgba(239,68,68,0.15); border-radius:12px; padding:1.25rem; margin-bottom:1rem;">
                    <p style="font-weight:700; color:#f87171; margin-bottom:0.5rem;">🤖 تولید محتوا با هوش مصنوعی:</p>
                    <p>در ویرایشگر پست، دکمه <strong style="color:#818cf8;">«تولید با AI»</strong> وجود دارد. با وارد کردن موضوع مورد نظر، هوش مصنوعی یک متن حرفه‌ای برای شما تولید می‌کند. این قابلیت نیازمند تنظیم کلید API در بخش تنظیمات پیشرفته است.</p>
                </div>
                <div style="background:rgba(245,158,11,0.08); border:1px solid rgba(245,158,11,0.2); border-radius:10px; padding:1rem; display:flex; gap:0.6rem; align-items:flex-start;">
                    <span style="font-size:1.1rem; flex-shrink:0;">💡</span>
                    <p style="color:#fbbf24; font-size:0.85rem; margin:0;"><strong>نکته:</strong> هر پلن اشتراک محدودیت تعداد ارسال روزانه/ماهانه دارد. باجه سهمیه (کوئوتا) در بالای داشبورد نمایش داده می‌شود.</p>
                </div>
            </div>
        </section>

        <!-- ====== STEP 5: Scheduling Posts ====== -->
        <section id="step-5" style="background:#1e293b; border:1px solid #334155; border-radius:16px; padding:1.75rem; box-shadow:0 4px 24px rgba(0,0,0,0.15);">
            <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1.25rem;">
                <span style="display:flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg, #6366f1 0%, #a78bfa 100%); font-size:1.3rem; flex-shrink:0;">⏰</span>
                <div>
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.15rem;">
                        <span style="font-size:0.7rem; color:#6366f1; background:rgba(99,102,241,0.12); padding:0.15rem 0.5rem; border-radius:6px; font-weight:700;">مرحله ۵</span>
                    </div>
                    <h2 style="font-size:1.15rem; font-weight:800; color:#f8fafc;">زمانبندی ارسال پست‌ها</h2>
                </div>
            </div>
            <div style="color:#cbd5e1; font-size:0.9rem; line-height:2;">
                <p style="margin-bottom:1rem;">با قابلیت <strong style="color:#f8fafc;">زمانبندی (Schedule)</strong>، می‌توانید پست‌ها را از قبل آماده کنید و در تاریخ و ساعت مشخصی به‌صورت خودکار منتشر شوند.</p>
                <div style="background:rgba(99,102,241,0.06); border:1px solid rgba(99,102,241,0.15); border-radius:12px; padding:1.25rem; margin-bottom:1rem;">
                    <p style="font-weight:700; color:#a78bfa; margin-bottom:0.5rem;">📅 نحوه زمانبندی:</p>
                    <ol style="padding-right:1.25rem; display:flex; flex-direction:column; gap:0.4rem;">
                        <li>در فرم ارسال پست، فیلد <strong style="color:#f8fafc;">«تاریخ ارسال»</strong> را پیدا کنید.</li>
                        <li>با کلیک روی فیلد، <strong style="color:#f8fafc;">تقویم شمسی (جلالی)</strong> باز می‌شود. تاریخ و ساعت مورد نظر را انتخاب کنید.</li>
                        <li>ساعت ارسال را نیز به‌صورت دستی وارد کنید (مثلاً: <code style="background:#0f172a; padding:0.15rem 0.5rem; border-radius:6px; font-size:0.85rem; color:#818cf8;">14:30</code>).</li>
                        <li>پست را ذخیره کنید. وضعیت پست به <strong style="color:#f59e0b;">«زمان‌بندی‌شده»</strong> تغییر می‌کند.</li>
                        <li>در زمان مقرر، سیستم به‌صورت خودکار پست را در کانال منتشر می‌کند.</li>
                    </ol>
                </div>
                <div style="background:rgba(99,102,241,0.06); border:1px solid rgba(99,102,241,0.15); border-radius:12px; padding:1.25rem; margin-bottom:1rem;">
                    <p style="font-weight:700; color:#a78bfa; margin-bottom:0.5rem;">📊 مدیریت پست‌های زمان‌بندی‌شده:</p>
                    <p>تمام پست‌های زمان‌بندی‌شده در لیست پست‌ها با برچسب <span style="background:rgba(245,158,11,0.15); color:#f59e0b; padding:0.1rem 0.4rem; border-radius:4px; font-size:0.8rem;">⏳ زمان‌بندی</span> نمایش داده می‌شوند. شما می‌توانید آن‌ها را ویرایش یا حذف کنید.</p>
                </div>
                <div style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.2); border-radius:10px; padding:1rem; display:flex; gap:0.6rem; align-items:flex-start;">
                    <span style="font-size:1.1rem; flex-shrink:0;">✅</span>
                    <p style="color:#6ee7b7; font-size:0.85rem; margin:0;"><strong>مزیت:</strong> با زمانبندی، می‌توانید پست‌های یک هفته‌ای را یکجا آماده کنید و سیستم به‌صورت خودکار آن‌ها را منتشر کند. این ویژگی برای کانال‌هایی که نیاز به انتشار منظم دارند بسیار مفید است.</p>
                </div>
            </div>
        </section>

        <!-- ====== STEP 6: Managing Inbox ====== -->
        <section id="step-6" style="background:#1e293b; border:1px solid #334155; border-radius:16px; padding:1.75rem; box-shadow:0 4px 24px rgba(0,0,0,0.15);">
            <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1.25rem;">
                <span style="display:flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg, #10b981 0%, #6ee7b7 100%); font-size:1.3rem; flex-shrink:0;">📩</span>
                <div>
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.15rem;">
                        <span style="font-size:0.7rem; color:#10b981; background:rgba(16,185,129,0.12); padding:0.15rem 0.5rem; border-radius:6px; font-weight:700;">مرحله ۶</span>
                    </div>
                    <h2 style="font-size:1.15rem; font-weight:800; color:#f8fafc;">مدیریت صندوق پیام</h2>
                </div>
            </div>
            <div style="color:#cbd5e1; font-size:0.9rem; line-height:2;">
                <p style="margin-bottom:1rem;">بخش <strong style="color:#f8fafc;">«صندوق پیام»</strong> تمام پیام‌هایی که اعضای کانال به ربات ارسال می‌کنند را نمایش می‌دهد. این بخش برای پاسخگویی به کاربران و مدیریت تعاملات بسیار مفید است.</p>
                <div style="background:rgba(16,185,129,0.06); border:1px solid rgba(16,185,129,0.15); border-radius:12px; padding:1.25rem; margin-bottom:1rem;">
                    <p style="font-weight:700; color:#6ee7b7; margin-bottom:0.5rem;">📋 امکانات صندوق پیام:</p>
                    <ul style="padding-right:1.25rem; display:flex; flex-direction:column; gap:0.4rem; list-style-type:disc;">
                        <li>مشاهده تمام پیام‌های دریافتی با نام فرستنده و تاریخ ارسال</li>
                        <li>فیلتر بر اساس کانال و وضعیت خوانده‌شده</li>
                        <li>پاسخ مستقیم به پیام‌ها (در صورت پشتیبانی ربات)</li>
                        <li>علامت‌گذاری پیام‌ها به‌عنوان خوانده‌شده</li>
                    </ul>
                </div>
                <div style="background:rgba(16,185,129,0.06); border:1px solid rgba(16,185,129,0.15); border-radius:12px; padding:1.25rem; margin-bottom:1rem;">
                    <p style="font-weight:700; color:#6ee7b7; margin-bottom:0.5rem;">🤖 پاسخگوی خودکار (Auto-Responder):</p>
                    <p>در صورت فعال‌بودن این ویژگی در پلن شما، می‌توانید <strong style="color:#f8fafc;">کلمات کلیدی</strong> و <strong style="color:#f8fafc;">پاسخ‌های متناظر</strong> تعریف کنید. وقتی کاربری پیامی حاوی آن کلمه کلیدی ارسال کند، ربات به‌صورت خودکار پاسخ می‌دهد. این قابلیت در سایدبار با نام <strong style="color:#f8fafc;">«پاسخگوی خودکار»</strong> در دسترس است.</p>
                </div>
                <div style="background:rgba(245,158,11,0.08); border:1px solid rgba(245,158,11,0.2); border-radius:10px; padding:1rem; display:flex; gap:0.6rem; align-items:flex-start;">
                    <span style="font-size:1.1rem; flex-shrink:0;">💡</span>
                    <p style="color:#fbbf24; font-size:0.85rem; margin:0;"><strong>نکته:</strong> برای دریافت پیام از کاربران، آن‌ها باید ابتدا ربات شما را در تلگرام/بله <strong>/start</strong> کنند. بدون این کار، ربات نمی‌تواند پیام‌ها را دریافت کند.</p>
                </div>
            </div>
        </section>

        <!-- ====== STEP 7: Support Ticket System ====== -->
        <section id="step-7" style="background:#1e293b; border:1px solid #334155; border-radius:16px; padding:1.75rem; box-shadow:0 4px 24px rgba(0,0,0,0.15);">
            <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1.25rem;">
                <span style="display:flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg, #f59e0b 0%, #fcd34d 100%); font-size:1.3rem; flex-shrink:0;">🎫</span>
                <div>
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.15rem;">
                        <span style="font-size:0.7rem; color:#f59e0b; background:rgba(245,158,11,0.12); padding:0.15rem 0.5rem; border-radius:6px; font-weight:700;">مرحله ۷</span>
                    </div>
                    <h2 style="font-size:1.15rem; font-weight:800; color:#f8fafc;">سیستم تیکت پشتیبانی</h2>
                </div>
            </div>
            <div style="color:#cbd5e1; font-size:0.9rem; line-height:2;">
                <p style="margin-bottom:1rem;">اگر در استفاده از سامانه با مشکلی مواجه شدید یا سؤالی دارید، می‌توانید از طریق <strong style="color:#f8fafc;">سیستم تیکت</strong> با تیم پشتیبانی پُست‌یار ارتباط برقرار کنید.</p>
                <div style="background:rgba(245,158,11,0.06); border:1px solid rgba(245,158,11,0.15); border-radius:12px; padding:1.25rem; margin-bottom:1rem;">
                    <p style="font-weight:700; color:#fcd34d; margin-bottom:0.5rem;">📨 نحوه ایجاد تیکت:</p>
                    <ol style="padding-right:1.25rem; display:flex; flex-direction:column; gap:0.4rem;">
                        <li>به بخش <strong style="color:#f8fafc;">«پشتیبانی و تیکت‌ها»</strong> در سایدبار بروید.</li>
                        <li>روی دکمه <strong style="color:#f59e0b;">«ثبت تیکت جدید»</strong> کلیک کنید.</li>
                        <li>موضوع تیکت را به‌اختصار وارد کنید (مثلاً: مشکل در ارسال پست).</li>
                        <li>توضیحات کامل مشکل یا سؤال خود را بنویسید. هرچه توضیحات دقیق‌تر باشد، پاسخ سریع‌تر خواهد بود.</li>
                        <li>تیکت را ارسال کنید و منتظر پاسخ بمانید.</li>
                    </ol>
                </div>
                <div style="background:rgba(245,158,11,0.06); border:1px solid rgba(245,158,11,0.15); border-radius:12px; padding:1.25rem; margin-bottom:1rem;">
                    <p style="font-weight:700; color:#fcd34d; margin-bottom:0.5rem;">📖 خواندن پاسخ تیکت:</p>
                    <p>پس از ارسال تیکت، در لیست تیکت‌ها می‌توانید وضعیت آن را مشاهده کنید. تیکت‌ها دارای وضعیت‌های <span style="color:#f59e0b;">باز</span>، <span style="color:#6366f1;">در حال بررسی</span> و <span style="color:#10b981;">پاسخ داده‌شده</span> هستند. با کلیک روی هر تیکت، می‌توانید مکاتبه کامل را بخوانید و در صورت نیاز پاسخ دهید.</p>
                </div>
                <div style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.2); border-radius:10px; padding:1rem; display:flex; gap:0.6rem; align-items:flex-start;">
                    <span style="font-size:1.1rem; flex-shrink:0;">✅</span>
                    <p style="color:#6ee7b7; font-size:0.85rem; margin:0;"><strong>راه‌های ارتباط سریع:</strong> در بالای بخش تیکت‌ها، دکمه‌هایی برای تماس مستقیم از طریق <strong>تلگرام</strong>، <strong>واتساپ</strong> و <strong>تماس تلفنی</strong> وجود دارد که در مواقع ضروری می‌توانید استفاده کنید.</p>
                </div>
            </div>
        </section>

        <!-- ====== STEP 8: Subscription & Payment ====== -->
        <section id="step-8" style="background:#1e293b; border:1px solid #334155; border-radius:16px; padding:1.75rem; box-shadow:0 4px 24px rgba(0,0,0,0.15);">
            <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1.25rem;">
                <span style="display:flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg, #ef4444 0%, #fca5a5 100%); font-size:1.3rem; flex-shrink:0;">💎</span>
                <div>
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.15rem;">
                        <span style="font-size:0.7rem; color:#ef4444; background:rgba(239,68,68,0.12); padding:0.15rem 0.5rem; border-radius:6px; font-weight:700;">مرحله ۸</span>
                    </div>
                    <h2 style="font-size:1.15rem; font-weight:800; color:#f8fafc;">مدیریت اشتراک و پرداخت</h2>
                </div>
            </div>
            <div style="color:#cbd5e1; font-size:0.9rem; line-height:2;">
                <p style="margin-bottom:1rem;">پُست‌یار بر اساس <strong style="color:#f8fafc;">پلن‌های اشتراکی</strong> کار می‌کند. هر پلن امکانات متفاوتی دارد و قیمت آن بر اساس مدت و ویژگی‌ها تعیین شده است.</p>
                <div style="background:rgba(239,68,68,0.06); border:1px solid rgba(239,68,68,0.15); border-radius:12px; padding:1.25rem; margin-bottom:1rem;">
                    <p style="font-weight:700; color:#fca5a5; margin-bottom:0.5rem;">📋 انتخاب و خرید پلن:</p>
                    <ol style="padding-right:1.25rem; display:flex; flex-direction:column; gap:0.4rem;">
                        <li>به بخش <strong style="color:#f8fafc;">«خرید اشتراک»</strong> در سایدبار بروید.</li>
                        <li>لیست پلن‌های موجود را مشاهده کنید. هر پلن شامل قیمت، مدت و لیست امکانات است.</li>
                        <li>پلن مورد نظر خود را انتخاب و روی <strong style="color:#10b981;">«خرید»</strong> کلیک کنید.</li>
                        <li><strong style="color:#f8fafc;">شماره کارت</strong> بانکی مقصد نمایش داده می‌شود. مبلغ را واریز کنید.</li>
                        <li>تصویر <strong style="color:#f8fafc;">رسید (فیش واریزی)</strong> خود را آپلود کنید و تراکنش را ثبت کنید.</li>
                        <li>پس از تأیید توسط مدیریت (حداکثر ۲۴ ساعت)، اشتراک شما فعال می‌شود.</li>
                    </ol>
                </div>
                <div style="background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.2); border-radius:10px; padding:1rem; display:flex; gap:0.6rem; align-items:flex-start;">
                    <span style="font-size:1.1rem; flex-shrink:0;">⚠️</span>
                    <p style="color:#fca5a5; font-size:0.85rem; margin:0;"><strong>هشدار:</strong> حتماً از <strong>شماره کارت</strong> نمایش داده‌شده در پنل استفاده کنید و نام پرداخت‌کننده را دقیقاً مطابق با نام حساب کاربری وارد کنید تا تأیید سریع‌تر انجام شود.</p>
                </div>
                <div style="background:rgba(245,158,11,0.08); border:1px solid rgba(245,158,11,0.2); border-radius:10px; padding:1rem; display:flex; gap:0.6rem; align-items:flex-start; margin-top:0.75rem;">
                    <span style="font-size:1.1rem; flex-shrink:0;">💡</span>
                    <p style="color:#fbbf24; font-size:0.85rem; margin:0;"><strong>نکته:</strong> وضعیت پرداخت‌های اخیر و تاریخ انقضای اشتراک در بالای داشبورد و در بخش سهمیه نمایش داده می‌شود. در صورت وجود <strong>کد تخفیف</strong>، می‌توانید هنگام ثبت تراکنش وارد کنید.</p>
                </div>
            </div>
        </section>

        <!-- ====== STEP 9: Referral & Wallet ====== -->
        <section id="step-9" style="background:#1e293b; border:1px solid #334155; border-radius:16px; padding:1.75rem; box-shadow:0 4px 24px rgba(0,0,0,0.15);">
            <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1.25rem;">
                <span style="display:flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg, #6366f1 0%, #c084fc 100%); font-size:1.3rem; flex-shrink:0;">🎯</span>
                <div>
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.15rem;">
                        <span style="font-size:0.7rem; color:#6366f1; background:rgba(99,102,241,0.12); padding:0.15rem 0.5rem; border-radius:6px; font-weight:700;">مرحله ۹</span>
                    </div>
                    <h2 style="font-size:1.15rem; font-weight:800; color:#f8fafc;">زیرمجموعه‌گیری و کیف پول</h2>
                </div>
            </div>
            <div style="color:#cbd5e1; font-size:0.9rem; line-height:2;">
                <p style="margin-bottom:1rem;">پُست‌یار دارای سیستم <strong style="color:#f8fafc;">زیرمجموعه‌گیری (Affiliate)</strong> و <strong style="color:#f8fafc;">کیف پول</strong> است. با معرفی پُست‌یار به دیگران، امتیاز کسب کنید و از آن‌ها برای تخفیف یا تمدید اشتراک استفاده نمایید.</p>
                <div style="background:rgba(99,102,241,0.06); border:1px solid rgba(99,102,241,0.15); border-radius:12px; padding:1.25rem; margin-bottom:1rem;">
                    <p style="font-weight:700; color:#c084fc; margin-bottom:0.5rem;">🔗 نحوه زیرمجموعه‌گیری:</p>
                    <ol style="padding-right:1.25rem; display:flex; flex-direction:column; gap:0.4rem;">
                        <li>به بخش <strong style="color:#f8fafc;">«زیرمجموعه‌گیری»</strong> در سایدبار بروید.</li>
                        <li><strong style="color:#f8fafc;">لینک دعوت</strong> اختصاصی خود را کپی کنید.</li>
                        <li>لینک را برای دوستان و آشنایان خود ارسال کنید.</li>
                        <li>هر شخصی که با لینک شما ثبت‌نام کند، به‌عنوان زیرمجموعه شما ثبت می‌شود.</li>
                        <li>با هر خرید اشتراک توسط زیرمجموعه‌هایتان، <strong style="color:#10b981;">امتیاز</strong> دریافت می‌کنید.</li>
                    </ol>
                </div>
                <div style="background:rgba(99,102,241,0.06); border:1px solid rgba(99,102,241,0.15); border-radius:12px; padding:1.25rem; margin-bottom:1rem;">
                    <p style="font-weight:700; color:#c084fc; margin-bottom:0.5rem;">💰 کیف پول:</p>
                    <p>امتیازات کسب‌شده در <strong style="color:#f8fafc;">کیف پول</strong> شما ذخیره می‌شوند. در بخش «کیف پول» می‌توانید:</p>
                    <ul style="padding-right:1.25rem; display:flex; flex-direction:column; gap:0.3rem; margin-top:0.5rem; list-style-type:disc;">
                        <li>موجودی امتیاز خود را مشاهده کنید</li>
                        <li>تاریخچه تراکنش‌ها (واریز و برداشت) را ببینید</li>
                        <li>امتیازات را به <strong style="color:#f8fafc;">تخفیف اشتراک</strong> یا <strong style="color:#f8fafc;">تمدید رایگان</strong> تبدیل کنید</li>
                    </ul>
                </div>
                <div style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.2); border-radius:10px; padding:1rem; display:flex; gap:0.6rem; align-items:flex-start;">
                    <span style="font-size:1.1rem; flex-shrink:0;">✅</span>
                    <p style="color:#6ee7b7; font-size:0.85rem; margin:0;"><strong>مزیت:</strong> زیرمجموعه‌گیری بدون محدودیت است! هر چقدر زیرمجموعه بیشتری دعوت کنید، امتیاز بیشتری کسب خواهید کرد. همچنین در صورت فعال‌بودن سیستم کیف پول توسط مدیر، امکان <strong>واریز مستقیم ریالی</strong> نیز وجود دارد.</p>
                </div>
            </div>
        </section>

        <!-- ====== STEP 10: Advanced Settings & AI ====== -->
        <section id="step-10" style="background:#1e293b; border:1px solid #334155; border-radius:16px; padding:1.75rem; box-shadow:0 4px 24px rgba(0,0,0,0.15);">
            <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1.25rem;">
                <span style="display:flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg, #10b981 0%, #2dd4bf 100%); font-size:1.3rem; flex-shrink:0;">⚙️</span>
                <div>
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.15rem;">
                        <span style="font-size:0.7rem; color:#10b981; background:rgba(16,185,129,0.12); padding:0.15rem 0.5rem; border-radius:6px; font-weight:700;">مرحله ۱۰</span>
                    </div>
                    <h2 style="font-size:1.15rem; font-weight:800; color:#f8fafc;">تنظیمات پیشرفته و هوش مصنوعی</h2>
                </div>
            </div>
            <div style="color:#cbd5e1; font-size:0.9rem; line-height:2;">
                <p style="margin-bottom:1rem;">بخش <strong style="color:#f8fafc;">«تنظیمات پیشرفته»</strong> به شما امکان می‌دهد تنظیمات AI و سایر قابلیت‌های پیشرفته را شخصی‌سازی کنید.</p>
                <div style="background:rgba(16,185,129,0.06); border:1px solid rgba(16,185,129,0.15); border-radius:12px; padding:1.25rem; margin-bottom:1rem;">
                    <p style="font-weight:700; color:#2dd4bf; margin-bottom:0.5rem;">🤖 تنظیم هوش مصنوعی (AI):</p>
                    <ol style="padding-right:1.25rem; display:flex; flex-direction:column; gap:0.4rem;">
                        <li>در تنظیمات پیشرفته، بخش <strong style="color:#f8fafc;">«تنظیمات هوش مصنوعی»</strong> را پیدا کنید.</li>
                        <li><strong style="color:#f8fafc;">سرویس‌دهنده AI</strong> را انتخاب کنید (مثلاً OpenAI، Google Gemini یا سایر ارائه‌دهندگان).</li>
                        <li><strong style="color:#f8fafc;">مدل</strong> مورد نظر را انتخاب کنید (مثلاً GPT-4o-mini، Gemini Pro و ...).</li>
                        <li><strong style="color:#f8fafc;">کلید API</strong> خود را وارد کنید. این کلید از پنل ارائه‌دهنده AI دریافت می‌شود.</li>
                        <li>تنظیمات را ذخیره کنید. اکنون می‌توانید از قابلیت تولید محتوا با AI در بخش ارسال پست استفاده کنید.</li>
                    </ol>
                </div>
                <div style="background:rgba(16,185,129,0.06); border:1px solid rgba(16,185,129,0.15); border-radius:12px; padding:1.25rem; margin-bottom:1rem;">
                    <p style="font-weight:700; color:#2dd4bf; margin-bottom:0.5rem;">🪙 ربات طلا و سکه:</p>
                    <p>اگر پلن شما شامل این ویژگی باشد، می‌توانید <strong style="color:#f8fafc;">ربات نرخ طلا و سکه</strong> را فعال کنید. این ربات به‌صورت خودکار و در بازه‌های زمانی مشخص، نرخ روزانه طلا و سکه را در کانال منتشر می‌کند.</p>
                </div>
                <div style="background:rgba(16,185,129,0.06); border:1px solid rgba(16,185,129,0.15); border-radius:12px; padding:1.25rem; margin-bottom:1rem;">
                    <p style="font-weight:700; color:#2dd4bf; margin-bottom:0.5rem;">🛒 اتصال به ووکامرس:</p>
                    <p>در صورت فعال‌بودن این ویژگی، می‌توانید فروشگاه اینترنتی ووکامرس خود را به پُست‌یار متصل کنید. محصولات جدید به‌صورت خودکار در کانال منتشر خواهند شد.</p>
                </div>
                <div style="background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.2); border-radius:10px; padding:1rem; display:flex; gap:0.6rem; align-items:flex-start;">
                    <span style="font-size:1.1rem; flex-shrink:0;">⚠️</span>
                    <p style="color:#fca5a5; font-size:0.85rem; margin:0;"><strong>هشدار امنیتی:</strong> کلید API هوش مصنوعی شما به‌صورت رمزنگاری‌شده ذخیره می‌شود. هرگز کلید API خود را با اشخاص ثالث به اشتراک نگذارید. در صورت نشت، فوراً آن را در پنل ارائه‌دهنده AI غیرفعال کنید.</p>
                </div>
            </div>
        </section>

        <!-- ====== FAQ Section ====== -->
        <section id="faq" style="background:#1e293b; border:1px solid #334155; border-radius:16px; padding:1.75rem; box-shadow:0 4px 24px rgba(0,0,0,0.15); margin-top:0.5rem;">
            <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1.5rem; padding-bottom:1rem; border-bottom:1px solid #334155;">
                <span style="display:flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg, #6366f1 0%, #818cf8 100%); font-size:1.3rem; flex-shrink:0;">❓</span>
                <h2 style="font-size:1.15rem; font-weight:800; color:#f8fafc;">سؤالات متداول (FAQ)</h2>
            </div>
            <div style="display:flex; flex-direction:column; gap:0.75rem;">

                <!-- FAQ Item 1 -->
                <details style="background:rgba(15,23,42,0.5); border:1px solid #334155; border-radius:12px; overflow:hidden;">
                    <summary style="padding:1rem 1.25rem; cursor:pointer; font-size:0.9rem; font-weight:700; color:#f8fafc; display:flex; align-items:center; gap:0.6rem; list-style:none; user-select:none; transition:background 0.2s;"
                             onmouseover="this.parentElement.style.borderColor='#6366f1';"
                             onmouseout="this.parentElement.style.borderColor='#334155';">
                        <span style="color:#6366f1; font-size:1rem;">▸</span>
                        آیا استفاده از پُست‌یار رایگان است؟
                    </summary>
                    <div style="padding:0 1.25rem 1rem 1.25rem; color:#94a3b8; font-size:0.88rem; line-height:1.9;">
                        پُست‌یار دارای پلن‌های مختلف با قیمت‌های متنوع است. شما می‌توانید پس از ثبت‌نام رایگان، پلن مناسب خود را انتخاب کنید. همچنین با سیستم زیرمجموعه‌گیری می‌توانید امتیاز کسب کرده و از تخفیف‌ها بهره‌مند شوید.
                    </div>
                </details>

                <!-- FAQ Item 2 -->
                <details style="background:rgba(15,23,42,0.5); border:1px solid #334155; border-radius:12px; overflow:hidden;">
                    <summary style="padding:1rem 1.25rem; cursor:pointer; font-size:0.9rem; font-weight:700; color:#f8fafc; display:flex; align-items:center; gap:0.6rem; list-style:none; user-select:none; transition:background 0.2s;"
                             onmouseover="this.parentElement.style.borderColor='#6366f1';"
                             onmouseout="this.parentElement.style.borderColor='#334155';">
                        <span style="color:#6366f1; font-size:1rem;">▸</span>
                        چند کانال می‌توانم اضافه کنم؟
                    </summary>
                    <div style="padding:0 1.25rem 1rem 1.25rem; color:#94a3b8; font-size:0.88rem; line-height:1.9;">
                        تعداد کانال‌های مجاز بسته به پلن اشتراک شما متفاوت است. در صفحه «خرید اشتراک» می‌توانید جزئیات هر پلن شامل حداکثر تعداد کانال را مشاهده کنید.
                    </div>
                </details>

                <!-- FAQ Item 3 -->
                <details style="background:rgba(15,23,42,0.5); border:1px solid #334155; border-radius:12px; overflow:hidden;">
                    <summary style="padding:1rem 1.25rem; cursor:pointer; font-size:0.9rem; font-weight:700; color:#f8fafc; display:flex; align-items:center; gap:0.6rem; list-style:none; user-select:none; transition:background 0.2s;"
                             onmouseover="this.parentElement.style.borderColor='#6366f1';"
                             onmouseout="this.parentElement.style.borderColor='#334155';">
                        <span style="color:#6366f1; font-size:1rem;">▸</span>
                        آیا وب‌هوک به‌صورت خودکار تنظیم می‌شود؟
                    </summary>
                    <div style="padding:0 1.25rem 1rem 1.25rem; color:#94a3b8; font-size:0.88rem; line-height:1.9;">
                        بله! هنگام افزودن کانال جدید، پُست‌یار به‌طور خودکار وب‌هوک را روی ربات تنظیم می‌کند. فقط کافیست توکن ربات را وارد کنید و مطمئن شوید ربات ادمین کانال است.
                    </div>
                </details>

                <!-- FAQ Item 4 -->
                <details style="background:rgba(15,23,42,0.5); border:1px solid #334155; border-radius:12px; overflow:hidden;">
                    <summary style="padding:1rem 1.25rem; cursor:pointer; font-size:0.9rem; font-weight:700; color:#f8fafc; display:flex; align-items:center; gap:0.6rem; list-style:none; user-select:none; transition:background 0.2s;"
                             onmouseover="this.parentElement.style.borderColor='#6366f1';"
                             onmouseout="this.parentElement.style.borderColor='#334155';">
                        <span style="color:#6366f1; font-size:1rem;">▸</span>
                        پست زمان‌بندی‌شده ارسال نشد، چه کنم؟
                    </summary>
                    <div style="padding:0 1.25rem 1rem 1.25rem; color:#94a3b8; font-size:0.88rem; line-height:1.9;">
                        ابتدا مطمئن شوید که ربات همچنان ادمین کانال است و دسترسی ارسال پیام دارد. همچنین وب‌هوک ممکن است به دلیل تغییرات قطع شده باشد؛ کانال را دوباره ویرایش و ذخیره کنید تا وب‌هوک مجدداً تنظیم شود. در صورت ادامه مشکل، تیکت پشتیبانی ثبت کنید.
                    </div>
                </details>

                <!-- FAQ Item 5 -->
                <details style="background:rgba(15,23,42,0.5); border:1px solid #334155; border-radius:12px; overflow:hidden;">
                    <summary style="padding:1rem 1.25rem; cursor:pointer; font-size:0.9rem; font-weight:700; color:#f8fafc; display:flex; align-items:center; gap:0.6rem; list-style:none; user-select:none; transition:background 0.2s;"
                             onmouseover="this.parentElement.style.borderColor='#6366f1';"
                             onmouseout="this.parentElement.style.borderColor='#334155';">
                        <span style="color:#6366f1; font-size:1rem;">▸</span>
                        آیا پُست‌یار از کانال‌های بله پشتیبانی می‌کند؟
                    </summary>
                    <div style="padding:0 1.25rem 1rem 1.25rem; color:#94a3b8; font-size:0.88rem; line-height:1.9;">
                        بله! پُست‌یار علاوه بر تلگرام، از پیام‌رسان بله نیز پشتیبانی کامل می‌کند. شما می‌توانید همزمان کانال‌های تلگرام و بله را مدیریت کنید.
                    </div>
                </details>

                <!-- FAQ Item 6 -->
                <details style="background:rgba(15,23,42,0.5); border:1px solid #334155; border-radius:12px; overflow:hidden;">
                    <summary style="padding:1rem 1.25rem; cursor:pointer; font-size:0.9rem; font-weight:700; color:#f8fafc; display:flex; align-items:center; gap:0.6rem; list-style:none; user-select:none; transition:background 0.2s;"
                             onmouseover="this.parentElement.style.borderColor='#6366f1';"
                             onmouseout="this.parentElement.style.borderColor='#334155';">
                        <span style="color:#6366f1; font-size:1rem;">▸</span>
                        چگونه رمز عبورم را تغییر دهم؟
                    </summary>
                    <div style="padding:0 1.25rem 1rem 1.25rem; color:#94a3b8; font-size:0.88rem; line-height:1.9;">
                        در بخش «تنظیمات حساب»، قسمت «تغییر رمز عبور» را پیدا کنید. رمز فعلی و رمز جدید را وارد کنید. حداقل طول رمز ۶ کاراکتر است.
                    </div>
                </details>

                <!-- FAQ Item 7 -->
                <details style="background:rgba(15,23,42,0.5); border:1px solid #334155; border-radius:12px; overflow:hidden;">
                    <summary style="padding:1rem 1.25rem; cursor:pointer; font-size:0.9rem; font-weight:700; color:#f8fafc; display:flex; align-items:center; gap:0.6rem; list-style:none; user-select:none; transition:background 0.2s;"
                             onmouseover="this.parentElement.style.borderColor='#6366f1';"
                             onmouseout="this.parentElement.style.borderColor='#334155';">
                        <span style="color:#6366f1; font-size:1rem;">▸</span>
                        امتیازات کیف پول چگونه محاسبه می‌شود؟
                    </summary>
                    <div style="padding:0 1.25rem 1rem 1.25rem; color:#94a3b8; font-size:0.88rem; line-height:1.9;">
                        هنگامی که شخصی که با لینک دعوت شما ثبت‌نام کرده، اشتراک خریداری می‌کند، درصدی از مبلغ خرید به‌صورت امتیاز به کیف پول شما واریز می‌شود. نرخ امتیازدهی در تنظیمات سیستم توسط مدیر تعیین شده است.
                    </div>
                </details>

                <!-- FAQ Item 8 -->
                <details style="background:rgba(15,23,42,0.5); border:1px solid #334155; border-radius:12px; overflow:hidden;">
                    <summary style="padding:1rem 1.25rem; cursor:pointer; font-size:0.9rem; font-weight:700; color:#f8fafc; display:flex; align-items:center; gap:0.6rem; list-style:none; user-select:none; transition:background 0.2s;"
                             onmouseover="this.parentElement.style.borderColor='#6366f1';"
                             onmouseout="this.parentElement.style.borderColor='#334155';">
                        <span style="color:#6366f1; font-size:1rem;">▸</span>
                        آیا امکان ارسال همزمان به چند کانال وجود دارد؟
                    </summary>
                    <div style="padding:0 1.25rem 1rem 1.25rem; color:#94a3b8; font-size:0.88rem; line-height:1.9;">
                        بله! هنگام ارسال پست جدید، می‌توانید چندین کانال را به‌صورت همزمان انتخاب کنید و پست در تمام آن‌ها منتشر خواهد شد.
                    </div>
                </details>

            </div>
        </section>

    </div>
</main>

<!-- Footer -->
<footer style="background:#1e293b; border-top:1px solid #334155; padding:1.5rem 0; text-align:center; margin-top:auto;">
    <div class="container">
        <p style="color:#64748b; font-size:0.82rem; margin-bottom:0.4rem;">سامانه هوشمند مدیریت و انتشار کانال‌ها</p>
        <p style="color:#475569; font-size:0.75rem;">© تمامی حقوق محفوظ است | پُست‌یار</p>
    </div>
</footer>

</body>
</html>