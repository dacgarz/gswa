//THEME SETTINGS

function convertHex(hexa,opacity){
    var hexa = hexa.replace('#','');
    r = parseInt(hexa.substring(0,2), 16);
    g = parseInt(hexa.substring(2,4), 16);
    b = parseInt(hexa.substring(4,6), 16);

    result = 'rgba('+r+','+g+','+b+','+opacity/100+')';
    return result;
}


jQuery(document).ready( function() {

   jQuery('.mg-color-field').each(function(){

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
                        }
                        if( typeof console === 'object' ) {
                            
                        }
                    },
                    theme: 'bootstrap'
                });

  });

});