package com.postyar.app.presentation.viewmodels

import android.net.Uri
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.postyar.app.data.remote.ApiService
import com.postyar.app.domain.*
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.*
import kotlinx.coroutines.launch
import okhttp3.MediaType.Companion.toMediaType
import okhttp3.MultipartBody
import okhttp3.RequestBody.Companion.asRequestBody
import okhttp3.RequestBody.Companion.toRequestBody
import javax.inject.Inject

@HiltViewModel
class TicketViewModel @Inject constructor(
    private val api: ApiService
) : ViewModel() {
    val tickets = MutableStateFlow<List<Ticket>>(emptyList())
    val selectedTicket = MutableStateFlow<Ticket?>(null)
    val ticketDetail = MutableStateFlow<TicketDetail?>(null)
    val replies = MutableStateFlow<List<TicketReply>>(emptyList())
    val categories = MutableStateFlow<List<TicketCategory>>(emptyList())
    val isLoading = MutableStateFlow(false)
    val isSubmitting = MutableStateFlow(false)
    val error = MutableStateFlow("")
    val actionSuccess = MutableStateFlow("")

    fun loadTickets() {
        viewModelScope.launch {
            isLoading.value = true
            try {
                val resp = api.getTickets()
                if (resp.success && resp.data != null) tickets.value = resp.data
            } catch (e: Exception) { error.value = "خطا در ارتباط با سرور" }
            finally { isLoading.value = false }
        }
    }

    fun loadTicketDetail(id: Int) {
        viewModelScope.launch {
            isLoading.value = true
            try {
                val resp = api.getTicketDetail(id)
                if (resp.success && resp.data != null) {
                    ticketDetail.value = resp.data
                    replies.value = resp.data.replies
                }
            } catch (_: Exception) {} finally { isLoading.value = false }
        }
    }

    fun createTicket(subject: String, message: String, categoryId: Int, priority: String) {
        viewModelScope.launch {
            isSubmitting.value = true; error.value = ""
            try {
                val params = mutableMapOf<String, okhttp3.RequestBody>(
                    "subject" to subject.toRequestBody("text/plain".toMediaType()),
                    "message" to message.toRequestBody("text/plain".toMediaType()),
                    "category_id" to categoryId.toString().toRequestBody("text/plain".toMediaType()),
                    "priority" to priority.toRequestBody("text/plain".toMediaType())
                )
                val resp = api.createTicket(params, null)
                if (resp.success) { actionSuccess.value = "تیکت ایجاد شد"; loadTickets() }
                else error.value = resp.message ?: "خطا در ایجاد تیکت"
            } catch (e: Exception) { error.value = "خطا در ارتباط با سرور" }
            finally { isSubmitting.value = false }
        }
    }

    fun replyTicket(ticketId: Int, message: String, attachmentUri: Uri? = null) {
        viewModelScope.launch {
            isSubmitting.value = true; error.value = ""
            try {
                val params = mutableMapOf<String, okhttp3.RequestBody>(
                    "message" to message.toRequestBody("text/plain".toMediaType())
                )
                val attachmentPart = attachmentUri?.let { uri ->
                    // Note: In production, you'd need ContentResolver to get file from URI
                    // This is a placeholder that sends without attachment for now
                    null
                }
                val resp = api.replyTicket(ticketId, params, attachmentPart)
                if (resp.success) { actionSuccess.value = "پاسخ ارسال شد"; loadTicketDetail(ticketId) }
                else error.value = resp.message ?: "خطا"
            } catch (e: Exception) { error.value = "خطا در ارسال پاسخ" }
            finally { isSubmitting.value = false }
        }
    }

    fun closeTicket(id: Int) {
        viewModelScope.launch {
            try {
                api.closeTicket(id)
                tickets.value = tickets.value.map { if (it.id == id) it.copy(status = "closed") else it }
                actionSuccess.value = "تیکت بسته شد"
            } catch (e: Exception) { error.value = "خطا" }
        }
    }

    fun loadCategories() {
        viewModelScope.launch {
            try {
                val resp = api.getTicketCategories()
                if (resp.success && resp.data != null) categories.value = resp.data
            } catch (_: Exception) {}
        }
    }

    fun clearAction() { actionSuccess.value = "" }
}
