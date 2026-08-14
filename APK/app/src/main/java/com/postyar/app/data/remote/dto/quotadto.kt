package com.postyar.app.data.remote.dto

import com.squareup.moshi.Json
import com.squareup.moshi.JsonClass

@JsonClass(generateAdapter = true)
data class QuotaDto(
    @Json(name = "can_send_post") val canSendPost: Boolean,
    @Json(name = "posts_used") val postsUsed: Int,
    @Json(name = "posts_limit") val postsLimit: Int,
    @Json(name = "channels_used") val channelsUsed: Int,
    @Json(name = "channels_limit") val channelsLimit: Int,
    @Json(name = "posts_remaining") val postsRemaining: Int,
    @Json(name = "channels_remaining") val channelsRemaining: Int
)