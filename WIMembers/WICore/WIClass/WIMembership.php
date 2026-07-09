<?php
declare(strict_types=1);

/**
 * File Information
 * Written By: Jules Warner / Warner Infinity
 * Company: Warner Infinity
 * Product: WICMS
 * Project: WIMembers
 * File: WIMembership.php
 * Location: WIMembers/WICore/WIClass/
 * Type: Class
 * Layer: Membership
 * Purpose Area: Member workspace, profile, account, settings, payments, forms and training
 * Version: 1.0.0
 * Created: 2026-05-24
 * Last Updated: 2026-05-24
 * Status: Refactored
 * Summary: WICMS-compatible WIMembers modernisation. Keeps WI-prefixed classes, database-driven modules, sessions and page rendering.
 */


class WIMembership
{
    private WIdb $WIdb;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    public function summary(int $userId): array
    {
        $latest = [];
        if ($this->WIdb->tableExists('wi_plugin_orders')) {
            $latest = $this->WIdb->row('SELECT * FROM `wi_plugin_orders` WHERE `user_id` = :user_id ORDER BY `order_date` DESC LIMIT 1', ['user_id' => $userId]);
        }

        return [
            'plan' => $latest !== [] ? (string) ($latest['plugin_slug'] ?? 'member') : 'member',
            'status' => $latest !== [] ? (string) ($latest['order_status'] ?? 'active') : 'active',
            'currency' => (string) ($latest['order_currency'] ?? 'GBP'),
            'last_payment' => (float) ($latest['order_price'] ?? 0),
            'last_order_date' => (string) ($latest['order_date'] ?? ''),
        ];
    }
}
