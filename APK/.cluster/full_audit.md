# Postyar Android App — Full Source Audit

**Generated:** 2026-08-14 21:26 GMT+3:30  
**Total Kotlin Files Audited:** 82  
**Scope:** `app/src/main/java/com/postyar/app/`

---

## Table of Contents

1. [File-by-File Inventory](#1-file-by-file-inventory)
2. [All ViewModels (Complete List)](#2-all-viewmodels)
3. [All @Composable Screen Functions (Complete List)](#3-all-composable-screen-functions)
4. [All API Interfaces & Method Signatures](#4-all-api-interfaces--method-signatures)
5. [TokenManager & AuthInterceptor Implementations](#5-tokenmanager--authinterceptor-implementations)
6. [Moshi vs Gson Usage](#6-moshi-vs-gson-usage)
7. [Hilt Annotation Usage](#7-hilt-annotation-usage)
8. [Room Annotation Usage](#8-room-annotation-usage)
9. [Navigation Routes Defined in PostyarNavigation](#9-navigation-routes-defined-in-postyarnavigation)
10. [Screens Without Navigation Routes](#10-screens-without-navigation-routes)
11. [PostyarTopBar / QuotaCard / PersianNumberText Call Sites](#11-postyartopbar--quotacard--persiannumbertext-call-sites)
12. [Screen → ViewModel → Property Cross-Reference](#12-screen--viewmodel--property-cross-reference)
13. [Critical Issues & Syntax Problems Found](#13-critical-issues--syntax-problems-found)

---

## 1. File-by-File Inventory

### 1.1 Application Layer (`postyarapp.kt`)

| # | File | Package | Annotations | Classes/Functions | Imports |
|---|------|---------|-------------|-------------------|---------|
| 1 | `postyarapp.kt` | `com.postyar.app` | _(none)_ | `class PostyarApp : Application()` | `android.app.Application` |

**Notes:** No Hilt annotation on Application class — this is **critical** if Hilt is used elsewhere.

---

### 1.2 Core Layer

#### `core/security/tokenmanager.kt`
| # | File | Package | Annotations | Classes/Functions | Key Imports |
|---|------|---------|-------------|-------------------|-------------|
| 2 | `tokenmanager.kt` | `com.postyar.app.core.security` | `@Singleton`, `@Inject`, `@ApplicationContext` | `class TokenManager @Inject constructor(@ApplicationContext private val context: Context)` | `dagger.hilt.android.qualifiers.ApplicationContext`, `javax.inject.*`, `androidx.security.crypto.*` |

**Methods:** `saveToken(token)`, `getToken(): String?`, `clearToken()`, `isAuthenticated(): Boolean`

#### `core/network/authinterceptor.kt`
| # | File | Package | Annotations | Classes/Functions | Key Imports |
|---|------|---------|-------------|-------------------|-------------|
| 3 | `authinterceptor.kt` | `com.postyar.app.core.network` | `@Singleton`, `@Inject` | `class AuthInterceptor @Inject constructor(private val tokenManager: TokenManager) : Interceptor` | `okhttp3.*`, `javax.inject.*` |

**Methods:** `override fun intercept(chain): Response`

---

### 1.3 Data Local (Room)

| # | File | Package | Room Annotations | Class Signature |
|---|------|---------|-----------------|-----------------|
| 4 | `channelentity.kt` | `com.postyar.app.data.local` | `@Entity(tableName="channels")`, `@PrimaryKey` | `data class ChannelEntity(id, tenantId, name, platform, channelId, token?, linkConfig?, buttonConfig?, webhookActive, createdAt)` |
| 5 | `notificationentity.kt` | `com.postyar.app.data.local` | `@Entity(tableName="notifications")`, `@PrimaryKey` | `data class NotificationEntity(id, userId, type, title, message, targetSection?, isRead, createdAt)` |
| 6 | `postentity.kt` | `com.postyar.app.data.local` | `@Entity(tableName="posts")`, `@PrimaryKey` | `data class PostEntity(id, tenantId, title, content, mediaUrl?, status, scheduledAt?, targetChannels?, createdAt, clickCount)` |
| 7 | `userentity.kt` | `com.postyar.app.data.local` | `@Entity(tableName="users")`, `@PrimaryKey` | `data class UserEntity(id, name, email, role, status, businessName?, businessType?, phone?, birthday?, referralCode?, referralPoints, walletBalance, createdAt)` |
| 8 | `postyardatabase.kt` | `com.postyar.app.data.local` | `@Database(entities=[UserEntity,ChannelEntity,PostEntity,NotificationEntity], version=1, exportSchema=false)` | `abstract class PostyarDatabase : RoomDatabase()` with abstract DAO accessors |
| 9 | `syncdao.kt` | `com.postyar.app.data.local` | `@Dao` (×4 interfaces) | `UserDao`, `ChannelDao`, `PostDao`, `NotificationDao` — all with suspend functions + Flow observers |

---

### 1.4 Data Remote

#### Legacy Monolith (`apiservice.kt` + `retrofitclient.kt`)
| # | File | Package | Key Details |
|---|------|---------|-------------|
| 10 | `apiservice.kt` | `com.postyar.app.data.remote` | **Monolithic** `interface ApiService` — ~60+ endpoints covering ALL domains (auth, channels, posts, notifications, billing, tickets, settings, auto-responder, wallet, referral, analytics, admin). Uses domain models from `models.kt`. Uses `GsonConverterFactory`. |
| 11 | `retrofitclient.kt` | `com.postyar.app.data.remote` | Contains **duplicate** `class TokenManager` (singleton pattern, NOT Hilt), duplicate `class AuthInterceptor`, and `class RetrofitClient` singleton. Base URL: `https://asovin.ir/api/v1/`. Uses `GsonConverterFactory`. |

**⚠️ CRITICAL DUPLICATION:** There are TWO `TokenManager` implementations and TWO `AuthInterceptor` implementations:
- Hilt-based: `core/security/tokenmanager.kt` + `core/network/authinterceptor.kt`
- Singleton manual: `retrofitclient.kt` (inline classes)

#### New Modular APIs (DTO layer uses Moshi)

| # | File | Package | Interface | Methods |
|---|------|---------|-----------|---------|
| 12 | `adminapi.kt` | `...data.remote.api` | `AdminApi` | dashboard(), listUsers(), suspendUser(), activateUser(), listPayments(), approvePayment(), listTickets(), replyTicket(), listPlans(), createPlan(), updatePlan(), deletePlan(), broadcast(), addDiscount(), deleteDiscount() |
| 13 | `analyticsapi.kt` | `...data.remote.api` | `AnalyticsApi` | listLinks(), linkDetail() |
| 14 | `authapi.kt` | `...data.remote.api` | `AuthApi` | login(), register(), logout(), me(), updateProfile(), changePassword(), resetPassword(), resetPasswordConfirm(), resetPasswordSms(), verifySmsCode() |
| 15 | `autoresponderapi.kt` | `...data.remote.api` | `AutoResponderApi` | list(), add(), delete(), toggle() |
| 16 | `billingapi.kt` | `...data.remote.api` | `BillingApi` | listPlans(), submitPayment(), listPayments(), validateCoupon() |
| 17 | `bootstrapapi.kt` | `...data.remote.api` | `BootstrapApi` | bootstrap(), sync() |
| 18 | `channelapi.kt` | `...data.remote.api` | `ChannelApi` | list(), get(), create(), update(), delete() |
| 19 | `notificationapi.kt` | `...data.remote.api` | `NotificationApi` | list(), markRead(), markAllRead() |
| 20 | `postapi.kt` | `...data.remote.api` | `PostApi` | list(), get(), create(), cancel(), retry() |
| 21 | `referralapi.kt` | `...data.remote.api` | `ReferralApi` | get() |
| 22 | `settingsapi.kt` | `...data.remote.api` | `SettingsApi` | get(), saveGold(), triggerGold(), saveAdvanced() |
| 23 | `ticketapi.kt` | `...data.remote.api` | `TicketApi` | list(), create(), get(), reply() |
| 24 | `walletapi.kt` | `...data.remote.api` | `WalletApi` | get(), convertPoints() |

#### DTOs (all use Moshi `@JsonClass(generateAdapter=true)`)
| # | File | DTO Classes |
|---|------|------------|
| 25 | `analyticslinkdto.kt` | `AnalyticsLinkDto`, `DailyBreakdownDto`, `AnalyticsLinkDetailDto` |
| 26 | `apiresponse.kt` | `ApiResponse<T>`, `EmptyData`, `MarkedCountData` |
| 27 | `autoreplydto.kt` | `AutoReplyDto` |
| 28 | `bootstrapdto.kt` | `AnnouncementDto`, `ReferralInfoDto`, `TicketCategoryDto`, `BootstrapDto`, `InboxMessageDto`, `DiscountOfferDto`, `BootstrapSettingsDto`, `NotificationsWrapperDto` |
| 29 | `channeldto.kt` | `ChannelDto` |
| 30 | `coupondto.kt` | `CouponDto` |
| 31 | `loginresponsedto.kt` | `LoginResponseDto`, `MeResponseDto`, `SubscriptionDto` |
| 32 | `notificationdto.kt` | `NotificationDto` |
| 33 | `paymentdto.kt` | `PaymentDto` |
| 34 | `plandto.kt` | `PlanDto` |
| 35 | `postdto.kt` | `PostDto` |
| 36 | `quotadto.kt` | `QuotaDto` |
| 37 | `referraldto.kt` | `ReferralUserDto`, `ReferralDto`, `ReferralStatsDto` |
| 38 | `settingsdto.kt` | `SettingsDto` |
| 39 | `syncdto.kt` | `SyncDto` |
| 40 | `ticketdto.kt` | `TicketDto`, `TicketDetailDto` |
| 41 | `ticketreplydto.kt` | `TicketReplyDto` |
| 42 | `userdto.kt` | `UserDto` |
| 43 | `walletdto.kt` | `WalletTransactionDto`, `WalletDto`, `ConvertPointsDto` |

---

### 1.5 Domain Models (`domain/models.kt`)

| # | File | Package | Notes |
|---|------|---------|-------|
| 44 | `models.kt` | `com.postyar.app.domain` | ~40 data classes using **plain properties** (no Moshi/Gson annotations). Used by legacy `ApiService`. Includes: `ApiResponse<T>`, `User`, `Subscription`, `AuthResponse`, `MeResponse`, `Quota`, `Channel`, `Post`, `NotificationItem`, `NotificationListData`, `Plan`, `Payment`, `Ticket`, `TicketDetail`, `TicketReply`, `Settings`, `AutoReplyRule`, `WalletData`, `WalletTransaction`, `ConvertPointsResult`, `ReferralData`, `ReferralStats`, `ReferredUser`, `LinkTracking`, `LinkDetail`, `DailyClickStat`, `CouponValidation`, `AdminDashboard`, `UserStats`, `PaymentStats`, `TicketStats`, `BootstrapData`, `ReferralInfo`, `TicketCategory`, `Announcement`, `SyncData`. Also contains request models: `LoginRequest`, `RegisterRequest`, `ProfileRequest`, `ChangePasswordRequest`, etc. |

**⚠️ CRITICAL:** This file imports `com.google.gson.annotations.SerializedName` but **never actually uses it** — no field has `@SerializedName`.

---

### 1.6 Presentation Layer

#### Activity
| # | File | Package | Annotations | Class |
|---|------|---------|-------------|-------|
| 45 | `mainactivity.kt` | `com.postyar.app.presentation` | `@AndroidEntryPoint` | `class MainActivity : ComponentActivity()` — creates `AuthViewModel` via `hiltViewModel()`, hosts `PostyarNavigation` |

#### Components (8 files)
| # | File | @Composable Functions |
|---|------|---------------------|
| 46 | `confirmationdialog.kt` | `ConfirmationDialog(title, message, confirmText?, dismissText?, onConfirm, onDismiss)` |
| 47 | `emptystateview.kt` | `EmptyStateView(message, modifier?)` |
| 48 | `errorview.kt` | `ErrorView(message, onRetry, modifier?)` |
| 49 | `loadingview.kt` | `LoadingView(modifier?)` |
| 50 | `persiannumbertext.kt` | `PersianNumberText(number, modifier?)` + top-level `toPersianNumber(input): String` + `formatPrice(amount): String` |
| 51 | `postyarbottomnav.kt` | `enum class BottomNavItem` + `PostyarBottomNav(currentRoute?, onNavigate, modifier?)` |
| 52 | `postyartopbar.kt` | `PostyarTopBar(onNotificationClick?, onBackClick?, unreadCount?, showNotification?)` |
| 53 | `quotacard.kt` | `QuotaCard(label, used, limit, modifier?)` |
| 54 | `statusbadge.kt` | `StatusBadge(status, modifier?)` |

#### Navigation
| # | File | Function |
|---|------|----------|
| 55 | `postyarnavigation.kt` | `@Composable fun PostyarNavigation(navController, authViewModel)` — defines all routes (see §9) |

#### Screens (22 screen files)
| # | File | @Composable Function | Parameters |
|---|------|---------------------|-----------|
| 56 | `splashscreen.kt` | `SplashScreen(checkAuth, onAuthChecked, authState?)` | Auth state-driven navigation |
| 57 | `loginscreen.kt` | `LoginScreen(viewModel, onNavigateRegister, onNavigateForgot, onLoginSuccess)` | Uses `AuthViewModel` |
| 58 | `registerscreen.kt` | `RegisterScreen(viewModel, onNavigateBack, onRegisterSuccess, onNavigateForgot?)` | Uses `AuthViewModel`; **has stray imports after class body** ⚠️ |
| 59 | `forgotpasswordscreen.kt` | `ForgotPasswordScreen(viewModel, onNavigateBack)` | Uses `AuthViewModel` |
| 60 | `dashboardscreen.kt` | `DashboardScreen(onNavigate, bootstrapViewModel?, syncViewModel?)` | Uses `BootstrapViewModel` + `SyncViewModel`; references undefined `PostyarNavigationItem` and `QuotaCard(title,used,limit,icon)` overload ⚠️ |
| 61 | `postsscreen.kt` | `PostsScreen(onNavigate, postViewModel?)` | Uses `PostViewModel` |
| 62 | `createpostscreen.kt` | `CreatePostScreen(onBack, postViewModel?, channelViewModel?)` | Uses `PostViewModel` + `ChannelViewModel` |
| 63 | `postdetailscreen.kt` | `PostDetailScreen(postId, onBack, postViewModel?)` | Uses `PostViewModel` |
| 64 | `channelsscreen.kt` | `ChannelsScreen(onNavigate, channelViewModel?)` | Uses `ChannelViewModel` |
| 65 | `addchannelscreen.kt` | `AddChannelScreen(onBack, channelViewModel?)` | Uses `ChannelViewModel` |
| 66 | `editchannelscreen.kt` | `EditChannelScreen(channelId, onBack, channelViewModel?)` | Uses `ChannelViewModel` |
| 67 | `notificationsscreen.kt` | `NotificationsScreen(notificationViewModel?)` | Uses `NotificationViewModel` |
| 68 | `plansscreen.kt` | `PlansScreen(onNavigate, billingViewModel?)` | Uses `BillingViewModel` |
| 69 | `paymentsscreen.kt` | `PaymentsScreen(billingViewModel?)` | Uses `BillingViewModel` |
| 70 | `ticketsscreen.kt` | `TicketsScreen(onNavigate, ticketViewModel?)` | Uses `TicketViewModel` |
| 71 | `createticketscreen.kt` | `CreateTicketScreen(onBack, ticketViewModel?)` | Uses `TicketViewModel` |
| 72 | `ticketdetailscreen.kt` | `TicketDetailScreen(ticketId, onBack, ticketViewModel?)` | Uses `TicketViewModel` |
| 73 | `settingsscreen.kt` | `SettingsScreen(onBack, settingsViewModel?)` | Uses `SettingsViewModel` |
| 74 | `goldtickerscreen.kt` | `GoldTickerScreen(onBack, settingsViewModel?, channelViewModel?)` | Uses `SettingsViewModel` + `ChannelViewModel` |
| 75 | `autoresponderscreen.kt` | `AutoResponderScreen(autoResponderViewModel?)` | Uses `AutoResponderViewModel` |
| 76 | `walletscreen.kt` | `WalletScreen(walletViewModel?)` | Uses `WalletViewModel` |
| 77 | `referralscreen.kt` | `ReferralScreen(referralViewModel?)` | Uses `ReferralViewModel` |
| 78 | `profilescreen.kt` | `ProfileScreen(userName?, userEmail?, userRole?, onLogout?, onUpdateProfile?, onChangePassword?)` | **No ViewModel** — pure callback-based |
| 79 | `analyticsscreen.kt` | `AnalyticsScreen(analyticsViewModel?)` + private `LinkCard()` + `LinkDetailScreen()` | Uses `AnalyticsViewModel` |
| 80 | `admindashboardscreen.kt` | `AdminDashboardScreen(totalUsers?, activeUsers?, ...)` | **No ViewModel** — pure parameter-based |
| 81 | `adminpaymentsscreen.kt` | `AdminPaymentsScreen(adminViewModel?)` | Uses `AdminViewModel` |
| 82 | `adminplansscreen.kt` | `AdminPlansScreen(adminViewModel?)` | Uses `AdminViewModel` |
| 83 | `adminticketsscreen.kt` | `AdminTicketsScreen(onNavigateDetail?, adminViewModel?)` | Uses `AdminViewModel` |
| 84 | `adminusersscreen.kt` | `AdminUsersScreen(users?, onSuspend?, onActivate?)` | **No ViewModel** — defines local `AdminUser` data class; has its own TopAppBar/SearchBar |

#### ViewModels (15 files)
| # | File | Class | Constructor | StateFlows |
|---|------|-------|-------------|-----------|
| 85 | `authviewmodel.kt` | `AuthViewModel(application: Application) : AndroidViewModel(application)` | Manual DI via `Application` | currentUser, authState, loginError, registerError, passwordResetSent |
| 86 | `bootstrapviewmodel.kt` | `BootstrapViewModel(application: Application) : AndroidViewModel(application)` | Manual DI via `Application` | quota, channels, posts, notifications, unreadCount, plans, tickets, autoReplies, paymentHistory, settings, ticketCategories, referralInfo, walletBalance, announcement, isLoading, error, lastSyncTime |
| 87 | `channelviewmodel.kt` | `ChannelViewModel(application: Application) : AndroidViewModel(application)` | Manual DI via `Application` | channels, selectedChannel, isLoading, error, actionSuccess |
| 88 | `postviewmodel.kt` | `PostViewModel(application: Application) : AndroidViewModel(application)` | Manual DI via `Application` | posts, selectedPost, isLoading, error, actionSuccess, currentFilter |
| 89 | `notificationviewmodel.kt` | `NotificationViewModel(application: Application) : AndroidViewModel(application)` | Manual DI via `Application` | notifications, unreadCount, isLoading |
| 90 | `billingviewmodel.kt` | `BillingViewModel(application: Application) : AndroidViewModel(application)` | Manual DI via `Application` | plans, payments, couponValidation, isLoading, error, actionSuccess |
| 91 | `ticketviewmodel.kt` | `TicketViewModel(application: Application) : AndroidViewModel(application)` | Manual DI via `Application` | tickets, ticketDetail, isLoading, error, actionSuccess |
| 92 | `settingsviewmodel.kt` | `SettingsViewModel(application: Application) : AndroidViewModel(application)` | Manual DI via `Application` | settings, isLoading, error, actionSuccess |
| 93 | `autoresponderviewmodel.kt` | `AutoResponderViewModel(application: Application) : AndroidViewModel(application)` | Manual DI via `Application` | rules, isLoading, error, actionSuccess |
| 94 | `analyticsviewmodel.kt` | `AnalyticsViewModel(application: Application) : AndroidViewModel(application)` | Manual DI via `Application` | links, linkDetail, isLoading |
| 95 | `referralviewmodel.kt` | `ReferralViewModel(application: Application) : AndroidViewModel(application)` | Manual DI via `Application` | referralData, isLoading |
| 96 | `walletviewmodel.kt` | `WalletViewModel(application: Application) : AndroidViewModel(application)` | Manual DI via `Application` | walletData, isLoading, error, actionSuccess |
| 97 | `profileviewmodel.kt` | `ProfileViewModel(application: Application) : AndroidViewModel(application)` | Manual DI via `Application` | user, subscription, isLoading, error, actionSuccess |
| 98 | `adminviewmodel.kt` | `AdminViewModel(application: Application) : AndroidViewModel(application)` | Manual DI via `Application` | dashboard, users, payments, tickets, plans, isLoading, error, actionSuccess |
| 99 | `syncviewmodel.kt` | `SyncViewModel(application: Application) : AndroidViewModel(application)` | Manual DI via `Application` | unreadCount, lastSyncError |

#### Theme
| # | File | Function |
|---|------|----------|
| 100 | `theme.kt` | `@Composable fun PostyarTheme(content)` — simple light color scheme |

---

## 2. All ViewModels (Complete List)

### Pattern: All extend `AndroidViewModel(application)`, manually construct `RetrofitClient.getInstance(TokenManager.getInstance(application)).create(ApiService::class.java)`

| # | VM Class | File | StateFlows Exposed | API Dependencies |
|---|---------|------|--------------------|-------------------|
| 1 | `AuthViewModel` | `authviewmodel.kt` | `currentUser`, `authState`, `loginError`, `registerError`, `passwordResetSent` | ApiService (login, register, logout, getMe, resetPassword*, verifySmsCode) |
| 2 | `BootstrapViewModel` | `bootstrapviewmodel.kt` | `quota`, `channels`, `posts`, `notifications`, `unreadCount`, `plans`, `tickets`, `autoReplies`, `paymentHistory`, `settings`, `ticketCategories`, `referralInfo`, `walletBalance`, `announcement`, `isLoading`, `error`, `lastSyncTime` | ApiService (bootstrap, getChannels) |
| 3 | `ChannelViewModel` | `channelviewmodel.kt` | `channels`, `selectedChannel`, `isLoading`, `error`, `actionSuccess` | ApiService (getChannels, getChannel, createChannel, updateChannel, deleteChannel) |
| 4 | `PostViewModel` | `postviewmodel.kt` | `posts`, `selectedPost`, `isLoading`, `error`, `actionSuccess`, `currentFilter` | ApiService (getPosts, getPost, createPost, cancelPost, retryPost) |
| 5 | `NotificationViewModel` | `notificationviewmodel.kt` | `notifications`, `unreadCount`, `isLoading` | ApiService (getNotifications, markNotificationRead, markAllNotificationsRead) |
| 6 | `BillingViewModel` | `billingviewmodel.kt` | `plans`, `payments`, `couponValidation`, `isLoading`, `error`, `actionSuccess` | ApiService (getPlans, getPayments, validateCoupon, submitPayment) |
| 7 | `TicketViewModel` | `ticketviewmodel.kt` | `tickets`, `ticketDetail`, `isLoading`, `error`, `actionSuccess` | ApiService (getTickets, getTicketDetail, createTicket, replyTicket) |
| 8 | `SettingsViewModel` | `settingsviewmodel.kt` | `settings`, `isLoading`, `error`, `actionSuccess` | ApiService (getSettings, saveAdvancedSettings, saveGoldSettings, triggerGold) |
| 9 | `AutoResponderViewModel` | `autoresponderviewmodel.kt` | `rules`, `isLoading`, `error`, `actionSuccess` | ApiService (getAutoReplies, addAutoReply, deleteAutoReply, toggleAutoResponder) |
| 10 | `AnalyticsViewModel` | `analyticsviewmodel.kt` | `links`, `linkDetail`, `isLoading` | ApiService (getAnalyticsLinks, getLinkDetail) |
| 11 | `ReferralViewModel` | `referralviewmodel.kt` | `referralData`, `isLoading` | ApiService (getReferral) |
| 12 | `WalletViewModel` | `walletviewmodel.kt` | `walletData`, `isLoading`, `error`, `actionSuccess` | ApiService (getWallet, convertPoints) |
| 13 | `ProfileViewModel` | `profileviewmodel.kt` | `user`, `subscription`, `isLoading`, `error`, `actionSuccess` | ApiService (getMe, updateProfile, changePassword) |
| 14 | `AdminViewModel` | `adminviewmodel.kt` | `dashboard`, `users`, `payments`, `tickets`, `plans`, `isLoading`, `error`, `actionSuccess` | ApiService (admin*, all admin endpoints) |
| 15 | `SyncViewModel` | `syncviewmodel.kt` | `unreadCount`, `lastSyncError` | ApiService (sync) |

**Key Observation:** None of these ViewModels use `@HiltViewModel` or `@Inject`. They are all `AndroidViewModel` subclasses that take `Application` and manually build their dependencies using the legacy `RetrofitClient`/`TokenManager` singletons from `retrofitclient.kt`. They do NOT use the new modular API interfaces or DTOs.

---

## 3. All @Composable Screen Functions (Complete List)

| # | Screen Function | File | Full Parameter Signature | ViewModel Used |
|---|----------------|------|--------------------------|---------------|
| 1 | `SplashScreen` | `splashscreen.kt` | `(checkAuth: () -> Unit, onAuthChecked: (Boolean) -> Unit, authState: AuthState = IDLE)` | _none_ (reads authState param) |
| 2 | `LoginScreen` | `loginscreen.kt` | `(viewModel: AuthViewModel, onNavigateRegister: () -> Unit, onNavigateForgot: () -> Unit, onLoginSuccess: () -> Unit)` | **AuthViewModel** |
| 3 | `RegisterScreen` | `registerscreen.kt` | `(viewModel: AuthViewModel, onNavigateBack: () -> Unit, onRegisterSuccess: () -> Unit, onNavigateForgot: () -> Unit = {})` | **AuthViewModel** |
| 4 | `ForgotPasswordScreen` | `forgotpasswordscreen.kt` | `(viewModel: AuthViewModel, onNavigateBack: () -> Unit)` | **AuthViewModel** |
| 5 | `DashboardScreen` | `dashboardscreen.kt` | `(onNavigate: (String) -> Unit, bootstrapViewModel: BootstrapViewModel = hiltViewModel(), syncViewModel: SyncViewModel = hiltViewModel())` | **BootstrapViewModel**, **SyncViewModel** |
| 6 | `PostsScreen` | `postsscreen.kt` | `(onNavigate: (String) -> Unit, postViewModel: PostViewModel = hiltViewModel())` | **PostViewModel** |
| 7 | `CreatePostScreen` | `createpostscreen.kt` | `(onBack: () -> Unit, postViewModel: PostViewModel = hiltViewModel(), channelViewModel: ChannelViewModel = hiltViewModel())` | **PostViewModel**, **ChannelViewModel** |
| 8 | `PostDetailScreen` | `postdetailscreen.kt` | `(postId: Int, onBack: () -> Unit, postViewModel: PostViewModel = hiltViewModel())` | **PostViewModel** |
| 9 | `ChannelsScreen` | `channelsscreen.kt` | `(onNavigate: (String) -> Unit, channelViewModel: ChannelViewModel = hiltViewModel())` | **ChannelViewModel** |
| 10 | `AddChannelScreen` | `addchannelscreen.kt` | `(onBack: () -> Unit, channelViewModel: ChannelViewModel = hiltViewModel())` | **ChannelViewModel** |
| 11 | `EditChannelScreen` | `editchannelscreen.kt` | `(channelId: Int, onBack: () -> Unit, channelViewModel: ChannelViewModel = hiltViewModel())` | **ChannelViewModel** |
| 12 | `NotificationsScreen` | `notificationsscreen.kt` | `(notificationViewModel: NotificationViewModel = hiltViewModel())` | **NotificationViewModel** |
| 13 | `PlansScreen` | `plansscreen.kt` | `(onNavigate: (String) -> Unit, billingViewModel: BillingViewModel = hiltViewModel())` | **BillingViewModel** |
| 14 | `PaymentsScreen` | `paymentsscreen.kt` | `(billingViewModel: BillingViewModel = hiltViewModel())` | **BillingViewModel** |
| 15 | `TicketsScreen` | `ticketsscreen.kt` | `(onNavigate: (String) -> Unit, ticketViewModel: TicketViewModel = hiltViewModel())` | **TicketViewModel** |
| 16 | `CreateTicketScreen` | `createticketscreen.kt` | `(onBack: () -> Unit, ticketViewModel: TicketViewModel = hiltViewModel())` | **TicketViewModel** |
| 17 | `TicketDetailScreen` | `ticketdetailscreen.kt` | `(ticketId: Int, onBack: () -> Unit, ticketViewModel: TicketViewModel = hiltViewModel())` | **TicketViewModel** |
| 18 | `SettingsScreen` | `settingsscreen.kt` | `(onBack: () -> Unit, settingsViewModel: SettingsViewModel = hiltViewModel())` | **SettingsViewModel** |
| 19 | `GoldTickerScreen` | `goldtickerscreen.kt` | `(onBack: () -> Unit, settingsViewModel: SettingsViewModel = hiltViewModel(), channelViewModel: ChannelViewModel = hiltViewModel())` | **SettingsViewModel**, **ChannelViewModel** |
| 20 | `AutoResponderScreen` | `autoresponderscreen.kt` | `(autoResponderViewModel: AutoResponderViewModel = hiltViewModel())` | **AutoResponderViewModel** |
| 21 | `WalletScreen` | `walletscreen.kt` | `(walletViewModel: WalletViewModel = hiltViewModel())` | **WalletViewModel** |
| 22 | `ReferralScreen` | `referralscreen.kt` | `(referralViewModel: ReferralViewModel = hiltViewModel())` | **ReferralViewModel** |
| 23 | `ProfileScreen` | `profilescreen.kt` | `(userName: String = "", userEmail: String = "", userRole: String = "", onLogout: () -> Unit = {}, onUpdateProfile: (String,String) -> Unit = {}, onChangePassword: (String,String,String) -> Unit = {})` | **None** (callback-only) |
| 24 | `AnalyticsScreen` | `analyticsscreen.kt` | `(analyticsViewModel: AnalyticsViewModel = hiltViewModel())` | **AnalyticsViewModel** |
| 25 | `AdminDashboardScreen` | `admindashboardscreen.kt` | `(totalUsers: Int = 0, activeUsers: Int = 0, suspendedUsers: Int = 0, totalPayments: String = "0", pendingPayments: Int = 0, openTickets: Int = 0, recentUsers: List<AdminUserBrief> = emptyList(), onNavigateUsers: () -> Unit = {})` | **None** (parameter-only) |
| 26 | `AdminPaymentsScreen` | `adminpaymentsscreen.kt` | `(adminViewModel: AdminViewModel = hiltViewModel())` | **AdminViewModel** |
| 27 | `AdminPlansScreen` | `adminplansscreen.kt` | `(adminViewModel: AdminViewModel = hiltViewModel())` | **AdminViewModel** |
| 28 | `AdminTicketsScreen` | `adminticketsscreen.kt` | `(onNavigateDetail: (Int) -> Unit = {}, adminViewModel: AdminViewModel = hiltViewModel())` | **AdminViewModel** |
| 29 | `AdminUsersScreen` | `adminusersscreen.kt` | `(users: List<AdminUser> = emptyList(), onSuspend: (Int) -> Unit = {}, onActivate: (Int) -> Unit = {})` | **None** (parameter-only) |

---

## 4. All API Interfaces & Method Signatures

### 4.1 Legacy Monolithic: `ApiService` (`apiservice.kt`)

Uses domain models from `models.kt`. **Gson serialization** (via `GsonConverterFactory` in RetrofitClient).

```
interface ApiService {
    // AUTH (8 methods)
    login(@Body LoginRequest): ApiResponse<AuthResponse>
    register(@Body RegisterRequest): ApiResponse<AuthResponse>
    logout(): ApiResponse<Any?>
    getMe(): ApiResponse<MeResponse>
    updateProfile(@Body ProfileRequest): ApiResponse<User>
    changePassword(@Body ChangePasswordRequest): ApiResponse<Any?>
    resetPassword(@Body Map<String,String>): ApiResponse<Any?>
    resetPasswordConfirm(@Body ResetPasswordConfirmRequest): ApiResponse<Any?>
    resetPasswordSms(@Body Map<String,String>): ApiResponse<Any?>
    verifySmsCode(@Body VerifySmsRequest): ApiResponse<Any?>

    // BOOTSTRAP & SYNC (2 methods)
    bootstrap(): ApiResponse<BootstrapData>
    sync(@Query since?): ApiResponse<SyncData>

    // CHANNELS (6 methods)
    getChannels(): ApiResponse<List<Channel>>
    createChannel(@Body Map): ApiResponse<Channel>
    getChannel(@Path id): ApiResponse<Channel>
    updateChannel(@Path id, @Body Map): ApiResponse<Channel>
    deleteChannel(@Path id): ApiResponse<any?>

    // POSTS (6 methods)
    getPosts(@Query status?, @Query limit, @Query offset): ApiResponse<List<Post>>
    createPost(@PartMap, @Part media_file?): ApiResponse<Post>
    getPost(@Path id): ApiResponse<Post>
    cancelPost(@Path id): ApiResponse<any?>
    retryPost(@Path id): ApiResponse<any?>

    // NOTIFICATIONS (3 methods)
    getNotifications(@Query limit, @Query offset): ApiResponse<NotificationListData>
    markNotificationRead(@Path id): ApiResponse<Map<String,Int>>
    markAllNotificationsRead(): ApiResponse<Map<String,Int>>

    // PLANS & BILLING (4 methods)
    getPlans(): ApiResponse<List<Plan>>
    submitPayment(@PartMap, @Part receipt_photo?): ApiResponse<Payment>
    getPayments(): ApiResponse<List<Payment>>
    validateCoupon(@Body Map): ApiResponse<CouponValidation>

    // TICKETS (5 methods)
    getTickets(): ApiResponse<List<Ticket>>
    createTicket(@PartMap, @Part attachment?): ApiResponse<Ticket>
    getTicketDetail(@Path id): ApiResponse<TicketDetail>
    replyTicket(@Path id, @PartMap, @Part attachment?): ApiResponse<any?>

    // SETTINGS (4 methods)
    getSettings(): ApiResponse<Settings>
    saveGoldSettings(@PartMap, @Part gold_image?): ApiResponse<any?>
    triggerGold(): ApiResponse<any?>
    saveAdvancedSettings(@Body Map): ApiResponse<any?>

    // AUTO RESPONDER (4 methods)
    getAutoReplies(): ApiResponse<List<AutoReplyRule>>
    addAutoReply(@Body Map): ApiResponse<AutoReplyRule>
    deleteAutoReply(@Path id): ApiResponse<any?>
    toggleAutoResponder(@Body Map): ApiResponse<any?>

    // WALLET & REFERRAL (3 methods)
    getWallet(): ApiResponse<WalletData>
    convertPoints(@Body Map): ApiResponse<ConvertPointsResult>
    getReferral(): ApiResponse<ReferralData>

    // ANALYTICS (2 methods)
    getAnalyticsLinks(): ApiResponse<List<LinkTracking>>
    getLinkDetail(@Path id): ApiResponse<LinkDetail>

    // ADMIN (16 methods)
    adminDashboard(): ApiResponse<AdminDashboard>
    adminUsers(@Query status?, @Query search?, @Query limit, @Query offset): ApiResponse<List<User>>
    adminSuspendUser(@Path id): ApiResponse<any?>
    adminActivateUser(@Path id): ApiResponse<any?>
    adminPayments(): ApiResponse<List<Payment>>
    adminApprovePayment(@Path id): ApiResponse<any?>
    adminTickets(): ApiResponse<List<Ticket>>
    adminReplyTicket(@Path id, @Body Map): ApiResponse<any?>
    adminPlans(): ApiResponse<List<Plan>>
    adminCreatePlan(@Body Map): ApiResponse<Plan>
    adminUpdatePlan(@Path id, @Body Map): ApiResponse<Plan>
    adminDeletePlan(@Path id): ApiResponse<any?>
    adminBroadcast(@Body Map): ApiResponse<any?>
    adminCreateDiscount(@Body Map): ApiResponse<any?>
    adminDeleteDiscount(@Path id): ApiResponse<any?>
}
```

### 4.2 New Modular APIs (use Moshi DTOs)

| Interface | File | Endpoint Count | Converter | Status |
|-----------|------|----------------|-----------|--------|
| `AdminApi` | `adminapi.kt` | 13 | Moshi | **Not wired to any ViewModel** |
| `AnalyticsApi` | `analyticsapi.kt` | 2 | Moshi | **Not wired to any ViewModel** |
| `AuthApi` | `authapi.kt` | 10 | Moshi | **Not wired to any ViewModel** |
| `AutoResponderApi` | `autoresponderapi.kt` | 4 | Moshi | **Not wired to any ViewModel** |
| `BillingApi` | `billingapi.kt` | 4 | Moshi | **Not wired to any ViewModel** |
| `BootstrapApi` | `bootstrapapi.kt` | 2 | Moshi | **Not wired to any ViewModel** |
| `ChannelApi` | `channelapi.kt` | 5 | Moshi | **Not wired to any ViewModel** |
| `NotificationApi` | `notificationapi.kt` | 3 | Moshi | **Not wired to any ViewModel** |
| `PostApi` | `postapi.kt` | 5 | Moshi | **Not wired to any ViewModel** |
| `ReferralApi` | `referralapi.kt` | 1 | Moshi | **Not wired to any ViewModel** |
| `SettingsApi` | `settingsapi.kt` | 4 | Moshi | **Not wired to any ViewModel** |
| `TicketApi` | `ticketapi.kt` | 4 | Moshi | **Not wired to any ViewModel** |
| `WalletApi` | `walletapi.kt` | 2 | Moshi | **Not wired to any ViewModel** |

**⚠️ CRITICAL FINDING:** All 13 new modular API interfaces are **dead code** — no ViewModel or other code instantiates them through a Hilt module or any DI container. The ViewModels exclusively use the legacy monolithic `ApiService`.

---

## 5. TokenManager & AuthInterceptor Implementations

### Implementation A: Hilt-based (NEW)

| Location | Class | Scope | DI Method |
|----------|-------|-------|-----------|
| `core/security/tokenmanager.kt` | `TokenManager` | `@Singleton` | `@Inject constructor(@ApplicationContext context: Context)` — uses `EncryptedSharedPreferences` with key `"***"` |
| `core/network/authinterceptor.kt` | `AuthInterceptor` | `@Singleton` | `@Inject constructor(tokenManager: TokenManager)` — adds `Bearer <token>` header |

### Implementation B: Legacy Singleton (OLD, ACTIVELY USED)

| Location | Class | Scope | DI Method |
|----------|-------|-------|-----------|
| `retrofitclient.kt` (inline) | `TokenManager` | Manual singleton (`getInstance(context)`) | Private constructor, `EncryptedSharedPreferences` with key `"auth_token"` |
| `retrofitclient.kt` (inline) | `AuthInterceptor` | Not singleton | `constructor(tokenManager: TokenManager)` — adds `Authorization: Bearer $it` header |

**Key Differences:**
- Hilt `TokenManager`: prefs file = `"postyar_secure_prefs.xml"`, token key = `"***"`
- Legacy `TokenManager`: prefs file = `"postyar_secure_prefs"`, token key = `"auth_token"`
- **Different encrypted prefs filenames AND different token keys!**

---

## 6. Moshi vs Gson Usage

### Moshi Users (`@JsonClass(generateAdapter = true)` + `@Json(name=...)`) — 18 files

**All DTO files under `data/remote/dto/`:**
- `analyticslinkdto.kt` — 3 classes
- `apiresponse.kt` — 3 classes
- `autoreplydto.kt` — 1 class
- `bootstrapdto.kt` — 8 classes
- `channeldto.kt` — 1 class
- `coupondto.kt` — 1 class
- `loginresponsedto.kt` — 3 classes
- `notificationdto.kt` — 1 class
- `paymentdto.kt` — 1 class
- `plandto.kt` — 1 class
- `postdto.kt` — 1 class
- `quotadto.kt` — 1 class
- `referraldto.kt` — 3 classes
- `settingsdto.kt` — 1 class
- `syncdto.kt` — 1 class
- `ticketdto.kt` — 2 classes
- `ticketreplydto.kt` — 1 class
- `userdto.kt` — 1 class
- `walletdto.kt` — 3 classes

**Total: ~38 Moshi-annotated data classes across 18 files**

### Gson Users

- **`retrofitclient.kt`**: Explicitly uses `GsonConverterFactory.create()` → drives `ApiService` which uses domain models from `models.kt`
- **`models.kt`**: Imports `com.google.gson.annotations.SerializedName` but **no field actually uses it** — relies on Gson's default property-name matching
- **`apiservice.kt`**: Returns `ApiResponse<T>` from `models.kt` (Gson-deserialized)

### Summary

| Layer | Serialization | Library | Actually Wired? |
|-------|--------------|---------|-----------------|
| Domain models (`models.kt`) | Gson (implicit) | `GsonConverterFactory` | ✅ Yes — used by all ViewModels |
| Remote DTOs (`dto/*.kt`) | Moshi (`@JsonClass`) | Would need `MoshiConverterFactory` | ❌ No — dead code |
| New API interfaces (`api/*.kt`) | Expect Moshi DTOs | Would need `MoshiConverterFactory` | ❌ No — dead code |

---

## 7. Hilt Annotation Usage

| File | Annotations |
|------|-------------|
| `mainactivity.kt` | `@AndroidEntryPoint` |
| `core/security/tokenmanager.kt` | `@Singleton`, `@Inject`, `@ApplicationContext` |
| `core/network/authinterceptor.kt` | `@Singleton`, `@Inject` |

**That's it.** Only 3 files use Hilt annotations.

**Missing Hilt annotations (problems):**
- `postyarapp.kt` — **No `@HiltAndroidApp`** on `Application` class ⚠️
- All 15 ViewModels — **No `@HiltViewModel`**, all use `AndroidViewModel(application)` manually
- No `@Module`, `@InstallIn`, `@Binds`, `@Provides` anywhere — no Hilt modules for providing repositories, API services, or Retrofit instances

**Conclusion:** Hilt is partially set up (annotations exist on TokenManager/AuthInterceptor) but **not functional** because:
1. Application lacks `@HiltAndroidApp`
2. No Hilt modules wire up Retrofit/API services
3. ViewModels don't use `@HiltViewModel` + `@Inject`

---

## 8. Room Annotation Usage

| File | Annotations |
|------|-------------|
| `channelentity.kt` | `@Entity(tableName="channels")`, `@PrimaryKey` |
| `notificationentity.kt` | `@Entity(tableName="notifications")`, `@PrimaryKey` |
| `postentity.kt` | `@Entity(tableName="posts")`, `@PrimaryKey` |
| `userentity.kt` | `@Entity(tableName="users")`, `@PrimaryKey` |
| `postyardatabase.kt` | `@Database(entities=[...4 entities...], version=1, exportSchema=false)` |
| `syncdao.kt` | `@Dao` (×4), `@Query` (×20), `@Insert` (×8), `@Delete` (×5), `OnConflictStrategy.REPLACE` |

**Room is fully defined but appears unused by ViewModels** — none of the 15 ViewModels reference any DAO or `PostyarDatabase`. All data comes from remote API calls.

---

## 9. Navigation Routes Defined in PostyarNavigation

| Route | Destination Screen | Arguments |
|-------|-------------------|-----------|
| `"splash"` | `SplashScreen` | none |
| `"login"` | `LoginScreen` | none |
| `"register"` | `RegisterScreen` | none |
| `"forgotPassword"` | `ForgotPasswordScreen` | none |
| `"dashboard"` | `DashboardScreen` | none |
| `"posts"` | `PostsScreen` | none |
| `"posts/create"` | `CreatePostScreen` | none |
| `"channels"` | `ChannelsScreen` | none |
| `"channels/add"` | `AddChannelScreen` | none |
| `"notifications"` | `NotificationsScreen` | none |
| `"plans"` | `PlansScreen` | none |
| `"payments"` | `PaymentsScreen` | none |
| `"tickets"` | `TicketsScreen` | none |
| `"tickets/create"` | `CreateTicketScreen` | none |
| `"tickets/detail/{id}" | `TicketDetailScreen` | `id: Int` |
| `"settings"` | `SettingsScreen` | none |
| `"settings/gold"` | `GoldTickerScreen` | none |
| `"settings/autoresponder"` | `AutoResponderScreen` | none |
| `"wallet"` | `WalletScreen` | none |
| `"referral"` | `ReferralScreen` | none |
| `"analytics"` | `AnalyticsScreen` | none |
| `"profile"` | `ProfileScreen` | none |
| `"admin/dashboard"` | `AdminDashboardScreen` | none |
| `"admin/users"` | `AdminUsersScreen` | none |

**Total: 24 routes**

---

## 10. Screens Without Navigation Routes

| Screen File | Screen Function | Status |
|-------------|-----------------|--------|
| `posts/postdetailscreen.kt` | `PostDetailScreen` | ⚠️ **Referenced as `"posts/detail/{id}"` in DashboardScreen's `onNavigate` callback, but NO `composable()` route defined** |
| `channels/editchannelscreen.kt` | `EditChannelScreen` | ⚠️ **No route defined at all** — not referenced in navigation |
| `admin/adminpaymentsscreen.kt` | `AdminPaymentsScreen` | ⚠️ **No route defined** |
| `admin/adminplansscreen.kt` | `AdminPlansScreen` | ⚠️ **No route defined** |
| `admin/adminticketsscreen.kt` | `AdminTicketsScreen` | ⚠️ **No route defined** |

**Also notable:** PlansScreen navigates to `"payments/create/{plan.id}"` but no such route exists in NavHost.

---

## 11. PostyarTopBar / QuotaCard / PersianNumberText Call Sites

### PostyarTopBar Call Sites

| Caller File | Arguments Passed |
|-------------|-----------------|
| `adminpaymentsscreen.kt` | `title="پرداخت‌ها (مدیر)"` |
| `adminplansscreen.kt` | `title="مدیریت پلن‌ها"` |
| `adminticketsscreen.kt` | `title="تیکت‌ها (مدیر)"` |
| `analyticsscreen.kt` | `title="تحلیل لینک‌ها"` |
| `billing/paymentsscreen.kt` | `title="تاریخچه پرداخت‌ها"` |
| `billing/plansscreen.kt` | `title="پلن‌های اشتراک"` |
| `channels/addchannelscreen.kt` | `title="افزودن کانال", onBack=onBack` |
| `channels/channelsscreen.kt` | `title="کانال‌ها"` |
| `channels/editchannelscreen.kt` | `title="ویرایش کانال", onBack=onBack` |
| `posts/createpostscreen.kt` | `title="ایجاد پست جدید", onBack=onBack` |
| `posts/postdetailscreen.kt` | `title="جزئیات پست", onBack=onBack` |
| `notifications/notificationsscreen.kt` | `title="اعلان‌ها"` (with custom actions for markAllRead) |
| `settings/settingsscreen.kt` | `title="تنظیمات", onBack=onBack` |
| `settings/goldtickerscreen.kt` | `title="تیکر قیمت طلا", onBack=onBack` |
| `tickets/createticketscreen.kt` | `title="تیکت جدید", onBack=onBack` |
| `tickets/ticketdetailscreen.kt` | `title="جزئیات تیکت", onBack=onBack` |
| `tickets/ticketsscreen.kt` | `title="تیکت‌های پشتیبانی"` |
| `wallet/walletscreen.kt` | `title="کیف پول"` |
| `referral/referralscreen.kt` | `title="زیرمجموعه‌گیری"` |
| `main/dashboardscreen.kt` | `title="پُست‌یار"` (with custom actions for notifications badge) |

**Note:** `DashboardScreen` passes `actions` lambda to PostyarTopBar — but `PostyarTopBar` doesn't have an `actions` parameter! It only has `onNotificationClick`, `onBackClick`, `unreadCount`, `showNotification`. ⚠️

### QuotaCard Call Sites

| Caller File | Arguments |
|-------------|-----------|
| `components/quotacard.kt` (definition) | `(label, used, limit, modifier)` |
| `main/dashboardscreen.kt` | `(title, used, limit, icon={...})` — **passes `icon` param that doesn't exist on QuotaCard!** ⚠️ |

### PersianNumberText Call Sites

| Caller File | Usage |
|-------------|-------|
| `adminpaymentsscreen.kt` | `${payment.amount} تومان` |
| `adminplansscreen.kt` | `${plan.price} تومان - ${plan.durationDays} روز` |
| `analyticsscreen.kt` | `${link.totalClicks}`, `${link.uniqueClicks}`, `${day.clicks} کلیک` |
| `billing/paymentsscreen.kt` | `${payment.amount} تومان` |
| `billing/plansscreen.kt` | `${plan.price} تومان` |
| `main/dashboardscreen.kt` | `unreadCount.toString()` (inside PostyarTopBar actions) |
| `posts/postdetailscreen.kt` | `${post.clickCount} کلیک` |
| `posts/postsscreen.kt` | `${post.clickCount} کلیک` |
| `referral/referralscreen.kt` | `${stats.total}`, `${referralPoints}` |
| `wallet/walletscreen.kt` | balance formatted, transaction amounts |

---

## 12. Screen → ViewModel → Property Cross-Reference

| Screen | ViewModel | Properties Accessed (read) | Properties Accessed (write/actions) |
|--------|-----------|----------------------------|-----------------------------------|
| **SplashScreen** | *(none)* | `authState` (param) | `checkAuth()` (param callback) |
| **LoginScreen** | `AuthViewModel` | `authState`, `loginError` | `login()`, `checkExistingSession()` |
| **RegisterScreen** | `AuthViewModel` | `authState`, `registerError` | `register()` |
| **ForgotPasswordScreen** | `AuthViewModel` | `passwordResetSent` | `requestPasswordReset()`, `requestSmsReset()`, `confirmSmsReset()` |
| **DashboardScreen** | `BootstrapViewModel` | `bootstrapData` (→ user, quota, posts, unreadCount), `isLoading`, `error` | `loadBootstrap()` |
| **DashboardScreen** | `SyncViewModel` | `syncData` (→ quota, recentPosts) | *(implicit via LaunchedEffect?)* |
| **PostsScreen** | `PostViewModel` | `posts`, `isLoading`, `statusFilter` | `loadPosts()`, `setStatusFilter()` |
| **CreatePostScreen** | `PostViewModel` | `isSubmitting` | `createPost()` |
| **CreatePostScreen** | `ChannelViewModel` | `channels` | `loadChannels()` |
| **PostDetailScreen** | `PostViewModel` | `postDetail`, `isLoading` | `loadPostDetail()`, `retryPost()`, `cancelPost()` |
| **ChannelsScreen** | `ChannelViewModel` | `channels`, `isLoading` | `loadChannels()`, `deleteChannel()` |
| **AddChannelScreen** | `ChannelViewModel` | `isSubmitting`, `error` | `addChannel()` |
| **EditChannelScreen** | `ChannelViewModel` | `channelDetail`, `isLoading`, `isSubmitting` | `loadChannelDetail()`, `updateChannel()` |
| **NotificationsScreen** | `NotificationViewModel` | `notifications`, `unreadCount`, `isLoading` | `loadNotifications()`, `markRead()`, `markAllRead()` |
| **PlansScreen** | `BillingViewModel` | `plans`, `isLoading` | `loadPlans()` |
| **PaymentsScreen** | `BillingViewModel` | `payments`, `isLoading` | `loadPayments()` |
| **TicketsScreen** | `TicketViewModel` | `tickets`, `isLoading` | `loadTickets()` |
| **CreateTicketScreen** | `TicketViewModel` | `isSubmitting` | `createTicket()` |
| **TicketDetailScreen** | `TicketViewModel` | `ticketDetail`, `isLoading` | `loadTicketDetail()`, `replyTicket()` |
| **SettingsScreen** | `SettingsViewModel` | `settings`, `isLoading`, `isSaving` | `loadSettings()`, `saveAdvanced()` |
| **GoldTickerScreen** | `SettingsViewModel` | `isSaving` | `triggerGold()`, `saveGold()` |
| **GoldTickerScreen** | `ChannelViewModel` | `channels` | `loadChannels()` |
| **AutoResponderScreen** | `AutoResponderViewModel` | `autoReplies`, `isLoading` | `loadAutoReplies()`, `toggleChannel()`, `deleteRule()` |
| **WalletScreen** | `WalletViewModel` | `wallet`, `isLoading`, `isConverting` | `loadWallet()`, `convertPoints()` |
| **ReferralScreen** | `ReferralViewModel` | `referral`, `isLoading` | `loadReferral()` |
| **ProfileScreen** | *(none)* | N/A (callback-based) | Callbacks only |
| **AnalyticsScreen** | `AnalyticsViewModel` | `links`, `isLoading`, `linkDetail`, `isDetailLoading` | `loadLinks()`, `loadLinkDetail()` |
| **AdminDashboardScreen** | *(none)* | N/A (parameter-based) | `onNavigateUsers()` callback |
| **AdminPaymentsScreen** | `AdminViewModel` | `allPayments`, `isLoading` | `loadPayments()`, `approvePayment()` |
| **AdminPlansScreen** | `AdminViewModel` | `plans`, `isLoading` | `loadPlans()`, `deletePlan()` |
| **AdminTicketsScreen** | `AdminViewModel` | `allTickets`, `isLoading` | `loadTickets()` |
| **AdminUsersScreen** | *(none)* | N/A (parameter-based) | `onSuspend()`, `onActivate()` callbacks |

---

## 13. Critical Issues & Syntax Problems Found

### 🔴 CRITICAL — Build-Breaking Issues

#### C1. Duplicate `PostyarTopBar` Signature Mismatch
- **File:** `postyartopbar.kt` defines: `PostyarTopBar(onNotificationClick, onBackClick?, unreadCount?, showNotification?)`
- **File:** `dashboardscreen.kt` calls: `PostyarTopBar(title="...", actions={...})`
- **Problem:** `title` and `actions` parameters don't exist on `PostyarTopBar`. Will fail compilation.

#### C2. Duplicate `QuotaCard` Signature Mismatch
- **File:** `quotacard.kt` defines: `QuotaCard(label, used, limit, modifier?)`
- **File:** `dashboardscreen.kt` calls: `QuotaCard(title=..., used=..., limit=..., icon={...})`
- **Problem:** `title` (should be `label`) and `icon` parameters don't exist. Will fail compilation.

#### C3. Undefined `PostyarNavigationItem` Reference
- **File:** `dashboardscreen.kt` imports `com.postyar.app.presentation.navigation.PostyarNavigationItem`
- **Problem:** No such class exists anywhere in the project. Compilation error.

#### C4. Stray Import Statements After Class Body
- **File:** `registerscreen.kt` — After the closing brace of `RegisterScreen`, there are two import lines:
  ```kotlin
  import androidx.compose.material.icons.Icons
  import androidx.compose.material.icons.filled.ArrowBack
  ```
- **Problem:** Imports after declarations are a syntax error in Kotlin.

#### C5. Missing Navigation Routes
- `PostDetailScreen` — referenced as `"posts/detail/{id)"` but no `composable()` in NavHost
- `EditChannelScreen` — exists as file, no route
- `AdminPaymentsScreen`, `AdminPlansScreen`, `AdminTicketsScreen` — exist as files, no routes
- `"payments/create/{plan.id}"` — navigated to from PlansScreen, no route defined

#### C6. `PostyarApp` Missing `@HiltAndroidApp`
- **File:** `postyarapp.kt`
- **Problem:** `MainActivity` has `@AndroidEntryPoint` but `PostyarApp` lacks `@HiltAndroidApp`. Hilt cannot function.

### 🟠 MAJOR — Architecture Issues

#### A1. Dual TokenManager / AuthInterceptor (Two Implementations)
- Hilt versions in `core/` package (unused by any consumer code)
- Legacy versions inline in `retrofitclient.kt` (actively used by all ViewModels)
- Different encryption keys and prefs filenames

#### A2. Dead Code: 13 Modular API Interfaces + 18 DTO Files
- All files under `data/remote/api/` and `data/remote/dto/` are completely unwired
- No Hilt module provides them
- No ViewModel references them
- Use Moshi serialization but no `MoshiConverterFactory` exists in project

#### A3. All ViewModels Use `AndroidViewModel(application)` Pattern
- None use `@HiltViewModel` + `@Inject constructor(...)`
- All manually call `TokenManager.getInstance(application)` and `RetrofitClient.getInstance(tokenManager)`
- Cannot benefit from Hilt DI, testing mocks, or lifecycle-aware injection

#### A4. Room Database Defined But Unused
- 4 entities, 4 DAOs with full CRUD + Flow support
- No repository, no ViewModel calls any DAO
- All data fetched directly from network via `ApiService`

#### A5. `ProfileScreen` Is Callback-Based, Not Connected to ProfileViewModel
- `ProfileViewModel` exists with full implementation
- `ProfileScreen` takes raw callbacks, never creates/injects `ProfileViewModel`
- `PostyarNavigation` doesn't pass any data to `ProfileScreen`

#### A6. Admin Screens Inconsistent
- `AdminDashboardScreen` and `AdminUsersScreen` take raw parameters (no VM)
- `AdminPaymentsScreen`, `AdminPlansScreen`, `AdminTicketsScreen` use `AdminViewModel` via `hiltViewModel()`
- But `AdminViewModel` extends `AndroidViewModel(application)` — `hiltViewModel()` will fail without proper Hilt setup

### 🟡 MINOR — Code Quality Issues

#### M1. `models.kt` Imports Gson But Never Uses `@SerializedName`
- Wasted import, potential confusion about serialization strategy

#### M2. Inconsistent Error Handling
- Some ViewModels silently swallow exceptions (`catch (_: Exception) {}`)
- Others set error messages
- No unified error handling strategy

#### M3. Admin Screens Define Their Own Data Classes
- `AdminUser` in `adminuserscreen.kt`
- `AdminStat`, `AdminUserBrief` in `admindashboardscreen.kt`
- These duplicate domain models from `models.kt`

#### M4. `AdminUsersScreen` Has Its Own TopAppBar
- Unlike all other screens that use `PostyarTopBar`, this one creates a raw `TopAppBar`
- Inconsistent UI pattern

#### M5. Hardcoded Base URL
- `retrofitclient.kt` hardcodes `https://asovin.ir/api/v1/`
- No build-flavor or config-based URL switching

---

## Appendix A: Complete Import Map By Library

### Compose UI (`androidx.compose.*`)
Used in: All screen files, all component files, `mainactivity.kt`, `theme.kt`

### Hilt/Dagger (`dagger.hilt.*`, `javax.inject.*`)
- `mainactivity.kt` — `@AndroidEntryPoint`, `hiltViewModel()`
- `core/security/tokenmanager.kt` — `@Singleton`, `@Inject`, `@ApplicationContext`
- `core/network/authinterceptor.kt` — `@Singleton`, `@Inject`
- Most screens — `hiltViewModel()` default parameter

### OkHttp3 / Retrofit (`okhttp3.*`, `retrofit2.*`)
- `core/network/authinterceptor.kt` — `Interceptor`, `Response`
- `data/remote/retrofitclient.kt` — full client setup
- `data/remote/apiservice.kt` — endpoint definitions
- `data/remote/api/*.kt` — endpoint definitions
- All ViewModels — network calls

### Room (`androidx.room.*`)
- `data/local/*.kt` — entities, database, DAOs

### Moshi (`com.squareup.moshi.*`)
- `data/remote/dto/*.kt` — all DTOs

### Gson (`com.google.gson.*`)
- `domain/models.kt` — imported but annotations unused
- `data/remote/retrofitclient.kt` — `GsonConverterFactory`

### Security (`androidx.security.crypto.*`)
- `core/security/tokenmanager.kt` — `EncryptedSharedPreferences`, `MasterKey`
- `data/remote/retrofitclient.kt` — same (legacy copy)

### Coroutines / Flow (`kotlinx.coroutines.*`, `kotlinx.flow.*`)
- All ViewModels — `viewModelScope`, `MutableStateFlow`, `StateFlow`
- `syncdao.kt` — `Flow` return types

---
*End of Audit*
