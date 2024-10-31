<?php
/**
* Plugin Name: NUAPAY WordPress Plugin
* Plugin URI: http://nuapay.com
* Description: This plugin adds nuapay direct debits to wordpress.
* Version: 1.0.8
* Author: nuapay-wordpress
* Author URI: http://sentenial.com
* License: GPL2
*/

define('NUAPAY_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('NUAPAY_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
define('NUAPAY_TEXT_DOMAIN', 'nuapay');

define('NUAPAY_PLUGIN_VER', '1.0.8');

require_once(NUAPAY_PLUGIN_DIR . 'logging/logging-utils.php');
require_once(NUAPAY_PLUGIN_DIR . 'utils/utils.php');
require_once(NUAPAY_PLUGIN_DIR . 'utils/NPUtils.php');
require_once(NUAPAY_PLUGIN_DIR . 'utils/NPSettings.php');

require_once(NUAPAY_PLUGIN_DIR . 'shortcode/class-shortcode.php');
require_once(NUAPAY_PLUGIN_DIR . 'shortcode/nuapay-emandate-shortcode.php');
require_once(NUAPAY_PLUGIN_DIR . 'shortcode/nuapay-aisp-shortcode.php');
require_once(NUAPAY_PLUGIN_DIR . 'shortcode/nuapay-pisp-shortcode.php');

add_action('plugins_loaded', 'np_update_check');
add_action('admin_init', 'np_options_init');
add_action('admin_menu', 'np_options_add_page');

function np_update_check() {
	$npPluginCurrentVersion = NPSettings::getPluginVersion();
	write_log('npPluginCurrentVersion is ' . $npPluginCurrentVersion);
	write_log('NUAPAY_PLUGIN_VER is ' . NUAPAY_PLUGIN_VER);
	if ($npPluginCurrentVersion !== NUAPAY_PLUGIN_VER) {
		write_log('************ np_plugin_update will be performed ************');
		require_once(NUAPAY_PLUGIN_DIR . 'update/plugin-updates.php');
		np_plugin_update($npPluginCurrentVersion, NUAPAY_PLUGIN_VER);
	} else {
		write_log('np_plugin_update will not be performed');
	}
}

// Init plugin options to white list our options
function np_options_init() {
	/*
	 * First, we register the settings. We will store all the settings in one options field, as an array.
	 * This is usually the recommended way. 
	 * 
	 * 1) The first argument is a group
	 * 2) The second argument is the name of the options.
	 * 3) The final arguement is a function name that will validate your options.
	 */
	register_setting( 'np_option_group', 'np_form_options', 'np_options_validate' );
}

// Add menu page
function np_options_add_page() {
	/*
	 a. It adds a link under the settings menu called NUAPAY Options
	 b. When you click it, you go to a page with a title of NUAPAY Options
	 c. You must have the manage_options capability to get there though (admins only).
	 d. The link this will be will in fact be /wp-admin/options-general.php?page=np_menu_slug
	 e. And the content of the page itself will be generated by the np_options_do_page function.
	 */
	add_options_page('NUAPAY\'s Options', 'NUAPAY Options', 'manage_options', 'np_menu_slug', 'np_options_do_page');
}

// Draw the menu page itself
function np_options_do_page() {
	NPUtils::render('admin');
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function np_options_validate($input) {
	// Say our option must be safe text with no HTML tags
	$input[NPSettings::REST_URL] =  wp_filter_nohtml_kses($input[NPSettings::REST_URL]);
	//TODO sanitise all the options.
	return $input;
}

