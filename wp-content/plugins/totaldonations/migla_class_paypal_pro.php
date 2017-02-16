<?php

class migla_paypal_pro{

	public $API_USERNAME;
	public $API_PASSWORD;
	public $API_SIGNATURE;
	public $API_ENDPOINT;
	
	function migla_paypal_pro($API_USERNAME, $API_PASSWORD, $API_SIGNATURE, $ENVIRONMENT)
        {
		$this->API_USERNAME = $API_USERNAME;
		$this->API_PASSWORD = $API_PASSWORD;
		$this->API_SIGNATURE = $API_SIGNATURE;
		if($ENVIRONMENT == 'sandbox'){
                    $this->API_ENDPOINT = "https://api-3t.sandbox.paypal.com/nvp";
		}else{
                    $this->API_ENDPOINT = "https://api-3t.paypal.com/nvp";			
		}
	}

	function sendAPIRequest($methodName_, $nvpStr_ , $verifySSLcerts ) 
        {

		$API_UserName 	= urlencode($this->API_USERNAME);
		$API_Password 	= urlencode($this->API_PASSWORD);
		$API_Signature 	= urlencode($this->API_SIGNATURE);
		$API_Endpoint 	= $this->API_ENDPOINT;
		$version_curl 	= curl_version() ;             
		$version		= urlencode('50.1'); //$version['version_number'] ;
		
		// Set the curl parameters.
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
		//curl_setopt($ch, CURLOPT_VERBOSE, 1);		
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6"); // set browser/user agent
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // automatically follow Location: headers (ie redirects)
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1); // auto set the referer in the event of a redirect
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5); // make sure we dont get stuck in a loop
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // 10s timeout time for cURL connection				
	 
		// Turn off the server and peer verification (TrustManager Concept).
                if( $verifySSLcerts ){
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 2);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		    curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/ca/cacert_migla.pem');
                }else{
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  		    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                }

        /* This is using TLS */
        //curl_setopt($ch, CURLOPT_SSLVERSION , 4);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
	 

		// Set the API operation, version, and API signature in the request.
		$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature"."$nvpStr_";
	 
		// Set the request as a POST FIELD for curl.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
	 
		// Get response from the server.
		$httpResponse = curl_exec($ch);
	 
		if(!$httpResponse) {
			exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
		}
	 
		// Extract the response details.
		$httpResponseAr = explode("&", $httpResponse);
	 
		$httpParsedResponseAr = array();
		foreach ($httpResponseAr as $i => $value) {
			$tmpAr = explode("=", $value);
			if(sizeof($tmpAr) > 1) {
				$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
			}
		}
	 
		if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
			exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
		}
	 
		return $httpParsedResponseAr;
	}

}

?>