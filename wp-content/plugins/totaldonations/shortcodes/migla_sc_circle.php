<?php
/*****    Migla_Circle_ProgressBar_Shortcode     ********/

class Migla_Circle_ProgressBar_Shortcode{
	static $progressbar_script;

 static function init() {
	  add_shortcode('totaldonations_circle_bar', array(__CLASS__, 'handle_shortcode'));
	  add_action('init', array(__CLASS__, 'register_script'));
	  add_action('wp_footer', array(__CLASS__, 'print_script'));
 }

 static function handle_shortcode($atts){

   self::$progressbar_script = true;

   extract(shortcode_atts(array(
        'id' 			=> '', 
		'campaign'		=> '',
        'button' 		=> 'no',
        'button_text' 	=> 'Donate Now',
        'button_class' => ''
    ), $atts )
   );  

  $draw = "";
  if( !isset($atts['button']) )	$atts['button'] = 'no';
  if( !isset($atts['button_text']) )	$atts['button_text'] = 'Donate';
  if( !isset($atts['id']) )	$atts['id'] = '';
  if( !isset($atts['campaign']) )	$atts['campaign'] = '';
  if(!isset($atts['button_class'])) 	$atts['button_class'] = '';

  if( $atts['id']=='' && $atts['campaign']=='' )
  {	
	$draw = 'Add attribute ID or the campaign ID so the circle progress bar can be shown';
	
  }else{
	
	if( !empty($atts['campaign']) )
	{
	  
		$cname = str_ireplace("'","[q]", $atts['campaign'] );
		$draw = migla_sc_circle_progressbar( $cname, '', $atts['button'], $atts['button_text'], '', rand(), $atts['button_class'] );
		
	}else if( $atts['id'] != '' )
	{
			$sql 			= 'SELECT post_title FROM wp_posts where ID = ' . $atts['id'] . ' order by ID desc';
			global $wpdb;
			$post_titles 	= $wpdb->get_results( $sql );
		
			if( !empty($post_titles) )
			{
				$cname 			= $post_titles[0]->post_title;
				$cname			= str_ireplace( "'", "[q]", 	$cname );
				$draw = migla_sc_circle_progressbar( $cname, '', $atts['button'], $atts['button_text'], '', rand(), $atts['button_class'] );
			}else{
				$draw = 'The ID doesn\'t exist';	
			}
	}else{
		$draw = 'Add attribute ID or the campaign ID so the circle progress bar can be shown';	
	}
	
  }

 return $draw;
 
}//function

 static function register_script() {
	wp_register_script( 'migla-circle-progress-js', plugins_url( '/js/circle-progress.js' , dirname(__FILE__)) ,'','1.1', true);
	wp_enqueue_script( 'migla-circle-progress-js' );
 }

 static function print_script() {
   if ( ! self::$progressbar_script )
 	return;
		
	add_action('wp_enqueue_scripts', array(__CLASS__, 'register_script') );	
  }
}

Migla_Circle_ProgressBar_Shortcode::init();
?>