var migla_circles = [];

jQuery(document).ready( function() { 

    jQuery('.migla_circle_wrapper').each(function( ){
           //alert(i); i = i + 1;
	   var temp      = {};
           var circle    = jQuery(this).find('.migla_circle_bar');
	   temp.id       = '#' + circle.attr('id');
	   temp.value    = jQuery(this).find('.migla_circle_value').val();
	   temp.fill     = jQuery(this).find('.migla_circle_fill').val();
	   temp.reverse  = jQuery(this).find('.migla_circle_reverse').val();
	   temp.start_angle  = jQuery(this).find('.migla_circle_start_angle').val();
	   temp.animation    = jQuery(this).find('.migla_circle_animation').val();
	   temp.inside       = jQuery(this).find('.migla_circle_inside').val();
	   temp.size         = jQuery(this).find('.migla_circle_size').val();
	   temp.line_cap     = jQuery(this).find('.migla_circle_line_cap').val();	   
	   
	   migla_circles.push(temp);
     });
	
     //alert( JSON.stringify(migla_circles) );

     for( key in migla_circles){

		 var _cvalue  = migla_circles[key]['value'];
		 var _csize  = migla_circles[key]['size'];
		 
		 var _fill    = migla_circles[key]['fill'];
		 var _efill   = "";

		 var _reverse = migla_circles[key]['reverse'];
		 if( _reverse == 'yes' ){ _reverse = true; }else{ _reverse = false; }

		 var _radian = Number( migla_circles[key]['start_angle'] );
		 _radian = -Math.PI / 4 * _radian ; 

		 var _circleanimation = String( migla_circles[key]['animation'] ); 
		 var _inside = String( migla_circles[key]['inside'] ); 
		 
		 var _cthickness  = migla_circles[key]['thickness'];		 
		 var _clinecap  = migla_circles[key]['line_cap'];
		 
		 var circle = jQuery( migla_circles[key]['id'] );

		 if( _circleanimation == 'normal' )
		 {
			  circle.circleProgress({
				 value       : _cvalue,
				 size        : _csize,
				 fill        : {
							   color: _fill
							  },
				 lineCap     : String( _clinecap ),
				 start_angle : _radian,
				 reverse     : _reverse,
				 thickness   : _cthickness
			  }).on('circle-animation-progress', function(event, progress, stepValue) {
				  if( _inside == 'progress' )
				  {
					 // jQuery(this).find('#migla_circle_text').html(parseInt(100 * progress) + '<i>%</i>');
				  }else if ( _inside == 'percentage' )
				  {
					 // jQuery(this).find('#migla_circle_text').html( _cvalue + '<i>%</i>');
				  }
			  });

		 }else if( _circleanimation == 'back_forth' )
		 {
		   circle.circleProgress({
				 value       : _cvalue,
				 size        : _csize,
				 fill        : {
							   color: _fill
							  },
				 lineCap     : String( _clinecap ),
				 start_angle : _radian,
				 reverse     : _reverse,
				 thickness   : _cthickness
			}).on('circle-animation-progress', function(event, progress, stepValue) {
				  if( _inside == 'progress' )
				  {
					  //jQuery(this).find('#migla_circle_text').html(parseInt(100 * progress) + '<i>%</i>');
				  }else if ( _inside == 'percentage' )
				  {
					  //jQuery(this).find('#migla_circle_text').html( _cvalue + '<i>%</i>');
				  }			   				  
			});

			   setTimeout(function() { 
                                       circle.circleProgress('value', 0.7); 
                           }, 1000);
			   setTimeout(function() { circle.circleProgress('value', 1.0); }, 1100);
 			   setTimeout(function() { circle.circleProgress('value', _cvalue); }, 2100);


		 }else{
		   circle.circleProgress({
				 value       : _cvalue,
				 size        : _csize,
				 fill        : {
							   color: _fill
							  },
				 lineCap     : String( _clinecap ),
				 start_angle : _radian,
				 reverse     : _reverse,
				 thickness   : _cthickness,
				animation   : false
			}).on('circle-animation-progress', function(event, progress, stepValue) {
				  if( _inside == 'progress' )
				  {
					  //jQuery(this).find('#migla_circle_text').html(parseInt(100 * progress) + '<i>%</i>');
				  }else if ( _inside == 'percentage' )
				  {
					  //jQuery(this).find('#migla_circle_text').html( _cvalue + '<i>%</i>');
				  }
			}); 
		 }  
    }
 

});