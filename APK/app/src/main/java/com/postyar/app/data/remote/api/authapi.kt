package com.postyar.app.data.remote.api

import com.postyar.app.data.remote.dto.*
import com.postyar.app.data.remote.dto.MarkedCountData
import retrofit2.http.*

interface AuthApi {

    @POST("auth/login")
    @Headers("Content-Type: application/json")
    suspend fun login(@Body body: LoginRequest): ApiResponse<LoginResponseDto>

    @POST("auth/register")
    @Headers("Content-Type: application/json")
    suspend fun register(@Body body: RegisterRequest): ApiResponse<LoginResponseDto>

    @POST("auth/logout")
    suspend fun logout(): ApiResponse<Nothing>

    @GET("auth/me")
    suspend fun me(): ApiResponse<MeResponseDto>

    @PUT("auth/profile")
    @Headers("Content-Type: application/json")
    suspend fun updateProfile(@Body body: UpdateProfileRequest): ApiResponse<UserDto>

    @POST("auth/change-password")
    @Headers("Content-Type: application/json")
    suspend fun changePassword(@Body body: ChangePasswordRequest): ApiResponse<Nothing>

    @POST("auth/reset-password")
    @Headers("Content-Type: application/json")
    suspend fun resetPassword(@Body body: ResetPasswordRequest): ApiResponse<Nothing>

    @POST("auth/reset-password/confirm")
    @Headers("Content-Type: application/json")
    suspend fun resetPasswordConfirm(@Body body: ResetPasswordConfirmRequest): ApiResponse<Nothing>

    @POST("auth/reset-password-sms")
    @Headers("Content-Type: application/json")
    suspend fun resetPasswordSms(@Body body: ResetPasswordSmsRequest): ApiResponse<Nothing>

    @POST("auth/verify-sms-code")
    @Headers("Content-Type: application/json")
    suspend fun verifySmsCode(@Body body: VerifySmsCodeRequest): ApiResponse<Nothing>
}

data class LoginRequest(
    val email: String,
    val password: String,
    val device_name: String = "android",
    val ref: String? = null
)

data class RegisterRequest(
    val name: String,
    val email: String,
    val password: String,
    val password_confirm: String,
    val business_name: String = "",
    val business_type: String = "",
    val device_name: String = "android",
    val ref: String? = null
)

data class UpdateProfileRequest(
    val name: String? = null,
    val email: String? = null,
    val birthday: String? = null
)

data class ChangePasswordRequest(
    val current_password: String,
    val new_password: String,
    val confirm_password: String
)

data class ResetPasswordRequest(
    val email: String
)

data class ResetPasswordConfirmRequest(
    val token: String,
    val new_password: String,
    val confirm_password: String
)

data class ResetPasswordSmsRequest(
    val phone: String
)

data class VerifySmsCodeRequest(
    val code: String,
    val new_password: String,
    val confirm_password: String
)