package com.postyar.app.presentation.viewmodels

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.viewModelScope
import com.postyar.app.data.remote.*
import com.postyar.app.domain.*
import kotlinx.coroutines.flow.*
import kotlinx.coroutines.launch

class AutoResponderViewModel(application: Application) : AndroidViewModel(application) {
    private val tokenManager = TokenManager.getInstance(application)
    private val api = RetrofitClient.getInstance(tokenManager).create(ApiService::class.java)
    val rules = MutableStateFlow<List<AutoReplyRule>>(emptyList())
    val isLoading = MutableStateFlow(false)
    val error = MutableStateFlow("")
    val actionSuccess = MutableStateFlow("")

    fun loadRules() {
        viewModelScope.launch {
            isLoading.value = true
            try {
                val resp = api.getAutoReplies()
                if (resp.success && resp.data != null) rules.value = resp.data
            } catch (_: Exception) {} finally { isLoading.value = false }
        }
    }
    fun addRule(channelId: Int, keyword: String, replyText: String) {
        viewModelScope.launch {
            try {
                val resp = api.addAutoReply(mapOf("channel_id" to channelId, "keyword" to keyword, "reply_text" to replyText))
                if (resp.success) { actionSuccess.value = "قانون اضافه شد"; loadRules() }
                else error.value = resp.message ?: "خطا"
            } catch (e: Exception) { error.value = "خطا در ارتباط با سرور" }
        }
    }
    fun deleteRule(id: Int) {
        viewModelScope.launch {
            try {
                api.deleteAutoReply(id)
                rules.value = rules.value.filter { it.id != id }
                actionSuccess.value = "قانون حذف شد"
            } catch (e: Exception) { error.value = "خطا در حذف" }
        }
    }
    fun toggle(channelId: Int, enabled: Int) {
        viewModelScope.launch {
            try { api.toggleAutoResponder(mapOf("channel_id" to channelId, "enabled" to enabled)) }
            catch (_: Exception) {}
        }
    }
    fun clearMessages() { error.value = ""; actionSuccess.value = "" }
}