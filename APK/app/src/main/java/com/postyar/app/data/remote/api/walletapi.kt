package com.postyar.app.data.remote.api

import com.postyar.app.data.remote.dto.*
import retrofit2.http.*

interface WalletApi {

    @GET("wallet")
    suspend fun get(): ApiResponse<WalletDto>

    @POST("wallet/convert-points")
    @Headers("Content-Type: application/json")
    suspend fun convertPoints(@Body body: ConvertPointsRequest): ApiResponse<ConvertPointsDto>
}

data class ConvertPointsRequest(
    val points: Int
)