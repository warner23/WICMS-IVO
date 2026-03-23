<?php 
include_once  dirname(dirname(dirname(dirname(dirname(__FILE__))))) .'/init.php';
include_once('Helpers/PayPalHelper.php');

$paypalHelper = new PayPalHelper;

$randNo= (string)rand(10000,20000);

$order = $cart->checkoutCart();

$orderAddress = $cart->OrderAddress();

$total_amount = $cart->TotalCost();
//echo $total_amount;

$Shipping_costs = $cart->OrderCost();

$orderData = '{
    "intent" : "CAPTURE",
    "application_context" : {
        "return_url" : "' . PAYPAL_CALLBACK. '",
        "cancel_url" : "' . PAYPAL_CANCEL_URL. '"
    },
    "purchase_units" : [ 
        {
            "reference_id" : "'.$randNo.'",
            "description" : "WICMS Shop",
            "invoice_id" : "INV-' . SHOP_NAME . '-'.$randNo.'",
            "custom_id" : "CUST-' . SHOP_NAME . '",
            "amount" : {
                "currency_code" : "'.CURRENCY.'",
                "value" : "' . $total_amount . '",
                "breakdown" : {
                    "item_total" : {
                        "currency_code" : "'.CURRENCY.'",
                        "value" : "'.$total_amount.'"
                    },
                    "shipping" : {
                        "currency_code" : "'.CURRENCY.'",
                        "value" : "' . $Shipping_costs . '"
                    },
                    "tax_total" : {
                        "currency_code" : "'.CURRENCY.'",
                        "value" : "0.00"
                    },
                    "handling" : {
                        "currency_code" : "'.CURRENCY.'",
                        "value" : "0.00"
                    },
                    "shipping_discount" : {
                        "currency_code" : "'.CURRENCY.'",
                        "value" : "0.00"
                    },
                    "insurance" : {
                        "currency_code" : "'.CURRENCY.'",
                        "value" : "0.00"
                    }

                }
            },
            "item_list": {
            "items" : [
            '.$cart->CreateOrder() .'
                ]
            }
        }
    ]
}';


    $orderDataArr = json_decode($orderData, true);
	$orderDataArr['application_context']['shipping_preference'] = "SET_PROVIDED_ADDRESS";
	$orderDataArr['application_context']['user_action'] = "PAY_NOW";
	
    $orderDataArr['purchase_units'][0]['shipping']['address']['address_line_1']= $orderAddress[0]['address'];
    $orderDataArr['purchase_units'][0]['shipping']['address']['admin_area_2']= $orderAddress[0]['city'];

    $orderDataArr['purchase_units'][0]['shipping']['address']['postal_code']= $orderAddress[0]['postcode'];
    $orderDataArr['purchase_units'][0]['shipping']['address']['country_code']= $orderAddress[0]['country'];

    $orderData = json_encode($orderDataArr);


header('Content-Type: application/json');
//var_dump($orderData);
echo json_encode($paypalHelper->orderCreate($orderData));