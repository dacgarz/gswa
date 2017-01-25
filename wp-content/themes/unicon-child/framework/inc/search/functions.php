<?php


add_filter( 'the_permalink', function( $permalink, $post ) {
  //if ( is_search() && 'application/pdf' == get_post_mime_type( $post->ID ) ) {
  if ( is_search() && 'attachment' === get_post_type( $post ) ) {
    $permalink = wp_get_attachment_url( $post->ID );
  }
  return esc_url( $permalink );
}, 10, 2 );