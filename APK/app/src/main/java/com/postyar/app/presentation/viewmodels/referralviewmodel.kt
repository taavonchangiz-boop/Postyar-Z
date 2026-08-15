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
class ReferralViewModel @Inject constructor(
    private val api: ApiService
) : ViewModel() {
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
