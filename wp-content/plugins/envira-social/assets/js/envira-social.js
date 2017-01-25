jQuery( document ).ready( function( $ ) {

	$( document ).on( 'click', '.envira-social-buttons a', function( e ) {

		e.preventDefault();

		// Get some attributes
		var hash 		= window.location.hash,
			url 		= $(this).attr('href'),
			width 		= $(this).parent().data('width'),
			height 		= $(this).parent().data('height'),
			network 	= $(this).parent().data('network'),
			deeplinking = $(this).parent().data('deeplinking');

		// If there's pagination, grab the current page
		var envira_pagination_page = false;
		var page_string = false;

		if ( $('.envira-pagination').length > 0 ) {
			// attempt to get page via URL querystring
			envira_pagination_page = envira_social_get_query_arg( 'page', $( this ).attr( 'href' ) );
			if ( !envira_pagination_page ) {
				// attempt to get page via pagination bar
				envira_pagination_page = $(this).closest('.envira-gallery-wrap').find('.envira-pagination').data('page');
				if ( !envira_pagination_page ) {
					// attempt to get page via fancybox data value, which should be added upon afterShow
					envira_pagination_page = $('img.envirabox-image').data('pagination-page');
				}
			}
		}		

		if ( envira_pagination_page ) {
			// now turn this into a string we can add after the url
			var page_string = envira_pagination_page + '/';
		} else {
			var page_string = '';
		}

		// Build links depending if the user is in the lightbox or not		

		if ( url == '#' ) { /* LIGHTBOX / MODAL */
			
			// prior we grabbed  some information form the img.envirabox-image tag... but switched to .envirabox-wrap because
			// there isn't an img.envirabox-image tag in every lightbox instance (prime example: embedded videos)
			// 
			// link_source is now an abstracted var so we can change the source in the future without hassle
			
			var link_source     	= $('.envirabox-wrap'),
				image 				= link_source.data('envira-src'), /* $('img.envirabox-image').attr('src'), */
				title 				= link_source.data('envira-title'),
				caption 			= link_source.data('envira-caption'),
				gallery_id 			= link_source.data('envira-gallery-id'),
				gallery_item_id 	= link_source.data('envira-item-id'),
				rand 				= Math.floor( ( Math.random() * 10000 ) + 1 ),
				envira_permalink 	= envira_social_get_query_arg( 'envira', window.location.href );

				if ( typeof envira_permalink !== "undefined" && envira_permalink !== null ) {
					envira_permalink = 'envira=' + envira_permalink;
				} else {
					envira_permalink = '';
				}

				// If WP_DEBUG enabled, output error details
				if ( envira_social.debug ) {
					console.log( 'detected gallery_id (lightbox):' + gallery_id );
					console.log( 'detected gallery_item_id (lightbox):' + gallery_item_id );
					console.log( 'detected hash:' + hash );
					console.log( 'detected envira_permalink:' + envira_permalink );
				}

				// if there are undefined vars for some reason, make them empty strings
				if (typeof title === "undefined") {
					caption = '';
				}
				if (typeof caption === "undefined") {
					caption = '';
				}

				// generate base_link, detect deeplinking
				var base_link   = ( hash.length > 0 ) ? window.location.href.split('#')[0] : window.location.href.split('?')[0];
				
				// If WP_DEBUG enabled, output error details
				if ( envira_social.debug ) {
					console.log('base_link (lightbox):' + base_link);
				}
				
				// "clean" the base_link var
				base_link 		= envira_clean_base_link( base_link );

				// If WP_DEBUG enabled, output error details
				if ( envira_social.debug ) {
					console.log('base_link cleaned (lightbox):' + base_link);
				}

				// generate the actual link based on the base_link, depending if deeplinking/hash exists
				link			= ( hash.length > 0 ) ? base_link + '?envira_social_gallery_id=' + gallery_id + '&envira_social_gallery_item_id=' + gallery_item_id + '&rand=' + rand + '&' + envira_permalink + hash: base_link + page_string + '?envira_social_gallery_id=' + gallery_id + '&envira_social_gallery_item_id=' + gallery_item_id + '&rand=' + rand + '&' + envira_permalink;

				// If WP_DEBUG enabled, output error details
				if ( envira_social.debug ) {
					console.log('link (lightbox):' + link);
				}
								
			switch ( network ) {
				case 'facebook':

					var quote 			= '',
						facebook_text 	= '',
						title 			= '',
						tags 			= '';

					if (typeof $(this).attr('data-envira-social-facebook-text') !== "undefined") {
						facebook_text	 		= decodeURIComponent($(this).data('envira-social-facebook-text'));
						facebook_text 			= facebook_text.replace(new RegExp("\\+","g"),' ');
						if ( $.trim(facebook_text) == '' ) { facebook_text = ' '; } // blank spaces force Facebook to not display description
						// If WP_DEBUG enabled, output error details
						if ( envira_social.debug ) {
							console.log ('updating facebook_text');
							console.log (facebook_text);
						}						
					}

					if (typeof $(this).attr('data-envira-facebook-quote') !== "undefined") {
						quote	 		= decodeURIComponent($(this).data('envira-facebook-quote'));
						quote 			= quote.replace(new RegExp("\\+","g"),' ');
						// If WP_DEBUG enabled, output error details
						if ( envira_social.debug ) {
							console.log ('updating quote');
							console.log (quote);
						}

					}

					if (typeof $(this).attr('data-envira-title') !== "undefined") {
						title	 		= decodeURIComponent($(this).data('envira-title'));
						title 				= title.replace(new RegExp("\\+","g"),' ');
						// If WP_DEBUG enabled, output error details
						if ( envira_social.debug ) {
							console.log ('updating title');
							console.log (title);
						}
					}

					if (typeof $(this).attr('data-facebook-tags-manual') !== "undefined") {
						tags	 		= decodeURIComponent($(this).data('facebook-tags-manual'));
						tags 			= tags.replace(new RegExp("\\+","g"),' ');
						// remove any dashes, since FB doesn't like them
						tags 			= tags.replace(/-/g, '');
						// If WP_DEBUG enabled, output error details
						if ( envira_social.debug ) {
							console.log ('updating tags');
							console.log (tags);
						}
					} else {
						if ( envira_social.debug ) {
							console.log ('updating tags - missing');
							console.log ( $(this).attr('data-facebook-tags-manual') );
						}		
					}

					url = 'https://www.facebook.com/dialog/feed?app_id=' + envira_social.facebook_app_id + '&display=popup&link=' + link + '&picture=' + image + '&name=' + title + '&caption=' + caption + '&description=' + facebook_text + '&redirect_uri=' + link + '#envira_social_sharing_close';
                    break;

				case 'twitter':

					var twitter_text = '';
					// caption and link var is taken from the 'general' caption above

					if (typeof $(this).attr('data-envira-social-twitter-text') !== "undefined") {
						twitter_text	 		= decodeURIComponent($(this).data('envira-social-twitter-text'));
						twitter_text 			= twitter_text.replace(new RegExp("\\+","g"),' ');
						console.log ('updating twitter_text');
					}

					// If WP_DEBUG enabled, output error details
					if ( envira_social.debug ) {
						console.log ('updating twitter_text');
						console.log (twitter_text);
					}

					url = 'https://twitter.com/intent/tweet?text=' + $.trim( $.trim(caption) + ' ' + $.trim(twitter_text) ) + '&url=' + encodeURIComponent( link );

					// If WP_DEBUG enabled, output error details
					if ( envira_social.debug ) {
						console.log ('facebook url (lightbox):');
						console.log (url);
					}

					break;

				case 'google':
					// link var is taken from the 'general' caption above
					url = 'https://plus.google.com/share?url=' + encodeURIComponent( link ); /* does not appear encodeURIComponent is needed */
					
					// If WP_DEBUG enabled, output error details
					if ( envira_social.debug ) {
						console.log ('google url (lightbox):');
						console.log (url);
					}

					break;

				case 'pinterest':
					// caption, image, and link var is taken from the 'general' caption above
					url = 'http://pinterest.com/pin/create/button/?url=' + link + '&media=' + image + '&description=' + caption;
					
					// If WP_DEBUG enabled, output error details
					if ( envira_social.debug ) {
						console.log ('pinterest url (lightbox):');
						console.log (url);
					}

					break;

				case 'email':

					// If WP_DEBUG enabled, output error details
					if ( envira_social.debug ) {
						console.log ('envira_permalink (lightboxx):');
						console.log (envira_permalink);
						console.log (envira_permalink.length);
					}
					
					if ( typeof envira_permalink !== "undefined" && envira_permalink !== null && envira_permalink.length > 0 ) {
						var cleaned_email_link = ( hash.length > 0 ) ? base_link + '?' + envira_permalink + hash: base_link + page_string + '?' + envira_permalink;
					} else {
						var cleaned_email_link = ( hash.length > 0 ) ? base_link + '?' + hash: base_link + page_string; 
					}

					// caption, image, and link var is taken from the 'general' caption above
					url = 'mailto:?subject=' + encodeURIComponent(caption) + '&body=Photo: ' + image + '%0D%0AURL: ' + cleaned_email_link;

					// If WP_DEBUG enabled, output error details
					if ( envira_social.debug ) {
						console.log ('email url (lightbox):');
						console.log (url);
						console.log (link);
					}

					break;
			}

		} else { /* GALLERY LINKS */

			var gallery_id 			= $(this).data('envira-gallery-id'),
				gallery_item_id 	= $(this).data('envira-item-id'),
				rand 				= Math.floor( ( Math.random() * 10000 ) + 1 ),
				envira_permalink 	= envira_social_get_query_arg( 'envira', window.location.href );

				// If WP_DEBUG enabled, output error details
				if ( envira_social.debug ) {
					console.log( 'detected gallery_id (gallery): ' + gallery_id );
					console.log( 'detected gallery_item_id (gallery): ' + gallery_item_id );
				}

			// Album ID might not exist, so let's check for this
			if (typeof $(this).attr('data-envira-album-id') !== "undefined") {
				var album_id	= $(this).data('envira-album-id');

				// If WP_DEBUG enabled, output error details
				if ( envira_social.debug ) {
					console.log ('album_id is currently:');
					console.log (album_id);
				}

			} else {
				var album_id    = false;
			}

			if ( typeof envira_permalink !== "undefined" ) {
				envira_permalink = 'envira=' + envira_permalink;
			} else {
				envira_permalink = '';
			}

			// If WP_DEBUG enabled, output error details
			if ( envira_social.debug ) {
				console.log('envira_permalink:' + envira_permalink);
			}

			switch ( network ) {
				case 'facebook':

					var quote 			= '',
						facebook_text   = '',
						title   		= '',
						tags   			= '',
						caption 		= '',
						image 			= '';

					if (typeof $(this).attr('data-envira-social-facebook-text') !== "undefined") {
						facebook_text		= decodeURIComponent($(this).data('envira-social-facebook-text'));
						facebook_text 		= facebook_text.replace(new RegExp("\\+","g"),' ');
						// If WP_DEBUG enabled, output error details
						if ( envira_social.debug ) {
							console.log ('updating facebook_text:');
							console.log (facebook_text);
						}
					}
					if (typeof $(this).attr('data-envira-caption') !== "undefined") {
						var caption	 		= decodeURIComponent($(this).data('envira-facebook-caption'));
						caption 			= caption.replace(new RegExp("\\+","g"),' ');
						// If WP_DEBUG enabled, output error details
						if ( envira_social.debug ) {
							console.log ('updating caption:');
							console.log (caption);
						}
					}
					if (typeof $(this).attr('data-envira-facebook-quote') !== "undefined") {
						quote	 			= decodeURIComponent($(this).data('envira-facebook-quote'));
						quote 				= quote.replace(new RegExp("\\+","g"),' ');
						// If WP_DEBUG enabled, output error details
						if ( envira_social.debug ) {
							console.log ('updating quote:');
							console.log (quote);
						}
					}

					if (typeof $(this).attr('data-envira-title') !== "undefined") {
						title	 			= decodeURIComponent($(this).data('envira-title'));
						title 				= title.replace(new RegExp("\\+","g"),' ');
						// If WP_DEBUG enabled, output error details
						if ( envira_social.debug ) {
							console.log ('updating title:');
							console.log (title);
						}
					}

					if (typeof $(this).attr('data-envira-facebook-tags') !== "undefined") {
						tags	 			= decodeURIComponent($(this).data('envira-facebook-tags'));
						tags 				= tags.replace(new RegExp("\\+","g"),' ');
						// remove any dashes, since FB doesn't like them
						tags 				= tags.replace(/-/g, '');
						// If WP_DEBUG enabled, output error details
						if ( envira_social.debug ) {
							console.log ('updating tags:');
							console.log (tags);
						}
					}

					if (typeof $(this).attr('data-envira-social-picture') !== "undefined") {
						image	 			= decodeURIComponent($(this).data('envira-social-picture'));
						image 				= image.replace(new RegExp("\\+","g"),' ');
						// If WP_DEBUG enabled, output error details
						if ( envira_social.debug ) {
							console.log ('updating image:');
							console.log (image);
						}
					}

					var link		    = ( hash.length > 0 ) ? window.location.href.split('#')[0] + encodeURIComponent(hash) + '&envira_album_id=' + album_id + '&envira_social_gallery_id=' + gallery_id + '&envira_social_gallery_item_id=' + gallery_item_id : window.location.href.split('?')[0] + page_string + '?envira_album_id=' + album_id + '&envira_social_gallery_id=' + gallery_id + '&envira_social_gallery_item_id=' + gallery_item_id + '&rand=' + rand + '&' + envira_permalink;
					
                    break;

                case 'pinterest':
					
					var description		= '',
						ptype 			= 'pin-one', // always the default
						image 			= '';

					if (typeof $(this).attr('data-envira-social-pinterest-description') !== "undefined") {
						description	 		= decodeURIComponent($(this).data('envira-social-pinterest-description'));
						description 		= description.replace(new RegExp("\\+","g"),' ');
						// If WP_DEBUG enabled, output error details
						if ( envira_social.debug ) {
							console.log ('updating pinterest description');
							console.log (description);
						}
					}

					if (typeof $(this).attr('data-envira-social-picture') !== "undefined") {
						image	 			= decodeURIComponent($(this).data('envira-social-picture'));
						image 				= image.replace(new RegExp("\\+","g"),' ');
						// If WP_DEBUG enabled, output error details
						if ( envira_social.debug ) {
							console.log ('updating pinterest image');
							console.log (image);
						}
					}

					if (typeof $(this).attr('data-envira-pinterest-type') !== "undefined") {
						ptype	 			= decodeURIComponent($(this).data('envira-pinterest-type'));
						ptype 				= ptype.replace(new RegExp("\\+","g"),' ');
						// If WP_DEBUG enabled, output error details
						if ( envira_social.debug ) {
							console.log ('updating pinterest ptype');
							console.log (ptype);
						}
					}

					var link		    = ( hash.length > 0 ) ? window.location.href.split('#')[0] + encodeURIComponent(hash) + '&envira_album_id=' + album_id + '&envira_social_gallery_id=' + gallery_id + '&envira_social_gallery_item_id=' + gallery_item_id + '&' + envira_permalink : window.location.href.split('?')[0] + '?envira_album_id=' + album_id + '&envira_social_gallery_id=' + gallery_id + '&envira_social_gallery_item_id=' + gallery_item_id + '&rand=' + rand + '&' + envira_permalink;

						// If WP_DEBUG enabled, output error details
						if ( envira_social.debug ) {
							console.log ('pinterest link:');
							console.log (image);
						}

                	break;

			}

		}
	
		// Open The Social Window
		// Depending on the network, we might do this via the social JS or open our own window, etc.
		
		if ( network === 'pinterest' ) {

			/* Using New Pinterest JS - PINIT.JS */

			if (typeof description === "undefined" && typeof caption !== "undefined") {
				description = caption;
			} else if (typeof description === "undefined") {
				// if there is no caption, then make the description blank instead of undefined
				// this helps make using the Pinterest API more stable
				description = '';
			}

			if ( ptype == "pin-all" ) {

				PinUtils.pinAny({
		            'media': image,
		            'description': description,
		            'url': link 
		        });

			} else {

				PinUtils.pinOne({
		            'media': image,
		            'description': description,
		            'url': link 
		        });

			}


		} else if ( network === 'facebook' ) {

			if ( hash.length > 0 ) {
				// var the_href = window.location.href.split('#')[0];
				var the_href = link;
			} else if ( link.length > 0 ) {
				var the_href = link;
			} else {
				var the_href = window.location.href;
			}

			// If WP_DEBUG enabled, output error details
			if ( envira_social.debug ) {
				console.log ('sending facebook link:');
				console.log (the_href);
			}

			FB.ui({
			    method: 'share',
	    		display: 'popup',
	    		href: the_href,
	    		title: title,
	    		description: facebook_text,
	    		caption: caption,
	    		picture: image,
	    		hashtag: tags,
	    		quote: quote,
			});

		} else if ( network === 'email' ) {

			window.location = url;	

		} else {

			var enviraSocialWin = window.open( url, 'Share', 'width=' + width + ',height=' + height );	

			// If WP_DEBUG enabled, output error details
			if ( envira_social.debug ) {
				console.log ('url:');
				console.log ( url );
				console.log ( encodeURIComponent(url) );
			}


		}

		return false;
	});

	// Gallery: Show Sharing Buttons on Image Hover
	// 
	// New: If this is a "touch" device, then it's likely we don't want to do this since it will require
	// another "touch" to get to the gallery, especially if there are no social items
	// 
	
	$( 'div.envira-gallery-item-inner' ).each(function() {
		if ( $( this ).find('.envira-social-buttons .envira-social-network').length === 0 ) {

			$( this ).find('div.envira-social-buttons').remove();
		}
	});

	// If the envira_social_sharing_close=1 key/value parameter exists, close the window
	if ( location.href.search( 'envira_social_sharing_close' ) > -1 ) {
		window.close();
	} 

} );


document.getElementsByClassName('button-facebook').onclick = function() {
  FB.ui({
    method: 'share',
    display: 'popup',
    href: 'https://developers.facebook.com/docs/',
  }, function(response){});
}

/**
 * Returns a URL parameter by name
 *
 * @since 1.1.7
 *
 * @param 	string 	name
 * @param 	string	url
 * @return 	string 	value
 */
function envira_social_get_query_arg( name, url ) {

	name = name.replace(/[\[\]]/g, "\\$&");
	var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
		results = regex.exec( url );

	if ( ! results ) {
		return null;
	}
	if ( ! results[2] ) {
		return '';
	}

	return decodeURIComponent( results[2].replace(/\+/g, " ") );

}

/**
 * "Cleans" a base_link so vars aren't repeated, etc.
 * Uses envira_remove_URL_parameter()
 *
 * @since 1.1.7
 *
 * @param 	string 	base_link
 * @return 	string 	value
 */
function envira_clean_base_link( base_link ) {
	
	base_link   = envira_remove_URL_parameter( base_link, 'doing_wp_cron' );
	base_link   = envira_remove_URL_parameter( base_link, 'envira_social_gallery_id' );
	base_link   = envira_remove_URL_parameter( base_link, 'envira_social_gallery_item_id' );
	base_link   = envira_remove_URL_parameter( base_link, 'rand' );
	base_link   = envira_remove_URL_parameter( base_link, 'envira' );
	base_link   = envira_remove_URL_parameter( base_link, 'envira_album' );
	base_link   = envira_remove_URL_parameter( base_link, 'album_id' );

	return base_link;
}

/**
 * Removes parameters in URLs
 *
 * @since 1.1.7
 *
 * @param 	string 	url
 * @param 	string	parameter
 * @return 	string 	value
 */
function envira_remove_URL_parameter(url, parameter) {
    //prefer to use l.search if you have a location/link object
    var urlparts= url.split('?');   
    if (urlparts.length>=2) {

        var prefix= encodeURIComponent(parameter)+'=';
        var pars= urlparts[1].split(/[&;]/g);

        //reverse iteration as may be destructive
        for (var i= pars.length; i-- > 0;) {    
            //idiom for string.startsWith
            if (pars[i].lastIndexOf(prefix, 0) !== -1) {  
                pars.splice(i, 1);
            }
        }

        url= urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : "");
        return url;
    } else {
        return url;
    }
}
