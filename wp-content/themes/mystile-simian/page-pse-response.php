<?php
get_header();
?>
<div id="content">
	<?php woo_main_before(); ?>
	<section id="main">
<p><?php _e( 'Your PSE order data is displayed now.', 'woocommerce' ); ?></p>
		<ul class="pse-order-details">
			<li class="company">
				<?php _e( 'Company:', 'woothemes' ); ?>
				<strong>Simian</strong>
			</li>
			<li class="nit">
				<?php _e( 'Nit:', 'woothemes' ); ?>
				<strong>NIT Number</strong>
			</li>
			<li class="date">
				<?php _e( 'Transaction Date:', 'woothemes' ); ?>
				<strong><?php echo $_GET['processingDate']; ?></strong>
			</li>
			<li class="state">
				<?php _e( 'State:', 'woothemes' ); ?>
				<strong><?php echo $_GET['transactionState']; ?></strong>
			</li>
			<li class="reference-code">
				<?php _e( 'Reference Code:', 'woothemes' ); ?>
				<strong><?php echo $_GET['referenceCode']; ?></strong>
			</li>
			<li class="transaction-id">
				<?php _e( 'Transaction ID:', 'woothemes' ); ?>
				<strong><?php echo $_GET['transactionId']; ?></strong>
			</li>
			<li class="cus">
				<?php _e( 'CUS:', 'woothemes' ); ?>
				<strong><?php echo $_GET['cus']; ?></strong>
			</li>
			<li class="bank">
				<?php _e( 'PSE Bank:', 'woothemes' ); ?>
				<strong><?php echo $_GET['pseBank']; ?></strong>
			</li>
			<li class="value">
				<?php _e( 'Value:', 'woothemes' ); ?>
				<strong><?php echo $_GET['TX_VALUE']; ?></strong>
			</li>
			<li class="currency">
				<?php _e( 'Currency:', 'woothemes' ); ?>
				<strong><?php echo $_GET['currency']; ?></strong>
			</li>
			<li class="description">
				<?php _e( 'Description:', 'woothemes' ); ?>
				<strong><?php echo $_GET['description']; ?></strong>
			</li>
			<li class="origin-ip">
				<?php _e( 'Origin IP:', 'woothemes' ); ?>
				<strong><?php echo $_GET['pseReference1']; ?></strong>
			</li>
			<button type="button"><?php _e( 'Retry transaction', 'woothemes' ); ?></button>
			<button type="button"><?php _e( 'End transaction', 'woothemes' ); ?></button>
			<button type="button"><?php _e( 'Print Voucher', 'woothemes' ); ?></button>
		</section>
		</ul>
		<?php woo_main_after(); ?>
	    <?php get_sidebar(); ?>
</div>

    <?php get_footer(); ?>