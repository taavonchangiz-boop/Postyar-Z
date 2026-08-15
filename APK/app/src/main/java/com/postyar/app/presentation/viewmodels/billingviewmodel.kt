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
class BillingViewModel @Inject constructor(
    private val api: ApiService
) : ViewModel() {
    val plans = MutableStateFlow<List<Plan>>(emptyList())
    val payments = MutableStateFlow<List<Payment>>(emptyList())
    val couponValidation = MutableStateFlow<CouponValidation?>(null)
    val isLoading = MutableStateFlow(false)
    val error = MutableStateFlow("")
    val actionSuccess = MutableStateFlow("")

    fun loadPlans() {
        viewModelScope.launch {
            isLoading.value = true
            try {
                val resp = api.getPlans()
                if (resp.success && resp.data != null) plans.value = resp.data
            } catch (_: Exception) {} finally { isLoading.value = false }
        }
    }
    fun loadPayments() {
        viewModelScope.launch {
            isLoading.value = true
            try {
                val resp = api.getPayments()
                if (resp.success && resp.data != null) payments.value = resp.data
            } catch (_: Exception) {} finally { isLoading.value = false }
        }
    }
    fun validateCoupon(code: String, planId: Int) {
        viewModelScope.launch {
            try {
                val resp = api.validateCoupon(mapOf("code" to code, "plan_id" to planId))
                if (resp.success && resp.data != null) couponValidation.value = resp.data
                else error.value = resp.message ?: "کد تخفیف نامعتبر"
            } catch (_: Exception) { error.value = "خطا در بررسی کد" }
        }
    }
    fun submitPayment(planId: Int, amount: String, referenceNum: String, receiptFile: File? = null) {
        viewModelScope.launch {
            isLoading.value = true; error.value = ""
            try {
                val params = mutableMapOf<String, okhttp3.RequestBody>(
                    "plan_id" to planId.toString().toRequestBody("text/plain".toMediaType()),
                    "amount" to amount.toRequestBody("text/plain".toMediaType()),
                    "reference_num" to referenceNum.toRequestBody("text/plain".toMediaType())
                )
                val imagePart = receiptFile?.let {
                    MultipartBody.Part.createFormData("receipt_photo", it.name, it.asRequestBody("image/*".toMediaType()))
                }
                val resp = api.submitPayment(params, imagePart)
                if (resp.success) { actionSuccess.value = "درخواست پرداخت ثبت شد"; loadPayments() }
                else error.value = resp.message ?: "خطا در ثبت پرداخت"
            } catch (e: Exception) { error.value = "خطا در ارتباط با سرور" }
            finally { isLoading.value = false }
        }
    }
    fun clearMessages() { error.value = ""; actionSuccess.value = ""; couponValidation.value = null }
}
