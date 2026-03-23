<?php



$paypal = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential(paypal_id,paypal_secret)
	);

?>