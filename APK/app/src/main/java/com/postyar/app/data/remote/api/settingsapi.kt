package com.postyar.app.data.remote.api

import com.postyar.app.data.remote.dto.ApiResponse
import com.postyar.app.data.remote.dto.SettingsDto
import okhttp3.MultipartBody
import retrofit2.http.*

interface SettingsApi {

    @GET("settings")
    suspend fun get(): ApiResponse<SettingsDto>

    @Multipart
    @POST("settings/gold")
    suspend fun saveGold(
        @Part("gold_schedule") goldSchedule: String,
        @Part("gold_api_url") goldApiUrl: String,
        @Part("gold_currency") goldCurrency: String,
        @Part("gold_template") goldTemplate: String,
        @Part("gold_channels") goldChannels: String,
        @Part goldImage: MultipartBody.Part? = null
    ): ApiResponse<Nothing>

    @POST("settings/gold/trigger")
    suspend fun triggerGold(): ApiResponse<Nothing>

    @PUT("settings/advanced")
    @Headers("Content-Type: application/json")
    suspend fun saveAdvanced(@Body body: AdvancedSettingsRequest): ApiResponse<Nothing>
}

data class AdvancedSettingsRequest(
    val ai_provider: String? = null,
    val ai_api_key: String? = null,
    val ai_model: String? = null,
    val ai_api_url: String? = null,
    val auto_publish_woo: String? = null,
    val watermark_active: String? = null,
    val caption_format: String? = null,
    val inbound_method: String? = null,
    val poll_interval: String? = null
)