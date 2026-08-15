package com.postyar.app.presentation.screens.main

import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.AddCircle
import androidx.compose.material.icons.filled.ContentCopy
import androidx.compose.material.icons.filled.Notifications
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import com.postyar.app.presentation.components.*
import com.postyar.app.presentation.viewmodels.BootstrapViewModel
import com.postyar.app.presentation.viewmodels.SyncViewModel
import com.postyar.app.domain.*

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun DashboardScreen(
    onNavigate: (String) -> Unit,
    bootstrapViewModel: BootstrapViewModel = androidx.hilt.navigation.compose.hiltViewModel(),
    syncViewModel: SyncViewModel = androidx.hilt.navigation.compose.hiltViewModel()
) {
    val quota by bootstrapViewModel.quota.collectAsStateWithLifecycle()
    val channels by bootstrapViewModel.channels.collectAsStateWithLifecycle()
    val posts by bootstrapViewModel.posts.collectAsStateWithLifecycle()
    val unreadCount by bootstrapViewModel.unreadCount.collectAsStateWithLifecycle(initialValue = 0)
    val isLoading by bootstrapViewModel.isLoading.collectAsStateWithLifecycle(initialValue = true)
    val error by bootstrapViewModel.error.collectAsStateWithLifecycle()
    val user by bootstrapViewModel.currentUser.collectAsStateWithLifecycle()

    LaunchedEffect(Unit) {
        bootstrapViewModel.loadBootstrap()
    }

    Scaffold(
        topBar = {
            PostyarTopBar(
                title = "پُست‌یار",
                onBackClick = null,
                unreadCount = unreadCount,
                actions = {
                    BadgedBox(
                        badge = {
                            if (unreadCount > 0) {
                                Badge { Text(unreadCount.toString()) }
                            }
                        }
                    ) {
                        IconButton(onClick = { onNavigate("notifications") }) {
                            Icon(Icons.Default.Notifications, contentDescription = "اعلان‌ها")
                        }
                    }
                }
            )
        },
        bottomBar = {
            PostyarBottomNav(
                currentRoute = "dashboard",
                onNavigate = onNavigate
            )
        }
    ) { paddingValues ->
        when {
            isLoading -> {
                Box(modifier = Modifier.fillMaxSize().padding(paddingValues), contentAlignment = Alignment.Center) {
                    LoadingView()
                }
            }
            error != null -> {
                Box(modifier = Modifier.fillMaxSize().padding(paddingValues), contentAlignment = Alignment.Center) {
                    ErrorView(message = error ?: "خطایی رخ داد", onRetry = { bootstrapViewModel.loadBootstrap() })
                }
            }
            else -> {
                LazyColumn(
                    modifier = Modifier.fillMaxSize().padding(paddingValues),
                    contentPadding = PaddingValues(16.dp),
                    verticalArrangement = Arrangement.spacedBy(16.dp)
                ) {
                    item { GreetingSection(user) }
                    item { QuotaSection(quota, onNavigate) }
                    item {
                        Text(
                            text = "پست‌های اخیر",
                            style = MaterialTheme.typography.titleMedium,
                            fontWeight = FontWeight.Bold
                        )
                    }
                    if (posts.isEmpty()) {
                        item { EmptyStateView(message = "هنوز پستی ایجاد نشده است") }
                    } else {
                        items(posts) { post -> RecentPostCard(post, onClick = { onNavigate("posts/detail/${post.id}") }) }
                    }
                    item { Spacer(modifier = Modifier.height(16.dp)) }
                }
            }
        }
    }
}

@Composable
private fun GreetingSection(user: User?) {
    Column {
        Text(
            text = "سلام${user?.name?.let { " $it" } ?: ""} 👋",
            style = MaterialTheme.typography.headlineSmall,
            fontWeight = FontWeight.Bold
        )
        user?.business_name?.let {
            Text(text = it, style = MaterialTheme.typography.bodyMedium, color = MaterialTheme.colorScheme.onSurfaceVariant)
        }
    }
}

@Composable
private fun QuotaSection(quota: Quota?, onNavigate: (String) -> Unit) {
    if (quota == null) return
    Row(
        modifier = Modifier.fillMaxWidth(),
        horizontalArrangement = Arrangement.spacedBy(12.dp)
    ) {
        QuotaCard(
            modifier = Modifier.weight(1f),
            title = "پست‌ها",
            used = quota.posts_used,
            limit = quota.posts_limit,
            icon = { Icon(Icons.Default.ContentCopy, contentDescription = null) }
        )
        QuotaCard(
            modifier = Modifier.weight(1f),
            title = "کانال‌ها",
            used = quota.channels_used,
            limit = quota.channels_limit,
            icon = { Icon(Icons.Default.Notifications, contentDescription = null) }
        )
    }
    Spacer(modifier = Modifier.height(8.dp))
    Row(
        modifier = Modifier.fillMaxWidth(),
        horizontalArrangement = Arrangement.spacedBy(8.dp)
    ) {
        QuickActionButton(
            modifier = Modifier.weight(1f),
            text = "ایجاد پست جدید",
            onClick = { onNavigate("posts/create") }
        )
        QuickActionButton(
            modifier = Modifier.weight(1f),
            text = "افزودن کانال",
            onClick = { onNavigate("channels/add") }
        )
        QuickActionButton(
            modifier = Modifier.weight(1f),
            text = "مشاهده پلن‌ها",
            onClick = { onNavigate("plans") }
        )
    }
}

@Composable
private fun QuickActionButton(modifier: Modifier = Modifier, text: String, onClick: () -> Unit) {
    Card(
        modifier = modifier.clickable(onClick = onClick),
        colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.primaryContainer)
    ) {
        Row(
            modifier = Modifier.padding(12.dp).fillMaxWidth(),
            horizontalArrangement = Arrangement.Center,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Icon(Icons.Default.AddCircle, contentDescription = null, modifier = Modifier.size(18.dp), tint = MaterialTheme.colorScheme.primary)
            Spacer(modifier = Modifier.width(6.dp))
            Text(text = text, style = MaterialTheme.typography.labelMedium, color = MaterialTheme.colorScheme.primary, fontWeight = FontWeight.Medium)
        }
    }
}

@Composable
private fun RecentPostCard(post: Post, onClick: () -> Unit) {
    Card(modifier = Modifier.fillMaxWidth().clickable(onClick = onClick)) {
        Row(
            modifier = Modifier.padding(16.dp).fillMaxWidth(),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Column(modifier = Modifier.weight(1f)) {
                Text(text = post.title, style = MaterialTheme.typography.bodyLarge, fontWeight = FontWeight.Medium)
                Spacer(modifier = Modifier.height(4.dp))
                Text(text = post.created_at ?: "", style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
            }
            StatusBadge(status = post.status)
        }
    }
}
