package com.postyar.app.data.local

import androidx.room.Database
import androidx.room.RoomDatabase

@Database(
    entities = [
        UserEntity::class,
        ChannelEntity::class,
        PostEntity::class,
        NotificationEntity::class
    ],
    version = 1,
    exportSchema = false
)
abstract class PostyarDatabase : RoomDatabase() {
    abstract fun userDao(): UserDao
    abstract fun channelDao(): ChannelDao
    abstract fun postDao(): PostDao
    abstract fun notificationDao(): NotificationDao
}
