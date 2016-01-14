<?php
/**
 * @package WordPress
 * @subpackage SP Google Maps
 * @version 1.1
 * @since SP Google Maps 1.0
 */
/*
Plugin Name: SP Google Maps
Plugin URI: http://samepagenet.com
Description: A simple plugin that embed Google Maps and Google Maps Street View. With Geo Routeing Functionality.
Version: 1.1
Author: mhamudul_hk
Author URI: http://samepagenet.com/
License: GPLv2 or later
Text Domain: sp_google_maps
Domain Path: /languages/
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}


add_action('plugins_loaded', 'i18n');
function i18n() {
	load_plugin_textdomain( 'sp_google_maps', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');        
}

//Add Extra Links To Plugin Meta

add_filter('plugin_row_meta',  'Register_Plugins_Links', 10, 2);
function Register_Plugins_Links($links, $file) {
	$base = plugin_basename(__FILE__);
	if ($file == $base) {
		$links[] = '<a href="' . admin_url( '/edit.php?post_type=sp_google_maps' ) . '">' . __('View Maps', 'sp_google_maps') . '</a>';
		$links[] = '<a href="http://samepagenet.com/contact-us" target="_blank">' . __('Support', 'sp_google_maps') . '</a>';
		$links[] = '<a href="https://www.facebook.com/samepageltd" target="_blank">' . __('Facebook', 'sp_google_maps') . '</a>';
		$links[] = '<a href="https://plus.google.com/100137269262808815094/" target="_blank">' . __('Google Plus', 'sp_google_maps') . '</a>';
	}
	return $links;
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
		'label'               => __( 'sp_google_maps', 'sp_google_maps' ),
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
add_action( 'init', 'sp_google_maps', 0 );

//add custom admin css

function load_custom_wp_admin_style_script() {
	
	global $post_type;
	global $post;
	
	if (($post_type == 'sp_google_maps')) :
	
		$values = get_post_custom( $post->ID );
		$maps_latlng = isset( $values['maps-latlng'] ) ? $values['maps-latlng'][0] : '35.68169,139.765396';
		$maps_latlng = explode(",",$maps_latlng);
		$lat = $maps_latlng[0];
		$lng = $maps_latlng[1];
		
		$maps_pov = isset( $values['maps-pov'] ) ? $values['maps-pov'][0] : '104.40013753534012,17.25915572778863';
		$maps_pov = explode(",",$maps_pov);
		$heading = $maps_pov[0];
		$pitch = $maps_pov[1];
		
		wp_register_script( 'Google-Maps', "//maps.googleapis.com/maps/api/js?v=3.exp", false, null);
		wp_register_style( 'Meta-Box', plugins_url('/css/admin.css', __FILE__), false, '1.0.0' );
		
		if(defined('WP_DEBUG') && WP_DEBUG){
			wp_register_script( 'AdminMapScript', plugins_url('/js/admin.js', __FILE__), array('jquery','Google-Maps'), '1.0.0', true);
		}else{
			wp_register_script( 'AdminMapScript', plugins_url('/js/admin.min.js', __FILE__), array('jquery','Google-Maps'), '1.0.0', true);
		}
		
		// Localize the script with new data
		$mapdata = array(
				'lat' => $lat,
				'lng' => $lng,
				'heading' => $heading,
				'pitch' => $pitch
		);
		wp_localize_script( 'Google-Maps', 'mapdata', $mapdata );
		
		wp_enqueue_style( 'Meta-Box' );
		wp_enqueue_script('jquery');
		wp_enqueue_script('Google-Maps');
		wp_enqueue_script('AdminMapScript');
	endif;
}
add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style_script' );


// meta box for maps

add_action( 'add_meta_boxes', 'sp_google_maps_shortcode_meta_box_add' );

function sp_google_maps_shortcode_meta_box_add(){
	add_meta_box( 'sp-maps-shortcode', 'Google Maps ShortCode', 'sp_google_maps_shortcode_meta', 'sp_google_maps', 'side', 'default' );
}

function sp_google_maps_shortcode_meta(){
	global $post;
?>
	<p>
		<strong>
			<label for="map_shortcode"><?php _e("ShortCode For This Map", 'sp_google_maps'); ?></label>
		</strong>
	</p>
	<?php if(get_post_status( $post->ID ) === "publish"): ?>
	<input type="text" id="map_shortcode" value='[SPGM id="<?php echo $post->ID; ?>"]' onclick="select();" />
	<br>
	<code><?php _e('Copy and Paste This ShortCode To Use This Map', 'sp_google_maps'); ?></code>
	<?php else: ?>
	<p><small><?php _e('ShortCode will apear after first save.', 'sp_google_maps'); ?></small></p>
	<?php endif; ?>
<?php
}

add_action( 'add_meta_boxes', 'sp_google_maps_meta_box_add' );

function sp_google_maps_meta_box_add(){
	add_meta_box( 'sp-maps-data', 'Google Maps Settings', 'sp_google_maps_settings', 'sp_google_maps', 'normal', 'high' );
}

function sp_google_maps_settings(){
	global $post;
	$values = get_post_custom( $post->ID );
	$maps_title = isset( $values['maps-title'] )? $values['maps-title'][0] : "";
	$maps_mwscroll = isset( $values['maps-mwscroll'] )? $values['maps-mwscroll'][0] : '';
	$maps_latlng = isset( $values['maps-latlng'] ) ? $values['maps-latlng'][0] : '';
	$maps_pov = isset( $values['maps-pov'] ) ? $values['maps-pov'][0] : '';
	$maps_style = isset( $values['maps-style'] ) ? $values['maps-style'][0] : '';
	$maps_icon = isset( $values['maps-icon'] ) ? $values['maps-icon'][0] : plugin_dir_url( __FILE__ ).'map_marker_icon.png';
	$maps_css = (isset( $values['maps-css'][0] )) ? $values['maps-css'][0] : '';
	
	// We'll use this nonce field later on when saving.
	wp_nonce_field( 'sp_google_maps_nonce', 'meta_box_nonce' );
?>

	<div class="group-container cf">
		<div class="box-group">
			<div class="box-label">
				<label for="maps-title"><?php _e('Google Maps Marker Title', 'sp_google_maps'); ?></label>
			</div>
			<div class="box-field">
				<input class="regular-text" type="text" name="maps-title" id="maps-title" value="<?php echo $maps_title; ?>" placeholder="<?php _e('Google Maps Marker Title', 'sp_google_maps'); ?>" />
				<div class="cf"></div>	
				<code><?php _e('This title will show under mouse cursor when visitor mouse hover the map marker icon', 'sp_google_maps'); ?></code>
			</div>
		</div>
		<div class="cf"></div>
		<div class="box-group">
			<div class="box-label">
				<?php _e('Mouse Wheel Scroll Settings:', 'sp_google_maps'); ?>
			</div>
			<div class="box-field">
				<label for="maps-mwscroll-disabled">
				<input class="radio" type="radio" name="maps-mwscroll" id="maps-mwscroll-disabled" value="0" <?php
					echo ($maps_mwscroll == 0)? 'checked' : '';
				?>/>
				<?php _e('Disable', 'sp_google_maps'); ?></label>
				<div class="cf"></div>
				<label for="maps-mwscroll-enabled">
				<input class="radio" type="radio" name="maps-mwscroll" id="maps-mwscroll-enabled" value="1"  <?php
					echo ($maps_mwscroll == 1|| $maps_mwscroll == "")? 'checked' : '';
				?>/>
				<?php _e('Enable', 'sp_google_maps'); ?></label>
				<div class="cf"></div>
				<code><?php _e('Enable or Disable Mouse Wheel Scroll On Google Maps And Google Maps Street View.<br>Enabled By Default', 'sp_google_maps'); ?></code>
			</div>
		</div>
		<div class="cf"></div>
		<div class="box-group">
			<div class="box-label">
				<label for="maps-latlng"><?php _e('Google Maps Latitude Longitude:', 'sp_google_maps'); ?></label>
			</div>
			<div class="box-field">
				<input class="regular-text" type="text" name="maps-latlng" id="maps-latlng" value="<?php echo $maps_latlng; ?>" placeholder="23.727663,90.41054964" />
				<div class="cf"></div>	
				<code><?php _e('Input Latitude &amp; Longitude Saperated By Comma. Or Choose Your Location From from the map bellow. eg. lat,lan.', 'sp_google_maps'); ?></code>
			</div>
			<div class="cf"></div>
			<div class="map-preview">
				<p><?php _e('Choose Your Location By Dragging The Marker', 'sp_google_maps'); ?></p>
				<div id="map"></div>
			</div>
		</div>
		<div class="cf"></div>
		<div class="box-group">
			<div class="box-label">
				<label for="maps-pov"><?php _e('Street View Angle:', 'sp_google_maps'); ?></label>
			</div>
			<div class="box-field">
				<input class="regular-text" type="text" name="maps-pov" id="maps-pov" value="<?php echo $maps_pov; ?>" placeholder="-133.26844,32.265165" />
				<div class="cf"></div>
				<code><?php _e('Saperated By Comma. Set Heading &amp; Pitch, eg. heading,pitch. Or use the Street Map Preview to set Point-of-View.', 'sp_google_maps'); ?></code>
			</div>
			<div class="cf"></div>
			<div class="map-preview">
				<p><?php _e('Choose Your Desigre Angle', 'sp_google_maps'); ?></p>
				<div id="pano"></div>
			</div>
		</div>
		<div class="cf"></div>
		<div class="box-group">
			<div class="box-label">
				<label for="maps-icon"><?php _e('Google Maps Marker Icon', 'sp_google_maps'); ?></label>
			</div>
			<div class="box-field">
				<input class="regular-text" type="text" name="maps-icon" id="maps-icon" value="<?php echo $maps_icon; ?>" placeholder="<?php _e('Marker Icon URL', 'sp_google_maps'); ?>" />
				<img src="<?php echo $maps_icon; ?>" alt="<?php _e('Google Maps Custom Marker Icon', 'sp_google_maps'); ?>" style="position: absolute;width: 34px;margin: 0 0 0 20px;padding: 0;top: -5px;">
				<div class="cf"></div>	
				<code><?php _e('Icon Url. Recommended 64x64PX PNG Image', 'sp_google_maps'); ?></code>
			</div>
		</div>
		<div class="cf"></div>
		<div class="box-group">
			<div class="box-label">
				<label for="maps-style"><?php _e('Maps Style', 'sp_google_maps'); ?></label>
			</div>
			<div class="box-field">
				<textarea class="regular-text" name="maps-style" id="maps-style" placeholder='[{
"featureType": "water",
"stylers": [{
	"color": "#19a0d8"
}]
}, {...' style="width: 100%;min-height: 150px;"><?php echo $maps_style; ?></textarea>
				<div class="cf"></div>
				<code><?php _e('Google Maps Styled API.<br>Get Settings For Colorful Google Maps From <a href="https://snazzymaps.com/">https://snazzymaps.com/</a><br>Copy and paste the <em>JavaScript Style Array</em> Here.<br>Or create a new style with  <a href="http://gmaps-samples-v3.googlecode.com/svn/trunk/styledmaps/wizard/index.html">Google Maps API Styled Map Wizard</a> and copy-paste the Json data here.', 'sp_google_maps'); ?></code>
			</div>
		</div>
		<div class="cf"></div>
		<div class="box-group">
			<div class="box-label">
				<label for="maps-css"><?php _e('Custom CSS', 'sp_google_maps'); ?></label>
			</div>
			<div class="box-field">
				<textarea class="regular-text" name="maps-css" id="maps-css" placeholder='.sp_maps_container{
	position:relative;
	display:block;
	margin:0 auto;
}...' style="width: 100%;min-height: 150px;"><?php echo $maps_css; ?></textarea>
				<div class="cf"></div>
				<code><?php _e('Put Your Custom CSS for This Map', 'sp_google_maps'); ?></code>
				<code><?php _e('Available CSS Class Selectors are:', 'sp_google_maps'); ?> .sp_maps_container, .google-maps-steetview, .google-maps-basic, .google-maps-route-calc</code>
			</div>
		</div>
		<div class="cf"></div>
	</div>
<?php  
}

add_action( 'save_post', 'sp_google_maps_meta_box_save' );

function sp_google_maps_meta_box_save( $post_id ){
	// Bail if we're doing an auto save
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	// if our nonce isn't there, or we can't verify it, bail
	if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'sp_google_maps_nonce' ) ) return;
	// if our current user can't edit this post, bail
	if( !current_user_can( 'edit_post' ) ) return;
	
	$allowed = array();
	if( isset( $_POST['maps-title'] ) )
		update_post_meta($post_id, 'maps-title', wp_kses( $_POST['maps-title'], $allowed ) );
	if( isset( $_POST['maps-mwscroll'] ) )
		update_post_meta($post_id, 'maps-mwscroll', wp_kses( $_POST['maps-mwscroll'], $allowed ) );
	if( isset( $_POST['maps-latlng'] ) )
		update_post_meta( $post_id, 'maps-latlng', wp_kses( $_POST['maps-latlng'], $allowed ) );
	
	if( isset( $_POST['maps-pov'] ) )
		update_post_meta( $post_id, 'maps-pov', wp_kses( $_POST['maps-pov'], $allowed ) );
	
	if( isset( $_POST['maps-style'] ) )
		update_post_meta( $post_id, 'maps-style', wp_kses( $_POST['maps-style'], $allowed ) );
	
	if( isset( $_POST['maps-icon'] ) )
		update_post_meta( $post_id, 'maps-icon', wp_kses( $_POST['maps-icon'], $allowed ) );
	if( isset( $_POST['maps-css'] ) )
		update_post_meta( $post_id, 'maps-css', wp_kses( $_POST['maps-css'], $allowed ) );
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
		$map = get_post($id);
		
		$values = get_post_custom($id);
		
		
		$map_title = $values['maps-title'][0];
		$map_description = $map->post_content;
		
		$maps_latlng = $values['maps-latlng'][0];
		$latlng = explode(",",$maps_latlng);
		$lat = $latlng[0];
		$lng = $latlng[1];
		$maps_pov = $values['maps-pov'][0];
		
		$pov = explode(",",$maps_pov);
		$heading = $pov[0];
		$pitch = $pov[1];
		$maps_icon = $values['maps-icon'][0];
		$maps_style = (empty( $values['maps-style'][0] )) ? '[{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#46bcec"},{"visibility":"on"}]}]' : $values['maps-style'][0];
		$maps_css = (empty( $values['maps-css'][0] )) ? '' : $values['maps-css'][0];
		//maps mouse wheel scroll settings
		$maps_mwscroll = ($values['maps-mwscroll'][0] == '0')? 'false':'true';
		//add script and style
		wp_register_style( 'SP-Google-Maps-Style', plugins_url('/css/sp_google_maps.css', __FILE__), false, '1.0.0' );
		
		wp_register_script( 'Google-Maps', "//maps.googleapis.com/maps/api/js?v=3.exp", false, null);
		if(defined('WP_DEBUG') && WP_DEBUG){
			wp_register_script( 'SP-Google-Maps-Script', plugins_url('/js/sp_google_maps.js', __FILE__), array('Google-Maps','jquery'), '1.0.0', true);
		}else{
			wp_register_script( 'SP-Google-Maps-Script', plugins_url('/js/sp_google_maps.min.js', __FILE__), array('Google-Maps','jquery'), '1.0.0', true);
		}
		
		// Localize the script with new data
		$mapdata = array(
				'mapid' => $id,
				'lat' => $lat,
				'lng' => $lng,
				'mwscroll' => $maps_mwscroll,
				'style' => $maps_style,
				'icon' => $maps_icon,
				'title' => $map_title,
				'description' => $map_description,
				'heading' => $heading,
				'pitch' => $pitch,
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
				)
		);
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
		if(wp_is_mobile()){
				$output .= '<a href="#" class="travelMode" data-travelMode="DRIVING">'. __('Show Car Route From Your Location', 'sp_google_maps') .'</a><br>';
				$output .= '<a href="#" class="travelMode" data-travelMode="WALKING">'. __('Show Walking Route', 'sp_google_maps') .'</a><br>';
				$output .= '<a href="#" class="travelMode" data-travelMode="TRANSIT">'. __('Show Public Transport Route', 'sp_google_maps') .'</a>';
				$output .= '<a href="#" class="travelMode" data-travelMode="TRANSIT">'. __('Show Public Transport Route', 'sp_google_maps') .'</a>';
		}
			$output .= '</div>';
		$output .= '</div>';
		return $output;
	}
}

add_shortcode( 'SPGM', 'sp_google_maps_shortcode' );
//Added By Yasunori Kawakami
function sp_google_maps_isMobile() {
	$ua = $_SERVER['HTTP_USER_AGENT'];
	if(preg_match("@(Android)|(iPhone)|(Windows Phone)|(iPad)@", $ua)){
		return true;
	}
	return false;
}