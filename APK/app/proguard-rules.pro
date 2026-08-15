# Retrofit
-keepattributes Signature
-keepattributes *Annotation*
-keep class com.postyar.app.data.remote.** { *; }
-keep class com.postyar.app.domain.** { *; }
-dontwarn retrofit2.**
-keep class retrofit2.** { *; }
-keepclasseswithmembers class * {
    @retrofit2.http.* <methods>;
}

# Gson
-keepattributes Signature
-keepattributes *Annotation*
-keep class com.google.gson.** { *; }
-keep class * implements com.google.gson.TypeAdapter { *; }
-keep class * implements com.google.gson.TypeAdapterFactory { *; }
-keep class * implements com.google.gson.JsonSerializer { *; }
-keep class * implements com.google.gson.JsonDeserializer { *; }
-keep class com.postyar.app.domain.** { *; }

# Hilt
-keep class dagger.hilt.** { *; }
-keep class javax.inject.** { *; }
-dontwarn dagger.hilt.android.internal.**
-keep class * extends dagger.hilt.android.internal.managers.ViewComponentManager$FragmentContextWrapper { *; }

# OkHttp
-dontwarn okhttp3.**
-dontwarn okio.**
-keep class okhttp3.** { *; }
-keep class okio.** { *; }

# Coroutines
-keepnames class kotlinx.coroutines.internal.MainDispatcherFactory {}
-keepnames class kotlinx.coroutines.CoroutineExceptionHandler {}

# Room
-keep class * extends androidx.room.RoomDatabase
-dontwarn androidx.room.paging.**

# EncryptedSharedPreferences / security-crypto
-keep class androidx.security.crypto.** { *; }

# Moshi (for dead DTO code)
-keep class com.postyar.app.data.remote.dto.** { *; }
-keepattributes Signature
-keep @com.squareup.moshi.Json class ** { *; }
-keepclassmembers class ** {
    @com.squareup.moshi.Json <fields>;
}

# Coil
-keep class coil.** { *; }
-dontwarn coil.**
