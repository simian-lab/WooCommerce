<?php
get_header();
?>
<div id="content">
	<?php woo_main_before(); ?>
	<section id="main">
<p><?php _e( 'Your PSE order data is displayed now.', 'woocommerce' ); ?></p>
		<ul class="pse-order-details">
			<li class="company">
				<?php _e( 'Company:', 'woocommerce' ); ?>
				<strong>Simian</strong>
			</li>
			<li class="nit">
				<?php _e( 'Nit:', 'woocommerce' ); ?>
				<strong>NIT Number</strong>
			</li>
			<li class="date">
				<?php _e( 'Transaction Date:', 'woocommerce' ); ?>
				<strong><?php echo $_GET['processingDate']; ?></strong>
			</li>
			<li class="state">
				<?php _e( 'State:', 'woocommerce' ); ?>
				<strong><?php echo $_GET['transactionState']; ?></strong>
			</li>
			<li class="reference-code">
				<?php _e( 'Reference Code:', 'woocommerce' ); ?>
				<strong><?php echo $_GET['referenceCode']; ?></strong>
			</li>
			<li class="transaction-id">
				<?php _e( 'Transaction ID:', 'woocommerce' ); ?>
				<strong><?php echo $_GET['transactionId']; ?></strong>
			</li>
			<li class="cus">
				<?php _e( 'CUS:', 'woocommerce' ); ?>
				<strong><?php echo $_GET['cus']; ?></strong>
			</li>
			<li class="bank">
				<?php _e( 'PSE Bank:', 'woocommerce' ); ?>
				<strong><?php echo $_GET['pseBank']; ?></strong>
			</li>
			<li class="value">
				<?php _e( 'Value:', 'woocommerce' ); ?>
				<strong><?php echo $_GET['TX_VALUE']; ?></strong>
			</li>
			<li class="currency">
				<?php _e( 'Currency:', 'woocommerce' ); ?>
				<strong><?php echo $_GET['currency']; ?></strong>
			</li>
			<li class="description">
				<?php _e( 'Description:', 'woocommerce' ); ?>
				<strong><?php echo $_GET['description']; ?></strong>
			</li>
			<li class="origin-ip">
				<?php _e( 'Origin IP:', 'woocommerce' ); ?>
				<strong><?php echo $_GET['pseReference1']; ?></strong>
			</li>
			<button type="button"><?php _e( 'Retry transaction', 'woocommerce' ); ?></button>
			<button type="button"><?php _e( 'End transaction', 'woocommerce' ); ?></button>
			<button type="button"><?php _e( 'Print Voucher', 'woocommerce' ); ?></button>
		</section>
		</ul>
		<?php woo_main_after(); ?>
	    <?php get_sidebar(); ?>
</div>

    <?php get_footer(); ?>