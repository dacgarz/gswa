<?php
class migla_authorize_settings_class {

	function __construct() {
		add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 12 );
	}
	
	function menu_item() {
		add_submenu_page(
			'migla_donation_menu_page',
			__( 'Authorize.Net Settings', 'migla-donation' ),
			__( 'Authorize.Net Settings', 'migla-donation' ),
			$this->get_capability(),
			'migla_donation_authorize_settings_page',
			array( $this, 'menu_page' )
		);
	}

       function writeme( $str ){
         $result =  str_replace( "//" , "/" , $str );
         $result =  str_replace( "[q]" , "'" , $result );
         return $result;
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

  $cc_label = get_option('migla_authorize_cc_info');
  if( $cc_label == false )
  {
     $cc_label = array();
     $cc_label[0][1] = 'Authorize.Net';     //0 
     $cc_label[1][1] = 'Your Name';             //1
     $cc_label[2][1] = 'as it appears on your card'; //2
     $cc_label[3][1] = 'Your Last Name';            //3
     $cc_label[4][1] = 'as it appears on your card'; //4
     $cc_label[5][1] = 'Card Number';            //5
     $cc_label[6][1] = 'No Dashes';        //6
     $cc_label[7][1] = 'Expiration//CVC';           //7
     $cc_label[8][1] = 'CVC';                 //8

     add_option('migla_authorize_cc_info', $cc_label );  

  }else{
     $cc_label = $cc_label;
  }

		echo "<div class='wrap'><div class='container-fluid'>";   
                echo "<h2 class='migla'>". __("Authorize.Net Settings","migla-donation")."</h2>";

		echo "<div class='row form-horizontal'>";

echo "<div class='col-sm-12'>";
		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseOne' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa fa-credit-card'></i>". __("Authorize.Net Donation Form","migla-donation"). "</h2></header>";
		echo "<div id='collapseOne' class='panel-body collapse in'>";


/***** The order of Gateways *******************/

$order_of_gateways = get_option('migla_gateways_order');
echo "<div class='row'><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>". __("Choose the gateway(s) you'd like to use and their order:","migla-donation"). "</label></div>";

echo "<div class='col-sm-6 col-xs-12' id='default_payment_section'>";
if( $order_of_gateways == 'false' || $order_of_gateways[0] == '' )
{
   $order_of_gateways[0] = array('paypal', false);
   $order_of_gateways[1] = array('stripe', false);
   $order_of_gateways[2] = array('authorize', false);
   $order_of_gateways[3] = array('offline', false);

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
		echo "<div class='row'><div class='col-sm-6'><div class='row'><div class='col-sm-12'><input type='checkbox' class='mg_status_gateways' value='".$value[0]."' checked> " .$name. "</div></div></div>";
		echo "</div>";
             }else{
		echo "<div class='row'><div class='col-sm-6'><div class='row'><div class='col-sm-12'><input type='checkbox' class='mg_status_gateways' value='".$value[0]."'> " .$name. "</div></div></div>";
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

	    echo "<li class='ui-state-default formfield '>";
            if( $value[1] == 'true' || $value[1] == 1 )
            {
		echo "<div class='row'><div class='col-sm-6'><div class='row'><div class='col-sm-12'><input type='checkbox' class='mg_status_gateways' value='".$value[0]."' checked> " .$name. "</div></div></div>";
		echo "</div>";
             }else{
		echo "<div class='row'><div class='col-sm-6'><div class='row'><div class='col-sm-12'><input type='checkbox' class='mg_status_gateways' value='".$value[0]."'> " .$name. "</div></div></div>";
		echo "</div>";
             }
	    echo "</li>";
   }
   echo "</ul>";
}

echo "</div></div>";


echo "<div class='row'> <div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs' for='mg_tab-authorize'>".__("Authorize.Net Tab Label:","migla-donation")."</label></div> <div class='col-sm-6 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_tab-authorize" id="mg_tab-authorize" class="form-control" placeholder="Tab Name" value="'.$this->writeme($cc_label[0][1]).'">';
echo "</div></div>";

		
		echo "<div class='row'> <div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div><div class='col-sm-6'><button id='miglaUpdateAuthorizeSettings' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation"). "</button></div></div>";


///binti
echo "</div></section></div>";



/******* Silent POST Information *****************************************************************************/

echo "<div class='col-sm-12'>";
	echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseTwo' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa fa-credit-card'></i>". __("Authorize.Net Silent Post","migla-donation"). "</h2></header>";
		echo "<div id='collapseTwo' class='panel-body collapse in'>";

echo "<div class='row'><div class='col-sm-3'><label for='mg_auth_post_url' class='control-label text-right-sm text-center-xs'>". __("Authorize.Net Silent Post URL on Front-End","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<input type='text' value='".home_url( 'index.php' )."?migla_authorize_listener=silent_post' id='mg_auth_post_url' />";
echo "</div>";

   echo "<div class='col-sm-3'><a><button value='Preview Page' class='btn btn-info obutton' id='miglaStripeWebhook' onclick='window.open(\"http://www.authorize.net\")'><i class='fa fa-fw fa-search'></i>". __(" Go to Authorize.net","migla-donation"). "</button></a></div>";

echo "</div>";

echo "<p id='warningEmptyAmounts' >".__("Total Donations uses Silent Post URL to record ongoing recurring donations. Copy the URL above and save it to silent post on Authorize.Net.","migla-donation")."<i class='fa fa-fw fa-caret-up'></i></p>";

echo "</div>";
echo "</section>";
echo "</div>";


/***** Authorize.Net info ************************/

echo "<div class='col-sm-12'>";
		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseThree' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa  fa-credit-card'></i>". __("Authorize.Net Info","migla-donation"). "</h2></header>";
		echo "<div id='collapseThree' class='panel-body collapse in'>";

	
		echo "<div class='row'><div class='col-sm-3'><label for='miglaAuthorizeAPIKey' class='control-label text-right-sm text-center-xs'>". __("API Key:","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<input type='text' id='miglaAuthorizeAPIKey' value='".get_option('migla_authorize_api_key')."' class='form-control'></div>";
 

echo "<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>". __("The API Key","migla-donation"). "</span>
</div>";

		echo "<div class='row'><div class='col-sm-3'><label for='miglaAuthorizeTranKey' class='control-label text-right-sm text-center-xs'>". __("Transaction Key:","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<input type='text' id='miglaAuthorizeTranKey' value='".get_option('migla_authorize_trans_key')."' class='form-control'></div>";
 

echo "<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>". __("Transaction Key","migla-donation"). "</span>
</div>";


$typeAuth = get_option('migla_payment_authorize');
if( $typeAuth == false || $typeAuth == '' ){
   $typeAuth = 'sandbox';  add_option('migla_payment_authorize', 'sandbox');
}	

if( $typeAuth == 'authorize' ){
  echo "<div class='row'><div class='col-sm-3'><label for='mg_sandbox' class='control-label text-right-sm text-center-xs'>". __("Type:","migla-donation"). "</label></div><div class='col-sm-9'>
  <div class='radio'><label>
  <input type='radio' id='mg_sandbox' name='miglaAuthorize' value='sandbox'>". __("Sandbox Authorize.Net","migla-donation"). "</label>
													</div>
  <div class='radio'>
  <label><input type='radio' name='miglaAuthorize' value='authorize' checked>". __("Authorize.Net","migla-donation"). "</label>
  </div>
  </div>";
}else{
  echo "<div class='row'><div class='col-sm-3'><label for='mg_sandbox' class='control-label text-right-sm text-center-xs'>". __("Type:","migla-donation"). "</label></div><div class='col-sm-9'>
  <div class='radio'><label>
  <input type='radio' id='mg_sandbox' name='miglaAuthorize' value='sandbox' checked >". __("Sand Box Authorize.NET","migla-donation"). "</label>
													</div>
  <div class='radio'>
  <label><input type='radio' name='miglaAuthorize' value='authorize' >". __("Authorize.NET","migla-donation"). "</label>
  </div>
  </div>";
}

		echo "</div><div class='row'><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div><div class='col-sm-6 '><button id='miglaUpdateAuthKeys' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation"). "</button></div></div>";

		echo "</div></section>";
		echo "</div>"; 

/** SECURITY ISSUE ***/

echo "<div class='col-xs-12'>";
echo "<section class='panel'>";  
echo "<header class='panel-heading'>";
echo "<div class='panel-actions'>";
echo "<a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseSecurity' aria-expanded='true'></a>";
echo "</div>";  
echo "<h2 class='panel-title'><div class='dashicons dashicons-admin-appearance'></div>".__("Security Issue","migla-donation")."</h2>";
echo "</header>";
echo "<div id='collapseSecurity' class='panel-body collapse in'>";


		echo "<div class='row'><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>". __("Force to verify SSL by using ca.pem:","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
if( get_option('migla_authorize_verifySSL') == 'yes' )
{
  echo "<label class='checkbox-inline'><input type='checkbox' id='miglaAuthorizeVerifySSL' name='' class='' checked>". __("If checked Total Donations will check if the SSL certificate belongs to Authorize.net before processing the donation. This is to prevent against fraudulent sites pretending to be Authorize.net.","migla-donation"). "</label></div>";
}else{
  echo "<label class='checkbox-inline'><input type='checkbox' id='miglaAuthorizeVerifySSL' name='' class=''>". __("If checked Total Donations will check if the SSL certificate belongs to Authorize.net before processing the donation. This is to prevent against fraudulent sites pretending to be Authorize.net.","migla-donation"). "</label></div>";
}

echo "</div>";

echo "<div class='row'><div class='col-sm-3'><label for='migla_credit_card_validator' class='control-label text-right-sm text-center-xs'>". __("Force to verify Credit Card pattern and length:","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'><label class='checkbox-inline'>";
if( get_option('migla_credit_card_validator') == 'yes' )
{
  echo "<input type='checkbox' id='migla_credit_card_validator' name='migla_credit_card_validator' class='' checked>". __(" Force Total Donations to check credit card before it send to Authorize.Net","migla-donation"). "</label></div></div>";
}else{
  echo "<input type='checkbox' id='migla_credit_card_validator' name='migla_credit_card_validator' class=''>". __(" Force Total Donations to check credit card before it send to Authorize.Net"). " </label></div></div>";
}

echo "<div class='row'><div class='col-sm-3'></div>";
echo "<div class='col-sm-6 col-xs-12'><label>";
echo  __('Address Verification System (AVS) provide additional levels of confirmation that the person using the card is the legitimate owner of the card, this is useful to identify and avoid fraud.', 'migla-donation');
echo __(' To setup this feature go to your Authorize.net Account, on Tools > Fraud Detection Suite.', 'migla-donation');
echo  "<strong style='color: #e05c5c;'>". __(" You have to make the address and postal code fields mandatory on the form.", "migla-donation"). "</strong>";

echo "</label></div></div>";

/*
if( get_option('migla_credit_card_avs') == 'yes' )
{
  echo "<input type='checkbox' id='migla_credit_card_avs' name='migla_credit_card_avs' class='' checked>";
  echo "Address Verification System (AVS) provides additional levels of confirmation that the person using the card is the legitimate owner of the card, this is useful to identify and avoid fraud.";
  echo  "<strong style='color: #e05c5c;'>". __(" You have to make address and postal code fields mandatory on the form.", "migla-donation"). "</strong>";
  echo  "</label></div></div>";
}else{
echo "Address Verification System (AVS) provides additional levels of confirmation that the person using the card is the legitimate owner of the card, this is useful to identify and avoid fraud.";
  echo  "<strong style='color: #e05c5c;'>". __(" You have to make address and postal code fields mandatory on the form.", "migla-donation"). "</strong>";
  echo  "</label></div></div>";
}
*/


$isCapcthaOn = get_option('migla_use_captcha') ;
echo "<div class='row'><div class='col-sm-3'><label for='migla_use_captcha' class='control-label text-right-sm text-center-xs'>". __("Force Total Donations to verifiy using captcha:","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'><label class='checkbox-inline'>";

$style_captcha = '';
if( $isCapcthaOn == 'yes' )
{
	echo "<input type='checkbox' id='migla_use_captcha' name='migla_use_captcha' class='' checked>";
	echo __('Re-Captcha is used to prevent your form being attack by spam bot. Register here: ', 'migla-donation');
echo "<a href='https://www.google.com/recaptcha/admin#list'>https://www.google.com/recaptcha/admin#list</a>";
	echo "</div></div>";

}else{
  echo "<input type='checkbox' id='migla_use_captcha' name='migla_use_captcha' class=''>";
	echo __('Re-Captcha is used to prevent your form being attack by spam bot. Register here: ', 'migla-donation');
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
echo "<a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseFive' aria-expanded='true'></a>";
echo "</div>";  
echo "<h2 class='panel-title'><div class='dashicons dashicons-admin-appearance'></div>".__("Authorize.NET Credit Card Tab","migla-donation")."</h2>";
echo "</header>";
echo "<div id='collapseFive' class='panel-body collapse in'>";


// Names //


echo "<div class='form-horizontal'><div class='form-group grouping'> <div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs' for='mg_name-authorize'>".__("First and Last Name/Placeholder","migla-donation")."</label></div> <div class='col-sm-6 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_name-authorize" id="mg_name-authorize" class="form-control" placeholder="Name Label" value="'.$this->writeme($cc_label[1][1]).'">';
echo "</div></div><br><div class='form-group grouping'><div class='col-sm-3 col-xs-12'></div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_placeholder-name" id="mg_placeholder-name" class="form-control" placeholder="Placeholder First Name" value="'.$this->writeme($cc_label[2][1]).'">';
echo "</div>";
echo "<div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_placeholder-name" id="mg_placeholder-lname" class="form-control" placeholder="Placeholder Last Name" value="'.$this->writeme($cc_label[4][1]).'">';
echo "</div></div>";
echo "</div>";


/*

echo "<div class='row'> <div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs' for='mg_name-authorize'>".__("First Name/Placeholder","migla-donation")."</label></div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_name-authorize" id="mg_name-authorize" class="form-control" placeholder="Name" value="'.$this->writeme($cc_label[1][1]).'">';
echo "</div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_placeholder-name" id="mg_placeholder-name" class="form-control" placeholder="Placeholder Text" value="'.$this->writeme($cc_label[2][1]).'">';
echo "</div></div>";

echo "<div class='row'> <div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs' for='mg_lname-authorize'>".__("Last Name/Placeholder","migla-donation")."</label></div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_lname-authorize" id="mg_lname-authorize" class="form-control" placeholder="Name" value="'.$this->writeme($cc_label[3][1]).'">';
echo "</div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_placeholder-name" id="mg_placeholder-lname" class="form-control" placeholder="Placeholder Text" value="'.$this->writeme($cc_label[4][1]).'">';
echo "</div></div>";


*/

// Card Number //

echo "<div class='row'> <div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs' for='mg_cardnumber-authorize'>".__("Card Number/Placeholder","migla-donation")."</label></div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_cardnumber-authorize" id="mg_cardnumber-authorize" class="form-control" placeholder="Card Number" value="'.$this->writeme($cc_label[5][1]).'">';
echo "</div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_placeholder-card" id="mg_placeholder-card" class="form-control" placeholder="Placeholder Text" value="'.$this->writeme($cc_label[6][1]).'"> </div></div>';

// CVC //

echo "<div class='row'> <div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs' for='mg_cvc-authorize'>".__("CVC/Placeholder","migla-donation")."</label></div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_cvc-authorize" id="mg_cvc-authorize" class="form-control" placeholder="Expiration/CVC" value="'.$this->writeme($cc_label[7][1]).'">';
echo "</div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_placeholder-CVC" id="mg_placeholder-CVC" class="form-control" placeholder="Placeholder Text" value="'.$this->writeme($cc_label[8][1]).'"> </div></div>';


// waiting text //

echo "<div class='row'> <div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs' for='mg_waiting_authorize'>".__("Text displayed while redirecting/processing.","migla-donation")."</label></div> <div class='col-sm-6 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_placeholder_waiting" id="mg_waiting_authorize" class="form-control" placeholder="Just a moment while we process your donation" value="'.get_option('migla_wait_authorize').'"> </div></div>';


echo "<div class='row'><div class='col-sm-12 center-button'><button value='save' class='btn btn-info pbutton msave' id='miglaSaveCCInfo'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div></div>";

echo "</div>";             
echo "</section>"; echo "</div>";












//////////////////// Upload Button Image ///////////////////////////////////


$btnchoice = get_option('miglaAuthorizeButtonChoice');
$choice['imageUpload'] = ""; $choice['cssButton'] = "";

$btnurl = get_option('migla_authorizebuttonurl');

$btnstyle = get_option('migla_authorizecssbtnstyle');
  if( $btnstyle == false ){ add_option('migla_authorizecssbtnstyle', 'Default'); }
$btntext = get_option('migla_authorizecssbtntext');
  if( $btntext == false ){ add_option('migla_authorizecssbtntext', 'Donate Now'); }
$btnclass = get_option('migla_authorizecssbtnclass');
  if( $btnclass == false ){ add_option('migla_authorizecssbtnclass', ''); }

if( $btnchoice == false ){ 

  $btnchoice = 'cssButton'; 
  add_option('miglaAuthorizeButtonChoice', $btnchoice );
  $choice['cssButton'] = "checked";

}else if( $btnchoice == '' ){

  $btnchoice = 'cssButton'; 
  add_option('miglaAuthorizeButtonChoice', $btnchoice );
  $choice['cssButton'] = "checked";

}else if( $btnchoice == 'imageUpload' ){

   $choice['imageUpload'] = "checked";

}else{ 

   $choice['cssButton'] = "checked";

}


echo "<div class='col-xs-12'>";
echo "<section class='panel'>";  
echo "<header class='panel-heading'>";
echo "<div class='panel-actions'>";
echo "<a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseFour' aria-expanded='true'></a>";
echo "</div>";  
echo "<h2 class='panel-title'><div class='dashicons dashicons-admin-appearance'></div>".__("Button","migla-donation")."</h2>";
echo "</header>";
echo "<div id='collapseFour' class='panel-body collapse in'>";



echo "<div class='form-horizontal'><div class='form-group touching radio'><div class='col-sm-1'></div>
        <div class='col-sm-11'><label><input type='radio' ".$choice['imageUpload']." value='imageUpload' name='miglaAuthorizeButtonChoice'>".__("Upload Your Own Button","migla-donation")."</label></div></div>
<br><div class='form-group touching'>
<div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs'>".__("Upload:","migla-donation")."</label></div>";

echo "<div class='col-sm-6 col-xs-12'>";

 echo "<input id='mg_upload_image' type='text' size='36' name='mg_upload_image' value='".$btnurl."' />";

echo "</div><div class='col-sm-3  col-xs-12'><button value='upload' class='btn btn-info obutton ' id='miglaUploadAuthorizeBtn'><i class='fa fa-fw fa-upload'></i>".__(" upload","migla-donation")."</button>";
echo "<button value='save' class='btn btn-info pbutton' id='miglaSaveAuthorizeBtnUrl'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div>";
echo "</div></div>";               

echo "<div class='form-horizontal'><div class='form-group touching radio'><div class='col-sm-1'></div>
        <div class='col-sm-11'><label><input type='radio' ".$choice['cssButton']." value='cssButton' name='miglaAuthorizeButtonChoice'>".__("Choose a CSS Button","migla-donation")."</label></div></div><br>

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