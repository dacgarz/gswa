<?php

/*
Plugin Name: Unicon Portfolio CPT
Plugin URI: http://themeforest.net/user/minti
Description: This Plugin will create a Portfolio Custom Post Type for Unicon WordPress Theme.
Version: 1.01
Author: minti
Author URI: http://mintithemes.com
License: Custom
License URI: http://themeforest.net/licenses 
*/

/* ----------------------------------------------------- */
/* Add Portfolio Custom Post Type
/* ----------------------------------------------------- */
function minti_portfolio_register() {  

	global $minti_data;

	if(isset($minti_data['text_portfolioslug']) && $minti_data['text_portfolioslug'] != ''){
		$portfolio_slug = $minti_data['text_portfolioslug'];
	} else {
		$portfolio_slug = 'portfolio-item';
	}
	
	$labels = array(
		'name' => __( 'Portfolio', 'minti-framework' ),
		'singular_name' => __( 'Portfolio Item', 'minti-framework' ),
		'add_new' => __( 'Add New Item', 'minti-framework' ),
		'add_new_item' => __( 'Add New Portfolio Item', 'minti-framework' ),
		'edit_item' => __( 'Edit Portfolio Item', 'minti-framework' ),
		'new_item' => __( 'Add New Portfolio Item', 'minti-framework' ),
		'view_item' => __( 'View Item', 'minti-framework' ),
		'search_items' => __( 'Search Portfolio', 'minti-framework' ),
		'not_found' => __( 'No portfolio items found', 'minti-framework' ),
		'not_found_in_trash' => __( 'No portfolio items found in trash', 'minti-framework' )
	);
	
    $args = array(  
        'labels' => $labels,
        'public' => true,  
        'show_ui' => true,  
        'capability_type' => 'post',  
        'hierarchical' => false,  
        'menu_icon' => 'dashicons-portfolio',
        'rewrite' => array('slug' => $portfolio_slug), // Permalinks format
        'supports' => array('title', 'editor', 'thumbnail', 'comments', 'excerpt')  
       );  
  
    register_post_type( 'portfolio' , $args );  
}
add_action('init', 'minti_portfolio_register', 1);   

/* ----------------------------------------------------- */
/* Register Taxonomy
/* ----------------------------------------------------- */
function minti_portfolio_taxonomy() {
	
	register_taxonomy("portfolio_filter", array("portfolio"), array("hierarchical" => true, "label" => "Portfolio Filter", "singular_label" => "Project Filter", "rewrite" => true));

}
add_action('init', 'minti_portfolio_taxonomy', 1);   

/* ----------------------------------------------------- */
/* Add Columns to Portfolio Edit Screen
 * http://wptheming.com/2010/07/column-edit-pages/
/* ----------------------------------------------------- */
function minti_portfolio_edit_columns( $portfolio_columns ) {
	$portfolio_columns = array(
		"cb" => "<input type=\"checkbox\" />",
		"title" => __('Title', 'minti-framework'),
		"thumbnail" => __('Thumbnail', 'minti-framework'),
		"portfolio_filter" => __('Filter', 'minti-framework'),
		"author" => __('Author', 'minti-framework'),
		"comments" => __('Comments', 'minti-framework'),
		"date" => __('Date', 'minti-framework'),
	);
	$portfolio_columns['comments'] = '<div class="vers"><img alt="Comments" src="' . esc_url( admin_url( 'images/comment-grey-bubble.png' ) ) . '" /></div>';
	return $portfolio_columns;
}
add_filter( 'manage_edit-portfolio_columns', 'minti_portfolio_edit_columns' );

/* ----------------------------------------------------- */

function minti_portfolio_column_display( $portfolio_columns, $post_id ) {
	
	switch ( $portfolio_columns ) {
		
		// Display the thumbnail in the column view
		case "thumbnail":
			$width = (int) 80;
			$height = (int) 80;
			$thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
			
			// Display the featured image in the column view if possible
			if ( $thumbnail_id ) {
				$thumb = wp_get_attachment_image( $thumbnail_id, array($width, $height), true );
			}
			if ( isset( $thumb ) ) {
				echo $thumb; // No need to escape
			} else {
				echo __('None', 'minti-framework');
			}
			break;	
			
		// Display the portfolio tags in the column view
		case "portfolio_filter":
		
		if ( $category_list = get_the_term_list( $post_id, 'portfolio_filter', '', ', ', '' ) ) {
			echo $category_list; // No need to escape
		} else {
			echo __('None', 'minti-framework');
		}
		break;			
	}
}
add_action( 'manage_posts_custom_column', 'minti_portfolio_column_display', 10, 2 );

/* ----------------------------------------------------- */
/* EOF */
/* ----------------------------------------------------- */
