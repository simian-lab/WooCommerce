<?php
function remove_unused_field( $fields){
	unset($fields['billing']['billing_postcode']);
	unset($fields['billing']['billing_company']);
	return $fields;
}
add_filter('woocommerce_checkout_fields','remove_unused_field');

?>