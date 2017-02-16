//jQuery = jQuery.noConflict();

function getRidForbidden(){
 jQuery('#migla_donation_form').find('.migla-panel').each(function(){
  jQuery(this).find('.form-group').each(function(){
     var type = jQuery(this).find("input").attr('type'); 
     var val = "";
          
     if( type == 'text' ){ //text
       val = jQuery(this).find("input").attr('placeholder');
       jQuery(this).find("input").attr('placeholder', val.replace("[q]", "'") );
     }
  });
 });
}

var tab_bgcolor; var tab_border;

jQuery(document).ready( function() {

   jQuery('#memorialgift').click(function(){
      if( jQuery(this).is(':checked') )
      {
         var hemail_id = jQuery('#migla_donation_form').find('#miglad_honoreeemail');
         var div1      = jQuery(hemail_id).closest('.form-group');
         div1.hide();
      }else{
         var hemail_id = jQuery('body').find('#miglad_honoreeemail');
         var div1      = jQuery(hemail_id).closest('.form-group');
         div1.show();
      }
   });

	if( jQuery('#miglaCustomAmount').length > 0 && jQuery('#miglaCustomAmount').val() > 0 )
	{  
       jQuery('#miglaCustomAmount').val('');
	}
	
    jQuery('.migla_positive_number_only').on('keyup', function (e){
		var mg_current_value 		=  jQuery(this).val();
		var mg_current_array_value 	= mg_current_value.split('');
		var mg_new_val				=	'';
		var mg_decimal				= jQuery('#miglaDecimalSep').val();
		var mg_thousand				= jQuery('#miglaThousandSep').val();
		var mg_count_decimal = 0 ; var mg_count_thousand = 0 ;
		
		for( var i = 0; i < mg_current_array_value.length ; i = i + 1 )
		{
			if( mg_current_array_value[i] == '0' || mg_current_array_value[i] == '1' || mg_current_array_value[i] == '2' ||
				mg_current_array_value[i] == '3' || mg_current_array_value[i] == '4' || mg_current_array_value[i] == '5' ||
				mg_current_array_value[i] == '6' || mg_current_array_value[i] == '7' || mg_current_array_value[i] == '8' ||  
				mg_current_array_value[i] == '9' 
			)
			{
				mg_new_val	= mg_new_val + mg_current_array_value[i];
				
			}else if( mg_current_array_value[i] == mg_decimal && jQuery('#miglaShowDecimal').val() == 'yes' )
			{
			   	mg_count_decimal = mg_count_decimal + 1;
				if( mg_count_decimal == 1 )
				{
					mg_new_val	= mg_new_val + mg_current_array_value[i];
				}
			}
		}
		
		 jQuery(this).val( mg_new_val );
    });


//This is for tab yeah
  tab_bgcolor = jQuery('.mg_tab-content').find('.mg_active').css('background-color');
  tab_border  = jQuery('.mg_tab-content').find('.mg_active').css('border');
  
  var tab_color_active; var tab_color_notactive ;
  jQuery('.mg_nav-tabs').find('li').each(function(){
      if( jQuery(this).hasClass('mg_active') ){
          tab_color_active = jQuery(this).find('a').css('background-color');
      }else{
          tab_color_notactive = jQuery(this).find('a').css('background-color');
      } 
  });

  jQuery('.mg_nav li').click(function(){

      var id = jQuery(this).find('a').attr('id');
      var $this = jQuery(this);

          jQuery('.mg_nav li').each(function(){
               jQuery(this).removeClass('mg_active');
          });

      jQuery('#'+id).closest('li').addClass('mg_active') ;

      if( id == '_sectionstripe' ){

          jQuery('.mg_tab-content').find('#sectionstripe').addClass('mg_active');
          jQuery('.mg_tab-content').find('#sectionstripe').css('background-color', tab_bgcolor);
          jQuery('.mg_tab-content').find('#sectionstripe').css('border', tab_border);

          jQuery('#_sectionstripe').css('background-color', tab_color_active );
          jQuery('#_sectionpaypal').css('background-color', tab_color_notactive );
          jQuery('#_sectionoffline').css('background-color', tab_color_notactive );
          jQuery('#_sectionauthorize').css('background-color', tab_color_notactive );

          jQuery('.mg_tab-content').find('#sectionpaypal').removeClass('mg_active');
          jQuery('.mg_tab-content').find('#sectionoffline').removeClass('mg_active');
          jQuery('.mg_tab-content').find('#sectionauthorize').removeClass('mg_active');

      }else if( id == '_sectionpaypal' ){

          jQuery('.mg_tab-content').find('#sectionpaypal').addClass('mg_active');
          jQuery('.mg_tab-content').find('#sectionpaypal').css('background-color', tab_bgcolor);
          jQuery('.mg_tab-content').find('#sectionpaypal').css('border', tab_border);

          jQuery('.mg_tab-content').find('#sectionstripe').removeClass('mg_active');
          jQuery('.mg_tab-content').find('#sectionoffline').removeClass('mg_active');
          jQuery('.mg_tab-content').find('#sectionauthorize').removeClass('mg_active');

          jQuery('#_sectionpaypal').css('background-color', tab_color_active );
          jQuery('#_sectionstripe').css('background-color', tab_color_notactive );
          jQuery('#_sectionoffline').css('background-color', tab_color_notactive );
          jQuery('#_sectionauthorize').css('background-color', tab_color_notactive  );

      }else if( id == '_sectionauthorize' ){

          jQuery('.mg_tab-content').find('#sectionstripe').removeClass('mg_active');
          jQuery('.mg_tab-content').find('#sectionoffline').removeClass('mg_active');
          jQuery('.mg_tab-content').find('#sectionpaypal').removeClass('mg_active');

          jQuery('.mg_tab-content').find('#sectionauthorize').addClass('mg_active');
          jQuery('.mg_tab-content').find('#sectionauthorize').css('background-color', tab_bgcolor);
          jQuery('.mg_tab-content').find('#sectionauthorize').css('border', tab_border);

          jQuery('#_sectionauthorize').css('background-color', tab_color_active );
          jQuery('#_sectionstripe').css('background-color', tab_color_notactive );
          jQuery('#_sectionoffline').css('background-color', tab_color_notactive );
          jQuery('#_sectionpaypal').css('background-color', tab_color_notactive );

      }else{

          jQuery('.mg_tab-content').find('#sectionoffline').addClass('mg_active');
          jQuery('.mg_tab-content').find('#sectionoffline').css('background-color', tab_bgcolor);
          jQuery('.mg_tab-content').find('#sectionoffline').css('border', tab_border);

          jQuery('#_sectionoffline').css('background-color', tab_color_active );
          jQuery('#_sectionpaypal').css('background-color', tab_color_notactive );
          jQuery('#_sectionstripe').css('background-color', tab_color_notactive );
          jQuery('#_sectionauthorize').css('background-color', tab_color_notactive );

          jQuery('.mg_tab-content').find('#sectionpaypal').removeClass('mg_active');
          jQuery('.mg_tab-content').find('#sectionstripe').removeClass('mg_active');
          jQuery('.mg_tab-content').find('#sectionauthorize').removeClass('mg_active');

      }
  });



getRidForbidden();

   jQuery('.miglaNAD2').on('keypress', function (e){ 
     var str = jQuery(this).val(); 
     var separator = jQuery('#miglaDecimalSep').val();
     var key = String.fromCharCode(e.which);

     // Allow: backspace, delete, escape, enter
     if (jQuery.inArray( e.which, [ 8, 0, 27, 13]) !== -1 ||
        jQuery.inArray( key, [ '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ]) !== -1 ||
        ( key == separator )
     )
     {
        if( key == separator  ){
          
           if(jQuery('#miglaShowDecimal').val()=='yes'){
             if( ( str.indexOf(separator) >= 0 ) ){
               e.preventDefault();
             }else{
               return;
             }
           }else{
              e.preventDefault();
           }
        }

     }else{
        e.preventDefault();
     }
  });

//someone click custom amount
jQuery('.migla_amount_choice').click(function(){
	if( jQuery(this).val() == 'custom' ){
		jQuery('#miglaCustomAmount').focus();
		jQuery('#miglaCustomAmount').val('');
	}
});

jQuery('#miglaCustomAmount').focus(function(){
	jQuery('#miglaCustomAmount').val('');
    jQuery('.migla_custom_amount').attr("checked", "checked");	
	
	if( jQuery('.migla_amount_lbl').length > 0 )
	{
		var thecustom= jQuery('.miglaCustomAmount').find('.migla_amount_lbl');
		thecustom.trigger('click');
	}
	
});

jQuery('#miglaCustomAmount').click(function(){
    jQuery('.migla_custom_amount').attr("checked", "checked");
	jQuery('#miglaCustomAmount').val('');
});

//honoree


//DONOR
	  if( jQuery('select[name=miglad_country] option:selected').text() == 'United States' ){
	    jQuery('#state').show();
	    jQuery('#province').hide();
	  }else{
	    if( jQuery('#state').is(':visible') ) 
		{ 
	      jQuery('#state').hide();	  
		}
	  }
	  
          if( jQuery('select[name=miglad_country] option:selected').text() == 'Canada' ){
	    jQuery('#state').hide();
		jQuery('#province').show();
	  }else{
	    if( jQuery('#province').is(':visible') ) 
		{ 
	      jQuery('#province').hide();	  
		}
	  }

	jQuery('#country').change(function (e){
	  if( jQuery('select[name=miglad_country] option:selected').text() == 'United States' ){
	    jQuery('#state').show();
	    jQuery('#province').hide();
	  }else{
	    if( jQuery('#state').is(':visible') ) 
		{ 
	      jQuery('#state').hide();	  
		}
	  }
	  
          if( jQuery('select[name=miglad_country] option:selected').text() == 'Canada' ){
	    jQuery('#state').hide();
		jQuery('#province').show();
	  }else{
	    if( jQuery('#province').is(':visible') ) 
		{ 
	      jQuery('#province').hide();	  
		}
	  }
	 });	
	 
//HONOREE
	  if( jQuery('select[name=miglad_honoreecountry] option:selected').text() == 'United States' ){
	    jQuery('#honoreestate').show();
	    jQuery('#honoreeprovince').hide();
	  }else{
	    if( jQuery('#honoreestate').is(':visible') ) 
		{ 
	      jQuery('#honoreestate').hide();	  
		}
	  }
	  
          if( jQuery('select[name=miglad_honoreecountry] option:selected').text() == 'Canada' ){
	    jQuery('#honoreestate').hide();
		jQuery('#honoreeprovince').show();
	  }else{
	    if( jQuery('#honoreeprovince').is(':visible') ) 
		{ 
	      jQuery('#honoreeprovince').hide();	  
		}
	  }

	jQuery('#honoreecountry').change(function (e){
	  if( jQuery('select[name=miglad_honoreecountry] option:selected').text() == 'United States' ){
	    jQuery('#honoreestate').show();
	    jQuery('#honoreeprovince').hide();
	  }else{
	    if( jQuery('#honoreestate').is(':visible') ) 
		{ 
	      jQuery('#honoreestate').hide();	  
		}
	  }
	  
          if( jQuery('select[name=miglad_honoreecountry] option:selected').text() == 'Canada' ){
	    jQuery('#honoreestate').hide();
		jQuery('#honoreeprovince').show();
	  }else{
	    if( jQuery('#honoreeprovince').is(':visible') ) 
		{ 
	      jQuery('#honoreeprovince').hide();	  
		}
	  }
	 });		  
	  
//Campaign
jQuery('#miglaform_campaign').change(function(){
    var c = jQuery('select[name=campaign] option:selected').text();
    jQuery('#migla_bar').empty();

    //if( c == 'Undesignated' ){ }else{
    //alert(c);
    var temp = c.replace("'", "[q]");
    
    jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_draw_progress_bar", cname:temp, posttype:""},
        success: function(msg) {
             //alert(msg);    
            jQuery(msg).appendTo( jQuery('#migla_bar'));
        }
       }); //ajax 
    //} //else
 });

    var c = jQuery('select[name=campaign] option:selected').text();
    jQuery('#migla_bar').empty();
    if( c == 'Undesignated' ){ }else{
    //alert(c);
    var temp = c.replace("'", "[q]");
    
    jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_draw_progress_bar", cname:temp, posttype:""},
        success: function(msg) {
             //alert(msg);    
            jQuery(msg).appendTo( jQuery('#migla_bar'));
        }
       }); //ajax 
    }

//Toggle
jQuery('.mtoggle').each(function(){
  jQuery(this).prop("checked", false);
});



jQuery('.mtoggle').click(function(){
   var p = jQuery(this).closest('.migla-panel');
   p.find('.migla-panel-body').toggle();
});


  jQuery("#sectionpaypal").find("input[name='miglaPaypalMethod']").click(function(){
     if( jQuery(this).val() == 'paypal_pro' )
     {
        jQuery('#mg-paypalpro-payment-form').slideDown();


     }else  if( jQuery(this).val() == 'paypal_standard' )
     {
        jQuery('#mg-paypalpro-payment-form').slideUp();

     }
  });

  jQuery('.migla_amount_lbl').click(function(){
		
		jQuery('.radio-inline').each(function(){
			jQuery(this).removeClass('selected');
		});

		jQuery('.migla_amount_choice').each(function(){
			jQuery(this).removeClass('mg_amount_checked');
		});

		var parent = jQuery(this).closest('.radio-inline');
		parent.addClass('selected');

		jQuery(this).find('.migla_amount_choice').addClass('mg_amount_checked');

  });

})