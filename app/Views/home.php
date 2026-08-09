<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'پُست‌یار | سامانه هوشمند مدیریت و انتشار کانال‌ها'); ?></title>
    <meta name="description" content="پُست‌یار - ابزار هوشمند مدیریت، زمان‌بندی شمسی، انتشار چندکاناله در تلگرام و بله، ربات خودکار نرخ طلا و سکه، پاسخگوی کلمات کلیدی و اتصال به ووکامرس.">

    <?php $baseUrl = rtrim(str_replace(['/assets', '/public/assets'], '', \WHCM\Core\Bootstrap::getAssetsUrl()), '/'); ?>

    <!-- PWA Meta Tags -->
    <link rel="manifest" href="<?php echo $baseUrl; ?>/manifest.json">
    <meta name="theme-color" content="#6366f1">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="پُست‌یار">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $baseUrl; ?>/assets/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $baseUrl; ?>/assets/icons/favicon-16x16.png">
    <!-- iOS PWA Support -->
    <link rel="apple-touch-icon" href="<?php echo $baseUrl; ?>/assets/icons/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo $baseUrl; ?>/assets/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="167x167" href="<?php echo $baseUrl; ?>/assets/icons/apple-touch-icon-167x167.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="پُست‌یار">
    <meta name="format-detection" content="telephone=no">
    <link rel="apple-touch-startup-image" href="<?php echo $baseUrl; ?>/assets/icons/icon-512x512.png">
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
    <script>window.tailwind || document.write('<script src="https://cdn.tailwindcss.com"><\/script>')</script>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo \WHCM\Core\Bootstrap::getAssetsUrl(); ?>/css/home.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        vazir: ['Vazirmatn', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-[#0a0a0a] text-neutral-100 antialiased selection:bg-indigo-500 selection:text-white">

    <!-- ===== NAVIGATION BAR ===== -->
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-500 py-4 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="glass-light rounded-2xl px-6 py-3.5 flex items-center justify-between">
                <!-- Logo & Brand -->
                <a href="#" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 via-purple-500 to-pink-500 p-0.5 shadow-lg shadow-indigo-500/20 group-hover:scale-105 transition-transform">
                        <div class="w-full h-full bg-[#0a0a0a] rounded-[10px] flex items-center justify-center overflow-hidden">
                            <?php if (class_exists('\WHCM\Core\Bootstrap')): ?>
                                <img src="<?php echo \WHCM\Core\Bootstrap::getAssetsUrl(); ?>/images/logo-white-bg.webp" alt="پُست‌یار" class="w-7 h-7 object-contain" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <?php endif; ?>
                            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="<?php echo class_exists('\WHCM\Core\Bootstrap') ? 'display:none;' : ''; ?>">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xl font-black bg-gradient-to-l from-white via-neutral-200 to-neutral-400 bg-clip-text text-transparent">پُست‌یار</span>
                        
                    </div>
                </a>

                <!-- Desktop Menu Links -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="#features" class="text-sm font-medium text-neutral-300 hover:text-white transition-colors">امکانات سیستم</a>
                    <a href="#how-it-works" class="text-sm font-medium text-neutral-300 hover:text-white transition-colors">نحوه کارکرد</a>
                    <a href="#pricing" class="text-sm font-medium text-neutral-300 hover:text-white transition-colors">تعرفه اشتراک</a>
                    <a href="#testimonials" class="text-sm font-medium text-neutral-300 hover:text-white transition-colors">نظرات مدیران</a>
                    <a href="#faq" class="text-sm font-medium text-neutral-300 hover:text-white transition-colors">سوالات متداول</a>
                </div>

                <!-- Action Buttons -->
                <div class="hidden sm:flex items-center gap-3">
                    <button onclick="openModal('login')" class="px-5 py-2.5 rounded-xl border border-neutral-700/80 text-sm font-semibold text-neutral-300 hover:text-white hover:border-neutral-500 hover:bg-white/5 transition-all">
                        ورود به پنل
                    </button>
                    <button onclick="openModal('register')" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-500 to-pink-500 text-sm font-bold text-white hover:from-indigo-600 hover:to-pink-600 shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 transition-all">
                        ثبت‌نام رایگان
                    </button>
                </div>

                <!-- Mobile Hamburger Toggle -->
                <button id="mobileToggle" class="md:hidden p-2 text-neutral-400 hover:text-white" aria-label="منوی موبایل">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <!-- ===== MOBILE MENU DRAWER ===== -->
    <div id="mobileMenu" class="fixed inset-0 z-50 bg-[#0a0a0a]/95 backdrop-blur-xl hidden flex-col justify-between p-6 md:hidden">
        <div>
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-2">
                    <span class="text-xl font-black text-white">پُست‌یار</span>
                </div>
                <button id="mobileClose" class="p-2 text-neutral-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="flex flex-col gap-6 text-lg font-bold">
                <a href="#features" class="text-neutral-300 hover:text-white" onclick="closeMobileMenu()">امکانات سیستم</a>
                <a href="#how-it-works" class="text-neutral-300 hover:text-white" onclick="closeMobileMenu()">نحوه کارکرد</a>
                <a href="#pricing" class="text-neutral-300 hover:text-white" onclick="closeMobileMenu()">تعرفه اشتراک</a>
                <a href="#testimonials" class="text-neutral-300 hover:text-white" onclick="closeMobileMenu()">نظرات مدیران</a>
                <a href="#faq" class="text-neutral-300 hover:text-white" onclick="closeMobileMenu()">سوالات متداول</a>
            </div>
        </div>
        <div class="flex flex-col gap-3 mt-8">
            <button onclick="closeMobileMenu(); openModal('login')" class="w-full py-3 rounded-xl border border-neutral-700 text-center font-bold text-neutral-200">
                ورود به پنل کاربری
            </button>
            <button onclick="closeMobileMenu(); openModal('register')" class="w-full py-3 rounded-xl bg-gradient-to-r from-indigo-500 to-pink-500 text-center font-bold text-white shadow-lg">
                ثبت‌نام رایگان و شروع تست
            </button>
        </div>
    </div>

    <!-- ===== 1. HERO SECTION ===== -->
    <section class="relative min-h-screen flex items-center pt-32 pb-20 mesh-bg noise overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                <div class="lg:col-span-7 text-center lg:text-right">
                    <?php if (!empty($message)): ?>
                        <div class="reveal mb-6 px-5 py-3 rounded-2xl bg-indigo-500/20 border border-indigo-500/40 text-indigo-200 text-sm font-bold">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- لوگوی کامل پُست‌یار در هیرو -->
                    <div class="reveal mb-6">
                        <img src="<?php echo \WHCM\Core\Bootstrap::getAssetsUrl(); ?>/images/logo-full.webp" alt="پُست‌یار" class="h-14 sm:h-16 md:h-20 lg:h-24 w-auto object-contain mx-auto lg:mx-0" style="filter: brightness(1.05);">
                    </div>

                    <div class="reveal inline-flex items-center gap-2 px-4 py-2 rounded-full glass-light mb-8">
                        <span class="w-2.5 h-2.5 rounded-full bg-green-400 animate-pulse"></span>
                        <span class="text-xs sm:text-sm font-semibold text-neutral-200">نسخه ۲.۰ منتشر شد — مجهز به ربات نرخ لحظه‌ای طلا و سکه 🚀</span>
                    </div>

                    <h1 class="reveal reveal-delay-1 text-3xl sm:text-4xl md:text-5xl lg:text-[3.25rem] font-black !leading-[1.65] tracking-tight mb-8" style="line-height: 1.65 !important;">
                        <span class="text-white block mb-2">مدیریت هوشمند و انتشار خودکار</span>
                        <span class="bg-gradient-to-l from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent gradient-animate block">در تلگرام و بله</span>
                    </h1>

                    <p class="reveal reveal-delay-2 text-base sm:text-lg md:text-xl text-neutral-400 leading-relaxed mb-10 max-w-2xl mx-auto lg:mx-0">
                        یک بار منتشر کن، همه جا دیده شو! پیام‌های متنی، ویدیویی و تصویری خود را به کانال‌های تلگرام و بله ارسال و زمان‌بندی کنید. به همراه ربات خودکار نرخ طلا و سکه، پاسخگوی هوشمند کلمات کلیدی و اتصال مستقیم به ووکامرس.
                    </p>

                    <div class="reveal reveal-delay-3 flex flex-col sm:flex-row items-center gap-4 justify-center lg:justify-start mb-12">
                        <button onclick="openModal('register')" class="w-full sm:w-auto group relative inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl bg-gradient-to-r from-indigo-500 to-pink-500 text-white font-bold text-base hover:from-indigo-600 hover:to-pink-600 transition-all duration-300 hover:shadow-2xl hover:shadow-indigo-500/25 pulse-glow">
                            <span>ثبت‌نام و شروع تست رایگان 🚀</span>
                        </button>
                        <a href="#features" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl glass text-neutral-300 font-medium text-base hover:text-white hover:bg-white/10 transition-all duration-300">
                            <span>مشاهده امکانات سیستم</span>
                        </a>
                    </div>
                </div>

                <!-- Hero Visual Showcase with Floating Badges -->
                <div class="lg:col-span-5 relative reveal reveal-delay-2">
                    <div class="relative">
                        <div class="glass-light rounded-3xl p-5 sm:p-6 float-animation shadow-2xl">
                            <div class="bg-[#111116] rounded-2xl p-5 space-y-4 border border-neutral-800/80">
                                <div class="flex items-center justify-between pb-3 border-b border-neutral-800">
                                    <span class="text-xs text-neutral-400 font-mono">dashboard.postyar.app</span>
                                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 font-bold">متصل به تلگرام و بله</span>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex items-center gap-3 p-3 rounded-xl bg-white/[0.04]">
                                        <span class="text-lg">✈</span>
                                        <div class="flex-1">
                                            <div class="text-sm font-bold text-white">پست کانال تلگرام</div>
                                            <div class="text-xs text-neutral-400">پابلیش شده • ۲ دقیقه پیش</div>
                                        </div>
                                        <div class="text-xs text-green-400 font-bold bg-green-500/10 px-2.5 py-1 rounded-lg">+۲۴۳ لایک</div>
                                    </div>
                                    <div class="flex items-center gap-3 p-3 rounded-xl bg-white/[0.04]">
                                        <span class="text-lg">🪙</span>
                                        <div class="flex-1">
                                            <div class="text-sm font-bold text-white">ربات نرخ طلا ۱۸ عیار و سکه</div>
                                            <div class="text-xs text-neutral-400">بروزرسانی زنده از API</div>
                                        </div>
                                        <div class="text-xs text-amber-400 font-bold bg-amber-500/10 px-2.5 py-1 rounded-lg">شلیک خودکار</div>
                                    </div>
                                    <div class="flex items-center gap-3 p-3 rounded-xl bg-white/[0.04]">
                                        <span class="text-lg">💬</span>
                                        <div class="flex-1">
                                            <div class="text-sm font-bold text-white">پست ویدیویی کانال بله</div>
                                            <div class="text-xs text-neutral-400">زمان‌بندی • فردا ساعت ۰۹:۰۰</div>
                                        </div>
                                        <div class="text-xs text-indigo-400 font-bold bg-indigo-500/10 px-2.5 py-1 rounded-lg">در صف انتشار</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Top-Left Floating Badge -->
                        <div class="absolute -top-6 -left-6 glass-light rounded-2xl p-3 pr-4 float-animation-delay flex items-center gap-3 shadow-xl border border-white/20">
                            <div class="w-10 h-10 rounded-xl bg-green-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-white">ارسال موفق چندکاناله!</div>
                                <div class="text-[11px] text-neutral-400">تلگرام و بله • همین الان</div>
                            </div>
                        </div>

                        <!-- Bottom-Right Floating Badge -->
                        <div class="absolute -bottom-5 -right-5 glass-light rounded-2xl p-4 float-animation-delay2 shadow-xl border border-white/20">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-pink-500/20 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                </div>
                                <div>
                                    <div class="text-lg font-black text-white">+۳۸%</div>
                                    <div class="text-[11px] text-neutral-400">رشد تعامل اعضا</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== 2. TRUSTED BY MARQUEE ===== -->
    <section class="relative py-14 border-y border-neutral-800/60 overflow-hidden bg-[#0c0c10]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
            <p class="text-center text-sm text-neutral-400 font-medium">مورد اعتماد گالری‌های طلا، فروشگاه‌های آنلاین و کانال‌های پرمخاطب ایرانی</p>
        </div>
        <div class="flex overflow-hidden">
            <div class="marquee-track flex items-center gap-16 whitespace-nowrap">
                <span class="text-xl font-bold text-neutral-500 hover:text-neutral-300 transition-colors">گالری طلا آسوین</span>
                <span class="text-xl font-bold text-neutral-500 hover:text-neutral-300 transition-colors">فروشگاه دیجیتال‌شاپ</span>
                <span class="text-xl font-bold text-neutral-500 hover:text-neutral-300 transition-colors">کانال بورس‌تایمز</span>
                <span class="text-xl font-bold text-neutral-500 hover:text-neutral-300 transition-colors">آکادمی تجارت نوین</span>
                <span class="text-xl font-bold text-neutral-500 hover:text-neutral-300 transition-colors">رصدخانه نرخ طلا</span>
                <span class="text-xl font-bold text-neutral-500 hover:text-neutral-300 transition-colors">کافه مارکتینگ</span>
                <span class="text-xl font-bold text-neutral-500 hover:text-neutral-300 transition-colors">فروشگاه استایل‌نو</span>
                <span class="text-xl font-bold text-neutral-500 hover:text-neutral-300 transition-colors">رسانه فناوری تک‌مگ</span>
                <span class="text-xl font-bold text-neutral-500 hover:text-neutral-300 transition-colors">گالری طلا آسوین</span>
                <span class="text-xl font-bold text-neutral-500 hover:text-neutral-300 transition-colors">فروشگاه دیجیتال‌شاپ</span>
                <span class="text-xl font-bold text-neutral-500 hover:text-neutral-300 transition-colors">کانال بورس‌تایمز</span>
                <span class="text-xl font-bold text-neutral-500 hover:text-neutral-300 transition-colors">آکادمی تجارت نوین</span>
                <span class="text-xl font-bold text-neutral-500 hover:text-neutral-300 transition-colors">رصدخانه نرخ طلا</span>
                <span class="text-xl font-bold text-neutral-500 hover:text-neutral-300 transition-colors">کافه مارکتینگ</span>
                <span class="text-xl font-bold text-neutral-500 hover:text-neutral-300 transition-colors">فروشگاه استایل‌نو</span>
                <span class="text-xl font-bold text-neutral-500 hover:text-neutral-300 transition-colors">رسانه فناوری تک‌مگ</span>
            </div>
        </div>
    </section>

    <!-- ===== 3. FEATURES SECTION ===== -->
    <section id="features" class="relative py-24 md:py-32 mesh-bg noise">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16 md:mb-20">
                <h2 class="reveal text-3xl sm:text-4xl md:text-5xl font-black tracking-tight mb-6 text-white">
                    امکانات فوق‌حرفه‌ای پُست‌یار
                </h2>
                <p class="reveal text-base sm:text-lg text-neutral-400 leading-relaxed">
                    هر آنچه برای اتوماسیون، زمان‌بندی شمسی و مدیریت کانال‌های تلگرام و بله نیاز دارید
                </p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                <div class="reveal card-glow glass-light rounded-3xl p-7 md:p-8 border border-white/[0.08]">
                    <span class="text-2xl block mb-4">✈</span>
                    <h3 class="text-xl font-bold mb-3 text-white">سیستم هوشمند انتشار و زمان‌بندی</h3>
                    <p class="text-neutral-400 text-sm leading-relaxed">ارسال همگام و زمان‌بندی دقیق پست‌های ویدیویی، تصویری و متنی بر اساس تقویم شمسی به کانال‌های تلگرام و بله.</p>
                </div>
                <div class="reveal card-glow glass-light rounded-3xl p-7 md:p-8 border border-white/[0.08]">
                    <span class="text-2xl block mb-4">🪙</span>
                    <h3 class="text-xl font-bold mb-3 text-white">ربات خودکار نرخ لحظه‌ای طلا و سکه</h3>
                    <p class="text-neutral-400 text-sm leading-relaxed">دریافت زنده قیمت انس، طلا ۱۸ عیار و انواع مسکوکات از API و شلیک خودکار به کانال‌ها به تومان یا ریال.</p>
                </div>
                <div class="reveal card-glow glass-light rounded-3xl p-7 md:p-8 border border-white/[0.08]">
                    <span class="text-2xl block mb-4">🤖</span>
                    <h3 class="text-xl font-bold mb-3 text-white">پاسخگوی کلمات کلیدی و صندوق پیام</h3>
                    <p class="text-neutral-400 text-sm leading-relaxed">سیستم پاسخگویی هوشمند به کلمات کلیدی مانند «قیمت» به همراه مدیریت متمرکز تیکت‌های پشتیبانی.</p>
                </div>
                <div class="reveal card-glow glass-light rounded-3xl p-7 md:p-8 border border-white/[0.08]">
                    <span class="text-2xl block mb-4">🛍</span>
                    <h3 class="text-xl font-bold mb-3 text-white">اتصال مستقیم به ووکامرس</h3>
                    <p class="text-neutral-400 text-sm leading-relaxed">همگام‌سازی فروشگاه وردپرس برای انتشار خودکار محصولات جدید، تغییرات قیمت و تخفیف‌ها در کانال‌ها.</p>
                </div>
                <div class="reveal card-glow glass-light rounded-3xl p-7 md:p-8 border border-white/[0.08]">
                    <span class="text-2xl block mb-4">🧠</span>
                    <h3 class="text-xl font-bold mb-3 text-white">کپشن‌ساز هوش مصنوعی</h3>
                    <p class="text-neutral-400 text-sm leading-relaxed">تولید متن جذاب، هشتگ‌گذاری حرفه‌ای و نگارش کپشن‌های تبلیغاتی توسط هوش مصنوعی مولد.</p>
                </div>
                <div class="reveal card-glow glass-light rounded-3xl p-7 md:p-8 border border-white/[0.08]">
                    <span class="text-2xl block mb-4">📈</span>
                    <h3 class="text-xl font-bold mb-3 text-white">تحلیل آمار تفکیکی کانال‌ها</h3>
                    <p class="text-neutral-400 text-sm leading-relaxed">آنالیز دقیق بازدیدها، تعامل کاربران و رشد اعضای کانال‌های تلگرام و بله در داشبورد اختصاصی.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== 4. STATS BANNER ===== -->
    <section class="relative py-20 border-y border-neutral-800/60 bg-[#0c0c10]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="reveal text-center">
                    <div class="text-4xl sm:text-5xl font-black bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent mb-2">۵,۰۰۰+</div>
                    <div class="text-sm text-neutral-400 font-medium">کانال و کسب‌وکار فعال</div>
                </div>
                <div class="reveal reveal-delay-1 text-center">
                    <div class="text-4xl sm:text-5xl font-black bg-gradient-to-r from-pink-400 to-amber-400 bg-clip-text text-transparent mb-2">۲M+</div>
                    <div class="text-sm text-neutral-400 font-medium">پست منتشر شده در تلگرام و بله</div>
                </div>
                <div class="reveal reveal-delay-2 text-center">
                    <div class="text-4xl sm:text-5xl font-black bg-gradient-to-r from-emerald-400 to-teal-400 bg-clip-text text-transparent mb-2">۹۹.۹%</div>
                    <div class="text-sm text-neutral-400 font-medium">آپتایم سرورها و ربات‌ها</div>
                </div>
                <div class="reveal reveal-delay-3 text-center">
                    <div class="text-4xl sm:text-5xl font-black bg-gradient-to-r from-amber-400 to-orange-400 bg-clip-text text-transparent mb-2">۴.۹ / ۵</div>
                    <div class="text-sm text-neutral-400 font-medium">رضایت کاربران از اتوماسیون</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== 5. HOW IT WORKS SECTION ===== -->
    <section id="how-it-works" class="relative py-24 md:py-32 mesh-bg noise">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <div class="reveal inline-flex items-center gap-2 px-4 py-2 rounded-full glass-light mb-6">
                    <span class="text-xs font-semibold text-pink-400">ساده و سریع</span>
                </div>
                <h2 class="reveal reveal-delay-1 text-3xl sm:text-4xl md:text-5xl font-black tracking-tight mb-6 text-white">
                    شروع اتوماسیون در
                    <span class="bg-gradient-to-l from-pink-400 to-amber-400 bg-clip-text text-transparent">۳ مرحله ساده</span>
                </h2>
                <p class="reveal reveal-delay-2 text-base sm:text-lg text-neutral-400 leading-relaxed">
                    بدون نیاز به دانش فنی یا نصب نرم‌افزار پیچیده، تنها با چند کلیک کانال‌های خود را به پستیار متصل کنید.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 lg:gap-12 relative">
                <div class="reveal text-center group relative">
                    <div class="relative inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-gradient-to-br from-indigo-500/20 to-indigo-500/5 border border-indigo-500/20 mb-8 group-hover:scale-110 transition-transform duration-500">
                        <span class="text-3xl">🔗</span>
                        <span class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-indigo-500 text-white text-xs font-bold flex items-center justify-center shadow-lg">۱</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">کانال‌های تلگرام و بله را متصل کنید</h3>
                    <p class="text-neutral-400 leading-relaxed text-sm">
                        با چند کلیک ساده ربات پستیار را به کانال‌های تلگرام و بله خود متصل کرده و دسترسی انتشار را تایید کنید. اتصال کاملاً امن و مستقل.
                    </p>
                </div>

                <div class="reveal reveal-delay-1 text-center group relative">
                    <div class="relative inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-gradient-to-br from-pink-500/20 to-pink-500/5 border border-pink-500/20 mb-8 group-hover:scale-110 transition-transform duration-500">
                        <span class="text-3xl">⚙️</span>
                        <span class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-pink-500 text-white text-xs font-bold flex items-center justify-center shadow-lg">۲</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">محتوا، ربات طلا یا ووکامرس را تنظیم کنید</h3>
                    <p class="text-neutral-400 leading-relaxed text-sm">
                        پست‌های خود را زمان‌بندی کنید، الگوهای ربات نرخ طلا و سکه را سفارشی‌سازی کنید یا فروشگاه ووکامرس خود را برای انتشار خودکار متصل سازید.
                    </p>
                </div>

                <div class="reveal reveal-delay-2 text-center group relative">
                    <div class="relative inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-gradient-to-br from-emerald-500/20 to-emerald-500/5 border border-emerald-500/20 mb-8 group-hover:scale-110 transition-transform duration-500">
                        <span class="text-3xl">🚀</span>
                        <span class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-emerald-500 text-white text-xs font-bold flex items-center justify-center shadow-lg">۳</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">انتشار خودکار و رشد مخاطبان</h3>
                    <p class="text-neutral-400 leading-relaxed text-sm">
                        پست‌ها در زمان دقیق شمسی منتشر می‌شوند، ربات‌ها به کلمات کلیدی پاسخ می‌دهند و شما رشد تعامل را در داشبورد مشاهده می‌کنید.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== 6. PRICING SECTION (Dynamically Rendered from $plans) ===== -->
    <section id="pricing" class="relative py-24 md:py-32 mesh-bg noise">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16 md:mb-20">
                <h2 class="reveal text-3xl sm:text-4xl md:text-5xl font-black tracking-tight mb-6 text-white">
                    تعرفه اشتراک‌های پُست‌یار
                </h2>
                <p class="reveal text-base sm:text-lg text-neutral-400 leading-relaxed">
                    انتخاب بهترین پلن متناسب با نیاز کانال‌ها و کسب‌وکار شما با امکانات نئونی و فشرده
                </p>
            </div>

            <?php
                $occasion_discount_text = 'تخفیف مناسبتی';
                if (class_exists('\WHCM\Core\Bootstrap')) {
                    try {
                        $stmt_occ = \WHCM\Core\Bootstrap::getDB()->prepare("SELECT key_value FROM settings WHERE tenant_id = 0 AND key_name = 'occasion_discount_text'");
                        $stmt_occ->execute();
                        $occ_row = $stmt_occ->fetch();
                        if (!empty($occ_row['key_value'])) {
                            $occasion_discount_text = $occ_row['key_value'];
                        }
                    } catch (\Exception $e) {}
                }
            ?>
            <div class="plans-container">
                <?php if (!empty($plans) && is_array($plans)): ?>
                    <?php foreach ($plans as $p): ?>
                        <?php 
                            $is_featured = !empty($p['is_featured']);
                            $gen_discount = (int)($p['general_discount'] ?? 0);
                            $final_price = $p['price'];
                            if ($gen_discount > 0) {
                                $final_price = $p['price'] * (1 - ($gen_discount / 100));
                            }
                            $feats = json_decode($p['features'] ?? '{}', true);
                        ?>
                        <div class="plan-card <?php echo $is_featured ? 'featured-plan' : ($p['price'] > 500000 ? 'recommended' : ''); ?>">
                            <div>
                                <?php if (!empty($p['image_url']) && $p['image_url'] !== 'null'): ?>
                                    <div class="plan-card-img-wrapper" style="background: linear-gradient(135deg, rgba(99,102,241,0.2) 0%, rgba(15,23,42,0.95) 100%); min-height: 160px; display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: 16px; margin-bottom: 1.25rem; position: relative; border: 1px solid rgba(99,102,241,0.3);">
                                        <img src="<?php echo \WHCM\Core\Bootstrap::getPlanImageUrl($p['image_url']); ?>" alt="<?php echo htmlspecialchars($p['title']); ?>" class="plan-card-img" style="max-height: 160px; width: auto; object-fit: contain;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="fallback-plan-banner" style="display:none; width:100%; height:160px; background: linear-gradient(135deg, #1e1b4b 0%, #311042 50%, #0f172a 100%); align-items:center; justify-content:center; flex-direction:column; gap:0.5rem; border-radius:16px;">
                                            <span style="font-size:3rem; filter: drop-shadow(0 0 15px rgba(168,85,247,0.8));">💎</span>
                                            <span style="color:#e9d5ff; font-weight:900; font-size:1.05rem; letter-spacing:1px;"><?php echo htmlspecialchars($p['title']); ?></span>
                                        </div>
                                        <div class="neon-badges-overlay">
                                            <?php if (!empty($p['discount_badge_text'])): ?>
                                                <div class="neon-glow-badge neon-badge-pink">
                                                    ✨ <?php echo htmlspecialchars($p['discount_badge_text']); ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($gen_discount > 0): ?>
                                                <div class="neon-glow-badge neon-badge-emerald">
                                                    🔥 %<?php echo \WHCM\Domain\TextFormat::fa_digits($gen_discount); ?> <?php echo !empty($p['discount_badge_text']) ? htmlspecialchars($p['discount_badge_text']) : 'تخفیف ویژه'; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="plan-card-img-wrapper" style="background: linear-gradient(135deg, #1e1b4b 0%, #311042 50%, #0f172a 100%); min-height: 160px; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 0.5rem; overflow: hidden; border-radius: 16px; margin-bottom: 1.25rem; position: relative; border: 1px solid rgba(168,85,247,0.4); box-shadow: inset 0 0 25px rgba(168,85,247,0.2);">
                                        <span style="font-size:3rem; filter: drop-shadow(0 0 15px rgba(168,85,247,0.8));">💎</span>
                                        <span style="color:#e9d5ff; font-weight:900; font-size:1.05rem; letter-spacing:1px;"><?php echo htmlspecialchars($p['title']); ?></span>
                                        <div class="neon-badges-overlay">
                                            <?php if (!empty($p['discount_badge_text'])): ?>
                                                <div class="neon-glow-badge neon-badge-pink">
                                                    ✨ <?php echo htmlspecialchars($p['discount_badge_text']); ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($gen_discount > 0): ?>
                                                <div class="neon-glow-badge neon-badge-emerald">
                                                    🔥 %<?php echo \WHCM\Domain\TextFormat::fa_digits($gen_discount); ?> <?php echo !empty($p['discount_badge_text']) ? htmlspecialchars($p['discount_badge_text']) : 'تخفیف ویژه'; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <h3><?php echo htmlspecialchars($p['title']); ?></h3>
                                <div style="text-align: center; margin-bottom: 0.9rem;">
                                    <?php if ($gen_discount > 0): ?>
                                        <span style="text-decoration: line-through; color: #64748b; font-size: 0.88rem; margin-left: 0.35rem;"><?php echo \WHCM\Domain\TextFormat::fa_num($p['price']); ?></span>
                                        <span style="color: #10b981; font-size: 1.25rem; font-weight: 900;"><?php echo \WHCM\Domain\TextFormat::fa_num($final_price); ?> <span style="font-size: 0.78rem; font-weight: normal; color: #94a3b8;">تومان</span></span>
                                    <?php else: ?>
                                        <span style="font-size: 1.25rem; font-weight: 900; color: #ffffff;"><?php echo \WHCM\Domain\TextFormat::fa_num($p['price']); ?> <span style="font-size: 0.78rem; font-weight: normal; color: #94a3b8;">تومان</span></span>
                                    <?php endif; ?>
                                </div>

                                <ul>
                                    <li>⌛ مدت زمان: <strong><?php echo \WHCM\Domain\TextFormat::fa_digits($p['duration_days']); ?> روز</strong></li>
                                    <li>📻 سهمیه کانال: <strong><?php echo \WHCM\Domain\TextFormat::fa_digits($p['max_channels']); ?> کانال</strong></li>
                                    <li>📝 سهمیه پست: <strong><?php echo $p['max_posts'] === 0 ? 'نامحدود' : \WHCM\Domain\TextFormat::fa_digits($p['max_posts']) . ' پست'; ?></strong></li>
                                    <li>📈 تحلیل آمار تفکیکی: <?php echo !empty($feats['stats']) ? '✅' : '❌'; ?></li>
                                    <li>🪙 ربات خودکار نرخ طلا: <?php echo !empty($feats['gold_ticker']) ? '✅' : '❌'; ?></li>
                                    <li>🤖 پاسخگوی کلمات کلیدی: <?php echo !empty($feats['auto_responder']) ? '✅' : '❌'; ?></li>
                                    <li>🛍 قابلیت اتصال به ووکامرس: <?php echo !empty($feats['woocommerce']) ? '✅' : '❌'; ?></li>
                                </ul>
                            </div>

                            <button onclick="openModal('register')" class="w-full py-3 rounded-xl <?php echo $is_featured ? 'bg-gradient-to-r from-indigo-500 to-pink-500 text-white shadow-lg' : 'border border-neutral-700 text-neutral-300 hover:text-white'; ?> font-bold text-sm">
                                ثبت‌نام و خرید اشتراک
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ===== 7. TESTIMONIALS SECTION ===== -->
    <section id="testimonials" class="relative py-24 md:py-32 border-y border-neutral-800/60 bg-[#0c0c10]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 md:mb-20">
                <div class="reveal inline-flex items-center gap-2 px-4 py-2 rounded-full glass-light mb-6">
                    <span class="text-xs font-semibold text-indigo-400">نظرات مدیران کانال‌ها</span>
                </div>
                <h2 class="reveal reveal-delay-1 text-3xl sm:text-4xl md:text-5xl font-black tracking-tight mb-6 text-white">
                    تجربه واقعی
                    <span class="bg-gradient-to-l from-indigo-400 to-pink-400 bg-clip-text text-transparent">کاربران پُست‌یار</span>
                </h2>
                <p class="reveal reveal-delay-2 text-base sm:text-lg text-neutral-400 max-w-2xl mx-auto">
                    ببینید چگونه مدیران گالری‌های طلا، فروشگاه‌های آنلاین و کانال‌های خبری در زمان و هزینه خود صرفه‌جویی کرده‌اند.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="reveal card-glow glass-light rounded-3xl p-8 flex flex-col justify-between border border-white/[0.08]">
                    <div>
                        <div class="flex items-center gap-1 text-amber-400 mb-6 text-sm">★★★★★</div>
                        <p class="text-neutral-300 leading-relaxed mb-8 text-sm sm:text-base">
                            «از وقتی ربات نرخ لحظه‌ای طلا و سکه پستیار رو روی کانال گالریمون فعال کردیم، دیگه نیازی نیست دستی قیمت‌ها رو آپدیت کنیم! همه تغییرات انس و عیار خودکار با تومان ارسال میشه.»
                        </p>
                    </div>
                    <div class="flex items-center gap-4 pt-4 border-t border-neutral-800/80">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center font-bold text-white text-sm">HN</div>
                        <div>
                            <div class="font-bold text-white text-sm">هومن نقشی</div>
                            <div class="text-xs text-neutral-400">مدیر گالری طلا و جواهر</div>
                        </div>
                    </div>
                </div>

                <div class="reveal reveal-delay-1 card-glow glass-light rounded-3xl p-8 flex flex-col justify-between border border-white/[0.08]">
                    <div>
                        <div class="flex items-center gap-1 text-amber-400 mb-6 text-sm">★★★★★</div>
                        <p class="text-neutral-300 leading-relaxed mb-8 text-sm sm:text-base">
                            «ارسال همزمان پست‌ها به تلگرام و بله با قابلیت زمان‌بندی شمسی فوق‌العاده‌ست. سیستم پاسخگوی خودکار کلمه «قیمت» کار پشتیبانی ما رو ۱۰ برابر سریع‌تر و دقیق‌تر کرده.»
                        </p>
                    </div>
                    <div class="flex items-center gap-4 pt-4 border-t border-neutral-800/80">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-pink-500 to-red-500 flex items-center justify-center font-bold text-white text-sm">SR</div>
                        <div>
                            <div class="font-bold text-white text-sm">سارا رضایی</div>
                            <div class="text-xs text-neutral-400">مدیر کانال فروشگاهی پوشاک</div>
                        </div>
                    </div>
                </div>

                <div class="reveal reveal-delay-2 card-glow glass-light rounded-3xl p-8 flex flex-col justify-between border border-white/[0.08]">
                    <div>
                        <div class="flex items-center gap-1 text-amber-400 mb-6 text-sm">★★★★★</div>
                        <p class="text-neutral-300 leading-relaxed mb-8 text-sm sm:text-base">
                            «اتصال ووکامرس به کانال تلگراممون باعث شد هر محصولی اضافه می‌کنیم یا قیمتش تغییر می‌کنه، در لحظه و با قالب شیک برای اعضای کانال ارسال بشه. پستیار یک دستیار واقعیه.»
                        </p>
                    </div>
                    <div class="flex items-center gap-4 pt-4 border-t border-neutral-800/80">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center font-bold text-white text-sm">AK</div>
                        <div>
                            <div class="font-bold text-white text-sm">علیرضا کاظمی</div>
                            <div class="text-xs text-neutral-400">بنیان‌گذار فروشگاه آنلاین دیجی‌کالا استایل</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== 8. FAQ SECTION ===== -->
    <section id="faq" class="relative py-24 md:py-32 mesh-bg noise">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight mb-6 text-white">
                    پاسخ به پرسش‌های شما
                </h2>
            </div>
            <div class="space-y-4">
                <div class="faq-item glass-light rounded-2xl border border-white/[0.08] overflow-hidden">
                    <button class="faq-toggle w-full px-6 py-5 text-right flex items-center justify-between gap-4">
                        <span class="font-bold text-base text-white">آیا برای استفاده از پستیار نیاز به دانش فنی داریم؟</span>
                        <span class="faq-icon text-neutral-400 transition-transform duration-300 text-xl">▼</span>
                    </button>
                    <div class="faq-answer px-6 pb-5 text-neutral-300 text-sm leading-relaxed">
                        خیر! پستیار به گونه‌ای طراحی شده است که هر مدیری با هر سطح از دانش فنی بتواند تنها با چند کلیک، کانال‌های تلگرام و بله خود را متصل کرده و از امکانات زمان‌بندی، ربات نرخ طلا و اتوماسیون استفاده کند.
                    </div>
                </div>
                <div class="faq-item glass-light rounded-2xl border border-white/[0.08] overflow-hidden">
                    <button class="faq-toggle w-full px-6 py-5 text-right flex items-center justify-between gap-4">
                        <span class="font-bold text-base text-white">پستیار از کدام پیام‌رسان‌ها پشتیبانی می‌کند؟</span>
                        <span class="faq-icon text-neutral-400 transition-transform duration-300 text-xl">▼</span>
                    </button>
                    <div class="faq-answer px-6 pb-5 text-neutral-300 text-sm leading-relaxed">
                        در حال حاضر پستیار به صورت تخصصی از کانال‌های تلگرام و بله پشتیبانی می‌کند. شما می‌توانید یک پست را همزمان و هماهنگ در هر دو پلتفرم منتشر یا زمان‌بندی کنید.
                    </div>
                </div>
                <div class="faq-item glass-light rounded-2xl border border-white/[0.08] overflow-hidden">
                    <button class="faq-toggle w-full px-6 py-5 text-right flex items-center justify-between gap-4">
                        <span class="font-bold text-base text-white">ربات نرخ لحظه‌ای طلا و سکه چگونه کار می‌کند؟</span>
                        <span class="faq-icon text-neutral-400 transition-transform duration-300 text-xl">▼</span>
                    </button>
                    <div class="faq-answer px-6 pb-5 text-neutral-300 text-sm leading-relaxed">
                        ربات پستیار به صورت مداوم نرخ زنده انس جهانی، طلا ۱۸ عیار و انواع مسکوکات را از API معتبر دریافت می‌کند. بر اساس الگوی سفارشی و فرمت متنی دلخواه شما، هر زمان که نرخ تغییر کند، قیمت جدید به صورت خودکار در کانال شما ارسال می‌شود.
                    </div>
                </div>
                <div class="faq-item glass-light rounded-2xl border border-white/[0.08] overflow-hidden">
                    <button class="faq-toggle w-full px-6 py-5 text-right flex items-center justify-between gap-4">
                        <span class="font-bold text-base text-white">آیا امکان اتصال فروشگاه ووکامرس به کانال وجود دارد؟</span>
                        <span class="faq-icon text-neutral-400 transition-transform duration-300 text-xl">▼</span>
                    </button>
                    <div class="faq-answer px-6 pb-5 text-neutral-300 text-sm leading-relaxed">
                        بله! با استفاده از قابلیت اتصال به ووکامرس، به محض افزودن محصول جدید، اعمال تخفیف یا تغییر قیمت در فروشگاه وردپرسی شما، اطلاعات محصول به همراه تصویر در کانال‌های تلگرام و بله منتشر می‌شود.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== 9. FINAL CTA SECTION ===== -->
    <section class="relative py-24 md:py-32 overflow-hidden bg-[#0a0a0a]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="reveal relative card-glow glass-light rounded-3xl p-10 md:p-16 text-center border-gradient overflow-hidden">
                <div class="relative z-10 max-w-2xl mx-auto">
                    <!-- لوگوی کامل پُست‌یار در CTA -->
                    <img src="<?php echo \WHCM\Core\Bootstrap::getAssetsUrl(); ?>/images/logo-full.webp" alt="پُست‌یار" class="h-12 sm:h-14 md:h-16 w-auto object-contain mx-auto mb-8" style="filter: brightness(1.05);">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl font-black !leading-[1.65] tracking-tight mb-8 text-white" style="line-height: 1.65 !important;">
                        <span class="block mb-2">آماده‌اید مدیریت کانال‌های خود را</span>
                        <span class="bg-gradient-to-l from-indigo-400 to-pink-400 bg-clip-text text-transparent block">هوشمند کنید؟</span>
                    </h2>
                    <p class="text-neutral-400 text-base sm:text-lg leading-relaxed mb-10">
                        همین حالا به جمع مدیران حرفه‌ای بپیوندید و ارسال خودکار، ربات نرخ طلا و اتوماسیون کانال‌های تلگرام و بله را تجربه کنید.
                    </p>
                    <button onclick="openModal('register')" class="group inline-flex items-center justify-center gap-2 px-10 py-5 rounded-2xl bg-gradient-to-r from-indigo-500 to-pink-500 text-white font-bold text-lg hover:from-indigo-600 hover:to-pink-600 transition-all duration-300 shadow-2xl shadow-indigo-500/30 pulse-glow">
                        <span>ثبت‌نام و شروع رایگان</span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="relative pt-16 pb-12 border-t border-neutral-800/60 bg-[#0a0a0a]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 pb-10 border-b border-neutral-800/60">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-500 via-purple-500 to-pink-500 p-0.5">
                        <div class="w-full h-full bg-[#0a0a0a] rounded-[10px] flex items-center justify-center overflow-hidden">
                            <img src="<?php echo \WHCM\Core\Bootstrap::getAssetsUrl(); ?>/images/logo-white-bg.webp" alt="پُست‌یار" class="w-full h-full object-contain">
                        </div>
                    </div>
                    <span class="text-xl font-black text-white">پُست‌یار</span>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-8 text-sm text-neutral-400">
                    <a href="#features" class="hover:text-white transition-colors">امکانات سیستم</a>
                    <a href="#how-it-works" class="hover:text-white transition-colors">نحوه کارکرد</a>
                    <a href="#pricing" class="hover:text-white transition-colors">تعرفه اشتراک</a>
                    <a href="#testimonials" class="hover:text-white transition-colors">نظرات مدیران</a>
                    <a href="#faq" class="hover:text-white transition-colors">سوالات متداول</a>
                </div>
            </div>
            <div class="pt-8 text-center text-xs text-neutral-500">
                <p>تمامی حقوق مادی و معنوی محفوظ است. توسعه یافته تحت فناوری‌های پیشرفته گروه پُست‌یار.</p>
            </div>
        </div>
    </footer>

    <!-- ========================================== -->
    <!-- مدال‌های احراز هویت (ورود، ثبت‌نام، بازیابی) -->
    <!-- ========================================== -->
    <div id="modal-login" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal('login')">✖</button>
            <h2 class="text-xl font-black text-white text-center mb-6">🔐 ورود به پیشخوان پُست‌یار</h2>
            <form action="<?php echo \WHCM\Core\Bootstrap::getRouteUrl('/login'); ?>" method="POST">
                <?php echo $csrf_field ?? ''; ?>
                <div class="form-group">
                    <label>نشانی ایمیل:</label>
                    <input type="email" name="email" required placeholder="name@example.com">
                </div>
                <div class="form-group">
                    <label>رمز عبور:</label>
                    <input type="password" name="password" required placeholder="رمز عبور حساب">
                </div>
                <div class="form-group">
                    <label>سوال امنیتی ضد ربات: <strong style="color:var(--primary);"><?php echo $captcha_question ?? '۵ + ۳ = ؟'; ?></strong></label>
                    <input type="number" name="captcha" required placeholder="پاسخ عددی">
                </div>
                <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-indigo-500 to-pink-500 text-white font-bold">ورود به حساب کاربری 🔑</button>
            </form>
        </div>
    </div>

    <div id="modal-register" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal('register')">✖</button>
            <h2 class="text-xl font-black text-white text-center mb-6">✨ ساخت حساب کاربری جدید</h2>
            <form action="<?php echo \WHCM\Core\Bootstrap::getRouteUrl('/register'); ?>" method="POST">
                <?php echo $csrf_field ?? ''; ?>
                <div class="form-group">
                    <label>نام و نام خانوادگی:</label>
                    <input type="text" name="name" required placeholder="هومن نقشی">
                </div>
                <div class="form-group">
                    <label>نشانی ایمیل:</label>
                    <input type="email" name="email" required placeholder="name@example.com">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>نام کسب‌وکار:</label>
                        <input type="text" name="business_name" required placeholder="مثلاً: گالری طلا آسوین">
                    </div>
                    <div class="form-group">
                        <label>نوع فعالیت:</label>
                        <input type="text" name="business_type" required placeholder="مثلاً: طلا و جواهر">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>رمز عبور امن:</label>
                        <input type="password" name="password" required placeholder="حداقل ۸ کاراکتر">
                    </div>
                    <div class="form-group">
                        <label>تکرار رمز عبور:</label>
                        <input type="password" name="password_confirm" required placeholder="تکرار رمز عبور">
                    </div>
                </div>
                <div class="form-group">
                    <label>سوال امنیتی ضد ربات: <strong style="color:var(--primary);"><?php echo $captcha_question ?? '۷ + ۲ = ؟'; ?></strong></label>
                    <input type="number" name="captcha" required placeholder="پاسخ عددی">
                </div>
                <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-indigo-500 to-pink-500 text-white font-bold">ایجاد حساب کاربری ✨</button>
            </form>
        </div>
    </div>

    <!-- ===== JAVASCRIPT ===== -->
    <script src="<?php echo \WHCM\Core\Bootstrap::getAssetsUrl(); ?>/js/home.js"></script>

    <!-- PWA: ثبت سرویس ورکر و بنر نصب (فقط موبایل/تبلت) -->
    <script>
    (function(){
        var baseUrl = '<?php echo $baseUrl; ?>';
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register(baseUrl + '/service-worker.js', { scope: baseUrl + '/' })
                .catch(function() {});
        }
    })();
    </script>
    <script src="<?php echo \WHCM\Core\Bootstrap::getAssetsUrl(); ?>/js/pwa-install.js"></script>
</body>
</html>