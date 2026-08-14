package com.postyar.app.data.remote.dto

import com.squareup.moshi.Json
import com.squareup.moshi.JsonClass

@JsonClass(generateAdapter = true)
data class UserDto(
    @Json(name = "id") val id: Int,
    @Json(name = "name") val name: String,
    @Json(name = "email") val email: String,
    @Json(name = "role") val role: String,
    @Json(name = "status") val status: String,
    @Json(name = "business_name") val businessName: String? = null,
    @Json(name = "business_type") val businessType: String? = null,
    @Json(name = "phone") val phone: String? = null,
    @Json(name = "birthday") val birthday: String? = null,
    @Json(name = "referral_code") val referralCode: String? = null,
    @Json(name = "referral_points") val referralPoints: Double = 0.0,
    @Json(name = "wallet_balance") val walletBalance: Double = 0.0,
    @Json(name = "created_at") val createdAt: String
)