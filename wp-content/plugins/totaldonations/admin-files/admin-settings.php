<?php
class migla_settings_class {

	function __construct() {
		add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 12 );
	}
	
	function menu_item() {
		add_submenu_page(
			'migla_donation_menu_page',
			__( 'General Settings', 'migla-donation' ),
			__( 'General Settings', 'migla-donation' ),
			$this->get_capability(),
			'migla_donation_settings_page',
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
	
	


    function get_all_posts(){
       global $wpdb;
       $post_obj = array();
       $post_obj = $wpdb->get_results( 
	                 $wpdb->prepare( 
	                   "SELECT ID,post_title  FROM {$wpdb->prefix}posts WHERE post_type = %s" ,
	                   'page'
                         ) 
                   ); 

		$post_array = array();	$i = 0;	   
				   
       foreach( $post_obj as $post )
	   {
            $post_array[$i]['id'] = $post->ID;
            $post_array[$i]['title'] = $post->post_title;
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
                echo "<h2 class='migla'>". __("Settings","migla-donation")."</h2>";

	

                echo "<div class='row form-horizontal'>";

		echo "<div class='col-sm-12'>";
		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseFour' aria-expanded='true'></a></div><h2 class='panel-title'><div class='dashicons dashicons-plus'></div>". __( "Notifications and Email Settings","migla-donation"). "</h2></header>";
		echo "<div id='collapseFour' class='panel-body collapse in'><div class='row'>";
		
        $nEmails = get_option( 'migla_notif_emails' ) ;
		
		echo "<div class='col-sm-3'><label for='miglaNotifEmails' class='control-label text-right-sm text-center-xs'>" . esc_html__( 'emails to notify upon new donations', 'migla-donation' );
		echo "</label></div>";
		echo "<div class='col-sm-6 col-xs-12'><input class='form-control' id='miglaNotifEmails' type='text' value='".$nEmails."' />";
		echo "</div><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button id='miglaUpdateNotifEmails' class='btn btn-info pbutton miglaThankEmail' value='save'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation"). "</button></div><span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>". __("Use commas to separate emails","migla-donation"). "</span>";

		echo "</div>";


// The email it appears from

echo "<div class='row'><div class='col-sm-3'><label for='miglaReplyToTxt' class='control-label text-right-sm text-center-xs'>".esc_html__( 'Email Address: ', 'migla-donation' );
echo "</label></div><div class='col-sm-6 col-xs-12'>
		
		
<input type='text' id='miglaReplyToTxt' placeholder='".get_option('migla_replyTo')."' value='".get_option('migla_replyTo')."' class='form-control'></div><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button value='save' class='btn btn-info pbutton' id='miglaReplyTo'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation"). "</button></div><span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>". __("The is the address all your emails will appear from when a donor receives an email","migla-donation"). "</span></div>";


// The name the email appears from


echo "<div class='row'><div class='col-sm-3'><label for='miglaReplyToNameTxt' class='control-label text-right-sm text-center-xs'>".esc_html__( 'Email Name : ', 'migla-donation' );
echo "</label></div><div class='col-sm-6 col-xs-12'>

<input type='text' id='miglaReplyToNameTxt' class='form-control' placeholder='".get_option('migla_replyToName')."' value='".get_option('migla_replyToName')."'class='form-control' /></div><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button id='miglaReplyToName' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation"). "</button></div><span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>". __("This is the name that all of your emails will appear from","migla-donation"). "</span></div>";


  if( get_option('migla_thankyou_url') != '' )
  {

  echo "<div class='row'><div class='col-sm-3 col-xs-12'><label for='miglaSetThankYouPage' class='control-label text-right-sm text-center-xs'>". __("The Thank You Page From Short Code:","migla-donation");
  echo "</label></div>";
  
  echo "<div class='col-sm-6 col-xs-12'>"; 
  echo get_option('migla_thankyou_url') ;
  echo "</div><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div></div>"; 

  }else{

  echo "<div class='row'><div class='col-sm-3 col-xs-12'><label for='miglaSetThankYouPage' class='control-label text-right-sm text-center-xs'>". __("Set The Thank You Page Here:","migla-donation");
  echo "</label></div>";
  
  $page_id = get_option('migla_thank_you_page');
  $pages   = $this->get_all_posts(); 
  $is_page_exist = false;

  echo "<div class='col-sm-6 col-xs-12'><select id='miglaSetThankYouPage' name='miglaSetThankYouPage'>"; 
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
     echo "<option value=''  selected>". __("Default (Go to Donation Form)","migla-donation"). "</option>"; 
  }else{
     echo "<option value='' >". __("Default (Go to Donation Form)","migla-donation"). "</option>"; 
  }
  
  echo "</select></div><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button value='save' class='btn btn-info pbutton' id='miglaSetThankYouPageButton'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation"). "</button></div><span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>". __(" If you set the thank you page to any page other than the default, you must add this shortcode to this page:","migla-donation"). "<code>". __("[totaldonations_thank_you_page]","migla-donation"). "</code></span></div>"; 

}













echo "</div></section></div>";

// New Panel

		echo "<div class='col-sm-12'>";
		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down ' data-toggle='collapse' data-parent='.panel' href='#collapseOne' aria-expanded='true'></a></div><h2 class='panel-title'><div class='dashicons dashicons-plus'></div>". __("Thank You Page","migla-donation"). "<span class='panel-subtitle'>". __("The page that appears after you donate","migla-donation"). "</span></h2></header>";
		echo "<div id='collapseOne' class='panel-body collapse in'>";
		echo "<div class='row'>";
		echo "<div class='col-sm-12'>";
	
/*******************   Tiny MCE    *******************************/
$content = get_option( 'migla_thankyoupage' );
echo "<div id='content' style='display:none'>".$content."</div>";

$settings =   array(
    'wpautop' => true, // use wpautop?
    'media_buttons' => true, // show insert/upload button(s)
    'textarea_name' => 'migla_editor', // set the textarea name to something different, square brackets [] can be used here
    'textarea_rows' => 30, // rows="..."
    'tinymce' => true
);
wp_editor(  stripslashes($content) , 'migla_editor', $settings  );

echo "</div>";

echo "<span>";
echo "<div class='col-sm-12 '><button id='miglaThankPage' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation"). "</button> ";

echo "<div id='migla_urlshortcode' style='display:none'></div>";
echo "<form id='miglaFormPreviewThank' style='display:inline;' action='' method='post' target='_blank' >";
echo "<input type='hidden' name='thanks' value='testThanks' />";
echo "</form>";
echo "<button id='miglaThankPagePrev' class='btn btn-info obutton' value='Preview Page'><i class='fa fa-fw fa-search'></i>". __(" Preview","migla-donation"). "</button>";

echo "</span>";

echo "&nbsp;&nbsp;Shortcodes allowed: <code>[firstname][lastname][amount][date]</code><br></div>";
echo "</div></section></div>";
		

/*********************************************************************************************/
/*
		echo "<div class='col-sm-12'>";
		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down ' data-toggle='collapse' data-parent='.panel' href='#collapseEight' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa fa-envelope-o'></i>". __("Thank you Email","migla-donation"). "</h2></header>";
		echo "<div id='collapseEight' class='panel-body collapse in' >";

   echo "<div class='row'><div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>". __("Preview PDF receipt","migla-donation"). "</label></div><div class='col-sm-9 col-xs-12 text-left-sm text-center-xs'><button id='mg_preview_pdf' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation")."</button></div><div class='col-sm-3 hidden-xs'></div></div>";


echo "</div></section></div>";
*/
/***************************************************************************/
		
		// new panel


		echo "<div class='col-sm-12'>";
		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down ' data-toggle='collapse' data-parent='.panel' href='#collapseTwo' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa fa-envelope-o'></i>". __("Thank you Email","migla-donation"). "</h2></header>";
		echo "<div id='collapseTwo' class='panel-body collapse in' >";
		
if( get_option('migla_disable_thank_email') == 'yes' ){
   echo "<div class='row'><div class='col-sm-3 col-xs-12'><label for='mNoThankyouEmailCheck' class='control-label text-right-sm text-center-xs'>". __("Disable thank you emails:","migla-donation")." </label></div><div class='col-sm-9 col-xs-12 text-left-sm text-center-xs'>";
   echo "<label class='checkbox-inline' for='mNoThankyouEmailCheck'><input  type='checkbox' checked id='mNoThankyouEmailCheck' name='mNoThankyouEmailCheck'>". __(" Check this if you'd like no thank you email to be sent to your donors after they donate","migla-donation")."  </label></div><div class='col-sm-3 hidden-xs'></div></div>";
}else{
   echo "<div class='row'><div class='col-sm-3 col-xs-12'><label for='mNoThankyouEmailCheck' class='control-label text-right-sm text-center-xs'>". __(" Disable thank you emails:","migla-donation")." </label></div><div class='col-sm-9 col-xs-12 text-left-sm text-center-xs'>";
   echo "<label class='checkbox-inline'  for='mNoThankyouEmailCheck'><input type='checkbox' id='mNoThankyouEmailCheck' name='mNoThankyouEmailCheck'>". __(" Check this if you'd like no thank you email to be sent to your donors after they donate","migla-donation")."  </label></div><div class='col-sm-3 hidden-xs'></div></div>";
}

// Form Grouping for Astried

echo "<div class='form-horizontal'> <div class='form-group touching'><div class='col-sm-3 col-xs-12'><label for='migla_thankSbj' class=' control-label text-right-sm text-center-xs'>". __(" Email Subject:","migla-donation")."</label>
  </div><div class='col-sm-6 col-xs-12'><input type='text' name='migla_thankSbj' id='migla_thankSbj' class='form-control touch-top' title='Please enter subject of email' placeholder='' required='' value='".get_option('migla_thankSbj')."'></div>
<div class='col-sm-3 hidden-xs'></div></div><div class='form-group touching '><div class='col-sm-3'><label for='miglaThankBody' class=' control-label text-right-sm text-center-xs'>". __(" Thank you Email Text Body: ","migla-donation"). "</label>
 </div><div class='col-sm-6 col-xs-12'>"; 

echo "<textarea type='text' id='miglaThankBody' class='form-control touch-middle'  cols='50' rows='6' name='miglaThankEmailTxt'>";

$thankstr = get_option( 'migla_thankBody' );

echo $thankstr;

echo "</textarea></div><div class='col-sm-3'> </div> </div><div class='form-group touching '><div class='col-sm-3 col-xs-12'><label for='migla_thankRepeat' class='control-label text-right-sm text-center-xs'>". __("Repeating Donations:","migla-donation")."</label> </div><div class='col-sm-6 col-xs-12'><input required='' placeholder='' class='form-control touch-middle' title='' rows='5' name='migla_thankRepeat' id='migla_thankRepeat' style='overflow: hidden;' value='".get_option('migla_thankRepeat')."'></div>	
<div class='col-sm-3 hidden-xs'> </div></div>


<div class='form-group touching '>
									<div class='col-sm-3 col-xs-12'>		<label for='migla_thankAnon' class='control-label text-right-sm text-center-xs'>". __("Anonymous Donations:","migla-donation"). "</label> </div>
											<div class='col-sm-6 col-xs-12'>
												<input required='' placeholder='' class='form-control touch-middle' title='' rows='5' name='migla_thankAnon' id='migla_thankAnon' style='overflow: hidden;' value='".get_option('migla_thankAnon')."'>
												
												
								</div>				
												
												
											


<div class='col-sm-3 hidden-xs'> </div>

										</div>

  
  
  
  <div class='form-group'>
									<div class='col-sm-3 col-xs-12'>		<label for='migla_thankSig' class='control-label text-right-sm text-center-xs'>". __("Signature:","migla-donation"). "</label> </div>
											<div class='col-sm-6 col-xs-12'>
												<input required='' placeholder='' class='form-control touch-bottom' title='' rows='5' name='migla_thankSig' id='migla_thankSig' style='overflow: hidden;' value='".get_option('migla_thankSig')."'>

<div style='border:none' class='row'><br><label class='col-sm-6 help-control'>". __(" Use the following shortcodes in the email body:","migla-donation"). "</label>

<div class='col-sm-6'><code>[firstname]</code>". __(" Donor's First Name", "migla-donation"). "<br><code>[lastname]</code>". __(" Donor's Last Name","migla-donation"). "<br><code>[amount]</code>". __(" Donation Amount","migla-donation"). "<br><code>[date]</code>". __(" Donation date ","migla-donation")."<br><code>[newline] </code>". __(" Line Break ","migla-donation")."</div></div>
												
												
								</div>												


<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button id='miglaThankEmail' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation")."</button>  </div>

										 </div>

</div>";


  echo "<div class='row'><div class='col-sm-3'><label for='miglaTestEmailAdd' class='control-label text-right-sm text-center-xs'>" . esc_html__( 'Email address for Test:', 'migla-donation' );
		echo "</label></div>";
		echo "<div class='col-sm-6 col-xs-12'><input class='form-control' id='miglaTestEmailAdd' type='text' value='' />";
		echo "</div><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button id='miglaTestEmail' class='btn btn-info obutton' value='Send Testing Email'><i class='fa fa-fw fa-envelope-o'></i>". __(" Preview Email","migla-donation"). "</button></div><span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>". __(" Use this to preivew what your donors will see when they donate.","migla-donation"). "</span> </div></div></div>";


// Honoreee Email Panel


echo "<div class='col-sm-12'>";

echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseThree' aria-expanded='true'></a></div><h2 class='panel-title'><div class='dashicons dashicons-plus'></div>". __("Honoree Email","migla-donation"). "</h2></header>";
		echo "<div id='collapseThree' class='panel-body collapse in'>";

if( get_option('migla_disable_honoree_email') == 'yes' ){		
echo "<div class='row'><div class='col-sm-3 col-xs-12'><label for='mNoHonoreeEmailCheck' class='control-label text-right-sm text-center-xs'>". __(" Disable thank you emails:","migla-donation"). "</label></div><div class='col-sm-9 col-xs-12 text-left-sm text-center-xs'><label class='checkbox-inline' for='mNoHonoreeEmailCheck'><input  type='checkbox' checked id='mNoHonoreeEmailCheck' name='mNoHonoreeEmailCheck'>". __(" Check this if you'd like no thank you email to be sent to the Honoree after someone has donated in their honor","migla-donation")."  </label></div><div class='col-sm-3 hidden-xs'></div></div>";
}else{
echo "<div class='row'><div class='col-sm-3 col-xs-12'><label for='mNoHonoreeEmailCheck' class='control-label text-right-sm text-center-xs'>". __(" Disable thank you emails:","migla-donation"). "</label></div><div class='col-sm-9 col-xs-12 text-left-sm text-center-xs'><label class='checkbox-inline' for='mNoHonoreeEmailCheck'><input type='checkbox' id='mNoHonoreeEmailCheck' name='mNoHonoreeEmailCheck'>". __(" Check this if you'd like no thank you email to be sent to the Honoree after someone has donated in their honor","migla-donation")."  </label></div><div class='col-sm-3 hidden-xs'></div></div>";
}

echo "<div class='form-horizontal'> <div class='form-group touching'><div class='col-sm-3 col-xs-12'><label for='migla_honoreESbj' class=' control-label text-right-sm text-center-xs'>". __("Email Subject:","migla-donation"). "</label>
  </div><div class='col-sm-6 col-xs-12'><input type='text' name='migla_honoreESbj' id='migla_honoreESbj' class='form-control touch-top' title='Plase enter a name.' placeholder='' required='' value='".get_option('migla_honoreESbj')."'></div>
<div class='col-sm-3 hidden-xs'></div></div><div class='form-group touching '><div class='col-sm-3'><label for='migla_honoreEBody' class=' control-label text-right-sm text-center-xs'>". __("Thank you Email Text Body: ","migla-donation"). "</label>
 </div><div class='col-sm-6 col-xs-12'>"; 

echo "<textarea   id='migla_honoreEBody' class='form-control touch-middle'  cols='50' rows='6' name=''>";

$thankstr = get_option('migla_honoreEBody');

echo $thankstr;

echo "</textarea></div><div class='col-sm-3'> </div> </div>";

echo "<div class='form-group touching '><div class='col-sm-3 col-xs-12'><label for='migla_honoreECustomIntro' class='control-label text-right-sm text-center-xs'>". __("Custom Message Intro:","migla-donation"). "</label> </div>
<div class='col-sm-6 col-xs-12'><input value='".get_option('migla_honoreECustomIntro')."' id='migla_honoreECustomIntro' name='migla_honoreECustomIntro' rows='5' title='' class='form-control touch-middle' placeholder='' required=''>
</div> <div class='col-sm-3 hidden-xs'> </div></div>";

echo "<div class='form-group touching '><div class='col-sm-3 col-xs-12'><label for='migla_honoreERepeat' class='control-label text-right-sm text-center-xs'>". __("Repeating Donations:","migla-donation"). "</label> </div><div class='col-sm-6 col-xs-12'><input required='' placeholder='' class='form-control touch-middle' title='' rows='5' name='migla_honoreERepeat' id='migla_honoreERepeat' style='overflow: hidden;' value='".get_option('migla_honoreERepeat')."'></div>	
<div class='col-sm-3 hidden-xs'> </div></div>";


echo "<div class='form-group touching '>
									<div class='col-sm-3 col-xs-12'>		<label for='migla_honoreEAnon' class='control-label text-right-sm text-center-xs'>". __("Anonymous Donations:","migla-donation"). "</label> </div>
											<div class='col-sm-6 col-xs-12'>
												<input required='' placeholder='' class='form-control touch-middle' title='' rows='5' name='migla_honoreEAnon' id='migla_honoreEAnon' style='overflow: hidden;' value='".get_option('migla_honoreEAnon')."'>
												
												
								</div>				
												
												
											


<div class='col-sm-3 hidden-xs'> </div>

										</div>

  
  
  
  <div class='form-group'>
									<div class='col-sm-3 col-xs-12'>		<label for='migla_honoreESig' class='control-label text-right-sm text-center-xs'>". __("Signature:","migla-donation"). "</label> </div>
											<div class='col-sm-6 col-xs-12'>
												<input required='' placeholder='' class='form-control touch-bottom' title='' rows='5' name='migla_honoreESig' id='migla_honoreESig' style='overflow: hidden;' value='".get_option('migla_honoreESig')."'>

<div style='border:none' class='row'><br><label class='col-sm-6 help-control'>". __(" Use the following shortcodes in the email body:","migla-donation"). "</label>

<div class='col-sm-6'> <code>[honoreename]</code>". __(" Honoree's Name","migla-donation"). "<br> <code>[firstname]</code>". __(" Donor's First Name","migla-donation"). "<br><code>[lastname]</code>". __(" Donor's Last Name","migla-donation"). "<br> <code>[amount]</code>". __("Donation Amount","migla-donation"). "<br><code>[date]</code> Donation date <br><code>[newline] </code> ". __(" Line Break","migla-donation"). "</div></div>
												
												
								</div>			
<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button id='miglaHonoreEmail' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>". __("save","migla-donation"). "</button>  </div>

										</div> 




</div> 



<div class='row'><div class='col-sm-3'><label for='miglaTestHEmailAdd' class='control-label text-right-sm text-center-xs'>" . esc_html__( 'Email address for Honoree Email Test:', 'migla-donation' );
		echo "</label></div>";
		echo "<div class='col-sm-6 col-xs-12'><input class='form-control' id='miglaTestHEmailAdd' type='text' value='' />";
		echo "</div><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button id='miglaTestHEmail' class='btn btn-info obutton' value='Send Testing Email'><i class='fa fa-fw fa-envelope-o'></i>". __(" Preview Email","migla-donation"). "</button></div><span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>". __(" Use this to preivew what the honoree will see when they receive a message.","migla-donation"). "</span> </div></div>   
</div>";




		///////////////////////////////////////////////////////////////
		// Timezones Section	
		
		
       ///GET CURRENT TIME SETTINGS----------------------------------
	$php_time_zone 	= date_default_timezone_get();
    $default 		= get_option('migla_default_timezone');
	
	$language 		= get_option('migla_default_datelanguage');
	$date_format 	= get_option('migla_default_dateformat');
	
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
       ///---------------------------------GET CURRENT TIME SETTINGS

	echo "<div class='col-sm-12'>";	
	$timezones = get_option( 'migla_timezones' );
		
	echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseSeven' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa fa-fw fa-clock-o'></i>". __(" Default Time Zone Section","migla-donation"). "</h2></header>";
	echo "<div id='collapseSeven' class='panel-body collapse in'>";
	
	echo "<div class='row'><div class='col-sm-3 col-xs-12'>";
	echo "<label for='miglaDefaultLanguage' class='control-label text-right-sm text-center-xs'>". __("Set Date Language","migla-donation");
    echo "</label></div>";
    echo "<div class='col-sm-6 col-xs-12'>";
    echo "<select id='miglaDefaultLanguage' name='' class=''>";
    
	$lang 	= (array)migla_get_local();
    $keys 	= array_keys($lang); 
	$i 		= 0;
    
	foreach( (array)$lang as $value)
    {
        if( $value == $language )
        {
            echo "<option value='".$value."' selected>". $keys[$i] ." ( ". $value .") </option>";
        }else{
            echo "<option value='".$value."' >". $keys[$i] ." ( ". $value .") </option>";
        }
        $i++;
    }
	
    echo "</select>";	
	echo "</div>";	  
	echo "</div>";		
		  
	echo "<div class='row'><div class='col-sm-3 col-xs-12'><label for='miglaDefaultDateFormat' class='control-label text-right-sm text-center-xs'>". __("Set Date Format","migla-donation");
    echo "</label></div>";
    echo "<div class='col-sm-6 col-xs-12'>";
    echo "<select id='miglaDefaultDateFormat' name='' class=''>";

    $df 	= array('%B %d %Y', '%b %d %Y', '%B %d, %Y', '%b %d, %Y' , '%d %B %Y', '%d %b %Y' ,'%Y-%m-%d', '%m/%d/%Y');
    $keys 	= array_keys($df); 
	$i 		= 0;
	
    setlocale(LC_TIME, $language);

        foreach( $df as $value )
        {
           if( $value == $date_format )
           {
             echo "<option value='".$value."' selected>". strftime($value, time())  . "</option>";
           }else{
             echo "<option value='".$value."' >". strftime($value , time())  . "</option>";
           }
           $i++;
        }
    echo "</select>";	
	echo "</div>";	  
	echo "</div>";	  
		  
	echo "<div class='row'><div class='col-sm-3 col-xs-12'><label for='miglaDefaultTimezone' class='control-label text-right-sm text-center-xs'>". __("Set Time Zone","migla-donation");
    echo "</label></div>";
    echo "<div class='col-sm-6 col-xs-12'><select id='miglaDefaultTimezone' name='miglaDefaultTimezone'>"; 
    echo "<option value='Server Time' >". __("Server Time","migla-donation")."</option>"; 
	   foreach ( (array) $timezones as $key => $value ) 
	   { 
	      if ( $value == get_option( 'migla_default_timezone' ) )
		   { 
		     echo "<option value='".$value."' selected >".$key."</option>"; 
		  }else{  
		    echo "<option value='".$value."'>".$key."</option>"; 
		  }
	   }	   
	echo "</select></div><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button value='save' class='btn btn-info pbutton' id='miglaSetTimezoneButton'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation"). "</button></div>
			<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>". __(" Set your timezone here","migla-donation"). "</span>";
	echo "<span id='migla_current_time' class='time-control col-sm-12 col-sm-pull-3'><strong>".$now."</strong></span></div>"; 
		
		
		
		echo "<div></section>";	
		//echo "</div>";
		
		echo "</div>"; 

/**********************************************************************************/




              echo "</div></div>"; // row id=wrap
		
	}

}

?>