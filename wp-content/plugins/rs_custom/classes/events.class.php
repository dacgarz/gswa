<?php

class  rsEvents {
  public static $category_filter_name = 'tribe-bar-search-category';
  public static $access_to_private_events = FALSE;

  function __construct() {
//    self::$access_to_private_events = $this->access_to_private();
    add_filter( 'tribe-events-bar-filters', array( $this, 'setup_category_search_in_bar' ) , 1, 1 );
    add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ), 51 );
    add_action( 'init', array( $this, 'access_to_private' ));
  }


  public function setup_category_search_in_bar($filters) {
    $value = '';
    if ( ! empty( $_REQUEST[self::$category_filter_name] ) ) {
      $value = esc_attr( $_REQUEST[self::$category_filter_name] );
    }
    $options = array();
    $categories = get_categories(array(
      'taxonomy' => 'tribe_events_cat'
    ));
    foreach ($categories as $cat) {
      $options[$cat->term_id] = array(
        'name' => $cat->name,
        'slug' => $cat->slug
      );
    }

    if ( tribe_get_option( 'tribeDisableTribeBar', false ) == false ) {
      $filters[self::$category_filter_name] = array(
        'name'    => self::$category_filter_name,
        'caption' => esc_html__( 'Category', 'the-events-calendar' ),
        'html'    => $this->get_category_html(self::$category_filter_name, $options, $value),
      );
    }

    return $filters;
  }

  private function get_category_html($name, $items, $default_value) {
    $html = "<select name='$name' id='$name' >";
    $html .= "<option value='' >All Categories</option>";
    foreach ($items as $id => $values) {
      if ( (!self::$access_to_private_events) && ($values['slug'] == 'private') ) continue;
      $att = '';
      if ($default_value == $id) {
        $att = " selected='selected' ";
      }
      $html .= "<option value='$id' $att >" . $values['name'] . "</option>";
    }
    $html .= '</select>';
    return $html;
  }


  public function  pre_get_posts($query) {
    if ( $query->tribe_is_event || $query->tribe_is_event_category ) {
      $tax_query = array(
        'relation' => 'AND',
      );

      if (!self::$access_to_private_events) {
        $tax_query[] = array(
          'taxonomy' => 'tribe_events_cat',
          'field'    => 'slug',
          'terms'    => array('private'),
          'operator' => 'NOT IN'
        );
      }

      if ( ! empty( $_REQUEST[self::$category_filter_name] ) ) {
        $cat = $_REQUEST[self::$category_filter_name];
        $tax_query[] = array(
          'taxonomy' => 'tribe_events_cat',
          'field'    => 'term_id',
          'terms'    => array( $cat ),
        );
      }
      $query->set( 'tax_query', $tax_query);
    }
    return $query;
  }

  public function access_to_private() {
    $file_name = basename($_SERVER['SCRIPT_NAME']);
    self::$access_to_private_events = FALSE;
    $user = wp_get_current_user();
    if ((is_admin() && ($file_name != 'admin-ajax.php') ) && in_array('administrator', $user->roles)) {
      self::$access_to_private_events = TRUE;
    }
    return self::$access_to_private_events;
  }

}