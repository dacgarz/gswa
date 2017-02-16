<?php
include_once 'migla-functions.php';

/* Mail List Vendor */
$maillist_choice = get_option('migla_mail_list_choice');
if( $maillist_choice == 'constant_contact' )
{
   include_once 'migla_class_constant_contact.php';
}
include_once 'migla_class_mailchimp.php';

/* Credit Card*/
include_once 'migla_credit_card_validator.php';

  add_action("wp_ajax_miglaA_approve_offline", "miglaA_approve_offline");
  add_action("wp_ajax_nopriv_miglaA_approve_offline", "miglaA_approve_offline");

function miglaA_approve_offline()
{
    update_post_meta( intval( $_POST['id'] ) , 'miglad_status', 'complete');
	
	$_email =""; 
	$_fname = ""; 
	$_lname = ""; 
	$_add_milis = false; 
	$postData = ""; 
	$mailchimp_data = array();

    global $wpdb;

    $wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}posts SET post_type=%s WHERE ID=%d" 
                             , 'migla_odonation' , intval( $_POST['id'] ) )
				);

    $e = get_option('migla_replyTo');
    $en = get_option('migla_replyToName');
    $ne = get_option('migla_notif_emails'); 

    $postdata = array();
    $postdata['miglad_firstname'] 	= get_post_meta( intval( $_POST['id'] ) , 'miglad_firstname', true);
    $postdata['miglad_lastname'] 	= get_post_meta( intval( $_POST['id'] ) , 'miglad_lastname', true);
    $postdata['miglad_amount']    	= get_post_meta( intval( $_POST['id'] ) , 'miglad_amount', true);
    $postdata['miglad_email']     	= get_post_meta( intval( $_POST['id'] ) , 'miglad_email', true);
    $postdata['miglad_anonymous'] 	= get_post_meta( intval( $_POST['id'] ) , 'miglad_anonymous', true);
    $postdata['miglad_repeating'] 	= 'no';
    $postdata['miglad_date'] 		= get_post_meta( intval( $_POST['id'] ) , 'miglad_date', true);  
	$postData['miglad_mg_add_to_milist'] = get_post_meta( intval( $_POST['id'] ) , 'miglad_mg_add_to_milist', true);
	
    if( get_option('migla_disable_thank_email') != 'yes' )
	{	
		mg_send_thank_you_email( $postdata, $e, $en );
	}
	
    //Data for Mailing List
    $_email = $postData['miglad_email']; 
    $_fname = $postData['miglad_firstname']; 
    $_lname = $postData['miglad_lastname'];
    $mailchimp_data['miglad_email']     = $postData['miglad_email']; 
    $mailchimp_data['miglad_firstname'] = $postData['miglad_firstname'];
    $mailchimp_data['miglad_lastname']  = $postData['miglad_lastname'];
	
    if( $postData['miglad_mg_add_to_milist']  == 'yes' )
	{
         $_add_milis = true;	
    }else{
         $_add_milis = false;	
	}

   /*** MAIL LIST ****/
   $maillist_choice = get_option('migla_mail_list_choice');
   if( $maillist_choice == 'constant_contact' )
   //if( true )
   {
        //add to Constant Contact
		include_once 'migla_class_constant_contact.php';
        $cc = new migla_constant_contact_class();
        $cc->add_to_milist_test( $_email, $_fname, $_lname, $_add_milis );

   }else if( $maillist_choice == 'mail_chimp' )
   {
         //add to mailchimp
         $cc = new migla_mailchimp_class();
         $cc->subscribe_contact( $mailchimp_data , $_add_milis);
   }	

    die();
}


  add_action("wp_ajax_miglaA_paypalDirectPaymentExecutor", "miglaA_paypalDirectPaymentExecutor");
  add_action("wp_ajax_nopriv_miglaA_paypalDirectPaymentExecutor", "miglaA_paypalDirectPaymentExecutor");

/**** PayPal *****/
function mg_paypal_avs_code_text( $code , $card_type, $method )
{
	// -1 : Not Match, 0 : Partial Match , 1 : Exact match , 2 : Unavailable
	$response_array = array();
	$response_array[0] = '-1';
	$response_array[1] = 'Error';
		
	if( $code == 'A' ){
		$response_array[0] = '0';
		$response_array[1] = 'Address only (no ZIP code)';
		
	}else if ( $code  == 'B' )
	{
		$response_array[0] = '0';
		$response_array[1] = 'Address only (no ZIP code)';

	}else if ( $code  == 'C' )
	{
		$response_array[0] = '-1';
		$response_array[1] = 'The transaction is declined.';
	}else if ( $code  == 'D' )
	{
		$response_array[0] = '1'; 
		$response_array[1] = 'International X Address and Postal Code';

	}else if ( $code  == 'E' ) 
	{
		$response_array[0] = '-1'; 
		$response_array[1] = 'Not allowed for MOTO (Internet/Phone) transactions. The transaction is declined.';

	}else if ( $code  == 'F' ) 
	{
		$response_array[0] = '1'; 
		$response_array[1] = 'UK-specific X Address and Postal Code';

	}else if ( $code  == 'G' ) 
	{	
		$response_array[0] = '2'; 
		$response_array[1] = 'Unavailable. Not applicable';
	}else if ( $code  == 'I' )
	{
		$response_array[0] = '2'; 
		$response_array[1] = 'International Unavailable. Not applicable';	
	
	}else if ( $code  == 'M' )
	{
		$response_array[0] = '1'; 
		$response_array[1] = 'Address and Postal Code';	
	
	}else if ( $code  == 'N' )
	{
		$response_array[0] = '-1'; 
		$response_array[1] = 'The transaction is declined.';	

	}else if ( $code  == 'P' )
	{
		$response_array[0] = '0'; 
		$response_array[1] = 'Postal Code only (no Address)';

	}else if ( $code  == 'R' )
	{
		$response_array[0] = '-1'; 
		$response_array[1] = 'Not applicable';

	}else if ( $code  == 'S' )
	{
		$response_array[0] = '2'; 
		$response_array[1] = 'Service not Supported. Not applicable';	
	
	}else if ( $code  == 'U' )
	{
		$response_array[0] = '2'; 
		$response_array[1] = 'Unavailable. Not applicable';	
	
	}else if ( $code  == 'W' )
	{
		$response_array[0] = '0'; 
		$response_array[1] = 'Whole ZIP. Nine-digit ZIP code (no Address)';	

	}else if ( $code  == 'X' )
	{
		$response_array[0] = '1'; 
		$response_array[1] = 'Exact match. Address and nine-digit ZIP code';

	}else if ( $code  == 'Y' )
	{
		$response_array[0] = '0'; 
		$response_array[1] = 'Yes. Address and five-digit ZIP';	
	
	}else if ( $code  == 'Z' )
	{
		$response_array[0] = '0'; 
		$response_array[1] = 'ZIP. Five-digit ZIP code (no Address)';	
	}
	
	return $response_array;
}
    
/* Paypal one time website pro */	
function miglaA_paypalDirectPaymentExecutor()
{
	$message 		= '';
	$is_use_nonce 	=  get_option('migla_use_nonce') == 'yes';
	
	if( !wp_verify_nonce( $_POST['nonce'], 'migla_' ) && $is_use_nonce )
	{
		$message = __('Nonce is not recognize', 'migla-donation');
	}else{
		
		include_once 'migla_class_paypal_pro.php';
		
			$_email =""; 
			$_fname = ""; 
			$_lname = ""; 
			$_add_milis = false; 
			$postData = ""; 
			$mailchimp_data = array();

		/* checker */
		$credit_card_checker = new migla_credit_card();
		
		$checker_message = array('', '', '');
		$is_valid		 = true; 
		
		if( get_option('migla_credit_card_validator') == 'yes' ) //Only checked if it is activate
		{
			$is_valid_pattern 	= $credit_card_checker->validPattern( $_POST['ccnumber'] , $_POST['cctype'] );	
			$is_valid_length 	= $credit_card_checker->validLength( $_POST['ccnumber'] , $_POST['cctype'] );	
			$is_valid_lunh 		= $credit_card_checker->validLuhn( $_POST['ccnumber'] , $_POST['cctype'] );	
				
			if( !$is_valid_pattern )
			{
				$is_valid = false; 
				$checker_message[0] = 'Invalid Card Pattern';
			}	
			if( !$is_valid_length )
			{
				$is_valid = false; 
				$checker_message[1] = 'Invalid Card Length';
			}	
			if( !$is_valid_lunh  )
			{
				$is_valid = false; 
				$checker_message[2] = 'Failed passing Lunh';
			}	
		}
		
		if( !$is_valid )
		{
			$message = $checker_message[0] . ' ' . $checker_message[1] . ' ' . $checker_message[2];
			
		}else{	
			
		 // Repack the Default Field Post
			$_array = $_POST['donorinfo'];
			$map 	= array();
			$map 	= migla_data_mapping( $_array );
			
			$transientKey =  "t_". $map['miglad_session_id'];

			//this is paypal pro
			   $paypal_username 	= get_option('migla_paypalpro_username'); 
			   $paypal_password 	= get_option('migla_paypalpro_password'); 
			   $paypal_signature 	= get_option('migla_paypalpro_signature');

			   $firstName 		= urlencode( migla_trim_sql_xss($_POST['ccfname']) ); 
			   $lastName		= urlencode( migla_trim_sql_xss($_POST['cclname']) ); 
			   $email               = urlencode( migla_trim_sql_xss($map['miglad_email']) );
			   $creditCardType 	= urlencode( $_POST['cctype'] );
			   $creditCardNumber 	= urlencode( migla_trim_sql_xss($_POST['ccnumber']) );
			   $expDateMonth 	= urlencode( migla_trim_sql_xss($_POST['ccmonth']) );
			   $padDateMonth 	= str_pad($expDateMonth, 2, '0', STR_PAD_LEFT);
			   $expDateYear 	= urlencode( migla_trim_sql_xss($_POST['ccyear']) );
			   $cvv2Number 		= urlencode( migla_trim_sql_xss($_POST['cccvc']) );
			   $address1 		= urlencode( $map['miglad_address'] );
			   $address2 		= urlencode("");
			   $country         = urlencode( migla_get_country_code($map['miglad_country']) );
			   $city 			= urlencode( $map['miglad_city'] );
			   $state 			= urlencode( $map['miglad_state'] );
			   $zip 			= urlencode( $map['miglad_postalcode'] );
			   $amount 			= urlencode( $map['miglad_amount'] );
			   $currencyCode	= urlencode( get_option(  'migla_default_currency'  ) );
			   
			   $is_AVS_on = get_option('migla_credit_card_avs')  == 'yes' ;
				if( $is_AVS_on )
				{   
				   $paymentAction 	= urlencode('Authorization');   
				}else{
					$paymentAction 	= urlencode('Sale');
				}
				
				$AVS_status = array();
				$AVS_status[0] = '999';
				$AVS_status[1] = 'Not Applicable';
				$AVS_CODE 	= '';
				$AVS_LEVEL 	= get_option('migla_avs_level'); //'AVS_level_medium';
			
				$desc  =  $map['miglad_campaign'];

				$nvpstr =	'&PAYMENTACTION='.$paymentAction.
						'&AMT='.$amount.
						'&CREDITCARDTYPE='.$creditCardType.
						'&ACCT='.$creditCardNumber.
						'&EXPDATE='.$padDateMonth.$expDateYear.
						'&CVV2='.$cvv2Number.
						'&FIRSTNAME='.$firstName.
						'&LASTNAME='.$lastName.
						'&STREET='.$address1.
						'&CITY='.$city.
						'&STATE='.$state.
						'&ZIP='.$zip.
						'&COUNTRYCODE='.$country.
						'&CURRENCYCODE='.$currencyCode.
						'&EMAIL='.$email.
						'&DESC='.$desc.
						'&RETURNFMFDETAILS=1'.'&IPADDRESS=127.0.0.1';
				
				$verifySSL = get_option('migla_paypal_verifySSL') == 'yes';

				$environment 	=  get_option('migla_payment') ;  // or 'beta-sandbox' or live
				$paypalPro		= new migla_paypal_pro($paypal_username, $paypal_password, $paypal_signature, $environment);
				$resArray 		= $paypalPro->sendAPIRequest('DoDirectPayment', $nvpstr, $verifySSL);
				$ack 			= strtoupper($resArray["ACK"]);
				$message 		= "1";      
				
				//add_option('migla_pro_status_auth', $resArray );
				
				$ack_status	= stristr($ack, 'SUCCESS') || stristr($ack, 'SUCCESSWITHWARNING');
				$pass = false;
				
				if( $is_AVS_on && $ack_status )
				{
					$AVS_status = mg_paypal_avs_code_text( $resArray['AVSCODE'] , $creditCardType , 'website_payment_pro' );
					$AVS_CODE = $resArray['AVSCODE'];
					
					$avs_level_pass = false;
					if( $AVS_LEVEL == 'medium' )
					{
						$avs_level_pass = $AVS_status[0] == '1' || $AVS_status[0] == '0' ;
					}else{
						$avs_level_pass = $AVS_status[0] == '1' ;
					}
					
					if( $avs_level_pass )
					{
						$nvpstr_capture =	'&AUTHORIZATIONID='.$resArray['TRANSACTIONID'].
											'&AMT='.$amount.
											'&CURRENCYCODE='.$currencyCode.
											'&COMPLETETYPE='.'Complete';
						
						$paypalPro_capture		= new migla_paypal_pro($paypal_username, $paypal_password, $paypal_signature, $environment);
						$resArray_capture 		= $paypalPro_capture->sendAPIRequest( 'DoCapture' , $nvpstr_capture , $verifySSL);
						$ack_capture 			= strtoupper($resArray_capture["ACK"]);
						$message 				= "1";  

						$ack_status	= stristr( $ack_capture , 'SUCCESS') || stristr( $ack_capture , 'SUCCESSWITHWARNING');	
						$resArray 	= $resArray_capture ;	
						$pass 		= true;
						//add_option('migla_pro_status_docapture', $resArray_capture );
					}else{
					
						$pass = false;
						
					}
				}else{
					$pass = true;
				}
				
				
			if( $pass )
			{
				if( $ack_status )
				{		
					add_option( $transientKey, $map); //save caches		
					$message 	= "1";
					$post_id 	= migla_create_post();
					$i 			= 0; 			

					$keys = array_keys( $map );
					foreach( (array)$map as $value)
					{
						add_post_meta( $post_id, $keys[$i], $value );
						$i++;
					}			
					  
					  //Save data from paypal
					  add_post_meta( $post_id, 'miglad_paymentmethod', 'Credit Card' );
					  add_post_meta( $post_id, 'miglad_paymentdata', $resArray );
					  add_post_meta( $post_id, 'miglad_transactionId', $resArray['TRANSACTIONID'] );
					  add_post_meta( $post_id, 'miglad_transactionType', 'One time (Paypal Pro)' );
					  
					  add_post_meta( $post_id, 'miglad_avs_response_code', $AVS_CODE );
					  add_post_meta( $post_id, 'miglad_avs_response_text', $AVS_status[1] );
					  
					if( $AVS_status[0] == '0' )
					{
						add_post_meta( $post_id, 'miglad_payment_status', 'AVS Partial Match' );
					}else if( $AVS_status[0] == '2' )
					{
						add_post_meta( $post_id, 'miglad_payment_status', 'AVS Unavailable' );				
					}
					
					 $map['miglad_transactionType']      = 'One time (Paypal Pro)';

					 /*** MAIL LIST ****/
					$_email = $map['miglad_email']; 
					$_fname = $map['miglad_firstname']; 
					$_lname = $map['miglad_lastname'];
					$mailchimp_data['miglad_email']     = $map['miglad_email']; 
					$mailchimp_data['miglad_firstname'] = $map['miglad_firstname'];
					$mailchimp_data['miglad_lastname']  = $map['miglad_lastname'];

					 if( isset($map['miglad_mg_add_to_milist']) && $map['miglad_mg_add_to_milist']  == 'yes' ){
						  $_add_milis = true;	
					 }else{
						  $_add_milis = false;	
					 }

							 $maillist_choice = get_option('migla_mail_list_choice');
							 if( $maillist_choice == 'constant_contact' )
							 {
								//add to Constant Contact
								$cc = new migla_constant_contact_class();
								$cc->add_to_milist_test( $_email, $_fname, $_lname, $_add_milis );

							 }else if( $maillist_choice == 'mail_chimp' )
							 {
								//add to mailchimp
								$cc = new migla_mailchimp_class();
								$cc->subscribe_contact( $mailchimp_data , $_add_milis);
							 }

					/*** SEND EMAIL ****/
			
					$e = get_option('migla_replyTo');
					$en = get_option('migla_replyToName');
					$ne = get_option('migla_notif_emails');

					if( get_option('migla_disable_thank_email') != 'yes' )		
						mg_send_thank_you_email( $map, $e, $en );
					
					mg_send_notification_emails( $map, $e, $en, $ne);

					if( isset($map['miglad_honoreeemail']) && get_option('migla_disable_honoree_email') != 'yes' )
					{	 			
						mg_send_hletter( $map, $e, $en, $map['miglad_honoreeletter'], $map['miglad_date'] );
					}

						$php_time_zone 	= date_default_timezone_get();				
						$default 		= get_option('migla_default_timezone');

						if( $default == 'Server Time' )
						{
							$gmt_offset = -get_option( 'gmt_offset' );
					
							if ($gmt_offset > 0) 
								$time_zone = 'Etc/GMT+' . $gmt_offset; 
							else		
								$time_zone = 'Etc/GMT' . $gmt_offset;    
					
							date_default_timezone_set( $time_zone );
						}else{
							date_default_timezone_set( $default );
						}
						add_post_meta( $post_id, 'miglad_timezone', $default );
						date_default_timezone_set( $php_time_zone );
					
				}else{
					$message = "";
					$message .= $resArray['L_ERRORCODE0'] .": ";
					$message .= urldecode($resArray['L_LONGMESSAGE0']); 
			   }
			   
			}else{
				$message = 'Address Verification System failed';
			}

		}
	}
	
	echo $message;
	die(); 
}

  add_action("wp_ajax_miglaA_paypalSubscriptionExecutor", "miglaA_paypalSubscriptionExecutor");
  add_action("wp_ajax_nopriv_miglaA_paypalSubscriptionExecutor", "miglaA_paypalSubscriptionExecutor");

function miglaA_paypalSubscriptionExecutor()
{
	$message 		= '';
	$is_use_nonce 	=  get_option('migla_use_nonce') == 'yes';
	
	if( !wp_verify_nonce( $_POST['nonce'], 'migla_' ) && $is_use_nonce )
	{
		$message = __('Nonce is not recognize', 'migla-donation');
	}else{
		
	include_once 'migla_class_paypal_pro.php';
	
        $_email =""; 
		$_fname = ""; 
		$_lname = ""; 
		$_add_milis = false; 
		$postData = ""; 
		$mailchimp_data = array();
		$trans_id	= '';
		$profile_id	= '';
		
	/* checker */
	$credit_card_checker = new migla_credit_card();
	
	$checker_message = array('', '', '');
	$is_valid		 = true; 
	
	if( get_option('migla_credit_card_validator') == 'yes' ) //Only checked if it is activate
	{
		$is_valid_pattern 	= $credit_card_checker->validPattern( $_POST['ccnumber'] , $_POST['cctype'] );	
		$is_valid_length 	= $credit_card_checker->validLength( $_POST['ccnumber'] , $_POST['cctype'] );	
		$is_valid_lunh 		= $credit_card_checker->validLuhn( $_POST['ccnumber'] , $_POST['cctype'] );	
			
		if( !$is_valid_pattern )
		{
			$is_valid = false; 
			$checker_message[0] = 'Invalid Card Pattern';
		}	
		if( !$is_valid_length )
		{
			$is_valid = false; 
			$checker_message[1] = 'Invalid Card Length';
		}	
		if( !$is_valid_lunh  )
		{
			$is_valid = false; 
			$checker_message[2] = 'Failed passing Lunh';
		}	
	}
	
	if( !$is_valid )
	{
		$message = $checker_message[0] . ' ' . $checker_message[1] . ' ' . $checker_message[2];
		
	}else{	
		// Repack the Default Field Post
        $_array = $_POST['donorinfo'];
        $map = array();
        $map =  migla_data_mapping( $_array );

        $transientKey =  "t_". $map['miglad_session_id'];
		
        //paypal pro code
           $paypal_username 	= get_option('migla_paypalpro_username'); 
           $paypal_password 	= get_option('migla_paypalpro_password'); 
           $paypal_signature 	= get_option('migla_paypalpro_signature');

           $custom              = urlencode( migla_trim_sql_xss($map['miglad_session_id']) );
           $firstName 		= urlencode( migla_trim_sql_xss($_POST['ccfname']) ); 
           $lastName		= urlencode( migla_trim_sql_xss($_POST['cclname']) );
           $email               = urlencode( $map['miglad_email'] );
           $creditCardType 	= urlencode( migla_trim_sql_xss($_POST['cctype']) );
           $creditCardNumber 	= urlencode( migla_trim_sql_xss($_POST['ccnumber']) );
           $expDateMonth 	= urlencode( migla_trim_sql_xss($_POST['ccmonth']) );
           $padDateMonth 	= str_pad($expDateMonth, 2, '0', STR_PAD_LEFT);
           $expDateYear 	= urlencode( migla_trim_sql_xss($_POST['ccyear']) );
           $cvv2Number 		= urlencode( migla_trim_sql_xss($_POST['cccvc']) );
           $address1 		= urlencode( $map['miglad_address'] );
           $address2 		= urlencode("");
           $country             = urlencode( migla_get_country_code($map['miglad_country']) );
           $city 		= urlencode( $map['miglad_city'] );
           $state 		= urlencode( $map['miglad_state'] );
           $zip 		= urlencode( $map['miglad_postalcode'] );
           $amount 		= urlencode( $map['miglad_amount'] );
           $currencyCode	= urlencode( get_option(  'migla_default_currency'  ) );
           $period              = urlencode( migla_trim_sql_xss($_POST['period']) );
           $time                = urlencode( migla_trim_sql_xss($_POST['time']) );
           $desc  =  $map['miglad_campaign'];
		   
			$verifySSL 	= get_option('migla_paypal_verifySSL') == 'yes';
			$pass		= false;
			$is_AVS_on 	= get_option('migla_credit_card_avs')  == 'yes' ;
			
			$AVS_status = array();
			$AVS_status[0] = '999';
			$AVS_status[1] = 'Not Applicable';
			$AVS_CODE = '';	
			$AVS_LEVEL 	= get_option('migla_avs_level'); //'AVS_level_medium';

			$environment  	= get_option('migla_payment') ; //'sandbox' or 'paypal'; 			
			  
				//Authorize only
				$nvpstr =	'&PAYMENTACTION='.'Authorization'.
						'&AMT='.$amount.
						'&CREDITCARDTYPE='.$creditCardType.
						'&ACCT='.$creditCardNumber.
						'&EXPDATE='.$padDateMonth.$expDateYear.
						'&CVV2='.$cvv2Number.
						'&FIRSTNAME='.$firstName.
						'&LASTNAME='.$lastName.
						'&STREET='.$address1.
						'&CITY='.$city.
						'&STATE='.$state.
						'&ZIP='.$zip.
						'&COUNTRYCODE='.$country.
						'&CURRENCYCODE='.$currencyCode.
						'&EMAIL='.$email.
						'&DESC='.$desc.
						'&RETURNFMFDETAILS=1';

				$environment 	=  get_option('migla_payment') ;  // or 'beta-sandbox' or live
				$paypalPro		= new migla_paypal_pro($paypal_username, $paypal_password, $paypal_signature, $environment);
				$resArray 		= $paypalPro->sendAPIRequest('DoDirectPayment', $nvpstr, $verifySSL);
				$ack 			= strtoupper($resArray["ACK"]);

				$ack_status	= stristr($ack, 'SUCCESS') || stristr($ack, 'SUCCESSWITHWARNING');
				
				$AVS_status = mg_paypal_avs_code_text( $resArray['AVSCODE'] , $creditCardType , 'website_payment_pro' );
				$AVS_CODE = $resArray['AVSCODE'];
				
				if( $AVS_LEVEL == 'medium' )
				{
					$security_level = $AVS_status[0] == '1' || $AVS_status[0] == '0' ;
				}else{
					$security_level = $AVS_status[0] == '1' ;
				}
				
				if( $is_AVS_on )
				{
					$first_check     =  $ack_status && $security_level;
					$msg_first_check = 'AVS Failed' ;
				}else{
					$first_check = $ack_status;
					$msg_first_check = 'Authorization Failed';
				}
				
				if( $first_check )
				{
				
					//Capture the previous
					$nvpstr_capture =	'&AUTHORIZATIONID='.$resArray['TRANSACTIONID'].
										'&AMT='.$amount.'&CURRENCYCODE='.$currencyCode.
										'&COMPLETETYPE='.'Complete';
					
					$paypalPro_capture		= new migla_paypal_pro($paypal_username, $paypal_password, $paypal_signature, $environment);
					$resArray_capture 		= $paypalPro_capture->sendAPIRequest( 'DoCapture' , $nvpstr_capture , $verifySSL);
					$ack_capture 			= strtoupper($resArray_capture["ACK"]);
					$message 				= "1";  

					//add_option('migla_paypal_sub_capture', $resArray_capture );
					$trans_id = $resArray_capture['TRANSACTIONID'];
					
					$ack_status	= stristr( $ack_capture , 'SUCCESS') || stristr( $ack_capture , 'SUCCESSWITHWARNING');						
				
					$php_time_zone = date_default_timezone_get();					
					$default 	= get_option('migla_default_timezone');

					if( $default == 'Server Time' )
					{
						$gmt_offset = -get_option( 'gmt_offset' );
				
						if ($gmt_offset > 0) 
							$time_zone = 'Etc/GMT+' . $gmt_offset; 
						else		
							$time_zone = 'Etc/GMT' . $gmt_offset;    
				
						date_default_timezone_set( $time_zone );
					}else{
						date_default_timezone_set( $default );
					}
								
					if( $ack_status )
					{
						//Create Recurring Profile
					   $first_frequency = 1; $billing_period = "";
					   if( $period == 'day' || $period == 'Day' )
					   {
						   $billing_period  = "Day";
						   $first_frequency = $time;
					   }else if( $period == 'month' || $period == 'Month' )
					   {
						   $billing_period = "Month";
						   $first_frequency = $time * 30;
					   }else if( $period == 'week' || $period == 'Week' )
					   {
						   $billing_period = "Week";
						   $first_frequency = $time * 7;
					   }else if( $period == 'year' || $period == 'Year' )
					   {
						   $billing_period = "Year";
						   $first_frequency = $time * 365;
					   }
					   
					   $add_time = "+". $first_frequency . " days";
					   $startBilling = date('Y-m-d', strtotime( $add_time ));
					   $f   		 = $startBilling. '\TH:i:s\Z';   
					   $start_date 	 = date( $f ) ;

					   date_default_timezone_set( $php_time_zone );

						$nvpstr_profile = '&PROFILESTARTDATE='.$start_date.
									'&PAYERSTATUS=verified'.
									'&DESC='.$desc.
									'&EMAIL='.$email.
									'&BILLINGPERIOD='.$billing_period.
									'&BILLINGFREQUENCY='.$time.
									'&AMT='.$amount.
									'&MAXFAILEDPAYMENTS=3'.
									'&FIRSTNAME='.$firstName.
									'&LASTNAME='.$lastName.
									'&ACCT='.$creditCardNumber.
									'&CREDITCARDTYPE='.$creditCardType.
									'&CVV2='.$cvv2Number.
									'&SUBSCRIBERNAME='.$firstName.' '.$lastName.
									'&STREET='.$address1.
									'&CITY='.$city.
									'&STATE='.$state.
									'&ZIP='.$zip.
									'&COUNTRYCODE='.$country.
									'&CURRENCYCODE='.$currencyCode.
									'&EXPDATE='.$padDateMonth.$expDateYear.
									'&PROFILEREFERENCE='.substr($custom, 5);
						
						$paypalPro_profile	  	= new migla_paypal_pro($paypal_username, $paypal_password, $paypal_signature, $environment);
						$resArray_profile 	  	= $paypalPro_profile->sendAPIRequest('CreateRecurringPaymentsProfile', $nvpstr_profile, $verifySSL);
						$ack_profile	  		= strtoupper($resArray_profile["ACK"]);
						$message      			= "1";  

						$ack_status	= stristr($ack_profile, 'SUCCESS') || stristr($ack_profile, 'SUCCESSWITHWARNING');
						$resArray = $resArray_profile;
						$pass = true;
					
						$profile_id 	= urldecode( $resArray['PROFILEID'] ) ;						
						
					}else{
						$ack_status	= stristr($ack_capture, 'SUCCESS') || stristr($ack_capture, 'SUCCESSWITHWARNING');
						$resArray = $resArray_capture;
						$pass = false;					
					}
					
				}else{
					$pass = false;
				}
				

		if(  $pass )
		{
			if( $ack_status )
			{		
				$message = "1";
				$post_id = migla_create_post();
				$i = 0; 

				$keys = array_keys( $map );
				foreach( (array)$map as $value) 
				{
					add_post_meta( $post_id, $keys[$i], $value );  //save in database
					$i++;
				}

				add_option( $transientKey, $map); //caches
				
				   //Save data from paypal
				  add_post_meta( $post_id, 'miglad_paymentmethod', 'Credit Card' );
				  add_post_meta( $post_id, 'miglad_paymentdata', $resArray );
				  add_post_meta( $post_id, 'miglad_transactionId', $trans_id );
				  add_post_meta( $post_id, 'miglad_transactionType', 'Recurring (Paypal Pro)' );
				  //add_post_meta( $post_id, 'miglad_timezone', $default );
				  add_post_meta( $post_id, 'miglad_subscription_id', $profile_id ); 

				  $map['miglad_transactionType']      = 'Recurring (Paypal Pro)';

				 /*** MAIL LIST ****/

				 $_email = $map['miglad_email']; 
				 $_fname = $map['miglad_firstname']; 
				 $_lname = $map['miglad_lastname'];
				 $mailchimp_data['miglad_email']     = $map['miglad_email']; 
				 $mailchimp_data['miglad_firstname'] = $map['miglad_firstname'];
				 $mailchimp_data['miglad_lastname']  = $map['miglad_lastname'];

				 if( isset($map['miglad_mg_add_to_milist']) && $map['miglad_mg_add_to_milist']  == 'yes' ){
					  $_add_milis = true;	
				 }else{
					  $_add_milis = false;	
				 }

						 $maillist_choice = get_option('migla_mail_list_choice');
						 if( $maillist_choice == 'constant_contact' )
						 {
							//add to Constant Contact
							$cc = new migla_constant_contact_class();
							$cc->add_to_milist_test( $_email, $_fname, $_lname, $_add_milis );

						 }else if( $maillist_choice == 'mail_chimp' )
						 {
							//add to mailchimp
							$cc = new migla_mailchimp_class();
							$cc->subscribe_contact( $mailchimp_data , $_add_milis);
						 }

				/*** SEND EMAIL ****/
				$e = get_option('migla_replyTo');
				$en = get_option('migla_replyToName');
				$ne = get_option('migla_notif_emails');

				 if( get_option('migla_disable_thank_email') != 'yes' ){				
					mg_send_thank_you_email( $map, $e, $en );
				 }
					mg_send_notification_emails( $map, $e, $en, $ne);
		 
				 if( isset($map['miglad_honoreeemail']) && get_option('migla_disable_honoree_email') != 'yes' ){
					mg_send_hletter( $map, $e, $en, $map['miglad_honoreeletter'], $map['miglad_date'] );
				 }
			 
			}else{
			
				 $message = "";
				 $message .= $resArray['L_ERRORCODE0'] .": ";
				 $message .= urldecode($resArray['L_LONGMESSAGE0']); 
				 
			}
	   
		}else{
			$message = $msg_first_check;
		}//AVS
		
	} // Credit checker
	}
	
   echo $message;
   die(); 
}


function miglaA_paypalSubscriptionExecutor_backup()
{
	$message 		= '';
	$is_use_nonce 	=  get_option('migla_use_nonce') == 'yes';
	
	if( !wp_verify_nonce( $_POST['nonce'], 'migla_' ) && $is_use_nonce )
	{
		$message = __('Nonce is not recognize', 'migla-donation');
	}else{
		
	include_once 'migla_class_paypal_pro.php';
	
        $_email =""; 
		$_fname = ""; 
		$_lname = ""; 
		$_add_milis = false; 
		$postData = ""; 
		$mailchimp_data = array();
		$trans_id	= '';
		$profile_id	= '';
		
	/* checker */
	$credit_card_checker = new migla_credit_card();
	
	$checker_message = array('', '', '');
	$is_valid		 = true; 
	
	if( get_option('migla_credit_card_validator') == 'yes' ) //Only checked if it is activate
	{
		$is_valid_pattern 	= $credit_card_checker->validPattern( $_POST['ccnumber'] , $_POST['cctype'] );	
		$is_valid_length 	= $credit_card_checker->validLength( $_POST['ccnumber'] , $_POST['cctype'] );	
		$is_valid_lunh 		= $credit_card_checker->validLuhn( $_POST['ccnumber'] , $_POST['cctype'] );	
			
		if( !$is_valid_pattern )
		{
			$is_valid = false; 
			$checker_message[0] = 'Invalid Card Pattern';
		}	
		if( !$is_valid_length )
		{
			$is_valid = false; 
			$checker_message[1] = 'Invalid Card Length';
		}	
		if( !$is_valid_lunh  )
		{
			$is_valid = false; 
			$checker_message[2] = 'Failed passing Lunh';
		}	
	}
	
	if( !$is_valid )
	{
		$message = $checker_message[0] . ' ' . $checker_message[1] . ' ' . $checker_message[2];
		
	}else{	
		// Repack the Default Field Post
        $_array = $_POST['donorinfo'];
        $map = array();
        $map =  migla_data_mapping( $_array );

        $transientKey =  "t_". $map['miglad_session_id'];
		
        //paypal pro code
           $paypal_username 	= get_option('migla_paypalpro_username'); 
           $paypal_password 	= get_option('migla_paypalpro_password'); 
           $paypal_signature 	= get_option('migla_paypalpro_signature');

           $custom              = urlencode( migla_trim_sql_xss($map['miglad_session_id']) );
           $firstName 		= urlencode( migla_trim_sql_xss($_POST['ccfname']) ); 
           $lastName		= urlencode( migla_trim_sql_xss($_POST['cclname']) );
           $email               = urlencode( $map['miglad_email'] );
           $creditCardType 	= urlencode( migla_trim_sql_xss($_POST['cctype']) );
           $creditCardNumber 	= urlencode( migla_trim_sql_xss($_POST['ccnumber']) );
           $expDateMonth 	= urlencode( migla_trim_sql_xss($_POST['ccmonth']) );
           $padDateMonth 	= str_pad($expDateMonth, 2, '0', STR_PAD_LEFT);
           $expDateYear 	= urlencode( migla_trim_sql_xss($_POST['ccyear']) );
           $cvv2Number 		= urlencode( migla_trim_sql_xss($_POST['cccvc']) );
           $address1 		= urlencode( $map['miglad_address'] );
           $address2 		= urlencode("");
           $country             = urlencode( migla_get_country_code($map['miglad_country']) );
           $city 		= urlencode( $map['miglad_city'] );
           $state 		= urlencode( $map['miglad_state'] );
           $zip 		= urlencode( $map['miglad_postalcode'] );
           $amount 		= urlencode( $map['miglad_amount'] );
           $currencyCode	= urlencode( get_option(  'migla_default_currency'  ) );
           $period              = urlencode( migla_trim_sql_xss($_POST['period']) );
           $time                = urlencode( migla_trim_sql_xss($_POST['time']) );
           $desc  =  $map['miglad_campaign'];
		   
			$verifySSL 	= get_option('migla_paypal_verifySSL') == 'yes';
			$pass		= false;
			$is_AVS_on 	= get_option('migla_credit_card_avs')  == 'yes' ;
			
			$AVS_status = array();
			$AVS_status[0] = '999';
			$AVS_status[1] = 'Not Applicable';
			$AVS_CODE = '';	
			$AVS_LEVEL 	= get_option('migla_avs_level'); //'AVS_level_medium';

			$environment  	= get_option('migla_payment') ; //'sandbox' or 'paypal'; 			
			
			if( $is_AVS_on )
			{   
				//Authorize only
				$nvpstr =	'&PAYMENTACTION='.'Authorization'.
						'&AMT='.$amount.
						'&CREDITCARDTYPE='.$creditCardType.
						'&ACCT='.$creditCardNumber.
						'&EXPDATE='.$padDateMonth.$expDateYear.
						'&CVV2='.$cvv2Number.
						'&FIRSTNAME='.$firstName.
						'&LASTNAME='.$lastName.
						'&STREET='.$address1.
						'&CITY='.$city.
						'&STATE='.$state.
						'&ZIP='.$zip.
						'&COUNTRYCODE='.$country.
						'&CURRENCYCODE='.$currencyCode.
						'&EMAIL='.$email.
						'&DESC='.$desc.
						'&RETURNFMFDETAILS=1';

				$environment 	=  get_option('migla_payment') ;  // or 'beta-sandbox' or live
				$paypalPro		= new migla_paypal_pro($paypal_username, $paypal_password, $paypal_signature, $environment);
				$resArray 		= $paypalPro->sendAPIRequest('DoDirectPayment', $nvpstr, $verifySSL);
				$ack 			= strtoupper($resArray["ACK"]);

				$ack_status	= stristr($ack, 'SUCCESS') || stristr($ack, 'SUCCESSWITHWARNING');
				
				$AVS_status = mg_paypal_avs_code_text( $resArray['AVSCODE'] , $creditCardType , 'website_payment_pro' );
				$AVS_CODE = $resArray['AVSCODE'];
				
				if( $AVS_LEVEL == 'medium' )
				{
					$security_level = $AVS_status[0] == '1' || $AVS_status[0] == '0' ;
				}else{
					$security_level = $AVS_status[0] == '1' ;
				}
				
				if($ack_status && $security_level )
				{
				
					//Capture the previous
					$nvpstr_capture =	'&AUTHORIZATIONID='.$resArray['TRANSACTIONID'].
										'&AMT='.$amount.
										'&COMPLETETYPE='.'Complete';
					
					$paypalPro_capture		= new migla_paypal_pro($paypal_username, $paypal_password, $paypal_signature, $environment);
					$resArray_capture 		= $paypalPro_capture->sendAPIRequest( 'DoCapture' , $nvpstr_capture , $verifySSL);
					$ack_capture 			= strtoupper($resArray_capture["ACK"]);
					$message 				= "1";  

					//add_option('migla_paypal_sub_capture', $resArray_capture );
					$trans_id = $resArray_capture['TRANSACTIONID'];
					
					$ack_status	= stristr( $ack_capture , 'SUCCESS') || stristr( $ack_capture , 'SUCCESSWITHWARNING');						
				
					$php_time_zone = date_default_timezone_get();					
					$default 	= get_option('migla_default_timezone');

					if( $default == 'Server Time' )
					{
						$gmt_offset = -get_option( 'gmt_offset' );
				
						if ($gmt_offset > 0) 
							$time_zone = 'Etc/GMT+' . $gmt_offset; 
						else		
							$time_zone = 'Etc/GMT' . $gmt_offset;    
				
						date_default_timezone_set( $time_zone );
					}else{
						date_default_timezone_set( $default );
					}
								
					if( $ack_status )
					{
						//Create Recurring Profile
					   $first_frequency = 1; $billing_period = "";
					   if( $period == 'day' || $period == 'Day' )
					   {
						   $billing_period  = "Day";
						   $first_frequency = $time;
					   }else if( $period == 'month' || $period == 'Month' )
					   {
						   $billing_period = "Month";
						   $first_frequency = $time * 30;
					   }else if( $period == 'week' || $period == 'Week' )
					   {
						   $billing_period = "Week";
						   $first_frequency = $time * 7;
					   }else if( $period == 'year' || $period == 'Year' )
					   {
						   $billing_period = "Year";
						   $first_frequency = $time * 365;
					   }
					   
					   $add_time = "+". $first_frequency . " days";
					   $startBilling = date('Y-m-d', strtotime( $add_time ));
					   $f   		 = $startBilling. '\TH:i:s\Z';   
					   $start_date 	 = date( $f ) ;

					   date_default_timezone_set( $php_time_zone );

						$nvpstr_profile = '&PROFILESTARTDATE='.$start_date.
									'&PAYERSTATUS=verified'.
									'&DESC='.$desc.
									'&EMAIL='.$email.
									'&BILLINGPERIOD='.$billing_period.
									'&BILLINGFREQUENCY='.$time.
									'&AMT='.$amount.
									'&MAXFAILEDPAYMENTS=3'.
									'&FIRSTNAME='.$firstName.
									'&LASTNAME='.$lastName.
									'&ACCT='.$creditCardNumber.
									'&CREDITCARDTYPE='.$creditCardType.
									'&CVV2='.$cvv2Number.
									'&SUBSCRIBERNAME='.$firstName.' '.$lastName.
									'&STREET='.$address1.
									'&CITY='.$city.
									'&STATE='.$state.
									'&ZIP='.$zip.
									'&COUNTRYCODE='.$country.
									'&CURRENCYCODE='.$currencyCode.
									'&EXPDATE='.$padDateMonth.$expDateYear.
									'&PROFILEREFERENCE='.substr($custom, 5);
						
						$paypalPro_profile	  	= new migla_paypal_pro($paypal_username, $paypal_password, $paypal_signature, $environment);
						$resArray_profile 	  	= $paypalPro_profile->sendAPIRequest('CreateRecurringPaymentsProfile', $nvpstr_profile, $verifySSL);
						$ack_profile	  		= strtoupper($resArray_profile["ACK"]);
						$message      			= "1";  

						$ack_status	= stristr($ack_profile, 'SUCCESS') || stristr($ack_profile, 'SUCCESSWITHWARNING');
						$resArray = $resArray_profile;
						$pass = true;
					
						$profile_id 	= urldecode( $resArray['PROFILEID'] ) ;						
						
					}else{
						$ack_status	= stristr($ack_capture, 'SUCCESS') || stristr($ack_capture, 'SUCCESSWITHWARNING');
						$resArray = $resArray_capture;
						$pass = false;					
					}
					
				}else{
					$pass = false;
				}
				
			}else{	

					$php_time_zone = date_default_timezone_get();					
					$default 	= get_option('migla_default_timezone');

					if( $default == 'Server Time' )
					{
						$gmt_offset = -get_option( 'gmt_offset' );
				
						if ($gmt_offset > 0) 
							$time_zone = 'Etc/GMT+' . $gmt_offset; 
						else		
							$time_zone = 'Etc/GMT' . $gmt_offset;    
				
						date_default_timezone_set( $time_zone );
					}else{
						date_default_timezone_set( $default );
					}			
			
			   $first_frequency = 1; $billing_period = "";
			   if( $period == 'day' || $period == 'Day' )
			   {
				   $billing_period  = "Day";
				   $first_frequency = $time;
			   }else if( $period == 'month' || $period == 'Month' )
			   {
				   $billing_period = "Month";
				   $first_frequency = $time * 30;
			   }else if( $period == 'week' || $period == 'Week' )
			   {
				   $billing_period = "Week";
				   $first_frequency = $time * 7;
			   }else if( $period == 'year' || $period == 'Year' )
			   {
				   $billing_period = "Year";
				   $first_frequency = $time * 365;
			   }
			   
			   $add_time = "+". $first_frequency . " days";
			   $startBilling = date('Y-m-d', strtotime( $add_time ));
			   $f     = $startBilling. '\TH:i:s\Z';   
			   $start_date = date( $f ) ;

			   date_default_timezone_set( $php_time_zone );

				$nvpstr = '&PROFILESTARTDATE='.$start_date.
							'&PAYERSTATUS=verified'.
							'&DESC='.$desc.
							'&EMAIL='.$email.
							'&BILLINGPERIOD='.$billing_period.
							'&BILLINGFREQUENCY='.$time.
							'&AMT='.$amount.
							'&INITAMT='.$amount.
							'&MAXFAILEDPAYMENTS=3'.
							'&FIRSTNAME='.$firstName.
							'&LASTNAME='.$lastName.
							'&ACCT='.$creditCardNumber.
							'&CREDITCARDTYPE='.$creditCardType.
							'&CVV2='.$cvv2Number.
							'&SUBSCRIBERNAME='.$firstName.' '.$lastName.
							'&STREET='.$address1.
							'&CITY='.$city.
							'&STATE='.$state.
							'&ZIP='.$zip.
							'&COUNTRYCODE='.$country.
							'&CURRENCYCODE='.$currencyCode.
							'&EXPDATE='.$padDateMonth.$expDateYear.
							'&PROFILEREFERENCE='.substr($custom, 5);

				$paypalPro	  	= new migla_paypal_pro($paypal_username, $paypal_password, $paypal_signature, $environment);
				$resArray 	  	= $paypalPro->sendAPIRequest('CreateRecurringPaymentsProfile', $nvpstr, $verifySSL);
				$ack 	  		= strtoupper($resArray["ACK"]);
				$message      	= "1";  
				
				$trans_id		= $resArray['TRANSACTIONID'];
				$profile_id 	= urldecode( $resArray['PROFILEID'] ) ;
				
				$ack_status	= stristr($ack, 'SUCCESS') || stristr($ack, 'SUCCESSWITHWARNING');
				$pass = true;
			}

		if(  $pass )
		{
			if( $ack_status )
			{		
				$message = "1";
				$post_id = migla_create_post();
				$i = 0; 

				$keys = array_keys( $map );
				foreach( (array)$map as $value) 
				{
					add_post_meta( $post_id, $keys[$i], $value );  //save in database
					$i++;
				}

				add_option( $transientKey, $map); //caches
				
				   //Save data from paypal
				  add_post_meta( $post_id, 'miglad_paymentmethod', 'Credit Card' );
				  add_post_meta( $post_id, 'miglad_paymentdata', $resArray );
				  add_post_meta( $post_id, 'miglad_transactionId', $trans_id );
				  add_post_meta( $post_id, 'miglad_transactionType', 'Recurring (Paypal Pro)' );
				  //add_post_meta( $post_id, 'miglad_timezone', $default );
				  add_post_meta( $post_id, 'miglad_subscription_id', $profile_id ); 

				  $map['miglad_transactionType']      = 'Recurring (Paypal Pro)';

				 /*** MAIL LIST ****/

				 $_email = $map['miglad_email']; 
				 $_fname = $map['miglad_firstname']; 
				 $_lname = $map['miglad_lastname'];
				 $mailchimp_data['miglad_email']     = $map['miglad_email']; 
				 $mailchimp_data['miglad_firstname'] = $map['miglad_firstname'];
				 $mailchimp_data['miglad_lastname']  = $map['miglad_lastname'];

				 if( isset($map['miglad_mg_add_to_milist']) && $map['miglad_mg_add_to_milist']  == 'yes' ){
					  $_add_milis = true;	
				 }else{
					  $_add_milis = false;	
				 }

						 $maillist_choice = get_option('migla_mail_list_choice');
						 if( $maillist_choice == 'constant_contact' )
						 {
							//add to Constant Contact
							$cc = new migla_constant_contact_class();
							$cc->add_to_milist_test( $_email, $_fname, $_lname, $_add_milis );

						 }else if( $maillist_choice == 'mail_chimp' )
						 {
							//add to mailchimp
							$cc = new migla_mailchimp_class();
							$cc->subscribe_contact( $mailchimp_data , $_add_milis);
						 }

				/*** SEND EMAIL ****/
				$e = get_option('migla_replyTo');
				$en = get_option('migla_replyToName');
				$ne = get_option('migla_notif_emails');

				 if( get_option('migla_disable_thank_email') != 'yes' ){				
					mg_send_thank_you_email( $map, $e, $en );
				 }
					mg_send_notification_emails( $map, $e, $en, $ne);
		 
				 if( isset($map['miglad_honoreeemail']) && get_option('migla_disable_honoree_email') != 'yes' ){
					mg_send_hletter( $map, $e, $en, $map['miglad_honoreeletter'], $map['miglad_date'] );
				 }
			 
			}else{
			
				 $message = "";
				 $message .= $resArray['L_ERRORCODE0'] .": ";
				 $message .= urldecode($resArray['L_LONGMESSAGE0']); 
				 
			}
	   
		}else{
			$message = 'Address Verification failed';
		}//AVS
		
	} // Credit checker
	}
	
   echo $message;
   die(); 
}



function mg_paypal_flow_avs_text_code( $addresscode, $zipcode )
{
	$result = array();
	$result[0] 	= '-1';
	$result[1]	= 'Error';
	
	$code = $addresscode . $zipcode;
	// 1  : Success : YY
	// 0  : Warning : XY, YX
	// 2  : Not Available : XX 
	// -1 : Error : NN 
	
	if( $code == 'YY' )
	{
		$result[0] 	= '1';
		$result[1]	= 'Both Address and Zip code are checked';
	}else if( $code == 'XY' ) 
	{
		$result[0] 	= '0';
		$result[1]	= 'Addres is unchecked but the Zip code is checked';	
	}else if( $code == 'YX' ) 
	{
		$result[0] 	= '0';
		$result[1]	= 'Addres is checked but the Zip code is unchecked';	
	}else if( $code == 'XX' ) 
	{
		$result[0] 	= '2';
		$result[1]	= 'Unavailable';	
	}else if( $code == 'NN' ) 
	{
		$result[0] 	= '-1';
		$result[1]	= 'Address Verification is failed';	
	}
	
	return $result ;
}

  add_action("wp_ajax_miglaA_paypalFlow", "miglaA_paypalFlow");
  add_action("wp_ajax_nopriv_miglaA_paypalFlow", "miglaA_paypalFlow");

function miglaA_paypalFlow()
{
	$message 		= '';
	$is_use_nonce 	=  get_option('migla_use_nonce') == 'yes';
	
	if( !wp_verify_nonce( $_POST['nonce'], 'migla_' ) && $is_use_nonce )
	{
		$message = __('Nonce is not recognize', 'migla-donation');
	}else{	
	
	include_once 'migla_class_paypal_flow.php';
	
		$result = "";
        $_email =""; 
		$_fname = ""; 
		$_lname = ""; 
		$_add_milis = false; 
		$postData = ""; 
		$mailchimp_data = array();
		
	/* checker */
	$credit_card_checker = new migla_credit_card();
	
	$checker_message = array('', '', '');
	$is_valid		 = true; 
	
	if( get_option('migla_credit_card_validator') == 'yes' ) //Only checked if it is activate
	{
		$is_valid_pattern 	= $credit_card_checker->validPattern( $_POST['ccnumber'] , $_POST['cctype'] );	
		$is_valid_length 	= $credit_card_checker->validLength( $_POST['ccnumber'] , $_POST['cctype'] );	
		$is_valid_lunh 		= $credit_card_checker->validLuhn( $_POST['ccnumber'] , $_POST['cctype'] );	
			
		if( !$is_valid_pattern )
		{
			$is_valid = false; 
			$checker_message[0] = 'Invalid Card Pattern';
		}	
		if( !$is_valid_length )
		{
			$is_valid = false; 
			$checker_message[1] = 'Invalid Card Length';
		}	
		if( !$is_valid_lunh  )
		{
			$is_valid = false; 
			$checker_message[2] = 'Failed passing Lunh';
		}	
	}
	
	if( !$is_valid )
	{
		$message = $checker_message[0] . ' ' . $checker_message[1] . ' ' . $checker_message[2];
		
	}else{			

	 // Repack the Default Field Post
        $_array = $_POST['donorinfo'];
        $map = array();
        $map = migla_data_mapping( $_array );
		
        $transientKey =  "t_". $map['miglad_session_id'];

           $firstName 		= urlencode( migla_trim_sql_xss($_POST['ccfname']) ); 
           $lastName		= urlencode( migla_trim_sql_xss($_POST['cclname']) ); 
           $email               = urlencode( migla_trim_sql_xss($map['miglad_email']) );
           $creditCardType 	= urlencode( $_POST['cctype'] );
		   
           $creditCardNumber 	= urlencode( migla_trim_sql_xss($_POST['ccnumber']) );
		   
           $expDateMonth 	= urlencode( migla_trim_sql_xss($_POST['ccmonth']) );	   
		   $padDateMonth 	= str_pad($expDateMonth, 2, '0', STR_PAD_LEFT);		   
           $expDateYear 	= urlencode( migla_trim_sql_xss($_POST['ccyear']) );
		   $expDateYear    = substr( $expDateYear , 2, 2);
		   
           $cvv2Number 		= urlencode( migla_trim_sql_xss($_POST['cccvc']) );
           $address1 		= urlencode( $map['miglad_address'] );
           $address2 		= urlencode("");
           $country         = migla_get_country_code($map['miglad_country']) ;
           $city 		= urlencode( $map['miglad_city'] );
           $state 		= urlencode( $map['miglad_state'] );
           $zip 		= urlencode( $map['miglad_postalcode'] );
           $amount 		= urlencode( $map['miglad_amount'] );
           $currencyCode	= urlencode( get_option(  'migla_default_currency'  ) );
           $paymentAction 	= urlencode("Sale");
           $desc  =  'Donation for '.$map['miglad_campaign'].' campaign by xxxxxxxxxxx';
		   $desc  .= substr($creditCardNumber,-5).' Card Holder: '.$firstName.' '.$lastName;

    //payflow($vendor, $user, $partner, $password)
    $paypal_vendor	    = get_option('migla_paypalflow_vendor'); //'astrieddeveloper';  
	$paypal_user        =  get_option('migla_paypalflow_user'); //'astrieddeveloper';
	$paypal_partner     = get_option('migla_paypalflow_partner'); //'paypal';
    $paypal_password 	= get_option('migla_paypalflow_password'); //'markov@model';  
	$paypal_mode 		= get_option('migla_payment');
   
	$payflow = new migla_paypal_payflow($paypal_vendor, $paypal_user, $paypal_partner, $paypal_password, $paypal_mode );

	if ($payflow->get_errors()) {
       echo $payflow->get_errors();
       exit;
	} 
  
    $data_array = array('comment1' => 'Donation',
                        'firstname' => $firstName,
                        'lastname' => $lastName,
                        'street' => $address1,
                        'city' => $city ,
                        'state' => '',
                        'zip' => $zip ,
                        'country' => $country  , // iso codes
                        'cvv' => $cvv2Number , 
                        'clientip' => '0.0.0.0',
						'card_name' =>  ($firstName.' '.$lastName) 
                        );    
											
						
    $result = $payflow->sale_transaction( $creditCardNumber  , 
	                                     ($padDateMonth.$expDateYear), 
										 $amount , 
										 $currencyCode, 
										 $data_array);

	//add_option('migla_paypal_pro' , $result);
		
		if (isset($result['RESULT']) && $result['RESULT'] == 0) 
		{    	
				add_option( $transientKey, $map); //save caches		
				$message = "1";
				$post_id = migla_create_post();
				$i = 0; 			

				$keys = array_keys( $map );
				foreach( (array)$map as $value)
				{
					add_post_meta( $post_id, $keys[$i], $value );
					$i++;
				}			
				  
				  //Save data from paypal
				  add_post_meta( $post_id, 'miglad_paymentmethod', 'Credit Card' );
				  add_post_meta( $post_id, 'miglad_transactionId', $result['PNREF'] );
				  add_post_meta( $post_id, 'miglad_transactionType', 'One time (Paypal Pro Flow)' );

				  add_post_meta( $post_id, 'miglad_avs_response_code', ($result['AVSADDR'].$result['AVSZIP']) );
				  
				 $map['miglad_transactionType']      = 'One time (Paypal Pro Flow)';

	  
				 $_email = $map['miglad_email']; 
				 $_fname = $map['miglad_firstname']; 
				 $_lname = $map['miglad_lastname'];
				 $mailchimp_data['miglad_email']     = $map['miglad_email']; 
				 $mailchimp_data['miglad_firstname'] = $map['miglad_firstname'];
				 $mailchimp_data['miglad_lastname']  = $map['miglad_lastname'];

				 if( isset($map['miglad_mg_add_to_milist'] ) && $map['miglad_mg_add_to_milist']  == 'yes' ){
					  $_add_milis = true;	
				 }else{
					  $_add_milis = false;	
				 }

						 $maillist_choice = get_option('migla_mail_list_choice');
						 if( $maillist_choice == 'constant_contact' )
						 {
							//add to Constant Contact
							$cc = new migla_constant_contact_class();
							$cc->add_to_milist_test( $_email, $_fname, $_lname, $_add_milis );

						 }else if( $maillist_choice == 'mail_chimp' )
						 {
							//add to mailchimp
							$cc = new migla_mailchimp_class();
							$cc->subscribe_contact( $mailchimp_data , $_add_milis);
						 }

				$e = get_option('migla_replyTo');
				$en = get_option('migla_replyToName');
				$ne = get_option('migla_notif_emails');

				if( get_option('migla_disable_thank_email') != 'yes' ){		
				   mg_send_thank_you_email( $map, $e, $en );
				}
				mg_send_notification_emails( $map, $e, $en, $ne);

				if( isset($map['miglad_honoreemail']) && get_option('migla_disable_honoree_email') != 'yes' ){	 			
				   mg_send_hletter( $map, $e, $en, $map['miglad_honoreeletter'], $map['miglad_date'] );
				}

					$php_time_zone 	= date_default_timezone_get();				
					$default 		= get_option('migla_default_timezone');

					if( $default == 'Server Time' )
					{
						$gmt_offset = -get_option( 'gmt_offset' );
				
						if ($gmt_offset > 0) 
							$time_zone = 'Etc/GMT+' . $gmt_offset; 
						else		
							$time_zone = 'Etc/GMT' . $gmt_offset;    
				
						date_default_timezone_set( $time_zone );
					}else{
						date_default_timezone_set( $default );
					}
					add_post_meta( $post_id, 'miglad_timezone', $default );
					date_default_timezone_set( $php_time_zone );				

		} else {
			$payflow->set_errors($result['RESPMSG'] . ' ['. $result['RESULT'] . ']');
			$message = ($result['RESPMSG'] . ' ['. $result['RESULT'] . ']');     
		}
		
	} //Credit Card Validator
	
}
	
	echo  $message; //json_encode($result);
	die();
}

  add_action("wp_ajax_miglaA_paypalFlow_recurring", "miglaA_paypalFlow_recurring");
  add_action("wp_ajax_nopriv_miglaA_paypalFlow_recurring", "miglaA_paypalFlow_recurring");

function miglaA_paypalFlow_recurring()
{
	$message 		= '';
	$is_use_nonce 	=  get_option('migla_use_nonce') == 'yes';
	
	if( !wp_verify_nonce( $_POST['nonce'], 'migla_' ) && $is_use_nonce )
	{
		$message = __('Nonce is not recognize', 'migla-donation');
	}else{
		
	include_once 'migla_class_paypal_flow.php';

		$result = "";
        $_email = ""; 
		$_fname = ""; 
		$_lname = ""; 
		$_add_milis = false; 
		$postData = ""; 
		$mailchimp_data = array();

	/* checker */
	$credit_card_checker = new migla_credit_card();
	
	$checker_message = array('', '', '');
	$is_valid		 = true; 
	
	if( get_option('migla_credit_card_validator') == 'yes' ) //Only checked if it is activate
	{
		$is_valid_pattern 	= $credit_card_checker->validPattern( $_POST['ccnumber'] , $_POST['cctype'] );	
		$is_valid_length 	= $credit_card_checker->validLength( $_POST['ccnumber'] , $_POST['cctype'] );	
		$is_valid_lunh 		= $credit_card_checker->validLuhn( $_POST['ccnumber'] , $_POST['cctype'] );	
			
		if( !$is_valid_pattern )
		{
			$is_valid = false; 
			$checker_message[0] = 'Invalid Card Pattern';
		}	
		if( !$is_valid_length )
		{
			$is_valid = false; 
			$checker_message[1] = 'Invalid Card Length';
		}	
		if( !$is_valid_lunh  )
		{
			$is_valid = false; 
			$checker_message[2] = 'Failed passing Lunh';
		}	
	}
	
	if( !$is_valid )
	{
		$message = $checker_message[0] . ' ' . $checker_message[1] . ' ' . $checker_message[2];
		
	}else{			
		
	 // Repack the Default Field Post
        $_array = $_POST['donorinfo'];
        $map = array();
        $map = migla_data_mapping( $_array );
		
        $transientKey =  "t_". $map['miglad_session_id'];

           $firstName 		= urlencode( migla_trim_sql_xss($_POST['ccfname']) ); 
           $lastName		= urlencode( migla_trim_sql_xss($_POST['cclname']) ); 
           $email               = urlencode( migla_trim_sql_xss($map['miglad_email']) );
           $creditCardType 	= urlencode( $_POST['cctype'] );
		   
           $creditCardNumber 	= urlencode( migla_trim_sql_xss($_POST['ccnumber']) );
		   
           $expDateMonth 	= urlencode( migla_trim_sql_xss($_POST['ccmonth']) );	   
		   $padDateMonth 	= str_pad($expDateMonth, 2, '0', STR_PAD_LEFT);		   
           $expDateYear 	= urlencode( migla_trim_sql_xss($_POST['ccyear']) );
		   $expDateYear    = substr( $expDateYear , 2, 2);
		   
           $cvv2Number 		= urlencode( migla_trim_sql_xss($_POST['cccvc']) );
           $address1 		= urlencode( $map['miglad_address'] );
           $address2 		= urlencode("");
           $country         = migla_get_country_code($map['miglad_country']) ;
           $city 		= urlencode( $map['miglad_city'] );
           $state 		= urlencode( $map['miglad_state'] );
           $zip 		= urlencode( $map['miglad_postalcode'] );
           $amount 		= urlencode( $map['miglad_amount'] );
           $currencyCode	= urlencode( get_option(  'migla_default_currency'  ) );
           $paymentAction 	= urlencode("Sale");
           $desc  =  'Donation for '.$map['miglad_campaign'].' campaign by xxxxxxxxxxx';
		   $desc  .= substr($creditCardNumber,-5).' Card Holder: '.$firstName.' '.$lastName;

		//payflow($vendor, $user, $partner, $password)
		$paypal_vendor	    = get_option('migla_paypalflow_vendor'); //'astrieddeveloper';  
		$paypal_user        =  get_option('migla_paypalflow_user'); //'astrieddeveloper';
		$paypal_partner     = get_option('migla_paypalflow_partner'); //'paypal';
		$paypal_password 	= get_option('migla_paypalflow_password'); //'markov@model';  
		$paypal_mode = get_option('migla_payment');
	   
		$payflow = new migla_paypal_payflow($paypal_vendor, $paypal_user, $paypal_partner, $paypal_password, $paypal_mode );

		if ($payflow->get_errors()) {
		   echo $payflow->get_errors();
		   exit;
		} 
	  
		$data_array = array('comment1' => 'Donation',
							'firstname' => $firstName,
							'lastname' => $lastName,
							'street' => $address1,
							'city' => $city ,
							'state' => '',
							'zip' => $zip ,
							'country' => $country  , // iso codes
							'cvv' => $cvv2Number , 
							'clientip' => '0.0.0.0',
							'card_name' =>  ($firstName.' '.$lastName) 
							);    
			
	   /*GET THE PERIOD*/	
	  
	   $period              = urlencode( migla_trim_sql_xss($_POST['period']) );
	   $time                = urlencode( migla_trim_sql_xss($_POST['time']) );
			   
		$rec_type = 'add';//get_option('migla_payflow_rec_type');
		
		if( $rec_type == 'add_convert' )
		{

		   $set_sale = $payflow->sale_transaction( $creditCardNumber  , 
											 ($padDateMonth.$expDateYear), 
											 $amount , 
											 $currencyCode, 
											 $data_array);	
											 
			if (isset($set_sale['RESULT']) && $set_sale['RESULT'] == 0) 
			{								 
						$result = $payflow->recurring_convert_existing(
											 $creditCardNumber  , 
											 ($padDateMonth.$expDateYear), 
											 $amount , 
											 $currencyCode, 
											 $data_array , 
											 $time  , 
											 $period,
											 $set_sale['PNREF'] ) ;
			}else{
				$result	= $set_sale;
			}									 
		}else{	
		
			$set_sale = $payflow->sale_transaction( $creditCardNumber  , 
											 ($padDateMonth.$expDateYear), 
											 $amount , 
											 $currencyCode, 
											 $data_array);			
		
			if (isset($set_sale['RESULT']) && $set_sale['RESULT'] == 0) 
			{
				$result = $payflow->recurring_add_action( 
												 $creditCardNumber  , 
												 ($padDateMonth.$expDateYear), 
												 $amount , 
												 $currencyCode, 
												 $data_array , 
												 $time  , 
												 $period  );
			}else{
				$result	= $set_sale;
			}
		}
		
		if (isset($result['RESULT']) && $result['RESULT'] == 0) {
				
				add_option( $transientKey, $map); //save caches		
				$message = "1";
				$post_id = migla_create_post();
				$i = 0; 			

				$keys = array_keys( $map );
				foreach( (array)$map as $value)
				{
					add_post_meta( $post_id, $keys[$i], $value );
					$i++;
				}			
				  
				  //Save data from paypal
				  add_post_meta( $post_id, 'miglad_paymentmethod', 'Credit Card' );
				  add_post_meta( $post_id, 'miglad_transactionId', $result['TRXPNREF'] );
				  add_post_meta( $post_id, 'miglad_subscription_id', $result['PROFILEID'] );
				  add_post_meta( $post_id, 'miglad_transactionType', 'Recurring (Paypal Pro Flow)' );

				 $map['miglad_transactionType']      = 'Recurring (Paypal Pro Flow)';

				 $_email = $map['miglad_email']; 
				 $_fname = $map['miglad_firstname']; 
				 $_lname = $map['miglad_lastname'];
				 $mailchimp_data['miglad_email']     = $map['miglad_email']; 
				 $mailchimp_data['miglad_firstname'] = $map['miglad_firstname'];
				 $mailchimp_data['miglad_lastname']  = $map['miglad_lastname'];

				 if( $map['miglad_mg_add_to_milist']  == 'yes' ){
					  $_add_milis = true;	
				 }else{
					  $_add_milis = false;	
				 }

                     $maillist_choice = get_option('migla_mail_list_choice');
                     if( $maillist_choice == 'constant_contact' )
                     {
                        //add to Constant Contact
                        $cc = new migla_constant_contact_class();
                        $cc->add_to_milist_test( $_email, $_fname, $_lname, $_add_milis );

                     }else if( $maillist_choice == 'mail_chimp' )
                     {
                        //add to mailchimp
                        $cc = new migla_mailchimp_class();
                        $cc->subscribe_contact( $mailchimp_data , $_add_milis);
                     }

            $e = get_option('migla_replyTo');
            $en = get_option('migla_replyToName');
            $ne = get_option('migla_notif_emails');

            if( get_option('migla_disable_thank_email') != 'yes' )
			{		
				mg_send_thank_you_email( $map, $e, $en );
		    }
            mg_send_notification_emails( $map, $e, $en, $ne);

            if( get_option('migla_disable_honoree_email') != 'yes' )
			{	 			
				mg_send_hletter( $map, $e, $en, $map['miglad_honoreeletter'], $map['miglad_date'] );
            }  

					$php_time_zone 	= date_default_timezone_get();				
					$default 		= get_option('migla_default_timezone');

					if( $default == 'Server Time' )
					{
						$gmt_offset = -get_option( 'gmt_offset' );
				
						if ($gmt_offset > 0) 
							$time_zone = 'Etc/GMT+' . $gmt_offset; 
						else		
							$time_zone = 'Etc/GMT' . $gmt_offset;    
				
						date_default_timezone_set( $time_zone );
					}else{
						date_default_timezone_set( $default );
					}
					add_post_meta( $post_id, 'miglad_timezone', $default );
					date_default_timezone_set( $php_time_zone );
			

    } else {
        $payflow->set_errors($result['RESPMSG'] . ' ['. $result['RESULT'] . ']');
        $message = ($result['RESPMSG'] . ' ['. $result['RESULT'] . ']');     
    }					

	}
}
	
	echo  $message; //json_encode($result);
	die();
}


/********* START STRIPE ********************************/  

  add_action("wp_ajax_miglaA_stripeCharge", "miglaA_stripeCharge");
  add_action("wp_ajax_nopriv_miglaA_stripeCharge", "miglaA_stripeCharge");

function miglaA_stripeCharge()
{
	$message 		= '';
	$is_use_nonce 	=  get_option('migla_use_nonce') == 'yes';
	
	if( !wp_verify_nonce( $_POST['nonce'], 'migla_' ) && $is_use_nonce )
	{
		$message = __('Nonce is not recognize', 'migla-donation');
	}else{
		
    require_once 'migla-call-stripe.php'; 
    Migla_Stripe::setApiKey( migla_getSK() );
 
    $message = "";
    $success = "1"; 
	$error1 = ""; 
	$error2 = ""; 
	$error3 = ""; 
	$error4 = ""; 
	$error5 = ""; 
	$error6 = "";
    $_email =""; 
	$_fname = ""; 
	$_lname = ""; 
	$_add_milis = false;
	$postData = ""; 
	$mailchimp_data = array();

	/* checker */
	$credit_card_checker = new migla_credit_card();
	
	$checker_message = array('', '', '');
	$is_valid		 = true; 
	
	if( get_option('migla_credit_card_validator') == 'yes' ) //Only checked if it is activate
	{
		$is_valid_pattern 	= $credit_card_checker->validPattern( $_POST['number'] , $_POST['card_type'] );	
		$is_valid_length 	= $credit_card_checker->validLength( $_POST['number'] , $_POST['card_type'] );	
		$is_valid_lunh 		= $credit_card_checker->validLuhn( $_POST['number'] , $_POST['card_type'] );	
			
		if( !$is_valid_pattern )
		{
			$is_valid = false; 
			$checker_message[0] = 'Invalid Card Pattern';
		}	
		if( !$is_valid_length )
		{
			$is_valid = false; 
			$checker_message[1] = 'Invalid Card Length';
		}	
		if( !$is_valid_lunh  )
		{
			$is_valid = false; 
			$checker_message[2] = 'Failed passing Lunh';
		}	
	}
	
	if( !$is_valid )
	{
		$message = $checker_message[0] . ' ' . $checker_message[1] . ' ' . $checker_message[2];
		
	}else{		
	
	// Repack the Default Field Post
    $_array = $_POST['donorinfo'];
    $map = array();
    $map =  migla_data_mapping( $_array );

    $transientKey =  "t_". $map['miglad_session_id'];
	
    try {
       
	  $token =   MStripe_Token::create( array( 
	                "card" => array(
						'name'            => $_POST['name'],
						'number'          => $_POST['number'],
						'cvc'             => $_POST['cvc'],
						'exp_month'       => $_POST['exp_month'],
						'exp_year'        => $_POST['exp_year'],
						'address_line1'   => $_POST['address_line1'],
						'address_city'    => $_POST['address_city'],
						'address_country' => $_POST['address_country'],
						'address_zip'     => $_POST['address_zip' ],
						'address_state'   => $_POST['address_state']
					)
				));
	   

	   
	$is_pass = true;  
	if( get_option('migla_credit_card_avs') == 'no'  )
	{
		$is_pass = true;
	}else {
		if( $token->card->address_line1_check != 'pass' && $token->card->address_zip_check != 'pass' )
		{
			$is_pass = false;
		}
	}
	
	if( $is_pass )
	{
		//AVS is ignored or AVS is accepted Then create a charge
		
		  $charge = MStripe_Charge::create( array(
							"amount" => $_POST['amount'],
							"currency" => get_option('migla_default_currency'),
							"source" => $token['id'],
							"receipt_email" => $map['miglad_email']
					 ));

		 $array = $charge->__toArray(true);  	
	 
      if(  $charge['status'] == 'paid' || $charge['status'] == 'succeeded' ) //only record if it is succeded
	  {

         $new_donation = array(
  	      'post_title' => 'migla_donation',
	      'post_content' => '',
	      'post_status' => 'publish',
	      'post_author' => 1,
	      'post_type' => 'migla_donation'
        );

           $new_id = wp_insert_post( $new_donation );

           $desc = "Name: ". $map['miglad_firstname'] . " " . $map['miglad_lastname']; 
		   $desc .= "; Email: " . $map['miglad_email'] .";" ;
           $desc .= substr( $map['miglad_session_id'], 5 ) ;
           $desc .= "; Campaign :". $map['miglad_campaign'] ;
		   
           $keys = array_keys( $map ); $i = 0;
           foreach( (array)$map as $value)
           {
               add_post_meta( $new_id , $keys[$i], $value );
               $i++;
           }  

			add_option( $transientKey, $map ); //cache saved		   
		   
               /*****  Add transaction data *******/
                   add_post_meta( $new_id, 'miglad_paymentmethod', 'credit card' );
                   add_post_meta( $new_id, 'miglad_paymentdata', $array );
                   add_post_meta( $new_id, 'miglad_transactionId', $charge['id'] );
                   add_post_meta( $new_id, 'miglad_transactionType', 'One time (Stripe)' );
				  
			$avs_response = 'Address:' . $token->card->address_line1_check;
			$avs_response .=  ', Zip:'. $token->card->address_zip_check;
			add_post_meta( $new_id, 'miglad_avs_response_text', $avs_response ); 

           $map['miglad_transactionType']      = 'One time (Stripe)';

           $success = "1";
       
           //Update Charge Description
           $ch = MStripe_Charge::retrieve( $charge['id'] );
           $ch->description =  $desc;
           $ch->save();

           //Data for Mailing List
           $_email = $map['miglad_email']; 
           $_fname = $map['miglad_firstname']; 
           $_lname = $map['miglad_lastname'];
           $mailchimp_data['miglad_email']     = $map['miglad_email']; 
           $mailchimp_data['miglad_firstname'] = $map['miglad_firstname'];
           $mailchimp_data['miglad_lastname']  = $map['miglad_lastname'];

           if( isset($map['miglad_mg_add_to_milist']) && $map['miglad_mg_add_to_milist']  == 'yes' ){
                $_add_milis = true;	
           }else{
                $_add_milis = false;	
           }

            /*** MAIL LIST ****/
            $maillist_choice = get_option('migla_mail_list_choice');
            if( $maillist_choice == 'constant_contact' )
            {
                //add to Constant Contact
                $cc = new migla_constant_contact_class();
                $cc->add_to_milist_test( $_email, $_fname, $_lname, $_add_milis );

             }else if( $maillist_choice == 'mail_chimp' )
             {
                 //add to mailchimp
                 $cc = new migla_mailchimp_class();
                 $cc->subscribe_contact( $mailchimp_data , $_add_milis);
             }

            /*** SEND EMAIL ****/

            $e = get_option('migla_replyTo');
            $en = get_option('migla_replyToName');
            $ne = get_option('migla_notif_emails');

            if( get_option('migla_disable_thank_email') != 'yes' )
			{							
               mg_send_thank_you_email( $map , $e, $en );
			}
            mg_send_notification_emails( $map , $e, $en, $ne);

			if(  get_option('migla_disable_honoree_email') != 'yes' && isset($map['miglad_honoreeemail']) )
			{
               mg_send_hletter( $map, $e, $en, $map['miglad_honoreeletter'], $map['miglad_date'] );
            }
			
					$php_time_zone 	= date_default_timezone_get();				
					$default 		= get_option('migla_default_timezone');

					if( $default == 'Server Time' )
					{
						$gmt_offset = -get_option( 'gmt_offset' );
				
						if ($gmt_offset > 0) 
							$time_zone = 'Etc/GMT+' . $gmt_offset; 
						else		
							$time_zone = 'Etc/GMT' . $gmt_offset;    
				
						date_default_timezone_set( $time_zone );
					}else{
						date_default_timezone_set( $default );
					}
					add_post_meta( $new_id, 'miglad_timezone', $default );
					date_default_timezone_set( $php_time_zone );			
      
	  }else{  //If status is not paid or succeded
			$success = '-1';
			$error1 = 'Charge is failed.';	
      }
	
	}else{
	    $success = '-1';
		$error1 = 'Address verification (AVS) is failed.';	
	}

   } catch( MStripe_CardError $e ) {
            $error1 = $e->getMessage();  $success = "-1";
   } catch ( MStripe_InvalidRequestError $e ) {
                // Invalid parameters were supplied to Stripe's API
           $error2 = $e->getMessage(); $success = "-1";
   } catch ( MStripe_AuthenticationError $e ) {
                 // Authentication with Stripe's API failed
           $error3 = $e->getMessage(); $success = "-1";
   } catch ( MStripe_ApiConnectionError $e ) {
                  // Network communication with Stripe failed
           $error4 = $e->getMessage(); $success = "-1";
   } catch ( MStripe_Error $e ) {
           $error5 = $e->getMessage(); $success = "-1";
   } catch ( Exception $e ) {
                 // Something else happened, completely unrelated to Stripe
          $error6 = $e->getMessage(); $success = "-1";
   }

   //$message = "";
   if( $success == "1" ){
       $message = $success;
   }else{
       $message .= $error1. " " . $error2. " " . $error3. " " . $error4. " " .$error5. " " .$error6 ;
   }

	}   
	
}
    echo $message; 
    die();
}

  add_action("wp_ajax_miglaA_createSubscription", "miglaA_createSubscription");
  add_action("wp_ajax_nopriv_miglaA_createSubscription", "miglaA_createSubscription");

function miglaA_createSubscription()
{
	
	$message 		= '';
	$is_use_nonce 	=  get_option('migla_use_nonce') == 'yes';
	
	if( !wp_verify_nonce( $_POST['nonce'], 'migla_' ) && $is_use_nonce )
	{
		$message = __('Nonce is not recognize', 'migla-donation');
	}else{
		
   $success = "1"; 
   $error1 = ""; 
   $error2 = ""; 
   $error3 = ""; 
   $error4 = ""; 
   $error5 = ""; 
   $error6 = "";
   $_email =""; 
   $_fname = ""; 
   $_lname = ""; 
   $_add_milis = false;
   $postData = ""; 
   $mailchimp_data = array();

	/* checker */
	$credit_card_checker = new migla_credit_card();
	
	$checker_message = array('', '', '');
	$is_valid		 = true; 
	
	if( get_option('migla_credit_card_validator') == 'yes' ) //Only checked if it is activate
	{
		$is_valid_pattern 	= $credit_card_checker->validPattern( $_POST['number'] , $_POST['card_type'] );	
		$is_valid_length 	= $credit_card_checker->validLength( $_POST['number'] , $_POST['card_type'] );	
		$is_valid_lunh 		= $credit_card_checker->validLuhn( $_POST['number'] , $_POST['card_type'] );	
			
		if( !$is_valid_pattern )
		{
			$is_valid = false; 
			$checker_message[0] = 'Invalid Card Pattern';
		}	
		if( !$is_valid_length )
		{
			$is_valid = false; 
			$checker_message[1] = 'Invalid Card Length';
		}	
		if( !$is_valid_lunh  )
		{
			$is_valid = false; 
			$checker_message[2] = 'Failed passing Lunh';
		}	
	}
	
	if( !$is_valid )
	{
		$message = $checker_message[0] . ' ' . $checker_message[1] . ' ' . $checker_message[2];
		
	}else{	   
   
	  try{

		 require_once 'migla-call-stripe.php'; 
		 Migla_Stripe::setApiKey( migla_getSK() );

		// Repack the Default Field Post
		$_array = $_POST['donorinfo'];
		$map = array();
		$map =  migla_data_mapping( $_array );
		 
		 $transientKey =  "t_". $map['miglad_session_id'];
		 
		 $desc = "Name: ". $map['miglad_firstname'] . " " . $map['miglad_lastname'] ;
		 $desc .= ";" . substr( $map['miglad_session_id'] ,5 );
		 $desc .= "; Campaign :". $map['miglad_campaign'] ;
		 
		  $token =   MStripe_Token::create( array( 
						"card" => array(
							'name'            => $_POST['name'],
							'number'          => $_POST['number'],
							'cvc'             => $_POST['cvc'],
							'exp_month'       => $_POST['exp_month'],
							'exp_year'        => $_POST['exp_year'],
							'address_line1'   => $_POST['address_line1'],
							'address_city'    => $_POST['address_city'],
							'address_country' => $_POST['address_country'],
							'address_zip'     => $_POST['address_zip' ],
							'address_state'   => $_POST['address_state']
						)
					));	 

		$is_pass = true;  
		if( get_option('migla_credit_card_avs') == 'no'  )
		{
			$is_pass = true;
		}else {
			if( $token->card->address_line1_check != 'pass' && $token->card->address_zip_check != 'pass' )
			{
				$is_pass = false;
			}
		}
		
		if( $is_pass )
		{				
					
			 $customer = MStripe_Customer::create( array(
				"source"      => $token['id'], 
				"email"       => $map['miglad_email'],
				"description" => $desc,
				"plan"        => $_POST['plan'],
				"quantity"    => $_POST['quantity']
			 ));

				//post for donation
				$new_donation = array(
				'post_title' => 'migla_donation',
				'post_content' => '',
				'post_status' => 'publish',
				'post_author' => 1,
				'post_type' => 'migla_donation'
				 );

				 //Save in database
				 $new_id = wp_insert_post( $new_donation );
				 $keys = array_keys( $map ); $i = 0;
				 foreach( (array)$map as $value)
				 {
					  add_post_meta( $new_id , $keys[$i], $value );
					  $i++;
				 }
				 
				add_option( $transientKey, $map ); //cache saved			 

				 add_post_meta( $new_id, 'miglad_customer_created', $customer['created']  );
				 add_post_meta( $new_id, 'miglad_paymentmethod', 'credit card' );
				 add_post_meta( $new_id, 'miglad_transactionType', 'Recurring (Stripe)' );
				 add_post_meta( $new_id, 'miglad_subscription_type', 'inital' ); 
				 add_post_meta( $new_id, 'miglad_subscription_id', $customer['id']  );
				 
				$avs_response = 'Address:' . $token->card->address_line1_check;
				$avs_response .=  ', Zip:'. $token->card->address_zip_check;
				add_post_meta( $new_id, 'miglad_avs_response_text', $avs_response ); 				 
		
				$map['miglad_transactionType']      = 'Recurring(Stripe)';

				/*** SEND EMAIL ****/

				$e = get_option('migla_replyTo');
				$en = get_option('migla_replyToName');
				$ne = get_option('migla_notif_emails');
				
				if( get_option('migla_disable_thank_email') != 'yes' ){		
				   mg_send_thank_you_email( $map , $e, $en );
				}
				mg_send_notification_emails( $map , $e, $en, $ne);

				if( isset($map['miglad_honoreeemail']) && get_option('migla_disable_honoree_email') != 'yes' ){
				   mg_send_hletter( $map, $e, $en, $map['miglad_honoreeletter'], $map['miglad_date'] );
				}

			  /* Save Description */         
			  $cu = MStripe_Customer::retrieve( $customer['id'] ); 
			  $cu->description = $desc ;
			  $cu->save();

				
			   //Data for Mailing List
			   $_email = $map['miglad_email']; 
			   $_fname = $map['miglad_firstname']; 
			   $_lname = $map['miglad_lastname'];
			   $mailchimp_data['miglad_email']     = $map['miglad_email']; 
			   $mailchimp_data['miglad_firstname'] = $map['miglad_firstname'];
			   $mailchimp_data['miglad_lastname']  = $map['miglad_lastname'];

			   if( $map['miglad_mg_add_to_milist'] == 'yes' && isset($map['miglad_mg_add_to_milist']) ){
					$_add_milis = true;	
			   }else{
					$_add_milis = false;	
			   }

				/*** MAIL LIST ****/
				$maillist_choice = get_option('migla_mail_list_choice');
				if( $maillist_choice == 'constant_contact' )
				{
					//add to Constant Contact
					$cc = new migla_constant_contact_class();
					$cc->add_to_milist_test( $_email, $_fname, $_lname, $_add_milis );

				 }else if( $maillist_choice == 'mail_chimp' )
				 {
					 //add to mailchimp
					 $cc = new migla_mailchimp_class();
					 $cc->subscribe_contact( $mailchimp_data , $_add_milis);
				 }

				$success = "1";
	  
					$php_time_zone 	= date_default_timezone_get();				
					$default 		= get_option('migla_default_timezone');

					if( $default == 'Server Time' )
					{
						$gmt_offset = -get_option( 'gmt_offset' );
				
						if ($gmt_offset > 0) 
							$time_zone = 'Etc/GMT+' . $gmt_offset; 
						else		
							$time_zone = 'Etc/GMT' . $gmt_offset;    
				
						date_default_timezone_set( $time_zone );
					}else{
						date_default_timezone_set( $default );
					}
					add_post_meta( $new_id, 'miglad_timezone', $default );
					date_default_timezone_set( $php_time_zone );	  
	  
		}else{ //If AVS failed
			$success = '-1';
			$error1 = 'Address verification (AVS) is failed.';			
		}
	  
	   } catch( MStripe_CardError $e ) {
				$error1 = $e->getMessage();  $success = "-1";
	   } catch ( MStripe_InvalidRequestError $e ) {
					// Invalid parameters were supplied to Stripe's API
			   $error2 = $e->getMessage(); $success = "-1";
	   } catch ( MStripe_AuthenticationError $e ) {
					 // Authentication with Stripe's API failed
			   $error3 = $e->getMessage(); $success = "-1";
	   } catch ( MStripe_ApiConnectionError $e ) {
					  // Network communication with Stripe failed
			   $error4 = $e->getMessage(); $success = "-1";
	   } catch ( MStripe_Error $e ) {
			   $error5 = $e->getMessage(); $success = "-1";
	   } catch ( Exception $e ) {
					 // Something else happened, completely unrelated to Stripe
			  $error6 = $e->getMessage(); $success = "-1";
	   }

	   $message = "";
	   if( $success == "1" ){
			$message = $success;
	   }else{
			$message .= $error1. " " . $error2. " " . $error3. " " . $error4. " " .$error5. " " .$error6 ;
	   }
  
	} //Card validator
}
	
  echo $message;
  die();
}


	/* Stripe.JS */
  add_action("wp_ajax_miglaA_createSubscription_", "miglaA_createSubscription_");
  add_action("wp_ajax_nopriv_miglaA_createSubscription_", "miglaA_createSubscription_");

function miglaA_createSubscription_()
{
	$message 		= '';
	$is_use_nonce 	=  get_option('migla_use_nonce') == 'yes';
	
	if( !wp_verify_nonce( $_POST['nonce'], 'migla_' ) && $is_use_nonce )
	{
		$message = __('Nonce is not recognize', 'migla-donation');
	}else{	
	
   $success = "1"; 
   $error1 = ""; 
   $error2 = ""; 
   $error3 = ""; 
   $error4 = ""; 
   $error5 = ""; 
   $error6 = "";
   $_email =""; 
   $_fname = ""; 
   $_lname = ""; 
   $_add_milis = false; 
   $postData = ""; 
   $mailchimp_data = array();   
   
	/* checker */
	$credit_card_checker = new migla_credit_card();
	
	$checker_message = array('', '', '');
	$is_valid		 = true; 
	
	if( get_option('migla_credit_card_validator') == 'yes' ) //Only checked if it is activate
	{
		$is_valid_pattern 	= $credit_card_checker->validPattern( $_POST['number'] , $_POST['card_type'] );	
		$is_valid_length 	= $credit_card_checker->validLength( $_POST['number'] , $_POST['card_type'] );	
		$is_valid_lunh 		= $credit_card_checker->validLuhn( $_POST['number'] , $_POST['card_type'] );
		
		if( !$is_valid_pattern )
		{
			$is_valid = false; 
			$checker_message[0] = 'Invalid Card Pattern';
		}	
		if( !$is_valid_length )
		{
			$is_valid = false; 
			$checker_message[1] = 'Invalid Card Length';
		}	
		if( !$is_valid_lunh  )
		{
			$is_valid = false; 
			$checker_message[2] = 'Failed passing Lunh';
		}	
	}
	
	if( !$is_valid )
	{
		$message = $checker_message[0] . ' ' . $checker_message[1] . ' ' . $checker_message[2];
		
	}else{	   
   
  try{

     require_once 'migla-call-stripe.php'; 
     Migla_Stripe::setApiKey( migla_getSK() );

	// Repack the Default Field Post
    $_array = $_POST['donorinfo'];
    $map = array();
    $map =  migla_data_mapping( $_array );
	 
     $transientKey =  "t_". $map['miglad_session_id'];
	 
     $desc = "Name: ". $map['miglad_firstname'] . " " . $map['miglad_lastname'] ;
     $desc .= ";" . substr( $map['miglad_session_id'] ,5 );
     $desc .= "; Campaign :". $map['miglad_campaign'] ;
	 
     $customer = MStripe_Customer::create( array(
        "source"      => $_POST['stripeToken'], 
        "email"       => $map['miglad_email'],
        "description" => $desc
     ));
	 
     $cu 		= MStripe_Customer::retrieve( $customer['id'] ); 
     $subscr 	= $cu->subscriptions->create(
					array(
                         "plan" 	=> $_POST['plan'],
                         "quantity" => $_POST['quantity']
                      ));	 

            //post for donation
            $new_donation = array(
				'post_title' => 'migla_donation',
				'post_content' => '',
				'post_status' => 'publish',
				'post_author' => 1,
				'post_type' => 'migla_donation'
             );

			 //Save in database
             $new_id = wp_insert_post( $new_donation );
             $keys = array_keys( $map ); $i = 0;
             foreach( (array)$map as $value)
             {
                  add_post_meta( $new_id , $keys[$i], $value );
                  $i++;
             }
			 
	 add_option( $transientKey, $map ); //cache saved			 

             add_post_meta( $new_id, 'miglad_customer_created', $customer['created']  );
             add_post_meta( $new_id, 'miglad_paymentmethod', 'credit card' );
             //add_post_meta( $new_id, 'miglad_timezone', $default );
             add_post_meta( $new_id, 'miglad_transactionType', 'Recurring (Stripe)' );
             add_post_meta( $new_id, 'miglad_subscription_type', 'inital' ); 
             add_post_meta( $new_id, 'miglad_subscription_id', $customer['id']  );
    
          $map['miglad_transactionType']      = 'Recurring(Stripe)';

            /*** SEND EMAIL ****/

            $e = get_option('migla_replyTo');
            $en = get_option('migla_replyToName');
            $ne = get_option('migla_notif_emails');
			
            if( get_option('migla_disable_thank_email') != 'yes' ){		
               mg_send_thank_you_email( $map , $e, $en );
			}
            mg_send_notification_emails( $map , $e, $en, $ne);

			if( isset($map['miglad_honoreeemail']) && get_option('migla_disable_honoree_email') != 'yes' ){
               mg_send_hletter( $map, $e, $en, $map['miglad_honoreeletter'], $map['miglad_date'] );
            }

          /* Save Description */         
          $cu = MStripe_Customer::retrieve( $customer['id'] ); 
          $cu->description = $desc ;
          $cu->save();

			
           //Data for Mailing List
           $_email = $map['miglad_email']; 
           $_fname = $map['miglad_firstname']; 
           $_lname = $map['miglad_lastname'];
           $mailchimp_data['miglad_email']     = $map['miglad_email']; 
           $mailchimp_data['miglad_firstname'] = $map['miglad_firstname'];
           $mailchimp_data['miglad_lastname']  = $map['miglad_lastname'];

           if( $map['miglad_mg_add_to_milist'] == 'yes' && isset($map['miglad_mg_add_to_milist']) ){
                $_add_milis = true;	
           }else{
                $_add_milis = false;	
           }

            /*** MAIL LIST ****/
            $maillist_choice = get_option('migla_mail_list_choice');
            if( $maillist_choice == 'constant_contact' )
            {
                //add to Constant Contact
                $cc = new migla_constant_contact_class();
                $cc->add_to_milist_test( $_email, $_fname, $_lname, $_add_milis );

             }else if( $maillist_choice == 'mail_chimp' )
             {
                 //add to mailchimp
                 $cc = new migla_mailchimp_class();
                 $cc->subscribe_contact( $mailchimp_data , $_add_milis);
             }

			$success = "1";
	   
					$php_time_zone 	= date_default_timezone_get();				
					$default 		= get_option('migla_default_timezone');

					if( $default == 'Server Time' )
					{
						$gmt_offset = -get_option( 'gmt_offset' );
				
						if ($gmt_offset > 0) 
							$time_zone = 'Etc/GMT+' . $gmt_offset; 
						else		
							$time_zone = 'Etc/GMT' . $gmt_offset;    
				
						date_default_timezone_set( $time_zone );
					}else{
						date_default_timezone_set( $default );
					}
					add_post_meta( $new_id, 'miglad_timezone', $default );
					date_default_timezone_set( $php_time_zone );	   
  
   } catch( MStripe_CardError $e ) {
            $error1 = $e->getMessage();  $success = "-1";
   } catch ( MStripe_InvalidRequestError $e ) {
                // Invalid parameters were supplied to Stripe's API
           $error2 = $e->getMessage(); $success = "-1";
   } catch ( MStripe_AuthenticationError $e ) {
                 // Authentication with Stripe's API failed
           $error3 = $e->getMessage(); $success = "-1";
   } catch ( MStripe_ApiConnectionError $e ) {
                  // Network communication with Stripe failed
           $error4 = $e->getMessage(); $success = "-1";
   } catch ( MStripe_Error $e ) {
           $error5 = $e->getMessage(); $success = "-1";
   } catch ( Exception $e ) {
                 // Something else happened, completely unrelated to Stripe
          $error6 = $e->getMessage(); $success = "-1";
   }

   $message = "";
   if( $success == "1" ){
        $message = $success;
   }else{
        $message .= $error1. " " . $error2. " " . $error3. " " . $error4. " " .$error5. " " .$error6 ;
   }
   
	}   
  
}
	
  echo $message;
  die();
}

	/* Stripe.JS */
  add_action("wp_ajax_miglaA_stripeCharge_", "miglaA_stripeCharge_");
  add_action("wp_ajax_nopriv_miglaA_stripeCharge_", "miglaA_stripeCharge_");

function miglaA_stripeCharge_()
{
	$message 		= '';
	$is_use_nonce 	=  get_option('migla_use_nonce') == 'yes';
	
	if( !wp_verify_nonce( $_POST['nonce'], 'migla_' ) && $is_use_nonce )
	{
		$message = __('Nonce is not recognize', 'migla-donation');
	}else{
		
    require_once 'migla-call-stripe.php'; 
    Migla_Stripe::setApiKey( migla_getSK() );
	
    $message = '';
    $success = "-1"; 
	$error1 = ""; 
	$error2 = ""; 
	$error3 = ""; 
	$error4 = ""; 
	$error5 = "";
	$error6 = "";
	$_email =""; 
	$_fname = ""; 
	$_lname = ""; 
	$_add_milis = false; 
	$postData = ""; 
	$mailchimp_data = array();
	
	/* checker */
	$credit_card_checker = new migla_credit_card();
	
	$checker_message = array('', '', '');
	$is_valid		 = true; 
	
	if( get_option('migla_credit_card_validator') == 'yes' ) //Only checked if it is activate
	{
		$is_valid_pattern 	= $credit_card_checker->validPattern( $_POST['number'] , $_POST['card_type'] );	
		$is_valid_length 	= $credit_card_checker->validLength( $_POST['number'] , $_POST['card_type'] );	
		$is_valid_lunh 		= $credit_card_checker->validLuhn( $_POST['number'] , $_POST['card_type'] );
			
		if( !$is_valid_pattern )
		{
			$is_valid = false; 
			$checker_message[0] = 'Invalid Card Pattern';
		}	
		if( !$is_valid_length )
		{
			$is_valid = false; 
			$checker_message[1] = 'Invalid Card Length';
		}	
		if( !$is_valid_lunh  )
		{
			$is_valid = false; 
			$checker_message[2] = 'Failed passing Lunh';
		}	
	}
	
	if( !$is_valid )
	{
		$message = $checker_message[0] . ' ' . $checker_message[1] . ' ' . $checker_message[2];
		
	}else{		
	
    try 
	{
       if (!isset($_POST['stripeToken']))
         throw new Exception("The Stripe Token was not generated correctly");
    
      $charge = MStripe_Charge::create(
						array(	"amount"	=> $_POST['amount'],
								"currency" 	=> get_option('migla_default_currency'),
								"card" 		=> $_POST['stripeToken'] 
						));

     $array = $charge->__toArray(true); 

		//This is the checking occours
	 $validate = false;
	 /*
	 if()
	 {
		$validate = ( $charge['status'] == 'paid' ) && () && ();
	 }else{
		
	 }
	 */

      if( $charge['status'] == 'paid' || $charge['status'] == 'succeeded'  )
	  {
	  
		// Repack the Default Field Post
		$_array = $_POST['donorinfo'];
		$map = array();
		$map =  migla_data_mapping( $_array );

		$transientKey =  "t_". $map['miglad_session_id'];	  

         $new_donation = array(
			   'post_title' => 'migla_donation',
			   'post_content' => '',
			   'post_status' => 'publish',
			   'post_author' => 1,
			   'post_type' => 'migla_donation'
          );

           $new_id = wp_insert_post( $new_donation );

           $desc = "Name: ". $map['miglad_firstname'] . " " . $map['miglad_lastname']; 
		   $desc .= "; Email: " . $map['miglad_email'] .";" ;
           $desc .= substr( $map['miglad_session_id'], 5 ) ;
           $desc .= "; Campaign :". $map['miglad_campaign'] ;
		   
           $keys = array_keys( $map ); $i = 0;
           foreach( (array)$map as $value)
           {
               add_post_meta( $new_id , $keys[$i], $value );
               $i++;
           }  

			add_option( $transientKey, $map ); //cache saved	

            /*****  Add transaction data *******/
            add_post_meta( $new_id, 'miglad_paymentmethod', 'credit card' );
            add_post_meta( $new_id, 'miglad_paymentdata', $array );
            add_post_meta( $new_id, 'miglad_transactionId', $charge['id'] );
            add_post_meta( $new_id, 'miglad_transactionType', 'One time (Stripe)' );
			
			add_post_meta( $new_id, 'miglad_avs_response_code', '' );
			add_post_meta( $new_id, 'miglad_cavv_response_code', '');
			add_post_meta( $new_id, 'miglad_avs_response_text', ( 'Address:'. $charge['source']['address_line1_check'] . ', Zip:' . $charge['source']['address_zip_check'] ) ) ;  	  

           $map['miglad_transactionType']      = 'One time (Stripe)';

           $success = "1";			

           //Update Charge Description
           $ch = MStripe_Charge::retrieve( $charge['id'] );
           $ch->description =  $desc;
           $ch->save();

           //Data for Mailing List
           $_email = $map['miglad_email']; 
           $_fname = $map['miglad_firstname']; 
           $_lname = $map['miglad_lastname'];
           $mailchimp_data['miglad_email']     = $map['miglad_email']; 
           $mailchimp_data['miglad_firstname'] = $map['miglad_firstname'];
           $mailchimp_data['miglad_lastname']  = $map['miglad_lastname'];

           if( isset($map['miglad_mg_add_to_milist']) && $map['miglad_mg_add_to_milist']  == 'yes' ){
                $_add_milis = true;	
           }else{
                $_add_milis = false;	
           }

            /*** MAIL LIST ****/
            $maillist_choice = get_option('migla_mail_list_choice');
            if( $maillist_choice == 'constant_contact' )
            {
                //add to Constant Contact
                $cc = new migla_constant_contact_class();
                $cc->add_to_milist_test( $_email, $_fname, $_lname, $_add_milis );

             }else if( $maillist_choice == 'mail_chimp' )
             {
                 //add to mailchimp
                 $cc = new migla_mailchimp_class();
                 $cc->subscribe_contact( $mailchimp_data , $_add_milis);
             }

            /*** SEND EMAIL ****/

            $e = get_option('migla_replyTo');
            $en = get_option('migla_replyToName');
            $ne = get_option('migla_notif_emails');

            if( get_option('migla_disable_thank_email') != 'yes' )
			{							
               mg_send_thank_you_email( $map , $e, $en );
			}
            mg_send_notification_emails( $map , $e, $en, $ne);

			if(  get_option('migla_disable_honoree_email') != 'yes' && isset($map['miglad_honoreeemail']) )
			{
               mg_send_hletter( $map, $e, $en, $map['miglad_honoreeletter'], $map['miglad_date'] );
            }

					$php_time_zone 	= date_default_timezone_get();				
					$default 		= get_option('migla_default_timezone');

					if( $default == 'Server Time' )
					{
						$gmt_offset = -get_option( 'gmt_offset' );
				
						if ($gmt_offset > 0) 
							$time_zone = 'Etc/GMT+' . $gmt_offset; 
						else		
							$time_zone = 'Etc/GMT' . $gmt_offset;    
				
						date_default_timezone_set( $time_zone );
					}else{
						date_default_timezone_set( $default );
					}
					add_post_meta( $new_id, 'miglad_timezone', $default );
					date_default_timezone_set( $php_time_zone );			
			
      }else{  //If status is not paid

      }

   } catch( MStripe_CardError $e ) {
            $error1 = $e->getMessage();  $success = "-1";
   } catch ( MStripe_InvalidRequestError $e ) {
                // Invalid parameters were supplied to Stripe's API
           $error2 = $e->getMessage(); $success = "-1";
   } catch ( MStripe_AuthenticationError $e ) {
                 // Authentication with Stripe's API failed
           $error3 = $e->getMessage(); $success = "-1";
   } catch ( MStripe_ApiConnectionError $e ) {
                  // Network communication with Stripe failed
           $error4 = $e->getMessage(); $success = "-1";
   } catch ( MStripe_Error $e ) {
           $error5 = $e->getMessage(); $success = "-1";
   } catch ( Exception $e ) {
                 // Something else happened, completely unrelated to Stripe
          $error6 = $e->getMessage(); $success = "-1";
   }

   $message = "";
   if( $success == "1" ){
       $message = $success;
   }else{
       $message .= $error1. " " . $error2. " " . $error3. " " . $error4. " " .$error5. " " .$error6 ;
   }
   
	}
}
	
   echo $message; 

    die();
}
  
/********* END STRIPE ********************************/  

/**** AUTHORIZE *****/
function mg_authorize_avs_code_text( $code )
{
	// -1 : Not Match, 0 : Partial Match , 1 : Exact match , 2 : Unavailable
	
	$result = array();
	$result[0] = '-1';
	$result[1] = 'Failed';	
	$text = '';

	if( $code == 'A' )
	{
		$result[0] = '0';
		$result[1] = 'Street address match but first 5-digits of Zip is not match';
	}else if( $code == 'B' )
	{
		$result[0] = '2';
		$result[1] = 'Address not provided for AVS check or street address match, postal code could not be verified';
	}else if( $code == 'E' )
	{
		$result[0] = '2';
		$text = 'AVS Error, an error occurred on the processing network while processing the AVS request so ';
		$text .= 'AVS information is not available for this transaction.';
		$result[1] = $text;
	}else if( $code == 'G' )
	{	
		$result[0] = '2';
		$result[1] =  'The credit card-issuing bank is not of U.S. origin and does not support the AVS system';
	}else if( $code == 'N' )
	{
		$result[0] = '0';
		$result[1] =  ' Street Address: No Match -- First 5 Digits of ZIP: No Match';
	}else if( $code == 'P' )
	{
		$result[0] = '2';
		$text = 'This response code is returned when address information is not checked against the AVS system. ';
		$text .= 'Examples of this would be eCheck.Net transactions, ';
		$text .= 'credits, voids, prior auth capture transactions, capture only transactions, declines and other transactions ';
		$text .= 'that do not include address checking';
		$result[1] = $text;
	}else if( $code == 'R' )
	{	
		$result[0] = '2';
		$result[1] = 'AVS was unavailable on the processing network or the processor did not respond';
	}else if( $code == 'S' )
	{	
		$result[0] = '2';
		$result[1] = ' AVS Not Supported by Card Issuing Bank';
	}else if( $code == 'U' )
	{	
		$result[0] = '2';
		$result[1] = 'Address Information For This Cardholder Is Unavailable';
	}else if( $code == 'W' )
	{
		$result[0] = '0';
		$result[1] = ' Street Address: No Match -- All 9 Digits of ZIP: Match';
	}else if( $code == 'X' )
	{	
		$result[0] = '1';
		$result[1] = 'Street Address: Match -- All 9 Digits of ZIP: Match';
	}else if( $code == 'Y' )
	{	
		$result[0] = '0';
		$result[1] = 'Street Address: Match - First 5 Digits of ZIP: Match';
	}else if( $code == 'Z' )
	{	
		$result[0] = '0';
		$result[1] = 'Street Address: No Match - First 5 Digits of ZIP: Match';
	}
	
	return $result ;
}


  add_action("wp_ajax_miglaA_authorize_send", "miglaA_authorize_send");
  add_action("wp_ajax_nopriv_miglaA_authorize_send", "miglaA_authorize_send");
  
function miglaA_authorize_send()
{
	$result		= '';
	$is_use_nonce 	=  get_option('migla_use_nonce') == 'yes';
	
	if( !wp_verify_nonce( $_POST['nonce'], 'migla_' ) && $is_use_nonce )
	{
		$result = __('Nonce is not recognize', 'migla-donation');
	}else{
		
  require_once 'inc_authorize/AuthorizeNet.php';

	// Repack the Default Field Post
    $_array 	= $_POST['donorinfo'];
    $map 		= array();
    $map 		=  migla_data_mapping( $_array );
    $_email 	= ""; 
	$_fname 	= ""; 
	$_lname 	= ""; 
	$_add_milis = false; 
	$mailchimp_data = array();	

    $transientKey =  "t_". $map['miglad_session_id'];

	/* checker */
	$credit_card_checker = new migla_credit_card();
	
	$checker_message = array('', '', '');
	$is_valid		 = true; 
	
	if( get_option('migla_credit_card_validator') == 'yes' ) //Only checked if it is activate
	{
		$is_valid_pattern 	= $credit_card_checker->validPattern( $_POST['card_number'] , $_POST['card_type'] );	
		$is_valid_length 	= $credit_card_checker->validLength( $_POST['card_number'] , $_POST['card_type'] );	
		$is_valid_lunh 		= $credit_card_checker->validLuhn( $_POST['card_number'] , $_POST['card_type'] );
			
		if( !$is_valid_pattern )
		{
			$is_valid = false; 
			$checker_message[0] = 'Invalid Card Pattern';
		}	
		if( !$is_valid_length )
		{
			$is_valid = false; 
			$checker_message[1] = 'Invalid Card Length';
		}	
		if( !$is_valid_lunh  )
		{
			$is_valid = false; 
			$checker_message[2] = 'Failed passing Lunh';
		}	
	}
	
	if( !$is_valid )
	{
		$result = $checker_message[0] . ' ' . $checker_message[1] . ' ' . $checker_message[2];
		
	}else{	
	 
		$METHOD_TO_USE = "AIM";
		// $METHOD_TO_USE = "DIRECT_POST"; 

		define("AUTHORIZENET_API_LOGIN_ID", get_option('migla_authorize_api_key') ); 
		define("AUTHORIZENET_TRANSACTION_KEY", get_option('migla_authorize_trans_key') ); 
		//define("AUTHORIZENET_LOG_FILE", "authorize.log");
		
		$verifySSL = false;
		if( get_option('migla_authorize_verifySSL') == 'yes' ){
			$verifySSL = true;  	
		}
		define("AUTHORIZENET_IS_VERIFY_PEER", $verifySSL ); 

		if( get_option('migla_payment_authorize') == 'authorize' ){
			  define("AUTHORIZENET_SANDBOX",false);   
			  define("TEST_REQUEST", "FALSE"); 
		}else{
			  define("AUTHORIZENET_SANDBOX",true);   
			  define("TEST_REQUEST", "TRUE");    
		}

		// You only need to adjust the two variables below if testing DPM
		define("AUTHORIZENET_MD5_SETTING",""); 

		$expiration_date = sprintf("%04d-%02d", $_POST['exp_year'], $_POST['exp_month']);
		$desc = 'Donation for ' . $map['miglad_campaign'] ;	
		$invoice = 'i-'.time();
		
		$transaction = new AuthorizeNetAIM;
		$transaction->setSandbox(AUTHORIZENET_SANDBOX);
		$transaction->setFields(
			array(
			'amount'     => $map['miglad_amount'], 
			'card_num'   => $_POST['card_number'], 
			'exp_date'   => $expiration_date,
			'first_name' => $_POST['firstname'],
			'last_name'  => $_POST['lastname'],
			'address'    => $map['miglad_address'],
			'city'       => $map['miglad_city'],                
			'state'      => $_POST['state'],               
			'country'    => $map['miglad_country'],    
			'zip'        => $map['miglad_postalcode'],  
			'email'      => $map['miglad_email'] ,
			'card_code'  => $_POST['cvc'],
			'description'    => $desc,
			'email_customer' => $map['miglad_email'],
			'invoice_num'    => $invoice,
			'company'        => $map['miglad_employer']
			)
		);

		$response = $transaction->authorizeAndCapture();

		if ($response->approved)
		{
			$transaction_id     = $response->transaction_id;
			$authorization_code = $response->authorization_code;
			$avs_response       = $response->avs_response;
			$cavv_response      = $response->cavv_response;

			$pass 	= false;
			$avs 	= array();
			if( get_option('migla_credit_card_avs') == 'yes')
			{
				$avs = mg_authorize_avs_code_check( $avs_response ) ;
				if( $avs[0] == '1' || $avs[0] == '0' || $avs[0] == '2' )
				{
					$pass =  true;
				}
			}else{
			    $pass 	= true;
				$avs[0] = '1';
				$avs[1] = 'AVS on Total Donations is turned off';
			}
			
			if( $pass )
			{
				$result = "1";
				
				$new_donation = array(
				  'post_title' => 'migla_donation',
				  'post_content' => '',
				  'post_status' => 'publish',
				  'post_author' => 1,
				  'post_type' => 'migla_donation'
				);

				 //Save in database
				 $new_id = wp_insert_post( $new_donation ); 
				 $i = 0;
				 $keys = array_keys( $map );
				 foreach( (array)$map as $value)
				 {
					  add_post_meta( $new_id , $keys[$i], $value );
					  $i++;
				 }

				add_option( $transientKey, $map ); //cache saved
		 
				/*****  Add transaction data *******/                  
				add_post_meta( $new_id, 'miglad_paymentmethod', 'credit card' );
				add_post_meta( $new_id, 'miglad_transactionId', $transaction_id );
				add_post_meta( $new_id, 'miglad_transactionType', 'One time (Authorize.NET)' );
				update_post_meta( $new_id, 'miglad_session_id', $_POST['session'] ); 
				add_post_meta( $new_id, 'miglad_payment_status', 'unsettled' );
				 
				add_post_meta( $new_id, 'miglad_avs_response_code', $avs_response );
				add_post_meta( $new_id, 'miglad_cavv_response_code', $cavv_response );
				add_post_meta( $new_id, 'miglad_raw_data', $response );
				add_post_meta( $new_id, 'miglad_avs_response_text', $avs[1] ) ;  
				 
				 $map['miglad_transactionType'] = 'One time (Authorize.NET)'; 

				 /*** SEND EMAIL ****/
				$e 	= get_option('migla_replyTo');
				$en = get_option('migla_replyToName');
				$ne = get_option('migla_notif_emails');

				if( get_option('migla_disable_thank_email') != 'yes' )
				{	
					mg_send_thank_you_email( $map , $e, $en );
				}
				mg_send_notification_emails( $map , $e, $en, $ne);

				if( isset($map['miglad_honoreeemail']) && get_option('migla_disable_honoree_email') != 'yes' )
				{
					mg_send_hletter( $map , $e, $en, $map['miglad_honoreeletter'], $map['miglad_date'] );
				}
				
				/*** MAIL LIST ****/
				 $_email = $map['miglad_email']; 
				 $_fname = $map['miglad_firstname']; 
				 $_lname = $map['miglad_lastname'];
				 $mailchimp_data['miglad_email']     = $map['miglad_email']; 
				 $mailchimp_data['miglad_firstname'] = $map['miglad_firstname'];
				 $mailchimp_data['miglad_lastname']  = $map['miglad_lastname'];

				 if( isset($map['miglad_mg_add_to_milist']) && $map['miglad_mg_add_to_milist'] == 'yes' ){
					  $_add_milis = true;	
				 }else{
					  $_add_milis = false;	
				 }			
				
				/* Mail list */
				$maillist_choice = get_option('migla_mail_list_choice');
				if( $maillist_choice == 'constant_contact' )
				{
					//add to Constant Contact
					$cc = new migla_constant_contact_class();
					$cc->add_to_milist_test( $_email, $_fname, $_lname, $_add_milis );

				 }else if( $maillist_choice == 'mail_chimp' )
				 {
					 //add to mailchimp
					 $cc = new migla_mailchimp_class();
					 $cc->subscribe_contact( $mailchimp_data , $_add_milis);
				 }

					$php_time_zone 	= date_default_timezone_get();				
					$default 		= get_option('migla_default_timezone');

					if( $default == 'Server Time' )
					{
						$gmt_offset = -get_option( 'gmt_offset' );
				
						if ($gmt_offset > 0) 
							$time_zone = 'Etc/GMT+' . $gmt_offset; 
						else		
							$time_zone = 'Etc/GMT' . $gmt_offset;    
				
						date_default_timezone_set( $time_zone );
					}else{
						date_default_timezone_set( $default );
					}
					add_post_meta( $new_id, 'miglad_timezone', $default );
					date_default_timezone_set( $php_time_zone );				 
			
			}else{ //AVS failed
				$result = 'Address Verification System failed';
			}
			
		}else if ($response->declined)
		{
			$result = 'Your credit card was declined by your bank. Please try another form of payment.';
		}else{
			$result = 'We encountered an error while processing your payment : '. $response->response_reason_text ;
		}

	} // TD's Card validation	

}	
	
   echo $result ;
   die();
}


  add_action("wp_ajax_miglaA_ARB_create", "miglaA_ARB_create");
  add_action("wp_ajax_nopriv_miglaA_ARB_create", "miglaA_ARB_create");

function miglaA_ARB_create()
{
	$result		= '';
	$is_use_nonce 	=  get_option('migla_use_nonce') == 'yes';
	
	if( !wp_verify_nonce( $_POST['nonce'], 'migla_' ) && $is_use_nonce )
	{
		$result = __('Nonce is not recognize', 'migla-donation');
	}else{
		
  require_once 'inc_authorize/AuthorizeNet.php';

  // Repack the Default Field Post
  $_array 	= $_POST['donorinfo'];
  $map 		= array();
  $map 		=  migla_data_mapping( $_array );
  $_email 	= ""; 
  $_fname 	= ""; 
  $_lname 	= ""; 
  $_add_milis = false; 
  $mailchimp_data = array();
  $transientKey =  "t_". $map['miglad_session_id'];
  
	/* checker */
	$credit_card_checker = new migla_credit_card();
	
	$checker_message = array('', '', '');
	$is_valid		 = true; 
	
	if( get_option('migla_credit_card_validator') == 'yes' ) //Only checked if it is activate
	{
		$is_valid_pattern 	= $credit_card_checker->validPattern( $_POST['card_number'] , $_POST['card_type'] );	
		$is_valid_length 	= $credit_card_checker->validLength( $_POST['card_number'] , $_POST['card_type'] );	
		$is_valid_lunh 		= $credit_card_checker->validLuhn( $_POST['card_number'] , $_POST['card_type'] );
			
		if( !$is_valid_pattern )
		{
			$is_valid = false; 
			$checker_message[0] = 'Invalid Card Pattern';
		}	
		if( !$is_valid_length )
		{
			$is_valid = false; 
			$checker_message[1] = 'Invalid Card Length';
		}	
		if( !$is_valid_lunh  )
		{
			$is_valid = false; 
			$checker_message[2] = 'Failed passing Lunh';
		}	
	}
	
	if( !$is_valid )
	{
		$result = $checker_message[0] . ' ' . $checker_message[1] . ' ' . $checker_message[2];
		
	}else{	  
  
		  $METHOD_TO_USE = "AIM";
		  // $METHOD_TO_USE = "DIRECT_POST";         // Uncomment this line to test DPM

		  define("AUTHORIZENET_API_LOGIN_ID", get_option('migla_authorize_api_key') );    // Add your API LOGIN ID
		  define("AUTHORIZENET_TRANSACTION_KEY", get_option('migla_authorize_trans_key') ); // Add your API transaction key
		  define("AUTHORIZENET_LOG_FILE", "authorize.log");
		  
			$verifySSL = false;
			if( get_option('migla_authorize_verifySSL') == 'yes' ){
				$verifySSL = true;  	
			}
			define("AUTHORIZENET_IS_VERIFY_PEER", $verifySSL );   

		  if( get_option('migla_payment_authorize') == 'authorize' ){
			 define("AUTHORIZENET_SANDBOX",false);
			 define("TEST_REQUEST", "FALSE"); 	 
		  }else{
			 define("AUTHORIZENET_SANDBOX",true);       // Set to false to test against production
			 define("TEST_REQUEST", "TRUE");           // You may want to set to true if testing against production
		  }

		$expiration_date = sprintf("%04d-%02d", $_POST['exp_year'], $_POST['exp_month']);
		$invoice = 'i-'.time();
		 
		/**** Create subscription ************************/
			// Set the subscription fields.
			$subscription = new AuthorizeNet_Subscription;
			$sub_name     = $map['miglad_campaign'] . ' '. $_POST['firstname'] . ' ' . $_POST['lastname'];
			$sub_name     = substr( $sub_name ,0 , 50);
			$subscription->name   = $sub_name ;
			 
			$period = $_POST['intervalUnit'];
			if( $period == 'day' ){
			   $period = 'days';
			}else if( $period == 'month' ){
			   $period = 'months';
			}
			$subscription->intervalLength           = $_POST['intervalLength'];
			$subscription->intervalUnit             = $period ;

			$subscription->startDate                = date('Y-m-d'); 
			$subscription->totalOccurrences         = "9999";
			$subscription->amount                   = $map['miglad_amount'];
			$subscription->creditCardCardNumber     = $_POST['card_number'];
			$subscription->creditCardExpirationDate = $expiration_date;
			$subscription->creditCardCardCode       = $_POST['cvc'];
			$subscription->billToFirstName          = $_POST['firstname'] ;
			$subscription->billToLastName           = $_POST['lastname'];
			$subscription->customerId               = time();
			$subscription->customerEmail            = $map['miglad_email'];
			$subscription->orderInvoiceNumber       = $invoice;
			$subscription->orderDescription         = $map['miglad_campaign'];
			$subscription->billToCompany            = "";
			$subscription->billToAddress            = $map['miglad_address'];
			$subscription->billToCity               = $map['miglad_city'];
			$subscription->billToState              = $map['miglad_state'].$map['miglad_province'];
			$subscription->billToZip                = $map['miglad_postalcode'];
			$subscription->billToCountry            = $map['miglad_country'];

			
			// Create the subscription.
			$request = new AuthorizeNetARB;
			$response = $request->createSubscription($subscription);
			$subscription_id = $response->getSubscriptionId();
			
			//error_log( $response->__toArray(true) , '', AUTHORIZENET_LOG_FILE );
			
			if( $subscription_id != '' )
			{
			  $result = '1';

			  $new_donation = array(
				 'post_title' => 'migla_donation',
				 'post_content' => '',
				 'post_status' => 'publish',
				 'post_author' => 1,
				 'post_type' => 'migla_donation'
			   );

				 $new_id = wp_insert_post( $new_donation );
				 $i = 0;
				 $keys = array_keys( $map );
				 foreach( (array)$map as $value)
				 {
					  add_post_meta( $new_id , $keys[$i], $value );
					  $i++;
				 }
	 
				 add_option($transientKey , $map);
				 
			   /*****  Add transaction data *******/
				 add_post_meta( $new_id, 'miglad_paymentmethod', 'credit card' );
				 add_post_meta( $new_id, 'miglad_transactionId', "" );
				 add_post_meta( $new_id, 'miglad_transactionType', 'Recurring (Authorize.NET)' );
				 update_post_meta( $new_id, 'miglad_session_id', $_POST['session'] ); 
				 add_post_meta( $new_id, 'miglad_subscription_id', $subscription_id );
				 add_post_meta( $new_id, 'miglad_payment_status', 'unsettled' );

				 $map['miglad_transactionType'] = 'Recurring (Authorize.NET)'; 

							/*** SEND EMAIL ****/

				$e = get_option('migla_replyTo');
				$en = get_option('migla_replyToName');
				$ne = get_option('migla_notif_emails');

				if( get_option('migla_disable_thank_email') != 'yes' ){	
				   mg_send_thank_you_email( $map , $e, $en );
				}
				mg_send_notification_emails( $map , $e, $en, $ne);
				
				if( isset($map['miglad_honoreeemail']) && get_option('migla_disable_honoree_email') != 'yes' )
				{	 
					mg_send_hletter( $map, $e, $en, $map['miglad_honoreeletter'], $map['miglad_date'] );
				}
				
				/*** MAIL LIST ****/
				 $_email = $map['miglad_email']; 
				 $_fname = $map['miglad_firstname']; 
				 $_lname = $map['miglad_lastname'];
				 $mailchimp_data['miglad_email']     = $map['miglad_email']; 
				 $mailchimp_data['miglad_firstname'] = $map['miglad_firstname'];
				 $mailchimp_data['miglad_lastname']  = $map['miglad_lastname'];

				 if( isset($map['miglad_mg_add_to_milist']) && $map['miglad_mg_add_to_milist'] == 'yes' )
				 {
					  $_add_milis = true;	
				 }else{
					  $_add_milis = false;	
				 }			
				
				$maillist_choice = get_option('migla_mail_list_choice');
				if( $maillist_choice == 'constant_contact' )
				{
					//add to Constant Contact
					$cc = new migla_constant_contact_class();
					$cc->add_to_milist_test( $_email, $_fname, $_lname, $_add_milis );

				 }else if( $maillist_choice == 'mail_chimp' )
				 {
					 //add to mailchimp
					 $cc = new migla_mailchimp_class();
					 $cc->subscribe_contact( $mailchimp_data , $_add_milis);
				 }

					$php_time_zone 	= date_default_timezone_get();				
					$default 		= get_option('migla_default_timezone');

					if( $default == 'Server Time' )
					{
						$gmt_offset = -get_option( 'gmt_offset' );
				
						if ($gmt_offset > 0) 
							$time_zone = 'Etc/GMT+' . $gmt_offset; 
						else		
							$time_zone = 'Etc/GMT' . $gmt_offset;    
				
						date_default_timezone_set( $time_zone );
					}else{
						date_default_timezone_set( $default );
					}
					add_post_meta( $new_id, 'miglad_timezone', $default );
					date_default_timezone_set( $php_time_zone );				 
				
		}
		else
		{
			$result = 'We encountered an error while processing your payment : '. $response->xml->messages->message->text  ;
		}

	}//CC Validator	
}
	
   echo $result;
   die();
}


  add_action("wp_ajax_miglaA_checkout_offline", "miglaA_checkout_offline");
  add_action("wp_ajax_nopriv_miglaA_checkout_offline", "miglaA_checkout_offline");

function miglaA_checkout_offline()
{		
	 // Repack the Default Field Post
        $arr = $_POST['donorinfo'] ;
        $map = array();

        $map['miglad_anonymous'] = 'no'; 
        $map['miglad_repeating'] = 'no'; 

        $new_donation = array(
		  'post_title' => 'migla_pending_offline_donation',
		  'post_content' => '',
		  'post_status' => 'publish',
		  'post_author' => 1,
		  'post_type' => 'migla_odonation_p'
        );

        $post_id = wp_insert_post( $new_donation );

        foreach( (array)$arr as $d)
        {
           $map[ esc_attr( $d[0] ) ] = esc_attr( $d[1] );
           add_post_meta( $post_id, esc_attr( $d[0] ), $map[ esc_attr( $d[0] ) ] );
        }
        add_post_meta( $post_id, 'miglad_status' , 'pending' );
        add_post_meta( $post_id, 'miglad_method' , 'online' );

        $transientKey =  "t_". esc_attr( $map['miglad_session_id'] );

     
       ///GET CURRENT TIME SETTINGS----------------------------------
	  $php_time_zone = date_default_timezone_get();
        $t = ""; $d = "";
        $default = get_option('migla_default_timezone');
		
        if( $default == 'Server Time' ){
		
          $gmt_offset = -get_option( 'gmt_offset' );

		    if ($gmt_offset > 0){ 
				$time_zone = 'Etc/GMT+' . $gmt_offset; 
			}else{		
				$time_zone = 'Etc/GMT' . $gmt_offset;    
			}
			  
		  date_default_timezone_set( $time_zone );
		  $t = date('H:i:s');
		  $d = date('m')."/".date('d')."/".date('Y');
		  
		}else{
		
		  date_default_timezone_set( $default );
		  $t = date('H:i:s');
		  $d = date('m')."/".date('d')."/".date('Y');
		  
        }
		
		date_default_timezone_set( $php_time_zone );
       ///---------------------------------GET CURRENT TIME SETTINGS
   
        $map['miglad_date'] = $d; 
        $map['miglad_time'] = $t; 

        add_post_meta( $post_id, 'miglad_date' , $d );
        add_post_meta( $post_id, 'miglad_time' , $t );
   
        add_option( $transientKey, $map );

    if(  $map['miglad_honoreeletter'] != '' )
    {
        $hletter =  $transientKey. "hletter";
        add_option($hletter , $map['miglad_honoreeletter'] );
    }

        $map['miglad_transactionType'] = 'Offline Donation';


                                   /*** MAIL LIST ****/

                          $_email = $map['miglad_email']; 
                          $_fname = $map['miglad_firstname']; 
                          $_lname = $map['miglad_lastname'];
                          $mailchimp_data['miglad_email']     = $map['miglad_email']; 
                          $mailchimp_data['miglad_firstname'] = $map['miglad_firstname'];
                          $mailchimp_data['miglad_lastname']  = $map['miglad_lastname'];

                            if( $map['miglad_mg_add_to_milist']  == 'yes' ){
                               $_add_milis = true;	
                            }else{
                               $_add_milis = false;	
                            }
    
                     $maillist_choice = get_option('migla_mail_list_choice');
                     if( $maillist_choice == 'constant_contact' )
                     {
                        include_once 'migla_class_constant_contact.php';
                        $cc = new migla_constant_contact_class();
                        $cc->add_to_milist_test( $_email, $_fname, $_lname, $_add_milis );

                     }else if( $maillist_choice == 'mail_chimp' )
                     {
                        //add to mailchimp
                        include_once 'migla_class_mailchimp.php';
                        $cc = new migla_mailchimp_class();
                        $cc->subscribe_contact( $mailchimp_data , $_add_milis);
                     }

            /*** SEND EMAIL ****/
			$e 	= get_option('migla_replyTo');
			$en = get_option('migla_replyToName');
			$ne = get_option('migla_notif_emails');
											
            if( get_option('migla_send_offmsg') == 'yes' )
			{
				mg_send_offline_first_email( $map , $e, $en );
			}
			
			mg_send_notification_emails( $map , $e, $en, $ne);  			

    echo $post_id;
    die();
}

  add_action("wp_ajax_miglaA_checkout", "miglaA_checkout");
  add_action("wp_ajax_nopriv_miglaA_checkout", "miglaA_checkout");

function miglaA_checkout()
{
	$message 		= '';
	$is_use_nonce 	=  get_option('migla_use_nonce') == 'yes';
	
	if( !wp_verify_nonce( $_POST['nonce'], 'migla_' ) && $is_use_nonce )
	{
		$message = __('Nonce is not recognize', 'migla-donation');
	}else{

		$message = '';
		// Repack the Default Field Post
        $arr = $_POST['donorinfo'] ;
        $map = array();

        $map['miglad_anonymous'] = 'no'; 
        $map['miglad_repeating'] = 'no'; 

        foreach( (array)$arr as $d)
        {
           $map[ esc_attr( $d[0] ) ] = esc_attr( $d[1] );
        }

        $transientKey =  "t_". esc_attr( $map['miglad_session_id'] );

     
       ///GET CURRENT TIME SETTINGS----------------------------------
	  $php_time_zone = date_default_timezone_get();
        $t = ""; $d = "";
        $default = get_option('migla_default_timezone');
		
        if( $default == 'Server Time' ){
		
          $gmt_offset = -get_option( 'gmt_offset' );

		    if ($gmt_offset > 0){ 
				$time_zone = 'Etc/GMT+' . $gmt_offset; 
			}else{		
				$time_zone = 'Etc/GMT' . $gmt_offset;    
			}
			  
		  date_default_timezone_set( $time_zone );
		  $t = date('H:i:s');
		  $d = date('m')."/".date('d')."/".date('Y');
		  
		}else{
		
		  date_default_timezone_set( $default );
		  $t = date('H:i:s');
		  $d = date('m')."/".date('d')."/".date('Y');
		  
        }
		
		date_default_timezone_set( $php_time_zone );
       ///---------------------------------GET CURRENT TIME SETTINGS
   
        $map['miglad_date'] = $d; 
        $map['miglad_time'] = $t; 

		add_option( $transientKey, $map );

		if(  $map['miglad_honoreeletter'] != '' )
		{
			$hletter =  $transientKey. "hletter";
			add_option($hletter , $map['miglad_honoreeletter'] );
		}

	}
	
    echo $message;
    die();   
}

  add_action("wp_ajax_miglaA_checkout_nonce", "miglaA_checkout_nonce");
  add_action("wp_ajax_nopriv_miglaA_checkout_nonce", "miglaA_checkout_nonce");

function miglaA_checkout_nonce()
{
  $msg ='';

   if ( wp_verify_nonce( $_POST['nonce'], 'migla_' ) )
   {
	 // Repack the Default Field Post
        $arr = $_POST['donorinfo'] ;
        $map = array();
        
        $map['miglad_anonymous'] = 'no'; 
        $map['miglad_repeating'] = 'no'; 

        foreach( (array)$arr as $d)
        {
          $map[ esc_attr( $d[0] ) ] = esc_attr( $d[1] );
        }

        $transientKey =  "t_". esc_attr( $map['miglad_session_id'] );

       ///GET CURRENT TIME SETTINGS----------------------------------
	  $php_time_zone = date_default_timezone_get();
        $t = ""; $d = "";
        $default = get_option('migla_default_timezone');
		
        if( $default == 'Server Time' ){
		
          $gmt_offset = -get_option( 'gmt_offset' );

		    if ($gmt_offset > 0){ 
				$time_zone = 'Etc/GMT+' . $gmt_offset; 
			}else{		
				$time_zone = 'Etc/GMT' . $gmt_offset;    
			}
			  
		  date_default_timezone_set( $time_zone );
		  $t = date('H:i:s');
		  $d = date('m')."/".date('d')."/".date('Y');
		  
		}else{
		
		  date_default_timezone_set( $default );
		  $t = date('H:i:s');
		  $d = date('m')."/".date('d')."/".date('Y');
		  
        }
		
		date_default_timezone_set( $php_time_zone );
       ///---------------------------------GET CURRENT TIME SETTINGS
   
        $map['miglad_date'] = $d; 
        $map['miglad_time'] = $t; 
   
	// Put the results in a transient. Expire after 12 hours.
        add_option( $transientKey, $map );

      if(  $map['miglad_honoreeletter'] != '' )
      {
        $hletter = $transientKey. "hletter" ;
        add_option($hletter , $map['miglad_honoreeletter'] );
      }

      $msg = '0';
   }else{
      $msg = '-1';
   }

    echo $msg;
    die();  
       
}

?>