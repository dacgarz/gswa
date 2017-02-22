<?php
$data = custom_publications_index_content();
$category_labels = custom_publications_index_get_labels();
?>
<div class="publications-index">
  <div class="row-wrapper">
    <?php foreach ($data['posts'] as $category_id => $post): ?>
      <?php setup_postdata($post); ?>
      <div class="post-wrapper">
        <div class="post-inner">
          <div class="post-type-label"><?php print isset($category_labels[$category_id]) ? $category_labels[$category_id] : get_cat_name($category_id); ?></div>
          <?php $GLOBALS['current_post_category'] = $category_id; ?>
          <?php get_template_part('framework/inc/post-format/publications_index/entry', get_post_format()); ?>
          <?php /*<div class="category-footer">
            <a href="?swp_category_limiter=<?php print $category_id; ?>"><?php print category_description($category_id); ?></a>
          </div> */ ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>