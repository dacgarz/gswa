jQuery(document).ready(function(){

	//alert(miglaAdminAjax.ajaxurl);

   jQuery('.migla_select_campaign').click(function(){
		var id 		= jQuery(this).attr('id');
		var cvalue 	= jQuery(this).val().replace(/\s/g, "") ;
		var form_id	= jQuery('#mg_'+cvalue).html();
		var parent	= jQuery(this).closest('.widget');
				
		if( typeof form_id !== 'undefined' )
		{
			parent.find('.mg_form_id').val(form_id);

			jQuery.ajax({
			  type : 'post',
			  url  : miglaAdminAjax.ajaxurl, 
			  data : { 
						action	: 'miglaA_get_postmeta', 
						id	: form_id, 
						key     : 'migla_form_url'
					},
			  success: function(msg) {
                                if( msg == '' || msg == '-1' )
                                {
				   parent.find('.mg_form_url').html('None');
                                }else{
                                   parent.find('.mg_form_url').html(msg);
                                }
			  }
			})  ; //ajax			
		}
   });
   
	jQuery('.mg_select_circle_animation').click(function(){
		var value 	= jQuery(this).val();		
		var parent   = jQuery(this).closest('.mg_circle_animation_text_inside');
		var hideThis = parent.find('.mg_circle_text_inside');
		
		if( value == 'normal' || value == 'back_forth' )
		{
			hideThis.show();
		}else{
			hideThis.hide();		
		}
	});

});