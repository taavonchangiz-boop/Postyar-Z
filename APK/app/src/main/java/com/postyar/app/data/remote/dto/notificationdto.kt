package com.postyar.app.data.remote.dto

import com.squareup.moshi.Json
import com.squareup.moshi.JsonClass

@JsonClass(generateAdapter = true)
data class NotificationDto(
    @Json(name = "id") val id: Int,
    @Json(name = "user_id") val userId: Int,
    @Json(name = "type") val type: String,
    @Json(name = "title") val title: String,
    @Json(name = "message") val message: String,
    @Json(name = "target_section") val targetSection: String? = null,
    @Json(name = "is_read") val isRead: Int = 0,
    @Json(name = "created_at") val createdAt: String
)