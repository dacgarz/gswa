<?php

/**
 * A Excerpt method used across the board on our Events Plugin Suite.
 *
 * By default it removes all shortcodes, the reason for this is that shortcodes added by other plugins/themes
 * may not have been registered by the time our ajax responses are generated. To avoid leaving unparsed
 * shortcodes in our excerpts then we strip out anything that looks like one.
 *
 * @category Events
 *
 * @param  WP_Post|int|null $post The Post Object|ID, if null defaults to `get_the_ID()`
 * @param  array $allowed_html The wp_kses compatible array
 *
 * @return string|null Will return null on Bad Post Instances
 */
function custom_tribe_events_get_the_excerpt( $post = null, $allowed_html = null, $excerpt_length = 20 ) {
  // If post is not numeric or instance of WP_Post it defaults to the current Post ID
  if ( ! is_numeric( $post ) && ! $post instanceof WP_Post ) {
    $post = get_the_ID();
  }

  // If not a WP_Post we try to fetch it as one
  if ( is_numeric( $post ) ) {
    $post = WP_Post::get_instance( $post );
  }

  // Prevent Non usable $post instances
  if ( ! $post instanceof WP_Post ) {
    return null;
  }

  // Default Allowed HTML
  if ( ! is_array( $allowed_html ) ) {
    $base_attrs = array(
      'class' => array(),
      'id' => array(),
      'style' => array(),
    );
    $allowed_html = array(
      'a' => array(
        'class' => array(),
        'id' => array(),
        'style' => array(),
        'href' => array(),
        'rel' => array(),
        'target' => array(),
      ),
      'b' => $base_attrs,
      'strong' => $base_attrs,
      'em' => $base_attrs,
      'span' => $base_attrs,
      'ul' => $base_attrs,
      'li' => $base_attrs,
      'ol' => $base_attrs,
    );
  }

  /**
   * Allow developers to filter what are the allowed HTML on the Excerpt
   *
   * @var array Must be compatible to wp_kses structure
   *
   * @link https://codex.wordpress.org/Function_Reference/wp_kses
   */
  $allowed_html = apply_filters( 'tribe_events_excerpt_allowed_html', $allowed_html, $post );

  /**
   * Allow shortcodes to be Applied on the Excerpt or not
   *
   * @var bool
   */
  $allow_shortcodes = apply_filters( 'tribe_events_excerpt_allow_shortcode', false );

  /**
   * Filter to stop removal of shortcode markup in the Excerpt
   * This will remove all text that resembles a shortcode [shortcode 5]
   *
   * @var bool
   */
  $remove_shortcodes = apply_filters( 'tribe_events_excerpt_shortcode_removal', true );

  // Get the Excerpt or content based on what is available
  if ( has_excerpt( $post->ID ) ) {
    $excerpt = $post->post_excerpt;
  } else {
    $excerpt = $post->post_content;
    // We will only trim Excerpt if it comes from Post Content

    /**
     * Filter the number of words in an excerpt.
     *
     * @param int $number The number of words. Default 55.
     */
//    $excerpt_length = apply_filters( 'excerpt_length', 55 );

    /**
     * Filter the string in the "more" link displayed after a trimmed excerpt.
     *
     * @param string $more_string The string shown within the more link.
     */
    $excerpt_more = apply_filters( 'excerpt_more', ' [&hellip;]' );

    // Now we actually trim it
    $excerpt = wp_trim_words( $excerpt, $excerpt_length, $excerpt_more );
  }

  // If shortcode filter is enabled lets process them
  if ( $allow_shortcodes ) {
    $excerpt = do_shortcode( $excerpt );
  }

  // Remove all shortcode Content before removing HTML
  if ( $remove_shortcodes ) {
    $excerpt = preg_replace( '#\[.+\]#U', '', $excerpt );
  }

  // Remove "all" HTML based on what is allowed
  $excerpt = wp_kses( $excerpt, $allowed_html );

  /**
   * Filter the event excerpt used in various views.
   *
   * @param string  $excerpt
   * @param WP_Post $post
   */
  return apply_filters( 'tribe_events_get_the_excerpt', wpautop( $excerpt ), $post );
}

function custom_tribe_events_has_meta($data) {

  if (is_array($data) && (count($data) == 1) && isset($data['address']) ) {
    $address =  trim($data['address']);
    if (empty($address)) {
      return FALSE;
    }
  }

  return !empty($data);
}

function custom_tribe_events_intro_text() {
  $currently_displaying = 'list';
  if ( ( ! empty( $_GET['tribe_event_display'] ) && 'past' === $_GET['tribe_event_display'] )
    || ( ! empty( $_POST['tribe_event_display'] ) && 'past' === $_POST['tribe_event_display'] ) ) {
    $currently_displaying = 'past';
  }
  
  if ($currently_displaying == 'past') {
    $href = "?tribe_event_display=list";
    return "Visit the <a href='$href' >Events index page</a>";
  } else {
    $href = "?tribe_event_display=past";
    return "Looking for older events? Visit the <a href='$href' >Events Archive</a>";
  }

  return NULL;
}