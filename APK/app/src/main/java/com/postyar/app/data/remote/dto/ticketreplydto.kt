package com.postyar.app.data.remote.dto

import com.squareup.moshi.Json
import com.squareup.moshi.JsonClass

@JsonClass(generateAdapter = true)
data class TicketReplyDto(
    @Json(name = "id") val id: Int,
    @Json(name = "ticket_id") val ticketId: Int,
    @Json(name = "user_id") val userId: Int? = null,
    @Json(name = "sender_name") val senderName: String,
    @Json(name = "message") val message: String,
    @Json(name = "created_at") val createdAt: String
)