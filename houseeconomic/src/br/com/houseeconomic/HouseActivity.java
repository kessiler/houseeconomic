package br.com.houseeconomic;

import android.app.*;
import android.content.DialogInterface;
import android.content.Intent;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.provider.MediaStore;
import android.view.*;
import android.webkit.JavascriptInterface;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Toast;


public class HouseActivity extends Activity {
    /**
     * Called when the activity is first created.
     */
    private WebView webView;
    private ProgressDialog moduleLoading = null;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        setContentView(R.layout.main);
        Util.setAppContext(this);
        if(!Util.verifyConnection()) {
            Toast.makeText(Util.getAppContext(), "Não foi possível estabelecer conexão com o servidor.", Toast.LENGTH_LONG).show();
            finish();
        } else {
            this.webView = (WebView)findViewById(R.id.webView);
            if(Build.VERSION.SDK_INT >= Build.VERSION_CODES.HONEYCOMB)
                this.webView.setLayerType(View.LAYER_TYPE_SOFTWARE, null);
            this.webView.getSettings().setDomStorageEnabled(true);
            this.webView.getSettings().setAppCachePath("/data/data/"+ getPackageName() +"/cache");
            this.webView.getSettings().setAllowFileAccess(true);
            this.webView.getSettings().setAppCacheEnabled(true);
            this.webView.getSettings().setCacheMode(WebSettings.LOAD_DEFAULT);
            this.webView.getSettings().setUseWideViewPort(true);
            this.webView.getSettings().setSaveFormData(false);
            this.webView.getSettings().setJavaScriptEnabled(true);
            this.webView.getSettings().setSavePassword(false);
            this.webView.setVerticalScrollBarEnabled(false);
            this.webView.setHorizontalScrollBarEnabled(false);
            this.webView.addJavascriptInterface(new JsInterface(), "Mobile");
            this.webView.setLongClickable(false);
            this.webView.setWebViewClient(new WebViewClient() {
                @Override
                public boolean shouldOverrideUrlLoading(WebView view, String url)
                {
                    if (Uri.parse(url).getHost().equals(Uri.parse(Util.getURL()).getHost())) {
                        return false;
                    }
                    Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse(url));
                    startActivity(intent);
                    return true;
                }
                @Override
                public void onReceivedError(WebView view, int errorCode, String description, String failingUrl) {
                    view.stopLoading();
                    Toast.makeText(Util.getAppContext(), description, Toast.LENGTH_LONG).show();
                }
                public void onLoadResource (WebView view, String url) {
                    if (moduleLoading == null) {
                        moduleLoading = new ProgressDialog(Util.getAppContext());
                        moduleLoading.setIndeterminate(true);
                        moduleLoading.setCancelable(false);
                        moduleLoading.setMessage("Loading...");
                        moduleLoading.show();
                    }
                }

                public void onPageFinished(WebView view, String url) {
                    if (moduleLoading != null && moduleLoading.isShowing()) {
                        moduleLoading.dismiss();
                    }
                }
            });
            this.webView.loadUrl(Util.getURL());
        }
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        MenuInflater menuInflater = getMenuInflater();
        menuInflater.inflate(R.menu.menu, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem Item) {
        switch (Item.getItemId()) {
            case R.id.settings:
                startActivity(new Intent(this, Preferences.class));
                return true;
            case R.id.settingsReload:
                this.moduleLoading = null;
                this.webView.loadUrl(Util.getURL());
                this.webView.clearHistory();
                return true;
            default:
                return super.onOptionsItemSelected(Item);
        }
    }


    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event)
    {
        if ((keyCode == KeyEvent.KEYCODE_BACK) && webView.canGoBack()) {
            webView.goBack();
            return true;
        }
        return super.onKeyDown(keyCode, event);
    }

    public class JsInterface {
        @JavascriptInterface
        public void showLoading() {
            if(moduleLoading instanceof ProgressDialog) {
                moduleLoading.show();
            }
        }

        public void removeLoading() {
            if(moduleLoading instanceof ProgressDialog) {
                if(moduleLoading.isShowing()) moduleLoading.dismiss();
            }
        }

        public void showMessage(String message) {
            Toast.makeText(Util.getAppContext(), message, Toast.LENGTH_SHORT).show();
        }

        public void openCamera() {
            Intent intent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
            startActivityForResult(intent, 0);
        }
    }
}

