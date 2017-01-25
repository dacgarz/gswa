<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>

    <?php if ( has_post_thumbnail() ) { ?>
        <div class="entry-image">
            <a href="<?php echo esc_url(get_post_meta( get_the_ID(), 'minti_blog-link', true )) ?>" title="<?php printf( esc_attr__('Link to %s', 'minti'), the_title_attribute('echo=0') ); ?>" target="_blank">
                <?php the_post_thumbnail('post-thumbnail-link'); ?>
            </a>
        </div>
    <?php } ?>
    <div class="entry-wrap">

        <div class="entry-title">
            <h2>
                <a href="<?php echo esc_url(get_post_meta( get_the_ID(), 'minti_blog-link', true )) ?>" title="<?php printf( esc_attr__('Link to %s', 'minti'), the_title_attribute('echo=0') ); ?>" target="_blank">
                    <?php the_title(); ?>
                </a>
            </h2>
        </div>

    </div>

</article><!-- #post -->
