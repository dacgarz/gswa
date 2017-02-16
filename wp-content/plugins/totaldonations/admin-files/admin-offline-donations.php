<?php
class migla_offline_donations_class{

function __construct(){
  add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 7 );
}

	
function menu_item() {
 add_submenu_page( 
   'migla_donation_menu_page',
   __( 'Offline Donations', 'migla-donation' ),
   __( 'Offline Donations', 'migla-donation' ),
   $this->get_capability(),
   'migla_offline_donations_page',
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
   
   function get_countries()
   {
		$str = '';
		
		$countries = get_option('migla_world_countries');
		foreach( $countries as $code => $country )
		{
			$str .= "<option value='".$country."'>".$country."</option>";
		}
		
		return $str;
   }
   
   function get_provinces()
   {
		$str = '';
		
		$provinces = get_option('migla_Canada_provinces');
		foreach( $provinces as $code => $province )
		{
			$str .= "<option value='".$province."'>".$province."</option>";
		}
		
		return $str;
   }

   function get_states()
   {
		$str = '';
		
		$states = get_option('migla_US_states');
		foreach( $states as $code => $state )
		{
			$str .= "<option value='".$state."'>".$state."</option>";
		}
		
		return $str;
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
 
   global $wpdb;
 
 echo "<div class='wrap'><div class='container-fluid'>";

 echo "<div id='mg_load_image' style='display:none !important'><img src='".plugins_url('totaldonations/images/loading.gif')."'></div>";
 echo "<div id='mg_load_image_circle' style='display:none !important'><img src='".plugins_url('totaldonations/images/loading-boots.gif')."'></div>";

   echo "<h2 class='migla'>". __( "Offline donations","migla-donation")."</h2>";
   
   echo "<div class='row'>";
   echo "<div class='col-sm-12'>";
   
        $x[0] = get_option('migla_thousandSep');
        $x[1] = get_option('migla_decimalSep');
        $showSep = get_option('migla_showDecimalSep');
        $numDecimal = 0;
		
	echo "<input type='hidden' id='mg_thousand_separator' value='".$x[0]."'>";	
	echo "<input type='hidden' id='mg_decimal_separator' value='".$x[1]."'>";	
	echo "<input type='hidden' id='mg_show_separator' value='".$showSep ."'>";	   
   
   echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down ' data-toggle='collapse' data-parent='.panel' href='#collapseOne' aria-expanded='true'></a></div><h2 class='panel-title'><div class='dashicons dashicons-plus'></div>". __( "Add Offline Donation", "migla-donation")."</h2></header>";
   echo "<div id='collapseOne' class='panel-body collapse in'>";
   echo "<div class='form-horizontal'>";
   
   echo "<input type='hidden' id='mg_hdn_loaded_form' value='' />";
   
   echo "<div class='row'><div class='form-group touching'><div class='col-sm-3 col-xs-12'>";
   echo "<label class='control-label  text-right-sm text-center-xs' for='mg_campaigns'>". __( "Choose Campaign : ", "migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12'>";
   echo "<select id='mg_campaigns' name='' >";
   //echo "<option value='' selected>Default Multi Campaign Form</option>";

   echo "<option value='".get_option('migla_undesignLabel')."' selected>".get_option('migla_undesignLabel')."</option>";
   
	$campaigns = get_option('migla_campaign');
	if( $campaigns != false )
	{
		if( count($campaigns) > 0 )
		{
			foreach( $campaigns as $c )
			{
				echo "<option value='".$c['name']."'>".str_ireplace( "[q]", "'", $c['name'] )."</option>";
			}
		}
	}
	
   echo "</select>";
   echo "</div><div class='col-sm-3 hidden-xs'></div></div></div>";   

   echo "<div id='mg_row_form' class='row mg_linesrow'>";
   
	echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'>";
	echo "<label class='control-label  text-right-sm text-center-xs required' for='miglad_date'>". __( "Date: ", "migla-donation")."</label></div>";
	echo "<div class='col-sm-6 col-xs-12'><span class='input-group input-group-control mb-md'>";
	echo "<span class='input-group-addon btn-success dashicons dashicons-calendar '></span>";
	echo "<input class='miglaOffdate form-control custom_date required' type='text' name='miglad_date' size='10' value='' placeholder='' id='miglad_date'  /></span></div><div class='col-sm-3 hidden-xs'></div></div>";
  
  
	echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'>";
	echo "<label class='control-label  text-right-sm text-center-xs required' for='miglad_time'>". __( "Time (hh:mm:ss): ", "migla-donation")."</label></div>";
	

	echo "<div class='col-sm-2 col-xs-12'><div class='dropdown'>
		<input type='text' id='mg_time_hour' class='dropdown-toggle migla_time_hour' type='button' data-toggle='dropdown'>
		<ul class='dropdown-menu mg_time_scrollable-menu'>";
		for( $h = 0; $h <= 23; $h++ )
		{
			if( $h < 10 )
				echo "<li class='mg_time_li'><a href='#'>0".$h."</a></li>";
			else	
				echo "<li class='mg_time_li'><a href='#'>".$h."</a></li>";
		}
	echo "	</ul>
	</div></div>";

	echo "<div class='col-sm-2 col-xs-12'><div class='dropdown'>
		<input type='text' id='mg_time_minute' class='dropdown-toggle migla_time_mm_ss' type='button' data-toggle='dropdown'>
		<ul class='dropdown-menu mg_time_scrollable-menu'>";
		for( $m = 0; $m <= 59; $m++ )
		{
			if( $m < 10 )
				echo "<li class='mg_time_li'><a href='#'>0".$m."</a></li>";
			else	
				echo "<li class='mg_time_li'><a href='#'>".$m."</a></li>";
		}
	echo "	</ul>
	</div></div>";

	echo "<div class='col-sm-2 col-xs-12'><div class='dropdown'>
		<input type='text' id='mg_time_second' class='dropdown-toggle migla_time_mm_ss' type='button' data-toggle='dropdown'>
		<ul class='dropdown-menu mg_time_scrollable-menu'>";
		for( $s = 0; $s <= 59; $s++ )
		{
			if( $s < 10 )
				echo "<li class='mg_time_li'><a href='#'>0".$s."</a></li>";
			else	
				echo "<li class='mg_time_li'><a href='#'>".$s."</a></li>";
		}
	echo "	</ul>
	</div></div>";

		
	
	echo "<div class='col-sm-3 hidden-xs'></div></div>";
	
	
	
   echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'>";
   echo "<label class='control-label  text-right-sm text-center-xs' for='miglad_transactionType'>". __( "Transaction Type: ", "migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12'><input name='miglad_transactionType' id='miglad_transactionType' type='text'  size='10' value='' />";
   echo "</span></div><div class='col-sm-3 hidden-xs'></div></div>"; 
   
   echo "<div class='form-group touching mg_off_form_lines'><div class='col-sm-3 col-xs-12'>";
   echo "<label class='control-label  text-right-sm text-center-xs' for='miglad_orgname'>". __( "Organization: ", "migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12'><input id='miglad_orgname' class='mg_off_form_input' name='miglad_orgname' type='text'  size='10' value='' />";
   echo "</span></div><div class='col-sm-3 hidden-xs'></div></div>";   
     
	echo "</div>";
   
   echo "<div id='mg_row_form' class='row'>";
   
   $form = get_option( 'migla_form_fields' );
   
	if( !empty( $form ) && $form[0] !='')
	{
		foreach ( (array)$form as $fields )
		{   
			if ( count((array) $fields['child']) > 0 )
			{
				foreach ( (array)$fields['child'] as $field )
				{		
					if( $field['id'] == 'repeating' || $field['id'] == 'campaign' || $field['status'] == '0' || 
						$field['id'] == 'state' || $field['id'] == 'honoreestate'  ||
						$field['id'] == 'province' || $field['id'] == 'honoreeeprovince' 
						)
					{
					}else if( $field['id'] == 'country' )
					{
						echo "<div class='form-group touching mg_off_form_lines'><div class='col-sm-3 col-xs-12'>";
						echo "<label class='control-label  text-right-sm text-center-xs'>".__('Country', 'migla-donation')."</label></div>";
						echo "<div class='col-sm-6 col-xs-12'>";
						
						echo "<select id='miglad_country' name='miglad_country' class='mg_off_form_input' >";
						echo $this->get_countries();
						echo "</select>";
						
						echo "</div>";
						echo "<div class='col-sm-3 hidden-xs'></div>";
						echo "</div>";							

						echo "<div class='form-group touching mg_off_form_lines mg_off_form_lines_province' style='display:none'><div class='col-sm-3 col-xs-12'>";
						echo "<label class='control-label  text-right-sm text-center-xs'>".__('Province', 'migla-donation')."</label></div>";
						echo "<div class='col-sm-6 col-xs-12'>";
						
						echo "<select id='miglad_province' name='miglad_province' class='mg_off_form_input'>";
						echo $this->get_provinces();	
						echo "</select>";
						
						echo "</div>";
						echo "<div class='col-sm-3 hidden-xs'></div>";
						echo "</div>";	
						
						echo "<div class='form-group touching mg_off_form_lines mg_off_form_lines_state' style='display:none'><div class='col-sm-3 col-xs-12'>";
						echo "<label class='control-label  text-right-sm text-center-xs'>".__('State', 'migla-donation')."</label></div>";
						echo "<div class='col-sm-6 col-xs-12'>";
						
						echo "<select id='miglad_state' name='miglad_state' class='mg_off_form_input' >";
						echo $this->get_states();	
						echo "</select>";
						
						echo "</div>";
						echo "<div class='col-sm-3 hidden-xs'></div>";
						echo "</div>";						
						
					}else if( $field['id'] == 'honoreecountry' )
					{
						echo "<div class='form-group touching mg_off_form_lines'><div class='col-sm-3 col-xs-12'>";
						echo "<label class='control-label  text-right-sm text-center-xs'>".__("Honoree's country", 'migla-donation')."</label></div>";
						echo "<div class='col-sm-6 col-xs-12'>";
						
						echo "<select id='miglad_honoreecountry' name='miglad_honoreecountry' class='mg_off_form_input' >";
						echo $this->get_countries();
						echo "</select>";
						
						echo "</div>";
						echo "<div class='col-sm-3 hidden-xs'></div>";
						echo "</div>";							

						echo "<div class='form-group touching mg_off_form_lines mg_off_form_lines_hprovince' style='display:none'><div class='col-sm-3 col-xs-12'>";
						echo "<label class='control-label  text-right-sm text-center-xs'>".__("Honoree's Province", 'migla-donation')."</label></div>";
						echo "<div class='col-sm-6 col-xs-12'>";
						
						echo "<select id='miglad_province' name='miglad_province' class='mg_off_form_input' >";
						echo $this->get_provinces();	
						echo "</select>";
						
						echo "</div>";
						echo "<div class='col-sm-3 hidden-xs'></div>";
						echo "</div>";	
						
						echo "<div class='form-group touching mg_off_form_lines mg_off_form_lines_hstate' style='display:none'><div class='col-sm-3 col-xs-12'>";
						echo "<label class='control-label  text-right-sm text-center-xs'>".__("Honoree's State", 'migla-donation')."</label></div>";
						echo "<div class='col-sm-6 col-xs-12'>";
						
						echo "<select id='miglad_state' name='miglad_state' class='mg_off_form_input' >";
						echo $this->get_states();	
						echo "</select>";
						
						echo "</div>";
						echo "<div class='col-sm-3 hidden-xs'></div>";
						echo "</div>";						
					}else{
				
						if( $field['id'] == 'amount' ) $field['type'] = 'text';
						
						$label_text = str_ireplace( "[q]", "'", $field['label'] );
						
						if( strcmp( $field['status'], "2" ) == 0 || strcmp( $field['status'], "3" ) == 0 )
							$req = 'required';
						else
							$req = '';						
						
						echo "<div class='form-group touching mg_off_form_lines'><div class='col-sm-3 col-xs-12'>";
						echo "<label class='control-label  text-right-sm text-center-xs ".$req."'>". $label_text ."</label></div>";
						echo "<div class='col-sm-6 col-xs-12'>";
							
						if( $field['type'] == 'text' || $field['type'] == 'textarea' )
						{
							if( $field['id'] == 'amount' )
							{
								echo "<input id='".$field['code'].$field['id']."' name='".$field['code'].$field['id']."' type='text' class='mg_off_form_input migla_positive_number_only ".$req."' >";
							}else{
								echo "<input id='".$field['code'].$field['id']."' name='".$field['code'].$field['id']."' type='text' class='mg_off_form_input ".$req."' >";							
							}
						}else if( $field['type'] == 'checkbox')
						{
							echo "<label class='checkbox-inline'><input id='".$field['code'].$field['id']."' name='".$field['code'].$field['id']."' type='checkbox' class='mg_off_form_input ".$req."' >&nbsp;</label>";
							
						}else if( $field['type'] == 'select')
						{
							echo "<select id='".$field['code'].$field['id']."' name='".$field['code'].$field['id']."' class='mg_off_form_input ".$req."' >";
							
							$meta_key 			= 'mgval_' . $field['uid'];
							$option_maps_data	= $wpdb->get_results( 
													$wpdb->prepare(
													"SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = %s " 
													, $meta_key )
												);
							if( !empty($option_maps_data) )
							{	
							$option_maps_data	= $option_maps_data[0];
							$maps 				= $option_maps_data->meta_value ;
							
							$first_map	= explode( ";" , $maps );
							foreach( $first_map as $fmap )
							{
								if( $fmap == null || $fmap == '' )
								{
								}else{
									$second_map	= explode( "::" , $fmap );
									if( $second_map[0] == null || $second_map[0] == '' )
									{
									}else{
										echo "<option value='".$second_map[0]."'>";
										echo $second_map[1] . "</option>";
									}
								}
							}
							}
							echo "</select>";
							
						}else if( $field['type'] == 'radio' )
						{
							$meta_key 			= 'mgval_' . $field['uid'];
							$option_maps_data	= $wpdb->get_results( 
													$wpdb->prepare(
													"SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = %s " 
													, $meta_key )
												);
							if( !empty($option_maps_data) )
							{	
							$option_maps_data	= $option_maps_data[0];
							$maps 				= $option_maps_data->meta_value ;
							
							$first_map	= explode( ";" , $maps );
							foreach( $first_map as $fmap )
							{
								if( $fmap == null || $fmap == '' )
								{
								}else{
									$second_map	= explode( "::" , $fmap );
									if( $second_map[0] == null || $second_map[0] == '' )
									{
									}else{
										echo "<label class='radio-inline'><input name='".$field['code'].$field['id']."' type='radio' class='mg_off_form_input ".$req."' value='".$second_map[0]."'>";
										echo $second_map[1] ;
										echo "</label>";
									}
								}
							}						
							}
						}else if( $field['type'] == 'multiplecheckbox' )
						{	

							$meta_key 			= 'mgval_' . $field['uid'];
							$option_maps_data	= $wpdb->get_results( 
													$wpdb->prepare(
													"SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = %s " 
													, $meta_key )
												);
												
							if( !empty($option_maps_data) )
							{					
								$option_maps_data	= $option_maps_data[0];
								$maps 				= $option_maps_data->meta_value ;
								
								$first_map	= explode( ";" , $maps );
								foreach( $first_map as $fmap )
								{
									if( $fmap == null || $fmap == '' )
									{
									}else{
										$second_map	= explode( "::" , $fmap );
										if( $second_map[0] == null || $second_map[0] == '' )
										{
										}else{
											echo "<label class='checkbox-inline'><input name='".$field['code'].$field['id']."' type='checkbox' class='mg_off_form_input ".$req."' value='".$second_map[0]."'>";
											echo $second_map[1] ;
	echo "</label>";
										}
									}
								}
							}
						}
						
						echo "</div>";
						echo "<div class='col-sm-3 hidden-xs'></div>";
						echo "</div>";
					
					}
				}
			}
		}
	}
	
   /*
   echo "<div class='form-horizontal'> <div class='form-group touching'><div class='col-sm-3 col-xs-12'>";
   echo "<label for='miglad_firstname' class='control-label  text-right-sm text-center-xs required'>". __( "First Name: ", "migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12'><input name='miglad_firstname' ID='miglad_firstname' type='text' class='required' /></div><div class='col-sm-3 hidden-xs'></div></div>";

   echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label for='miglad_lastname' class='control-label  text-right-sm text-center-xs required'>". __( "Last Name: ", "migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12'><input name='miglad_lastname' id='miglad_lastname' type='text' class='required' /></div><div class='col-sm-3 hidden-xs'></div></div>";

   echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label for='miglad_amount' class='control-label  text-right-sm text-center-xs required'>". __( "Amount: ", "migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12'><input class='form-control miglaNAD2 required' type='text' name='miglad_amount' id='miglad_amount' size='10' value='' placeholder='' /></div><div class='col-sm-3 hidden-xs'></div></div>";

  echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label class='control-label  text-right-sm text-center-xs required' for='miglad_date'>". __( "Date: ", "migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12'><span class='input-group input-group-control mb-md'><span class='input-group-addon btn-success dashicons dashicons-calendar '></span><input class='miglaOffdate form-control custom_date required' type='text' name='miglad_date' size='10' value='' placeholder='' id='miglad_date'  /></span></div><div class='col-sm-3 hidden-xs'></div></div>";

   echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label for='miglad_email' class='control-label  text-right-sm text-center-xs '>". __( "Email: ", "migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12'><input placeholder='' id='miglad_email' name='miglad_email' type='text' class='form-control'></div><div class='col-sm-3 hidden-xs'></div></div>";


   echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label class='control-label  text-right-sm text-center-xs' for='miglad_orgname'>". __( "Organization: ", "migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12'><input id='miglad_orgname' name='miglad_orgname' type='text'  size='10' value='' />";
   echo "</span></div><div class='col-sm-3 hidden-xs'></div></div>";

   echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label for='miglad_anonymous' class='control-label  text-right-sm text-center-xs'>". __( "Anonymous: ", "migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12'><span class='checkbox-inline'><label><input id='miglad_anonymous' type='checkbox' value='' name='miglad_anonymous'><small>". __( "Check this if you want the name to be hidden from the public. It will still be shown in the reports", "migla-donation")."</small></label></span></div><div class='col-sm-3 hidden-xs'></div></div>";

$fund_array = (array)get_option( 'migla_campaign' );
//print_r($fund_array);

   echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label id='mcampaign'  for='miglad_campaign' class='control-label text-right-sm text-center-xs ' >". __( "Campaign: ", "migla-donation")."</label></div><div class='col-sm-6 col-xs-12'>";

  $label = get_option('migla_undesignLabel');
  if( $label == false )
  { 
     add_option('migla_undesignLabel', 'undesignated'); 
  }
  if( $label == '' ){ 
     $label = 'undesignated'; 
  }

       echo "<select name='miglad_campaign' id='miglad_campaign'>"; 
           $_undesignated_label = str_replace( "[q]", "'", $label );
	   echo "<option value='".$label."' checked>".$_undesignated_label."</option>";
           $b = "";

  if( empty($fund_array[0]) ){

  }else{
	   foreach ( (array) $fund_array as $key => $value ) 
	   { 
	     if( strcmp($fund_array[$key]['show'],"1")==0  ){
                  $c_name = $fund_array[$key]['name'] ;
                  $c_name = str_replace( "[q]", "'", $c_name );
		    echo "<option value='".$fund_array[$key]['name']."' >".$c_name."</option>";
	     }
	   }	
  }	   
	    echo "</select></div><div class='col-sm-3 hidden-xs'></div></div>"; 
	    
   echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label for='miglad_address' class='control-label   text-right-sm text-center-xs'>". __( "Address: ", "migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12'><input name='miglad_address' id='miglad_address' type='textarea' /></div><div class='col-sm-3 hidden-xs'></div></div>";

   echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label for='miglad_city' class='control-label  text-right-sm text-center-xs'>". __( "City: ", "migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12'><input name='miglad_city' id='miglad_city' type='text'  size='10' value='' />";
   echo "</span></div><div class='col-sm-3 hidden-xs'></div></div>";

   echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label for='country' class='control-label  text-right-sm text-center-xs '>". __( "Country: ", "migla-donation")."</label></div><div class='col-sm-6 col-xs-12'>";
   $countries = get_option( 'migla_world_countries' );
   echo "<select id='country' name='miglad_country'> "; 
	   
   foreach ( $countries as $key => $value ) { 
	      if ( $value == get_option( 'migla_default_country' ) )
		  { 
		     echo "<option value='".$value."' selected >".$value."</option>"; 
		  }else{  
		    echo "<option value='".$value."'>".$value."</option>"; 
		  }
	   }	   
   echo "</select></div><div class='col-sm-3 hidden-xs'></div></div>"; 		
 

   echo "<div id='state' style='display:none'><div class='form-group  touching'><div class='col-sm-3 col-xs-12'> <label for='miglad_state' class='control-label  text-right-sm text-center-xs' >". __( "States ", "migla-donation")."</label></div>";
	   $states = get_option( 'migla_US_states' );
   echo "<div class='col-sm-6 col-xs-12'><select id='miglad_state' name='miglad_state'>"; 
   echo "<option value=''>".__("Please pick one", "migla-donation")."</option>";
	   foreach ( $states as $key => $value ) 
	   { 
	      echo "<option value='".$value."'>".$value."</option>"; 
	   }	   
   echo "</select></div><div class='col-sm-3 hidden-xs'></div></div>"; 		
  echo "</div>";	   
	   
   echo "<div id='province' style='display:none'><div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label for='miglad_province' class='control-label  text-right-sm text-center-xs'>". __( "Provinces", "migla-donation")."</label></div>";
	   $states = get_option( 'migla_Canada_provinces' );
   echo "<div class='col-sm-6 col-xs-12'><select id='miglad_province' name='miglad_province'>"; 
   echo "<option value=''>".__("Please pick one", "migla-donation")."</option>";
	   foreach ( $states as $key => $value ) 
	   { 
	      echo "<option value='".$value."'>".$value."</option>"; 
	   }	   
   echo "</select></div><div class='col-sm-3 hidden-xs'></div></div>"; 		
   echo "</div>";	 

   echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label for='miglad_zip' class='control-label  text-right-sm text-center-xs' for='miglaOffzip'>". __( "Postal Code: ", "migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12'><input name='miglad_zip' id='miglad_zip' type='text'  size='10' value='' />";
   echo "</span></div><div class='col-sm-3 hidden-xs'></div></div>";


   echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label class='control-label  text-right-sm text-center-xs' for='miglad_employer'>". __( "Employer: ", "migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12'><input name='miglad_employer' type='text' id='miglad_employer' size='10' value='' />";
   echo "</span></div><div class='col-sm-3 hidden-xs'></div></div>";


   echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label class='control-label  text-right-sm text-center-xs' for='miglad_occupation'>". __( "Occupation: ", "migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12'><input name='miglad_occupation' id='miglad_occupation' type='text'  size='10' value='' />";
   echo "</span></div><div class='col-sm-3 hidden-xs'></div></div>";


   echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label class='control-label  text-right-sm text-center-xs' for='miglad_transactionType'>". __( "Type of payment : ", "migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12'><select id='miglad_transactionType' name='miglad_transactionType' >";
   echo "<option value=''>".__("Please pick one", "migla-donation")."</option>";
   echo "<option value='cash'>".__("Cash", "migla-donation")."</option>";
   echo "<option value='cheque'>".__("Cheque", "migla-donation")."</option>";
   echo "<option value='credit card'>".__("Credit Card", "migla-donation")."</option>";
   echo "</select>";
   echo "</span></div><div class='col-sm-3 hidden-xs'></div></div>";
*/  

	echo "</div>";   
   
    echo "<div class='row'>";
	echo "<div class='col-sm-12 center-button'>";
	echo "<button value='save' class='btn btn-info pbutton'  id='miglaAddOffline'><i class='fa fa-fw fa-save'></i>". __( " save ", "migla-donation")."</button>";
	echo "</div></div>";
      
   echo "</div></div></section>";
   echo "</div>"; //col-container
   echo "</div>"; //col-sm-12
   
echo "<section class='panel'>
							<header class='panel-heading'>
								<div class='panel-actions'>
									<a class='fa fa-caret-down ' data-toggle='collapse' data-parent='.panel' href='#collapseTwo' aria-expanded='true'></a>
									
								</div>
						
								<h1 class='panel-title'>". __( "Reports ", "migla-donation")."</h2>
							</header>
							<div id='collapseTwo' class='panel-body collapse in'>
								<div id='datatable-default_wrapper' class='dataTables_wrapper no-footer'><div class='table-responsive'>";
								
								
							   
   echo "<table id='miglaReportTable' class='display' cellspacing='0' width='100%'>";

   echo "<thead>";
   echo "<tr>";
   echo "<th class=''>". __("Delete","migla-donation")."</th>";
   echo "<th class='detailsHeader' style='width:15px;'>". __("Detail","migla-donation")."</th>";
   echo "<th class=''>". __("Date","migla-donation")."</th>";
   echo "<th class=''>". __("FirstName","migla-donation")."</th>";
   echo "<th class=''>". __("LastName","migla-donation")."</th>";
   echo "<th class=''>". __("Campaign","migla-donation")."</th>";
   echo "<th class=''>". __("Amount","migla-donation")."</th>";
   echo "<th class=''>". __("Country","migla-donation")."</th>";
   echo "<th class=''>". __("Status","migla-donation")."</th>";
   echo "<th></th>";
   echo "</tr>"; 
   echo "</thead>";

   echo "<tfoot><tr>";

   echo "<th id='f0' colspan='3'>";
   echo "<div data-plugin-datepicker='' class='input-daterange input-group migla-date-range-picker'>
   <span class='input-group-addon migla-date-range-icon'>
															<i class='fa fa-calendar'></i>
														</span>
														<input type='text' name='start' class='form-control miglaOffdate' placeholder='mm/dd/yyyy' id='sdate'>
														<span class='input-group-addon migla-to-date'>". __("to","migla-donation")."</span>
														<input type='text' name='end' class='form-control miglaOffdate' placeholder='mm/dd/yyyy' id='edate'></div>";

   //echo "<th id='f2'>". __("Date","migla-donation")."</th>";   
   echo "<th id='f3'>". __("FirstName","migla-donation")."</th>";
   echo "<th id='f4'>". __("LastName","migla-donation")."</th>";
   echo "<th id='f5'>". __("Campaign","migla-donation")."</th>";
   echo "<th id='f6'>". __("Amount","migla-donation")."</th>";
   echo "<th id='f7'>". __("Country","migla-donation")."</th>";
   echo "<th id='f8'>". __("Status","migla-donation")."</th>";
   echo "<th id='f9'></th>";
   
   echo "</tr></tfoot>";
   echo "</table>";

echo "<div class='row datatables-footer'><div class='col-sm-12 col-md-6'>

   
   <button  class='btn rbutton'  id='miglaRemove' data-toggle='modal' data-target='#confirm-delete'>
   <i class='fa fa-fw fa-times'></i>". __( " remove ", "migla-donation")."</button>

<button class='btn mbutton' id='miglaUnselect' data-target='#unselect-all'>
   <i class='fa fa-fw fa-square-o '></i>". __( " Unselect All ", "migla-donation")."</button>

</div>
   
   <div class='col-sm-12 col-md-6'>

</div></div>";

   echo "  </div>   ";

   echo "</div> ";  
   
  $icon = $this->getSymbol();  
  $thousandSep = get_option('migla_thousandSep');
  $decimalSep = get_option('migla_decimalSep');
  $placement = get_option('migla_curplacement');
$showDecimal = get_option( 'migla_showDecimalSep');
        echo "<div style='display:none' id='thousandSep'>".$thousandSep."</div>";
        echo "<div style='display:none' id='decimalSep'>".$decimalSep."</div>";
        echo "<div style='display:none' id='placement'>".$placement."</div>";   
   	 echo "<div style='display:none' id='showDecimal'>".$showDecimal."</div>";							
 echo "<div id='symbol' style='display:none'>".$icon."</div>";

echo "</div></section> <div class='row'> <div class='col-sm-12 col-md-6'> <div class='tabs'>
								<ul class='nav nav-tabs nav-justified'>
									<li class='active'>
										<a class='text-center' data-toggle='tab' href='#thisreport' aria-expanded=''><i class='fa 

fa-star'></i>". __( " This Report", "migla-donation")."</a>
									</li>
									<li class=''>
										<a class='text-center' data-toggle='tab' href='#all' aria-expanded=''><i class='fa 

fa-star'></i>". __( " All Donations", "migla-donation")."</a>
									</li>
								</ul>
								<div class='tab-content'>
	<div class='tab-pane  active' id='thisreport'>									
  <div class='widget-summary'>
												<div class='widget-summary-col-icon'>
													<div class='summary-icon bg-primary'>";
	echo $icon;
        echo "</div>
												</div>
												<div class='widget-summary-col'>
													<div class='summary'>
														<h4 class='title'>". __( " Grand Total:", "migla-donation")."</h4>
														<div class='info'>
															<strong class='amount' id='miglaOnTotalAmount2'>".$icon."</strong>
															<span class='text-primary'></span>";
/*
$export_url = plugins_url( '/export.php', __FILE__ );
echo "<input id='exportTable' class='mbutton' type='submit' value='". __(" Export table to CSV file","migla-donation")."'>";
   echo "<form id='miglaExportTable' method='POST' action='" . esc_url( $export_url ) . "' >";
   echo "<input name='miglaFilters' type='hidden' >";
   echo "</form>"; 
*/

echo "<a style='' href='#' id='exportTable' class='export mbutton'>Export data in table to CSV Excel format</a>";
 echo "</div>";
echo "<div class='widget-footer-2'>";
											
 
													echo "</div>												</div>
													
												</div>

											</div>
											
												
										
  </div>


<div id='all' class='tab-pane'>									
  <div class='widget-summary'>
												<div class='widget-summary-col-icon'>
													<div class='summary-icon bg-color-teal'>
														<i class='fa fa-check'></i>
													</div>
												</div>
												<div class='widget-summary-col'>
													<div class='summary'>
													<input type='hidden' id='mg_total_pending' value=''>
													<input type='hidden' id='mg_total_all' value=''>
														<h4 class='title'>". __( " All Offline Donations:", "migla-donation")."</h4>
														<strong class='badge alert-success' id='mg_pending_amount'><span class=''>".$icon."</span>0.00 pending</strong>
														<div class='info'>
															<strong class='amount' id='miglaOnTotalAmount'>".$icon."</strong>
															<span class='text-primary'></span>";




echo "<a style='' href='#' id='exportTableJS' class='export mbutton'>". __("Export All data in CSV Excel format", "migla-donation")."</a>";

echo "</div>
													</div>



													
												</div>
											</div>
											
											
											</div></div></div></div> </div></div>


";	

 echo "<div class='modal fade' id='mg-edit-record' tabindex='-1' role='dialog' aria-labelledby='mgModalEditLabel' aria-hidden='true'>
  <div class='modal-dialog mg_reports-edit'>
    <div class='modal-content'>
      <div class='modal-header'>
        <button type='button' id='mg-edit-record-close' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
        <input type='hidden' name='mg_recordID' id='mg_recordID' disabled>
		<input type='hidden' name='mg_ajaxRow' id='mg_ajaxRow' disabled>
		<input type='hidden' name='mg_FormID' id='mg_FormID' disabled>
        <h4 class='modal-title' id='mgModalEditLabel'>". __("Edit Record Form","migla-donation")."</h4>
      </div>
      <div class='modal-body'>
      </div>
      <div class='modal-footer'>";

echo "<button type='button'  id='mg_cancel_update_record' class='btn btn-default mbutton' data-dismiss='modal'>". __("Cancel","migla-donation")."</button>";
echo "        <button type='button' id='mg_update_record' class='btn btn-primary pbutton '><i class='fa fa-fw fa-save'></i>". __(" Save changes","migla-donation")."</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->"; 

echo "<div id='mg-warningconfirm1' style='display:none'>".__("You will delete these records:","migla-donation")."</div>";
echo "<div id='mg-warningconfirm2' style='display:none'>".__("Do you want to proceed?","migla-donation")."</div>";
echo "<div id='mg-warningconfirm3' style='display:none'>".__("A donation you wish to delete is reoccurring. Deleting this record will NOT stop those donations. Reoccurring donations must be stopped by PayPal","migla-donation")."</div>";									

 echo " <div class='modal fade' id='confirm-delete' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true' data-backdrop='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
            
                <div class='modal-header'>


                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true' data-target='#confirm-delete'><i class='fa fa-times'></i></button>
                    <h4 class='modal-title' id='myModalLabel'>". __( " Confirm Delete", "migla-donation")."</h4>
                </div>
            
<div class='modal-wrap clearfix'>

           <div class='modal-alert'>
														<i class='fa fa-times-circle'></i>
													</div>  

   <div class='modal-body'>


                    <p>". __( " Are you sure you want to delete? This cannot be undone", "migla-donation")."</p>
                </div>

</div> 
                
                <div class='modal-footer'>
                    <button type='button' class='btn btn-default mbutton' data-dismiss='modal' id='miglacancel'>". __( "Cancel", "migla-donation")."</button>
                    <button type='button' class='btn btn-danger danger rbutton mg_remove_donation' >". __( "Delete", "migla-donation")."</button>
                   
                </div>
            </div>
        </div>
    </div></div>"; 

}

}


?>