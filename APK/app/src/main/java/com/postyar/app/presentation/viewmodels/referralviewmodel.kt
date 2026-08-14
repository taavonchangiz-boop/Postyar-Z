package com.postyar.app.presentation.viewmodels

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.viewModelScope
import com.postyar.app.data.remote.*
import com.postyar.app.domain.*
import kotlinx.coroutines.flow.*
import kotlinx.coroutines.launch

class ReferralViewModel(application: Application) : AndroidViewModel(application) {
    private val tokenManager = TokenManager.getInstance(application)
    private val api = RetrofitClient.getInstance(tokenManager).create(ApiService::class.java)
    val referralData = MutableStateFlow<ReferralData?>(null)
    val isLoading = MutableStateFlow(false)
    fun loadReferral() {
        viewModelScope.launch {
            isLoading.value = true
            try {
                val resp = api.getReferral()
                if (resp.success && resp.data != null) referralData.value = resp.data
            } catch (_: Exception) {} finally { isLoading.value = false }
        }
    }
}