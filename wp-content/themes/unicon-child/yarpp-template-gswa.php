<?php 
/*
YARPP Template: GSWA
Author: mitcho (Michael Yoshitaka Erlewine)
Description: A simple example YARPP template.
*/
?>
<?php if (have_posts()):?>
	<h3>RELATED POSTS</h3>
	<div class="row-wrapper">
		<?php while (have_posts()) : the_post(); ?>
			<div class="post-wrapper">
				<?php get_template_part( 'framework/inc/post-format/gswa/entry', get_post_format() ); ?>
			</div>
		<?php endwhile; ?>
	</div>
<?php endif; ?>
