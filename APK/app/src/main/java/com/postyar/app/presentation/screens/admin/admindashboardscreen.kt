package com.postyar.app.presentation.screens.admin

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.items
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp

data class AdminStat(
    val label: String,
    val value: String,
    val color: androidx.compose.ui.graphics.Color
)

data class AdminUserBrief(
    val id: Int,
    val name: String,
    val email: String,
    val status: String
)

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun AdminDashboardScreen(
    totalUsers: Int = 0,
    activeUsers: Int = 0,
    suspendedUsers: Int = 0,
    totalPayments: String = "0",
    pendingPayments: Int = 0,
    openTickets: Int = 0,
    recentUsers: List<AdminUserBrief> = emptyList(),
    onNavigateUsers: () -> Unit = {}
) {
    val stats = listOf(
        AdminStat("کل کاربران", totalUsers.toString(), MaterialTheme.colorScheme.primary),
        AdminStat("فعال", activeUsers.toString(), androidx.compose.ui.graphics.Color(0xFF4CAF50)),
        AdminStat("معلق", suspendedUsers.toString(), androidx.compose.ui.graphics.Color(0xFFFF9800)),
        AdminStat("پرداخت‌ها", totalPayments, MaterialTheme.colorScheme.tertiary),
        AdminStat("پرداخت معلق", pendingPayments.toString(), androidx.compose.ui.graphics.Color(0xFFFFC107)),
        AdminStat("تیکت باز", openTickets.toString(), MaterialTheme.colorScheme.secondary)
    )

    Column(modifier = Modifier.fillMaxSize().padding(16.dp)) {
        Text("پنل مدیریت", style = MaterialTheme.typography.headlineMedium, fontWeight = FontWeight.Bold)
        Spacer(modifier = Modifier.height(16.dp))

        LazyVerticalGrid(columns = GridCells.Fixed(2), horizontalArrangement = Arrangement.spacedBy(12.dp), verticalArrangement = Arrangement.spacedBy(12.dp)) {
            items(stats) { stat ->
                Card(modifier = Modifier.fillMaxWidth(), colors = CardDefaults.cardColors(containerColor = stat.color.copy(alpha = 0.1f))) {
                    Column(modifier = Modifier.padding(16.dp)) {
                        Text(text = stat.value, style = MaterialTheme.typography.headlineSmall, color = stat.color, fontWeight = FontWeight.Bold)
                        Spacer(modifier = Modifier.height(4.dp))
                        Text(text = stat.label, style = MaterialTheme.typography.bodyMedium, color = MaterialTheme.colorScheme.onSurfaceVariant)
                    }
                }
            }
        }

        Spacer(modifier = Modifier.height(24.dp))
        Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween) {
            Text("کاربران اخیر", style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.Bold)
            TextButton(onClick = onNavigateUsers) { Text("مشاهده همه") }
        }
        Spacer(modifier = Modifier.height(12.dp))

        if (recentUsers.isEmpty()) {
            Text("اطلاعاتی موجود نیست", style = MaterialTheme.typography.bodyMedium, color = MaterialTheme.colorScheme.onSurfaceVariant)
        } else {
            recentUsers.forEach { user ->
                Card(modifier = Modifier.fillMaxWidth().padding(vertical = 4.dp)) {
                    Row(modifier = Modifier.fillMaxWidth().padding(12.dp), horizontalArrangement = Arrangement.SpaceBetween) {
                        Column {
                            Text(text = user.name, style = MaterialTheme.typography.bodyMedium, fontWeight = FontWeight.Medium)
                            Text(text = user.email, style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
                        }
                        Badge(
                            containerColor = when (user.status) {
                                "active" -> androidx.compose.ui.graphics.Color(0xFF4CAF50)
                                "suspended" -> MaterialTheme.colorScheme.error
                                else -> MaterialTheme.colorScheme.surfaceVariant
                            }
                        ) { Text(if (user.status == "active") "فعال" else "معلق") }
                    }
                }
            }
        }
    }
}
