package com.postyar.app.domain

import com.google.gson.annotations.SerializedName
import java.io.Serializable

enum class AuthState {
    IDLE, LOADING, AUTHENTICATED, UNAUTHENTICATED
}

enum class AuthProvider {
    EMAIL, SMS
}

data class ApiResponse<T>(
    val success: Boolean,
    val message: String? = null,
    val data: T? = null
)

data class User(
    val id: Int,
    val name: String,
    val email: String,
    val role: String,
    val status: String,
    val business_name: String? = null,
    val business_type: String? = null,
    val phone: String? = null,
    val birthday: String? = null,
    val referral_code: String? = null,
    val referral_points: Double = 0.0,
    val wallet_balance: Double = 0.0,
    val created_at: String? = null
) : Serializable

data class Subscription(
    val id: Int,
    val plan_id: Int,
    val plan_title: String,
    val plan_price: Double,
    val max_channels: Int,
    val max_posts: Int,
    val features: List<String> = emptyList(),
    val start_date: String? = null,
    val end_date: String? = null,
    val status: String
)

data class AuthResponse(
    val token: String,
    val user: User
)

data class MeResponse(
    val user: User,
    val subscription: Subscription?
)

data class Quota(
    val can_send_post: Boolean,
    val posts_used: Int,
    val posts_limit: Int,
    val channels_used: Int,
    val channels_limit: Int,
    val posts_remaining: Int,
    val channels_remaining: Int
)

data class Channel(
    val id: Int,
    val tenant_id: Int,
    val name: String,
    val platform: String,
    val channel_id: String,
    val token: String,
    val link_config: String? = null,
    val button_config: String? = null,
    val webhook_active: Int = 0,
    val created_at: String? = null,
    // UI-only fields from auto-responder endpoint
    var channel_name: String? = null,
    var channel_platform: String? = null
) : Serializable

data class Post(
    val id: Int,
    val tenant_id: Int,
    val title: String,
    val content: String,
    val media_url: String? = null,
    val status: String,
    val scheduled_at: String? = null,
    val target_channels: String? = null,
    val created_at: String? = null,
    val click_count: Int = 0
) : Serializable

data class NotificationItem(
    val id: Int,
    val user_id: Int,
    val type: String,
    val title: String,
    val message: String,
    val target_section: String? = null,
    val is_read: Int = 0,
    val created_at: String? = null
) : Serializable

data class NotificationListData(
    val notifications: List<NotificationItem>,
    val unread_count: Int
)

data class Plan(
    val id: Int,
    val title: String,
    val price: Double,
    val duration_days: Int,
    val max_channels: Int,
    val max_posts: Int,
    val features: List<String> = emptyList(),
    val payment_url: String? = null,
    val image_url: String? = null,
    val description: String? = null,
    val early_renewal_discount: Int = 0,
    val general_discount: Int = 0,
    val discount_badge_text: String? = null,
    val is_featured: Int = 0,
    val created_at: String? = null
) : Serializable

data class Payment(
    val id: Int,
    val user_id: Int,
    val plan_id: Int,
    val amount: Double,
    val discount_code_id: Int? = null,
    val payment_method: String = "card_to_card",
    val receipt_photo: String? = null,
    val reference_num: String? = null,
    val status: String,
    val admin_notes: String? = null,
    val verified_at: String? = null,
    val created_at: String? = null,
    val plan_title: String? = null
) : Serializable

data class Ticket(
    val id: Int,
    val user_id: Int,
    val subject: String,
    val category: String,
    val message: String,
    val status: String,
    val attachment: String? = null,
    val assigned_to: Int? = null,
    val created_at: String? = null,
    val replies_count: Int = 0
) : Serializable

data class TicketDetail(
    val ticket: Ticket,
    val replies: List<TicketReply>
)

data class TicketReply(
    val id: Int,
    val ticket_id: Int,
    val user_id: Int? = null,
    val sender_name: String,
    val message: String,
    val created_at: String? = null
) : Serializable

data class Settings(
    val gold_schedule: String = "",
    val gold_api_url: String = "",
    val gold_currency: String = "",
    val gold_template: String = "",
    val ai_provider: String = "openai",
    val ai_api_key: String = "",
    val ai_model: String = "gpt-4",
    val ai_api_url: String = "",
    val caption_format: String = "",
    val watermark_active: String = "0",
    val auto_publish_woo: String = "0",
    val inbound_method: String = "webhook",
    val poll_interval: String = "60"
)

data class AutoReplyRule(
    val id: Int,
    val tenant_id: Int,
    val channel_id: Int,
    val keyword: String,
    val reply_text: String,
    val active: Int = 1,
    var channel_name: String? = null,
    var channel_platform: String? = null
) : Serializable

data class WalletData(
    val balance: Double,
    val transactions: List<WalletTransaction>
)

data class WalletTransaction(
    val id: Int,
    val user_id: Int,
    val type: String,
    val amount: Double,
    val balance_after: Double,
    val description: String,
    val reference_type: String? = null,
    val reference_id: Int? = null,
    val created_at: String? = null
) : Serializable

data class ConvertPointsResult(
    val new_balance: Double,
    val converted: Int,
    val wallet_amount: Double
)

data class ReferralData(
    val code: String,
    val link: String,
    val stats: ReferralStats,
    val referrals: List<ReferredUser>,
    val referral_points: Double = 0.0
)

data class ReferralStats(val total: Int)

data class ReferredUser(
    val id: Int,
    val referred_name: String,
    val referred_email: String,
    val status: String,
    val created_at: String? = null
) : Serializable

data class LinkTracking(
    val id: Int,
    val title: String? = null,
    val original_url: String,
    val short_code: String,
    val total_clicks: Int,
    val unique_clicks: Int,
    val created_at: String? = null
) : Serializable

data class LinkDetail(
    val link: LinkTracking,
    val daily_breakdown: List<DailyClickStat>
)

data class DailyClickStat(
    val date: String,
    val clicks: Int,
    val unique_clicks: Int
)

data class CouponValidation(
    val id: Int,
    val code: String,
    val type: String,
    val amount: Double,
    val max_uses: Int,
    val used: Int,
    val expires_at: String? = null
)

// Admin models
data class AdminDashboard(
    val users: UserStats,
    val payments: PaymentStats,
    val tickets: TicketStats,
    val recent_users: List<User>
)

data class UserStats(val total: Int, val active: Int, val suspended: Int)
data class PaymentStats(val total: Int, val amount: Double, val pending: Int, val approved: Int)
data class TicketStats(val total: Int, val open: Int)

// Bootstrap response
data class BootstrapData(
    val user: User,
    val quota: Quota,
    val channels: List<Channel>,
    val posts: List<Post>,
    val notifications: List<NotificationItem>,
    val unread_count: Int,
    val auto_replies: List<AutoReplyRule>,
    val inbox: List<Any>,
    val tickets: List<Ticket>,
    val plans: List<Plan>,
    val offers: List<Any>,
    val subscription_history: List<Subscription>,
    val payment_history: List<Payment>,
    val settings: Settings,
    val responder_settings: Map<String, String>? = null,
    val ticket_categories: List<TicketCategory>,
    val announcement: Announcement? = null,
    val announcement_unread: Boolean = false,
    val referral_info: ReferralInfo,
    val wallet_balance: Double
)

data class ReferralInfo(val code: String, val total: Int)
data class TicketCategory(val id: Int, val name: String, val sort_order: Int)
data class Announcement(val id: Int, val title: String, val message: String)

// Sync response
data class SyncData(
    val server_time: String,
    val notifications: List<NotificationItem>,
    val unread_count: Int,
    val channels: List<Channel>,
    val recent_posts: List<Post>,
    val quota: Quota,
    val wallet_balance: Double
)