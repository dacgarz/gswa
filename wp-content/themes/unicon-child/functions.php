<?php

require_once "framework/inc/overrid/shortcodes.php";
require_once "tribe-events/custom/functions.php";
require_once "framework/inc/publications/functions.php";

require_once "framework/inc/overrid/meta-boxes.php";

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles');
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles_last', 100);
function theme_enqueue_styles() {

  /** Style theme */
  wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
  wp_enqueue_style( 'unicon_child_header_css', get_stylesheet_directory_uri() . '/framework/css/header.css', array(), '0.0.1');
  wp_enqueue_style( 'unicon_child_footer_css', get_stylesheet_directory_uri() . '/framework/css/footer.css');

  wp_enqueue_style( 'unicon_child_styles_gulp', get_stylesheet_directory_uri() . '/assets/compiled/css/global.css', array(), '0.0.6');
  wp_enqueue_script( 'unicon_child_js_gulp', get_stylesheet_directory_uri() . '/assets/compiled/js/global.js', array('jquery'), '0.0.4');
}

function theme_enqueue_styles_last() {
  wp_dequeue_style('shortcodes');
  wp_enqueue_style('shortcodes-child', get_stylesheet_directory_uri() . '/framework/css/shortcodes-child.css');
}


// Custom Excerpt Length
function minti_custom_excerpt_child($limit=50, $read_more = TRUE) {
  if($read_more) {
    return strip_shortcodes(wp_trim_words(get_the_content(), $limit, '... <a class="read-more-link" href="'. get_permalink() .'">' . __('read more', 'minti') . '  &rarr;</a>'));
  } else {
    return strip_shortcodes(wp_trim_words(get_the_content(), $limit));
  }
}
function cc_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');


function is_tribe_calendar() {
  if ( tribe_is_event_query() || tribe_is_event_organizer() || tribe_is_event_venue() ) {
    return TRUE;
  }
  return FALSE;
}

function remove_redux_section($section) {
  if (isset($section['4']['fields'])) {
    foreach ($section['4']['fields'] as $key => $value) {
      if ($value['id'] == 'switch_stickyheader') {
        unset($section['4']['fields'][$key]);
      }
    }
  }
  return $section;
}
add_filter('redux-sections','remove_redux_section');

define('BLOG_IMAGES', 'post-thumbnail-cropped-new');
add_action( 'init', function() {
  add_image_size('post-thumbnail-link', 300, 400, FALSE);
  add_image_size(BLOG_IMAGES, 288, 229, TRUE);
});

//HIDE EVENT PRICE
add_filter('tribe_events_admin_show_cost_field', '__return_false', 100);

function custom_generate_style_for_header($post_id, $is_event = FALSE) {
  if ($is_event) {
    $style = Tribe__Settings_Manager::get_option('index_settings_title_style');
  } else {
    $style = get_post_meta( $post_id, 'minti_headertext_style', true );
  }
  if (!empty($style)) {
    print "style='$style'";
  }
  return NULL;
}

function custom_photo_credit_for_header( $post_id, $is_event = FALSE ) {
  if ( $is_event ) {
    $photo_credit = Tribe__Settings_Manager::get_option( 'index_settings_photo_credit' );
  } else {
    $photo_credit = get_post_meta( $post_id, 'minti_headerphoto_credit', TRUE );
  }

  return $photo_credit;
}