package com.postyar.app.presentation.screens.analytics

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import com.postyar.app.presentation.components.PostyarTopBar
import com.postyar.app.presentation.components.PersianNumberText
import com.postyar.app.presentation.components.EmptyStateView
import com.postyar.app.presentation.components.LoadingView
import com.postyar.app.presentation.viewmodels.AnalyticsViewModel
import com.postyar.app.data.remote.dto.AnalyticsLinkDto

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun AnalyticsScreen(
    analyticsViewModel: AnalyticsViewModel = hiltViewModel()
) {
    val links by analyticsViewModel.links.collectAsStateWithLifecycle()
    val isLoading by analyticsViewModel.isLoading.collectAsStateWithLifecycle(initialValue = false)
    var selectedLink by remember { mutableStateOf<AnalyticsLinkDto?>(null) }

    LaunchedEffect(Unit) { analyticsViewModel.loadLinks() }

    Scaffold(topBar = { PostyarTopBar(title = "تحلیل لینک‌ها") }) { paddingValues ->
        if (isLoading) {
            Box(modifier = Modifier.fillMaxSize().padding(paddingValues), contentAlignment = Alignment.Center) { LoadingView() }
        } else if (links.isEmpty()) {
            Box(modifier = Modifier.fillMaxSize().padding(paddingValues), contentAlignment = Alignment.Center) { EmptyStateView(message = "لینکی ثبت نشده") }
        } else if (selectedLink != null) {
            LinkDetailScreen(link = selectedLink!!, onBack = { selectedLink = null }, analyticsViewModel = analyticsViewModel)
        } else {
            LazyColumn(
                modifier = Modifier.fillMaxSize().padding(paddingValues),
                contentPadding = PaddingValues(16.dp),
                verticalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                items(links, key = { it.id }) { link ->
                    LinkCard(link = link, onClick = { selectedLink = link })
                }
            }
        }
    }
}

@Composable
private fun LinkCard(link: AnalyticsLinkDto, onClick: () -> Unit) {
    Card(modifier = Modifier.fillMaxWidth(), onClick = onClick) {
        Column(modifier = Modifier.padding(16.dp)) {
            Text(text = link.originalUrl ?: "", style = MaterialTheme.typography.bodyMedium, fontWeight = FontWeight.Medium, maxLines = 1)
            Spacer(modifier = Modifier.height(8.dp))
            Row(horizontalArrangement = Arrangement.SpaceAround, modifier = Modifier.fillMaxWidth()) {
                Column(horizontalAlignment = Alignment.CenterHorizontally) {
                    PersianNumberText(text = "${link.totalClicks ?: 0}", style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.Bold)
                    Text("کل کلیک", style = MaterialTheme.typography.labelSmall)
                }
                Column(horizontalAlignment = Alignment.CenterHorizontally) {
                    PersianNumberText(text = "${link.uniqueClicks ?: 0}", style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.Bold)
                    Text("یکتا", style = MaterialTheme.typography.labelSmall)
                }
            }
        }
    }
}

@Composable
private fun LinkDetailScreen(
    link: AnalyticsLinkDto,
    onBack: () -> Unit,
    analyticsViewModel: AnalyticsViewModel
) {
    val detail by analyticsViewModel.linkDetail.collectAsStateWithLifecycle()
    val isDetailLoading by analyticsViewModel.isDetailLoading.collectAsStateWithLifecycle(initialValue = false)

    LaunchedEffect(link.id) { analyticsViewModel.loadLinkDetail(link.id) }

    Column(modifier = Modifier.fillMaxSize().padding(paddingValues)) {
        Row(verticalAlignment = Alignment.CenterVertically) {
            IconButton(onClick = onBack) { Icon(Icons.Default.ArrowBack, contentDescription = "بازگشت") }
            Text(text = "جزئیات لینک", style = MaterialTheme.typography.titleLarge, fontWeight = FontWeight.Bold)
        }

        if (isDetailLoading) {
            Box(modifier = Modifier.weight(1f).fillMaxWidth(), contentAlignment = Alignment.Center) { CircularProgressIndicator() }
        } else {
            LazyColumn(
                modifier = Modifier.weight(1f).fillMaxWidth(),
                contentPadding = PaddingValues(16.dp),
                verticalArrangement = Arrangement.spacedBy(8.dp)
            ) {
                item {
                    Card(modifier = Modifier.fillMaxWidth()) {
                        Column(modifier = Modifier.padding(16.dp)) {
                            Text(text = link.originalUrl ?: "", style = MaterialTheme.typography.bodyMedium)
                            Spacer(modifier = Modifier.height(8.dp))
                            Row(horizontalArrangement = Arrangement.SpaceAround) {
                                Column { PersianNumberText("${link.totalClicks ?: 0}"); Text("کل کلیک") }
                                Column { PersianNumberText("${link.uniqueClicks ?: 0}"); Text("یکتا") }
                            }
                        }
                    }
                }
                items(detail?.dailyBreakdown ?: emptyList()) { day ->
                    Card(colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surfaceVariant)) {
                        Row(modifier = Modifier.padding(12.dp).fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween) {
                            Text(day.date ?: "", style = MaterialTheme.typography.bodyMedium)
                            PersianNumberText("${day.clicks ?: 0} کلیک", fontWeight = FontWeight.Medium)
                        }
                    }
                }
            }
        }
    }
}
