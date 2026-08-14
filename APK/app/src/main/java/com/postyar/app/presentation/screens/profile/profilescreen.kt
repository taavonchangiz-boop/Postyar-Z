package com.postyar.app.presentation.screens.profile

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.unit.dp

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ProfileScreen(
    userName: String = "",
    userEmail: String = "",
    userRole: String = "",
    onLogout: () -> Unit = {},
    onUpdateProfile: (String, String) -> Unit = { _, _ -> },
    onChangePassword: (String, String, String) -> Unit = { _, _, _ -> }
) {
    var name by remember { mutableStateOf(userName) }
    var email by remember { mutableStateOf(userEmail) }
    var showChangePassword by remember { mutableStateOf(false) }
    var showLogoutDialog by remember { mutableStateOf(false) }
    var currentPassword by remember { mutableStateOf("") }
    var newPassword by remember { mutableStateOf("") }
    var confirmPassword by remember { mutableStateOf("") }

    Column(modifier = Modifier.fillMaxSize().verticalScroll(rememberScrollState()).padding(16.dp)) {
        Card(modifier = Modifier.fillMaxWidth(), colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.primaryContainer)) {
            Column(modifier = Modifier.padding(24.dp), horizontalAlignment = Alignment.CenterHorizontally) {
                Surface(modifier = Modifier.size(72.dp), shape = MaterialTheme.shapes.large, color = MaterialTheme.colorScheme.primary) {
                    Box(contentAlignment = Alignment.Center) {
                        Text(text = name.take(1).ifEmpty { "?" }, style = MaterialTheme.typography.headlineLarge, color = MaterialTheme.colorScheme.onPrimary)
                    }
                }
                Spacer(modifier = Modifier.height(12.dp))
                Text(text = name.ifEmpty { "بدون نام" }, style = MaterialTheme.typography.titleMedium)
                Spacer(modifier = Modifier.height(4.dp))
                Text(text = email, style = MaterialTheme.typography.bodyMedium, color = MaterialTheme.colorScheme.onSurfaceVariant)
                if (userRole == "superadmin") {
                    Spacer(modifier = Modifier.height(4.dp))
                    Badge { Text("مدیر کل") }
                }
            }
        }
        Spacer(modifier = Modifier.height(24.dp))
        Text("ویرایش پروفایل", style = MaterialTheme.typography.titleMedium)
        Spacer(modifier = Modifier.height(12.dp))
        OutlinedTextField(value = name, onValueChange = { name = it }, label = { Text("نام و نام خانوادگی") }, modifier = Modifier.fillMaxWidth(), singleLine = true)
        Spacer(modifier = Modifier.height(8.dp))
        OutlinedTextField(value = email, onValueChange = { email = it }, label = { Text("ایمیل") }, modifier = Modifier.fillMaxWidth(), singleLine = true)
        Spacer(modifier = Modifier.height(16.dp))
        Button(onClick = { onUpdateProfile(name, email) }, modifier = Modifier.fillMaxWidth()) { Text("ذخیره تغییرات") }

        HorizontalDivider(modifier = Modifier.padding(vertical = 24.dp))
        Text("تغییر کلمه عبور", style = MaterialTheme.typography.titleMedium)
        Spacer(modifier = Modifier.height(12.dp))
        if (showChangePassword) {
            OutlinedTextField(value = currentPassword, onValueChange = { currentPassword = it }, label = { Text("کلمه عبور فعلی") }, modifier = Modifier.fillMaxWidth(), singleLine = true, visualTransformation = PasswordVisualTransformation())
            Spacer(modifier = Modifier.height(8.dp))
            OutlinedTextField(value = newPassword, onValueChange = { newPassword = it }, label = { Text("کلمه عبور جدید") }, modifier = Modifier.fillMaxWidth(), singleLine = true, visualTransformation = PasswordVisualTransformation())
            Spacer(modifier = Modifier.height(8.dp))
            OutlinedTextField(value = confirmPassword, onValueChange = { confirmPassword = it }, label = { Text("تکرار کلمه عبور جدید") }, modifier = Modifier.fillMaxWidth(), singleLine = true, visualTransformation = PasswordVisualTransformation())
            Spacer(modifier = Modifier.height(12.dp))
            Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                OutlinedButton(onClick = { showChangePassword = false }, modifier = Modifier.weight(1f)) { Text("انصراف") }
                Button(onClick = { onChangePassword(currentPassword, newPassword, confirmPassword); showChangePassword = false }, modifier = Modifier.weight(1f)) { Text("تغییر رمز") }
            }
        } else {
            OutlinedButton(onClick = { showChangePassword = true }, modifier = Modifier.fillMaxWidth()) { Text("تغییر کلمه عبور") }
        }

        HorizontalDivider(modifier = Modifier.padding(vertical = 24.dp))
        OutlinedButton(onClick = { showLogoutDialog = true }, modifier = Modifier.fillMaxWidth(), colors = ButtonDefaults.outlinedButtonColors(contentColor = MaterialTheme.colorScheme.error)) { Text("خروج از حساب") }
        Spacer(modifier = Modifier.height(32.dp))
    }
    if (showLogoutDialog) {
        AlertDialog(onDismissRequest = { showLogoutDialog = false }, title = { Text("خروج از حساب") }, text = { Text("آیا مطمئن هستید؟") }, confirmButton = { TextButton(onClick = { showLogoutDialog = false; onLogout() }) { Text("بله") } }, dismissButton = { TextButton(onClick = { showLogoutDialog = false }) { Text("انصراف") } })
    }
}
