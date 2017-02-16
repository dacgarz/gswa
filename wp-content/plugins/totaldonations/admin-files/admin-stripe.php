<?php
class migla_stripe_setting_class {

	function __construct() {
		add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 12 );
	}
	
	function menu_item() {
		add_submenu_page(
			'migla_donation_menu_page',
			__( 'Stripe Settings', 'migla-donation' ),
			__( 'Stripe Settings', 'migla-donation' ),
			$this->get_capability() ,
			'migla_stripe_setting_page',
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
                echo "<h2 class='migla'>". __("Stripe Settings","migla-donation")."</h2>";

		echo "<div class='row form-horizontal'>";

  $cc_label = get_option('migla_stripe_cc_info');
  if( $cc_label == false )
  {
     $cc_label = array();
     $cc_label[0][1] = 'Stripe'; 
     $cc_label[1][1] = 'Your Name';
     $cc_label[2][1] = 'as it appears on your card';
     $cc_label[3][1] = 'Card Number';
     $cc_label[4][1] = 'No Dashes';
     $cc_label[5][1] = 'Expiration//CVC';
     $cc_label[6][1] = 'CVC';

     add_option('migla_stripe_cc_info', $cc_label );  

  }else{
     $cc_label = $cc_label;
  }

  $showStripe = get_option('migla_show_stripe');


echo "<div class='col-sm-12'>";
		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseTwo' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa fa-cc-stripe'></i>". __("Stripe Donation Form","migla-donation"). "</h2></header>";
		echo "<div id='collapseTwo' class='panel-body collapse in'>";


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


echo "<div class='row'><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs' for='mg_stripe-tab'>".__("Stripe Tab Label:","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'>";
echo '<input type="text" class="form-control" value="'.$this->writeme($cc_label[0][1]).'" placeholder="Stripe" id="mg_stripe-tab"></div></div>';

echo "<div class='row'><div class='col-sm-3 '></div><div class='col-sm-3 '><button value='save' class='btn btn-info pbutton'  id='mg_save_section1'><i class='fa fa-fw fa-save'></i>". __( " save ", "migla-donation")."</button></div></div>";


echo "</div></section>";
echo "</div>"; 





/******* Web Hook Information *****************************************************************************/

echo "<div class='col-sm-12'>";
	echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseOne' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa  fa-cc-stripe'></i>". __("Stripe's Webhook","migla-donation")."</h2></header>";
		echo "<div id='collapseOne' class='panel-body collapse in'>";



echo "<div class='row'><div class='col-sm-3'><label for='mg_stripe_webhook_url' class='control-label text-right-sm text-center-xs'>". __("Webhook's URL (Front End)","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<input type='text' value='".home_url( 'index.php' ) . "?migla_listener_stripe=webhook"."' id='mg_stripe_webhook_url2' />";
echo "</div></div>";

echo "<div class='row'><div class='col-sm-3'><label for='mg_stripe_webhook_url' class='control-label text-right-sm text-center-xs'>". __("Webhook's URL (Back End)","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<input type='text' value='".plugins_url( 'migla-stripe-weebhook.php' , dirname(__FILE__) )."' id='mg_stripe_webhook_url' />";
echo "</div>";

   echo "<div class='col-sm-3'><a><button value='Preview Page' class='btn btn-info obutton' id='miglaStripeWebhook' onclick='window.open(\"https://dashboard.stripe.com/account/webhooks\")'><i class='fa fa-fw fa-search'></i>". __(" Go to Stripe","migla-donation"). "</button></a></div>";



echo "</div>";

echo "<p id='warningEmptyAmounts' >".__("Copy one of the URLs above and add it into the webhook area inside the admin panel on Stripe's website. Please read Stripe's documentation for more detailed information.","migla-donation")."<i class='fa fa-fw fa-caret-up'></i></p>";

echo "</section>";
echo "</div>";


/*************  Stripe ******************************************************************************************************/

$testSK = get_option('migla_testSK'); $testPK = get_option('migla_testPK'); 
$liveSK = get_option('migla_liveSK'); $livePK = get_option('migla_livePK');
$stripeMode = get_option('migla_stripemode');

 
echo "<div class='col-sm-12'>";
		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseInfo' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa fa-cc-stripe'></i>".__("Stripe Info","migla-donation")."</h2></header>";
		echo "<div id='collapseInfo' class='panel-body collapse in'>";



/////////////////// Testing ////////////////////////////////////////////////
	
		echo "<div class='row'><div class='col-sm-3'><label for='migla_testSK' class='control-label text-right-sm text-center-xs'>". __("Test Secret Key","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<input type='text' id='migla_testSK' value='".$testSK."' class='form-control'></div></div>";

		echo "<div class='row'><div class='col-sm-3'><label for='migla_testPK' class='control-label text-right-sm text-center-xs'>". __("Test Publishable Key","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<input type='text' id='migla_testPK' value='".$testPK."' class='form-control'></div></div>";


/////////////////// ALIVE ////////////////////////////////////////////////

		echo "<div class='row'><br><div class='col-sm-3'><label for='migla_liveSK' class='control-label text-right-sm text-center-xs'>". __("Live Secret Key","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<input type='text' id='migla_liveSK' value='".$liveSK."' class='form-control'></div></div>";

		echo "<div class='row'><div class='col-sm-3'><label for='migla_livePK' class='control-label text-right-sm text-center-xs'>". __("Live Publishable Key","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<input type='text' id='migla_livePK' value='".$livePK."' class='form-control'></div></div>";


echo "<div class='row'><div class='col-sm-3'></div><div class='col-sm-9'>";
														
if( $stripeMode == false)
{
  add_option( 'migla_stripemode', 'test' );
}

if( $stripeMode == 'test' )
{
  echo "<div class='radio'>
														<label>
															<input type='radio' name='miglaStripe' value='test' checked >". __("Test Stripe","migla-donation"). "</label>
													</div>


<div class='radio'>
														<label>
															<input type='radio' name='miglaStripe' value='live' >". __("Live Stripe","migla-donation"). "
														</label>
													</div>


</div>";

}else{

  echo "<div class='radio'>
														<label>
															<input type='radio' name='miglaStripe' value='test' >". __("Testing Stripe","migla-donation"). "</label>
													</div>


<div class='radio'>
														<label>
															<input type='radio' name='miglaStripe' value='live' checked >". __("Live Stripe","migla-donation"). "
														</label>
													</div>


</div>";

}

		
		echo "</div><div class='row'><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div><div class='col-sm-6'><button id='miglaUpdateStripeKeys' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation"). "</button></div></div>";

		echo "</div></section>";
		echo "</div>"; 


/** SECURITY ISSUE ***/

echo "<div class='col-xs-12'>";
echo "<section class='panel'>";  
echo "<header class='panel-heading'>";
echo "<div class='panel-actions'>";
echo "<a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseSecurity' aria-expanded='true'></a>";
echo "</div>";  
echo "<h2 class='panel-title'><div class='dashicons dashicons-lock'></div>".__(" Stripe Security Options","migla-donation")."</h2>";
echo "</header>";
echo "<div id='collapseSecurity' class='panel-body collapse in'>";

echo "<div class='row'><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>". __("Force to verify SSL by using ca.pem:","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
if( get_option('migla_stripe_verifySSL') == 'yes' )
{
  echo "<label class='checkbox-inline'><input type='checkbox' id='miglaStripeVerifySSL' name='' class='' checked>". __("If checked Total Donations will check if the SSL certificate belongs to Stripe before processing the donation. This is to prevent against fraudulent sites pretending to be Stripe. ","migla-donation"). "</label></div></div>";
}else{
  echo "<label class='checkbox-inline'><input type='checkbox' id='miglaStripeVerifySSL' name='' class=''>". __("If checked Total Donations will check if the SSL certificate belongs to Stripe before processing the donation. This is to prevent against fraudulent sites pretending to be Stripe. ","migla-donation"). "</label></div></div>";
}


echo "<div class='row'><div class='col-sm-3'><label for='migla_credit_card_validator' class='control-label text-right-sm text-center-xs'>". __("Force to verify Credit Card pattern and length:","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'><label class='checkbox-inline'>";
if( get_option('migla_credit_card_validator') == 'yes' )
{
  echo "<input type='checkbox' id='migla_credit_card_validator' name='migla_credit_card_validator' class='' checked>". __(" If checked Total Donations will check the credit card pattern and length before sending it to Stripe","migla-donation"). "</label></div></div>";
}else{
  echo "<input type='checkbox' id='migla_credit_card_validator' name='migla_credit_card_validator' class=''>". __(" If checked Total Donations will check the credit card pattern and length before sending it to Stripe"). " </label></div></div>";
}

echo "<div class='row'><div class='col-sm-3'><label for='migla_stripe_js' class='control-label text-right-sm text-center-xs'>". __("Use Stripe.JS :","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
if( get_option('migla_stripe_js') == 'yes' )
{
  echo "<label class='checkbox-inline'><input type='checkbox' id='migla_stripe_js' name='' class='' checked>";
  echo __("Make Total Donations use the JavaScript provided by Stripe for creating token. This is recommend for easier PCI Compliance", "migla-donation");
}else{
  echo "<label class='checkbox-inline'><input type='checkbox' id='migla_stripe_js' name='' class=''>";
 echo __("Make Total Donations use the JavaScript provided by Stripe for creating token. This is recommend for easier PCI Compliance", "migla-donation");  
}
echo "</label></div></div>";

echo "<div class='row'><div class='col-sm-3'><label for='migla_credit_card_avs' class='control-label text-right-sm text-center-xs'>". __("Use Address Verification Service (AVS):","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'><label class='checkbox-inline'>";
if( get_option('migla_credit_card_avs') == 'yes' )
{
  echo "<input type='checkbox' id='migla_credit_card_avs' name='migla_credit_card_avs' class='' checked>";
  echo __('Address Verification System (AVS) provides additional levels of confirmation that the person using the card is the legitimate owner of the card. This is useful to identify and avoid fraud. ', 'migla-donation');
echo "<strong style='color: #e05c5c;'>". __(" You have to make address and postal code fields mandatory on the form", "migla-donation"). "</strong></label></div></div>";
}else{
  echo "<input type='checkbox' id='migla_credit_card_avs' name='migla_credit_card_avs' class=''>";
   echo __('Address Verification System (AVS) provides additional levels of confirmation that the person using the card is the legitimate owner of the card. This is useful to identify and avoid fraud.   ', 'migla-donation');
echo "<strong style='color: #e05c5c;'>". __(" You have to make address and postal code fields mandatory on the form.", "migla-donation"). "</strong></label></div></div>";
}


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
	echo __('Re-Captcha is used to prevent your form being attack by spam bot. Register here:', 'migla-donation');
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

//////////////////// Localization Tab ///////////////////////////////////


echo "<div class='col-sm-12'>";
echo "<section class='panel'>";  
echo "<header class='panel-heading'>";
echo "<div class='panel-actions'>";
echo "<a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseEight' aria-expanded='true'></a>";
echo "</div>";  
echo "<h2 class='panel-title'><div class='dashicons dashicons-admin-appearance'></div>".__("Stripe Credit Card Tab","migla-donation")."</h2>";
echo "</header>";
echo "<div id='collapseEight' class='panel-body collapse in'>";

// Name //

echo "<div class='row'> <div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs' for='mg_name-stripe'>".__("Name/Placeholder","migla-donation")."</label></div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_name-stripe" id="mg_name-stripe" class="form-control" placeholder="Name" value="'.$this->writeme($cc_label[1][1]).'">';
echo "</div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_placeholder-name" id="mg_placeholder-name" class="form-control" placeholder="Placeholder Text" value="'.$this->writeme($cc_label[2][1]).'">';
echo "</div></div>";

// Card Number //

echo "<div class='row'> <div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs' for='mg_cardnumber-stripe'>".__("Card Number/Placeholder","migla-donation")."</label></div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_cardnumber-stripe" id="mg_cardnumber-stripe" class="form-control" placeholder="Card Number" value="'.$this->writeme($cc_label[3][1]).'">';
echo "</div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_placeholder-card" id="mg_placeholder-card" class="form-control" placeholder="Placeholder Text" value="'.$this->writeme($cc_label[4][1]).'"> </div></div>';

// CVC //

echo "<div class='row'> <div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs' for='mg_cvc-stripe'>".__("CVC/Placeholder","migla-donation")."</label></div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_cvc-stripe" id="mg_cvc-stripe" class="form-control" placeholder="Expiration/CVC" value="'.$this->writeme($cc_label[5][1]).'">';
echo "</div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_placeholder-CVC" id="mg_placeholder-CVC" class="form-control" placeholder="Placeholder Text" value="'.$this->writeme($cc_label[6][1]).'"> </div></div>';


// waiting text //


echo "<div class='row'> <div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs' for='mg_waiting_stripe'>".__("Text displayed while redirecting/processing.","migla-donation")."</label></div> <div class='col-sm-6 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_placeholder_waiting" id="mg_waiting_stripe" class="form-control" placeholder="Just a moment while we process your donation" value="'.get_option('migla_wait_stripe').'"> </div></div>';


echo "<div class='row'><div class='col-sm-12 center-button'><button value='save' class='btn btn-info pbutton msave' id='miglaSaveCCInfo'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div></div>";

echo "</div>";             
echo "</section></div>"; 
 


//////////////////// Upload Button Image ///////////////////////////////////


$btnchoice = get_option('miglaStripeButtonChoice');
$choice['StripeButton'] = ""; $choice['imageUpload'] = ""; $choice['cssButton'] = "";

$btnlang = get_option('migla_stripebutton');

$btnurl = get_option('migla_stripebuttonurl');

$btnstyle = get_option('migla_stripecssbtnstyle');
  if( $btnstyle == false ){ add_option('migla_stripecssbtnstyle', 'Default'); }

$btntext = get_option('migla_stripecssbtntext');
  if( $btntext == false ){ add_option('migla_stripecssbtntext', 'Donate Now'); }

$btnclass = get_option('migla_stripecssbtnclass');
  if( $btnclass == false ){ add_option('migla_stripecssbtnclass', ''); }

if( $btnchoice == false ){ 

  $btnchoice = 'stripeButton'; 
  add_option('miglaStripeButtonChoice', $btnchoice );
  $choice['stripeButton'] = "checked";

}else if( $btnchoice == '' ){

  $btnchoice = 'stripeButton'; 
  update_option('miglaStripeButtonChoice', $btnchoice );
  $choice['stripeButton'] = "checked";

}else if( $btnchoice == 'stripeButton' ){

   $choice['stripeButton'] = "checked";

}else if( $btnchoice == 'imageUpload' ){

   $choice['imageUpload'] = "checked";

}else{ 

   $choice['cssButton'] = "checked";

}

echo "<div class='col-xs-12'>";
echo "<section class='panel'>";  
echo "<header class='panel-heading'>";
echo "<div class='panel-actions'>";
echo "<a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseNine' aria-expanded='true'></a>";
echo "</div>";  
echo "<h2 class='panel-title'><div class='dashicons dashicons-admin-appearance'></div>".__("Stripe Button","migla-donation")."</h2>";
echo "</header>";
echo "<div id='collapseNine' class='panel-body collapse in'>";

echo "<div class='form-horizontal'><div class='form-group touching radio'><div class='col-sm-1'></div>
        <div class='col-sm-2'><label><input type='radio' ".$choice['stripeButton']." value='stripeButton' name='miglaStripeButtonChoice'>".__("Choose the default Stripe Button","migla-donation")."</label></div></div><div class='form-group touching'><div class='col-sm-3  col-xs-12'><label for='stripeButtonText' class='control-label text-right-sm text-center-xs'>".__("Button Text","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'> <input id='stripeButtonText' type='text' value='' required='' placeholder='Donate Now' title='' class='form-control ' name=''></div><div class='col-sm-3'><button value='save' class='btn btn-info pbutton' id='miglaSavestripeButtonPicker'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div></div></div>";

echo "<div class='form-horizontal'><div class='form-group touching radio'><div class='col-sm-1'></div><div class='col-sm-11'><label><input type='radio' ".$choice['imageUpload']." value='imageUpload' name='miglaStripeButtonChoice'>".__("Upload Your Own Button","migla-donation")."</label></div></div><div class='form-group touching'><div class='col-sm-3  col-xs-12'><label for='mg_upload_image' class='control-label text-right-sm text-center-xs'>".__("Upload:","migla-donation")."</label></div>";

echo "<div class='col-sm-6 col-xs-12'>";
echo "<input id='mg_upload_image' type='text' size='36' name='mg_upload_image' value='".$btnurl."' />";
echo "</div><div class='col-sm-3  col-xs-12'><button value='upload' class='btn btn-info obutton ' id='miglaUploadstripeBtn'><i class='fa fa-fw fa-upload'></i>".__(" upload","migla-donation")."</button>";

echo "<button value='save' class='btn btn-info pbutton' id='miglaSavestripeBtnUrl'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div>";
echo "</div></div>";               

echo "<div class='form-horizontal'><div class='form-group touching radio'><div class='col-sm-1'></div><div class='col-sm-11'><label><input type='radio' ".$choice['cssButton']." value='cssButton' name='miglaStripeButtonChoice'>".__("Choose a CSS Button","migla-donation")."</label></div></div><div class='form-group touching'><div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs' for='mg_CSSButtonPicker'>".__("Button","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'><select id='mg_CSSButtonPicker' class='form-control touch-top' name='miglaCSSButtonPicker'>";

if( $btnstyle == 'Default'){
 echo "<option selected='selected' value='Default'>".__("Your Default Form Button","migla-donation")."</option><option value='Grey'>".__("Grey Button","migla-donation")."</option></select></div><div class='col-sm-3'></div></div>";
}else{
 echo "<option value='Default'>".__("Your Default Form Button","migla-donation")."</option><option selected='selected' value='Grey'>".__("Grey Button","migla-donation")."</option></select></div><div class='col-sm-3'></div></div>";

}

echo "<div class='form-group touching'><div class='col-sm-3  col-xs-12'><label for='mg_CSSButtonText' class='control-label text-right-sm text-center-xs'>".__("Button Text","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'> <input id='mg_CSSButtonText' type='text' value='".$btntext."' required='' placeholder='Donate Now' title='' class='form-control touch-middle' name=''></div><div class='col-sm-3'></div></div><div class='form-group touching'><div class='col-sm-3  col-xs-12'><label for='mg_CSSButtonClass' class='control-label text-right-sm text-center-xs'>".__("Add CSS class (theme button only)","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'> <input id='mg_CSSButtonClass' type='text' value='".$btnclass."' required='' placeholder='enter your css class here' title='' class='form-control touch-bottom' name=''></div><div class='col-sm-3'><button value='save' class='btn btn-info pbutton' id='miglaCSSButtonPickerSave'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div></div>";

echo "</div>";             

echo "</section>";  
echo "</div></div> <!-- row col-xs-12-->";
//////////////////// END OF Upload Button Image ///////////////////////////////////




echo "<section class='panel'>
							<header class='panel-heading'>
								<div class='panel-actions'>
									<a class='fa fa-caret-down ' data-toggle='collapse' data-parent='.panel' href='#collapseTen' aria-expanded='true'></a>
									
								</div>

								<h2 class='panel-title'><i class='fa fa-fw fa-list'></i>". __("List of Stripe Plans","migla-donation")."</h2>
							</header>
							<div id='collapseTen' class='panel-body collapse in'>
								<div id='datatable-default_wrapper' class='dataTables_wrapper no-footer'><div class='table-responsive'>";
 							
							   
   echo "<table id='miglaStripePlanTable' class='display' cellspacing='0' width='100%'>";

   echo "<thead>";
   echo "<tr>";
   echo "<th class='detailsHeader' style='width:15px;'>".__("Detail","migla-donation")."</th>";
   echo "<th class=''>". __("Created","migla-donation")."</th>";
   echo "<th class=''>". __("id","migla-donation")."</th>";
   echo "<th class=''>". __("Name","migla-donation")."</th>";
   echo "<th class=''>". __("Interval","migla-donation")."</th>";
   echo "<th class=''>". __("Amount","migla-donation")."</th>";
   echo "<th></th>";
   echo "</tr>"; 
   echo "</thead>";

   echo "<tfoot><tr>";
   echo "<th id='f0'>". __("Detail","migla-donation")."</th>";   
   echo "<th id='f1'>". __("Created","migla-donation")."</th>";   
   echo "<th id='f2'>". __("id","migla-donation")."</th>";
   echo "<th id='f3'>". __("Name","migla-donation")."</th>";
   echo "<th id='f4'>". __("Interval","migla-donation")."</th>";
   echo "<th id='f5'>". __("Amount","migla-donation")."</th>";
   echo "<th id='f6'></th>";
   
   echo "</tr></tfoot>";
   echo "</table>";

echo "<div class='row datatables-footer'><div class='col-sm-12 col-md-6'>
   <button class='btn mbutton' id='miglaSyncPlan' style=''>
   <i class='fa fa-fw fa-refresh '></i>". __(" Synchronize Plans with Stripe ","migla-donation")."</button>
   </div>
   
   <div class='col-sm-12 col-md-6'>

</div></div>";

   echo "  </div>   ";

   echo "</div> ";  
   					
		

              echo "</div></div></div>";
		
	}

}

?>