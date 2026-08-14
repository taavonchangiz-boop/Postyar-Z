package com.postyar.app.data.remote.dto

import com.squareup.moshi.Json
import com.squareup.moshi.JsonClass

@JsonClass(generateAdapter = true)
data class SettingsDto(
    @Json(name = "gold_schedule") val goldSchedule: String = "",
    @Json(name = "gold_api_url") val goldApiUrl: String = "",
    @Json(name = "gold_currency") val goldCurrency: String = "",
    @Json(name = "gold_template") val goldTemplate: String = "",
    @Json(name = "ai_provider") val aiProvider: String = "",
    @Json(name = "ai_api_key") val aiApiKey: String = "",
    @Json(name = "ai_model") val aiModel: String = "",
    @Json(name = "ai_api_url") val aiApiUrl: String = "",
    @Json(name = "auto_publish_woo") val autoPublishWoo: String = "0",
    @Json(name = "watermark_active") val watermarkActive: String = "0",
    @Json(name = "caption_format") val captionFormat: String = "",
    @Json(name = "inbound_method") val inboundMethod: String = "",
    @Json(name = "poll_interval") val pollInterval: String = ""
)