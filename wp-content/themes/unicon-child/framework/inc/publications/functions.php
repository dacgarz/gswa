<?php

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

add_action( 'init', function() {
  $labels = array(
    'name'              => 'Post Type',
    'singular_name'     => 'Post Type',
    'search_items'      => 'Search Post Type',
    'all_items'         => 'All Post Types',
    'parent_item'       => 'Parent Type',
    'parent_item_colon' => 'Parent Type:',
    'edit_item'         => 'Edit Post Type',
    'update_item'       => 'Update Post Type',
    'add_new_item'      => 'Add New Post Type',
    'new_item_name'     => 'New Post Type',
    'menu_name'         => 'Post Type',
  );
  $args = array(
    'hierarchical'      => TRUE,
    'labels'            => $labels,
    'show_ui'           => true,
    'show_admin_column' => true,
    'query_var'         => FALSE,
    'rewrite'           => array( 'slug' => 'post_type' ),
  );

  register_taxonomy( 'post_type', array( 'post' ), $args );
}, 0 );

add_action( 'wp', function(){
  $term = get_queried_object();
  if (!empty($term)) {
    if ($term->taxonomy == 'post_type') {
      wp_redirect("publications?swp_category_limiter=" . $term->term_id);
    }
  }
}, 100);


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
  $categories = get_terms( array(
    'hide_empty' => false,
    'taxonomy' => 'post_type'
  ) );
  if (!empty($categories) && is_array($categories)) {
    $categories = array_map(function($item){
      return $item->term_id;
    }, $categories);
  }

  if ($get_categories) {
    return $categories;
  }

  $render_array = array(
    'posts' => array(),
    'categories' => $categories
  );
  foreach ($categories as $category_id ) {
    $post = get_recent_post_for_post_type($category_id);
    if (!empty($post)) {
      $render_array['posts'][$category_id] = $post;
    }
  }
  return $render_array;
}

function custom_publications_index_get_labels() {
  return array(
//    53 => 'Print News',
//    51 => 'e-News',
    54 => 'Water Quality<br/> Report Card',
    50 => 'Watershed Blog',
//    52 => 'Past Presentations',
  );
}

function get_recent_post_for_post_type($category_id) {
  $args = array(
    'numberposts' => 1,
    'offset' => 0,
    'orderby' => 'post_date',
    'order' => 'DESC',
    'post_type' => 'post',
    'post_status' => 'publish',
    'suppress_filters' => true,
    'tax_query' => array(
      array(
        'taxonomy' => 'post_type',
        'field' => 'term_id',
        'terms' => $category_id
      )
    )
  );

  $recent_posts = wp_get_recent_posts( $args, OBJECT);
  return (empty($recent_posts) ? NULL : $recent_posts[0]);
}