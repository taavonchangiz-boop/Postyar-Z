# Source Code Review: PostYar Android APK

**Scope:** All 94 `.kt` files under `app/src/main/java/com/`  
**Date:** 2026-08-14  
**Reviewer:** Subagent 02 (Automated)

---

## Executive Summary

The codebase is **structurally impossible to compile as-is**. There are two fundamentally incompatible architectures coexisting: a legacy system using `AndroidViewModel` + manual singleton DI + Gson (`domain/models.kt` + `retrofitclient.kt` + all ViewModels), and a modern system using `@HiltViewModel` + Hilt DI + Moshi DTOs (`core/security/tokenmanager.kt` + `core/network/authinterceptor.kt` + all `dto/` files + all API interfaces + most screen composables). The `build.gradle` matches neither system fully. Below is the full breakdown.

---

## 1. Missing Dependencies in `build.gradle`

The build.gradle declares:
- ✅ Retrofit + OkHttp + Gson converter
- ✅ Coil 2.6
- ✅ Lifecycle ViewModel/LiveData/Runtime
- ✅ Security Crypto + DataStore
- ✅ Material 1.x + AppCompat + ConstraintLayout
- ✅ Navigation Fragment (non-Compose) + Navigation UI

**CRITICAL MISSING dependencies:**

| Dependency | Used By | Severity |
|---|---|---|
| `androidx.compose.*` (BOM or individual) | ALL 30+ screen/component files, theme.kt, MainActivity | **BUILD BREAKER** |
| `androidx.compose.material3:material3` | All screens, components, theme.kt | **BUILD BREAKER** |
| `androidx.compose.ui:ui-*` | All Compose files | **BUILD BREAKER** |
| `androidx.activity:activity-compose` | MainActivity (`ComponentActivity`, `enableEdgeToEdge`, `setContent`) | **BUILD BREAKER** |
| `androidx.navigation:navigation-compose` | PostyarNavigation, MainActivity | **BUILD BREAKER** |
| `com.google.dagger:hilt-android` | TokenManager (Hilt), AuthInterceptor (Hilt), MainActivity (`@AndroidEntryPoint`) | **BUILD BREAKER** |
| `com.google.dagger:hilt-android-compiler` (kapt) | Required for Hilt annotation processing | **BUILD BREAKER** |
| `androidx.hilt:hilt-navigation-compose` | All screens using `hiltViewModel()` | **BUILD BREAKER** |
| `androidx.lifecycle:lifecycle-runtime-compose` | Screens using `collectAsStateWithLifecycle` | **BUILD BREAKER** |
| `androidx.room:room-runtime` | Entities, Database, DAOs | **BUILD BREAKER** |
| `androidx.room:room-ktx` | Coroutines support in DAOs | **BUILD BREAKER** |
| `androidx.room:room-compiler` (kapt) | Room annotation processing | **BUILD BREAKER** |
| `com.squareup.moshi:moshi-kotlin` | ALL DTO files use `@JsonClass(generateAdapter = true)` from Moshi | **BUILD BREAKER** |
| `com.squareup.moshi:moshi-kotlin-codegen` (kapt) | Moshi code generation | **BUILD BREAKER** |
| `org.jetbrains.kotlinx:kotlinx-coroutines-android` | All ViewModels use `viewModelScope.launch` | **BUILD BREAKER** |
| `androidx.compose.material:material-icons-extended` | Screens using `Icons.Default.*` (Dashboard, Sensors, CheckCircle, Block, AttachFile, Send, ContentCopy, AddCircle, Article, Refresh, Search, Delete, Edit, Add, ArrowDownward, ArrowUpward, DoneAll) | **BUILD BREAKER** |

**Wrong/incompatible dependencies in build.gradle:**

| Dependency | Problem |
|---|---|
| `com.google.android.material:material:1.11.0` | This is the View-based Material library. The code uses Compose Material3. Having this isn't harmful but is dead weight. |
| `androidx.constraintlayout:constraintlayout:2.1.4` | Not used anywhere in the Kotlin source (all Compose layouts). Dead dependency. |
| `androidx.recyclerview:recyclerview:1.3.2` | Not used (Compose LazyColumn used instead). Dead dependency. |
| `androidx.swiperefreshlayout:swiperefreshlayout:1.1.0` | Not used. Dead dependency. |
| `androidx.cardview:cardview:1.0.0` | Not used (Compose Card used instead). Dead dependency. |
| `androidx.appcompat:appcompat:1.6.1` | Not directly used in any source file. Dead dependency. |
| `androidx.navigation:navigation-fragment-ktx` | Fragment-based navigation; code uses Compose navigation. Wrong variant. |
| `androidx.navigation:navigation-ui-ktx` | Same - Fragment-based. Wrong variant. |
| `androidx.datastore:datastore-preferences:1.0.0` | Not used anywhere (token storage uses EncryptedSharedPreferences). Dead dependency. |
| `viewBinding true` in buildFeatures | No XML layouts are referenced. Useless. |

---

## 2. Duplicate Class Definitions

### 🔴 CRITICAL: `TokenManager` defined TWICE

**Location 1:** `core/security/tokenmanager.kt` (package: `com.postyar.app.core.security`)
- Uses **Hilt annotations**: `@Singleton`, `@Inject constructor`, `@ApplicationContext`
- Uses `MasterKey.Builder(context).setKeyScheme(MasterKey.KeyScheme.AES256_GCM)`
- Prefs file: `"postyar_secure_prefs.xml"`
- Key name: `"***"`

**Location 2:** `data/remote/retrofitclient.kt` (package: `com.postyar.app.data.remote`)
- Plain singleton pattern with `companion object` + synchronized
- Uses `MasterKeys.getOrCreate(MasterKeys.AES256_GCM_SPEC)` (deprecated API)
- Prefs file: `"postyar_secure_prefs"`
- Key name: `"auth_token"`

**Also in retrofitclient.kt:** `AuthInterceptor` defined TWICE:
- **Location A:** `core/network/authinterceptor.kt` — Hilt `@Singleton` + `@Inject constructor(TokenManager from core.security)`
- **Location B:** `data/remote/retrofitclient.kt` — Plain class `AuthInterceptor(TokenManager from data.remote)`

These are in different packages with different constructors but same class names — this creates ambiguity when both are on the classpath.

### 🟡 Duplicate `LoginRequest` and `RegisterRequest`

- **Version A:** In `domain/models.kt` (package `com.postyar.app.domain`) — uses Gson-style snake_case field names
- **Version B:** In `data/remote/api/authapi.kt` (package `com.postyar.app.data.remote.api`) — uses camelCase field names

These have the same simple class name but different packages and different field naming conventions.

### 🟡 Duplicate `ApiResponse`

- **Version A:** In `domain/models.kt` — fields: `success`, `message`, `data` (Gson-style, no `@SerializedName`, no `errors` field)
- **Version B:** In `data/remote/dto/apiresponse.kt` — uses `@JsonClass(generateAdapter = true)` with Moshi `@Json` annotations, includes `errors: Map<String, String>?`

---

## 3. Type Mismatches Between ViewModels and Screens

This is the **most pervasive issue**. All 13 ViewModels use `AndroidViewModel(application)` with manual `RetrofitClient.getInstance(TokenManager.getInstance(application))` and `ApiService` (the monolithic interface from `domain/models.kt` using domain model classes like `User`, `Post`, `Channel`, `Ticket`, etc.). But the **screen composables** reference DTO types from `data.remote.dto.*` and some screens use `hiltViewModel()`.

### Screen ↔ ViewModel Type Conflicts:

| Screen | Expects From ViewModel | ViewModel Actually Provides | Issue |
|---|---|---|---|
| `AdminPaymentsScreen` | `adminViewModel.allPayments: StateFlow<List<PaymentDto>>` | `AdminViewModel.payments: StateFlow<List<Payment>>` (domain model) | **Type mismatch** — `Payment` vs `PaymentDto`; also property is `payments` not `allPayments` |
| `AdminPlansScreen` | `adminViewModel.plans: StateFlow<List<PlanDto>>` | `AdminViewModel.plans: StateFlow<List<Plan>>` | **Type mismatch** — `Plan` vs `PlanDto` |
| `AdminTicketsScreen` | `adminViewModel.allTickets: StateFlow<List<TicketDto>>` | `AdminViewModel.tickets: StateFlow<List<Ticket>>` | **Type mismatch** — `Ticket` vs `TicketDto`; property name `allTickets` vs `tickets` |
| `AnalyticsScreen` | `analyticsViewModel.links: StateFlow<List<AnalyticsLinkDto>>` | `AnalyticsViewModel.links: StateFlow<List<LinkTracking>>` | **Type mismatch** |
| `AnalyticsScreen` (LinkDetailScreen) | `analyticsViewModel.linkDetail: StateFlow<AnalyticsLinkDetailDto?>` | `AnalyticsViewModel.linkDetail: StateFlow<LinkDetail?>` | **Type mismatch** — `LinkDetail` has `daily_breakdown: List<DailyClickStat>` but screen accesses `.dailyBreakdown` (Moshi-named) |
| `ChannelsScreen` | `channelViewModel.channels: StateFlow<List<ChannelDto>>` | `ChannelViewModel.channels: StateFlow<List<Channel>>` | **Type mismatch** |
| `NotificationsScreen` | `notificationViewModel.notifications: StateFlow<List<NotificationDto>>` | `NotificationViewModel.notifications: StateFlow<List<NotificationItem>>` | **Type mismatch** |
| `PaymentsScreen` | `billingViewModel.payments: StateFlow<List<PaymentDto>>` | `BillingViewModel.payments: StateFlow<List<Payment>>` | **Type mismatch** |
| `PlansScreen` | `billingViewModel.plans: StateFlow<List<PlanDto>>` | `BillingViewModel.plans: StateFlow<List<Plan>>` | **Type mismatch** |
| `DashboardScreen` | `bootstrapViewModel.bootstrapData`, `syncViewModel.syncData` | No such properties exist | **Missing properties** — ViewModel has individual fields, not a single `bootstrapData`/`syncData` flow |
| `DashboardScreen` | `bootstrapViewModel.unreadCount` | `BootstrapViewModel.unreadCount` exists but type is `MutableStateFlow<Int>` | ✅ Matches (but accessed via wrong aggregation property) |
| `ReferralScreen` | `referralViewModel.referral: StateFlow<ReferralDto?>` | `ReferralViewModel.referralData: StateFlow<ReferralData?>` | **Type mismatch** + property name mismatch |
| `WalletScreen` | `walletViewModel.wallet: StateFlow<WalletDto?>` | `WalletViewModel.walletData: StateFlow<WalletData?>` | **Type mismatch** + property name mismatch |
| `AutoResponderScreen` | `autoResponderViewModel.autoReplies: StateFlow<List<AutoReplyDto>>` | `AutoResponderViewModel.rules: StateFlow<List<AutoReplyRule>>` | **Type mismatch** + property name mismatch |
| `TicketsScreen` | `ticketViewModel.tickets: StateFlow<List<TicketDto>>` | `TicketViewModel.tickets: StateFlow<List<Ticket>>` | **Type mismatch** |
| `TicketDetailScreen` | `ticketViewModel.ticketDetail: StateFlow<TicketDetailDto?>` | `TicketViewModel.ticketDetail: StateFlow<TicketDetail?>` | **Type mismatch** |
| `PostsScreen` | `postViewModel.posts: StateFlow<List<PostDto>>` | `PostViewModel.posts: StateFlow<List<Post>>` | **Type mismatch** |
| `PostDetailScreen` | `postViewModel.postDetail: StateFlow<PostDto?>` | `PostViewModel.selectedPost: StateFlow<Post?>` | **Type mismatch** + property name `postDetail` vs `selectedPost` |
| `CreatePostScreen` | `channelViewModel.channels: StateFlow<List<ChannelDto>>` | `ChannelViewModel.channels: StateFlow<List<Channel>>` | **Type mismatch** |

### ViewModel property name mismatches (ViewModel has it, screens reference different name):

| Screen expects | ViewModel has | File |
|---|---|---|
| `adminViewModel.allPayments` | `adminViewModel.payments` | adminpayments.kt |
| `adminViewModel.allTickets` | `adminViewModel.tickets` | admintickets.kt |
| `autoResponderViewModel.autoReplies` | `autoResponderViewModel.rules` | autoresponderscreen.kt |
| `referralViewModel.referral` | `referralViewModel.referralData` | referralscreen.kt |
| `walletViewModel.wallet` | `walletViewModel.walletData` | walletscreen.kt |
| `postViewModel.postDetail` | `postViewModel.selectedPost` | postdetailscreen.kt |

### ViewModel method signature mismatches:

| Screen calls | ViewModel has | File |
|---|---|---|
| `channelViewModel.addChannel(name, platform, channelId, token, onSuccess)` | `channelViewModel.createChannel(name, platform, channelId, token)` (no `onSuccess` callback) | addchannelscreen.kt |
| `channelViewModel.updateChannel(channelId, name, channelIdVal, token, onSuccess)` | `channelViewModel.updateChannel(id, params: Map<String, Any>)` (different signature) | editchannelscreen.kt |
| `channelViewModel.channelDetail` (property) | `channelViewModel.selectedChannel` | editchannelscreen.kt |
| `channelViewModel.loadChannelDetail(id)` | `channelViewModel.loadChannel(id)` | editchannelscreen.kt |
| `autoResponderViewModel.deleteRule(id)` | `autoResponderViewModel.deleteRule(id)` — BUT ViewModel uses `api.deleteAutoReply(id)` from `ApiService` (domain models) while screen expects DTO-based ViewModel | autoresponderscreen.kt |
| `autoResponderViewModel.toggleChannel(channelId, enabled)` | `autoResponderViewModel.toggle(channelId, enabled)` | autoresponderscreen.kt |
| `postViewModel.createPost(title, content, sendType, channelIds, mediaUri, schedDate, schedHour, schedMinute, onSuccess)` | `postViewModel.createPost(title, content, sendType, channelIds, schedDate, schedHour, schedMinute, captionFormat, imageFile)` (takes `File` not `Uri`, no `onSuccess`) | createpostscreen.kt |
| `postViewModel.loadPostDetail(id)` | `postViewModel.loadPost(id)` | postdetailscreen.kt |
| `ticketViewModel.createTicket(subject, category, message, attachmentUri, onSuccess)` | `ticketViewModel.createTicket(subject, category, message, attachmentFile)` (takes `File` not `Uri`, no `onSuccess`) | createticketscreen.kt |
| `ticketViewModel.replyTicket(ticketId, replyText, attachmentUri)` | `ticketViewModel.replyTicket(id, message, closeAfter, attachmentFile)` (different signature) | ticketdetailscreen.kt |
| `settingsViewModel.saveAdvanced(...)` with named params and `onSuccess` | `settingsViewModel.saveAdvancedSettings(map)` (takes `Map<String, String>`, no `onSuccess`) | settingsscreen.kt |
| `settingsViewModel.saveGold(schedule, apiUrl, currency, template, channels, imageUri, onSuccess)` | `settingsViewModel.saveGoldSettings(schedule, apiUrl, currency, template, channels, imageFile)` (takes `File` not `Uri`, no `onSuccess`) | goldtickerscreen.kt |
| `settingsViewModel.triggerGold(onSuccess)` | `settingsViewModel.triggerGold()` (no callback) | goldtickerscreen.kt |
| `walletViewModel.convertPoints(points: Int)` | `walletViewModel.convertPoints(points: Int)` — matches, but `isConverting` property doesn't exist in WalletViewModel | walletscreen.kt |

---

## 4. Architecture Mismatch: Hilt vs Manual DI

### The `MainActivity` problem:

`MainActivity` is annotated with `@AndroidEntryPoint` and uses `hiltViewModel()` to obtain `AuthViewModel`. However, `AuthViewModel` is defined as:

```kotlin
class AuthViewModel(application: Application) : AndroidViewModel(application)
```

This is **NOT a `@HiltViewModel`**. Hilt cannot create it. `hiltViewModel()` will fail at runtime with a `MissingBinding` error because Hilt doesn't know how to provide `AuthViewModel` without the `@HiltViewModel` annotation.

**Same issue applies to ALL screens that use `hiltViewModel()`:**
- `AdminPaymentsScreen` → `AdminViewModel` (AndroidViewModel, no Hilt)
- `AdminPlansScreen` → `AdminViewModel` (same)
- `AdminTicketsScreen` → `AdminViewModel` (same)
- `AnalyticsScreen` → `AnalyticsViewModel` (AndroidViewModel, no Hilt)
- `AutoResponderScreen` → `AutoResponderViewModel` (AndroidViewModel, no Hilt)
- `ChannelsScreen` → `ChannelViewModel` (AndroidViewModel, no Hilt)
- `AddChannelScreen` → `ChannelViewModel` (same)
- `EditChannelScreen` → `ChannelViewModel` (same)
- `CreatePostScreen` → `PostViewModel` + `ChannelViewModel` (both AndroidViewModel, no Hilt)
- `PostDetailScreen` → `PostViewModel` (AndroidViewModel, no Hilt)
- `PostsScreen` → `PostViewModel` (same)
- `DashboardScreen` → `BootstrapViewModel` + `SyncViewModel` (both AndroidViewModel, no Hilt)
- `NotificationsScreen` → `NotificationViewModel` (AndroidViewModel, no Hilt)
- `PaymentsScreen` → `BillingViewModel` (AndroidViewModel, no Hilt)
- `PlansScreen` → `BillingViewModel` (same)
- `ReferralScreen` → `ReferralViewModel` (AndroidViewModel, no Hilt)
- `SettingsScreen` → `SettingsViewModel` (AndroidViewModel, no Hilt)
- `GoldTickerScreen` → `SettingsViewModel` + `ChannelViewModel` (both AndroidViewModel, no Hilt)
| `CreateTicketScreen` → `TicketViewModel` (AndroidViewModel, no Hilt)
- `TicketDetailScreen` → `TicketViewModel` (same)
- `TicketsScreen` → `TicketViewModel` (same)
- `WalletScreen` → `WalletViewModel` (AndroidViewModel, no Hilt)

### No Hilt module to provide RetrofitClient / TokenManager / ApiService

Even if ViewModels were `@HiltViewModel`, there is no `@Module` providing `RetrofitClient`, `TokenManager`, or `ApiService`. The ViewModels instantiate these manually via `TokenManager.getInstance(application)` and `RetrofitClient.getInstance(tokenManager).create(ApiService::class.java)`.

### `PostyarApp` is not annotated with `@HiltAndroidApp`

The `Application` class (`postyarapp.kt`) is a bare `Application()` subclass with no Hilt annotation. Hilt requires `@HiltAndroidApp` on the Application class.

---

## 5. Gson vs Moshi Conflict

The build.gradle declares `converter-gson` and `gson` for JSON. However:

- **ALL 16 DTO files** in `data/remote/dto/` use **Moshi** annotations (`@JsonClass`, `@Json`)
- The `domain/models.kt` file uses `@SerializedName` from **Gson** on the `User` class but none of the other 25+ data classes (they use raw snake_case field names that Gson's default policy would handle)
- The separate API interfaces in `data/remote/api/` return DTOs with Moshi annotations
- The monolithic `ApiService` in `data/remote/apiservice.kt` returns domain model classes

With only the Gson converter on the classpath, **none of the Moshi-annotated DTOs will deserialize correctly**. The `@Json(name = "...")` annotations will be silently ignored by Gson, leading to null fields or deserialization failures.

---

## 6. Room Database Issues

### Missing Room dependency
Room is not in build.gradle. All `@Entity`, `@Dao`, `@Database` annotations will fail.

### DAOs in wrong file
All 4 DAO interfaces (`UserDao`, `ChannelDao`, `PostDao`, `NotificationDao`) are defined in `syncdao.kt` — a file named for sync but containing all DAOs. The `PostyarDatabase` in `postyardatabase.kt` references them correctly by type, so this works if Room is available.

### Entities match correctly
- `PostyarDatabase` lists: `UserEntity`, `ChannelEntity`, `PostEntity`, `NotificationEntity`
- All 4 entity classes exist in `data/local/`
- All 4 DAOs exist in `syncdao.kt`
- DAOs reference correct entity types in their queries and return types
- ✅ Entity ↔ DAO mapping is correct

### Room is never instantiated
There is no `Room.databaseBuilder()` call anywhere in the codebase. The database is defined but never created or provided.

---

## 7. Missing / Referenced-But-Unimplemented Composables

### Screens referenced in `PostyarNavigation` but NOT having a navigation route:

| Screen Composable | In Navigation? | Notes |
|---|---|---|
| `SplashScreen` | ✅ `"splash"` | |
| `LoginScreen` | ✅ `"login"` | |
| `RegisterScreen` | ✅ `"register"` | |
| `ForgotPasswordScreen` | ✅ `"forgotPassword"` | |
| `DashboardScreen` | ✅ `"dashboard"` | |
| `PostsScreen` | ✅ `"posts"` | |
| `CreatePostScreen` | ✅ `"posts/create"` | |
| `ChannelsScreen` | ✅ `"channels"` | |
| `AddChannelScreen` | ✅ `"channels/add"` | |
| `NotificationsScreen` | ✅ `"notifications"` | |
| `PlansScreen` | ✅ `"plans"` | |
| `PaymentsScreen` | ✅ `"payments"` | |
| `TicketsScreen` | ✅ `"tickets"` | |
| `CreateTicketScreen` | ✅ `"tickets/create"` | |
| `TicketDetailScreen` | ✅ `"tickets/detail/{id}"` | |
| `SettingsScreen` | ✅ `"settings"` | |
| `GoldTickerScreen` | ✅ `"settings/gold"` | |
| `AutoResponderScreen` | ✅ `"settings/autoresponder"` | |
| `WalletScreen` | ✅ `"wallet"` | |
| `ReferralScreen` | ✅ `"referral"` | |
| `AnalyticsScreen` | ✅ `"analytics"` | |
| `ProfileScreen` | ✅ `"profile"` | |
| `AdminDashboardScreen` | ✅ `"admin/dashboard"` | |
| `AdminUsersScreen` | ✅ `"admin/users"` | |
| `PostDetailScreen` | ❌ NOT in navigation | File exists but no route. `PostsScreen` navigates to `"posts/detail/{id}"` but that route doesn't exist in `NavHost` |
| `EditChannelScreen` | ❌ NOT in navigation | File exists but no route. No screen navigates to it |
| `AdminPaymentsScreen` | ❌ NOT in navigation | File exists but no route |
| `AdminPlansScreen` | ❌ NOT in navigation | File exists but no route |
| `AdminTicketsScreen` | ❌ NOT in navigation | File exists but no route |

### Composables referenced in code but not defined:

| Reference | Location | Issue |
|---|---|---|
| `PostyarNavigationItem` | `dashboardscreen.kt` | **Not defined anywhere.** Imported but doesn't exist. |
| `PostyarBottomNav` with `title` parameter | `adminpaymentsscreen.kt`, `adminplansscreen.kt`, `adminticketscreen.kt`, `analyticsscreen.kt`, `billing/*`, `notificationsscreen.kt`, etc. | `PostyarTopBar` is called with `title: String` parameter but the actual `PostyarTopBar` in `postyartopbar.kt` has NO `title` parameter — it hardcodes `"پُست‌یار"` as the title. |
| `QuotaCard` with `title`, `used`, `limit`, `icon` params | `dashboardscreen.kt` | Actual `QuotaCard` in `quotacard.kt` has `label`, `used`, `limit` but NO `icon` parameter |
| `PersianNumberText` with `style`, `fontWeight`, `color`, `textAlign` params | Multiple screens | Actual `PersianNumberText` in `persiannumbertext.kt` only accepts `number: Any, modifier: Modifier` — no text style params |
| `PostDetailScreen` | Navigation reference | Referenced via `"posts/detail/{id}"` route from `PostsScreen` but not registered in `NavHost` |
| `Icons.Default.Sensors` | `postyarbottomnav.kt` | Requires `material-icons-extended` dependency |
| `Icons.Default.Article` | `postyarbottomnav.kt` | Requires `material-icons-extended` |
| `Icons.Default.CheckCircle` | `adminusersscreen.kt` | Requires `material-icons-extended` |
| `Icons.Default.Block` | `adminusersscreen.kt` | Requires `material-icons-extended` |
| `Icons.Default.AttachFile` | `ticketdetailscreen.kt` | Requires `material-icons-extended` |
| `Icons.Default.Send` | `ticketdetailscreen.kt` | Requires `material-icons-extended` |

### Composables defined but with wrong parameter signatures:

| Composable | Defined As | Called As | File(s) |
|---|---|---|---|
| `PostyarTopBar` | `onNotificationClick`, `onBackClick?`, `unreadCount`, `showNotification` | `title: String` | adminpaymentsscreen.kt, adminplansscreen.kt, adminticketscreen.kt, analyticsscreen.kt, billing/paymentsscreen.kt, billing/plansscreen.kt, channels/addchannelscreen.kt, channels/editchannelscreen.kt, notifications/notificationsscreen.kt, posts/createpostscreen.kt, posts/postdetailscreen.kt, referral/referralscreen.kt, settings/autoresponderscreen.kt, settings/goldtickerscreen.kt, settings/settingsscreen.kt, tickets/createticketscreen.kt, tickets/ticketdetailscreen.kt, wallet/walletscreen.kt |
| `PersianNumberText` | `number: Any, modifier: Modifier` | `text: String, style: TextStyle, fontWeight: FontWeight, color: Color, textAlign: TextAlign?` | Multiple screens |
| `QuotaCard` | `label, used, limit, modifier` | `modifier, title, used, limit, icon: @Composable () -> Unit` | dashboardscreen.kt |
| `DashboardScreen` | `onNavigate: (String) -> Unit, bootstrapViewModel, syncViewModel` | Called from navigation with just `onNavigate` (no ViewModel params, default hiltViewModel would kick in but fails) | postyarnavigation.kt |
| `AdminDashboardScreen` | 8 params (totalUsers, activeUsers, etc.) + onNavigateUsers | Called from navigation with just `onNavigateUsers` (all data params are defaults) | postyarnavigation.kt — screen accepts no ViewModel, all data is hardcoded defaults, never populated from API |
| `AdminUsersScreen` | `users, onSuspend, onActivate` (manual data passing) | Called from navigation with no params | postyarnavigation.kt — screen is purely static, never loads data |
| `ProfileScreen` | `userName, userEmail, userRole, onLogout, onUpdateProfile, onChangePassword` | Called from navigation with just `onLogout` | postyarnavigation.kt — screen never receives actual user data |

---

## 8. Empty / Placeholder Files

No `.kt` files are empty or `.gitkeep` placeholders. All 94 files contain substantive code.

---

## 9. Other Issues

### 9.1 `RegisterScreen` has imports after class closing brace

`registerscreen.kt` has:
```kotlin
}

import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
```
These imports are after the `RegisterScreen` composable's closing brace and outside any class/function. This is a **syntax error** in Kotlin — imports must be at the top of the file.

### 9.2 `AnalyticsScreen` LinkDetailScreen references `paddingValues` not in scope

In `analyticsscreen.kt`, `LinkDetailScreen` is a `@Composable` function that references `paddingValues`:
```kotlin
Column(modifier = Modifier.fillMaxSize().padding(paddingValues)) {
```
But `paddingValues` is only available inside the `Scaffold` content lambda above. `LinkDetailScreen` is a separate composable — it has no access to that variable. This is a **compile error**.

### 9.3 `DashboardScreen` uses `PostyarTopBar` with `actions` parameter

```kotlin
PostyarTopBar(
    title = "پُست‌یار",
    actions = { ... }
)
```
But `PostyarTopBar` doesn't have a `title` parameter or an `actions` parameter. **Compile error.**

### 9.4 `DashboardScreen` references non-existent `PostyarNavigationItem`

```kotlin
import com.postyar.app.presentation.navigation.PostyarNavigationItem
```
This class doesn't exist anywhere in the codebase. **Compile error.**

### 9.5 `AdminDashboardScreen` and `AdminUsersScreen` don't use ViewModels

These screens are called with no data from the navigation graph and have all params defaulted to empty. They display static/empty content and never load real data. This means the admin section is non-functional even if everything else compiled.

### 9.6 `ProfileScreen` doesn't use a ViewModel

`ProfileScreen` takes primitive params (`userName`, `userEmail`, `userRole`) and callbacks, but the navigation only passes `onLogout`. It never receives actual user data and has no way to fetch it. The `ProfileViewModel` exists but is never connected to any screen.

### 9.7 All API interfaces in `data/remote/api/` are unused

The 10 separate API interfaces (`AdminApi`, `AnalyticsApi`, `AuthApi`, `AutoResponderApi`, `BillingApi`, `BootstrapApi`, `ChannelApi`, `NotificationApi`, `SettingsApi`, `TicketApi`, `WalletApi`, `ReferralApi`) are defined but never instantiated or referenced by any ViewModel. All ViewModels use the monolithic `ApiService` from `apiservice.kt`.

### 9.8 `walletViewModel.isConverting` doesn't exist

`WalletScreen` references `walletViewModel.isConverting` but `WalletViewModel` has no such property.

### 9.9 `AnalyticsViewModel.isDetailLoading` doesn't exist

`AnalyticsScreen`'s `LinkDetailScreen` references `analyticsViewModel.isDetailLoading` but `AnalyticsViewModel` only has `isLoading`.

---

## 10. Summary Statistics

| Category | Count |
|---|---|
| Total `.kt` files | 94 |
| Files with import errors (missing deps) | ~55+ |
| Files with compile-time type mismatches | ~30+ |
| Duplicate class definitions | 3 (`TokenManager`, `AuthInterceptor`, `ApiResponse` + request models) |
| Missing dependencies in build.gradle | 15+ |
| Dead/wrong dependencies in build.gradle | 8 |
| Unreachable screen routes (composable exists, no route) | 5 |
| Composables with wrong parameter signatures | 5 |
| Undefined references (classes/params) | 8+ |
| Empty/placeholder files | 0 |

## 11. Root Cause Assessment

The codebase shows clear evidence of **two separate development efforts** that were merged without reconciliation:

1. **Legacy system** (View-based? or early Compose?): `AndroidViewModel` + manual singletons (`RetrofitClient`, `TokenManager`) + monolithic `ApiService` + domain models with Gson + `navigation-fragment-ktx`
2. **Refactored/modern system** (Compose): Hilt DI + separate API interfaces + Moshi DTOs + `navigation-compose` + `hiltViewModel()` + `collectAsStateWithLifecycle`

The `build.gradle` is from the **legacy system**. The screen composables are from the **modern system**. The ViewModels are from the **legacy system**. Neither system is complete or consistent.

**This project cannot compile. It requires a complete architectural reconciliation to choose one system and eliminate the other, or a systematic migration.**