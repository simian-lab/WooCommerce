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
	if(	is_page( 'payu-response' ) ||  is_page( 'transaction-response' ) ){
		wp_deregister_script( 'jquery' );
		wp_enqueue_script('jquery', get_stylesheet_directory_uri() . '/js/jquery-1.11.1.min.js' );
		wp_enqueue_script('jspdf', get_stylesheet_directory_uri() . '/jspdf/jspdf.source.js' );
		wp_enqueue_script('from-html', get_stylesheet_directory_uri() . '/js/from-html.js' );	
		wp_enqueue_style('print', get_stylesheet_directory_uri() . '/css/print.css' );		
	}	
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
function remove_calculate_zipcode( $value){
	$value = false;
	return 	$value;
}
add_filter('woocommerce_shipping_calculator_enable_postcode', 'remove_calculate_zipcode');
function remove_shipping_label($full_label){
    $full_label = str_replace('Shipping: ','',  $full_label);
    return $full_label;
}
add_filter( 'woocommerce_cart_shipping_method_full_label', 'remove_shipping_label');
?>