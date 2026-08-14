package com.postyar.app.presentation.screens.channels

import androidx.compose.foundation.layout.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import com.postyar.app.presentation.components.PostyarTopBar
import com.postyar.app.presentation.viewmodels.ChannelViewModel
import androidx.lifecycle.compose.collectAsStateWithLifecycle

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun AddChannelScreen(
    onBack: () -> Unit,
    channelViewModel: ChannelViewModel = hiltViewModel()
) {
    var name by remember { mutableStateOf("") }
    var platform by remember { mutableStateOf("telegram") }
    var channelId by remember { mutableStateOf("") }
    var token by remember { mutableStateOf("") }
    val isSubmitting by channelViewModel.isSubmitting.collectAsStateWithLifecycle(initialValue = false)
    val error by channelViewModel.error.collectAsStateWithLifecycle()

    val platforms = listOf("telegram" to "تلگرام", "eitaa" to "ایتا", "gap" to "گپ")
    var expanded by remember { mutableStateOf(false) }

    Scaffold(topBar = { PostyarTopBar(title = "افزودن کانال", onBack = onBack) }) { paddingValues ->
        Column(
            modifier = Modifier.fillMaxSize().padding(paddingValues).padding(16.dp),
            verticalArrangement = Arrangement.spacedBy(16.dp)
        ) {
            OutlinedTextField(value = name, onValueChange = { name = it }, label = { Text("نام کانال") }, modifier = Modifier.fillMaxWidth(), singleLine = true)

            Box(modifier = Modifier.fillMaxWidth()) {
                OutlinedTextField(
                    value = platforms.firstOrNull { it.first == platform }?.second ?: "",
                    onValueChange = {},
                    label = { Text("پلتفرم") },
                    modifier = Modifier.fillMaxWidth(),
                    readOnly = true,
                    interactionSource = remember { MutableInteractionSource() }.also { source ->
                        LaunchedEffect(source) {
                            source.interactions.collect { if (it is PressInteraction.Release) expanded = true }
                        }
                    }
                )
                DropdownMenu(expanded = expanded, onDismissRequest = { expanded = false }) {
                    platforms.forEach { (key, label) ->
                        DropdownMenuItem(text = { Text(label) }, onClick = { platform = key; expanded = false })
                    }
                }
            }

            OutlinedTextField(value = channelId, onValueChange = { channelId = it }, label = { Text("شناسه کانال (مثلاً @channel)") }, modifier = Modifier.fillMaxWidth(), singleLine = true)
            OutlinedTextField(value = token, onValueChange = { token = it }, label = { Text("توکن ربات") }, modifier = Modifier.fillMaxWidth(), singleLine = true)

            error?.let { Text(text = it, color = MaterialTheme.colorScheme.error, style = MaterialTheme.typography.bodySmall) }

            Spacer(modifier = Modifier.weight(1f))

            Button(
                onClick = { channelViewModel.addChannel(name, platform, channelId, token, onSuccess = onBack) },
                modifier = Modifier.fillMaxWidth(),
                enabled = name.isNotBlank() && channelId.isNotBlank() && token.isNotBlank() && !isSubmitting
            ) {
                if (isSubmitting) CircularProgressIndicator(modifier = Modifier.size(20.dp), strokeWidth = 2.dp, color = MaterialTheme.colorScheme.onPrimary)
                else Text("افزودن کانال")
            }
        }
    }
}
