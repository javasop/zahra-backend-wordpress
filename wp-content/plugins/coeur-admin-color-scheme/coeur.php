<?php

/*
Plugin Name: Coeur Admin Color Scheme
License: GPLv2 or later
Plugin URI: http://wordpress.org/plugins/coeur-admin-color-scheme/
Description: Refresh your wordpress dashboard
Author: Titouanc
Version: 1.0
Author URI: http://themeforest.net/user/titouanc
*/

// Create Custom Color Scheme
function coeur_admin_color_scheme() {
	//Get the plugin directory
	$url = get_settings( 'siteurl' );
	$plugin_dir = $dir = $url . '/wp-content/plugins/coeur-admin-color-scheme/css/';
	//Coeur
	wp_admin_css_color( 'coeur', __( 'coeur' ),
		$plugin_dir . '/color-coeur.min.css',
		array( '#ddd', '#f9f9f9', '#77AFEE', '#47A9EB' )
	);
}
add_action( 'admin_init', 'coeur_admin_color_scheme' );

// Set our new color scheme as default
function update_user_option_admin_color( $color_scheme ) {
	$color_scheme = 'coeur';

	return $color_scheme;
}

add_filter( 'get_user_option_admin_color', 'update_user_option_admin_color', 5 );

// Prevent users from changing the color scheme
remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );

// Revome default value
function coeur_admin_remove_default_value() {
	global $_wp_admin_css_colors;
	$color_name = $_wp_admin_css_colors['fresh']->name;

	if ( $color_name == 'Default' ) {
		$_wp_admin_css_colors['fresh']->name = 'Fresh';
	}
	return $_wp_admin_css_colors;
}
add_filter( 'admin_init', 'coeur_admin_remove_default_value' );

// Custom Login Page Styles.
function coeur_admin_login_css() {

	$url = get_settings( 'siteurl' );
	$plugin_dir = $dir = $url . '/wp-content/plugins/coeur-admin-color-scheme/css/'; ?>

	<link rel="stylesheet" id="custom_wp_admin_css"  href="<?php echo $plugin_dir . 'style-login.min.css' ?>" type="text/css" media="all" />
	<?php }

add_action( 'login_enqueue_scripts', 'coeur_admin_login_css' );

function coeur_admun_login_head() {
	remove_action( 'login_head', 'wp_shake_js', 12 );
}
add_action( 'login_head', 'coeur_admin_login_head' );
