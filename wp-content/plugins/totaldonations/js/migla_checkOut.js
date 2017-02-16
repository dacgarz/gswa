var mdata = [];
var sessionid;
var amount; 
var cleanAmount; 
var repeating; 
var anonymous;
var token_;
var warning = ["", "", ""];
var recurring_time=''; var recurring_period ='';
var state_code = {} ;
var province_code = {} ;
var plan_info = [];
var honoreecountry; var honoreestate; var honoreeprovince;
var  first_name ;
var  last_name ;
var postdata = {};
var stripe_response;

function sendtoPaypal(){
   jQuery( '#migla-hidden-form' ).submit();
} 


function getMapValue( v ){
 str = "";
 for (i = 0; i < mdata.length; i++) {
    if( v == mdata[i][0] )
    {
      return mdata[i][1];
    }
 }  
 return str;
}

function getMapIndex( v ){
 str = 0;
 for (i = 0; i < mdata.length; i++) {
    if( v == mdata[i][0] )
    {
      str = i;
    }
 }  
 return str;
}


function mg_stripeResponseHandler(status, response) {
	
	stripe_response = response;
	
   if (response.error) {
   
     // re-enable the submit button
     //jQuery('#miglastripecheckout').removeAttr("disabled");

     // show the errors on the form
     jQuery(".payment-errors").html(response.error.message);

     alert( response.error.message );
     jQuery('#miglastripecheckout').show(); 
     jQuery('#mg_wait_stripe').hide();

   } else {

		 var form$ = jQuery("#mg-stripe-payment-form");

		 // token contains id, last4, and card type
		 var token 			= response['id'];
		 var address_pass 	= stripe_response.card.address_line1_check;
		 var zip_pass 		= stripe_response.card.address_zip_check ;
		 var is_avs_pass 	= (address_pass != 'unchecked') && (zip_pass != 'unchecked');
		
		//alert( address_pass + zip_pass  );
		 
		if( !is_avs_pass && jQuery('#migla_credit_card_avs').val() == 'yes' )
		{ 
			 alert('Address Verification failed. Try to fill correct address and postal code');
			 jQuery('#miglastripecheckout').show(); 
			 jQuery('#mg_wait_stripe').hide();
			 
		}else{
		 
		 var repeatingField = jQuery("#migla_donation_form").find("input[name='miglad_repeating']");
		 if( repeatingField.length > 0 )
		 {
			if( repeatingField.attr("type") == "checkbox" ){
			  if( jQuery("#migla_donation_form").find("input[name='miglad_repeating']").is(":checked") ){
				 var info = jQuery('#infomiglad_repeating').val();
				 plan_info = info.split(";");
			  }else{
				 plan_info[0] = 'No'; plan_info[1] = '0'; plan_info[2] = '0';
			  }
			}else{
				 var p_info = "#info" + jQuery("input[name='miglad_repeating']:checked").attr('id');
				 var info   = jQuery( p_info ).val();
				 plan_info  = info.split(";");   
			}
		 }else{
			  plan_info[0] = 'No'; plan_info[1]=''; plan_info[2]='';
		 }
				 
		 //alert( JSON.stringify(plan_info) );

		   var card_number = cleanIt( jQuery('#mg_stripe_card_number').val() );
		   card_number = card_number.trim();   
		   
		   var cc_type      = getCreditCardType( card_number ) ;	 
		 
		 if( plan_info[0] != 'No'  )
		 { 
			//alert('repeating stripe');

			var qty = cleanAmount * 100;
			var plan_name = plan_info[0];
			
			jQuery.ajax({
			  type : "post",
			  url :  miglaAdminAjax.ajaxurl,  
			  data :  { 	action		: "miglaA_createSubscription_" , 
							stripeToken	: token,
							session		: sessionid,
													donorinfo	: mdata, 
							plan		: plan_name,
							number		: card_number,
							card_type	: cc_type,
							quantity	: qty,
							nonce		: miglaAdminAjax.nonce
					  },
			  success: function( stripemsg1 ) {
				 if(  stripemsg1 == "1" ){
					 var url = mg_succes_url() + "?" + "thanks=thanks&id=" + sessionid;
					 window.location.replace(url);
				 }else{
					 alert( stripemsg1 );
					 jQuery('#miglastripecheckout').show(); 
					 jQuery('#mg_wait_stripe').hide();
				 }
			  }
			}); //ajax 

		 }else{

			jQuery.ajax({
			  type : "post",
			  url :  miglaAdminAjax.ajaxurl,  
			  data :  { action		: "miglaA_stripeCharge_" , 
						stripeToken	: token, 
						amount		: (cleanAmount*100), 
						donorinfo	: mdata, 
							number		: card_number,
							card_type	: cc_type,					
						session		: sessionid,
						nonce		: miglaAdminAjax.nonce
					  },
			  success: function( stripemsg ) {
				 if( stripemsg == "1" ){
					 var url = mg_succes_url() + "?" + "thanks=thanks&id=" + sessionid;
					 window.location.replace(url);
				 }else{
					 alert( stripemsg );
					 jQuery('#miglastripecheckout').show(); 
					 jQuery('#mg_wait_stripe').hide();
				 }
			  }
			}); //ajax 
			
		 } //IF THEN ELSE

   }//AVS
   
   }//If Response Error

} 

function mg_sendtoStripe(){
   jQuery( "#mg-stripe-payment-form" ).submit();
}

function mg_use_stripe_js()
{
  jQuery("#mg-stripe-payment-form").submit(function(event) {

	//jQuery('#miglastripecheckout').attr("disabled", "disabled");

        Stripe.setPublishableKey( miglaAdminAjax.stripe_PK );

       var countryin 	= getMapValue( 'miglad_country' ); 
	   var statein 		= '';
       if( countryin == 'Canada' ){
          statein = getMapValue( 'miglad_province' );
       }
       if( countryin == 'United States' ){
          statein = getMapValue( 'miglad_state' );
       }
       
       var name_on_card = jQuery('#mg_stripe_card_name').val();
       if( name_on_card == '' )
       { 
           name_on_card =  getMapValue( 'miglad_firstname' ) + " " + getMapValue( 'miglad_lastname' );
       }
       var card_number = cleanIt( jQuery("#mg-stripe-payment-form").find('.card-number').val() );
       card_number = card_number.trim();
       
       Stripe.createToken({
           name            : name_on_card,
		   number          : card_number,
		   cvc             : jQuery("#mg-stripe-payment-form").find('.card-cvc').val(),
		   exp_month       : jQuery("#mg-stripe-payment-form").find('.card-expiry-month').val(),
		   exp_year        : jQuery("#mg-stripe-payment-form").find('.card-expiry-year').val(),
           address_line1   : getMapValue( 'miglad_address' ),
           address_city    : getMapValue( 'miglad_city' ),
           address_country : countryin,
           address_zip     : getMapValue( 'miglad_postalcode' ),
           address_state   : statein
		}, 
          mg_stripeResponseHandler
        );
	
	 return false; 
    });
}

function getCreditCardType(accountNumber)
{

  //start without knowing the credit card type
  var result = "unknowncard";

  //first check for MasterCard
  if (/^(5[0-5]|2[2-7])/.test(accountNumber))
  {
    result = "mastercard";
  }
  //then check for Visa
  else if (/^4/.test(accountNumber))
  {
    result = "visa";
  }
  //then check for AmEx
  else if (/^3[47]/.test(accountNumber))
  {
    result = "amex";
  }
  //then check for Discover
  else if (/^6(?:011|5[0-9]{2}|22[21])[0-9]{3,}/.test(accountNumber))
  {
    result = "discover";
  }
  //then check for JCB 
  else if (/^(?:2131|1800|35[0-9]{3})[0-9]{3,}|3569/.test(accountNumber))
  {
    result = "JCB";
  }
  //then check for Maestro only for Euro
  else if (/^(5018|5020|5038|5612|5893|6304|6759|6761|6762|6763|0604|6390|6705|6777|6766)/.test(accountNumber))
  {
    result = "maestro";
  }
  else if( /(\d{1,4})(\d{1,6})?(\d{1,4})?/.test(accountNumber) )
  {
    result = "dinersclub";
  
  }else if( /^4(026|17500|405|508|844|91[37])/.test(accountNumber) )
  {
    result = "visaelectron";

  }else if( /^600/.test(accountNumber) ){
	result = "forbrugsforeningen";
	
  }else if( /^5019/.test(accountNumber) ){
	result = "dankort";
  
  }else if( /^(62|88)/.test(accountNumber) ){
	result = "unionpay";
  
  }
	
  return result;
}


function cleanIt( dirty ){
  var _dirty = new String(dirty);
  var clean ;
  
  clean = _dirty.replace(/</gi,"");
  clean = clean.replace(/>/gi,"");
  clean = clean.replace(/!/gi,"");
  clean = clean.replace(/&amp/gi,"");
  clean = clean.replace(/&/gi,"");
  clean = clean.replace(/#/gi,"");  
  clean = clean.replace(/"/gi,"");
  clean = clean.replace(/'/gi,"");
  return clean;
}

function isEmailAddress(str) {
   var pattern =/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,5})+$/;
   return pattern.test(str);  // returns a boolean 
}

function isValid(){
   var isVal        = true;
   warning          = ["", "", ""];
   var email_id     = jQuery('#miglad_email');
   var parent_email = email_id.closest('div');
   var email_       = parent_email.find('.miglad_');
   var email        = email_.val();
 
   if( !isEmailAddress(email) ){ 
      isVal = false; warning[1] = jQuery('#mg_warning2').text();
   }
 
  jQuery('#migla_donation_form').find('.migla-panel').each(function(){ 
     var toggle = jQuery(this).find('.mtoggle');
     if( (toggle.length < 1) || ( (toggle.length > 0) && toggle.is(':checked') ) )
     {
        jQuery(this).find('.required').each(function(){
        if(  jQuery(this).attr('type') == 'checkbox' ){
           if(  !jQuery(this).is(':checked') ) 
           {
             jQuery(this).addClass('pink-highlight'); 
			 isVal = false;
             warning[0] = jQuery('#mg_warning1').text();
           }else{
             jQuery(this).removeClass('pink-highlight');
           }		   
        }else{
           if(  jQuery(this).val() == '' ) 
           {
             jQuery(this).addClass('pink-highlight'); isVal = false;
             warning[0] = jQuery('#mg_warning1').text();
           }else{
             jQuery(this).removeClass('pink-highlight');
           }
        }
        });

     }
  });//perpanel

    if( jQuery('.mg_amount_checked').length < 1 )
    {
         amount = jQuery("input[name=miglaAmount]:checked").val();
    }else{
         amount = jQuery('.mg_amount_checked').val();
    }   
   
   if( amount == 'custom' && ( jQuery("#miglaCustomAmount").val() == '' || jQuery("#miglaCustomAmount").val() == '0') ) 
   { 
       isVal = false;
       warning[2] = jQuery('#mg_warning3').text();
       jQuery('#miglaCustomAmount').addClass('pink-highlight');
   }else{
      jQuery('#miglaCustomAmount').removeClass('pink-highlight');
   }

  if( amount == '' || amount == '0' )
  {
      isVal = false;
      warning[2] = jQuery('#mg_warning3').text();
  }

  var campaign = jQuery('select[name=campaign] option:selected').text();

  return isVal;
}

function get_state_code( search_str ){
    var state = '';
	switch( search_str ) 
	{
		case 'Alabama' 	: state = 'AB';
						  break; 
		case 'Alaska' 	: state = 'AK';
						  break; 		
		case 'American Samoa' 	: state = 'AS';
						  break; 		
		case 'Arizona' 	: state = 'AZ';
						  break; 		
		case 'Arkansas' 	: state = 'AR';
						  break; 
		case 'California' 	: state = 'CA';
						  break; 
		case 'Colorado' 	: state = 'CO';
						  break; 
		case 'Connecticut' 	: state = 'CT';
						  break; 
		case 'Delaware' 	: state = 'DE';
						  break; 
		case 'District of Columbia' 	: state = 'DC' ;
						  break; 
		case 'Federated States of Micronesia' 	: state = 'FM';
						  break; 
		case 'Florida' 	: state = 'FL';
						  break; 	
		case 'Georgia' 	: state = 'GA';
						  break; 
		case 'Guam' 	: state = 'GU';
						  break; 			
		case 'Hawaii' 	: state = 'HI';
						  break; 
		case 'Idaho' 	: state = 'ID' ;
						  break; 
		case 'Illinois' 	: state = 'IL';
						  break; 
		case 'Indiana' 	: state = 'IN';
						  break; 
		case 'Iowa' 	: state = 'IA';
						  break; 						  
		case 'Kansas' 	: state = 'KS';
						  break; 
		case 'Kentucky'	: state = 'KY';
						  break; 
		case 'Louisiana'	: state = 'LA';
						  break; 
		case 'Maine' 	: state = 'ME';
						  break; 		
		case 'Marshall Islands'	: state = 'MH';
						  break; 			
		case 'Maryland'	: state = 'MD';
						  break; 
		case 'Massachusetts'	: state = 'MA';
						  break; 
		case 'Michigan'	: state = 'MI' ;
						  break; 
		case 'Minnesota'	: state = 'MN';
						  break; 
		case 'Mississippi'	: state = 'MS';
						  break; 
		case 'Missouri'	: state = 'MO' ;
						  break; 
		case 'Montana'	: state = 'MT';
						  break; 
		case 'Nebraska'	: state = 'NE';
						  break; 
		case 'Nevada'	: state = 'NV';
						  break; 
		case 'New Hampshire'	: state = 'NH';
						  break; 
		case 'New Jersey'	: state = 'NJ';
						  break; 						  
		case 'New Mexico': state = 'NM' ;
						  break; 	
		case 'New York': state = 'NY' ;
						  break; 	
		case 'North Carolina': state = 'NC';
						  break; 	
		case 'North Dakota': state = 'ND' ;
						  break; 	
		case 'Northern Mariana Islands': state = 'MP' ;
						  break; 	
		case 'Ohio': state = 'OH' ;
						  break; 	
		case 'Oklahoma': state = 'OK' ;
						  break; 	
		case 'Oregon': state = 'OR';
						  break; 	
		case 'Palau': state = 'PW' ;
						  break; 	
		case 'Pennsylvania': state = 'PA' ;
						  break; 	
		case 'Puerto Rico': state = 'PR' ;
						  break; 	
		case 'Rhode Island': state = 'RI';
						  break; 	
		case 'South Carolina': state = 'SC' ;
						  break; 	
		case 'South Dakota': state = 'SD' ;
						  break; 	
		case 'Tennessee': state = 'TN' ;
						  break; 	
		case 'Texas': state = 'TX';
						  break; 	
		case 'Utah': state = 'UT';
						  break; 	
		case 'Vermont': state = 'VT';
						  break; 	
		case 'Virgin Islands': state = 'VI';
						  break; 	
		case 'Virginia': state = 'VA' ;
						  break; 	
		case 'Washington': state = 'WA';
						  break; 	
		case 'West Virginia': state = 'WV' ;
						  break; 	
		case 'Wisconsin': state = 'WI' ;
						  break; 	
		case 'Wyoming': state = 'WY' ;
						  break; 	
		case 'Armed Forces Americas': state = 'AA' ;
						  break; 	
		case 'Armed Forces': state = 'AE' ;
						  break; 	
		case 'Armed Forces Pacific'	: state = 'AP' ;
						  break; 						  
	}
	
	return state;
}

function get_province_code( search_str ){
    var province = '';
	switch( search_str ) 
	{
		case 'Alberta' : province = 'AB';
						 break; 
		case 'British Columbia': province = 'BC';
						 break; 
		case 'Manitoba': province = 'MB';
						 break; 
		case 'New Brunswick': province = 'NB' ;
						 break; 
		case 'Newfoundland and Labrador': province = 'NL' ;
						 break; 
		case 'Northwest Territories': province = 'NT' ;
						 break; 
		case 'Nova Scotia': province = 'NS' ;
						 break; 
		case 'Nunavut': province = 'NU' ;
						 break; 
		case 'Ontario': province = 'ON' ;
						 break; 
		case 'Prince Edward Island': province = 'PE' ;
						 break; 
		case 'Quebec': province = 'QC' ;
						 break; 
		case 'Saskatchewan': province = 'SK' ;
						 break; 
		case 'Yukon': province = 'YT' ;
						 break; 
	};

	return province;
}

jQuery(document).ready( function() {

	if( jQuery('#migla_stripe_js').val() == 'yes' )
	{
		mg_use_stripe_js();	
	}
	
  sessionid = jQuery("input[name=migla_session_id]").val();
  repeating = 'no'; anonymous='no';

  jQuery('.miglacheckout').click(function(){
	var isValidCaptcha = true;
	if( jQuery('#migla_use_recaptcha').val() == 'yes'  )
	{
		isValidCaptcha = jQuery('#migla_token_data').val() == grecaptcha.getResponse() && ( grecaptcha.getResponse() != '' ) && typeof grecaptcha.getResponse() !== 'undefined' ;	
	}
	
    if( isValid() )
    {	
	if( isValidCaptcha )
	{
      if( jQuery(this).attr('id') === "miglapaypalcheckout_std" )
      {
	     if( jQuery("input[name='miglaPaypalMethod']:checked").val() == 'paypal_standard' )
		 {
           jQuery('#miglapaypalcheckout_std').hide();
           jQuery('#mg_wait_paypal').show();
		 }else{
           jQuery('#miglapaypalcheckout_std').hide();
           jQuery('#mg_wait_paypal_pro').show();		 
		 }
      }else if( jQuery(this).attr('id') === "miglastripecheckout" )
      {
           jQuery('#miglastripecheckout').hide(); 
           jQuery('#mg_wait_stripe').show();
      }else if( jQuery(this).attr('id') === "miglaofflinecheckout" )
      {
           jQuery('#miglaofflinecheckout').hide(); 
           jQuery('#mg_wait_offline').show();
      }else if( jQuery(this).attr('id') === "miglaauthorizecheckout" )
      {
           jQuery('#miglaauthorizecheckout').hide(); 
           jQuery('#mg_wait_authorize').show();
      }

      mdata.length = 0;
      var item = [];

      //RETRIEVE ALL DEFAULT MANDATORY FOR DONOR
      if( jQuery('.mg_amount_checked').length < 1 )
      {
         amount = jQuery("input[name=miglaAmount]:checked").val();
      }else{
         amount = jQuery('.mg_amount_checked').val();
      }

    if( amount == 'custom') { 
	    amount = cleanIt(jQuery("#miglaCustomAmount").val()); 
	} 

      cleanAmount = amount.replace( jQuery('#miglaThousandSep').val() ,"");
      cleanAmount = amount.replace( jQuery('#miglaDecimalSep').val() ,".");

      var campaign = jQuery('select[name=campaign] option:selected').val();

      item = [ 'miglad_session_id_', sessionid ]; mdata.push( item );
      item = [ 'miglad_session_id', sessionid ]; mdata.push( item );
      item = [ 'miglad_amount', cleanAmount ]; mdata.push( item );
      item = [ 'miglad_campaign', campaign ]; mdata.push( item );
      postdata['amount'] = cleanAmount;
	  
	  item = [ 'miglad_form_id', jQuery('#migla_form_id').val() ]; mdata.push( item );
		  
   //READ LOOP FOR EACH FIELD
   jQuery('#migla_donation_form').find('.migla-panel').each(function()
   { //READ PERPANEL

     var toggle = jQuery(this).find('.mtoggle');

     if( (toggle.length > 0)  )  //IF HAS TOGGLE
     {
       //////////////TOGGLE IS CHECKED/////////////////////////////////////////////////////
       if( toggle.is(':checked') )
       {
       //loop per form group
       jQuery(this).find('.form-group').each(function(){

         var whoami = jQuery(this).find('.idfield').attr('id');  var val = "";

          if(  whoami == 'miglad_amount' || whoami == 'miglad_camount' || whoami == 'miglad_campaign'
               || whoami == 'miglad_anonymous'
          )
          { 

          }else{  

           //certain input type
           var type = jQuery(this).find("input").attr('type'); 

           if( jQuery(this).find('select').length >= 1 ){
               type = 'select';
           }
          
          if( jQuery(this).find("textarea").length >= 1)
          {
              val = cleanIt(jQuery(this).find("textarea").val());

          }else{

            if( type == 'text'){ //text

              val = cleanIt(jQuery(this).find("input").val());  
           
            }else if( type == 'radio' ) { //radio

              val = jQuery(this).find("input[type=radio]:checked").val() ;

            }else if( type == 'select' ) { //select
                var name = jQuery(this).find('select').attr('name');
                val = jQuery(this).find("select[name='" + name + "'] option:selected").val();
            }


            if( jQuery(this).find('input:checkbox').length > 1 )
            {
				var n = jQuery(this).find('input:checkbox').length;
				var i = 0;				
                
				jQuery(this).find('input').each(function(){
					i = i + 1;
					if( jQuery(this).is(':checked') )
                    {
						if( i < n && i > 1 )
						{	
							val = val + jQuery(this).val() + ", ";
						}else{
							val = val + jQuery(this).val() ;							
						}
                    }
                });
            }else if( jQuery(this).find('input:checkbox').length == 1 ){
                     val = 'no';
                     if( jQuery(this).find('input:checkbox').is(':checked') ){
                        val = 'yes';
                     }
            }

         }
 
          //////////push it//////////////////////////////
          val    = cleanIt( val ) ;
          whoami = cleanIt( whoami) ;
          var e = [ whoami , val ];    
          mdata.push(e);
        }
       }); //foreach form loop

       }else{

        //loop per form group
        jQuery(this).find('.form-group').each(function(){
          var e = [ jQuery(this).find('.idfield').attr('id') , "" ];
          mdata.push(e);
        });
       }
      //////////////END OF TOGGLE IS CHECKED/////////////////////////////////////////////////////

     }else{ //does not have toggle 

       
       ////////////////////loop per form group
       jQuery(this).find('.form-group').each(function(){

        var whoami = jQuery(this).find('.idfield').attr('id');   var val = "";
 
          if(  whoami == 'miglad_amount' || whoami == 'miglad_camount' || whoami == 'miglad_campaign' 
               || whoami == 'miglad_anonymous'
          )
        { 
        }else{  
          
           //certain input type
           var type = jQuery(this).find("input").attr('type'); 

           if( jQuery(this).find('select').length >= 1 ){
               type = 'select';
           }
          
          if( jQuery(this).find("textarea").length >= 1)
          {
              val = cleanIt(jQuery(this).find("textarea").val());

          }else{

            if( type == 'text'){ //text

              val = cleanIt(jQuery(this).find("input").val());  
           
            }else if( type == 'radio' ) { //radio

              val = jQuery(this).find("input[type=radio]:checked").val();

            }else if( type == 'select' ) { //select
                var name = jQuery(this).find('select').attr('name');
                val = jQuery(this).find("select[name='" + name + "'] option:selected").val();
            }


             if( jQuery(this).find('input:checkbox').length > 1 )
             {
				var n = jQuery(this).find('input:checkbox').length;
				var i = 0;				
                
				jQuery(this).find('input').each(function(){
					i = i + 1;
					if( jQuery(this).is(':checked') )
                    {
						if( i < n && i > 1 )
						{	
							val = val + jQuery(this).val() + ", ";
						}else{
							val = val + jQuery(this).val() ;							
						}
                    }
                });
             }else if( jQuery(this).find('input:checkbox').length == 1 ){
                     val = 'no';
                     if( jQuery(this).find('input:checkbox').is(':checked') ){
                        val = 'yes';
                     }
             }
         }
          
          ////////// PUSH IT ////////////////////////
          val    = cleanIt( val ) ;
          whoami = cleanIt( whoami) ;
          var e = [ whoami , val ];  
          mdata.push(e);
        }
       }); //foreach form loop

     } 
   }) //READ EACH FIELD

   var idx1 = getMapIndex('miglad_state');
   var idx2 = getMapIndex('miglad_province');
   var c = getMapValue( 'miglad_country' );
   if( c == 'Canada' )
   {
       mdata[idx1][1] = "";  
   }else if( c == 'United States' ){
       mdata[idx2][1] = "";
   }else{
       mdata[idx1][1] = ""; 
       mdata[idx2][1] = ""; 
   }
   

   var m = getMapValue( 'miglad_memorialgift' );
   var hc = getMapValue( 'miglad_honoreecountry' );
   var idx3 = getMapIndex('miglad_honoreestate');
   var idx4 = getMapIndex('miglad_honoreeprovince');
   if( m == 'yes')
   {
      mdata[idx3][1] = ""; 
      mdata[idx4][1] = "";   
      var idx5 = getMapIndex('miglad_honoreecountry'); 
      mdata[idx5][1] = "";  
   }else{
      if( hc == 'Canada' )
      {
         mdata[idx3][1] = "";  
      }else if( hc == 'United States' ){
         mdata[idx4][1] = "";
      }
      else{
         mdata[idx3][1] = ""; 
         mdata[idx4][1] = ""; 
      }   
   }

      var anon = jQuery("#migla_donation_form").find("input[name='miglad_anonymous']");
      if( anon.is(':checked') ){
         item = [ 'miglad_anonymous', 'yes' ]; mdata.push( item );
      }else{
         item = [ 'miglad_anonymous', 'no' ]; mdata.push( item );
      }
	  

   //GET the repeating
        var isRepeat = "";
        var repeatingField = jQuery("#migla_donation_form").find("input[name='miglad_repeating']");
        if( repeatingField.length > 0 )
        {
            if( repeatingField.attr("type") == "checkbox" )
            {
               if( jQuery("#migla_donation_form").find("input[name='miglad_repeating']").is(":checked") ){
                   var info = jQuery('#infomiglad_repeating').val();
                   plan_info = info.split(";");
                }else{
                   plan_info[0] = 'No'; plan_info[1] = '0'; plan_info[2] = '0';
                }
            }else{
                var p_info = "#info" + jQuery("input[name='miglad_repeating']:checked").attr('id');
                var info   = jQuery( p_info ).val();
                plan_info  = info.split(";");   
            }
        }else{
              plan_info[0] = 'No'; plan_info[1]=''; plan_info[2]=''; isRepeat = "no";
        }     

        if( plan_info[0] == 'No' ){
             isRepeat = "no";

             var idx5 = getMapIndex('miglad_repeating');
             mdata[idx5][1] = 'no';  
        }else{
             recurring_time = plan_info[1];
             recurring_period = plan_info[2];
 
             var idx5 = getMapIndex('miglad_repeating');
             mdata[idx5][1] = plan_info[3];  

        } 
		
	//data to thank you page	
	postdata['amount'] = cleanAmount;	
    var idx201 = getMapIndex('miglad_firstname'); 
    postdata['firstname']  = mdata[idx201][1];  
    var idx202 = getMapIndex('miglad_lastname'); 
    postdata['lastname']  = mdata[idx202][1];  

   //alert(JSON.stringify(mdata));
	  
   if( jQuery(this).attr('id') == 'miglapaypalcheckout_std' )
   {
	   var paypal_method = jQuery("input[name='miglaPaypalMethod']:checked").val();
	   
	   if( paypal_method == 'paypal_standard' )
	   {			
			//alert('paypal std');
	   
			  /////HIDDEN FORM////////////////////
			var hiddenForm = jQuery('#migla-hidden-form');
                        var paypal_fname = getMapValue( 'miglad_firstname' );
			var paypal_lname = getMapValue( 'miglad_lastname' );
	
			hiddenForm.find('input[name="first_name"]').val(paypal_fname);
			hiddenForm.find('input[name="last_name"]').val(paypal_lname);
			hiddenForm.find('input[name="address1"]').val(  getMapValue( 'miglad_address' ) );
			hiddenForm.find('input[name="city"]').val(  getMapValue( 'miglad_city' ) );
			hiddenForm.find('input[name="zip"]').val(  getMapValue( 'miglad_postalcode' ) );
			hiddenForm.find('input[name="country"]').val( c );
			   
			if( c == 'Canada' ){ 
				   hiddenForm.find('input[name="state"]').val( get_province_code( getMapValue( 'miglad_province' ) ) );
			}else if( c == 'United States' ){
				   hiddenForm.find('input[name="state"]').val(  get_state_code( getMapValue( 'miglad_state' ) )  );
			}

			hiddenForm.find('input[name="email"]').val( getMapValue( 'miglad_email' ));
			hiddenForm.find('input[name="custom"]').val(sessionid);
			hiddenForm.find('input[name="amount"]').val(cleanAmount);
			
            if( jQuery('#migla_paypal_fec').val() == 'yes' )
			{			
				var paypalName = paypal_fname + " " + paypal_lname ;
				hiddenForm.find('input[name="os0"]').val(paypalName);

                var paypalemployer = getMapValue( 'miglad_employer' );
                var occupation     = '';
                if( paypalemployer != '' )
				{
				    occupation =  paypalemployer  + "," + getMapValue( 'miglad_occupation' );
                }else{
					 occupation =  getMapValue( 'miglad_occupation' );	
				}
				hiddenForm.find('input[name="os1"]').val(occupation);
			}
			
			hiddenForm.find('input[name="os2"]').val( getMapValue( 'miglad_campaign' ) );			

			if ( isRepeat == 'no') {
				hiddenForm.find( 'input[name="src"]' ).remove();
				hiddenForm.find( 'input[name="p3"]' ).remove();
				hiddenForm.find( 'input[name="t3"]' ).remove();
				hiddenForm.find( 'input[name="a3"]' ).remove();
			} else {
				hiddenForm.find( 'input[name="cmd"]' ).val( '_xclick-subscriptions' );
				hiddenForm.find( 'input[name="p3"]' ).val( '1' );
						
				hiddenForm.find( 'input[name="p3"]' ).val( recurring_time );

						switch( recurring_period )
						{  
					  case 'day' : hiddenForm.find( 'input[name="t3"]' ).val( 'D' ); break;
					  case 'week' : hiddenForm.find( 'input[name="t3"]' ).val( 'W' ); break;
					  case 'month' : hiddenForm.find( 'input[name="t3"]' ).val( 'M' ); break;
					  case 'year' : hiddenForm.find( 'input[name="t3"]' ).val( 'Y' ); break;
						}
					  
				hiddenForm.find( 'input[name="a3"]' ).val( cleanAmount );
				hiddenForm.find( 'input[name="amount"]' ).remove();
			}
			
			jQuery.ajax({
				type : "post",
				url  :  miglaAdminAjax.ajaxurl,  
				data :  { action    : 'miglaA_checkout', 
						  donorinfo : mdata, 
						  session   : sessionid,
						  nonce		: miglaAdminAjax.nonce
								  },
				success : function( msg ) {
					if( msg == '')
					{
						sendtoPaypal();
					}else{
						alert(msg);
					}
				}
			}); //ajax 	
			

			
	   }else{
	   
	   	//alert('paypal pro');

			var cc_number    = jQuery('#mg_paypalpro_card_number').val();
			var cc_type      = getCreditCardType( cc_number ) ;
			var cc_month     = jQuery('#mg_paypalpro_month').val();
			var cc_year      = jQuery('#mg_paypalpro_year').val(); 
			var cc_cvc       = jQuery('#mg_paypalpro_cvc').val();
			var cc_firstname = jQuery('#mg_paypalpro_split_firstname').val();
			var cc_lastname  = jQuery('#mg_paypalpro_split_lastname').val();
			if( cc_firstname == '' ){ cc_firstname = getMapValue( 'miglad_firstname' ); }		
			if( cc_lastname == '' ){ cc_lastname = getMapValue( 'miglad_lastname' ); }	

			if ( isRepeat == 'no' ) {

			     var mg_paypal_action = 'miglaA_paypalDirectPaymentExecutor';

			     if(jQuery('#migla_paypal_pro_type').val()=='paypal_flow')
				 {
				     mg_paypal_action = 'miglaA_paypalFlow';
				 }
			
				   jQuery.ajax({
					   type : "post",
					   url :  miglaAdminAjax.ajaxurl,  
					   data :  { 
						  action    : mg_paypal_action,
						  donorinfo : mdata, 
						  session   : sessionid,
						  ccnumber  : cc_number,
						  cctype    : cc_type ,
						  ccmonth   : cc_month,
						  ccyear    : cc_year,
						  cccvc     : cc_cvc,
						  ccfname   : cc_firstname, 
						  cclname   : cc_lastname,
						  nonce		: miglaAdminAjax.nonce
						  },
					   success: function( promsg1 ) {
						 if( promsg1 == '1' ){						 
							 var url = mg_succes_url() + "thanks=thanks" ;
								url = url + '&firstname=' + postdata['firstname'];
								url = url + '&lastname=' + postdata['lastname'] ;
								url = url + '&amount=' + postdata['amount'] ;
							  window.location.replace(url);
						 }else{
							  alert(promsg1);
							  jQuery('#miglapaypalcheckout_std').slideDown();
							  jQuery('#mg_wait_paypal').hide();
							  jQuery('#mg_wait_paypal_pro').hide();
						 }
					   }
				  }); //ajax 

				}else{

			     var mg_paypal_action = 'miglaA_paypalSubscriptionExecutor';
				 
			     if(jQuery('#migla_paypal_pro_type').val()=='paypal_flow')
				 {
				     mg_paypal_action = 'miglaA_paypalFlow_recurring';

					   jQuery.ajax({
						   type : "post",
						   url :  miglaAdminAjax.ajaxurl,  
						   data :  { action    : mg_paypal_action,
							  donorinfo : mdata, 
							  session   : sessionid,
							  ccnumber  : cc_number,
							  cctype    : cc_type ,
							  ccmonth   : cc_month,
							  ccyear    : cc_year,
							  cccvc     : cc_cvc,
							  time      : recurring_time,
							  period    : recurring_period,
							  ccfname   : cc_firstname, 
							  cclname   : cc_lastname,
							   nonce	: miglaAdminAjax.nonce
							  },
						   success: function( promsg2 ) 
						   {
							if( promsg2 == '1' ){
								  var url = mg_succes_url() + "thanks=thanks";
								  url = url + '&firstname=' + postdata['firstname'];
								  url = url + '&lastname=' + postdata['lastname'] ;
								  url = url + '&amount=' + postdata['amount'] ;					  
								  window.location.replace(url);
							 }else{
									alert( promsg2 );
									jQuery('#miglapaypalcheckout_std').slideDown();
									jQuery('#mg_wait_paypal').hide();
									jQuery('#mg_wait_paypal_pro').hide();	
							 }
							 
						   }
					  }); //ajax 					 
					 
				 }else {
					//alert(mg_paypal_action);
					   jQuery.ajax({
						   type : "post",
						   url :  miglaAdminAjax.ajaxurl,  
						   data :  { action    : mg_paypal_action,
							  donorinfo : mdata, 
							  session   : sessionid,
							  ccnumber  : cc_number,
							  cctype    : cc_type ,
							  ccmonth   : cc_month,
							  ccyear    : cc_year,
							  cccvc     : cc_cvc,
							  time      : recurring_time,
							  period    : recurring_period,
							  ccfname   : cc_firstname, 
							  cclname   : cc_lastname,
							   nonce	: miglaAdminAjax.nonce
							  },
						   success: function( promsg2 ) 
						   {
								if( promsg2 == '1' ){
									  var url = mg_succes_url() + "thanks=thanks";
									  url = url + '&firstname=' + postdata['firstname'];
									  url = url + '&lastname=' + postdata['lastname'] ;
									  url = url + '&amount=' + postdata['amount'] ;					  
									  window.location.replace(url);
								 }else{
										alert( promsg2 );
										jQuery('#miglapaypalcheckout_std').slideDown();
										jQuery('#mg_wait_paypal').hide();
										jQuery('#mg_wait_paypal_pro').hide();	
								 }
						   }
							 
					  }); //ajax 					
					 }
		
				}				
				
		 }//PayPal switch
		 	 
   }else if( jQuery(this).attr('id') == 'miglastripecheckout' ){

   		//alert('stripe');
   
	if( jQuery('#migla_stripe_js').val() == 'yes' )
	{   
	    mg_sendtoStripe();   
	
	}else{
	
       var countryin = getMapValue( 'miglad_country' ); var statein = '';
       if( countryin == 'Canada' ){
          statein = getMapValue( 'miglad_province' );
       }
       if( countryin == 'United States' ){
          statein = getMapValue( 'miglad_state' );
       }
       
       var name_on_card = jQuery('#mg_stripe_card_name').val();
       if( name_on_card == '' )
       { 
           name_on_card =  getMapValue( 'miglad_firstname' ) + " " + getMapValue( 'miglad_lastname' );
       }
       var card_number = cleanIt( jQuery('#mg_stripe_card_number').val() );
       card_number = card_number.trim();   
	   
	   var cc_type      = getCreditCardType( card_number ) ;
		
     var repeatingField = jQuery("#migla_donation_form").find("input[name='miglad_repeating']");
     if( repeatingField.length > 0 )
     {
        if( repeatingField.attr("type") == "checkbox" ){
          if( jQuery("#migla_donation_form").find("input[name='miglad_repeating']").is(":checked") ){
             var info = jQuery('#infomiglad_repeating').val();
             plan_info = info.split(";");
          }else{
             plan_info[0] = 'No'; plan_info[1] = '0'; plan_info[2] = '0';
          }
        }else{
             var p_info = "#info" + jQuery("input[name='miglad_repeating']:checked").attr('id');
             var info   = jQuery( p_info ).val();
             plan_info  = info.split(";");   
        }
     }else{
          plan_info[0] = 'No'; plan_info[1]=''; plan_info[2]='';
     }
             
     if( plan_info[0] != 'No'  )
     { 
        var qty = cleanAmount * 100;
        var plan_name = plan_info[0];
        
        jQuery.ajax({
          type : "post",
          url :  miglaAdminAjax.ajaxurl,  
          data :  {   action	: "miglaA_createSubscription" ,
                      donorinfo	: mdata, 
                      session	: sessionid,
                      plan		: plan_name,
                      quantity	: qty,
					name            : name_on_card,
					number          : card_number,
					card_type		: cc_type ,
					cvc             : cleanIt( jQuery('#mg_stripe_cvc').val() ),
					exp_month       : cleanIt( jQuery('#mg_stripe_month').val() ),
					exp_year        : cleanIt( jQuery('#mg_stripe_year').val() ),
					address_line1   : getMapValue( 'miglad_address' ),
					address_city    : getMapValue( 'miglad_city' ),
					address_country : countryin,
					address_zip     : getMapValue( 'miglad_postalcode' ),
					address_state   : statein,
					 nonce			: miglaAdminAjax.nonce
                  },
          success: function( stripemsg1 ) {
             if( stripemsg1 == "1" ){
                 var url = mg_succes_url() + "thanks=thanks" ;
							  url = url + '&firstname=' + postdata['firstname'];
							  url = url + '&lastname=' + postdata['lastname'] ;
							  url = url + '&amount=' + postdata['amount'] ;		 
                 window.location.replace(url);
             }else{
                 alert( stripemsg1 );
                 jQuery('#miglastripecheckout').show(); 
                 jQuery('#mg_wait_stripe').hide();
             }
          }
        }); //ajax 

     }else{

        jQuery.ajax({
          type : "post",
          url :  miglaAdminAjax.ajaxurl,  
          data :  { action			: "miglaA_stripeCharge" , 
                    donorinfo		: mdata,
                    amount			: (cleanAmount*100), 
                    session			: sessionid,
					name            : name_on_card,
					number          : card_number,
					card_type		: cc_type ,
					cvc             : cleanIt( jQuery('#mg_stripe_cvc').val() ),
					exp_month       : cleanIt( jQuery('#mg_stripe_month').val() ),
					exp_year        : cleanIt( jQuery('#mg_stripe_year').val() ),
					address_line1   : getMapValue( 'miglad_address' ),
					address_city    : getMapValue( 'miglad_city' ),
					address_country : countryin,
					address_zip     : getMapValue( 'miglad_postalcode' ),
					address_state   : statein,
					 nonce		    : miglaAdminAjax.nonce
                  },
          success: function( stripemsg ) {
             if( stripemsg == "1" ){
                 //alert( stripemsg );
                 var url = mg_succes_url() + "thanks=thanks";
							  url = url + '&firstname=' + postdata['firstname'];
							  url = url + '&lastname=' + postdata['lastname'] ;
							  url = url + '&amount=' + postdata['amount'] ;		 
                 window.location.replace(url);
             }else{
                 alert( stripemsg );
                 jQuery('#miglastripecheckout').show(); 
                 jQuery('#mg_wait_stripe').hide();
             }
          }
        }); //ajax 

     } //IF THEN ELSE
	}//IF USING STRIPE JS

   }else if( jQuery(this).attr('id') == 'miglaofflinecheckout' ) 
   {
		//alert('offline');
   
      jQuery.ajax({
         type : "post",
         url :  miglaAdminAjax.ajaxurl,  
         data :  { action:"miglaA_checkout_offline" , 
                    donorinfo	: mdata, 
                    session		: sessionid,
					 nonce		: miglaAdminAjax.nonce
                  },
         success: function(msg4) {

            myVar = setTimeout(function(){
                    jQuery('#mg_wait_offline').hide();
                    jQuery('#mg_thankyou_offline').show();
            }, 3000 );
           
         }
      }); //ajax 

   }else if( jQuery(this).attr('id') == 'miglaauthorizecheckout' ) 
   {
		//alert('authorize');
		
      var state_saved = "";
      var c = getMapValue( 'miglad_country' );
      if( c == 'Canada' )
      {
         state_saved = getMapValue('miglad_province');  
      }else if( c == 'United States' ){
         state_saved = getMapValue('miglad_state');
      }
	  
        var cc_number    = jQuery('#mg_authorize_card_number').val();
        var cc_type      = getCreditCardType( cc_number ) ;
        var cc_month     = jQuery('#mg_authorize_month').val();
        var cc_year      = jQuery('#mg_authorize_year').val(); 
        var cc_cvc       = jQuery('#mg_authorize_cvc').val();
        var cc_firstname = jQuery('#mg_authorize_split_firstname').val();
        var cc_lastname  = jQuery('#mg_authorize_split_lastname').val();
        if( cc_firstname == '' ){ cc_firstname = getMapValue( 'miglad_firstname' ); }		
        if( cc_lastname == '' ){ cc_lastname = getMapValue( 'miglad_lastname' ); }		  

      if( isRepeat == 'no')
      {

            jQuery.ajax({
                     type : "post",
                     url :  miglaAdminAjax.ajaxurl,  
                     data :  {  action:"miglaA_authorize_send" ,
                                donorinfo       : mdata,
                                session         : sessionid,
                                firstname       : cc_firstname,
                                lastname        : cc_lastname,
	                            card_number     : cc_number ,
								card_type		: cc_type,
	                            cvc             : cc_cvc,
	                            exp_month       : cc_month ,
	                            exp_year        : cc_year ,
								state           : state_saved ,
								 nonce			: miglaAdminAjax.nonce
                     },
                     success: function(msg_auth) {
                       if( msg_auth == '1' ){
                           var url = mg_succes_url() + "thanks=thanks";
							  url = url + '&firstname=' + postdata['firstname'];
							  url = url + '&lastname=' + postdata['lastname'] ;
							  url = url + '&amount=' + postdata['amount'] ;					   
                           window.location.replace(url);
                       }else{
                           alert(msg_auth);
                           jQuery('#miglaauthorizecheckout').show();
                           jQuery('#mg_wait_authorize').hide();
                       }          
                     }
             }); //ajax 

      }else{

         jQuery.ajax({
            type : "post",
            url :  miglaAdminAjax.ajaxurl,  
            data :  { action:"miglaA_ARB_create",
                        donorinfo      : mdata,
                        session         : sessionid,
                        donorinfo       : mdata,
                        session         : sessionid,
                        firstname       : cc_firstname,
                        lastname        : cc_lastname,
	                    card_number     : cc_number ,
						card_type		: cc_type,						
	                    cvc             : cc_cvc,
	                    exp_month       : cc_month ,
	                    exp_year        : cc_year ,
                        state           : state_saved,
                        intervalLength  : recurring_time,
                        intervalUnit    : recurring_period,
						nonce			: miglaAdminAjax.nonce
                  },
            success: function(msgARB) {
               if( msgARB == '1' ){
                  var url = mg_succes_url() + "thanks=thanks" ;
							  url = url + '&firstname=' + postdata['firstname'];
							  url = url + '&lastname=' + postdata['lastname'] ;
							  url = url + '&amount=' + postdata['amount'] ;		  
                  window.location.replace(url);
               }else{
                  alert(msgARB);
                  jQuery('#miglaauthorizecheckout').show();
                  jQuery('#mg_wait_authorize').hide();
               }  
            }
         }); //ajax 

      }
   }
   
   }else{
		alert('Please click the captcha');
   }
   
 }else{
 
   var warn = warning[0];
   if( warning[1] != "" && warn != ""){
       warn = warn + "\n" + warning[1];
   }

   if( warning[1] != "" && warn == ""){
       warn = warn +  warning[1];
   }

   if( warning[2] != "" && warn != ""){
       warn = warn + "\n" + warning[2];
   }

   if( warning[2] != "" && warn == ""){
       warn = warn +  warning[2];
   }

   alert(warn);

 }

}); //Donate Button Clicked


  jQuery('#mg_stripe_card_number').keyup(function(e){
     var cc_number = jQuery(this).val();
     cc_number = cc_number.trim();
     var card_type = getCreditCardType( cc_number ) ; 
     var p         = jQuery(this).closest('div');   
     var icon_span = p.find('span.mg_creditcardicons'); 
     icon_span.removeClass();
     icon_span.addClass( 'mg_creditcardicons' );
     icon_span.addClass( ('mg_stripe-' + card_type.toLowerCase() ) );
  });

  jQuery('#mg_paypalpro_card_number').keyup(function(e){
     var cc_number = jQuery(this).val();
     cc_number = cc_number.trim();
     var card_type = getCreditCardType( cc_number ) ; 
     var p         = jQuery(this).closest('div');   
     var icon_span = p.find('span.mg_creditcardicons_paypal'); 
     icon_span.removeClass();
     icon_span.addClass( 'mg_creditcardicons_paypal' );
     icon_span.addClass( ('mg_paypal-' + card_type.toLowerCase() ) );
  });

  jQuery('#mg_authorize_card_number').keyup(function(e){
     var cc_number = jQuery(this).val();
     cc_number = cc_number.trim();
     var card_type = getCreditCardType( cc_number ) ; 
     var p         = jQuery(this).closest('div');   
     var icon_span = p.find('span.mg_creditcardicons_authorize'); 
     icon_span.removeClass();
     icon_span.addClass( 'mg_creditcardicons_authorize' );
     icon_span.addClass( ('mg_authorize-' + card_type.toLowerCase() ) );
  });
  
  /*
  jQuery('.migla_amount_choice').click(function(){
	
	jQuery('.migla_amount_choice').each(function(){
		var parent_label = jQuery(this).closest('label');
		if( jQuery(this).is(':checked') ){
			parent_label.css('background-color', jQuery('#mg_level_active_color').val());
		}else{
			parent_label.css('background-color', jQuery('#mg_level_color').val());				
		}
	});
  
  });
  */
  
  jQuery('.migla_amount_lbl').click(function(){
  
	var me = jQuery(this);
  
	jQuery('.migla_amount_lbl').each(function(){
		jQuery(this).css('background-color', jQuery('#mg_level_color').val());				
	});

	me.css('background-color', jQuery('#mg_level_active_color').val());
	
  });
  
});   //Document

function mg_succes_url(){

    var successUrl = new String(miglaAdminAjax.return_url);    

    if( jQuery('#migla_thankyou_url').val() != '' )
    {
      successUrl = jQuery('#migla_thankyou_url').val();
    }

	if ( successUrl.search( "\\?" ) < 0 ){
	    successUrl = successUrl + "?";
	}else{
		successUrl = successUrl + "&";
	}
   return  successUrl;
}