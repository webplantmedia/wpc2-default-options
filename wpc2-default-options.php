<?php
/*
Plugin Name: WP Canvas - Default Options
Plugin URI: http://webplantmedia.com/starter-themes/wordpresscanvas/features/plugins/wpc2-default-options/
Description: Generate default options PHP file for WP Canvas 2
Author: Chris Baldelomar
Author URI: http://webplantmedia.com/
Version: 1.0
License: GPLv2 or later
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

if ( is_admin() ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-admin.php' );

	add_action( 'plugins_loaded', array( 'WPC2_Default_Options_Admin', 'get_instance' ) );
}
