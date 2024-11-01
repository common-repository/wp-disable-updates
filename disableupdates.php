<?php
/*
Plugin Name: Disable plugins / themes updates
Plugin URI: https://www.vinvin.dev/worpdress/plugins/disable-plugins-themes-wordpress/
Description: Disable update for plugins or themes
Version: 1.1.3
Author: vinvin27
Author URI: https://www.vinvin.dev
License: GPLv2 or later
WC requires at least: 3.0
WC tested up to: 6.1
Text Domain: wpdisableupdates
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'You are allow to access directly on this page ';
	exit;
}

/* Change Value  */

if( get_option('vgwpdup_disable-theme') ){
	remove_action( 'load-update-core.php', 'wp_update_themes' );
	add_filter( 'pre_site_transient_update_themes', '__return_null' );
}
if(get_option('vgwpdup_disable-plugin')){
	remove_action( 'load-update-core.php', 'wp_update_plugins' );
	add_filter( 'pre_site_transient_update_plugins', '__return_null' );
}
if(get_option('vgwpdup_disable-wordpress')){
	/* Désactivé la mise à jour de wordpress auto. */
	add_filter('pre_site_transient_update_core','remove_core_updates');
	add_filter( 'auto_update_core', '__return_false');
}
if(get_option('vgwpdup_disable-translation')){
	/* Désactivé la mise à jour de wordpress auto. */
	add_filter( 'pre_site_transient_update_translation', '__return_false' );
}

function remove_core_updates() {
  global $wp_version;
  return (object) array(
    'last_checked' => time(),
    'version_checked' => $wp_version,
  );
}

add_action( 'admin_menu', 'vgwpdup_my_plugin_menu' );
function vgwpdup_my_plugin_menu() {
	add_options_page( __('Disable Updates','wpdisableupdates'), __('Disable Updates','wpdisableupdates'), 'manage_options', 'id-disable-plugins', 'vgwpdup_my_plugin_options' );
}

function vgwpdup_my_plugin_options() {

	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}



if(isset($_POST['vgwpdup_submit-disable'])){


	// Check Nonce
	check_admin_referer( 'vgwpdup_options_change-updates' );

	if(isset($_POST['vgwpdup_disable-plugin']) && $_POST['vgwpdup_disable-plugin']=='on'){
		update_option('vgwpdup_disable-plugin',1);
	}
	else{
		update_option('vgwpdup_disable-plugin',0);
	}

	if(isset($_POST['vgwpdup_disable-theme']) && $_POST['vgwpdup_disable-theme']=='on'){
		update_option('vgwpdup_disable-theme',1);
	}
	else{
		update_option('vgwpdup_disable-theme',0);
	}

	if(isset($_POST['vgwpdup_disable-translation']) && $_POST['vgwpdup_disable-translation']=='on'){
		update_option('vgwpdup_disable-translation',1);
	}
	else{
		update_option('vgwpdup_disable-translation',0);
	}

	if(isset($_POST['vgwpdup_disable-wordpress']) && $_POST['vgwpdup_disable-wordpress']=='on'){
		update_option('vgwpdup_disable-wordpress',1);
	}
	else{
		update_option('vgwpdup_disable-wordpress',0);
	}

}

	?>



		<div class="wrap">
			<h2><?php _e('Plugin options : ','wpdisableupdates') ?></h2>
			<form method="POST">
				<table>
				<tbody>
					<tr>
						<td>
							<label for=""> <?php _e('Disable plugins updates : ','wpdisableupdates') ?> </label>
						</td>
						<td>
							<input <?php echo get_option('vgwpdup_disable-plugin')?'CHECKED="CHECKED"':'' ?> type="checkbox" name="vgwpdup_disable-plugin" />
						</td>
					</tr>

					<tr>
						<td>
						<label for=""> <?php _e('Disable templates updates : ','wpdisableupdates') ?> </label>
						</td>
						<td>
						<input <?php echo get_option('vgwpdup_disable-theme')?'CHECKED="CHECKED"':'' ?> type="checkbox" name="vgwpdup_disable-theme" />
					</td>
					</tr>
					<tr>
						<td>
						<label for=""> <?php _e('Disable Wordpress Translation update : ','wpdisableupdates') ?> </label>
						</td>
						<td>
						<input <?php echo get_option('vgwpdup_disable-translation')?'CHECKED="CHECKED"':'' ?> type="checkbox" name="vgwpdup_disable-translation" />
					</td>
					</tr>	<tr>
							<td>
							<label for=""> <?php _e('Disable Wordpress core updates : ','wpdisableupdates') ?> </label>
							</td>
							<td>
							<input <?php echo get_option('vgwpdup_disable-wordpress')?'CHECKED="CHECKED"':'' ?> type="checkbox" name="vgwpdup_disable-wordpress" />
						</td>
						</tr>
					<tr>

				</table>
				<?php wp_nonce_field( 'vgwpdup_options_change-updates' ); ?>
				<p class="submit">
					<input type="submit" name="vgwpdup_submit-disable" class="button-primary" value="<?php _e('Save','wpdisableupdates') ?>" />
				</p>
			</form>
		</div>
	<?php

}

add_action('plugins_loaded', 'vgwpdup_wan_load_textdomain');
function vgwpdup_wan_load_textdomain() {
	load_plugin_textdomain( 'wpdisableupdates', false, dirname( plugin_basename(__FILE__) ).'/lang/' );
}
