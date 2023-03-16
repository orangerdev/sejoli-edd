<?php

namespace Sejoli_EDD\Admin;

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
class Order {

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
     * Buyer ID
     * @since   1.0.0
     * @var
     */
    protected $buyer_id = NULL;

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
     * Set edd metadata to order
     * Hooked via filter sejoli/order/meta-data, priority 1222
     * @since   1.0.0
     * @param   array   $metadata   [description]
     * @param   array   $order_data [description]
     * @return  array
     */
    public function set_order_metadata(array $metadata, array $order_data) {

        $product = sejolisa_get_product($order_data['product_id']);

        if(property_exists($product, 'edd_products') && is_array($product->edd_products)) :
            $metadata['edd_products'] = $product->edd_products;
        endif;

        return $metadata;
    }

	/**
     * Create edd order when sejoli order completed
     * Hooked via sejoli/order/set-status/completed, prioirty 1222
     * @since   1.0.0
     * @param   array  $order_data
     * @return  void
     */
    public function create_edd_order(array $order_data) {

        if(
            isset($order_data['meta_data']['edd_products']) &&
            !isset($order_data['meta_data']['edd_order'])
        ) :

            $this->buyer_id = $order_data['user_id'];
            $edd_products   = $order_data['meta_data']['edd_products'];

            $user         = get_user_by('id', $this->buyer_id);
            $quantity     = 1;
            $cart_details = $downloads = array();
            $price_total  = 0;
            $product      = sejolisa_get_product($order_data['product_id']);

            foreach( $edd_products as $download_id ) :

                $price_options = array();
        	    $price         = edd_get_download_price( $download_id );
                $price_total  += $price;

                $downloads[]   = array(
                    'id'      => $product,
                    'options' => $price_options
                );

                // Set up Cart Details array
            	$cart_details[] = array(
            			'name'        => get_the_title( $download_id ),
            			'id'          => $download_id,
            			'item_number' => array(
            				'id'      => $download_id,
            				'options' => $price_options
            			),
            			'tax'         => 0,
            			'discount'    => 0,
            			'item_price'  => $price,
            			'subtotal'    => ( $price * $quantity ),
            			'price'       => ( $price * $quantity ),
            			'quantity'    => $quantity,
            	);

            endforeach;

            // Setup user information
        	$user_info = array(
        		'id'         => $user->ID,
        		'email'      => $user->user_email,
        		'first_name' => $user->user_firstname,
        		'last_name'  => $user->user_lastname,
        		'discount'   => 'none',
        		'address'    => array()
        	);

        	// Setup purchase information
        	$purchase_data = array(
        		'downloads'    => $downloads,
        		'fees'         => edd_get_cart_fees(),
        		'subtotal'     => $price * $quantity,
        		'discount'     => 0,
        		'tax'          => 0,
        		'price'        => $price * $quantity,
        		'purchase_key' => strtolower( md5( uniqid() ) ),
        		'user_email'   => $user_info['email'],
        		'date'         => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
        		'user_info'    => $user_info,
        		'post_data'    => array(),
        		'cart_details' => $cart_details,
        		'gateway'      => 'manual',
        		'buy_now'      => true,
        		'card_info'    => array()
        	);

            edd_set_purchase_session( $purchase_data );

            $payment_data = array(
        		'price'        => $purchase_data['price'],
        		'date'         => $purchase_data['date'],
        		'user_email'   => $purchase_data['user_email'],
        		'purchase_key' => $purchase_data['purchase_key'],
        		'currency'     => edd_get_currency(),
        		'downloads'    => $purchase_data['downloads'],
        		'user_info'    => $purchase_data['user_info'],
        		'cart_details' => $purchase_data['cart_details'],
        		'status'       => 'pending',
        	);

        	// Record the pending payment
        	$payment_id = edd_insert_payment( $payment_data );

            edd_update_payment_status( $payment_id, 'publish' );

            $order_data['meta_data']['edd_order'] = $payment_id;

            sejolisa_update_order_meta_data($order_data['ID'], $order_data['meta_data']);

			update_post_meta( $payment_id, '_sejoli_order_id', $order_data['ID'] );

		elseif(isset($order_data['meta_data']['edd_order'])) :

			$edd_order_id = intval($order_data['meta_data']['edd_order']);

            edd_update_payment_status( $edd_order_id, 'publish' );

        endif;
    }

    /**
     * Cancel edd order
     * @since   1.0.0
     * @param   array  $order_data [description]
     * @return  void
     */
    public function cancel_edd_order(array $order_data) {

        if(isset($order_data['meta_data']['edd_order'])) :

            $edd_order_id = intval( $order_data['meta_data']['edd_order'] );

            edd_update_payment_status( $edd_order_id, 'pending' );

        endif;

    }

	/**
	 * Check if need to disable email after complete purchase
	 * Hooked via action init, priority 998
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function disable_email() {

		if( false !== boolval( carbon_get_theme_option( 'edd_disable_email_notification' ) ) ) :

			remove_action( 'edd_complete_purchase', 'edd_trigger_purchase_receipt', 999 );
			remove_action( 'edd_admin_sale_notice', 'edd_admin_email_notice', 10 );

		endif;

	}
}
