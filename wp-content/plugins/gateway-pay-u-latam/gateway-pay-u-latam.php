<?php 
    /*
    Plugin Name: Gateway PayU Latam
    Description: Plugin that allows integration of Woocommerce with PayU Latam
    Author: Pablo García
    Version: 1.0
    */
require_once (plugin_dir_path( __FILE__ ).'payu-php-sdk/lib/PayU.php');
function init_gateway_payu_class(){
	class WC_Gateway_PayU_Latam extends WC_Payment_Gateway {		
		function config_payu(){
			$test_pay_url = 'https://stg.api.payulatam.com/payments-api/4.0/service.cgi';
			$test_consult_url = 'https://stg.api.payulatam.com/reports-api/4.0/service.cgi';
			$pay_url = 'https://api.payulatam.com/payments-api/4.0/service.cgi';
			$consult_url = 'https://api.payulatam.com/reports-api/4.0/service.cgi';
			PayU::$apiKey = $this->settings['apikey']; 
			PayU::$apiLogin = $this->settings['apilogin'];
			PayU::$merchantId = $this->settings['merchant_id']; 
			PayU::$language = SupportedLanguages::ES; 
			PayU::$isTest = $this->settings['testmode'];
			Environment::setPaymentsCustomUrl("https://api.payulatam.com/payments-api/4.0");
			Environment::setReportsCustomUrl("https://api.payulatam.com/reports-api/4.0");
			Environment::setSubscriptionsCustomUrl("https://api.payulatam.com/payments-api/4.3"); 
		}
		public function __construct(){
			$this->id = 'payu_latam';
			$this->icon = 'http://docs.payulatam.com/wp-content/uploads/2013/07/cropped-logo.png';
			$this->has_fields = true;
			$this->method_title = 'PayU Latam';
			$this->method_description = 'Connects payments to PayU Latam';
			$this->init_form_fields();
			$this->init_settings();
			$this->config_payu();
			$this->title = $this->settings['title'];
			$this->description = $this->settings['description'];
			$this->currency	= get_woocommerce_currency();
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		}
		public function init_form_fields(){
			$this->form_fields = array(
				'enabled' => array(
					'title' => __( 'Enable/Disable', 'woocommerce' ),
					'type' => 'checkbox',
					'label' => __( 'Enable PayU Latam Payment', 'woocommerce' ),
					'default' => 'yes'
					),
				'title' => array(
					'title' => __( 'PayU Latam', 'woocommerce' ),
					'type' => 'text',
					'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
					'default' => __( 'PayU Latam Payment', 'woocommerce' ),
					'desc_tip'      => true,
					),
				'description' => array(
					'title' => __( 'Customer Message', 'woocommerce' ),
					'type' => 'textarea',
					'default' => ''
					),
				'merchant_id' => array(
					'title' 		=> __('Merchant ID', 'woocommerce'),
					'type' 			=> 'text',
					'description' 	=> __('Given by PayU Latam', 'woocommerce'),
					'desc_tip' 		=> true
					),
				'apilogin' => array(
					'title' 		=> __('API Login', 'woocommerce'),
					'type' 			=> 'text',
					'description' 	=> __('Given by PayU Latam', 'woocommerce'),
					'desc_tip' 		=> true
					),
				'apikey' => array(
					'title' 		=> __('ApiKey', 'woocommerce'),
					'type' 			=> 'text',
					'description' 	=>  __('Given by PayU Latam', 'woocommerce'),
					'desc_tip' 		=> true
                	),
				'taxes' => array(
					'title' 		=> __('Tax Rate - Read', 'woocommerce').' <a target="_blank" href="http://docs.payulatam.com/manual-integracion-web-checkout/informacion-adicional/tablas-de-variables-complementarias/">PayU Documentacion</a>',
					'type' 			=> 'text',
					'default' 		=> '0',
					'description' 	=> __('Tax rates for Transactions (IVA).', 'woocommerce'),
					'desc_tip' 		=> true
		        	),
      			'tax_return_base' => array(
					'title' 		=> __('Tax Return Base', 'woocommerce'),
					'type' 			=> 'text',
					'default' 		=> '0',
					'description' 	=> __('Tax base to calculate IVA ', 'woocommerce'),
					'desc_tip' 		=> true
                	),
      			'testmode' => array(
					'title' 		=> __('TEST Mode', 'woocommerce'),
					'type' 			=> 'checkbox',
					'label' 		=> __('Enable PayU Latam TEST Transactions.', 'woocommerce'),
					'default' 		=> 'no',
					'description' 	=> __('Tick to run TEST Transaction on the PayU Latam platform', 'payu-latam-woocommerce'),
					'desc_tip' 		=> true
                )
				);
		}
		public function payulatam_order_args($order){
			$txnid = $order->order_key;
			$productinfo = 'Pedido $order_id';
			$order_total = $order->get_total();
			$str ="$this->settings['apikey']~$this->settings['merchant_id']_id~$txnid~$order_total~$this->currency";
			$hash =  strtolower(md5( $str));
			$taxes = $this->settings['taxes'];
			$tax_return_base = $this->settings['tax_return_base'];
			if(PayU::$isTest){
				$payer_name = 'APPROVED';
			}else{
				$payer_name = 'APPROVED';
			}
			return array(
				PayUParameters::REFERENCE_CODE => $txnid,
				PayUParameters::DESCRIPTION => $productinfo,
				PayUParameters::VALUE => $order_total,
				PayUParameters::SIGNATURE => $hash,
				PayUParameters::CREDIT_CARD_NUMBER => '4556906384445985',
				PayUParameters::PAYER_NAME => $payer_name,
				PayUParameters::CREDIT_CARD_EXPIRATION_DATE => "2015/01",
				PayUParameters::CREDIT_CARD_SECURITY_CODE => "495",
				PayUParameters::PAYMENT_METHOD => PaymentMethods::VISA,
				PayUParameters::PROCESS_WITHOUT_CVV2 => "true",
				PayUParameters::TAX_RETURN_BASE => $taxes,
				PayUParameters::TAX_VALUE => $tax_return_base);
		}
		public function process_payment( $order_id ){
			global $woocommerce;
			$order = new WC_Order( $order_id );
			// Mark as on-hold (we're awaiting the cheque)
			$order->update_status('on-hold', __( 'Awaiting PayU Latam payment', 'woocommerce' ));
			
			if(PayUPayments::doPing()){
				$parameters = $this->payulatam_order_args($order);	
				$result = PayUPayments::doAuthorizationAndCapture($parameters);
				if($result['paymentResponse']['transactionResponse']['state']=='APPROVED'){
					$order->reduce_order_stock();
					// Remove cart
					$woocommerce->cart->empty_cart();
					$order->payment_complete();
					// Return thankyou redirect
					return array(
						'result' => 'success',
						'redirect' => $this->get_return_url( $order )
						);
				}
			}else{
				$woocommerce->add_error($error_message);
				return;
			}
		}
	}
}
function add_payu_gateway_class( $methods ){
	$methods[] = 'WC_Gateway_PayU_Latam';
	return $methods;
}
add_action('plugins_loaded','init_gateway_payu_class');
add_filter( 'woocommerce_payment_gateways', 'add_payu_gateway_class' );
?>