package edu.uml.cs.obd.driving.activity;

import android.bluetooth.BluetoothAdapter;
import android.hardware.Sensor;
import android.os.Bundle;
import android.os.Handler;
import android.os.PowerManager;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.widget.TextView;

import com.github.pires.obd.enums.AvailableCommandNames;
import edu.uml.cs.obd.driving.R;

import java.util.HashMap;
import java.util.Map;

import roboguice.RoboGuice;
import roboguice.activity.RoboActivity;
import roboguice.inject.ContentView;

@ContentView(R.layout.main)
public class MainActivity extends RoboActivity {

    private static final String TAG = MainActivity.class.getName();
    private static final int START_LIVE_DATA = 1;
    private static final int STOP_LIVE_DATA = 2;
    private static final int SETTINGS = 3;
    private static final int GET_DTC = 4;
    private static final int TRIPS_LIST = 5;
    private static boolean bluetoothDefaultIsEnable = false;

    static {
        RoboGuice.setUseAnnotationDatabases(false);
    }

    public Map<String, String> commandResult = new HashMap<String, String>();

    private Sensor orientSensor = null;
    private PowerManager.WakeLock wakeLock = null;
    private boolean preRequisites = true;

    public static String LookUpCommand(String txt) {
        for (AvailableCommandNames item : AvailableCommandNames.values()) {
            if (item.getValue().equals(txt)) return item.name();
        }
        return txt;
    }

    public void updateTextView(final TextView view, final String txt) {
        new Handler().post(new Runnable() {
            public void run() {
                view.setText(txt);
            }
        });
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        final BluetoothAdapter btAdapter = BluetoothAdapter.getDefaultAdapter();
        if (btAdapter != null)
            bluetoothDefaultIsEnable = btAdapter.isEnabled();
    }

    @Override
    protected void onStart() {
        super.onStart();
        Log.d(TAG, "Entered onStart...");
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();

        releaseWakeLockIfHeld();

        final BluetoothAdapter btAdapter = BluetoothAdapter.getDefaultAdapter();
        if (btAdapter != null && btAdapter.isEnabled() && !bluetoothDefaultIsEnable)
            btAdapter.disable();
    }

    @Override
    protected void onPause() {
        super.onPause();
        Log.d(TAG, "Pausing..");
        releaseWakeLockIfHeld();
    }

    private void releaseWakeLockIfHeld() {
        if (wakeLock.isHeld())
            wakeLock.release();
    }

    protected void onResume() {
        super.onResume();

        final BluetoothAdapter btAdapter = BluetoothAdapter
                .getDefaultAdapter();

        // // TODO: 4/2/16
    }

    public boolean onCreateOptionsMenu(Menu menu) {
        menu.add(0, START_LIVE_DATA, 0, getString(R.string.menu_start_live_data));
        menu.add(0, STOP_LIVE_DATA, 0, getString(R.string.menu_stop_live_data));
        menu.add(0, GET_DTC, 0, getString(R.string.menu_get_dtc));
        menu.add(0, TRIPS_LIST, 0, getString(R.string.menu_trip_list));
        menu.add(0, SETTINGS, 0, getString(R.string.menu_settings));
        return true;
    }

    public boolean onOptionsItemSelected(MenuItem item) {
        if (item.getItemId() == START_LIVE_DATA) {
            startLiveData();
        }
        return false;
    }


    private void startLiveData() {
        Log.d(TAG, "Starting live data..");
        wakeLock.acquire();
    }

}
