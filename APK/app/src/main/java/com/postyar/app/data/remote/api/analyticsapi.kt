package com.postyar.app.data.remote.api

import com.postyar.app.data.remote.dto.AnalyticsLinkDetailDto
import com.postyar.app.data.remote.dto.AnalyticsLinkDto
import com.postyar.app.data.remote.dto.ApiResponse
import retrofit2.http.GET
import retrofit2.http.Path

interface AnalyticsApi {

    @GET("analytics/links")
    suspend fun listLinks(): ApiResponse<List<AnalyticsLinkDto>>

    @GET("analytics/links/{id}")
    suspend fun linkDetail(@Path("id") id: Int): ApiResponse<AnalyticsLinkDetailDto>
}