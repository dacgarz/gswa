<?php
session_start();

include_once '../../../wp-blog-header.php';

include_once 'migla-functions.php';
include_once 'migla-call-stripe.php';

  Migla_Stripe::setApiKey( migla_getSK() );

  // retrieve the request's body and parse it as JSON
  $body = @file_get_contents('php://input');
  $event_json = json_decode($body);

  $customer_id = "";
  $customer_id = $event_json->data->object->customer;
  $charge_id = $event_json->data->object->id;  

  //Testing data to analyze
  //add_option( ('_miglaCharge'.time()), $event_json );
  //add_option( ('_miglaCustID'.time()), $customer_id );

 // This will send receipts on succesful invoices for subscription only
 if ( $event_json->type == 'charge.succeeded' )
 {
    $created = (string)$event_json->data->object->created;  //When this charge created
	$invoice = $event_json->data->object->invoice ;
    
    //If this charge has a customer a.k.a Recurring Payment
    if( $customer_id != '' || $customer_id != null) 
    {
       //Get Customer ID
       $customer = MStripe_Customer::retrieve($customer_id);
       $description = $customer->description;

       //Testing data to analyze
       //add_option( ('_miglaCust'.time()), $customer );


      //Get it done
      $desc = explode(";", $description ) ;
      $session = $desc[1];
      $transient_key = "t_migla". $session;
      $session_id = 'migla'. $session;
    
      $old_id      = migla_cek_repeating_id( $session_id );
      $old_created = $customer->created ; //(string)get_post_meta( $old_id, 'miglad_customer_created');

      if( $old_id == -1 ) //This is not repeating but initial subscriber
      { 
		  add_post_meta(  $old_id, 'miglad_invoice' , $invoice );
		  
      }else{ //There is an old repeating id, recurring payment

          $isExist = migla_check_if_exist( 'miglad_invoice', $invoice );
          add_post_meta(  $old_id, 'miglad_invoice' , $invoice );	  
	  
          if( $old_created != $created ){

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

			  //post for donation
			  $new_donation = array(
			  'post_title' => 'migla_donation',
			  'post_content' => '',
			  'post_status' => 'publish',
			  'post_author' => 1,
			  'post_type' => 'migla_donation'
			  );

			   $new_id = wp_insert_post( $new_donation );

			    //This a repeating, old subscriber
			   migla_create_from_old_donation( $old_id, $new_id);

			   update_post_meta( $new_id, 'miglad_time', $t ); 
			   update_post_meta( $new_id, 'miglad_date', $d ); 

			   add_post_meta( $new_id, 'miglad_paymentmethod', 'credit card' );
			   add_post_meta( $new_id, 'miglad_timezone', $default );
			   add_post_meta( $new_id, 'miglad_transactionType', 'Recurring (Stripe)' );
			   add_post_meta( $new_id, 'miglad_subscription_type', 'current' ); 
			   add_post_meta( $new_id,  'miglad_subscription_id' , $customer_id ); 

			   //Get Charge ID
			   $charge = MStripe_Charge::retrieve($charge_id);
			   add_post_meta( $new_id, 'miglad_paymentdata', $charge );
			   add_post_meta( $new_id, 'miglad_transactionId', $charge_id  );

                          /*** SEND EMAIL ****/
                          $e = get_option('migla_replyTo');
                          $en = get_option('migla_replyToName');
                          $ne = get_option('migla_notif_emails');

                          $post_data = array();
                          $post_data['miglad_firstname'] = get_post_meta( $new_id , 'miglad_firstname', true);
                          $post_data['miglad_lastname']  = get_post_meta( $new_id , 'miglad_lastname', true);
                          $post_data['miglad_email']     = get_post_meta( $new_id , 'miglad_email', true);
                          $post_data['miglad_amount']    = get_post_meta( $new_id , 'miglad_amount', true);
                          $post_data['miglad_address']   = get_post_meta( $new_id , 'miglad_address', true);
                          $post_data['miglad_country']   = get_post_meta( $new_id , 'miglad_country', true);
                          $post_data['miglad_city']      = get_post_meta( $new_id , 'miglad_city', true);
                          $post_data['miglad_date']      = $d;
                          $post_data['miglad_time']      = $t;
			  $post_data['miglad_transactionType']      = 'Recurring (Stripe)';

                          mg_send_thank_you_email( $post_data , $e, $en );
                          mg_send_notification_emails( $post_data , $e, $en, $ne);	

          }//Check created time

      }//End If Repeating


   }//endif has customer

 }else if ( $event_json->type == 'charge.dispute.created' )
 {
    //Ok get this charge id
    $charge_id = $event_json->data->object->charge;
    $charge = MStripe_Charge::retrieve($charge_id);   
    $description = $charge->description;
    $desc = explode(";", $description ) ;

    $session = $desc[2];
    $session_id = 'migla'. $session;

    $who_is = migla_cek_repeating_id( $session_id );
    add_post_meta( $who_is , 'miglad_charge_dispute', 'dispute' ); 
 
 }else if ( $event_json->type == 'charge.dispute.closed' )
 {
    //Ok get this charge id
    $charge_id = $event_json->data->object->charge;
    $charge = MStripe_Charge::retrieve($charge_id);   
    $description = $charge->description;
    $desc = explode(";", $description ) ;

    $session = $desc[2];
    $session_id = 'migla'. $session;

    $who_is = migla_cek_repeating_id( $session_id );
    update_post_meta( $who_is , 'miglad_charge_dispute', '' ); 
 
 }else{ // ELSE This will send receipts on succesful invoices

 }


?>