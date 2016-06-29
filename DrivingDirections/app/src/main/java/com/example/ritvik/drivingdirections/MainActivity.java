package com.example.ritvik.drivingdirections;

import android.content.Context;
import android.location.Location;
import android.location.LocationListener;
import android.location.LocationManager;
import android.os.AsyncTask;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.webkit.WebChromeClient;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Toast;
import android.widget.Toolbar;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;
import java.util.List;


public class MainActivity extends AppCompatActivity {

    double latitude, longitude, speed;
    String geohash;
    HttpClient httpclient = new DefaultHttpClient();
    HttpPost httppost = new HttpPost("http://52.33.22.202/coord/fetch.php");

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        //Webview Code

        WebView webViewer = (WebView) findViewById(R.id.webview);
        webViewer.getSettings().setJavaScriptEnabled(true);
        webViewer.setWebChromeClient(new WebChromeClient());
        webViewer.setWebViewClient(new WebViewClient() {

            @Override
            public boolean shouldOverrideUrlLoading(WebView view, String url) {
                view.loadUrl(url);
                return true;
            }
        });
        webViewer.loadUrl("http://52.33.22.202:8989/");

        //Location Code

        LocationManager locationManager = (LocationManager) this.getSystemService(Context.LOCATION_SERVICE);
        // Define a listener that responds to location updates
        LocationListener locationListener = new LocationListener() {
            public void onLocationChanged(Location location) {
                latitude = location.getLatitude();
                longitude = location.getLongitude();
                speed = (int) ((location.getSpeed()*3600)/1000);




                Thread thread = new Thread(new Runnable(){
                    @Override
                    public void run() {
                        try {
                            //Your code goes here
                            URL url = new URL("http://geohash.org?q=" + latitude + "," + longitude + "&format=url&redirect=0");
                            BufferedReader in = new BufferedReader(
                                    new InputStreamReader(
                                            url.openStream()));
                            geohash = in.readLine();
                            in.close();

                                List<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>(2);
                                nameValuePairs.add(new BasicNameValuePair("geohash", geohash));
                                nameValuePairs.add(new BasicNameValuePair("speed", String.valueOf(speed)));
                                httppost.setEntity(new UrlEncodedFormEntity(nameValuePairs));
                                httpclient.execute(httppost);

                        } catch (Exception e) {
                            e.printStackTrace();
                        }
                    }
                });

                thread.start();

            }

            public void onStatusChanged(String provider, int status, Bundle extras) {
            }

            public void onProviderEnabled(String provider) {
            }

            public void onProviderDisabled(String provider) {
            }
        };
        locationManager.requestLocationUpdates(LocationManager.GPS_PROVIDER, 3, 0, locationListener);
    }
}
