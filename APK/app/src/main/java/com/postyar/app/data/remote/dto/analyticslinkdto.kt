package com.postyar.app.data.remote.dto

import com.squareup.moshi.Json
import com.squareup.moshi.JsonClass

@JsonClass(generateAdapter = true)
data class AnalyticsLinkDto(
    @Json(name = "id") val id: Int,
    @Json(name = "title") val title: String? = null,
    @Json(name = "original_url") val originalUrl: String? = null,
    @Json(name = "short_code") val shortCode: String? = null,
    @Json(name = "total_clicks") val totalClicks: Int = 0,
    @Json(name = "unique_clicks") val uniqueClicks: Int = 0,
    @Json(name = "created_at") val createdAt: String? = null
)

@JsonClass(generateAdapter = true)
data class DailyBreakdownDto(
    @Json(name = "date") val date: String,
    @Json(name = "clicks") val clicks: Int,
    @Json(name = "unique_clicks") val uniqueClicks: Int
)

@JsonClass(generateAdapter = true)
data class AnalyticsLinkDetailDto(
    @Json(name = "link") val link: AnalyticsLinkDto,
    @Json(name = "daily_breakdown") val dailyBreakdown: List<DailyBreakdownDto>
)