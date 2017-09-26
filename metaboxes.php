<?php
/**
 * SP Google Maps Metaboxes for custom post type
 * @package     SP Google Maps
 * @author      Kudratullah
 * @version     1.0.1
 * @since       SP Google Maps 1.1.5
 * @copyright   2017 SamePage Inc.
 * @license     GPL-2.0+
 */
/**
 * 
 * @since SP Google Maps 1.1.5
 */
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}
// meta box for maps
add_action( 'add_meta_boxes', 'sp_google_maps_shortcode_meta_box_add' );

function sp_google_maps_shortcode_meta_box_add(){
	add_meta_box( 'sp-maps-shortcode', __("Google Maps ShortCode", "sp_google_maps"), 'sp_google_maps_shortcode_meta', 'sp_google_maps', 'side', 'default' );
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
	add_meta_box( 'sp-maps-data', __("Google Maps Settings"), 'sp_google_maps_settings', 'sp_google_maps', 'normal', 'high' );
}

function sp_google_maps_settings(){
	global $post;
	$values = get_post_custom( $post->ID );
	$maps_title = isset( $values['maps-title'] )? $values['maps-title'][0] : "";
	$maps_mwscroll = isset( $values['maps-mwscroll'] )? $values['maps-mwscroll'][0] : '';
	$maps_latlng = isset( $values['maps-latlng'] )? $values['maps-latlng'][0] : '';
	$maps_zoom = isset( $values['maps-zoom'] )? $values['maps-zoom'][0] : 15;
	$maps_pov = isset( $values['maps-pov'] ) ? $values['maps-pov'][0] : '';
	
	$maps_SV_latlng = isset( $values['maps-SV-latlng'] ) ? $values['maps-SV-latlng'][0] : '';
	$maps_SV_zoom = isset( $values['maps-SV-zoom'] ) ? $values['maps-SV-zoom'][0] : 1;
	
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
					echo ($maps_mwscroll == 0|| $maps_mwscroll == "")? 'checked' : '';
				?>/>
				<?php _e('Disable', 'sp_google_maps'); ?></label>
				<div class="cf"></div>
				<label for="maps-mwscroll-enabled">
				<input class="radio" type="radio" name="maps-mwscroll" id="maps-mwscroll-enabled" value="1"  <?php
					echo ($maps_mwscroll == 1)? 'checked' : '';
				?>/>
				<?php _e('Enable', 'sp_google_maps'); ?></label>
				<div class="cf"></div>
				<code><?php _e('Enable or Disable Mouse Wheel Scroll On Google Maps And Google Maps Street View.<br>Disabled By Default', 'sp_google_maps'); ?></code>
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
				<code><?php _e('Input Latitude &amp; Longitude Separated By Comma. Or Choose Your Location From from the map below. eg. lat,lon.', 'sp_google_maps'); ?></code>
			</div>
			<div class="cf"></div>
			<div class="box-label">
				<label for="maps-zoom"><?php _e('Google Maps Zoom Level:', 'sp_google_maps'); ?></label>
			</div>
			<div class="box-field">
				<input class="regular-text" type="number" name="maps-zoom" id="maps-zoom" value="<?php echo $maps_zoom; ?>" step="0.01" min="0" max="21"/>
				<div class="cf"></div>	
				<code><?php echo sprintf(__('Input into the text box above or change from the map preview.<br><em>Allowed minimum value %d &amp; maximum value %d by google maps.</em>', 'sp_google_maps'), 0,21)?></code>
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
				<code><?php _e('Separated By Comma. Set Heading &amp; Pitch, eg. heading,pitch. Or use the Street Map Preview to set Point-of-View.', 'sp_google_maps'); ?></code>
			</div>
			<div class="cf"></div>
			<div class="box-label">
				<label for="maps-pov"><?php _e('Street View Latitude Longitude:', 'sp_google_maps'); ?></label>
			</div>
			<div class="box-field">
				<input class="regular-text" type="text" name="maps-SV-latlng" id="maps-SV-latlng" value="<?php echo $maps_SV_latlng; ?>" placeholder="-133.26844,32.265165" />
				<div class="cf"></div>
				<code><?php _e('Input Latitude &amp; Longitude Separated By Comma. Or Change The View, Latitude and Longitude will be automatically calculated. eg. lat,lon.', 'sp_google_maps'); ?></code>
			</div>
			<div class="cf"></div>
			<div class="box-label">
				<label for="maps-pov"><?php _e('Street View Zoom Level:', 'sp_google_maps'); ?></label>
			</div>
			<div class="box-field">
				<input class="regular-text" type="number" name="maps-SV-zoom" id="maps-SV-zoom" value="<?php echo $maps_SV_zoom; ?>" step="0.01" min="0" max="5"/>
				<div class="cf"></div>
				<code><?php echo sprintf(__('Input into the text box above  or change from the map preview.<br><em>Allowed minimum value %d &amp; maximum value %d by google street view.</em>', 'sp_google_maps'), 0,5)?></code>
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
				<code><?php _e('Custom Maps Style for this map.', 'sp_google_maps'); ?></code>
				<code><?php _e('Get Colorful Maps From <a href="https://snazzymaps.com/">https://snazzymaps.com/</a><br>Or create a new style with <a href="http://gmaps-samples-v3.googlecode.com/svn/trunk/styledmaps/wizard/index.html">Google Maps API Styled Map Wizard</a><br>Copy and paste the <strong><em>JSON Data</em></strong> into above  textarea.', 'sp_google_maps')?></code>
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
				<code><?php _e('Custom CSS for this map.', 'sp_google_maps'); ?></code>
				<code><?php _e('Available CSS Class Selectors are:', 'sp_google_maps'); ?> <strong>.sp_maps_container, .google-maps-steetview, .google-maps-basic, .google-maps-route-calc</strong></code>
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
	if( isset($_POST['maps-zoom']) )
		update_post_meta( $post_id, 'maps-zoom', wp_kses( $_POST['maps-zoom'], $allowed ) );
	if( isset($_POST['maps-SV-latlng']) )
		update_post_meta( $post_id, 'maps-SV-latlng', wp_kses( $_POST['maps-SV-latlng'], $allowed ) );
	if( isset($_POST['maps-SV-zoom']) )
		update_post_meta( $post_id, 'maps-SV-zoom', wp_kses( $_POST['maps-SV-zoom'], $allowed ) );
}
// End of file metaboxes.php