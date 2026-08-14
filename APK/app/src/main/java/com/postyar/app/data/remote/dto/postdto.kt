package com.postyar.app.data.remote.dto

import com.squareup.moshi.Json
import com.squareup.moshi.JsonClass

@JsonClass(generateAdapter = true)
data class PostDto(
    @Json(name = "id") val id: Int,
    @Json(name = "tenant_id") val tenantId: Int,
    @Json(name = "title") val title: String,
    @Json(name = "content") val content: String,
    @Json(name = "media_url") val mediaUrl: String? = null,
    @Json(name = "status") val status: String,
    @Json(name = "scheduled_at") val scheduledAt: String? = null,
    @Json(name = "target_channels") val targetChannels: String? = null,
    @Json(name = "created_at") val createdAt: String,
    @Json(name = "click_count") val clickCount: Int = 0
)