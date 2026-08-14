package com.postyar.app.presentation.screens.channels

import androidx.compose.foundation.layout.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import com.postyar.app.presentation.components.PostyarTopBar
import com.postyar.app.presentation.viewmodels.ChannelViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun EditChannelScreen(
    channelId: Int,
    onBack: () -> Unit,
    channelViewModel: ChannelViewModel = hiltViewModel()
) {
    val channel by channelViewModel.channelDetail.collectAsStateWithLifecycle()
    val isLoading by channelViewModel.isLoading.collectAsStateWithLifecycle(initialValue = false)
    val isSaving by channelViewModel.isSubmitting.collectAsStateWithLifecycle(initialValue = false)

    var name by remember { mutableStateOf("") }
    var channelIdVal by remember { mutableStateOf("") }
    var token by remember { mutableStateOf("") }

    LaunchedEffect(channelId) {
        channelViewModel.loadChannelDetail(channelId)
    }

    LaunchedEffect(channel) {
        channel?.let {
            name = it.name ?: ""
            channelIdVal = it.channelId ?: ""
            token = it.token ?: ""
        }
    }

    Scaffold(topBar = { PostyarTopBar(title = "ویرایش کانال", onBack = onBack) }) { paddingValues ->
        Column(modifier = Modifier.fillMaxSize().padding(paddingValues).padding(16.dp), verticalArrangement = Arrangement.spacedBy(16.dp)) {
            OutlinedTextField(value = name, onValueChange = { name = it }, label = { Text("نام") }, modifier = Modifier.fillMaxWidth(), singleLine = true)
            OutlinedTextField(value = channelIdVal, onValueChange = { channelIdVal = it }, label = { Text("شناسه") }, modifier = Modifier.fillMaxWidth(), singleLine = true)
            OutlinedTextField(value = token, onValueChange = { token = it }, label = { Text("توکن") }, modifier = Modifier.fillMaxWidth(), singleLine = true)
            Spacer(modifier = Modifier.weight(1f))
            Button(onClick = { channelViewModel.updateChannel(channelId, name, channelIdVal, token, onSuccess = onBack) }, modifier = Modifier.fillMaxWidth(), enabled = !isSaving) {
                if (isSaving) CircularProgressIndicator(modifier = Modifier.size(20.dp), strokeWidth = 2.dp, color = MaterialTheme.colorScheme.onPrimary)
                else Text("ذخیره")
            }
        }
    }
}