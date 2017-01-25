<?php
/**
 * Shortcode class.
 *
 * @since 1.0.0
 *
 * @package Envira_Pagination
 * @author  Tim Carr
 */
class Envira_Social_Shortcode {

    /**
     * Holds the class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $instance;

    /**
     * Path to the file.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $file = __FILE__;

    /**
     * Holds the base class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public $base;
    
     /**
     * Holds a flag to determine whether metadata has been set
     *
     * @since 1.0.0
     *
     * @var array
     */
    public $meta_data_set = false;

    /**
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
	    
	    // Load the base class object.
        $this->base = Envira_Social::get_instance();
	    
	    // Register CSS
        wp_register_style( $this->base->plugin_slug . '-style', plugins_url( 'assets/css/envira-social.css', $this->base->file ), array(), $this->base->version );
	    
        // Register JS
        wp_register_script( $this->base->plugin_slug . '-script', plugins_url( 'assets/js/min/envira-social-min.js', $this->base->file ), array( 'jquery' ), $this->base->version, true );
        wp_register_script( $this->base->plugin_slug . '-pinterest-pinit', '//assets.pinterest.com/js/pinit.js', array( 'jquery' ), $this->base->version, true );

        // Init Scripts
        add_action( 'init',     array( $this, 'maybe_prevent_caching' ) );
        add_action( 'wp_head',  array( $this, 'metadata' ) );
        add_action( 'wp_head',  array( $this, 'facebook_sdk_init' ) );  

        // Gallery      
        add_action( 'envira_gallery_before_output', array( $this, 'gallery_output_css_js' ) );
        add_filter( 'envira_gallery_output_dynamic_position', array( $this, 'gallery_output_html_high_priority' ), 0, 6 );
        add_filter( 'envira_gallery_output_dynamic_position', array( $this, 'gallery_output_html_low_priority' ), 100, 6 );
        add_action( 'envira_gallery_api_before_show', array( $this, 'gallery_output_lightbox_data_attributes' ) );
        add_action( 'envirabox_output_dynamic_position', array( $this, 'gallery_output_legacy_lightbox_html_high_priority' ), 0, 3 );
        add_action( 'envirabox_output_dynamic_position', array( $this, 'gallery_output_legacy_lightbox_html_low_priority' ), 100, 3 );
        
        add_action( 'envirabox_inner_below', array( $this, 'gallery_output_lightbox_html' ), 0, 3 );

        add_filter( 'envirabox_margin', array( $this, 'envirabox_margin' ), 11, 2 );

        // Schema Microdata
        add_filter( 'envira_gallery_output_schema_microdata', array( $this, 'envira_output_schema_microdata' ), 10, 6 );
        add_filter( 'envira_gallery_output_shortcode_schema_microdata', array( $this, 'envira_gallery_output_shortcode_schema_microdata' ), 10, 2 );

        // Album
        add_action( 'envira_albums_before_output', array( $this, 'albums_output_css_js' ) );
        add_filter( 'envira_albums_output_dynamic_position', array( $this, 'gallery_output_html_high_priority' ), 0, 6 );
        add_filter( 'envira_albums_output_dynamic_position', array( $this, 'gallery_output_html_low_priority' ), 100, 6 );
        add_action( 'envira_albums_api_before_show', array( $this, 'gallery_output_lightbox_data_attributes' ) );

        // Social + Pagination
        // add_action( 'wp', array( $this, 'gallery_social_pagination_redirect' ) );

    }

    /* public function gallery_social_pagination_redirect() {

        return;

        global $wp_query, $wp, $post;

        if ( empty( $_GET['envira_social_gallery_id'] ) || empty( $_GET['envira_social_gallery_item_id'] ) ) {
            return;
        }


        $envira_social_gallery_id = intval( $_GET['envira_social_gallery_id'] );
        $envira_social_gallery_item_id = intval( $_GET['envira_social_gallery_item_id'] );

        if ( ! $envira_social_gallery_id || ! $envira_social_gallery_item_id ) { 
            return;
        }

        $current_url = home_url( add_query_arg( array() , $wp->request ) );

        // get gallery data
        $data = get_post_meta( $envira_social_gallery_id, '_eg_gallery_data', true );

        // if this isn't an ajax pagination gallery, there might be no need
        if ( $data['config']['pagination_ajax_load'] != 2 ) {
            // let's see if there's a envira_post_id being passed - if so and it doesn't match the current post_id, redirect
            if ( $_GET['envira_post_id'] ) {
                $envira_post_id = intval( $_GET['envira_post_id'] );
                if ( $envira_post_id > 0 ) {
                    $permalink = $current_url; // get_permalink( $envira_post_id );
                    $permalink = trailingslashit( $permalink ) . '/?envira_social_gallery_id=' . $envira_social_gallery_id . '&envira_social_gallery_item_id=' . $envira_social_gallery_item_id;
                    wp_redirect( $permalink ); exit;
                }
            } else {
                return; // no redirect - continue with loading the page
            }

        }

        // if there are no photos in this gallery, no need
        if ( empty( $data['gallery'] ) ) {
            return;
        }

        // we have the gallery, now try to determine where the image is

        $images_per_page = intval( $data['config']['pagination_images_per_page'] );
        if ( $images_per_page == 0 ) { return; } // if there is no 'per page', then we can't calculate

        $start_index = array_search( $envira_social_gallery_item_id, array_keys( $data['gallery'] ) ) + 1; // add the one because array starts at zero, of course

        $current_page = ceil($start_index / $images_per_page);

        if ( $current_page == 1 ) { 
            return;
        }

        if ( get_query_var( 'page' ) == $current_page ) { 
            return;
        }

        // if a post id has been passed along, we should use THAT permalink
        if ( $_GET['envira_post_id'] ) {
            $envira_post_id = intval( $_GET['envira_post_id'] );
            // if $envira_post_id is false, we'll go back to using the default permalink
            if ( $envira_post_id ) {
                $permalink = get_permalink( $envira_post_id );
            } else {
                $permalink = get_permalink( $post->ID );
            }
        } else {
            $permalink = get_permalink( $post->ID );
        }



        // $permalink = $current_url; // get_permalink( $envira_post_id );

        $permalink = trailingslashit( $permalink ) . '/?envira_social_gallery_id=' . $envira_social_gallery_id . '&envira_social_gallery_item_id=' . $envira_social_gallery_item_id;

        wp_redirect( $permalink ); exit;

    } */

    /**
     * Remove schema data because Google+ will use this over the Open Graph data the social addon uses
     *
     * @since 1.1.7
     */
    public function envira_output_schema_microdata( $html, $gallery, $id, $item, $data, $i ) {

        if ( ! $data['config']['social_google'] && ! $data['config']['social_lightbox_google'] ) {
            return $html;
        } else {
            return false;
        }

    }

    /**
     * Remove schema data because Google+ will use this over the Open Graph data the social addon uses
     *
     * @since 1.1.7
     */
    public function envira_gallery_output_shortcode_schema_microdata( $html, $gallery ) {

        if ( ! $gallery['config']['social_google'] && ! $gallery['config']['social_lightbox_google'] ) {
            return $html;
        } else {
            return false;
        }

    }


    /**
     * If an envira_social_gallery_id and envira_social_gallery_item_id are present in the URL,
     * force the server to fetch a fresh version of the page, and not use cache.
     *
     * This prevents some social networks, such as Google, from always returning the first image
     * the user chose to share, because its cached.  If the user then tries to share a different 
     * second image, the social network will (wrongly) share the first again.
     *
     * @since 1.1.7
     */
    public function maybe_prevent_caching() {

        // Check if specific request parameters exist
        if ( ! isset( $_REQUEST['envira_social_gallery_id'] ) ) {
            return;
        }
        if ( ! isset( $_REQUEST['envira_social_gallery_item_id'] ) ) {
            return;
        }

        // Add some headers to prevent caching
        header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT');
        header( 'Cache-Control: no-store, no-cache, must-revalidate');
        header( 'Cache-Control: post-check=0, pre-check=0', false);
        header( 'Pragma: no-cache');
        header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT');

    }

    /**
    * Set Open Graph and Twitter Card metadata to share the chosen gallery and image
    * The Gallery ID and Gallery Item ID will be specified in the URL
    *
    * @since 1.0.5
    */
    public function facebook_sdk_init() { 

        // Get instance
        $common = Envira_Social_Common::get_instance();

        if ( !$common->get_setting( 'facebook_app_id' ) ) { return; }

        ?>

        <script>
          window.fbAsyncInit = function() {
            FB.init({
              appId      : '<?php echo $common->get_setting( 'facebook_app_id' ); ?>',
              xfbml      : true,
              version    : 'v2.7'
            });
          };

          (function(d, s, id){
             var js, fjs = d.getElementsByTagName(s)[0];
             if (d.getElementById(id)) {return;}
             js = d.createElement(s); js.id = id;
             js.src = "//connect.facebook.net/en_US/sdk.js";
             fjs.parentNode.insertBefore(js, fjs);
           }(document, 'script', 'facebook-jssdk'));
        </script>

    <?php }


    /**
    * Set Open Graph and Twitter Card metadata to share the chosen gallery and image
    * The Gallery ID and Gallery Item ID will be specified in the URL
    *
    * @since 1.0.5
    */
    public function metadata() {

        global $post;

        // Bail if metadata already set
        if ( $this->meta_data_set ) {
            return;
        }

        // Get gallery ID and gallery item ID
        $gallery_id      = ( isset( $_GET['envira_social_gallery_id'] ) ? sanitize_text_field( $_GET['envira_social_gallery_id'] ) : '' );
        $album_id        = ( isset( $_GET['envira_album_id'] ) ? sanitize_text_field( $_GET['envira_album_id'] ) : false );
        $gallery_item_id = ( isset( $_GET['envira_social_gallery_item_id'] ) ? sanitize_text_field ( $_GET['envira_social_gallery_item_id'] ) : '' );
        $dynamic_gallery_post = false;

        // Check for dynamic gallery
        if ( substr( $gallery_id, 0, 7 ) === "custom_" ) {
            // this is a dynamic gallery, so let's use the dynamic gallery for settings
            // step one: find the dynamic gallery, if it exists
            $args = array(
                  'name' => 'envira-dynamic-gallery',
                  'post_type' => 'envira',
                  'post_status' => 'publish'
                );
            $dynamic_gallery_post = get_posts($args);
            if ( !$dynamic_gallery_post ) { return; }
            // revise our gallery id
            $gallery_id = $dynamic_gallery_post[0]->ID;
            $post = $dynamic_gallery_post[0];

        } else {

            // If either ID is missing, don't bail yet - attempt to find the featured image for the gallery
            // TO-DO: CHECK POST TYPE?
            if ( ( empty( $gallery_id ) || empty( $gallery_item_id ) ) && ! empty( $post->ID ) ) {
                $images_in_gallery = get_post_meta( $post->ID, '_eg_in_gallery', true );
                if ( $images_in_gallery ) {
                    $gallery_id = $post->ID;
                    $gallery_item_id = $images_in_gallery[0];
                }
            }

        }

        // NOW we bail if either ID are missing
        if ( empty( $gallery_id ) || empty( $gallery_item_id ) ) {
            return;
        }

        // Get gallery
        if ( $album_id ) {
            $data           = Envira_Albums::get_instance()->get_album( $album_id );
            $gallery_data   = Envira_Gallery::get_instance()->get_gallery( $gallery_id );   
        } else {
            $data           = Envira_Gallery::get_instance()->get_gallery( $gallery_id );            
        }
        if ( ! $data ) {
            return;
        }
        // Get gallery item - check first if it's dynamic
        // if ( ! isset( $data['gallery'][ $gallery_item_id ] ) ) {
        //     return;
        // }
        if ( $dynamic_gallery_post ) {

            $media_item = get_post( $gallery_item_id );

            if ( $media_item ) {

                $item = array ( 'status' => 'active', 
                                'src' => $media_item->guid,
                                'title' => $media_item->post_title,
                                'link' => $media_item->guid,
                                'alt' => $media_item->post_title,
                                'caption' => $media_item->post_title );

            }

        } else if ( $album_id ) {
            
            $media_item = get_post( $gallery_item_id );

            if ( $media_item ) {

                $item = array ( 'status' => 'active', 
                                'src' => $media_item->guid,
                                'title' => $media_item->post_title,
                                'link' => $media_item->guid,
                                'alt' => $media_item->post_title,
                                'caption' => $media_item->post_title );

            }

            $item = $gallery_data['gallery'][ $gallery_item_id ];
            
        } else {

            $item = $data['gallery'][ $gallery_item_id ];

        }

        if ( !$item ) {
            return;
        }

        // Allow devs to filter image
        $item = apply_filters( 'envira_social_metadata_image', $item, $gallery_item_id, $data, $gallery_id );

        // If here, we have an item
        // Get instance
        $common = Envira_Social_Common::get_instance();
        $facebook_app_id = $common->get_setting( 'facebook_app_id' );
        $twitter_username = $common->get_setting( 'twitter_username' );

        // If there's an author, get the name information
        if ( ! empty( $post ) && $post->post_author ) {
            $user = get_user_by( 'id', $post->post_author );
            $author_name = $user->first_name . ' ' . $user->last_name;
        }

        // If there's a post, get the date publish information
        if ( ! empty( $post ) && $post->ID ) {
            // format needs to be 2014-08-12T00:01:56+00:00
            $date_published = gmdate('c', strtotime( $post->post_date ) );
        }

        // If there's a post, get the permalink
        if ( ! empty( $post ) && $post->ID ) {
            $social_url = get_permalink( $post->ID );
        } else {
            $social_url = false;
        }


        /* OPEN GRAPH TAGS */

        // The Title

        if ( ! empty( $gallery_data['config']['title'] ) ) {
            // if this exists, we are looking at an album and we want to pass along the title of the GALLERY, not the GALLERY IMAGE
            $social_title = $gallery_data['config']['title'];
        } else if ( $item['title'] ) {
            $social_title = $item['title'];
        } else if ( $post->post_title ) {
            $social_title = $post->title;
        } else {
            $social_title = $data['config']['social_google_text'];
        }

        // Clean Up Title
        $social_title = str_replace('"', '&quot;', $social_title);

        // The Description

        $override_description = $this->get_config( 'social_google_desc', $data );

        if ( $item['caption'] ) {
            $social_description = $item['caption'];
        } else if ( $override_description ) {
            $social_description = $override_description;
        } else { // last resort - grab the gallery description
            $social_description = $data['config']['description'];
        }

        // Clean Up Title
        $social_description = str_replace('"', '&quot;', $social_description);

        // Make sure the description has spaces if the description is false.
        // Otherwise Facebook takes this a sign to try to parse the page, which is rarely good

        if ( strlen( $social_description ) == 0 ) {
            $social_description = "  ";
        }

        // The Image

        if ( $item['src'] ) {
            $social_image = $item['src'];
        } else {
            $social_image = false;
        }

        // Add Tag If User Doesn't Have "Rich Pins" checked
        if ( empty( $data['config']['social_pinterest_rich'] ) ) { ?>
            <meta name="pinterest-rich-pin" content="false" />
        <?php }

        // print_r ($data); exit;

        // We should display this for almost any social network choosen, outside of Twitter which has it's own tags
        if ( $this->get_config( 'social', $data ) || $this->get_config( 'social_lightbox', $data ) ) : 



        ?>

            <meta property="og:type" content="article" />
            <meta property="og:title" content="<?php echo $social_title; ?>" />
            <meta property="og:description" content="<?php echo $social_description; ?>" />
            <meta property="og:image" content="<?php echo $social_image; ?>" />
            <meta property="og:url" content="<?php echo $social_url; ?>" />
            <?php /* Below tags are more for Pinterest than any of the other social networks */ ?>
<meta property="og:site_name" content="<?php bloginfo( 'name' ); ?>" />
<?php if ( $date_published ) { ?>
            <meta property="article:published_time" content="<?php echo $date_published; ?>" />
<?php } ?>
            <?php if ( $author_name ) { ?>
<meta property="article:author" content="<?php echo $author_name; ?>" />
            <?php } ?>

        <?php 

            // This allows some tracking features, although most probably won't take advantage of this

            if ( $this->get_config( 'social_facebook', $data ) && $facebook_app_id ) { ?>

            <meta property="fb:app_id" content="<?php echo $facebook_app_id; ?>" />

            <?php } ?>

        <?php

        endif;

        /* TWITTER META TAGS */

        if ( ! empty( $gallery_data['config']['title'] ) ) {
            // if this exists, we are looking at an album and we want to pass along the title of the GALLERY, not the GALLERY IMAGE
            $summary_card_title = $gallery_data['config']['title'];
        } else if ( $item['title'] ) {
            $summary_card_title = $item['title'];
        } else if ( $post->post_title ) {
            $summary_card_title = $post->title;
        } else {
            $summary_card_title = $data['config']['social_twitter_text'];
        }

        // Clean Up Title
        $summary_card_title = str_replace('"', '&quot;', $social_title);

        $override_description = $this->get_config( 'social_twitter_summary_card_desc', $data );

        if ( $override_description ) {
            $summary_card_description = esc_html($override_description);
        } else if ( !empty( $item['caption'] ) ) {
            $summary_card_description = $item['caption'];
        } else if ( !empty( $data['description'] ) ) {
            $summary_card_description = $data['config']['description'];
        } else {
            $summary_card_description = $data['config']['social_twitter_text'];
        }

        // Did the user select a summary card for Twitter?
        // If so, spit out the meta-data for Twitter Summary Card

        if ( ( $this->get_config( 'social', $data ) || $this->get_config( 'social_lightbox', $data ) ) && ( $this->get_config( 'social_twitter', $data ) || $this->get_config( 'social_lightbox_twitter', $data ) ) ) :

            if ( $this->get_config( 'social_twitter_sharing_method', $data ) == "card" ) { ?>

            <meta name="twitter:card" content="summary" />
            <?php } else if ( $this->get_config( 'social_twitter_sharing_method', $data ) == "card-photo" ) { ?>
            <meta name="twitter:card" content="summary_large_image">
            <?php } ?>
            <?php if ( $this->get_config( 'social_twitter_summary_card_site', $data ) ) { ?>
            <meta name="twitter:site" content="<?php echo sanitize_text_field( $this->get_config( 'social_twitter_summary_card_site', $data ) ); ?>" />
            <?php } ?>
            <meta name="twitter:title" content="<?php echo $summary_card_title; ?>" />
            <meta name="twitter:description" content="<?php echo $summary_card_description; ?>" />
            <meta name="twitter:image" content="<?php echo $item['src']; ?>" />

        <?php

        endif; // end Twitter Summary Card meta-data

        // Mark our metadata as loaded
        $this->meta_data_set = true;

    }

    /**
	* Enqueue CSS and JS if Social Sharing is enabled
	*
	* @since 1.0.0
	*
	* @param array $data Gallery Data
	*/
    public function gallery_output_css_js( $data ) {

        // Check if Social Sharing Buttons output is enabled
        if ( ! $this->get_config( 'social', $data ) && ! $this->get_config( 'social_lightbox', $data ) ) {
            return;
        }

        // Get instance
        $common = Envira_Social_Common::get_instance();
        
        // Enqueue CSS + JS
        wp_enqueue_style( $this->base->plugin_slug . '-style' );
        wp_enqueue_script( $this->base->plugin_slug . '-script' );
        wp_localize_script( $this->base->plugin_slug . '-script', 'envira_social', array(
            'facebook_app_id'   => $common->get_setting( 'facebook_app_id' ),
            'debug'             => ( defined( 'ENVIRA_DEBUG' ) && ENVIRA_DEBUG ? true : false ),
        ) );

        // If the user has enabled Pinterest
        if ( $this->get_config( 'social_pinterest', $data ) || $this->get_config( 'social_lightbox_pinterest', $data ) ) {
            wp_enqueue_script( $this->base->plugin_slug . '-pinterest-pinit' );
        }

        
    }

    /**
    * Enqueue CSS and JS for Albums if Social Sharing is enabled
    *
    * @since 1.0.3
    *
    * @param array $data Album Data
    */
    public function albums_output_css_js( $data ) {

        global $post;
        
        // Check if Social Sharing Buttons output is enabled
        if ( ! $this->get_config( 'social', $data ) && ! $this->get_config( 'social_lightbox', $data ) ) {
            return;
        }

        // Get instance
        $common = Envira_Social_Common::get_instance();
        
        // Enqueue CSS + JS
        wp_enqueue_style( $this->base->plugin_slug . '-style' );
        wp_enqueue_script( $this->base->plugin_slug . '-script' );
        wp_localize_script( $this->base->plugin_slug . '-script', 'envira_social', array(
            'facebook_app_id'   => $common->get_setting( 'facebook_app_id' ),
            'debug'             => ( defined( 'ENVIRA_DEBUG' ) && ENVIRA_DEBUG ? true : false ),
        ) );


        // If the user has enabled Pinterest
        if ( $this->get_config( 'social_pinterest', $data ) || $this->get_config( 'social_lightbox_pinterest', $data ) ) {
            wp_enqueue_script( $this->base->plugin_slug . '-pinterest-pinit' );
        }
    }

    public function envirabox_margin( $margin, $data ) {

        // Check if Social Sharing Buttons output is enabled
        if ( ! $this->get_config( 'social_lightbox', $data ) ) {
            return $margin;
        }

        if ( in_array( $this->get_config( 'lightbox_theme', $data ), array( 'space_dark', 'space_light' ) ) ) {
            if( ! $this->get_config( 'thumbnails', $data ) ) {
                $margin = '[35, 35, 60, 35]';
            }
        } else if ( in_array( $this->get_config( 'lightbox_theme', $data ), array( 'base', 'legacy', 'subtle', 'sleek', 'showcase', 'polaroid', 'captioned' ) ) ) {
            if( $this->get_config( 'social_lightbox_outside', $data ) ) {
                $margin = '[80, 75, 80, 75]';
            }
        }

        return $margin;

    }

    
    /**
	* Outputs Social Media Sharing HTML for the Gallery thumbnail with a high priority
	*
	* @since 1.0.0
	* 
	* @param string $output HTML Output
	* @param int $id Attachment ID
	* @param array $item Image Item
	* @param array $data Gallery Config
	* @param int $i Image number in gallery
	* @return string HTML Output
	*/
    public function gallery_output_html_high_priority( $output, $id, $item, $data, $i, $position ) {

        // Check if Social Sharing Buttons output is enabled
        if ( ! $this->get_config( 'social', $data ) ) {
            return $output;
        }

        if ( $this->get_config( 'social_position', $data ) !== $position 
            || ( $this->get_config( 'social_orientation', $data ) == 'horizontal' && $position == 'bottom-left' ) 
            || $position == 'bottom-right' 
        ) {
            return $output;
        }

        // Prepend Button(s)
	    $buttons = $this->get_social_sharing_buttons( $id, $item, $data, $i, $position );

		return $output . $buttons;
	    
    }

    /**
    * Outputs Social Media Sharing HTML for the Gallery thumbnail with a low priority
    *
    * @since 1.0.0
    * 
    * @param string $output HTML Output
    * @param int $id Attachment ID
    * @param array $item Image Item
    * @param array $data Gallery Config
    * @param int $i Image number in gallery
    * @return string HTML Output
    */
    public function gallery_output_html_low_priority( $output, $id, $item, $data, $i, $position ) {

        // Check if Social Sharing Buttons output is enabled
        if ( ! $this->get_config( 'social', $data ) ) {
            return $output;
        }

        if ( $this->get_config( 'social_position', $data ) !== $position 
            || $position == 'top-left' 
            || ( $this->get_config( 'social_orientation', $data ) == 'vertical' && $position == 'top-right' ) 
            || ( $this->get_config( 'social_orientation', $data ) == 'vertical' && $position == 'bottom-left' ) 
            || ( $this->get_config( 'social_orientation', $data ) == 'horizontal' && $position == 'top-right' ) 
        ) {
            return $output;
        }

        // Prepend Button(s)
        $buttons = $this->get_social_sharing_buttons( $id, $item, $data, $i, $position );

        return $output . $buttons;
        
    }

    /**
     * Outputs data- attributes on the Lightbox image for the Facebook and Twitter Text settings
     * for the given Gallery.
     *
     * @since 1.1.2
     *
     * @param   array   $data   Gallery Data
     * @return  JS
     */
    public function gallery_output_lightbox_data_attributes( $data ) {

        global $wp;

        // Check if Social Sharing Buttons output is enabled
        if ( ! $this->get_config( 'social_lightbox', $data ) ) {
            return;
        }

        // there needs to be a description in $facebook_text, otherwise Facebook will try to grab/make one with poor results
        if (  $this->get_config( 'social_facebook_text', $data ) == "" ) {

            $facebook_text = "    ";

        } else {
	        
            $facebook_text = $this->get_config( 'social_facebook_text', $data );
        
        }

        $current_url = home_url( add_query_arg( array() , $wp->request ) );

        ?>           

        this.inner.find('img').attr('data-envira-social-url', '<?php echo urlencode( $current_url ); ?>' );     

        this.inner.find('img').attr('data-envira-social-facebook-text', '<?php echo esc_html($facebook_text); ?>' );     
        this.inner.find('img').attr('data-envira-facebook-quote',       '<?php echo esc_html($this->get_config( 'social_facebook_quote', $data )); ?>');
        this.inner.find('img').attr('data-envira-facebook-tags-manual', '<?php echo esc_html($this->get_config( 'social_facebook_tags_manual', $data )); ?>');

        this.inner.find('img').attr('data-envira-social-twitter-text',  '<?php echo esc_html($this->get_config( 'social_twitter_text', $data )); ?>');


        <?php

    }
   
    /**
    * Gallery: Outputs EXIF Lightbox data when a lightbox image is displayed from a Gallery with a high priority
    *
    * @param array $data Gallery Data
    * @return JS
    */
    public function gallery_output_lightbox_html( $template, $data, $position = false ) {

        // Check if Social Sharing Buttons output is enabled
        // if ( ! $this->get_config( 'social_lightbox', $data ) ) {
        if ( ! $data['config']['social_lightbox'] ) {
            return $template;
        }


        if ( $data['config']['lightbox_theme'] != 'base_dark' && $data['config']['lightbox_theme'] != 'base_light' ) {
            return $template;
        }

        // Get Button(s)
        $buttons = $this->get_lightbox_social_sharing_buttons( $data, $position );
       
        return $template . $buttons;
                
    }

    /**
	* Gallery: Outputs EXIF Lightbox data when a lightbox image is displayed from a Gallery with a high priority
	*
	* @param array $data Gallery Data
	* @return JS
	*/
    public function gallery_output_legacy_lightbox_html_high_priority( $template, $data, $position = false ) {

        // Check if Social Sharing Buttons output is enabled
        // if ( ! $this->get_config( 'social_lightbox', $data ) ) {
        if ( ! $data['config']['social_lightbox'] ) {
            return $template;
        }


        if ( $data['config']['lightbox_theme'] == 'base_dark' || $data['config']['lightbox_theme'] == 'base_light' ) {
            return $template;
        }

        if ( $position && ( $this->get_config( 'social_lightbox_position', $data ) !== $position 
            || ( $this->get_config( 'social_lightbox_orientation', $data ) == 'horizontal' && $position == 'bottom-left' ) 
            || $position == 'bottom-right' )
        ) {
            return $template;
        }


        // Get Button(s)
        $buttons = $this->get_lightbox_social_sharing_buttons( $data, $position );
       
        return $template . $buttons;
			    
    }

    /**
    * Gallery: Outputs EXIF Lightbox data when a lightbox image is displayed from a Gallery with a low priority
    *
    * @param array $data Gallery Data
    * @return JS
    */
    public function gallery_output_legacy_lightbox_html_low_priority( $template, $data, $position = false ) {
        
        // Check if Social Sharing Buttons output is enabled
        if ( ! $data['config']['social_lightbox'] ) {
            return $template;
        }

        if ( $data['config']['lightbox_theme'] == 'base_dark' || $data['config']['lightbox_theme'] == 'base_light' ) {
            return $template;
        }

        if ( $position && ( $this->get_config( 'social_lightbox_position', $data ) !== $position 
            || $position == 'top-left' 
            || ( $this->get_config( 'social_lightbox_orientation', $data ) == 'vertical' && $position == 'top-right' ) 
            || ( $this->get_config( 'social_lightbox_orientation', $data ) == 'vertical' && $position == 'bottom-left' ) 
            || ( $this->get_config( 'social_lightbox_orientation', $data ) == 'horizontal' && $position == 'top-right' ) 
        ) ) {
            return $template;
        }

        // Get Button(s)
        $buttons = $this->get_lightbox_social_sharing_buttons( $data, $position );
       
        return $template . $buttons;
                
    }

    /**
    * Helper to output social sharing buttons for an image
    *
    * @since 1.0.0
    *
    * @global object $post Gallery
    *
    * @param int   $id   Image ID
    * @param array $item Image Data
    * @param array $data Gallery Data
    * @param int $i Index
    * @return string HTML
    */
    function get_social_sharing_buttons( $id, $item, $data, $i, $position ) {
      
        global $post, $wp;

        // Init $post_id var
        $post_id = false;
        $paged = false;

        // Get instance
        $common = Envira_Social_Common::get_instance();

        // Mobile check, is user allowing ANY social sharing on mobile for galleries?
        if ( envira_mobile_detect()->isMobile() && ! $data['config']['mobile_social'] ) {
            return;
        }

        // Start
        $buttons = '<div class="envira-social-buttons position-' . $this->get_config( 'social_position', $data ) . ' orientation-' . $this->get_config( 'social_orientation', $data ) . '">';

        // Ready the current url
        $current_url = home_url( add_query_arg( array() , $wp->request ) );

        // Get the Post/Page/CPT we're viewing
        // However, check AJAX $_POST call for post/page/CPT id FIRST
        if ( ! empty( $_POST['envira_post_social_url'] ) ) {
            $post_url = esc_url( $_POST['envira_post_social_url'] );
            $post_id  = false;
        } else if ( ! empty( $_POST['post_id'] ) ) {
            $post_id    = intval( $_POST['post_id'] );
            $post_url   = get_permalink( $post_id );
        } else if ( $current_url ) {
            $post_url   = $current_url;
        } else if ( ! empty($post) ) {
            $post_url   = get_permalink( $post->ID );
            $post_id    = $post->ID;
        }

        // Permalink check -> if the user has permalinks set to off/plain
        if ( ! empty( $_REQUEST['envira'] ) ) {
            // include this in the url we are building for social, otherwise link might just point back to the homepage
            $envira_permalink = 'envira=' . esc_html( $_REQUEST['envira'] ) . '&';
        } else {
            $envira_permalink = false;
        }

        // If this is pagination, add the page in the url
        if ( ! empty( $_POST['page'] ) ) { // passed along in pagination ajax
            $paged = intval( $_POST['page'] );
        } else {
            $paged = get_query_var( 'page', 0 );
        }

        $post_url = trailingslashit( $post_url );

        if ( $paged ) { $post_url .= intval( $paged ) . '/'; }

        $gallery_id = false;
        $gallery_item_id = false;
        $album_id = false;

        // Define the gallery_id -> can't assume it's $data['id'] because an album (gallery view) might be passed in
        if ( !empty( $data['album_id'] ) ) {
           
            // there's an album id, so this should be an album
            // therefore make the id of $item the id to share
            $gallery_id = $item['id'];
            // also make the image id to pass the cover image of the gallery
            $gallery_item_id = $item['cover_image_id'];
            // we HAVE to pass the album id
            $album_id = $data['album_id'];

            // the envira-social-picture is the cover_image_url
            $item['src'] = $item['cover_image_url'];

        } else {
            $gallery_id         = $data['id'];
            $gallery_item_id    = $id;
        }

        // Allow devs to filter the title and caption
        // Don't worry about url encoding - we'll handle this
        $title          = apply_filters( 'envira_social_sharing_title', $item['title'], $id, $item, $data, $i );
        $caption        = apply_filters( 'envira_social_sharing_caption', $item['caption'], $id, $item, $data, $i );
        $facebook_text  = apply_filters( 'envira_social_sharing_facebook_text', $this->get_config( 'social_facebook_text', $data ), $id, $item, $data, $i );
        $twitter_text   = apply_filters( 'envira_social_sharing_twitter_text', $this->get_config( 'social_twitter_text', $data ), $id, $item, $data, $i );

        // Iterate through networks, adding a button if enabled in the settings
        foreach ( $common->get_networks() as $network => $name ) {

            // Unset vars that might have been set in a previous loop
            unset( $url, $width, $height );

            // Skip network if not enabled
            if ( envira_mobile_detect()->isMobile() && ! $this->get_config( 'mobile_social_' . $network, $data ) ) {
                continue;
            }
            if ( ! envira_mobile_detect()->isMobile() && ! $this->get_config( 'social_' . $network, $data ) ) {
                continue;
            }

            // If the facebook text is nothing, add some spaces so that Facebook ignores the description and doesn't attempt to scrape it
            if ( trim($facebook_text) == '' ) {
                $facebook_text = "  ";
            } else {
                $facebook_text = urlencode( $facebook_text );
            }

            $tags                   = false;
            $pinterest_additional   = false;
            $email_url              = false;
            $button_specific_html   = false;

            // Define sharing URL and popup window dimensions
            switch ( $network ) {

                /**
                * Facebook
                */
                case 'facebook':

                    // Mobile check, is user allowing facebook on mobile for galleries?
                    if ( envira_mobile_detect()->isMobile() && ! $data['config']['mobile_social_facebook'] ) {
                        break;
                    }

                    // Get App ID
                    $app_id = $common->get_setting( 'facebook_app_id' );
                    $url = 'https://www.facebook.com/dialog/feed?app_id=' . $app_id . '&display=popup&link=' . urlencode( $post_url ) . '?' . $envira_permalink . 'picture=' . urlencode( $item['src'] ) . '&name=' . urlencode( strip_tags( $title ) ) . '&caption=' . urlencode( strip_tags( $caption ) )  . '&description=' .  $facebook_text  . '&redirect_uri=' . urlencode( $post_url . '#envira_social_sharing_close' );
                    $width = 626;
                    $height = 436;
                    if ( ! $data['config']['social_facebook_show_option_optional_text'] ) {
                        $facebook_text = "  ";
                    }
                    if ( ! empty( $data['config']['social_facebook_show_option_quote'] ) ) {
                        $facebook_quote = esc_html( $data['config']['social_facebook_quote'] );
                    } else {
                        $facebook_quote = false;
                    }
                    if ( $data['config']['social_facebook_show_option_tags'] ) {


                        if ( $data['config']['social_facebook_tag_options'] == "manual" ) {
                            $tags = sanitize_text_field( $data['config']['social_facebook_tags_manual'] );
                        } else if ( $data['config']['social_facebook_tag_options'] == "envira-tags" ) {
                            // If no more tags, return the classes.
                            $terms = wp_get_object_terms( $id, 'envira-tag' );
                            if ( count( $terms ) > 0 ) {
                                // we are only grabbing the first tag
                                $tags = "#" . $terms[0]->slug;
                            }
                        }
                    }
                    if ( $data['config']['social_facebook_show_option_caption'] ) {
                        $fb_caption = 'data-envira-facebook-caption="' . urlencode( strip_tags( $caption ) ) . '"';
                    } else {
                        $fb_caption = 'data-envira-facebook-caption=""';
                    }

                    // Build Button HTML
                    
                    $button_specific_html = '<a data-envira-album-id="' . $album_id . '" data-envira-social-picture="' .  $item['src']  . '" ' . $fb_caption  . ' data-envira-facebook-tags="' .  $tags  . '" data-envira-gallery-id="' .  $gallery_id  . '" data-envira-item-id="' .  $gallery_item_id  . '" data-envira-social-facebook-text="' .  $facebook_text  . '" data-envira-facebook-quote="' .  $facebook_quote  . '" data-envira-caption="' . urlencode( strip_tags( $caption ) ) . '" data-envira-title="' . urlencode( strip_tags( $title ) ) . '" href="' . $url . '" class="envira-social-button button-' . $network . '" data-envira-post-id="' . $post_id .'" >'.__( 'Share', 'envira-social' ).' <span>on ' . $name . '</span></a>';

                    break;

                /**
                * Twitter
                */
                case 'twitter':

                    // Mobile check, is user allowing twitter on mobile for galleries?
                    if ( envira_mobile_detect()->isMobile() && ! $data['config']['mobile_social_twitter'] ) {
                        break;
                    }

                    $url = 'https://twitter.com/intent/tweet?text=' . urlencode( strip_tags( $caption ) ) . urlencode( $twitter_text ) . '&url=' . urlencode( $post_url . '?' . $envira_permalink . 'envira_album_id='. $album_id . '&envira_social_gallery_id=' . $gallery_id . '&envira_social_gallery_item_id=' . $gallery_item_id . '&rand=' . mt_rand( 0, 99999 ) );
                    $width = 500;
                    $height = 300;

                    // Build Button HTML
                    
                    $button_specific_html = '<a href="' . $url . '" class="envira-social-button button-' . $network . '" >'.__( 'Share', 'envira-social' ).' <span>on ' . $name . '</span></a>';



                    break;
                /**
                * Google
                */
                case 'google':

                    // Mobile check, is user allowing google on mobile for galleries?
                    if ( envira_mobile_detect()->isMobile() && ! $data['config']['mobile_social_google'] ) {
                        break;
                    }

                    $url = 'https://plus.google.com/share?url=' . urlencode( $post_url . '?' . $envira_permalink . 'envira_album_id='. $album_id . '&envira_social_gallery_id=' . $gallery_id . '&envira_social_gallery_item_id=' . $gallery_item_id . '&rand=' . mt_rand( 0, 99999 ) );
                    $width = 500;
                    $height = 400;

                    // Build Button HTML
                    
                    $button_specific_html = '<a data-envira-album-id="' . $album_id . '" data-envira-gallery-id="' .  $gallery_id  . '" data-envira-item-id="' .  $gallery_item_id  . '" href="' . $url . '" class="envira-social-button button-' . $network . '" data-envira-post-id="' . $post_id .'" >'.__( 'Share', 'envira-social' ).' <span>on ' . $name . '</span></a>';

                    break;

                /**
                * Pinterest
                */
                case 'pinterest':

                    // Mobile check, is user allowing pinterest on mobile for galleries?
                    if ( envira_mobile_detect()->isMobile() && ! $data['config']['mobile_social_pinterest'] ) {
                        break;
                    }

                    $url = 'javascript:null(0);'; /* http://pinterest.com/pin/create/button/?url=' . urlencode( $post_url ) . '&media=' . urlencode( $item['src'] ) . '&description=' . urlencode( strip_tags( $caption ) ); */
                    $width = 500;
                    $height = 400;
                    $pinterest_share_type = $this->get_config( 'social_pinterest_type', $data );
                    if ( !$pinterest_share_type ) { // just in case we don't have anything, go with the default
                        $pinterest_share_type = "pin-one";
                    }
                    if ( !$caption ) {
                        // without a caption, pInterest grabs the page description
                        // so for now let's make the caption the title
                        $caption = $title;
                    }
                    $pinterest_additional = 'data-envira-pinterest-type="'.$pinterest_share_type.'" data-pin-do="buttonPin" data-pin-custom="true" data-envira-social-pinterest-description="' . urlencode( strip_tags( $caption ) ) . '"';

                    // Build Button HTML
                    
                    $button_specific_html = '<a data-envira-album-id="' . $album_id . '" ' . $pinterest_additional . ' data-envira-social-picture="' .  $item['src']  . '" data-envira-gallery-id="' .  $gallery_id  . '" data-envira-item-id="' .  $gallery_item_id  . '" data-envira-social-url="' .  $url  . '" data-envira-caption="' . urlencode( strip_tags( $caption ) ) . '" data-envira-title="' . urlencode( strip_tags( $title ) ) . '" href="' . $url . '" class="envira-social-button button-' . $network . '">'.__( 'Share', 'envira-social' ).' <span>on ' . $name . '</span></a>';

                    break;

                /**
                * Email
                */
                case 'email':

                    // Mobile check, is user allowing email on mobile for galleries?
                    if ( envira_mobile_detect()->isMobile() && ! $data['config']['mobile_social_email'] ) {
                        break;
                    }

                    if ( $post->ID ) {
                        $email_url = 'URL: ' . $post_url;
                        if ( $envira_permalink ) {
                            $email_url .= '?' . str_replace('&', '', $envira_permalink);
                        }
                        $email_url .= '%0D%0A';
                    }
                    $url = 'mailto:?subject=' . ( $title ) . '&body=' . $email_url . 'Photo: ' . urlencode( $item['src'] );
                    $width = 500;
                    $height = 400;

                    // Build Button HTML
                    
                    $button_specific_html = '<a href="' . $url . '" class="envira-social-button button-' . $network . '">'.__( 'Share', 'envira-social' ).' <span>on ' . $name . '</span></a>';

                    break;

            }

            // Build the button HTML, but with the specific data tags so we aren't needlessly repeating attributes

            if ( $button_specific_html ) { 

                // Only build if there is HTML

                $buttons .= '<div class="envira-social-network ' . $network . '" data-width="' . $width . '" data-height="' . $height . '" data-network="' . $network . '">' . $button_specific_html . '</div>';            

            }

        }
        
        // Close button HTML
        $buttons .= '
        </div>';

        // Return
        return $buttons; 
    }

    /**
    * Helper to output social sharing buttons for the lightbox
    *
    * @since 1.0.0
    *
    * @param array $data Gallery Data
    * @return string HTML
    */
    function get_lightbox_social_sharing_buttons( $data, $position = false ) {

        // Mobile check, is user allowing ANY social sharing on mobile for lightboxes?
        if ( envira_mobile_detect()->isMobile() && ! $data['config']['mobile_social_lightbox'] ) {
            return;
        }
        
        // Get instance and other variables
        $common     = Envira_Social_Common::get_instance();
        $deeplink   = $this->get_config( 'deeplinking', $data ) ? true : false;

        if ( $data['config']['lightbox_theme'] == 'base_dark' || $data['config']['lightbox_theme'] == 'base_light' ) {
            $buttons = '<div class="envira-social-buttons-exterior"><div class="envira-social-buttons" data-gallery-id="" data-gallery_item_id="" >';
        } else { /* legacy */
            $buttons = '<div class="envira-social-buttons position-' . $this->get_config( 'social_lightbox_position', $data ) . ' ' . ( ( $this->get_config( 'social_lightbox_outside', $data ) == 1 ) ? 'outside' : 'inside' ) . ' orientation-' . $this->get_config( 'social_lightbox_orientation', $data ) . '" data-gallery-id="" data-gallery_item_id="" >';
        }

        // Start


        $facebook_text  = apply_filters( 'envira_social_sharing_facebook_text', $this->get_config( 'social_facebook_text', $data ), $data, $position );
        $twitter_text   = apply_filters( 'envira_social_sharing_twitter_text', $this->get_config( 'social_twitter_text', $data ), $data, $position );

        // Iterate through networks, adding a button if enabled in the settings
        foreach ( $common->get_networks() as $network => $name ) {
            // Unset vars that might have been set in a previous loop
            unset($url, $width, $height);
            $deeplink = $this->get_config( 'deeplinking', $data ) ? true : false;

            // Skip network if not enabled
            if ( envira_mobile_detect()->isMobile() && ! $this->get_config( 'mobile_social_lightbox_' . $network, $data ) ) {
                continue;
            } else if ( ! envira_mobile_detect()->isMobile() && ! $this->get_config( 'social_lightbox_' . $network, $data ) ) {
                continue;
            }

            $button_specific_html   = false;
            $caption                = false;
            $post_url               = false;
            $title                  = false;
            $src                    = false;

            // Define sharing URL and popup window dimensions
            switch ( $network ) {

                /**
                * Facebook
                */
                case 'facebook':

                    // Get App ID
                    $app_id = $common->get_setting( 'facebook_app_id' );
                    $url = 'https://www.facebook.com/dialog/feed?app_id=' . $app_id . '&display=popup&link=' . urlencode( $post_url ) . '?picture=' . urlencode( $src ) . '&name=' . urlencode( strip_tags( $title ) ) . '&caption=' . urlencode( strip_tags( $caption ) )  . '&description=' .  $facebook_text  . '&redirect_uri=' . urlencode( $post_url . '#envira_social_sharing_close' );
                    $width = 626;
                    $height = 436;
                    if ( ! $data['config']['social_facebook_show_option_optional_text'] ) {
                        $facebook_text = "  ";
                    }
                    if ( ! empty( $data['config']['social_facebook_show_option_quote'] ) ) {
                        $facebook_quote = esc_html( $data['config']['social_facebook_quote'] );
                    } else {
                        $facebook_quote = false;
                    }
                    if ( $data['config']['social_facebook_show_option_tags'] ) {


                        if ( $data['config']['social_facebook_tag_options'] == "manual" ) {
                            $tags = sanitize_text_field( $data['config']['social_facebook_tags_manual'] );
                        } else if ( $data['config']['social_facebook_tag_options'] == "envira-tags" ) {
                            // If no more tags, return the classes.
                            $terms = wp_get_object_terms( $id, 'envira-tag' );
                            if ( count( $terms ) > 0 ) {
                                // we are only grabbing the first tag
                                $tags = "#" . $terms[0]->slug;
                            }
                        }
                    } else {
                        $tags = false;
                    }
                    if ( $data['config']['social_facebook_show_option_caption'] ) {
                        $fb_caption = 'data-envira-facebook-caption="' . urlencode( strip_tags( $caption ) ) . '"';
                    } else {
                        $fb_caption = 'data-envira-facebook-caption=""';
                    }

                    $button_specific_html = '<a href="#" class="envira-social-button" data-facebook-tags-manual="' .  esc_html($tags)  . '" data-envira-social-facebook-text="' .  esc_html($facebook_text)  . '" data-envira-facebook-quote="' .  $facebook_quote  . '" data-envira-caption="' . urlencode( strip_tags( $caption ) ) . '" >' . __( 'Share', 'envira-social' ) . ' <span> on ' . $name . '</span></a>';

                    

                    break;

                /**
                * Twitter
                */
                case 'twitter':

                    $url = 'https://twitter.com/intent/tweet?';
                    $width = 500;
                    $height = 300;

                    if ( ! $data['config']['social_twitter_text'] ) {
                        $twitter_text = "  ";
                    }

                    $button_specific_html = '<a href="#" class="envira-social-button" data-envira-social-twitter-text="' .  esc_html($twitter_text)  . '" data-envira-caption="' . urlencode( strip_tags( $caption ) ) . '" >' . __( 'Share', 'envira-social' ) . ' <span> on ' . $name . '</span></a>';

                    break;
                /**
                * Google
                */
                case 'google':

                    $url = 'https://plus.google.com/share?';
                    $width = 500;
                    $height = 400;

                    $button_specific_html = '<a href="#" class="envira-social-button" data-envira-caption="' . urlencode( strip_tags( $caption ) ) . '" >' . __( 'Share', 'envira-social' ) . ' <span> on ' . $name . '</span></a>';

                    break;

                /**
                * Pinterest
                */
                case 'pinterest':

                    $url = 'http://pinterest.com/pin/create/button/?';
                    $width = 500;
                    $height = 400;

                    $button_specific_html = '<a href="#" class="envira-social-button" data-envira-caption="' . urlencode( strip_tags( $caption ) ) . '" >' . __( 'Share', 'envira-social' ) . ' <span> on ' . $name . '</span></a>';

                    break;

                /**
                * Email
                */
                case 'email':

                    $url = 'mailto:?';
                    $width = 500;
                    $height = 400;

                    $button_specific_html = '<a href="#" class="envira-social-button">' . __( 'Share', 'envira-social' ) . ' <span> on ' . $name . '</span></a>';

                    break;

            }
            
            if ( $button_specific_html ) { 

                // Only build if there is HTML

            // Build Button HTML
            $buttons .= '<div class="envira-social-network ' . $network . '" data-width="' . $width . '" data-height="' . $height . '" data-network="' . $network . '" data-deeplinking="' . $network . '">' . $button_specific_html .'</div>';

            }

        }
        
        // Close button HTML
        $buttons .= '
        </div>';

        if ( $data['config']['lightbox_theme'] == 'base_dark' || $data['config']['lightbox_theme'] == 'base_light' ) {
            $buttons .= '</div>'; // end div for external
        }

        // Return
        return str_replace( "\n", "", $buttons );
    }
   
    /**
     * Helper method for retrieving gallery config values.
     *
     * @since 1.0.0
     *
     * @param string $key The config key to retrieve.
     * @param array $data The gallery data to use for retrieval.
     * @return string     Key value on success, default if not set.
     */
    public function get_config( $key, $data ) {

        // Determine whether data is for a gallery or album
        $post_type = get_post_type( $data['id'] );

        // If post type is false, we're probably on a dynamic gallery/album
        // Grab the ID from the config
        if ( ! $post_type && isset( $data['config']['id'] ) ) {
            $post_type = get_post_type( $data['config']['id'] );
        }

        switch ( $post_type ) {
            case 'envira':
                $instance = Envira_Gallery_Shortcode::get_instance();
                break;
            case 'envira_album':
                $instance = Envira_Albums_Shortcode::get_instance();
                break;
        }

        // If no instance was set, bail
        if ( ! isset( $instance ) ) {
            return false;
        }

        // Return value
        return $instance->get_config( $key, $data );

    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Envira_Social_Shortcode object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Envira_Social_Shortcode ) ) {
            self::$instance = new Envira_Social_Shortcode();
        }

        return self::$instance;

    }

}

// Load the shortcode class.
$envira_social_shortcode = Envira_Social_Shortcode::get_instance();