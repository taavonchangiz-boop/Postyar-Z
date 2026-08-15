package com.postyar.app.presentation.navigation

import androidx.compose.runtime.Composable
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.navigation.NavHostController
import androidx.navigation.NavType
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.rememberNavController
import androidx.navigation.navArgument
import com.postyar.app.presentation.screens.admin.AdminDashboardScreen
import com.postyar.app.presentation.screens.admin.AdminUsersScreen
import com.postyar.app.presentation.screens.analytics.AnalyticsScreen
import com.postyar.app.presentation.screens.auth.ForgotPasswordScreen
import com.postyar.app.presentation.screens.auth.LoginScreen
import com.postyar.app.presentation.screens.auth.RegisterScreen
import com.postyar.app.presentation.screens.auth.SplashScreen
import com.postyar.app.presentation.screens.billing.PaymentsScreen
import com.postyar.app.presentation.screens.billing.PlansScreen
import com.postyar.app.presentation.screens.channels.AddChannelScreen
import com.postyar.app.presentation.screens.channels.ChannelsScreen
import com.postyar.app.presentation.screens.main.DashboardScreen
import com.postyar.app.presentation.screens.notifications.NotificationsScreen
import com.postyar.app.presentation.screens.posts.CreatePostScreen
import com.postyar.app.presentation.screens.posts.PostsScreen
import com.postyar.app.presentation.screens.profile.ProfileScreen
import com.postyar.app.presentation.screens.referral.ReferralScreen
import com.postyar.app.presentation.screens.settings.AutoResponderScreen
import com.postyar.app.presentation.screens.settings.GoldTickerScreen
import com.postyar.app.presentation.screens.settings.SettingsScreen
import com.postyar.app.presentation.screens.tickets.CreateTicketScreen
import com.postyar.app.presentation.screens.tickets.TicketDetailScreen
import com.postyar.app.presentation.screens.tickets.TicketsScreen
import com.postyar.app.presentation.screens.wallet.WalletScreen
import com.postyar.app.presentation.viewmodels.AuthViewModel

@Composable
fun PostyarNavigation(
    navController: NavHostController,
    authViewModel: AuthViewModel
) {
    val authState by authViewModel.authState.collectAsState()

    NavHost(navController = navController, startDestination = "splash") {
        composable("splash") {
            SplashScreen(
                authState = authState,
                checkAuth = { authViewModel.checkExistingSession() },
                onAuthChecked = { authenticated ->
                    if (authenticated) {
                        navController.navigate("dashboard") { popUpTo("splash") { inclusive = true } }
                    } else {
                        navController.navigate("login") { popUpTo("splash") { inclusive = true } }
                    }
                }
            )
        }

        composable("login") {
            LoginScreen(
                viewModel = authViewModel,
                onNavigateRegister = { navController.navigate("register") },
                onNavigateForgot = { navController.navigate("forgotPassword") },
                onLoginSuccess = { navController.navigate("dashboard") { popUpTo("login") { inclusive = true } } }
            )
        }

        composable("register") {
            RegisterScreen(
                viewModel = authViewModel,
                onNavigateBack = { navController.popBackStack() },
                onRegisterSuccess = { navController.navigate("dashboard") { popUpTo("register") { inclusive = true } } }
            )
        }

        composable("forgotPassword") {
            ForgotPasswordScreen(
                viewModel = authViewModel,
                onNavigateBack = { navController.popBackStack() }
            )
        }

        composable("dashboard") {
            DashboardScreen(
                onNavigate = { route -> navController.navigate(route) }
            )
        }

        composable("posts") {
            PostsScreen(
                onNavigate = { route -> navController.navigate(route) }
            )
        }

        composable("posts/create") {
            CreatePostScreen(
                onBack = { navController.popBackStack() }
            )
        }

        composable("channels") {
            ChannelsScreen(
                onNavigate = { route -> navController.navigate(route) }
            )
        }

        composable("channels/add") {
            AddChannelScreen(
                onBack = { navController.popBackStack() }
            )
        }

        composable("notifications") {
            NotificationsScreen()
        }

        composable("plans") {
            PlansScreen(
                onNavigate = { route -> navController.navigate(route) }
            )
        }

        composable("payments") {
            PaymentsScreen()
        }

        composable("tickets") {
            TicketsScreen(
                onNavigate = { route -> navController.navigate(route) }
            )
        }

        composable("tickets/create") {
            CreateTicketScreen(
                onBack = { navController.popBackStack() }
            )
        }

        composable("tickets/detail/{id}", arguments = listOf(navArgument("id") { type = NavType.IntType })) {
            TicketDetailScreen(
                ticketId = it.arguments?.getInt("id") ?: return@composable,
                onBack = { navController.popBackStack() }
            )
        }

        composable("settings") {
            SettingsScreen(
                onBack = { navController.popBackStack() }
            )
        }

        composable("settings/gold") {
            GoldTickerScreen(
                onBack = { navController.popBackStack() }
            )
        }

        composable("settings/autoresponder") {
            AutoResponderScreen()
        }

        composable("wallet") {
            WalletScreen()
        }

        composable("referral") {
            ReferralScreen()
        }

        composable("analytics") {
            AnalyticsScreen()
        }

        composable("profile") {
            ProfileScreen(
                onLogout = {
                    authViewModel.logout()
                    navController.navigate("login") { popUpTo(0) { inclusive = true } }
                }
            )
        }

        composable("admin/dashboard") {
            AdminDashboardScreen(
                onNavigateUsers = { navController.navigate("admin/users") }
            )
        }

        composable("admin/users") {
            AdminUsersScreen()
        }
    }
}
