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
function enqueue_print_style(){
	wp_enqueue_style('print', get_stylesheet_directory_uri() . '/css/print.css' );	
}
add_action('wp_enqueue_scripts', 'enqueue_print_style'); 
function CO_woocommerce_states( $states ) {
 
  $states['CO'] = array(
				'CU' => __('Cundinamarca', 'woocommerce') ,
				'AN' => __('Antioquia', 'woocommerce') ,
				'ST' => __('Santander', 'woocommerce') ,
				'VC' => __('Valle Del Cauca', 'woocommerce') ,
				'AT' => __('Atlántico', 'woocommerce') ,
				'BO' => __('Bolívar', 'woocommerce') ,
				'NS' => __('Norte de Santander', 'woocommerce') ,
				'TO' => __('Tolima', 'woocommerce') ,
				'MG' => __('Magdalena', 'woocommerce'),
				'ME' => __('Meta', 'woocommerce'),
				'RS' => __('Risaralda', 'woocommerce'),
				'CA' => __('Caldas', 'woocommerce'),
				'OC' => __('Other Cities', 'woocommerce')
  );
 
  return $states;
}
add_filter( 'woocommerce_states', 'CO_woocommerce_states' );
?>