package br.com.houseeconomic.security;

import android.app.Activity;
import android.app.Fragment;
import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.telephony.TelephonyManager;
import android.view.View;
import android.widget.Button;

public class SecurityActivity extends Activity {
    /**
     * Called when the activity is first created.
     */
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.main);
        Button callPolice = (Button)findViewById(R.id.call);
        Button searchMap = (Button)findViewById(R.id.searchMap);
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


    }
}
