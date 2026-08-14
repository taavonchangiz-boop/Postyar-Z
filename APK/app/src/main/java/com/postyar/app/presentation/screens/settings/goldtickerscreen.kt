package com.postyar.app.presentation.screens.settings

import android.net.Uri
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyRow
import androidx.compose.foundation.lazy.items
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import com.postyar.app.presentation.components.PostyarTopBar
import com.postyar.app.presentation.viewmodels.ChannelViewModel
import com.postyar.app.presentation.viewmodels.SettingsViewModel
import androidx.lifecycle.compose.collectAsStateWithLifecycle

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun GoldTickerScreen(
    onBack: () -> Unit,
    settingsViewModel: SettingsViewModel = hiltViewModel(),
    channelViewModel: ChannelViewModel = hiltViewModel()
) {
    var schedule by remember { mutableStateOf("") }
    var goldApiUrl by remember { mutableStateOf("") }
    var currency by remember { mutableStateOf("") }
    var template by remember { mutableStateOf("") }
    var imageUri by remember { mutableStateOf<Uri?>(null) }
    val selectedChannels = remember { mutableStateListOf<Int>() }
    val isSaving by settingsViewModel.isSaving.collectAsStateWithLifecycle(initialValue = false)
    val channels by channelViewModel.channels.collectAsStateWithLifecycle()

    val imageLauncher = rememberLauncherForActivityResult(ActivityResultContracts.GetContent()) { uri -> uri?.let { imageUri = it } }

    LaunchedEffect(Unit) { channelViewModel.loadChannels(); settingsViewModel.loadSettings() }

    Scaffold(topBar = { PostyarTopBar(title = "تیکر قیمت طلا", onBack = onBack) }) { paddingValues ->
        Column(
            modifier = Modifier.fillMaxSize().verticalScroll(rememberScrollState()).padding(paddingValues).padding(16.dp),
            verticalArrangement = Arrangement.spacedBy(16.dp)
        ) {
            OutlinedTextField(value = schedule, onValueChange = { schedule = it }, label = { Text("زمان‌بندی (کرون)") }, modifier = Modifier.fillMaxWidth())
            OutlinedTextField(value = goldApiUrl, onValueChange = { goldApiUrl = it }, label = { Text("آدرس API طلا") }, modifier = Modifier.fillMaxWidth())
            OutlinedTextField(value = currency, onValueChange = { currency = it }, label = { Text("واحد پول") }, modifier = Modifier.fillMaxWidth())
            OutlinedTextField(value = template, onValueChange = { template = it }, label = { Text("قالب پیام") }, modifier = Modifier.fillMaxWidth().height(120.dp), maxLines = 8)

            OutlinedButton(onClick = { imageLauncher.launch("image/*") }) {
                Text(if (imageUri != null) "تصویر نمودار انتخاب شده ✓" else "بارگذاری نمودار قیمت")
            }

            Text("کانال‌های هدف", style = MaterialTheme.typography.titleSmall)
            LazyRow(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                items(channels) { ch ->
                    val selected = selectedChannels.contains(ch.id)
                    FilterChip(selected = selected, onClick = { if (selected) selectedChannels.remove(ch.id) else selectedChannels.add(ch.id) }, label = { Text(ch.name ?: "") })
                }
            }

            Spacer(modifier = Modifier.height(16.dp))

            Button(onClick = { settingsViewModel.triggerGold(onSuccess = { }) }, modifier = Modifier.fillMaxWidth(), enabled = !isSaving) {
                if (isSaving) CircularProgressIndicator(modifier = Modifier.size(20.dp), strokeWidth = 2.dp, color = MaterialTheme.colorScheme.onPrimary)
                else Text("ارسال دستی")
            }

            Button(onClick = {
                settingsViewModel.saveGold(schedule, goldApiUrl, currency, template, selectedChannels.toList(), imageUri, onSuccess = onBack)
            }, modifier = Modifier.fillMaxWidth(), enabled = !isSaving) {
                Text("ذخیره تنظیمات")
            }
        }
    }
}