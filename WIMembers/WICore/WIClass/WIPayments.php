<?php
declare(strict_types=1);

/**
 * File Information
 * Written By: Jules Warner / Warner Infinity
 * Company: Warner Infinity
 * Product: WICMS
 * Project: WIMembers
 * File: WIPayments.php
 * Location: WIMembers/WICore/WIClass/
 * Type: Class
 * Layer: Payments
 * Purpose Area: Member workspace, profile, account, settings, payments, forms and training
 * Version: 1.0.0
 * Created: 2026-05-24
 * Last Updated: 2026-05-24
 * Status: Refactored
 * Summary: WICMS-compatible WIMembers modernisation. Keeps WI-prefixed classes, database-driven modules, sessions and page rendering.
 */


class WIPayments
{
    private WIdb $WIdb;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    public function orders(int $userId): array
    {
        if ($userId <= 0 || !$this->WIdb->tableExists('wi_plugin_orders')) {
            return [];
        }

        return $this->WIdb->select('SELECT * FROM `wi_plugin_orders` WHERE `user_id` = :user_id ORDER BY `order_date` DESC LIMIT 25', ['user_id' => $userId]);
    }
}
