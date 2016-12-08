<?php
//ID OF BLOG INDEX = 3208
$blog_index_id = 3208;
?>
<div id="fullimagecenter" class="titlebar" style="background-image: url( <?php $images = rwmb_meta( 'minti_headerimage', 'type=image_advanced&size=standard', $blog_index_id ); foreach ( $images as $image ) { echo esc_url($image['url']); } ?> );">
  <div class="container">
	  <div class="sixteen columns">
		  <h1><?php print get_the_title($blog_index_id); ?></h1>
		</div>
	</div>
</div>