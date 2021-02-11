<?php
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
