<?php
function customize_checkout_fields( $fields){
	unset($fields['billing']['billing_postcode']);
	unset($fields['billing']['billing_company']);
	unset($fields['shipping']['shipping_postcode']);
	unset($fields['shipping']['shipping_company']);
	$fields['billing']['billing_state']['label'] = __('Department','woocommerce');
	$fields['billing']['billing_state']['placeholder'] = __('Department','woocommerce');
	$fields['shipping']['shipping_state']['label'] = __('Department','woocommerce');
	$fields['shipping']['shipping_state']['placeholder'] = __('Department','woocommerce');
	return $fields;
}
add_filter('woocommerce_checkout_fields','customize_checkout_fields');

?>