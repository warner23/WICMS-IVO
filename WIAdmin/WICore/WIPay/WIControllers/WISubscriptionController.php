<?php

require_once __DIR__ . '/../WIServices/WISubscriptionService.php';

class WISubscriptionController
{

    private $subscriptionService;

    public function __construct()
    {
        $this->subscriptionService = new WISubscriptionService();
    }

    public function create()
    {

        $customer = $_POST['customer_id'];
        $plan = $_POST['plan_id'];

        $result = $this->subscriptionService->create($customer,$plan);

        echo json_encode($result);

    }

}