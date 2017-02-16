<?php
class migla_customize_theme_class {

	function __construct() {
		add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 12 );
	}
	
	function menu_item() {
		add_submenu_page(
			'migla_donation_menu_page',
			__( 'Customize Theme', 'migla-donation' ),
			__( 'Customize Theme', 'migla-donation' ),
			$this->get_capability() ,
			'migla_donation_custom_theme',
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
	

function migla_circle_js_shortcode( $id )
{
        $output = '';
        $output .= "<script type='text/javascript'>";
        $output .= "jQuery(document).ready( function() { ";
        		
        $output .= "var _reverse".$id . " = false ;";
        $output .= "if( jQuery('#mg_circle_wrap".$id."').find('.migla_circle_reverse').val() == 'yes' ){ _reverse".$id. " = true ; } ";

        $output .= "var _startangle".$id . " = ( Number(jQuery('#mg_circle_wrap".$id."').find('.migla_circle_start_angle').val()) / 180) * 3.14 ;";

        $output .= "if( jQuery('#mg_circle_wrap".$id."').find('.migla_circle_animation').val() == 'normal' ){";    

        $output .= "	jQuery('#mg_circle_".$id."').circleProgress({
				 value       : Number(jQuery('#mg_circle_wrap".$id."').find('.migla_circle_value').val()),
				 size        : jQuery('#mg_circle_wrap".$id."').find('.migla_circle_size').val(),
				 fill        : {
							   color: jQuery('#mg_circle_wrap".$id."').find('.migla_circle_fill').val()
					        },
				 lineCap     : jQuery('#mg_circle_wrap".$id."').find('.migla_circle_line_cap').val(),
				 startAngle  : _startangle".$id . ",
				 reverse     : _reverse".$id . ",
				 thickness   : jQuery('#mg_circle_wrap".$id."').find('.migla_circle_thickness').val()
			  }).on('circle-animation-progress', function(event, progress, stepValue)  {
                                  
				  if( jQuery('#mg_circle_wrap".$id."').find('.migla_circle_inside').val() == 'progress' )
				  {
                      jQuery('#mg_circle_wrap".$id."').find('.migla_circle_text').html(parseInt(100 * progress) + '<i>%</i>');					 
				  }else if ( jQuery('#mg_circle_wrap".$id."').find('.migla_circle_inside').val() == 'percentage' )
				  {
					  jQuery('#mg_circle_wrap".$id."').find('.migla_circle_text').html(jQuery('#mg_circle_wrap".$id."').find('.migla_circle_percentage').val() + '<i>%</i>'); 
				  }
                                  
			  });";

        $output .= "}else if( jQuery('#mg_circle_wrap".$id."').find('.migla_circle_animation').val() == 'back_forth' ) {";

        $output .= "	jQuery('#mg_circle_".$id."').circleProgress({
				 value       : Number(jQuery('#mg_circle_wrap".$id."').find('.migla_circle_value').val()),
				 size        : jQuery('#mg_circle_wrap".$id."').find('.migla_circle_size').val(),
				 fill        : {
							   color: jQuery('#mg_circle_wrap".$id."').find('.migla_circle_fill').val()
					        },
				 lineCap     : jQuery('#mg_circle_wrap".$id."').find('.migla_circle_line_cap').val(),
				 startAngle  : _startangle".$id . ",
				 reverse     : _reverse".$id . ",
				 thickness   : jQuery('#mg_circle_wrap".$id."').find('.migla_circle_thickness').val()
			  }).on('circle-animation-progress', function(event, progress, stepValue) 
                          {
						  
				  if( jQuery('#mg_circle_wrap".$id."').find('.migla_circle_inside').val() == 'progress' )
				  {
                      jQuery('#mg_circle_wrap".$id."').find('.migla_circle_text').html(parseInt(100 * progress) + '<i>%</i>');					 
				  }else if ( jQuery('#mg_circle_wrap".$id."').find('.migla_circle_inside').val() == 'percentage' )
				  {
					  jQuery('#mg_circle_wrap".$id."').find('.migla_circle_text').html(jQuery('#mg_circle_wrap".$id."').find('.migla_circle_percentage').val() + '<i>%</i>'); 
				  }
                                  
			  });";

        $output .= "			   setTimeout(function() { 
                                       jQuery('#mg_circle_".$id."').circleProgress('value', 0.7); 
                           }, 1000);
			   setTimeout(function() { jQuery('#mg_circle_".$id."').circleProgress('value', 1.0); }, 1100);
 			   setTimeout(function() { jQuery('#mg_circle_".$id."').circleProgress('value', jQuery('#mg_circle_wrap".$id."').find('.migla_circle_value').val()); }, 2100);";

        $output .= "}else{";


        $output .= "	jQuery('#mg_circle_".$id."').circleProgress({
				 value       : Number(jQuery('#mg_circle_wrap".$id."').find('.migla_circle_value').val()),
				 size        : jQuery('#mg_circle_wrap".$id."').find('.migla_circle_size').val(),
				 fill        : {
							   color: jQuery('#mg_circle_wrap".$id."').find('.migla_circle_fill').val()
					        },
				 lineCap     : jQuery('#mg_circle_wrap".$id."').find('.migla_circle_line_cap').val(),
				 startAngle  : _startangle".$id . ",
				 reverse     : _reverse".$id .",
				 thickness   : jQuery('#mg_circle_wrap".$id."').find('.migla_circle_thickness').val(),
                                 animation   : false
			  }).on('circle-animation-progress', function(event, progress, stepValue) 
                          {
				  if( jQuery('#mg_circle_wrap".$id."').find('.migla_circle_inside').val() == 'progress' )
				  {
                      jQuery('#mg_circle_wrap".$id."').find('.migla_circle_text').html(parseInt(100 * progress) + '<i>%</i>');					 
				  }else if ( jQuery('#mg_circle_wrap".$id."').find('.migla_circle_inside').val() == 'percentage' )
				  {
					  jQuery('#mg_circle_wrap".$id."').find('.migla_circle_text').html(jQuery('#mg_circle_wrap".$id."').find('.migla_circle_percentage').val() + '<i>%</i>'); 
				  }
                                 
			  });";

        $output .= "}";
		$output .= "jQuery('#mg_circle_wrap".$id."').find('.migla_circle_text').css('line-height', (jQuery('#mg_circle_wrap".$id."').find('.migla_circle_size').val()+'px'));";
		
		/*
		$output .= "var witdh".$id." = parseInt(jQuery('#mg_inpage_box_".$id."').css('width').replace('px', '')) ;";
		$output .= "var w_circle".$id." = parseInt(jQuery('#mg_circle_wrap".$id."').css('width').replace('px', ''))  ;";	
        $output .= "var w_html".$id." = ( ( ( ( witdh".$id." - w_circle".$id." ) / (1.5 * witdh".$id.") ) * 100 ) ) ;";		
		
		$output .= "jQuery('#mg_chtml_".$id."').css( 'width',  (w_html".$id."+'%') ) ;";
		*/
        $output .= "}); ";

        $output .= "</script>";

     return $output;
}
	
	
function preview_circle_section(){

echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'>";
echo "<a aria-expanded='true' href='#collapseCP' data-parent='.panel' data-toggle='collapse' class='fa fa-caret-down'></a></div>";
echo "<h2 class='panel-title'><div class='dashicons dashicons-admin-appearance'></div>Circle Preview</h2></header>";
echo "<div class='panel-body collapse in' id='collapseCP'>";

	$output = "";

  	$total_amount 	= 0; 
	$target_amount 	= 0; 
	$percent 		= 0; 
    $total 			= 500;
    $target			= 1000; 
	$donor			= 199;
	$id				= rand();

			$symbol = getCurrencySymbol2();
			$x = array();
			$x[0] = get_option('migla_thousandSep');
			$x[1] = get_option('migla_decimalSep');
			$before = ''; $after = '';

			if( strtolower(get_option('migla_curplacement')) == 'before' ){
				$before = $symbol;
			}else{
				$after = $symbol;		
			}
			
			$showSep = get_option('migla_showDecimalSep');
			$decSep = 0;
			if( strcmp($showSep , "yes") == 0 ){ $decSep = 2; }

			$total_amount  = $before. number_format( $total , $decSep, $x[1], $x[0]). $after;
			$target_amount = $before. number_format( $target , $decSep, $x[1], $x[0]) .$after;
			$percentStr = $percent . "%";

			$output .= "<div class='bootstrap-wrapper mg_inpage_circle_box clearfix' id='mg_inpage_box_".$id."'>";

			$output .= $this->migla_circle_js_shortcode( $id );
					  			   
			$output .= "<div class='migla_inpage_circle_wrapper' id='mg_circle_wrap".$id."' style='display: table;margin: 0 auto 0 !important;float: none;'>";
			$output .= "<div id='mg_circle_" . $id . "' class='migla_inpage_circle_bar' ";
				
			$align = get_option('migla_circle_textalign');
			
				if( $align == 'mg_left-right' ){
					  $output .= "style='float:left !important;margin-right:40px !important;'";
				}else if( $align == 'mg_right-left' ){
					  $output .= "style='float:right !important;margin-left:40px !important;'";
				}else if( $align == 'mg_left-left' ){
					  $output .= "style='float:left !important;margin-right:40px !important;'";	
				}else if( $align == 'mg_right-right' ){
					  $output .= "style='float:right !important;margin-left:40px !important;'";			  
				}else{
					  $output .= "";
				}				
			$output .= ">";
			$output .= "<span class='migla_circle_text' style='font-size:23px !important' ";
			$output .= "></span></div>";			
				  
			//Circle
			$output .= "<input type='hidden' class='migla_circle_id' value='".$id."' >";
			$output .= "<input type='hidden' class='migla_circle_value' value='".($total / $target)."' >";
			$output .= "<input type='hidden' class='migla_circle_percentage' value='". number_format(  ( $total / $target) * 100 , 2) ."' >";
					
			$circle_settings = get_option( 'migla_circle_settings' );				  
			$keys = array_keys($circle_settings[0]);
			foreach( $keys as $key  )
			{   
				$output .= "<input type='hidden' class='migla_circle_" . $key. "' value='" .$circle_settings[0][$key]. "'>";
			}
						
			  $output .= "<div class='mg_text-barometer' ";
		  
			  if( $align == 'mg_left-right' )
			  {
				  $output .= "style='float:right !important;margin-left:00px;text-align:right !important'";
			  }else if( $align == 'mg_right-left' )
			  {
				  $output .= "style='float:left !important;margin-right:40px;text-align:left !important'";
			  }else if( $align == 'mg_left-left' )
			  {
				  $output .= "style='float:right !important;margin-left:40px;text-align:left !important'";	
			  }else if( $align == 'mg_right-right' )
			  {
				  $output .= "style='float:left !important;margin-right:0px;text-align:right !important'";				  
			  }else{
				  $output .= "";
			  }

			  $output .= ">";
			
			$text1 	= get_option( 'migla_circle_text1' );
			$text2 	= get_option( 'migla_circle_text2' );
			$text3 	= get_option( 'migla_circle_text3' );
		 
          $output .= "<ul>
                      <li class='mg_inpage_campaign-raised'>
                      <span class='mg_inpage_current'>Amount</span> 
                      <span class='mg_inpage_current-amount'>".$total_amount."</span>
                      </li>
                      <li class='mg_inpage_campaign-goal'>
                      <span class='mg_inpage_target'>Target</span>
                      <span class='mg_inpage_target-amount'>".$target_amount."</span>  
                      </li>
                      <li class='mg_inpage_campaign-backers'>
                      <span class='mg_inpage_backers'>Backers</span>
                      <span class='mg_inpage_backers-amount'>".$donor."</span>  
                     </li>  
                  </ul>
               </div>";	

		$output .= "</div>";
		$output .= "</div>";
		$output .= "</div>";
  
  $output .= "</div></section>";
  
  echo $output;

}
	
function migla_circle_settings(){

echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'>";
echo "<a aria-expanded='true' href='#collapseFour' data-parent='.panel' data-toggle='collapse' class='fa fa-caret-down'></a></div>";
echo "<h2 class='panel-title'><div class='dashicons dashicons-admin-appearance'></div>Circle Progress Bar</h2></header>";
echo "<div class='panel-body collapse in' id='collapseFour'>";

/********* Circle Settings *************/
   $circle = get_option( 'migla_circle_settings');
	 
     if( $circle == false || $circle[0] == '' )
	 {
        $circle[0]['size'] = 100; 
        $circle[0]['start_angle'] = 0; 
        $circle[0]['thickness'] = 10; 
        $circle[0]['reverse'] = 'yes'; 
        $circle[0]['line_cap'] = 'but';
        $circle[0]['fill'] = '#00ff00';
        //$circle[0]['empty_fill'] = '#777777';
        $circle[0]['animation'] = 'none';
        $circle[0]['inside'] = 'none';
        update_option( 'migla_circle_settings', $circle );
     }

     if( $circle[0]['size'] == '' ) {
           $circle[0]['size'] = 100;
     } 
     if( $circle[0]['start_angle'] == '' ) {
        $circle[0]['start_angle'] = 0; 
     }
     if( $circle[0]['thickness'] == '' ) {
        $circle[0]['thickness'] = 10;
     }
     if( $circle[0]['reverse'] == '' ) { 
        $circle[0]['reverse'] = 'yes';
     }
     if( $circle[0]['line_cap'] == '' ) { 
        $circle[0]['line_cap'] = 'butt';
     }
     if( $circle[0]['fill'] == '' ) {
        $circle[0]['fill'] = '#00ff00';
     }
/*
     if( $circle[0]['empty_fill'] == '' ) {
        $circle[0]['empty_fill'] = '#777777';
     }
*/
     if( $circle[0]['animation'] == '' ) {
        $circle[0]['animation'] = 'none';
     }
     if( $circle[0]['inside'] == '' ) {
        $circle[0]['inside'] = 'none';
     }

	$text1 	= get_option( 'migla_circle_text1' );
	$text2 	= get_option( 'migla_circle_text2' );
	$text3 	= get_option( 'migla_circle_text3' );
 

/*********** binti's edit **********************/

echo "<div class='row mg_edit_circle'>";
echo "<div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs'>".__('Circle Text Info Alignment :', 'localization')."</label></div>";  

	$text_align = get_option( 'migla_circle_textalign');
	if( $text_align == '' || $text_align == false  ){
		$text_align = 'left_right';
	}else{
		$text_align = $text_align;
    }
	 
	 $align = array();
	 $align['mg_no_text'] = '';
	 $align['mg_left-right'] = '';
	 $align['mg_left-left'] = '';
	 $align['mg_right-left'] = '';
	 $align['mg_right-right'] = '';
	 
	 $align[$text_align] = 'checked';

echo "<div class='col-sm-6  col-xs-12'><div class='mg_layout-selector'>";

echo "<label class='mg_box'><img alt='No Text' src='".plugins_url('/images/circle-no-html-1.png', dirname(__FILE__) )."' /><br> ";
echo "<input ".$align['mg_no_text']." type='radio' value='mg_no_text' id='mg_no_text' name='mg_circle-text-align'></label>";

echo "<label class='mg_box'><img alt='align left with right text' src='".plugins_url('/images/circle-left-text-right.png', dirname(__FILE__) )."' /><br>";
echo "<input ".$align['mg_left-right']." type='radio' value='mg_left-right' id='mg_left-right' name='mg_circle-text-align'></label>";

echo "<label class='mg_box'><img alt='align left with left text' src='".plugins_url('/images/circle-left-text-left.png', dirname(__FILE__) )."' /><br>";
echo "<input ".$align['mg_left-left']." type='radio' value='mg_left-left' id='mg_left-left' name='mg_circle-text-align'></label>";

echo "<label class='mg_box'><img alt='alight right with left text' src='".plugins_url('/images/circle-right-text-left.png', dirname(__FILE__) )."' /><br>";
echo "<input ".$align['mg_right-left']." type='radio' value='mg_right-left' id='mg_right-left' name='mg_circle-text-align'></label>";

echo "<label class='mg_box'><img alt='>align right with right text' src='".plugins_url('/images/circle-right-text-right.png', dirname(__FILE__) )."' /><br>";
echo "<input ".$align['mg_right-right']." type='radio' value='mg_right-right' id='mg_right-right' name='mg_circle-text-align'></label>";

echo "</div></div></div>";  

/*********** End binti's edit **********************/

	if( $align['mg_no_text'] == 'checked' ){
		echo "<div class='row mg_edit_circle' id='mg_text_barometer_input' style='display:none'>";
	}else{
		echo "<div class='row mg_edit_circle' id='mg_text_barometer_input'>";	
	}
    echo "<div class='col-sm-3  col-xs-12'>";  
	echo "<label for='migla_circle_text1' class='control-label text-right-sm text-center-xs'>".__('Text Info :', 'localization')."</label></div>" ;
	echo "<div class='col-sm-2 col-xs-12'>"; 
	echo "<input id='migla_circle_text1' placeholder='current' type='text' value='".$text1."'>";
    echo "</div>";
	echo "<div class='col-sm-2 col-xs-12'>"; 
	echo "<input id='migla_circle_text2' placeholder='target' type='text' value='".$text2."'>";
    echo "</div>";
	echo "<div class='col-sm-2 col-xs-12'>"; 
	echo "<input id='migla_circle_text3' placeholder='total donors' type='text' value='".$text3."'>";
    echo "</div>";	


    echo "</div>";





/*********** End Binti's Edit ************/

    



echo "<div class='row'><div class='col-sm-12 center-button'><button value='save' class='btn btn-info pbutton' id='miglaSaveCircleLayout'><i class='fa fa-fw fa-save'></i> save</button></div></div>";	

echo "</div>";

echo "</section>";








echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'>";
echo "<a aria-expanded='true' href='#collapseFive' data-parent='.panel' data-toggle='collapse' class='fa fa-caret-down'></a></div>";
echo "<h2 class='panel-title'><div class='dashicons dashicons-admin-appearance'></div>Circle Progress Bar Editor</h2></header>";
echo "<div class='panel-body collapse in' id='collapseFive'>";



















                echo "<div class='row mg_edit_circle'>";
		echo "<div class='col-sm-3  col-xs-12'><label for='migla_circle_size' class='control-label text-right-sm text-center-xs'>".__("Size:","migla-donation")."</label></div>";

echo "<div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>
   <div data-plugin-spinner='' data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }'>
														<div class='input-group' style=''>";
												
			
echo "<input id='migla_circle_size' type='text' class='spinner-input form-control' maxlength='3' value='".$circle[0]['size']."'>
     <div  class='spinner-buttons input-group-btn'>
    <button id='migla_circle_size_spinner_up' type='button' class='btn btn-default spinner-up'>
    <i class='fa fa-angle-up'></i>
																</button>
<button type='button' id='migla_circle_size_spinner_down' class='btn btn-default spinner-down' >
																	<i class='fa fa-angle-down'></i>
																</button>
															</div>
														</div>
															
		</div>
		
		</div><div class='col-sm-1 col-xs-12 text-left-sm text-center-xs'><label for='migla_circle_thickness' class='control-label text-right-sm text-center-xs'>".__("Thickness:","migla-donation")."</label></div>
   <div class='col-sm-2' data-plugin-spinner='' data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }'>
														<div class='input-group' style=''>";
												
			
echo "<input id='migla_circle_thickness' type='text' class='spinner-input form-control' maxlength='3' value='".$circle[0]['thickness']."' >
     <div  class='spinner-buttons input-group-btn'>
    <button type='button' class='btn btn-default spinner-up' id='migla_circle_thickness_spinner_up'>
    <i class='fa fa-angle-up'></i>
																</button>
	<button type='button' class='btn btn-default spinner-down' id='migla_circle_thickness_spinner_down'>
																	<i class='fa fa-angle-down'></i>
																</button>
															</div> 
														</div> 
															
		</div> 

<div class='col-sm-3  col-xs-12'><button id='migla_sizethick_save' name='migla_sizethick_save' class='btn btn-info pbutton msave migla_circle_settings' value='save'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div>
		
		</div>";


                echo "<div class='row mg_edit_circle'>";
		
	        echo "<div class='col-sm-3  col-xs-12'><label for='migla_circle_start_angle' class='control-label text-right-sm text-center-xs'>".__("Start Angle:","migla-donation")."</label></div>";

echo "<div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>
   <div data-plugin-spinner='' data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }'>
														<div class='input-group' style=''>";
												
			
echo "<input id='migla_circle_start_angle' type='text' class='spinner-input form-control' maxlength='3' value='".$circle[0]['start_angle']."' >
     <div  class='spinner-buttons input-group-btn'>
    <button type='button' class='btn btn-default spinner-up' id='migla_circle_angle_spinner_up'>
    <i class='fa fa-angle-up'></i>
																</button>
	<button type='button' class='btn btn-default spinner-down' id='migla_circle_angle_spinner_down'>
																	<i class='fa fa-angle-down'></i>
																</button>
															</div>
														</div>
															
		</div>
		
		</div>";

        echo "<div class='col-sm-1  col-xs-12'><label for='migla_circle_reverse' class='control-label text-right-sm text-center-xs'>".__("Reverse:","migla-donation")."</label></div>";
                if( $circle[0]['reverse'] == "yes" )
                {
  	          echo "<div class='col-sm-2  col-xs-12'><label for='migla_circle_reverse'><input type='checkbox' class='' id='migla_circle_reverse' checked />".__("This reverses the direction of the circle","migla-donation")."</label></div>";
                }else{
  	          echo "<div class='col-sm-2  col-xs-12'><label for='migla_circle_reverse'><input type='checkbox' class='' id='migla_circle_reverse' />".__("This reverses the direction of the circle","migla-donation")."</label></div>";
                }

                echo "<div class='col-sm-3  col-xs-12'><button id='migla_startreverse_save' name='migla_startreverse_save' class='btn btn-info pbutton msave migla_circle_settings' value='save'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div>";
                echo "</div> ";


echo "<div class='row mg_edit_circle'>";
echo "<input type='hidden' class='rgba_value' value=''>";
		echo "<div class='col-sm-3  col-xs-12'><label for='migla_circle_fill' class='control-label text-right-sm text-center-xs'>".__('Fill:','migla-donation')." </label></div><div class='col-sm-6 col-xs-12'>
		 <span class='input-group'><span class='input-group-addon '><button class='currentColor' style='background-color:".$circle[0]['fill'].";'></button></span><input type='text' class='form-control mg-color-field' data-opacity='1.0' value='".$circle[0]['fill']."' id='migla_circle_fill'></span></div>";
		echo "<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button id='migla_fill_save' name='migla_fill_save' class='migla_circle_settings btn btn-info pbutton msave' value='save'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button> </div>";
		echo "</div>";


echo "<div class='row mg_edit_circle'>";
echo "<input type='hidden' class='' value=''>";
		echo "<div class='col-sm-3  col-xs-12'><label for='migla_circle_inner_font_size' class='control-label text-right-sm text-center-xs'>".__('Inner Font Size:','migla-donation')." </label></div><div class='col-sm-6 col-xs-12'>
		 

<div data-plugin-spinner='' data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }'>
														<div class='input-group' style=''>";
												
			
echo "<input id='migla_circle_inner_font_size' type='text' class='spinner-input form-control' maxlength='3' value='".$circle[0]['inner_font_size']."' >
     <div  class='spinner-buttons input-group-btn'>
    <button type='button' class='btn btn-default spinner-up' id='migla_inner_font_size_spinner_up'>
    <i class='fa fa-angle-up'></i>
																</button>
	<button type='button' class='btn btn-default spinner-down' id='migla_inner_font_size_spinner_down'>
																	<i class='fa fa-angle-down'></i>
																</button>
															</div>
														</div></div></div>";




		echo "<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button id='migla_font_save' name='migla_font_save' class='migla_circle_settings btn btn-info pbutton msave' value='save'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button> </div>";
		echo "</div>";






                echo "<div class='row mg_edit_circle'>";
                echo "<div class='col-sm-3  col-xs-12'><label for='migla_circle_line_cap' class='control-label text-right-sm text-center-xs'>".__("Line Cap:","migla-donation")."</label></div>";
                if( $circle[0]['line_cap'] == 'butt' ){
	          echo "<div class='col-sm-6  col-xs-12'><select id='migla_circle_line_cap'><option value='butt' selected>".__("Square","migla-donation")."</option><option value='round'>".__("Round","migla-donation")."</option></select></div>";
                }else{
	          echo "<div class='col-sm-6  col-xs-12'><select id='migla_circle_line_cap'><option value='butt'>".__("Square","migla-donation")."</option><option value='round' selected>".__("Round","migla-donation")."</option></select></div>";
                }
                echo "<div class='col-sm-3  col-xs-12'><button id='migla_linecap_save' name='migla_linecap_save' class='migla_circle_settings btn btn-info pbutton msave' value='save'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div></div>";

                echo "<div class='row mg_edit_circle'>";
                echo "<div class='col-sm-3  col-xs-12'><label for='migla_circle_animation' class='control-label text-right-sm text-center-xs'>".__("Animation:","migla-donation")."</label></div>";
                if( $circle[0]['animation'] == 'none' ){
	           echo "<div class='col-sm-6  col-xs-12'><select id='migla_circle_animation'>";
                   echo "<option value='none' selected>".__("None","migla-donation")."</option><option value='normal'>".__("Normal","migla-donation")."</option><option value='back_forth'>".__("Back and Forth","migla-donation")."</option>";
                   echo "</select></div>";
                }else if( $circle[0]['animation'] == 'normal' ){
	           echo "<div class='col-sm-6  col-xs-12'><select id='migla_circle_animation'>";
                   echo "<option value='none'>".__("None","migla-donation")."</option><option value='normal' selected>".__("Normal","migla-donation")."</option><option value='back_forth'>".__("Back and Forth","migla-donation")."</option>";
                   echo "</select></div>";
                }else{
	           echo "<div class='col-sm-6  col-xs-12'><select id='migla_circle_animation'>";
                   echo "<option value='none'>".__("None","migla-donation")."</option><option value='normal'>".__("Normal","migla-donation")."</option><option value='back_forth' selected>".__("Back and Forth","migla-donation")."</option>";
                   echo "</select></div>";
                }
                echo "<div class='col-sm-3  col-xs-12'><button id='migla_animation_save' name='migla_animation_save' class='migla_circle_settings btn btn-info pbutton msave' value='save'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div></div>";
              
                echo "<div class='row mg_edit_circle'>";
                echo "<div class='col-sm-3  col-xs-12'><label for='migla_circle_inside' class='control-label text-right-sm text-center-xs'>".__("Inner Value:","migla-donation")."</label></div>";
                if( $circle[0]['inside'] == 'none' ){
	           echo "<div class='col-sm-6  col-xs-12'><select id='migla_circle_inside'>";
                   echo "<option value='none' selected>".__("None","migla-donation")."</option><option value='percentage'>".__("Donation Percentage","migla-donation")."</option>";
                   echo "</select></div>";
                }else if( $circle[0]['inside'] == 'progress' ){
	           echo "<div class='col-sm-6  col-xs-12'><select id='migla_circle_inside'>";
                   echo "<option value='none' >".__("None","migla-donation")."</option><option value='percentage'>".__("Donation Percentage","migla-donation")."</option>";
                   echo "</select></div>";
                }else{
	           echo "<div class='col-sm-6  col-xs-12'><select id='migla_circle_inside'>";
                   echo "<option value='none' >".__("None","migla-donation")."</option><option value='percentage' selected>".__("Donation Percentage","migla-donation")."</option>";
                   echo "</select></div>";
                }
                echo "</div><div class='row'><div class='col-sm-12  center-button'><button id='migla_save_circle_settings' class='btn btn-info pbutton msave' value='save'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div>";   
	
	echo "</div></section>";	
}
	
function hex2RGB($hex) 
{
        preg_match("/^#{0,1}([0-9a-f]{1,6})$/i",$hex,$match);
        if(!isset($match[1]))
        {
            return false;
        }

        if(strlen($match[1]) == 6)
        {
            list($r, $g, $b) = array($hex[0].$hex[1],$hex[2].$hex[3],$hex[4].$hex[5]);
        }
        elseif(strlen($match[1]) == 3)
        {
            list($r, $g, $b) = array($hex[0].$hex[0],$hex[1].$hex[1],$hex[2].$hex[2]);
        }
        else if(strlen($match[1]) == 2)
        {
            list($r, $g, $b) = array($hex[0].$hex[1],$hex[0].$hex[1],$hex[0].$hex[1]);
        }
        else if(strlen($match[1]) == 1)
        {
            list($r, $g, $b) = array($hex.$hex,$hex.$hex,$hex.$hex);
        }
        else
        {
            return false;
        }

        $color = array();
        $color['r'] = hexdec($r);
        $color['g'] = hexdec($g);
        $color['b'] = hexdec($b);

        return $color;
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
 
                echo "<h2 class='migla'>". __("Theme Customization","migla-donation")."</h2>";
		echo "<div class='row form-horizontal'>";
		echo "<div class='col-xs-12'>";
                  
		//FORM
                $bgcolor2 = explode(",", get_option('migla_2ndbgcolor'));
                $bgcborder = explode(",", get_option('migla_2ndbgcolorb') );
                

echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseOne' aria-expanded='true'></a></div><h2 class='panel-title'><div class='dashicons dashicons-admin-appearance'></div>". __('Customize the Form','migla-donation')."<span class='panel-subtitle'> ". __('Add your color/width or leave blank','migla-donation')."</span></h2></header>";

echo "<div id='collapseOne' class='panel-body collapse in'>";
		


// Secondary Options
echo "<div class='row'>";

//echo "<input type='hidden' class='rgba_value' value='".$bgcolor2[0].",".$bgcolor2[1]."'>";
		echo "<div class='col-sm-3  col-xs-12'><label for='migla_backgroundcolor' class='control-label text-right-sm text-center-xs'>".__('Panel Background:','migla-donation')." </label></div><div class='col-sm-6 col-xs-12'>
		 <span class='input-group'><span class='input-group-addon '><button class='currentColor' style='background-color:".$bgcolor2[0].";'></button></span><input type='text' class='form-control mg-color-field' data-opacity='".$bgcolor2[1]."' value='".$bgcolor2[0]."' id='migla_backgroundcolor'></span></div>";
		
		echo "<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div>";

		echo "<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>".__("This is the background color of the panel in the form. Default is grey.","migla-donation")." </span></div>";




echo "<div class='row'>";

echo "<input type='hidden' class='rgba_value' value='".$bgcborder[0].",".$bgcborder[1]."'>";

echo "<div class='col-sm-3  col-xs-12'><label for='migla_panelborder'  class='control-label text-right-sm text-center-xs'>".__("Panel Border","migla-donation")."</label></div><div class='col-sm-3 col-xs-12'>
		
		
		 <span class='input-group'><span class='input-group-addon '><button style='background-color:".$bgcborder[0]."' class='currentColor'></button></span>";
		 echo "<input type='text' class='form-control mg-color-field' data-opacity='".$bgcborder[1]."' autocomplete='off' style='background-image: none;' value='".$bgcborder[0]."' id='migla_panelborder'></span></div>

<div class='col-sm-1 col-xs-12'>
  <label for='migla_widthpanelborder' class='control-label  text-right-sm text-center-xs'>".__("Width","migla-donation")." </label>
		
		</div>

<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
   <div data-plugin-spinner='' data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }'>
														<div class='input-group' style=''>";
												
			
echo "<input id='migla_widthpanelborder' type='text' class='spinner-input form-control' maxlength='2' value='".$bgcborder[2]."' >
     <div  class='spinner-buttons input-group-btn'>
    <button type='button' class='btn btn-default spinner-up' id='migla_widthpanelborderspinner-up'>
    <i class='fa fa-angle-up'></i>
																</button>
																<button type='button' class='btn btn-default spinner-down' id='migla_widthpanelborderspinner-down'>
																	<i class='fa fa-angle-down'></i>
																</button>
															</div>
														</div>
															
		</div>
		
		</div><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div><span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>
".__(" This is the panel's border color and width in the form.","migla-donation")."</span></div>";



///////////////// Donor level boxes////////////////////////////

  $levelcolor = get_option('migla_bglevelcolor');
  $activelevelcolor = get_option('migla_bglevelcoloractive');
  
// Secondary Options
echo "<div class='row'>";

echo "<input type='hidden' class='rgba_value' value='".$levelcolor."'>";

		echo "<div class='col-sm-3  col-xs-12'><label for='migla_bglevelcolor' class='control-label text-right-sm text-center-xs'>
".__("Giving Level Background:","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'>
		 <span class='input-group'><span class='input-group-addon '><button class='currentColor' style='background-color:". $levelcolor.";'></button></span><input type='text' class='form-control mg-color-field' data-opacity='' value='". $levelcolor."' id='migla_bglevelcolor' name='migla_bglevelcolor'></span></div>";
		
		echo "<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div>";

echo "<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>".__("  This is the background color of the suggested giving level.","migla-donation")."</span></div>";


  $borderlevelcolor = get_option('migla_borderlevelcolor');
  $borderlevel = get_option('migla_borderlevel');

echo "<div class='row'>";

echo "<input type='hidden' class='rgba_value' value='".$borderlevelcolor ."'>";

echo "<div class='col-sm-3  col-xs-12'><label for='migla_borderlevelcolor' class='control-label text-right-sm text-center-xs'>".__("Giving Level Border","migla-donation")."</label></div><div class='col-sm-3 col-xs-12'>
		
		
		 <span class='input-group'><span class='input-group-addon '><button style='background-color:".$borderlevelcolor ."' class='currentColor'></button></span><input type='text' class='form-control mg-color-field' data-opacity='' autocomplete='off' style='background-image: none;' value='".$borderlevelcolor."' id='migla_borderlevelcolor' name='migla_borderlevelcolor'></span></div>

<div class='col-sm-1 col-xs-12'>
  <label for='migla_Widthborderlevelcolor' class='control-label  text-right-sm text-center-xs'>".__("Width","migla-donation")."</label>
		
		</div>

<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
   <div data-plugin-spinner='' data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }'>
														<div class='input-group' style=''>";
												
			
echo "<input type='text' id='migla_Widthborderlevelcolor' class='spinner-input form-control' maxlength='2' value='".$borderlevel."' >
     <div class='spinner-buttons input-group-btn'>
    <button type='button' class='btn btn-default spinner-up' id='migla_Widthborderlevelcolorspinner-up'>
    <i class='fa fa-angle-up'></i>
																</button>
																<button type='button' class='btn btn-default spinner-down' id='migla_Widthborderlevelcolorspinner-down'>
																	<i class='fa fa-angle-down'></i>
																</button>
															</div>
														</div>
															
		</div>
		
		</div><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div><span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>".__("  This is the panel's border color and width for the suggested giving level. ","migla-donation")."</span></div>";





echo "<div class='row'>";

echo "<input type='hidden' class='rgba_value' value='".$activelevelcolor."'>";

		echo "<div class='col-sm-3  col-xs-12'><label for='migla_bglevelcoloractive' class='control-label text-right-sm text-center-xs'>
".__("Active Giving Level Background:","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'>
		 <span class='input-group'><span class='input-group-addon '><button class='currentColor' style='background-color:".$activelevelcolor.";'></button></span><input type='text' class='form-control mg-color-field' data-opacity='' value='".$activelevelcolor."' id='migla_bglevelcoloractive' name='migla_bglevelcoloractive'></span></div>";
		
		echo "<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div>";

echo "<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>".__("  This is the background color of the suggested giving level when it has been selected.","migla-donation")."<span style='color:#c7254e;'>".__(" Note: This only works with the button option. ","migla-donation")." </span></span></div>";










// Secondary Options
$tabcolor = get_option('migla_tabcolor');
//echo $tabcolor;

echo "<div class='row'>";

echo "<input type='hidden' class='rgba_value' value=''>";
		echo "<div class='col-sm-3  col-xs-12'><label for='migla_tabcolor' class='control-label text-right-sm text-center-xs'>".__('Inactive Tab Background:','migla-donation')." </label></div><div class='col-sm-6 col-xs-12'>
		 <span class='input-group'><span class='input-group-addon '><button class='currentColor' style='background-color:".$tabcolor."'></button></span><input type='text' class='form-control mg-color-field' data-opacity='' value='".$tabcolor."' id='migla_tabcolor'></span></div>";
		
		echo "<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div>";

		echo "<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>".__("This is the background color of the your inactive tab when using more than one gateway","migla-donation")." </span></div>";


echo "<div class='row'>";
echo "<div class='col-sm-12 center-button'><button id='migla_save_form' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div>";		
echo "</div>";


echo "</section></div>";
		
		echo "<div class='col-xs-12'>";

		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseTwo' aria-expanded='true'></a></div><h2 class='panel-title'><div class='dashicons dashicons-admin-appearance'></div>
".__(" Customize Progress Bar","migla-donation")."</h2></header>";
		echo "<div id='collapseTwo' class='panel-body collapse in'>";

$progbarInfo = get_option('migla_progbar_info');

		// BEFORE First Row for text
		echo "<div class='row'>";
		echo "<div class='col-sm-3  col-xs-12'><label for='migla_progressbar_text' class='control-label text-right-sm text-center-xs'>". __("Progress Bar info:","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'>
		<input type='textarea' id='migla_progressbar_text' class='form-control' value='".$progbarInfo."' cols='50' rows='2'></div>";
		echo "<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div>";
		echo "<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>  
".__(" This is the information that is above the progress bar. You can use the following shortcodes:","migla-donation")."<code>[total]</code><code>[target]</code><code>[campaign]</code><code>[percentage]</code><code>[remainder]</code> </span></div>";
					
                 //BAR
                $borderRadius = explode(",", get_option( 'migla_borderRadius' )); //4spinner
                $bar_color = explode(",", get_option( 'migla_bar_color' ));  //rgba
                $progressbar_bg = explode(",", get_option( 'migla_progressbar_background' )); //rgba
                $boxshadow_color = explode(",", get_option( 'migla_wellboxshadow' )); //rgba 4spinner 

echo"<div class='row '>
  
  <div class='form-group touching'>
  
  <div class='col-sm-3  col-xs-12'><label for='mg_WBRtop-left' class='control-label text-right-sm text-center-xs'>
".__("  Well Border Radius:","migla-donation")."</label></div>

  <div class='col-sm-1'>
  <label for='mg_WBRtop-left' class='control-label  text-right-sm text-center-xs'>
".__("top-left","migla-donation")."</label>
		
		</div>


<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
   <div data-plugin-spinner='' data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }'>
														<div class='input-group' style=''>
<input id='mg_WBRtop-left' type='text' class='spinner-input form-control' maxlength='2' name='topleft' value='".$borderRadius[0]."' id='migla_radiustopleft'>
															<div class='spinner-buttons input-group-btn'>
																<button type='button' class='btn btn-default spinner-up' id='mg_WBRtop-leftspinner-up'>
																	<i class='fa fa-angle-up'></i>
																</button>
																<button type='button' class='btn btn-default spinner-down' id='mg_WBRtop-leftspinner-down'>
																	<i class='fa fa-angle-down'></i>
																</button>
															</div>
														</div>
													
  
  
  
        

		
		</div>
		
		</div>



  <div class='col-sm-1'>
  <label for='migla_WRBtopright' class='control-label  text-right-sm text-center-xs'>".__("top-right","migla-donation")."</label>
		
		</div>


<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
   <div data-plugin-spinner='' data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }'>
														<div class='input-group' style=''>
	<input type='text' class='spinner-input form-control' maxlength='2' name='topright'  value='".$borderRadius[1]."' id='migla_WRBtopright'>
															<div class='spinner-buttons input-group-btn'>
																<button type='button' class='btn btn-default spinner-up' id='migla_WRBtoprightspinner-up'>
																	<i class='fa fa-angle-up'></i>
																</button>
																<button type='button' class='btn btn-default spinner-down' id='migla_WRBtoprightspinner-down'>
																	<i class='fa fa-angle-down'></i>
																</button>
															</div>
														</div>
													
  
  
  
        

		
		</div>
		
		</div>






    <div class='col-sm-3 hidden-xs'></div> </div>
  
  
  <div class='form-group touching'>





  <div class='col-sm-3  col-xs-12'></div>


<div class='col-sm-1'>
  <label for='migla_radiusbottomleft' class='control-label  text-right-sm text-center-xs'>".__("bottom-left","migla-donation")."</label>
		
		</div>


<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
   <div data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }' data-plugin-spinner=''>
														<div style='' class='input-group'>
  <input type='text' maxlength='2' class='spinner-input form-control' name='bottomleft'  value='".$borderRadius[2]."' id='migla_radiusbottomleft'>
															<div class='spinner-buttons input-group-btn'>
																<button class='btn btn-default spinner-up' type='button' id='migla_radiusbottomleftspinner-up'>
																	<i class='fa fa-angle-up'></i>
																</button>
																<button class='btn btn-default spinner-down' type='button' id='migla_radiusbottomleftspinner-down'>
																	<i class='fa fa-angle-down'></i>
																</button>
															</div>
														</div>
													
  
  
  
        

		
		</div>
		
		</div>



  <div class='col-sm-1'>
  <label for='migla_radiusbottomright' class='control-label  text-right-sm text-center-xs'>
".__("bottom-right","migla-donation")."</label>
		
		</div>


<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
   <div data-plugin-spinner='' data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }'>
														<div class='input-group' style=''>
  <input type='text' class='spinner-input form-control' maxlength='2' name='bottomright'  value='".$borderRadius[3]."' id='migla_radiusbottomright'>
															<div class='spinner-buttons input-group-btn'>
																<button type='button' class='btn btn-default spinner-up' id='migla_radiusbottomrightspinner-up'>
																	<i class='fa fa-angle-up'></i>
																</button>
																<button type='button' class='btn btn-default spinner-down' id='migla_radiusbottomrightspinner-down'>
																	<i class='fa fa-angle-down'></i>
																</button>
															</div></div>		
		</div></div><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div> <span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>".__("This controls the round corners of the bar.","migla-donation")."</span>
    
      </div>
</div>";


		
		// First Row
		
		
		
		
		
		echo "<div class='row'>";
echo "<input type='hidden' class='rgba_value' value='".$bar_color[0].",".$bar_color[1]."'>";
		echo "<div class='col-sm-3  col-xs-12'><label for='migla_barcolor' class='control-label text-right-sm text-center-xs'>".__("Bar Color:","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'>
<span class='input-group'><span class='input-group-addon'><button class='currentColor' style='background-color:".$bar_color[0].";'></button></span><input type='text' class='mg-color-field form-control' value='".$bar_color[0]."' data-opacity='".$bar_color[1]."' id='migla_barcolor'></span></div>";
		echo "<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div>";
		echo "<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>".__(" This is the color of the progress bar. ","migla-donation")." </span></div>";
		

// Second Row
		echo "<div class='row'>";
echo "<input type='hidden' class='rgba_value' value='".$progressbar_bg[0].",".$progressbar_bg[1]."'>";
		echo "<div class='col-sm-3  col-xs-12'><label for='migla_wellcolor' class='control-label text-right-sm text-center-xs'>".__("Well Background:","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'>
		<span class='input-group'><span class='input-group-addon'><button class='currentColor' style='background-color:".$progressbar_bg[0].";'></button></span><input type='text' class='mg-color-field form-control' value='".$progressbar_bg[0]."' data-opacity='".$progressbar_bg[1]."' id='migla_wellcolor'></span></div>";
		echo "<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div>";
		echo "<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>".__(" This is for the background inlay of the progress bar.","migla-donation")." </span></div>";


		
		echo "<div class='row '>";
echo "<input type='hidden' class='rgba_value' value='".$boxshadow_color[0].",".$boxshadow_color[1]."'>";
echo "<div class='form-group touching'>
    
    <div class='col-sm-3'><label for='migla_wellshadow' class='control-label text-right-sm text-center-xs'>".__("Well Box Shadow:","migla-donation")."</label></div>
  
  <div class='col-sm-6 col-xs-12'>
<span class='input-group'><span class='input-group-addon'><button style='background-color:".$boxshadow_color[0].";' class='currentColor'></button></span><input type='text' value='".$boxshadow_color[0]."' class='mg-color-field form-control' data-opacity='".$boxshadow_color[1]."' autocomplete='off' style='background-image: none;' id='migla_wellshadow'></span></div> <br>
    
     <div class='col-sm-3'></div>
    
    <br> <br>
    
  </div>
  
  <div class='form-group touching'>
  
  <div class='col-sm-3  col-xs-12'></div>

  <div class='col-sm-1'>
  <label for='migla_hshadow' class='control-label  text-right-sm text-center-xs'>".__("h-shadow","migla-donation")."</label>
		
		</div>


<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
   <div data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }' data-plugin-spinner=''>
														<div style='' class='input-group'><input type='text'  maxlength='2' class='spinner-input form-control' name='hshadow' value='".$boxshadow_color[2]."' id='migla_hshadow'>
															<div class='spinner-buttons input-group-btn'>
																<button class='btn btn-default spinner-up2' type='button' id='migla_hshadowspinner-up'>
																	<i class='fa fa-angle-up'></i>
																</button>
																<button class='btn btn-default spinner-down' type='button' id='migla_hshadowspinner-down'>
																	<i class='fa fa-angle-down'></i>
																</button>
															</div>
														</div>
													
  
  
  
        

		
		</div>
		
		</div>



  <div class='col-sm-1'>
  <label for='migla_vshadow' class='control-label  text-right-sm text-center-xs'>".__("v-shadow","migla-donation")."</label>
		
		</div>


<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
   <div data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }' data-plugin-spinner=''>
														<div style='' class='input-group'><input type='text'  maxlength='2' class='spinner-input form-control' name='vshadow' value='".$boxshadow_color[3]."' id='migla_vshadow'>
															<div class='spinner-buttons input-group-btn'>
																<button class='btn btn-default spinner-up2' type='button' id='migla_vshadow-spinner-up'>
																	<i class='fa fa-angle-up'></i>
																</button>
																<button class='btn btn-default spinner-down' type='button' id='migla_vshadow-spinner-down'>
																	<i class='fa fa-angle-down'></i>
																</button>
															</div>
														</div>
													
  
  
  
        

		
		</div>
		
		</div>






    <div class='col-sm-3 hidden-xs'></div> </div>
  
  
  <div class='form-group touching'>





  <div class='col-sm-3  col-xs-12'></div>


<div class='col-sm-1'>
  <label for='migla_blur' class='control-label  text-right-sm text-center-xs'>".__("Blur","migla-donation")."</label>
		
		</div>


<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
   <div data-plugin-spinner='' data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }'>
														<div class='input-group' style=''><input type='text' class='spinner-input form-control' maxlength='2' name='blur' value='".$boxshadow_color[4]."' id='migla_blur'>
															<div class='spinner-buttons input-group-btn'>
																<button type='button' class='btn btn-default spinner-up2' id='migla_blurspinner-up'>
																	<i class='fa fa-angle-up'></i>
																</button>
																<button type='button' class='btn btn-default spinner-down' id='migla_blurspinner-down'>
																	<i class='fa fa-angle-down'></i>
																</button>
															</div>
														</div>
													
  
  
  
        

		
		</div>
		
		</div>



  <div class='col-sm-1'>
  <label for='migla_spread' class='control-label  text-right-sm text-center-xs'>".__("Spread","migla-donation")."</label>
		
		</div>


<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
   <div data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }' data-plugin-spinner=''>
														<div style='' class='input-group'><input type='text'  maxlength='2' class='spinner-input form-control' name='spread' value='".$boxshadow_color[5]."' id='migla_spread'>
															<div class='spinner-buttons input-group-btn'>
																<button class='btn btn-default spinner-up2' type='button' id='migla_spreadspinner-up'>
																	<i class='fa fa-angle-up'></i>
																</button>
																<button class='btn btn-default spinner-down' type='button' id='migla_spreadspinner-down'>
																	<i class='fa fa-angle-down'></i>
																</button>
															</div>
														</div>
													
  
  
  
        

		
		</div>
		
		</div>


<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div>














            
        <span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'> ".__("This controls the inlay shadow.","migla-donation")."</span>
    
      </div>
</div>";










		
		
		// Fourth row
		$effect = (array)get_option( 'migla_bar_style_effect' );
                $check['yes'] = 'checked';
                $check['no'] = '';
		echo "<div class='row'>"; 
		echo "<div class='col-sm-3  col-xs-12'><label for='inlineCheckbox1' class='control-label text-right-sm text-center-xs'>".__("Bar Styling and Effects:","migla-donation")."</label></div>";
		echo "<div class='col-sm-6 col-xs-12'>
		
		
		
		<div class='list-group'>
		
		<label class='list-group-item border-check-control '>
		
		
		
		
		
		
                                <input type='checkbox' id='inlineCheckbox1' value='option1' ". $check[ ($effect['Stripes']) ]." class='meffects'> ".__("Stripes","migla-donation")."</label><label class='list-group-item border-check-control'>";

 $e =   $check[ ($effect['Pulse']) ]; 
                echo "<input type='checkbox' id='inlineCheckbox2' value='option2' ". $check[ ($effect['Pulse']) ]." class='meffects'>".__("Pulse","migla-donation")."</label><label class=' list-group-item border-check-control'>";

 $e =   $check[ ($effect['AnimatedStripes']) ]; 
                echo "<input type='checkbox' id='inlineCheckbox3' value='option3' ".$check[ ($effect['AnimatedStripes']) ]." class='meffects'>".__("Animated Stripes","migla-donation")."<span class='text-muted'><small> ".__("(Stripes must be on)","migla-donation")."</small></span></label><label class=' list-group-item border-check-control'>";

 $e =   $check[ ($effect['Percentage']) ]; 
               echo "<input type='checkbox' value='option4' id='inlineCheckbox4' ". $check[ ($effect['Percentage']) ]." class='meffects'>".__("Percentage","migla-donation")."</label>";
                echo "</div>";
              
		echo "<span class='help-control col-sm-12 text-center-xs'> ".__("This controls the progress bar's effects and styling. Settings are automatically saved.","migla-donation")."</span></div></div>";
		
		
		
		// Five Row Progress Bar
                $effectClasses = "";
                if( strcmp( $effect['Stripes'] , "yes") == 0){
                  $effectClasses = $effectClasses . " striped";
                }
                if( strcmp( $effect['Pulse'] , "yes") == 0){
                  $effectClasses = $effectClasses . " mg_pulse";
                }
                if( strcmp( $effect['AnimatedStripes'] ,"yes") == 0){
                  $effectClasses = $effectClasses . " active animated-striped";
                }
                if( strcmp( $effect['Percentage'], "yes") == 0 ){
                  $effectClasses = $effectClasses . " mg_percentage";
                }

        $style1 = "";
        $style1 .= "box-shadow:".$boxshadow_color[2]."px ".$boxshadow_color[3]."px ".$boxshadow_color[4]."px ".$boxshadow_color[5]."px " ;
        $style1 .= $boxshadow_color[0]." inset !important;";

        $style1 .= "background-color:". $progressbar_bg[0].";";

        $style1 .= "-webkit-border-top-left-radius:".$borderRadius[0]."px; -webkit-border-top-right-radius: ".$borderRadius[1]."px;";
        $style1 .= "-webkit-border-bottom-left-radius: ".$borderRadius[2]."px; -webkit-border-bottom-right-radius:".$borderRadius[3]."px;";

        $style1 .= "-moz-border-radius-topleft:".$borderRadius[0]."px; -moz-border-radius-topright: ".$borderRadius[1]."px;";
        $style1 .= "-moz-border-radius-bottomleft: ".$borderRadius[2]."px;-moz-border-radius-bottomright:".$borderRadius[3]."px;";

        $style1 .= "border-top-left-radius:".$borderRadius[0]."px; border-top-right-radius: ".$borderRadius[1]."px;";
        $style1 .= "border-bottom-left-radius:  ".$borderRadius[2]."px;border-bottom-right-radius:".$borderRadius[3]."px;";
	
        $stylebar = "background-color:".$bar_color[0].";";

		echo "<div class='row'>"; 
		echo "<div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs'><strong>".__("Preview:","migla-donation")."</strong></label></div>";
                
		echo "<div class='col-sm-6 col-xs-12'><div class='progress ".$effectClasses."' id='me' style='".$style1."' >";
		echo "<div id='div2previewbar' style='width: 50%;".$stylebar."' aria-valuemax='100' aria-valuemin='0' aria-valuenow='20' role='progressbar' class='progress-bar'>50%</div></div></div>";

		//RESTORE
		echo "<div class='col-sm-3  col-xs-12'></div>";
		echo "</div>";
		
		
echo "<div class='row'>";
echo "<div class='col-sm-12 center-button'><button id='migla_save_bar' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div>";		
echo "</div>";			
		
		echo "</div></div> <!--  -->";			
                echo "</div>";
		echo "</section>";	

	$this->migla_circle_settings();	
	
	//$this->preview_circle_section();	


echo "<div class='row'><div class='col-md-12 col-lg-12 col-xl-12'><section class='panel panel-featured-left panel-featured-primary'><div class='panel-body'>";
			
echo "<div class='col-sm-12  col-xs-12'><p><button data-target='#confirm-reset' data-toggle='modal' value='reset' class='btn btn-info rbutton ' id=''><i class='fa fa-fw fa-refresh'></i>".__("Restore to Default","migla-donation")."</button></p></div>";
								
	echo "</div></section></div></div>";

//RESTORE
 echo " <div class='modal fade' style='display:none' id='confirm-reset' tabindex='-1' role='dialog' aria-labelledby='miglaWarning' aria-hidden='true' data-backdrop='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
            
                <div class='modal-header'>


                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true' data-target='#confirm-reset'><i class='fa fa-times'></i> </button>
                    <h4 class='modal-title' id='miglaConfirm'>".__("Confirm Restore","migla-donation")."</h4>
                </div>
            
<div class='modal-wrap clearfix'>

           <div class='modal-alert'>
														<i class='fa fa-exclamation-circle'></i> 
													</div>  

   <div class='modal-body'>


                    <p>".__("Are you sure you want to restore all of the styling to their default styles? This can not be undone","migla-donation")."</p>
                </div>

</div> 
                
                <div class='modal-footer'>
                    <button type='button' class='btn btn-default mbutton' data-dismiss='modal'>".__("Cancel","migla-donation")."</button>
                    <button type='button' class='btn btn-danger danger rbutton' id='miglaRestore'><i class='fa fa-fw fa-refresh'></i>".__("Restore to default","migla-donation")."</button>
                   
                </div>
            </div>
        </div>
    </div>"; 

	
		echo "</div></div> <!-- container -->";
	
	}

}

?>