package com.postyar.app.ui.theme

import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.lightColorScheme
import androidx.compose.runtime.Composable
import androidx.compose.ui.graphics.Color

private val Primary = Color(0xFF6366F1)
private val OnPrimary = Color.White
private val PrimaryContainer = Color(0xFFE0E7FF)
private val Secondary = Color(0xFF6366F1)
private val Background = Color(0xFFF8FAFC)
private val Surface = Color.White

private val LightColorScheme = lightColorScheme(
    primary = Primary,
    onPrimary = OnPrimary,
    primaryContainer = PrimaryContainer,
    secondary = Secondary,
    background = Background,
    surface = Surface,
)

@Composable
fun PostyarTheme(content: @Composable () -> Unit) {
    MaterialTheme(
        colorScheme = LightColorScheme,
        content = content
    )
}