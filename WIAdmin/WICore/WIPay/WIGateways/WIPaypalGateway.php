<?php

class WIPaypalGateway
{

    public function charge($amount,$currency)
    {

        return [
            "status"=>"paid",
            "transaction"=>"paypal_" . uniqid()
        ];

    }

}

?>