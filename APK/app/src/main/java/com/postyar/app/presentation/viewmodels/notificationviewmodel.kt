package com.postyar.app.presentation.viewmodels

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.viewModelScope
import com.postyar.app.data.remote.*
import com.postyar.app.domain.*
import kotlinx.coroutines.flow.*
import kotlinx.coroutines.launch

class NotificationViewModel(application: Application) : AndroidViewModel(application) {
    private val tokenManager = TokenManager.getInstance(application)
    private val api = RetrofitClient.getInstance(tokenManager).create(ApiService::class.java)
    val notifications = MutableStateFlow<List<NotificationItem>>(emptyList())
    val unreadCount = MutableStateFlow(0)
    val isLoading = MutableStateFlow(false)
    fun loadNotifications() {
        viewModelScope.launch {
            isLoading.value = true
            try {
                val resp = api.getNotifications()
                if (resp.success && resp.data != null) {
                    notifications.value = resp.data.notifications
                    unreadCount.value = resp.data.unread_count
                }
            } catch (_: Exception) {} finally { isLoading.value = false }
        }
    }
    fun markRead(id: Int) {
        viewModelScope.launch {
            try {
                api.markNotificationRead(id)
                notifications.value = notifications.value.map {
                    if (it.id == id) it.copy(is_read = 1) else it
                }
                unreadCount.value = (unreadCount.value - 1).coerceAtLeast(0)
            } catch (_: Exception) {}
        }
    }
    fun markAllRead() {
        viewModelScope.launch {
            try {
                api.markAllNotificationsRead()
                notifications.value = notifications.value.map { it.copy(is_read = 1) }
                unreadCount.value = 0
            } catch (_: Exception) {}
        }
    }
}