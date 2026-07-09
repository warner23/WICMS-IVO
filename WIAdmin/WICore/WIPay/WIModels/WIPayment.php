<?php

class WIPayment
{

    private WIdb $WIdb;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    public function create($data)
    {

        $stmt = $this->WIdb->prepare("
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