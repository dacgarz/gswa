<?php global $minti_data; ?>

<?php if( is_object($post) && !is_archive() &&!is_search() && !is_404() && ! is_author() && !is_home()  ) { ?>

<?php if(rwmb_meta('minti_titlebar') != 'default' && rwmb_meta('minti_titlebar') != ''){ ?>

		<?php if (rwmb_meta('minti_titlebar') == 'title') { ?>

			<div id="fulltitle" class="titlebar">
				<div class="container">
					<div id="title" class="ten columns">
						<h1><?php the_title(); ?></h1>
					</div>
					<div id="breadcrumbs" class="six columns">
						<?php if($minti_data['titlebar_breadcrumbs'] == 1) { minti_breadcrumbs(); } ?>
					</div>
				</div>
			</div>

			<?php if ( custom_photo_credit_for_header( get_the_ID() ) != '' ) { ?>
				<div class="container">
					<div class="sixteen columns">
						<div class="wpb_row vc_row-fluid standard-section section section-no-parallax stretch" style="margin-bottom: 20px">
							<div class="col span_12 color-dark left">
								<div class="vc_col-sm-12 wpb_column column_container col no-padding color-dark">
									<div class="wpb_wrapper">
										<div class="wpb_text_column wpb_content_element ">
											<div class="wpb_wrapper">
												<h6 style="text-align: right;">
													CREDIT: <?php echo custom_photo_credit_for_header( get_the_ID() ) ?></h6>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>

		<?php /* ---------------------------------------------------------------------------------------*/ ?>

		<?php } elseif (rwmb_meta('minti_titlebar') == 'featuredimagecenter') { ?>

			<div id="fullimagecenter" class="titlebar" style="background-image: url( <?php $images = rwmb_meta( 'minti_headerimage', 'type=image_advanced&size=standard' ); foreach ( $images as $image ) { echo esc_url($image['url']); } ?> );">
				<div class="container">
					<div class="sixteen columns">
						<div class="header-wrapper">
							<h1 <?php custom_generate_style_for_header(get_the_ID()) ?> ><?php the_title(); ?></h1>
						</div>
					</div>
				</div>
			</div>
			
			<?php if ( custom_photo_credit_for_header( get_the_ID() ) != '' ) { ?>
				<div class="container">
					<div class="sixteen columns">
						<div class="wpb_row vc_row-fluid standard-section section section-no-parallax stretch" style="margin-bottom: 20px">
							<div class="col span_12 color-dark left">
								<div class="vc_col-sm-12 wpb_column column_container col no-padding color-dark">
									<div class="wpb_wrapper">
										<div class="wpb_text_column wpb_content_element ">
											<div class="wpb_wrapper">
												<h6 style="text-align: right;">
													CREDIT: <?php echo custom_photo_credit_for_header( get_the_ID() ) ?></h6>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>

		<?php /* ---------------------------------------------------------------------------------------*/ ?>

		<?php } elseif (rwmb_meta('minti_titlebar') == 'transparentimage') { ?>

			<div id="transparentimage" class="titlebar" style="background-image: url( <?php $images = rwmb_meta( 'minti_headerimage', 'type=image_advanced&size=standard' ); foreach ( $images as $image ) { echo esc_url($image['url']); } ?> );">
				<div class="container">
					<div class="sixteen columns">
						<div class="header-wrapper">
							<h1 <?php custom_generate_style_for_header(get_the_ID()) ?> ><?php the_title(); ?></h1>
						</div>
					</div>
				</div>
			</div>

			<?php if ( custom_photo_credit_for_header( get_the_ID() ) != '' ) { ?>
				<div class="container">
					<div class="sixteen columns">
						<div class="wpb_row vc_row-fluid standard-section section section-no-parallax stretch" style="margin-bottom: 20px">
							<div class="col span_12 color-dark left">
								<div class="vc_col-sm-12 wpb_column column_container col no-padding color-dark">
									<div class="wpb_wrapper">
										<div class="wpb_text_column wpb_content_element ">
											<div class="wpb_wrapper">
												<h6 style="text-align: right;">
													CREDIT: <?php echo custom_photo_credit_for_header( get_the_ID() ) ?></h6>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>

		<?php /* ---------------------------------------------------------------------------------------*/ ?>

		<?php } elseif (rwmb_meta('minti_titlebar') == 'notitle') { ?>

			<div id="notitlebar"></div>

		<?php } ?>

<?php } else { ?>

		<?php
			// Define the Title for different Pages
			if ( is_home() ) { $title = 'Blog'; }
			elseif( is_search() ) {
				$allsearch = new WP_Query("s=$s&showposts=-1");
				$count = $allsearch->post_count;
				wp_reset_postdata();
				$title = $count . ' ';
				$title .= __('Search results for:', 'minti');
				$title .= ' ' . get_search_query();
			}
			elseif( class_exists('Woocommerce') && is_woocommerce() ) { $title = $minti_data['text_titlebar_woocommerce']; }
			elseif( is_archive() ) {
				if (is_category()) { 	$title = single_cat_title('',false); }
				elseif( is_tag() ) { 	$title = __('Posts Tagged:', 'minti') . ' ' . single_tag_title('',false); }
				elseif (is_day()) { 	$title = __('Archive for', 'minti') . ' ' . get_the_time('F jS, Y'); }
				elseif (is_month()) { 	$title = __('Archive for', 'minti') . ' ' . get_the_time('F Y'); }
				elseif (is_year()) { 	$title = __('Archive for', 'minti') . ' ' . get_the_time('Y'); }
				elseif (is_author()) { 	$title = __('Author Archive for', 'minti') . ' ' . get_the_author(); }
				elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { $title = __('Blog Archives', 'minti'); }
				else{
					$title = single_term_title( "", false );
					if ( $title == '' ) { // Fix for templates that are archives
						$post_id = $post->ID;
						$title = get_the_title($post_id);
					}
				}
			}
			elseif( is_404() ) { $title = __('Oops, this Page could not be found.', 'minti'); }
			elseif( get_post_type() == 'post' ) { $title = $minti_data['text_titlebar_blog']; }
			else { $title = get_the_title(); }
		?>

		<?php if($minti_data['titlebar_layout'] == 'title') { ?>
			<div id="fulltitle" class="titlebar">
				<div class="container">
					<div  id="title" class="ten columns">
						<h1><?php echo esc_html($title); ?></h1>
					</div>
					<div id="breadcrumbs" class="six columns">
						<?php if($minti_data['titlebar_breadcrumbs'] == 1) { minti_breadcrumbs(); } ?>
					</div>
				</div>
			</div>
		<?php } elseif($minti_data['titlebar_layout'] == 'featuredimagecenter') { ?>
			<div id="fullimagecenter" class="titlebar" style="background-image: url( <?php echo esc_url($minti_data['titlebar_image']['url']); ?> );">
				<div id="fullimagecentertitle">
					<div class="container">
						<div class="sixteen columns">
							<h1><?php echo esc_html($title); ?></h1>
						</div>
					</div>
				</div>
			</div>
		<?php } elseif($minti_data['titlebar_layout'] == 'transparentimage') { ?>
			<div id="transparentimage" class="titlebar" style="background-image: url( <?php echo esc_url($minti_data['titlebar_image']['url']); ?> );">
				<div class="titlebar-overlay"></div>
				<div class="container">
					<div class="sixteen columns">
						<h1><?php echo esc_html($title); ?></h1>
					</div>
				</div>
			</div>
		<?php } elseif($minti_data['titlebar_layout'] == 'notitle') { ?>
			<div id="notitlebar"></div>
		<?php } ?>

<?php } // End Else ?>

<?php } else { // If no post page ?>

<?php
			// Define the Title for different Pages
			if ( is_home() ) { $title = 'Blog'; }
			elseif( is_search() ) {
				$allsearch = new WP_Query("s=$s&showposts=-1");
				$count = $allsearch->post_count;
				wp_reset_postdata();
				$title = $count . ' ';
				$title .= __('Search results for:', 'minti');
				$title .= ' ' . get_search_query();
			}
			elseif( class_exists('Woocommerce') && is_woocommerce() ) { $title = $minti_data['text_titlebar_woocommerce']; }
			elseif( is_archive() ) {
				if (is_category()) { 	$title = single_cat_title('',false); }
				elseif( is_tag() ) { 	$title = __('Posts Tagged:', 'minti') . ' ' . single_tag_title('',false); }
				elseif (is_day()) { 	$title = __('Archive for', 'minti') . ' ' . get_the_time('F jS, Y'); }
				elseif (is_month()) { 	$title = __('Archive for', 'minti') . ' ' . get_the_time('F Y'); }
				elseif (is_year()) { 	$title = __('Archive for', 'minti') . ' ' . get_the_time('Y'); }
				elseif (is_author()) { 	$title = __('Author Archive for', 'minti') . ' ' . get_the_author(); }
				elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { $title = __('Blog Archives', 'minti'); }
				else{
					$title = single_term_title( "", false );
					if ( $title == '' ) { // Fix for templates that are archives
						$post_id = $post->ID;
						$title = get_the_title($post_id);
					}
				}
			}
			elseif( is_404() ) { $title = __('Oops, this Page could not be found.', 'minti'); }
			elseif( get_post_type() == 'post' ) { $title = $minti_data['text_titlebar_blog']; }
			else { $title = get_the_title(); }
		?>

		<?php if($minti_data['titlebar_layout'] == 'title') { ?>
			<div id="fulltitle" class="titlebar">
				<div class="container">
					<div  id="title" class="ten columns">
						<h1><?php echo esc_html($title); ?></h1>
					</div>
					<div id="breadcrumbs" class="six columns">
						<?php if($minti_data['titlebar_breadcrumbs'] == 1) { minti_breadcrumbs(); } ?>
					</div>
				</div>
			</div>
		<?php } elseif($minti_data['titlebar_layout'] == 'featuredimagecenter') { ?>
			<div id="fullimagecenter" class="titlebar" style="background-image: url( <?php echo esc_url($minti_data['titlebar_image']['url']); ?> );">
				<div id="fullimagecentertitle">
					<div class="container">
						<div class="sixteen columns">
							<h1><?php echo esc_html($title); ?></h1>
						</div>
					</div>
				</div>
			</div>
		<?php } elseif($minti_data['titlebar_layout'] == 'transparentimage') { ?>
			<div id="transparentimage" class="titlebar" style="background-image: url( <?php echo esc_url($minti_data['titlebar_image']['url']); ?> );">
				<div class="titlebar-overlay"></div>
				<div class="container">
					<div class="sixteen columns">
						<h1><?php echo esc_html($title); ?></h1>
					</div>
				</div>
			</div>
		<?php } elseif($minti_data['titlebar_layout'] == 'notitle') { ?>
			<div id="notitlebar"></div>
		<?php } ?>

<?php } // End Else ?>
