# 🔑 راهنمای امضای دیجیتال APK

برای انتشار در Google Play یا نصب خارج از آن، APK باید امضا شود.

## راه‌حل ۱: ساخت و امضای محلی (توصیه‌شده)

### ۱. ساخت Keystore
```bash
keytool -genkey -v -keystore postyar-release.jks -keyalg RSA -keysize 2048 -validity 10000 -alias postyar
```

### ۲. افزودن به build.gradle
در فایل `app/build.gradle` بخش `signingConfigs` و `buildTypes` را اضافه کنید:

```groovy
android {
    signingConfigs {
        release {
            storeFile file("../postyar-release.jks")
            storePassword System.getenv("KEYSTORE_PASSWORD") ?: "YOUR_PASSWORD"
            keyAlias "postyar"
            keyPassword System.getenv("KEY_PASSWORD") ?: "YOUR_PASSWORD"
        }
    }
    buildTypes {
        release {
            signingConfig signingConfigs.release
            minifyEnabled true
            shrinkResources true
            proguardFiles getDefaultProguardFile('proguard-android-optimize.txt'), 'proguard-rules.pro'
        }
    }
}
```

### ۳. ساخت با Gradle
```bash
cd android-webview-app
./gradlew assembleRelease
```

APK خروجی در مسیر `app/build/outputs/apk/release/` قرار دارد.

---

## راه‌حل ۲: ساخت از طریق GitHub Actions

### ۱. تنظیم Secrets در GitHub

به مسیر `Settings > Secrets and variables > Actions` در ریپازیتوری بروید و این Secrets را اضافه کنید:

| Secret Name | محتوا |
|-------------|-------|
| `KEYSTORE_BASE64` | `base64 -w0 postyar-release.jks` |
| `KEYSTORE_PASSWORD` | رمز keystore |
| `KEY_PASSWORD` | رمز کلید |

### ۲. تگ بزنید و پوش کنید
```bash
git tag v1.1.0
git push origin v1.1.0
```

GitHub Actions به‌صورت خودکار APK را می‌سازد و در Releases آپلود می‌کند.

---

## نصب APK روی گوشی

### از طریق USB
```bash
adb install postyar-v1.1.0-release.apk
```

### از طریق فایل
فایل APK را به گوشی منتقل کنید و از File Manager نصب کنید.

⚠️  باید در تنظیمات گوشی: **منابع ناشناخته > اجازه نصب** فعال باشد.
