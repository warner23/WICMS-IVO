<?php

class WIManualGateway
{

    public function charge($amount,$currency)
    {

        return [
            "status"=>"pending",
            "transaction"=>"manual_" . uniqid()
        ];

    }

}

?>