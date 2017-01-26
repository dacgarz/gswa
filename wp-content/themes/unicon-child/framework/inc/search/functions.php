<?php

//@TODO CHECK WIKI
//@TODO CHANGE PAGINATION

//@TODO RESPONSIVE CSS

//@TODO MAYBE CHANGE POST TYPE - FORMAT/CUSTOM CATEGORY

add_filter( 'searchwp_basic_auth_creds', function() {
  return array(
    'username' => 'admin', // the HTTP BASIC AUTH username
    'password' => 'dev1'  // the HTTP BASIC AUTH password
  );
} );

add_filter( 'the_permalink', function( $permalink, $post ) {
  //if ( is_search() && 'application/pdf' == get_post_mime_type( $post->ID ) ) {
  if ( is_search() && 'attachment' === get_post_type( $post ) ) {
    $permalink = wp_get_attachment_url( $post->ID );
  }
  return esc_url( $permalink );
}, 10, 2 );

add_action( 'post_updated', function($post_ID, $post_after, $post_before) {
  if (is_object($post_after) && property_exists($post_after, 'post_type') && ($post_after->post_type == 'post')) {
    $format = get_post_format($post_ID);
    if ($format == 'link') {
      $type = get_post_meta( $post_ID, 'minti_blog-link-type', true );
      $file = get_post_meta( $post_ID, 'minti_blog-link-file', true );
      if ($type == 'File' && (!empty($file))) {
        wp_update_post(
          array(
            'ID' => $file,
            'post_parent' => $post_ID
          )
        );
      }
    }
  }
}, 10, 3 );


/**********************************************************************************************************************/

function custom_get_link_post_type($post_id) {
  $type = get_post_meta( $post_id, 'minti_blog-link-type', true );
  if ($type == 'File') {
    $file = get_post_meta( $post_id, 'minti_blog-link-file', true );
    if (!empty($file)) {
      return wp_get_attachment_url($file);
    }
  } else {
    $link = get_post_meta( $post_id, 'minti_blog-link', true );
    if (!empty($link)) {
      return esc_url($link);
    }
  }
  return NULL;
}

function custom_publications_index_content($get_categories = FALSE) {
  $categories = array(
    '39' => array('footer' => 'Previous Presentations'),
    '40' => array('footer' => 'Previous Issues'),
    '41' => array('footer' => 'Previous Issues'),
    '42' => array('footer' => 'Previous Issues'),
    '43' => array('footer' => 'Blog Archive')
  );

  if ($get_categories) {
    return array_keys($categories);
  }

  $render_array = array(
    'posts' => array(),
    'categories' => $categories
  );
  foreach ($categories as $category_id => $data ) {
    $post = get_recent_post_for_category($category_id);
    if (!empty($post)) {
      $render_array['posts'][$category_id] = $post;
    }
  }
  return $render_array;
}

function get_recent_post_for_category($category_id) {
  $args = array(
    'numberposts' => 1,
    'offset' => 0,
    'category' => $category_id,
    'orderby' => 'post_date',
    'order' => 'DESC',
    'post_type' => 'post',
    'post_status' => 'publish',
    'suppress_filters' => true
  );

  $recent_posts = wp_get_recent_posts( $args, OBJECT);
  return (empty($recent_posts) ? NULL : $recent_posts[0]);
}



//add_action('searchwp_log', function($msg){
//  print "<div class='FFFFFFFF' style='outline: 1px solid red'>$msg</div>";
//});