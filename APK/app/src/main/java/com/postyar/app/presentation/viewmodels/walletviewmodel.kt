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
class WalletViewModel @Inject constructor(
    private val api: ApiService
) : ViewModel() {
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
