<?php
declare(strict_types=1);

#[\AllowDynamicProperties]
class WIPluginSubscription
{
    private WIdb $WIdb;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    public function create(
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

    public function cancel(int $subscriptionId): bool
    {
        return (bool)$this->WIdb->update(
            'wi_plugin_subscriptions',
            ['subscription_status' => 'cancelled'],
            '`subscription_id` = :id',
            ['id' => $subscriptionId]
        );
    }

    public function getActiveByUser(int $userId): array
    {
        return $this->WIdb->select(
            "SELECT * FROM `wi_plugin_subscriptions`
             WHERE `user_id` = :uid AND `subscription_status` = :status
             ORDER BY `subscription_id` DESC",
            [
                'uid' => $userId,
                'status' => 'active'
            ]
        );
    }
}