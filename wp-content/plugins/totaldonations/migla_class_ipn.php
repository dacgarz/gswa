<?php

class migla_front_ipn_handler{

  public function __construct()
  {

  }

public function migla_get_id( $meta_value ) {
    global $wpdb;
    $pid = $wpdb->get_var( $wpdb->prepare(
           "SELECT ID FROM $wpdb->posts WHERE post_title = %s ORDER BY ID ASC"
            ,$meta_value  ));
    if( $pid != '' )
        return $pid;
    else 
        return -1;
}

public function migla_create_post( $sessionid ) 
{
  global $wpdb;
  $wpdb->insert("wp_posts", array(
	  'post_title' => $sessionid,
	  'post_content' => '',
	  'post_status' => 'publish',
	  'post_type' => 'migla_donation'
   ));
}

	
   public function migla_cek_repeating_id( $meta_value ) 
   {
      global $wpdb;
      $pid = $wpdb->get_var( $wpdb->prepare(
           "SELECT post_id FROM $wpdb->postmeta WHERE meta_value = %s AND meta_key = 'miglad_session_id' ORDER BY post_id ASC"
            ,$meta_value  ));
      if( $pid != '' )
        return $pid;
      else 
        return -1;
   }	

	public function migla_check_if_exist( $meta_key, $meta_value ) {
		global $wpdb;
		$pid = $wpdb->get_var( $wpdb->prepare(
			   "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value = %s ORDER BY post_id ASC"
				, $meta_key, $meta_value  ));
		if( $pid != '' )
			return $pid;
		else 
			return -1;
	}   
   
	public function migla_create_from_old_donation( $old_id, $new_id)
	{
	   global $wpdb;

	   //get data from old id
	   $sql = "SELECT distinct meta_key,meta_value FROM {$wpdb->prefix}postmeta WHERE post_id = '".$old_id."' ";
	   $post = array(); $result = array(); $i = 0;
	   $post = $wpdb->get_results($sql);
	   
	   //insert into new id
	   foreach ( $post as $id ){
		$key = (string)$id->meta_key;
		if( $key == "miglad_paymentdata" || $key == "miglad_paymentmethod" || 
			 $key == "miglad_time" || $key == "miglad_date" || 
			 $key == "miglad_transactionType" || $key == "miglad_transactionId"
		 ){
		   // add_post_meta( $new_id, $key , $post[$key] );     
		 }else{
			add_post_meta( $new_id, (string)$id->meta_key , (string)$id->meta_value );
		 }
	   }//foreach  

	}
   

public function migla_paypal_ipn_frontend()
{
   $chat_back_url  =  'https://www.paypal.com/cgi-bin/webscr'; //  'ssl://www.paypal.com' ;
   $host_header    =  "Host: www.paypal.com\r\n";
   $session_id     = '';
   $profileid      = '';

		// Set up for production or test
		if ( "sandbox" == get_option( 'migla_payment' ) ) {
			$chat_back_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';  //  'ssl://www.sandbox.paypal.com' ;  
			$host_header   = "Host: www.sandbox.paypal.com\r\n";
		 }

		if( isset( $_POST[ 'custom' ] ) )
                {
                    if(  ! empty( $_POST[ 'custom' ] ) ){ 
                        $session_id = $_POST[ 'custom' ]; 
                    }
                }
               

	      if ( !empty( $session_id ) ) 
          {		  
		    if( get_option('migla_ipn_chatback') == 'yes' )
			{
			    $response = $this->migla_to_paypal( $chat_back_url, $host_header );

				if ( strcmp ($response , "VERIFIED") == 0 ) {
					$this->migla_handle_verified_ipn( $_POST );
				} else if ( strcmp ($response , "INVALID" ) == 0 ) {
					
				} else {
				    
				}
			}else{
			    $this->migla_handle_verified_ipn( $_POST );	
			}	
          }else {

                    //This is for paypal pro bro
                    if( isset( $_POST[ "rp_invoice_id" ] ) && $_POST["txn_type"] == "recurring_payment"  )
                    {
                        $this->session_id = $_POST[ 'rp_invoice_id' ] ;  
						if( get_option('migla_ipn_chatback_paypal') == 'yes' )
						{						 
							 $response = $this->migla_to_paypal(  $chat_back_url, $host_header );

							 if ( strcmp ($response , "VERIFIED") == 0 ) {
								$this->handle_verified_ipn_pro( $_POST );
							 } else if ( strcmp ($response , "INVALID" ) == 0 ) {
								
							 } else {
								
							 }
						}else{
						     $this->handle_verified_ipn_pro( $_POST );
						}
                    }  
                
	      }//IF EMPTY SESSION

}

public function migla_to_paypal( $chat_back_url, $host_header )
{
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
		$header .= $host_header;
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:12.0) Gecko/20100101 Firefox/21.0\r\n";
		$header .= "Content-Length: " . strlen( $req ) . "\r\n\r\n";

		$response = '';

		$fp = fsockopen( $chat_back_url, 443, $errno, $errstr, 30 );
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


public function migla_handle_verified_ipn( $post )
{
  $maillist_choice = get_option('migla_mail_list_choice');
  
  if( $maillist_choice == 'constant_contact' )
  {
    include_once 'migla_class_constant_contact.php';
  }
  include_once 'migla_class_mailchimp.php';
  include_once 'migla-functions.php';

   add_option('migla_paypal_ipn'.time(),  $post );

  $post_id      = ""; 
  $transientKey = "t_". $post['custom'];
  $_email       = ""  ; 
  $_fname       = "" ; 
  $_lname       = "" ; 
  $_add_milis   = false; 
  $postData     = ""; 
  $mailchimp_data = array();

  if ( "Completed" == $post['payment_status'] || "completed" == $post['payment_status'] )
  {				
	 ///GET CURRENT TIME SETTINGS----------------------------------
	$php_time_zone = date_default_timezone_get();
	$t = ""; 
	$d = ""; 
	$default = "";
	$default = get_option('migla_default_timezone');
	
	if( $default == 'Server Time' )
	{
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
  
     if(  $post['txn_type'] == 'subscr_payment' )
     { 			 
        $is_ongoing = $this->migla_check_if_exist( 'miglad_subscription_id', $post['subscr_id'] );
        $pid_txn    = $this->migla_check_if_exist( 'miglad_transactionId', $post['txn_id'] );				 
        $postData 	= get_option( $transientKey );

	            if( $is_ongoing == -1 && $pid_txn == -1 ) //Initial Recurring    	
      	        {       			   
                       if( $postData == false )
					   { //Lost its cache

			              $this->migla_create_post( $post['custom'] );	
			              $post_id = $this->migla_get_id( $post['custom'] ); // $post_id =  migla_create_post_2(); //Make A POST

                            // migla_donation_from_paypal($post_id, $post);
                     	   add_post_meta( $post_id, 'miglad_session_id', $post['custom'] );
                           add_post_meta( $post_id, 'miglad_firstname', $post['first_name'] );
                           add_post_meta( $post_id, 'miglad_lastname', $post['last_name'] );

                           $amountfrompaypal = $post['payment_gross'] ;
                           if( $amountfrompaypal == '' ){ 
                              $amountfrompaypal = $post['mc_gross']; 
                           }
                           add_post_meta( $post_id, 'miglad_amount', $amountfrompaypal );

                           add_post_meta( $post_id, 'miglad_phone', $post['contact_phone'] );
                           add_post_meta( $post_id, 'miglad_country', $post['address_country'] );
                           add_post_meta( $post_id, 'miglad_address', $post['address_street'] );
                           add_post_meta( $post_id, 'miglad_email', $post['payer_email'] );
                           add_post_meta( $post_id, 'miglad_city', $post['address_city'] ); 
                           add_post_meta( $post_id, 'miglad_state', $post['address_state'] ); 

                            //Additional data
                             add_post_meta( $post_id, "miglad_time" , $t );
                             add_post_meta( $post_id, "miglad_date" , $d );
    
						   
							   //Save data from paypal
							   add_post_meta( $post_id, 'miglad_paymentmethod', 'Paypal' );
							   add_post_meta( $post_id, 'miglad_paymentdata', $post );
							   add_post_meta( $post_id, 'miglad_transactionId', $post['txn_id'] );
							   add_post_meta( $post_id, 'miglad_timezone', $default );

							  //Check what type is it
								   add_post_meta( $post_id, 'miglad_transactionType', 'Recurring (Paypal)' );
								   add_post_meta( $post_id, 'miglad_subscription_id', $post['subscr_id'] ); 
                            
                                $post_data = array();
                                $post_data['miglad_firstname'] = $post['first_name'];
                                $post_data['miglad_lastname']  = $post['last_name'];
                                $post_data['miglad_email']     = $post['payer_email'];
                                $post_data['miglad_amount']    = $amountfrompaypal;
                                $post_data['miglad_address']   = $post['address_street'];
                                $post_data['miglad_country']   = $post['address_country'];
                                $post_data['miglad_city']      = $post['address_city'];
								$post_data['miglad_transactionType']      = 'Recurring (Paypal)';
								$post_data['miglad_date']      = $d;
								$post_data['miglad_time']      = $t;

                                 /*** SEND EMAIL ****/
                                 $e = get_option('migla_replyTo');
                                 $en = get_option('migla_replyToName');
                                 $ne = get_option('migla_notif_emails');

                                 mg_send_thank_you_email( $post_data , $e, $en );
                                 mg_send_notification_emails( $post_data , $e, $en, $ne);
								 
							 /*** Repack Mailist data ****/
                              $_email = $post_data['miglad_email'] ; 
                              $_fname = $post_data['miglad_firstname']; 
                              $_lname = $post_data['miglad_firstname'];
                              $mailchimp_data['miglad_email']     = $post_data['miglad_email'] ; 
                              $mailchimp_data['miglad_firstname'] = $post_data['miglad_firstname'];
                              $mailchimp_data['miglad_lastname']  = $post_data['miglad_firstname'];

								if( $post_data['miglad_mg_add_to_milist']  == 'yes' ){
								   $_add_milis = true;	
								}else{
								   $_add_milis = false;	
								}								 
						   					   
						}else{

			              $this->migla_create_post( $post['custom'] );	
			              $post_id = $this->migla_get_id( $post['custom'] ); 

							 $i = 0; 
							 $keys = array_keys( $postData );
							 foreach( (array)$postData as $value)
							 {
								  add_post_meta( $post_id, $keys[$i], $value );  $i++;
							  }

							  $amountfrompaypal = $post['payment_gross'] ;
                              if( $amountfrompaypal == '' ){ 
                                  $amountfrompaypal = $post['mc_gross']; 
                              }
                               update_post_meta( $post_id, 'miglad_amount', $amountfrompaypal );							  

							   update_post_meta( $post_id, 'miglad_time', $t ); 
							   update_post_meta( $post_id, 'miglad_date', $d ); 
								
							   //Save data from paypal
							   add_post_meta( $post_id, 'miglad_paymentmethod', 'Paypal' );
							   add_post_meta( $post_id, 'miglad_paymentdata', $post );
							   add_post_meta( $post_id, 'miglad_transactionId', $post['txn_id'] );
							   add_post_meta( $post_id, 'miglad_timezone', $default );

							  //Check what type is it
							  add_post_meta( $post_id, 'miglad_transactionType', 'Recurring (Paypal)' );
							   add_post_meta( $post_id, 'miglad_subscription_id', $post['subscr_id'] ); 

							   	$postData['miglad_date']      = $d;
								$postData['miglad_time']      = $t;
								$postData['miglad_transactionType']      = 'Recurring (Paypal)';

                                 /*** SEND EMAIL ****/
                                 $e = get_option('migla_replyTo');
                                 $en = get_option('migla_replyToName');
                                 $ne = get_option('migla_notif_emails');

                                 mg_send_thank_you_email( $postData  , $e, $en );
                                 mg_send_notification_emails( $postData  , $e, $en, $ne);	
								 
                                 $tdata   =  $transientKey . "hletter";
                                 $content =  get_option( $tdata );
                                 mg_send_hletter( $postData, $e, $en, $content, $d );								 
								 
							   /*** Repack Mailist data ****/
                              $_email = $postData['miglad_email'] ; 
                              $_fname = $postData['miglad_firstname']; 
                              $_lname = $postData['miglad_firstname'];
                              $mailchimp_data['miglad_email']     = $postData['miglad_email'] ; 
                              $mailchimp_data['miglad_firstname'] = $postData['miglad_firstname'];
                              $mailchimp_data['miglad_lastname']  = $postData['miglad_firstname'];

                            if( $postData['miglad_mg_add_to_milist']  == 'yes' ){
                               $_add_milis = true;	
                            }else{
                               $_add_milis = false;	
                            }
							  
						}
						
						 /*** MAIL LIST ****/
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
 			                 $this->migla_create_post( $txn_new );	
			                 $post_id = $this->migla_get_id( $txn_new );
                             $this->migla_create_from_old_donation( $isIDExist, $post_id );

                             //Additional data
                             add_post_meta( $post_id, "miglad_time" , $t );
                             add_post_meta( $post_id, "miglad_date" , $d );
								 
                             //Save data from paypal
                             add_post_meta( $post_id, 'miglad_paymentmethod', 'Paypal' );
                             add_post_meta( $post_id, 'miglad_paymentdata', $post );
                             add_post_meta( $post_id, 'miglad_transactionId', $post['txn_id'] );
                             add_post_meta( $post_id, 'miglad_timezone', $default );

                             //Check what type is it
                                add_post_meta( $post_id, 'miglad_transactionType', 'Recurring (Paypal)' );
                                add_post_meta( $post_id, 'miglad_subscription_id', $post['subscr_id'] ); 
	

                          /*** SEND EMAIL ****/
                          $e = get_option('migla_replyTo');
                          $en = get_option('migla_replyToName');
                          $ne = get_option('migla_notif_emails');

                          $post_data = array();
                          $post_data['miglad_firstname'] = get_post_meta( $post_id , 'miglad_firstname', true);
                          $post_data['miglad_lastname']  = get_post_meta( $post_id , 'miglad_lastname', true);
                          $post_data['miglad_email']     = get_post_meta( $post_id , 'miglad_email', true);
                          $post_data['miglad_amount']    = get_post_meta( $post_id , 'miglad_amount', true);
                          $post_data['miglad_address']   = get_post_meta( $post_id , 'miglad_address', true);
                          $post_data['miglad_country']   = get_post_meta( $post_id , 'miglad_country', true);
                          $post_data['miglad_city']      = get_post_meta( $post_id , 'miglad_city', true);
                          $post_data['miglad_date']      = $d;
                          $post_data['miglad_time']      = $t;
			              $post_data['miglad_transactionType']      = 'Recurring (Paypal)';

                          mg_send_thank_you_email( $post_data , $e, $en );
                          mg_send_notification_emails( $post_data , $e, $en, $ne);
	
					} //Detect If it is intial or not
	 	 
     }else{  //One donation
     	 			 
  	    $postData = get_option( $transientKey );
        $isIDExist =  $this->migla_cek_repeating_id( $post['custom'] );
				
		if( $postData == false && $isIDExist == -1 )
		{
				    //It lost its transient data
			$this->migla_create_post( $post['custom'] );	
			$post_id = $this->migla_get_id( $post['custom'] ); 
                    	
                           // migla_donation_from_paypal($post_id, $post);
                     	   add_post_meta( $post_id, 'miglad_session_id', $post['custom'] );
                           add_post_meta( $post_id, 'miglad_firstname', $post['first_name'] );
                           add_post_meta( $post_id, 'miglad_lastname', $post['last_name'] );

                           $amountfrompaypal = $post['payment_gross'] ;
                           if( $amountfrompaypal == '' ){ 
                              $amountfrompaypal = $post['mc_gross']; 
                           }
                           add_post_meta( $post_id, 'miglad_amount', $amountfrompaypal );

                           add_post_meta( $post_id, 'miglad_phone', $post['contact_phone'] );
                           add_post_meta( $post_id, 'miglad_country', $post['address_country'] );
                           add_post_meta( $post_id, 'miglad_address', $post['address_street'] );
                           add_post_meta( $post_id, 'miglad_email', $post['payer_email'] );
                           add_post_meta( $post_id, 'miglad_city', $post['address_city'] ); 
                           add_post_meta( $post_id, 'miglad_state', $post['address_state'] ); 

                            //Additional data
                             add_post_meta( $new_id, "miglad_time" , $t );
                             add_post_meta( $new_id, "miglad_date" , $d );
    
						   
							   //Save data from paypal
							   add_post_meta( $post_id, 'miglad_paymentmethod', 'Paypal Front-End' );
							   add_post_meta( $post_id, 'miglad_paymentdata', $post );
							   add_post_meta( $post_id, 'miglad_transactionId', $post['txn_id'] );
							   add_post_meta( $post_id, 'miglad_timezone', $default );

							  //Check what type is it
								add_post_meta( $post_id, 'miglad_transactionType', 'One time (Paypal)' );

                                $post_data = array();
                                $post_data['miglad_firstname'] = $post['first_name'];
                                $post_data['miglad_lastname']  = $post['last_name'];
                                $post_data['miglad_email']     = $post['payer_email'];
                                $post_data['miglad_amount']    = $amountfrompaypal;
                                $post_data['miglad_address']   = $post['address_street'];
                                $post_data['miglad_country']   = $post['address_country'];
                                $post_data['miglad_city']      = $post['address_city'];
                                $post_data['miglad_transactionType']      = 'One time (Paypal)';
								$post_data['miglad_date']  = $d;
								$post_data['miglad_time']  = $t;

                                 /*** SEND EMAIL ****/
                                 $e = get_option('migla_replyTo');
                                 $en = get_option('migla_replyToName');
                                 $ne = get_option('migla_notif_emails');

                                 mg_send_thank_you_email( $post_data , $e, $en );
                                 mg_send_notification_emails( $post_data , $e, $en, $ne);

                                 $tdata   =  $transientKey . "hletter";
                                 $content =  get_option( $tdata );
                                 mg_send_hletter( $post_data , $e, $en, $content, $d );
										
				 }else{
				    //it has its transient data

                    if( $isIDExist == -1 ){ //Check if this already saved
					
	
					$this->migla_create_post( $post['custom'] );	
					$post_id = $this->migla_get_id( $post['custom'] ); // $post_id =  migla_create_post_2(); //Make A POST

                         $i = 0; 
                         $keys = array_keys( $postData );
                         foreach( (array)$postData as $value)
                         {
                              add_post_meta( $post_id, $keys[$i], $value );
                             $i++;
                          }
						  
						  $amountfrompaypal = $post['payment_gross'] ;
                          if( $amountfrompaypal == '' ){ 
                                 $amountfrompaypal = $post['mc_gross']; 
                          }
                          update_post_meta( $post_id, 'miglad_amount', $amountfrompaypal );	//saved from paypal
							   
                           //Save data from paypal
                           add_post_meta( $post_id, 'miglad_paymentmethod', 'Paypal Front-End' );
                           add_post_meta( $post_id, 'miglad_paymentdata', $post );
                           add_post_meta( $post_id, 'miglad_transactionId', $post['txn_id'] );
                           add_post_meta( $post_id, 'miglad_timezone', $default );

                          //Check what type is it
                           add_post_meta( $post_id, 'miglad_transactionType', 'One time (Paypal)' );                        	

                           $postData['miglad_transactionType']      = 'One time (Paypal)';

                          /*** SEND EMAIL ****/
                          $e = get_option('migla_replyTo');
                          $en = get_option('migla_replyToName');
                          $ne = get_option('migla_notif_emails');

                          mg_send_thank_you_email( $postData, $e, $en );
                          mg_send_notification_emails( $postData, $e, $en, $ne);

                         $tdata   =  $transientKey . "hletter";
                         $content =  get_option( $tdata );
                         mg_send_hletter( $postData, $e, $en, $content, $d );

						 /** Repacking Mailist Data **/
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
						 					 
                    }else{
                         //Do Nothing, it is already saved on database        
                    }			
                    		
		     } //HAS TRANSIENT
		    
                     /*** MAIL LIST ****/
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
}

 public function handle_verified_ipn_pro( $post )
 {
    include_once 'migla-functions.php';
    $payment_status = $post['payment_status'];

   add_option('migla_paypal_ipn'.time(),  $post );
       
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
             
               $ref_id       = $_POST[ 'rp_invoice_id' ];
               $session_id   = "migla". $ref_id;
               $transientKey = "t_migla" . $ref_id ;
               $txn_new      = (string)$post['txn_id'] ;

	           $this->migla_create_post( $txn_new );	
	           $new_id = $this->migla_get_id( $txn_new );

                /** Cek if this donation session id exist **/
                $isIDExist =  $this->migla_cek_repeating_id( $session_id );	
                $this->migla_create_from_old_donation( $isIDExist, $new_id );

                /** Additional data **/
                add_post_meta( $new_id, "miglad_time" , $t );
                add_post_meta( $new_id, "miglad_date" , $d );

                   /** Save data from paypal **/
                   add_post_meta( $new_id, 'miglad_paymentmethod', 'Credit Card' );
                   add_post_meta( $new_id, 'miglad_paymentdata', $post );
                   add_post_meta( $new_id, 'miglad_transactionId', $post['TRANSACTIONID'] );
                   add_post_meta( $new_id, 'miglad_transactionType', 'Recurring (Paypal Pro)' );
                   add_post_meta( $new_id, 'miglad_timezone', $default );
                   add_post_meta( $new_id, 'miglad_desc', $post['desc'] );
                   add_post_meta( $new_id, 'miglad_preference', $post['rp_invoice_id'] );
                   add_post_meta( $new_id, 'miglad_subscription_id', $post['recurring_payment_id'] ); 

                          /*** SEND EMAIL ****/
                          $e = get_option('migla_replyTo');
                          $en = get_option('migla_replyToName');
                          $ne = get_option('migla_notif_emails');

                          $post_data = array();
                          $post_data['miglad_firstname'] = get_post_meta( $isIDExist , 'miglad_firstname', true);
                          $post_data['miglad_lastname']  = get_post_meta( $isIDExist , 'miglad_lastname', true);
                          $post_data['miglad_email']     = get_post_meta( $isIDExist , 'miglad_email', true);
                          $post_data['miglad_amount']    = get_post_meta( $isIDExist , 'miglad_amount', true);
                          $post_data['miglad_address']   = get_post_meta( $isIDExist , 'miglad_address', true);
                          $post_data['miglad_country']   = get_post_meta( $isIDExist , 'miglad_country', true);
                          $post_data['miglad_city']      = get_post_meta( $isIDExist , 'miglad_city', true);
						  $post_data['miglad_transactionType']      = 'Recurring (Paypal Pro)';
						  $post_data['miglad_date']      = $d;
						  $post_data['miglad_time']      = $t;

                          mg_send_thank_you_email( $post_data , $e, $en );
                          mg_send_notification_emails( $post_data , $e, $en, $ne);
	   
	   } // If $payment_status
 }

}//END OF CLASS

?>