package com.postyar.app.data.remote.dto

import com.squareup.moshi.Json
import com.squareup.moshi.JsonClass

@JsonClass(generateAdapter = true)
data class ReferralUserDto(
    @Json(name = "id") val id: Int,
    @Json(name = "referred_name") val referredName: String,
    @Json(name = "referred_email") val referredEmail: String,
    @Json(name = "status") val status: String,
    @Json(name = "created_at") val createdAt: String
)

@JsonClass(generateAdapter = true)
data class ReferralDto(
    @Json(name = "code") val code: String,
    @Json(name = "link") val link: String,
    @Json(name = "stats") val stats: ReferralStatsDto,
    @Json(name = "referrals") val referrals: List<ReferralUserDto>,
    @Json(name = "referral_points") val referralPoints: Double
)

@JsonClass(generateAdapter = true)
data class ReferralStatsDto(
    @Json(name = "total") val total: Int
)