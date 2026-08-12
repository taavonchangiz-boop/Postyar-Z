# Postyar Mobile API Contract — For Google AI Studio

> **Version:** 1.0.0  
> **Base URL:** `https://asovin.ir/api/v1`  
> **Auth:** Bearer Token (SHA-256 hashed, 30-day expiry)  
> **Format:** JSON (`Content-Type: application/json; charset=utf-8`)  
> **Language:** All messages, errors, and responses are in **Persian (Farsi)**.  

---

## CRITICAL INSTRUCTIONS FOR AI STUDIO

```
DO NOT invent, simulate, mock, replace, redesign, or approximate any backend
functionality. The Android application must consume the production API layer
of the existing Postyar backend at https://asovin.ir.

DO NOT create a separate database for Android.
DO NOT duplicate business logic on the client.
Room Database is LOCAL CACHE ONLY — the server database is the single source of truth.
DO NOT modify any existing website behavior.
```

---

## 1. AUTHENTICATION

### How It Works
- Login returns a **raw token** (64 hex chars). This token is sent ONLY ONCE.
- Server stores **SHA-256 hash** of the token — raw token is never stored.
- Every authenticated request includes: `Authorization: Bearer <token>`
- Tokens expire after **30 days**.
- Max **5 active tokens** per user (oldest auto-revoked).
- Token is device-specific (device_name field).

### Security Notes
- Rate limiting: 10 login attempts per 5 minutes per IP.
- Failed logins are tracked. Account locked after exceeding limit.
- Password is hashed with **bcrypt (cost 12)**.

---

## 2. STANDARD RESPONSE FORMAT

Every endpoint returns JSON in this structure:

### Success Response (200)
```json
{
  "success": true,
  "message": "optional success message in Persian",
  "data": { ... }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Persian error message"
}
```

### Validation Error (422)
```json
{
  "success": false,
  "message": "اطلاعات ورودی نامعتبر است",
  "errors": {
    "field_name": "Persian validation error for this field"
  }
}
```

### HTTP Status Codes
| Code | Meaning |
|------|---------|
| 200 | Success |
| 400 | Bad Request (general error) |
| 401 | Unauthorized (missing/invalid token) |
| 403 | Forbidden (insufficient permissions) |
| 404 | Not Found |
| 422 | Validation Error |
| 429 | Too Many Requests (rate limited) |
| 500 | Internal Server Error |

---

## 3. SANITIZED USER OBJECT

Whenever a user object is returned (login, register, /auth/me), it looks like:
```json
{
  "id": 1,
  "name": "نام کاربر",
  "email": "user@example.com",
  "role": "user",
  "status": "active",
  "business_name": null,
  "business_type": null,
  "phone": null,
  "birthday": null,
  "referral_code": "ABC123",
  "referral_points": 0.00,
  "wallet_balance": 0.00,
  "created_at": "2024-01-15 10:30:00"
}
```

**Fields NEVER returned:** `password`, any internal session data.

---

## 4. COMPLETE ENDPOINT REFERENCE

### Legend
- 🔓 = No auth required
- 🔐 = Bearer token required
- 👑 = Superadmin only

---

### 4.1 AUTH — احراز هویت

#### POST /auth/login 🔓
Login and receive API token.

**Request Body (JSON):**
```json
{
  "email": "user@example.com",
  "password": "secret123",
  "device_name": "android",
  "ref": "optional_referral_code"
}
```

**Success Response:**
```json
{
  "success": true,
  "message": "ورود موفقیت‌آمیز بود.",
  "data": {
    "token": "a1b2c3d4e5f6...64hexchars",
    "user": { /* sanitized user object */ }
  }
}
```

**Error (401):**
```json
{
  "success": false,
  "message": "ایمیل یا کلمه عبور نادرست است."
}
```

**Error (429):** Rate limited — 10 attempts per 5 minutes.

---

#### POST /auth/register 🔓
Register new user and auto-login.

**Request Body (JSON):**
```json
{
  "name": "نام و نام خانوادگی",
  "email": "user@example.com",
  "password": "secret123",
  "password_confirm": "secret123",
  "business_name": "",
  "business_type": "",
  "device_name": "android",
  "ref": "optional_referral_code"
}
```

**Success Response:**
```json
{
  "success": true,
  "message": "ثبت‌نام با موفقیت انجام شد.",
  "data": {
    "token": "a1b2c3...",
    "user": { /* sanitized user object */ }
  }
}
```

**Validation Errors:** email uniqueness, password match, min 6 chars.

---

#### POST /auth/logout 🔐
Revoke current token.

**No request body.** Uses token from Authorization header.

**Response:** `{ "success": true, "message": "با موفقیت خارج شدید.", "data": null }`

---

#### GET /auth/me 🔐
Get current user info with active subscription.

**Response:**
```json
{
  "success": true,
  "data": {
    "user": { /* sanitized user object */ },
    "subscription": {
      "id": 5,
      "plan_id": 2,
      "plan_title": "پلن حرفه‌ای",
      "plan_price": 250000.00,
      "max_channels": 5,
      "max_posts": 100,
      "features": ["feature1", "feature2"],
      "start_date": "2024-01-15 00:00:00",
      "end_date": "2024-02-15 00:00:00",
      "status": "active"
    }
  }
}
```

Note: `subscription` is `null` if no active subscription.

---

#### PUT /auth/profile 🔐
Update user profile.

**Request Body (JSON):**
```json
{
  "name": "نام جدید",
  "email": "new@example.com",
  "birthday": "1370/01/15"
}
```

---

#### POST /auth/change-password 🔐
Change password.

**Request Body (JSON):**
```json
{
  "current_password": "old123",
  "new_password": "new456",
  "confirm_password": "new456"
}
```

---

#### POST /auth/reset-password 🔓
Request email-based password reset.

**Request Body:** `{ "email": "user@example.com" }`

**Response (always 200):** `{ "success": true, "message": "اگر ایمیل در سامانه ثبت شده باشد، لینک بازیابی ارسال خواهد شد." }`

---

#### POST /auth/reset-password/confirm 🔓
Confirm email password reset with token.

**Request Body:**
```json
{
  "token": "64_char_token_from_email",
  "new_password": "newpass",
  "confirm_password": "newpass"
}
```

---

#### POST /auth/reset-password-sms 🔓
Request SMS-based password reset.

**Request Body:** `{ "phone": "09121234567" }`

**Rate Limit:** 3 requests per 5 minutes.

---

#### POST /auth/verify-sms-code 🔓
Verify SMS code and set new password.

**Request Body:**
```json
{
  "code": "12345",
  "new_password": "newpass",
  "confirm_password": "newpass"
}
```

---

### 4.2 BOOTSTRAP & SYNC — بوت‌استرپ و همگام‌سازی

#### GET /bootstrap 🔐
**THE MOST IMPORTANT ENDPOINT.** Called once after login. Returns ALL data needed to build the entire app UI.

**Response:**
```json
{
  "success": true,
  "data": {
    "user": { /* sanitized user */ },
    "quota": {
      "can_send_post": true,
      "posts_used": 5,
      "posts_limit": 100,
      "channels_used": 2,
      "channels_limit": 5,
      "posts_remaining": 95,
      "channels_remaining": 3
    },
    "channels": [
      {
        "id": 1,
        "tenant_id": 1,
        "name": "کانال تلگرام من",
        "platform": "telegram",
        "channel_id": "@mychannel",
        "token": "bot_token...",
        "link_config": "[{\"name\":\"سایت\",\"url\":\"https://...\"}]",
        "button_config": null,
        "webhook_active": 0,
        "created_at": "2024-01-15 10:30:00"
      }
    ],
    "posts": [
      {
        "id": 1,
        "tenant_id": 1,
        "title": "عنوان پست",
        "content": "محتوای پست...",
        "media_url": "/assets/posts/image.webp",
        "status": "sent",
        "scheduled_at": null,
        "target_channels": "[1,2]",
        "created_at": "2024-01-15 12:00:00",
        "click_count": 42
      }
    ],
    "notifications": [ /* notification objects */ ],
    "unread_count": 3,
    "auto_replies": [ /* auto_reply objects */ ],
    "inbox": [ /* inbox message objects */ ],
    "tickets": [ /* ticket objects */ ],
    "plans": [ /* plan objects */ ],
    "offers": [ /* discount offer objects */ ],
    "subscription_history": [ /* subscription objects */ ],
    "payment_history": [ /* payment objects with plan_title */ ],
    "settings": {
      "gold_schedule": "",
      "gold_api_url": "",
      "ai_provider": "openai",
      "caption_format": ""
    },
    "responder_settings": { /* responder_enabled_1: "1", etc. */ },
    "ticket_categories": [ /* {id, name, sort_order} */ ],
    "announcement": { "id": 1, "title": "...", "message": "..." },
    "announcement_unread": false,
    "referral_info": {
      "code": "ABC123",
      "total": 5
    },
    "wallet_balance": 15000.00
  }
}
```

---

#### GET /sync?since=<unix_timestamp> 🔐
Periodic sync endpoint. Returns recent changes.

**Query Parameters:**
- `since` (optional): Unix timestamp. If provided, only returns posts created after this time.

**Response:**
```json
{
  "success": true,
  "data": {
    "server_time": "2024-06-15 14:30:00",
    "notifications": [ /* all recent notifications */ ],
    "unread_count": 2,
    "channels": [ /* current channels */ ],
    "recent_posts": [ /* posts, filtered by 'since' if provided */ ],
    "quota": { /* same as bootstrap quota */ },
    "wallet_balance": 15000.00
  }
}
```

---

### 4.3 CHANNELS — کانال‌ها

#### GET /channels 🔐
List all channels for current user.

**Response:** `data` is array of channel objects (same structure as in bootstrap).

---

#### POST /channels 🔐
Add a new channel.

**Request Body (JSON):**
```json
{
  "name": "نام کانال",
  "platform": "telegram",
  "channel_id": "@channelusername",
  "token": "bot_api_token"
}
```

`platform` values: `"telegram"`, `"eitaa"`, `"gap"`, etc.

---

#### GET /channels/{id} 🔐
Get single channel details.

---

#### PUT /channels/{id} 🔐
Update channel settings.

**Request Body (JSON):**
```json
{
  "name": "نام جدید",
  "platform": "telegram",
  "channel_id_val": "@channelusername",
  "token": "new_bot_token",
  "link_name_1": "سایت من",
  "link_url_1": "https://example.com",
  "link_name_2": "",
  "link_url_2": "",
  "link_name_3": "",
  "link_url_3": "",
  "buttons_active": true,
  "btn_text_1": "دکمه ۱",
  "btn_url_1": "https://...",
  "btn_text_2": "",
  "btn_url_2": ""
}
```

Note: `channel_id_val` (not `channel_id`) to avoid path parameter conflict.

---

#### DELETE /channels/{id} 🔐
Delete a channel. Checks ownership first.

---

### 4.4 POSTS — پست‌ها

#### GET /posts?status=&limit=&offset= 🔐
List posts with optional filtering.

**Query Parameters:**
- `status` (optional): Filter by status: `"draft"`, `"queued"`, `"scheduled"`, `"sent"`, `"failed"`, `"cancelled"`
- `limit` (default 50, max 200)
- `offset` (default 0)

**Response:** Each post includes `click_count` field.

---

#### POST /posts 🔐
Create a new post.

**Request (multipart/form-data for file upload OR JSON):**
```json
{
  "title": "عنوان پست",
  "content": "محتوای پست",
  "send_type": "instant",
  "post_channels": [1, 2, 3],
  "caption_format": ""
}
```

For **scheduled** posts, add:
```json
{
  "send_type": "scheduled",
  "sched_date": "1403/03/25",
  "sched_hour": "18",
  "sched_minute": "30"
}
```

**Date format:** Jalali (Persian calendar) — `YYYY/MM/DD`. Server converts to Gregorian.

**File Upload:** Send image as `media_file` field (multipart/form-data). Converted to WebP server-side. Max 5MB. Allowed: JPG, PNG, GIF, WebP.

**Business Logic:**
- Checks quota via `Quota::getTenantQuota()`
- For `instant`: calls `Sender::sendPostToChannels()` immediately
- For `scheduled`: saves with status `"scheduled"` — **server cron handles actual sending, NOT the Android app**
- Post status updates: `"queued"` → `"sent"` or `"failed"`

---

#### GET /posts/{id} 🔐
Get single post details with `click_count`.

---

#### POST /posts/{id}/cancel 🔐
Cancel a post. Only posts with status `"scheduled"`, `"queued"`, or `"draft"` can be cancelled.

---

#### POST /posts/{id}/retry 🔐
Retry a failed post. Only posts with status `"failed"` can be retried.

---

### 4.5 NOTIFICATIONS — اعلان‌ها

#### GET /notifications?limit=&offset= 🔐
List notifications.

**Query Parameters:** `limit` (default 20, max 100), `offset` (default 0)

**Response:**
```json
{
  "data": {
    "notifications": [
      {
        "id": 1,
        "user_id": 1,
        "type": "payment",
        "title": "عنوان اعلان",
        "message": "متن اعلان",
        "target_section": "",
        "is_read": 0,
        "created_at": "2024-01-15 12:00:00"
      }
    ],
    "unread_count": 3
  }
}
```

---

#### POST /notifications/{id}/read 🔐
Mark single notification as read.

**Response:** `{ "data": { "remaining_unread": 2 } }`

---

#### POST /notifications/read-all 🔐
Mark all notifications as read.

**Response:** `{ "data": { "marked_count": 5 } }`

---

### 4.6 BILLING — پرداخت و اشتراک

#### GET /plans 🔓
List all available plans (public, no auth needed).

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "پلن رایگان",
      "price": 0.00,
      "duration_days": 30,
      "max_channels": 1,
      "max_posts": 10,
      "features": ["feature1", "feature2"],
      "payment_url": null,
      "image_url": null,
      "description": "...",
      "early_renewal_discount": 0,
      "general_discount": 0,
      "discount_badge_text": null,
      "is_featured": 0,
      "created_at": "..."
    }
  ]
}
```

---

#### POST /payments 🔐
Submit a new payment (card-to-card).

**Request (multipart/form-data):**
- `plan_id` (required, int)
- `amount` (required, float)
- `reference_num` (required, string — bank reference number)
- `receipt_photo` (file, optional — image of bank receipt)

---

#### GET /payments 🔐
List user's payment history.

**Response:** Each payment includes `plan_title` (joined from plans table).

---

#### POST /coupons/validate 🔐
Validate a discount coupon code.

**Request:** `{ "code": "DISCOUNT20", "plan_id": 2 }`

**Response:**
```json
{
  "data": {
    "id": 1,
    "code": "DISCOUNT20",
    "type": "percent",
    "amount": 20.00,
    "max_uses": 100,
    "used": 15,
    "expires_at": "2025-01-01 00:00:00"
  }
}
```

---

### 4.7 TICKETS — تیکت‌های پشتیبانی

#### GET /tickets 🔐
List all tickets for current user.

**Response:** Each ticket includes `replies_count` field.

---

#### POST /tickets 🔐
Create a new ticket.

**Request (multipart/form-data):**
- `subject` (required)
- `category` (required)
- `message` (required)
- `attachment` (file, optional — JPG, PNG, GIF, WebP, PDF, max 5MB)

---

#### GET /tickets/{id} 🔐
Get ticket details with all replies.

**Response:**
```json
{
  "data": {
    "ticket": { /* ticket object */ },
    "replies": [
      {
        "id": 1,
        "ticket_id": 5,
        "user_id": 1,
        "sender_name": "نام کاربر",
        "message": "متن پاسخ",
        "created_at": "..."
      }
    ]
  }
}
```

---

#### POST /tickets/{id}/reply 🔐
Reply to a ticket.

**Request (multipart/form-data):**
- `message` (required)
- `close_after_reply` (optional, bool)
- `attachment` (file, optional)

---

### 4.8 SETTINGS — تنظیمات

#### GET /settings 🔐
Get all user settings as key-value pairs.

**Response:**
```json
{
  "data": {
    "gold_schedule": "",
    "gold_api_url": "",
    "gold_currency": "",
    "gold_template": "",
    "ai_provider": "openai",
    "ai_api_key": "",
    "ai_model": "gpt-4",
    "caption_format": "",
    "watermark_active": "0"
  }
}
```

---

#### POST /settings/gold 🔐
Save gold price ticker settings.

**Request (multipart/form-data):**
- `gold_schedule` (string — cron expression or time)
- `gold_api_url` (string — external gold price API URL)
- `gold_currency` (string — currency type)
- `gold_template` (string — message template)
- `gold_channels` (JSON array of channel IDs)
- `gold_image` (file, optional — gold price chart image)

---

#### POST /settings/gold/trigger 🔐
Manually trigger gold price publish. Fetches prices from API, builds message, sends to channels.

---

#### PUT /settings/advanced 🔐
Save advanced settings.

**Request Body (JSON) — all fields optional:**
```json
{
  "ai_provider": "openai",
  "ai_api_key": "sk-...",
  "ai_model": "gpt-4",
  "ai_api_url": "",
  "auto_publish_woo": "0",
  "watermark_active": "0",
  "caption_format": "",
  "inbound_method": "webhook",
  "poll_interval": "60"
}
```

---

### 4.9 AUTO-RESPONDER — پاسخگوی خودکار

#### GET /auto-responder 🔐
List all auto-replies with channel info.

**Response:** Each object includes `channel_name` and `channel_platform`.

---

#### POST /auto-responder 🔐
Add a new auto-reply rule.

**Request:** `{ "channel_id": 1, "keyword": "سلام", "reply_text": "سلام! چطور می‌تونم کمکتون کنم؟" }`

---

#### DELETE /auto-responder/{id} 🔐
Delete an auto-reply rule.

---

#### POST /auto-responder/toggle 🔐
Enable/disable auto-responder for a channel.

**Request:** `{ "channel_id": 1, "enabled": 1 }`

---

### 4.10 WALLET & REFERRAL — کیف پول و زیرمجموعه‌گیری

#### GET /wallet 🔐
Get wallet balance and recent transactions.

**Response:**
```json
{
  "data": {
    "balance": 15000.00,
    "transactions": [
      {
        "id": 1,
        "user_id": 1,
        "type": "credit",
        "amount": 5000.00,
        "balance_after": 15000.00,
        "description": "تبدیل امتیاز",
        "reference_type": "referral_convert",
        "reference_id": 3,
        "created_at": "..."
      }
    ]
  }
}
```

---

#### POST /wallet/convert-points 🔐
Convert referral points to wallet balance.

**Request:** `{ "points": 100 }`

**Response:**
```json
{
  "data": {
    "new_balance": 25000.00,
    "converted": 100,
    "wallet_amount": 100.00
  }
}
```

---

#### GET /referral 🔐
Get referral information.

**Response:**
```json
{
  "data": {
    "code": "ABC123",
    "link": "https://asovin.ir/register?ref=ABC123",
    "stats": { "total": 5 },
    "referrals": [
      {
        "id": 1,
        "referred_name": "کاربر جدید",
        "referred_email": "new@example.com",
        "status": "active",
        "created_at": "..."
      }
    ],
    "referral_points": 250.00
  }
}
```

---

### 4.11 ANALYTICS — تحلیل آماری

#### GET /analytics/links 🔐
Get all tracked links with click stats.

**Response:** Each link includes `total_clicks` and `unique_clicks`.

---

#### GET /analytics/links/{id} 🔐
Get detailed click breakdown for a specific link.

**Response:**
```json
{
  "data": {
    "link": { /* link object with stats */ },
    "daily_breakdown": [
      { "date": "2024-06-15", "clicks": 15, "unique_clicks": 12 }
    ]
  }
}
```

---

### 4.12 ADMIN — پنل مدیریت (👑 Superadmin Only)

All admin endpoints require `role: "superadmin"`.

#### GET /admin/dashboard 👑
Admin dashboard stats.

**Response:**
```json
{
  "data": {
    "users": { "total": 150, "active": 140, "suspended": 10 },
    "payments": { "total": 200, "amount": 50000000, "pending": 5, "approved": 190 },
    "tickets": { "total": 80, "open": 15 },
    "recent_users": [ /* user objects */ ]
  }
}
```

---

#### GET /admin/users?status=&search=&limit=&offset= 👑
List all users with optional filtering.

---

#### POST /admin/users/{id}/suspend 👑
Suspend a user.

---

#### POST /admin/users/{id}/activate 👑
Activate a suspended user.

---

#### GET /admin/payments 👑
List all payments (admin view).

---

#### POST /admin/payments/{id}/approve 👑
Approve a payment.

---

#### GET /admin/tickets 👑
List all tickets (admin view).

---

#### POST /admin/tickets/{id}/reply 👑
Admin reply to a ticket.

---

#### GET /admin/plans 👑
List all plans.

---

#### POST /admin/plans 👑
Create a new plan.

---

#### PUT /admin/plans/{id} 👑
Update a plan.

---

#### DELETE /admin/plans/{id} 👑
Delete a plan.

---

#### POST /admin/broadcast 👑
Send push notification broadcast to all users.

---

#### POST /admin/discounts 👑
Add a new discount code.

---

#### DELETE /admin/discounts/{id} 👑
Delete a discount code.

---

## 5. DATABASE SCHEMA (Key Tables)

The Android app should NOT directly access the database. This schema is provided for understanding data structures.

### users
| Column | Type | Notes |
|--------|------|-------|
| id | INTEGER PK | Auto-increment |
| name | VARCHAR(150) | |
| email | VARCHAR(150) | Unique |
| password | VARCHAR(255) | Bcrypt hashed |
| role | VARCHAR(20) | `user` or `superadmin` |
| status | VARCHAR(20) | `active`, `suspended`, `inactive` |
| business_name | VARCHAR(150) | Nullable |
| business_type | VARCHAR(150) | Nullable |
| phone | VARCHAR(15) | Nullable |
| referral_code | VARCHAR(20) | Unique, nullable |
| referred_by | INTEGER | Nullable |
| referral_points | DECIMAL(15,2) | Default 0 |
| wallet_balance | DECIMAL(15,2) | Default 0 |
| birthday | VARCHAR(10) | Nullable (Jalali) |
| created_at | DATETIME | |

### plans
| Column | Type |
|--------|------|
| id | INTEGER PK |
| title | VARCHAR(100) |
| price | DECIMAL(12,2) |
| duration_days | INTEGER |
| max_channels | INTEGER |
| max_posts | INTEGER |
| features | TEXT (JSON array) |
| payment_url | TEXT |
| image_url | TEXT |
| description | TEXT |
| early_renewal_discount | INTEGER |
| general_discount | INTEGER |
| discount_badge_text | VARCHAR(150) |
| is_featured | INTEGER |

### channels
| Column | Type |
|--------|------|
| id | INTEGER PK |
| tenant_id | INTEGER FK → users |
| name | VARCHAR(150) |
| platform | VARCHAR(20) | `telegram`, `eitaa`, `gap`, etc. |
| channel_id | VARCHAR(150) | e.g. `@channelname` |
| token | TEXT | Bot token |
| link_config | TEXT (JSON) |
| button_config | TEXT (JSON) |
| webhook_active | INTEGER |
| webhook_secret | VARCHAR(100) |

### posts
| Column | Type |
|--------|------|
| id | INTEGER PK |
| tenant_id | INTEGER FK → users |
| title | VARCHAR(255) |
| content | TEXT |
| media_url | TEXT |
| status | VARCHAR(20) | `draft`, `queued`, `scheduled`, `sent`, `failed`, `cancelled` |
| scheduled_at | DATETIME |
| target_channels | TEXT (JSON array of channel IDs) |

### subscriptions
| Column | Type |
|--------|------|
| id | INTEGER PK |
| user_id | INTEGER FK → users |
| plan_id | INTEGER FK → plans |
| start_date | DATETIME |
| end_date | DATETIME |
| status | VARCHAR(20) | `active`, `expired` |

### payments
| Column | Type |
|--------|------|
| id | INTEGER PK |
| user_id | INTEGER FK → users |
| plan_id | INTEGER FK → plans |
| amount | DECIMAL(12,2) |
| discount_code_id | INTEGER FK → discount_codes |
| payment_method | VARCHAR(50) | Default `card_to_card` |
| receipt_photo | TEXT |
| reference_num | VARCHAR(100) |
| status | VARCHAR(20) | `pending`, `approved`, `rejected` |
| admin_notes | TEXT |
| verified_at | DATETIME |

### tickets
| Column | Type |
|--------|------|
| id | INTEGER PK |
| user_id | INTEGER FK → users |
| subject | VARCHAR(255) |
| category | VARCHAR(100) |
| message | TEXT |
| status | VARCHAR(50) | `open`, `closed`, `replied` |
| attachment | TEXT |
| assigned_to | INTEGER |

### ticket_replies
| Column | Type |
|--------|------|
| id | INTEGER PK |
| ticket_id | INTEGER FK → tickets |
| user_id | INTEGER FK → users (nullable) |
| message | TEXT |

### auto_replies
| Column | Type |
|--------|------|
| id | INTEGER PK |
| tenant_id | INTEGER FK → users |
| channel_id | INTEGER FK → channels |
| keyword | VARCHAR(255) |
| reply_text | TEXT |
| active | INTEGER |

### notifications
| Column | Type |
|--------|------|
| id | INTEGER PK |
| user_id | INTEGER FK → users |
| type | VARCHAR(50) | `general`, `payment`, `subscription`, etc. |
| title | TEXT |
| message | TEXT |
| target_section | VARCHAR(100) |
| is_read | INTEGER |

### settings (Key-Value store)
| Column | Type |
|--------|------|
| id | INTEGER PK |
| tenant_id | INTEGER | 0 = global/system settings |
| key_name | VARCHAR(100) |
| key_value | TEXT |

### wallet_transactions
| Column | Type |
|--------|------|
| id | INTEGER PK |
| user_id | INTEGER FK → users |
| type | VARCHAR(30) | `credit`, `debit` |
| amount | DECIMAL(15,2) |
| balance_after | DECIMAL(15,2) |
| description | TEXT |
| reference_type | VARCHAR(50) |
| reference_id | INTEGER |

### api_tokens (Mobile Auth)
| Column | Type |
|--------|------|
| id | INTEGER PK |
| user_id | INTEGER FK → users |
| token_hash | TEXT | SHA-256 of raw token |
| device_name | VARCHAR(100) |
| created_at | DATETIME |
| last_used_at | DATETIME |
| expires_at | DATETIME |
| revoked_at | DATETIME | Null = active |

### Other Tables
- `referrals` — Referral tracking
- `referral_settings` — System referral configuration
- `link_tracking` — URL shortening/tracking
- `link_clicks` — Click logs for tracked links
- `clicks_log` — Post click tracking
- `inbox` — Channel incoming messages
- `channel_messages` — Sent message tracking
- `post_channel_stats` — Per-channel post stats
- `verification_codes` — SMS verification codes
- `discount_codes` — Public discount codes
- `discount_offers` — User-specific discount offers
- `push_subscriptions` — Web Push subscriptions
- `rate_limits` — Rate limiting
- `sms_templates` / `sms_log` — SMS system
- `email_templates` / `email_log` — Email system
- `responder_logs` — Auto-responder logs
- `channel_registry` — Global channel ownership (anti-fraud)
- `ticket_categories` — Ticket category definitions

---

## 6. APP ARCHITECTURE RULES

### Data Flow
```
Android App
    ↓ HTTPS (JSON)
REST API (/api/v1/*)
    ↓
PHP Backend (existing Domain/Business Logic)
    ↓
SQLite Database (shared with website)
```

### Android Must NOT:
- Have its own primary database
- Duplicate server-side business logic
- Execute scheduled posts locally (server cron handles this)
- Generate fake/mock data
- Modify the website's behavior

### Android MUST:
- Use Room ONLY as a local read-cache
- Call `/bootstrap` once after login for initial data
- Call `/sync` periodically (every 30-60 seconds when app is open)
- Store the Bearer token in Android Keystore / EncryptedSharedPreferences
- Send `Authorization: Bearer <token>` header on every authenticated request
- Handle all errors from the standard response format
- Be fully Persian (Farsi) and RTL
- Use the brand name: **پُست‌یار** (with nim-fasele/half-space)
- Package name: `com.postyar.app`

### Synchronization Strategy
1. **After Login:** Call `GET /bootstrap` → cache everything in Room
2. **Periodically (30-60s):** Call `GET /sync?since=<last_sync_time>` → update Room cache
3. **On pull-to-refresh:** Call `GET /sync` → update Room cache
4. **On user action:** Call the specific API → update Room cache → update UI
5. **Post Scheduling:** Android only sends the command. Server cron publishes the post.

### File Uploads
- Content-Type: `multipart/form-data`
- Images converted to WebP server-side
- Max size: 5MB
- Allowed formats: JPG, PNG, GIF, WebP
- For tickets: also PDF allowed
- Returned path format: `/assets/<subfolder>/<filename>.webp`
- Full URL: `https://asovin.ir/assets/<subfolder>/<filename>.webp`

### Quota System
The `quota` object (from bootstrap/sync) determines what the user can do:
- `can_send_post`: Boolean — whether user can create new posts
- `posts_used` / `posts_limit`: Current vs max posts in subscription period
- `channels_used` / `channels_limit`: Current vs max connected channels
- If `can_send_post` is false, show upgrade plan prompt

---

## 7. APP SCREENS MAP

Based on the API endpoints, the Android app needs these screens:

| # | Screen | API Used | Description |
|---|--------|----------|-------------|
| 1 | **Splash** | — | App logo + loading |
| 2 | **Login** | POST /auth/login | Email + password + optional referral code |
| 3 | **Register** | POST /auth/register | Name, email, password, confirm, business info, ref code |
| 4 | **Forgot Password (Email)** | POST /auth/reset-password | Email input → send reset link |
| 5 | **Forgot Password (SMS)** | POST /auth/reset-password-sms → POST /auth/verify-sms-code | Phone → code → new password |
| 6 | **Dashboard** | GET /bootstrap (initial), GET /sync (periodic) | Main screen: quota, recent posts, notifications badge |
| 7 | **Posts List** | GET /posts | All posts with status filter tabs |
| 8 | **Create Post** | POST /posts | Title, content, media, channels, send_type (instant/scheduled) |
| 9 | **Post Detail** | GET /posts/{id} | View single post with click stats |
| 10 | **Channels** | GET /channels | List connected channels |
| 11 | **Add/Edit Channel** | POST/PUT /channels | Channel name, platform, channel_id, token, links, buttons |
| 12 | **Notifications** | GET /notifications, POST /notifications/{id}/read, POST /notifications/read-all | List with unread badge |
| 13 | **Plans & Billing** | GET /plans, POST /payments, GET /payments | View plans, submit payment, history |
| 14 | **Tickets** | GET /tickets, POST /tickets, GET /tickets/{id}, POST /tickets/{id}/reply | Support system |
| 15 | **Settings** | GET /settings, PUT /settings/advanced | AI settings, caption format, etc. |
| 16 | **Gold Ticker** | GET /settings, POST /settings/gold, POST /settings/gold/trigger | Gold price automation |
| 17 | **Auto Responder** | GET/POST/DELETE /auto-responder, POST /auto-responder/toggle | Keyword-based auto-reply rules |
| 18 | **Wallet** | GET /wallet, POST /wallet/convert-points | Balance, transactions, convert points |
| 19 | **Referral** | GET /referral | Code, link, stats, referred users list |
| 20 | **Analytics** | GET /analytics/links, GET /analytics/links/{id} | Link tracking stats |
| 21 | **Profile** | GET /auth/me, PUT /auth/profile, POST /auth/change-password | User info, edit, change password |
| 22 | **Admin Panel** | All /admin/* endpoints | Only visible for superadmin users |

---

## 8. IMPORTANT IMPLEMENTATION NOTES

### Date/Time
- Server uses Asia/Tehran timezone
- Post scheduling uses **Jalali (Persian/Solar) calendar** format: `YYYY/MM/DD`
- Server internally converts Jalali to Gregorian
- All `created_at`, `updated_at` etc. are in standard datetime format

### Text Processing
- All text content is in Persian/Farsi
- The app uses `TextFormat::en_num()` to convert Persian/Arabic numerals to Latin
- Persian half-space (نیم‌فاصله) is important in brand name: **پُست‌یار**

### Platform Values
Channels can be on these platforms: `telegram`, `eitaa`, `gap`, and potentially others.

### Post Status Flow
```
draft → queued → sent
                 → failed → (retry) → queued
       → scheduled → (cron) → sent / failed
       → cancelled (deleted)
```

### Payment Flow
```
User selects plan → Enters bank info → Uploads receipt → Status: pending
                                                          ↓
                                                   Admin approves → Status: approved
                                                   Admin rejects  → Status: rejected
                                                          ↓
                                              Subscription activated
```

### Push Notifications
The existing backend has Web Push infrastructure. For Android, FCM (Firebase Cloud Messaging) should be integrated later. The backend already supports broadcast notifications via `sendPushBroadcast()`.

---

## 9. ENDPOINT SUMMARY TABLE

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| POST | /auth/login | 🔓 | Login & get token |
| POST | /auth/register | 🔓 | Register & auto-login |
| POST | /auth/logout | 🔐 | Revoke token |
| GET | /auth/me | 🔐 | Current user + subscription |
| PUT | /auth/profile | 🔐 | Update profile |
| POST | /auth/change-password | 🔐 | Change password |
| POST | /auth/reset-password | 🔓 | Email reset request |
| POST | /auth/reset-password/confirm | 🔓 | Email reset confirm |
| POST | /auth/reset-password-sms | 🔓 | SMS reset request |
| POST | /auth/verify-sms-code | 🔓 | SMS code verify |
| GET | /bootstrap | 🔐 | Initial data load (ALL data) |
| GET | /sync | 🔐 | Periodic sync |
| GET | /plans | 🔓 | List plans |
| GET | /channels | 🔐 | List channels |
| POST | /channels | 🔐 | Add channel |
| GET | /channels/{id} | 🔐 | Get channel |
| PUT | /channels/{id} | 🔐 | Update channel |
| DELETE | /channels/{id} | 🔐 | Delete channel |
| GET | /posts | 🔐 | List posts |
| POST | /posts | 🔐 | Create post |
| GET | /posts/{id} | 🔐 | Get post |
| POST | /posts/{id}/cancel | 🔐 | Cancel post |
| POST | /posts/{id}/retry | 🔐 | Retry failed post |
| GET | /notifications | 🔐 | List notifications |
| POST | /notifications/{id}/read | 🔐 | Mark notification read |
| POST | /notifications/read-all | 🔐 | Mark all read |
| POST | /payments | 🔐 | Submit payment |
| GET | /payments | 🔐 | List payments |
| POST | /coupons/validate | 🔐 | Validate coupon |
| GET | /tickets | 🔐 | List tickets |
| POST | /tickets | 🔐 | Create ticket |
| GET | /tickets/{id} | 🔐 | Get ticket + replies |
| POST | /tickets/{id}/reply | 🔐 | Reply to ticket |
| GET | /settings | 🔐 | Get settings |
| POST | /settings/gold | 🔐 | Save gold settings |
| POST | /settings/gold/trigger | 🔐 | Trigger gold publish |
| PUT | /settings/advanced | 🔐 | Save advanced settings |
| GET | /auto-responder | 🔐 | List auto-replies |
| POST | /auto-responder | 🔐 | Add auto-reply |
| DELETE | /auto-responder/{id} | 🔐 | Delete auto-reply |
| POST | /auto-responder/toggle | 🔐 | Toggle responder |
| GET | /wallet | 🔐 | Wallet balance + transactions |
| POST | /wallet/convert-points | 🔐 | Convert points to wallet |
| GET | /referral | 🔐 | Referral info |
| GET | /analytics/links | 🔐 | Link stats |
| GET | /analytics/links/{id} | 🔐 | Link detail stats |
| GET | /admin/dashboard | 👑 | Admin dashboard |
| GET | /admin/users | 👑 | List users |
| POST | /admin/users/{id}/suspend | 👑 | Suspend user |
| POST | /admin/users/{id}/activate | 👑 | Activate user |
| GET | /admin/payments | 👑 | List all payments |
| POST | /admin/payments/{id}/approve | 👑 | Approve payment |
| GET | /admin/tickets | 👑 | List all tickets |
| POST | /admin/tickets/{id}/reply | 👑 | Admin reply ticket |
| GET | /admin/plans | 👑 | List plans |
| POST | /admin/plans | 👑 | Create plan |
| PUT | /admin/plans/{id} | 👑 | Update plan |
| DELETE | /admin/plans/{id} | 👑 | Delete plan |
| POST | /admin/broadcast | 👑 | Send push broadcast |
| POST | /admin/discounts | 👑 | Add discount |
| DELETE | /admin/discounts/{id} | 👑 | Delete discount |

**Total: 47 endpoints** (6 public, 35 authenticated, 6 superadmin)