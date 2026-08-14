package com.postyar.app;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.pm.PackageManager;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import org.json.JSONArray;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.HashMap;
import java.util.Map;

import javax.net.ssl.HttpsURLConnection;

public class MainActivity extends Activity {

    private static final String BASE_URL = "https://asovin.ir/api/v1/";
    private static final String PREFS_NAME = "postyar_prefs";

    private TextView tvWelcome;
    private TextView tvQuota;
    private TextView tvPostsCount;
    private TextView tvChannelsCount;
    private TextView tvStatus;

    private SharedPreferences prefs;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        // Force RTL
        getWindow().getDecorView().setLayoutDirection(View.LAYOUT_DIRECTION_RTL);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            Window window = getWindow();
            window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
            window.setStatusBarColor(0xFF4F46E5);
        }

        setContentView(R.layout.activity_main);

        prefs = getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE);

        tvWelcome = findViewById(R.id.tvWelcome);
        tvQuota = findViewById(R.id.tvQuota);
        tvPostsCount = findViewById(R.id.tvPostsCount);
        tvChannelsCount = findViewById(R.id.tvChannelsCount);
        tvStatus = findViewById(R.id.tvStatus);

        // Set version
        try {
            String version = getPackageManager().getPackageInfo(getPackageName(), 0).versionName;
            tvStatus.setText("نسخه " + version);
        } catch (PackageManager.NameNotFoundException ignored) {}

        // Open Web Panel button
        findViewById(R.id.btnOpenWeb).setOnClickListener(v -> {
            String url = BASE_URL.replace("/api/v1/", "");
            openUrl(url);
        });

        // Settings button
        findViewById(R.id.btnSettings).setOnClickListener(v -> {
            showSettingsDialog();
        });

        // About button
        findViewById(R.id.btnAbout).setOnClickListener(v -> {
            new AlertDialog.Builder(this)
                .setTitle("درباره پُست‌یار")
                .setMessage("پُست‌یار - نسخه ۱.۰.۰\n\nاپلیکیشن مدیریت پست و کانال\nبا استفاده از API رسمی asovin.ir")
                .setPositiveButton("بستن", null)
                .show();
        });

        // Load data
        String token = prefs.getString("token", null);
        if (token != null && !token.isEmpty()) {
            loadBootstrap(token);
        } else {
            tvWelcome.setText("سلام، به پُست‌یار خوش آمدید");
            tvQuota.setText("برای شروع وارد حساب شوید");
        }
    }

    private void openUrl(String url) {
        try {
            Intent intent = new Intent(Intent.ACTION_VIEW);
            intent.setData(Uri.parse(url));
            startActivity(intent);
        } catch (Exception e) {
            Toast.makeText(this, "خطا در باز کردن مرورگر", Toast.LENGTH_SHORT).show();
        }
    }

    private void showSettingsDialog() {
        new AlertDialog.Builder(this)
            .setTitle("تنظیمات")
            .setItems(new String[]{"باز کردن پنل در مرورگر", "پاک‌سازی حافظه محلی", "درباره اپلیکیشن"}, (dialog, which) -> {
                switch (which) {
                    case 0:
                        openUrl(BASE_URL.replace("/api/v1/", ""));
                        break;
                    case 1:
                        prefs.edit().clear().apply();
                        Toast.makeText(this, "حافظه پاک شد", Toast.LENGTH_SHORT).show();
                        break;
                    case 2:
                        new AlertDialog.Builder(this)
                            .setTitle("درباره پُست‌یار")
                            .setMessage("نسخه ۱.۰.۰\n\nپُست‌یار - مدیریت هوشمند پست‌ها و کانال‌ها")
                            .setPositiveButton("بستن", null)
                            .show();
                        break;
                }
            })
            .show();
    }

    private void loadBootstrap(String token) {
        new BootstrapTask(this).execute(token);
    }

    private void updateUI(JSONObject data) {
        try {
            JSONObject user = data.getJSONObject("user");
            String name = user.optString("name", "کاربر");
            tvWelcome.setText("سلام، " + name);

            JSONObject quota = data.getJSONObject("quota");
            int postsUsed = quota.optInt("posts_used", 0);
            int postsLimit = quota.optInt("posts_limit", 0);
            int channelsUsed = quota.optInt("channels_used", 0);
            int channelsLimit = quota.optInt("channels_limit", 0);

            tvQuota.setText("پست: " + toPersianNum(postsUsed) + "/" + toPersianNum(postsLimit) +
                    " | کانال: " + toPersianNum(channelsUsed) + "/" + toPersianNum(channelsLimit));
            tvPostsCount.setText(toPersianNum(postsUsed));
            tvChannelsCount.setText(toPersianNum(channelsUsed));

            JSONArray posts = data.optJSONArray("posts");
            JSONArray channels = data.optJSONArray("channels");
            if (posts != null) tvPostsCount.setText(toPersianNum(posts.length()));
            if (channels != null) tvChannelsCount.setText(toPersianNum(channels.length()));

        } catch (Exception e) {
            tvWelcome.setText("سلام، به پُست‌یار خوش آمدید");
            tvQuota.setText("در حال بارگذاری...");
        }
    }

    private static String toPersianNum(int number) {
        String digits = "۰۱۲۳۴۵۶۷۸۹";
        return String.valueOf(number).replace("0", "۰")
                .replace("1", "۱").replace("2", "۲")
                .replace("3", "۳").replace("4", "۴")
                .replace("5", "۵").replace("6", "۶")
                .replace("7", "۷").replace("8", "۸")
                .replace("9", "۹");
    }

    private static class BootstrapTask extends AsyncTask<String, Void, String> {
        private final MainActivity activity;

        BootstrapTask(MainActivity activity) {
            this.activity = activity;
        }

        @Override
        protected String doInBackground(String... params) {
            return httpGet(BASE_URL + "bootstrap", params[0]);
        }

        @Override
        protected void onPostExecute(String result) {
            if (result != null && !result.isEmpty()) {
                try {
                    JSONObject json = new JSONObject(result);
                    if (json.optBoolean("success")) {
                        JSONObject data = json.getJSONObject("data");
                        activity.updateUI(data);

                        // Save token
                        String token = activity.prefs.getString("token", "");
                        if (!token.isEmpty()) {
                            // Token already saved
                        }
                    }
                } catch (Exception e) {
                    // Silent fail
                }
            }
        }
    }

    private static String httpGet(String urlStr, String token) {
        HttpURLConnection conn = null;
        try {
            URL url = new URL(urlStr);
            conn = (HttpURLConnection) url.openConnection();
            conn.setRequestMethod("GET");
            conn.setRequestProperty("Authorization", "Bearer " + token);
            conn.setRequestProperty("Accept", "application/json");
            conn.setConnectTimeout(15000);
            conn.setReadTimeout(15000);

            int code = conn.getResponseCode();
            InputStream is = code >= 200 && code < 300 ? conn.getInputStream() : conn.getErrorStream();
            if (is == null) return null;

            BufferedReader reader = new BufferedReader(new InputStreamReader(is, "UTF-8"));
            StringBuilder sb = new StringBuilder();
            String line;
            while ((line = reader.readLine()) != null) sb.append(line);
            reader.close();
            return sb.toString();
        } catch (Exception e) {
            return null;
        } finally {
            if (conn != null) conn.disconnect();
        }
    }
}
