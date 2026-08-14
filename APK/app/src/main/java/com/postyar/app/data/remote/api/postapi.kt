package com.postyar.app.data.remote.api

import com.postyar.app.data.remote.dto.ApiResponse
import com.postyar.app.data.remote.dto.PostDto
import okhttp3.MultipartBody
import retrofit2.http.*

interface PostApi {

    @GET("posts")
    suspend fun list(
        @Query("status") status: String? = null,
        @Query("limit") limit: Int = 50,
        @Query("offset") offset: Int = 0
    ): ApiResponse<List<PostDto>>

    @GET("posts/{id}")
    suspend fun get(@Path("id") id: Int): ApiResponse<PostDto>

    @Multipart
    @POST("posts")
    suspend fun create(
        @Part("title") title: String,
        @Part("content") content: String,
        @Part("send_type") sendType: String,
        @Part("post_channels") postChannels: String,
        @Part("caption_format") captionFormat: String = "",
        @Part("sched_date") schedDate: String? = null,
        @Part("sched_hour") schedHour: String? = null,
        @Part("sched_minute") schedMinute: String? = null,
        @Part mediaFile: MultipartBody.Part? = null
    ): ApiResponse<PostDto>

    @POST("posts/{id}/cancel")
    suspend fun cancel(@Path("id") id: Int): ApiResponse<Nothing>

    @POST("posts/{id}/retry")
    suspend fun retry(@Path("id") id: Int): ApiResponse<Nothing>
}
