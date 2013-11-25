package br.com.houseeconomic.security;

import android.app.Activity;
import android.app.Fragment;
import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Bundle;
import android.telephony.TelephonyManager;
import android.util.Log;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.Toast;
import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.URL;
import java.net.URLConnection;

public class SecurityActivity extends Activity {
    /**
     * Called when the activity is first created.
     */
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.main);
        Util.setAppContext(this);
        Button callPolice = (Button)findViewById(R.id.call);
        Button searchMap = (Button)findViewById(R.id.searchMap);
        Button disableAlarm = (Button)findViewById(R.id.disableAlarm);
        TelephonyListen phoneListener = new TelephonyListen(getBaseContext());
        TelephonyManager telManager = (TelephonyManager)this.getSystemService(Context.TELEPHONY_SERVICE);
        telManager.listen(phoneListener, TelephonyListen.LISTEN_CALL_STATE);
        callPolice.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent callPolice = new Intent(Intent.ACTION_CALL);
                callPolice.setData(Uri.parse("tel:190"));
                startActivity(callPolice);
            }
        });
        searchMap.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent mapsIntent = new Intent(SecurityActivity.this, MapsActivity.class);
                startActivity(mapsIntent);
            }
        });
        disableAlarm.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (!Util.verifyConnection()) {
                    Toast.makeText(Util.getAppContext(), "Não há conexão WIFI / 3G habilitada.", Toast.LENGTH_LONG).show();
                    finish();
                } else {
                    new ActionAlarm().execute();
                }
            }
        });
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
                finish();
                return true;
            default:
                return super.onOptionsItemSelected(Item);
        }
    }

    private class ActionAlarm extends AsyncTask<String, String, String> {
        @Override
        protected String doInBackground(String... arg0) {
            disableAlarm();
            return null;
        }
        @Override
        protected void onPostExecute(String result) {
            super.onPostExecute(result);
            Toast.makeText(Util.getAppContext(), "Alarme desligado com sucesso.", Toast.LENGTH_LONG).show();
        }
    }

    public void disableAlarm() {
        String url = Util.getURL();
        if(!url.endsWith("/")) {
            url = url + "/";
        }
        try {
            HttpClient httpclient = new DefaultHttpClient();
            httpclient.execute(new HttpGet(url+ "?AlarmOFF"));
        } catch (Exception e) {
            Log.d("[GET REQUEST]", "Network exception", e);
        }
    }
}
