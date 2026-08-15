package com.postyar.app.presentation.viewmodels

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
import java.io.File
import javax.inject.Inject

@HiltViewModel
class SettingsViewModel @Inject constructor(
    private val api: ApiService
) : ViewModel() {
    val settings = MutableStateFlow(Settings())
    val isLoading = MutableStateFlow(false)
    val error = MutableStateFlow("")
    val actionSuccess = MutableStateFlow("")

    fun loadSettings() {
        viewModelScope.launch {
            isLoading.value = true
            try {
                val resp = api.getSettings()
                if (resp.success && resp.data != null) settings.value = resp.data
            } catch (_: Exception) {} finally { isLoading.value = false }
        }
    }

    fun saveAdvancedSettings(map: Map<String, String>) {
        viewModelScope.launch {
            isLoading.value = true; error.value = ""
            try {
                val resp = api.saveAdvancedSettings(map)
                if (resp.success) actionSuccess.value = "تنظیمات ذخیره شد"
                else error.value = resp.message ?: "خطا"
            } catch (e: Exception) { error.value = "خطا در ذخیره تنظیمات" }
            finally { isLoading.value = false }
        }
    }

    fun saveGoldSettings(schedule: String, apiUrl: String, currency: String, template: String, channels: String, imageFile: File? = null) {
        viewModelScope.launch {
            isLoading.value = true; error.value = ""
            try {
                val params = mutableMapOf<String, okhttp3.RequestBody>(
                    "gold_schedule" to schedule.toRequestBody("text/plain".toMediaType()),
                    "gold_api_url" to apiUrl.toRequestBody("text/plain".toMediaType()),
                    "gold_currency" to currency.toRequestBody("text/plain".toMediaType()),
                    "gold_template" to template.toRequestBody("text/plain".toMediaType()),
                    "gold_channels" to channels.toRequestBody("text/plain".toMediaType())
                )
                val imgPart = imageFile?.let {
                    MultipartBody.Part.createFormData("gold_image", it.name, it.asRequestBody("image/*".toMediaType()))
                }
                val resp = api.saveGoldSettings(params, imgPart)
                if (resp.success) actionSuccess.value = "تنظیمات طلا ذخیره شد"
                else error.value = resp.message ?: "خطا"
            } catch (e: Exception) { error.value = "خطا در ارتباط با سرور" }
            finally { isLoading.value = false }
        }
    }

    fun triggerGold() {
        viewModelScope.launch {
            try {
                api.triggerGold()
                actionSuccess.value = "اطلاعات طلا ارسال شد"
            } catch (e: Exception) { error.value = "خطا در ارسال" }
        }
    }

    fun clearMessages() { error.value = ""; actionSuccess.value = "" }
}
