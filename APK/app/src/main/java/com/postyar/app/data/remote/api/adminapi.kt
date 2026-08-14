package com.postyar.app.data.remote.api

import com.postyar.app.data.remote.dto.*
import retrofit2.http.*

interface AdminApi {

    @GET("admin/dashboard")
    suspend fun dashboard(): ApiResponse<AdminDashboardDto>

    @GET("admin/users")
    suspend fun listUsers(
        @Query("status") status: String? = null,
        @Query("search") search: String? = null,
        @Query("limit") limit: Int = 50,
        @Query("offset") offset: Int = 0
    ): ApiResponse<List<UserDto>>

    @POST("admin/users/{id}/suspend")
    suspend fun suspendUser(@Path("id") id: Int): ApiResponse<Nothing>

    @POST("admin/users/{id}/activate")
    suspend fun activateUser(@Path("id") id: Int): ApiResponse<Nothing>

    @GET("admin/payments")
    suspend fun listPayments(): ApiResponse<List<PaymentDto>>

    @POST("admin/payments/{id}/approve")
    suspend fun approvePayment(@Path("id") id: Int): ApiResponse<Nothing>

    @GET("admin/tickets")
    suspend fun listTickets(): ApiResponse<List<TicketDto>>

    @POST("admin/tickets/{id}/reply")
    @Headers("Content-Type: application/json")
    suspend fun replyTicket(
        @Path("id") id: Int,
        @Body body: AdminTicketReplyRequest
    ): ApiResponse<Nothing>

    @GET("admin/plans")
    suspend fun listPlans(): ApiResponse<List<PlanDto>>

    @POST("admin/plans")
    @Headers("Content-Type: application/json")
    suspend fun createPlan(@Body body: PlanManageRequest): ApiResponse<PlanDto>

    @PUT("admin/plans/{id}")
    @Headers("Content-Type: application/json")
    suspend fun updatePlan(
        @Path("id") id: Int,
        @Body body: PlanManageRequest
    ): ApiResponse<PlanDto>

    @DELETE("admin/plans/{id}")
    suspend fun deletePlan(@Path("id") id: Int): ApiResponse<Nothing>

    @POST("admin/broadcast")
    @Headers("Content-Type: application/json")
    suspend fun broadcast(@Body body: BroadcastRequest): ApiResponse<Nothing>

    @POST("admin/discounts")
    @Headers("Content-Type: application/json")
    suspend fun addDiscount(@Body body: DiscountManageRequest): ApiResponse<Nothing>

    @DELETE("admin/discounts/{id}")
    suspend fun deleteDiscount(@Path("id") id: Int): ApiResponse<Nothing>
}

data class AdminDashboardDto(
    val users: AdminUsersStatsDto,
    val payments: AdminPaymentsStatsDto,
    val tickets: AdminTicketsStatsDto,
    val recent_users: List<UserDto>
)

data class AdminUsersStatsDto(
    val total: Int,
    val active: Int,
    val suspended: Int
)

data class AdminPaymentsStatsDto(
    val total: Int,
    val amount: Double,
    val pending: Int,
    val approved: Int
)

data class AdminTicketsStatsDto(
    val total: Int,
    val open: Int
)

data class AdminTicketReplyRequest(
    val message: String
)

data class PlanManageRequest(
    val title: String,
    val price: Double,
    val duration_days: Int,
    val max_channels: Int,
    val max_posts: Int,
    val features: List<String> = emptyList(),
    val description: String? = null,
    val early_renewal_discount: Int = 0,
    val general_discount: Int = 0,
    val discount_badge_text: String? = null,
    val is_featured: Int = 0
)

data class BroadcastRequest(
    val title: String,
    val message: String
)

data class DiscountManageRequest(
    val code: String,
    val type: String,
    val amount: Double,
    val max_uses: Int,
    val expires_at: String
)