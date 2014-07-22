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
				'BOG' => __('Bogotá D.C.', 'woocommerce') ,
				'CUN' => __('Bosa, Soacha, Cajica, Chia, Madrid, Usme', 'woocommerce') ,
				'MED' => __('Medellín', 'woocommerce') ,
				'ANT' => __('Rionegro y Guame', 'woocommerce') ,
				'BUC' => __('Bucaramanga', 'woocommerce') ,
				'STN' => __('Girón y Floridablanca', 'woocommerce') ,
				'CAL' => __('Cali', 'woocommerce') ,
				'BAR' => __('Barranquilla', 'woocommerce') ,
				'ATL' => __('Malambo', 'woocommerce'),
				'CAR' => __('Cartagena', 'woocommerce'),
				'CUC' => __('Cúcuta', 'woocommerce'),
				'IBG' => __('Ibagué', 'woocommerce'),
				'STM' => __('Santa Marta', 'woocommerce'),
				'VIL' => __('Villavicencio', 'woocommerce'),
				'PER' => __('Pereira', 'woocommerce'),
				'MAN' => __('Manizales', 'woocommerce'),
				'OC' => __('Other Cities', 'woocommerce')
  );
 
  return $states;
}
add_filter( 'woocommerce_states', 'CO_woocommerce_states' );
?>