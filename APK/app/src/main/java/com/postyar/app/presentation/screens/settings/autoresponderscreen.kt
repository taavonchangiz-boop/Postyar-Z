package com.postyar.app.presentation.screens.settings

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.Delete
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import com.postyar.app.presentation.components.*
import com.postyar.app.presentation.viewmodels.AutoResponderViewModel
import com.postyar.app.data.remote.dto.AutoReplyDto

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun AutoResponderScreen(
    autoResponderViewModel: AutoResponderViewModel = hiltViewModel()
) {
    val autoReplies by autoResponderViewModel.autoReplies.collectAsStateWithLifecycle()
    val isLoading by autoResponderViewModel.isLoading.collectAsStateWithLifecycle(initialValue = false)

    LaunchedEffect(Unit) { autoResponderViewModel.loadAutoReplies() }

    Scaffold(
        topBar = { PostyarTopBar(title = "پاسخگوی خودکار") }
    ) { paddingValues ->
        if (isLoading) {
            Box(modifier = Modifier.fillMaxSize().padding(paddingValues), contentAlignment = Alignment.Center) { LoadingView() }
        } else if (autoReplies.isEmpty()) {
            Box(modifier = Modifier.fillMaxSize().padding(paddingValues), contentAlignment = Alignment.Center) { EmptyStateView(message = "قانونی تنظیم نشده") }
        } else {
            LazyColumn(
                modifier = Modifier.fillMaxSize().padding(paddingValues),
                contentPadding = PaddingValues(16.dp),
                verticalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                items(autoReplies, key = { it.id }) { rule ->
                    AutoReplyRuleCard(rule = rule, onToggle = { autoResponderViewModel.toggleChannel(rule.channelId, if (rule.active == 1) 0 else 1) }, onDelete = { autoResponderViewModel.deleteRule(rule.id) })
                }
            }
        }
    }
}

@Composable
private fun AutoReplyRuleCard(rule: AutoReplyDto, onToggle: () -> Unit, onDelete: () -> Unit) {
    Card(modifier = Modifier.fillMaxWidth()) {
        Row(
            modifier = Modifier.padding(16.dp).fillMaxWidth(),
            verticalAlignment = Alignment.CenterVertically,
            horizontalArrangement = Arrangement.SpaceBetween
        ) {
 Column(modifier = Modifier.weight(1f)) {
 Text(text = rule.channelName ?: "", style = MaterialTheme.typography.bodyMedium, fontWeight = androidx.compose.ui.text.font.FontWeight.Medium)
 Spacer(modifier = Modifier.height(2.dp))
 Text(text = "${rule.keyword} → ${rule.replyText}", style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant, maxLines = 1)
 }
 Row {
 Switch(checked = rule.active == 1, onCheckedChange = { onToggle() })
 IconButton(onClick = onDelete) { Icon(Icons.Default.Delete, contentDescription = "حذف", tint = MaterialTheme.colorScheme.error) }
 }
 }
 }
}