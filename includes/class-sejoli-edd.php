<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://sejoli.co.id
 * @since      1.0.0
 *
 * @package    Sejoli_EDD
 * @subpackage Sejoli_EDD/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Sejoli_EDD
 * @subpackage Sejoli_EDD/includes
 * @author     Sejoli <orangerdigiart@gmail.com>
 */
class Sejoli_EDD {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Sejoli_EDD_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'SEJOLI_EDD_VERSION' ) ) {
			$this->version = SEJOLI_EDD_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'sejoli-edd';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_json_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Sejoli_EDD_Loader. Orchestrates the hooks of the plugin.
	 * - Sejoli_EDD_i18n. Defines internationalization functionality.
	 * - Sejoli_EDD_Admin. Defines all hooks for the admin area.
	 * - Sejoli_EDD_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sejoli-edd-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sejoli-edd-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sejoli-edd-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sejoli-edd-order.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sejoli-edd-product.php';

		/**
		 * The class responsible for defining all actions that require JSON data return.
		 */
		 require_once plugin_dir_path( dirname( __FILE__ ) ) . 'json/main.php';
		 require_once plugin_dir_path( dirname( __FILE__ ) ) . 'json/edd.php';

		/**
		 * The class responsible for defining all actions that occur in the member area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-sejoli-edd-member.php';

		$this->loader = new Sejoli_EDD_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Sejoli_EDD_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Sejoli_EDD_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$admin = new Sejoli_EDD\Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_filter( 'sejoli/general/fields',		$admin, 'set_plugin_options', 666);

		$order = new Sejoli_EDD\Admin\Order( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'sejoli/order/meta-data',				$order, 'set_order_metadata',	1222, 2);
		$this->loader->add_action( 'sejoli/order/set-status/completed',		$order, 'create_edd_order',		1222);
		$this->loader->add_filter( 'sejoli/order/set-status/on-hold',		$order, 'cancel_edd_order',  	1222);
		$this->loader->add_filter( 'sejoli/order/set-status/cancelled',		$order, 'cancel_edd_order',		1222);
		$this->loader->add_filter( 'sejoli/order/set-status/refunded',		$order, 'cancel_edd_order',		1222);
		$this->loader->add_filter( 'sejoli/order/set-status/in-progress',	$order, 'cancel_edd_order',		1222);
		$this->loader->add_filter( 'sejoli/order/set-status/shipped',		$order, 'cancel_edd_order',		1222);
		$this->loader->add_action( 'init',									$order, 'disable_email', 		999);

		$product = new Sejoli_EDD\Admin\Product( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'sejoli/product/fields',		$product, 'set_product_fields', 	12);
		$this->loader->add_filter( 'sejoli/product/meta-data',	$product, 'set_product_metadata', 	1222, 2);

	}

	/**
	 * Register all of the hooks related to the front area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$member = new Sejoli_EDD\Front\Member( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts',	$member, 'enqueue_scripts',		 1999);
		$this->loader->add_filter( 'edd_template_paths', 	$member, 'set_edd_template_dir', 11, 2);
		$this->loader->add_action( 'template_redirect',		$member, 'block_edd_pages',		 1);

	}

	/**
	 * Register all-related with JSON returning data
	 *
	 * @since 	1.0.0
	 * @access   private
	 */
	private function define_json_hooks() {

		$edd = new Sejoli_EDD\JSON\EDD();

		$this->loader->add_action( 'sejoli_ajax_get-purchase-detail',			$edd, 'get_purchase_detail',	   1);
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Sejoli_EDD_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
