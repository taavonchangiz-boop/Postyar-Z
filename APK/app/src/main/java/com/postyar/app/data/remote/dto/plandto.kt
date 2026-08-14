package com.postyar.app.data.remote.dto

import com.squareup.moshi.Json
import com.squareup.moshi.JsonClass

@JsonClass(generateAdapter = true)
data class PlanDto(
    @Json(name = "id") val id: Int,
    @Json(name = "title") val title: String,
    @Json(name = "price") val price: Double,
    @Json(name = "duration_days") val durationDays: Int,
    @Json(name = "max_channels") val maxChannels: Int,
    @Json(name = "max_posts") val maxPosts: Int,
    @Json(name = "features") val features: List<String> = emptyList(),
    @Json(name = "payment_url") val paymentUrl: String? = null,
    @Json(name = "image_url") val imageUrl: String? = null,
    @Json(name = "description") val description: String? = null,
    @Json(name = "early_renewal_discount") val earlyRenewalDiscount: Int = 0,
    @Json(name = "general_discount") val generalDiscount: Int = 0,
    @Json(name = "discount_badge_text") val discountBadgeText: String? = null,
    @Json(name = "is_featured") val isFeatured: Int = 0,
    @Json(name = "created_at") val createdAt: String? = null
)