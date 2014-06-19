<?php 
    /*
    Plugin Name: Gateway PayU Latam
    Description: Plugin that allows integration of Woocommerce with PayU Latam
    Author: Pablo García
    Version: 1.0
    */
function init_gateway_payu_class(){
	class WC_Gateway_PayU_Latam extends WC_Payment_Gateway {		
		public function config_payu(){
			$this->test_pay_url = 'https://stg.api.payulatam.com/payments-api/4.0/service.cgi';
			$this->pay_url = 'https://api.payulatam.com/payments-api/4.0/service.cgi';
			$this->isTest = $this->settings['testmode'];
			if($this->isTest == 'yes'){
				//Taken from http://docs.payulatam.com/integracion-con-api/pruebas-de-pago-en-api/ for tests
				$this->apiKey = '6u39nqhq8ftd0hlvnjfs66eh8c';
				$this->apiLogin = '11959c415b33d0c';
				$this->merchantId = '500238';
				$this->account_id = '500538';
			}else{
				$this->apiKey = $this->settings['apikey'];
				$this->apiLogin = $this->settings['apilogin'];
				$this->merchantId = $this->settings['merchant_id'];
				$this->account_id = $this->settings['account_id'];  
			}
			
			$this->language = 'es';
			$this->country = 'CO';
			$this->currencyPayU = 'COP';
								
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
			$this->supports[] = 'default_credit_card_form';
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
					'title' 		=> __('Api Key', 'woocommerce'),
					'type' 			=> 'text',
					'description' 	=>  __('Given by PayU Latam', 'woocommerce'),
					'desc_tip' 		=> true
                	),
				'account_id' => array(
					'title' 		=> __('Account ID', 'woocommerce'),
					'type' 			=> 'text',
					'description' 	=> __('Some Countrys (Brasil, Mexico) require this ID, Gived to you by PayU Latam on regitration.', 'woocommerce'),
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
		public function payment_fields() {
			if ( $description = $this->get_description() ) {
        		echo wpautop( wptexturize( $description ) );
         	}
 	       if ( $this->supports( 'default_credit_card_form' ) ) {
    	        $this->credit_card_form();
        	}
    	}
		public function payulatam_order_args($order){
			$txnid = $order->order_key;
			$productinfo = 'Orden de woocommerce';
			$order_total = $order->get_total();
			$tax_return_base = $this->settings['tax_return_base'];
			$taxes = $this->settings['taxes'];	
			$str = $this->settings['apikey'].'~'.$this->settings['merchant_id'].'~'.$txnid.'~'.$order_total.'~'.$this->currencyPayU;
			$hash =  strtolower(md5($str));
			$date_credit_card =	str_replace(' ', '',$_POST['payu_latam-card-expiry']);
			$month = substr($date_credit_card,0,2);
			$year = substr($date_credit_card,3);				
			if($this->isTest){
				$payer_name = 'APPROVED';
			}else{
				$payer_name = $_POST['billing_first_name'].' '.$_POST['billing_last_name'];
			}
			return array(
				'REFERENCE_CODE' => $txnid,
				'DESCRIPTION' => $productinfo,
				'VALUE' => $order_total,
				'SIGNATURE' => $hash,
				'COUNTRY' => $this->country,
				'CREDIT_CARD_NUMBER' => $_POST['payu_latam-card-number'],
				'PAYER_NAME' => $payer_name,
				'CREDIT_CARD_EXPIRATION_DATE' => $year.'/'.$month,
				'CREDIT_CARD_SECURITY_CODE' => $_POST['payu_latam-card-cvc'],
				'PAYMENT_METHOD' => 'VISA',
				'TAX_RETURN_BASE' => $tax_return_base,
				'TAX_VALUE' => $taxes,
				'INSTALLMENTS_NUMBER' => 1,	
				'CURRENCY' => $this->currencyPayU
				);
		}
		public function build_credit_card($parameters){
			$creditCard = new stdClass();
			$creditCard->number = str_replace(' ', '', $parameters['CREDIT_CARD_NUMBER']);
			$creditCard->securityCode = $parameters['CREDIT_CARD_SECURITY_CODE'];
			$creditCard->expirationDate = stripslashes($parameters['CREDIT_CARD_EXPIRATION_DATE']);
			if($this->isTest=='yes'){
				$creditCard->name = 'APPROVED';
			}else{
				$creditCard->name = $parameters['PAYER_NAME'];
			}
			return $creditCard;
		}
		public function request_assembler($command, $parameters){
			$request = new stdClass();
			$request->language = 'es';
			$request->command = $command;
			$merchant = new stdClass();
			$merchant->apiLogin = '11959c415b33d0c';
			$merchant->apiKey= '6u39nqhq8ftd0hlvnjfs66eh8c';
			$request->merchant = $merchant;
			
			if($command == 'SUBMIT_TRANSACTION'){
				$transaction = new stdClass();
				$order = new stdClass();
				if($this->account_id!=''){
					$order->accountId = $this->account_id;
				}				
				$order->referenceCode = $parameters['REFERENCE_CODE'];
				$order->description = $parameters['DESCRIPTION'];
				$order->language = $this->language;
				$order->signature = $parameters['SIGNATURE'];
				$shippingAddress = new stdClass();
				$shippingAddress->country = $this->country;
				$order->shippingAddress = $shippingAddress;
				$buyer = new stdClass();
				if($this->isTest=='yes'){
					$buyer->fullName = 'APPROVED';
				}else{
					$buyer->fullName = $parameters['PAYER_NAME'];
				}
				$buyer->emailAddress = $_POST['billing_email'];
				$order->buyer = $buyer;				
				$additionalValues = new stdClass();
				$TX_VALUE = new stdClass();
				$TX_VALUE->value = $parameters['VALUE'];
				$TX_VALUE->currency = $this->currencyPayU;
				$additionalValues->TX_VALUE = $TX_VALUE;
				$order->additionalValues = $additionalValues;
				$transaction->order = $order;				
				$creditCard = $this->build_credit_card($parameters);
				$transaction->creditCard = $creditCard;
				$transaction->type = 'AUTHORIZATION_AND_CAPTURE';
				$transaction->paymentMethod = $parameters['PAYMENT_METHOD'];
				$transaction->paymentCountry = $parameters['COUNTRY'];
				$payer = new stdClass();
				if($this->isTest=='yes'){
					$payer->fullName = 'APPROVED';
				}else{
					$payer->fullName = $parameters['PAYER_NAME'];
				}
				$payer->emailAddress = $_POST['billing_email'];
				$payer->contactPhone = $_POST['billing_phone'];
				$transaction->payer = $payer;
				$extraParameters = new stdClass();
				$extraParameters->INSTALLMENTS_NUMBER = $parameters['INSTALLMENTS_NUMBER'];
				$transaction->extraParameters = $extraParameters;				
				$request->transaction = $transaction;
			}
			if($this->isTest =='yes'){
				$request->test = true;
			}else{
				$request->test = false;
			}
			$requestJSON = json_encode($request);
			return $requestJSON;
		}			
		public function init_curl_json($requestJSON){
			if($this->isTest=='yes'){
				$curl = curl_init($this->test_pay_url);
			}else{
				$curl = curl_init($this->pay_url);
			}			
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($curl, CURLOPT_POSTFIELDS, $requestJSON);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=UTF-8', 'Accept: application/json'));
			return $curl;
		}
		public function process_payment( $order_id ){
			global $woocommerce;
			$order = new WC_Order( $order_id );
			$requestJSON = $this->request_assembler('PING',NULL);
			$curl = $this->init_curl_json($requestJSON);
			$curlResponse = json_decode(curl_exec($curl));
			$httpStatus = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
			curl_close($curl);
			if($curlResponse->code == 'SUCCESS'){
				$order->update_status('on-hold', __( 'Awaiting PayU Latam payment', 'woocommerce' ));
				$parameters = $this->payulatam_order_args($order);	
				$requestJSON = 	$this->request_assembler('SUBMIT_TRANSACTION',$parameters);
				$curl = $this->init_curl_json($requestJSON);
				$curlResponse = json_decode(curl_exec($curl));
				$httpStatus = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
				curl_close($curl);
				if($curlResponse->transactionResponse->state == 'APPROVED'){
					// Remove cart
					$woocommerce->cart->empty_cart();
					$order->payment_complete();
					// Return thankyou redirect
					return array(
						'result' => 'success',
						'redirect' => $this->get_return_url($order)
						);
				}else{
					$woocommerce->add_error('Hubo un error conla transaccion. Estado :'.$curlResponse->transactionResponse->state);
					return;
				}
			}else{
				$woocommerce->add_error('Hubo un error de conexion con PayU Latam');
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