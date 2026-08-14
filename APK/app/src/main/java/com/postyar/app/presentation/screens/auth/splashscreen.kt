package com.postyar.app.presentation.screens.auth

import androidx.compose.foundation.layout.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import kotlinx.coroutines.delay

@Composable
fun SplashScreen(
    checkAuth: () -> Unit,
    onAuthChecked: (Boolean) -> Unit,
    authState: com.postyar.app.presentation.viewmodels.AuthState = com.postyar.app.presentation.viewmodels.AuthState.IDLE
) {
    LaunchedEffect(authState) {
        when (authState) {
            com.postyar.app.presentation.viewmodels.AuthState.AUTHENTICATED -> onAuthChecked(true)
            com.postyar.app.presentation.viewmodels.AuthState.UNAUTHENTICATED -> onAuthChecked(false)
            else -> {}
        }
    }
    LaunchedEffect(Unit) { delay(500); checkAuth() }

    Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
        Column(horizontalAlignment = Alignment.CenterHorizontally) {
            Text(
                text = "\u067E\u064F\u0633\u062A\u200C\u06CC\u0627\u0631",
                style = MaterialTheme.typography.displayLarge,
                fontWeight = FontWeight.Bold,
                color = MaterialTheme.colorScheme.primary,
                textAlign = TextAlign.Center
            )
            Spacer(modifier = Modifier.height(24.dp))
            CircularProgressIndicator()
        }
    }
}