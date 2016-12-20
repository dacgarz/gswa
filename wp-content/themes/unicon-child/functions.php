<?php

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles');
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles_last', 100);
function theme_enqueue_styles() {

  /** Style theme */
  wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
  wp_enqueue_style( 'unicon_child_header_css', get_stylesheet_directory_uri() . '/framework/css/header.css', array(), '0.0.1');
  wp_enqueue_style( 'unicon_child_footer_css', get_stylesheet_directory_uri() . '/framework/css/footer.css');

  wp_enqueue_style( 'unicon_child_styles_gulp', get_stylesheet_directory_uri() . '/assets/compiled/css/global.css', array(), '0.0.3');
  wp_enqueue_script( 'unicon_child_js_gulp', get_stylesheet_directory_uri() . '/assets/compiled/js/global.js', array('jquery'), '0.0.1');
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