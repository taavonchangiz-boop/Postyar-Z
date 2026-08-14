package com.postyar.app.data.local

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity(tableName = "channels")
data class ChannelEntity(
    @PrimaryKey val id: Int,
    val tenantId: Int,
    val name: String,
    val platform: String,
    val channelId: String,
    val token: String? = null,
    val linkConfig: String? = null,
    val buttonConfig: String? = null,
    val webhookActive: Int = 0,
    val createdAt: String
)