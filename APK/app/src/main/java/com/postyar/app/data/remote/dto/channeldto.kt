package com.postyar.app.data.remote.dto

import com.squareup.moshi.Json
import com.squareup.moshi.JsonClass

@JsonClass(generateAdapter = true)
data class ChannelDto(
    @Json(name = "id") val id: Int,
    @Json(name = "tenant_id") val tenantId: Int,
    @Json(name = "name") val name: String,
    @Json(name = "platform") val platform: String,
    @Json(name = "channel_id") val channelId: String,
    @Json(name = "token") val token: String? = null,
    @Json(name = "link_config") val linkConfig: String? = null,
    @Json(name = "button_config") val buttonConfig: String? = null,
    @Json(name = "webhook_active") val webhookActive: Int = 0,
    @Json(name = "created_at") val createdAt: String
)