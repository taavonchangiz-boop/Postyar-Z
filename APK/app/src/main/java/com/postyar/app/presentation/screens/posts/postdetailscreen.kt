package com.postyar.app.presentation.screens.posts

import androidx.compose.foundation.layout.*
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Refresh
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

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun PostDetailScreen(
    postId: Int,
    onBack: () -> Unit,
    postViewModel: PostViewModel = hiltViewModel()
) {
    val post by postViewModel.postDetail.collectAsStateWithLifecycle()
    val isLoading by postViewModel.isLoading.collectAsStateWithLifecycle(initialValue = false)

    LaunchedEffect(postId) { postViewModel.loadPostDetail(postId) }

    Scaffold(topBar = { PostyarTopBar(title = "جزئیات پست", onBack = onBack) }) { paddingValues ->
        when {
            isLoading -> Box(modifier = Modifier.fillMaxSize().padding(paddingValues), contentAlignment = Alignment.Center) { LoadingView() }
            post == null -> Box(modifier = Modifier.fillMaxSize().padding(paddingValues), contentAlignment = Alignment.Center) { ErrorView(message = "پست یافت نشد", onRetry = { postViewModel.loadPostDetail(postId) }) }
            else -> Column(modifier = Modifier.fillMaxSize().verticalScroll(rememberScrollState()).padding(paddingValues).padding(16.dp), verticalArrangement = Arrangement.spacedBy(16.dp)) {
                Card(modifier = Modifier.fillMaxWidth()) {
                    Column(modifier = Modifier.padding(16.dp)) {
                        Row(horizontalArrangement = Arrangement.SpaceBetween) {
                            Text(text = post!!.title ?: "بدون عنوان", style = MaterialTheme.typography.titleLarge, fontWeight = FontWeight.Bold)
                            StatusBadge(status = post!!.status ?: "draft")
                        }
                        Spacer(modifier = Modifier.height(12.dp))
                        Text(text = post!!.content ?: "", style = MaterialTheme.typography.bodyMedium)
                        post!!.mediaUrl?.let { url ->
                            Spacer(modifier = Modifier.height(12.dp))
                            Text(text = "تصویر: $url", style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.primary)
                        }
                        Spacer(modifier = Modifier.height(8.dp))
                        Row(horizontalArrangement = Arrangement.spacedBy(16.dp)) {
                            Text(text = post!!.createdAt ?: "", style = MaterialTheme.typography.labelSmall, color = MaterialTheme.colorScheme.outline)
                            PersianNumberText(text = "${post!!.clickCount ?: 0} کلیک", style = MaterialTheme.typography.labelSmall, color = MaterialTheme.colorScheme.primary)
                        }
                    }
                }

                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    if (post!!.status == "failed") {
                        Button(onClick = { postViewModel.retryPost(postId) }, modifier = Modifier.weight(1f)) { Icon(Icons.Default.Refresh, contentDescription = null); Spacer(modifier = Modifier.width(4.dp)); Text("تلاش مجدد") }
                    }
                    if (post!!.status == "scheduled" || post!!.status == "draft" || post!!.status == "queued") {
                        OutlinedButton(onClick = { postViewModel.cancelPost(postId); onBack() }, modifier = Modifier.weight(1f), colors = ButtonDefaults.outlinedButtonColors(contentColor = MaterialTheme.colorScheme.error)) { Text("لغو") }
                    }
                }
            }
        }
    }
}
