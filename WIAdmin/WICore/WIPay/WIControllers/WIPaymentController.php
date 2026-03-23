<?php

require_once __DIR__ . '/../WIServices/WIPaymentService.php';

class WIPaymentController
{

    private $paymentService;

    public function __construct()
    {
        $this->paymentService = new WIPaymentService();
    }

    public function charge()
    {

        $amount = $_POST['amount'] ?? 0;
        $currency = $_POST['currency'] ?? 'GBP';
        $gateway = $_POST['gateway'] ?? 'manual';

        $result = $this->paymentService->charge($amount,$currency,$gateway);

        echo json_encode($result);

    }

}

?>