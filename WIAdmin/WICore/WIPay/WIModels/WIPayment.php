<?php

class WIPayment
{

    private $db;

    public function __construct()
    {
        $this->db = new PDO("mysql:host=localhost;dbname=wicms","root","");
    }

    public function create($data)
    {

        $stmt = $this->db->prepare("
            INSERT INTO payments
            (amount,currency,status,transaction_ref)
            VALUES
            (:amount,:currency,:status,:transaction)
        ");

        $stmt->execute([
            ':amount'=>$data['amount'],
            ':currency'=>$data['currency'],
            ':status'=>$data['status'],
            ':transaction'=>$data['transaction_ref']
        ]);

        return true;

    }

}

?>