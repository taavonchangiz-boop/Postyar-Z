package com.postyar.app.presentation.viewmodels

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.viewModelScope
import com.postyar.app.data.remote.*
import com.postyar.app.domain.*
import kotlinx.coroutines.flow.*
import kotlinx.coroutines.launch

class ChannelViewModel(application: Application) : AndroidViewModel(application) {
    private val tokenManager = TokenManager.getInstance(application)
    private val api = RetrofitClient.getInstance(tokenManager).create(ApiService::class.java)
    val channels = MutableStateFlow<List<Channel>>(emptyList())
    val selectedChannel = MutableStateFlow<Channel?>(null)
    val isLoading = MutableStateFlow(false)
    val error = MutableStateFlow("")
    val actionSuccess = MutableStateFlow("")
    fun loadChannels() {
        viewModelScope.launch {
            isLoading.value = true
            try {
                val resp = api.getChannels()
                if (resp.success && resp.data != null) channels.value = resp.data
            } catch (e: Exception) { error.value = "خطا در ارتباط با سرور" }
            finally { isLoading.value = false }
        }
    }
    fun loadChannel(id: Int) {
        viewModelScope.launch {
            isLoading.value = true
            try {
                val resp = api.getChannel(id)
                if (resp.success && resp.data != null) selectedChannel.value = resp.data
            } catch (_: Exception) {} finally { isLoading.value = false }
        }
    }
    fun createChannel(name: String, platform: String, channelId: String, token: String) {
        viewModelScope.launch {
            isLoading.value = true; error.value = ""
            try {
                val resp = api.createChannel(mapOf(
                    "name" to name, "platform" to platform,
                    "channel_id" to channelId, "token" to token
                ))
                if (resp.success) { actionSuccess.value = "کانال اضافه شد"; loadChannels() }
                else error.value = resp.message ?: "خطا"
            } catch (e: Exception) { error.value = "خطا در ارتباط با سرور" }
            finally { isLoading.value = false }
        }
    }
    fun updateChannel(id: Int, params: Map<String, Any>) {
        viewModelScope.launch {
            isLoading.value = true; error.value = ""
            try {
                val resp = api.updateChannel(id, params)
                if (resp.success) { actionSuccess.value = "کانال بروزرسانی شد"; loadChannels() }
                else error.value = resp.message ?: "خطا"
            } catch (e: Exception) { error.value = "خطا در ارتباط با سرور" }
            finally { isLoading.value = false }
        }
    }
    fun deleteChannel(id: Int) {
        viewModelScope.launch {
            try {
                api.deleteChannel(id)
                channels.value = channels.value.filter { it.id != id }
                actionSuccess.value = "کانال حذف شد"
            } catch (e: Exception) { error.value = "خطا در حذف کانال" }
        }
    }
    fun clearAction() { actionSuccess.value = "" }
}