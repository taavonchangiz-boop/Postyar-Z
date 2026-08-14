package com.postyar.app.data.remote.dto

import com.squareup.moshi.Json
import com.squareup.moshi.JsonClass

@JsonClass(generateAdapter = true)
data class WalletTransactionDto(
    @Json(name = "id") val id: Int,
    @Json(name = "user_id") val userId: Int,
    @Json(name = "type") val type: String,
    @Json(name = "amount") val amount: Double,
    @Json(name = "balance_after") val balanceAfter: Double,
    @Json(name = "description") val description: String? = null,
    @Json(name = "reference_type") val referenceType: String? = null,
    @Json(name = "reference_id") val referenceId: Int? = null,
    @Json(name = "created_at") val createdAt: String
)

@JsonClass(generateAdapter = true)
data class WalletDto(
    @Json(name = "balance") val balance: Double,
    @Json(name = "transactions") val transactions: List<WalletTransactionDto>
)

@JsonClass(generateAdapter = true)
data class ConvertPointsDto(
    @Json(name = "new_balance") val newBalance: Double,
    @Json(name = "converted") val converted: Int,
    @Json(name = "wallet_amount") val walletAmount: Double
)