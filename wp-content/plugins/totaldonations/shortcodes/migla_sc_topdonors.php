<?php

/*****  Top Donor Shortcode   ***********/
class Migla_TopDonors_Shortcode {
  static $progressbar_script;

 static function init() {
  add_shortcode('totaldonations-top-donors', array(__CLASS__, 'handle_shortcode'));
  add_action('init', array(__CLASS__, 'register_script'));
  add_action('wp_footer', array(__CLASS__, 'print_script'));
 }

 static function handle_shortcode($atts){

   self::$progressbar_script = true;

	$args = shortcode_atts( 
		array(
				'title'     => '',
				'num_rec'   =>  5,
				'donation_type' =>  '',
				'use_link'  => '',
				'btn_class' => '',
				'btn_style' =>  '',
				'btn_text'  => '',
				'url_link'   => '',
				'show_anonymous' =>'',
				'campaign' => ''
		), 
		$atts
	);	

 $draw = "";
 
 if( !isset($args['campaign']) ){  
	$args['campaign'] = '';
 }else{
	$old_one	= $args['campaign'];
	$args['campaign'] = str_ireplace("'", "[q]", $old_one);
 }
 
 if( !isset($args['title']) ) 			$args['title'] = '';
 if( !isset($args['num_rec']) ) 		$args['num_rec'] = 5;
 if( !isset($args['donation_type']) ) 	$args['donation_type'] = '';
 if( !isset($args['use_link']) ) 		$args['use_link'] = '';
 if( !isset($args['btn_class']) ) 		$args['btn_class'] = '';
 if( !isset($args['btn_style']) ) 		$args['btn_style'] = '';
 if( !isset($args['btn_text']) ) 		$args['btn_text'] = '';
 if( !isset($args['url_link']) ) 		$args['url_link'] = '';
 if( !isset($args['show_anonymous']) ) 	$args['show_anonymous'] = '';
 
    $draw = mg_draw_topdonors( 	$args['title'], 
								$args['num_rec'], 
								$args['donation_type'] , 
								$args['use_link'], 
								$args['btn_class'], 
								$args['btn_style'], 
								$args['btn_text'] , 
								$args['url_link'], 
								$args['show_anonymous']  , 
								$args['campaign'] );

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

Migla_TopDonors_Shortcode::init();


?>