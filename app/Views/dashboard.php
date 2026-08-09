<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?> | پُست‌یار</title>
    <link rel="stylesheet" href="<?php echo \WHCM\Core\Bootstrap::getAssetsUrl(); ?>/css/dashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jalalidatepicker@latest/dist/jalalidatepicker.min.css">
    <style id="modern-jdp-style">
        /* ظاهر شیک و مدرن هماهنگ با تم تاریک پُست‌یار برای تقویم شمسی */
        .jdp-container {
            background: #0f172a !important;
            border: 1px solid #6366f1 !important;
            border-radius: 16px !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.8), 0 0 25px rgba(99, 102, 241, 0.25) !important;
            color: #e2e8f0 !important;
            font-family: 'Vazirmatn', sans-serif !important;
            z-index: 9999999 !important;
            padding: 0.85rem !important;
        }
        .jdp-container .jdp-icon-plus, .jdp-container .jdp-icon-minus {
            fill: #818cf8 !important;
        }
        .jdp-container .jdp-months, .jdp-container .jdp-years {
            background: #1e293b !important;
            border-radius: 10px !important;
        }
        .jdp-container .jdp-day, .jdp-container .jdp-month, .jdp-container .jdp-year {
            border-radius: 8px !important;
            transition: all 0.2s ease !important;
        }
        .jdp-container .jdp-day:hover, .jdp-container .jdp-month:hover, .jdp-container .jdp-year:hover {
            background: rgba(99, 102, 241, 0.25) !important;
            color: #ffffff !important;
            transform: scale(1.05) !important;
        }
        .jdp-container .jdp-day.selected, .jdp-container .jdp-month.selected, .jdp-container .jdp-year.selected {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
            color: #ffffff !important;
            font-weight: 900 !important;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.5) !important;
        }
        .jdp-container .jdp-day.today {
            border: 2px solid #34d399 !important;
            color: #34d399 !important;
        }
        .jdp-container .jdp-footer {
            background: #1e293b !important;
            border-top: 1px dashed #334155 !important;
            border-radius: 0 0 12px 12px !important;
        }
        .jdp-container .jdp-btn-today {
            background: #10b981 !important;
            color: white !important;
            border-radius: 8px !important;
            font-weight: bold !important;
            padding: 0.35rem 1rem !important;
        }
    
        @media (max-width: 768px){
            #user-bell-popup{
                position:fixed !important;
                left:50% !important;
                top:50% !important;
                transform:translate(-50%,-50%) !important;
                width:90vw !important;
                max-width:340px !important;
                max-height:80vh;
                overflow:auto;
            }
        }

    
        header{overflow:visible !important;}
        #user-bell-popup{max-height:75vh; overflow:auto;}
        @media (min-width: 769px){
            #user-bell-popup{top:60px !important;}
        }

    </style>
</head>
<body>

    <!-- توستر کپی کارت بانکی -->
    <div id="copy-toast" class="toast">شماره کارت با موفقیت کپی شد! 📋</div>

    <!-- هدر بالای صفحه با لوگوی اختصاصی پُست‌یار -->
    <header>
        <div class="logo-container">
            <img src="<?php echo \WHCM\Core\Bootstrap::getAssetsUrl(); ?>/images/logo.webp" alt="پُست‌یار" class="logo-img">
            <span class="logo-text">پُست‌یار</span>
        </div>
        <div style="display:flex; align-items:center; gap:0.75rem;">
            <!-- دکمه زنگوله اعلان‌های کاربر -->
            <div style="position:relative;">
                <button type="button" onclick="var p=document.getElementById('user-bell-popup'); p.style.display=(p.style.display==='flex'?'none':'flex');" style="background:rgba(15,23,42,0.85); border:1px solid rgba(99,102,241,0.4); border-radius:50%; width:40px; height:40px; display:flex; align-items:center; justify-content:center; color:white; font-size:1.15rem; cursor:pointer; box-shadow:0 4px 12px rgba(0,0,0,0.4);">
                    <span>🔔</span>
                    <?php if (!empty($announcement)): ?>
                        <span style="position:absolute; top:2px; right:2px; width:10px; height:10px; background:#ef4444; border-radius:50%; border:2px solid #0f172a;"></span>
                    <?php endif; ?>
                </button>
                <div id="user-bell-popup" style="display:none; position:absolute; left:0; top:60px; width:290px; background:#0f172a; border:1px solid #4f46e5; border-radius:16px; box-shadow:0 15px 35px rgba(0,0,0,0.85); z-index:9999; flex-direction:column; padding:1rem;">
                    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px dashed #334155; padding-bottom:0.6rem; margin-bottom:0.75rem;">
                        <strong style="color:white; font-size:0.9rem;">🔔 صندوق اعلان‌ها و اخبار</strong>
                    </div>
                    <?php if (!empty($announcement)): ?>
                        <div style="padding:0.65rem; background:#1e293b; border-radius:10px; border-left:3px solid #6366f1; margin-bottom:0.5rem; cursor:pointer;" onclick="switchSection('dashboard'); document.getElementById('user-bell-popup').style.display='none';">
                            <div style="font-weight:900; color:#38bdf8; font-size:0.85rem; margin-bottom:0.3rem;">📢 <?php echo htmlspecialchars($announcement['title']); ?></div>
                            <div style="font-size:0.78rem; color:#cbd5e1;"><?php echo htmlspecialchars(mb_substr($announcement['message'], 0, 70)) . '...'; ?></div>
                        </div>
                    <?php else: ?>
                        <div style="color:#94a3b8; font-size:0.8rem; text-align:center; padding:1rem 0;">اعلان جدیدی وجود ندارد ✔</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- نشان جذاب و چندرنگ کاربر و نوع اشتراک -->
            <div class="user-badge" style="display:flex; align-items:center; gap:0.6rem; background:rgba(15, 23, 42, 0.9); border:1px solid rgba(99, 102, 241, 0.45); padding:0.35rem 1rem; border-radius:9999px; box-shadow:0 4px 15px rgba(0,0,0,0.5);">
                <span style="display:flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:50%; background:linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color:#ffffff; font-size:0.9rem; font-weight:bold;">
                    👤
                </span>
                <span style="color:#f8fafc; font-weight:850; font-size:0.92rem;">
                    <?php echo htmlspecialchars($user['name']); ?>
                </span>
                <span style="background:linear-gradient(135deg, #10b981 0%, #059669 100%); color:#ffffff; font-size:0.78rem; font-weight:900; padding:0.2rem 0.75rem; border-radius:12px; box-shadow:0 2px 8px rgba(16, 185, 129, 0.35);">
                    💎 <?php echo \WHCM\Domain\TextFormat::fa_digits($quota['plan_title']); ?>
                </span>
            </div>
        </div>
    </header>

    <!-- کانتینر اصلی محتوا -->
    <div class="wrapper">
        
        <!-- سایدبار دسکتاپ -->
        <aside>
            <div class="menu-item active" data-target="dashboard" onclick="switchSection('dashboard')">🏠 وضعیت کلی</div>
            <div class="menu-item" data-target="publish" onclick="switchSection('publish')">✉ ارسال پست جدید</div>
            <div class="menu-item" data-target="channels" onclick="switchSection('channels')">📻 مدیریت کانال‌ها</div>
            <?php if (!empty($quota['features']['gold_ticker'])): ?>
                <div class="menu-item" data-target="ticker" onclick="switchSection('ticker')">🪙 ربات طلا و سکه</div>
            <?php endif; ?>
            <?php if (!empty($quota['features']['auto_responder'])): ?>
                <div class="menu-item" data-target="responder" onclick="switchSection('responder')">🤖 پاسخگوی خودکار</div>
            <?php endif; ?>
            <div class="menu-item" data-target="tickets" onclick="switchSection('tickets')">🎫 پشتیبانی و تیکت‌ها</div>
            <div class="menu-item" data-target="inbox" onclick="switchSection('inbox')">📩 صندوق پیام</div>
            <div class="menu-item" data-target="settings" onclick="switchSection('settings')">👤 تنظیمات حساب</div>
            <div class="menu-item" data-target="advanced-settings" onclick="switchSection('advanced-settings')">⚙ تنظیمات پیشرفته</div>
            <div class="menu-item" data-target="upgrade" onclick="switchSection('upgrade')">💎 خرید اشتراک</div>
            <?php if (\WHCM\Core\Auth::isSuperAdmin()): ?>
                <a href="<?php echo \WHCM\Core\Bootstrap::getRouteUrl('/hnnh'); ?>" class="menu-item" style="color: var(--warning); border: 1px dashed var(--warning); margin-top: 1rem;">👑 پنل مدیریت کل</a>
            <?php endif; ?>
            <a href="<?php echo \WHCM\Core\Bootstrap::getRouteUrl('/logout'); ?>" class="menu-item logout-btn">🚪 خروج از حساب</a>
        </aside>

        <!-- منوی تب موبایل (Native-like) -->
        <div class="mobile-nav">
            <div class="mobile-nav-item active" data-target="dashboard">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span>داشبورد</span>
            </div>
            <div class="mobile-nav-item" data-target="publish">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                <span>پست جدید</span>
            </div>
            <div class="mobile-nav-item" data-target="channels">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z"></path></svg>
                <span>کانال‌ها</span>
            </div>
            <div class="mobile-nav-item" data-target="tickets">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                <span>تیکت‌ها</span>
            </div>
            <div class="mobile-nav-item" data-target="settings">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                <span>تنظیمات</span>
            </div>
        </div>

        <!-- محتوای صفحات -->
        <main>
            <?php if (!empty($message)): ?>
                <div class="alert" id="system-alert-toast" style="position:relative; display:flex; justify-content:space-between; align-items:center;">
                    <span><?php echo htmlspecialchars($message); ?></span>
                    <button type="button" onclick="document.getElementById('system-alert-toast').style.display='none'" style="background:none; border:none; color:white; font-size:1.1rem; cursor:pointer; margin-right:1rem;">✖</button>
                </div>
                <script>
                    setTimeout(function() {
                        var toast = document.getElementById('system-alert-toast');
                        if (toast) {
                            toast.style.opacity = '0';
                            toast.style.transition = 'opacity 0.6s ease';
                            setTimeout(function() { toast.style.display = 'none'; }, 600);
                        }
                    }, 5000);
                </script>
            <?php endif; ?>

            <!-- نمایش اعلان همگانی مدیر کل پلتفرم در صورت وجود -->
            <?php if (!empty($announcement)): ?>
                <div class="broadcast-alert" id="broadcast-alert-banner">
                    <span class="broadcast-alert-close" onclick="closeBroadcastBanner()">✖</span>
                    <h4 style="font-weight:900; margin-bottom:0.4rem; color:#ffffff;">📢 پیام همگانی مدیریت: <?php echo htmlspecialchars($announcement['title']); ?></h4>
                    <p style="font-size:0.85rem; line-height:1.7; color:#cbd5e1;"><?php echo nl2br(htmlspecialchars($announcement['message'])); ?></p>
                    <span style="font-size:0.75rem; color:#818cf8; display:inline-block; margin-top:0.5rem; font-weight:bold;">ثبت شده در تاریخ: <?php echo $announcement['date']; ?></span>
                </div>
            <?php endif; ?>

            <!-- ========================================== -->
            <!-- ۱. بخش وضعیت کلی و گراف آماری لوکس -->
            <!-- ========================================== -->
            <div id="section-dashboard" class="tab-content active">
                
                <!-- سه باکس آماری درخشان و هدفمند -->
                <div class="grid-stats" style="margin-bottom:0.5rem;">
                    <div class="card-stat" style="border-color: rgba(99,102,241,0.25);">
                        <div class="card-stat-icon" style="color:#6366f1;">📈</div>
                        <div class="card-stat-info">
                            <span class="title">کل بازدیدهای ورودی (کلیک کل)</span>
                            <?php 
                                $total_clicks = 0;
                                foreach ($posts as $pst) { $total_clicks += (int)$pst['clicks']; }
                            ?>
                            <span class="value"><?php echo \WHCM\Domain\TextFormat::fa_digits($total_clicks); ?> کلیک</span>
                        </div>
                    </div>
                    <div class="card-stat" style="border-color: rgba(16,185,129,0.25);">
                        <div class="card-stat-icon" style="color:#10b981;">👥</div>
                        <div class="card-stat-info">
                            <span class="title">بازدیدهای یکتای حقیقی (Unique)</span>
                            <?php 
                                $unique_clicks = 0;
                                foreach ($posts as $pst) { $unique_clicks += (int)$pst['unique_clicks']; }
                            ?>
                            <span class="value"><?php echo \WHCM\Domain\TextFormat::fa_digits($unique_clicks); ?> کاربر</span>
                        </div>
                    </div>
                    <div class="card-stat" style="border-color: rgba(245,158,11,0.25);">
                        <div class="card-stat-icon" style="color:#f59e0b;">⚡</div>
                        <div class="card-stat-info">
                            <span class="title">نرخ تعامل کانال‌های شما</span>
                            <?php 
                                $ratio = $total_clicks > 0 ? round(($unique_clicks / $total_clicks) * 100) : 0;
                            ?>
                            <span class="value">%<?php echo \WHCM\Domain\TextFormat::fa_digits($ratio); ?> تعامل</span>
                        </div>
                    </div>
                </div>

                <div class="grid-stats">
                    <div class="card-stat">
                        <div class="card-stat-icon">📻</div>
                        <div class="card-stat-info">
                            <span class="title">کانال‌های متصل شده</span>
                            <span class="value"><?php echo \WHCM\Domain\TextFormat::fa_digits($quota['used_channels']); ?> / <?php echo \WHCM\Domain\TextFormat::fa_digits($quota['max_channels']); ?></span>
                        </div>
                    </div>
                    <div class="card-stat">
                        <div class="card-stat-icon">📝</div>
                        <div class="card-stat-info">
                            <span class="title">پست‌های ارسالی این دوره</span>
                            <span class="value"><?php echo \WHCM\Domain\TextFormat::fa_digits($quota['used_posts']); ?> / <?php echo $quota['max_posts'] === 0 ? 'نامحدود' : \WHCM\Domain\TextFormat::fa_digits($quota['max_posts']); ?></span>
                        </div>
                    </div>
                    <div class="card-stat">
                        <div class="card-stat-icon">⏰</div>
                        <div class="card-stat-info">
                            <span class="title">تاریخ اتمام اشتراک</span>
                            <span class="value" style="font-size: 1.05rem; font-weight: bold;">
                                <?php echo $quota['end_date'] ? \WHCM\Domain\TextFormat::mysql_to_jalali($quota['end_date']) : 'بدون انقضا'; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- گراف آماری جامع و درخشان SVG بومی -->
                <div class="card">
                    <h2>📊 نمودار تحلیل مقایسه‌ای پیشرفته کانال‌ها</h2>
                    <div class="analytics-graph">
                        <svg viewBox="0 0 500 200" style="width: 100%; height: 100%;">
                            <!-- گرید لاین‌ها -->
                            <line x1="50" y1="30" x2="480" y2="30" stroke="rgba(255,255,255,0.05)" />
                            <line x1="50" y1="80" x2="480" y2="80" stroke="rgba(255,255,255,0.05)" />
                            <line x1="50" y1="130" x2="480" y2="130" stroke="rgba(255,255,255,0.05)" />
                            <line x1="50" y1="170" x2="480" y2="170" stroke="rgba(255,255,255,0.1)" stroke-width="2" />

                            <!-- راهنما -->
                            <text x="55" y="20" fill="rgba(99, 102, 241, 0.8)" font-size="9" font-family="Vazirmatn">● میزان کلیک‌ها (تک کلیک)</text>
                            <text x="180" y="20" fill="rgba(16, 185, 129, 0.8)" font-size="9" font-family="Vazirmatn">● میزان کلیک‌های یکتا (Unique)</text>

                            <!-- نمودار خطی کلیک کل (آبی نئون) -->
                            <path d="M 50 160 Q 120 120 190 70 T 330 110 T 470 40" fill="none" stroke="#6366f1" stroke-width="3" stroke-linecap="round" />
                            <circle cx="470" cy="40" r="5" fill="#6366f1" />

                            <!-- نمودار خطی کلیک یکتا (سبز نئون) -->
                            <path d="M 50 170 Q 120 140 190 90 T 330 130 T 470 60" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" />
                            <circle cx="470" cy="60" r="5" fill="#10b981" />

                            <!-- محور افقی روزهای شمسی دوره -->
                            <text x="50" y="190" fill="var(--text-muted)" font-size="8" font-family="Vazirmatn">شروع دوره</text>
                            <text x="250" y="190" fill="var(--text-muted)" font-size="8" font-family="Vazirmatn">میانه دوره</text>
                            <text x="440" y="190" fill="var(--text-muted)" font-size="8" font-family="Vazirmatn">امروز شمسی</text>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ۲. بخش ارسال پست جدید (Publish) -->
            <!-- ========================================== -->
            <div id="section-publish" class="tab-content">
                <div class="card">
                    <h2>✉ ایجاد و انتشار پست جدید در کانال‌ها</h2>
                    <form action="<?php echo \WHCM\Core\Bootstrap::getRouteUrl('/dashboard/add-post'); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo $csrf_field; ?>
                        
                        <div class="form-group">
                            <label for="p-title">عنوان پست:</label>
                            <input type="text" name="title" id="p-title" required placeholder="مثلاً: رونمایی از کلکسیون طلای جدید آسوین">
                        </div>

                        <div class="form-group" style="position:relative;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
                                <label for="p-content" style="margin:0;">محتوای پست (متن پیام):</label>
                                <!-- پک استیکر و اموجی تلگرامی پاپ‌آپ بومی پُست‌یار -->
                                <div class="emoji-picker-container">
                                    <button type="button" class="emoji-picker-btn" onclick="toggleEmojiPicker()">😀 افزودن استیکر و اموجی پُست‌یار</button>
                                    <div class="emoji-popup" id="emoji-popup">
                                        <div class="emoji-tabs">
                                            <span class="emoji-tab active" onclick="switchEmojiTab('face')">😀</span>
                                            <span class="emoji-tab" onclick="switchEmojiTab('objects')">💰</span>
                                            <span class="emoji-tab" onclick="switchEmojiTab('arrows')">🔺</span>
                                        </div>
                                        <div class="emoji-grid" id="emoji-grid-face">
                                            <?php 
                                                $smileys = ['😀','😃','😄','😁','😆','😅','😂','🤣','👍','🔥','❤️','🎉','✨','✅','❌','⏳'];
                                                foreach ($smileys as $em) {
                                                    echo "<span class='emoji-item' onclick='insertEmoji(\"{$em}\")'>{$em}</span>";
                                                }
                                            ?>
                                        </div>
                                        <div class="emoji-grid hidden" id="emoji-grid-objects">
                                            <?php 
                                                $objects = ['🌟','🪙','💰','📈','📉','⏰','💎','📻','✉','⚙','🚀','💬','👑','💳','🛒','🛍','📦'];
                                                foreach ($objects as $em) {
                                                    echo "<span class='emoji-item' onclick='insertEmoji(\"{$em}\")'>{$em}</span>";
                                                }
                                            ?>
                                        </div>
                                        <div class="emoji-grid hidden" id="emoji-grid-arrows">
                                            <?php 
                                                $arrows = ['🔺','🔻','🔸','🔹','◽','◾','🔗','⚡','✈','🔽','🔼'];
                                                foreach ($arrows as $em) {
                                                    echo "<span class='emoji-item' onclick='insertEmoji(\"{$em}\")'>{$em}</span>";
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <textarea name="content" id="p-content" rows="6" required placeholder="متن پیام خود را بنویسید..."></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="p-media">بارگذاری و آپلود تصویر شاخص (فرمت بهینه وب‌پی خودکار):</label>
                                <input type="file" name="media_file" id="p-media" accept="image/*" style="padding:0.5rem 1rem;">
                            </div>
                            <div class="form-group">
                                <label for="p-send-type">نوع انتشار:</label>
                                <select name="send_type" id="p-send-type" onchange="toggleScheduleInput(this.value)">
                                    <option value="instant">ارسال آنی و سریع ⚡</option>
                                    <option value="scheduled">زمان‌بندی ارسال خودکار ⏰</option>
                                </select>
                            </div>
                        </div>

                        <!-- زمان‌بندی ارسال شمسی با تقویم تصویری فوق جذاب و مدرن -->
                        <div class="form-group hidden" id="schedule-datetime-group">
                            <label style="color:#a5b4fc; font-weight:bold; display:block; margin-bottom:0.75rem;">📅 انتخاب تاریخ و ساعت دقیق ارسال:</label>
                            <div style="display:grid; grid-template-columns: 2fr 1fr 1fr; gap:1rem;">
                                <div>
                                    <label style="font-size:0.75rem; color:var(--text-muted);">انتخاب روز از تقویم:</label>
                                    <input type="text" name="sched_date" id="sched_date_input" data-jdp placeholder="کلیک کنید تا تقویم باز شود..." style="background-color: rgba(15,23,42,0.6); color: #34d399; font-weight: bold; border: 2px solid #34d399; border-radius:12px; padding:0.85rem 1rem; cursor: pointer;" readonly onfocus="if(typeof jalaliDatepicker !== 'undefined'){try{jalaliDatepicker.show(this);}catch(e){}}" onclick="if(typeof jalaliDatepicker !== 'undefined'){try{jalaliDatepicker.show(this);}catch(e){}}">
                                </div>
                                <div>
                                    <label style="font-size:0.75rem; color:var(--text-muted);">ساعت:</label>
                                    <select name="sched_hour" id="sched_hour" style="border-radius:12px;">
                                        <?php for($h=0; $h<=23; $h++): ?>
                                            <option value="<?php echo str_pad($h,2,'0',STR_PAD_LEFT); ?>"><?php echo \WHCM\Domain\TextFormat::fa_digits(str_pad($h,2,'0',STR_PAD_LEFT)); ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div>
                                    <label style="font-size:0.75rem; color:var(--text-muted);">دقیقه:</label>
                                    <select name="sched_minute" id="sched_minute" style="border-radius:12px;">
                                        <?php for($i=0; $i<=59; $i++): ?>
                                            <option value="<?php echo str_pad($i,2,'0',STR_PAD_LEFT); ?>"><?php echo \WHCM\Domain\TextFormat::fa_digits(str_pad($i,2,'0',STR_PAD_LEFT)); ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- چک‌باکس کانال‌های مقصد -->
                        <div class="form-group">
                            <label style="margin-bottom:0.75rem;">انتخاب کانال‌های هدف جهت انتشار پست:</label>
                            <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; background: rgba(15,23,42,0.4); padding: 1rem; border-radius: 12px; border:1px solid var(--border);">
                                <?php if (empty($channels)): ?>
                                    <span style="color:var(--text-muted); font-size:0.85rem;">هنوز کانالی متصل نکرده‌اید. ابتدا از تب کانال‌ها یک کانال ثبت کنید.</span>
                                <?php else: ?>
                                    <?php foreach ($channels as $ch): ?>
                                        <label class="toggle-container" style="background: none; border: none; padding: 0;">
                                            <input type="checkbox" name="post_channels[]" value="<?php echo $ch['id']; ?>" class="toggle-input" checked>
                                            <span><?php echo htmlspecialchars($ch['name']); ?> (<?php echo $ch['platform'] === 'telegram' ? 'تلگرام' : 'بله'; ?>)</span>
                                        </label>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <button type="submit" class="btn">انتشار و زمان‌بندی پست در پُست‌یار 🚀</button>
                    </form>
                </div>

                <!-- تاریخچه پست‌های ارسالی -->
                <div class="card">
                    <h2>📋 تاریخچه پست‌های ارسالی و زمان‌بندی شده شما</h2>
                    <?php if (empty($posts)): ?>
                        <p style="color: var(--text-muted); text-align: center; padding: 2rem 0;">هنوز پستی ارسال یا ثبت نکرده‌اید.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>عنوان پست</th>
                                        <th>نوع ارسال</th>
                                        <th>وضعیت</th>
                                        <th>زمان ثبت / ارسال</th>
                                        <th>بازدید کل (کلیک)</th>
                                        <th>کلیک‌های یکتا</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($posts as $p): ?>
                                        <tr>
                                            <td data-label="عنوان پست"><strong><?php echo htmlspecialchars($p['title']); ?></strong></td>
                                            <td data-label="نوع ارسال">
                                                <span class="badge" style="background:rgba(255,255,255,0.05);">
                                                    <?php echo $p['scheduled_at'] ? 'زمان‌بندی شده ⏰' : 'آنی ⚡'; ?>
                                                </span>
                                            </td>
                                            <td data-label="وضعیت">
                                                <?php if ($p['status'] === 'sent'): ?>
                                                    <span class="badge badge-success">ارسال شده ✔</span>
                                                <?php elseif ($p['status'] === 'scheduled'): ?>
                                                    <span class="badge badge-scheduled">در انتظار ارسال ⏳</span>
                                                <?php else: ?>
                                                    <span class="badge badge-failed">خطا در ارسال ❌</span>
                                                <?php endif; ?>
                                            </td>
                                            <td data-label="زمان ثبت / ارسال"><span style="font-size:0.8rem;"><?php echo \WHCM\Domain\TextFormat::mysql_to_jalali($p['created_at']); ?></span></td>
                                            <td data-label="بازدید کل (کلیک)"><strong style="color:var(--primary);"><?php echo \WHCM\Domain\TextFormat::fa_digits($p['clicks']); ?> بازدید</strong></td>
                                            <td data-label="کلیک‌های یکتا"><strong><?php echo \WHCM\Domain\TextFormat::fa_digits($p['unique_clicks']); ?> کلیک</strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ۳. بخش مدیریت کانال‌ها به صورت دو باکس مجزا متقارن -->
            <!-- ========================================== -->
            <div id="section-channels" class="tab-content">
                
                <!-- حالت ویرایش تنظیمات کانال -->
                <?php if ($edit_channel): ?>
                    <div class="card" style="border-color: var(--primary);">
                        <h2>⚙ ویرایش تنظیمات کانال: «<?php echo htmlspecialchars($edit_channel['name']); ?>»</h2>
                        <form action="<?php echo \WHCM\Core\Bootstrap::getRouteUrl('/dashboard/edit-channel'); ?>" method="POST">
                            <?php echo $csrf_field; ?>
                            <input type="hidden" name="channel_id" value="<?php echo $edit_channel['id']; ?>">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="edit-name">نام نمایشی کانال:</label>
                                    <input type="text" name="name" id="edit-name" value="<?php echo htmlspecialchars($edit_channel['name']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="edit-platform">پلتفرم:</label>
                                    <select name="platform" id="edit-platform" required>
                                        <option value="telegram" <?php echo $edit_channel['platform'] === 'telegram' ? 'selected' : ''; ?>>تلگرام</option>
                                        <option value="bale" <?php echo $edit_channel['platform'] === 'bale' ? 'selected' : ''; ?>>بله (Bale)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="edit-channel_id_val">آیدی کانال (مانند @MyGoldShop):</label>
                                    <input type="text" name="channel_id_val" id="edit-channel_id_val" value="<?php echo htmlspecialchars($edit_channel['channel_id']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="edit-token">توکن ربات:</label>
                                    <input type="password" name="token" id="edit-token" value="<?php echo htmlspecialchars($edit_channel['token']); ?>" required>
                                </div>
                            </div>

                            <!-- تنظیمات ۳ لینک اختصاصی زیر هر پست -->
                            <h3 style="font-size: 0.95rem; margin-top: 1rem; margin-bottom: 0.75rem; border-bottom: 1px dashed var(--border); padding-bottom: 0.4rem; color:#a5b4fc;">🔗 تنظیمات ۳ دکمه شیشه‌ای کپشن (لینک وب‌سایت)</h3>
                            <?php 
                                $links = json_decode($edit_channel['link_config'] ?? '[]', true); 
                                if (count($links) < 3) {
                                    $links = [['name'=>'', 'url'=>''], ['name'=>'', 'url'=>''], ['name'=>'', 'url'=>'']];
                                }
                            ?>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>دکمه ۱ - عنوان و آدرس لینک:</label>
                                    <input type="text" name="link_name_1" value="<?php echo htmlspecialchars($links[0]['name'] ?? ''); ?>" placeholder="عنوان دکمه اول" style="margin-bottom:0.5rem;">
                                    <input type="text" name="link_url_1" value="<?php echo htmlspecialchars($links[0]['url'] ?? ''); ?>" placeholder="https://example.com/shop">
                                </div>
                                <div class="form-group">
                                    <label>دکمه ۲ - عنوان و آدرس لینک:</label>
                                    <input type="text" name="link_name_2" value="<?php echo htmlspecialchars($links[1]['name'] ?? ''); ?>" placeholder="عنوان دکمه دوم" style="margin-bottom:0.5rem;">
                                    <input type="text" name="link_url_2" value="<?php echo htmlspecialchars($links[1]['url'] ?? ''); ?>" placeholder="https://example.com/t.me">
                                </div>
                            </div>
                            <div class="form-group" style="max-width: 50%;">
                                <label>دکمه ۳ - عنوان (آدرس آن خودکار به ردیاب کلیک تبدیل می‌شود):</label>
                                <input type="text" name="link_name_3" value="<?php echo htmlspecialchars($links[2]['name'] ?? ''); ?>" placeholder="مثلاً: ورود به فروشگاه">
                                <input type="hidden" name="link_url_3" value="">
                            </div>

                            <!-- تنظیمات دکمه‌های شیشه‌ای تعاملی زیرین -->
                            <h3 style="font-size: 0.95rem; margin-top: 1rem; margin-bottom: 0.75rem; border-bottom: 1px dashed var(--border); padding-bottom: 0.4rem; color:#a5b4fc;">💬 دکمه‌های شیشه‌ای تعاملی زیر پست (Interactive Buttons)</h3>
                            <?php 
                                $btn_cfg = json_decode($edit_channel['button_config'] ?? '[]', true); 
                                $btns_active = !empty($btn_cfg['active']);
                                $btns = $btn_cfg['buttons'] ?? [['text'=>'', 'url'=>''], ['text'=>'', 'url'=>'']];
                            ?>
                            <div class="form-group">
                                <label class="toggle-container">
                                    <input type="checkbox" name="buttons_active" value="1" class="toggle-input" <?php echo $btns_active ? 'checked' : ''; ?>>
                                    <span>فعال‌سازی دکمه‌های تعاملی شیشه‌ای زیر پیام در زمان ارسال</span>
                                </label>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>دکمه تعاملی ۱ - متن و آدرس دکمه:</label>
                                    <input type="text" name="btn_text_1" value="<?php echo htmlspecialchars($btns[0]['text'] ?? ''); ?>" placeholder="ارتباط با ادمین" style="margin-bottom:0.5rem;">
                                    <input type="text" name="btn_url_1" value="<?php echo htmlspecialchars($btns[0]['url'] ?? ''); ?>" placeholder="https://t.me/your_admin">
                                </div>
                                <div class="form-group">
                                    <label>دکمه تعاملی ۲ - متن و آدرس دکمه:</label>
                                    <input type="text" name="btn_text_2" value="<?php echo htmlspecialchars($btns[1]['text'] ?? ''); ?>" placeholder="سفارش فوری" style="margin-bottom:0.5rem;">
                                    <input type="text" name="btn_url_2" value="<?php echo htmlspecialchars($btns[1]['url'] ?? ''); ?>" placeholder="https://asovin.ir">
                                </div>
                            </div>

                            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                                <button type="submit" class="btn btn-success">ذخیره تنظیمات کانال ✔</button>
                                <a href="<?php echo \WHCM\Core\Bootstrap::getRouteUrl('/dashboard'); ?>" class="btn btn-danger" style="background: rgba(255,255,255,0.08); color: white;">انصراف</a>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>

                <!-- نمایش دو باکسی متقارن و در کنار هم کانال‌ها (طراحی روز دنیا) -->
                <div class="card">
                    <h2>📻 لیست تفکیکی کانال‌های متصل شده شما</h2>
                    <div class="grid-channels">
                        
                        <!-- باکس کانال‌های تلگرام -->
                        <div class="channel-box">
                            <div class="channel-box-title">🔵 کانال‌های فعال تلگرام</div>
                            <?php 
                                $tg_channels = array_filter($channels, function($c) { return $c['platform'] === 'telegram'; });
                                if (empty($tg_channels)):
                            ?>
                                <p style="color:var(--text-muted); text-align:center; font-size:0.8rem; padding: 1rem 0;">هنوز هیچ کانال تلگرامی متصل نکرده‌اید.</p>
                            <?php else: foreach ($tg_channels as $ch): ?>
                                <div class="channel-item">
                                    <div>
                                        <strong><?php echo htmlspecialchars($ch['name']); ?></strong><br>
                                        <code style="font-size:0.75rem; color:var(--text-muted);"><?php echo htmlspecialchars($ch['channel_id']); ?></code>
                                    </div>
                                    <div style="display:flex; gap:0.25rem;">
                                        <a href="?edit_channel=<?php echo $ch['id']; ?>" class="btn btn-sm" style="background:#3b82f6; padding:0.35rem 0.65rem;">⚙</a>
                                        <a href="<?php echo \WHCM\Core\Bootstrap::getRouteUrl('/dashboard/delete-channel'); ?>&id=<?php echo $ch['id']; ?>" class="btn btn-danger btn-sm" style="padding:0.35rem 0.65rem;" onclick="return confirm('آیا از حذف این کانال اطمینان دارید؟');">🗑</a>
                                    </div>
                                </div>
                            <?php endforeach; endif; ?>
                        </div>

                        <!-- باکس کانال‌های بله -->
                        <div class="channel-box">
                            <div class="channel-box-title">🟢 کانال‌های فعال بله</div>
                            <?php 
                                $bale_channels = array_filter($channels, function($c) { return $c['platform'] === 'bale'; });
                                if (empty($bale_channels)):
                            ?>
                                <p style="color:var(--text-muted); text-align:center; font-size:0.8rem; padding: 1rem 0;">هنوز هیچ کانال بله متصل نکرده‌اید.</p>
                            <?php else: foreach ($bale_channels as $ch): ?>
                                <div class="channel-item">
                                    <div>
                                        <strong><?php echo htmlspecialchars($ch['name']); ?></strong><br>
                                        <code style="font-size:0.75rem; color:var(--text-muted);"><?php echo htmlspecialchars($ch['channel_id']); ?></code>
                                    </div>
                                    <div style="display:flex; gap:0.25rem;">
                                        <a href="?edit_channel=<?php echo $ch['id']; ?>" class="btn btn-sm" style="background:#3b82f6; padding:0.35rem 0.65rem;">⚙</a>
                                        <a href="<?php echo \WHCM\Core\Bootstrap::getRouteUrl('/dashboard/delete-channel'); ?>&id=<?php echo $ch['id']; ?>" class="btn btn-danger btn-sm" style="padding:0.35rem 0.65rem;" onclick="return confirm('آیا از حذف این کانال اطمینان دارید؟');">🗑</a>
                                    </div>
                                </div>
                            <?php endforeach; endif; ?>
                        </div>

                    </div>
                </div>

                <div class="card">
                    <h2>➕ اتصال کانال جدید</h2>
                    <form action="<?php echo \WHCM\Core\Bootstrap::getRouteUrl('/dashboard/add-channel'); ?>" method="POST">
                        <?php echo $csrf_field; ?>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="c-name">نام نمایشی کانال:</label>
                                <input type="text" name="name" id="c-name" required placeholder="مثلا: فروشگاه طلا و سکه آسوین">
                            </div>
                            <div class="form-group">
                                <label for="c-platform">پلتفرم پیام‌رسان:</label>
                                <select name="platform" id="c-platform" required>
                                    <option value="telegram">تلگرام (Telegram)</option>
                                    <option value="bale">بله (Bale)</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="c-channel_id">آیدی کانال (شروع با @ یا آیدی عددی):</label>
                                <input type="text" name="channel_id" id="c-channel_id" required placeholder="@MyGoldShop">
                            </div>
                            <div class="form-group">
                                <label for="c-token">توکن ربات (Bot Token):</label>
                                <input type="password" name="token" id="c-token" required placeholder="توکن دریافتی از BotFather">
                            </div>
                        </div>
                        <button type="submit" class="btn">اتصال ربات و بررسی ارتباط 📡</button>
                    </form>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ۴. بخش ربات طلا و سکه -->
            <!-- ========================================== -->
            <div id="section-ticker" class="tab-content">
                <div class="card">
                    <h2>🪙 ربات خودکار و هوشمند انتشار نرخ لحظه‌ای طلا</h2>
                    <form action="<?php echo \WHCM\Core\Bootstrap::getRouteUrl('/dashboard/save-gold-settings'); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo $csrf_field; ?>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="g-schedule">زمان‌بندی بررسی و انتشار خودکار:</label>
                                <?php $sel_sched = $settings['gold_schedule'] ?? 'manual'; ?>
                                <select name="gold_schedule" id="g-schedule">
                                    <option value="manual" <?php echo $sel_sched === 'manual' ? 'selected' : ''; ?>>غیرفعال (فقط دستی)</option>
                                    <option value="every_5_minutes" <?php echo $sel_sched === 'every_5_minutes' ? 'selected' : ''; ?>>هر ۵ دقیقه</option>
                                    <option value="every_15_minutes" <?php echo $sel_sched === 'every_15_minutes' ? 'selected' : ''; ?>>هر ۱۵ دقیقه</option>
                                    <option value="every_30_minutes" <?php echo $sel_sched === 'every_30_minutes' ? 'selected' : ''; ?>>هر ۳۰ دقیقه</option>
                                    <option value="every_1_hour" <?php echo $sel_sched === 'every_1_hour' ? 'selected' : ''; ?>>هر ۱ ساعت</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="g-currency">واحد پول ورودی API:</label>
                                <?php $sel_curr = $settings['gold_currency'] ?? 'toman'; ?>
                                <select name="gold_currency" id="g-currency">
                                    <option value="toman" <?php echo $sel_curr === 'toman' ? 'selected' : ''; ?>>تومان</option>
                                    <option value="rial" <?php echo $sel_curr === 'rial' ? 'selected' : ''; ?>>ریال (تبدیل خودکار به تومان)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="g-url">آدرس اختصاصی API طلا (اختیاری):</label>
                                <input type="text" name="gold_api_url" id="g-url" value="<?php echo htmlspecialchars($settings['gold_api_url'] ?? ''); ?>" placeholder="https://api.tgju.org/v1/...">
                            </div>
                            <div class="form-group">
                                <label for="g-image">بارگذاری و آپلود تصویر شاخص نرخ طلا:</label>
                                <input type="file" name="gold_image" id="g-image" accept="image/*" style="padding:0.5rem 1rem;">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>انتخاب کانال‌های هدف جهت ارسال خودکار:</label>
                            <?php $saved_channels = json_decode($settings['gold_auto_channels'] ?? '[]', true); ?>
                            <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; background: rgba(15,23,42,0.4); padding: 1rem; border-radius: 12px; border:1px solid var(--border);">
                                <?php if (empty($channels)): ?>
                                    <span style="color:var(--text-muted); font-size:0.85rem;">هنوز کانالی متصل نکرده‌اید.</span>
                                <?php else: ?>
                                    <?php foreach ($channels as $ch): ?>
                                        <label class="toggle-container" style="background: none; border: none; padding: 0;">
                                            <input type="checkbox" name="gold_channels[]" value="<?php echo $ch['id']; ?>" class="toggle-input" <?php echo in_array($ch['id'], $saved_channels) ? 'checked' : ''; ?>>
                                            <span><?php echo htmlspecialchars($ch['name']); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="g-template">قالب پیام نرخ طلا:</label>
                            <?php $tpl = $settings['gold_template'] ?? "🌟 اعلام نرخ لحظه‌ای بازار طلا و سکه\n\nهر گرم طلا ۱۸ عیار: {g18k}\nسکه تمام بهار آزادی: {coin}\nانس جهانی: {oz}\n\n⏰ به‌روزشده در: {time}"; ?>
                            <textarea name="gold_template" id="g-template" rows="6" required><?php echo htmlspecialchars($tpl); ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-success">ذخیره تنظیمات ربات طلا 🪙</button>
                    </form>
                </div>

                <div class="card" style="border: 1px solid rgba(16,185,129,0.2); background: linear-gradient(135deg, rgba(16,185,129,0.05) 0%, rgba(15,23,42,0.6) 100%);">
                    <h2>⚡ انتشار آنی، زنده و تستی نرخ طلا</h2>
                    <form action="<?php echo \WHCM\Core\Bootstrap::getRouteUrl('/dashboard/trigger-gold-publish'); ?>" method="POST">
                        <?php echo $csrf_field; ?>
                        <button type="submit" class="btn btn-success" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">انتشار زنده و آنی به کانال‌ها 🚀</button>
                    </form>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ۵. بخش پاسخگوی خودکار -->
            <!-- ========================================== -->
            <div id="section-responder" class="tab-content">
                <div class="card">
                    <h2>🤖 کلمات کلیدی پاسخگوی خودکار</h2>
                    <form action="<?php echo \WHCM\Core\Bootstrap::getRouteUrl('/dashboard/add-auto-reply'); ?>" method="POST">
                        <?php echo $csrf_field; ?>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="ar-channel">انتخاب ربات کانال:</label>
                                <select name="channel_id" id="ar-channel" required>
                                    <option value="">-- کانال هدف را انتخاب کنید --</option>
                                    <?php foreach ($channels as $ch): ?>
                                        <option value="<?php echo $ch['id']; ?>"><?php echo htmlspecialchars($ch['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="ar-keyword">کلمه کلیدی:</label>
                                <input type="text" name="keyword" id="ar-keyword" required placeholder="مثلاً: طلا">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ar-reply">متن پاسخ خودکار:</label>
                            <textarea name="reply_text" id="ar-reply" rows="4" required placeholder="پاسخ را بنویسید..."></textarea>
                        </div>
                        <button type="submit" class="btn">ثبت کلمه کلیدی پاسخگو 🤖</button>
                    </form>
                </div>

                <div class="card">
                    <h2>📋 کلمات کلیدی تعریف شده</h2>
                    <?php if (empty($auto_replies)): ?>
                        <p style="color:var(--text-muted); text-align: center; padding: 2rem 0;">هیچ قانون پاسخ خودکاری تعریف نشده است.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>کانال</th>
                                        <th>کلمه کلیدی</th>
                                        <th>متن پاسخ</th>
                                        <th>عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($auto_replies as $rule): ?>
                                        <tr>
                                            <td data-label="کانال"><strong><?php echo htmlspecialchars($rule['channel_name']); ?></strong></td>
                                            <td data-label="کلمه کلیدی"><code><?php echo htmlspecialchars($rule['keyword']); ?></code></td>
                                            <td data-label="متن پاسخ"><span style="font-size:0.8rem;"><?php echo htmlspecialchars($rule['reply_text']); ?></span></td>
                                            <td data-label="عملیات">
                                                <a href="<?php echo \WHCM\Core\Bootstrap::getRouteUrl('/dashboard/delete-auto-reply'); ?>&id=<?php echo $rule['id']; ?>" class="btn btn-danger btn-sm">حذف</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ۵.۵. بخش سیستم پشتیبانی و تیکت‌ها -->
            <!-- ========================================== -->
            <div id="section-tickets" class="tab-content">
                <!-- کارت راه‌های ارتباط سریع با پشتیبانی -->
                <div class="card" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.15) 0%, rgba(15, 23, 42, 0.8) 100%); border: 1px solid var(--primary); margin-bottom: 2rem;">
                    <h3 style="color: #818cf8; margin-bottom: 0.75rem;">📞 راه‌های ارتباط سریع با تیم پشتیبانی پُست‌یار</h3>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1.25rem;">شما می‌توانید علاوه بر ارسال تیکت در سامانه، از طریق کانال‌ها و پیام‌رسان‌های زیر به‌صورت مستقیم با مدیریت و کارشناسان پشتیبانی در ارتباط باشید:</p>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <a href="<?php echo htmlspecialchars($global_bank['support_telegram_url'] ?? 'https://t.me/asovin_support'); ?>" target="_blank" class="btn btn-outline" style="border-color: #38bdf8; color: #38bdf8;">✈ تلگرام پشتیبانی</a>
                        <a href="<?php echo htmlspecialchars($global_bank['support_bale_url'] ?? 'https://ble.ir/asovin_support'); ?>" target="_blank" class="btn btn-outline" style="border-color: #34d399; color: #34d399;">💬 بله پشتیبانی</a>
                        <a href="mailto:<?php echo htmlspecialchars($global_bank['support_email'] ?? 'support@asovin.ir'); ?>" class="btn btn-outline" style="border-color: #a855f7; color: #a855f7;">✉ ایمیل پشتیبانی</a>
                    </div>
                </div>

                <div class="card">
                    <h2>🎫 ارسال تیکت پشتیبانی جدید</h2>
                    <form action="<?php echo \WHCM\Core\Bootstrap::getRouteUrl('/dashboard/add-ticket'); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo $csrf_field; ?>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="t-subject">موضوع تیکت:</label>
                                <input type="text" name="subject" id="t-subject" required placeholder="مثلاً: سوال در مورد فعال‌سازی سهمیه">
                            </div>
                            <div class="form-group">
                                <label for="t-cat">دسته‌بندی تیکت:</label>
                                <select name="category" id="t-cat" required>
                                    <option value="technical">فنی و ربات‌ها 🤖</option>
                                    <option value="billing">مالی و فیش واریزی 💳</option>
                                    <option value="general">سوال عمومی 🌐</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="t-msg">متن پیام شما برای پشتیبانی:</label>
                            <textarea name="message" id="t-msg" rows="5" required placeholder="توضیحات کامل مشکل یا سوال خود را اینجا بنویسید..."></textarea>
                        </div>
                        <div class="form-group">
                            <label for="t-file">پیوست تصویر (اختیاری):</label>
                            <input type="file" name="attachment" id="t-file" accept="image/*,.pdf" style="padding:0.5rem 1rem;">
                        </div>
                        <button type="submit" class="btn">ارسال تیکت به تیم پشتیبانی 🎫</button>
                    </form>
                </div>

                <div class="card">
                    <h2>📋 وضعیت تیکت‌های پشتیبانی قبلی شما</h2>
                    <?php if (empty($tickets)): ?>
                        <p style="color: var(--text-muted); text-align: center; padding: 2.5rem 0;">شما هنوز تیکتی ارسال نکرده‌اید.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>شناسه</th>
                                        <th>موضوع تیکت</th>
                                        <th>دسته‌بندی</th>
                                        <th>وضعیت پاسخ</th>
                                        <th>تاریخ ارسال</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tickets as $t): ?>
                                        <tr>
                                            <td data-label="شناسه"><code>#<?php echo \WHCM\Domain\TextFormat::fa_digits($t['id']); ?></code></td>
                                            <td data-label="موضوع تیکت">
                                                <strong style="color:#ffffff;"><?php echo htmlspecialchars($t['subject']); ?></strong><br>
                                                <button type="button" class="btn btn-outline btn-sm" style="margin-top:0.5rem; background:linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important; color:#ffffff !important; font-weight:800; border:none; font-size:0.78rem; padding:0.4rem 0.8rem; box-shadow:0 4px 10px rgba(99,102,241,0.3);" onclick='openTicketModal(<?php echo json_encode($t, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP|JSON_UNESCAPED_UNICODE); ?>)'>👁 مشاهده گفتگو و پاسخ پشتیبانی</button>
                                            </td>
                                            <td data-label="دسته‌بندی">
                                                <span class="badge" style="background:rgba(255,255,255,0.05);">
                                                    <?php echo $t['category'] === 'technical' ? 'فنی' : ($t['category'] === 'billing' ? 'مالی' : 'عمومی'); ?>
                                                </span>
                                            </td>
                                            <td data-label="وضعیت پاسخ">
                                                <?php if ($t['status'] === 'open'): ?>
                                                    <span class="badge badge-pending">در انتظار پاسخ ⏳</span>
                                                <?php elseif ($t['status'] === 'replied'): ?>
                                                    <span class="badge badge-success">پاسخ داده شده ✔</span>
                                                <?php else: ?>
                                                    <span class="badge badge-telegram">بسته شده</span>
                                                <?php endif; ?>
                                            </td>
                                            <td data-label="تاریخ ارسال"><span style="font-size:0.8rem;"><?php echo \WHCM\Domain\TextFormat::mysql_to_jalali($t['created_at']); ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- راه‌های ارتباطی فرعی -->
                <?php
                    // لود راه‌های ارتباطی فرعی پشتیبانی ادمین
                    $stmt = \WHCM\Core\Bootstrap::getDB()->prepare("SELECT key_name, key_value FROM settings WHERE tenant_id = 0 AND key_name IN ('support_telegram_url', 'support_bale_url', 'support_email')");
                    $stmt->execute();
                    $global_support_rows = $stmt->fetchAll();
                    $global_support = [];
                    foreach ($global_support_rows as $row) {
                        $global_support[$row['key_name']] = $row['key_value'];
                    }
                    $saved_tele_url = $global_support['support_telegram_url'] ?? 'https://t.me/asovin_support';
                    $saved_bale_url = $global_support['support_bale_url'] ?? 'https://ble.ir/asovin_support';
                    $saved_support_email = $global_support['support_email'] ?? 'support@asovin.ir';
                ?>
                <div class="card" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(15, 23, 42, 0.6) 100%);">
                    <h2>📞 سایر روش‌های تماس با پشتیبانی</h2>
                    <p style="color:var(--text-muted); font-size:0.85rem; margin-bottom:1.5rem; line-height:1.7;">علاوه بر ارسال تیکت، می‌توانید از راه‌های مستقیم زیر نیز با کارشناسان پُست‌یار در ارتباط باشید:</p>
                    <div style="display:flex; flex-wrap:wrap; gap:1rem;">
                        <a href="<?php echo htmlspecialchars($saved_tele_url); ?>" target="_blank" class="btn btn-outline" style="border-color:#38bdf8; color:#38bdf8;">🌐 پشتیبانی تلگرام</a>
                        <a href="<?php echo htmlspecialchars($saved_bale_url); ?>" target="_blank" class="btn btn-outline" style="border-color:#fbbf24; color:#fbbf24;">💬 پشتیبانی بله</a>
                        <a href="mailto:<?php echo htmlspecialchars($saved_support_email); ?>" class="btn btn-outline">✉ ایمیل مستقیم</a>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ۶. بخش صندوق پیام -->
            <!-- ========================================== -->
            <div id="section-inbox" class="tab-content">
                <div class="card">
                    <h2>📩 صندوق پیام‌های دریافتی</h2>
                    <?php if (empty($inbox)): ?>
                        <p style="color: var(--text-muted); text-align: center; padding: 2rem 0;">پیامی یافت نشد.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>کانال</th>
                                        <th>فرستنده</th>
                                        <th>متن پیام</th>
                                        <th>تاریخ دریافت</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($inbox as $msg): ?>
                                        <tr>
                                            <td data-label="کانال"><span class="badge badge-telegram"><?php echo htmlspecialchars($msg['channel_name']); ?></span></td>
                                            <td data-label="فرستنده"><strong><?php echo htmlspecialchars($msg['sender_name']); ?></strong></td>
                                            <td data-label="پیام"><span style="font-size: 0.85rem;"><?php echo nl2br(htmlspecialchars($msg['message_text'])); ?></span></td>
                                            <td data-label="زمان دریافت"><span style="font-size: 0.8rem; color: var(--text-muted);"><?php echo \WHCM\Domain\TextFormat::mysql_to_jalali($msg['received_at']); ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ۷. بخش تنظیمات حساب کاربری -->
            <!-- ========================================== -->
            <div id="section-settings" class="tab-content">
                <div class="card">
                    <h2>👤 ویرایش پروفایل کاربری</h2>
                    <form action="<?php echo \WHCM\Core\Bootstrap::getRouteUrl('/dashboard/update-profile'); ?>" method="POST">
                        <?php echo $csrf_field; ?>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="u-name">نام و نام خانوادگی:</label>
                                <input type="text" name="name" id="u-name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="u-email">نشانی ایمیل:</label>
                                <input type="email" name="email" id="u-email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">بروزرسانی مشخصات کاربری ✔</button>
                    </form>
                </div>

                <div class="card">
                    <h2>🔑 تغییر کلمه عبور</h2>
                    <form action="<?php echo \WHCM\Core\Bootstrap::getRouteUrl('/dashboard/change-password'); ?>" method="POST">
                        <?php echo $csrf_field; ?>
                        <div class="form-group">
                            <label for="p-curr">کلمه عبور فعلی:</label>
                            <input type="password" name="current_password" id="p-curr" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="p-new">کلمه عبور جدید:</label>
                                <input type="password" name="new_password" id="p-new" required placeholder="حداقل ۸ کاراکتر">
                            </div>
                            <div class="form-group">
                                <label for="p-conf">تکرار کلمه عبور جدید:</label>
                                <input type="password" name="confirm_password" id="p-conf" required placeholder="تکرار مجدد">
                            </div>
                        </div>
                        <button type="submit" class="btn">بروزرسانی کلمه عبور 🔒</button>
                    </form>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ۷.۵. بخش تنظیمات پیشرفته (جداگانه در لیست منو) -->
            <!-- ========================================== -->
            <div id="section-advanced-settings" class="tab-content">
                <?php
                    // لود گروهی تمام تنظیمات پیشرفته مستأجر
                    $stmt = \WHCM\Core\Bootstrap::getDB()->prepare("SELECT key_name, key_value FROM settings WHERE tenant_id = ?");
                    $stmt->execute([$user['id']]);
                    $settings_rows = $stmt->fetchAll();
                    $adv_settings = [];
                    foreach ($settings_rows as $row) {
                        $adv_settings[$row['key_name']] = $row['key_value'];
                    }

                    $woo_active = ($adv_settings['auto_publish_woo'] ?? 'yes') === 'yes';
                    $watermark_active = ($adv_settings['watermark_active'] ?? 'yes') === 'yes';
                    $caption_format = $adv_settings['caption_format'] ?? 'plain';
                    $inbound_method = $adv_settings['inbound_method'] ?? 'polling';
                    $poll_interval = $adv_settings['poll_interval'] ?? 'every_1_minute';
                    
                    $ai_key = $adv_settings['ai_api_key'] ?? '';
                    $ai_model = $adv_settings['ai_model'] ?? 'gpt-4o';
                    $ai_url = $adv_settings['ai_api_url'] ?? 'https://api.openai.com/v1/chat/completions';
                    
                    $link_1_n = $adv_settings['link_1_name'] ?? '📢 کانال تلگرام';
                    $link_1_u = $adv_settings['link_1_url'] ?? '';
                    $link_2_n = $adv_settings['link_2_name'] ?? '💬 کانال بله';
                    $link_2_u = $adv_settings['link_2_url'] ?? '';
                    $link_3_n = $adv_settings['link_3_name'] ?? '🌐 خرید آنلاین از سایت';
                    $link_3_u = $adv_settings['link_3_url'] ?? '';
                    
                    $btn_1_t = $adv_settings['btn_1_text'] ?? '🛒 خرید آنلاین از سایت';
                    $btn_2_t = $adv_settings['btn_2_text'] ?? '💎 پشتیبانی VIP';
                    $btn_2_u = $adv_settings['btn_2_url'] ?? '';
                    $btn_3_t = $adv_settings['btn_3_text'] ?? '📢 هومن وب';
                    $btn_3_u = $adv_settings['btn_3_url'] ?? '';
                ?>
                <div class="card" style="border: 1px solid rgba(139, 92, 246, 0.25); background: linear-gradient(135deg, rgba(139, 92, 246, 0.05) 0%, rgba(15, 23, 42, 0.6) 100%);">
                    <h2>⚙ تنظیمات پیشرفته و اتوماسیون پُست‌یار</h2>
                    <form action="<?php echo \WHCM\Core\Bootstrap::getRouteUrl('/dashboard/save-advanced-settings'); ?>" method="POST">
                        <?php echo $csrf_field; ?>
                        
                        <!-- ۳ لینک سراسری -->
                        <h3 style="font-size: 0.95rem; margin-top: 1rem; margin-bottom: 0.75rem; border-bottom: 1px dashed var(--border); padding-bottom: 0.4rem; color:#a5b4fc;">🔗 پیش‌فرض سراسری ۳ لینک پایین محتوا</h3>
                        <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:1rem; margin-bottom: 1.5rem;">
                            <div class="form-group" style="background: rgba(15,23,42,0.4); padding: 1rem; border-radius: 12px; border: 1px solid var(--border);">
                                <label style="color:#38bdf8;">🔗 لینک ۱ (پیش‌فرض):</label>
                                <input type="text" name="link_1_name" value="<?php echo htmlspecialchars($link_1_n); ?>" style="margin-bottom: 0.5rem;">
                                <input type="url" name="link_1_url" value="<?php echo htmlspecialchars($link_1_u); ?>" placeholder="https://t.me/MyChannel">
                            </div>
                            <div class="form-group" style="background: rgba(15,23,42,0.4); padding: 1rem; border-radius: 12px; border: 1px solid var(--border);">
                                <label style="color:#34d399;">🔗 لینک ۲ (پیش‌فرض):</label>
                                <input type="text" name="link_2_name" value="<?php echo htmlspecialchars($link_2_n); ?>" style="margin-bottom: 0.5rem;">
                                <input type="url" name="link_2_url" value="<?php echo htmlspecialchars($link_2_u); ?>" placeholder="https://ble.ir/MyChannel">
                            </div>
                            <div class="form-group" style="background: rgba(15,23,42,0.4); padding: 1rem; border-radius: 12px; border: 1px solid var(--border);">
                                <label style="color:#a855f7;">🔗 لینک ۳ (پیش‌فرض سایت):</label>
                                <input type="text" name="link_3_name" value="<?php echo htmlspecialchars($link_3_n); ?>" style="margin-bottom: 0.5rem;">
                                <input type="url" name="link_3_url" value="<?php echo htmlspecialchars($link_3_u); ?>" placeholder="https://example.com">
                            </div>
                        </div>

                        <!-- ۳ دکمه تعاملی سراسری -->
                        <h3 style="font-size: 0.95rem; margin-top: 1rem; margin-bottom: 0.75rem; border-bottom: 1px dashed var(--border); padding-bottom: 0.4rem; color:#a5b4fc;">🎛️ پیش‌فرض سراسری دکمه‌های شیشه‌ای تعاملی</h3>
                        <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:1rem; margin-bottom: 1.5rem;">
                            <div class="form-group" style="background: rgba(15,23,42,0.4); padding: 1rem; border-radius: 12px; border: 1px solid var(--border);">
                                <label style="color:#38bdf8;">🎛️ دکمه ۱ (خرید):</label>
                                <input type="text" name="btn_1_text" value="<?php echo htmlspecialchars($btn_1_t); ?>">
                            </div>
                            <div class="form-group" style="background: rgba(15,23,42,0.4); padding: 1rem; border-radius: 12px; border: 1px solid var(--border);">
                                <label style="color:#34d399;">🎛️ دکمه ۲ (پشتیبانی):</label>
                                <input type="text" name="btn_2_text" value="<?php echo htmlspecialchars($btn_2_t); ?>" style="margin-bottom: 0.5rem;">
                                <input type="url" name="btn_2_url" value="<?php echo htmlspecialchars($btn_2_u); ?>" placeholder="https://t.me/MySupport">
                            </div>
                            <div class="form-group" style="background: rgba(15,23,42,0.4); padding: 1rem; border-radius: 12px; border: 1px solid var(--border);">
                                <label style="color:#a855f7;">🎛️ دکمه ۳ (برند):</label>
                                <input type="text" name="btn_3_text" value="<?php echo htmlspecialchars($btn_3_t); ?>" style="margin-bottom: 0.5rem;">
                                <input type="url" name="btn_3_url" value="<?php echo htmlspecialchars($btn_3_u); ?>" placeholder="https://hoomanweb.ir">
                            </div>
                        </div>

                        <!-- تنظیمات و اتوماسیون ووکامرس (گیت شده بر اساس ویژگی پلن) -->
                        <?php if (!empty($quota['features']['woocommerce'])): ?>
                            <h3 style="font-size: 0.95rem; margin-top: 1rem; margin-bottom: 0.75rem; border-bottom: 1px dashed var(--border); padding-bottom: 0.4rem; color:#a5b4fc;">🛍️ اتوماسیون هوشمند فروشگاهی ووکامرس</h3>
                            <div class="form-row" style="margin-bottom: 1rem;">
                                <label class="toggle-container" style="background: rgba(15,23,42,0.4); border: 1px solid var(--border);">
                                    <input type="checkbox" name="auto_publish_woo" value="yes" class="toggle-input" <?php echo $woo_active ? 'checked' : ''; ?>>
                                    <span>انتشار خودکار محصول جدید ووکامرس به همه‌ی کانال‌های فعال</span>
                                </label>
                                <label class="toggle-container" style="background: rgba(15,23,42,0.4); border: 1px solid var(--border);">
                                    <input type="checkbox" name="watermark_active" value="yes" class="toggle-input" <?php echo $watermark_active ? 'checked' : ''; ?>>
                                    <span>درج خودکار واترمرک روی تصاویر محصولات</span>
                                </label>
                            </div>
                        <?php endif; ?>

                        <!-- تنظیمات ارسال و دریافت -->
                        <h3 style="font-size: 0.95rem; margin-top: 1.5rem; margin-bottom: 0.75rem; border-bottom: 1px dashed var(--border); padding-bottom: 0.4rem; color:#a5b4fc;">✉️ تنظیمات ارسال و دریافت پیام</h3>
                        <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:1rem; margin-bottom: 1.5rem;">
                            <div class="form-group">
                                <label>قالب متن ارسالی به کانال‌ها:</label>
                                <select name="caption_format" style="border-radius:12px;">
                                    <option value="plain" <?php echo $caption_format === 'plain' ? 'selected' : ''; ?>>متن ساده + دکمه‌های شیشه‌ای</option>
                                    <option value="html" <?php echo $caption_format === 'html' ? 'selected' : ''; ?>>متن HTML (لینک روی متن)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>روش دریافت پیام‌ها (پاسخ خودکار):</label>
                                <select name="inbound_method" style="border-radius:12px;">
                                    <option value="polling" <?php echo $inbound_method === 'polling' ? 'selected' : ''; ?>>Polling خودکار (getUpdates)</option>
                                    <option value="webhook" <?php echo $inbound_method === 'webhook' ? 'selected' : ''; ?>>وبهوک (Webhook)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>سرعت بررسی پیام‌ها (Polling):</label>
                                <select name="poll_interval" style="border-radius:12px;">
                                    <option value="every_30_seconds" <?php echo $poll_interval === 'every_30_seconds' ? 'selected' : ''; ?>>هر ۳۰ ثانیه (تقریباً بلادرنگ)</option>
                                    <option value="every_1_minute" <?php echo $poll_interval === 'every_1_minute' ? 'selected' : ''; ?>>هر ۱ دقیقه (پیشنهادی)</option>
                                    <option value="every_2_minutes" <?php echo $poll_interval === 'every_2_minutes' ? 'selected' : ''; ?>>هر ۲ دقیقه</option>
                                    <option value="every_5_minutes" <?php echo $poll_interval === 'every_5_minutes' ? 'selected' : ''; ?>>هر ۵ دقیقه</option>
                                </select>
                            </div>
                        </div>

                        <!-- تنظیمات هوش مصنوعی (گیت شده بر اساس ویژگی پلن) -->
                        <?php if (!empty($quota['features']['ai_caption'])): ?>
                            <h3 style="font-size: 0.95rem; margin-top: 1.5rem; margin-bottom: 0.75rem; border-bottom: 1px dashed var(--border); padding-bottom: 0.4rem; color:#a5b4fc;">🤖 تنظیمات هوش مصنوعی مولد کپشن</h3>
                            <div class="form-row" style="margin-bottom: 1rem;">
                                <div class="form-group">
                                    <label>سرویس هوش مصنوعی:</label>
                                    <select id="whcm-ai-provider" onchange="onAiProviderChange(this.value)" style="border-radius:12px;">
                                        <option value="">-- انتخاب سرویس --</option>
                                        <option value="openai">OpenAI (GPT)</option>
                                        <option value="gemini">Google Gemini</option>
                                        <option value="groq">Groq (سریع و رایگان)</option>
                                        <option value="deepseek">DeepSeek</option>
                                        <option value="mistral">Mistral</option>
                                        <option value="together">Together AI</option>
                                        <option value="ollama">Ollama (محلی)</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>مدل هوش مصنوعی:</label>
                                    <select id="ai-model-select" onchange="onAiModelChange(this.value)" style="border-radius:12px;">
                                        <option value="<?php echo htmlspecialchars($ai_model); ?>"><?php echo htmlspecialchars($ai_model); ?></option>
                                    </select>
                                    <input type="hidden" name="ai_model" id="ai-model-hidden" value="<?php echo htmlspecialchars($ai_model); ?>">
                                </div>
                            </div>
                            <div class="form-row" style="margin-bottom: 1rem;">
                                <div class="form-group">
                                    <label>کلید API هوش مصنوعی:</label>
                                    <input type="text" name="ai_api_key" value="<?php echo htmlspecialchars($ai_key); ?>" placeholder="sk-..." class="dir-ltr">
                                </div>
                                <div class="form-group id-custom-group hidden" id="ai-custom-model-group">
                                    <label>نام مدل دلخواه:</label>
                                    <input type="text" id="ai-model-custom-input" value="<?php echo htmlspecialchars($ai_model); ?>" oninput="onAiModelChange('custom')" placeholder="مثال: custom-model-name" class="dir-ltr">
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 2rem;">
                                <label>آدرس اختصاصی API (Completions URL):</label>
                                <input type="url" name="ai_api_url" id="ai-url-input" value="<?php echo htmlspecialchars($ai_url); ?>" placeholder="https://api.openai.com/v1/chat/completions" class="dir-ltr">
                            </div>
                        <?php endif; ?>

                        <button type="submit" class="btn btn-success" style="width:100%; padding:1rem; background: linear-gradient(135deg, var(--primary) 0%, #4f46e5 100%); border:none;">ذخیره تنظیمات پیشرفته و اتوماسیون پُست‌یار 💾✔</button>
                    </form>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ۸. بخش خرید اشتراک -->
            <!-- ========================================== -->
            <div id="section-upgrade" class="tab-content">
                <div class="card">
                    <h2>💎 ارتقا و تمدید اشتراک پنل کاربری</h2>
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">یکی از پلن‌های اشتراکی زیر را انتخاب کنید و رسید تراکنش واریزی خود را ثبت نمایید.</p>
                    
                    <?php
                        $stmt_occ = \WHCM\Core\Bootstrap::getDB()->prepare("SELECT key_value FROM settings WHERE tenant_id = 0 AND key_name = 'occasion_discount_text'");
                        $stmt_occ->execute();
                        $occ_row = $stmt_occ->fetch();
                        $occasion_discount_text = !empty($occ_row['key_value']) ? $occ_row['key_value'] : 'تخفیف مناسبتی';
                    ?>
                    <div class="plans-container">
                        <?php foreach ($plans as $p): ?>
                            <?php 
                                $is_featured = !empty($p['is_featured']);
                                $gen_discount = (int)($p['general_discount'] ?? 0);
                                $early_discount = (int)($p['early_renewal_discount'] ?? 0);
                                
                                // محاسبه قیمت بر اساس تخفیف‌های عمومی و تمدید
                                $base_price = $p['price'];
                                if ($gen_discount > 0) {
                                    $base_price = $p['price'] * (1 - ($gen_discount / 100));
                                }
                                
                                $eligible_early = false;
                                $final_price = $base_price;
                                
                                $subStmt = \WHCM\Core\Bootstrap::getDB()->prepare("SELECT end_date FROM subscriptions WHERE user_id = ? AND status = 'active' ORDER BY id DESC LIMIT 1");
                                $subStmt->execute([$user['id']]);
                                $activeSub = $subStmt->fetch();
                                if ($activeSub && strtotime($activeSub['end_date']) > time() && $early_discount > 0) {
                                    $eligible_early = true;
                                    $final_price = $base_price * (1 - ($early_discount / 100));
                                }
                            ?>
                            <div class="plan-card <?php echo $is_featured ? 'featured-plan' : ($p['price'] > 500000 ? 'recommended' : ''); ?>">
                                <div>
                                    <div class="plan-card-img-wrapper">
                                        <img src="<?php echo \WHCM\Core\Bootstrap::getPlanImageUrl($p['image_url']); ?>" alt="<?php echo htmlspecialchars($p['title']); ?>" class="plan-card-img">
                                        
                                        <!-- برچسب‌ها و متن‌های تخفیف نئونی با افکت نوری روی تصویر -->
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
                                            <?php if ($eligible_early): ?>
                                                <div class="neon-glow-badge neon-badge-cyan">
                                                    ⚡ %<?php echo \WHCM\Domain\TextFormat::fa_digits($early_discount); ?> تخفیف تمدید پیش از موعد!
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <h3><?php echo htmlspecialchars($p['title']); ?></h3>
                                    <?php if (!empty($p['description'])): ?>
                                        <p style="font-size:0.78rem; color:var(--text-muted); margin-bottom:0.75rem; line-height:1.5; text-align:right;">
                                            <?php echo nl2br(htmlspecialchars($p['description'])); ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <div class="price" style="margin-bottom: 1rem; text-align: center;">
                                        <?php if ($gen_discount > 0 || $eligible_early): ?>
                                            <span style="text-decoration: line-through; color: var(--text-muted); font-size: 0.95rem; margin-left: 0.35rem; font-weight: normal;"><?php echo \WHCM\Domain\TextFormat::fa_num($p['price']); ?></span>
                                            <span style="color: #10b981; font-size: 1.2rem; font-weight: 900;"><?php echo \WHCM\Domain\TextFormat::fa_num($final_price); ?> <span style="font-size: 0.8rem; font-weight: normal; color: var(--text-muted);">تومان</span></span>
                                        <?php else: ?>
                                            <span style="font-size: 1.25rem; font-weight: 900; color: #ffffff;"><?php echo \WHCM\Domain\TextFormat::fa_num($p['price']); ?> <span style="font-size: 0.8rem; font-weight: normal; color: var(--text-muted);">تومان</span></span>
                                        <?php endif; ?>
                                    </div>
                                    <ul>
                                        <li>⌛ مدت زمان: <strong><?php echo \WHCM\Domain\TextFormat::fa_digits($p['duration_days']); ?> روز</strong></li>
                                        <li>📻 سهمیه کانال: <strong><?php echo \WHCM\Domain\TextFormat::fa_digits($p['max_channels']); ?> کانال</strong></li>
                                        <li>📝 سهمیه پست: <strong><?php echo $p['max_posts'] === 0 ? 'نامحدود' : \WHCM\Domain\TextFormat::fa_digits($p['max_posts']) . ' پست'; ?></strong></li>
                                        <?php $feats = json_decode($p['features'] ?? '{}', true); ?>
                                        <li>📈 تحلیل آمار تفکیکی: <?php echo !empty($feats['stats']) ? '✅' : '❌'; ?></li>
                                        <li>🪙 ربات خودکار نرخ طلا: <?php echo !empty($feats['gold_ticker']) ? '✅' : '❌'; ?></li>
                                        <li>🤖 پاسخگوی کلمات کلیدی: <?php echo !empty($feats['auto_responder']) ? '✅' : '❌'; ?></li>
                                        <li>🛍 قابلیت اتصال به ووکامرس: <?php echo !empty($feats['woocommerce']) ? '✅' : '❌'; ?></li>
                                        <?php if (!empty($feats['ai_caption'])): ?>
                                            <li>🧠 کپشن‌ساز هوش مصنوعی: ✅</li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                                <button class="btn btn-success" onclick="selectPlan(<?php echo $p['id']; ?>, '<?php echo htmlspecialchars($p['title']); ?>', <?php echo $final_price; ?>, '<?php echo htmlspecialchars($p['payment_url'] ?? ''); ?>')" style="width: 100%; border-radius: 12px; padding: 0.65rem; font-size: 0.85rem; font-weight: 850; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">انتخاب این پلن</button>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- بخش پرداخت کارت به کارت فین‌تک هومن نقشی -->
                    <?php
                        $stmt = \WHCM\Core\Bootstrap::getDB()->prepare("SELECT key_name, key_value FROM settings WHERE tenant_id = 0 AND key_name IN ('admin_card_number', 'admin_card_holder', 'admin_bank_name')");
                        $stmt->execute();
                        $global_bank_rows = $stmt->fetchAll();
                        $global_bank = [];
                        foreach ($global_bank_rows as $row) {
                            $global_bank[$row['key_name']] = $row['key_value'];
                        }
                        $saved_card = $global_bank['admin_card_number'] ?? '۶۲۱۹-۸۶۱۰-xxxx-xxxx';
                        $saved_holder = $global_bank['admin_card_holder'] ?? 'هومن نقشی';
                        $saved_bank = $global_bank['admin_bank_name'] ?? 'بانک سامان';
                    ?>
                    <div id="payment-box" class="payment-box hidden">
                        <h4 style="margin-bottom: 1rem; color: #ffffff;">💳 جزئیات پرداخت پلن انتخابی <strong id="sel-title" style="color:#a5b4fc;">...</strong></h4>
                        
                        <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 2rem; align-items: center;">
                            <div>
                                <p style="font-size:0.85rem; color: var(--text-muted); margin-bottom:0.75rem;">برای کپی سریع شماره کارت، روی کارت بانکی زیر ضربه بزنید:</p>
                                <div class="credit-card" onclick="copyCardNumber()">
                                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                                        <span style="font-size:0.85rem; font-weight:bold; letter-spacing:1px; color:#cbd5e1;"><?php echo htmlspecialchars($saved_bank); ?></span>
                                        <span style="font-size:1.1rem;">💳</span>
                                    </div>
                                    <div class="credit-card-chip"></div>
                                    <div class="credit-card-number" id="card-num-text"><?php echo htmlspecialchars($saved_card); ?></div>
                                    <div class="credit-card-holder">
                                        <span>صاحب حساب: <?php echo htmlspecialchars($saved_holder); ?></span>
                                        <span><?php echo htmlspecialchars($saved_bank); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- نمایش دکمه پرداخت مستقیم بلو لینک اختصاصی هر پلن -->
                            <div id="online-pay-div" class="hidden" style="text-align: center; border-right: 1px dashed var(--border); padding-right: 1.5rem;">
                                <p style="font-size:0.85rem; color: var(--text-muted); margin-bottom:1rem;">یا می‌توانید مستقیماً به صورت آنلاین از طریق بلو لینک زیر پرداخت را انجام دهید:</p>
                                <a href="#" id="online-pay-link" target="_blank" class="btn btn-success" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border: none; padding: 1rem; width: 100%; font-size:0.95rem;">
                                    💳 پرداخت آنلاین با بلو لینک ⚡
                                </a>
                            </div>
                        </div>

                        <form action="<?php echo \WHCM\Core\Bootstrap::getRouteUrl('/dashboard/submit-payment'); ?>" method="POST" enctype="multipart/form-data" style="max-width: 480px; margin-top: 1.5rem;">
                            <?php echo $csrf_field; ?>
                            <input type="hidden" name="plan_id" id="form-plan-id">
                            <input type="hidden" name="amount" id="form-amount">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="ref_num">کد رهگیری / شماره ارجاع تراکنش:</label>
                                    <input type="text" name="reference_num" id="ref_num" required placeholder="مثلاً: ۷۴۵۸۹۶۲۱۰">
                                </div>
                                <div class="form-group">
                                    <label for="rec-photo">بارگذاری تصویر رسید پرداخت (وب‌پی خودکار):</label>
                                    <input type="file" name="receipt_photo" id="rec-photo" accept="image/*" required style="padding: 0.5rem 1rem;">
                                </div>
                            </div>

                            <!-- دکمه اتصال مستقیم به برنامه همراه بانک بلو جهت کارت به کارت فوری -->
                            <div style="margin-bottom: 1.5rem; text-align: center;">
                                <a href="blubank://transfer" target="_blank" class="btn btn-outline" style="width: 100%; border-color: #3b82f6; color: #3b82f6; border-radius: 12px; font-weight: bold; font-size: 0.85rem;">
                                    🚀 کارت به کارت فوری در اپلیکیشن بلو بانک (مخصوص گوشی)
                                </a>
                            </div>

                            <button type="submit" class="btn btn-block" style="width:100%;">ثبت نهایی رسید و واریز 💳</button>
                        </form>
                    </div>
                </div>

        
</main>
    </div>

    <script>
        function copyCardNumber() {
            var cardNumber = "<?php echo htmlspecialchars($saved_card); ?>"; // شماره کارت دریافتی پویا از مدیر ارشد
            navigator.clipboard.writeText(cardNumber).then(function() {
                var toast = document.getElementById('copy-toast');
                toast.classList.add('show');
                setTimeout(function() {
                    toast.classList.remove('show');
                }, 3000);
            });
        }

        function toggleEmojiPicker() {
            var picker = document.getElementById('emoji-popup');
            picker.style.display = (picker.style.display === 'flex') ? 'none' : 'flex';
        }

        function switchEmojiTab(tabName) {
            // غیرفعال کردن تمامی تب‌ها و گریدها به صورت ES5 ایمن
            var tabs = document.querySelectorAll('.emoji-tab');
            for (var i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove('active');
            }
            var grids = document.querySelectorAll('.emoji-grid');
            for (var j = 0; j < grids.length; j++) {
                grids[j].classList.add('hidden');
            }

            // فعال‌سازی تب و گرید مربوطه
            event.target.classList.add('active');
            document.getElementById('emoji-grid-' + tabName).classList.remove('hidden');
        }

        function insertEmoji(emoji) {
            var textarea = document.getElementById('p-content');
            var start = textarea.selectionStart;
            var end = textarea.selectionEnd;
            var text = textarea.value;
            textarea.value = text.substring(0, start) + emoji + text.substring(end);
            textarea.focus();
            textarea.selectionStart = textarea.selectionEnd = start + emoji.length;
            document.getElementById('emoji-popup').style.display = 'none';
        }

        function toggleScheduleInput(val) {
            var group = document.getElementById('schedule-datetime-group');
            if (val === 'scheduled') {
                group.classList.remove('hidden');
            } else {
                group.classList.add('hidden');
            }
        }

        function closeBroadcastBanner() {
            document.getElementById('broadcast-alert-banner').style.display = 'none';
        }

        // کنترلر ذخیره‌سازی ایمن جهت ممانعت از کرش شدن اسکریپت در مرورگرهای قدیمی و پرایوت
        var SafeStorage = {
            getItem: function(key, defaultValue) {
                try {
                    return sessionStorage.getItem(key) || defaultValue;
                } catch (e) {
                    return defaultValue;
                }
            },
            setItem: function(key, value) {
                try {
                    sessionStorage.setItem(key, value);
                } catch (e) {
                    // نادیده گرفتن خطای پرایوت مرورگر
                }
            }
        };

        function switchSection(sectionId) {
            window.scrollTo({ top: 0, behavior: 'smooth' });
            // ۱. پنهان کردن تمام بخش‌ها
            var sections = document.querySelectorAll('.tab-content');
            for (var i = 0; i < sections.length; i++) {
                sections[i].classList.remove('active');
            }

            // ۲. نمایش بخش هدف
            var targetSec = document.getElementById('section-' + sectionId);
            if (targetSec) {
                targetSec.classList.add('active');
            }

            // ۳. غیرفعال کردن کلاس‌های فعال منوها
            var menuItems = document.querySelectorAll('.menu-item, .mobile-nav-item');
            for (var j = 0; j < menuItems.length; j++) {
                menuItems[j].classList.remove('active');
            }

            // ۴. فعال کردن خودکار منوهای متناظر (هم موبایل و هم دسکتاپ)
            var targets = document.querySelectorAll('.menu-item[data-target="' + sectionId + '"], .mobile-nav-item[data-target="' + sectionId + '"]');
            for (var k = 0; k < targets.length; k++) {
                targets[k].classList.add('active');
            }

            // ۵. ذخیره‌سازی وضعیت تب جاری به روش ایمن
            SafeStorage.setItem('last_tab', sectionId);
        }

        var AI_PROVIDERS = {
            'openai': {
                'url': 'https://api.openai.com/v1/chat/completions',
                'models': ['gpt-4o-mini', 'gpt-4o', 'gpt-3.5-turbo']
            },
            'gemini': {
                'url': 'https://generativelanguage.googleapis.com/v1beta/openai/chat/completions',
                'models': ['gemini-2.0-flash', 'gemini-1.5-flash', 'gemini-1.5-pro']
            },
            'groq': {
                'url': 'https://api.groq.com/openai/v1/chat/completions',
                'models': ['llama-3.3-70b-versatile', 'llama-3.1-8b-instant', 'llama3-70b-8192']
            },
            'deepseek': {
                'url': 'https://api.deepseek.com/chat/completions',
                'models': ['deepseek-chat', 'deepseek-reasoner']
            },
            'mistral': {
                'url': 'https://api.mistral.ai/v1/chat/completions',
                'models': ['mistral-large-latest', 'open-mistral-nemo']
            },
            'together': {
                'url': 'https://api.together.xyz/v1/chat/completions',
                'models': ['meta-llama/Llama-3.3-70B-Instruct-Turbo', 'Qwen/Qwen2.5-72B-Instruct-Turbo']
            },
            'ollama': {
                'url': 'http://localhost:11434/v1/chat/completions',
                'models': ['llama3.2', 'qwen2.5', 'mistral']
            }
        };

        function onAiProviderChange(providerKey) {
            var provider = AI_PROVIDERS[providerKey];
            var urlInput = document.getElementById('ai-url-input');
            var modelSelect = document.getElementById('ai-model-select');
            
            if (provider) {
                if (urlInput) urlInput.value = provider.url;
                
                if (modelSelect) {
                    modelSelect.innerHTML = '';
                    for (var i = 0; i < provider.models.length; i++) {
                        var opt = document.createElement('option');
                        opt.value = provider.models[i];
                        opt.textContent = provider.models[i];
                        modelSelect.appendChild(opt);
                    }
                    var customOpt = document.createElement('option');
                    customOpt.value = 'custom';
                    customOpt.textContent = '-- مدل دلخواه --';
                    modelSelect.appendChild(customOpt);
                    
                    onAiModelChange(modelSelect.value);
                }
            }
        }

        function onAiModelChange(modelVal) {
            var customGroup = document.getElementById('ai-custom-model-group');
            var customInput = document.getElementById('ai-model-custom-input');
            var hiddenInput = document.getElementById('ai-model-hidden');
            
            if (modelVal === 'custom') {
                if (customGroup) customGroup.classList.remove('hidden');
                if (hiddenInput && customInput) hiddenInput.value = customInput.value;
            } else {
                if (customGroup) customGroup.classList.add('hidden');
                if (hiddenInput) hiddenInput.value = modelVal;
            }
        }

        function initDashboard() {
            // ۱. اتصال کلیک به تک‌تک منوهای پنل کاربری (دسکتاپ و موبایل)
            var clickableItems = document.querySelectorAll('.menu-item, .mobile-nav-item');
            for (var i = 0; i < clickableItems.length; i++) {
                var item = clickableItems[i];
                var target = item.getAttribute('data-target');
                if (target) {
                    item.addEventListener('click', function(e) {
                        var clickedItem = e.currentTarget;
                        var sectionId = clickedItem.getAttribute('data-target');
                        switchSection(sectionId);
                    });
                }
            }

            // ۲. لود تب پیش‌فرض یا آخرین تب ذخیره شده
            var query = window.location.search || '';
            if (query.indexOf('edit_channel') !== -1) {
                switchSection('channels');
                return;
            }

            var lastTab = SafeStorage.getItem('last_tab', 'dashboard');
            switchSection(lastTab);
        }

        // بررسی لود بودن سند جهت اجرای آنی یا تعویقی اسکریپت (تضمین کارکرد تحت هر شرایطی!)
        if (document.readyState !== 'loading') {
            initDashboard();
        } else {
            window.addEventListener('DOMContentLoaded', initDashboard);
        }

        // بستن پاپ‌آپ اموجی با کلیک در خارج از کادر به صورت ES5 ایمن
        window.addEventListener('click', function(event) {
            var popup = document.getElementById('emoji-popup');
            var btn = document.querySelector('.emoji-picker-btn');
            if (popup && event.target !== popup && !popup.contains(event.target) && event.target !== btn) {
                popup.style.display = 'none';
            }
        });

        function selectPlan(id, title, price, paymentUrl) {
            document.getElementById('payment-box').classList.remove('hidden');
            document.getElementById('sel-title').textContent = title;
            document.getElementById('sel-price').textContent = price.toLocaleString('fa-IR');
            
            document.getElementById('form-plan-id').value = id;
            document.getElementById('form-amount').value = price;

            var onlinePayDiv = document.getElementById('online-pay-div');
            var onlinePayLink = document.getElementById('online-pay-link');
            
            if (paymentUrl && paymentUrl.trim() !== '') {
                onlinePayDiv.classList.remove('hidden');
                onlinePayLink.href = paymentUrl;
            } else {
                onlinePayDiv.classList.add('hidden');
                onlinePayLink.href = "#";
            }
            
            document.getElementById('payment-box').scrollIntoView({ behavior: 'smooth' });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/jalalidatepicker@latest/dist/jalalidatepicker.min.js"></script>
    <script>
        // راه‌اندازی تقویم شمسی تصویری لوکس
        if (typeof jalaliDatepicker !== 'undefined') {
            try {
                jalaliDatepicker.startWatch({
                    minDate: "today",
                    showTodayBtn: true,
                    showEmptyBtn: false
                });
            } catch (e) {}
        }
    </script>

    <!-- مدال گفتگو و مدیریت حرفه‌ای تیکت توسط مستأجر -->
    <div id="ticketModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85); z-index:1200; align-items:center; justify-content:center; padding:1rem; overflow-y:auto;">
        <div class="card" style="width:100%; max-width:580px; margin:auto; position:relative; background:#0f172a; border:1px solid #4f46e5; border-radius:16px; box-shadow:0 20px 50px rgba(0,0,0,0.8);">
            <button onclick="closeTicketModal()" style="position:absolute; top:15px; left:15px; background:none; border:none; color:#94a3b8; font-size:1.4rem; cursor:pointer;">✖</button>
            
            <div style="border-bottom:1px dashed #334155; padding-bottom:1rem; margin-bottom:1.25rem;">
                <span id="t-modal-status" class="badge" style="float:left; margin-top:2px;"></span>
                <h3 id="t-modal-subject" style="color:white; margin:0; font-size:1.15rem; font-weight:900;"></h3>
            </div>

            <div id="t-modal-body" style="display:flex; flex-direction:column; gap:1rem; max-height:350px; overflow-y:auto; padding-right:0.5rem; margin-bottom:1.5rem;">
                <!-- گفتگوهای تیکت در اینجا به صورت چت درج می‌شوند -->
            </div>

            <!-- فرم ارسال پاسخ به تیکت توسط کاربر -->
            <form action="<?php echo \WHCM\Core\Bootstrap::getRouteUrl('/dashboard/reply-ticket'); ?>" method="POST" enctype="multipart/form-data" style="margin-bottom:1rem;">
                <?php echo $csrf_field; ?>
                <input type="hidden" name="ticket_id" id="t-reply-id">
                <div class="form-group" style="margin-bottom:0.75rem;">
                    <textarea name="reply" rows="3" required placeholder="پاسخ یا توضیحات تکمیلی خود را بنویسید..." style="width:100%; border-radius:10px; background:#1e293b; color:white; border:1px solid #334155; padding:0.75rem;"></textarea>
                </div>
                <div class="form-group">
                    <label style="font-size:0.8rem; color:#94a3b8;">پیوست تصویر (اختیاری):</label>
                    <input type="file" name="attachment" accept="image/*,.pdf" style="padding:0.4rem; font-size:0.8rem;">
                </div>
                <label style="display:flex; align-items:center; gap:0.5rem; font-size:0.85rem; color:#fbbf24; margin-bottom:0.75rem; cursor:pointer;"><input type="checkbox" name="close_after_reply" value="1"> ارسال و بستن همزمان تیکت</label>
                <button type="submit" class="btn btn-success" style="width:100%; padding:0.75rem;">ارسال پاسخ جدید به پشتیبانی ✔</button>
            </form>

            <!-- دکمه بستن تیکت توسط کاربر -->
            <form action="<?php echo \WHCM\Core\Bootstrap::getRouteUrl('/dashboard/close-ticket'); ?>" method="POST" style="margin:0;">
                <?php echo $csrf_field; ?>
                <input type="hidden" name="ticket_id" id="t-close-id">
                <button type="submit" class="btn btn-danger" style="width:100%; padding:0.6rem; font-size:0.85rem; background:rgba(239, 68, 68, 0.2); border:1px solid #ef4444; color:#ef4444;">بستن این تیکت (مختومه کردن)</button>
            </form>
        </div>
    </div>
    <script>
        function openTicketModal(t) {
            document.getElementById('t-modal-subject').textContent = t.subject || "تیکت پشتیبانی";
            document.getElementById('t-reply-id').value = t.id;
            document.getElementById('t-close-id').value = t.id;
            
            var statusSpan = document.getElementById('t-modal-status');
            if (t.status === 'open') {
                statusSpan.className = "badge badge-pending";
                statusSpan.textContent = "در انتظار پاسخ ⏳";
            } else if (t.status === 'replied') {
                statusSpan.className = "badge badge-success";
                statusSpan.textContent = "پاسخ داده شده ✔";
            } else {
                statusSpan.className = "badge badge-telegram";
                statusSpan.textContent = "بسته شده";
            }

            var bodyDiv = document.getElementById('t-modal-body');
            bodyDiv.innerHTML = "";

            var rawText = t.message || "";
            var parts = rawText.split("➖➖➖➖➖➖➖➖➖➖");

            for (var i = 0; i < parts.length; i++) {
                var text = parts[i].trim();
                if (!text) continue;

                var bubble = document.createElement('div');
                bubble.style.padding = "1rem";
                bubble.style.borderRadius = "12px";
                bubble.style.lineHeight = "1.8";
                bubble.style.fontSize = "0.9rem";
                
                if (i === 0) {
                    bubble.style.background = "#1e293b";
                    bubble.style.border = "1px solid #334155";
                    bubble.style.color = "#e2e8f0";
                    bubble.innerHTML = '<div style="font-size:0.75rem; color:#818cf8; font-weight:bold; margin-bottom:0.4rem;">👤 پیام شما:</div>' + text.replace(/\n/g, "<br>");
                } else {
                    bubble.style.background = "linear-gradient(135deg, rgba(99, 102, 241, 0.15) 0%, rgba(15, 23, 42, 0.9) 100%)";
                    bubble.style.border = "1px solid #6366f1";
                    bubble.style.color = "#ffffff";
                    bubble.innerHTML = '<div style="font-size:0.8rem; color:#34d399; font-weight:900; margin-bottom:0.4rem;">👑 پاسخ کارشناس پشتیبانی پُست‌یار:</div>' + text.replace(/\n/g, "<br>");
                }
                bodyDiv.appendChild(bubble);
            }

            document.getElementById('ticketModal').style.display = 'flex';
        }

        function closeTicketModal() {
            document.getElementById('ticketModal').style.display = 'none';
        }
    </script>
</body>
</html>