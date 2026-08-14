package com.postyar.app.presentation.screens.admin

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
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
import com.postyar.app.presentation.viewmodels.AdminViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun AdminPaymentsScreen(
    adminViewModel: AdminViewModel = hiltViewModel()
) {
    val payments by adminViewModel.allPayments.collectAsStateWithLifecycle()
    val isLoading by adminViewModel.isLoading.collectAsStateWithLifecycle(initialValue = false)

    LaunchedEffect(Unit) { adminViewModel.loadPayments() }

    Scaffold(topBar = { PostyarTopBar(title = "پرداخت‌ها (مدیر)") }) { paddingValues ->
        when {
            isLoading -> Box(modifier = Modifier.fillMaxSize().padding(paddingValues), contentAlignment = Alignment.Center) { LoadingView() }
            payments.isEmpty() -> Box(modifier = Modifier.fillMaxSize().padding(paddingValues), contentAlignment = Alignment.Center) { EmptyStateView(message = "پرداختی یافت نشد") }
            else -> LazyColumn(
                modifier = Modifier.fillMaxSize().padding(paddingValues),
                contentPadding = PaddingValues(16.dp),
                verticalArrangement = Arrangement.spacedBy(8.dp)
            ) {
                items(payments, key = { it.id }) { payment ->
                    val statusColor = when (payment.status) {
                        "approved" -> Color(0xFF4CAF50)
                        "rejected" -> Color(0xFFE53935)
                        else -> Color(0xFFFFB300)
                    }
                    Card(modifier = Modifier.fillMaxWidth()) {
                        Row(modifier = Modifier.padding(12.dp).fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween) {
                            Column {
                                Text(payment.planTitle ?: "", style = MaterialTheme.typography.bodyMedium, fontWeight = FontWeight.Medium)
                                PersianNumberText("${payment.amount?.let { String.format("%,.0f", it) } ?: "0"} تومان")
                            }
                            Column(horizontalArrangement = Arrangement.spacedBy(4.dp)) {
                                Badge(containerColor = statusColor) { Text(payment.status ?: "") }
                                if (payment.status == "pending") {
                                    TextButton(onClick = { adminViewModel.approvePayment(payment.id) }) { Text("تأیید") }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}