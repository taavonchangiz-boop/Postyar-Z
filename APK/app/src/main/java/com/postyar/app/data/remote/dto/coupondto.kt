package com.postyar.app.data.remote.dto

import com.squareup.moshi.Json
import com.squareup.moshi.JsonClass

@JsonClass(generateAdapter = true)
data class CouponDto(
    @Json(name = "id") val id: Int,
    @Json(name = "code") val code: String,
    @Json(name = "type") val type: String,
    @Json(name = "amount") val amount: Double,
    @Json(name = "max_uses") val maxUses: Int,
    @Json(name = "used") val used: Int,
    @Json(name = "expires_at") val expiresAt: String
)