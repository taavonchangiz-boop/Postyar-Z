package com.postyar.app.data.remote.dto

import com.squareup.moshi.Json
import com.squareup.moshi.JsonClass

@JsonClass(generateAdapter = true)
data class SyncDto(
    @Json(name = "server_time") val serverTime: String,
    @Json(name = "notifications") val notifications: List<NotificationDto>,
    @Json(name = "unread_count") val unreadCount: Int,
    @Json(name = "channels") val channels: List<ChannelDto>,
    @Json(name = "recent_posts") val recentPosts: List<PostDto>,
    @Json(name = "quota") val quota: QuotaDto,
    @Json(name = "wallet_balance") val walletBalance: Double
)