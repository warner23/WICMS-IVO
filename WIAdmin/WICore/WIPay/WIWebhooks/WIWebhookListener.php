<?php

require_once __DIR__ . '/../Models/Payment.php';

class WIWebhookListener
{

    public function handle()
    {

        $payload = file_get_contents("php://input");

        $data = json_decode($payload,true);

        if($data['event'] == "payment_success")
        {

            $payment = new Payment();

            $payment->updateStatus(
                $data['transaction'],
                "paid"
            );

        }

    }

}


?>