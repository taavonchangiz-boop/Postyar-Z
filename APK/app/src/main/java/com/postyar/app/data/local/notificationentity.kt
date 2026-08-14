package com.postyar.app.data.local

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity(tableName = "notifications")
data class NotificationEntity(
    @PrimaryKey val id: Int,
    val userId: Int,
    val type: String,
    val title: String,
    val message: String,
    val targetSection: String? = null,
    val isRead: Int = 0,
    val createdAt: String
)