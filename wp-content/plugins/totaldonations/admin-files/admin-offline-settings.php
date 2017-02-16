<?php

class migla_offline_settings_class{

function __construct(){
  add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 12);
}

	
function menu_item() {
 add_submenu_page( 
   'migla_donation_menu_page',
   __( 'Offline Payment Settings', 'migla-donation' ),
   __( 'Offline Payment Settings', 'migla-donation' ),
   $this->get_capability() ,
   'migla_offline_settings_page',
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
	
	

  function getSymbol(){
    $i = '';
    $currencies = get_option( 'migla_currencies' );
    $def = get_option( 'migla_default_currency' );

	   foreach ( (array)$currencies as $key => $value ) 
	   { 
	      if ( strcmp($def,$currencies[$key]['code'] ) == 0 )
              { 
                 if( $currencies[$key]['faicon']!='' ) { 
                     $i = "<i class='fa fa-fw ".$currencies[$key]['faicon']."'></i>";
                     //$icon = $currencies[$key]['faicon']; 
                 }else{ $i = $currencies[$key]['symbol']; }
              }
	   }

    return $i;
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
   
   echo "<h2 class='migla'>". __("Offline donations","migla-donation"). "</h2>";

   echo "<div class='row'>";
   echo "<div class='col-sm-12'>";
   echo "<input type='hidden' id='miglaDecimalSep' value='".get_option('migla_decimalSep')."' />";
   
   echo "<section class='panel'>";
   echo "<header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down ' data-toggle='collapse' data-parent='.panel' href='#collapseOne' aria-expanded='true'></a></div><h2 class='panel-title'><div class='dashicons dashicons-feedback'></div>". __( "Offline donation form", "migla-donation")."</h2></header>";

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
}

echo "</div></div>";

echo "<div class='row'><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs' for='mg_offline_tab'>".__("Offline Tab Label","migla-donation")."</label></div>";
echo "<div class='col-sm-6 col-xs-12'>";
echo "<input type='text' class='form-control' value='". get_option('migla_offline_tab') ."' placeholder='Offline' id='mg_offline_tab' /></div>";
echo "</div>";

echo "<div class='row'><div class='col-sm-3 '></div><div class='col-sm-3 '><button value='save' class='btn btn-info pbutton'  id='miglaOfflineSettings'><i class='fa fa-fw fa-save'></i>". __( " save ", "migla-donation")."</button></div></div>";

   echo "</div></section></div>";

 

echo "<div class='col-sm-12 '><section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down ' data-toggle='collapse' data-parent='.panel' href='#collapseTwo' aria-expanded='true'></a></div><h2 class='panel-title'><div class='dashicons dashicons-analytics'></div>". __( "Info on Offline Payment Panel:", "migla-donation")."</h2></header>";
   echo "<div id='collapseTwo' class='panel-body collapse in'>";

/*
if( get_option('migla_offline_info_btn') == 'yes' )
{
echo "<div class='form-horizontal'> <div class='form-group'>
<div class='col-sm-3 col-xs-12'><label for='migla_offline_info_btn' class=' control-label text-right-sm text-center-xs'>". __(" Use button on Offline's info","migla-donation")."</label></div>
<div class='col-sm-9 col-xs-12 text-left-sm text-center-xs'><label for='migla_offline_info_btn'><input type='checkbox' checked id='migla_offline_info_btn' name='migla_offline_info_btn'>". __( "Check this if you want to use button on your offline info", "migla-donation")." </label></div>
</div></div>";
}else{
echo "<div class='form-horizontal'> <div class='form-group'>
<div class='col-sm-3 col-xs-12'><label for='migla_offline_info_btn' class=' control-label text-right-sm text-center-xs'>". __(" Use button on Offline's info","migla-donation")."</label></div>
<div class='col-sm-9 col-xs-12 text-left-sm text-center-xs'><label for='migla_offline_info_btn'><input type='checkbox' id='migla_offline_info_btn' name='migla_offline_info_btn'>". __( "Check this if you want to use button on your offline info", "migla-donation")." </label></div>
</div></div>";
}
*/

 $offline_info = get_option('migla_offline_info');

 $settings =   array(
    'wpautop' => true, // use wpautop?
    'media_buttons' => true, // show insert/upload button(s)
    'textarea_name' => 'mg_offinfo_editor', // set the textarea name to something different, square brackets [] can be used here
    'textarea_rows' => 20, // rows="..."
    'tinymce' => true
 );


echo "<div class='row'><div class='col-sm-12 col-xs-12'>";
echo "<div class='col-sm-9 col-xs-12'>". wp_editor(  stripslashes($offline_info) , 'mg_offinfo_editor', $settings  ) . "</div></div>";

echo "<div class='col-sm-3'><br><button value='save' class='btn btn-info pbutton'  id='miglaInfoOffline'><i class='fa fa-fw fa-save'></i>". __( " save ", "migla-donation")."</button></div><div class='col-sm-9'><span class='help-control col-sm-12  text-right-sm text-center-xs'>". __( " This is the text that appears on the frontend inside the offline tab. Use this to tell your donors how to send you a donation offline.", "migla-donation")."</span></div>";


   echo "</div></section></div>";



/* Here is the new panel - Edit Here Astried */


echo "<div class='col-sm-12'><section class='panel'><header class='panel-heading'><div class='panel-actions'><a aria-expanded='true' href='#collapseThree' data-parent='.panel' data-toggle='collapse' class='fa fa-caret-down'></a></div><h2 class='panel-title'><div class='dashicons dashicons-email'></div>". __( "Instructions Emailed to Offline Donor before Payment is approved:", "migla-donation")."</h2></header><div class='panel-body collapse in' id='collapseThree'>";


if( get_option('migla_send_offmsg') == 'yes' )
{
echo "<div class='form-horizontal'> <div class='form-group'>
<div class='col-sm-3 col-xs-12'><label for='mSendOfflineEmail' class=' control-label text-right-sm text-center-xs'>". __(" Send an email to Offline donator?","migla-donation")."</label></div>
<div class='col-sm-9 col-xs-12 text-left-sm text-center-xs'><label class='checkbox-inline' for='mSendOfflineEmail'><input type='checkbox' checked id='mSendOfflineEmail' name='mSendOfflineEmail' checked>". __( "Check this if you want your donors to receive an email with instructions on how to donate offline.", "migla-donation")." </label></div>
</div></div>";
}else{
echo "<div class='form-horizontal'> <div class='form-group'>
<div class='col-sm-3 col-xs-12'><label for='mSendOfflineEmail' class=' control-label text-right-sm text-center-xs'>". __(" Send an email to Offline donator?","migla-donation")."</label></div>
<div class='col-sm-9 col-xs-12 text-left-sm text-center-xs'><label class='checkbox-inline' for='mSendOfflineEmail'><input type='checkbox' id='mSendOfflineEmail' name='mSendOfflineEmail'>". __( "Check this if you want your donors to receive an email with instructions on how to donate offline.", "migla-donation")." </label></div>
</div></div>";
}

echo "<div class='form-horizontal'> <div class='form-group touching'><div class='col-sm-3 col-xs-12'><label class=' control-label text-right-sm text-center-xs' for='migla_OfflineESbj'>". __(" Email Subject:","migla-donation")."</label>
  </div><div class='col-sm-6 col-xs-12'><input type='text' value='".get_option('migla_offmsg_thankSbj')."' required='' placeholder='' title='Here is how you can donate to us' class='form-control touch-top' id='migla_OfflineESbj' name='migla_OfflineESbj'></div>
<div class='col-sm-3 hidden-xs'></div></div><div class='form-group touching '><div class='col-sm-3'><label class=' control-label text-right-sm text-center-xs' for='migla_OfflineEBody'>". __(" Directions for Offline Donations. Email Body:","migla-donation")." </label>
 </div><div class='col-sm-6 col-xs-12'><textarea name='migla_OfflineEBody' rows='6' cols='50' class='form-control touch-middle' id='migla_OfflineEBody'>". get_option('migla_offmsg_body'). "</textarea></div><div class='col-sm-3'> </div> </div>

<div class='form-group'>
									<div class='col-sm-3 col-xs-12'>		<label class='control-label text-right-sm text-center-xs' for='migla_OfflineESig'>". __(" Signature:","migla-donation")."</label> </div>
											<div class='col-sm-6 col-xs-12'>
												<input value='".get_option('migla_offmsg_signature')."' style='overflow: hidden;' id='migla_OfflineESig' name='migla_OfflineESig' rows='5' title='' class='form-control touch-bottom' placeholder='' required=''>

<div class='row' style='border:none'><br><label class='col-sm-6 help-control'>". __("  Use the following shortcodes in the email body:","migla-donation")."</label>

<div class='col-sm-6'><code>[firstname]</code>". __(" Donor's First Name","migla-donation")."<br><code>[lastname]</code>". __(" Donor's Last Name","migla-donation")."<br> <code>[amount]</code>". __(" Donation Amount","migla-donation")."<br><code>[date]</code>". __(" Donation date","migla-donation")." <br><code>[newline] </code>". __("  Line Break","migla-donation")."</div></div>
												
												
								</div>			
<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button value='save' class='btn btn-info pbutton' id='miglaSaveMsgOffline'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation")."</button>  </div>

										</div> 
</div>"; 

/*
* echo "<div class='row'><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs' for='miglaTestEmailAdd'>". __(" Email address for Offline Email Test:","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'><input type='text' value='' id='miglaTestEmailAdd' class='form-control'></div><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button value='Send Testing Email' class='btn btn-info obutton' id='miglaTestEmail'><i class='fa fa-fw fa-envelope-o'></i>". __(" Preview Email","migla-donation")."</button></div><span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>". __("  Use this to preivew what the Offline Donor will see when they receive a email message.","migla-donation")."</span> </div>";   
*/


 
echo "</div></section></div>";

/* end new panel for offline email */


echo "<div class='col-xs-12'>";
echo "<section class='panel'>";  
echo "<header class='panel-heading'>";
echo "<div class='panel-actions'>";
echo "<a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseFour' aria-expanded='true'></a>";
echo "</div>";  
echo "<h2 class='panel-title'><div class='dashicons dashicons-admin-appearance'></div>".__("Offline Donation Text Waiting/Thank you","migla-donation")."</h2>";
echo "</header>";
echo "<div id='collapseFour' class='panel-body collapse in'>";

echo "<div class='row'> <div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs' for='mg_waiting_offline'>".__("Text displayed while redirecting/completed.","migla-donation")."</label></div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_waiting_offline" id="mg_waiting_offline" class="form-control" placeholder="Just a moment while we process your donation" value="'.get_option('migla_wait_offline').'">';
echo "</div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>";
echo '<input type="text" name="mg_placeholder_waiting" id="mg_thankyou_offline" class="form-control" placeholder="Thank you. An email with instructions has been send to your email address" value="'.get_option('migla_thankyou_offline').'"> </div>';
echo "<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button value='save' class='btn btn-info pbutton' id='migla_save_waiting_text'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button><br><br><br></div>";
echo "</div>";




echo "</div>";             
echo "</section>"; 
echo "</div>";





/////////////////// Upload Button Image ///////////////////////////////////


$btnchoice = get_option('miglaOfflineButtonChoice');
$choice['imageUpload'] 	= ''; 
$choice['cssButton'] 	= '';
$choice['none'] 		= '';
if( $btnchoice == false || $btnchoice == '')
{ 
  $btnchoice = 'none'; 
  update_option('miglaOfflineButtonChoice', $btnchoice );
  $choice['none'] = "checked";
}else if( $btnchoice == 'imageUpload' )
{
   $choice['imageUpload'] = "checked";
}else  if( $btnchoice == 'cssButton' ){ 
   $choice['cssButton'] = "checked";
}

$btnurl = get_option('migla_offlinebuttonurl');

$btnstyle = get_option('migla_offlinecssbtnstyle');
  if( $btnstyle == false ){ add_option('migla_offlinecssbtnstyle', 'Default'); }
  
$btntext = get_option('migla_offlinecssbtntext');
  if( $btntext == false ){ add_option('migla_offlinecssbtntext', 'Donate Now'); }
  
$btnclass = get_option('migla_authorizecssbtnclass');
  if( $btnclass == false ){ add_option('migla_offlinecssbtnclass', ''); }

echo "<div class='col-xs-12'>";
echo "<section class='panel'>";  
echo "<header class='panel-heading'>";
echo "<div class='panel-actions'>";
echo "<a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseFive' aria-expanded='true'></a>";
echo "</div>";  
echo "<h2 class='panel-title'><div class='dashicons dashicons-admin-appearance'></div>".__("Button","migla-donation")."</h2>";
echo "</header>";
echo "<div id='collapseFive' class='panel-body collapse in'>";

/* Hide Button Radio Button */

echo "<div class='form-horizontal'><div class='form-group touching radio'><div class='col-sm-1'></div>
<div class='col-sm-2'><label><input type='radio' value='none' name='miglaOfflineButtonChoice' ".$choice['none'].">".__("Hide Button on Form","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'></div><div class='col-sm-3  col-xs-12'><button id='miglaSaveOfflineBtnNone' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div>
<br></div></div>";

/* Upload Radio Button */

echo "<div class='form-horizontal'><div class='form-group touching radio'><div class='col-sm-1'></div>
        <div class='col-sm-11'><label><input type='radio' ".$choice['imageUpload']." value='imageUpload' name='miglaOfflineButtonChoice'>".__("Upload Your Own Button","migla-donation")."</label></div></div>
<br><div class='form-group touching'>
<div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs'>".__("Upload:","migla-donation")."</label></div>";

echo "<div class='col-sm-6 col-xs-12'>";

 echo "<input id='mg_upload_image' type='text' size='36' name='mg_upload_image' value='".$btnurl."' />";

echo "</div><div class='col-sm-3  col-xs-12'><button value='upload' class='btn btn-info obutton ' id='miglaUploadOfflineBtn'><i class='fa fa-fw fa-upload'></i>".__(" upload","migla-donation")."</button>";
echo "<button value='save' class='btn btn-info pbutton' id='miglaSaveOfflineBtnUrl'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div>";
echo "</div></div>";               

echo "<div class='form-horizontal'><div class='form-group touching radio'><div class='col-sm-1'></div>
        <div class='col-sm-11'><label><input type='radio' ".$choice['cssButton']." value='cssButton' name='miglaOfflineButtonChoice'>".__("Choose a CSS Button","migla-donation")."</label></div></div><br>

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

























   echo "</div></div>"; //WRAP & FLUID
}

}


?>