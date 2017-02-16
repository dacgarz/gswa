jQuery(document).ready(function() {	

   jQuery('#migla_paypal_std_pdt_choice').click(function(){
       if ( jQuery(this).val() == 'std' )
       {
           jQuery('.mg_paypal_std').show();
           jQuery('.mg_paypal_pdt').hide();
       }else{
           jQuery('.mg_paypal_std').hide();
           jQuery('.mg_paypal_pdt').show();          
       }       
   });

   jQuery('#migla_credit_card_avs').click(function(){
       if ( jQuery(this).is(':checked') )
       {
           jQuery('#migla_div_avs_level').show();
       }else{
           jQuery('#migla_div_avs_level').hide();
       }
   });

    ohDrag();

    jQuery('#mg_paypalpro_recurring').click(function(){
         if( jQuery(this).val() == 'sec' )
         {
            jQuery('#div_set_SEC_page').show();
         }else{
            jQuery('#div_set_SEC_page').hide();
         }
    });

    jQuery('#miglaUpdatePaypalSettings').click(function() {

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
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_update_me", key:'migla_paypal_method', value: jQuery('#mg_paypal_method').val() },
			success: function(msg) {     
			}
		  })  ; //ajax	  

		var info = {}; var cc_info_array = [];
		 info = ['tab_name',      cleanIt( jQuery('#mg_tab-paypalpro').val() ) ] ; cc_info_array.push(info);
		 info = ['radio1_label',      cleanIt( jQuery('#mg_paypalpro-radio1').val() ) ] ; cc_info_array.push(info);
		 info = ['radio2_label',      cleanIt( jQuery('#mg_paypalpro-radio2').val() ) ] ; cc_info_array.push(info);
		 info = ['firstname_label',      cleanIt( jQuery('#mg_name-paypalpro').val() ) ] ; cc_info_array.push(info);
		 info = ['firstname_placeholder', cleanIt( jQuery('#mg_placeholder-name').val() ) ] ; cc_info_array.push(info);
		 info = ['lastname_label',      cleanIt( jQuery('#mg_lname-paypalpro').val() ) ] ; cc_info_array.push(info);
		 info = ['lastname_placeholder', cleanIt( jQuery('#mg_placeholder-lname').val() ) ] ; cc_info_array.push(info);
		 info = ['number_label',       cleanIt( jQuery('#mg_cardnumber-paypalpro').val() ) ] ; cc_info_array.push(info);
		 info = ['number_placeholder', cleanIt( jQuery('#mg_placeholder-card').val() ) ] ; cc_info_array.push(info);
		 info = ['cvc_label' ,        cleanIt( jQuery('#mg_cvc-paypalpro').val() ) ] ; cc_info_array.push(info);
		 info = ['cvc_placeholder' , cleanIt( jQuery('#mg_placeholder-CVC').val() ) ] ; cc_info_array.push(info);

		 jQuery.ajax({
			 type : "post",
			 url :  miglaAdminAjax.ajaxurl, 
			 data : {action: "miglaA_update_me", key:'migla_paypalpro_cc_info', value:cc_info_array },
			 success: function(msg) {  
				saved('#miglaUpdatePaypalSettings');
			 }
		 }); //ajax
      	
    });	

    jQuery('#miglaUpdatePaypalAccSettings').click(function() {

		jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_update_me", key:'migla_paypal_emails', value: jQuery('#miglaPaypalEmails').val() },
			success: function(msg) {                   
			}
		})  ; //ajax	
        
		var fec = 'no';
        if( jQuery('#miglaPaypalSendFEC').is(':checked') ){ fec = 'yes';  }

		  jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : { action: "miglaA_update_me", key:'migla_paypal_fec', value: fec },
			success: function(msg) {                         
			}
		  })  ; //ajax	
	
	var is_PDT = jQuery( "#migla_paypal_std_pdt_choice option:selected" ).val();
	//alert(is_PDT);
	
	if( is_PDT == 'pdt' )
	{
		jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_update_me", key:'migla_using_pdt', value: 'yes' },
			success: function(msg) {			  
			}
		})  ; //ajax	
		  
		jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_update_me", key:'migla_pdt_token', value: jQuery('#miglaPaypal_PDT_Token').val() },
			success: function(msg) {     
			}
		})  ; //ajax

         var isCA = 'no';
         if( jQuery('#migla_pdt_using_ca').is(':checked') ){ isCA = 'yes';  }
		  jQuery.ajax({
				type : "post",
				url  :  miglaAdminAjax.ajaxurl, 
				data : {action: "miglaA_update_me", key:'migla_pdt_using_ca', value: isCA },
				success: function(msg) {  		
				}
		  })  ; //ajax	
		  
	}else{
				
		jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_update_me", key:'migla_using_pdt', value: 'no' },
			success: function(msg) {			  
			}
		})  ; //ajax	
		
		var ipn = 'back';
        if( jQuery('#migla_ipn_choice').is(':checked') ){ ipn = 'front';  }

		  jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : { action: "miglaA_update_me", key:'migla_ipn_choice', value: ipn },
			success: function(msg) {                         
			}
		  })  ; //ajax	

		  
        var isChatBack = 'yes';
        if( jQuery('#migla_ipn_chatback').is(':checked') ){ isChatBack = 'no'; }
		
		jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_update_me", key:'migla_ipn_chatback', value: isChatBack },
			success: function(msg) {                         
			}
		  })  ; //ajax	

		  jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : { action : "miglaA_update_me", key:'migla_payment', 
   			         value  : jQuery('#mg_payment').val() },
			success: function(msg) { 

			}
		  })  ; //ajax			
		
	}	

    saved('#miglaUpdatePaypalAccSettings');	
      	
    });	


//// PAYPAL BUTTON CHOICE ////////////////////
jQuery('#miglaUploadPaypalBtn').click(function() {
	 formfield = jQuery('#mg_upload_image').attr('name');
	 tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
	 return false;
});
 
window.send_to_editor = function(html) {
	 imgurl = jQuery('img',html).attr('src');
	 jQuery('#mg_upload_image').val(imgurl);
	 tb_remove();
}

jQuery('#miglaSavePaypalBtnUrl').click(function(){
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'miglaPayPalButtonChoice', value:'imageUpload' },
        success: function(msg) {  
        }
   }); //ajax
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_paypalbuttonurl', value:jQuery('#mg_upload_image').val() },
        success: function(msg) {  
          saved('#miglaSavePaypalBtnUrl');
        }
   }); //ajax
});

jQuery('#miglaSavePayPalButtonPicker').click(function(){
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'miglaPayPalButtonChoice', value:'paypalButton' },
        success: function(msg) {  
        }
   }); //ajax
   var lang = jQuery("#miglaPayPalButtonPicker").val(); 
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_paypalbutton', value:lang },
        success: function(msg) {  
          saved('#miglaSavePayPalButtonPicker');
        }
   }); //ajax
});

jQuery('#miglaCSSButtonPickerSave').click(function(){
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'miglaPayPalButtonChoice', value:'cssButton' },
        success: function(msg) {  
        }
   }); //ajax
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_paypalcssbtnstyle', value:jQuery('#mg_CSSButtonPicker').val() },
        success: function(msg) {  
        }
   }); //ajax
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_paypalcssbtntext', value:jQuery('#mg_CSSButtonText').val() },
        success: function(msg) {  
        }
   }); //ajax
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_paypalcssbtnclass', value:jQuery('#mg_CSSButtonClass').val()},
        success: function(msg) {  
          saved('#miglaCSSButtonPickerSave');
        }
   }); //ajax
});

  jQuery('#migla_paypalpro_save').click(function(){

   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_paypalpro_username', value:jQuery('#mg_paypalpro_username').val()},
        success: function(msg) {     
        }
   }); //ajax

   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_paypalpro_password', value:jQuery('#mg_paypalpro_password').val()},
        success: function(msg) {     
        }
   }); //ajax

   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_paypal_pro_type', value:jQuery('#mg_paypal_pro_type').val()},
        success: function(msg) {     
        }
   }); //ajax  
   
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {   action : "miglaA_update_me", 
                   key    : 'migla_paypalpro_recurring', 
                   value  : jQuery('#mg_paypalpro_recurring').val()
               },
        success: function(msg) {     
        }
   }); //ajax    

   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {    action : "miglaA_update_me", 
                    key    : 'migla_express_checkout_listener', 
                    value  : jQuery('#miglaSetSECPage').val()
               },
        success: function(msg) {     
        }
   }); //ajax        

   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_paypalpro_signature', value:jQuery('#mg_paypalpro_signature').val()},
        success: function(msg) {
          saved('#migla_paypalpro_save');    
        }
   }); //ajax

  });

 jQuery('#migla_paypalflow_save').click(function(){

   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_paypalflow_vendor', value:jQuery('#mg_paypalflow_vendor').val()},
        success: function(msg) {     
        }
   }); //ajax

   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_paypalflow_user', value:jQuery('#mg_paypalflow_user').val()},
        success: function(msg) {     
        }
   }); //ajax

   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_paypalflow_password', value:jQuery('#mg_paypalflow_password').val()},
        success: function(msg) {     
        }
   }); //ajax   

   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_paypal_pro_type', value:jQuery('#mg_paypal_pro_type').val()},
        success: function(msg) {     
        }
   }); //ajax   

   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_paypalflow_partner', value:jQuery('#mg_paypalflow_partner').val()},
        success: function(msg) {
          saved('#migla_paypalflow_save');    
        }
   }); //ajax

  });  

 jQuery('#miglaSaveCCInfo').click(function(){

		  jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_update_me", key:'migla_paypal_wait_paypalpro', value: jQuery('#mg_waiting_paypalpro').val() },
			success: function(msg) {     
			}
		  })  ; //ajax	

		  jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_update_me", key:'migla_paypal_wait_paypal', value: jQuery('#mg_waiting_paypal').val() },
			success: function(msg) {     
			}
		  })  ; //ajax		

     var info = {}; var cc_info_array = [];
     info = ['tab_name',      cleanIt( jQuery('#mg_tab-paypalpro').val() ) ] ; cc_info_array.push(info);
     info = ['radio1_label',      cleanIt( jQuery('#mg_paypalpro-radio1').val() ) ] ; cc_info_array.push(info);
     info = ['radio2_label',      cleanIt( jQuery('#mg_paypalpro-radio2').val() ) ] ; cc_info_array.push(info);
     info = ['firstname_label',      cleanIt( jQuery('#mg_name-paypalpro').val() ) ] ; cc_info_array.push(info);
     info = ['firstname_placeholder', cleanIt( jQuery('#mg_placeholder-name').val() ) ] ; cc_info_array.push(info);
     info = ['lastname_label',      cleanIt( jQuery('#mg_lname-paypalpro').val() ) ] ; cc_info_array.push(info);
     info = ['lastname_placeholder', cleanIt( jQuery('#mg_placeholder-lname').val() ) ] ; cc_info_array.push(info);
     info = ['number_label',       cleanIt( jQuery('#mg_cardnumber-paypalpro').val() ) ] ; cc_info_array.push(info);
     info = ['number_placeholder', cleanIt( jQuery('#mg_placeholder-card').val() ) ] ; cc_info_array.push(info);
     info = ['cvc_label' ,        cleanIt( jQuery('#mg_cvc-paypalpro').val() ) ] ; cc_info_array.push(info);
     info = ['cvc_placeholder' , cleanIt( jQuery('#mg_placeholder-CVC').val() ) ] ; cc_info_array.push(info);

     jQuery.ajax({
         type : "post",
         url :  miglaAdminAjax.ajaxurl, 
         data : {action: "miglaA_update_me", key:'migla_paypalpro_cc_info', value:cc_info_array },
         success: function(msg) {  
            saved('#miglaSaveCCInfo');
         }
     }); //ajax
 });

  jQuery('#miglaUpdatePaypalInfo').click(function(){
       jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_update_me", key:'migla_paypalitem', value: jQuery('#miglaPaypalItem').val() },
			success: function(msg) {  
			}
	   })  ; //ajax	

	   jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_update_me", key:'migla_paymentcmd', value: jQuery("input[name='miglaPaypalcmd']:checked").val() },
			success: function(msg) { 
			   saved('#miglaUpdatePaypalInfo');
			}
	   })  ; //ajax		  
  });

  jQuery('#migla_ipn_choice').click(function(){
     if( jQuery(this).is(':checked') ){
        jQuery('#listener_front_url').show();
        jQuery('#listener_back_url').hide();
     }else{
        jQuery('#listener_front_url').hide();
        jQuery('#listener_back_url').show();
     }
  });

  jQuery('#mg_paypal_pro_type').click(function(){
      if(jQuery(this).val() == 'website_pro')
      {
         jQuery('#div_website_pro').show();
         jQuery('#div_paypal_flow').hide();

      }else{

         jQuery('#div_website_pro').hide();
         jQuery('#div_paypal_flow').show();

      }  
  
  });

  jQuery('#migla_security_save').click(function()
  {

        var isVerifiySSL = 'no';
        if( jQuery('#miglaPaypalVerifySSL').is(':checked') ){ isVerifiySSL = 'yes'; }
		
		jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_update_me", key:'migla_paypal_verifySSL', value: isVerifiySSL },
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
	   

		jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : { 	action	: 'miglaA_update_me', 
						key		: 'migla_avs_level', 
						value	: jQuery("input[name='migla_credit_card_AVS_levels']:checked").val() 
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


   jQuery('#migla_use_captcha').click(function(){
      if( jQuery(this).is(':checked') )
         jQuery('.mg_captcha_keys').show();
      else
         jQuery('.mg_captcha_keys').hide();
   });
 
}); //Document Ready

function cleanIt( dirty ){
  var _dirty = new String(dirty);
  var clean ;
  
  clean = _dirty.replace(/\//gi,"//");  
  clean = clean.replace(/"/gi,"[q]");
  clean = clean.replace(/'/gi,"[q]");
  return clean;
}

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