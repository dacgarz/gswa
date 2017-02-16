var dataArr;
var inQuery;
var ajaxData 		= {}; 
var ajaxData2		= {};

var undesignatedLabel;
 
var mg_recurring = {};
var mg_campaigns 	= {};
var mg_countries 	= {}; 
var mg_states 		= {}; 
var mg_provinces 	= {};

var removeList = new Array(); 
var removeMessage = new Array();
var removeRow = new Array();
var displayID = new Array();
var details_str = '';

var form_struct	;
var lines		;
var deleted_fields;
var customlist	;
var _clist 		;
var thedata		;	
var mg_form_id_send;
var mg_current_form_id;			

var mapKey = ['FirstName','LastName','campaign','Amount','Country','startdate','endddate'];
var mapFilter = ['','','','','','',''];
var before = ''; var after = '';
var thouSep = ''; var decSep = ''; var showDec;
var allAmount = 0;

function mdataTable( x ){
   var table;
   table = jQuery('#miglaReportTable').DataTable(
      {
      "scrollX": true ,
      "data": x,
      "columns" : [
            {
                "orderable":      false,
                "data":           'remove',
                "class" : 'removeColumn',
                "defaultContent": '',
                
            },            
            {
                "class":          'details-control sorting_disable',
                "orderable":      false,
                "orderable":      false,
                "data":           'detail',
                "defaultContent": ''                
            },            
            { "data": 'miglad_date' , sDefaultContent: "" },
            { "data": 'miglad_firstname', sDefaultContent: "" },
            { "data": 'miglad_lastname' , sDefaultContent: ""},
            { "data": function ( row, type, val, meta ) {
                        var campaign_name = row.miglad_campaign ;                
                        var rep            = /\[q\]/g;
                        campaign_name     = campaign_name.replace(rep, "'");
                        return campaign_name;
                     }
            },
            { "data": 'miglad_amount' , sDefaultContent: ""},
            { "data": 'miglad_country' , sDefaultContent: ""},
            { "data": function ( row, type, val, meta ) {
                        var r = row.miglad_transactionType ;
						if( r == 'web_accept' ){
						    r = 'One time (Paypal)';
						}else if( r == 'subscr_payment' ){
						    r = 'Recurring (Paypal)';
						}
                  		r = r + " <i class='fa fa-check-circle'></i>";
                        if( row.miglad_charge_dispute == 'dispute' ||  row.miglad_status == 'Warning' )
						{ 
							r = row.miglad_transactionType + " <i class='fa fa-exclamation-triangle'></i>"; 
						}
						
                        return r;
                     }
            },
            { "data": 'id' }
            ],
        "columnDefs": [            
                { "targets": [ 1 ], "searchable": false}
                ,
                { "targets": [ 9 ], "visible": false}
         ],
        "createdRow": function ( row, data, index ) {
           },
"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay )
       {
                        /* Calculate the market share for browsers on this page  */
                        var iPage = 0;  displayID.length = 0;
                        for ( var i=0 ; i<aiDisplay.length ; i++ )
                        {
                            iPage += Number( aaData[ aiDisplay[i] ]['miglad_amount'] );
                            displayID.push( aaData[ aiDisplay[i] ]['id'] );
                            //alert( JSON.stringify(displayID) );
                        }
						
document.getElementById("miglaOnTotalAmount2").innerHTML =  before +" "+ iPage.formatMoney(showDec, thouSep , decSep  ) + after; 

  
                         
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
function mg_export(){
   jQuery('#miglaExportAll').click(function(){
      alert("this might take a while if your dataset is large");
      jQuery("input[name='miglaFilters']").val("");
      jQuery('#miglaExportTable').submit();
   });
   
  jQuery('#exportTableJS').click(function(){
  
		var date = new Date(); 
		var filename = "online" + String( date.getFullYear() ) + String( date.getDate() ) + String( date.getHours() ) ;
           filename = filename + String( date.getMinutes() ) + String( date.getSeconds() ) + ".csv";
		var csv = "";

       var colDelim = '","';
       var rowDelim = '"\r\n"';
       var countrow = 0;

       csv = csv + '"';
       
	   for(key in ajaxData2[0])
	   {      
			csv = csv + key + colDelim;       
       }
       
       csv = csv + rowDelim;

       for(row in ajaxData2 ){
         for(key in ajaxData2[row])
		 {
		 
				var tempvalue = new String(ajaxData2[row][key]);
				
				if( countrow < ajaxData2.length ){
					csv = csv + tempvalue.replace(/,/g, ";") + colDelim;
				}else{
					csv = csv + tempvalue.replace(/,/g, ";")  + '"';
				}

         }
         csv = csv + rowDelim;
         countrow = countrow + 1;
       }
		
       var csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);

       jQuery('#exportTableJS')
            .attr({
				'download': filename,
                'href': csvData,
                'target': '_blank'
        });
	 	
  });  

  jQuery('#exportTable').click(function()
  {
       //alert(JSON.stringify(displayID));
	   var _filtered_ajaxData = [];
  	   
	   var i = 0; 
	   for( filtered_key in displayID )
	   {
	        for(row in ajaxData2 )
			{
			    if( ajaxData2[row]['id'] == displayID[filtered_key] )
				{
				   
				   var temp = {};
				   for(key in ajaxData2[row])
				   {
						if( key == ('uid') || key == ('remove') || key == ('detail') )
						{
						}else{
							temp[key] =  ajaxData2[row][key];
						}
				   }
				   _filtered_ajaxData.push(temp);			   
				}
			}
			i = i + 1;
	   }
	   
	   //alert( JSON.stringify(_filtered_ajaxData) );

       var date = new Date(); 
       var filename = "online" + String( date.getFullYear() ) + String( date.getDate() ) + String( date.getHours() ) ;
           filename = filename + String( date.getMinutes() ) + String( date.getSeconds() ) + ".csv";
       var csv = "";

       var colDelim = '","';
       var rowDelim = '"\r\n"';
       var countrow = 0;

       csv = csv + '"';
       
       for(key2 in _filtered_ajaxData[0]){
         if( key == ('uid') || key == ('remove') || key == ('detail') )
         {
         }else{
                 csv = csv + key2 + colDelim;
         }
       }
       
       csv = csv + rowDelim;

       for(row2 in _filtered_ajaxData )
	   {
         for(key2 in _filtered_ajaxData[row2])
		 {
           if( key == ('uid') || key == ('remove') || key == ('detail') )
           {
           }else{
				var tempvalue = new String( _filtered_ajaxData[row2][key2] );
				if( countrow < _filtered_ajaxData.length )
				{
					csv = csv + tempvalue.replace(/,/g, ";") + colDelim;
				}else{
					csv = csv + tempvalue.replace(/,/g, ";") + '"';
				}
           }
         }
         csv = csv + rowDelim;
         countrow = countrow + 1;
       }

       var csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);

       jQuery(this)
            .attr({
            'download': filename,
                'href': csvData,
                'target': '_blank'
        });
	   
  });
  
  
   
}

function getIndex( id ){
  var idx = 0;
  for( var i = 0; i < ajaxData.length; i++)
  {  
    if( Number(id) == Number(ajaxData[i]['id']) ){
        idx = i;
    }
  }
  return idx;
}

function celClick(){

          var rows = jQuery('#miglaReportTable').dataTable().fnGetNodes();
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
         var aData = oTable.cell('.selectedrow', 9).data();
         //alert( getIndex( aData ) );

         if( tr.hasClass('shown') )
         {         
			  tr.removeClass('shown');
			  jQuery( '#det_' + aData ).remove();
			  //var rec_detail 		= tr.next();
			  //var recurring_detail	= rec_detail.next();
			  //rec_detail .remove();
			  //recurring_detail.remove();
			  
         }else{
				tr.addClass('shown');
				var details_idx	= getIndex( aData );
				mg_detail_format( details_idx, aData , tr , ajaxData[details_idx]['miglad_form_id'] ) ; 	  			  
         }   
  } 
}

function mg_clear( str )
{
    var new_str = '';
	new_str		= str.replace( /\[q\]/g , "'" );
	return new_str;
}

function mg_undefined( str )
{
	var new_str = '';
	if ( typeof str === 'undefined' ){
		return new_str;
	}else{
		return str;
	}
}
		
function mg_decode( str )
{
    var new_str = '';
	new_str		= str.replace( /'/g , "[q]" );
	return new_str;
}

function mg_decode_apothese()
{
	jQuery('.mg_field_edit_text').each(function(){
		var first_val	= jQuery(this).val();
		jQuery(this).val( mg_clear(first_val) );
	});
}

function mg_detail_load ( pid ) 
{
    var str = '';
    str = str + '<tr class="det" id="det_'+pid+'" colspan="9"><td colspan="9">';
    str = str + '<div class="col-sm-6">';
    str = str + '<table class="table-hover" cellpadding="5" cellspacing="0" border="0">';
    str = str + '<tr><td>'+jQuery('#mg_load_image').html()+'</td></tr>';
	str = str + '</table></div></td></tr>'; 
    return str;	
}

function mg_detail_format( idx, pid, tr, fid) 
{
	jQuery( mg_detail_load ( pid ) ).insertAfter(tr);
	
 jQuery.ajax({
   type 	: "post",
   url 		:  miglaAdminAjax.ajaxurl,  
   data 	:  { 
					action	: 'miglaA_detail_report' ,
					post_id	: pid,
					form_id	: fid
				},
   success	: function( detail_report ) 
     {
	    retrieve 	= JSON.parse(detail_report);
		form_struct	= retrieve[0] ;
        lines		= retrieve[1];
		customlist	= retrieve[2];
		_clist 		= [];
		thedata		= [];		
		
		mg_current_form_id	= lines['miglad_form_id'];
		
		var str = '';
		str = str + '<tr class="det" id="det_'+pid+'" colspan="9"><td colspan="9">';
		str = str + '<div class="col-sm-6">';
		str = str + '<table class="table-hover" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
		
		mg_subscription_amount = lines['miglad_amount'];
		
		for( key1 in form_struct )
		{
			var str_child = '';
			var count = 0;
			var children = form_struct[key1]['child'];
			
			for( child in children )
			{
				var f_label	= children[child]['label'];
				var f_code	= children[child]['code'];
				var f_id	= children[child]['id'];
				var line_id	= f_code + f_id;
				
				if( lines[line_id] == '' || typeof lines[line_id] === 'undefined' ){
				
				}else{
					
					if( line_id  == 'miglad_country' )
					{
						str_child 	= str_child + '<tr><td>'+ mg_clear( f_label ) +' : </td><td>'+ lines[line_id] +'</td></tr>';
						
						if( lines[line_id] == 'Canada' )
						{
							str_child 	= str_child + '<tr><td>Province : </td><td>'+ lines['miglad_province'] +'</td></tr>';
							thedata['miglad_province']	= mg_undefined( lines['miglad_province'] );
							lines['miglad_province']	= 'marked_migla';
							
						}else if( lines[line_id] == 'United States' )
						{
							str_child 	= str_child + '<tr><td>State : </td><td>'+ lines['miglad_state'] +'</td></tr>';
							thedata['miglad_state']		= mg_undefined( lines['miglad_state'] );
							lines['miglad_state']		= 'marked_migla';							
						}
						
						count 		= count + 1;
						thedata[line_id]	= lines[line_id];
						lines[line_id] 		= 'marked_migla';	
						
					}else{
						str_child 	= str_child + '<tr><td>'+ mg_clear( f_label ) +' : </td><td>'+ mg_clear( lines[line_id] ) +'</td></tr>';
						count 		= count + 1;
						thedata[line_id]	= lines[line_id];
						lines[line_id] 		= 'marked_migla';
					}
				}	
				
			}
			
			if( count > 0 ){
				str = str + "<tr class='reportGroupHeader'>" ;
				str = str + "<td  colspan='2'>" + mg_clear(	form_struct[key1]['title']	) + " " + "</td>"+"</tr>";
				str = str + str_child;
			}
		}
		
		var str_payment = '';
		str_payment = str_payment + '<tr><td> Session ID :</td><td>'+ lines['miglad_session_id'] +'</td></tr>';
			lines['miglad_session_id']	= 'marked_migla';
			lines['miglad_session_id_']	= 'marked_migla';	
			
		str_payment = str_payment + '<tr><td> Transaction Type :</td><td>'+ lines['miglad_transactionType'] +'</td></tr>';
			lines['miglad_transactionType']	= 'marked_migla';
			
		if( lines['miglad_transactionId'] != '' && typeof lines['miglad_transactionId'] !== 'undefined' ){		
			str_payment = str_payment + '<tr><td> Transaction ID :</td><td>'+ lines['miglad_transactionId'] +'</td></tr>';
			lines['miglad_transactionId']	= 'marked_migla';
		}	
		str_payment = str_payment + '<tr><td> Date :</td><td>'+ lines['miglad_date'] + ' at ' + lines['miglad_time'] +' in ';
		str_payment = str_payment + lines['miglad_timezone'] + '</td></tr>';
			lines['miglad_date']	= 'marked_migla';
			lines['miglad_time']	= 'marked_migla';
            lines['miglad_timezone']	= 'marked_migla';
		lines['miglad_preference']	= 'marked_migla';	
		
		if( lines['miglad_paymentmethod'] != '' && typeof lines['miglad_paymentmethod'] !== 'undefined' ){	
			str_payment = str_payment + '<tr><td> Method :</td><td>'+ lines['miglad_paymentmethod'] +'</td></tr>';
			lines['miglad_paymentmethod'] = 'marked_migla';			
		}
		if( lines['miglad_status'] != '' && typeof lines['miglad_status'] !== 'undefined'){	
			str_payment = str_payment + '<tr><td>Status :</td><td>'+ lines['miglad_status'] +'</td></tr>';
			lines['miglad_status'] = 'marked_migla';			
		}		
		if( lines['miglad_dispute'] != '' && typeof lines['miglad_dispute'] !== 'undefined'){	
			str_payment = str_payment + '<tr><td>Dispute :</td><td>'+ lines['miglad_dispute'] +'</td></tr>';
			lines['miglad_dispute'] = 'marked_migla';			
		}	
		if( lines['miglad_avs_response_text'] != '' && typeof lines['miglad_avs_response_text'] !== 'undefined'){	
			str_payment = str_payment + '<tr><td>AVS response text :</td><td>[Code:'+ lines['miglad_avs_response_code'] + '] ' + lines['miglad_avs_response_text'] +'</td></tr>';
			lines['miglad_avs_response_text'] = 'marked_migla';	
		        lines['miglad_avs_response_code'] = 'marked_migla';	
		}		
		
		var mg_subscription_id = '';
		if( typeof lines['miglad_subscription_id'] !== 'undefined' && lines['miglad_subscription_id'] != '' )
		{
			mg_subscription_id = lines['miglad_subscription_id'];
			str_payment = str_payment + '<tr><td> Subscription :</td><td>'+ lines['miglad_subscription_id'] +'</td></tr>';
			lines['miglad_subscription_id']	= 'marked_migla';	
		}
		if( typeof lines['miglad_form_id'] !== 'undefined' && lines['miglad_form_id'] != '' ){
			str_payment = str_payment + '<tr><td> Form ID :</td><td>'+ lines['miglad_form_id'] +'</td></tr>';
			lines['miglad_form_id']	= 'marked_migla';	
		}
		
		var str_custom 		= '';
		var count_custom 	= 0;
		deleted_fields		= [];
		
		for( key in lines)
		{
		    if( lines[key] != 'marked_migla' && key.substr(0, 7) == 'miglac_' && key != '' )
		    //if( lines[key] != 'marked_migla' && key != '' )
			{
				str_custom 		= str_custom + '<tr><td>'+ mg_clear(key.substr( 7 )) + '</td><td>'+ mg_clear(lines[key]) +'</td></tr>';
				deleted_fields[count_custom] = [ key , lines[key] ];	
				count_custom 	= count_custom + 1;
			}
		}

		if( count_custom > 0 )
		{
			str = str + "<tr class='reportGroupHeader'>" ;
			str = str + "<td colspan='2'>Custom Fields " + "</td>"+"</tr>";		
			str = str + str_custom;
		}
		str = str + "<tr class='reportGroupHeader'>" ;
		str = str + "<td colspan='2'>Payment Info " + "</td>"+"</tr>";		
		str = str + str_payment;

		// str = str + "<tr class='reportGroupHeader tr_edit tr_edit"+pid+"'><td></td><td>" ;
		str = str + '<tr class="reportGroupHeader"><td><a name="mg_'+ pid + '_' + idx + '_' + fid + '" title="Edit this record" class="mg_editrecord btn btn-primary obutton" href="#mg-edit-record">Edit this record (ID:'
		str = str + pid +')</a></td>';
		str = str + '<td class="col-sm-3" ><label class="mg_edit_link_img mg_load'+pid+'" style="display:none">'+jQuery('#mg_load_image').html()+'</label></td></tr>';
		str = str + "</td></tr>";
				
		str = str + '</table></div>';
		
		
		if( mg_subscription_id != '' && typeof mg_subscription_id !== 'undefined' )
		{	
		var total_amount = 0;
		
		str = str + '<div class="col-sm-6" id="recdet_'+pid+'">';
		str = str + '<table class="table-hover" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';			
		
	
			str = str + "<tr class='reportGroupHeader mg_retrieve_recurring_header' id='tr_recurring_" + mg_subscription_id + "'>" ;
			str = str + "<input type='hidden' value='" + mg_subscription_id + "' class='mg_hidden_subscr_id'>";
			str = str + "<input type='hidden' value='" + mg_subscription_amount + "' class='mg_hidden_subscr_amount'>";
			str = str + "<td><a class=' mg_retrieve_recurring btn btn-primary obutton' aria-expanded='true'><i class='fa fa-caret-down'></i> Retrieve Reoccurring Data (Sub:" + mg_subscription_id + ")</a></td>";
			str = str + "<td><button class='btn rbutton mg_rec_close" + mg_subscription_id + "' style='display:none;'>X Close</button></td>"+"</tr>";							
		
		str = str + '</table></div></td>';
		str = str +'</tr>';	
                str = str +'</td></tr>';
		}		
		
		jQuery('#det_'+pid).remove();
		
		jQuery( str  ).insertAfter(tr);
				 
         mg_edit_record();		 
		 mg_edit_record_modal();	
		 mg_retrieve_recurring();
	 }
});	 

}

function mg_retrieve_recurring(){
	jQuery('.mg_retrieve_recurring').click(function()
	{
		var his_parent 	= jQuery(this).closest('tr');
		var sub_id 		= his_parent.find('.mg_hidden_subscr_id').val();
		var sub_amount 	= his_parent.find('.mg_hidden_subscr_amount').val();
						
		if( jQuery( '.mg_rec_detail_' + sub_id ).length > 0 )
		{	
			//Do Nothing
		}else{	
				 jQuery.ajax({
				   type 	: "post",
				   url 		:  miglaAdminAjax.ajaxurl,  
				   data 	:  { 
									action		: 'miglaA_detail_recurring' ,
									subscr_id	: sub_id
								},
				   success	: function( detail_rec ) 
					{	
						var det_rec	= JSON.parse( detail_rec );
						//alert(JSON.stringify(det_rec));
					
						var str = '';
						str = str + '<tr class="reportGroupHeader mg_rec_detail_'+sub_id+'"><td>Date Time</td><td>Amount</td></tr>';
						
						for(rec_keys in det_rec)
						{
							if( typeof rec_keys !== 'undefined' && det_rec != '' && typeof det_rec[rec_keys]['date'] !== 'undefined' )
							{
								str = str + '<tr class="mg_rec_detail_'+sub_id+'"><td>' + det_rec[rec_keys]['date'] + ' : ' + det_rec[rec_keys]['time'] ;
								str = str + '<td>' +  sub_amount + '</td></tr>';
							}
						}
						
						jQuery(str).insertAfter( '#tr_recurring_' + sub_id );
						his_parent.find('.mg_rec_close' + sub_id ).show();
						mg_close_recurring( sub_id );
					}
				});		

		}
	});
}

function mg_close_recurring( sub_id )
{
	jQuery('.mg_rec_close' + sub_id ).click(function(){
		var his_parent 	= jQuery(this).closest('tr');
		jQuery( ('.mg_rec_detail_' + sub_id ) ).remove();
		jQuery(this).hide();
	});
}

function getData( data, val){
  var value = "";
  for ( key in data ) {
     if(  key == val ){ value = data[key]; }
  } 
  return value; 
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
            var trans  = new String(array[i]['miglad_transactionType']);
            if( trans == 'subscr_payment' ||  trans == 'Recurring (Paypal)' || trans == "Recurring (Stripe)"
				|| trans == "Recurring (Authorize.NET)" )
            { 
               status = "Recurring Payment";  
			   out[1] = true; 
            }

            r = r + "<td>" + status +"</td>";
            r = r + "<td width=''>"+array[i]['miglad_amount']+"</td>";
            r = r + "</tr>";

            out[0] = r;
            
            return out;
        }
    }
}
  
function isValid(){
  var isVal = true;
  jQuery('input.required').each(function(){
     if( jQuery(this).val() == '' ){
       jQuery(this).addClass('pink-highlight'); isVal = false;
     }
  });
  return isVal;
}

function getBack(){
  jQuery('input.required').each(function(){
     jQuery(this).removeClass('pink-highlight');
  });
}

function calcAmount(){
var num = 0;
    for(var i = 0; i < ajaxData.length; i += 1) {
        if( removeList.indexOf( ajaxData[i]['id'] ) > -1 ) {
          num = num + Number( ajaxData[i]['amount'] );
        }
    }
return num;	
}

function mg_country_detect(){
	jQuery('#mg_select_country').click(function()
	{
		if( jQuery(this).val() == 'Canada')
		{
			jQuery('#mg_province_div').show();
			jQuery('#mg_state_div').hide();			
		}else if( jQuery(this).val() == 'United States')
		{
			jQuery('#mg_province_div').hide();
			jQuery('#mg_state_div').show();					
		}else{
			jQuery('#mg_province_div').hide();
			jQuery('#mg_state_div').hide();					
		}
	});
	
	jQuery('#mg_select_honoreecountry').click(function()
	{
		if( jQuery(this).val() == 'Canada')
		{
			jQuery('#mg_honoreeprovince_div').show();
			jQuery('#mg_honoreestate_div').hide();			
		}else if( jQuery(this).val() == 'United States')
		{
			jQuery('#mg_honoreeprovince_div').hide();
			jQuery('#mg_honoreestate_div').show();					
		}else{
			jQuery('#mg_honoreeprovince_div').hide();
			jQuery('#mg_honoreestate_div').hide();					
		}	
	});
	
}

function mg_campaign_id(){
	jQuery('#mg_select_campaign_form').click(function()
	{
		var mg_selected_campaign	= jQuery(this).val();
		//alert(mg_selected_campaign)
	});

}

function mg_edit_record(){
	jQuery(document).on("click", ".mg_editrecord", function (e) {
		e.preventDefault();	
		var _self 		= jQuery(this);
		var _data 		= _self.attr('name').split('_');				
		
		var _tr			= jQuery(this).closest('tr');
		_tr.find('.mg_edit_link_img').show();
		
		jQuery("#mg_recordID").val(_data[1] );
		jQuery("#mg_ajaxRow").val(_data[2] );
		jQuery("#mg_FormID").val(_data[3] );
		
		jQuery(_self.attr('href')).modal('show');
	 });

}

function mg_edit_record_modal_close()
{
	var rec_id 		= jQuery('#mg_recordID').val();
	jQuery('.mg_load'+rec_id).hide();
}

function mg_edit_record_modal()
{

 jQuery('#mg-edit-record').on('show.bs.modal', function(e) {

   //alert(JSON.stringify(form_struct));
 
   var id    		= jQuery("#mg_recordID").val() ;
   var index 		= jQuery("#mg_ajaxRow").val();
   var form_id  	= jQuery("#mg_FormID").val();
   var form  		= '';   
   var edited 		= ajaxData[index]; 
   var curCampaign	= '';
   var curCountry 	= ''; 
   var curState 	= ''; 
   var curProvince 	= '';
   var curHCountry 	= ''; 
   var curHState 	= ''; 
   var curHProvince = '';
 
	jQuery('#mg_load_img_'+id).html('');
 
	jQuery(this).find('#mgModalEditLabel').text( "Edit Form Record-" + id);
	jQuery(this).find('.modal-body').empty();
		
	form = form + "<div class='form-horizontal'>";
	
	for( var j = 0; j < form_struct.length ; j = j + 1 )
	{
			var str_child = '';
			var count = 0;
			var children = form_struct[j]['child'];
			
		for( var ij = 0; ij < children.length ; ij = ij + 1 )
		{
			var f_code	= children[ij]['code'];
			var f_id	= children[ij]['id'];		
			var line_id	= f_code + f_id;
				
			if( line_id == 'miglad_amount' || line_id == 'miglad_repeating' )
			{
			}else{
				var f_label	= children[ij]['label'];
				var fuid	= children[ij]['uid'];
				var ftype	= children[ij]['type'];
				var clist	= '';

				for( _key11 in customlist )
				{
				    if( _key11 == ('mgval_' + fuid) && typeof customlist[_key11] !== 'undefined' )
					{
						clist	=	customlist[_key11];
						break;
					}
				}
					
				if( f_label == '' || typeof f_label	 === 'undefined' )
				{
					
				}else{
					var mg_regional_id 	= '';
					var mg_style		= '';
				
					if( f_id == 'country' )
					{
						mg_regional_id 	= 'mg_country_div';
						
					}else if( f_id == 'honoreecountry' )
					{
						mg_regional_id = 'mg_honoreecountry_div';
						
					}
					
					form = form + "<div class='form-group touching' id='" + mg_regional_id + "' style='" + mg_style + "'>";
					form = form + "<div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>"+mg_clear(f_label)+" &nbsp;</label></div>";
					
					form = form + "<div id='id_"+line_id+"' class='col-sm-6 col-xs-12 mg_field_to_edit_div '>";		
					
					form = form + "<div style='display:none' class='mg_edit_id'>"+line_id+"</div>";

					if( typeof thedata[line_id] === 'undefined' ){
						form = form + mg_draw_input_type( ftype, fuid, '', clist, f_id );	
					}else{
						form = form + mg_draw_input_type( ftype, fuid, thedata[line_id], clist, f_id );	
					}
					form = form + "</div>";
					form = form + "<div class='col-sm-3 hidden-xs'></div></div>";
					
					if( line_id == 'miglad_campaign' )
					{
						curCampaign 	= thedata['miglad_campaign'];
					}
					
					if( line_id == 'miglad_country' )
					{
					    curCountry 	= thedata['miglad_country'];
					    curState 	= thedata['miglad_state'];
					    curProvince = thedata['miglad_province'];
					  
						if( curCountry == 'Canada' ){
							form = form + "<div class='form-group touching' id='mg_province_div' >";	
						}else{
							form = form + "<div class='form-group touching' id='mg_province_div' style='display:none;'>";							
						}
						
						form = form + "<div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>Provinces</label></div>";						
						form = form + "<div id='id_miglad_province' class='col-sm-6 col-xs-12 mg_field_to_edit_div'>";	
						
							form = form + "<div style='display:none' class='mg_edit_id'>miglad_province</div>";
						
						if( typeof thedata['miglad_province'] === 'undefined' ){
							form = form + mg_draw_input_type( ftype, fuid, '', clist, 'province' );	
						}else{
							form = form + mg_draw_input_type( ftype, fuid, thedata['miglad_province'], clist, 'province' );	
						}
						form = form + "</div>";
						form = form + "<div class='col-sm-3 hidden-xs'></div></div>";					
						
						if( curCountry == 'United States' ){
							form = form + "<div class='form-group touching' id='mg_state_div'>";						
						}else{
							form = form + "<div class='form-group touching' id='mg_state_div' style='display:none;'>";
						}
						form = form + "<div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>States</label></div>";
						
						form = form + "<div id='id_miglad_state' class='col-sm-6 col-xs-12 mg_field_to_edit_div'>";		
						
						form = form + "<div style='display:none' class='mg_edit_id'>miglad_state</div>";

						if( typeof thedata['miglad_state'] === 'undefined' ){
							form = form + mg_draw_input_type( ftype, fuid, '', clist, 'state' );	
						}else{
							form = form + mg_draw_input_type( ftype, fuid, thedata['miglad_state'], clist, 'state' );	
						}
						form = form + "</div>";
						form = form + "<div class='col-sm-3 hidden-xs'></div></div>";							
					}
					
					if( line_id == 'miglad_honoreecountry' )
					{
					    curHCountry 	= thedata['miglad_honoreecountry'];
					    curHState 		= thedata['miglad_honoreestate'];
					    curHProvince 	= thedata['miglad_honoreeprovince'];
					  
						if( curHCountry == 'Canada' ){
							form = form + "<div class='form-group touching' id='mg_honoreeprovince_div' >";	
						}else{
							form = form + "<div class='form-group touching' id='mg_honoreeprovince_div' style='display:none;'>";							
						}
						
						form = form + "<div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>Honoree Province</label></div>";						
						form = form + "<div id='id_miglad_honoreeprovince' class='col-sm-6 col-xs-12 mg_field_to_edit_div'>";	
						
							form = form + "<div style='display:none' class='mg_edit_id'>miglad_honoreeprovince</div>";
						
						if( typeof thedata['miglad_honoreeprovince'] === 'undefined' ){
							form = form + mg_draw_input_type( ftype, fuid, '', clist, 'honoreeprovince' );	
						}else{
							form = form + mg_draw_input_type( ftype, fuid, thedata['miglad_honoreeprovince'], clist, 'honoreeprovince' );	
						}
						form = form + "</div>";
						form = form + "<div class='col-sm-3 hidden-xs'></div></div>";					
						
						if( curHCountry == 'United States' ){
							form = form + "<div class='form-group touching' id='mg_honoreestate_div'>";						
						}else{
							form = form + "<div class='form-group touching' id='mg_honoreestate_div' style='display:none;'>";
						}
						form = form + "<div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>Honoree State</label></div>";
						
						form = form + "<div id='id_miglad_honoreestate' class='col-sm-6 col-xs-12 mg_field_to_edit_div'>";		
						
						form = form + "<div style='display:none' class='mg_edit_id'>miglad_honoreestate</div>";

						if( typeof thedata['miglad_honoreestate'] === 'undefined' ){
							form = form + mg_draw_input_type( ftype, fuid, '', clist, 'honoreestate' );	
						}else{
							form = form + mg_draw_input_type( ftype, fuid, thedata['miglad_honoreestate'], clist, 'honoreestate' );	
						}
						form = form + "</div>";
						form = form + "<div class='col-sm-3 hidden-xs'></div></div>";							
					}					
					
				}
			}
		}
	}
	
	form = form + "</div>";
	
	//If deleted field exist
	if( deleted_fields.length > 0 )
	{
		for( var m = 0; m < deleted_fields.length; m = m + 1 )
		{
			form = form + "<div class='form-horizontal'>";
			form = form + "<div class='form-group touching'>";
			form = form + "<div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>" +  mg_clear( deleted_fields[m][0].substr( 7 ) )  + "</label></div>";
						
			form = form + "<div id='id_"+deleted_fields[m][0]+"' class='col-sm-6 col-xs-12 mg_field_to_edit_div'>";		
						
			form = form + "<div style='display:none' class='mg_edit_id'>"+deleted_fields[m][0]+"</div>";

			if( typeof deleted_fields[m][1] === 'undefined' ){
				form = form + mg_draw_input_type( 'text', '', deleted_fields[m][1], '', '' );	
			}else{
				form = form + mg_draw_input_type( 'text', '', deleted_fields[m][1], '', '' );	
			}
				form = form + "</div>";
				form = form + "<div class='col-sm-3 hidden-xs'></div></div>";
				
			form = form + "</div>";	
		}
	}
	
	//IF it is a single campaign

	//if( form_id != '' )
	//{
		form = form + "<div class='form-horizontal'>";
		form = form + "<div class='form-group touching'>";
		form = form + "<div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>Switch Campaign's Form to</label></div>";
					
		form = form + "<div id='id_miglad_campaign_form' class='col-sm-6 col-xs-12 mg_field_to_edit_div'>";		
					
		form = form + "<div style='display:none' class='mg_edit_id'>miglad_campaign_form</div>";

		if( typeof thedata['miglad_campaign'] === 'undefined' ){
			form = form + mg_draw_input_type( 'select', '', '', '', 'campaign_form' );	
		}else{
			form = form + mg_draw_input_type( 'select', '', '', '', 'campaign_form' );	
		}
			form = form + "</div>";
			form = form + "<div class='col-sm-3 hidden-xs'></div></div>";
			
		form = form + "</div>";
	//}
	
	jQuery( form ).prependTo( jQuery(this).find('.modal-body') );
	
	mg_decode_apothese();
	
	jQuery.ajax
		({
		   type 	: "post",
		   url 		:  miglaAdminAjax.ajaxurl,  
		   data 	:  {  	action		: 'miglaA_get_extra_data_report'
						},
		   success	: function( _result ) 
		   {
				var extraData 	= JSON.parse( _result );
				mg_campaigns		= extraData[0];
				mg_countries		= extraData[1];
				mg_states			= extraData[2];
				mg_provinces		= extraData[3];
				undesignatedLabel	= extraData[4];
				
				//Fill Campaigns
				var isCampaignExisted = false;
				var form 	= form + "<select class='mg_field_to_edit' id='mg_select_campaign'>";
				form 		= form + "<option value='' >Please Choose One</option>";
				form 		= form + "<option value='" +  undesignatedLabel + "'>" +  undesignatedLabel + "</option>";	
				
				for( var i = 0; i < mg_campaigns.length; i = i + 1 ){
				    if( mg_campaigns[i]['name'] == curCampaign )
					{
						form = form + "<option value='" +  mg_campaigns[i]['name'] + "' selected>" +  mg_clear( mg_campaigns[i]['name'] ) + "</option>";
						isCampaignExisted = true;
					}else{
						form = form + "<option value='" +  mg_campaigns[i]['name'] + "'>" +  mg_clear( mg_campaigns[i]['name'] ) + "</option>";	
					}
				}

				if( !isCampaignExisted ){
					form = form + "<option value='" +  curCampaign + "' selected>" +  mg_clear( curCampaign ) + "</option>";	
				}
											   
				form = form + "</select>";	

				jQuery('#mg_edit_campaign').remove();
				jQuery(form).appendTo( jQuery('#id_miglad_campaign' ) );
				
				//Fill Form Campaigns
				var form5 	= form5 + "<select class='mg_field_to_edit' id='mg_select_campaign_form'>";
				form5 		= form5 + "<option value='' >Multi Campaigns</option>";
				
				for( var i = 0; i < mg_campaigns.length; i = i + 1 ) //sweeping through campaign
				{
				    if( mg_campaigns[i]['form_id'] == mg_current_form_id)
					{
						form5 = form5 + "<option value='" +  mg_campaigns[i]['form_id'] + "' selected>" ;
						form5 = form5 + mg_clear( mg_campaigns[i]['name'] ) + ' | ' + mg_campaigns[i]['form_id'] + "</option>";			
					}else{
						form5 = form5 + "<option value='" +  mg_campaigns[i]['form_id'] + "'>" ;
						form5 = form5 + mg_clear( mg_campaigns[i]['name'] ) + ' | ' + mg_campaigns[i]['form_id'] +  "</option>";	
					}
				}			
											   
				form5 = form5 + "</select>";	

				jQuery('#mg_edit_campaign_form').remove();
				jQuery(form5).appendTo( jQuery('#id_miglad_campaign_form' ) );				
				
				//Fill Countries
				var form2 	= form2 + "<select class='mg_field_to_edit' id='mg_select_country'>";
				form2 		= form2 + "<option value='' >Please Choose One</option>";

				var form6 	= form6 + "<select class='mg_field_to_edit' id='mg_select_honoreecountry'>";
				form6 		= form6 + "<option value='' >Please Choose One</option>";				
				
				for( var countries_keys in mg_countries ){
					if( mg_countries[countries_keys] == curCountry ){
						form2 = form2 + "<option value='" +  mg_countries[countries_keys] + "' selected>" +  mg_countries[countries_keys] + "</option>";								 
					}else{ 
						form2 = form2 + "<option value='" +  mg_countries[countries_keys] + "' >" +  mg_countries[countries_keys] + "</option>";			
					}
					
					if( mg_countries[countries_keys] == curHCountry ){
						form6 = form6 + "<option value='" +  mg_countries[countries_keys] + "' selected>" +  mg_countries[countries_keys] + "</option>";								 
					}else{ 
						form6 = form6 + "<option value='" +  mg_countries[countries_keys] + "' >" +  mg_countries[countries_keys] + "</option>";			
					}					
				}			
											   
				form2 = form2 + "</select>";	
				form6 = form6 + "</select>";

				jQuery('#mg_edit_country').remove();
				jQuery(form2).appendTo( jQuery('#id_miglad_country' ) );
				
				jQuery('#mg_edit_honoreecountry').remove();
				jQuery(form6).appendTo( jQuery('#id_miglad_honoreecountry' ) );				
				
				//Fill States
				var form3 	= form3 + "<select class='mg_field_to_edit'>";
				form3 		= form3 + "<option value='' >Please Choose One</option>";
				
				var form7 	= form7 + "<select class='mg_field_to_edit'>";
				form7 		= form7 + "<option value='' >Please Choose One</option>";				
				
				for( var states_keys in mg_states )
				{
					if( mg_states[states_keys] == curState ){				
						form3 = form3 + "<option value='" +  mg_states[states_keys] + "' selected>" +  mg_states[states_keys] + "</option>";							
					}else{
						form3 = form3 + "<option value='" +  mg_states[states_keys] + "' >" +  mg_states[states_keys] + "</option>";			
					}
					
					if( mg_states[states_keys] == curHState ){				
						form7 = form7 + "<option value='" +  mg_states[states_keys] + "' selected>" +  mg_states[states_keys] + "</option>";							
					}else{
						form7 = form7 + "<option value='" +  mg_states[states_keys] + "' >" +  mg_states[states_keys] + "</option>";			
					}					
				}			
											   
				form3 = form3 + "</select>";	
				form7 = form7 + "</select>";	

				jQuery('#mg_edit_state').remove();
				jQuery(form3).appendTo( jQuery('#id_miglad_state' ) );

				jQuery('#mg_edit_honoreestate').remove();
				jQuery(form7).appendTo( jQuery('#id_miglad_honoreestate' ) );				
				
				//Fill Provinces
				var form4 	= form4 + "<select class='mg_field_to_edit' id='mg_select_province'>";
				form4 		= form4 + "<option value='' >Please Choose One</option>";
				
				var form8	= form8 + "<select class='mg_field_to_edit' id='mg_select_province'>";
				form8 		= form8 + "<option value='' >Please Choose One</option>";				
				
				for( var provinces_keys in mg_provinces )
				{
					if(mg_provinces[provinces_keys] == curProvince )
					{
						form4 = form4 + "<option value='" +  mg_provinces[provinces_keys] + "' selected>" +  mg_provinces[provinces_keys] + "</option>";	
					}else{
						form4 = form4 + "<option value='" +  mg_provinces[provinces_keys] + "' >" +  mg_provinces[provinces_keys] + "</option>";			
					}
					
					if(mg_provinces[provinces_keys] == curHProvince )
					{
						form8 = form8 + "<option value='" +  mg_provinces[provinces_keys] + "' selected>" +  mg_provinces[provinces_keys] + "</option>";	
					}else{
						form8 = form8 + "<option value='" +  mg_provinces[provinces_keys] + "' >" +  mg_provinces[provinces_keys] + "</option>";			
					}					
				}			
											   
				form4 = form4 + "</select>";	
				form8 = form8 + "</select>";	

				jQuery('#mg_edit_province').remove();
				jQuery(form4).appendTo( jQuery('#id_miglad_province' ) );
				
				jQuery('#mg_edit_honoreeprovince').remove();
				jQuery(form8).appendTo( jQuery('#id_miglad_honoreeprovince' ) );				
				
				jQuery('#mg_select_country').click(function()
				{
					if( jQuery(this).val() == 'Canada')
					{
						jQuery('#mg_province_div').show();
						jQuery('#mg_state_div').hide();			
					}else if( jQuery(this).val() == 'United States')
					{
						jQuery('#mg_province_div').hide();
						jQuery('#mg_state_div').show();					
					}else{
						jQuery('#mg_province_div').hide();
						jQuery('#mg_state_div').hide();					
					}
				});
				
				jQuery('#mg_select_honoreecountry').click(function()
				{
					if( jQuery(this).val() == 'Canada')
					{
						jQuery('#mg_honoreeprovince_div').show();
						jQuery('#mg_honoreestate_div').hide();			
					}else if( jQuery(this).val() == 'United States')
					{
						jQuery('#mg_honoreeprovince_div').hide();
						jQuery('#mg_honoreestate_div').show();					
					}else{
						jQuery('#mg_honoreeprovince_div').hide();
						jQuery('#mg_honoreestate_div').hide();					
					}	
				});				
								
				
				//mg_country_detect();
				//mg_campaign_id();
				
			 }, 
			 async : false
		});
		
	jQuery('#mg-edit-record-close').click(function(){
		mg_edit_record_modal_close();
	});	
	
	jQuery('#mg_cancel_update_record').click(function(){
		mg_edit_record_modal_close();
	});		
	
 });
}

function mg_draw_input_type( _type, _fuid, _data, _list, _specialcode )
{
	var form = '';
	if( _specialcode == 'campaign' )
	{		
	    form = form + '<div id="mg_edit_campaign">' + jQuery('#mg_load_image').html() + '</div>';
	
	}else if( _specialcode == 'campaign_form' )
	{		
	    form = form + '<div id="mg_edit_campaign_form">' + jQuery('#mg_load_image').html() + '</div>';
		
	}else if( _specialcode == 'country' || _specialcode == 'province' || _specialcode == 'state' 
		|| _specialcode == 'honoreecountry' || _specialcode == 'honoreestate' || _specialcode == 'honoreeprovince' )
	{	
		form = form + '<div id="mg_edit_'+_specialcode+'">' + jQuery('#mg_load_image').html() + '</div>';

	}else{
		if( _type == 'text' )
		{
			form = form + "<input class='mg_field_to_edit mg_field_edit_text' type='text' value='"+ _data  +"' />" ;
			
		}else if( _type == 'textarea' )
		{
			form = form + "<input class='mg_field_to_edit mg_field_edit_text' type='text' value='"+ _data  +"' />" ;
			
		}else if( _type == 'checkbox' )
		{
				if(  _data == 'yes' )
				{
				   form = form + "<div class='checkbox'><label><input class='mg_field_to_edit' name='' type='checkbox' id='' checked value='yes' />&nbsp;</label></div>" ;
				}else{
				   form = form + "<div class='checkbox'><label><input class='mg_field_to_edit' name='' type='checkbox' id='' value='yes' />&nbsp;</label></div>" ;
				}	
		}else if( _type == 'radio' )
		{
		   var radio_list1 	= _list;
		   radio_list1 = radio_list1.substring(0, (radio_list1.length - 1));	   
		   if( radio_list1 != '' && typeof radio_list1 !== 'undefined' )
		   {		   
				form = form + "<div class='radio'><label for=''>";
				form = form + "<input name='"+_fuid+"' value='' type='radio' class='mg_field_to_edit'>";
				form = form + 'none';
				form = form + "</label></div>"; 
				
				var radio_list2	= radio_list1.split(';');	
				
				for( var i = 0; i < radio_list2.length; i = i + 1 )
				{
					var radio_list3 = radio_list2[i].split('::');
					form = form + "<div class='radio'><label for='' >";
					if( radio_list3[0] == _data )
					{
						form = form + "<input name='"+_fuid+"' value='" + radio_list3[0] + "' type='radio' class='mg_field_to_edit' checked>";
					}else{
						form = form + "<input name='"+_fuid+"' value='" + radio_list3[0] + "' type='radio' class='mg_field_to_edit'>";	
					}
					form = form + mg_clear( radio_list3[1] );
					form = form + "</label></div>"; 				
				}	
				
			}
		}else if( _type == 'select' )
		{
		   form = form + "<select class='mg_field_to_edit' >";
		   form = form + "<option value='' >Please Choose One</option>";

		   var radio_list1 	= _list;
		   radio_list1 		= radio_list1.substring(0, (radio_list1.length - 1));	   
		   if( radio_list1 != '' && typeof radio_list1 !== 'undefined' )
		   {		   			
				var radio_list2	= radio_list1.split(';');	
				
				for( var i = 0; i < radio_list2.length; i = i + 1 )
				{
					var radio_list3 = radio_list2[i].split('::');
					form = form + "<option value='" + radio_list3[0] + "' >" + mg_clear( radio_list3[1] ) + "</option>";			
				}	
				
			}			   
				   
			form = form + "</select>";
				   
		}else if( _type == 'multiplecheckbox' )
		{
		   var mcheckbox1 	= _list;
		   mcheckbox1 = mcheckbox1.substring(0, (mcheckbox1.length - 1));	   
		   if( mcheckbox1 != '' && typeof mcheckbox1 !== 'undefined' )
		   {		   
	            /*
				form = form + "<div class='checkbox'><label for='' >";
				form = form + "<input name='"+_fuid+"' value='' type='checkbox' class='mg_field_to_edit'>";
				form = form + 'None';
				form = form + "</label></div>"; 
				
				
				var mcheckbox2	= mcheckbox1.split(';');	
				
				for( var i = 0; i < mcheckbox2.length; i = i + 1 )
				{
					var mcheckbox3 = mcheckbox2[i].split('::');
					
					
					form = form + "<div class='checkbox'><label for='' >";
					form = form + "<input name='"+_fuid+"' value='" + mcheckbox3[0] + "' type='checkbox' class='mg_field_to_edit'>";
					form = form + mg_clear( mcheckbox3[1] );
					form = form + "</label></div>"; 	
                    
					
					form = form + "<div class='checkbox'><label for='' >";
					if( _data != '' && typeof _data !== 'undefined' )
					{
						if( _data.search( mcheckbox3[0] ) >= 0 ){
							form = form + "<input name='"+_fuid+"' value='" + mcheckbox3[0] + "' checked='checked' type='checkbox' class='mg_field_to_edit'>"
						}else{
							form = form + "<input name='"+_fuid+"' value='" + mcheckbox3[0] + "' type='checkbox'  class='mg_field_to_edit'>"
						}
					}
					form = form + mg_clear( mcheckbox3[1] );
					form = form + "</label></div>";    	
				}	
				*/
				
				
				var mcheckbox2	= mcheckbox1.split(';');	
				
				for( var i = 0; i < mcheckbox2.length; i = i + 1 )
				{
					var mcheckbox3 = mcheckbox2[i].split('::');
					
					form = form + "<div class='checkbox'><label for='' >";

						if( _data.search( mcheckbox3[0] ) >= 0 ){
							form = form + "<input name='"+_fuid+"' value='" + mcheckbox3[0] + "' checked='checked' type='checkbox' class='mg_field_to_edit'>"
						}else{
							form = form + "<input name='"+_fuid+"' value='" + mcheckbox3[0] + "' type='checkbox'  class='mg_field_to_edit'>"
						}
					
					form = form + mg_clear( mcheckbox3[1] );
					form = form + "</label></div>";    	
				}				
			}	
		}
	}
	
	//jQuery('#mg_load_edit').hide();
	
	return form;
}

function get_data_for_update()
{
  var updatedFields = []; 
  var row 			= 0;

  jQuery('#mg-edit-record').find('.mg_field_to_edit_div').each(function(){

     var new_value_obj 	= jQuery(this).find('.mg_field_to_edit');
     var send_value		= '';
     var send_id		= jQuery(this).find('.mg_edit_id').html();
     var ajaxIdx 		= jQuery('#mg_ajaxRow').val();

          if( new_value_obj.attr('type') == 'checkbox' )
          {
				//multiple checkbox
				var n = new_value_obj.length;
				var i = 0;
                if( n > 1 )
                {
					new_value_obj.each( function()
					{
						i = i + 1;
                        if( jQuery(this).is(':checked') )
                        {        
							if( i < n )
							{	
								send_value = send_value + jQuery(this).val() + ",";
							}else{
								send_value = send_value + jQuery(this).val() ;
							}
                        }
					});
                }else{
                  //id  = new_val.attr('name');
                  if( new_value_obj.is(':checked') ){
                      send_value	= 'yes';
                  }else{
                      send_value    = 'no';
                  }
                }

                send_value = new String( send_value );
                var re 		= /\[q\]/g;
                var cval 	= send_value.replace(re, "'");
                ajaxData[ajaxIdx][send_id] = cval;
				ajaxData2[ajaxIdx][send_id] = cval;
				
                var e = [ send_id, send_value ];  
                updatedFields.push(e);
            
          }else if ( new_value_obj.attr('type') == 'radio' )
          {          
                send_value 	= new String ( jQuery(this).find("input[type=radio]:checked").val() );
                var re = /\[q\]/g;
                var cval = send_value.replace(re, "'");
                //var cval =  val.replace( "[q]", "'" );
                ajaxData[ajaxIdx][send_id] = cval;
				ajaxData2[ajaxIdx][send_id] = cval;
             
                var e = [ send_id , send_value ];  
                updatedFields.push(e);

          }else if( new_value_obj.attr('type') == 'text' )
		  {
                var re = /\[q\]/g;
				send_value	= new_value_obj.val();	
                var cval 	= send_value.replace(re, "'");
                //var cval =  val.replace( "[q]", "'" );
                ajaxData[ajaxIdx][send_id] = cval;
				ajaxData2[ajaxIdx][send_id] = cval;
             
                var e = [ send_id , mg_decode( send_value ) ];  
                updatedFields.push(e);

          }else
          {
			send_value	= new_value_obj.val();	
            var re 		= /\[q\]/g;
            var cval 	= send_value.replace(re, "'");

            ajaxData[ajaxIdx][send_id] = cval;
			ajaxData2[ajaxIdx][send_id] = cval;
			
            var e = [ send_id , mg_decode( send_value ) ];  
                updatedFields.push(e);
          }

  });
  
  //alert( JSON.stringify(updatedFields) );
  
  return updatedFields;
}



function doRefresh(data) {
        var oTable = jQuery('#miglaReportTable').dataTable();
        oTable.fnClearTable();
        oTable.fnAddData(ajaxData);
        oTable.fnDraw();
}

jQuery(document).ready( function() {

  if( jQuery('#placement').text() == 'before')
  { 
	before =jQuery('div#symbol').html();after=''; 
  }else{ 
	after =jQuery('div#symbol').html();before=''; 
  } 
  thouSep = jQuery('#thousandSep').text(); decSep = jQuery('#decimalSep').text();
  showDec = 0;
  if( jQuery('#showDecimal').text() == 'yes' ){ 
	showDec = 2; 
  }

  jQuery('#confirm-delete').modal({show: false});

  jQuery('#sdate, #edate').val("");


	jQuery('.miglaOffdate').datepicker({
		dateFormat : 'mm/dd/yy', 
		onSelect: function() { 
			jQuery(".ui-datepicker a").removeAttr("href");
			jQuery('#sdate, #edate').trigger('change');
		} 
	});  
  
  Number.prototype.formatMoney = function(decPlaces, thouSeparator, decSeparator) {
    var n = this,
        decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
        decSeparator = decSeparator == undefined ? "." : decSeparator,
        thouSeparator = thouSeparator == undefined ? "," : thouSeparator,
        sign = n < 0 ? "-" : "",
        i = parseInt(n = Math.abs(+n || 0).toFixed(decPlaces)) + "",
        j = (j = i.length) > 3 ? j % 3 : 0;
    var result = sign + (j ? i.substr(0, j) + thouSeparator : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" 
            + thouSeparator) + (decPlaces ? decSeparator + Math.abs(n - i).toFixed(decPlaces).slice(2) : "");
    return result;
  };

  jQuery('input[type=checkbox]').each(function () {
    jQuery(this).checked = false;
  });

 Array.prototype.remove = function(value) {
    var idx = this.indexOf(value);
    if (idx != -1) {
       return this.splice(idx, 1); // The second parameter is the number of elements to remove.
    }
   return false;
 }


/////RETRIEVE ALL RECORD///////////////////////////////

 jQuery.ajax({
   type 	: "post",
   url 		:  miglaAdminAjax.ajaxurl,  
   data 	:  { action:'miglaA_report' },
   success	: function( report_data ) 
     {
		 ajaxData = JSON.parse(report_data);		 		 		 

		 if( ajaxData.length > 0){ 
		   ajaxData.sort(function (a, b) {
			 return (new Date(b.miglad_date + ' ' + b.miglad_time) - new Date(a.miglad_date + ' ' + a.miglad_time) );
		   });
		 }
		 
         jQuery('#miglaReportTable tfoot th').each( function () {
               var th_ = jQuery('#miglaReportTable thead th').eq( (jQuery(this).index()+2) );
                if ( th_.hasClass('detailsHeader') || th_.hasClass('th_date') ) 
				{
				}else{
					jQuery(this).html( '<input type="text" placeholder="'+ th_.text() +'" name="'+ th_.text() +'" />' );
				}
         });	
		 
         oTable   = mdataTable( ajaxData ); 
		 
		 jQuery.fn.dataTable.ext.search.push(
		   function( settings, data, dataIndex ) {
			 var min = Date.parse( jQuery('#sdate').val() ) ;
			 var max = Date.parse( jQuery('#edate').val() );
			 var age = Date.parse( data[2] );
	 
			 if (  ( isNaN( min ) && isNaN( max ) ) ||
				 ( isNaN( min ) && age <= max ) ||
				 ( min <= age   && isNaN( max ) ) ||
				 ( min <= age   && age <= max ) 
			 )
			 {
				return true;
			}
			return false;
		 });

		// Event listener to the two range filtering inputs to redraw on input
		jQuery('#sdate, #edate').on( 'keyup change', function () 
		{
		   jQuery('#miglaReportTable').DataTable().draw();
		   jQuery('.miglaOffdate').datepicker('hide');
		});

        //Search on footer
         jQuery( 'input' ).on( 'keyup change', function () {

            var p = jQuery(this).parent();
            var col = p.attr("id");
            col = col.slice(1);
            
            jQuery('#miglaReportTable').DataTable().column( col ).search(
               jQuery(this).val(),
               jQuery(this).prop('checked'),
               jQuery(this).prop('checked')
			).draw(); 

         });

		 jQuery('.sorting').click(function(){
			var n = jQuery('tr.det');
			var m = jQuery('.shown');
			m.removeClass('shown');
			n.remove();
		 });
 
		  jQuery('th.detailsHeader').removeClass('sorting_asc');
		  jQuery('th.detailsHeader').removeClass('sorting_desc');  

		jQuery('#miglaReportTable tbody').delegate("td", "click", celClick);		  

	},
    async : false
}); //ajax Report
////////////RETRIEVE ALL RECORDS END HERE////////////////////////

	jQuery('#mg_update_record').click(function(){
	
	
		var the_update_list	= get_data_for_update();
		var rec_id			= jQuery('#mg_recordID').val();
		var ajax_row		= jQuery('#mg_ajaxRow').val();
		mg_form_id_send		= jQuery('#mg_select_campaign_form').val();
		var campaign		= jQuery('#mg_select_campaign_form option:selected').text();
		var cname = campaign.split(" | ");

		jQuery.ajax({
			type	: 'post',
			url		: miglaAdminAjax.ajaxurl,
			data	: {
						action		: 'miglaA_update_report',
						data_send	: the_update_list,
						record_id	: rec_id,
						new_form_id	: mg_form_id_send
					},
			success	: function(){
				saved('#mg_update_record'); 
                jQuery('#mg-edit-record-close').trigger('click');
				if( mg_form_id_send != '' ){
					ajaxData[ajax_row]['miglad_campaign'] = cname[0];
				}
				ajaxData[ajax_row]['miglad_form_id'] = mg_form_id_send;

			},
			async	: false
		});
		
		jQuery.ajax({
			type 	: "post",
			url 		:  miglaAdminAjax.ajaxurl,  
			data 	:  { action:'miglaA_export_report' , post_type : 'migla_donation' },
			success	: function( report_data2 ) 
			{
				ajaxData2 = JSON.parse(report_data2);  
			}
		});
		
		jQuery.ajax({
			type 	: "post",
			url 		:  miglaAdminAjax.ajaxurl,  
			data 	:  { action:'miglaA_report' },
			success	: function( report_data ) 
			{
				ajaxData = JSON.parse(report_data);	
								doRefresh(ajaxData);
			}
		 });
		
		//alert(JSON.stringify(the_update_list));
	});
	
	mg_export();	
	
	jQuery('#miglaRemove').click( function(){
		jQuery('#confirm-delete').show();
	});
	
	jQuery('#confirm-delete').on('show.bs.modal', function(e) 
	{
		var msg 			= "";
		var msg_line 		= "";       
		inQuery 			= "";
	  
		jQuery(this).find('.modal-body').empty();
	  
		if( removeList.length > 0 )
		{	
			msg_line = msg_line + "<table>";
			for(var i = 0; i < removeList.length; i = i + 1)
			{
			    if( i == 0 )
				{
					inQuery = inQuery + removeList[i];
				}else{
					inQuery = inQuery + ", " + removeList[i];				
				}
				
				var get_info = findWithAttr(ajaxData, "id", removeList[i]);
				msg_line = msg_line + get_info[0];
			}

			msg_line = msg_line + "</table>";
			
			inQuery = "(" + inQuery + ")";
			
			msg_line = "<p>" + jQuery('#mg-warningconfirm1').text() + "</p>" + msg_line ;
			
			if( get_info[1] )
			{
				msg_line = msg_line + "<p>" + jQuery('#mg-warningconfirm3').text() + "</p>";	
			}

			msg_line = msg_line + "<p>" + jQuery('#mg-warningconfirm2').text() + "</p>";

			jQuery('.btn-danger').show();
			
			msg = msg_line ;
			
		}else{
			msg = "<p>Nothing selected for deletion</p>";
			jQuery('.btn-danger').hide();
		}
	  
		jQuery(msg).prependTo( jQuery(this).find('.modal-body') );         
	})

jQuery('.mg_remove_donation').click( function(){
 if( removeList.length > 0 )
 {
   jQuery.ajax({
      type : "post",
      url :  miglaAdminAjax.ajaxurl,  
      data :  { 
				action	: 'miglaA_remove_donation', 
				list	: inQuery },
      success: function() {
		allAmount = allAmount - calcAmount();
		document.getElementById("miglaOnTotalAmount").innerHTML = before +" "+ (allAmount).formatMoney(showDec, thouSep , decSep ) + after; 
        oTable.row('.removed').remove().draw( false );
        removeList.length = 0;    
        jQuery( ".close" ).trigger( "click" );
      }
    }); //ajax  
 }

});

   jQuery.ajax({
        type  : 'post',
        url   : miglaAdminAjax.ajaxurl,
        data  : {
                  action : 'miglaA_total_online'
                },
        success : function( result ){
                    var result_json = JSON.parse(result);
                    document.getElementById("miglaOnTotalAmount").innerHTML = before +" "+ (result_json.amount).formatMoney(showDec , thouSep , decSep  ) + after;  
                }
   });
   
	
	 jQuery.ajax({
	   type 	: "post",
	   url 		:  miglaAdminAjax.ajaxurl,  
	   data 	:  { action:'miglaA_export_report' , post_type : 'migla_donation' },
	   success	: function( report_data2 ) 
		 {
			 ajaxData2 = JSON.parse(report_data2);  
		 }
	 });
	
}); //Document ready
