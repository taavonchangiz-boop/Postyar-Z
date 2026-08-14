package com.postyar.app.presentation.screens.auth

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.unit.dp
import com.postyar.app.presentation.viewmodels.AuthViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ForgotPasswordScreen(
    viewModel: AuthViewModel,
    onNavigateBack: () -> Unit
) {
    var selectedTab by remember { mutableIntStateOf(0) }
    var email by remember { mutableStateOf("") }
    var phone by remember { mutableStateOf("") }
    var smsCode by remember { mutableStateOf("") }
    var newPassword by remember { mutableStateOf("") }
    var confirmPass by remember { mutableStateOf("") }
    val resetSent by viewModel.passwordResetSent.collectAsState()

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("بازیابی رمز عبور") },
                navigationIcon = { IconButton(onClick = onNavigateBack) { Icon(Icons.Default.ArrowBack, "بازگشت") } }
            )
        }
    ) { padding ->
        Column(modifier = Modifier.padding(padding).padding(24.dp)) {
            TabRow(selectedTabIndex = selectedTab) {
                Tab(selected = selectedTab == 0, onClick = { selectedTab = 0 }, text = { Text("ایمیل") })
                Tab(selected = selectedTab == 1, onClick = { selectedTab = 1 }, text = { Text("پیامک") })
            }
            Spacer(modifier = Modifier.height(24.dp))
            if (selectedTab == 0) {
                if (resetSent) {
                    Text("اگر ایمیل در سامانه ثبت شده باشد، لینک بازیابی ارسال خواهد شد.", style = MaterialTheme.typography.bodyLarge)
                } else {
                    OutlinedTextField(value = email, onValueChange = { email = it }, label = { Text("ایمیل") }, keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Email), modifier = Modifier.fillMaxWidth(), singleLine = true)
                    Spacer(modifier = Modifier.height(16.dp))
                    Button(onClick = { viewModel.requestPasswordReset(email) }, modifier = Modifier.fillMaxWidth(), enabled = email.isNotBlank()) { Text("ارسال لینک بازیابی") }
                }
            } else {
                if (!resetSent) {
                    OutlinedTextField(value = phone, onValueChange = { phone = it }, label = { Text("شماره موبایل") }, keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Phone), modifier = Modifier.fillMaxWidth(), singleLine = true)
                    Spacer(modifier = Modifier.height(16.dp))
                    Button(onClick = { viewModel.requestSmsReset(phone) }, modifier = Modifier.fillMaxWidth(), enabled = phone.isNotBlank()) { Text("ارسال کد تایید") }
                } else {
                    OutlinedTextField(value = smsCode, onValueChange = { smsCode = it }, label = { Text("کد تایید") }, modifier = Modifier.fillMaxWidth(), singleLine = true)
                    Spacer(modifier = Modifier.height(8.dp))
                    OutlinedTextField(value = newPassword, onValueChange = { newPassword = it }, label = { Text("رمز عبور جدید") }, visualTransformation = PasswordVisualTransformation(), modifier = Modifier.fillMaxWidth(), singleLine = true)
                    Spacer(modifier = Modifier.height(8.dp))
                    OutlinedTextField(value = confirmPass, onValueChange = { confirmPass = it }, label = { Text("تکرار رمز عبور") }, visualTransformation = PasswordVisualTransformation(), modifier = Modifier.fillMaxWidth(), singleLine = true)
                    Spacer(modifier = Modifier.height(16.dp))
                    Button(onClick = { viewModel.confirmSmsReset(smsCode, newPassword, confirmPass) { onNavigateBack() } }, modifier = Modifier.fillMaxWidth(), enabled = smsCode.isNotBlank() && newPassword.isNotBlank()) { Text("تایید و تغییر رمز") }
                }
            }
        }
    }
}