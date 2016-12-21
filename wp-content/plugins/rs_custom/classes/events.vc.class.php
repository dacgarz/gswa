<?php


class homepageEventsWidget {
  private $plate_engine = false;
  private $template_prefix = 'events.vc.';

  function __construct($plate_engine) {
    $this->plate_engine = $plate_engine;

    add_action( 'init', array( $this, 'integrateWithVC' ) );
    add_shortcode( 'gswa_events_row', array( $this, 'renderMyShortCode' ) );
  }

  public function integrateWithVC() {
    if ( ! defined( 'WPB_VC_VERSION' ) ) {
      add_action('admin_notices', array( $this, 'showVcVersionNotice' ));
      return;
    }

    vc_map( array(
      "name" => __("Events Row", 'vc_extend'),
      "description" => '',
      "base" => "gswa_events_row",
      "class" => "",
      "controls" => "full",
//      "icon" => plugins_url('assets/asterisk_yellow.png', __FILE__), // or css class name which you can reffer in your css file later. Example: "vc_extend_my_class"
      "category" => __('GSWA', 'js_composer'),
      "params" => array(
        array(
          "type" => "textfield",
          "holder" => "div",
          "class" => "",
          "heading" => "Block Title",
          "param_name" => "block_title",
          "value" => 'Upcoming Events',
          "description" => ""
        ),
        array(
          'admin_label' => false,
          'type' => 'attach_image',
          'heading' => 'Image',
          'param_name' => '4th_item_image',
          'value' => '',
          'description' => __( 'Select image from media library.', 'js_composer' ),
          "group" => "4th Item"
        ),
        array(
          'admin_label' => false,
          "type" => "textfield",
          "holder" => "div",
          "class" => "",
          "heading" => "Title",
          "param_name" => "4th_item_title",
          "value" => 'All Upcoming Events',
          "description" => "",
          "group" => "4th Item"
        ),
        array(
          'admin_label' => false,
          "type" => "textarea_html",
          "holder" => "div",
          "class" => "",
          "heading" => "Content",
          "param_name" => "4th_item_content",
          "value" => "<p>I am test text block. Click edit button to change this text.</p>",
          "description" => "",
          "group" => "4th Item"
        ),
        array(
          'type' => 'vc_link',
          'heading' => 'URL (Link)',
          'param_name' => '4th_item_url',
          'description' => '',
          'group' => '4th Item'
        ),
      )
    ) );
  }

  public function renderMyShortCode( $atts, $content = null ) {
    $data = $this->process_atts($atts);
    return $this->plate_engine->render($this->template_prefix . 'block', $data);
  }

  private function process_atts($atts = array()) {
    $data = array(
      'block_title' => (empty($atts['block_title']) ? 'Upcoming Events' : $atts['block_title']),
      'items' => $this->get_upcoming_events(),
    );

    $link_data = vc_build_link( $atts['4th_item_url'] );
    $data['items'][] = array(
      'image' => $atts['4th_item_image'],
      'title' => (empty($atts['4th_item_title']) ? 'All Upcoming Events' : $atts['4th_item_title']),
      'desc' => $atts['4th_item_content'],
      'link_attrs' =>  $this->generate_link_attrs($link_data),
      'link_title' => $link_data['title']
    );

    return $data;
  }


  private function get_upcoming_events() {
    $events = tribe_get_events(
      apply_filters(
        'tribe_events_list_widget_query_args', array(
          'eventDisplay'   => 'list',
          'posts_per_page' => 3,
          'tribe_render_context' => 'widget',
        )
      )
    );
    $events_data = array();
    if (!empty($events)) {
      foreach ($events as $event) {
        $events_data[] = array(
          'image' => get_post_thumbnail_id( $event->ID ),
          'title' => $event->post_title,
          'desc' => wp_trim_words($event->post_content, 30),
          'link_attrs' => $this->generate_link_attrs(array('url' => get_permalink($event->ID))),
          'link_title' => 'Read More'
        );
      }
    }

    $total = count($events_data);
    if ($total < 3) {
      $additional_events = $this->get_additional_events(3 - $total);
      if (!empty($additional_events)) {
        foreach ($additional_events as $ad_event) {
          $events_data[] = $ad_event;
        }
      }
    }

    return $events_data;
  }

  private function generate_link_attrs($link_data) {
    $additional_attributes = array();
    $href = (empty($link_data['url']) ? '/events' : $link_data['url']);
    $additional_attributes[] = "href='$href'";

    if (!empty($link_data['target'])) {
      $target = $link_data['target'];
      $additional_attributes[] = "target='$target'";
    }
    if (!empty($link_data['rel'])) {
      $target = $link_data['rel'];
      $additional_attributes[] = "rel='$target'";
    }

    return  implode(' ', $additional_attributes);
  }

  public function showVcVersionNotice() {
    $plugin_data = get_plugin_data(__FILE__);
    print $this->plate_engine->render($this->template_prefix . 'notice', array(
      'plugin_data' => $plugin_data,
    ));
  }

  private function get_additional_events($limit) {
    $events = array();

    $args = array(
      'post_status' => 'publish',
      'posts_per_page' => $limit,
      'type' => 'tribe_events',
      'tax_query' => array(
        array(
          'taxonomy' => 'tribe_events_cat',
          'field'    => 'slug',
          'terms'    => array('stock_events'),
          'operator' => 'IN'
        )
      )
    );
    $posts = new WP_Query($args);
    if ($posts->have_posts()) {
      while ($posts->have_posts()) {
        $posts->the_post();
        $id = get_the_ID();
        $post = get_post($id);
        $events[] = array(
          'id' => $id,
          'image' => get_post_thumbnail_id($id),
          'title' => $post->post_title,
          'desc' => wp_trim_words($post->post_content, 30),
          'link_attrs' => $this->generate_link_attrs(array( 'url' => get_permalink($id))),
          'link_title' => 'Read More'
        );
      }
    };
    return $events;
  }

}