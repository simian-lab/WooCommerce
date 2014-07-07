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
			$this->test_reports_url = 'https://stg.api.payulatam.com/reports-api/4.0/service.cgi';
			$this->reports_url = 'https://api.payulatam.com/reports-api/4.0/service.cgi';
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
         	echo '<label for="payu_latam-payment-select">' . __( 'Payment Method Select', 'woocommerce' ) . ' <span class="required">*</span></label>
         	<select id="payu_latam-payment-select" name="payu_latam-payment-select" onchange="displayPayuForm()">
				<option value="Credit Card">Credit Card</option>
				<option value="PSE">PSE Bank Transfer</option>
				<option value="BALOTO">Baloto</option>
			</select>';
         	$this->credit_card_form(array('fields_have_names' => true), array('card-select-field' => '<p class="form-row form-row-first">
			<label for="payu_latam-card-select">' . __( 'Credit Card Type', 'woocommerce' ) . ' <span class="required">*</span></label>
			<select id="payu_latam-card-select" class="input-text wc-credit-card-form-card-select" name="payu_latam-card-select">
				<option value="VISA">VISA</option>
				<option value="MASTERCARD">MASTERCARD</option>
				<option value="AMEX">AMERICAN EXPRESS</option>
				<option value="DINERS">DINERS CLUB</option>
			</select></p>'));
         		$this->pse_form();    	
    	}
    	public function pse_form(){
    		$bankLists = $this->get_pse_banklist();
    		echo '<fieldset id="payu_latam-pse-form">';
         	echo '<label for="payu_latam-pse-banklist">' . __( 'Select Bank', 'woocommerce' ) . ' <span class="required">*</span></label><select id="payu_latam-pse-banklist" name="payu_latam-pse-bank">';
         	foreach ($bankLists as $key => $value) {
         		echo '<option value="'.$key.'">'.$key.'</option>';
			}
			echo '</select>';
			echo '<label for="payu_latam-person-type">' . __( 'Person Type', 'woocommerce' ) . ' <span class="required">*</span></label><select id="payu_latam-person-type" name="payu_latam-person-type">
				<option value="N">Natural</option>
				<option value="J">Juridic</option>
			</select>';
			echo '<label for="payu_latam-docid-type">' . __( 'ID Document Type', 'woocommerce' ) . ' <span class="required">*</span></label><select id="payu_latam-docid-type" name="payu_latam-docid-type">
				<option value="CC">Cedula de Ciudadania</option>
				<option value="NIT">NIT</option>
			</select>';
			echo '<label for="payu_latam-id-number">' . __( 'ID Number', 'woocommerce' ) . ' <span class="required">*</span></label>
			<input id="payu_latam-id-number" type="text" autocomplete="off" placeholder="' . __( 'ID Number', 'woocommerce' ) . '" name="payu_latam-id-number" />';
    		echo '</fieldset>';
    	}
    	public function get_pse_banklist(){
    		$requestJSON = $this->request_assembler('GET_BANKS_LIST', NULL);
    		$curl = $this->init_curl_json($requestJSON,'pay');
    		$curlResponse = json_decode(curl_exec($curl));
			$httpStatus = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
			curl_close($curl);
			$bankLists = array();
			foreach ($curlResponse->banks as $key => $value) {
				$bankLists = array_merge($bankLists,array($value->description => $value->pseCode));
			}
			return $bankLists;
    	}
		public function payulatam_order_args($order){
			global $woocommerce;
			$txnid = $order->order_key;
			$productinfo = 'Orden '.$txnid;
			$order_total = $order->get_total();
			$tax_return_base = $this->settings['tax_return_base'];
			$taxes = $this->settings['taxes'];				
			$str = $this->apiKey.'~'.$this->merchantId.'~'.$txnid.'~'.$order_total.'~'.$this->currencyPayU;
			$hash =  strtolower(md5($str));
			if ($_POST['payu_latam-payment-select'] == 'Credit Card'){
				$date_credit_card =	str_replace(' ', '',$_POST['payu_latam-card-expiry']);
				$month = substr($date_credit_card,0,2);
				$year = substr($date_credit_card,3);
				if(strlen($year)<4){
					wc_add_notice('Year has to be in format YYYY',$notice_type = 'error');
					return;
				}		
			}
					
			if($this->isTest){
				$payer_name = 'APPROVED';
			}else{
				$payer_name = $_POST['billing_first_name'].' '.$_POST['billing_last_name'];
			}
			$returnArray = array(
				'REFERENCE_CODE' => $txnid,
				'DESCRIPTION' => $productinfo,
				'VALUE' => $order_total,
				'SIGNATURE' => $hash,
				'COUNTRY' => $this->country,
				'PAYER_NAME' => $payer_name,
				'PAYMENT_METHOD' => $_POST['payu_latam-payment-select'],
				'TAX_RETURN_BASE' => $tax_return_base,
				'TAX_VALUE' => $taxes,
				'CURRENCY' => $this->currencyPayU
				);
			if($_POST['payu_latam-payment-select'] == 'Credit Card'){
				$returnArray['CREDIT_CARD_EXPIRATION_DATE'] = $year.'/'.$month;
				$returnArray['CREDIT_CARD_SECURITY_CODE'] = $_POST['payu_latam-card-cvc'];
				$returnArray['CREDIT_CARD_NUMBER'] = $_POST['payu_latam-card-number'];
				$returnArray['INSTALLMENTS_NUMBER'] = 1;
			}
			return $returnArray;
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
				if ($parameters['PAYMENT_METHOD'] != 'PSE' && $parameters['PAYMENT_METHOD'] != 'BALOTO' && $parameters['PAYMENT_METHOD'] != 'EFECTY') {
					$creditCard = $this->build_credit_card($parameters);
					$transaction->creditCard = $creditCard;
				}				
				$transaction->type = 'AUTHORIZATION_AND_CAPTURE';
				if($parameters['PAYMENT_METHOD'] == 'Credit Card'){
					$transaction->paymentMethod = $_POST['payu_latam-card-select'];
				}else{
					$transaction->paymentMethod = $parameters['PAYMENT_METHOD'];
				}				
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
				if ($parameters['PAYMENT_METHOD'] != 'PSE' && $parameters['PAYMENT_METHOD'] != 'BALOTO' && $parameters['PAYMENT_METHOD'] != 'EFECTY') {
					$extraParameters->INSTALLMENTS_NUMBER = $parameters['INSTALLMENTS_NUMBER'];
					$transaction->extraParameters = $extraParameters;				
				}
				if ($parameters['PAYMENT_METHOD'] == 'PSE') {
					$banksList = $this->get_pse_banklist();
					$extraParameters->FINANCIAL_INSTITUTION_CODE = $banksList[$_POST['payu_latam-pse-bank']];
					$extraParameters->FINANCIAL_INSTITUTION_NAME = $_POST['payu_latam-pse-bank'];
					$extraParameters->USER_TYPE = $_POST['payu_latam-person-type'];
					$extraParameters->PSE_REFERENCE2 = $_POST['payu_latam-docid-type'];
					$extraParameters->PSE_REFERENCE3 = $_POST['payu_latam-id-number'];
					$transaction->extraParameters = $extraParameters;	
				}							
				$request->transaction = $transaction;
			}
			if($this->isTest =='yes'){
				$request->test = true;
			}else{
				$request->test = false;
			}
			if($command == 'GET_BANKS_LIST'){
				$bankListInformation = new stdClass();
				$bankListInformation->paymentMethod = 'PSE';
				$bankListInformation->paymentCountry = $this->country;
				$request->bankListInformation = $bankListInformation;
			}
			if($command == 'ORDER_DETAIL_BY_REFERENCE_CODE'){
				$details = new stdClass();
				$details->referenceCode = $parameters['REFERENCE_CODE'];
				$request->details = $details;
			}
			$requestJSON = json_encode($request);
			return $requestJSON;
		}			
		public function init_curl_json($requestJSON, $payOrReport){
			if($payOrReport ==  'pay'){
				if($this->isTest=='yes'){
					$curl = curl_init($this->test_pay_url);
				}else{
					$curl = curl_init($this->pay_url);
				}	
			}
			if($payOrReport ==  'report'){
				if($this->isTest=='yes'){
					$curl = curl_init($this->test_reports_url);
				}else{
					$curl = curl_init($this->reports_url);
				}	
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
			$curl = $this->init_curl_json($requestJSON,'pay');
			$curlResponse = json_decode(curl_exec($curl));
			$httpStatus = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
			curl_close($curl);
			if($curlResponse->code == 'SUCCESS'){
				$order->update_status('on-hold', __( 'Awaiting PayU Latam payment', 'woocommerce' ));
				$parameters = $this->payulatam_order_args($order);	
				$requestJSON = 	$this->request_assembler('SUBMIT_TRANSACTION',$parameters);
				$bankListArray = $this->get_pse_banklist();
				$curl = $this->init_curl_json($requestJSON,'pay');
				$curlResponse = json_decode(curl_exec($curl));
				$httpStatus = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
				curl_close($curl);
				if($_POST['payu_latam-payment-select'] == 'PSE'){
					$requestJSON = 	$this->request_assembler('ORDER_DETAIL_BY_REFERENCE_CODE',$parameters);
					$curlAuxiliar = $this->init_curl_json($requestJSON,'report');
					$curlResponseAuxiliar = json_decode(curl_exec($curlAuxiliar));
					$httpStatus = curl_getinfo($curlAuxiliar, CURLINFO_EFFECTIVE_URL);
					curl_close($curlAuxiliar);
					if($curlResponseAuxiliar == 'PENDING'){
						wc_add_notice(__('You already have a pending PSE order with that reference: ','woocommerce').$parameters['REFERENCE_CODE'],$notice_type = 'error');
						$order->update_status('pending', __( 'Error with PayU Payment', 'woocommerce' ));
						return;
					}
					if($curlResponse->transactionResponse->state == 'ERROR'){
						wc_add_notice(__( 'There was an error with the transaction: ', 'woocommerce' ).$curlResponse->error. ' Code: '.$curlResponse->code. ' Transaction State: '.$curlResponse->transactionResponse->state.' Codigo de error : '.$curlResponse->transactionResponse->errorCode,$notice_type = 'error');
						$order->update_status('pending', __( 'Error with PayU Payment', 'woocommerce' ));
						return;
					}
					if($curlResponse->transactionResponse->state == 'DECLINED'){
						wc_add_notice(__( 'Your transaction was Declined', 'woocommerce' ).$curlResponse->transactionResponse->responseCode,$notice_type = 'error');
						$order->update_status('pending', __( 'Error with PayU Payment', 'woocommerce' ));
						return;
					}
					if($curlResponse->transactionResponse->state == 'PENDING'){
						$order->update_status('on-hold', __( 'Waiting for BALOTO confirmation', 'woocommerce' ));
						wp_redirect($curlResponse->transactionResponse->extraParameters->BANK_URL);
						exit;
					}
				}
				if($_POST['payu_latam-payment-select'] == 'BALOTO'){
					if($curlResponse->transactionResponse->state == 'ERROR'){
						wc_add_notice(__( 'There was an error with the transaction: ', 'woocommerce' ).$curlResponse->error. ' Code: '.$curlResponse->code. ' Transaction State: '.$curlResponse->transactionResponse->state.' Codigo de error : '.$curlResponse->transactionResponse->errorCode,$notice_type = 'error');
						$order->update_status('pending', __( 'Error with PayU Payment', 'woocommerce' ));
						return;
					}
					if($curlResponse->transactionResponse->state == 'DECLINED'){
						wc_add_notice(__( 'Your transaction was Declined', 'woocommerce' ).$curlResponse->transactionResponse->responseCode,$notice_type = 'error');
						$order->update_status('pending', __( 'Error with PayU Payment', 'woocommerce' ));
						return;
					}
					if($curlResponse->transactionResponse->state == 'PENDING'){
						$order->update_status('on-hold', __( 'Waiting for BALOTO confirmation', 'woocommerce' ));
						wp_redirect($curlResponse->transactionResponse->extraParameters->URL_PAYMENT_RECEIPT_HTML);
						exit;
					}
				}				
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
					wc_add_notice(__( 'There was an error with the transaction: ', 'woocommerce' ).$curlResponse->error. ' Code: '.$curlResponse->code. ' Transaction State: '.$curlResponse->transactionResponse->state.' Codigo de error : '.$curlResponse->transactionResponse->errorCode,$notice_type = 'error');
					$order->update_status('pending', __( 'Error with PayU Payment', 'woocommerce' ));
					return;
				}
			}else{
				wc_add_notice(__( 'There was an error connecting to PayU', 'woocommerce' ),$notice_type = 'error');
				return;
			}
		}
	}
}
function add_payu_gateway_class( $methods ){
	$methods[] = 'WC_Gateway_PayU_Latam';
	return $methods;
}
function override_credit_card_field( $fields ){
	$fields['card-expiry-field'] = '<p class="form-row form-row-first">
	<label for="payu_latam-card-expiry">' . __( 'Expiry (MM/YYYY)', 'woocommerce' ) . ' <span class="required">*</span></label>
	<input id="payu_latam-card-expiry" class="input-text wc-credit-card-form-card-expiry" type="text" autocomplete="off" placeholder="' . __( 'MM / YYYY', 'woocommerce' ) . '" name="payu_latam-card-expiry" />
    </p>';
	return $fields; 
}
function remove_zipcode_field( $fields){
	unset($fields['billing']['billing_postcode']);
	return $fields;
}
function payu_enqueue_scripts(){
	if ( function_exists( 'is_woocommerce' ) ) {
		wp_enqueue_style('payu-forms',plugins_url('/gateway-pay-u-latam/assets/css/payu-forms.css'),__FILE__);
		wp_enqueue_script('payu-display-form',plugins_url('/gateway-pay-u-latam/assets/js/displayPayForms.js'),__FILE__);
	}
}
add_action('plugins_loaded','init_gateway_payu_class');
add_filter( 'woocommerce_payment_gateways', 'add_payu_gateway_class' );
add_filter('woocommerce_credit_card_form_fields','override_credit_card_field');
add_filter('woocommerce_checkout_fields','remove_zipcode_field');
add_action( 'wp_enqueue_scripts', 'payu_enqueue_scripts', 99 );
?>