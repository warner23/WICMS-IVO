<?php

class WIInvoice
{

    private $WIdb;

    public function __construct()
    {
        $this->WIdb = new PDO("mysql:host=localhost;dbname=wicms","root","");
    }

    public function find($id)
    {

        $stmt = $this->db->prepare("SELECT * FROM invoices WHERE id=?");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

}

?>