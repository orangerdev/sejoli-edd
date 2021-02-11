<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://sejoli.co.id
 * @since             1.0.0
 * @package           Sejoli_EDD
 *
 * @wordpress-plugin
 * Plugin Name:       Sejoli - EDD
 * Plugin URI:        https://sejoli.co.id
 * Description:       Integrates Sejoli premium membership WordPress plugin with Easy Digital Downloads ( EDD )
 * Version:           1.0.1
 * Author:            Sejoli
 * Author URI:        https://sejoli.co.id
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sejoli-edd
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

add_action('muplugins_loaded', 'sejoli_edd_check_sejoli');

function sejoli_edd_check_sejoli() {

	if(!defined('SEJOLISA_VERSION')) :

		add_action('admin_notices', 'sejolp_no_sejoli_functions');

		function sejolp_no_sejoli_functions() {
			?><div class='notice notice-error'>
			<p><?php _e('Anda belum menginstall atau mengaktifkan SEJOLI terlebih dahulu.', 'sejolp'); ?></p>
			</div><?php
		}

		return;
	endif;

}

if( !class_exists( 'Easy_Digital_Downloads' )) :

	add_action('admin_notices', 'sejoli_edd_no_edd_functions');

	function sejoli_edd_no_edd_functions() {
		?><div class='notice notice-error'>
		<p><?php _e('Anda belum menginstall atau mengaktifkan Easy Digital Download terlebih dahulu.', 'sejolp'); ?></p>
		</div><?php
	}

else :

	/**
	 * Currently plugin version.
	 * Start at version 1.0.0 and use SemVer - https://semver.org
	 * Rename this for your plugin and update it as you release new versions.
	 */
	define( 'SEJOLI_EDD_VERSION', 	'1.0.1' );
	define( 'SEJOLI_EDD_PATH',		plugin_dir_path( __FILE__ ) );
	define( 'SEJOLI_EDD_URL',		plugin_dir_url( __FILE__ ) );
	define( 'EDD_CPT',				'download');

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-sejoli-edd-activator.php
	 */
	function activate_sejoli_edd() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-sejoli-edd-activator.php';
		Sejoli_EDD_Activator::activate();
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-sejoli-edd-deactivator.php
	 */
	function deactivate_sejoli_edd() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-sejoli-edd-deactivator.php';
		Sejoli_EDD_Deactivator::deactivate();
	}

	register_activation_hook( __FILE__, 'activate_sejoli_edd' );
	register_deactivation_hook( __FILE__, 'deactivate_sejoli_edd' );

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-sejoli-edd.php';

	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	function run_sejoli_edd() {

		$plugin = new Sejoli_EDD();
		$plugin->run();

	}

	run_sejoli_edd();

endif;
