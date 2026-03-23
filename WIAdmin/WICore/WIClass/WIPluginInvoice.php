<?php
declare(strict_types=1);

#[\AllowDynamicProperties]
class WIPluginInvoice
{
    private WIdb $WIdb;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    public function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $rows = $this->WIdb->select(
            "SELECT COUNT(*) AS total FROM `wi_plugin_invoices`"
        );

        $count = (int)($rows[0]['total'] ?? 0) + 1;

        return 'INV-' . $year . '-' . str_pad((string)$count, 5, '0', STR_PAD_LEFT);
    }

    public function create(
        int $orderId,
        float $total,
        string $currency = 'GBP',
        string $status = 'paid'
    ): string {
        $invoiceNumber = $this->generateInvoiceNumber();

        $this->WIdb->insert('wi_plugin_invoices', [
            'order_id' => $orderId,
            'invoice_number' => $invoiceNumber,
            'invoice_total' => $total,
            'invoice_currency' => $currency,
            'invoice_status' => $status
        ]);

        return $invoiceNumber;
    }

    public function getByOrderId(int $orderId): ?array
    {
        $rows = $this->WIdb->select(
            "SELECT * FROM `wi_plugin_invoices`
             WHERE `order_id` = :oid
             ORDER BY `invoice_id` DESC
             LIMIT 1",
            ['oid' => $orderId]
        );

        return $rows[0] ?? null;
    }
}
?>