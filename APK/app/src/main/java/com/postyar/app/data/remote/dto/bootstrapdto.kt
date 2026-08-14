package com.postyar.app.data.remote.dto

import com.squareup.moshi.Json
import com.squareup.moshi.JsonClass

@JsonClass(generateAdapter = true)
data class AnnouncementDto(
    @Json(name = "id") val id: Int,
    @Json(name = "title") val title: String,
    @Json(name = "message") val message: String
)

@JsonClass(generateAdapter = true)
data class ReferralInfoDto(
    @Json(name = "code") val code: String,
    @Json(name = "total") val total: Int
)

@JsonClass(generateAdapter = true)
data class TicketCategoryDto(
    @Json(name = "id") val id: Int,
    @Json(name = "name") val name: String,
    @Json(name = "sort_order") val sortOrder: Int = 0
)

@JsonClass(generateAdapter = true)
data class BootstrapDto(
    @Json(name = "user") val user: UserDto,
    @Json(name = "quota") val quota: QuotaDto,
    @Json(name = "channels") val channels: List<ChannelDto>,
    @Json(name = "posts") val posts: List<PostDto>,
    @Json(name = "notifications") val notifications: List<NotificationDto>,
    @Json(name = "unread_count") val unreadCount: Int,
    @Json(name = "auto_replies") val autoReplies: List<AutoReplyDto>,
    @Json(name = "inbox") val inbox: List<InboxMessageDto>,
    @Json(name = "tickets") val tickets: List<TicketDto>,
    @Json(name = "plans") val plans: List<PlanDto>,
    @Json(name = "offers") val offers: List<DiscountOfferDto>,
    @Json(name = "subscription_history") val subscriptionHistory: List<SubscriptionDto>,
    @Json(name = "payment_history") val paymentHistory: List<PaymentDto>,
    @Json(name = "settings") val settings: BootstrapSettingsDto,
    @Json(name = "responder_settings") val responderSettings: Map<String, String> = emptyMap(),
    @Json(name = "ticket_categories") val ticketCategories: List<TicketCategoryDto>,
    @Json(name = "announcement") val announcement: AnnouncementDto? = null,
    @Json(name = "announcement_unread") val announcementUnread: Boolean = false,
    @Json(name = "referral_info") val referralInfo: ReferralInfoDto,
    @Json(name = "wallet_balance") val walletBalance: Double
)

@JsonClass(generateAdapter = true)
data class InboxMessageDto(
    @Json(name = "id") val id: Int,
    @Json(name = "channel_id") val channelId: Int? = null,
    @Json(name = "sender_id") val senderId: String? = null,
    @Json(name = "text") val text: String? = null,
    @Json(name = "created_at") val createdAt: String? = null
)

@JsonClass(generateAdapter = true)
data class DiscountOfferDto(
    @Json(name = "id") val id: Int,
    @Json(name = "code") val code: String? = null,
    @Json(name = "discount_percent") val discountPercent: Int = 0,
    @Json(name = "plan_id") val planId: Int? = null,
    @Json(name = "expires_at") val expiresAt: String? = null
)

@JsonClass(generateAdapter = true)
data class BootstrapSettingsDto(
    @Json(name = "gold_schedule") val goldSchedule: String = "",
    @Json(name = "gold_api_url") val goldApiUrl: String = "",
    @Json(name = "ai_provider") val aiProvider: String = "",
    @Json(name = "caption_format") val captionFormat: String = ""
)

@JsonClass(generateAdapter = true)
data class NotificationsWrapperDto(
    @Json(name = "notifications") val notifications: List<NotificationDto>,
    @Json(name = "unread_count") val unreadCount: Int
)