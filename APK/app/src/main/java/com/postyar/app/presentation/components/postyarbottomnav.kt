package com.postyar.app.presentation.components

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.selection.*
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.unit.dp

enum class BottomNavItem(val label: String, val icon: ImageVector, val route: String) {
    DASHBOARD("داشبورد", Icons.Default.Dashboard, "dashboard"),
    POSTS("پست‌ها", Icons.Default.Article, "posts"),
    CHANNELS("کانال‌ها", Icons.Default.Sensors, "channels"),
    SETTINGS("تنظیمات", Icons.Default.Settings, "settings")
}

@Composable
fun PostyarBottomNav(
    currentRoute: String?,
    onNavigate: (String) -> Unit,
    modifier: Modifier = Modifier
) {
    val items = BottomNavItem.entries
    val selectedIndex = items.indexOfFirst { it.route == currentRoute }.coerceAtLeast(0)

    NavigationBar(modifier = modifier) {
        items.forEachIndexed { index, item ->
            NavigationBarItem(
                icon = { Icon(item.icon, contentDescription = item.label) },
                label = { Text(item.label) },
                selected = index == selectedIndex,
                onClick = { onNavigate(item.route) }
            )
        }
    }
}