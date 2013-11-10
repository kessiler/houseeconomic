package br.com.houseeconomic.security;

import android.content.Context;
import android.content.Intent;
import android.telephony.PhoneStateListener;
import android.telephony.TelephonyManager;
import android.util.Log;

public class TelephonyListen extends PhoneStateListener {

    private Context baseContext;
    private boolean phoneCalling = false;
    private final String TAG = "CALL_PHONE";

    public TelephonyListen(Context app) {
        this.baseContext = app;
    }

    @Override
    public void onCallStateChanged(int state, String incomingNumber) {

        if (TelephonyManager.CALL_STATE_RINGING == state) {
            // phone ringing
            Log.i(TAG, "RINGING, number: " + incomingNumber);
        }

        if (TelephonyManager.CALL_STATE_OFFHOOK == state) {
            // active
            Log.i(TAG, "OFFHOOK");

            phoneCalling = true;
        }

        // When the call ends launch the main activity again
        if (TelephonyManager.CALL_STATE_IDLE == state) {
            Log.i(TAG, "IDLE");

            if (phoneCalling) {
                Log.i(TAG, "restart app");
                Intent appSecurity = this.baseContext.getPackageManager().getLaunchIntentForPackage(this.baseContext.getPackageName());
                appSecurity.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                this.baseContext.startActivity(appSecurity);
                phoneCalling = false;
            }

        }
    }

}
