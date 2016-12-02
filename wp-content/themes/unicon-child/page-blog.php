<?php
/* Template Name: Blog */

get_header(); ?>

<div id="page-wrap" class="blog-page blog-gswa container">

	<div id="content" class="blog-wrap sixteen columns">
	
		<?php
		global $wp_query;
		if ( get_query_var('paged') ) { $paged = get_query_var('paged'); } elseif ( get_query_var('page') ) { $paged = get_query_var('page'); } else { $paged = 1; }

		$categories = rwmb_meta( 'minti_blogcategories', 'type=checkbox_list' );
		$categories = implode( ', ', $categories );	

		$args = array(
			'post_status' => 'publish',
			'orderby' => 'date',
			'order' => 'DESC',
			'category_name'	=> $categories,
			'paged' => $paged,
			'posts_per_page' => 12
		);
		$wp_query = new WP_Query($args);
		$is_open_tag = FALSE;
		
		while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
			<?php if ($wp_query->current_post % 4 === 0):?>
				<?php if($wp_query->current_post !== 0) {print '</div>'; $is_open_tag = FALSE;} ?>
				<?php $is_open_tag = TRUE; ?>
				<div class="row-wrapper">
			<?php endif; ?>
					
			<div class="post-wrapper">
				<?php get_template_part( 'framework/inc/post-format/gswa/entry', get_post_format() ); ?>
			</div>

		<?php endwhile; ?>
		<?php if ($is_open_tag == TRUE) :?>
			</div>
		<?php endif; ?>
	</div>

	<?php get_template_part( 'framework/inc/nav' ); wp_reset_postdata(); ?>

</div>

<?php get_footer(); ?>
