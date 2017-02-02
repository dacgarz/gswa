<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>

    <?php if ( has_post_thumbnail() ) { ?>
        <div class="entry-image">
            <a href="<?php echo custom_get_link_post_type(get_the_ID()); ?>" title="<?php printf( esc_attr__('Link to %s', 'minti'), the_title_attribute('echo=0') ); ?>" target="_blank">
                <?php the_post_thumbnail(BLOG_IMAGES); ?>
            </a>
        </div>
    <?php } ?>
    <div class="entry-wrap">

        <div class="entry-title">
            <h2>
                <a href="<?php echo custom_get_link_post_type(get_the_ID()); ?>" title="<?php printf( esc_attr__('Link to %s', 'minti'), the_title_attribute('echo=0') ); ?>" target="_blank">
                    <?php the_title(); ?>
                </a>
            </h2>
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
