jQuery(document).ready(function() {

 jQuery('#miglaSaveUserList').click(function(){
      var users = [];
	  var allowed_caps  = [];
      var me = jQuery(this); 
      me.data( 'oldtext','save' );

      jQuery('.mg_li_user').each(function(){
         if( jQuery(this).find('.mg-settings').is(':checked') ){
             users.push( jQuery(this).attr('id') );
         }
      });
	  
	jQuery('.mg_user_caps').each(function(){
		allowed_caps.push( jQuery(this).val() );
	});  

   //alert( JSON.stringify(users) );

   jQuery('#miglaSaveUserList').data( 'oldtext', jQuery('#miglaSaveUserList').html() );

   jQuery.ajax({
		type : "post",
		url : miglaAdminAjax.ajaxurl, 
		data : {
				action	: "miglaA_update_me", 
				key		: "migla_allowed_capabilities", 
				value	: allowed_caps
			},
		success: function(m) {}
    })  ; //ajax

   jQuery.ajax({
		type : "post",
		url : miglaAdminAjax.ajaxurl, 
		data : {
				action	: "miglaA_update_me", 
				key	: "migla_allowed_users", 
				value	: users 
				},
		success: function(m) {
			   jQuery(me).html(' saved');
			   setTimeout(function (){
					jQuery(me).html(" <i class='fa fa-fw fa-save'></i> save");
				  }, 800);
		   }
    })  ; //ajax	

 });

 jQuery('.mg-settings').change(function(){

   var id = jQuery(this).attr('id');
   var msg = '#' + id + '_';
   var inputValue = 'no';

   if( jQuery(this).is(':checked') ){ inputValue = 'yes' }

   jQuery.ajax({
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : {action: "miglaA_update_me", key:id, value:inputValue },
	success: function() {
         
         jQuery(msg).text('Saved'); 
         setTimeout(function (){
           jQuery(msg).text(''); 
         }, 1000);

	}
    })  ; //ajax	   

 });

 jQuery('#migla_ajax_caller_setting').change(function(){

   var inputValue = 'td';
   if( jQuery(this).is(':checked') ){ inputValue = 'wp' }

   jQuery.ajax({
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : {action: "miglaA_update_me", key:'migla_ajax_caller', value:inputValue },
	success: function() {
           jQuery('#migla_ajax_caller_setting_').text('Saved'); 
           setTimeout(function (){
               jQuery('#migla_ajax_caller_setting_').text(''); 
           }, 1000);
	}
    })  ; //ajax	   

 });

 jQuery('#migla_allow_cors_setting').change(function(){
   var inputValue = 'no';
   if( jQuery(this).is(':checked') ){ inputValue = 'yes' }

   jQuery.ajax({
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : {action: "miglaA_update_me", key:'migla_allow_cors', value:inputValue },
	success: function() {
           jQuery('#migla_allow_cors_setting_').text('Saved'); 
           setTimeout(function (){
               jQuery('#migla_allow_cors_setting_').text(''); 
           }, 1000);
	}
    })  ; //ajax	
 });

 jQuery('#miglaEraseCache').click(function(){

      var me = jQuery(this); 
      me.data( 'oldtext', me.html() );

   jQuery.ajax({
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : {action: "miglaA_purgeCache" },
	success: function(m) {
           alert(m);
          
              jQuery(me).html('Saved');
                setTimeout(function (){
                jQuery(me).html(' ' + jQuery(me).data('oldtext') );
              }, 800);
	}
    })  ; //ajax	
 });

    jQuery('#miglaSetJSScriptLoad').click(function() {
       var id = '#' + jQuery(this).attr('id');
	  jQuery.ajax({
		type : "post",
		url : miglaAdminAjax.ajaxurl, 
		data : {action: "miglaA_update_me", key:'migla_script_load_js_pos', 
		    value: jQuery('select[name=migla_script_load_js_pos] option:selected').val() },
		success: function(msg) {  
                    saved(id);
		}
	  })  ; //ajax	
    });	

    jQuery('#miglaSetCSSScriptLoad').click(function() {
       var id = '#' + jQuery(this).attr('id');
	  jQuery.ajax({
		type : "post",
		url : miglaAdminAjax.ajaxurl, 
		data : {action: "miglaA_update_me", key:'migla_script_load_css_pos', 
		    value: jQuery('select[name=migla_script_load_css_pos] option:selected').val() },
		success: function(msg) {  
                    saved(id);
		}
	  })  ; //ajax	
    });	

});