<?php

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {

    /** Style theme */
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'unicon_child_header_css', get_stylesheet_directory_uri() . '/framework/css/header.css');
    wp_enqueue_style( 'unicon_child_footer_css', get_stylesheet_directory_uri() . '/framework/css/footer.css');
    wp_enqueue_style( 'unicon_child_uam_css', get_stylesheet_directory_uri() . '/framework/css/uam.css');

    wp_enqueue_style( 'unicon_child_blog_css', get_stylesheet_directory_uri() . '/framework/css/blog.css');
    wp_enqueue_script( 'unicon_child_matchHeight', get_stylesheet_directory_uri() . '/framework/js/libs/jquery.matchHeight.js', array('jquery'));
    wp_enqueue_script( 'unicon_child_blog_js', get_stylesheet_directory_uri() . '/framework/js/blog.js', array('jquery', 'unicon_child_matchHeight'));

    wp_enqueue_style( 'gotham-fonts', get_stylesheet_directory_uri() . '/framework/fonts/Gotham/Gotham.css');
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