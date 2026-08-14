package com.postyar.app.data.remote.api

import com.postyar.app.data.remote.dto.*
import okhttp3.MultipartBody
import retrofit2.http.*

interface TicketApi {

    @GET("tickets")
    suspend fun list(): ApiResponse<List<TicketDto>>

    @Multipart
    @POST("tickets")
    suspend fun create(
        @Part("subject") subject: String,
        @Part("category") category: String,
        @Part("message") message: String,
        @Part attachment: MultipartBody.Part? = null
    ): ApiResponse<TicketDto>

    @GET("tickets/{id}")
    suspend fun get(@Path("id") id: Int): ApiResponse<TicketDetailDto>

    @Multipart
    @POST("tickets/{id}/reply")
    suspend fun reply(
        @Path("id") id: Int,
        @Part("message") message: String,
        @Part("close_after_reply") closeAfterReply: String = "false",
        @Part attachment: MultipartBody.Part? = null
    ): ApiResponse<TicketReplyDto>
}