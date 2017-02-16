<?php
class migla_paypal_settings_class {

	function __construct() {
		add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 12 );
	}
	
	function menu_item() {
		add_submenu_page(
			'migla_donation_menu_page',
			__( 'Paypal Settings', 'migla-donation' ),
			__( 'Paypal Settings', 'migla-donation' ),
			$this->get_capability() ,
			'migla_donation_paypal_settings_page',
			array( $this, 'menu_page' )
		);
	}

  static function get_capability()
  {
	/** SHOW CURRENT USER **/
	global $current_user;
	$curr_caps			= $current_user->caps;
	$curr_caps_key 		= array_keys($curr_caps);
	$cur_is_allowed		= false;
	$allowed_cap_curr	= 'administrator';
	$ok_found			= false;
	$get_allowed_caps	= (array)get_option( 'migla_allowed_capabilities' );
	$list				= (array)get_option('migla_allowed_users');
	
	if( in_array( $current_user->ID , $list ) )
	{
		$cur_is_allowed = true;
		for( $k = 0 ; $k < count($curr_caps_key) && !$ok_found ; $k++ )
		{
			if( $curr_caps[$curr_caps_key[$k]] == '1' || $curr_caps[$curr_caps_key[$k]] == true )
			{
				if( in_array( $curr_caps_key[$k] , $get_allowed_caps ) )
				{
					$allowed_cap_curr = $curr_caps_key[$k];
					$ok_found = true;
				}
			}
		}//for
	}
        return $allowed_cap_curr ;
   }
	
	

    function writeme( $str ){
		$result =  str_replace( "//" , "/" , $str );
		$result =  str_replace( "[q]" , "'" , $result );
		return $result;
    }
	
    function get_all_posts(){
        global $wpdb;
        $post_obj = array();
        $post_obj = $wpdb->get_results( 
						$wpdb->prepare( 
						"SELECT ID,post_title  FROM {$wpdb->prefix}posts WHERE post_type = %s" ,
						'page'
                        ) 
					); 

		$post_array = array();	
		$i 			= 0;	   
				   
        foreach( $post_obj as $post )
	    {
            $post_array[$i]['id'] 		= $post->ID;
            $post_array[$i]['title'] 	= $post->post_title;
			$i++;
        }	   
       
       return $post_array ; 	   
    }	

	function menu_page() {

	// Validate user
    $users 			= (array)get_option('migla_allowed_users');
	$has_privilege	= in_array( get_current_user_id(), $users );
	if ( $has_privilege || current_user_can( 'manage_options' ) ) 
	{
	}else{
		$error = "<div class='wrap'><div class='container-fluid'>";
        $error .= "<h2 class='migla'>";
		$error .= __("You do not have sufficient permissions to access this page. Please contact your web administrator","migla-donation"). "</h2>";
		$error .= "</div></div>";

	    wp_die( __( $error , 'migla-donation' ) );
	}		
		
		echo "<div class='wrap'><div class='container-fluid'>";   
                echo "<h2 class='migla'>". __("Paypal Settings","migla-donation")."</h2>";

		echo "<div class='row form-horizontal'>";

$payment['sandbox'] = '';  $payment['paypal'] = '';
		$paymentMethod = get_option( 'migla_payment' ) ;
		$payment[ $paymentMethod ] = 'checked';		
        $pEmail = get_option( 'migla_paypal_emails' ) ;
        $pEmailName = get_option( 'migla_paypal_emailsname' ) ;


echo "<div class='col-sm-12'>";
		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseOne' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa fa-paypal'></i>". __("PayPal Account Settings","migla-donation"). "</h2></header>";
		echo "<div id='collapseOne' class='panel-body collapse in'>";


/***** The order of Gateways *******************/

$order_of_gateways = get_option('migla_gateways_order');
echo "<div class='row'><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>". __("Choose the gateway(s) you'd like to use and their order:","migla-donation"). "</label></div>";

echo "<div class='col-sm-6 col-xs-12' id='default_payment_section'>";
if( $order_of_gateways == 'false' || $order_of_gateways[0] == '' )
{
   $order_of_gateways[0] = array( 'paypal' , false  );
   $order_of_gateways[1] = array( 'stripe' , false );
   $order_of_gateways[2] = array( 'authorize' , false );
   $order_of_gateways[3] = array( 'offline' , false  );

   echo "<ul class='containers' >";
   foreach( (array)$order_of_gateways as $value )
   {
       $name = ucfirst( $value[0] );
       if( $name == 'Authorize' ){
          $name = 'Authorize.net';
       }

		echo "<li class='ui-state-default formfield'>";
            if( $value[1] == 'true' || $value[1] == 1 )
            {
		echo "<div class='row'><div class='col-sm-6'><div class='row'><div class='col-sm-12'><input type='checkbox' class='mg_status_gateways' value='".$value[0]."' checked> " . $name . "</div></div></div>";
		echo "</div>";
             }else{
		echo "<div class='row'><div class='col-sm-6'><div class='row'><div class='col-sm-12'><input type='checkbox' class='mg_status_gateways' value='".$value[0]."'> " . $name . "</div></div></div>";
		echo "</div>";
             }
		echo "</li>";
   }
   echo "</ul>";

}else{
   echo "<ul class='containers' >";
   foreach( (array)$order_of_gateways as $value )
   {

       $name = ucfirst( $value[0] );
       if( $name == 'Authorize' ){
          $name = 'Authorize.net';
       }

	    echo "<li class='ui-state-default formfield'>";
            if( $value[1] == 'true' || $value[1] == 1 )
            {
		echo "<div class='row'><div class='col-sm-6'><div class='row'><div class='col-sm-12'><input type='checkbox' class='mg_status_gateways' value='".$value[0]."' checked> " . $name . "</div></div></div>";
		echo "</div>";
             }else{
		echo "<div class='row'><div class='col-sm-6'><div class='row'><div class='col-sm-12'><input type='checkbox' class='mg_status_gateways' value='".$value[0]."'> " . $name . "</div></div></div>";
		echo "</div>";
             }
	    echo "</li>";
   }
   echo "</ul>";
}

echo "</div></div>";



		echo "<div class='row'><div class='col-sm-3'><label for='mg_paypal_method' class='control-label text-right-sm text-center-xs'>". __("Paypal Method","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";

  $isPDT = get_option('migla_using_pdt');
  if( $isPDT == false )
  { 
     add_option('migla_using_pdt', 'no') ;
     $isPDT = get_option('migla_using_pdt'); 
  }

  $paypal_ = get_option('migla_paypal_method');
  $the_method['standar'] = ''; $the_method['pdt'] = ''; $the_method['pro_standard'] = ''; 
  $the_method['pro_pdt'] = ''; $the_method['pro_only'] = '';

  if(  $paypal_ == false && !$isPDT )
  {
      $the_method['standar'] = 'selected';
  }else{
      $the_method[ $paypal_ ] = 'selected'; 
  }

    echo "<select id='mg_paypal_method'>";
    echo "<option value='standar' ".$the_method['standar']." >". __("Paypal Standard / Paypal Data Tansfer (PDT)","migla-donation"). " </option>";
    echo "<option value='pro_standard' ".$the_method['pro_standard']." >". __("Paypal Pro and Paypal Standard / Paypal Data Tansfer (PDT)","migla-donation"). "</option>";
    echo "<option value='pro_only' ".$the_method['pro_only'].">". __("Paypal Pro","migla-donation"). "</option>";
    echo "</select>";
    echo "</div></div>";

  $cc_label = get_option('migla_paypalpro_cc_info');
  if( $cc_label == false )
  {
     $cc_label = array();
     $cc_label[0][1] = 'Pay with Credit Card';     //0 
     $cc_label[1][1] = 'Pay with Credit Card';     //1 
     $cc_label[2][1] = 'Pay with PayPal account';  //2 
     $cc_label[3][1] = 'Your Name';             //3
     $cc_label[4][1] = 'First Name'; //4
     $cc_label[5][1] = 'Your Name';            //5
     $cc_label[6][1] = 'Last Name'; //6
     $cc_label[7][1] = 'Card Number';            //7
     $cc_label[8][1] = 'No Dashes';        //8
     $cc_label[9][1] = 'Expiration//CVC';           //9
     $cc_label[10][1] = 'CVC';                 //10

     add_option('migla_paypalpro_cc_info', $cc_label );  

  }else{
     $cc_label = $cc_label;
  }

echo "<div class='row'> <div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs' for='mg_tab-paypalpro'>".__("Tab Name","migla-donation")."</label></div> <div class='col-sm-6 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_tab-paypalpro" id="mg_tab-paypalpro" class="form-control" placeholder="Tab Name" value="'.$this->writeme($cc_label[0][1]).'">';
echo "</div></div>";

		
		echo "<div class='row'><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div><div class='col-sm-6'><button id='miglaUpdatePaypalSettings' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation"). "</button></div>";

		echo "</div>";

echo "</div></section></div>";

/****************************************/

 $isPDT 	= get_option('migla_using_pdt');
 $std_show 	= '';
 $pdt_show 	= '';

echo "<div class='col-sm-12'>";
		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseTwo' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa fa-paypal'></i>". __("PayPal Standard/PDT Settings","migla-donation"). "</h2></header>";
		echo "<div id='collapseTwo' class='panel-body collapse in'>";
	
echo "<div class='row'><div class='col-sm-3'><label for='migla_paypal_std_pdt_choice' class='control-label text-right-sm text-center-xs'>". __("Choose which method you want to use:","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<select id='migla_paypal_std_pdt_choice'>";
if( $isPDT == 'yes' )
{
	echo "<option value='std'>PayPal Standard</option>";
	echo "<option value='pdt' selected>PayPal PDT</option>";
	$std_show 	= 'display:none';
	$pdt_show 	= '';	
}else{
	echo "<option value='std' selected>PayPal Standard</option>";
	echo "<option value='pdt'>PayPal PDT</option>";	
	$std_show 	= '';
	$pdt_show 	= 'display:none';		
}
echo "</select>";
echo "</div>";
echo "</div>";	
	
		echo "<div class='row'><div class='col-sm-3'><label for='miglaPaypalEmails' class='control-label text-right-sm text-center-xs'>". __("Business Email","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<input type='text' id='miglaPaypalEmails' value='".$pEmail."' class='form-control'></div>";
 
echo "<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>". __("The PayPal address you use for accepting donations. ","migla-donation"). "</span>
</div>";


$ipn = get_option('migla_ipn_choice');
 if( $ipn == false )
 { 
    add_option('migla_ipn_choice', 'back') ;
    $ipn = get_option('migla_ipn_choice'); 
 }

		echo "<div class='row' style='".$std_show."'><div class='col-sm-3'><label for='migla_ipn_choice' class='control-label text-right-sm text-center-xs'>". __("Use the IPN listener on the frontend ?","migla-donation"). "</label></div><div class='col-sm-9 col-xs-12'>";
if( $ipn == 'front' ){
  echo "<label  class='checkbox-inline' for='migla_ipn_choice'><input type='checkbox' id='migla_ipn_choice' name='migla_ipn_choice' class='' checked><em style='color:#E05C5C;'>". __("Warning: ","migla-donation"). "</em> ". __(" Check this only if need to use the IPN listener on the frontend. See the documentation for details ","migla-donation"). " </div>";
}else{
  echo "<label class='checkbox-inline' for='migla_ipn_choice'><input type='checkbox' id='migla_ipn_choice' name='migla_ipn_choice' class=''><em>". __("Warning: ","migla-donation"). "</em> ". __(" Check this only if need to use the IPN listener on the frontend. See the documentation for details ","migla-donation"). "</label></div>";
}
   echo "</div>";

   $listenerfront = esc_url( add_query_arg( 'migla_listener', 'IPN', home_url( 'index.php' ) ) );

		echo "<div class='row' style='".$std_show."'><div class='col-sm-3'><label for='miglaPaypalListener' class='control-label text-right-sm text-center-xs'>". __("IPN Listener:","migla-donation"). "</label></div><div class='col-sm-9 col-xs-12'>";
   if( $ipn == 'front' ){
      echo "<label id='listener_front_url'>" . $listenerfront . "</label>";
      echo "<label id='listener_back_url' style='display:none'>" . migla_get_notify_url() . "</label>";
   }else{
      echo "<label id='listener_front_url' style='display:none'>" . $listenerfront . "</label>";
      echo "<label id='listener_back_url'>" . migla_get_notify_url() . "</label>";
   }
   echo "</div>";
   echo "</div>";    

		echo "<div class='row' style='".$std_show."'><div class='col-sm-3'><label for='migla_ipn_chatback' class='control-label text-right-sm text-center-xs'>". __("IPN ChatBack:","migla-donation"). "</label></div><div class='col-sm-9 col-xs-12'>";
   if( get_option('migla_ipn_chatback') == 'yes' ){
        echo "<label class='checkbox-inline'><input type='checkbox' id='migla_ipn_chatback'> ". __(" Check this if you'd like to disable chatback with PayPal. See documentation for details","migla-donation"). "</label>";
        if(  gethostbyname ( 'www.paypal.com' ) == 'www.paypal.com' )
        {
             echo "<span class='help-control col-sm-12   text-right-sm text-center-xs'><em style='color:red;'>". __("Warning: Could not resolved PayPal hostname. Chat Back has failed.","migla-donation"). "</em> </span>";
        }else{
             echo "<span class=' box-success checkbox-inline'> ". __("Success:  Hostname was resolved successfully! ","migla-donation"). " </span> ";
        }
   }else{
        echo "<label class='checkbox-inline'><input type='checkbox' id='migla_ipn_chatback' checked>  ". __(" Check this if you'd like to disable chatback with PayPal. See documentation for details","migla-donation"). "</label>";
        if(  gethostbyname ( 'www.paypal.com' ) == 'www.paypal.com' )
        {
             echo "<span class='help-control col-sm-12 text-right-sm text-center-xs'><em style='color:red;'>". __("Warning: Could not resolved PayPal hostname. Chat Back has failed.","migla-donation"). " </em> </span>";
        }else{
             echo "<span class='box-success checkbox-inline'> ". __("Success:   Hostname was resolved successfully! ","migla-donation"). " </span> ";

        }   
   }
   echo "</div>";
   echo "</div>";      


 $pToken = get_option('migla_pdt_token', '');
 echo "<div class='row mg_paypal_pdt' style='".$pdt_show."'><div class='col-sm-3'><label for='miglaPaypal_PDT_Token' class='control-label text-right-sm text-center-xs'>". __("Paypal Data Transfer Token ID","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
 echo "<input type='text' id='miglaPaypal_PDT_Token' value='".$pToken."' class='form-control'>";
 echo "</div></div>";

 $isCA = get_option('migla_pdt_using_ca');
 if( $isCA == false )
 { 
    add_option('migla_pdt_using_ca', 'no') ;
    $isCA = get_option('migla_pdt_using_ca'); 
 }
		echo "<div class='row mg_paypal_pdt' style='".$pdt_show."'><div class='col-sm-3'><label for='migla_pdt_using_ca' class='control-label text-right-sm text-center-xs'>". __("Force Certificate Authority verify on Paypal Data Transfer (PDT)","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
if( $isCA == 'yes' ){
  echo "<label class='checkbox-inline'><input type='checkbox' id='migla_pdt_using_ca' name='' class='' checked><label for='migla_pdt_using_ca'>". __("Yes","migla-donation"). "</label> </div>";
}else{
  echo "<label class='checkbox-inline'><input type='checkbox' id='migla_pdt_using_ca' name='' class=''><label for='migla_pdt_using_ca'>". __("Yes","migla-donation"). "</label></div>";
}
echo "</div>";

   

echo "<div class='row'><div class='col-sm-3'>";
echo "<label for='miglaPaypalSendFEC' class='control-label text-right-sm text-center-xs'>". __("Send FEC Data to PayPal:","migla-donation"). "</label></div>";
echo "<div class='col-sm-6 col-xs-12'><label class='checkbox-inline'>";
if( get_option('migla_paypal_fec') == 'yes' )
{
	echo "<input type='checkbox' id='miglaPaypalSendFEC' name='miglaPaypalSendFEC' class='' checked>". __("This sends data required by political donations in the USA to PayPal STD","migla-donation"). "</label></div></div>";
}else{
	echo "<input type='checkbox' id='miglaPaypalSendFEC' name='miglaPaypalSendFEC' class=''>". __("This sends data required by political donations in the USA to PayPal STD","migla-donation"). "</label></div></div>";
}
	
	
if( $paymentMethod == 'sandbox' )
{	
echo "<div class='row'><div class='col-sm-3'><label for='mg_sandbox' class='control-label text-right-sm text-center-xs'>". __("Type:","migla-donation"). "</label></div><div class='col-sm-6'>
														


<select id='mg_payment'>
<option value='sandbox' selected>". __("Sandbox PayPal","migla-donation"). "</option>
<option value='paypal'>". __("PayPal","migla-donation"). "</option>
</select>
</div>";
}else{
echo "<div class='row'><div class='col-sm-3'><label for='mg_sandbox' class='control-label text-right-sm text-center-xs'>". __("Type:","migla-donation"). "</label></div><div class='col-sm-6'>
														


<select id='mg_payment'>
<option value='sandbox'>". __("Sandbox PayPal","migla-donation"). "</option>
<option value='paypal' selected>". __("PayPal","migla-donation"). "</option>
</select>
</div>";
		
}
		echo "</div><div class='row'><div class='col-sm-3 col-xs-12'></div><div class='col-sm-6'><button id='miglaUpdatePaypalAccSettings' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation"). "</button></div></div>";

echo "</div></section></div>";


/***********************************************************/
/*
echo "<div class='col-sm-12'>";
		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseThree' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa fa-paypal'></i>". __("PayPal PDT Settings","migla-donation"). "</h2></header>";
		echo "<div id='collapseThree' class='panel-body collapse in'>";


 if( $isPDT == false )
 { 
    add_option('migla_using_pdt', 'no') ;
    $isPDT = get_option('migla_using_pdt'); 
 }
 
echo "<div class='row div_paypal_pdt'><div class='col-sm-3'><label for='mg_pdt_info_yes' class='control-label text-right-sm text-center-xs'>". __("Use Paypal Data Transfer (PDT)","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";

if( $isPDT == 'yes' ){
  echo "<label class='checkbox-inline' for='migla_using_pdt' id='mg_pdt_info_lbl'><input type='checkbox' id='migla_using_pdt' name='' class='' checked>". __("Check this if you want to use PDT instead PayPal standard","migla-donation"). "</label> </div>";
}else{
  echo "<label class='checkbox-inline' for='migla_using_pdt' id='mg_pdt_info_lbl'><input type='checkbox' id='migla_using_pdt' name='' class=''>". __("Check this if you want to use PDT instead PayPal standard","migla-donation"). "</label></div>";
}
echo "</div>";

 $pToken = get_option('migla_pdt_token', '');
 echo "<div class='row div_paypal_pdt'><div class='col-sm-3'><label for='miglaPaypal_PDT_Token' class='control-label text-right-sm text-center-xs'>". __("Paypal Data Transfer Token ID","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
 echo "<input type='text' id='miglaPaypal_PDT_Token' value='".$pToken."' class='form-control'>";
 echo "</div></div>";

 $isCA = get_option('migla_pdt_using_ca');
 if( $isCA == false )
 { 
    add_option('migla_pdt_using_ca', 'no') ;
    $isCA = get_option('migla_pdt_using_ca'); 
 }
		echo "<div class='row div_paypal_pdt'><div class='col-sm-3'><label for='migla_pdt_using_ca' class='control-label text-right-sm text-center-xs'>". __("Force Certificate Authority verify on Paypal Data Transfer (PDT)","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
if( $isCA == 'yes' ){
  echo "<label class='checkbox-inline'><input type='checkbox' id='migla_pdt_using_ca' name='' class='' checked><label for='migla_pdt_using_ca'>". __("Yes","migla-donation"). "</label> </div>";
}else{
  echo "<label class='checkbox-inline'><input type='checkbox' id='migla_pdt_using_ca' name='' class=''><label for='migla_pdt_using_ca'>". __("Yes","migla-donation"). "</label></div>";
}
echo "</div>";

		echo "<div class='row'><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div><div class='col-sm-6'><button id='miglaUpdatePaypalPDTSettings' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation"). "</button></div></div>";

echo "</div></section></div>";
*/

/*******************************************/

echo "<div class='col-sm-12'>";
		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseFour' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa fa-paypal'></i>". __("PayPal Payment Type","migla-donation"). "</h2></header>";
		echo "<div id='collapseFour' class='panel-body collapse in'>";


$pItem = get_option('migla_paypalitem' );

 if( $pItem == false ){
      add_option( 'migla_paymentitem', 'donation') ;
      $pItem = 'donation';	
 }

 if( $pItem == '' ){
    update_option( 'migla_paymentitem', 'donation') ;
    $pItem = 'donation';	
 }

	
	echo "<div class='row'><div class='col-sm-3'><label for='miglaPaypalItem' class='control-label text-right-sm text-center-xs'>". __("Item name:","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<input type='text' id='miglaPaypalItem' value='".$pItem."' class='form-control'></div>";


echo "<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>". __("This is the name of the item that shows up in PayPal, You can change it to something else: donation, backing, support. etc","migla-donation"). "
 </span></div>";
	
$paymentcmd['donation'] = '';  $paymentcmd['payment'] = '';
      $paymentCmd = get_option( 'migla_paymentcmd' ) ;

 if( $paymentCmd == false ){
    add_option( 'migla_paymentcmd', 'donation') ;
      $paymentcmd[ 'donation'] = 'checked';	
 }else{
      $paymentcmd[ $paymentCmd ] = 'checked';	
 }

 if( $paymentCmd == '' ){
    update_option( 'migla_paymentcmd', 'donation') ;
      $paymentcmd[ 'donation' ] = 'checked';	
 }else{
      $paymentcmd[ $paymentCmd ] = 'checked';	
 }


// Payment type ///////////////////////////////////////////////////////////////////////////////////////////


echo "<div class='row'><div class='col-sm-3'><label for='miglaPaypalcmd' class='control-label text-right-sm text-center-xs'>". __("Payment Type","migla-donation"). "</label></div><div class='col-sm-9'>														

<div class='radio'>
														<label>
															<input type='radio' id='miglaPaypalcmd' name='miglaPaypalcmd' value='donation' ".$paymentcmd['donation']." >". __("Donation","migla-donation"). "</label>
													</div>


<div class='radio'>
														<label>
															<input type='radio' name='miglaPaypalcmd' value='payment' ".$paymentcmd['payment']." >". __("Payment","migla-donation"). "
														</label>
													</div>


</div>";

		
		echo "</div><div class='row'><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div><div class='col-sm-6'><button id='miglaUpdatePaypalInfo' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation"). "</button></div>";

        echo "</div>";

		echo "</div></section></div>";



/*************** PAYPAL PRO *************************/
echo "<div class='col-sm-12'>";
	echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseFive' aria-expanded='true'></a></div>";
                echo "<h2 class='panel-title'><i class='fa fa-paypal'></i>". __("Paypal Pro API Keys and Signature (Live and Testing)","migla-donation"). "</h2></header>";
		
                echo "<div class='panel-body collapse in' id='collapseFive'>";

     $paypal_type = get_option('migla_paypal_pro_type');	
	 

                    echo "<div class='row'>";
                    echo "<div class='col-sm-3'><label for='mg_paypal_pro_type' class='control-label   text-right-sm text-center-xs'>". __("PayPal Type:","migla-donation"). " </label></div>";
	 	    echo "<div class='col-sm-6 col-xs-12'>";
          
			//echo "<div class='col-sm-3'><label class='control-label   text-right-sm text-center-xs'>". __("Website Pro","migla-donation"). " </label></div>";
	
			
                    echo "<select id='mg_paypal_pro_type'>";
					if( $paypal_type == 'paypal_flow' ){
						echo "<option value='website_pro'>Website Payment Pro</option>";
						echo "<option value='paypal_flow' selected>PayPal Flow</option>";
					}else{
						echo "<option value='website_pro' selected>Website Payment Pro</option>";
						echo "<option value='paypal_flow'>PayPal Flow</option>";					
					}
					echo "</select>";
			
		    echo "</div>";
                    echo "<div class='col-sm-3'></div>";
                    echo "</div>";				
			
                if ( function_exists('curl_version') ){
                  $version = curl_version(); //print_r($version);
                  /*
                  foreach( (array)$version as $key => $value ){
                    echo "<div class='row'>";
                    echo "<div class='col-sm-3'><label class='control-label  text-right-sm text-center-xs'>Curl ".$key.": </label></div>";
	 	    echo "<div class='col-sm-6 col-xs-12'>";
                    echo "<input class='form-control' placeholder='' value='".$version[$key]."'>";
		    echo "</div>";
                    echo "<div class='col-sm-3'></div>";
                    echo "</div>";
                 }
                 */		
                    echo "<div class='row'>";
                    echo "<div class='col-sm-3'><label for='mg_curl-version' class='control-label   text-right-sm text-center-xs'>". __("Curl Version / Number :","migla-donation"). " </label></div>";
	 	    echo "<div class='col-sm-6 col-xs-12'>";
                    echo "<input type='text' class='form-control' id='mg_curl-version' placeholder='' value='".$version['version']." / ".$version['version_number']." '>";
		    echo "</div>";
                    echo "<div class='col-sm-3'></div>";
                    echo "</div>";

                }else{
                }
				
if( $paypal_type != 'paypal_flow' ){				
	echo "<div id='div_website_pro' class='row'> <div class='col-sm-12 col-xs-12'>";
}else{
	echo "<div id='div_website_pro' class='row' style='display:none'> <div class='col-sm-12 col-xs-12'>";
}
                echo "<div class='row'>";
                echo "<div class='col-sm-3'><label for='mg_paypalpro_username' class='control-label  text-right-sm text-center-xs'>". __("User Name:","migla-donation"). "</label></div>";
		echo "<div class='col-sm-6 col-xs-12'>";
                echo "<input type='text' class='form-control' placeholder='' id='mg_paypalpro_username' value='".get_option('migla_paypalpro_username')."'>";
		echo "</div>";
                echo "<div class='col-sm-3'></div>";
                echo "</div>";						

                echo "<div class='row'>";
                echo "<div class='col-sm-3'><label for='mg_paypalpro_password' class='control-label  text-right-sm text-center-xs'>". __("Password:","migla-donation"). "</label></div>";
		echo "<div class='col-sm-6 col-xs-12'>";
                echo "<input type='text' class='form-control' placeholder='' id='mg_paypalpro_password' value='".get_option('migla_paypalpro_password')."'>";
		echo "</div>";
                echo "<div class='col-sm-3'></div>";
                echo "</div>";						

                echo "<div class='row'>";
                echo "<div class='col-sm-3'><label for='mg_paypalpro_signature' class='control-label  text-right-sm text-center-xs'>". __("Signature:","migla-donation"). "</label></div>";
		echo "<div class='col-sm-6 col-xs-12'>";
                echo "<input type='text' class='form-control' placeholder='' id='mg_paypalpro_signature' value='".get_option('migla_paypalpro_signature')."'>";
		echo "</div>";
                echo "<div class='col-sm-3'></div>";
                echo "</div>";

		$pro_rec = get_option('migla_paypalpro_recurring');
                echo "<div class='row'>";
                echo "<div class='col-sm-3'><label for='mg_paypalpro_recurring' class='control-label  text-right-sm text-center-xs'>". __("Recurring Method:","migla-donation"). "</label></div>";
		echo "<div class='col-sm-6 col-xs-12'>";
		echo "<label class='control-label  text-left-sm text-center-xs'>Direct Payment</label>";
		
		/*
                echo "<select id='mg_paypalpro_recurring'>";
				if($pro_rec == 'drp')
				{		
					echo "<option value='drp' selected>Direct Payment</option>";
					echo "<option value='sec'>Set Express Checkout</option>";
				}else{
					echo "<option value='drp'>Direct Payment</option>";
					echo "<option value='sec' selected>Set Express Checkout</option>";		
				}		
				echo "</select>";
		*/
		
		echo "</div>";
                echo "<div class='col-sm-3'></div>";
                echo "</div>";		

		/*		
		$page_id = get_option('migla_express_checkout_listener');
		$pages   = $this->get_all_posts(); 
		$is_page_exist = false;

		if( $pro_rec == 'drp' )
		{
			echo "<div class='row' id='div_set_SEC_page' style='display:none'>";
		}else{
			echo "<div class='row' id='div_set_SEC_page' style=''>";
		}
		echo "<div class='col-sm-3 col-xs-12'><label for='miglaSetSECPage' class='control-label text-right-sm text-center-xs'>". __("(Important) Set Return URL for this :","migla-donation");
		echo "</label></div>";
		echo "<div class='col-sm-6 col-xs-12'><select id='miglaSetSECPage'>"; 
		foreach( $pages as $key )
		{
			if(  $page_id == $key['id'] )
			{
				echo "<option value='".$key['id']."' selected>".$key['title']."</option>"; 
				$is_page_exist = true;
			}else{
				echo "<option value='".$key['id']."' >".$key['title']."</option>"; 
			}
		}
		  
		if(!$is_page_exist){
			echo "<option value=''  selected>None</option>"; 
		}else{
			echo "<option value='' >None</option>"; 
		}	

		echo "</select>";
		echo "<span class='col-sm-12 box-success'> Use short code [totaldonations_setexpresscheckout] in one of your page as return page after donor redirect from PayPal.";
		echo "Donation will be saved after donor accept transaction on PayPal and redirect here with success message</span>";		
		echo "</div>";
		
		echo "<div class='col-sm-3 col-xs-12'></div></div>";

               */
		
								
                echo "</div><div class='col-sm-12'><div class='row'>";
                echo "<div class='col-sm-3'></div>";
		echo "<div class='col-sm-6 col-xs-12'><button id='migla_paypalpro_save' class='btn btn-info pbutton '><i class='fa fa-fw fa-save'></i>". __(" Save","migla-donation"). "</button></div>";
                echo "<div class='col-sm-3'></div>";
                echo "</div></div></div>";	
				
				
/** PayPAL FLOW **/
if( $paypal_type == 'paypal_flow' ){	
	echo "<div id='div_paypal_flow' class='row'><div class='col-sm-12 col-xs-12'>";
}else{
	echo "<div id='div_paypal_flow' class='row' style='display:none'><div class='col-sm-12 col-xs-12'>";
}

                echo "<div class='row'>";
                echo "<div class='col-sm-3'><label for='mg_paypalflow_vendor' class='control-label  text-right-sm text-center-xs'>". __("Vendor:","migla-donation"). "</label></div>";
		echo "<div class='col-sm-6 col-xs-12'>";
                echo "<input type='text' class='form-control' placeholder='' id='mg_paypalflow_vendor' value='".get_option('migla_paypalflow_vendor')."'>";
		echo "</div>";
                echo "<div class='col-sm-3'></div>";
                echo "</div>";						

                echo "<div class='row'>";
                echo "<div class='col-sm-3'><label for='mg_paypalflow_user' class='control-label  text-right-sm text-center-xs'>". __("User:","migla-donation"). "</label></div>";
		echo "<div class='col-sm-6 col-xs-12'>";
                echo "<input type='text' class='form-control' placeholder='' id='mg_paypalflow_user' value='".get_option('migla_paypalflow_user')."'>";
		echo "</div>";
                echo "<div class='col-sm-3'></div>";
                echo "</div>";						

                echo "<div class='row'>";
                echo "<div class='col-sm-3'><label for='mg_paypalflow_partner' class='control-label  text-right-sm text-center-xs'>". __("Partner:","migla-donation"). "</label></div>";
		echo "<div class='col-sm-6 col-xs-12'>";
                echo "<input type='text' class='form-control' placeholder='' id='mg_paypalflow_partner' value='".get_option('migla_paypalflow_partner')."'>";
		echo "</div>";
                echo "<div class='col-sm-3'></div>";
                echo "</div>";		

                echo "<div class='row'>";
                echo "<div class='col-sm-3'><label for='mg_paypalflow_password' class='control-label  text-right-sm text-center-xs'>". __("Password:","migla-donation"). "</label></div>";
		echo "<div class='col-sm-6 col-xs-12'>";
                echo "<input type='text' class='form-control' placeholder='' id='mg_paypalflow_password' value='".get_option('migla_paypalflow_password')."'>";
		echo "</div>";
                echo "<div class='col-sm-3'></div>";
                echo "</div>";	

                echo "<div class='row'>";
                echo "<div class='col-sm-3'></div>";
		echo "<div class='col-sm-6 col-xs-12'><button id='migla_paypalflow_save' class='btn btn-info pbutton '><i class='fa fa-fw fa-save'></i>". __(" Save","migla-donation"). "</button></div>";
                echo "<div class='col-sm-3'></div>";
                echo "</div>";					
				
   echo "</div></div>";

                echo "</div></section>"; echo "</div>";


/*************** PAYPAL FLOW *************************/
/*
echo "<div class='col-sm-12'>";
	echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseEleven' aria-expanded='true'></a></div>";
                echo "<h2 class='panel-title'><i class='fa fa-paypal'></i>". __("Paypal Flow","migla-donation"). "</h2></header>";
		
                echo "<div class='panel-body collapse in' id='collapseEleven'>";			

                echo "<div class='row'>";
                echo "<div class='col-sm-3'></div>";
		echo "<div class='col-sm-6 col-xs-12'><button id='migla_paypalflow_save' class='btn btn-info pbutton '><i class='fa fa-fw fa-save'></i>". __(" Save","migla-donation"). "</button></div>";
                echo "<div class='col-sm-3'></div>";
                echo "</div>";				

                echo "</div></section>"; echo "</div>";
*/


/** SECURITY ISSUE ***/

echo "<div class='col-xs-12'>";
echo "<section class='panel'>";  
echo "<header class='panel-heading'>";
echo "<div class='panel-actions'>";
echo "<a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseSecurity' aria-expanded='true'></a>";
echo "</div>";  
echo "<h2 class='panel-title'><div class=' dashicons dashicons-lock'></div>".__("PayPal Security Options","migla-donation")."</h2>";
echo "</header>";
echo "<div id='collapseSecurity' class='panel-body collapse in'>";

echo "<div class='row'><div class='col-sm-3'><label for='miglaPaypalVerifySSL' class='control-label text-right-sm text-center-xs'>". __("Force to verify SSL by using ca.pem:","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'><label class='checkbox-inline'>";
if( get_option('migla_paypal_verifySSL') == 'yes' )
{
  echo "<input type='checkbox' id='miglaPaypalVerifySSL' name='miglaPaypalVerifySSL' class='' checked>". __(" If checked Total Donations will check if the SSL certificate belongs to PayPal before processing the donation. This is to prevent against fraudulent sites pretending to be PayPal. ","migla-donation"). "</label></div></div>";
}else{
  echo "<input type='checkbox' id='miglaPaypalVerifySSL' name='miglaPaypalVerifySSL' class=''>". __(" If checked Total Donations will check if the SSL certificate belongs to PayPal before processing the donation. This is to prevent against fraudulent sites pretending to be PayPal. ","migla-donation"). " </label></div></div>";
}

echo "<div class='row'><div class='col-sm-3'><label for='migla_credit_card_validator' class='control-label text-right-sm text-center-xs'>". __("Force to verify Credit Card pattern and length:","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'><label class='checkbox-inline'>";
if( get_option('migla_credit_card_validator') == 'yes' )
{
  echo "<input type='checkbox' id='migla_credit_card_validator' name='migla_credit_card_validator' class='' checked>". __(" If checked Total Donations will check the credit card pattern and length before sending it to PayPal","migla-donation"). "</label></div></div>";
}else{
  echo "<input type='checkbox' id='migla_credit_card_validator' name='migla_credit_card_validator' class=''>". __(" If checked Total Donations will check the credit card pattern and length before sending it to PayPal","migla-donation"). " </label></div></div>";
}

$isAVSon = get_option('migla_credit_card_avs') ;
echo "<div class='row'><div class='col-sm-3'><label for='migla_credit_card_avs' class='control-label text-right-sm text-center-xs'>". __("Force Total Donations to use Address Verification Service:","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'><label class='checkbox-inline'>";
if( $isAVSon == 'yes' )
{
  echo "<input type='checkbox' id='migla_credit_card_avs' name='migla_credit_card_avs' class='' checked>";
  echo __('Address Verification System (AVS) provides additional levels of confirmation that the person using the card is the legitimate owner of the card. This is useful to identify and avoid fraud.', 'migla-donation');

echo __(" For PayPal Flow, the Fraud Management Filter is set from inside the PayPal Flow Manager and not inside Total Donations.","migla-donation"). "";
echo "<strong style='color: #e05c5c;'>". __(" You have to make the address and postal code fields mandatory on the form.", "migla-donation"). "</strong></label><br></div></div>";
}else{
  echo "<input type='checkbox' id='migla_credit_card_avs' name='migla_credit_card_avs' class=''>";
  echo __('Address Verification System (AVS) provides additional levels of confirmation that the person using the card is the legitimate owner of the card. This is useful to identify and avoid fraud.', 'migla-donation');
echo __(" For PayPal Flow the Fraud Management Filter is set from inside PayPalFlow Manager not from Total Donations.","migla-donation"). "";
echo "<strong style='color: #e05c5c;'>". __(" You have to make the address and postal code fields mandatory on the form.", "migla-donation"). "</strong></label><br></div></div>";
}

/*********** AVS levels *****************/
if( $isAVSon == 'yes' )
{
    echo "<div class='row' id='migla_div_avs_level'>";
}else{
    echo "<div class='row' id='migla_div_avs_level' style='display:none'>";
}
echo "<div class='col-sm-3'><label for='migla_credit_card_AVS_levels' class='control-label text-right-sm text-center-xs'>". __("Choice the AVS Level of Security:","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";

$avs_level = get_option( 'migla_avs_level' );

if( $avs_level == 'medium')
{
	echo "<div class='radio'>
				<label>
					<input type='radio' id='migla_credit_card_AVS_levels' name='migla_credit_card_AVS_levels' value='medium' checked>". __("Medium: Allow partial match of Postal Code and Address Fields ","migla-donation"). "</label></div>";
	echo "<div class='radio'>
				<label>
					<input type='radio' id='migla_credit_card_AVS_levels2' name='migla_credit_card_AVS_levels' value='high'>". __("High: Only allow exact match of both Address and Postal Code Fields","migla-donation"). "</label></div>";
	echo "</div>";	
				
}else{
	echo "<div class='radio'>
				<label>
					<input type='radio' id='migla_credit_card_AVS_levels' name='migla_credit_card_AVS_levels' value='medium'>". __("Medium: Allow partial match of Postal Code and Address Fields ","migla-donation"). "</label></div>";
	echo "<div class='radio'>
				<label>
					<input type='radio' id='migla_credit_card_AVS_levels2' name='migla_credit_card_AVS_levels' value='high' checked>". __("High: Only allow exact match of both Address and Postal Code Fields","migla-donation"). "</label></div>";
	echo "</div>";	

}
echo "<div class='col-sm-3'></div>";
echo "</div>";	


/********** End AVS levels ***************/

$isCapcthaOn = get_option('migla_use_captcha') ;
echo "<div class='row'><div class='col-sm-3'><label for='migla_use_captcha' class='control-label text-right-sm text-center-xs'>". __("Force Total Donations to verifiy using captcha:","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'><label class='checkbox-inline'>";

$style_captcha = '';
if( $isCapcthaOn == 'yes' )
{
	echo "<input type='checkbox' id='migla_use_captcha' name='migla_use_captcha' class='' checked>";
	echo __('Re-Captcha is used to prevent your form from being attacked by spam bots. Register here: ', 'migla-donation');  
        echo "<a href='https://www.google.com/recaptcha/admin#list'>https://www.google.com/recaptcha/admin#list</a>";
	echo "</div></div>";

}else{
  echo "<input type='checkbox' id='migla_use_captcha' name='migla_use_captcha' class=''>";
	echo __('Re-Captcha is used to prevent your form from being attacked by spam bots. Register here: ', 'migla-donation');  
        echo "<a href='https://www.google.com/recaptcha/admin#list'>https://www.google.com/recaptcha/admin#list</a>";
	echo "</div></div>";
   
  $style_captcha = "style='display:none'";
}

echo "<div class='row mg_captcha_keys' ".$style_captcha."><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>". __("Captcha Site Key","migla-donation"). "</label></div>";
echo "<div class='col-sm-6 col-xs-12'><input type='text' id='migla_captcha_site_key' value='".get_option('migla_captcha_site_key')."'>";
echo "</div>";
echo "<div class='col-sm-3'></div>";
echo "</div>";

echo "<div class='row mg_captcha_keys' ".$style_captcha."><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>". __("Captcha Secret Key","migla-donation"). "</label></div>";
echo "<div class='col-sm-6 col-xs-12'><input type='text' id='migla_captcha_secret_key' value='".get_option('migla_captcha_secret_key')."'>";
echo "</div>";
echo "<div class='col-sm-3'></div>";
echo "</div>";

echo "<div class='row'>";
echo "<div class='col-sm-3'></div>";
echo "<div class='col-sm-6 col-xs-12'><button id='migla_security_save' class='btn btn-info pbutton '><i class='fa fa-fw fa-save'></i>". __(" Save","migla-donation"). "</button></div>";
echo "<div class='col-sm-3'></div>";
echo "</div>";	

echo "</div></section></div>";

/************  CC Info **************************/

echo "<div class='col-xs-12'>";
echo "<section class='panel'>";  
echo "<header class='panel-heading'>";
echo "<div class='panel-actions'>";
echo "<a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseSix' aria-expanded='true'></a>";
echo "</div>";  
echo "<h2 class='panel-title'><div class='dashicons dashicons-admin-appearance'></div>".__("Paypal Pro Credit Card Tab","migla-donation")."</h2>";
echo "</header>";
echo "<div id='collapseSix' class='panel-body collapse in'>";

  $cc_label = get_option('migla_paypalpro_cc_info');
  if( $cc_label == false )
  {
     $cc_label = array();
     $cc_label[0][1] = 'Paypal';     //0 
     $cc_label[1][1] = 'Pay with Credit Card';     //1 
     $cc_label[2][1] = 'Pay with paypal account';  //2 
     $cc_label[3][1] = 'Your Name';             //3
     $cc_label[4][1] = 'as it appears on your card'; //4
     $cc_label[5][1] = 'Your Name';            //5
     $cc_label[6][1] = 'as it appears on your card'; //6
     $cc_label[7][1] = 'Card Number';            //7
     $cc_label[8][1] = 'No Dashes';        //8
     $cc_label[9][1] = 'Expiration//CVC';           //9
     $cc_label[10][1] = 'CVC';                 //10

     add_option('migla_paypalpro_cc_info', $cc_label );  

  }else{
     $cc_label = $cc_label;
  }

  //print_r($cc_label);

echo "<div class='row'><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs' for='mg_paypalpro-radio1'>".__("Paypal Pro Radio","migla-donation")."</label></div><div class='col-sm-3 col-xs-12'>";
echo '<input type="text" class="form-control" value="'.$this->writeme($cc_label[1][1]).'" placeholder="" id="mg_paypalpro-radio1"></div>';
echo "<div class='col-sm-3 col-xs-12'>";
echo '<input type="text" class="form-control" value="'.$this->writeme($cc_label[2][1]).'" placeholder="" id="mg_paypalpro-radio2"></div>';
echo "</div>";


// Names //

echo "<div class='form-horizontal'><div class='form-group grouping'> <div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs' for='mg_name-paypalpro'>".__("First and Last Name/Placeholder","migla-donation")."</label></div> <div class='col-sm-6 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_name-paypalpro" id="mg_name-paypalpro" class="form-control" placeholder="Name" value="'.$this->writeme($cc_label[3][1]).'">';
echo "</div></div><br><div class='form-group grouping'><div class='col-sm-3 col-xs-12'></div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_placeholder-name" id="mg_placeholder-name" class="form-control" placeholder="Placeholder Text" value="'.$this->writeme($cc_label[4][1]).'">';
echo "</div>";
echo "<div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_placeholder-name" id="mg_placeholder-lname" class="form-control" placeholder="Placeholder Text" value="'.$this->writeme($cc_label[6][1]).'">';
echo "</div></div>";
echo "</div>";

// Card Number //

echo "<div class='row'> <div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs' for='mg_cardnumber-paypalpro'>".__("Card Number/Placeholder","migla-donation")."</label></div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_cardnumber-paypalpro" id="mg_cardnumber-paypalpro" class="form-control" placeholder="Card Number" value="'.$this->writeme($cc_label[7][1]).'">';
echo "</div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_placeholder-card" id="mg_placeholder-card" class="form-control" placeholder="Placeholder Text" value="'.$this->writeme($cc_label[8][1]).'"> </div></div>';

// CVC //

echo "<div class='row'> <div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs' for='mg_cvc-paypalpro'>".__("CVC/Placeholder","migla-donation")."</label></div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_cvc-paypalpro" id="mg_cvc-paypalpro" class="form-control" placeholder="Expiration/CVC" value="'.$this->writeme($cc_label[9][1]).'">';
echo "</div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_placeholder-CVC" id="mg_placeholder-CVC" class="form-control" placeholder="Placeholder Text" value="'.$this->writeme($cc_label[10][1]).'"> </div></div>';


// waiting text //


echo "<div class='row'> <div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs' for='mg_waiting_paypal'>".__("Text displayed while redirecting/processing.","migla-donation")."</label></div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_waiting_paypal" id="mg_waiting_paypal" class="form-control" placeholder="Just a moment while we redirect you to PayPal" value="'.get_option('migla_paypal_wait_paypal').'">';
echo "</div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_placeholder_waiting" id="mg_waiting_paypalpro" class="form-control" placeholder="Just a moment while we process your donation" value="'.get_option('migla_paypal_wait_paypalpro').'"> </div></div>';


echo "<div class='row'><div class='col-sm-12 center-button'><button value='save' class='btn btn-info pbutton msave' id='miglaSaveCCInfo'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div></div>";

echo "</div>";             
echo "</section>"; echo "</div>";











//////////////////// Upload Button Image ///////////////////////////////////


$btnchoice = get_option('miglaPayPalButtonChoice');
$choice['paypalButton'] = ""; $choice['imageUpload'] = ""; $choice['cssButton'] = "";

$btnlang = get_option('migla_paypalbutton');
if( $btnlang == false ){ add_option('migla_paypalbutton', 'English'); }
if( $btnlang == '' ){ update_option('migla_paypalbutton', 'English'); }

$btnurl = get_option('migla_paypalbuttonurl');

$btnstyle = get_option('migla_paypalcssbtnstyle');
  if( $btnstyle == false ){ add_option('migla_paypalcssbtnstyle', 'Default'); }
$btntext = get_option('migla_paypalcssbtntext');
  if( $btntext == false ){ add_option('migla_paypalcssbtntext', 'Donate Now'); }
$btnclass = get_option('migla_paypalcssbtnclass');
  if( $btnclass == false ){ add_option('migla_paypalcssbtnclass', ''); }

if( $btnchoice == false ){ 

  $btnchoice = 'paypalButton'; 
  add_option('miglaPayPalButtonChoice', $btnchoice );
  update_option('migla_paypalbutton', 'English');
  $choice['paypalButton'] = "checked";

}else if( $btnchoice == '' ){

  $btnchoice = 'paypalButton'; 
  update_option('miglaPayPalButtonChoice', $btnchoice );
  update_option('migla_paypalbutton', 'English'); 
  $choice['paypalButton'] = "checked";

}else if( $btnchoice == 'paypalButton' ){

   $choice['paypalButton'] = "checked";

}else if( $btnchoice == 'imageUpload' ){

   $choice['imageUpload'] = "checked";

}else{ 

   $choice['cssButton'] = "checked";

}


echo "<div class='col-xs-12'>";
echo "<section class='panel'>";  
echo "<header class='panel-heading'>";
echo "<div class='panel-actions'>";
echo "<a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseSeven' aria-expanded='true'></a>";
echo "</div>";  
echo "<h2 class='panel-title'><div class='dashicons dashicons-admin-appearance'></div>".__("Button","migla-donation")."</h2>";
echo "</header>";
echo "<div id='collapseSeven' class='panel-body collapse in'>";

echo "<div class='form-horizontal'><div class='form-group touching radio'><div class='col-sm-1'></div>
        <div class='col-sm-11'><label><input type='radio' ".$choice['paypalButton']." value='paypalButton' name='miglaPayPalButtonChoice'>".__("Use Paypal Button","migla-donation")."</label></div></div><div class='form-group touching'><div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs'>".__("Language","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'><select id='miglaPayPalButtonPicker' name='miglaPayPalButtonPicker'>";

$checkit = "";
if( $btnlang == 'english'){ $checkit = "selected='selected'"; }
 echo "<option ".$checkit." value='english'>".__("English","migla-donation")."</option>";

$checkit = "";
if( $btnlang == 'simplified_chinese'){ $checkit = "selected='selected'"; }
 echo "<option ".$checkit." value='simplified_chinese'>".__("Chinese (Simplified)","migla-donation")."</option>";

$checkit = "";
if( $btnlang == 'traditional_chinese'){ $checkit = "selected='selected'"; }
 echo "<option ".$checkit." value='traditional_chinese'>".__("Chinese (Traditional)","migla-donation")."</option>";

$checkit = "";
if( $btnlang == 'dutch'){ $checkit = "selected='selected'"; }
 echo "<option ".$checkit." value='dutch'>".__("Dutch","migla-donation")."</option>";

$checkit = "";
if( $btnlang == 'french'){ $checkit = "selected='selected'"; }
 echo "<option ".$checkit." value='french'>".__("French","migla-donation")."</option>";

$checkit = "";
if( $btnlang == 'hebrew'){ $checkit = "selected='selected'"; }
 echo "<option ".$checkit." value='hebrew'>".__("Hebrew","migla-donation")."</option>";

$checkit = "";
if( $btnlang == 'norwegian'){ $checkit = "selected='selected'"; }
 echo "<option ".$checkit." value='norwegian'>".__("Norwegian","migla-donation")."</option>";

$checkit = "";
if( $btnlang == 'polish'){ $checkit = "selected='selected'"; }
 echo "<option ".$checkit." value='polish'>".__("Polish","migla-donation")."</option>";

$checkit = "";
if( $btnlang == 'russian'){ $checkit = "selected='selected'"; }
 echo "<option ".$checkit." value='russian'>".__("Russian","migla-donation")."</option>";

$checkit = "";
if( $btnlang == 'spanish'){ $checkit = "selected='selected'"; }
 echo "<option ".$checkit." value='spanish'>".__("Spanish","migla-donation")."</option>";

$checkit = "";
if( $btnlang == 'swedish'){ $checkit = "selected='selected'"; }
 echo "<option ".$checkit." value='swedish'>".__("Swedish","migla-donation")."</option>";

$checkit = "";
if( $btnlang == 'turkey'){ $checkit = "selected='selected'"; }
 echo "<option ".$checkit." value='turkey'>".__("Turkish","migla-donation")."</option>";

echo "</select></div><div class='col-sm-3'><button value='save' class='btn btn-info pbutton' id='miglaSavePayPalButtonPicker'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div></div></div>";


echo "<div class='form-horizontal'><div class='form-group touching radio'><div class='col-sm-1'></div>
        <div class='col-sm-11'><label><input type='radio' ".$choice['imageUpload']." value='imageUpload' name='miglaPayPalButtonChoice'>".__("Upload Your Own Button","migla-donation")."</label></div></div>
<br><div class='form-group touching'>
<div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs'>".__("Upload:","migla-donation")."</label></div>";

echo "<div class='col-sm-6 col-xs-12'>";

 echo "<input id='mg_upload_image' type='text' size='36' name='mg_upload_image' value='".$btnurl."' />";

echo "</div><div class='col-sm-3  col-xs-12'><button value='upload' class='btn btn-info obutton ' id='miglaUploadPaypalBtn'><i class='fa fa-fw fa-upload'></i>".__(" upload","migla-donation")."</button>";
echo "<button value='save' class='btn btn-info pbutton' id='miglaSavePaypalBtnUrl'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div>";
echo "</div></div>";               

echo "<div class='form-horizontal'><div class='form-group touching radio'><div class='col-sm-1'></div>
        <div class='col-sm-11'><label><input type='radio' ".$choice['cssButton']." value='cssButton' name='miglaPayPalButtonChoice'>".__("Choose a CSS Button","migla-donation")."</label></div></div><br>

<div class='form-group touching'><div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs'>".__("Button","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'><select id='mg_CSSButtonPicker' class='form-control touch-top' name='miglaCSSButtonPicker'>";

if( $btnstyle == 'Default'){
 echo "<option selected='selected' value='Default'>".__("Your Default Form Button","migla-donation")."</option><option value='Grey'>".__("Grey Button","migla-donation")."</option></select></div><div class='col-sm-3'></div></div>";
}else{
 echo "<option value='Default'>".__("Your Default Form Button","migla-donation")."</option><option selected='selected' value='Grey'>".__("Grey Button","migla-donation")."</option></select></div><div class='col-sm-3'></div></div>";

}

echo "<div class='form-group touching'><div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs'>".__("Button Text","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'> <input id='mg_CSSButtonText' type='text' value='".$btntext."' required='' placeholder='Donate Now' title='' class='form-control touch-middle' name=''></div><div class='col-sm-3'></div></div><div class='form-group touching'><div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs'>".__("Add CSS class (theme button only)","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'> <input id='mg_CSSButtonClass' type='text' value='".$btnclass."' required='' placeholder='enter your css here' title='' class='form-control touch-bottom' name=''>     </div><div class='col-sm-3'><button value='save' class='btn btn-info pbutton' id='miglaCSSButtonPickerSave'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div></div></div>";

echo "</div>";             

echo "</section>";  
echo "</div> <!-- row col-xs-12-->";
//////////////////// END OF Upload Button Image ///////////////////////////////////


















 



		//echo "<br> <br></div>"; 
              echo "</div></div>"; // row id=wrap
		
	}

}

?>