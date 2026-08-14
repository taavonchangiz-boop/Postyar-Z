package com.postyar.app.presentation.components

import androidx.compose.runtime.Composable
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.buildAnnotatedString

@Composable
fun PersianNumberText(
    number: Any,
    modifier: Modifier = Modifier
) {
    val persianDigits = charArrayOf('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹')
    val input = number.toString()
    val persian = buildAnnotatedString {
        for (c in input) {
            if (c in '0'..'9') {
                append(persianDigits[c - '0'])
            } else {
                append(c)
            }
        }
    }
    androidx.compose.material3.Text(text = persian, modifier = modifier)
}

fun toPersianNumber(input: String): String {
    val persianDigits = charArrayOf('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹')
    val sb = StringBuilder()
    for (c in input) {
        if (c in '0'..'9') {
            sb.append(persianDigits[c - '0'])
        } else {
            sb.append(c)
        }
    }
    return sb.toString()
}

fun formatPrice(amount: Double): String {
    val formatted = "%,.0f".format(amount)
    return toPersianNumber(formatted) + " تومان"
}