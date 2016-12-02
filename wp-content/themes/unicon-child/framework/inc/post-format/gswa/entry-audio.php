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

        <div class="entry-content">
        	<?php echo wp_kses_post(minti_custom_excerpt_child(20, FALSE)); ?>
        </div>

        <?php get_template_part( 'framework/inc/additional/post-index-read-more' ); ?>

    </div>

</article><!-- #post -->
