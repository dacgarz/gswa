/**
 * Author: Hearken Media
 * License: GNU General Public License version 2 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 */

function hm_replace_image() {
	
	frame = wp.media({
		title: "Choose Replacement Image",
		button: {
			text: "Replace Image"
		},
		multiple: false
	});
	
	frame.on("select", function() {
		
		jQuery("#hm_replace_image_with_fld").val(frame.state().get("selection").first().toJSON().id);
		if (jQuery("#hm_replace_image_with_fld").closest('.media-modal').length) {
			jQuery("#hm_replace_image_with_fld").change();
			location.href = 'upload.php';
		} else {
			jQuery("#hm_replace_image_with_fld").closest("form").submit();
		}
		
	});
	
	var frameEl = jQuery(frame.open().el);
	frameEl.find('.media-router > a:first-child').click();
	
}