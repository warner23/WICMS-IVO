<?php 
include_once  dirname(dirname(dirname(dirname(dirname(__FILE__))))) .'/init.php';
include_once('Helpers/PayPalHelper.php');

$paypalHelper = new PayPalHelper;

$randNo= (string)rand(10000,20000);


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
                "value" : "'.$_POST['total_amt'].'",
                "breakdown" : {
                    "item_total" : {
                        "currency_code" : "'.CURRENCY.'",
                        "value" : "'.$_POST['total_amt'].'"
                    },
                    "tax_total" : {
                        "currency_code" : "'.CURRENCY.'",
                        "value" : "0.00"
                    }
                }
            },

            "item_list": {
            "items" : [{
                "name" : "'.$_POST['item_title'].'",
                "description" : "'.$_POST['item_title'].'",
                "sku" : "sku01",
                "unit_amount" : {
                    "currency_code" : "'.CURRENCY.'",
                    "value" : "'.$_POST['item_amt'].'"
                },
                "quantity" : "'.$_POST['item_qty'].'",
                "category" : "Training"
            }]
            }
        }
    ]
}';


    $orderDataArr = json_decode($orderData, true);
	
	$orderDataArr['application_context']['user_action'] = "PAY_NOW";
    $orderDataArr['application_context']['shipping_preference'] = "NO_SHIPPING";
    $orderData = json_encode($orderDataArr);


header('Content-Type: application/json');
//var_dump($orderData);
echo json_encode($paypalHelper->orderCreate($orderData));