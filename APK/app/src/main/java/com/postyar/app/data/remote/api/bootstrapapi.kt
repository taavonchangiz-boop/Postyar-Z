package com.postyar.app.data.remote.api

import com.postyar.app.data.remote.dto.ApiResponse
import com.postyar.app.data.remote.dto.BootstrapDto
import com.postyar.app.data.remote.dto.SyncDto
import retrofit2.http.GET
import retrofit2.http.Query

interface BootstrapApi {

    @GET("bootstrap")
    suspend fun bootstrap(): ApiResponse<BootstrapDto>

    @GET("sync")
    suspend fun sync(@Query("since") since: Long? = null): ApiResponse<SyncDto>
}