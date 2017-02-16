<?php
class migla_form_settings_class {

	function __construct(  ) {
		add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 12 );

	}
		
	function menu_item() {
		add_submenu_page(
			'migla_donation_menu_page',
			__( 'Form Options', 'migla-donation' ),
			__( 'Form Options', 'migla-donation' ),
			$this->get_capability() ,
			'migla_donation_form_options_page',
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
	
	

function migla_campaign_section( $postCampaign , $label )
{
   $out = "";

    $campaign = (array)get_option( 'migla_campaign' );
    $undesign = get_option('migla_undesignLabel');
    $_undesignated_label = str_replace( "[q]", "'", $undesign );  
   
   if( empty($campaign[0]) ){ 

    $out .= "<select name='campaign' class='mg_form-control' id='miglaform_campaign' disabled='disabled'>";
    $b = ""; $i = 0;   
    $out .= "<option value='". esc_html($undesign)."'>".esc_html($_undesignated_label)."</option>";
	$out .= "</select>"; 
			
  }else{    
    $b = ""; $i = 0;  $out2 = ""; $campaignCount = 0;
 	
    $out2 .= "<select name='campaign' class='mg_form-control' id='miglaform_campaign' disabled='disabled'>";

   if( get_option('migla_hideUndesignated') == 'no' ){
     $out2 .= "<option value='". esc_html($undesign)."'>".esc_html($_undesignated_label)."</option>";
     $campaignCount++;
   }
	
	foreach ( (array)$campaign as $key => $value ) 
	{ 
	    if( strcmp( $campaign[$i]['show'],"1")==0 ){
                  $campaignCount++;
                  $c_name = esc_html__( $campaign[$i]['name'] );
                  $c_name = str_replace( "[q]", "'", $c_name );

                  if( strcmp($c_name, $postCampaign) == 0  ){
		    $out2 .= "<option value='".esc_html__( $campaign[$i]['name'] )."' selected >".$c_name."</option>";
                  }else{
		    $out2 .= "<option value='".esc_html__( $campaign[$i]['name'] )."' >".$c_name."</option>";
                  }
	   }
           $i++;
	}  

	$out2 .= "</select>"; 

    if( $campaignCount > 0 ){
      $out .= $out2;	
   
    }else{

       $out .= "<select name='campaign' class='mg_form-control' id='miglaform_campaign' disabled='disabled'>";
       $b = ""; $i = 0;   
       $out .= "<option value='". esc_html($undesign)."'>".esc_html($undesign)."</option>";
       $out .= "</select>"; 
	
    }
  }	   
  
  return $out;

}

   function getSymbol(){
    $i = '';
    $currencies =  get_option( 'migla_currencies' ) ; 
    $def = get_option( 'migla_default_currency' );
	   foreach ( (array)$currencies as $key => $value ) 
	   { 
	     if ( strcmp($def,$currencies[$key]['code'] ) == 0 )
              { 
                 if( $currencies[$key]['faicon']!='' ) { 
                     $i = "<i class='fa fa-fw ".$currencies[$key]['faicon']."'></i>";
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
                
        echo "<h2 class='migla'>".__(" Form options","migla-donation")."</h2>";
		echo "<div class='row'>";
		echo "<div class='col-sm-12'>";
		
        $x[0] = get_option('migla_thousandSep');
        $x[1] = get_option('migla_decimalSep');
        $showSep = get_option('migla_showDecimalSep');
        $numDecimal = 0;
		
		echo "<input type='hidden' id='mg_thousand_separator' value='".$x[0]."'>";	
		echo "<input type='hidden' id='mg_decimal_separator' value='".$x[1]."'>";	
		echo "<input type='hidden' id='mg_show_separator' value='".$showSep ."'>";			
		

 echo  "<div class='form-horizontal'><ul class='nav nav-pills'>
        <li class='active' ><a data-toggle='tab' href='#section1'>".__("Multi Campaign Suggested Levels","migla-donation"). "</a></li>
        <li ><a data-toggle='tab' href='#section2'>".__("Country & Currency","migla-donation"). "</a></li>
        <li ><a data-toggle='tab' href='#section3'>".__("Multi Campaign Form Settings","migla-donation"). "</a></li>
        <li ><a data-toggle='tab' href='#section4'>".__("Recurring Settings","migla-donation"). "</a></li>
        <li ><a data-toggle='tab' href='#section5'>".__("Mailing List Integration","migla-donation"). "</a></li>
    </ul>";

    echo "<div class='tab-content nav-pills-tabs' >";
		
   /**********************************************************************************************************/	

    echo "<div id='section1' class='tab-pane  active' >";

		// Giving Levels Sections Table

		$amounts = get_option( 'migla_amounts' ) ;
        $curSymbol = $this->getSymbol();

		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseOne' aria-expanded='true'></a></div><h2 class='panel-title'><span>".$curSymbol."</span>".__("Suggested Giving Misc Options","migla-donation")."</h2></header>";
		echo "<div id='collapseOne' class='panel-body collapse in'>";

   /************** Hide custom amount   ***************************/
   $hide_custom_amount = get_option('migla_hideCustomAmount') ;

   echo "<div class='row'><div class='col-sm-3 col-xs-12'><label for='mHideHideCustomCheck' class='control-label text-right-sm text-center-xs'>".__("Hide Custom Amount on Form:","migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12 text-left-sm text-center-xs'><label for='mHideHideCustomCheck' class='checkbox-inline'>";
   if( $hide_custom_amount  == 'yes'  )
   {
		echo "<input type='checkbox' id='mHideHideCustomCheck' name='mHideHideCustomCheck' checked>";
   }else{
        echo "<input type='checkbox' id='mHideHideCustomCheck' name='mHideHideCustomCheck'>";
   }
   
   echo __("Check this if you want your donors not to be able to choose a custom amount","migla-donation")." </label></div>";
		
   echo "<div class='col-sm-3 col-xs-12'>";
   echo "</div>";
   echo "</div>";


   if( $hide_custom_amount == 'yes' )
   {
		echo "<div class='row' id='mg_div_custom_amount_text' style='display:none !important'>";
   }else{
		echo "<div class='row' id='mg_div_custom_amount_text'>";
   }
   
   $ctext = get_option( 'migla_custamounttext' );

   echo "<div class='col-sm-3 col-xs-12'><label for='mHideHideCustomText' class='control-label text-right-sm text-center-xs'>".__("Custom Amount Text:","migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12 text-left-sm text-center-xs'>  ";
   echo "<input type='text' id='mg_custom_amount_text' value='".$ctext."'></div></div>";

   
   /************** Level  ***************************/
   echo "<div class='row'><div class='col-sm-3 col-xs-12'><label for='mg_amount_btn_type' class='control-label text-right-sm text-center-xs'>".__("Choose the style of the giving level amounts:","migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12 text-left-sm text-center-xs'>";
   echo "<select id='mg_amount_btn_type'>";
   if( get_option('migla_amount_btn') == 'button' )
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
   
/*   
echo "<div class='row'>
    <div class='col-sm-3 col-xs-12'>
        <label class='control-label text-right-sm text-center-xs' for='mReverseOrderCheck'>".__("Reverse Giving Level Order:","migla-donation"). "</label>
    </div>
    <div class='col-sm-6 col-xs-12 text-left-sm text-center-xs'>";

   $sort = get_option('migla_sort_level');
   if( $sort == FALSE || $sort == 'none' )
   {		
		echo "<label class='checkbox-inline' for='mReverseOrderCheck'>";
		echo "<select id='mReverseOrderCheck'>";
		echo "<option value='none' checked>None</option>";
		echo "<option value='sort'>Sort Ascending</option>";
		echo "<option value='rsort'>Sort Descending</option>";
		echo "</select>";
	    echo "</label></div>";   
			
   }else if( $sort == 'rsort' ){
		echo "<label class='checkbox-inline' for='mReverseOrderCheck'>";
		echo "<select id='mReverseOrderCheck'>";
		echo "<option value='none'>None</option>";
		echo "<option value='sort'>Sort Ascending</option>";
		echo "<option value='rsort' checked>Sort Descending</option>";
		echo "</select>";
	    echo "</label></div>"; 
        if( $amounts != false ) rsort($amounts); 		
   }else{			
	    echo "<label class='checkbox-inline' for='mReverseOrderCheck'>";
		echo "<select id='mReverseOrderCheck'>";
		echo "<option value='none'>None</option>";
		echo "<option value='sort' checked>Sort Ascending</option>";
		echo "<option value='rsort'>Sort Descending</option>";
		echo "</select>";
	    echo "</label></div>"; 
        if( $amounts != false ) sort($amounts); 		
   }
   echo "<div class='col-sm-3 col-xs-12'></div>";
   echo "</div>";   
*/   

   /************** Box Width  ***************************/
echo "<div class='row'><div class='col-sm-3 col-xs-12'><label for='mg_amount_box_type' class='control-label text-right-sm text-center-xs'>".__("Choose the length of the giving level amount boxes:","migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12 text-left-sm text-center-xs'>";
   
   $amount_box_type = get_option('migla_amount_box_type');
   
   echo "<select id='migla_amount_box_type'>";
   if( $amount_box_type == 'box' )
   {
		echo "<option value='fill'>Fill Form</option>";
		echo "<option value='box' selected>Box</option>";
   }else{
		echo "<option value='fill' selected>Fill Form</option>";
		echo "<option value='box'>Box</option>";   
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





																				echo "<div class='center-button col-sm-12'><button id='miglaAddAmountButton' class='btn btn-info pbutton msave' value='save'>".__(" Add","migla-donation")."</button>";
		
																				echo "</div></div>";
				
        echo "<div id='miglaAmountTable'>";	

	   if( isset($amounts[0]) )
	   {
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
				$amount = get_option( 'migla_amounts' ) ;
			}
	   
		    foreach( $keys_amount as $key )
            {
			     $valLabel = $amounts[$key]['amount'] ; 
				 $valPerk = $amounts[$key]['perk'];

                 if( $showSep == 'yes' ){
                        $valLabel = str_replace(".", $x[1] , $valLabel  );
                 }else{
                        $digit = explode( ".", $valLabel  ) ;
                        $valLabel = $digit[0];
                 }
					 					
				 echo "<p class='mg_amount_level'>";
				 echo "<input class='mg_amount_level_value' type=hidden value='".$amounts[$key]['amount']."' />";
				 echo "<label>". $valLabel. "</label>";	
				 echo "<label class='mg_amount_level_perk'>".$valPerk. "</label>";
				   
				 echo "<button name='miglaAmounts' class='miglaRemoveLevel obutton'><i class='fa fa-times'></i></button>";
				 echo "</p>";
			   $idx++;
			}		 
    } 	   

    echo "<p id='warningEmptyAmounts' style='display:none'>".__("No amounts have been added. Add some amounts above.","migla-donation")."<i class='fa fa-fw fa-caret-up'></i></p>";

	echo "</div>";	
	
	echo "</section>";

    echo "</div>";
    
   /**********************************************************************************************************/	

   echo "<div id='section2' class='tab-pane ' >";
	
   echo "<div class='row'>";

   echo "<div class='col-sm-6'>";
		$currencies = get_option( 'migla_currencies' ) ; 
                $icon ='';
                $def = get_option( 'migla_default_currency' );
  
	   echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseTwo' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa fa-fw fa-money'></i>".__("Default Currency Selection","migla-donation")."</h2></header>";
	   echo "<div id='collapseTwo' class='panel-body  collaspe in'><div class='row'>";
	   echo "<div class='col-sm-3 col-xs-12'><label for='miglaDefaultCurrency' class='control-label text-right-sm text-center-xs'>". __("Set currency","migla-donation");
           echo "</label></div>";
           echo "<div class='col-sm-6 col-xs-12'><select id='miglaDefaultCurrency' name='miglaDefaultCurrency'>"; 
	   foreach ( (array)$currencies as $key => $value ) 
	   { 
	      if ( strcmp($def,$currencies[$key]['code'] ) == 0 )
              { 
                 echo "<option value='".$currencies[$key]['code']."' selected='selected' >".$currencies[$key]['code']."</option>"; 
                 if( $currencies[$key]['faicon']!='' ) { 
                     $icon = "<i class='fa ".$currencies[$key]['faicon']."'></i>";
                     //$icon = $currencies[$key]['faicon']; 
                 }else{ $icon = $currencies[$key]['symbol']; }
              }else{  
                 echo "<option value='".$currencies[$key]['code']."'>".$currencies[$key]['code']."</option>"; 
              }
	   }	   
	   echo "</select></div>";
	   

    if( strcmp($showSep,"yes")==0 ){ $numDecimal = 2; } 

    $num = number_format("10000", $numDecimal, $x[1], $x[0]);

   $placement = get_option('migla_curplacement');
   if( strtolower( $placement ) == 'before' ){
      $before = $icon; $after='';
   }else{
     $before = ''; $after= $icon;
  }
  echo "<div style='display:none' id='sep1'>".$x[0]."</div>";
  echo "<div style='display:none' id='sep2'>".$x[1]."</div>";
  echo "<div style='display:none' id='placement'>".$placement."</div>";
  echo "<div style='display:none' id='showDecimal'>".$showSep."</div>";
  echo "<div style='display:none' id='icon'>".$icon."</div>";
 			   
           echo "<div class='col-sm-3 hidden-xs' id='currencyIcon'>";
		   echo "<label id='miglabefore'>".$before."</label><label id='miglanum'>".$num."</label>";
		   echo "<label id='miglaafter'>".$after."</label></div>"; 

echo"</div><div class='row'><div class='col-sm-3 col-xs-12'><label for='miglaDefaultPlacement' class='control-label text-right-sm text-center-xs'>".__("Sign Location","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'>";
echo "<select name='miglaDefaultplacement' id='miglaDefaultPlacement'>";
if( strtolower( $placement ) == 'before' ){
echo "<option value='before' checked>".__("Before","migla-donation")."</option><option value='after'>".__("After","migla-donation")."</option>";
}else{
echo "<option value='before' >".__("Before","migla-donation")."</option><option value='after' checked>".__("After","migla-donation")."</option>";
}
echo "</select></div></div>";

echo "<div class='row'> <div class='col-sm-3 col-xs-12'><label for='thousandSep' class='control-label text-right-sm text-center-xs'>".__("Separators","migla-donation")."</label></div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'> <input type='text' placeholder='Thousands' class=' form-control' id='thousandSep' name='thousandSep'>";

echo "</div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'> <input type='text' placeholder='Decimal' class=' form-control' id='decimalSep' name='decimalSep'> </div> </div>";

$checkShowSep = "";
if( strcmp($showSep , "yes") == 0 ){ $checkShowSep = "checked"; }
echo "<div class='row'><div class='col-sm-3 col-xs-12'><label  for='mHideDecimalCheck' class='control-label text-right-sm text-center-xs'>".__("Show Decimal Place","migla-donation")."</label></div><div class='col-sm-6 col-xs-12 text-left-sm text-center-xs'><label class='checkbox-inline'><input type='checkbox' name='mHideDecimalCheck' id='mHideDecimalCheck' ".$checkShowSep." >".__("check this if you want donors to be able to add decimal places","migla-donation")."</label></div><div class='col-sm-3 hidden-xs'></div></div>";

	
		echo "<div class='row'><div class='col-sm-12 center-button'><button value='save' class='btn btn-info pbutton msave' id='miglaSetCurrencyButton'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div>";
		echo "</div></div>";
				
		echo "</section></div>";

		///////////////////////////////////////////////////////////////
		// Country Section	
		echo "<div class='col-sm-6'>";	
		$countries = get_option( 'migla_world_countries' );
		
	   echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseThree' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa fa-fw fa-flag'></i>".__("Default Country Section","migla-donation")."</h2></header>";
	      echo "<div id='collapseThree' class='panel-body collapse in'><div class='row'>";
	   echo "<div class='col-sm-3 col-xs-12'><label for='miglaDefaultCountry' class='control-label text-right-sm text-center-xs'>". __("Set country","migla-donation");
        echo "</label></div>";
       echo "<div class='col-sm-6 col-xs-12'><select id='miglaDefaultCountry' name='miglaDefaultCountry'>"; 
	   foreach ( (array) $countries as $key => $value ) 
	   { 
	      if ( $value == get_option( 'migla_default_country' ) )
		  { 
		    echo "<option value='".$value."' selected >".$value."</option>"; 
		  }else{  
		    echo "<option value='".$value."'>".$value."</option>"; 
		  }
	   }	   
	   echo "</select></div><div class='col-sm-3 hidden-xs'></div></div>"; 
		
		echo "<div class='row'><div class='col-sm-12 center-button'><button value='save' class='btn btn-info pbutton' id='miglaSetCountryButton'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button>";
		
		echo "</div></div></section>";	
		echo "</div>";
    echo "</div></div>";

   
	
   /**********************************************************************************************************/	
	
    echo "<div id='section3' class='tab-pane ' >";
    echo "<div class='row'>";
  //  echo "<div class='col-sm-12'>";
   
///////////////////////////////Undesignated Campaign
  $label = get_option('migla_undesignLabel');
  if( $label == false ){ 
	add_option('migla_undesignLabel', 'undesignated'); 
  }
  if( $label == '' ){ 
	$label = 'undesignated'; 
  }

  $label_value = str_replace( '[q]', '"', $label );

  $hidelabel = get_option('migla_hideUndesignated');
  if( $hidelabel == false ){ 
	add_option('migla_hideUndesignated', 'no'); 
  }
  if( $hidelabel == '' ){ 
	$hidelabel = 'no'; 
  }

  echo "<input type='hidden' id='mg_oldUnLabel' value='".$label."'>";
		

echo "<div class='col-sm-12'><section class='panel'><header class='panel-heading'><div class='panel-actions'><a aria-expanded='true' href='#collapseFive' data-parent='.panel' data-toggle='collapse' class='fa fa-caret-down '></a></div><h2 class='panel-title'><i class='fa fa-fw fa-bullhorn'></i>".__("Multi-Campaign Form Options","migla-donation")."</h2></header><div id='collapseFive' class='panel-body collapse in'>


<div class='row'><div class='col-sm-3'><label class='miglaCampaignTargetLabel control-label  text-right-sm text-center-xs' for='mg-undesignated-default'>".__("Undesignated Category Label:","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'><input type='text' id='mg-undesignated-default' ";
echo 'placeholder="';
echo $label.'"';
echo " class='form-control ' value='".$label_value."'></div><div class='col-sm-3 hidden-xs'></div></div>
<div class='row'><div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs' for='mHideUndesignatedCheck'>".__("Hide Undesignated Category on Form:","migla-donation")."</label></div><div class='col-sm-9 col-xs-12 text-left-sm text-center-xs'>";

if(  $hidelabel == 'yes' ){
echo "<label class='checkbox-inline' for='mHideUndesignatedCheck'><input type='checkbox' name='mHideUndesignatedCheck' id='mHideUndesignatedCheck' checked='checked'>".__("Check this if you want your donors to only be able to donate towards a campaign ","migla-donation")." </div><div class='col-sm-3 hidden-xs'></label></div></div>";
} else{
echo "<label class='checkbox-inline' for='mHideUndesignatedCheck'><input type='checkbox' name='mHideUndesignatedCheck' id='mHideUndesignatedCheck' >".__("Check this if you want your donors to only be able to donate towards a campaign ","migla-donation")." </div><div class='col-sm-3 hidden-xs'></label></div></div>";
}


/************** Hide progress bar  ***************************/

echo "<div class='row'>
    <div class='col-sm-3 col-xs-12'>
        <label class='control-label text-right-sm text-center-xs' for='mHideProgressBarCheck'>".__("Hide Progress Bar on Form:","migla-donation")."</label>
    </div>
    <div class='col-sm-9 col-xs-12 text-left-sm text-center-xs'>";

   $showbar = get_option('migla_show_bar') ;   
   if( $showbar == FALSE ){
       update_option('migla_show_bar', 'no') ;
       echo  "<label class='checkbox-inline' for='mHideProgressBarCheck'><input checked='checked' id='mHideProgressBarCheck' name='mHideProgressBarCheck' type='checkbox'>".
             __("If checked, no progress bar or its accompanying text will be displayed on the form. ","migla-donation"). "</label></div>";
   }else if( $showbar == 'no' ){
       echo  "<label class='checkbox-inline' for='mHideProgressBarCheck'><input checked='checked' id='mHideProgressBarCheck' name='mHideProgressBarCheck' type='checkbox'>".
             __("If checked, no progress bar or its accompanying text will be displayed on the form. ","migla-donation"). "</label></div>";
   }else{
       echo  "<label class='checkbox-inline' for='mHideProgressBarCheck'><input id='mHideProgressBarCheck' name='mHideProgressBarCheck' type='checkbox'>".
             __("If checked, no progress bar or its accompanying text will be displayed on the form. ","migla-donation"). "</label></div>";
   }
echo "<div class='col-sm-3 hidden-xs'></div>
       </div>";







/**** error message for the general fields ****/
$warning1 = get_option('migla_warning_1'); //Please insert all the required fields
$warning2 = get_option('migla_warning_2'); //Please insert correct email
$warning3 = get_option('migla_warning_3'); //please fill in a valid amount

echo "<div class='row'><div class='col-sm-3'><label class='miglaErrorGeneralLabel control-label  text-right-sm text-center-xs' for='mg-errorgeneral-default'>".__("Error Message Label for the General Fields:","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'><input type='text' id='mg-errorgeneral-default' ";

echo " class='form-control ' value='".$warning1 ."' placeholder='".$warning1 ."'></div><div class='col-sm-3 hidden-xs'></div></div>";


/**** error message for the emails ****/


echo "<div class='row'><div class='col-sm-3'><label class='miglaErrorEmailLabel control-label  text-right-sm text-center-xs' for='mg-erroremail-default'>".__("Error Message Label for the Email Field:","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'><input type='text' id='mg-erroremail-default' ";

echo " class='form-control ' value='".$warning2."' placeholder='".$warning2 ."'></div><div class='col-sm-3 hidden-xs'></div></div>";


/**** error message for the amounts ****/


echo "<div class='row'><div class='col-sm-3'><label class='miglaErrorAmountLabel control-label  text-right-sm text-center-xs' for='mg-erroramount-default'>".__("Error Message Label for the Amount:","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'><input type='text' id='mg-erroramount-default' ";

echo " class='form-control ' value='".$warning3 ."' placeholder='".$warning3 ."'></div><div class='col-sm-3 hidden-xs'></div></div>";





/**************** end astried edit *************/



echo "<div class='row'><div class='col-sm-12 center-button'><button id='miglaUnLabelChange' class='btn btn-info pbutton msave' value='save'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div></div>
</div></section></div>";
		
		///////////////////////////////////////////////////////////////////////////////////////


		// Fields and Sections Table PART 3
		echo "<div class='col-sm-12 hidden-xs'>";
		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseFour' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa fa-fw fa-check-square-o'></i>".__("Form Fields","migla-donation")."<span class='panel-subtitle'>".__("Drag and drop fields and groups or add new ones","migla-donation")."</span></h2></header><div id='collapseFour' class='panel-body collapse in'><div class='row'><div class='col-sm-12 groupbutton'><button value='save' class='btn btn-info pbutton miglaSaveForm' id='miglaSaveFormTop'><i class='fa fa-fw fa-save'></i>".__(" save form","migla-donation")."</button><button class='btn btn-info obutton mAddGroup' value='add'><i class='fa fa-fw fa-plus-square-o'></i>".__("Add Group","migla-donation")."</button></div>";


////////HERE
echo "<div id='divAddGroup' class='col-sm-12'  style='display:none'><div class='addAgroup'><div class='row'><div class='col-sm-4'><div class='row'><div class='col-sm-2'> <i class='fa fa-bars bar-icon-styling'></i></div>
<div class='col-sm-10'> <input type='text' id='labelNewGroup' placeholder='".__("insert new header for group","migla-donation")."' /> </div></div>
</div><div class='col-sm-4'><div class='col-sm-5'><input type='checkbox' id='t' checked='false' class='toggle' id='toggleNewGroup' /><label>".__("Toggle","migla-donation")."</label></div></div>
<div class='col-sm-4 addfield-button-control alignright'><button type='button' class='btn btn-default mbutton' id='cancelAddGroup'>".__("Cancel","migla-donation")."</button> <button type='button' class='btn btn-info inputFieldbtn pbutton' id='saveAddGroup'><i class='fa fa-fw fa-save'></i>".__(" Save Group","migla-donation")."</button></div>
</div></div>
</div>";

//////////END OF HERE

// echo "</div>";


 echo "<div class='col-sm-12'>";

$d 				= get_option( 'migla_form_fields' );
$custAmountText = get_option('migla_custamounttext');

if( $custAmountText == false ){ 
		add_option('migla_custamounttext', 'Custom Amount'); 
}

if( $custAmountText == '' ){ 
		update_option('migla_custamounttext', 'Custom Amount'); 
}

$selectedCampaign = get_option('migla_selectedCampaign');
if( $selectedCampaign == false ){ 
	add_option('migla_selectedCampaign', $label ); 
}
if( $selectedCampaign == '' ){ 
	update_option('migla_selectedCampaign', $label ); 
}

echo "<ul class='containers'>";
$id = 0; $i = 0;

if( !empty( $d ) && $d[0] !='')
{
foreach ( (array) $d as $f ){

 echo "<li class='title formheader'><div class='row'><div class='col-sm-4'><div class='row'><div class='col-sm-2'>";
echo "<i class='fa fa-bars bar-icon-styling'></i></div><div class='col-sm-10'> "; 
echo "<input type='text' class='titleChange'  placeholder='" . $f['title']."' name='grouptitle' value='" . $f['title']."'>  </div> ";
echo "</div></div>
      <div class='col-sm-4'>"; 

if(  strcmp( $f['toggle'], '-1') == 0){
 echo "<div class='col-sm-5'><input type='checkbox' id='t".$id."'  class='toggle' disabled><label>".__("Toggle","migla-donation")."</label></div>";
}else if(strcmp( $f['toggle'], '1') == 0){
 echo "<div class='col-sm-5'><input type='checkbox' id='t".$id."' checked='checked' class='toggle' /><label>".__("Toggle","migla-donation")."</label></div>";
}else{
 echo "<div class='col-sm-5'><input type='checkbox' id='t".$id."'  class='toggle' disabled><label>".__("Toggle","migla-donation")."</label></div>";
}
$id++;

echo "<button value='add' class='btn btn-info obutton mAddField addfield-button-control' ><i class='fa fa-fw fa-plus-square-o'></i>".__("Add Field","migla-donation")."</button> </div>";

 echo "<div class='col-sm-4 text-right-sm text-right-xs divDelGroup' >  <button class='rbutton btn btn-danger mDeleteGroup pull-right' ><i class='fa fa-fw fa-trash'></i>".__("Delete Group","migla-donation")."</button>  
  </div>";

 echo "</div>";

 echo "<input class='mHiddenTitle' type='hidden' name='title' value='".$f['title']."' />";

 $ulId = str_replace(" ","", $f['title']);

 echo "<ul class='rows' id='".$ulId."' >";

 if ( count((array) $f['child']) > 0 )
 {
	//print_r($f['child']);
	$j = -1;
   foreach ( (array)$f['child'] as $c )
   {
     $j++;
     $arrShow = array();
     $arrShow[0] = "";$arrShow[1] = "";$arrShow[2] = "";$arrShow[3] = "";$arrShow[4] = "";$arrShow[5] = "";

     echo "<li class='ui-state-default formfield clearfix'>";
     echo "<input class='mHiddenLabel' type='hidden' name='label' value='".$f['child'][$j]['label']."' />";
     echo "<input type='hidden' name='type' value='".$f['child'][$j]['type']."' />";
     echo "<input type='hidden' name='id' value='".$f['child'][$j]['id']."' />";
     echo "<input type='hidden' name='code' value='".$f['child'][$j]['code']."' />";
     echo "<input type='hidden' name='status' value='".$f['child'][$j]['status']."' />";

     if ( array_key_exists("uid", $f['child'][$j] ) ){
        echo "<input type='hidden' name='uid' value='".$f['child'][$j]['uid']."' />";
     }

		 if( strcmp( $f['child'][$j]['code'],"miglad_" ) == 0 ){ 
			$disabled="disabled";
			$op="disabled"; 
			$field_id = $f['child'][$j]['id'];
			$field_id = str_ireplace( 'honoree', 'H' , $field_id );
			if( $field_id == 'mg_add_to_milist' )
			{
			   $field_id = 'mail list';
			}
		 }else{ 
			$disabled="";
			$op="";
			$field_id = 'Label';
		 }	

     echo "<div class='clabel col-sm-1 hidden-xs'><label class='control-label'>".__($field_id,"migla-donation")."</label></div>";
     echo "<div class='col-sm-3 col-xs-12'><input type='text' name='labelChange' class='labelChange'  value='".$f['child'][$j]['label']."' /></div>";
     echo "<div class='ctype col-sm-2 col-xs-12'>";
     

     if( (string)$f['child'][$j]['type'] == "text" ){
        $arrShow[0] = "selected=selected";
     }
     if( (string)$f['child'][$j]['type'] == "checkbox" ){
        $arrShow[1] = "selected=selected";
     }
     if( (string)$f['child'][$j]['type'] == "textarea" ){
        $arrShow[2] = "selected=selected";
     }
     if( (string)$f['child'][$j]['type'] == "select" ){
        $arrShow[3] = "selected=selected";
     }
     if( (string)$f['child'][$j]['type'] == "radio" ){
        $arrShow[4] = "selected=selected";
     }
     if( (string)$f['child'][$j]['type'] == "multiple checkbox" ){
        $arrShow[5] = "selected=selected";
     }

       echo "<select name='typeChange' class='typeChange' id='s".$f['child'][$j]['id']."' ".$disabled." >";
       echo "<option value='text' ".$arrShow[0].">".__("text","migla-donation")."</option>";
       echo  "<option value='checkbox' ".$arrShow[1].">".__("checkbox","migla-donation")."</option>";
       echo "<option value='textarea' ".$arrShow[2].">".__("textarea","migla-donation")."</option>";   
       echo "<option value='select' ".$arrShow[3].">".__("select","migla-donation")."</option>";  
       echo "<option value='radio' ".$arrShow[4].">".__("radio","migla-donation")."</option>";
       echo "<option value='multiplecheckbox' ".$arrShow[5].">".__("multiple checkbox","migla-donation")."</option>";       

     echo "</select>";

     if( (string)$f['child'][$j]['code'] == "miglac_" )
     {
        if( (string)$f['child'][$j]['type'] == "select" || (string)$f['child'][$j]['type'] == "radio" || (string)$f['child'][$j]['type'] == "multiplecheckbox" )
        { 
           echo "</div><div class='col-sm-2 col-xs-12'><button class='mbutton edit_select_value' id='mgval_".$f['child'][$j]['id']."' >".__("Enter Values","migla-donation"). "</button>";
        }
     }

     echo "</div>";

      /*
     if( $f['child'][$j]['id'] == 'amount' ){
       echo "<div class='col-sm-2 col-xs-12'><input type='text' id='migla_custAmountTxt' value='".$custAmountText."'></div>";
     }
     */

     //echo "<div class='cid col-sm-2 hidden-xs'><label>".__("ID :","migla-donation"). " ".$f['child'][$j]['id']."</label></div>";
     echo "<div class='ccode' style='display:none'>".$f['child'][$j]['code']."</div>";

     if( $f['child'][$j]['id'] == 'amount' ){
        echo "<div class='control-radio-sortable col-sm-4 col-xs-12'>";
     }else{
        echo "<div class='control-radio-sortable col-sm-4 col-xs-12'>";
     }

     $iid = $f['child'][$j]['id'];
     $cekid = $f['child'][$j]['id'];

   if( $cekid == 'amount' || $cekid == 'firstname' || $cekid == 'lastname' || $cekid == 'email' || $cekid == 'campaign' )
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


     echo "<span><button class='removeField ".$op."' ".$disabled."><i class='fa fa-fw fa-trash'></i></button></span></div>";
     echo "</li>";

$i++;

   }//foreach
 }//if

 echo "</ul>";
 echo "</li>";

}//foreach
}//if not empty
echo "</ul>";

echo "<div class='row'><div class='col-sm-6'><button value='save' class='btn btn-info pbutton miglaSaveForm' id='miglaSaveFormBottom'><i class='fa fa-fw fa-save'></i>".__("  save form","migla-donation")."</button></div> <div class='col-sm-6'> <button id='miglaResetForm' class='btn btn-info rbutton pull-right' value='reset' data-toggle='modal' data-target='#confirm-reset'><i class='fa fa-fw fa-refresh'></i>".__("  Restore to Default","migla-donation")."</button></div></div>";
	
   echo "</div></div></section></div>";
//   echo "</div></div>";		
   echo "</div></div>"; //Tabs content
	

/******************************************************************************************************************************/

   echo "<div id='section4' class='tab-pane ' >";
    echo "<div class='row'>";
    echo "<div class='col-sm-12'>";

echo "<section class='panel'>
      <header class='panel-heading'>
         <div class='panel-actions'><a aria-expanded='true' href='#collapseNine' data-parent='.panel' data-toggle='collapse' class='fa fa-caret-down'></a></div>
         <h2 class='panel-title'><i class='fa fa-fw fa-refresh'></i></i>". __("Add A New Recurring Donation Plan","migla-donation"). "</h2>
      </header>
      <div class='panel-body collapse in' id='collapseNine'>";

echo "<div class='row'>";
echo "<div class='col-sm-12'>";


		echo "<div class='row'><div class='col-sm-3'><label for='migla_planName' class='control-label text-right-sm text-center-xs'>". __("Label","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<input type='text' id='migla_planName' class='form-control' placeholder='e.g. every two weeks'></div></div>";


       echo "<div class='row'><div class='col-sm-3'><label for='migla_planTime' class='control-label text-right-sm text-center-xs'>". __("Interval Count","migla-donation"). "</label></div>";

        echo  "<div class='col-sm-2 col-xs-12'>


<div data-plugin-spinner='' data-plugin-options='{ }'>
		<div class='input-group' style=''>
<input class='spinner-input form-control time'  min='1' name='' placeholder='' value='1' id='migla_planTime'>
     <div class='spinner-buttons input-group-btn'>
    <button type='button' class='btn btn-default spinner-up'>
    <i class='fa fa-angle-up'></i>
																</button>
																<button type='button' class='btn btn-default spinner-down'>
																	<i class='fa fa-angle-down'></i>
																</button>
															</div>
														</div>
															
		</div>
</div>



               <div class='col-sm-4 col-xs-12'>
                  <select id='migla_planPeriod'>
                     <option value='day' selected='selected'>". __("Day(s)","migla-donation"). "</option>
                     <option value='week'>". __("Week(s)","migla-donation"). "</option>
                     <option value='month'>". __("Month(s)","migla-donation"). "</option>
                     <option value='year'>". __("Year(s)","migla-donation"). "</option>
                  </select>
               </div>";
         echo "</div>";


		echo "<div class='row'><div class='col-sm-3'><label for='migla_planMethod' class='control-label text-right-sm text-center-xs'>". __("Payment Gateway","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";

                echo  "<select id='migla_planMethod'>                 
                         <option value='paypal' selected>". __("PayPal Only","migla-donation"). "</option>
                         <option value='stripe' >". __("Stripe Only","migla-donation"). "</option>
                         <option value='authorize'>". __("Authorize Only","migla-donation"). "</option>
                         <option value='paypal-stripe'>". __("PayPal or Stripe","migla-donation"). "</option>
                         <option value='paypal-authorize'>". __("PayPal or Authorize","migla-donation"). "</option>
                         <option value='stripe-authorize'>". __("Stripe or Authorize","migla-donation"). "</option>
                         <option value='paypal-stripe-authorize'>". __("PayPal/Stripe/Authorize","migla-donation"). "</option>
                        </select>";

                echo "</div><span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'><span style='color: rgb(208, 47, 47); '>". __(" Warning: ","migla-donation"). "</span>". __(" Gateways must match your form's gateway(s) in order to be displayed on the form","migla-donation"). "</span></div>";

		echo "<div class='row'><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div><div class='col-sm-6'><br><button id='miglaAddPlan' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation"). "</button></div></div>";

		echo "</div>";
      echo "</div>
   </section>
</div>";


 $plans = (array)get_option( 'migla_recurring_plans' ) ; $count = 0;

 echo "<div class='col-sm-12'>
   <section class='panel'>
      <header class='panel-heading'>
         <div class='panel-actions'><a aria-expanded='true' href='#collapseTen' data-parent='.panel' data-toggle='collapse' class='fa fa-caret-down'></a></div>
         <h2 class='panel-title'><i class='fa fa-fw fa-list'></i>". __("Current Recurring Donation Plans","migla-donation"). "</h2>
      </header>
      <div class='panel-body collapse in' id='collapseTen'>
         <ul class='mg_recurring_row containers ui-sortable'>";

   if( $plans[0] !='' )
   {
     $row = 0;
	 $m = array(); $isThisEnabled = array();
     foreach( (array)$plans as $plan )
     {
         $uid_row = "planbtn".date("Ymdhis") . rand();

         $checked = array(); $checked['day'] = ""; $checked['week'] = ""; $checked['month'] = ""; $checked['year'] = "";
         switch ($plan['interval']) 
         {
           case "day": $checked['day'] = 'selected'; break;
           case "week": $checked['week'] = 'selected'; break;
           case "month": $checked['month'] = 'selected'; break;
           case "year": $checked['year'] = 'selected'; break;
         }

         $m[$row]['paypal'] = ""; $m[$row]['stripe'] = ""; $m[$row]['paypal-stripe'] = "";
         $isThisEnabled[$row] = '';

		 /*
         switch ( $plan['payment_method'] ) {
             case "paypal"       	: $m[$row]['paypal'] = "selected='selected'"; break;
             case "authorize"           : $m[$row]['authorize'] = "selected='selected'"; break;
             case "stripe"		: $m[$row]['stripe'] = "selected='selected'"; $isThisEnabled[$row] = 'disabled'; break;
             case "paypal-stripe"	: $m[$row]['paypal-stripe'] = "selected='selected'"; $isThisEnabled[$row] = 'disabled'; break;
             case "paypal-authorize"	: $m[$row]['paypal-authorize'] = "selected='selected'"; break;
             case "stripe-authorize"		: $m[$row]['stripe-authorize'] = "selected='selected'"; $isThisEnabled[$row] = 'disabled'; break;
             case "paypal-stripe-authorize"	: $m[$row]['paypal-stripe-authorize'] = "selected='selected'"; $isThisEnabled[$row] = 'disabled'; break;			 
         }
         */
          echo "<li class='mg_reoccuring-field clearfix title formheader ui-sortable-handle '>  
             <input type='hidden' class='old_id' value='".$plan['id']."'>
             <input type='hidden' class='old_name' value='".$plan['name']."'>
             <input type='hidden' class='old_time' value='".$plan['interval_count']."'>
             <input type='hidden' class='old_period' value='".$plan['interval']."'>
             <input type='hidden' class='old_status' value='".$plan['status']."'>
             <input type='hidden' class='old_method' value='".$plan['payment_method']."'>

              <div class='rows'> <div class='col-sm-1 clabel'>
                  <label class='control-label '>". __("Label","migla-donation"). "</label>
                  </div>
               <div class='col-sm-2 col-xs-12'><input type='text' class='name' name='' placeholder='' value='".$plan['name']."'></div>
               <div class='col-sm-1 hidden-xs'><label class='control-label'>". __("Interval","migla-donation"). "</label></div>
            

<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
   <div data-plugin-spinner='' data-plugin-options='{ }'>
		<div class='input-group' style=''><input type='text' value='".$plan['interval_count']."' class='spinner-input form-control time' maxlength='2' ".$isThisEnabled[$row].">
     <div class='spinner-buttons input-group-btn'>
    <button type='button' class='btn btn-default spinner-up' ".$isThisEnabled[$row].">
    <i class='fa fa-angle-up'></i>
																</button>
   <button type='button' class='btn btn-default spinner-down' ".$isThisEnabled[$row].">
																	<i class='fa fa-angle-down'></i>
																</button>
															</div>
														</div>
															
		</div>
		</div>

               <div class='col-sm-2 col-xs-12  '>
                  <select id='period".$plan['id']."' class='period' name='' ".$isThisEnabled[$row].">                 
                  <option value='day' ".$checked['day'].">". __("Day(s)","migla-donation"). "</option>
                     <option value='week' ".$checked['week'].">". __("Week(s)","migla-donation"). "</option>
                     <option value='month' ".$checked['month'].">". __("Month(s)","migla-donation"). "</option>
                     <option value='year' ".$checked['year'].">". __("Year(s)","migla-donation"). "</option>
                  </select>
               </div>";

        if($plan['status'] == '1' ){
          echo "<div class='control-radio-sortable col-sm-3 col-xs-12 form-group touching'><span><label><input type='radio' class='status' value='1' name='status".$plan['id']."' checked>". __(" Show","migla-donation"). "</label></span><span><label><input type='radio' class='status' value='0' name='status".$plan['id']."'>". __(" Deactivate","migla-donation"). "</label></span><span><button class='removePlanField'><i class='fa fa-fw fa-trash'></i></button></span></div>
           </div> ";
         }else{
          echo "<div class='control-radio-sortable col-sm-3 col-xs-12 form-group touching'><span><label><input type='radio' class='status' value='1' name='status".$plan['id']."'>". __(" Show","migla-donation"). "</label></span><span><label><input type='radio' class='status' value='0' name='status".$plan['id']."' checked>". __(" Deactivate","migla-donation"). "</label></span><span><button class='removePlanField'><i class='fa fa-fw fa-trash'></i></button></span></div>
           </div> ";
         }



        echo "<div class='rows'><div class='col-sm-1 '>
                  
                  </div>
               <div class='col-sm-2 col-xs-12'></div>
               <div class='col-sm-1 hidden-xs'><label class='control-label'>". __("Gateways","migla-donation"). "</label></div>               
               <div class='col-sm-4 col-xs-12  '>";

       switch ( $plan['payment_method'] ) {
             case "paypal"       	: 
				echo "<select id='method".$plan['id']."' class='method'> ";               
				echo "<option value='paypal' selected >". __("PayPal Only","migla-donation"). "</option>";
				echo "<option value='stripe'>". __("Stripe Only","migla-donation"). "</option>";
				echo "<option value='authorize'>". __("Authorize Only","migla-donation"). "</option>";
				echo "<option value='paypal-stripe'>". __("PayPal or Stripe","migla-donation"). "</option>";
				echo "<option value='paypal-authorize'>". __("PayPal or Authorize","migla-donation"). "</option>";
				echo "<option value='stripe-authorize'>". __("Stripe or Authorize","migla-donation"). "</option>";
				echo "<option value='paypal-stripe-authorize'>". __("PayPal/Stripe/Authorize","migla-donation"). "</option>";
				echo  "</select>"; 	
                break;				
             case "authorize"       : 
				echo "<select id='method".$plan['id']."' class='method'> ";               
				echo "<option value='paypal'>". __("PayPal Only","migla-donation"). "</option>";
				echo "<option value='stripe'>". __("Stripe Only","migla-donation"). "</option>";
				echo "<option value='authorize' selected >". __("Authorize Only","migla-donation"). "</option>";
				echo "<option value='paypal-stripe'>". __("PayPal or Stripe","migla-donation"). "</option>";
				echo "<option value='paypal-authorize'>". __("PayPal or Authorize","migla-donation"). "</option>";
				echo "<option value='stripe-authorize'>". __("Stripe or Authorize","migla-donation"). "</option>";
				echo "<option value='paypal-stripe-authorize'>". __("PayPal/Stripe/Authorize","migla-donation"). "</option>";
				echo  "</select>"; 	
                break;					 
             case "stripe"		    : 
				echo "<select id='method".$plan['id']."' class='method' disabled> ";               
				echo "<option value='paypal'>". __("PayPal Only","migla-donation"). "</option>";
				echo "<option value='stripe' selected >". __("Stripe Only","migla-donation"). "</option>";
				echo "<option value='authorize'>". __("Authorize Only","migla-donation"). "</option>";
				echo "<option value='paypal-stripe'>". __("PayPal or Stripe","migla-donation"). "</option>";
				echo "<option value='paypal-authorize'>". __("PayPal or Authorize","migla-donation"). "</option>";
				echo "<option value='stripe-authorize'>". __("Stripe or Authorize","migla-donation"). "</option>";
				echo "<option value='paypal-stripe-authorize'>". __("PayPal/Stripe/Authorize","migla-donation"). "</option>";
				echo  "</select>"; 	
                break;				 
             case "paypal-stripe"	: 
				echo "<select id='method".$plan['id']."' class='method' disabled> ";               
				echo "<option value='paypal'>". __("PayPal Only","migla-donation"). "</option>";
				echo "<option value='stripe'>". __("Stripe Only","migla-donation"). "</option>";
				echo "<option value='authorize'>". __("Authorize Only","migla-donation"). "</option>";
				echo "<option value='paypal-stripe' selected >". __("PayPal or Stripe","migla-donation"). "</option>";
				echo "<option value='paypal-authorize'>". __("PayPal or Authorize","migla-donation"). "</option>";
				echo "<option value='stripe-authorize'>". __("Stripe or Authorize","migla-donation"). "</option>";
				echo "<option value='paypal-stripe-authorize'>". __("PayPal/Stripe/Authorize","migla-donation"). "</option>";
				echo  "</select>"; 	
                break;				 
             case "paypal-authorize"	    : 
				echo "<select id='method".$plan['id']."' class='method'> ";               
				echo "<option value='paypal'>". __("PayPal Only","migla-donation"). "</option>";
				echo "<option value='stripe'>". __("Stripe Only","migla-donation"). "</option>";
				echo "<option value='authorize'>". __("Authorize Only","migla-donation"). "</option>";
				echo "<option value='paypal-stripe'>". __("PayPal or Stripe","migla-donation"). "</option>";
				echo "<option value='paypal-authorize' selected >". __("PayPal or Authorize","migla-donation"). "</option>";
				echo "<option value='stripe-authorize'>". __("Stripe or Authorize","migla-donation"). "</option>";
				echo "<option value='paypal-stripe-authorize'>". __("PayPal/Stripe/Authorize","migla-donation"). "</option>";
				echo  "</select>"; 	
                break;				 
             case "stripe-authorize"		: 
				echo "<select id='method".$plan['id']."' class='method'> ";               
				echo "<option value='paypal'>". __("PayPal Only","migla-donation"). "</option>";
				echo "<option value='stripe'>". __("Stripe Only","migla-donation"). "</option>";
				echo "<option value='authorize'>". __("Authorize Only","migla-donation"). "</option>";
				echo "<option value='paypal-stripe'>". __("PayPal or Stripe","migla-donation"). "</option>";
				echo "<option value='paypal-authorize'>". __("PayPal or Authorize","migla-donation"). "</option>";
				echo "<option value='stripe-authorize' selected >". __("Stripe or Authorize","migla-donation"). "</option>";
				echo "<option value='paypal-stripe-authorize'>". __("PayPal/Stripe/Authorize","migla-donation"). "</option>";
				echo  "</select>"; 	
                break;				 
             case "paypal-stripe-authorize"	: 
				echo "<select id='method".$plan['id']."' class='method' disabled> ";               
				echo "<option value='paypal'>". __("PayPal Only","migla-donation"). "</option>";
				echo "<option value='stripe'>". __("Stripe Only","migla-donation"). "</option>";
				echo "<option value='authorize'>". __("Authorize Only","migla-donation"). "</option>";
				echo "<option value='paypal-stripe'>". __("PayPal or Stripe","migla-donation"). "</option>";
				echo "<option value='paypal-authorize'>". __("PayPal or Authorize","migla-donation"). "</option>";
				echo "<option value='stripe-authorize'>". __("Stripe or Authorize","migla-donation"). "</option>";
				echo "<option value='paypal-stripe-authorize' selected >". __("PayPal/Stripe/Authorize","migla-donation"). "</option>";
				echo  "</select>"; 	
                break;				 
       }		

   echo "</div>

               

               <div class='control-radio-sortable col-sm-3 col-xs-12 '>

<button value='save' class='btn btn-info pbutton migla_save_row_plan' id='".$uid_row."'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation"). "</button>


</div>
           
  </div>




</li>";

          $count++; $row++;
       }
    }else{
           echo "<div class='row'>
            <div class='col-sm-12' id='mg_plan_list_info'>".__("You don't have any recurring plans. Recurring donations won't be displayed until you add some above.","migla-donation")." <i class='fa fa-caret-up'></i></div>
         </div>";
    }

     echo "</ul>";

  echo "<div id='mg_recurring_warning1' style='display:none'>".__("This will add a new plan on stripe","migla-donation")."</div>";
  echo "<div id='mg_recurring_warning2' style='display:none'>".__("This will remove the old plan on stripe. Removing a plan will not cancel your existing donor's recurring donations","migla-donation")."</div>";
  echo "<div id='mg_recurring_warning3' style='display:none'>".__("This will delete the old plan and recreate a new one with new info again. This process will not stop current donor's recurring donations","migla-donation")."</div>";
  echo "<div id='mg_recurring_warning4' style='display:none'>".__("You are updating a plan on stripe","migla-donation")."</div>";

  echo  "</div> </section>";



//////////////// Misc Localization settings /////////////////////////////

  echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseEleven' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa fa-bullhorn'></i>".__("Localization Option","migla-donation"). "</h2></header>";
		echo "<div id='collapseEleven' class='panel-body collapse in'>";

echo "<div class='row'><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>". __("The None Option label:","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<input type='text' value='".get_option('migla_none_rec_radiobtn_text')."' id='mg_none_rec_radiobtn_text' placeholder='e.g. none, no etc' />";
echo "</div><div class='col-sm-3'><button type='button' class='btn btn-default pbutton' id='mg_none_rec_radiobtn'><i class='fa fa-save'></i>". __(" Save","migla-donation"). "</button></div>";
echo "<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>". __("When using radio buttons, an option for none will appear. Change the language here","migla-donation"). "</span>";

echo "</div>";
echo "</section>";

    echo "</div></div>";
    echo "</div>";

/*********** mail intergration *******************************/

//print_r(stream_get_wrappers());
	
   echo "<div id='section5' class='tab-pane'>";
   echo "<div class='row'>";
   echo "<div class='col-sm-12'>";


echo "<section class='panel'>
      <header class='panel-heading'>
         <div class='panel-actions'><a aria-expanded='true' href='#collapseZero' data-parent='.panel' data-toggle='collapse' class='fa fa-caret-down'></a></div>
         <h2 class='panel-title'><i class='fa fa-fw fa-envelope'></i></i>". __("Mailing List Chooser","migla-donation"). "</h2>
      </header>
      <div class='panel-body collapse in' id='collapseZero'>";
echo "<div class='row'><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>". __("Choice Mailing List Vendor:","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
$maillist_choice = get_option('migla_mail_list_choice');
if( $maillist_choice == 'constant_contact' )
{
  echo "<select id='migla_mail_list_choice'>";
  echo "<option value=''>". __("None","migla-donation"). "</option><option value='constant_contact' selected>". __("Constant Contact","migla-donation"). "</option><option value='mail_chimp'>". __("Mail Chimp","migla-donation"). "</option>";
  echo "</select>";
}else if( $maillist_choice == 'mail_chimp' ){
  echo "<select id='migla_mail_list_choice'>";
  echo "<option value=''>". __("None","migla-donation"). "</option><option value='constant_contact'>". __("Constant Contact","migla-donation"). "</option><option value='mail_chimp' selected>". __("Mail Chimp","migla-donation"). "</option>";
  echo "</select>";
}else{
  echo "<select id='migla_mail_list_choice'>";
  echo "<option value='' selected>". __("None","migla-donation"). "</option><option value='constant_contact'>". __("Constant Contact","migla-donation"). "</option><option value='mail_chimp'>". __("Mail Chimp","migla-donation"). "</option>";
  echo "</select>";
}
echo "</div><div class='col-sm-3'><button type='button' class='btn btn-default pbutton' id='migla_mail_list_choice_save'><i class='fa fa-save'></i>". __(" Save","migla-donation"). "</button></div></div>";
echo "</div>";
echo "</section>";


echo "<section class='panel'>
      <header class='panel-heading'>
         <div class='panel-actions'><a aria-expanded='true' href='#collapse12' data-parent='.panel' data-toggle='collapse' class='fa fa-caret-down'></a></div>
         <h2 class='panel-title'><i class='fa fa-fw fa-envelope'></i></i>". __("Constant Contact Integration","migla-donation"). "</h2>
      </header>
      <div class='panel-body collapse in' id='collapse12'>";

    echo "<div class='row'><div class='col-sm-3'></div><div class='col-sm-6'><span class='help-control col-sm-12 text-right-sm text-center-xs'>". __("Constant Contact Classes implements the technical interoperability standards for PHP 5.3 namespaces and class names.","migla-donation"). "</span></div></div>";

echo "<div class='row'><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>". __("API Key:","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<input type='text' value='".get_option('migla_constantcontact_apikey')."' id='migla_constantcontact_apikey' />";
echo "</div><div class='col-sm-3'><button type='button' class='btn btn-default pbutton' id='migla_constantcontact_apikey_save'><i class='fa fa-save'></i>". __(" Save","migla-donation"). "</button></div></div>";

echo "<div class='row'><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>". __("Token:","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<input type='text' value='".get_option('migla_constantcontact_token')."' id='migla_constantcontact_token' />";
echo "</div><div class='col-sm-3'><button type='button' class='btn btn-default pbutton' id='migla_constantcontact_token_save'><i class='fa fa-save'></i>". __(" Save","migla-donation"). "</button></div></div>";

   $list1 = (array)get_option('migla_constantcontact_list1');
   $list2 = (array)get_option('migla_constantcontact_list2');
   $count_mail_list = 0;
   
   echo "<div class='row'>";
   echo "<div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>". __("Constant Contact Lists:","migla-donation"). "</label></div>";
   echo "<div class='col-sm-6'><ul id='mg_constantcontact_list'>";

   if( count($list1) > 0 && $list1[0] != '' ){
     foreach( (array)$list1 as $l1 )
     {
       echo "<li>".$l1['name']." : Add every donor automatically to this campaign </li>";
	   $count_mail_list++;
     }
   }
   
   if( count($list2) > 0 && $list2[0] != '' ){
      foreach( (array)$list2 as $l1 )
      {
        echo "<li>".$l1['name']."  : Add donor if they have checked the checkbox </li>";
		$count_mail_list++;
      }
   }

	if( $count_mail_list == 0 ){
		echo "You don't have any automatic sign ups set up yet. Add some by clicking the button below <i class='fa fa-caret-down'></i>";
	}
	
  echo "</ul>";
  echo "</div>";
   echo "</div>";

   echo "<div class='row cc_available_lists_row' style='display:none'>";
   echo "<div class='col-sm-12' id='cc_available_lists'>";
   echo "</div></div>";

echo "<div class='row'>";
   echo "<div class='col-sm-3'></div><div class='col-sm-3'>";
   echo "<button type='button' class='btn btn-default mbutton' id='mg_constant_retrieve_list'><i class='fa fa-download'></i>". __(" Retrieve available lists","migla-donation"). "</button>";
   echo "</div><div class='col-sm-6'>";
   echo "<button type='button' class='btn btn-default pbutton' id='mg_constant_update_list' style='display:none'><i class='fa fa-save'></i>". __("  Update the lists","migla-donation"). "</button>";
   echo "</div>";
   echo "</div>";

echo "<div class='row'>";
   echo "<div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>".__("Email for Test:", "migla-donation")."</label></div><div class='col-sm-6'>";
   echo "<input type='text' id='mg_constantcontact_test_email'></div>";
   echo "<div class='col-sm-3'>";
   echo "<button type='button' class='btn btn-info obutton' id='mg_constantcontact_test'><i class='fa fa-search'></i>".__(" Run Test","migla-donation")."</button>";
   echo "</div>";
   echo "</div>";   

   echo "</div>";

echo "</section>";



  echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseTwelve' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa fa-bullhorn'></i>". __("Mailchimp Settings","migla-donation"). "</h2></header>";
		echo "<div id='collapseTwelve' class='panel-body collapse in'>";

echo "<div class='row'><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>". __("API Key:","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<input type='text' value='".get_option('migla_mailchimp_apikey')."' id='migla_mailchimp_apikey' /></div><div class='col-sm-3'><button type='button' class='btn btn-default pbutton' id='mg_mailchimp_save'><i class='fa fa-save'></i> ". __("Save","migla-donation"). "</button></div></div>";

 $mailchimp_list = (array)get_option('migla_mailchimp_list');
 $count_mc_list = count($mailchimp_list);
 $count_mail_list_mc = 0;

echo "<div class='row'><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>". __("Mail Chimp Lists:","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";

echo "<ul id='migla_mailchimp_list'>";
foreach( $mailchimp_list as $list )
{
    if( $mailchimp_list[0] != '' )
    {
       echo '<li>'. $list['name'] ; 
       if( $list['status'] == '2' ){
           echo '  -- Add every donor automatically to this campaign </li>';
		   $count_mail_list_mc++;
       }
       if( $list['status'] == '3' ){
           echo '  -- Add donor if they have checked the checkbox </li>';
		   $count_mail_list_mc++;
       }
    } 
}

 if( $count_mail_list_mc == 0 ){
     echo "".__("You don't have any automatic sign ups set up yet. Add some by clicking the button below","migla-donation")." <i class='fa fa-caret-down'></i>";
 }

echo "</ul></div><div class='col-sm-3'></div></div>";

   echo "<div class='row mg_mailchimp_lists_row' style='display:none'>";
     echo "<div class='col-sm-12'><ul id='mg_mailchimp_lists'>";
     echo "</ul>";
     echo "</div>";
   echo "</div>";

echo "<div class='row'>";
   echo "<div class='col-sm-3'></div><div class='col-sm-3'>";
   echo "<button type='button' class='btn btn-default mbutton' id='mg_mailchimp_retrieve_list'><i class='fa fa-download'></i>".__(" Retrieve available lists","migla-donation")."</button>";
   echo "</div>";
   echo "<div class='col-sm-3'>";
   echo "<button type='button' class='btn btn-default pbutton' id='mg_mailchimp_update_list' style='display:none' ><i class='fa fa-save'></i>".__(" Update the lists","migla-donation")."</button>";
   echo "</div>";
   echo "</div>";

echo "<div class='row'>";
   echo "<div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>".__("Email for test:", "migla-donation")."</label></div><div class='col-sm-6'>";
   echo "<input type='text' id='mg_mailchimp_test_email'></div>";
   echo "<div class='col-sm-3'>";
   echo "<button type='button' class='btn btn-info obutton' id='mg_mailchimp_test'><i class='fa fa-search'></i>".__(" Run Test","migla-donation")."</button>";
   echo "</div>";
   echo "</div>";

   
echo "</div>";
echo "</section>";




   echo "</div></div></div>";

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
     <hr><div class='help-control-center'>". __("You can drag the list item to reorganize. Here are the available list values:","migla-donation"). "</div><br>";

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

}


?>