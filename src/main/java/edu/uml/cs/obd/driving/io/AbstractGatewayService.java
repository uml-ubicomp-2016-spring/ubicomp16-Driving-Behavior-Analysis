package edu.uml.cs.obd.driving.io;

import android.app.NotificationManager;
import android.content.Context;
import android.content.Intent;
import android.os.IBinder;
import android.util.Log;

import com.google.inject.Inject;

import java.util.concurrent.BlockingQueue;
import java.util.concurrent.LinkedBlockingQueue;

import roboguice.service.RoboService;


public abstract class AbstractGatewayService extends RoboService {
    public static final int NOTIFICATION_ID = 1;
    private static final String TAG = AbstractGatewayService.class.getName();
    @Inject
    protected NotificationManager notificationManager;
    protected Context ctx;
    protected boolean isRunning = false;
    protected Long queueCounter = 0L;
    protected BlockingQueue<ObdCommandJob> jobsQueue = new LinkedBlockingQueue<>();
    // Run the executeQueue in a different thread to lighten the UI thread
    Thread t = new Thread(new Runnable() {
        @Override
        public void run() {
            // TODO: 4/2/16
        }
    });

    @Override
    public IBinder onBind(Intent intent) {
        // // TODO: 4/2/16
        return null;
    }

    @Override
    public void onCreate() {
        super.onCreate();
        Log.d(TAG, "Creating service..");
        t.start();
        Log.d(TAG, "Service created.");
    }

    @Override
    public void onDestroy() {
        super.onDestroy();
        Log.d(TAG, "Destroying service...");
        notificationManager.cancel(NOTIFICATION_ID);
        t.interrupt();
        Log.d(TAG, "Service destroyed.");
    }
}
