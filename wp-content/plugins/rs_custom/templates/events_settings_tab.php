<div class="tribe-settings-form-wrap">
  <h3>Event Index Page</h3>
  <div class="clear"></div>
  <fieldset>
    <legend class="tribe-field-label">Title</legend>
    <div class="tribe-field-wrap">
      <input name='index_settings_title' value='<?php print $default_value['title']; ?>'>
    </div>
  </fieldset>
  <div class="clear"></div>
  <fieldset>
    <legend class="tribe-field-label">Title CSS Style</legend>
    <div class="tribe-field-wrap">
      <input name='index_settings_title_style' value='<?php print $default_value['title_style']; ?>'>
    </div>
  </fieldset>
  <div class="clear"></div>
  <fieldset>
    <legend class="tribe-field-label">Header Image</legend>
    <div class="tribe-field-wrap">
      <img id="preview" src="<?php print $default_value['header_image']; ?>" style="width: auto; height: 100px;">
      <input id="image-url" type="text" readonly="readonly" name="index_settings_image" value='<?php print $default_value['header_image']; ?>' style="display: none"/>
      <input id="upload-button" type="button" class="button" value="Upload Image" />
    </div>
  </fieldset>
  <script type="text/javascript">
    jQuery(document).ready(function($){
      var mediaUploader;
      $('#upload-button').click(function(e) {
        e.preventDefault();
        if (mediaUploader) {
          mediaUploader.open();
          return;
        }
        mediaUploader = wp.media.frames.file_frame = wp.media({
          title: 'Choose Image',
          button: {
            text: 'Choose Image'
          }, multiple: false });

        mediaUploader.on('select', function() {
          attachment = mediaUploader.state().get('selection').first().toJSON();
          $('#image-url').val(attachment.url);
          $('#preview').attr('src', attachment.url);
        });
        mediaUploader.open();
      });

    });
  </script>
  <div class="clear"></div>
</div>