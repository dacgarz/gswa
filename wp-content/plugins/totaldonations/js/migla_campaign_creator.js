var changed_fields = [];
var del;
var updatedList = [];

jQuery(document).ready(function() {

  remove();

  var effect = [ "yes", "yes", "yes", "yes"];

  jQuery('.migla_circle_settings').hide();

jQuery('.spinner-up').click(function(){
  var parent = jQuery(this).closest('.input-group');
  var num = Number(parent.find('.spinner-input').val());
  if(  num < 10 ){
     num = num + 1;
     parent.find('.spinner-input').val(num);
     parent.find('.spinner-input').trigger('change');
  }
});

jQuery('.spinner-up2').click(function(){
  var parent = jQuery(this).closest('.input-group');
  var num = Number(parent.find('.spinner-input').val());
  if(  num < 10 ){
     num = num + 1;
     parent.find('.spinner-input').val(num);parent.find('.spinner-input').trigger('change');
  }
});


jQuery('.spinner-down').click(function(){
  var parent = jQuery(this).closest('.input-group');
  var num = Number(parent.find('.spinner-input').val());
  if(  num > 0 ){
     num = num - 1;
     parent.find('.spinner-input').val(num);parent.find('.spinner-input').trigger('change');
  }
});

//Changing the spinner
jQuery('#migla_radiustopleft').change(function(){
   jQuery('#me').css( '-webkit-border-top-left-radius' , jQuery(this).val()+"px" );
   jQuery('#me').css( '-moz-border-radius-topleft' , jQuery(this).val()+"px" );
   jQuery('#me').css( 'border-top-left-radius' , jQuery(this).val()+"px" );
});

jQuery('#migla_radiustopright').change(function(){
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

//Changing the color
jQuery('#migla_backgroundcolor').change(function(){
   var parent = jQuery(this).closest('div.row');
   jQuery(parent).find('#currentColor').css('background-color', jQuery(this).val());	 
});

jQuery('#migla_panelborder').change(function(){
   var parent = jQuery(this).closest('div.row');
   jQuery(parent).find('#currentColor').css('background-color', jQuery(this).val());	 
});

jQuery('#migla_bglevelcolor').change(function(){
   var parent = jQuery(this).closest('div.row');
   jQuery(parent).find('#currentColor').css('background-color', jQuery(this).val());	 
});

jQuery('#migla_borderlevelcolor').change(function(){
   var parent = jQuery(this).closest('div.row');
   jQuery(parent).find('#currentColor').css('background-color', jQuery(this).val());	 
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

//Save changes
jQuery('.msave').click(function() {
    	        var id = '#' + jQuery(this).attr('id');
		var parent = jQuery(this).closest('div.row');
		var ColorCode = jQuery(parent).find('.rgba_value').val();
		//alert(ColorCode);

	   jQuery.ajax({
           type : "post",
           url :miglaAdminAjax.ajaxurl,
           data : {action: "miglaA_update_me", key:jQuery(this).attr('name'), value:ColorCode},
           success: function(msg) { 
             jQuery(parent).find('#currentColor').css('background-color', jQuery(parent).find('.mg-color-field').val());	 
             saved( id );
          }
      })  ; //ajax	 	
	
	});

jQuery('#migla_2ndbgcolorb').click(function(){
    var parent = jQuery(this).closest('div.row');
    var ColorCode = parent.find('.rgba_value').val() + "," + parent.find('.spinner-input').val();

    jQuery.ajax({
           type : "post",
           url :miglaAdminAjax.ajaxurl,
           data : {action: "miglaA_update_me", key:jQuery(this).attr('name'), value:ColorCode},
           success: function(msg) { 
             jQuery(parent).find('#currentColor').css('background-color', jQuery(parent).find('.mg-color-field').val());		 
             saved( '#migla_2ndbgcolorb' );
          }
      })  ; //ajax	 	

});

jQuery('#migla_borderRadius').click(function(){
    var parent = jQuery(this).closest('div.row');
    var border = "";
    border = border + parent.find('input[name=topleft]').val() + ",";
    border = border + parent.find('input[name=topright]').val() + ",";
    border = border + parent.find('input[name=bottomleft]').val() + ",";
    border = border + parent.find('input[name=bottomright]').val();

    jQuery.ajax({
           type : "post",
           url :miglaAdminAjax.ajaxurl,
           data : {action: "miglaA_update_me", key:'migla_borderRadius', value:border},
           success: function(msg) {  
             saved( '#migla_borderRadius' );
          }
      })  ; //ajax	

});

jQuery('#migla_wellboxshadow').click(function(){
    var parent = jQuery(this).closest('div.row');
    var well = "";
    well = parent.find('.rgba_value').val() + ",";
    well = well + parent.find('input[name=hshadow]').val() + ",";
    well = well + parent.find('input[name=vshadow]').val() + ",";
    well = well + parent.find('input[name=blur]').val() + ",";
    well = well + parent.find('input[name=spread]').val();

    jQuery.ajax({
           type : "post",
           url :miglaAdminAjax.ajaxurl,
           data : {action: "miglaA_update_me", key:'migla_wellboxshadow', value:well},
           success: function(msg) {  
            jQuery(parent).find('#currentColor').css('background-color', jQuery(parent).find('.mg-color-field').val());	
            saved( '#migla_wellboxshadow' );
          }
      })  ; //ajax

});

	jQuery('#migla_progressbar_info').click(function() {

	   jQuery.ajax({
           type : "post",
           url :miglaAdminAjax.ajaxurl,
           data : {action: "miglaA_update_barinfo", key:'migla_progbar_info', value:jQuery('#migla_progressbar_text').val()},
           success: function(msg) { 
             saved('#migla_progressbar_info');
          }
          })  ; //ajax	 	
	
	});


     jQuery('.meffects').click(function() {
     
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
           url :miglaAdminAjax.ajaxurl,
           data : {action: "miglaA_update_us", Stripes:s, Pulse:ps, AnimatedStripes:as, Percentage:pc},
            success: function(msg) { 

           }
          })  ; //ajax	
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


 ////LEVEL SECTION
 jQuery('#migla_bgcolorLevelsSave').click(function(){

    var parent = jQuery(this).closest('div.row');
    var ColorCode = parent.find('#migla_bglevelcolor').val() ;

   jQuery.ajax({
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : {action: "miglaA_update_me", key:'migla_bglevelcolor', value:ColorCode },
	success: function(msg) {
          saved('#migla_bgcolorLevelsSave');
	}
    })  ; //ajax	
 });

 //LEVEL SECTION
 jQuery('#migla_borderlevelsave').click(function(){

    var parent = jQuery(this).closest('div.row');
    var ColorCode = parent.find('#migla_borderlevelcolor').val() ;
    var spinner = parent.find('.spinner-input').val();

   jQuery.ajax({
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : {action: "miglaA_update_me", key:'migla_borderlevelcolor', value:ColorCode },
	success: function(msg) {
	}
    })  ; //ajax

   jQuery.ajax({
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : {action: "miglaA_update_me", key:'migla_borderlevel', value:spinner  },
	success: function(msg) {
           saved('#migla_borderlevelsave');
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

  ohDrag(); 
  /*
  jQuery('.mg_box_edit').click(function(){
  
     var par    = jQuery(this).attr('name');
	 var edited = jQuery('#'+par).find('.mg_circle_content');
	 var edited_id = edited.attr('id') ;
	 
	 jQuery('.mg_box').removeClass('mg_cbox_active');
	 jQuery('.mg_box').addClass('mg_cbox_notactive');
	 jQuery('#'+par).addClass('mg_cbox_active');
	  jQuery('#'+par).removeClass('mg_cbox_notactive');

	 if( edited_id == 'html1' ){
	 
		jQuery('.mg_edit_circle').hide();
		jQuery('.mg_edit_bar').hide();
		jQuery('.mg_edit_circle_chooser').hide(); 
		jQuery('#mg_text_barometer_input').hide();
		
		 if( jQuery('.mg_edit_'+edited_id).is(':visible') ){
		 
			jQuery('.mg_edit_html1').hide();			
			jQuery('#migla_circle_box_save').hide();						
			
		 }else{
		 
			jQuery('.mg_edit_html1').show();
			jQuery('#migla_circle_box_save').show();
			jQuery('#migla_circle_box_save').attr('name', edited_id);
			
		 }			
	 
	 }else{
	 
	    jQuery('.mg_edit_html1').hide();
		
		    jQuery('.mg_edit_circle').show();
			jQuery('#mg_text_barometer_input').show();
			jQuery('#migla_circle_box_save').show();
			jQuery('#migla_circle_box_save').attr('name', edited_id);		
		
		 if( jQuery('.mg_edit_circle_chooser').is(':visible') ){
		 
			jQuery('.mg_edit_circle').hide();
			jQuery('.mg_edit_circle_chooser').hide();  
			jQuery('#migla_circle_box_save').hide();
            jQuery('.mg_edit_bar').hide();			
			jQuery('#mg_text_barometer_input').hide();
			 
		 }else{
		 
			jQuery('.mg_edit_circle').hide();
			jQuery('.mg_edit_circle_chooser').show();  
			jQuery('#mg_text_barometer_input').hide();
			jQuery('#migla_circle_box_save').show();
			jQuery('#migla_circle_box_save').attr('name', edited_id);
			
		 }		
	 }
	 	 
 });  
 */

/* 
  jQuery('#migla_circle_box_save').click(function(){
     var which_content = jQuery(this).attr('name');
	 if( which_content == 'circle' )
	 {
	     save_circle_settings();
	 }else if( which_content == 'html1' )
	 {
	     save_html_content(); 
	 }
  });
*/

 jQuery('#migla_save_circle_settings').click(function(){
    save_circle_settings();
	save_html_content();
 }); 

 jQuery('#miglaSaveCreatorSettings').click(function()
 {
    jQuery.ajax({
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : {
			 action   : 'miglaA_update_metadata' , 
			 post_id  : jQuery('#mg_current_form').val() , 
			 meta_key : 'migla_cmpcreator_text1',
			 meta_value    : jQuery('#migla_circle_text1').val() 				
			},
	success: function(msg) {    
			}
    })  ; //ajax	

   jQuery.ajax({
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : {
			 action   : 'miglaA_update_metadata' , 
			 post_id  : jQuery('#mg_current_form').val() , 
			 meta_key : 'migla_cmpcreator_text2',
			 meta_value    : jQuery('#migla_circle_text2').val() 				
			},
	success: function(msg) {    
			}
    })  ; //ajax		
	
   jQuery.ajax({
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : {
			 action   : 'miglaA_update_metadata' , 
			 post_id  : jQuery('#mg_current_form').val() , 
			 meta_key : 'migla_cmpcreator_text3',
			 meta_value    : jQuery('#migla_circle_text3').val() 				
			},
	success: function(msg) {    
			}
    })  ; //ajax		
	
   var circlelayout = jQuery('input[name=mg_circle-HTML]:checked').val();
   jQuery.ajax({
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : {
			 action   : 'miglaA_update_metadata' , 
			 post_id  : jQuery('#mg_current_form').val() , 
			 meta_key : 'migla_circle_layout',
			 meta_value    : circlelayout				
			},
	success: function(msg) {    
			}
    })  ; //ajax		
	
   var textalign = jQuery('input[name=mg_circle-text-align]:checked').val() ;	
   jQuery.ajax({
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : {
			 action   : 'miglaA_update_metadata' , 
			 post_id  : jQuery('#mg_current_form').val() , 
			 meta_key : 'migla_cmpcreator_textalign',
			 meta_value    : textalign			
			},
	success: function(msg) { 
			saved('#miglaSaveCreatorSettings');	
			}
    })  ; //ajax	   
 }); 
  
  jQuery('.mg_progress_li').click(function(){
     jQuery('#mg_text_barometer_input').show();
     var this_name = jQuery(this).attr('name');
	 if( this_name == 'mg_link_circle' ){
	    jQuery('.mg_edit_circle').show();
		jQuery('.mg_edit_bar').hide();
	 }else{
	    jQuery('.mg_edit_circle').hide();
        jQuery('.mg_edit_bar').show();		
	 }
  });

  add_new_campaign_creator();
  
  jQuery('input[name=mg_circle-HTML]').click(function(){
  
  });
  
 jQuery('input[name=mg_circle-text-align]').click(function(){
     var myValue = jQuery(this).val();
	 if( myValue == 'mg_no_text' )
	 {
	    jQuery('#mg_text_barometer_input').hide();
	 }else{
	    jQuery('#mg_text_barometer_input').show();	 
	 }
  });
    
  
}); //End of document

function findDuplicateName( checkvalue ){
    var trimVal = checkvalue.replace("'", "[q]"); 
    return jQuery(".titleChange[value='" + trimVal + "']").length ;
}

function add_new_campaign_creator()
{
  jQuery('#miglaAddCampaignCreator').click(function() 
  {  
	  var _title = jQuery('#mTitle').val();
	  var _desc = jQuery('#mDesc').val();
      var _campaign = jQuery('#mCampaign').val();
	  
	  if( jQuery.trim( _title ) == ''  )
	  {
          alert('Please fill in the campaign name');
		  canceled( '#miglaAddCampaignCreator' );
      }else{
          		  
 			  jQuery.ajax({
					type : "post",
					url :  miglaAdminAjax.ajaxurl, 
					data : { action: "miglaA_new_mCampaignCreator", 
							 title : _title, 
							 desc  : _desc,
							 campaign : _campaign
						   },
					success: function( fid ) 
					{  		  
						var str = addCampaign( _title, _desc, fid, _campaign );
						if( countAll() < 0 ){
							jQuery('ul.mg_campaign_list').empty();
						}
						jQuery(str).prependTo( jQuery('ul.mg_campaign_list') );
						saved('#miglaAddCampaignCreator'); 
						remove();
                   }//success ajax 1
            });				   
	 }
	
});

}

function addCampaign( label, desc, form_id, campaign )
{

   var newComer = "";
   var lbl = label.replace("'", "[q]");

    newComer = newComer + "<li class='ui-state-default formfield formfield_campain clearfix'>";
	newComer = newComer + "<input type='hidden' name='oldlabel' value='"+lbl+"' />";
	newComer = newComer + "<input type='hidden' name='label' value='"+lbl+"' />";
	newComer = newComer + "<input type='hidden' name='desc' value='"+desc+"' />";
	newComer = newComer + "<input type='hidden' name='form_id'  value='"+form_id+"' />";
   newComer = newComer + "<div class='col-sm-1 hidden-xs'><label  class='control-label'>Title</label></div>";
   newComer = newComer + "<div class='col-sm-2 col-xs-12'><input type='text' class='labelChange' name='' placeholder='";
   newComer = newComer + lbl + "' value='" + lbl + "' /></div>";

   newComer = newComer + "<div class='col-sm-1 hidden-xs'><label  class='control-label'>Description</label></div>";
   newComer = newComer + "<div class='col-sm-2 col-xs-12'><input type='text' class='descChange' name='' placeholder='";
   newComer = newComer + desc + "' value='" + desc + "' /></div>";
   
	newComer = newComer + "<div class='col-sm-1 col-xs-12'>";
	newComer = newComer + "<button id='form_"+desc+"' class='mg_a-form-per-campaign-options mbutton edit_custom-fields-list' onClick='mg_send_form_id("+form_id+")'>";
	newComer = newComer + "</button></div>";	
	
	newComer = newComer + "<div class='col-sm-3 col-xs-12'>";
	newComer = newComer +'<input type="text" value="[totaldonations-circle-progressbar id=\''+form_id+'\']" ';
	newComer = newComer + "placeholder='' name='' class='mg_label-shortcode' onclick='this.setSelectionRange(0, this.value.length)'></div>";
		 
   newComer = newComer + "<div class='control-radio-sortable col-sm-1 col-xs-12'>";
   newComer = newComer + "<span><button class='removeField' data-toggle='modal' data-target='#confirm-delete'><i class='fa fa-fw fa-trash'></i></button></span>";
   newComer = newComer + "</div>";

   newComer = newComer + "</li>";

   return newComer;
}

function remove()
{
  var form_id = '';
  jQuery('.removeList').click(function(){
	 del 		= jQuery(this).closest('li.formfield_campaign');   
     form_id 	= del.find('input[name=form_id]').val();	
  });

  jQuery('#mRemove').click(function(){
      del.remove(); 
       jQuery.ajax({
			type 	: "post",
			url 	:  miglaAdminAjax.ajaxurl, 
			data 	: {
						action  : "miglaA_delete_mform", 
						post_id : form_id
					},
			success: function(msg) {  
				jQuery( ".close" ).trigger( "click" );
			 }
       }) ; //ajax

  });
}

function titleChanged(){
  jQuery('.titleChange').bind("keyup change", function(e) {
   var p = jQuery(this).closest('li.formfield');

   var val = jQuery(this).val().replace("'", "[q]");
   p.find("input[name='title']").val( val );
  });
}

function descChanged(){
  jQuery('.descChange').bind("keyup", function(e) {
   var p = jQuery(this).closest('li.formfield');

   p.find("input[name='desc']").val( val );
  });
}

function countAll(){
	var c = -1;
	jQuery('li.formfield').each(function(){ c = c + 1; });
	return c;
}

//*************************************************/
//**			MENU EDIT			 		 **/

function ohDrag(){
  jQuery("div.containers").sortable({
    placeholder: "ui-state-highlight-container",
    revert: true,
    forcePlaceholderSize: true,
    axis: 'y',
    update: function (e, ui) {
        //alert("updated");
        //save();
    },
    start: function (e, ui) {
    }
  }).bind('sortstop', function (event, ui) {

  });

//jQuery("ul.containers").disableSelection();

function SetSortableRows(rows)
{
    rows.sortable({
    placeholder: "ui-state-highlight-row",
    connectWith: "div.rows:not(.containers)",
    containment: "div.containers",
    helper: "clone",
    revert: true,
    forcePlaceholderSize: true,
    axis: 'y',
    start: function (e, ui) {
    },
    update: function (e, ui) {

        //alert("updated");
        //save();
    },
    stop: function(e, ui){

    },
    received: function(e, ui){
    }
   }).bind('sortstop', function (event, ui) {
  }); 
}
  SetSortableRows(jQuery("ul.rows"));
//jQuery("ul.rows").disableSelection();
}

function allowDrop(ev) {
    ev.preventDefault();	
}

function drag(ev) {    
    //Get the ID and previous div
    ev.dataTransfer.setData("content", ev.target.id);	
    ev.dataTransfer.setData("previous", ev.target.getAttribute('name') );
}

function drag_(ev) {    
     ev.preventDefault();	
}

function drop(ev) {
    ev.preventDefault();
	
    var _receiver = '#' + ev.target.getAttribute("id") ;

        var p_div =  jQuery(_receiver).closest('.mg_box');
        _receiver =  '#' + p_div.attr('id');  
    
    var data_receive  = jQuery(_receiver).find('.mg_circle_content');
    var name_data_receive  = data_receive.attr('name');
	
    var data_send     = ev.dataTransfer.getData("content");
	var name_data_send  = jQuery('#'+data_send).attr('name') ;
	var _sender       = '#' + ev.dataTransfer.getData("previous");
	
	//alert( _receiver + _sender  );
	
	  //Switch
      jQuery( _receiver ).append( document.getElementById(data_send) );

      jQuery('#'+data_send).attr('name', name_data_receive) ;	
      data_receive.attr('name',  name_data_send);

      jQuery( _sender ).append( data_receive );  
      saved_the_box_arrangement();
}

function saved_the_box_arrangement(){
     var box = [];
     box.push( jQuery('#div1').find('.mg_circle_content').attr('id') );
     box.push( jQuery('#div2').find('.mg_circle_content').attr('id') );

   jQuery.ajax({
		type : "post",
		url : miglaAdminAjax.ajaxurl, 
		data : {
				 action   : 'miglaA_update_metadata' , 
				 post_id  : jQuery('#mg_current_form').val() , 
				 meta_key : 'migla_circle_boxes',
				 meta_value    : box					 
				 },
		success: function(msg) {
		}
    })  ; //ajax	
} 

function save_circle_settings()
{
    var circle_arr = {};

    circle_arr.size        = Number( jQuery('#migla_circle_size').val() );
    circle_arr.start_angle = Number( jQuery('#migla_circle_start_angle').val() );
    circle_arr.thickness   = Number( jQuery('#migla_circle_thickness').val() );

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
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : {
			 action   : 'miglaA_update_metadata' , 
			 post_id  : jQuery('#mg_current_form').val() , 
			 meta_key : 'migla_circle_settings',
			 meta_value    : carr  				
			},
	success: function(msg) {
           saved('#migla_circle_settings_save');
	}
    })  ; //ajax	

	/*
   jQuery.ajax({
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : { action   : "miglaA_load_circle" , 
                 settings : carr 
               },
	success: function( circle_result ) {
             jQuery('.migla_circle_wrapper').empty()
             jQuery('.migla_circle_wrapper').html(circle_result);
	}
    })  ; //ajax	
  */

}

function save_html_content(){
   jQuery.ajax({
		type : "post",
		url : miglaAdminAjax.ajaxurl, 
		data : {
				action   : 'miglaA_update_metadata' , 
				post_id  : jQuery('#mg_current_form').val() , 
				meta_key : 'migla_circle_box_html',
				meta_value    : get_tinymce_content() 					
				},
		success: function(msg) {
		}
    })  ; //ajax
}

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
  //style = style + convertHex(hex,opacity) + " inset" ;
  style = style + hex + " inset" ;
  return style;
}

function mg_send_form_id( var_id )
{
   jQuery('#mg_form_id_send').val( var_id);
   form_submit();
}

function mg_go_campaign()
{
   form_submit();
}

function form_submit(){
   jQuery( '#mg_submit_form' ).trigger( "click" );
}
