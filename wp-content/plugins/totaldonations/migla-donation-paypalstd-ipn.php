<?php

include "../../../wp-config.php";

include_once './migla_class_email_handler.php';
include_once './migla-functions.php';

$maillist_choice = get_option('migla_mail_list_choice');
if( $maillist_choice == 'constant_contact' ){
    include_once './migla_class_constant_contact.php';
}
include_once './migla_class_mailchimp.php';

/************* CALLING HOOK ***************************/
$isHooked = get_option( 'miglaactions_2_1' );
if( $isHooked == 'yes' ){
   $url = get_option( 'migla_ipnrequire' );
   if( $url == '' ){ }else{ include( dirname(__FILE__). $url);}
}
/*******************************************************/


class migla_IPN_Handler {

    var $chat_back_url  = "tls://www.paypal.com";
	var $host_header    = "Host: www.paypal.com\r\n";
	var $session_id     = '';
        var $profileid      = '';

	public function __construct() {

		// Set up for production or test
		if ( "sandbox" == get_option( 'migla_payment' ) ) {
			$this->chat_back_url = "tls://www.sandbox.paypal.com";
			$this->host_header   = "Host: www.sandbox.paypal.com\r\n";
		}

		if( isset( $_POST[ 'custom' ] ) )
                {
                    if(  ! empty( $_POST[ 'custom' ] ) ){ 
                        $this->session_id = $_POST[ 'custom' ]; 
                    }
                }
               

		if ( ! empty( $this->session_id ) ) {

                   if( get_option('migla_ipn_chatback') == 'yes' )
                   {
			$response = $this->migla_to_paypal();

			if ( "VERIFIED" == $response ) {
				$this->handle_verified_ipn();
			} else if ( "INVALID" == $response ) {
				$this->handle_invalid_ipn();
			} else {
				$this->handle_unrecognized_ipn( $response );
			}
                    }else{
                         $this->handle_verified_ipn();
                    }
                }else {

                    //This is for paypal pro bro
                    if( isset( $_POST[ "rp_invoice_id" ] ) && $_POST["txn_type"] == "recurring_payment"  )
                    {
                         $this->session_id = $_POST[ 'rp_invoice_id' ] ;
                        
                         $response = $this->migla_to_paypal();

			 if ( "VERIFIED" == $response ) {
				$this->handle_verified_ipn_pro();
			 } else if ( "INVALID" == $response ) {
				$this->handle_invalid_ipn();
			 } else {
				$this->handle_unrecognized_ipn( $response );
			 }
                    }
	      }
	}

	function migla_to_paypal() {
		$req = 'cmd=_notify-validate';
		$get_magic_quotes_exists = function_exists( 'get_magic_quotes_gpc' );

		foreach ($_POST as $key => $value) {
			if( $get_magic_quotes_exists && get_magic_quotes_gpc() == 1 ) {
				$value = urlencode( stripslashes( $value ) );
			} else {
				$value = urlencode( $value );
			}
			$req .= "&$key=$value";
		}

		$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= $this->host_header;
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:12.0) Gecko/20100101 Firefox/21.0\r\n";
		$header .= "Content-Length: " . strlen( $req ) . "\r\n\r\n";

		$response = '';

		$fp = fsockopen( $this->chat_back_url, 443, $errno, $errstr, 30 );
		if ( $fp ) {
			fputs( $fp, $header . $req );

			$done = false;
			do {
				if ( feof( $fp ) ) {
					$done = true;
				} else {
					$response = fgets( $fp, 1024 );
					$done = in_array( $response, array( "VERIFIED", "INVALID" ) );
				}
			} while ( ! $done );
		} else {
		}
		fclose ($fp);

		return $response;
	}


        /*************This is a regular paypal****************************/
	function handle_verified_ipn() {
           $post_id = ""; 

           $_email =""; $_fname = ""; $_lname = ""; $_add_milis = false; $postData = ""; $mailchimp_data = array();

	   if ( "Completed" == $_POST['payment_status'] || "completed" == $_POST['payment_status'] )
       {

	       if(  $_POST[ 'txn_type' ] == 'subscr_payment' )
		   {
		      //This is where recurring goes
	
				$transientKey = "t_". $_POST[ 'custom' ];

				$e = get_option('migla_replyTo');
				$en = get_option('migla_replyToName');
				$ne = get_option('migla_notif_emails');
				
				///GET CURRENT TIME SETTINGS----------------------------------
				$php_time_zone = date_default_timezone_get();
				$t = ""; 
				$d = ""; 
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
	 			 
            $is_ongoing = migla_check_if_exist( 'miglad_subscription_id', $_POST['subscr_id'] );
            $pid_txn    = migla_check_if_exist( 'miglad_transactionId', $_POST['txn_id'] );			
		    $isPDT      = get_option('migla_using_pdt');
            $postData   = get_option( $transientKey );

	        if( $is_ongoing == -1 && $pid_txn == -1 ) //Initial Recurring
	        { 
		       if( $isPDT != 'yes' )
		       {	       	
      	               			   
                    if( $postData == false ){ //cache not found

                           $post_id = migla_create_post(); //Make A POST

                            // migla_donation_from_paypal($post_id, $post);
                     	   add_post_meta( $post_id, 'miglad_session_id', $_POST['custom'] );
                           add_post_meta( $post_id, 'miglad_firstname', $_POST['first_name'] );
                           add_post_meta( $post_id, 'miglad_lastname', $_POST['last_name'] );

                           $amountfrompaypal = $_POST['payment_gross'] ;
                           if( $amountfrompaypal == '' ){ 
                              $amountfrompaypal = $_POST['mc_gross']; 
                           }
                           add_post_meta( $post_id, 'miglad_amount', $amountfrompaypal );

                           add_post_meta( $post_id, 'miglad_phone', $_POST['contact_phone'] );
                           add_post_meta( $post_id, 'miglad_country', $_POST['address_country'] );
                           add_post_meta( $post_id, 'miglad_address', $_POST['address_street'] );
                           add_post_meta( $post_id, 'miglad_email', $_POST['payer_email'] );
                           add_post_meta( $post_id, 'miglad_city', $_POST['address_city'] ); 
                           add_post_meta( $post_id, 'miglad_state', $_POST['address_state'] ); 

                            //Additional data
                             add_post_meta( $post_id, "miglad_time" , $t );
                             add_post_meta( $post_id, "miglad_date" , $d );
						   
							   //Save data from paypal
							   add_post_meta( $post_id, 'miglad_paymentmethod', 'Paypal' );
							   add_post_meta( $post_id, 'miglad_paymentdata', $_POST );
							   add_post_meta( $post_id, 'miglad_transactionId', $_POST['txn_id'] );
							   add_post_meta( $post_id, 'miglad_timezone', $default );

								   add_post_meta( $post_id, 'miglad_transactionType', 'Recurring (Paypal)' );
								   add_post_meta( $post_id, 'miglad_subscription_id', $_POST['subscr_id'] ); 

                                $_email           = $_POST['payer_email']; 
                                $_fname           = $_POST['first_name']; 
                                $_lname           = $_POST['last_name'];	
                                $mailchimp_data['miglad_email']     = $_POST['payer_email'];
                                $mailchimp_data['miglad_firstname'] = $_POST['first_name']; 
                                $mailchimp_data['miglad_lastname']  = $_POST['last_name'];	

	                     /** EMAIL **/
						 $email_data = array();
						 $email_data['miglad_firstname']    =  $_POST['first_name']; 
						 $email_data['miglad_lastname']     =  $_POST['last_name'];
						 $email_data['miglad_email']        =  $_POST['payer_email'];  
						 $email_data['miglad_amount']       =  $amountfrompaypal; 
						 $email_data['miglad_date']         =  $d; 
						 $email_data['miglad_time']         =  $t; 
						 $email_data['miglad_campaign']     =  ''; 
						 mg_send_thank_you_email( $email_data, $e, $en );
						 mg_send_notification_emails( $email_data, $e, $en , $ne );
								
						}else{

                             $post_id = migla_create_post(); //Make A POST

							 $i = 0; 
							 $keys = array_keys( $postData );
							 foreach( (array)$postData as $value)
							 {
								  add_post_meta( $post_id, $keys[$i], $value );  $i++;
							  }
							  
                           $amountfrompaypal = $_POST['payment_gross'] ;
                           if( $amountfrompaypal == '' ){ 
                              $amountfrompaypal = $_POST['mc_gross']; 
                           }
                           update_post_meta( $post_id, 'miglad_amount', $amountfrompaypal );							  

							   update_post_meta( $post_id, 'miglad_time', $t ); 
							   update_post_meta( $post_id, 'miglad_date', $d ); 
									 
							   //Save data from paypal
							   add_post_meta( $post_id, 'miglad_paymentmethod', 'Paypal' );
							   add_post_meta( $post_id, 'miglad_paymentdata', $_POST );
							   add_post_meta( $post_id, 'miglad_transactionId', $_POST['txn_id'] );
							   add_post_meta( $post_id, 'miglad_timezone', $default );

								   add_post_meta( $post_id, 'miglad_transactionType', 'Recurring (Paypal)' );
								   add_post_meta( $post_id, 'miglad_subscription_id', $_POST['subscr_id'] ); 

							/*** EMAILS ***/
							/*
							 mg_send_thank_you_email( $postData, $e, $en );
							 mg_send_notification_emails( $postData, $e, $en , $ne );
						 
							   $tdata =  $transientKey. "hletter";
							   $content =  get_option( $tdata );

							   migla_hletter( $e, $en , $postData['miglad_honoreeemail'], $content, $postData['miglad_repeating']
									, $postData['miglad_anonymous'], $postData['miglad_firstname'], $postData['miglad_lastname'], 
									 $postData['miglad_amount'], $postData['miglad_honoreename'] , $d );	
							*/

							if( get_option('migla_disable_thank_email') != 'yes' )		
								mg_send_thank_you_email( $postData, $e, $en  );
							
							mg_send_notification_emails( $postData, $e, $en , $ne);

							if( isset($postData['miglad_honoreeemail']) && get_option('migla_disable_honoree_email') != 'yes' )
							{	 			
								mg_send_hletter( $postData, $e, $en, $postData['miglad_honoreeletter'], $postData['miglad_date'] );
							}							
								   
							  $_email = $postData['miglad_email']; 
                              $_fname = $postData['miglad_firstname']; 
                              $_lname = $postData['miglad_lastname'];
                              $mailchimp_data['miglad_email']     = $postData['miglad_email']; 
                              $mailchimp_data['miglad_firstname'] = $postData['miglad_firstname'];
                              $mailchimp_data['miglad_lastname']  = $postData['miglad_lastname'];

                              if( $postData['miglad_mg_add_to_milist']  == 'yes' ){
                                  $_add_milis = true;	
                              }else{
                                  $_add_milis = false;	
                              }										 
				
						}	                       
			    }//Not PDT	

                     /*** MAIL LIST ****/
                     $maillist_choice = get_option('migla_mail_list_choice');
                     if( $maillist_choice == 'constant_contact' )
                     {
                        //add to Constant Contact
                        //mg_add_to_milist( $_email, $_fname, $_lname, $_add_milis );
                        $cc = new migla_constant_contact_class();
                        $cc->add_to_milist( $_email, $_fname, $_lname, $_add_milis );

                     }else if( $maillist_choice == 'mail_chimp' )
                     {
                        //add to mailchimp
                        $cc = new migla_mailchimp_class();
                        $cc->subscribe_contact( $mailchimp_data , $_add_milis);
                     }						 						 
					
			}else if( $is_ongoing != -1 && $pid_txn == -1 ) //Next Recurring
			{ 

                $post_id = migla_create_post(); //Make A POST
                migla_create_from_old_donation( $is_ongoing , $post_id );

                //Additional data
                add_post_meta( $post_id, "miglad_time" , $t );
                add_post_meta( $post_id, "miglad_date" , $d );			                               
								 
                //Save data from paypal
                add_post_meta( $post_id, 'miglad_paymentmethod', 'Paypal' );
                add_post_meta( $post_id, 'miglad_paymentdata', $_POST );
                add_post_meta( $post_id, 'miglad_transactionId', $_POST['txn_id'] );
                add_post_meta( $post_id, 'miglad_timezone', $default );

                add_post_meta( $post_id, 'miglad_transactionType', 'Recurring (Paypal)' );
                add_post_meta( $post_id, 'miglad_subscription_id', $_POST['subscr_id'] ); 
				
				/*** EMAILS ***/
				$email_data = array();
				$email_data['miglad_firstname']    =  get_post_meta( $post_id, 'miglad_firstname', true );
				$email_data['miglad_lastname']     =  get_post_meta( $post_id, 'miglad_lastname', true );
				$email_data['miglad_email']        =  get_post_meta( $post_id, 'miglad_email', true ); 
				$email_data['miglad_amount']       =  get_post_meta( $post_id, 'miglad_amount', true ); 
				$email_data['miglad_date']         =  $d; 
				$email_data['miglad_time']         =  $t; 
				$email_data['miglad_campaign']     =  get_post_meta( $post_id, 'miglad_campaign', true );				

						/*
						mg_send_thank_you_email( $email_data, $e, $en );
						mg_send_notification_emails( $email_data, $e, $en , $ne );			
						*/
							if( get_option('migla_disable_thank_email') != 'yes' )		
								mg_send_thank_you_email( $email_data, $e, $en   );
							
							mg_send_notification_emails( $email_data, $e, $en , $ne );					

		      } //Detect If it is intial or not
			  
		   }else{

		      /****ONE TIME DONATION GOES HERE**/

              $isPDT = get_option('migla_using_pdt');

              if( $isPDT != 'yes' )
              {
                     
				$transientKey = "t_". $_POST[ 'custom' ];

				$e = get_option('migla_replyTo');
				$en = get_option('migla_replyToName');
				$ne = get_option('migla_notif_emails');
				
				   ///GET CURRENT TIME SETTINGS----------------------------------
					$php_time_zone = date_default_timezone_get();
						$t = ""; $d = ""; $default = "";
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
	 			 
  	                $postData  = get_option( $transientKey );
                    $isIDExist =  migla_check_if_exist( 'miglad_transactionId', $_POST['txn_id'] );	
					
				 if( $postData == false && $isIDExist == -1 ){
				    //It lost its transient data
				
				    $post_id = migla_create_post(); //Make A POST
				
                           // migla_donation_from_paypal($post_id, $post);
                     	   add_post_meta( $post_id, 'miglad_session_id', $_POST['custom'] );
                           add_post_meta( $post_id, 'miglad_firstname', $_POST['first_name'] );
                           add_post_meta( $post_id, 'miglad_lastname', $_POST['last_name'] );

                           $amountfrompaypal = $_POST['payment_gross'] ;
                           if( $amountfrompaypal == '' ){ 
                              $amountfrompaypal = $_POST['mc_gross']; 
                           }
                           add_post_meta( $post_id, 'miglad_amount', $amountfrompaypal );

                           add_post_meta( $post_id, 'miglad_phone', $_POST['contact_phone'] );
                           add_post_meta( $post_id, 'miglad_country', $_POST['address_country'] );
                           add_post_meta( $post_id, 'miglad_address', $_POST['address_street'] );
                           add_post_meta( $post_id, 'miglad_email', $_POST['payer_email'] );
                           add_post_meta( $post_id, 'miglad_city', $_POST['address_city'] ); 
                           add_post_meta( $post_id, 'miglad_state', $_POST['address_state'] ); 

                            //Additional data
                             add_post_meta( $new_id, "miglad_time" , $t );
                             add_post_meta( $new_id, "miglad_date" , $d );
						   
							   //Save data from paypal
							   add_post_meta( $post_id, 'miglad_paymentmethod', 'Paypal' );
							   add_post_meta( $post_id, 'miglad_paymentdata', $_POST );
							   add_post_meta( $post_id, 'miglad_transactionId', $_POST['txn_id'] );
							   add_post_meta( $post_id, 'miglad_timezone', $default );

								   add_post_meta( $post_id, 'miglad_transactionType', 'One time (Paypal)' );


                            $_email = $_POST['payer_email']; 
                            $_fname = $_POST['first_name']; 
                            $_lname = $_POST['last_name'];
                                $mailchimp_data['miglad_email']     = $_POST['payer_email'];
                                $mailchimp_data['miglad_firstname'] = $_POST['first_name']; 
                                $mailchimp_data['miglad_lastname']  = $_POST['last_name'];	
								
					/** EMAILS **/			
						 $email_data = array();
						 $email_data['miglad_firstname']    =  $_POST['first_name']; 
						 $email_data['miglad_lastname']     =  $_POST['last_name'];
						 $email_data['miglad_email']        =  $_POST['payer_email'];  
						 $email_data['miglad_amount']       =  $amountfrompaypal; 
						 $email_data['miglad_date']         =  $d; 
						 $email_data['miglad_time']         =  $t; 
						 $email_data['miglad_campaign']     =  ''; 
					
						/*
						mg_send_thank_you_email( $email_data, $e, $en );
						mg_send_notification_emails( $email_data, $e, $en , $ne );			
						*/
							if( get_option('migla_disable_thank_email') != 'yes' )		
								mg_send_thank_you_email( $email_data, $e, $en   );
							
							mg_send_notification_emails( $email_data, $e, $en , $ne );
					
					
				 }else{
				    //it has its transient data
					
                    if( $isIDExist == -1 ){ //Check if this already saved

                        $post_id = migla_create_post(); //Make A POST
  
                         $i = 0; 
                         $keys = array_keys( $postData );
                         foreach( (array)$postData as $value)
                         {
                              add_post_meta( $post_id, $keys[$i], $value );
                            $i++;
                          }
						  
                         $amountfrompaypal = $_POST['payment_gross'] ;
                           if( $amountfrompaypal == '' ){ 
                              $amountfrompaypal = $_POST['mc_gross']; 
                           }
                          update_post_meta( $post_id, 'miglad_amount', $amountfrompaypal );						  
                                 
                           //Save data from paypal
                           add_post_meta( $post_id, 'miglad_paymentmethod', 'Paypal' );
                           add_post_meta( $post_id, 'miglad_paymentdata', $_POST );
                           add_post_meta( $post_id, 'miglad_transactionId', $_POST['txn_id'] );
                           add_post_meta( $post_id, 'miglad_timezone', $default );

                               add_post_meta( $post_id, 'miglad_transactionType', 'One time (Paypal)' );

                            $_email = $postData['miglad_email']; 
                            $_fname = $postData['miglad_firstname']; 
                            $_lname = $postData['miglad_lastname'];
                              $mailchimp_data['miglad_email']     = $postData['miglad_email']; 
                              $mailchimp_data['miglad_firstname'] = $postData['miglad_firstname'];
                              $mailchimp_data['miglad_lastname']  = $postData['miglad_lastname'];

                            if( $postData['miglad_mg_add_to_milist']  == 'yes' ){
                               $_add_milis = true;	
                            }else{
                               $_add_milis = false;	
                            }
							
							/*** EMAILS ***/
							/*		
							mg_send_thank_you_email( $postData, $e, $en );
							 mg_send_notification_emails( $postData, $e, $en , $ne );
						 
							   $tdata =  $transientKey. "hletter";
							   $content =  get_option( $tdata );

							   migla_hletter( $e, $en , $postData['miglad_honoreeemail'], $content, $postData['miglad_repeating']
									, $postData['miglad_anonymous'], $postData['miglad_firstname'], $postData['miglad_lastname'], 
									 $postData['miglad_amount'], $postData['miglad_honoreename'] , $d );		
							*/
							
							if( get_option('migla_disable_thank_email') != 'yes' )		
								mg_send_thank_you_email( $postData, $e, $en  );
							
							mg_send_notification_emails( $postData, $e, $en , $ne);

							if( isset($postData['miglad_honoreeemail']) && get_option('migla_disable_honoree_email') != 'yes' )
							{	 			
								mg_send_hletter( $postData, $e, $en, $postData['miglad_honoreeletter'], $postData['miglad_date'] );
							}								

                    }else{
                         //Do Nothing, it is already saved on database  
                         add_option('migla_Error'.time(), 'Seems it thinks duplicate with ID '.$isIDExist );						 
                    }			
                    		
		     } //HAS TRANSIENT
		     
		     } //IS PDT  
		     
                     /*** MAIL LIST ****/
                     $maillist_choice = get_option('migla_mail_list_choice');
                     if( $maillist_choice == 'constant_contact' )
                     {
                        //add to Constant Contact
                        //mg_add_to_milist( $_email, $_fname, $_lname, $_add_milis );
                        $cc = new migla_constant_contact_class();
                        $cc->add_to_milist( $_email, $_fname, $_lname, $_add_milis );

                     }else if( $maillist_choice == 'mail_chimp' )
                     {
                        //add to mailchimp
                        $cc = new migla_mailchimp_class();
                        $cc->subscribe_contact( $mailchimp_data , $_add_milis);
                     }

		   }//Transaction type switch
		   
	  }else{ //IF Status is not completed
				   
	  } // If $payment_status
	}//function


	function handle_invalid_ipn() {
	}

	function handle_unrecognized_ipn( $paypal_response ) {
	}

/****************************************************************************************/
	function handle_verified_ipn_pro(){

	   $payment_status = $_POST['payment_status'];
       
          if ( "Completed" == $payment_status  ) {

              $e = get_option('migla_replyTo');
              $en = get_option('migla_replyToName');
              $ne = get_option('migla_notif_emails');
			
              ///GET CURRENT TIME SETTINGS----------------------------------
	      $php_time_zone = date_default_timezone_get();
              $t = ""; $d = ""; $default = "";
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
             
              $post_id = migla_create_post();
              $ref_id = $_POST[ 'rp_invoice_id' ];
              $transientKey = "t_migla" . $ref_id ;
              $postData = get_option( $transientKey );


                    $new_id = $post_id;

                    $session_id = "migla". $ref_id;

                    //1 Cek if this donation session id exist
                    $old_ids =  migla_cek_repeating_id( $session_id );


                              migla_create_from_old_donation( $old_ids, $new_id);

                              //Additional data
                              add_post_meta( $new_id, "miglad_time" , $t );
                              add_post_meta( $new_id, "miglad_date" , $d );

					sendThankYouEmailRepeating( $new_id, $e, $en );
					sendNotifEmailRepeating( $new_id, $e, $en, $ne);
 
					/*		   
                    $tdata =  $transientKey. "hletter";
                    $content =  get_option( $tdata );

                    migla_hletter( $e, $en , $postData['miglad_honoreeemail'], $content, $postData['miglad_repeating']
                                , $postData['miglad_anonymous'], $postData['miglad_firstname'], $postData['miglad_lastname'], 
                                 $postData['miglad_amount'], $postData['miglad_honoreename'] , $d );
					*/

                  //Save data from paypal
                   add_post_meta( $post_id, 'miglad_paymentmethod', 'Credit Card' );
                   add_post_meta( $post_id, 'miglad_paymentdata', $_POST );
                   add_post_meta( $post_id, 'miglad_transactionId', $_POST['TRANSACTIONID'] );
                   add_post_meta( $post_id, 'miglad_transactionType', 'Recurring (Paypal Pro)' );
                   add_post_meta( $post_id, 'miglad_timezone', $default );
                   add_post_meta( $post_id, 'miglad_desc', $_POST['desc'] );
                   add_post_meta( $post_id, 'miglad_preference', $_POST['rp_invoice_id'] );
                   add_post_meta( $post_id, 'miglad_subscription_id', $_POST['recurring_payment_id'] ); 
				   
	   } // If $payment_status
    }

}

$migla_paypal_responder = new migla_IPN_Handler();

echo "content-type: text/plain\n\n";
?>