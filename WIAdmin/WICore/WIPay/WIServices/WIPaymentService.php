<?php

require_once __DIR__ . '/GatewayService.php';
require_once __DIR__ . '/../Models/Payment.php';

class WIPaymentService
{

    public function charge($amount,$currency,$gateway)
    {

        $gatewayService = new GatewayService();

        $gatewayObj = $gatewayService->load($gateway);

        $transaction = $gatewayObj->charge($amount,$currency);

        $payment = new Payment();

        $payment->create([
            'amount'=>$amount,
            'currency'=>$currency,
            'status'=>$transaction['status'],
            'transaction_ref'=>$transaction['transaction']
        ]);

        return $transaction;

    }

}

?>