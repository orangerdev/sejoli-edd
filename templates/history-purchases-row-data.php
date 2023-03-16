<?php
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    
$plugin_dir  = WP_PLUGIN_DIR . '/easy-digital-downloads/easy-digital-downloads.php';
$plugin_data = get_plugin_data( $plugin_dir );

if($plugin_data['Version'] <= '2.11.7') :

foreach ( $payments as $payment ) :

    $payment         = new EDD_Payment( $payment->ID );
    $sejoli_order_id = absint( get_post_meta( $payment->ID, '_sejoli_order_id', true) );
    $order_id        = ( 0 < $sejoli_order_id ) ? '#'.$sejoli_order_id : '#EDD-'. $payment->number;
    $cart            = edd_get_payment_meta_cart_details( $payment->ID, true );
    $items           = array();

    foreach($cart as $cart_item) :
        $items[] = $cart_item['name'];
    endforeach;

?>
    <tr class="edd_purchase_row">
        <?php do_action( 'edd_purchase_history_row_start', $payment->ID, $payment->payment_meta ); ?>

        <td class="edd_purchase_id">
            <?php echo $order_id; ?>
        </td>

        <td class='edd_purchase_item'>
            <?php echo implode(', ', $items); ?>
        </td>

        <td class="edd_purchase_details">
        <?php
            if( 'publish' !== $payment->status ) :
                _e('Order belum selesai', 'sejoli');
            else:
                ?>
                <a href="#" class='edd-view-purchase' data-payment-key="<?php echo $payment->key; ?>">
                    <?php _e( 'View Details and Downloads', 'sejoli-edd' ); ?>
                </a>
                <?php
            endif;
        ?>
        </td>
        <?php do_action( 'edd_purchase_history_row_end', $payment->ID, $payment->payment_meta ); ?>
    </tr>
<?php endforeach; ?>

<?php else: ?>

    <?php foreach ( $orders as $order ) :

        $payment         = new EDD_Payment( $order->id );
        $sejoli_order_id = absint( get_post_meta( $payment->ID, '_sejoli_order_id', true) );
        $order_id        = ( 0 < $sejoli_order_id ) ? '#'.$sejoli_order_id : '#EDD-'. $payment->number;
        $cart            = edd_get_payment_meta_cart_details( $payment->ID, true );
        $items           = array();

        foreach($cart as $cart_item) :
            $items[] = $cart_item['name'];
        endforeach;

    ?>
            <tr class="edd_purchase_row">
                <?php do_action( 'edd_order_history_row_start', $order ); ?>
                <td class="edd_purchase_id">#<?php echo esc_html( $order->get_number() ); ?></td>
                <td class='edd_purchase_item'>
                    <?php echo implode(', ', $items); ?>
                </td>
                <td class="edd_purchase_details">
                    <?php
                    if ( ! in_array( $order->status, array( 'complete', 'partially_refunded' ), true ) ) : ?>
                        <span class="edd_purchase_status <?php echo esc_html( $order->status ); ?>"><?php echo esc_html( edd_get_status_label( $order->status ) ); ?></span>
                        <?php
                        $recovery_url = $order->get_recovery_url();
                        if ( $recovery_url ) :
                            ?>
                            &mdash; <a href="<?php echo esc_url( $recovery_url ); ?>"><?php esc_html_e( 'Complete Purchase', 'sejoli-edd' ); ?></a>
                            <?php
                        endif;
                        ?>
                    <?php else: ?>
                        <a href="#" class='edd-view-purchase' data-payment-key="<?php echo $payment->key; ?>">
                            <?php _e( 'View Details and Downloads', 'sejoli-edd' ); ?>
                        </a>
                    <?php endif; ?>
                </td>
                <?php do_action( 'edd_order_history_row_end', $order ); ?>
            </tr>
        <?php endforeach; ?>

<?php endif; ?>