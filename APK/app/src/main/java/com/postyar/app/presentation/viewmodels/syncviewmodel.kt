package com.postyar.app.presentation.viewmodels

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.viewModelScope
import com.postyar.app.data.remote.*
import com.postyar.app.domain.*
import kotlinx.coroutines.flow.*
import kotlinx.coroutines.launch

class SyncViewModel(application: Application) : AndroidViewModel(application) {
    private val tokenManager = TokenManager.getInstance(application)
    private val api = RetrofitClient.getInstance(tokenManager).create(ApiService::class.java)

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