<?php
declare(strict_types=1);

#[\AllowDynamicProperties]
class WIPluginCommerce
{
    private WIdb $WIdb;
    private WIPluginLicense $licenseService;
    private WIPluginInvoice $invoiceService;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->licenseService = new WIPluginLicense();
        $this->invoiceService = new WIPluginInvoice();
    }

    public function createOrder(
        int $userId,
        string $pluginSlug,
        float $price,
        string $currency = 'GBP',
        string $gateway = 'manual',
        string $status = 'pending',
        ?string $transactionId = null
    ): int {
        $this->WIdb->insert('wi_plugin_orders', [
            'user_id' => $userId,
            'plugin_slug' => $pluginSlug,
            'order_price' => $price,
            'order_currency' => $currency,
            'order_status' => $status,
            'payment_gateway' => $gateway,
            'transaction_id' => $transactionId
        ]);

        $row = $this->WIdb->select(
            "SELECT `order_id` FROM `wi_plugin_orders`
             WHERE `user_id` = :uid
             AND `plugin_slug` = :slug
             ORDER BY `order_id` DESC
             LIMIT 1",
            [
                'uid' => $userId,
                'slug' => $pluginSlug
            ]
        );

        return (int)($row[0]['order_id'] ?? 0);
    }

    public function markOrderPaid(int $orderId, ?string $transactionId = null): bool
    {
        return (bool)$this->WIdb->update(
            'wi_plugin_orders',
            [
                'order_status' => 'paid',
                'transaction_id' => $transactionId ?? ''
            ],
            '`order_id` = :oid',
            ['oid' => $orderId]
        );
    }

    public function createInvoiceForOrder(int $orderId): ?string
    {
        $rows = $this->WIdb->select(
            "SELECT * FROM `wi_plugin_orders` WHERE `order_id` = :oid LIMIT 1",
            ['oid' => $orderId]
        );

        if (!$rows) {
            return null;
        }

        $order = $rows[0];

        return $this->invoiceService->create(
            (int)$order['order_id'],
            (float)$order['order_price'],
            (string)$order['order_currency'],
            'paid'
        );
    }

    public function createLicenseForOrder(
        int $orderId,
        string $licenseType = 'lifetime',
        string $licenseStatus = 'active',
        ?string $expires = null
    ): ?string {
        $rows = $this->WIdb->select(
            "SELECT * FROM `wi_plugin_orders` WHERE `order_id` = :oid LIMIT 1",
            ['oid' => $orderId]
        );

        if (!$rows) {
            return null;
        }

        $order = $rows[0];

        return $this->licenseService->create(
            (int)$order['user_id'],
            (string)$order['plugin_slug'],
            $licenseType,
            $licenseStatus,
            $expires
        );
    }

    public function createSubscription(
        int $userId,
        string $pluginSlug,
        float $price,
        string $currency = 'GBP',
        string $period = 'monthly',
        string $status = 'active',
        ?string $endDate = null,
        ?string $gatewaySubscriptionId = null
    ): bool {
        return (bool)$this->WIdb->insert('wi_plugin_subscriptions', [
            'user_id' => $userId,
            'plugin_slug' => $pluginSlug,
            'subscription_price' => $price,
            'subscription_currency' => $currency,
            'subscription_period' => $period,
            'subscription_status' => $status,
            'subscription_end' => $endDate,
            'gateway_subscription_id' => $gatewaySubscriptionId
        ]);
    }

    public function completePurchase(
        int $userId,
        string $pluginSlug,
        float $price,
        string $currency = 'GBP',
        string $gateway = 'manual',
        string $licenseType = 'lifetime',
        bool $subscription = false,
        string $subscriptionPeriod = 'monthly'
    ): array {
        $orderId = $this->createOrder($userId, $pluginSlug, $price, $currency, $gateway, 'paid');
        $this->markOrderPaid($orderId);

        $invoice = $this->createInvoiceForOrder($orderId);

        $expires = null;
        if ($licenseType === 'subscription') {
            $expires = ($subscriptionPeriod === 'yearly')
                ? date('Y-m-d H:i:s', strtotime('+1 year'))
                : date('Y-m-d H:i:s', strtotime('+1 month'));
        }

        $license = $this->createLicenseForOrder(
            $orderId,
            $licenseType,
            'active',
            $expires
        );

        if ($subscription) {
            $this->createSubscription(
                $userId,
                $pluginSlug,
                $price,
                $currency,
                $subscriptionPeriod,
                'active',
                $expires
            );
        }

        return [
            'order_id' => $orderId,
            'invoice_number' => $invoice,
            'license_key' => $license
        ];
    }
}
?>