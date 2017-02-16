<?php
  class migla_paypal_payflow {
    
    var $submiturl;
    var $vendor;
    var $user;
    var $partner;
    var $password;
    var $errors = '';
    var $ClientCertificationId = '';
    var $currencies_allowed = array('USD', 'EUR', 'GBP', 'CAD', 'JPY', 'AUD');
    var $test_mode = 1; 
    
    function migla_paypal_payflow($vendor, $user, $partner, $password, $mode) //CONSTRUCTOR
	{  
		$this->vendor = $vendor;
		$this->user = $user;
		$this->partner = $partner;
		$this->password = $password;
            
		if ( $mode == 'sandbox' ) 
		{
			$this->submiturl = 'https://pilot-payflowpro.paypal.com';   
		} else {
			$this->submiturl = 'https://payflowpro.paypal.com';
		}
      
		// check for CURL
		if (!function_exists('curl_init')) {
			$this->set_errors('Curl function not found.');
			return;
		}           
    }

    // Curl custom headers; adjust appropriately for your setup:
    function get_curl_headers() {
      $headers = array();
      
      $headers[] = "Content-Type: text/namevalue"; //or maybe text/xml
      $headers[] = "X-VPS-Timeout: 30";
      $headers[] = "X-VPS-VIT-OS-Name: Linux";  // Name of your OS
      $headers[] = "X-VPS-VIT-OS-Version: RHEL 4";  // OS Version
      $headers[] = "X-VPS-VIT-Client-Type: PHP/cURL";  // What you are using
      $headers[] = "X-VPS-VIT-Client-Version: 0.01";  // For your info
      $headers[] = "X-VPS-VIT-Client-Architecture: x86";  // For your info
      $headers[] = "X-VPS-VIT-Client-Certification-Id: " . $this->ClientCertificationId . ""; // get this from payflowintegrator@paypal.com
      $headers[] = "X-VPS-VIT-Integration-Product: MyApplication";  // For your info, would populate with application name
      $headers[] = "X-VPS-VIT-Integration-Version: 0.01"; // Application version    
      
      return $headers;  
    }

    // parse result and return an array
    function get_curl_result($result) 
	{
      if (empty($result)) return;

      $pfpro = array();
      $result = strstr($result, 'RESULT');    
      $valArray = explode('&', $result);
      foreach($valArray as $val) {
        $valArray2 = explode('=', $val);
        $pfpro[$valArray2[0]] = $valArray2[1];
      }
      return $pfpro;    
    }

    function get_errors() 
	{
      if ($this->errors != '') {
        return $this->errors;
      }
      return false;
    }
  
    function set_errors($string) {
      $this->errors = $string;
    }

    function get_version() {
      return '4.03';
    }    

    // sale
    function sale_transaction($card_number, $card_expire, $amount, $currency = 'USD', $data_array = array()) {
	   
      if (!is_numeric($amount) || $amount <= 0) {
        $this->set_errors('Amount is not valid');
        return;           
      }
      if (!in_array($currency, $this->currencies_allowed)) {
        $this->set_errors('Currency not allowed');
        return;                   
      } 

      // build hash
      $tempstr = $card_number . $amount . date('YmdGis') . "1";
      $request_id = md5($tempstr);

      // body
      $plist = 'USER=' . $this->user . '&';
      $plist .= 'VENDOR=' . $this->vendor . '&';
      $plist .= 'PARTNER=' . $this->partner . '&';
      $plist .= 'PWD=' . $this->password . '&';           
      $plist .= 'TENDER=' . 'C' . '&'; // C = credit card, P = PayPal
      $plist .= 'TRXTYPE=' . 'S' . '&'; //  S = Sale transaction, A = Authorisation, C = Credit, D = Delayed Capture, V = Void                        
      $plist .= 'ACCT=' . $card_number . '&'; 
      $plist .= 'EXPDATE=' . $card_expire . '&';
      $plist .= 'NAME=' . $data_array['card_name'] . '&';
      $plist .= 'AMT=' . $amount . '&';
      // extra data
      $plist .= 'CURRENCY=' . $currency . '&';
      $plist .= 'COMMENT1=' . $data_array['comment1'] . '&'; 
      $plist .= 'FIRSTNAME=' . $data_array['firstname'] . '&';
      $plist .= 'LASTNAME=' . $data_array['lastname'] . '&';
      $plist .= 'STREET=' . $data_array['street'] . '&';
      $plist .= 'CITY=' . $data_array['city'] . '&';     
      $plist .= 'STATE=' . $data_array['state'] . '&';     
      $plist .= 'ZIP=' . $data_array['zip'] .  '&';      
      $plist .= 'COUNTRY=US' . $data_array['country'] . '&';
      if (isset($data_array['cvv'])) {
        $plist .= 'CVV2=' . $data_array['cvv'] . '&';
      }
      $plist .= 'CLIENTIP=' . $data_array['clientip'] . '&';
      // verbosity
      $plist .= 'VERBOSITY=MEDIUM';

      $headers = $this->get_curl_headers();
      $headers[] = "X-VPS-Request-ID: " . $request_id;

      $user_agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)"; // play as Mozilla
      $ch = curl_init(); 
      curl_setopt($ch, CURLOPT_URL, $this->submiturl);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
      curl_setopt($ch, CURLOPT_HEADER, 1); // tells curl to include headers in response
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
      curl_setopt($ch, CURLOPT_TIMEOUT, 45); // times out after 45 secs
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // this line makes it work under https
      curl_setopt($ch, CURLOPT_POSTFIELDS, $plist); //adding POST data
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2); //verifies ssl certificate
      curl_setopt($ch, CURLOPT_FORBID_REUSE, TRUE); //forces closure of connection when done 
      curl_setopt($ch, CURLOPT_POST, 1); //data sent as POST 
    
      $result = curl_exec($ch);
      $headers = curl_getinfo($ch);
      curl_close($ch);

      $pfpro = $this->get_curl_result($result); //result arrray
    
      return $pfpro;
    }
	
  function recurring_add_action($card_number, $card_expire, $amount, $currency = 'USD', $data_array = array(),
     $interval, $period ) 
  {
     
      if (!is_numeric($amount) || $amount <= 0) {
        $this->set_errors('Amount is not valid');
        return;           
      }
      if (!in_array($currency, $this->currencies_allowed)) {
        $this->set_errors('Currency not allowed');
        return;                   
      } 

      // build hash
      $tempstr = $card_number . $amount . date('YmdGis') . "1";
      $request_id = md5($tempstr);
	  	  
      // body
      $plist = 'USER=' . $this->user . '&';
      $plist .= 'VENDOR=' . $this->vendor . '&';
      $plist .= 'PARTNER=' . $this->partner . '&';
      $plist .= 'PWD=' . $this->password . '&';           
      $plist .= 'TENDER=' . 'C' . '&'; // C = credit card, P = PayPal
      $plist .= 'TRXTYPE=' . 'R' . '&'; //  S = Sale transaction, A = Authorisation, C = Credit, D = Delayed Capture, V = Void
      $plist .= 'ACTION=' . 'A'. '&'; 	  
      $plist .= 'PROFILENAME=RegularSubscription&';	  
	  $plist .= 'AMT=' . $amount . '&';
	  $plist .= 'ACCT=' .  $card_number . '&';	  
		
      $added = 	$interval ;
	  
      if( $period == 'day' || $period == 'Day' )
      {
		$plist .= 'PAYPERIOD=' . 'DAYS' . '&';
		$plist .= 'FREQUENCY=' . $interval. '&';	
        $added = 	$interval ;		
      }else if( $period == 'month' || $period == 'Month' )
      {
		$plist .= 'PAYPERIOD=' . 'MONT' . '&';	  	  
        $added = 	$interval * 30 ;		
      }else if( $period == 'week' || $period == 'Week' )
      {
		$plist .= 'PAYPERIOD=' . 'WEEK' . '&';
        $added = 	$interval * 7;				
      }else if( $period == 'year' || $period == 'Year' )
      {
		$plist .= 'PAYPERIOD=' . 'YEAR' . '&';	  
        $added = 	$interval * 365;		
      }	


	$php_time_zone = date_default_timezone_get();
	$t = ""; 
	$d = ""; 
	$default = get_option('migla_default_timezone');

	if( $default == 'Server Time' ){
		$add_time 		= "+".  $added ." days";
		$startBilling 	= date('mdY', strtotime( $add_time ));	
		//$startBilling 	= date('mdY');
	}else{
		date_default_timezone_set( $default );
		$add_time 		= "+".  $added ." days";
		$startBilling 	= date('mdY', strtotime( $add_time ));	
		//$startBilling 	= date('mdY');
	}
	date_default_timezone_set( $php_time_zone );
	
	  $plist .= 'START=' . $startBilling  . '&';
	  	  
	  $plist .= 'TERM=' . '0' . '&' ; //Forever
	  
      // extra data
      $plist .= 'STREET=' . $data_array['street'] . '&';   
      $plist .= 'ZIP=' . $data_array['zip'] ;      
  
      $headers = $this->get_curl_headers();
      $headers[] = "X-VPS-Request-ID: " . $request_id;

      $user_agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)"; // play as Mozilla
      $ch = curl_init(); 
      curl_setopt($ch, CURLOPT_URL, $this->submiturl);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
      curl_setopt($ch, CURLOPT_HEADER, 1); // tells curl to include headers in response
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
      curl_setopt($ch, CURLOPT_TIMEOUT, 45); // times out after 45 secs
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // this line makes it work under https
      curl_setopt($ch, CURLOPT_POSTFIELDS, $plist); //adding POST data
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2); //verifies ssl certificate
      curl_setopt($ch, CURLOPT_FORBID_REUSE, TRUE); //forces closure of connection when done 
      curl_setopt($ch, CURLOPT_POST, 1); //data sent as POST 
    
      $result = curl_exec($ch);
      $headers = curl_getinfo($ch);
      curl_close($ch);

      $pfpro = $this->get_curl_result($result); //result arrray

      return $pfpro;    
  }	
  
  function recurring_convert_existing($card_number, $card_expire, $amount, $currency = 'USD', $data_array = array(),
     $interval, $period , $ORIGID ) 
  {
    
      if (!is_numeric($amount) || $amount <= 0) {
        $this->set_errors('Amount is not valid');
        return;           
      }
      if (!in_array($currency, $this->currencies_allowed)) {
        $this->set_errors('Currency not allowed');
        return;                   
      } 

      // build hash
      $tempstr = $card_number . $amount . date('YmdGis') . "1";
      $request_id = md5($tempstr);
	  	  
      // body
      $plist = 'USER=' . $this->user . '&';
      $plist .= 'VENDOR=' . $this->vendor . '&';
      $plist .= 'PARTNER=' . $this->partner . '&';
      $plist .= 'PWD=' . $this->password . '&';           
      $plist .= 'TENDER=' . 'C' . '&'; // C = credit card, P = PayPal
      $plist .= 'TRXTYPE=' . 'R' . '&'; //  S = Sale transaction, A = Authorisation, C = Credit, D = Delayed Capture, V = Void
      $plist .= 'ACTION=A&'; 	  
      $plist .= 'PROFILENAME=RegularSubscription&';	 
      $plist .= 'ORIGID='.$ORIGID.'&';	 	  
      $plist .= 'OPTIONALTRX=S&';
  
      $added = 	$interval ;
	  
      if( $period == 'day' || $period == 'Day' )
      {
		$plist .= 'PAYPERIOD=' . 'DAYS' . '&';
		$plist .= 'FREQUENCY=' . $interval. '&';	
        $added = 	$interval ;		
      }else if( $period == 'month' || $period == 'Month' )
      {
		$plist .= 'PAYPERIOD=' . 'MONT' . '&';	  	  
        $added = 	$interval * 30 ;		
      }else if( $period == 'week' || $period == 'Week' )
      {
		$plist .= 'PAYPERIOD=' . 'WEEK' . '&';
        $added = 	$interval * 7;				
      }else if( $period == 'year' || $period == 'Year' )
      {
		$plist .= 'PAYPERIOD=' . 'YEAR' . '&';	  
        $added = 	$interval * 365;		
      }	


	$php_time_zone = date_default_timezone_get();
	$t = ""; 
	$d = ""; 
	$default = get_option('migla_default_timezone');

	if( $default == 'Server Time' ){
		$add_time 		= "+".  $added ." days";
		$startBilling 	= date('mdY', strtotime( $add_time ));	
		//$startBilling 	= date('mdY');
	}else{
		date_default_timezone_set( $default );
		$add_time 		= "+".  $added ." days";
		$startBilling 	= date('mdY', strtotime( $add_time ));	
		//$startBilling 	= date('mdY');
	}
	date_default_timezone_set( $php_time_zone );
	
	  $plist .= 'START=' . $startBilling  . '&';
	  
	  	  
	  $plist .= 'TERM=' . '0' . '&' ; //Forever
	  $plist .= 'ACCT=' .  $card_number . '&';
	  
      // extra data
      $plist .= 'STREET=' . $data_array['street'] . '&';   
      $plist .= 'ZIP=' . $data_array['zip'] ;      
  
      $headers = $this->get_curl_headers();
      $headers[] = "X-VPS-Request-ID: " . $request_id;

      $user_agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)"; // play as Mozilla
      $ch = curl_init(); 
      curl_setopt($ch, CURLOPT_URL, $this->submiturl);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
      curl_setopt($ch, CURLOPT_HEADER, 1); // tells curl to include headers in response
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
      curl_setopt($ch, CURLOPT_TIMEOUT, 45); // times out after 45 secs
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // this line makes it work under https
      curl_setopt($ch, CURLOPT_POSTFIELDS, $plist); //adding POST data
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2); //verifies ssl certificate
      curl_setopt($ch, CURLOPT_FORBID_REUSE, TRUE); //forces closure of connection when done 
      curl_setopt($ch, CURLOPT_POST, 1); //data sent as POST 
    
      $result = curl_exec($ch);
      $headers = curl_getinfo($ch);
      curl_close($ch);

      $pfpro = $this->get_curl_result($result); //result arrray

      return $pfpro;    
  }	  
	
  } //END of Payflow class 


?>