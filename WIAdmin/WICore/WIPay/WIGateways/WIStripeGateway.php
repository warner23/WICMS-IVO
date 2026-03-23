<?php

class WIStripeGateway
{

    public function charge($amount,$currency)
    {

        // Example simulation

        return [
            "status"=>"paid",
            "transaction"=>"stripe_" . uniqid()
        ];

    }

}

?>