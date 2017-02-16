<?php


class Migla_TextAmountRaised_Shortcode {
  static $progressbar_script;

 static function init() {

  add_shortcode('totaldonations-text-fields', array(__CLASS__, 'handle_shortcode'));
  add_action('init', array(__CLASS__, 'register_script'));
  add_action('wp_footer', array(__CLASS__, 'print_script'));

 }

 static function handle_shortcode($atts){

   self::$progressbar_script = true;

   extract(shortcode_atts(array(
        'campaign' => '', 
        'button' => 'no',
        'button_text' => 'Donate Now',
        'text' => '',
        'button_class' => ''
    ), $atts )
   ); 

$args = shortcode_atts( 
    array(
        'campaign' => '', 
        'button' => 'no',
        'button_text' => 'Donate Now',
        'text' => '',
        'button_class' => ''
    ), 
    $atts
);

 $draw = "";

 $campaign = str_replace("'", "[q]", $args['campaign'] );

 if( $atts == null || count($atts) <= 0)
 {
    $draw = migla_draw_textbarshortcode( $campaign, $args['button'] , $args['button_text'], $args['text'], $args['button_class'] );
 }else{	
   if( $args['campaign'] == "" ){	
      $draw = migla_draw_textbarshortcode( $campaign, $args['button'] , $args['button_text'], $args['text'], $args['button_class'] );
   }else{
      $draw = migla_draw_textbarshortcode( $campaign, $args['button'] , $args['button_text'], $args['text'], $args['button_class'] );
   }
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

Migla_TextAmountRaised_Shortcode::init();


?>