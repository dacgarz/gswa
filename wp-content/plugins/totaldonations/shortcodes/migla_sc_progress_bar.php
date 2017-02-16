<?php

/*****    Migla_ProgressBar_Shortcode      ********/

class Migla_ProgressBar_Shortcode {
  static $progressbar_script;

 static function init() {
  add_shortcode('totaldonations-progress-bar', array(__CLASS__, 'handle_shortcode'));
  add_action('init', array(__CLASS__, 'register_script'));
  add_action('wp_footer', array(__CLASS__, 'print_script'));
 }

 static function handle_shortcode($atts){

   self::$progressbar_script = true;

	$args = shortcode_atts( 
		array(
			'id' 	=> '',
			'campaign' => '', 
			'button' => 'no',
			'button_text' => 'Donate Now',
			'text' => '',
                        'button_class' => ''
		), 
		$atts
	);

 $draw = "";
		
		if(!isset($args['button'])) 		$args['button'] = 'no';
		if(!isset($args['button_text'])) 	$args['button_text'] = 'Donate';
		if(!isset($args['text'])) 			$args['text'] = 'no';
		if(!isset($args['button_class'])) 	$args['button_class'] = '';
		if( !isset($args['id']) )			$args['id'] = '';
		if( !isset($args['campaign']) )		$args['campaign'] = ''; 
		
		if( $args['id']=='' && $args['campaign']=='' )
		{	
			$draw = __("Add attribute ID or the campaign ID so the progress bar can be shown", "migla-donation");
			
		}else if( $args['id'] != '' )
		{
				$sql 			= 'SELECT post_title FROM wp_posts where ID = ' . $args['id'] . ' order by ID desc';
				global $wpdb;
				$post_titles 	= $wpdb->get_results( $sql );
				
				if( !empty($post_titles) )
				{
					$cname 			= $post_titles[0]->post_title;
					$cname			= str_ireplace( "'", "[q]", 	$cname );
					$draw = migla_shortcode_progressbar( $cname, $args['button'] , $args['button_text'], $args['text'], $args['button_class'] );
				}else{
					$draw = 'The ID doesn\'t exist';							
				}
				
		}else if( $args['campaign'] != '' )
		{
			$cname 	= str_ireplace("'","[q]", $atts['campaign'] );
			$draw 	= migla_shortcode_progressbar( $cname, $args['button'] , $args['button_text'], $args['text'], $args['button_class'] );
		}else{
			$draw = '';
		}


	return $draw;
 
}//function

 static function register_script() {

 }

 static function print_script() {
   if ( ! self::$progressbar_script )
	return;

          if( wp_script_is( 'mg_progress-bar', 'queue' ) ){
          }else{
              wp_enqueue_style( 'mg_progress-bar', plugins_url( 'totaldonations/css/mg_progress-bar.css' , dirname(__FILE__)) );
          }          

  }
}

Migla_ProgressBar_Shortcode::init();


?>