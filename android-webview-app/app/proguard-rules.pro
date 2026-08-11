# Postyar ProGuard Rules
-keepattributes *Annotation*
-keep class ir.asovin.postyar.** { *; }
-dontwarn com.google.android.material.**
-keep class com.google.android.material.** { *; }
-keepclassmembers class * {
    @android.webkit.JavascriptInterface <methods>;
}