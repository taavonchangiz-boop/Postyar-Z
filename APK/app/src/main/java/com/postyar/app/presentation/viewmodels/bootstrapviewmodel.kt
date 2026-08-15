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
class BootstrapViewModel @Inject constructor(
    private val api: ApiService
) : ViewModel() {
    val currentUser = MutableStateFlow<User?>(null)
    val quota = MutableStateFlow<Quota?>(null)
    val channels = MutableStateFlow<List<Channel>>(emptyList())
    val posts = MutableStateFlow<List<Post>>(emptyList())
    val notifications = MutableStateFlow<List<NotificationItem>>(emptyList())
    val unreadCount = MutableStateFlow(0)
    val plans = MutableStateFlow<List<Plan>>(emptyList())
    val tickets = MutableStateFlow<List<Ticket>>(emptyList())
    val autoReplies = MutableStateFlow<List<AutoReplyRule>>(emptyList())
    val paymentHistory = MutableStateFlow<List<Payment>>(emptyList())
    val settings = MutableStateFlow<Settings?>(null)
    val ticketCategories = MutableStateFlow<List<TicketCategory>>(emptyList())
    val referralInfo = MutableStateFlow<ReferralInfo?>(null)
    val walletBalance = MutableStateFlow(0.0)
    val announcement = MutableStateFlow<Announcement?>(null)
    val isLoading = MutableStateFlow(false)
    val error = MutableStateFlow("")
    val lastSyncTime = MutableStateFlow(0L)

    fun loadBootstrap() {
        viewModelScope.launch {
            isLoading.value = true
            error.value = ""
            try {
                val resp = api.bootstrap()
                if (resp.success && resp.data != null) {
                    val d = resp.data
                    currentUser.value = d.user
                    quota.value = d.quota
                    channels.value = d.channels
                    posts.value = d.posts
                    notifications.value = d.notifications
                    unreadCount.value = d.unread_count
                    plans.value = d.plans
                    tickets.value = d.tickets
                    autoReplies.value = d.auto_replies
                    paymentHistory.value = d.payment_history
                    settings.value = d.settings
                    ticketCategories.value = d.ticket_categories
                    referralInfo.value = d.referral_info
                    walletBalance.value = d.wallet_balance
                    announcement.value = d.announcement
                    lastSyncTime.value = System.currentTimeMillis() / 1000
                } else {
                    error.value = resp.message ?: "خطا در بارگذاری"
                }
            } catch (e: Exception) {
                error.value = "خطا در ارتباط با سرور"
            } finally {
                isLoading.value = false
            }
        }
    }

    fun refreshChannels() {
        viewModelScope.launch {
            try {
                val resp = api.getChannels()
                if (resp.success && resp.data != null) channels.value = resp.data
            } catch (_: Exception) {}
        }
    }
}
