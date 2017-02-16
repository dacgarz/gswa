var dataArr;
var inQuery;
var ajaxPlanData = {}; 
var oPlanTable; 
var removeList = new Array();
var removeMessage = new Array();
var removeRow = new Array();
var displayID = new Array();
var before = ''; 
var after = '';
var thouSep = ''; 
var decSep = ''; 
var showDec;
var allAmount = 0;


function doRefresh( _data, _table) {
        var oTable = jQuery( _table ).dataTable();
        oTable.fnClearTable();
        oTable.fnAddData( _data );
        oTable.fnDraw();
}

function mPlanTable( x ){
   var table = jQuery('#miglaStripePlanTable').DataTable(
      {
      "scrollX": true ,
      "data": x,
      "columns" : [           
            {
                "class":          'details-control sorting_disable',
                "orderable":      false,
                "orderable":      false,
                "data":           'detail',
                "defaultContent": ''                
            },            
            { "data": 'created' , sDefaultContent: "" },
            { "data": 'planid', sDefaultContent: "" },
            { "data": 'name' , sDefaultContent: ""},
            { "data": function ( row, type, val, meta ) {
                        return (row.interval_count + " " + row.interval + "(s)");
                      } 
            },
            { "data": 'amount' , sDefaultContent: ""},
            { "data": 'id' }
            ],
        "columnDefs": [            
                { "targets": [ 0 ], "searchable": false},
                { "targets": [ 1 ], "searchable": false},
                { "targets": [ 6 ], "visible": false}
         ],
        "createdRow": function ( row, data, index ) {
           },
"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay )
       {       
                    },
        "language": {
			 "lengthMenu": '<label>Show  Entries<select>'+
			  '<option value="10">10</option>'+
			 '<option value="20">20</option>'+
			 '<option value="30">30</option>'+
			 '<option value="40">40</option>'+
			 '<option value="50">50</option>'+
			 '<option value="-1">All</option>'+
			 '</select></label>'
	},
       "fnDrawCallback": function( oSettings ) {
         var rows = jQuery('#miglaReportTable').dataTable().fnGetNodes();
         for(var i=0;i<rows.length;i++)
         {
           var r = rows[i];
           jQuery(r).removeClass('shown');
         }
        }
      });
         
  jQuery('th.detailsHeader').removeClass('sorting_asc');
  jQuery('th.detailsHeader').removeClass('sorting_desc');  
  jQuery('th.removeColumn').removeClass('sorting_asc');
  jQuery('th.removeColumn').removeClass('sorting_desc');  
   
    return table;
}


function getIndex( id, ajaxData ){
  var idx = 0;
  for( var i = 0; i < ajaxData.length; i++)
  {  
    if( Number(id) == Number(ajaxData[i]['id']) ){
        idx = i;
    }
  }
  return idx;
}


function getData( data, val){
  var value = "";
  for ( key in data ) {
     if(  key == val ){ value = data[key]; }
  } 
  return value; 
}

function format( d, idx  ) {
    var check = 0;
    var str = '';

    str = str + '<tr class="det"><td colspan="9">';
    str = str + '<div class="col-sm-6">';
    str = str + '<table class="table-hover" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';

  var row = d[idx];
  for(key in row)
  {
    if( key == 'detail' ){
    }else{
      str = str + "<tr>";
      str = str + "<td>" + key + "</td>";
      str = str + "<td>" + row[key] + "</td>";
      str = str + "</tr>";
    }
  }

  str = str +  '</table>';
  str = str +  '</div>';
  str = str + '</td>';
  str = str + '</tr>';

            
  return str;
}


function findWithAttr(array, attr, value) {
  var out = []; out[0] = ""; out[1] = false;
    for(var i = 0; i < array.length; i += 1) {
        if(array[i][attr] === value) {
            var r = "";
            r = r + "<tr><td width=''>"+array[i]['miglad_date']+"</td><td width='' align='center'>";
            r = r + array[i]['miglad_firstname']+"</td><td width=''>";
            r = r + array[i]['miglad_lastname']+"</td>";

            var status = "One time donation";
            if( array[i]['miglad_transactionType'] == 'subscr_payment' ){ status = "Recurring Payment";  out[1] = true; }

            r = r + "<td>" + status +"</td>";
            r = r + "<td width=''>"+array[i]['miglad_amount']+"</td>";
            r = r + "</tr>";

            out[0] = r;
            
            return out;
        }
    }
}

function retrievePlans(){
 jQuery.ajax({
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : {action: "miglaA_stripe_getPlan"
                       },
	success: function(msg) {  
                   var output = eval(msg);
                   ajaxPlanData = output[0];

            jQuery('#miglaStripePlanTable tfoot th').each( function () {
               var title = jQuery('#miglaStripePlanTable thead th').eq( (jQuery(this).index() ) ).text();
               if ( title == 'Detail' || title == 'Created' ) 
               {
                 jQuery(this).html( '<input type="hidden" />' );
               }else{
                 jQuery(this).html( '<input class="mg_footer" type="text" placeholder="'+title+'" name="'+title+'" />' );
               }
             }); //

                   oPlanTable = mPlanTable( ajaxPlanData );

        //Search on footer
         jQuery( '.mg_footer' ).on( 'keyup change', function () {
              var p = jQuery(this).parent();
              var col = p.attr("id");
              col = col.slice(1);
              //alert(col);
              jQuery('#miglaStripePlanTable').DataTable().column( col ).search(
                 jQuery(this).val(),
                 jQuery(this).prop('checked'),
                 jQuery(this).prop('checked')
              ).draw(); 

        });

      jQuery('#miglaStripePlanTable tbody').delegate("td", "click", function (){

          var rows = jQuery( '#miglaStripePlanTable' ).dataTable().fnGetNodes();
          for(var i=0;i<rows.length;i++)
         {
              var r = rows[i];
               jQuery(r).removeClass('selectedrow');
          }

       var tr = jQuery(this).closest('tr');
        if ( tr.hasClass('selectedrow') ) {
            tr.removeClass('selectedrow');
        }
        else {
            tr.addClass('selectedrow');
        }

        if ( jQuery(this).hasClass('removeColumn') )
        {
           var parent = jQuery(this).closest('tr');
           var him = jQuery(this).find('.removeRow');
           var name = him.attr('name');

           if( jQuery(parent).hasClass('removed') ){
             removeList.remove(name); 
             jQuery(parent).closest("tr").removeClass('pink-highlight');
             jQuery(parent).removeClass('removed'); 
         
           }else {
             removeList.push( name );
             jQuery(parent).closest("tr").addClass('pink-highlight');
             jQuery(parent).addClass('removed'); 
           }

        }

        if( jQuery(this).hasClass('details-control') )
        {
           var tr = jQuery(this).closest('tr');
           var tt = jQuery(this).next();
           var aData = oPlanTable.cell('.selectedrow', 6).data();

           if( tr.hasClass('shown') )
           {
              tr.removeClass('shown');
              var n = tr.next();
              n.remove();         
           }else{
              tr.addClass('shown');
              jQuery( format( ajaxPlanData, getIndex( aData, ajaxPlanData ) ) ).insertAfter(tr);
          
           }   
        } 
     });

      jQuery('.sorting').click(function(){
        var n = jQuery('tr.det');
        var m = jQuery('.shown');
        m.removeClass('shown');
        n.remove();
      })
 
      jQuery('th.detailsHeader').removeClass('sorting_asc');
      jQuery('th.detailsHeader').removeClass('sorting_desc');   

	} //success
  })  ; //ajax	 
}


jQuery(document).ready( function() {

  ohDrag();

//////////////////////////////////////////////// STRIPE /////////////////////////////////////////////////////////////////////////////////
  jQuery('#mg_save_section1').click(function(){

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
     info = ['tab_header',      cleanIt( jQuery('#mg_stripe-tab').val() ) ] ; cc_info_array.push(info);
     info = ['name_label',      cleanIt( jQuery('#mg_name-stripe').val() ) ] ; cc_info_array.push(info);
     info = ['name_placeholder', cleanIt( jQuery('#mg_placeholder-name').val() ) ] ; cc_info_array.push(info);
     info = ['number_label',       cleanIt( jQuery('#mg_cardnumber-stripe').val() ) ] ; cc_info_array.push(info);
     info = ['number_placeholder', cleanIt( jQuery('#mg_placeholder-card').val() ) ] ; cc_info_array.push(info);
     info = ['cvc_label' ,        cleanIt( jQuery('#mg_cvc-stripe').val() ) ] ; cc_info_array.push(info);
     info = ['cvc_placeholder' , cleanIt( jQuery('#mg_placeholder-CVC').val() ) ] ; cc_info_array.push(info);

     jQuery.ajax({
         type : "post",
         url :  miglaAdminAjax.ajaxurl, 
         data : {action: "miglaA_update_me", key:'migla_stripe_cc_info', value:cc_info_array },
         success: function(msg) {  
            saved('#mg_save_section1');
         }
     }); //ajax

  });


 jQuery('#miglaSaveCCInfo').click(function(){

     jQuery.ajax({
         type : "post",
         url :  miglaAdminAjax.ajaxurl, 
         data : {  action : 'miglaA_update_me' , key:'migla_wait_stripe', value : jQuery('#mg_waiting_stripe').val()  },
         success: function(msg) { 
         }
     }); //ajax

     var info = {}; var cc_info_array = [];
     info = ['tab_header',      cleanIt( jQuery('#mg_stripe-tab').val() ) ] ; cc_info_array.push(info);
     info = ['name_label',      cleanIt( jQuery('#mg_name-stripe').val() ) ] ; cc_info_array.push(info);
     info = ['name_placeholder', cleanIt( jQuery('#mg_placeholder-name').val() ) ] ; cc_info_array.push(info);
     info = ['number_label',       cleanIt( jQuery('#mg_cardnumber-stripe').val() ) ] ; cc_info_array.push(info);
     info = ['number_placeholder', cleanIt( jQuery('#mg_placeholder-card').val() ) ] ; cc_info_array.push(info);
     info = ['cvc_label' ,        cleanIt( jQuery('#mg_cvc-stripe').val() ) ] ; cc_info_array.push(info);
     info = ['cvc_placeholder' , cleanIt( jQuery('#mg_placeholder-CVC').val() ) ] ; cc_info_array.push(info);

     jQuery.ajax({
         type : "post",
         url :  miglaAdminAjax.ajaxurl, 
         data : {action: "miglaA_update_me", key:'migla_stripe_cc_info', value:cc_info_array },
         success: function(msg) {  
            saved('#miglaSaveCCInfo');
         }
     }); //ajax
 });


 jQuery('#miglaUpdateStripeKeys').click(function(){
     var mg_liveSK = jQuery.trim(jQuery('#migla_liveSK').val());
     var mg_livePK = jQuery.trim(jQuery('#migla_livePK').val());
     var mg_testSK = jQuery.trim(jQuery('#migla_testSK').val());
     var mg_testPK = jQuery.trim(jQuery('#migla_testPK').val());
 
	  jQuery.ajax({
		type : "post",
		url : miglaAdminAjax.ajaxurl, 
		data : { action : "miglaA_update_me", key:'migla_liveSK', 
		         value  : mg_liveSK  
               },
		success: function(msg) {  
             jQuery('#migla_liveSK').val(mg_liveSK);       
		}
	  })  ; //ajax	

	  jQuery.ajax({
		type : "post",
		url : miglaAdminAjax.ajaxurl, 
		data : {  action: "miglaA_update_me", key:'migla_livePK', 
		          value : mg_livePK 
                },
		success: function(msg) { 
               jQuery('#migla_livePK').val(mg_livePK);		
		}
	  })  ; //ajax	 

	  jQuery.ajax({
		type : "post",
		url : miglaAdminAjax.ajaxurl, 
		data : { action : "miglaA_update_me", key:'migla_testSK', 
		         value  : mg_testSK
                       },
		success: function(msg) {    
               jQuery('#migla_testSK').val(mg_testSK);		
		}
	  })  ; //ajax	

	  jQuery.ajax({
		type : "post",
		url : miglaAdminAjax.ajaxurl, 
		data : { action : "miglaA_update_me", key:'migla_testPK', 
		         value  : mg_testPK 
                       },
		success: function(msg) {   
	            jQuery('#migla_testPK').val(mg_testPK);	
		}
	  })  ; //ajax	
	  
		  
	  jQuery.ajax({
		type : "post",
		url : miglaAdminAjax.ajaxurl, 
		data : { action : "miglaA_update_me", key:'migla_stripemode', 
		         value  : jQuery("input[name='miglaStripe']:checked").val() 
                       },
		success: function(msg) {  
                       saved('#miglaUpdateStripeKeys');  
		}
	  })  ; //ajax	  

 });


 jQuery('#miglaAddPlan').click(function(){


	  jQuery.ajax({
		type : "post",
		url : miglaAdminAjax.ajaxurl, 
		data : {action: "miglaA_stripe_addPlan", 
                         amount:jQuery('#migla_planAmount').val(),
                         interval: jQuery('#migla_planInterval').val(),
                         name: jQuery('#migla_planName').val(),
                         id: jQuery('#migla_planID').val()
                        },
		success: function(msg) {  
                   alert(msg);
                   saved('#miglaAddPlan');  
		}
	  })  ; //ajax	  

   jQuery.ajax({
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : {action: "miglaA_stripe_getPlan"
                       },
	success: function(msg) {  
                   var output = eval(msg);
                   ajaxPlanData = output[0];
                   if( ajaxPlanData == '' ){
               
                   }else{
                     doRefresh( ajaxPlanData , '#miglaStripePlanTable' );
                   }
        }
   });


 });


 //////////RETRIEVE PLANS
 retrievePlans();


///// Synchronize Plans
jQuery('#miglaSyncPlan').click(function(){
   jQuery.ajax({
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : {action: "miglaA_syncPlan"
                       },
	success: function(msg) {  
                   //alert(msg);
        }
   });

   jQuery.ajax({
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : {action: "miglaA_stripe_getPlan"
                       },
	success: function(msg) {  
                   var output = eval(msg);
                   ajaxPlanData = output[0];
                   if( ajaxPlanData == '' ){
               
                   }else{
                     doRefresh( ajaxPlanData , '#miglaStripePlanTable' );
                   }
        }
   });
   
});

//// stripe BUTTON CHOICE ////////////////////
jQuery('#miglaUploadstripeBtn').click(function() {
   formfield = jQuery('#mg_upload_image').attr('name');
   tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
   return false;
});
 
window.send_to_editor = function(html) {
   imgurl = jQuery('img',html).attr('src');
   jQuery('#mg_upload_image').val(imgurl);
   tb_remove();
}



jQuery('#miglaSavestripeBtnUrl').click(function(){


   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'miglaStripeButtonChoice', value:'imageUpload' },
        success: function(msg) {  
        }
   }); //ajax
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_stripebuttonurl', value:jQuery('#mg_upload_image').val() },
        success: function(msg) {  
          saved('#miglaSavestripeBtnUrl');
        }
   }); //ajax
});

jQuery('#miglaSavestripeButtonPicker').click(function(){
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'miglaStripeButtonChoice', value:'stripeButton' },
        success: function(msg) {  
        }
   }); //ajax

   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_stripebutton_text', value:jQuery('#stripeButtonText').val() },
        success: function(msg) {  
          saved('#miglaSavestripeButtonPicker');
        }
   }); //ajax
});


jQuery('#miglaCSSButtonPickerSave').click(function(){

   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'miglastripeButtonChoice', value:'cssButton' },
        success: function(msg) {  
        }
   }); //ajax
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_stripecssbtnstyle', value:jQuery('#mg_CSSButtonPicker').val() },
        success: function(msg) {  
        }
   }); //ajax
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_stripecssbtntext', value:jQuery('#mg_CSSButtonText').val() },
        success: function(msg) {  
        }
   }); //ajax
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_stripecssbtnclass', value:jQuery('#mg_CSSButtonClass').val()},
        success: function(msg) {  
          saved('#miglaCSSButtonPickerSave');
        }
   }); //ajax
});

//WebHook's URL
 jQuery('#mg_stripe_webhook_url').on('keypress', function(e){
       e.preventDefault(); 
 });

 jQuery('#mg_stripe_webhook_url2').on('keypress', function(e){
       e.preventDefault(); 
 });
 
 
   jQuery('#migla_security_save').click(function()
  {

                  var isVerifiySSL = 'no';
                  if( jQuery('#miglaStripeVerifySSL').is(':checked') ){ isVerifiySSL = 'yes'; }
		  jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_update_me", key:'migla_stripe_verifySSL', value: isVerifiySSL },
			success: function(msg) {                         
			}
		  })  ; //ajax	    

     var isStripeJS = 'no';
     if( jQuery('#migla_stripe_js').is(':checked') ){ 
			isStripeJS = 'yes'; 
	 }
	 jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : { action		: 'miglaA_update_me', 
						key		: 'migla_stripe_js', 
						value	: isStripeJS 
					},
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

   jQuery('#migla_use_captcha').click(function(){
      if( jQuery(this).is(':checked') )
         jQuery('.mg_captcha_keys').show();
      else
         jQuery('.mg_captcha_keys').hide();
   }); 
 
}); //DOC

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
