<?php
/**
 * SP Google Maps Admin Panel
 * @since SP Google Maps 1.1.5
 */
add_action('admin_menu', 'spGMSettings');
function spGMSettings() {
	$parent_slug = 'edit.php?post_type=sp_google_maps';
	$page_title = __("SP Google Maps Settings", "sp_google_maps");
	$menu_title = __("Maps Settings", "sp_google_maps");
	$capability = 'manage_options';
	$menu_slug = 'maps-settings';
	add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, 'spGMSettings_callback');
	add_action( 'admin_init', 'spGM_RegINIT' );
}
function spGM_RegINIT() {
	register_setting( 'spgmSettingsFields', 'spgmSettings' );
}
function spGMSettings_callback() {
	?>
	<div class="wrap spgm_settings">
		<h2><?php _e("Default Settings For SP Google Maps", "sp_google_maps"); ?></h2>
		<form method="post" action="options.php">
		<?php
			settings_fields( 'spgmSettingsFields' );
			do_settings_sections( 'spgmSettingsFields' );
			$settings = get_option("spgmSettings");
			$checked='checked="checked"';
		?>
		    <table class="form-table">
		    	<tr valign="top">
		        	<th scope="row">
		        		<label for="apiKey"><?php _e("API Key", "sp_google_maps"); ?></label>
		        	</th>
		        	<td>
		        		<input type="text" name="spgmSettings[apiKey]" value="<?php echo (isset($settings["apiKey"]))? $settings["apiKey"]:""; ?>" id="apiKey" class="regular-text" placeholder="<?php _e("API Key", "sp_google_maps"); ?>"/>
		        		<div class="cf"></div>
		        		<code class="description"><?php _e("API Key From Google API Console (Optional).", "sp_google_maps"); ?></code>
		        	</td>
		        </tr>
		        <tr valign="top">
		        	<th scope="row"><label for="routeCal"><?php _e("Route Calculator", "sp_google_maps"); ?></label></th>
		        	<td>
			        	<label for="routeCalMobile" style="display: block;">
							<input name="spgmSettings[routeCal]" type="radio" class="tog" value="mobile" id="routeCalMobile" <?php echo (isset($settings["routeCal"]) && $settings["routeCal"] == "mobile")? $checked:""; ?>><?php _e("Show Route Calculator in Mobile", "sp_google_maps"); ?>
						</label>
						<label for="routeCalDesktop" style="display: block;">
							<input name="spgmSettings[routeCal]" type="radio" class="tog" value="desktop" id="routeCalDesktop" <?php echo (isset($settings["routeCal"]) && $settings["routeCal"] == "desktop")? $checked:""; ?>><?php _e("Show Route Calculator in Desktop", "sp_google_maps"); ?>
						</label>
						<label for="routeCalBoth" style="display: block;">
							<input name="spgmSettings[routeCal]" type="radio" class="tog" value="both" id="routeCalBoth" <?php echo (isset($settings["routeCal"]) && $settings["routeCal"] == "both")? $checked:""; ?>><?php _e("Show Route Calculator in Both Mobile And Desktop", "sp_google_maps"); ?>
						</label>
						<label for="routeCalOff" style="display: block;">
							<input name="spgmSettings[routeCal]" type="radio" class="tog" value="none" id="routeCalOff" <?php echo (!isset($settings["routeCal"]) || (isset($settings["routeCal"]) && $settings["routeCal"] == "none"))? $checked:""; ?>><?php _e("Don't Show", "sp_google_maps"); ?>
						</label>
						<div class="cf"></div>
						<code class="description caution"><?php _e("Caution: Desktop's sometime (without GPS module) show wrong location (with Geolocation API) for the user because of IP address, proxy server etc.","sp_google_maps"); ?></code>
		        	</td>
		        </tr>
		        <tr valign="top">
		        	<th scope="row"><label for="MapsStyleJson"><?php _e("Default Style", "sp_google_maps"); ?></label></th>
		        	<td>
		        		<textarea type="text" name="spgmSettings[MapsStyleJson]" id="MapsStyleJson" rows="8" cols="46" placeholder='[{
"featureType": "water",
"stylers": [{
	"color": "#19a0d8"
}]
}, {...'><?php echo (isset($settings["MapsStyleJson"]))? $settings["MapsStyleJson"]:""; ?></textarea>
						<div class="cf"></div>
						<code class="description"><?php _e("This style will be applied to all maps unless that map have a style of it's own.", "sp_google_maps"); ?></code>
						<div class="cf"></div>
						<code class="description"><?php _e('Get Colorful Maps From <a href="https://snazzymaps.com/">https://snazzymaps.com/</a><br>Or create a new style with <a href="http://gmaps-samples-v3.googlecode.com/svn/trunk/styledmaps/wizard/index.html">Google Maps API Styled Map Wizard</a><br>Copy and paste the <strong><em>JSON Data</em></strong> into avobe textarea.', 'sp_google_maps')?></code>
					</td>
		        </tr>
			</table>   
			<?php submit_button(); ?>
		</form>
	</div>
<?php }