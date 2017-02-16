<?php

class migla_campaign_menu_class {

	function __construct(  ) {
		add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 7 );
	}
	
	function menu_item() {
		add_submenu_page(
			'migla_donation_menu_page',
			__( 'Campaigns', 'migla-donation' ),
			__( 'Campaigns', 'migla-donation' ),
			$this->get_capability() ,
			'migla_donation_campaigns_page',
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

	   foreach ( $currencies as $key => $value ) 
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
   
function migla_init_form()
{
	$fields = array (
		'0' => array (
			'title' => 'Donation Information',
			'child' =>  array(
					   '0' => array( 'type'=>'radio','id'=>'amount', 'label'=>'How much would you like to donate?', 'status'=>'3', 'code' => 'miglad_', 
						   'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
					   '1' => array( 'type'=>'radio','id'=>'repeating', 'label'=>'Is this a recurring donation?', 'status'=>'1', 'code' => 'miglad_', 
						   'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
					   '2' => array( 'type'=>'checkbox','id'=>'mg_add_to_milist', 'label'=>'Add to mailing list?', 'status'=>'1', 'code' => 'miglad_', 
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

   function menu_campaign()
   {

       echo "<div class='wrap'><div class='container-fluid'>";
               
                echo "<h2 class='migla'>". __('Campaign', 'migla-donation'). "</h2>";
		echo "<div class='row'>";
		echo "<div class='col-sm-12'>";

		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down ' data-toggle='collapse' data-parent='.panel' href='#collapseOne' aria-expanded='true'></a></div><h2 class='panel-title'><div class='dashicons dashicons-plus'></div>". __('Add a Campaign', 'migla-donation'). "";
		__("Add New Campaigns","migla-donation");
		echo "</h2></header>";
		echo "<div class='panel-body collapse in' id='collapseOne'>";
		//echo "<input type='hidden' name='migla_donation_fund_nonce' id='migla_donation_fund_nonce' value='" . esc_attr( $fund_nonce ) . "' />";	
		
		echo "<div class='row'><div class='col-sm-3'><label for='mName' class='miglaCampaignNameLabel  control-label  text-right-sm text-center-xs'>". __('Campaign Name', 'migla-donation');
		echo "</label></div><div class='col-sm-6 col-xs-12'><span class='input-group input-group-control'><span class='input-group-addon '><i class='fa fa-medkit  fa-fw'></i>";
                echo "</span><input type='text' id='mName' placeholder='Name' class='form-control' /></span></div><div class='col-sm-3 hidden-xs'></div>";
                echo "<div class='col-sm-12 col-xs-12'><div class='help-control-center'>". __('Enter the name of the Campaign (e.g. Bulid a School)','migla-donation') ."</div></div></div>";
		
		echo "<div class='row'><div class='col-sm-3'><label for='mAmount'  class='miglaCampaignTargetLabel control-label text-right-sm text-center-xs migla_positive_number_only'>". __('Donation Target','migla-donation') ;
		echo "</label></div><div class='col-sm-6 col-xs-12'><span class='input-group input-group-control'><span class='input-group-addon'>";
     echo $this->getSymbol();
echo "</i></span><input type='text' class='form-control miglaNAD' placeholder='0' id='mAmount'></span></div><div class='col-sm-3 hidden-xs'></div><div class='col-sm-12 col-xs-12'><div  class='help-control-center'>". __("No currency symbol. Leave blank if you don't want the progress bar.","migla-donation") . "</div></div></div>";
		
		echo "<p><button id='miglaAddCampaign' class='btn btn-info pbutton miglaAddCampaign' value='save'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation")."</button></p>";
		echo "<div></section><br></div>";

echo "<div class='col-sm-12'>";

		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseTwo' aria-expanded='true'></a></div>";	

		echo "<h2 class='panel-title'><div class='dashicons dashicons-list-view'></div>". __("List of Available Campaigns","migla-donation") ."</h2></header>";
		echo "<div id='collapseTwo' class='panel-body collapse in'>";	


echo "<ul class='row mg_campaign_list'>";
$data = get_option( 'migla_campaign' ) ;

$idk = 0;
$new_data    = array();
$old_version = false;
 
if( empty($data) )
{
    echo "<li style='padding-left:15px;'>". __('You do not have any campaigns yet','migla-donation') ."</li>";
  
}else{
 
 foreach( (array)$data as $d ){
 
  echo "<li class='ui-state-default formfield clearfix formfield_campain'>";
 
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
  
	$fields = $this->migla_init_form();
	add_post_meta( $post_id, 'migla_form_fields', $fields );   
  
    $new_data[$idk] = array(
	                     'name' 	=> $n,
						 'target' 	=> $t,
						 'show'		=> $s,
						 'form_id'	=> $post_id
	                   );					   
  }else
  {
     $post_id = $d['form_id'];
  }  
  
	echo "<input type='hidden' name='oldlabel' value='".$n."' />";
	echo "<input type='hidden' name='label' value='".$n."' />";
	echo "<input type='hidden' name='target' value='".$t."' />";
	echo "<input type='hidden' name='show'  value='".$s."' />";
	echo "<input type='hidden' name='form_id'  value='".$post_id."' />";

	  echo"<div class='col-sm-1 hidden-xs'><label class='control-label'>". __('Campaign','migla-donation'). "</label></div>";
	  echo "<div class='col-sm-2 col-xs-12'>"; 
	  echo "<input type='text' class='labelChange' name='' placeholder='' value='".$n."' /></div>";

	  echo "<div class='col-sm-1 hidden-xs'><label class='control-label'>". __('Target','migla-donation'). "</label></div>";
	  echo "<div class='col-sm-2 col-xs-12'>";
	  echo "<input type='text' class='targetChange miglaNAD' name='' placeholder='' value='" . $t . "' /></div>";
	  
	echo "<div class='col-sm-1 col-xs-12'>";
	//echo "<a class='' href='".get_admin_url()."admin.php?page=migla_donation_campaigns_page&form_id=".$post_id."'>";
	echo "<button id='form_".$post_id."' class='mg_a-form-per-campaign-options mbutton edit_custom-fields-list' onClick='mg_send_form_id(".$post_id.")'>";
	echo "</button></div>";

	echo "<div class='col-sm-2 col-xs-12'>";
	echo '<input type="text" value="[totaldonations form_id=\''.$post_id .'\']" ';
	echo "placeholder='' name='' class='mg_label-shortcode' onclick='this.setSelectionRange(0, this.value.length)'></div>";


  $s = ""; $h = ""; $da = ""; $cl ="";
  if( strcmp($s,'1') == 0 ){ $s = "checked"; }else if( strcmp($s,'0') == 0 ){ 
      $h = "checked"; 
  }else{ 
      $da = "checked";
	  $cl="pink-highlight" ;
  }

  echo "<div class='control-radio-sortable col-sm-3 col-xs-12'>";
  
  echo "<span><label><input type='radio' name='r".$idk."'  value='1' ".$s." class='' >". __(" Show","migla-donation") ."</label></span>";
  echo "<span><label><input type='radio' name='r".$idk."'  value='-1' ".$da." class=''>". __(" Deactivate","migla-donation") ."</label></span>";

  echo "<span><button class='removeCampaignField' data-toggle='modal' data-target='#confirm-delete'><i class='fa fa-fw fa-trash'></i></button></span>";
  echo "</div>";  

  $idk++;

  echo "</li>";
 }
 
 if( $old_version )
 {
    update_option( 'migla_campaign' , $new_data ) ;
 }
 
}

echo "</ul>";

echo "<div class='row'><div class='col-sm-6'><button value='save' class='btn btn-info pbutton' id='miglaSaveCampaign'><i class='fa fa-fw fa-save'></i>". __(' update list of campaigns','migla-donation'). "</button></div></div>";
echo "</div></section>";
		
		echo "</div></div> <!--  -->";		


 echo " <div class='modal fade' id='confirm-delete' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true' data-backdrop='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
            
                <div class='modal-header'>


                    <button type='button' class='close mg_campaign_remove_cancel' data-dismiss='modal' aria-hidden='true' data-target='#confirm-delete'><i class='fa fa-times'></i></button>
                    <h4 class='modal-title' id='myModalLabel'>". __("Confirm Delete","migla-donation"). "</h4>
                </div>
<div class='modal-wrap clearfix'>
           <div class='modal-alert'>
														<i class='fa fa-times-circle'></i>
													</div>  
   <div class='modal-body'>
                    <p>". __("Deleting this campaign will also delete any changes you've made on its unique form. The donation data will be not deleted.", "migla-donation") .
 "</p>
                </div>
</div> 
                
                <div class='modal-footer'>
                    <button type='button' class='mg_campaign_remove_cancel btn btn-default mbutton' data-dismiss='modal'>". __("Cancel","migla-donation") ."</button>
                    <button type='button' id='mg_campaign_remove' class='btn btn-danger danger rbutton' >". __("Delete","migla-donation") ."</button>
                   
                </div>
            </div>
        </div>
    </div></div>"; 

		echo "</div><!-- container-fluild  -->";
  
   
   }
   
   function menu_form($post_id , $thousand, $decimal, $showSeparator )
   {
      $current_pos 	= get_post($post_id); 
     // $campaign = get_post_meta ( $post_id, 'migla_campaign_creator' );
	  $current_title = $current_pos->post_title ;
     
      echo "<div class='wrap'><div class='container-fluid'>";         
        echo "<input type='hidden' id='mg_current_form' value='".$post_id."'>";        
        echo "<h2 class='migla'>".__(" Form options : ".$post_id."-".$current_title ,"migla-donation")."</h2>";
		
		echo "<div class='row'>";
		echo "<div class='col-sm-6'>";		
		echo "<a class='mg_go-back' onclick='mg_go_campaign()'><i class='fa fa-fw fa-arrow-left'></i>".__(" Go back to Main Campaign Page", "migla-donation")."</a>";		
		echo "</div>";

                echo "<div class='col-sm-6 mg_inner-form-shortcode'>";	
                echo '<input type="text" value="[totaldonations form_id=\''.$post_id .'\']" ';
	        echo "placeholder='' name='' class='mg_label-shortcode' onclick='this.setSelectionRange(0, this.value.length)'></div>";
                echo "</div>";

		echo "<div class='row'>";
		echo "<div class='col-sm-12'>";

    echo  "<div class='form-horizontal'><ul class='nav nav-pills'>
        <li class='active' ><a data-toggle='tab' href='#section1' class='mg_a_nav_pills'>".__("Suggested Levels","migla-donation"). "</a></li>
        <li ><a data-toggle='tab' href='#section3' class='mg_a_nav_pills'>".__("Form Settings","migla-donation"). "</a></li>
    </ul>";

    echo "<div class='tab-content nav-pills-tabs' >";
		
   /**********************************************************************************************************/	

    echo "<div id='section1' class='tab-pane  active' >";

		//the form meta data
		$amounts            = get_post_meta ( $post_id, 'migla_amounts' );
		$hide_custom_amount = get_post_meta ( $post_id, 'migla_hideCustomAmount' );
		$hide_custom_amount = $hide_custom_amount[0];
		$type_amount_btn	= get_post_meta ( $post_id, 'migla_amount_btn' );
		$type_amount_btn	= $type_amount_btn[0];
	
       $curSymbol = $this->getSymbol();
		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseOne' aria-expanded='true'></a></div><h2 class='panel-title'><span>".$curSymbol."</span>".__("Suggested Giving Level Options","migla-donation")."</h2></header>";
		echo "<div id='collapseOne' class='panel-body collapse in'>";

   /************** Hide custom amount   ***************************/
   echo "<div class='row'>";
   
   echo "<div class='col-sm-3 col-xs-12'><label for='mHideHideCustomCheck' class='control-label text-right-sm text-center-xs'>".__("Hide Custom Amount on Form:","migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12 text-left-sm text-center-xs'><label for='mHideHideCustomCheck' class='checkbox-inline'>";
   if(  $hide_custom_amount == 'yes'  )
   {
		echo "<input type='checkbox' id='mHideHideCustomCheck' name='mHideHideCustomCheck' checked>";
   }else{
        echo "<input type='checkbox' id='mHideHideCustomCheck' name='mHideHideCustomCheck'>";
   }
   
   echo __("Check this if you want your donors not to be able to choose a custom amount","migla-donation")." </label></div>";
	
echo "</div>"; 

   

   if( $hide_custom_amount == 'yes' )
   {
		echo "<div class='row' id='mg_div_custom_amount_text' style='display:none !important'>";
   }else{
		echo "<div class='row' id='mg_div_custom_amount_text'>";
   }
   $ctext = get_post_meta ( $post_id, 'migla_custom_amount_text' );
   $ctext = $ctext[0];
   echo "<div class='col-sm-3 col-xs-12'><label for='mHideHideCustomText' class='control-label text-right-sm text-center-xs'>".__("Custom Amount Text:","migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12 text-left-sm text-center-xs'>  ";
   echo "<input type='text' id='mg_custom_amount_text' value='".$ctext."'></div></div>";

   
   /************** Level  ***************************/
   echo "<div class='row'><div class='col-sm-3 col-xs-12'><label for='mg_amount_btn_type' class='control-label text-right-sm text-center-xs'>".__("Choose the style of the giving level amounts:","migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12 text-left-sm text-center-xs'>";
   echo "<select id='mg_amount_btn_type'>";
   if( $type_amount_btn == 'button' )
   {
		echo "<option value='radio'>Radio Button</option>";
		echo "<option value='button' selected>Button</option>";
   }else{
		echo "<option value='radio' selected>Radio Button</option>";
		echo "<option value='button'>Button</option>";   
   }
   echo "</select>";
   echo "</div>";
   echo "<div class='col-sm-3 col-xs-12'>";
   echo "</div>";
   echo "</div>";   


   /************** Box Width  ***************************/
echo "<div class='row'><div class='col-sm-3 col-xs-12'><label for='mg_amount_box_type' class='control-label text-right-sm text-center-xs'>".__("Choose the length of the giving level amount boxes:","migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12 text-left-sm text-center-xs'>";
   
   $amount_box_type = get_post_meta ( $post_id, 'migla_amount_box_type' );
	if( $amount_box_type == false || $amount_box_type == '' )
	{
		add_post_meta ( $post_id, 'migla_amount_box_type', 'box' );
		$amount_box_type = 'box' ;	
	}else{
		$amount_box_type = $amount_box_type[0];
	}
   echo "<select id='migla_amount_box_type'>";
   if( $amount_box_type == 'box' )
   {
		echo "<option value='box' selected>Box</option>";   
		echo "<option value='fill'>Fill Form</option>";
   }else{
		echo "<option value='box'>Box</option>";   
		echo "<option value='fill' selected>Fill Form</option>";
   }
   echo "</select>";
   echo "</div>";
   echo "<div class='col-sm-3 col-xs-12'>";
   echo "</div>";
   echo "</div>";   


   
   echo "<div class='row'><div class='col-sm-12 center-button'><button id='mg_amount_settings' class='btn btn-info pbutton msave' value='save'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div></div>";	   
	
   echo "</section>";
	
	
		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseTwenty' aria-expanded='true'></a></div><h2 class='panel-title'><span>".$curSymbol."</span>".__("Giving Levels","migla-donation")."<span class='panel-subtitle'>".__(" Drag and drop the amounts into the order you want","migla-donation")."</span></h2></header>";
		echo "<div id='collapseTwenty' class='panel-body collapse in'>";
		
		echo "<div class='row'><div class='col-sm-3 col-xs-12'><label for='miglaAddAmount' class='control-label text-right-sm text-center-xs'>".__("Add a suggested giving level","migla-donation")."</label></div>";
				
		echo "<div class='col-sm-6 col-xs-12'><span class='input-group input-group-control'><span id='curSymbol' class='input-group-addon'>";
                echo $curSymbol."</span><input type='text' class='form-control migla_positive_number_only' placeholder='0' id='miglaAddAmount'></span></div></div>";	
echo "<div class='row'><div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs' for='miglaAddAmount'>".__("Add a giving level description (optional)","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'><input type='text' id='miglaAmountPerk' class='form-control' placeholder='e.g. This amount will provide enough food for...'></div>";	





																				echo "<div class='center-button col-sm-12'><input id='miglaAddAmountButton' class='mbutton' type='button' value='add' >";
	
		
		echo "</div></div>";
				
        echo "<div id='miglaAmountTable'>";	

	   if( isset($amounts[0]) && count($amounts[0]) > 0 )
	   {
		   	$keys_amount = array_keys($amounts[0]);
			$idx = 0;
		    foreach( $keys_amount as $key )
            {
			     $valLabel = $amounts[0][$key]['amount'] ; $valPerk = $amounts[0][$key]['perk'];

                     if( $showSeparator == 'yes' ){
                        $valLabel = str_replace(".", $decimal , $valLabel  );
                     }else{
                        $digit = explode( ".", $valLabel  ) ;
                        $valLabel = $digit[0];
                     }
					 					
				    echo "<p class='mg_amount_level'>";
					echo "<input class='mg_amount_level_value' type=hidden value='".$amounts[0][$key]['amount']."' />";
					echo "<label>". $valLabel. "</label>";	
					echo "<label class='mg_amount_level_perk'>".$valPerk. "</label>";
				   
				    echo "<button name='miglaAmounts' class='miglaRemoveLevel obutton'><i class='fa fa-times'></i></button>";
				    echo "</p>";

				 $idx++;
			 }		 
    } 	   
	echo "</div>";	
    echo "<p id='warningEmptyAmounts' style='display:none'>".__("No amounts have been added. Add some amounts above.","migla-donation")."<i class='fa fa-fw fa-caret-up'></i></p>";

	
    echo "</section>";
    echo "</div>";
    	
   /**********************************************************************************************************/	
	
    echo "<div id='section3' class='tab-pane ' >";

	
    echo "<div class='row'>";
   		
echo "<div class='col-sm-12'><section class='panel'><header class='panel-heading'><div class='panel-actions'><a aria-expanded='true' href='#collapseFive' data-parent='.panel' data-toggle='collapse' class='fa fa-caret-down '></a></div><h2 class='panel-title'><i class='fa fa-fw fa-bullhorn'></i>".__("Error Message Options","migla-donation")."</h2></header>";
echo "<div id='collapseFive' class='panel-body collapse in'>";

/**** error message for the general fields ****/
$warning1 = get_post_meta ( $post_id, 'migla_warning_1'); //Please insert all the required fields
if( $warning1 == false ){
	$warning1 = 'please insert required fields';
}else{
	$warning1 = $warning1[0];
}
$warning2 = get_post_meta ( $post_id, 'migla_warning_2'); //Please insert correct email
if( $warning2 == false ){
	$warning2 = 'please correct emails';
}else{
	$warning2 = $warning2[0];
}
$warning3 = get_post_meta ( $post_id, 'migla_warning_3'); //please fill in a valid amount
if( $warning3 == false ){
	$warning3 = 'please fill valid amounts';
}else{
	$warning3 = $warning3[0];
}

echo "<div class='row'><div class='col-sm-3'><label class='miglaErrorGeneralLabel control-label  text-right-sm text-center-xs' for='mg-errorgeneral-default'>".__("Error Message Label for the General Fields:","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'><input type='text' id='mg-errorgeneral-default' ";

echo " class='form-control ' value='".$warning1 ."' placeholder='".$warning1 ."'></div><div class='col-sm-3 hidden-xs'></div></div>";


/**** error message for the emails ****/


echo "<div class='row'><div class='col-sm-3'><label class='miglaErrorEmailLabel control-label  text-right-sm text-center-xs' for='mg-erroremail-default'>".__("Error Message Label for the Email Field:","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'><input type='text' id='mg-erroremail-default' ";

echo " class='form-control ' value='".$warning2."' placeholder='".$warning2 ."'></div><div class='col-sm-3 hidden-xs'></div></div>";


/**** error message for the amounts ****/


echo "<div class='row'><div class='col-sm-3'><label class='miglaErrorAmountLabel control-label  text-right-sm text-center-xs' for='mg-erroramount-default'>".__("Error Message Label for the Amount:","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'><input type='text' id='mg-erroramount-default' ";

echo " class='form-control ' value='".$warning3 ."' placeholder='".$warning3 ."'></div><div class='col-sm-3 hidden-xs'></div></div>";





/**************** end astried edit *************/



echo "<div class='row'><div class='col-sm-12 center-button'><button id='mg_save_misc_form_settings' class='btn btn-info pbutton msave' value='save'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div></div>
</div></section></div>";
		
		///////////////////////////////////////////////////////////////////////////////////////


		// Fields and Sections Table PART 3
		echo "<div class='col-sm-12 hidden-xs'>";
		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseFour' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa fa-fw fa-check-square-o'></i>".__("Form Fields","migla-donation")."<span class='panel-subtitle'>".__("Drag and drop fields and groups or add new ones","migla-donation")."</span></h2></header><div id='collapseFour' class='panel-body collapse in'><div class='row'><div class='col-sm-12 groupbutton'><button value='save' class='btn btn-info pbutton miglaSaveForm' id='miglaSaveFormTop'><i class='fa fa-fw fa-save'></i>".__(" save form","migla-donation")."</button><button class='btn btn-info obutton mAddGroup' value='add'><i class='fa fa-fw fa-plus-square-o'></i>".__("Add Group","migla-donation")."</button></div>";


////////HERE
echo "<div id='divAddGroup' class='col-sm-12'  style='display:none'><div class='addAgroup'><div class='row'><div class='col-sm-4'><div class='row'><div class='col-sm-2'> <i class='fa fa-bars bar-icon-styling'></i></div>
<div class='col-sm-10'> <input type='text' id='labelNewGroup' placeholder='".__("insert new header for group","migla-donation")."'  /> </div></div>
</div><div class='col-sm-4'><div class='col-sm-5'><input type='checkbox' id='t' checked='false' class='toggle' id='toggleNewGroup' /><label>".__("Toggle","migla-donation")."</label></div></div>
<div class='col-sm-4 addfield-button-control alignright'><button type='button' class='btn btn-default mbutton' id='cancelAddGroup'>".__("Cancel","migla-donation")."</button> <button type='button' class='btn btn-info inputFieldbtn pbutton' id='saveAddGroup'><i class='fa fa-fw fa-save'></i>".__(" Save Group","migla-donation")."</button></div>
</div></div>
</div>";

//////////END OF HERE

 echo "<div class='col-sm-12'>";

$formFields 	= get_post_meta ( $post_id, 'migla_form_fields' );

echo "<ul class='containers'>";
$id = 0; $i = 0;

if( !empty( $formFields ) && $formFields[0] !='')
{
  foreach ( (array)$formFields[0] as $field ){

 echo "<li class='title formheader'><div class='row'><div class='col-sm-4'><div class='row'><div class='col-sm-2'>";
echo "<i class='fa fa-bars bar-icon-styling'></i></div><div class='col-sm-10'> "; 
echo "<input type='text' class='titleChange'  placeholder='" . $field['title']."' name='grouptitle' value='" . $field['title']."'>  </div> ";
echo "</div></div>
      <div class='col-sm-4'>"; 




echo "<div class='col-sm-4 mg_addfield'><button value='add' class='btn btn-info obutton mAddField addfield-button-control' ><i class='fa fa-fw fa-plus-square-o'></i>".__("Add Field","migla-donation")."</button> </div>";




if(  strcmp( $field['toggle'], '-1') == 0)
{
	echo "<div class='col-sm-5'><input type='checkbox' id='t".$id."'  class='toggle' disabled><label>".__("Toggle","migla-donation")."</label></div>";
}else if(strcmp( $field['toggle'], '1') == 0)
{
	echo "<div class='col-sm-5'><input type='checkbox' id='t".$id."' checked='checked' class='toggle' /><label>".__("Toggle","migla-donation")."</label></div>";
}else{
	echo "<div class='col-sm-5'><input type='checkbox' id='t".$id."'  class='toggle' disabled><label>".__("Toggle","migla-donation")."</label></div>";
}
$id++;

echo "</div>";

echo "<div class='col-sm-4 text-right-sm text-right-xs divDelGroup' >  <button class='rbutton btn btn-danger mDeleteGroup pull-right' ><i class='fa fa-fw fa-trash'></i>".__("Delete Group","migla-donation")."</button>  
  </div>";

 echo "</div>";

 echo "<input class='mHiddenTitle' type='hidden' name='title' value='".$field['title']."' />";

 $ulId = str_replace(" ","", $field['title']);

 echo "<ul class='rows' id='".$ulId."' >";
if(isset($field['child']))
{  
if ( count((array) $field['child']) > 0 )
 {
	$j = -1;
   foreach ( (array)$field['child'] as $c )
   {
     $j++;
     $arrShow = array();
     $arrShow[0] = "";$arrShow[1] = "";$arrShow[2] = "";$arrShow[3] = "";$arrShow[4] = "";$arrShow[5] = "";

	if(  $field['child'][$j]['id'] == 'campaign' ){
         //do nothing aye	
	}else{ 
		 echo "<li class='ui-state-default formfield form_field clearfix'>";
		 echo "<input class='mHiddenLabel' type='hidden' name='label' value='".$field['child'][$j]['label']."' />";
		 echo "<input type='hidden' name='type' value='".$field['child'][$j]['type']."' />";
		 echo "<input type='hidden' name='id' value='".$field['child'][$j]['id']."' />";
		 echo "<input type='hidden' name='code' value='".$field['child'][$j]['code']."' />";
		 echo "<input type='hidden' name='status' value='".$field['child'][$j]['status']."' />";

		 if( strcmp( $field['child'][$j]['code'],"miglad_" ) == 0 ){ 
			$disabled="disabled";
			$op="disabled"; 
			$field_id = $field['child'][$j]['id'];
			if( $field['child'][$j]['id'] == 'mg_add_to_milist' ){
			   $field_id = 'Mail List';
			}
			$field_id = str_ireplace('honoree', 'H', $field_id);
			$field_id = ucfirst($field_id);
		}else{ 
			$disabled="";
			$op="";
			$field_id = 'Label:';
		 }		 
		 
		 if ( array_key_exists("uid", $field['child'][$j] ) ){
			echo "<input type='hidden' name='uid' value='".$field['child'][$j]['uid']."' />";
		 }

		 echo "<div class='clabel col-sm-1 hidden-xs'><label class='control-label'>".__($field_id,"migla-donation")."</label></div>";
		 echo "<div class='col-sm-3 col-xs-12'><input type='text' name='labelChange' class='labelChange'  value='".$field['child'][$j]['label']."' /></div>";
		 echo "<div class='ctype col-sm-2 col-xs-12'>";

		 if( (string)$field['child'][$j]['type'] == "text" ){
			$arrShow[0] = "selected=selected";
		 }
		 if( (string)$field['child'][$j]['type'] == "checkbox" ){
			$arrShow[1] = "selected=selected";
		 }
		 if( (string)$field['child'][$j]['type'] == "textarea" ){
			$arrShow[2] = "selected=selected";
		 }
		 if( (string)$field['child'][$j]['type'] == "select" ){
			$arrShow[3] = "selected=selected";
		 }
		 if( (string)$field['child'][$j]['type'] == "radio" ){
			$arrShow[4] = "selected=selected";
		 }
		 if( (string)$field['child'][$j]['type'] == "multiple checkbox" ){
			$arrShow[5] = "selected=selected";
		 }

		   echo "<select name='typeChange' class='typeChange' id='s".$field['child'][$j]['id']."' ".$disabled." >";
		   echo "<option value='text' ".$arrShow[0].">".__("text","migla-donation")."</option>";
		   echo  "<option value='checkbox' ".$arrShow[1].">".__("checkbox","migla-donation")."</option>";
		   echo "<option value='textarea' ".$arrShow[2].">".__("textarea","migla-donation")."</option>";   
		   echo "<option value='select' ".$arrShow[3].">".__("select","migla-donation")."</option>";  
		   echo "<option value='radio' ".$arrShow[4].">".__("radio","migla-donation")."</option>";
		   echo "<option value='multiplecheckbox' ".$arrShow[5].">".__("multiple checkbox","migla-donation")."</option>";       

		 echo "</select>";

		 if( (string)$field['child'][$j]['code'] == "miglac_" )
		 {
			if( (string)$field['child'][$j]['type'] == "select" || (string)$field['child'][$j]['type'] == "radio" || (string)$field['child'][$j]['type'] == "multiplecheckbox" )
			{ 
			   echo "</div><div class='col-sm-2 col-xs-12'><button class='mbutton edit_select_value' id='mgval_".$field['child'][$j]['id']."' >".__("Enter Values","migla-donation"). "</button>";
			}
		 }

		 echo "</div>";

		 //echo "<div class='cid col-sm-2 hidden-xs'><label>".__("ID :","migla-donation"). " ".$f['child'][$j]['id']."</label></div>";
		 echo "<div class='ccode' style='display:none'>".$field['child'][$j]['code']."</div>";

		 if( $field['child'][$j]['id'] == 'amount' ){
			echo "<div class='control-radio-sortable col-sm-4 col-xs-12'>";
		 }else{
			echo "<div class='control-radio-sortable col-sm-4 col-xs-12'>";
		 }

		 $iid 	= $field['child'][$j]['id'];
		 $cekid = $field['child'][$j]['id'];

	   if( $cekid == 'amount' || $cekid == 'firstname' || $cekid == 'lastname' || $cekid == 'email'  )
	   { 

		  echo "<span><label class='".$disabled."'><input type='radio' name='".$iid."st'  value='1' class='".$disabled."' />".__(" Show","migla-donation")."</label></span>";
		  echo "<span><label class='".$disabled."'><input type='radio' name='".$iid."st'  value='0' class='".$disabled."' />".__(" Hide","migla-donation")."</label></span>";
		  echo "<span><label class='".$disabled."'><input type='radio' name='".$iid."st'  value='2' checked='checked' class='".$disabled."' />".__(" Mandatory","migla-donation")."</label></span>";

	   }else{
		 if( strcmp( $c['status'],"0") == 0 ){

		  echo "<span><label><input type='radio' name='".$iid."st' value='1' />".__(" Show","migla-donation")."</label></span>";
		  echo "<span><label><input type='radio' name='".$iid."st'  value='0' checked='checked' />".__(" Hide","migla-donation")."</label></span>";
		  echo "<span><label><input type='radio' name='".$iid."st'  value='2' />".__(" Mandatory","migla-donation")."</label></span>";

		 }else if( strcmp( $c['status'],"1") == 0){

		  echo "<span><label><input type='radio' name='".$iid."st'  value='1' checked='checked' />".__(" Show","migla-donation")."</label></span>";
		  echo "<span><label><input type='radio' name='".$iid."st'  value='0' />".__(" Hide","migla-donation")."</label></span>";
		  echo "<span><label><input type='radio' name='".$iid."st'  value='2' />".__(" Mandatory","migla-donation")."</label></span>";

		 }else if( strcmp( $c['status'],"2") == 0 || strcmp( $c['status'],"3") == 0 ){

		  echo "<span><label><input type='radio' name='".$iid."st'  value='1' />".__(" Show","migla-donation")."</label></span>";
		  echo "<span><label><input type='radio' name='".$iid."st'  value='0' />".__(" Hide","migla-donation")."</label></span>";
		  echo "<span><label><input type='radio' name='".$iid."st'  value='2' checked='checked' />".__(" Mandatory","migla-donation")."</label></span>";

		 }else{

		  echo "<span><label><input type='radio' name='".$iid."st'  value='1' checked='checked' />".__(" Show","migla-donation")."</label></span>";
		  echo "<span><label><input type='radio' name='".$iid."st'  value='0' />".__(" Hide","migla-donation")."</label></span>";
		  echo "<span><label><input type='radio' name='".$iid."st'  value='2' />".__(" Mandatory","migla-donation")."</label></span>";
		 }
	   }


		 echo "<span><button class='removeField ".$op."' ".$disabled."><i class='fa fa-fw fa-trash'></i></button></span>";
		 echo "</div>";
		 echo "</li>";
	}
	
	$i++;

   }//foreach
 }//if
}
 echo "</ul>";
 echo "</li>";

 }
}
echo "</ul>";

echo "<div class='row'><div class='col-sm-6'><button value='save' class='btn btn-info pbutton miglaSaveForm' id='miglaSaveFormBottom'><i class='fa fa-fw fa-save'></i>".__("  save form","migla-donation")."</button></div> <div class='col-sm-6'> <button id='miglaResetForm' class='btn btn-info rbutton pull-right' value='reset' data-toggle='modal' data-target='#confirm-reset'><i class='fa fa-fw fa-refresh'></i>".__("  Restore to Default","migla-donation")."</button></div></div>";
	
   echo "</div></div></section></div>";
	
    echo "</div>";

/************************************************************************************************/	
   echo "</div></div>"; //Tabs content
	


/////////////// End Divs //////////////////////////

    echo "</div>";

    echo "</div>";

    echo "</div></div>";
	
 echo " <div class='modal fade' id='confirm-reset' tabindex='-1' role='dialog' aria-labelledby='miglaWarning' aria-hidden='true' data-backdrop='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
            
                <div class='modal-header'>


                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true' data-target='#confirm-reset'><i class='fa fa-times'></i></button>
                    <h4 class='modal-title' id='miglaConfirm'>".__(" Confirm Restore","migla-donation")."</h4>
                </div>
            
<div class='modal-wrap clearfix'>

           <div class='modal-alert'>
														<i class='fa fa-times-circle'></i>
													</div>  

   <div class='modal-body'>
 <p>".__("Are you sure you want to restore to default fields? This cannot be undone","migla-donation")."</p>
                </div>

</div> 
                
                <div class='modal-footer'>
                    <button type='button' class='btn btn-default mbutton' data-dismiss='modal'>".__("Cancel","migla-donation")."</button>
                    <button type='button' class='btn btn-danger danger rbutton' id='miglaRestore'><i class='fa fa-fw fa-refresh'></i>".__("Restore to default","migla-donation")."</button>
                   
                </div>
            </div>
        </div>
    </div>"; 



echo "<div class='modal fade' id='mg_add_values' tabindex='-1' role='dialog' data-backdrop='true'>
        <div class='modal-dialog'>
          <div class='modal-content'>  
                <div class='modal-header'>
                    <button data-target='#mg_add_values' aria-hidden='true' data-dismiss='modal' class='close' type='button'><i class='fa fa-times'></i></button>
                    <h4 class='modal-title'>". __(" Edit Values","migla-donation"). " </h4>
                </div>
            
<div class='modal-wrap clearfix'>
   <div class='modal-body'>  
  <div class='form-horizontal'>";

  echo "<input type='hidden' value='".migla_get_select_values_postid()."' id='migla_custom_values_id' />"; 
  echo  "<div id='mg_id_custom_values_edit' style='display:none'></div>";
  
  echo "<div class='form-group '>
  
   <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'><label class='control-label' for='mg_add_value'>". __("Value","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>
     <input type='text' id='mg_add_value'><span class='help-control'>". __("The value stored in your database","migla-donation"). "</span></div><div class='col-sm-3 hidden-xs'></div></div>
  
    <div class='form-group '>
  
  <div class='col-sm-3 col-xs-12  text-right-sm text-center-xs'>   <label class='control-label' for='mg_add_label'>". __("Label","migla-donation"). "</label></div> 
  
  <div class='col-sm-6 col-xs-12'> <input type='text' id='mg_add_label'><span class='help-control'>". __("What the user sees on the form","migla-donation"). "</span> </div><div class='col-sm-3'> <button type='button' class='btn btn-info obutton' id='miglaAddCustomValueForm'><i class='fa fa-plus'></i>". __(" Add","migla-donation"). "</button></div></div>";
  
   
  echo "<div class='form-group '>
     <hr><div class='help-control-center'>". __("You can drag the list items to reorganize. Here are the available list values:","migla-donation"). "</div><br>";

  echo "<div class='col-sm-12 col-xs-12 text-center-sm'><i class='fa fa-fw fa-spinner fa-spin'></i></div>";

  echo "<div class='col-sm-12 col-xs-12 text-center-sm' id='mg_custom_list_container'>
        </div>

  </div> 
  </div> <!--Touching-->

  </div>
</div> 
                
                <div class='modal-footer'>                   
  <button type='button' class='btn btn-default mbutton' data-dismiss='modal'>". __("Cancel","migla-donation"). "</button> <button type='button' class='btn btn-info obutton' id='miglaAddCustomValues'><i class='fa fa-check'></i> ". __("great, I'm done","migla-donation"). "</button>
                </div>
                
            </div>";
echo "</div></div></div> ";      

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

        $x[0] = get_option('migla_thousandSep');
        $x[1] = get_option('migla_decimalSep');
        $showSep = get_option('migla_showDecimalSep');
        $numDecimal = 0;
		
		echo "<input type='hidden' id='mg_thousand_separator' value='".$x[0]."'>";	
		echo "<input type='hidden' id='mg_decimal_separator' value='".$x[1]."'>";	
		echo "<input type='hidden' id='mg_show_separator' value='".$showSep ."'>";	
		
		if( isset($_POST['form_id']) )
		{

  		    //print_r($_POST);
			$this->menu_form( $_POST['form_id'], $x[0], $x[1], $showSep );

		  echo "<form id='mg_form_campaign' action='".get_admin_url()."admin.php?page=migla_donation_campaigns_page' method='post' style='display:none'>";
		  echo "<input id='mg_submit_form' class='button' type='submit' value='test submit' name='submit' />";
		  echo "</form>"; 	
		  
		}else if( isset($_GET['form_id']) ){

		    //print_r($_GET);
			$this->menu_form( $_GET['form_id'], $x[0], $x[1], $showSep );

		  echo "<form id='mg_form_campaign' action='".get_admin_url()."admin.php?page=migla_donation_campaigns_page' method='post' style='display:none'>";
		  echo "<input id='mg_submit_form' class='button' type='submit' value='test submit' name='submit' />";
		  echo "</form>"; 				
			
		}else{
		
		  $this->menu_campaign();
			
		  echo "<form id='mg_form_campaign' action='".get_admin_url()."admin.php?page=migla_donation_campaigns_page' method='post' style='display:none'>";
		  echo "<input type='hidden' id='mg_form_id_send' name='form_id' value='' >";
		  echo "<input id='mg_submit_form' class='button' type='submit' value='test submit' name='submit' />";
		  echo "</form>"; 	
			
		}		

	}
	
}

?>