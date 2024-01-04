<?php
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    
$plugin_dir  = WP_PLUGIN_DIR . '/easy-digital-downloads/easy-digital-downloads.php';
$plugin_data = get_plugin_data( $plugin_dir );

if($plugin_data['Version'] <= '2.11.7') :
?>
<?php if( ! empty( $_GET['edd-verify-success'] ) ) : ?>
<p class="edd-account-verified edd_success">
	<?php _e( 'Your account has been successfully verified!', 'sejoli-edd' ); ?>
</p>
<?php
endif;
/**
 * This template is used to display the purchase history of the current user.
 */
if ( is_user_logged_in() ):

?><div id='sejoli-edd-table-holder'><?php

	$payments = edd_get_users_purchases( get_current_user_id(), 20, true, 'any' );

	if ( $payments ) :

		do_action( 'edd_before_purchase_history', $payments ); ?>

		<table style="width:100%" class='ui single line table'>
			<thead>
				<tr class="edd_purchase_row">
					<?php do_action('edd_purchase_history_header_before'); ?>
					<th class="edd_purchase_id" style="width:100px;"><?php _e('ID','sejoli-edd' ); ?></th>
					<th class="edd_purchase_item"><?php _e('Item', 'sejoli-edd'); ?></th>
					<th class="edd_purchase_details"><?php _e('Details','sejoli-edd' ); ?></th>
					<?php do_action('edd_purchase_history_header_after'); ?>
				</tr>
			</thead>

		<?php require plugin_dir_path( __FILE__ ) . '/history-purchases-row-data.php'; ?>

		</table>
		<?php
			echo edd_pagination(
				array(
					'type'  => 'purchase_history',
					'total' => ceil( edd_count_purchases_of_customer() / 20 ) // 20 items per page
				)
			);
		?>
		<?php do_action( 'edd_after_purchase_history', $payments ); ?>
		<?php wp_reset_postdata(); ?>
	<?php else : ?>
		<p class="edd-no-purchases"><?php _e('Belum ada data','sejoli-edd' ); ?></p>
	<?php endif;

?></div><!-- END #sejoli-edd-table-holder --><?php

endif;

?>
<div id='edd-purchase-detail' class="ui modal">
	<i class='close icon'></i>
	<div class="content">

	</div>
</div>

<?php else: ?>

<?php
if ( ! empty( $_GET['edd-verify-success'] ) ) : ?>
	<p class="edd-account-verified edd_success">
		<?php esc_html_e( 'Your account has been successfully verified!', 'sejoli-edd' ); ?>
	</p>
	<?php
endif;
/**
 * This template is used to display the purchase history of the current user.
 */
if ( ! is_user_logged_in() ) {
	return;
}
?>
<div id='sejoli-edd-table-holder'>
<?php
$page    = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
$user_id = get_current_user_id();
$orders  = edd_get_orders(
	array(
		'user_id'        => $user_id,
		'number'         => 20,
		'offset'         => 20 * ( intval( $page ) - 1 ),
		'type'           => 'sale',
		'status__not_in' => array( 'trash' ),
	)
);

/**
 * Fires before the order history, whether or not orders have been found.
 *
 * @since 3.0
 * @param array $orders  The array of order objects for the current user.
 * @param int   $user_id The current user ID.
 */
do_action( 'edd_pre_order_history', $orders, $user_id );

if ( $orders ) :
	do_action( 'edd_before_order_history', $orders );
	?>
	<table style="width:100%" class='ui single line table'>
		<thead>
			<tr class="edd_purchase_row">
				<?php do_action( 'edd_purchase_history_header_before' ); ?>
				<th class="edd_purchase_id"><?php esc_html_e( 'ID', 'sejoli-edd' ); ?></th>
				<th class="edd_purchase_date"><?php esc_html_e( 'Item', 'sejoli-edd' ); ?></th>
				<th class="edd_purchase_details"><?php esc_html_e( 'Details', 'sejoli-edd' ); ?></th>
				<?php do_action( 'edd_purchase_history_header_after' ); ?>
			</tr>
		</thead>

		<?php require plugin_dir_path( __FILE__ ) . '/history-purchases-row-data.php'; ?>
		
	</table>
	<?php
	$count = edd_count_orders(
		array(
			'user_id' => get_current_user_id(),
			'type'    => 'sale',
		)
	);
	echo edd_pagination(
		array(
			'type'  => 'purchase_history',
			'total' => ceil( $count / 20 ), // 20 items per page
		)
	);
	do_action( 'edd_after_order_history', $orders );
	?>
<?php else : ?>
	<p class="edd-no-purchases"><?php esc_html_e( 'You have not made any purchases.', 'sejoli-edd' ); ?></p>
	<?php
endif;
?>
</div>
<div id='edd-purchase-detail' class="ui modal">
	<i class='close icon'></i>
	<div class="content">

	</div>
</div>

<div class="edd-alert-holder"></div>

<?php endif; ?>