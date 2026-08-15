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
class AnalyticsViewModel @Inject constructor(
    private val api: ApiService
) : ViewModel() {
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
