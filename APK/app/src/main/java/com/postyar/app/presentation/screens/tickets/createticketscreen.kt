package com.postyar.app.presentation.screens.tickets

import android.net.Uri
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.layout.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import com.postyar.app.presentation.components.PostyarTopBar
import com.postyar.app.presentation.viewmodels.TicketViewModel
import androidx.lifecycle.compose.collectAsStateWithLifecycle

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun CreateTicketScreen(
    onBack: () -> Unit,
    ticketViewModel: TicketViewModel = hiltViewModel()
) {
    var subject by remember { mutableStateOf("") }
    var category by remember { mutableStateOf("") }
    var message by remember { mutableStateOf("") }
    var attachmentUri by remember { mutableStateOf<Uri?>(null) }
    val isSubmitting by ticketViewModel.isSubmitting.collectAsStateWithLifecycle(initialValue = false)

    val attachmentLauncher = rememberLauncherForActivityResult(ActivityResultContracts.GetContent()) { uri ->
        uri?.let { attachmentUri = it }
    }

    Scaffold(topBar = { PostyarTopBar(title = "تیکت جدید", onBack = onBack) }) { paddingValues ->
        Column(
            modifier = Modifier.fillMaxSize().padding(paddingValues).padding(16.dp),
            verticalArrangement = Arrangement.spacedBy(16.dp)
        ) {
            OutlinedTextField(value = subject, onValueChange = { subject = it }, label = { Text("موضوع") }, modifier = Modifier.fillMaxWidth(), singleLine = true)
            OutlinedTextField(value = category, onValueChange = { category = it }, label = { Text("دسته‌بندی") }, modifier = Modifier.fillMaxWidth(), singleLine = true)
            OutlinedTextField(value = message, onValueChange = { message = it }, label = { Text("پیام") }, modifier = Modifier.fillMaxSize().weight(1f), minLines = 5, maxLines = 15)
            OutlinedButton(onClick = { attachmentLauncher.launch("*/*") }) {
                Text(text = if (attachmentUri != null) "فایل پیوست شده ✓" else "پیوست فایل (اختیاری)")
            }

            Button(
                onClick = { ticketViewModel.createTicket(subject, category, message, attachmentUri, onSuccess = onBack) },
                modifier = Modifier.fillMaxWidth(),
                enabled = subject.isNotBlank() && category.isNotBlank() && message.isNotBlank() && !isSubmitting
            ) {
                if (isSubmitting) CircularProgressIndicator(modifier = Modifier.size(20.dp), strokeWidth = 2.dp, color = MaterialTheme.colorScheme.onPrimary)
                else Text("ارسال تیکت")
            }
        }
    }
}
