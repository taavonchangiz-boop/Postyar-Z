package com.postyar.app.di

import android.content.Context
import androidx.room.Room
import com.postyar.app.data.local.PostyarDatabase
import com.postyar.app.data.local.UserDao
import com.postyar.app.data.local.ChannelDao
import com.postyar.app.data.local.PostDao
import com.postyar.app.data.local.NotificationDao
import dagger.Module
import dagger.Provides
import dagger.hilt.InstallIn
import dagger.hilt.android.qualifiers.ApplicationContext
import dagger.hilt.components.SingletonComponent
import javax.inject.Singleton

@Module
@InstallIn(SingletonComponent::class)
object DatabaseModule {

    @Provides
    @Singleton
    fun provideDatabase(@ApplicationContext context: Context): PostyarDatabase {
        return Room.databaseBuilder(
            context,
            PostyarDatabase::class.java,
            "postyar.db"
        ).build()
    }

    @Provides
    fun provideUserDao(db: PostyarDatabase): UserDao = db.userDao()

    @Provides
    fun provideChannelDao(db: PostyarDatabase): ChannelDao = db.channelDao()

    @Provides
    fun providePostDao(db: PostyarDatabase): PostDao = db.postDao()

    @Provides
    fun provideNotificationDao(db: PostyarDatabase): NotificationDao = db.notificationDao()
}
