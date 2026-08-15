# Gradle & Build Configuration Review

**Project:** PostyarAndroid (`com.postyar.app`)
**Date:** 2026-08-14
**Reviewer:** Subagent (Gradle & Build Config Reviewer)

---

## Versions Detected

| Component | Version |
|---|---|
| Gradle Wrapper | 8.5 |
| AGP | 8.1.1 |
| Kotlin | 1.9.22 |
| compileSdk / targetSdk | 34 |
| minSdk | 24 |
| JVM Target | 17 |

---

## CRITICAL Issues (Will Prevent Compilation)

### C1. Missing Compose Compiler Plugin
**Root `build.gradle`:** The `org.jetbrains.kotlin.plugin.compose` plugin is **NOT** declared in the `buildscript.dependencies` block, and it is **NOT** applied in `app/build.gradle`. 

Without this plugin, no `@Composable` function can compile. If the source code uses Jetpack Compose (the task states this is a "Kotlin + Jetpack Compose project"), the build will fail immediately with unresolved `@Composable` annotations.

**Fix:**
```groovy
// Root build.gradle — add to buildscript.dependencies:
classpath 'org.jetbrains.kotlin:compose-compiler-gradle-plugin:1.9.22'

// app/build.gradle — add to plugins block:
id 'org.jetbrains.kotlin.plugin.compose'
```

### C2. Missing ALL Compose Dependencies
**`app/build.gradle`:** Zero Compose libraries are declared. The following are all absent:

| Dependency | Purpose | Missing? |
|---|---|---|
| `androidx.activity:activity-compose` | `ComponentActivity` Compose support | ✅ |
| `androidx.compose.ui:ui` | Core Compose UI toolkit | ✅ |
| `androidx.compose.material3:material3` | Material Design 3 components | ✅ |
| `androidx.compose.runtime:runtime` | Compose runtime (implicit via BOM, but required) | ✅ |
| `androidx.compose.foundation:foundation` | Foundation layout/components | ✅ |
| `androidx.navigation:navigation-compose` | Compose Navigation integration | ✅ |
| `androidx.compose.ui:ui-tooling-preview` | Preview support (`@Preview`) | ✅ |

The project has **only** non-Compose navigation (`navigation-fragment-ktx`, `navigation-ui-ktx`) and traditional View-system dependencies (`appcompat`, `constraintlayout`, `recyclerview`, `cardview`, `viewBinding`).

**Fix:** Add a Compose BOM and all required dependencies (see recommended fix at end).

### C3. Missing `composeOptions` Block
**`app/build.gradle`:** There is no `composeOptions { kotlinCompilerExtensionVersion = "1.5.8" }` block.

For Kotlin 1.9.22, the Compose Compiler extension version must be **1.5.8**. Without declaring this (or applying the `org.jetbrains.kotlin.plugin.compose` plugin which makes it automatic), the compiler won't know which Compose compiler to use.

**Fix:** Either apply the Compose compiler plugin (preferred for Kotlin 1.9.22+) or add the `composeOptions` block.

### C4. Compose Not Enabled in `buildFeatures`
**`app/build.gradle`:** The `buildFeatures` block enables `viewBinding` and `buildConfig` but **NOT** `compose = true`.

Without this, the Compose tooling and compiler integration is not activated.

**Fix:**
```groovy
buildFeatures {
    compose true
    buildConfig true
}
```

### C5. Missing Hilt Plugin and Dependencies
**`app/build.gradle`:** No Hilt/Dagger plugin or dependency is declared anywhere.

If the source code uses `@HiltAndroidApp`, `@AndroidEntryPoint`, or `@Inject`, the build will fail.

**Fix:**
```groovy
// Root build.gradle:
classpath 'com.google.dagger:hilt-android-gradle-plugin:2.50'

// app/build.gradle plugins:
id 'com.google.dagger.hilt.android'
id 'kotlin-kapt'

// app/build.gradle dependencies:
implementation 'com.google.dagger:hilt-android:2.50'
kapt 'com.google.dagger:hilt-compiler:2.50'
implementation 'androidx.hilt:hilt-navigation-compose:1.1.0'
```

### C6. Missing Room Dependencies
**`app/build.gradle`:** No Room database dependencies are declared.

If source code uses `@Entity`, `@Dao`, `@Database`, or `Room.databaseBuilder`, the build will fail.

**Fix:**
```groovy
implementation 'androidx.room:room-runtime:2.6.1'
implementation 'androidx.room:room-ktx:2.6.1'
kapt 'androidx.room:room-compiler:2.6.1'
```

### C7. No `kotlin-kapt` Plugin Applied
**`app/build.gradle`:** The `kotlin-kapt` plugin is not in the plugins block.

Hilt and Room both require `kapt` for annotation processing. Even if Hilt/Room deps are added, compilation will fail without this plugin.

**Fix:** Add `id 'kotlin-kapt'` to the plugins block.

---

## WARNING Issues (May Cause Runtime/Feature Issues)

### W1. Compose BOM Not Used — Version Drift Risk
Even after adding Compose dependencies, managing each version individually risks incompatibility. The Compose BOM (`androidx.compose:compose-bom:2024.01.00`) ensures all Compose libraries are compatible.

### W2. Traditional View Dependencies Coexist with Compose
The project includes `appcompat`, `constraintlayout`, `recyclerview`, `cardview`, `swiperefreshlayout`, and `viewBinding`. These are View-system artifacts. Mixing View and Compose in the same module works but requires `ComposeView` as a bridge and can lead to confusion and increased APK size.

**Recommendation:** Remove View-system dependencies once full Compose migration is complete, or isolate them in a separate module.

### W3. `security-crypto:1.1.0-alpha06` Is an Alpha Dependency
Using an alpha version in production can lead to API changes and instability.

**Recommendation:** Check if a stable version is available; otherwise document the risk acceptance.

### W4. `datastore-preferences:1.0.0` Is Outdated
Current stable version is **1.1.1** (as of early 2024). Version 1.0.0 may lack bug fixes and Kotlin coroutines improvements.

### W5. Missing `debugImplementation` Compose Tooling
For Compose development, `ui-tooling` (debug only) is essential for layout inspector and interactive preview. This should be added as a `debugImplementation` dependency.

### W6. AGP 8.1.1 With Kotlin 1.9.22 — Minor Version Mismatch
AGP 8.1.x officially pairs with Kotlin up to 1.9.20. Kotlin 1.9.22 works but is not officially tested with AGP 8.1.1. AGP 8.2+ is recommended for Kotlin 1.9.22.

### W7. No ProGuard Rules for Compose, Hilt, or Room
`minifyEnabled true` is set in the release build type, but there are no visible ProGuard/R8 rules for:
- Compose (required to prevent stripping Compose runtime classes)
- Hilt (required for dependency injection reflection)
- Room (required for entity/DAO reflection)
- Gson (required for serialized model classes)

Without these, the release build will crash at runtime.

---

## INFO Issues (Best Practice Improvements)

### I1. Use `plugins {}` Block in Root `build.gradle` Instead of `buildscript`
The `buildscript {}` pattern is legacy. Modern Gradle uses the `plugins {}` block with `pluginManagement` in `settings.gradle`. This is already partially done in `settings.gradle` (which has `pluginManagement`), but the root `build.gradle` still uses the old pattern.

### I2. Consider Kotlin 2.0+ With Compose Compiler Integrated Into Kotlin
Kotlin 2.0+ integrates the Compose compiler directly — no separate plugin needed. This would simplify the build configuration.

### I3. Missing `androidx.compose:compose-bom` In `dependencyResolutionManagement`
For centralized dependency management, consider using a version catalog (`libs.versions.toml`) instead of hardcoding versions.

### I4. JVM Target 17 Is Appropriate
Java 17 is the correct target for AGP 8.x. ✅ No issue here.

### I5. Gradle 8.5 Is Compatible With AGP 8.1.1
This pairing is correct. ✅ No issue here.

### I6. Consider Adding `android:enableAggregatingTasks` to `gradle.properties`
This can improve build performance.

---

## Summary

| Category | Count |
|---|---|
| CRITICAL | 7 |
| WARNING | 7 |
| INFO | 6 |

## Recommended Fix: Complete `app/build.gradle` Dependencies

Below is the minimal set of additions needed to make this project compilable as a Compose + Hilt + Room project:

```groovy
plugins {
    id 'com.android.application'
    id 'org.jetbrains.kotlin.android'
    id 'org.jetbrains.kotlin.plugin.compose'  // NEW
    id 'com.google.dagger.hilt.android'       // NEW
    id 'kotlin-kapt'                           // NEW
}

android {
    // ... existing config ...
    buildFeatures {
        compose true   // NEW — replaces viewBinding
        buildConfig true
    }
    // Remove composeOptions block if using plugin (Kotlin 1.9.22+)
    // Otherwise add: composeOptions { kotlinCompilerExtensionVersion '1.5.8' }
}

dependencies {
    // --- Existing (keep these) ---
    implementation 'androidx.core:core-ktx:1.12.0'
    implementation 'androidx.lifecycle:lifecycle-viewmodel-ktx:2.7.0'
    implementation 'androidx.lifecycle:lifecycle-runtime-ktx:2.7.0'
    implementation 'androidx.lifecycle:lifecycle-livedata-ktx:2.7.0'
    implementation 'androidx.security:security-crypto:1.1.0-alpha06'
    implementation 'androidx.datastore:datastore-preferences:1.1.1' // updated
    implementation 'com.squareup.retrofit2:retrofit:2.9.0'
    implementation 'com.squareup.retrofit2:converter-gson:2.9.0'
    implementation 'com.squareup.okhttp3:okhttp:4.12.0'
    implementation 'com.squareup.okhttp3:logging-interceptor:4.12.0'
    implementation 'com.google.code.gson:gson:2.10.1'
    implementation 'io.coil-kt:coil:2.6.0'
    // Replace with compose-coil if migrating fully to Compose

    // --- Compose (ALL NEW) ---
    def composeBom = platform('androidx.compose:compose-bom:2024.02.00')
    implementation composeBom
    androidTestImplementation composeBom
    implementation 'androidx.activity:activity-compose:1.8.2'
    implementation 'androidx.compose.ui:ui'
    implementation 'androidx.compose.ui:ui-graphics'
    implementation 'androidx.compose.ui:ui-tooling-preview'
    implementation 'androidx.compose.material3:material3'
    implementation 'androidx.compose.foundation:foundation'
    implementation 'androidx.navigation:navigation-compose:2.7.7'
    debugImplementation 'androidx.compose.ui:ui-tooling'

    // --- Hilt (ALL NEW) ---
    implementation 'com.google.dagger:hilt-android:2.50'
    kapt 'com.google.dagger:hilt-compiler:2.50'
    implementation 'androidx.hilt:hilt-navigation-compose:1.1.0'

    // --- Room (ALL NEW) ---
    implementation 'androidx.room:room-runtime:2.6.1'
    implementation 'androidx.room:room-ktx:2.6.1'
    kapt 'androidx.room:room-compiler:2.6.1'
}
```

And in **root `build.gradle`**, add to `buildscript.dependencies`:
```groovy
classpath 'com.google.dagger:hilt-android-gradle-plugin:2.50'
classpath 'org.jetbrains.kotlin:compose-compiler-gradle-plugin:1.9.22'
```
