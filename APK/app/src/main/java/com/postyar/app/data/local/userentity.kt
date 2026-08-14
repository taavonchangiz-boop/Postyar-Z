package com.postyar.app.data.local

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity(tableName = "users")
data class UserEntity(
    @PrimaryKey val id: Int,
    val name: String,
    val email: String,
    val role: String,
    val status: String,
    val businessName: String? = null,
    val businessType: String? = null,
    val phone: String? = null,
    val birthday: String? = null,
    val referralCode: String? = null,
    val referralPoints: Double = 0.0,
    val walletBalance: Double = 0.0,
    val createdAt: String
)