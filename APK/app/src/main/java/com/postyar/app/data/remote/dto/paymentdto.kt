package com.postyar.app.data.remote.dto

import com.squareup.moshi.Json
import com.squareup.moshi.JsonClass

@JsonClass(generateAdapter = true)
data class PaymentDto(
    @Json(name = "id") val id: Int,
    @Json(name = "user_id") val userId: Int,
    @Json(name = "plan_id") val planId: Int? = null,
    @Json(name = "amount") val amount: Double,
    @Json(name = "plan_title") val planTitle: String? = null,
    @Json(name = "reference_num") val referenceNum: String? = null,
    @Json(name = "receipt_photo") val receiptPhoto: String? = null,
    @Json(name = "status") val status: String,
    @Json(name = "admin_notes") val adminNotes: String? = null,
    @Json(name = "created_at") val createdAt: String
)