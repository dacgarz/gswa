function get_tinymce_content( id_editor, id_editor_wrap ){
    if (jQuery( id_editor_wrap ).hasClass("tmce-active")){
        return tinyMCE.activeEditor.getContent();
    }else if (jQuery( id_editor_wrap ).hasClass("html-active")){
        return jQuery(id_editor).val();
    }
}

jQuery(document).ready(function() {

 ohDrag();
 
 jQuery('#miglaOfflineSettings').click(function(){

 	       var gateways = [];
       
       jQuery('li.formfield').each(function(){
          var temp = [];
          var key    = jQuery(this).find('.mg_status_gateways').val();
          if( jQuery(this).find('.mg_status_gateways').is(':checked') )
          {
              temp = [ key, true ];
          }else{
              temp = [ key, false ];
          }
          gateways.push( temp );
       });

       //alert( JSON.stringify(gateways) );

       jQuery.ajax({
	        type : "post",
	        url  :  miglaAdminAjax.ajaxurl, 
		data : {  action: "miglaA_update_me", key:'migla_gateways_order', value:gateways },
		success: function(msg) { 
                   saved('#miglaUpdateGatewayOrder');         
                }
       })  ; //ajax	

    jQuery.ajax({
       type   : "post",
       url    :  miglaAdminAjax.ajaxurl,  
       data   :   { action: "miglaA_update_me", key:'migla_offline_tab', value : jQuery('#mg_offline_tab').val() },
       success: function() { saved('#miglaOfflineSettings');   }
    }); //ajax 

 });

 jQuery('#miglaInfoOffline').click(function(){

   jQuery.ajax({
      type : "post",
      url :  miglaAdminAjax.ajaxurl,  
      data :   {action: "miglaA_update_me", key:'migla_offline_info', value:get_tinymce_content( '#mg_offinfo_editor', '#wp-mg_offinfo_editor-wrap') },
      success: function() { saved('#miglaInfoOffline'); }
   }); //ajax 

 });

 jQuery('#migla_save_waiting_text').click(function(){

     jQuery.ajax({
	type   : "post",
	url    :  miglaAdminAjax.ajaxurl, 
	data   : {action: "miglaA_update_me", key:'migla_wait_offline', value: jQuery('#mg_waiting_offline').val() },
	success: function(msg) {  
                 }
     })  ; //ajax	

     jQuery.ajax({
	type   : "post",
	url    :  miglaAdminAjax.ajaxurl, 
	data   : {action: "miglaA_update_me", key:'migla_thankyou_offline', value: jQuery('#mg_thankyou_offline').val() },
	success: function(msg) {  
                    saved('#migla_save_waiting_text');
                 }
     })  ; //ajax	

 });

 jQuery('#miglaSaveMsgOffline').click(function(){

     var isSend = 'no';
     if( jQuery('#mSendOfflineEmail').is(':checked') ){ isSend = 'yes'; }

     jQuery.ajax({
	type   : "post",
	url    :  miglaAdminAjax.ajaxurl, 
	data   : {action: "miglaA_update_me", key:'migla_send_offmsg', value: isSend },
	success: function(msg) {  
                 }
     })  ; //ajax	

     jQuery.ajax({
	type   : "post",
	url    :  miglaAdminAjax.ajaxurl, 
	data   : {action: "miglaA_update_me", key:'migla_offmsg_thankSbj', value: jQuery('#migla_OfflineESbj').val() },
	success: function(msg) {  
                 }
     })  ; //ajax	

     jQuery.ajax({
	type   : "post",
	url    :  miglaAdminAjax.ajaxurl, 
	data   : {action: "miglaA_update_me", key:'migla_offmsg_body', value: jQuery('#migla_OfflineEBody').val() },
	success: function(msg) {  
                 }
     })  ; //ajax	

     jQuery.ajax({
	type   : "post",
	url    :  miglaAdminAjax.ajaxurl, 
	data   : {action: "miglaA_update_me", key:'migla_offmsg_signature', value: jQuery('#migla_OfflineESig').val() },
	success: function(msg) {  
                    saved('#miglaSaveMsgOffline');
                 }
     })  ; //ajax	

 });

 jQuery('#miglaTestEmail').click(function(){

     jQuery.ajax({
	type   : "post",
	url    :  miglaAdminAjax.ajaxurl, 
	data   : {action: "miglaA_test_offline_email", testemail : jQuery('#miglaTestEmailAdd').val()  },
	success: function(msg) {  
                    saved('#miglaTestEmail');
                 }
     })  ; //ajax
	
 });


//// Authorize.NET BUTTON CHOICE ////////////////////
jQuery('#miglaUploadOfflineBtn').click(function() {
   formfield = jQuery('#mg_upload_image').attr('name');
   tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
   return false;
});
 
window.send_to_editor = function(html) {
 imgurl = jQuery('img',html).attr('src');
 jQuery('#mg_upload_image').val(imgurl);
 tb_remove();
}

jQuery('#miglaSaveOfflineBtnUrl').click(function(){
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'miglaOfflineButtonChoice', value:'imageUpload' },
        success: function(msg) {  
        }
   }); //ajax
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_offlinebuttonurl', value:jQuery('#mg_upload_image').val() },
        success: function(msg) {  
          saved('#miglaSaveOfflineBtnUrl');
        }
   }); //ajax
});

jQuery('#miglaSaveOfflineBtnNone').click(function(){
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'miglaOfflineButtonChoice', value:'none' },
        success: function(msg) { 
            saved('#miglaSaveOfflineBtnNone'); 
        }
   }); //ajax
});

jQuery('#miglaCSSButtonPickerSave').click(function(){
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'miglaOfflineButtonChoice', value:'cssButton' },
        success: function(msg) {  
        }
   }); //ajax
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_offlinecssbtnstyle', value:jQuery('#mg_CSSButtonPicker').val() },
        success: function(msg) {  
        }
   }); //ajax
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_offlinecssbtntext', value:jQuery('#mg_CSSButtonText').val() },
        success: function(msg) {  
        }
   }); //ajax
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_offlinecssbtnclass', value:jQuery('#mg_CSSButtonClass').val()},
        success: function(msg) {  
          saved('#miglaCSSButtonPickerSave');
        }
   }); //ajax
});


}); //ready

function ohDrag(){
  jQuery('#default_payment_section').find("ul.containers").sortable({
    placeholder: "ui-state-highlight-container",
    revert: true,
    forcePlaceholderSize: true,
    axis: 'y',
    update: function (e, ui) {
    },
    start: function (e, ui) {
    }
  }).bind('sortstop', function (event, ui) {
  });

function SetSortableRows(rows)
{
    rows.sortable({
    placeholder: "ui-state-highlight-row",
    connectWith: "ul.rows:not(.containers)",
    containment: "ul.containers",
    helper: "clone",
    revert: true,
    forcePlaceholderSize: true,
    axis: 'y',
    start: function (e, ui) {
    },
    update: function (e, ui) {
    },
    stop: function(e, ui){
    },
    received: function(e, ui){
    }
   }).bind('sortstop', function (event, ui) {

  }); 
}
  SetSortableRows(jQuery("ul.rows"));

}