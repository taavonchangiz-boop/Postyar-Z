package com.postyar.app.data.remote.api

import com.postyar.app.data.remote.dto.ApiResponse
import com.postyar.app.data.remote.dto.ReferralDto
import retrofit2.http.GET

interface ReferralApi {

    @GET("referral")
    suspend fun get(): ApiResponse<ReferralDto>
}