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
			<?php elseif(is_search()): ?>
				<div class="header-wrapper">
					<h1 <?php custom_generate_style_for_header($blog_index_id) ?> ><?php print sprintf(
							__( 'Search Results for &#8220;%s&#8221;' ),
							get_search_query()
						); ?></h1>
				</div>
			<?php else: ?>
				<div class="header-wrapper">
					<h1 <?php custom_generate_style_for_header($blog_index_id) ?> ><?php print get_the_title($blog_index_id); ?></h1>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php if ( custom_photo_credit_for_header( $blog_index_id ) != '' ) { ?>
	<div class="container">
		<div class="sixteen columns">
			<div class="wpb_row vc_row-fluid standard-section section section-no-parallax stretch" style="margin-bottom: 20px">
				<div class="col span_12 color-dark left">
					<div class="vc_col-sm-12 wpb_column column_container col no-padding color-dark">
						<div class="wpb_wrapper">
							<div class="wpb_text_column wpb_content_element ">
								<div class="wpb_wrapper">
									<h6 style="text-align: right;">
										CREDIT: <?php echo custom_photo_credit_for_header( $blog_index_id ) ?></h6>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php } ?>