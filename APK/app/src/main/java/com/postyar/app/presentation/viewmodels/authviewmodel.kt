package com.postyar.app.presentation.viewmodels

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.postyar.app.core.security.TokenManager
import com.postyar.app.data.remote.ApiService
import com.postyar.app.domain.*
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.*
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class AuthViewModel @Inject constructor(
    private val api: ApiService,
    private val tokenManager: TokenManager
) : ViewModel() {
    val currentUser = MutableStateFlow<User?>(null)
    val authState = MutableStateFlow(AuthState.IDLE)
    val loginError = MutableStateFlow("")
    val registerError = MutableStateFlow("")
    val passwordResetSent = MutableStateFlow(false)

    fun checkExistingSession() {
        viewModelScope.launch {
            val token = tokenManager.getToken()
            if (token != null) {
                try {
                    val resp = api.getMe()
                    if (resp.success && resp.data != null) {
                        currentUser.value = resp.data.user
                        authState.value = AuthState.AUTHENTICATED
                    } else {
                        tokenManager.clearToken()
                        authState.value = AuthState.UNAUTHENTICATED
                    }
                } catch (e: Exception) {
                    tokenManager.clearToken()
                    authState.value = AuthState.UNAUTHENTICATED
                }
            } else {
                authState.value = AuthState.UNAUTHENTICATED
            }
        }
    }

    fun login(email: String, password: String, ref: String?) {
        viewModelScope.launch {
            authState.value = AuthState.LOADING
            loginError.value = ""
            try {
                val resp = api.login(LoginRequest(email, password, "android", ref))
                if (resp.success && resp.data != null) {
                    tokenManager.saveToken(resp.data.token)
                    currentUser.value = resp.data.user
                    authState.value = AuthState.AUTHENTICATED
                } else {
                    loginError.value = resp.message ?: "خطا در ورود"
                    authState.value = AuthState.UNAUTHENTICATED
                }
            } catch (e: Exception) {
                loginError.value = "خطا در ارتباط با سرور"
                authState.value = AuthState.UNAUTHENTICATED
            }
        }
    }

    fun register(name: String, email: String, password: String, confirmPassword: String, businessName: String, businessType: String, ref: String?) {
        viewModelScope.launch {
            authState.value = AuthState.LOADING
            registerError.value = ""
            try {
                val resp = api.register(RegisterRequest(name, email, password, confirmPassword, businessName, businessType, "android", ref))
                if (resp.success && resp.data != null) {
                    tokenManager.saveToken(resp.data.token)
                    currentUser.value = resp.data.user
                    authState.value = AuthState.AUTHENTICATED
                } else {
                    registerError.value = resp.message ?: "خطا در ثبت‌نام"
                    authState.value = AuthState.UNAUTHENTICATED
                }
            } catch (e: Exception) {
                registerError.value = "خطا در ارتباط با سرور"
                authState.value = AuthState.UNAUTHENTICATED
            }
        }
    }

    fun logout() {
        viewModelScope.launch {
            try { api.logout() } catch (_: Exception) {}
            tokenManager.clearToken()
            currentUser.value = null
            authState.value = AuthState.UNAUTHENTICATED
        }
    }

    fun requestPasswordReset(email: String) {
        viewModelScope.launch {
            try {
                api.resetPassword(mapOf("email" to email))
                passwordResetSent.value = true
            } catch (_: Exception) {
                passwordResetSent.value = true
            }
        }
    }

    fun requestSmsReset(phone: String) {
        viewModelScope.launch {
            try {
                api.resetPasswordSms(mapOf("phone" to phone))
                passwordResetSent.value = true
            } catch (_: Exception) {
                passwordResetSent.value = true
            }
        }
    }

    fun confirmSmsReset(code: String, newPassword: String, confirmPassword: String, onSuccess: () -> Unit) {
        viewModelScope.launch {
            try {
                val resp = api.verifySmsCode(VerifySmsRequest(code, newPassword, confirmPassword))
                if (resp.success) onSuccess()
            } catch (_: Exception) {}
        }
    }
}
