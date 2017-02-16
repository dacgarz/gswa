var del;
var updatedList = [];
var changed_fields = [];
var radioState = {}; 
var currencies = []; 
var showDec ;
var tempid = -1; var btnid = 0;
var new_form_id = '';
var decSep = '.';
var thouSep = ',';
var showSep = 'no';
var radioState2 = [];

//*** MISC *****************************************************/
function is_float(mixed_var) {
	return +mixed_var === mixed_var && (!isFinite(mixed_var) || !! (mixed_var % 1));
}

function is_int(mixed_var) {
	return mixed_var === +mixed_var && isFinite(mixed_var) && !(mixed_var % 1);
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

function mg_getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function mg_random(){
  var date = new Date();
  var day        = String( date.getDate() );
  var monthIndex = String( date.getMonth() );
  var year       = String( date.getFullYear() );
  var hours      = String( date.getHours() );
  var minutes    = String( date.getMinutes() );
  var seconds    = String( date.getSeconds() );
  var rand_value = year + monthIndex + day + hours + minutes + seconds  + "_" + String(  mg_getRandomInt( 100000, 100000000) ); 
  return rand_value;
}

function mg_generate_uid(){
  var date = new Date();
  var day        = String( date.getDate() );
  var monthIndex = String( date.getMonth() );
  var year       = String( date.getFullYear() );
  var hours      = String( date.getHours() );
  var minutes    = String( date.getMinutes() );
  var seconds    = String( date.getSeconds() );

  var dd = "f" + year + monthIndex + day + hours + minutes + seconds  + "_" + String(  mg_getRandomInt( 100000, 100000000) ); 

  return dd;
}

function labelChanged(){
  jQuery('.labelChange').bind("keyup change", function(e) {
   var p = jQuery(this).closest('li.formfield');

   var val = jQuery(this).val().replace("'", "[q]");
   p.find("input[name='label']").val( val );
  });
}

function targetChanged(){
  jQuery('.targetChange').bind("keyup", function(e) {
   var p = jQuery(this).closest('li.formfield');

   p.find("input[name='target']").val( val );
  });
}


//****************************************************/
//*			FORM			*/
//****************************************************/
function disFormfield(){
  jQuery('body').find('li.formfield').each(function(){
    if( jQuery(this).hasClass('justAdded') ){
      //skipp
    }else{
       jQuery(this).find('input').addClass('disabled');
    }
  });
}

function enFormfield(){
  jQuery('li.formfield').each(function(){
      jQuery(this).find('input').removeClass('disabled');
  });
}

function countAll(){
	var c = -1;
	jQuery('li.formfield').each(function(){ c = c + 1; });
	return c;
}

function findDuplicateName( checkvalue ){
    var trimVal = checkvalue.replace("'", "[q]"); 
    return jQuery(".labelChange[value='" + trimVal + "']").length ;
}

function getRidForbiddenChars(){
  jQuery('li.formfield').each(function() { 
     var lbl 	= jQuery(this).find(".labelChange").val();
     var r 		= lbl.replace("[q]","'");
     jQuery(this).find(".labelChange").val( r ); 
  });
}

function ohDrag(){
  jQuery('#section3').find("ul.containers").sortable({
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
     jQuery("ul.rows").find('input[type="radio"]').each(function() {
       if(  radioState[ jQuery(this).attr('name') ] === jQuery(this).val() ){
         jQuery(this).prop('checked', true);
       }
     });
  });

   SetSortableRows(jQuery("ul.rows"));

}

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
        jQuery(this).find('input[type="radio"]').each(function() {
          if( jQuery(this).is(':checked') ){
            radioState[ jQuery(this).attr('name') ] = jQuery(this).val();
          }
        });
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
     jQuery(this).find('input[type="radio"]').each(function() {
       if(  radioState[ jQuery(this).attr('name') ] === jQuery(this).val() ){
         jQuery(this).prop('checked', true);
       }
     });
           //checkGroup();
  }); 
}

function ohLevelDrag(){
  jQuery("#miglaAmountTable").sortable({
		helper		: "clone",
		revert		: true,
		forcePlaceholderSize: true,
		axis		: 'y',
		start: function (e, ui) {
		},
		update: function (e, ui) {
			mg_save_amount_list();
		},
		stop: function(e, ui){
		},
		received: function(e, ui){
		}
   });
}

function mg_save_amount_list(){
		      var amount_levels_array = [];
			  
			  jQuery('.mg_amount_level').each(function(){
		          var amount_levels = {};	
			      var aValue           = jQuery(this).find('.mg_amount_level_value').val();
				  var aPerkValue       = jQuery(this).find('.mg_amount_level_perk').html();
				  amount_levels.amount = aValue ;
				  amount_levels.perk   = aPerkValue ;
				  amount_levels_array.push(amount_levels);
			  });
		  
		       //alert( JSON.stringify( amount_levels_array) );

			   jQuery.ajax({
					type : "post",
					url :  miglaAdminAjax.ajaxurl, 
					data : {
							 action   : 'miglaA_update_metadata' , 
							 post_id  : jQuery('#mg_current_form').val() , 
							 meta_key : 'migla_amounts',
							 meta_value    : amount_levels_array				
						   },
					success: function(msg) {
								jQuery('#warningEmptyAmounts').hide(); 	
								jQuery('#miglaAddAmount').val('');
								jQuery('#miglaAmountPerk').val('');
								remove(); 							
							}
			   }); //ajax
}

function ohCustomDrag(){
  jQuery("#mg_custom_list_container").sortable({
		helper		: "clone",
		revert		: true,
		forcePlaceholderSize: true,
		axis		: 'y',
		start: function (e, ui) {
		},
		update: function (e, ui) {
		    mg_save_custom_list('');
		},
		stop: function(e, ui){
		},
		received: function(e, ui){
		}
   });
}

function mg_campaign_deletion()
{
	var del = jQuery('.mg_on_remove_list').closest('li');
	
       jQuery.ajax({
			type 	: "post",
			url 	:  miglaAdminAjax.ajaxurl, 
			data 	: {
						action  : "miglaA_delete_mform", 
						post_id : del.find('input[name=form_id]').val()
					  },
			success: function(msg) {  
					}
       }) ; //ajax 
 
      del.remove();
	  var list = getCampaignStructure();
	  
       jQuery.ajax({
			type 	: "post",
			url 	:  miglaAdminAjax.ajaxurl, 
			data 	: {
						action : "miglaA_save_campaign", 
						values:list
					  },
			success: function(msg) {  
						jQuery( ".close" ).trigger( "click" );
					}
       }) ; //ajax

}

function removeCampaignField(){
 
jQuery('.removeCampaignField').click( function(){

	jQuery('.removeCampaignField').each(function(){
		jQuery(this).removeClass('mg_on_remove_list');	
	});	
    jQuery(this).addClass('mg_on_remove_list');

	jQuery('#confirm-delete').show();
});

jQuery('#confirm-delete').on('show.bs.modal', function(e) {
	jQuery('.btn-danger').show();           
});

jQuery('.mg_campaign_remove_cancel').click( function(e) {
});

jQuery('#mg_campaign_remove').click(function(){
	mg_campaign_deletion();
});

}

function getFormStructure()
{
   var fields = [];
   changed_fields.length = 0;

   jQuery('#section3').find('li.formheader').each(function()
   {
      var group = {};
      var t = jQuery(this).find("input[name='grouptitle']").val();
      group.title = t.replace("'","[q]");
      group.parent_id = 'NULL';

      if ( jQuery(this).find(".toggle").is( ":checked" ) )
      {
			group.toggle = '1';
      }else{
			group.toggle = '0';
      }

      var leaf = -1;
      var children = []; 
      var i = 0;
      jQuery(this).find('li.formfield').each(function() { 
        var child = {}; var changed = [];
        leaf = leaf + 1; 

        var lbl = jQuery(this).find(".labelChange").val();
        child.label = lbl.replace("'","[q]");

        child.code = jQuery(this).find("input[name='code']").val();
         
        child.id =  jQuery(this).find("input[name='id']").val(); 

        if( child.code == 'miglac_' )
        {
          var new_id = lbl.replace("'","[q]");
          new_id     = new_id.replace(" ", "");

          var old_id = jQuery(this).find("input[name='id']").val();          

          if( new_id != old_id )
          {            
            changed[0] = old_id;
            changed[1] = new_id;
            changed_fields.push( changed );

            jQuery(this).find("input[name='id']").val(new_id);
            child.id =  new_id; 
          }
        }

        child.type = jQuery(this).find("select[name=typeChange] option:selected").val();
        jQuery(this).find("input[name='type']").val( child.type );
        //alert(child.type);
        
        var status = "1";
        jQuery(this).find("input[type=radio]").each(function(){
           if( jQuery(this).is(':checked') ){
             status = jQuery(this).val();
           }
        });
        child.status = status;

        child.uid = jQuery(this).find("input[name='uid']").val();

        if( (child.code == 'miglad_') && (child.status == '2') ){ child.status = '3' }     
  
        children.push(child);
      });
      
      group.depth = leaf;
      group.child = children;
      
      fields.push(group);
   });
   //alert( fields[0]['title'] );
   
  //alert( JSON.stringify(changed_fields) );

   return fields;
}

function isFieldValid()
{
	  var isValid = true;
	  var BreakException= {};

	  try {
		jQuery('#section3').find('li.formheader').each(function()
		{
			var title = jQuery(this).find("input[name='grouptitle']").val();
		
			if( title == '' || findDuplicateTitle(title) > 1 )
			{ 
			   isValid = false; throw BreakException;
			}
		
		   var row = jQuery(this).find('ul.rows');
		   row.find('li.formfield').each(function(){
			  var label = jQuery(this).find('.labelChange').val();

			  if( label == '' || findDuplicateLabel(label) > 1 )
			  {
					isValid = false; throw BreakException;
			  }
		   });

		});
	  } catch(e) {
			if (e!==BreakException) throw e;
	  }  
	  return isValid;
}

function findDuplicateTitle( checkvalue ){
    var trimVal = checkvalue.replace("'", "[q]"); 
    return jQuery(".mHiddenTitle[value='" + trimVal + "']").length ;
}

function findDuplicateLabel( checkvalue ){
    var trimVal = checkvalue.replace("'", "[q]");  
    return jQuery(".mHiddenLabel[value='" + trimVal + "']").length ;
}

function calcChildren( group ){
  var count = 0;
  group.find('li.formfield').each(function() {
    count = count + 1; 
  });
  return count;
}

function clearLeftover(){
  jQuery('li.formheader').each(function(){
    jQuery(this).find('.titleChange').val( jQuery(this).find('.mHiddenTitle').val() );

    jQuery('li.formfield').each(function(){
    // alert(jQuery(this).find('input[name=label]').val());
     jQuery(this).find('.labelChange').val( jQuery(this).find('input[name=label]').val() );

     var s = jQuery(this).find('input[name=type]').val();
      jQuery(this).find(".typeChange option[value='" + s + "']").attr("selected","selected");
    });
  })
}

function deleteGroup(){
	jQuery('.mDeleteGroup').click(function() {
	  var parent = jQuery(this).closest('.formheader');
	  
	  if( parent.find('li.form_field').length > 0 ){
			alert("Groups that have fields in them cannot be deleted. Move the fields to a different group or delete them before deleting the group ");
	  }else{
			parent.remove();
			jQuery.ajax({
			  type : "post",
			  url  : miglaAdminAjax.ajaxurl, 
			  data : { 
						action	: "miglaA_update_cform", 
						values	: getFormStructure(), 
						changes : changed_fields ,
						formID  : jQuery('#mg_current_form').val()
					},
			  success: function(msg) {  
			  }
			})  ; //ajax	
	  }
	});
}

function addField()
{
   var currentRow ;
   var currentRowid;

   jQuery('.mAddField').click(function()
   {
	   parent = jQuery(this).closest('.formheader'); //group header
	   currentRow = parent.find('ul.rows'); //check the children list

	  if( jQuery('body').find('li.justAdded').length > 0 )
	  {  
	  }else{

			disFormfield();

			tempid = tempid + 1;
			var parent ;

			var newlist = "";
			newlist = writeList( tempid );

			jQuery(newlist).prependTo( currentRow );

			if( !parent.find('.mDeleteGroup').hasClass('disabled') ) { 
				parent.find('.mDeleteGroup').addClass('disabled'); 
			}

			labelChanged();

		////CANCEL//////////////
			jQuery('.cancelAddField').click(function()
			{
				enFormfield();

			   jQuery('#section3').find('li.formheader').each(function(){
					var currow = jQuery(this).find('ul.rows'); 
					  currow.find('li.justAdded').each(function(){ 
							jQuery(this).fadeOut('slow').remove()
					  });
					  tempid = -1;
					  if( currow.children('li').length > 0 ){ 
							parent.find('.mDeleteGroup').removeClass('disabled');  
					  }
					});
			});
		}
		
	 jQuery('#saveNewField').click(function(){

		  var me = jQuery(this); 
		  me.data( 'oldtext', me.html() );
		  me.text('Saving...'); jQuery("<i class='fa fa-fw fa-spinner fa-spin'></i>" ).prependTo( me ); 

		 var curFormField = jQuery(this).closest('li.justAdded'); // formfield
		 var newLabel = curFormField.find('.labelChange');
		 var newList = [];

		 //CHEK VALID/////////////////
		 var isValid = true;
		 var BreakException= {};

		try {    
		   //alert( findDuplicateLabel(  newLabel.val() ) );
		   if( newLabel.val() == '' || findDuplicateLabel(  newLabel.val() ) > 1 ){
			 isValid = false; throw BreakException;
		   }
		} catch(e) {
		   if (e!==BreakException) throw e;
		} 


	   if( isValid )
	   {
		 jQuery('#section3').find('li.justAdded').each(function()
		 {
			 var x = jQuery(this).find("input.labelChange").val();
			 n = x.replace(" ","");
			 x = n.replace("'","");

			jQuery(this).find("input[name='id']").val(x);
	 
			jQuery(this).find("input[type=radio]").each(function(){
			   jQuery(this).attr('name', (x+'st') );
			});

			var new_type = jQuery(this).find("select[name='typeChange'] option:selected").val();
			jQuery(this).find("input[name='type']").val( new_type );

		   /******** Editable *************************************************************/
		   if( new_type == 'select' || new_type == 'radio' || new_type == 'multiplecheckbox' ){
				var me = jQuery(this).find(".ctype");
				if( me.find('.edit_select_value').length == 0 ){
					 
					  var html = "<div class='col-sm-2 col-xs-12'><button class='mbutton edit_select_value' >Enter Values</button></div>";
					  jQuery( html ).insertAfter( me );
				}
		  }

		  jQuery(".edit_select_value").click(function(e){
			  e.preventDefault();
			  var parent = jQuery(this).closest('li.formfield');
			  var recId  = "mgval_" + parent.find("input[name='uid']").val();

			  jQuery("#mg_id_custom_values_edit").text("");
			  jQuery("#mg_id_custom_values_edit").text(recId);
			  jQuery('#mg_add_values').modal('show');
		  });

		  /******** Editable *************************************************************/

		  jQuery(this).removeClass('justAdded');

		});

		  jQuery.ajax({
		   type : "post",
		   url : miglaAdminAjax.ajaxurl, 
		   data : {
					action	: "miglaA_update_cform", 
					values	: getFormStructure(), 
					changes	: changed_fields,
					formID  : jQuery('#mg_current_form').val()
					},
		   success: function(msg) { 
					 saved("#saveNewField"); 
					 jQuery('body').find('.rowsavenewcomer').remove();
					 removeField(); 
					 ohDrag();
				}
		 })  ; //ajax	 

		 enFormfield(); 
		 field_type_change();
		 ifTypeChange();
		 
	   }else{
		  alert("No empty values please or duplicate label !");
		  canceledLoser( "#saveNewField", "<i class='fa fa-fw fa-save'></i> Save field");
	   }

	 }); //SaveNewField
	 

	 
 }); //AddNewField
 
 }
 
function removeField(){
 
jQuery('.removeField').click( function(){

    var parent =  jQuery(this).closest('li');
    var group =  parent.closest('ul.rows');
    group = group.closest('li.formheader');
   
    //alert(parent.attr('class'));
    if( parent.find("input[name='code']").val() === 'miglad_' ){
        alert("You can not remove default field !");
        return false;
    }else{
        var type_ = parent.find("input[name='type']").val();
        //alert(type_);
        if( type_ === 'select' || type_ === 'radio' || type_ === 'multiplecheckbox' )
        {
           var recId  = "mgval_" + parent.find("input[name='uid']").val();
           //alert( recId + " " + jQuery('#migla_custom_values_id').val() );
           jQuery.ajax({
               type : "post",
               url : miglaAdminAjax.ajaxurl, 
               data : {
						action	: "miglaA_delete_postmeta", 
						key 	: recId , 
						id 		: jQuery('#migla_custom_values_id').val() 
						},
               success: function(msg) { 
               }, asycn : true
           })  ; //ajax	
        }

          jQuery(this).closest('li').remove();
		  
          jQuery.ajax({
              type : "post",
              url : miglaAdminAjax.ajaxurl, 
              data : {
						action  : "miglaA_update_cform", 
						values  : getFormStructure() , 
						changes :changed_fields,
						formID  : jQuery('#mg_current_form').val()
						},
              success: function(msg) { 
                         var count = calcChildren( group );
                         if( Number(count) < 1  ){ group.find('.mDeleteGroup').removeClass('disabled');  }

             }
         })  ; //ajax	
    } 
 });

}

function deleteGroup(){
	jQuery('.mDeleteGroup').click(function() {
	  var parent = jQuery(this).closest('.formheader');
	  
	  if( parent.find('li.formfield').length > 0 ){
			alert("Groups that have fields in them cannot be deleted. Move the fields to a different group or delete them before deleting the group ");
	  }else{
			parent.remove();
			jQuery.ajax({
			  type : "post",
			  url  : miglaAdminAjax.ajaxurl, 
			  data : { 
					action	: "miglaA_update_cform", 
					values	: getFormStructure(), 
					changes	: changed_fields ,
					formID  : jQuery('#mg_current_form').val()
					},
			  success: function(msg) {  
			  }
			})  ; //ajax	
	  }
	});
}

function writeList( tempid )
{

   var  newComer = "";  var random_uid =  mg_generate_uid();
   newComer = newComer + "<li class='ui-state-default formfield clearfix justAdded'>";
   
   newComer = newComer + "<input class='mHiddenLabel' type='hidden' name='label' value='' />";
   newComer = newComer + "<input type='hidden' name='type' value='text' />";
   newComer = newComer + "<input type='hidden' name='id' value='' />";
   newComer = newComer + "<input type='hidden' name='code' value='miglac_' />";
   newComer = newComer + "<input type='hidden' name='status' value='1' />";
   newComer = newComer + "<input type='hidden' name='uid' value='" + random_uid + "' />";

   newComer = newComer + "<div class='clabel col-sm-1 hidden-xs'><label class='control-label'>Label:</label></div>";
   newComer = newComer + "<div class='col-sm-3 col-xs-12'><input type='text' class='labelChange' name='labelChange' placeholder='";
   newComer = newComer + "' value='' /></div>";

   newComer = newComer + "<div class='ctype col-sm-2 col-xs-12'>";

   newComer = newComer + "<select class='typeChange' name='typeChange'>";

   newComer = newComer + "<option value='text'>text</option>";
   newComer = newComer + "<option value='checkbox'>checkbox</option>";
   newComer = newComer + "<option value='textarea'>textarea</option>";
   newComer = newComer + "<option value='select'>select</option>";
   newComer = newComer + "<option value='radio'>radio</option>";
   newComer = newComer + "<option value='multiplecheckbox'>multiple checkbox</option>";

   newComer = newComer + "</select>";

   newComer = newComer + "</div>";
   newComer = newComer + "<div class='ccode' style='display:none'>miglac_</div>";

   newComer = newComer + "<div class='control-radio-sortable col-sm-4 col-xs-12'>";

   newComer = newComer + "<span><label><input type='radio' name='r"+tempid+"'  value='1' checked > Show</label></span>";
   newComer = newComer + "<span><label><input type='radio' name='r"+tempid+"'  value='0' > hide</label></span>";
   newComer = newComer + "<span><label><input type='radio' name='r"+tempid+"'  value='2' > mandatory</label></span>";
   newComer = newComer + "<span><button class='removeField'><i class='fa fa-fw fa-trash'></i></button></span></div>";


   newComer = newComer + "<div class='row rowsavenewcomer'>";
   newComer = newComer + "<div class='addButton col-sm-12 '>";
   newComer = newComer + "<button id='' class='btn btn-default mbutton cancelAddField' type='button'>Cancel</button>";
   newComer = newComer + "<button id='saveNewField' class='btn btn-info pbutton AddNewComer' type='button'>";
   newComer = newComer + "<i class='fa fa-fw fa-save'></i>save field</button>";
   newComer = newComer + "</div>";
   newComer = newComer + "</div>";

   newComer = newComer + "</li>";

 return newComer;
}

function field_type_change()
{
  jQuery('.typeChange').on('click', function(e){

     var p = jQuery(this).closest('li.formfield');
     var type_val = jQuery(this).val() ;
     p.find("input[name='type']").val(type_val);

    if( type_val=='select' || type_val=='radio' || type_val=='multiplecheckbox' )
    {
      if( p.find('.edit_select_value').length == 0 ){
        jQuery("<div class='col-sm-2 col-xs-12'><button class='mbutton edit_select_value' >Enter Values</button></div>").insertAfter( p.find('.ctype') );
        
        jQuery(".edit_select_value").click(function(e){
			e.preventDefault();
			var parent = jQuery(this).closest('li.formfield');
			var recId  = "mgval_" + parent.find("input[name='uid']").val();

			jQuery("#mg_id_custom_values_edit").text("");
			jQuery("#mg_id_custom_values_edit").text(recId);
			jQuery('#mg_add_values').modal('show');
		});
      }
    }else{
         p.find('.edit_select_value').remove();  
    }

  });
}

function ifTypeChange(){
	 jQuery('.typeChange').change(function(){
		 var optionSelected = jQuery(this).find("option:selected");
		 var valueSelected  = optionSelected.val();
		 var textSelected   = optionSelected.text();
		 //alert( valueSelected + textSelected );
		
	   jQuery(this).find("option").each(function(){
			 if( jQuery(this).val() == valueSelected ){
			   jQuery(this).attr('selected', 'selected');
			 }else{
			   jQuery(this).removeAttr('selected');
			 }
	   });

	 });
}

function mg_delete_custom_list(){
   jQuery('.mg_customlist_remove').bind( "click", function() {
       var par = jQuery(this).closest(".mg_custom_list_row"); 
       par.remove();
   });
}

 function mg_save_custom_list( flag ){

    var data_obj = ""; 

   if( jQuery('.mg_custom_list_row').length > 0  ){

     jQuery('.mg_custom_list_row').each(function(){
          data_obj = data_obj + jQuery(this).find('.mg_customlist_key').val() + "::" + jQuery(this).find('.mg_customlist_val').val() + ";";
     });   

     jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {
				action	: "miglaA_update_postmeta", 
				key		: jQuery('#mg_id_custom_values_edit').text(), 
				id		: jQuery('#mg_current_form').val() , 
				value	: data_obj 
				},
        success: function() {  

        }
     }); //ajax

   }else{

    jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {
				action	: "miglaA_update_postmeta", 
				key		: jQuery('#mg_id_custom_values_edit').text(), 
				id		: jQuery('#mg_current_form').val() , 
				value	: "" 
				},
        success: function() { 

        }
     }); //ajax

   }
 }

//****************************************************/
//*			CAMPAIGN				*/
//****************************************************/

function addCampaign( label, target, form_id )
{

   var newComer = "";
   if( target == '' ){
		target = 0;
   }
   var lbl = label.replace("'", "[q]");

    newComer = newComer + "<li class='ui-state-default formfield formfield_campain clearfix'>";
	newComer = newComer + "<input type='hidden' name='oldlabel' value='"+lbl+"' />";
	newComer = newComer + "<input type='hidden' name='label' value='"+lbl+"' />";
	newComer = newComer + "<input type='hidden' name='target' value='"+target+"' />";
	newComer = newComer + "<input type='hidden' name='show'  value='1' />";
	newComer = newComer + "<input type='hidden' name='form_id'  value='"+form_id+"' />";
   newComer = newComer + "<div class='col-sm-1 hidden-xs'><label  class='control-label'>Campaign</label></div>";
   newComer = newComer + "<div class='col-sm-2 col-xs-12'><input type='text' class='labelChange' name='' placeholder='";
   newComer = newComer + lbl + "' value='" + lbl + "' /></div>";

   newComer = newComer + "<div class='col-sm-1 hidden-xs'><label  class='control-label'>Target</label></div>";
   newComer = newComer + "<div class='col-sm-2 col-xs-12'><input type='text' class='targetChange miglaNAD' name='' placeholder='";
   newComer = newComer + target + "' value='" + target + "' /></div>";
   
	newComer = newComer + "<div class='col-sm-1 col-xs-12'>";
	newComer = newComer + "<button id='form_"+form_id+"' class='mg_a-form-per-campaign-options mbutton edit_custom-fields-list' onClick='mg_send_form_id("+form_id+")'>";
	newComer = newComer + "</button></div>";	
	
	newComer = newComer + "<div class='col-sm-2 col-xs-12'>";
	newComer = newComer +'<input type="text" value="[totaldonations form_id=\''+form_id+'\']" ';
	newComer = newComer + "placeholder='' name='' class='mg_label-shortcode' onclick='this.setSelectionRange(0, this.value.length)'></div>";
	 
   var c = countAll(); c = c + 1;
   newComer = newComer + "<div class='control-radio-sortable col-sm-3 col-xs-12'>";
   newComer = newComer + "<span><label><input type='radio' name=r'"+c+"'  value='1' checked='checked' > Show</label></span>";
   newComer = newComer + "<span><label><input type='radio' name=r'"+c+"'  value='-1' > Deactivate</label></span>";

   newComer = newComer + "<span><button class='removeCampaignField' data-toggle='modal' data-target='#confirm-delete'><i class='fa fa-fw fa-trash'></i></button></span>";
   newComer = newComer + "</div>";

   newComer = newComer + "</li>";

   return newComer;
}

function getCampaignStructure(){
   var fields = []; 
   updatedList.length = 0;
   var c = 0;

   jQuery('li.formfield_campain').each(function(){
      var item = {};

      var temp = jQuery(this).find('.labelChange').val();      
      var target = String(jQuery(this).find('.targetChange').val());

      if( target == '' ){
        jQuery(this).find('.targetChange').val('0');
        target = 0;
      }

      item.name 	= temp.replace("'", "[q]");
      item.target 	= target; 
      item.show 	= jQuery(this).find("input[type='radio']:checked").val();  
	  item.form_id	= jQuery(this).find('input[name=form_id]').val() ;
      
      if( item.name != jQuery(this).find('input[name=oldlabel]').val() )
	  {
	     var str_update_list = jQuery(this).find('input[name=oldlabel]').val()+"-**-"+item.name+"-**-"+jQuery(this).find('input[name=form_id]').val();
         updatedList.push( str_update_list );
      }

      fields.push(item);
      //alert(item.show);
      c = c + 1;
  });
   
	//alert( JSON.stringify(fields) );
	
	save_radio_status();
	
	return fields;
}


/////////GET RID THE FORBIDDEN CHAR/////////////
function getRidForbiddenCampaignChars(){
  jQuery('li.formfield').each(function() { 
     var lbl = jQuery(this).find("input[name=label]").val();
     var r = lbl.replace("[q]","'");
     jQuery(this).find(".labelChange").val( r ); 

     var target = jQuery(this).find("input[name=target]").val();
     jQuery(this).find(".targetChange").val( target ); 
  });
}

function getRidForbiddenCampaignCharsLoad(){
  jQuery('li.formfield').each(function() { 
     var lbl = jQuery(this).find("input[name=label]").val();
     var r = lbl.replace("[q]","'");
     jQuery(this).find(".labelChange").val( r ); 

     var target = jQuery(this).find("input[name=target]").val();
     jQuery(this).find(".targetChange").val( target ); 

     var show = jQuery(this).find("input[name=show]").val();
     jQuery(this).find("input[value='"+show+"']").prop('checked',true); 
	 
  });
}


//****************************************************/
//*			AMOUNT				*/
//****************************************************/
function remove(){
	jQuery('.miglaRemoveLevel').click( function() {
		var parent = jQuery(this).closest('p.mg_amount_level');
		parent.remove();
	 
		mg_save_amount_list();
	 
		if( jQuery('p.mg_amount_level').length < 1 ){
			jQuery('#warningEmptyAmounts').show();
		}else{
			jQuery('#warningEmptyAmounts').hide();
		}
   });	
}

function drawAmountLevel( key, amount, perk ){
   var str = '';
   var decimal = jQuery('#sep2').text();
   str = str + "<p id='amount"+key+"'>";
   str = str + "<input class='value' type=hidden id='"+ amount +"' value='"+ amount +"' />";
   str = str + "<label>" + amount.replace(".", decimal ) + "</label>";
   str = str + "<label>" + perk + "</label>";   
   str = str + "<button name='miglaAmounts' class='miglaRemoveLevel obutton'><i class='fa fa-times'></i></button>";
   str = str + "</p>";
  return str;
}

jQuery(document).ready(function() {    
   //alert("Load OK");
    jQuery('#campaign-fa').click(function() {
       jQuery('#panel-addcampaign').toggle();
    });	 

	decSep 	= jQuery('mg_decSep').val();
	thouSep = jQuery('mg_thouSep').val();
	showSep = jQuery('mg_showSep').val();
	
  ohDrag();
  ohLevelDrag();
  ohCustomDrag();

  getRidForbiddenChars();
  labelChanged(); 
  targetChanged();
  remove();
  deleteGroup();
  addField();
  ifTypeChange();
  field_type_change();
  removeCampaignField();
  removeField();
  clearLeftover();
  
  save_radio_status();
  getRidForbiddenCampaignCharsLoad();
  ohCampaignDrag();
   
  jQuery('.mg_a_nav_pills').click(function(){
	getRidForbiddenChars();
  });

  jQuery('#mName').val('');
  jQuery('#mAmount').val('');

  jQuery('#miglaAddCampaign').click(function() {
	  
	  var name = jQuery('#mName').val();
	  var target = jQuery('#mAmount').val();
	  
	  if( jQuery.trim(name) == ''  )
	  {
          alert('Please fill in the campaign name');
		  canceled( '#miglaAddCampaign' );
      }else{
          
	      if( findDuplicateName( name ) > 0)
		  {
		      alert('No duplicate campaign name is allowed');
                       canceled( '#miglaAddCampaign' );
		  }else{
		  		  
 			  jQuery.ajax({
					type : "post",
					url :  miglaAdminAjax.ajaxurl, 
					data : { action: "miglaA_new_mform", 
							 title : name, 
							 desc  : ''
						   },
					success: function( fid ) 
					{  	

			var str = addCampaign( name , target, fid );
			if( countAll() < 0 )
			{
				jQuery('ul.mg_campaign_list').empty();
			}
			jQuery(str).prependTo( jQuery('ul.mg_campaign_list') );

			var campaign_structure = getCampaignStructure();
			
			jQuery.ajax({
				type : "post",
				url :  miglaAdminAjax.ajaxurl, 
				data : { action: "miglaA_save_campaign", 
									 values : campaign_structure, 
									 update : updatedList
				},
				success: function(msg) {  
						removeCampaignField();
						getRidForbiddenCampaignChars();
						labelChanged(); 
						targetChanged();
						saved( '#miglaAddCampaign' );
						jQuery('#mName').val('');
						jQuery('#mAmount').val('');						
				}
			})  ; //ajax				  
					
                       				
			 		}//Success First Ajax
			  })  ; //ajax			  
		}   
	 }
	
});

//Add Campaign
jQuery('#miglaSaveCampaign').click(function() {

   var empty_count = 0; 
   var array_name = []; 
   var duplicate_count = 0;
   
   jQuery('li.formfield').each(function(){
       var campaign_name = jQuery(this).find('.labelChange').val();
	   
	   if( array_name.indexOf(campaign_name) > -1 )
	   {
          duplicate_count = duplicate_count + 1;
	   }else{
	     array_name.push( campaign_name );	   
	   }
	   
       if( jQuery.trim(campaign_name) == '' ){
          empty_count = empty_count + 1;
       }
   });

   if( empty_count > 0 ){
       alert('No empty campaign is allowed');
       canceled( '#miglaSaveCampaign' );
   }else{
     
	 if( duplicate_count > 0 )
	 {
             alert('No duplicate name is allowed');
             canceled( '#miglaSaveCampaign' );
     }else{
		var list = getCampaignStructure(); 
		
		 jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : { 
			         action: "miglaA_save_campaign", 
			         values: list, 
					 update: updatedList
				   },
			success: function(msg) {  
					//alert(msg); 
					labelChanged(); 
					targetChanged();
					saved( '#miglaSaveCampaign' );									
					
                                        jQuery('li.formfield').each(function() { 
                                           var lbl = jQuery(this).find("input[name=label]").val();
                                           var r = lbl.replace("[q]","'");
                                           jQuery(this).find(".labelChange").val( r ); 
                                        });
                                          
			}
		 })  ; //ajax
		 		 
	  }
   }

});


jQuery("input[type='radio']").each(function(){
	var parent = jQuery(this).closest('.formfield');
 
   jQuery(this).click(function(){
      if( jQuery(this).val()== "-1" )
      {
        //alert("clicked");
        parent.addClass('pink-highlight');
      }else{
        parent.removeClass('pink-highlight');
      }
   });

})

jQuery(("input[type='radio']:checked")).each(function(){
	var parent = jQuery(this).closest('.formfield');
      if( jQuery(this).val()== "-1" )
      {
        parent.addClass('pink-highlight');
      }else{
        parent.removeClass('pink-highlight');
      }
})

  jQuery('#mg_amount_settings').click(function(){
     var isHide = 'no';
	 if( jQuery('#mHideHideCustomCheck').is(':checked') )
	 { 
	     isHide = 'yes'; 
	 }
	 jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : { 
			         action   : 'miglaA_update_metadata' , 
			         post_id  : jQuery('#mg_current_form').val() , 
					 meta_key : 'migla_hideCustomAmount',
					 meta_value    : isHide
				   },
			success: function(msg) {  
			}
	 })  ; //ajax
	 
	 jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : { 
			         action   : 'miglaA_update_metadata' , 
			         post_id  : jQuery('#mg_current_form').val() , 
					 meta_key : 'migla_custom_amount_text',
					 meta_value    : jQuery('#mg_custom_amount_text').val()
				   },
			success: function(msg) {  
			}
	 })  ; //ajax	 
	 
	 jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : { 
			         action   : 'miglaA_update_metadata' , 
			         post_id  : jQuery('#mg_current_form').val() , 
					 meta_key : 'migla_amount_box_type',
					 meta_value    : jQuery('#migla_amount_box_type').val()
				   },
			success: function(msg) {  
			}
	 })  ; //ajax	 	 

	 jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : { 
			         action   : 'miglaA_update_metadata' , 
			         post_id  : jQuery('#mg_current_form').val() , 
					 meta_key : 'migla_amount_btn',
					 meta_value    : jQuery('#mg_amount_btn_type').val()
				   },
			success: function(msg) {  
					saved('#mg_amount_settings');
			}
	 })  ; //ajax
	 
  });
  
  jQuery('#mg_save_misc_form_settings').click(function(){
  
	 jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : { 
			         action   : 'miglaA_update_metadata' , 
			         post_id  : jQuery('#mg_current_form').val() , 
					 meta_key : 'migla_warning_1',
					 meta_value    : jQuery('#mg-errorgeneral-default').val()
				   },
			success: function(msg) {  
			}
	 })  ; //ajax		

	 jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : { 
			         action   : 'miglaA_update_metadata' , 
			         post_id  : jQuery('#mg_current_form').val() , 
					 meta_key : 'migla_warning_2',
					 meta_value    : jQuery('#mg-erroremail-default').val()
				   },
			success: function(msg) {  
			}
	 })  ; //ajax

	 jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : { 
			         action   : 'miglaA_update_metadata' , 
			         post_id  : jQuery('#mg_current_form').val() , 
					 meta_key : 'migla_warning_3',
					 meta_value    : jQuery('#mg-erroramount-default').val()
				   },
			success: function(msg) {  
					saved('#mg_save_misc_form_settings');
			}
	 })  ; //ajax	 
  });

 jQuery('#miglaAddAmountButton').click(function(){
		var newVal   = jQuery('#miglaAddAmount').val();
		var newValue = newVal.replace( decSep , "." );
		var filterThousand = '/' + thouSep + '/g';
			newValue = newVal.replace( filterThousand, '');
		var newPerk  = jQuery('#miglaAmountPerk').val();
		  
		var amount_newline = "<p class='mg_amount_level'>";
		amount_newline = amount_newline + "<input class='mg_amount_level_value' type=hidden value='"+newValue+"' />";
		amount_newline = amount_newline + "<label>"+newVal+"</label>";	
		amount_newline = amount_newline + "<label class='mg_amount_level_perk'>"+newPerk+"</label>";		   
		amount_newline = amount_newline + "<button name='miglaAmounts' class='miglaRemoveLevel obutton'><i class='fa fa-times'></i></button>";
		amount_newline = amount_newline + "</p>";
			  		  		  
		  if( newVal == '' || Number(newVal) <= 0.0 || isNaN(newValue) )
		  { 
		     alert('please insert valid number for amount');
		  }else{
		  
		      jQuery(amount_newline).appendTo( jQuery('#miglaAmountTable') );
	
 		      var amount_levels_array = [];
			  
			  jQuery('.mg_amount_level').each(function(){
		          var amount_levels = {};	
			      var aValue           = jQuery(this).find('.mg_amount_level_value').val();
				  var aPerkValue       = jQuery(this).find('.mg_amount_level_perk').html();
				  amount_levels.amount = aValue ;
				  amount_levels.perk   = aPerkValue ;
				  amount_levels_array.push(amount_levels);
			  });
		  
		       //alert( JSON.stringify( amount_levels_array) );

			   jQuery.ajax({
					type : "post",
					url :  miglaAdminAjax.ajaxurl, 
					data : {
							 action   : 'miglaA_update_metadata' , 
							 post_id  : jQuery('#mg_current_form').val() , 
							 meta_key : 'migla_amounts',
							 meta_value    : amount_levels_array				
						   },
					success: function(msg) {
								jQuery('#warningEmptyAmounts').hide(); 	
								jQuery('#miglaAddAmount').val('');
								jQuery('#miglaAmountPerk').val('');
								remove(); 							
							}
			   }); //ajax
			   
		  }
  });
  
//*** FORM SETTINGS *************/  
jQuery('.miglaSaveForm').click(function() {

	var fields = []; var id = '#' + jQuery(this).attr('id'); 

	if(  isFieldValid() )
	{
	  fields = getFormStructure();
	  jQuery.ajax({
			type : "post",
			url  : miglaAdminAjax.ajaxurl, 
			data : { 
						action	: "miglaA_update_cform", 
						values	: fields , 
						changes	: changed_fields,
						formID  : jQuery('#mg_current_form').val()
					},
			success: function(msg) {                 
			   //alert( "done" );
			   saved( id );
			   clearLeftover(); 
			   getRidForbiddenChars();
		}
	  })  ; //ajax

	  jQuery.ajax({
		type 	: "post",
		url 	: miglaAdminAjax.ajaxurl, 
		data 	: {
					action: "miglaA_update_me", 
					key:'migla_custamounttext', 
					value:jQuery('#migla_custAmountTxt').val() 
					},
		success: function(msg) {                 
		}
	  })  ; //ajax

	}else{
		alert("No empty values or duplicate values");
		canceled(id);
	}
	
});

jQuery('.mAddGroup').click(function() {
   jQuery('#divAddGroup').toggle();
});

jQuery('#cancelAddGroup').click(function() {
   jQuery('#divAddGroup').toggle();
  jQuery('#labelNewGroup').val('');
});

jQuery('#saveAddGroup').click(function() {
   var title = jQuery('#labelNewGroup').val();
   var ulid = "";
   var newG = "";
   var idGroup = Number(jQuery('ul.containers').children('li').length) + 1;

//CHEK VALID/////////////////
  var isValid = true;
  var BreakException= {};

  try {    
      if( title == '' || findDuplicateTitle(  title ) > 0 ){
        isValid = false; throw BreakException;
      }
  } catch(e) {
    if (e!==BreakException) throw e;
  } 
////////////////////////////

if( isValid )
{
  newG = newG + "<li class='title formheader'>";
   newG = newG + "<div class='row'>";

   newG = newG + "<div class='col-sm-4'>";
   newG = newG + "<div class='row'>";
   newG = newG + "<div class='col-sm-2'> <i class='fa fa-bars bar-icon-styling'></i></div>";
  newG = newG + "<div class='col-sm-10'> ";
  newG = newG + "<input type='text' class='miglaNQ'  placeholder='"+title+"' name='grouptitle' value='"+title+"'> ";
  newG = newG + "</div>";
  newG = newG + "</div></div>";

  newG = newG + "<div class='col-sm-4'>";

  newG = newG + "<div class='col-sm-4 mg_addfield'><button value='add' class='btn btn-info obutton mAddField addfield-button-control' style='display:none'>";
  newG = newG + "<i class='fa fa-fw fa-plus-square-o'></i>Add Field</button></div>";
  

  newG = newG + "<div class='col-sm-5'>";

if( jQuery('#toggleNewGroup').is(':checked') ){
  newG = newG + "<input type='checkbox' id='t" + idGroup + "' class='toggle' checked='checked' /><label>Toggle</label>";
}else{
  newG = newG + "<input type='checkbox' id='t" + idGroup + "' class='toggle' /><label>Toggle</label>";
}

  newG = newG + "</div>";
newG = newG + "</div>";

  newG = newG + "<div class='col-sm-4 text-right-sm text-center-xs divDelGroup'>";
  newG = newG + "<button value='add' class='rbutton btn btn-danger mDeleteGroup pull-right'>";
  newG = newG + "<i class='fa fa-fw fa-trash'></i>Delete Group</button>";
  newG = newG + "</div>";

  newG = newG + "</div>";

  newG = newG + "<input type='hidden' name='title' value='"+title+"' />";
  newG = newG +"<input type='hidden' name='child' value='NULL' />";
  newG = newG +"<input type='hidden' name='parent_id' value='NULL' />";
  newG = newG +"<input type='hidden' name='depth' value='0' />";

  ulid = title.replace(" ", "");
  newG = newG + "<ul class='rows' id='"+ulid+"'>";

  newG = newG + "</ul>";
  newG = newG + "</li>";

  jQuery(newG).prependTo( jQuery('ul.containers') );
  jQuery('#labelNewGroup').val('');
  addField(); 
  deleteGroup(); 
  ohDrag();

  var fielddata = [];
  fielddata = getFormStructure(); 
  //alert( JSON.stringify(fielddata) );

   jQuery.ajax({
     type : "post",
     url : miglaAdminAjax.ajaxurl, 
     data : {
				action	: "miglaA_update_cform", 
				values	: fielddata , 
				changes	: changed_fields,
				formID  : jQuery('#mg_current_form').val()
			},	
     success: function(msg) {  
		  jQuery('#divAddGroup').toggle();
		  jQuery('.mAddField').show();
		  saved('#saveAddGroup');
		}
   })  ; //ajax

}else{
   alert("data can not be empty or duplicate title !");
   canceled('#saveAddGroup');
}

}); 


//** So this is to add values to multivalue field type(radio, drop down, checkbox) **// 
//add to list
 jQuery('#miglaAddCustomValueForm').click(function(){
   
    if( jQuery( '#mg_custom_list_container' ).find('.mg_custom_list_row'.length) <= 0 ){ jQuery( '#mg_custom_list_container' ).empty() };
    
    var content = "";
                    content = content + "<div class='mg_custom_list_row'>";

                    content = content + "<div class='form-group mg_custom_list'><div class='col-sm-3'><label class='control-label' for=''>Value</label></div>";
                    content = content + "<div class='col-sm-6'><input type='text'  value='" + jQuery('#mg_add_value').val();
                    content = content + "' class='mg_customlist_key form-control'></div>";
                    content = content + "<div class='col-sm-3'></div>";
                    content = content + "</div>";

                    content = content + "<div class='form-group mg_custom_list'><div class='col-sm-3'><label class='control-label' for=''>Label</label></div>";
                    content = content + "<div class='col-sm-6'><input type='text'  value='" + jQuery('#mg_add_label').val();
                    content = content + "' class='form-control touch-bottom mg_customlist_val'></div>";
                    content = content + "<div class='col-sm-3'><button class='mg_customlist_remove btn obutton alignleft'><i class='fa fa-fw fa-trash'></i> </button></div>";
                    content = content + "</div>";

                    content = content + "</div>";

    jQuery( content ).appendTo( '#mg_custom_list_container' ) ;
    mg_delete_custom_list();
    jQuery('#mg_add_value').val(''); jQuery('#mg_add_label').val('');

 });
 
   jQuery('#miglaAddCustomValues').click(function(){
     mg_save_custom_list('');
     jQuery('#mg_add_values').find('.close').trigger('click');  
   });
 
  jQuery(".edit_select_value").click(function(e){
        e.preventDefault();
		var parent = jQuery(this).closest('li.formfield');
        var recId  = "mgval_" + parent.find("input[name='uid']").val();

        jQuery("#mg_id_custom_values_edit").text("");
		jQuery("#mg_id_custom_values_edit").text(recId);
		jQuery('#mg_add_values').modal('show');
  });


 jQuery('#mg_add_values').on('show.bs.modal', function(e) {
  
   jQuery( '#mg_custom_list_container' ).empty(); //jQuery('#mg_add_val_load').show();

   //alert( jQuery('#mg_id_custom_values_edit').text() + jQuery('#migla_custom_values_id').val() );

    jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {
					action	: "miglaA_get_postmeta", 
					key		: jQuery('#mg_id_custom_values_edit').text(), 
					id		: jQuery('#mg_current_form').val() 
				},
        success: function( msg ) {  

          jQuery('#miglaAddCustomValues').removeAttr('disabled');

          if( msg == "-1" || msg == ""){

            jQuery('#mg_add_values').find('.fa-spinner').hide();
            jQuery( '#mg_custom_list_container' ).html('');

          }else{
             
              jQuery('#mg_add_values').find('.fa-spinner').hide();
              jQuery( '#mg_custom_list_container' ).html('');

              var content = "";
              var res = msg.split(";");

             for( key in res ){
               if( res[key] !== ''  ){
                  var res2 = res[key].split("::");
                  if( typeof res2[1] !== 'undefined' && res2[1] !== '' ){ 

                    content = content + "<div class='mg_custom_list_row'>";

                    content = content + "<div class='form-group mg_custom_list'><div class='col-sm-3'><label class='control-label' for=''>Value</label></div>";
                    content = content + "<div class='col-sm-6'><input type='text'  value='" + res2[0]
                    content = content + "' class='mg_customlist_key form-control touch-top'></div>";
                    content = content + "<div class='col-sm-3'></div>";
                    content = content + "</div>";

                    content = content + "<div class='form-group mg_custom_list'><div class='col-sm-3'><label class='control-label' for=''>Label</label></div>";
                    content = content + "<div class='col-sm-6'><input type='text'  value='" + res2[1];
                    content = content + "' class='form-control  mg_customlist_val'></div>";
                    content = content + "<div class='col-sm-3'><button class='mg_customlist_remove btn obutton alignleft'><i class='fa fa-fw fa-trash'></i> </button></div>";
                    content = content + "</div>";

                    content = content + "</div>";

                }
              }

             }

             jQuery( content ).appendTo( '#mg_custom_list_container' ) ;

             mg_delete_custom_list();

          }

        }
   }); //ajax

 });
 
 
 jQuery('#miglaRestore').click(function(){
	 jQuery.ajax({
		type : "post",
		url : miglaAdminAjax.ajaxurl, 
		data : {
					action	: "miglaA_reset_cform",
					formID  : jQuery('#mg_current_form').val()
				},
		success: function(msg) {
			  //alert( msg );  
		  location.reload(true);
			  clearLeftover();
			  getRidForbiddenChars();
		}
	 })  ; //ajax	   
});

	jQuery('#mHideHideCustomCheck').click(function(){
		if( jQuery(this).is(':checked') ){
			jQuery('#mg_div_custom_amount_text').hide();
		}else{
			jQuery('#mg_div_custom_amount_text').show();		
		}
	});
 
}); //Document

function ohCampaignDrag(){
  jQuery(".mg_campaign_list").sortable({
		helper		: "clone",
		revert		: true,
		forcePlaceholderSize: true,
		axis		: 'y',
		start: function (e, ui) {
			save_radio_status();
		},
		update: function (e, ui) {			
		},
		stop: function(e, ui){
		},
		received: function(e, ui){
		}
   }).bind('sortstop', function (event, ui) {
	   
	//alert( JSON.stringify(radioState2) );   
     var i = 0;
     jQuery(".mg_campaign_list").find('input[type="radio"]').each(function() {
		for( i = 0; i < radioState2.length ; i = i + 1 )
		{	
			var temp = radioState2[i];
			if( temp.name == jQuery(this).attr('name') )
			{
				if( temp.state === jQuery(this).val() )
					jQuery(this).prop('checked', true);
			}
		}
     });
	 
	 save_the_campaigns();
  });
}

function save_radio_status()
{
	radioState2.length = 0;
			jQuery(".mg_campaign_list").find("input[type=radio]").each(function() {
				if( jQuery(this).is(':checked') ){
					var el = {};
					el.name 	= jQuery(this).attr('name');
					el.state 	= jQuery(this).val();
					radioState2.push(el);
				}
			});		
}

function save_the_campaigns()
{
		   var empty_count = 0; 
		   var array_name = []; 
		   var duplicate_count = 0;
		   
		   jQuery('li.formfield').each(function(){
			   var campaign_name = jQuery(this).find('.labelChange').val();
			   
			   if( array_name.indexOf(campaign_name) > -1 )
			   {
				  duplicate_count = duplicate_count + 1;
			   }else{
				 array_name.push( campaign_name );	   
			   }
			   
			   if( jQuery.trim(campaign_name) == '' ){
				  empty_count = empty_count + 1;
			   }
		   });

		   if( empty_count > 0 ){
			   alert('No empty campaign is allowed');
			   canceled( '#miglaSaveCampaign' );
		   }else{
			 
			 if( duplicate_count > 0 )
			 {
					 alert('No duplicate name is allowed');
					 canceled( '#miglaSaveCampaign' );
			 }else{
				 
				var list = getCampaignStructure();  
				 //alert( JSON.stringify(updatedList) ); 

				 jQuery.ajax({
					type : "post",
					url :  miglaAdminAjax.ajaxurl, 
					data : { 
							 action: "miglaA_save_campaign", 
							 values: list, 
							 update: updatedList
						   },
					success: function(msg) {  
							labelChanged(); 
							targetChanged();
					}
				 })  ; //ajax
						 
			  }
		   }
}