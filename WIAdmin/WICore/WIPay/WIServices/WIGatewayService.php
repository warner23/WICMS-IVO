<?php

require_once __DIR__ . '/../Gateways/StripeGateway.php';
require_once __DIR__ . '/../Gateways/PaypalGateway.php';
require_once __DIR__ . '/../Gateways/ManualGateway.php';

class WIGatewayService
{

    public function load($gateway)
    {

        switch($gateway)
        {

            case "stripe":
                return new StripeGateway();

            case "paypal":
                return new PaypalGateway();

            default:
                return new ManualGateway();

        }

    }

}

?>