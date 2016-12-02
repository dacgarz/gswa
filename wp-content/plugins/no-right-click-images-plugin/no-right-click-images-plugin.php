<?PHP
/*
Plugin Name: No Right Click Images Plugin
Plugin URI: http://www.BlogsEye.com/
Description: Uses Javascript to prevent right clicking of images to help keep leaches from copying images
Version: 2.5
Author: Keith P. Graham
Author URI: http://www.BlogsEye.com/

This software is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/
if (!defined('ABSPATH')) exit; // just in case

// (second time updating WordPress repository)

/************************************************************
* 	kpg_no_rc_img_fixup()
*	Shows the javascript in the footer so that the image events can be adjusted
*
*************************************************************/
function kpg_no_rc_img_fixup() {
	// this is the No Right Click Images functionality.
	// now we have to get the options
	
	$options=get_option('kpg_no_right_click_image');
	if (empty($options)||!is_array($options)) $options=array();
	$replace='N';
	$drag='Y';
	$allowforlogged='N';
	$altimg='';
	extract($options);
	if ($replace!='Y') $replace='N';
	if ($drag!='Y') $drag='N';
	if ($allowforlogged!='Y') $allowforlogged='N';
	if (empty($cell)) $cell='N';
	if ($cell!='Y') $cell='N';
	// if the user is logged in and the option is set, let them copy images
	if ($allowforlogged=='Y' && is_user_logged_in() ) { return; }	
	$dir = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	$img = addslashes($dir.'not.gif');
	$js = addslashes($dir.'no-right-click-images.js');
	$altimg=trim($altimg);
	if (!empty($altimg)) $img=$altimg;
	
?>
<script language="javascript" type="text/javascript">var kpg_cell="<?php echo $cell; ?>";var kpg_nrci_image="<?php echo $img; ?>";var kpg_nrci_extra="<?php echo $replace; ?>";var kpg_nrci_drag="<?php echo $drag; ?>";</script>
<script language="javascript" type="text/javascript" src="<?php echo $js; ?>"></script>

<?php
}
function kpg_no_rc_img_control()  {
	$options=get_option('kpg_no_right_click_image');
	if (empty($options)||!is_array($options)) $options=array();
	$replace='N';
	$drag='Y';
	$allowforlogged='N';
	$altimg='';
	extract($options);
	if ($replace!='Y') $replace='N';
	if (empty($drag)) $drag='Y';
	if ($drag!='Y') $drag='N';
	if (empty($cell)) $cell='N';
	if ($cell!='Y') $cell='N';
	if ($allowforlogged!='Y') $allowforlogged='N';
	if (array_key_exists('kpg_no_rc_nonce',$_POST)&&wp_verify_nonce($_POST['kpg_no_rc_nonce'],'kpg_no_rc')) { 
		// need to update replace
		if (array_key_exists('kpg_replace_image',$_POST)) {
			$replace=stripslashes($_POST['kpg_replace_image']);
		} else {
			$replace='N';
		}
		if (array_key_exists('kpg_prevent_drag',$_POST)) {
			$drag=stripslashes($_POST['kpg_prevent_drag']);
		} else {
			$drag='N';
		}
		if (array_key_exists('kpg_allowforlogged',$_POST)) {
			$allowforlogged=stripslashes($_POST['kpg_allowforlogged']);
		} else {
			$allowforlogged='N';
		}
		if (array_key_exists('kpg_cell',$_POST)) {
			$cell=stripslashes($_POST['kpg_cell']);
		} else {
			$cell='N';
		}
		if (array_key_exists('altimg',$_POST)) {
			$altimg=stripslashes($_POST['altimg']);
		} else {
			$altimg='';
		}
		if ($replace!='Y') $replace='N';
		if ($drag!='Y') $drag='N';
		if ($allowforlogged!='Y') $allowforlogged='N';
		if (empty($cell)) $cell='N';
		if ($cell!='Y') $cell='N';
		$options['replace']=$replace;
		$options['drag']=$drag;
		$options['cell']=$cell;
		$options['altimg']=$altimg;
		$options['allowforlogged']=$allowforlogged;
		update_option('kpg_no_right_click_image', $options);

	}
   $nonce=wp_create_nonce('kpg_no_rc');
   	$dir = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	$img = addslashes($dir.'not.gif');
	$altimg=trim($altimg);
	if (!empty($altimg)) $img=$altimg;

?>

<div class="wrap">
  <h2>No Right Click Images Plugin</h2>
 
  <div style="position:relative;float:right;width:35%;background-color:ivory;border:#333333 medium groove;padding:4px;margin-left:4px;">
    <p>This plugin is free and I expect nothing in return. If you would like to support my programming effforts, please <a target="_blank" href="http://www.blogseye.com/donate/">donate a small amount</a> to help keep me interested in this project.</p>
  </div>
  <h4>The No Right Click Images Plugin is installed and working correctly.</h4>
  <p>This plugin installs some javascript in the footer of every page. When your page finishes loading, the javascript sets properties on the images to stop them from being dragged or right clicked. </p>
  <p>This is a major revision in the way the plugin works. <a href="mailto:bugs@blogseye.com">Please report all bugs</a> as soon as possible. </p>
  <p>There are many ways to bypass this plugin and it is impossible to prevent a determined and resourceful user from stealing images, but this plugin will prevent casual users from glomming your images. </p>
  <p>The context menu is disabled on simple elements with background images, but will not work in some cases depending on which element receives the mouse click. </p>
  <p>If you have uploaded your images to WordPress so that the images from the gallery can be opened in their own window, then this plugin will not work on the clicked image. Always upload images using FTP and insert the image via URL with no  link to the image other than the IMG tag. </p>  
  <form method="post" action="">
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="kpg_no_rc_nonce" value="<?php echo $nonce;?>" />
	<table cellpadding="2">
	<tr>
		<td>Replace image on right mouse click</td>
		<td><input type="checkbox" <?php if ($replace=='Y') {echo 'checked="true"';} ?>value="Y" name="kpg_replace_image"></td>
		<td><p>This temporarily replaces the target image with the image above. FireFox users can prevent the plugin from stopping the default context menu. Use this to prevent FireFox users from right clicking the image. <br />
		  This may interfere with other plugins, especially jquery sliders and galleries. Check your blog pages for functionality and turn this off if your images are not behaving correctly. The original image will reappear in about 10 seconds or when the mouse is clicked. </p>
	    </td>
	</tr>
	<tr>
		<td>Prevent Drag and Drop</td>
		<td><input type="checkbox" <?php if ($drag=='Y') {echo 'checked="true"';} ?>value="Y" name="kpg_prevent_drag"></td>
		<td>Users may be able to drag an image to the desktop. If you wish to prevent this, check the box. If this conflicts with a plugin that uses drag and drop, you may wish to uncheck this.
		</td>
	</tr>
	<tr>
		<td>Allow for Logged Users</td>
		<td><input type="checkbox" <?php if ($allowforlogged=='Y') {echo 'checked="true"';} ?>value="Y" name="kpg_allowforlogged"></td>
		<td>You may wish to allow logged in users to copy images. You can do this by checking this box.
		</td>
	</tr>
	<tr>
		<td>Stop saving on Smart Phones</td>
		<td><input type="checkbox" <?php if ($cell=='Y') {echo 'checked="true"';} ?>value="Y" name="kpg_cell"></td>
		<td>You can stop users from pressing and holding an image to get a save menu, but it may interfere with other gestures on some phones. Test this before allowing it on your site.
		</td>
	</tr>
	
	<tr>
		<td align="top">Use an alternate replacement image</td>
		<td align="top"><input type="text" name="altimg" value="<?php echo $altimg; ?>"></td>
		<td>If you don't like the alternate image then put the url of an alternat image here.<br/>
		The image will display below after you save your changes. If no image displays then you typed the URL wrong.
		<img width="80" src="<?php echo $img; ?>" style="margin:2px;"/>
		</td>
	</tr>

	
	
	</table>
    <p>
      <input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
  </form>
</div>
<?php
}
function kpg_no_rc_img_init() {
   add_options_page('No Right Click Images', 'No Right Click Images', 'manage_options',__FILE__,'kpg_no_rc_img_control');
}
  // Plugin added to Wordpress plugin architecture
	add_action('admin_menu', 'kpg_no_rc_img_init');	
	add_action( 'wp_footer', 'kpg_no_rc_img_fixup' );
// uninstall
function kpg_no_rc_img_uninstall() {
	if(!current_user_can('manage_options')) {
		die('Access Denied');
	}
	delete_option('kpg_no_right_click_image'); 
	return;
}
if ( function_exists('register_uninstall_hook') ) {
	register_uninstall_hook(__FILE__, 'kpg_no_rc_img_uninstall');
}

// bottom	
?>