<?php

class migla_authorize_handler_{

  public function __construct(){
     add_action( 'migla_hook_silent_post', array( $this , 'migla_silent_post_frontend') , 10, 1 );
  }
  
	public function migla_get_id( $value ) {
		global $wpdb;
		$pid = $wpdb->get_var( $wpdb->prepare(
			   "SELECT ID FROM $wpdb->posts WHERE post_title = %s ORDER BY ID ASC"
				, $value  ));
		if( $pid != '' )
			return $pid;
		else 
			return -1;
	}

	public function migla_create_post( $sessionid ) 
	{
	  global $wpdb;
	  $wpdb->insert("$wpdb->posts", array(
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
   
   public function migla_cek_repeating_id_by_field( $meta_value, $meta_key ) 
	{
		global $wpdb;
		$pid = $wpdb->get_var( $wpdb->prepare(
			   "SELECT post_id FROM $wpdb->postmeta WHERE meta_value = %s AND meta_key = %s ORDER BY post_id ASC"
				, $meta_value , $meta_key ));
		if( $pid != '' )
			return $pid;
		else 
			return -1;
		
		/*
		$pid = -1;
		
		$args = array(
			'post_type'		=>	'migla_donation',
			'meta_query'	=>	array(
				array(
					'value'	=>	$meta_value
				)
			)
		);
		$my_query = new WP_Query( $args );

		global $post;

		if( $my_query->have_posts() ) {

		  while( $my_query->have_posts() ) {
			$pid	= $post->ID;	
		  } // end while
		} // end if

		wp_reset_postdata();
		
		return $pid ;
		*/
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

    public function migla_silent_post_frontend()
    {
	    include_once 'migla-functions.php';

 	    //add_option( ('migla_auth_POST_any_'.date('Y-m-d-h-i:s')), $_POST );
        
		//Check if status is completed
		 if( isset($_POST['x_response_code']) && ( $_POST['x_response_code'] == 1  || $_POST['x_response_code'] == '1' ) )
		 {
			if( isset($_POST['x_subscription_id']) ) //check if it is a sub
			{
			    $old_id = $this->migla_cek_repeating_id_by_field( $_POST['x_subscription_id'] , 'miglad_subscription_id' );
			   			   	
				if( $_POST['x_subscription_paynum'] == '1' )
				{
					//Initial Recurring
					//add_option( ('migla_auth_POST_sub_'.date('Y-m-d-h-i:s')), $_POST );
					update_post_meta( $old_id, 'miglad_payment_status', 'settled' );
					update_post_meta( $old_id, 'miglad_transactionId', $_POST['x_trans_id'] );
					add_post_meta( $old_id, 'miglad_paymentdata', $_POST );
					
				}else{
					
					if( $old_id != -1 )
					{	
						$unique_id = $_POST['x_subscription_id'] . "_" . $_POST['x_subscription_paynum'];
						$this->migla_create_post( $unique_id );	
						$post_id = $this->migla_get_id( $unique_id );					  
						$this->migla_create_from_old_donation( $old_id , $post_id );

					  $php_time_zone = date_default_timezone_get();
					  $t = ""; 
					  $d = ""; 
					  $default = "";
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
					  

					   add_post_meta( $post_id, 'miglad_time', $t ); 
					   add_post_meta( $post_id, 'miglad_date', $d ); 

								 /*****  Add transaction data *******/
								 add_post_meta( $post_id, 'miglad_paymentmethod', 'credit card' );
								 add_post_meta( $post_id, 'miglad_paymentdata', $_POST );
								 add_post_meta( $post_id, 'miglad_transactionId', (string)$_POST['x_trans_id'] );
								 add_post_meta( $post_id, 'miglad_transactionType', 'Recurring (Authorize.NET)' );
								 add_post_meta( $post_id, 'miglad_timezone', $default );
								 
								 add_post_meta( $post_id, 'miglad_subscription_id', $_POST['x_subscription_id'] );
								 ///add_post_meta( $post_id, 'miglad_customer_profile_id', $customerProfileId );
								 add_post_meta( $post_id, 'miglad_customer_id', $_POST['x_cust_id'] );
								 add_post_meta( $post_id, 'miglad_payment_status', 'settled' );
								 
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
							$post_data['miglad_transactionType']      = 'Recurring (Authorize.NET)';

                          mg_send_thank_you_email( $post_data , $e, $en );
                          mg_send_notification_emails( $post_data , $e, $en, $ne);	
				
					}//If this record initial by TD
									
				} //Ongoing recurring
								
			}else{
				
                $post_id = $this->migla_cek_repeating_id_by_field( $_POST['x_trans_id'] , 'miglad_transactionId' );  
				//add_option( ('migla_auth_POST_any_'.$post_id.'_'.$_POST['x_trans_id'].'_'.date('Y-m-d-h-i:s')), $_POST );				
				update_post_meta( $post_id , 'miglad_payment_status', 'settled' );
				
			} //Recurring | One time
		   
		 } //IF COMPLETE
		 
	} //END OF Function Front End
  
 } //end of class

$listener_url = add_query_arg( 'migla_authorize_listener', 'silent_post', home_url( 'index.php' ) );
$obj = new migla_authorize_handler_();
if( isset($_GET['migla_authorize_listener']) || isset($_POST['migla_authorize_listener']) )
{ 
   do_action( 'migla_hook_silent_post' );
}

?>