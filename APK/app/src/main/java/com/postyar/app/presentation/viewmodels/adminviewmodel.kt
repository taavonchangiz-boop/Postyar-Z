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
class AdminViewModel @Inject constructor(
    private val api: ApiService
) : ViewModel() {
    val dashboard = MutableStateFlow<AdminDashboard?>(null)
    val users = MutableStateFlow<List<User>>(emptyList())
    val payments = MutableStateFlow<List<Payment>>(emptyList())
    val tickets = MutableStateFlow<List<Ticket>>(emptyList())
    val plans = MutableStateFlow<List<Plan>>(emptyList())
    val isLoading = MutableStateFlow(false)
    val error = MutableStateFlow("")
    val actionSuccess = MutableStateFlow("")

    fun loadDashboard() {
        viewModelScope.launch {
            isLoading.value = true
            try { val resp = api.adminDashboard(); if (resp.success) dashboard.value = resp.data }
            catch (_: Exception) {} finally { isLoading.value = false }
        }
    }

    fun loadUsers(status: String? = null, search: String? = null) {
        viewModelScope.launch {
            isLoading.value = true
            try { val resp = api.adminUsers(status, search); if (resp.success) users.value = resp.data ?: emptyList() }
            catch (_: Exception) {} finally { isLoading.value = false }
        }
    }

    fun suspendUser(id: Int) {
        viewModelScope.launch {
            try { api.adminSuspendUser(id); actionSuccess.value = "کاربر معلق شد"; loadUsers() }
            catch (e: Exception) { error.value = "خطا" }
        }
    }

    fun activateUser(id: Int) {
        viewModelScope.launch {
            try { api.adminActivateUser(id); actionSuccess.value = "کاربر فعال شد"; loadUsers() }
            catch (e: Exception) { error.value = "خطا" }
        }
    }

    fun loadPayments() {
        viewModelScope.launch {
            isLoading.value = true
            try { val resp = api.adminPayments(); if (resp.success) payments.value = resp.data ?: emptyList() }
            catch (_: Exception) {} finally { isLoading.value = false }
        }
    }

    fun approvePayment(id: Int) {
        viewModelScope.launch {
            try { api.adminApprovePayment(id); actionSuccess.value = "پرداخت تایید شد"; loadPayments() }
            catch (e: Exception) { error.value = "خطا" }
        }
    }

    fun loadTickets() {
        viewModelScope.launch {
            isLoading.value = true
            try { val resp = api.adminTickets(); if (resp.success) tickets.value = resp.data ?: emptyList() }
            catch (_: Exception) {} finally { isLoading.value = false }
        }
    }

    fun adminReplyTicket(id: Int, message: String) {
        viewModelScope.launch {
            try { api.adminReplyTicket(id, mapOf("message" to message)); actionSuccess.value = "پاسخ ارسال شد" }
            catch (e: Exception) { error.value = "خطا" }
        }
    }

    fun loadPlans() {
        viewModelScope.launch {
            isLoading.value = true
            try { val resp = api.adminPlans(); if (resp.success) plans.value = resp.data ?: emptyList() }
            catch (_: Exception) {} finally { isLoading.value = false }
        }
    }

    fun createPlan(body: Map<String, Any>) {
        viewModelScope.launch {
            try { api.adminCreatePlan(body); actionSuccess.value = "پلن ایجاد شد"; loadPlans() }
            catch (e: Exception) { error.value = "خطا" }
        }
    }

    fun updatePlan(id: Int, body: Map<String, Any>) {
        viewModelScope.launch {
            try { api.adminUpdatePlan(id, body); actionSuccess.value = "پلن بروزرسانی شد"; loadPlans() }
            catch (e: Exception) { error.value = "خطا" }
        }
    }

    fun deletePlan(id: Int) {
        viewModelScope.launch {
            try { api.adminDeletePlan(id); actionSuccess.value = "پلن حذف شد"; loadPlans() }
            catch (e: Exception) { error.value = "خطا" }
        }
    }

    fun broadcast(title: String, message: String) {
        viewModelScope.launch {
            try { api.adminBroadcast(mapOf("title" to title, "message" to message)); actionSuccess.value = "پیام ارسال شد" }
            catch (e: Exception) { error.value = "خطا" }
        }
    }

    fun clearMessages() { error.value = ""; actionSuccess.value = "" }
}
