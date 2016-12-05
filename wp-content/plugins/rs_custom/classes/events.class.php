<?php

class  rsEvents {
  public static $category_filter_name = 'tribe-bar-search-category';

  function __construct() {
    add_filter( 'tribe-events-bar-filters', array($this, 'setup_category_search_in_bar') , 1, 1 );
    add_action( 'pre_get_posts', array( __CLASS__, 'pre_get_posts' ), 51 );
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
      $options[$cat->term_id] = $cat->name;
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
    $html .= "<option value='' >Select Category</option>";
    foreach ($items as $id => $title) {
      $att = '';
      if ($default_value == $id) {
        $att = " selected='selected' ";
      }
      $html .= "<option value='$id' $att >$title</option>";
    }
    $html .= '</select>';
    return $html;
  }


  public function  pre_get_posts($query) {
    if ( $query->tribe_is_event || $query->tribe_is_event_category ) {
      $tax_query = array(
        //@TODO hide private
        //'operator' => 'NOT IN',
      );
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
  //@TODO hide private
  //@TODO css
  
}