var decimalSep; 
var thousandSep; 
var decimalNum; 
var showDec;
var off 			= []; 
var on 				= [];
var offArray 		= []; 
var onArray 		= [];

var data_retrieval 	= [];
var campaigns	 	= {};
var mg_online_total = 0;
var mg_all_total 	= 0;
var total_online_this_month = 0;
var total_all_this_month = 0;
var theoutput		= [];

function calMonth( month )
{
  var m;
 switch ( Number(month) ) {
    case 1:
        m = "Jan";
        break;
    case 2:
        m = "Feb";
        break;
    case 3:
        m = "March";
        break;
    case 4:
        m = "April";
        break;
    case 5:
        m = "May";
        break;
    case 6:
        m = "June";
        break;
    case 7:
        m = "July";
        break;
    case 8:
        m = "Aug";
        break;
    case 9:
        m = "Sept";
        break;
    case 10:
        m = "Oct";
        break;
    case 11:
        m = "Nov";
        break;
    case 12:
        m = "Dec";
        break;
  }
  
	return m;
}

function convertDate( theDate )
{
  var str; var m; var d; var y;
  var field = [ '01', '01', '2015' ];
  if( theDate != '' ){
    field = theDate.split('/');
  }
  m = calMonth( field[0] );

 var dd = field[1];
 dd     = dd.slice(-1); 
 var something;
 if( dd == "1" ){ 
    something = "st"; 
 }else if( dd == "2" ){ 
    something = "nd"; 
 }else{ 
   something = "th"; 
 } 

 str = m + " " + String(Number(field[1])) +  something +", " + field[2];

return str;
}

/**** Added April 2th ****/
function clean_undefined( x ){
  var str = '';
  if( (typeof x === 'undefined') )
  {
    return str ;
  }else{
    return x;
  }
}

/*** Updated April 2th *****/
function recentItem( tdate ,time,name,amount,address, city, state, province, country,repeat, anon)
{
  name    = clean_undefined( name ); 
  amount  = clean_undefined( amount ); 
  address = clean_undefined( address ); 
  city    = clean_undefined( city ); 
  state   = clean_undefined( state ); 
  province= clean_undefined( province ); 
  country = clean_undefined( country );

  var timedif = 0;
  var cdate = convertDate( tdate );

  var province_state = state;
  if( state == ''){ 
     if( province == '' ){

     }else{
       province_state = province; 
     }
  }

  str = "";
  str = str + "<div class='timeline-item'><div class='row'><div class='col-xs-3 date'>";
  str = str + "<span class=''>" + jQuery("div#symbol").html() + "</span>";
  str = str + cdate;
  str = str + "<br> <small class='text-navy'>"+ time +"</small> </div>";
  str = str + "<div class='col-xs-8 content'><p class='m-b-xs'>";
  str = str + "<strong>"+ amount +"</strong>";
  str = str + "<span class='donorname'>" + name + "</span></p>";

  if( address != '' ){
     str = str + address + '<br>';
  }

  if( province_state != '' ){
     str = str + province_state + '<br>';
  }

  if( city != '' ){
     str = str + city + ', ';
  }

  if( country != '' ){
     str = str + country + '<br>';
  }

  str = str + "Anonynmous : ";
  str = str + " <strong>" + anon + "</strong>";
  str = str + "<br>Repeating : ";
  str = str + " <strong>" + repeat + "</strong>";
  str = str + "</p>";

  str = str + "</div></div></div>";

  return str;
}

function getcampaigns( num, name, percent, status, t, a, type)
{
	var stat 		= "open"; 
	var statclass 	= 'label-success';

	if( status == '0' || status == '-1' )
	{ 
		stat 		= "closed"; 
		statclass 	= 'label-warning'; 
	}

	var lbl = name.replace("[q]", "'");

	var str = "";
	str = str + "<tr><td>" + num + "</td><td>"+lbl+"</td>";
	str = str + "<td><span class='label " + statclass + "'>" + stat + "</span></td>";

	if( Number(t) != 0 ){
		  str = str + "<td><div class='progress progress-sm progress-half-rounded m-none mt-xs light mg_percentage'>";
		  str = str + "<div style='width: " + percent + "%;' aria-valuemax='100' aria-valuemin='0' aria-valuenow='60' role='progressbar'";
		  str = str + "class='progress-bar progress-bar-primary'>" + percent + "%</div></div></td>";    			
	}else{
		if( jQuery('#placement').html() == 'before' ){
			str = str + "<td><div class='undeclared-campaign'> Raised " + jQuery('#symbol').html() + a + "</div></td>";
		}else{
			str = str + "<td><div class='undeclared-campaign'> Raised "  + a + jQuery('#symbol').html() + "</div></td>";
		}
	} 
	
	str = str + "</tr>";

	return str;
}

function drawChartRev(online, offline)
{
    var major = []; var amount_online = [] ; var amount_offline = [];
    for(key1 in on){
	    major.push( online[key1]['label'] ); 
		amount_online.push( online[key1]['amount'] ); 
		amount_offline.push( offline[key1]['amount'] ); 
	}

	var lineChartData  = {
    labels:major,
    datasets: [
        {
            label: "Online",
            fillColor: "rgba(66,139,202,0.1)",
            strokeColor: "rgba(66,139,202,1)",
            pointColor: "rgba(66,139,202,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(220,220,220,1)",
            data: amount_online
        },
        {
            label: "Offline",
            fillColor: "rgba(151,187,205,0.1)",
            strokeColor: "rgba(43,170,177,1)",
            pointColor: "rgba(43,170,177,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(151,187,205,1)",
            data: amount_offline
        }
    ]
};
	
	return lineChartData;
}

function campaignPrototype(campaign, percent, status) {
  this.campaign = campaign;
  this.percent = percent;
  this.status = status;
}

jQuery(document).ready( function() {	

Number.prototype.formatMoney = function(decPlaces, thouSeparator, decSeparator) {
    var n = this,
        decPlaces 		= isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
        decSeparator 	= decSeparator == undefined ? "." : decSeparator,
        thouSeparator 	= thouSeparator == undefined ? "," : thouSeparator,
        sign 			= n < 0 ? "-" : "",
        i 				= parseInt(n = Math.abs(+n || 0).toFixed(decPlaces)) + "",
        j 				= (j = i.length) > 3 ? j % 3 : 0;
    return sign + (j ? i.substr(0, j) + thouSeparator : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thouSeparator) + (decPlaces ? decSeparator + Math.abs(n - i).toFixed(decPlaces).slice(2) : "");
};

	var ajaxData ; 
	var ajaxData2;
	var d;
	var list;
	
	jQuery.ajax({
			type 	: "post",
			url 	: miglaAdminAjax.ajaxurl, 
			data 	: {	action	: 'miglaA_detail_6months'	},
			success	: function( data_6months ) 
			{ 
				data_retrieval 	= JSON.parse( data_6months );
				list				= data_retrieval[0];
				campaigns			= data_retrieval[2] ;
				var timeline 		= '';
				
				//alert( JSON.stringify(data_retrieval[0]) );
				
				if( list.length > 0)
				{ 
					for (var i = 0; i < 5 && i < list.length; i++) 
					{
						var item = list[i];
						formatAmount = Number(item.amount);	  
					 
						timeline  = timeline  +  recentItem( 
							 item.date, 
							 item.time, 
							 item.name, 
							 formatAmount.formatMoney(showDec,decimalSep,thousandSep), 
							 item.address, 
							 item.city,
							 item.state, 
							 item.province,
							 item.country, 
							 item.repeating, item.anonymous);          
					}  
					jQuery( timeline ).appendTo( jQuery(".ibox-content") );
				
				}else{			
					timeline = timeline + "<div class='timeline-item'><div class='row'>";
					timeline = timeline + "No donation has been made";
					timeline = timeline + "</div></div>";
					jQuery(timeline ).appendTo( jQuery(".ibox-content") );
				}

				var str = '';
				jQuery.ajax({
					type 	: 'post',
					url 	: miglaAdminAjax.ajaxurl, 
					data 	: {	
								action			: 'miglaA_campaignprogress'
								},
					success	: function( output ) 
					{ 
						var theoutput	= JSON.parse(output);
						for( ckey in theoutput )
						{
							str = str +  getcampaigns( 
									theoutput[ckey]['index'] , 
									theoutput[ckey]['campaign'], 
									Number( theoutput[ckey]['percent'] ), 
									theoutput[ckey]['status'],
									theoutput[ckey]['target'], 
									theoutput[ckey]['amount'], 
									theoutput[ckey]['type']
							);							
						}
						
						jQuery( str ).appendTo( jQuery('.table tbody') );		

					}
				});
				 
				
			},
			asycn : false
	});

		document.getElementById("monthOnAmount").innerHTML 	= "online";
		document.getElementById("onAmount").innerHTML 		= "online";
		decimalSep 											= jQuery('#thousandSep').text();
		thousandSep											= jQuery('#decimalSep').text();
		showDec											 	= 0;
		
		if( jQuery('#showDecimal').text() == 'yes' ){ 
			showDec = 2; 
		}
		var before = ''; 
		var after = '';

	if( jQuery('#placement').text() == 'before' )
	{ 
		before = jQuery("div#symbol").html(); 
	}else{ 
		after = jQuery("div#symbol").html(); 
	}
	
	jQuery.ajax({
		type 	: "post",
		url 	:  miglaAdminAjax.ajaxurl, 
		data 	: {	action: "miglaA_total_online" },
		success	: function( result ) 
			{
                        var json_result = JSON.parse(result); 
                        mg_online_total = json_result.amount;
			var str_total = "<span class=''>" + before + "</span>"+(json_result.amount).formatMoney(showDec,decimalSep,thousandSep)+ after + " online";	
			document.getElementById("onAmount").innerHTML =  str_total ;        

			}//success
	}); //ajax  	
	
	jQuery.ajax({
		type 	: "post",
		url 	:  miglaAdminAjax.ajaxurl, 
		data 	: {	action: "miglaA_total_offline" },
		success	: function( result ) 
			{
                        var json_result   = JSON.parse(result); 
                        mg_all_total      = json_result.amount +  mg_online_total; 
                        var total_all_str = "<span class=''>" + before + "</span>" + (mg_all_total).formatMoney(showDec,decimalSep,thousandSep)+ after + ""; 
			document.getElementById("amount").innerHTML = total_all_str;          				
			}//success
	}); //ajax  	
		
	jQuery.ajax({
		type 	: "post",
		url 	:  miglaAdminAjax.ajaxurl, 
		data 	: {	action: 'miglaA_total_online_this_month' },
		success	: function( total ) 
			{
			    total_online_this_month = Number(total);
				document.getElementById("monthOnAmount").innerHTML = "<span class=''>" + before + "</span>" + (total_online_this_month).formatMoney(showDec,decimalSep,thousandSep)+ after + " online";           				
			}//success
	}); //ajax  	

	
	jQuery.ajax({
		type 	: "post",
		url 	:  miglaAdminAjax.ajaxurl, 
		data 	: {	action: 'miglaA_total_offline_this_month' },
		success	: function( total ) 
			{
			    var t_amount = Number(total);
				total_all_this_month = total_online_this_month + t_amount ;
				document.getElementById("monthAmount").innerHTML = "<span class=''>" + before + "</span>" + (total_all_this_month).formatMoney(showDec,decimalSep,thousandSep)+ after + "";           				
			}//success
	}); //ajax  
	
/*
				var ij = 0;
				//alert(JSON.stringify(campaigns));
				for( ckey in campaigns )
				{
					var curr_campaign	= campaigns[ij];
					var cname 	= curr_campaign['name'];
					var str		= '';
					var theoutput = {};
					
					//alert(JSON.stringify(curr_campaign));
					
					jQuery.ajax({
						type 	: 'post',
						url 	: miglaAdminAjax.ajaxurl, 
						data 	: {	
									action			: 'miglaA_campaign_progress',
									campaign_name	: cname,
									index			: (ij+1)
								},
						success	: function( output ) 
						{ 
							theoutput	= JSON.parse( output );
							
							alert( JSON.stringify(theoutput) );
							
							decimalSep 	= jQuery('#thousandSep').text();
							thousandSep = jQuery('#decimalSep').text();
							showDec 	= 0;
							var str		= '';
							
							if( jQuery('#showDecimal').text() == 'yes' ){ 
								showDec = 2; 
							}
							
							var money1 	= theoutput['amount'];					
							
							str = str +  getcampaigns( 
												theoutput['index'] , 
												theoutput['name'], 
												Number( theoutput['percent'] ), 
												theoutput['show'],
												theoutput['target'], 
												money1, 
												theoutput['type']
												);

							jQuery( str ).appendTo( jQuery('.table tbody') );								
						}
					});
					ij = ij + 1;
				}; //For each campaign progress	
	*/
	
	
		jQuery.ajax({
			type 	: "post",
			url 	:  miglaAdminAjax.ajaxurl, 
			data 	: {	action	: "miglaA_getGraphData" },
			success	: function(msg) 
			{ 
			
			   var d 	= JSON.parse(msg);
			   on 		= d[0]; 
			   off 		= d[1];

			   var total_this_month = on[5]['amount'] + off[5]['amount'];
			   var total_online_this_month = on[5]['amount'];

				  document.getElementById("monthAmount").innerHTML = "<span class=''>" + before + "</span>" + (total_this_month).formatMoney(showDec,decimalSep,thousandSep) + after;   
				  document.getElementById("monthOnAmount").innerHTML = "<span class=''>" + before + "</span>" + (total_online_this_month).formatMoney(showDec,decimalSep,thousandSep)+ after + " online";     
			   
				if( on.length == 1 && off.length == 1 && on[0].amount == 0 && off[0].amount == 0 ){
				  document.getElementById("sectionB").remove();
				  jQuery('<p>No data to display</p>').insertAfter('#migla-donation-title');
				}else{	   
					  var linechart = drawChartRev(on, off);
						   var ctx = document.getElementById("sectionB").getContext("2d");
					   window.myLine = new Chart(ctx).Line(linechart, {
						  responsive: true
						});
					  document.getElementById("legendDiv").innerHTML = window.myLine.generateLegend(); 
				 
					   jQuery('ul.line-legend').find('li').each(function(){
						   jQuery(this).remove('span');
						   var label = jQuery(this).text(); var change = "";
						   jQuery(this).empty();
						 if( label == 'Online'){
						   change = change + "<div class='swatch' style='background-color:rgba(66,139,202,1);'></div><span class='swatchLabel'>";
						   change = change + "Online</span>";
						 }else{
						   change = change + "<div class='swatch' style='background-color:rgba(43, 170, 177, 1);'></div><span class='swatchLabel'>";
						   change = change + "Offline</span>";
						 }
						  jQuery(change).appendTo( jQuery(this) ); 
					   });

					 jQuery('#legendDiv').insertAfter('#migla-donation-title');
				  }							  
		   }//ajax success bracket
		 }); //ajax  
});
