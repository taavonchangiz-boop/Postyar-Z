# Keep BuildConfig
-keep class ir.belitia.whcm.BuildConfig { *; }

# Keep WebView related classes
-keepclassmembers class * {
    @android.webkit.JavascriptInterface <methods>;
}

# Keep AndroidX
-keep class androidx.appcompat.** { *; }
-keep class com.google.android.material.** { *; }
