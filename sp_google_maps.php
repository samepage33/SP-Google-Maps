<?php
/**
 * @package SP Google Maps
 */
/*
Plugin Name: SP Google Maps
Plugin URI: http://samepagenet.com
Description: A simple plugin that embed Google Maps and Google Maps Street View. With Geo Routeing Functionality.
Version: 1.0
Author: Kudratullah
Author URI: http://samepagenet.com/
License: GPLv2 or later
Text Domain: sp_google_maps
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

//Add Extra Links To Plugin Meta

add_filter('plugin_row_meta',  'Register_Plugins_Links', 10, 2);
function Register_Plugins_Links($links, $file) {
	$base = plugin_basename(__FILE__);
	if ($file == $base) {
		$links[] = '<a href="' . admin_url( '/edit.php?post_type=sp_google_maps' ) . '">' . __('View Maps') . '</a>';
		$links[] = '<a href="http://samepagenet.com/contact-us" target="_blank">' . __('Support') . '</a>';
		$links[] = '<a href="https://www.facebook.com/samepageltd" target="_blank">' . __('Facebook') . '</a>';
		$links[] = '<a href="https://plus.google.com/100137269262808815094/" target="_blank">' . __('Google Plus') . '</a>';
	}
	return $links;
}
// Register Custom Post Type Creating New Map
function sp_google_maps() {

	$labels = array(
		'name'                => _x( 'Google Maps', 'Post Type General Name', 'sp_google_maps' ),
		'singular_name'       => _x( 'Google Maps', 'Post Type Singular Name', 'sp_google_maps' ),
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
		'description'         => __( 'A simple plugin that embed Google Maps and Google Maps Street View. With Geo Routeing Functionality.', 'sp_google_maps' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 20,
		'menu_icon'           => 'dashicons-location-alt',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,		
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
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
	
	$values = get_post_custom( $post->ID );
	$maps_latlng = isset( $values['maps-latlng'] ) ? $values['maps-latlng'][0] : '23.727369,90.396604';
	$maps_latlng = explode(",",$maps_latlng);
	$lat = $maps_latlng[0];
	$lng = $maps_latlng[1];
	
	$maps_pov = isset( $values['maps-pov'] ) ? $values['maps-pov'][0] : '-181.32468802438547,15.485711770927328';
	$maps_pov = explode(",",$maps_pov);
	$heading = $maps_pov[0];
	$pitch = $maps_pov[1];
	
	if (($_GET['post_type'] == 'sp_google_maps') || ($post_type == 'sp_google_maps')) :
	
		wp_register_style( 'Meta-Box', plugins_url('/css/admin.css', __FILE__), false, '1.0.0' );
		
		wp_register_script( 'Google-Maps', "https://maps.googleapis.com/maps/api/js?v=3.exp", false, null);
		
		wp_register_script( 'AdminMapScript', plugins_url('/js/admin.js', __FILE__), array('jquery','Google-Maps'), '1.0.0', true);
		
		
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
	<p><strong><label for="map_shortcode">ShortCode For This Map</label></strong></p>
	<?php if(get_post_status( $post->ID ) === "publish"): ?>
	<input type="text" id="map_shortcode" value='[SPGM id="<?php echo $post->ID; ?>"]' onclick="select();" />
	<br>
	<code>Copy and Paste This ShortCode To Use This Map</code>
	<?php else: ?>
	<p><small>ShortCode will apear after first save;</small></p>
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
	$maps_latlng = isset( $values['maps-latlng'] ) ? $values['maps-latlng'][0] : '';
	$maps_pov = isset( $values['maps-pov'] ) ? $values['maps-pov'][0] : '';
	$maps_style = isset( $values['maps-style'] ) ? $values['maps-style'][0] : '';
	$maps_icon = isset( $values['maps_icon'] ) ? $values['maps_icon'][0] : plugin_dir_url( __FILE__ ).'map_marker_icon.png';
	
	
	// We'll use this nonce field later on when saving.
	wp_nonce_field( 'sp_google_maps_nonce', 'meta_box_nonce' );
?>

	<div class="group-container cf">
		<div class="box-group">
			<div class="box-label">
				<label for="maps-latlng">Google Maps Latitude Longitude </label>
			</div>
			<div class="box-field">
				<input class="regular-text" type="text" name="maps-latlng" id="maps-latlng" value="<?php echo $maps_latlng; ?>" placeholder="23.727663,90.41054964" />
				<div class="cf"></div>	
				<code>Input Latitude &amp; Longitude Saperated By Comma. Or Choose Ur Location From from the map bellow. eg. lat,lan. Or Use the Google Maps Preview, drag the marker to your location to set the Latitude,Longitude</code>
			</div>
			<div class="cf"></div>
			<div class="map-preview">
				<p>Choose Your Location By Dragging The Marker</p>
				<div id="map"></div>
			</div>
		</div>
		<div class="cf"></div>
		<div class="box-group">
			<div class="box-label">
				<label for="maps-pov">Point of View </label>
			</div>
			<div class="box-field">
				<input class="regular-text" type="text" name="maps-pov" id="maps-pov" value="<?php echo $maps_pov; ?>" placeholder="-133.26844,32.265165" />
				<div class="cf"></div>
				<code>Saperated By Comma. Set Heading &amp; Pitch, eg. heading,pitch. Or use the Street Map Preview to set Point-of-View;</code>
			</div>
			<div class="cf"></div>
			<div class="map-preview">
				<p>Choose Your Desigre Angle</p>
				<div id="pano"></div>
			</div>
		</div>
		<div class="cf"></div>
		<div class="box-group">
			<div class="box-label">
				<label for="maps-icon">Google Maps Marker Icon</label>
			</div>
			<div class="box-field">
				<input class="regular-text" type="text" name="maps-icon" id="maps-icon" value="<?php echo $maps_icon; ?>" placeholder="Marker Icon URL" />
				<img src="<?php echo $maps_icon; ?>" alt="Google Maps Custom Marker Icon" style="position: absolute;width: 34px;margin: 0 0 0 20px;padding: 0;top: -5px;">
				<div class="cf"></div>	
				<code>Icon Url. Recommended 64x64PX PNG Image</code>
			</div>
		</div>
		<div class="cf"></div>
		<div class="box-group">
			<div class="box-label">
				<label for="maps-style">Maps Style</label>
			</div>
			<div class="box-field">
				<textarea class="regular-text" name="maps-style" id="maps-style" placeholder='[{
"featureType": "water",
"stylers": [{
	"color": "#19a0d8"
}]
}, {...' style="width: 100%;min-height: 150px;"><?php echo $maps_style; ?></textarea>
				<div class="cf"></div>
				<code>Google Maps Styled API.<br>
					Get Settings For Colorful Google Maps From <a href="https://snazzymaps.com/">https://snazzymaps.com/</a><br>
					Copy and paste the <em>JavaScript Style Array</em> Here.<br>
					Or create a new style with  <a href="http://gmaps-samples-v3.googlecode.com/svn/trunk/styledmaps/wizard/index.html">Google Maps API Styled Map Wizard</a> and copy-paste the Json data here.
				</code>
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
	if( isset( $_POST['maps-latlng'] ) )
		update_post_meta( $post_id, 'maps-latlng', wp_kses( $_POST['maps-latlng'], $allowed ) );
	
	if( isset( $_POST['maps-pov'] ) )
		update_post_meta( $post_id, 'maps-pov', wp_kses( $_POST['maps-pov'], $allowed ) );
	
	if( isset( $_POST['maps-style'] ) )
		update_post_meta( $post_id, 'maps-style', wp_kses( $_POST['maps-style'], $allowed ) );
}


// Adding Map 
// Add Shortcode
function sp_google_maps_shortcode( $atts ) {
	// Attributes
	extract( shortcode_atts(
		array(
			'id' => '', // post id of the custom post type of google maps
		), $atts )
	);
	
	$output = "";
	
	if(!$id || empty($id)){
		return "Map Not Found";
	}else{
		$map = get_post($id);
		$values = get_post_custom($id);
		
		$map_title = $map->post_title;
		$map_description = $map->post_content;
		
		$maps_latlng = $values['maps-latlng'][0];
		$latlng = explode(",",$maps_latlng);
		$lat = $latlng[0];
		$lng = $latlng[1];
		$maps_pov = $values['maps-pov'][0];
		
		$pov = explode(",",$maps_pov);
		$heading = $pov[0];
		$pitch = $pov[1];
		$maps_icon = $values['maps_icon'][0];
		$maps_style = empty( $values['maps-style'][0] ) ? '[{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#46bcec"},{"visibility":"on"}]}]' : $values['maps-style'][0];
		
		//add script and style
		wp_register_style( 'SP-Google-Maps-Style', plugins_url('/css/sp_google_maps.css', __FILE__), false, '1.0.0' );
		//wp_register_style( 'Modal-CSS', plugins_url('/css/modal.css', __FILE__), false, '1.0.0' );
		//wp_register_script( 'Modal-JS', plugins_url('/js/modal.js', __FILE__), 'jQuery', '1.0.0' );
		
		wp_register_script( 'Google-Maps', "https://maps.googleapis.com/maps/api/js?v=3.exp", false, null);
		
		wp_register_script( 'SP-Google-Maps-Script', plugins_url('/js/sp_google_maps.js', __FILE__), 'Google-Maps', '1.0.0', true);
		
		// Localize the script with new data
		$mapdata = array(
				'mapid' => $id,
				'lat' => $lat,
				'lng' => $lng,
				'style' => $maps_style,
				'icon' => $maps_icon,
				'title' => $map_title,
				'description' => $map_description,
				'heading' => $heading,
				'pitch' => $pitch
		);
		wp_localize_script( 'Google-Maps', 'mapdata', $mapdata );
		
		wp_enqueue_style( 'SP-Google-Maps-Style' );
		wp_enqueue_script('Google-Maps');
		wp_enqueue_script('SP-Google-Maps-Script');
		
		/*$output .= '
				<style type="text/css">
				.sp_maps_container{
					position:relative;display:block;margin:0 auto;padding: 0;float:left;
					width: 100%;
				}
				.google-maps-steetview,
				.google-maps-basic{
					position: relative;display:block;float:left;
					margin: 10px 0; padding: 0;
					width: 50%;
					height:350px;
				}
				
				.google-maps-route-calc{
					position: relative;display:block;float:left;
					margin: 10px 0; padding: 0;
					width: 100%;
					
				}
				.sp_maps_container:after,.sp_maps_container:before,
				.cf:before,.cf:after {content:"";display:table;}
				.sp_maps_container:after,
				.cf:after {clear:both;}
				.sp_maps_container,
				.cf {zoom:1;}
				</style>
				';*/
		$output .= '<div class="sp_maps_container">';
			$output .= '<div class="google-maps-basic" id="map_canvas_'.$id.'"></div>';
			$output .= '<div class="google-maps-steetview" id="pano_'.$id.'"></div>';
			$output .= <<<SCR
<div class="cf"></div>
<div class="google-maps-route-calc">
	<a href="javascript:void(0);" onclick="calcRoute('DRIVING');return false;">Show Car Rout From Your Location</a><br>
	<a href="javascript:void(0);" onclick="calcRoute('WALKING');return false;">Show Walking Route</a><br>
	<a id="link" target="_blank" onclick="return getMyLocation();">Show Public Transport Route</a>
</div>
SCR;
		$output .= '</div>';
		return $output;
	}
}
add_shortcode( 'SPGM', 'sp_google_maps_shortcode' );