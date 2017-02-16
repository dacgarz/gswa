<?php
 /*
 * Plugin Name: Total Donations Top Donor Wall
 * Plugin URI: http://calmar-webmedia.com/testing-area/wp-plugin-dev
 * Description: A widget that displays list of top donors in Total Donations.
 * Version: 1.0
 * Author: Binti Brindamour and Astried Silvanie
 * Author URI: http://calmar-webmedia.com/
 * License: Licensed
 */
 

/*
 * Add function to widgets_init that'll load our widget.
 */
add_action( 'widgets_init', 'totaldonations_topdonor_widget' );

/*
 * Register widget.
 */
function totaldonations_topdonor_widget() {
	register_widget( 'totaldonations_topdonor_widget' );
}


/*
 * Widget class.
 */
class totaldonations_topdonor_widget extends WP_Widget {

	/* ---------------------------- */
	/* -------- Widget setup -------- */
	/* ---------------------------- */
	
	function __construct(){
	
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'totaldonations_topdonor_widget', 'description' => __('A widget that displays list of top donor in total donation', 'localization') );

		 /* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'totaldonations_topdonor_widget' );

		/* Create the widget. */
		WP_Widget::__construct( 'totaldonations_topdonor_widget', __('Total Donations Top Donor Wall','localization'), $widget_ops, $control_ops );
	}

	/* ---------------------------- */
	/* ------- Display Widget -------- */
	/* ---------------------------- */
	
	function widget( $args, $instance ) {
		extract( $args );

          if( wp_script_is( 'mg_progress-bar', 'queue' ) ){
          }else{
              wp_enqueue_style( 'mg_progress-bar' , plugins_url( 'totaldonations/css/mg_progress-bar.css' , dirname(__FILE__))  );
          }          

		/* Our variables from the widget settings. */
        $title = apply_filters('widget_title', $instance['title'], 10, 3 );
        $numberOfRecords = $instance['numberOfRecords'];
        $filter = $instance['filter'];
        $donationType = $instance['donationType'];
        $link = $instance['link'];
        $btnclass = $instance['btnclass'];
	$btnstyle = $instance['btnstyle'];
        $btntext = $instance['btntext'];
        $urlLink     = $instance['urlLink'];
        $showAnon     = $instance['showAnon'];
        $campaign = $instance['campaign'];

     /* Before widget (defined by themes). */
     echo $before_widget;

      echo "<h3 class='widget-title'>";
      echo $title. "<br>";
      echo "</h3>";
    
     $posttype = 'migla_donation';
     if( $donationType == 'offline' ){ $posttype = 'migla_odonation'; }
     if( $donationType == 'both' ){ $posttype = ''; }

      $symbol = miglaCurrencySymbol();
      $b = ""; $a = "";
      $showdec = get_option('migla_showDecimalSep'); $dec = 0;
      if( $showdec == 'yes' ){ $dec = 2; }
      if( get_option('migla_curplacement') == 'before' ){ $b = $symbol; }else{ $a = $symbol; }
      $thousep = get_option('migla_thousandSep'); $decsep = get_option('migla_decimalSep');
      $data = array();

    if( $link == 'on' ){ $BtnExisted = 'mg_widgetButton'; }else{ $BtnExisted = ''; }
      
      $show_anon = 'no'; if( $showAnon == 'on' ){ $show_anon = 'yes'; }

      $data = migla_donorwall_top($posttype, $numberOfRecords , $show_anon, $campaign );

      $i = 0;

      echo "<ol class='mg_top_donors_widget ".$BtnExisted."'>";
      foreach( (array)$data as $datum ){

          echo "<li>" ;
          echo "<span class='mg_widgetName'>". $datum['firstname'] ."&nbsp;". $datum['lastname'] . " </span>"; 
          echo "<span class='mg_widgetAmount'>".$b.number_format( $datum['total'], $dec , $decsep, $thousep ) .$a. " </span>";
          echo "</li>"; 
          $i++;
          if( $i == $numberOfRecords ){ break; }

      }
      echo "</ol>";


     $class2 = "";
     if( $btnstyle == 'GreyButton' ){  $class2 = ' mg-btn-grey';	  }	  
	
      if( $link=='on' ){
        echo "<form action='".esc_url($urlLink)."' method='post'>";
          if( $btntext == '' ){ $btntext = 'Donate'; }
        echo "<input type='hidden' name='thanks' value='widget_bar' />";
        echo "<button class='migla_donate_now ".$btnclass . $class2."'>".$btntext."</button>";
        echo "</form>";
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
          $instance['numberOfRecords'] = strip_tags( $new_instance['numberOfRecords'] );	
          $instance['filter'] = strip_tags( $new_instance['filter'] );	
          $instance['donationType'] = strip_tags( $new_instance['donationType'] );

          $instance['link'] =  strip_tags( $new_instance['link'] ) ;		
          $instance['btnclass'] =  strip_tags( $new_instance['btnclass'] );
          $instance['btnstyle'] =  strip_tags( $new_instance['btnstyle'] );
          $instance['btntext'] = $new_instance['btntext'];
          $instance['urlLink']     = strip_tags( $new_instance['urlLink'] );
          $instance['showAnon']     = strip_tags( $new_instance['showAnon'] );
		  $instance['campaign']     = strip_tags( $new_instance['campaign'] );

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
      if( $instance) { 
        $title           = esc_attr($instance['title']); 
        $numberOfRecords = $instance['numberOfRecords'];
        $filter          = $instance['filter'];
        $donationType    = $instance['donationType'];
        $link            = esc_attr($instance['link']); 
        $btnclass        = esc_attr($instance['btnclass']); 
        $btnstyle        = esc_attr($instance['btnstyle']); 
        $btntext         = esc_attr($instance['btntext']);
        $urlLink         = $instance['urlLink'];
        $showAnon        = $instance['showAnon'];
		$campaign        = $instance['campaign'];
      } else { 
          $title      = "Total Donations Donor Wall"; 
          $numberOfRecords = 10;
          $filter     = 'recent';
          $donationType = 'online';
          $link       = ''; 
          $btnclass   = ''; 
          $btnstyle   = ''; 
          $btntext    = '';
          $urlLink    = get_option('migla_form_url');
          $showAnon   = '';
		  $campaign   = '';
      } 
?>

	<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title of the Donor Wall:', 'localization') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" />
		</p>
		
<?php
    //Show select on widget 
      $out = "";
      $out .= "<select class='widefat migla_select' name='".$this->get_field_name( 'campaign' )."' id='".$this->get_field_id( 'campaign' )."'>" ;
      $b = "";
      $i = 0;   
      $fund_array = (array)get_option( 'migla_campaign' );
      $undesignated = get_option('migla_undesignLabel');
	
	$out .= "<option value='show_all' selected>".__('Show All Campaign', 'migla-donation')."</option>";
	
	if( strcmp( $undesignated, $campaign ) == 0  ){
	    $out .= "<option value='".$undesignated."' selected>".__( $undesignated, 'migla-donation')."</option>";
	}else{
        $out .= "<option value='".$undesignated."' >".__( $undesignated, 'migla-donation')."</option>";	
	}
	
    if( empty($fund_array[0]) ){ 
    }else{    
	
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
	</p>	
		


        <p>
        <div><label>Type of Donation ? </label><br/>
        <?php if(  $donationType == 'online' ){ ?>
         <label><input type="radio" name="<?php echo $this->get_field_name( 'donationType' ); ?>" value="online" checked />Online Donations</label><br/>
         <label><input type="radio" name="<?php echo $this->get_field_name( 'donationType' ); ?>" value="offline" />Offline Donations</label><br/>
         <label><input type="radio" name="<?php echo $this->get_field_name( 'donationType' ); ?>" value="both" />Both</label><br/>
        <?php } else if(  $donationType == 'offline' ){ ?>
         <label><input type="radio" name="<?php echo $this->get_field_name( 'donationType' ); ?>" value="online" />Online Donations</label><br/>
         <label><input type="radio" name="<?php echo $this->get_field_name( 'donationType' ); ?>" value="offline" checked />Offline Donations</label><br/>
         <label><input type="radio" name="<?php echo $this->get_field_name( 'donationType' ); ?>" value="both" />Both</label><br/>
        <?php }else{ ?>
         <label><input type="radio" name="<?php echo $this->get_field_name( 'donationType' ); ?>" value="online" />Online Donations</label><br/>
         <label><input type="radio" name="<?php echo $this->get_field_name( 'donationType' ); ?>" value="offline" />Offline Donations</label><br/>
        <label> <input type="radio" name="<?php echo $this->get_field_name( 'donationType' ); ?>" value="both" checked />Both</label><br/>
        <?php } ?>
        </div> 
        </p>

        <p>
        <div><label>How many records to show:</label>
        <input input='text' class='widefat' type='number' min='1' max='25' id="<?php echo $this->get_field_id( 'numberOfRecords' ); ?>" name="<?php echo $this->get_field_name( 'numberOfRecords' ); ?>" value="<?php echo $numberOfRecords; ?>"></input></div> 
        </p>

       <p>
      <?php if( $link == 'on'){  ?> 
        <div><input type="checkbox" checked="checked" id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>">
        <label>Add link button ? </label></div>
      <?php }else{  ?> 
        <div><input type="checkbox" id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>">
        <label>Add link button ? </label></div>
      <?php }  ?> 
      </p>

       <p>
      <?php if( $showAnon== 'on'){  ?> 
        <div><input type="checkbox" checked="checked" id="<?php echo $this->get_field_id( 'showAnon' ); ?>" name="<?php echo $this->get_field_name( 'showAnon' ); ?>">
        <label>Show anonymous ? </label></div>
      <?php }else{  ?> 
        <div><input type="checkbox" id="<?php echo $this->get_field_id( 'showAnon' ); ?>" name="<?php echo $this->get_field_name( 'showAnon' ); ?>">
        <label>Show anonymous ? </label></div>
      <?php }  ?> 
      </p>

        <div><label>Add a css class on button: <small>(theme button only)</small></label>
        <input input='text' class='widefat' type='text' id="<?php echo $this->get_field_id( 'btnclass' ); ?>" name="<?php echo $this->get_field_name( 'btnclass' ); ?>" value="<?php echo $btnclass; ?>"></input></div>  
  
     <p><label>Choose a button style:</label> 
     <select id="<?php echo $this->get_field_id( 'btnstyle' ); ?>" name="<?php echo $this->get_field_name( 'btnstyle' ); ?>" class="widefat migla_select">
     <?php if( $btnstyle == "GreyButton" ) { ?>
 	   <option  value="themeDefault">Your Theme Default</option>
       <option selected="" value="GreyButton">Grey Button</option>
	 <?php }else{ ?>
 	   <option selected="" value="themeDefault">Your Theme Default</option>
       <option value="GreyButton">Grey Button</option>	 
	 <?php } ?>
	 </select>
	</p>

        <p>
        <div><label>Url that link will open:</label>
        <input input='text' class='widefat' type='text' id="<?php echo $this->get_field_id( 'urlLink' ); ?>" name="<?php echo $this->get_field_name( 'urlLink' ); ?>" value="<?php echo $urlLink;  ?>"></input></div> 
        </p>

      <p>
	<label for="<?php echo $this->get_field_id( 'btntext' ); ?>"><?php _e('Text of button:', 'localization') ?></label>
	<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'btntext' ); ?>" name="<?php echo $this->get_field_name( 'btntext' ); ?>" value="<?php echo $btntext; ?>" />
      </p>


<?php
  }
}
?>