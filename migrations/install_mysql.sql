-- جدول کاربران (شامل مدیران کل و مستاجرین)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user', -- 'superadmin' یا 'user'
    status VARCHAR(20) DEFAULT 'active', -- 'active'، 'suspended'، 'inactive'
    business_name VARCHAR(150) NULL, -- نام کسب و کار
    business_type VARCHAR(150) NULL, -- نوع کسب و کار (گالری طلا، صرافی، ...)
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول پلن‌های اشتراک (مدیریت توسط مدیر کل)
CREATE TABLE IF NOT EXISTS plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    price DECIMAL(12,2) DEFAULT 0.00,
    duration_days INT DEFAULT 30, -- مدت اعتبار به روز
    max_channels INT DEFAULT 1, -- حداکثر کانال متصل
    max_posts INT DEFAULT 10, -- حداکثر پست ارسالی در ماه (۰ = نامحدود)
    features TEXT, -- قالب JSON ویژگی‌ها
    payment_url TEXT NULL, -- لینک پرداخت اختصاصی هر پلن
    image_url TEXT NULL, -- آدرس تصویر شاخص پلن اشتراکی
    description TEXT NULL, -- توضیحات اختصاصی هر پلن
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول اشتراک‌های فعال کاربران
CREATE TABLE IF NOT EXISTS subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    status VARCHAR(20) DEFAULT 'active', -- 'active'، 'expired'، 'cancelled'
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول ثبت جهانی کانال‌ها (ضد تقلب)
CREATE TABLE IF NOT EXISTS channel_registry (
    id INT AUTO_INCREMENT PRIMARY KEY,
    channel_id VARCHAR(150) NOT NULL, -- مثلاً MyGoldShop@ یا chat_id عددی
    platform VARCHAR(20) NOT NULL, -- 'telegram' یا 'bale'
    owner_user_id INT NOT NULL, -- اولین مالک
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_platform_channel (platform, channel_id),
    FOREIGN KEY (owner_user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول کانال‌های اختصاصی هر مستاجر (Tenant Channels)
CREATE TABLE IF NOT EXISTS channels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL, -- شناسه‌ کاربری مستاجر
    name VARCHAR(150) NOT NULL,
    platform VARCHAR(20) NOT NULL, -- 'telegram' یا 'bale'
    channel_id VARCHAR(150) NOT NULL,
    token TEXT NOT NULL,
    link_config TEXT, -- قالب JSON لینک‌های سه‌گانه
    button_config TEXT, -- قالب JSON دکمه‌های تعاملی زیرین
    webhook_active INT DEFAULT 0,
    webhook_secret VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول پست‌های تولیدشده توسط کاربران
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    media_url TEXT,
    status VARCHAR(20) DEFAULT 'draft', -- 'draft'، 'scheduled'، 'sent'، 'failed'
    scheduled_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول پیگیری پیام‌های ارسال‌شده به کانال‌ها جهت ویرایش زنده
CREATE TABLE IF NOT EXISTS channel_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    channel_id INT NOT NULL,
    message_id VARCHAR(100) NOT NULL,
    status VARCHAR(20) DEFAULT 'sent',
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (channel_id) REFERENCES channels(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول آمار کلیک و بازدید پست‌ها در هر کانال
CREATE TABLE IF NOT EXISTS post_channel_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    channel_id INT NOT NULL,
    clicks INT DEFAULT 0,
    views INT DEFAULT 0,
    UNIQUE KEY uq_post_channel (post_id, channel_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (channel_id) REFERENCES channels(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول لاگ جزئیات کلیک‌ها
CREATE TABLE IF NOT EXISTS clicks_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    channel_id INT NOT NULL,
    ip VARCHAR(50),
    user_agent TEXT,
    clicked_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (channel_id) REFERENCES channels(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول پیام‌های دریافتی ربات‌ها (صندوق پیام)
CREATE TABLE IF NOT EXISTS inbox (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    channel_id INT NOT NULL,
    sender_id VARCHAR(100) NOT NULL,
    sender_name VARCHAR(150),
    message_text TEXT,
    received_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (channel_id) REFERENCES channels(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول پاسخ‌های خودکار تعاملی (Auto-responders)
CREATE TABLE IF NOT EXISTS auto_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    channel_id INT NOT NULL,
    keyword VARCHAR(255) NOT NULL,
    reply_text TEXT NOT NULL,
    active INT DEFAULT 1,
    FOREIGN KEY (tenant_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (channel_id) REFERENCES channels(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول کدهای تخفیف عمومی
CREATE TABLE IF NOT EXISTS discount_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    type VARCHAR(20) DEFAULT 'percent', -- 'percent' یا 'fixed'
    amount DECIMAL(12,2) NOT NULL,
    max_uses INT DEFAULT 0, -- ۰ = بی‌نهایت
    used INT DEFAULT 0,
    expires_at DATETIME,
    active INT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول تخفیف‌های اختصاصی به یک کاربر مشخص
CREATE TABLE IF NOT EXISTS discount_offers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    type VARCHAR(20) DEFAULT 'percent', -- 'percent' یا 'fixed'
    amount DECIMAL(12,2) NOT NULL,
    expires_at DATETIME,
    used INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول پرداخت‌های کاربران (تایید دستی رسید)
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    discount_code_id INT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول وب پوش VAPID برای ارسال اعلان
CREATE TABLE IF NOT EXISTS push_subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    endpoint VARCHAR(500) NOT NULL UNIQUE,
    keys_p256dh VARCHAR(255) NOT NULL,
    keys_auth VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول تنظیمات کلیدی
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT DEFAULT 0, -- ۰ نشان‌دهنده‌ی تنظیمات عمومی کل پلتفرم (سوپر ادمین) است
    key_name VARCHAR(100) NOT NULL,
    key_value TEXT,
    UNIQUE KEY uq_tenant_key (tenant_id, key_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول محدودیت نرخ درخواست‌ها (جلوگیری از حملات بروت‌فورس)
CREATE TABLE IF NOT EXISTS rate_limits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip VARCHAR(50) NOT NULL,
    action VARCHAR(100) NOT NULL,
    attempts INT DEFAULT 1,
    last_attempt INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول تیکت‌های پشتیبانی داخلی پستیار
CREATE TABLE IF NOT EXISTS tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    status VARCHAR(50) DEFAULT 'open', -- 'open', 'replied', 'resolved'
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ایندکس‌های بهینه‌سازی برای کارایی بالا
CREATE INDEX idx_channels_tenant_id ON channels(tenant_id);
CREATE INDEX idx_channels_platform_cid ON channels(platform, channel_id);
CREATE INDEX idx_posts_tenant_id ON posts(tenant_id);
CREATE INDEX idx_posts_status ON posts(status);
CREATE INDEX idx_posts_scheduled ON posts(scheduled_at);
CREATE INDEX idx_subscriptions_user ON subscriptions(user_id, status);
CREATE INDEX idx_subscriptions_end_date ON subscriptions(end_date);
CREATE INDEX idx_inbox_tenant ON inbox(tenant_id, channel_id);
CREATE INDEX idx_auto_replies_tenant ON auto_replies(tenant_id, channel_id, active);
CREATE INDEX idx_settings_tenant_key ON settings(tenant_id, key_name);
CREATE INDEX idx_rate_limits_ip_action ON rate_limits(ip, action);
CREATE INDEX idx_clicks_log_post ON clicks_log(post_id, channel_id);
CREATE INDEX idx_channel_messages_post ON channel_messages(post_id, channel_id);
CREATE INDEX idx_payments_status ON payments(status);
CREATE INDEX idx_payments_user ON payments(user_id);
CREATE INDEX idx_tickets_status ON tickets(status);
CREATE INDEX idx_tickets_user ON tickets(user_id);
