<?php

use Illuminate\Support\Facades\Http;
use App\Helpers\ExtensionHelper;


function createServer($user, $params, $order){
    $config;
    foreach($params as $key => $value){
        error_log($key . " => " . $value);
        if($key == "config"){
            $config = json_decode($value);
            foreach($config as $key => $value){
                error_log($key . " => " . $value);
            }
        }
    }
    $apiKey = ExtensionHelper::getConfig('virtualizor', 'key');
    $url = 'http://192.168.2.17:4084/index.php?act=productdetails&api=json&apikey='. $apiKey . '&apipass='. ExtensionHelper::getConfig('virtualizor', 'pass');

    $pass = ExtensionHelper::getConfig('virtualizor', 'pass');

    // Create post request
    $response = Http::get($url);
    if($response->json()){
        $response = $response->json();
        error_log($response);
    }
    else{
    }
    if(!$response->successful()){
        return;
    }


}



