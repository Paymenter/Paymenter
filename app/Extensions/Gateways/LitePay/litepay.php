<?php

if (!extension_loaded('curl')) {
    throw new \Exception('cURL extension seems not to be installed');
}

class litepay
{
    
    /**
     * Validate the given API key on instantiation
     */
     
    private $type;

    public function __construct($type)
    { // the constructor
      $this->type = $type;
    }

    public function __call($name, array $args)
    { // method_missing for PHP

        $response = "";
	
	if (empty($args)) { $args = array(); }
	else { $args = $args[0]; }
        
        if ($this->type == 'merchant') {
            $response = $this->_request('p/', $args, 'POST');
        } elseif ($this->type == 'api') {
            $response = $this->_request('api/'.$name, $args, 'GET');
        } else {
            $response = $this->_request($name, $args);
        }

	return $response;

    }

    /**
     * cURL GET request driver
     */ 
    private function _request($path, $args = array(), $method = 'GET')
    {
        // Generate cURL URL
        $url =  'https://litepay.ch/'.$path;
        $addedData = http_build_query($args);
     
        // Initiate cURL and set headers/options
        $ch  = curl_init();
        
        // If we run windows, make sure the needed pem file is used
        if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        	$pemfile = dirname(realpath(__FILE__)) . DIRECTORY_SEPARATOR . 'cacert.pem';
        	if(!file_exists($pemfile)) {
        		throw new Exception("Needed .pem file not found. Please download the .pem file at http://curl.haxx.se/ca/cacert.pem and save it as " . $pemfile);
        	}        	
        	curl_setopt($ch, CURLOPT_CAINFO, $pemfile);
        }

	// it's a GET method
	if ($method == 'GET') { $url .= '?' . $addedData; }

	curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1'); // enforce use of TLSv1
        curl_setopt($ch, CURLOPT_URL, $url);

	if ($method == 'POST')
	{ // this is a POST method
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $addedData);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	}

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the cURL request
        $result = curl_exec($ch);
        curl_close($ch);

	$json_result = json_decode($result);

	if ($json_result->status != 'success') { throw new Exception('Failed: ' . $json_result->message); }

        // Spit back the response object or fail
        return $result ? $json_result : false;
    }

}
