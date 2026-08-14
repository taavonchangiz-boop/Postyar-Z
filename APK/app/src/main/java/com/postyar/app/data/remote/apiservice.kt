package com.postyar.app.data.remote

import com.postyar.app.domain.*
import okhttp3.MultipartBody
import retrofit2.http.*

interface ApiService {

    // Auth
    @POST("auth/login")
    suspend fun login(@Body body: LoginRequest): ApiResponse<AuthResponse>

    @POST("auth/register")
    suspend fun register(@Body body: RegisterRequest): ApiResponse<AuthResponse>

    @POST("auth/logout")
    suspend fun logout(): ApiResponse<Any?>

    @GET("auth/me")
    suspend fun getMe(): ApiResponse<MeResponse>

    @PUT("auth/profile")
    suspend fun updateProfile(@Body body: ProfileRequest): ApiResponse<User>

    @POST("auth/change-password")
    suspend fun changePassword(@Body body: ChangePasswordRequest): ApiResponse<Any?>

    @POST("auth/reset-password")
    suspend fun resetPassword(@Body body: Map<String, String>): ApiResponse<Any?>

    @POST("auth/reset-password/confirm")
    suspend fun resetPasswordConfirm(@Body body: ResetPasswordConfirmRequest): ApiResponse<Any?>

    @POST("auth/reset-password-sms")
    suspend fun resetPasswordSms(@Body body: Map<String, String>): ApiResponse<Any?>

    @POST("auth/verify-sms-code")
    suspend fun verifySmsCode(@Body body: VerifySmsRequest): ApiResponse<Any?>

    // Bootstrap & Sync
    @GET("bootstrap")
    suspend fun bootstrap(): ApiResponse<BootstrapData>

    @GET("sync")
    suspend fun sync(@Query("since") since: Long? = null): ApiResponse<SyncData>

    // Channels
    @GET("channels")
    suspend fun getChannels(): ApiResponse<List<Channel>>

    @POST("channels")
    suspend fun createChannel(@Body body: Map<String, Any>): ApiResponse<Channel>

    @GET("channels/{id}")
    suspend fun getChannel(@Path("id") id: Int): ApiResponse<Channel>

    @PUT("channels/{id}")
    suspend fun updateChannel(@Path("id") id: Int, @Body body: Map<String, Any>): ApiResponse<Channel>

    @DELETE("channels/{id}")
    suspend fun deleteChannel(@Path("id") id: Int): ApiResponse<Any?>

    // Posts
    @GET("posts")
    suspend fun getPosts(@Query("status") status: String? = null, @Query("limit") limit: Int = 50, @Query("offset") offset: Int = 0): ApiResponse<List<Post>>

    @Multipart
    @POST("posts")
    suspend fun createPost(@PartMap params: Map<String, @JvmSuppressWildcards RequestBody>, @Part media_file: MultipartBody.Part? = null): ApiResponse<Post>

    @GET("posts/{id}")
    suspend fun getPost(@Path("id") id: Int): ApiResponse<Post>

    @POST("posts/{id}/cancel")
    suspend fun cancelPost(@Path("id") id: Int): ApiResponse<Any?>

    @POST("posts/{id}/retry")
    suspend fun retryPost(@Path("id") id: Int): ApiResponse<Any?>

    // Notifications
    @GET("notifications")
    suspend fun getNotifications(@Query("limit") limit: Int = 20, @Query("offset") offset: Int = 0): ApiResponse<NotificationListData>

    @POST("notifications/{id}/read")
    suspend fun markNotificationRead(@Path("id") id: Int): ApiResponse<Map<String, Int>>

    @POST("notifications/read-all")
    suspend fun markAllNotificationsRead(): ApiResponse<Map<String, Int>>

    // Plans & Billing
    @GET("plans")
    suspend fun getPlans(): ApiResponse<List<Plan>>

    @Multipart
    @POST("payments")
    suspend fun submitPayment(@PartMap params: Map<String, @JvmSuppressWildcards RequestBody>, @Part receipt_photo: MultipartBody.Part? = null): ApiResponse<Payment>

    @GET("payments")
    suspend fun getPayments(): ApiResponse<List<Payment>>

    @POST("coupons/validate")
    suspend fun validateCoupon(@Body body: Map<String, Any>): ApiResponse<CouponValidation>

    // Tickets
    @GET("tickets")
    suspend fun getTickets(): ApiResponse<List<Ticket>>

    @Multipart
    @POST("tickets")
    suspend fun createTicket(@PartMap params: Map<String, @JvmSuppressWildcards RequestBody>, @Part attachment: MultipartBody.Part? = null): ApiResponse<Ticket>

    @GET("tickets/{id}")
    suspend fun getTicketDetail(@Path("id") id: Int): ApiResponse<TicketDetail>

    @Multipart
    @POST("tickets/{id}/reply")
    suspend fun replyTicket(@Path("id") id: Int, @PartMap params: Map<String, @JvmSuppressWildcards RequestBody>, @Part attachment: MultipartBody.Part? = null): ApiResponse<Any?>

    // Settings
    @GET("settings")
    suspend fun getSettings(): ApiResponse<Settings>

    @Multipart
    @POST("settings/gold")
    suspend fun saveGoldSettings(@PartMap params: Map<String, @JvmSuppressWildcards RequestBody>, @Part gold_image: MultipartBody.Part? = null): ApiResponse<Any?>

    @POST("settings/gold/trigger")
    suspend fun triggerGold(): ApiResponse<Any?>

    @PUT("settings/advanced")
    suspend fun saveAdvancedSettings(@Body body: Map<String, String>): ApiResponse<Any?>

    // Auto Responder
    @GET("auto-responder")
    suspend fun getAutoReplies(): ApiResponse<List<AutoReplyRule>>

    @POST("auto-responder")
    suspend fun addAutoReply(@Body body: Map<String, Any>): ApiResponse<AutoReplyRule>

    @DELETE("auto-responder/{id}")
    suspend fun deleteAutoReply(@Path("id") id: Int): ApiResponse<Any?>

    @POST("auto-responder/toggle")
    suspend fun toggleAutoResponder(@Body body: Map<String, Any>): ApiResponse<Any?>

    // Wallet & Referral
    @GET("wallet")
    suspend fun getWallet(): ApiResponse<WalletData>

    @POST("wallet/convert-points")
    suspend fun convertPoints(@Body body: Map<String, String>): ApiResponse<ConvertPointsResult>

    @GET("referral")
    suspend fun getReferral(): ApiResponse<ReferralData>

    // Analytics
    @GET("analytics/links")
    suspend fun getAnalyticsLinks(): ApiResponse<List<LinkTracking>>

    @GET("analytics/links/{id}")
    suspend fun getLinkDetail(@Path("id") id: Int): ApiResponse<LinkDetail>

    // Admin
    @GET("admin/dashboard")
    suspend fun adminDashboard(): ApiResponse<AdminDashboard>

    @GET("admin/users")
    suspend fun adminUsers(@Query("status") status: String? = null, @Query("search") search: String? = null, @Query("limit") limit: Int = 50, @Query("offset") offset: Int = 0): ApiResponse<List<User>>

    @POST("admin/users/{id}/suspend")
    suspend fun adminSuspendUser(@Path("id") id: Int): ApiResponse<Any?>

    @POST("admin/users/{id}/activate")
    suspend fun adminActivateUser(@Path("id") id: Int): ApiResponse<Any?>

    @GET("admin/payments")
    suspend fun adminPayments(): ApiResponse<List<Payment>>

    @POST("admin/payments/{id}/approve")
    suspend fun adminApprovePayment(@Path("id") id: Int): ApiResponse<Any?>

    @GET("admin/tickets")
    suspend fun adminTickets(): ApiResponse<List<Ticket>>

    @POST("admin/tickets/{id}/reply")
    suspend fun adminReplyTicket(@Path("id") id: Int, @Body body: Map<String, String>): ApiResponse<Any?>

    @GET("admin/plans")
    suspend fun adminPlans(): ApiResponse<List<Plan>>

    @POST("admin/plans")
    suspend fun adminCreatePlan(@Body body: Map<String, Any>): ApiResponse<Plan>

    @PUT("admin/plans/{id}")
    suspend fun adminUpdatePlan(@Path("id") id: Int, @Body body: Map<String, Any>): ApiResponse<Plan>

    @DELETE("admin/plans/{id}")
    suspend fun adminDeletePlan(@Path("id") id: Int): ApiResponse<Any?>

    @POST("admin/broadcast")
    suspend fun adminBroadcast(@Body body: Map<String, String>): ApiResponse<Any?>

    @POST("admin/discounts")
    suspend fun adminCreateDiscount(@Body body: Map<String, Any>): ApiResponse<Any?>

    @DELETE("admin/discounts/{id}")
    suspend fun adminDeleteDiscount(@Path("id") id: Int): ApiResponse<Any?>
}

// Request models
data class LoginRequest(val email: String, val password: String, val device_name: String = "android", val ref: String? = null)
data class RegisterRequest(val name: String, val email: String, val password: String, val password_confirm: String, val business_name: String = "", val business_type: String = "", val device_name: String = "android", val ref: String? = null)
data class ProfileRequest(val name: String? = null, val email: String? = null, val birthday: String? = null)
data class ChangePasswordRequest(val current_password: String, val new_password: String, val confirm_password: String)
data class ResetPasswordConfirmRequest(val token: String, val new_password: String, val confirm_password: String)
data class VerifySmsRequest(val code: String, val new_password: String, val confirm_password: String)
