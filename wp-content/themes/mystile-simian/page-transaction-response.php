<?php get_header();?>
<div id="content">
	<?php woo_main_before(); ?>
	<section id="main">
		<h1><?php echo __('Hello, ','woocommerce'); ?></h1>
		<span><?php echo __('Thanks for using our service!','woocommerce'); ?></span>
		<p><?php echo __('Print and present this receipt on any '.$_GET['paymentMethod'].' store of the country to fulfill the payment for your order. Tell the casheir the exact value on this receipt. Otherwhise is probable we may not be able to validate correctly the purchase.','woocommerce'); ?></p>
		<?php if($_GET['paymentMethod']=='BALOTO'){ ?>
			<img src="https://stg.gateway.payulatam.com/ppp-web-gateway/images/baloto.jpg" alt="via-baloto">
		<?php }else{ ?>
			<img src="https://stg.gateway.payulatam.com/ppp-web-gateway/images/efecty.jpg" alt="via-efecty">
		<?php } ?>
		<ul>
			<li><?php echo __('Payment Number: ','woocommerce').$_GET['paymentNumber']; ?></li> 
			<li><?php echo __('Value: $','woocommerce').$_GET['TX_VALUE'].' COP'; ?></li>
			<li><?php echo __('Agreement code : ','woocommerce').'123'; ?></li> 
			<li><?php 
			date_default_timezone_set('America/Bogota');
			$date = date('m/d/Y h:i:s a', time());
			echo __('Order Date: ','woocommerce').$date; ?></li> 
			<li><?php echo __('Expiration Date: ','woocommerce').$_GET['expirationDate']; ?></li> 
		</ul>
		<span><?php echo __('Keep in mind!','woocommerce'); ?></span>
		<ol>
			<li><?php echo __('This receipt is only valid for the current payment and will reflect on the commerce account 24 hourse after the payment.','woocommerce'); ?></li> 
			<li><?php echo __('If you have any doubts about your purchase, contact us, its our responsability to clarify ani claim about your purchase.','woocommerce'); ?></li>
			<li><?php echo __('Once your payment has beed recieved in '.$_GET['paymentMethod'].', PayU will inform us, and we will proceed to deliver the product.','woocommerce'); ?></li>
		</ol>
		<span><?php echo __('If you have additional doubts about payment, contact us or email PayU to ','woocommerce'); ?><a href="mailto:sac@payulatam.com">sac@payulatam.com</a></span>
		
	</section>
	<?php woo_main_after(); ?>
	<?php get_sidebar(); ?>
</div>
<div id="no-print-pdf">
	<button type="buttom" onclick="window.print();">Print Receipt</button>
	<button type="buttom" onclick="printPdfHtml();">Pdf</button>
</div>	
<?php get_footer(); ?>