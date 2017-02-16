<?php
/*
 Plugin Name: Total Donations
 Plugin URI: http://totaldonations.com/
 Description: A plugin for accepting donations.
 Version: 2.0.5
 Author: Binti Brindamour and Astried Silvanie
 Author URI: http://calmar-webmedia.com/
 License: Licensed
*/

require_once 'migla-donation-admin.php';
require_once 'migla-functions.php';
require_once 'migla_class_email_handler.php';
require_once 'migla-currency.php';
require_once 'migla-geography.php';
require_once 'migla-timezone.php';
require_once 'migla-locale.php';
require_once 'migla-icon-style.php';
require_once 'migla_ajax_functions.php';
require_once 'migla_class_form.php';

include_once 'migla_class_ipn.php'; 
include_once 'migla_class_webhook.php';
include_once 'migla_class_authorize.php';

require_once 'migla-donation-widget.php';
require_once 'migla-top-donor-widget.php';
require_once 'migla-bar-widget.php';
require_once 'migla-circle-widget.php';

/** Shortcodes **/

include_once 'shortcodes//migla_sc_textamountraised.php';
include_once 'shortcodes//migla_sc_progress_bar.php';
include_once 'shortcodes//migla_sc_circle.php';
include_once 'shortcodes//migla_sc_recent_donor.php';
include_once 'shortcodes//migla_sc_topdonors.php';

migla_call_hooks();
 
add_action( 'plugins_loaded', 'migla_donate_plugins_loaded' );

add_action('admin_enqueue_scripts', 'migla_load_admin_scripts');

register_activation_hook( __FILE__, 'migla_donation_active_' );

register_deactivation_hook( __FILE__, 'migla_donation_deactived_' );

add_action( 'migla_hook_IPN', 'migla_paypal_ipn_frontend' );

function migla_paypal_ipn_frontend()
{
   $obj_ipn_handler = new migla_front_ipn_handler;
   $obj_ipn_handler->migla_paypal_ipn_frontend();
}


if( isset( $_GET['migla_listener'] ) || isset( $_POST['migla_listener'] ) )
{ 
   do_action( 'migla_hook_IPN' );
}

function migla_donation_active_( $networkwide )
{
    global $wpdb;
                 
    if( function_exists('is_multisite') && is_multisite() )
	{    
        if ($networkwide) 
		{
            $old_blog 	= $wpdb->blogid;
            
            $blogids 	= $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ($blogids as $blog_id) 
			{
                switch_to_blog($blog_id);
                migla_donation_active();
            }
            switch_to_blog($old_blog);
            return;
        }   
    }
	
	migla_donation_active();
}

/************************ INITIALIAZE VARIABLES & ACTIVATED *********************************************/
function miglainit_option( $key, $value )
{
  $op = get_option($key);
  if( $op == false )
  { 
	add_option( $key , $value );
  }
}

function migla_change_form_structure()
{
  $d = (array)get_option('migla_form_fields'); $group = 0;
  foreach ( (array) $d as $f ){
      $newchild = array();
      $child = $f['child']; 
      $row = 0;
      
      if( count( $child ) > 0 )
      {
            foreach ( (array)$child as $c ){
               $keys = array_keys( $child[$row] );

               foreach ( (array)$keys as $k ){
                   $newchild[$row][$k] = $child[$row][$k];
                }

                $newchild[$row]['uid'] = ("f".date("Ymdhis"). "_" . rand());
                $row++;
            }
      }
    $d[$group]['child'] = $newchild;
    $group++;
  } 

  //print_r( $d );
  return $d;
}

function migla_donation_active() {

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

 miglainit_option( 'migla_daybeforeclean' , 'no' );
  
 /////FORM
 $current_form = get_option( 'migla_form_fields' ) ;
 if( $current_form == false  ){
     add_option( 'migla_form_fields', $fields ) ;
 }else{
     if( get_option('migla_install') ==  false ){
        $new_form =  migla_change_form_structure();
        update_option( 'migla_form_fields', $new_form ) ;
     }
 }

 miglainit_option('migla_undesignLabel', 'undesignated');
 miglainit_option('migla_hideUndesignated', 'no');

 //CAMPAIGN
 miglainit_option( 'migla_campaign' , '' );

//THEME SETTINGS
 miglainit_option( 'migla_tabcolor', '#eeeeee') ;
 miglainit_option( 'migla_2ndbgcolor' , '#fafafa,1' ); 
 miglainit_option( 'migla_2ndbgcolorb' , '#eeeeee,1,1' ); 
 miglainit_option( 'migla_borderRadius' , '8,8,8,8' );

 $barinfo = "We have collected [total] of our [target] target. It is [percentage] of our goal for the [campaign] campaign";
 miglainit_option('migla_progbar_info', $barinfo); 
 miglainit_option( 'migla_bar_color' , '#428bca,1' );
 miglainit_option( 'migla_progressbar_background', '#bec7d3,1');
 miglainit_option( 'migla_wellboxshadow', '#969899,1, 1,1,1,1');	
 
 miglainit_option( 'migla_bglevelcoloractive', '#ba9cb5');
 miglainit_option( 'migla_bglevelcolor', '#eeeeee' );  
 miglainit_option( 'migla_borderlevelcolor', '#b0b0b0');
 miglainit_option( 'migla_borderlevel', '1');

 $arr = array( 'Stripes' => 'yes', 'Pulse' => 'yes', 'AnimatedStripes' => 'yes', 'Percentage' => 'yes' );
 miglainit_option( 'migla_bar_style_effect' , $arr);

////////// EMAILS ////////////////////////////////////////////////////////
$thankyou = "Dear [firstname] [lastname],[newline][newline]
Thank you for your donation of [amount] on the [date]. Your help is deeply appreciated and your generosity will make an immediate difference to our cause. We'd like to extend our heartfelt thanks for your contribution. We appreciate your generosity.";

  miglainit_option( 'migla_thankyoupage' , $thankyou);
  
  miglainit_option( 'migla_thankyouemail' , $thankyou);
  miglainit_option('migla_thankSbj', 'Thank you for your donation'); 
  miglainit_option('migla_thankBody',   $thankyou); 
  miglainit_option('migla_thankRepeat', 'This donation will be repeated.[newline]'); 
  miglainit_option('migla_thankAnon', 'Your name will not appear in public.[newline][newline]'); 
  miglainit_option('migla_thankSig', 'Sincerely, [newline]Our team'); 

//Honoree's letter
 miglainit_option('migla_honoreESbj', 'A donation was made in your name.'); 
 $honoreebody = "Dear [honoreename],[newline][newline]We wanted to let you know that a donation has been made in honor of you for $[amount] on [date] by [firstname] [lastname]. Thank you for your support.";

 miglainit_option('migla_honoreEBody', $honoreebody); 
 miglainit_option('migla_honoreECustomIntro', '[firstname] [lastname] has included a message for you below:[newline][newline]'); 
 miglainit_option('migla_honoreERepeat', 'This donation will be repeated.[newline]'); 
 miglainit_option('migla_honoreEAnon', 'Your name will not appear in public.[newline][newline]'); 
 miglainit_option('migla_honoreESig', 'Sincerely, [newline]Our team'); 

 miglainit_option( 'migla_replyTo' , '');
 miglainit_option( 'migla_replyToName' , '');
 miglainit_option( 'migla_notif_emails','');

	////////////AMOUNTS//////////////////////////////
	$amounts = get_option( 'migla_amounts') ;
	if( $amounts == false || $amounts == '' )
	{
		$f[0]['amount'] 	= '10';
		$f[0]['perk'] 		= 'perk-1';
		$f[1]['amount'] 	= '25';
		$f[1]['perk'] 		= 'perk-2';
		$f[2]['amount'] 	= '50';
		$f[2]['perk'] 		= 'perk-3';
		$f[3]['amount']		= '100';
		$f[3]['perk']		= 'perk-4';
		
		miglainit_option( 'migla_amounts' , $f);		
		   		
	}else{
	
		$keys_amount = array_keys($amounts);
		$idx = 0;	   
	    if( !isset($amounts[0]['amount']) )
		{
			$new_amounts = array();
			$j = 0;
			foreach( $keys_amount as $key )
			{
				$new_amounts[$j]['amount'] 	= $amounts[$key] ; 
				$new_amounts[$j]['perk'] 	= '';
				$j++;
			}
			update_option( 'migla_amounts', $new_amounts ) ;
		}
	}
 //////CURRENCY & COUNTRY///////////////
 miglainit_option( 'migla_world_countries', (array)migla_get_world_countries() );
 miglainit_option( 'migla_default_country', 'Canada');
 miglainit_option( 'migla_US_states', (array)migla_get_US_states() );
 miglainit_option( 'migla_Canada_provinces', (array)migla_get_Canada_provinces() );

 miglainit_option( 'migla_currencies' , (array)migla_get_currency_array() ); //array of array
 miglainit_option( 'migla_default_currency' , 'CAD');
 miglainit_option( 'migla_thousandSep' , ',');
 miglainit_option( 'migla_decimalSep' , '.');
 miglainit_option( 'migla_curplacement' , 'before');
 miglainit_option( 'migla_showDecimalSep' , 'yes');

 ///////////TimeZone////////////////////////
 miglainit_option( 'migla_timezones', (array)migla_get_timezone() );
 miglainit_option( 'migla_default_timezone', 'Server Time' );

 miglainit_option( 'migla_form_url', '' );

 miglainit_option('migla_show_recover', 'no') ;
 miglainit_option('migla_use_nonce', 'no');
 miglainit_option('migla_delete_settings', 'no');

 ////////VERSION/////////////////////////////
 miglainit_option('migla_install', '2.3.1.'.time() ) ;

////CORS FALSE ALARM///////////////////
 miglainit_option('migla_allow_cors' , 'no' );
 miglainit_option('migla_sort_level', 'rsort');
 miglainit_option('migla_show_bar', 'yes');

  /*** STRIPE ****/
   $cc_label = array();
     $cc_label = array();
     $cc_label[0][1] = 'Stripe'; 
     $cc_label[1][1] = 'Your Name';
     $cc_label[2][1] = 'as it appears on your card';
     $cc_label[3][1] = 'Card Number';
     $cc_label[4][1] = 'Your Card Number';
     $cc_label[5][1] = 'Expiration//CVC';
     $cc_label[6][1] = 'CVC';
     miglainit_option('migla_stripe_cc_info', $cc_label ); 
	miglainit_option('migla_livePK' , '');   
	miglainit_option('migla_liveSK' , '');
	miglainit_option('migla_testPK' , '');   
	miglainit_option('migla_testSK' , '');
	miglainit_option('migla_stripemode' , 'test' );
	miglainit_option('migla_show_stripe' , 'no');
	miglainit_option('miglaStripeButtonChoice', 'stripeButton' );
	miglainit_option('migla_stripecssbtnclass', '');
    miglainit_option('migla_stripecssbtntext', 'Donate Now');
	miglainit_option('migla_stripecssbtnstyle', 'Default');
	miglainit_option('migla_stripebuttonurl', '');
	miglainit_option('migla_wait_stripe', 'Just a moment while we process your donation');
	
    /*** AUTHORIZE.NET ****/
     $cc_label_auth = array();
     $cc_label_auth[0][1] = 'Authorize.Net';     //0 
     $cc_label_auth[1][1] = 'Your Name';             //1
     $cc_label_auth[2][1] = 'First Name'; //2
     $cc_label_auth[3][1] = 'Your Last Name';            //3
     $cc_label_auth[4][1] = 'Last Name'; //4
     $cc_label_auth[5][1] = 'Card Number';            //5
     $cc_label_auth[6][1] = 'Your Card Number';        //6
     $cc_label_auth[7][1] = 'Expiration//CVC';           //7
     $cc_label_auth[8][1] = 'CVC';                 //8
     miglainit_option('migla_authorize_cc_info',  $cc_label_auth );  
	 miglainit_option('migla_payment_authorize', 'sandbox');
	 miglainit_option('miglaAuthorizeButtonChoice', 'cssButton');
	 miglainit_option('migla_authorizecssbtnclass', '');
	 miglainit_option('migla_authorizecssbtntext', 'Donate Now'); 
	 miglainit_option('migla_authorizecssbtnstyle', 'Default'); 
     miglainit_option('migla_payment_authorize', 'sandbox' );
	 miglainit_option('migla_wait_authorize', 'Just a moment while we process your donation');

	 /***PAYPAL***/
     $cc_label_paypal = array();
     $cc_label_paypal[0][1] = 'Paypal';     //0 
     $cc_label_paypal[1][1] = 'Pay with Credit Card';     //1 
     $cc_label_paypal[2][1] = 'Pay with paypal account';  //2 
     $cc_label_paypal[3][1] = 'Your Name';             //3
     $cc_label_paypal[4][1] = 'First Name'; //4
     $cc_label_paypal[5][1] = 'Your Name';            //5
     $cc_label_paypal[6][1] = 'Last Name'; //6
     $cc_label_paypal[7][1] = 'Card Number';            //7
     $cc_label_paypal[8][1] = 'Your Card Number';        //8
     $cc_label_paypal[9][1] = 'Expiration//CVC';           //9
     $cc_label_paypal[10][1] = 'CVC';                 //10
     miglainit_option('migla_paypalpro_cc_info', $cc_label_paypal );
	 
     $order_of_gateways[0] = array( 'paypal' , false  );
     $order_of_gateways[1] = array( 'stripe' , false );
     $order_of_gateways[2] = array( 'authorize' , false );
     $order_of_gateways[3] = array( 'offline' , false  );
   
     miglainit_option('migla_paypal_wait_paypal' , 'Just a moment while we redirect you to PayPal' ) ;
	 miglainit_option('migla_paypal_wait_paypalpro' , 'Just a moment while we process your donation' ) ;
	 miglainit_option('migla_gateways_order' , $order_of_gateways );
	 miglainit_option('migla_paypal_method', 'standar') ;
	 miglainit_option('migla_ipn_choice', 'front') ;
	 miglainit_option('migla_paypal_verifySSL', 'no');	 
	 miglainit_option( 'migla_paypal_emails' , '');
	 miglainit_option( 'migla_payment' , 'sandbox');
	 miglainit_option('migla_paypalitem', 'donation' );
	 miglainit_option('migla_paypalcmd', 'donation' );
	 miglainit_option('migla_default_payment', 'paypal');
	 miglainit_option('migla_using_pdt', 'no') ;
	 miglainit_option('migla_pdt_token', '');
	 miglainit_option('migla_pdt_using_ca', 'no') ;

	miglainit_option('miglaPayPalButtonChoice', 'cssButton' );
	miglainit_option('migla_paypalbutton', 'English');
	miglainit_option('migla_paypalcssbtnstyle', 'Default');
	miglainit_option('migla_paypalcssbtntext', 'Donate Now');
	miglainit_option('migla_paypalcssbtnclass', '');
	miglainit_option('migla_paypalbuttonurl', '');	 

     miglainit_option('migla_warning_1', 'Please insert all the required fields' );
     miglainit_option('migla_warning_2', 'Please insert correct email' );
     miglainit_option('migla_warning_3', 'Please fill in a valid amount' );
	  
   miglainit_option('migla_offline_tab', 'Offline');	  
   miglainit_option('migla_offline_info', '');
   miglainit_option('migla_send_offmsg' , 'yes' );
   miglainit_option('migla_offmsg_thankSbj', '' );
   miglainit_option('migla_offmsg_signature', '');
   miglainit_option('miglaOfflineButtonChoice', 'cssButton' );
   miglainit_option('migla_offlinecssbtnclass', '');
   miglainit_option('migla_offlinecssbtntext', 'Donate Now');
   miglainit_option('migla_offlinecssbtnstyle', 'Default'); 
   miglainit_option('migla_offlinebuttonurl', '' );
   miglainit_option('migla_wait_offline', 'Just a moment while we process your donation') ;
   miglainit_option('migla_thankyou_offline', 'Thank you an email with instructions has been send to your email address') ;

   miglainit_option('migla_ajax_caller', 'wp' );
   miglainit_option('migla_script_load_css_pos' , 'head' );
   miglainit_option('migla_script_load_js_pos' , 'footer' );


   /** VERSION 1.7.0 **/
   /** Convert Old Campaigns ****/
   $data = get_option( 'migla_campaign' ) ;
   if( $data == false || $data == '' ){

   }else{
        $idk = 0;
        $new_data    = array();
        $old_version = false;
              
        foreach( (array)$data as $d )
        {  
            $n = $d['name'];
            $t = $d['target'];
            $s = $d['show'];
            $post_id = '';

            //Check if the structure still oldl
            if( !isset($d['form_id']) )
            {
               $old_version = true;
				$my_post = array(
	            'post_title'    => $n,
	            'post_content'  => '',
	            'post_status'   => 'publish',
	            'post_author'   => 1,
	            'post_type'     => 'miglaform'
	        );

	        // Insert the post into the database
	        $post_id = wp_insert_post( $my_post );   
	        add_post_meta( $post_id, 'migla_form_fields', $fields );   
  
                $new_data[$idk] = array(
	                     'name' 	=> $n,
			     'target' 	=> $t,
			     'show'	=> $s,
			     'form_id'	=> $post_id
	                   );					   
             }
             $idk++;
        }//Foreach

        if( $old_version )
        {
               update_option( 'migla_campaign' , $new_data ) ;
        }
   }

   miglainit_option( 'migla_paypal_pro_type' , 'website_pro' );
   miglainit_option( 'migla_paypalpro_recurring' , 'sec' );
   miglainit_option( 'migla_express_checkout_listener', '' );

   miglainit_option('migla_bglevelcoloractive', 'eeeeee');
   
   $circles = array();
   $circles[0]['size'] 	= 200;
   $circles[0]['thickness'] 	= 10;
   $circles[0]['start_angle'] 	= 1;
   $circles[0]['reverse'] 	= 'no';
   $circles[0]['line_cap'] 	= 'round';
   $circles[0]['fill'] 	= '#428bca';
   $circles[0]['animation'] 	= 'none';
   $circles[0]['inside'] 	= 'none';
   $circles[0]['inner_font_size'] 	= 22;
   
   miglainit_option( 'migla_circle_settings', $circles );
   miglainit_option( 'migla_circle_textalign', 'mg_left-right');
   miglainit_option( 'migla_circle_text1', 'Amount');
   miglainit_option( 'migla_circle_text2', 'Target');
   miglainit_option( 'migla_circle_text3', 'Backers');
   
   miglainit_option( 'migla_credit_card_avs', 'no');
   miglainit_option( 'migla_credit_card_validator', 'no');
   
   miglainit_option( 'migla_ipn_chatback', 'yes');
   
}


/** 1.CALL HOOK FILES that require on plugin main page ********************************************************/ 
 function migla_call_hooks(){
   global $wpdb;
   $require_files = array();
   $require_files = $wpdb->get_results( 
	 $wpdb->prepare( 
	  "SELECT * FROM {$wpdb->prefix}options WHERE option_name like %s" ,
	  'miglarequire%'
      ) 
    ); 
	
	if( count($require_files) > 0){
      foreach( (array)$require_files as $f ){
	    $url = $f->option_value;
		if( $url == '' ){ }else{ include( dirname(__FILE__). $url);}
      }
	}
 }

 /*  This function call all hooks on front end form Shortcode */
  function migla_hook_action_1_array(){
   global $wpdb;
   $hookactions = array();
   $hookactions = $wpdb->get_results( 
	 $wpdb->prepare( 
	  "SELECT * FROM {$wpdb->prefix}options WHERE option_name like %s" ,
	  'miglaactions_1%'
      )
    ); 

	$out = array(); $i = 0;
    foreach( (array)$hookactions as $ha ){
	  if( $ha != '')
	  {
         $out[$i]['action_name'] = ($ha->option_name);
	     $varx = explode(';' , $ha->option_value);
         $out[$i]['action_function'] = $varx[0];
         $out[$i]['action_priority'] = $varx[1];
         $out[$i]['action_num_args'] = $varx[2];
		 $out[$i]['action_purpose'] = $varx[3];

	     $i++;
	   }
    }	
	
    return $out;
 }

function migla_donation_deactived_( $networkwide )
{
    global $wpdb;
                 
    if( function_exists('is_multisite') && is_multisite() )
	{    
        if ($networkwide) 
		{
            $old_blog 	= $wpdb->blogid;
            
            $blogids 	= $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ($blogids as $blog_id) 
			{
                switch_to_blog($blog_id);
                 migla_donation_deactived();
            }
            switch_to_blog($old_blog);
            return;
        }   
    }
	
	 migla_donation_deactived();
} 
 
function migla_donation_deactived()
{
   if( get_option('migla_delete_settings') == 'yes' )
   {
		migla_delete_all_settings();
	  
		//Delete the form campaign
		global $wpdb;
		$rows = array();
		$rows =  $wpdb->get_results( 
					$wpdb->prepare( 
					"SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = %s"
					, 'miglaform'
					)
				); 
		
		foreach( $rows as $row )
		{
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}postmeta WHERE post_id = %d" , $row->ID ));
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}posts WHERE ID = %d" , $row->ID ));
		}	
	  
   }
}

function migla_admin_notice() {
    $stripe_mode = get_option('migla_stripemode');
    $msg = ""; $count_gateways = 0;
    $ready = true;
    $gateways_order   = (array) get_option('migla_gateways_order');
    $show		      = array( 'paypal'    => 'no' , 
			                    'stripe'    => 'no' ,
								'authorize' => 'no' , 
								'offline'   => 'no' 
							    );		
	$message	      = array( 'paypal'    => '' , 
			                    'stripe'    => '' ,
								'authorize' => '' , 
								'offline'   => '' 
							    );	
    $msg = "";								

	foreach( (array)$gateways_order as $value  ){
		if(  $value[1] == 'true' || $value[1] == 1  ){
			$show[ $value[0] ] = 'yes';
                        $count_gateways++;
		}
	}

     $email1 = get_option( 'migla_replyTo' );
     $email2 = get_option( 'migla_replyToName' );
     $email3 = get_option( 'migla_notif_emails' );
	 
     if( $email1 == '' || $email2 == '' || $email3 == '' ){
       $ready = false;  
       $msg .= " ". __("Please fill in the notification email, your email name and your email address. Go to ","migla-donation");
       $msg .= "<a class='' href='".get_admin_url()."admin.php?page=migla_donation_settings_page'>". __(" General Settings","migla-donation"). "</a></p>";
    }	
	
  if( $show['paypal'] == 'yes' )
  {
     $payment_method = get_option('migla_payment');
     $business_email = get_option('migla_paypal_emails');

     if( $business_email == '' ){
       $ready = false;  
       $message['paypal'] .= "<p> ". __("Please fill in your PayPal account details in Total Donations. It is required to begin accepting donations. Go to ","migla-donation");
       $message['paypal'] .= "<a class='' href='".get_admin_url()."admin.php?page=migla_donation_paypal_settings_page'>". __(" Paypal Settings","migla-donation"). "</a></p>";
     }

     if( $payment_method == 'sandbox' ){
       $ready = false;  
       $message['paypal'] .= "<p> ". __("Total Donations is currently in PayPal's sandbox mode. To switch to production mode, please go to ","migla-donation");
       $message['paypal'] .= "<a class='' href='".get_admin_url()."admin.php?page=migla_donation_paypal_settings_page'>". __(" PayPal Settings","migla-donation"). "</a>". __(" and change the payment method to PayPal","migla-donation"). "</p>";
     }
  }

   if( $show['stripe'] == 'yes' )
   {
  	 $stripe_liveSK = get_option( 'migla_liveSK' );
     $stripe_livePK = get_option( 'migla_livePK' );        

      if( $stripe_mode == 'test' )
	  {
		   $message['stripe'] .= "<p> ". __("Total Donations is currently in Stripe's test mode. To switch to Live mode, please go to ","migla-donation");
		   $message['stripe'] .= "<a class='' href='".get_admin_url()."admin.php?page=migla_stripe_setting_page'>". __(" Stripe Settings","migla-donation"). "</a>". __(" and change the payment method to Live","migla-donation"). "</p>";		 
      }	  
      if( (empty($stripe_liveSK) || $stripe_liveSK==false) || (empty($stripe_livePK) || $stripe_livePK==false) ){ 
            $message['stripe'] .= " ". __("Please fill in your Stripe Keys in Total Donations if you wish to use Stripe. Go to ","migla-donation");
            $message['stripe'] .= "<a class='' href='".get_admin_url()."admin.php?page=migla_stripe_setting_page'>". __(" Stripe Settings","migla-donation"). "</a></p>";        
      }	  
   }
   
   if( $show['authorize'] == 'yes' )
   {  
      $api_key = get_option('migla_authorize_api_key');
      $trans_key = get_option('migla_authorize_trans_key');

      if( $api_key == '' || $trans_key == '' || $api_key == false || $trans_key == false )
      {
          $message['authorize'] .=  "<p>".__("Please fill in the API key and Transaction Key for Authorize.Net . Go to ","migla-donation");
 	      $message['authorize'] .= "<a class='' href='".get_admin_url()."admin.php?page=migla_donation_authorize_settings_page'>". __(" Authorize.Net Settings","migla-donation") . "</a></p>";
      }
	  
	  if( get_option('migla_payment_authorize') == 'sandbox' ){
         $message['authorize'] .=  "<p>".__("Total Donations is currently in Authorize.net's test mode. To switch to the Live mode, please go to ","migla-donation");	  
  	     $message['authorize'] .= "<a class='' href='".get_admin_url()."admin.php?page=migla_donation_authorize_settings_page'>". __(" Authorize.Net's Settings","migla-donation") . "</a></p>";
	  }  

   }
   
   if( $ready ){

     //echo "<div class='updated'><p><strong>". __("Welcome to Total Donations ","migla-donation")."</strong></p></div>";

  }else{
  
     if( $count_gateways < 1 ){
         echo "<div class='updated'><p><strong>". __("Welcome to Total Donations ","migla-donation")."</strong></p><p>";
         echo " ". __("Please choose at least one payment method. Either ","migla-donation");
         echo "<a class='' href='".get_admin_url()."admin.php?page=migla_stripe_setting_page'>". __(" Stripe","migla-donation"). "</a>";
         echo " , "; 
         echo "<a class='' href='".get_admin_url()."admin.php?page=migla_donation_paypal_settings_page'>". __(" Paypal","migla-donation"). "</a>";   
         echo " or ";
         echo  "<a class='' href='".get_admin_url()."admin.php?page=migla_donation_authorize_settings_page'>". __(" Authorize.Net Settings","migla-donation"). "</a></p>";
         echo "</div>";			 
     }
	 if( $message['paypal'] != '' )
	 {
	     echo "<div class='updated'>";
		 echo "<p><strong>Paypal</strong></p>";
         echo $message['paypal'] ;
         echo "</div>";			 
     }
	 
	 if( $message['stripe'] != '' )
	 {
	     echo "<div class='updated'>";
         echo "<p><strong>Stripe</strong></p>";		 
  	     echo $message['stripe'] ;
         echo "</div>";			 
     }

	 if( $message['authorize'] != '' )
	 {
	     echo "<div class='updated'>";
         echo "<p><strong>Authorize.Net</strong></p>";	 
  	     echo $message['authorize'] ;
         echo "</div>";			 
     }

 
  }

}

/*************LOAD SCRIPTS AND STYLE*******************************************************************/
function migla_load_admin_scripts($hook) 
{ 
        $screen = get_current_screen();
        //echo "<script>alert('".$screen->id."');</script>";

        if( $screen->id == 'widgets'  ){
		
			$ajax_url = plugins_url( 'totaldonations/the-ajax-caller.php' , dirname(__FILE__));
			if( get_option('migla_ajax_caller') == 'wp' )
			{
				$ajax_url =  admin_url( 'admin-ajax.php' );
			}		
		
            wp_enqueue_script( 'jminicolor.js', plugins_url( 'totaldonations/js/jquery.minicolors.js' , dirname(__FILE__)) );
			wp_enqueue_style( 'jminicolor_css', plugins_url( 'totaldonations/css/jquery.minicolors.css' , dirname(__FILE__)) );
            wp_enqueue_script( 'migla_color_box.js', plugins_url( 'totaldonations/js/migla_color_widget.js' , dirname(__FILE__)) );
			 wp_enqueue_script( 'migla_admin_widgets.js', plugins_url( 'totaldonations/js/migla_widgets.js' , dirname(__FILE__)) );
			 
			wp_localize_script( 'migla_admin_widgets.js', 'miglaAdminAjax', array( 'ajaxurl' =>  admin_url( 'admin-ajax.php' ) )); 
        }

	//Added modified code for prevention people change Total Donations to other language in PO file
	//Added Campaign Creator
	
	$migla_is_in_the_hook = ( $hook == ("toplevel_page_migla_donation_menu_page") || ( strpos( $hook, 'migla'  ) !== false )  );

	if( $migla_is_in_the_hook ) 
	{

        add_action( 'admin_notices', 'migla_admin_notice' );

        $ajax_url = plugins_url( 'totaldonations/the-ajax-caller.php' , dirname(__FILE__));
        if( get_option('migla_ajax_caller') == 'wp' )
        {
            $ajax_url =  admin_url( 'admin-ajax.php' );
        }

        migla_enqueue_script('jquery');
        migla_enqueue_script('jquery-ui-core');

  	  wp_enqueue_script( 'miglageneric-js', plugins_url( 'totaldonations/js/migla_generic.js' , dirname(__FILE__)) );

          wp_enqueue_script( 'respond.min.js', plugins_url( 'totaldonations/js/respond.min.js' , dirname(__FILE__)) );
          	  
 	  wp_enqueue_script( 'miglabootstrap.min.js', plugins_url( 'totaldonations/js/bootstrap.min.js' , dirname(__FILE__)) );
	   

	if( $hook == ("toplevel_page_migla_donation_menu_page") ) 
        {
           wp_enqueue_script( 'migla-jschart-js', plugins_url( 'totaldonations/js/Chart.js' , dirname(__FILE__)) );
           wp_enqueue_script( 'migla-main-js', plugins_url( 'totaldonations/js/migla_main.js' , dirname(__FILE__)));
      
           wp_localize_script( 'migla-main-js', 'miglaAdminAjax',
		array( 'ajaxurl' => $ajax_url,
                       'nonce' => wp_create_nonce( 'migla-donate-nonce' )
 	   ));
        }

	if( strpos(  $hook , 'migla_donation_campaigns_page') !== false ) 
        {
           wp_enqueue_script( 'migla-campaign-js', plugins_url( 'totaldonations/js/migla_campaign.js' , dirname(__FILE__)),
              array('jquery-ui-core','jquery-ui-sortable','jquery-ui-draggable','jquery-ui-droppable', 'jquery','media-upload','thickbox') );

          wp_localize_script( 'migla-campaign-js', 'miglaAdminAjax',
		array( 'ajaxurl' => $ajax_url,
                        'nonce' => wp_create_nonce( 'migla-donate-nonce' )
 	   ));
        }

	if( strpos(  $hook , 'migla_donation_help' ) !== false ) 
        {
           wp_enqueue_script( 'migla-help-js', plugins_url( 'totaldonations/js/migla_help.js' , dirname(__FILE__)) );

              wp_localize_script( 'migla-help-js', 'miglaAdminAjax',
					array( 'ajaxurl' =>  admin_url( 'admin-ajax.php' ) , 
                          'nonce' => wp_create_nonce( 'migla-donate-nonce' )
 	      ));
        }
	  
	if( strpos(  $hook , 'migla_donation_form_options_page' ) !== false ) 
        {
           wp_enqueue_script('media-upload');
           wp_enqueue_script('thickbox');
           wp_enqueue_style('thickbox');

           wp_enqueue_script( 'migla-form-settings-js',  plugins_url( 'totaldonations/js/migla_form_settings.js' , dirname(__FILE__)) , 
		  array('jquery-ui-core','jquery-ui-sortable','jquery-ui-draggable','jquery-ui-droppable', 'jquery','media-upload','thickbox') );
	   
           wp_localize_script( 'migla-form-settings-js', 'miglaAdminAjax',
		array( 'ajaxurl' =>  $ajax_url,
                'nonce' => wp_create_nonce( 'migla-donate-nonce' )
 	   ));

        }

	if( strpos(  $hook , 'migla_donation_settings_page' ) !== false  ) 
        {

           wp_enqueue_script( 'migla-settings-js', plugins_url( 'totaldonations/js/migla_settings.js' , dirname(__FILE__)) );
	   wp_localize_script( 'migla-settings-js', 'miglaAdminAjax',
		array( 'ajaxurl' =>  $ajax_url,
                          'nonce' => wp_create_nonce( 'migla-donate-nonce' )
 	   ));
        }

	if( strpos(  $hook , 'migla_offline_donations_page' ) !== false  ) 
        {
  	  
         wp_enqueue_script( 'migla-offline-js', plugins_url( 'totaldonations/js/migla_offline.js' , dirname(__FILE__)) ,
             array('jquery-ui-core', 'jquery-ui-datepicker') );

           wp_enqueue_script( 'migla-offlineTables-js', plugins_url( 'totaldonations/js/jquery.dataTables.min.js' , dirname(__FILE__)) );
	
   wp_localize_script( 'migla-offline-js', 'miglaAdminAjax',
		array( 'ajaxurl' => $ajax_url,
                        'nonce' => wp_create_nonce( 'migla-donate-nonce' )
 	   ));

	  wp_enqueue_style( 'migla-dataTables-css' ,  plugins_url( 'totaldonations/css/jquery.dataTables.min.css' , dirname(__FILE__)) );
	  wp_enqueue_style( 'migla-dataTables2-css' , plugins_url( 'totaldonations/css/extra.css' , dirname(__FILE__))); 	
	  
        }        

	if( strpos(  $hook , 'migla_reports_page' ) !== false) 
        {
           wp_enqueue_script( 'migla-reports-js', plugins_url( 'totaldonations/js/migla_reports.js' , dirname(__FILE__)) 
                 , array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ) );

           wp_enqueue_script( 'migla-dataTables-js', plugins_url( 'totaldonations/js/jquery.dataTables.min.js' , dirname(__FILE__)) );

           wp_localize_script( 'migla-reports-js', 'miglaAdminAjax',
		array( 'ajaxurl' => $ajax_url,
                        'nonce' => wp_create_nonce( 'migla-donate-nonce' )
 	   ));
 	   
	   wp_enqueue_style( 'migla-dataTables-css',  plugins_url( 'totaldonations/css/jquery.dataTables.min.css' , dirname(__FILE__)) ); 
	   wp_enqueue_style( 'migla-dataTables2-css', plugins_url( 'totaldonations/css/extra.css' , dirname(__FILE__)) ); 	
        }


	if( strpos( $hook , 'migla_donation_custom_theme') !== false ) 
        {

          wp_enqueue_script( 'migla-circle-progress-js', plugins_url( 'totaldonations/js/circle-progress.js' , dirname(__FILE__)) );

          wp_enqueue_script( 'jminicolor.js', plugins_url( 'totaldonations/js/jquery.minicolors.js' , dirname(__FILE__)) );
	  wp_enqueue_style( 'jminicolor_css', plugins_url( 'totaldonations/css/jquery.minicolors.css' , dirname(__FILE__)) );

      wp_enqueue_script( 'migla-color-themes-js', plugins_url( 'totaldonations/js/migla_color_themes.js' , dirname(__FILE__)),
		array('jquery-ui-core','jquery-ui-sortable','jquery-ui-draggable','jquery-ui-droppable', 'jquery','media-upload','thickbox') );  

	   wp_localize_script( 'migla-color-themes-js', 'miglaAdminAjax',
		array( 'ajaxurl' => $ajax_url,
                       'nonce' => wp_create_nonce( 'migla-donate-nonce' )
 	   ));
        }
        
	if( strpos( $hook , 'migla_donation_paypal_settings_page' ) !== false ) 
        {
           wp_enqueue_script('media-upload');  wp_enqueue_script('thickbox'); wp_enqueue_style('thickbox');

           wp_enqueue_script( 'migla-settings-js', plugins_url( 'totaldonations/js/migla_paypal_settings.js' , dirname(__FILE__)),
                 array('jquery-ui-core','jquery-ui-sortable','jquery-ui-draggable','jquery-ui-droppable', 'jquery','media-upload','thickbox') );

	   wp_localize_script( 'migla-settings-js', 'miglaAdminAjax',
		array( 'ajaxurl' =>  $ajax_url,
                         'nonce' => wp_create_nonce( 'migla-donate-nonce' )
 	   ));
        }

	if( strpos( $hook , 'migla_offline_settings_page') !== false  ) 
        {
           wp_enqueue_script('media-upload');  wp_enqueue_script('thickbox'); wp_enqueue_style('thickbox');

           wp_enqueue_script( 'migla-settings-js', plugins_url( 'totaldonations/js/migla_offline_settings.js' , dirname(__FILE__)),
                 array('jquery-ui-core','jquery-ui-sortable','jquery-ui-draggable','jquery-ui-droppable', 'jquery','media-upload','thickbox')  );

	   wp_localize_script( 'migla-settings-js', 'miglaAdminAjax',
		array( 'ajaxurl' =>  $ajax_url,
                         'nonce' => wp_create_nonce( 'migla-donate-nonce' )
 	   ));
        }

	if( strpos( $hook , 'migla_stripe_setting_page' ) !== false ) 
        {

           wp_enqueue_script('media-upload');  wp_enqueue_script('thickbox'); wp_enqueue_style('thickbox');

          wp_enqueue_script( 'migla-stripe-settings-js', plugins_url( 'totaldonations/js/migla_stripe_settings.js' , dirname(__FILE__)) , 
                   array('jquery-ui-core','jquery-ui-sortable','jquery-ui-draggable','jquery-ui-droppable', 'jquery','media-upload','thickbox')  );

	   wp_localize_script( 'migla-stripe-settings-js', 'miglaAdminAjax',
		array( 'ajaxurl' => $ajax_url,
                       'nonce' => wp_create_nonce( 'migla-donate-nonce' )
 	   ));

             wp_enqueue_script( 'migla-dataTables-js', plugins_url( 'totaldonations/js/jquery.dataTables.min.js' , dirname(__FILE__)) );

	     wp_enqueue_style( 'migla-dataTables-css', plugins_url( 'totaldonations/css/jquery.dataTables.min.css' , dirname(__FILE__))  ); 
	     wp_enqueue_style( 'migla-dataTables2-css', plugins_url( 'totaldonations/css/extra.css' , dirname(__FILE__))  ); 	

         }

        
	if( strpos( $hook , 'migla_donation_authorize_settings_page') !== false ) 
        {
           wp_enqueue_script('media-upload');  wp_enqueue_script('thickbox'); wp_enqueue_style('thickbox');

           wp_enqueue_script( 'migla-settings-js', plugins_url( 'totaldonations/js/migla_authorize_settings.js' , dirname(__FILE__)),
                array('jquery-ui-core','jquery-ui-sortable','jquery-ui-draggable','jquery-ui-droppable', 'jquery','media-upload','thickbox')  );

	   wp_localize_script( 'migla-settings-js', 'miglaAdminAjax',
		array( 'ajaxurl' =>  $ajax_url,
                         'nonce' => wp_create_nonce( 'migla-donate-nonce' )
 	   ));
        }
		
	if( strpos( $hook , 'migla_donation_campaign_creator_page') !== false ) 
    {

          //wp_enqueue_script( 'migla-circle-progress-js', plugins_url( 'totaldonations/js/circle-progress.js' , dirname(__FILE__)) );

          wp_enqueue_script( 'jminicolor.js', plugins_url( 'totaldonations/js/jquery.minicolors.js' , dirname(__FILE__)) );
		  wp_enqueue_style( 'jminicolor_css', plugins_url( 'totaldonations/css/jquery.minicolors.css' , dirname(__FILE__)) );

		  wp_enqueue_script( 'migla-campaign-creator-js', plugins_url( 'totaldonations/js/migla_campaign_creator.js' , dirname(__FILE__)),
			array('jquery-ui-core','jquery-ui-sortable','jquery-ui-draggable','jquery-ui-droppable', 'jquery','media-upload','thickbox') );  

		   wp_localize_script( 'migla-campaign-creator-js', 'miglaAdminAjax',
					array( 'ajaxurl' => $ajax_url,
						   'nonce' 	=> wp_create_nonce( 'migla-donate-nonce' )
		   ));
     }		

	  wp_enqueue_style( 'miglabootstrap-css', plugins_url( 'totaldonations/css/bootstrap.min.css' , dirname(__FILE__)) );
	  wp_enqueue_style( 'migla_admin_css', plugins_url( 'totaldonations/css/admin_migla.css' , dirname(__FILE__)) );
	  wp_enqueue_style( 'miglafont-awesome-css', plugins_url( 'totaldonations/css/font-awesome/css/font-awesome.min.css' , dirname(__FILE__)) );

	  }else{

	  return;
	}
	
}

function migla_enqueue_style( $style ){
  if( wp_script_is( $style, 'queue' ) ){}else{
    wp_enqueue_style( $style );
  }
}

function migla_enqueue_script( $script ){
  if( wp_script_is( $script, 'queue' ) ){}else{
    wp_enqueue_style( $script );
  }
}

function migla_donate_plugins_loaded() {
    load_plugin_textdomain( 'migla-donation', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

function mg_add_cors_http_header(){
    header("Access-Control-Allow-Origin: *");
}

function register_session(){
    if( !session_id() )
        session_start();
}

function migla_get_current_url()
{
   if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'){
	$http = 'https';
    }else{
	$http = 'http';
    }

    $currentUrl = $http . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    return $currentUrl;
} 

/***********************************************************************************/
/*             CLASSES FOR FORM SHORTCODES */
/***********************************************************************************/

class Migla_Shortcode {
     static $add_script; static $nonce; static $pk; static $ajax_url; static $notifyurl;


     static function pdt_post( $tx ){
        // Init cURL
        $request = curl_init();

 	$payPalServer = get_option('migla_payment');
	   if ($payPalServer == "sandbox")
	   {
 		$formAction = "https://www.sandbox.paypal.com/cgi-bin/webscr";
	   }else{
		 $formAction = "https://www.paypal.com/cgi-bin/webscr";
	   }

        // Set request options
        if( get_option('migla_pdt_using_ca') != 'yes' ){
           curl_setopt_array($request, array
          (
             CURLOPT_URL => $formAction,
             CURLOPT_POST => TRUE,
             CURLOPT_POSTFIELDS => http_build_query(array
             (
               'cmd' => '_notify-synch',
               'tx' => $tx,
               'at' => get_option('migla_pdt_token'),
              )),
              CURLOPT_RETURNTRANSFER => TRUE,
              CURLOPT_HEADER         => FALSE,
              CURLOPT_SSLVERSION     => 1
           ));
        }else{
           curl_setopt_array($request, array
          (
             CURLOPT_URL => $formAction,
             CURLOPT_POST => TRUE,
             CURLOPT_POSTFIELDS => http_build_query(array
             (
               'cmd' => '_notify-synch',
               'tx' => $tx,
               'at' => get_option('migla_pdt_token'),
              )),
              CURLOPT_RETURNTRANSFER => TRUE,
              CURLOPT_HEADER         => FALSE,
              CURLOPT_SSL_VERIFYPEER => TRUE,
              CURLOPT_CAINFO      => dirname(__FILE__) . '/ca/cacert_migla.pem',
              CURLOPT_SSLVERSION     => 1
           ));

        }

         // Execute request and get response and status code
         $response = curl_exec($request);
         $status   = curl_getinfo($request, CURLINFO_HTTP_CODE);

         // Close connection
         curl_close($request);

         $page = "";

         if($status == 200 && strpos($response, 'SUCCESS') === 0)
         {
            $response = substr($response, 7);
            $response = urldecode($response);
            preg_match_all('/^([^=\s]++)=(.*+)/m', $response, $m, PREG_PATTERN_ORDER);
            $response = array_combine($m[1], $m[2]);       
   
            // Fix character encoding if different from UTF-8 (in my case)
            if(isset($response['charset']) && strtoupper($response['charset']) !== 'UTF-8')
            {
                 foreach($response as $key => &$value)
                 {
                       $value = mb_convert_encoding($value, 'UTF-8', $response['charset']);
                 }
                 $response['charset_original'] = $response['charset'];
                 $response['charset'] = 'UTF-8';
            }

            if( $response['payment_status'] == 'Completed' )
            {
              $custom_id = str_replace("'", "", $response['custom'] ); 
              $custom_id = str_replace( '\"', '' , $custom_id );

              //Get Transient data
              $transientKey  = "t_".  $custom_id ;
              $postData      = get_option( $transientKey );

            /********Thank You page ***********************/
            	   
			 $str = get_option( 'migla_thankyoupage' );
			 $trimquote = str_replace( '\"', '' , $str );
                         $trimquote = str_replace( '\\', '' , $trimquote );
			 
			   if( $postData  == false ){
			   
				   $field = explode(" ", esc_attr( $response['payment_date'] ) );
				   $day = str_replace( ",", "", $field[2] );
				   $month = "";
				   if( $field[1]=="Jan" ){ $month="01"; }
				   else if( $field[1]=="Feb" ){ $month="02"; }
				   else if( $field[1]=="March" ){ $month="03"; }
				   else if( $field[1]=="April" ){ $month="04"; }
				   else if( $field[1]=="May" ){ $month="05"; }
				   else if( $field[1]=="June" ){ $month="06"; }
				   else if( $field[1]=="July" ){ $month="07"; }
				   else if( $field[1]=="Aug" ){ $month="08"; }
				   else if( $field[1]=="Sept" ){ $month="09"; }
				   else if( $field[1]=="Oct" ){ $month="10"; }
				   else if( $field[1]=="Nov" ){ $month="11"; }
				   else if( $field[1]=="Dec" ){ $month="12"; }
				   $temp = $month."/".$day."/".$field[3];

				   $amount = esc_attr( $response['payment_gross'] );
				   if( $amount == '' ){ 
					 $amount = esc_attr( $response['mc_gross'] ); 
				   }

                                   $default 		= get_option('migla_default_timezone');
	                           $language 		= get_option('migla_default_datelanguage');
	                           $date_format 	= get_option('migla_default_dateformat');

                                   $the_date = migla_get_date( $default, $language , $date_format, false, $temp );

				   $placeholder = array( '[firstname]','[lastname]' ,'[amount]' ,'[date]' , '[newline]' );
				   
				   $replace = array( esc_attr( $response['first_name'] ), esc_attr( $response['last_name'] ), $amount, $the_date, '<br>' );

				   $page .=  str_replace($placeholder, $replace, $trimquote);
				   
				}else{
				
				   $placeholder = array( '[firstname]','[lastname]' ,'[amount]' ,'[date]' , '[newline]' );
				   
				   $replace = array( $postData['miglad_firstname'], $postData['miglad_lastname'] , $postData['miglad_amount'] , 
								  date("F jS, Y", strtotime($postData['miglad_date']) ) , '<br>'  );

				   $page .=  str_replace($placeholder, $replace, $trimquote);
				}	

            /***********************************************/

	    $post_id = migla_create_post();
				
	     ///GET CURRENT TIME SETTINGS----------------------------------
		$php_time_zone = date_default_timezone_get();
		$t = ""; $d = ""; $default = "";
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
	 			 

                $isIDExist =  migla_cek_repeating_id( $custom_id );
					
		if( $postData == false && $isIDExist == -1 ){
				     //It lost its transient data
					      
                           // migla_donation_from_paypal($post_id, $post);
                     	   add_post_meta( $post_id, 'miglad_session_id', $response['custom'] );
                           add_post_meta( $post_id, 'miglad_firstname', $response['first_name'] );
                           add_post_meta( $post_id, 'miglad_lastname', $response['last_name'] );

                           $amountfrompaypal = $response['payment_gross'] ;
                           if( $amountfrompaypal == '' ){ 
                              $amountfrompaypal = $response['mc_gross']; 
                           }
                           add_post_meta( $post_id, 'miglad_amount', $amountfrompaypal );

                           add_post_meta( $post_id, 'miglad_phone', $response['contact_phone'] );
                           add_post_meta( $post_id, 'miglad_country', $response['address_country'] );
                           add_post_meta( $post_id, 'miglad_address', $response['address_street'] );
                           add_post_meta( $post_id, 'miglad_email', $response['payer_email'] );
                           add_post_meta( $post_id, 'miglad_city', $response['address_city'] ); 
                           add_post_meta( $post_id, 'miglad_state', $response['address_state'] ); 

                            //Additional data
                             add_post_meta( $post_id, "miglad_time" , $t );
                             add_post_meta( $$post_id, "miglad_date" , $d );
 
						   
			   //Save data from paypal
			   add_post_meta( $post_id, 'miglad_paymentmethod', 'Paypal PDT' );
				add_post_meta( $post_id, 'miglad_paymentdata', $response );
				add_post_meta( $post_id, 'miglad_transactionId', $response['txn_id'] );
				add_post_meta( $post_id, 'miglad_timezone', $default );

							  //Check what type is it
							  if(  $response[ 'txn_type' ] == 'subscr_payment' ){
								   add_post_meta( $post_id, 'miglad_transactionType', 'Recurring (Paypal)' );
								   add_post_meta( $post_id, 'miglad_subscription_id', $response['subscr_id'] ); 
							  }else{
								   add_post_meta( $post_id, 'miglad_transactionType', 'One time (Paypal)' );
							  }						
            /*** SEND EMAIL ****/


					
		}else{
		    //it has its transient data
					
                    if( $isIDExist == -1 ){ //Check if this already saved

                         $i = 0; 
                         $keys = array_keys( $postData );
                         foreach( (array)$postData as $value)
                         {
                              add_post_meta( $post_id, $keys[$i], $value );
                            $i++;
                          }

                           update_post_meta( $post_id, 'miglad_time', $t ); 
                           update_post_meta( $post_id, 'miglad_date', $d ); 

                              
                           //Save data from paypal
                           add_post_meta( $post_id, 'miglad_paymentmethod', 'Paypal PDT' );
                           add_post_meta( $post_id, 'miglad_paymentdata', $response );
                           add_post_meta( $post_id, 'miglad_transactionId', $response['txn_id'] );
                           add_post_meta( $post_id, 'miglad_timezone', $default );

                          //Check what type is it
                          if(  $response[ 'txn_type' ] == 'subscr_payment' ){
                               add_post_meta( $post_id, 'miglad_transactionType', 'Recurring (Paypal)' );
                               add_post_meta( $post_id, 'miglad_subscription_id', $response['subscr_id'] ); 
	                  }else{
                               add_post_meta( $post_id, 'miglad_transactionType', 'One time (Paypal)' );
                          }
        

                         /***** SEND EMAIL *************/
                          $e = get_option('migla_replyTo');
                          $en = get_option('migla_replyToName');
                          $ne = get_option('migla_notif_emails');

                          if( get_option( 'miglaactions_2_1' ) == 'yes' )
                          {
                              sendThankYouEmailCustom( $postData, 2 ,  $e, $en );		         
	                      sendNotifEmailCustom( $postData, 2, $e, $en, $ne);
                          }else{
                              mg_send_thank_you_email( $postData, $e, $en );
                              mg_send_notification_emails( $postData, $e, $en, $ne);

                              $tdata   =  $transientKey. "hletter";
                              $content =  get_option( $tdata );
                              mg_send_hletter( $postData, $e, $en, $content, $d );
                          }
	
                    }else{
                         //Do Nothing, it is already saved on database        
                    }			
                    		
	        } //HAS TRANSIENT
            
            }//Payment Status completed
            else{
              //If it is Failed
              $page .= "Donation status : " . $response['payment_status'];                
            }
              
         }else {
             //If it is Failed
             $page .= "Payment Failed";    
         }

         return $page;
     }

	static function init()
	{  
	     add_shortcode('totaldonations', array(__CLASS__, 'handle_shortcode'));

          if( get_option('migla_allow_cors') == 'yes' ){
             add_action('init','mg_add_cors_http_header');
          }
		 
         if( get_option('migla_script_load_css_pos') == 'head' )
		 {
		      self::register_stylesheet();
			  add_action( 'wp_enqueue_scripts' , array(__CLASS__, 'enqueue_stylesheet') , 11 );
         }else{
		      self::register_stylesheet();
			  add_action('wp_footer', array(__CLASS__, 'enqueue_stylesheet'));
         }		 

		 /*Captcha*/
		 if( get_option('migla_use_captcha') == 'yes' )
		 {
			 add_action( 'wp_enqueue_scripts' , array(__CLASS__, 'enqueue_captcha_script') );
			 add_action( 'wp_head', 'migla_captcha_scripts');
		 }
		 
         if( get_option('migla_script_load_js_pos') == 'head' )
         {
			add_action( 'wp_enqueue_scripts' , array(__CLASS__, 'enqueue_jsscript_head') );
         }else{
  	        add_action('wp_footer', array(__CLASS__, 'enqueue_jsscript_footer') );
         }
	}

	static function handle_shortcode($atts) {
	   
       self::$add_script = true;     
	   $content = "";
 	   
	   $isThank = false; $get_id = "";
	  	
    if( isset($_GET['tx']) && get_option('migla_using_pdt') == 'yes' )
    {
        $tx = str_replace( '\"', '' , $_GET['tx'] ); $tx = str_replace( "'", "" , $tx );
        $content .= self::pdt_post( $tx );             

	}else{
  	  
		  if ( isset( $_GET['thanks'] ) && $_GET['thanks'] == 'thanks' && isset( $_GET['id'] ) ) {
			$isThank = true;
					$get_id = esc_attr( $_GET['id'] ); str_replace( '\"', '' , $get_id  );
					str_replace( "'", "" , $get_id  );
		  } else if( isset( $_POST['thanks'] ) && $_POST['thanks'] == 'thanks' && isset( $_POST['id'] ) ) { 
			$isThank = true;
					$get_id = esc_attr( $_POST['id'] ); str_replace( '\"', '' , $get_id  );
					str_replace( "'", "" , $get_id  );
			  } else if( isset( $_POST['thanks'] ) && $_POST['thanks'] == 'testThanks'){
					$isThank = true;
		  }else if( isset( $_GET['auth'] ) || isset( $_POST['auth'] ) ){
					$isThank = true;
			  }
	  
		if( isset( $_POST['thanks'] ) && $_POST['thanks'] == 'widget_bar' ){ $isThank = false; }
	
		if ( $isThank ) 
		{
	   
			 $str = get_option( 'migla_thankyoupage' );
			 $trimquote = str_replace( '\"', '' ,$str );
			
			if( isset($_POST['thanks']) && $_POST['thanks'] == 'testThanks' )  //Testing thank You Page
			{
				$default 		= get_option('migla_default_timezone');
				$language 		= get_option('migla_default_datelanguage');
				$date_format 	= get_option('migla_default_dateformat');
			
		        $the_date = migla_get_date( $default, $language , $date_format, false, '');
			    $placeholder = array( '[firstname]','[lastname]' ,'[amount]' ,'[date]', '[newline]' );
			    $replace = array( 'John','Doe' ,'100' ,$the_date , '<br>' );
			    $page =  str_replace($placeholder, $replace, $trimquote);
			   
			}else{
			
			   $page = ""; 
			   $transientKey = "t_". $get_id;
			   $postData =  get_option( $transientKey );
			   
				$default 		= get_option('migla_default_timezone');
				$language 		= get_option('migla_default_datelanguage');
				$date_format 	= get_option('migla_default_dateformat');

				$the_date = migla_get_date( $default, $language , $date_format, false, $postData['miglad_date'] );			   
			   
			   if(  $postData == false ){
			   
				   $field = explode(" ", esc_attr( $_GET['payment_date'] ) );
				   $day = str_replace( ",", "", $field[2] );
				   $month = "";
				   if( $field[1]=="Jan" ){ $month="01"; }
				   else if( $field[1]=="Feb" ){ $month="02"; }
				   else if( $field[1]=="March" ){ $month="03"; }
				   else if( $field[1]=="April" ){ $month="04"; }
				   else if( $field[1]=="May" ){ $month="05"; }
				   else if( $field[1]=="June" ){ $month="06"; }
				   else if( $field[1]=="July" ){ $month="07"; }
				   else if( $field[1]=="Aug" ){ $month="08"; }
				   else if( $field[1]=="Sept" ){ $month="09"; }
				   else if( $field[1]=="Oct" ){ $month="10"; }
				   else if( $field[1]=="Nov" ){ $month="11"; }
				   else if( $field[1]=="Dec" ){ $month="12"; }
				   $temp = $month."/".$day."/".$field[3];

				   $amount = esc_attr( $_GET['payment_gross'] );
				   if( $amount == '' ){ 
					 $amount = esc_attr( $_GET['mc_gross'] ); 
				   }

				   $placeholder = array( '[firstname]','[lastname]' ,'[amount]' ,'[date]' , '[newline]' );
				   
				   $replace = array( esc_attr( $_GET['first_name'] ), esc_attr( $_GET['last_name'] ) , $amount , $the_date, '<br>'  );

				   $page =  str_replace($placeholder, $replace, $trimquote);
				   
				}else{
				
				   $placeholder = array( '[firstname]','[lastname]' ,'[amount]' ,'[date]' , '[newline]' );
				   
				   $replace = array( $postData['miglad_firstname'], $postData['miglad_lastname'] , $postData['miglad_amount'] , 
									 $the_date , '<br>'  );

				   $page =  str_replace($placeholder, $replace, $trimquote);

				}	

			}

			$content .= $page ;                      

	   }else{  /******** This is not thank you page then call migla_draw_form function *************/

                //get the attribute
               extract(shortcode_atts(array(
						'campaign'	=> '',
						'form_id'	=> ''
					), $atts ) 
				);

		if(!isset($atts['form_id'])){
			$atts['form_id'] = '';
		}	 		
	
		if(!isset($atts['campaign'])){
			$atts['campaign'] = '';
		}	 		
			   
			/***** DRAWING FORM **********/
			$obj_form = new migla_form_creator( $atts['form_id'] );

			$session_id = 'migla' . date("Ymdhis"). "_" . rand() ;		

			$content .= "<div style='clear:both' class='bootstrap-wrapper'><div id='wrap-migla'>";
			$content .= "<div id='migla_donation_form' style='' >";

			$content .= "<input type='hidden' name='migla_session_id' value='".$session_id."' />";
				
			$content .= $obj_form->draw_form();

			$content .= "</div>";
			$content .= "</div>";	

			$content .= $obj_form->migla_hidden_form( $session_id );
			
			$content .= "</div>";

			$content .= "<p></p>";
		
	  }

	}//Is it PDT POST RETURN

	   return $content;
			
	}

    static function register_stylesheet(){
	}	
	
	static function enqueue_stylesheet()
	{
		if( is_rtl() )
		{
			wp_enqueue_style( 'migla-front-end', plugins_url( 'totaldonations/css/migla-frontend.css' , dirname(__FILE__))  );	
			wp_enqueue_style( 'migla-front-end-rtl', plugins_url( 'totaldonations/css/migla-rtl.css' , dirname(__FILE__))  );	
		}else{
            wp_enqueue_style( 'migla-front-end', plugins_url( 'totaldonations/css/migla-frontend.css' , dirname(__FILE__))  );	
		}
	}
	
	static function enqueue_captcha_script(){
		wp_register_script( "recaptcha", "https://www.google.com/recaptcha/api.js" );
        wp_enqueue_script( "recaptcha" );		
	
	}
	
	static function enqueue_jsscript_head() 
	{
		add_action ( 'wp_head', 'migla_variables' );
		migla_enqueue_script( 'jquery' );
		wp_enqueue_script( 'respond.min.js', plugins_url( 'totaldonations/js/respond.min.js' , dirname(__FILE__)) ,array( 'jquery' ), false, false );
		wp_enqueue_script( 'migla-checkout-head-js', plugins_url( 'totaldonations/js/migla_checkOut_head.js', dirname(__FILE__)) ,array( 'jquery' ), false, false);
	
		wp_enqueue_script( 'migla-boots-nav.js', plugins_url( 'totaldonations/js/boot-tabs.js' , dirname(__FILE__)) ,array( 'jquery' ), false, false);
		wp_enqueue_script( 'migla-boots-tooltip.js', plugins_url( 'totaldonations/js/bootstrap_tooltip.js' , dirname(__FILE__)) ,array( 'jquery' ), false, false);
             
		if( get_option('migla_stripe_js') == 'yes' )
		{
           wp_enqueue_script( 'migla-stripe.js', 'https://js.stripe.com/v2/' );
		}
			  
   }

	static function enqueue_jsscript_footer() 
	{
			if ( ! self::$add_script )
				return;
				
           self::$ajax_url = plugins_url( 'totaldonations/the-ajax-caller.php' , dirname(__FILE__));
           if( get_option('migla_ajax_caller') == 'wp' )
           {
             self::$ajax_url =  admin_url( 'admin-ajax.php' );
           }

           if( get_option('migla_ipn_choice') == 'front' )
           {
                self::$notifyurl = home_url( 'index.php' ) . "?migla_listener=IPN";
           }else{
                self::$notifyurl = migla_get_notify_url();
           }
				
		/******************************************** call all actions ************************************************/
		$array_of_action = migla_hook_action_1_array();
		if( empty($array_of_action) ){
		}else{
		  foreach( $array_of_action as $act )
		  {
			if( $act['action_purpose'] == 'add' ){
			 add_action( $act['action_name'] , $act['action_function'], $act['action_priority'], $act['action_num_args']);   
			 do_action( $act['action_name'], '', plugins_url( 'totaldonations/the-ajax-caller.php' , dirname(__FILE__)), '');
			}
		  }
		}
		/*******************************************************************************************************************/ 
				
		migla_enqueue_script( 'jquery' );
		wp_enqueue_script( 'respond.min.js', plugins_url( 'totaldonations/js/respond.min.js' , dirname(__FILE__)) );
                
        //stripe
		if( get_option('migla_stripe_js') == 'yes' )
		{
           wp_enqueue_script( 'migla-stripe.js', 'https://js.stripe.com/v2/' );
		}
        self::$pk = migla_getPK() ;

		$return_id  = get_option('migla_thank_you_page');
		if( $return_id == '' || $return_id == false )
		{
		   $return_url = migla_get_current_url();
		}else{
		   $return_url = get_permalink($return_id) ;
		}	
		
           self::$nonce = wp_create_nonce('migla_');
		   wp_enqueue_script( 'migla-checkout-js', plugins_url( 'totaldonations/js/migla_checkOut.js', dirname(__FILE__)), array( 'jquery' ), false, true );

		   wp_localize_script( 'migla-checkout-js', 'miglaAdminAjax',
		       array( 'ajaxurl' => self::$ajax_url,
                              'notifyurl'  => self::$notifyurl,
                              'successurl' => migla_get_current_url(),
                              'nonce'      => self::$nonce, 
                              'stripe_PK'  => self::$pk,
                              'return_url' => $return_url						  
		    ));	

			wp_enqueue_script( 'migla-donation-js', plugins_url( 'totaldonations/js/migla_form.js', dirname(__FILE__) ), array( 'jquery' ), false, true );

		   wp_localize_script( 'migla-donation-js', 'miglaAdminAjax',
			array( 'ajaxurl' => self::$ajax_url,
                               'notifyurl' => self::$notifyurl,
                               'successurl' => migla_get_current_url(),
                               'nonce' => self::$nonce, 
                               'stripe_PK' =>  self::$pk, 
                               'return_url' => $return_url						  
		   ));		

         wp_enqueue_script( 'migla-boots-nav.js', plugins_url( 'totaldonations/js/boot-tabs.js' , dirname(__FILE__)) );
         wp_enqueue_script( 'migla-boots-tooltip.js', plugins_url( 'totaldonations/js/bootstrap_tooltip.js' , dirname(__FILE__)) );
	}
	
} //End of Migla_Shortcode Class

Migla_Shortcode::init();


/**** Thank You Page Shortcode ****/
class Migla_Thank_You_Shortcode 
{
  static $progressbar_script;

	 static function init() {
  	     add_shortcode('totaldonations_thank_you_page', array(__CLASS__, 'handle_shortcode'));
	 }

     static function pdt_post( $tx ){
        // Init cURL
        $request = curl_init();

 	$payPalServer = get_option('migla_payment');
	   if ($payPalServer == "sandbox")
	   {
 		$formAction = "https://www.sandbox.paypal.com/cgi-bin/webscr";
	   }else{
		 $formAction = "https://www.paypal.com/cgi-bin/webscr";
	   }

        // Set request options
        if( get_option('migla_pdt_using_ca') != 'yes' ){
           curl_setopt_array($request, array
          (
             CURLOPT_URL => $formAction,
             CURLOPT_POST => TRUE,
             CURLOPT_POSTFIELDS => http_build_query(array
             (
               'cmd' => '_notify-synch',
               'tx' => $tx,
               'at' => get_option('migla_pdt_token'),
              )),
              CURLOPT_RETURNTRANSFER => TRUE,
              CURLOPT_HEADER         => FALSE,
              CURLOPT_SSLVERSION     => 1
           ));
        }else{
           curl_setopt_array($request, array
          (
             CURLOPT_URL => $formAction,
             CURLOPT_POST => TRUE,
             CURLOPT_POSTFIELDS => http_build_query(array
             (
               'cmd' => '_notify-synch',
               'tx' => $tx,
               'at' => get_option('migla_pdt_token'),
              )),
              CURLOPT_RETURNTRANSFER => TRUE,
              CURLOPT_HEADER         => FALSE,
              CURLOPT_SSL_VERIFYPEER => TRUE,
              CURLOPT_CAINFO      => dirname(__FILE__) . '/ca/cacert_migla.pem',
              CURLOPT_SSLVERSION     => 1
           ));

        }

         // Execute request and get response and status code
         $response = curl_exec($request);
         $status   = curl_getinfo($request, CURLINFO_HTTP_CODE);

         // Close connection
         curl_close($request);

         $page = "";

         if($status == 200 && strpos($response, 'SUCCESS') === 0)
         {
            $response = substr($response, 7);
            $response = urldecode($response);
            preg_match_all('/^([^=\s]++)=(.*+)/m', $response, $m, PREG_PATTERN_ORDER);
            $response = array_combine($m[1], $m[2]);       
   
            // Fix character encoding if different from UTF-8 (in my case)
            if(isset($response['charset']) && strtoupper($response['charset']) !== 'UTF-8')
            {
                 foreach($response as $key => &$value)
                 {
                       $value = mb_convert_encoding($value, 'UTF-8', $response['charset']);
                 }
                 $response['charset_original'] = $response['charset'];
                 $response['charset'] = 'UTF-8';
            }

            if( $response['payment_status'] == 'Completed' )
            {
              $custom_id = str_replace("'", "", $response['custom'] ); 
              $custom_id = str_replace( '\"', '' , $custom_id );

              //Get Transient data
              $transientKey  = "t_".  $custom_id ;
              $postData      = get_option( $transientKey );

            /********Thank You page ***********************/
            	   
			 $str = get_option( 'migla_thankyoupage' );
			 $trimquote = str_replace( '\"', '' , $str );
                         $trimquote = str_replace( '\\', '' , $trimquote );
			 
			$default 		= get_option('migla_default_timezone');
	        $language 		= get_option('migla_default_datelanguage');
	        $date_format 	= get_option('migla_default_dateformat');

            $the_date = migla_get_date( $default, $language , $date_format, false, $postData['miglad_date'] ); 
			 
			   if( $postData  == false ){
			   
				   $field = explode(" ", esc_attr( $response['payment_date'] ) );
				   $day = str_replace( ",", "", $field[2] );
				   $month = "";
				   if( $field[1]=="Jan" ){ $month="01"; }
				   else if( $field[1]=="Feb" ){ $month="02"; }
				   else if( $field[1]=="March" ){ $month="03"; }
				   else if( $field[1]=="April" ){ $month="04"; }
				   else if( $field[1]=="May" ){ $month="05"; }
				   else if( $field[1]=="June" ){ $month="06"; }
				   else if( $field[1]=="July" ){ $month="07"; }
				   else if( $field[1]=="Aug" ){ $month="08"; }
				   else if( $field[1]=="Sept" ){ $month="09"; }
				   else if( $field[1]=="Oct" ){ $month="10"; }
				   else if( $field[1]=="Nov" ){ $month="11"; }
				   else if( $field[1]=="Dec" ){ $month="12"; }
				   $temp = $month."/".$day."/".$field[3];

				   $amount = esc_attr( $response['payment_gross'] );
				   if( $amount == '' ){ 
					 $amount = esc_attr( $response['mc_gross'] ); 
				   }

				   $placeholder = array( '[firstname]','[lastname]' ,'[amount]' ,'[date]' , '[newline]' );
				   
				   $replace = array( esc_attr( $response['first_name'] ), esc_attr( $response['last_name'] ) , $amount ,$the_date , '<br>'  );

				   $page .=  str_replace($placeholder, $replace, $trimquote);
				   
				}else{
				
				   $placeholder = array( '[firstname]','[lastname]' ,'[amount]' ,'[date]' , '[newline]' );
				   
				   $replace = array( $postData['miglad_firstname'], $postData['miglad_lastname'] , $postData['miglad_amount'] , 
								  $the_date , '<br>'  );

				   $page .=  str_replace($placeholder, $replace, $trimquote);
				}	

            /***********************************************/

	    $post_id = migla_create_post();
				
	     ///GET CURRENT TIME SETTINGS----------------------------------
		$php_time_zone = date_default_timezone_get();
		$t = ""; $d = ""; $default = "";
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
	 			 

                $isIDExist =  migla_cek_repeating_id( $custom_id );
					
		if( $postData == false && $isIDExist == -1 ){
				     //It lost its transient data
					      
                           // migla_donation_from_paypal($post_id, $post);
                     	   add_post_meta( $post_id, 'miglad_session_id', $response['custom'] );
                           add_post_meta( $post_id, 'miglad_firstname', $response['first_name'] );
                           add_post_meta( $post_id, 'miglad_lastname', $response['last_name'] );

                           $amountfrompaypal = $response['payment_gross'] ;
                           if( $amountfrompaypal == '' ){ 
                              $amountfrompaypal = $response['mc_gross']; 
                           }
                           add_post_meta( $post_id, 'miglad_amount', $amountfrompaypal );

                           add_post_meta( $post_id, 'miglad_phone', $response['contact_phone'] );
                           add_post_meta( $post_id, 'miglad_country', $response['address_country'] );
                           add_post_meta( $post_id, 'miglad_address', $response['address_street'] );
                           add_post_meta( $post_id, 'miglad_email', $response['payer_email'] );
                           add_post_meta( $post_id, 'miglad_city', $response['address_city'] ); 
                           add_post_meta( $post_id, 'miglad_state', $response['address_state'] ); 

                            //Additional data
                             add_post_meta( $post_id, "miglad_time" , $t );
                             add_post_meta( $$post_id, "miglad_date" , $d );
 
						   
			   //Save data from paypal
			   add_post_meta( $post_id, 'miglad_paymentmethod', 'Paypal PDT' );
				add_post_meta( $post_id, 'miglad_paymentdata', $response );
				add_post_meta( $post_id, 'miglad_transactionId', $response['txn_id'] );
				add_post_meta( $post_id, 'miglad_timezone', $default );

							  //Check what type is it
							  if(  $response[ 'txn_type' ] == 'subscr_payment' ){
								   add_post_meta( $post_id, 'miglad_transactionType', 'Recurring (Paypal)' );
								   add_post_meta( $post_id, 'miglad_subscription_id', $response['subscr_id'] ); 
							  }else{
								   add_post_meta( $post_id, 'miglad_transactionType', 'One time (Paypal)' );
							  }						
            /*** SEND EMAIL ****/


					
		}else{
		    //it has its transient data
					
                    if( $isIDExist == -1 ){ //Check if this already saved

                         $i = 0; 
                         $keys = array_keys( $postData );
                         foreach( (array)$postData as $value)
                         {
                              add_post_meta( $post_id, $keys[$i], $value );
                            $i++;
                          }

                           update_post_meta( $post_id, 'miglad_time', $t ); 
                           update_post_meta( $post_id, 'miglad_date', $d ); 

                              
                           //Save data from paypal
                           add_post_meta( $post_id, 'miglad_paymentmethod', 'Paypal PDT' );
                           add_post_meta( $post_id, 'miglad_paymentdata', $response );
                           add_post_meta( $post_id, 'miglad_transactionId', $response['txn_id'] );
                           add_post_meta( $post_id, 'miglad_timezone', $default );

                          //Check what type is it
                          if(  $response[ 'txn_type' ] == 'subscr_payment' ){
                               add_post_meta( $post_id, 'miglad_transactionType', 'Recurring (Paypal)' );
                               add_post_meta( $post_id, 'miglad_subscription_id', $response['subscr_id'] ); 
	                  }else{
                               add_post_meta( $post_id, 'miglad_transactionType', 'One time (Paypal)' );
                          }
        

                         /***** SEND EMAIL *************/
                          $e = get_option('migla_replyTo');
                          $en = get_option('migla_replyToName');
                          $ne = get_option('migla_notif_emails');

                          if( get_option( 'miglaactions_2_1' ) == 'yes' )
                          {
                              sendThankYouEmailCustom( $postData, 2 ,  $e, $en );		         
	                      sendNotifEmailCustom( $postData, 2, $e, $en, $ne);
                          }else{
                              mg_send_thank_you_email( $postData, $e, $en );
                              mg_send_notification_emails( $postData, $e, $en, $ne);

                              $tdata   =  $transientKey. "hletter";
                              $content =  get_option( $tdata );
                              mg_send_hletter( $postData, $e, $en, $content, $d );
                          }
	
                    }else{
                         //Do Nothing, it is already saved on database        
                    }			
                    		
	        } //HAS TRANSIENT
            
            }//Payment Status completed
            else{
              //If it is Failed
              $page .= "";                
            }
              
         }else {
             //If it is Failed
             $page .= "";    
         }

         return $page;
     }
	 
	 
	static function handle_shortcode($atts)
	{   	
         $isThank = false;	$get_id = '';
		 $page = '';
		 $str = get_option( 'migla_thankyoupage' );
		 $trimquote = str_replace( '\"', '' , $str );
         $trimquote = str_replace( '\\', '' , $trimquote );
		 $firstname = ''; $lastname = ''; $date = ''; $amount  = '';	
				 
			 if( isset($_GET['tx']) && get_option('migla_using_pdt') == 'yes' )
			 {
				 $tx = str_replace( '\"', '' , $_GET['tx'] ); $tx = str_replace( "'", "" , $tx );
				 $trimquote = self::pdt_post( $tx );             

			 }else{
	  		 }				 		  
		  //$page =  $_GET['thanks'] .'-'.  $_POST['thanks'] .'-'. $_GET['id'] .'-'. $_POST['id'];
		  
			  if(  isset( $_POST['thanks'] ) && $_POST['thanks'] == 'testThanks' ){
				   $firstname = 'John';
				   $lastname  = 'Doe';
				   
                                   $default 		= get_option('migla_default_timezone');
	                           $language 		= get_option('migla_default_datelanguage');
	                           $date_format 	= get_option('migla_default_dateformat');

                                   $date = migla_get_date( $default, $language , $date_format, false, '' );

				   $amount    = '1000';
				   
			  }else{
 	
                 if( isset( $_POST['id'] ) ){
				     $get_id = $_POST['id'];
				 }				 
                 if( isset( $_GET['id'] ) ){
				     $get_id = $_GET['id'];
				 }				 
	 			
  				$session_id  = 't_' . $get_id;
				$postdata    = get_option( $session_id );
				
 				 if( $get_id != '')
				 {
					   $firstname = $postdata['miglad_firstname'];
					   $lastname  = $postdata['miglad_lastname'];
					   
                        $default 		= get_option('migla_default_timezone');
	                    $language 		= get_option('migla_default_datelanguage');
	                    $date_format 	= get_option('migla_default_dateformat');

                        $date = migla_get_date( $default, $language , $date_format, false, $postdata['miglad_date'] );					   
					   
					   $amount    = $postdata['miglad_amount'];				 
				 
				 }else{
				 
					   if( isset($_GET['firstname']) ){
					    
						$firstname = $_GET['firstname'];
					    $lastname  = $_GET['lastname'];
						
                        $default 		= get_option('migla_default_timezone');
	                    $language 		= get_option('migla_default_datelanguage');
	                    $date_format 	= get_option('migla_default_dateformat');

                        $date = migla_get_date( $default, $language , $date_format, false, $postdata['miglad_date'] );		
						
					    $amount    = $_GET['amount'];
                       }					  

					   if( isset($_POST['firstname']) ){
					    $firstname = $_POST['firstname'];
					    $lastname  = $_POST['lastname'];
						
                        $default 		= get_option('migla_default_timezone');
	                    $language 		= get_option('migla_default_datelanguage');
	                    $date_format 	= get_option('migla_default_dateformat');

                        $date = migla_get_date( $default, $language , $date_format, false, $postdata['miglad_date'] );	
						
					    $amount    = $_POST['amount'];
                       }	
    				   
			     }
			}
			
			$placeholder = array( '[firstname]','[lastname]' ,'[amount]' ,'[date]' , '[newline]' );				   
			$replace = array( $firstname , $lastname , $amount , $date, '<br>'  );

			$page =  str_replace($placeholder, $replace, $trimquote);	
		
		    //$page .= $firstname . $lastname . $date . $amount . $_GET['firstname'] . '_' . $_POST['firstname'];
            //$page .= 'END';	

	  if( get_option('migla_thankyou_url') == false || get_option('migla_thankyou_url') == ''  )
		add_option( 'migla_thankyou_url', get_permalink() );

          update_option( 'migla_thankyou_url', get_permalink() );
		
         return $page;				
	}

}//END OF CLASSES

Migla_Thank_You_Shortcode::init();


	function migla_variables()
	{ ?>
      <script type="text/javascript">

       <?php
       if( get_option('migla_ajax_caller') == 'wp' )
       { ?>
             var miglaajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
       <?php
       }else{ ?>
            var miglaajaxurl = '<?php echo plugins_url( 'totaldonations/the-ajax-caller.php' , dirname(__FILE__)); ?>';
       <?php
       } ?>

       <?php
       if( get_option('migla_ipn_choice') == 'front' )
       { ?>
             var miglanotifyurl  = '<?php echo home_url( 'index.php' ) . "?migla_listener=IPN"; ?>';             
       <?php
       }else{ ?>
             var miglanotifyurl  = '<?php echo migla_get_notify_url(); ?>';
       <?php
       } ?>

        var miglasuccessurl = '<?php 
		
			$return_id  = get_option('migla_thank_you_page');
			$return_url = get_permalink($return_id ) ;	
		    echo $return_url; 
	    ?>';

        var miglaajaxnonce = '<?php echo wp_create_nonce( "migla_" ); ?>';
        var miglastripe_PK = '<?php echo migla_getPK() ; ?>' ;

      </script><?php
	}

	function migla_captcha_scripts()
	{ 
		$ajax_url = plugins_url( 'totaldonations/the-ajax-caller.php' , dirname(__FILE__));
        if( get_option('migla_ajax_caller') == 'wp' )
			$ajax_url =  admin_url( 'admin-ajax.php' );   	
	?>
		<script>
		        function recaptchaCallback()
				{
					//alert('send this '+grecaptcha.getResponse());
					
					jQuery.ajax({
						type : 'post',
						url  : '<?php echo $ajax_url; ?>' ,
						data : {
										action 			: 'miglaA_authenticate_recaptcha', 
										response_send   :  grecaptcha.getResponse()
						},
						success: function( challenge_msg ) {
                            //alert( challenge_msg );	
							if( challenge_msg == 'success' ){
								jQuery('#migla_token_data').val( grecaptcha.getResponse() );
							}else{
								jQuery('#migla_token_data').val( 'failed' );							
							}
						} //Captcha	Success	
					}); //captcha ajax						
				}
				
				function mg_expired_recaptchaCallback()
				{
					jQuery('#migla_token_data').val( 'expired' );	
				}
		</script>		
	
	<?php }


  ?>