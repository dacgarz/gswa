jQuery(document).ready(function() {	

  jQuery('#migla_security_save').click(function()
  {
                  var isVerifiySSL = 'no';
                  if( jQuery('#miglaAuthorizeVerifySSL').is(':checked') ){ isVerifiySSL = 'yes'; }
		  jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_update_me", key:'migla_authorize_verifySSL', value: isVerifiySSL },
			success: function(msg) {                         
			}
		  })  ; //ajax	
  
    var cc_validator = 'no';
    if( jQuery('#migla_credit_card_validator').is(':checked') ) cc_validator = 'yes' ;
    
       jQuery.ajax({
		type : "post",
		url  : miglaAdminAjax.ajaxurl, 
		data : {
		          action : 'miglaA_update_me', 
		          key    : 'migla_credit_card_validator', 
		          value  : cc_validator 
		        },
		success: function(msg) {  
			}
       })  ; //ajax	
       
    var cc_avs = 'no';
    if( jQuery('#migla_credit_card_avs').is(':checked') ) cc_avs = 'yes';
    
       jQuery.ajax({
		type : "post",
		url  : miglaAdminAjax.ajaxurl, 
		data : {
		          action : 'miglaA_update_me', 
		          key    : 'migla_credit_card_avs', 
		          value  : cc_avs
		        },
		success: function(msg) { 
			}
       })  ; //ajax	

		var mg_captcha = 'no';
		if( jQuery('#migla_use_captcha').is(':checked') ) mg_captcha = 'yes';
		
		jQuery.ajax({
			type : "post",
			url  : miglaAdminAjax.ajaxurl, 
			data : {
					  action : 'miglaA_update_me', 
					  key    : 'migla_use_captcha', 
					  value  : mg_captcha
					},
			success: function(msg) { 
				}
		})  ; //ajax			
	   
		jQuery.ajax({
			type : "post",
			url  : miglaAdminAjax.ajaxurl, 
			data : {
					  action : 'miglaA_update_me', 
					  key    : 'migla_captcha_site_key', 
					  value  : jQuery('#migla_captcha_site_key').val()
					},
			success: function(msg) { 
				}
		})  ; //ajax	

		jQuery.ajax({
			type : "post",
			url  : miglaAdminAjax.ajaxurl, 
			data : {
					  action : 'miglaA_update_me', 
					  key    : 'migla_captcha_secret_key', 
					  value  : jQuery('#migla_captcha_secret_key').val()
					},
			success: function(msg) { 
					   saved('#migla_security_save'); 
				}
		})  ; //ajax		

   });  

   jQuery('#miglaUpdateAuthorizeSettings').click(function() {

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

       var info = {}; var cc_info_array = [];
       info = ['tab_name',      cleanIt( jQuery('#mg_tab-authorize').val() ) ] ; cc_info_array.push(info);
       info = ['firstname_label',      cleanIt( jQuery('#mg_name-authorize').val() ) ] ; cc_info_array.push(info);
       info = ['firstname_placeholder', cleanIt( jQuery('#mg_placeholder-name').val() ) ] ; cc_info_array.push(info);
       info = ['lastname_label',      cleanIt( jQuery('#mg_lname-authorize').val() ) ] ; cc_info_array.push(info);
       info = ['lastname_placeholder', cleanIt( jQuery('#mg_placeholder-lname').val() ) ] ; cc_info_array.push(info);
       info = ['number_label',       cleanIt( jQuery('#mg_cardnumber-authorize').val() ) ] ; cc_info_array.push(info);
       info = ['number_placeholder', cleanIt( jQuery('#mg_placeholder-card').val() ) ] ; cc_info_array.push(info);
       info = ['cvc_label' ,        cleanIt( jQuery('#mg_cvc-authorize').val() ) ] ; cc_info_array.push(info);
       info = ['cvc_placeholder' , cleanIt( jQuery('#mg_placeholder-CVC').val() ) ] ; cc_info_array.push(info);

       jQuery.ajax({
           type : "post",
           url  :  miglaAdminAjax.ajaxurl, 
           data : {action: "miglaA_update_me", key:'migla_authorize_cc_info', value:cc_info_array },
           success: function(msg) {  
              saved('#miglaUpdateAuthorizeSettings');
           }
       }); //ajax

    	
    });	

    jQuery('#miglaUpdateAuthKeys').click(function(){

		  jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_update_me", key:'migla_authorize_api_key', value: jQuery('#miglaAuthorizeAPIKey').val() },
			success: function(msg) {                          
			}
		  })  ; //ajax	

		  jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_update_me", key:'migla_authorize_trans_key', value: jQuery('#miglaAuthorizeTranKey').val() },
			success: function(msg) {                         
			}
		  })  ; //ajax	

		  jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {  action : "miglaA_update_me", key:'migla_payment_authorize', 
                                  value  : jQuery('input[name=miglaAuthorize]:checked').val() 
                               },
			success: function(msg) { 
                             saved('#miglaUpdateAuthKeys');
			}
		  })  ; //ajax	

    });


//// Authorize.NET BUTTON CHOICE ////////////////////
jQuery('#miglaUploadAuthorizeBtn').click(function() {
   formfield = jQuery('#mg_upload_image').attr('name');
   tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
   return false;
});
 
window.send_to_editor = function(html) {
 imgurl = jQuery('img',html).attr('src');
 jQuery('#mg_upload_image').val(imgurl);
 tb_remove();
}

jQuery('#miglaSaveAuthorizeBtnUrl').click(function(){
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'miglaAuthorizeButtonChoice', value:'imageUpload' },
        success: function(msg) {  
        }
   }); //ajax
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_authorizebuttonurl', value:jQuery('#mg_upload_image').val() },
        success: function(msg) {  
          saved('#miglaSaveAuthorizeBtnUrl');
        }
   }); //ajax
});

jQuery('#miglaCSSButtonPickerSave').click(function(){
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'miglaAuthorizeButtonChoice', value:'cssButton' },
        success: function(msg) {  
        }
   }); //ajax
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_authorizecssbtnstyle', value:jQuery('#mg_CSSButtonPicker').val() },
        success: function(msg) {  
        }
   }); //ajax
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_authorizecssbtntext', value:jQuery('#mg_CSSButtonText').val() },
        success: function(msg) {  
        }
   }); //ajax
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_authorizecssbtnclass', value:jQuery('#mg_CSSButtonClass').val()},
        success: function(msg) {  
          saved('#miglaCSSButtonPickerSave');
        }
   }); //ajax
});

 jQuery('#miglaSaveCCInfo').click(function(){

     jQuery.ajax({
         type : "post",
         url :  miglaAdminAjax.ajaxurl, 
         data : {  action : 'miglaA_update_me' , key:'migla_wait_authorize', value : jQuery('#mg_waiting_authorize').val()  },
         success: function(msg) { 
         }
     }); //ajax

     var info = {}; var cc_info_array = [];
     info = ['tab_name',      cleanIt( jQuery('#mg_tab-authorize').val() ) ] ; cc_info_array.push(info);
     info = ['firstname_label',      cleanIt( jQuery('#mg_name-authorize').val() ) ] ; cc_info_array.push(info);
     info = ['firstname_placeholder', cleanIt( jQuery('#mg_placeholder-name').val() ) ] ; cc_info_array.push(info);
     info = ['lastname_label',      cleanIt( jQuery('#mg_lname-authorize').val() ) ] ; cc_info_array.push(info);
     info = ['lastname_placeholder', cleanIt( jQuery('#mg_placeholder-lname').val() ) ] ; cc_info_array.push(info);
     info = ['number_label',       cleanIt( jQuery('#mg_cardnumber-authorize').val() ) ] ; cc_info_array.push(info);
     info = ['number_placeholder', cleanIt( jQuery('#mg_placeholder-card').val() ) ] ; cc_info_array.push(info);
     info = ['cvc_label' ,        cleanIt( jQuery('#mg_cvc-authorize').val() ) ] ; cc_info_array.push(info);
     info = ['cvc_placeholder' , cleanIt( jQuery('#mg_placeholder-CVC').val() ) ] ; cc_info_array.push(info);

     jQuery.ajax({
         type : "post",
         url :  miglaAdminAjax.ajaxurl, 
         data : {action: "miglaA_update_me", key:'migla_authorize_cc_info', value:cc_info_array },
         success: function(msg) {  
            saved('#miglaSaveCCInfo');
         }
     }); //ajax

 });

 ohDrag();

   jQuery('#migla_use_captcha').click(function(){
      if( jQuery(this).is(':checked') )
         jQuery('.mg_captcha_keys').show();
      else
         jQuery('.mg_captcha_keys').hide();
   });

});

function cleanIt( dirty ){
  var _dirty = new String(dirty);
  var clean ;
  
  clean = _dirty.replace(/\//gi,"//");  
  clean = clean.replace(/"/gi,"[q]");
  clean = clean.replace(/'/gi,"[q]");
  return clean;
}

///////////////////////////// SORTABLE ///////////////////////////////////////////////
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
