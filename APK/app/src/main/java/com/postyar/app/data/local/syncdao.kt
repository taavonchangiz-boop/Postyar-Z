package com.postyar.app.data.local

import androidx.room.*
import kotlinx.coroutines.flow.Flow

@Dao
interface UserDao {
    @Query("SELECT * FROM users WHERE id = :id")
    suspend fun getById(id: Int): UserEntity?

    @Query("SELECT * FROM users WHERE id = :id")
    fun observeById(id: Int): Flow<UserEntity?>

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertOrUpdate(user: UserEntity)

    @Query("DELETE FROM users WHERE id = :id")
    suspend fun delete(id: Int)

    @Query("DELETE FROM users")
    suspend fun deleteAll()
}

@Dao
interface ChannelDao {
    @Query("SELECT * FROM channels ORDER BY id DESC")
    suspend fun getAll(): List<ChannelEntity>

    @Query("SELECT * FROM channels ORDER BY id DESC")
    fun observeAll(): Flow<List<ChannelEntity>>

    @Query("SELECT * FROM channels WHERE id = :id")
    suspend fun getById(id: Int): ChannelEntity?

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertOrUpdate(channel: ChannelEntity)

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertOrUpdateAll(channels: List<ChannelEntity>)

    @Query("DELETE FROM channels WHERE id = :id")
    suspend fun delete(id: Int)

    @Query("DELETE FROM channels")
    suspend fun deleteAll()
}

@Dao
interface PostDao {
    @Query("SELECT * FROM posts ORDER BY id DESC")
    suspend fun getAll(): List<PostEntity>

    @Query("SELECT * FROM posts WHERE status = :status ORDER BY id DESC")
    suspend fun getByStatus(status: String): List<PostEntity>

    @Query("SELECT * FROM posts ORDER BY id DESC")
    fun observeAll(): Flow<List<PostEntity>>

    @Query("SELECT * FROM posts WHERE id = :id")
    suspend fun getById(id: Int): PostEntity?

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertOrUpdate(post: PostEntity)

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertOrUpdateAll(posts: List<PostEntity>)

    @Query("DELETE FROM posts WHERE id = :id")
    suspend fun delete(id: Int)

    @Query("DELETE FROM posts")
    suspend fun deleteAll()
}

@Dao
interface NotificationDao {
    @Query("SELECT * FROM notifications ORDER BY id DESC")
    suspend fun getAll(): List<NotificationEntity>

    @Query("SELECT * FROM notifications ORDER BY id DESC")
    fun observeAll(): Flow<List<NotificationEntity>>

    @Query("SELECT COUNT(*) FROM notifications WHERE is_read = 0")
    suspend fun unreadCount(): Int

    @Query("SELECT COUNT(*) FROM notifications WHERE is_read = 0")
    fun observeUnreadCount(): Flow<Int>

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertOrUpdate(notification: NotificationEntity)

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertOrUpdateAll(notifications: List<NotificationEntity>)

    @Query("UPDATE notifications SET is_read = 1 WHERE id = :id")
    suspend fun markRead(id: Int)

    @Query("UPDATE notifications SET is_read = 1")
    suspend fun markAllRead()

    @Query("DELETE FROM notifications")
    suspend fun deleteAll()
}