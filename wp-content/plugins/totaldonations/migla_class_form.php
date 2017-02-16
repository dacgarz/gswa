<?php

class migla_form_creator
{
   public $FORM_ID;
   private $CONTENT;
   private $FORM_TYPE;      
   private $FORM_FIELDS;
   private $TYPE_AMOUNT_BTN ;
   private $AMOUNTS;
   private $CUSTOM_AMOUNT_TEXT;
   private $HIDE_CUSTOM_AMOUNT;
   private $WARNING_MESSAGE1;
   private $WARNING_MESSAGE2;
   private $WARNING_MESSAGE3;
   private $CAMPAIGN;
   private $THE_POST;
   private $USE_RECAPTCHA;
   private static $IS_AFORM_EXIST;
   private static $AFORM_EXIST;
	
   public function __construct( $fid )
   {
      $this->CONTENT 		= '';
      $this->FORM_ID 		= $fid;
	  $this->USE_RECAPTCHA 	= get_option('migla_use_captcha') == 'yes';

   }
   
   public function get_form()
   {
	  $out = "";

		  $bgclor2nd        = explode("," , get_option('migla_2ndbgcolor') );
		  $border           = explode(",", get_option('migla_2ndbgcolorb') );     
		  $borderCSS        = "border: ".$border[2]."px solid ".$border[0].";";
		  $bglevelcolor     = get_option('migla_bglevelcolor');   

		  $out .= "<input type='hidden' id='migla_paypal_pro_type' value='".get_option('migla_paypal_pro_type')."'>";
		  $out .= "<input type='hidden' id='migla_pro_rec' value='".get_option('migla_paypalpro_recurring')."'>";
		  $out .= "<input type='hidden' id='migla_paypal_fec' value='".get_option('migla_paypal_fec')."'>";
		  $out .= "<input type='hidden' id='migla_form_id' value='".$this->FORM_ID."'>";
		  $out .= "<input type='hidden' id='migla_stripe_js' value='".get_option('migla_stripe_js')."'>";
		  $out .= "<input type='hidden' id='migla_credit_card_avs' value='".get_option('migla_credit_card_avs')."'>";
		  $out .= "<input type='hidden' id='migla_use_recaptcha' value='". get_option('migla_use_captcha') ."'>";
		  $out .= "<input type='hidden' id='migla_thankyou_url' value='". get_option( 'migla_thankyou_url') ."'>";  	  
		  
		$out .= $this->migla_form( '', $bgclor2nd, $border, $borderCSS, $bglevelcolor );  
		
		if( $this->USE_RECAPTCHA )
		{
			$out .= "<input type='hidden' id='migla_token_data' value=''>";
			$out .= $this->display_captcha();	
		}
		$out .= $this->migla_tabs( '', $bgclor2nd[0], $borderCSS, $bglevelcolor );
	  
	  return $out;   
   }   

   
   private function display_captcha()
   {
		$ajax_url = plugins_url( 'totaldonations/the-ajax-caller.php' , dirname(__FILE__));
        if( get_option('migla_ajax_caller') == 'wp' )
			$ajax_url =  admin_url( 'admin-ajax.php' );   
   
		$out = "<form id='migla_captcha_form' method='post' action='".get_permalink()."'>";
		$out .= "<div id='g-recaptcha' style='margin-bottom:30px' class='g-recaptcha' data-callback='recaptchaCallback' ";
		$out .= " data-expired-callback='mg_expired_recaptchaCallback' data-sitekey='".get_option('migla_captcha_site_key')."'></div>";	
		$out .= "</form>";     

	return $out;
 
   }

   public function draw_form()
   {
      if( $this->FORM_ID == '' )
      {
			$this->FORM_TYPE = 'multi_campaign';
		    $this->FORM_FIELDS = get_option( 'migla_form_fields' );
			$this->TYPE_AMOUNT_BTN = get_option('migla_amount_btn' );
	        $this->HIDE_CUSTOM_AMOUNT = get_option('migla_hideCustomAmount' );
			$this->AMOUNTS = get_option( 'migla_amounts' );
			$this->CUSTOM_AMOUNT_TEXT = get_option('migla_custamounttext');
			$this->TYPE_AMOUNT_BOX = get_option('migla_amount_box_type' );
			
			$this->WARNING_MESSAGE1 = get_option('migla_warning_1');
			$this->WARNING_MESSAGE2 = get_option('migla_warning_2');
			$this->WARNING_MESSAGE3 = get_option('migla_warning_3');			

			$this->CONTENT  = $this->get_form();
		    $this->CONTENT .= $this->migla_modal_box();

			if( get_option('migla_form_url') == false || get_option('migla_form_url') == ''  )
				add_option( 'migla_form_url', get_permalink() );
			else
				update_option( 'migla_form_url', get_permalink() );

                        update_option( 'migla_form_url', get_permalink() );
			
			//$this->CONTENT .= get_permalink();	
				
      }else{
	  		$this->FORM_TYPE = 'single_campaign';
			$the_post = get_post( $this->FORM_ID, ARRAY_A );
			
			if( $the_post !== false ){
				if( $the_post['post_status'] == 'publish' ){
				
					$this->FORM_FIELDS = get_post_meta ( $this->FORM_ID, 'migla_form_fields' );
					$this->FORM_FIELDS =  $this->FORM_FIELDS[0];
					
					$this->TYPE_AMOUNT_BTN = get_post_meta ( $this->FORM_ID, 'migla_amount_btn' );
					if( $this->TYPE_AMOUNT_BTN == false )
					{
					   $this->TYPE_AMOUNT_BTN = 'radio';    
					}else{
					  $this->TYPE_AMOUNT_BTN = $this->TYPE_AMOUNT_BTN[0];
					}
					
					$this->WARNING_MESSAGE1 = get_post_meta ( $this->FORM_ID, 'migla_warning_1' );
					$this->WARNING_MESSAGE2 = get_post_meta ( $this->FORM_ID, 'migla_warning_2' );
					$this->WARNING_MESSAGE3 = get_post_meta ( $this->FORM_ID, 'migla_warning_3' );	
					$this->WARNING_MESSAGE1 = $this->WARNING_MESSAGE1[0];
					$this->WARNING_MESSAGE2 = $this->WARNING_MESSAGE2[0];
					$this->WARNING_MESSAGE3 = $this->WARNING_MESSAGE3[0];						
					
					$current_pos 	= get_post( $this->FORM_ID );
					$this->CAMPAIGN =  $current_pos->post_title;
					
					$this->HIDE_CUSTOM_AMOUNT = get_post_meta ( $this->FORM_ID, 'migla_hideCustomAmount' );
					if( $this->HIDE_CUSTOM_AMOUNT == false )
					{
						$this->HIDE_CUSTOM_AMOUNT = 'no';    
					}else{
						$this->HIDE_CUSTOM_AMOUNT = $this->HIDE_CUSTOM_AMOUNT[0];
					}
						
					$this->CUSTOM_AMOUNT_TEXT = get_post_meta ( $this->FORM_ID, 'migla_custom_amount_text' );
					$this->CUSTOM_AMOUNT_TEXT = $this->CUSTOM_AMOUNT_TEXT[0];
					
					$this->TYPE_AMOUNT_BOX = get_post_meta ( $this->FORM_ID, 'migla_amount_box_type' );
					$this->TYPE_AMOUNT_BOX = $this->TYPE_AMOUNT_BOX[0];
					
					$this->AMOUNTS =  get_post_meta ( $this->FORM_ID, 'migla_amounts' );
					$this->AMOUNTS = $this->AMOUNTS[0];
					
					$this->CONTENT  = $this->get_form();
					$this->CONTENT .= $this->migla_modal_box();
				
				}else{
					 $this->CONTENT  = 'Total Donation form does not exist or has been deleted';
				}
			}
           update_post_meta( $this->FORM_ID, 'migla_form_url', get_permalink() );
      }

		
	  
		return $this->CONTENT;
   }
   
  public function migla_modal_box(){
    $out = "";
    $out .= "<div style='display:none'><div id='mg_warning1'>". $this->WARNING_MESSAGE1 ."</div>";
    $out .= "<div id='mg_warning2'>". $this->WARNING_MESSAGE2 ."</div>";
    $out .= "<div id='mg_warning3'>". $this->WARNING_MESSAGE3 ."</div>";
    $out .= "</div>";

    $out .= "";

    return $out;
  
  }   
   
   public function migla_form( $campaign_attr, $bgclor2nd, $border, $borderCSS, $bglevelcolor)
   {
		$this->RECAPTCHA_POST = '';

	  $out = "";
	  $out .= "<input type='hidden' id='miglaShowDecimal' value='".get_option('migla_showDecimalSep')."'>";
	  $out .= "<input type='hidden' id='miglaDecimalSep' value='".get_option('migla_decimalSep')."'>";
	  $out .= "<input type='hidden' id='miglaThousandSep' value='".get_option('migla_thousandSep')."'>";

	  if( $this->FORM_TYPE == 'single_campaign' )
	  {	  
			$post_id_custom_list = $this->FORM_ID;
      }else{	  
			$post_id_custom_list = $this->migla_get_select_values_postid();
      }
	  $dataField = $this->FORM_FIELDS;
	  
	  foreach ( (array) $dataField as $f )
	  {
		   $has = false;
		   $index = 0;
		   if( isset($f['child']) )
		   {
			   foreach ( (array)$f['child'] as $c ){
				   if( strcmp( $c['status'], '0' ) == 0 ){ 
				   }else{ 
					  $has=true;break;
				   }
				   $index++;
			   }
		   }

		 if( $has )
		 { //if it has children

		   $lbl = str_replace("[q]","'", $f['title'] );

		   $classtitle = str_replace(" ","", $f['title'] );
		   $classtitle = str_replace("?","", $classtitle );
		   $classtitle = "mg_".$classtitle ;

		   if( strcmp( $f['toggle'], '1') == 0 ) //Check the toggle
		   {     
			  //section
			  $out .= "<section class='migla-panel' style='background-color:".$bgclor2nd[0].";".$borderCSS."' >";
			  $out .= "<header class='migla-panel-heading'>";

			  $out .= "<h2 class='".$classtitle."'> ".$lbl ." <input type='checkbox' class='mtoggle'/></h2>"; 
			  $out .= "</header>";
			  $out .= "<div class='migla-panel-body form-horizontal' style='display:none'>";
		   }else{
			  //section
			  $out .= "<section class='migla-panel' style='background-color:".$bgclor2nd[0].";".$borderCSS."' >";
			  $out .= "<header class='migla-panel-heading'><h2 class='".$classtitle."'>".$lbl."</h2></header>";
			  $out .= "<div class='migla-panel-body form-horizontal' >";
		   }

		 foreach ( (array) $f['child'] as $c ){

		  if( strcmp( $c['status'], '0' ) == 0){ //as long as they are not shown

			 if ( strcmp( $c['id'], 'campaign' ) == 0 ){ //setting with hide campaign dropdown
				 $out .= $this->migla_onecampaign_section( get_option('migla_selectedCampaign'),  $c['label'] , 0 );
			 }	  

		  }else{
		  		  
			$req = '';
			if( strcmp( $c['status'], '2' ) == 0 || strcmp( $c['status'], '3' ) == 0) { 
				$req = 'required'; 
			} //is it mandatory ?

			 if( strcmp( $c['id'], 'amount' ) == 0 )
			 {
				   $out .= $this->migla_amounts_single_campaign( $c['label'], $this->FORM_ID , $this->CUSTOM_AMOUNT_TEXT );
			 }else if ( strcmp( $c['id'], 'campaign' ) == 0 )
			 {
				if( $this->FORM_TYPE == 'single_campaign' )
				{
				
				}else{
				   	$postcampaign = "";
					if( isset($_POST['campaign']) && $_POST['campaign'] != '' )
					{ 
						$postcampaign = $_POST['campaign']; 
					}
					if( $campaign_attr == '' ){
						$out .= $this->migla_campaign_section( $postcampaign, $c['label'] );
					}else{
						$out .= $this->migla_onecampaign_section( $campaign_attr  ,  $c['label'] , 0 );
					}
				}
			 }else if( strcmp( $c['id'], 'country' ) == 0  ){    
	   
				 $out .= $this->mg_makeInputCountry( $c['id'], $c['label'], $c['type'], $c['code'], '', $req );

			 }else if(  strcmp( $c['id'], 'honoreecountry' ) == 0 ){

				 $out .= $this->mg_makeInputCountry( $c['id'], $c['label'], $c['type'], $c['code'], '', $req );

			 }else if(  strcmp( $c['id'], 'repeating' ) == 0 ){

				$gateways_order   = (array) get_option('migla_gateways_order');			
				$out .= $this->mg_makeRepeatingTag(  $c['id'], $c['label'], $c['type'], $c['code'], $gateways_order, $req );
					 
			 }else if(strcmp( $c['id'], 'anonymous' ) == 0){

				  $out .= $this->mg_anonymous_check( $c['id'], $c['label'], $c['type'], $c['code'], "", $req , $c['uid'] );  

			 }else{ //not special field

				if( $c['type'] == 'text' ){

					$out .= $this->mg_makeInputTextTag( $c['id'], $c['label'], $c['type'], $c['code'], "", $req, "" , $c['uid'] );

				}else if ( $c['type'] == 'checkbox' ){

					$out .= $this->mg_makeInputCheckTag( $c['id'], $c['label'], $c['type'], $c['code'], "", $req , $c['uid'] );  

				}else if ( $c['type'] == 'textarea' ){

					$out .= $this->mg_makeInputTextareaTag( $c['id'], $c['label'], $c['type'], $c['code'], "", $req , $c['uid']);  

				}else if( $c['type'] == 'select' ){

					$out .=  $this->mg_makeInputDropDownTag( $c['id'], $c['label'], $c['type'], $c['code'], "", $req, $post_id_custom_list, $c['uid'] );

				}else if( $c['type'] == 'radio' ){

					$out .=  $this->mg_makeInputRadioTag( $c['id'], $c['label'], $c['type'], $c['code'], "", $req, $post_id_custom_list, $c['uid'] );

				}else if( $c['type'] == 'multiplecheckbox' ){

					$out .=  $this->mg_makeInputMultiCheckboxTag( $c['id'], $c['label'], $c['type'], $c['code'], "", $req, $post_id_custom_list, $c['uid'] );
				}

			}//if check who is special

		   }//as long as they are shown (Toggle)

		  }//foreach child

		  //end section
		  $out .= "</div></section>";

		}//if it has child 

	   }//foreach Section Field
	   
		if( $this->FORM_TYPE == 'single_campaign' )
		{
			$out .= "<section class='migla-panel' style='background-color:".$bgclor2nd[0].";".$borderCSS."display:none;' >";
			$out .= "<header class='migla-panel-heading'><h2 class='".$classtitle."'>".$lbl."</h2></header>";
			$out .= "<div class='migla-panel-body form-horizontal' >";	
			$out .= $this->migla_onecampaign_section( $this->CAMPAIGN  ,  $c['label'] , 0 );
			$out .= "</div></section>";
		} 
	
	  return $out;
	}

	
/************* TABS **************************************************************/
function migla_load_tab_content( $id , $bgcolor, $borderCSS ){
   $out = "";

   if( $id == 'stripe' )
   {
      $out .= $this->migla_stripe();
   }else if( $id == 'paypal' )
   {
      $out .= $this->migla_paypal( true, $bgcolor, $borderCSS );
   }else if( $id == 'offline' )
   {
      $out .= $this->migla_offline();
   }else if( $id == 'authorize' )
   {
      $out .= $this->migla_authorize();
   }

   return $out;
}

function migla_tabs( $gateways, $bgcolor, $borderCSS, $bglevelcolor){

    $out = ""; $add_class = "";
    $tabs_name   = array();
    $cc_paypal   = (array)get_option('migla_paypalpro_cc_info');
    $cc_stripe   = (array)get_option('migla_stripe_cc_info');   
    $cc_authorize   = (array)get_option('migla_authorize_cc_info'); 
    $tabs_name['paypal']     = $cc_paypal[0][1];
    $tabs_name['stripe']     = $cc_stripe[0][1];
    $tabs_name['authorize']  = $cc_authorize[0][1];
    $tabs_name['offline']    = get_option('migla_offline_tab');
	$gateways                = (array) get_option('migla_gateways_order');
	$count_the_tabs   = 0; 
	$one_gateway_only = "";
	$inactive_color   = get_option('migla_tabcolor');
	
	foreach( (array)$gateways as $value  ){
	   if(  $value[1] == 'true' || $value[1] == 1  ){
	      $count_the_tabs++;
	      $one_gateway_only = $value[0];	 
	   } 
	}
	
	if( $gateways == false || $gateways[0] == '' )
	{
	}else{
	
	    if( $count_the_tabs > 1 )
		{
			$out .= "<div class='form-horizontal migla-payment-options' >";
			$out .= "<ul class='mg_nav mg_nav-tabs'>";
		
		    $gateways_j = 1;
			foreach( (array)$gateways as $value  )
			{
			   if( $value[1] == 'true' || $value[1] == 1 )
			   {
			        if( $gateways_j == 1 ){
						$out .= "<li class='mg_active'><a id='_section".$value[0]."' style='background-color:".$bgcolor.";".$borderCSS.";' >". $tabs_name[$value[0]] ."</a></li>";					
					}else{
						$out .= "<li ><a id='_section".$value[0]."' style='background-color:".$inactive_color.";".$borderCSS.";' >". $tabs_name[$value[0]] ."</a></li>";										
					}
                    $gateways_j++;
			   }				
			}

			$out .= "</ul>";
			
			$out .= "<div class='mg_tab-content' style='".$borderCSS."' >";
			
			$gateways_i = 1;
			foreach( (array)$gateways as $value )
			{
				if( $value[1] == 'true' || $value[1] == 1 )
				{
				   if( $gateways_i == 1 ){
					   $out .= "<div id='section".$value[0]."' class='mg_tab-pane mg_active' style='background-color:".$bgcolor."' >";
				   }else{
					   $out .= "<div id='section".$value[0]."' class='mg_tab-pane' style='background-color:".$bgcolor."' >";		   
				   }
				   $out .= $this->migla_load_tab_content( $value[0],  $bgcolor, $borderCSS  );
				   $out .= "</div>";
				   $gateways_i++;
				}
			}

			$out .= "</div>"; //content
			$out .= "</div>"; //FORM
			
		}else{ //One Tab

            $out .= "<div class='form-horizontal migla-payment-options' >
                     <div class='mg_tab-content' >
                     <div id='section".$one_gateway_only."' class='' style='background-color:".$bgcolor.";".$borderCSS."'>";
					 				   
			$out .= $this->migla_load_tab_content( $one_gateway_only , $bgcolor, $borderCSS  );
			$out .=  "</div> </div> </div>";	
		}
  }
	
  return $out;
}

/*** Functions to decode the input ***/
function mg_makeInputTextTag( $id, $label, $type, $code, $filter, $req, $col, $uid )
{
   $out = ""; $info = "";
   if( strcmp($req, 'required')==0 ){ 
         $info = " <abbr class='mg_asterisk' title='required'> *</abbr>"; 
  }

   $lbl = str_replace("[q]","'",$label);

   $out .= "<div class='form-group'><div class='col-sm-3'>";
   $out .= "<div class='input-group input-group-icon'>";
   $out .= "<label class='mg_control-label'>".$lbl;

   $out .= $info;
   $out .= "</label>"; 

   $out .= "</div></div>";

   $out .= "<div class='col-sm-6 col-xs-12'>";
   $out .= "<label style='display:none' class='idfield' id='".$code.$id."'></label>";
   $out .= "<input type='".$type."' id='".$uid."' placeholder='".$label."' class='mg_form-control miglaNumAZ ".$code." ".$req."'/>";
   $out .= "</div>";
   $out .= "<div class='col-sm-3 hidden-xs'></div></div>";

   return $out;
}

function mg_makeInputTextareaTag( $id, $label, $type, $code, $filter, $req, $uid )
{
   $out = ""; $info = "";
   if( strcmp($req, 'required')==0 ){ 
         $info = "<abbr class='mg_asterisk' title='required'> *</abbr>"; 
  }

   $lbl = str_replace("[q]","'",$label);


   $out .= "<div class='form-group'><div class='col-sm-3'>";
   $out .= "<div class='input-group input-group-icon'>";
   $out .= "<label class='mg_control-label  '>".$lbl;

   $out .= $info;
   $out .= "</label>"; 

   $out .= "</div></div>";

   $out .= "<div class='col-sm-6 col-xs-12'>";
   $out .= "<label style='display:none' class='idfield' id='".$code.$id."'></label>";
  
   $out .= "<textarea type='".$type."' id='".$uid."' class='mg_form-control ".$code." ".$req."  miglaNumAZ'></textarea>";

   $out .= "</div>";
   $out .= "<div class='col-sm-3 hidden-xs'></div></div>";

   return $out;
}


function mg_anonymous_check( $id, $label, $type, $code, $filter, $req , $uid)
{
   $out = ""; $info = "";
   $lbl = str_replace("[q]","'",$label);

   $out .= "<div class='form-group'><div class='col-sm-3'>";
   $out .= "<div class='input-group input-group-icon'>";
   $out .= "<label class='mg_control-label'>".$lbl;

   $out .= $info;
   $out .= "</label>"; 
   $out .= "</div></div>";
   
   $out .= "<div class='col-sm-6 col-xs-12'>";
   $out .= "<label style='display:none' class='idfield' id='".$code.$id."'></label>";
   if( strcmp($req, 'required')==0 ){ 
   $out .= "<div class='checkbox'><label for='".$uid."'><input checked name='".$code.$id."' type='checkbox' id='".$id."' class='check-control ".$code." ".$req."' value='".
			   __("yes", "migla-donation")."'/></label></div>";
   }else{
   $out .= "<div class='checkbox'><label for='".$uid."'><input name='".$code.$id."' type='checkbox' id='".$id."' class='check-control ".$code." ".$req."' value='".
			   __("yes", "migla-donation")."'/></label></div>";	
   }		   
   $out .= "</div>";
   $out .= "<div class='col-sm-3 hidden-xs'></div></div>";

   return $out;
}

function mg_makeInputCheckTag( $id, $label, $type, $code, $filter, $req , $uid)
{
   $out = ""; $info = "";
   $lbl = str_replace("[q]","'",$label);
   
    if( strcmp($req, 'required')==0 ){ 
        $info = " <abbr class='mg_asterisk' title='required'> *</abbr>"; 
    }   

   $out .= "<div class='form-group'><div class='col-sm-3'>";
   $out .= "<div class='input-group input-group-icon'>";
   $out .= "<label class='mg_control-label'>".$lbl;
 
   $out .= $info;
   $out .= "</label>"; 

   $out .= "</div></div>";
   $out .= "<div class='col-sm-6 col-xs-12'>";
   $out .= "<label style='display:none' class='idfield' id='".$code.$id."'></label>";

   $out .= "<div class='checkbox'><label for='".$uid."'><input type='checkbox' id='".$id."' class='check-control ".$code." ".$req."' value='".
           __("yes", "migla-donation")."'/></label></div>";   
   
   $out .= "</div>";
   $out .= "<div class='col-sm-3 hidden-xs'></div></div>";

   return $out;
}

function mg_makeInputDropDownTag( $id, $label, $type, $code, $filter, $req, $post_id, $meta_key )
{
   $out 	= ""; 
   $info 	= ""; 
   $first_text = "<option value=''>".__("Please choose one", "migla-donation")."</option>"; 
   $options = array();
   if( strcmp($req, 'required')==0 )
   { 
		$info       = "<abbr class='mg_asterisk' title='required'> *</abbr>"; 
		$first_text = '';
   }

   $lbl = str_replace("[q]","'",$label);

   $out .= "<div class='form-group'><div class='col-sm-3'>";
   $out .= "<div class='input-group input-group-icon'>";
   $out .= "<label class='mg_control-label  '>".$lbl;

   $out .= $info;
   $out .= "</label>"; 

   $out .= "</div></div>";

   $out .= "<div class='col-sm-6 col-xs-12'>";
   $out .= "<label style='display:none' class='idfield' id='".$code.$id."'></label>";
              
		$data = get_post_meta( $post_id, 'mgval_'.$meta_key );			  
        if( !empty($data) )
		{
           $options = explode( ";" , (string)$data[0]  );
        }

          $out .= "<select class='mg_form-control' id='".$meta_key."'  name='".$code.$id."'>"; 
          $out .=  $first_text;
			  
          if( !empty($options) ){			  
			  foreach ( (array)$options as $op ) 
			  { 
					 $op_value = explode(  "::" , $op );
					 if( !empty($op_value[1]) && !empty($op_value[0]) ){
					 $out .= "<option value='".$op_value[0]."'>".$op_value[1]."</option>"; 
					 }
			  } 	   
		  }
              $out .= "</select>";  

   $out .= "</div>";
   $out .= "<div class='col-sm-3 hidden-xs'></div></div>";

   return $out;
}

function mg_makeRepeatingTag( $id, $label, $type, $code, $filter, $req)
{
   $out = ""; $info = "";
   $count = 0; $data = '';
   $meta_key = 'mg_repeating_radio';
   $gateways = array(); $gateways_i = 0;
   
	   foreach( (array)$filter as $value  ){
		 if( $value[0] == 'offline' ){
		 }else{
			 if( ( $value[1] == 'true' || $value[1] == 1)  ){
				$gateways[$gateways_i] = $value[0];
						$gateways_i++;
			 }
		 }
	   }
	
   if( $gateways_i == 0 ){
   }else{	
	   $show_recurring = array();		 		   
	   $data = get_option( 'migla_recurring_plans' ) ; 
       if( $data != false && $data != '' )
	   {   
		   foreach( (array)$data as $d )
		   { 
			  $is_eligible = true;
			  foreach( (array)$gateways as $value  ){
					if ( preg_match("~\b".$value."\b~", (string)$d['payment_method']) && ($d['status']=='1') )
					{	  
						 $is_eligible = $is_eligible && true;
					}else{
						 $is_eligible = $is_eligible && false;	
					}          		
			  }
			  if( $is_eligible ){
			        $count++;
					$show_recurring[$count-1] = $d['name'];
					//$out .= $show_recurring[$count]."<br>";			   
			  }
		   }
	  }
	  
	  if( $count <= 0 ){

	  }else{

		$lbl = str_replace("[q]","'",$label);

		$out .= "<div class='form-group'>";
		$out .= "<div class='col-sm-3'>";
		$out .= "<label class='mg_control-label  '>".$lbl;
		$out .= $info;
		$out .= "</label>"; 
	        
		$out .= "</div>";

		$out .= "<label style='display:none' class='idfield' id='".$code.$id."'></label>";

		$bglevelcolor = get_option('migla_bglevelcolor');
		$borderlevelcolor = get_option('migla_borderlevelcolor');
		$borderlevel = get_option('migla_borderlevel');
		$borderCSS = "border: ".$borderlevel."px solid ".$borderlevelcolor.";"; 

		if ( $count == 1 ){

				 $jj = 0;
				 foreach( (array)$data as $d )
				 { 
					 if(  in_array( $d['name'], $show_recurring ) )
					 {
					   $plan_name = $data[$jj]['name'];
					   $plan_name = str_replace( "'", "", $plan_name );
					   $out .= "<div class='col-sm-6 col-xs-12'>";
					   $out .= "<div class='checkbox'><label for='mg_repeating_check'>";
					   $out .= "<input type='checkbox' id='mg_repeating_check' class='check-control ".$code." ".$req." ".$code.$id."' name='".$code.$id."' />";
					   $out .= "<input type='hidden' value='".$data[$jj]['id'].";".$data[$jj]['interval_count'].";".$data[$jj]['interval'].";".$plan_name."' id='info".$code.$id."'>";
					   $out .= "".$data[$jj]['name']."</label></div>";
					   $out .= "</div>";
					}
					$jj++;
				 }

		   }else{
		           $none_text = get_option('migla_none_rec_radiobtn_text');
				   if( $none_text == false || $none_text == '' )
					{
						$none_text = __( 'None' , "migla-donation");
					} 
					
				   $out .= "<div class='col-sm-6 col-xs-12'>";
				   $out .= "<div class='radio' id='".$meta_key.$count."' >"; 
				   $out .= "<label for='radios-repeating".$count."'><input checked type='radio' value='no' id='radios-repeating".$count."' name='".$code.$id."' >";
				   $out .= __( $none_text , "migla-donation");
				   $out .= "<input type='hidden' value='No;0;0' id='inforadios-repeating".$count."'></label></div>"; 

				foreach( (array)$data as $d )
				{ 
					   if(  in_array( $d['name'], $show_recurring ) )
					   {
						   $count++;
						   $plan_name = $d['name'];
						   $plan_name = str_replace( "'", "", $plan_name );

						   $out .= "<div class='radio' id='".$meta_key.$count."'>"; 
						   $out .= "<label for='radios-repeating".$count."'><input type='radio' value='".$d['name']."' id='radios-repeating".$count."' name='".$code.$id."'>";
						   $out .= $d['name']; 
						   $out .= "<input type='hidden' value='".$d['id'].";".$d['interval_count'].";".$d['interval'].";".$plan_name."' id='inforadios-repeating".$count."' ></label>";  
						   $out .= "</div>";
					   } 
				}

				   $out .= "</div>";

		   }

		$out .= "</div>";
	  }
   }

   return $out;
}

function mg_makeInputRadioTag( $id, $label, $type, $code, $filter, $req, $post_id , $meta_key )
{
   $out = ""; $info = "";
   $lbl = str_replace("[q]","'",$label);
   $first_text = '';
   
   if( strcmp($req, 'required')==0 )
   { 
		$info       = "<abbr class='mg_asterisk' title='required'> *</abbr>"; 
		$first_text = '';
   }   

   $out .= "<div class='form-group'><div class='col-sm-3'>";
   $out .= "<div class='input-group input-group-icon'>";
   $out .= "<label class='mg_control-label  '>".$lbl;
   $out .= $info; 
   $out .= "</label>"; 

   $out .= "</div></div>";

   $out .= "<div class='col-sm-6 col-xs-12'>";
   $out .= "<label style='display:none' class='idfield' id='".$code.$id."'></label>";

   $bglevelcolor = get_option('migla_bglevelcolor');
   $borderlevelcolor = get_option('migla_borderlevelcolor');
   $borderlevel = get_option('migla_borderlevel');
   $borderCSS = "border: ".$borderlevel."px solid ".$borderlevelcolor.";"; 
    
    $data 		= get_post_meta( $post_id, 'mgval_'.$meta_key );
    $options 	= array();
    if( !empty($data) ){
        $options = explode( ";" , (string)$data[0]  );
    }

     $name_radio = $code.$id;
     $name_radio = str_replace(" ","", $name_radio);
     $name_radio = str_replace("?","", $name_radio);
  
    $count = 0;
	if( count($options) > 0 )
	{
	      foreach ( (array)$options as $op ) 
	      { 
		     if( strpos($op, '::') != false )
			 {
                 $op_value = explode(  "::" , $op );
                 if( $op_value[1] != '' ){
                   
                   $out .= "<div class='radio' id='".$meta_key.$count."'  >";
                   if( $count == 0 )
                   { 
						$out .= "<label for='radios-".$meta_key.$count."'><input type='radio' checked value='".$op_value[0]."' id='radios-".$meta_key.$count."' name='".$name_radio."'>".$op_value[1]."</label></div>";
                   }else{
						$out .= "<label for='radios-".$meta_key.$count."'><input type='radio'  value='".$op_value[0]."' id='radios-".$meta_key.$count."' name='".$name_radio."'>".$op_value[1]."</label></div>";
                   }
                   $count++;
                 }
			 }
	      } 	    
	}
	
   $out .= "</div>";
   $out .= "<div class='col-sm-3 hidden-xs'></div></div>";

   return $out;
}

function mg_makeInputMultiCheckboxTag( $id, $label, $type, $code, $filter, $req, $post_id , $meta_key )
{

   $out = ""; $info = "";
   $lbl = str_replace("[q]","'",$label);
   $first_text = '';
   
   if( strcmp($req, 'required')==0 )
   { 
		$info       = "<abbr class='mg_asterisk' title='required'> *</abbr>"; 
		$first_text = '';
   }   
   
   $out .= "<div class='form-group'><div class='col-sm-3'>";
   $out .= "<div class='input-group input-group-icon'>";
   $out .= "<label class='mg_control-label  '>".$lbl;
 
  $out .= $info;
  $out .= "</label>"; 
 
   $out .= "</div></div>";

   $out .= "<div class='col-sm-6 col-xs-12'>";
   $out .= "<label style='display:none' class='idfield' id='".$code.$id."'></label>";

   $bglevelcolor = get_option('migla_bglevelcolor');
   $borderlevelcolor = get_option('migla_borderlevelcolor');
   $borderlevel = get_option('migla_borderlevel');
   $borderCSS = "border: ".$borderlevel."px solid ".$borderlevelcolor.";"; 
    
    $data 		= get_post_meta( $post_id, 'mgval_'.$meta_key );
	$options 	= array();
    if( !empty($data) ){
        $options = explode( ";" , (string)$data[0]  );
    }

     $name_radio = $code.$id;
     $name_radio = str_replace(" ","", $name_radio);
     $name_radio = str_replace("?","", $name_radio);
	 
	$count = 0; 
	 
	if( count($options) > 0 )
	{
	      foreach ( (array)$options as $op ) 
	      { 
			if( strpos($op, '::') != false )
			 {
                 $op_value = explode(  "::" , $op );
                 if( $op_value[1] != '' && $op_value[0] != null )
				 {
                   $count++;
                   $value_to_save = str_replace("'","", $op_value[0] );

                   $out .= "<div class='checkbox' id='".$meta_key.$count."' >"; 
                   $out .= "<label for='checkbox-".$meta_key.$count."'>";
                   $out .= "<input type='checkbox' class='".$req."' id='checkbox-".$meta_key.$count."' name='".$name_radio."' value='".$value_to_save."'>".$op_value[1]."</label></div>";
                   
                 }
			}
	      } 	    
	}
	
   $out .= "</div>";
   $out .= "<div class='col-sm-3 hidden-xs'></div></div>";

   return $out;

}

function mg_makeInputCountry( $id, $label, $type, $code,  $filter, $req )
{
   $out = ""; 
   $info = ""; 
   $first_text = "<option value=''>".__("Please choose one", "migla-donation")."</option>"; 
   if( strcmp($req, 'required')==0 )
   { 
         $info = "<abbr class='mg_asterisk' title='required'> *</abbr>"; 
		 $first_text = '';
   }

   $out = "";
   $lbl = str_replace("[q]","'",$label);
   $out .= "<div class='form-group'><div class='col-sm-3'><label class='mg_control-label'>".$lbl;
   $out .= $info;
   $out .= "</label></div>";
   $out .= "<div class='col-sm-6 col-xs-12'>";
   $out .= "<div class=''>";

   $out .= "<label style='display:none' class='idfield' id='".$code.$id."'></label>";
   $countries =  get_option( 'migla_world_countries' );
   
   $out .= "<select class='mg_form-control migla_country' id=".$id." name='".$code.$id."'>"; 
   $out .= $first_text;
   
   foreach ( $countries as $key => $value ) 
   { 
	      if ( strcmp ( $value , (string)get_option( 'migla_default_country' ) ) == 0 )
		  { 
		    $out .= "<option value='".$value."' selected='selected' >".$value."</option>"; 
		  }else{  
		    $out .= "<option value='".$value."'>".$value."</option>"; 
		  }
   }	   
   $out .= "</select>"; 		
 
 //  $out .= $info;
   $out .= "</div></div>";
   $out .= "<div class='col-sm-3 hidden-xs'></div></div>";

   if( $id == 'country')
   {
      $out .= $this->mg_makeInputState ( 'state',   'State',   'select',  'miglad_', '1', $req);
      $out .= $this->mg_makeInputProvince( 'province', 'Province', 'select', 'miglad_', '1', $req);
   }else if( $id == 'honoreecountry')
   {
      $out .= $this->mg_makeInputState( 'honoreestate',   'State',   'select',  'miglad_', '1', $req);
      $out .= $this->mg_makeInputProvince( 'honoreeprovince', 'Province', 'select', 'miglad_', '1', $req);   
   }
   
   return $out;
}

function mg_makeInputState( $id, $label, $type, $code,  $filter, $req )
{
   $info = '';
   $first_text = "<option value=''>".__("Please choose one", "migla-donation")."</option>"; 
   if( strcmp($req, 'required')==0 )
   { 
         $info = "<abbr class='mg_asterisk' title='required'> *</abbr>"; 
		 $first_text = '';
   }
   $out = "";
   $lbl = str_replace("[q]","'",$label);
   $out .= "<div class='form-group migla_state' id='".$id."'  style='display:none'><div class='col-sm-3'>";
   $out .= "<label class='mg_control-label '>".$lbl. $info;
   $out .= "</label></div><div class='col-sm-6 col-xs-12'>";

   $out .= "<label style='display:none'  class='idfield' id='".$code.$id."'></label>";

	   $states = migla_get_US_states(); //get_option( 'migla_US_states' );
	   
	   $out .= "<select class='mg_form-control migla_state' name='".$code.$id."'>"; 
       $out .= $first_text;
	   
	   foreach ( $states as $key => $value ) 
	   { 
	       $out .= "<option value='".$value."'>".$value."</option>"; 
	   }	   
	   $out .= "</select>";    

   $out .= "</div><div class='col-sm-3 hidden-xs'></div></div>";

   return $out;
}

function mg_makeInputProvince( $id, $label, $type, $code,  $filter, $req )
{
   $info = '';
   $first_text = "<option value=''>".__("Please choose one", "migla-donation")."</option>"; 
   if( strcmp($req, 'required')==0 )
   { 
         $info = "<abbr class='mg_asterisk' title='required'> *</abbr>"; 
		 $first_text = '';
   }

   $out = "";
   $lbl = str_replace("[q]","'",$label);
   $out .= "<div class='form-group migla_province' id='".$id."' style='display:none'><div class='col-sm-3'>";
   $out .= "<label class='mg_control-label '>".$lbl . $info;
   $out .= "</label></div><div class='col-sm-6 col-xs-12'>";

    $out .= "<label style='display:none' class='idfield' id='".$code.$id."'></label>";
	$states = get_option( 'migla_Canada_provinces' );
	$out .= "<select class='mg_form-control migla_province' name='".$code.$id."'>"; 

    $out .= $first_text;
	
	   foreach ( $states as $key => $value ) 
	   { 
	      $out .= "<option value='".$value."'>".$value."</option>"; 
	   }	   
	   $out .= "</select>"; 
		
   $out .= "</div><div class='col-sm-3 hidden-xs'></div></div>";

   return $out;
}

function getCurrencySymbol()
{
   $code = (string)get_option(  'migla_default_currency'  );
   $arr  = get_option( 'migla_currencies' ); 
   $icon ='';
   foreach ( $arr as $key => $value ) {
     if(  strcmp( $code, $arr[$key]['code'] ) == 0  ){
       $icon = $arr[$key]['symbol']; 
       break;
     }
   }
   return $icon;
}

function migla_amounts_single_campaign( $label, $formid , $custom_amount_text )
{
   $symbol = $this->getCurrencySymbol();
   
   if( $this->TYPE_AMOUNT_BOX == 'fill' )
		$amount_box_class = 'mg_giving-levels-text';
   else
		$amount_box_class = '';
		
   $x = array();
   $x[0] = get_option('migla_thousandSep');
   $x[1] = get_option('migla_decimalSep');

   $showSep = get_option('migla_showDecimalSep');
   $decSep = 0;
   if( strcmp($showSep , "yes") == 0 )
   {   
		$decSep = 2; }else{ $x[1] = '';$decSep = 0; 
	}
   
   $placement = get_option('migla_curplacement');
   if( strtolower( $placement ) == 'before' )
   {
      $before = $symbol; $after = "";  $toogle='icon-before';
   }else if( strtolower( $placement ) == 'after' )
   {
      $before = ""; $after = $symbol ; $toogle='icon-after';
   } 
   
   $bglevelcolor     = get_option('migla_bglevelcolor');
   $bglevelcoloractive     = get_option('migla_bglevelcoloractive');
   $borderlevelcolor = get_option('migla_borderlevelcolor');
   $borderlevel      = get_option('migla_borderlevel');
   $borderCSS        = "border: ".$borderlevel."px solid ".$borderlevelcolor.";";    
   
   $out = "";
   
   $out	.= "<input type='hidden' id='mg_level_active_color' value='". $bglevelcoloractive ."'>";
   $out	.= "<input type='hidden' id='mg_level_color' value='".$bglevelcolor."'>";
   $out	.= "<input type='hidden' id='mg_level_active_border' value='".$borderlevel."px solid ".$borderlevelcolor."'>";
   $out .= "<div class='form-group mg_giving-levels'><div class='col-sm-12 col-xs-12'><label class='mg_control-label'>".$label;
   $out .= "</label></div><div class='col-sm-12 col-xs-12'>";
    
    $out .= "<label style='display:none' class='idfield' id='miglad_amount'></label>";
		
	$amounts = $this->AMOUNTS;	

     if( !empty($amounts) )
     {
         $keys_amount = array_keys($amounts);
     }else{

     }
     $idx = 0;
	
if( $this->TYPE_AMOUNT_BTN == 'button' )
{
    if( !empty($amounts) )
    {
	foreach( $keys_amount as $key )
    {	    
		  $state    = '';  $selected = ''; $active_style = '';
		  $valLabel = (double)$amounts[$key]['amount'] ; 
		  $valPerk = $amounts[$key]['perk'];  
		   
          if( $idx == 0 )
		  { 
             $state='mg_amount_checked'; 
			 $selected = 'selected';
			 $active_style = "'background-color:".$bglevelcoloractive.";". $borderCSS."'";		  
          }else{
			 $active_style = "'background-color:".$bglevelcolor.";". $borderCSS."'";		  
		  }

		$out .= "<div class='radio-inline ".$selected." ".$amount_box_class ."'><label for='miglaAmount".$idx."' style=".$active_style." class='migla_amount_lbl' >";
        $out .= "<input type='hidden' value='".$valLabel."' id='miglaAmount".$idx."' name='miglaAmount' class='migla_amount_choice ".$state."' >";
        $out .= "<span class='currency-symbol'>".$before."</span>";
        $out .= number_format( $valLabel, $decSep, $x[1], $x[0]);
        $out .= "<span class='currency-symbol'>".$after."</span>";
		  
		  if($valPerk == '' )
		    $out.= "<span class=''>".$valPerk."</span>";
		  else
		    $out.= "<span class='mg_giving-text-perk'>".$valPerk."</span>";		  
		  
          $out .= "</label></div>";
  
		  $idx = $idx + 1; 
    }
	
   $out .= "</div><div class='col-sm-3 hidden-xs'></div></div>";	
   }
   if( $this->HIDE_CUSTOM_AMOUNT == 'yes' )
   {
      
   }else
   {
	   $inline_class = 'form-group mg_giving-levels';
	   
          if( $idx == 0 )
		  { 
				$state			='mg_amount_checked'; 
				$selected 		= 'selected';
				$active_style   = "'background-color:".$bglevelcoloractive.";". $borderCSS."'";		
				$out 			.= "</div></div>";
          }else{
				$active_style = "'background-color:".$bglevelcolor.";". $borderCSS."'";	
		  }
	   
	   $out .= "<div class='".$inline_class."'>";
	   $out .= "<div class='col-sm-5 col-xs-12'>";
		$out .= "<label style='display:none' class='idfield' id='miglad_camount'></label>";
			
		 $out .= "<div class='radio-inline miglaCustomAmount ".$selected." ".$amount_box_class ."'>";
		 $out .= "<label for='miglaCustomAmount".$idx."' style=".$active_style." class='migla_amount_lbl'>";
		$out .= "<input type='hidden' value='custom' id='miglaAmount".$idx."' name='miglaAmount' class='migla_amount_choice migla_custom_amount'><div>". $custom_amount_text."</div>";

	   if( strtolower( $placement ) == 'before' ){
			  $out .= "<div class='input-group input-group-icon ".$toogle."'><span class='input-group-addon mg_symbol-before'><span class='icon'>".$before."</span></span><input type='text' value='0' id='miglaCustomAmount' class='migla_positive_number_only'></div>";
	   }else{
			  $out .= "<div class='input-group input-group-icon ".$toogle."'><input type='text' value='0' id='miglaCustomAmount' class='migla_positive_number_only'><span class='input-group-addon mg_symbol -after'><span class='icon'>".$after."</span></span></div>";
	   }
	   
	$out .= "</div></div>";
	$out .= "<div class='col-sm-3 hidden-xs'></div></div>";  
	
	//if( $idx == 0 ) $out .= "</div>";  
	
   }

}else{
	
    if( !empty($amounts) )
    {
    foreach( $keys_amount as $key )
    {
		  $state    = '';  $selected = ''; $active_style  = '';
		  $valLabel = (double)$amounts[$key]['amount'] ; 
		  $valPerk  = $amounts[$key]['perk'];
	          	  
          if( $idx == 0 )
		  { 
             $state=''; 
			 $selected = 'selected';
			 $checked = 'checked';
			 $active_style = "'background-color:".$bglevelcolor.";". $borderCSS."'";
          }else{
             $state		= ''; 
			 $selected 	= '';
			 $checked 	= '';		  
			 $active_style = "'background-color:".$bglevelcolor.";". $borderCSS."'";			  
		  }
		    		  	  
	      $out .= "<div class='radio-inline ".$selected." ".$amount_box_class ."'><label for='miglaAmount".$idx."' style=".$active_style." >";
          $out .= "<input type='radio' value='".$valLabel."' id='miglaAmount".$idx."' name='miglaAmount' ".$checked." class='migla_amount_choice ".$state."'>";
          $out .= "<span class='currency-symbol'>".$before."</span>";
          $out .= number_format( $valLabel, $decSep, $x[1], $x[0]);
		  $out .= "<span class='currency-symbol'>".$after."</span>";
		  if($valPerk == '' )
		    $out.= "<span class=''>".$valPerk."</span>";
		  else
		    $out.= "<span class='mg_giving-text-perk'>".$valPerk."</span>";		  		  

          $out .= "</label></div>";
		  
          $idx = $idx + 1;
    }
	
   $out .= "</div><div class='col-sm-3 hidden-xs'></div></div>";	
	}
	
   if( $this->HIDE_CUSTOM_AMOUNT == 'yes' )
   {
      //Dont write custom amount
   }else
   {   
		
          if( $idx == 0 )
		  {  
				$out 			.= "</div></div>";
	  
				$state=''; 
				$selected = 'selected';
				$checked = 'checked';
				$active_style = "'background-color:".$bglevelcolor.";". $borderCSS."'";	
				

				$inline_class = 'form-group mg_giving-levels';
				
				$out .= "<div class='".$inline_class."'>";
				$out .= "<div class='col-sm-5 col-xs-12'>";
		   
				$out .= "<label style='display:none' class='idfield' id='miglad_camount' style=".$active_style."></label></div>";	  
				$out .= "<div class='radio-inline miglaCustomAmount' style='display:none'><label for='miglaCustomAmount".$idx."'";
				$out .= " style='background-color:".$bglevelcolor.";". $borderCSS."'>";
				$out .= "<input type='radio' ".$checked." value='custom' id='miglaAmount".$idx."' name='miglaAmount' class='migla_amount_choice migla_custom_amount'>";
				$out .= "<div>". $custom_amount_text."</div>";

				if( strtolower( $placement ) == 'before' ){
					  $out .= "<div class='input-group input-group-icon ".$toogle."'><span class='input-group-addon'><span class='icon'>".$before."</span></span><input type='text' value='0' id='miglaCustomAmount' class='migla_positive_number_only'></div>";
				}else{
					  $out .= "<div class='input-group input-group-icon ".$toogle."'><input type='text' value='0' id='miglaCustomAmount' class='migla_positive_number_only'><span class='input-group-addon'><span class='icon'>".$after."</span></span></div>";
				}	  
			  
				$out .= "</div></div>";
				$out .= "<div class='col-sm-3 hidden-xs'></div></div>";
				
          }else{
		  
				 $state		= ''; 
				 $selected 	= '';
				 $checked 	= '';		  
				 $active_style = "'background-color:".$bglevelcolor.";". $borderCSS."'";	
				 $inline_class = 'form-group mg_giving-levels';

				$out .= "<div class='".$inline_class."'>";
				$out .= "<div class='col-sm-5 col-xs-12'>";
		   
				$out .= "<label style='display:none' class='idfield' id='miglad_camount' style=".$active_style."></label>";	  
				$out .= "<div class='radio-inline miglaCustomAmount' style='display:none'><label for='miglaCustomAmount".$idx."'";
				$out .= " style='background-color:".$bglevelcolor.";". $borderCSS."'>";
				$out .= "<input type='radio' ".$checked." value='custom' id='miglaAmount".$idx."' name='miglaAmount' class='migla_amount_choice migla_custom_amount'>";
				$out .= "<div>". $custom_amount_text."</div>";

				if( strtolower( $placement ) == 'before' ){
					  $out .= "<div class='input-group input-group-icon ".$toogle."'><span class='input-group-addon'><span class='icon'>".$before."</span></span><input type='text' value='0' id='miglaCustomAmount' class='migla_positive_number_only'></div>";
				}else{
					  $out .= "<div class='input-group input-group-icon ".$toogle."'><input type='text' value='0' id='miglaCustomAmount' class='migla_positive_number_only'><span class='input-group-addon'><span class='icon'>".$after."</span></span></div>";
				}	  
			  
				$out .= "</div></div>";
				$out .= "<div class='col-sm-3 hidden-xs'></div></div>";	
		  }

		//if( $idx == 0 ) $out .= "</div>";  
   }

}

   
    return $out;
 }

function migla_campaign_section( $postCampaign , $label )
{
   $out = "";

    $campaign = (array)get_option( 'migla_campaign' );
    $undesign = get_option('migla_undesignLabel');
    $undesign = esc_attr($undesign);   
    $show_bar = get_option( 'migla_show_bar' );
   
   if( empty($campaign[0]) ){ 
   
   $out .= "<div class='form-group' style='display:none'><div class='col-sm-12 col-xs-12'><label class='mg_control-label  mg_campaign-switcher'>".$label;
   $out .= "</label></div><div class='col-sm-6 col-xs-12'>";
	
    $out .= "<label style='display:none' class='idfield' id='miglad_campaign'></label>";

    $out .= "<select name='campaign' class='mg_form-control' id='miglaform_campaign' style='display:none'>";
    $b = ""; $i = 0;   
    $out .= "<option value='". esc_html($undesign)."'>".esc_html($undesign)."</option>";
    $out .= "</select>"; 
	
    $out .= "</div>";
    $out .= "<div class='col-sm-3 hidden-xs'></div></div>";		

  }else{    
    $b = ""; $i = 0;  $out2 = ""; $campaignCount = 0;
   
    $out2 .= "<div class='form-group' ><div class='col-sm-12 col-xs-12'><label class='mg_control-label  mg_campaign-switcher'>".$label;
    $out2 .= "</label></div><div class='col-sm-6 col-xs-12'>";
	
    $out2 .= "<label style='display:none' class='idfield' id='miglad_campaign'></label>";
 	
    $out2 .= "<select name='campaign' class='mg_form-control' id='miglaform_campaign' >";

   if( get_option('migla_hideUndesignated') == 'no' ){
     $out2 .= "<option value='". esc_html($undesign)."'>".esc_html($undesign)."</option>";
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
	
       $out2 .= "</div>";
       if( $show_bar == 'yes' ){  $out2 .= "<div class='col-sm-12 col-xs-12'><div id='migla_bar'></div></div>";  }
       $out2 .= "<div class='col-sm-3 hidden-xs'></div></div>";	

    if( $campaignCount > 0 ){
       $out .= $out2;	   
    }else{
       $out .= "<div class='form-group' style='display:none'><div class='col-sm-12 col-xs-12'><label class='mg_control-label  mg_campaign-switcher'>".$label;
       $out .= "</label></div><div class='col-sm-6 col-xs-12'>";
	
       $out .= "<label style='display:none' class='idfield' id='miglad_campaign'></label>";

       $out .= "<select name='campaign' class='mg_form-control' id='miglaform_campaign' style='display:none'>";
       $b = ""; $i = 0;   
       $out .= "<option value='". esc_html($undesign)."'>".esc_html($undesign)."</option>";
       $out .= "</select>"; 
	
       $out .= "</div>";
       if( $show_bar == 'yes' ){  $out .= "<div class='col-sm-12 col-xs-12'><div id='migla_bar'></div></div>";  }
       $out .= "<div class='col-sm-3 hidden-xs'></div></div>";
    }
  }	   
  
  return $out;

}


function migla_onecampaign_section( $postCampaign , $label , $show )
{
    $out = ""; $style = "";
    if( $show == 0 ){
       $style = 'display:none';
    }
    $out .= "<div class='form-group' style='".$style."'><div class='col-sm-12 col-xs-12'><label class='mg_control-label  '>".$label;
    $out .= "</label></div><div class='col-sm-6 col-xs-12'>";
	
    $out .= "<label style='display:none' class='idfield' id='miglad_campaign'></label>";

    $out .= "<select name='campaign' class='mg_form-control' id='miglaform_campaign' style='".$style."'>";
    $b = ""; $i = 0;   

    $postCampaign_value = str_replace("'", "[q]" , $postCampaign) ;

    $out .= "<option value='". esc_html($postCampaign_value)."'>".esc_html($postCampaign)."</option>";
    $out .= "</select>"; 
	
    $out .= "</div>";
    $show_bar = get_option('migla_show_bar');
    if( $show_bar == 'yes' ){
       $out .= "<div class='col-sm-12 col-xs-12'><div id='migla_bar'></div></div>";
    }
    $out .= "<div class='col-sm-3 hidden-xs'></div></div>";

   return $out;
}


function migla_paypal(  $isTab, $bgcolor, $borderCSS ) {

   $button_image_url = plugins_url( '/images/paypal_btn_donate_lg.gif', __FILE__  );
   $btnchoice = get_option('miglaPayPalButtonChoice');

   if ( $btnchoice == 'paypalButton' )
   {
        $btnlang = get_option('migla_paypalbutton');
        $btnlang = "/images/btn_donate_". $btnlang .".gif";
        $button_image_url = plugins_url( $btnlang , __FILE__ );
   }else if( $btnchoice == 'imageUpload' ){
        $btnurl = get_option('migla_paypalbuttonurl');
        $button_image_url = $btnurl;
   }else{ 
   }
   
   $out = "";

   $pm = get_option('migla_paypal_method');
   if( $pm == 'pro_only' )
   {
     	  $out .= "<div class='form-group' style='display:none'>";
		  $out .= "<div class='col-sm-12 col-xs-12' >";	  
		  $out .= "<div class='radio'><label for='miglaPaypalMethodPro' >";
		  $out .= "<input type='radio' value='paypal_pro' id='miglaPaypalMethodPro' name='miglaPaypalMethod' class='' checked> ";
		  $out .= "</label></div>";
		  $out .= "</div></div>";   
		  
     if( $isTab ){   
        $out .= $this->migla_paypal_pro();
	  }else{
         $out .= "<div class='form-horizontal migla-payment-options' >
                     <div class='mg_tab-content' >
                     <div id='sectionpaypal' class='' style='background-color:".$bgcolor.";".$borderCSS."'>";	  
        $out .= $this->migla_paypal_pro();
        $out .= "</div>";
        $out .= "</div>";
        $out .= "</div>";
	  }

   }else if( $pm == 'pro_standard' || $pm == 'pro_pdt' )
   {
     $cc_label = (array)get_option('migla_paypalpro_cc_info'); 
	  
     if( $isTab )
	 {   					 
		  $out .= "<div class='form-group'>";
		  $out .= "<div class='col-sm-12 col-xs-12' >";	  
		  $out .= "<div class='radio'><label for='miglaPaypalMethodPro' >";
		  $out .= "<input type='radio' value='paypal_pro' id='miglaPaypalMethodPro' name='miglaPaypalMethod' checked> ";
		  $out .= $this->mg_write_me( $cc_label[1][1] )."</label></div>";
		  $out .= "<div class='radio'><label for='miglaPaypalMethodStd' >";
		  $out .= "<input type='radio' value='paypal_standard' id='miglaPaypalMethodStd' name='miglaPaypalMethod'> ";
		  $out .= $this->mg_write_me( $cc_label[2][1] )."</label></div>";
		  $out .= "</div>";
		  $out .= "</div>";
  
		  $out .= $this->migla_paypal_pro();

     }else{
         $out .= "<div class='form-horizontal migla-payment-options' >
                     <div class='mg_tab-content' >
                     <div id='sectionpaypal' class='' style='background-color:".$bgcolor.";".$borderCSS."'>";	  
		  $out .= "<div class='form-group'>";
		  $out .= "<div class='col-sm-12 col-xs-12' >";	  
		  $out .= "<div class='radio'><label for='miglaPaypalMethodPro' >";
		  $out .= "<input type='radio' value='paypal_pro' id='miglaPaypalMethodPro' name='miglaPaypalMethod' checked> ";
		  $out .= $this->mg_write_me( $cc_label[1][1] )."</label></div>";
		  $out .= "<div class='radio'><label for='miglaPaypalMethodStd' >";
		  $out .= "<input type='radio' value='paypal_standard' id='miglaPaypalMethodStd' name='miglaPaypalMethod'> ";
		  $out .= $this->mg_write_me( $cc_label[2][1] )."</label></div>";
		  $out .= "</div>";
		  $out .= "</div>";
 
		  $out .= $this->migla_paypal_pro();
		 			  
		  $out .= "</div>";
		  $out .= "</div>";
		 $out .= "</div>";
	  }
					 
   }else{
       /** STD only **/
     	  $out .= "<div class='form-group' style='display:none'>";
		  $out .= "<div class='col-sm-12 col-xs-12' >";	  
		  $out .= "<div class='radio'><label for='miglaPaypalMethodStd' >";
		  $out .= "<input type='radio' value='paypal_standard' id='miglaPaypalMethodStd' name='miglaPaypalMethod' checked > ";
		  $out .= "</label></div>";
		  $out .= "</div>";
		  $out .= "</div>";   

   } //$pm

		  $out .= "<div class='form-group'>";
		  $out .= "<div class='col-sm-12 col-xs-12' >";

		  if( $btnchoice == 'cssButton' || $btnchoice == '' || $btnchoice == false){
			$btnstyle = "";
			if( get_option('migla_paypalcssbtnstyle')=='Grey' ){ $btnstyle='mg-btn-grey'; }
			$out .= "<button id='miglapaypalcheckout_std' class='miglacheckout ".$btnstyle." ";
			$out .= get_option('migla_paypalcssbtnclass') ."' >".  get_option('migla_paypalcssbtntext') ."</button>";
		  }else
		  {
			$out .= "<a class='mg_PayPalButton miglacheckout mbutton' id='miglapaypalcheckout_std'";
			$out .= "> <img src='" . esc_url( $button_image_url ) . "'> </a>";
		  }
		  $out .= "</div>";
		  $out .= "</div>";	   
   
      $load = plugins_url( 'totaldonations/images/loading.gif', dirname(__FILE__) );
      $out .= "<div id='mg_wait_paypal' class='mg_wait' style='display:none !important'>".get_option('migla_paypal_wait_paypal');
      $out .= "&nbsp; <input  id='mg_loadingButton' type='image' src='" . esc_url( $load ) . "'></div>";
      $out .= "<div id='mg_wait_paypal_pro' class='mg_wait' style='display:none !important'>".get_option('migla_paypal_wait_paypalpro');
      $out .= "&nbsp; <input  id='mg_loadingButton' type='image' src='" . esc_url( $load ) . "'></div>";

   return $out;
}



  function migla_hidden_form( $id ) 
  {
		$paypalEmail = get_option( 'migla_paypal_emails' );
	  
		$payPalServer = get_option('migla_payment');
		 if ($payPalServer == "sandbox")
		 {
			$formAction = "https://www.sandbox.paypal.com/cgi-bin/webscr";
		 }else{
			 $formAction = "https://www.paypal.com/cgi-bin/webscr";
		 }

		$notifyUrl = '';
        $successUrl = '';
		$ipn = get_option('migla_ipn_choice');
		
		if( $ipn == 'back' )
			$notifyUrl 	= plugins_url('totaldonations/migla-donation-paypalstd-ipn.php', dirname(__FILE__) );
		else
			$notifyUrl 	= esc_url( add_query_arg( 'migla_listener', 'IPN', home_url( 'index.php' ) ) );
			
		$successUrl = migla_get_succesful_url( $id ) ;

		$currency_code = get_option( 'migla_default_currency' );
		 
        $_item_ = get_option('migla_paypalitem');
        if(  $_item_ == '' || $_item_ == false ){
           $item_name = 'donation';   
        }else{
   	   $item_name = $_item_ ;               
        }
	
		$output = "";
		$output .= "<form id='migla-hidden-form' action='" . esc_attr( $formAction ) . "' method='post' >";

        $cmd_type = get_option('migla_paymentcmd');
        if(  $cmd_type == 'payment' ){
            $output .= "<input type='hidden' name='cmd' value='_xclick' >"; 
        }else{
			$output .= "<input type='hidden' name='cmd' value='_donations' >";            
        }

        $output .= "<input type='hidden' name='custom' value='".$id."' >";
	$output .= "<input type='hidden' name='business' value='" . esc_attr( $paypalEmail ) . "' >";

	$output .= "<input type='hidden' name='return' value='" . esc_attr( $successUrl ) ."' >";
	$output .= "<input type='hidden' name='notify_url' value='" . esc_attr( $notifyUrl ) . "' >";
	

	$output .= "<input type='hidden' name='email' value='' >";
	$output .= "<input type='hidden' name='first_name' value='' > ";
	$output .= "<input type='hidden' name='last_name' value='' >";
	$output .= "<input type='hidden' name='address1' value='' >";
        $output .= "<input type='hidden' name='address2' value=''>";
        $output .= "<input type='hidden' name='city' value=''>";
        $output .= "<input type='hidden' name='country' value=''>";
        $output .= "<input type='hidden' name='state' value=''>";
        $output .= "<input type='hidden' name='zip' value=''>";

        $output .= "<input type='hidden' value='2' name='rm'>"; 

	if( get_option('migla_paypal_fec') == 'yes')
	{	
		$output .= "<input type='hidden' name='on0' value='DisclosureName' >";
		$output .= "<input type='hidden' name='os0' value='' > ";
		$output .= "<input type='hidden' name='on1' value='DisclosureEmployerOccupation' >";
        $output .= "<input type='hidden' name='os1' value='' > ";
	}

		$output .= "<input type='hidden' name='on2' value='Campaign' >";
        $output .= "<input type='hidden' name='os2' value='' > ";
	
	$output .= "<input type='hidden' name='item_name' value='" . esc_attr( $item_name ) . "' >";
	$output .= "<input type='hidden' name='amount' value='1.00' />";
	$output .= "<input type='hidden' name='quantity' value='1' />";
	$output .= "<input type='hidden' name='currency_code' value='".esc_attr( $currency_code )."' >";
	$output .= "<input type='hidden' name='no_note' value='1'>";

	$output .= "<input type='hidden' name='src' value='1'>"; 
	$output .= "<input type='hidden' name='p3' value='1'>";  
 	$output .= "<input type='hidden' name='t3' value='1'>";  
	$output .= "<input type='hidden' name='a3' value='1'>"; 

        $output .= "<input type='submit' id='miglaHiddenSubmit' style='display:none !important' />";

	$output .= "</form>";

	return $output;
	
  }

public function mg_write_me( $str )
{
   $result =  str_replace( "//" , "/" , $str );
   $result =  str_replace( "[q]" , "'" , $result );
   return $result;
}

/**************  STRIPE Tab   *********************************************/
public function migla_stripe(){
    $out = "";

   $cc_label = (array)get_option('migla_stripe_cc_info'); 

   $out .= "<form id='mg-stripe-payment-form' ><div class='form-group'>
								<div class='col-sm-3 col-xs-12'><div class='input-group input-group-icon'><label class='mg_control-label  '>".$this->mg_write_me($cc_label[1][1])."  <abbr class='mg_asterisk' title='required'> *</abbr></label><span class='input-group-addon'><span class='icon'><!--*--></span></span></div></div>



								<div class='col-sm-6 col-xs-12'>
									
										


<input type='text' aria-required='true' data-stripe='name' name='cardholder_name' class='mg_form-control touch-top' placeholder='".$this->mg_write_me($cc_label[2][1])."' value='' data-rule-required='true' id='mg_stripe_card_name'>
										
									 
								</div>
							</div>
							<div class='form-group'>
								<div class='col-sm-3 col-xs-12'><div class='input-group input-group-icon'><label class='mg_control-label  '>".$this->mg_write_me($cc_label[3][1])."  <abbr class='mg_asterisk' title='required'> *</abbr></label><span class='input-group-addon'><span class='icon'><!--*--></span></span></div></div>
								<div class='col-sm-6 col-xs-12'>
									


							

										



<div class='input-group input-group-icon icon-after'><input type='text' id='mg_stripe_card_number' aria-required='true' data-stripe='number' class='mg_form-control touch-middle card-number' placeholder='".$this->mg_write_me($cc_label[4][1])."' value='' data-rule-required='true' data-rule-creditcard='true'><span class='input-group-addon'><span class='mg_creditcardicons'></span></span></div>


 
										
																	
							
							
								</div>
							</div>";
										



      $out .= "							<div class='form-group'>
								<div class='col-sm-3 col-xs-12'><div class='input-group input-group-icon'><label class='mg_control-label  '>".$this->mg_write_me($cc_label[5][1])."  <abbr class='mg_asterisk' title='required'> *</abbr></label><span class='input-group-addon'><span class='icon'><!--*--></span></span></div></div>
								

<div class='col-sm-6 col-xs-12'>


  <div class='input-group'>
    <span class='input-group-btn'>
      <select aria-required='true' id='mg_stripe_month' data-stripe='exp-month' class='mg_form-control touch-bottom card-expiry-month' data-rule-required='true'>";
       for( $m = 1; $m <=12 ; $m++ ){
               $out .= "<option value='".$m."'>".$m."</option>";
       }
                                                                                        
       $out .= " </select>";

       $out .= "</span><span class='input-group-btn'>";

       $start_year = date('Y');
		$out .= "	
					<select id='mg_stripe_year' aria-required='true' data-stripe='exp-year' class='mg_form-control touch-bottom card-expiry-year' data-rule-required='true'>";
          for( $y = $start_year; $y <= ($start_year + 15) ; $y++ ){
               $out .= "<option value='".$y."'>".$y."</option>";
          }
        $out .= "</select>";
        $out .= "</span>

<span class='input-group-btn'>


<div class='input-group input-group-icon icon-after'><input type='text' id='mg_stripe_cvc' data-rule-required='true' value='' placeholder='".$this->mg_write_me($cc_label[6][1])."' class='mg_form-control touch-bottom card-cvc' name='cvc' data-stripe='cvc' aria-required='true'><span class='input-group-addon'><span class='mg_creditcardicons mg_padlock'></span></span></div>


  </span>
  </div>
</div>


</form>";

   
   $btnchoice = get_option('miglaStripeButtonChoice');

   $out .= "</div><div class='form-group'>";
   $out .= "<div class='col-sm-12 col-xs-12'>";

   if( $btnchoice == 'cssButton' ){
         $btnstyle = "";
	 if( get_option('migla_stripecssbtnstyle')=='Grey' ){ $btnstyle='mg-btn-grey'; }
	    $out .= "<button id='miglastripecheckout' class='miglacheckout ".$btnstyle." ". get_option('migla_stripecssbtnclass') ."'>". get_option('migla_stripecssbtntext') ."</button>";
   }else if( $btnchoice == 'imageUpload' ){
        $btnurl = get_option('migla_stripebuttonurl');
        $button_image_url = $btnurl;
        $out .= "<input class='mg_StripeButton miglacheckout' id='miglastripecheckout' type='image' src='" . esc_url( $button_image_url ) . "' />"; 
   }else{
        $btn_text = get_option("migla_stripebutton_text");
        if ( $btn_text == '' || $btn_text == false){
             $btn_text = "Pay with Card";
        }
	$out .= "<button id='miglastripecheckout' class='stripe-button-el mg_StripeButton miglacheckout'>
                <span style='display: block; min-height: 30px;'>".$btn_text."</span></button>"; 
   }
   $load = plugins_url( 'totaldonations/images/loading.gif', dirname(__FILE__) );
   
   $out .= "<div id='mg_wait_stripe' class='mg_wait' style='display:none'>".get_option('migla_wait_stripe')."&nbsp; <input  id='mg_loadingButton' type='image' src='" . esc_url( $load ) . "'></div>";

   $out .= "</div>";
   $out .= "</div>";

    return $out;
  
  } 


/**************  PAYPAL PRO tab  *********************************************/
function migla_paypal_pro(){
   

   $cc_label = (array)get_option('migla_paypalpro_cc_info'); 
   $out = "";
   $out .= "<form id='mg-paypalpro-payment-form'>";

/**** First Name PayPal Pro 

$out .= "<div class='form-group'><div class='col-sm-3 col-xs-12'><div class='input-group input-group-icon'><label class='mg_control-label  '>".mg_write_me($cc_label[3][1])."</label><span class='input-group-addon'><span class='icon'><!--*--></span></span></div></div><div class='col-sm-6 col-xs-12'><input type='text' name='cardholder_name' class='mg_form-control touch-top' placeholder='".mg_write_me($cc_label[4][1])."' value='' id='mg_paypalpro_card_firstname'></div></div>";


/***** Last Name PayPal Pro 

$out .= "<div class='form-group'><div class='col-sm-3 col-xs-12'><div class='input-group input-group-icon'><label class='mg_control-label  '>".mg_write_me($cc_label[5][1])."</label><span class='input-group-addon'><span class='icon'><!--*--></span></span></div></div><div class='col-sm-6 col-xs-12'><input type='text' name='cardholder_name' class='mg_form-control touch-middle' placeholder='".mg_write_me($cc_label[6][1])."' id='mg_paypalpro_card_lastname'></div></div>";


**/

$out .= "<div class='form-group'><div class='col-sm-3 col-xs-12'><div class='input-group input-group-icon'><label class='mg_control-label  '>".$this->mg_write_me($cc_label[3][1])."  <abbr class='mg_asterisk' title='required'> *</abbr></label><span class='input-group-addon'><span class='icon'></span></span></div></div>

<div class='col-sm-6 col-xs-12'>


  <div class='input-group'>
    <span class='input-group-btn'>
    
    
       <input type='text' name='cardholder_name' class='mg_form-control touch-top' placeholder='".$this->mg_write_me($cc_label[4][1])."' value='' id='mg_paypalpro_split_firstname'>
    
    
    </span>

<span class='input-group-btn'>


<input type='text' name='cardholder_name' class='mg_form-control touch-top' placeholder='".$this->mg_write_me($cc_label[6][1])."' id='mg_paypalpro_split_lastname'>


  </span>
  </div>
</div></div>";




/*** PayPal Pro Card Number ***/


$out .= "<div class='form-group'><div class='col-sm-3 col-xs-12'><div class='input-group input-group-icon'><label class='mg_control-label  '>".$this->mg_write_me($cc_label[7][1])."  <abbr class='mg_asterisk' title='required'> *</abbr></label><span class='input-group-addon'><span class='icon'></span></span></div></div><div class='col-sm-6 col-xs-12'><div class='input-group input-group-icon icon-after'><input type='text' id='mg_paypalpro_card_number' class='mg_form-control touch-middle card-number' placeholder='".$this->mg_write_me($cc_label[8][1])."' value=''><span class='input-group-addon'><span class='mg_creditcardicons_paypal'></span></span></div></div></div>";


/** PayPal Pro EXP and CVC **/

							
$out .= "<div class='form-group'><div class='col-sm-3 col-xs-12'><div class='input-group input-group-icon'><label class='mg_control-label '>".$this->mg_write_me($cc_label[9][1])."  <abbr class='mg_asterisk' title='required'> *</abbr></label><span class='input-group-addon'><span class='icon'><!--*--></span></span></div></div>
								

<div class='col-sm-6 col-xs-12'>


  <div class='input-group'>
    <span class='input-group-btn'>
      <select id='mg_paypalpro_month' class='mg_form-control touch-bottom card-expiry-month'>";
       for( $m = 1; $m <=12 ; $m++ ){
               $out .= "<option value='".$m."'>".$m."</option>";
       }
                                                                                        
       $out .= " </select>";

       $out .= "</span><span class='input-group-btn'>";

       $start_year = date('Y');
		$out .= "	
					<select id='mg_paypalpro_year' class='mg_form-control touch-bottom card-expiry-year ' >";
          for( $y = $start_year; $y <= ($start_year + 15) ; $y++ ){
               $out .= "<option value='".$y."'>".$y."</option>";
          }
        $out .= "</select>";
        $out .= "</span>

<span class='input-group-btn'>


<div class='input-group input-group-icon icon-after'><input type='text' id='mg_paypalpro_cvc' data-rule-required='true' value='' placeholder='".$this->mg_write_me($cc_label[10][1])."' class='mg_form-control touch-bottom card-cvc' name='cvc' ><span class='input-group-addon'><span class='mg_creditcardicons_paypal mg_padlock'></span></span></div>


  </span>
  </div>
</div>
</div>

</form>";

/*
   $out .= "<div class='form-group'>";
   $out .= "<div class='col-sm-12 col-xs-12 ".$add_class."' >";

      if( $btnchoice == 'cssButton' || $btnchoice == '' || $btnchoice == false){
	 $btnstyle = "";
		if( get_option('migla_paypalcssbtnstyle')=='Grey' ){ $btnstyle='mg-btn-grey'; }
		$out .= "<button id='miglapaypalcheckout_pro' class='miglacheckout ".$btnstyle." ";
                $out .= get_option('migla_paypalcssbtnclass') ."'>".  get_option('migla_paypalcssbtntext') ."</button>";
      }else{
	$out .= "<input class='mg_PayPalButton miglacheckout' id='miglapaypalcheckout_pro' type='image' src='" . esc_url( $button_image_url ) . "' />"; 
      }

   $out .= "</div>";
   $out .= "</div>";
*/
    return $out;
  
  } 
/******** Offline Donations ************************************************************************/

function migla_offline(){
   
   $str = get_option('migla_offline_info');
   $str_out = str_replace( '\"', '' ,$str );

   $out = "";
   $out .= "<div class='form-group'>"; 
   $out .= "<div class='col-sm-12 col-xs-12' >";
   $out .= $str_out;
   $out .= "</div>";
   $out .= "</div>";   

   $button_image_url = get_option('migla_offlinebuttonurl');
   $btnchoice = get_option('miglaOfflineButtonChoice');   
   
   if( $btnchoice == 'none' )
   {
   
   }else{
	   $out .= "<div class='form-group'>"; 
	   $out .= "<div class='col-sm-12 col-xs-12' >";

      if( $btnchoice == 'cssButton' || $btnchoice == '' || $btnchoice == false){
		 $btnstyle = "";
			if( get_option('migla_offlinecssbtnstyle')=='Grey' ){ $btnstyle='mg-btn-grey'; }
			$out .= "<button id='miglaofflinecheckout' class='miglacheckout ".$btnstyle." ";
					$out .= get_option('migla_offlinecssbtnclass') ."'>".  get_option('migla_offlinecssbtntext') ."</button>";
      }else{
		$out .= "<input class='mg_OfflineButton miglacheckout' id='miglaofflinecheckout' type='image' src='" . esc_url( $button_image_url ) . "' />"; 
      }   
   
	   $out .= "</div>";
	   $out .= "</div>";   

      $load = plugins_url( 'totaldonations/images/loading.gif', dirname(__FILE__) );
      $out .= "<div id='mg_wait_offline' class='mg_wait' style='display:none !important'>".get_option('migla_wait_offline');
      $out .= "&nbsp; <input  id='mg_loadingButton' type='image' src='" . esc_url( $load ) . "'/></div>";
      $out .= "<div id='mg_thankyou_offline' class='mg_wait' style='display:none !important'>".get_option('migla_thankyou_offline');
      $out .= "</div>";
	  
	}  

//   $out .= "</div>";

   return $out;
}

/****** Authorize Tabs ************/

function migla_authorize(){
    $out = "";

   $cc_label = (array)get_option('migla_authorize_cc_info'); 



$out .= "<form id='mg-authorize-payment-form'>";


/********* first name *********


$out .= "<div class='form-group'><div class='col-sm-3 col-xs-12'><div class='input-group input-group-icon'><label class='mg_control-label  '>".mg_write_me($cc_label[1][1])."  <abbr class='mg_asterisk' title='required'> *</abbr></label><span class='input-group-addon'><span class='icon'><!--*--></span></span></div></div><div class='col-sm-6 col-xs-12'><input type='text' name='cardholder_name' class='mg_form-control touch-top' placeholder='".mg_write_me($cc_label[2][1])."' value='' id='mg_authorize_firstname'></div></div>";


/********* last name **********


$out .= "<div class='form-group'><div class='col-sm-3 col-xs-12'><div class='input-group input-group-icon'><label class='mg_control-label  '>".mg_write_me($cc_label[3][1])."  <abbr class='mg_asterisk' title='required'> *</abbr></label><span class='input-group-addon'><span class='icon'><!--*--></span></span></div></div><div class='col-sm-6 col-xs-12'><input type='text' name='cardholder_name' class='mg_form-control touch-middle' placeholder='".mg_write_me($cc_label[4][1])."' id='mg_authorize_lastname'></div></div>";


**************/


$out .= "<div class='form-group'><div class='col-sm-3 col-xs-12'><div class='input-group input-group-icon'><label class='mg_control-label  '>".$this->mg_write_me($cc_label[1][1])."  <abbr class='mg_asterisk' title='required'> *</abbr></label><span class='input-group-addon'><span class='icon'><!--*--></span></span></div></div>

<div class='col-sm-6 col-xs-12'>


  <div class='input-group'>
    <span class='input-group-btn'>
    
    
  <input type='text' name='cardholder_name' class='mg_form-control touch-top' placeholder='".$this->mg_write_me($cc_label[2][1])."' value='' id='mg_authorize_split_firstname'>
    
    
    </span>

<span class='input-group-btn'>


<input type='text' name='cardholder_name' class='mg_form-control touch-top' placeholder='".$this->mg_write_me($cc_label[4][1])."' id='mg_authorize_split_lastname'>


  </span>
  </div>
</div></div>";



/******* credit card number ******/


$out .= "<div class='form-group'><div class='col-sm-3 col-xs-12'><div class='input-group input-group-icon'><label class='mg_control-label  '>".$this->mg_write_me($cc_label[5][1])."  <abbr class='mg_asterisk' title='required'> *</abbr></label><span class='input-group-addon'><span class='icon'><!--*--></span></span></div></div><div class='col-sm-6 col-xs-12'><div class='input-group input-group-icon icon-after'><input type='text' id='mg_authorize_card_number' class='mg_form-control touch-middle card-number' placeholder='".$this->mg_write_me($cc_label[6][1])."' value=''><span class='input-group-addon'><span class='mg_creditcardicons_authorize'></span></span></div></div></div>";
										


$out .= "<div class='form-group'><div class='col-sm-3 col-xs-12'><div class='input-group input-group-icon'><label class='mg_control-label  '>".$this->mg_write_me($cc_label[7][1])."  <abbr class='mg_asterisk' title='required'> *</abbr></label><span class='input-group-addon'><span class='icon'><!--*--></span></span></div></div><div class='col-sm-6 col-xs-12'>


  <div class='input-group'>
    <span class='input-group-btn'>
      <select id='mg_authorize_month' class='mg_form-control touch-bottom card-expiry-month'>";
       for( $m = 1; $m <=12 ; $m++ ){
               $out .= "<option value='".$m."'>".$m."</option>";
       }
                                                                                        
       $out .= " </select>";

       $out .= "</span><span class='input-group-btn'>";

       $start_year = date('Y');
		$out .= "	
					<select id='mg_authorize_year' class='mg_form-control touch-bottom card-expiry-year' >";
          for( $y = $start_year; $y <= ($start_year + 15) ; $y++ ){
               $out .= "<option value='".$y."'>".$y."</option>";
          }
        $out .= "</select>";
        $out .= "</span>

<span class='input-group-btn'>


<div class='input-group input-group-icon icon-after'><input type='text' id='mg_authorize_cvc' data-rule-required='true' value='' placeholder='".$this->mg_write_me($cc_label[8][1])."' class='mg_form-control touch-bottom card-cvc' name='cvc' ><span class='input-group-addon'><span class='mg_creditcardicons_authorize mg_padlock'></span></span></div>


 </span>
  </div>
</div>
</div>

</form>";

   $out .= "<div class='form-group'>";
   $out .= "<div class='col-sm-12 col-xs-12' >";

   $button_image_url = get_option('migla_authorizebuttonurl');
   $btnchoice = get_option('miglaAuthorizeButtonChoice');

      if( $btnchoice == 'cssButton' || $btnchoice == '' || $btnchoice == false){
	 $btnstyle = "";
		if( get_option('migla_authorizecssbtnstyle')=='Grey' ){ $btnstyle='mg-btn-grey'; }
		$out .= "<button id='miglaauthorizecheckout' class='miglacheckout ".$btnstyle." ";
                $out .= get_option('migla_authorizecssbtnclass') ."'>".  get_option('migla_authorizecssbtntext') ."</button>";
      }else{
	$out .= "<input class='mg_AuthorizeButton miglacheckout' id='miglaauthorizecheckout' type='image' src='" . esc_url( $button_image_url ) . "' />"; 
      }

   $out .= "</div>";
   $out .= "</div>";
   $load = plugins_url( 'totaldonations/images/loading.gif', dirname(__FILE__) );
   $out .= "<div id='mg_wait_authorize' class='mg_wait' style='display:none !important'>".get_option('migla_wait_authorize');
   $out .= "&nbsp; <input  id='mg_loadingButton' type='image' src='" . esc_url( $load ) . "'></div>";

    return $out;  
  } 
   
  public function migla_get_select_values_postid() 
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
   
  function migla_hidden_expressCheckout_form( $id ) {
	$paypalEmail = get_option( 'migla_paypal_emails' );
  
 	$payPalServer = get_option('migla_payment');
	 if ($payPalServer == "sandbox")
	 {
 		$formAction = "https://api-3t.sandbox.paypal.com/nvp";
	 }else{
		 $formAction = "https://api-3t.paypal.com/nvp";
	 }

	$return_url	= home_url('index.php') . "?migla_listener_setexpresscheckout=migla_sec_success&"; 
	$cancel_url	= home_url('index.php') . "?migla_listener_setexpresscheckout=migla_sec_cancel&"; 

	$currency_code = get_option( 'migla_default_currency' );
	
    //paypal pro code
    $paypal_username 	= get_option('migla_paypalpro_username'); 
    $paypal_password 	= get_option('migla_paypalpro_password'); 
    $paypal_signature 	= get_option('migla_paypalpro_signature');	
	
	$output = "<form method='post' action=' ".$formAction."' >
				<input type='hidden' name='USER' value='". $paypal_username."' />
				<input type='hidden' name='PWD' value='".$paypal_password ."' />
				<input type='hidden' name='SIGNATURE' value='".$paypal_signature."' />
				<input type='hidden' name='VERSION' value='76.0' />
				<input type='hidden' name='PAYMENTACTION' value='Sale' />
				<input type='hidden' name='AMT' value='10' />
				<input type='hidden' name='RETURNURL' value='".$return_url."' />
				<input type='hidden' name='CANCELURL' value='".$cancel_url."' />
				<input type='submit' id='submitBtn' name='METHOD' value='SetExpressCheckout' />
				</form>";

	return $output;
	
  }
      
} // END OF CLASS

?>