package com.postyar.app.presentation.viewmodels

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.postyar.app.data.remote.ApiService
import com.postyar.app.domain.*
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.*
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class ProfileViewModel @Inject constructor(
    private val api: ApiService
) : ViewModel() {
    val user = MutableStateFlow<User?>(null)
    val subscription = MutableStateFlow<Subscription?>(null)
    val isLoading = MutableStateFlow(false)
    val error = MutableStateFlow("")
    val actionSuccess = MutableStateFlow("")

    fun loadProfile() {
        viewModelScope.launch {
            isLoading.value = true
            try {
                val resp = api.getMe()
                if (resp.success && resp.data != null) {
                    user.value = resp.data.user
                    subscription.value = resp.data.subscription
                }
            } catch (_: Exception) {} finally { isLoading.value = false }
        }
    }

    fun updateProfile(name: String, email: String, birthday: String?) {
        viewModelScope.launch {
            isLoading.value = true; error.value = ""
            try {
                val resp = api.updateProfile(ProfileRequest(name, email, birthday))
                if (resp.success) { actionSuccess.value = "پروفایل بروزرسانی شد"; loadProfile() }
                else error.value = resp.message ?: "خطا"
            } catch (e: Exception) { error.value = "خطا در ارتباط" }
            finally { isLoading.value = false }
        }
    }

    fun changePassword(current: String, newPass: String, confirm: String) {
        viewModelScope.launch {
            isLoading.value = true; error.value = ""
            try {
                val resp = api.changePassword(ChangePasswordRequest(current, newPass, confirm))
                if (resp.success) actionSuccess.value = "رمز عبور تغییر کرد"
                else error.value = resp.message ?: "خطا"
            } catch (e: Exception) { error.value = "خطا" }
            finally { isLoading.value = false }
        }
    }

    fun clearMessages() { error.value = ""; actionSuccess.value = "" }
}
