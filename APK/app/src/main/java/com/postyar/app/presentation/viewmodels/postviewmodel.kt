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
class PostViewModel @Inject constructor(
    private val api: ApiService
) : ViewModel() {
    val posts = MutableStateFlow<List<Post>>(emptyList())
    val selectedPost = MutableStateFlow<Post?>(null)
    val postDetail = MutableStateFlow<Post?>(null)
    val isLoading = MutableStateFlow(false)
    val isSubmitting = MutableStateFlow(false)
    val error = MutableStateFlow("")
    val actionSuccess = MutableStateFlow("")
    val currentFilter = MutableStateFlow<String?>(null)
    val statusFilter = MutableStateFlow<String?>(null)

    fun loadPosts(status: String? = null) {
        viewModelScope.launch {
            isLoading.value = true
            currentFilter.value = status
            try {
                val resp = api.getPosts(status = status)
                if (resp.success && resp.data != null) posts.value = resp.data
                else error.value = resp.message ?: "خطا در بارگذاری"
            } catch (e: Exception) {
                error.value = "خطا در ارتباط با سرور"
            } finally { isLoading.value = false }
        }
    }

    fun loadPostDetail(id: Int) {
        viewModelScope.launch {
            isLoading.value = true
            try {
                val resp = api.getPost(id)
                if (resp.success && resp.data != null) postDetail.value = resp.data
            } catch (_: Exception) {} finally { isLoading.value = false }
        }
    }

    fun createPost(title: String, content: String, sendType: String, channelIds: List<Int>, schedDate: String? = null, schedHour: String? = null, schedMinute: String? = null, captionFormat: String = "", imageFile: File? = null) {
        viewModelScope.launch {
            isSubmitting.value = true
            error.value = ""
            try {
                val params = mutableMapOf<String, okhttp3.RequestBody>(
                    "title" to title.toRequestBody("text/plain".toMediaType()),
                    "content" to content.toRequestBody("text/plain".toMediaType()),
                    "send_type" to sendType.toRequestBody("text/plain".toMediaType()),
                    "post_channels" to channelIds.joinToString(",").toRequestBody("text/plain".toMediaType()),
                    "caption_format" to captionFormat.toRequestBody("text/plain".toMediaType())
                )
                if (sendType == "scheduled" && schedDate != null) {
                    params["sched_date"] = schedDate.toRequestBody("text/plain".toMediaType())
                    params["sched_hour"] = (schedHour ?: "0").toRequestBody("text/plain".toMediaType())
                    params["sched_minute"] = (schedMinute ?: "0").toRequestBody("text/plain".toMediaType())
                }
                val imagePart = imageFile?.let {
                    MultipartBody.Part.createFormData("media_file", it.name, it.asRequestBody("image/*".toMediaType()))
                }
                val resp = api.createPost(params, imagePart)
                if (resp.success) actionSuccess.value = "پست با موفقیت ایجاد شد"
                else error.value = resp.message ?: "خطا در ایجاد پست"
            } catch (e: Exception) {
                error.value = "خطا در ارتباط با سرور"
            } finally { isSubmitting.value = false }
        }
    }

    fun cancelPost(id: Int) {
        viewModelScope.launch {
            try {
                api.cancelPost(id)
                posts.value = posts.value.map { if (it.id == id) it.copy(status = "cancelled") else it }
                actionSuccess.value = "پست لغو شد"
            } catch (e: Exception) { error.value = "خطا در لغو پست" }
        }
    }

    fun retryPost(id: Int) {
        viewModelScope.launch {
            try {
                api.retryPost(id)
                loadPosts(currentFilter.value)
                actionSuccess.value = "ارسال مجدد پست"
            } catch (e: Exception) { error.value = "خطا در ارسال مجدد" }
        }
    }

    fun setStatusFilter(status: String?) { statusFilter.value = status; loadPosts(status) }
    fun clearAction() { actionSuccess.value = "" }
    fun clearError() { error.value = "" }
}
