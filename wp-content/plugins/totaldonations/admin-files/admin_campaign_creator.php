<?php

class migla_campaign_creator_menu_class {

	function __construct(  ) {
		add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 7 );
	}
	
	function menu_item() {
		add_submenu_page(
			'migla_donation_menu_page',
			__( 'Campaign Creator', 'migla-donation' ),
			__( 'Campaign Creator', 'migla-donation' ),
			'manage_options',
			'migla_donation_campaign_creator_page',
			array( $this, 'menu_page' )
		);
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

   function getSymbol(){
    $i = '';
    $currencies =  get_option( 'migla_currencies' ) ; 
    $def = get_option( 'migla_default_currency' );
	   foreach ( (array)$currencies as $key => $value ) 
	   { 
	     if ( strcmp($def,$currencies[$key]['code'] ) == 0 )
              { 
                 $i = $currencies[$key]['symbol'];
              }
	   }

    return $i;
   }
	
function menu_list()
{
 		echo "<div class='row'>";
		echo "<div class='col-sm-12'>";

		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down ' data-toggle='collapse' data-parent='.panel' href='#collapseOne' aria-expanded='true'></a></div><h2 class='panel-title'><div class='dashicons dashicons-plus'></div>". __('Add a Campaign Creator', 'migla-donation'). "";
		__("Add New Campaigns Creator","migla-donation");
		echo "</h2></header>";
		echo "<div class='panel-body collapse in' id='collapseOne'>";
		
		echo "<div class='row'><div class='col-sm-3'>";
		echo "<label for='mName' class='miglaCampaignNameLabel  control-label  text-right-sm text-center-xs'>". __('Title', 'migla-donation');
		echo "</label></div>";
		echo "<div class='col-sm-6 col-xs-12'><span class='input-group input-group-control'><span class='input-group-addon '><i class='fa fa fa-rocket fa-fw'></i>";
        echo "</span>";
		echo "<input type='text' id='mTitle' placeholder='Title' class='form-control' /></span></div>";
		echo "<div class='col-sm-3 hidden-xs'></div>";
        echo "<div class='col-sm-12 col-xs-12'><div class='help-control-center'>". __('This title will not be displayed on the frontend', 'migla-donation')."</div></div></div>";

		echo "<div class='row'><div class='col-sm-3'>";
		echo "<label for='mDesc' class='miglaCampaignDescLabel  control-label  text-right-sm text-center-xs'>". __('Description', 'migla-donation');
		echo "</label></div>";
		echo "<div class='col-sm-6 col-xs-12'><span class='input-group input-group-control'><span class='input-group-addon '><i class='fa fa-info-circle  fa-fw'></i>";
        echo "</span>";
		echo "<input type='text' id='mDesc' placeholder='Decription' class='form-control' /></span></div>";
		echo "<div class='col-sm-3 hidden-xs'></div>";
        echo "<div class='col-sm-12 col-xs-12'><div class='help-control-center'>". __('This description will not be displayed on the frontend', 'migla-donation')."</div></div></div>";

		echo "<div class='row'><div class='col-sm-3'>";
		echo "<label for='mDesc' class='miglaCampaignLabel  control-label  text-right-sm text-center-xs'>". __('Campaign', 'migla-donation');
		echo "</label></div>";
		echo "<div class='col-sm-6 col-xs-12'><span class='input-group input-group-control'><span class='input-group-addon '><i class='fa fa-medkit  fa-fw'></i>";
        echo "</span>";
		
		$campaigns = get_option('migla_campaign');
		//print_r($campaigns);
		echo "<select id='mCampaign'>";
		foreach( $campaigns as $campaign )
		{
			echo "<option value='".$campaign['form_id']."'>".str_ireplace('[q]', "'", $campaign['name'])."|". $this->getSymbol() . $campaign['target']."</option>";
		}
		echo "</select>";
		echo "</span></div>";
		echo "<div class='col-sm-3 hidden-xs'></div>";
        echo "<div class='col-sm-12 col-xs-12'><div class='help-control-center'></div></div></div>";		
				
		echo "<p><button id='miglaAddCampaignCreator' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation")."</button></p>";
		echo "<div></section><br></div>";
		

/** The LIST **/
echo "<div class='col-sm-12'>";

		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseTwo' aria-expanded='true'></a></div>";	

		echo "<h2 class='panel-title'><div class='dashicons dashicons-list-view'></div>". __("List of Available Campaigns Creator","migla-donation") ."</h2></header>";
		echo "<div id='collapseTwo' class='panel-body collapse in'>";	

		
global $wpdb;
$the_list = array(); 		
$the_list = $wpdb->get_results( 
				$wpdb->prepare( 
				"SELECT * FROM {$wpdb->prefix}posts WHERE post_type = %s" ,
				'miglacampaigncreator'
			)
	 );
	 
$index = 0;
$data = array();	 
	 
if( $the_list != false ){
	foreach( $the_list as $list ){
	    $data[$index]['title'] 		= $list->post_title;
	    $data[$index]['desc'] 		= $list->post_content;
	    $data[$index]['form_id'] 	= $list->ID;	
        $campaign 					= get_post_meta( $list->ID, 'migla_campaign_creator');
		$data[$index]['campaign'] 	= $campaign[0];
		$index++;
	}
}	 
	 


$idk = 0;
		
echo "<ul class='row mg_campaign_list'>";
 
if( empty($data) )
{
  echo __('You do not have any campaigns creator yet','migla-donation');
  
}else{
 
 foreach( (array)$data as $d ){
 
  echo "<li class='ui-state-default formfield clearfix formfield_campaign'>";
 
  $n 		= $d['title'];
  $t 		= $d['desc'];
  $post_id 	= $d['form_id'];
  
	echo "<input type='hidden' name='oldlabel' value='".$n."' />";
	echo "<input type='hidden' name='title' value='".$n."' />";
	echo "<input type='hidden' name='desc' value='".$t."' />";
	echo "<input type='hidden' name='form_id'  value='".$post_id."' />";

	  echo"<div class='col-sm-1 hidden-xs'><label class='control-label'>". __('Title','migla-donation'). "</label></div>";
	  echo "<div class='col-sm-2 col-xs-12'>"; 
	  echo "<input type='text' class='titleChange' name='' placeholder='' value='".$n."' /></div>";

	  echo "<div class='col-sm-1 hidden-xs'><label class='control-label'>". __('Description','migla-donation'). "</label></div>";
	  echo "<div class='col-sm-2 col-xs-12'>";
	  echo "<input type='text' class='descChange' name='' placeholder='' value='" . $t . "' /></div>";
	  
	echo "<div class='col-sm-1 col-xs-12'>";
	echo "<button id='form_".$post_id."' class='mg_a-form-per-campaign-options mbutton edit_custom-fields-list' onClick='mg_send_form_id(".$post_id.")'>";
	echo "</button></div>";

	echo "<div class='col-sm-3 col-xs-12'>";
	echo '<input type="text" value="[totaldonations-circle-progressbar id=\''.$post_id .'\']" ';
	echo "placeholder='' name='' class='mg_label-shortcode' onclick='this.setSelectionRange(0, this.value.length)'></div>";

  echo "<div class='control-radio-sortable col-sm-1 col-xs-12'>";
  echo "<span><button class='removeList' data-toggle='modal' data-target='#confirm-delete'><i class='fa fa-fw fa-trash'></i></button></span>";
  echo "</div>";  

  $idk++;

  echo "</li>";
 } 
}
echo "</ul>";



echo "<div class='row'><div class='col-sm-6'><button value='save' class='btn btn-info pbutton' id='miglaSaveCampaign'><i class='fa fa-fw fa-save'></i>". __(' update list','migla-donation'). "</button></div></div>";

echo "</div></section>";
		
		echo "</div></div> <!--  -->";		


 echo " <div class='modal fade' id='confirm-delete' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true' data-backdrop='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
            
                <div class='modal-header'>


                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true' data-target='#confirm-delete'><i class='fa fa-times'></i></button>
                    <h4 class='modal-title' id='myModalLabel'>". __("Confirm Delete","migla-donation"). "</h4>
                </div>
<div class='modal-wrap clearfix'>
           <div class='modal-alert'>
														<i class='fa fa-times-circle'></i>
													</div>  
   <div class='modal-body'>
                    <p>". __("Are you sure you want to delete this campaign creator?","migla-donation") . "</p>
                </div>
</div> 
                
                <div class='modal-footer'>
                    <button type='button' class='btn btn-default mbutton' data-dismiss='modal'>". __("Cancel","migla-donation") ."</button>
                    <button type='button' id='mRemove' class='btn btn-danger danger rbutton' >". __("Delete","migla-donation") ."</button>
                   
                </div>
            </div>
        </div>
    "; 				
   
}

function menu_edit( $post_id )
{

echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'>";
echo "<a aria-expanded='true' href='#collapseFour' data-parent='.panel' data-toggle='collapse' class='fa fa-caret-down'></a></div>";
echo "<h2 class='panel-title'><div class='dashicons dashicons-admin-appearance'></div>Circle Progress Bar Campaign Creator</h2></header>";
echo "<div class='panel-body collapse in' id='collapseFour'>";

$layout = get_post_meta( $post_id, 'migla_circle_layout' );
if( $layout == false || $layout == '' )
{
    $layout = 'mg_centered';
}else{
	$layout = $layout[0];
}

$lyt = array();
$lyt['mg_centered'] = '';
$lyt['mg_circle-left'] = '';
$lyt['mg_circle-right'] = '';
$lyt[$layout] = 'checked';

echo "<div class='row mg_edit_circle'><div class='col-sm-3  col-xs-12'>";
echo "<label class='control-label text-right-sm text-center-xs' for='migla_circle_line_cap'>Circle/HTML alignment:</label></div>";

echo "<div class='col-sm-6  col-xs-12'><div class='mg_layout-selector'>";

echo "<label class='mg_box'><img alt='Centered Circle with No HTML' src='".plugins_url('/images/circle-no-html-1.png', dirname(__FILE__) )."' /><br>";
echo "<input ".$lyt['mg_centered'] ." type='radio' value='mg_centered' id='mg_centered' name='mg_circle-HTML'></label>";


echo "<label class='mg_box'><img alt='Circle on the left HTML right' src='".plugins_url('/images/circle-left-html-right.png', dirname(__FILE__) )."' /><br>";
echo "<input ".$lyt['mg_circle-left']." type='radio' value='mg_circle-left' id='mg_circle-left' name='mg_circle-HTML'></label>";


echo "<label class='mg_box'><img alt='Circle on the right HTML on the left' src='".plugins_url('/images/circle-right-html-left.png', dirname(__FILE__) )."' /><br>";
echo "<input ".$lyt['mg_circle-right']." type='radio' value='mg_circle-right' id='mg_circle-right' name='mg_circle-HTML'></label></div></div></div>";


    echo "<div class='row mg_edit_ mg_edit_html1' >";
    echo "<div class='col-sm-3  col-xs-12'>";  
    echo "<label class='control-label text-right-sm text-center-xs'>".__('Progress Bar HTML  (HTML must be on):', 'localization')."</label></div>" ;
    echo "<div class='col-sm-6 col-xs-12'>"; 

$content = get_post_meta( $post_id, 'migla_circle_box_html' );
if( $content == false ){
   $content = '';
}else{
   $content = $content[0];
}

$settings =   array(
    'wpautop' => true, // use wpautop?
    'media_buttons' => true, // show insert/upload button(s)
    'textarea_name' => 'migla_editor_html1', // set the textarea name to something different, square brackets [] can be used here
    'textarea_rows' => 10, // rows="..."
    'tinymce' => true
);
wp_editor(  stripslashes($content) , 'migla_editor_html1', $settings  );	

    echo "</div>";
 //   echo "</div><!-- end row -->";	


/*********** End Binti's Edit ************/

    echo "</div>";



 
 /********* Circle Settings *************/
   $circle = get_post_meta( $post_id, 'migla_circle_settings');
	 
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
        update_post_meta( $post_id, 'migla_circle_settings', $circle );
     }else{
	    $circle = $circle[0];
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

 $text1 	= get_post_meta($post_id, 'migla_cmpcreator_text1' );
  $text2 	= get_post_meta($post_id, 'migla_cmpcreator_text2' );
  $text3 	= get_post_meta($post_id, 'migla_cmpcreator_text3' );
  
	


/*********** binti's edit **********************/



echo "<div class='row mg_edit_circle'>";
echo "<div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs'>".__('Circle Text Info Alignment :', 'localization')."</label></div>";  

	 $text_align = get_post_meta( $post_id, 'migla_cmpcreator_textalign');
	 if( $text_align == '' || $text_align == false  ){
	    $text_align = 'left_right';
	 }else{
		$text_align = $text_align[0];
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
	echo "<input id='migla_circle_text1' placeholder='current' type='text' value='".$text1[0]."'>";
    echo "</div>";
	echo "<div class='col-sm-2 col-xs-12'>"; 
	echo "<input id='migla_circle_text2' placeholder='target' type='text' value='".$text2[0]."'>";
    echo "</div>";
	echo "<div class='col-sm-2 col-xs-12'>"; 
	echo "<input id='migla_circle_text3' placeholder='total donors' type='text' value='".$text3[0]."'>";
    echo "</div>";	


    echo "</div>";



echo "<div class='row'><div class='col-sm-12 center-button'><button value='save' class='btn btn-info pbutton' id='miglaSaveCreatorSettings'><i class='fa fa-fw fa-save'></i> save</button></div></div>";	



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
		 <span class='input-group'><span class='input-group-addon '><button id='currentColor' style='background-color:".$circle[0]['fill'].";'></button></span><input type='text' class='form-control mg-color-field' data-opacity='1.0' value='".$circle[0]['fill']."' id='migla_circle_fill'></span></div>";
		echo "<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button id='migla_fill_save' name='migla_fill_save' class='migla_circle_settings btn btn-info pbutton msave' value='save'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button> </div>";
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
                   echo "<option value='none' selected>".__("None","migla-donation")."</option><option value='progress'>".__("Animation Progress","migla-donation")."</option><option value='percentage'>".__("Donation Percentage","migla-donation")."</option>";
                   echo "</select></div>";
                }else if( $circle[0]['inside'] == 'progress' ){
	           echo "<div class='col-sm-6  col-xs-12'><select id='migla_circle_inside'>";
                   echo "<option value='none' >".__("None","migla-donation")."</option><option value='progress' selected>".__("Animation Progress","migla-donation")."</option><option value='percentage'>".__("Donation Percentage","migla-donation")."</option>";
                   echo "</select></div>";
                }else{
	           echo "<div class='col-sm-6  col-xs-12'><select id='migla_circle_inside'>";
                   echo "<option value='none' >".__("None","migla-donation")."</option><option value='progress'>".__("Animation Progress","migla-donation")."</option><option value='percentage' selected>".__("Donation Percentage","migla-donation")."</option>";
                   echo "</select></div></div>";
                }
                echo "<div class='row'><div class='col-sm-12  center-button'><button id='migla_save_circle_settings' class='btn btn-info pbutton msave' value='save'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div></div>";   
	
echo "</div></section>";

}

    function menu_page(){

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

		if( isset($_POST['campaign_creator_id']) ){

		echo "<div class='wrap'><div class='container-fluid'>";
		
		$cname = get_post_meta($_POST['campaign_creator_id'], 'migla_cmpcreator_campaign_name');
        echo "<h2 class='migla'>". __("Campaign Creator : ".$_POST['campaign_creator_id'] . " | " . $cname[0] ,"migla-donation")."</h2>";
		
		
		echo "<input type='hidden' id='mg_current_form' value='".$_POST['campaign_creator_id']."' >";
		
		echo "<div class='row'>";
		echo "<div class='col-sm-12'>";		
		echo "<a class='mg_go-back' onclick='mg_go_campaign()'><i class='fa fa-fw fa-arrow-left'></i>".__(" Go back to Main Campaign Creator Page", "migla-donation")."</a><br><br>";		
		echo "</div></div>";			
		
  		    //print_r($_POST);
			//$this->menu_edit( $_POST['campaign_creator_id']) ;
			
			$this->menu_edit( $_POST['campaign_creator_id']) ;

		  echo "<form id='mg_form_campaign' action='".get_admin_url()."admin.php?page=migla_donation_campaign_creator_page' method='post' style='display:none'>";
		  echo "<input id='mg_submit_form' class='button' type='submit' value='test submit' name='submit' />";
		  echo "</form>"; 				

		echo "</div></div> <!-- Wrap Fluid End -->";
		  
		}else{
		
		echo "<div class='wrap'><div class='container-fluid'>";
		
        echo "<h2 class='migla'>". __("Campaign Creator","migla-donation")."</h2>";
		
		
		  $this->menu_list();
			
		  echo "<form id='mg_form_campaign' action='".get_admin_url()."admin.php?page=migla_donation_campaign_creator_page' method='post' style='display:none'>";
		  echo "<input type='hidden' id='mg_form_id_send' name='campaign_creator_id' value='' >";
		  echo "<input id='mg_submit_form' class='button' type='submit' value='test submit' name='submit' />";
		  echo "</form>"; 	

		  echo "</div></div> <!-- Wrap Fluid End -->";
			
		}		
				
    }

}

?>