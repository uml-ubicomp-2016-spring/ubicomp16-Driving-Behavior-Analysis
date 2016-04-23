package edu.uml.cs.obd.driving.io;

import com.loopj.android.http.AsyncHttpClient;
import com.loopj.android.http.AsyncHttpResponseHandler;
import com.loopj.android.http.RequestParams;

import java.io.File;
import java.io.FileNotFoundException;

public class FileUploadUtil {

    public String uploadFile(File file, String url) throws FileNotFoundException {

        final String[] uploadResult = {null};
        uploadResult[0] = "";
        AsyncHttpClient client = new AsyncHttpClient();
        RequestParams params = new RequestParams();
        params.put("file", file);
        client.post(url, params, new AsyncHttpResponseHandler() {

            @Override
            public void onSuccess(int statusCode, cz.msebera.android.httpclient.Header[] headers, byte[] responseBody) {
                //Toast.makeText(MainActivity.this, "successful", Toast.LENGTH_SHORT).show();
                uploadResult[0] = "success";
            }

            @Override
            public void onFailure(int statusCode, cz.msebera.android.httpclient.Header[] headers, byte[] responseBody, Throwable error) {
                //Toast.makeText(MainActivity.this, "failed", Toast.LENGTH_SHORT).show();
                uploadResult[0] = error.toString();
            }

        });
        return uploadResult[0];

    }

}
