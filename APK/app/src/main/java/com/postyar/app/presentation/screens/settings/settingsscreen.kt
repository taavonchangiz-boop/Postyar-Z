package com.postyar.app.presentation.screens.settings

import androidx.compose.foundation.layout.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import com.postyar.app.presentation.components.PostyarTopBar
import com.postyar.app.presentation.viewmodels.SettingsViewModel
import androidx.lifecycle.compose.collectAsStateWithLifecycle

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun SettingsScreen(
    onBack: () -> Unit,
    settingsViewModel: SettingsViewModel = hiltViewModel()
) {
    val settings by settingsViewModel.settings.collectAsStateWithLifecycle()
    val isLoading by settingsViewModel.isLoading.collectAsStateWithLifecycle(initialValue = false)
    val isSaving by settingsViewModel.isSaving.collectAsStateWithLifecycle(initialValue = false)

    var aiProvider by remember { mutableStateOf(settings?.aiProvider ?: "openai") }
    var apiKey by remember { mutableStateOf(settings?.aiApiKey ?: "") }
    var aiModel by remember { mutableStateOf(settings?.aiModel ?: "gpt-4") }
    var aiApiUrl by remember { mutableStateOf(settings?.aiApiUrl ?: "") }
    var captionFormat by remember { mutableStateOf(settings?.captionFormat ?: "") }
    var watermarkActive by remember { mutableStateOf((settings?.watermarkActive == "1")) }
    var autoPublishWoo by remember { mutableStateOf((settings?.autoPublishWoo == "1")) }
    var inboundMethod by remember { mutableStateOf(settings?.inboundMethod ?: "webhook") }
    var pollInterval by remember { mutableStateOf(settings?.pollInterval ?: "60") }

    LaunchedEffect(Unit) { settingsViewModel.loadSettings() }

    Scaffold(topBar = { PostyarTopBar(title = "تنظیمات", onBack = onBack) }) { paddingValues ->
        if (isLoading) {
            Box(modifier = Modifier.fillMaxSize().padding(paddingValues), contentAlignment = androidx.compose.ui.Alignment.Center) {
                com.postyar.app.presentation.components.LoadingView()
            }
        } else {
            Column(
                modifier = Modifier.fillMaxSize().verticalScroll(rememberScrollState()).padding(paddingValues).padding(16.dp),
                verticalArrangement = Arrangement.spacedBy(20.dp)
            ) {
                Text("تنظیمات هوش مصنوعی", style = MaterialTheme.typography.titleMedium, fontWeight = androidx.compose.ui.text.font.FontWeight.Bold)

                OutlinedTextField(value = aiProvider, onValueChange = { aiProvider = it }, label = { Text("فراهم‌کننده AI") }, modifier = Modifier.fillMaxWidth(), singleLine = true)
                OutlinedTextField(value = apiKey, onValueChange = { apiKey = it }, label = { Text("کلید API") }, modifier = Modifier.fillMaxWidth(), singleLine = true)
                OutlinedTextField(value = aiModel, onValueChange = { aiModel = it }, label = { Text("مدل") }, modifier = Modifier.fillMaxWidth(), singleLine = true)
                OutlinedTextField(value = aiApiUrl, onValueChange = { aiApiUrl = it }, label = { Text("آدرس API (اختیاری)") }, modifier = Modifier.fillMaxWidth(), singleLine = true)

                Divider()

                Text("تنظیمات عمومی", style = MaterialTheme.typography.titleMedium, fontWeight = androidx.compose.ui.text.font.FontWeight.Bold)

                OutlinedTextField(value = captionFormat, onValueChange = { captionFormat = it }, label = { Text("فرمت کپشن") }, modifier = Modifier.fillMaxWidth())

                Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.SpaceBetween, modifier = Modifier.fillMaxWidth()) {
                    Text(text = "واترمارک فعال")
                    Switch(checked = watermarkActive, onCheckedChange = { watermarkActive = it })
                }
                Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.SpaceBetween, modifier = Modifier.fillMaxWidth()) {
                    Text(text = "انتشار خودکار ووکامرس")
                    Switch(checked = autoPublishWoo, onCheckedChange = { autoPublishWoo = it })
                }

                Divider()
                Text("دریافت پیام‌ها", style = MaterialTheme.typography.titleMedium, fontWeight = androidx.compose.ui.text.font.FontWeight.Bold)
                Row(horizontalArrangement = Arrangement.spacedBy(12.dp)) {
                    FilterChip(selected = inboundMethod == "webhook", onClick = { inboundMethod = "webhook" }, label = { Text("وبهوک") })
                    FilterChip(selected = inboundMethod == "poll", onClick = { inboundMethod = "poll" }, label = { Text("پولینگ") })
                }
                if (inboundMethod == "poll") {
                    OutlinedTextField(value = pollInterval, onValueChange = { pollInterval = it }, label = { Text("فاصله پولینگ (ثانیه)") }, modifier = Modifier.fillMaxWidth())
                }

                Spacer(modifier = Modifier.height(24.dp))
                Button(
                    onClick = {
                        settingsViewModel.saveAdvanced(
                            aiProvider = aiProvider, aiApiKey = apiKey, aiModel = aiModel,
                            aiApiUrl = aiApiUrl, captionFormat = captionFormat,
                            watermarkActive = if (watermarkActive) "1" else "0",
                            autoPublishWoo = if (autoPublishWoo) "1" else "0",
                            inboundMethod = inboundMethod, pollInterval = pollInterval,
                            onSuccess = onBack
                        )
                    },
                    modifier = Modifier.fillMaxWidth(),
                    enabled = !isSaving
                ) {
                    if (isSaving) CircularProgressIndicator(modifier = Modifier.size(20.dp), strokeWidth = 2.dp, color = MaterialTheme.colorScheme.onPrimary)
                    else Text("ذخیره تنظیمات")
                }
            }
        }
    }
}