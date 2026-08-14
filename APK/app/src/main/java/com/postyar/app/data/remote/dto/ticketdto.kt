package com.postyar.app.data.remote.dto

import com.squareup.moshi.Json
import com.squareup.moshi.JsonClass

@JsonClass(generateAdapter = true)
data class TicketDto(
    @Json(name = "id") val id: Int,
    @Json(name = "user_id") val userId: Int,
    @Json(name = "subject") val subject: String,
    @Json(name = "category") val category: String,
    @Json(name = "message") val message: String,
    @Json(name = "status") val status: String,
    @Json(name = "attachment") val attachment: String? = null,
    @Json(name = "replies_count") val repliesCount: Int = 0,
    @Json(name = "created_at") val createdAt: String
)

@JsonClass(generateAdapter = true)
data class TicketDetailDto(
    @Json(name = "ticket") val ticket: TicketDto,
    @Json(name = "replies") val replies: List<TicketReplyDto>
)