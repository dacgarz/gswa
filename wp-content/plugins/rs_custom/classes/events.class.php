<?php

class  rsEvents {
  public static $category_filter_name = 'tribe-bar-search-category';
  public static $access_to_private_events = FALSE;
  private $plate_engine = false;

  function __construct($plate_engine) {
    $this->plate_engine = $plate_engine;
//    self::$access_to_private_events = $this->access_to_private();
    add_filter( 'tribe-events-bar-filters', array( $this, 'setup_category_search_in_bar' ) , 1, 1 );
    add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ), 51 );
    add_action( 'init', array( $this, 'access_to_private' ));

    add_action( 'tribe_settings_after_tabs', array($this, 'tribe_settings_after_tabs'));
    add_action( 'tribe_settings_content_tab_index_settings', array($this, 'tribe_settings_content_tab_index_settings'));

    add_action( 'tribe_settings_validate_tab_index_settings', array($this, 'tribe_settings_validate_tab_index_settings'));
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

  public function tribe_settings_after_tabs() {
    print "<a id='index-settings' href='?page=tribe-common&tab=index_settings&post_type=tribe_events' class='nav-tab' >Index Page Settings</a>";
  }

  public function tribe_settings_content_tab_index_settings() {
    wp_enqueue_media();
    print $this->plate_engine->render('events_settings_tab', array(
        'default_value' => array(
          'title' => Tribe__Settings_Manager::get_option('index_settings_title'),
          'header_image' => Tribe__Settings_Manager::get_option('index_settings_image'),
          'title_style' => Tribe__Settings_Manager::get_option('index_settings_title_style')
        )
      ));
  }

  public function tribe_settings_validate_tab_index_settings() {
    $settings_keys = array('index_settings_title', 'index_settings_image', 'index_settings_title_style');
    foreach ($settings_keys as $setting) {
      if (isset($_POST[$setting])) {
        Tribe__Settings_Manager::set_option($setting, $_POST[$setting]);
      }
    }
  }
}