<?php

class WISubscription
{

    private $db;

    public function __construct()
    {
        $this->db = new PDO("mysql:host=localhost;dbname=wicms","root","");
    }

    public function create($data)
    {

        $stmt = $this->db->prepare("
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