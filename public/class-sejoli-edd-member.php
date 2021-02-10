<?php

namespace Sejoli_EDD\Front;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://ridwan-arifandi.com
 * @since      1.0.0
 *
 * @package    SejoliLP
 * @subpackage SejoliLP/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    SejoliLP
 * @subpackage SejoliLP/admin
 * @author     Ridwan Arifandi <orangerdigiart@gmail.com>
 */
class Member {

    /**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Enqueue JS and CSS files
	 * Hooked via action wp_enqueue_scripts, priority 999
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function enqueue_scripts() {

		if(is_page( edd_get_option( 'purchase_history_page' ) ) ) :

			wp_enqueue_style ( 'sejoli-edd', SEJOLI_EDD_URL . '/public/css/sejoli-edd.css');

			wp_enqueue_script( 'sejoli-edd', SEJOLI_EDD_URL . '/public/js/sejoli-edd.js', array('jquery', 'blockUI'), $this->version, true);

			wp_localize_script( 'sejoli-edd', 'sejoli_edd', array(
				'detail_url'	=> site_url('/sejoli-ajax/get-purchase-detail')
			));

		endif;

	}

	/**
	 * Overwrite EDD template directory
	 * Hooked via filter edd_template_paths, priority 11
	 * @since 	1.0.0
	 * @param 	array 	$file_paths
	 * @return 	array
	 */
	public function set_edd_template_dir( $file_paths ) {

		$file_paths[11]	= trailingslashit( SEJOLI_EDD_PATH . 'templates/' );

		return $file_paths;

	}

	/**
	 * Block all access to default EDD pages
	 * Hooked via action template_redirect, priority 1
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function block_edd_pages() {

		$block = boolval( carbon_get_theme_option('edd_disable_product_checkout') );
		$edd_pages = array();

		$edd_pages[] = edd_get_option( 'success_page' );
		$edd_pages[] = edd_get_option( 'failure_page' );

		if(
			false !== $block &&
			(
				is_singular(EDD_CPT) ||
				edd_is_checkout() ||
				is_page( $edd_pages )
			)
		) :
			wp_redirect(home_url());
			exit;

		endif;
	}

}
