package com.postyar.app.presentation.screens.billing

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import com.postyar.app.presentation.components.PostyarTopBar
import com.postyar.app.presentation.viewmodels.BillingViewModel
import com.postyar.app.data.remote.dto.PlanDto

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun PlansScreen(
    onNavigate: (String) -> Unit,
    billingViewModel: BillingViewModel = hiltViewModel()
) {
    val plans by billingViewModel.plans.collectAsStateWithLifecycle()
    val isLoading by billingViewModel.isLoading.collectAsStateWithLifecycle(initialValue = false)

    LaunchedEffect(Unit) { billingViewModel.loadPlans() }

    Scaffold(topBar = { PostyarTopBar(title = "پلن‌های اشتراک") }) { paddingValues ->
        when {
            isLoading -> Box(modifier = Modifier.fillMaxSize().padding(paddingValues), contentAlignment = Alignment.Center) { com.postyar.app.presentation.components.LoadingView() }
            plans.isEmpty() -> Box(modifier = Modifier.fillMaxSize().padding(paddingValues), contentAlignment = Alignment.Center) { com.postyar.app.presentation.components.EmptyStateView(message = "پلنی یافت نشد") }
            else -> LazyColumn(
                modifier = Modifier.fillMaxSize().padding(paddingValues),
                contentPadding = PaddingValues(16.dp),
                verticalArrangement = Arrangement.spacedBy(16.dp)
            ) {
                items(plans, key = { it.id }) { plan ->
                    PlanCard(plan = plan, onClick = { onNavigate("payments/create/${plan.id}") })
                }
            }
        }
    }
}

@Composable
private fun PlanCard(plan: PlanDto, onClick: () -> Unit) {
    val isFree = (plan.price ?: 0.0) == 0.0
    Card(
        modifier = Modifier.fillMaxWidth(),
        onClick = onClick,
        colors = if ((plan.isFeatured ?: 0) == 1) CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.primaryContainer) else CardDefaults.cardColors()
    ) {
        Column(modifier = Modifier.padding(20.dp)) {
            Row(horizontalArrangement = Arrangement.SpaceBetween, verticalAlignment = Alignment.CenterVertically) {
                Text(text = plan.title ?: "", style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.Bold)
                if ((plan.isFeatured ?: 0) == 1) {
                    Badge(containerColor = MaterialTheme.colorScheme.primary) { Text("پیشنهاد ویژه", color = Color.White) }
                }
            }

            Spacer(modifier = Modifier.height(12.dp))
            Row(verticalAlignment = Alignment.Bottom) {
                PersianNumberText(text = "${plan.price?.let { String.format("%,.0f", it) } ?: "0"} تومان", style = MaterialTheme.typography.headlineSmall, fontWeight = FontWeight.Bold, color = MaterialTheme.colorScheme.primary)
                Spacer(modifier = Modifier.width(8.dp))
                Text(text = "/ ${plan.durationDays} روز", style = MaterialTheme.typography.bodyMedium, color = MaterialTheme.colorScheme.onSurfaceVariant)
            }

            Spacer(modifier = Modifier.height(12.dp))

            Column(verticalArrangement = Arrangement.spacedBy(4.dp)) {
                (plan.features ?: emptyList()).forEach { feature ->
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        Icon(Icons.Default.Check, contentDescription = null, modifier = Modifier.size(16.dp), tint = MaterialTheme.colorScheme.primary)
                        Spacer(modifier = Modifier.width(6.dp))
                        Text(text = feature, style = MaterialTheme.typography.bodySmall)
                    }
                }
            }

            Spacer(modifier = Modifier.height(12.dp))
            OutlinedButton(onClick = onClick, modifier = Modifier.fillMaxWidth()) {
                Text(if (isFree) "شروع رایگان" else "انتخاب و پرداخت")
            }
        }
    }
}
