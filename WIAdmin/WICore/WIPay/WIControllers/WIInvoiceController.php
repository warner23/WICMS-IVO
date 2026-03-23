<?php

require_once __DIR__ . '/../WIModels/WIInvoice.php';

class WIInvoiceController
{

    public function get($id)
    {

        $invoice = new WIInvoice();
        $data = $invoice->find($id);

        echo json_encode($data);

    }

}