<?php

 /*
 * Plugin Name: Total Donations Recent Donor Wall
 * Plugin URI: http://calmar-webmedia.com/testing-area/wp-plugin-dev
 * Description: A widget that displays list of donations in Total Donations.
 * Version: 1.0
 * Author: Binti Brindamour and Astried Silvanie
 * Author URI: http://calmar-webmedia.com/
 * License: Licensed
 */

/*
 * Add function to widgets_init that'll load our widget.
 */
add_action( 'widgets_init', 'totaldonations_donation_widget' );

/*
 * Register widget.
 */
function totaldonations_donation_widget() {
	register_widget( 'Totaldonations_donation_Widget' );
}


/*
 * Widget class.
 */
class totaldonations_donation_widget extends WP_Widget {

	/* ---------------------------- */
	/* -------- Widget setup -------- */
	/* ---------------------------- */
	
	function __construct(){
	
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'totaldonations_donation_widget', 'description' => __('A widget that displays list of recent donations in total donation', 'localization') );

		 /* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'totaldonations_donation_widget' );

		/* Create the widget. */
		WP_Widget::__construct( 'totaldonations_donation_widget', __('Total Donations Recent Donors Wall','localization'), $widget_ops, $control_ops );
	}

	/* ---------------------------- */
	/* ------- Display Widget -------- */
	/* ---------------------------- */
	
	function widget( $args, $instance ) {
		extract( $args );

          if( wp_script_is( 'mg_progress-bar', 'queue' ) ){
          }else{
              wp_enqueue_style( 'mg_progress-bar',  plugins_url( 'totaldonations/css/mg_progress-bar.css' , dirname(__FILE__)) );
          }          

		/* Our variables from the widget settings. */
        $title = apply_filters('widget_title', $instance['title'], 10, 3 );
        $numberOfRecords = $instance['numberOfRecords'];
        $filter      = $instance['filter'];
        $donationType = $instance['donationType'];
        $link        = $instance['link'];
        $btnclass    = $instance['btnclass'];
		$btnstyle    = $instance['btnstyle'];
        $btntext     = $instance['btntext'];
        $language    = $instance['language'];
        $hide_date   = $instance['hide_date'];
        $date_format = $instance['date_format'];
        $urlLink     = $instance['urlLink'];
        $showAnon     = $instance['showAnon'];
        $campaign    = $instance['campaign'];
        $show_honoree = $instance['showhonoree'];

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
	  $show_honoree = 'no'; if( $show_honoree == 'on' ){ $show_honoree = 'yes'; }
    
    $get_list = migla_donor_recent( $posttype, $numberOfRecords, $show_anon, $campaign ,$show_honoree );
	$order 	= (array)$get_list[0];
	$data 	= (array)$get_list[1];	  	  
    rsort($order);	 
		
      $row = 1; 
      
      echo "<div class='bootstrap-wrapper mg_latest_donations_widget ".$BtnExisted."'><div class='mg_donations_wrap'> ";

      setlocale(LC_TIME, $language );
      $my_locale = get_locale();

	$row_order = 0;  
    foreach( $order as $order_datum )
	{
        if( $row > $numberOfRecords ){ break; }
		
		$ij = $order[$row_order];
  
         echo "<section class='mg_widgetPanel'>";
         echo "<input type='hidden' id='rec".$row."' value='".$data[$ij]['post_id']."'>";

         if($hide_date == 'on'){
               echo "<div class='mg_widgetDate pull-right'></div>";
         }else{
              echo "<div class='mg_widgetDate pull-right'>". strftime( $date_format , date(strtotime($data[$ij]['date'])) ). "</div>";
         }

         echo "<div class='mg_widgetAmount'>".$b.number_format( (float)$data[$ij]['amount'], $dec , $decsep, $thousep ) .$a. "</div>";

         echo "<div class='mg_widgetName'>". $data[$ij]['firstname'] . "&nbsp;" . $data[$ij]['lastname']. " ";

         echo "</div></section>";
         
		 $row++;
		 $row_order++;
		 
     }

      echo "</div></div>";

     setlocale(LC_TIME, $my_locale );


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
          $instance['title']           = strip_tags( $new_instance['title'] );
          $instance['numberOfRecords'] = strip_tags( $new_instance['numberOfRecords'] );	
          $instance['filter']          = strip_tags( $new_instance['filter'] );	
          $instance['donationType']    = strip_tags( $new_instance['donationType'] );

          $instance['link']        =  strip_tags( $new_instance['link'] ) ;		
          $instance['btnclass']    =  strip_tags( $new_instance['btnclass'] );
          $instance['btnstyle']    =  strip_tags( $new_instance['btnstyle'] );
          $instance['btntext']     = strip_tags( $new_instance['btntext'] );
          $instance['language']    = $new_instance['language'];
          $instance['hide_date']   = $new_instance['hide_date'];
          $instance['date_format'] = $new_instance['date_format'];
          $instance['urlLink']     = strip_tags( $new_instance['urlLink'] );
          $instance['showAnon']     = strip_tags( $new_instance['showAnon'] );
		  $instance['campaign']     = strip_tags( $new_instance['campaign'] );
		  $instance['showhonoree']     = strip_tags( $new_instance['showhonoree'] );

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
          $title           = esc_attr($instance['title']); 
          $numberOfRecords = $instance['numberOfRecords'];
          $filter          = $instance['filter'];
          $donationType    = $instance['donationType'];
          $link            = esc_attr($instance['link']); 
          $btnclass        = esc_attr($instance['btnclass']); 
          $btnstyle        = esc_attr($instance['btnstyle']); 
          $btntext         = esc_attr($instance['btntext']);
          $language        = $instance['language'];
          $hide_date       = $instance['hide_date'];
          $date_format     = $instance['date_format'];
          $urlLink         = $instance['urlLink'];
          $showAnon        = $instance['showAnon'];
          $campaign        = $instance['campaign'];
          $show_honoree    = $instance['showhonoree'];		  
      } else { 
          $title = "Total Donations Donor Wall"; 
          $numberOfRecords  = 10;
          $filter           = 'recent';
          $donationType     = 'online';
          $link             = ''; 
          $btnclass         = ''; 
          $btnstyle         = ''; 
          $btntext          = '';
          $language         = '';
          $hide_date        = '';
          $date_format      = '%B %d %Y';
          $urlLink          = get_option('migla_form_url');
          $showAnon         = '';
          $campaign        = '';
          $show_honoree    = '';			  
      } 
?>

	<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title of the Donor Wall:', 'localization') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" />
		</p>

		
		
<p>
   
<label ><?php _e('Current Campaign : '  , 'localization') ?></label>  
<label ><?php $c_name = str_replace( "[q]", "'", $campaign ); echo $c_name; ?></label>  

<br><label ><?php _e('Choose a campaign to show :', 'localization') ?></label>
   
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
        <input input='text' class='widefat' type='number' min='1' max='10' id="<?php echo $this->get_field_id( 'numberOfRecords' ); ?>" name="<?php echo $this->get_field_name( 'numberOfRecords' ); ?>" value="<?php echo $numberOfRecords; ?>"></input></div> 
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
	  
       <p>
      <?php if( $show_honoree == 'on'){  ?> 
        <div><input type="checkbox" checked="checked" id="<?php echo $this->get_field_id( 'showhonoree' ); ?>" name="<?php echo $this->get_field_name( 'showhonoree' ); ?>">
        <label>Show Honoree's name instead of donor's name? </label></div>
      <?php }else{  ?> 
        <div><input type="checkbox" id="<?php echo $this->get_field_id( 'showhonoree' ); ?>" name="<?php echo $this->get_field_name( 'showhonoree' ); ?>">
        <label>Show Honoree's name instead of donor's name? </label></div>
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
	<label for="<?php echo $this->get_field_id( 'btntext' ); ?>"><?php _e('Text of button:', 'localization') ?></label>
	<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'btntext' ); ?>" name="<?php echo $this->get_field_name( 'btntext' ); ?>" value="<?php echo $btntext; ?>" />
      </p>

        <p>
        <div><label>Url that link will open:</label>
        <input input='text' class='widefat' type='text' id="<?php echo $this->get_field_id( 'urlLink' ); ?>" name="<?php echo $this->get_field_name( 'urlLink' ); ?>" value="<?php echo $urlLink;  ?>"></input></div> 
        </p>

      <p>
	<label for="<?php echo $this->get_field_id( 'hide_date' ); ?>"><?php _e('Hide Date:', 'localization') ?></label>
         <?php  
            if($hide_date == 'on'){
         ?>
 	  <input type="checkbox" class="widefat" id="<?php echo $this->get_field_id( 'hide_date' ); ?>" name="<?php echo $this->get_field_name( 'hide_date' ); ?>" checked />
        <?php }else{ ?>
          <input type="checkbox" class="widefat" id="<?php echo $this->get_field_id( 'hide_date' ); ?>" name="<?php echo $this->get_field_name( 'hide_date' ); ?>" />
        <?php } ?>
      </p>

     <p><label>Choose language for date:</label> 
     <select id="<?php echo $this->get_field_id( 'language' ); ?>" name="<?php echo $this->get_field_name( 'language' ); ?>" class="widefat migla_select">
     <?php
        $lang = (array)migla_get_local();
        $keys = array_keys($lang); $i = 0;
        foreach( (array)$lang as $value)
        {
           if( $value == $language )
           {
             echo "<option value='".$value."' selected>". $keys[$i] ." ( ". $value .") </option>";
           }else{
             echo "<option value='".$value."' >". $keys[$i] ." ( ". $value .") </option>";
           }
           $i++;
        }
     ?>
     </select>
	</p>


     <p><label>Choose format for date:</label> 
     <select id="<?php echo $this->get_field_id( 'date_format' ); ?>" name="<?php echo $this->get_field_name( 'date_format' ); ?>" class="widefat migla_select">
     <?php

        $df = array('%B %d %Y', '%b %d %Y', '%B %d, %Y', '%b %d, %Y' , '%d %B %Y', '%d %b %Y' ,'%Y-%m-%d', '%m/%d/%Y');

        $keys = array_keys($df); $i = 0;

        setlocale(LC_TIME, $language);

        foreach( $df as $value )
        {
           if( $value == $date_format )
           {
             echo "<option value='".$value."' selected>". strftime($value, time())  . "</option>";
           }else{
             echo "<option value='".$value."' >". strftime($value , time())  . "</option>";
           }
           $i++;
        }
     ?>
     </select>
	</p>      

<?php
  }
}
?>