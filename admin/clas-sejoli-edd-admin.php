<?php

namespace Sejoli_EDD;

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
class Admin {

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
     * Set plugin options
     * Hooked via filter sejoli/general/fields, priority 999
     * @since   1.0.0
     * @param   array   $field
     * @return  array
     */
    public function set_plugin_options( array $fields ) {

        $fields['edd']  = array(

            'title'     => 'Easy Digital Downloads',
            'fields'    => array(

                Field::make('separator', 'sep_edd_setting',	__('Pengaturan integrasi dengan Easy Digital Downloads', 'sejoli')),

                Field::make('checkbox', 'edd_disable_product_checkout', __('Nonaktifkan penjualan produk EDD', 'sejoli') )
                    ->set_default_value(true)
                    ->set_help_text( __('Dengan menonaktifkan penjualan produk EDD, maka semua penjualan akan menggunakan sistem dari sejoli', 'sejoli')),

                Field::make('checkbox', 'edd_disable_email_notification', __('Nonaktifkan semua notifikasi yang berasalah dari EDD', 'sejoli'))
                    ->set_default_value(true)
                    ->set_help_text( __('Dengan menonaktifkan penjualan produk EDD, maka semua penjualan akan menggunakan sistem dari sejoli', 'sejoli')),
            )
        )

        return $field;

    }
}
