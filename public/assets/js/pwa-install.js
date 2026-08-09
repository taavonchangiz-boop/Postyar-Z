/* ==========================================================================
   PWA Install Handler — فقط موبایل و تبلت (نه دسکتاپ)
   سامانه مدیریت کانال‌ها و انتشار خودکار پُست‌یار
   ========================================================================== */

(function() {
    'use strict';

    // ===== تشخیص موبایل/تبلت — دسکتاپ هرگز نمی‌بیند =====
    function isMobileOrTablet() {
        if (typeof window === 'undefined' || typeof navigator === 'undefined') return false;

        var ua = navigator.userAgent || navigator.vendor || '';
        var hasTouchPoints = navigator.maxTouchPoints > 1;

        // تشخیص UA (پوشش کامل اندروید، آیفون، آیپد، تبلت‌ها)
        var mobileRegex = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|Mobile|Tablet/i;
        var isMobileUA = mobileRegex.test(ua);

        // تشخیص آیپد جدید (iPadOS 13+ خودش را Mac معرفی می‌کند)
        var isMacWithTouch = /Macintosh/.test(ua) && hasTouchPoints;

        return isMobileUA || isMacWithTouch || hasTouchPoints;
    }

    // اگر دسکتاپ است، هیچ کاری نکن — خروج فوری
    if (!isMobileOrTablet()) {
        return;
    }

    var deferredPrompt = null;
    var installBanner = null;
    var installBtn = null;
    var dismissBtn = null;
    var isIOS = /iphone|ipad|ipod/i.test((navigator.userAgent || ''));
    var isStandalone = window.navigator.standalone === true || window.matchMedia('(display-mode: standalone)').matches;

    // ===== ساخت بنر نصب (فقط یکبار) =====
    function createInstallBanner() {
        if (installBanner) return installBanner;

        var banner = document.createElement('div');
        banner.id = 'pwa-install-banner';
        banner.setAttribute('role', 'dialog');
        banner.setAttribute('aria-label', 'نصب اپلیکیشن');

        if (isIOS) {
            banner.innerHTML =
                '<div style="display:flex;align-items:center;gap:12px;padding:14px 16px;">' +
                    '<img src="/assets/icons/icon-192x192.png" alt="پُست‌یار" width="48" height="48" style="border-radius:12px;flex-shrink:0;">' +
                    '<div style="flex:1;min-width:0;">' +
                        '<div style="font-weight:700;color:#f1f5f9;font-size:14px;margin-bottom:2px;">پُست‌یار را نصب کنید</div>' +
                        '<div style="color:#94a3b8;font-size:12px;line-height:1.5;">روی دکمه <b style="color:#6366f1;">اشتراک‌گذاری</b> بزنید و <b style="color:#6366f1;">افزودن به صفحه اصلی</b> را بزنید</div>' +
                    '</div>' +
                    '<button id="pwa-dismiss" aria-label="بستن" style="background:none;border:none;color:#64748b;font-size:20px;cursor:pointer;padding:8px;flex-shrink:0;">&#10005;</button>' +
                '</div>';
        } else {
            banner.innerHTML =
                '<div style="display:flex;align-items:center;gap:12px;padding:14px 16px;">' +
                    '<img src="/assets/icons/icon-192x192.png" alt="پُست‌یار" width="48" height="48" style="border-radius:12px;flex-shrink:0;">' +
                    '<div style="flex:1;min-width:0;">' +
                        '<div style="font-weight:700;color:#f1f5f9;font-size:14px;margin-bottom:2px;">پُست‌یار را نصب کنید</div>' +
                        '<div style="color:#94a3b8;font-size:12px;">دسترسی سریع مثل اپلیکیشن واقعی</div>' +
                    '</div>' +
                    '<button id="pwa-install-btn" style="background:#6366f1;color:#fff;border:none;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;flex-shrink:0;font-family:inherit;">نصب</button>' +
                    '<button id="pwa-dismiss" aria-label="بستن" style="background:none;border:none;color:#64748b;font-size:20px;cursor:pointer;padding:8px;flex-shrink:0;">&#10005;</button>' +
                '</div>';
        }

        // استایل بنر
        banner.style.cssText =
            'position:fixed;bottom:0;left:0;right:0;z-index:99999;' +
            'background:linear-gradient(135deg,#1e293b 0%,#0f172a 100%);' +
            'border-top:1px solid rgba(99,102,241,0.3);' +
            'box-shadow:0 -4px 24px rgba(0,0,0,0.4);' +
            'font-family:Vazirmatn,Tahoma,Arial,sans-serif;' +
            'direction:rtl;' +
            'transform:translateY(100%);' +
            'transition:transform 0.4s cubic-bezier(0.16,1,0.3,1);';

        document.body.appendChild(banner);
        return banner;
    }

    // ===== نمایش بنر =====
    function showBanner() {
        // اگر قبلاً رد شده یا قبلاً نصب شده، نمایش نده
        if (localStorage.getItem('pwa_install_dismissed') === 'permanent') return;
        if (isStandalone) return;

        installBanner = createInstallBanner();
        dismissBtn = document.getElementById('pwa-dismiss');

        // انیمیشن نمایش
        requestAnimationFrame(function() {
            installBanner.style.transform = 'translateY(0)';
        });

        // دکمه بستن
        if (dismissBtn) {
            dismissBtn.addEventListener('click', function() {
                hideBanner();
                localStorage.setItem('pwa_install_dismissed', 'permanent');
            });
        }

        // دکمه نصب (اندروید)
        if (!isIOS) {
            installBtn = document.getElementById('pwa-install-btn');
            if (installBtn && deferredPrompt) {
                installBtn.addEventListener('click', async function() {
                    installBtn.disabled = true;
                    installBtn.textContent = 'در حال نصب...';
                    try {
                        deferredPrompt.prompt();
                        var outcome = await deferredPrompt.userChoice;
                        if (outcome.outcome === 'accepted') {
                            localStorage.setItem('pwa_install_dismissed', 'permanent');
                        }
                        deferredPrompt = null;
                    } catch(e) {}
                    hideBanner();
                });
            }
        }
    }

    // ===== مخفی کردن بنر =====
    function hideBanner() {
        if (installBanner) {
            installBanner.style.transform = 'translateY(100%)';
            setTimeout(function() {
                if (installBanner && installBanner.parentNode) {
                    installBanner.parentNode.removeChild(installBanner);
                    installBanner = null;
                }
            }, 400);
        }
    }

    // ===== شروع =====
    if (isStandalone) return; // اگر از قبل نصب شده، بنر نمایش نده

    if (isIOS) {
        // iOS: نمایش بنر آموزشی بعد از ۳ ثانیه
        setTimeout(function() {
            showBanner();
        }, 3000);
    } else {
        // اندروید: منتظر رویداد beforeinstallprompt
        window.addEventListener('beforeinstallprompt', function(e) {
            e.preventDefault();
            deferredPrompt = e;
            setTimeout(function() {
                showBanner();
            }, 2000);
        });
    }

})();