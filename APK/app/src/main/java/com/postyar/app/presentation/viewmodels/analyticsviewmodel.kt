package com.postyar.app.presentation.viewmodels

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.viewModelScope
import com.postyar.app.data.remote.*
import com.postyar.app.domain.*
import kotlinx.coroutines.flow.*
import kotlinx.coroutines.launch

class AnalyticsViewModel(application: Application) : AndroidViewModel(application) {
    private val tokenManager = TokenManager.getInstance(application)
    private val api = RetrofitClient.getInstance(tokenManager).create(ApiService::class.java)
    val links = MutableStateFlow<List<LinkTracking>>(emptyList())
    val linkDetail = MutableStateFlow<LinkDetail?>(null)
    val isLoading = MutableStateFlow(false)
    fun loadLinks() {
        viewModelScope.launch {
            isLoading.value = true
            try {
                val resp = api.getAnalyticsLinks()
                if (resp.success && resp.data != null) links.value = resp.data
            } catch (_: Exception) {} finally { isLoading.value = false }
        }
    }
    fun loadLinkDetail(id: Int) {
        viewModelScope.launch {
            isLoading.value = true
            try {
                val resp = api.getLinkDetail(id)
                if (resp.success && resp.data != null) linkDetail.value = resp.data
            } catch (_: Exception) {} finally { isLoading.value = false }
        }
    }
}