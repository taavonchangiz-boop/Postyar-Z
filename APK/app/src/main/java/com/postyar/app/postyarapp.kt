package com.postyar.app

import android.app.Application
import dagger.hilt.android.HiltAndroidApp

@HiltAndroidApp
class PostyarApp : Application() {
    override fun onCreate() {
        super.onCreate()
    }
}
