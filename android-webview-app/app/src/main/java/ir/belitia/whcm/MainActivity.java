package ir.belitia.whcm;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.net.Uri;
import android.os.Bundle;
import android.view.KeyEvent;
import android.webkit.ValueCallback;
import android.webkit.WebChromeClient;
import android.webkit.WebResourceRequest;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.net.http.SslCertificate;
import android.net.http.SslError;
import android.webkit.SslErrorHandler;
import androidx.appcompat.app.AppCompatActivity;
import ir.belitia.whcm.BuildConfig;

/**
 * رپر اندرویدی نیتیو (WebView Client) جهت اجرای وب‌اپلیکیشن SaaS پُست‌یار
 *
 * شامل: هندل دکمه بازگشت فیزیکی، شتاب‌دهنده سخت‌افزاری، آپلود فایل،
 * مدیریت خطای شبکه، صفحه آفلاین سفارشی و اعتبارسنجی SSL
 */
public class MainActivity extends AppCompatActivity {

    private static final String APP_URL = BuildConfig.APP_URL;
    private static final String OFFLINE_HTML =
        "<html dir='rtl'><head><meta charset='UTF-8'>" +
        "<meta name='viewport' content='width=device-width, initial-scale=1.0'>" +
        "<style>body{font-family:Tahoma,Arial,sans-serif;display:flex;" +
        "align-items:center;justify-content:center;min-height:100vh;margin:0;" +
        "background:#0f172a;color:#f1f5f9;text-align:center;}" +
        ".box{padding:2rem;}h1{color:#6366f1;font-size:2rem;}" +
        "p{color:#94a3b8;line-height:1.8;}button{background:#6366f1;color:#fff;" +
        "border:none;padding:0.75rem 2rem;border-radius:8px;font-size:1rem;" +
        "cursor:pointer;margin-top:1rem;}</style></head><body>" +
        "<div class='box'><h1>🔌 ارتباط با سرور برقرار نشد</h1>" +
        "<p>لطفاً اتصال اینترنت خود را بررسی کرده و مجدداً تلاش کنید.</p>" +
        "<button onclick='location.reload()'>تلاش مجدد 🔄</button></div>" +
        "</body></html>";

    private WebView mWebView;
    private ValueCallback<Uri[]> mUploadMessage;
    private final static int FILECHOOSER_RESULTCODE = 1;
    private boolean isErrorPageShown = false;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        mWebView = new WebView(this);
        setContentView(mWebView);

        WebSettings webSettings = mWebView.getSettings();
        webSettings.setJavaScriptEnabled(true);
        webSettings.setDomStorageEnabled(true);
        webSettings.setDatabaseEnabled(true);
        // امنیت: غیرفعال‌سازی دسترسی فایل برای جلوگیری از XSS
        webSettings.setAllowFileAccess(false);
        webSettings.setAllowContentAccess(false);
        webSettings.setLoadWithOverviewMode(true);
        webSettings.setUseWideViewPort(true);
        webSettings.setCacheMode(WebSettings.LOAD_DEFAULT);
        // فعال‌سازی Mixed Content Mode — فقط HTTPS
        webSettings.setMixedContentMode(WebSettings.MIXED_CONTENT_NEVER_ALLOW);

        mWebView.setWebViewClient(new WebViewClient() {
            @Override
            public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest request) {
                // فقط URLهای HTTPS مجاز هستند
                String url = request.getUrl().toString();
                if (url.startsWith("https://") || url.startsWith(BuildConfig.APP_URL)) {
                    view.loadUrl(url);
                    return true;
                }
                return true; // مسدود کردن URLهای غیرمجاز
            }

            @Override
            public void onReceivedSslError(WebView view, SslErrorHandler handler, SslError error) {
                // امنیت: هرگز SSL errors را نادیده نگیر
                handler.cancel();
            }

            @Override
            public void onReceivedError(WebView view, WebResourceRequest request, android.webkit.WebResourceError error) {
                // فقط برای درخواست اصلی (نه منابع فرعی مثل CSS/JS)
                if (request.isForMainFrame()) {
                    isErrorPageShown = true;
                    view.loadDataWithBaseURL("about:blank", OFFLINE_HTML, "text/html", "UTF-8", null);
                }
            }
        });

        mWebView.setWebChromeClient(new WebChromeClient() {
            @Override
            public boolean onShowFileChooser(WebView webView, ValueCallback<Uri[]> filePathCallback, FileChooserParams fileChooserParams) {
                if (mUploadMessage != null) {
                    mUploadMessage.onReceiveValue(null);
                }
                mUploadMessage = filePathCallback;

                Intent intent = fileChooserParams.createIntent();
                try {
                    startActivityForResult(intent, FILECHOOSER_RESULTCODE);
                } catch (Exception e) {
                    mUploadMessage = null;
                    return false;
                }
                return true;
            }
        });

        // بررسی اتصال اینترنت قبل از بارگذاری
        if (isNetworkAvailable()) {
            mWebView.loadUrl(APP_URL);
        } else {
            isErrorPageShown = true;
            mWebView.loadDataWithBaseURL("about:blank", OFFLINE_HTML, "text/html", "UTF-8", null);
        }
    }

    /**
     * بررسی در دسترس بودن اتصال اینترنت
     */
    @SuppressWarnings("deprecation")
    private boolean isNetworkAvailable() {
        ConnectivityManager cm = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
        if (cm == null) return false;
        android.net.Network network = cm.getActiveNetwork();
        if (network == null) {
            NetworkInfo activeNetwork = cm.getActiveNetworkInfo();
            return activeNetwork != null && activeNetwork.isConnectedOrConnecting();
        }
        android.net.NetworkCapabilities caps = cm.getNetworkCapabilities(network);
        return caps != null && (caps.hasTransport(android.net.NetworkCapabilities.TRANSPORT_WIFI) || caps.hasTransport(android.net.NetworkCapabilities.TRANSPORT_CELLULAR) || caps.hasTransport(android.net.NetworkCapabilities.TRANSPORT_ETHERNET));
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (requestCode == FILECHOOSER_RESULTCODE) {
            if (mUploadMessage == null) return;
            Uri[] results = null;
            if (resultCode == Activity.RESULT_OK) {
                if (data != null) {
                    String dataString = data.getDataString();
                    if (dataString != null) {
                        results = new Uri[]{Uri.parse(dataString)};
                    }
                }
            }
            mUploadMessage.onReceiveValue(results);
            mUploadMessage = null;
        }
    }

    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK && isErrorPageShown) {
            finish();
            return true;
        }
        if ((keyCode == KeyEvent.KEYCODE_BACK) && mWebView.canGoBack()) {
            mWebView.goBack();
            return true;
        }
        return super.onKeyDown(keyCode, event);
    }
}
