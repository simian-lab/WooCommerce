function displayPayuForm(){
	var selectedPayment = document.getElementById('payu_latam-payment-select').value;
	if (selectedPayment == 'PSE'){
		document.getElementById('payu_latam-pse-form').style.display = 'block';
		document.getElementById('payu_latam-cc-form').style.display = 'none';
	}	
	if (selectedPayment == 'Credit Card'){
		document.getElementById('payu_latam-cc-form').style.display = 'block';
		document.getElementById('payu_latam-pse-form').style.display = 'none';
	}
	if (selectedPayment == 'BALOTO'){
		document.getElementById('payu_latam-cc-form').style.display = 'none';
		document.getElementById('payu_latam-pse-form').style.display = 'none';
	}
}
