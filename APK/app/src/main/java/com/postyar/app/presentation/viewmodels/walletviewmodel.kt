package com.postyar.app.presentation.viewmodels

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.viewModelScope
import com.postyar.app.data.remote.*
import com.postyar.app.domain.*
import kotlinx.coroutines.flow.*
import kotlinx.coroutines.launch

class WalletViewModel(application: Application) : AndroidViewModel(application) {
    private val tokenManager = TokenManager.getInstance(application)
    private val api = RetrofitClient.getInstance(tokenManager).create(ApiService::class.java)
    val walletData = MutableStateFlow<WalletData?>(null)
    val isLoading = MutableStateFlow(false)
    val error = MutableStateFlow("")
    val actionSuccess = MutableStateFlow("")

    fun loadWallet() {
        viewModelScope.launch {
            isLoading.value = true
            try {
                val resp = api.getWallet()
                if (resp.success && resp.data != null) walletData.value = resp.data
            } catch (_: Exception) {} finally { isLoading.value = false }
        }
    }
    fun convertPoints(points: Int) {
        viewModelScope.launch {
            isLoading.value = true; error.value = ""
            try {
                val resp = api.convertPoints(mapOf("points" to points.toString()))
                if (resp.success && resp.data != null) {
                    actionSuccess.value = "امتیاز تبدیل شد"
                    loadWallet()
                } else error.value = resp.message ?: "خطا"
            } catch (e: Exception) { error.value = "خطا در ارتباط با سرور" }
            finally { isLoading.value = false }
        }
    }
    fun clearMessages() { error.value = ""; actionSuccess.value = "" }
}