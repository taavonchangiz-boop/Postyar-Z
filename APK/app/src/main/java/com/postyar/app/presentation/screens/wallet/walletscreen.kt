package com.postyar.app.presentation.screens.wallet

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowDownward
import androidx.compose.material.icons.filled.ArrowUpward
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import com.postyar.app.presentation.components.*
import com.postyar.app.presentation.viewmodels.WalletViewModel
import com.postyar.app.data.remote.dto.WalletDto

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun WalletScreen(
    walletViewModel: WalletViewModel = hiltViewModel()
) {
    val wallet by walletViewModel.wallet.collectAsStateWithLifecycle()
    val isLoading by walletViewModel.isLoading.collectAsStateWithLifecycle(initialValue = false)
    var pointsToConvert by remember { mutableStateOf("") }
    val isConverting by walletViewModel.isConverting.collectAsStateWithLifecycle(initialValue = false)

    LaunchedEffect(Unit) { walletViewModel.loadWallet() }

    Scaffold(topBar = { PostyarTopBar(title = "کیف پول") }) { paddingValues ->
        when {
            isLoading -> Box(modifier = Modifier.fillMaxSize().padding(paddingValues), contentAlignment = Alignment.Center) { LoadingView() }
            else -> Column(modifier = Modifier.fillMaxSize().padding(paddingValues)) {
                BalanceCard(balance = wallet?.balance ?: 0.0)

                ConvertPointsSection(
                    pointsToConvert = pointsToConvert,
                    onPointsChange = { pointsToConvert = it },
                    isConverting = isConverting,
                    onConvert = { walletViewModel.convertPoints(pointsToConvert.toIntOrNull() ?: 0); pointsToConvert = "" }
                )

                LazyColumn(
                    modifier = Modifier.weight(1f).fillMaxWidth(),
                    contentPadding = PaddingValues(16.dp),
                    verticalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    item { Text("تراکنش‌ها", style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.Bold) }
                    if (wallet?.transactions.isNullOrEmpty()) {
                        item { EmptyStateView(message = "تراکنشی ثبت نشده") }
                    } else {
                        items(wallet?.transactions ?: emptyList(), key = { it.id }) { tx -> TransactionCard(tx) }
                    }
                }
            }
        }
    }
}

@Composable
private fun BalanceCard(balance: Double) {
    Card(
        modifier = Modifier.fillMaxWidth().padding(16.dp),
        colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.primaryContainer)
    ) {
        Column(modifier = Modifier.padding(24.dp), horizontalAlignment = Alignment.CenterHorizontally) {
            Text(text = "موجودی کیف پول", style = MaterialTheme.typography.bodyMedium, color = MaterialTheme.colorScheme.onSurfaceVariant)
            Spacer(modifier = Modifier.height(8.dp))
            PersianNumberText(
                text = "${String.format("%,.0f", balance)} تومان",
                style = MaterialTheme.typography.headlineMedium,
                fontWeight = FontWeight.Bold,
                color = MaterialTheme.colorScheme.primary,
                textAlign = TextAlign.Center
            )
        }
    }
}

@Composable
private fun ConvertPointsSection(pointsToConvert: String, onPointsChange: (String) -> Unit, isConverting: Boolean, onConvert: () -> Unit) {
    Card(modifier = Modifier.fillMaxWidth().padding(horizontal = 16.dp)) {
        Column(modifier = Modifier.padding(16.dp)) {
            Text("تبدیل امتیاز به موجودی", style = MaterialTheme.typography.titleSmall, fontWeight = FontWeight.Bold)
            Spacer(modifier = Modifier.height(12.dp))
            Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                OutlinedTextField(value = pointsToConvert, onValueChange = onPointsChange, label = { Text("تعداد امتیاز") }, modifier = Modifier.weight(1f), singleLine = true)
                Button(onClick = onConvert, enabled = pointsToConvert.isNotBlank() && !isConverting) {
                    if (isConverting) CircularProgressIndicator(modifier = Modifier.size(18.dp), strokeWidth = 2.dp, color = MaterialTheme.colorScheme.onPrimary)
                    else Text("تبدیل")
                }
            }
        }
    }
}

@Composable
private fun TransactionCard(tx: com.postyar.app.data.remote.dto.WalletTransactionDto) {
    val isCredit = tx.type == "credit"
    Card(colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surfaceVariant)) {
        Row(
            modifier = Modifier.padding(12.dp).fillMaxWidth(),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Icon(if (isCredit) Icons.Default.ArrowUpward else Icons.Default.ArrowDownward, null, tint = if (isCredit) Color(0xFF4CAF50) else Color(0xFFE53935), modifier = Modifier.size(20.dp))
            Spacer(modifier = Modifier.width(12.dp))
            Column(modifier = Modifier.weight(1f)) {
                Text(tx.description ?: "", style = MaterialTheme.typography.bodySmall, maxLines = 2)
                Text(tx.createdAt ?: "", style = MaterialTheme.typography.labelSmall, color = MaterialTheme.colorScheme.outline)
            }
            PersianNumberText(
                text = "${if (isCredit) "+" else "-"}${String.format("%,.0f", tx.amount)}",
                style = MaterialTheme.typography.bodyMedium,
                fontWeight = FontWeight.Medium,
                color = if (isCredit) Color(0xFF4CAF50) else Color(0xFFE53935)
            )
        }
    }
}