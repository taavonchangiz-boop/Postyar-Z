-- جدول کاربران (شامل مدیران کل و مستاجرین)
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user', -- 'superadmin' یا 'user'
    status VARCHAR(20) DEFAULT 'active', -- 'active'، 'suspended'، 'inactive'
    business_name VARCHAR(150) NULL, -- نام کسب و کار
    business_type VARCHAR(150) NULL, -- نوع کسب و کار
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- جدول پلن‌های اشتراک (مدیریت توسط مدیر کل)
CREATE TABLE IF NOT EXISTS plans (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(100) NOT NULL,
    price DECIMAL(12,2) DEFAULT 0.00,
    duration_days INTEGER DEFAULT 30, -- مدت اعتبار به روز
    max_channels INTEGER DEFAULT 1, -- حداکثر کانال متصل
    max_posts INTEGER DEFAULT 10, -- حداکثر پست ارسالی در ماه (۰ = نامحدود)
    features TEXT, -- قالب JSON ویژگی‌ها (مانند طلا، پاسخگو، ووکامرس و غیره)
    payment_url TEXT, -- لینک پرداخت اختصاصی هر پلن
    image_url TEXT, -- آدرس تصویر شاخص پلن اشتراکی
    description TEXT, -- توضیحات اختصاصی هر پلن
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- جدول اشتراک‌های فعال کاربران
CREATE TABLE IF NOT EXISTS subscriptions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    plan_id INTEGER NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    status VARCHAR(20) DEFAULT 'active', -- 'active'، 'expired'، 'cancelled'
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE CASCADE
);

-- جدول ثبت جهانی کانال‌ها (ضد تقلب - قفل همیشگی آیدی کانال به اولین ثبت‌کننده)
CREATE TABLE IF NOT EXISTS channel_registry (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    channel_id VARCHAR(150) NOT NULL, -- مثلاً MyGoldShop@ یا chat_id عددی
    platform VARCHAR(20) NOT NULL, -- 'telegram' یا 'bale'
    owner_user_id INTEGER NOT NULL, -- اولین مالک
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(platform, channel_id),
    FOREIGN KEY (owner_user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- جدول کانال‌های اختصاصی هر مستاجر (Tenant Channels)
CREATE TABLE IF NOT EXISTS channels (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    tenant_id INTEGER NOT NULL, -- شناسه‌ کاربری مستاجر
    name VARCHAR(150) NOT NULL,
    platform VARCHAR(20) NOT NULL, -- 'telegram' یا 'bale'
    channel_id VARCHAR(150) NOT NULL,
    token TEXT NOT NULL,
    link_config TEXT, -- قالب JSON لینک‌های سه‌گانه
    button_config TEXT, -- قالب JSON دکمه‌های شیشه‌ای تعاملی زیرین
    webhook_active INTEGER DEFAULT 0,
    webhook_secret VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES users(id) ON DELETE CASCADE
);

-- جدول پست‌های تولیدشده توسط کاربران
CREATE TABLE IF NOT EXISTS posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    tenant_id INTEGER NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    media_url TEXT,
    status VARCHAR(20) DEFAULT 'draft', -- 'draft'، 'scheduled'، 'sent'، 'failed'
    scheduled_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES users(id) ON DELETE CASCADE
);

-- جدول پیگیری پیام‌های ارسال‌شده به کانال‌ها جهت ویرایش زنده
CREATE TABLE IF NOT EXISTS channel_messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    post_id INTEGER NOT NULL,
    channel_id INTEGER NOT NULL,
    message_id VARCHAR(100) NOT NULL,
    status VARCHAR(20) DEFAULT 'sent',
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (channel_id) REFERENCES channels(id) ON DELETE CASCADE
);

-- جدول آمار کلیک و بازدید پست‌ها در هر کانال
CREATE TABLE IF NOT EXISTS post_channel_stats (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    post_id INTEGER NOT NULL,
    channel_id INTEGER NOT NULL,
    clicks INTEGER DEFAULT 0,
    views INTEGER DEFAULT 0,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (channel_id) REFERENCES channels(id) ON DELETE CASCADE
);

-- جدول لاگ جزئیات کلیک‌ها جهت آمار دقیق جغرافیایی و دستگاهی
CREATE TABLE IF NOT EXISTS clicks_log (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    post_id INTEGER NOT NULL,
    channel_id INTEGER NOT NULL,
    ip VARCHAR(50),
    user_agent TEXT,
    clicked_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (channel_id) REFERENCES channels(id) ON DELETE CASCADE
);

-- جدول پیام‌های دریافتی ربات‌ها (صندوق پیام)
CREATE TABLE IF NOT EXISTS inbox (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    tenant_id INTEGER NOT NULL,
    channel_id INTEGER NOT NULL,
    sender_id VARCHAR(100) NOT NULL,
    sender_name VARCHAR(150),
    message_text TEXT,
    received_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (channel_id) REFERENCES channels(id) ON DELETE CASCADE
);

-- جدول پاسخ‌های خودکار تعاملی (Auto-responders)
CREATE TABLE IF NOT EXISTS auto_replies (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    tenant_id INTEGER NOT NULL,
    channel_id INTEGER NOT NULL,
    keyword VARCHAR(255) NOT NULL,
    reply_text TEXT NOT NULL,
    active INTEGER DEFAULT 1,
    FOREIGN KEY (tenant_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (channel_id) REFERENCES channels(id) ON DELETE CASCADE
);

-- جدول کدهای تخفیف عمومی (مدیریت توسط مدیر کل)
CREATE TABLE IF NOT EXISTS discount_codes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code VARCHAR(50) NOT NULL UNIQUE,
    type VARCHAR(20) DEFAULT 'percent', -- 'percent' یا 'fixed'
    amount DECIMAL(12,2) NOT NULL,
    max_uses INTEGER DEFAULT 0, -- ۰ = بی‌نهایت
    used INTEGER DEFAULT 0,
    expires_at DATETIME,
    active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- جدول تخفیف‌های اختصاصی به یک کاربر مشخص
CREATE TABLE IF NOT EXISTS discount_offers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    plan_id INTEGER NOT NULL,
    type VARCHAR(20) DEFAULT 'percent', -- 'percent' یا 'fixed'
    amount DECIMAL(12,2) NOT NULL,
    expires_at DATETIME,
    used INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE CASCADE
);

-- جدول پرداخت‌های کاربران (تراکنش‌های کارت به کارت / لینک بلو بانک و تایید دستی)
CREATE TABLE IF NOT EXISTS payments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    plan_id INTEGER NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    discount_code_id INTEGER,
    payment_method VARCHAR(50) DEFAULT 'card_to_card', -- کارت به کارت یا لینک مستقیم
    receipt_photo TEXT, -- مسیر فایل رسید آپلود شده
    reference_num VARCHAR(100), -- شماره ارجاع تراکنش
    status VARCHAR(20) DEFAULT 'pending', -- 'pending'، 'approved'، 'rejected'
    admin_notes TEXT, -- یادداشت‌های مدیر کل
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    verified_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE CASCADE,
    FOREIGN KEY (discount_code_id) REFERENCES discount_codes(id) ON DELETE SET NULL
);

-- جدول وب پوش VAPID برای ارسال اعلان به موبایل/PWA
CREATE TABLE IF NOT EXISTS push_subscriptions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    endpoint TEXT NOT NULL UNIQUE,
    keys_p256dh VARCHAR(255) NOT NULL,
    keys_auth VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- جدول تنظیمات کلیدی (تنظیمات عمومی پلتفرم و تنظیمات مستاجرین)
CREATE TABLE IF NOT EXISTS settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    tenant_id INTEGER DEFAULT 0, -- ۰ نشان‌دهنده‌ی تنظیمات عمومی کل پلتفرم (سوپر ادمین) است
    key_name VARCHAR(100) NOT NULL,
    key_value TEXT,
    UNIQUE(tenant_id, key_name)
);

-- جدول محدودیت نرخ درخواست‌ها (جلوگیری از حملات بروت‌فورس)
CREATE TABLE IF NOT EXISTS rate_limits (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    ip VARCHAR(50) NOT NULL,
    action VARCHAR(100) NOT NULL,
    attempts INTEGER DEFAULT 1,
    last_attempt INTEGER NOT NULL
);

-- جدول تیکت‌های پشتیبانی داخلی پستیار
CREATE TABLE IF NOT EXISTS tickets (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    subject VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    status VARCHAR(50) DEFAULT 'open', -- 'open', 'replied', 'resolved'
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ایندکس‌های بهینه‌سازی برای کارایی بالا
CREATE INDEX IF NOT EXISTS idx_channels_tenant_id ON channels(tenant_id);
CREATE INDEX IF NOT EXISTS idx_channels_platform_cid ON channels(platform, channel_id);
CREATE INDEX IF NOT EXISTS idx_posts_tenant_id ON posts(tenant_id);
CREATE INDEX IF NOT EXISTS idx_posts_status ON posts(status);
CREATE INDEX IF NOT EXISTS idx_posts_scheduled ON posts(scheduled_at);
CREATE INDEX IF NOT EXISTS idx_subscriptions_user ON subscriptions(user_id, status);
CREATE INDEX IF NOT EXISTS idx_subscriptions_end_date ON subscriptions(end_date);
CREATE INDEX IF NOT EXISTS idx_inbox_tenant ON inbox(tenant_id, channel_id);
CREATE INDEX IF NOT EXISTS idx_auto_replies_tenant ON auto_replies(tenant_id, channel_id, active);
CREATE INDEX IF NOT EXISTS idx_settings_tenant_key ON settings(tenant_id, key_name);
CREATE INDEX IF NOT EXISTS idx_rate_limits_ip_action ON rate_limits(ip, action);
CREATE INDEX IF NOT EXISTS idx_clicks_log_post ON clicks_log(post_id, channel_id);
CREATE INDEX IF NOT EXISTS idx_channel_messages_post ON channel_messages(post_id, channel_id);
CREATE INDEX IF NOT EXISTS idx_payments_status ON payments(status);
CREATE INDEX IF NOT EXISTS idx_payments_user ON payments(user_id);
CREATE INDEX IF NOT EXISTS idx_tickets_status ON tickets(status);
CREATE INDEX IF NOT EXISTS idx_tickets_user ON tickets(user_id);
