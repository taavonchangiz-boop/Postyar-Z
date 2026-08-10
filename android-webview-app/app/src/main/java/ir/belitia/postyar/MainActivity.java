package ir.belitia.postyar;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.content.res.Configuration;
import android.graphics.Bitmap;
import android.net.ConnectivityManager;
import android.net.NetworkCapabilities;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.view.KeyEvent;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.webkit.CookieManager;
import android.webkit.ValueCallback;
import android.webkit.WebChromeClient;
import android.webkit.WebResourceError;
import android.webkit.WebResourceRequest;
import android.webkit.WebResourceResponse;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.ProgressBar;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.content.ContextCompat;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

import java.io.ByteArrayInputStream;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;

/**
 * اکتیویتی اصلی اپلیکیشن پُست‌یار
 *
 * ویژگی‌ها:
 * - باز شدن مستقیم داشبورد (بدون صفحه اصلی)
 * - Splash Screen حرفه‌ای
 * - Pull-to-Refresh بومی
 * - Push Notification از طریق Service Worker
 * - مدیریت حرفه‌ای بک‌باسن
 * - آپلود فایل
 * - صفحه آفلاین سفارشی
 * - Cookie پایدار بین اجراها
 * - Share Target
 */
public class MainActivity extends AppCompatActivity {

    private static final String TAG = "Postyar";
    private static final int FILECHOOSER_RESULTCODE = 1001;

    private WebView webView;
    private ProgressBar progressBar;
    private SwipeRefreshLayout swipeRefreshLayout;
    private ValueCallback<Uri[]> uploadMessage;
    private boolean isSplashVisible = true;
    private boolean hasLoadedOnce = false;

    // ─── HTML Templates ──────────────────────────────────────────

    private static final String SPLASH_HTML = buildSplashHtml();
    private static final String OFFLINE_HTML = buildOfflineHtml();
    private static final String ERROR_HTML = buildErrorHtml();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        // حذف ActionBar
        if (getSupportActionBar() != null) getSupportActionBar().hide();

        // Status bar تم‌شده
        setupStatusBar();

        // لایه‌اوت اصلی
        setContentView(R.layout.activity_main);

        // اتصال ویوها
        webView = findViewById(R.id.webview);
        progressBar = findViewById(R.id.progress_bar);
        swipeRefreshLayout = findViewById(R.id.swipe_refresh);

        // Setup WebView
        setupWebView();

        // Setup Pull-to-Refresh
        setupSwipeRefresh();

        // مدیریت اکشن‌های ورودی (Share Target)
        handleIncomingIntent(getIntent());

        // بارگذاری اولیه
        loadApp();
    }

    // ─── Setup Methods ──────────────────────────────────────────

    private void setupStatusBar() {
        Window window = getWindow();
        window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
        window.setStatusBarColor(0xFF0A0A0A);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            window.getDecorView().setSystemUiVisibility(
                    View.SYSTEM_UI_FLAG_LIGHT_NAVIGATION_BAR
            );
        }
    }

    @SuppressLint("SetJavaScriptEnabled")
    private void setupWebView() {
        WebSettings settings = webView.getSettings();

        // عملکرد
        settings.setJavaScriptEnabled(true);
        settings.setDomStorageEnabled(true);
        settings.setDatabaseEnabled(true);

        // Cache
        settings.setCacheMode(WebSettings.LOAD_DEFAULT);

        // رندر
        settings.setLoadWithOverviewMode(true);
        settings.setUseWideViewPort(true);
        settings.setSupportZoom(false);

        // امنیت
        settings.setAllowFileAccess(false);
        settings.setAllowContentAccess(false);
        settings.setMixedContentMode(WebSettings.MIXED_CONTENT_NEVER_ALLOW);

        // Service Worker — الزامی برای PWA و Push
        CookieManager.getInstance().setAcceptThirdPartyCookies(webView, true);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
            settings.setSafeBrowsingEnabled(true);
        }

        // فونت و Viewport
        settings.setTextZoom(100);

        // WebViewClient
        webView.setWebViewClient(new PostyarWebViewClient());

        // WebChromeClient
        webView.setWebChromeClient(new PostyarChromeClient());

        // JavaScript Bridge — اپ Native به وب می‌گوید در اپ هستیم
        webView.addJavascriptInterface(new NativeBridge(), "PostyarNative");
    }

    private void setupSwipeRefresh() {
        swipeRefreshLayout.setColorSchemeResources(
                R.color.color_primary,
                R.color.color_accent
        );
        swipeRefreshLayout.setProgressBackgroundColorSchemeColor(0xFF0A0A0A);
        swipeRefreshLayout.setOnRefreshListener(() -> {
            if (hasLoadedOnce) {
                webView.reload();
            }
            swipeRefreshLayout.setRefreshing(false);
        });
    }

    private void loadApp() {
        if (isNetworkAvailable()) {
            // نمایش Splash و بارگذاری داشبورد
            showSplash();
            webView.loadUrl(BuildConfig.DASHBOARD_URL);
        } else {
            showErrorPage();
        }
    }

    // ─── WebViewClient ──────────────────────────────────────────

    private class PostyarWebViewClient extends WebViewClient {

        @Override
        public void onPageStarted(WebView view, String url, Bitmap favicon) {
            super.onPageStarted(view, url, favicon);
            progressBar.setVisibility(View.VISIBLE);
            progressBar.setProgress(0);

            // غیرفعال کردن Pull-to-Refresh هنگام لود
            swipeRefreshLayout.setEnabled(false);
        }

        @Override
        public void onPageFinished(WebView view, String url) {
            super.onPageFinished(view, url);
            progressBar.setVisibility(View.GONE);
            swipeRefreshLayout.setEnabled(true);

            // مخفی کردن Splash بعد از اولین لود موفق
            if (isSplashVisible && url.contains(BuildConfig.APP_URL)) {
                hideSplash();
                hasLoadedOnce = true;
            }

            // Inject JS: اطلاع‌رسانی به وب‌سایت که در اپ هستیم
            view.evaluateJavascript("window.__isNativeApp = true;", null);
        }

        @Override
        public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest request) {
            String url = request.getUrl().toString();
            String appHost = Uri.parse(BuildConfig.APP_URL).getHost();
            String requestHost = request.getUrl().getHost();

            // لینک‌های داخلی → WebView
            if (requestHost != null && requestHost.equals(appHost)) {
                return false;
            }

            // لینک‌های تلگرام/بله → باز شدن در اپ مربوطه
            if (url.startsWith("https://t.me/") || url.startsWith("tg://")) {
                try {
                    Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse(url));
                    startActivity(intent);
                } catch (Exception ignored) {}
                return true;
            }

            // سایر لینک‌های خارجی → Chrome Custom Tabs
            try {
                Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse(url));
                startActivity(intent);
            } catch (Exception ignored) {}
            return true;
        }

        @Override
        public void onReceivedError(WebView view, WebResourceRequest request, WebResourceError error) {
            if (request.isForMainFrame()) {
                showErrorPage();
            }
        }

        @Override
        public void onReceivedHttpError(WebView view, WebResourceRequest request, WebResourceResponse errorResponse) {
            super.onReceivedHttpError(view, request, errorResponse);
            // فقط خطای اصلی صفحه (نه منابع فرعی)
            if (request.isForMainFrame() && errorResponse != null) {
                int code = errorResponse.getStatusCode();
                if (code == 404 || code >= 500) {
                    showErrorPage();
                }
            }
        }

    }

    // ─── WebChromeClient ────────────────────────────────────────

    private class PostyarChromeClient extends WebChromeClient {

        @Override
        public void onProgressChanged(WebView view, int newProgress) {
            progressBar.setProgress(newProgress);
            if (newProgress >= 100) {
                progressBar.setVisibility(View.GONE);
            }
        }

        @Override
        public boolean onShowFileChooser(WebView webView, ValueCallback<Uri[]> filePathCallback,
                                           FileChooserParams fileChooserParams) {
            if (uploadMessage != null) {
                uploadMessage.onReceiveValue(null);
            }
            uploadMessage = filePathCallback;

            Intent intent = fileChooserParams.createIntent();
            try {
                startActivityForResult(intent, FILECHOOSER_RESULTCODE);
            } catch (Exception e) {
                uploadMessage = null;
                return false;
            }
            return true;
        }

        @Override
        public void onReceivedTitle(WebView view, String title) {
            // آپدیت Title اکتیویتی بر اساس صفحه
            if (title != null && !title.isEmpty() && !isSplashVisible) {
                setTitle(title);
            }
        }
    }

    // ─── Splash Screen ──────────────────────────────────────────

    private void showSplash() {
        if (!isSplashVisible) return;
        webView.loadDataWithBaseURL(
                BuildConfig.APP_URL + "/",
                SPLASH_HTML,
                "text/html",
                "UTF-8",
                null
        );
        isSplashVisible = true;
    }

    private void hideSplash() {
        isSplashVisible = false;
        webView.stopLoading();
        // لود واقعی داشبورد
        webView.loadUrl(BuildConfig.DASHBOARD_URL);
    }

    // ─── Error Pages ────────────────────────────────────────────

    private void showErrorPage() {
        if (!isNetworkAvailable()) {
            webView.loadDataWithBaseURL(null, OFFLINE_HTML, "text/html", "UTF-8", null);
        } else {
            webView.loadDataWithBaseURL(null, ERROR_HTML, "text/html", "UTF-8", null);
        }
        swipeRefreshLayout.setEnabled(true);
        progressBar.setVisibility(View.GONE);
    }

    // ─── Network ────────────────────────────────────────────────

    @SuppressWarnings("deprecation")
    private boolean isNetworkAvailable() {
        ConnectivityManager cm = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
        if (cm == null) return false;

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            android.net.Network network = cm.getActiveNetwork();
            if (network == null) return false;
            NetworkCapabilities caps = cm.getNetworkCapabilities(network);
            return caps != null && (
                    caps.hasTransport(NetworkCapabilities.TRANSPORT_WIFI) ||
                    caps.hasTransport(NetworkCapabilities.TRANSPORT_CELLULAR) ||
                    caps.hasTransport(NetworkCapabilities.TRANSPORT_ETHERNET)
            );
        } else {
            android.net.NetworkInfo info = cm.getActiveNetworkInfo();
            return info != null && info.isConnectedOrConnecting();
        }
    }

    // ─── Share Target ───────────────────────────────────────────

    private void handleIncomingIntent(Intent intent) {
        if (intent == null) return;
        String action = intent.getAction();
        String type = intent.getType();

        if (Intent.ACTION_SEND.equals(action) && type != null) {
            // محتوای Share شده ذخیره و بعد از لود داشبورد به آن پاس می‌دهیم
            String sharedText = intent.getStringExtra(Intent.EXTRA_TEXT);
            if (sharedText != null) {
                // ذخیره برای ارسال به WebView بعد از لود
                getSharedPreferences("postyar_prefs", MODE_PRIVATE)
                        .edit().putString("pending_share", sharedText).apply();
            }
        }
    }

    // ─── Activity Lifecycle ─────────────────────────────────────

    @Override
    protected void onActivityResult(int requestCode, int resultCode, @Nullable Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (requestCode == FILECHOOSER_RESULTCODE) {
            if (uploadMessage == null) return;
            Uri[] results = null;
            if (resultCode == Activity.RESULT_OK && data != null) {
                String dataString = data.getDataString();
                if (dataString != null) {
                    results = new Uri[]{Uri.parse(dataString)};
                } else if (data.getClipData() != null) {
                    int count = data.getClipData().getItemCount();
                    results = new Uri[count];
                    for (int i = 0; i < count; i++) {
                        results[i] = data.getClipData().getItemAt(i).getUri();
                    }
                }
            }
            uploadMessage.onReceiveValue(results);
            uploadMessage = null;
        }
    }

    @Override
    protected void onNewIntent(Intent intent) {
        super.onNewIntent(intent);
        handleIncomingIntent(intent);
    }

    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        // دکمه بازگشت → بک در WebView
        if (keyCode == KeyEvent.KEYCODE_BACK) {
            // اگر در صفحه خطا هستیم → بستن اپ
            String currentUrl = webView.getUrl();
            if (currentUrl == null || !currentUrl.contains(Uri.parse(BuildConfig.APP_URL).getHost())) {
                if (webView.canGoBack()) {
                    webView.goBack();
                } else {
                    loadApp();
                }
                return true;
            }

            // اگر در صفحه اصلی وب‌سایت هستیم → بستن اپ
            if (webView.canGoBack()) {
                String previousUrl = webView.copyBackForwardList().getItemAtIndex(
                        webView.copyBackForwardList().getCurrentIndex() - 1
                ).getUrl();

                // اگر به صفحه لاگین یا صفحه اصلی برگشت → خروج
                if (previousUrl.equals(BuildConfig.APP_URL + "/") ||
                        previousUrl.equals(BuildConfig.APP_URL) ||
                        (previousUrl.contains("/login") && !webView.canGoBackOrForward(-2))) {
                    moveTaskToBack(true);
                    return true;
                }

                webView.goBack();
                return true;
            }

            // در صفحه اصلی داشبورد → خروج به پس‌زمینه
            moveTaskToBack(true);
            return true;
        }
        return super.onKeyDown(keyCode, event);
    }

    @Override
    protected void onResume() {
        super.onResume();
        webView.onResume();
    }

    @Override
    protected void onPause() {
        super.onPause();
        webView.onPause();
    }

    @Override
    protected void onDestroy() {
        webView.destroy();
        super.onDestroy();
    }

    // ─── JavaScript Bridge ──────────────────────────────────────

    public class NativeBridge {
        @android.webkit.JavascriptInterface
        public String getAppVersion() {
            return BuildConfig.VERSION_NAME;
        }

        @android.webkit.JavascriptInterface
        public boolean isNativeApp() {
            return true;
        }

        @android.webkit.JavascriptInterface
        public void shareText(String text) {
            Intent intent = new Intent(Intent.ACTION_SEND);
            intent.setType("text/plain");
            intent.putExtra(Intent.EXTRA_TEXT, text);
            startActivity(Intent.createChooser(intent, "اشتراک‌گذاری"));
        }
    }

    // ─── HTML Templates ─────────────────────────────────────────

    private static String buildSplashHtml() {
        return "<!DOCTYPE html><html dir='rtl'><head><meta charset='UTF-8'>" +
                "<meta name='viewport' content='width=device-width,initial-scale=1,maximum-scale=1'>" +
                "<style>*{margin:0;padding:0;box-sizing:border-box}" +
                "body{background:#0a0a0a;display:flex;align-items:center;justify-content:center;" +
                "min-height:100vh;font-family:Tahoma,Arial,sans-serif}" +
                ".splash{display:flex;flex-direction:column;align-items:center;gap:1.5rem}" +
                ".logo-wrap{width:90px;height:90px;border-radius:22px;padding:3px;" +
                "background:linear-gradient(135deg,#6366f1 0%,#a855f7 50%,#f97316 100%)}" +
                ".logo-inner{width:100%;height:100%;border-radius:19px;background:#0a0a0a;" +
                "display:flex;align-items:center;justify-content:center;overflow:hidden}" +
                ".logo-inner img{width:80%;height:80%;object-fit:contain}" +
                ".app-name{color:#f1f5f9;font-size:1.4rem;font-weight:900;letter-spacing:-0.5px}" +
                ".tagline{color:#64748b;font-size:0.8rem}" +
                ".loader{width:36px;height:36px;border:3px solid #1e293b;border-top-color:#6366f1;" +
                "border-radius:50%;animation:spin 0.8s linear infinite}" +
                "@keyframes spin{to{transform:rotate(360deg)}}" +
                "</style></head><body><div class='splash'>" +
                "<div class='logo-wrap'><div class='logo-inner'>" +
                "<img src='" + BuildConfig.APP_URL + "/assets/images/logo-white-bg.webp' alt='پُست‌یار'>" +
                "</div></div>" +
                "<div class='app-name'>پُست‌یار</div>" +
                "<div class='tagline'>سامانه هوشمند مدیریت کانال‌ها</div>" +
                "<div class='loader'></div>" +
                "</div></body></html>";
    }

    private static String buildOfflineHtml() {
        return "<!DOCTYPE html><html dir='rtl'><head><meta charset='UTF-8'>" +
                "<meta name='viewport' content='width=device-width,initial-scale=1'>" +
                "<style>*{margin:0;padding:0;box-sizing:border-box}" +
                "body{background:#0f172a;display:flex;align-items:center;justify-content:center;" +
                "min-height:100vh;font-family:Tahoma,Arial,sans-serif;padding:2rem}" +
                ".box{text-align:center;max-width:320px}" +
                ".icon{font-size:3.5rem;margin-bottom:1.5rem}" +
                "h1{color:#f1f5f9;font-size:1.3rem;margin-bottom:0.75rem;font-weight:700}" +
                "p{color:#94a3b8;font-size:0.9rem;line-height:1.8;margin-bottom:1.5rem}" +
                "button{background:linear-gradient(135deg,#6366f1,#4f46e5);color:white;border:none;" +
                "padding:0.75rem 2rem;border-radius:12px;font-size:0.95rem;font-family:inherit;" +
                "cursor:pointer;width:100%;font-weight:600}" +
                "</style></head><body><div class='box'>" +
                "<div class='icon'>🔌</div>" +
                "<h1>ارتباط با سرور برقرار نشد</h1>" +
                "<p>لطفاً اتصال اینترنت خود را بررسی کنید و دوباره تلاش نمایید.</p>" +
                "<button onclick='location.reload()'>تلاش مجدد</button>" +
                "</div></body></html>";
    }

    private static String buildErrorHtml() {
        return "<!DOCTYPE html><html dir='rtl'><head><meta charset='UTF-8'>" +
                "<meta name='viewport' content='width=device-width,initial-scale=1'>" +
                "<style>*{margin:0;padding:0;box-sizing:border-box}" +
                "body{background:#0f172a;display:flex;align-items:center;justify-content:center;" +
                "min-height:100vh;font-family:Tahoma,Arial,sans-serif;padding:2rem}" +
                ".box{text-align:center;max-width:320px}" +
                ".icon{font-size:3.5rem;margin-bottom:1.5rem}" +
                "h1{color:#f1f5f9;font-size:1.3rem;margin-bottom:0.75rem;font-weight:700}" +
                "p{color:#94a3b8;font-size:0.9rem;line-height:1.8;margin-bottom:1.5rem}" +
                "button{background:linear-gradient(135deg,#6366f1,#4f46e5);color:white;border:none;" +
                "padding:0.75rem 2rem;border-radius:12px;font-size:0.95rem;font-family:inherit;" +
                "cursor:pointer;width:100%;font-weight:600}" +
                "</style></head><body><div class='box'>" +
                "<div class='icon'>⚠️</div>" +
                "<h1>خطا در بارگذاری</h1>" +
                "<p>مشکلی در ارتباط با سرور پیش آمده است. لطفاً دوباره تلاش کنید.</p>" +
                "<button onclick='location.reload()'>تلاش مجدد</button>" +
                "</div></body></html>";
    }
}
