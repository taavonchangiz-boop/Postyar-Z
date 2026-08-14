package com.postyar.app.presentation.screens.referral

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ContentCopy
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
import com.postyar.app.presentation.viewmodels.ReferralViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ReferralScreen(
    referralViewModel: ReferralViewModel = hiltViewModel()
) {
    val referral by referralViewModel.referral.collectAsStateWithLifecycle()
    val isLoading by referralViewModel.isLoading.collectAsStateWithLifecycle(initialValue = false)

    LaunchedEffect(Unit) { referralViewModel.loadReferral() }

    Scaffold(topBar = { PostyarTopBar(title = "زیرمجموعه‌گیری") }) { paddingValues ->
        when {
            isLoading -> Box(modifier = Modifier.fillMaxSize().padding(paddingValues), contentAlignment = Alignment.Center) {
                com.postyar.app.presentation.components.LoadingView()
            }
            else -> Column(modifier = Modifier.fillMaxSize().verticalScroll(rememberScrollState()).padding(paddingValues).padding(16.dp), verticalArrangement = Arrangement.spacedBy(16.dp)) {

                Card(modifier = Modifier.fillMaxWidth(), colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.primaryContainer)) {
                    Column(modifier = Modifier.padding(20.dp), horizontalAlignment = Alignment.CenterHorizontally) {
                        Text("کد دعوت شما", style = MaterialTheme.typography.bodyMedium)
                        Spacer(modifier = Modifier.height(8.dp))
                        Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(12.dp)) {
                            Text(referral?.code ?: "", style = MaterialTheme.typography.headlineSmall, fontWeight = FontWeight.Bold)
                            IconButton(onClick = { /* copy code */ }) { Icon(Icons.Default.ContentCopy, contentDescription = "کپی") }
                        }
                    }
                }

                ReferralField(label = "لینک دعوت", value = referral?.link ?: "", onCopy = {})

                Card(modifier = Modifier.fillMaxWidth()) {
                    Row(modifier = Modifier.padding(16.dp).fillMaxWidth(), horizontalArrangement = Arrangement.SpaceAround) {
                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                            PersianNumberText(text = "${referral?.stats?.total ?: 0}", style = MaterialTheme.typography.headlineMedium, fontWeight = FontWeight.Bold)
                            Text("کل زیرمجموعه‌ها", style = MaterialTheme.typography.bodySmall)
                        }
                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                            PersianNumberText(text = "${referral?.referralPoints ?: 0.0}", style = MaterialTheme.typography.headlineMedium, fontWeight = FontWeight.Bold)
                            Text("امتیاز", style = MaterialTheme.typography.bodySmall)
                        }
                    }
                }

                if (!referral?.referrals.isNullOrEmpty()) {
                    Text("کاربران دعوت شده", style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.Bold)
                    LazyColumn(verticalArrangement = Arrangement.spacedBy(6.dp)) {
                        items(referral?.referrals ?: emptyList()) { ref ->
                            Card(colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surfaceVariant)) {
                                Row(modifier = Modifier.padding(10.dp).fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween) {
                                    Column {
                                        Text(ref.referredName ?: "", style = MaterialTheme.typography.bodyMedium, fontWeight = FontWeight.Medium)
                                        Text(ref.referredEmail ?: "", style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
                                    }
                                    com.postyar.app.presentation.components.StatusBadge(status = ref.status ?: "")
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

@Composable
private fun ReferralField(label: String, value: String, onCopy: () -> Unit) {
    OutlinedTextField(value = value, onValueChange = {}, label = { Text(label) }, modifier = Modifier.fillMaxWidth(), readOnly = true,
        trailingIcon = { IconButton(onClick = onCopy) { Icon(Icons.Default.ContentCopy, contentDescription = "کپی") } })
}