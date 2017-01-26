<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>

    <div class="entry-gallery">

        <div class="flexslider">
            <ul class="slides">
                <?php $images = rwmb_meta( 'minti_blog-gallery', 'type=image_advanced&size=standard' );
                    foreach ( $images as $image ) {
                        echo "<li><img src='".esc_url($image['url'])."' width='".esc_attr($image['width'])."' height='".esc_attr($image['height'])."' alt='".esc_attr($image['alt'])."' /></li>";
                    } 
                ?>
            </ul>
        </div>

    </div>

    <div class="entry-wrap">

        <div class="entry-title">
            <h2><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'minti'), the_title_attribute('echo=0') ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
        </div>

    </div>

</article><!-- #post -->
