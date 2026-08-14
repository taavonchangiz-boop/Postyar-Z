package com.postyar.app.presentation.screens.admin

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import com.postyar.app.presentation.components.*
import com.postyar.app.presentation.viewmodels.AdminViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun AdminTicketsScreen(
    onNavigateDetail: (Int) -> Unit = {},
    adminViewModel: AdminViewModel = hiltViewModel()
) {
    val tickets by adminViewModel.allTickets.collectAsStateWithLifecycle()
    val isLoading by adminViewModel.isLoading.collectAsStateWithLifecycle(initialValue = false)

    LaunchedEffect(Unit) { adminViewModel.loadTickets() }

    Scaffold(topBar = { PostyarTopBar(title = "تیکت‌ها (مدیر)") }) { paddingValues ->
        when {
            isLoading -> Box(modifier = Modifier.fillMaxSize().padding(paddingValues), contentAlignment = Alignment.Center) { LoadingView() }
            tickets.isEmpty() -> Box(modifier = Modifier.fillMaxSize().padding(paddingValues), contentAlignment = Alignment.Center) { EmptyStateView(message = "تیکتی یافت نشد") }
            else -> LazyColumn(modifier = Modifier.fillMaxSize().padding(paddingValues), contentPadding = PaddingValues(16.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
                items(tickets, key = { it.id }) { ticket ->
                    Card(modifier = Modifier.fillMaxWidth(), onClick = { onNavigateDetail(ticket.id) }) {
                        Row(modifier = Modifier.padding(12.dp).fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween) {
                            Column(modifier = Modifier.weight(1f)) {
                                Text(ticket.subject ?: "", style = MaterialTheme.typography.bodyMedium)
                                ticket.category?.let { Text(it, style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant) }
                            }
                            StatusBadge(status = ticket.status ?: "open")
                        }
                    }
                }
            }
        }
    }
}