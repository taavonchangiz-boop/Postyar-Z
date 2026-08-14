package com.postyar.app.presentation.screens.billing

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
import com.postyar.app.presentation.viewmodels.BillingViewModel
import com.postyar.app.data.remote.dto.PaymentDto

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun PaymentsScreen(
    billingViewModel: BillingViewModel = hiltViewModel()
) {
    val payments by billingViewModel.payments.collectAsStateWithLifecycle()
    val isLoading by billingViewModel.isLoading.collectAsStateWithLifecycle(initialValue = false)

    LaunchedEffect(Unit) { billingViewModel.loadPayments() }

    Scaffold(topBar = { PostyarTopBar(title = "تاریخچه پرداخت‌ها") }) { paddingValues ->
        when {
            isLoading -> Box(modifier = Modifier.fillMaxSize().padding(paddingValues), contentAlignment = Alignment.Center) { LoadingView() }
            payments.isEmpty() -> Box(modifier = Modifier.fillMaxSize().padding(paddingValues), contentAlignment = Alignment.Center) { EmptyStateView(message = "پرداختی ثبت نشده") }
            else -> LazyColumn(
                modifier = Modifier.fillMaxSize().padding(paddingValues),
                contentPadding = PaddingValues(16.dp),
                verticalArrangement = Arrangement.spacedBy(8.dp)
            ) {
                items(payments, key = { it.id }) { payment ->
                    PaymentCard(payment = payment)
                }
            }
        }
    }
}

@Composable
private fun PaymentCard(payment: PaymentDto) {
    val statusColor = when (payment.status) {
        "approved" -> Color(0xFF4CAF50)
        "rejected" -> Color(0xFFE53935)
        else -> Color(0xFFFFB300)
    }
    val statusText = when (payment.status) {
        "approved" -> "تأیید شده"
        "rejected" -> "رد شده"
        else -> "در انتظار بررسی"
    }

    Card(modifier = Modifier.fillMaxWidth()) {
        Row(
            modifier = Modifier.padding(16.dp).fillMaxWidth(),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Column(modifier = Modifier.weight(1f)) {
                Text(text = payment.planTitle ?: "", style = MaterialTheme.typography.bodyMedium, fontWeight = FontWeight.Medium)
                Spacer(modifier = Modifier.height(4.dp))
                PersianNumberText(text = "${payment.amount?.let { String.format("%,.0f", it) } ?: "0"} تومان", style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.primary)
                Spacer(modifier = Modifier.height(2.dp))
                Text(text = payment.createdAt ?: "", style = MaterialTheme.typography.labelSmall, color = MaterialTheme.colorScheme.outline)
            }
            Badge(containerColor = statusColor) { Text(statusText, color = Color.White) }
        }
    }
}