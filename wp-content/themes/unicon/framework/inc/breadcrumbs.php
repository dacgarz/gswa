<?php
/*-----------------------------------------------------------------------------------*/
/*  Breadcrumbs Function
    Source: http://dimox.net/wordpress-breadcrumbs-without-a-plugin/ */
/*-----------------------------------------------------------------------------------*/

function minti_breadcrumbs() {

  global $minti_data;

  $showOnHome = 1; // 1 - show breadcrumbs on the homepage, 0 - don't show
  $delimiter = '/'; // delimiter between crumbs
  $home = get_bloginfo('name'); // text for the 'Home' link
  $blog = $minti_data['text_titlebar_blog'];
  $shop = $minti_data['text_titlebar_woocommerce'];
  $forums = $minti_data['text_titlebar_forums'];
  $showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
  $before = '<span class="current">'; // tag before the current crumb
  $after = '</span>'; // tag after the current crumb
 
  global $post;
  $homeLink = home_url();

  global $woocommerce;
  if($woocommerce) {
    $shopLink = get_permalink( woocommerce_get_page_id( 'shop' ) );
  }
  
  $forumLink = get_post_type_archive_link('forum');
 
  if (is_home() || is_front_page()) {
 
    if ($showOnHome == 1) echo '<div id="crumbs"><a href="' . esc_url($homeLink) . '">' . esc_html($home) . '</a> ' . esc_html($delimiter) . ' ' . esc_html($blog) . '</a></div>';
 
  } else {
 
    echo '<div id="crumbs"><a href="' . esc_url($homeLink) . '">' . esc_html($home) . '</a> ' . esc_html($delimiter) . ' ';
 
    if ( is_category() ) {
      $thisCat = get_category(get_query_var('cat'), false);
      if ($thisCat->parent != 0) echo get_category_parents($thisCat->parent, TRUE, ' ' . esc_html($delimiter) . ' ') . '';
      echo $before . __('Category', 'minti') . ': ' . esc_html(single_cat_title('', false)) . '' . $after;
 
    } elseif ( is_search() ) {
      echo $before . __('Search', 'minti') . $after;
 
    } elseif ( is_day() ) {
      echo '<a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . esc_html(get_the_time('Y')) . '</a> ' . esc_html($delimiter) . ' ';
      echo '<a href="' . esc_url(get_month_link(get_the_time('Y'),get_the_time('m'))) . '">' . esc_html(get_the_time('F')) . '</a> ' . esc_html($delimiter) . ' ';
      echo $before . get_the_time('d') . $after;
 
    } elseif ( is_month() ) {
      echo '<a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . esc_html(get_the_time('Y')) . '</a> ' . esc_html($delimiter) . ' ';
      echo $before . esc_html(get_the_time('F')) . $after;
 
    } elseif ( is_year() ) {
      echo $before . esc_html(get_the_time('Y')) . $after;
      
    } elseif( class_exists('Woocommerce') && is_shop() ) {
      echo $before . '<a href="' . esc_url($shopLink) . '">' . esc_html($shop) . '</a>' . $after;

    } elseif( class_exists('Woocommerce') && is_product() ) {
      echo '<a href="' . esc_url($shopLink) . '">' . esc_html($shop) . '</a> ' . esc_html($delimiter) . ' ' . $before . esc_html(get_the_title()) . $after;

    } elseif( class_exists('bbPress') && is_bbpress() ) {
      echo '<a href="' . esc_url($forumLink) . '">' . esc_html($forums) . '</a> ' . esc_html($delimiter) . ' ' . $before . esc_html(get_the_title()) . $after . '</a>';

    } elseif ( is_single() && !is_attachment() ) {
      if ( get_post_type() != 'post' ) {
        $post_type = get_post_type_object(get_post_type());
        $slug = $post_type->rewrite;
        if ($showCurrent == 1) echo ' ' . $before . esc_html(get_the_title()) . $after;
      } else {
        $cat = get_the_category(); $cat = $cat[0];
        $cats = get_category_parents($cat, TRUE, ' ' . esc_html($delimiter) . ' ');
        if ($showCurrent == 0) $cats = preg_replace("#^(.+)\s$delimiter\s$#", "$1", $cats);
        echo $cats; // No need to escape here
        if ($showCurrent == 1) echo $before . esc_html(get_the_title()) . $after;
      }
 
    } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
      $post_type = get_post_type_object(get_post_type());
      echo $before . esc_html($post_type->labels->singular_name) . $after;
 
    } elseif ( is_attachment() ) {
      if ($showCurrent == 1) echo $before . esc_html(get_the_title()) . $after;
 
    } elseif ( is_page() && !$post->post_parent ) {
      if ($showCurrent == 1) echo $before . esc_html(get_the_title()) . $after;
 
    } elseif ( is_page() && $post->post_parent ) {
      $parent_id  = $post->post_parent;
      $breadcrumbs = array();
      while ($parent_id) {
        $page = get_page($parent_id);
        $breadcrumbs[] = '<a href="' . esc_url(get_permalink($page->ID)) . '">' . esc_html(get_the_title($page->ID)) . '</a>';
        $parent_id  = $page->post_parent;
      }
      $breadcrumbs = array_reverse($breadcrumbs);
      for ($i = 0; $i < count($breadcrumbs); $i++) {
        echo $breadcrumbs[$i]; // No need to escape here
        if ($i != count($breadcrumbs)-1) echo ' ' . esc_html($delimiter) . ' ';
      }
      if ($showCurrent == 1) echo ' ' . esc_html($delimiter) . ' ' . $before . esc_html(get_the_title()) . $after;
 
    } elseif ( is_tag() ) {
      echo $before . __('Tag', 'minti') . ': ' . esc_html(single_tag_title('', false)) . $after;
 
    } elseif ( is_author() ) {
      global $author;
      $userdata = get_userdata($author);
      echo $before . __('Articles by', 'minti') . ' ' . esc_html($userdata->display_name) . $after;
 
    } elseif ( is_404() ) {
      echo $before . __('Error 404', 'minti') . $after;
    }
 
    if ( get_query_var('paged') ) {
      echo ' (' . __('Page', 'minti') . ' ' . esc_html(get_query_var('paged')) . ')';
    }
 
    echo '</div>';
 
  }

}

?>
