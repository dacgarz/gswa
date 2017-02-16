<?php

function migla_get_date( $default, $language , $date_format, $isfull, $inputdate ) 
{
    $php_time_zone 	= date_default_timezone_get();
	
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
 
    if( $inputdate == '' )
	$d = date('m')."/".date('d')."/".date('Y');
    else	
        $d = $inputdate ;

    if( $isfull )
        $now =  strftime( $date_format , date(strtotime($d)) ) . " " . $t ;
    else
        $now =  strftime( $date_format , date(strtotime($d)) ) ;

  date_default_timezone_set( $php_time_zone );

  return $now;
}


function migla_get_time_date()
{
	$php_time_zone 	= date_default_timezone_get();
	$date_time 		= array();
	$date_time['date'] = ""; 
	$date_time['time'] = ""; 
	$date_time['default'] = get_option('migla_default_timezone');
	
	if( $date_time['default'] == 'Server Time' )
	{
		$gmt_offset = -(get_option('gmt_offset'));
		if ($gmt_offset > 0){ 
			$time_zone = 'Etc/GMT+' . $gmt_offset; 
		}else{		
			$time_zone = 'Etc/GMT' . $gmt_offset;    
		}
		date_default_timezone_set( $time_zone );
		$date_time['time'] = date('H:i:s');
		$date_time['date'] = date('m')."/".date('d')."/".date('Y');				
	}else{
		date_default_timezone_set( $date_time['default'] );
		$date_time['time'] = date('H:i:s');
		$date_time['date'] = date('m')."/".date('d')."/".date('Y');
	}
	
	date_default_timezone_set( $php_time_zone );
	return $date_time;	
}

function migla_init_form()
{
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

   return $fields;
}

function migla_donations_last6months() 
{
  global $wpdb;
  $out     = array();  $data = array();
  $offline = array(); $online = array();
  
  $last_donation = $wpdb->get_var( $wpdb->prepare( 
		"SELECT MAX(DATE_FORMAT(STR_TO_DATE(meta_value, %s), %s)) 
		 FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta 
		 ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
		 WHERE (post_type = %s OR post_type = %s) and meta_key = %s"  ,
		   '%m/%d/%Y', '%Y-%m-%d','migla_donation','migla_odonation','miglad_date'
        )
	);
	
  $label        = array(); $offline = array(); $online = array();
  $first_date   = $last_donation;
  $dateField    = explode( "-", $first_date);
  $firstyear    = $dateField[0];//substr($thedate, 3,2);  
  $firstmonth   = $dateField[1]; //substr($thedate, 0,2);
  
  $i = 5; 
  while($i >= 0){
     if($i == 5){
	      if( strlen($firstmonth) < 2 ){  
		      $firstmonth = '0' . $firstmonth;
		  }
		  $online[$i]['month']  = $firstmonth;
		  $online[$i]['year']   = $firstyear;
		  $online[$i]['label']  = $firstyear.'/'.$firstmonth;  
		  $online[$i]['amount'] = 0.0;  
		  $online[$i]['name'] = ''; 
		  
          $offline[$i]['month'] = $firstmonth;
		  $offline[$i]['year']  = $firstyear;
		  $offline[$i]['label'] = $firstyear.'/'.$firstmonth; 
          $offline[$i]['amount'] = 0.0; 
          $offline[$i]['name'] = ''; 		  	  
	  }else{
		  $prevmonth = (int)$online[$i+1]['month'];
		  $prevyear  = (int)$online[$i+1]['year'];
		  if( $prevmonth == 1 ){
             $nextmonth = 12;
			 $nextyear  = $prevyear - 1;
		  }else{
             $nextmonth = $prevmonth - 1;
			 $nextyear  = $prevyear;		  
		  }

		  $next_month    = $nextmonth;		  
	      if( strlen($next_month) < 2 ){  
		      $next_month = '0' . $next_month;
		  }		  
		  $online[$i]['month'] = $next_month;
		  $online[$i]['year']  = $nextyear;
		  $online[$i]['label'] = $nextyear.'/'.$next_month; 
          $online[$i]['amount'] = 0.0; 		
          $online[$i]['name'] = '';		  
		  
		  $offline[$i]['month'] = $next_month;
		  $offline[$i]['year']  = $nextyear;
		  $offline[$i]['label'] = $nextyear.'/'.$next_month;  	
          $offline[$i]['amount'] = 0.0;
          $offline[$i]['name'] = ''; 		  		  
	}
	$i--;
  }	
  
  for( $j = 0; $j < 6; $j++ ){
	 $id_online = $wpdb->get_results( $wpdb->prepare( 
			    "SELECT ID 
			    FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta 
				ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
				WHERE post_type = %s AND meta_key = %s and meta_value like %s"  ,
				'migla_donation','miglad_date', '%'.($online[$j]['month']).'/%/'.($online[$j]['year'])
			)
		); 
		
	 foreach( $id_online as $id1 )
	 {
	    $online[$j]['amount'] = $online[$j]['amount'] + (float)get_post_meta( intval( $id1->ID ) , 'miglad_amount', true);
	 }
	 
	 $id_offline = $wpdb->get_results( $wpdb->prepare( 
			    "SELECT ID 
			    FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta 
				ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
				WHERE post_type = %s AND meta_key = %s and meta_value like %s"  ,
				'migla_odonation','miglad_date', '%'.($offline[$j]['month']).'/%/'.($offline[$j]['year'])
			)
		); 
		
	 foreach( $id_offline as $id1 )
	 {
	    $offline[$j]['amount'] = $offline[$j]['amount'] + (float)get_post_meta( intval( $id1->ID ) , 'miglad_amount', true);
	 }	 
	 
  }  
 
  $output = array();
  $output[0] = $online;
  $output[1] = $offline;
  
  return $output; 
}

function migla_data_mapping( $_array )
{
     $map = array();
     $map['miglad_anonymous'] = 'no'; 
     $map['miglad_repeating'] = 'no'; 
	 $map['miglad_honoreeletter'] = '';
	 $map['miglad_employer'] = '';

     foreach( (array)$_array as $_data )
     {
          $_key 		= migla_trim_sql_xss( $_data[0] );
          $_value 		= migla_trim_sql_xss( $_data[1] );
          $map[$_key] 	= $_value;
     }

	 $date_time 				= migla_get_time_date();
	 $map['miglad_timezone'] 	= $date_time['default'];  
     $map['miglad_date'] 		= $date_time['date'];	   
     $map['miglad_time'] 		= $date_time['time'];
	   
   return $map;
}

function migla_trim_sql_xss( $string ){
   //$safeout = mysql_real_escape_string( $string );
   $safeout = str_replace("'"," ", $string );
   $safeout = htmlspecialchars( $safeout );
   $safeout = strip_tags( $safeout );
    
   return $safeout;
}

function migla_get_country_code( $name ){
    $country = get_option('migla_world_countries');
    $r 		 = "";
    foreach( (array)$country as $key => $value ){
		if( $value == $name )
			   $r = $key;
    }
   return $r;
}

function migla_delete_post_meta1( $meta_key ) {
  global $wpdb;
 
  $wpdb->query( 
	$wpdb->prepare( 
         "DELETE FROM {$wpdb->prefix}postmeta WHERE meta_key like %s" 
          , $meta_key
        )
     ); 
}


function migla_delete_post_meta2( $meta_id ) {
  global $wpdb;
 
  $wpdb->query( 
	$wpdb->prepare( 
         "DELETE FROM {$wpdb->prefix}postmeta WHERE meta_id = %d" 
          , $meta_id
        )
     ); 
}

/***********      Stripe's Function    *********************************/
function migla_getSK(){
   $SK = get_option('migla_liveSK');

   if( get_option('migla_stripemode') == 'test' ){
      $SK = get_option('migla_testSK');
   }

   return $SK;
}

function migla_getPK(){
   $PK = get_option('migla_livePK');

   if( get_option('migla_stripemode') == 'test' ){
      $PK = get_option('migla_testPK');
   }

   return $PK;
}

function migla_get_stripeplan_id() {
    global $wpdb;
    $pid = $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type = %s ORDER BY ID ASC" , 'migla_stripe_plan') );
    if( $pid != '' )
    {
        return $pid;
    }else{
 
      $new_donation = array(
	'post_title' => 'migla_donation',
	'post_content' => '',
	'post_status' => 'publish',
	'post_author' => 1,
	'post_type' => 'migla_stripe_plan'
       );

       $new_id = wp_insert_post( $new_donation );

       return $new_id;
   }
}

function migla_get_select_values_postid() 
{
    global $wpdb;
    $pid = $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type = %s ORDER BY ID ASC" , 'migla_custom_values') );
    if( $pid != '' )
    {
        return $pid;
    }else{
 
      $new_donation = array(
	'post_title' => 'migla_donation',
	'post_content' => '',
	'post_status' => 'publish',
	'post_author' => 1,
	'post_type' => 'migla_custom_values'
       );

       $new_id = wp_insert_post( $new_donation );

       return $new_id;
   }
}

/************************************************************************/


function migla_delete_all_settings(){
  global $wpdb;
 
  $wpdb->query( 
	$wpdb->prepare( 
         "DELETE FROM {$wpdb->prefix}options WHERE option_name like %s" 
          , 'migla%'
        )
     ); 

  $wpdb->query( 
	$wpdb->prepare( 
         "DELETE FROM {$wpdb->prefix}options WHERE option_name like %s" 
          , 't_migla%'
        )
     ); 

    $pid = $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type = %s ORDER BY ID ASC" , 'migla_custom_values') );
    if( $pid != '' )
    {
       $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}postmeta WHERE post_id = %d" , $pid  ));
       $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}posts WHERE ID = %d" , $pid  ));
    }else{

    }

}


function migla_get_succesful_url( $id )
{
	$thank_url = get_option('migla_thankyou_url');
	if( $thank_url != '' )
		$successUrl = $thank_url ;
	else
		$successUrl = migla_get_current_url();
		
	if (strpos($successUrl, "?") === false)
	{
		$successUrl .= "?";
	}
	else
	{
		$successUrl .= "&";
	}

	$successUrl .= "thanks=thanks";
	$successUrl .= "&id=" . $id ;

   return $successUrl;
}


function migla_get_notify_url(){
    $notifyUrl = plugins_url('totaldonations/migla-donation-paypalstd-ipn.php', dirname(__FILE__) );

    return $notifyUrl;
}


function migla_restore_from_old_donation( $old_id, $new_id){
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
        update_post_meta( $new_id, (string)$id->meta_key , (string)$id->meta_value );
     }
   }//foreach  

}

/***************************************************************************************/
/*     THANK YOU EMAIL, NOTIFICATION EMAIL AND LETTER FOR HONOREE Nov 23rd  2014       */
/***************************************************************************************/

function mg_send_thank_you_email( $postData, $e, $en )
{
   include_once 'migla_class_email_handler.php';

   $CEmail = new migla_email_handler();
   $status = $CEmail->send_thankyou_email( $postData, $e, $en );    

   return $status;
}

function mg_send_notification_emails( $postData, $e, $en , $ne )
{
   include_once 'migla_class_email_handler.php';

   $CEmail = new migla_email_handler();
   $status = $CEmail->send_notif_email( $postData, $e, $en , $ne );    

   return $status;
}

function mg_send_hletter( $postData, $e, $en, $content, $d )
{
   include_once 'migla_class_email_handler.php';

    $CEmail = new migla_email_handler();

    $status = $CEmail->send_hletter( $e, $en , 
        $postData['miglad_honoreeemail'], 
        $content, 
        $postData['miglad_repeating'],
        $postData['miglad_anonymous'], 
        $postData['miglad_firstname'], 
        $postData['miglad_lastname'], 
        $postData['miglad_amount'], 
        $postData['miglad_honoreename'], 
        $d ,
        $postData['miglad_campaign'] );

   return $status;
}

function mg_send_offline_first_email( $postData, $e, $en )
{
   include_once 'migla_class_email_handler.php';

   $CEmail = new migla_email_handler();
   $status = $CEmail->send_offline_first_email( $postData, $e, $en );    

   return $status;
}


/************************** THE REPEATING HANDLER ******************************/
/*********************************************************************************************/
function testing_repeat(){
  $old_ids = array(); 
  $new_id = 504;
  $post = array();
  $post[0] = "paymentdata";
  $post[1] = "paymentmethod";
  $post[2] = "transaction_type";
  $post[3] = "id";
  $post[4] = "session";
  $post[5] = (string)date( 'H:i:s', current_time( 'timestamp', 0 ) );
  $post[6] = (string)date( 'm/d/Y');

  //1
  $old_ids =  migla_cek_repeating_id( "migla161715530_20141221021252" );
  if(  migla_cek_id_exist( $new_id )==1  || empty($old_ids[0]) )
  {
  }else{
     migla_create_from_old_donation( $old_ids[0], $new_id);

        //Payment data

   add_post_meta( $new_id, "miglad_paymentdata" , $post[0] );
   add_post_meta( $new_id, "miglad_paymentmethod" , $post[1] );
   add_post_meta( $new_id, "miglad_transactionType" , $post[2] );
   add_post_meta( $new_id, "miglad_transactionId" , $post[3] );
   add_post_meta( $new_id, "miglad_session_id" , $post[4] );
   add_post_meta( $new_id, "miglad_time" , $post[5] );
   add_post_meta( $new_id, "miglad_date" , $post[6] );
  } 
}

function migla_cek_repeating_id( $meta_value ) {
    global $wpdb;
    $pid = $wpdb->get_var( $wpdb->prepare(
           "SELECT post_id FROM $wpdb->postmeta WHERE meta_value = %s AND meta_key = 'miglad_session_id' ORDER BY post_id ASC"
            ,$meta_value  ));
    if( $pid != '' )
        return $pid;
    else 
        return -1;
}

function migla_cek_card_id( $meta_value ) {
    global $wpdb;
    $pid = $wpdb->get_var( $wpdb->prepare(
           "SELECT post_id FROM $wpdb->postmeta WHERE meta_value = %s AND meta_key = 'miglad_card_id' ORDER BY post_id ASC"
            ,$meta_value  ));
    if( $pid != '' )
        return $pid;
    else 
        return -1;
}

function migla_check_if_exist( $meta_key, $meta_value ) {
    global $wpdb;
    $pid = $wpdb->get_var( $wpdb->prepare(
           "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value = %s ORDER BY post_id ASC"
            , $meta_key, $meta_value  ));
    if( $pid != '' )
        return $pid;
    else 
        return -1;
}

function migla_cek_id_exist( $id ){
   $isExist = 1;
   $sql = "SELECT distinct post_id FROM {$wpdb->prefix}postmeta WHERE post_id = ".$id." ";
   global $wpdb;
   $post = array(); $id = array(); $i = 0;
   $post = $wpdb->get_results($sql);   
   foreach ( $post as $p ){
     $id[$i] = intval( $p->post_id ); $i++;
   }  
   if( empty( $id[0] )  ){ $isExist = 0; } 
   return $isExist;
}

function migla_create_from_old_donation( $old_id, $new_id)
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

/**********************************************************************************************************************************/

/***********************************************/
/*            GET OPTION ID  FINISH Nov 21st */
/***********************************************/
function migla_get_option_id( $op ){
  global $wpdb; $res =array();
  $sql = "SELECT option_id from {$wpdb->prefix}options WHERE option_name='".$op."'";
  $res = $wpdb->get_row($sql);
  return $res->option_id;
}

function migla_reset_form() {
global $wpdb;

$fields =  array (
    '0' => array (
        'title' => 'Donation Information',
        'child' =>  array(
                   '0' => array( 'type'=>'radio','id'=>'amount', 'label'=>'How much would you like to donate?', 'status'=>'3', 'code' => 'miglad_'),
                   '1' => array( 'type'=>'select','id'=>'campaign', 'label'=>'Would you like to donate this to a specific campaign?', 'status'=>'3', 'code' => 'miglad_'),
                   '2' => array( 'type'=>'checkbox','id'=>'repeating', 'label'=>'Repeat Monthly?', 'status'=>'1', 'code' => 'miglad_')
                 ),
        'parent_id' => 'NULL',
        'depth' => 2,
        'toggle' => '-1'
    ),
    '1' => array (
        'title' => 'Donor Information',
        'child' => array(
                   '0' => array( 'type'=>'text','id'=>'firstname', 'label'=>'First Name', 'status'=>'3', 'code' => 'miglad_' ),
                   '1' => array( 'type'=>'text','id'=>'lastname', 'label'=>'Last Name', 'status'=>'3', 'code' => 'miglad_' ),
                   '2' => array( 'type'=>'text','id'=>'address', 'label'=>'Address', 'status'=>'1' , 'code' => 'miglad_' ),
                   '3' => array( 'type'=>'select','id'=>'country', 'label'=>'Country', 'status'=>'1' , 'code' => 'miglad_' ),
                   '4' => array( 'type'=>'text','id'=>'city', 'label'=>'City', 'status'=>'1' , 'code' => 'miglad_' ),
                   '5' => array( 'type'=>'text','id'=>'postalcode', 'label'=>'Postal Code', 'status'=>'1' , 'code' => 'miglad_' ),
                   '6' => array( 'type'=>'checkbox','id'=>'anonymous', 'label'=>'Anonymous?', 'status'=>'1' , 'code' => 'miglad_' ),
                   '7' => array( 'type'=>'text','id'=>'email', 'label'=>'Email', 'status'=>'3' , 'code' => 'miglad_' )
                 ),
        'parent_id' => 'NULL',
        'depth' => 8,
        'toggle' => '-1'
    ),
    '2' => array (
        'title' => 'Is this donation a Gift?',
        'child' => array(
                   '0' => array( 'type'=>'checkbox','id'=>'memorialgift', 'label'=>"Is this a Memorial Gift?", 'status'=>'1', 'code' => 'miglad_' ),
                   '1' => array( 'type'=>'text','id'=>'honoreename', 'label'=>"Honoree[q]s Name", 'status'=>'1', 'code' => 'miglad_' ),
                   '2' => array( 'type'=>'text','id'=>'honoreeemail', 'label'=>"Honoree[q]s Email", 'status'=>'1', 'code' => 'miglad_' ),
                   '3' => array( 'type'=>'textarea','id'=>'honoreeletter', 'label'=>"Write a custom note to the Honoree here", 'status'=>'1', 'code' => 'miglad_' ),
                   '4' => array( 'type'=>'text','id'=>'honoreeaddress', 'label'=>"Honoree[q]s Address", 'status'=>'1', 'code' => 'miglad_' ),
                   '5' => array( 'type'=>'text','id'=>'honoreecountry', 'label'=>"Honoree[q]s Country", 'status'=>'1', 'code' => 'miglad_' ),
                   '6' => array( 'type'=>'text','id'=>'honoreecity', 'label'=>'Honoree[q]s City', 'status'=>'1' , 'code' => 'miglad_' ),
                   '7' => array( 'type'=>'text','id'=>'honoreepostalcode', 'label'=>'Honoree[q]s Postal Code', 'status'=>'1' , 'code' => 'miglad_' ),				   
                 ),
        'parent_id' => 'NULL',
        'depth' => 5,
        'toggle' => '1'

    ),
    '3' => array (
        'title' => 'Is this a matching gift?',
        'child' => array(
                   '0' => array( 'type'=>'text','id'=>'employer', 'label'=>'Employer[q]s Name', 'status'=>'1', 'code' => 'miglad_' ),
                   '1' => array( 'type'=>'text','id'=>'occupation', 'label'=>'Occupation', 'status'=>'1', 'code' => 'miglad_' )
                 ),
        'parent_id' => 'NULL',
        'depth' => 3,
        'toggle' => '1'
    )        
);

if ( migla_get_option_id( 'migla_form_fields' ) > 0){
  $sql = "UPDATE {$wpdb->prefix}options SET option_value = '".serialize($fields)."' WHERE option_name ='migla_form_fields'";
}else{
  $sql = "INSERT INTO {$wpdb->prefix}options(option_name, option_value) values('migla_form_fields', '".serialize($fields)."')";

}
$wpdb->query($sql);
}


/************************************************************************
* 	PURGE TRANSIENT Dec 04 2014
*************************************************************************/
function purgeTransient(){
 global $wpdb;

 $sql = "DELETE FROM {$wpdb->prefix}options
        where option_name like '%transient_migla%'
        AND SUBSTRING(option_name, 12) IN
       (
         SELECT SUBSTRING(option_name,20) from {$wpdb->prefix}options
         WHERE option_name LIKE '%transient_timeout_migla%'
         AND DATEDIFF( DATE_FORMAT( FROM_UNIXTIME( option_value ) , '%Y-%m-%d' ) , Now() ) < -10
        )";

 $wpdb->query( $sql );
}

function purgeTransient2(){
 global $wpdb;
 $sql = " DELETE from {$wpdb->prefix}options
          WHERE option_name LIKE '%transient_timeout_migla%'
          AND DATEDIFF( DATE_FORMAT( FROM_UNIXTIME( option_value ) , '%Y-%m-%d' ) , Now() ) < -10";
 $wpdb->query( $sql );
}



/************************************************************************
* 	PROGRESS BAR   
*************************************************************************/


function getCurrencySymbol2()
{
   $code = (string)get_option(  'migla_default_currency'  );
   $arr = get_option( 'migla_currencies' ); 
   $icon ='';
   foreach ( $arr as $key => $value ) {
     if(  strcmp( $code, $arr[$key]['code'] ) == 0  ){
       $icon = $arr[$key]['symbol']; 
       break;
     }
   }
   return $icon;
}

function miglahex2RGB($hex) 
{
        preg_match("/^#{0,1}([0-9a-f]{1,6})$/i",$hex,$match);
        if(!isset($match[1]))
        {
            return false;
        }

        if(strlen($match[1]) == 6)
        {
            list($r, $g, $b) = array($hex[0].$hex[1],$hex[2].$hex[3],$hex[4].$hex[5]);
        }
        elseif(strlen($match[1]) == 3)
        {
            list($r, $g, $b) = array($hex[0].$hex[0],$hex[1].$hex[1],$hex[2].$hex[2]);
        }
        else if(strlen($match[1]) == 2)
        {
            list($r, $g, $b) = array($hex[0].$hex[1],$hex[0].$hex[1],$hex[0].$hex[1]);
        }
        else if(strlen($match[1]) == 1)
        {
            list($r, $g, $b) = array($hex.$hex,$hex.$hex,$hex.$hex);
        }
        else
        {
            return false;
        }

        $color = array();
        $color['r'] = hexdec($r);
        $color['g'] = hexdec($g);
        $color['b'] = hexdec($b);

        return $color;
}

////////////////////// Progress Bar and Text Shortcodes //////////////////////////////////

function migla_shortcode_progressbar( $c, $btn , $btntext, $text, $btn_class )
{

  $output = "";
  if( $c == '' )
  {
    $campaignArr = (array)get_option('migla_campaign');
    if( empty($campaignArr[0]) ){
    }else{
      foreach( $campaignArr as $key => $value )
      {
        $output = migla_text_progressbar2( $campaignArr[$key]['name'], '', $btn, $btntext, $text, $btn_class );
      }
    }

  }else{
        $output =migla_text_progressbar2(  $c, '', $btn , $btntext, $text, $btn_class );
  }
  return $output;
}

function migla_draw_progress_bar( $percent )
{
   $effect = (array)get_option( 'migla_bar_style_effect' );
		// Five Row Progress Bar
                $effectClasses = "";
                if( strcmp( $effect['Stripes'] , "yes") == 0){
                  $effectClasses = $effectClasses . " striped";
                }
                if( strcmp( $effect['Pulse'] , "yes") == 0){
                  $effectClasses = $effectClasses . " mg_pulse";
                }
                if( strcmp( $effect['AnimatedStripes'] ,"yes") == 0){
                  $effectClasses = $effectClasses . " active animated-striped";
                }
                if( strcmp( $effect['Percentage'], "yes") == 0 ){
                  $effectClasses = $effectClasses . " mg_percentage";
                }

        $borderRadius = explode(",", get_option( 'migla_borderRadius' )); //4spinner
        $bar_color = explode(",", get_option( 'migla_bar_color' ));  //rgba
        $progressbar_bg = explode(",", get_option( 'migla_progressbar_background' )); //rgba
        $boxshadow_color = explode(",", get_option( 'migla_wellboxshadow' )); //rgba 4spinner 

        $style1 = "";
        $style1 .= "box-shadow:".$boxshadow_color[2]."px ".$boxshadow_color[3]."px ".$boxshadow_color[4]."px ".$boxshadow_color[5]."px " ;
        $style1 .= $boxshadow_color[0]." inset !important;";
        $style1 .= "background-color:".$progressbar_bg[0].";";

        $style1 .= "-webkit-border-top-left-radius:".$borderRadius[0]."px; -webkit-border-top-right-radius: ".$borderRadius[1]."px;";
        $style1 .= "-webkit-border-bottom-left-radius: ".$borderRadius[2]."px; -webkit-border-bottom-right-radius:".$borderRadius[3]."px;";

        $style1 .= "-moz-border-radius-topleft:".$borderRadius[0]."px; -moz-border-radius-topright: ".$borderRadius[1]."px;";
        $style1 .= "-moz-border-radius-bottomleft: ".$borderRadius[2]."px;-moz-border-radius-bottomright:".$borderRadius[3]."px;";

        $style1 .= "border-top-left-radius:".$borderRadius[0]."px; border-top-right-radius: ".$borderRadius[1]."px;";
        $style1 .= "border-bottom-left-radius:  ".$borderRadius[2]."px;border-bottom-right-radius:".$borderRadius[3]."px;";
	
        $stylebar = "background-color:".$bar_color[0].";";

	$output = "";

        $output .= "<div id='me' class='progress ".$effectClasses."' style='".$style1."'> ";
        $output .= "<div class='progress-bar bar' role='progressbar' aria-valuenow='20' aria-valuemin='0' aria-valuemax='100'";
        $output .= "style='width:".$percent."%;".$stylebar."'>";
        $output .= $percent . "%";
        $output .= "</div>";
        $output .= "</div>";

        return $output;
}

function migla_get_form_id( $cname )
{
	$campaigns 	= get_option( 'migla_campaign' );
	$form_id 	= '';
	$j 			= 0;
			
	if( !empty($campaigns) )
	{    
		foreach ( (array)$campaigns as $key => $value ) 
		{ 
			if( $cname == $campaigns[$j]['name'] )
			{
				$form_id = $campaigns[$j]['form_id'];
				break;
			}
			$j++;
	   }
	}	

	return $form_id;
}

function migla_text_progressbar(  $cname, $posttype , $linkbtn, $btntext, $text )
{
  	$total = 0;
	$total_amount = 0;
	$target = 0; 
	$target_amount = 0;
	$percent = 0.0;
	$percentStr = '';
	$remainder = 0;
	$donors = 0;
	$totals = array();
	
        $totals = migla_get_totals( $cname, $posttype );
		$total	= $totals[0];
		$donors	= $totals[1];
        $target = migla_get_campaign_target( $cname );

    if(  $target != 0 )
	{
		if( $total == 0 )
		{
			  $percent = 0;	
		}else if( $target != 0 ) {
			  $percent = number_format(  ( $total / $target) * 100 , 2);		  
		}
			$remainder = $target - $total ;
			$info = get_option('migla_progbar_info'); 

			$symbol = getCurrencySymbol2();
			$x = array();
			$x[0] = get_option('migla_thousandSep');
			$x[1] = get_option('migla_decimalSep');
			$before = ''; $after = '';

			if( strtolower(get_option('migla_curplacement')) == 'before' ){
			  $before = $symbol;
			}else{
			  $after = $symbol;		
			}
			
			$showSep = get_option('migla_showDecimalSep');
			$decSep = 0;
			if( strcmp($showSep , "yes") == 0 ){ $decSep = 2; }

			$total_amount 	= $before. number_format( $total , $decSep, $x[1], $x[0]). $after;
			$target_amount 	= $before. number_format( $target , $decSep, $x[1], $x[0]) .$after;
			$percentStr = $percent . "%";
			if( $remainder < 0 ){
			   $remainder_text = '';
			}else{
			   $remainder_text = $before. number_format( $remainder , $decSep, $x[1], $x[0]). $after;
			}

			
			//codes [target] [total] [percentage] [campaign]
			$cname2 = str_replace("[q]", "'", $cname);

			$placeholder = array( '[total]','[target]' ,'[campaign]', '[percentage]', '[remainder]' );
			$replace = array( $total_amount , $target_amount , $cname2, $percentStr , $remainder_text  );
			$content =  str_replace($placeholder, $replace, $info);
			$output = "";
			$output .= "<div class='bootstrap-wrapper'>";
			if($text == 'yes' || $text == '' )
			{
			  $output .= "<div class='progress-bar-text'><p class='progress-bar-text'>";
			  $output .= $content;
			  $output .= "</p></div>";
			}
			$output .= migla_draw_progress_bar( $percent );
			
			$form_id = migla_get_form_id( $cname );
			$output .= "<input type='hidden' id='mg_pg_".$form_id."' value=''/>";
			
			$output .= "</div>";

         if( $linkbtn == "yes")
		{	  		
			if( $form_id != '')
			{
				$url = get_post_meta( $form_id, 'migla_form_url', true);
				
				if( $url == '' || $url == false || $url[0] == '' || empty($url) )
					$url = get_option('migla_form_url');
			}else{
				$url = get_option('migla_form_url');
			}
			
         $output .= "<form action='".$url."' method='post'>";
         $output .= "<input type='hidden' name='campaign' value='".$cname."' />";
         $output .= "<input type='hidden' name='thanks' value='widget_bar' />";
         $output .= "<button class='migla_donate_now mg-btn-grey'>".$btntext."</button>";
         $output .= "</form>";			
       }

	}else{
		$output = "";
	}

    return $output;
}

function migla_text_progressbar2(  $cname, $posttype , $linkbtn, $btntext, $text, $btn_class )
{
  	$total = 0;
	$total_amount = 0;
	$target = 0; 
	$target_amount = 0;
	$percent = 0.0;
	$percentStr = '';
	$remainder = 0;
	$donors = 0;
	$totals = array();
	
        $totals = migla_get_totals( $cname, $posttype );
		$total	= $totals[0];
		$donors	= $totals[1];
        $target = migla_get_campaign_target( $cname );

    if(  $target != 0 )
	{
		if( $total == 0 )
		{
			  $percent = 0;	
		}else if( $target != 0 ) {
			  $percent = number_format(  ( $total / $target) * 100 , 2);		  
		}
			$remainder = $target - $total ;
			$info = get_option('migla_progbar_info'); 

			$symbol = getCurrencySymbol2();
			$x = array();
			$x[0] = get_option('migla_thousandSep');
			$x[1] = get_option('migla_decimalSep');
			$before = ''; $after = '';

			if( strtolower(get_option('migla_curplacement')) == 'before' ){
			  $before = $symbol;
			}else{
			  $after = $symbol;		
			}
			
			$showSep = get_option('migla_showDecimalSep');
			$decSep = 0;
			if( strcmp($showSep , "yes") == 0 ){ $decSep = 2; }

			$total_amount 	= $before. number_format( $total , $decSep, $x[1], $x[0]). $after;
			$target_amount 	= $before. number_format( $target , $decSep, $x[1], $x[0]) .$after;
			$percentStr = $percent . "%";
			if( $remainder < 0 ){
			   $remainder_text = '';
			}else{
			   $remainder_text = $before. number_format( $remainder , $decSep, $x[1], $x[0]). $after;
			}

			
			//codes [target] [total] [percentage] [campaign]
			$cname2 = str_replace("[q]", "'", $cname);

			$placeholder = array( '[total]','[target]' ,'[campaign]', '[percentage]', '[remainder]' );
			$replace = array( $total_amount , $target_amount , $cname2, $percentStr , $remainder_text  );
			$content =  str_replace($placeholder, $replace, $info);
			$output = "";
			$output .= "<div class='bootstrap-wrapper'>";
			if($text == 'yes' || $text == '' )
			{
			  $output .= "<div class='progress-bar-text'><p class='progress-bar-text'>";
			  $output .= $content;
			  $output .= "</p></div>";
			}
			$output .= migla_draw_progress_bar( $percent );
			
			$form_id = migla_get_form_id( $cname );
			$output .= "<input type='hidden' id='mg_pg_".$form_id."' value=''/>";
			
			$output .= "</div>";

        if( $linkbtn == "yes")
		{	  		
			if( $form_id != '')
			{
				$url = get_post_meta( $form_id, 'migla_form_url', true);
				
				if( $url == '' || $url == false || $url[0] == '' || empty($url) )
					$url = get_option('migla_form_url');
			}else{
				$url = get_option('migla_form_url');
			}
			
        $output .= "<form action='".$url."' method='post'>";
        $output .= "<input type='hidden' name='campaign' value='".$cname."' />";
        $output .= "<input type='hidden' name='thanks' value='widget_bar' />";
		
		if( $btn_class == '')
			$output .= "<button class='migla_donate_now mg-btn-grey'>".$btntext."</button>";
		else
			$output .= "<button class='migla_donate_now ".$btn_class."'>".$btntext."</button>";
			
        $output .= "</form>";			
       }

	}else{
		$output = "";
	}

    return $output;
}

/**************** Widget Progress Bar *********************************/
function migla_widget_progress_bar( $cname, $borderRadius, $boxshadow_color, $barcolor,
       	   $well_background, $well_shadows, $effects )
{

  	$total_amount 	= 0; 
	$target_amount 	= 0; 
	$percent 		= 0; 
    $total 			= 0;
    $target			= 0; 
	$donors			= 0;
	$posttype		= '';
	
    $totals = migla_get_totals( $cname, $posttype );
	$total	= $totals[0];
	$donors	= $totals[1];
	
    $cname2 = str_replace("[q]", "'", $cname);		
	
    $target = migla_get_campaign_target( $cname );

	$output = "";
	
    if(  $target != 0 )
	{
		if( $total == 0 )
		{
			  $percent = 0;	
		}else if( $target != 0 ) {
			  $percent = number_format(  ( $total / $target) * 100 , 2);		
		}
	

        $symbol = getCurrencySymbol2();
        $x = array();
        $x[0] = get_option('migla_thousandSep');
        $x[1] = get_option('migla_decimalSep');
		$before = ''; $after = '';

		if( strtolower(get_option('migla_curplacement')) == 'before' ){
		  $before = $symbol;
		}else{
		  $after = $symbol;		
		}
		
        $showSep = get_option('migla_showDecimalSep');
        $decSep = 0;
        if( strcmp($showSep , "yes") == 0 ){ $decSep = 2; }

        $total_amount  = $before. number_format( $total , $decSep, $x[1], $x[0]). $after;
        $target_amount = $before. number_format( $target , $decSep, $x[1], $x[0]) .$after;
        $percentStr 	= $percent . "%";
	
	   $output .= "<div class='bootstrap-wrapper'>";
	   $output .= "<div class='progress-sidebar'><p class='progress-sidebar'>";
	   $output .= "</p></div>";
	   
		// Five Row Progress Bar
		$effectClasses = "";
		if( $effects['stripes'] ){
			 $effectClasses = $effectClasses . " striped";
		}
		if( $effects['pulse'] ){
			 $effectClasses = $effectClasses . " mg_pulse";
		}
		if( $effects['animated_stripes'] ){
			 $effectClasses = $effectClasses . " active animated-striped";
		}
		if( $effects['percentage']){
			 $effectClasses = $effectClasses . " mg_percentage";
		}

        $style = "";
        $style .= "box-shadow:".$boxshadow_color[0]."px ".$boxshadow_color[1]."px ";
		$style .= $boxshadow_color[2]."px ".$boxshadow_color[3]."px " ;
        $style .= $boxshadow_color[4]." inset !important;";
		
        $style .= "background-color:".$well_background.";";

        $style .= "-webkit-border-top-left-radius:".$borderRadius[0]."px;";
		$style .= "-webkit-border-top-right-radius: ".$borderRadius[1]."px;";
        $style .= "-webkit-border-bottom-left-radius: ".$borderRadius[2]."px;";
        $style .=	"-webkit-border-bottom-right-radius:".$borderRadius[3]."px;";

        $style .= "-moz-border-radius-topleft:".$borderRadius[0]."px; -moz-border-radius-topright: ".$borderRadius[1]."px;";
        $style .= "-moz-border-radius-bottomleft: ".$borderRadius[2]."px;-moz-border-radius-bottomright:".$borderRadius[3]."px;";

        $style .= "border-top-left-radius:".$borderRadius[0]."px; border-top-right-radius: ".$borderRadius[1]."px;";
        $style .= "border-bottom-left-radius:  ".$borderRadius[2]."px;border-bottom-right-radius:".$borderRadius[3]."px;";
	
        $stylebar = "background-color:".$barcolor.";";

        $output .= "<div id='me' class='progress ".$effectClasses."' style='".$style."'> ";
        $output .= "<div class='progress-bar bar' role='progressbar' aria-valuenow='20' ";
		$output .= "aria-valuemin='0' aria-valuemax='100'";
        $output .= "style='width:".$percent."%;".$stylebar."'>";
        $output .= $percent . "%";
        $output .= "</div>";
        $output .= "</div>";   
   
		$output .= "</div>";
	}
	
	$the_outputs	= array();
	$the_outputs[0]	= $output;
	$the_outputs[1]	= $total_amount;
	$the_outputs[2]	= $target_amount;
	$the_outputs[3]	= $cname;
	$the_outputs[4]	= $donors;
	$the_outputs[5]	= $percent . "%";
	
    return $the_outputs;
}


/************************** Migla progress circle widget *************************************/
function migla_get_percentage(  $cname ){
  	$total_amount = 0; 
        $target = 0; $percent = 0.0;
	
        $total_amount = migla_get_total( $cname, "" );
        $target = migla_get_campaign_target( $cname );

        if(  $target != 0 ){
	  if( $total_amount == 0 )
	  {
	  }else if( $target != 0 ) {
             $percent = number_format(  ( $total_amount / $target) * 100 , 2);		
	  }
        }else{
        }
   return $percent;
}

function migla_count_by_key( $meta_key, $cname ){
   $total = 0;
   global $wpdb;
   
   /*
   $total = $wpdb->get_var( $wpdb->prepare("SELECT DISTINCT COUNT(post_id) FROM wp_postmeta
                                            where meta_key='%s' and meta_value='%s'
                                            group by meta_key" 
                            , $meta_key, $cname ) 
             );
	*/

	$posts = $wpdb->get_results( "SELECT DISTINCT post_id FROM {$wpdb->prefix}postmeta WHERE meta_value = '".$cname."'"
					, ARRAY_N );

	   
   return 'total'.count($posts);
}

function migla_get_campaign_target( $cname ){
  $t = 0;

  $cname = str_ireplace( "'", "[q]", $cname );  
   
  $data = (array)get_option('migla_campaign');
  if( $data == '' || $data == false )
  {
	  
  }else{
	  if( $cname == '' )
	  {
		if( empty($data[0]) ){
		}else{ 
		   foreach( (array)$data as $d ){
			  $t = $t + (float)$d['target'];
		   }
		}
	  }else{
		if( empty($data[0]) ){
		}else{ 
		   foreach( (array)$data as $d ){
			  if( strcmp($cname, $d['name']) == 0 ){ 
				 $t = $d['target']; break; 
			  }
		   }
		}
	  }
  }
  
  return $t;
}


function migla_get_campaign_target_b( $cname ){
  $t = 0;

  $cname = str_ireplace( "'", "[q]", $cname );  
   
  $data = get_option('migla_campaign');
  if( $data == '' || $data == false )
  {
	$t = -1;  
  }else{
	  if( $cname == '' )
	  {
		if( empty($data[0]) ){
		}else{ 
		   foreach( (array)$data as $d ){
			  $t = $t + (float)$d['target'];
		   }
		}
	  }else{
		if( empty($data[0]) ){
		}else{ 
		   foreach( (array)$data as $d ){
			  if( strcmp($cname, $d['name']) == 0 ){ 
				 $t = $d['target']; break; 
			  }
		   }
		}
	  }
  }
  
  return $t;
}


function migla_get_totals( $cname, $posttype ){
	global $wpdb; 
	$posts		= array();
	$totals		= array();
	
	if( $cname != '')
	{
		$posts = $wpdb->get_results("SELECT DISTINCT post_id FROM {$wpdb->prefix}postmeta INNER JOIN {$wpdb->prefix}posts
									ON {$wpdb->prefix}postmeta.post_id = {$wpdb->prefix}posts.ID
									WHERE meta_value = '".$cname."'
									AND post_type != 'migla_odonation_p'");
    }else{
		$posts = $wpdb->get_results("SELECT DISTINCT post_id FROM {$wpdb->prefix}postmeta INNER JOIN {$wpdb->prefix}posts
									ON {$wpdb->prefix}postmeta.post_id = {$wpdb->prefix}posts.ID
									WHERE post_type != 'migla_odonation_p'");	
	}
	
	$total = 0; $count = 0;
	foreach ( $posts as $id )
	{
		$total  = $total + (float)get_post_meta( intval( $id->post_id ) , 'miglad_amount', true);
		$count++;
	}  
	
	$totals[0] = $total;
	$totals[1] = $count;	
	
    return $totals;

}

function migla_get_total( $cname, $posttype )
{

  global $wpdb; $res =array();

 /* 
 if( $cname == '' )
 {
     $sql = 'select sum(meta_value) as total from '.$wpdb->prefix.'posts inner join '.$wpdb->prefix.'postmeta
             on '.$wpdb->prefix.'posts.ID = '.$wpdb->prefix.'postmeta.post_id
             where post_type like "migla%donation" AND meta_key = "miglad_amount"';
 }else{

     $sql = 'select sum(meta_value) as total from '.$wpdb->prefix.'posts inner join '.$wpdb->prefix.'postmeta
             on '.$wpdb->prefix.'posts.ID = '.$wpdb->prefix.'postmeta.post_id
             where (post_type = "migla_donation" OR post_type = "migla_odonation") AND post_id in (
             select post_id from '.$wpdb->prefix.'postmeta where meta_value = "'.$cname.'" and meta_key = "miglad_campaign"
              ) and meta_key = "miglad_amount"';
  }

  if( $posttype != '' ){
    $sql = $sql . " and post_type = '".$posttype."'";
  }
  $res = $wpdb->get_results($sql , ARRAY_A);
  return  $res[0]['total'];
  */
  
    if( $cname != '')
	{
		$posts = $wpdb->get_results("SELECT DISTINCT post_id FROM {$wpdb->prefix}postmeta WHERE meta_value = '".$cname."'");
    }else{
		$posts = $wpdb->get_results("SELECT DISTINCT post_id FROM {$wpdb->prefix}postmeta");	
	}
	
	$total = 0;
	foreach ( $posts as $id )
	{
		$total  = $total + (float)get_post_meta( intval( $id->post_id ) , 'miglad_amount', true);
	}  
	
    return $total;
}


function migla_draw_all_progress_bar( $c ){

  $output = "";
  if( $c == '' )
  {
    $campaignArr = (array)get_option('migla_campaign');
    if( empty($campaignArr[0]) ){
    }else{
      foreach( $campaignArr as $key => $value )
      {
        $output = migla_text_progressbar( $campaignArr[$key]['name'], "", "", "no", "no");
      }
    }

  }else{
        $output = migla_text_progressbar(  $c, "","", "no", "no");
  }
  echo $output;
}


/***************  Shortcode Progress Bar    ******************************/

function migla_draw_textbarshortcode(  $cname, $button, $buttontext, $text, $btn_class )
{
  	$total_amount	= 0;
	$remainder 		= 0;
    $target 		= 0; 
	$percent 		= 0.0;
	$backers 		= 0;		
	
	$the_totals 	= migla_get_totals( $cname, '' );
	$backers		= $the_totals[1];
	
    $total_amount = $the_totals[0];
    $target = migla_get_campaign_target( $cname );

    //if(  $target != 0 ){
    if( $total_amount == 0 )
	{
        $percent = 0;	
		if( $target != 0 )
		    $remainder = $target;
    }else if( $target != 0 ) 
	{
          $percent = number_format(  ( $total_amount / $target) * 100 , 2);	
          $remainder = $target -  $total_amount;		  
    }
     
        $op = get_option('migla_progbar_info'); 

        $symbol = getCurrencySymbol2();
        $x = array();
        $x[0] = get_option('migla_thousandSep');
        $x[1] = get_option('migla_decimalSep');
		$before = ''; $after = '';

         if( strtolower(get_option('migla_curplacement')) == 'before' ){
		   $before = $symbol;
         }else{
		   $after = $symbol;		
         }
		
        $showSep = get_option('migla_showDecimalSep');
        $decSep = 0;
        if( strcmp($showSep , "yes") == 0 ){ $decSep = 2; }

        $total_amount = $before. number_format( $total_amount , $decSep, $x[1], $x[0]). $after;
        $target = $before. number_format( $target , $decSep, $x[1], $x[0]) .$after;
        $percentStr = $percent . "%";
		if( $remainder < 0 ){
		   $remainder_text = '';
		}else{
		   $remainder_text = $before. number_format( $remainder , $decSep, $x[1], $x[0]). $after;
        }
		
        //codes [target] [total] [percentage] [campaign]
        $cname2 = str_replace("[q]", "'", $cname);	       
 		$form_id = migla_get_form_id( $cname );   
		$url = get_option('migla_form_url');
		
			if( $form_id != '')
			{
				$url = get_post_meta( $form_id, 'migla_form_url', true);
				
				if( $url == '' || $url == false || $url[0] == '' || empty($url) )
					$url = get_option('migla_form_url');
			}
		
        $placeholder = array( '#campaign#', '#total#','#target#' , '#percentage#' , '#remainder#', '#backers#' );
        $replace = array(  $cname2, $total_amount , $target , $percentStr , $remainder_text, $backers);
        $content =  str_replace($placeholder, $replace, $text);

        $start = $content;
        $pos1 = strpos($start , "#textlink:"); $afterform = "";

        if( $pos1 >= 0)
        {
          $start = substr($start, ( $pos1 + 1) );     
          $pos2 = strpos( $start , "#");

          $id = rand(); $id = "mgtextlink" . $id;
          $thecode = substr( $start , 0, $pos2 );
          $textlink = substr( $thecode , 9 );
          $thecode =  "#".$thecode."#";
          $temp = $content;

          $temp2 = "<a style='display:inline;padding:0px;margin:0px !important' href='javascript:{}' onclick='document.getElementById(\"".$id."\").submit(); return false;'>". $textlink."</a>";

          $afterform .= "<form id='".$id."' action='".$url."' method='post' style='display:none inline;padding:0px;margin:0px !important' class='form-inline' role='form'>";
          $afterform .= "<input type='hidden' name='campaign' value='".$cname."' style='display:inline;padding:0px;margin:0px !important' />";
          $afterform .= "<input type='hidden' name='thanks' value='widget_bar' />";
          $afterform .= "</form>";

          $content =  str_replace( $thecode, $temp2, $temp );
        }

        $output = "";
        $output .= "<div style='display:inline;' class='wrapper'>";
        $output .= $content;
        $output .= "</div>";
        $output .= $afterform;


        if( $button == "yes")
		{		
			$output .= "<form action='".$url."' method='post'>";
			$output .= "<input type='hidden' name='campaign' value='".$cname."' />";
			
			if($btn_class == '')
				$output .= "<button class='migla_donate_now mg-btn-grey'>".$buttontext."</button>";
			else
				$output .= "<button class='migla_donate_now ".$btn_class."'>".$buttontext."</button>";
				
			$output .= "</form>";
		}

     return $output;
}


/************** Shortcode Circle Progress Bar ********************************/
function migla_circle_text($id, $align, $fontsize)
{
	$output = "<div id='mg_circle_" . $id . "' class='migla_inpage_circle_bar' ";
        
        if( $align == 'mg_left-right' ){
              $output .= "style='float:left !important;margin-right:40px !important;'";
        }else if( $align == 'mg_right-left' ){
              $output .= "style='float:right !important;margin-left:0px !important;'";
		}else if( $align == 'mg_left-left' ){
              $output .= "style='float:left !important;margin-right:0px !important;'";	
        }else if( $align == 'mg_right-right' ){
              $output .= "style='float:right !important;margin-left:40px !important;'";			  
		}else{
              $output .= "";
        }				
		$output .= ">";
        $output .= "<span class='migla_circle_text' style='font-size:".$fontsize."px; ' ";
		$output .= "></span></div>";
		
		return $output ;
}


function migla_circle_sc_text_barometer($total_amount, $target_amount, $donors, $size, $align , $cname)
{

          $output = '';
          $output .= "<div class='mg_text-barometer' ";
	  
          if( $align == 'mg_left-right' )
		  {
              $output .= "style='float:right !important;margin-left:0px;text-align:right !important'";
          }else if( $align == 'mg_right-left' )
		  {
              $output .= "style='float:left !important;margin-right:40px;text-align:left !important'";
          }else if( $align == 'mg_left-left' )
		  {
              $output .= "style='float:right !important;margin-left:40px;text-align:left !important'";	
          }else if( $align == 'mg_right-right' )
		  {
              $output .= "style='float:left !important;margin-right:0px;text-align:right !important'";				  
		  }else{
              $output .= "";
          }

          $output .= ">";
			
        $text1 	= get_option('migla_circle_text1');
        $text2 	= get_option('migla_circle_text2');
        $text3 	= get_option('migla_circle_text3');
		 
          $output .= "<ul>
                      <li class='mg_inpage_campaign-raised'>
                      <span class='mg_inpage_current'>".$text1."</span> 
                      <span class='mg_inpage_current-amount'>".$total_amount."</span>
                      </li>
                      <li class='mg_inpage_campaign-goal'>
                      <span class='mg_inpage_target'>".$text2."</span>
                      <span class='mg_inpage_target-amount'>".$target_amount."</span>  
                      </li>
                      <li class='mg_inpage_campaign-backers'>
                      <span class='mg_inpage_backers'>".$text3."</span>
                      <span class='mg_inpage_backers-amount'>".$donors."</span>  
                     </li>  
                  </ul>
               </div>";	
   return $output;
}

function migla_circle_sc_html($id , $size, $align)
{
  $str          = get_option('migla_circle_box_html');
  $content_out  = str_replace( '\"', '', $str );

  $output  = "<div class='mg_inpage_text-html1' id='mg_chtml_".$id."'>";
  $output .= $content_out ;
  $output .= "</div>";
  
  return $output ;
}

function migla_sc_circle_progressbar(  $cname, $posttype , $linkbtn, $btntext, $text , $id, $btn_class )
{
  	$total_amount 	= 0; 
	$target_amount 	= 0; 
	$percent 		= 0; 
    $total 			= 0;
    $target			= 0; 
	$donors			= 0;
	
    $totals = migla_get_totals( $cname, $posttype );
	$total	= $totals[0];
	$donors	= $totals[1];
	
    $target = migla_get_campaign_target( $cname );

    if(  $target != 0 )
	{
		if( $total == 0 )
		{
			  $percent = 0;	
		}else if( $target != 0 ) {
			  $percent = number_format(  ( $total / $target) * 100 , 2);		
		}

        $symbol = getCurrencySymbol2();
        $x = array();
        $x[0] = get_option('migla_thousandSep');
        $x[1] = get_option('migla_decimalSep');
		$before = ''; $after = '';

		if( strtolower(get_option('migla_curplacement')) == 'before' ){
		  $before = $symbol;
		}else{
		  $after = $symbol;		
		}
		
        $showSep = get_option('migla_showDecimalSep');
        $decSep = 0;
        if( strcmp($showSep , "yes") == 0 ){ $decSep = 2; }

        $total_amount  = $before. number_format( $total , $decSep, $x[1], $x[0]). $after;
        $target_amount = $before. number_format( $target , $decSep, $x[1], $x[0]) .$after;
        $percentStr = $percent . "%";

        //codes [target] [total] [percentage] [campaign]
        $cname2 = str_replace("[q]", "'", $cname);
		
        $output 		= "";
        $output 		.= "<div class='bootstrap-wrapper mg_inpage_circle_box clearfix' id='mg_inpage_box_".$id."'>";

        $output .= migla_circle_js_shortcode($id);
		
		$boxes = get_option('migla_circle_layout');
		if( $boxes == false || $boxes == '' )
		{
		  $boxes = 'mg_no_text';		
		}
		
		
		$align = get_option('migla_circle_textalign');
		if($align == false){
			$align = 'mg_left-right';
		}
		  
			$circle_settings = get_option( 'migla_circle_settings' );				  
				  
		      $output .= "<div class='migla_inpage_circle_wrapper' id='mg_circle_wrap".$id."' style='display: table;margin: 0 auto 0 !important;float: none;'>";
		      $output .= migla_circle_text($id, $align, $circle_settings[0]['inner_font_size']  );
			  
				//Circle
				$output .= "<input type='hidden' class='migla_circle_id' value='".$id."' >";
				$output .= "<input type='hidden' class='migla_circle_value' value='".($total / $target)."' >";
				$output .= "<input type='hidden' class='migla_circle_percentage' value='". number_format(  ( $total / $target) * 100 , 2) ."' >";
				

				  $keys = array_keys($circle_settings[0]);
				  foreach( $keys as $key  )
				  {   
					   $output .= "<input type='hidden' class='migla_circle_" . $key. "' value='" .$circle_settings[0][$key]. "'>";
				  }
						 
			  if( $align != 'mg_no_text')
			  {
				$output .= migla_circle_sc_text_barometer($total_amount, $target_amount, $donors, 0, $align, $cname);		
              }			  
		 
				$output .= "</div>";
				
			   if( $linkbtn == "yes")
			   {
				 $c = str_replace( "'", "", $cname ); //Clean
				 $c = str_replace( " ", "", $c ); //Clean
				 $form_url = 'migla_url_' . $c;
				 $url = get_option( $form_url );

				if( $url == '' || $url == false){
					$url = get_option('migla_form_url');
				}

				$output .= "<form action='".$url."' method='post' class='mg_form-button-circle'>";
				$output .= "<input type='hidden' name='campaign' value='".$cname."' />";
				$output .= "<input type='hidden' name='thanks' value='widget_bar' />";	 
				
				if( $btn_class == '' ) 
					$output .= "<button class='migla_donate_now mg-btn-grey'>".$btntext."</button>";
				else
					$output .= "<button class='migla_donate_now ".$btn_class."'>".$btntext."</button>";
					
				$output .= "</form>";
			   }
										
			$output .= "</div>";
		
     }else{//Target
         $output = "";
     }
			
        return $output;
}


function migla_circle_js_shortcode( $id )
{
        $output = '';
        $output .= "<script type='text/javascript'>";
        $output .= "jQuery(document).ready( function() { ";
        		
        $output .= "var _reverse".$id . " = false ;";
        $output .= "if( jQuery('#mg_circle_wrap".$id."').find('.migla_circle_reverse').val() == 'yes' ){ _reverse".$id. " = true ; } ";

        $output .= "var _startangle".$id . " = ( Number(jQuery('#mg_circle_wrap".$id."').find('.migla_circle_start_angle').val()) / 180) * 3.14 ;";

        $output .= "if( jQuery('#mg_circle_wrap".$id."').find('.migla_circle_animation').val() == 'normal' ){";    

        $output .= "	jQuery('#mg_circle_".$id."').circleProgress({
				 value       : Number(jQuery('#mg_circle_wrap".$id."').find('.migla_circle_value').val()),
				 size        : jQuery('#mg_circle_wrap".$id."').find('.migla_circle_size').val(),
				 fill        : {
							   color: jQuery('#mg_circle_wrap".$id."').find('.migla_circle_fill').val()
					        },
				 lineCap     : jQuery('#mg_circle_wrap".$id."').find('.migla_circle_line_cap').val(),
				 startAngle  : _startangle".$id . ",
				 reverse     : _reverse".$id . ",
				 thickness   : jQuery('#mg_circle_wrap".$id."').find('.migla_circle_thickness').val()
			  }).on('circle-animation-progress', function(event, progress, stepValue)  {
                                  
				  if( jQuery('#mg_circle_wrap".$id."').find('.migla_circle_inside').val() == 'progress' )
				  {
                      jQuery('#mg_circle_wrap".$id."').find('.migla_circle_text').html(parseInt(100 * progress) + '<i>%</i>');					 
				  }else if ( jQuery('#mg_circle_wrap".$id."').find('.migla_circle_inside').val() == 'percentage' )
				  {
					  jQuery('#mg_circle_wrap".$id."').find('.migla_circle_text').html(jQuery('#mg_circle_wrap".$id."').find('.migla_circle_percentage').val() + '<i>%</i>'); 
				  }
                                  
			  });";

        $output .= "}else if( jQuery('#mg_circle_wrap".$id."').find('.migla_circle_animation').val() == 'back_forth' ) {";

        $output .= "	jQuery('#mg_circle_".$id."').circleProgress({
				 value       : Number(jQuery('#mg_circle_wrap".$id."').find('.migla_circle_value').val()),
				 size        : jQuery('#mg_circle_wrap".$id."').find('.migla_circle_size').val(),
				 fill        : {
							   color: jQuery('#mg_circle_wrap".$id."').find('.migla_circle_fill').val()
					        },
				 lineCap     : jQuery('#mg_circle_wrap".$id."').find('.migla_circle_line_cap').val(),
				 startAngle  : _startangle".$id . ",
				 reverse     : _reverse".$id . ",
				 thickness   : jQuery('#mg_circle_wrap".$id."').find('.migla_circle_thickness').val()
			  }).on('circle-animation-progress', function(event, progress, stepValue) 
                          {
						  
				  if( jQuery('#mg_circle_wrap".$id."').find('.migla_circle_inside').val() == 'progress' )
				  {
                      jQuery('#mg_circle_wrap".$id."').find('.migla_circle_text').html(parseInt(100 * progress) + '<i>%</i>');					 
				  }else if ( jQuery('#mg_circle_wrap".$id."').find('.migla_circle_inside').val() == 'percentage' )
				  {
					  jQuery('#mg_circle_wrap".$id."').find('.migla_circle_text').html(jQuery('#mg_circle_wrap".$id."').find('.migla_circle_percentage').val() + '<i>%</i>'); 
				  }
                                  
			  });";

        $output .= "			   setTimeout(function() { 
                                       jQuery('#mg_circle_".$id."').circleProgress('value', 0.7); 
                           }, 1000);
			   setTimeout(function() { jQuery('#mg_circle_".$id."').circleProgress('value', 1.0); }, 1100);
 			   setTimeout(function() { jQuery('#mg_circle_".$id."').circleProgress('value', jQuery('#mg_circle_wrap".$id."').find('.migla_circle_value').val()); }, 2100);";

        $output .= "}else{";


        $output .= "	jQuery('#mg_circle_".$id."').circleProgress({
				 value       : Number(jQuery('#mg_circle_wrap".$id."').find('.migla_circle_value').val()),
				 size        : jQuery('#mg_circle_wrap".$id."').find('.migla_circle_size').val(),
				 fill        : {
							   color: jQuery('#mg_circle_wrap".$id."').find('.migla_circle_fill').val()
					        },
				 lineCap     : jQuery('#mg_circle_wrap".$id."').find('.migla_circle_line_cap').val(),
				 startAngle  : _startangle".$id . ",
				 reverse     : _reverse".$id .",
				 thickness   : jQuery('#mg_circle_wrap".$id."').find('.migla_circle_thickness').val(),
                                 animation   : false
			  }).on('circle-animation-progress', function(event, progress, stepValue) 
                          {
				  if( jQuery('#mg_circle_wrap".$id."').find('.migla_circle_inside').val() == 'progress' )
				  {
                      jQuery('#mg_circle_wrap".$id."').find('.migla_circle_text').html(parseInt(100 * progress) + '<i>%</i>');					 
				  }else if ( jQuery('#mg_circle_wrap".$id."').find('.migla_circle_inside').val() == 'percentage' )
				  {
					  jQuery('#mg_circle_wrap".$id."').find('.migla_circle_text').html(jQuery('#mg_circle_wrap".$id."').find('.migla_circle_percentage').val() + '<i>%</i>'); 
				  }
                                 
			  });";

        $output .= "}";
		$output .= "jQuery('#mg_circle_wrap".$id."').find('.migla_circle_text').css('line-height', (jQuery('#mg_circle_wrap".$id."').find('.migla_circle_size').val()+'px'));";
		
		
		$output .= "var witdh".$id." = parseInt(jQuery('#mg_inpage_box_".$id."').css('width').replace('px', '')) ;";
		$output .= "var w_circle".$id." = parseInt(jQuery('#mg_circle_wrap".$id."').css('width').replace('px', ''))  ;";	
        $output .= "var w_html".$id." = ( ( ( ( witdh".$id." - w_circle".$id." ) / (1.5 * witdh".$id.") ) * 100 ) ) ;";		
		
		$output .= "jQuery('#mg_chtml_".$id."').css( 'width',  (w_html".$id."+'%') ) ;";
		
        $output .= "}); ";

        $output .= "</script>";

     return $output;
}


function migla_circle_js( $id )
{
        $output = '';
        $output .= "<script type='text/javascript'>";
        $output .= "jQuery(document).ready( function() { ";
        		
        $output .= "var _reverse".$id . " = false ;";
        $output .= "if( jQuery('#mg_circle_wrap".$id."').find('.migla_circle_reverse').val() == 'yes' ){ _reverse".$id. " = true ; } ";

        $output .= "var _startangle".$id . " = ( Number(jQuery('#mg_circle_wrap".$id."').find('.migla_circle_start_angle').val()) / 180) * 3.14 ;";

        $output .= "if( jQuery('#mg_circle_wrap".$id."').find('.migla_circle_animation').val() == 'normal' ){";    

        $output .= "	jQuery('#mg_circle_".$id."').circleProgress({
				 value       : Number(jQuery('#mg_circle_wrap".$id."').find('.migla_circle_value').val()),
				 size        : jQuery('#mg_circle_wrap".$id."').find('.migla_circle_size').val(),
				 fill        : {
							   color: jQuery('#mg_circle_wrap".$id."').find('.migla_circle_fill').val()
					        },
				 lineCap     : jQuery('#mg_circle_wrap".$id."').find('.migla_circle_line_cap').val(),
				 startAngle  : _startangle".$id . ",
				 reverse     : _reverse".$id . ",
				 thickness   : jQuery('#mg_circle_wrap".$id."').find('.migla_circle_thickness').val()
			  }).on('circle-animation-progress', function(event, progress, stepValue)  {
                                  
				  if( jQuery('#mg_circle_wrap".$id."').find('.migla_circle_inside').val() == 'progress' )
				  {
                      jQuery('#mg_circle_wrap".$id."').find('.migla_circle_text').html(parseInt(100 * progress) + '<i>%</i>');					 
				  }else if ( jQuery('#mg_circle_wrap".$id."').find('.migla_circle_inside').val() == 'percentage' )
				  {
					  jQuery('#mg_circle_wrap".$id."').find('.migla_circle_text').html(jQuery('#mg_circle_wrap".$id."').find('.migla_circle_percentage').val() + '<i>%</i>'); 
				  }
                                  
			  });";

        $output .= "}else if( jQuery('#mg_circle_wrap".$id."').find('.migla_circle_animation').val() == 'back_forth' ) {";

        $output .= "	jQuery('#mg_circle_".$id."').circleProgress({
				 value       : Number(jQuery('#mg_circle_wrap".$id."').find('.migla_circle_value').val()),
				 size        : jQuery('#mg_circle_wrap".$id."').find('.migla_circle_size').val(),
				 fill        : {
							   color: jQuery('#mg_circle_wrap".$id."').find('.migla_circle_fill').val()
					        },
				 lineCap     : jQuery('#mg_circle_wrap".$id."').find('.migla_circle_line_cap').val(),
				 startAngle  : _startangle".$id . ",
				 reverse     : _reverse".$id . ",
				 thickness   : jQuery('#mg_circle_wrap".$id."').find('.migla_circle_thickness').val()
			  }).on('circle-animation-progress', function(event, progress, stepValue) 
                          {
						  
				  if( jQuery('#mg_circle_wrap".$id."').find('.migla_circle_inside').val() == 'progress' )
				  {
                      jQuery('#mg_circle_wrap".$id."').find('.migla_circle_text').html(parseInt(100 * progress) + '<i>%</i>');					 
				  }else if ( jQuery('#mg_circle_wrap".$id."').find('.migla_circle_inside').val() == 'percentage' )
				  {
					  jQuery('#mg_circle_wrap".$id."').find('.migla_circle_text').html(jQuery('#mg_circle_wrap".$id."').find('.migla_circle_percentage').val() + '<i>%</i>'); 
				  }
                                  
			  });";

        $output .= "			   setTimeout(function() { 
                                       jQuery('#mg_circle_".$id."').circleProgress('value', 0.7); 
                           }, 1000);
			   setTimeout(function() { jQuery('#mg_circle_".$id."').circleProgress('value', 1.0); }, 1100);
 			   setTimeout(function() { jQuery('#mg_circle_".$id."').circleProgress('value', jQuery('#mg_circle_wrap".$id."').find('.migla_circle_value').val()); }, 2100);";

        $output .= "}else{";


        $output .= "	jQuery('#mg_circle_".$id."').circleProgress({
				 value       : Number(jQuery('#mg_circle_wrap".$id."').find('.migla_circle_value').val()),
				 size        : jQuery('#mg_circle_wrap".$id."').find('.migla_circle_size').val(),
				 fill        : {
							   color: jQuery('#mg_circle_wrap".$id."').find('.migla_circle_fill').val()
					        },
				 lineCap     : jQuery('#mg_circle_wrap".$id."').find('.migla_circle_line_cap').val(),
				 startAngle  : _startangle".$id . ",
				 reverse     : _reverse".$id .",
				 thickness   : jQuery('#mg_circle_wrap".$id."').find('.migla_circle_thickness').val(),
                                 animation   : false
			  }).on('circle-animation-progress', function(event, progress, stepValue) 
                          {
				  if( jQuery('#mg_circle_wrap".$id."').find('.migla_circle_inside').val() == 'progress' )
				  {
                      jQuery('#mg_circle_wrap".$id."').find('.migla_circle_text').html(parseInt(100 * progress) + '<i>%</i>');					 
				  }else if ( jQuery('#mg_circle_wrap".$id."').find('.migla_circle_inside').val() == 'percentage' )
				  {
					  jQuery('#mg_circle_wrap".$id."').find('.migla_circle_text').html(jQuery('#mg_circle_wrap".$id."').find('.migla_circle_percentage').val() + '<i>%</i>'); 
				  }
                                 
			  });";

        $output .= "}";

        $output .= "}); ";

        $output .= "</script>";

     return $output;
}

function migla_text_circle_widget( $cname, $posttype , $linkbtn, $btntext, $text , $id, $align,
   $info1, $info2, $info3, $fontsize , $circle )
{
	$the_outputs 	= array();
	$output			= '';
	$percent 		= 0;
	$percentStr		= '';
	$target_amount 	= 0;
    $target 		= 0; 
  	$total_amount 	= 0; 
	$total 			= 0;
	$donors 		= 0;
	
    $totals = migla_get_totals( $cname, $posttype );
	$total	= $totals[0];
	$donors	= $totals[1];
    $target = migla_get_campaign_target( $cname );

    if(  $target != 0 )
	{	
		if( $total == 0 )
		{
			  $percent = 0;	
		}else if( $target != 0 ) {
			  $percent = number_format(  ( $total / $target ) * 100 , 2);		
		}

			$op = get_option('migla_progbar_info'); 

			$symbol = getCurrencySymbol2();
			$x = array();
			$x[0] = get_option('migla_thousandSep');
			$x[1] = get_option('migla_decimalSep');
			$before = ''; $after = '';

			if( strtolower(get_option('migla_curplacement')) == 'before' ){
			  $before = $symbol;
			}else{
			  $after = $symbol;		
			}
			
			$showSep = get_option('migla_showDecimalSep');
			$decSep = 0;
			if( strcmp($showSep , "yes") == 0 ){ $decSep = 2; }

			$total_amount 	= $before. number_format( $total , $decSep, $x[1], $x[0]). $after;
			$target_amount 	= $before. number_format( $target , $decSep, $x[1], $x[0]) .$after;
			$percentStr		= $percent . "%";

			//codes [target] [total] [percentage] [campaign]
			$cname2 = str_replace("[q]", "'", $cname);
			$output = "";
			$output .= "<div class='bootstrap-wrapper'>";
			 
			$output .= migla_circle_js($id);

			$output .= "<div class='mg_circle-text-wrapper' ";
			$output .= ">";
			$output .= "<div class='migla_circle_wrapper' id='mg_circle_wrap".$id."' ";
			if( $align == 'left_right' || $align == 'left_left' )
			{
				$output .= "style='float:right !important'";
			}else if( $align == 'right_left' || $align == 'right_right' )
			{
				 $output .= "style='float:left !important'";
			}else{
				 $output .= "style='float:none !important'";
			}
			$output .= ">";
			$output .= "<div id='mg_circle_" . $id . "' class='migla_circle_bar' >";
			$output .= "<span class='migla_circle_text' style='line-height:".($circle['size'])."px; font-size:".$fontsize."px;' ></span></div>";

			//Circle
			$output .= "<input type='hidden' class='migla_circle_id' value='".$id."' >";
			$output .= "<input type='hidden' class='migla_circle_value' value='". ($total/$target) ."' >";
			$output .= "<input type='hidden' class='migla_circle_percentage' value='". $percent ."' >";

			  $keys = array_keys( $circle);
			  foreach( $keys as $key  )
			  {   
				   $output .= "<input type='hidden' class='migla_circle_" . $key. "' value='" .$circle[$key]. "'>";
			 }
			$output .= "</div>";

			if( $text == 'yes'  )
			{

			  $output .= "<div class='mg_text-barometer' ";
			  if( $align == 'left_right' ){
				  $output .= "style='float:left !important;text-align:right !important'";
			  }else if( $align == 'right_left' ){
				  $output .= "style='float:right !important;text-align:left !important'";
			  }else if( $align == 'left_left' ){
				  $output .= "style='float:left !important;text-align:left !important'";	
			  }else if( $align == 'right_right' ){
				  $output .= "style='float:right !important;text-align:right !important'";				  
			  }else{
				  $output .= "";
				}

			  $output .= ">";
			 
			  $output .= "<ul>
						  <li class='mg_campaign-raised'>
						  <span class='mg_current'>".$info1."</span> 
						  <span class='mg_current-amount'>".$total_amount."</span>
						  </li>
						  <li class='mg_campaign-goal'>
						  <span class='mg_target'>".$info2."</span>
						  <span class='mg_target-amount'>".$target_amount."</span>  
						  </li>
						  <li class='mg_campaign-backers'>
						  <span class='mg_backers'>".$info3."</span>
						  <span class='mg_backers-amount'>".$donors."</span>  
						 </li>  
					  </ul>
				   </div>";
			 $output.= '</div></div>';

			}else{
			  $output.= '</div></div>';
			} 
// try here
    }else{
        //$output = "</div>";
    }
	

	$the_outputs[0]	= $output;
	$the_outputs[1]	= $total_amount;
	$the_outputs[2]	= $target_amount;
	$the_outputs[3]	= $cname;
	$the_outputs[4]	= $donors;
	$the_outputs[5]	= $percentStr;
	
    return $the_outputs;
}


/************************************************************************
  TARGET, TOTAL Campaign
*************************************************************************/

function  migla_get_target( $campaign ){
	$campaignArray = get_option( 'migla_campaign' );
	$t = 0;
	foreach( (Array) $campaignArray as $key => $value){
	  if ( $campaignArray[$key]['name'] == $campaign )
	  {
	    $t = $campaignArray[$key]['target']; 
	    break;
	  }
	}	
	return $t;
}

function migla_get_total_amount($campaign) 
{
 global $wpdb;
 $postIDs = array();
 $postIDs = $wpdb->get_results(" SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_value = '".$campaign."'");

$t = 0;
foreach ( $postIDs as $id ){
 $t = $t + get_post_meta( intval( $id->post_id ) , 'miglad_amount', true);
}

return $t;

}

function migla_number_and_total($campaign){
 global $wpdb;
 $postIDs = array();
 $postIDs = $wpdb->get_results( 
	$wpdb->prepare( 
		"
			SELECT post_id
			FROM {$wpdb->prefix}postmeta
			WHERE meta_value = %s
			AND meta_key = %s
		",
	        $campaign,'migla_campaign' 
        )
 ); 


 $t = 0;
   foreach ( $postIDs as $id ){
   $t = $t + get_post_meta( intval( $id->post_id ) , 'migla_amount', true);
 }

 $arrOut = array();
 $arrOut[0] = count( $postIDs ); //number of records
 $arrOut[1] = $t; //total amount

  return $arrOut;
} //migla_number_and_total



/************************************************************************
  Create POST ONLINE AND OFFLINE
*************************************************************************/

function migla_create_post() 
{

$new_donation = array(
	'post_title' => 'migla_donation',
	'post_content' => '',
	'post_status' => 'publish',
	'post_author' => 1,
	'post_type' => 'migla_donation'
);

$new_id = wp_insert_post( $new_donation );

return $new_id;

}

function migla_create_offpost() 
{

$new_donation = array(
	'post_title' => 'migla_offlinedonation',
	'post_content' => '',
	'post_status' => 'publish',
	'post_author' => 1,
	'post_type' => 'migla_odonation'
);

$new_id = wp_insert_post( $new_donation );

return $new_id;

}

/************************************************************************
  GET POST ID
*************************************************************************/
function migla_get_ids_campaign( $campaign ) 
{
 
 global $wpdb;
 $postIDs = array();
 $postIDs = $wpdb->get_results( 
	$wpdb->prepare( 
		"SELECT pm.post_id,pm.meta_value FROM {$wpdb->prefix}postmeta pm
        where pm.post_id in ( 
	SELECT {$wpdb->prefix}posts.ID FROM {$wpdb->prefix}posts
	INNER JOIN {$wpdb->prefix}postmeta ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
	WHERE post_type = %s
	AND meta_key = %s
	AND meta_value = %s
        )
        AND pm.meta_key = %s
        ORDER BY STR_TO_DATE( pm.meta_value, %s) DESC" ,
	         'migla_donation','migla_campaign', $campaign, 'migla_date','%m/%d/%Y'
        )
 ); 

  return $postIDs;
}

function migla_get_ids_all() 
{
 
 global $wpdb;
 $postIDs = array();
 $postIDs =  $wpdb->get_results( 
	$wpdb->prepare( 
	"SELECT ID, meta_value, meta_key FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
	 WHERE post_type = %s and meta_key = %s
         ORDER BY STR_TO_DATE( meta_value, %s) DESC
        " ,
	   'migla_donation','miglad_date','%m/%d/%Y'
        )
 );

  return $postIDs;
}

function migla_get_oflineids_all() 
{
 
 global $wpdb;
 $postIDs = array();
 $postIDs =  $wpdb->get_results( 
	$wpdb->prepare( 
	"SELECT ID, meta_value, meta_key FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
	 WHERE post_type = %s and meta_key = %s
         ORDER BY STR_TO_DATE( meta_value, %s) DESC
        " ,
	   'migla_odonation','miglad_date','%m/%d/%Y'
        )
 ); 

  return $postIDs;
}

/************************************************************/
/*           DATA RETRIEVING FOR REPORT  FINISH Nov 23st */
/**********************************************************/
function migla_get_id_range( $start, $end ) 
{
 global $wpdb;
 $postIDs = array();

 $postIDs = $wpdb->get_results( 
	$wpdb->prepare( 
	"SELECT ID, meta_value, meta_key FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
	 WHERE post_type = %s and meta_key = %s
         ORDER BY STR_TO_DATE( meta_value, %s) DESC
        " ,
	   'migla_donation','miglad_date','%m/%d/%Y'
        )
 ); 

  return $postIDs;
}



/***********************************************************************************************************/
/**							OFFLINE REPORT																***/
/***********************************************************************************************************/

function migla_get_ofids_all() 
{
 
 global $wpdb;
 $postIDs = array();
 $postIDs = $wpdb->get_results( 
	$wpdb->prepare( 
	"SELECT ID FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
	 WHERE post_type = %s and meta_key = %s
         ORDER BY STR_TO_DATE( meta_value, %s) DESC
        " ,
	   'migla_odonation','miglad_date','%m/%d/%Y'
        ), ARRAY_N 
 ); 

  return $postIDs;
}


function migla_remove_donation($str) 
{
	 global $wpdb;
	 $wpdb->query( "DELETE FROM {$wpdb->prefix}posts where ID in" . $str);
	 $wpdb->query( "DELETE FROM {$wpdb->prefix}postmeta where post_id in ". $str );       
}


/*******************************************************************************************/
/*************** CURRENT PAGE URL ********************************/
/*******************************************************************************************/
function migla_current_page_url() {
	$pageURL = 'http';
	if( isset($_SERVER["HTTPS"]) ) {
		if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

/*******************************************************************************************/
/*************** DONATION WIDGETS ********************************/
/*******************************************************************************************/
function miglaCurrencySymbol()
{
   $code = (string)get_option(  'migla_default_currency'  );
   $arr = get_option( 'migla_currencies' ); 
   $icon ='';
   foreach ( $arr as $key => $value ) {
     if(  strcmp( $code, $arr[$key]['code'] ) == 0  ){
       $icon = $arr[$key]['symbol']; 
       break;
     }
   }
   return $icon;
}

function migla_donor_recent($type, $num, $show_anon, $campaign, $show_honoree ){
 global $wpdb;
 $data = array();

	 if( $type == "" ){
	  $data = $wpdb->get_results( 
		$wpdb->prepare( 
			 "SELECT DISTINCT {$wpdb->prefix}posts.ID, {$wpdb->prefix}posts.post_type FROM {$wpdb->prefix}posts 
			  INNER JOIN {$wpdb->prefix}postmeta
			  ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
		      WHERE {$wpdb->prefix}posts.post_type like %s 
			  ORDER BY post_date 
			  " 
			  , 'migla%donation%'
			)
		 );
	 }else{
	  $data = $wpdb->get_results( 
		$wpdb->prepare( 
			 "SELECT DISTINCT {$wpdb->prefix}posts.ID, {$wpdb->prefix}posts.post_type FROM {$wpdb->prefix}posts 
				INNER JOIN {$wpdb->prefix}postmeta
				ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
				WHERE {$wpdb->prefix}posts.post_type like %s and meta_key=%s
				ORDER BY STR_TO_DATE( meta_value, %s ) DESC
			  " 
			  , $type, 'miglad_date', '%m/%d/%Y'
			)
		 );
	 }

 $row 				= 0;
 $list 				= array(); 
 $date_time_order 	= array();
 $post_id_list		= array(); //avoid redudancy
 $post_id_list[$row] = 99;
 
 if( $campaign == 'show_all' || $campaign == '' )
 {
 	 foreach( $data as $id )
	 {
		   $anon   = get_post_meta( intval( $id->ID ) , 'miglad_anonymous', true);
		   $status = get_post_meta( intval( $id->ID ) , 'miglad_status', true);	 
		   $amount = get_post_meta( intval( $id->ID ) , 'miglad_amount', true);

			$date   	= get_post_meta( intval( $id->ID ) , 'miglad_date', true); 
			$time		= get_post_meta( intval( $id->ID ) , 'miglad_time', true); 		   
			$datetime	= strtotime($date.$time) . $id->ID;
			
		if( $amount == '0' || $amount == 0 || $amount == '')
		{
		
		}else{
			
		if( $status != 'pending' && !in_array( $id->ID , $post_id_list ) )
		{
			$post_id_list[$row]	=  $id->ID ;
		
			 if( strtolower($anon) != 'yes' )
			 {
				$honoreename = get_post_meta( intval( $id->ID ) , 'miglad_honoreename', true);
				if( $show_honoree == 'yes' && $honoreename != '' )
				{
					$list[$datetime]['firstname'] 	= $honoreename  ;
					$list[$datetime]['lastname'] 	= "";			  
				}else{
					$list[$datetime]['firstname'] 	= get_post_meta( intval( $id->ID ) , 'miglad_firstname', true);
					$list[$datetime]['lastname'] 	= get_post_meta( intval( $id->ID ) , 'miglad_lastname', true);
				}
				$list[$datetime]['amount'] 	= $amount;
				$list[$datetime]['date'] 	= $date;
				$list[$datetime]['time'] 	= $time; 
				$list[$datetime]['datetime'] = $datetime;				
				$list[$datetime]['type'] 	= $id->post_type;
				$list[$datetime]['post_id'] = $id->ID ;
				
				$date_time_order[$row] 		= $datetime;
				$row++;
			 }else{
			 
				if( $show_anon == 'yes' ){
				  if( $show_honoree == 'yes' && $honoreename != '')
				  {
					  $list[$datetime]['firstname'] 	= get_post_meta( intval( $id->ID ) , 'miglad_honoreename', true);
					  $list[$datetime]['lastname'] 		= "";			  
				  }else{
					  $list[$datetime]['firstname'] 	= "Anonymous" ;
					  $list[$datetime]['lastname'] 		= "";
				  }
				  $list[$datetime]['amount'] 	= $amount;
				  $list[$datetime]['date'] 		= get_post_meta( intval( $id->ID ) , 'miglad_date', true); 
				  $list[$datetime]['time'] 		= get_post_meta( intval( $id->ID ) , 'miglad_time', true); 
				  $list[$datetime]['datetime'] = $datetime;
				  $list[$datetime]['type'] 		= $id->post_type;
				  $list[$datetime]['post_id'] 	= $id->ID ;
				  
				  $date_time_order[$row] 		= $datetime;
				  $row++;
				}				
			 }//ELSE ANON
		   }//ELSE STATUS
		}
	 }//FOREACH

 }else{
	
	 foreach( $data as $id )
	 {
	   $_campaign      = get_post_meta( intval( $id->ID ) , 'miglad_campaign', true);
	   $campaign_clear = str_replace('{q}', '[q]', $campaign);
	   
	   if( $campaign_clear == $_campaign )
	   {
			$anon   	= get_post_meta( intval( $id->ID ) , 'miglad_anonymous', true);
			$status 	= get_post_meta( intval( $id->ID ) , 'miglad_status', true);
			$amount = get_post_meta( intval( $id->ID ) , 'miglad_amount', true);

			$date   	= get_post_meta( intval( $id->ID ) , 'miglad_date', true); 
			$time		= get_post_meta( intval( $id->ID ) , 'miglad_time', true); 		   
			$datetime	= strtotime($date.$time) . $id->ID;		
		
		if( $amount == '0' || $amount == 0 || $amount == '')
		{
		
		}else{
		
			if( $status != 'pending' && !in_array( $id->ID, $post_id_list) )
			{
				$post_id_list[$row]	=  $id->ID ;
				
			 if( strtolower($anon) != 'yes' ){
				$honoreename = get_post_meta( intval( $id->ID ) , 'miglad_honoreename', true);
				if( $show_honoree == 'yes' && $honoreename != '' )
				{
					$list[$datetime]['firstname'] 	= $honoreename;
					$list[$datetime]['lastname'] 	= "";			  
				}else{
					$list[$datetime]['firstname'] 	= get_post_meta( intval( $id->ID ) , 'miglad_firstname', true);
					$list[$datetime]['lastname'] 	= get_post_meta( intval( $id->ID ) , 'miglad_lastname', true);
				}
				$list[$datetime]['amount'] 	= $amount ;
				$list[$datetime]['date'] 	= $date;
				$list[$datetime]['time'] 	= $time;
				$list[$datetime]['datetime']	= $datetime;
				$list[$datetime]['type'] = $id->post_type;
				$list[$datetime]['post_id'] = $id->ID ;
				
				$date_time_order[$row] = $datetime ;				
				$row++;
			 }else{
			 
				if( $show_anon == 'yes' )
				{
				  if( $show_honoree == 'yes'  && $honoreename != '')
				  {
					  $list[$datetime]['firstname'] = get_post_meta( intval( $id->ID ) , 'miglad_honoreename', true);
					  $list[$datetime]['lastname'] = "";			  
				  }else{
					  $list[$datetime]['firstname'] = "Anonymous" ;
					  $list[$datetime]['lastname'] = "";
				  }
				  $list[$datetime]['amount'] = $amount ;
				  $list[$datetime]['date'] 	= $date;
				  $list[$datetime]['time'] 	= $time;
				  $list[$datetime]['datetime'] = $datetime;
				  $list[$datetime]['type'] = $id->post_type;
				  $list[$datetime]['post_id'] = $id->ID ;
					
				  $date_time_order[$row] = $datetime;
				  $row++;
				}
			 }//ELSE ANON

		   }//ELSE STATUS 
		} 

		}//IF CAMPAIGN
	   
	 }//foreach 
  }
  
  $output = array();
  $output[0] = $date_time_order;
  $output[1] = $list;
  $output[2] = $post_id_list;
  
  //usort($list, 'mgcompareTime');

 return $output;  
}

function mgcompareTime($a, $b)
{
   $first = strtotime( $a['date']." ".$a['time'] );
   $second = strtotime( $b['date']." ".$b['time'] );

     return  $second - $first;

}

function migla_donorwall_top( $type, $num, $show_anon,$campaign ){
 global $wpdb;
 $data = array();

 if( $type == "" ){
  $data = $wpdb->get_results( 
	$wpdb->prepare( 
         "SELECT DISTINCT {$wpdb->prefix}posts.ID FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta
          on {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
	  WHERE {$wpdb->prefix}posts.post_type like %s 
          ORDER BY ID
          " 
          , 'migla%donation%'
        )
     );
 }else{
  $data = $wpdb->get_results( 
	$wpdb->prepare( 
         "SELECT DISTINCT {$wpdb->prefix}posts.ID FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta
          on {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
	  WHERE {$wpdb->prefix}posts.post_type = %s 
          " 
          , $type
        )
     );
 }


 $row = -1; $count_anon = 0;
 $list = array(); $name = array(); $n = array();
 
 if( $campaign == 'show_all' || $campaign == '' )
 {
	 foreach( $data as $id )
	 {
	   $status = get_post_meta( intval( $id->ID ) , 'miglad_status', true);
	   if( $status != 'pending' )
	   {

		 $f = get_post_meta( intval( $id->ID ) , 'miglad_firstname', true);
		 $l = get_post_meta( intval( $id->ID ) , 'miglad_lastname', true);
		 $theirname = $f . $l ;
		 $theirname = strtolower( $theirname );
		 $theirname = str_replace(" ", "", $theirname);

		 //cek new one
		 if(  in_array( $theirname , $n, true )  )
		 {

		   $anon = get_post_meta( intval( $id->ID ) , 'miglad_anonymous', true);
		   if( strtolower($anon) != 'yes' ){
			  $index = array_search( $theirname , $n, true );
			  $name[ $index ]['total'] = $name[ $index ]['total'] + floatval( get_post_meta( intval( $id->ID ) , 'miglad_amount', true) );
		   }else{
			  if( $show_anon == 'yes' ){
				 $index = array_search( $theirname , $n, true );
				 $name[ $index ]['total'] = $name[ $index ]['total'] + floatval( get_post_meta( intval( $id->ID ) , 'miglad_amount', true) );        
			  }
		   }

		}else{

		   $anon = get_post_meta( intval( $id->ID ) , 'miglad_anonymous', true);
		   if( strtolower($anon) != 'yes'  ){
			   $row++;
			   $n[$row] =  $theirname;
			   $name[$row]['total'] =  floatval ( get_post_meta( intval( $id->ID ) , 'miglad_amount', true) );
					   $name[$row]['name'] = $theirname;
			   $name[$row]['firstname'] = $f;
			   $name[$row]['lastname'] = $l;
		  }else{
			   if( $show_anon == 'yes' ){
			   $row++; $count_anon++;
			   $n[$row] =  $theirname;
			   $name[$row]['total'] =  floatval ( get_post_meta( intval( $id->ID ) , 'miglad_amount', true) );
					   $name[$row]['name'] = $theirname;
			   $name[$row]['firstname'] = 'Anonymous';
			   $name[$row]['lastname'] = $count_anon;
			   }
		  }

		} //look for them in Array

	   }//IF STATUS

	 } //foreach
 
 }else{
    
 	 foreach( $data as $id )
	 {
		$_campaign      = get_post_meta( intval( $id->ID ) , 'miglad_campaign', true);
		$campaign_clear = str_replace('{q}', '[q]', $campaign);
	    if( $campaign_clear ==  $_campaign )
	    {
		   $status = get_post_meta( intval( $id->ID ) , 'miglad_status', true);
		   if( $status != 'pending' )
		   {

			 $f = get_post_meta( intval( $id->ID ) , 'miglad_firstname', true);
			 $l = get_post_meta( intval( $id->ID ) , 'miglad_lastname', true);
			 $theirname = $f . $l ;
			 $theirname = strtolower( $theirname );
			 $theirname = str_replace(" ", "", $theirname);

			 //cek new one
			 if(  in_array( $theirname , $n, true )  )
			 {

			   $anon = get_post_meta( intval( $id->ID ) , 'miglad_anonymous', true);
			   if( strtolower($anon) != 'yes' ){
				  $index = array_search( $theirname , $n, true );
				  $name[ $index ]['total'] = $name[ $index ]['total'] + floatval( get_post_meta( intval( $id->ID ) , 'miglad_amount', true) );
			   }else{
				  if( $show_anon == 'yes' ){
					 $index = array_search( $theirname , $n, true );
					 $name[ $index ]['total'] = $name[ $index ]['total'] + floatval( get_post_meta( intval( $id->ID ) , 'miglad_amount', true) );        
				  }
			   }

			}else{

			   $anon = get_post_meta( intval( $id->ID ) , 'miglad_anonymous', true);
			   if( strtolower($anon) != 'yes'  ){
				   $row++;
				   $n[$row] =  $theirname;
				   $name[$row]['total'] =  floatval ( get_post_meta( intval( $id->ID ) , 'miglad_amount', true) );
						   $name[$row]['name'] = $theirname;
				   $name[$row]['firstname'] = $f;
				   $name[$row]['lastname'] = $l;
			  }else{
				   if( $show_anon == 'yes' ){
				   $row++; $count_anon++;
				   $n[$row] =  $theirname;
				   $name[$row]['total'] =  floatval ( get_post_meta( intval( $id->ID ) , 'miglad_amount', true) );
						   $name[$row]['name'] = $theirname;
				   $name[$row]['firstname'] = 'Anonymous';
				   $name[$row]['lastname'] = $count_anon;
				   }
			  }
			} //look for them in Array
		   }//IF STATUS
		}//IF Campaign
	 } //foreach

 }
	 
 usort($name, 'mgcompareOrder');

 return $name; 
}

function mgcompareOrder($a, $b)
{
  return $b['total'] - $a['total'];
}



/********************* Top Donors & Recent Donations Shortcodes **********************************************/

function mg_draw_topdonors( $title, $num_rec, $donation_type, $use_link, $btn_class, $btn_style, 
   $btn_text, $urlLink, $show_anon, $campaign )
{
    $out = "";

    $out .= "<h3 class='top_donors_title'>";
    $out .= $title. "<br>";
    $out .= "</h3>";
    
     $posttype = 'migla_donation';
     if( $donation_type == 'offline' ){ $posttype = 'migla_odonation'; }
     if( $donation_type == 'both' ){ $posttype = ''; }

      $symbol = miglaCurrencySymbol();
      $b = ""; $a = "";
      $showdec = get_option('migla_showDecimalSep'); $dec = 0;
      if( $showdec == 'yes' ){ $dec = 2; }
      if( get_option('migla_curplacement') == 'before' ){ $b = $symbol; }else{ $a = $symbol; }
      $thousep = get_option('migla_thousandSep'); $decsep = get_option('migla_decimalSep');
      $data = array();

    if( $use_link == 'yes' ){ $BtnExisted = 'mg_widgetButton'; }else{ $BtnExisted = ''; }
      
  
      $data = migla_donorwall_top($posttype, $num_rec, $show_anon, $campaign);

      $i = 0;

      $out .= "<ol class='mg_top_donors ".$BtnExisted."'>";
      foreach( (array)$data as $datum ){

          $out .= "<li>" ;
          $out .= "<span class='mg_top_donors_name'>". $datum['firstname'] ."&nbsp;". $datum['lastname'] . " </span>"; 
          $out .= "<span class='mg_top_donors_amount'>".$b.number_format( (float)$datum['total'], $dec , $decsep, $thousep ) .$a. " </span>";
          $out .= "</li>"; 
          $i++;
          if( $i == $num_rec ){ break; }

      }

     $out .= "</ol>";
     $out .= "<br>";
      
     $class2 = "";
     if( $btn_style == 'grey_button' ){  $class2 = ' mg-btn-grey';	  }	  
	
      if( $use_link=='yes' ){
        if( $urlLink == '' || $urlLink == false ){ $urlLink = get_option('migla_form_url');  }

        $out .= "<form action='".esc_url( $urlLink)."' method='post'>";
          if( $btn_text == '' ){ $btn_text = 'Donate'; }
        $out .= "<input type='hidden' name='thanks' value='widget_bar' />";
        $out .= "<button class='migla_donate_now ".$btn_class . $class2."'>".$btn_text."</button>";
        $out .= "</form>";
      }

   return $out;   
}


function migla_draw_donor_recent( $title, $num_rec, $donation_type, $use_link, $btn_class, $btn_style, 
  $btn_text , $language, $url_link, $show_anon, $campaign, $show_honoree )
{
     $out = ""; 
  
    $out .= "<h3 class='mg-recent-donors-title'>";
    $out .= $title. "<br>";
    $out .= "</h3>";
    
     $posttype = 'migla_donation';
     if( $donation_type == 'offline' ){ $posttype = 'migla_odonation'; }
     if( $donation_type == 'both' ){ $posttype = ''; }

      $symbol = miglaCurrencySymbol();
      $b = ""; $a = "";
      $showdec = get_option('migla_showDecimalSep'); $dec = 0;
      if( $showdec == 'yes' ){ $dec = 2; }
      if( get_option('migla_curplacement') == 'before' ){ $b = $symbol; }else{ $a = $symbol; }
      $thousep = get_option('migla_thousandSep'); $decsep = get_option('migla_decimalSep');
      $data = array();

    if( $use_link == 'yes' ){ $BtnExisted = 'mg_widgetButton'; }else{ $BtnExisted = ''; }
      
      $get_list = (array)migla_donor_recent($posttype, $num_rec, $show_anon, $campaign, $show_honoree );
	  $order 	= (array)$get_list[0];
	  $data 	= (array)$get_list[1];	  
	 
      $row = 1; 
      $out .= "<div class='bootstrap-wrapper mg_latest_donations_widget ".$BtnExisted."'><div class='mg_donations_wrap'> ";
      
      $df = array('%B %d %Y', '%b %d %Y', '%B %d, %Y', '%b %d, %Y' , '%d %B %Y', '%d %b %Y' ,'%Y-%m-%d', '%m/%d/%Y');
      $date_format = $df[0];

      $my_locale = get_locale();
      if( $language == "" ){
        $language = $my_locale;
      }
      setlocale(LC_TIME, $language );
	  			
		rsort($order);	
		
      foreach( $order as $order_datum )
	  {
        if( $row > $num_rec ){ 
			break; 
		}
  
         $out .= "<section class='mg_recent_donors_Panel'>";

		 $hide_date 	= "";
		 
         if($hide_date == 'on')
		 {
               $out .= "<div class='mg_recent_donors_date'></div> ";
         }else{
              $out .= "<div class='mg_recent_donors_date'>".strftime( $date_format , date(strtotime($data[$order_datum]['date'])) )."</div> ";
         }

         $out .= "<div class='mg_recent_donors_amount'>".$b.number_format( (float)$data[$order_datum]['amount'], $dec , $decsep, $thousep ) .$a. "</div>";

         $out .= "<div class='mg_recent_donors_name'>". $data[$order_datum]['firstname']. "&nbsp;" . $data[$order_datum]['lastname']  . "</div>";

         $out .=  "</section>";
         $row++;
      }

      $out .= "</div></div>";

      setlocale(LC_TIME, $my_locale );

     $class2 = "";
     if( $btn_style == 'grey_btn' ){  $class2 = ' mg-btn-grey';	  }	  
	
      if( $use_link == 'yes' ){
        if( $url_link == '' || $url_link == false ){   $url_link = get_option( 'migla_form_url' ); }
        $out .= "<form action='".esc_url($url_link)."' method='post'>";
          if( $btn_text == '' ){ $btn_text = 'Donate'; }
        $out .= "<input type='hidden' name='thanks' value='widget_bar' />";
        $out .= "<button class='migla_donate_now ".$btn_class . $class2."'>".$btn_text."</button>";

        $out .= "</form>";
      }		

   return $out;
}

function sendNotifEmailRepeating( $id, $e, $en, $ne)
{
	$postData = array();
	$postData['miglad_campaign']	= get_post_meta( $id, 'miglad_campaign', true );
	$postData['miglad_amount']	= get_post_meta( $id, 'miglad_amount', true );
	$postData['miglad_firstname']	= get_post_meta( $id, 'miglad_firstname', true ) ;
	$postData['miglad_lastname']	= get_post_meta( $id, 'miglad_lastname', true );
	$postData['miglad_address']	= get_post_meta( $id, 'miglad_address', true ) ;
	$postData['miglad_country']	= get_post_meta( $id, 'miglad_country', true );
	$postData['miglad_state']	= get_post_meta( $id, 'miglad_state', true );
	$postData['miglad_province']	= get_post_meta( $id, 'miglad_province', true );
	$postData['miglad_postalcode']	= get_post_meta( $id, 'miglad_postalcode', true );

	mg_send_notification_emails( $postData, $e, $en , $ne );
	 
} 

function sendThankYouEmailRepeating( $id, $e, $en )
{
	$postData = array();
	$postData['miglad_campaign']	= get_post_meta( $id, 'miglad_campaign', true );
	$postData['miglad_amount']	= get_post_meta( $id, 'miglad_amount', true );
	$postData['miglad_firstname']	= get_post_meta( $id, 'miglad_firstname', true ) ;
	$postData['miglad_lastname']	= get_post_meta( $id, 'miglad_lastname', true );
	$postData['miglad_address']	= get_post_meta( $id, 'miglad_address', true ) ;
	$postData['miglad_country']	= get_post_meta( $id, 'miglad_country', true );
	$postData['miglad_state']	= get_post_meta( $id, 'miglad_state', true );
	$postData['miglad_province']	= get_post_meta( $id, 'miglad_province', true );
	$postData['miglad_postalcode']	= get_post_meta( $id, 'miglad_postalcode', true );

	mg_send_thank_you_email( $postData, $e, $en ); 
} 

?>