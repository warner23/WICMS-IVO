<?php 
include_once  dirname(dirname(dirname(dirname(dirname(__FILE__))))) .'/init.php';
include_once('Helpers/PayPalHelper.php');

$paypalHelper = new PayPalHelper;


$randNo= (string)rand(10000,20000);

$orderData = '{

    "application_context" : {
        "return_url" : "' . PAYPAL_CALLBACK. '",
        "cancel_url" : "' . PAYPAL_CANCEL_URL. '",
        "auto_bill_amount": "YES",
    "initial_fail_amount_action": "CONTINUE",
    "max_fail_attempts": "0"
    },
    "name": sessionStorage.getItem(`membership_plan`),
  "description": sessionStorage.getItem(`membership_plan`) " Membership",
  "type": "fixed",
  "payment_definitions": [
  {
    "name": "Regular payment definition",
    "type": "REGULAR",
    "frequency": "MONTH",
    "frequency_interval": "12",
    "amount":
    {
      "value": "sessionStorage.getItem(`membership_plan`)",
      "currency": "'.CURRENCY.'"
    },
    "cycles": "12",
    {
      "type": "TAX",
      "amount":
      {
        "value": "10",
        "currency": "'.CURRENCY.'"
      }
    }]
  }
}';


    $orderDataArr = json_decode($orderData, true);
	
	$orderDataArr['application_context']['user_action'] = "PAY_NOW";
    $orderDataArr['application_context']['shipping_preference'] = "NO_SHIPPING";
    $orderData = json_encode($orderDataArr);


header('Content-Type: application/json');
//var_dump($orderData);
echo json_encode($paypalHelper->orderCreate($orderData));