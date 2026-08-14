package com.postyar.app.presentation.screens.channels

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.Check
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
import com.postyar.app.presentation.viewmodels.ChannelViewModel
import com.postyar.app.data.remote.dto.ChannelDto

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ChannelsScreen(
    onNavigate: (String) -> Unit,
    channelViewModel: ChannelViewModel = hiltViewModel()
) {
    val channels by channelViewModel.channels.collectAsStateWithLifecycle()
    val isLoading by channelViewModel.isLoading.collectAsStateWithLifecycle(initialValue = false)
    var showDeleteDialog by remember { mutableStateOf<ChannelDto?>(null) }

    LaunchedEffect(Unit) { channelViewModel.loadChannels() }

    Scaffold(
        topBar = { PostyarTopBar(title = "کانال‌ها") },
        floatingActionButton = {
            FloatingActionButton(onClick = { onNavigate("channels/add") }) {
                Icon(Icons.Default.Add, contentDescription = "افزودن کانال")
            }
        }
    ) { paddingValues ->
        when {
            isLoading -> Box(modifier = Modifier.fillMaxSize().padding(paddingValues), contentAlignment = Alignment.Center) { LoadingView() }
            channels.isEmpty() -> Box(modifier = Modifier.fillMaxSize().padding(paddingValues), contentAlignment = Alignment.Center) { EmptyStateView(message = "کانالی متصل نشده است") }
            else -> LazyColumn(
                modifier = Modifier.fillMaxSize().padding(paddingValues),
                contentPadding = PaddingValues(16.dp),
                verticalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                items(channels, key = { it.id }) { channel ->
                    ChannelCard(
                        channel = channel,
                        onDelete = { showDeleteDialog = channel }
                    )
                }
            }
        }
    }

    showDeleteDialog?.let { channel ->
        ConfirmationDialog(
            title = "حذف کانال",
            message = "آیا از حذف «${channel.name}» مطمئن هستید؟",
            onConfirm = { channelViewModel.deleteChannel(channel.id); showDeleteDialog = null },
            onDismiss = { showDeleteDialog = null }
        )
    }
}

@Composable
private fun ChannelCard(channel: ChannelDto, onDelete: () -> Unit) {
    val platformColor = when (channel.platform) {
        "telegram" -> Color(0xFF0088cc)
        "eitaa" -> Color(0xFF7C4DFF)
        "gap" -> Color(0xFF4CAF50)
        else -> MaterialTheme.colorScheme.primary
    }

    Card(
        modifier = Modifier.fillMaxWidth(),
        onClick = onDelete
    ) {
        Row(
            modifier = Modifier.padding(16.dp).fillMaxWidth(),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Column(modifier = Modifier.weight(1f)) {
                Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    Text(text = channel.name ?: "", style = MaterialTheme.typography.bodyLarge, fontWeight = FontWeight.Medium)
                    Badge(containerColor = platformColor) {
                        Text(text = channel.platform ?: "", color = Color.White, style = MaterialTheme.typography.labelSmall)
                    }
                }
                Spacer(modifier = Modifier.height(4.dp))
                Text(text = channel.channelId ?: "", style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
            }
            if (channel.webhookActive == 1) {
                Icon(Icons.Default.Check, contentDescription = "وبهوک فعال", tint = Color(0xFF4CAF50), modifier = Modifier.size(20.dp))
            }
        }
    }
}
