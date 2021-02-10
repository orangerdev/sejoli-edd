<?php
namespace Sejoli_EDD\JSON;

Class EDD extends \Sejoli_EDD\JSON
{
    /**
     * Construction
     */
    public function __construct() {

    }

    /**
     * Get single purchase details
     * @since   1.0.0
     * @return  string
     */
    public function get_purchase_detail() {

        global $edd_receipt_args;

        $get_data = wp_parse_args($_GET, array(
                        'key'   => NULL
                    ));

        ob_start();

        if( edd_can_view_receipt( $get_data['key'] ) ):

            $edd_receipt_args['products'] = true;

            require( SEJOLI_EDD_PATH . '/templates/shortcode-receipt.php' );

        else :

            ?><p><?php _e('Sorry, you don\'t have permission to access this data', 'sejoli'); ?></p><?php

        endif;

        $content = ob_get_contents();
        ob_end_clean();

        wp_send_json(array(
            'content'   => $content
        ));

        exit;
    }
}
