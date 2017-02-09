<?php

$event_id = get_queried_object_id();
if ((!empty($event_id)) && (get_post_type($event_id) == 'tribe_events')) {
		$title = get_the_title($event_id);
} else {
	$title = Tribe__Settings_Manager::get_option('index_settings_title');
}

if ((!empty($_GET['tribe_event_display'])) && ($_GET['tribe_event_display'] == 'past')) {
	$title .= " Archive";
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

<?php if ( ! empty( custom_photo_credit_for_header( $event_id, TRUE ) ) ) { ?>
	<div class="container">
		<div class="sixteen columns">
			<div class="wpb_row vc_row-fluid standard-section section section-no-parallax stretch" style="margin-bottom: 20px">
				<div class="col span_12 color-dark left">
					<div class="vc_col-sm-12 wpb_column column_container col no-padding color-dark">
						<div class="wpb_wrapper">
							<div class="wpb_text_column wpb_content_element ">
								<div class="wpb_wrapper">
									<h6 style="text-align: right;">
										CREDIT: <?php echo custom_photo_credit_for_header( $event_id, TRUE ) ?></h6>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php } ?>