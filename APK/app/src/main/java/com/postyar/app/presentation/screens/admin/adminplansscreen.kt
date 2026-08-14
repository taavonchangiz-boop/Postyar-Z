package com.postyar.app.presentation.screens.admin

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.Delete
import androidx.compose.material.icons.filled.Edit
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import com.postyar.app.presentation.components.*
import com.postyar.app.presentation.viewmodels.AdminViewModel
import com.postyar.app.data.remote.dto.PlanDto

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun AdminPlansScreen(
    adminViewModel: AdminViewModel = hiltViewModel()
) {
    val plans by adminViewModel.plans.collectAsStateWithLifecycle()
    val isLoading by adminViewModel.isLoading.collectAsStateWithLifecycle(initialValue = false)
    var showDeleteDialog by remember { mutableStateOf<PlanDto?>(null) }

    LaunchedEffect(Unit) { adminViewModel.loadPlans() }

    Scaffold(
        topBar = { PostyarTopBar(title = "مدیریت پلن‌ها") },
        floatingActionButton = { FloatingActionButton(onClick = { /* TODO: create plan dialog */ }) { Icon(Icons.Default.Add, contentDescription = "پلن جدید") } }
    ) { paddingValues ->
        when {
            isLoading -> Box(modifier = Modifier.fillMaxSize().padding(paddingValues), contentAlignment = androidx.compose.ui.Alignment.Center) { LoadingView() }
            plans.isEmpty() -> Box(modifier = Modifier.fillMaxSize().padding(paddingValues), contentAlignment = androidx.compose.ui.Alignment.Center) { EmptyStateView(message = "پلنی یافت نشد") }
            else -> LazyColumn(modifier = Modifier.fillMaxSize().padding(paddingValues), contentPadding = PaddingValues(16.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
                items(plans, key = { it.id }) { plan ->
                    Card(modifier = Modifier.fillMaxWidth()) {
                        Row(modifier = Modifier.padding(12.dp).fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween) {
                            Column(modifier = Modifier.weight(1f)) {
                                Text(plan.title ?: "", style = MaterialTheme.typography.bodyMedium, fontWeight = FontWeight.Medium)
                                PersianNumberText("${String.format("%,.0f", plan.price ?: 0)} تومان - ${plan.durationDays} روز")
                            }
                            Row {
                                IconButton(onClick = { /* edit */ }) { Icon(Icons.Default.Edit, contentDescription = "ویرایش") }
                                IconButton(onClick = { showDeleteDialog = plan }) { Icon(Icons.Default.Delete, contentDescription = "حذف", tint = MaterialTheme.colorScheme.error) }
                            }
                        }
                    }
                }
            }
        }
    }

    showDeleteDialog?.let { plan ->
        ConfirmationDialog(
            title = "حذف پلن",
            message = "آیا از حذف «${plan.title}» مطمئنید؟",
            onConfirm = { adminViewModel.deletePlan(plan.id); showDeleteDialog = null },
            onDismiss = { showDeleteDialog = null }
        )
    }
}
