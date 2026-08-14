package com.postyar.app.presentation.screens.auth

import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.text.style.TextDecoration
import androidx.compose.ui.unit.dp
import com.postyar.app.presentation.viewmodels.AuthViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun LoginScreen(
    viewModel: AuthViewModel,
    onNavigateRegister: () -> Unit,
    onNavigateForgot: () -> Unit,
    onLoginSuccess: () -> Unit
) {
    val authState by viewModel.authState.collectAsState()
    val error by viewModel.loginError.collectAsState()
    var email by remember { mutableStateOf("") }
    var password by remember { mutableStateOf("") }
    var ref by remember { mutableStateOf("") }
    var showRef by remember { mutableStateOf(false) }

    LaunchedEffect(authState) {
        if (authState == com.postyar.app.presentation.viewmodels.AuthState.AUTHENTICATED) onLoginSuccess()
    }

    Scaffold(
        topBar = {
            TopAppBar(title = { Text("پُست‌یار", fontWeight = FontWeight.Bold) })
        }
    ) { padding ->
        Column(
            modifier = Modifier
                .padding(padding)
                .padding(horizontal = 24.dp)
                .fillMaxSize(),
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.Center
        ) {
            Text(
                text = "ورود به حساب",
                style = MaterialTheme.typography.headlineMedium,
                fontWeight = FontWeight.Bold
            )
            Spacer(modifier = Modifier.height(32.dp))

            OutlinedTextField(
                value = email, onValueChange = { email = it },
                label = { Text("ایمیل") },
                keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Email),
                modifier = Modifier.fillMaxWidth(),
                singleLine = true
            )
            Spacer(modifier = Modifier.height(16.dp))
            OutlinedTextField(
                value = password, onValueChange = { password = it },
                label = { Text("رمز عبور") },
                visualTransformation = PasswordVisualTransformation(),
                modifier = Modifier.fillMaxWidth(),
                singleLine = true
            )
            Spacer(modifier = Modifier.height(8.dp))
            Text(
                text = if (showRef) "پنهان کد دعوت" else "داری کد دعوت؟",
                style = MaterialTheme.typography.bodySmall,
                color = MaterialTheme.colorScheme.primary,
                modifier = Modifier.clickable { showRef = !showRef },
                textDecoration = if (!showRef) TextDecoration.Underline else null
            )
            if (showRef) {
                OutlinedTextField(
                    value = ref, onValueChange = { ref = it },
                    label = { Text("کد دعوت") },
                    modifier = Modifier.fillMaxWidth(), singleLine = true
                )
            }
            Spacer(modifier = Modifier.height(24.dp))

            if (error.isNotEmpty()) {
                Text(text = error, color = MaterialTheme.colorScheme.error, style = MaterialTheme.typography.bodySmall)
                Spacer(modifier = Modifier.height(8.dp))
            }

            Button(
                onClick = { viewModel.login(email, password, ref.ifBlank { null }) },
                modifier = Modifier.fillMaxWidth().height(50.dp),
                enabled = email.isNotBlank() && password.isNotBlank() && authState != com.postyar.app.presentation.viewmodels.AuthState.LOADING
            ) {
                if (authState == com.postyar.app.presentation.viewmodels.AuthState.LOADING) CircularProgressIndicator(
                    modifier = Modifier.size(24.dp), color = MaterialTheme.colorScheme.onPrimary, strokeWidth = 2.dp
                ) else Text("ورود")
            }
            Spacer(modifier = Modifier.height(16.dp))
            Row(horizontalArrangement = Arrangement.spacedBy(16.dp)) {
 TextButton(onClick = onNavigateRegister) { Text("ثبت‌نام") }
                TextButton(onClick = onNavigateForgot) { Text("فراموشی رمز") }
            }
        }
    }
}
