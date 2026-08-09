/* ==========================================================================
   فایل جاوااسکریپت داشبورد کاربری (Dashboard JavaScript)
   سامانه مدیریت کانال‌ها و انتشار خودکار پُست‌یار
   ========================================================================== */

/* ===== کپی شماره کارت ===== */
function copyCardNumber() {
    var cardNumber = (window.__dashboardSavedCard || '');
    navigator.clipboard.writeText(cardNumber).then(function() {
        var toast = document.getElementById('copy-toast');
        toast.classList.add('show');
        setTimeout(function() {
            toast.classList.remove('show');
        }, 3000);
    });
}

/* ===== پیکر اموجی و استیکر ===== */
function toggleEmojiPicker() {
    var picker = document.getElementById('emoji-popup');
    picker.style.display = (picker.style.display === 'flex') ? 'none' : 'flex';
}

function switchEmojiTab(tabName) {
    var tabs = document.querySelectorAll('.emoji-tab');
    for (var i = 0; i < tabs.length; i++) {
        tabs[i].classList.remove('active');
    }
    var grids = document.querySelectorAll('.emoji-grid');
    for (var j = 0; j < grids.length; j++) {
        grids[j].classList.add('hidden');
    }
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

/* ===== نمایش/پنهان فرم زمان‌بندی ===== */
function toggleScheduleInput(val) {
    var group = document.getElementById('schedule-datetime-group');
    if (val === 'scheduled') {
        group.classList.remove('hidden');
    } else {
        group.classList.add('hidden');
    }
}

/* ===== بستن اعلان همگانی ===== */
function closeBroadcastBanner() {
    document.getElementById('broadcast-alert-banner').style.display = 'none';
}

/* ===== تب‌بندی بخش‌های داشبورد ===== */
function switchSection(sectionId) {
    window.scrollTo({ top: 0, behavior: 'smooth' });
    var sections = document.querySelectorAll('.tab-content');
    for (var i = 0; i < sections.length; i++) {
        sections[i].classList.remove('active');
    }
    var targetSec = document.getElementById('section-' + sectionId);
    if (targetSec) {
        targetSec.classList.add('active');
    }
    var menuItems = document.querySelectorAll('.menu-item, .mobile-nav-item');
    for (var j = 0; j < menuItems.length; j++) {
        menuItems[j].classList.remove('active');
    }
    var targets = document.querySelectorAll('.menu-item[data-target="' + sectionId + '"], .mobile-nav-item[data-target="' + sectionId + '"]');
    for (var k = 0; k < targets.length; k++) {
        targets[k].classList.add('active');
    }
    SafeStorage.setItem('last_tab', sectionId);
}

/* ===== تنظیمات هوش مصنوعی (AI Provider/Model) ===== */
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

/* ===== راه‌اندازی اولیه داشبورد ===== */
function initDashboard() {
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
    var query = window.location.search || '';
    if (query.indexOf('edit_channel') !== -1) {
        switchSection('channels');
        return;
    }
    var lastTab = SafeStorage.getItem('last_tab', 'dashboard');
    switchSection(lastTab);
}

if (document.readyState !== 'loading') {
    initDashboard();
} else {
    window.addEventListener('DOMContentLoaded', initDashboard);
}

/* بستن پاپ‌آپ اموجی با کلیک در خارج از کادر */
window.addEventListener('click', function(event) {
    var popup = document.getElementById('emoji-popup');
    var btn = document.querySelector('.emoji-picker-btn');
    if (popup && event.target !== popup && !popup.contains(event.target) && event.target !== btn) {
        popup.style.display = 'none';
    }
});

/* ===== انتخاب پلن و نمایش فرم پرداخت ===== */
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

/* ===== مدال گفتگو و مدیریت تیکت ===== */
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
