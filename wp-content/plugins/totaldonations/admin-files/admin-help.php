<?php
class migla_help_class {

	function __construct() {
		add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 19 );
	}
	
	function menu_item() {
		add_submenu_page(
			'migla_donation_menu_page',
			__( 'Help & Advanced', 'migla-donation' ),
			__( 'Help & Advanced', 'migla-donation' ),
			'manage_options',
			'migla_donation_help',
			array( $this, 'menu_page' )
		);
	}
	
	function menu_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'migla-donation' ) );
		}
 
 echo "<div class='wrap'><div class='container-fluid'>";                		
 
                echo "<h2 class='migla'>". esc_html__('Help', 'migla-donation'). "</h2>";
               
		
		 echo "";
		 
		 echo "<div class='row'>";



echo "<div class='col-md-6 col-lg-6 col-xl-12'>
									<section class='panel panel-featured-left panel-featured-primary'>
										<div class='panel-body'><h2 class='panel-title'>". __('Advanced/Technical Settings','migla-donation')." </h2>";


/*
	echo "<div class='row'><br><div class='col-sm-8'><label class='check-control '>";
	
        if( get_option('migla_show_recover') == 'yes' ){
           echo "<input type='checkbox' id='migla_show_recover' class='mg-settings' checked />". __('Show Recovery Buttons', 'migla-donation')."</label>";
        }else{
           echo "<input type='checkbox' id='migla_show_recover' class='mg-settings' />". __('Show Recovery Buttons', 'migla-donation')."</label>";
        }
        echo "</div><div class='col-sm-4' id='migla_show_recover_'></div><span class='help-control col-sm-12'>". __('This button will show on the edit form report','migla-donation')."</span></div>";
*/



        
	
        echo "<div class='row'><br><div class='col-sm-12'>". __('Erase transient cache for data more than one day old.','migla-donation')." </div>";

echo "<div class='col-sm-12 '><br><button id='miglaEraseCache' style='width:120px' class='btn btn-info obutton ' value='save'><i class='fa fa-fw fa-times'></i>". __(" erase","migla-donation"). "</button></div>";

echo "<br><span class='help-control-left col-sm-12'>". __('Clicking this button will erase old cache data stored in WordPress by Total Donations','migla-donation')."</span></div>";


	echo "<div class='row'><div class='col-sm-9'><label class='check-control '>";
	
        if( get_option('migla_use_nonce') == 'yes' ){
              echo "<input type='checkbox' class='mg-settings' id='migla_use_nonce' checked />". __('Use nonce security on frontend form', 'migla-donation')."</label>";
        }else{
              echo "<input type='checkbox' class='mg-settings' id='migla_use_nonce' />". __('Use nonce security on frontend form', 'migla-donation')."</label>";
        }
        echo "</div><div class='col-sm-3  col-xs-12' id='migla_use_nonce_'></div><span class='col-sm-12 help-control-left'>". __('This is security against xss attacks. Disable if you have issues with compatibility','migla-donation')."</span></div>";


	echo "<div class='row'><div class='col-sm-9'><label class='check-control '>";
	
        if( get_option('migla_delete_settings') == 'yes' ){
              echo "<input type='checkbox' class='mg-settings' id='migla_delete_settings' checked />". __('Reset all settings to default when plugin is deactivated', 'migla-donation')."</label>";
        }else{
              echo "<input type='checkbox' class='mg-settings' id='migla_delete_settings' />". __('Reset all settings to default when plugin is deactivated', 'migla-donation')."</label>";
        }
        echo "</div><div class='col-sm-3  col-xs-12' id='migla_delete_settings_'></div><div class='col-sm-12 col-xs-12'><div class='help-control-left'>". __('When the plugin is activated again it will use the default settings','migla-donation')."</span></div></div></div>";


echo "<div class='row'><div class='col-sm-8'><label class='check-control '>";
	$aj = get_option('migla_ajax_caller');
        if( $aj == false ){ 
             update_option('migla_ajax_caller', 'td'); $ap = get_option('migla_ajax_caller'); 
        } 
        if( get_option('migla_ajax_caller') == 'wp' ){
              echo "<input type='checkbox' class='' id='migla_ajax_caller_setting' checked />". __('Change the ajax caller to the default Wordpress Ajax caller. ', 'migla-donation')."</label>";
        }else{
              echo "<input type='checkbox' class='' id='migla_ajax_caller_setting' />". __('Change the ajax caller to the default Wordpress Ajax caller.', 'migla-donation')."</label>";
        }
        echo "</div><div class='col-sm-4  col-xs-12' id='migla_ajax_caller_setting_'></div><div class='col-sm-12 col-xs-12'><div class='help-control-left'>". __('Do this if your site wont save settings. It is not as fast but might help.','migla-donation')."</span></div></div></div>";


echo "<div class='row'><div class='col-sm-8'><label class='check-control '>";
	$allow_cors = get_option('migla_allow_cors');
        if( $allow_cors == false ){ 
             update_option('migla_allow_cors', 'no'); 
             $allow_cors = get_option('migla_allow_cors');
        } 
        if( $allow_cors  == 'yes' ){
              echo "<input type='checkbox' class='' id='migla_allow_cors_setting' checked />". __('Allows across domain request (CORS) for the AJAX caller. ', 'migla-donation')."</label>";
        }else{
              echo "<input type='checkbox' class='' id='migla_allow_cors_setting' />". __('Allows a cross domain request (CORS) for the AJAX caller. ', 'migla-donation')."</label>";
        }
        echo "</div><div class='col-sm-4  col-xs-12' id='migla_allow_cors_setting_'></div><div class='col-sm-12 col-xs-12'><div class='help-control-left'>". __('Use this if your site blocks the AJAX caller from Total Donation on the front-end form.','migla-donation')."</span></div></div></div>";


/* load JS in footer or header */


       $load_js_pos = get_option('migla_script_load_js_pos');

	      echo "<div class='row'>";
	   echo "<div class='col-sm-12'><label for='migla_script_js_load_pos' class='text-right-sm text-center-xs'>". __("Load javascript on","migla-donation");
        echo "</label></div>";
       echo "<br><div class='col-sm-8'><select id='migla_script_load_js_pos' name='migla_script_load_js_pos'>";

       if( $load_js_pos == 'footer' ){ 
           echo "<option value='footer' selected >footer</option>"; 
           echo "<option value='head' >head</option>"; 
        }else{
           echo "<option value='footer' >footer</option>"; 
           echo "<option value='head'  selected >head</option>"; 
        } 
	   echo "</select></div><div class='col-sm-12 text-left-sm text-center-xs'><br><button value='save' class='btn btn-info pbutton' id='miglaSetJSScriptLoad'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation"). "</button></div><div class='col-sm-12 col-xs-12'><div class='help-control-left'>". __('Here you can change where the Javascript gets loaded.','migla-donation')."</span></div>";
          echo "</div></div>"; 



/* load CSS in header or footer */


       $load_css_pos = get_option('migla_script_load_css_pos');

	      echo "<div class='row'>";
	   echo "<div class='col-sm-12'><label for='migla_script_load_css_pos' class='text-right-sm text-center-xs'>". __("Load stylesheet  on","migla-donation");
        echo "</label></div>";
       echo "<br><div class='col-sm-8'><select id='migla_script_load_css_pos' name='migla_script_load_css_pos'>";

       if( $load_css_pos == 'footer' ){ 
           echo "<option value='footer' selected >footer</option>"; 
           echo "<option value='head' >head</option>"; 
        }else{
           echo "<option value='footer' >footer</option>"; 
           echo "<option value='head'  selected >head</option>"; 
        } 
	   echo "</select></div><div class='col-sm-12 text-left-sm text-center-xs'><br><button value='save' class='btn btn-info pbutton' id='miglaSetCSSScriptLoad'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation"). "</button></div><div class='col-sm-12 col-xs-12'><div class='help-control-left'>". __('Here you can change where the stylesheet gets loaded.','migla-donation')."</span></div>";
          echo "</div>"; 

echo "</div>";


/* end options for css loading */


echo "</div></section></div>";


/********** User access start here ***************/

  global $wpdb;
  $wp_users = $wpdb->get_results("SELECT ID, display_name FROM $wpdb->users ORDER BY ID");
  $list = (array)get_option('migla_allowed_users');
  $level = 10;
  echo "<div class='col-md-6 col-lg-6 col-xl-12'><section class='panel panel-featured-left panel-featured-primary'><div class='panel-body'><h2 class='panel-title'>".__("Grant User Access:","migla-donation")."  <span class='panel-subtitle'>".__("All Users other than administers will not be able to change user access.","migla-donation")." </span></h2>";
  echo "<div class='row'><div class='col-sm-8'><label class='check-control '>".__("Give access to these user :","migla-donation")."</label>";
  echo "<br><br><ul>";
  
 /** SHOW THE USER LIST **/	
   $cap_list = ''; $cap_array = array(); $cap_j = 0;

  foreach ( $wp_users as $user ) {

    $user_id       	= (int)$user->ID;
    $display_name  	= stripslashes($user->display_name); 
    $u             	= get_userdata($user_id);
	$is_admin		= user_can( $user_id, 'administrator' );
	
	$caps 			= $u->caps ;
	$caps_key 		= array_keys($caps);
	
	for( $i = 0 ; $i < count($caps_key); $i++ )
	{
		if( $caps[$caps_key[$i]] == '1' || $caps[$caps_key[$i]] == true )
		{
			if( !in_array($caps_key[$i], $cap_array) )
			{
				$cap_list .= "<input type='hidden' class='mg_user_caps' value='". $caps_key[$i] ."'>";
				$cap_array[$cap_j] = $caps_key[$i];
				$cap_j++;
			}
		}
	}
		
	if( $is_admin ){
       echo "<li class='mg_li_user' id='".$user_id."'><label for='mg_user".$user_id."'>";
       echo "<input id='mg_user".$user_id."' type='checkbox' class='mg-settings' checked disabled /> ". $display_name. "</label></li>";	
	}else if( in_array( $user_id , $list ) )
	{
       echo "<li class='mg_li_user' id='".$user_id."'><label for='mg_user".$user_id."'>";
       echo "<input id='mg_user".$user_id."' type='checkbox' class='mg-settings' checked /> ". $display_name. "</label></li>";
    }else{
       echo "<li class='mg_li_user' id='".$user_id."'><label for='mg_user".$user_id."'>";
       echo "<input id='mg_user".$user_id."' type='checkbox' class='mg-settings' /> ". $display_name. "</label></li>";     }
	}
  echo "</ul>";

  echo $cap_list;  
  
	/** SHOW CURRENT USER **/
	global $current_user;
	$curr_caps			= $current_user->caps;
	$curr_caps_key 		= array_keys($curr_caps);
	$cur_is_allowed		= false;
	$allowed_cap_curr	= 'administrator';
	$ok_found			= false;
	$get_allowed_caps	= (array)get_option( 'migla_allowed_capabilities' );
	
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
		  
	echo $allowed_cap_curr ;
	
  echo "</div>";

echo "<div class='col-sm-12 '><br><button id='miglaSaveUserList' style='width:150px' class='btn btn-info pbutton ' value='save'><i class='fa fa-fw fa-save'></i>". __(" save user access","migla-donation"). "</button></div>";

  echo "</div>";

/********** User access end here ***************/


echo "</div></section></div>";



echo "<div class='col-md-6 col-lg-6 col-xl-12'>
									<section class='panel panel-featured-left panel-featured-primary'>
										<div class='panel-body'>

<div class='widget-summary'>
											
											
                      <h2 class='panel-title'>".__("Documentation","migla-donation ")."</h2>


<br><br>
 <i class='fa fa-fw fa-heart-o'></i>&nbsp;".__("Get started by going ","migla-donation ")."<a href='http://totaldonations.com/knowledgebase_category/setting-up-the-plugin/'>".__(" here ","migla-donation")."</a>. 
<br><br>
 <i class='fa fa-fw fa-plane'></i>&nbsp;".__("Visit ","migla-donation")."<a href='http://totaldonations.com/knowledgebase'>".__("here for complete documentation ","migla-donation")."</a>. 
<br><br>
<i class='fa fa-fw fa-question'></i>&nbsp;".__("Visit ","migla-donation")."<a href='http://totaldonations.com/knowledgebase_category/shortcodes/'>".__("here for shortcode examples and arguments","migla-donation")."</a>. 
<br><br>
</div>

</div>
									</section>
								</div>";
		 
	
echo "<div class='col-md-6 col-lg-6 col-xl-12'>
									<section class='panel panel-featured-left panel-featured-primary'>
										<div class='panel-body'>

<div class='widget-summary shortcode-list'>
						

<h2 class='panel-title'>".__("ShortCode List","migla-donation")."<span class='panel-subtitle'>".__("See all the attributes for these shortcodes by clicking on the shortcode example link above","migla-donation")."</span></h2>";

							  echo  "";
									
									
									
      global $shortcode_tags;
      $arr = $shortcode_tags;
      foreach( (array)$arr as $key => $value ) {
        if( substr($key, 0, 14) == 'totaldonations' ){
         echo "<p><code>[". $key ."]</code></p>";
        }
      }
			
		echo  "</div>









</div>
									</section>
								</div>
"; 

                                                     echo "</div></div></div>";			
							
							
							
				
							
							
							
							
							
							
							
							
							
							
							
							
							
							
               
               
               
                }
              }
?>