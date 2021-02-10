<?php if( ! empty( $_GET['edd-verify-success'] ) ) : ?>
<p class="edd-account-verified edd_success">
	<?php _e( 'Your account has been successfully verified!', 'easy-digital-downloads' ); ?>
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
					<th class="edd_purchase_id" style="width:100px;"><?php _e('ID','easy-digital-downloads' ); ?></th>
					<th class="edd_purchase_details"><?php _e('Details','easy-digital-downloads' ); ?></th>
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
		<p class="edd-no-purchases"><?php _e('Belum ada data','easy-digital-downloads' ); ?></p>
	<?php endif;

?></div><!-- END #sejoli-edd-table-holder --><?php

endif;

?>
<div id='edd-purchase-detail' class="ui modal">
	<i class='close icon'></i>
	<div class="content">

	</div>
</div>
