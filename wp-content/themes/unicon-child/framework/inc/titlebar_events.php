<?php
//ID OF BLOG INDEX = 3208
$blog_index_id = 3208;

$title = 'Events';
$event_id = get_queried_object_id();
if (!empty($event_id)) {
	if (get_post_type($event_id) == 'tribe_events') {
		$title = get_the_title($event_id);
	}
}

$bg_image = null;
$images = rwmb_meta( 'minti_headerimage', 'type=image_advanced&size=standard', $blog_index_id );
foreach ( $images as $image ) {
	$bg_image = esc_url($image['url']);
}

?>
<div id="fullimagecenter" class="titlebar" style="background-image: url( <?php print $bg_image; ?> );">
  <div class="container">
	  <div class="sixteen columns">
		  <h1><?php print $title; ?></h1>
		</div>
	</div>
</div>