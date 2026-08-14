package com.postyar.app.data.remote.dto

import com.squareup.moshi.Json
import com.squareup.moshi.JsonClass

@JsonClass(generateAdapter = true)
data class LoginResponseDto(
    @Json(name = "token") val token: String,
    @Json(name = "user") val user: UserDto
)

@JsonClass(generateAdapter = true)
data class MeResponseDto(
    @Json(name = "user") val user: UserDto,
    @Json(name = "subscription") val subscription: SubscriptionDto? = null
)

@JsonClass(generateAdapter = true)
data class SubscriptionDto(
    @Json(name = "id") val id: Int,
    @Json(name = "plan_id") val planId: Int,
    @Json(name = "plan_title") val planTitle: String,
    @Json(name = "plan_price") val planPrice: Double,
    @Json(name = "max_channels") val maxChannels: Int,
    @Json(name = "max_posts") val maxPosts: Int,
    @Json(name = "features") val features: List<String> = emptyList(),
    @Json(name = "start_date") val startDate: String,
    @Json(name = "end_date") val endDate: String,
    @Json(name = "status") val status: String
)