package com.postyar.app.presentation.screens.posts

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import com.postyar.app.presentation.components.*
import com.postyar.app.presentation.viewmodels.PostViewModel
import com.postyar.app.data.remote.dto.PostDto

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun PostsScreen(
    onNavigate: (String) -> Unit,
    postViewModel: PostViewModel = hiltViewModel()
) {
    val posts by postViewModel.posts.collectAsStateWithLifecycle()
    val isLoading by postViewModel.isLoading.collectAsStateWithLifecycle(initialValue = false)
    val selectedTab by postViewModel.statusFilter.collectAsStateWithLifecycle()

    val tabs = listOf("همه" to null, "پیش‌نویس" to "draft", "زمان‌بندی" to "scheduled", "ارسال‌شده" to "sent", "ناموفق" to "failed")
    val selectedTabIndex = tabs.indexOfFirst { it.second == selectedTab }.coerceAtLeast(0)

    LaunchedEffect(Unit) { postViewModel.loadPosts() }

    Scaffold(
        topBar = { PostyarTopBar(title = "پست‌ها") },
        floatingActionButton = {
            FloatingActionButton(onClick = { onNavigate("posts/create") }) {
                Icon(Icons.Default.Add, contentDescription = "ایجاد پست")
            }
        }
    ) { paddingValues ->
        Column(modifier = Modifier.fillMaxSize().padding(paddingValues)) {
            ScrollableTabRow(
                selectedTabIndex = selectedTabIndex,
                modifier = Modifier.fillMaxWidth()
            ) {
                tabs.forEachIndexed { index, (label, status) ->
                    Tab(
                        selected = selectedTabIndex == index,
                        onClick = { postViewModel.setStatusFilter(status) },
                        text = { Text(text = label) }
                    )
                }
            }

            val filteredPosts = if (selectedTab == null) posts else posts.filter { it.status == selectedTab }

            if (isLoading) {
                Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) { LoadingView() }
            } else if (filteredPosts.isEmpty()) {
                EmptyStateView(message = "پستی یافت نشد")
            } else {
                LazyColumn(
                    modifier = Modifier.fillMaxSize(),
                    contentPadding = PaddingValues(16.dp),
                    verticalArrangement = Arrangement.spacedBy(12.dp)
                ) {
                    items(filteredPosts, key = { it.id }) { post ->
                        PostCard(post = post, onClick = { onNavigate("posts/detail/${post.id}") })
                    }
                }
            }
        }
    }
}

@Composable
private fun PostCard(post: PostDto, onClick: () -> Unit) {
    Card(
        modifier = Modifier.fillMaxWidth(),
        onClick = onClick
    ) {
        Row(
            modifier = Modifier.padding(16.dp).fillMaxWidth(),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Column(modifier = Modifier.weight(1f)) {
                Text(text = post.title ?: "بدون عنوان", style = MaterialTheme.typography.bodyLarge, fontWeight = FontWeight.Medium)
                Spacer(modifier = Modifier.height(4.dp))
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    Text(text = post.createdAt ?: "", style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
                    if (post.clickCount != null && post.clickCount > 0) {
                        PersianNumberText(text = "${post.clickCount} کلیک", style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.primary)
                    }
                }
            }
            Spacer(modifier = Modifier.width(8.dp))
            StatusBadge(status = post.status ?: "draft")
        }
    }
}
