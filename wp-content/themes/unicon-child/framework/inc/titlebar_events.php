<?php

$event_id = get_queried_object_id();
if ((!empty($event_id)) && (get_post_type($event_id) == 'tribe_events')) {
		$title = get_the_title($event_id);
} else {
	$title = Tribe__Settings_Manager::get_option('index_settings_title');
}

$bg_image = Tribe__Settings_Manager::get_option('index_settings_image');

?>
<div id="fullimagecenter" class="titlebar" style="background-image: url( <?php print $bg_image; ?> );">
  <div class="container">
	  <div class="sixteen columns">
			<div class="header-wrapper">
		  	<h1 <?php custom_generate_style_for_header($event_id, TRUE) ?> ><?php print $title; ?></h1>
			</div>
		</div>
	</div>
</div>