package ir.asovin.postyar;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Context;
import android.content.Intent;
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

import androidx.annotation.Nullable;
import androidx.appcompat.app.AppCompatActivity;

/**
 * اکتیویتی اصلی اپلیکیشن پُست‌یار
 */
public class MainActivity extends AppCompatActivity {

    private static final String TAG = "Postyar";
    private static final int FILECHOOSER_RESULTCODE = 1001;

    private WebView webView;
    private ProgressBar progressBar;
    private ValueCallback<Uri[]> uploadMessage;
    private boolean isSplashVisible = true;
    private boolean hasLoadedOnce = false;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        if (getSupportActionBar() != null) getSupportActionBar().hide();
        setupStatusBar();
        setContentView(R.layout.activity_main);

        webView = findViewById(R.id.webview);
        progressBar = findViewById(R.id.progress_bar);

        setupWebView();
        handleIncomingIntent(getIntent());
        loadApp();
    }

    private void setupStatusBar() {
        Window window = getWindow();
        window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
        window.setStatusBarColor(0xFF0A0A0A);
        window.setNavigationBarColor(0xFF0A0A0A);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            window.getDecorView().setSystemUiVisibility(
                    View.SYSTEM_UI_FLAG_LIGHT_NAVIGATION_BAR
            );
        }
    }

    @SuppressLint("SetJavaScriptEnabled")
    private void setupWebView() {
        WebSettings settings = webView.getSettings();

        settings.setJavaScriptEnabled(true);
        settings.setDomStorageEnabled(true);
        settings.setDatabaseEnabled(true);
        settings.setCacheMode(WebSettings.LOAD_DEFAULT);
        settings.setLoadWithOverviewMode(true);
        settings.setUseWideViewPort(true);
        settings.setSupportZoom(false);
        settings.setAllowFileAccess(false);
        settings.setAllowContentAccess(false);
        settings.setMixedContentMode(WebSettings.MIXED_CONTENT_NEVER_ALLOW);
        CookieManager.getInstance().setAcceptThirdPartyCookies(webView, true);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
            settings.setSafeBrowsingEnabled(true);
        }
        settings.setTextZoom(100);

        webView.setWebViewClient(new PostyarWebViewClient());
        webView.setWebChromeClient(new PostyarChromeClient());
        webView.addJavascriptInterface(new NativeBridge(), "PostyarNative");
    }

    private void loadApp() {
        if (isNetworkAvailable()) {
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
        }

        @Override
        public void onPageFinished(WebView view, String url) {
            super.onPageFinished(view, url);
            progressBar.setVisibility(View.GONE);

            if (isSplashVisible && url.contains(Uri.parse(BuildConfig.APP_URL).getHost())) {
                hideSplash();
                hasLoadedOnce = true;
            }

            view.evaluateJavascript("window.__isNativeApp = true;", null);
        }

        @Override
        public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest request) {
            String url = request.getUrl().toString();
            String appHost = Uri.parse(BuildConfig.APP_URL).getHost();
            String requestHost = request.getUrl().getHost();

            if (requestHost != null && requestHost.equals(appHost)) {
                return false;
            }

            if (url.startsWith("https://t.me/") || url.startsWith("tg://")) {
                openExternalUrl(url);
                return true;
            }

            openExternalUrl(url);
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
    }

    private void hideSplash() {
        isSplashVisible = false;
        webView.stopLoading();
        webView.loadUrl(BuildConfig.DASHBOARD_URL);
    }

    // ─── Error Pages ────────────────────────────────────────────

    private void showErrorPage() {
        if (!isNetworkAvailable()) {
            webView.loadDataWithBaseURL(null, OFFLINE_HTML, "text/html", "UTF-8", null);
        } else {
            webView.loadDataWithBaseURL(null, ERROR_HTML, "text/html", "UTF-8", null);
        }
        progressBar.setVisibility(View.GONE);
    }

    // ─── Helpers ────────────────────────────────────────────────

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

    private void openExternalUrl(String url) {
        try {
            Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse(url));
            startActivity(intent);
        } catch (Exception ignored) {}
    }

    private void handleIncomingIntent(Intent intent) {
        if (intent == null) return;
        String action = intent.getAction();
        String type = intent.getType();
        if (Intent.ACTION_SEND.equals(action) && type != null) {
            String sharedText = intent.getStringExtra(Intent.EXTRA_TEXT);
            if (sharedText != null) {
                getSharedPreferences("postyar_prefs", MODE_PRIVATE)
                        .edit().putString("pending_share", sharedText).apply();
            }
        }
    }

    // ─── Lifecycle ──────────────────────────────────────────────

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
        if (keyCode == KeyEvent.KEYCODE_BACK) {
            String currentUrl = webView.getUrl();
            if (currentUrl == null || !currentUrl.contains(Uri.parse(BuildConfig.APP_URL).getHost())) {
                if (webView.canGoBack()) {
                    webView.goBack();
                } else {
                    loadApp();
                }
                return true;
            }

            if (webView.canGoBack()) {
                String previousUrl = webView.copyBackForwardList().getItemAtIndex(
                        webView.copyBackForwardList().getCurrentIndex() - 1
                ).getUrl();

                if (previousUrl.equals(BuildConfig.APP_URL + "/") ||
                        previousUrl.equals(BuildConfig.APP_URL) ||
                        (previousUrl.contains("/login") && !webView.canGoBackOrForward(-2))) {
                    moveTaskToBack(true);
                    return true;
                }

                webView.goBack();
                return true;
            }

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

    private static final String SPLASH_HTML = "<!DOCTYPE html><html dir='rtl'><head><meta charset='UTF-8'>" +
            "<meta name='viewport' content='width=device-width,initial-scale=1,maximum-scale=1'>" +
            "<style>*{margin:0;padding:0;box-sizing:border-box}" +
            "body{background:#0a0a0a;display:flex;align-items:center;justify-content:center;min-height:100vh;font-family:Tahoma,Arial,sans-serif}" +
            ".splash{display:flex;flex-direction:column;align-items:center;gap:1.5rem}" +
            ".app-name{color:#f1f5f9;font-size:1.4rem;font-weight:900;letter-spacing:-0.5px}" +
            ".tagline{color:#64748b;font-size:0.8rem}" +
            ".loader{width:36px;height:36px;border:3px solid #1e293b;border-top-color:#6366f1;border-radius:50%;animation:spin 0.8s linear infinite}" +
            "@keyframes spin{to{transform:rotate(360deg)}}" +
            "</style></head><body><div class='splash'>" +
            "<div class='app-name'>پُست‌یار</div>" +
            "<div class='tagline'>سامانه هوشمند مدیریت کانال‌ها</div>" +
            "<div class='loader'></div>" +
            "</div></body></html>";

    private static final String OFFLINE_HTML = "<!DOCTYPE html><html dir='rtl'><head><meta charset='UTF-8'>" +
            "<meta name='viewport' content='width=device-width,initial-scale=1'>" +
            "<style>*{margin:0;padding:0;box-sizing:border-box}" +
            "body{background:#0f172a;display:flex;align-items:center;justify-content:center;min-height:100vh;font-family:Tahoma,Arial,sans-serif;padding:2rem}" +
            ".box{text-align:center;max-width:320px}" +
            ".icon{font-size:3.5rem;margin-bottom:1.5rem}" +
            "h1{color:#f1f5f9;font-size:1.3rem;margin-bottom:0.75rem;font-weight:700}" +
            "p{color:#94a3b8;font-size:0.9rem;line-height:1.8;margin-bottom:1.5rem}" +
            "button{background:#6366f1;color:white;border:none;padding:0.75rem 2rem;border-radius:12px;font-size:0.95rem;font-family:inherit;cursor:pointer;width:100%;font-weight:600}" +
            "</style></head><body><div class='box'>" +
            "<div class='icon'>&#128268;</div>" +
            "<h1>ارتباط با سرور برقرار نشد</h1>" +
            "<p>لطفا اتصال اینترنت خود را بررسی کنید و دوباره تلاش نمایید.</p>" +
            "<button onclick='location.reload()'>تلاش مجدد</button>" +
            "</div></body></html>";

    private static final String ERROR_HTML = "<!DOCTYPE html><html dir='rtl'><head><meta charset='UTF-8'>" +
            "<meta name='viewport' content='width=device-width,initial-scale=1'>" +
            "<style>*{margin:0;padding:0;box-sizing:border-box}" +
            "body{background:#0f172a;display:flex;align-items:center;justify-content:center;min-height:100vh;font-family:Tahoma,Arial,sans-serif;padding:2rem}" +
            ".box{text-align:center;max-width:320px}" +
            ".icon{font-size:3.5rem;margin-bottom:1.5rem}" +
            "h1{color:#f1f5f9;font-size:1.3rem;margin-bottom:0.75rem;font-weight:700}" +
            "p{color:#94a3b8;font-size:0.9rem;line-height:1.8;margin-bottom:1.5rem}" +
            "button{background:#6366f1;color:white;border:none;padding:0.75rem 2rem;border-radius:12px;font-size:0.95rem;font-family:inherit;cursor:pointer;width:100%;font-weight:600}" +
            "</style></head><body><div class='box'>" +
            "<div class='icon'>&#9888;</div>" +
            "<h1>خطا در بارگذاری</h1>" +
            "<p>مشکلی در ارتباط با سرور پیش آمده است.</p>" +
            "<button onclick='location.reload()'>تلاش مجدد</button>" +
            "</div></body></html>";
}
