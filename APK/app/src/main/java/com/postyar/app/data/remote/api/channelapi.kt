package com.postyar.app.data.remote.api

import com.postyar.app.data.remote.dto.ApiResponse
import com.postyar.app.data.remote.dto.ChannelDto
import retrofit2.http.*

interface ChannelApi {

    @GET("channels")
    suspend fun list(): ApiResponse<List<ChannelDto>>

    @GET("channels/{id}")
    suspend fun get(@Path("id") id: Int): ApiResponse<ChannelDto>

    @POST("channels")
    @Headers("Content-Type: application/json")
    suspend fun create(@Body body: CreateChannelRequest): ApiResponse<ChannelDto>

    @PUT("channels/{id}")
    @Headers("Content-Type: application/json")
    suspend fun update(@Path("id") id: Int, @Body body: UpdateChannelRequest): ApiResponse<ChannelDto>

    @DELETE("channels/{id}")
    suspend fun delete(@Path("id") id: Int): ApiResponse<Nothing>
}

data class CreateChannelRequest(
    val name: String,
    val platform: String,
    val channel_id: String,
    val token: String? = null
)

data class UpdateChannelRequest(
    val name: String? = null,
    val platform: String? = null,
    val channel_id_val: String? = null,
    val token: String? = null,
    val link_name_1: String? = null,
    val link_url_1: String? = null,
    val link_name_2: String? = null,
    val link_url_2: String? = null,
    val link_name_3: String? = null,
    val link_url_3: String? = null,
    val buttons_active: Boolean? = null,
    val btn_text_1: String? = null,
    val btn_url_1: String? = null,
    val btn_text_2: String? = null,
    val btn_url_2: String? = null
)