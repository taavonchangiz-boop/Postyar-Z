package com.postyar.app.data.remote.dto

import com.squareup.moshi.Json
import com.squareup.moshi.JsonClass

@JsonClass(generateAdapter = true)
data class AutoReplyDto(
    @Json(name = "id") val id: Int,
    @Json(name = "channel_id") val channelId: Int,
    @Json(name = "keyword") val keyword: String,
    @Json(name = "reply_text") val replyText: String,
    @Json(name = "active") val active: Int = 1,
    @Json(name = "channel_name") val channelName: String? = null,
    @Json(name = "channel_platform") val channelPlatform: String? = null
)