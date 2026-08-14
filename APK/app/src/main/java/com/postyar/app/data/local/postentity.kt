package com.postyar.app.data.local

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity(tableName = "posts")
data class PostEntity(
    @PrimaryKey val id: Int,
    val tenantId: Int,
    val title: String,
    val content: String,
    val mediaUrl: String? = null,
    val status: String,
    val scheduledAt: String? = null,
    val targetChannels: String? = null,
    val createdAt: String,
    val clickCount: Int = 0
)