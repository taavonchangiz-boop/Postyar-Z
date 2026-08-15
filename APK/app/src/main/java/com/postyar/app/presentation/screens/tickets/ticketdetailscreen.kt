package com.postyar.app.presentation.screens.tickets

import android.net.Uri
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.AttachFile
import androidx.compose.material.icons.filled.Send
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import com.postyar.app.domain.TicketReply
import com.postyar.app.presentation.components.PostyarTopBar
import com.postyar.app.presentation.components.StatusBadge
import com.postyar.app.presentation.viewmodels.TicketViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun TicketDetailScreen(
    ticketId: Int,
    onBack: () -> Unit,
    ticketViewModel: TicketViewModel = hiltViewModel()
) {
    val ticketDetail by ticketViewModel.ticketDetail.collectAsStateWithLifecycle()
    val isLoading by ticketViewModel.isLoading.collectAsStateWithLifecycle(initialValue = false)
    var replyText by remember { mutableStateOf("") }
    var attachmentUri by remember { mutableStateOf<Uri?>(null) }

    val attachmentLauncher = rememberLauncherForActivityResult(ActivityResultContracts.GetContent()) { uri ->
        uri?.let { attachmentUri = it }
    }

    LaunchedEffect(ticketId) { ticketViewModel.loadTicketDetail(ticketId) }

    Scaffold(topBar = { PostyarTopBar(title = "جزئیات تیکت", onBack = onBack) }) { paddingValues ->
        if (isLoading) {
            Box(modifier = Modifier.fillMaxSize().padding(paddingValues), contentAlignment = Alignment.Center) { CircularProgressIndicator() }
        } else {
            val ticket = ticketDetail?.ticket
            val replies = ticketDetail?.replies ?: emptyList()

            Column(modifier = Modifier.fillMaxSize().padding(paddingValues)) {
                ticket?.let {
                    Card(modifier = Modifier.fillMaxWidth().padding(16.dp)) {
                        Column(modifier = Modifier.padding(16.dp)) {
                            Text(text = it.subject, style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.Bold)
                            Spacer(modifier = Modifier.height(4.dp))
                            Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                                StatusBadge(status = it.status)
                            }
                        }
                    }
                }

                LazyColumn(
                    modifier = Modifier.weight(1f).fillMaxWidth().padding(horizontal = 16.dp),
                    verticalArrangement = Arrangement.spacedBy(8.dp),
                    contentPadding = PaddingValues(vertical = 8.dp)
                ) {
                    items(replies, key = { it.id }) { reply ->
                        ReplyCard(reply = reply)
                    }
                }

                HorizontalDivider()
                Row(
                    modifier = Modifier.padding(12.dp).fillMaxWidth(),
                    verticalAlignment = Alignment.CenterVertically,
                    horizontalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    OutlinedTextField(
                        value = replyText, onValueChange = { replyText = it },
                        placeholder = { Text("پاسخ خود را بنویسید...") },
                        modifier = Modifier.weight(1f), maxLines = 3
                    )
                    IconButton(onClick = { attachmentLauncher.launch("*/*") }) {
                        Icon(Icons.Default.AttachFile, contentDescription = "پیوست")
                    }
                    IconButton(
                        onClick = {
                            ticketViewModel.replyTicket(ticketId, replyText, attachmentUri)
                            replyText = ""
                            attachmentUri = null
                        },
                        enabled = replyText.isNotBlank()
                    ) {
                        Icon(Icons.Default.Send, contentDescription = "ارسال", tint = MaterialTheme.colorScheme.primary)
                    }
                }
            }
        }
    }
}

@Composable
private fun ReplyCard(reply: TicketReply) {
    Card(modifier = Modifier.fillMaxWidth(), colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surfaceVariant)) {
        Column(modifier = Modifier.padding(12.dp)) {
            Text(text = reply.sender_name, style = MaterialTheme.typography.labelMedium, fontWeight = FontWeight.Bold, color = MaterialTheme.colorScheme.primary)
            Spacer(modifier = Modifier.height(4.dp))
            Text(text = reply.message, style = MaterialTheme.typography.bodyMedium)
            Spacer(modifier = Modifier.height(4.dp))
            Text(text = reply.created_at ?: "", style = MaterialTheme.typography.labelSmall, color = MaterialTheme.colorScheme.outline)
        }
    }
}
