<?php
declare(strict_types=1);

/**
 * Settings Class
 * WICMS Core
 */

final class WISettings
{
    private WILib $WIC;

    public function __construct()
    {
        $this->WIC = WILib::getInstance();
    }

    private function getSetting(string $table, string $column, int $id = 1): mixed
    {
        return $this->WIC->selectColumn(
            "SELECT * FROM {$table} WHERE id = :id",
            ['id' => $id],
            $column
        );
    }

    public function website(string $column): mixed
    {
        return $this->getSetting('wi_site', $column);
    }

    public function pos(string $column): mixed
    {
        return $this->getSetting('wipos_settings', $column);
    }

    public function shop(string $column): mixed
    {
        return $this->getSetting('wipos_settings', $column);
    }

    public function membership(string $column): mixed
    {
        return $this->getSetting('wi_membership_settings', $column);
    }
}