<?php

/* Template Name: SearchWP Publications Search Results */

global $post;

// retrieve our search query if applicable
$query = isset($_REQUEST['swpquery']) ? sanitize_text_field($_REQUEST['swpquery']) : '';
$category_filter = isset($_REQUEST['swp_category_limiter']) ? intval(sanitize_text_field($_REQUEST['swp_category_limiter'])) : NULL;

// retrieve our pagination if applicable
$swppg = isset($_REQUEST['swppg']) ? absint($_REQUEST['swppg']) : 1;

if (class_exists('SWP_Query')) {

  $engine = 'publications'; // taken from the SearchWP settings screen

  $search_args = array(
    'posts_per_page' => 12,
    's' => $query,
    'engine' => $engine,
    'page' => $swppg
  );
  if (!empty($category_filter)) {
    $search_args['tax_query'] = array(
      array(
        'taxonomy' => 'category',
        'field'    => 'term_id',
        'terms'    => $category_filter//,
      )
    );
  } else {
    $search_args['tax_query'] = array(
      array(
        'taxonomy' => 'category',
        'field'    => 'term_id',
        'terms'    => custom_publications_index_content(TRUE),
        'operator' => 'IN',
      )
    );
  }

  if (empty($query)) {

    $swp_query = new WP_Query($search_args);
    $pagination = paginate_links(array(
//      'format' => '?swppg=%#%',
//      'current' => $swppg,
//      'total' => $swp_query->max_num_pages,
    ));

  } else {
    $swp_query = new SWP_Query(
    // see all args at https://searchwp.com/docs/swp_query/
      $search_args
    );

    // set up pagination
    $pagination = paginate_links(array(
      'format' => '?swppg=%#%',
      'current' => $swppg,
      'total' => $swp_query->max_num_pages,
    ));
  }

}

get_header(); ?>

  <div id="page-wrap" class="publications-page publications-gswa container">

  <div id="content" class="blog-wrap sixteen columns">
    <!-- ----------------------------------------------------------------------------------------------------------- -->
    <!-- begin search form -->
    <div class="search-box">
      <form role="search" method="get" class="search-form clearfix" action="<?php echo esc_html(get_permalink()); ?>">
        <div class="element-wrapper">
          <label for="swp_category_limiter">Category</label>
          <?php
          // output all of our Categories
          // for more information see http://codex.wordpress.org/Function_Reference/wp_dropdown_categories
          $swp_cat_dropdown_args = array(
            'show_option_all' => __('Select Category'),
            'name' => 'swp_category_limiter',
            'child_of' => 44,
            'class' => 'category-dropdown'
          );
          if (!empty($category_filter)) {
            $swp_cat_dropdown_args['selected'] = $category_filter;
          }
          wp_dropdown_categories($swp_cat_dropdown_args);
          ?>
        </div>
        <div class="element-wrapper">
          <label for="swpquery">Topic</label>
          <input type="text" class="search-field" placeholder="Search …" value="<?php if (!empty($query)) print $query; ?>" name="swpquery" id="swpquery" title="Search for:">
        </div>
        <div class="element-wrapper">
          <input type="submit" value="Search"/>
        </div>
      </form>
    </div>
    <!-- end search form -->

    <?php
    $is_open_tag = FALSE;
    if (((!empty($query)) || (!empty($category_filter))) && isset($swp_query) && !empty($swp_query->posts)) {
    foreach ($swp_query->posts as $index => $post) {
    setup_postdata($post);
    ?>
    <?php if ($index % 4 === 0): ?>
    <?php if ($index !== 0) {
      print '</div>';
      $is_open_tag = FALSE;
    } ?>
    <?php $is_open_tag = TRUE; ?>
    <div class="row-wrapper">
      <?php endif; ?>

      <div class="post-wrapper">
        <?php get_template_part('framework/inc/post-format/gswa/entry', get_post_format()); ?>
      </div>
      <?php
      }
      if ($is_open_tag == TRUE) {
        print '</div>';
      }
      wp_reset_postdata();

      // pagination
      if ($swp_query->max_num_pages > 1) { ?>
        <div class="navigation pagination" role="navigation">
          <h2 class="screen-reader-text">Posts navigation</h2>
          <div class="nav-links">
            <?php echo wp_kses_post($pagination); ?>
          </div>
        </div>
      <?php }
      } elseif (empty($query) && empty($category_filter)) {
        get_template_part('framework/inc/publications/index');
      } else {
        ?><p>No results found.</p><?php
      } ?>

      <!-- ----------------------------------------------------------------------------------------------------------- -->
    </div>
  </div>
<?php get_footer();