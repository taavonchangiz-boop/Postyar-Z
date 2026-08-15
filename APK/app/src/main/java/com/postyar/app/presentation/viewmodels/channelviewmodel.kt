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
class ChannelViewModel @Inject constructor(
    private val api: ApiService
) : ViewModel() {
    val channels = MutableStateFlow<List<Channel>>(emptyList())
    val selectedChannel = MutableStateFlow<Channel?>(null)
    val channelDetail = MutableStateFlow<Channel?>(null)
    val isLoading = MutableStateFlow(false)
    val isSubmitting = MutableStateFlow(false)
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
    fun loadChannelDetail(id: Int) {
        viewModelScope.launch {
            isLoading.value = true
            try {
                val resp = api.getChannel(id)
                if (resp.success && resp.data != null) channelDetail.value = resp.data
            } catch (_: Exception) {} finally { isLoading.value = false }
        }
    }
    fun addChannel(name: String, platform: String, channelId: String, token: String) {
        viewModelScope.launch {
            isSubmitting.value = true; error.value = ""
            try {
                val resp = api.createChannel(mapOf(
                    "name" to name, "platform" to platform,
                    "channel_id" to channelId, "token" to token
                ))
                if (resp.success) { actionSuccess.value = "کانال اضافه شد"; loadChannels() }
                else error.value = resp.message ?: "خطا"
            } catch (e: Exception) { error.value = "خطا در ارتباط با سرور" }
            finally { isSubmitting.value = false }
        }
    }
    fun updateChannel(id: Int, params: Map<String, Any>) {
        viewModelScope.launch {
            isSubmitting.value = true; error.value = ""
            try {
                val resp = api.updateChannel(id, params)
                if (resp.success) { actionSuccess.value = "کانال بروزرسانی شد"; loadChannels() }
                else error.value = resp.message ?: "خطا"
            } catch (e: Exception) { error.value = "خطا در ارتباط با سرور" }
            finally { isSubmitting.value = false }
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
