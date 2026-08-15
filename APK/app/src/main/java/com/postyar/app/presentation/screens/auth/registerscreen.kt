package com.postyar.app.presentation.screens.auth

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.unit.dp
import com.postyar.app.presentation.viewmodels.AuthViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun RegisterScreen(
    viewModel: AuthViewModel,
    onNavigateBack: () -> Unit,
    onRegisterSuccess: () -> Unit
) {
    val authState by viewModel.authState.collectAsState()
    val error by viewModel.registerError.collectAsState()
    var name by remember { mutableStateOf("") }
    var email by remember { mutableStateOf("") }
    var password by remember { mutableStateOf("") }
    var confirmPass by remember { mutableStateOf("") }
    var businessName by remember { mutableStateOf("") }
    var businessType by remember { mutableStateOf("") }
    var ref by remember { mutableStateOf("") }

    LaunchedEffect(authState) {
        if (authState == com.postyar.app.presentation.viewmodels.AuthState.AUTHENTICATED) onRegisterSuccess()
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("ثبت‌نام") },
                navigationIcon = { IconButton(onClick = onNavigateBack) { Icon(Icons.Default.ArrowBack, "بازگشت") } }
            )
        }
    ) { padding ->
        Column(
            modifier = Modifier.padding(padding).padding(horizontal = 24.dp).fillMaxSize(),
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.spacedBy(12.dp)
        ) {
            Spacer(modifier = Modifier.height(24.dp))
            OutlinedTextField(value = name, onValueChange = { name = it }, label = { Text("نام و نام خانوادگی") }, modifier = Modifier.fillMaxWidth(), singleLine = true)
            OutlinedTextField(value = email, onValueChange = { email = it }, label = { Text("ایمیل") }, keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Email), modifier = Modifier.fillMaxWidth(), singleLine = true)
            OutlinedTextField(value = password, onValueChange = { password = it }, label = { Text("رمز عبور") }, visualTransformation = PasswordVisualTransformation(), modifier = Modifier.fillMaxWidth(), singleLine = true)
            OutlinedTextField(value = confirmPass, onValueChange = { confirmPass = it }, label = { Text("تکرار رمز عبور") }, visualTransformation = PasswordVisualTransformation(), modifier = Modifier.fillMaxWidth(), singleLine = true)
            OutlinedTextField(value = businessName, onValueChange = { businessName = it }, label = { Text("نام کسب‌وکار (اختیاری)") }, modifier = Modifier.fillMaxWidth(), singleLine = true)
            OutlinedTextField(value = businessType, onValueChange = { businessType = it }, label = { Text("نوع کسب‌وکار (اختیاری)") }, modifier = Modifier.fillMaxWidth(), singleLine = true)
            OutlinedTextField(value = ref, onValueChange = { ref = it }, label = { Text("کد دعوت (اختیاری)") }, modifier = Modifier.fillMaxWidth(), singleLine = true)
            if (error.isNotEmpty()) Text(text = error, color = MaterialTheme.colorScheme.error, style = MaterialTheme.typography.bodySmall)
            Button(
                onClick = { viewModel.register(name, email, password, confirmPass, businessName, businessType, ref.ifBlank { null }) },
                modifier = Modifier.fillMaxWidth().height(50.dp),
                enabled = name.isNotBlank() && email.isNotBlank() && password.isNotBlank() && confirmPass.isNotBlank() && authState != com.postyar.app.presentation.viewmodels.AuthState.LOADING
            ) {
                if (authState == com.postyar.app.presentation.viewmodels.AuthState.LOADING) CircularProgressIndicator(modifier = Modifier.size(24.dp), color = MaterialTheme.colorScheme.onPrimary, strokeWidth = 2.dp) else Text("ثبت‌نام")
            }
        }
    }
}
