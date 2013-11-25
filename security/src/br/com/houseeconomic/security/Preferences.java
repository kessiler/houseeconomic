package br.com.houseeconomic.security;

import android.content.Intent;
import android.os.Bundle;
import android.app.Activity;
import android.view.KeyEvent;


public class Preferences extends Activity {
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        if(savedInstanceState == null) {
            getFragmentManager().beginTransaction().add(android.R.id.content, new PreferencesFragment()).commit();
        }
    }

    @Override
    public boolean onKeyDown(int keyCode, KeyEvent keyEvent) {
        if(keyCode == KeyEvent.KEYCODE_BACK) {
            startActivity(new Intent(this, SecurityActivity.class));
            finish();
        }
        return super.onKeyDown(keyCode, keyEvent);
    }
}