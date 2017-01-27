<?php
//ID OF BLOG INDEX = 3208
$blog_index_id = 3208;
?>
<div id="fullimagecenter" class="titlebar" style="background-image: url( <?php $images = rwmb_meta( 'minti_headerimage', 'type=image_advanced&size=standard', $blog_index_id ); foreach ( $images as $image ) { echo esc_url($image['url']); } ?> );">
  <div class="container">
	  <div class="sixteen columns">
			<?php if (is_archive()): ?>
				<div class="header-wrapper">
					<h1 <?php custom_generate_style_for_header($blog_index_id) ?> ><?php print the_archive_title(); ?></h1>
				</div>
			<?php else: ?>
				<div class="header-wrapper">
					<h1 <?php custom_generate_style_for_header($blog_index_id) ?> ><?php print get_the_title($blog_index_id); ?></h1>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>