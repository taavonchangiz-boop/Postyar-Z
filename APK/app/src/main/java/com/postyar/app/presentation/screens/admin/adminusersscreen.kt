package com.postyar.app.presentation.screens.admin

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Search
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp

data class AdminUser(
    val id: Int,
    val name: String,
    val email: String,
    val status: String,
    val createdAt: String
)

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun AdminUsersScreen(
    users: List<AdminUser> = emptyList(),
    onSuspend: (Int) -> Unit = {},
    onActivate: (Int) -> Unit = {}
) {
    var searchQuery by remember { mutableStateOf("") }
    var selectedTab by remember { mutableIntStateOf(0) }
    val tabs = listOf("همه", "فعال", "معلق")

    val filteredUsers = remember(searchQuery, selectedTab) {
        users.filter { user ->
            val matchesSearch = searchQuery.isBlank() || user.name.contains(searchQuery, ignoreCase = true) || user.email.contains(searchQuery, ignoreCase = true)
            val matchesTab = when (selectedTab) {
                1 -> user.status == "active"
                2 -> user.status == "suspended"
                else -> true
            }
            matchesSearch && matchesTab
        }
    }

    Column(modifier = Modifier.fillMaxSize()) {
        TopAppBar(title = { Text("مدیریت کاربران") })

        SearchBar(
            query = searchQuery,
            onQueryChange = { searchQuery = it },
            onSearch = {},
            active = false,
            onActiveChange = {},
            modifier = Modifier.fillMaxWidth().padding(horizontal = 16.dp),
            placeholder = { Text("جستجوی نام یا ایمیل...") },
            leadingIcon = { Icon(Icons.Default.Search, contentDescription = null) }
        ) {}

        TabRow(selectedTabIndex = selectedTab) {
            tabs.forEachIndexed { index, title ->
                Tab(selected = selectedTab == index, onClick = { selectedTab = index }, text = { Text(title) })
            }
        }

        LazyColumn(modifier = Modifier.fillMaxSize().padding(16.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
            items(filteredUsers, key = { it.id }) { user ->
                Card(modifier = Modifier.fillMaxWidth()) {
                    Row(modifier = Modifier.fillMaxWidth().padding(12.dp), verticalAlignment = Alignment.CenterVertically) {
                        Column(modifier = Modifier.weight(1f)) {
                            Text(text = user.name, style = MaterialTheme.typography.bodyLarge)
                            Text(text = user.email, style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
                            Text(text = user.createdAt, style = MaterialTheme.typography.labelSmall, color = MaterialTheme.colorScheme.outline)
                        }
                        Spacer(modifier = Modifier.width(8.dp))
                        Badge(
                            containerColor = when (user.status) {
                                "active" -> androidx.compose.ui.graphics.Color(0xFF4CAF50)
                                "suspended" -> MaterialTheme.colorScheme.error
                                else -> MaterialTheme.colorScheme.surfaceVariant
                            }
                        ) { Text(if (user.status == "active") "فعال" else "معلق") }
                        Spacer(modifier = Modifier.width(8.dp))
                        if (user.status == "active") {
                            IconButton(onClick = { onSuspend(user.id) }) { Icon(androidx.compose.material.icons.Icons.Default.Block, contentDescription = "معلق", tint = MaterialTheme.colorScheme.error) }
                        } else {
                            IconButton(onClick = { onActivate(user.id) }) { Icon(androidx.compose.material.icons.Icons.Default.CheckCircle, contentDescription = "فعال", tint = androidx.compose.ui.graphics.Color(0xFF4CAF50)) }
                        }
                    }
                }
            }
            if (filteredUsers.isEmpty()) {
                item {
                    Box(modifier = Modifier.fillMaxWidth().padding(32.dp), contentAlignment = Alignment.Center) {
                        Text("کاربری یافت نشد", style = MaterialTheme.typography.bodyLarge, color = MaterialTheme.colorScheme.onSurfaceVariant)
                    }
                }
            }
        }
    }
}
