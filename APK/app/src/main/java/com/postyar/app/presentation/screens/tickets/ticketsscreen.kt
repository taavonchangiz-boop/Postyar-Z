package com.postyar.app.presentation.screens.tickets

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import com.postyar.app.presentation.components.*
import com.postyar.app.presentation.viewmodels.TicketViewModel
import com.postyar.app.data.remote.dto.TicketDto

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun TicketsScreen(
    onNavigate: (String) -> Unit,
    ticketViewModel: TicketViewModel = hiltViewModel()
) {
    val tickets by ticketViewModel.tickets.collectAsStateWithLifecycle()
    val isLoading by ticketViewModel.isLoading.collectAsStateWithLifecycle(initialValue = false)

    LaunchedEffect(Unit) { ticketViewModel.loadTickets() }

    Scaffold(
        topBar = { PostyarTopBar(title = "تیکت‌های پشتیبانی") },
        floatingActionButton = {
            FloatingActionButton(onClick = { onNavigate("tickets/create") }) {
                Icon(Icons.Default.Add, contentDescription = "تیکت جدید")
            }
        }
    ) { paddingValues ->
        when {
            isLoading -> Box(modifier = Modifier.fillMaxSize().padding(paddingValues), contentAlignment = Alignment.Center) { LoadingView() }
            tickets.isEmpty() -> Box(modifier = Modifier.fillMaxSize().padding(paddingValues), contentAlignment = Alignment.Center) { EmptyStateView(message = "تیکتی وجود ندارد") }
            else -> LazyColumn(
                modifier = Modifier.fillMaxSize().padding(paddingValues),
                contentPadding = PaddingValues(16.dp),
                verticalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                items(tickets, key = { it.id }) { ticket ->
                    TicketCard(ticket = ticket, onClick = { onNavigate("tickets/detail/${ticket.id}") })
                }
            }
        }
    }
}

@Composable
private fun TicketCard(ticket: TicketDto, onClick: () -> Unit) {
    val statusColor = when (ticket.status) {
        "open" -> Color(0xFF1976D2)
        "replied" -> Color(0xFFFF9800)
        else -> Color(0xFF9E9E9E)
    }
    val statusText = when (ticket.status) {
        "open" -> "باز"
        "replied" -> "پاسخ داده شده"
        "closed" -> "بسته شده"
        else -> ticket.status ?: ""
    }

    Card(modifier = Modifier.fillMaxWidth(), onClick = onClick) {
        Row(
            modifier = Modifier.padding(16.dp).fillMaxWidth(),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Column(modifier = Modifier.weight(1f)) {
                Text(text = ticket.subject ?: "", style = MaterialTheme.typography.bodyLarge, fontWeight = FontWeight.Medium)
                Spacer(modifier = Modifier.height(4.dp))
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    ticket.category?.let { Badge { Text(it) } }
                    Text(text = ticket.createdAt ?: "", style = MaterialTheme.typography.labelSmall, color = MaterialTheme.colorScheme.outline)
                }
            }
            Badge(containerColor = statusColor) { Text(statusText, color = Color.White) }
        }
    }
}