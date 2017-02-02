<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
	
	<?php if (get_post_meta( get_the_ID(), 'minti_blog-audio', true ) != '') {  ?>
    <div class="entry-audio">
       <?php echo wp_kses(get_post_meta( get_the_ID(), 'minti_blog-audio', true ), minti_expand_allowed_tags()); ?>
    </div>
    <?php } ?>

    <div class="entry-wrap">

        <div class="entry-title">
        	<h2><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'minti'), the_title_attribute('echo=0') ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
        </div>

    </div>
  <?php
  $category_id = NULL;
  if (!empty($GLOBALS['current_post_category'])) {
    $category_id = $GLOBALS['current_post_category'];
    unset($GLOBALS['current_post_category']);
  }
  ?>
  <?php if(!empty($category_id)): ?>
    <div class="category-footer">
      <a class="button" href="?swp_category_limiter=<?php print $category_id; ?>"><?php print category_description($category_id); ?></a>
    </div>
  <?php endif; ?>
</article><!-- #post -->
