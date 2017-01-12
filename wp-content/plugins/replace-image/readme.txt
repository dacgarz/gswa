=== Replace Image ===
Contributors: hearken
Donate link: https://potentplugins.com/donate/?utm_source=replace-image&utm_medium=link&utm_campaign=wp-plugin-readme-donate-link
Tags: replace, overwrite, image, images, media, attachment, attachments
Requires at least: 3.5
Tested up to: 4.7
Stable tag: 1.1.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Upload a new version of an image without deleting the old image attachment, so that references to the image remain intact.

== Description ==

The Replace Image plugin adds a button to the Attachment Details screen which allows you to upload or select an image to replace the current image while retaining the URL and attachment ID. This allows you to easily swap in an updated version of an image without having to re-select it in theme settings pages, post/page content, or anywhere else where it might be referenced.

**Important:** Disable your browser's cache and any WordPress caching plugins before use; otherwise, the plugin may appear not to work. See Tools > Replace Image for instructions.

If you like this plugin, please consider [making a donation](https://potentplugins.com/donate/?utm_source=replace-image&utm_medium=link&utm_campaign=wp-plugin-repo-donate-link).

== Installation ==

1. Click "Plugins" > "Add New" in the WordPress admin menu.
1. Search for "Replace Image".
1. Click "Install Now".
1. Click "Activate Plugin".

Alternatively, you can manually upload the plugin to your wp-content/plugins directory.

== Frequently Asked Questions ==

= I tried to replace an image but nothing happened. =

Your browser is likely still caching the old image. Try doing a hard refresh (Ctrl + F5 on Windows, Apple + R / Command + R on Mac) while viewing the page on the frontend. Note that the backend seems to retain cached image thumbnails even after a hard refresh.

== Screenshots ==

1. The Replace Image button in the Attachment Details screen (opened by clicking on an image in the Media Library).

== Changelog ==

= 1.1.3 =
* Fixed undefined offset error per https://wordpress.org/support/topic/undefined-index-0-on-line-100-mainfile?replies=1

= 1.1.1 =
* Added support for image replacement where the image file being replaced doesn't exist
* Removed anonymous functions

= 1.1 =
* Added functionality to prevent image caching in the Media Library

= 1.0.3 =
* Fixed a bug where the upload UI would temporarily stop working properly after replacing an image.

= 1.0 =
* Initial release


== Upgrade Notice ==