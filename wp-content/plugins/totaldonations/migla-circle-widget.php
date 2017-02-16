<?php
/*
 * Plugin Name: Total Donations Circle Widget
 * Plugin URI: http://calmar-webmedia.com/testing-area/wp-plugin-dev
 * Description: A widget that displays the progress-bar for each campaign in Total Donations.
 * Version: 1.2.1
 * Author: Binti Brindamour and Astried Silvanie
 * Author URI: http://calmar-webmedia.com/
 * License: Licensed
 */


/*
 * Add function to widgets_init that'll load our widget.
 */
add_action( 'widgets_init', 'totaldonations_circle_widget' );

/*
 * Register widget.
 */
function totaldonations_circle_widget() {
	register_widget( 'Totaldonations_circle_Widget' );
}


/*
 * Widget class.
 */
class totaldonations_circle_widget extends WP_Widget {

	/* ---------------------------- */
	/* -------- Widget setup -------- */
	/* ---------------------------- */
	
	function __construct(){
	
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'totaldonations_circle_widget', 'description' => __('A widget that displays a circle progress bar for Total Donations', 'localization') );

		 /* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'totaldonations_circle_widget' );

		/* Create the widget. */
		WP_Widget::__construct( 'totaldonations_circle_widget', __('Total Donations Circle Widget','localization'), $widget_ops, $control_ops );
	}

	/* ---------------------------- */
	/* ------- Display Widget -------- */
	/* ---------------------------- */
	
	function widget( $args, $instance ) {
		extract( $args );


       if( wp_script_is( 'migla-front-end-css', 'registered' ) && wp_script_is( 'migla-front-end-css', 'queue' )  )
       {
       }else{
          //make sure it only load once
          if( wp_script_is( 'mg_progress-bar', 'registered' ) ){
          }else{
              wp_register_style( 'mg_progress-bar', plugins_url( 'totaldonations/css/mg_progress-bar.css' , dirname(__FILE__)) );
          }

          if( wp_script_is( 'mg_progress-bar', 'queue' ) ){
          }else{
              wp_enqueue_style( 'mg_progress-bar' );
          }          
       }


          wp_enqueue_script( 'migla-circle-progress-js', plugins_url( 'totaldonations/js/circle-progress.js' , dirname(__FILE__)) );
          wp_enqueue_script( 'migla-migla-circle-progress-js', plugins_url( 'totaldonations/js/migla-circle-progress.js' , dirname(__FILE__)) );


		/* Our variables from the widget settings. */
        $title = apply_filters('widget_title', $instance['title'] );
	    $campaign = $instance['campaign'];
        $style = $instance['belowHTML']; 
		$style_above = $instance['aboveHTML'];
        $link = $instance['link'];
        $btnclass = $instance['btnclass'];
	    $btnstyle = $instance['btnstyle'];
        $btntext = $instance['btntext'];
        $text_align = $instance['text_align'];    
        $text1 = $instance['text1'];  
        $text2 = $instance['text2'];  
        $text3 = $instance['text3'];    
        $fontsize = $instance['fontsize'];
        $circle_setting = array();		
	    $circle_setting['size'] = $instance['circle_size']; 
		$circle_setting['start_angle'] = $instance['circle_start_angle'];
		$circle_setting['thickness']   = $instance['circle_thickness'];
		$circle_setting['reverse']     = $instance['circle_reverse'];
	    $circle_setting['fill']      = $instance['circle_fill'];
	    $circle_setting['line_cap']  = $instance['circle_line_cap'];
	    $circle_setting['animation'] = $instance['circle_animation'];
		$circle_setting['inside'] = $instance['circle_inside'];
		$form_id	= $instance['form_id'];		
		
              	/* Before widget (defined by themes). */
        echo $before_widget;

      $send =  str_replace( "[q]", "'", $campaign); 
      echo "<h3 class='widget-title'>";
      echo $title. "<br>";
      echo "</h3>";
	  
	if( $text_align == 'no' )  
		$is_text = 'no';
	else
		$is_text = 'yes';
		
	$the_widget = migla_text_circle_widget( $campaign, "", $link, $btntext, $is_text, rand(), 
					$text_align , $text1, $text2, $text3 , $fontsize, $circle_setting );
	
		$out_cname		= str_ireplace("[q]", "'", $the_widget[3]);	
		$placeholder	= array( '[amount]'		,'[target]' 	,'[campaign]'	, '[backers]'	, '[percentage]' );
		$replace		= array( $the_widget[1] , $the_widget[2], $out_cname	,$the_widget[4] , $the_widget[5] );	
			  

		$style_above_fix = str_ireplace( $placeholder, $replace, $style_above);
		echo "<div class='mg_circle-custom-text' >".$style_above_fix. "</div>";	

		$is_text = 'no';
		if( $text_align != 'no' )
			 $is_text = 'yes'; 
		   
		if( $fontsize == '' || $fontsize > 40 ) 
			$fontsize = 40; 
				
		if( $fontsize < 9 )
			$fontsize = 9;

		echo  $the_widget[0];
	  
		$class2 = "";
		if( $btnstyle == 'GreyButton' )
			$class2 = ' mg-btn-grey';	    
			
		$style_fix 		= str_ireplace( $placeholder, $replace, $style);
		
		echo "<div class='mg_circle-custom-text' >".$style_fix. "</div>";	

		$fund_array = (array)get_option( 'migla_campaign' );

		$i = 0;
		if( empty($fund_array[0]) )
		{ 
		}else{    
			foreach ( (array)$fund_array as $key => $value ) 
			{ 
				$c1_name = esc_html__( $fund_array[$i]['name'] );
                $c_name = str_replace( "[q]", "'", $c1_name );

				if( strcmp( $fund_array[$i]['name'], $campaign ) == 0  )
				{
					$form_id = $fund_array[$i]['form_id'];
					break;
				}
			
				$i++;
			}
		}		
		
		if( $link == 'on' ){
			$url = get_post_meta( $form_id, 'migla_form_url', true);
			
			if( $url != false && $url != '')
			{
				echo "<form action='".$url."' method='post'>";
				echo "<input type='hidden' name='thanks' value='widget_bar' />";
				echo "<button class='migla_donate_now ".$btnclass . $class2."'>".$btntext."</button>";
				echo "</form>";
			}else{
				echo "<form action='".get_option('migla_form_url')."' method='post'>";
				echo "<input type='hidden' name='campaign' value='".$campaign."' />";
				echo "<input type='hidden' name='thanks' value='widget_bar' />";
				echo "<button class='migla_donate_now ".$btnclass . $class2."'>".$btntext."</button>";
				echo "</form>";		
			}
		}

?>

<script> 
jQuery('.migla_donate_now').click(function(e) {
   e.preventDefault();
   jQuery(this).parents('form').submit();
});
</script>

<?php		

        echo $after_widget;
                
         
		
	}

	/* ---------------------------- */
	/* ------- Update Widget -------- */
	/* ---------------------------- */
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['campaign'] = strip_tags( $new_instance['campaign'] );

		/* No need to strip tags for.. */
        $instance['belowHTML'] =  $new_instance['belowHTML'] ; $instance['aboveHTML'] =  $new_instance['aboveHTML'] ;		
        $instance['link'] =  strip_tags( $new_instance['link'] ) ;		
        $instance['btnclass'] =  strip_tags( $new_instance['btnclass'] );
        $instance['btnstyle'] =  strip_tags( $new_instance['btnstyle'] );
        $instance['btntext'] = $new_instance['btntext'];
        $instance['text_align'] = $new_instance['text_align'];
        $instance['text1'] = $new_instance['text1'];
        $instance['text2'] = $new_instance['text2'];
        $instance['text3'] = $new_instance['text3'];
        $instance['fontsize'] = $new_instance['fontsize'];
	    $instance['circle_size'] = $new_instance['circle_size']; 
		$instance['circle_thickness'] = $new_instance['circle_thickness']; 
		$instance['circle_start_angle'] = $new_instance['circle_start_angle']; 
	    $instance['circle_fill'] = $new_instance['circle_fill'];
		$instance['circle_reverse'] = $new_instance['circle_reverse'];
	    $instance['circle_line_cap'] = $new_instance['circle_line_cap'];
	    $instance['circle_animation'] = $new_instance['circle_animation'];
		$instance['circle_inside'] = $new_instance['circle_inside'];
		$instance['form_id']       = $new_instance['form_id'];		
	   		
 	return $instance;
	}
	
	/* ---------------------------- */
	/* ------- Widget Settings ------- */
	/* ---------------------------- */
	
	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	 
	function form( $instance ) {
	
     // Check values 
    if( $instance ) { 

       $title = esc_attr($instance['title']); 
       $campaign = esc_attr($instance['campaign']); 
       $belowHTML = $instance['belowHTML']; $aboveHTML = $instance['aboveHTML']; 
       $link = esc_attr($instance['link']); 
       $btnclass = esc_attr($instance['btnclass']); 
       $btnstyle = esc_attr($instance['btnstyle']); 
       $btntext = esc_attr($instance['btntext']);
       $text_align = esc_attr($instance['text_align']);
       $text1 = esc_attr($instance['text1']);
       $text2 = esc_attr($instance['text2']);
       $text3 = esc_attr($instance['text3']);
       $fontsize = esc_attr($instance['fontsize']); 

	    $circle_size = $instance['circle_size']; 
		$circle_thickness = $instance['circle_thickness'];
		$circle_start_angle = $instance['circle_start_angle'];
	    $circle_reverse = $instance['circle_reverse'];
	    $circle_fill = $instance['circle_fill'];
		$circle_line_cap = $instance['circle_line_cap'];
		$circle_animation = $instance['circle_animation'];
		$circle_inside = $instance['circle_inside'];
	    $form_id = $instance['form_id'];  
     } else { 
       $title = "Total Donations Progress Bar"; 
       $campaign = ''; 
       $belowHTML = ''; $aboveHTML = '';
       $link = ''; 
       $btnclass = ''; 
       $btnstyle = ''; 
       $btntext = '';
       $text_align = 'left';
       $text1 = 'Current';
       $text2 = 'Target';
       $text3 = 'Backers';
       $fontsize = 20;
	   $circle_size = 100;
	   $circle_thickness = 10;
	   $circle_start_angle = 10;
	   $circle_reverse = 'no';	   
	   $circle_fill = '#FF00FF';
	   $circle_line_cap = 'square';
	   $circle_animation = 'none';
	   $circle_inside = 'percentage';	
	   
       $instance['title'] = "Total Donations Progress Bar"; 
       $instance['campaign'] = ''; 
       $instance['belowHTML'] = ''; $instance['aboveHTML'] = ''; 
       $instance['link'] = ''; 
       $instance['btnclass'] = ''; 
       $instance['btnstyle'] = '';  
       $instance['btntext'] = ''; 
       $instance['text_align'] = ''; 
       $instance['text1'] = 'Current';
       $instance['text2'] = 'Target' ;
       $instance['text3'] = 'Backer';
       $instance['fontsize'] = 20;
	   $instance['circle_size'] = 100;   
		$instance['circle_thickness'] = 10; 
		$instance['circle_start_angle'] = 10; 
	    $instance['circle_fill'] = '#FF0000';
		$instance['circle_reverse'] = 'no';
	    $instance['circle_line_cap'] = 'square';
	    $instance['circle_animation'] = 'normal';
		$instance['circle_inside'] = 'percentage';   
     } 
	 
	if( !isset($instance['form_id']) )	
		$form_id = '';	 
?>

	<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title of the circle progress bar:', 'localization') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" />
		</p>
		
<?php		

	$fund_array = (array)get_option( 'migla_campaign' );

	$j = 0;
    if( empty($fund_array[0]) ){ 
    }else{    
       foreach ( (array)$fund_array as $key => $value ) 
	   { 
			echo "<div id='mg_".str_ireplace(" ", "",$fund_array[$j]['name'])."' style='display:none'>".$fund_array[$j]['form_id']."</div>";
			$j++;
	   }
	}
?>			

<label ><?php _e('Current Campaign : '  , 'localization') ?></label>  
<label ><?php $c_name = str_replace( "[q]", "'", $campaign ); echo $c_name; ?></label>  

<p style='display:none'><label ><?php _e('Form ID : '  , 'localization') ?></label>  
<label ><input disabled type='text' class='mg_form_id' value='<?php echo $form_id; ?>' 
 name='<?php echo $this->get_field_name( 'form_id' ); ?>' id='<?php echo $this->get_field_id( 'form_id' )?>' ></label></p>  
 
<p style='display:none'><label ><?php _e('URL : '  , 'localization') ?></label>  
<label class='mg_form_url'></label></p>   

<br><br><label ><?php _e('Choose a campaign to show :', 'localization') ?></label>   
   
<?php
    //Show select on widget 
      $out = "";
      $out .= "<select class='widefat migla_select_campaign' name='".$this->get_field_name( 'campaign' )."' id='".$this->get_field_id( 'campaign' )."'>" ;
      $b = "";
      $i = 0;   

    if( empty($fund_array[0]) ){ 
    }else{    
       //print_r($fund_array);
       foreach ( (array)$fund_array as $key => $value ) 
	   { 
	    if( strcmp( $fund_array[$i]['show'],"1")==0 ){
                  $c1_name = esc_html__( $fund_array[$i]['name'] );
                  $c_name = str_replace( "[q]", "'", $c1_name );

         if( strcmp( $fund_array[$i]['name'], $campaign ) == 0  ){
		    $out .= "<option value='".$fund_array[$i]['name']."' selected=selected >".$c_name."</option>";
                  }else{
		     $out .= "<option value='".$fund_array[$i]['name']."' >".$c_name."</option>";
                  }
	   }
        $i++;
	   }  
      }	   
      $out .= "</select>"; 
      echo $out;
?>

     <?php       if( $fontsize == '' || $fontsize > 40 ){ $fontsize = 40; }
      if( $fontsize < 9 ){ $fontsize = 9; } ?>
        <p>
        <div><label>Font Size: <small>(The Percentage inside the circle)</small></label>
        <input input='text' class='widefat' type='number' min='9' max='40' id="<?php echo $this->get_field_id( 'fontzise' ); ?>" name="<?php echo $this->get_field_name( 'fontsize' ); ?>" value="<?php echo $fontsize; ?>"></input></div> 
        </p>

<?php
	 echo "<p>";
	 echo "<label>".__("Circle Size in Pixels :","migla-donation")."</label>";
	 echo "<input class='widefat' id='".$this->get_field_id( 'circle_size' )."' name='".$this->get_field_name( 'circle_size' )."' type='number' min='10' max='500' value='".$circle_size."'>";
	 echo "</p>";
	 
	 echo "<p>";
	 echo "<label>".__("Circle Thickness :","migla-donation")."</label>";
	 echo "<input class='widefat' id='".$this->get_field_id( 'circle_thickness' )."' name='".$this->get_field_name( 'circle_thickness' )."' type='number' min='10' max='100' value='".$circle_thickness."'>";
	 echo "</p>";

	 echo "<p>";
	 echo "<label>".__("Circle Start Angle :","migla-donation")."</label>";
	 echo "<input class='widefat' id='".$this->get_field_id( 'circle_start_angle' )."' name='".$this->get_field_name( 'circle_start_angle' )."' type='number' min='10' max='100' value='".$circle_start_angle."'>";
	 echo "</p>";	

 	echo "<p>";
	echo "<label >".__("reverse:","migla-donation")."</label>";
    if( $circle_reverse == 'yes' ){
	echo "<select id='".$this->get_field_id( 'circle_reverse' )."' name='".$this->get_field_name( 'circle_reverse' )."' class='widefat'><option value='yes' selected>".__("Yes","migla-donation")."</option><option value='no'>".__("No","migla-donation")."</option></select>";
    }else{
	echo "<select id='".$this->get_field_id( 'circle_reverse' )."' name='".$this->get_field_name( 'circle_reverse' )."' class='widefat'><option value='yes'>".__("Yes","migla-donation")."</option><option value='no' selected>".__("No","migla-donation")."</option></select>";
    }	 
	 echo "</p>";	 
	 
 	echo "<p>";
	echo "<label >".__("Line Cap:","migla-donation")."</label>";
    if( $circle_line_cap == 'butt' ){
	echo "<select id='".$this->get_field_id( 'circle_line_cap' )."' name='".$this->get_field_name( 'circle_line_cap' )."' class='widefat'><option value='butt' selected>".__("Square","migla-donation")."</option><option value='round'>".__("Round","migla-donation")."</option></select>";
    }else{
	echo "<select id='".$this->get_field_id( 'circle_line_cap' )."' name='".$this->get_field_name( 'circle_line_cap' )."' class='widefat'><option value='butt'>".__("Square","migla-donation")."</option><option value='round' selected>".__("Round","migla-donation")."</option></select>";
    }	 
	 echo "</p>";

	 echo "<p>";
	 echo "<label>".__('Fill:','migla-donation')." </label>";
	 echo "<input maxlength='7' size='7' class='mg-color-field widefat' type='text' id='".$this->get_field_id( 'circle_fill' )."' name='".$this->get_field_name( 'circle_fill' )."' value='".$circle_fill."'>";
	 echo "</p>";	 
	 
    echo "<div class='mg_circle_animation_text_inside'><p>";
    echo "<label>".__("Animation:","migla-donation")."</label>";
    if( $circle_animation == 'none' ){
	echo "<select id='".$this->get_field_id( 'circle_animation' )."' name='".$this->get_field_name( 'circle_animation' )."' class='widefat mg_select_circle_animation'>";
    echo "<option value='none' selected>".__("None","migla-donation")."</option><option value='normal'>".__("Normal","migla-donation")."</option><option value='back_forth'>".__("Back and Forth","migla-donation")."</option>";
    echo "</select>";
    }else if( $circle_animation == 'normal' ){
	echo "<select id='".$this->get_field_id( 'circle_animation' )."' name='".$this->get_field_name( 'circle_animation' )."' class='widefat mg_select_circle_animation'>";
    echo "<option value='none'>".__("None","migla-donation")."</option><option value='normal' selected>".__("Normal","migla-donation")."</option><option value='back_forth'>".__("Back and Forth","migla-donation")."</option>";
    echo "</select>";
    }else{
	echo "<select id='".$this->get_field_id( 'circle_animation' )."' name='".$this->get_field_name( 'circle_animation' )."' class='widefat mg_select_circle_animation'>";
    echo "<option value='none'>".__("None","migla-donation")."</option><option value='normal'>".__("Normal","migla-donation")."</option><option value='back_forth' selected>".__("Back and Forth","migla-donation")."</option>";
    echo "</select>";
    }	
	echo "</p>";

	if( $circle_animation == 'none' )
		echo "<p class='mg_circle_text_inside' style='display:none'>";
 	else
		echo "<p class='mg_circle_text_inside'>";

	echo "<label >".__("Inside Text:","migla-donation")."</label>";
    if( $circle_inside == 'percentage' ){
		echo "<select id='".$this->get_field_id( 'circle_inside' )."' name='".$this->get_field_name( 'circle_inside' )."' class='widefat'>";
		echo "<option value='none' >".__("None","migla-donation")."</option>";
		echo "<option value='percentage' selected>".__("Donation Percentage","migla-donation")."</option>";
		echo "</select>";
	}else{
		echo "<select id='".$this->get_field_id( 'circle_inside' )."' name='".$this->get_field_name( 'circle_inside' )."' class='widefat'>";
		echo "<option value='none' selected>".__("None","migla-donation")."</option>";
		echo "<option value='percentage' >".__("Donation Percentage","migla-donation")."</option>";
		echo "</select>";	
	}	 
	 echo "</p></div>";		
?>		
			
       <br><br>
      <?php if( $link == 'on'){  ?> 
        <div><input type="checkbox" checked="checked" id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>">
        <label>Add link button ? </label></div>
      <?php }else{  ?> 
        <div><input type="checkbox" id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>">
        <label>Add link button ? </label></div>
      <?php }  ?> 

        <div><label>Add a css class on button: <small>(theme button only)</small></label>
        <input input='text' class='widefat' type='text' id="<?php echo $this->get_field_id( 'btnclass' ); ?>" name="<?php echo $this->get_field_name( 'btnclass' ); ?>" value="<?php echo $btnclass; ?>"></input></div>  
  
     <br><label>Choose a button style:</label> 
     <select id="<?php echo $this->get_field_id( 'btnstyle' ); ?>" name="<?php echo $this->get_field_name( 'btnstyle' ); ?>" class="widefat migla_select">
     <?php if( $btnstyle == "GreyButton" ) { ?>
 	   <option  value="themeDefault">Your Theme Default</option>
       <option selected="" value="GreyButton">Grey Button</option>
	 <?php }else{ ?>
 	   <option selected="" value="themeDefault">Your Theme Default</option>
       <option value="GreyButton">Grey Button</option>	 
	 <?php } ?>
	 </select>
	 <br><br>

      <p>
	<label for="<?php echo $this->get_field_id( 'btntext' ); ?>"><?php _e('Text on button:', 'localization') ?></label>
	<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'btntext' ); ?>" name="<?php echo $this->get_field_name( 'btntext' ); ?>" value="<?php echo $btntext; ?>" />
      </p>

<p><label ><?php _e('Text Alignment and Orientation:', 'localization') ?></label>    
<?php
      $out2 = "";
      $out2 .= "<select class='widefat migla_select' name='".$this->get_field_name( 'text_align' )."' id='".$this->get_field_id( 'text_align' )."'>" ;

      if( $text_align == 'no' ){
	   $out2 .= "<option value='no' selected=selected >No Text</option>";
      }else{
	   $out2 .= "<option value='no' >No Text</option>";
      }	   
      if( $text_align == 'left_right' ){
	   $out2 .= "<option value='left_right' selected=selected >align left with right text</option>";
      }else{
	   $out2 .= "<option value='left_right' >align left with right text</option>";
      }	  
      if( $text_align == 'left_left' ){
	   $out2 .= "<option value='left_left' selected=selected >align left with left text</option>";
      }else{
	   $out2 .= "<option value='left_left' >align left with left text</option>";
      }	 	  
      if( $text_align == 'right_left' ){
	   $out2 .= "<option value='right_left' selected=selected >alight right with left text</option>";
      }else{
	   $out2 .= "<option value='right_left' >alight right with left text</option>";
      }	   
      if( $text_align == 'right_right' ){
	   $out2 .= "<option value='right_right' selected=selected >align right with right text</option>";
      }else{
	   $out2 .= "<option value='right_right' >align right with right text</option>";
      }	 	  
      $out2 .= "</select>"; 
      echo $out2;
?>
   </p>
	  
      <p>
	<label for="<?php echo $this->get_field_id( 'text1' ); ?>"><?php _e('Current Amount Label:', 'localization') ?></label>
	<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'text1' ); ?>" name="<?php echo $this->get_field_name( 'text1' ); ?>" value="<?php echo $text1; ?>" />
      </p>

      <p>
	<label for="<?php echo $this->get_field_id( 'text2' ); ?>"><?php _e('Target Amount Label:', 'localization') ?></label>
	<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'text2' ); ?>" name="<?php echo $this->get_field_name( 'text2' ); ?>" value="<?php echo $text2; ?>" />
      </p>
      
      <p>
	<label for="<?php echo $this->get_field_id( 'text3' ); ?>"><?php _e('Total Supporters Label:', 'localization') ?></label>
	<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'text3' ); ?>" name="<?php echo $this->get_field_name( 'text3' ); ?>" value="<?php echo $text3; ?>" />
      </p>

      <p> <label for="<?php echo $this->get_field_id('aboveHTML'); ?>">Add HTML or Plain Text above:</label>
       <textarea  class="widefat"  id="<?php echo $this->get_field_id( 'aboveHTML' ); ?>" name="<?php echo $this->get_field_name( 'aboveHTML' ); ?>"  ><?php echo $aboveHTML; ?></textarea><small><?php _e('shortcodes allowed: [amount]	[target] [campaign] [backers] [percentage]', 'localization') ?></small></p>

       <p> <label for="<?php echo $this->get_field_id('belowHTML'); ?>">Add HTML or Plain Text below:</label>
       <textarea  class="widefat"  id="<?php echo $this->get_field_id( 'belowHTML' ); ?>" name="<?php echo $this->get_field_name( 'belowHTML' ); ?>"  ><?php echo $belowHTML; ?></textarea><small><?php _e('shortcodes allowed: [amount]	[target] [campaign] [backers] [percentage]', 'localization') ?></small></p>

	<?php
	}
}
?>