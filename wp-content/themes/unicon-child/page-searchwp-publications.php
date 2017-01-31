<?php

/* Template Name: SearchWP Publications Search Results */

global $post;

// retrieve our search query if applicable
$query = isset($_REQUEST['swpquery']) ? sanitize_text_field($_REQUEST['swpquery']) : '';
$category_filter = isset($_REQUEST['swp_category_limiter']) ? intval(sanitize_text_field($_REQUEST['swp_category_limiter'])) : NULL;
$swppg = isset($_REQUEST['swppg']) ? absint($_REQUEST['swppg']) : 1;

$is_default_search = empty($query);

if (class_exists('SWP_Query')) {
  $engine = 'publications';
  $search_args = array(
    'posts_per_page' => 12,
    's' => $query,
    'engine' => $engine,
    'page' => $swppg
  );
  $search_args['tax_query'] = array(
    array(
      'taxonomy' => 'post_type',
      'field'    => 'term_id',
      'terms'    => custom_publications_index_content(TRUE),
      'operator' => 'IN',
    )
  );
  if (!empty($category_filter)) {
    if (isset($search_args['tax_query'][0]['operator'])) {
      unset($search_args['tax_query'][0]['operator']);
    }
    $search_args['tax_query'][0]['terms'] = $category_filter;
  }

  $pagination = array(
    'prev_text' => '<i class="fa fa-chevron-left"></i>',
    'next_text' => '<i class="fa fa-chevron-right"></i>',
    'type' => 'list',
    'current' => $swppg,
    'format' => '?swppg=%#%',
  );

  if ($is_default_search) {
    if (isset($search_args['page'])) unset($search_args['page']);
    $search_args['paged'] =$swppg;
    $swp_query = new WP_Query($search_args);
  } else {
    $swp_query = new SWP_Query(
    // see all args at https://searchwp.com/docs/swp_query/
      $search_args
    );
  }
  $pagination['total'] = $swp_query->max_num_pages;
  $pagination = paginate_links($pagination);
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
          // see http://codex.wordpress.org/Function_Reference/wp_dropdown_categories
          $swp_cat_dropdown_args = array(
            'show_option_all' => __('All Publications'),
            'name' => 'swp_category_limiter',
            'taxonomy' => 'post_type',
            'class' => 'category-dropdown'
          );
          if (!empty($category_filter)) {
            $swp_cat_dropdown_args['selected'] = $category_filter;
          }
          wp_dropdown_categories($swp_cat_dropdown_args);
          ?>
        </div>
        <div class="element-wrapper search-text-wrapper">
          <label for="swpquery">Topic</label>
          <input type="text" class="search-field" placeholder="Search …" value="<?php if (!empty($query)) print $query; ?>" name="swpquery" id="swpquery" title="Search for:">
        </div>
        <div class="element-wrapper search-button-wrapper">
          <input type="submit" value="Search"/>
        </div>
      </form>
    </div>
    <!-- end search form -->
    <?php if (/*(!empty($query)) || */(!empty($category_filter))): ?>
      <div class="current-filters">
        <?php if (!empty($category_filter)): ?>
          <span>
            <?php print get_cat_name($category_filter); ?>
          </span>
        <?php endif; ?>
        <?php /* if ((!empty($query)) && (!empty($category_filter))): ?>
          +
        <?php endif; ?>
        <?php if (!empty($query)): ?>
          <span>
            &#8220;<?php print $query; ?>&#8221;
          </span>
        <?php endif; */ ?>
      </div>
    <?php endif; ?>
    <div class="publications-content">
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
            <div class="pagination clearfix" role="navigation" id="pagination">
              <?php echo wp_kses_post($pagination); ?>
            </div>
          <?php }
        } elseif (empty($query) && empty($category_filter)) {
          get_template_part('framework/inc/publications/index');
        } else {
          ?><p class="no-results">No results found.</p><?php
        } ?>
      </div>
      <!-- ----------------------------------------------------------------------------------------------------------- -->
    </div>
  </div>
<?php get_footer();