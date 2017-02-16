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


/************ Gateways *********************************/
include_once 'migla_ajax_gateways.php';


  add_action("wp_ajax_miglaA_get_date", "miglaA_get_date");
  add_action("wp_ajax_nopriv_miglaA_get_date", "miglaA_get_date");

function miglaA_get_date() 
{
    $php_time_zone 	= date_default_timezone_get();
    $default 		= $_POST['timezone'];
	
    $language 		= $_POST['language'];
    $date_format 	= $_POST['dateformat'];
	
	if( $language == false || $language == '' )
		$language = 'en.UTF-8';
	
	if( $date_format == false || $date_format == '' )
		$date_format = '%B %d %Y' ;
	 
    setlocale(LC_TIME, $language );
    $my_locale = get_locale();
	
    if( $default == 'Server Time' )
    {
        $gmt_offset = -get_option( 'gmt_offset' );
	if ($gmt_offset > 0)
	{ 
		$time_zone = 'Etc/GMT+' . $gmt_offset; 
        }else{		
		$time_zone = 'Etc/GMT' . $gmt_offset;    
        }
	date_default_timezone_set( $time_zone );
    }else{
	date_default_timezone_set( $default );
    }

	$t = date('H:i:s');
	$d = date('m')."/".date('d')."/".date('Y');
	
    //$now = date("F jS, Y", strtotime($d))." ".$t;
    $now =  strftime( $date_format , date(strtotime($d)) ) . " " . $t ;
 	
	date_default_timezone_set( $php_time_zone );

  echo $now;

  die();
}

  add_action("wp_ajax_miglaA_retrieve_cc_lists", "miglaA_retrieve_cc_lists");
  add_action("wp_ajax_nopriv_miglaA_retrieve_cc_lists", "miglaA_retrieve_cc_lists");

function miglaA_retrieve_cc_lists() 
{
  $cc = new migla_constant_contact_class();
  $theList = $cc->get_milist();

  echo $theList;
  die();
}

  add_action("wp_ajax_miglaA_mailchimp_getlists", "miglaA_mailchimp_getlists");
  add_action("wp_ajax_nopriv_miglaA_mailchimp_getlists", "miglaA_mailchimp_getlists");

include_once 'migla_class_mailchimp.php';

function miglaA_mailchimp_getlists() 
{
   $cc    = new migla_mailchimp_class();
   $lists = $cc->get_contact_list();
  
   echo $lists;
   die();
}


/*********************************************************************************/

  add_action("wp_ajax_miglaA_update_postmeta", "miglaA_update_postmeta");
  add_action("wp_ajax_nopriv_miglaA_update_postmeta", "miglaA_update_postmeta");

function miglaA_update_postmeta() 
{
   $id = $_POST['id'];
   $key = $_POST['key'];
   $value = $_POST['value'];

 if( empty($value) || $value == '' ){
     global $wpdb;
     $wpdb->query( 
	$wpdb->prepare( "DELETE FROM {$wpdb->prefix}postmeta WHERE meta_key = %s and post_id = %d" , $key, $id )
     );
 }else{
   update_post_meta( $id, $key , '');
   update_post_meta( $id, $key , $value);
 }  
   die();
}


  add_action("wp_ajax_miglaA_delete_postmeta", "miglaA_delete_postmeta");
  add_action("wp_ajax_nopriv_miglaA_delete_postmeta", "miglaA_delete_postmeta");

function miglaA_delete_postmeta() 
{
   $id = $_POST['id'];
   $key = $_POST['key'];

   global $wpdb;
   $wpdb->query( 
	$wpdb->prepare( "DELETE FROM {$wpdb->prefix}postmeta WHERE meta_key = %s and post_id = %d" , $key, $id )
   );

   die();
}

  add_action("wp_ajax_miglaA_get_postmeta", "miglaA_get_postmeta");
  add_action("wp_ajax_nopriv_miglaA_get_postmeta", "miglaA_get_postmeta");

function miglaA_get_postmeta() {

   $id = $_POST['id'];
   $key = $_POST['key'];

   $out = '-1'; 

   $data = get_post_meta( $id, $key );
   if( !empty($data) ){
      $out = $data[0];
   }
   echo $out ;
   die();
}


/**** STRIPE's PLAN ********/

  add_action("wp_ajax_miglaA_update_recurring_plans", "miglaA_update_recurring_plans");
  add_action("wp_ajax_nopriv_miglaA_update_recurring_plans", "miglaA_update_recurring_plans");

function miglaA_update_recurring_plans(){
 
        $isUpgrade = false;
        if( $_POST['old_interval_count'] != $_POST['new_interval_count'] ) {
            $isUpgrade = true;
        } 
        if(  $_POST['old_interval'] != $_POST['new_interval']  ){
            $isUpgrade = true;
        } 
 
   $success = "1"; $error1 = ""; $error2 = ""; $error3 = ""; $error4 = ""; $error5 = ""; $error6 = "";

   try{

        require_once 'migla-call-stripe.php';

        if( ( strpos( $_POST['old_payment_method'], 'stripe') === false ) 
		       && ( strpos( $_POST['new_payment_method'] , 'stripe') !== false ) )
        {
		   //add the new plan to stripe and database
			   if( !isset($_POST['new_interval_count']) || empty($_POST['new_interval_count']) ){ 
					 $_count = 1; 
			   }else{ 
					 $_count = $_POST['new_interval_count'] ;
			   }

				 //Retrieve
				  Migla_Stripe::setApiKey( migla_getSK() );

			   $plan = MStripe_Plan::create(
				  array(
				   "amount"         => 1,
				   "interval"       => $_POST['new_interval'],
				   "interval_count" => $_count,
				   "name"           => $_POST['new_name'],
				   "currency"       => get_option('migla_default_currency'),
				   "id"             => $_POST['new_id']
				  )
			   );

			   $plan_array = $plan->__toArray(true); 
			   $post_id = migla_get_stripeplan_id();
			   add_post_meta( $post_id, 'stripeplan_'.$_POST['new_id'], $plan_array );

            $success = "1";

       }else if( ( strpos( $_POST['old_payment_method'], 'stripe') !== false) 
	        && ( strpos( $_POST['new_payment_method'] , 'stripe') !== false ) ){
           
           if( $isUpgrade ){

					//Oh this is a change on Interval values in Stripe Plan, well we need to delete and create new
					$post_id = migla_get_stripeplan_id();

				   Migla_Stripe::setApiKey( migla_getSK() );

				   //delete Plan in Stripe and Database
				   $plan = MStripe_Plan::retrieve( $_POST['old_id'] );
				   $plan->delete();
	 
				   global $wpdb; 
				   $delete_key = 'stripeplan_'.$_POST['old_id'];
				   $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}postmeta WHERE post_id = %d AND META_KEY = %s" , $post_id, $delete_key ) );

				   //Recreate a new Plan              
				   //Ok new beginning, This is very hard and all you do just click a button
				   Migla_Stripe::setApiKey( migla_getSK() );

				   if( !isset($_POST['new_interval_count']) || empty($_POST['new_interval_count']) ){ 
					   $_count = 1; 
				   }else{ 
					   $_count = $_POST['new_interval_count'] ;
				   }

				   $plan = MStripe_Plan::create(
					  array(
						"amount" => 1,
						"interval" => $_POST['new_interval'],
						"interval_count" => $_count,
						"name" => $_POST['new_name'],
						"currency" => get_option('migla_default_currency'),
						"id" => $_POST['old_id']
					  )
					);

				   $plan_array = $plan->__toArray(true); 
				   add_post_meta( $post_id, 'stripeplan_'.$_POST['new_id'], $plan_array );
                   $success = "1";

           }else{ //Ok thanks, This is only change the name

			//update Plan in Stripe and Database
			//Well if the intervals doesn't change then
			Migla_Stripe::setApiKey( migla_getSK() );
			$plan = MStripe_Plan::retrieve( $_POST['old_id'] );
			$plan->name = $_POST['new_name'];
			$plan->save();

                        //Renew postmeta
                        $post_id = migla_get_stripeplan_id();
                        $new_plan_array = $plan->__toArray(true) ;
                        update_post_meta( $post_id, ("stripeplan_".$_POST['old_id']), $new_plan_array );

                    $success = "1";
           }

       }else if( ( strpos( $_POST['old_payment_method'], 'stripe') !== false ) 
	                 && ( strpos( $_POST['new_payment_method'], 'stripe') === false ) ){

                //Oh this is a downgrade
               Migla_Stripe::setApiKey( migla_getSK() );

               //delete Plan in Stripe and Database
               $plan = MStripe_Plan::retrieve( $_POST['old_id'] );
               $plan->delete();
  
               //Remove it from db
               $post_id = migla_get_stripeplan_id();
               $delete_key = 'stripeplan_'.$_POST['old_id'];
               delete_post_meta( $post_id, ("stripeplan_".$_POST['old_id']) );

              $success = "1";

       }else if( ($_POST['old_payment_method'] == 'paypal') && ($_POST['new_payment_method'] == 'paypal') ){  
              $success = "1";
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
       update_option( 'migla_recurring_plans' , $_POST['list'] ); $message = $success;
   }else{
        $message .= $error1. " " . $error2. " " . $error3. " " . $error4. " " .$error5. " " .$error6 ;
   }

   echo $message;
   die();
}


/***********************************************/
/*   STRIPE    APRIL 2th */
/***********************************************/
  add_action("wp_ajax_miglaA_syncPlan", "miglaA_syncPlan");
  add_action("wp_ajax_nopriv_miglaA_syncPlan", "miglaA_syncPlan");

function miglaA_syncPlan(){

  require_once 'migla-call-stripe.php'; 

  $post_id = migla_get_stripeplan_id();
   
  global $wpdb;
  $data = array(); 
  $data =  $wpdb->get_results( 
	$wpdb->prepare( 
  	   "SELECT * FROM {$wpdb->prefix}postmeta WHERE post_id = %d" , $post_id
        )
  );

  $row1 = 0; $row2 = 0; $metaid_on_server = array(); $list_on_server = array();
  foreach( $data as $d ){

     $val = unserialize( $d->meta_value );
     $keys = array_keys($val);

       $metaid_on_server[$row1]  = $d->meta_id;

     foreach($keys as $key){
        if( $key == 'id' ){
           if( in_array( $val[$key] , $list_on_server, true) ){
             migla_delete_post_meta2( $d->meta_id  );
           }else{
             $list_on_server[$row2] = $val[$key];
             $row2++;
           }
        }
     }

    $row1++;
  }


 //Retrieve
 Migla_Stripe::setApiKey( migla_getSK() );

 $plans = MStripe_Plan::all(); 

 $plans_arr = $plans->__toArray(true);
 $plan_data = (array)$plans_arr['data'];


  //Let's make comparison and add plan that doesn't exist on server
   $list_on_stripe = array(); $row = 0;
   $keys = array_keys($plan_data);
   foreach( $keys as $key ){
     $id_from_stripe = $plan_data[$key]['id']; 
     $list_on_stripe[$row] = $id_from_stripe; $row++;
     if( in_array( $id_from_stripe , $list_on_server, TRUE) ){
     }else{
        add_post_meta( $post_id, 'stripeplan_'.$id_from_stripe, $plan_data[$key] ); //if is not add to database
     }
   }

  //Reverse . Let's make comparison and delete plan that doesn't exist on stripe
  $keys = array_keys( $list_on_server );
   foreach( $keys as $key ){
     if( in_array( $list_on_server[$key] ,  $list_on_stripe , TRUE) ){
     }else{
        $metakey = "stripeplan_" . $list_on_server[$key] . "%";
        migla_delete_post_meta1( $metakey );
     }
   }

  echo miglaA_stripe_getPlan();
  die();

}

  add_action("wp_ajax_miglaA_stripe_addPlan", "miglaA_stripe_addPlan");
  add_action("wp_ajax_nopriv_miglaA_stripe_addPlan", "miglaA_stripe_addPlan");

function miglaA_stripe_addPlan(){

   require_once 'migla-call-stripe.php'; 
   Migla_Stripe::setApiKey( migla_getSK() );

   if( !isset($_POST['interval_count']) || empty($_POST['interval_count']) ){ 
      $_count = 1; 
   }else{ 
      $_count = $_POST['interval_count'] ;
   }

  $plan = MStripe_Plan::create(
    array(
      "amount" => $_POST['amount'],
      "interval" => $_POST['interval'],
      "interval_count" => $_count,
      "name" => $_POST['name'],
      "currency" => get_option('migla_default_currency'),
      "id" => $_POST['id']
    )
 );

  $plan_array = $plan->__toArray(true);
   
   $post_id = migla_get_stripeplan_id();

   add_post_meta( $post_id, 'stripeplan_'.$_POST['id'], $plan_array );

   echo $arr;
   die();
}

  add_action("wp_ajax_miglaA_stripe_addBasicPlan", "miglaA_stripe_addBasicPlan");
  add_action("wp_ajax_nopriv_miglaA_stripe_addBasicPlan", "miglaA_stripe_addBasicPlan");

function miglaA_stripe_addBasicPlan(){

   $success = "-1"; $error1 = ""; $error2 = ""; $error3 = ""; $error4 = ""; $error5 = ""; $error6 = "";

   try{

      require_once 'migla-call-stripe.php'; 
      Migla_Stripe::setApiKey( migla_getSK() );
 
      if( !isset($_POST['interval_count']) || empty($_POST['interval_count']) ){ 
          $_count = 1; 
      }else{ 
          $_count = (int)$_POST['interval_count'] ;
      }

      $plan = MStripe_Plan::create(
          array(
              "amount" => 1,
              "interval" => $_POST['interval'],
              "interval_count" => $_count,
              "name" => $_POST['name'],
              "currency" => get_option('migla_default_currency'),
              "id" => $_POST['id']
          ));

       $plan_array = $plan->__toArray(true);
       $success = "1";

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
        $post_id = migla_get_stripeplan_id();
        add_post_meta( $post_id, 'stripeplan_'.$_POST['id'], $plan_array ); $message = $success;
   }else{
        $message .= $error1. " " . $error2. " " . $error3. " " . $error4. " " .$error5. " " .$error6 ;
   }

   echo $message;
   die();
}

  add_action("wp_ajax_miglaA_stripe_deletePlan", "miglaA_stripe_deletePlan");
  add_action("wp_ajax_nopriv_miglaA_stripe_deletePlan", "miglaA_stripe_deletePlan");

function miglaA_stripe_deletePlan(){

   require_once 'migla-call-stripe.php'; 
   $success = "-1"; $error1 = ""; $error2 = ""; $error3 = ""; $error4 = ""; $error5 = ""; $error6 = "";

 try{
      Migla_Stripe::setApiKey( migla_getSK() );
 
     $plan = MStripe_Plan::retrieve( $_POST['id'] );
     $plan->delete();
   
     $post_id = migla_get_stripeplan_id();
     $meta_key = 'stripeplan_'.$_POST['id'];

     global $wpdb;
     $data =  $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}postmeta WHERE post_id = %d and meta_key = %s" ,
	       $post_id,  $meta_key 
            )
     );
     $success = "1";

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

   echo $message;
   die();
}

  add_action("wp_ajax_miglaA_stripe_getPlan", "miglaA_stripe_getPlan");
  add_action("wp_ajax_nopriv_miglaA_stripe_getPlan", "miglaA_stripe_getPlan");

function miglaA_stripe_getPlan(){

   $out = array(); $result = array(); $row = 0;

   $post_id = migla_get_stripeplan_id();
   
     global $wpdb;
     $data = array();
     $data =  $wpdb->get_results( 
	$wpdb->prepare( 
	"SELECT * FROM {$wpdb->prefix}postmeta WHERE post_id = %d" ,
	   $post_id
        )
     );

     $result = array(); $row = 0;  $out = array();

   if( count($data) > 0 ){
     
      foreach( $data as $d )
      {
         $out[$row]['id'] = $d->meta_id;
         $out[$row]['detail'] = "<input class='mglrec' type=hidden name='".$row."' >"; 
 
         $x = unserialize( $d->meta_value );
         $keys = array_keys($x);

         foreach($keys as $key){
           if( $key == 'created' ){  
              $out[$row][$key] = date( "Y-m-d" , $x[$key] );
           }else if( $key == 'id' ){  
              $out[$row]['planid'] =  $x[$key];
           }else{
              $out[$row][$key] = $x[$key];
           }
         }

         $row++;
      }//foreach
  }

  $result[0] = $out;
 
  echo json_encode( $result );
  die();

}


add_action("wp_ajax_miglaA_purgeCache", "miglaA_purgeCache");
add_action("wp_ajax_nopriv_miglaA_purgeCache", "miglaA_purgeCache");

function miglaA_purgeCache(){
 global $wpdb; $msg = ""; $count = 0;
 
 $option_id = array(); 
 $option_id = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}options WHERE option_name LIKE 't_migla%'" );
 
 foreach( $option_id as $id )
 {
        $now = time();
	$option_name = $id->option_name;
        $date = substr(  $option_name , 7, 8);      
	if( ( $now - time($date) ) > 0  )
	{
	   delete_option( $option_name );
           //$msg .= $now ." ". $date. " ".($now - $date). " " . $option_name. "<br>";
           $count++;
	}

 }
 $msg .= $count . " cache(s) erased";
 echo $msg;
 die();
}

add_action("wp_ajax_get_option_id", "get_option_id");
add_action("wp_ajax_nopriv_get_option_id", "get_option_id");

function get_option_id( $op ){
  global $wpdb; $res =array();
  $sql = "SELECT option_id from {$wpdb->prefix}options WHERE option_name='".$op."'";
  $res = $wpdb->get_row($sql);
  return $res->option_id;
}

/**************         DASHBOARD Page            ***********************/
/*******************************************************************/
  add_action("wp_ajax_miglaA_total_online", "miglaA_total_online");
  add_action("wp_ajax_nopriv_miglaA_total_online", "miglaA_total_online");

function miglaA_total_online()
{
	global $wpdb; 
	$data		= array();
	$totals		= array();
	
	$data = $wpdb->get_results( $wpdb->prepare( 	
			"SELECT DISTINCT ID FROM {$wpdb->prefix}posts WHERE post_type = %s" ,
			'migla_donation'
			)
	);
	
	$total = 0; $count = 0;
	foreach ( $data as $id )
	{
		$total  = $total + (float)get_post_meta( intval( $id->ID ) , 'miglad_amount', true);
		$count++;
	} 
	
	$totals['amount'] = $total;
	$totals['donors'] = $count;
	
	echo json_encode($totals);
	die();
}

  add_action("wp_ajax_miglaA_total_offline", "miglaA_total_offline");
  add_action("wp_ajax_nopriv_miglaA_total_offline", "miglaA_total_offline");

function miglaA_total_offline()
{
	global $wpdb; 
	$data		= array();
	$totals		= array();
	
	$data = $wpdb->get_results( $wpdb->prepare( 	
			"SELECT DISTINCT ID FROM {$wpdb->prefix}posts WHERE post_type like %s" ,
			'migla_odonation%'
			)
	);
	
	$total = 0; $count = 0;
	foreach ( $data as $id )
	{
		$total  = $total + (float)get_post_meta( intval( $id->ID ) , 'miglad_amount', true);
		$count++;
	} 
	
	$totals['amount'] = $total;
	$totals['donors'] = $count;
	
	echo json_encode($totals);
	die();
}

  add_action("wp_ajax_miglaA_total_pending_offline", "miglaA_total_pending_offline");
  add_action("wp_ajax_nopriv_miglaA_total_pending_offline", "miglaA_total_pending_offline");

function miglaA_total_pending_offline()
{
	global $wpdb; 
	$data		= array();
	$totals		= array();
	
	$data = $wpdb->get_results( $wpdb->prepare( 	
			"SELECT DISTINCT ID FROM {$wpdb->prefix}posts WHERE post_type = %s" ,
			'migla_odonation_p'
			)
	);
	
	$total = 0; $count = 0;
	foreach ( $data as $id )
	{
		$total  = $total + (float)get_post_meta( intval( $id->ID ) , 'miglad_amount', true);
		$count++;
	} 
	
	$totals['amount'] = $total;
	$totals['donors'] = $count;
	
	echo json_encode($totals);
	die();
}

  add_action("wp_ajax_miglaA_total_online_this_month", "miglaA_total_online_this_month");
  add_action("wp_ajax_nopriv_miglaA_total_online_this_month", "miglaA_total_online_this_month");

function miglaA_total_online_this_month()
{
	global $wpdb;
	$data = array();

	$data = $wpdb->get_results( $wpdb->prepare( 	
			"SELECT DISTINCT ID FROM wp_posts INNER JOIN wp_postmeta 
			ON wp_posts.ID = wp_postmeta.post_id
			WHERE post_type = %s AND meta_key = %s
			AND DATE_FORMAT( NOW(), %s ) = DATE_FORMAT(STR_TO_DATE(meta_value, %s ), %s )
			" ,
			'migla_donation', 'miglad_date', '%Y%m', '%m/%d/%Y', '%Y%m'
			)
	);
	
	$amount = 0;
	foreach( $data as $datum )
	{
		$amount = $amount + (float)get_post_meta( $datum->ID, 'miglad_amount', true );
	}
	
	echo $amount ;
	die();
}

  add_action("wp_ajax_miglaA_total_offline_this_month", "miglaA_total_offline_this_month");
  add_action("wp_ajax_nopriv_miglaA_total_this_month", "miglaA_total_offline_this_month");

function miglaA_total_offline_this_month()
{
	global $wpdb;
	$data = array();

	$data = $wpdb->get_results( $wpdb->prepare( 	
			"SELECT DISTINCT ID FROM wp_posts INNER JOIN wp_postmeta 
			ON wp_posts.ID = wp_postmeta.post_id
			WHERE post_type = %s AND meta_key = %s
			AND DATE_FORMAT( NOW(), %s ) = DATE_FORMAT(STR_TO_DATE(meta_value, %s ), %s )
			" ,
			'migla_odonation', 'miglad_date', '%Y%m', '%m/%d/%Y', '%Y%m'
			)
	);
	
	$amount = 0;
	foreach( $data as $datum )
	{
		$amount = $amount + (float)get_post_meta( $datum->ID, 'miglad_amount', true );
	}
	
	echo $amount ;
	die();
}

/*
  add_action("wp_ajax_miglaA_totalAll", "miglaA_totalAll");
  add_action("wp_ajax_nopriv_miglaA_totalAll", "miglaA_totalAll");

function miglaA_totalAll()
{
 global $wpdb;
 $data = array(); $ton = 0;

 $data = $wpdb->get_results( 
	 $wpdb->prepare( 	"SELECT ID FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta 
         ON {$wpdb->prefix}posts.ID =        {$wpdb->prefix}postmeta.post_id
	 WHERE post_type = %s and meta_key = %s
         ORDER BY STR_TO_DATE( meta_value, %s) DESC
        " ,
	   'migla_donation','miglad_date','%m/%d/%Y'
        )
 );

 foreach( $data as $id )
 {
    $ton = $ton + get_post_meta( intval( $id->ID ) , 'miglad_amount', true);
 }

 global $wpdb;
 $data = array();
 $data = $wpdb->get_results( 
	$wpdb->prepare( 
		"SELECT {$wpdb->prefix}posts.ID FROM {$wpdb->prefix}posts
	  WHERE post_type = %s",
	  'migla_odonation'
        )
 );
 $toff = 0;
 foreach( $data as $id )
 {
    $toff = $toff + get_post_meta( intval( $id->ID ) , 'miglad_amount', true);
 }

$out = array();
$out[0] = $ton; $out[1] = $toff; $out[2] = $ton + $toff;

  echo json_encode ( $out );
  die();
}

  add_action("wp_ajax_miglaA_totalOffAll", "miglaA_totalOffAll");
  add_action("wp_ajax_nopriv_miglaA_totalOffAll", "miglaA_totalOffAll");

function miglaA_totalOffAll()
{
  $toff = 0;

  global $wpdb;
  $data = array();
  $data = $wpdb->get_results( $wpdb->prepare( 
		"SELECT {$wpdb->prefix}posts.ID FROM {$wpdb->prefix}posts
	  WHERE post_type like %s",
	  'migla_odonation%'
        ));
  foreach( $data as $id )
  {
     $toff =  $toff + get_post_meta( intval( $id->ID ) , 'miglad_amount', true);
  }

  $out = array();
  $out[0] = $toff;
  echo json_encode ( $out );
  die();
}

  add_action("wp_ajax_miglaA_totalThisMonth", "miglaA_totalThisMonth");
  add_action("wp_ajax_nopriv_miglaA_totalThisMonth", "miglaA_totalThisMonth");

function miglaA_totalThisMonth()
{
  $year = date('Y');
  $month = date('m');
  if( strlen($month) < 2){
     $month = '0'.$month;
  }  
  global $wpdb;
  $data = array();
  $data = $wpdb->get_results( $wpdb->prepare( 
			    "SELECT ID 
			    FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta 
				ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
				WHERE post_type = %s AND meta_key = %s and meta_value like %s"  ,
				'migla_donation','miglad_date', $month.'/%/'.$year
			)
		); 
  $ton = 0;
  foreach( $data as $id )
  {
     $ton = $ton + get_post_meta( intval( $id->ID ) , 'miglad_amount', true);
  } 

  global $wpdb;
  $data = array();
  $data = $wpdb->get_results( "
        SELECT {$wpdb->prefix}posts.ID, post_date
        FROM {$wpdb->prefix}posts
        WHERE post_type = 'migla_odonation'
        AND year( post_date ) = year( current_date( ) )
         AND month( post_date ) = month( current_date( ) )
	 "
       );

  $toff = 0;
  foreach( $data as $id )
  {
     $toff =  $toff + get_post_meta( intval( $id->ID ) , 'miglad_amount', true);
  }
  $out = array();
  $out[0] = $ton; $out[1] = $toff; $out[2] = $ton + $toff;

  echo json_encode ( $out );
  die();
}
*/
  add_action("wp_ajax_miglaA_detail_6months", "miglaA_detail_6months");
  add_action("wp_ajax_nopriv_miglaA_detail_6months", "miglaA_detail_6months");

function miglaA_detail_6months() 
{
    global $wpdb;
    $data = array();
    $data = $wpdb->get_results( 
    $wpdb->prepare( 
	     "SELECT ID FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta 
		  ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
          WHERE post_type = %s and meta_key = %s 
          AND ( DATE_FORMAT(STR_TO_DATE(meta_value,%s), %s) - DATE_FORMAT(now(), %s) < 180) 
		 ORDER BY DATE_FORMAT(STR_TO_DATE(meta_value,%s), %s) DESC 
          "  ,
	      'migla_donation','miglad_date', '%m/%d/%Y', '%Y%m%d','%Y%m%d', '%m/%d/%Y', '%Y%m%d'
        )
    );
	
   $out 	= array(); 
   $output 	= array();
   $row = 0;
   
   foreach( $data as $id )
   {    
	 if( $row == 5 ) break;
	 
      $out[$row]['time'] = get_post_meta( intval( $id->ID ) , 'miglad_time', true);
      $out[$row]['date'] = get_post_meta( intval( $id->ID ) , 'miglad_date', true);
      $out[$row]['name'] = get_post_meta( intval( $id->ID ) , 'miglad_firstname', true)." ".get_post_meta( intval( $id->ID ) , 'miglad_lastname', true);
      $out[$row]['amount'] = get_post_meta( intval( $id->ID ) , 'miglad_amount', true);

      $out[$row]['address'] = get_post_meta( intval( $id->ID ) , 'miglad_address', true);
      $out[$row]['city'] = get_post_meta( intval( $id->ID ) , 'miglad_city', true);

      $out[$row]['state'] = get_post_meta( intval( $id->ID ) , 'miglad_state', true);
      $out[$row]['province'] = get_post_meta( intval( $id->ID ) , 'miglad_province', true);

      $out[$row]['country'] = get_post_meta( intval( $id->ID ) , 'miglad_country', true);
      $out[$row]['postalcode'] = get_post_meta( intval( $id->ID ) , 'miglad_postalcode', true);

      $out[$row]['repeating'] = get_post_meta( intval( $id->ID ) , 'miglad_repeating', true);   
      $out[$row]['anonymous'] = get_post_meta( intval( $id->ID ) , 'miglad_anonymous', true);  
	  
	  $row++;
  }	
	
	$output[0] = $out;
	$output[1] = $data;
	$output[2] = (array)get_option( 'migla_campaign' ); 
	
	echo json_encode( $output );
	die();
}

  add_action("wp_ajax_miglaA_campaign_progress", "miglaA_campaign_progress");
  add_action("wp_ajax_nopriv_miglaA_campaign_progress", "miglaA_campaign_progress");

function miglaA_campaign_progress()
{
	$campaigns	= get_option('migla_campaign');
    $cname 	= $_POST['campaign_name'];
    $ccname = str_replace( "[q]", "'", $cname );
	
    $target = migla_get_campaign_target( $cname );
    $amount = migla_get_total( $cname , "" );

    $dec 			= 2;
    $showDecimal 	= get_option('migla_showDecimalSep');
    if( $showDecimal == 'no' ){ 
		$dec = 0; 
	}
	
	$out 	= array(); 

	$out['index'] 		= $_POST['index'];
	$out['name'] 		= $_POST['campaign_name'];
    $out['type'] 		= 'designated';
    $out['target'] 		= number_format( $target , 2);
    $out['amount'] 		= number_format( $amount, $dec);
	$out['show']		= $campaigns[ $_POST['index'] ]['show'];

    if( $target != 0 ){
		$out['percent']  =  number_format( ($amount / $target) * 100, 2);
    }else{
		$out['percent'] = 0;
    }
     
	echo json_encode($out); 
	die();
}
	
	
  add_action("wp_ajax_miglaA_campaignprogress", "miglaA_campaignprogress");
  add_action("wp_ajax_nopriv_miglaA_campaignprogress", "miglaA_campaignprogress");

function miglaA_campaignprogress()
{
   $out = array(); //[index][campaign][percent]

   $campaignArray 	= (array)get_option( 'migla_campaign' );
   $row = 0;
   $dec = 2;
   
   $showDecimal 	= get_option('migla_showDecimalSep');
	if( $showDecimal == 'no' ){ 
		$dec = 0; 
	}

   if( $campaignArray[0] != '')
   {
    foreach( (Array) $campaignArray as $key => $value)
    { 
     $cname = $campaignArray[$key]['name'];
     $ccname = str_replace( "[q]", "'", $cname );

	 $out[$row]['index']	= $row+1;
     $out[$row]['type'] 	= 'designated';
     $out[$row]['campaign'] = $ccname; //remember ' is replaced by [q] 

     $target = migla_get_campaign_target( $cname );
     $amount = migla_get_total( $cname , "" );
     $out[$row]['target'] = number_format( $target , 2);
     $out[$row]['amount'] = number_format( $amount, $dec);

     if( $target != 0 ){
      $out[$row]['percent']  =  number_format( ($amount / $target) * 100, 2);
     }else{
      $out[$row]['percent'] = 0;
     }    
     $out[$row]['status'] = $campaignArray[$key]['show'];

     $row = $row + 1;
    }	
   }

   echo json_encode($out); 
   die(); 
}

//////// GRAPHIC //////////////////
  add_action("wp_ajax_migla_donations_6months", "migla_donations_6months");
  add_action("wp_ajax_nopriv_migla_donations_6months", "migla_donations_6months");

function migla_donations_6months() {
  $out = array();
 global $wpdb;
 $arr = array();
 $arr = $wpdb->get_results( 
	$wpdb->prepare( 
	"SELECT ID FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
         WHERE post_type = %s and meta_key = %s
         AND
         ( DATEDIFF( DATE_FORMAT(STR_TO_DATE(meta_value, %s), %s), Now() ) BETWEEN -180 AND 0)
         ORDER BY post_date ASC
        "  ,
	   'migla_donation','miglad_date', '%m/%d/%Y', '%Y-%m-%d', '%m/%d/%Y'
        )
 ); 

   $row = 0;
   if( empty($arr) ){
     $out[0]['amount'] = 0;
     $out[0]['date'] = date("m/d/Y");
     $out[0]['month'] = date("m");
     $out[0]['day'] = date("d");
     $out[0]['year'] = date("Y");   
   }else{
    foreach( $arr as $id )
    { 
     $out[$row]['amount'] = get_post_meta( intval( $id->ID ) , 'miglad_amount', true);
     $thedate = get_post_meta( intval( $id->ID ) , 'miglad_date', true);
     $out[$row]['date'] = $thedate;
     $dateField = explode( "/", $thedate);
     $out[$row]['month'] = $dateField[0]; //substr($thedate, 0,2);
     $out[$row]['day'] = $dateField[1];//substr($thedate, 6);
     $out[$row]['year'] = $dateField[2];//substr($thedate, 3,2);
     $row = $row + 1;
    }
   }  
  return $out;
}

  add_action("wp_ajax_migla_Ofdonations_6months", "migla_Ofdonations_6months");
  add_action("wp_ajax_nopriv_migla_Ofdonations_6months", "migla_Ofdonations_6months");

function migla_Ofdonations_6months() {
  $out = array();
 global $wpdb;
 $arr = array();
 $arr = $wpdb->get_results( 
	$wpdb->prepare( 
	"SELECT ID FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
         WHERE post_type = %s and meta_key = %s
         AND
         ( DATEDIFF( DATE_FORMAT(STR_TO_DATE(meta_value, %s), %s), Now() ) BETWEEN -180 AND 0)
         ORDER BY STR_TO_DATE( meta_value, %s) ASC
        " ,
	   'migla_odonation','miglad_date', '%m/%d/%Y', '%Y-%m-%d', '%m/%d/%Y'
        )
 ); 

   $row = 0;
   if( empty($arr) ){
     $out[0]['amount'] = 0;
     $out[0]['date'] = date("m/d/Y");
     $out[0]['month'] = date("m");
     $out[0]['day'] = date("d");
     $out[0]['year'] = date("Y");   
   }else{   
   foreach( $arr as $id )
   { 

    $out[$row]['amount'] = get_post_meta( intval( $id->ID ) , 'miglad_amount', true);;
    $thedate = get_post_meta( intval( $id->ID ) , 'miglad_date', true);
    $out[$row]['date'] = $thedate;
    $dateField = explode( "/", $thedate);
    $out[$row]['month'] = $dateField[0]; //substr($thedate, 0,2);
    $out[$row]['day'] = $dateField[1];//substr($thedate, 6);
    $out[$row]['year'] = $dateField[2];//substr($thedate, 3,2);
	
    $row = $row + 1;
   }
   }  
 return $out;
}

  add_action("wp_ajax_miglaA_getGraphData", "miglaA_getGraphData");
  add_action("wp_ajax_nopriv_miglaA_getGraphData", "miglaA_getGraphData");

function miglaA_getGraphData(){
  $data      = (array)migla_donations_last6months();
  
  echo json_encode( $data );
  die();
}

/**********************************************************************/
/********** THEME COLOR SETTINGS ***********************/
/**********************************************************************/
  add_action("wp_ajax_miglaA_reset_theme", "miglaA_reset_theme");
  add_action("wp_ajax_nopriv_miglaA_reset_theme", "miglaA_reset_theme");

function miglaA_reset_theme() {
   //THEME SETTINGS
   update_option( 'migla_tabcolor', '#eeeeee') ;
   update_option( 'migla_2ndbgcolor' , '#FAFAFA,1' ); 
   update_option( 'migla_2ndbgcolorb' , '#DDDDDD,1,1' ); 
   update_option( 'migla_borderRadius' , '8,8,8,8' );   
   
   update_option( 'migla_bglevelcolor', '#eeeeee' ); 
   update_option( 'migla_bglevelcoloractive', '#ba9cb5');  
   update_option( 'migla_borderlevelcolor', '#b0b0b0' ); 
   update_option( 'migla_borderlevel', '1' ); 

   $barinfo = "We have collected [total] of our [target] target. It is [percentage] of our goal for the [campaign] campaign";
   update_option('migla_progbar_info', $barinfo); 
   update_option( 'migla_bar_color' , '#428bca,1' );
   update_option( 'migla_progressbar_background', '#bec7d3,1');
   update_option( 'migla_wellboxshadow', '#969899,1, 1,1,1,1');	

   $arr = array( 'Stripes' => 'yes', 'Pulse' => 'yes', 'AnimatedStripes' => 'yes', 'Percentage' => 'yes' );
   update_option( 'migla_bar_style_effect' , $arr);
   
   $circles = array();
   $circles[0]['size'] 	= 100;
   $circles[0]['thickness'] 	= 10;
   $circles[0]['start_angle'] 	= 1;
   $circles[0]['reverse'] 	= 'no';
   $circles[0]['line_cap'] 	= 'round';
   $circles[0]['fill'] 	= '#428bca';
   $circles[0]['animation'] 	= 'none';
   $circles[0]['inside'] 	= 'none';
   $circles[0]['inner_font_size'] 	= 22;
   
   update_option( 'migla_circle_settings', $circles );
   update_option( 'migla_circle_textalign', 'mg_left-right');
   update_option( 'migla_circle_text1', 'Amount');
   update_option( 'migla_circle_text2', 'Target');
   update_option( 'migla_circle_text3', 'Backers');
   
}

  add_action("wp_ajax_miglaA_form_bgcolor", "miglaA_form_bgcolor");
  add_action("wp_ajax_nopriv_miglaA_form_bgcolor", "miglaA_form_bgcolor");

function miglaA_form_bgcolor() {
   $code = $_POST['color_code'];
   $op = get_option( 'migla_bgcolor' );
   if( get_option( 'migla_bgcolor' ) == ''){
      add_option( 'migla_bgcolor' , $code);
   }else{                     
      update_option( 'migla_bgcolor' , $code);   
   }
   die();
}

/**********************************************************************/
/********** GENERIC UPDATE OF OPTIONS ***********************/

  add_action("wp_ajax_migla_getme", "migla_getme");
  add_action("wp_ajax_nopriv_migla_getme", "migla_getme");

function migla_getme(){
  $r =  get_option($_POST['key']);
  echo $r;
  die();
}

  add_action("wp_ajax_migla_getme_array", "migla_getme_array");
  add_action("wp_ajax_nopriv_migla_getme_array", "migla_getme_array");

function migla_getme_array(){
  $r =  (array)get_option($_POST['key']);

  echo json_encode( $r );
  die();
}

  add_action("wp_ajax_nopriv_miglaA_update_me", "miglaA_update_me");
  add_action("wp_ajax_miglaA_update_me", "miglaA_update_me");

function miglaA_update_me() {
   $key 	= $_POST['key'];
   $value 	= $_POST['value'];

   update_option( $key , $value);
   
   die();
}

  add_action("wp_ajax_miglaA_update_barinfo", "miglaA_update_barinfo");
  add_action("wp_ajax_nopriv_miglaA_update_barinfo", "miglaA_update_barinfo");

function miglaA_update_barinfo() {
   $key = $_POST['key'];
   $value = $_POST['value'];

  update_option( $key , $value);
   
   die();
}

  add_action("wp_ajax_miglaA_update_arr", "miglaA_update_arr");
  add_action("wp_ajax_nopriv_miglaA_update_arr", "miglaA_update_arr");

function miglaA_update_arr() {
   $key = $_POST['key'];
   $value = serialize( $_POST['value'] );

   $op = get_option( $key );
   if( $op == false ){ add_option( $key , $value); }else{ update_option( $key , $value); }   
   
   die();
}

  add_action("wp_ajax_miglaA_update_us", "miglaA_update_us");
  add_action("wp_ajax_nopriv_miglaA_update_us", "miglaA_update_us");

function miglaA_update_us() {
  $arr = array();

  $arr = array(
    'Stripes' => $_POST['Stripes'],
    'Pulse' => $_POST['Pulse'],
    'AnimatedStripes' => $_POST['AnimatedStripes'],
    'Percentage' => $_POST['Percentage']    
  );

   update_option( 'migla_bar_style_effect' , $arr);
   echo( $_POST['Stripes'] );
   die();
}

/********** GIVING LEVELS ***********************/

  add_action("wp_ajax_miglaA_remove_options", "miglaA_remove_options");
  add_action("wp_ajax_nopriv_miglaA_remove_options", "miglaA_remove_options");

function miglaA_remove_options() {

   $key =  $_POST['key'];
   $option = $_POST['option_name'];
   $op = get_option( $option );

   unset( $op[$key] ); 
    
   update_option( $option ,  $op ); 

   $newData = get_option( $option );
   sort($newData); 
 
   echo json_encode($newData); 
   
   die();
}

  add_action("wp_ajax_miglaA_add_options", "miglaA_add_options");
  add_action("wp_ajax_nopriv_miglaA_add_options", "miglaA_add_options");

function miglaA_add_options() {  

   $key = $_POST['key'];
   $value = $_POST['value'];   
   $option = $_POST['option_name'];
   
   $op = get_option( $option );
                       
      $op[$key] = $value;
      update_option( $option , $op);   
   
      
   $newData = get_option( $option );
   sort($newData); 
   
   echo json_encode($newData);   
   
   die();
}

  add_action("wp_ajax_miglaA_add_amount", "miglaA_add_amount");
  add_action("wp_ajax_nopriv_miglaA_add_amount", "miglaA_add_amount");

function miglaA_add_amount() 
{  
   $post_id		 = $_POST['post_id'];
   $meta_key     = $_POST['meta_key'];  
   $amountValue	 = $_POST['amount_value']; 
   $perkValue 	 = $_POST['perk_value']; 
    
   $amounts = get_post_meta( $post_id, $meta_key );
   
   if( count($amounts[0]) <= 0 || $amounts == false )
   {
	   $newData = array( array( $amountValue , $perkValue) );
	   update_post_meta( $post_id, $meta_key, serialize($newData) );
   }else{
       $idx = count($amounts[0]);
	   $amounts[0][$idx]['amount'] = $amountValue	;
	   $amounts[0][$idx]['perk'] = $perkValue;
	   update_post_meta( $post_id, $meta_key, $amounts );
   }   
  
   $newData = get_post_meta( $post_id, $meta_key );
   
   echo json_encode($newData[0]);   
   
   die();
}

/***********************************************/
/*            FORM OPTIONS  FINISH Nov 21st */
/***********************************************/
  add_action("wp_ajax_miglaA_get_option", "miglaA_get_option");
  add_action("wp_ajax_nopriv_miglaA_get_option", "miglaA_get_option");

function miglaA_get_option() {
  echo json_encode( get_option( $_POST['option'] ) );
  die();
}

  add_action("wp_ajax_miglaA_get_currencies", "miglaA_get_currencies");
  add_action("wp_ajax_nopriv_miglaA_get_currencies", "miglaA_get_currencies");

function miglaA_get_currencies() {
  $op =  get_option( 'migla_currencies' );
  echo json_encode( $op );
  die();
}

  add_action("wp_ajax_miglaA_updateUndesignated", "miglaA_updateUndesignated");
  add_action("wp_ajax_nopriv_miglaA_updateUndesignated", "miglaA_updateUndesignated");

function miglaA_updateUndesignated(){
  update_option( 'migla_undesignLabel' , $_POST['new'] );
  updateACampaign($_POST['old'], $_POST['new']);
  die();
}

function mg_updateARecord($old, $new){
	 global $wpdb;
	 $sql = "UPDATE {$wpdb->prefix}postmeta SET meta_key = 'miglac_".$new."' WHERE meta_key ='miglac_".$old."'";
	 $wpdb->query($sql);
}

  add_action("wp_ajax_miglaA_update_form", "miglaA_update_form");
  add_action("wp_ajax_nopriv_miglaA_update_form", "miglaA_update_form");

function miglaA_update_form() 
{
		if( $_POST['values'] != '' )
		{
			$d = serialize($_POST['values']);
		}
		update_option('migla_form_fields', $_POST['values']);
		
		if( isset($_POST['changes']) )
		{
			$data = (array)$_POST['changes'];
			if( count($data) > 0 && $data[0] != '')
			{
				  foreach( (array)$data as $d )
				  {
					   mg_updateARecord($d[0], $d[1]);
				  }
			}
		}   
    die();
}

  add_action("wp_ajax_miglaA_update_cform", "miglaA_update_cform");
  add_action("wp_ajax_nopriv_miglaA_update_cform", "miglaA_update_cform");

function miglaA_update_cform() 
{
	update_post_meta( $_POST['formID'], 'migla_form_fields', $_POST['values'] );   
    die();
}

  add_action("wp_ajax_miglaA_reset_form", "miglaA_reset_form");
  add_action("wp_ajax_nopriv_miglaA_reset_form", "miglaA_reset_form");

function miglaA_reset_form() {
global $wpdb;

/////////FORM////////////////////////
$fields = array (
    '0' => array (
        'title' => 'Donation Information',
        'child' =>  array(
                   '0' => array( 'type'=>'radio','id'=>'amount', 'label'=>'How much would you like to donate?', 'status'=>'3', 'code' => 'miglad_', 
                       'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '1' => array( 'type'=>'select','id'=>'campaign', 'label'=>'Would you like to donate this to a specific campaign?', 'status'=>'3', 'code' => 'miglad_', 
                       'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '2' => array( 'type'=>'radio','id'=>'repeating', 'label'=>'Is this a recurring donation?', 'status'=>'1', 'code' => 'miglad_', 
                       'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '3' => array( 'type'=>'checkbox','id'=>'mg_add_to_milist', 'label'=>'Add to mailing list?', 'status'=>'1', 'code' => 'miglad_', 
                       'uid' => ("f".date("Ymdhis"). "_" . rand()) )
                 ),
        'parent_id' => 'NULL',
        'depth' => 2,
        'toggle' => '-1'
    ),
    '1' => array (
        'title' => 'Donor Information',
        'child' => array(
                   '0' => array( 'type'=>'text','id'=>'firstname', 'label'=>'First Name', 'status'=>'3', 'code' => 'miglad_', 'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '1' => array( 'type'=>'text','id'=>'lastname', 'label'=>'Last Name', 'status'=>'3', 'code' => 'miglad_', 'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '2' => array( 'type'=>'text','id'=>'address', 'label'=>'Address', 'status'=>'1' , 'code' => 'miglad_', 'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '3' => array( 'type'=>'select','id'=>'country', 'label'=>'Country', 'status'=>'1' , 'code' => 'miglad_', 'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '4' => array( 'type'=>'text','id'=>'city', 'label'=>'City', 'status'=>'1' , 'code' => 'miglad_', 'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '5' => array( 'type'=>'text','id'=>'postalcode', 'label'=>'Postal Code', 'status'=>'1' , 'code' => 'miglad_', 'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '6' => array( 'type'=>'checkbox','id'=>'anonymous', 'label'=>'Anonymous?', 'status'=>'1' , 'code' => 'miglad_', 'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '7' => array( 'type'=>'text','id'=>'email', 'label'=>'Email', 'status'=>'3' , 'code' => 'miglad_' , 'uid' => ("f".date("Ymdhis"). "_" . rand()) )
                 ),
        'parent_id' => 'NULL',
        'depth' => 8,
        'toggle' => '-1'
    ),
    '2' => array (
        'title' => 'Is this in honor of someone?',
        'child' => array(
                   '0' => array( 'type'=>'checkbox','id'=>'memorialgift', 'label'=>"Is this a Memorial Gift?", 'status'=>'1', 'code' => 'miglad_', 
                        'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '1' => array( 'type'=>'text','id'=>'honoreename', 'label'=>"Honoree[q]s Name", 'status'=>'1', 'code' => 'miglad_', 
                        'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '2' => array( 'type'=>'text','id'=>'honoreeemail', 'label'=>"Honoree[q]s Email", 'status'=>'1', 'code' => 'miglad_', 
                        'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '3' => array( 'type'=>'textarea','id'=>'honoreeletter', 'label'=>"Write a custom note to the Honoree here", 'status'=>'1', 'code' => 'miglad_', 
                        'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '4' => array( 'type'=>'text','id'=>'honoreeaddress', 'label'=>"Honoree[q]s Address", 'status'=>'1', 'code' => 'miglad_', 
                        'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '5' => array( 'type'=>'text','id'=>'honoreecountry', 'label'=>"Honoree[q]s Country", 'status'=>'1', 'code' => 'miglad_', 
                        'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '6' => array( 'type'=>'text','id'=>'honoreecity', 'label'=>'Honoree[q]s City', 'status'=>'1' , 'code' => 'miglad_', 
                         'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '7' => array( 'type'=>'text','id'=>'honoreepostalcode', 'label'=>'Honoree[q]s Postal Code', 'status'=>'1' , 'code' => 'miglad_', 
                         'uid' => ("f".date("Ymdhis"). "_" . rand()) )		   
                 ),
        'parent_id' => 'NULL',
        'depth' => 5,
        'toggle' => '1'

    ),
    '3' => array (
        'title' => 'Is this a matching gift?',
        'child' => array(
                   '0' => array( 'type'=>'text','id'=>'employer', 'label'=>'Employer[q]s Name', 'status'=>'1', 'code' => 'miglad_', 
                       'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '1' => array( 'type'=>'text','id'=>'occupation', 'label'=>'Occupation', 'status'=>'1', 'code' => 'miglad_', 
                       'uid' => ("f".date("Ymdhis"). "_" . rand()) )
                 ),
        'parent_id' => 'NULL',
        'depth' => 3,
        'toggle' => '1'
    )        
 );

	update_option('migla_form_fields', $fields);

	global $wpdb;
    $pid = $wpdb->get_results( 
			$wpdb->prepare(
				"SELECT ID FROM $wpdb->posts WHERE post_type = %s ORDER BY ID ASC" 
				, 'miglaform') 
			);
	
	$cform_id = array();
	$i = 0;
	foreach($pid as $id){
		$cform_id[$i] = $id->ID;
		$i++;
	}
	
	//GET ALL METAVALUES
    $pid2nd = $wpdb->get_results( 
			$wpdb->prepare(
				"SELECT post_id FROM $wpdb->postmeta WHERE meta_key like %s ORDER BY post_id ASC" 
				, 'mgval_%' ) 
			);
			
	foreach($pid2nd as $id2nd)
	{
		if( in_array( $id2nd->post_id, $cform_id ) )
		{
		
		}else{
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}postmeta WHERE post_id = %d AND meta_key like %s" 
										, $_POST['formID'] , 'mgval_%'
									)
					);	
		}		
	}			
	
  die();
}

 add_action("wp_ajax_miglaA_reset_cform", "miglaA_reset_cform");
  add_action("wp_ajax_nopriv_miglaA_reset_cform", "miglaA_reset_cform");

function miglaA_reset_cform() {

$fields = array (
    '0' => array (
        'title' => 'Donation Information',
        'child' =>  array(
                   '0' => array( 'type'=>'radio','id'=>'amount', 'label'=>'How much would you like to donate?', 'status'=>'3', 'code' => 'miglad_', 
                       'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '1' => array( 'type'=>'select','id'=>'campaign', 'label'=>'Would you like to donate this to a specific campaign?', 'status'=>'3', 'code' => 'miglad_', 
                       'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '2' => array( 'type'=>'radio','id'=>'repeating', 'label'=>'Is this a recurring donation?', 'status'=>'1', 'code' => 'miglad_', 
                       'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '3' => array( 'type'=>'checkbox','id'=>'mg_add_to_milist', 'label'=>'Add to mailing list?', 'status'=>'1', 'code' => 'miglad_', 
                       'uid' => ("f".date("Ymdhis"). "_" . rand()) )
                 ),
        'parent_id' => 'NULL',
        'depth' => 2,
        'toggle' => '-1'
    ),
    '1' => array (
        'title' => 'Donor Information',
        'child' => array(
                   '0' => array( 'type'=>'text','id'=>'firstname', 'label'=>'First Name', 'status'=>'3', 'code' => 'miglad_', 'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '1' => array( 'type'=>'text','id'=>'lastname', 'label'=>'Last Name', 'status'=>'3', 'code' => 'miglad_', 'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '2' => array( 'type'=>'text','id'=>'address', 'label'=>'Address', 'status'=>'1' , 'code' => 'miglad_', 'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '3' => array( 'type'=>'select','id'=>'country', 'label'=>'Country', 'status'=>'1' , 'code' => 'miglad_', 'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '4' => array( 'type'=>'text','id'=>'city', 'label'=>'City', 'status'=>'1' , 'code' => 'miglad_', 'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '5' => array( 'type'=>'text','id'=>'postalcode', 'label'=>'Postal Code', 'status'=>'1' , 'code' => 'miglad_', 'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '6' => array( 'type'=>'checkbox','id'=>'anonymous', 'label'=>'Anonymous?', 'status'=>'1' , 'code' => 'miglad_', 'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '7' => array( 'type'=>'text','id'=>'email', 'label'=>'Email', 'status'=>'3' , 'code' => 'miglad_' , 'uid' => ("f".date("Ymdhis"). "_" . rand()) )
                 ),
        'parent_id' => 'NULL',
        'depth' => 8,
        'toggle' => '-1'
    ),
    '2' => array (
        'title' => 'Is this in honor of someone?',
        'child' => array(
                   '0' => array( 'type'=>'checkbox','id'=>'memorialgift', 'label'=>"Is this a Memorial Gift?", 'status'=>'1', 'code' => 'miglad_', 
                        'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '1' => array( 'type'=>'text','id'=>'honoreename', 'label'=>"Honoree[q]s Name", 'status'=>'1', 'code' => 'miglad_', 
                        'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '2' => array( 'type'=>'text','id'=>'honoreeemail', 'label'=>"Honoree[q]s Email", 'status'=>'1', 'code' => 'miglad_', 
                        'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '3' => array( 'type'=>'textarea','id'=>'honoreeletter', 'label'=>"Write a custom note to the Honoree here", 'status'=>'1', 'code' => 'miglad_', 
                        'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '4' => array( 'type'=>'text','id'=>'honoreeaddress', 'label'=>"Honoree[q]s Address", 'status'=>'1', 'code' => 'miglad_', 
                        'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '5' => array( 'type'=>'text','id'=>'honoreecountry', 'label'=>"Honoree[q]s Country", 'status'=>'1', 'code' => 'miglad_', 
                        'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '6' => array( 'type'=>'text','id'=>'honoreecity', 'label'=>'Honoree[q]s City', 'status'=>'1' , 'code' => 'miglad_', 
                         'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '7' => array( 'type'=>'text','id'=>'honoreepostalcode', 'label'=>'Honoree[q]s Postal Code', 'status'=>'1' , 'code' => 'miglad_', 
                         'uid' => ("f".date("Ymdhis"). "_" . rand()) )		   
                 ),
        'parent_id' => 'NULL',
        'depth' => 5,
        'toggle' => '1'

    ),
    '3' => array (
        'title' => 'Is this a matching gift?',
        'child' => array(
                   '0' => array( 'type'=>'text','id'=>'employer', 'label'=>'Employer[q]s Name', 'status'=>'1', 'code' => 'miglad_', 
                       'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '1' => array( 'type'=>'text','id'=>'occupation', 'label'=>'Occupation', 'status'=>'1', 'code' => 'miglad_', 
                       'uid' => ("f".date("Ymdhis"). "_" . rand()) )
                 ),
        'parent_id' => 'NULL',
        'depth' => 3,
        'toggle' => '1'
    )        
 );

	update_post_meta( $_POST['formID'], 'migla_form_fields', $fields );     

	global $wpdb;			
    $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}postmeta WHERE post_id = %d AND meta_key like %s" 
									, $_POST['formID'] , 'mgval_%'
								)
				);

    die();
}

/***********************************************/
/*             CAMPAIGN   FINISH Nov 21st */

function updateACampaign($old, $new , $form_id)
{
	 global $wpdb;
	 //Update all donations
	 $sql = "UPDATE {$wpdb->prefix}postmeta SET meta_value = '".$new."' WHERE meta_value ='".$old."'";
	 $wpdb->query($sql);
	 
	 //update the campaign form post
	 $sql = "UPDATE {$wpdb->prefix}posts SET post_title = '".$new."' WHERE ID ='".$form_id."'";
	 $wpdb->query($sql);	 
}

  add_action("wp_ajax_miglaA_save_campaign", "miglaA_save_campaign");
  add_action("wp_ajax_nopriv_miglaA_save_campaign", "miglaA_save_campaign");

function miglaA_save_campaign() {
	global $wpdb;

	$campaign_array = $_POST['values'];
	
	foreach($campaign_array as $campaign)
	{
		if( $campaign['show'] == '-1'  )
		{
			 $sql = "UPDATE {$wpdb->prefix}posts SET post_status = 'pending' WHERE ID ='".$campaign['form_id']."'";
		}else{
			 $sql = "UPDATE {$wpdb->prefix}posts SET post_status = 'publish' WHERE ID ='".$campaign['form_id']."'";
		}
		$wpdb->query($sql);	 		   
	}

	if( isset($_POST['update']) )
	{
		$updates = (array)$_POST['update'];
		if( count($updates) > 0 && $updates[0] != '')
		{
			  foreach( $updates as $u ){
				   $change = array();
				   $change = explode( "-**-", $u);
				   updateACampaign($change[0], $change[1], $change[2]);
			  }
		}
	}

  update_option('migla_campaign', $_POST['values']);
  die();
}

  add_action("wp_ajax_miglaA_add_offline_backend", "miglaA_add_offline_backend");
  add_action("wp_ajax_nopriv_miglaA_add_offline_backend", "miglaA_add_offline_backend");

function miglaA_add_offline_backend()
{
    $new_donation = array(
		'post_title' => 'migla_donation',
		'post_content' => '',
		'post_status' => 'publish',
		'post_author' => 1,
		'post_type' => 'migla_odonation'
    );
	$new_id = wp_insert_post( $new_donation );
	
	$data	= (array)$_POST['new_offline'];
	
    $keys = array_keys( $data ); 
	$i = 0;
    foreach( $data as $metakey => $metavalue)
    {
        add_post_meta( $new_id , $metakey, $metavalue);
        $i++;
    }  
	echo $new_id;
	die();
}


  add_action("wp_ajax_miglaA_getOffDonation", "miglaA_getOffDonation");
  add_action("wp_ajax_nopriv_miglaA_getOffDonation", "miglaA_getOffDonation");

function miglaA_getOffDonation()
{
  global $wpdb;
  
  $PID = array();
  $PID =  $wpdb->get_results( 
	  $wpdb->prepare( 
	    "SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = %s OR post_type = %s", 
			"migla_odonation", "migla_odonation_p" )
        );
			
  $record_num 	= 0;
  $output 		= array();
  
  foreach( $PID as $p_id )
  {
    $fname	= get_post_meta( $p_id->ID, 'miglad_firstname' , true);
	$lname	= get_post_meta( $p_id->ID, 'miglad_lastname' , true);
	$amount	= get_post_meta( $p_id->ID, 'miglad_amount' , true);
	
	if( $amount !== false && $amount != 0 )
	{
		$output[$record_num]['miglad_firstname']	= $fname;  
		$output[$record_num]['miglad_lastname']		= $lname;  
		$output[$record_num]['miglad_amount']		= $amount;  
		$campaign									= get_post_meta( $p_id->ID, 'miglad_campaign', true);  
		$output[$record_num]['miglad_campaign']		= str_ireplace("[q]", "'", $campaign );
		$output[$record_num]['miglad_country']		= get_post_meta( $p_id->ID, 'miglad_country' , true);
		$output[$record_num]['miglad_date']			= get_post_meta( $p_id->ID, 'miglad_date' , true);  
		$output[$record_num]['miglad_time']			= get_post_meta( $p_id->ID, 'miglad_time' , true);  	
		
		$output[$record_num]['id'] 		= $p_id->ID  ;
		$output[$record_num]['remove'] 	= "<input type='hidden' name='".$p_id->ID."' class='removeRow' /><i class='fa fa-trash'></i>"; 
		$output[$record_num]['detail'] 	= "<input class='mglrec' type=hidden name='".$record_num."' >"; 

	   $output[$record_num]['miglad_charge_dispute'] = get_post_meta( intval( $p_id->ID ) , 'miglad_charge_dispute', true);
	   $output[$record_num]['miglad_session_id'] = get_post_meta( intval( $p_id->ID ) , 'miglad_session_id', true);  
	   $output[$record_num]['miglad_paymentmethod'] = get_post_meta( intval( $p_id->ID ) ,'miglad_paymentmethod' , true); 
	   $output[$record_num]['miglad_transactionType'] = get_post_meta( intval( $p_id->ID ) ,'miglad_transactionType', true ); 
	   $output[$record_num]['miglad_transactionId'] = get_post_meta( intval( $p_id->ID ) ,'miglad_transactionId', true ); 

	   $output[$record_num]['miglad_status'] = get_post_meta( intval( $p_id->ID ) ,'miglad_status', true ); 	
	   
	   $output[$record_num]['miglad_form_id'] = get_post_meta( intval( $p_id->ID ) ,'miglad_form_id', true ); 	   
	   
	   $record_num++;	  
   }
   
  }

  echo json_encode($output);
  die();
}

  add_action("wp_ajax_miglaA_remove_donation", "miglaA_remove_donation");
  add_action("wp_ajax_nopriv_miglaA_remove_donation", "miglaA_remove_donation");

function miglaA_remove_donation() {
  migla_remove_donation( $_POST['list'] ) ; 
   die();
}



/***********************************************/
/*    Progress BAR draw on Form Nov 21st still continue */
/***********************************************/
  add_action("wp_ajax_miglaA_draw_progress_bar", "miglaA_draw_progress_bar");
  add_action("wp_ajax_nopriv_miglaA_draw_progress_bar", "miglaA_draw_progress_bar");

function miglaA_draw_progress_bar() {

 /* migla_text_progressbar(  $cname, $posttype , $linkbtn, $btntext, $text ) */
  $out = "";
  if( $_POST['cname'] == "undesignated" ){
  }else{
   $out .= migla_text_progressbar( $_POST['cname'], $_POST['posttype'], "no", "no", "yes"  );
  }

  echo $out;
  die();
}

  add_action("wp_ajax_miglaA_currentTime", "miglaA_currentTime");
  add_action("wp_ajax_nopriv_miglaA_currentTime", "miglaA_currentTime");

function miglaA_currentTime()
{
       ///GET CURRENT TIME SETTINGS----------------------------------
	$php_time_zone = date_default_timezone_get();
        $t = ""; $d = "";
        $default = $_POST['timezone'];
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
		
        $now =  date("F jS, Y", strtotime($d))." ".$t;
		date_default_timezone_set( $php_time_zone );
       ///---------------------------------GET CURRENT TIME SETTINGS
 
    echo $now;
    die();
}

/****************************************************************/
/*           DATA RETRIEVING FOR REPORT  FINISH Nov 23st        */
/***************************************************************/
add_action("wp_ajax_miglaA_export_report", "miglaA_export_report");
add_action("wp_ajax_nopriv_miglaA_export_report", "miglaA_export_report");

function miglaA_export_report() 
{
	global $wpdb;
	$meta = array();
	$meta = $wpdb->get_results(
					"SELECT DISTINCT meta_key FROM {$wpdb->prefix}postmeta where meta_key like 'miglad%' OR meta_key like 'miglac%'"
			);
	
	$PID = 	array();
	$PID =  $wpdb->get_results( 
				$wpdb->prepare( 
					"SELECT ID FROM {$wpdb->prefix}posts WHERE post_type like %s ORDER BY ID", $_POST['post_type']
				)
			);	
	
	$data = array();
	$row = 0;

	foreach( $PID as $id )
	{
		foreach( $meta as $m )
		{
			$data[$row][$m->meta_key]	= get_post_meta( intval( $id->ID ) , $m->meta_key, true );
		}
		$data[$row]['id'] =  intval( $id->ID ) ;
		$row++;
	}

	echo json_encode($data);
	die();
}

add_action("wp_ajax_miglaA_detail_report", "miglaA_detail_report");
add_action("wp_ajax_nopriv_miglaA_detail_report", "miglaA_detail_report");

function miglaA_detail_report() 
{
   global $wpdb;
   $post_id		= $_POST['post_id'];
   $form_id		= $_POST['form_id'];
   
	$details 	= array();
	$form		= array();    
	$data 		= array();
	$custom_values	= array();
	$output		= array();
	
	$custom_values['999'] = 'NULL';
	
	if( $form_id == '')
	{
		$form = get_option('migla_form_fields');

		$custom_pid = $wpdb->get_results(  
					$wpdb->prepare(	"SELECT meta_key, meta_value FROM $wpdb->posts INNER JOIN $wpdb->postmeta
									on $wpdb->posts.ID 	= $wpdb->postmeta.post_id
									WHERE post_type 	= %s 
									ORDER BY ID ASC" 
					, 'migla_custom_values'
					) );	

		if( count($custom_pid) > 0 ){
			foreach( $custom_pid as $cid )
			{
				$custom_values[($cid->meta_key)] = $cid->meta_value; 
			}
		}			
		
	}else{
	
		$form = get_post_meta( $form_id, 'migla_form_fields', true );
		
		$custom_pid = $wpdb->get_results(  
						$wpdb->prepare(	"SELECT meta_key, meta_value FROM $wpdb->postmeta
									WHERE meta_key like %s AND post_id = %d" 
						, 'mgval_f%', $form_id
					));	

		if( count($custom_pid) > 0 ){
			foreach( $custom_pid as $cid )
			{
				$custom_values[($cid->meta_key)] = $cid->meta_value; 
			}
		}
		
	}
	
	$data =  $wpdb->get_results( $wpdb->prepare( 
					"SELECT meta_key, meta_value FROM {$wpdb->prefix}postmeta WHERE post_id = %d
					", 
					$post_id 				)
			); 
	
   foreach( $data  as $datum )
   {
      if( $datum->meta_key == 'miglad_paymentdata' || $datum->meta_key == '' || $datum->meta_value == '')
	  {
	  }else{
			$details[ ($datum->meta_key) ]	= $datum->meta_value ;
	  }
   }

   $output[0]	= $form;
   $output[1]	= $details ;
   $output[2]	= $custom_values;
      
   echo json_encode( $output );   
   die();
}

  add_action("wp_ajax_miglaA_report", "miglaA_report");
  add_action("wp_ajax_nopriv_miglaA_report", "miglaA_report");

function miglaA_report() 
{
  global $wpdb;
  
  $PID = array();
  $PID =  $wpdb->get_results( 
	  $wpdb->prepare( 
	    "SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = %s", "migla_donation"
         )
        );
			
  $record_num 	= 0;
  $output 		= array();
  
  foreach( $PID as $p_id )
  {
    $fname	= get_post_meta( $p_id->ID, 'miglad_firstname' , true);
	$lname	= get_post_meta( $p_id->ID, 'miglad_lastname' , true);
	$amount	= get_post_meta( $p_id->ID, 'miglad_amount' , true);
	
	if( $amount !== false && $amount != 0 )
	{
		$output[$record_num]['miglad_firstname']	= $fname;  
		$output[$record_num]['miglad_lastname']		= $lname;  
		$output[$record_num]['miglad_amount']		= $amount;  
		$output[$record_num]['miglad_campaign']		= get_post_meta( $p_id->ID, 'miglad_campaign', true);  
		$output[$record_num]['miglad_country']		= get_post_meta( $p_id->ID, 'miglad_country' , true);
		$output[$record_num]['miglad_date']			= get_post_meta( $p_id->ID, 'miglad_date' , true);  
		$output[$record_num]['miglad_time']			= get_post_meta( $p_id->ID, 'miglad_time' , true);  	
		
		$output[$record_num]['id'] 		= $p_id->ID  ;
		$output[$record_num]['remove'] 	= "<input type='hidden' name='".$p_id->ID."' class='removeRow' /><i class='fa fa-trash'></i>"; 
		$output[$record_num]['detail'] 	= "<input class='mglrec' type=hidden name='".$record_num."' >"; 

	   $output[$record_num]['miglad_charge_dispute'] = get_post_meta( intval( $p_id->ID ) , 'miglad_charge_dispute', true);
	   $output[$record_num]['miglad_session_id'] = get_post_meta( intval( $p_id->ID ) , 'miglad_session_id', true);  
	   $output[$record_num]['miglad_paymentmethod'] = get_post_meta( intval( $p_id->ID ) ,'miglad_paymentmethod' , true); 
	   $output[$record_num]['miglad_transactionType'] = get_post_meta( intval( $p_id->ID ) ,'miglad_transactionType', true ); 
	   $output[$record_num]['miglad_transactionId'] = get_post_meta( intval( $p_id->ID ) ,'miglad_transactionId', true ); 
	   $output[$record_num]['miglad_subscription_id'] = get_post_meta( intval( $p_id->ID ) ,'miglad_subscription_id' , true);
	   $output[$record_num]['miglad_customer_id'] = get_post_meta( intval( $p_id->ID ) ,'miglad_customer_id', true ); 

	   $output[$record_num]['miglad_form_id'] = get_post_meta( intval( $p_id->ID ) ,'miglad_form_id', true ); 	   
	   
	   $record_num++;	  
   }
   
  }

  echo json_encode($output);
  die();
}

  add_action("wp_ajax_miglaA_get_extra_data_report", "miglaA_get_extra_data_report");
  add_action("wp_ajax_nopriv_miglaA_get_extra_data_report", "miglaA_get_extra_data_report");

function miglaA_get_extra_data_report() 
{
  $out 		= array();
  $out[0] 	= get_option('migla_campaign');
  $out[1] 	= get_option('migla_world_countries');
  $out[2] 	= get_option('migla_US_states');
  $out[3] 	= get_option('migla_Canada_provinces');
  $out[4] 	= get_option('migla_undesignLabel');
    
  echo json_encode($out);
 
  die();
}

  add_action("wp_ajax_miglaA_get_number_and_total", "miglaA_get_number_and_total");
  add_action("wp_ajax_nopriv_miglaA_get_number_and_total", "miglaA_get_number_and_total");

function miglaA_get_number_and_total() {
  $out = array();
  $out = migla_number_and_total( $_POST['campaign'] );
  
  echo json_encode($out);
 
  die();
}


/*********** Testing Emails ***********************/
  add_action("wp_ajax_miglaA_test_email", "miglaA_test_email");
  add_action("wp_ajax_nopriv_miglaA_test_email", "miglaA_test_email");

function miglaA_test_email()
{
  $postData = array();
  $postData['miglad_email']     = $_POST['testemail'];
  $postData['miglad_firstname'] = 'John';
  $postData['miglad_lastname']  = 'Doe';
  $postData['miglad_amount']    = 100;
  $postData['miglad_date']      = date("Y-m-d", time());
  $postData['miglad_address']   = "Houwei Ave Road";
  $postData['miglad_country']   = "Canada";
  $postData['miglad_province']  = "British Columbia";
  $postData['miglad_postalcode'] = "1234";
  $postData['miglad_campaign']  = 'Save Sun Bears';
  $postData['miglad_repeating'] = 'no';
  $postData['miglad_anonymous'] = 'no';
  $ne                            = get_option('migla_notif_emails');

  $test    = mg_send_thank_you_email( $postData, $_POST['email'], $_POST['emailname'] );
  mg_send_notification_emails( $postData, $_POST['email'], $_POST['emailname'] , $ne );

  if( $test ){ 
       echo "Email has been sent to ".$_POST['testemail']; 
  } else { 
       echo "Sending email failed"; 
  }

  die();
}

  add_action("wp_ajax_miglaA_test_hEmail", "miglaA_test_hEmail");
  add_action("wp_ajax_nopriv_miglaA_test_hEmail", "miglaA_test_hEmail");

function miglaA_test_hEmail(){
  $postData = array();
  $postData['miglad_honoreeemail']     = $_POST['testemail'];
  $postData['miglad_firstname'] = 'John';
  $postData['miglad_lastname']  = 'Doe';
  $postData['miglad_amount']    = 100;
  $postData['miglad_date']      = date("Y-m-d", time());
  $postData['miglad_honoreename'] = 'Jane Doe';
  $postData['miglad_address']   = "Houwei Ave Road";
  $postData['miglad_country']   = "Canada";
  $postData['miglad_province']  = "British Columbia";
  $postData['miglad_postalcode'] = "1234";
  $postData['miglad_campaign']  = 'Save Sun Bears';
  $postData['miglad_repeating'] = 'no';
  $postData['miglad_anonymous'] = 'no';

  $e = get_option('migla_replyTo');
  $en = get_option('migla_replyToName');

  $content = "A donation has been made in your honour";
  $test    = mg_send_hletter(  $postData , $e, $en, $content, $postData['miglad_date'] );

  if( $test ){ 
       echo "Email has been sent to ".$_POST['testemail']; 
  } else { 
       echo "Sending email failed"; 
  }

  die();

}

  add_action("wp_ajax_miglaA_test_offline_email", "miglaA_test_offline_email");
  add_action("wp_ajax_nopriv_miglaA_test_offline_email", "miglaA_test_offline_email");

function miglaA_test_offline_email()
{
  $postData = array();
  $postData['miglad_email']     = $_POST['testemail'];
  $postData['miglad_firstname'] = 'John';
  $postData['miglad_lastname']  = 'Doe';
  $postData['miglad_amount']    = 100;
  $postData['miglad_date']      = date("Y-m-d", time());
  $postData['miglad_address']   = "Houwei Ave Road";
  $postData['miglad_country']   = "Canada";
  $postData['miglad_province']  = "British Columbia";
  $postData['miglad_postalcode'] = "1234";
  $postData['miglad_campaign']  = 'Save Sun Bears';
  $postData['miglad_repeating'] = 'no';
  $postData['miglad_anonymous'] = 'no';

  $e  = get_option('migla_replyTo');
  $en = get_option('migla_replyToName');

  $test = mg_send_offline_first_email( $postData , $e, $en );;

  if( $test ){ 
       echo "Email has been sent to ".$_POST['testemail']; 
  } else { 
       echo "Sending email failed"; 
  }

  die();
}

  add_action("wp_ajax_miglaA_change_donation", "miglaA_change_donation");
  add_action("wp_ajax_nopriv_miglaA_change_donation", "miglaA_change_donation");

function miglaA_change_donation()
{
  $post_id    = $_POST['post_id'];
  $arrayData = (array)$_POST['arrayData'];
  
  $keys = array_keys( $arrayData ); $i = 0;
  foreach( (array)$arrayData as $value)
  {
       update_post_meta( $post_id , $value[0], $value[1] );
     $i++;
  }

   echo "done";
   die();
}

  add_action("wp_ajax_miglaA_get_thank_you_page_url", "miglaA_get_thank_you_page_url");
  add_action("wp_ajax_nopriv_miglaA_get_thank_you_page_url", "miglaA_get_thank_you_page_url");

function miglaA_get_thank_you_page_url(){
   $return_url = get_option('migla_form_url');
   $return_id  = get_option('migla_thank_you_page');

   if( $return_id == false || $return_id == '' ){

   }else{
      $return_obj = (array)get_post($return_id);
      $return_url = $return_obj['guid'];
   }

   echo $return_url;
   die();
}

  add_action("wp_ajax_miglaA_load_circle", "miglaA_load_circle");
  add_action("wp_ajax_nopriv_miglaA_load_circle", "miglaA_load_circle");

function miglaA_load_circle()
{
        $total        = 100;
        $target       = 200;
        $percent       = number_format(  ( $total / $target) * 100 , 2);		
        $output = "";
        $id = 'admin';
        $output .= migla_circle_js( $id );

	$output .= "<div id='mg_circle_" . $id . "' class='migla_circle_bar' ><div class='migla_circle_text' id='migla_circle_text".$id."'></div></div>";
        $output .= "";

        //Circle
        $output .= "<input type='hidden' class='migla_circle_id' value='".$id."' >";
        $output .= "<input type='hidden' class='migla_circle_value' value='".($percent/100)."' >";
        $output .= "<input type='hidden' class='migla_circle_percentage' value='".$percent."' >";
        $circle_settings = (array)$_POST['settings'];
        $keys = array_keys($circle_settings[0]);
        foreach( $keys as $key  ){
            $output .= "<input type='hidden' class='migla_circle_" . $key. "' value='" .$circle_settings[0][$key]. "'>";
        }

        echo $output;

        die();
}

  add_action("wp_ajax_miglaA_new_mform", "miglaA_new_mform");
  add_action("wp_ajax_nopriv_miglaA_new_mform", "miglaA_new_mform");

function miglaA_new_mform()
{
    // Create post object
	$my_post = array(
	  'post_title'    => $_POST['title'],
	  'post_content'  => $_POST['desc'],
	  'post_status'   => 'publish',
	  'post_author'   => 1,
	  'post_type'     => 'miglaform'
	);

	// Insert the post into the database
	$post_id = wp_insert_post( $my_post ); 
	
	$fields = migla_init_form();
	add_post_meta( $post_id, 'migla_form_fields', $fields);
	
    $amounts[0]['amount'] = 25;
    $amounts[0]['perk'] = '';	
    $amounts[1]['amount'] = 50;
    $amounts[1]['perk'] = '';	
    $amounts[2]['amount'] = 75;
    $amounts[2]['perk'] = '';	
    $amounts[3]['amount'] = 100;
    $amounts[3]['perk'] = '';	

	add_post_meta( $post_id, 'migla_amounts' , $amounts ); 
	
	add_post_meta ( $post_id, 'migla_hideCustomAmount', 'no' );
	add_post_meta ( $post_id, 'migla_amount_btn' , 'button' );
	add_post_meta ( $post_id, 'migla_amount_box_type', 'box' );
	add_post_meta ( $post_id, 'migla_warning_1', 'Please insert all the required fields' );
	add_post_meta ( $post_id, 'migla_warning_2', 'Please insert correct email' );
	add_post_meta ( $post_id, 'migla_warning_3', 'Please fill in a valid amount' );
	add_post_meta ( $post_id, 'migla_custom_amount_text', 'Custom');
    add_post_meta ( $post_id, 'migla_form_url', '');
	
	echo $post_id;
	die();
}

  add_action("wp_ajax_miglaA_avaliable_form", "miglaA_avaliable_form");
  add_action("wp_ajax_nopriv_miglaA_avaliable_form", "miglaA_avaliable_form");

function miglaA_avaliable_form() 
{
  global $wpdb;
  $rows = array();
  $rows =  $wpdb->get_results( 
	  $wpdb->prepare( 
	    "SELECT ID, post_title, post_content,post_date FROM {$wpdb->prefix}posts WHERE post_type = %s"
             , 'miglaform'
           )
          );
 
  $out = array();  
 
  if( count($rows) > 0)
  {   
	$i = 0;
	foreach( (array)$rows as $row )
	{ 
       $out[$i]['post_id'] = $row->ID;
       $out[$i]['post_title'] = $row->post_title;
       $out[$i]['post_description'] = $row->post_content;
       $out[$i]['post_date'] = $row->post_date;
	   $i++;
    }	 
  }
    
  echo json_encode($out);
  die();
}

  add_action("wp_ajax_miglaA_delete_mform", "miglaA_delete_mform");
  add_action("wp_ajax_nopriv_miglaA_delete_mform", "miglaA_delete_mform");

function miglaA_delete_mform()
{
  global $wpdb;
   
  //Delete all the meta data
  $wpdb->query( 
	$wpdb->prepare( 
         "DELETE FROM {$wpdb->prefix}postmeta WHERE post_id = %d" 
          , $_POST['post_id']
        )
     );    
   
   //Delete the post
   $wpdb->query( 
	 $wpdb->prepare( 
         "DELETE FROM {$wpdb->prefix}posts WHERE ID = %d" 
          , $_POST['post_id']
        )
     );  
	 
	die();
}

  add_action("wp_ajax_miglaA_update_metadata", "miglaA_update_metadata");
  add_action("wp_ajax_nopriv_miglaA_update_metadata", "miglaA_update_metadata");

function miglaA_update_metadata()
{
   update_post_meta ( $_POST['post_id'] , $_POST['meta_key'], $_POST['meta_value'] );
   die();
}


  add_action("wp_ajax_miglaA_delete_metadata", "miglaA_delete_metadata");
  add_action("wp_ajax_nopriv_miglaA_delete_metadata", "miglaA_delete_metadata");

function miglaA_delete_metadata()
{
}

function updateACampaignCretor($old, $new , $form_id)
{
	 $sql = "UPDATE {$wpdb->prefix}posts SET post_title = '".$new."' WHERE ID ='".$form_id."'";
	 $wpdb->query($sql);	 
}

  add_action("wp_ajax_miglaA_save_campaign_creator", "miglaA_save_campaign_creator");
  add_action("wp_ajax_nopriv_miglaA_save_campaign_creator", "miglaA_save_campaign_creator");

function miglaA_save_campaign_creator() {
	global $wpdb;
	$d = '';

	if( isset($_POST['values']) && $_POST['values'] != '' ){
		$d = serialize($_POST['values']);
	}

	if( isset($_POST['update']) ){
		$up = (array)$_POST['update'];
		if( count($up) > 0 && $up[0] != '')
		{
			  foreach( $up as $u ){
				   $change = array();
				   $change = explode( "-**-", $u);
				    updateACampaignCretor($change[0], $change[1], $change[2]);
			  }
		}
	}

  update_option('migla_campaign', $_POST['values']);
  die();
}

  add_action("wp_ajax_miglaA_new_mCampaignCreator", "miglaA_new_mCampaignCreator");
  add_action("wp_ajax_nopriv_miglaA_new_mCampaignCreator", "miglaA_new_mCampaignCreator");

function miglaA_new_mCampaignCreator()
{
    // Create post object
	$my_post = array(
	  'post_title'    => $_POST['title'],
	  'post_content'  => $_POST['desc'],
	  'post_status'   => 'publish',
	  'post_author'   => 1,
	  'post_type'     => 'miglacampaigncreator'
	);

	// Insert the post into the database
	$post_id = wp_insert_post( $my_post ); 
	
	add_post_meta( $post_id, 'migla_campaign_creator', $_POST['campaign'] ); 	
	
	$circle = array();
    $circle[0]['size'] = 100; 
    $circle[0]['start_angle'] = 0; 
    $circle[0]['thickness'] = 10; 
    $circle[0]['reverse'] = 'yes'; 
    $circle[0]['line_cap'] = 'but';
    $circle[0]['fill'] = '#00ff00';
    $circle[0]['animation'] = 'none';
    $circle[0]['inside'] = 'none';	
	add_post_meta( $post_id, 'migla_circle_settings', $circle );
	add_post_meta( $post_id, 'migla_circle_layout', 'mg_centered' );
		
	$campaigns 	= get_option('migla_campaign');
	$name		= '';
	foreach( $campaigns as $campaign )
	{
		if( $campaign['form_id'] ==  $_POST['campaign'] )
		{
			$name = $campaign['name'];
			break;
		}
	}	
	add_post_meta( $post_id, 'migla_cmpcreator_campaign_name', $name );
	
	add_post_meta( $post_id, 'migla_circle_box_html', 'You can put HTML here' ); 
    add_post_meta($post_id, 'migla_cmpcreator_text1', 'Funded');
    add_post_meta($post_id, 'migla_cmpcreator_text2', 'Target');
    add_post_meta($post_id, 'migla_cmpcreator_text3', 'Donors');	
    add_post_meta($post_id, 'migla_cmpcreator_textalign', 'mg_left-right');	
	
	echo $post_id;
	die();
}

  add_action("wp_ajax_miglaA_update_report", "miglaA_update_report");
  add_action("wp_ajax_nopriv_miglaA_update_report", "miglaA_update_report");

function miglaA_update_report()
{
	$data	= $_POST['data_send'];
	$id		= $_POST['record_id'];
	$form_id	= $_POST['new_form_id'];
	
	$keys	= array_keys($data);
	
	foreach( $keys as $key )
	{
		update_post_meta( $id, $data[$key][0],  $data[$key][1] );
	}
	
	update_post_meta( $id, 'miglad_form_id', $form_id ); 
	if( $form_id != '' )
	{
		$campaigns = get_option('migla_campaign');
		foreach( $campaigns as $c )
		{
			if( $c['form_id'] == $form_id ){
				update_post_meta( $id, 'miglad_campaign', $c['name'] ); 
			}
		}
	}
	
	die();
}

  add_action("wp_ajax_miglaA_detail_recurring", "miglaA_detail_recurring");
  add_action("wp_ajax_nopriv_miglaA_detail_recurring", "miglaA_detail_recurring");

function miglaA_detail_recurring()
{
	global $wpdb;

	$pid = $wpdb->get_results(  
					$wpdb->prepare(	"SELECT DISTINCT post_id FROM $wpdb->postmeta
									WHERE meta_key = %s AND meta_value = %s 
									ORDER BY post_id DESC" 
					, 'miglad_subscription_id' , $_POST['subscr_id']
				) );	
	
	$row = 0;
	$out = array();
	
	foreach( $pid as $datum )
	{
		$out[$row]['date']	= get_post_meta( $datum->post_id, 'miglad_date', true);
		$out[$row]['time']	= get_post_meta( $datum->post_id, 'miglad_time', true);
		$out[$row]['trans_id']	= get_post_meta( $datum->post_id, 'miglad_transactionId', true);
		$row++;
	}

	echo json_encode($out);
	die();
}

  add_action("wp_ajax_miglaA_test_constant_contact", "miglaA_test_constant_contact");
  add_action("wp_ajax_nopriv_miglaA_test_constant_contact", "miglaA_test_constant_contact");

function miglaA_test_constant_contact()
{
    //add to Constant Contact
    $cc = new migla_constant_contact_class();
    $out = $cc->add_to_milist_test( $_POST['email'], 'Omar', 'Omar', true );
	
	echo json_encode($out);
	die();
}

  add_action("wp_ajax_miglaA_save_option", "miglaA_save_option");
  add_action("wp_ajax_nopriv_miglaA_save_option", "miglaA_save_option");

function miglaA_save_option()
{
   add_option( $_POST['option_name'], $_POST['option_value'] );
   die();
}


  add_action("wp_ajax_miglaA_authenticate_recaptcha", "miglaA_authenticate_recaptcha");
  add_action("wp_ajax_nopriv_miglaA_authenticate_recaptcha", "miglaA_authenticate_recaptcha");

function miglaA_authenticate_recaptcha()
{

    $captcha_message = '';
	$captcha	= $_POST['response_send'];
	$secretKey      = get_option('migla_captcha_secret_key');
	$ip 		= $_SERVER['REMOTE_ADDR'];
	$response	= file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
	$responseKeys   = json_decode( $response , true );

	if( intval($responseKeys["success"]) !== 1) 
	{
		$captcha_message = 'failed';
	} else {
		$captcha_message = 'success';				  
	}

	echo $captcha_message;
	die();
}


  add_action("wp_ajax_miglaA_mailchimp_test", "miglaA_mailchimp_test");
  add_action("wp_ajax_nopriv_miglaA_mailchimp_test", "miglaA_mailchimp_test");

function miglaA_mailchimp_test()
{
	$MC    = new migla_mailchimp_class();
	$postData = array();
	$postData['miglad_email'] = $_POST['email'];
	$postData['miglad_firstname'] = 'John';
	$postData['miglad_lastname'] = 'Doe';
	
	$message = $MC->subscribe_contact( $postData , true ) ;
	
	echo $message;
	die();
}

  add_action("wp_ajax_miglaA_constantcontact_test", "miglaA_constantcontact_test");
  add_action("wp_ajax_nopriv_miglaA_constantcontact_test", "miglaA_constantcontact_test");

function miglaA_constantcontact_test()
{
	$CC = new migla_constant_contact_class();
	$message = $CC->add_to_milist_test( $_POST['email'], 'Jane', 'Doe', true );
	
	echo json_encode($message);
	die();
}
?>