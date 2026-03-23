<?php

require_once __DIR__ . '/../Models/Subscription.php';

class WISubscriptionService
{

    public function create($customer,$plan)
    {

        $subscription = new Subscription();

        return $subscription->create([
            'customer_id'=>$customer,
            'plan_id'=>$plan,
            'status'=>'active'
        ]);

    }

}

?>