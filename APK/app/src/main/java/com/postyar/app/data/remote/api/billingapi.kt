package com.postyar.app.data.remote.api

import com.postyar.app.data.remote.dto.*
import okhttp3.MultipartBody
import retrofit2.http.*

interface BillingApi {

    @GET("plans")
    suspend fun listPlans(): ApiResponse<List<PlanDto>>

    @Multipart
    @POST("payments")
    suspend fun submitPayment(
        @Part("plan_id") planId: Int,
        @Part("amount") amount: String,
        @Part("reference_num") referenceNum: String,
        @Part receiptPhoto: MultipartBody.Part? = null
    ): ApiResponse<PaymentDto>

    @GET("payments")
    suspend fun listPayments(): ApiResponse<List<PaymentDto>>

    @POST("coupons/validate")
    @Headers("Content-Type: application/json")
    suspend fun validateCoupon(@Body body: ValidateCouponRequest): ApiResponse<CouponDto>
}

data class ValidateCouponRequest(
    val code: String,
    val plan_id: Int
)