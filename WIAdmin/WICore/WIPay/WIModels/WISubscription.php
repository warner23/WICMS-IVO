<?php

class WISubscription
{

    private WIdb $WIdb;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    public function create($data)
    {

        $stmt = $this->WIdb->prepare("
            INSERT INTO subscriptions
            (customer_id,plan_id,status)
            VALUES
            (:customer,:plan,:status)
        ");

        $stmt->execute([
            ':customer'=>$data['customer_id'],
            ':plan'=>$data['plan_id'],
            ':status'=>$data['status']
        ]);

        return true;

    }

}

?>