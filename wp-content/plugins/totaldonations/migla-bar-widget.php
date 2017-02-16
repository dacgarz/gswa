<?php
/*
 * Plugin Name: Total Donations Bar Widget
 * Plugin URI: http://calmar-webmedia.com/testing-area/wp-plugin-dev
 * Description: A widget that displays the progress-bar for each campaign in Total Donations.
 * Version: 1.3.0
 * Author: Binti Brindamour and Astried Silvanie
 * Author URI: http://calmar-webmedia.com/
 * License: Licensed
 */


/*
 * Add function to widgets_init that'll load our widget.
 */
add_action( 'widgets_init', 'totaldonations_bar_widget' );

/*
 * Register widget.
 */
function totaldonations_bar_widget() {
	register_widget( 'Totaldonations_Bar_Widget' );
}

/* Adding styles on head */
if( get_option('migla_script_load_css_pos') == 'head' )
{
    add_action( 'wp_enqueue_scripts' , 'mg_bar_enqueue_stylesheet');	
}

function mg_bar_enqueue_stylesheet(){
    wp_enqueue_style( 'mg_progress-bar',  plugins_url( 'totaldonations/css/mg_progress-bar.css' , dirname(__FILE__)) );
}


/*
 * Widget class.
 */
class totaldonations_bar_widget extends WP_Widget {

	/* ---------------------------- */
	/* -------- Widget setup -------- */
	/* ---------------------------- */
	
	function __construct(){
	
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'totaldonations_bar_widget', 'description' => __('A widget that displays progress bar for total donation', 'localization') );

		 /* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'totaldonations_bar_widget' );

		/* Create the widget. */
		WP_Widget::__construct( 'totaldonations_bar_widget', __('Total Donations Bar Widget','localization'), $widget_ops, $control_ops );
	}	
	
	/* ---------------------------- */
	/* ------- Display Widget -------- */
	/* ---------------------------- */
	
	function widget( $args, $instance ) 
	{
		extract( $args );

	   if( get_option('migla_script_load_css_pos') == 'head' )
       {
	   }else{
          if( wp_script_is( 'mg_progress-bar', 'queue' , plugins_url( 'totaldonations/css/mg_progress-bar.css' , dirname(__FILE__)) ) )
		  {
          }else{		  
              wp_enqueue_style( 'mg_progress-bar' );
          }          
       }
	 
		/* Our variables from the widget settings. */
        $title = apply_filters('widget_title', $instance['title'], 10, 3 );
	    $campaign = $instance['campaign'];
		$style = $instance['belowHTML']; 
		$style_above = $instance['aboveHTML'];
        $link = $instance['link'];
        $btnclass = $instance['btnclass'];
	    $btnstyle = $instance['btnstyle'];
        $btntext = $instance['btntext'];
		
		/** New attributes **/
		$borderRadius = array();
		$borderRadius[0] = $instance['border_radius1']; 
		$borderRadius[1] = $instance['border_radius2']; 
		$borderRadius[2] = $instance['border_radius3']; 
		$borderRadius[3] = $instance['border_radius4']; 
		
		$boxshadow_color = array();
		$boxshadow_color[0] = $instance['boxshadow_color1'];
		$boxshadow_color[1] = $instance['boxshadow_color2'];
		$boxshadow_color[2] = $instance['boxshadow_color3'];
		$boxshadow_color[3] = $instance['boxshadow_color4'];
		$boxshadow_color[4] = $instance['boxshadow_color5'];
		
		$barcolor = $instance['barcolor'];
		$well_background = $instance['well_background'];
		$well_shadows = $instance['well_shadows'];	
		
		$form_id	= $instance['form_id'];
		
		$effects   = array();
		$effects['stripes'] = false;
		if( $instance['stripes'] == 'on'){ $effects['stripes'] = true; }
		$effects['pulse'] = false;
		if( $instance['pulse'] == 'on'){ $effects['pulse'] = true; }
		$effects['animated_stripes'] = false;
		if( $instance['animated_stripes'] == 'on'){ $effects['animated_stripes'] = true; }
		$effects['percentage'] = false;
		if( $instance['percentage'] == 'on'){ $effects['percentage'] = true; }
        
		$form_id = $instance['form_id'];
		$form_url = $instance['form_url'];
		
        /* Before widget (defined by themes). */
        echo $before_widget;

      echo "<h3 class='widget-title'>";
      echo $title. "<br>";
      echo "</h3>";
	
	$the_widget = migla_widget_progress_bar( $campaign, $borderRadius, $boxshadow_color, $barcolor, 
					$well_background, $well_shadows, $effects ); 
	
	$out_cname		= str_ireplace("[q]", "'", $the_widget[3]);	
	$placeholder	= array( '[amount]'		,'[target]' 	,'[campaign]'	, '[backers]'	, '[percentage]' );
	$replace		= array( $the_widget[1] , $the_widget[2], $out_cname	,$the_widget[4] , $the_widget[5] );	
		  	  
	$style_above_fix = str_ireplace( $placeholder, $replace, $style_above );
    echo "<div class='mg_bar-custom-text' >".$style_above_fix. "</div>";		  

    echo "<div>";
	echo $the_widget[0];
	echo "</div>";

	$style_fix = str_ireplace( $placeholder, $replace, $style);
    echo "<div class='mg_bar-custom-text' >".$style_fix. "</div>";	
  
	  $class2 = "";
      if( $btnstyle == 'GreyButton' )
	  {
        $class2 = ' mg-btn-grey';	  
	  }	

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
	
	function update( $new_instance, $old_instance ) 
	{
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['campaign'] = strip_tags( $new_instance['campaign'] );

		/* No need to strip tags for.. */
        $instance['belowHTML'] =  $new_instance['belowHTML'] ;	
        $instance['aboveHTML'] =  $new_instance['aboveHTML'] ;			
        $instance['link'] =  strip_tags( $new_instance['link'] ) ;		
        $instance['btnclass'] =  strip_tags( $new_instance['btnclass'] );
        $instance['btnstyle'] =  strip_tags( $new_instance['btnstyle'] );
        $instance['btntext'] = $new_instance['btntext'];
		
		/*new*/
		$instance['border_radius1'] = $new_instance['border_radius1'];
		$instance['border_radius2'] = $new_instance['border_radius2'];
		$instance['border_radius3'] = $new_instance['border_radius3'];
		$instance['border_radius4'] = $new_instance['border_radius4'];
		
		$instance['boxshadow_color1'] = $new_instance['boxshadow_color1'];
		$instance['boxshadow_color2'] = $new_instance['boxshadow_color2'];
		$instance['boxshadow_color3'] = $new_instance['boxshadow_color3'];
		$instance['boxshadow_color4'] = $new_instance['boxshadow_color4'];
		$instance['boxshadow_color5'] = $new_instance['boxshadow_color5']; //RGBA
		
		$instance['barcolor']        = $new_instance['barcolor'] ;
		$instance['well_background'] = $new_instance['well_background'];
		$instance['well_shadows']    = $new_instance['well_shadows'] ;	

		$instance['stripes']         = $new_instance['stripes'] ;
		$instance['pulse']           = $new_instance['pulse'];
		$instance['animated_stripes'] = $new_instance['animated_stripes'];
		$instance['percentage']       = $new_instance['percentage'];
		
		$instance['form_id']       = $new_instance['form_id'];
        $instance['form_url']       = $new_instance['form_url'];
		
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
	 
	function form( $instance ) 
	{

     // Check values 
    if( $instance ) 
	{ 
       $title = esc_attr($instance['title']); 
       $campaign = esc_attr($instance['campaign']); 
       $belowHTML = $instance['belowHTML'] ; 
	   $aboveHTML = $instance['aboveHTML'] ; 
	   
       $link = esc_attr($instance['link']); 
       $btnclass = esc_attr($instance['btnclass']); 
       $btnstyle = esc_attr($instance['btnstyle']); 
       $btntext = esc_attr($instance['btntext']);
	   
	   $border_radius1 = $instance['border_radius1'];
	   $border_radius2 = $instance['border_radius2'];
	   $border_radius3 = $instance['border_radius3'];
	   $border_radius4 = $instance['border_radius4'];
		
		$boxshadow_color1 = $instance['boxshadow_color1'];
		$boxshadow_color2 = $instance['boxshadow_color2'];
		$boxshadow_color3 = $instance['boxshadow_color3'];
		$boxshadow_color4 = $instance['boxshadow_color4'];
		$boxshadow_color5 = $instance['boxshadow_color5']; //RGBA
		
		$barcolor        = $instance['barcolor'] ;
		$well_background = $instance['well_background'];
		$well_shadows    = $instance['well_shadows'] ;	

		$stripes          = $instance['stripes'] ;
		$pulse            = $instance['pulse'];
		$animated_stripes = $instance['animated_stripes'];
		$percentage       = $instance['percentage'];
		
		$form_id		= $instance['form_id'];
		$form_url		= $instance['form_url'];
     }
	
     if( !isset($instance['title']) )  
		$title = "Total Donations Progress Bar"; 
     if( !isset($instance['campaign']) )   
		$campaign = ''; 
     if( !isset($instance['belowHTML']) )   
		$belowHTML = ''; 
	 if( !isset($instance['aboveHTML']) ) 
		$aboveHTML = '';
     if( !isset($instance['link']) )   
		$link = ''; 
     if( !isset($instance['btnclass']) )   
		$btnclass = ''; 
     if( !isset($instance['btnstyle']) )   
		$btnstyle = ''; 
     if( !isset($instance['btntext']) )   
		$btntext = '';
	 if( !isset($instance['border_radius1']) )   
		$border_radius1 = 8;
	 if( !isset($instance['border_radius2']) )   
		$border_radius2 = 8;
	 if( !isset($instance['border_radius3']) )   
		$border_radius3 = 8;
	 if( !isset($instance['border_radius4']) )   
		$border_radius4 = 8;
		
	if( !isset($instance['boxshadow_color1']) ) 	
		$boxshadow_color1 = 8;
	if( !isset($instance['boxshadow_color2']) ) 	
		$boxshadow_color2 = 8;
	if( !isset($instance['boxshadow_color3']) ) 	
		$boxshadow_color3 = 8;
	if( !isset($instance['boxshadow_color4']) ) 	
		$boxshadow_color4 = 8;
	if( !isset($instance['boxshadow_color5']) ) 	
		$boxshadow_color5 = '#969899'; //RGBA
		
	if( !isset($instance['barcolor']) ) 	
		$barcolor        = '#428bca';
	if( !isset($instance['well_background']) )	
		$well_background = '#bec7d3';
	if( !isset($instance['well_shadows']) )	
		$well_shadows    = '#969899';

	if( !isset($instance['stripes']) )	
		$stripes          = false;
	if( !isset($instance['pulse']) )	
		$pulse            = false;
	if( !isset($instance['animated_stripes']) )	
		$animated_stripes = false;
	if( !isset($instance['percentage']) )	
		$percentage       = false;
	
	if( !isset($instance['form_id']) )	
		$form_id = '';

	if( !isset($instance['form_url']) )	
		$form_url = '';
	
?>

	<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title of the progress bar:', 'localization') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" />
		</p>

<?php		

	$fund_array = (array)get_option( 'migla_campaign' );
?>	
	
<p><label ><?php _e('Current Campaign : '  , 'localization') ?></label>  
<label ><?php $c_name = str_replace( "[q]", "'", $campaign ); echo $c_name; ?></label></p>  

<p><label ><?php _e('Choose a campaign to show :', 'localization') ?></label></p>

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

<p style='display:none'><label ><?php _e('Form ID : '  , 'localization') ?></label>  
<label ><input disabled type='text' class='mg_form_id' value='<?php echo $form_id; ?>' 
 name='<?php echo $this->get_field_name( 'form_id' ); ?>' id='<?php echo $this->get_field_id( 'form_id' )?>' ></label></p>  
 
<p style='display:none'><label ><?php _e('URL : '  , 'localization') ?></label>  
<label class='mg_form_url' name='<?php echo $this->get_field_name( 'form_url' ); ?>' id='<?php echo $this->get_field_id( 'form_url' )?>'> 
<?php echo $form_url; ?></label></p>   

		<p>
		  <label ><?php _e('Border:', 'localization') ?></label>
		</p>
		
		<p> <label ><?php _e('Top Left:', 'localization') ?></label>
          <input maxlength="2" size="2" type="number" id="<?php echo $this->get_field_id( 'border_radius1' ); ?>" name="<?php echo $this->get_field_name( 'border_radius1' ); ?>" value="<?php echo $border_radius1; ?>" /></p>

          <p><label ><?php _e('Top Right:', 'localization') ?></label>
          <input maxlength="2" size="2" type="number" id="<?php echo $this->get_field_id( 'border_radius2' ); ?>" name="<?php echo $this->get_field_name( 'border_radius2' ); ?>" value="<?php echo $border_radius2; ?>" /></p>

          <p><label ><?php _e('Bottom Left:', 'localization') ?></label>
		  <input maxlength="2" size="2" type="number" id="<?php echo $this->get_field_id( 'border_radius3' ); ?>" name="<?php echo $this->get_field_name( 'border_radius3' ); ?>" value="<?php echo $border_radius3; ?>" />
          </p>

          <p><label ><?php _e('Bottom Right:', 'localization') ?></label>
          <input maxlength="2" size="2" type="number" id="<?php echo $this->get_field_id( 'border_radius4' ); ?>" name="<?php echo $this->get_field_name( 'border_radius4' ); ?>" value="<?php echo $border_radius4; ?>" />
		</p>

		<p>
		  <label ><?php _e('Well Box Shadow:', 'localization') ?></label>
		</p>

		<p> <label ><?php _e('H-Shadow:', 'localization') ?></label>
          <input maxlength="3" size="3" type="number" id="<?php echo $this->get_field_id( 'boxshadow_color1' ); ?>" name="<?php echo $this->get_field_name( 'boxshadow_color1' ); ?>" value="<?php echo $boxshadow_color1; ?>" /></p>

          <p> <label ><?php _e('V-Shadow:', 'localization') ?></label>
          <input maxlength="3" size="3" type="number" id="<?php echo $this->get_field_id( 'boxshadow_color2' ); ?>" name="<?php echo $this->get_field_name( 'boxshadow_color2' ); ?>" value="<?php echo $boxshadow_color2; ?>" /></p>

         <p> <label ><?php _e('Blur:', 'localization') ?></label>
          <input maxlength="3" size="3" type="number" id="<?php echo $this->get_field_id( 'boxshadow_color3' ); ?>" name="<?php echo $this->get_field_name( 'boxshadow_color3' ); ?>" value="<?php echo $boxshadow_color3; ?>" /></p>

         <p> <label ><?php _e('Spread:', 'localization') ?></label>
          <input maxlength="2" size="2" type="number" id="<?php echo $this->get_field_id( 'boxshadow_color4' ); ?>" name="<?php echo $this->get_field_name( 'boxshadow_color4' ); ?>" value="<?php echo $boxshadow_color4; ?>" /></p>

          <p> <label ><?php _e('Well Box Shadow Color:', 'localization') ?></label>
          <input maxlength="7" size="7" type="text" class='mg-color-field widefat' id="<?php echo $this->get_field_id( 'boxshadow_color5' ); ?>" name="<?php echo $this->get_field_name( 'boxshadow_color5' ); ?>" value="<?php echo $boxshadow_color5; ?>" />
		</p>		

 		<p>
		  <label ><?php _e('Bar Color:', 'localization') ?></label>
		  <input type="text" maxlength="7" size="7" class='mg-color-field widefat' id="<?php echo $this->get_field_id( 'barcolor' ); ?>" name="<?php echo $this->get_field_name( 'barcolor' ); ?>" value="<?php echo $barcolor; ?>" />
		</p>		

 		<p>
		  <label ><?php _e('Well Background:', 'localization') ?></label>
		  <input type="text" maxlength="7" size="7" class='mg-color-field widefat' id="<?php echo $this->get_field_id( 'well_background' ); ?>" name="<?php echo $this->get_field_name( 'well_background' ); ?>" value="<?php echo $well_background; ?>" />
		</p>

 		<p>
		  <label ><?php _e('Well Shadows:', 'localization') ?></label>
		  <input type="text" maxlength="7" size="7" class='mg-color-field widefat' id="<?php echo $this->get_field_id( 'well_shadows' ); ?>" name="<?php echo $this->get_field_name( 'well_shadows' ); ?>" value="<?php echo $well_shadows; ?>" />
		</p>	

 		<p>
		  <label ><?php _e('Stripes Effect:', 'localization') ?></label>
                 <?php
         if( $stripes == 'on' ){
           echo "<input value='on' checked='checked' type='checkbox' id='".$this->get_field_id( 'stripes' )."' name='".$this->get_field_name( 'stripes' )."' value='".$stripes."' />";
         }else{
           echo "<input value='on' type='checkbox' id='".$this->get_field_id( 'stripes' )."' name='".$this->get_field_name( 'stripes' )."' value='".$stripes."' />";
         }
                 ?>
		</p>			
 		<p>
		  <label ><?php _e('Animated Stripes Effect (stripes must be on):', 'localization') ?></label>
                <?php
         if( $animated_stripes == 'on' ){
           echo "<input value='on' checked='checked' type='checkbox' id='".$this->get_field_id( 'animated_stripes' )."' name='".$this->get_field_name( 'animated_stripes' )."' value='".$animated_stripes."' />";
         }else{
           echo "<input value='on' type='checkbox' id='".$this->get_field_id( 'animated_stripes' )."' name='".$this->get_field_name( 'animated_stripes' )."' value='".$animated_stripes."' />";
         }
                 ?>		  
		</p>			
 		<p>
		  <label ><?php _e('Pulse Effect:', 'localization') ?></label>
                <?php
         if( $pulse == 'on' ){
           echo "<input value='on' checked='checked' type='checkbox' id='".$this->get_field_id( 'pulse' )."' name='".$this->get_field_name( 'pulse' )."' value='".$pulse."' />";
         }else{
           echo "<input value='on' type='checkbox' id='".$this->get_field_id( 'pulse' )."' name='".$this->get_field_name( 'pulse' )."' value='".$pulse."' />";
         }
                 ?>			  
		</p>			
 		<p>
		  <label ><?php _e('Percentage:', 'localization') ?></label>
                <?php
         if( $percentage == 'on' ){
           echo "<input value='on' checked='checked' type='checkbox' id='".$this->get_field_id( 'percentage' )."' name='".$this->get_field_name( 'percentage' )."' value='".$percentage."' />";
         }else{
           echo "<input value='on' type='checkbox' id='".$this->get_field_id( 'percentage' )."' name='".$this->get_field_name( 'percentage' )."' value='".$percentage."' />";
         }
                 ?>				  
		</p>			
		
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
      
      <p> <label for="<?php echo $this->get_field_id('aboveHTML'); ?>"><?php _e('Add HTML or Plain Text above:', 'localization') ?></label>
       <textarea  class="widefat"  id="<?php echo $this->get_field_id( 'aboveHTML' ); ?>" name="<?php echo $this->get_field_name( 'aboveHTML' ); ?>"  ><?php echo $aboveHTML; ?></textarea><small><?php _e('shortcodes allowed: [campaign] [percentage] [backers] [amount]', 'localization') ?></small></p>

       <p> <label for="<?php echo $this->get_field_id('belowHTML'); ?>"><?php _e('Add HTML or Plain Text below:', 'localization') ?></label>
       <textarea  class="widefat"  id="<?php echo $this->get_field_id( 'belowHTML' ); ?>" name="<?php echo $this->get_field_name( 'belowHTML' ); ?>"  ><?php echo $belowHTML; ?></textarea><small><?php _e('shortcodes allowed: [campaign] [percentage] [backers] [amount]', 'localization') ?></small></p>

	<?php
	}
}
?>