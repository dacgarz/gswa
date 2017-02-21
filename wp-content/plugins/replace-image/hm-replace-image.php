<?php
/**
 * Plugin Name: Replace Image
 * Description: Upload a new version of an image without deleting the old image attachment, so that references to the image remain intact.
 * Version: 1.1.5
 * Author: Potent Plugins
 * Author URI: http://potentplugins.com/?utm_source=replace-image&utm_medium=link&utm_campaign=wp-plugin-author-uri
 * License: GNU General Public License version 2 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 */

add_action('admin_menu', 'hm_replace_image_admin_menu');
function hm_replace_image_admin_menu() {
	add_submenu_page('tools.php', 'Replace Image', 'Replace Image', 'upload_files', 'hm_replace_image', 'hm_replace_image_page');
}

add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'hm_replace_image_action_links');
function hm_replace_image_action_links($links) {
	array_unshift($links, '<a href="'.esc_url(get_admin_url(null, 'tools.php?page=hm_replace_image')).'">Instructions</a>');
	return $links;
}

function hm_replace_image_page() {
	echo('
		<div class="wrap">
			<h2>Replace Image</h2>
			<p>To use this tool, click the Replace Image button on the Attachment Details screen for the image you wish to replace (e.g. by clicking on the image in the Media Library). Then upload or select the image to use as a replacement. Do not choose an image already in use elsewhere, since the replacement image will be moved to overwrite the original image.</p>
			<h3>Important:</h3>
			<p>In order for this plugin to work correctly, please ensure the following:</p>
			<ol>
				<li><strong>Disable all WordPress caching plugins during development.</strong></li>
				<li>
					<strong>Disable your browser\'s cache.</strong><br /><br />
					In Google Chrome, hit F12 to open Developer Tools, click the Network tab, and ensure that &quot;Disable Cache&quot; is checked. Leave the Developer Tools window open during development to keep this setting in effect.<br /><br />
					<img src="'.plugins_url('images/disable-cache-chrome.png', __FILE__).'" alt="Disabling the cache in Google Chrome" /><br /><br />
					In Mozilla Firefox, hit F12, click the gearwheel icon, and ensure that &quot;Disable Cache (when toolbox is open)&quot; is checked. Leave the toolbox window open during development to keep this setting in effect.<br /><br />
					<img src="'.plugins_url('images/disable-cache-firefox.png', __FILE__).'" alt="Disabling the cache in Mozilla Firefox" />
				</li>
			</ol><br />
	');
	$potent_slug = 'replace-image';
	include(__DIR__.'/plugin-credit.php');
	echo('
		</div>
	');
}


add_action('admin_enqueue_scripts', 'hm_replace_image_enqueue_scripts');
function hm_replace_image_enqueue_scripts() {
	wp_enqueue_script('hm-replace-image', plugins_url('js/hm-replace-image.js', __FILE__));
}

add_action('edit_attachment', 'hm_replace_image_edit_attachment');
function hm_replace_image_edit_attachment($postId) {
	if (!empty($_POST['replaceWith']) && is_numeric($_POST['replaceWith'])) {
	
		$uploadDir = wp_upload_dir();
		$newFile = $uploadDir['basedir'].'/'.get_post_meta($_POST['replaceWith'], '_wp_attached_file', true);
		$oldFile = $uploadDir['basedir'].'/'.get_post_meta($postId, '_wp_attached_file', true);
		if (!file_exists(dirname($oldFile)))
			mkdir(dirname($oldFile), 0777, true);
		copy($newFile, $oldFile);
		$meta = wp_generate_attachment_metadata($postId, $oldFile);
		wp_update_attachment_metadata($postId, $meta);
		wp_delete_post($_POST['replaceWith'], true);
	}
	
}

add_filter('attachment_fields_to_edit', 'hm_replace_image_attachment_fields');
function hm_replace_image_attachment_fields($fields) {
	wp_enqueue_media();
	$fields['hm_image_replace'] = array();
	$fields['hm_image_replace']['label'] = '';
	$fields['hm_image_replace']['input'] = 'html';
	$fields['hm_image_replace']['html'] = '
		<button type="button" class="button-secondary button-large" onclick="hm_replace_image();">Replace Image</button>
		<input type="hidden" id="hm_replace_image_with_fld" name="replaceWith" />
		<p><strong>Warning:</strong> Replacing this image with another one will permanently delete the current image file, and the replacement image will be moved to overwrite this one. <a href="'.esc_url(get_admin_url(null, 'tools.php?page=hm_replace_image')).'" target="_blank">Instructions</a></p>
	';
	
	return $fields;
}


add_filter('wp_calculate_image_srcset', 'hm_replace_image_calculate_image_srcset');
function hm_replace_image_calculate_image_srcset($sources) {
	if (is_admin()) {
		foreach ($sources as $size => $source) {
			$source['url'] .= (strpos($source['url'], '?') === false ? '?' : '&').'_t='.time();
			$sources[$size] = $source;
		}
	}
	return $sources;
}

add_filter('wp_get_attachment_image_src', 'hm_replace_image_get_attachment_image_src');
function hm_replace_image_get_attachment_image_src($attr) {
	if (is_admin() && !empty($attr[0])) {
		$attr[0] .= (strpos($attr[0], '?') === false ? '?' : '&').'_t='.time();
	}
	return $attr;
}

add_filter('wp_prepare_attachment_for_js', 'hm_replace_image_prepare_attachment_for_js');
function hm_replace_image_prepare_attachment_for_js($response) {
	if (is_admin()) {
		if (strpos($response['url'], '?') !== false)
			$response['url'] .= (strpos($response['url'], '?') === false ? '?' : '&').'_t='.time();
		if (isset($response['sizes'])) {
			foreach ($response['sizes'] as $sizeName => $size) {
				$response['sizes'][$sizeName]['url'] .= (strpos($size['url'], '?') === false ? '?' : '&').'_t='.time();
			}
		}
	}
	return $response;
}


/* Review/donate notice */

register_activation_hook(__FILE__, 'hm_replace_image_first_activate');
function hm_replace_image_first_activate() {
	$pre = 'hm_replace_image';
	$firstActivate = get_option($pre.'_first_activate');
	if (empty($firstActivate)) {
		update_option($pre.'_first_activate', time());
	}
}
if (is_admin() && get_option('hm_replace_image_rd_notice_hidden') != 1 && time() - get_option('hm_replace_image_first_activate') >= (14*86400)) {
	add_action('admin_notices', 'hm_replace_image_rd_notice');
	add_action('wp_ajax_hm_replace_image_rd_notice_hide', 'hm_replace_image_rd_notice_hide');
}
function hm_replace_image_rd_notice() {
	$pre = 'hm_replace_image';
	$slug = 'replace-image';
	echo('
		<div id="'.$pre.'_rd_notice" class="updated notice is-dismissible"><p>Does the <strong>Replace Image</strong> plugin make your life easier?
		Please support our free plugin by <a href="https://wordpress.org/support/view/plugin-reviews/'.$slug.'" target="_blank">writing a review</a> and/or <a href="https://potentplugins.com/donate/?utm_source='.$slug.'&amp;utm_medium=link&amp;utm_campaign=wp-plugin-notice-donate-link" target="_blank">making a donation</a>!
		Thanks!</p></div>
		<script>jQuery(document).ready(function($){$(\'#'.$pre.'_rd_notice\').on(\'click\', \'.notice-dismiss\', function(){jQuery.post(ajaxurl, {action:\'hm_replace_image_rd_notice_hide\'})});});</script>
	');
}
function hm_replace_image_rd_notice_hide() {
	$pre = 'hm_replace_image';
	update_option($pre.'_rd_notice_hidden', 1);
}


?>