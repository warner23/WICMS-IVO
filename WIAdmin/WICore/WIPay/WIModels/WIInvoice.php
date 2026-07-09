<?php

class WIInvoice
{

    private WIdb $WIdb;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    public function find($id)
    {

        $stmt = $this->WIdb->prepare("SELECT * FROM invoices WHERE id=?");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

}

?>