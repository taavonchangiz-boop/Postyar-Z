package com.postyar.app.data.remote.dto

import com.squareup.moshi.Json
import com.squareup.moshi.JsonClass

@JsonClass(generateAdapter = true)
data class ApiResponse<T>(
    @Json(name = "success") val success: Boolean,
    @Json(name = "message") val message: String? = null,
    @Json(name = "data") val data: T? = null,
    @Json(name = "errors") val errors: Map<String, String>? = null
)

@JsonClass(generateAdapter = true)
data class EmptyData(@Json(name = "remaining_unread") val remainingUnread: Int? = null)

@JsonClass(generateAdapter = true)
data class MarkedCountData(@Json(name = "marked_count") val markedCount: Int? = null)