package br.com.houseeconomic;

import android.net.ConnectivityManager;
import android.preference.PreferenceManager;
import android.content.Context;

public class Util {

    private static Context AppContext;

    public static String getURL() {
        return PreferenceManager.getDefaultSharedPreferences(Util.getAppContext()).getString("URL", "http://economichouse.orgfree.com");
    }

    public static Context getAppContext() {
        return AppContext;
    }
    public static void setAppContext(Context appContext) {
        AppContext = appContext;
    }

    public static boolean verifyConnection() {
        ConnectivityManager conectivtyManager = (ConnectivityManager)getAppContext().getSystemService(Context.CONNECTIVITY_SERVICE);
        return (conectivtyManager.getActiveNetworkInfo() != null
                && conectivtyManager.getActiveNetworkInfo().isAvailable()
                && conectivtyManager.getActiveNetworkInfo().isConnected());
    }
}
