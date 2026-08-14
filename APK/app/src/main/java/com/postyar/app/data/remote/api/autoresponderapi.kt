package com.postyar.app.data.remote.api

import com.postyar.app.data.remote.dto.ApiResponse
import com.postyar.app.data.remote.dto.AutoReplyDto
import retrofit2.http.*

interface AutoResponderApi {

    @GET("auto-responder")
    suspend fun list(): ApiResponse<List<AutoReplyDto>>

    @POST("auto-responder")
    @Headers("Content-Type: application/json")
    suspend fun add(@Body body: AddAutoReplyRequest): ApiResponse<AutoReplyDto>

    @DELETE("auto-responder/{id}")
    suspend fun delete(@Path("id") id: Int): ApiResponse<Nothing>

    @POST("auto-responder/toggle")
    @Headers("Content-Type: application/json")
    suspend fun toggle(@Body body: ToggleAutoReplyRequest): ApiResponse<Nothing>
}

data class AddAutoReplyRequest(
    val channel_id: Int,
    val keyword: String,
    val reply_text: String
)

data class ToggleAutoReplyRequest(
    val channel_id: Int,
    val enabled: Int
)