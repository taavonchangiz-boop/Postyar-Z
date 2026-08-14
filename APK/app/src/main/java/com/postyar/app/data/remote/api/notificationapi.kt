package com.postyar.app.data.remote.api

import com.postyar.app.data.remote.dto.*
import retrofit2.http.*

interface NotificationApi {

    @GET("notifications")
    suspend fun list(
        @Query("limit") limit: Int = 20,
        @Query("offset") offset: Int = 0
    ): ApiResponse<NotificationsWrapperDto>

    @POST("notifications/{id}/read")
    suspend fun markRead(@Path("id") id: Int): ApiResponse<EmptyData>

    @POST("notifications/read-all")
    suspend fun markAllRead(): ApiResponse<MarkedCountData>
}