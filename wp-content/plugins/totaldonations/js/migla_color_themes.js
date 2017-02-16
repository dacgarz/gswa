function get_tinymce_content(){
    if (jQuery("#wp-migla_editor_html1-wrap").hasClass("tmce-active")){
        return tinyMCE.activeEditor.getContent();
    }else if (jQuery("#wp-migla_editor_html1-wrap").hasClass("html-active")){
        return jQuery('#migla_editor_html1').val();
    }
}

function convertHex(hexa,opacity){
    var hexa = hexa.replace('#','');
    r = parseInt(hexa.substring(0,2), 16);
    g = parseInt(hexa.substring(2,4), 16);
    b = parseInt(hexa.substring(4,6), 16);

    result = 'rgba('+r+','+g+','+b+','+opacity/100+')';
    return result;
}

function changeBoxShadow(hshadow, vshadow, blur, spread, hex, opacity)
{
  var style = "";
  
  style = style + hshadow +"px "+ vshadow +"px "+ blur +"px "+ spread +"px " ;
  style = style + hex + " inset" ;
  
  return style;
}

function save_circle_settings()
{
    var circle_arr = {};

    circle_arr.size        = Number( jQuery('#migla_circle_size').val() );
    circle_arr.start_angle = Number( jQuery('#migla_circle_start_angle').val() );
    circle_arr.thickness   = Number( jQuery('#migla_circle_thickness').val() );
	circle_arr.inner_font_size   = Number( jQuery('#migla_circle_inner_font_size').val() );

    if( circle_arr.size  > 300 ){ circle_arr.size  = 300; }
    if( circle_arr.size  < 1 ){ circle_arr.size  = 1; }

    if( circle_arr.thickness  > 300 ){ circle_arr.thickness  = 300; }
    if( circle_arr.thickness  < 1 ){ circle_arr.thickness  = 1; }

    if( jQuery('#migla_circle_reverse').is(':checked') ){
      circle_arr.reverse = 'yes';
    }else{
      circle_arr.reverse = 'no';
    }

    circle_arr.line_cap = jQuery('#migla_circle_line_cap').val();
    circle_arr.fill     = jQuery('#migla_circle_fill').val() ;

    circle_arr.animation = jQuery('#migla_circle_animation').val();
    circle_arr.inside = jQuery('#migla_circle_inside').val();

   // alert( JSON.stringify(circle_arr) );
    var carr = [];
    carr.push(circle_arr);

   jQuery.ajax({
		type 	: "post",
		url 	: miglaAdminAjax.ajaxurl, 
		data 	: {	action	: "miglaA_update_me", 
					key		: 'migla_circle_settings', 
					value:carr  
					},
		success: function(msg) {
					saved('#migla_save_circle_settings');
				}
    })  ; //ajax	

}


jQuery(document).ready(function() {

 var effect = [ "yes", "yes", "yes", "yes"];

 jQuery('.migla_circle_settings').hide();

//Changing the spinner

	jQuery('#migla_widthpanelborderspinner-up').click(function(){
	  var num = Number( jQuery('#migla_widthpanelborder').val() );	
           if(  num < 10 ){    
		 num = num + 1;
		 jQuery('#migla_widthpanelborder').val(num);
		 jQuery('#migla_widthpanelborder').trigger('change');
	  }
	});

	jQuery('#migla_widthpanelborderspinner-down').click(function(){
	  var num = Number( jQuery('#migla_widthpanelborder').val() );
	  if(  num > 1 ){
		 num = num - 1;
		  jQuery('#migla_widthpanelborder').val(num);
		  jQuery('#migla_widthpanelborder').trigger('change');
	  }
	});

	jQuery('#migla_Widthborderlevelcolorspinner-up').click(function(){
	  var num = Number( jQuery('#migla_Widthborderlevelcolor').val() );	
           if(  num < 10 ){    
		num = num + 1;
		jQuery('#migla_Widthborderlevelcolor').val(num);
		jQuery('#migla_Widthborderlevelcolor').trigger('change');
	   }
	});

	jQuery('#migla_Widthborderlevelcolorspinner-down').click(function(){
	  var num = Number( jQuery('#migla_Widthborderlevelcolor').val() );
	  if(  num > 1 ){
		num = num - 1;
		jQuery('#migla_Widthborderlevelcolor').val(num);
		jQuery('#migla_Widthborderlevelcolor').trigger('change');
	  }
	});
	
	jQuery('#mg_WBRtop-leftspinner-up').click(function(){
	  var num = Number( jQuery('#mg_WBRtop-left').val() );	
           if(  num < 10 ){    
		num = num + 1;
		jQuery('#mg_WBRtop-left').val(num);
		jQuery('#mg_WBRtop-left').trigger('change');
	   }
	});

	jQuery('#mg_WBRtop-leftspinner-down').click(function(){
	  var num = Number( jQuery('#mg_WBRtop-left').val() );
	  if(  num > 1 ){
		num = num - 1;
		jQuery('#mg_WBRtop-left').val(num);
		jQuery('#mg_WBRtop-left').trigger('change');
	  }
	});

	jQuery('#migla_WRBtoprightspinner-up').click(function(){
	  var num = Number( jQuery('#migla_WRBtopright').val() );
           if(  num < 10 ){  	  
		num = num + 1;
		jQuery('#migla_WRBtopright').val(num);
		jQuery('#migla_WRBtopright').trigger('change');
	   }
	});

	jQuery('#migla_WRBtoprightspinner-down').click(function(){
	  var num = Number( jQuery('#migla_WRBtopright').val() );
	  if(  num > 1 ){
		num = num - 1;
		jQuery('#migla_WRBtopright').val(num);
		jQuery('#migla_WRBtopright').trigger('change');
	  }
	});
	
	jQuery('#migla_radiusbottomleftspinner-up').click(function(){
	  var num = Number( jQuery('#migla_radiusbottomleft').val() );	  
           if(  num < 10 ){  
		num = num + 1;
		jQuery('#migla_radiusbottomleft').val(num);
		jQuery('#migla_radiusbottomleft').trigger('change');
	   }
	});

	jQuery('#migla_radiusbottomleftspinner-down').click(function(){
	  var num = Number( jQuery('#migla_radiusbottomleft').val() );
	  if(  num > 1 ){
		num = num - 1;
		jQuery('#migla_radiusbottomleft').val(num);
		jQuery('#migla_radiusbottomleft').trigger('change');
	  }
	});	
	
	jQuery('#migla_radiusbottomrightspinner-up').click(function(){
	  var num = Number( jQuery('#migla_radiusbottomright').val() );	
           if(  num < 10 ){    
		num = num + 1;
		jQuery('#migla_radiusbottomright').val(num);
		jQuery('#migla_radiusbottomright').trigger('change');
	   }
	});

	jQuery('#migla_radiusbottomrightspinner-down').click(function(){
	  var num = Number( jQuery('#migla_radiusbottomright').val() );
	  if(  num > 1 ){
		num = num - 1;
		jQuery('#migla_radiusbottomright').val(num);
		jQuery('#migla_radiusbottomright').trigger('change');
	  }
	});	

	jQuery('#migla_hshadowspinner-up').click(function(){
	  var num = Number( jQuery('#migla_hshadow').val() );
           if(  num < 10 ){  	  
		num = num + 1;
		jQuery('#migla_hshadow').val(num);
		jQuery('#migla_hshadow').trigger('change');
	   }
	});

	jQuery('#migla_hshadowspinner-down').click(function(){
	  var num = Number( jQuery('#migla_hshadow').val() );
	  if(  num > 1 ){
		num = num - 1;
		jQuery('#migla_hshadow').val(num);
		jQuery('#migla_hshadow').trigger('change');
	  }
	});	
	
	jQuery('#migla_vshadow-spinner-up').click(function(){
	  var num = Number( jQuery('#migla_vshadow').val() );	
          if(  num < 10 ){  
		num = num + 1;
		jQuery('#migla_vshadow').val(num);
		jQuery('#migla_vshadow').trigger('change');
	  }
	});

	jQuery('#migla_vshadow-spinner-down').click(function(){
	  var num = Number( jQuery('#migla_vshadow').val() );
	  if(  num > 1 ){
		num = num - 1;
		jQuery('#migla_vshadow').val(num);
		jQuery('#migla_vshadow').trigger('change');
	  }
	});		
	
	jQuery('#migla_blurspinner-up').click(function(){
	  var num = Number( jQuery('#migla_blur').val() );	
           if(  num < 10 ){    
		num = num + 1;
		jQuery('#migla_blur').val(num);
		jQuery('#migla_blur').trigger('change');
	   }
	});

	jQuery('#migla_blurspinner-down').click(function(){
	  var num = Number( jQuery('#migla_blur').val() );
	  if(  num > 1 ){
		num = num - 1;
		jQuery('#migla_blur').val(num);
		jQuery('#migla_blur').trigger('change');
	  }
	});	
	
	jQuery('#migla_spreadspinner-up').click(function(){
	  var num = Number( jQuery('#migla_spread').val() );	 
           if(  num < 10 ){   
		num = num + 1;
		jQuery('#migla_spread').val(num);
		jQuery('#migla_spread').trigger('change');
	   }
	});

	jQuery('#migla_spreadspinner-down').click(function(){
	  var num = Number( jQuery('#migla_spread').val() );
	  if(  num > 1 ){
		num = num - 1;
		jQuery('#migla_spread').val(num);
		jQuery('#migla_spread').trigger('change');
	  }
	});	

	jQuery('#migla_inner_font_size_spinner_up').click(function(){
	  var num = Number( jQuery('#migla_circle_inner_font_size').val() );	 
           if(  num < 40 ){   
		num = num + 1;
		jQuery('#migla_circle_inner_font_size').val(num);
		jQuery('#migla_circle_inner_font_size').trigger('change');
	   }
	});

	jQuery('#migla_inner_font_size_spinner_down').click(function(){
	  var num = Number( jQuery('#migla_circle_inner_font_size').val() );
	  if(  num > 9 ){
		num = num - 1;
		jQuery('#migla_circle_inner_font_size').val(num);
		jQuery('#migla_circle_inner_font_size').trigger('change');
	  }
	});		
	
	
jQuery('#mg_WBRtop-left').change(function(){
   jQuery('#me').css( '-webkit-border-top-left-radius' , jQuery(this).val()+"px" );
   jQuery('#me').css( '-moz-border-radius-topleft' , jQuery(this).val()+"px" );
   jQuery('#me').css( 'border-top-left-radius' , jQuery(this).val()+"px" );
});

jQuery('#migla_WRBtopright').change(function(){
   jQuery('#me').css( '-webkit-border-top-right-radius' , jQuery(this).val()+"px" );
   jQuery('#me').css( '-moz-border-radius-topright' , jQuery(this).val()+"px" );
   jQuery('#me').css( 'border-top-right-radius' , jQuery(this).val()+"px" );
});

jQuery('#migla_radiusbottomleft').change(function(){
   jQuery('#me').css( '-webkit-border-bottom-left-radius' , jQuery(this).val()+"px" );
   jQuery('#me').css( '-moz-border-radius-bottomleft' , jQuery(this).val()+"px" );
   jQuery('#me').css( 'border-bottom-left-radius' , jQuery(this).val()+"px" );
});

jQuery('#migla_radiusbottomright').change(function(){
   jQuery('#me').css( '-webkit-border-bottom-right-radius' , jQuery(this).val()+"px" );
   jQuery('#me').css( '-moz-border-radius-bottomright' , jQuery(this).val()+"px" );
   jQuery('#me').css( 'border-bottom-right-radius' , jQuery(this).val()+"px" );
});


jQuery('#migla_barcolor').change(function(){
   var parent = jQuery(this).closest('div.row');
   jQuery(parent).find('#currentColor').css('background-color', jQuery(this).val());	 
   jQuery('#div2previewbar').css( 'background-color' , jQuery(this).val() );
});

jQuery('#migla_wellcolor').change(function(){
   var parent = jQuery(this).closest('div.row');
   jQuery(parent).find('#currentColor').css('background-color', jQuery(this).val());	 
   jQuery('#me').css( 'background-color' , jQuery(this).val() );
});

jQuery('#migla_wellshadow').change(function(){
   var parent = jQuery(this).closest('div.row');
   jQuery(parent).find('#currentColor').css('background-color', jQuery(this).val());

   var newStyle = changeBoxShadow( jQuery('#migla_hshadow').val(), jQuery('#migla_vshadow').val(), 
               jQuery('#migla_blur').val(),  jQuery('#migla_spread').val(), jQuery(this).val(), 100);

   jQuery('#me').css( 'box-shadow' , newStyle );	
 
});

jQuery('#migla_hshadow').change(function(){
  var newStyle = changeBoxShadow( jQuery(this).val(), jQuery('#migla_vshadow').val(), jQuery('#migla_blur').val(), 
                jQuery('#migla_spread').val(), jQuery('#migla_wellshadow').val(), 100);
  jQuery('#me').css( 'box-shadow' , newStyle );
});

jQuery('#migla_vshadow').change(function(){
  var newStyle = changeBoxShadow( jQuery('#migla_hshadow').val(), jQuery(this).val(), jQuery('#migla_blur').val(), 
                jQuery('#migla_spread').val(), jQuery('#migla_wellshadow').val(), 100);
  jQuery('#me').css( 'box-shadow' , newStyle );
});

jQuery('#migla_blur').change(function(){
  var newStyle = changeBoxShadow( jQuery('#migla_hshadow').val(), jQuery('#migla_vshadow').val(), jQuery(this).val(), 
                jQuery('#migla_spread').val(), jQuery('#migla_wellshadow').val(), 100);
  jQuery('#me').css( 'box-shadow' , newStyle );
});

jQuery('#migla_spread').change(function(){
  var newStyle = changeBoxShadow( jQuery('#migla_hshadow').val(), jQuery('#migla_vshadow').val(), jQuery('#migla_blur').val(), 
                jQuery(this).val(), jQuery('#migla_wellshadow').val(), 100);
  jQuery('#me').css( 'box-shadow' , newStyle );
});

	jQuery('.mg-color-field').change(function(){
		var parent 		= jQuery(this).closest('div.row');
		jQuery(parent).find('.currentColor').css('background-color', jQuery(this).val() );
	});
	
	
////////////////////////////////////////////////////////////////////	
jQuery('#migla_save_form').click(function(){

    var ColorCode1 = jQuery('#migla_backgroundcolor').val() + ',1';
    jQuery.ajax({
           type : "post",
           url :miglaAdminAjax.ajaxurl,
           data : {	action 	: 'miglaA_update_me', 
					key		: 'migla_2ndbgcolor', 
					value	: ColorCode1
				  },
           success: function(msg) { 
          }
      })  ; //ajax	 	


    var ColorCode2 = jQuery('#migla_panelborder').val() + ",1," + jQuery('#migla_widthpanelborder').val();
    jQuery.ajax({
           type : "post",
           url :miglaAdminAjax.ajaxurl,
           data : {	action	: "miglaA_update_me", 
					key		: 'migla_2ndbgcolorb', 
					value	: ColorCode2
				},
           success: function(msg) { 
          }
      })  ; //ajax	 	

	  
    var ColorCode3 = jQuery('#migla_bglevelcolor').val() ;
	jQuery.ajax({
		type	: "post",
		url 	: miglaAdminAjax.ajaxurl, 
		data 	: { action 	: "miglaA_update_me", 
					key		: 'migla_bglevelcolor', 
					value	: ColorCode3 
				},
		success: function(msg) {
		}
    })  ; //ajax	

	
		var ColorCode4 	= jQuery('#migla_borderlevelcolor').val() ;
		var spinner 	= jQuery('#migla_Widthborderlevelcolor').val();

	   jQuery.ajax({
			type : "post",
			url : miglaAdminAjax.ajaxurl, 
			data : {	action 	: "miglaA_update_me", 
						key		: 'migla_borderlevelcolor', 
						value	: ColorCode4 
					},
			success: function(msg) {
			}
		})  ; //ajax

	   jQuery.ajax({
			type : "post",
			url : miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_update_me", key:'migla_borderlevel', value:spinner  },
			success: function(msg) {
			}
		})  ; //ajax	

    var ColorCode5 = jQuery('#migla_bglevelcoloractive').val() ;
    jQuery.ajax({
           type : "post",
           url :miglaAdminAjax.ajaxurl,
           data : {	action 	: 'miglaA_update_me', 
					key		: 'migla_bglevelcoloractive', 
					value	: ColorCode5
				  },
           success: function(msg) { 
          }
      })  ; //ajax	 	


    var ColorCode6 = jQuery('#migla_tabcolor').val() ;
    jQuery.ajax({
           type : "post",
           url :miglaAdminAjax.ajaxurl,
           data : {	action 	: 'miglaA_update_me', 
					key		: 'migla_tabcolor', 
					value	: ColorCode6
				  },
           success: function(msg) { 
          }
      })  ; //ajax	 	

	saved('#migla_save_form');
});
//////////////////////////////////////////////

jQuery('#migla_save_bar').click(function(){

    var border = "";
    border = border + jQuery('#mg_WBRtop-left').val() + ",";
    border = border + jQuery('#migla_WRBtopright').val() + ",";
    border = border + jQuery('#migla_radiusbottomleft').val() + ",";
    border = border + jQuery('#migla_radiusbottomright').val();

    jQuery.ajax({
           type : "post",
           url :miglaAdminAjax.ajaxurl,
           data : { action 	: "miglaA_update_me", 
					key		: 'migla_borderRadius', 
					value	: border
				},
           success: function(msg) {  
          }
      })  ; //ajax	

    var well = "";
    well = jQuery('#migla_wellshadow').val() + ",1,";
    well = well + jQuery('#migla_hshadow').val() + ",";
    well = well + jQuery('#migla_vshadow').val() + ",";
    well = well + jQuery('#migla_blur').val() + ",";
    well = well + jQuery('#migla_spread').val();

    jQuery.ajax({
           type : "post",
           url 	: miglaAdminAjax.ajaxurl,
           data : {	action	: "miglaA_update_me", 
					key		: 'migla_wellboxshadow', 
					value	:	well
					},
           success	: function(msg) {  
          }
      })  ; //ajax

	   jQuery.ajax({
           type : "post",
           url :miglaAdminAjax.ajaxurl,
           data : {action: "miglaA_update_barinfo", key:'migla_progbar_info', value:jQuery('#migla_progressbar_text').val()},
           success: function(msg) { 
					}
          })  ; //ajax	 	
	
	
		var ColorCode = jQuery('#migla_barcolor').val() + ',1';
		jQuery.ajax({
           type : "post",
           url 	: miglaAdminAjax.ajaxurl,
           data : {	action 	: 'miglaA_update_me', 
					key		: 'migla_bar_color', 
					value	: ColorCode
				  },
           success: function(msg) { 
				}
      })  ; //ajax	 	
	
	
		var ColorCode2 = jQuery('#migla_wellcolor').val() + ',1';
		jQuery.ajax({
           type : "post",
           url 	: miglaAdminAjax.ajaxurl,
           data : {	action 	: 'miglaA_update_me', 
					key		: 'migla_progressbar_background', 
					value	: ColorCode2
				  },
           success: function(msg) { 
          }
      })  ; //ajax	 	

          
          var s = "no";var ps = "no"; var as = "no"; var pc = "no";

          if( jQuery("#inlineCheckbox1").is(":checked") ){
             s = "yes";
          }
          if( jQuery("#inlineCheckbox2").is(":checked") ){
             ps = "yes";
          }
          if( jQuery("#inlineCheckbox3").is(":checked") ){
             as = "yes";
          }
          if( jQuery("#inlineCheckbox4").is(":checked") ){
             pc = "yes";
          }

	jQuery.ajax({
           type : "post",
           url  : miglaAdminAjax.ajaxurl,
           data : {action: "miglaA_update_us", Stripes:s, Pulse:ps, AnimatedStripes:as, Percentage:pc},
            success: function(msg) { 
					}
    })  ; //ajax	

	  
	saved('#migla_save_bar');
	
});

	jQuery('.meffects').click(function(){
          var id = jQuery(this).attr('id');
          if( id == "inlineCheckbox1" ){
            jQuery('div.progress').toggleClass("striped");
          }
          if( id == "inlineCheckbox2" ){
            jQuery('div.progress').toggleClass("mg_pulse");
          }
          if( id == "inlineCheckbox3" ){
            jQuery('div.progress').toggleClass("animated-striped");
            jQuery('div.progress').toggleClass("active");
          }
          if( id == "inlineCheckbox4" ){
            jQuery('div.progress').toggleClass("mg_percentage");
          }
	});

   jQuery('.mg-color-field').each(function(){
   var rgb = ""; alpha = "";
   var row = jQuery(this).closest('div.row');

                jQuery(this).minicolors({
                    control: jQuery(this).attr('data-control') || 'hue',
                    defaultValue: jQuery(this).attr('data-defaultValue') || '',
                    inline: jQuery(this).attr('data-inline') === 'true',
                    letterCase: jQuery(this).attr('data-letterCase') || 'lowercase',
                    opacity: jQuery(this).attr('data-opacity'),
                    position: jQuery(this).attr('data-position') || 'bottom left',
                    change: function(hex, opacity) {
                        if( !hex ) return;
                        if( opacity ) { 
                          hex += ',' + opacity; 
                          row.find('.rgba_value').val(hex);
                        }
                        if( typeof console === 'object' ) {
                            console.log(hex);
                        }
                    },
                    theme: 'bootstrap'
                });

});

////////////RESTORE/////////////////////////////////
jQuery('#miglaRestore').click(function(){
	 jQuery.ajax({
		type : "post",
		url : miglaAdminAjax.ajaxurl, 
		data : {action: "miglaA_reset_theme"},
		success: function(msg) {
						//alert( msg );  
						location.reload(true);
				}
	 })  ; //ajax	   
});


	//CIRCLE
	 jQuery('#migla_circle_fill').change(function(){
		var parent = jQuery(this).closest('div.row');
		jQuery(parent).find('#currentColor').css('background-color', jQuery(this).val());	 
	 });

	jQuery('#migla_circle_size_spinner_up').click(function(){
	  var parent = jQuery(this).closest('.input-group');
	  var num = Number(parent.find('.spinner-input').val());
	  if(  num < 300 ){
		 num = num + 1;
		 parent.find('.spinner-input').val(num);
		 parent.find('.spinner-input').trigger('change');
	  }
	});

	jQuery('#migla_circle_size_spinner_down').click(function(){
	  var parent = jQuery(this).closest('.input-group');
	  var num = Number(parent.find('.spinner-input').val());
	  if(  num > 1 ){
		 num = num - 1;
		 parent.find('.spinner-input').val(num);
		 parent.find('.spinner-input').trigger('change');
	  }
	});


	jQuery('#migla_circle_angle_spinner_up').click(function(){
	  var parent = jQuery(this).closest('.input-group');
	  var num = Number(parent.find('.spinner-input').val());
	  if(  num < 180 ){
		 num = num + 1;
		 parent.find('.spinner-input').val(num);
		 parent.find('.spinner-input').trigger('change');
	  }
	});

	jQuery('#migla_circle_angle_spinner_down').click(function(){
	  var parent = jQuery(this).closest('.input-group');
	  var num = Number(parent.find('.spinner-input').val());
	  if(  num > 0 ){
		 num = num - 1;
		 parent.find('.spinner-input').val(num);
		 parent.find('.spinner-input').trigger('change');
	  }
	});

	jQuery('#migla_circle_thickness_spinner_up').click(function(){
	  var parent = jQuery(this).closest('.input-group');
	  var num = Number(parent.find('.spinner-input').val());
	  if(  num < 50 ){
		 num = num + 1;
		 parent.find('.spinner-input').val(num);
		 parent.find('.spinner-input').trigger('change');
	  }
	});

	jQuery('#migla_circle_thickness_spinner_down').click(function(){
	  var parent = jQuery(this).closest('.input-group');
	  var num = Number(parent.find('.spinner-input').val());
	  if(  num > 1 ){
		 num = num - 1;
		 parent.find('.spinner-input').val(num);
		 parent.find('.spinner-input').trigger('change');
	  }
	});

	jQuery('#migla_save_circle_settings').click(function(){
		save_circle_settings();
	})

	jQuery('#miglaSaveCircleLayout').click(function(){
	
	var textalign = jQuery('input[name=mg_circle-text-align]:checked').val() ;	
	
	   jQuery.ajax({
			type : "post",
			url : miglaAdminAjax.ajaxurl, 
			data : { 	action	: "miglaA_update_me", 
						key		: 'migla_circle_textalign', 
						value 	: textalign  
					},
			success: function(msg) {
			}
		})  ; //ajax	
	

	   jQuery.ajax({
			type : "post",
			url : miglaAdminAjax.ajaxurl, 
			data : { action: "miglaA_update_me", key:'migla_circle_text1', value : jQuery('#migla_circle_text1').val() },
			success: function(msg) {
			}
		})  ; //ajax	

	   jQuery.ajax({
			type : "post",
			url : miglaAdminAjax.ajaxurl, 
			data : { action: "miglaA_update_me", key:'migla_circle_text2', value : jQuery('#migla_circle_text2').val()  },
			success: function(msg) {
			}
		})  ; //ajax	

	   jQuery.ajax({
			type : "post",
			url : miglaAdminAjax.ajaxurl, 
			data : { action: "miglaA_update_me", key:'migla_circle_text3', value : jQuery('#migla_circle_text3').val() },
			success: function(msg) {
				saved('#miglaSaveCircleLayout');
			}
		})  ; //ajax	
		
	});

    jQuery('input[name=mg_circle-text-align]').click(function(){
          if( jQuery(this).val() == 'mg_no_text' )
          {
                jQuery('#mg_text_barometer_input').hide();
          }else{
                jQuery('#mg_text_barometer_input').show();
          }
    });

}); //End of document