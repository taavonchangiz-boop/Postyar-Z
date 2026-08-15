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
class SyncViewModel @Inject constructor(
    private val api: ApiService
) : ViewModel() {
    val unreadCount = MutableStateFlow(0)
    val lastSyncError = MutableStateFlow<String?>(null)

    fun sync(since: Long? = null, onSynced: ((SyncData) -> Unit)? = null) {
        viewModelScope.launch {
            try {
                val resp = api.sync(since)
                if (resp.success && resp.data != null) {
                    unreadCount.value = resp.data.unread_count
                    onSynced?.invoke(resp.data)
                }
            } catch (_: Exception) {}
        }
    }
}
