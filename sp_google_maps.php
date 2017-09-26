<?php
/**
 * SP Google Maps
 * 
 * @package     PluginPackage
 * @author      Kudratullah
 * @copyright   2017 SamePage Inc.
 * @license     GPL-2.0+
 * @version     1.1.8
 * 
 * @wordpress-plugin
 * Plugin Name: SP Google Maps
 * Plugin URI: https://wordpress.org/plugins/sp-google-maps/
 * Description: A simple plugin that embed Google Maps and Google Maps Street View. With Google Maps Routeing Functionality.
 * Version: 1.1.7
 * Author: SamePage Inc.
 * Author URI: http://samepagenet.com/
 * Text Domain: sp_google_maps
 * Domain Path: /languages
 * License: GPLv2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}
define('SPGMVersion', '1.1.8');
define("SPGMDir", plugin_dir_path( __FILE__ ));
define("SPGMBase", plugin_basename(__FILE__));
if(!function_exists('spgm_i18n')){
	add_action('plugins_loaded', 'SPGMi18n');
	function SPGMi18n() {
		load_plugin_textdomain( 'sp_google_maps', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
	}
}
// include admin scripts only in wp-admin
if( is_admin() ) {
	require_once(SPGMDir."admin.php");
	require_once(SPGMDir."metaboxes.php");
}

add_filter('plugin_row_meta',  'SPGMPluginsMetaLinks', 10, 2);
/**
 * Add support and social link to plugin meta 
 * @param array $links
 * @param string $file
 * @return array
 */
function SPGMPluginsMetaLinks($links, $file) {
	if ($file == SPGMBase) {
		$links[] = '<a href="http://samepagenet.com/contact-us" target="_blank">' . __('Support', 'sp_google_maps') . '</a>';
		$links[] = '<a href="https://www.facebook.com/samepageltd" target="_blank">' . __('Facebook', 'sp_google_maps') . '</a>';
		$links[] = '<a href="https://plus.google.com/100137269262808815094/" target="_blank">' . __('Google Plus', 'sp_google_maps') . '</a>';
	}
	return $links;
}
add_filter('plugin_action_links_'.SPGMBase, 'RegisterPluginSettingsPage', 10, 1);
/**
 * Add plugin page links with other action links
 * @param array $actions
 * @return array
 */
function RegisterPluginSettingsPage($actions){
	$actions['view_item'] = '<a href="' . admin_url( '/edit.php?post_type=sp_google_maps' ) . '">' . __('View Maps', 'sp_google_maps') . '</a>';
	$actions['maps_settings'] = '<a href="' . admin_url( '/edit.php?post_type=sp_google_maps&page=maps-settings' ) . '">' . __('Settings', 'sp_google_maps') . '</a>';
	return $actions;
}
// Register Custom Post Type Creating New Map
function sp_google_maps() {

	$labels = array(
		'name'                => _x( 'SP Google Maps', 'Post Type General Name', 'sp_google_maps' ),
		'singular_name'       => _x( 'SP Google Maps', 'Post Type Singular Name', 'sp_google_maps' ),
		'menu_name'           => __( 'SP Google Maps', 'sp_google_maps' ),
		'name_admin_bar'      => __( 'SP Google Maps', 'sp_google_maps' ),
		'parent_item_colon'   => __( 'Parent Item:', 'sp_google_maps' ),
		'all_items'           => __( 'All Maps', 'sp_google_maps' ),
		'add_new_item'        => __( 'Add New Map', 'sp_google_maps' ),
		'add_new'             => __( 'Add New', 'sp_google_maps' ),
		'new_item'            => __( 'New Map', 'sp_google_maps' ),
		'edit_item'           => __( 'Edit Map', 'sp_google_maps' ),
		'update_item'         => __( 'Update Map', 'sp_google_maps' ),
		'view_item'           => __( 'View Map', 'sp_google_maps' ),
		'search_items'        => __( 'Search Map', 'sp_google_maps' ),
		'not_found'           => __( 'Not found', 'sp_google_maps' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'sp_google_maps' ),
	);
	$args = array(
		'label'               => __( 'SP Google Maps', 'sp_google_maps' ),
		'description'         => __( 'A simple plugin that embed Google Maps and Google Maps Street View. With Geo Routing Functionality.', 'sp_google_maps' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', ),
		'hierarchical'        => false,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 20,
		'menu_icon'           => 'dashicons-location-alt',
		'show_in_admin_bar'   => false,
		'show_in_nav_menus'   => false,
		'can_export'          => true,
		'has_archive'         => false,		
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'rewrite'             => false,
		'capability_type'     => 'page',
	);
	register_post_type( 'sp_google_maps', $args );
}
// Hook into the 'init' action
add_action( 'init', 'sp_google_maps', 10 );

//add custom admin css
add_action( 'admin_enqueue_scripts', 'spgm_admin_scripts' );
/**
 * Include admin styles and javascripts
 * @return void
 */
function spgm_admin_scripts() {
	
	global $post_type;
	global $post;
	$scriptPrefix = ( defined('WP_DEBUG') && WP_DEBUG )? "" : ".min";
	if(($post_type == 'sp_google_maps') || (isset($_GET['page']) && $_GET['page'] == "maps-settings")){
		$mapdata = array();
		if($post instanceof WP_Post){
			$values = get_post_custom( $post->ID );
			$maps_latlng = isset( $values['maps-latlng'] ) ? $values['maps-latlng'][0] : '35.68169,139.765396';
			$maps_latlng = explode(",",$maps_latlng);
			$lat = $maps_latlng[0];
			$lng = $maps_latlng[1];
			$maps_zoom = isset( $values['maps-zoom'] )? $values['maps-zoom'][0] : 15;
			
			$maps_SV_latlng = isset( $values['maps-SV-latlng'] ) ? $values['maps-SV-latlng'][0] : '35.68169,139.765396';
			$maps_SV_latlng = explode(",",$maps_SV_latlng);
			$sv_lat = $maps_SV_latlng[0];
			$sv_lng = $maps_SV_latlng[1];
			$maps_SV_zoom = isset( $values['maps-SV-zoom'] ) ? $values['maps-SV-zoom'][0] : 1;
			$maps_pov = isset( $values['maps-pov'] ) ? $values['maps-pov'][0] : '104.40013753534012,17.25915572778863';
			$maps_pov = explode(",",$maps_pov);
			$heading = $maps_pov[0];
			$pitch = $maps_pov[1];
			$mapdata = array(
					'maps_lat' => $lat,
					'maps_lng' => $lng,
					'maps_zoom' => $maps_zoom,
					'sv_lat' => $sv_lat,
					'sv_lng' => $sv_lng,
					'sv_zoom' => $maps_SV_zoom,
					'heading' => $heading,
					'pitch' => $pitch
			);
			$settings = get_option("spgmSettings");
			
			$GoogleMapsSRC = "https://maps.googleapis.com/maps/api/js";
			$GoogleMapsQuery = array();
			if( ! empty( $settings['apiKey'] ) ) $GoogleMapsQuery["key"] = $settings['apiKey'];
			$GoogleMapsQuery["libraries"] = "geometry";
			$GoogleMapsQuery["language"] = substr(get_bloginfo ( 'language' ), 0, 2);
			$GoogleMapsQuery = http_build_query( apply_filters( "googlemapsapi_params", $GoogleMapsQuery ) );
			$GoogleMapsSRC .= ( !empty( $GoogleMapsQuery ) ) ? "?".$GoogleMapsQuery : "";
			
			wp_register_script( 'Google-Maps', $GoogleMapsSRC, false, null);
			wp_register_script( 'AdminMapScript', plugins_url( "/js/admin{$scriptPrefix}.js", __FILE__ ), array('jquery','Google-Maps'), SPGMVersion, true );
			
			// Localize the script with new data
			wp_localize_script( 'Google-Maps', 'mapdata', $mapdata );
			
			wp_enqueue_script('jquery');
			wp_enqueue_script('Google-Maps');
			wp_enqueue_script('AdminMapScript');
		}
		
		wp_register_style( 'Admin-CSS', plugins_url( "/css/admin{$scriptPrefix}.css", __FILE__ ), false, SPGMVersion );
		wp_enqueue_style( 'Admin-CSS' );
	}
}

// Adding Map Shortcode
function sp_google_maps_shortcode( $atts ) {
	// Attributes
	extract( shortcode_atts(
		array(
			'id' => '', // post id of the custom post type of google maps
		), $atts )
	);
	
	$output = "";
	
	if(!$id || empty($id) || !is_object(get_post($id))){
		return __("Map Not Found");
	}else{
		$scriptPrefix = ( defined('WP_DEBUG') && WP_DEBUG )? "" : ".min";
		$settings = get_option("spgmSettings"); 
		$map = get_post($id);
		
		$values = get_post_custom($id);
		
		
		$map_title = $values['maps-title'][0];
		$map_description = apply_filters('the_content',$map->post_content);
		
		$maps_latlng = $values['maps-latlng'][0];
		$latlng = explode(",",$maps_latlng);
		$lat = $latlng[0];
		$lng = $latlng[1];
		$maps_zoom = isset( $values['maps-zoom'] )? $values['maps-zoom'][0] : 15;
			
		$maps_SV_latlng = isset( $values['maps-SV-latlng'] ) ? $values['maps-SV-latlng'][0] : '35.68169,139.765396';
		$maps_SV_latlng = explode(",",$maps_SV_latlng);
		$sv_lat = $maps_SV_latlng[0];
		$sv_lng = $maps_SV_latlng[1];
		$maps_SV_zoom = isset( $values['maps-SV-zoom'] ) ? $values['maps-SV-zoom'][0] : 1;
		
		$maps_pov = $values['maps-pov'][0];
		$maps_pov = explode(",",$maps_pov);
		$heading = $maps_pov[0];
		$pitch = $maps_pov[1];
		$maps_icon = $values['maps-icon'][0];
		$maps_style = (empty( $values['maps-style'][0] )) ? $settings['MapsStyleJson'] : $values['maps-style'][0];
		$maps_css = (empty( $values['maps-css'][0] )) ? '' : $values['maps-css'][0];
		//maps mouse wheel scroll settings
		$maps_mwscroll = ($values['maps-mwscroll'][0] == '0')? 'false':'true';
		
		//add script and style
		$GoogleMapsSRC = "https://maps.googleapis.com/maps/api/js";
		$GoogleMapsQuery = array();
		if( ! empty( $settings['apiKey'] ) ) $GoogleMapsQuery["key"] = $settings['apiKey'];
		$GoogleMapsQuery["libraries"] = "geometry";
		$GoogleMapsQuery["language"] = substr(get_bloginfo ( 'language' ), 0, 2);
		$GoogleMapsQuery = http_build_query( apply_filters( "googlemapsapi_params", $GoogleMapsQuery ) );
		$GoogleMapsSRC .= ( !empty( $GoogleMapsQuery ) ) ? "?".$GoogleMapsQuery : "";
		
		wp_register_style( 'SP-Google-Maps-Style', plugins_url( "/css/sp_google_maps{$scriptPrefix}.css", __FILE__ ), false, SPGMVersion );
		wp_register_script( 'Google-Maps', $GoogleMapsSRC, false, null);
		wp_register_script( 'SP-Google-Maps-Script', plugins_url( "/js/sp_google_maps{$scriptPrefix}.js", __FILE__ ), array( 'Google-Maps', 'jquery' ), SPGMVersion, true);
		
		// Localize the script with new data
		$mapdata = array(
				'mapid' => $id,
				'lat' => $lat,
				'lng' => $lng,
				'maps_zoom' => $maps_zoom,
				'sv_lat' => $sv_lat,
				'sv_lng' => $sv_lng,
				'sv_zoom' => $maps_SV_zoom,
				'heading' => $heading,
				'pitch' => $pitch,
				'mwscroll' => $maps_mwscroll,
				'icon' => $maps_icon,
				'title' => $map_title,
				'description' => $map_description,
				'messages' => array(
						/*General messages*/
						'client_location_request' => __('Type Your Location', 'sp_google_maps'),
						/*geo location api response messages*/
						'geo_not_supported' => __("Your Browser Doesn't Support Location Service.", 'sp_google_maps'),
						'geo_timeout' => __("Request Timeout. Please Reload Your Browser.",'sp_google_maps'),
						'geo_position_unavailable' => __("Your Position is Unavailable.\nPlease Activate Your GPS And Location Services And Reload The Page.",'sp_google_maps'),
						'geo_permission_denied' => __("Your Location Settings Is Blocked.\nPlease Change Your Location Sharing Settings And Reload The Page.",'sp_google_maps'),
						'geo_unknown_error' => __("An Unknown Error Occurred.\nPlease Try Again After Sometime.",'sp_google_maps'),
						/*google maps api response messages*/
						'g_zero_results' => __('No route could be found between the origin and destination.','sp_google_maps'),
						'g_request_denied' => __('This webpage is not allowed to use the directions service.','sp_google_maps'),
						'g_over_query_limit' => __('The webpage has gone over the requests limit in too short a period of time.','sp_google_maps'),
						'g_not_found' => __('At least one of the origin, destination, or waypoints could not be geocoded.','sp_google_maps'),
						'g_invalid_request' => __('The Directions Request provided was invalid.','sp_google_maps'),
						'g_no_status_found' => __('There was an unknown error in your request. Request status is:','sp_google_maps'),
				),
		);
		
		if(!empty($maps_style))
			$mapdata['style'] = $maps_style;
		wp_localize_script( 'Google-Maps', 'mapdata', $mapdata );
		wp_enqueue_style( 'SP-Google-Maps-Style' );
		wp_add_inline_style( 'SP-Google-Maps-Style', $maps_css );
		wp_enqueue_script('jquery');
		wp_enqueue_script('Google-Maps');
		wp_enqueue_script('SP-Google-Maps-Script');
		
		$output .= '<div class="sp_maps_container">';
			$output .= '<div class="google-maps-basic" id="map_canvas_'.$id.'"></div>';
			$output .= '<div class="google-maps-steetview" id="pano_'.$id.'"></div>';
			$output .= '<div class="cf"></div>';
			$output .= '<div class="google-maps-route-calc">';
		if(
				($settings['routeCal'] === "both")
				|| ($settings['routeCal'] === "desktop" && !wp_is_mobile())
				|| ($settings['routeCal'] === "mobile" && wp_is_mobile())
				){
			$output .= '<a href="#" class="travelMode" data-travelMode="DRIVING">'. __('Show Car Route From Your Location', 'sp_google_maps') .'</a><br>';
			$output .= '<a href="#" class="travelMode" data-travelMode="WALKING">'. __('Show Walking Route', 'sp_google_maps') .'</a><br>';
			$output .= '<a href="#" class="travelMode" data-travelMode="TRANSIT" target="_blank">'. __('Show Public Transport Route', 'sp_google_maps') .'</a>';
		}
			$output .= '</div>';
		$output .= '</div>';
		return $output;
	}
}

add_shortcode( 'SPGM', 'sp_google_maps_shortcode' );

function SPGMinstall() {
	$settings = get_option("spgmSettings");
	//SPGMVersion
	if(!$settings){
		$opts = array(
				"version" => SPGMVersion,
				"apiKey" => "",
				"routeCal" => "mobile",
				"MapsStyleJson" => "",//'[{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#46bcec"},{"visibility":"on"}]}]',
		);
		add_option( 'spgmSettings',$opts);
	}
}
register_activation_hook( __FILE__, 'SPGMinstall' );