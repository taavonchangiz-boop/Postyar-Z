package com.postyar.app.presentation.components

import androidx.compose.foundation.layout.*
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Notifications
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun PostyarTopBar(
    title: String = "پُست‌یار",
    onBack: (() -> Unit)? = null,
    onBackClick: (() -> Unit)? = null,
    onNotificationClick: () -> Unit = {},
    unreadCount: Int = 0,
    showNotification: Boolean = true,
    actions: @Composable RowScope.() -> Unit = {}
) {
    val backHandler = onBackClick ?: onBack
    TopAppBar(
        title = {
            Row(verticalAlignment = Alignment.CenterVertically) {
                Text(
                    text = title,
                    style = MaterialTheme.typography.titleLarge,
                    fontWeight = FontWeight.Bold
                )
            }
        },
        navigationIcon = {
            if (backHandler != null) {
                IconButton(onClick = backHandler) {
                    Icon(Icons.Default.ArrowBack, contentDescription = "بازگشت")
                }
            }
        },
        actions = {
            actions()
            if (showNotification) {
                BadgedBox(
                    badge = {
                        if (unreadCount > 0) {
                            Badge { Text(unreadCount.toString()) }
                        }
                    }
                ) {
                    IconButton(onClick = onNotificationClick) {
                        Icon(Icons.Default.Notifications, contentDescription = "اعلان‌ها")
                    }
                }
            }
        },
        colors = TopAppBarDefaults.topAppBarColors(
            containerColor = MaterialTheme.colorScheme.primaryContainer
        )
    )
}
