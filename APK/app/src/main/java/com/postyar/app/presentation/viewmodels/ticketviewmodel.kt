package com.postyar.app.presentation.viewmodels

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.viewModelScope
import com.postyar.app.data.remote.*
import com.postyar.app.domain.*
import kotlinx.coroutines.flow.*
import kotlinx.coroutines.launch
import okhttp3.MediaType.Companion.toMediaType
import okhttp3.MultipartBody
import okhttp3.RequestBody.Companion.asRequestBody
import okhttp3.RequestBody.Companion.toRequestBody
import java.io.File

class TicketViewModel(application: Application) : AndroidViewModel(application) {
    private val tokenManager = TokenManager.getInstance(application)
    private val api = RetrofitClient.getInstance(tokenManager).create(ApiService::class.java)
    val tickets = MutableStateFlow<List<Ticket>>(emptyList())
    val ticketDetail = MutableStateFlow<TicketDetail?>(null)
    val isLoading = MutableStateFlow(false)
    val error = MutableStateFlow("")
    val actionSuccess = MutableStateFlow("")

    fun loadTickets() {
        viewModelScope.launch {
            isLoading.value = true
            try {
                val resp = api.getTickets()
                if (resp.success && resp.data != null) tickets.value = resp.data
            } catch (_: Exception) {} finally { isLoading.value = false }
        }
    }
    fun loadTicketDetail(id: Int) {
        viewModelScope.launch {
            isLoading.value = true
            try {
                val resp = api.getTicketDetail(id)
                if (resp.success && resp.data != null) ticketDetail.value = resp.data
            } catch (_: Exception) {} finally { isLoading.value = false }
        }
    }
    fun createTicket(subject: String, category: String, message: String, attachmentFile: File? = null) {
        viewModelScope.launch {
            isLoading.value = true; error.value = ""
            try {
                val params = mutableMapOf<String, okhttp3.RequestBody>(
                    "subject" to subject.toRequestBody("text/plain".toMediaType()),
                    "category" to category.toRequestBody("text/plain".toMediaType()),
                    "message" to message.toRequestBody("text/plain".toMediaType())
                )
                val attachPart = attachmentFile?.let {
                    MultipartBody.Part.createFormData("attachment", it.name, it.asRequestBody("*/*".toMediaType()))
                }
                val resp = api.createTicket(params, attachPart)
                if (resp.success) { actionSuccess.value = "تیکت ایجاد شد"; loadTickets() }
                else error.value = resp.message ?: "خطا"
            } catch (e: Exception) { error.value = "خطا در ارتباط با سرور" }
            finally { isLoading.value = false }
        }
    }
    fun replyTicket(id: Int, message: String, closeAfter: Boolean = false, attachmentFile: File? = null) {
        viewModelScope.launch {
            try {
                val params = mutableMapOf<String, okhttp3.RequestBody>(
                    "message" to message.toRequestBody("text/plain".toMediaType()),
                    "close_after_reply" to (if (closeAfter) "1" else "0").toRequestBody("text/plain".toMediaType())
                )
                val attachPart = attachmentFile?.let {
                    MultipartBody.Part.createFormData("attachment", it.name, it.asRequestBody("*/*".toMediaType()))
                }
                api.replyTicket(id, params, attachPart)
                loadTicketDetail(id)
                actionSuccess.value = "پاسخ ارسال شد"
            } catch (e: Exception) { error.value = "خطا در ارسال پاسخ" }
        }
    }
    fun clearMessages() { error.value = ""; actionSuccess.value = "" }
}