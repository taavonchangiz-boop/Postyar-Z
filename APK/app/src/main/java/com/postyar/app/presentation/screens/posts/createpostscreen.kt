package com.postyar.app.presentation.screens.posts

import android.net.Uri
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyRow
import androidx.compose.foundation.lazy.items
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import com.postyar.app.presentation.components.PostyarTopBar
import com.postyar.app.presentation.viewmodels.PostViewModel
import com.postyar.app.presentation.viewmodels.ChannelViewModel
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import com.postyar.app.data.remote.dto.ChannelDto

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun CreatePostScreen(
    onBack: () -> Unit,
    postViewModel: PostViewModel = hiltViewModel(),
    channelViewModel: ChannelViewModel = hiltViewModel()
) {
    var title by remember { mutableStateOf("") }
    var content by remember { mutableStateOf("") }
    var sendType by remember { mutableStateOf("instant") }
    var schedDate by remember { mutableStateOf("") }
    var schedHour by remember { mutableStateOf("") }
    var schedMinute by remember { mutableStateOf("") }
    var mediaUri by remember { mutableStateOf<Uri?>(null) }
    val selectedChannels = remember { mutableStateListOf<Int>() }
    val channels by channelViewModel.channels.collectAsStateWithLifecycle()
    val isSubmitting by postViewModel.isSubmitting.collectAsStateWithLifecycle(initialValue = false)

    val imageLauncher = rememberLauncherForActivityResult(ActivityResultContracts.GetContent()) { uri ->
        uri?.let { mediaUri = it }
    }

    LaunchedEffect(Unit) { channelViewModel.loadChannels() }

    Scaffold(topBar = { PostyarTopBar(title = "ایجاد پست جدید", onBack = onBack) }) { paddingValues ->
        Column(
            modifier = Modifier.fillMaxSize().padding(paddingValues).padding(16.dp),
            verticalArrangement = Arrangement.spacedBy(16.dp)
        ) {
            OutlinedTextField(
                value = title, onValueChange = { title = it },
                label = { Text("عنوان پست") }, modifier = Modifier.fillMaxWidth(), singleLine = true
            )
            OutlinedTextField(
                value = content, onValueChange = { content = it },
                label = { Text("محتوای پست") }, modifier = Modifier.fillMaxWidth().height(150.dp), maxLines = 10
            )

            OutlinedButton(onClick = { imageLauncher.launch("image/*") }, modifier = Modifier.fillMaxWidth()) {
                Text(text = if (mediaUri != null) "تصویر انتخاب شده ✓" else "انتخاب تصویر (اختیاری)")
            }

            Text(text = "کانال‌های هدف", style = MaterialTheme.typography.titleSmall, fontWeight = FontWeight.Bold)
            LazyRow(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                items(channels) { channel ->
                    val selected = selectedChannels.contains(channel.id)
                    FilterChip(
                        selected = selected,
                        onClick = {
                            if (selected) selectedChannels.remove(channel.id) else selectedChannels.add(channel.id)
                        },
                        label = { Text(channel.name ?: "") }
                    )
                }
            }

            Text(text = "نوع ارسال", style = MaterialTheme.typography.titleSmall, fontWeight = FontWeight.Bold)
            Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                FilterChip(selected = sendType == "instant", onClick = { sendType = "instant" }, label = { Text("فوری") })
                FilterChip(selected = sendType == "scheduled", onClick = { sendType = "scheduled" }, label = { Text("زمان‌بندی") })
            }

            if (sendType == "scheduled") {
                OutlinedTextField(value = schedDate, onValueChange = { schedDate = it }, label = { Text("تاریخ (مثلاً 1403/03/25)") }, modifier = Modifier.fillMaxWidth())
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    OutlinedTextField(value = schedHour, onValueChange = { schedHour = it }, label = { Text("ساعت") }, modifier = Modifier.weight(1f))
                    OutlinedTextField(value = schedMinute, onValueChange = { schedMinute = it }, label = { Text("دقیقه") }, modifier = Modifier.weight(1f))
                }
            }

            Spacer(modifier = Modifier.weight(1f))

            Button(
                onClick = {
                    postViewModel.createPost(
                        title = title, content = content, sendType = sendType,
                        channelIds = selectedChannels.toList(), mediaUri = mediaUri,
                        schedDate = schedDate.ifEmpty { null }, schedHour = schedHour.ifEmpty { null }, schedMinute = schedMinute.ifEmpty { null },
                        onSuccess = onBack
                    )
                },
                modifier = Modifier.fillMaxWidth(),
                enabled = title.isNotBlank() && content.isNotBlank() && selectedChannels.isNotEmpty() && !isSubmitting
            ) {
                if (isSubmitting) CircularProgressIndicator(modifier = Modifier.size(20.dp), strokeWidth = 2.dp, color = MaterialTheme.colorScheme.onPrimary)
                else Text(text = if (sendType == "instant") "ارسال فوری" else "زمان‌بندی ارسال")
            }
        }
    }
}
