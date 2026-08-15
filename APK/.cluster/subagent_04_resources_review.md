# Subagent 04: Resource & Manifest Review

## 1. AndroidManifest.xml

### 1.1 Filename Case — **CONFIRMED BUG**
- **Actual filename:** `androidmanifest.xml` (all lowercase)
- **Required filename:** `AndroidManifest.xml` (capital A, capital M)
- **Impact:** CRITICAL. The Android build system (Gradle / AAPT2) expects the exact filename `AndroidManifest.xml`. A lowercase filename **will not be found** by the build tools. This alone prevents compilation. This was likely introduced when the APK was reverse-decoded (apktool often lowercases filenames).

### 1.2 Application Class Declaration
- `android:name=".PostyarApp"` ✅
- Maps to `com.postyar.app.PostyarApp` — confirmed: `postyarapp.kt` exists at `java/com/postyar/app/postyarapp.kt` and declares `class PostyarApp : Application()`.
- **Issue:** The class is essentially empty (`onCreate` calls `super` only). No DI (Hilt/Koin) initialization, no Timber setup, no WorkManager configuration. For a Compose app using Room, Retrofit, ViewModels, this is suspicious — the app likely relies on late initialization or is incomplete.

### 1.3 Theme Reference
- `android:theme="@style/Theme.PostyarApp"` → resolved in `res/values/themes.xml` ✅ (file exists and declares this style)
- **BUT** the theme is wrong for a Compose app (see §2.4 below).

### 1.4 Launcher Activity
- `android:name=".presentation.MainActivity"` → resolves to `com.postyar.app.presentation.MainActivity` ✅
- File confirmed at `java/com/postyar/app/presentation/mainactivity.kt`
- Intent filter with `MAIN`/`LAUNCHER` ✅
- `configChanges` and `windowSoftInputMode` declared ✅
- **Missing:** No `android:theme` override on the activity (relies on application-level theme)

### 1.5 Declared Permissions
| Permission | Status |
|---|---|
| `INTERNET` | ✅ Declared |
| `ACCESS_NETWORK_STATE` | ✅ Declared |

### 1.6 Missing Permissions
| Permission | Severity | Reason |
|---|---|---|
| `POST_NOTIFICATIONS` | **HIGH** | The app has a full notifications screen (`NotificationScreen.kt`) and notification entities. On Android 13+ (API 33, targetSdk 34), posting notifications requires `android.permission.POST_NOTIFICATIONS` at runtime. Without declaring it in the manifest, the permission request will crash or silently fail. |
| `FOREGROUND_SERVICE` | MEDIUM | If the app uses sync/workers that need foreground services, this is needed. |
| `RECEIVE_BOOT_COMPLETED` | LOW | If auto-sync on boot is intended. |
| `VIBRATE` | LOW | Common for notifications. |

### 1.7 roundIcon Reference — **CONFIRMED BUG**
- Manifest declares: `android:roundIcon="@mipmap/ic_launcher_round"`
- **File does NOT exist.** Complete mipmap inventory:
  - `res/mipmap-hdpi/ic_launcher.png` ✅
  - `res/mipmap-mdpi/ic_launcher.png` ✅
  - `res/mipmap-xhdpi/ic_launcher.png` ✅
  - `res/mipmap-xxhdpi/ic_launcher.png` ✅
  - `res/mipmap-xxxhdpi/ic_launcher.png` ✅
  - `ic_launcher_round.png` — **MISSING from ALL density buckets**
- **Impact:** BUILD FAILURE. AAPT2 will fail with "resource not found" for `@mipmap/ic_launcher_round`.
- **Fix:** Either add `ic_launcher_round.png` to all 5 mipmap folders, or change the manifest to `android:roundIcon="@mipmap/ic_launcher"` (use the regular icon as round icon).

### 1.8 Other Manifest Issues
- **No `package` attribute** on `<manifest>`. The Gradle build system can inject this from `build.gradle`/`namespace`, but a standalone manifest needs it. The `manual-build` manifest has `package="com.postyar.app"` — the Gradle version relies on the build file.
- **No `uses-sdk` declaration.** Relies on Gradle `compileSdk`/`minSdk`/`targetSdk`. Acceptable for Gradle builds.
- **No `android:usesCleartextTraffic="true"`.** The API base URL is `https://asovin.ir` (HTTPS), so this is fine. But if any dev/debug endpoint uses HTTP, this would be needed.

---

## 2. res/ Directory

### 2.1 Complete File Listing
```
res/
├── mipmap-hdpi/ic_launcher.png
├── mipmap-mdpi/ic_launcher.png
├── mipmap-xhdpi/ic_launcher.png
├── mipmap-xxhdpi/ic_launcher.png
├── mipmap-xxxhdpi/ic_launcher.png
└── values/
    ├── strings.xml
    └── themes.xml
```

**Notable absences:**
- No `drawable/` directory
- No `mipmap-anydpi-v26/` (adaptive icons for API 26+)
- No `values-night/` (dark theme resources)
- No `values-land/`, `values-fa/`, `values-ar/` (localization)
- No `xml/` directory (no backup rules, no network security config)

### 2.2 ic_launcher_round.png — **MISSING** (confirmed above)

### 2.3 themes.xml — **WRONG FOR COMPOSE**
```xml
<style name="Theme.PostyarApp" parent="Theme.AppCompat.Light.NoActionBar">
    <item name="colorPrimary">#6366F1</item>
    <item name="colorPrimaryDark">#4F46E5</item>
    <item name="colorAccent">#6366F1</item>
</style>
```

**Issues:**
1. **Wrong parent.** A Jetpack Compose app using Material3 should NOT use `Theme.AppCompat.Light.NoActionBar`. The XML theme is only needed as a bridge for `Activity.setContent()`. The correct approach:
   - For Material3 Compose: parent should be `Theme.Material3.DayNight.NoActionBar` (from `com.google.android.material:material`)
   - OR, if using pure Compose theming with `MaterialTheme(composable)`, a minimal bridge theme like `android:Theme.Material.Light.NoActionBar` suffices since Compose handles all theming internally.
   - Using `Theme.AppCompat` requires the AppCompat library as a dependency even if Compose doesn't use it.

2. **colorPrimaryDark is deprecated** in Material3 (it was a Material2/AppCompat concept).

3. **No dark theme variant.** Only one theme is declared. A proper app targeting API 34 should have `values-night/themes.xml`.

4. **Compose already has its own theme** in `ui/theme/theme.kt`. The XML theme duplicates/conflicts with the Compose theme. The XML theme only needs to provide a minimal window/background bridge.

### 2.4 strings.xml — Minimal but Sufficient
```xml
<string name="app_name">پُست‌یار</string>
```
- Only the app name is declared. All other strings appear to be hardcoded in Compose composables (Kotlin code).
- **Risk:** This prevents localization. For a Persian/RTL app, all strings should be in `strings.xml` with `values-fa/` for proper i18n.

### 2.5 colors.xml — **DOES NOT EXIST**
- The XML theme references hardcoded hex colors (`#6366F1`, `#4F46E5`).
- For a pure Compose app, this is acceptable since `theme.kt` defines colors programmatically.
- However, the XML theme should ideally reference color resources for consistency.

### 2.6 XML Layout Files — None in main/res (Correct)
- **No `res/layout/` directory** in the Gradle project's `main/res/`. ✅
- This is correct for a pure Compose app — all UI is defined in Kotlin composables.
- Contrast with `manual-build/res/layout/activity_main.xml` which uses traditional Views.

---

## 3. manual-build/ Directory

### 3.1 Overview
The `manual-build/` directory at the project root is a **completely separate, self-contained build** of the app, built using a non-Gradle toolchain (likely `aapt` + `javac` + `dx` + `apksigner` manually). It is NOT part of the Gradle project structure.

### 3.2 Contents
```
manual-build/
├── androidmanifest.xml          # Separate manifest (differs from Gradle version)
├── debug.keystore               # Signing keystore
├── compiled_resources.zip       # Pre-compiled resources
├── src/mainactivity.java        # JAVA source (not Kotlin!)
├── res/
│   ├── layout/activity_main.xml # Traditional XML layout
│   ├── mipmap-{hdpi..xxxhdpi}/ic_launcher.png
│   └── values/styles.xml        # Different theme (AppTheme, not Theme.PostyarApp)
├── classes/com/postyar/app/     # Compiled .class files
│   ├── MainActivity.class
│   ├── MainActivity$BootstrapTask.class
│   └── R*.class
├── dex/classes.dex              # DEX bytecode
└── bin/
    ├── app-base.apk
    ├── app-aligned.apk
    └── Postyar-Z-Android-debug.apk  # The actual APK artifact
```

### 3.3 Key Differences: manual-build vs Gradle Project

| Aspect | manual-build | Gradle Project |
|---|---|---|
| **Language** | Java (`mainactivity.java`) | Kotlin (`*.kt`) |
| **Architecture** | Single `Activity` with `AsyncTask` | Full MVVM + Clean Architecture |
| **UI Framework** | Traditional XML layouts | Jetpack Compose |
| **Theme** | `android:Theme.DeviceDefault.Light.NoActionBar` | `Theme.AppCompat.Light.NoActionBar` |
| **App Class** | None (no Application subclass) | `PostyarApp : Application()` |
| **roundIcon** | `@mipmap/ic_launcher` (no round variant) | `@mipmap/ic_launcher_round` (missing file!) |
| **Features** | Bootstrap API call, settings dialog, open web panel | Full app: auth, posts, channels, billing, admin, wallet, tickets, analytics, notifications, referral |
| **Networking** | Raw `HttpURLConnection` + `AsyncTask` | Retrofit + OkHttp + AuthInterceptor |
| **Data Storage** | `SharedPreferences` only | Room database + entities + DAOs |
| **Package** | `com.postyar.app` (in manifest) | `com.postyar.app` (from namespace) |
| **API Base** | `https://asovin.ir/api/v1/` (hardcoded) | Configured via Retrofit (likely in code) |

### 3.4 Assessment

The `manual-build/` is clearly the **original prototype** — a minimal Java app thrown together quickly (possibly in a day) to produce a working APK. It uses:
- **Deprecated patterns:** `AsyncTask` (deprecated since API 30), raw HTTP, no error handling
- **No architecture:** Everything in one Activity class (~200 lines)
- **Minimal UI:** Single screen with hardcoded Persian text

The Gradle project is the **intended production version** — a proper Kotlin/Compose app with clean architecture. However, the Gradle project has critical build issues (wrong manifest filename, missing round icon) and was apparently never successfully compiled via Gradle.

### 3.5 The Actual APK
The file `Postyar-Z-Android-debug.apk` in the project root was almost certainly built from `manual-build/bin/Postyar-Z-Android-debug.apk` (they're likely identical). The Gradle project has never produced a working APK — its manifest and resource issues would prevent compilation.

### 3.6 Useful Config from manual-build
- **`debug.keystore`** — The signing keystore used for the APK. The Gradle project should reference this or generate its own.
- **`minSdkVersion=24`, `targetSdkVersion=34`** — Confirmed SDK levels that the Gradle project must match.
- **`android:roundIcon="@mipmap/ic_launcher"`** — The manual-build correctly avoids referencing a non-existent round icon. The Gradle manifest should follow this pattern or add the actual round icon files.

---

## Summary of Critical Issues (Must Fix to Build)

| # | Issue | Severity | Fix |
|---|---|---|---|
| 1 | Filename `androidmanifest.xml` (lowercase) | **CRITICAL** | Rename to `AndroidManifest.xml` |
| 2 | Missing `ic_launcher_round.png` in all mipmap folders | **CRITICAL** | Add round icons OR change `roundIcon` to reference existing `@mipmap/ic_launcher` |
| 3 | Wrong XML theme parent for Compose app | **HIGH** | Change to `Theme.Material3.DayNight.NoActionBar` or minimal bridge |
| 4 | Missing `POST_NOTIFICATIONS` permission | **HIGH** | Add to manifest for Android 13+ notification support |
| 5 | Empty `PostyarApp` Application class | **MEDIUM** | Initialize DI, database, etc. |
